# La Zona (Salto Grande): lugares para listar, revisión del 17/11/2026

Pedido del dueño (17/11/2026): hay búsquedas reales de «la zona fishing lodge», «la zona lodge argentina», «golden dorado la zona argentina» y «la zona dorado fishing», y la nota «La Zona (Salto Grande)» no tiene ningún lugar. **Resultado del 17/11: no se creó ninguna ficha (ver la actualización del 18/11 arriba, que sí crea una).** Ningún candidato alcanza el criterio (página propia leída, con contenido fechado y actual, y oferta clara para pescadores). Este documento dice por qué y qué haría falta.

## Actualización del 18/11/2026: La Zona Lodge está activo (dato del dueño) y decisión de dónde vive la ficha

**Dato nuevo y fuente.** El dueño leyó el 18/11/2026 la cuenta de Instagram verificada del lugar (más de 17.000 seguidores) y pasó capturas y las láminas del carrusel. **Fuente: publicación conjunta de la cuenta del lugar con un operador brasileño de promoción, fechada el 15/10/2026 («hace 4 semanas» al 18/11/2026), ubicación Concordia, Entre Ríos. Leída por el dueño el 18/11/2026; sin enlace en el sitio** (en las páginas va como texto: «Instagram del lugar, octubre de 2026»). El sitio web del lugar (lazonalodge.com) está caído por certificado vencido; las capturas no están en el repo (no las tengo; no se verificó nada contra imágenes).

Qué dice la publicación (texto en portugués y láminas, transcripto por el dueño; sin teléfonos ni correos, que **no se guardan en ningún archivo**):

- Ubicación: Concordia, a orillas del río Uruguay, «na exclusiva reserva de Salto Grande»; «destino ideal para quem busca grandes capturas sem abrir mão do conforto»; «região famosa pelos grandes dourados»; atención personalizada.
- Régimen all inclusive: hospedaje, comidas, bebidas, jornadas de pesca e infraestructura de la estadía; traslado diario de la posada al río Uruguay.
- Especies que menciona: dorado, surubí, tararira, patí, armado, pacú y boga.
- Habitaciones: apartamentos amplios, climatizados y preparados para grupos (en las fotos, camas individuales con mesas de luz).
- Comidas: desayuno, almuerzo y cena completos con bebidas (en las fotos, buffet de desayuno variado y asado a la estaca y a la parrilla).
- Instalaciones: piscina al aire libre, salón de estar y juegos con mesa de billar y vista panorámica, comedor amplio con ventanales, jardines con palmeras y mesas con sombrillas.
- **Solo en este documento interno:** las imágenes llevan el logo del operador brasileño de promoción. No va en la ficha ni en ninguna página.
- Historias más viejas de la cuenta (de hace ~31 y ~40 semanas): un «buffet» con pensión completa, media pensión o solo desayuno, y una **cabaña de alquiler temporario en Concordia** (3 dormitorios, piscina, quincho): es otra oferta, no se mezcla con el lodge y no se publicó.
- **No figura:** precio, temporada, botes, guías, días de pesca, qué permiso cubre la pesca en La Zona. No se publica nada de récords, tamaños, cantidad de botes ni reapertura (no verificado). «Uno de los mejores puntos del mundo» **no** se publica como afirmación nuestra: solo lo que dice el texto del post, atribuido («región famosa por los grandes dorados»).

**Qué cambia respecto del 17/11.** La tabla de abajo decía «sin prueba de que esté activo» para La Zona Fishing Lodge. Ahora hay una publicación fechada (octubre de 2026) del propio lugar que lo muestra activo y describe la oferta. El permiso argentino de La Zona **sigue sin resolverse en lo público** y la publicación no dice qué permiso cubre la pesca: la ficha lo dice («no dice qué permiso cubre la pesca en La Zona») y la guía no afirma ni niega nada. Sigue sin leerse la web (caída) y River Plate Dorados sigue sin alcanzar el criterio.

### Decisión A o B: dónde puede vivir la ficha

Se miraron las plantillas el 18/11/2026 (`vistas/destino.php`, `vistas/guias.php`, `vistas/paquetes.php`, `parciales/guias-especie.php`, `vistas/nota.php`):

| Opción | Qué exige | Resultado |
|---|---|---|
| **B. Sección con la ficha dentro de la nota de La Zona** | Un mecanismo de tarjetas en las notas | **No existe.** `nota.php` solo imprime el cuerpo, las fuentes y, si la nota tiene especie, `guias-especie` (una línea por ciudad con guía, que a su vez solo lee ciudades que ya son destinos). Las tarjetas (`parciales/prestador.php`) salen únicamente en la ficha de un destino, en la página de guías de una ciudad y en el paquete. Hacer B es cambiar plantillas (un parcial nuevo, estilos, un destino virtual en el selector de la ficha) y desplegarlo sin poder verlo desde el sandbox: más invención que A |
| **A. Guía nueva «Pesca en Concordia y La Zona»** (destino `concordia`) | Contenido de guía (reglamento, permiso, licencia) salido de fuentes ya leídas | **Se puede sin inventar.** Cada dato sale de lo ya publicado en la nota de La Zona (CARU 27/2004, 63/2026, 111/2023, 37/2023; Decreto 3874/25; pliego 01-2026; El Cronista) y de la publicación del lugar. Lo que no se leyó (licencia, tallas y cupos de Entre Ríos para el río Uruguay; quién tiene el permiso argentino; si la veda rige en La Zona) se dice como «no lo podemos confirmar». Usa el mecanismo ya probado: mismo molde que Chascomús e Itá Ibaté; hereda la foto de la tarjeta «próximamente» de Concordia, que se oculta sola |

**Elegida: A.** Es el único camino con tarjetas que existe hoy, el dueño pidió «la ficha de Concordia» y no obliga a tocar plantillas. La guía es corta a propósito (como Itá Ibaté): datos clave, reglas de La Zona en corto, veda, quién puede llevarte, cómo llegar, cuánto cuesta (a consultar), qué leer antes y la línea «Contanos qué buscás y te armamos la mejor opción para vos». Título y H1 sin «lodge» (el contenido sustenta «alojamiento», no «lodge» más allá del nombre del negocio). Es es/en/pt; en pt, «pousada» para el lugar. Script: `wordpress/scripts/concordia-la-zona-2026-11-18.php` (guía + ficha + enlaces desde la nota de La Zona y el pilar del dorado, cuyo texto «Concordia… todavía no tiene guía» deja de ser cierto).

La ficha es «información pública» (sin consentimiento): La Zona Lodge, Concordia (Entre Ríos), río Uruguay, zona de Salto Grande; alojamiento y paquetes; incluye alojamiento, comidas, bebidas y traslados; especies dorado, surubí, tararira, patí, pacú y boga (el «armado» no es una especie cargada en el sitio: va solo en el texto, atribuido); **tarifa a consultar, sin foto, sin contacto, sin enlace** al sitio ni a Instagram; «Datos al 18 de noviembre de 2026». Tipo de pesca en palabras del lector: se omite (la publicación no dice si es mosca, spinning o carnada).

### Preguntas para el correo al lugar (para pasar a consentimiento)

Contacto por la vía interna de Pescamapa; el correo no se publica ni se guarda acá. Preguntar:

1. ¿Quieren figurar en Pescamapa y que tildemos «Aceptó aparecer»? ¿Con qué texto y qué fotos (con crédito)?
2. ¿Qué permiso o resolución cubre la pesca en La Zona en 2026/27 y qué días y horarios pescan?
3. ¿Cómo se aplica la veda del 1/9 al 31/12 a sus fechas? ¿Operan en esos meses?
4. Temporada, duración de las estadías, cantidad de pescadores, embarcaciones y guías, y si hablan español, portugués e inglés.
5. Tarifa vigente (con año y moneda) y qué no incluye (licencia, equipo, propinas, bebidas fuera del régimen).
6. ¿Siguen con la cabaña de alquiler en Concordia? (Es otra oferta; solo si quieren que se sepa.)
7. ¿Hay otro sitio vigente (el web figura caído)?

## Cómo se hizo y qué límite tuvo

- **El sandbox no llega a los sitios de los lugares** (`lazonalodge.com` y `riverplatedorados.com` no resuelven desde la sesión: están fuera de los dominios permitidos). Por regla 2 de CLAUDE.md y la orden de no gastar Actions (minutos agotados hasta el 5/12), **no se pudo releer ningún sitio el 17/11**. Se usaron las copias que bajó `investigar.yml` el 13/11/2026 a la rama `investigacion` (carpetas `concordia-3-20261113-2009` y `concordia-4-20261113-2011`), **leídas hoy en esa copia**, con fecha de captura 13/11/2026. Cualquier cambio posterior de esos sitios no está visto.
- Búsquedas web del 17/11/2026 (WebSearch, sin Actions) por Concordia, Federación, Chajarí y Monte Caseros. No se usó pescaargentina.com.ar ni agencias de reventa como fuente. Los listados de reventa (FishingBooker, GetMyBoat, Bait Your Hook, directorios) aparecieron en los resultados y **se descartaron como fuente**: sirven para saber que algo existe, no para listarlo.
- **Dorados on the Fly no es de La Zona**: su ficha es de Itá Ibaté (Corrientes, río Paraná; `docs/prestadores.md`, tabla del 13/11). No se toca.
- No se anotan teléfonos, correos, personas ni coordenadas.

## Tabla de candidatos

| Candidato | Dónde | Qué ofrece (su página) | Temporada que declara | Tarifa | Reglamento / veda | Idioma | ¿Activo en 2026/27? | Decisión |
|---|---|---|---|---|---|---|---|---|
| **La Zona Fishing Lodge** (sitio propio, copia del 13/11/2026) | Ciudad de Concordia, Entre Ríos, debajo de la represa de Salto Grande | Pesca en lanchas de fibra con guías, «debajo de la represa»; especies que nombra: piapara, pintado y dorado; apartamentos dobles o triples con baño privado, TV por cable y aire acondicionado; todo incluido; paquetes para 2 o 3 pescadores por embarcación | Ninguna | Ninguna | Nada sobre veda, días ni permiso | Portugués (pt-BR) | **Sin prueba.** La página (152 palabras) no tiene fecha, año, noticias ni temporada. Los datos de 2020 a 2023 son de blogs y reventas, que no son fuente | **No alcanza.** Sin contenido fechado ni actual en su propia página; no se pudo releer. Candidato n.º 1 si se lee una versión con fecha |
| **River Plate Dorados** (sitio propio, copia del 13/11/2026) | «Tailwater» del río Uruguay; la página no dice orilla ni ciudad. El sitio de una agencia lo ubica del lado uruguayo (no es fuente) | Pesca en las dos embarcaciones de la zona (según el sitio, derecho adquirido en subasta en 2013), alojamiento a unos 500 yardas de la pesca, caza combinada, programas con pavo real (otro destino) | Menciona una página «Best Times» que no se leyó | Hay una página «Rates» que no se leyó | Nada sobre veda | Inglés | **No.** Pie de página «Copyright 2013» y sin fechas de 2026 en lo leído | **No alcanza** (sin fecha actual; no se sabe que esté en la orilla argentina; no se leyeron temporada ni tarifa) |
| Operadores sin sitio propio leído: guías de Concordia y Federación (salidas de pesca variada, lanchas) | Concordia, Federación | Solo aparecen en directorios, FishingBooker o GetMyBoat | — | Tarifas de reventa, no se usan | — | — | — | **No alcanza**: ninguno tiene página propia leída. Una nota de Elonce (30/9/2026) menciona una asociación de guías del bajo río Uruguay que analizó con la provincia y la Prefectura reforzar el control: es dato de noticia, no una oferta |
| Chajarí y Monte Caseros | — | Búsqueda sin resultados de alojamiento o guía de pesca de dorado con página propia | — | — | — | — | — | **Sin candidatos.** No se encontró un solo lugar con página propia |
| Lodges de la orilla uruguaya (p. ej. los que vende Yellow Dog) | Uruguay | Fuera del pedido (ambas orillas argentinas) | — | — | — | — | — | **Fuera de alcance** |

## Por qué no se lista aunque haya búsqueda

1. **Sin fecha no hay dato.** Las reglas de `docs/prestadores.md` piden «Datos al <fecha>» en la tarjeta. Ninguna de las dos páginas propias da una fecha de 2026 ni una temporada; una tarjeta con «Datos al 13/11/2026» sería la fecha de nuestra captura, no una prueba de que el lugar funciona.
2. **El permiso del lado argentino no está resuelto en lo público.** Según la prensa del 17/3/2026 (`libreentrerios.com`, en la nota de La Zona), el contrato adjudicado en 2008 se prorrogó en 2019 «hasta que se concrete un próximo contrato» y el concurso de 2023 fue declarado fracasado (Decreto 3874/25); hay un nuevo Concurso N.º 01-2026 sin resultado leído. River Plate dice que adquirió en 2013 el derecho de las dos embarcaciones: no coincide con lo que dice el decreto de 2023 sobre quién tenía el contrato. **Listar un lugar de La Zona sin saber quién está habilitado a llevar pescadores adentro es el riesgo real** (regla de oro: lo falso cuesta más que el hueco). Pregunta abierta n.º 1 del documento del 13/11.
3. **La veda del 1/9 al 31/12 (Res. CARU 111/2023)**: ninguno de los dos sitios dice si opera en veda. La nota de La Zona ya dice que no se encontró un texto que la excepcione; una tarjeta no puede afirmar lo contrario.
4. **Nombre de búsqueda ≠ ficha.** «La Zona Lodge» es a la vez el nombre de un negocio y lo que buscan; `docs/prestadores.md` (29/10/2026) prohíbe usar el nombre de un lodge solo para atraer la búsqueda.

## Qué haría falta para crear la ficha (primera de La Zona)

- Una lectura **con fecha de 2026 o 2027** de la página de La Zona Fishing Lodge (o de River Plate Dorados si dice orilla y ciudad): lanzar `wp-script` con `leer-vivo` (corre en el servidor, ~1 min) con las URLs `lazonalodge.com` y `riverplatedorados.com/rates` y `/best-times` y cadenas como `2026`, `2027`, `season`, `temporada`, `rates`; o la captura del dueño. Un solo run cubre las dos.
- Alternativa más sólida: **consentimiento** (tilde «Aceptó aparecer»): ver preguntas abajo. Con consentimiento no hace falta fecha de página.
- Si se lee la página y confirma oferta y fecha, el molde es `wordpress/scripts/ita-ibate-unico-2026-11-15.php` (listado con información pública, sin contacto, sin precio) con `prest_destino` = la clave de la guía/destino de Concordia que corresponda (hoy Concordia es solo la tarjeta «próximamente»; no hay guía de ciudad: ver `la-zona-salto-grande-2026-11-13.md`, «Qué NO se publicó»). Es decir, **también hay que crear el destino o la guía** donde colgar la ficha: otro paso, no incluido hoy.

## Preguntas abiertas para el dueño

1. **¿Autoriza un run de `leer-vivo`** (un minuto de Actions) con las dos URLs de arriba para releer los sitios con fecha? Es el camino más barato para decidir.
2. **¿Escribir al lodge y a River Plate para pasar a consentimiento?** Pedirles: ciudad y orilla, temporada 2026/27, si operan en veda y bajo qué acto (resolución o decreto del permiso argentino), idioma, si quieren figurar. Contacto por la vía interna de Pescamapa; el correo no se publica.
3. **¿Se crea una guía de Concordia/La Zona** (destino) para colgar las fichas? Sin esa guía, una ficha no tiene dónde salir (hoy solo hay la nota).
4. **Búsqueda de cola larga sin lugar:** si no hay lugar listable, ¿se mantiene la nota informativa tal como está (reglamento, quién te puede llevar, permiso en trámite) como respuesta a «la zona lodge»? Los títulos y H2 no se tocan: el contenido no sustenta «lodge» más allá del nombre de un negocio.

## Qué NO se verificó

- Ningún sitio de lugar se leyó hoy (red); se usó la copia del 13/11/2026.
- Capturas de pantalla de las páginas públicas de La Zona: ninguna.
- No hubo cambios de WordPress: no corrió `wp-script`, ni simulacro, ni aplicar, ni `leer-vivo`; no se tocó la nota de La Zona ni ninguna guía.
