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
- Fotos o logos de su sitio, salvo que nos los hayan cedido (crédito del autor).
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
