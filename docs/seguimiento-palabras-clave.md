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
| la zona dorado fishing / la zona fishing lodge / la zona lodge argentina / golden dorado la zona argentina | **C** | 2 + 1 + 1 + 1 | nota «La Zona (Salto Grande)» y pilar del dorado | **Brecha:** la nota dice quién te puede llevar y el reglamento, pero no hay ninguna tarjeta de lugar en La Zona; quien busca «lodge» no encuentra uno | **17/11/2026: revisados los candidatos, ninguno alcanza para ficha** (sin fecha de 2026 en su página, permiso argentino sin resolver; `docs/investigacion/la-zona-lugares-2026-11-17.md`). Pendiente: releer con `leer-vivo` o pedir consentimiento |
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
- **Con intención comercial y sin cubrir bien:** «La Zona + lodge». Hay búsqueda real (5 impresiones en cuatro formas), pero la nota actual solo informa. Es una decisión de listados, no de texto; el 17/11 se revisaron los lugares y ninguno alcanzó el criterio de ficha (ver la fila).
- **Mayor volumen del día:** altura del río en Zárate (8 impresiones), informativa. Conviene comprobar la página en vivo antes de tocarla.
- Las impresiones son diminutas (1 a 8): sirven para ver qué aparece, no para decidir por volumen. Cada fila se mira por tendencia entre fechas.

## Registro por fecha

| Fecha | Qué se pegó | Observaciones |
|---|---|---|
| 17/11/2026 | Top queries del día (incompleto) | Primera fila de la tabla |
