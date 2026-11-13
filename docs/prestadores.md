# Prestadores — cómo se listan

Decisión del dueño (21/10/2026): **listar primero, verificar después.** Es el
modelo de los directorios (Google Maps, TripAdvisor): se publica información
pública y se corrige o se retira a pedido. Reemplaza la regla anterior
"solo con consentimiento". Construimos utilidad primero; cuando hay pescadores
preguntando, los prestadores quieren figurar.

## Dos tipos de ficha pública

| Tipo | Cómo se carga | Cómo se ve |
| --- | --- | --- |
| Con permiso | Tilde **Aceptó aparecer** | Ficha normal |
| Listado público | Tilde **Listado con información pública** + fuente | Ficha con la nota "Información pública, todavía sin verificar con el prestador" y el enlace **"¿Es tu negocio? Pedí una corrección o que lo quitemos"** (mail a info@pescamapa.com con el nombre en el asunto) |

Nunca se publica una ficha con **Datos de ejemplo** marcado.

## Qué se puede publicar de un listado público

- Nombre del servicio, qué ofrece, destino, modalidades y especies, **con
  nuestras palabras**, y enlace a su sitio como fuente (con fecha en "Datos
  verificados el").
- Lancha, motor y capacidad **solo si su sitio o una fuente lo dice**; si no,
  se deja vacío (no se inventa).
- Precio: solo con fecha. Sin fecha la tarjeta dice **"Precio: a consultar"**
  (los precios cambian seguido; se piden cuando hay una consulta).

## Qué NO

- Teléfono, WhatsApp, email o redes: son internos y nunca se imprimen (las
  consultas llegan a Pescamapa y nosotros se las pasamos).
- Fotos o logos de su sitio, salvo que nos los hayan cedido (crédito del autor). **Regla nueva del dueño (13/11/2026): una foto del material de la propia fuente (por ejemplo el programa que nos manda el operador) puede usarse con crédito visible y se quita si lo piden; la ficha la lleva en el campo Fotos con "Autor de la foto". Vale para el punto 8 de la estrategia.**
- Frases de marketing suyas ("los mejores precios"): son afirmaciones sin
  fuente. Tampoco reseñas ni valoraciones.
- Datos estructurados de negocio (`LocalBusiness`): solo cuando la ficha esté
  confirmada con el prestador.
- Datos personales de una persona física (domicilio, DNI, familia): la ficha
  es del servicio, no de la persona.

## Bajas y correcciones

Un mail a info@pescamapa.com pide la baja o la corrección. Quien lo atiende
destilda **Listado con información pública** (y **Aceptó aparecer**) en
wp-admin → Prestadores y la ficha desaparece de la guía al instante; se
responde confirmando. Prometemos quitarla de inmediato, así que alguien tiene
que mirar ese buzón.

## Después del listado

Se contacta al prestador (más adelante, no es requisito para publicar) para
verificar datos, pedirle fotos y proponerle trabajar juntos. Al hablar con
él: tildar **Aceptó aparecer**, completar "Datos verificados el" y el estado
de la relación. Mensaje tipo para pedirle precio cuando llega una consulta:
*"Tenemos un grupo interesado en pesca con mosca en Esquina. ¿Tienen
capacidad? ¿Cuáles son los precios?"*

## Modelo: por qué nos piden a nosotros (21/10/2026)

La mayoría de las consultas llegará a través de un asistente de IA, que puede
mandar al usuario directo al prestador. Para que igual pase por nosotros:

1. **La oferta viaja con la respuesta.** Con los prestadores con los que
   trabajamos acordamos una **condición especial** para quien pida por
   Pescamapa (una frase concreta). Sale en la tarjeta ("Con Pescamapa: …") y en
   `/llms.txt`, así un asistente la repite. Solo se publica si el Estado es
   "Trabajamos juntos".
2. **Somos el camino fácil**: consulta en su idioma, respondida por una persona,
   con el precio y la disponibilidad que ningún otro sitio publica.
3. **Cobramos del lado del prestador**, como Booking: el pescador paga lo mismo y
   el prestador nos paga una comisión por el viaje que concretamos.
4. Reseñas y "los mejores" vienen después, con volumen.

Mensajes y cómo medimos: `docs/mensajes-prestadores.md`. La comisión y la
condición las decide el dueño con cada prestador.

## Cuidado legal

Esto no es asesoramiento legal. La información de negocios sacada de fuentes
públicas suele ser de bajo riesgo; el riesgo real es publicar algo **falso**
(precio, capacidad, habilitación) o tratar datos de una persona. Antes de
escalar el volumen conviene una consulta con un abogado local sobre la ley de
protección de datos personales (Ley 25.326) y el uso de nombres comerciales.


## Textos en portugués e inglés (26/10/2026)

Los textos libres de la ficha (en dos líneas, lancha, motor, cuándo trabaja, precio) tienen una versión pt y en (pestaña "Traducciones (se publican)"). Si falta, la página en ese idioma muestra el texto en español marcado `lang="es"` (no queda mezclado sin aviso, pero conviene cargar la versión). Al sumar o editar una ficha pública, completá las dos. En el precio escribí la moneda ("From ARS 300,000 per outing"). Las 8 fichas públicas de hoy se tradujeron con `wordpress/scripts/prestadores-traducciones-2026-10-26.php`.


## Modelo de negocio y qué se muestra (dueño, 27/10/2026; sujeto a revisión)

- **Fase 1 (hoy):** conseguir audiencia con información útil, gratis e imparcial. Ganamos poco o nada mientras tanto.
- **Fase 2:** pasar a cobrar una **comisión**: cobrarle a los guías por figurar o destacar, o vender nosotros los paquetes (como Yellow Dog Flyfishing). El dueño lo mantiene flexible según lo que vea de otros modelos y del tráfico.
- **Consecuencia ya vigente:** no se publican teléfono, WhatsApp, email ni redes del prestador; las consultas llegan a Pescamapa y las pasamos. **No se lista todo**: la ficha muestra qué ofrece y cómo trabaja, no cómo saltearnos. Qué otros datos mostrar (por ejemplo el precio de referencia con fecha) queda abierto para cuando se defina la fase 2.
- **En lo que decimos al público:** no presentamos "directorio abierto" como ventaja ni criticamos a competidores por ocultar contactos: hacemos lo mismo y el modelo no está cerrado.
- **Qué medir para decidir el pivote** (revisar cada mes): impresiones y clics en Search Console por guía, consultas recibidas por destino y por prestador (panel de consultas), cuántas pasamos a un prestador y cuántas se concretan (estado de la consulta), y qué prestadores preguntan por figurar.


## Decisión del dueño (29/10/2026): modelo de dos niveles (Gemini), con una salvedad

El dueño adopta el modelo de dos niveles: información pública imparcial y operadores con acuerdo arriba.

| | Ficha informativa (sin acuerdo) | Operador con acuerdo |
| --- | --- | --- |
| Objetivo | Cubrir la oferta real del río y dar credibilidad | Convertir y cobrar comisión |
| Contacto | Interno; la consulta va por el formulario de Pescamapa | Igual; lo pasamos nosotros |
| Marca visible | "Información pública, todavía sin verificar" + enlace para corregir o reclamar la ficha | "Operador con acuerdo con Pescamapa" + una línea que dice que cobramos comisión al operador y que el pescador paga lo mismo |
| Lugar | Después de los operadores con acuerdo | Primero en la lista del destino |

Cómo se activa el nivel 2 en wp-admin: Prestadores → la ficha → **Aceptó aparecer** y **Estado = Trabajamos juntos** (`acuerdo`). Sin las dos cosas no sale la marca ni sube.

**Salvedad (escenario "desviar la venta"):** si el operador consultado no tiene cupo o no contesta, se le dice al cliente la verdad y se le ofrecen alternativas **nombrando al operador con acuerdo y diciendo que lo es**. No se usa el nombre de un lodge conocido solo para atraer la búsqueda ni se insinúa falta de cupo sin haberlo consultado. Motivo: riesgo de marca y de reputación, y nuestra credibilidad es el activo.

**Antes de decir "agencia" o vender paquetes ajenos:** confirmar si hace falta registro en Argentina (referir por comisión no es lo mismo que vender el paquete). Pendiente con el dueño.

Formulario de las guías: ahora también pide el país desde el que viaja (fechas, pescadores, modalidad y especie ya estaban).

Cifras de comisión y de precios del análisis externo: referencia **interna** del dueño (ver `docs/seo-palabras-clave-en-2026-10-29.md`); nunca se publican.


## Estrategia de listados (refinada el 29-30/10/2026)

Resumen de lo decidido con el dueño para que ninguna sesión lo vuelva a discutir. Los detalles de cada punto están en las secciones de arriba.

1. **Listar ya, verificar después.** Un prestador con información pública entra a la guía aunque no lo hayamos contactado. Esperar contratos antes de mostrar una ficha mata el proyecto; los directorios (TripAdvisor, Yelp, AllTrails) nacieron indexando la oferta existente. El riesgo real no es listar, es afirmar algo **falso**.
2. **La puerta de salida siempre visible.** Cada ficha pública lleva "Información pública, todavía sin verificar" y el enlace **"¿Es tu negocio? Pedí una corrección o que lo quitemos"** (info@pescamapa.com). Una baja se atiende al instante. Nadie demanda antes de pedir que lo saquen; el 80 % de los que escriben piden actualizar un dato o saber cómo recibir más reservas: es el canal de prospección entrante.
3. **Dos niveles, con nombres que no mienten.** Nivel 1: **"Información pública"** (nunca "auditada": no la verificamos nosotros). Nivel 2: **"Operador con acuerdo con Pescamapa"** (Aceptó aparecer + Estado "Trabajamos juntos"), primero en la lista, con la línea de que cobramos comisión al operador y el pescador paga lo mismo. Hace falta que **al menos uno** esté firmado antes de prometer propuestas concretas a un cliente.
4. **Modo Uber (dueño, 1/11/2026): se lista primero, se corrige después.** **Precios de perfiles públicos (dueño, 1/11/2026): se publican**, como "Desde $ X por lancha" con su fecha interna; la tarjeta dice cuándo se revisó en palabras ("en los últimos 30 días", "en los últimos 3 meses", "en <mes>"), nunca una fecha exacta. **La plataforma de origen no se nombra en la tarjeta** (nombrar a un marketplace competidor manda al lector ahí): la URL y la fecha de lectura quedan en `notas`, y `prest_fuente` se deja vacío cuando la fuente es una plataforma intermediaria. Cada ficha vence a los 90 días (`PM_PREST_REVISION_DIAS`): la columna "Revisado" del listado de prestadores lo marca y `scripts/fichas-por-revisar.php` lista las vencidas; releer la fuente de `notas` y actualizar `verificado`/`precio_fecha`. Un perfil público del prestador en cualquier plataforma (su web, Instagram, Hooked, FishingBooker) alcanza para listarlo en nivel 1, atribuido ("según su perfil público, leído el…"), sin contacto y sin precio de plataforma (ese precio es referencia interna en `notas`). Quien pide corrección o baja la recibe al instante. No se espera ningún acuerdo previo para publicar.
4b. **Afirmar solo lo leído, atribuido.** Los datos del prestador van "según el operador", con enlace y fecha de lectura. Lo que su sitio no dice o se contradice (por ejemplo, si la licencia está incluida) no se publica; lo que no se pudo abrir (403) se lista solo con el dominio. Nada de textos ni fotos copiados: redactamos con nuestras palabras.
5. **Precios: solo con temporada y fuente.** Una tarifa se muestra únicamente si el sitio del propio operador la publica con su temporada (ejemplo: Rio Manso Lodge 2026-27, US$ 1.050 por noche o US$ 7.000 por semana, por persona en habitación doble, leído el 30/10/2026). Una tarifa sin fecha, o que solo da una agencia, es referencia interna. Las agencias (Yellow Dog, Frontiers, Orvis, Nervous Waters como intermediaria) **nunca son fuente** de un dato publicable.
6. **Contacto y comisión.** El contacto del prestador no se publica; las consultas llegan a Pescamapa. Las cifras de comisión (por ejemplo 15 %) y los precios de referencia del análisis externo son **internos**. En el primer lead: llamar al lodge con fechas y grupo reales, preguntar disponibilidad y cómo respetan la comisión; si no hay lugar, decirle la verdad al cliente y ofrecer alternativas **nombrando al operador con acuerdo y diciendo que lo es**.
7. **El formulario es de concierge, no de reserva.** Texto: "Contanos tu viaje y consultamos disponibilidad con los operadores, con tu autorización" (esa autorización es el consentimiento para pasar los datos). Nunca "reservá" ni "te conseguimos plaza". Pide país desde el que viaja. Hasta tener un operador con acuerdo, el formulario recoge interés y orienta.
8. **Fotos.** Solo las que el dueño aporta como disponibles o cedidas, siempre con el crédito del fotógrafo (`docs/contenido.md`). Nunca descargadas del sitio del lodge. **Ratificado el 1/11/2026 con el modo Uber:** los datos de un perfil público se listan; las fotos de ese perfil no se copian ni se embeben (la foto tiene titular, a veces un fotógrafo, y embeber Instagram regala el salto). Ficha sin foto a todo el ancho; la página por ciudad lleva la foto del destino; al contactar al guía, lo primero que se le pide son tres fotos suyas.
9. **Agencia: resuelto (dueño, 1/11/2026).** Si vender un paquete exige figura de agencia, se opera a través de Bitácora, la agencia registrada de Cheyenne. Sigue abierta la consulta sobre Ley 25.326 y uso de nombres comerciales en fichas de nivel 1.
10. **Dossier por lodge** (`docs/investigacion/lodges-lote1-dossiers-2026-10-30.md`): 10 campos (nombre y dominio, acceso, aguas y tipo de acceso, especies, temporada, capacidad, tarifa publicada con temporada, qué incluye, qué no, canal de contacto). Lo no confirmado en el dominio del lodge: `NO CONFIRMADO`. Una tarifa sin temporada explícita no se usa. Acceso privado no es exclusividad: la exclusividad exige una prueba específica.

## Posicionamiento (13/11/2026)

Mensaje para el pescador: **"Decinos qué querés pescar. Nosotros encontramos dónde."** Les preguntamos a los operadores que encajan con su viaje y en 48 horas le respondemos con las opciones comparadas (quién tiene lugar, a qué precio, qué incluye). Detalle, reglas de copy y consentimiento: `docs/viaje.md` → "Posicionamiento (13/11/2026)".

Qué cambia para los prestadores:

- **Comparamos con y sin acuerdo.** Figurar no depende de tener un acuerdo; el nivel 2 sigue existiendo y se dice. Los operadores no son intercambiables: solo preguntamos a los que encajan con el pedido.
- **Orden de las comparaciones:** las que mandamos a un pescador van ordenadas por cuánto encajan con su pedido, nunca por comisión; las diferencias de comisión entre operadores son internas.
- **La consulta de una ficha pide el viaje, no ese prestador.** El prestador desde el que se pidió queda como primera referencia (`prestador` en la Consulta), pero también preguntamos a otros que encajen.
- **La fuga** (un pescador encontró a un operador en nuestra página y le escribió directo) se ataca con: (a) registro del lead ante el operador, con correo fechado y **sin datos personales** apenas llega el pedido, y términos acordados (cliente nuestro si lo registramos nosotros o nombra a Pescamapa, 12 meses); (b) decirle al pescador cómo nos pagamos y pedirle que mencione a Pescamapa; (c) que el pedido sea el camino más fácil del sitio.
- **Términos estándar:** borrador de una página en `docs/condiciones-operadores.md` (no enviado; el dueño decide). El primer operador ofreció 10 % por correo el 13/11/2026.
- Esto matiza el punto 7 de "Estrategia de listados" (el formulario ya no pasa la consulta a quien se elija: consulta sin datos personales y solo pasa el contacto cuando el pescador lo autoriza para un operador).

## Acuerdos con operadores (13/11/2026)

Sin nombres de personas. Las comisiones y las tarifas de abajo son internas: no se publican en el sitio (ficha, guía, FAQ, `llms.txt`). **Regla de orden:** las comparaciones que mandamos a un pescador se ordenan por cuánto encajan con su pedido, nunca por comisión; las diferencias de comisión entre operadores son internas.

| Operador | Comisión | Cómo se atribuye el cliente | Quién factura | Restricciones | Estado |
|---|---|---|---|---|---|
| Solid Adventures (Las Buitreras, Glencross) | 10 %, ofrecida por el operador por correo | Pedido enviado por nosotros; el operador le contesta directo al cliente | A fijar: o ellos facturan al cliente y nosotros les facturamos el 10 %, o al revés | Solo semanas completas (programa 2027) | Ofrecido, pendiente de confirmar: mandamos nuestra confirmación con tres preguntas, esperamos respuesta |
| Guía local y anfitrión en Río Gallegos (semanas "River House" y días de pesca guiados) | 10 % de comisión de agencia, dicha por el operador | A confirmar por el operador | A confirmar | Precios informados por el operador: US$ 2.000 por persona por semana en habitación doble, todo incluido salvo la licencia (semanas del 9 al 16 y del 16 al 23 de enero de 2027) y unos US$ 350 por día guiado. **Sin publicar hasta que confirme que se puede** | Aceptado por nosotros (13/11), pendiente de su confirmación |
| Karku Fly Fishing | 20 % sobre reservas de pescadores que llegan por Pescamapa | El operador reconoce a los clientes que mencionan Pescamapa, sin códigos ni enlaces | Pescamapa también puede encargarse de la venta; el reparto de facturas no está fijado | **Pidió que su tarifa no se publique en ningún sitio**: se la da solo a quien pide una cotización. La tarifa está solo en las notas internas de la ficha, marcada "NO PUBLICAR" | Acordado por correo (13/11/2026) |

**Karku en el sitio (13/11/2026, `karku-acuerdo-2026-11-13`):** la ficha pasa a "Trabajamos juntos" (`estado` = `acuerdo`) y "Aceptó aparecer" (`prest_consentimiento`). Efecto público de la combinación, tal como está en `main`: (1) rótulo "Operador con acuerdo con Pescamapa" con la nota "Si pedís por Pescamapa pagás el mismo precio; el operador nos paga una comisión por el viaje." (en: "Operator with an agreement with Pescamapa" / "If you ask through Pescamapa you pay the same price; the operator pays us a commission on the trip."; pt: "Operador com acordo com a Pescamapa" / "Se você pede pela Pescamapa paga o mesmo preço; o operador nos paga uma comisão pela viagem."); (2) desaparece el aviso "Información pública, todavía sin verificar" y el enlace de corrección; (3) sube al principio de la lista del destino; (4) entra al JSON-LD de la guía como `LocalBusiness` (antes solo un ítem con nombre); (5) `llms.txt` lo describe como "operador con acuerdo con Pescamapa". La condición especial (`prest_oferta`) queda vacía: no hay una. La frase "pagás el mismo precio" se apoya en que la comisión la paga el operador; el operador no lo dijo con esas palabras: si se quiere ser estricto, volver `estado` a `contactado`. El campo `capacidad` se vació (la ficha mostraba "Hasta 4 pescadores por lancha"); "recibe 4 pescadores por semana" va en la descripción, igual que la disponibilidad 2027 con fecha, que no tiene campo propio.


## Correo de registro del pedido a los operadores (decisión del dueño, 13/11/2026)

**Regla:** cuando entra un pedido, se consulta a los operadores que encajan, tengan o no acuerdo con nosotros, **sin nombre ni contacto del pescador** (lo prometemos en el consentimiento). Es lo que se hizo a mano con el primer pedido (Solid Adventures, Karku, El Rincón, un anfitrión local). Dos variantes:

- **Con acuerdo:** es un aviso de pedido. El correo fechado es el registro de que el cliente vino de Pescamapa, aunque el pescador luego escriba directo.
- **Sin acuerdo:** es una consulta: "tenemos a alguien interesado, ¿tenéis establecida alguna comisión por derivaciones?". Nada de exigir nada.

**Cómo debe sonar:** como lo escribiría una persona, no un sistema. Texto plano, sin logo, sin HTML, sin pie de "mensaje automático". Saludo por el nombre, el pedido contado en una o dos frases (no una tabla), firmado por quien lo envía, `Reply-To` a una bandeja que lee una persona, y una línea natural para dejar de recibirlos. Nunca inventar toques personales ("vi tu web…"). El orden de los datos nunca incluye nombre ni contacto.

**Cómo sale:** a mano mientras haya pocos pedidos (plantillas abajo). Se construye el envío desde Consultas (borrador listo con un clic, registrado en la consulta) al segundo pedido, según la regla de construir la herramienta en la segunda repetición. Automático del todo solo para operadores con acuerdo que hayan dicho que sí. Requiere un campo interno (no público) con el correo de cada operador; los que solo tienen formulario o WhatsApp siguen a mano.

### Plantilla A, operador con acuerdo
**Asunto:** Pedido de un pescador: {especie} en {destino}, {mes}

> Hola {nombre}:
>
> Nos acaba de llegar por Pescamapa un pedido que encaja con vosotros: {personas}, desde {país}, quiere {especie} en {destino}, {fechas}, {días de pesca}{, sin equipo propio}. {Las fechas son flexibles.}
>
> ¿Tenéis lugar? Si es así, ¿qué opciones y precio le pasaríais? Con vuestra respuesta le preparo la comparación y se la mando; si decide seguir con vosotros, le paso su contacto.
>
> Como acordamos, si reserva con vosotros lo contáis como cliente de Pescamapa.
>
> Un saludo,
> {nombre de quien envía}
> Pescamapa

### Plantilla B, operador sin acuerdo
**Asunto:** Consulta: {personas}, {especie} en {destino}, {mes}

> Hola {nombre}:
>
> Soy {nombre de quien envía} de Pescamapa (pescamapa.com), una guía para pescar en Argentina. Tenemos a alguien interesado en vuestro servicio: {personas}, desde {país}, quiere {especie} en {destino}, {fechas}, {días de pesca}.
>
> ¿Tenéis lugar en esas fechas y qué le ofreceríais? Y una pregunta para el futuro: ¿tenéis establecida alguna comisión para los clientes que os derivamos? No hace falta decidir nada hoy.
>
> Si preferís que no os escribamos por pedidos como este, decídmelo y lo dejo de hacer.
>
> Un saludo,
> {nombre de quien envía}
> Pescamapa

La versión en inglés (para operadores extranjeros) sigue la misma estructura. Los datos entre llaves salen de los campos del pedido (`destino`, `especie`, `fecha_desde`/`fecha_hasta`, `flexibilidad`, `estadia`, `pescadores`, `acompanantes`, `equipo_propio`, país).

## Campos de la ficha (13/11/2026)

Pensados para lodges semanales (un pescador extranjero compara programa,
estadía mínima, acompañante y exclusiones de un vistazo). Todos opcionales:
vacío no muestra nada y las fichas viejas no cambian. En la tarjeta salen
después de "Incluye", como filas de la misma lista (Programa, Estadía mínima,
Acompañante que no pesca, No incluye). Código: `inc/prestadores.php`,
`parciales/prestador.php`; claves de interfaz `prest.*` en `data/ui.json`.

| Campo (meta) | Qué es | ¿Traducible? | Cuándo se muestra | Qué nunca va |
| --- | --- | --- | --- | --- |
| `prest_programa` | Duración y formato ("7 noches, 6 días de pesca con guía; sábado a sábado") | Sí: `prest_programa_en`, `_pt` (vacío = español, marcado `lang="es"`) | Si tiene texto. También en la línea de la ficha en `llms.txt` | Contacto, marketing sin fuente |
| `prest_estadia_minima` | Estadía mínima o "solo semanas completas" | Sí: `_en`, `_pt` | Si tiene texto. También en `llms.txt` | Contacto |
| `prest_acompanante` | Tarifa y condiciones del acompañante que no pesca | Sí: `_en`, `_pt` (escribir la moneda) | **Solo con "Fecha del precio" cargada**, con la misma nota "(precio revisado …)" que el precio; sin fecha la fila dice "a consultar" y no muestra el monto. No va a `llms.txt` | Monto sin fecha; precio sacado por un agente o de otra fuente que no sea la del propio prestador; contacto |
| `prest_no_incluye` | Qué no incluye (vuelos, equipo, licencia, propinas) | Sí: `_en`, `_pt` | Si tiene texto | Contacto; promesas ("sin costos ocultos") |
| `prest_capacidad_unidad` | `lancha` (por defecto) \| `semana` \| `total`; cambia la frase de `capacidad` | No (la frase está en `ui.json`) | Con `capacidad` cargada: "Hasta N pescadores por lancha" / "Recibe N pescadores por semana" / "Capacidad: N pescadores". `semana` y `total` salen en su propia fila "Capacidad" | Cargar `capacidad` sin saber a qué se refiere |

Reglas que siguen valiendo: sin datos de contacto, el precio solo con fecha, y
la "información pública" se escribe con nuestras palabras con la fuente en
`prest_fuente`. No hay JSON-LD de estos campos (no existe una propiedad
schema.org natural para programa o exclusiones en una ficha sin
`LocalBusiness`, y los datos de negocio solo salen con la ficha confirmada).

## Operadores cargados con campos nuevos (13/11/2026)

| Destino | Ficha | Fuente de lo publicado | Precio publicado (fecha) | Notas |
|---|---|---|---|---|
| Jurassic Lake | Jurassic Lake Lodge | Su sitio: `/rates` (10/11), `/pre-trip-info`, `/seasons`, `/the-lodge` (13/11) | US$ 8.000 / 5.200 / 4.200, sin año ni temporada en la página (10/11/2026) | La cifra "Jurassic Full Week US$ 6.900" y el suplemento de individual de una agencia no se publican |
| Jurassic Lake | Estancia Laguna Verde | Su sitio: `/the-lodge`, `/about-us-why` | Ninguno | Cifras de agencia (Tight Loop) no se publican |
| Jurassic Lake | Tres Amigos Outfitters | Su página del combo (modificada en 2024) | Ninguno | |
| Río Grande | Villa María Lodge (nueva) | Su página en Nervous Waters (grupo que opera el lodge) y planilla 2025/26 | US$ 13.950 / 14.950 / 12.950 por semana, temporada 25/12/2026 a 26/3/2027; acompañante US$ 650 por noche (13/11/2026) | Se trata Nervous Waters como página propia del operador porque dice operar sus lodges; si se prefiere tratarla como intermediaria (punto 5 de la estrategia de listados), bajar la tarifa |
| Río Grande | Kau Tapen Lodge | Su sitio y su página en Nervous Waters | Igual que antes; acompañante US$ 650 por noche (13/11/2026) | |
| Río Gallegos | Estancia Glencross (nueva) | Página de Solid Adventures | Ninguno ("a consultar") | Días de la semana y tarifa solo en una agencia: no se publican |
| Río Gallegos | Las Buitreras, Karku | Ver arriba | Las Buitreras como estaba; Karku ninguno | Karku pidió no publicar su tarifa |
