#!/usr/bin/env python3
"""Gasto diario de GitHub Actions por workflow (19/11/2026).

Se corre desde el sandbox con `gh` (no gasta minutos de Actions):
    python3 -I scripts/uso-actions.py            # mes en curso
    python3 -I scripts/uso-actions.py 2026-11-01 # desde una fecha
    python3 -I scripts/uso-actions.py 2026-11-01 pescamapa,design-momuto  # varios repos privados

Los minutos del plan (2.000/mes) son de la cuenta y los comparten los repos
privados; los repos públicos (p. ej. momuto-api) no consumen minutos del plan.

Minutos estimados = duración de cada run redondeada hacia arriba al minuto
(incluye algo de cola, así que sobrestima). Calibración con la facturación
real del 15 al 19/11/2026: 620 min estimados = US$ 3,21 facturados,
o sea ~US$ 0,005 por minuto estimado. El umbral del dueño (US$ 0,50 por día)
equivale a ~100 minutos estimados: los días por encima salen marcados.
"""
import collections, datetime as dt, json, math, subprocess, sys

REPOS = (sys.argv[2] if len(sys.argv) > 2 else "pescamapa,design-momuto").split(",")
USD_POR_MIN = 3.21 / 620  # facturado 15–19/11/2026 ÷ minutos estimados de esos días
UMBRAL_USD = 0.50

desde = sys.argv[1] if len(sys.argv) > 1 else dt.date.today().replace(day=1).isoformat()
runs = []
for repo in REPOS:
    salida = subprocess.run(
        ["gh", "run", "list", "-R", f"33zpl4/{repo}", "-L", "2000", "--created", f">={desde}",
         "--json", "workflowName,createdAt,startedAt,updatedAt,conclusion"],
        capture_output=True, text=True, check=True).stdout
    for r in json.loads(salida):
        r["workflowName"] = f"{repo}: {r['workflowName']}"
        runs.append(r)
fecha = lambda s: dt.datetime.fromisoformat(s.replace("Z", "+00:00"))

dias = collections.defaultdict(lambda: collections.defaultdict(lambda: [0, 0, 0]))
for r in runs:
    if not r.get("startedAt") or not r.get("updatedAt"):
        continue
    m = max(1, math.ceil((fecha(r["updatedAt"]) - fecha(r["startedAt"])).total_seconds() / 60))
    d = dias[r["createdAt"][:10]][r["workflowName"]]
    d[0] += 1
    d[1] += m
    d[2] += r.get("conclusion") == "failure"

total = 0
print(f"Desde {desde}: {len(runs)} runs. Umbral: US$ {UMBRAL_USD:.2f}/día ≈ {UMBRAL_USD / USD_POR_MIN:.0f} min.\n")
for dia in sorted(dias):
    mins = sum(v[1] for v in dias[dia].values())
    total += mins
    usd = mins * USD_POR_MIN
    marca = "  ⚠ ALTO" if usd > UMBRAL_USD else ""
    top = sorted(dias[dia].items(), key=lambda z: -z[1][1])[:4]
    detalle = "; ".join(f"{w} {v[0]} runs/{v[1]} min" + (f"/{v[2]} fallidos" if v[2] else "") for w, v in top)
    print(f"{dia}  {mins:4} min  ~US$ {usd:4.2f}{marca}\n    {detalle}")
print(f"\nTotal estimado: {total} min (~US$ {total * USD_POR_MIN:.2f}); el plan incluye 2.000 min/mes.")
