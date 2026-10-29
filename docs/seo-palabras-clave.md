# Palabras clave de alta intención: qué ya cubrimos y cómo las atacamos

Origen: lista de consultas pasada por el dueño el 28/10/2026 (armada por razonamiento, **sin volúmenes**: no hay Search Console ni
Keyword Planner detrás). Los volúmenes los da Google Keyword Planner (Argentina y Brasil) y, ya indexado el sitio, Search Console
(consultas en posición 8–20 de cada página = lo que hay que reforzar). Complementa `docs/seo-oportunidades.md`. **Export en inglés con volúmenes (29/10/2026): `docs/seo-palabras-clave-en-2026-10-29.md`.**

Reglas que mandan sobre la lista: lo que no se puede verificar online no se publica (decisión 22/10); precios solo con fecha y fuente,
si no "a consultar"; el contacto de prestadores es interno; áreas, no puntos exactos; "Armá tu viaje" es el único camino comercial hoy.

## 1. Transaccional (reservar / alquilar / contratar)

| Consulta | Estado | Cómo se ataca |
| --- | --- | --- |
| guía de pesca en [destino] tarifas | fichas de prestadores publicadas (8), sin precios con fecha | Sumar a cada guía "Cuánto cuesta salir" con **rango fechado y fuente** cuando un prestador lo confirme; si no, "a consultar" + formulario. No inventar rangos. |
| lodge / all inclusive | Patagonia y Goya (Isoró en borrador) | Fichas de alojamiento con precio fechado (`docs/modelo-alojamientos.md`). Después de la ronda con prestadores. |
| alquiler de botes laguna de Chascomús | **sin dato** | Ronda de investigación dirigida (rampas/muelles/alquiler) para Chascomús; mientras tanto la guía dice que no lo podemos confirmar. |
| pacotes de pescaria Paso da Pátria / Itatí | guías pt de Corrientes publicadas | Revisar el pt de Paso de la Patria con la consulta en portugués ("quanto custa", "como chegar", "licença"); precios solo fechados. |
| qué señuelos llevar | sin contenido | Baja prioridad; es del plan de afiliados (fase 2). Nota por especie cuando haya fuente. |

## 2. Operativa (alto tráfico, construye autoridad) — **acá está el mayor retorno ahora**

| Consulta | Estado | Cómo se ataca |
| --- | --- | --- |
| licencia de pesca [provincia] online / arancel | guías con licencia y fuente oficial; aranceles solo si hay fuente vigente | Nota por provincia "Cómo sacar la licencia en X (paso a paso)", con enlace oficial y fecha. El arancel solo si la web oficial lo muestra. |
| altura del río [puerto] hoy / Prefectura | **hecho**: `/altura-del-rio/` + una página por punto, diario | Medir la hora real del dato (corrida a las 07:07 AR) y revisar los títulos con "hoy" y "Prefectura" cuando Search Console muestre cómo nos buscan. |
| viento laguna de Chascomús hoy | clima en la guía | Página propia de clima del destino (misma maquinaria que los puntos del río). Candidato siguiente. |
| medidas mínimas / cupo / veda | Chascomús y pejerrey publicados; tallas en fichas de especie | **Tabla "Tallas mínimas y vedas por especie y provincia"** armada con los datos ya verificados (índice `/especies/` ya existe). Cupo del pejerrey fuera de la veda: sin dato, queda oculto. |
| bajadas de lanchas / muelles / costo | sin dato fiable (OpenStreetMap cubre poco) | Rondas por ciudad (`docs/rampas.md`): administrador, costo con fecha, fuente, sin coordenadas exactas. |
| acuerdo de licencias Litoral | **sin fuente**: no lo damos por cierto | Buscar el texto oficial antes de escribir una línea. |

## 3. Decisión y época ("¿voy este finde?")

| Consulta | Estado | Cómo se ataca |
| --- | --- | --- |
| cuándo pescar pejerrey en Chascomús | época en la ficha del pejerrey: oculta (sin fuente) | Ronda de investigación (época por temperatura, no por mes); la veda ya está. |
| temporada del dorado / surubí | notas "cuándo pescar" publicadas | Reforzar con el nivel del río (enlazar al punto de Goya/Paso). |
| cómo afecta la creciente al surubí | sin nota | Nota que cruza altura del río (dato nuestro) con la biología, con fuentes. |
| cierre de temporada trucha Neuquén [año] | nota Patagonia publicada (es/en/pt) | Actualizar con el reglamento 2026-2027 cuando salga (lo vigila `reglamento-patagonico`). |

## Orden propuesto

1. Tabla de tallas, cupos y vedas por especie y provincia (solo datos ya verificados).
2. Nota "Cómo sacar la licencia" por provincia (empezar por Buenos Aires: tenemos el texto oficial; Corrientes y Neuquén ya tienen la guía).
3. Ronda de rampas/muelles/alquiler de Chascomús (cierra la fila transaccional más pedida de AMBA).
4. Página de clima/viento de Chascomús.
5. Precios fechados por prestador, cuando la fase 2 se defina.

Lo que medimos: Search Console por página (impresiones y posición 8–20), y el "Top of page bid" de Keyword Planner para saber dónde pagan
los prestadores (señal de dónde estará la comisión).
