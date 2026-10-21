# pescamapa — guía de sesión

> **Empezá por `docs/estado.md`** (dónde quedamos, decisiones del dueño,
> pendientes, mapa de workflows). Guía para quien edita: `docs/guia-editor.md`. **`pescamapa.com` es WordPress desde el 20/10/2026** (`wordpress/`). Leé
> `docs/wordpress.md` antes de tocar el plugin, el tema o el deploy. Lo que
> sigue describe el sitio Astro (`src/`), que ya no se publica pero sigue
> siendo la fuente de estilos generados. **Desde el 20/10/2026 no hay
> bloqueos de publicación ni carteles de "en construcción"**: la completitud
> es una guía interna para editores (ver `docs/wordpress.md`); las secciones
> de abajo sobre `borrador`/build que falla describen solo el sitio Astro.

**Qué es Pescamapa (una frase, 21/10/2026):** la guía para pescar en toda la
Argentina: dónde ir, cuándo, qué dice el reglamento y con qué guía local
salir, con datos al día y la voz de quienes pescan ahí. **Marca** (color,
tipografía, componentes): `docs/marca.md`, obligatoria para todo lo que se
publique; `node wordpress/build/revisar-marca.mjs` la verifica. Contenido
propio (entrevistas, notas): `docs/contenido.md`. **Regla de oro** para todo lo que
se agregue (útil para personas y para LLM: dato concreto, fechado y con fuente,
arriba, que responde una pregunta real): `docs/estrategia-contenido.md`.

Pescamapa (pescamapa.com): destinos, especies y guías para pescar en
Argentina. Sitio Astro estático, publicado en Cloudflare Workers (static
assets). Español rioplatense primero; portugués e inglés después, con su
propio árbol de URLs (/pt/, /en/) y hreflang.

## La regla que no se negocia: datos → páginas

Cada destino es UN archivo (`src/content/destinos/<x>.md`) y cada especie
otro (`src/content/especies/<id>.md`). Título, meta, migas, JSON-LD
(TouristDestination, BreadcrumbList, FAQPage), pines del mapa, tarjetas del
inicio y enlaces especie↔destino salen de esos campos. Nunca se escribe un
dato dos veces; si hace falta en otro lado, se agrega un campo al esquema
(`src/content.config.ts`).

## Editor web (Pages CMS) — histórico, del sitio Astro

`.pages.yml` define los formularios de app.pagescms.org, donde edita gente
no técnica. Guardar = commit a
`main` = Cloudflare rebuild. Consecuencias para el código:

- **Los nombres de campo de `.pages.yml` y del esquema son un contrato**:
  renombrar uno exige cambiar los dos y migrar los archivos.
- El editor guarda campos opcionales vacíos como `""` o `null`: en el
  esquema todo opcional pasa por `opcional()` / `lista()`.
- Coordenadas como `lat` / `lon` sueltos (el esquema arma `coordenadas`).
- `foto` = ruta de archivo (`src/assets/fotos/x.jpg`); se resuelve por
  nombre. Crédito: `src/data/fotos.json` (Commons) o `foto_autor` (+
  `foto_origen`: propia | cedida) para subidas. Sin crédito, el build falla
  con un mensaje para el editor.
- **Datos compartidos se cargan una vez, en español**: región, provincia,
  lat/lon, especies y foto de un destino viven solo en `destinos/es/`; pt y
  en los heredan por `clave` (`src/lib/destinos.ts` — usar siempre
  `getDestinos()`, nunca `getCollection('destinos')`). Los formularios pt/en
  no tienen esos campos. `borradores.mjs` y `verify-live.yml` replican la
  herencia para armar las URLs.
- Contenido por idioma en carpetas: `destinos/{es,pt,en}/`,
  `especies/{es,pt,en}/`. Próximos destinos: un archivo por lugar en
  `src/content/proximos/`; se ocultan solos cuando existe una guía con la
  misma `clave` que el nombre del archivo.
- Una especie nueva necesita sumarse a las opciones de `.pages.yml`
  (selector) y a `ETIQUETAS` en `src/i18n/ui.ts` si se usa en próximos.

## Confianza (la ventaja contra los portales genéricos)

Los reglamentos cambian y en este nicho el lector se cobra un error. Por eso:

1. `estado: borrador` → la página se construye con `noindex`, queda fuera del
   sitemap y muestra el banner "Ficha en construcción". Sirve para revisar
   en la vista previa.
2. `estado: publicado` **falla el build** si:
   - no hay ninguna fuente con `oficial: true`;
   - falta `reglamento.verificado` o tiene más de 365 días;
   - queda alguna marca `PENDIENTE` en el cuerpo, la temporada, las FAQs,
     las aguas o el reglamento.
3. `PENDIENTE: …` (en un blockquote del cuerpo o en un campo) marca un dato
   por verificar. Se ve en la vista previa y bloquea la publicación.
4. `reglamento.verificado` se completa solo cuando una persona contrastó el
   reglamento vigente con la autoridad (no alcanza con un informe).
5. Las FAQ con `PENDIENTE` no entran al JSON-LD.

## Reglas de contenido

- **Áreas, no puntos de pesca.** No publicar coordenadas de pesqueros ni
  presentar un sitio como habilitado sin confirmación oficial. Los pines de
  `aguas` marcan zonas generales.
- **Prestadores** (owner, 21/10/2026): se listan con consentimiento **o** como
  "listado con información pública" (marcado sin verificar, con enlace de
  corrección/baja); nunca datos de ejemplo, ni contacto, ni fotos ajenas. Precio
  solo con fecha, si no "a consultar". Datos estructurados de negocio solo con
  datos confirmados. Reglas completas: `docs/prestadores.md`.
- **Nada de "se pesca todo el año" sin matiz**: temporada = especie ×
  altura del río × reglamento.
- Las reglas de un torneo no se trasladan a la pesca recreativa.
- Copy rioplatense (voseo: encontrá, querés, consultá). Nunca "vosotros".
- Cada fuente lleva `consultada:`; las oficiales, `oficial: true`.

## Idiomas

Español (rioplatense) en la raíz, portugués de Brasil en `/pt/`, inglés en
`/en/`. Las páginas de cada idioma son envoltorios de 2 líneas sobre las
vistas compartidas de `src/vistas/`; los textos de interfaz están en
`src/i18n/ui.ts` (una clave faltante en pt/en es error de tipos).

- Una traducción es otro archivo con la misma `clave` y su `idioma`
  (`src/content/destinos/pt/goya.md`; el español en `destinos/es/`). Slug propio por idioma
  (`pesca-em-goya`, `fishing-in-goya`).
- **El español es la fuente**: una traducción no agrega ni cambia datos, y
  no puede estar `publicado` si la versión en español no lo está (falla el
  build). Las marcas internas siguen siendo `PENDIENTE:` en todo idioma.
- hreflang: `es` (todo hispanohablante), `pt-BR`, `en`, y `x-default` → es.
  El selector ES·PT·EN lleva a la misma página traducida, o al inicio del
  idioma si no existe.

## Tipografía y fotos

Tres fuentes, tres trabajos (no sumar más): **Playfair Display** para
titulares (itálica = énfasis), **Outfit** para texto e interfaz, **Bebas
Neue** para la marca y etiquetas (eyebrows, pills, numeración).

Fotos solo de Wikimedia Commons con licencias libres (CC0, PD, CC BY, CC
BY-SA), nunca de Google: `scripts/commons.py` vía el workflow `fotos.yml`
(candidatas → rama `fotos-candidatas`; elegidas en
`scripts/fotos-elegidas.json` → `src/assets/fotos/` + créditos en
`src/data/fotos.json`). El crédito se muestra sobre la foto y en
`/creditos/`; una foto sin crédito rompe el build.

## URLs

`[/pt|/en]/<region>/<provincia>/<slug>/` (estables, sin fechas),
`/especies/<clave>/` (`/pt/especies/`, `/en/species/`), `/mapa/`
(`/pt/mapa/`, `/en/map/`), `/creditos/` (`/en/credits/`). Rutas en
`src/lib/rutas.ts`; `src/lib/borradores.mjs` las replica para el sitemap. `trailingSlash: 'always'`. Cambiar una URL publicada exige una
redirección; nunca romperla.

## Mapa

Leaflet + mosaicos de OpenStreetMap con atribución. La política de uso de
OSM no admite tráfico alto: antes de crecer, pasar a un proveedor (MapTiler,
Stadia, Protomaps propio). Ver `docs/decisiones.md`.

## Deploy

Cloudflare Workers conectado al repo: cada push a la rama de producción corre
`npx wrangler deploy`, que ejecuta `npm run build` (ver `wrangler.jsonc`) y
sube `dist/`. El build ES la validación: si un dato no cumple el esquema, no
hay deploy. Correr `npm run build` localmente antes de pushear.

El sandbox de Claude no llega a OpenStreetMap ni a Cloudflare: en las
capturas locales los mosaicos no cargan, y nunca se afirma que un deploy
funcionó sin verlo en el dashboard o en el sitio.
