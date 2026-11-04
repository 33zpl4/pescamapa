# PESQA (pesqa.ar): qué hace, dónde es débil y qué tomamos (4/11/2026)

Lectura con `investigar.yml` (rama `investigacion`, carpeta `pesqa-20261104`): páginas `/lugar/rio-gallegos`, `/lugar/rio-quilquihue`, la portada y el sitemap. Sale de lo leído ese día; no medimos su tráfico ni si los asistentes la citan (eso es la línea base de `docs/preguntas-llm.md`).

## Qué es

Plataforma Next.js de Buenos Aires (CUIT en el pie, hecha por una agencia), en es-AR, pt-BR y en-US con hreflang. La portada dice 244 destinos, 554 pesqueros y 984 guías. El sitemap tiene **731 URLs** (con fecha de modificación entre junio y octubre de 2026): `/lugar/<destino>`, `/pescar/<especie>` (más de 100 páginas de especie por zona), `/especies`, `/pesquedex`, `/guias`, `/alojamientos`, `/lodges`, `/clubes`, `/capturas`, `/el-pique`, `/blog` (la revista) y las puertas de negocio `/para-negocios`, `/soy-guia`, `/tengo-alojamiento`. Modelo de negocio: fichas gratis para atraer y planes pagos, reservas con "pago y seña protegidos", eventos para empresas.

## Anatomía de una página de lugar (Río Gallegos)

1. Titular y descripción cortos, con la provincia.
2. Franja de actualidad: "Picando ahora · Trucha marrón", clima del lugar (Open-Meteo), "Este mes pica trucha marrón. Mosca. 6 guías cerca".
3. Tres párrafos de descripción, y debajo un bloque con **Veda y temporada, Modalidades, Permiso, Acceso**.
4. **Barra de meses por especie**: en temporada (NOV a ABR), pico (MAR a ABR), mes actual resaltado.
5. "Equipo recomendado" (spinning y mosca, genérico).
6. "Quién te lleva": guías, alojamiento, náutica, clubes, con distancia y "Consultar".
7. Reportes de pescadores y opiniones (vacíos), e invitación a sumar el negocio ("ya hay 36 negocios cargados en la zona, la ficha es gratis").
8. "Cerca de acá" y 11 preguntas frecuentes con FAQPage; datos estructurados TouristAttraction y BreadcrumbList.

## Lo que hacen bien y vale copiar

- **Todo está atado al mes.** "Este mes pica…", la barra de meses y la pregunta "¿Se puede pescar en X ahora, en noviembre?" responden lo que alguien pregunta hoy. Es contenido que un asistente cita y que parece fresco.
- **Cobertura programática:** 244 destinos contra nuestras guías hechas a mano. La escala es su ventaja.
- **Cruces internos:** lugar a especie a provincia ("Trucha marrón en Santa Cruz"), y "Cerca de acá".
- **El bucle de negocio** en cada página: "¿Alguno es tuyo? Sumate gratis" con el número de negocios ya cargados como prueba social.
- Datos estructurados y FAQPage en cada lugar.

## Donde es débil (nuestra oportunidad)

- **Reglas viejas.** El 4/11/2026 la página de Río Gallegos todavía dice "Cierre el 1 de mayo de 2026" y "ventana del 1 al 28 de febrero de 2026". Es la temporada 2025/26, y la 2026/27 abrió el 1/11 con otras fechas (cierre 30/5/2027, ventana del 1 al 28/2/2027). Nosotros publicamos las nuevas, del Anexo I oficial. Es nuestra ventaja: fecha y fuente.
- **"6 guías cerca" no son guías de pesca.** Son comercios y alojamientos de la ciudad tomados de OpenStreetMap (una tienda patagónica, hospedajes). No figura ninguno de los lodges reales del río (Karku, Las Buitreras). Los "28 alojamientos" son hoteles de la ciudad.
- **Bloques genéricos.** "Equipo recomendado" habla de spinning y de líneas flotantes para un río que su propia ficha marca como solo mosca.
- **Sin precios**, solo "Consultar", y el permiso sin montos.
- **Foto de archivo** ("Foto de ambiente… vía Pexels", y en otros lugares "no es una foto del lugar").
- **Reportes y opiniones vacíos** en las dos páginas que leímos.
- Admiten el límite de su dato oficial: "El texto oficial se corta ahí". Es honesto, pero deja la veda incompleta.

## Qué tomamos, en orden

1. **Fechas de temporada estructuradas y una barra de meses.** Hoy `temporada` es texto libre. Agregar `abre`, `cierra` y `pico` (este último solo cuando un operador lo afirma, con su nombre) permite mostrar la barra, escribir "¿Se puede pescar ahora?" con la fecha de hoy y que la respuesta nunca quede desfasada. Distinguimos **temporada legal (norma)** de **mejor época (operadores)**, que PESQA mezcla.
2. **Pregunta "¿Se puede pescar en <lugar> ahora?"** como FAQ calculada contra esas fechas. Es la consulta de mayor intención.
3. **"Cerca de acá"** entre guías cercanas (y con la altura del río donde hay estación).
4. **Prueba social en la puerta de negocios:** mostrar cuántas fichas hay en la zona en cada página por ciudad (ya mostramos la cantidad; falta la invitación "¿alguno es tuyo?").
5. **Página de especie por provincia** (`trucha marrón en Santa Cruz`), cuando haya guías suficientes.

## Qué no copiamos

- Fichas armadas con puntos de OpenStreetMap como si fueran guías: rompe nuestra regla de listar solo lo leído y atribuido.
- Texto genérico escrito sin fuente ("equipo recomendado").
- Gamificación (medallas, láminas) y planes pagos: no ahora.
- Escala por escala. 731 URLs con datos viejos pierden contra menos páginas con la norma vigente y la fuente; el trabajo es volver barato verificar, no publicar más rápido.

## Para vigilar

Que los asistentes la citen o no (preguntas de control 11 a 20), y cuándo actualiza su temporada 2026/27. Si lo hace, la ventaja de las fechas se acorta y quedan los precios fechados, las tarifas de los lodges y la lectura de la norma.
