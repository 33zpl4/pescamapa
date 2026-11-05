# Páginas de precios de paquetes (5/11/2026): fuentes internas y método

**Decisión del dueño (5/11/2026):** en público la fuente se muestra como *"investigación propia a partir de datos públicos y proveedores asociados"*, con la fecha de lectura (5/11/2026). No se enlaza la página exacta de cada precio. Este documento guarda las fuentes exactas para poder actualizar y para defender cada número. El espacio queda listo para sumar datos propios (acuerdos, tarifas que nos pasen los prestadores): cuando existan, reemplazan al dato público fila por fila.

## Método (y por qué)

1. **Medición día 0** (`docs/investigacion/medicion-ia/2026-11-05/`): ChatGPT con búsqueda, ante "viaje de dorado con amigos", citó dos lodges de Nervous Waters (Karandá, Suindá) y la tabla de tarifas de Pirá Lodge. Todas son páginas con **escalera de noches y días de pesca, precio por persona en dólares, qué incluye y qué no**. De ahí el formato: tabla por noches, respuesta corta arriba, "por día de pesca" calculado, FAQ en preguntas. Gemini (sin búsqueda) respondió de memoria con rangos genéricos (USD 150–250 por día económico, 400–700 en lodge) y sin fuentes.
2. **Pistas con ChatGPT** (`docs/investigacion/ia-2026-11-05/*.json`, script `scripts/consultar-ia.mjs`): seis consultas por zona. **No se usa nada de ahí sin leer la página del operador.** Cada precio sale de la página real, leída con `investigar.yml` en modo navegador (carpeta `paquetes-precios-20261105-20261105-2208` de la rama `investigacion`).
3. **Traducción** es → en / pt con la API de OpenAI (`wordpress/build/traducir-precios.mjs`), con verificación automática de que los números y los marcadores no cambien. Las fechas van escritas en palabras en el original para que el chequeo sea exacto.
4. **Generación**: `wordpress/build/precios-a-script.mjs` arma `wordpress/scripts/precios-paquetes-2026-11-05.php` desde `wordpress/contenido/precios/paginas.{es,en,pt}.json`. Para cambiar un precio: editar el JSON es, volver a traducir, regenerar el script (o editar la nota en el admin).

## Dónde se equivocó ChatGPT (por qué no se puede publicar sin leer)

- **Futa Lodge** lo puso en Chubut: está en **Chile**. Excluido.
- **El Tránsito (Iberá)**: dijo US$ 2.610 por pareja; la página **no publica precio** (solo "consultar").
- **Paraná Sunrise**: dijo US$ 2.695; la página no mostró precio en el lector. Excluido.
- **Calafate**: dijo $ 230.000 / $ 430.000; la página dice **$ 440.000 / $ 720.000** (los otros eran de otra vigencia).
- **Itatí Lodge** (US$ 7.850 7n/6d) y Suindá/Pirá (US$ 7.950) coincidieron; Itatí además publica 4n/3d a US$ 4.050, que ChatGPT no trajo.
- **Argentina Fishing Lodges**: la oferta real es "3 días / 4 noches por US$ 2.500, fechas elegidas dic 2026–feb 2027" y Río Bravo Lodge "desde US$ 3.600 (agosto a abril)"; ChatGPT dijo "4 días/3 noches por persona".
- **Uguay Lodge**: US$ 6.400 es **en habitación individual** y con tarifa de lanzamiento 2x1; ChatGPT lo dio como "1 persona" sin esa aclaración.
- **La Alondra**: la tarifa de la página es **de 2025** (US$ 6.340); figura en la tabla con esa etiqueta ("reconfirmar").
- **Bariloche Fishing Guides** (`barilochefishingguides.com`): su página de tarifas dice "Rates 2023/2024" → **no se usó** (vencida).
- Sin URLs en la respuesta: la API con `web_search_preview` a veces no devuelve anotaciones; no se puede confiar en que cite.

## Fuente exacta de cada número publicado (carpeta `paquetes-precios-20261105-20261105-2208`)

| Dato publicado | Archivo leído | Nota |
| --- | --- | --- |
| Suindá/Pirá Lodge: 3n/2d 4.050; 4n/3d 5.350; 5n/4d 6.495; 6n/5d 7.595; 7n/6d 7.950 (US$, doble, guía compartido); recargo single 60 %; acompañante US$ 650/noche; día completo llegada/salida US$ 950 por lancha | `04-…suinda-lodge`, `05-…pira-lodge` | Suindá: octubre 2026–mayo 2027; Pirá: todo el año. Mismo precio. No incluye licencia ni traslados |
| Itatí Lodge: 7n/6d 7.850; 4n/3d 4.050 | `17-farbank-…itati-lodge` | Incluye vuelos chárter desde Buenos Aires, licencia, equipo |
| Uguay Lodge (Iberá): US$ 6.400, 7n/6d, single | `15-pointeroutfitters-…uguay-lodge` | "Subject to change" |
| La Alondra (Iberá): US$ 6.340, sep–mayo **2025** | `16-flyfishingcaribe-…` | Etiquetada "reconfirmar" |
| Río Bravo Lodge (Paraná, Santa Fe): desde US$ 3.600 (agosto–abril); oferta US$ 2.500 3d/4n, fechas dic 2026–feb 2027 | `03-argentinafishinglodges-com` | |
| Delta superior del Paraná (barco-hotel): 3n US$ 1.260; 4n US$ 1.680 | `18-www-4tourists-…` | Catamarán 8 pasajeros |
| Posada Hambaré (Esquina): 705 / 1.010 / 1.305 (grupo 2), 605 (grupo 3) | lectura del 4/11/2026, `hambare-veda-20261105-20261105-0004` (+ `fuentes-chatgpt-dorado-…`) | Faltan 3d/4d para grupo de 3 |
| Esquina, salida de un día: desde $ 480.000 por lancha | lectura del 1/11/2026 (perfil público de un guía) | |
| Chime Lodge (Junín de los Andes): 3.950 / 4.950 / 5.750 / 6.350 / 6.950 | `07-www-nervouswaters-…chime-lodge` | Diciembre 2026–abril 2027 |
| Lodges de Cholila 9.200, Futaleufú 8.750, Trevelin 6.545 (7n/6d, temporada alta) | `08-www-patagoniaflyfishinglodge-…` | |
| Challhuaquen Lodge, semana 6–13 marzo 2027: US$ 5.800 | `09-www-isolationoutfitters-…` | Incluye licencias y traslados desde Esquel |
| Bariloche: US$ 500 / 520 y licencias (10/30/90/180/30) | `10-barilochefishingtrips-…` | Licencias: dato del operador |
| Bariloche: desde US$ 545 | `13-www-elorigen-…` | |
| Bariloche: $ 666.000 por día (marzo 2027) | `12-pescar-pro-…` | Plataforma de reservas |
| Calafate: $ 440.000 / 720.000 y acompañante 185.000 / 335.000 | `14-calafatetravel-…` | |
| Kau Tapen Lodge (Río Grande): 13.950 / 14.950 / 12.950, licencia ~US$ 550 | lectura del 4/11/2026 (ver `docs/patagonia-fuentes-y-guias.md`) | Single, guía compartido |

## Pendiente

- Hambaré: la temporada de sus tarifas. Los paquetes 3d/4d para grupos de 3 se leyeron el 5/11/2026 (US$ 855 y US$ 1.105) y están en la nota ancla.
- Precios de salidas de un día en Paso de la Patria y Goya (ningún operador los publica); micros/vuelos; valor de la licencia de Corrientes.
- Rate sheets de Karandá y Delta Lodge (Nervous Waters), Pirá "Rates 2025-26 PDF", Villa María Lodge (trucha de mar, Río Gallegos) y Despedida Lodge: no leídos.
- Medición día 14 y día 30 de las mismas preguntas (`scripts/consultar-ia.mjs`, carpeta `medicion-ia/`).
- Gemini con búsqueda (grounding) requiere la clave en el encabezado `x-goog-api-key` (config del entorno o secreto del repo).
