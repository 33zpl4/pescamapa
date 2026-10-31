# Estrategia SEO y comercial de contenido (v2, 31/10/2026)

Este documento manda sobre `docs/seo-oportunidades.md` (20/10) y sobre la lectura de la ronda 1 (`docs/seo-palabras-clave-en-2026-10-29.md`) en todo lo que los contradiga. Las reglas editoriales (`docs/estrategia-contenido.md`) y de listados (`docs/prestadores.md`) siguen vigentes y no se tocan acá.

**Insumos (31/10/2026, pasados por el dueño):** (1) un protocolo de "Ronda 2" de Keyword Planner, (2) una crítica externa a ese protocolo, (3) un análisis de los cuatro CSV exportados ese día. **Los CSV no están en el repo**: los volúmenes y CPC que se citan abajo son los que reporta el análisis, no verificados por nosotros. Primer pedido: subir los cuatro CSV a `docs/investigacion/keyword-planner/` con la configuración de cada exportación (país, idioma, rango de fechas), para que cualquier sesión pueda contrastarlos.

## 1. Tesis

Pescamapa es **un portal de destinos y experiencias de pesca de la Argentina con dos productos comerciales distintos**, Patagonia (trucha, mosca, lodges, flotadas; temporada noviembre–mayo) y Litoral (dorado, surubí, paquetes y salidas; todo el año con picos), y **una capa comercial compartida**: ficha → comparación → pedido de cotización por nuestro formulario → derivación a un prestador. Ninguno de los dos productos depende del otro.

Dos mercados, dos idiomas de entrada, un mismo sistema:

| Mercado | Idioma | Qué busca | Qué le damos | Cómo gana Pescamapa |
| --- | --- | --- | --- | --- |
| Internacional (EE. UU., Reino Unido, Canadá) | EN | Lodges, paquetes, guías de día, precios, permisos | Guías de destino, notas pilar, comparadores con precios fechados, fichas de operadores | Derivación de consultas (fase 2: comisión a operadores con acuerdo) |
| Regional (Argentina; Brasil en PT; España después) | ES / PT | Salidas de un día, paquetes, cabañas, licencias, reglamento | Guías, paquetes por destino, fichas de prestadores, reglamento verificado | Consultas a guías locales; paquetes |

El regional **no se deduce del export en inglés**: necesita su propia extracción (ronda 3, lotes 5 y 6).

**La ventaja que construimos** no es escribir más que las agencias de EE. UU., sino lo que el viajero necesita antes de reservar y nadie le da junto: precios comparables con fecha, opciones locales, inclusiones desglosadas, logística, reglamento leído y accesos con fuente. Eso exige que cada ficha distinga **tarifa vigente** (con temporada), **tarifa publicada sin temporada confirmada** y **precio a consultar**, y que cualquier acuerdo comercial esté a la vista.

## 2. Lo que dicen los datos (reportados; sin verificar en el repo)

Del archivo principal (1.782 filas), solo 69 keywords nombran Argentina, Patagonia, un destino, una especie o un lodge: la extracción tiene mucho ruido y no sirve para priorizar sin depurar. Lo útil:

| Keyword | Búsquedas/mes (escalón) | CPC alto (USD) | Competencia Ads | Lectura |
| --- | --- | --- | --- | --- |
| `patagonia fishing`, `patagonia fly fish` | 5.000 | 1,90 / 3,00 | Alta | Demanda amplia, intención mixta: la captan guías de destino y la nota de trucha |
| `golden dorado fishing` | 5.000 | 3,17 | Baja | **Silo propio del dorado**, no una subsección de Patagonia |
| `golden dorado` | 5.000 | 1,72 | — | Puede ser biología o equipo: lo cubre la ficha de especie |
| `fly fishing in patagonia`, `argentina fly fishing`, `fly fishing patagonia argentina` | 500 | 3,1–3,5 | Media | Pilar EN de Patagonia (falta) |
| `patagonia fly fishing lodges` | 500 | 4,11 | Media | Directorio de lodges con precios fechados |
| `golden dorado fishing argentina`, `golden dorado argentina`, `golden dorado fly fishing` | 500 | 2,6–3,8 | Baja | Ya hay pilar EN; sumarle lodges, guías y precios |
| `best fly fishing lodges in patagonia` | 50 | 4,54 | Alta | Comparador |
| `patagonia fishing guides`, `patagonia fly fishing outfitters` | 50 | 4,10 / 3,50 | Media | **Fichas de guías de día** (transaccional) |
| `patagonia fly fishing trip cost` | 50 | 3,00 | Alta | Página de costo del viaje |
| `pampas lodge` (500, 5,95), `el encuentro lodge argentina` (50, **15,28**), `las pampas lodge argentina` (50, 5,24), `world's end lodge argentina` (50) | | | | Fichas por marca, solo con información propia y verificable |
| `dorado on the fly` (50, 5,31) | | | | Marca/operador: ficha |

Lotes específicos de la ronda 2 (sea-run 7 keywords, dorado/Litoral 9, guías 8): sin volumen ni CPC en la mayoría. **Es falta de investigación, no ausencia de mercado.** Kau Tapen, San Huberto y Jurassic Lake casi no aparecen: se investigan por nombre en la ronda 3.

## 3. Correcciones de método que adoptamos

1. **CPC alto no es comisión alta.** Dice presión publicitaria y valor comercial para quien puja; el margen solo se conoce con acuerdos y conversiones. No se escribe "CPC > 3,50 confirma 15 %" en ningún doc.
2. **El índice de competencia es de anuncios, no dificultad SEO.** Un índice bajo con CPC alto es un indicio, no un hueco orgánico probado: se confirma mirando la SERP del país.
3. **No se tira nada por debajo de 20 búsquedas.** Marcas, tarifas, disponibilidad y reservas viven en el long tail. Tres segmentos de trabajo: 100+ (evaluar SERP ya), 10–99 (conservar si hay intención comercial), 0–9 (conservar marcas y precios; validar a mano).
4. **Un país y un idioma por exportación**, con la configuración guardada. No se suman volúmenes de exportaciones distintas. País de búsqueda ≠ nacionalidad del que busca.
5. **"Start with a website" extrae ideas del contenido de la página**, no las pujas reales del competidor. Sirve para descubrir clusters, no para espiar gasto.
6. **Exclusiones globales, no.** `chile`, `saltwater`, `bass` solo se excluyen cuando la intención es ajena; `argentina vs chile fly fishing` es útil.
7. **La métrica que decide es el valor capturable**, no el CPC: `kau tapen rates` tiene mucha intención, pero si el pescador reserva directo con el lodge y no hay acuerdo, para nosotros vale poco hasta que lo haya.
8. **URLs de competidores que cambiaron** (comprobadas por el análisis el 31/10): Yellow Dog → `yellowdogflyfishing.com/collections/fly-fishing-argentina`; Nervous Waters → `nervouswaters.com/fishing-by-country/argentina/` (y su página `our-rates`, más útil que la portada); Frontiers → `frontierstrvl.co.uk/argentina/hosted-trips`; Fly Water Travel migró a `farbank.com/pages/fly-water-travel/`. Fly Water publica **fechas de disponibilidad 2026 y 2027** (Pirá Lodge, Dorados on the Fly): nace el silo F.

## 4. Seis silos y qué página nuestra los sirve

No abrimos un árbol de URLs nuevo (las rutas `/en/patagonia/fly-fishing-lodges/` del análisis no existen y no hacen falta): cada silo se sirve con los tipos de página que ya tenemos. Guías de destino (`/<región>/<provincia>/<slug>/`), notas tipo guía (`/notas/`, `/en/notes/`, `/pt/notas/`), páginas de paquetes (`/paquetes-de-pesca/<destino>/`, `/en/fishing-packages/<destino>/`), fichas de especie y **prestadores dentro de la guía del destino** (dos niveles, `docs/prestadores.md`).

| Silo | Intención | Página que lo sirve | Estado |
| --- | --- | --- | --- |
| A. Lodges y viajes de alto importe | `patagonia fly fishing lodges`, `best fly fishing lodges in patagonia`, `kau tapen lodge rates` | Nota pilar EN "Patagonia fly fishing lodges: what each one publishes" (comparador) + fichas de prestador tipo Alojamiento en cada guía | Dossiers lote 1 (8 lodges, 3 sin dominio o 403); comparador no escrito |
| B. Guías, salidas y flotadas | `patagonia fishing guides`, `bariloche fly fishing day trip`, `limay river drift boat guide` | Fichas de prestador (Guía / Paquetes y salidas) en la guía de Bariloche y de San Martín–Junín + paquetes por destino | **4 fichas de Bariloche el 31/10** (script `prestadores-bariloche-dia-2026-10-31`); San Martín–Junín: 0 fichas de guía de día |
| C. Presupuestos y por tu cuenta | `fly fishing argentina on a budget`, `patagonia fly fishing trip cost`, `without a guide` | Nota "Fly fishing Argentina on a budget" (es/en/pt, 31/10) + futura "How much does a trip cost" | Publicada; falta la ronda de logística para el total |
| D. Dorado y Litoral | `golden dorado fishing argentina`, `corrientes fishing trips`, `parana river fishing packages` | Pilar EN `golden-dorado-fishing-argentina` + guías de Paso de la Patria, Goya, Esquina, La Paz + paquetes de Goya y Esquina + fichas | Falta: lodges y precios en el pilar (R1/R5), delta (R3), versión es/pt del pilar |
| E. Especies, ríos y zonas | `rio malleo fly fishing`, `chimehuin river access`, `jurassic lake fishing season` | Guías de destino (aguas con reglas por tramo) + ficha de especie + nota del Limay | Reglas por ambiente leídas (Neuquén, Parques, Río Negro parcial); accesos: solo con fuente |
| F. Marcas, reseñas y disponibilidad | `san huberto lodge reviews`, `kau tapen 2027 availability`, `limay river lodge booking` | Ficha de prestador por lodge con tarifa fechada, temporada, capacidad y "disponibilidad declarada" (fecha de lectura) | Depende de que el lodge publique; sin reseñas propias (no inventamos) |

**Decisión de producto pendiente (dueño):** un **índice de prestadores por región** ("Guías y lodges de la Patagonia") como vista propia, para que las fichas no vivan solo dentro de cada guía. Costo: una vista nueva en el tema + una ruta; beneficio: una URL por silo A y B. No se hace hasta tener ≥ 8 fichas por región.

## 5. Cómo decidimos qué publicar: puntaje

Cinco criterios, 100 puntos. Se puntúa **después** de mirar la SERP del país y las fuentes disponibles, no desde el CPC.

| Criterio | Peso | Qué mide |
| --- | --- | --- |
| Intención comercial | 25 | ¿El que busca está por contratar o por comparar precios? |
| Oportunidad SEO | 25 | ¿Quién domina la SERP (agencias, lodges, foros, directorios) y podemos hacer una página mejor? |
| Monetización capturable | 20 | ¿La consulta puede pasar por nosotros y llegar a un prestador con el que trabajemos? |
| Datos verificados disponibles | 15 | ¿Tenemos fuente y fecha, o hay que investigar desde cero? |
| Encaje con Pescamapa | 15 | ¿Es lo que somos (reglamento, fuentes, derivación) o un artículo genérico? |

Ranking de hoy (puntajes nuestros, ilustrativos, no de Google Ads): ver `docs/ideas-contenido-seo-2026-10-31.md` (backlog).

## 6. Orden de trabajo (90 días, adaptado a lo que ya existe)

| Fase | Trabajo | Resultado | Estado |
| --- | --- | --- | --- |
| Días 1–15 | Ronda 3 de Keyword Planner por país e idioma (`docs/investigacion/ronda-3-keyword-planner-2026-10-31.md`); revisión de SERP de los grupos prioritarios; CSV al repo | Mapa de demanda por mercado | **Pedido al dueño, 31/10** |
| Días 16–30 | Auditoría de 15–20 prestadores (lote 1 de lodges ya tiene 8; sumar guías de día de Bariloche y San Martín–Junín, operadores de Corrientes); dossier de 10 campos cada uno | Base de fichas con fuente y fecha | 4 fichas de Bariloche hechas el 31/10; resto pendiente |
| Días 31–60 | 5 páginas principales (ver backlog P1–P5) y 10–15 fichas verificadas, enlazadas entre sí | Primer ecosistema que convierte | Idea 1 publicada en 3 idiomas (31/10) |
| Días 61–90 | Comparadores, formulario con país, medición de consultas por destino y prestador, primer operador con acuerdo | Validación comercial | Depende del primer acuerdo firmado |

No se invierte en publicidad paga para términos genéricos hasta ver si llegan consultas y si los prestadores las reciben y las remuneran.

## 7. Qué medimos

| SEO (Search Console) | Comercial (panel de consultas de WP) |
| --- | --- |
| Impresiones y clics sin marca, por país y por tipo de página | Consultas recibidas por destino y por prestador |
| Keywords en top 10 por mercado | Cuántas pasamos a un prestador y cuántas se concretan (estado de la consulta) |
| CTR orgánico por tipo de página | Prestadores que piden figurar o corregir su ficha (canal de prospección entrante) |
| Páginas indexadas y fecha de actualización | Reservas atribuidas e ingresos (cuando haya acuerdo) |

Revisión mensual, junto con `docs/preguntas-llm.md`.

## 8. Reglas que no cambian (resumen)

1. Hecho con fuente y fecha, o no se publica; precio sin temporada = referencia interna. Las agencias nunca son fuente de un dato publicable.
2. Nivel 1 "Información pública" con enlace de corrección o baja; nivel 2 "Operador con acuerdo", primero en la lista, con la línea de comisión. Nunca "verificado", "auditado" ni "sin sesgo".
3. Contacto del prestador interno; la consulta entra por nuestro formulario, de concierge, no de reserva.
4. Editorial puede nombrar lodges y guías; sin críticas, superlativos ni "lo que no te dicen".
5. Comisiones y precios de referencia de agencias: internos.
6. Inglés primero para el mercado internacional; español como versión, no como fuente de esos datos. Español primero para lo regional.
7. Un `wp-script` a la vez; simulacro → log → vivo → `VERIFIED` → capturas.

## 9. Lo que no sabemos y pedimos

- **Los CSV** de la ronda 2 y la configuración de cada export (país, idioma, fechas): sin eso, los números de la sección 2 quedan como "reportados".
- **Demanda regional en ES y PT**: no hay export. Lotes 5 y 6 de la ronda 3.
- **Marcas** (Kau Tapen, San Huberto, Jurassic Lake lodge, Tipiliuke, Rio Manso, Spring Creek, El Encuentro, Las Pampas): lote 1 de la ronda 3.
- **Dificultad SEO real**: solo con revisión de SERP por país; nada en el export la mide.
- **Qué país convierte mejor**: no se sabe hasta tener consultas por país en el panel.
