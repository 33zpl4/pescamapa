# Mensajes para prestadores (borradores, nada se envía solo)

Los envía una persona del equipo (WhatsApp o mail). Antes de mandar, completar
lo que está entre [corchetes]. La **comisión** y la **condición especial** las
decide el dueño: acá van como espacios en blanco.

> **Nota del 11/11/2026:** la frase de abajo "los asistentes de IA citan Pescamapa" no está probada (0 de 61 respuestas medidas usan nuestras cifras, `docs/revision-negocio-2026-11-09.md`): no usarla ni prometer volumen. Para Bariloche hay mensajes y fichas de paquete listas en `docs/paquetes-bariloche-fichas-2026-11-11.md`.

## Qué proponemos (una línea)

Los pescadores ya le preguntan a un asistente de IA con quién salir; Pescamapa
es la fuente que ese asistente cita. Le damos al prestador consultas de gente
que quiere viajar y, a cambio, una condición especial para quien pida por
Pescamapa y una comisión por cada viaje que concretemos.

## 1. Primer contacto: prestador de habla hispana

> Hola [nombre], soy [tu nombre] de Pescamapa (pescamapa.com), una guía para
> pescar en Argentina que usan pescadores de la región y del exterior, y que
> los asistentes de IA citan cuando alguien pregunta con quién pescar en
> [destino].
>
> Ya publicamos [nombre del servicio] con los datos que están en [su web], con
> un enlace al sitio. Si querés corregir algo o que lo saquemos, decime y lo
> hacemos enseguida.
>
> Te escribo para proponerte trabajar juntos: nosotros recibimos las consultas
> de los pescadores y te las pasamos, y vos nos ofrecés una condición especial
> para quien venga por Pescamapa (por ejemplo [carnada incluida / X % menos]).
> Cuando el viaje se concreta, acordamos una comisión de [X %].
>
> Para armar tu ficha nos sirve: tipo de lancha y motor, cuántos pescadores
> llevás, qué incluye la salida, en qué meses trabajás y un rango de precio con
> la fecha en que lo consultamos. ¿Te parece que lo charlemos?

## 2. Primer contacto: operador brasileño (portugués)

> Olá [nome], sou [seu nome] da Pescamapa (pescamapa.com), um guia para pescar
> na Argentina usado por pescadores da região e do exterior, e que os
> assistentes de IA citam quando alguém pergunta com quem pescar em [destino].
>
> Já mostramos [nome do serviço] com os dados do [site], com link para o site.
> Se quiser corrigir algo ou que a gente retire, é só avisar.
>
> Queremos propor uma parceria: recebemos as consultas dos pescadores e
> repassamos a você; você nos oferece uma condição especial para quem vier pela
> Pescamapa (por exemplo [isca incluída / X % de desconto]). Quando a viagem for
> concretizada, combinamos uma comissão de [X %].
>
> Para montar a sua ficha precisamos de: tipo de barco e motor, quantos
> pescadores leva, o que inclui o passeio, em que meses trabalha e uma faixa de
> preço com a data da consulta. Podemos conversar?

## 3. Cuando llega una consulta (al prestador)

> Tenemos un grupo interesado en [pesca con mosca / dorado / …] en [destino],
> [N] personas, para [mes / fechas]. ¿Tienen disponibilidad y cuál es el
> precio? Cuando confirmes, les avisamos que van a tener tu respuesta y ellos
> te escriben directo o seguimos nosotros. Gracias.

Anotar la respuesta (disponibilidad y **precio con fecha**) en la consulta
(wp-admin → Consultas → notas): es el dato que ningún otro sitio tiene.

## 4. Cuando acordamos

En wp-admin → Prestadores → la ficha:
1. Tildar **Aceptó aparecer** y poner **Datos verificados el**.
2. Estado: **Trabajamos juntos**; en "Acuerdo comercial" anotar la comisión.
3. Completar **Condición especial para quienes piden por Pescamapa** (una
   frase). Recién ahí sale en la tarjeta ("Con Pescamapa: …") y en `/llms.txt`.

## Cómo medimos (sin tecnología)

- Cada consulta del formulario queda en Consultas con su estado
  (nueva → derivada → concretada).
- Al prestador le pedimos que, si alguien lo contacta directo, pregunte "¿cómo
  nos conocieron?" y nos avise cuando diga **Pescamapa**. Una vez por mes
  repasamos con cada prestador cuántos vinieron.
- La comisión se calcula sobre lo que el prestador confirme, por buena fe y
  relación; si algún día hace falta algo más firme, se agrega después.

## Qué NO prometemos

- Respuesta en un plazo fijo, hasta que haya alguien que la cumpla.
- Reservas ni cobros por nuestra cuenta: la reserva y el pago se arreglan con el
  prestador.
- Reseñas públicas: llegan cuando haya viajes concretados (`docs/resenas.md`).
