#!/usr/bin/env python3
"""Fetches river heights (INA a5 API) and weather (Open-Meteo) for the destinations in
wordpress/plugins/pescamapa-core/data/rio.json and writes one JSON file for rio-guardar.php.

  python3 wordpress/build/rio-traer.py SALIDA.json     fetch and write
  python3 wordpress/build/rio-traer.py --test          self-check of the calculations (no network)

Read-only towards both services. A station that fails is left out with a warning; the run fails
only when nothing at all could be read (so a broken source is never published as 'all fine').
"""
import json, sys, time, datetime as dt
from urllib.request import Request, urlopen
from urllib.parse import urlencode

INA = "https://alerta.ina.gob.ar/a5"
METEO = "https://api.open-meteo.com/v1/forecast"
CONFIG = "wordpress/plugins/pescamapa-core/data/rio.json"
UMBRAL_TENDENCIA = 0.05  # m in 3 days: below this the river is "stable"


def get_json(url, intentos=3):
    ultimo = None
    for i in range(intentos):
        try:
            req = Request(url, headers={"User-Agent": "pescamapa-rio/1.0 (+https://pescamapa.com)"})
            with urlopen(req, timeout=90) as r:
                return json.loads(r.read().decode("utf-8"))
        except Exception as e:  # noqa: BLE001 - report and retry
            ultimo = e
            time.sleep(2 * (i + 1))
    raise RuntimeError(f"{url}: {ultimo}")


def fecha_local(ts):
    """INA stamps daily values at 03:00Z = 00:00 in Argentina (UTC-3): the local date is the value's day."""
    t = dt.datetime.strptime(ts[:19], "%Y-%m-%dT%H:%M:%S") - dt.timedelta(hours=3)
    return t.date().isoformat()


def tendencia(serie):
    """serie: [(YYYY-MM-DD, metros)] ascending. Compares the last value with the one 3 days before it."""
    if len(serie) < 2:
        return None, None
    ultimo_dia = dt.date.fromisoformat(serie[-1][0])
    previo = None
    for dia, val in serie:
        if (ultimo_dia - dt.date.fromisoformat(dia)).days >= 3:
            previo = val  # the latest value at least 3 days older than the last one
    if previo is None:
        previo = serie[0][1]
    delta = round(serie[-1][1] - previo, 2)
    if delta > UMBRAL_TENDENCIA:
        return "sube", delta
    if delta < -UMBRAL_TENDENCIA:
        return "baja", delta
    return "estable", delta


def altura(estacion, niveles, hoy):
    desde = (hoy - dt.timedelta(days=10)).isoformat()
    filas = get_json(f"{INA}/obs/puntual/series/{estacion}/observaciones?" + urlencode({"timestart": desde, "timeend": hoy.isoformat(), "format": "json"}))
    serie = sorted((fecha_local(f["timestart"]), float(f["valor"])) for f in filas if f.get("valor") is not None)
    if not serie:
        raise RuntimeError(f"estación {estacion}: sin observaciones en 10 días")
    tend, delta = tendencia(serie)
    return {
        "fecha": serie[-1][0], "altura": serie[-1][1], "tendencia": tend, "delta_3d": delta,
        "alerta": niveles.get("nivel_alerta"), "evacuacion": niveles.get("nivel_evacuacion"),
        "serie": [{"fecha": d, "m": v} for d, v in serie],
    }


def clima(lat, lon):
    q = {"latitude": lat, "longitude": lon, "past_days": 3, "forecast_days": 3, "timezone": "America/Argentina/Buenos_Aires",
         "daily": "wind_speed_10m_max,precipitation_sum"}
    d = get_json(f"{METEO}?{urlencode(q)}")["daily"]
    return [{"fecha": f, "viento_max": v, "lluvia": l} for f, v, l in zip(d["time"], d["wind_speed_10m_max"], d["precipitation_sum"])]


def main(salida):
    cfg = json.load(open(CONFIG))["destinos"]
    hoy = dt.datetime.now(dt.timezone.utc).astimezone(dt.timezone(dt.timedelta(hours=-3))).date()
    estaciones = {e["id"]: e for e in get_json(f"{INA}/obs/puntual/estaciones?format=json")}
    out, leidos = {}, 0
    for clave, c in cfg.items():
        item = {}
        if c.get("estacion"):
            try:
                niveles = estaciones.get(c["estacion"], {})
                item["rio"] = {"estacion": c["estacion_nombre"], "curso": c["rio"], **altura(c["estacion"], niveles, hoy)}
                leidos += 1
            except Exception as e:  # noqa: BLE001
                print(f"AVISO {clave}: altura no disponible ({e})", file=sys.stderr)
        try:
            item["clima"] = clima(c["lat"], c["lon"])
            leidos += 1
        except Exception as e:  # noqa: BLE001
            print(f"AVISO {clave}: clima no disponible ({e})", file=sys.stderr)
        if item:
            out[clave] = item
    if not leidos:
        sys.exit("ERROR: no se pudo leer nada (ni altura ni clima); no se publica.")
    datos = {"capturado": dt.datetime.now(dt.timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ"), "destinos": out}
    json.dump(datos, open(salida, "w"), ensure_ascii=False, indent=1)
    print(f"OK: {len(out)} destinos, {leidos} lecturas -> {salida}")
    # Which stations already have the reading of today's Argentine date (to pick the run hour).
    hoy_ar = (dt.datetime.now(dt.timezone.utc) - dt.timedelta(hours=3)).date().isoformat()
    con_rio = [it["rio"] for it in out.values() if it.get("rio")]
    de_hoy = sum(1 for r in con_rio if r["fecha"] == hoy_ar)
    print(f"HORA: {dt.datetime.now(dt.timezone.utc):%H:%M}Z ({(dt.datetime.now(dt.timezone.utc) - dt.timedelta(hours=3)):%H:%M} AR); lecturas con fecha de hoy ({hoy_ar}): {de_hoy} de {len(con_rio)}")
    for clave, it in out.items():
        r = it.get("rio")
        print(f"  {clave}: " + (f"{r['altura']} m ({r['fecha']}, {r['tendencia']} {r['delta_3d']})" if r else "solo clima"))


def prueba():
    assert fecha_local("2026-10-19T03:00:00.000Z") == "2026-10-19"
    assert fecha_local("2026-10-19T02:59:59.000Z") == "2026-10-18"
    s = [("2026-10-19", 5.38), ("2026-10-20", 5.40), ("2026-10-21", 5.45), ("2026-10-22", 5.52), ("2026-10-23", 5.60)]
    assert tendencia(s) == ("sube", 0.2), tendencia(s)  # 5.60 vs 5.40 (3 days earlier)
    assert tendencia([("2026-10-20", 5.0), ("2026-10-23", 4.7)]) == ("baja", -0.3)
    assert tendencia([("2026-10-20", 5.0), ("2026-10-23", 5.03)])[0] == "estable"
    assert tendencia([("2026-10-23", 5.0)]) == (None, None)
    assert tendencia([("2026-10-22", 5.0), ("2026-10-23", 5.3)]) == ("sube", 0.3)  # fewer than 3 days: compare with the first
    cfg = json.load(open(CONFIG))["destinos"]
    assert "goya" in cfg and cfg["goya"]["estacion"] == 23
    print("prueba ok")


if __name__ == "__main__":
    if len(sys.argv) > 1 and sys.argv[1] == "--test":
        prueba()
    else:
        main(sys.argv[1] if len(sys.argv) > 1 else "rio.json")
