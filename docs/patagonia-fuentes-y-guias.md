# Patagonia: fuentes, valores y guías (recap al 30/10/2026)

Un solo lugar para retomar el trabajo de Patagonia. Lo que está leído lleva su fuente; lo que no, dice **NO LEÍDO**. Detalle y citas textuales en `docs/investigacion/` (archivos nombrados abajo).

## Guías publicadas el 30/10/2026

| Guía | Idiomas | URLs | Script |
| --- | --- | --- | --- |
| **Lago Strobel / Jurassic Lake** (Santa Cruz) | es "Lago Strobel", en "Jurassic Lake" | `/patagonia/santa-cruz/pesca-en-jurassic-lake/`, `/en/patagonia/santa-cruz/fishing-in-jurassic-lake/` (los slugs conservan "jurassic-lake") | `jurassic-lake-2026-10-30` y sus ajustes (`-exclusion`, `-faq`, `-foto`, `-pin`, `jurassic-nombre`) |
| **San Martín y Junín de los Andes** (Neuquén) | es, en | `/patagonia/neuquen/pesca-san-martin-junin-de-los-andes/`, `/en/patagonia/neuquen/fishing-san-martin-junin-de-los-andes/` | la guía es de `san-martin-junin-2026-10-24`; el 30/10 se le cargó el contenido 2026/27 con `junin-unificar-2026-10-30` |
| **Bariloche y el Nahuel Huapi** (Río Negro) | es, en | `/patagonia/rio-negro/pesca-en-bariloche/`, `/en/patagonia/rio-negro/fishing-in-bariloche/` (confirmar en vivo) | `bariloche-2026-10-30` |

**Error del 30/10 que no se repite:** se creó una guía duplicada de Junín (clave `junin-de-los-andes`) sin ver que ya existía la de `san-martin-junin` (24/10). El mapa del inicio mostró el nombre dos veces. **Antes de crear una guía: buscar la clave y el nombre en los scripts de `wordpress/scripts/` y en la lista de guías vivas.** El duplicado quedó en la papelera; sus URLs ya no sirven.

## Dónde vive cada regla (fuentes primarias)

| Qué | Dónde | Estado |
| --- | --- | --- |
| Reglamento Patagónico 2026/27, anexos | `https://reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/`, `…/anexo-rio-negro/`, `…/ambientes-neuquen/`, `…/ambientes-de-rio-negro/`, `…/ambientes-parques-nacionales-del-sur/` | El sitio **bloquea los runners de GitHub** (403 / fetch failed). El dueño abre la página en su navegador y pega el texto: así se leyeron los **anexos Neuquén y Río Negro (puntos generales)**. Los **listados de ambientes** se leyeron en copias de AICACYP (`aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-*.pdf`) |
| **NO LEÍDO** | Texto general 2026/27 (arts. 4 y 22), listado de ambientes de **Río Negro** | Pedirle al dueño que pegue `…/reglamento-items/ambientes-de-rio-negro/` y el reglamento general |
| Permisos Neuquén | `cazaypesca.neuquen.gob.ar/permisos/` (no abre desde GitHub) | Valores por la nota de ANBariloche del 16/10/2026 y el portal de Río Negro |
| Permisos Río Negro (Bariloche) | `permisosdepesca.com.ar` (Mesa Directiva de Pesca Deportiva Zona Andina, Av. 12 de Octubre y Onelli) | Leído: valores 2026/27 |
| Santa Cruz | `pesca.santacruz.gob.ar` (SIPESQ; no abre desde GitHub) | Disposición 241/2026 (copia fiel, escaneada, arts. 1 a 18) y el Word de 16 páginas con el Anexo I; MINPRO del 1/10/2026 para los valores |

## Valores de permisos 2026/27 (pesos argentinos)

| | Día | 10 días / semana | Temporada |
| --- | --- | --- | --- |
| **Neuquén y Río Negro, no residente** | 50.000 | 140.000 (10 días) | 280.000 |
| Neuquén y Río Negro, limítrofes | — | 50.000 (10 días) | — |
| Neuquén y Río Negro, residente | 12.500 | 25.000 | 50.000 |
| Trolling (adicional, según Perfil/Comisión) | 25.000 | 50.000 | 100.000 |
| Zona preferencial Neuquén (anuncio 16/10) | 12.500 | 25.000 | 50.000 |
| Zona preferencial en el texto del anexo (puede ser la temporada anterior) | 10.000 | 20.000 | 40.000 |
| **Santa Cruz, no residente** | 90.000 | 300.000 (semanal) | 450.000 |
| Santa Cruz, trolling adicional | 20.000 | 30.000 (semanal) | 40.000 |

Las guías publican los valores con su fuente y fecha, y mandan a confirmar al comprar.

## Lo confirmado y lo que no

- **Confirmado (leído):** validez del permiso patagónico en Río Negro, Neuquén, Chubut y Parques Nacionales (anexos de las dos provincias); fieltro prohibido en todas las aguas de jurisdicción de Río Negro y, por ambiente, en las aguas del Parque; guías no habilitados = infracción en Neuquén; no se pesca a menos de 500 m de presas ni desde puentes (Neuquén); Strobel y Barrancoso (Santa Cruz): captura y devolución, anzuelo simple, sin motores (Strobel), Barrancoso del 1/10/2026 al 3/5/2027.
- **No confirmado:** distancias de exclusión de 200 m (Strobel) y 100 m (Barrancoso): están **solo** en la copia de terceros, no en el Word de 16 páginas; **no se publican**. Los permisos "adicionales valen solo donde se emiten": viene de un extracto del texto general.
- **Descartado:** el "esqueleto" de una IA con citas de artículos y una lista de ríos de Río Negro sin fuente; una de sus afirmaciones (Manso Medio "solo mosca") contradecía el anexo leído.

## Cómo se trabaja una guía de Patagonia

1. Pegar las páginas del reglamento desde el navegador del dueño (fuente oficial) o leer los PDF por `investigar.yml`.
2. Escribir el script con es (fuente de los datos compartidos: región, provincia, coordenadas, especies, foto) y en. Slug propio por idioma.
3. **Simulacro → leer el log → en vivo → VERIFIED**, un `wp-script` a la vez (no hay `concurrency`: dos juntos pisan).
4. Fotos solo cedidas con crédito del autor ("Red Kulper" en Jurassic Lake, "Chad Shmukler" en Junín); la foto se carga en la guía en español y la inglesa la hereda.
5. Capturas (`capturas.yml`) para ver la página; `list_workflow_runs` devuelve ~500 KB: leerlo con un script y no imprimirlo.

## Pendiente de Patagonia

- Listado de ambientes de **Río Negro** y texto general 2026/27 → completar Bariloche (Limay río negrino, Ñirihuau, Pichileufu) y confirmar fieltro/validez.
- **Anexo I oficial** de Santa Cruz (con o sin las líneas de exclusión) y la resolución de valores.
- **Lodges**: San Huberto (su sitio responde 403), Spring Creek (sin dominio), Estancia Tecka (sin portal); contactar a los de las fichas de información pública. Dossiers: `docs/investigacion/lodges-lote1-dossiers-2026-10-30.md`.
- Guía de equipo y viento para Jurassic Lake (palabra clave 2 del plan); es/pt de la guía pilar del dorado.
