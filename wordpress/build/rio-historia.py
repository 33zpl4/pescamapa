#!/usr/bin/env python3
"""Histórico del río para notas (20/11/2026): a research tool, not a site feature. Reads the whole daily history
(1990 -> today) of the river stations from the INA a5 API and writes the raw material for an editorial note:

  python3 -I wordpress/build/rio-historia.py SALIDA/      fetch, clean, compute, write the files below
  python3 -I wordpress/build/rio-historia.py --autotest   offline self-check on a synthetic fixture (no network)

Output directory:
  resumen.json            every number, per station, in metres (machine-readable)
  resumen.md              a plain table + the raw facts as sentences (no interpretation) + the rules used
  datos-<clave>.csv       per calendar day: reference statistics, the two reference years, the current year
  grafico-<clave>.svg     one chart per station (1200 px wide viewBox; pure string building, no libraries)
  panel.svg               small multiples of every station, upstream -> downstream

Stations: every non-tidal station with an "estacion" in wordpress/plugins/pescamapa-core/data/rio.json, plus the
reference stations Corrientes (19) and Santa Fe (30), which have no page on the site (REFERENCIA below).
Read-only towards INA. Nothing is written to the site.

Cleaning rules (docs/rio.md, "Histórico para notas"):
  1. One value per LOCAL day (Argentina, UTC-3): the mean of that day's readings, rounded to the cm.
  2. Anything that is not a number, or is outside -2..60 m, is dropped.
  3. Exact 0.00 looks like a gap in some series. A run of consecutive 0.00 values is dropped when the reading just
     before it or just after it is more than 1 m away from 0; a run next to readings within 1 m of 0 is kept.
Statistics: reference period = 1990 .. last complete year; days are aligned by calendar date (1 March is always index
60; 29 February has its own slot with fewer years). Percentiles by linear interpolation; the percentile of "today" is
the share of reference years below it (ties count half). Chart bands and median are smoothed with a centred 15-day
moving average (the statistics, CSV and resumen are not). Years for "lowest / highest year" need >= 300 days of data.
"""
import csv, io, json, os, sys, time, datetime as dt
from concurrent.futures import ThreadPoolExecutor
from urllib.request import Request, urlopen
from urllib.parse import urlencode
from xml.sax.saxutils import escape

INA = "https://alerta.ina.gob.ar/a5"
CONFIG = "wordpress/plugins/pescamapa-core/data/rio.json"
PRIMER_ANIO = 1990
MIN_DIAS_ANIO_REF = 300
LIMITES = (-2.0, 60.0)
# Reference stations without a page on the site: clave -> (INA id, name, river, latitude for the upstream -> downstream order)
REFERENCIA = {
    "corrientes": (19, "Corrientes", "Paraná", -27.47),
    "santa-fe": (30, "Santa Fe", "Paraná", -31.65),
}
MESES = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"]
MES3 = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"]
INI_MES = [0, 31, 60, 91, 121, 152, 182, 213, 244, 274, 305, 335]
# Brand (docs/marca.md): hex values live here because a standalone SVG cannot read the site's CSS variables.
C = {"paper": "#fbfaf6", "ink": "#14251f", "forest": "#11251f", "lime": "#d8e46a", "moss": "#344a3d", "olive": "#5e6b33", "muted": "#5f685e", "line": "#d9ddd2", "cream": "#f4f2e9"}
F_TXT = "Outfit, 'Helvetica Neue', Arial, sans-serif"
F_TIT = "'Playfair Display', Georgia, 'Times New Roman', serif"
F_MARCA = "'Bebas Neue', Impact, 'Arial Narrow', sans-serif"


def get_json(url, intentos=3, timeout=120):
    ultimo = None
    for i in range(intentos):
        try:
            req = Request(url, headers={"User-Agent": "pescamapa-rio-historia/1.0 (+https://pescamapa.com)"})
            with urlopen(req, timeout=timeout) as r:
                return json.loads(r.read().decode("utf-8"))
        except Exception as e:  # noqa: BLE001 - report and retry
            ultimo = e
            time.sleep(3 * (i + 1))
    raise RuntimeError(f"{url.split('?')[0]}: {ultimo}")


# ---------------------------------------------------------------- cleaning
def fecha_local(ts):
    t = dt.datetime.strptime(ts[:19], "%Y-%m-%dT%H:%M:%S") - dt.timedelta(hours=3)
    return t.date().isoformat()


def diarios(filas):
    """Raw rows [{timestart, valor}] -> sorted [(YYYY-MM-DD, metres)], one mean per local day."""
    por_dia = {}
    for f in filas:
        v = f.get("valor")
        if isinstance(v, bool) or not isinstance(v, (int, float)) or v != v or not (LIMITES[0] <= v <= LIMITES[1]):
            continue
        try:
            d = fecha_local(str(f["timestart"]))
        except (KeyError, ValueError):
            continue
        por_dia.setdefault(d, []).append(float(v))
    return sorted((d, round(sum(vs) / len(vs), 2)) for d, vs in por_dia.items())


def quitar_ceros(serie):
    """Drops runs of exact 0.00 that sit next to a reading more than 1 m away (a gap, not a river at zero)."""
    out, i, n = [], 0, len(serie)
    while i < n:
        if serie[i][1] != 0:
            out.append(serie[i])
            i += 1
            continue
        j = i
        while j + 1 < n and serie[j + 1][1] == 0:
            j += 1
        vecinos = ([serie[i - 1][1]] if i > 0 else []) + ([serie[j + 1][1]] if j + 1 < n else [])
        if not any(abs(v) > 1.0 for v in vecinos):
            out.extend(serie[i:j + 1])
        i = j + 1
    return out


# ---------------------------------------------------------------- statistics
def indice(fecha):
    """YYYY-MM-DD -> 0..365 by calendar date (leap-year template: 1 March is always 60)."""
    return dt.date(2000, int(fecha[5:7]), int(fecha[8:10])).timetuple().tm_yday - 1


def mmdd(i):
    return (dt.date(2000, 1, 1) + dt.timedelta(days=i)).strftime("%m-%d")


def percentil(ordenados, q):
    """Linear interpolation over an ascending list; q in 0..100."""
    if len(ordenados) == 1:
        return ordenados[0]
    pos = q / 100 * (len(ordenados) - 1)
    lo = int(pos)
    hi = min(lo + 1, len(ordenados) - 1)
    return ordenados[lo] + (ordenados[hi] - ordenados[lo]) * (pos - lo)


def rango_percentil(x, valores):
    """Share (0..100) of the values below x, ties counting half."""
    menos = sum(1 for v in valores if v < x)
    iguales = sum(1 for v in valores if v == x)
    return 100 * (menos + iguales / 2) / len(valores)


def categoria(p):
    """Descriptive label by percentile: <10 muy bajo, <25 bajo, 25-75 normal, <=90 alto, else muy alto."""
    return "muy bajo" if p < 10 else "bajo" if p < 25 else "normal" if p <= 75 else "alto" if p <= 90 else "muy alto"


def r2(x):
    return None if x is None else round(x, 2)


def cambio(por_fecha, fecha, dias):
    """Metres between the reading of `fecha` and the value `dias` days earlier (nearest day within +-2), or None."""
    base = dt.date.fromisoformat(fecha)
    for off in (0, -1, 1, -2, 2):
        d = (base - dt.timedelta(days=dias - off)).isoformat()
        if d in por_fecha:
            return round(por_fecha[fecha] - por_fecha[d], 2)
    return None


def suave(v, r=7):
    """Centred circular moving average (chart only); None entries are skipped."""
    n = len(v)
    out = []
    for i in range(n):
        xs = [v[(i + j) % n] for j in range(-r, r + 1) if v[(i + j) % n] is not None]
        out.append(sum(xs) / len(xs) if xs else None)
    return out


def resumen_estacion(serie):
    """serie: cleaned [(date, m)] ascending. Returns every number of the station (metres)."""
    por_fecha = dict(serie)
    fecha, valor = serie[-1]
    anio = int(fecha[:4])
    ult_ref = anio - 1
    por_anio = {}
    for d, v in serie:
        por_anio.setdefault(int(d[:4]), {})[d] = v
    ref = {a: dias for a, dias in por_anio.items() if PRIMER_ANIO <= a <= ult_ref}
    por_dia = [[] for _ in range(366)]
    for a, dias in ref.items():
        for d, v in dias.items():
            por_dia[indice(d)].append((v, a))
    tabla = []
    for lista in por_dia:
        if not lista:
            tabla.append(None)
            continue
        vs = sorted(v for v, _ in lista)
        tabla.append({
            "n": len(vs), "min": vs[0], "p10": percentil(vs, 10), "p25": percentil(vs, 25), "mediana": percentil(vs, 50),
            "p75": percentil(vs, 75), "p90": percentil(vs, 90), "max": vs[-1],
            "anio_min": min(lista)[1], "anio_max": max(lista, key=lambda t: (t[0], -t[1]))[1],
        })
    i = indice(fecha)
    t = tabla[i]
    vals_hoy = sorted(v for v, _ in por_dia[i])
    pct = rango_percentil(valor, vals_hoy) if vals_hoy else None
    # lowest / highest mean year among the reference years with enough days
    cand = [(sum(d.values()) / len(d), a) for a, d in ref.items() if len(d) >= MIN_DIAS_ANIO_REF]
    refs = {}
    if len(cand) >= 2:
        for tipo, (media, a) in (("bajo", min(cand)), ("alto", max(cand))):
            v = [None] * 366
            for d, x in por_anio[a].items():
                v[indice(d)] = x
            refs[tipo] = {"anio": a, "media": round(media, 2), "mismo_dia": r2(v[i]), "v": v}
    actual = [None] * 366
    for d, x in por_anio[anio].items():
        actual[indice(d)] = x
    anual = {str(a): {"min": min(d.values()), "media": round(sum(d.values()) / len(d), 2), "max": max(d.values()), "dias": len(d)} for a, d in sorted(por_anio.items())}
    vmin = min(serie, key=lambda s: (s[1], s[0]))
    vmax = max(serie, key=lambda s: (s[1], s[0]))
    # mean of January 1 -> the reading's calendar day, for every year with at least 80 % of those days
    ventana, medias = i + 1, []
    for a, dias in por_anio.items():
        xs = [v for d, v in dias.items() if indice(d) <= i]
        if len(xs) >= 0.8 * ventana:
            medias.append((sum(xs) / len(xs), a))
    medias.sort()
    mi = next((k for k, (_, a) in enumerate(medias, 1) if a == anio), None)
    return {
        "fecha": fecha, "valor": valor, "anio": anio, "ref": [PRIMER_ANIO, ult_ref], "idx": i, "dia": f"{int(fecha[8:10])} de {MESES[int(fecha[5:7]) - 1]}",
        "dia_ref": None if t is None else {k: (r2(x) if isinstance(x, float) else x) for k, x in t.items()},
        "percentil": None if pct is None else round(pct, 1), "categoria": None if pct is None else categoria(pct),
        "dif_mediana": None if t is None else round(valor - t["mediana"], 2),
        "cambio_30d": cambio(por_fecha, fecha, 30), "cambio_90d": cambio(por_fecha, fecha, 90),
        "anio_bajo": None if "bajo" not in refs else {k: refs["bajo"][k] for k in ("anio", "media", "mismo_dia")},
        "anio_alto": None if "alto" not in refs else {k: refs["alto"][k] for k in ("anio", "media", "mismo_dia")},
        "anual": anual,
        "record_bajo": {"valor": vmin[1], "fecha": vmin[0]}, "record_alto": {"valor": vmax[1], "fecha": vmax[0]},
        "ventana_media": {"hasta": fecha, "media": round(medias[mi - 1][0], 2) if mi else None, "puesto": mi, "de": len(medias),
                          "mas_bajos": [{"anio": a, "media": round(m, 2)} for m, a in medias[:3]]},
        "anios_con_datos": len(por_anio), "primer_anio": min(por_anio), "dias_leidos": len(serie),
        # not in the json: the arrays the charts and the CSV need
        "_tabla": tabla, "_actual": actual, "_refs": refs,
    }


# ---------------------------------------------------------------- text
def fm(x, d=2):
    return "—" if x is None else f"{x:.{d}f}".replace(".", ",")


def fs(x, d=2):
    """Signed: +0,45 / −1,10."""
    return "—" if x is None else ("+" if x > 0.004 else "−" if x < -0.004 else "±") + fm(abs(x), d)


def fecha_larga(ymd):
    return f"{int(ymd[8:10])} de {MESES[int(ymd[5:7]) - 1]} de {ymd[:4]}"


def md(estaciones, nombres):
    """resumen.md: the table + the raw facts as sentences, no interpretation."""
    hasta = max(r["fecha"] for r in estaciones.values())
    ref = next(iter(estaciones.values()))["ref"]
    o = [f"# Histórico del río para notas: lecturas hasta el {fecha_larga(hasta)}", "",
         f"Referencia: {ref[0]}–{ref[1]}. Alturas de escala en metros (cada escala tiene su propio cero; no se comparan entre estaciones). "
         "Datos: INA (escalas de Prefectura Naval Argentina). Elaboración: Pescamapa. De norte a sur.", "",
         "| Estación | Lectura | Hoy (m) | Mediana de la fecha (m) | Diferencia (m) | Percentil | Categoría | 30 días (m) | 90 días (m) | Año más bajo: mismo día (m) | Año más alto: mismo día (m) | Mínimo histórico (m, fecha) | Máximo histórico (m, fecha) | Media 1/1→hoy: puesto |",
         "|---|---|---|---|---|---|---|---|---|---|---|---|---|---|"]
    for clave, r in estaciones.items():
        d = r["dia_ref"] or {}
        ab, aa, vm = r["anio_bajo"], r["anio_alto"], r["ventana_media"]
        o.append("| {n} | {f} | {v} | {m} | {df} | {p} | {c} | {c30} | {c90} | {b} | {a} | {rb} | {ra} | {pu} |".format(
            n=nombres[clave], f=r["fecha"], v=fm(r["valor"]), m=fm(d.get("mediana")), df=fs(r["dif_mediana"]),
            p="—" if r["percentil"] is None else f"{r['percentil']:.0f}", c=r["categoria"] or "—", c30=fs(r["cambio_30d"]), c90=fs(r["cambio_90d"]),
            b="—" if not ab else f"{fm(ab['mismo_dia'])} ({ab['anio']})", a="—" if not aa else f"{fm(aa['mismo_dia'])} ({aa['anio']})",
            rb=f"{fm(r['record_bajo']['valor'])} ({r['record_bajo']['fecha']})", ra=f"{fm(r['record_alto']['valor'])} ({r['record_alto']['fecha']})",
            pu="—" if not vm["puesto"] else f"{vm['puesto']} de {vm['de']} (1 = el más bajo)"))
    o += ["", "## Hechos por estación", "",
          "Categoría: descripción por percentil (menos de 10 muy bajo, hasta 25 bajo, 25 a 75 normal, hasta 90 alto, más muy alto). No es un pronóstico.", ""]
    for clave, r in estaciones.items():
        d = r["dia_ref"]
        s = [f"**{nombres[clave]}**: {fm(r['valor'])} m el {fecha_larga(r['fecha'])}."]
        if d:
            s.append(f"Para el {r['dia']}, {d['n']} años con dato ({r['ref'][0]}–{r['ref'][1]}): mínimo {fm(d['min'])} m ({d['anio_min']}), percentil 10 {fm(d['p10'])} m, "
                     f"percentil 25 {fm(d['p25'])} m, mediana {fm(d['mediana'])} m, percentil 75 {fm(d['p75'])} m, percentil 90 {fm(d['p90'])} m, máximo {fm(d['max'])} m ({d['anio_max']}).")
            s.append(f"La lectura queda en el percentil {r['percentil']:.0f}: más bajo que el {100 - r['percentil']:.0f} % de esos años y más alto que el {r['percentil']:.0f} %; "
                     f"diferencia con la mediana {fs(r['dif_mediana'])} m.")
        s.append(f"Cambio: {fs(r['cambio_30d'])} m en 30 días y {fs(r['cambio_90d'])} m en 90 días.")
        for k, et in (("anio_bajo", "menor"), ("anio_alto", "mayor")):
            a = r[k]
            if a:
                s.append(f"El año de {et} altura media de la referencia fue {a['anio']} (media {fm(a['media'])} m); el mismo día midió {fm(a['mismo_dia'])} m.")
        s.append(f"Récords de la serie ({r['primer_anio']}–{r['anio']}): mínimo {fm(r['record_bajo']['valor'])} m el {r['record_bajo']['fecha']}, máximo {fm(r['record_alto']['valor'])} m el {r['record_alto']['fecha']}.")
        vm = r["ventana_media"]
        if vm["puesto"]:
            masb = ", ".join(f"{x['anio']} ({fm(x['media'])} m)" for x in vm["mas_bajos"])
            s.append(f"La media del 1 de enero al {r['dia']} de {r['anio']} es {fm(vm['media'])} m: puesto {vm['puesto']} de {vm['de']} años con datos suficientes, ordenados de menor a mayor. Las tres medias más bajas del mismo tramo: {masb}.")
        s.append(f"Datos: {r['anios_con_datos']} años con lecturas (desde {r['primer_anio']}), {r['dias_leidos']} días limpios.")
        o += [" ".join(s), ""]
    o += ["## Reglas", "",
          "- Un valor por día local (UTC−3): el promedio de las lecturas del día, al centímetro.",
          "- Se descarta lo que no es número o queda fuera de −2 a 60 m. Una racha de 0,00 exacto se descarta si la lectura anterior o la siguiente está a más de 1 m de 0; si está a menos, se conserva.",
          "- Referencia: años completos desde 1990; cada día se compara con el mismo día del calendario (el 29 de febrero tiene su propio lugar, con menos años). Percentiles por interpolación lineal.",
          "- Percentil de la lectura: parte de los años de referencia con ese día por debajo de la lectura (empates cuentan la mitad).",
          "- Año más bajo / más alto: el de menor / mayor altura media entre los años de referencia con al menos 300 días de datos.",
          "- Puesto de la media: se compara la media del 1 de enero a esa fecha de cada año con al menos el 80 % de esos días.",
          "- Las bandas y la mediana de los gráficos van suavizadas con promedio móvil de 15 días; las tablas y los CSV no.",
          "- Lo que no se afirma: pronóstico, profundidad real, comparación entre estaciones, ni efecto sobre la pesca."]
    return "\n".join(o) + "\n"


# ---------------------------------------------------------------- csv
def csv_estacion(r):
    t, refs, act = r["_tabla"], r["_refs"], r["_actual"]
    cab = ["mes_dia", "anios_con_datos", "minimo_m", "p10_m", "p25_m", "mediana_m", "p75_m", "p90_m", "maximo_m", "anio_del_minimo", "anio_del_maximo"]
    for tipo in ("bajo", "alto"):
        if tipo in refs:
            cab.append(f"{refs[tipo]['anio']}_{'anio_mas_bajo' if tipo == 'bajo' else 'anio_mas_alto'}_m")
    cab.append(f"{r['anio']}_m")
    buf = io.StringIO()
    w = csv.writer(buf, lineterminator="\n")
    w.writerow(cab)
    f = lambda x: "" if x is None else f"{x:.2f}"  # noqa: E731
    for i in range(366):
        d = t[i]
        if d is None and act[i] is None:
            continue
        fila = [mmdd(i)] + ([d["n"], f(d["min"]), f(d["p10"]), f(d["p25"]), f(d["mediana"]), f(d["p75"]), f(d["p90"]), f(d["max"]), d["anio_min"], d["anio_max"]] if d else [0, "", "", "", "", "", "", "", "", ""])
        fila += [f(refs[tipo]["v"][i]) for tipo in ("bajo", "alto") if tipo in refs] + [f(act[i])]
        w.writerow(fila)
    return buf.getvalue()


# ---------------------------------------------------------------- svg
def nice(lo, hi):
    span = hi - lo
    paso = 2.0 if span > 8 else 1.0 if span > 3.5 else 0.5 if span > 1.6 else 0.25
    a = (lo // paso) * paso
    b = -(-hi // paso) * paso
    return a, (b if b > a else a + paso), paso


def trazo(v, X, Y):
    d, ant = [], False
    for i, x in enumerate(v):
        if x is None:
            ant = False
            continue
        d.append(("L" if ant else "M") + f"{X(i):.1f} {Y(x):.1f}")
        ant = True
    return "".join(d)


def banda(a, b, X, Y):
    pts = [(i, x) for i, x in enumerate(a) if x is not None]
    p = [f"{'L' if k else 'M'}{X(i):.1f} {Y(x):.1f}" for k, (i, x) in enumerate(pts)]
    q = [f"L{X(i):.1f} {Y(x):.1f}" for i, x in reversed(list(enumerate(b))) if x is not None]
    return "".join(p) + "".join(q) + "Z"


def capas(r):
    """The smoothed arrays shared by the chart and the panel."""
    t = r["_tabla"]
    col = lambda k: suave([None if d is None else d[k] for d in t])  # noqa: E731
    return {"p10": col("p10"), "p25": col("p25"), "p50": col("mediana"), "p75": col("p75"), "p90": col("p90")}


def grafico(r, nombre, curso):
    """One chart (1200 x 900 viewBox; text sized to stay legible at 390 px wide)."""
    W, H = 1200, 1160
    L, Rm, T, B = 120, 210, 330, 310
    pw, ph = W - L - Rm, H - T - B
    k = capas(r)
    refs, act = r["_refs"], r["_actual"]
    series = [k["p10"], k["p90"], act] + [refs[t]["v"] for t in refs]
    vals = [x for s in series for x in s if x is not None]
    y0, y1, paso = nice(min(vals), max(vals))
    X = lambda i: L + i / 365 * pw  # noqa: E731
    Y = lambda m: T + (y1 - m) / (y1 - y0) * ph  # noqa: E731
    d = r["dia_ref"] or {}
    ref0, ref1 = r["ref"]
    titulo = f"Altura del río {curso} en {nombre}: {r['anio']} contra {ref0}–{ref1}"
    sub = f"{fm(r['valor'])} m el {fecha_larga(r['fecha'])}" + (f" · percentil {r['percentil']:.0f} de la fecha · mediana {fm(d.get('mediana'))} m" if d else "")
    sub1 = f"{fm(r['valor'])} m · {fecha_larga(r['fecha'])}"
    sub2 = f"Percentil {r['percentil']:.0f} de la fecha · mediana {fm(d.get('mediana'))} m" if d else ""
    sub_t = f"Altura del río {curso}: {r['anio']} contra {ref0}–{ref1}"
    s = [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-labelledby="t d" font-family="{F_TXT}">',
         f'<title id="t">{escape(titulo)}</title>',
         f'<desc id="d">{escape(sub)}. Banda clara: percentiles 10 a 90 de cada día; banda oscura: 25 a 75; línea fina: mediana; línea gruesa: {r["anio"]}; trazos: años de menor y mayor altura media.</desc>',
         f'<rect width="{W}" height="{H}" fill="{C["paper"]}"/>',
         f'<text x="{L - 80}" y="64" font-family="{F_MARCA}" font-size="34" letter-spacing="2" fill="{C["olive"]}">PESCAMAPA</text>',
         f'<text x="{L - 80}" y="120" font-family="{F_TIT}" font-size="58" font-weight="700" fill="{C["ink"]}">{escape(nombre)}</text>',
         f'<text x="{L - 80}" y="168" font-size="34" fill="{C["moss"]}">{escape(sub_t)}</text>',
         f'<text x="{L - 80}" y="222" font-size="40" font-weight="700" fill="{C["forest"]}">{escape(sub1)}</text>',
         f'<text x="{L - 80}" y="266" font-size="32" fill="{C["moss"]}">{escape(sub2)}</text>']
    g = y0
    while g <= y1 + 1e-9:
        y = Y(g)
        s.append(f'<line x1="{L}" x2="{W - Rm}" y1="{y:.1f}" y2="{y:.1f}" stroke="{C["line"]}" stroke-width="2"/>'
                 f'<text x="{L - 12}" y="{y + 12:.1f}" font-size="32" text-anchor="end" fill="{C["muted"]}">{fm(g, 1 if paso < 1 else 0)}</text>')
        g += paso
    s.append(f'<text x="{L - 80}" y="{T - 14}" font-size="30" fill="{C["muted"]}">metros</text>')
    for m, i0 in enumerate(INI_MES):
        s.append(f'<text x="{X(i0 + 15):.1f}" y="{T + ph + 48}" font-size="32" text-anchor="middle" fill="{C["muted"]}">{MES3[m]}</text>')
    s.append(f'<path d="{banda(k["p10"], k["p90"], X, Y)}" fill="{C["moss"]}" fill-opacity="0.13"/>')
    s.append(f'<path d="{banda(k["p25"], k["p75"], X, Y)}" fill="{C["moss"]}" fill-opacity="0.24"/>')
    s.append(f'<path d="{trazo(k["p50"], X, Y)}" fill="none" stroke="{C["moss"]}" stroke-width="3"/>')
    for tipo, color in (("bajo", C["olive"]), ("alto", C["muted"])):
        if tipo in refs:
            s.append(f'<path d="{trazo(refs[tipo]["v"], X, Y)}" fill="none" stroke="{color}" stroke-width="3.5" stroke-dasharray="14 9"/>')
    s.append(f'<path d="{trazo(act, X, Y)}" fill="none" stroke="{C["forest"]}" stroke-width="7" stroke-linejoin="round" stroke-linecap="round"/>')
    cx, cy = X(r["idx"]), Y(r["valor"])
    s.append(f'<circle cx="{cx:.1f}" cy="{cy:.1f}" r="13" fill="{C["lime"]}" stroke="{C["forest"]}" stroke-width="5"/>')
    # direct labels at the right end of every line, pushed apart when they collide
    med_fin = next(x for x in reversed(k["p50"]) if x is not None)
    etq = [(Y(med_fin), "Mediana", C["moss"])]
    for tipo, color in (("bajo", C["olive"]), ("alto", C["muted"])):
        if tipo in refs:
            ult = next(x for x in reversed(refs[tipo]["v"]) if x is not None)
            etq.append((Y(ult), str(refs[tipo]["anio"]), color))
    ult_i = max(i for i, x in enumerate(act) if x is not None)
    if ult_i >= 340:
        etq.append((Y(act[ult_i]), str(r["anio"]), C["forest"]))
    else:  # this year ends today: its label sits next to the marker
        s.append(f'<text x="{min(cx + 22, W - Rm - 60):.1f}" y="{cy - 22:.1f}" font-size="34" font-weight="700" fill="{C["forest"]}">{r["anio"]}</text>')
    prev = -1e9
    for y, txt, color in sorted(etq):
        y = max(y + 12, prev + 42)
        prev = y
        s.append(f'<text x="{W - Rm + 14}" y="{y:.1f}" font-size="34" font-weight="700" fill="{color}">{escape(txt)}</text>')
    notas = [f"Banda clara: percentiles 10–90 de cada día de {ref0}–{ref1}; oscura: 25–75.",
             f"Línea fina: mediana (suavizada, 15 días). Punto: lectura del {r['fecha'][8:10]}/{r['fecha'][5:7]}.",
             "Trazos: " + " y ".join(f"{refs[t]['anio']}, año de {'menor' if t == 'bajo' else 'mayor'} altura media" for t in ("bajo", "alto") if t in refs) + ".",
             f"Datos: INA (escalas de Prefectura Naval Argentina), {r['primer_anio']}–{r['anio']}.", "Elaboración: Pescamapa."]
    for n, txt in enumerate(notas):
        s.append(f'<text x="{L - 80}" y="{T + ph + 112 + n * 38}" font-size="{27 if n < 3 else 28}" fill="{C["muted"] if n < 3 else C["moss"]}">{escape(txt)}</text>')
    s.append("</svg>")
    return "\n".join(s) + "\n"


def panel(estaciones, nombres):
    """Small multiples, two per row, upstream -> downstream."""
    n = len(estaciones)
    filas = -(-n // 2)
    W, CW, CH, TOP = 1200, 600, 420, 296
    H = TOP + filas * CH + 130
    hasta = max(r["fecha"] for r in estaciones.values())
    ref = next(iter(estaciones.values()))["ref"]
    s = [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-labelledby="t d" font-family="{F_TXT}">',
         f'<title id="t">El río Paraná, estación por estación, de norte a sur: {escape(fecha_larga(hasta))}</title>',
         f'<desc id="d">Un gráfico por estación: banda de percentiles 25 a 75 de cada día de {ref[0]}–{ref[1]}, mediana, el año en curso y la lectura más reciente.</desc>',
         f'<rect width="{W}" height="{H}" fill="{C["paper"]}"/>',
         f'<text x="40" y="64" font-family="{F_MARCA}" font-size="34" letter-spacing="2" fill="{C["olive"]}">PESCAMAPA</text>',
         f'<text x="40" y="118" font-family="{F_TIT}" font-size="48" font-weight="700" fill="{C["ink"]}">El Paraná, estación por estación</text>',
         f'<text x="40" y="164" font-size="34" fill="{C["moss"]}">De norte a sur · lecturas hasta el {escape(fecha_larga(hasta))}</text>',
         f'<text x="40" y="206" font-size="28" fill="{C["muted"]}">Banda: 25–75 % de los años {ref[0]}–{ref[1]} para cada día.</text>',
         f'<text x="40" y="240" font-size="28" fill="{C["muted"]}">Línea fina: mediana. Línea gruesa: este año. Punto: la última lectura.</text>',
         f'<text x="40" y="274" font-size="28" fill="{C["muted"]}">Cada gráfico tiene su propia escala de altura.</text>']
    for k_, (clave, r) in enumerate(estaciones.items()):
        x0 = (k_ % 2) * CW + 40
        y0 = TOP + (k_ // 2) * CH
        cw = CW - 80
        s.append(f'<text x="{x0}" y="{y0 + 44}" font-size="38" font-weight="700" fill="{C["ink"]}">{escape(nombres[clave])}</text>')
        d = r["dia_ref"]
        detalle = f"{fm(r['valor'])} m" + (f" · {r['categoria']} · {fs(r['dif_mediana'])} m vs mediana" if d else "")
        s.append(f'<text x="{x0}" y="{y0 + 86}" font-size="30" fill="{C["moss"]}">{escape(detalle)}</text>')
        k = capas(r)
        act = r["_actual"]
        vals = [x for sr in (k["p25"], k["p75"], act) for x in sr if x is not None]
        lo, hi = min(vals), max(vals)
        hi = hi if hi > lo else lo + 0.1
        py0, ph = y0 + 110, CH - 150
        X = lambda i, x0=x0, cw=cw: x0 + i / 365 * cw  # noqa: E731
        Y = lambda m, py0=py0, ph=ph, lo=lo, hi=hi: py0 + (hi - m) / (hi - lo) * ph  # noqa: E731
        s.append(f'<rect x="{x0}" y="{py0}" width="{cw}" height="{ph}" fill="{C["cream"]}" rx="10"/>')
        s.append(f'<path d="{banda(k["p25"], k["p75"], X, Y)}" fill="{C["moss"]}" fill-opacity="0.26"/>')
        s.append(f'<path d="{trazo(k["p50"], X, Y)}" fill="none" stroke="{C["moss"]}" stroke-width="3"/>')
        s.append(f'<path d="{trazo(act, X, Y)}" fill="none" stroke="{C["forest"]}" stroke-width="6" stroke-linejoin="round" stroke-linecap="round"/>')
        s.append(f'<circle cx="{X(r["idx"]):.1f}" cy="{Y(r["valor"]):.1f}" r="11" fill="{C["lime"]}" stroke="{C["forest"]}" stroke-width="4"/>')
    s.append(f'<text x="40" y="{H - 62}" font-size="28" fill="{C["moss"]}">Datos: INA (escalas de Prefectura Naval Argentina), {min(r["primer_anio"] for r in estaciones.values())}–{hasta[:4]}.</text>')
    s.append(f'<text x="40" y="{H - 24}" font-size="28" fill="{C["moss"]}">Elaboración: Pescamapa.</text></svg>')
    return "\n".join(s) + "\n"


# ---------------------------------------------------------------- run
def leer(item, hoy):
    clave, est = item
    filas = get_json(f"{INA}/obs/puntual/series/{est}/observaciones?" + urlencode({"timestart": f"{PRIMER_ANIO}-01-01", "timeend": hoy.isoformat(), "format": "json"}))
    if isinstance(filas, dict):
        filas = filas.get("rows") or filas.get("observaciones") or []
    bruta = diarios(filas)
    return clave, bruta, quitar_ceros(bruta)


def escribir(estaciones, nombres, cursos, salida):
    os.makedirs(salida, exist_ok=True)
    pub = {k: {kk: vv for kk, vv in r.items() if not kk.startswith("_")} | {"nombre": nombres[k], "curso": cursos[k]} for k, r in estaciones.items()}
    json.dump({"generado": dt.datetime.now(dt.timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"), "estaciones": pub}, open(f"{salida}/resumen.json", "w"), ensure_ascii=False, indent=1)
    open(f"{salida}/resumen.md", "w").write(md(estaciones, nombres))
    for k, r in estaciones.items():
        open(f"{salida}/datos-{k}.csv", "w").write(csv_estacion(r))
        open(f"{salida}/grafico-{k}.svg", "w").write(grafico(r, nombres[k], cursos[k]))
    open(f"{salida}/panel.svg", "w").write(panel(estaciones, nombres))


def main(salida):
    cfg = json.load(open(CONFIG))["destinos"]
    todas = {k: (c["estacion"], c["estacion_nombre"], c.get("rio", "Paraná"), c["lat"]) for k, c in cfg.items() if c.get("estacion") and not c.get("mareal")}
    todas.update(REFERENCIA)
    hoy = dt.datetime.now(dt.timezone.utc).astimezone(dt.timezone(dt.timedelta(hours=-3))).date()
    orden = sorted(todas, key=lambda k: -todas[k][3])  # north to south = upstream to downstream
    estaciones, avisos = {}, []
    with ThreadPoolExecutor(max_workers=4) as ex:
        futuros = [(k, ex.submit(leer, (k, todas[k][0]), hoy)) for k in orden]
        for k, f in futuros:
            try:
                _, bruta, limpia = f.result()
                if not limpia:
                    raise RuntimeError("sin observaciones")
                r = resumen_estacion(limpia)
                estaciones[k] = r
                print(f"{k} (est. {todas[k][0]}): {len(bruta)} días leídos, {len(bruta) - len(limpia)} quitados por la regla del cero, {r['anios_con_datos']} años desde {r['primer_anio']}, último {r['fecha']} = {r['valor']:.2f} m")
            except Exception as e:  # noqa: BLE001
                avisos.append(f"AVISO {k}: {e}")
    for a in avisos:
        print(a, file=sys.stderr)
    if not estaciones:
        sys.exit("ERROR: no se pudo leer ninguna estación.")
    escribir(estaciones, {k: todas[k][1] for k in estaciones}, {k: todas[k][2] for k in estaciones}, salida)
    print(f"OK: {len(estaciones)} estaciones -> {salida}/")


def autotest():
    import xml.etree.ElementTree as ET
    assert fecha_local("2026-10-19T02:59:59.000Z") == "2026-10-18" and fecha_local("2026-10-19T03:00:00.000Z") == "2026-10-19"
    filas = [{"timestart": "2020-05-01T09:00:00.000Z", "valor": 2.0}, {"timestart": "2020-05-01T21:00:00.000Z", "valor": 3.0},
             {"timestart": "2020-05-02T09:00:00.000Z", "valor": None}, {"timestart": "2020-05-03T09:00:00.000Z", "valor": 99},
             {"timestart": "2020-05-04T09:00:00.000Z", "valor": "x"}, {"timestart": "2020-05-05T09:00:00.000Z", "valor": 1.234}]
    assert diarios(filas) == [("2020-05-01", 2.5), ("2020-05-05", 1.23)], diarios(filas)
    assert quitar_ceros([("a", 3.1), ("b", 0.0), ("c", 3.0)]) == [("a", 3.1), ("c", 3.0)]
    assert quitar_ceros([("a", 3.1), ("b", 0.0), ("c", 0.0), ("d", 0.0), ("e", 2.9)]) == [("a", 3.1), ("e", 2.9)]
    assert quitar_ceros([("a", 0.3), ("b", 0.0), ("c", 0.0), ("d", 0.2)]) == [("a", 0.3), ("b", 0.0), ("c", 0.0), ("d", 0.2)]
    assert quitar_ceros([("a", 0.0), ("b", 0.0)]) == [("a", 0.0), ("b", 0.0)] and quitar_ceros([("a", 0.0), ("b", 1.5)]) == [("b", 1.5)]
    assert indice("2020-03-01") == indice("2021-03-01") == 60 and indice("2020-02-29") == 59 and indice("2021-12-31") == 365
    assert percentil([1, 2, 3, 4, 5], 50) == 3 and percentil([1, 2], 50) == 1.5 and percentil([7], 30) == 7
    assert rango_percentil(2.0, [1.0, 2.0, 2.0, 3.0]) == 50.0 and rango_percentil(0.5, [1.0, 2.0]) == 0.0 and rango_percentil(9, [1.0, 2.0]) == 100.0
    assert [categoria(x) for x in (5, 18, 50, 80, 95)] == ["muy bajo", "bajo", "normal", "alto", "muy alto"]
    assert cambio({"2020-02-01": 3.0, "2020-01-02": 2.5}, "2020-02-01", 30) == 0.5 and cambio({"2020-02-01": 3.0}, "2020-02-01", 30) is None
    # synthetic station: 2000..2019 complete, 2020 until 19 Nov; year Y has level 1 + (Y-2000)/10 (+0.05 from July)
    serie, d = [], dt.date(2000, 1, 1)
    while d <= dt.date(2020, 11, 19):
        serie.append((d.isoformat(), round(1 + (d.year - 2000) / 10 + (0.05 if d.month > 6 else 0), 2)))
        d += dt.timedelta(days=1)
    global PRIMER_ANIO
    PRIMER_ANIO = 2000
    r = resumen_estacion(serie)
    i = indice("2020-11-19")
    assert r["idx"] == i and r["dia_ref"]["n"] == 20 and r["dia_ref"]["mediana"] == 2.0 and r["dia_ref"]["min"] == 1.05 and r["dia_ref"]["anio_max"] == 2019
    assert r["valor"] == 3.05 and r["percentil"] == 100.0 and r["categoria"] == "muy alto" and r["dif_mediana"] == 1.05
    assert r["cambio_30d"] == 0.0 and r["cambio_90d"] == 0.0
    assert r["anio_bajo"]["anio"] == 2000 and r["anio_alto"]["anio"] == 2019 and r["anio_alto"]["mismo_dia"] == 2.95
    assert r["record_bajo"] == {"valor": 1.0, "fecha": "2000-01-01"} and r["anual"]["2020"]["dias"] == 324
    assert r["ventana_media"]["puesto"] == 21 and r["ventana_media"]["de"] == 21   # 2020 has the highest 1 Jan -> 19 Nov mean
    est = {"a": r, "b": r}
    nom = {"a": "Estación A", "b": "Estación B"}
    texto = md(est, nom)
    assert "Estación A" in texto and "puesto 21 de 21" in texto and "| Estación |" in texto and "percentil 100" in texto
    filas_csv = list(csv.reader(io.StringIO(csv_estacion(r))))
    assert filas_csv[0][0] == "mes_dia" and filas_csv[0][-1] == "2020_m" and len(filas_csv) == 367 and len(set(map(len, filas_csv))) == 1
    for svg in (grafico(r, "Estación A", "Paraná"), panel(est, nom)):
        ET.fromstring(svg)  # well-formed
        assert 'role="img"' in svg and "<title" in svg and "Elaboración: Pescamapa." in svg and 'viewBox="0 0 1200 ' in svg
    out = os.path.join(os.environ.get("TMPDIR", "/tmp"), "rio-historia-autotest")
    escribir(est, nom, {"a": "Paraná", "b": "Paraná"}, out)
    assert sorted(os.listdir(out)) == sorted(["resumen.json", "resumen.md", "datos-a.csv", "datos-b.csv", "grafico-a.svg", "grafico-b.svg", "panel.svg"])
    json.load(open(f"{out}/resumen.json"))
    print("autotest ok")


if __name__ == "__main__":
    if len(sys.argv) > 1 and sys.argv[1] in ("--autotest", "--test"):
        autotest()
    else:
        main(sys.argv[1] if len(sys.argv) > 1 else "rio-historia-salida")
