#!/usr/bin/env python3
"""Fetches the two weekly reference values of the licence calculator and the cost estimator and writes one JSON file for cotizaciones-guardar.php.

  python3 wordpress/build/cotizaciones-traer.py SALIDA.json     fetch and write
  python3 wordpress/build/cotizaciones-traer.py --test          self-check of the parsing (no network)
  NAFTA_PRECIO=2177 NAFTA_FECHA=2026-10-21 python3 ... SALIDA.json   the fuel price typed in (see nafta_a_mano), when the Energía host is unreachable

  dolar  USD -> ARS, the OFFICIAL quotation of the Banco Central de la República Argentina (Estadísticas Cambiarias API, USD, latest date).
  nafta  price per litre of nafta súper at the pump in Corrientes capital: Secretaría de Energía open data, "Precios vigentes en surtidor -
         Resolución 314/2016" (CSV, streamed and filtered, never loaded whole). The Disposición 1144/2024 of Corrientes names no brand, so YPF
         (the brand with the stations in the capital); the price is the MEDIAN of its stations' declared prices, dated with the most recent
         fecha_vigencia among them. With no YPF station the median of every brand is used and the brand is left empty (the page says so).

Official sources only: no blue/parallel rate, no aggregator. A value that cannot be read is left out with a warning (the site keeps the previous one
and stops using it when it is stale: see inc/cotizaciones.php); the run fails only when NOTHING could be read. Read-only towards both services.
"""
import csv, io, json, os, statistics, sys, time, unicodedata, datetime as dt
from urllib.request import Request, urlopen

BCRA_URL = "https://api.bcra.gob.ar/estadisticascambiarias/v1.0/Cotizaciones/USD"
ENERGIA_DATASET = "https://datos.energia.gob.ar/dataset/1c181390-5045-475e-94dc-410429be4b17"
ENERGIA_CSV = ENERGIA_DATASET + "/resource/80ac25de-a44a-4445-9215-090cf55cfda5/download/precios-en-surtidor-resolucin-3142016.csv"
UA = {"User-Agent": "pescamapa-cotizaciones/1.0 (+https://pescamapa.com)"}
PRODUCTO = "NAFTA (SUPER) ENTRE 92 Y 95 RON"
BANDERA = "YPF"
VENTANA_DIAS = 60  # a station whose declared price is older than this, counted from the newest one, is a leftover: left out


def hoy_ar():
    return (dt.datetime.now(dt.timezone.utc) - dt.timedelta(hours=3)).date()


def norm(s):
    """Upper case without accents or extra spaces: 'Nafta (súper) entre 92 y 95 Ron' -> 'NAFTA (SUPER) ENTRE 92 Y 95 RON'."""
    s = unicodedata.normalize("NFKD", s or "")
    return " ".join("".join(c for c in s if not unicodedata.combining(c)).upper().split())


def con_reintentos(f, intentos=3, espera=3):
    ultimo = None
    for i in range(intentos):
        try:
            return f()
        except Exception as e:  # noqa: BLE001 - report and retry
            ultimo = e
            time.sleep(espera * (i + 1))
    raise RuntimeError(str(ultimo))


def parse_dolar(datos):
    """BCRA answer -> {'valor', 'fecha'}. Raises on anything unexpected (a broken read is never published)."""
    res = datos["results"]
    if not res:
        raise ValueError("BCRA: sin resultados")
    ultimo = max(res, key=lambda r: r["fecha"])
    for d in ultimo["detalle"]:
        if d.get("codigoMoneda") == "USD":
            v = float(d["tipoCotizacion"])
            if not 100 <= v <= 100000:
                raise ValueError(f"BCRA: cotización fuera de rango ({v})")
            dt.date.fromisoformat(ultimo["fecha"])
            return {"valor": round(v, 2), "fecha": ultimo["fecha"]}
    raise ValueError("BCRA: sin USD en el detalle")


def traer_dolar():
    def uno():
        with urlopen(Request(BCRA_URL, headers=UA), timeout=40) as r:
            return json.loads(r.read().decode("utf-8"))
    d = parse_dolar(con_reintentos(uno))
    return {
        "valor": d["valor"], "fecha": d["fecha"], "fuente_url": BCRA_URL,
        "nombre": "Cotización oficial del dólar estadounidense publicada por el BCRA (Estadísticas Cambiarias)",
    }


def mediana_nafta(filas):
    """filas: dicts with provincia, localidad, producto, tipohorario, precio, fecha_vigencia, empresabandera (CSV names).
    -> {'precio', 'fecha', 'bandera', 'estaciones'} for nafta súper in Corrientes capital, or raises."""
    cand = []
    for f in filas:
        if norm(f.get("provincia")) != "CORRIENTES" or norm(f.get("localidad")) != "CORRIENTES" or norm(f.get("producto")) != PRODUCTO:
            continue
        if norm(f.get("tipohorario")) not in ("", "DIURNO"):
            continue
        try:
            precio = float(str(f.get("precio", "")).replace(",", "."))
            fecha = dt.date.fromisoformat(str(f.get("fecha_vigencia", ""))[:10])
        except ValueError:
            continue
        if precio > 0:
            cand.append((norm(f.get("empresabandera")), precio, fecha))
    if not cand:
        raise ValueError("sin filas de nafta súper en Corrientes capital")
    ypf = [c for c in cand if c[0] == BANDERA]
    uso, bandera = (ypf, BANDERA) if ypf else (cand, "")
    nuevo = max(c[2] for c in uso)
    vigentes = [c for c in uso if (nuevo - c[2]).days <= VENTANA_DIAS]
    return {"precio": round(statistics.median(c[1] for c in vigentes), 2), "fecha": nuevo.isoformat(), "bandera": bandera, "estaciones": len(vigentes), "descartadas": len(uso) - len(vigentes)}


def filas_de_csv(texto_io):
    for fila in csv.DictReader(texto_io):
        yield fila


def traer_nafta():
    def uno():
        r = urlopen(Request(ENERGIA_CSV, headers=UA), timeout=60)
        print(f"  CSV: HTTP {r.status}, {r.headers.get('content-length')} bytes, modificado {r.headers.get('last-modified')}")
        texto = io.TextIOWrapper(r, encoding="utf-8-sig", errors="replace", newline="")
        # Stream: keep only Corrientes rows in memory, however big the national file is.
        return [f for f in filas_de_csv(texto) if norm(f.get("provincia")) == "CORRIENTES"]
    filas = con_reintentos(uno, intentos=2, espera=5)
    print(f"  filas de Corrientes: {len(filas)}")
    m = mediana_nafta(filas)
    return {
        "precio": m["precio"], "fecha": m["fecha"], "fuente_url": ENERGIA_DATASET, "bandera": m["bandera"], "localidad": "Corrientes",
        "producto": "Nafta (súper) entre 92 y 95 Ron", "estaciones": m["estaciones"],
        "nombre": "Precios vigentes en surtidor, Resolución 314/2016 (Secretaría de Energía, datos abiertos)",
    }


def nafta_a_mano(precio, fecha):
    """A price typed in by a person from the official source (workflow inputs nafta_precio and nafta_fecha), for when the Secretaría de Energía host cannot be
    reached from Actions (it timed out on 23/11/2026). Same record, no brand or station count, and the name says it was typed in; guardar validates it like any other."""
    return {
        "precio": round(float(str(precio).replace(",", ".")), 2), "fecha": dt.date.fromisoformat(fecha).isoformat(), "fuente_url": ENERGIA_DATASET, "bandera": "", "localidad": "Corrientes",
        "producto": "Nafta (súper) entre 92 y 95 Ron", "estaciones": 0,
        "nombre": "Precio de la Secretaría de Energía (datos abiertos, Resolución 314/2016) cargado a mano con su fecha",
    }


def main(salida):
    hoy = hoy_ar().isoformat()
    out = {"capturado": hoy}
    errores = []
    manual = (os.environ.get("NAFTA_PRECIO") or "").strip(), (os.environ.get("NAFTA_FECHA") or "").strip()
    fuentes = [("dolar", traer_dolar), ("nafta", (lambda: nafta_a_mano(*manual)) if all(manual) else traer_nafta)]
    for clave, f in fuentes:
        try:
            b = f()
            b["capturado"] = hoy
            out[clave] = b
            print(f"OK {clave}: {json.dumps(b, ensure_ascii=False)}")
        except Exception as e:  # noqa: BLE001
            errores.append(f"{clave}: {e}")
            print(f"AVISO {clave} no disponible ({e}); el sitio conserva el valor anterior", file=sys.stderr)
            print(f"::warning::cotizaciones: {clave} no se pudo leer ({str(e)[:150]})")
    if "dolar" not in out and "nafta" not in out:
        sys.exit("ERROR: no se pudo leer ninguna cotización; no se publica. " + " | ".join(errores))
    json.dump(out, open(salida, "w"), ensure_ascii=False, indent=1)
    print(f"-> {salida} ({', '.join(k for k in ('dolar', 'nafta') if k in out)})")


def prueba():
    muestra = {"status": 200, "results": [{"fecha": "2026-11-20", "detalle": [{"codigoMoneda": "USD", "descripcion": "DOLAR E.E.U.U.", "tipoPase": 0.0, "tipoCotizacion": 1580.0}]}]}
    assert parse_dolar(muestra) == {"valor": 1580.0, "fecha": "2026-11-20"}
    for malo in ({"results": []}, {"results": [{"fecha": "2026-11-20", "detalle": [{"codigoMoneda": "EUR", "tipoCotizacion": 1700}]}]},
                 {"results": [{"fecha": "2026-11-20", "detalle": [{"codigoMoneda": "USD", "tipoCotizacion": 15.8}]}]},
                 {"results": [{"fecha": "ayer", "detalle": [{"codigoMoneda": "USD", "tipoCotizacion": 1580}]}]}):
        try:
            parse_dolar(malo)
            raise AssertionError("debía fallar: " + json.dumps(malo))
        except (ValueError, KeyError):
            pass
    assert norm("Nafta (súper) entre 92 y 95 Ron") == PRODUCTO and norm(" Corrientes ") == "CORRIENTES"
    csv_txt = (
        "indice_tiempo,idempresa,empresa,direccion,localidad,provincia,idproducto,producto,idtipohorario,tipohorario,precio,fecha_vigencia,idempresabandera,empresabandera\n"
        "2026-11-01,1,A,Calle 1,CORRIENTES,CORRIENTES,2,Nafta (súper) entre 92 y 95 Ron,1,Diurno,2000.0,2026-11-18 10:00:00,1,YPF\n"
        "2026-11-01,2,B,Calle 2,CORRIENTES,CORRIENTES,2,Nafta (súper) entre 92 y 95 Ron,1,Diurno,2100.0,2026-11-20 09:00:00,1,YPF\n"
        "2026-11-01,3,C,Calle 3,CORRIENTES,CORRIENTES,2,Nafta (súper) entre 92 y 95 Ron,1,Diurno,2200.0,2026-11-10 09:00:00,1,YPF\n"
        "2026-11-01,4,D,Calle 4,CORRIENTES,CORRIENTES,2,Nafta (súper) entre 92 y 95 Ron,1,Diurno,1500.0,2026-05-01 09:00:00,1,YPF\n"  # leftover: older than the window
        "2026-11-01,5,E,Calle 5,CORRIENTES,CORRIENTES,2,Nafta (súper) entre 92 y 95 Ron,2,Nocturno,9999.0,2026-11-20 09:00:00,1,YPF\n"   # night price: not used
        "2026-11-01,6,F,Calle 6,CORRIENTES,CORRIENTES,2,Nafta (súper) entre 92 y 95 Ron,1,Diurno,1900.0,2026-11-19 09:00:00,2,SHELL C.A.P.S.A.\n"  # other brand: not used
        "2026-11-01,7,G,Calle 7,GOYA,CORRIENTES,2,Nafta (súper) entre 92 y 95 Ron,1,Diurno,2500.0,2026-11-19 09:00:00,1,YPF\n"  # other town
        "2026-11-01,8,H,Calle 8,CORRIENTES,CORRIENTES,3,Nafta (premium) de más de 95 Ron,1,Diurno,2600.0,2026-11-19 09:00:00,1,YPF\n"  # other product
        "2026-11-01,9,I,Calle 9,SALTA,SALTA,2,Nafta (súper) entre 92 y 95 Ron,1,Diurno,1800.0,2026-11-19 09:00:00,1,YPF\n"  # other province
    )
    m = mediana_nafta(filas_de_csv(io.StringIO(csv_txt)))
    assert m == {"precio": 2100.0, "fecha": "2026-11-20", "bandera": "YPF", "estaciones": 3, "descartadas": 1}, m
    sin_ypf = "provincia,localidad,producto,tipohorario,precio,fecha_vigencia,empresabandera\nCORRIENTES,CORRIENTES,Nafta (súper) entre 92 y 95 Ron,Diurno,2000,2026-11-20,SHELL\nCORRIENTES,CORRIENTES,Nafta (súper) entre 92 y 95 Ron,Diurno,2200,2026-11-19,AXION\n"
    m = mediana_nafta(filas_de_csv(io.StringIO(sin_ypf)))
    assert m["precio"] == 2100.0 and m["bandera"] == "" and m["estaciones"] == 2, m
    try:
        mediana_nafta(filas_de_csv(io.StringIO("provincia,localidad,producto,tipohorario,precio,fecha_vigencia,empresabandera\nSALTA,SALTA,x,Diurno,1,2026-11-20,YPF\n")))
        raise AssertionError("sin filas debía fallar")
    except ValueError:
        pass
    mano = nafta_a_mano("2.177".replace(".", ""), "2026-10-21")
    assert mano["precio"] == 2177.0 and mano["fecha"] == "2026-10-21" and mano["bandera"] == ""
    print("prueba ok")


if __name__ == "__main__":
    if len(sys.argv) > 1 and sys.argv[1] == "--test":
        prueba()
    else:
        main(sys.argv[1] if len(sys.argv) > 1 else "cotizaciones.json")
