#!/usr/bin/env python3
"""Three infographics for the Goya note of 20/11/2026 (nota-parana-goya-2026-11-20), standard library only.

  python3 -I wordpress/build/rio-nota-graficos.py [entrada] [salida]

entrada (default wordpress/build/rio-nota-goya): resumen.json and datos-goya.csv, the files rio-historia.py wrote on
the branch `rio-historia` on 20/11/2026. salida (default the same folder): 1-goya-ano.svg, 2-goya-20-de-noviembre.svg,
3-crecida-rio-abajo.svg. Every number comes from those two files; nothing is typed in here. 1200 x 900 (4:3, the crop of
the note's cover), text >= 30 units so it reads at 390 px. PNGs: node wordpress/build/rio-nota-png.mjs.
"""
import csv
import importlib.util
import json
import os
import sys

AQUI = os.path.dirname(os.path.abspath(__file__))
spec = importlib.util.spec_from_file_location("rio_historia", os.path.join(AQUI, "rio-historia.py"))
RH = importlib.util.module_from_spec(spec)
spec.loader.exec_module(RH)
C, F_TXT, F_TIT, F_MARCA = RH.C, RH.F_TXT, RH.F_TIT, RH.F_MARCA
fm, fs, esc = RH.fm, RH.fs, RH.escape
W, H = 1200, 900
FUENTE = "Datos: Instituto Nacional del Agua (escalas de Prefectura Naval Argentina)."
AUTORIA = "Elaboración: Pescamapa."


def cabecera(titulo, sub, linea, desc_id="d"):
    return [
        f'<text x="40" y="58" font-family="{F_MARCA}" font-size="34" letter-spacing="2" fill="{C["olive"]}">PESCAMAPA</text>',
        f'<text x="40" y="118" font-family="{F_TIT}" font-size="56" font-weight="700" fill="{C["ink"]}">{esc(titulo)}</text>',
        f'<text x="40" y="164" font-size="34" fill="{C["moss"]}">{esc(sub)}</text>',
        f'<text x="40" y="212" font-size="38" font-weight="700" fill="{C["forest"]}">{esc(linea)}</text>',
    ]


def abrir(titulo, desc):
    return [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-labelledby="t d" font-family="{F_TXT}">',
            f'<title id="t">{esc(titulo)}</title>', f'<desc id="d">{esc(desc)}</desc>',
            f'<rect width="{W}" height="{H}" fill="{C["paper"]}"/>']


def pie(s, lineas, y0):
    for n, (txt, color) in enumerate(lineas):
        s.append(f'<text x="40" y="{y0 + n * 36}" font-size="30" fill="{color}">{esc(txt)}</text>')


def leer(entrada):
    res = json.load(open(os.path.join(entrada, "resumen.json"), encoding="utf-8"))
    filas = list(csv.DictReader(open(os.path.join(entrada, "datos-goya.csv"), encoding="utf-8")))
    return res["estaciones"], filas


def serie(filas, col):
    out = [None] * 366
    for f in filas:
        v = f.get(col, "")
        out[RH.indice("2000-" + f["mes_dia"])] = float(v) if v != "" else None
    return out


# ------------------------------------------------------------------ 1. the year
def grafico_ano(g, filas):
    cols = [c for c in filas[0] if c.endswith("_anio_mas_bajo_m") or c.endswith("_anio_mas_alto_m")]
    bajo, alto = (c for c in sorted(cols, key=lambda c: "alto" in c))
    a_bajo, a_alto = bajo.split("_")[0], alto.split("_")[0]
    act = serie(filas, f"{g['anio']}_m")
    k = {n: RH.suave(serie(filas, c)) for n, c in (("p10", "p10_m"), ("p25", "p25_m"), ("p50", "mediana_m"), ("p75", "p75_m"), ("p90", "p90_m"))}
    refs = {"bajo": (a_bajo, serie(filas, bajo)), "alto": (a_alto, serie(filas, alto))}
    d, r0, r1 = g["dia_ref"], g["ref"][0], g["ref"][1]
    fecha = RH.fecha_larga(g["fecha"])
    L, Rm, T, B = 100, 190, 268, 225
    pw, ph = W - L - Rm, H - T - B
    vals = [x for s in (k["p10"], k["p90"], act, refs["bajo"][1], refs["alto"][1]) for x in s if x is not None]
    y0, y1, paso = RH.nice(min(vals), max(vals))
    X = lambda i: L + i / 365 * pw  # noqa: E731
    Y = lambda m: T + (y1 - m) / (y1 - y0) * ph  # noqa: E731
    titulo = f"Paraná en Goya: {g['anio']} contra {r0}–{r1}"
    linea = f"{fm(g['valor'])} m el {fecha}"
    sub = f"Más alto que cualquier otro 20 de noviembre desde {g['primer_anio']}"
    desc = (f"Altura del río Paraná en Goya durante el año: {fm(g['valor'])} m el {fecha}, más que en cualquier 20 de noviembre de {r0} a {r1} "
            f"(mediana de la fecha {fm(d['mediana'])} m). Banda clara: percentiles 10 a 90 de cada día; oscura: 25 a 75; línea fina: mediana; "
            f"línea gruesa: {g['anio']}; trazos: {a_bajo}, año de menor altura media, y {a_alto}, el de mayor.")
    s = abrir(titulo, desc) + cabecera(titulo, sub, linea)
    gl = y0
    while gl <= y1 + 1e-9:
        y = Y(gl)
        s.append(f'<line x1="{L}" x2="{W - Rm}" y1="{y:.1f}" y2="{y:.1f}" stroke="{C["line"]}" stroke-width="2"/>'
                 f'<text x="{L - 12}" y="{y + 11:.1f}" font-size="30" text-anchor="end" fill="{C["muted"]}">{fm(gl, 1 if paso < 1 else 0)}</text>')
        gl += paso
    s.append(f'<text x="{L - 12}" y="{T - 16}" font-size="30" text-anchor="end" fill="{C["muted"]}">m</text>')
    for m, i0 in enumerate(RH.INI_MES):
        s.append(f'<text x="{X(i0 + 15):.1f}" y="{T + ph + 38}" font-size="30" text-anchor="middle" fill="{C["muted"]}">{RH.MES3[m]}</text>')
    s.append(f'<path d="{RH.banda(k["p10"], k["p90"], X, Y)}" fill="{C["moss"]}" fill-opacity="0.13"/>')
    s.append(f'<path d="{RH.banda(k["p25"], k["p75"], X, Y)}" fill="{C["moss"]}" fill-opacity="0.24"/>')
    s.append(f'<path d="{RH.trazo(k["p50"], X, Y)}" fill="none" stroke="{C["moss"]}" stroke-width="3"/>')
    for tipo, color in (("bajo", C["olive"]), ("alto", C["muted"])):
        s.append(f'<path d="{RH.trazo(refs[tipo][1], X, Y)}" fill="none" stroke="{color}" stroke-width="3.5" stroke-dasharray="14 9"/>')
    s.append(f'<path d="{RH.trazo(act, X, Y)}" fill="none" stroke="{C["forest"]}" stroke-width="7" stroke-linejoin="round" stroke-linecap="round"/>')
    cx, cy = X(g["idx"]), Y(g["valor"])
    s.append(f'<circle cx="{cx:.1f}" cy="{cy:.1f}" r="12" fill="{C["lime"]}" stroke="{C["forest"]}" stroke-width="5"/>')
    s.append(f'<text x="{cx - 24:.1f}" y="{cy - 44:.1f}" font-size="34" font-weight="700" text-anchor="end" fill="{C["forest"]}">{g["anio"]}: {fm(g["valor"])} m</text>')
    etq = []
    for tipo, color in (("bajo", C["olive"]), ("alto", C["muted"])):
        ult = next(x for x in reversed(refs[tipo][1]) if x is not None)
        etq.append((Y(ult), refs[tipo][0], color))
    etq.append((Y(next(x for x in reversed(k["p50"]) if x is not None)), "Mediana", C["moss"]))
    prev = -1e9
    for y, txt, color in sorted(etq):
        y = max(y + 11, prev + 40)
        prev = y
        s.append(f'<text x="{W - Rm + 14}" y="{y:.1f}" font-size="32" font-weight="700" fill="{color}">{esc(txt)}</text>')
    pie(s, [(f"Bandas: percentiles 10–90 (clara) y 25–75 (oscura) de {r0}–{r1}.", C["muted"]),
            (f"Trazos: {a_bajo}, año de menor altura media; {a_alto}, el de mayor.", C["muted"]),
            (FUENTE, C["moss"]), (AUTORIA, C["moss"])], T + ph + 80)
    s.append("</svg>")
    return "\n".join(s) + "\n"


# ------------------------------------------------------------------ 2. 20 November
def grafico_dia(g):
    d, r0, r1 = g["dia_ref"], g["ref"][0], g["ref"][1]
    fecha = RH.fecha_larga(g["fecha"])
    n = d["n"]
    L, R = 60, 1140
    x0, x1 = 0.0, 6.5
    X = lambda m: L + (m - x0) / (x1 - x0) * (R - L)  # noqa: E731
    titulo = "El 20 de noviembre en Goya"
    sub = f"{g['anio']} contra los {n} años de {r0} a {r1}"
    linea = f"{fm(g['valor'])} m: más alto que los {n} años anteriores"
    desc = (f"Altura del Paraná en Goya cada 20 de noviembre de {r0} a {r1}: mínimo {fm(d['min'])} m ({d['anio_min']}), percentiles 10 a 90 de {fm(d['p10'])} a {fm(d['p90'])} m, "
            f"25 a 75 de {fm(d['p25'])} a {fm(d['p75'])} m, mediana {fm(d['mediana'])} m, máximo {fm(d['max'])} m ({d['anio_max']}). "
            f"El {fecha}: {fm(g['valor'])} m, por encima de todos. Mismo día en {g['anio_bajo']['anio']} (año de menor altura media): {fm(g['anio_bajo']['mismo_dia'])} m; en {g['anio_alto']['anio']} (mayor): {fm(g['anio_alto']['mismo_dia'])} m.")
    s = abrir(titulo, desc) + cabecera(titulo, sub, linea)
    top = 262
    fila = 108
    ejes_y = top + 4 * fila + 8
    xa = X(g["valor"])
    # axis
    for m in range(0, 7):
        s.append(f'<line x1="{X(m):.1f}" x2="{X(m):.1f}" y1="{top - 14}" y2="{ejes_y}" stroke="{C["line"]}" stroke-width="2"/>'
                 f'<text x="{X(m):.1f}" y="{ejes_y + 36}" font-size="30" text-anchor="middle" fill="{C["muted"]}">{m}{" m" if m == 6 else ""}</text>')

    def barra(i, titulo_f, a, b, color, op, ta, tb, centro=None):
        y = top + i * fila
        s.append(f'<text x="{L}" y="{y + 26}" font-size="32" font-weight="700" fill="{C["ink"]}">{esc(titulo_f)}</text>')
        s.append(f'<rect x="{X(a):.1f}" y="{y + 40}" width="{X(b) - X(a):.1f}" height="34" rx="4" fill="{color}" fill-opacity="{op}"/>')
        s.append(f'<text x="{X(a):.1f}" y="{y + 106}" font-size="30" text-anchor="middle" fill="{C["moss"]}">{esc(ta)}</text>')
        s.append(f'<text x="{min(X(b), xa - 8):.1f}" y="{y + 106}" font-size="30" text-anchor="{"end" if X(b) > xa - 60 else "middle"}" fill="{C["moss"]}">{esc(tb)}</text>')
        if centro is not None:
            s.append(f'<line x1="{X(centro):.1f}" x2="{X(centro):.1f}" y1="{y + 34}" y2="{y + 80}" stroke="{C["forest"]}" stroke-width="6"/>')
    barra(0, f"Mínimo y máximo de los {n} años", d["min"], d["max"], C["moss"], 0.16, f"{fm(d['min'])} ({d['anio_min']})", f"{fm(d['max'])} ({d['anio_max']})")
    barra(1, "Percentiles 10 a 90: ocho de cada diez años", d["p10"], d["p90"], C["moss"], 0.34, fm(d["p10"]), fm(d["p90"]))
    y = top + 2 * fila
    barra(2, "Percentiles 25 a 75: la mitad de los años", d["p25"], d["p75"], C["moss"], 0.62, fm(d["p25"]), fm(d["p75"]), d["mediana"])
    # median label sits under the bar, between the two end labels
    s.append(f'<text x="{X(d["mediana"]):.1f}" y="{y + 106}" font-size="30" font-weight="700" text-anchor="middle" fill="{C["forest"]}">mediana {fm(d["mediana"])}</text>')
    # row 4: the same day in the two extreme years
    y = top + 3 * fila
    ab, aa = g["anio_bajo"], g["anio_alto"]
    s.append(f'<text x="{L}" y="{y + 26}" font-size="32" font-weight="700" fill="{C["ink"]}">El mismo día en dos años extremos</text>')
    for anio, val, etiqueta, forma in ((ab["anio"], ab["mismo_dia"], f"{ab['anio']}: {fm(ab['mismo_dia'])}", "cuadro"), (aa["anio"], aa["mismo_dia"], f"{aa['anio']}: {fm(aa['mismo_dia'])}", "rombo")):
        cx, cy = X(val), y + 57
        if forma == "cuadro":
            s.append(f'<rect x="{cx - 14:.1f}" y="{cy - 14:.1f}" width="28" height="28" fill="{C["olive"]}"/>')
        else:
            s.append(f'<path d="M{cx:.1f} {cy - 19:.1f} L{cx + 19:.1f} {cy:.1f} L{cx:.1f} {cy + 19:.1f} L{cx - 19:.1f} {cy:.1f}Z" fill="{C["muted"]}"/>')
        s.append(f'<text x="{cx:.1f}" y="{y + 106}" font-size="30" text-anchor="middle" fill="{C["moss"]}">{esc(etiqueta)}</text>')
    # 2026
    s.append(f'<line x1="{xa:.1f}" x2="{xa:.1f}" y1="{top - 14}" y2="{ejes_y}" stroke="{C["forest"]}" stroke-width="6"/>')
    s.append(f'<circle cx="{xa:.1f}" cy="{top - 14}" r="13" fill="{C["lime"]}" stroke="{C["forest"]}" stroke-width="5"/>')
    s.append(f'<text x="{xa - 24:.1f}" y="{top - 4}" font-size="34" font-weight="700" text-anchor="end" fill="{C["forest"]}">{g["anio"]}: {fm(g["valor"])} m</text>')
    pie(s, [(f"Lectura del {fecha}. Años de comparación: {r0} a {r1}.", C["muted"]),
            (f"Años de menor y mayor altura media: {ab['anio']} y {aa['anio']}.", C["muted"]),
            (FUENTE, C["moss"]), (AUTORIA, C["moss"])], ejes_y + 76)
    s.append("</svg>")
    return "\n".join(s) + "\n"


# ------------------------------------------------------------------ 3. the crest travels downriver
def grafico_crecida(est):
    orden = ["paso-de-la-patria", "corrientes", "goya", "esquina", "la-paz", "santa-fe"]
    filas = [(est[k]["nombre"], est[k]["cambio_30d"], k == "goya") for k in orden]
    fecha = RH.fecha_larga(est["goya"]["fecha"])
    titulo = "La crecida viaja río abajo"
    sub = "Cambio de la altura del Paraná en 30 días"
    linea = f"Al {fecha}"
    L = 60
    cero = 500
    X = lambda m: cero + m * 200  # noqa: E731
    desc = ("Cambio de la altura del río Paraná en los últimos 30 días, de norte a sur: " + "; ".join(f"{n} {fs(v)} m ({'baja' if v < 0 else 'sube'})" for n, v, _ in filas)
            + f". Al {fecha}. Río arriba baja; río abajo sube. Cada escala tiene su propio cero.")
    s = abrir(titulo, desc) + cabecera(titulo, sub, linea)
    top, fila = 284, 76
    s.append('<defs><pattern id="r" width="14" height="14" patternUnits="userSpaceOnUse" patternTransform="rotate(45)">'
             f'<rect width="14" height="14" fill="{C["paper"]}"/><rect width="7" height="14" fill="{C["olive"]}"/></pattern></defs>')
    s.append(f'<text x="{L}" y="{top - 26}" font-size="30" fill="{C["muted"]}">↑ río arriba (norte)</text>')
    for i, (nombre, v, es_goya) in enumerate(filas):
        y = top + i * fila
        if es_goya:
            s.append(f'<rect x="20" y="{y - 6}" width="{W - 40}" height="{fila - 4}" rx="10" fill="{C["cream"]}"/>')
        s.append(f'<text x="{L}" y="{y + 28}" font-size="34" font-weight="{800 if es_goya else 500}" fill="{C["ink"]}">{esc(nombre)}</text>')
        x_a, x_b = (X(v), cero) if v < 0 else (cero, X(v))
        relleno = "url(#r)" if v < 0 else (C["lime"] if es_goya else C["moss"])
        s.append(f'<rect x="{x_a:.1f}" y="{y + 36}" width="{x_b - x_a:.1f}" height="30" fill="{relleno}" stroke="{C["forest"]}" stroke-width="{4 if es_goya else 2.5}"/>')
        txt = f"{fs(v)} m · {'baja' if v < 0 else 'sube'}"
        if v < 0:  # negative: the label goes on the empty side of the axis
            s.append(f'<text x="{cero + 14}" y="{y + 62}" font-size="32" font-weight="700" fill="{C["forest"]}">{esc(txt)}</text>')
        else:
            s.append(f'<text x="{x_b + 12:.1f}" y="{y + 62}" font-size="32" font-weight="700" fill="{C["forest"]}">{esc(txt)}</text>')
    s.append(f'<line x1="{cero:.1f}" x2="{cero:.1f}" y1="{top - 12}" y2="{top + 6 * fila - 8}" stroke="{C["forest"]}" stroke-width="3"/>')
    s.append(f'<text x="{L}" y="{top + 6 * fila + 22}" font-size="30" fill="{C["muted"]}">↓ río abajo (sur)</text>')
    s.append(f'<text x="{cero + 14}" y="{top + 6 * fila + 22}" font-size="30" fill="{C["muted"]}">0 = sin cambio</text>')
    pie(s, [("Cada escala tiene su propio cero: se comparan los cambios, no las alturas.", C["muted"]),
            (FUENTE, C["moss"]), (AUTORIA, C["moss"])], top + 6 * fila + 70)
    s.append("</svg>")
    return "\n".join(s) + "\n"


def main(entrada, salida):
    est, filas = leer(entrada)
    g = est["goya"]
    os.makedirs(salida, exist_ok=True)
    for nombre, svg in (("1-goya-ano.svg", grafico_ano(g, filas)), ("2-goya-20-de-noviembre.svg", grafico_dia(g)), ("3-crecida-rio-abajo.svg", grafico_crecida(est))):
        with open(os.path.join(salida, nombre), "w", encoding="utf-8") as f:
            f.write(svg)
        print("escrito", nombre, len(svg), "bytes")


if __name__ == "__main__":
    ent = sys.argv[1] if len(sys.argv) > 1 else os.path.join(AQUI, "rio-nota-goya")
    main(ent, sys.argv[2] if len(sys.argv) > 2 else ent)
