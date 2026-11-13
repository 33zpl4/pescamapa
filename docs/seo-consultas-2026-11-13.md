# Consultas de Search Console: qué se arregló (13/11/2026)

Base: auditoría query × página de Search Console (14/10–11/11/2026) y el HTML en vivo de ~44 páginas (rama `investigacion`, carpetas `seo-consultas-20261113-1944/` y `-1947/`). Es el "antes". Mensaje comercial desde el PR 171: *contanos qué querés pescar; preguntamos a los operadores que encajan y respondemos en 48 horas con las opciones comparadas; gratis para el pescador*. Botón: `inicio.armar` ("Pedir opciones →" / "Request options →" / "Pedir opções →").

## Qué es en vivo ahora y qué llega con el deploy

- **Contenido (scripts `seo-consultas-*-2026-11-13`)**: en vivo al correrlos (títulos de guías/notas, H2, FAQ visibles, notas nuevas, enlaces).
- **Plantilla, `ui.json`, `seo.php`**: llegan con el merge y el deploy (FAQPage en notas y especies, botón de pedido en especies, títulos y textos por página, atajos del inicio, wording de "opciones").

## Mecanismos nuevos (ver tests en `test-wp.yml`)

- **FAQ dentro del contenido** (`pm_faqs_contenido`, `pm_jsonld_faq` en `seo.php`): un bloque de grupo con clase `pm-faq`, un `<h3>` por pregunta y la respuesta debajo. El mismo marcado es la FAQ visible y la fuente del JSON-LD `FAQPage` (notas y especies no tienen campo de FAQ). Los scripts lo escriben con `pm_sc_faq()`.
- **Textos opcionales por página en `ui.json`**: `pm_t_clave( base, clave )` usa `<base>.<clave>` si existe y si no la plantilla. Guías por ciudad: `guias.title|h1|description|faq.temp.q.<clave>`; hubs de provincia: `prov.title|description|intro.<provincia>`. Sin clave específica nada cambia.
- **Pregunta de temporada en la página de guías** (`pm_guias_faqs`, `temporada.php`): "¿Cuándo es la temporada…?" armada con los campos de la guía (fechas, nota de aguas con otras fechas, autoridad, fecha de revisión del reglamento). Aparece en todas las ciudades con fechas.
- Meta de guías y de provincias pasa por `pm_recortar_meta` (≤160).
- Ficha de especie: botón "Pedir opciones" con `?especie=<clave>` y barra móvil. Lista "Guías que pescan trucha": la trucha marrón de mar de Río Grande (`trucha-marron-anadroma`) cuenta como trucha, así que Río Grande y su tarifa fechada (Kau Tapen) aparecen en la ficha.
- Inicio: fila de atajos (licencia para extranjeros, trucha, dorado, paquetes) cuando existen en ese idioma.

## Tabla de clusters

| Cluster | Valor | Qué mostraba Google | Página destino | Estado |
|---|---|---|---|---|
| "argentina fishing license" | alto | inicio EN y nota licencia Patagonia | hub `/en/notes/fishing-license-foreigners-argentina-2026-27/` | title, H2, 3 FAQ, enlaces desde inicio, nota Patagonia y guías |
| dorado (récord, tamaño, viaje) | alto | ficha de dorado sin FAQ ni pedido | ficha de dorado es/en/pt | H2 de tamaño, 3 FAQ, enlace a costo de viaje, botón de pedido |
| trucha: tipos | alto | ficha de trucha | ficha de trucha | H2 "Tipos de truchas…", bloque con guías, 2 FAQ, Río Grande en la lista de precios |
| surubí | medio | ficha de 289 palabras | ficha de surubí | 3 FAQ, enlaces a guías y paquetes, botón |
| "fishing in argentina" | medio | inicio EN | inicio EN | title nuevo |
| fly fishing Esquel | alto | guía por ciudad y ficha | `/en/fishing-guides/esquel-trevelin/`, ficha del destino | title/H1/meta/FAQ de temporada; title de la ficha |
| Tierra del Fuego / Río Grande | alto | hub sin texto | hub provincia, `/en/fishing-guides/rio-grande/` | title, meta, intro de 95 palabras; title/H1/FAQ de Río Grande |
| pacotes de pesca (PT) | medio | nota "O que inclui…" | nota renombrada + hub nuevo | title nuevo; hub es/en/pt |
| dorado Río Uruguay | medio | sin sección | pilar del dorado | sección Río Uruguay y línea honesta en "qué no cubre" |

## Pendiente de fuente ("needs a source")

Resuelto el 13/11/2026 (segunda tanda, scripts `dorado-rango-record-2026-11-13`, `santa-cruz-reglamento-general-2026-11-13`, `nota-la-zona-2026-11-13`; detalle en `docs/investigacion/la-zona-salto-grande-2026-11-13.md`):

- ~~Dorado: sección "dónde vive / rango"~~: sección "Dónde vive el dorado" con FishBase y CARU (Serra et al. 2019, reed. 2023), tamaño máximo (100 cm de longitud estándar) y mayor peso publicado (31,4 kg, aparte y rotulado), UICN "Preocupación menor" vía FishBase, FAQ "¿Dónde vive el dorado?".
- ~~"La Zona" (Río Uruguay) y reglamento, veda y operadores~~: nota nueva `la-zona-salto-grande` (es/en/pt) con reglamento de la CARU, veda 1/9–31/12, quién puede llevarte y lo que no se sabe; el pilar del dorado y la ficha la enlazan.
- Corrección de atribución: la ficha de la IGFA muestra solo el peso (25,28 kg); río, año y pescador salen de la prensa (Diario Uno). Reescrito en datos clave, párrafo, FAQ, descripción para Google y pilar.
- Reglamento general de Santa Cruz (Disposición N° 241/2026): sección y 4 FAQ en Río Gallegos; ajustes en Jurassic Lake y El Calafate; sale "no leímos el reglamento general".

Sigue pendiente de fuente:

- Surubí: tamaño y peso típicos; récord (el borrador menciona un video de 70 kg, sin fuente verificada).
- Trucha: tamaño típico y récord por especie (la página ya dice que no se publican sin fuente); dónde está la trucha de arroyo en el sur (solo sale lo de Macchi y Vigliano 2014).
- Dorado: tamaño y peso típicos con una fuente que los mida (los rangos son de la pesca deportiva y bibliografía general; el máximo ya tiene fuente).
- Río Uruguay: tasas de licencia, tallas y cupos de Entre Ríos; quién tiene el permiso argentino; si la veda rige dentro de La Zona (preguntas en el doc de investigación).
- Licencia: compra en línea en Patagonia y Tierra del Fuego (la nota solo lo dice para Santa Cruz y Corrientes).

## Qué queda

- Guía nueva de Concordia (hoy "próximamente"; la tarjeta del inicio no puede enlazar a la nota de La Zona sin un cambio de plantilla, y Concordia no está en `data/rio.json`).
- Índice de paquetes: se crearon es/en/pt con el mismo mecanismo; si alguno se salteó, ver el log.
- Pilar EN de la Patagonia.
- Chequeo http→https: del lado del dueño.
- `x-default`: resuelto el 13/11/2026 por el dueño: apunta al español (idioma fuente) cuando la página existe en español, si no al inglés. Antes apuntaba al inglés (3/11/2026). `seo.php`, el test y `CLAUDE.md` coinciden. Documentación a alinear; no se cambió el código.
- **Re-correr `gsc-consultas.yml` en ~2 semanas (≈27/11/2026)** y comparar posiciones de cada cluster contra esta tabla.

## Wording "opciones" (arreglo 8)

Reemplazos de "cotización/quote/orçamento" como etiqueta de botón o promesa por "opciones": `consulta.h2/p/enviar`, `consulta.boutique.h2/t2/t3`, `ficha.prestadores.p` (sin "conectamos"), `ns.contacto.viaje`, `paquetes.cta` ("Pedir esta opción"), `paquetes.barra`, `nota.cta` y `nota.cta.boton`, `paquetes.description/licencia/faq.2.a/3.a/4.q/4.a/incluye.vacio`, `viaje.privacidad`, `viaje.title/description` y el ítem de `llms.txt`. Se dejó, porque habla legítimamente de cotizar: `paquetes.precio.consultar`, `paquetes.intro`, `guias.resumen.sinprecio`, `pg.como.3` (el operador cotiza), `*.mensaje*` ("presupuesto" = budget) y el texto legal (`legal.json`). El género de la marca en PT queda "a Pescamapa" en `ui.json` y `legal.json`.

## Wording "opciones": lo que quedaba (13/11/2026)

La descripción para Google de las páginas de paquetes de Goya y Esquina (campo guardado `paquetes_description`, cargado por `paquetes-intros-2026-10-28`) decía "Cotización sin costo" / "Free quote" / "Cotação sem custo": no vino del tema ni de `ui.json`. Corregida con `paquetes-opciones-2026-11-13` (solo si el campo conserva ese texto). El ítem de `llms.txt` ("Pedir una cotización en español", "Ask for a quote in English", "Pedir cotação…") sí era del plugin (`maquinas.php`): ahora "Pedir opciones…". La etiqueta "Pedir cotización" como botón no existe en el tema: el botón de cada paquete en vivo dice "Pedir esta opción" (verificado el 13/11/2026); la cadena solo queda en un comentario de `vistas/paquetes.php` y en `docs/auditoria-seo-ia-2026-10-31.md` (histórico).
