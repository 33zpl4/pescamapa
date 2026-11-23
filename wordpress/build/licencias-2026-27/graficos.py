#!/usr/bin/env python3
"""Two infographics for the note «Licencia de pesca en Argentina 2026-27» (nota-licencias-2026-27-2026-11-20), standard library only.

  python3 -I wordpress/build/licencias-2026-27/graficos.py

Reads plugins/pescamapa-core/data/licencias.json (generar-datos.py) and writes 1-licencias-precios.svg and 2-licencias-donde-se-saca.svg next to it
(1200 x 900, 4:3, text >= 30 units). Every number is read from the dataset. PNGs: node wordpress/build/licencias-2026-27/png.mjs.
"""
import json
import os
from xml.sax.saxutils import escape as esc

AQUI = os.path.dirname(os.path.abspath(__file__))
C = {"paper": "#fbfaf6", "ink": "#14251f", "forest": "#11251f", "lime": "#d8e46a", "moss": "#344a3d", "olive": "#5e6b33", "muted": "#5f685e", "line": "#d9ddd2", "cream": "#f4f2e9"}
F_TXT = "Outfit, 'Helvetica Neue', Arial, sans-serif"
F_TIT = "'Playfair Display', Georgia, 'Times New Roman', serif"
F_MARCA = "'Bebas Neue', Impact, 'Arial Narrow', sans-serif"
W, H = 1200, 900
D = json.load(open(os.path.join(AQUI, "..", "..", "plugins", "pescamapa-core", "data", "licencias.json"), encoding="utf-8"))["filas"]


def peso(v):
    s = f"{v:,.2f}" if v != int(v) else f"{int(v):,}"
    return "$ " + s.replace(",", "X").replace(".", ",").replace("X", ".")


def fila(region, quien, periodo, cat="Permiso ordinario"):
    for r in D:
        if r["region"] == region and r["quien"] == quien and r["periodo"] == periodo and r["categoria"] == cat:
            return r
    raise KeyError((region, quien, periodo))


def fecha_corta(iso):
    return f"{int(iso[8:10])}/{int(iso[5:7])}"


def abrir(titulo, desc):
    return [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-labelledby="t d" font-family="{F_TXT}">',
            f'<title id="t">{esc(titulo)}</title>', f'<desc id="d">{esc(desc)}</desc>', f'<rect width="{W}" height="{H}" fill="{C["paper"]}"/>']


def cabecera(titulo, sub, linea):
    return [f'<text x="40" y="58" font-family="{F_MARCA}" font-size="34" letter-spacing="2" fill="{C["olive"]}">PESCAMAPA</text>',
            f'<text x="40" y="118" font-family="{F_TIT}" font-size="56" font-weight="700" fill="{C["ink"]}">{esc(titulo)}</text>',
            f'<text x="40" y="164" font-size="34" fill="{C["moss"]}">{esc(sub)}</text>',
            f'<text x="40" y="208" font-size="32" font-weight="700" fill="{C["forest"]}">{esc(linea)}</text>']


def pie(s, lineas, y0):
    for n, txt in enumerate(lineas):
        s.append(f'<text x="40" y="{y0 + n * 36}" font-size="30" fill="{C["moss"]}">{esc(txt)}</text>')


# ---------------------------------------------------------------- 1. non-resident foreigner, one day and season, same unit
def grafico_precios():
    regs = [("Patagonia: permiso unificado", "Patagonia"), ("Tierra del Fuego", "Tierra del Fuego"), ("Santa Cruz", "Santa Cruz")]
    dia = [(n, fila(r, "extranjero no residente", "1 día")) for r, n in regs]
    temp = [(n, fila(r, "extranjero no residente", "temporada")) for r, n in regs]
    er = fila("Entre Ríos", "extranjero", "5 días", "Permiso para extranjeros")
    ct1 = fila("Corrientes", "extranjero (domicilio fuera de la Argentina)", "1 día", "Licencia de turista extranjero")
    ct6 = fila("Corrientes", "extranjero (domicilio fuera de la Argentina)", "6 días", "Licencia de turista extranjero")
    fechas = sorted({r["consultada"] for _, r in dia + temp} | {er["consultada"]})
    titulo = "Cuánto cuesta la licencia de pesca"
    sub = "Permiso para extranjeros no residentes, 2026/27, en pesos"
    linea = f"Leído entre el {fechas[0][8:10].lstrip('0')} y el {fechas[-1][8:10].lstrip('0')} de noviembre de 2026"
    desc = ("Barras con el valor del permiso de un extranjero no residente en pesos argentinos. Un día: " + "; ".join(f"{n} {peso(r['valor'])}" for n, r in dia)
            + ". Temporada: " + "; ".join(f"{n} {peso(r['valor'])}" for n, r in temp)
            + f". Fuera de la comparación: Entre Ríos, {er['periodo']}, {peso(er['valor'])}; Corrientes se fija en litros de nafta súper: {ct1['valor']} litros por un día y {ct6['valor']} por seis.")
    s = abrir(titulo, desc) + cabecera(titulo, sub, linea)
    L, X0, esc_max = 40, 400, 500000
    ancho = 560
    X = lambda v: X0 + v / esc_max * ancho  # noqa: E731
    y = 262
    for nom, lista in (("Un día", dia), ("Temporada", temp)):
        s.append(f'<text x="{L}" y="{y}" font-family="{F_MARCA}" font-size="34" letter-spacing="1.5" fill="{C["olive"]}">{nom.upper()}</text>')
        for i, (n, r) in enumerate(lista):
            yy = y + 18 + i * 56
            s.append(f'<text x="{L}" y="{yy + 33}" font-size="34" font-weight="600" fill="{C["ink"]}">{esc(n)}</text>')
            s.append(f'<rect x="{X0}" y="{yy + 4}" width="{X(r["valor"]) - X0:.1f}" height="40" fill="{C["moss"]}" stroke="{C["forest"]}" stroke-width="2.5"/>')
            s.append(f'<text x="{X(r["valor"]) + 14:.1f}" y="{yy + 36}" font-size="34" font-weight="700" fill="{C["forest"]}">{peso(r["valor"])}</text>')
        y += 18 + 3 * 56 + 44
    s.append(f'<line x1="{X0}" x2="{X0}" y1="{250}" y2="{y - 52}" stroke="{C["forest"]}" stroke-width="3"/>')
    s.append(f'<rect x="20" y="{y - 24}" width="{W - 40}" height="104" rx="10" fill="{C["cream"]}"/>')
    s.append(f'<text x="40" y="{y + 12}" font-size="30" fill="{C["ink"]}"><tspan font-weight="700">Entre Ríos:</tspan> {er["periodo"]}, {peso(er["valor"])} (octubre de 2026)</text>')
    s.append(f'<text x="40" y="{y + 52}" font-size="30" fill="{C["ink"]}"><tspan font-weight="700">Corrientes:</tspan> {ct1["valor"]} litros de nafta súper por un día, {ct6["valor"]} por seis</text>')
    pie(s, ["Entre Ríos (5 días) y Corrientes (litros) quedan fuera de la comparación.",
            "Fuentes: organismos provinciales (ver nota). Elaboración: Pescamapa."], H - 56)
    s.append("</svg>")
    return "\n".join(s) + "\n"


# ---------------------------------------------------------------- 2. where it is issued
LUGARES = [
    ("Patagonia (Neuquén, Río Negro, Chubut y Parques)", "cazaypesca.neuquen.gob.ar y permisosdepesca.com.ar", "16/11"),
    ("Santa Cruz", "pesca.santacruz.gob.ar, en línea", "4/11"),
    ("Tierra del Fuego", "siped.ar y puntos de venta", "4/11"),
    ("Corrientes", "recnaturalesctes.gob.ar, en línea", "6/11"),
    ("Entre Ríos", "portal.entrerios.gov.ar, trámite 708", "18/11"),
    ("Buenos Aires", "gba.gob.ar, Desarrollo Agrario", "27/10"),
]


def grafico_donde():
    titulo = "Dónde se saca la licencia"
    sub = "Cada región tiene la suya: el organismo y su sitio"
    linea = "Páginas leídas entre el 27/10 y el 18/11 de 2026"
    desc = "Seis regiones con su organismo emisor y el sitio oficial donde se tramita: " + "; ".join(f"{a}: {b}" for a, b, _ in LUGARES)
    s = abrir(titulo, desc) + cabecera(titulo, sub, linea)
    top, alto = 246, 88
    for i, (reg, sitio, f) in enumerate(LUGARES):
        y = top + i * alto
        if i % 2 == 0:
            s.append(f'<rect x="20" y="{y - 6}" width="{W - 40}" height="{alto - 6}" rx="10" fill="{C["cream"]}"/>')
        s.append(f'<text x="40" y="{y + 34}" font-size="34" font-weight="700" fill="{C["ink"]}">{esc(reg)}</text>')
        s.append(f'<text x="40" y="{y + 68}" font-size="30" fill="{C["moss"]}">{esc(sitio)}</text>')
        s.append(f'<text x="{W - 40}" y="{y + 68}" font-size="30" text-anchor="end" fill="{C["muted"]}">leído {f}</text>')
    pie(s, ["Fuentes: organismos provinciales (ver nota). Elaboración: Pescamapa."], H - 28)
    s.append("</svg>")
    return "\n".join(s) + "\n"


for nombre, svg in (("1-licencias-precios.svg", grafico_precios()), ("2-licencias-donde-se-saca.svg", grafico_donde())):
    with open(os.path.join(AQUI, nombre), "w", encoding="utf-8") as f:
        f.write(svg)
    print("escrito", nombre, len(svg), "bytes")
