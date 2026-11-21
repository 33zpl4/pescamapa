#!/usr/bin/env python3
"""Dos infografias de la nota de precios (20/11/2026), 1200 x 900 (4:3). Biblioteca estandar.
  python3 -I wordpress/build/precios-2026-11/graficos.py   -> 1-precio-por-dia.svg, 2-fichas-con-precio.svg (en esta carpeta)
Cada numero sale de precios.json / resumen.json (generar.py). PNG: node wordpress/build/precios-2026-11/precios-png.mjs
"""
import json, os
from xml.sax.saxutils import escape as esc

AQUI = os.path.dirname(os.path.abspath(__file__))
C = {"paper": "#fbfaf6", "ink": "#14251f", "forest": "#11251f", "lime": "#d8e46a", "moss": "#344a3d", "olive": "#5e6b33", "muted": "#5f685e", "line": "#d9ddd2", "cream": "#f4f2e9"}
F_TXT = "Outfit, 'Helvetica Neue', Arial, sans-serif"
F_TIT = "'Playfair Display', Georgia, 'Times New Roman', serif"
F_MARCA = "'Bebas Neue', Impact, 'Arial Narrow', sans-serif"
W, H = 1200, 900
FILAS = json.load(open(os.path.join(AQUI, "precios.json"), encoding="utf-8"))["filas"]
RES = json.load(open(os.path.join(AQUI, "resumen.json"), encoding="utf-8"))
fm = lambda x: f"{int(x + 0.5):,}".replace(",", ".")


def abrir(t, d):
    return [f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {W} {H}" width="{W}" height="{H}" role="img" aria-labelledby="t d" font-family="{F_TXT}">',
            f'<title id="t">{esc(t)}</title>', f'<desc id="d">{esc(d)}</desc>', f'<rect width="{W}" height="{H}" fill="{C["paper"]}"/>']


def cab(t, sub, linea):
    return [f'<text x="40" y="58" font-family="{F_MARCA}" font-size="34" letter-spacing="2" fill="{C["olive"]}">PESCAMAPA</text>',
            f'<text x="40" y="116" font-family="{F_TIT}" font-size="50" font-weight="700" fill="{C["ink"]}">{esc(t)}</text>',
            f'<text x="40" y="160" font-size="32" fill="{C["moss"]}">{esc(sub)}</text>',
            f'<text x="40" y="204" font-size="34" font-weight="700" fill="{C["forest"]}">{esc(linea)}</text>']


def pie(s, lineas, y0):
    for n, txt in enumerate(lineas):
        s.append(f'<text x="40" y="{y0 + n * 36}" font-size="29" fill="{C["muted"] if n < len(lineas) - 1 else C["moss"]}">{esc(txt)}</text>')


def vals(f):
    out = []
    for r in FILAS:
        if r["entra_en_la_comparacion"] and r["usd_por_pescador_por_dia"] is not None and f(r):
            out.append((r["usd_por_pescador_por_dia"], r["usd_por_pescador_por_dia_max"], r["lugar_codigo"]))
    return out


def grafico1():
    lodge_pq = lambda r: r["tipo_oferta"] == "paquete_noches" and r["moneda"] == "USD" and r["lugar_codigo"][:3] in ("COR", "PAN", "PAS")
    filas = [
        ("Día con guía, Bariloche", "dos pescadores en el bote", vals(lambda r: r["tipo_oferta"] == "dia_flotada_mosca"), C["moss"]),
        ("Posada, Esquina", "paquete de 2 a 4 días", vals(lambda r: r["lugar_codigo"] == "ESQ-B"), C["moss"]),
        ("Delta y Santa Fe", "barco-hotel y lodge, «desde»", vals(lambda r: r["lugar_codigo"] in ("DEL-A", "SFE-A")), C["moss"]),
        ("Lodges, paquetes", "de 2 a 5 días de pesca", vals(lodge_pq), C["olive"]),
        ("Semana, Corrientes", "7 noches, 6 días de pesca", vals(lambda r: r["tipo_oferta"] == "semana_lodge" and r["destino_clave"] in ("itati", "esteros-del-ibera", "corrientes")), C["forest"]),
        ("Semana, Patagonia norte", "7 noches, 6 días de pesca", vals(lambda r: r["tipo_oferta"] == "semana_lodge" and r["lugar_codigo"].startswith("PAN")), C["forest"]),
        ("Semana, Patagonia sur", "7 noches, 6 o 6,5 días", vals(lambda r: r["tipo_oferta"] == "semana_lodge" and r["lugar_codigo"].startswith("PAS")), C["forest"]),
    ]
    x0, x1, vmax = 500, 985, 2500
    X = lambda v: x0 + v / vmax * (x1 - x0)
    T, rh = 262, 62
    d = "Puntos: cada precio publicado, en US$ por pescador y por día de pesca. " + "; ".join(f"{a}: {len(v)} precios de {min(p[0] for p in v):.0f} a {max((p[1] or p[0]) for p in v):.0f}" for a, _, v, _ in filas)
    s = abrir("US$ por pescador y por día de pesca", d) + cab("US$ por pescador y por día de pesca", "Precios publicados por cada lugar, por formato", f"Leídos entre el {RES['fechas'][0][8:]}/10 y el {RES['fechas'][1][8:]}/11/2026")
    for v in (0, 500, 1000, 1500, 2000, 2500):
        s.append(f'<line x1="{X(v):.1f}" x2="{X(v):.1f}" y1="{T - 14}" y2="{T + rh * len(filas) - 8}" stroke="{C["line"]}" stroke-width="2"/>')
        s.append(f'<text x="{X(v):.1f}" y="{T + rh * len(filas) + 28}" font-size="28" text-anchor="middle" fill="{C["muted"]}">{fm(v)}</text>')
    for k, (a, b, v, col) in enumerate(filas):
        y = T + k * rh
        if k % 2 == 0:
            s.append(f'<rect x="24" y="{y - 14}" width="{W - 48}" height="{rh - 4}" rx="12" fill="{C["cream"]}" fill-opacity="0.8"/>')
            for g in (0, 500, 1000, 1500, 2000, 2500):
                pass
        s.append(f'<text x="40" y="{y + 18}" font-size="30" font-weight="700" fill="{C["ink"]}">{esc(a)}</text>')
        s.append(f'<text x="40" y="{y + 44}" font-size="26" fill="{C["muted"]}">{esc(b)}</text>')
        cy = y + 20
        lo, hi = min(p[0] for p in v), max((p[1] or p[0]) for p in v)
        s.append(f'<line x1="{X(lo):.1f}" x2="{X(hi):.1f}" y1="{cy}" y2="{cy}" stroke="{col}" stroke-width="4" stroke-opacity="0.35"/>')
        for lo_, hi_, _ in v:
            if hi_:
                s.append(f'<line x1="{X(lo_):.1f}" x2="{X(hi_):.1f}" y1="{cy}" y2="{cy}" stroke="{col}" stroke-width="12" stroke-linecap="round"/>')
            else:
                s.append(f'<circle cx="{X(lo_):.1f}" cy="{cy}" r="9" fill="{col}" fill-opacity="0.8" stroke="{C["paper"]}" stroke-width="2.5"/>')
        s.append(f'<text x="{x1 + 22}" y="{y + 17}" font-size="29" font-weight="700" fill="{C["ink"]}">n = {len(v)}</text>')
        s.append(f'<text x="{x1 + 22}" y="{y + 45}" font-size="26" fill="{C["muted"]}">{fm(lo)}–{fm(hi)}</text>')
    pie(s, ["Cuenta: precio ÷ pescadores ÷ días de pesca publicados (día con guía: dos en el bote).",
            "Barra gruesa: el lugar publica precios por fecha. Escala en US$.",
            "Sin conversión de monedas: los precios en pesos y euros quedan fuera.",
            "Fuente: precios publicados por cada lugar (ver nota). Elaboración: Pescamapa."], T + rh * len(filas) + 68)
    s.append("</svg>")
    return "\n".join(s) + "\n"


def grafico2():
    F = {n: (t, c) for n, t, c in RES["fichas"]["por_destino"]}
    grupos = [("Bariloche", ["Bariloche"]), ("Corrientes", ["Paso de la Patria", "Goya", "Esquina", "Itati", "Ita Ibate", "Esteros del Ibera"]),
              ("Patagonia sur", ["Rio Grande", "Rio Gallegos", "Jurassic Lake", "Rio Santa Cruz"]),
              ("Chubut y Neuquén", ["Esquel y Trevelin", "Junin de los Andes"]), ("Entre Ríos y Buenos Aires", ["La Paz", "Concordia", "Chascomus"])]
    datos = [(n, sum(F[d][0] for d in ds), sum(F[d][1] for d in ds)) for n, ds in grupos]
    datos.sort(key=lambda x: -x[1])
    datos.append(("Todas las fichas", RES["fichas"]["total"], RES["fichas"]["con_precio"]))
    assert sum(d[1] for d in datos[:-1]) == datos[-1][1] and sum(d[2] for d in datos[:-1]) == datos[-1][2]
    x0, x1 = 440, 1020
    mx = max(d[1] for d in datos[:-1])
    T, rh = 262, 80
    sc = lambda n, tot: n / tot * (x1 - x0)
    d = "Barras por zona: fichas con precio fechado y sin precio. " + "; ".join(f"{a}: {c} de {t}" for a, t, c in datos)
    s = abrir("Cuántas fichas publican su precio", d) + cab("Cuántas fichas publican su precio", "Fichas de lugares con precio fechado, por zona", f"{datos[-1][2]} de {datos[-1][1]} fichas, a noviembre de 2026")
    for k, (a, t, c) in enumerate(datos):
        y = T + k * rh + (24 if k == len(datos) - 1 else 0)
        tot = k == len(datos) - 1
        if tot:
            s.append(f'<rect x="24" y="{y - 20}" width="{W - 48}" height="{rh - 4}" rx="12" fill="{C["cream"]}"/>')
        s.append(f'<text x="40" y="{y + 40}" font-size="31" font-weight="700" fill="{C["ink"]}">{esc(a)}</text>')
        wtot = (x1 - x0) * (1 if tot else t / mx)
        wc = wtot * c / t
        s.append(f'<rect x="{x0}" y="{y + 8}" width="{wtot:.1f}" height="46" fill="{C["paper"] if not tot else C["cream"]}" stroke="{C["moss"]}" stroke-width="3" stroke-dasharray="10 6"/>')
        if c:
            s.append(f'<rect x="{x0}" y="{y + 8}" width="{wc:.1f}" height="46" fill="{C["lime"] if tot else C["moss"]}" stroke="{C["forest"]}" stroke-width="3"/>')
        s.append(f'<text x="{x0 + wtot + 14:.1f}" y="{y + 44}" font-size="31" font-weight="700" fill="{C["forest"]}">{c} de {t}</text>')
    s.append(f'<rect x="{x0}" y="{T + 5 * rh + 108}" width="34" height="24" fill="{C["moss"]}" stroke="{C["forest"]}" stroke-width="3"/><text x="{x0 + 46}" y="{T + 5 * rh + 128}" font-size="28" fill="{C["ink"]}">con precio fechado</text>')
    s.append(f'<rect x="{x0 + 330}" y="{T + 5 * rh + 108}" width="34" height="24" fill="none" stroke="{C["moss"]}" stroke-width="3" stroke-dasharray="6 4"/><text x="{x0 + 376}" y="{T + 5 * rh + 128}" font-size="28" fill="{C["ink"]}">sin precio</text>')
    pie(s, ["Recuento de fichas públicas de lugares registradas en el repositorio al 19/11/2026.",
            "«Con precio»: ficha con tarifa y fecha de lectura. No cuenta las notas de precios.",
            "Fuente: fichas de Pescamapa (ver nota). Elaboración: Pescamapa."], 818)
    s.append("</svg>")
    return "\n".join(s) + "\n"


open(os.path.join(AQUI, "1-precio-por-dia.svg"), "w", encoding="utf-8").write(grafico1())
open(os.path.join(AQUI, "2-fichas-con-precio.svg"), "w", encoding="utf-8").write(grafico2())
print("ok")
