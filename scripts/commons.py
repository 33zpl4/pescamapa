#!/usr/bin/env python3
"""Free-licence photos from Wikimedia Commons, with their credits.

Runs on a GitHub runner (the Claude sandbox can't reach Wikimedia):

    python3 scripts/commons.py candidatas [lugar…]   # thumbs + metadata to review
    python3 scripts/commons.py bajar picks.json      # chosen files → src/assets/fotos/

Only licences that allow commercial use are accepted (CC0, public domain,
CC BY, CC BY-SA). Every photo keeps author, licence and source URL in
src/data/fotos.json; the site shows them next to the photo and on
/creditos/. Never use photos found on Google: most are copyrighted.
"""

import html
import json
import re
import sys
import urllib.parse
import urllib.request
from pathlib import Path

API = "https://commons.wikimedia.org/w/api.php"
UA = "pescamapa/0.1 (https://pescamapa.com; github.com/33zpl4/pescamapa)"
ROOT = Path(__file__).resolve().parent.parent
FREE = re.compile(r"^(cc0|public domain|pd|cc by(-sa)? \d)", re.I)

LUGARES = {
    "goya": ["Goya Corrientes", "Riacho Goya", "Goya Argentina río Paraná", "Costanera Goya", "Puerto Goya", "Isoró"],
    "paso-de-la-patria": ["Paso de la Patria"],
    "esquina": ["Esquina Corrientes", "Esquina Argentina río"],
    "la-paz": ["La Paz Entre Ríos"],
    "reconquista": ["Reconquista Santa Fe", "Jaaukanigás", "Puerto Reconquista", "Río Paraná Santa Fe isla", "San Javier Santa Fe río"],
    "concordia": ["Concordia Entre Ríos", "Salto Grande Concordia"],
    "san-martin-junin": ["San Martín de los Andes", "Lago Lácar", "Río Chimehuín"],
    "rio-grande": ["Río Grande Tierra del Fuego"],
    "chascomus": ["Laguna de Chascomús", "Chascomús"],
    "mar-del-plata": ["Mar del Plata"],
    # 14/11/2026: guides that had no photo (WordPress, wordpress/fotos + fotos-wp.json)
    "el-calafate": ["El Calafate", "Lago Argentino", "Lago Argentino El Calafate costanera", "Bahía Redonda El Calafate", "Laguna Nimez"],
    "ushuaia": ["Ushuaia", "Bahía de Ushuaia", "Canal Beagle Ushuaia", "Ushuaia puerto montañas", "Río Pipo Ushuaia", "Lago Escondido Tierra del Fuego"],
    "itati": ["Itatí Corrientes", "Itatí río Paraná", "Basílica de Itatí", "Costanera Itatí", "Itatí Argentina"],
    "esteros-del-ibera": ["Esteros del Iberá", "Laguna Iberá", "Iberá Corrientes", "Colonia Carlos Pellegrini Iberá", "Iberá camalotes laguna"],
    "ita-ibate": ["Itá Ibaté", "Ita Ibate Corrientes", "Itá Ibaté río Paraná", "Ita Ibate costanera", "Itá Ibaté Paraná", "Ita Ibate pesca surubí", "Itá Ibaté Corrientes río", "Itá Ibaté puerto", "Itá Ibaté playa"],
    "rio-santa-cruz": ["Río Santa Cruz Piedra Buena", "Piedra Buena Santa Cruz", "Río Santa Cruz Argentina", "Isla Pavón Piedra Buena", "Río Santa Cruz Comandante Luis Piedra Buena"],
    # 14/11/2026: notes without a cover photo (WordPress notes; keys are the note keys)
    "viaje-dorado-amigos-precios": ["Dorado fishing Paraná", "Pesca del dorado Corrientes", "Pescadores bote río Paraná Corrientes", "golden dorado fishing Argentina", "Salminus brasiliensis fishing", "Pesca deportiva dorado"],
    "precio-viaje-dorado-lodge": ["Río Paraná Corrientes", "Paraná river Corrientes sunset", "Río Paraná Ituzaingó", "Embalse Yacyretá Ituzaingó", "Río Paraná isla Corrientes costa"],
    "precio-pesca-bariloche-limay": ["Río Limay", "Río Limay Neuquén", "Limay River Patagonia", "Río Limay Bariloche", "Fly fishing Patagonia"],
    "precio-semana-trucha-patagonia-norte": ["Río Chimehuín", "Río Malleo", "Río Futaleufú Chubut", "Lago Futalaufquen", "Río Collón Curá", "Río Traful", "Río Alumine"],
    "licencia-pesca-patagonia-precios": ["Pesca con mosca Patagonia", "fly fishing Patagonia Argentina", "Pescador caña Patagonia", "Fishing Patagonia river bank", "Lago Nahuel Huapi orilla"],
    "pesca-desde-brasil-corrientes": ["Río Uruguay Santo Tomé", "Río Uruguay Paso de los Libres", "Paso de los Libres", "Río Uruguay Corrientes", "Santo Tomé Corrientes"],
    "paquete-incluye-no-incluye": ["Fishing lodge Patagonia", "Fishing lodge Argentina", "Bote de pesca río Argentina", "fishing boat Paraná Argentina", "Pesca Río Paraná embarcación"],
    "licencias-extranjeros-argentina": ["Pescador río Argentina", "fisherman Argentina river", "Pesca con caña orilla Argentina", "Pesca deportiva Argentina", "Fishing Argentina rod shore"],
}

# Places with few free photos accept smaller ones (still landscape, still free licence).
MIN_ANCHO = {"ita-ibate": 700}


def get(params):
    url = API + "?" + urllib.parse.urlencode({**params, "format": "json", "formatversion": 2})
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=60) as r:
        return json.load(r)


def download(url, dest):
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=120) as r:
        dest.write_bytes(r.read())


def text(meta, key):
    raw = (meta.get(key) or {}).get("value", "")
    return html.unescape(re.sub(r"<[^>]+>", "", str(raw))).strip()


def info(titles, width):
    data = get({
        "action": "query", "titles": "|".join(titles), "prop": "imageinfo",
        "iiprop": "url|size|extmetadata|mime", "iiurlwidth": width,
    })
    return data["query"]["pages"]


def ficha(page):
    ii = page["imageinfo"][0]
    meta = ii.get("extmetadata", {})
    return {
        "archivo": page["title"],
        "ancho": ii["width"], "alto": ii["height"],
        "thumb": ii.get("thumburl"),
        "pagina": ii["descriptionurl"],
        "autor": text(meta, "Artist") or "Autor desconocido",
        "licencia": text(meta, "LicenseShortName"),
        "licencia_url": text(meta, "LicenseUrl"),
        "descripcion": text(meta, "ImageDescription")[:300],
        "categorias": text(meta, "Categories")[:300],
    }


def candidatas(solo=()):
    out_dir = ROOT / "candidatas"
    out_dir.mkdir(exist_ok=True)
    todo = {}
    for lugar, queries in LUGARES.items():
        if solo and lugar not in solo:
            continue
        vistos, lista = set(), []
        for q in queries:
            data = get({
                "action": "query", "generator": "search", "gsrnamespace": 6,
                "gsrsearch": f"{q} filetype:bitmap", "gsrlimit": 20,
                "prop": "imageinfo", "iiprop": "url|size|extmetadata|mime", "iiurlwidth": 480, "gsroffset": 0,
            })
            for page in sorted(data.get("query", {}).get("pages", []), key=lambda p: p.get("index", 0)):
                if page["title"] in vistos or "imageinfo" not in page:
                    continue
                vistos.add(page["title"])
                f = ficha(page)
                if not FREE.match(f["licencia"]) or f["ancho"] < MIN_ANCHO.get(lugar, 1000) or f["ancho"] <= f["alto"]:
                    continue
                lista.append(f)
        lista = lista[:16]
        for i, f in enumerate(lista):
            f["n"] = i
            download(f["thumb"], out_dir / f"{lugar}-{i}.jpg")
        todo[lugar] = lista
        print(f"{lugar}: {len(lista)} candidatas")
    (out_dir / "candidatas.json").write_text(json.dumps(todo, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


def bajar(picks_file):
    picks = json.loads(Path(picks_file).read_text(encoding="utf-8"))
    dest = ROOT / "src" / "assets" / "fotos"
    dest.mkdir(parents=True, exist_ok=True)
    creditos_file = ROOT / "src" / "data" / "fotos.json"
    creditos = json.loads(creditos_file.read_text(encoding="utf-8")) if creditos_file.exists() else {}
    for lugar, archivo in picks.items():
        page = info([archivo], 1800)[0]
        f = ficha(page)
        if not FREE.match(f["licencia"]):
            sys.exit(f"✖ {archivo}: licencia {f['licencia']!r} no admitida")
        download(f["thumb"], dest / f"{lugar}.jpg")
        f.pop("thumb")
        creditos[lugar] = f
        print(f"✓ {lugar}: {archivo} — {f['autor']} ({f['licencia']})")
    creditos_file.write_text(json.dumps(creditos, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


def recortar_43(ruta):
    """Centre-crop to 4:3 (house rule: horizontal 4:3) when the photo is wider or taller; needs Pillow."""
    from PIL import Image
    im = Image.open(ruta)
    w, h = im.size
    if abs(w / h - 4 / 3) < 0.03:
        return w, h
    if w / h > 4 / 3:
        nw = round(h * 4 / 3)
        box = ((w - nw) // 2, 0, (w - nw) // 2 + nw, h)
    else:
        nh = round(w * 3 / 4)
        box = (0, (h - nh) // 2, w, (h - nh) // 2 + nh)
    im = im.convert("RGB").crop(box)
    im.save(ruta, quality=88)
    return im.size


def bajar_wp(picks_file, ancho=1600, salida="fotos-wp.json", recortar=False):
    """WordPress: picks {clave: "File:..."} -> wordpress/fotos/commons-<clave>.jpg (<= `ancho` px) + fotos-wp.json
    (author, licence, licence URL, Commons page, description, categories, date read) read from Commons at run time."""
    from datetime import date
    picks = json.loads(Path(picks_file).read_text(encoding="utf-8"))
    dest = ROOT / "wordpress" / "fotos"
    dest.mkdir(parents=True, exist_ok=True)
    out = {}
    for clave, archivo in picks.items():
        page = info([archivo], ancho)[0]
        f = ficha(page)
        if not FREE.match(f["licencia"]):
            sys.exit(f"✖ {archivo}: licencia {f['licencia']!r} no admitida")
        if f["autor"] == "Autor desconocido":
            sys.exit(f"✖ {archivo}: sin autor")
        nombre = f"commons-{clave}.jpg"
        download(f["thumb"], dest / nombre)
        f.pop("thumb")
        if recortar:
            f["recorte"] = "centrado 4:3"
            f["ancho_final"], f["alto_final"] = recortar_43(dest / nombre)
        f["archivo_local"] = nombre
        f["leida"] = date.today().isoformat()
        out[clave] = f
        print(f"✓ {clave}: {archivo} — {f['autor']} ({f['licencia']}) {f['ancho']}x{f['alto']} -> {nombre} {(dest / nombre).stat().st_size // 1024} KB")
    (dest / salida).write_text(json.dumps(out, ensure_ascii=False, indent=2) + "\n", encoding="utf-8")


if __name__ == "__main__":
    if sys.argv[1:2] == ["candidatas"]:
        candidatas(sys.argv[2:])
    elif sys.argv[1:2] == ["bajar-wp"] and len(sys.argv) == 3:
        bajar_wp(sys.argv[2])
    elif sys.argv[1:2] == ["bajar-wp-notas"] and len(sys.argv) == 3:
        # notes (14/11/2026): same, but centre-cropped to 4:3 and written to fotos-wp-notas.json (the guides' file stays as it was)
        bajar_wp(sys.argv[2], salida="fotos-wp-notas.json", recortar=True)
    elif sys.argv[1:2] == ["bajar"] and len(sys.argv) == 3:
        bajar(sys.argv[2])
    else:
        sys.exit(__doc__)
