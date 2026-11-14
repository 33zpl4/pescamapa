# Marca — reglas que valen para todo lo que se publica

Definido el 21/10/2026 a partir de lo que el sitio ya usa. **Todo lo nuevo
(plantillas, componentes, notas, páginas) sale de esta hoja; si algo no
alcanza, se cambia acá primero.** Verificación automática:
`node wordpress/build/revisar-marca.mjs` (corre en `test-wp.yml`).

## Qué es Pescamapa (desde el 14/11/2026)

Dos promesas, SABER e IR (`docs/estrategia-contenido.md` → "Posicionamiento (14/11/2026)"). Textos en `data/ui.json`:

- `marca.definicion` (descripción larga): "Pescamapa es un servicio gratuito para pescadores que viajan a la Argentina. Enviás un pedido, consultamos a los lodges, guías y paquetes que encajan y en 48 horas te devolvemos una comparación de precios, qué incluye y disponibilidad. Algunos operadores nos pagan una comisión por las reservas que llegan por Pescamapa; el pescador paga lo mismo. Antes de decidir, cada destino tiene su reglamento, su temporada y sus fuentes."
- `marca.saber` (pilar SABER): "Pescamapa reúne en un solo lugar lo que está disperso en cientos de webs y PDFs oficiales: licencias, dónde pescar, normas, vedas y temporadas, con la fuente a la vista."
- `marca.lema` (tagline paraguas, pie de página y descripción por defecto): **"Todo para pescar en la Argentina, en un solo lugar."** (pt: "Tudo para pescar na Argentina, em um só lugar."; en: "Everything for fishing in Argentina, in one place.")

(Historia: el 26/10/2026 la definición era "Pescamapa reúne todo lo que necesitás saber antes de ir a pescar…" y el lema "Cuándo ir, qué dice el reglamento y con quién pescar en Argentina". Sin superlativos sin fuente: "la más completa" se dirá cuando se pueda probar.)

## Voz

- Español rioplatense, voseo (*encontrá, querés, consultá*); nunca "vosotros".
- Concreto y con fecha: "actualizado el…", "precio al…". Sin superlativos sin
  fuente ("el mejor", "el más grande"); sin "se pesca todo el año" sin matiz.
- Áreas, no pesqueros: nada de coordenadas exactas.
- Glosario: **flycast** es el nombre local de la pesca con mosca (sin distinción
  práctica; fuente: consulta a Gemini del 21/10/2026, sin fuente oficial). Se
  escribe una sola modalidad, "Mosca (flycast)", no dos.
- El reglamento y las vedas se enlazan a la autoridad; no se copian fechas.
- Las personas aparecen con nombre, rol y fecha, y solo con su permiso.

## Color

Tokens en `pescamapa.css` (`:root`). **Nunca un hex en CSS nuevo ni en
`style=""`**; se usa `var(--token)`. Contraste medido (WCAG, mínimo 4.5 para
texto normal):

| Token | Valor | Uso | Contraste |
| --- | --- | --- | --- |
| `--ink` | `#14251f` | Texto | 15.3 sobre `--paper` |
| `--forest` | `#11251f` | Superficies oscuras (cabeceras, pie), botón oscuro | blanco sobre forest: 16.1 |
| `--lime` | `#d8e46a` | Acento: botón principal, etiquetas sobre oscuro | forest sobre lime: 11.6 |
| `--cream` | `#f4f2e9` | Tarjetas y cuadros de apoyo | ink sobre cream: 14.3 |
| `--paper` | `#fbfaf6` | Fondo de página | — |
| `--white` | `#ffffff` | Tarjetas con borde, formularios | — |
| `--line` | `#d9ddd2` | Bordes y separadores | — |
| `--muted` | `#5f685e` | Texto secundario | 5.5 sobre paper |
| `--olive` | `#5e6b33` | Etiquetas (Bebas) sobre fondo claro | 5.5 sobre paper |
| `--moss` | `#344a3d` | Texto de chips y sellos | 8.5 sobre cream |
| `--on-dark` | `#cbd3c9` | Texto secundario sobre forest | 10.5 |
| `--orange` | `#ee7a48` | **Solo gráficos del mapa** (pines) | 2.7 sobre paper: nunca texto sobre claro |
| `--warn-bg` / `--warn-line` | amarillo | **Solo avisos y vista previa interna** | — |

Reglas: lime **nunca** como color de texto sobre claro (1.3); sobre claro va
como relleno con texto forest. El amarillo de aviso no se usa para citas ni
destacados. Un solo acento por pantalla.

## Tipografía

Tres familias, tres trabajos. No se suma una cuarta ni se cambia el trabajo
de una.

| Familia | Token | Trabajo |
| --- | --- | --- |
| Playfair Display | `--font-head` | Titulares y citas destacadas. La cursiva es el énfasis |
| Outfit | `--font-body` | Texto corrido, botones, formularios |
| Bebas Neue | `--font-label` | La marca y etiquetas cortas en mayúsculas (eyebrows, pills, numeración). Nunca en frases |

Escala (tokens `--fs-*` en `extra.css`): caption 13 · small 14 · body 16 ·
lead 18 · h3 22 · h2 `clamp(24px, 3vw, 32px)` · h1 `clamp(30px, 4.6vw, 52px)`
· label 16 (Bebas). El hero y los títulos de las secciones del inicio llevan su
propio `clamp()` en `pescamapa.css`. Mínimo 12 px (chips y sellos); nada por
debajo de 13 px en texto que se lee de corrido. Interlineado: 1.6 en texto,
1.08–1.2 en titulares. Columna de lectura: `--measure` (720 px).

## Forma

Radios: 16 px (tarjetas, cuadros), 10 px (campos), 99 px (botones, píldoras).
Ancho de página `--shell` (1180 px) con `--gutter`. Fotos horizontales 4:3,
con el crédito del autor sobre la imagen (`pm_foto()`); nunca una foto sin
autor. Sombras solo en elementos flotantes.

## Componentes que ya existen (se reutilizan, no se reinventan)

| Componente | Clase | Dónde |
| --- | --- | --- |
| Etiqueta de sección | `.eyebrow` | Cabeceras |
| Botones | `.button` + `.primary` / `.dark` / `.secondary` | Todo |
| Chips | `.chips` | Especies, modalidades |
| Cuadro de apoyo | `.regla`, `.elegir`, `.datos` | Reglamento, listas, datos clave |
| Cita de una entrevista | `.cita` | Guías |
| Cita dentro de un texto | `blockquote` en `.prose` (cita destacada: cursiva Playfair con filete forest) | Notas, guías |
| Tarjeta de prestador | `.prestador` | Guías |
| Enlace "más para hacer" | `.mas` (+ `.sello-pago` si es publicidad) | Guías |
| Aviso | `.aviso.ok` / `.aviso.error` | Formularios |
| Fuentes | `.fuentes` + `.ofi` | Guías, notas |

## Plantilla nueva: lista de control

1. Hereda de la cabecera de las guías (`.v-destino` + `.top`, eyebrow,
   `h1`, `.bajada`, `.confianza`) y del cuerpo `.prose` con foto lateral.
2. CSS solo con tokens; si falta un tamaño o color, se discute acá.
3. Datos desde el plugin (`pm_*()`), nunca escritos a mano en la vista.
4. Texto de interfaz en `ui.json` en los **tres** idiomas.
5. SEO: título, descripción, canonical, hreflang, JSON-LD (`inc/seo.php`).
6. Fotos con `pm_foto()` y autor; fechas con `pm_fecha()`.
7. `node wordpress/build/revisar-marca.mjs` en verde, y una captura
   (`capturas.yml`) mirada por una persona antes de dar algo por terminado.

## Lo que encontramos al definir esto (21/10/2026)

- Las citas dentro del texto salían con el recuadro amarillo de aviso: se
  corrigió (cita destacada) y el amarillo queda para avisos internos.
- 16 tamaños de fuente sueltos (13 a 22 px) en `extra.css`: pasaron a la
  escala. `pescamapa.css` es generado desde Astro y conserva los suyos; se
  ordena cuando se toque cada componente.
- `--orange` y `--lime` fallan contraste como texto sobre claro: limitados a
  gráfico y relleno, respectivamente.

## Pendiente de decidir (propuestas)

- Mínimo de tamaño: hoy los chips (12 px) y el sello "oficial" (11 px) están
  por debajo de 13. Propuesta: 12 como piso absoluto.
- Un segundo peso de Bebas o una cursiva de Outfit: **no**; mantener tres
  familias con un solo peso por trabajo.
