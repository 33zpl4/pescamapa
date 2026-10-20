# Pescamapa en WordPress

Desde el 20/10/2026 **`pescamapa.com` es WordPress** (antes, Astro en
Cloudflare Workers). El sitio Astro (`src/`) queda en el repo como
referencia y para generar estilos/datos, pero ya no se publica: el Worker
`pescamapa` de Cloudflare no tiene dominio. `nuevo.pescamapa.com` sigue
apuntando al mismo WordPress (alias del vhost).

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
(`wp-script.yml`). Nunca pisan a ciegas lo editado en wp-admin. Corridos:
`goya-2026-10-20` (reglamento de Goya), `esquina-2026-10-20` (guía de
Esquina, solo español).

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
  `/especies/<clave>/` (`/en/species/`), `/mapa/` (`/en/map/`),
  `/creditos/` (`/en/credits/`).
- **Completitud, no bloqueos** (`inc/reglas.php`, decisión del 20/10/2026):
  toda ficha publicada en WordPress es pública e indexable, sin carteles.
  El formulario muestra "Para completar la ficha" (fuente oficial,
  reglamento verificado hace menos de un año, notas PENDIENTE, foto con
  autor, versión en español) solo para los editores. Las notas `PENDIENTE:`
  (párrafos, citas, filas de calendario/FAQ/aguas) nunca se muestran en la
  web. La fecha "Reglamento verificado el…" aparece solo cuando existe.
- **SEO sin plugin** (`inc/seo.php`): título, descripción, canonical,
  hreflang, Open Graph, JSON-LD y sitemap de WordPress (todas las fichas).
- **Fotos** (`inc/fotos.php`): WordPress genera tamaños y srcset; salen en
  WebP. Crédito en la propia foto (biblioteca de medios → "Autor de la
  foto"), visible sobre la imagen y en `/creditos/`.
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
  **El contacto nunca se imprime.** Con "Aceptó aparecer", sale como tarjeta
  en la guía de su destino con botón "Consultar disponibilidad", que
  precarga el formulario. Etiquetas de opciones en 3 idiomas:
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

## Panel del editor (rol "Editor de Pescamapa")

`inc/editor.php`: el editor ve solo las fichas en español, sin campos de
idioma/clave, con bloques simples y un menú corto (Destinos, Especies,
Próximos, Entrevistas, Prestadores, Consultas, Fotos). Cuando edita en
español, la columna "Traducciones" (solo admins) marca pt/en como
desactualizadas; las propagamos con un script.

## Cambio de dominio (hecho el 20/10/2026)

1. aaPanel → WP Toolkit → el sitio → agregar el dominio `pescamapa.com`.
2. Cloudflare → Workers & Pages → `pescamapa` → Domains: quitar
   `pescamapa.com`. DNS: registro `A @ → 185.47.128.48` con proxy.
3. GitHub secret `WP_URL = https://pescamapa.com` y quitar la rama de
   trabajo de `deploy-wp.yml`.
4. Correr el deploy (pone home/siteurl y `blog_public = 1`) y en el
   servidor `wp search-replace https://nuevo.pescamapa.com https://pescamapa.com`.
5. `verify-live.yml` y Search Console con el sitemap `wp-sitemap.xml`.
