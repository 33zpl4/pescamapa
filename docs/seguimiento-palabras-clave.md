# Seguimiento manual de palabras clave (17/11/2026)

Lista de consultas que seguimos a mano porque en los informes de Search Console se pierden (la cola larga desaparece al agrupar). Se agrega una columna por fecha con las impresiones del día o del período que se pegue. La cobertura de la tabla sale de lo que hay en los scripts y las notas del repo; **no está leída en vivo** y no dice en qué posición sale cada página.

Cómo se usa: el dueño pega el listado de «Top queries» (clics, impresiones); se suman las consultas nuevas relevantes y se actualiza la columna de la fecha. Una consulta se saca solo si pasa a no tener ninguna impresión durante un mes. La corrida de `gsc-consultas.yml` (≈27/11/2026, un minuto de Actions) compara posiciones por página.

Intención: **C** = comercial (el visitante puede acabar pidiendo una propuesta), **I** = informativa, **M** = de marca o de competidor.

## Lista seguida

| Consulta | Int. | Impr. 17/11 | Página que debería responderla | Cobertura (repo) | Acción |
|---|---|---|---|---|---|
| argentina fishing license / fishing license | C | 2 + 2 | hub «licencia para extranjeros» y nota «permiso paso a paso» (en) | Cubierta: título, FAQ y enlaces desde inicio y guías (13 y 16/11) | Medir el 27/11 |
| trout fishing in patagonia / trout fishing patagonia / trout patagonia / patagonia trout / trout fishing in patagonia argentina | C | 2 + 2 + 1 + 1 + 1 | pilar «Trout fishing in Patagonia: brown, rainbow and sea-run» | Cubierta desde el 15/11 | Medir el 27/11 |
| best time to fish patagonia | I/C | 1 | pilar de la trucha y calendario en inglés | Parcial: hay «cuándo pescar trucha» en es/en/pt, sin H2 con esta frase | Revisar H2 y FAQ de temporada del pilar |
| la zona dorado fishing / la zona fishing lodge / la zona lodge argentina / golden dorado la zona argentina | **C** | 2 + 1 + 1 + 1 | nota «La Zona (Salto Grande)» y pilar del dorado | **Brecha:** la nota dice quién te puede llevar y el reglamento, pero no hay ninguna tarjeta de lugar en La Zona; quien busca «lodge» no encuentra uno | **17/11/2026: revisados los candidatos, ninguno alcanza para ficha** (sin fecha de 2026 en su página, permiso argentino sin resolver; `docs/investigacion/la-zona-lugares-2026-11-17.md`). **18/11/2026: ficha de La Zona Lodge y guía nueva «Pesca en Concordia y La Zona»** (es/en/pt; información pública, tarifa a consultar; fuente: Instagram del lugar, 15/10/2026, texto sin enlace; `docs/investigacion/la-zona-lugares-2026-11-17.md`), enlazada desde la nota de La Zona y el pilar del dorado. Medir en Search Console desde el 2/12/2026: impresiones y posición de «la zona lodge», «la zona fishing lodge» y «golden dorado la zona argentina» en la guía de Concordia frente a la nota. **18/11/2026 (tarde):** nota, guía y ficha corregidas con el reglamento por artículo, el llamado 01-2026 (Boletín del 12/11/2026; ofertas el 10/12/2026) y la reapertura atribuida a la prensa; la ficha lista solo el dorado (`docs/investigacion/la-zona-normas-2026-11-18.md`). **18/11/2026 (noche):** la guía de Concordia suma foto con crédito a la Municipalidad, la sección con lo que dice la página municipal de turismo y la FAQ «¿Cuándo es la veda del dorado en Concordia?» (dos fechas que no coinciden, sin resolver), y la nota, la guía y la ficha citan lo leído de las Res. CARU 46/09 y 46/16 (`docs/investigacion/la-zona-normas-2026-11-18.md`, secciones E y F). Consultas a mirar desde ≈27/11: «veda dorado concordia», «pesca concordia entre ríos», «boga concordia temporada», «surubí puerto yeruá». Pendiente: consentimiento del lugar y permiso argentino; releer el 10/12/2026 si hay adjudicación; leer la 1/2023 y la 63/2026 |
| se puede pescar en bariloche | I/C | 2 | guía de Bariloche (la pregunta ya está en la FAQ) | Cubierta en el contenido; falta confirmar que el título/H1 la use | Leer en vivo con `leer-vivo` |
| manso | I | 1 | guía de Bariloche (río Manso, 3 tramos con reglas) | Cubierta | — |
| lago cholila | I/C | 1 | guía de Esquel y Trevelin | Parcial: aparece nombrado, sin sección propia | Evaluar sección en la guía de Esquel |
| tipos de truchas en la patagonia | I | 2 | ficha de la trucha y pilar | Cubierta (13 y 15/11) | Medir el 27/11 |
| surubí pez | I | 1 | ficha del surubí | Cubierta y reforzada (16/11) | — |
| calendario de pesca 2026 / calendario pesca 2026 / calendario de pesca noviembre 2026 | I | 3 + 2 + 3 | página «Calendario de pesca» (título «…2026/27») | Cubierta; no hay vista por mes | Evaluar si conviene una vista «noviembre» |
| permiso de pesca buenos aires | I | 1 | nota del permiso (provincia de Buenos Aires) | **Brecha:** ningún script del permiso menciona Buenos Aires; el público es local, no el visitante extranjero | Decidir si interesa (guías de Chascomús existen) |
| altura del río en Zárate / Baradero / Paraná Zárate / altura de los ríos | I | 8 + 1 + 1 + 1 + 3 | página «Río hoy» | Cubierta con Zárate y Baradero en `rio.json`; sin comprobar cómo sale | Leer la página en vivo; tráfico alto para ser sin valor comercial |
| mapa de pesca | I | 1 | `/mapa/` | Sin comprobar | Leer título y descripción de `/mapa/` |
| equipo de spinning | C | 1 | nota de equipo para dorado (15/11) | Parcial: está el equipo del dorado; no hay una nota de spinning en general | Evaluar |
| espoesía alternatives | M | 2 | ninguna | No cubierta a propósito: no hacemos páginas de comparación con nombre propio | No actuar; seguir como indicador |

## Lectura

- **Con intención comercial y cubiertas:** licencia en inglés y trucha en la Patagonia. Falta medir (≈27/11).
- **Con intención comercial y sin cubrir bien:** «La Zona + lodge». Hay búsqueda real (5 impresiones en cuatro formas), pero la nota actual solo informa. Es una decisión de listados, no de texto; el 17/11 ninguno alcanzó el criterio y el 18/11 apareció una publicación fechada del lugar: ficha y guía de Concordia (ver la fila).
- **Mayor volumen del día:** altura del río en Zárate (8 impresiones), informativa. Conviene comprobar la página en vivo antes de tocarla.
- Las impresiones son diminutas (1 a 8): sirven para ver qué aparece, no para decidir por volumen. Cada fila se mira por tendencia entre fechas.

- **Entre Ríos (18/11/2026):** el permiso de 5 días para extranjeros ($24.251,60, octubre de 2026) y la pregunta «¿se puede pescar dorado en Entre Ríos?» (Ley 11.174, Decreto 726/25) están ahora en la nota del permiso, el hub, y las guías de La Paz y Concordia (es/en/pt), con FAQ visible = JSON-LD. Consultas a mirar en Search Console desde ≈27/11: «permiso de pesca entre ríos», «licencia de pesca entre ríos extranjeros», «se puede pescar dorado en entre ríos», «pesca dorado concordia», «pesca la paz entre ríos», «entre rios fishing license». Sin medición todavía: no se verificó cómo salen en Google.

## Apariciones en funciones de IA de Google (Search Console → Performance → Generative AI features)

Línea de base pegada por el dueño el 18/11/2026. **Período no indicado**; **111 páginas en total** con apariciones; abajo el top 25 por impresiones (apariciones en funciones de IA).

| # | Página | Apariciones |
|---|---|---|
| 1 | `/pampa/buenos-aires/pesca-chascomus/` | 37 |
| 2 | `/mapa/` | 23 |
| 3 | `/patagonia/rio-negro/pesca-en-bariloche/` | 18 |
| 4 | `/patagonia/santa-cruz/pesca-en-rio-gallegos/` | 15 |
| 5 | `/en/river-levels/` | 14 |
| 6 | `/litoral/corrientes/pesca-en-goya/` | 12 |
| 7 | `/notas/reglas-equipo-pesca-patagonia/` | 12 |
| 8 | `/patagonia/neuquen/pesca-san-martin-junin-de-los-andes/` | 12 |
| 9 | `/pt/especies/dourado/` | 12 |
| 10 | `/litoral/corrientes/pesca-en-esquina/` | 11 |
| 11 | `/en/notes/golden-dorado-fishing-argentina/` | 10 |
| 12 | `/notas/licencia-pesca-patagonia-2026-27/` | 10 |
| 13 | `/en/litoral/corrientes/fishing-in-paso-de-la-patria/` | 9 |
| 14 | `/notas/la-zona-salto-grande-pesca-dorado/` | 9 |
| 15 | `/pt/calendario-de-pesca/` | 8 |
| 16 | `/calendario-de-pesca/` | 7 |
| 17 | `/litoral/entre-rios/pesca-en-la-paz/` | 7 |
| 18 | `/notas/cuando-pescar-surubi/` | 7 |
| 19 | `/pique/` | 7 |
| 20 | `/` | 6 |
| 21 | `/en/patagonia/santa-cruz/fishing-in-jurassic-lake/` | 6 |
| 22 | `/patagonia/santa-cruz/pesca-en-el-calafate/` | 6 |
| 23 | `/en/species/golden-dorado/` | 5 |
| 24 | `/especies/trucha/` | 5 |
| 25 | `/notas/tallas-cupos-vedas/` | 5 |

- Es la **medición directa de Google**: reemplaza, para Google, el pegado manual de respuestas de asistentes. **ChatGPT, Perplexity y Grok siguen midiéndose a mano** (`docs/preguntas-llm.md`).
- **Falta el período y las consultas** que dispararon cada aparición: sin ellos no se puede comparar ni saber qué pregunta respondía cada página.
- **Repetir junto con `gsc-consultas` (≈27/11/2026)**, con el período anotado, y comparar contra esta línea de base.
- Lectura rápida (sin período, solo orden): lideran una guía de Buenos Aires (Chascomús, 37), el mapa (23) y las guías de Bariloche, Río Gallegos, Goya, San Martín/Junín y Esquina; también aparecen notas, fichas de especie y páginas en inglés y portugués.

## Registro por fecha

| Fecha | Qué se pegó | Observaciones |
|---|---|---|
| 17/11/2026 | Top queries del día (incompleto) | Primera fila de la tabla |
| 18/11/2026 | Apariciones en funciones de IA de Google (top 25 de 111 páginas, período no indicado) | Línea de base de la sección nueva; repetir con `gsc-consultas` (≈27/11) |
