#!/usr/bin/env python3
"""Read-only probe of official river sources (20/11/2026, round 4: TabProno, weekly and monthly INA reports). Run by .github/workflows/sonda-rio.yml. Prints short extracts only."""
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


hoy = dt.date.today()
INA = "https://alerta.ina.gob.ar/a5"
BASE = "https://www.ina.gov.ar/archivos/alerta/"


def lineas_pdf(url):
    st, ct, b, e = get(url, 90)
    print(f"--- {url} -> {st} {e or ''} {len(b)} B")
    if st != 200 or b[:4] != b"%PDF":
        return []
    return [l.rstrip() for l in pdf_texto(b).splitlines() if l.strip()]


seccion("A. TabProno (pronóstico río Paraná) del 20/11 y del 17/11")
for u in (BASE + "TabProno20261120_.pdf", BASE + "TabProno20261117_.pdf"):
    L = lineas_pdf(u)
    print("\n".join(l[:200] for l in L[:80]))

seccion("B. Informe semanal INA (18/11): fecha y párrafos del Paraná")
L = lineas_pdf(BASE + "2026Nov18_10480mapat.pdf")
print("\n".join(l[:200] for l in L[:12]))
for n, l in enumerate(L):
    if re.search(r"Corrientes|Goya|Paran[aá] (medio|inferior)|tramo medio|Alto Paran|Yacyret", l):
        print(f"[{n}]", l[:220])

seccion("C. Boletín hidroclimático mensual (noviembre 2026)")
L = lineas_pdf(BASE + "boletin_hidroclim_2026_11.pdf")
print("\n".join(l[:200] for l in L[:10]))
for n, l in enumerate(L):
    if re.search(r"Corrientes|Goya|tramo medio|Paran[aá]\b.*(altura|nivel|crecida)|perspectiv|trimestr|pron[oó]stic|El Ni[nñ]o", l, re.I):
        print(f"[{n}]", l[:220])

seccion("D. Cuadro hidrométrico del INA (20/11): filas Corrientes a Rosario")
L = lineas_pdf(BASE + "Cuadro_2026nov20.pdf")
for l in L[:6]:
    print("  ", l[:200])
for l in L:
    if re.search(r"Corrientes|Goya|Esquina|La Paz|Santa Fe|Reconquista|Barranqueras|Paso de la Patria|Fecha|Estaci", l):
        print("  ", l[:220])

seccion("E. a5: forma de la corrida 289 con includeProno (primeros bytes)")
st, ct, b, e = get(f"{INA}/sim/calibrados/289/corridas?includeProno=true&format=json", 90)
print(st, len(b), "B")
try:
    d = json.loads(b)
    cor = d[0]
    print("claves corrida:", sorted(cor.keys()))
    for se in cor["series"]:
        print("  serie", se.get("series_id"), "est", se.get("estacion_id"), "var", se.get("var_id"), "claves", sorted(se.keys())[:14], "n pron", len(se.get("pronosticos") or []))
        pr = se.get("pronosticos") or []
        if pr and se.get("estacion_id") in (23, 19):
            print("     fila0", json.dumps(pr[0])[:240])
            print("     todas", [(str(x.get("timestart"))[:10], x.get("valor"), x.get("qualifier")) for x in pr][:40])
except Exception as ex:  # noqa: BLE001
    print("no JSON:", str(ex)[:100], texto(b)[:400])
print("\nfin de la sonda", dt.datetime.now(dt.timezone.utc).isoformat()[:19])
