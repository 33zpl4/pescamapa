# Plan de contenido por recorrido del viajero (8/11/2026)

Origen: análisis pegado por el dueño del recorrido de planificación de un pescador de EE.UU. (7 etapas + lista de 10 preguntas antes de pagar el depósito). **Límite:** es un texto sin fuentes ni volúmenes; ordena y muestra huecos, pero no prueba demanda. Las rondas de palabras clave (`docs/investigacion/ronda-3-keyword-planner-2026-10-31.md`) y la medición (`docs/investigacion/medicion-ia/`) siguen siendo la evidencia. Lo que se publique sale de fuentes leídas, con fecha (regla de siempre).

## Mapa: etapa del viajero → lo que ya tenemos → hueco
| Etapa | Qué busca | Ya tenemos | Hueco |
| --- | --- | --- | --- |
| 1. Qué tipo de viaje | dorado vs trucha, costo desde EE.UU., noviembre vs marzo | Pilar EN del dorado; "por tu cuenta"; 5 páginas de costo (dorado con amigos, lodges de dorado, Bariloche, semana de trucha, Patagonia sur) | "Dorado o trucha: qué viaje elegir"; costo total del viaje desde EE.UU. (P3 del backlog) |
| 2. Destino, época, especie | lodge por zona, mejor mes, fechas de temporada | Guías de destino (Goya, Esquina, Paso de la Patria, La Paz, Bariloche, San Martín–Junín, Esquel y Trevelin, Jurassic Lake, Río Grande, Río Gallegos, Chascomús), notas "cuándo pescar", calendario, fechas de temporada | Comparador "temporada por región" y "noviembre vs marzo" (datos ya cargados en los campos de temporada); Esquina solo en español |
| 3. Operadores y qué incluye | qué cubre el precio, depósito, cancelación, acompañante, traslados | Tablas de costo con "incluye / no incluye" por operador | **Página "qué incluye (y qué cuesta aparte)" y "cómo pedir una cotización detallada"**: los ejemplos ya están leídos |
| 4. Ruta desde EE.UU. | vuelos, aeropuerto, traslado, conexión | Rutas desde la frontera brasileña (Google Maps) | **Nada para EE.UU.**: Buenos Aires → Corrientes / Esquel / Bariloche, traslados, conexión el mismo día |
| 5. Entrada, salud, seguro | visa, pasaporte, seguro y evacuación, vacunas | Documentos de ingreso para brasileños | Entrada, seguro y salud para EE.UU. (fuentes oficiales: Cancillería, Departamento de Estado, CDC) |
| 6. Licencias y reglas | permiso de extranjero por provincia, equipo, "¿el lodge lo incluye?" | Licencia de Patagonia (con permiso de vecinos), licencia de Corrientes (dentro de la página de Brasil), tallas y vedas | Licencia de Corrientes **como página propia**; **comparador de licencia de extranjero por provincia**; ingreso de equipo y carnada (aduana/SENASA) |
| 7. Equipo, dinero, práctica | qué llevar, equipo prestado, propinas, dólares/tarjeta, enchufe, teléfono | Parte: "equipo de préstamo" en las tablas de costo | Lista de equipo por pesquería; dinero y propinas (con fuente); enchufe y teléfono (menor prioridad) |

## Orden propuesto (impacto, costo y datos que ya hay)
1. **Qué incluye un paquete y qué cuesta aparte** (es/en/pt): cero datos nuevos (ejemplos ya leídos: recargo single, acompañante, día de llegada, licencia incluida o no). Responde la etapa 3 y la lista de 10 preguntas.
2. **Licencia de Corrientes como página propia** (es/en/pt): ya tenemos tabla, fórmula, portal y CPF; la página de Brasil la enlaza.
3. **Licencia de extranjero por provincia** (comparador): hay que leer las páginas provinciales (hoy solo fuente secundaria). Etapa 6.
4. **"Pescar en Argentina desde EE.UU."** (hub de origen, como el de Brasil): necesita capturas de Google Flights y Maps del dueño y lecturas oficiales gratuitas. Cubre etapas 4, 5 y 7.
5. Temporada por región y "noviembre vs marzo"; "dorado o trucha"; costo total desde EE.UU. (depende del 4).
6. Equipo y aduana (sirve a EE.UU. y a Brasil).

## Reorganizar lo que ya existe
- **Páginas de origen** (Brasil hecha; EE.UU. después; Europa solo si hay demanda medida) que enlazan a destino, temporada, costo y licencia.
- **Bloque "Siguiente paso"** al final de cada nota, según la etapa.
- **Sacar la licencia de Corrientes de la página de Brasil** (queda un resumen y el enlace).
- **`llms.txt`:** sección "Planificar el viaje" con el hub y las páginas de datos.
- No fusionar "dorado con amigos" con "costo de lodges": son intenciones distintas; ya se enlazan.

## Reglas
- Solo datos verificados con fecha; los aproximados, rotulados como aproximados.
- Las APIs de IA solo para una pregunta concreta (primero se lee con `investigar.yml`). Capturas de Google Maps/Flights del dueño valen como fuente fechada.
- **Ventana de medición (hasta el 5/12/2026):** las páginas nuevas pueden salir; las 8 páginas objetivo no se editan salvo correcciones de datos anotadas con fecha.
- Cada página nueva suma sus preguntas de control cuando existe (banco en inglés a partir de las búsquedas del análisis: costo desde EE.UU., visa, mejor mes de dorado en Corrientes, licencia de extranjero en Corrientes, "¿la licencia está incluida en el paquete?", cómo llegar de Buenos Aires a Corrientes).

## Lo aprendido de tres páginas que rankean bien en Gemini (8/11/2026)
Páginas leídas con `investigar.yml`: la guía de licencias de flyfishingarg.com, la guía "qué necesitás saber" de Pointer Outfitters y la guía "mejor época" de Patagonia Trashumante. Se usan como **mapa de temas**, no como texto: cada dato se verifica contra la fuente oficial.

| Página | Qué hace bien | Dónde la superamos o podemos |
| --- | --- | --- |
| Licencias (flyfishingarg) | Cuadro "de un vistazo"; permiso unificado vs. regiones aparte; tabla con dólares aproximados; dónde comprar; FAQ | Usa valores **2025/26** (residentes $ 10.000/20.000/40.000; trolling $ 20.000/40.000/80.000) y lo admite; Corrientes **sin precio**; Santa Cruz y Tierra del Fuego de la temporada pasada. Tenemos los 2026/27 oficiales de Patagonia, Santa Cruz y la fórmula de Corrientes |
| General (Pointer) | Recorrido completo y simple | Recomienda botas "de fieltro o de goma": el art. 22 del reglamento patagónico **prohíbe el fieltro**; dice "dorado mejor de septiembre a mayo" sin mencionar la veda |
| Mejor época (Trashumante) | Voz de guía; tres ventanas (noviembre–diciembre, mediados de diciembre–mediados de marzo, marzo–abril) con "para quién es"; el viento | Ya tenemos una nota con estructura equivalente (`cuando-pescar-trucha-patagonia`); lo que le falta es la voz de un guía. Podemos agregar el efecto del mes en el precio con datos propios (bandas de precio de Kau Tapen) |

**Lo que se decide con esto**
1. **Hub "Licencias de pesca para extranjeros en Argentina 2026/27"** (es/en/pt): cuadro de un vistazo + tabla por región. Filas verificadas hoy: Patagonia, Santa Cruz (portal oficial, 4/11) y Corrientes (litros × nafta, aprox.). Faltan: Tierra del Fuego (Anexo II de la Resolución 259/2026 en siped.ar, no abre) y las provincias del Litoral (hoy solo fuente secundaria). Dólares aproximados solo con un tipo de cambio fechado y con fuente.
2. **Nota de época de la trucha:** no se reescribe; se agrega "¿el mes cambia el precio?" (Kau Tapen: 26/12–9/1 US$ 13.950; 9/1–20/3 US$ 14.950; 20/3–17/4 US$ 12.950) y, si el dueño consigue 1 o 2 entrevistas con guías, la voz de "para quién es cada ventana".
3. **Cómo llegar a la Patagonia norte** (vuelos, conexiones, traslado): es un tema que esa competencia tiene como nota aparte y nosotros no.
4. Seguridad ("¿es seguro Argentina?") y listas genéricas de equipo y moscas: no los escribimos.

