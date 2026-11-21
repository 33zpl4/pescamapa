#!/usr/bin/env python3
"""Three infographics for the replacement note of 21/11/2026 («Goya nunca tuvo un noviembre así»), standard library only.

  python3 -I wordpress/build/rio-nota-noviembre-graficos.py [entrada] [salida]

entrada (default wordpress/build/rio-nota-noviembre): the files rio-historia.py wrote on the branch `rio-historia-desde-1903`
on 20/11/2026 (resumen.json, datos-goya.csv, datos-goya-mensual.csv and the daily files of the other four stations) plus
pronostico-ina-2026-11-20.json (the INA numeric forecast, typed once from the draft with its source). salida (default the
same folder): 1-nunca-en-noviembre.svg, 2-el-rio-dia-por-dia.svg, 3-la-misma-agua-rio-abajo.svg. Every number comes from
those files; the script asserts the figures the note quotes (34 and 22 days, 6,10 m, 5,80 m, the five peaks) and stops if
one differs. 1200 x 900 (4:3, the crop of the note's cover); PNGs: node wordpress/build/rio-nota-noviembre-png.mjs.
"""
import csv
import datetime as dt
import importlib.util
import json
import os
import sys

AQUI = os.path.dirname(os.path.abspath(__file__))
spec = importlib.util.spec_from_file_location("rio_historia", os.path.join(AQUI, "rio-historia.py"))
RH = importlib.util.module_from_spec(spec)
spec.loader.exec_module(RH)
C, F_TXT, F_TIT, F_MARCA = RH.C, RH.F_TXT, RH.F_TIT, RH.F_MARCA
fm, esc = RH.fm, RH.escape
W, H = 1200, 900
FUENTE = "Datos: INA (escalas de Prefectura Naval Argentina) · Elaboración: Pescamapa"
LECTURA = "Lecturas hasta el 20 de noviembre de 2026."
MESES3 = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"]


def d(s):
    return dt.date.fromisoformat(s)


def dm(s):
    return f"{int(s[8:10])}/{int(s[5:7])}"


def leer(entrada):
    res = json.load(open(os.path.join(entrada, "resumen.json"), encoding="utf-8"))["estaciones"]
    mens = list(csv.DictReader(open(os.path.join(entrada, "datos-goya-mensual.csv"), encoding="utf-8")))
    pron = json.load(open(os.path.join(entrada, "pronostico-ina-2026-11-20.json"), encoding="utf-8"))
    diarios = {}
    for e in ("goya", "corrientes", "esquina", "la-paz", "santa-fe"):
        filas = list(csv.DictReader(open(os.path.join(entrada, f"datos-{e}.csv"), encoding="utf-8")))
        diarios[e] = {f"2026-{f['mes_dia']}": float(f["2026_m"]) for f in filas if f.get("2026_m", "") != ""}
    return res, mens, pron, diarios


def abrir(titulo, desc):
    return [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-labelledby="t d" font-family="{F_TXT}">',
            f'<title id="t">{esc(titulo)}</title>', f'<desc id="d">{esc(desc)}</desc>',
            f'<rect width="{W}" height="{H}" fill="{C["paper"]}"/>']


def cabecera(s, titulo, sub):
    s.append(f'<text x="40" y="56" font-family="{F_MARCA}" font-size="34" letter-spacing="2" fill="{C["olive"]}">PESCAMAPA</text>')
    s.append(f'<text x="40" y="116" font-family="{F_TIT}" font-size="50" font-weight="700" fill="{C["ink"]}">{esc(titulo)}</text>')
    s.append(f'<text x="40" y="160" font-size="32" fill="{C["moss"]}">{esc(sub)}</text>')


def pie(s, lineas, y0, paso=34):
    for n, (txt, color) in enumerate(lineas):
        s.append(f'<text x="40" y="{y0 + n * paso}" font-size="28" fill="{color}">{esc(txt)}</text>')


def texto(s, x, y, txt, tam=30, color=None, peso=400, ancla="start", halo=True, extra=""):
    """Text with a paper halo so a stem or a line behind it never cuts the letters."""
    color = color or C["ink"]
    h = f' stroke="{C["paper"]}" stroke-width="7" stroke-linejoin="round" paint-order="stroke"' if halo else ""
    s.append(f'<text x="{x:.1f}" y="{y:.1f}" font-size="{tam}" font-weight="{peso}" text-anchor="{ancla}" fill="{color}"{h}{extra}>{esc(txt)}</text>')


# ------------------------------------------------------------------ 1. every November since 1903
def grafico_noviembres(res, mens):
    g = res["goya"]
    niv = g["niveles"]
    mes = g["mes_en_curso"]
    maxes, incompleto = {}, set()
    for f in mens:
        a = int(f["anio"])
        if f["max_nov_m"] != "":
            maxes[a] = float(f["max_nov_m"])
            if f["dias_nov"] != "" and int(f["dias_nov"]) < 27 and a != 2026:
                incompleto.add(a)
    a0, a1 = min(maxes), max(maxes)
    assert (a0, a1) == (1903, 2026) and len(maxes) == 124, (a0, a1, len(maxes))
    ant = sorted(((v, a) for a, v in maxes.items() if a != 2026), reverse=True)
    assert maxes[2026] == 6.10 and ant[0] == (5.80, 1998), ant[:3]
    assert maxes[2026] == mes["max"] and mes["fecha_max"] == "2026-11-05"
    assert mes["rango_meses_completos"]["puesto_max"] == 1 and mes["rango_ventana"]["puesto_max"] == 1
    L, R, T, B = 96, 1140, 296, 736
    ymax = 6.6
    X = lambda a: L + (a - a0) / (a1 - a0) * (R - L)  # noqa: E731
    Y = lambda m: B - m / ymax * (B - T)  # noqa: E731
    titulo = "Goya: el noviembre más alto en 124 años"
    sub = "Máxima de cada mes de noviembre, 1903–2026"
    desc = (f"Máxima de la altura del río Paraná en Goya en cada noviembre de 1903 a 2026, una varilla por año. 2026 marca {fm(maxes[2026])} m "
            f"(el 5 de noviembre), por encima de todos los demás; el máximo anterior para el mes es {fm(ant[0][0])} m, en {ant[0][1]}. "
            f"Dos líneas horizontales marcan el nivel de alerta ({fm(niv['alerta'])} m) y el de evacuación ({fm(niv['evacuacion'])} m), referencias de la escala. "
            f"Solo 1998 y 2026 pasaron el nivel de evacuación en un noviembre. Noviembre de 2026 está incompleto: lecturas hasta el 20.")
    s = abrir(titulo, desc)
    cabecera(s, titulo, sub)
    s.append(f'<text x="40" y="206" font-size="38" font-weight="700" fill="{C["forest"]}">2026: {fm(maxes[2026])} m · el anterior mayor, {fm(ant[0][0])} m (1998)</text>')
    for m in range(0, 7):
        y = Y(m)
        s.append(f'<line x1="{L}" x2="{R}" y1="{y:.1f}" y2="{y:.1f}" stroke="{C["line"]}" stroke-width="2"/>')
        s.append(f'<text x="{L - 12}" y="{y + 10:.1f}" font-size="28" text-anchor="end" fill="{C["muted"]}">{m}</text>')
    s.append(f'<text x="{L - 12}" y="{T - 22}" font-size="28" text-anchor="end" fill="{C["muted"]}">m</text>')
    for a in (1920, 1940, 1960, 1980, 2000, 2020):
        s.append(f'<line x1="{X(a):.1f}" x2="{X(a):.1f}" y1="{B}" y2="{B + 8}" stroke="{C["muted"]}" stroke-width="2"/>')
        s.append(f'<text x="{X(a):.1f}" y="{B + 40}" font-size="30" text-anchor="middle" fill="{C["muted"]}">{a}</text>')
    s.append(f'<line x1="{L}" x2="{R}" y1="{B}" y2="{B}" stroke="{C["muted"]}" stroke-width="2.5"/>')
    # thresholds (behind the stems); the labels sit on a paper chip so the stems never cut the letters
    for clave, color in (("alerta", C["olive"]), ("evacuacion", C["forest"])):
        y = Y(niv[clave])
        s.append(f'<line x1="{L}" x2="{R}" y1="{y:.1f}" y2="{y:.1f}" stroke="{color}" stroke-width="3.5" stroke-dasharray="16 9"/>')
    ye, ya = Y(niv["evacuacion"]), Y(niv["alerta"])
    s.append(f'<rect x="{L + 4}" y="{ye - 78:.1f}" width="404" height="70" rx="8" fill="{C["paper"]}" fill-opacity="0.94"/>')
    texto(s, L + 14, ye - 48, "nivel de evacuación", 30, C["forest"], 700, halo=False)
    texto(s, L + 14, ye - 16, f"(referencia de la escala) {fm(niv['evacuacion'])} m", 30, C["forest"], 400, halo=False)
    texto(s, X(1936) + 4, ya - 10, f"nivel de alerta {fm(niv['alerta'])} m", 30, C["olive"], 700)
    # one stem per year
    for a in sorted(maxes):
        if a == 2026:
            continue
        v, x = maxes[a], X(a)
        hueco = a in incompleto
        s.append(f'<line x1="{x:.1f}" x2="{x:.1f}" y1="{B}" y2="{Y(v):.1f}" stroke="{C["moss"]}" stroke-width="3" stroke-opacity="{0.45 if hueco else 0.8}"/>')
        if hueco:
            s.append(f'<circle cx="{x:.1f}" cy="{Y(v):.1f}" r="4.5" fill="{C["paper"]}" stroke="{C["moss"]}" stroke-width="2.5"/>')
        else:
            s.append(f'<circle cx="{x:.1f}" cy="{Y(v):.1f}" r="4.5" fill="{C["moss"]}"/>')
    # 1998 and 2026
    x98, y98 = X(1998), Y(maxes[1998])
    s.append(f'<circle cx="{x98:.1f}" cy="{y98:.1f}" r="8" fill="{C["paper"]}" stroke="{C["forest"]}" stroke-width="4"/>')
    texto(s, x98 - 6, y98 - 24, f"1998: {fm(maxes[1998])} m", 32, C["forest"], 700, "end")
    x26, y26 = X(2026), Y(maxes[2026])
    s.append(f'<line x1="{x26:.1f}" x2="{x26:.1f}" y1="{B}" y2="{y26:.1f}" stroke="{C["forest"]}" stroke-width="6"/>')
    s.append(f'<circle cx="{x26:.1f}" cy="{y26:.1f}" r="15" fill="{C["lime"]}" stroke="{C["forest"]}" stroke-width="6"/>')
    texto(s, x26 + 34, y26 - 34, f"2026: {fm(maxes[2026])} m", 40, C["forest"], 700, "end")
    texto(s, x26 - 28, y26 + 10, "(hasta el 20/11)", 28, C["forest"], 400, "end")
    pie(s, [(f"{LECTURA} Punto hueco: noviembre con datos incompletos.", C["muted"]),
            (FUENTE, C["moss"])], B + 92)
    s.append("</svg>")
    return "\n".join(s) + "\n"


# ------------------------------------------------------------------ 2. the river, day by day
def grafico_dias(res, diarios, pron):
    g = res["goya"]
    niv = g["niveles"]
    serie = {f: v for f, v in diarios["goya"].items() if "2026-09-01" <= f <= "2026-11-20"}
    assert len(serie) == 81 and min(serie.values()) >= 2.5, len(serie)
    al, ev = niv["alerta"], niv["evacuacion"]
    dias_al = sum(1 for v in diarios["goya"].values() if v >= al)
    assert g["umbrales"]["alerta"]["dias_anio"] == dias_al == 34, dias_al
    dias_ev = sum(1 for v in diarios["goya"].values() if v >= ev)
    assert g["umbrales"]["evacuacion"]["dias_anio"] == dias_ev == 22, dias_ev
    fechas = sorted(serie)
    cruza_al = next(f for f in fechas if serie[f] >= al)
    cruza_ev = next(f for f in fechas if serie[f] >= ev)
    assert (cruza_al, cruza_ev) == ("2026-10-18", "2026-10-29"), (cruza_al, cruza_ev)
    pico = max(serie.values())
    f_pico = [f for f in fechas if serie[f] == pico]
    assert pico == 6.10 and (f_pico[0], f_pico[-1]) == ("2026-11-03", "2026-11-05"), f_pico
    assert serie["2026-11-20"] == 5.68 and serie["2026-09-04"] == 2.57
    pg = {p["fecha"]: p for p in pron["goya"]}
    assert pg["2026-11-24"]["medio"] == 5.39 and pg["2026-12-01"]["medio"] == 5.45 and pron["emitido"] == "2026-11-20"
    L, R, T, B = 96, 1150, 332, 700
    ymin, ymax = 2.0, 7.6
    f0, f1 = d("2026-09-01"), d("2026-12-01")
    X = lambda f: L + (d(f) - f0).days / (f1 - f0).days * (R - L) if isinstance(f, str) else L + (f - f0).days / (f1 - f0).days * (R - L)  # noqa: E731
    Y = lambda m: B - (m - ymin) / (ymax - ymin) * (B - T)  # noqa: E731
    titulo = "Goya: 34 días sobre el nivel de alerta"
    sub = "Altura diaria del Paraná, del 1 de septiembre al 20 de noviembre de 2026"
    desc = (f"Altura diaria del río Paraná en Goya del 1 de septiembre al 20 de noviembre de 2026: sube de {fm(serie['2026-09-04'])} m el 4 de septiembre, "
            f"cruza el nivel de alerta ({fm(al)} m) el 18 de octubre, el de evacuación ({fm(ev)} m) el 29 de octubre, marca {fm(pico)} m del 3 al 5 de noviembre "
            f"y baja a {fm(serie['2026-11-20'])} m el 20 de noviembre: {dias_al} días sobre el alerta y {dias_ev} sobre el de evacuación. "
            f"Aparte, el pronóstico del INA emitido el 20 de noviembre: {fm(pg['2026-11-24']['medio'])} m el 24/11 y {fm(pg['2026-12-01']['medio'])} m el 1/12, "
            f"con rango probable de {fm(pg['2026-11-24']['inferior'])} a {fm(pg['2026-11-24']['superior'])} m.")
    s = abrir(titulo, desc)
    cabecera(s, titulo, sub)
    # counters
    for x0, num, l1, l2, color in ((40, dias_al, "días seguidos sobre el", f"nivel de alerta ({fm(al)} m)", C["lime"]), (620, dias_ev, "días seguidos sobre el", f"nivel de evacuación ({fm(ev)} m)", C["olive"])):
        s.append(f'<rect x="{x0}" y="196" width="26" height="82" rx="6" fill="{color}"/>')
        s.append(f'<text x="{x0 + 40}" y="262" font-family="{F_TIT}" font-size="74" font-weight="700" fill="{C["forest"]}">{num}</text>')
        s.append(f'<text x="{x0 + 148}" y="228" font-size="30" fill="{C["moss"]}">{esc(l1)}</text>')
        s.append(f'<text x="{x0 + 148}" y="262" font-size="30" font-weight="700" fill="{C["forest"]}">{esc(l2)}</text>')
    # grid and months
    for m in (3, 4, 5, 6, 7):
        y = Y(m)
        s.append(f'<line x1="{L}" x2="{R}" y1="{y:.1f}" y2="{y:.1f}" stroke="{C["line"]}" stroke-width="2"/>')
        s.append(f'<text x="{L - 12}" y="{y + 10:.1f}" font-size="28" text-anchor="end" fill="{C["muted"]}">{m}</text>')
    s.append(f'<text x="{L - 12}" y="{T - 22}" font-size="28" text-anchor="end" fill="{C["muted"]}">m</text>')
    for mes in (9, 10, 11, 12):
        f = f"2026-{mes:02d}-01"
        s.append(f'<line x1="{X(f):.1f}" x2="{X(f):.1f}" y1="{B}" y2="{B + 9}" stroke="{C["muted"]}" stroke-width="2"/>')
        s.append(f'<text x="{X(f) + (-8 if mes == 12 else 8):.1f}" y="{B + 40}" font-size="30" text-anchor="{"end" if mes == 12 else "start"}" fill="{C["muted"]}">{MESES3[mes - 1]}</text>')
    s.append(f'<line x1="{L}" x2="{R}" y1="{B}" y2="{B}" stroke="{C["muted"]}" stroke-width="2.5"/>')
    # forecast zone (a different thing: a band, hollow points, dashed range)
    xz = X("2026-11-20")
    s.append(f'<rect x="{xz:.1f}" y="{T}" width="{R - xz:.1f}" height="{B - T}" fill="{C["cream"]}"/>')
    s.append(f'<line x1="{xz:.1f}" x2="{xz:.1f}" y1="{T}" y2="{B}" stroke="{C["muted"]}" stroke-width="2" stroke-dasharray="6 7"/>')
    # shading above the thresholds
    def area(nivel, color, op):
        pts, ant = [], None
        trozos, cur = [], []
        for f in fechas:
            v = serie[f]
            if v > nivel:
                cur.append((X(f), Y(v)))
            elif cur:
                trozos.append(cur)
                cur = []
        if cur:
            trozos.append(cur)
        for t in trozos:
            a = f"M{t[0][0]:.1f} {Y(nivel):.1f}" + "".join(f"L{x:.1f} {y:.1f}" for x, y in t) + f"L{t[-1][0]:.1f} {Y(nivel):.1f}Z"
            s.append(f'<path d="{a}" fill="{color}" fill-opacity="{op}"/>')
    area(al, C["lime"], 0.7)
    area(ev, C["olive"], 0.55)
    for nivel, color, txt in ((al, C["olive"], f"nivel de alerta {fm(al)} m"), (ev, C["forest"], f"nivel de evacuación {fm(ev)} m")):
        y = Y(nivel)
        s.append(f'<line x1="{L}" x2="{R}" y1="{y:.1f}" y2="{y:.1f}" stroke="{color}" stroke-width="3.5" stroke-dasharray="16 9"/>')
        texto(s, L + 12, y - 12, txt, 28, color, 700)
    # the observed curve
    ruta = "".join(("L" if i else "M") + f"{X(f):.1f} {Y(serie[f]):.1f}" for i, f in enumerate(fechas))
    s.append(f'<path d="{ruta}" fill="none" stroke="{C["forest"]}" stroke-width="6.5" stroke-linejoin="round" stroke-linecap="round"/>')
    # annotations
    def punto(f, v, relleno=C["lime"]):
        s.append(f'<circle cx="{X(f):.1f}" cy="{Y(v):.1f}" r="10" fill="{relleno}" stroke="{C["forest"]}" stroke-width="5"/>')

    def guia(x, y_txt, y_pto):
        s.append(f'<line x1="{x:.1f}" x2="{x:.1f}" y1="{y_txt + 8:.1f}" y2="{y_pto:.1f}" stroke="{C["forest"]}" stroke-width="2.5"/>')

    xa, xe, xp = X(cruza_al), X(cruza_ev), X(f_pico[len(f_pico) // 2])
    ya, ye = Y(6.55), Y(7.22)
    guia(xa, ya, Y(serie[cruza_al]))
    texto(s, xa + 6, ya, f"{dm(cruza_al)}: cruza el alerta", 30, C["forest"], 700, "end")
    guia(xe, ye, Y(serie[cruza_ev]))
    texto(s, xe + 6, ye, f"{dm(cruza_ev)}: pasa el nivel de evacuación", 30, C["forest"], 700, "end")
    punto(cruza_al, serie[cruza_al])
    punto(cruza_ev, serie[cruza_ev])
    yp = Y(6.55)
    guia(xp, yp, Y(pico))
    texto(s, 778, yp, f"{int(f_pico[0][8:10])}–{dm(f_pico[-1])}: pico {fm(pico)} m", 30, C["forest"], 700, "start")
    s.append(f'<circle cx="{xp:.1f}" cy="{Y(pico):.1f}" r="13" fill="{C["lime"]}" stroke="{C["forest"]}" stroke-width="6"/>')
    x20, y20 = X("2026-11-20"), Y(serie["2026-11-20"])
    s.append(f'<circle cx="{x20:.1f}" cy="{y20:.1f}" r="11" fill="{C["forest"]}"/>')
    texto(s, x20 - 16, Y(4.55), "20/11: 5,68 m", 30, C["forest"], 700, "end")
    s.append(f'<line x1="{x20:.1f}" x2="{x20:.1f}" y1="{y20 + 12:.1f}" y2="{Y(4.55) - 28:.1f}" stroke="{C["forest"]}" stroke-width="2.5"/>')
    # forecast: hollow points, dashed range bars
    for f in ("2026-11-24", "2026-12-01"):
        p = pg[f]
        x = X(f)
        s.append(f'<rect x="{x - 13:.1f}" y="{Y(p["superior"]):.1f}" width="26" height="{Y(p["inferior"]) - Y(p["superior"]):.1f}" rx="13" fill="{C["olive"]}" fill-opacity="0.18" stroke="{C["olive"]}" stroke-width="3" stroke-dasharray="7 6"/>')
        s.append(f'<circle cx="{x:.1f}" cy="{Y(p["medio"]):.1f}" r="9" fill="{C["paper"]}" stroke="{C["olive"]}" stroke-width="5"/>')
        texto(s, x, Y(p["inferior"]) + 36, fm(p["medio"]), 30, C["olive"], 700, "middle")
    y_c = Y(3.85)
    texto(s, R, y_c, "Pronóstico del INA", 30, C["olive"], 700, "end")
    texto(s, R, y_c + 34, "emitido el 20/11/2026,", 30, C["olive"], 700, "end")
    texto(s, R, y_c + 68, f"rango probable {fm(pg['2026-11-24']['inferior'])}–{fm(pg['2026-11-24']['superior'])} m", 28, C["olive"], 400, "end")
    pie(s, [(f"{LECTURA} Franja clara: pronóstico del INA, no una lectura.", C["muted"]),
            ("Los niveles de alerta y evacuación son referencias de la escala, no una orden.", C["muted"]),
            (FUENTE, C["moss"])], B + 92)
    s.append("</svg>")
    return "\n".join(s) + "\n"


# ------------------------------------------------------------------ 3. the same water, downriver
def grafico_rio_abajo(res, diarios):
    est = [("corrientes", "Corrientes"), ("goya", "Goya"), ("esquina", "Esquina"), ("la-paz", "La Paz"), ("santa-fe", "Santa Fe")]
    picos = {}
    for e, _ in est:
        sr = {f: v for f, v in diarios[e].items() if f >= "2026-09-01"}
        mx = max(sr.values())
        fs = sorted(f for f, v in sr.items() if v == mx)
        picos[e] = (mx, fs[0], fs[-1])
    esperado = {"corrientes": (7.42, "2026-10-23", "2026-10-23"), "goya": (6.10, "2026-11-03", "2026-11-05"), "esquina": (5.60, "2026-11-06", "2026-11-07"),
                "la-paz": (6.30, "2026-11-14", "2026-11-14"), "santa-fe": (5.88, "2026-11-20", "2026-11-20")}
    assert picos == esperado, picos
    niveles = {e: res[e]["niveles"] for e, _ in est}
    assert all(picos[e][0] > niveles[e]["evacuacion"] for e, _ in est)
    f0, f1 = d("2026-10-20"), d("2026-11-23")
    XL, XR = 300, 1130
    X = lambda f: XL + (d(f) - f0).days / (f1 - f0).days * (XR - XL)  # noqa: E731
    titulo = "La misma agua, río abajo"
    sub = "Cresta de la crecida del Paraná: de Corrientes (23/10) a Santa Fe (20/11)"
    desc = ("Línea de tiempo con el pico de cada escala, de norte a sur: " + "; ".join(
        f"{n} {fm(picos[e][0])} m el {dm(picos[e][1])}" + (f" al {dm(picos[e][2])}" if picos[e][2] != picos[e][1] else "") for e, n in est)
        + ". Las cinco superaron su propio nivel de evacuación; cada escala tiene su propio cero, así que se comparan las fechas, no las alturas. "
        "En Santa Fe el 20 de noviembre es la lectura más alta hasta ahora.")
    s = abrir(titulo, desc)
    cabecera(s, titulo, sub)
    s.append(f'<text x="40" y="206" font-size="34" font-weight="700" fill="{C["forest"]}">Las cinco escalas pasaron su propio nivel de evacuación</text>')
    top, fila = 292, 92
    ybase = top + 4 * fila + 58
    # weekly grid
    for f in ("2026-10-25", "2026-11-01", "2026-11-08", "2026-11-15", "2026-11-22"):
        s.append(f'<line x1="{X(f):.1f}" x2="{X(f):.1f}" y1="{top - 52}" y2="{ybase}" stroke="{C["line"]}" stroke-width="2"/>')
        s.append(f'<text x="{X(f):.1f}" y="{ybase + 34}" font-size="28" text-anchor="middle" fill="{C["muted"]}">{dm(f)}</text>')
    s.append(f'<line x1="{XL}" x2="{XR + 10}" y1="{ybase}" y2="{ybase}" stroke="{C["muted"]}" stroke-width="2.5"/>')
    c0 = d(picos["corrientes"][1])
    prev = None
    for k, (e, nombre) in enumerate(est):
        yc = top + k * fila
        mx, fa, fb = picos[e]
        xa, xb = X(fa), X(fb)
        s.append(f'<line x1="{XL}" x2="{XR}" y1="{yc}" y2="{yc}" stroke="{C["moss"]}" stroke-opacity="0.35" stroke-width="2.5" stroke-dasharray="3 8"/>')
        s.append(f'<text x="40" y="{yc + 2}" font-size="36" font-weight="700" fill="{C["forest"]}">{esc(nombre)}</text>')
        if k:
            dd = (d(fa) - c0).days
            db = (d(fb) - c0).days
            txt = f"+{dd} a {db} días" if db != dd else f"+{dd} días"
            if e == "santa-fe":
                txt = f"+{dd} días y sigue"
            s.append(f'<text x="40" y="{yc + 34}" font-size="28" fill="{C["muted"]}">{esc(txt)}</text>')
        hueco = e == "santa-fe"
        if xb > xa:
            s.append(f'<rect x="{xa - 14:.1f}" y="{yc - 15}" width="{xb - xa + 28:.1f}" height="30" rx="15" fill="{C["lime"]}" stroke="{C["forest"]}" stroke-width="5"/>')
            xm = (xa + xb) / 2
        else:
            fill = C["paper"] if hueco else C["lime"]
            s.append(f'<circle cx="{xa:.1f}" cy="{yc}" r="16" fill="{fill}" stroke="{C["forest"]}" stroke-width="5"/>')
            xm = xa
        fecha_t = dm(fa) if fa == fb else f"{dm(fa)}–{dm(fb)}"
        lado_der = xb + 30 + 395 < 1180
        xt = (xb + 30) if lado_der else (xa - 30)
        anc = "start" if lado_der else "end"
        texto(s, xt, yc - 14, f"{fm(mx)} m · {fecha_t}", 34, C["forest"], 700, anc)
        n = niveles[e]
        texto(s, xt, yc + 34, f"alerta {fm(n['alerta'])} · evacuación {fm(n['evacuacion'])} m", 28, C["muted"], 400, anc)
    pie(s, [("Cada escala tiene su propio cero: se comparan las fechas, no las alturas.", C["ink"]),
            (f"{LECTURA} Santa Fe: la más alta hasta ahora.", C["muted"]),
            (FUENTE, C["moss"])], ybase + 76)
    s.append("</svg>")
    return "\n".join(s) + "\n"


def main(entrada, salida):
    res, mens, pron, diarios = leer(entrada)
    os.makedirs(salida, exist_ok=True)
    for nombre, svg in (("1-nunca-en-noviembre.svg", grafico_noviembres(res, mens)),
                        ("2-el-rio-dia-por-dia.svg", grafico_dias(res, diarios, pron)),
                        ("3-la-misma-agua-rio-abajo.svg", grafico_rio_abajo(res, diarios))):
        with open(os.path.join(salida, nombre), "w", encoding="utf-8") as f:
            f.write(svg)
        print("escrito", nombre, len(svg), "bytes")


if __name__ == "__main__":
    ent = sys.argv[1] if len(sys.argv) > 1 else os.path.join(AQUI, "rio-nota-noviembre")
    main(ent, sys.argv[2] if len(sys.argv) > 2 else ent)
