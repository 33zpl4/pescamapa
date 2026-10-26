# Condiciones del río (26/10/2026)

Qué es: un bloque "Cómo está el río hoy" en la guía de cada destino y una página propia
(`/altura-del-rio/`, `/pt/altura-do-rio/`, `/en/river-levels/`) con la altura del río, si sube o baja,
los niveles de alerta y evacuación, y el viento y la lluvia de la zona. Todo con fecha y fuente.

## De dónde salen los datos (verificado con la sonda del 26/10/2026)
- **Altura y niveles:** API del INA, `https://alerta.ina.gob.ar/a5/obs/puntual/...`. Son las escalas de la
  Prefectura publicadas por el INA. Sin clave. `estaciones` trae TODAS (2,8 MB, ignora `limit`); la serie de
  altura medida tiene el mismo id que la estación; observaciones: `obs/puntual/series/{id}/observaciones?timestart=&timeend=&format=json`
  (un valor diario en metros; `timestart` 03:00Z = 00:00 de Argentina). Estaciones: Goya 23, Esquina 25,
  La Paz 26, Paso de la Patria 18, Corrientes 19, Itatí 17, Ituzaingó 15, Santa Fe 30, Concordia 79 (Uruguay).
- **Respaldo (no se usa hoy):** el reporte diario en PDF (`.../ina/06-INFORMES/diario/pdf/reporte_diario_AAAA-MM-DD.pdf`);
  `pdftotext -layout` lo lee y trae la tabla Estación/Río/Nivel/Alerta/Evacuación/Perspectiva. Ojo: la fecha
  interna puede ir 3 días atrás del nombre del archivo. La API es más fresca.
- **Clima:** Open-Meteo (`api.open-meteo.com/v1/forecast`, viento máx. y lluvia diarios, 3 días atrás y 3 adelante).
  Uso gratuito no comercial con atribución CC BY 4.0. Pocas llamadas (una por destino por día, desde el servidor
  de GitHub, nunca por visita). Se enlaza a Open-Meteo en cada bloque y en la página (atribución con enlace).
  **Supuesto del dueño (26/10/2026): el uso como bien público entra en el plan gratuito.** Pendiente: su mail a
  Open-Meteo; si responden que no, el dueño avisa y se saca el clima (`rio-traer.py`, `parciales/rio.php`, `vistas/rio.php`).

## Cómo funciona
1. `.github/workflows/rio.yml` corre todos los días (11:40 UTC) y a mano. Ejecuta `wordpress/build/rio-traer.py`
   (lee INA y Open-Meteo; falla solo si no pudo leer nada) y guarda con `wordpress/scripts/rio-guardar.php`
   (valida y guarda la opción `pm_rio`, con `VERIFIED`).
2. Qué destinos y estaciones: `wordpress/plugins/pescamapa-core/data/rio.json` (una entrada por clave de destino;
   sin `estacion` solo se muestra el clima, como San Martín/Junín).
3. Las páginas solo leen `pm_rio` (`inc/rio.php`, `parciales/rio.php`, `vistas/rio.php`). Sin datos no muestran nada.
   Un dato de más de 10 días se muestra con su fecha y un aviso.
4. Tendencia: último valor contra el de 3 días antes (±0,05 m = estable).

## Sumar un destino
Agregar su clave a `rio.json` (con `estacion` si el INA tiene una escala cerca; ids en la sonda) y listo: la
próxima corrida diaria lo carga. Los niveles de alerta/evacuación no son de pesca: son de crecida.

## Cómo se expone (SEO y LLM)
- Página propia con título/descripción por idioma, `WebPage` JSON-LD con `dateModified`, hreflang, sitemap y
  enlace en el pie. Consultas: "altura del río Paraná hoy", "altura río Paraná Goya/Corrientes/La Paz/Esquina",
  "río Paraná creciendo o bajando", "river level Paraná", "pronóstico viento Goya" (+ las guías por destino).
- Un asistente que responde "¿cómo está el río en Goya?" puede citar la página con fecha.
