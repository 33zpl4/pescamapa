# Plan del dorado y el surubí (16/11/2026)

Registro del programa. Cada paso se marca al terminarlo, con el ID de la corrida y la prueba. Nada se da por hecho sin esa prueba.

## Punto de partida (16/11/2026)

- En dos respuestas de Grok sobre dorado (dónde pescarlo y cuánto cuesta) **no aparece Pescamapa**. Nombra otros sitios y lugares (Pirá, Suindá, Karandá, Don Joaquín, SET). Encuadra los destinos por **estilo**: tamaño (Paraná superior, Itatí), pesca a la vista y fauna (Iberá), cantidad (Paraná medio, Esquina y Bella Vista).
- Cuando se lo pide por nombre, Grok nos describe bien pero como sitio de trucha en la Patagonia («dorado y otras regiones tienen menos énfasis»): leyó las páginas de trucha.
- Lo que ya tenemos en inglés: pilar «Golden dorado fishing in Argentina: a planning guide» (`/en/notes/golden-dorado-fishing-argentina/`), ficha de la especie, guías de Goya, Esquina, Paso de la Patria, Itatí, Itá Ibaté, Iberá y La Paz, nota de La Zona y notas de precios del dorado.
- Planificador, inglés: «golden dorado fishing argentina» 100–1.000 búsquedas por mes, competencia baja; «golden dorado fly fishing argentina», «argentina dorado», «dorado fish argentina», «golden dorado fishing buenos aires», «dorado fishing in buenos aires» de 10 a 100. Brasil: «pousadas» domina; se busca por nombre de operador.
- Lo que no publicamos: tamaños, rangos de precio ni costo total con vuelos y propinas sin fuente.

## Reglas para el programa

- Mensaje de cara al visitante: «Contanos qué buscás y te armamos la mejor opción para vos». Sin «el mejor precio» ni rankings sin fuente; sin jerga lodge/operador/agencia; sin comisiones; sin nombres de personas ni contactos.
- Dato concreto, fechado y con fuente; lo coincidente se publica atribuido; nada de «no pudimos…».
- Un script por cambio, con simulacro leído y luego aplicado; cada paso se verifica en vivo y con capturas que se miran.

## Pasos

| # | Paso | Entregable | Estado |
|---|---|---|---|
| 0 | **Inventario** (solo lectura): todas las páginas publicadas de dorado y surubí en es/en/pt: URL, título SEO, H1, palabras, foto, enlaces entrantes y salientes, idiomas que faltan | Tabla en este documento | Hecho 16/11 |
| 1 | **Brechas**: cruce con las preguntas de Grok (dónde, cuándo, cuánto, técnica, permiso, lugares) y con las frases del Planificador (EN, PT, ES) | Lista de brechas con prioridad | Hecho 16/11 |
| 2 | **Pilar del dorado (es/en/pt)**: respuesta corta arriba; tabla «qué destino te conviene» por estilo (tamaño, cantidad, pesca a la vista, fauna) con hechos ya atribuidos; variantes del Planificador en subtítulos y preguntas; enlaces a notas de costo, permiso y semana de lodge | Pilar actualizado y verificado | Hecho 16/11 (aplicado y leído en vivo; ver «Registro») |
| 3 | **Destinos en brecha**: decidir con fuentes públicas si hay una nota de dorado cerca de Buenos Aires (delta del Paraná, Entre Ríos) y si Bella Vista merece mención; si las fuentes no alcanzan, queda afuera y se anota por qué | Nota o decisión documentada | Hecho 16/11: no se publica (ver «Registro») |
| 4 | **Surubí**: reforzar la ficha de la especie; nota de la 50.ª Fiesta Nacional del Surubí (Goya, 28/4 al 2/5/2027) con fuentes; nombres en portugués («surubim», «pintado») | Nota y ficha actualizadas | Hecho 16/11 (aplicado; ver «Registro») |
| 5 | **Enlaces internos**: portada, fichas de especie y guías del Litoral enlazan al pilar y a las notas de costo; el pilar enlaza a todas | Grafo de enlaces antes y después | Hecho 16/11 (contenido aplicado; portada con el merge) |
| 6 | **Lugares que los asistentes nombran** (Pirá, Suindá, Karandá, Don Joaquín y los programas de SET): cuáles ya tienen ficha, cuáles tienen información pública reciente y un canal para escribirles; propuesta, no creación | Tabla para que el dueño decida | Hecho 16/11 (propuesta, sin crear fichas) |
| 7 | **Medición**: línea de base de hoy (Grok no nos nombra en dorado) y protocolo mensual con las preguntas en inglés y portugués | Sección en `docs/estado.md` | |
| 8 | **Verificación final**: cada URL en vivo, datos estructurados, hreflang, `llms.txt` y capturas miradas (escritorio y móvil) | Informe con IDs de corridas | |

## Registro

(Se completa paso a paso con la fecha, el PR, los IDs de las corridas y lo que no se verificó.)

### Paso 0: inventario (16/11/2026, solo lectura)

Script `inventario-dorado-surubi-2026-11-16` (solo lectura, sin `$aplicar`), corrido con `wp-script.yml` desde la rama de trabajo: **run 43841788924**, éxito; terminó con `Success: Inventario terminado (solo lectura)`. Selecciona las páginas publicadas que tratan dorado o surubí: 64 de 141 publicadas (28 notas, 21 guías, 6 fichas de especie; ver abajo). Enlaces contados sobre el **contenido** de las 141 páginas publicadas (notas, guías, especies, próximos, entrevistas); los enlaces que arma la plantilla (lista de notas en la guía y la ficha, guías en la ficha de especie, tarjetas de la portada) no entran en esa cuenta.

Palabras por idioma (es / en / pt), foto, enlaces que salen (S) y que entran (E) en la versión en español:

| Página (clave) | URL en español | Palabras es/en/pt | Foto | S / E (es) | Idiomas que faltan |
|---|---|---|---|---|---|
| **Pilar del dorado** `golden-dorado-guia` | `/notas/pesca-del-dorado-en-la-argentina/` | 2.026 / 1.794 / 1.991 | #140 | 14 / **2** | ninguno |
| La Zona (Salto Grande) | `/notas/la-zona-salto-grande-pesca-dorado/` | 1.656 / 1.432 / 1.619 | #140 (la del pilar) | 3 / 1 | ninguno |
| Cuándo pescar el dorado | `/notas/cuando-pescar-dorado/` | 1.454 / 1.268 / 1.392 | #121 | 6 / 5 | ninguno |
| Cuándo pescar el surubí | `/notas/cuando-pescar-surubi/` | 1.425 / 1.296 / 1.403 | sí (id enmascarado) | 6 / **2** | ninguno |
| Cuánto cuesta un viaje de dorado | `/notas/cuanto-cuesta-viaje-pesca-dorado/` | 1.127 / 978 / 1.092 | #531 | 4 / 10 | ninguno |
| Viaje de dorado con amigos | `/notas/viaje-pesca-dorado-con-amigos/` | 1.163 / 989 / 1.158 | #530 | 3 / 7 | ninguno |
| Tallas, cupos y vedas | `/notas/tallas-cupos-vedas/` | 729 / 676 / existe (no entró en la selección por el umbral de menciones) | #141 | **0** / 4 | ninguno |
| Cuánto cuesta un viaje a la Argentina | `/notas/cuanto-cuesta-viaje-de-pesca-argentina-2026-27/` | 1.911 / 1.720 / existe (corte del log) | #527 | 12 / 11 | ninguno |
| Cómo preparar un viaje (hub) | `/notas/como-preparar-un-viaje-de-pesca-a-la-argentina/` | 2.141 / 1.940 / 2.139 | #531 | 27 / 5 | ninguno |
| Semana de pesca en un lodge | `/notas/como-funciona-una-semana-de-pesca-en-un-lodge/` | 3.128 / 2.736 / 30xx (cifra enmascarada en el log) | #536 | 15 / 5 | ninguno |
| Pescar en Corrientes desde Brasil | `/notas/pesca-en-corrientes-desde-brasil/` | 1.435 / 1.206 / 1.368 | #535 | 4 / 7 | ninguno |
| Qué incluye un paquete | `/notas/que-incluye-paquete-pesca-argentina/` | 1.626 / 1.353 / 1.602 | #536 | 8 / 6 | ninguno |
| Paquetes de pesca en Argentina | `/notas/paquetes-de-pesca-en-argentina/` | 991 / 865 / 990 | #528 | 13 / **0** | ninguno |
| Ficha **dorado** | `/especies/dorado/` | 1.259 / 1.052 / 1.170 | **sin foto** | 1 / 7 | ninguno |
| Ficha **surubí** | `/especies/surubi/` | 832 / 717 / 770 | **sin foto** | 7 / **1** | ninguno |
| Guía Itatí | `/litoral/corrientes/pesca-en-itati/` | 1.390 / 1.273 / 1.402 | #523 | 11 / 5 | ninguno |
| Guía Esteros del Iberá | `/litoral/corrientes/pesca-en-los-esteros-del-ibera/` | 1.577 / 1.432 / 1.576 | #524 | 11 / 5 | ninguno |
| Guía Itá Ibaté | `/litoral/corrientes/pesca-en-ita-ibate/` | 1.323 / 1.211 / 1.325 | #526 | 11 / 5 | ninguno |
| Guía Paso de la Patria | `/litoral/corrientes/pesca-en-paso-de-la-patria/` | 626 / 582 / 600 | #168 | 2 / 11 | ninguno |
| Guía Goya | `/litoral/corrientes/pesca-en-goya/` | 7xx (enmascarada) / 647 / 711 | #50 | **0** / 6 | ninguno |
| Guía Esquina | `/litoral/corrientes/pesca-en-esquina/` | 625 / 553 / 614 | #85 | **0** / 10 | ninguno |
| Guía La Paz (Entre Ríos) | `/litoral/entre-rios/pesca-en-la-paz/` | 512 / 479 / 463 | #169 | 1 / 6 | ninguno |

(GitHub enmascara en el log (`***`) algunas cifras que coinciden con un secreto; esas celdas dicen «enmascarada». La selección es por página: una hermana en otro idioma puede quedar fuera si no llega al umbral de menciones, y la columna «idiomas que faltan» se calcula sobre la clave, no sobre la selección. Las notas de precios y de licencias fuera del dorado también entran en el inventario completo del log; aquí se listan las que tocan el dorado o el surubí de forma directa.) Los títulos SEO de las 64 páginas están en el log del run; ninguno supera 62 caracteres. Las notas más viejas (temporada del dorado y del surubí, tallas, fichas de especie) guardan el sufijo «| Pescamapa» dentro del campo del título SEO (53 a 62 caracteres con el sufijo).

**Idiomas que faltan: ninguno** en las 64 páginas (todas existen en es, en y pt).

**Hallazgos del inventario**
1. El **pilar tiene 2 enlaces entrantes en el contenido** (el hub y La Zona) frente a 14 salientes; la portada y las fichas lo enlazan por plantilla o no lo enlazan (se comprueba en el paso 5 con HTML en vivo).
2. Las **fichas de especie dorado y surubí no tienen foto** (columna «SIN FOTO»).
3. **Enlaces cruzados de idioma**: las notas de temporada del dorado y del surubí en portugués enlazan la guía de Esquina en español (`/litoral/corrientes/pesca-en-esquina/`) y la guía de Esquina en portugués existe (`/pt/litoral/corrientes/pesca-em-esquina/`); la ficha de surubí en portugués (`/pt/especies/surubim/`) figura sin enlaces entrantes porque la nota portuguesa de temporada enlaza `/pt/especies/surubi/` (slug que no es el de la ficha).
4. Las guías de Goya y Esquina no tienen enlaces salientes dentro del contenido (solo los de la plantilla); las notas de costo y el pilar no las enlazan desde esas guías.
5. `paquetes-de-pesca-argentina` no tiene enlaces entrantes en el contenido.


### Paso 1: brechas (16/11/2026)

Cruce del inventario con las seis preguntas de Grok (dónde, cuándo, cuánto, técnica, permiso, lugares) y con las frases del Planificador. Fuentes de la cobertura: el inventario del paso 0 y el contenido de los scripts que armaron cada página.

| Pregunta o frase | Qué tenemos hoy | Brecha | Prioridad | Paso |
|---|---|---|---|---|
| **Dónde** (Grok encuadra por estilo: tamaño, cantidad, pesca a la vista, fauna) | Pilar con una tabla por tramo (Alto Paraná, Paraná medio) y siete guías. Ninguna página compara por estilo | Falta la comparación «qué destino te conviene». Las guías no traen el estilo atribuido (solo Itá Ibaté dice «pesca a la vista» y Suindá/Pirá/Karandá lo dicen en sus páginas, leídas el 13/11) | 1 | 2 |
| **Cuándo** | Nota de temporada (dos ventanas, es/en/pt) y filas de temporada en cada guía | El pilar la enlaza; falta la pregunta visible en el pilar | 2 | 2 |
| **Cuánto cuesta** | `precio-viaje-dorado-lodge`, `viaje-dorado-amigos-precios`, costo total 2026/27 | El pilar no los pone en una lista visible junto a la pregunta; Goya y Esquina no los enlazan | 1 | 2 y 5 |
| **Técnica** (mosca, spinning) | Sección de equipo en el pilar (15/11) y las respuestas de cada guía | Falta la pregunta «¿se puede pescar con mosca?» en el pilar, atribuida a las páginas de los lugares | 2 | 2 |
| **Permiso** | `licencia-pesca-extranjeros-argentina-2026-27`, `permiso-de-pesca-argentina-paso-a-paso` | El pilar explica la licencia de Corrientes pero no manda a la nota paso a paso | 2 | 2 |
| **Lugares** (Pirá, Suindá, Karandá, Don Joaquín, SET) | Suindá, Itatí Lodge, Pirá, Uguay, La Alondra'i y Dorados on the Fly con tarjeta en las guías de Itatí, Iberá e Itá Ibaté | Karandá (Paraná medio, hotel en Bella Vista), Don Joaquín y los programas de SET como programas: sin ficha | 3 | 6 (propuesta) |
| «golden dorado fishing argentina» (100 a 1.000) | Pilar en inglés, título «Golden dorado fishing in Argentina: a planning guide» | Subtítulos y preguntas con las variantes | 1 | 2 |
| «golden dorado fly fishing argentina» (10 a 100) | Equipo y respuestas por guía | Pregunta propia en el pilar | 2 | 2 |
| «argentina dorado», «dorado fish argentina» (10 a 100) | Ficha de especie con récord, tamaño y dónde vive | El pilar no responde «qué es el dorado» arriba, con la ficha enlazada | 2 | 2 |
| «golden dorado fishing buenos aires», «dorado fishing in buenos aires» (10 a 100) | Nada | Sin fuente fechada de reglas ni lugares cerca de Buenos Aires (paso 3). Sí hay un dato fechado y atribuido: el vuelo de una hora de Aeroparque a Corrientes o Resistencia según los lugares | 2 | 2 y 3 |
| Portugués: «pousadas» domina, se busca por nombre | Pilar en portugués, guías con «pousada» en el título | Variantes en subtítulos y preguntas del pilar en portugués | 2 | 2 |
| **Surubí** | Ficha (832 palabras), nota de temporada, tamaño y récord con fuente (13/11) | Una nota de la 50.ª Fiesta Nacional del Surubí (28/4 al 2/5/2027), nombres en portugués en la ficha, más enlaces entrantes (1) | 1 | 4 y 5 |
| Enlaces internos | Pilar con 2 entrantes en el contenido; fichas de especie con 7 (dorado) y 1 (surubí) | Grafo desigual: portada, fichas y guías del Litoral no mandan al pilar ni a las notas de costo | 1 | 5 |
| Dorado cerca de Buenos Aires, Bella Vista | Nada | Decisión con fuentes leídas (paso 3) | 3 | 3 |
| Fotos de las fichas de especie | Ninguna de las seis tiene foto | Decisión del dueño (las provisorias de Commons están anotadas en `docs/estado.md`) | 3 | pregunta al dueño |

Dos defectos de enlaces que se arreglan en el paso 5: las notas de temporada del dorado y del surubí en portugués enlazan la guía de Esquina en español; y enlazan `/pt/especies/surubi/` cuando la ficha en portugués vive en `/pt/especies/surubim/`.

**No se publica** (sin fuente leída): tamaños, rangos de precio ni «costo total con vuelos y propinas» que dio Grok; el orden de «mejor destino»; nada de «el mejor» ni ahorro.

### Paso 2: pilar del dorado es/en/pt (16/11/2026)

- **Script** `pilar-dorado-2026-11-16`, PR #200. Simulacro: run **43843828501** (3 notas, FAQ de 8 preguntas en cada idioma, CTA viejo reemplazado, línea «no cubre» reescrita). Aplicado: run **43844068555**, tres `VERIFIED` (es #179, en #138, pt #180), slugs sin cambios.
- **Qué quedó en el pilar** (los tres idiomas):
  - Tabla «¿Qué destino te conviene? Tamaño, pesca a la vista, fauna y cantidad»: seis filas (peces grandes, pesca a la vista, fauna y paisaje, cantidad de peces, surubí y riachos, cerca de Buenos Aires). Cada celda atribuye a la página que lo dice y su fecha de lectura: las páginas de Suindá y de Pirá (Nervous Waters), la de Dorados on the Fly y la lista de equipo de Globe-Truiteur (27/7/2026), más el documental de Flylords que la nota de temporada ya citaba. «Cantidad de peces» dice que las páginas leídas no dan peces por día y que todavía no publicamos una cifra: lo que dijo Grok sobre el Paraná medio no tiene fuente leída.
  - FAQ visible de 8 preguntas (grupo `pm-faq`: el JSON-LD `FAQPage` sale del mismo marcado): qué es el dorado y dónde vive, dónde se pesca, cuándo, con mosca, cerca de Buenos Aires, cuánto cuesta, licencia, si se puede quedar con el pez. Llevan las variantes del Planificador («golden dorado fishing argentina», «golden dorado fly fishing argentina», «argentina dorado», «dorado fish argentina», «golden dorado fishing buenos aires», «dorado fishing in buenos aires»; en portugués «pousadas» y «pesca à vista»).
  - «Para seguir con tu viaje»: lista a las notas de costo del dorado, dorado con amigos, costo total, permiso paso a paso, semana de pesca y hub «cómo preparar un viaje».
  - CTA: «Contanos qué buscás y te armamos la mejor opción para vos» con botón a `?especie=dorado`; reemplaza el párrafo anterior.
  - «Operadores» pasa a «lugares» en las frases que el script toca; la línea «lo que no cubre» dice lo que de verdad pasa con el Delta.
  - 5 fuentes nuevas en la nota (sin duplicar la de Suindá, que ya estaba) y un dato clave nuevo.
- **Prueba en vivo** (investigar.yml, carpeta `dorado-pilar-despues-20261116-0959`): las tres páginas en 200, datos estructurados `Article, BreadcrumbList, FAQPage`, título SEO de 54, 52 y 49 caracteres, descripción de 134, 147 y 127, hreflang es/en/pt-BR/x-default, FAQ visible igual a los H3 de las preguntas (8). Capturas (`capturas.yml`, run 43844465120, rama `capturas`, prefijo `2026-11-16-pilar-`) miradas en escritorio y móvil: la tabla se ve bien en escritorio; **en el móvil la tabla de tres columnas desbordaba (scroll lateral con un mínimo de 560 px)**: se corrige en este mismo PR con la clase `pm-estilo` (cada fila pasa a tarjeta en pantallas de 640 px o menos) y el script del paso 5 se la agrega a la tabla ya publicada.
- **No verificado**: el botón superior de la nota todavía dice «les preguntamos a los operadores que encajan» hasta que se despliegue el cambio de `nota.cta` de este PR; los tamaños de las páginas de los lugares son lo que ellas afirman, no una medición nuestra; el resto de «operadores» de la sección de equipo (del 15/11) no se tocó.

### Paso 3: destinos en brecha (16/11/2026)

Lecturas con `investigar.yml` (corrida `dorado-brecha-20261116-0944`, 10 páginas; carpeta de la rama `investigacion`) y una búsqueda de URLs.

- **Dorado cerca de Buenos Aires (Delta del Paraná).** Lo único leído que nombra el dorado en el Delta es una página de turismo de Welcome Argentina sobre el Río de la Plata y el Delta, **sin fecha de publicación visible**: dice que en verano llegan dorado, surubí y patí a los pozos del Río de la Plata y a ríos como el Paraná de las Palmas, «aunque no alcanzan los portes» de aguas arriba, y nombra pesqueros del Delta que sirven sobre todo a pejerrey, boga y tararira. No hay una fuente con fecha sobre reglas de la provincia de Buenos Aires para el dorado, sobre lugares que lo pesquen ni sobre tarifas. Nervous Waters tiene una página de un lodge en el Delta (Delta Lodge, ya leída el 13/11), pero habla de su programa y de las estaciones, no de reglas ni de números. **Decisión: no se publica una nota.** Lo que sí se publicó: la pregunta «¿Se puede pescar dorado cerca de Buenos Aires?» del pilar, con el vuelo de una hora de Buenos Aires a Corrientes o Resistencia (páginas de los lugares) y esa única línea del Delta atribuida y rotulada «sin fecha».
- **Entre Ríos.** El Heraldo informa (agosto y diciembre de 2024) que Entre Ríos declaró el dorado de interés turístico y prohibió su pesca comercial, su acopio, venta, tenencia y tránsito, y que permite la pesca «solamente cuando se realice en forma deportiva y con devolución obligatoria»; es prensa sobre un proyecto y su sanción, no el texto de la ley. Por la regla del dueño (reglamentos solo con fuente oficial) **no se publica**; queda como pregunta para el dueño (texto de la ley o su número, para la guía de La Paz).
- **Veda de la cuenca del Paraná (Notife).** La nota que da la veda del dorado deportivo «del 1 de octubre al 15 de enero» y del surubí «del 1 de noviembre al 31 de diciembre» no trae fecha ni nombra la provincia: **no se usa**.
- **Bella Vista (Corrientes).** Las lecturas son una crónica de turismo sin fecha (Welcome Argentina: un guía sale desde el muelle viejo a trolling, y una regla de talla que ya no coincide con la Disposición 853/22) y una nota de Revista Aire Libre de 2018 sobre kayak. La nota de La Nación que la búsqueda ligaba a Bella Vista no la nombra. Nervous Waters tiene un programa de dorado con hotel en Bella Vista (Karandá, ya leído el 13/11). **Decisión: no se publica una nota ni una mención en el pilar**; Karandá queda en la propuesta del paso 6.

### Paso 4: surubí, Fiesta Nacional 2027 y nombres en portugués (16/11/2026)

- **Lecturas** (`investigar.yml`, rama `investigacion`): `surubi-fiesta-20261116-0943` (dos notas de Elonce del 10/8 y del 23/8/2026, Canal 26 del 15/4/2026, Perfil del 4/5/2026, la revista oficial de la Fiesta de 2025 de la Municipalidad de Goya, el boletín municipal del 16/4/2026), `surubi-nombres-20261116-0945` (FishBase) y `surubi-nombres-pt-20261116-0954` (Embrapa Pesca e Aquicultura, diciembre de 2015). **No se encontró un sitio oficial del evento** con el buscador; las fechas de 2027 salen de Elonce (una sola fuente; la nota del 10/8/2026 trae las fechas, el cupo y los premios, la del 23/8/2026 el agotamiento del cupo).
- **Script** `surubi-fiesta-2026-11-16`, PR #200. Simulacro: run **43845636135** (3 notas, 6 datos, 7 fuentes, 5 FAQ cada una; title SEO de 55, 58 y 53 caracteres, descripción de 150, 146 y 143; 3 fichas a editar). Aplicado: run **43846198507**: notas `fiesta-nacional-del-surubi-2027` es **#680**, en **#681**, pt **#682**, y las fichas del surubí es #18, en #16, pt #20, todo `VERIFIED`. Una línea final de ese run dice `FALLÓ: nota es lee especie ?…`: era la caché de `pm_buscar` (se cargó antes de crear las notas), no un fallo de datos; el script ahora busca la nota nueva directo y el paso de test lo exige.
- **La nota** (`/notas/fiesta-nacional-del-surubi-2027/`, `/en/notes/national-surubi-festival-goya-2027/`, `/pt/notas/festa-nacional-do-surubim-goya-2027/`): tipo «datos», especie surubí, destino Goya, foto de la guía de Goya con su crédito. Respuesta corta arriba; 50.ª edición del 28/4 al 2/5/2027; inscripción web (contado o cuotas, mínimo dos pescadores por equipo); tope de 1.400 equipos (unos 4.200 pescadores); premios de 1.000 millones de pesos; 1.413 registros en unas dos horas y unos 300 equipos en lista de espera, con la ampliación en análisis (todo atribuido a Elonce con fecha); cómo es el concurso según la revista oficial de 2025 (embarcado, 12 zonas de 7 a 8 km, «sin bajarse durante quince horas», en Goya cada pieza se devuelve); la edición 2026 (27/4 al 3/5, unas 1.400 embarcaciones según Perfil, pieza mayor de 119 cm, ganó un equipo de Santa Fe); «si querés ir» con el mensaje nuevo y el botón a `?destino=goya&especie=surubi`; nombres del pez; FAQ visible de 5 preguntas.
- **Ficha del surubí** (es/en/pt): sección «Nombres del surubí y dónde vive» (en portugués «surubim» y «pintado» según Embrapa, con los otros nombres que da; FishBase: agua dulce, de fondo, migra por el río, cuencas del São Francisco y del Paraná, se alimenta sobre todo de peces), un párrafo con enlace a la nota de la Fiesta y dos preguntas más en la FAQ (4 pasan a 6): cómo se dice surubí en portugués y cuándo es la Fiesta 2027. 2 fuentes nuevas (Embrapa y Elonce).
- **Lo que no se escribió**: nombres de personas ni de equipos, el puntaje de la revista de 2025 (el texto del PDF sale con columnas mezcladas y la lectura es ambigua), precios de entradas, y cualquier cosa sobre cuántos peces se pescan. Hay un dato más que no se usa: la prensa brasileña informó en 2022 que el pintado entró en la lista nacional de especies amenazadas y que su pesca está prohibida en Brasil; no se leyó la norma ni su vigencia en 2026.
- **No verificado**: la nota de Elonce de 2027 es una sola fuente; la fecha y el cupo pueden cambiar; los 4.200 pescadores son el cálculo de Elonce (3 por equipo), no un registro; la verificación en vivo (200, datos estructurados, hreflang, `llms.txt`, capturas) se hace en el paso 8 una vez desplegado el tema.

### Paso 6: lugares que los asistentes nombran (16/11/2026, propuesta, no se creó ninguna ficha)

Estado de las fichas leído en vivo con `listar-prestadores-2026-10-24` (solo lectura, run **43846689869**, 62 fichas internas; no imprime contactos). Información pública: lo ya leído en `docs/investigacion/` y en la rama `investigacion` (carpetas `corrientes-1-20261113-2308`, `corrientes-3-20261113-2316`, `esquina`), sin lecturas nuevas para este paso. «Canal»: si el lugar o su grupo tiene una vía pública para escribirles, sin copiar datos de contacto.

| Lugar que nombra Grok | Ficha hoy | Información pública reciente | Canal público | Propuesta |
|---|---|---|---|---|
| **Suindá** (Nervous Waters, Itatí) | **Sí**: tarjeta #460 en la guía de Itatí, «listado con información pública», con la tarifa 2026-27 | Página leída el 13/11/2026: tarifas por noches, temporada «octubre a mayo», qué incluye y qué no | Sí: la página del grupo enlaza un formulario «Contact us» | Nada que crear. Falta que alguien le escriba para pasar de «información pública» a consentimiento (decisión de contacto del dueño) |
| **Pirá** (Nervous Waters, Esteros del Iberá) | **Sí**: tarjeta #465 en la guía del Iberá, con la tarifa 2026-27 | Página leída el 13/11/2026: tarifas, temporada «todo el año», aguas y tipo de pesca | Sí: el mismo formulario del grupo | Igual que Suindá |
| **Karandá** (Nervous Waters, Paraná medio; hotel en Bella Vista) | **No** | Página leída el 13/11/2026: «RATES 2027», temporada de junio a diciembre, licencia aparte | Sí: el mismo formulario del grupo | No crear todavía: una ficha cuelga de una guía de destino y Bella Vista no la tiene ni alcanzan las fuentes para hacerla (paso 3). Decisión del dueño: ¿guía de Bella Vista con ese único lugar, o esperar a más fuentes? |
| **Don Joaquín River Lodge** (Esquina) | **Interna, no pública**: #39, marcada «datos de ejemplo» desde el 20/10/2026 | `docs/investigacion/esquina.md` (20/10/2026): un listado de agencia de terceros con USD 5.825 por una salida del 14 al 21/11/2026 y otro, sin fecha, con USD 4.750 («no usar»); su web propia e Instagram figuran «PENDIENTE» | Solo por agencias que lo venden; no se encontró un canal propio | No publicar. Si el dueño quiere la ficha, primero leer su página propia con `investigar.yml` (si existe) y recién ahí pasarla de «ejemplo» a «información pública», sin precio hasta tener uno suyo con fecha |
| **SET Fly Fishing: Itatí Lodge** | **Sí**: tarjeta #461 en Itatí, sin tarifa | Páginas leídas el 13/11/2026 (temporada «septiembre a mayo», evitar enero y febrero, depósito y pago) | Sí: el sitio de SET (formulario o correo públicos; no se probaron) | Nada que crear |
| **SET: La Alondra'i** (Esteros del Iberá) | **Sí**: tarjeta #466 en el Iberá, sin tarifa | Páginas de SET leídas el 13/11/2026 («septiembre al final de mayo»; pesca de superficie, campamentos en el humedal) | Sí (el mismo sitio de SET) | Nada que crear |
| **SET: Jetu'u Cabañas de Esteros** (humedal del Isoró y Paraná medio, la zona de la guía de Goya) | **No** | Solo la página de SET «3 lodges, 3 experiencias» (13/11/2026): «mucha acción y cantidad de capturas», ideal para quienes empiezan, a pie del agua; **su página propia dio 404 ese día** (`corrientes-3-20261113-2316/09`) | Sí (el sitio de SET) | **Candidata más fuerte**: es lo que cubre el vacío de «cantidad» de Grok en el Paraná medio y cae dentro de una guía que ya existe (Goya). Si el dueño la aprueba: tarjeta «información pública» en Goya con lo que dice esa página, sin tarifa ni foto, y leer antes su página propia (otra corrida) |
| Otros nombrados por las páginas leídas y sin ficha: **Uguay** (ya tiene), **Dorados on the Fly** (ya tiene, #471 en Itá Ibaté) | Sí | 13/11/2026 | Sí (páginas propias) | Nada que crear |

**Resumen:** de lo que Grok nombra, tienen ficha pública Suindá, Pirá, el Itatí Lodge y La Alondra'i de SET; no la tienen Karandá, Don Joaquín (hay una interna de ejemplo) y el programa Jetu'u de SET. Ninguna se crea sin decisión del dueño. Todas las fichas que ya existen están marcadas «sin verificar» y ninguna tiene consentimiento: no hay acuerdo comercial con ninguno de estos lugares (la regla de comisiones sigue siendo no publicarlas).

### Paso 5: enlaces internos (16/11/2026)

- **Script** `enlaces-dorado-surubi-2026-11-16`, PR #200. Simulacro: run **43846360490** (51 cambios). Aplicado: run **43846523903**, todos `VERIFIED`. Un párrafo final «Para planificar: …» (es/en/pt, solo los enlaces que la página todavía no tiene y nunca a otro idioma) en las siete guías del Litoral (Goya, Esquina, Paso de la Patria, La Paz, Itatí, Itá Ibaté, Esteros del Iberá), la ficha del dorado y las notas de costo, dorado con amigos, temporada del dorado y del surubí, costo del viaje, tallas y permiso. Además corrige dos defectos del inventario: las dos notas en portugués (temporada del dorado y del surubí) pasan de la guía de Esquina en español a la de portugués, y la del surubí enlaza la ficha en portugués por su dirección actual (`/pt/especies/surubim/`; la anterior redirigía, así que no estaba rota). Y le pone la clase `pm-estilo` a la tabla del pilar ya publicada (tarjetas en el móvil).
- **Portada**: atajo al pilar junto al de la ficha del dorado (`inicio.php` y `inicio.atajo.dorado.guia` en los tres idiomas), solo cuando la nota existe en ese idioma. Llega con el merge y el deploy del tema; lo cubre el paso de test del PR y se comprueba en vivo en el paso 8.
- **Botón de las notas** (`nota.cta`): «¿Querés pescar esto? Contanos qué buscás y te armamos la mejor opción para vos: sin costo, y te respondemos en 48 horas.» (y sus versiones en inglés y portugués). Reemplaza «les preguntamos a los operadores que encajan…» en todas las notas; el botón ya llevaba `?especie=` y `?destino=` de cada nota.
- **Grafo, antes y después** (enlaces dentro del contenido, versión en español; inventario `inventario-dorado-surubi-2026-11-16`, antes run 43841788924, después run **43846998758**; los tres idiomas dan lo mismo):

| Página | Entrantes antes → después | Salientes antes → después |
|---|---|---|
| Pilar del dorado | 2 → **16** | 14 → 21 |
| Ficha del dorado | 7 → 7 | 1 → **4** |
| Ficha del surubí | 1 → 2 (la Fiesta) | 7 → 7 |
| Guía de Goya | 6 → 7 | 0 → **6** |
| Guía de Esquina | 10 → 8 (dos notas en portugués dejaron de apuntar a la guía en español) | 0 → **5** |
| Costo de un viaje de dorado | 10 → 16 | 4 → 5 |
| Tallas, cupos y vedas | 4 → 4 | 0 → 1 |
| Nota de la Fiesta 2027 (nueva) | — → 2 | — → 4 |

- **Queda fuera**: `paquetes-de-pesca-argentina` sigue sin enlaces entrantes en el contenido (no es del dorado; lo enlaza la plantilla); la ficha del surubí sigue con pocos entrantes (2): sus guías la enlazan por la plantilla de «Guías que pescan surubí». Los enlaces de plantilla no están en esta cuenta.
- **No verificado**: el HTML en vivo de las guías (se mira en el paso 8) y que los enlaces de plantilla de las guías y de las fichas sigan donde estaban.
