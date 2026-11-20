#!/usr/bin/env python3
"""Read-only probe of official river sources (20/11/2026, round 3: report narrative, a5 forecast runs, INA sections, Itaipú, Goya). Run by .github/workflows/sonda-rio.yml. Prints short extracts only."""
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


def jget(url, timeout=90):
    st, ct, b, e = get(url, timeout)
    try:
        return st, json.loads(b), e
    except Exception:  # noqa: BLE001
        return st, None, e or texto(b)[:150]


def pdf_url(url, desde=None, hasta=None, solo=None):
    st, ct, b, e = get(url, 60)
    print(f"--- {url} -> {st} {e or ''}")
    if st != 200 or b[:4] != b"%PDF":
        return []
    lineas = [l.rstrip() for l in pdf_texto(b).splitlines() if l.strip()]
    return lineas


seccion("A. Reporte diario INA 20/11 y 13/11: texto del tramo Corrientes - Santa Fe - Rosario")
for dia in (hoy, hoy - dt.timedelta(days=7)):
    L = pdf_url(f"https://alerta.ina.gob.ar/ina/06-INFORMES/diario/pdf/reporte_diario_{dia.isoformat()}.pdf")
    for l in L[:4]:
        print("  [cab]", l[:160])
    k = next((n for n, l in enumerate(L) if re.search(r"El r[ií]o Paran[aá] en Corrientes", l)), None)
    if k is None:
        print("  (no se halló el párrafo Corrientes)")
        continue
    print("\n".join(L[k:k + 62]))

seccion("B. a5 API: corridas de pronóstico (289 tabprono_central, 434 media semanal, 499 estacional) para Goya (23) y Corrientes (19)")
for cid in (289, 434, 499):
    for variante in (f"sim/calibrados/{cid}/corridas?includeProno=true&format=json", f"sim/calibrados/{cid}/corridas?format=json&includeProno=true&qualifier=all"):
        st, d, e = jget(f"{INA}/{variante}")
        print(f"GET a5/{variante} -> {st} {e or ''}", type(d).__name__, len(d) if isinstance(d, list) else "")
        if isinstance(d, list):
            for cor in d[:3]:
                print("  corrida", cor.get("id"), "forecast_date", cor.get("forecast_date"), "series:", len(cor.get("series", [])))
                for se in cor.get("series", []):
                    if se.get("estacion_id") in (23, 19):
                        pr = se.get("pronosticos") or []
                        print("    estacion", se.get("estacion_id"), "serie", se.get("series_id"), "var", se.get("var_id"), "qualifiers", se.get("qualifiers"), "begin", se.get("begin_date"), "end", se.get("end_date"), "n", se.get("count"), "pronosticos", len(pr))
                        if pr:
                            print("      fila0:", json.dumps(pr[0], ensure_ascii=False)[:200])
                            print("      filas:", [(str(x.get("timestart"))[:10], x.get("valor"), x.get("qualifier")) for x in pr[:40]])
            break
for cid in (289,):
    st, d, e = jget(f"{INA}/sim/calibrados/{cid}/corridas_guardadas?format=json")
    if isinstance(d, list) and d:
        ult = d[-1]
        print("última corrida guardada:", ult)
        st, d2, e = jget(f"{INA}/sim/calibrados/{cid}/corridas/{ult['cor_id']}?includeProno=true&format=json")
        print(f"corrida {ult['cor_id']} -> {st} {e or ''}", type(d2).__name__)
        if isinstance(d2, dict):
            for se in d2.get("series", []):
                if se.get("estacion_id") in (23, 19):
                    pr = se.get("pronosticos") or []
                    print("    estacion", se.get("estacion_id"), "serie", se.get("series_id"), "qualifiers", se.get("qualifiers"), "n", len(pr), [(str(x.get("timestart"))[:10], x.get("valor"), x.get("qualifier")) for x in pr[:40]])

seccion("C. INA: a5/secciones, alerta/index.php y directorios")
for u in ("https://alerta.ina.gob.ar/a5/secciones", "https://www.ina.gov.ar/alerta/index.php", "https://ina.gob.ar/alerta/index.php"):
    st, ct, b, e = get(u, 40)
    print(u, "->", st, e or "", len(b), "B", titulo(texto(b)) if st == 200 else "")
    if st == 200:
        for href, txt in enlaces(texto(b), u)[:120]:
            if re.search(r"pron[oó]stic|informe|escenari|semanal|perspectiv|parana|paraná|estacional|seccion", txt + href, re.I):
                print("    ", repr(txt[:80]), href[:140])
for n in (1, 2, 3, 4, 5, 6, 7, 8, 9, 11, 12, 13):
    u = f"https://ina.gob.ar/alerta/index.php?seccion={n}"
    st, ct, b, e = get(u, 25)
    if st == 200:
        h = texto(b)
        m = re.search(r"<h[12][^>]*>(.*?)</h[12]>", h, re.S)
        print(f"seccion={n}: {titulo(h)[:90]} | {scrub(re.sub('<[^>]+>', ' ', m.group(1)))[:80] if m else '-'}")
for d in ("06-INFORMES/", "08-PRONOSTICOS/", "mapa_informe_semanal/"):
    u = "https://alerta.ina.gob.ar/ina/" + d
    st, ct, b, e = get(u, 40)
    print(u, "->", st, len(b), "B")
    if st == 200:
        li = [(h, t) for h, t in enlaces(texto(b), u) if h.rstrip("/") != u.rstrip("/") and "~" not in t and not h.rstrip("/").endswith("/ina")]
        for h, t in li[-25:]:
            print("    ", t[:70], h[-90:])
        for h, t in li:
            if re.search(r"seman|escenar|pronost|perspect|mensual|estacional|boletin", h, re.I) and h.endswith("/"):
                st2, ct2, b2, e2 = get(h, 40)
                if st2 == 200:
                    l2 = [(hh, tt) for hh, tt in enlaces(texto(b2), h) if hh.rstrip("/") != h.rstrip("/") and "~" not in tt and not hh.rstrip("/").endswith("/ina")]
                    print("    >>", h[-60:], len(l2), "entradas; últimas:")
                    for hh, tt in l2[-10:]:
                        print("        ", tt[:70], hh[-80:])

seccion("D. Itaipú: artículo completo (19/11) y boletín hidrológico (PDF)")
u = "https://www.itaipu.gov.br/noticias/sala-de-imprensa/nivel-do-rio-parana-deve-seguir-estavel-no-feriadao-preve-itaipu"
st, ct, b, e = get(u, 40)
if st == 200:
    h = texto(b)
    m = re.search(r"Publicado em", sin_html(h))
    t = sin_html(h)
    print(t[m.start():m.start() + 2600] if m else t[:200])
st, ct, b, e = get("https://www.itaipu.gov.br/wp-content/uploads/2026/11/BH.pdf", 60)
print("BH.pdf ->", st, e or "", len(b), "B")
if st == 200 and b[:4] == b"%PDF":
    for l in [l.rstrip() for l in pdf_texto(b).splitlines() if l.strip()][:70]:
        print("   ", l[:200])

seccion("E. Goya: COE, noticias")
for u in ("https://www.goya.gob.ar/?q=coe-comite-operativo-emergencia", "https://www.goya.gob.ar/?q=boletin-prensa"):
    st, ct, b, e = get(u, 30)
    print(u, "->", st, e or "")
    if st == 200:
        t = sin_html(texto(b))
        k = t.find("Contenido principal")
        print("   ", t[k:k + 1800])
st, ct, b, e = get("https://www.goya.gob.ar/", 30)
if st == 200:
    vistos = set()
    for href, txt in enlaces(texto(b), "https://www.goya.gob.ar/"):
        if len(txt) > 28 and href not in vistos and "goya.gob.ar" in href:
            vistos.add(href)
            print("   portada:", repr(txt[:110]), href[:120])

seccion("F. Prefectura: alturas (curl, 90 s)")
r = subprocess.run(["curl", "-sS", "-m", "90", "-o", "/tmp/pna.html", "-w", "%{http_code} %{size_download} B %{time_total}s", "-A", UA, "https://contenidosweb.prefecturanaval.gob.ar/alturas/"], capture_output=True, text=True)
print("curl:", r.stdout, r.stderr[:200])
if os.path.exists("/tmp/pna.html") and os.path.getsize("/tmp/pna.html") > 0:
    t = sin_html(texto(open("/tmp/pna.html", "rb").read()))
    for m in re.finditer(r"Goya", t):
        print("   ", t[max(0, m.start() - 150):m.start() + 250])
        break
print("\nfin de la sonda", dt.datetime.now(dt.timezone.utc).isoformat()[:19])
