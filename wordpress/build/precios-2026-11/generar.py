#!/usr/bin/env python3
"""Indice de precios de la pesca con guia (nota nota-precios-pesca-guiada-2026-11-20). Biblioteca estandar solamente.

  python3 -I wordpress/build/precios-2026-11/generar.py

Escribe en esta carpeta: precios.json (interno: nombres, fuente exacta), precios.csv (publico: sin nombres ni plataformas),
resumen.json (todas las cifras de la nota). Cada fila es UN precio publicado en nuestro sitio (ficha, paquete o nota de precios)
con fecha de lectura; la fuente de cada una esta en `fuente_interna`. NUNCA se convierte entre monedas. Derivado solo cuando
la cuenta sale directa del texto publicado (pescadores y dias de pesca publicados). No hay tarifas de acuerdos confidenciales.
"""
import csv, json, os, statistics

AQUI = os.path.dirname(os.path.abspath(__file__))
FILAS = []


def R(id, dest, clave, tipo, formato, lugar, cod, monto, moneda, unidad, pesc, den, noches, dias, hab, fecha, fuente, fi,
      nota="", ni="", monto_max=None, comp=True, motivo="", dia_unit=False):
    FILAS.append(dict(id=id, destino=dest, destino_clave=clave, tipo_oferta=tipo, formato=formato, lugar_interno=lugar, lugar_codigo=cod,
                      monto=monto, monto_maximo=monto_max, moneda=moneda, unidad_publicada=unidad, pescadores_cubiertos=pesc, divisor_pescadores=den,
                      noches=noches, dias_de_pesca=dias, habitacion=hab, fecha_de_lectura=fecha, fuente_tipo=fuente, fuente_interna=fi,
                      nota_publica=nota, nota_interna=ni, entra_en_la_comparacion=comp, motivo_si_no=motivo, dia_unit=dia_unit))


P, G, L = "propia", "programa", "listado"
# ---------------------------------------------------------------- Bariloche: dia de pesca con mosca en bote (US$ por bote)
b = ("Bariloche", "bariloche", "dia_flotada_mosca")
R("BAR-01", *b, "Dia completo en el Limay", "Trout Bariloche", "BAR-A", 520, "USD", "por bote", "1-2", 2, 0, 1, "", "2026-10-31", P, "ficha + paquete 11/11; troutbariloche.com leido 31/10", "Licencia aparte.", dia_unit=True)
R("BAR-02", *b, "Dia completo en el Limay", "Southern Rivers Patagonia", "BAR-B", 530, "USD", "en total por 1-2 pescadores", "1-2", 2, 0, 1, "", "2026-10-31", P, "ficha; sitio del lugar leido 31/10", "Licencia y propinas aparte.", dia_unit=True)
R("BAR-03", *b, "Dia completo en el Limay", "Southern Rivers Patagonia", "BAR-B", 660, "USD", "por bote", "1-2", 2, 0, 1, "", "2026-11-11", L, "ficha + paquete; TripAdvisor pegado por el dueno 11/11", "El mismo lugar, otro precio: el de su sitio es BAR-02.", dia_unit=True)
R("BAR-04", *b, "Dia completo", "Patagonia in the Loop", "BAR-C", 550, "USD", "por bote", "1-2", 2, 0, 1, "", "2026-10-31", P, "ficha + paquete; sitio leido 31/10", "Licencia aparte.", dia_unit=True)
R("BAR-05", *b, "Medio dia", "Patagonia in the Loop", "BAR-C", 430, "USD", "por bote", "1-2", 2, 0, 0.5, "", "2026-10-31", P, "ficha; sitio leido 31/10", "Medio dia.", comp=False, motivo="medio dia: duracion distinta", dia_unit=True)
R("BAR-06", *b, "Flotada del Limay, dia completo", "Patagonia Drift Fly Fishing", "BAR-D", 550, "USD", "por bote", "1-2", 2, 0, 1, "", "2026-10-31", P, "ficha + paquete; sitio leido 31/10", "", dia_unit=True)
R("BAR-07", *b, "Lagos del Parque Nacional, dia completo", "Patagonia Drift Fly Fishing", "BAR-D", 580, "USD", "por bote", "1-2", 2, 0, 1, "", "2026-10-31", P, "ficha + paquete; sitio leido 31/10", "Lagos en vez de rio.", dia_unit=True)
R("BAR-08", *b, "Dia completo en el Limay, el Manso o el Pichi Leufu", "Outfitters Patagonia", "BAR-E", 690, "USD", "por grupo de hasta 2", "1-2", 2, 0, 1, "", "2026-11-11", L, "ficha + paquete; TripAdvisor pegado por el dueno 11/11 (sitio no leido)", "Licencia aparte.", dia_unit=True)
R("BAR-09", *b, "Dia completo en el Limay", "Guides Patagonia", "BAR-F", 599.69, "EUR", "por grupo de hasta 2", "1-2", None, 0, 1, "", "2026-11-11", L, "paquete; Viator pegado por el dueno 11/11", "Publicado en euros.", comp=False, motivo="moneda: euros (no se convierte)", dia_unit=True)
R("BAR-10", *b, "Medio dia", "Outfitters Patagonia", "BAR-E", 445.22, "EUR", "por grupo", "1-2", None, 0, 0.5, "", "2026-11-11", L, "paquete; Viator 11/11", "Publicado en euros; medio dia.", comp=False, motivo="moneda: euros y medio dia", dia_unit=True)
R("BAR-11", "Bariloche", "bariloche", "dia_guiado_bote", "Dia completo, 9 horas (reserva para marzo de 2027)", "guia en plataforma de reservas", "BAR-G", 666000, "ARS", "por dia, hasta 2 personas", "hasta 2", 2, 0, 1, "", "2026-11-05", L, "nota precio-pesca-bariloche-limay; pescar.pro 5/11", "Publicado en pesos.", dia_unit=True)
R("BAR-12", *b, "Dia completo, desde", "agencia", "BAR-H", 545, "USD", "desde (unidad no aclarada en lo que leimos)", "sin dato", None, 0, 1, "", "2026-11-05", L, "nota precio-pesca-bariloche-limay; elorigen 5/11", "", comp=False, motivo="el texto leido no aclara por cuantas personas es", dia_unit=True)
for i, (f, m, mm, den) in enumerate([("Dia completo, un pescador", 500, "", 1), ("Dia completo, dos pescadores", 520, "", 2), ("Medio dia", 410, "", 1), ("Dia completo en el Manso", 600, "", 2)], 13):
    R(f"BAR-{i}", *b, f, "operador sin identificar con certeza", "BAR-I", m, "USD", "por bote", "1-2", None, 0, 1, "", "2026-11-05", P, "nota precio-pesca-bariloche-limay; barilochefishingtrips 5/11",
      "", "puede ser Trout Bariloche (ya contado)", comp=False, motivo="no se sabe si es un lugar ya contado: no se cuenta dos veces", dia_unit=True)
# ---------------------------------------------------------------- Litoral y Calafate, dia (pesos)
R("ESQ-01", "Esquina", "esquina", "dia_guiado_bote", "Jornada completa en lancha", "Rebo Pesca", "ESQ-A", 480000, "ARS", "desde, por lancha completa", "no publica", None, 0, 1, "", "2026-11-01", L, "ficha; perfil publico en plataforma 1/11", "No dice cuantos pescadores van.", comp=False, motivo="no publica cuantos pescadores cubre", dia_unit=True)
R("CAL-01", "El Calafate", "el-calafate", "dia_guiado_pescador", "Dia completo (9 horas)", "agencia de viajes", "CAL-A", 720000, "ARS", "por pescador", "1", 1, 0, 1, "", "2026-11-05", L, "nota precio-pesca-patagonia-sur; calafatetravel 5/11", "Publicado en pesos.", dia_unit=True)
R("CAL-02", "El Calafate", "el-calafate", "dia_guiado_pescador", "Medio dia (5 horas)", "agencia de viajes", "CAL-A", 440000, "ARS", "por pescador", "1", 1, 0, 0.5, "", "2026-11-05", L, "nota precio-pesca-patagonia-sur; calafatetravel 5/11", "Publicado en pesos.", comp=False, motivo="medio dia", dia_unit=True)
# ---------------------------------------------------------------- Posada de Esquina, paquetes (US$ por persona, "desde")
for (cod, g, n, d, m, f) in [(2, 2, 3, 2, 705, "2026-11-04"), (2, 2, 4, 3, 1010, "2026-11-04"), (2, 2, 5, 4, 1305, "2026-11-04"), (3, 3, 3, 2, 605, "2026-11-04"), (3, 3, 4, 3, 855, "2026-11-05"), (3, 3, 5, 4, 1105, "2026-11-05")]:
    R(f"ESQ-P{g}-{d}", "Esquina", "esquina", "paquete_noches", f"{d} dias de pesca, {n} noches, lancha de {g}", "Posada Hambare", "ESQ-B", m, "USD", f"desde, por persona (grupo de {g} en la lancha)", "1", 1, n, d, "no aclarada", f, P, "nota viaje-dorado-amigos-precios; pagina de la posada 4 y 5/11",
      "Incluye alojamiento, comidas, lancha, guia y licencia. Al 19/11 la portada de la posada ya no mostraba tarifas.", "ficha #850 sin precio desde 19/11")
# ---------------------------------------------------------------- Corrientes y Santa Fe, lodges y paquetes
def escalera(cod, dest, clave, lugar, pasos, hab, fecha, fuente, fi, nota="", ni=""):
    for (n, d, m) in pasos:
        R(f"{cod}-{n}N", dest, clave, "semana_lodge" if n == 7 else "paquete_noches", f"{n} noches, {d} dias de pesca", lugar, cod, m, "USD", "por persona", "1", 1, n, d, hab, fecha, fuente, fi, nota, ni)
ALT = [(7, 6, 7950), (6, 5, 7595), (5, 4, 6495), (4, 3, 5350), (3, 2, 4050)]
escalera("COR-A", "Itati (Alto Parana, Corrientes)", "itati", "Suinda Lodge", ALT, "doble", "2026-11-13", P, "ficha; Nervous Waters 13/11", "Misma tarifa que otro lodge del mismo grupo (COR-B).")
escalera("COR-B", "Esteros del Ibera (Corrientes)", "esteros-del-ibera", "Pira Lodge", ALT, "doble", "2026-11-13", P, "ficha; Nervous Waters 13/11", "Misma tarifa que COR-A, del mismo grupo.")
R("COR-C-7N", "Esteros del Ibera (Corrientes)", "esteros-del-ibera", "semana_lodge", "7 noches, 6 dias de pesca", "Uguay Lodge", "COR-C", 6400, "USD", "por persona", "1", 1, 7, 6, "individual", "2026-11-13", L, "ficha; pagina de un outfitter (decision a revisar) 13/11", "Habitacion individual; sin ano, sujeta a cambios.")
escalera("COR-D", "Itati (Alto Parana, Corrientes)", "itati", "Itati Lodge", [(7, 6, 7850), (4, 3, 4050)], "doble", "2026-11-13", L, "nota precio-viaje-dorado-lodge; pagina de una agencia 13/11", "Segun una agencia.")
escalera("COR-E", "Alto Parana (Corrientes)", "itati", "lodge no nombrado", [(7, 6, 7000), (6, 5, 6250), (5, 4, 5100), (4, 3, 4250)], "doble", "2026-11-13", L, "nota precio-viaje-dorado-lodge; agencias 2026", "Segun agencias.")
escalera("COR-F", "Parana medio (Corrientes)", "corrientes", "lodge no nombrado", [(7, 6, 7950), (6, 5, 6950), (5, 4, 5950), (4, 3, 4950), (3, 2, 3950)], "no aclarada", "2026-11-13", P, "nota precio-viaje-dorado-lodge; pagina del operador, tarifa 2027", "La pagina dice habitacion individual y, en otro punto, compartida.")
escalera("COR-G", "Esteros del Ibera (Corrientes)", "esteros-del-ibera", "lodge no nombrado", [(7, 6, 7850), (4, 3, 4500)], "doble", "2026-11-13", L, "nota precio-viaje-dorado-lodge; dos agencias", "Segun dos agencias.")
R("SFE-1", "Parana de Santa Fe", "santa-fe", "paquete_noches", "4 dias de pesca (desde)", "Rio Bravo Lodge", "SFE-A", 3600, "USD", "desde, por persona", "1", 1, None, 4, "no aclarada", "2026-11-05", L, "nota precio-viaje-dorado-lodge; argentinafishinglodges 5/11", "Noches no publicadas.")
R("SFE-2", "Parana de Santa Fe", "santa-fe", "paquete_noches", "3 dias y 4 noches (oferta)", "Rio Bravo Lodge", "SFE-A", 2500, "USD", "por persona", "1", 1, 4, None, "no aclarada", "2026-11-05", L, "idem", "Oferta para fechas elegidas.", comp=False, motivo="no aclara cuantos de los 3 dias son de pesca")
R("DEL-3N", "Delta del Parana", "santa-fe", "paquete_noches", "3 noches, 2,5 dias de pesca (barco-hotel)", "barco-hotel (4tourists)", "DEL-A", 1260, "USD", "desde, por persona", "1", 1, 3, 2.5, "no aclarada", "2026-11-05", L, "nota precio-viaje-dorado-lodge; 4tourists 5/11")
R("DEL-4N", "Delta del Parana", "santa-fe", "paquete_noches", "4 noches, 3,5 dias de pesca (barco-hotel)", "barco-hotel (4tourists)", "DEL-A", 1680, "USD", "desde, por persona", "1", 1, 4, 3.5, "no aclarada", "2026-11-05", L, "idem")
for (id, m, den) in [("BRA-1", 1800, 2), ("BRA-2", 1600, 3)]:
    R(id, "Corrientes (capital)", "corrientes", "paquete_noches", f"paquete por dia, {den} pescadores por lancha, minimo 3 dias", "paquete en reales", "BRA-A", m, "BRL", "por pescador y por dia", "1", 1, None, 1, "no aclarada", "2026-11-10", P, "auditoria R2 10/11; Fishing Brazil Adventures, sin fecha de publicacion", "Publicado en reales, sin fecha de publicacion; fecha = lectura.", comp=False, motivo="moneda: reales (no se convierte)")
# ---------------------------------------------------------------- Patagonia norte
escalera("PAN-A", "Neuquen (Junin de los Andes)", "junin-de-los-andes", "Chime Lodge", [(7, 6, 6950), (6, 5, 6350), (5, 4, 5750), (4, 3, 4950), (3, 2, 3950)], "doble", "2026-11-05", P, "nota precio-semana-trucha-patagonia-norte; Nervous Waters 5/11", "Tarifa de diciembre de 2026 a abril de 2027.")
for (cod, lugar, m, zona) in [("PAN-B", "lodge de Cholila", 9200, "Chubut"), ("PAN-C", "lodge de Futaleufu", 8750, "Chubut"), ("PAN-D", "lodge de Trevelin", 6545, "Chubut")]:
    R(f"{cod}-7N", "Chubut", "esquel-trevelin", "semana_lodge", "7 noches, 6 dias de pesca", lugar, cod, m, "USD", "por persona, temporada alta", "1", 1, 7, 6, "doble", "2026-11-05", P, "nota precio-semana-trucha-patagonia-norte; pagina de la cadena 5/11", "Temporada alta.")
R("PAN-E-7N", "Chubut", "esquel-trevelin", "semana_lodge", "7 noches, 6 dias de pesca (semana del 6 al 13 de marzo de 2027)", "Challhuaquen Lodge", "PAN-E", 5800, "USD", "por persona", "1", 1, 7, 6, "no aclarada", "2026-11-05", L, "nota precio-semana-trucha-patagonia-norte; pagina del organizador 5/11", "Incluye licencias y traslados desde Esquel.")
R("PAN-F-7N", "Rio Negro (zona del Manso)", "bariloche", "semana_lodge", "7 noches", "Rio Manso Lodge", "PAN-F", 7000, "USD", "por persona", "1", 1, 7, None, "doble", "2026-11-10", P, "nota precio-semana-trucha-patagonia-norte; pagina del lodge 10/11", "Incluye licencias y traslado desde Bariloche; no publica cuantos dias de pesca.", comp=False, motivo="no publica cuantos dias de pesca tiene la semana")
# ---------------------------------------------------------------- Patagonia sur
for (n, d, m) in [(7, 6.5, 8000), (4, 3.5, 5200), (3, 2.5, 4200)]:
    R(f"PAS-A-{n}N", "Santa Cruz (Jurassic Lake)", "jurassic-lake", "semana_lodge" if n == 7 else "paquete_noches", f"{n} noches, {str(d).replace('.', ',')} dias de pesca", "Jurassic Lake Lodge", "PAS-A", m, "USD", "por pescador", "1", 1, n, d, "no aclarada", "2026-11-10", P, "ficha; pagina de tarifas 10/11", "La pagina no muestra ano ni temporada." + (" De enero a abril." if n < 7 else ""))
R("PAS-B-7N", "Santa Cruz (rio Gallegos)", "rio-gallegos", "semana_lodge", "7 noches, 6 dias de pesca (temporada 2027)", "Las Buitreras", "PAS-B", 9990, "USD", "por pescador", "1", 1, 7, 6, "doble", "2026-11-13", G, "ficha; programa 2027 enviado a Pescamapa 13/11", "Programa 2027 enviado por el lugar.")
for (cod, lugar, ni) in [("PAS-C", "Villa Maria Lodge", "ficha; Nervous Waters 13/11"), ("PAS-D", "Kau Tapen Lodge", "ficha; Nervous Waters 13/11")]:
    R(f"{cod}-7N", "Tierra del Fuego (Rio Grande)", "rio-grande", "semana_lodge", "7 noches, 6,5 dias de pesca (temporada 2027)", lugar, cod, 12950, "USD", "por persona y semana, segun la fecha", "1", 1, 7, 6.5, "single", "2026-11-13", P, ni, "Misma tarifa que otro lodge del mismo grupo; habitacion single." , monto_max=14950)

# ---------------------------------------------------------------- derivados
for r in FILAS:
    r["usd_por_pescador_por_dia"] = r["usd_por_pescador_por_dia_max"] = None
    r["ars_por_pescador_por_dia"] = None
    r["formula"] = ""
    if not r["entra_en_la_comparacion"] or r["dias_de_pesca"] is None:
        continue
    d = r["dias_de_pesca"]
    if r["moneda"] == "USD":
        den = r["divisor_pescadores"] or 1
        r["usd_por_pescador_por_dia"] = round(r["monto"] / den / d, 1)
        if r["monto_maximo"]:
            r["usd_por_pescador_por_dia_max"] = round(r["monto_maximo"] / den / d, 1)
        r["formula"] = f"{r['monto']:g}" + (f" / {den} pescadores" if den > 1 else "") + (f" / {d:g} dias de pesca" if d != 1 else "") + (f" ; max {r['monto_maximo']:g}" if r["monto_maximo"] else "")
    elif r["moneda"] == "ARS":
        den = r["divisor_pescadores"] or 1
        r["ars_por_pescador_por_dia"] = round(r["monto"] / den / d)
        r["formula"] = f"{r['monto']:g}" + (f" / {den} pescadores" if den > 1 else "") + " (pesos, sin convertir)"

# ---------------------------------------------------------------- Excluidos que no son filas (sin fecha o no publicados)
NO_FILAS = [
    "Guia/hotel de Goya: cifras en pesos (por salida de pesca) cargadas a mano, sin fecha de lectura en el repositorio (2 fichas).",
    "Fichas de ejemplo del 20/10/2026 (datos_ejemplo = 1): nunca publicadas.",
    "Paquetes de Bariloche despublicados el 11/11/2026 (precio sin validar por el lugar).",
    "Lugares que piden no publicar su tarifa o con tarifa solo por consulta: sin fila.",
    "Cifras de agencias que no se publican en la ficha (Jurassic Full Week 6.900, etc.): no estan en el sitio.",
]

# ---------------------------------------------------------------- Fichas: cuantas publican precio (recuento a partir de los scripts aplicados, 19/11/2026)
FICHAS = [  # destino, fichas, con_precio_fechado_en_ficha
    ("Paso de la Patria", 9, 0), ("Goya", 4, 0), ("Esquina", 5, 1), ("La Paz", 2, 0), ("Chascomus", 3, 0), ("Junin de los Andes", 2, 0),
    ("Esquel y Trevelin", 4, 0), ("Bariloche", 9, 5), ("Rio Grande", 5, 2), ("Rio Gallegos", 4, 1), ("Jurassic Lake", 3, 1),
    ("Rio Santa Cruz", 1, 0), ("Itati", 2, 1), ("Ita Ibate", 1, 0), ("Esteros del Ibera", 3, 2), ("Concordia", 1, 0)]

# ---------------------------------------------------------------- resumen
def stats(vals):
    v = sorted(vals)
    return dict(n=len(v), min=v[0], max=v[-1], mediana=statistics.median(v))

usables = [r for r in FILAS if r["entra_en_la_comparacion"]]
res = dict(filas=len(FILAS), usables=len(usables), excluidas=len(FILAS) - len(usables),
           excluidas_motivos={r["id"]: r["motivo_si_no"] for r in FILAS if not r["entra_en_la_comparacion"]},
           lugares=len({r["lugar_codigo"] for r in FILAS}), fechas=[min(r["fecha_de_lectura"] for r in FILAS), max(r["fecha_de_lectura"] for r in FILAS)])
res["por_moneda"] = {m: sum(1 for r in FILAS if r["moneda"] == m) for m in ("USD", "ARS", "EUR", "BRL")}
res["por_fuente"] = {k: sum(1 for r in FILAS if r["fuente_tipo"] == k) for k in (P, G, L)}
cod = lambda rows: len({r["lugar_codigo"] for r in rows})
sel = lambda f: [r for r in usables if f(r)]
bar = sel(lambda r: r["tipo_oferta"] == "dia_flotada_mosca" and r["moneda"] == "USD")
res["bariloche_dia"] = dict(**stats([r["monto"] for r in bar]), lugares=cod(bar), pp=stats([r["usd_por_pescador_por_dia"] for r in bar]))
def semana(f):
    rows = sel(lambda r: r["tipo_oferta"] == "semana_lodge" and f(r))
    cab = {}
    for r in rows:  # un precio de cabecera por lugar: el mas bajo publicado
        cab[r["lugar_codigo"]] = min(cab.get(r["lugar_codigo"], 1e9), r["monto"])
    tope = max([r["monto_maximo"] or r["monto"] for r in rows])
    pd = {}
    for r in rows:
        pd.setdefault(r["lugar_codigo"], []).append(r["usd_por_pescador_por_dia"])
    return dict(lugares=len(cab), precios=len(rows), min=min(cab.values()), max=tope, mediana=statistics.median(cab.values()),
                dia_min=min(min(x) for x in pd.values()), dia_max=max(r["usd_por_pescador_por_dia_max"] or r["usd_por_pescador_por_dia"] for r in rows),
                dia_mediana=statistics.median(min(x) for x in pd.values()))
res["semana_corrientes"] = semana(lambda r: r["destino_clave"] in ("itati", "esteros-del-ibera", "corrientes"))
res["semana_patagonia_norte"] = semana(lambda r: r["lugar_codigo"].startswith("PAN"))
res["semana_patagonia_sur"] = semana(lambda r: r["lugar_codigo"].startswith("PAS"))
res["semana_patagonia_norte_con_manso"] = dict(lugares=6, min=5800, max=9200, nota="incluye Rio Manso (7.000, sin dias de pesca publicados)")
pq = sel(lambda r: r["tipo_oferta"] == "paquete_noches" and r["moneda"] == "USD" and r["dias_de_pesca"])
res["paquetes"] = {k: stats([r["usd_por_pescador_por_dia"] for r in pq if r["lugar_codigo"] == k]) for k in sorted({r["lugar_codigo"] for r in pq})}
res["fichas"] = dict(total=sum(f[1] for f in FICHAS), con_precio=sum(f[2] for f in FICHAS), por_destino=FICHAS)
res["incluye_semana"] = dict(fichas=7, alojamiento=7, comida=7, bebidas=7, traslados=5, equipos=4, licencia=3)
res["incluye_dia_bariloche"] = dict(fichas=5, traslados=4, equipo=5, comida=5, bebidas=4, licencia=0)
res["pesos"] = {r["id"]: (r["monto"], r["unidad_publicada"], r["fecha_de_lectura"]) for r in FILAS if r["moneda"] == "ARS"}
res["no_filas"] = NO_FILAS

json.dump(dict(filas=FILAS), open(os.path.join(AQUI, "precios.json"), "w", encoding="utf-8"), ensure_ascii=False, indent=1)
json.dump(res, open(os.path.join(AQUI, "resumen.json"), "w", encoding="utf-8"), ensure_ascii=False, indent=1)
PUB = ["id", "destino", "tipo_oferta", "formato", "lugar_codigo", "monto", "monto_maximo", "moneda", "unidad_publicada", "pescadores_cubiertos", "noches", "dias_de_pesca", "habitacion",
       "fecha_de_lectura", "fuente_tipo", "usd_por_pescador_por_dia", "usd_por_pescador_por_dia_max", "ars_por_pescador_por_dia", "formula", "entra_en_la_comparacion", "motivo_si_no", "nota_publica"]
with open(os.path.join(AQUI, "precios.csv"), "w", encoding="utf-8", newline="") as f:
    w = csv.DictWriter(f, PUB, extrasaction="ignore")
    w.writeheader()
    for r in FILAS:
        w.writerow({k: ("" if r[k] is None or (k == "noches" and r[k] == 0) else ("si" if r[k] is True else "no" if r[k] is False else r[k])) for k in PUB})
print(json.dumps({k: res[k] for k in res if k not in ("pesos", "no_filas", "fichas", "excluidas_motivos")}, ensure_ascii=False, indent=1))
