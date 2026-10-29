# Investigar sitios (competencia, sitios oficiales) desde GitHub

El sandbox de Claude no llega a casi ningún sitio (red restringida); los runners de GitHub sí. El workflow **Investigar sitios**
(`.github/workflows/investigar.yml`, script `scripts/investigar.mjs`) lee páginas públicas desde un runner y empuja un informe en texto a
la rama `investigacion`, carpeta `<etiqueta>-<fecha>-<hora>/`. Desde ahí lo lee Claude.

## Cómo se usa

Actions → *Investigar sitios* → Run workflow:
- **urls**: hasta 30, una por línea o separadas por espacio.
- **etiqueta**: nombre de la carpeta (`competencia-1`).
- **sitemap** (`true`): además resume el sitemap de cada sitio (cantidad de URLs, grupos por ruta, fechas `lastmod`).
- **navegador** (`false`): `true` abre con un navegador real (Playwright) para sitios que cargan con JavaScript y saca captura de pantalla.
  Tarda más (instala Chromium).

Salida por página (`NN-host-ruta.md`): título, meta description, canonical, hreflang, datos estructurados (JSON-LD, con tipos), encabezados
H1–H3, cantidad de palabras, enlaces internos/externos y secciones más enlazadas, más el texto (primeros 12.000 caracteres) y el HTML crudo.
`INDEX.md` resume todo en una tabla.

## Reglas de cortesía (no se cambian sin hablar)

- Se identifica: `PescamapaResearchBot/1.0 (+https://pescamapa.com; …)`.
- Respeta `robots.txt`: lo que desautoriza no se lee, y el índice lo dice.
- Un pedido por sitio cada 1,5 s, 30 páginas por corrida como máximo.
- Solo lectura: nada de logins, formularios ni saltarse bloqueos. Si un sitio bloquea, no se insiste.
- Es para entender estructura, datos y estrategia. **No se copia texto ni datos de terceros** a nuestras páginas.

## Qué sacar de una competencia

Estructura de URLs y de datos (qué campos por lugar/especie), qué tan fresco está, cómo monetiza, SEO (títulos, datos estructurados, tamaño
del sitemap), y dónde podemos hacerlo mejor (fuentes y fechas visibles, reglamento verificado, datos del río). Se vuelca en
`docs/competencia.md` con una tabla "qué adoptar / dónde ganarles" (precedente: `docs/referencias-yellow-dog.md`).
