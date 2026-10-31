# Ronda 3 de Google Keyword Planner: protocolo para el dueño (31/10/2026)

Qué correr, cómo guardarlo y qué hago yo con cada archivo. Sale de la estrategia v2 (`docs/estrategia-seo-2026-10-31.md`, secciones 3 y 9). **Una exportación por país e idioma; la configuración se anota en el nombre del archivo.** No se filtra nada antes de exportar.

## 0. Antes de empezar: subir la ronda 2

Los cuatro CSV del 31/10 van a `docs/investigacion/keyword-planner/` con este nombre: `r2-<lote>-<país>-<idioma>-<desde>_<hasta>.csv` (ejemplo: `r2-principal-US-en-2025-10_2026-09.csv`). Si no sabés con qué país o idioma salió un export, ponelo como `desconocido`: ese archivo no se suma con otros.

## 1. Configuración (para cada corrida)

- **Ubicación:** un solo país por corrida: Estados Unidos, Reino Unido, Canadá, Brasil, Argentina, España. (Exploratorio, solo si sobra tiempo: español en EE. UU.)
- **Idioma:** inglés para US/UK/CA; portugués para BR; español para AR/ES.
- **Rango:** el máximo disponible (hasta 4 años), conservando el detalle mensual: la temporada de Patagonia es invertida y el promedio de 12 meses la aplasta.
- **Exportar todo**, sin filtros de volumen ni de texto. Los tres segmentos (100+, 10–99, 0–9) se trabajan después.
- Guardar como `r3-<lote>-<país>-<idioma>-<desde>_<hasta>.csv`.

## 2. "Start with a website" (una página por vez, "Use only this page")

URLs vigentes (las del protocolo original cambiaron):

- `https://www.yellowdogflyfishing.com/collections/fly-fishing-argentina`
- `https://www.nervouswaters.com/fishing-by-country/argentina/` y `https://www.nervouswaters.com/our-rates/`
- `https://www.frontierstrvl.co.uk/argentina/hosted-trips`
- `https://farbank.com/pages/fly-water-travel/index.html` (y las fichas de Pirá Lodge y Dorados on the Fly, que publican fechas 2026 y 2027)

Correrlas en **US** (y UK si da el tiempo). Anotar en el nombre del archivo de qué URL salió (`r3-web-nervouswaters-rates-US-en-…`). Esto trae ideas del contenido de la página, no las pujas reales del competidor.

## 3. Lotes de palabras ("Start with keywords")

Cada lote en cada país que le corresponda. Ordenar por "Top of page bid (high range)" descendente solo para mirar; **no filtrar** antes de exportar.

| Lote | Países | Semillas |
| --- | --- | --- |
| 1. Marcas de lodges | US, UK, CA | `san huberto lodge`, `kau tapen lodge`, `jurassic lake lodge`, `limay river lodge`, `tipiliuke lodge`, `rio manso lodge`, `spring creek lodge argentina`, `el encuentro lodge`, `las pampas lodge`, `villa maria lodge`, `pira lodge`, `dorado on the fly` |
| 2. Guías y salidas de Patagonia | US, UK, CA | `bariloche fly fishing guides`, `bariloche fishing trips`, `limay river fishing guide`, `limay river float trip`, `junin de los andes fly fishing`, `san martin de los andes fishing`, `patagonia fishing day trips`, `fly fishing float trip bariloche` |
| 3. Precios y comparativas | US, UK, CA | `patagonia fishing lodge prices`, `argentina fly fishing packages`, `fly fishing argentina cost`, `patagonia fishing vacation cost`, `fly fishing patagonia without a guide`, `all inclusive fly fishing argentina`, `cost of fly fishing trip in argentina` |
| 4. Dorado internacional | US, UK, CA | `dorado fishing argentina lodges`, `golden dorado fishing packages`, `corrientes fishing lodges`, `ibera wetlands fly fishing`, `parana river fishing guide`, `golden dorado outfitters`, `buenos aires fly fishing`, `tigre delta fishing` |
| 5. Mercado argentino y español | AR, ES | `pesca en goya corrientes`, `paquetes de pesca en esquina`, `excursiones de pesca paso de la patria`, `precios guías de pesca`, `pesca embarcada corrientes`, `cabañas para pescadores`, `pesca con mosca san martin de los andes`, `licencia de pesca neuquen`, `permiso de pesca patagonia 2027`, `guía de pesca bariloche` |
| 6. Brasil | BR | `pesca dourado argentina`, `pacotes de pesca argentina`, `pesca no rio paraná argentina`, `pesca em corrientes argentina`, `pousadas de pesca argentina`, `pesca esportiva argentina`, `dourado corrientes` |
| 7. Tierra del Fuego (medir, no asumir) | US, UK | `rio grande argentina fly fishing`, `tierra del fuego fly fishing`, `sea run brown trout argentina`, `rio grande sea trout` |

## 4. Qué hago yo con cada archivo

1. Lo leo sin filtrar y armo el archivo maestro (`docs/investigacion/keyword-planner/maestro.md`): una fila por keyword y país, con volumen, historial mensual, pujas baja y alta, índice de competencia, lote y URL de origen.
2. Clasifico intención (descubrir, planificar, comparar, consultar precio, comprobar disponibilidad, reservar) y entidad (lodge, guía, paquete, especie, destino), y asigno silo (A–F).
3. Para los grupos de 100+ y los de 10–99 con intención comercial, reviso la SERP del país (quién domina: agencias, lodges, publicaciones, foros, directorios, mapas) y anoto si podemos hacer una página mejor.
4. Puntúo con los cinco criterios de la estrategia y actualizo el backlog (`docs/ideas-contenido-seo-2026-10-31.md`).
5. Lo que una keyword pide y no tenemos con fuente pasa a una ronda de investigación (`docs/investigacion/rondas-ingles.md` o una nueva).

## 5. Lo que no se hace con estos datos

- No se suman volúmenes de países distintos ni de exports con configuración desconocida.
- No se infiere comisión ni margen del CPC.
- No se asume dificultad SEO del índice de competencia.
- No se publica ninguna cifra de volumen o CPC en el sitio.
