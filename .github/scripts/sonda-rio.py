#!/usr/bin/env python3
"""Read-only probe of official river sources (20/11/2026, round 5: numeric forecast series, weekly forecast table, monthly bulletin). Run by .github/workflows/sonda-rio.yml. Prints short extracts only."""
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


def get(url, timeout=25, maxb=6_000_000):
    if time.time() > FIN:
        return 0, "", b"", "deadline"
    try:
        with urlopen(Request(url, headers={"User-Agent": UA, "Accept-Language": "es"}), timeout=timeout) as r:
            return r.status, r.headers.get("content-type", ""), r.read(maxb), None
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

seccion("A. a5: pronósticos numéricos de la corrida 289 (tabprono_central) para Goya y Corrientes")
st, ct, b, e = get(f"{INA}/sim/calibrados/289/corridas?includeProno=true&format=json", 90)
d = json.loads(b)
cor = d[0]
print("corrida", cor.get("id"), "forecast_date", cor.get("forecast_date"))
for se in cor["series"]:
    pr = se.get("pronosticos") or []
    if se.get("series_id") in (3524, 1540, 1542, 3523, 3526, 3527, 3408, 3412):
        print("serie", se.get("series_id"), "n", len(pr))
        if pr:
            print("   fila0", json.dumps(pr[0], ensure_ascii=False)[:260])
            print("   filas", [(str(x.get("timestart"))[:10], x.get("valor"), x.get("qualifier")) for x in pr])
for sid in (3524, 1540):
    st, ct, b, e = get(f"{INA}/obs/puntual/series/{sid}?format=json", 60)
    try:
        x = json.loads(b)
        est = x.get("estacion", {})
        print("serie", sid, "-> estacion", est.get("id"), est.get("nombre"), "| var", (x.get("var") or {}).get("nombre"), "| proc", (x.get("procedimiento") or {}).get("nombre"))
    except Exception as ex:  # noqa: BLE001
        print("serie", sid, st, str(ex)[:80])

seccion("B. Informe semanal INA (18/11): párrafo Corrientes-La Paz y tabla de previsión")
st, ct, b, e = get(BASE + "2026Nov18_10480mapat.pdf", 90, 20_000_000)
L = [l.rstrip() for l in pdf_texto(b).splitlines() if l.strip()] if st == 200 else []
print("\n".join(l[:200] for l in L[236:300]))

seccion("C. Boletín hidroclimático mensual (noviembre 2026), descarga completa")
st, ct, b, e = get(BASE + "boletin_hidroclim_2026_11.pdf", 120, 40_000_000)
print(st, len(b), "B", e or "")
if st == 200 and b[:4] == b"%PDF":
    L = [l.rstrip() for l in pdf_texto(b).splitlines() if l.strip()]
    print(len(L), "líneas")
    print("\n".join(l[:200] for l in L[:8]))
    for n, l in enumerate(L):
        if re.search(r"Corrientes|Goya|tramo medio|perspectiv|trimestr|pron[oó]stic|El Ni[nñ]o|Paran[aá]", l, re.I):
            print(f"[{n}]", l[:230])
print("\nfin de la sonda", dt.datetime.now(dt.timezone.utc).isoformat()[:19])
