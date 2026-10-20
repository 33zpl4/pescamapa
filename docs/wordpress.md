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

Diagnóstico: `probe-wp.yml` (solo lectura: versiones, opciones, log de
Apache, render desde el propio servidor). Capturas: `capturas.yml` (empuja
PNGs a la rama `capturas`).

## Modelo de datos

Igual que en Astro, ahora en WordPress:

- **Tipos**: `pm_destino` (guías), `pm_especie`, `pm_proximo`. Campos con
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
- **Estilos**: `assets/pescamapa.css` se genera desde los componentes Astro
  (`wordpress/build/generar-css.mjs`) mientras convivan los dos sitios.
  Después del cambio de dominio, se edita directamente.

## Cambio de dominio (hecho el 20/10/2026)

1. aaPanel → WP Toolkit → el sitio → agregar el dominio `pescamapa.com`.
2. Cloudflare → Workers & Pages → `pescamapa` → Domains: quitar
   `pescamapa.com`. DNS: registro `A @ → 185.47.128.48` con proxy.
3. GitHub secret `WP_URL = https://pescamapa.com` y quitar la rama de
   trabajo de `deploy-wp.yml`.
4. Correr el deploy (pone home/siteurl y `blog_public = 1`) y en el
   servidor `wp search-replace https://nuevo.pescamapa.com https://pescamapa.com`.
5. `verify-live.yml` y Search Console con el sitemap `wp-sitemap.xml`.
