#!/usr/bin/env python3
"""Read-only probe of official river sources (20/11/2026, round 6: bulletin paragraphs). Run by .github/workflows/sonda-rio.yml. Prints short extracts only."""
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


BASE = "https://www.ina.gov.ar/archivos/alerta/"
st, ct, b, e = get(BASE + "boletin_hidroclim_2026_11.pdf", 120, 40_000_000)
print(st, len(b), "B", e or "")
L = [l.rstrip() for l in pdf_texto(b).splitlines() if l.strip()] if st == 200 else []
for a, z in ((0, 36), (105, 124), (418, 455)):
    print(f"--- líneas {a}-{z}")
    print("\n".join(l[:200] for l in L[a:z]))
print("\nfin de la sonda", dt.datetime.now(dt.timezone.utc).isoformat()[:19])
