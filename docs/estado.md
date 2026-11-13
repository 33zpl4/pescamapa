# Estado del proyecto — para retomar en otra sesión

Última actualización: 13/11/2026 (posicionamiento y consentimiento; antes, 9/11/2026: revisión del negocio y origen de las consultas; antes, 31/10/2026: idiomas completados en es/pt/en, estrategia SEO v2, auditoría con lente de IA, fichas de guías de día en Bariloche). Actualizá este archivo al cerrar cada sesión.

## Objetivo (del dueño)

**Ser útil para la gente que quiere pescar**, y que Google y las IA nos
refieran. No gastar tiempo en lo que no es prioridad. Las entrevistas a
gente local generan contenido propio; las consultas de pescadores llegan
todas a nosotros (centralizar datos y cobrar comisión más adelante).

## Qué está online

- **pescamapa.com = WordPress** en el servidor aaPanel de FansTemple, detrás
  de Cloudflare (ver `docs/wordpress.md`). Indexable, con sitemap
  `wp-sitemap.xml`. Español, `/pt/` y `/en/`.
- **Goya** (es/pt/en): reglamento, calendario y FAQs actualizados el
  20/10/2026 (`wordpress/scripts/goya-2026-10-20.php`). Sin fecha de
  "reglamento verificado" (nadie lo confirmó con la autoridad).
- **Panel para Cheyenne**: rol "Editor de Pescamapa", solo español, menú
  corto. Las traducciones las propagamos nosotros (columna "Traducciones").
- **Entrevistas, prestadores y consultas** (PR del 20/10/2026, ver
  `docs/wordpress.md` → "Entrevistas, prestadores y consultas").
- **Panel sin bloqueos** (21/10): ningún campo es obligatorio; el editor
  ve solo "Autor de la foto". `test-wp` entra como editora, cambia una foto
  y guarda en cada PR.
- **Rama `claude/confident-feynman-4oj6zh` (21/10/2026, sin mergear)**:
  definición y lema nuevos, `llms.txt` y `robots.txt` (`inc/maquinas.php`),
  reglas de marca (`docs/marca.md`), tipo **Notas** (`/notas/`),
  bloque **"Más para hacer"** en las guías (con marca de publicidad),
  registro de cuándo se concreta una consulta (base de las reseñas,
  `docs/resenas.md`). Para que Goya muestre el enlace a Bitácora de Viajes y
  las consultas lleguen a `info@pescamapa.com`, después del merge correr
  `goya-mas-2026-10-21` y `email-consultas-2026-10-21` con `wp-script.yml`.
- **Revisión mensual de fotos**: issue automático (`revisar-fotos.yml`); el
  primero es el #23.
- **Esquina**: **publicada el 20/10/2026** (2.ª guía, solo español),
  reglamento verificado por el equipo del dueño el 20/10/2026. Lo dudoso
  sigue como notas PENDIENTE (no se ven). Investigación:
  `docs/investigacion/esquina.md`.

## Al 13/11/2026: posicionamiento "pedí una vez, comparamos por vos" (rama, sin mergear)

- **Decisión del dueño:** el mensaje para el pescador es "Decinos qué querés pescar. Nosotros encontramos dónde." (preguntamos a los operadores que encajan y respondemos en 48 horas con las opciones comparadas). Reglas de copy y consentimiento: `docs/viaje.md` y `docs/prestadores.md` → "Posicionamiento (13/11/2026)".
- **En la rama:** formulario `/armar-viaje/` (título, bajada, tres pasos, campo opcional "fijo/flexible"), botón de ficha "Pedir opciones para pescar en <lugar>", `consulta.ok` dividida (pescador con 48 h / prestador), línea de consentimiento nueva (consultamos sin nombre ni contacto; el contacto se da solo con tu OK), acuse, bloque en la portada, "Cómo se sostiene", `llms.txt`, política de privacidad y términos.
- **Fuga detectada:** un pescador encontró a un operador en nuestra página y le escribió directo. Contramedidas decididas: registro del lead ante el operador (correo sin datos personales, fechado; cliente nuestro si lo registramos o nombra a Pescamapa, 12 meses), decirle al pescador cómo nos pagamos y pedirle que nos mencione, y que el pedido sea el camino más fácil.
- **Borrador, no enviado:** `docs/condiciones-operadores.md` (es/en, 10 % pagado por el operador); el primer operador ofreció 10 % por correo el 13/11/2026.
- **Pendiente del dueño:** aprobar el texto y las condiciones; decidir si el correo de registro al operador se automatiza (correo saliente nuevo). Nada está online hasta merge + deploy; sin captura mirada.

## Al 13/11/2026: primera consulta real de viaje y tarifas 2027 de Las Buitreras

- **Primera consulta real por el formulario (12/11/2026):** Río Gallegos, desde la ficha de Las Buitreras de la guía en inglés; llegó por Google. Sin datos personales acá.
- **Enviado:** respuesta al pescador; consultas a Solid Adventures (Las Buitreras), Karku, El Rincón, First Cast Fly Fishing y la oficina de turismo de Santa Cruz.
- **Respuesta de Solid Adventures (13/11):** solo semanas completas y enero de 2027 ya completo; mandó el programa "Las Buitreras – Season 2027" (PDF, no está en el repo). Tarifa: US$ 9.990 por pescador, semana de 7 noches y 6 días de pesca, del 30/12/2026 al 24/4/2027; acompañante US$ 3.000; individual +US$ 500. Cargado en la ficha y en la guía de Río Gallegos con `las-buitreras-2027-2026-11-13` (fuente "programa 2027 enviado por Solid Adventures, 13/11/2026"); la ficha sigue como información pública, sin acuerdo. Foto del programa con crédito "Solid Adventures" (regla del dueño del 13/11: foto del material de la propia fuente, con crédito visible, se quita si la piden).
- **Abierto:** esperar las otras respuestas; Truchaike y Güer Aike sin verificar (solo teléfonos). Idea aprobada por el dueño: una nota sobre estadías cortas en el Gallegos, a escribir con las respuestas de los operadores.
- **Sitio (PR 169, ya mergeado):** estado de envío en Consultas, enlaces en el acuse de recibo y promesa de respuesta en 48 horas.

## Al 11/11/2026: paquetes por operador en Bariloche y guías de El Calafate y Ushuaia

- **Bariloche:** 5 paquetes de operador publicados, cada uno con precio y fuente (script `paquetes-bariloche-operadores-2026-11-11`).
- **Guías nuevas:** El Calafate y Ushuaia, en es/en/pt, con un paquete cada una (script `calafate-ushuaia-2026-11-11`).
- **Viator (11/11, pegado por el dueño):** sexto operador en Bariloche, Guides Patagonia (€ 599,69 por grupo de hasta 2, 10 h), y variante de medio día de Outfitters (€ 445,22); script `paquetes-bariloche-viator-2026-11-11`. El resto del banco (Tangol, Mendoza, Trout Bariloche € 90,86 que no cuadra con su web) en `docs/paquetes-banco-plataformas-2026-11-11.md`.
- **Despublicados los dos paquetes sin validar de Bariloche (11/11, decisión del dueño):** el del lago ("desde US$ 400") y el del Limay con Claudio Antonelli ("desde US$ 550"), en es/en/pt, con `publicado` = 0 y nota interna (no se borraron); script `paquetes-bariloche-quitar-no-validados-2026-11-11`. Quedan seis de operador (Trout Bariloche, Southern Rivers Patagonia, Patagonia in the Loop, Patagonia Drift Fly Fishing, Outfitters Patagonia, Guides Patagonia).
- **Mergeado el arreglo de `investigar.yml`** (PR 167): una falla de apt ya no tumba la lectura de páginas.
- **En PR (sin mergear):** precios de paquetes con decimales ("€ 599,69") y fecha "Actualizado el" de la página de paquetes igual a la más reciente entre la guía y las fechas de precio de los paquetes publicados.
- **Decisión del dueño (11/11):** publicar paquetes con datos públicos de plataformas, pedirle al operador que confirme o se saca, y solo dos guías nuevas (no ampliar más).
- **Regla de trabajo del dueño:** el modelo principal dirige y revisa; la ejecución se delega a subagentes en Sonnet.
- **Documentos:** `docs/paquetes-bariloche-fichas-2026-11-11.md` y `docs/paquetes-banco-plataformas-2026-11-11.md`.

## Al 9/11/2026: revisión del negocio y origen de las consultas

- **Revisión completa: `docs/revision-negocio-2026-11-09.md`.** Diagnóstico: mucho contenido y ninguna prueba del negocio (0 consultas, 0 operadores con acuerdo; 10 clics por semana, casi todos de altura del río). Palancas en orden: cerrar un viaje a mano con un operador con acuerdo, demanda que paga (semanas de lodge en inglés, grupos al dorado en portugués) y probarla con un experimento chico de anuncios, enlaces entrantes, página "Quiénes somos" con el respaldo de la agencia. Tiene un tablero a 30 días y la lista de lo que depende del dueño. Dos propuestas contradicen decisiones previas y esperan su respuesta: escribirles ya a los prestadores listados (antes: "cuando haya tráfico") y un experimento de anuncios de US$ 150 a 300 (antes: sin publicidad paga).
- **Código en la rama `claude/determined-wright-q3oxnq` (sin PR ni deploy todavía):** cada consulta guarda por qué página entró la persona, desde qué sitio llegó y un **Canal** (ChatGPT, Perplexity, Gemini, Google, WhatsApp…), visible en el aviso, en la consulta y en una columna de wp-admin; acuse de recibo por correo a quien deja un email (texto fijo, en su idioma); el aviso interno lleva `Reply-To` del pescador. Sin cookies (`sessionStorage`); política de privacidad actualizada en es/pt/en. PR 161. **Página "Quiénes somos"** (`/quienes-somos/`, `/pt/quem-somos/`, `/en/about/`), sin fotos por ahora: Alberto y Cheyenne (guía de naturaleza, no de pesca), método, red local en Goya y cómo se sostiene el sitio; enlazada desde el pie, el sitemap y `llms.txt`. Decisiones del dueño del 9/11: sin publicidad paga; el sitio es hoy fuente de información y Goya es el primer lugar donde concretar una consulta con guías y alojamiento conocidos. Falta: `test-wp` en verde, deploy, una consulta de prueba real y Bing Webmaster Tools (dueño).

## Al 8/11/2026: hub de licencias para extranjeros y fotos con fuente web

- **Hub de licencias** (es/en/pt, clave `licencias-extranjeros-argentina`): `/notas/licencia-pesca-extranjeros-argentina-2026-27/`. Tabla por región (Patagonia, Tierra del Fuego ordinario + permiso adicional por zona preferencial y tarifa de país limítrofe, Santa Cruz, Corrientes), en pesos y en dólares aproximados con **US$ 1 = $ 1.545 (xe.com, 8/11/2026, dato del dueño)**. Los valores de Tierra del Fuego salen de la Resolución S.A. 259/2026 (leída entera); Patagonia, Santa Cruz y Corrientes, de las páginas ya publicadas. La rareza de la norma (diario limítrofe Río Grande/Menéndez $ 65.000 > diario general $ 59.000) se publica tal cual y marcada para confirmar con la Secretaría de Ambiente. Falta: Entre Ríos, Santa Fe, Chaco, Misiones, Buenos Aires (sin fuente oficial leída) y confirmar el permiso de vecinos de Chubut en una fuente oficial.
- **Fotos (regla del dueño, 8/11/2026):** Unsplash cuando se puede, con nombre del fotógrafo; si no, la web de origen, citada como fuente. Mismo criterio que con los prestadores: si alguien pide que se quite, se quita. Se carga con `foto_origen: cedida` y `foto_autor` = fotógrafo o dominio. Primera foto de Esquel y Trevelin: `pesca.chubut.gob.ar` (`esquel-foto-2026-11-08.php`). La otra foto que pasó el dueño (pescador con mosca, fuente pescachubut.ar) no llegó como archivo: queda disponible si se vuelve a enviar.
- **Enlaces al hub (9/11/2026):** Search Console mostró impresiones para "argentina fishing license" (25, posición media 65, EE. UU.) en la portada `/en/`, la nota de licencia de la Patagonia y otras, pero no en el hub (publicado el 8/11). Se enlazó el hub desde la sección del permiso de las guías de Río Grande y Esquel y Trevelin (es/en/pt, `hub-licencias-enlaces-guias-2026-11-09.php`) y desde las notas de licencia de la Patagonia y de pesca desde Brasil (generador). Indicador a mirar: si el hub empieza a aparecer para esa búsqueda y sube de posición. Un sola búsqueda con 25 impresiones es ruido hasta que haya más datos.
- **Rondas de investigación del 10/11/2026** (`docs/investigacion/consultas-pendientes-2026-11-10.md`, diez rondas con preguntas listas para pegar; auditoría de R1, R2 y R4 en `docs/investigacion/auditoria-rondas-r1-r2-r4-2026-11-10.md`). Publicado (es/en/pt, VERIFIED): tarifas del lodge del río Manso 2026/27 y 2027/28 en la página de la semana de trucha (Patagonia norte); Jurassic Lake (US$ 8.000 por semana, con la otra cifra de US$ 6.900 del mismo sitio) en la página de la Patagonia sur; en la nota de Brasil, los documentos del auto (ARCA), Migraciones, un paquete en reales y la ruta a Goya. **No se publicó:** la franquicia de US$ 300 (es para quien regresa a la Argentina), la norma de aduana sobre cañas (sin fuente oficial), las esperas del paso, los medios de pago y las fechas de introducción de cada trucha (los asistentes se contradicen). Pendiente: actualizar las fichas de trucha con el paper de Macchi y Vigliano (2014), ya verificado.
- **Search Console por búsqueda y página** (`scripts/gsc-consultas.mjs`, workflow `gsc-consultas.yml`): sirve para ver qué página nuestra muestra Google en cada búsqueda. Primer análisis del 10/11 (11/10 al 8/11): 165 búsquedas, 544 impresiones, 4 clics; las búsquedas en inglés de interés están en posiciones 60 a 90. Las URLs viejas de las fichas de especie (`/en/species/trucha/`, `/en/species/dorado/`, `/pt/especies/surubi/`) siguen apareciendo en Google y redirigen bien (301) a las nuevas.
- **Entrevistas a guías locales:** el dueño las va a conseguir; falta armar la lista de preguntas (reglas de consentimiento en `docs/contenido.md`).

## Al 6/11/2026: licencia, Bariloche y pesca desde Brasil

- **Páginas nuevas o corregidas (es/en/pt):** `licencia-pesca-patagonia-precios` (valores oficiales del permiso patagónico 2026/27 y permiso de 10 días para vecinos de Río Negro, E-L10, solo en esa provincia), `precio-pesca-bariloche-limay` (por niveles: medio día, día completo, Manso, pesos) y `pesca-desde-brasil-corrientes` (licencia de turista extranjero de Corrientes por litros de nafta, documentos, rutas). Detalle y fuentes: `docs/pendientes-y-mejoras.md`, `docs/investigacion/medicion-ia/2026-11-06-gemini-search.md`, `docs/analisis-ahrefs-2026-11-06.md`.
- **Medición con IA, solo a pedido:** `medir-ia.yml` sin cron; `sonda-bots.yml` (acceso de rastreadores de IA, peso del HTML, 404) corrió limpia; `consultar-ia.mjs` detecta cifras propias.
- **Regla que se afirmó:** los valores aproximados se publican siempre rotulados como aproximados, con su cálculo y su fecha (dueño, 6/11/2026).

## Al 5/11/2026: páginas de precios de paquetes (es/en/pt) y medición con APIs de IA

- **Cinco páginas de precios en vivo** (notas de tipo guía, 15 en total): `/notas/viaje-pesca-dorado-con-amigos/` (la ancla), `/notas/cuanto-cuesta-viaje-pesca-dorado/` (escalera de noches de los lodges), `/notas/cuanto-cuesta-pesca-con-mosca-bariloche/`, `/notas/cuanto-cuesta-semana-pesca-trucha-patagonia/`, `/notas/cuanto-cuesta-pescar-tierra-del-fuego-calafate/`; en `/en/notes/…` y `/pt/notas/…`. Formato elegido porque es el que ChatGPT citó en la medición del día 0: respuesta corta arriba, tabla por noches y días de pesca con precio por persona, qué incluye y qué no, costo por día de pesca calculado, preguntas como títulos. **Decisión del dueño:** la fuente pública es "investigación propia a partir de datos públicos y proveedores asociados" con fecha de lectura; no se enlazan las páginas de cada precio. Fuentes exactas y errores de ChatGPT: `docs/investigacion/precios-paquetes-2026-11-05.md`. Contenido en `wordpress/contenido/precios/*.json`; script generado `wordpress/scripts/precios-paquetes-2026-11-05.php` (`wordpress/build/precios-a-script.mjs`; traducción con `traducir-precios.mjs`, números verificados).
- **APIs de IA desde la sesión:** `scripts/consultar-ia.mjs` (ChatGPT con búsqueda web, Gemini por el endpoint compatible, sin búsqueda). Medición día 0 en `docs/investigacion/medicion-ia/2026-11-05/`: ChatGPT cita lodges con escalera de precios y no a Pescamapa; la corrida de Gemini salió vacía (corregido el script). Repetir a los 14 y 30 días. Gemini con búsqueda necesita la clave en el encabezado `x-goog-api-key` (entorno o secreto del repo).
- **Lección:** ChatGPT con búsqueda inventó o desactualizó varios precios (Futa Lodge es chileno, Calafate con tarifa vieja, El Tránsito sin precio publicado). Nunca se publica un precio sin leer la página del operador con `investigar.yml`.

## Al 30/10/2026 (leer primero)

- **Lista única de pendientes y mejoras: `docs/pendientes-y-mejoras.md`** (4/11/2026): Patagonia por completar, producto, prestadores, medición, correo y operación, con quién la hace y prioridad. La lista "Pendientes, en orden" de más abajo queda como historial.

- **Portugués de las tres guías nuevas (4/11/2026):** Río Grande (#217), Río Gallegos (#218) y Esquel y Trevelin (#219) en `/pt/…`, con la descripción en portugués de las 10 fichas y el precio de Kau Tapen (`*-pt-2026-11-04`, simulacro y `VERIFIED`). Con esto las seis guías de Patagonia están en es, en y pt. Pendiente de Patagonia: el anexo de Parques Nacionales (Los Alerces, Lago Puelo), Estancia María Behety, La Villa y Despedida (sus sitios no abren), el reglamento general de Santa Cruz y la lectura de las tarifas 2026/27 de los lodges de Chubut.

- **Temporada estructurada y Esquel (4/11/2026).** (1) Cada guía puede llevar `temporada_abre` y `temporada_cierra` (norma), `pico_meses` con `pico_quien` (mejor época, solo con un nombre) y una `temporada_nota` por idioma: de ahí salen la **barra de 12 meses** y la primera pregunta de la guía y del FAQPage, **"¿Se puede pescar en X ahora?"**, calculada con la fecha de hoy y con esa fecha dentro de la respuesta (una página en caché queda vieja, nunca equivocada). Sin fechas no se muestra nada. En vivo en las seis guías de Patagonia (PR #143; script `temporada-fechas-2026-11-04`, 27 cambios con `VERIFIED`). Código: `inc/temporada.php`, `parciales/barra-meses.php`. (2) **Esquel y Trevelin (Chubut)** en vivo (#211 es, #212 en, 4 fichas), escrita con el Anexo Chubut y Ambientes Chubut 2026/27, que esta vez sí abrieron con `investigar.yml` en modo navegador. Pendiente de esa guía: el anexo de Parques Nacionales (Los Alerces, Lago Puelo), que sigue sin leerse. (3) Corrección: las guías de Patagonia son seis, no nueve.

- **PESQA analizada (4/11/2026)** (`docs/competencia-pesqa-2026-11-04.md`): 731 URLs, 244 destinos, es/pt/en, páginas atadas al mes. Débil en lo nuestro: su página de Río Gallegos todavía muestra la temporada 2025/26, sus "guías cerca" son comercios de OpenStreetMap y no trae precios. Qué tomamos: fechas de temporada estructuradas, barra de meses y "¿Se puede pescar ahora?" (P14). **Fotos de portada** de Río Grande (Facundo Santana) y Río Gallegos (tripin.travel) cargadas el 4/11 con su crédito (`patagonia-sur-fotos-2026-11-04`, origen "cedida").

- **Patagonia: dos guías nuevas y la línea base (4/11/2026).** En vivo: **Río Grande** (Tierra del Fuego, #199 es, #200 en, 4 fichas) y **Río Gallegos** (Santa Cruz, #205 es, #206 en, 2 fichas), escritas con las normas oficiales 2026/27 leídas en el PDF y el portal (`docs/patagonia-fuentes-y-guias.md`). Falta la **traducción al portugués de ambas** (regla del dueño: lo que existe en un idioma se completa) y **Esquel/Trevelin**, que espera el anexo oficial de Chubut (el dueño debe pegarlo: el sitio bloquea los runners). Limay River Lodge salió de la lista de Bariloche (está en el Limay Medio, Neuquén; vuelve con una guía propia). Línea base del 4/11 en `docs/preguntas-llm.md` (preguntas 11 a 20 de Patagonia y cómo sale la búsqueda web hoy: páginas de agencias con precios sin fecha). Descubrimiento de método: los agentes de búsqueda del sandbox no abren páginas (ENOTFOUND); toda lectura de fuentes va por `investigar.yml`, y a veces conviene repetirla (una de tres corridas se canceló por solaparse).

- **Correo saliente (4/11/2026):** `info@pescamapa.com` recibe (Cloudflare Email Routing, MX + SPF + DKIM `cf2024-1`) pero el servidor **no puede enviar**: `wp_mail` falla con "No se pudo establecer la función de correo" (sin programa de correo, sin plugin SMTP; scripts `email-diagnostico-2026-11-04` y `email-prueba-2026-11-04`), y nunca se guardó ninguna consulta. El SPF raíz solo autoriza a Cloudflare y el DMARC es `quarantine`. Solución preparada: `inc/correo.php` manda todo `wp_mail` por la API de Resend (remitente `Pescamapa <info@pescamapa.com>`), inerte sin clave. Activar: (1) dominio `pescamapa.com` verificado en Resend con sus registros en Cloudflare (subdominio de envío + `resend._domainkey`, solo DNS, sin tocar MX ni SPF del raíz); (2) secreto del repo `RESEND_API_KEY`; (3) correr el workflow `correo-clave.yml` (guarda la clave en la opción `pm_resend_key` por stdin); (4) correr `email-prueba-2026-11-04` y mirar la bandeja; (5) probar el formulario real. **Activado y verificado el 4/11/2026:** dominio verificado en Resend, clave guardada con `correo-clave` (corrida 41824673356), `email-prueba-2026-11-04` aceptado por Resend (corrida 41824726960) y el dueño recibió el correo. Pendiente: una consulta de prueba desde el formulario real (guardado en Consultas + aviso completo).

- **Idiomas y enlaces desde la altura del río (3/11/2026, a pedido del dueño tras el export de Search Console 25/10–2/11):** (1) las URL de especies llevan el slug de cada idioma (`PM_SLUGS_ESPECIE` en `inc/i18n.php`): `/en/species/trout/`, `/en/species/golden-dorado/`, `/pt/especies/truta/`, `/pt/especies/dourado/`, `/pt/especies/surubim/`, `/pt/especies/peixe-rei/`; las URL viejas con la clave (`/en/species/trucha/`) siguen resolviendo y dan 301 a la nueva (lo hace el redirect a la URL real de `template_redirect`). La clave compartida no cambia. Una especie nueva con otro nombre en pt/en se suma a esa constante. (2) `hreflang="x-default"` apunta ahora al inglés cuando la página existe en inglés (antes al español). (3) Cada punto de `/altura-del-rio/<x>/` muestra arriba, junto al título, el paso siguiente: guía del lugar y "Pedir cotización" con el destino preseleccionado; los puntos sin guía (Buenos Aires, Delta, Paraná bonaerense) enlazan a las especies (dorado, surubí, pejerrey) y a la cotización general. Cloudflare "Always Use HTTPS" ya está activado (lo confirmó el dueño). Dato del export: las consultas en inglés ("trout patagonia", "golden dorado") aterrizan en `/en/species/trucha/` en posición 60–80; el sitio es nuevo, no se saca conclusión todavía.

- **Search Console al 1/11 (issue #132):** semana 24–30/10: 10 clics, 759 impresiones, posición media 14,6 (la anterior: 2 / 68 / 37,3); 58 de 60 URL indexadas. Casi todo lo que rankea son las páginas de **altura del río** (Zárate, Baradero, Paso de la Patria, Goya, La Paz; posiciones 2–11). Respuesta del mismo día: bloque "Qué significa para pescar" en cada estación (texto según estado: sube/baja/estable/mareal/sobre alerta, con enlace a la temporada de la guía y a "Guías de pesca en <ciudad>"), **hub por provincia** `/litoral/corrientes/` etc. (destinos + guías + paquetes + río + calendario + notas; CollectionPage; `llms.txt` "Por provincia"; miga de la guía enlaza al hub) y bloque **"Guías que pescan <especie>, con precios"** en las notas con especie y en las fichas de especie (lee las mismas fichas que las páginas por ciudad). El informe semanal nunca disparó por cron (0 corridas programadas desde el 22/10; `rio.yml` sí): cron movido a lunes 13:23 UTC; si tampoco dispara, correrlo a mano los lunes.

- **Calendario y página para prestadores (1/11/2026):** `/calendario-de-pesca/` (es/pt/en) agrega, por región, las filas de temporada y el resumen del reglamento de cada guía con autoridad, fecha de verificación y enlaces a la guía y a "Guías de pesca en <ciudad>"; FAQPage; sección propia en `llms.txt` ("Qué se pesca cada mes y qué está vedado") con una línea por destino. `/para-guias-y-lodges/` (es/pt/en): qué damos (ficha en 3 idiomas, consultas calificadas, sin cuota), qué pedimos (datos confirmados, tres fotos propias, respuesta en el día), corrección/baja, FAQ y el formulario `prestador`; el pie y la sección "Prestadores" del inicio enlazan ahí. No hay campo estructurado de vedas: el calendario muestra el texto de cada guía; si hace falta una matriz especie × mes, agregar un campo `vedas` al esquema.

- **Patagonia entra al sitio.** Guías nuevas o puestas al día el 30/10/2026: **Lago Strobel / Jurassic Lake** (Santa Cruz, es "Lago Strobel", en "Jurassic Lake"), **San Martín y Junín de los Andes** (Neuquén, contenido 2026/27 cargado en la guía del 24/10) y **Bariloche y el Nahuel Huapi** (Río Negro, #161 es / #162 en, en vivo y verificadas). Todo el detalle, las fuentes y los valores de permisos: **`docs/patagonia-fuentes-y-guias.md`**.
- **Estrategia de listados refinada** (dueño, 29-30/10): listar ya, con información pública y baja visible; dos niveles de ficha; formulario "concierge"; fotos solo cedidas con crédito; precios solo con fecha y fuente. Resumen en `docs/prestadores.md` → "Estrategia de listados (30/10/2026)".
- **Fuentes oficiales del reglamento: pegadas desde el navegador del dueño.** `reglamentodepesca.org.ar` y los portales de Neuquén y Santa Cruz bloquean los runners de GitHub. Lo que se pegó se lee como fuente oficial (anexos Neuquén y Río Negro, puntos generales); los listados de ambientes se leyeron en copias de AICACYP. Faltan el listado de ambientes de Río Negro y el texto general. `docs/investigacion/README.md` → "Lo aprendido el 30/10/2026".
- **Dossiers de lodges** (lote 1, ocho lodges): `docs/investigacion/lodges-lote1-dossiers-2026-10-30.md`. Regla: solo vale el dominio del lodge o una fuente regulatoria; una tarifa sin temporada es `NO CONFIRMADO`; las agencias no son fuente.
- **Inicio:** se saca la tarjeta "¿Qué querés pescar?" del mapa del hero (`vistas/inicio.php`); se unen las dos guías de San Martín/Junín (había una duplicada por un error mío: ver el recap de Patagonia) y el mapa muestra "Lago Strobel" en español y "Jurassic Lake" en inglés.
- **Error que no se repite:** antes de crear una guía, buscar si ya existe (clave y nombre en `wordpress/scripts/` y en la lista de guías vivas).
- **Paquetes de Bariloche (30/10/2026, dueño):** página "Paquetes de pesca en Bariloche" en vivo (`/paquetes-de-pesca/bariloche/`, `/en/fishing-packages/bariloche/`), paquete "Truchas del lago Nahuel Huapi (Bariloche): jornada en lancha con asado en la playa" (nombre que usa el mercado). **Con precio de referencia publicado por decisión del dueño:** desde US$ 400 para 1 pescador, jornada completa; 2 pescadores: US$ 600 en total (parte de los costos se reparte), fechado 30/10/2026 (lo ofrece un agente que organiza estos paquetes y con quien se trabaja si hay interesados). Antes salió a US$ 650 por 2 pescadores según una cotización de mercado del 5/11/2026 (competidora sin nombrar); de esa cotización viene el "incluye" publicado, todavía sin confirmar con el agente (guía, equipo, asado con bebidas, permisos, lancha y combustible). El "desde 450" de la agencia no se usa: unidad no confirmada. Las consultas entran por el formulario ("Pedir cotización") y se pasan a un operador; la propuesta final puede variar. Scripts `paquetes-bariloche`, `paquetes-titulo-bariloche`, `paquetes-precio-bariloche` (2026-10-30). Pendiente: que el agente confirme qué incluye (el "incluye" publicado viene de la cotización de mercado) y "Bahía López" como punto de encuentro.
- **Segundo paquete de Bariloche: Limay (30/10/2026, dueño):** "Pesca con mosca en el río Limay (Bariloche): descenso en bote a remo, jornada completa", en la misma página de paquetes. **Se lista al precio de la referencia de mercado por ahora** (dueño): desde US$ 550 para 1 pescador, 2 pescadores US$ 650 en total (cotizaciones del 19/11/2026, 07:00–16:00; competidora sin nombrar); se ajusta cuando haya un operador local. Reglas del tramo del anexo de Parques 2026/27 (bloque "Antes de viajar"). No se publican el peso de los peces, el largo del descenso ni el punto de encuentro (la agencia da dos lugares distintos). Script `paquetes-limay-bariloche` (2026-10-30).
- **Foto en la tarjeta de un paquete (30/10/2026):** los paquetes ahora admiten una foto opcional (campo "Foto" en la pestaña Paquetes de la guía; el crédito sale sobre la imagen como en las demás fotos). Primera: Limay, crédito "Claudio Antonelli" (cedida por el dueño; ortografía como la escribió, sin confirmar). Script `paquetes-foto-limay-2026-10-30`; va después del deploy del plugin.
- **Foto de Paso de la Patria reemplazada (31/10/2026, dueño):** un dorado saltando, crédito "Jim Klug" (cedida; ortografía como la escribió el dueño), optimizada de 324 a 171 KB (1000×949). Reemplaza la de Wikimedia (Playa Punta Mitre), que sigue en la biblioteca de medios. Script `paso-foto-2026-10-31`.
- **Foto de La Paz reemplazada (31/10/2026, dueño):** canoa y pescadores en la costa del Paraná al atardecer, crédito "Roque Mario Erazun" (cedida; ortografía como la escribió el dueño), recortada 2 px por lado (traía un borde claro) y optimizada de 213 KB (1600×990) a 89 KB (1200×741). La anterior (#8) sigue en la biblioteca de medios. Script `la-paz-foto-2026-10-31`.
- **Cuatro ideas de contenido en inglés guardadas (31/10/2026, dueño):** (1) pescar la Patagonia por tu cuenta, (2) golden dorado: delta vs Corrientes, (3) lodges de la Patagonia (qué compran USD 10.000 por semana), (4) río Limay superior/medio/boca. Se trabaja **la 1 primero**; el resto espera. Evaluación contra nuestras reglas y qué frena a cada una: `docs/ideas-contenido-seo-2026-10-31.md`. Directiva de investigación y dos auditorías externas (permisos y bioseguridad con base; presupuesto de 7 días y accesos sin confirmar): `docs/investigacion/diy-patagonia-directiva-y-auditorias-2026-10-31.md`.
- **Idea 1 publicada (31/10/2026): "Fly fishing Argentina on a budget: how to fish Patagonia without a lodge"** (`/en/notes/fly-fishing-argentina-on-a-budget/`, clave `patagonia-por-tu-cuenta`, solo en inglés por ahora; portada de Chad Shmukler). Contiene lo leído por nosotros: permisos y zona preferencial, reglas de equipo, agua pública y camino de sirga (arts. 235 y 1974 del CCyC leídos en argentina.gob.ar), **una sola cifra para el día con guía ("alrededor de USD 550 por barco", 1–2 pescadores; las páginas de cuatro operadores muestran 520–550 y quedan en las fuentes, sin tabla ni nombres en el texto, por decisión del dueño)** y la balsa de Villa Llanquín; el cierre enlaza al formulario y a la página de paquetes de Bariloche. **No** trae presupuesto total ni precios de auto, cabañas, combustible o supermercado (esperan la ronda de logística), ni el artículo 22 del reglamento (no se leyó). Scripts `nota-por-tu-cuenta-en`, `-ajuste` y `-ajuste2` (2026-10-31). **Paquete del Limay (desde USD 550 / 650): el dueño lo considera justo (incluye permiso, almuerzo y más); el "incluye" sigue sin confirmar con el agente.** **Versiones en español y portugués (pedido del dueño, hechas el mismo 31/10/2026)** con el script `nota-por-tu-cuenta-es-pt-2026-10-31`: la nota en español pasa a ser la fuente de la foto, el tipo, la fecha y la especie; la portuguesa enlaza las guías en español (no hay guías de Bariloche ni Junín en portugués) y lo dice en el enlace.
- **Estrategia SEO y comercial v2 (31/10/2026, noche):** `docs/estrategia-seo-2026-10-31.md` manda sobre `seo-oportunidades.md` y la lectura de la ronda 1. Tesis: dos productos (Patagonia, Litoral), dos mercados (internacional EN; regional ES/PT, que necesita su propia extracción), una capa comercial compartida; seis silos servidos con los tipos de página que ya existen (sin árbol de URLs nuevo); puntaje de 5 criterios para decidir qué publicar; correcciones de método (CPC ≠ comisión, índice de competencia ≠ dificultad SEO, no tirar <20, un país por export). **Los CSV de la ronda 2 no están en el repo: sus números quedan como reportados.** Backlog reescrito con puntaje: `docs/ideas-contenido-seo-2026-10-31.md` (P1 guías de día, P2 dorado Corrientes con precios, P3 costo del viaje, P4 directorio de lodges, P5 fichas por marca, P6 Limay, P7 pilar Patagonia, P8 delta, P9 Tierra del Fuego, P10 regional ES/PT, P11 hecho). Protocolo de la ronda 3 para el dueño: `docs/investigacion/ronda-3-keyword-planner-2026-10-31.md`. **Primera pieza de P1:** 4 fichas de guías de día en Bariloche (Trout Bariloche, Southern Rivers Patagonia, Patagonia in the Loop, Patagonia Drift), nivel 1 "información pública", tarifas fechadas 31/10/2026 de sus propios sitios, es + en; script `prestadores-bariloche-dia-2026-10-31`.
- **Idiomas completados (31/10/2026, regla del dueño: lo que exista en un idioma se completa en los otros):** pilar del dorado en es (#179 `/notas/pesca-del-dorado-en-la-argentina/`) y pt (#180); guía de Bariloche en pt (#181 `/pt/patagonia/rio-negro/pesca-em-bariloche/`) con sus paquetes en pt (`/pt/pacotes-de-pesca/bariloche/`, USD 400 / 550); descripción en pt de 11 fichas; 7 fichas con el español en su campo base (antes mostraban inglés en la página en español); guías en pt de Jurassic Lake (#182 `/pt/patagonia/santa-cruz/pesca-no-jurassic-lake/`), Chascomús (#183 `/pt/pampa/buenos-aires/pesca-em-chascomus/`) y San Martín–Junín (#184 `/pt/patagonia/neuquen/pesca-com-mosca-san-martin-junin-de-los-andes/`), en vivo y con `VERIFIED` (scripts `*-pt-2026-10-31`, traducidas de la versión inglesa **viva**, volcada con `volcar-guias-en-2026-10-31`, no del script original). Inventario de solo lectura: `inventario-idiomas-2026-10-31` (correrlo antes de dar por cerrado un idioma). Las traducciones las hizo la sesión, sin revisión nativa.
- **Precios y fechas en las fichas (dueño, 1/11/2026):** el precio que un guía muestra en un perfil público se publica (Rebo Pesca: desde $ 480.000 por lancha, script `prestadores-litoral-precios-2026-11-01`, 6 `VERIFIED`); la plataforma de origen no se nombra en la tarjeta (URL y fecha en `notas`); la tarjeta dice "precio revisado en los últimos 30 días / 3 meses / en <mes>" (`pm_fecha_relativa`), nunca la fecha exacta; internamente, columna "Revisado" en wp-admin y `scripts/fichas-por-revisar.php` (vence a los 90 días, `PM_PREST_REVISION_DIAS`). **Sin foto del guía por ahora** (ni copiada ni embebida de Instagram); la página por ciudad lleva la foto del destino.
- **Segunda tanda de fichas (1/11/2026):** Chascomús (Bordacharpapita, José Vargas, Luis Décima), La Paz (Juan Pesca Guiada), Bariloche (Lucas Pesca, Expediciones Baruzzi / Andrés Makek, Eze Faccio), script `prestadores-tanda2-2026-11-01`. Goya, Junín, San Martín de los Andes y Santa Cruz no tienen guías en Hooked: nada que sumar de esa fuente. Lectura: `hooked-3-localidades-20261101-0716`.
- **Guías de día del Litoral (1/11/2026, modo Uber):** 6 fichas nuevas de nivel 1 desde perfiles públicos, sin precio y sin contacto: Esquina #185 Rebo Pesca, #186 Beto Aguilar, #187 César Martín Pesca; Paso de la Patria #188 El Negro Cano, #189 José Titi, #190 Juan Pablo Calderaro (Del Paso Fishing #74 ya existía). Script `prestadores-litoral-dia-2026-11-01`, corrida 41282912837, 6 `VERIFIED`. Con eso la página `/guias-de-pesca/esquina/` pasa a existir (antes 404: Esquina solo tenía fichas de ejemplo). Los precios de plataforma (Hooked, FishingBooker) quedaron en `notas` como referencia interna. Volcado de todas las fichas: `volcar-prestadores-2026-11-01` (solo lectura).
- **Deploy y verificación en vivo (1/11/2026):** PR #127 mergeado, deploy 41282201555 en verde; lectura `guias-ciudad-20261101` (rama `investigacion`): `/guias-de-pesca/bariloche/`, `/en/fishing-guides/bariloche/` y `/pt/guias-de-pesca/paso-de-la-patria/` en 200 con `ItemList`, `CollectionPage`, `FAQPage` y `BreadcrumbList`; `robots.txt` con 16 bloques de `User-agent`; `llms.txt` con "Guías de pesca por ciudad". **Caída del servidor** de ~05:45 a ~07:45 UTC: la VM quedó apagada tras la migración del panel del hosting (vpsnet.es → manager.ginernet.com, plan pasa a pago por hora); se arrancó a mano. Síntoma en los workflows: `scp: Connection closed` tras 2 minutos y Cloudflare 523.
- **Página por ciudad "Guías de pesca en <lugar>" (1/11/2026, ruling del dueño frente a Hooked):** `/guias-de-pesca/<destino>/` (pt igual, `/en/fishing-guides/`), `vistas/guias.php`: respuesta corta (cuántas fichas, tarifas con fecha, licencia, cómo se contrata), las mismas tarjetas que la guía (acuerdo primero), enlace a los paquetes, FAQ y formulario; JSON-LD `ItemList` + `CollectionPage` + `FAQPage` + `BreadcrumbList` (`LocalBusiness` solo en fichas con acuerdo); hreflang, sitemap y enlace desde la guía y los paquetes. `llms.txt` suma la sección "Guías de pesca por ciudad" con una línea por prestador (tipo, modalidades, incluye, tarifa fechada, nivel de verificación; nunca contacto). `robots.txt` permite por nombre a GPTBot, OAI-SearchBot, ChatGPT-User, ClaudeBot, PerplexityBot, Google-Extended y otros, y enlaza `llms.txt`.
- **Hooked leído a fondo (1/11/2026):** `hooked.com.ar` es un marketplace de guías con reserva por Mercado Pago (101 guías, 86 localidades, cobra suscripción al guía, solo español, sin reglamento, `llms.txt` enorme y bots de IA permitidos: por eso ChatGPT lo recomienda primero). Análisis, debilidades y el plan de 8 puntos para el dominio argentino (ficha como página de producto, URL propia por prestador, guías de día del Litoral con tarifa, calendario con vedas, `llms.txt` con prestadores, página "para guías"): `docs/competencia-hooked-2026-11-01.md`. Dos decisiones del dueño pendientes: URL propia por prestador y si "según su perfil público en Hooked" vale como fuente. Corridas `hooked` y `hooked-2` en la rama `investigacion`.
- **Auditoría SEO con la lente de las respuestas por IA (31/10/2026):** `docs/auditoria-seo-ia-2026-10-31.md`. Base técnica ya cumplida (hreflang, sitemap, JSON-LD por vista, `llms.txt` generado, robots sin bloqueos a rastreadores de IA, respuesta corta arriba). Cambios de hoy: `dateModified` y `publisher` en el JSON-LD de las guías; llamado "Pedir cotización" bajo los "Datos clave" de cada nota (con destino y especie); sección "Cómo se contrata" en `llms.txt`. Verificado en vivo tras el deploy (PR #122, corrida 41189936186; `investigar` verificacion-20261031 y capturas 41190009391): `llms.txt` con la sección y los tres formularios, `dateModified`/`publisher` en el JSON-LD de Bariloche (es) y Chascomús (pt), hreflang es/en/pt-BR/x-default, el llamado bajo los "Datos clave" en las notas en inglés y español, y las guías pt de Jurassic Lake y San Martín–Junín renderizadas. Pendiente que corre una persona: las 10 preguntas de control de `docs/preguntas-llm.md` una vez por mes (única medición directa de si nos citan); PageSpeed de una guía en móvil.
- **Fotos cargadas hoy** (cedidas por el dueño, con crédito, ortografía confirmada): Jurassic Lake "Red Kulper", Junín "Chad Shmukler", Bariloche "arique.com" (adjunto #165, redimensionada de 1500×2000 / 443 KB a 1000×1333 / 217 KB; script `bariloche-foto-2026-10-30`).

## Al 24/10/2026

- **4 guías publicadas**: Goya, Esquina, Paso de la Patria (Corrientes, es/pt/en) y **La Paz, Entre Ríos** (es/pt, con aranceles de la tabla oficial, fiesta y precio 2026). Todo mergeado en `main` y desplegado; `verify-live` en verde. Contacto de licencias de Corrientes (Corrientes Turismo) en todas las guías correntinas.
- **Informe semanal** (`informe-semanal.yml`): Search Console + vigilancia de fuentes, un issue por lunes. Ya muestra impresiones reales (el sitio rankea). `docs/vigilancia.md`.
- **Mapa del inicio** verificado (proyección) y con etiquetas separadas (`docs/wordpress.md`).
- Páginas de ejemplo de WordPress despublicadas (404).
- **Siguiente sesión**: sumar ciudades con la receta de `docs/nueva-ciudad.md`. La **zona de pique** (`/pique/`) tiene su MVP construido: ver `docs/pique.md` (falta probarlo en un teléfono real).
- Pendientes del dueño: captura del mapa del inicio y de La Paz en vivo; ronda 2 de Gemini para Entre Ríos (tallas, cupos, vedas); OK para limpiar URLs muertas de `vigilancia/fuentes.json` (Entre Ríos: `boletinoficial.entrerios.gov.ar`, `entrerios.gov.ar/tramites` eran supuestas); revisar en Search Console el canonical http/https del inicio.

- **5.ª guía: San Martín y Junín de los Andes (Neuquén, es/en): en vivo desde el 24/10/2026** (PR #55 y #57; guías #94 es y #95 en; `wp-script` corrida 40 y `verify-live` en verde). El dueño confirmó el mismo día que aparece y se ve bien (inicio, mapa y etiqueta). Script `san-martin-junin-2026-10-24`. Fuentes: folleto oficial 2025/2026 del Reglamento (Parte Primera, Anexo Neuquén y Anexo Parques Nacionales del Sur, pasados por el dueño) y dos medios para la temporada 2026-2027; **todo dato del reglamento va con su año 2025-2026**. Dossier, auditoría de Gemini y lo no usado: `docs/investigacion/san-martin-junin.md` §7 a §10. **Falta**: la edición 2026-2027 del reglamento (la vigila `reglamento-patagonico` en `vigilancia/fuentes.json`), confirmar con la Intendencia del Lanín si cobra ingreso, y los prestadores (ninguno cargado).
- **Alojamientos y prestadores de Goya** (24/10/2026): modelo en `docs/modelo-alojamientos.md`. Publicados: Ogaraity (#46) y Experiencia Isoró (#48), con fotos. **Isoró Fishing Camp** está como borrador oculto (`isoro-fishing-camp-2026-10-24`): solo se sabe que es un operador grande de Goya con un Instagram exitoso; el texto largo que llegó es de origen dudoso y quedó solo en las notas internas. **Próxima sesión**: ver su Instagram, pedirles datos y fotos cedidas, completar y publicar. También: fotos extra con autor, y contactar a Ogaraity e Isoró (condición especial por Pescamapa).

## Decisiones del dueño (no relitigar)

- **APIs de IA: uso quirúrgico (6/11/2026).** Cada llamada a ChatGPT o Gemini cuesta: no se corre nada "por las dudas" ni en calendario. Se consulta cuando se detectó una oportunidad o hace falta un dato, con una hipótesis y una pregunta concreta (qué decisión cambia la respuesta). Antes se prueba lo que no cuesta: leer las páginas con `investigar.yml`, mirar lo ya medido. `medir-ia.yml` corre solo a pedido y con `preguntas`/`proveedores` acotados. Cada corrida deja anotado por qué se hizo y qué se decidió con el resultado (`docs/investigacion/medicion-ia/`).

| Fecha | Decisión |
| --- | --- |
| 20/10/2026 | Sin carteles "en construcción", sin noindex, sin bloquear publicación. Completitud = guía interna. |
| 20/10/2026 | Cheyenne edita solo en español; nosotros traducimos. Plantillas fijas; si necesita algo más, lo pide. |
| 20/10/2026 | Todas las consultas llegan a Pescamapa; el contacto de prestadores es interno. |
| 20/10/2026 | "Quotes" = citas de entrevistas (contenido y SEO), no cotizaciones de precio. |
| 20/10/2026 | Prioridad de destinos por oportunidad SEO: `docs/seo-oportunidades.md` y wireframes en `docs/seo-guia-wireframes.md` (original: `docs/fuentes/guia-desarrollo-wireframes-seo.docx`). |
| 20/10/2026 | Idioma del navegador: sugerir, nunca redirigir. |
| 21/10/2026 | Ningún campo del panel es obligatorio: guardar nunca se bloquea. Para una foto alcanza con el autor; nada de trámite de licencias "estilo NYT". Revisión mensual de fotos por issue (`revisar-fotos.yml`). |
| 20/10/2026 | Las guías se listan en orden de publicación (Goya 1.ª, Esquina 2.ª…), no alfabético (`pm_destinos_lista`). |
| 20/10/2026 | Email: Cloudflare Email Routing para `info@pescamapa.com` (en pausa: el routing quedó en "Syncing"; falta destino y regla). Después: `wp option update pm_email_consultas info@pescamapa.com`. |
| 20/10/2026 | La fecha "Reglamento verificado" es **opcional**: se muestra si está, nunca se pide ni bloquea. El protocolo de abajo es una buena práctica, no un requisito para publicar. |
| 20/10/2026 | Goya está en el Paraná medio (km ~960–990): no lleva vedas extraordinarias del Alto Paraná. |
| 20/10/2026 | **Vedas: mínimo esfuerzo.** Poco tráfico y mucho mantenimiento. Cada guía lleva un párrafo genérico (vedas por desove y extraordinarias por tramo, cambian cada año) y el enlace a la autoridad. Sin fechas ni kilómetros que haya que perseguir. |
| 21/10/2026 | **Definición**: "Pescamapa es la guía para pescar en toda la Argentina: dónde ir, cuándo, qué dice el reglamento y con qué guía local salir, con datos al día y la voz de quienes pescan ahí." Lema: "Dónde, cuándo y con quién pescar en Argentina." |
| 26/10/2026 | **Posicionamiento**: la definición pasó a "todo lo que necesitás saber antes de ir a pescar: cuándo ir, qué dice el reglamento, con quién salir; lo investigamos y mostramos las fuentes; y si querés, te armamos el viaje". Sin superlativos sin fuente. El botón de la cabecera es "Armá tu viaje"; no enlazamos guías ni lodges concretos en las notas, solo el formulario; cobramos comisión, no revendemos (por ahora). Ver `docs/viaje.md`, `docs/guia-maestra-patagonia.md`. |
| 21/10/2026 | Todo lo que se publica respeta `docs/marca.md` (colores, tipografías, tamaños, componentes) y se verifica con `revisar-marca.mjs`. |
| 21/10/2026 | "Más para hacer" es un bloque repetible por ciudad: hoy el enlace a Bitácora de Viajes (Goya); a futuro, espacio que se puede vender (marcado "Publicidad", `rel="sponsored"`). |
| 21/10/2026 | Reseñas de prestadores: se prepara la infraestructura (registro de viajes concretados), no se lanzan todavía. Plan en `docs/resenas.md`. |
| 21/10/2026 | Contenido propio: por ahora se alimenta a mano (enlace + hechos + resúmenes en el chat) y Claude arma citas, notas y entrevistas; la API viene después. `docs/contenido.md`. |
| 22/10/2026 | Vigilancia semanal automática (Search Console + fuentes que pueden vencer) en un issue por semana, sin modelos. Se dice en público solo cuando lleve varias semanas corriendo. `docs/vigilancia.md`. |
| 22/10/2026 | **Lo que no se puede verificar online no se persigue.** Si un dato (p. ej. el arancel de la licencia para turista argentino) no está publicado o actualizado en una web oficial, se dice que no lo podemos confirmar y se indica qué hacer (preguntarle a la guía local o consultar a Recursos Naturales). Que las webs públicas estén desactualizadas no es responsabilidad nuestra; no se cavan agujeros. Los aranceles de la licencia no se publican sin fuente oficial vigente. |
| 22/10/2026 | Frase para las guías cuando un dato no está online: "si no lo ves online, preguntale a tu guía". |
| 22/10/2026 | Las ciudades tienen varias fiestas: se destaca una sin sobre-indexar. Un precio pasado se publica con su año. |
| 24/10/2026 | **Fichas de prestadores**: lo que carga Cheyenne tras conocerlos se conserva; Experiencia Isoró ≠ Isoró Fishing Camp; sin referencias cruzadas públicas entre prestadores; las fichas muestran hasta 4 fotos (cedidas por ellos). `docs/modelo-alojamientos.md`. |
| 24/10/2026 | **Alojamientos**: se listan (tipo Alojamiento). Fotos solo cedidas; reseñas y puntaje de Booking no se copian; precio con fecha; cobro: afiliado → comisión directa → destacado, sin maquinaria hasta que haya consultas. `docs/modelo-alojamientos.md`. |
| 23/10/2026 | **Zona de pique**: mapa comunitario a ~500 m–1 km (no 10–20 m), alimentado por muchos pescadores y por prestadores, puede lanzarse vacío como invitación. `docs/pique.md`. |
| 21/10/2026 | `info@pescamapa.com` ya enruta (Cloudflare Email Routing). |
| 21/10/2026 | **Prestadores: listar primero, verificar después.** Se publican con información pública, marcados "sin verificar" y con enlace para corregir o pedir la baja (info@pescamapa.com). Reemplaza "solo con consentimiento". `docs/prestadores.md`. |
| 21/10/2026 | **Regla de oro**: cada página responde con un dato concreto, fechado y con fuente una pregunta real, arriba y en texto; muchos lectores serán LLM. `docs/estrategia-contenido.md` (prueba de cinco preguntas, preguntas de control mensuales en `docs/preguntas-llm.md`). |
| 21/10/2026 | **Modelo**: la oferta viaja con la respuesta (condición especial por Pescamapa, publicada en la tarjeta y en llms.txt), somos el camino fácil (idioma, precio, disponibilidad) y cobramos comisión del lado del prestador. Primero Esquina y Goya. `docs/prestadores.md`, `docs/mensajes-prestadores.md`. |
| 24/10/2026 | **Claude lanza los workflows (`wp-script`, `verify-live`) directamente, bajo demanda**: el dueño no tiene que ejecutarlos. Flujo: PR → `test-wp` en verde → merge → simulacro → `$aplicar = true` por PR → en vivo → `verify-live`. Nota: dos corridas de `wp-script` a la vez chocan en `/tmp/pm-script.php` del servidor (`Permission denied`); esperar a que termine la otra y reintentar. |
| antes | Fotos solo de Commons con licencia libre o cedidas con crédito; nunca de Google. Prestadores solo con consentimiento y precios fechados. |
| 28/10/2026 | **Competencia y paquetes (dueño):** el lodge brasileño de Goya cuyo material pegó el dueño es **competidor**: no se lista ni se nombra. **Todo lo que ofrezca se puede ofrecer** (paquetes por tamaño de grupo, alojamiento todo incluido, licencia/combustible/carnada incluidos, traslados, condiciones para agencias y grupos); sus detalles sirven de guía. Cheyenne factura como agencia por Bitácora. Página "Paquetes de pesca en Goya" en vivo desde el 28/10/2026, sin precios hasta que lleguen. `docs/preparacion-comercial.md`, `docs/referencias-rincon-del-dorado.md`. |
| 27/10/2026 | **Estrategia de negocio (dueño, todavía flexible):** fase 1 = tráfico con información útil, gratis e imparcial; fase 2 = cobrar comisión (cobrarle a los guías por figurar, o vender nosotros los paquetes, como Yellow Dog). Para eso **no se listan todos los datos del prestador**: el contacto sigue interno y las consultas pasan por Pescamapa. No se vende "directorio abierto" como diferencial ni se critica a quien hace lo mismo: hoy ganamos poco o nada y el modelo no está cerrado. Detalle en `docs/prestadores.md`. |
| 29/10/2026 | **Modelo de dos niveles** (Gemini): ficha informativa (sin acuerdo) y "Operador con acuerdo con Pescamapa" (primero en la lista, con la línea de comisión al operador). Cifras de comisión y precios del análisis externo: referencia **interna**, nunca públicas. |
| 29/10/2026 | **Fotos**: solo las que el dueño aporta como disponibles o permitidas, siempre con el crédito del fotógrafo (`docs/contenido.md`). Bitácora de Viajes **no** es un prestador de pesca: va en "otras cosas para hacer". |
| 30/10/2026 | **Publicar con lo que hay**: no se bloquea una guía por lo que falta; lo no leído se dice y se marca `NO CONFIRMADO` / PENDIENTE interno, y se completa después. |
| 30/10/2026 | **Listar ya, afirmar solo lo leído.** Un prestador entra como "información pública, sin verificar" con sitio y baja visibles; los datos concretos van "según el operador", con fuente y fecha. Un sitio que se contradice (licencia de Rio Manso) o que no abre (San Huberto, 403) se publica sin ese dato. No se usa "auditada" para el nivel básico. |
| 30/10/2026 | **Una distancia de exclusión que figura en una sola de dos copias del reglamento no se publica** (Strobel 200 m, Barrancoso 100 m). Se publican las reglas del texto firmado (arts. 11 y 15 a). |
| 30/10/2026 | **La boca del Barrancoso**: "cerca de la boca" respetando la distancia mínima legal; no hay contradicción con lo que dicen los lodges. |
| 30/10/2026 | **Nombres**: "Lago Strobel" en español, "Jurassic Lake" en inglés; los slugs ya publicados no cambian. |
| 1/11/2026 | **Frente a Hooked** (`docs/competencia-hooked-2026-11-01.md`): (a) se hace la **URL por ciudad** "Guías de pesca en <ciudad>" (es/pt/en) con los guías listados; **no** una URL por guía por ahora; (b) los precios que publica Hooked son **referencia interna** para armar nuestros paquetes, nunca dato publicable; (c) se contacta a los guías para competir con paquetes propios; el modelo (listar guías uno a uno vs paquetes, cuota fija vs comisión) sigue **abierto**: Hooked cobra solo suscripción fija al guía, sin comisión visible; la comisión por derivación no paga en la salida de día argentina y sí en paquetes y extranjero. |
| 1/11/2026 | **Modo Uber**: se construye y se lista con lo que es público (fichas, paquetes, bases), atribuido y sin contacto; se reacciona solo cuando un guía o lodge lo pide (corrección o baja inmediata). Ningún acuerdo previo frena una publicación. Resuelve la decisión "¿vale un perfil en Hooked como fuente?": sí para listar; el precio de plataforma queda interno. |
| 1/11/2026 | **Las salidas de día se mantienen** (volumen, posicionamiento, puerta de entrada y relación con el guía); lo que no se espera es que la comisión sobre una salida suelta sostenga el negocio: el margen está en paquetes armados por nosotros (día o varios días), grupos y extranjeros. **Agencia: resuelto.** Si hace falta figura de agencia, se opera a través de Bitácora (la agencia registrada de Cheyenne); deja de ser pendiente. |

## Pendientes, en orden

**Al 30/10/2026 (primero):**

- Pegar desde el navegador: `reglamentodepesca.org.ar/reglamento-items/ambientes-de-rio-negro/` y el texto general 2026/27 → cerrar Bariloche (Limay río negrino, Ñirihuau, Pichileufu), validez cruzada y fieltro.
- **Anexo I oficial de Santa Cruz** (con o sin las líneas de exclusión) y la resolución de valores.
- Guía de equipo y viento para Jurassic Lake; versión es/pt de la guía pilar del dorado.
- Lodges: contactar a los de las fichas de información pública; San Huberto (403), Spring Creek (sin dominio), Estancia Tecka (sin portal). Primer operador con acuerdo: abre el nivel 2.
- Consulta legal sobre el término "agencia" y sobre vender paquetes ajenos (pendiente con el dueño).
- Pedir indexación de las URLs nuevas en Search Console (el dueño lo hace a mano).

**Lista anterior:**

0. **Foto de Goya**: la socia subió "Experiencia Isoro (1)" (vertical) pero
   el cambio no llegó a guardarse por el bloqueo de validación, ya
   corregido. Que lo reintente (ver `docs/guia-editor.md`).
1. **Reemplazar los datos de ejemplo de los prestadores** (wp-admin →
   Prestadores, columna "En la web" = 🧪). Por cada uno: datos reales,
   permiso para aparecer, destildar "Datos de ejemplo". Sobre todo **lancha,
   capacidad y motor**, que los guías señalaron como clave.
2. **Traducir Esquina** a pt/en (hoy solo español).
3. **Primeras entrevistas**: Agus y su papá (Goya). Guía de preguntas en
   `docs/entrevistas.md`. Hace falta su permiso para nombre, foto y frases.
4. **Email de consultas**: el routing de `info@pescamapa.com` está listo;
   falta fijar el destinatario (script `email-consultas-2026-10-21`) y
   confirmar que el servidor envía correo (probar el formulario). Si no llega,
   igual quedan en wp-admin → Consultas.
5. **Próximas guías** (matriz SEO; receta en `docs/nueva-ciudad.md`; hechas Paso de la Patria, La Paz y San Martín/Junín): Río Grande → lagunas
   (Junín/Laguna de Gómez, Chascomús, Lobos).
6. **Eventos con URL estable** (`/eventos/fiesta-nacional-del-surubi/`,
   Fiesta del Pacú, Fiesta del Dorado): todavía no existe el tipo.
7. **Fichas de especie**: dorado y surubí publicadas el 24/10/2026 (es/en/pt; dorado: récord IGFA 25,28 kg con Diario Uno como fuente, tamaño típico 50–75 cm / 2,5–6 kg; surubí: pesca y devolución en Corrientes). Sin citar operadores de viaje (decisión del dueño). Faltan: pacú, patí, boga, tararira, trucha,
   pejerrey (hoy solo existen dorado y surubí).

## Protocolo de verificación (opcional, buena práctica)

Los resúmenes de buscadores repiten fórmulas ("se pesca todo el año") y no
dicen si una disposición sigue vigente ni si un guía sigue activo. Antes de
publicar una guía armada desde el escritorio:

1. **Reglamento específico**: leer completa la página de Recursos Naturales
   (u autoridad local). Para Esquina: si hay veda total o parcial de artes en
   la confluencia Corriente–Paraná y si el pacú en arroyos interiores tiene
   veda temporal.
2. **Directorio comercial real**: no cargar guías de listados de turismo
   viejos. Contactar al menos **2 guías o complejos** locales: precio de la
   jornada hoy, si incluye combustible, embarcación habilitada por
   Prefectura, y **consentimiento expreso** para figurar.
3. **Bitácora**: anotar fecha y responsable de la verificación (en la guía:
   fecha "Reglamento verificado el…"; acá: quién y qué fuente).

Bitácora de verificaciones:

| Fecha | Guía | Quién | Qué se verificó |
| --- | --- | --- | --- |
| 20/10/2026 | Esquina | Equipo del dueño | Reglamento: licencia y categorías, río Corriente solo pesca deportiva, pacú 45 cm, régimen de vedas. |

## Vedas extraordinarias 2026 (para las guías de Paso de la Patria e Itatí)

Solo como contexto (por la decisión de "vedas: mínimo esfuerzo", no se
publican fechas ni km). Según el dueño (20/10/2026):
- Mayo 2026: km 1232–1238 (Confluencia / "Los Cables"), por concentración de
  surubíes (Disposición 618 de Recursos Naturales).
- Septiembre 2026: ampliada a km 1180–1239 (riacho INTA → Confluencia,
  jurisdicción de Paso de la Patria), pesca deportiva, comercial y de
  subsistencia, por migración y desove de dorados y sábalos.
- El km 1109–1150 fue una veda extraordinaria de Chaco (zona Empedrado /
  Derqui). La Disposición 1054 levantó ambos tramos a fines de septiembre de 2026.
- Históricamente hubo cortes de emergencia en Itá Ibaté y Yahapé (km ~1100–1160)
  por bajantes. Nada de esto afecta a Goya (km ~970–990) ni a Esquina (km 853).

## Otro proyecto del mismo dueño (no mezclar)

La fábrica de sitios (`33zpl4/sites`, Hostinger) tiene su propio
`docs/estado.md`. Ahí está, por ejemplo, la mudanza de Catalina Porras a
catalinaporras.com. No usar ese hosting para Pescamapa.

## Mapa de workflows (`.github/workflows/`)

| Workflow | Cuándo | Qué hace |
| --- | --- | --- |
| `test-wp.yml` | Cada PR que toca `wordpress/**` | WordPress descartable: páginas, consultas, editora que cambia una foto. **Mergear solo en verde** |
| `deploy-wp.yml` | Push a `main` con `wordpress/**` | rsync del plugin y el tema, ajustes, verificación |
| `wp-script.yml` | A mano (`script=`) | Corre `wordpress/scripts/<script>.php` en el servidor |
| `verify-live.yml` | Semanal y a mano | Revisa lo servido: páginas, hreflang, sin PENDIENTE, formularios, redirección de www |
| `capturas.yml` | A mano | Capturas de pantalla a la rama `capturas` |
| `revisar-fotos.yml` | Mensual y a mano | Issue "Revisión de fotos" |
| `probe-wp.yml` | A mano | Diagnóstico de solo lectura: versiones, log, subida de fotos, validación de formularios |
| `investigar.yml` | A mano (`urls`, `etiqueta`, `navegador`) | Lee páginas y PDF (con `pdftotext`) respetando robots.txt y deja el texto en la rama `investigacion`. No abre los sitios que bloquean datacenters |
| `probe-movil.yml` | A mano | Playwright con un iPhone: toques del hero y menú. Solo lectura |
| `fotos.yml`, `vhost-alias.yml` | A mano | Candidatas de Commons; alias de vhost (histórico) |

## Cómo se trabaja

- Rama `claude/wordpress-studio-agentic-9i5h7t` → PR → **`test-wp.yml` en
  verde** → merge a `main` (deploy automático, `deploy-wp.yml`) →
  `verify-live.yml`.
- El sandbox no llega al servidor ni a wordpress.org: todo se prueba y se
  verifica desde GitHub Actions. Nunca afirmar que algo anda sin un run en
  verde o una captura (`capturas.yml`).
- Cambios de contenido en nombre del dueño: scripts idempotentes en
  `wordpress/scripts/` + `wp-script.yml`. Nunca pisar ediciones de wp-admin
  ni empujar la base de datos.
- **Reglas de trabajo aprendidas el 30/10/2026**: (1) un `wp-script` a la vez; (2) después de un squash-merge de la rama, volver a partir de `origin/main` (`git checkout -B <rama> origin/main`) y subir con `--force-with-lease`; (3) `list_workflow_runs` sin filtros devuelve cientos de KB: guardarlo y leerlo con un script; (4) si un texto de una IA cita artículos o enlaces, se contrasta contra la fuente abierta antes de usar nada (varias URLs estaban inventadas); (5) buscar si la guía ya existe antes de crearla.
