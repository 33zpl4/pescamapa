#!/usr/bin/env python3
"""Read-only probe of official river sources (20/11/2026, round 2: forecasts, INA directories, notices). Run by .github/workflows/sonda-rio.yml. Prints short extracts only."""
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


def jget(url, timeout=60):
    st, ct, b, e = get(url, timeout)
    try:
        return st, json.loads(b), e
    except Exception:  # noqa: BLE001
        return st, None, e or texto(b)[:150]


# ------------------------------------------------------------------ A. daily report, full text of the river part
seccion("A. Reporte diario del INA: texto completo del tramo Paraná (20/11 y 13/11)")
for dia in (hoy, hoy - dt.timedelta(days=7)):
    url = f"https://alerta.ina.gob.ar/ina/06-INFORMES/diario/pdf/reporte_diario_{dia.isoformat()}.pdf"
    st, ct, b, e = get(url, 60)
    print(f"--- {url} -> {st}")
    if st == 200:
        t = pdf_texto(b)
        lineas = [l.rstrip() for l in t.splitlines() if l.strip()]
        ini = next((k for k, l in enumerate(lineas) if re.search(r"Estaci[oó]n\s+R[ií]o\s+Nivel", l)), 0)
        print("\n".join(lineas[max(0, ini - 3):ini + 75]))
        if dia == hoy:
            print("...[resto: líneas con pronóstico/previsión/Goya]")
            for l in lineas[ini + 75:]:
                if re.search(r"Goya|Corrientes|La Paz|pron[oó]stic|previs|actualiz|escenario", l, re.I):
                    print("  ", l[:230])

# ------------------------------------------------------------------ B. a5 forecasts (tabprono and others) for the Paraná stations
seccion("B. a5 API: calibrados con salida en 23 / 19 / 25 / 26 y sus pronósticos")
vistos = {}
for est in (23, 19, 25):
    st, d, e = jget(f"{INA}/sim/calibrados?estacion_id={est}&format=json")
    print(f"estacion {est}: {st} {e or ''} -> {len(d) if isinstance(d, list) else d}")
    for c in (d or []):
        vistos[c["id"]] = c
        print("  ", json.dumps({k: c.get(k) for k in ("id", "nombre", "modelo", "activar", "out_id", "dt", "t_offset")}, ensure_ascii=False)[:300])
for cid, c in list(vistos.items())[:8]:
    for sub in (f"sim/calibrados/{cid}?format=json", f"sim/calibrados/{cid}/corridas_guardadas?format=json", f"sim/calibrados/{cid}/corridas?format=json"):
        st, ct, b, e = get(f"{INA}/{sub}", 60)
        print(f"GET a5/{sub} -> {st} {len(b)} B |", scrub(texto(b))[:500])
seccion("B2. a5 API: observaciones/pronósticos de las series simuladas (ventana hoy-3 a hoy+30)")
sids = set()
for c in vistos.values():
    for o in c.get("outputs") or []:
        sids.add(o.get("series_id"))
for sid in (23, 3524, 3382, 26265, 872, 19, 868, 1540, 1541, 26261, 26616) + tuple(sorted(x for x in sids if x))[:30]:
    for sub in ("observaciones", "pronosticos"):
        st, d, e = jget(f"{INA}/obs/puntual/series/{sid}/{sub}?timestart={hoy - dt.timedelta(days=3)}&timeend={hoy + dt.timedelta(days=30)}&format=json", 60)
        if st == 200 and d:
            rows = d if isinstance(d, list) else d.get("rows", d)
            if isinstance(rows, list) and rows:
                print(f"serie {sid}/{sub}: {len(rows)} filas; claves {sorted(rows[0].keys())[:8]}; primeras/últimas:", [(str(x.get("timestart"))[:10], x.get("valor")) for x in rows[:3]], "...", [(str(x.get("timestart"))[:10], x.get("valor")) for x in rows[-8:]])
                continue
        if sub == "observaciones":
            print(f"serie {sid}/{sub}: {st} {str(e)[:60] if e else 'sin filas'}")

# ------------------------------------------------------------------ C. INA directories
seccion("C. Directorios del INA (alerta.ina.gob.ar/ina/...)")


def listar(url, n=40):
    st, ct, b, e = get(url, 40)
    if st != 200:
        print(f"{url} -> {st} {e or ''}")
        return []
    h = texto(b)
    filas = re.findall(r"<a href=\"([^\"]+)\">([^<]+)</a>\s*([^<\n]*)", h)
    print(f"{url} -> {len(filas)} entradas")
    for href, nom, resto in filas[-n:]:
        print("   ", nom, "|", scrub(resto)[:60])
    return [(urljoin(url, href), nom) for href, nom, _ in filas]


for d in ("08-PRONOSTICOS/", "06-INFORMES/", "mapa_informe_semanal/"):
    sub = listar("https://alerta.ina.gob.ar/ina/" + d, 30)
    for href, nom in sub:
        if nom.endswith("/") and re.search(r"seman|escenar|pronost|perspect|parana|plata|mensual|boletin", nom, re.I) and nom != "../":
            listar(href, 12)

# ------------------------------------------------------------------ D. INA site, Prefectura, Itaipú, Goya, others
seccion("D1. www.ina.gob.ar: enlaces relevantes")
st, ct, b, e = get("https://www.ina.gob.ar/", 30)
if st == 200:
    for href, txt in enlaces(texto(b), "https://www.ina.gob.ar/"):
        if re.search(r"hidrol|escenari|pron[oó]stic|informe|alerta|cuenca|semanal|crecida|perspectiv", txt + href, re.I):
            print("  ", repr(txt[:80]), href[:140])
for u in ("https://ina.gob.ar/alerta/index.php?seccion=10", "https://www.ina.gob.ar/alerta/index.php?seccion=10"):
    st, ct, b, e = get(u, 30)
    print(u, "->", st, e or "", titulo(texto(b)) if st == 200 else "")
    if st == 200:
        for href, txt in enlaces(texto(b), u)[:60]:
            if re.search(r"pron[oó]stic|informe|escenari|semanal|perspectiv|parana|paraná", txt + href, re.I):
                print("    ", repr(txt[:70]), href[:140])

seccion("D2. Prefectura (reintento con plazo largo) y su portada")
for u in ("https://contenidosweb.prefecturanaval.gob.ar/alturas/", "http://contenidosweb.prefecturanaval.gob.ar/alturas/", "https://contenidosweb.prefecturanaval.gob.ar/alturas/index.php"):
    st, ct, b, e = get(u, 60)
    print(u, "->", st, e or "", len(b), "B")
    if st == 200:
        t = sin_html(texto(b))
        for m in re.finditer(r"Goya", t):
            print("   ", t[max(0, m.start() - 100):m.start() + 200])
            break
        break
st, ct, b, e = get("https://www.argentina.gob.ar/prefecturanaval", 30)
if st == 200:
    for href, txt in enlaces(texto(b), "https://www.argentina.gob.ar/prefecturanaval"):
        if re.search(r"inundaci|crecida|r[ií]o|isle[nñ]|creciente|altura", txt, re.I):
            print("  portada:", repr(txt[:120]), href[:150])
            st2, ct2, b2, e2 = get(href, 30)
            if st2 == 200:
                h2 = texto(b2)
                m = re.search(r"<h1[^>]*>(.*?)</h1>", h2, re.S)
                f = re.search(r"(\d{1,2} de \w+ de 20\d\d)", sin_html(h2))
                cuerpo = sin_html(h2[m.end():]) if m else ""
                print("     ", f.group(1) if f else "-", "|", cuerpo[:700])

seccion("D3. Itaipú (artículo y páginas del río)")
for u in ("https://www.itaipu.gov.br/noticias/sala-de-imprensa/nivel-do-rio-parana-deve-seguir-estavel-no-feriadao-preve-itaipu", "https://www.itaipu.gov.py/energia/embalse/rio-parana", "https://www.itaipu.gov.py/energia/hidrologia/boletin"):
    st, ct, b, e = get(u, 40)
    if st == 200:
        h = texto(b)
        m = re.search(r"<h1[^>]*>(.*?)</h1>", h, re.S)
        cuerpo = sin_html(h[m.end():]) if m else sin_html(h)
        f = re.search(r"(\d{1,2}/\d{1,2}/20\d\d|\d{1,2} de \w+ de 20\d\d|20\d\d-\d\d-\d\d)", cuerpo[:600])
        print(u[:110], "->", titulo(h), "| fecha:", f.group(1) if f else "-")
        print("    ", cuerpo[:1400])
        for href, txt in enlaces(h, u):
            if re.search(r"boletin|boletim|\.pdf|hidrol", href + txt, re.I) and "itaipu" in href:
                print("     enlace:", repr(txt[:60]), href[:140])
    else:
        print(u[:110], "->", st, e or "")

seccion("D4. Goya: portada, defensa civil, noticias del río")
for u in ("https://www.goya.gob.ar/",):
    st, ct, b, e = get(u, 30)
    if st == 200:
        h = texto(b)
        for href, txt in enlaces(h, u):
            if re.search(r"defensa|r[ií]o|crecida|altura|paran|emergencia|creciente|inunda|evacua", txt + href, re.I):
                print("  ", repr(txt[:110]), href[:140])
        t = sin_html(h)
        for m in re.finditer(r"r[ií]o Paran|crecida|Defensa Civil|evacua", t, re.I):
            print("  texto:", t[max(0, m.start() - 120):m.start() + 240])
for u in ("https://www.goya.gob.ar/?q=defensa-civil", "https://www.goya.gob.ar/?q=boletin-prensa&page=0"):
    st, ct, b, e = get(u, 30)
    print(u, "->", st, e or "", len(b), "B")
    if st == 200:
        for href, txt in enlaces(texto(b), u):
            if re.search(r"r[ií]o|crecida|altura|paran|defensa|emergencia|creciente", txt, re.I):
                print("    ", repr(txt[:120]), href[:140])

seccion("D5. Corrientes y Yacyretá: otros hosts (sin desactivar la verificación TLS)")
for u in ("https://defensacivil.corrientes.gob.ar/", "https://www.defensacivil.corrientes.gob.ar/", "https://proteccioncivil.corrientes.gob.ar/", "https://www.eby.gob.ar/", "https://eby.org.ar/hidrologia", "https://www.corrientes.gov.ar/", "https://www.mapaweb.corrientes.gob.ar/"):
    st, ct, b, e = get(u, 25)
    print(u, "->", st, e or "", len(b), "B", titulo(texto(b)) if st == 200 else "")
print("\nfin de la sonda", dt.datetime.now(dt.timezone.utc).isoformat()[:19])
