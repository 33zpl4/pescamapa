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
1. `.github/workflows/rio.yml` corre todos los días (10:07 UTC = 07:07 en Argentina; antes 11:40 UTC, cambiado el 28/10/2026 para ver si ya hay lectura del día; el log imprime la línea HORA con cuántas estaciones ya tienen la fecha de hoy) y a mano. Ejecuta `wordpress/build/rio-traer.py`
   (lee INA y Open-Meteo; falla solo si no pudo leer nada) y guarda con `wordpress/scripts/rio-guardar.php`
   (valida y guarda la opción `pm_rio`, con `VERIFIED`).
2. Qué destinos y estaciones: `wordpress/plugins/pescamapa-core/data/rio.json` (una entrada por clave de destino;
   sin `estacion` solo se muestra el clima, como San Martín/Junín).
3. Las páginas solo leen `pm_rio` (`inc/rio.php`, `parciales/rio.php`, `vistas/rio.php`). Sin datos no muestran nada.
   Un dato de más de 10 días se muestra con su fecha y un aviso.
4. Tendencia: último valor contra el de 3 días antes (±0,05 m = estable).

## Buenos Aires, Delta y Río de la Plata (26/10/2026)

13 estaciones de las escalas de Prefectura (red del INA, lectura diaria verificada con la sonda del 26/10): Buenos Aires, San Isidro, Olivos, La Plata, Martín García (Río de la Plata); Tigre, San Fernando (Delta, Luján); Escobar, Campana, Zárate (Paraná de las Palmas); Baradero, San Pedro, San Nicolás (Paraná inferior). Son lugares **sin guía**: en `rio.json` llevan `nombre` y `grupo: "ba"` y el hub los muestra sin enlace, bajo su propio título. La provincia figura como `BUENOSAIRES` (sin espacio) en la API.
No incluidos: Mar del Plata (mareógrafo del SHN, serie 3347 distinta del id de estación; lectura de marea, no sirve la tendencia de 3 días), los arroyos del AMBA y las lagunas (sin altura oficial: solo viento/lluvia). Pendiente: pronóstico de marea (SHN), sin fuente abierta verificada.

## Estructura de las páginas (según la intención de búsqueda)

- **Hub** `/altura-del-rio/`: responde "cómo está el río hoy" de un vistazo: línea de resumen (cuántos suben/bajan), atajos por zona (Litoral / Buenos Aires) y una tabla por zona. Cada fila enlaza a su punto.
- **Un punto** `/altura-del-rio/<clave>/` (pt `/altura-do-rio/`, en `/river-levels/`): una página por estación, para "altura del río en Zárate hoy". Arriba la respuesta en una frase (altura, fecha, tendencia, niveles), luego número grande, viento/lluvia, tabla de los últimos días (la serie se guarda en `pm_rio`), puntos cercanos, enlace a la guía si existe y CTA. La clave de `rio.json` es la URL; mismo slug en los 3 idiomas, hreflang y sitemap incluidos; una clave desconocida da 404.
- La **guía** de un lugar sigue mostrando su bloque `#rio` con enlace al punto.

## Sumar un destino
Agregar su clave a `rio.json` (con `estacion` si el INA tiene una escala cerca; ids en la sonda) y listo: la
próxima corrida diaria lo carga. Los niveles de alerta/evacuación no son de pesca: son de crecida.

## Cómo se expone (SEO y LLM)
- Página propia con título/descripción por idioma, `WebPage` JSON-LD con `dateModified`, hreflang, sitemap y
  enlace en el pie. Consultas: "altura del río Paraná hoy", "altura río Paraná Goya/Corrientes/La Paz/Esquina",
  "río Paraná creciendo o bajando", "river level Paraná", "pronóstico viento Goya" (+ las guías por destino).
- Un asistente que responde "¿cómo está el río en Goya?" puede citar la página con fecha.

**Portugués (28/10/2026, dueño):** los textos dicen "nível do rio", no "altura do rio": así buscan los brasileños ("nível do rio Paraná em Paso de la Patria"). La dirección sigue siendo `/pt/altura-do-rio/` para no romper enlaces.

## Histórico para notas (20/11/2026)

Una herramienta de investigación, **no una función del sitio**: junta el material para una nota con fecha («cómo está el Paraná contra los últimos 36 años»). No escribe en el sitio, no tiene opción propia ni cron; la nota la redacta una persona (o el orquestador) con los números reales.

- **Qué hace** (`wordpress/build/rio-historia.py`, solo biblioteca estándar): lee de la API del INA la serie diaria 1990 → hoy de las estaciones no mareales de `rio.json` más Corrientes (19) y Santa Fe (30), que no tienen página en el sitio (`REFERENCIA` en el script). Por estación calcula: última lectura y fecha; mínimo, percentiles 10/25/50/75/90 y máximo de ese día del calendario en 1990–(año anterior) y el percentil de la lectura; diferencia con la mediana; cambio a 30 y 90 días; el mismo día en el año de menor y de mayor altura media (con su año); mínima/media/máxima de cada año; récords con fecha; puesto de la media del 1 de enero a hoy entre todos los años.
- **Salida** (`salida/`): `resumen.json`, `resumen.md` (tabla + hechos en oraciones, sin interpretación + reglas), `datos-<clave>.csv`, `grafico-<clave>.svg` y `panel.svg` (SVG hechos con texto, 1200 px de ancho de `viewBox`, colores y familias de `docs/marca.md` con fuentes de respaldo; se leen sin las webfonts y a 390 px).
- **Reglas de limpieza:** (1) un valor por día local (UTC−3): promedio de las lecturas del día, al centímetro (2016–2021 tienen varias por día); (2) se descarta lo que no es número o queda fuera de −2 a 60 m; (3) una racha de 0,00 exacto se descarta si la lectura anterior o la siguiente está a más de 1 m de 0 (hueco de la escala); si está a menos, se conserva (río realmente bajo).
- **Estadística:** referencia = años completos desde 1990; los días se alinean por fecha del calendario (el 1 de marzo es siempre el índice 60; el 29 de febrero tiene su lugar, con menos años); percentiles por interpolación lineal; percentil de la lectura = parte de los años con ese día por debajo (empates cuentan la mitad); «año más bajo / más alto» = menor / mayor media entre los años con ≥ 300 días; el puesto de la media usa los años con ≥ 80 % de los días del tramo. Las bandas y la mediana de los gráficos van suavizadas (15 días); tablas y CSV no. Categorías descriptivas por percentil: < 10 muy bajo, < 25 bajo, 25–75 normal, ≤ 90 alto, más muy alto. Baradero y Zárate tienen huecos (1998–2005, 2000–2005): se calculan con los años que hay y `resumen.md` dice cuántos.
- **Cómo correrlo:** Actions → *River history* (`rio-historia.yml`, solo `workflow_dispatch`, 10 min, sin secretos). Imprime `resumen.md` en el log y empuja los archivos a la rama `rio-historia` (reemplaza su contenido; `git fetch origin rio-historia`). Prueba sin red: `python3 -I wordpress/build/rio-historia.py --autotest` (también en `chequeo-local.sh`).
- **Qué NO se afirma:** pronóstico, profundidad real (son alturas de escala, cada una con su cero), comparación entre estaciones, ni efecto sobre la pesca. Las alturas de 2026 son lecturas del INA sin validar. El sitio no muestra esto: las páginas de punto siguen igual.
