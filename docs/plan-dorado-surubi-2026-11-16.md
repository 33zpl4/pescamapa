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
| 2 | **Pilar del dorado (es/en/pt)**: respuesta corta arriba; tabla «qué destino te conviene» por estilo (tamaño, cantidad, pesca a la vista, fauna) con hechos ya atribuidos; variantes del Planificador en subtítulos y preguntas; enlaces a notas de costo, permiso y semana de lodge | Pilar actualizado y verificado | |
| 3 | **Destinos en brecha**: decidir con fuentes públicas si hay una nota de dorado cerca de Buenos Aires (delta del Paraná, Entre Ríos) y si Bella Vista merece mención; si las fuentes no alcanzan, queda afuera y se anota por qué | Nota o decisión documentada | |
| 4 | **Surubí**: reforzar la ficha de la especie; nota de la 50.ª Fiesta Nacional del Surubí (Goya, 28/4 al 2/5/2027) con fuentes; nombres en portugués («surubim», «pintado») | Nota y ficha actualizadas | |
| 5 | **Enlaces internos**: portada, fichas de especie y guías del Litoral enlazan al pilar y a las notas de costo; el pilar enlaza a todas | Grafo de enlaces antes y después | |
| 6 | **Lugares que los asistentes nombran** (Pirá, Suindá, Karandá, Don Joaquín y los programas de SET): cuáles ya tienen ficha, cuáles tienen información pública reciente y un canal para escribirles; propuesta, no creación | Tabla para que el dueño decida | |
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
