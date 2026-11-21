#!/usr/bin/env python3
"""Compara el arreglo $D de wordpress/scripts/nota-precios-pesca-guiada-2026-11-20.php con resumen.json y busca nombres en el CSV publico.
  python3 -I wordpress/build/precios-2026-11/verificar.py   (sale con 1 si algo no coincide)"""
import json, os, re, sys, statistics as st
A = os.path.dirname(os.path.abspath(__file__))
R = json.load(open(os.path.join(A, "resumen.json"), encoding="utf-8"))
F = json.load(open(os.path.join(A, "precios.json"), encoding="utf-8"))["filas"]
php = open(os.path.join(A, "../../scripts/nota-precios-pesca-guiada-2026-11-20.php"), encoding="utf-8").read()
D = {k: int(v) for k, v in re.findall(r"'([a-z0-9_]+)' => (\d+)", php[php.index("$D = ["):php.index("$sep")])}
u = [r for r in F if r["entra_en_la_comparacion"]]
lp = [r for r in u if r["tipo_oferta"] == "paquete_noches" and r["moneda"] == "USD" and r["lugar_codigo"][:3] in ("COR", "PAN", "PAS")]
pan = [r["monto"] for r in F if r["tipo_oferta"] == "semana_lodge" and r["lugar_codigo"].startswith("PAN")]
esq = [r for r in u if r["lugar_codigo"] == "ESQ-B"]
r5 = lambda x: int(x + 0.5)
E = dict(filas=R["filas"], usables=R["usables"], fuera=R["excluidas"], oferentes=R["lugares"], usd=R["por_moneda"]["USD"], ars=R["por_moneda"]["ARS"], eur=R["por_moneda"]["EUR"], brl=R["por_moneda"]["BRL"],
         propia=R["por_fuente"]["propia"], listado=R["por_fuente"]["listado"], programa=R["por_fuente"]["programa"],
         bar_n=R["bariloche_dia"]["n"], bar_lug=R["bariloche_dia"]["lugares"], bar_min=R["bariloche_dia"]["min"], bar_max=R["bariloche_dia"]["max"], bar_med=R["bariloche_dia"]["mediana"],
         bar_pp_min=R["bariloche_dia"]["pp"]["min"], bar_pp_max=R["bariloche_dia"]["pp"]["max"],
         cor_n=R["semana_corrientes"]["lugares"], cor_min=R["semana_corrientes"]["min"], cor_max=R["semana_corrientes"]["max"], cor_med=R["semana_corrientes"]["mediana"],
         cor_d_min=r5(R["semana_corrientes"]["dia_min"]), cor_d_max=r5(R["semana_corrientes"]["dia_max"]),
         pn_n=6, pn_min=min(pan), pn_max=max(pan), pn_med=int(st.median(pan)), pn_dn=R["semana_patagonia_norte"]["lugares"], pn_d_min=r5(R["semana_patagonia_norte"]["dia_min"]), pn_d_max=r5(R["semana_patagonia_norte"]["dia_max"]),
         ps_n=R["semana_patagonia_sur"]["lugares"], ps_min=R["semana_patagonia_sur"]["min"], ps_max=R["semana_patagonia_sur"]["max"], ps_med=int(R["semana_patagonia_sur"]["mediana"]),
         ps_d_min=r5(R["semana_patagonia_sur"]["dia_min"]), ps_d_max=r5(R["semana_patagonia_sur"]["dia_max"]),
         esq_n=len(esq), esq_min=min(r["monto"] for r in esq), esq_max=max(r["monto"] for r in esq), esq_d_min=r5(min(r["usd_por_pescador_por_dia"] for r in esq)), esq_d_max=r5(max(r["usd_por_pescador_por_dia"] for r in esq)),
         lp_n=len(lp), lp_lug=len({r["lugar_codigo"] for r in lp}), lp_min=min(r["monto"] for r in lp), lp_max=max(r["monto"] for r in lp), lp_med=int(st.median([r["monto"] for r in lp])),
         lp_d_min=r5(min(r["usd_por_pescador_por_dia"] for r in lp)), lp_d_max=r5(max(r["usd_por_pescador_por_dia"] for r in lp)),
         fichas=R["fichas"]["total"], con_precio=R["fichas"]["con_precio"], sin_precio=R["fichas"]["total"] - R["fichas"]["con_precio"], pct=r5(100 * R["fichas"]["con_precio"] / R["fichas"]["total"]))
mal = [(k, D.get(k), v) for k, v in E.items() if D.get(k) != v] + [(k, D[k], None) for k in D if k not in E]
for m in mal:
    print("NO COINCIDE", m)
csv = open(os.path.join(A, "precios.csv"), encoding="utf-8").read().lower()
nombres = [r["lugar_interno"].lower() for r in F if r["lugar_interno"] and not r["lugar_interno"].startswith(("lodge no", "guia en", "agencia", "barco", "paquete", "operador", "lodge de"))]
for n in nombres + ["tripadvisor", "viator", "hooked", "karku", "river house", "nervous", "pescar.pro"]:
    if n in csv or (n in php.lower() and n not in ("tripadvisor", "viator", "hooked")):
        print("NOMBRE EN PUBLICO:", n); mal.append(n)
print("OK" if not mal else "FALLA", len(E), "cifras")
sys.exit(1 if mal else 0)
