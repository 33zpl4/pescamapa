# pescamapa — guía de sesión

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
- **Prestadores solo con consentimiento** y con fecha de verificación de
  precios y datos. Datos estructurados de negocio solo con datos confirmados.
- **Nada de "se pesca todo el año" sin matiz**: temporada = especie ×
  altura del río × reglamento.
- Las reglas de un torneo no se trasladan a la pesca recreativa.
- Copy rioplatense (voseo: encontrá, querés, consultá). Nunca "vosotros".
- Cada fuente lleva `consultada:`; las oficiales, `oficial: true`.

## URLs

`/<region>/<provincia>/<slug>/` (estables, sin fechas), `/especies/<id>/`,
`/mapa/`. `trailingSlash: 'always'`. Cambiar una URL publicada exige una
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
