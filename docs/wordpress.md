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
  `/especies/` (índice, `/en/species/`) y `/especies/<clave>/`, `/paquetes-de-pesca/<destino>/` (`/pt/pacotes-de-pesca/`, `/en/fishing-packages/`, solo con paquetes publicados), `/guias-de-pesca/<destino>/` (`/pt/guias-de-pesca/`, `/en/fishing-guides/`, página por ciudad con todas las fichas públicas del destino; solo existe en un idioma si la guía existe en ese idioma y hay ≥1 ficha pública; 1/11/2026), `/calendario-de-pesca/` (`/pt/calendario-de-pesca/`, `/en/fishing-calendar/`: calendario y vedas de todos los destinos, agregado de las filas `temporada` y el `reglamento_resumen` de cada guía, FAQPage; 1/11/2026), `/para-guias-y-lodges/` (`/pt/para-guias-e-lodges/`, `/en/for-guides-and-lodges/`: qué damos y qué pedimos a los prestadores, formulario `prestador`; 1/11/2026), `/<region>/<provincia>/` (hub de provincia, ej. `/litoral/corrientes/`, `/pt/…`, `/en/…`: destinos de la provincia con guía, guías por ciudad, paquetes, altura del río y calendario; solo existe si la provincia tiene ≥1 destino publicado; 1/11/2026), `/mapa/` (`/en/map/`), **fechas de temporada** (4/11/2026): campos `temporada_abre`/`temporada_cierra`/`pico_meses`/`pico_quien` (comunes, en la guía en español) y `temporada_nota` (por idioma); `inc/temporada.php` calcula la barra de meses y la pregunta "¿Se puede pescar ahora?",
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
