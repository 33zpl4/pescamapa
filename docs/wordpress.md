# Pescamapa en WordPress

Desde el 20/10/2026 **`pescamapa.com` es WordPress** (antes, Astro en
Cloudflare Workers). El sitio Astro (`src/`) queda en el repo como
referencia y para generar estilos/datos, pero ya no se publica: el Worker
`pescamapa` de Cloudflare no tiene dominio. `nuevo.pescamapa.com` sigue
apuntando al mismo WordPress (alias del vhost).
El 26/10/2026 el dueño **desconectó el repositorio de ese Worker** (sin dominio
asociado): ya no arma cada rama ni comenta en los PR. El DNS y el proxy de
Cloudflare no cambiaron.

## Dónde corre

- Servidor propio con **aaPanel** (el de FansTemple), Ubuntu 24.04, Apache,
  **PHP 8.3**, WP-CLI 2.12. Carpeta: secret `WP_PATH`.
- **Cloudflare delante**: DNS con proxy, SSL *Full (strict)* con certificado
  de origen de Cloudflare (instalado en aaPanel), caché de estáticos.
- PHP tiene **`proc_open` deshabilitado**: nada de comandos de WP-CLI que se
  relancen solos (`rewrite structure/flush --hard`, `cron event run`…). El
  `.htaccess` lo escribe el deploy entero.

## Qué es de quién

| Superficie | Dueño | Cómo cambia |
|---|---|---|
| Plugin `pescamapa-core` (tipos, campos, idiomas, URLs, reglas, SEO) | git | `wordpress/plugins/pescamapa-core` → deploy |
| Tema `pescamapa` (diseño) | git | `wordpress/themes/pescamapa` → deploy |
| Contenido (guías, especies, próximos, fotos) | wp-admin | lo editan las personas; el deploy nunca lo pisa |
| Ajustes del sitio (URL, enlaces permanentes, indexable) | git | `deploy-wp.yml` |

## Deploy

`.github/workflows/deploy-wp.yml`: push a `main` (y, mientras dure el
staging, a la rama de trabajo) que toque `wordpress/**` → rsync por SSH del
plugin y el tema → WP-CLI como usuario `www` (instala/activa Secure Custom
Fields, ajustes, `.htaccess`, permisos de `wp-config.php`) → verifica las
páginas servidas. El sello `data-build` del `<html>` dice qué versión está
online.

**Migración** (una sola vez, ya corrida el 20/10/2026): Actions → Deploy
WordPress → Run workflow → `importar = true`. Exporta `src/content` a JSON
(`wordpress/build/exportar-contenido.mjs`) y lo importa
(`wordpress/build/importar.php`): crea lo que falta, actualiza lo que nadie
tocó y **respeta lo editado en wp-admin**.

**Antes de mergear**: `test-wp.yml` corre en cada PR que toca `wordpress/**`
(no hay staging): instala un WordPress descartable con SCF, el plugin y el
tema, crea contenido de prueba (script de Esquina, una entrevista con y sin
permiso, un prestador), renderiza las páginas, manda una consulta por el
formulario real y falla ante cualquier warning o fatal de PHP. Mergear solo
con ese check en verde.

**Cambios de contenido en nombre del dueño** (propagar traducciones, crear
una guía nueva desde una investigación): un script idempotente en
`wordpress/scripts/<nombre>.php` y Actions → Run a WordPress script
(`wp-script.yml`; si existe `wordpress/fotos/`, el workflow copia esas fotos a `/tmp/pm-fotos/` para que el script las cargue). Nunca pisan a ciegas lo editado en wp-admin. Corridos
(20/10/2026): `goya-2026-10-20` (reglamento de Goya), `goya-veda-2026-10-20`
(saca la veda km 1109–1150) y `esquina-2026-10-20` (Esquina, borrador #38).
También corridos: `prestadores-2026-10-20` (#39–#48, datos de ejemplo),
`esquina-reglamento-2026-10-20`, `esquina-publicar-2026-10-20` y
`esquina-verificado-2026-10-20`. Preparados el 21/10/2026 (correr después del
merge): `goya-mas-2026-10-21`, `email-consultas-2026-10-21`. La fecha "Reglamento verificado" es opcional.
También corridos el 13/11/2026: `dorado-record-igfa` (récord del dorado: río, fecha y pescador de la página de la IGFA), `surubi-trucha-tamanos` (tamaño y récord del surubí; tamaños de la trucha) y `santa-cruz-anexo-i` (Anexo I leído del Boletín Oficial: Gallegos, Jurassic Lake, El Calafate); tabla de fuentes en `docs/investigacion/fuentes-tamanos-records-2026-11-13.md`. También corridos el 14/11/2026: `itati-2026-11-14`, `ita-ibate-2026-11-14`, `esteros-del-ibera-2026-11-14` (guías nuevas de dorado en Corrientes, es/en/pt, con sus tarjetas) y `corrientes-enlaces-2026-11-14` (enlaces desde el pilar del dorado y entre las tres guías). Solo lectura, 14/11/2026: `conteo-fuentes-2026-11-14` (cuenta las fuentes de guías y notas publicadas; mismo criterio que `pm_fuentes_conteo()`, que alimenta la franja de la portada y `llms.txt`). También corrido el 15/11/2026: `nota-trucha-patagonia-pilar-2026-11-15` (nota pilar de la trucha en la Patagonia, es/en/pt, y un párrafo con el enlace en la ficha de trucha y en la nota de temporada). También corridos el 15/11/2026: `pt-pousadas-titulos-2026-11-15`, `dorado-equipo-2026-11-15` y `trucha-permiso-2026-11-15` (`pt-faq-custo-2026-11-15` se corre después del deploy del tema que deja enlaces en las respuestas de la FAQ). También corridos el 16/11/2026: `permiso-paso-a-paso-2026-11-16` y `costo-viaje-2026-11-16` (notas `permiso-pesca-argentina-paso-a-paso` y `cuanto-cuesta-viaje-pesca-argentina`, es/en/pt, con sus enlaces entrantes). También el 16/11/2026: `semana-lodge-2026-11-16` (nota `semana-de-pesca-en-lodge-argentina`), `preparar-viaje-hub-2026-11-16` (hub `como-preparar-viaje-pesca-argentina`, con enlaces entrantes desde permiso, costo, semana y los pilares del dorado y de la trucha) y `saptdf-correccion-2026-11-16` (corrige lo que decían la nota-hub de licencias, la guía de Río Grande y su nota de aguas sobre el sistema de accesos SAPTDF; la portada tiene el atajo `inicio.atajo.preparar` al hub). También el 16/11/2026, programa del dorado y el surubí (`docs/plan-dorado-surubi-2026-11-16.md`): `inventario-dorado-surubi-2026-11-16` (solo lectura), `pilar-dorado-2026-11-16` y `pilar-dorado-set-2026-11-16` (tabla «qué destino te conviene», FAQ de 8 preguntas y enlaces en el pilar, es/en/pt), `surubi-fiesta-2026-11-16` (nota `fiesta-nacional-del-surubi-2027` es/en/pt y refuerzo de la ficha del surubí), `surubi-fiesta-oficial-2026-11-16` (reescribe esa nota con el sitio oficial de la Fiesta, goyasurubi.com, como fuente principal; plan, paso 4b) y `enlaces-dorado-surubi-2026-11-16` (párrafo «Para planificar» en guías, fichas y notas; corrige dos enlaces cruzados de idioma). Con ellos llegan el atajo del pilar en la portada, el nuevo texto de `nota.cta` y la clase `pm-estilo` (tabla en tarjetas en el móvil). También el 16/11/2026: `copy-visa-operadores-2026-11-16` (la frase de visa del hub pasa a remitir a la página oficial de la Dirección Nacional de Migraciones, `argentina.gob.ar/migraciones/turistas`, leída con `investigar.yml`, más «confirmá con el consulado»; «operadores» pasa a «lugares y guías» en frases simples de las notas de permiso y de costo). Actualizá esta lista al correr uno nuevo.

**Revisión de fotos** (`revisar-fotos.yml`, el 1.º de cada mes y a pedido):
abre un issue "Revisión de fotos — mm/aaaa" (y cierra el anterior) con las
fotos para mirar: portadas verticales o chicas, sin autor, con nombre poco
descriptivo, pesadas, imágenes del texto sin descripción y fotos sin usar.
No bloquea nada; el equipo corrige lo que valga la pena. Licencias: alcanza
con el autor (las de Commons ya traen su licencia); no hacemos más trámite.

Diagnóstico: `probe-wp.yml` (solo lectura: versiones, opciones, log de
Apache, render desde el propio servidor). Capturas: `capturas.yml` (empuja
PNGs a la rama `capturas`).

## Modelo de datos

Igual que en Astro, ahora en WordPress:

- **Tipos**: `pm_destino` (guías), `pm_especie`, `pm_proximo`,
  `pm_entrevista`, `pm_prestador` (interno), `pm_consulta` (interno). Campos con
  Secure Custom Fields registrados en código (`inc/campos.php`): no se
  editan desde el panel.
- **Idiomas sin plugin**: un post por idioma con campos `idioma` y `clave`.
  Español en la raíz, `/pt/` y `/en/`. Textos de interfaz en
  `data/ui.json`.
- **Datos compartidos** (región, provincia, lat/lon, especies, foto
  destacada) solo en la versión en español; pt/en los heredan
  (`inc/datos.php`).
- **URLs** (`inc/rutas.php`): `[/pt|/en]/<region>/<provincia>/<slug>/`,
  `/especies/` (índice, `/en/species/`) y `/especies/<clave>/`, `/paquetes-de-pesca/<destino>/` (`/pt/pacotes-de-pesca/`, `/en/fishing-packages/`, solo con paquetes publicados), `/guias-de-pesca/<destino>/` (`/pt/guias-de-pesca/`, `/en/fishing-guides/`, página por ciudad con todas las fichas públicas del destino; solo existe en un idioma si la guía existe en ese idioma y hay ≥1 ficha pública; 1/11/2026), `/calendario-de-pesca/` (`/pt/calendario-de-pesca/`, `/en/fishing-calendar/`: calendario y vedas de todos los destinos, agregado de las filas `temporada` y el `reglamento_resumen` de cada guía, FAQPage; 1/11/2026), `/pedir-presupuesto-pesca/` (`/pt/pedir-orcamento-pesca/`, `/en/get-fishing-lodge-quotes/`: página-respuesta «Pedir presupuesto a varios lodges de pesca a la vez», vista `vistas/presupuesto.php`, textos `pres.*`, destinos y FAQ calculados en `inc/presupuesto.php` (visible = FAQPage = `llms.txt`); enlazada desde el formulario, la respuesta corta de las guías por ciudad, `llms.txt` y el sitemap; 20/11/2026), `/para-guias-y-lodges/` (`/pt/para-guias-e-lodges/`, `/en/for-guides-and-lodges/`: qué damos y qué pedimos a los prestadores, formulario `prestador`; 1/11/2026), `/<region>/<provincia>/` (hub de provincia, ej. `/litoral/corrientes/`, `/pt/…`, `/en/…`: destinos de la provincia con guía, guías por ciudad, paquetes, altura del río y calendario; solo existe si la provincia tiene ≥1 destino publicado; 1/11/2026), `/mapa/` (`/en/map/`), **fechas de temporada** (4/11/2026): campos `temporada_abre`/`temporada_cierra`/`pico_meses`/`pico_quien` (comunes, en la guía en español) y `temporada_nota` (por idioma); `inc/temporada.php` calcula la barra de meses y la pregunta "¿Se puede pescar ahora?",
  `/terminos/` (`/pt/termos/`, `/en/terms/`) y `/privacidad/` (`/pt/privacidade/`, `/en/privacy/`): texto en `data/legal.json`, links en el pie y bajo los formularios (26/10/2026; los lee un abogado antes de darlos por definitivos).
  `/creditos/` (`/en/credits/`) solo redirige (301) al inicio: la página de créditos se sacó el 26/10/2026.
- **Completitud, no bloqueos** (`inc/reglas.php`, decisión del 20/10/2026):
  toda ficha publicada en WordPress es pública e indexable, sin carteles.
  El formulario muestra "Para completar la ficha" (fuente oficial, notas PENDIENTE, foto con
  autor, versión en español) solo para los editores. Las notas `PENDIENTE:`
  (párrafos, citas, filas de calendario/FAQ/aguas) nunca se muestran en la
  web. La fecha "Reglamento verificado el…" aparece solo cuando existe.
- **SEO sin plugin** (`inc/seo.php`): título, descripción, canonical,
  hreflang, Open Graph, JSON-LD y sitemap de WordPress (todas las fichas).
- **Fotos** (`inc/fotos.php`): WordPress genera tamaños y srcset; salen en
  WebP. Crédito en la propia foto (biblioteca de medios → "Autor de la
  foto"), visible sobre la imagen (autor y licencia enlazada). Ya no hay página de créditos aparte ni aviso de recorte (decisión del dueño, 26/10/2026).
- **Mapa del hero**: contorno real precalculado (`data/hero.json`, generado
  por `wordpress/build/generar-datos.mjs`); PHP ubica los puntos con la
  misma proyección.
- **Estilos**: `assets/pescamapa.css` salió de los componentes Astro
  (`wordpress/build/generar-css.mjs`); lo nuevo y los ajustes van a mano en
  `assets/extra.css` (se carga después). Ahí está, por ejemplo, el cambio
  de lugar de las tarjetas del hero (la de Goya junto a su pin, "¿Qué querés
  pescar?" abajo, para que trucha/pejerrey no parezcan de Goya).
- **Idioma del navegador**: nunca redirige (Google rastrea sin idioma;
  hreflang hace el trabajo). Si el navegador está en otro de nuestros
  idiomas y la página tiene esa versión, aparece una píldora "Esta página en
  português →" (`footer.php`), descartable y recordada en `localStorage`.

## Entrevistas, prestadores y consultas (20/10/2026)

Decisiones del dueño: las **entrevistas** a guías y pescadores locales son
la forma de generar contenido propio que Google y las IA citen; **todas las
consultas llegan a Pescamapa** (nunca contacto directo al prestador), para
centralizar los datos y poder cobrar comisión por el negocio que traemos.

- **Entrevistas** (`inc/entrevistas.php`, `vistas/entrevista(s).php`):
  `/entrevistas/<slug>/` (`/pt/entrevistas/`, `/en/interviews/`) y un
  índice `/entrevistas/`. Mismo modelo de idiomas que las guías (persona,
  destino, fecha, permiso y foto solo en español). El cuerpo es la charla;
  las **citas** (repetidor con tema) aparecen además en la guía del destino,
  sección "Lo que dicen los que pescan ahí". **Sin la marca "Aceptó que
  publiquemos…" no hay página (404), ni citas, ni sitemap.** JSON-LD:
  Article (about/mentions = la persona) + BreadcrumbList. Guía para hacer
  entrevistas: `docs/entrevistas.md`.
- **Prestadores** (`inc/prestadores.php`, `parciales/prestador.php`): ficha
  central e interna con lo que los guías locales dijeron que importa
  (modalidad, lancha y equipo, qué incluye, temporada, precio con fecha,
  fotos, referencias) + contacto, estado de la relación y acuerdo comercial.
  **El contacto nunca se imprime.** Es pública solo con "Aceptó aparecer" y
  sin la marca **"Datos de ejemplo"**: sale como tarjeta en la guía de su
  destino (lancha, motor, capacidad, equipo, qué incluye, precio fechado) con
  botón "Consultar disponibilidad", que precarga el formulario. **Los
  editores logueados ven también las no públicas**, en la misma guía, como
  tarjetas punteadas "Vista previa" con enlace a editarlas: así se completa
  la sección en su lugar. `prestadores-2026-10-20` cargó Goya y Esquina con
  datos de ejemplo (qué es real y qué no, en "Notas internas" de cada una). Etiquetas de opciones en 3 idiomas:
  `data/ui.json` → `opciones`.
- **Consultas** (`inc/consultas.php`, `parciales/consulta.php`): formulario
  "Pedí tu cotización" en cada guía y "Quiero sumarme" para prestadores en
  el inicio. Se guardan como posts privados (wp-admin → Consultas, con
  contador de nuevas, enlace a WhatsApp, estado, "derivada a" y notas) y
  mandan un email a la opción `pm_email_consultas` (o al email del admin).
  **El email depende de que el servidor envíe correo; el registro en
  wp-admin es la fuente de verdad.** Antispam: campo trampa, tiempo mínimo
  y límite por IP.
- **Guía "Cómo elegir guía"**: lista fija en cada destino (textos
  `ficha.elegir.*`), armada con las respuestas de Agus y otro operador.
- **Altura del río**: campo `alturas_url` en la guía (versión en español),
  enlace junto a la fecha de actualización.

## Notas, "Más para hacer", llms.txt y marca (21/10/2026)

- **Notas** (`inc/notas.php`, `vistas/nota(s).php`): artículos propios en
  `/notas/<slug>/` (`/pt/notas/`, `/en/notes/`). Mismo modelo de idiomas que
  las guías. Cada nota lleva **fuentes públicas** (salen en la página y como
  `citation` en el JSON-LD) y un campo interno "Material de origen". Los
  "Datos clave" van en un cuadro arriba del texto. El menú y el sitemap
  muestran el índice solo cuando hay al menos una nota.
- **Más para hacer** (campo `mas` de la guía, pestaña propia): lista de
  enlaces (nombre, enlace, una línea) al final de cada guía. "Es publicidad"
  agrega el sello y `rel="sponsored"`. Se carga por idioma (la descripción se
  traduce). Goya: Bitácora de Viajes (`goya-mas-2026-10-21`).
- **`/llms.txt`** se genera desde los datos (guías, especies, entrevistas con
  permiso, notas) en `inc/maquinas.php`; no hay archivo estático. **`/robots.txt`**
  es el virtual de WordPress (con el `Sitemap:` de core) más un `Disallow` para
  las URL `?consulta=`. **No crear un `robots.txt` físico en la carpeta del
  sitio**: taparía el virtual. `verify-live.yml` revisa los dos.
- **Marca**: `docs/marca.md` + `wordpress/build/revisar-marca.mjs` (en
  `test-wp.yml`). Las citas dentro del texto ya no usan el amarillo de aviso.
- **Reseñas (preparación)**: al marcar una consulta como "concretada" se
  guarda la fecha (`concretada_el`). Diseño completo en `docs/resenas.md`.

**Regla del formulario (21/10/2026):** el código que corre mientras se dibuja el
formulario de edición (el cuadro "Para completar la ficha" y todo lo que llama
`pm_sugerencias()`) **no puede usar `get_field()`/`pm_campo()` con
repetidores**: deja en caché ACF la copia "formateada" y el formulario dibuja
el repetidor en blanco; al guardar se pisan los datos reales. Pasó con las
Fuentes de Goya (18 filas vacías, restauradas con `restaurar-fuentes-2026-10-21`).
Ahí se lee con `get_post_meta()` (`pm_meta_cruda`, `pm_fuentes_crudas`).
`test-wp.yml` guarda una guía como editora y verifica que sus fuentes
sigan iguales.

## Calculadora de licencia de pesca (23/11/2026)

Herramienta gratuita e indexable, ruta fija en los tres idiomas: `/calculadora-licencia-de-pesca/`, `/pt/calculadora-licenca-de-pesca/`, `/en/fishing-licence-calculator/` (segmento `licencia` en `PM_SEGMENTOS`, `pm_url_licencia()`, regla en `inc/rutas.php`, hreflang + FAQPage + WebApplication + BreadcrumbList en `inc/seo.php`, sitemap de vistas, línea en `llms.txt`). Vista `vistas/licencia.php`, lógica en `inc/licencia.php`, textos `lic.*` (y `nav.licencia`, `ficha.licencia.calc`) en `data/ui.json`.

- **Cómo funciona:** formulario GET (sin JavaScript, URL compartible: `?donde=region:patagonia|destino:<clave>&quien=<categoría>&dias=1..30|temporada&extra[]=trolling`). El resultado solo aparece si vienen `donde` y `quien` válidos; el enlace desde una guía (`?donde=destino:<clave>`) muestra el formulario con el destino elegido. Toda entrada pasa por lista blanca (`pm_licencia_entrada()`); una URL con parámetros lleva `rel=canonical` a la página sin ellos (lo hace `wp_head`, que corta el `?`). Un script chico completa la experiencia: muestra solo las categorías y adicionales de la región elegida y recalcula sin recargar pidiendo al servidor la misma URL y cambiando la caja `#resultado` (no duplica el cálculo en JS).
- **Dónde viven los datos:** `wordpress/plugins/pescamapa-core/data/licencias.json`, **la única copia**. Lo escribe `wordpress/build/licencias-2026-27/generar-datos.py` (`python3 -I wordpress/build/licencias-2026-27/generar-datos.py`; también deja `licencias.csv` junto al script, y `graficos.py` lee el JSON del plugin). Las columnas de texto (región, categoría, quién, período, valor, unidad, vigencia, fuente, enlace, fecha de lectura) son las de la nota; el generador agrega las claves de máquina (`region_clave`, `quien_clave`, `dias`, `tipo` = permiso/extra/gratis/sin_valor, `extra_clave`, `aplica_a`, `solo_en`) derivándolas del texto, y un bloque `donde` con los organismos y enlaces donde se saca cada licencia.
- **Cómo se actualizan los valores la próxima temporada:** (1) editar los valores y fechas de lectura en `generar-datos.py` (cada valor con su fuente oficial; los valores del dataset no se estiman ni se convierten: las aproximaciones son solo la capa de presentación de «Cotizaciones de referencia», abajo); (2) correr el generador y commitear el JSON; (3) actualizar en `ui.json` los textos que repiten datos: `lic.vig.<región>` (vigencia en es/pt/en; un paso de `test-wp.yml` falla si el español no coincide con el dato), `lic.como.<región>` (cómo se saca), `lic.como.4` (qué falta), `lic.res.litros` y las notas `lic.nota.*`; (4) actualizar los importes esperados en el paso «Licence calculator» de `test-wp.yml`. Las respuestas de la FAQ toman los importes del JSON, no hay que tocarlas.
- **Regla de la combinación más barata** (`pm_licencia_cubrir()`, función pura sin llamadas a WordPress, probada con `wp eval`): con los permisos de N días que publica la fuente (cualquier cantidad de cada uno) se busca por programación dinámica el costo mínimo de cubrir los días elegidos, con desempate por menos permisos; la temporada cubre todo y compite como una opción más; el permiso «fin de semana» solo cuenta para exactamente 2 días. Se muestran todas las opciones (la mejor, cada permiso repetido las veces que haga falta, la temporada) ordenadas por precio, con la aritmética en una línea. Ejemplo, extranjero en la Patagonia, 12 días (1 día $ 50.000; 10 días $ 140.000; temporada $ 280.000): 10 días + 2 × 1 día = $ 240.000; 2 × 10 días y la temporada = $ 280.000; 12 × 1 día = $ 600.000. Gana la combinación. A 13 días gana la temporada ($ 290.000 la combinación). Los adicionales (trolling, zonas preferenciales de Tierra del Fuego) se calculan con su propia tabla de períodos y se suman. Se asume que los días de pesca caen dentro del período de cada permiso: la página lo dice.
- **Reglas del dueño que respeta:** los valores del dataset son los publicados (el cálculo trabaja en pesos, litros y centavos y no cambia). **Desde el 23/11/2026 el RESULTADO muestra aproximaciones, por decisión del dueño (revierte el «nunca convertir» para estas dos herramientas)**: el total y las opciones en US$ aproximados y Corrientes en pesos aproximados (ver «Cotizaciones de referencia»); toda aproximación va rotulada «≈», con fecha y fuente, y desaparece sola cuando el valor de referencia vence. La tabla de todas las regiones sigue con lo publicado (pesos; Corrientes en litros) más una línea con el dólar. Lo que la fuente no da (Buenos Aires adultos, residentes de Corrientes, Santa Fe) se dice así, con el enlace oficial; siempre la línea «Los valores cambian cada temporada: confirmá en el enlace oficial antes de pagar.»; fecha de lectura (`consultada`) en cada resultado; montos con `pm_paquete_precio_txt()`.
- **Enlaces de entrada:** pie de página (`nav.licencia`) y, en la ficha de cada destino cuya provincia está en el dataset (`PM_LICENCIA_PROVINCIAS`), una línea dentro del bloque «Reglamento» (`vistas/destino.php`, `?donde=destino:<clave>#calculadora`). No se tocó contenido guardado. Dos botones al formulario del viaje como máximo (tras el resultado y al final; con el destino elegido va `?destino=<clave>`), cada uno con `pm_cta_nota()`.
- **Ver también:** la nota de datos «Licencia de pesca en Argentina 2026–27» (`wordpress/scripts/nota-licencias-2026-27-2026-11-20.php`, sin publicar) usa el mismo dataset.
- **Segunda ronda (23/11/2026):** la línea antes del botón depende del contexto (`lic.cta.res.destino`: con un destino elegido, «falta elegir con quién pescar en <destino>»; sin destino, `lic.cta.res`); el cierre enlaza al estimador de costo (`lic.cta.viaje`); la tabla «Los valores de todas las regiones» usa la clase `tabla-apilada` y cada celda lleva `data-label`, así que a 720 px o menos cada fila pasa a ser una tarjeta en vez de una tabla cortada (la misma regla sirve al estimador).

## Cotizaciones de referencia: dólar oficial y nafta de Corrientes (23/11/2026)

**Decisión del dueño (23/11/2026):** la calculadora de licencia y el estimador muestran conversiones **aproximadas**, siempre rotuladas, con fecha y con fuente. Revierte la regla anterior de «no convertir» solo para estas dos herramientas. Motivos (feedback del dueño sobre la calculadora en vivo): para Corrientes el titular «24 litros de nafta súper» no le sirve a un visitante, y «$ 140.000» sin moneda no dice si son pesos argentinos.

- **La opción `pm_cotizaciones`** (autoload sí; lee `inc/cotizaciones.php`, escribe `wordpress/scripts/cotizaciones-guardar.php`): `dolar` = `valor` (pesos por US$), `nombre`, `fuente_url`, `fecha` (la del valor), `capturado` (la de la lectura); `nafta` = `precio` (pesos por litro), `producto`, `bandera`, `localidad`, `estaciones`, `fuente_url`, `fecha`, `capturado`. Sin la opción, o con un valor roto o vencido, las herramientas muestran lo que publican las fuentes, como antes (sin «≈ US$», Corrientes en litros con una línea en lenguaje llano); nunca se inventa un valor.
- **Quién la llena:** workflow `cotizaciones.yml` (lunes 12:41 UTC = 09:41 en Argentina, `workflow_dispatch` con `aplicar`, `timeout-minutes: 6`, mismo SSH que `rio.yml`) → `wordpress/build/cotizaciones-traer.py` (biblioteca estándar, `--test` sin red) → `cotizaciones-guardar.php` (simulacro por defecto; el workflow lo pasa a aplicar; valida con `pm_cot_validar()`, la misma función con la que lee el sitio; un bloque inválido o más viejo que el guardado **no pisa** al anterior; falla solo si no hubo ningún bloque válido; imprime `VERIFIED`). Cada lectura imprime valores, fechas y fuentes: nada personal.
- **Dólar:** cotización oficial del dólar estadounidense del BCRA, API de Estadísticas Cambiarias `https://api.bcra.gob.ar/estadisticascambiarias/v1.0/Cotizaciones/USD` (alcanzable desde Actions el 23/11/2026: 1.580 del 20/11/2026; las APIs `estadisticas/v2.0` y `v3.0/monetarias` responden 410 Gone). Banco Nación (`bna.com.ar/Personas`, minorista comprador 1.550 / vendedor 1.600 ese día) también responde, y es el respaldo si el BCRA deja de andar; no hay que usar el blue ni agregadores. Texto visible: «Dólar oficial (BCRA) del <fecha>: $ X por US$. Valor aproximado; lo actualizamos cada semana.»
- **Nafta de Corrientes:** Secretaría de Energía, datos abiertos, «Precios vigentes en surtidor, Resolución 314/2016» (CSV `…/resource/80ac25de-…/download/precios-en-surtidor-resolucin-3142016.csv`; se lee en flujo y se filtra: provincia y localidad CORRIENTES, producto «Nafta (súper) entre 92 y 95 Ron», horario diurno). **La Disposición 1144/2024 no nombra marca ni lugar** (dice «litros de nafta súper» y el portal muestra el importe vigente), así que se usa YPF en Corrientes capital: **mediana** del precio declarado por sus estaciones, con la fecha de vigencia más reciente entre ellas y sin contar estaciones con precio más viejo que 60 días respecto de la más nueva; sin estaciones YPF, la mediana de todas las banderas y el texto no nombra marca. **Problema (23/11/2026): `datos.energia.gob.ar` (168.101.26.134) no responde desde GitHub Actions (el TCP agota el tiempo; tampoco `energia.gob.ar` ni `res1104.se.gob.ar`).** El catálogo `datos.gob.ar` sí responde pero solo lista los enlaces al mismo servidor; `infra.datos.gob.ar` no tiene copia; YPF no publica precios por localidad. Hasta que ese servidor responda, el paso de nafta falla con aviso, el sitio conserva lo guardado y, vencido, muestra Corrientes en litros. **Plan B:** correr el workflow con `nafta_precio` y `nafta_fecha` (copiados de la fuente oficial por una persona; queda marcado «cargado a mano»); vale 45 días. El precio de la nota de Brasil ($ 2.177, promedio de 14 estaciones de Corrientes, Secretaría de Energía, 21/10/2026) vence para el sitio el 5/12/2026.
- **Vencimiento:** el dólar se usa hasta 21 días después de **su** fecha, la nafta hasta 45 (`PM_COT_DOLAR_DIAS`, `PM_COT_NAFTA_DIAS`; una fecha de mañana pasa por desfase de reloj, de pasado mañana no). Pasado el plazo se deja de mostrar solo esa aproximación.
- **Redondeo:** dólares enteros, mitad hacia arriba (140.000 / 1.450 = 96,55 → «≈ US$ 97»; en un rango se redondea cada extremo); nafta: litros × precio redondeado a los 100 pesos más cercanos (24 × 2.177 = 52.248 → «≈ ARS $ 52.200»; 24 × 2.000 = «≈ ARS $ 48.000»), y los US$ de Corrientes salen de esos pesos redondeados. La función de cálculo (`pm_licencia_calcular()`) sigue en pesos/litros/centavos; las aproximaciones son una capa de presentación (`pm_licencia_monto/usd/titular/plan_txt`, `pm_estimador_usd_rango`), con pruebas puras en `test-wp.yml`.
- **Una convención de dinero por idioma, en las dos herramientas:** es «ARS $ 140.000», pt «ARS 140.000», en «ARS 140,000»; dólares «US$ 97» (separadores del idioma); con centavos solo si los tiene («ARS $ 24.251,60»).
- **Cómo se ve:** calculadora: titular «ARS $ 140.000 ≈ US$ 97»; opciones «10 días = ARS $ 140.000 (≈ US$ 97)»; la línea del dólar bajo el resultado; la tabla de regiones solo en pesos publicados + la línea del dólar. Corrientes: titular «≈ ARS $ 48.000 ≈ US$ 33», debajo «Corrientes fija la licencia en litros de nafta súper: 24 litros × $ 2.000 el litro (precio en surtidor de YPF en Corrientes capital, Secretaría de Energía, <fecha>). El portal oficial calcula el importe final al comprar.» y opciones «3 días = ≈ ARS $ 48.000 (24 litros · ≈ US$ 33)». Sin precio de la nafta: titular «24 litros de nafta súper» con «Corrientes cobra la licencia según el precio de la nafta: son 24 litros de nafta súper. El portal oficial te dice el importe en pesos al comprar.» primero. Estimador: debajo del titular de una serie en pesos, «≈ US$ …» (rango o valor), en las listas también en la cuenta de cada precio, y una sola línea del dólar al final del resultado; **a los precios en dólares no se les suman pesos** (no se hizo: un lodge no cobra su precio en dólares al tipo oficial).
- **Estimador, titular de la zona (23/11/2026):** cuando el destino elegido es uno de varios de su zona (Río Gallegos → Patagonia sur; Itatí → Corrientes) el titular nombra la zona de la que sale el rango: «en lodges de la Patagonia sur (zona de Río Gallegos)», y la línea «Sale de N precios…» agrega «de la zona»; con un solo destino en la zona (Bariloche, El Calafate) o con la zona elegida directamente no cambia (`pm_estimador_donde_txt()`).
- **Cómo sembrar la opción después de mergear:** Actions → *Reference values (dollar and fuel)* → Run workflow (`aplicar` = true); para el dólar alcanza solo; para la nafta, mientras el servidor de Energía no responda, cargar `nafta_precio` y `nafta_fecha`. Verificar con el `VERIFIED` del log y mirando la calculadora (`?donde=region:patagonia&quien=extranjero&dias=3`).

## Estimador de costo de un viaje de pesca (23/11/2026)

Herramienta gratuita e indexable (pedido del dueño: «el estimador de costes es lo más útil seguramente y deberá tener la llamada comercial»): `/cuanto-cuesta-viaje-de-pesca/`, `/pt/quanto-custa-viagem-de-pesca/`, `/en/fishing-trip-cost-estimator/` (segmento `estimador` en `PM_SEGMENTOS`, `pm_url_estimador()`, regla en `inc/rutas.php`, hreflang + FAQPage + WebApplication + BreadcrumbList en `inc/seo.php`, sitemap de vistas, línea en `llms.txt`, enlace en el pie junto a la calculadora de licencia). Vista `vistas/estimador.php`, lógica en `inc/precios.php`, textos `est.*` (y `nav.estimador`, `est.guias.*`) en `data/ui.json`. Mismo aspecto que la calculadora (clase `v-licencia`, reglas `lic-*`; lo propio son las `est-*` al final de `extra.css`). La ruta no choca con las notas de costo (`/notas/cuanto-cuesta-viaje-de-pesca-argentina-2026-27/`, otro prefijo), pero las dos responden a la misma consulta: la herramienta es la página para entrar, la nota es el contexto.

- **Qué hace:** formulario GET (sin JavaScript, URL compartible: `?donde=zona:<clave>|destino:<clave>&formato=dia|paquete|semana&pescadores=1..8&acompanantes=0..4&dias=1..14&semanas=1..4`). Resultado solo con `donde` y `formato` válidos; el enlace desde una guía (`?donde=destino:<clave>`) muestra el formulario con el destino elegido. Toda entrada pasa por lista blanca (`pm_estimador_entrada()` y `pm_estimador_calcular()`): zona desconocida → la primera con datos; formato que la zona no tiene → su primer formato, avisado; pescadores y acompañantes acotados; días o semanas dentro del rango del formato (día 1–14, paquete 2–5, semana 1–4), avisado. Una URL con parámetros lleva `rel=canonical` a la página sin ellos. Un script chico muestra solo los formatos de la zona y el campo de días o de semanas que corresponde, y recalcula pidiendo al servidor la misma URL.
- **Dónde viven los datos:** `wordpress/plugins/pescamapa-core/data/precios.json`, **la única copia**. Lo escribe `wordpress/build/precios-2026-11/generar.py` (`python3 -I wordpress/build/precios-2026-11/generar.py`; el mismo dataset de la nota de precios del 20/11, que sigue sin publicar) y lo revisa `verificar.py` (sin nombres, sin las filas excluidas, cada serie igual a `resumen.json["herramienta"]`, destinos mapeados a zonas que existen; corre en Test WordPress). **Cómo refrescarlo:** agregar o corregir filas `R(...)` en `generar.py` con su fecha de lectura, correr los dos scripts, commitear `precios.json`, `precios.csv`, `resumen.json` y `data/precios.json`, y actualizar las cifras esperadas del bloque «Trip cost estimator» de `test-wp.yml`. Una zona nueva se suma a `ZONAS` (y a las claves `est.zona.<clave>` y `est.zona.<clave>.corto` de `ui.json`).
- **Qué entra y qué no (decidido el 23/11/2026):** solo precios publicados y fechados, en USD o ARS (los precios no se convierten: los pesos van en su propia serie y, desde el 23/11, con un «≈ US$» aproximado al lado; los dólares no reciben pesos, porque un lodge no cobra su precio en dólares al tipo oficial), que dicen por cuántas personas son y cuántos días de pesca cubren; sin euros ni reales, medios días ni tarifas confidenciales. **Excluidos del estimador:** Hambaré (`ESQ-B`: su portada ya no mostraba tarifas el 19/11) y Uguay (`COR-C`: precio de la página de una agencia, marcado «a revisar»). La nota del 20/11 todavía los cuenta (sus cifras de Corrientes y de Esquina son otras: regenerar la nota antes de publicarla). Sin nombres de lugares ni de plataformas en ningún lado (el JSON solo lleva el código del lugar para contar oferentes). Nunca «la cifra de 13 de 58 fichas».
- **Reglas del cálculo** (`pm_estimador_serie()`, función pura probada con `wp eval`): un **rango** solo cuando los precios vienen de 3 lugares o más; con menos, **cada precio publicado por separado** con su total. Día con guía: precios por bote para 1 o 2 pescadores → botes = ceil(pescadores / 2) × días × rango. Lodge por semana: pescadores × semanas × rango por persona (el tope de un precio «según la fecha» cuenta). Paquete corto: precio del paquete ÷ sus días de pesca (redondeado al dólar) × pescadores × días; en modo lista el paquete es de duración fija y los días no cambian el total. «Por pescador y por día de pesca» sale del mismo rango. El acompañante que no pesca no suma: ninguna fuente publica tarifa, y el resultado dice que se consulta. Los valores unitarios que se multiplican son los mostrados, así que la cuenta de una línea se verifica a mano. Ejemplo (día con guía, Bariloche, 3 pescadores, 3 días; 7 precios de 5 lugares, US$ 520–690 por bote): 2 botes × 3 días × US$ 520–690 = **US$ 3.120–4.140**, equivale a US$ 347–460 por pescador y por día. Semana (Corrientes, 2 pescadores, 1 semana; 6 precios de 6 lugares, US$ 7.000–7.950): 2 × 1 × 7.000–7.950 = US$ 14.000–15.900. Paquete (Corrientes, 2 pescadores, 4 días; 17 precios de 6 lugares, 1.250–2.025 por día de pesca): 2 × 4 × 1.250–2.025 = US$ 10.000–16.200.
- **Qué suele incluir:** se cuenta de `resumen.json` (`incluye_dia_bariloche`, `incluye_semana`, copiados a `data/precios.json`): «equipo en 5 de 5», «alojamiento en 7 de 7». Esos recuentos son de las fichas leídas el 5–13/11 y no se rehicieron al excluir Uguay; los otros formatos dicen que cambia de un lugar a otro.
- **La llamada comercial:** justo después del resultado, «Pedir opciones para este viaje →» (con `pm_cta_nota()`), más un cierre al final de la página. El enlace lleva a `/armar-viaje/` lo elegido: `?destino=<clave>` (el elegido, o el único destino de la zona cuando hay uno: Bariloche, El Calafate), `pescadores`, `acompanantes` (solo si es mayor que 0), `estadia` (días de pesca → `uno`, `dos-tres`, `cuatro-cinco`, `semana`, `mas`; una semana cuenta 6) y `presupuesto` (la ficha de US$ por pescador y por día que contiene el punto medio del rango: `hasta-300`, `300-600`, `600-1000`, `mas-1000`; nada si el estimado es en pesos). **Precarga del formulario:** `vistas/viaje.php` lee esos cuatro parámetros por GET con lista blanca (números dentro de los límites del formulario, claves que existen en `opciones`); lo que vuelve de un error de validación gana a la URL (`pm_campos_personas()` recibe los valores iniciales). Con una zona sin destino único (Corrientes, Patagonia) no viaja el destino.
- **Enlaces de entrada:** pie de página (`nav.estimador`), el cierre de la calculadora de licencia («¿Y cuánto cuesta el viaje?») y, en la página «Guías y precios» de cada destino que el dataset mapea a una zona (`vistas/guias.php`, una línea en la respuesta corta), `?donde=destino:<clave>#calculadora`. Hoy mapean Bariloche, El Calafate, Itatí, Esteros del Iberá, Junín de los Andes, Esquel y Trevelin, Jurassic Lake, Río Gallegos y Río Grande (los destinos que existan como guía); Esquina no tiene precios usables. No se tocó contenido guardado. Desde el resultado, la licencia se enlaza con la región preseleccionada cuando es inequívoca (Patagonia norte y Bariloche → Patagonia; El Calafate → Santa Cruz; Corrientes; la Patagonia sur y el Delta, varias provincias → calculadora sin región).
- **No se verificó:** el renderizado real (lo corre Test WordPress sobre la rama), las capturas a 390 px, ni la carga de la herramienta en navegadores reales; las cifras de «qué suele incluir» tras excluir Uguay; cómo se posiciona frente a la nota de costo.

## Panel del editor (rol "Editor de Pescamapa")

`inc/editor.php`: el editor ve solo las fichas en español, sin campos de
idioma/clave, con bloques simples y un menú corto (Destinos, Especies,
Próximos, Entrevistas, Prestadores, Consultas, Fotos). Cuando edita en
español, la columna "Traducciones" (solo admins) marca pt/en como
desactualizadas; las propagamos con un script.

## Antes de pushear y Actions sin minutos (17/11/2026)

- **Antes de pushear, correr `bash wordpress/build/chequeo-local.sh`** (gratis, ~segundos): `php -l` sobre todo `wordpress/**/*.php`, `revisar-marca.mjs`, `node --check` de los `.mjs` y YAML de los workflows. No instala WordPress: no prueba renderizado ni el formulario.
- **`Test WordPress` (`test-wp.yml`) corre solo por `workflow_dispatch`**, sobre la rama, y **es obligatorio antes de mergear todo cambio de plugin o tema** (regla del dueño, 19/11/2026: se mergea solo con test verde sobre el mismo head SHA). No hace falta para scripts de contenido (`wordpress/scripts/*`).
- **`wp-script.yml`**: input `aplicar` (`true` = aplicar; vacío o `false` = simulacro). Con `true` el workflow pone `$aplicar = true;` en una copia del script antes de mandarlo al servidor (`sed 's/^\$aplicar = false;/$aplicar = true;/'`), así no se commitea el cambio. Un script que usa `$aplicar` debe tener esa línea tal cual, al comienzo de la línea. Si existe `wordpress/scripts/<script>-urls.txt`, viaja a `/tmp/pm-script-urls.txt` (lo usa `leer-vivo`). Un solo run a la vez (`concurrency`), `timeout-minutes: 10`.
- **`leer-vivo`** (solo lectura): lee las URLs de `wordpress/scripts/leer-vivo-urls.txt` desde el servidor y muestra status, title, description, nº de h1, tipos de JSON-LD y presente/ausente de las cadenas pedidas. Es el reemplazo barato de `investigar.yml`. **Sintaxis extendida (17/11/2026):** `url | cadena ; cadena || contexto: palabra ; palabra` (el `||` y el `contexto:` son opcionales; las líneas viejas siguen igual): por palabra imprime hasta 3 extractos de ±250 caracteres de texto visible (sin mayúsculas ni tildes), la fecha de publicación si la encuentra (meta, `<time>`, JSON-LD, dd/mm/aaaa) y los 600 primeros caracteres de `<main>`/`<article>`; máximo 1,5 MB por página, timeout 15 s.

## Errores de PHP: no se muestran, se registran (19/11/2026)

- **Qué pasó:** el servidor (aaPanel, PHP 8.3) tiene `display_errors = On` en `php.ini`, y `WP_DEBUG=false` no lo apaga; un aviso («Undefined array key», `parciales/contraste.php`) salió en la portada con rutas internas. Diagnóstico (script `diagnostico-errores-php-2026-11-19`, run 44497187843): `WP_DEBUG=false`, `WP_DEBUG_DISPLAY=true`, `WP_DEBUG_LOG=false`; `php.ini` del 8.3 con `display_errors=On`, `log_errors=On`, **sin `error_log`**; no existía `wp-content/debug.log`; `/www/wwwlogs` (fuera de la web) escribible por el usuario `www`; `dirname(ABSPATH)` no escribible.
- **Qué hace el plugin** (`pescamapa-core.php`, al principio, solo peticiones web, no WP-CLI): `display_errors=0`, `log_errors=1`, `error_reporting(E_ALL)` (WordPress, con `WP_DEBUG=false`, deja afuera notices y deprecations; aquí se registran sin mostrarse) y `error_log = wp-content/pm-logs/errores-php.php` (`PM_ERROR_LOG`). Si alguien activa `WP_DEBUG_LOG`, se respeta. **No se tocó `wp-config.php` ni la configuración del servidor.** Límite: lo que ocurre antes de cargar el plugin (wp-config, núcleo temprano) sigue la configuración del servidor. En WP-CLI (wp-script, deploy) los errores siguen yendo a la salida, a propósito.
- **Por qué `wp-content/pm-logs/` y no `/www/wwwlogs` (20/11/2026):** la primera versión apuntaba a `/www/wwwlogs/pescamapa-php-errors.log`. La autoprueba del 19/11 (run 44559414292) mostró que el pool web (php-fpm, usuario `www`) **no ve** ese directorio (`dir_existe=false`, aislamiento/open_basedir de aaPanel): `display_errors=0` sí funcionaba, pero los avisos iban al stderr de FPM y el paso del deploy y `leer-vivo` leían un archivo al que la web nunca escribía (decían «sin errores» siempre). Ahora el registro es `wp-content/pm-logs/errores-php.php`, un `.php` cuyo **primer renglón es `<?php exit; ?>`**: si alguien lo pide por la web, PHP lo ejecuta y sale sin mostrar nada. El directorio lleva un `index.php` casi vacío. No se usa `uploads`. El plugin crea el archivo con esa cabecera si no existe (web o WP-CLI) y, si no puede crearlo o escribirlo, deja la opción **`pm_registro_estado`** en «falla: motivo» (en vez de callarse); la leen la autoprueba y `leer-vivo`.
- **Cómo se revisa:** (1) `wp-script` con `leer-vivo`: al final imprime el estado del registro (`pm_registro_estado`) y las últimas 20 entradas de la última hora que mencionen el tema o `pescamapa-core` (sin rutas del servidor; saltea la cabecera; cuenta como error si hay alguna o si el estado no es `ok`); (2) por SSH, `tail -n 50 <WP>/wp-content/pm-logs/errores-php.php` (sin rotación: si crece, vaciarlo a mano dejando la primera línea `<?php exit; ?>`).
- **Qué falla el deploy** (`deploy-wp.yml`): el paso «Verify the served site» sigue fallando si una página muestra `<b>Warning</b>:` (etc.). Paso **«Check the PHP error log»**: antes de subir se guarda el tamaño del registro y la hora; después lee solo lo escrito desde entonces (saltando la cabecera si el offset era 0), imprime «Registro leído: N líneas…» y **falla** ante `PHP Warning|Notice|Deprecated|Fatal error|Parse error` cuya línea mencione `wp-content/themes/pescamapa/` o `wp-content/plugins/pescamapa-core/`; los de otros plugins o del core solo se informan (⚠). Paso **«Check the PHP error log is not readable from the web»**: pide `<WP_URL>/wp-content/pm-logs/errores-php.php` y falla si responde 200 con cuerpo, si el cuerpo contiene texto de log o `<?php`, o si el código no es 200/403/404.

### Autoprueba de errores de PHP (19/11/2026)

- **Para qué:** comprobar de punta a punta, sin esperar un error real, que PHP **no** muestra un aviso en la página y **sí** lo deja en `PM_ERROR_LOG`, y que el paso «Check the PHP error log» de `deploy-wp.yml` lo detectaría. Cuándo: una vez al mes y siempre que cambie el servidor (`php.ini`, aaPanel, versión de PHP, ruta del registro).
- **Cómo funciona:** el plugin (`pescamapa-core.php`, hook `init`) atiende `?pm_autoprueba=<token>` solo si el token coincide con el transient `pm_autoprueba` (lo crea el wp-script; un solo uso: se borra al usarse; vence a los 5 minutos). Entonces hace `trigger_error('Pescamapa autoprueba controlada', E_USER_WARNING)` y la página sigue normal. Sin token válido no hace nada.
- **Cómo correrla:** Run wp-script con `script = autoprueba-errores` (no necesita `aplicar`). Lee `pm_registro_estado` (debe empezar con `ok`), pide la portada con el token y comprueba: HTML sin `<b>Warning</b>` ni «autoprueba controlada»; línea nueva en el registro con la marca; esa línea cumple la expresión del deploy (`PHP Warning` + ruta `wp-content/plugins/pescamapa-core/`). Después pide la URL pública del registro (debe dar vacío, 403 o 404, nunca texto del log). Imprime `OK` o `FALLA` y la línea del registro sin rutas del servidor. Solo escribe el transient.
- **Resultado (20/11/2026, run 44561859155): OK.** Estado `ok`, aviso fuera de la página, línea «PHP Warning: Pescamapa autoprueba controlada in wp-content/plugins/pescamapa-core/…» en el registro, URL pública del registro con HTTP 200 y 0 bytes. La del 19/11 había fallado por el directorio invisible para la web (ver arriba). Trampa: `?>` dentro de un comentario `//` cierra PHP (`php -l` no lo detecta); en comentarios de una línea no escribir la cabecera del registro.
- **Ruido en el registro:** la línea queda en el registro (no hay rotación) y `leer-vivo` la ignora a propósito; los deploys posteriores solo leen lo escrito después de su marca.

## Cambio de dominio (hecho el 20/10/2026)

1. aaPanel → WP Toolkit → el sitio → agregar el dominio `pescamapa.com`.
2. Cloudflare → Workers & Pages → `pescamapa` → Domains: quitar
   `pescamapa.com`. DNS: registro `A @ → 185.47.128.48` con proxy.
3. GitHub secret `WP_URL = https://pescamapa.com` y quitar la rama de
   trabajo de `deploy-wp.yml`.
4. Correr el deploy (pone home/siteurl y `blog_public = 1`) y en el
   servidor `wp search-replace https://nuevo.pescamapa.com https://pescamapa.com`.
5. `verify-live.yml` y Search Console con el sitemap `wp-sitemap.xml`.

## Mapa del inicio (22/10/2026)

Se verificó la proyección con las mismas funciones del plugin (`pm_proyectar`): las guías, los próximos destinos y 8 ciudades de referencia (La Quiaca, Ushuaia, Buenos Aires, Mendoza, Posadas, Córdoba, Bariloche, Puerto Madryn) caen donde corresponde sobre el contorno de `data/hero.json`. El defecto que se encontró no era de posición sino de **etiquetas**: Goya, Esquina y La Paz están a 14-16 px y la etiqueta mide 22 px, así que se pisaban. `pm_mapa_hero` ahora reparte las etiquetas en vertical (hueco mínimo de 24 px) y las une al punto con una línea fina (`.guia-linea`, en `extra.css`); el punto no se mueve. Los ríos del mapa son líneas simplificadas (pocos vértices): sirven de referencia, no de cartografía.

## verify-live no comprueba lo que editan las personas (dueño, 27/10/2026)

Un chequeo en vivo no debe fallar porque alguien cambió contenido (un título, un crédito de foto, un precio). Falló el 27/10 porque Cheyenne corrigió el crédito de una foto. Regla: `verify-live.yml` comprueba lo que garantizan el código y los scripts (la página existe, la plantilla se ve, hay JSON-LD, hay un `<figcaption>` con crédito, no hay marcas PENDIENTE, no se filtran contactos), **no** textos de títulos, cajas anuales, nombres de fotógrafos ni cifras (aranceles, récords). Un dato nuevo que escribe un script se verifica en el log del propio script (`VERIFIED`), no acá. El `check()` ahora también acepta un cuarto argumento (texto que NO debe aparecer); antes se ignoraba.

## Especies en las tarjetas y «Dónde conviene ir en <mes>» (19/11/2026)

- **Línea de especies**: `pm_especies_linea( $d, 2 )` (`inc/datos.php`) devuelve las dos primeras especies de la guía (campo `especies`), con el nombre de la ficha de especie en el idioma de la página (en español si no hay traducción); sin especies publicadas no imprime nada. Se usa en el hub de provincia (`.especies-linea`) y en el detalle de los pines del mapa (portada y `/mapa/`). Las tarjetas de la portada ya llevan todas las especies como chips.
- **Bloque de la portada** (`parciales/destinos-mes.php`, después de la franja y antes del contraste): `pm_destinos_del_mes()` (`inc/temporada.php`) toma solo las guías con fechas oficiales de temporada (`temporada_abre`/`temporada_cierra`) que hoy (fecha de Argentina) están dentro de la temporada. Nivel `pico` (mejor época: `pico_meses` con `pico_quien` con nombre) antes que `abierto`; hasta 6; con menos de 2 no se imprime. Sin fuente no hay «mejor época»: la etiqueta de cada tarjeta dice quién lo afirma. Se guarda en un transient por idioma y día; `save_post` de guías y especies sube `pm_mes_ver` para invalidarlo. Textos: `inicio.mes.*` en `ui.json` (es/pt/en); nombres de mes en `pm_mes_nombre()`.
- Test WordPress: siembra fechas relativas a hoy en Esquina y Paso de la Patria (así no caduca), comprueba el bloque en los 3 idiomas, el orden, que desaparece con una sola guía en temporada, y la línea de especies en `/litoral/corrientes/`.
