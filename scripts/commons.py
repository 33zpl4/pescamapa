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
}


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
        "descripcion": text(meta, "ImageDescription")[:200],
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
                "prop": "imageinfo", "iiprop": "url|size|extmetadata|mime", "iiurlwidth": 480,
            })
            for page in sorted(data.get("query", {}).get("pages", []), key=lambda p: p.get("index", 0)):
                if page["title"] in vistos or "imageinfo" not in page:
                    continue
                vistos.add(page["title"])
                f = ficha(page)
                if not FREE.match(f["licencia"]) or f["ancho"] < 1200 or f["ancho"] <= f["alto"]:
                    continue
                lista.append(f)
        lista = lista[:12]
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


if __name__ == "__main__":
    if sys.argv[1:2] == ["candidatas"]:
        candidatas(sys.argv[2:])
    elif sys.argv[1:2] == ["bajar"] and len(sys.argv) == 3:
        bajar(sys.argv[2])
    else:
        sys.exit(__doc__)
