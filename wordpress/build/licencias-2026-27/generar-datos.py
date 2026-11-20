#!/usr/bin/env python3
"""Dataset of the note «Licencia de pesca en Argentina 2026-27» (nota-licencias-2026-27-2026-11-20), standard library only.

  python3 -I wordpress/build/licencias-2026-27/generar-datos.py

Writes licencias.json and licencias.csv next to this file. One row per region x category x period. EVERY value below was
copied from a page or script already in this repo that names its official source and the date it was read (the script that
carries each one is in `repo`); nothing is estimated, converted or filled from memory. A missing value is left empty and the
row says so in `observacion`. Prices change every season: the dates are the dates WE read them.
"""
import csv
import json
import os

AQUI = os.path.dirname(os.path.abspath(__file__))

# ---- sources (name, url, official, date read) ----
S = {
    "regl": ("Reglamento General de Pesca Deportiva Continental Patagónico 2026/27: categorías y valores (texto actualizado el 26/10/2026)", "https://reglamentodepesca.org.ar/reglamento/", 1, "2026-11-06"),
    "nqn": ("Dirección Provincial de Fauna de Neuquén: comprar permiso de pesca, temporada 2026-2027", "https://cazaypesca.neuquen.gob.ar/permisos", 1, "2026-11-16"),
    "rn": ("Mesa Directiva de Pesca Deportiva Zona Andina: permisos de pesca de Río Negro (venta en línea)", "https://www.permisosdepesca.com.ar/", 1, "2026-11-16"),
    "rn_lim": ("Mesa Directiva de Pesca Deportiva Zona Andina: permiso para extranjeros no residentes de país limítrofe (E-L10)", "https://www.permisosdepesca.com.ar/ofertas.php", 1, "2026-11-06"),
    "sc": ("Pesca y Acuicultura de Santa Cruz: valores de los permisos 2026/27 (portal oficial)", "https://pesca.santacruz.gob.ar/valores/", 1, "2026-11-04"),
    "tdf": ("Resolución S.A. N.º 259/2026 de la Secretaría de Ambiente de Tierra del Fuego (temporada 2026/27)", "https://prodyambiente.tierradelfuego.gob.ar/wp-content/uploads/2026/10/Resolucion-N259-Temporada-pesca-2026.pdf", 1, "2026-11-04"),
    "ctes": ("Disposición 1144/2024 de la Dirección de Recursos Naturales de Corrientes (27/9/2024), Anexo I: licencias de turista extranjero en litros de nafta súper (copia de AICACYP)", "https://www.aicacyp.ar/wp-content/uploads/2025/02/Disposicion-1144.pdf", 1, "2026-11-06"),
    "er": ("Gobierno de Entre Ríos: trámite 708, «Costos Permisos y Licencias Oct2026» (trámite actualizado el 2/10/2026)", "https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva", 1, "2026-11-18"),
    "ba": ("Ministerio de Desarrollo Agrario de Buenos Aires: obtenga su licencia de pesca deportiva", "https://www.gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva", 1, "2026-10-27"),
    "sf": ("Gobierno de Santa Fe: acuerdo de reconocimiento recíproco de licencias con Entre Ríos, Chaco y Corrientes", "https://www.santafe.gob.ar/noticias/noticia/287531/", 1, "2026-10-20"),
}

PAT = ("Patagonia: permiso unificado", "Neuquén, Río Negro, Chubut y Parques Nacionales")
SC = ("Santa Cruz", "Santa Cruz")
TDF = ("Tierra del Fuego", "Tierra del Fuego")
CT = ("Corrientes", "Corrientes")
ER = ("Entre Ríos", "Entre Ríos")
BA = ("Buenos Aires", "Buenos Aires")
SF = ("Santa Fe", "Santa Fe")
V_PAT = "Temporada de pesca 1/11/2026 al 1/5/2027; el permiso de temporada vale hasta el 31/10/2027"
V_SC = "Temporada 1/10/2026 al 30/9/2027"
V_TDF = "Temporada 30/10/2026 al 2/5/2027"

rows = []


def add(reg, cat, quien, periodo, valor, unidad, vig, src, obs="", texto=""):
    nombre, url, ofi, fecha = S[src]
    rows.append({
        "region": reg[0], "jurisdicciones": reg[1], "categoria": cat, "quien": quien, "periodo": periodo,
        "valor": valor if valor is not None else "", "unidad": unidad, "valor_texto": texto, "vigencia": vig,
        "fuente": nombre, "fuente_url": url, "fuente_oficial": ofi, "consultada": fecha, "observacion": obs,
    })


# Patagonia (all from the Reglamento text read 6/11/2026; the Neuquén and Río Negro portals read 16/11/2026 give the same non-resident values)
for per, v in (("1 día", 50000), ("10 días", 140000), ("temporada", 280000)):
    add(PAT, "Permiso ordinario", "extranjero no residente", per, v, "ARS", V_PAT, "regl", "Mismos valores en los portales de Neuquén y Río Negro (16/11/2026)")
for per, v in (("1 día", 12500), ("10 días", 25000), ("temporada", 50000)):
    add(PAT, "Permiso ordinario", "residente en el país", per, v, "ARS", V_PAT, "regl", "Argentinos y extranjeros residentes")
add(PAT, "Permiso ordinario", "residente de 13 a 17 años", "temporada", 25000, "ARS", V_PAT, "regl")
add(PAT, "Permiso ordinario", "residente (mayores de 65, jubilados, pensionados, menores de hasta 12 años, discapacidad acreditada)", "temporada", 0, "ARS", V_PAT, "regl", "Sin cargo", "sin cargo")
add(("Río Negro", "Río Negro"), "Permiso de país limítrofe (E-L10, categoría provincial)", "extranjero no residente de país limítrofe", "10 días", 50000, "ARS", "Vale solo en Río Negro (el reglamento dice que las categorías fuera del cuadro común valen solo donde se emiten)", "rn_lim", "Chubut lo lista en una publicación de prensa, no en una fuente oficial leída: no se cuenta")
for per, v in (("1 día", 25000), ("10 días", 50000), ("temporada", 100000)):
    add(PAT, "Trolling (permiso adicional al ordinario)", "extranjero no residente y residente (cuadro común)", per, v, "ARS", V_PAT, "regl")

# Santa Cruz (portal read 4/11/2026)
for per, v in (("1 día", 90000), ("7 días", 300000), ("temporada", 450000)):
    add(SC, "Permiso ordinario", "extranjero no residente", per, v, "ARS", V_SC, "sc")
for per, v in (("1 día", 15000), ("fin de semana", 20000), ("7 días", 25000), ("temporada", 40000)):
    add(SC, "Permiso ordinario", "residente en el país", per, v, "ARS", V_SC, "sc")
add(SC, "Permiso ordinario", "residente (mayores de 65, jubilados, pensionados, menores de 12 años, discapacidad)", "temporada", 0, "ARS", V_SC, "sc", "Sin cargo", "sin cargo")
for per, v in (("1 día", 20000), ("7 días", 30000), ("temporada", 40000)):
    add(SC, "Trolling (permiso adicional al ordinario)", "extranjero no residente y residente", per, v, "ARS", V_SC, "sc")

# Tierra del Fuego (Resolución 259/26 read 4/11/2026; page read 16/11/2026)
for per, v in (("1 día", 78000), ("3 días", 117000), ("7 días", 195000), ("temporada", 260000)):
    add(TDF, "Permiso ordinario", "extranjero no residente", per, v, "ARS", V_TDF, "tdf")
for per, v in (("1 día", 26000), ("3 días", 39000), ("7 días", 65000), ("temporada", 90000)):
    add(TDF, "Permiso ordinario", "residente en el país", per, v, "ARS", V_TDF, "tdf")
add(TDF, "Permiso ordinario", "residente de 13 a 17 años", "temporada", 20000, "ARS", V_TDF, "tdf")
add(TDF, "Permiso ordinario", "residente (menores de 12 años, discapacidad, jubilados, pensionados)", "temporada", 0, "ARS", V_TDF, "tdf", "Sin cargo, solo para pesca con devolución", "sin cargo")
adic = (
    ("Zona preferencial ríos Grande y Menéndez (adicional al ordinario)", "extranjero no residente", (59000, 163000, 260000, 390000), ""),
    ("Zona preferencial ríos Grande y Menéndez (adicional al ordinario)", "extranjero de país limítrofe", (65000, 78000, 130000, 175000), "El diario ($ 65.000) figura más caro que el del extranjero general ($ 59.000): es lo que dice la resolución; confirmar con la Secretaría de Ambiente"),
    ("Zona preferencial ríos Ewan Sur, Irigoyen y lago Fagnano (adicional al ordinario)", "extranjero no residente", (43000, 85000, 130000, 247000), ""),
    ("Zona preferencial ríos Ewan Sur, Irigoyen y lago Fagnano (adicional al ordinario)", "extranjero de país limítrofe", (35000, 62000, 98000, 169000), ""),
)
for cat, quien, vals, obs in adic:
    for per, v in zip(("1 día", "3 días", "7 días", "temporada"), vals):
        add(TDF, cat, quien, per, v, "ARS", V_TDF, "tdf", obs)

# Corrientes: litres of super petrol (Disposición 1144/2024); the 2024 annex values in pesos are dated
anexo24 = {1: 18160, 2: 22700, 3: 27240, 4: 31780, 6: 40860}
litros = {1: 16, 2: 20, 3: 24, 4: 28, 6: 36}
for d in (1, 2, 3, 4, 6):
    add(CT, "Licencia de turista extranjero", "extranjero (domicilio fuera de la Argentina)", f"{d} día" + ("s" if d > 1 else ""), litros[d], "litros de nafta súper", "Se actualiza con el precio de la nafta súper al comprar; el portal muestra el importe vigente", "ctes", "El valor en pesos del anexo era $ " + f"{anexo24[d]:,}".replace(",", ".") + " con la nafta a $ 1.135 el litro (octubre de 2024): no es el valor de hoy")
add(CT, "Licencia", "residente de Corrientes, resto del país", "no leído", None, "", "", "ctes", "Hay categorías de costa y embarcada para correntinos, resto del país y extranjeros (portal), pero no leímos sus valores", "no publicado en la fuente leída")

# Entre Ríos (cuadro Oct 2026 read 18/11/2026; same values in the cuadro read 22/10/2026)
add(ER, "Permiso para extranjeros", "extranjero", "5 días", 24251.60, "ARS", "Valores de octubre de 2026", "er")
add(ER, "Permiso transitorio", "residente de otra provincia", "4 días", 5488.52, "ARS", "Valores de octubre de 2026", "er")
add(ER, "Licencia anual", "residente de otra provincia", "anual", 24251.60, "ARS", "Valores de octubre de 2026", "er", "Cuadro leído el 22/10/2026 en la guía de La Paz; mismo importe que el permiso de 5 días para extranjeros, tal como figura")
add(ER, "Licencia anual", "residente de Entre Ríos", "anual", 15444.44, "ARS", "Valores de octubre de 2026", "er", "Cuadro leído el 22/10/2026 en la guía de La Paz")

# Buenos Aires and Santa Fe: no price in the pages read
add(BA, "Licencia de pesca deportiva", "menor de 14 años", "—", 0, "ARS", "", "ba", "Hay que tramitarla igual", "sin cargo")
add(BA, "Licencia de pesca deportiva", "jubilado (con carné)", "sin vencimiento", 0, "ARS", "", "ba", "También quien tenga más de 65 años (varones) o 60 (mujeres) y acredite residencia en la provincia (Resolución 46/05)", "sin cargo")
add(BA, "Licencia de pesca deportiva", "resto (residentes, no residentes, extranjeros)", "no leído", None, "", "", "ba", "El valor no figura en la página ni en el Decreto 3237/95", "no publicado en la fuente leída")
add(SF, "Licencia de pesca deportiva", "—", "no leído", None, "", "", "sf", "Solo leímos el acuerdo de reconocimiento recíproco de licencias (Santa Fe, Entre Ríos, Chaco y Corrientes); sin valores ni categorías", "no publicado en la fuente leída")

for i, r in enumerate(rows, 1):
    r["id"] = i
cols = ["id", "region", "jurisdicciones", "categoria", "quien", "periodo", "valor", "unidad", "valor_texto", "vigencia", "fuente", "fuente_url", "fuente_oficial", "consultada", "observacion"]
with open(os.path.join(AQUI, "licencias.json"), "w", encoding="utf-8") as f:
    json.dump({"titulo": "Licencias de pesca deportiva en Argentina, temporada 2026-27: valores por región", "elaboracion": "Pescamapa", "moneda": "pesos argentinos (ARS) salvo donde la unidad dice litros de nafta súper", "filas": [{c: r[c] for c in cols} for r in rows]}, f, ensure_ascii=False, indent=1)
with open(os.path.join(AQUI, "licencias.csv"), "w", encoding="utf-8", newline="") as f:
    w = csv.writer(f)
    w.writerow(cols)
    for r in rows:
        w.writerow([r[c] for c in cols])
print(len(rows), "filas")
