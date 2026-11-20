#!/usr/bin/env python3
"""Read-only probe of official river sources (20/11/2026). Run by .github/workflows/sonda-rio.yml. Prints short extracts only."""
import datetime as dt, json, re, subprocess, sys, tempfile, time, os, html
from urllib.request import Request, urlopen
from urllib.parse import urljoin, urlparse
from urllib.error import HTTPError

UA = "Mozilla/5.0 (compatible; pescamapa-sonda/1.0; +https://pescamapa.com)"
FIN = time.time() + 11 * 60
KEY = re.compile(r"pron[oó]stic|perspectiv|escenario|informe|tendencia|crecida|semanal|hidrol|alerta|altura|caudal|par[aá]na|boletin|boletín|reporte|nivel", re.I)
TEMA = re.compile(r"Paran[aá]|Corrientes|Goya|Esquina|La Paz|Santa Fe|Resistencia|Itat[ií]|Yacyret[aá]|Itaip[uú]", re.I)


def scrub(t):
    t = re.sub(r"[\w.+-]+@[\w-]+\.[\w.-]+", "[mail]", t)
    t = re.sub(r"(?<!\d)(?:\+?\d[\d ()-]{7,}\d)(?!\d)", "[tel]", t)
    return re.sub(r"\s+", " ", t).strip()


def get(url, timeout=25):
    if time.time() > FIN:
        return 0, "", b"", "deadline"
    try:
        with urlopen(Request(url, headers={"User-Agent": UA, "Accept-Language": "es"}), timeout=timeout) as r:
            return r.status, r.headers.get("content-type", ""), r.read(6_000_000), None
    except HTTPError as e:
        return e.code, "", b"", str(e)[:80]
    except Exception as e:  # noqa: BLE001
        return 0, "", b"", str(e)[:100]


def texto(b):
    for enc in ("utf-8", "latin-1"):
        try:
            return b.decode(enc)
        except UnicodeDecodeError:
            pass
    return b.decode("utf-8", "replace")


def titulo(h):
    m = re.search(r"<title[^>]*>(.*?)</title>", h, re.S | re.I)
    return scrub(html.unescape(m.group(1)))[:120] if m else "-"


def enlaces(h, base):
    out = []
    for m in re.finditer(r'<a\b[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>', h, re.S | re.I):
        href = urljoin(base, html.unescape(m.group(1)))
        txt = scrub(html.unescape(re.sub(r"<[^>]+>", " ", m.group(2))))[:90]
        if href.startswith("http"):
            out.append((href, txt))
    return out


def sin_html(h):
    h = re.sub(r"<(script|style)[^>]*>.*?</\1>", " ", h, flags=re.S | re.I)
    return scrub(html.unescape(re.sub(r"<[^>]+>", " ", h)))


def trozos(t, n=4, ancho=260):
    out = []
    for m in TEMA.finditer(t):
        a = max(0, m.start() - 90)
        s = t[a:a + ancho]
        if not out or a > out[-1][0] + ancho:
            out.append((a, s))
        if len(out) >= n:
            break
    return [s for _, s in out]


def pdf_texto(b):
    with tempfile.TemporaryDirectory() as d:
        p = os.path.join(d, "x.pdf")
        open(p, "wb").write(b)
        r = subprocess.run(["pdftotext", "-layout", p, "-"], capture_output=True, text=True, timeout=60)
        return r.stdout


def seccion(t):
    print(f"\n===== {t} =====", flush=True)


# ------------------------------------------------------------------ 1. INA a5 API
INA = "https://alerta.ina.gob.ar/a5"
seccion("1. a5 API: catálogo de estaciones (niveles)")
st, ct, b, e = get(f"{INA}/obs/puntual/estaciones?format=json", 120)
cat = {}
if st == 200:
    cat = {x["id"]: x for x in json.loads(b)}
    print(f"catálogo: {len(cat)} estaciones, {len(b)/1e6:.1f} MB")
    for i in (23, 19, 25, 26, 30, 18, 17):
        x = cat.get(i)
        if x:
            print(i, json.dumps({k: v for k, v in x.items() if k not in ("geom", "rio_id") and not isinstance(v, (dict, list))}, ensure_ascii=False)[:600])
else:
    print("ERROR", st, e)

seccion("1b. a5 API: series de la estación y pronósticos")
for est in (23, 19):
    for url in (f"{INA}/obs/puntual/series?estacion_id={est}&format=json", f"{INA}/obs/puntual/series?estacion_id={est}&proc_id=2&format=json"):
        st, ct, b, e = get(url, 60)
        print(f"GET {url.replace(INA, 'a5')} -> {st} {e or ''} {len(b)} B")
        if st == 200:
            try:
                d = json.loads(b)
                d = d if isinstance(d, list) else d.get("rows", d)
                for x in d[:12]:
                    print("   ", json.dumps({k: x.get(k) for k in ("id", "tipo", "proc_id", "var_id", "unit_id", "estacion_id", "fuentes_id", "date_range") if k in x}, ensure_ascii=False)[:260])
            except Exception as ex:  # noqa: BLE001
                print("   no JSON", str(ex)[:80], texto(b)[:200])
for url in (f"{INA}/sim/calibrados?format=json", f"{INA}/sim/calibrados?estacion_id=23&format=json", f"{INA}/sim/calibrados?var_id=2&format=json", f"{INA}/sim/pronosticos?format=json",
            f"{INA}/sim/corridas?format=json", f"{INA}/obs/puntual/series/23?format=json"):
    st, ct, b, e = get(url, 60)
    print(f"GET {url.replace(INA, 'a5')} -> {st} {e or ''} {len(b)} B", "|", scrub(texto(b))[:420])

seccion("1c. a5 API: observaciones de 23 con timeend a futuro (¿hay valores posteriores a hoy?)")
hoy = dt.date.today()
for sid in (23, 19):
    st, ct, b, e = get(f"{INA}/obs/puntual/series/{sid}/observaciones?timestart={hoy - dt.timedelta(days=6)}&timeend={hoy + dt.timedelta(days=20)}&format=json", 60)
    if st == 200:
        d = json.loads(b)
        d = d if isinstance(d, list) else d.get("rows", [])
        print(sid, [(str(x.get("timestart"))[:10], x.get("valor")) for x in d][-9:])
    else:
        print(sid, "ERROR", st, e)

# ------------------------------------------------------------------ 2. INA daily report PDF
seccion("2. Reporte diario del INA (PDF, columna Perspectiva)")
bases = ["https://alerta.ina.gob.ar/ina/06-INFORMES/diario/pdf/", "https://alerta.ina.gob.ar/06-INFORMES/diario/pdf/", "https://alerta.ina.gob.ar/pub/06-INFORMES/diario/pdf/",
         "https://www.ina.gob.ar/ina/06-INFORMES/diario/pdf/", "https://alerta.ina.gob.ar/ina/06-INFORMES/diario/"]
hallado = None
for back in range(0, 6):
    d = hoy - dt.timedelta(days=back)
    for base in bases:
        url = f"{base}reporte_diario_{d.isoformat()}.pdf"
        st, ct, b, e = get(url, 40)
        print(f"{url} -> {st} {ct[:30]} {len(b)} B {e or ''}")
        if st == 200 and b[:4] == b"%PDF":
            hallado = (url, b)
            break
    if hallado:
        break
if hallado:
    url, b = hallado
    t = pdf_texto(b)
    print("PDF:", url, "|", len(t), "caracteres de texto")
    lineas = t.splitlines()
    for ln in lineas[:14]:
        if ln.strip():
            print("  [cabecera]", scrub(ln)[:200])
    for ln in lineas:
        if re.search(r"Estaci[oó]n|Perspectiva|Corrientes|Goya|Esquina|La Paz|Santa Fe|Paso de la Patria|Barranqueras|Rosario|Paran[aá]\b|Fecha", ln):
            print("  ", scrub(ln)[:230])
else:
    print("no se pudo descargar ningún reporte diario con esas URL")

# ------------------------------------------------------------------ 3. official pages
SEMILLAS = {
    "INA alerta": ["https://alerta.ina.gob.ar/", "https://alerta.ina.gob.ar/ina/", "https://alerta.ina.gob.ar/pub/", "https://www.ina.gob.ar/", "https://www.argentina.gob.ar/ina", "https://www.argentina.gob.ar/ina/alerta"],
    "Prefectura": ["https://contenidosweb.prefecturanaval.gob.ar/alturas/", "https://contenidosweb.prefecturanaval.gob.ar/alturas/index.php", "https://www.argentina.gob.ar/prefecturanaval"],
    "Yacyretá": ["https://www.eby.org.ar/", "https://www.eby.org.ar/es/", "https://eby.org.ar/"],
    "Itaipú": ["https://www.itaipu.gov.br/", "https://www.itaipu.gov.py/", "https://www.itaipu.gov.br/es"],
    "Goya": ["https://www.goya.gob.ar/", "https://goya.gob.ar/", "https://www.municipalidadgoya.gob.ar/"],
    "Corrientes": ["https://www.corrientes.gob.ar/", "https://corrientes.gob.ar/", "https://www.corrientes.gob.ar/noticias"],
    "Protección civil": ["https://www.argentina.gob.ar/seguridad/proteccion-civil", "https://www.argentina.gob.ar/interior/proteccion-civil"],
}
for sitio, urls in SEMILLAS.items():
    seccion(f"3. {sitio}")
    seguir = []
    for u in urls:
        st, ct, b, e = get(u, 25)
        if st == 200 and "html" in ct.lower() or (st == 200 and b[:5].lower() in (b"<!doc", b"<html")):
            h = texto(b)
            li = enlaces(h, u)
            print(f"{u} -> {st}, {len(b)} B, título: {titulo(h)}, enlaces {len(li)}")
            dom = urlparse(u).netloc.split(".", 1)[-1]
            for href, txt in li:
                if KEY.search(txt + " " + href) and dom in urlparse(href).netloc and href not in [s[0] for s in seguir] and len(seguir) < 40:
                    seguir.append((href, txt))
            tz = trozos(sin_html(h), 2)
            for s in tz:
                print("   texto:", s)
        else:
            print(f"{u} -> {st} {ct[:25]} {e or ''}")
    print(f"  enlaces con palabras clave ({len(seguir)}):")
    for href, txt in seguir[:25]:
        print(f"    {txt[:70]!r} {href[:150]}")
    # follow the most promising ones (pdf/pages), short extracts
    prior = sorted(seguir, key=lambda s: -len(re.findall(r"pron[oó]stic|perspectiv|escenario|semanal|informe|alturas|crecida|caudal", s[0] + s[1], re.I)))[:6]
    for href, txt in prior:
        st, ct, b, e = get(href, 30)
        if st != 200:
            print(f"  -> {href[:120]} {st} {e or ''}")
            continue
        if b[:4] == b"%PDF":
            t = pdf_texto(b)
            print(f"  PDF {href[:140]} ({len(t)} car.) inicio: {scrub(t[:240])}")
            for s in trozos(scrub(t), 3):
                print("     extracto:", s)
        else:
            h = texto(b)
            print(f"  {href[:140]} título: {titulo(h)}")
            for s in trozos(sin_html(h), 3):
                print("     extracto:", s)
print("\nfin de la sonda", dt.datetime.now(dt.timezone.utc).isoformat()[:19])
