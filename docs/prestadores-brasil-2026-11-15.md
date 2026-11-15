# Operadores brasileños que concentran la demanda (borrador interno, 15/11/2026)

**Borrador interno: no se publica y no se envía nada.** No crea fichas de prestador (lo decide el dueño después) ni toca contenido del sitio. Sin nombres de personas: solo operadores y agencias.

## De dónde sale

Planificador de palabras clave, Brasil, portugués (15/11/2026; datos en tramos: «50» = 10–100 búsquedas al mes, «500» = 100–1.000). La gente busca **por nombre de operador**: «pousada gêmeos» (Itá Ibaté) y «pedrinho pesca» entre 100 y 1.000 cada uno; «la regina», «hambaré» y «matute» entre 10 y 100. «Pousada(s)» es la palabra dominante («pesca na argentina pousadas», «pousadas pesca argentina»: 100–1.000, competencia baja). Pujas muy bajas (US$ 0,05–0,9): mercado de volumen y ticket menor; no se usa para decidir publicidad (no habrá anuncios), solo guía títulos y palabras clave.

Hasta hoy el repo no tenía nada sobre Gêmeos, Pedrinho ni La Regina; Hambaré y Matute aparecían solo en las notas de precios (y Hambaré en `docs/borradores/viaje-dorado-amigos-2026-11-05.md`).

## Cómo se leyó

`investigar.yml`, 3 corridas del 15/11/2026 (carpetas de la rama `investigacion`: `pt-vivo-20261115-2103`, `operadores-br-20261115-2106`, `equipo-dorado-20261115-2108`), solo páginas públicas, con `robots.txt` respetado. Se leyeron las páginas de inicio de cada operador (y de Hambaré también su página de pesca). Fallaron dos lecturas: `dellafishingtour.com.br` y el directorio `pescaesportivaargentina.com/dicas` (fetch failed). Nada de lo de abajo está verificado con el operador: es lo que cada sitio dice, leído ese día. «Hay contacto en su sitio» significa solo eso; los datos de contacto **no** se copian al repo.

## Tabla

Estado de relación en el repo: ninguno tiene ficha de prestador, ninguno figura como contactado ni con acuerdo (no hay registro de correo enviado a ninguno). Todos: **relevado** (sitio leído el 15/11/2026).

| Operador | Sitio propio (leído 15/11/2026) | Localidad | Qué ofrece (según su sitio) | Precio publicado | En el repo hoy | En una ficha | Contacto público |
|---|---|---|---|---|---|---|---|
| **Pousada Gêmeos** (Gêmeos Pesca Esportiva) | gemeospescaesportiva.com.br | Itá Ibaté, Corrientes, a orillas del Paraná | Lodge con apartamentos (aire, minibar, baño privado), piscina y jacuzzi, restaurante; lanchas de fibra de 19 pies para hasta 3 pescadores con motores de 115 y 150 hp, tres lanchas de 21 pies para baitcast y mosca; guías. Anuncia un «1.º campeonato de dourado» (sin fecha en lo leído). | No en las páginas leídas | Nada. La guía de Itá Ibaté dice que el lodge de Dorados on the Fly es «el único con página propia que encontramos»: desde hoy esa frase ya no es cierta (ver abajo) | No | Sí (WhatsApp y formulario en su sitio) |
| **Pedrinho Pesca** | pedrinhopesca.com.br | Se presenta como agencia de turismo de pesca; su HTML nombra Paso de la Patria | Agencia: grupos de pesca desde 1990 (primero Pantanal); en Argentina, lanchas tipo Tracker, Caribbean y Pacu, sistema todo incluido, «licencia de pesca en la Argentina» entre lo que lista. El texto leído no dice en qué alojamiento se queda el grupo. | No | Nada | No | Sí (teléfono, WhatsApp y formulario) |
| **La Regina** (lareginalodge.com.br y hotellaregina.com.br, dos sitios del mismo lugar) | los dos | Puerto Rzepecki, zona de Itatí, entre Puerto Corazón y Yahapé, Corrientes | Lodge y apart hotel «rural cinco estrellas» (según el sitio); apartamentos dobles y triples, piscina, jacuzzi, restaurante; lanchas Tracker 540 con motores de 50 a 115 hp, guías, bebidas y carnada a bordo; paquetes «de pesca»; dice dar orientación sobre equipo y documentos. Los dos sitios no coinciden en la capacidad (hasta 68 huéspedes en uno; 65 habitaciones y hasta 150 personas en el otro). | Sí, pero vencido: paquete Carnaval 2026, 3 días de pesca y 4 noches, R$ 4.200 por persona, llegada el 13/2/2026, con comidas y bebidas; «Pacotes de Pesca 2026: consulte valores» | Nada con su nombre. La nota de precios «O que inclui um pacote…» cita, sin nombrarlo, un paquete de hotel 5 estrellas con casino en la ciudad de Corrientes (no es este) | No | Sí (WhatsApp, formulario) |
| **Pousada Hambaré** | pousadahambare.com.br | Esquina, Corrientes, Ruta 12 Norte, frente a la confluencia del Corrientes con el Paraná | Posada con habitaciones con vista al río, restaurante panorámico, piscina, jacuzzi, tienda de pesca; en su página de pesca dice incluir «equipamento, iscas, bebidas», lanchas y guías; dorado, pacú, surubí. | En el repo, sí, con fecha: tarifas «desde» por persona leídas el 4 y 5/11/2026 en las páginas de sus paquetes (no en las páginas leídas hoy; su portada hoy no muestra precios) | Sí: notas de precios (viaje con amigos, desde Brasil) y borrador `viaje-dorado-amigos`; pendiente la temporada de sus tarifas (`docs/pendientes-y-mejoras.md`) | No | Sí (WhatsApp) |
| **Pousada Matute Pesca** | pousadadepescamatute.com.br | Esquina, Corrientes (ríos Paraná y Corrientes) | Posada todo incluido, wifi, climatizada, transporte «según el grupo»; 9 paquetes: 2, 3 o 4 días de pesca (3, 4 o 5 noches) para parejas, tríos o cuartetos; técnicas que nombra: pinchada, corrico y rodada. Dice trabajar en relación con una agencia de Porto Alegre. | No: cada paquete dice «Ver valores» y manda a WhatsApp | Solo mencionada (sin precio) en las notas de precios, junto a Hambaré y Puerto Yacarey, como pousada que una agencia de Porto Alegre lista sin precios | No | Sí (WhatsApp, teléfono) |
| Pousada Puerto Yacarey (otro que apareció) | pescanaargentina.com.br | Itatí, Corrientes | Posada todo incluido con wifi y aire; paquetes para parejas y tríos; dice orientar a los huéspedes sobre el equipo antes de llegar. | No | Solo mencionada, sin precio, en la misma nota | No | Sí (WhatsApp, teléfono) |
| Romani Pesca (agencia de Porto Alegre, otro que apareció) | romanipesca.com.br | Porto Alegre (Chile, Colombia y Argentina) | Agencia desde 2009 que vende paquetes a Hambaré, Puerto Yacarey y Matute entre otros destinos. | No | Citada sin nombre en la nota de precios | No | Sí (correo, formulario) |

## Lo que cambia la lectura

1. **Hay una capa de agencias brasileñas** que vende a los mismos operadores. Romani Pesca vende Hambaré, Puerto Yacarey y Matute; en las búsquedas aparecen además páginas de otras agencias con el nombre del operador (BCA Fishing en pacotesdepesca.com.br, que lista Gêmeos, La Regina y Matute, y también «Pousada Aventura Esquina», «Pousada Dourado Pesca» y «Pousada Pesca Argentina» de las que no encontramos sitio propio; Venturatur, Cobras Pesca, Fishing Viagens, Brava Pesca). No se leyeron. Antes de preguntar por comisión conviene saber **quién cobra**: el operador, la agencia, o los dos.
2. **Pedrinho Pesca no parece un lodge**, sino una agencia con base en Paso de la Patria: lo que se anuncia ahí es el servicio, no un alojamiento propio. Falta saber con quién opera en el destino.
3. **Precios**: ninguno publica un precio vigente con fecha en las páginas leídas hoy. Sirven Hambaré (4–5/11/2026, en notas) y la referencia vencida de La Regina. Matute y Gêmeos mandan a WhatsApp.
4. **La guía de Itá Ibaté** (es/en/pt) dice que Dorados on the Fly es el único lodge con página propia que encontramos. Hoy leímos la de Gêmeos. Se ajustó la frase del subtítulo en portugués (script `pt-pousadas-titulos-2026-11-15`) para no afirmar eso; el cuerpo de la guía y las versiones es/en lo siguen diciendo y se corrigen cuando el dueño decida si lista a Gêmeos.
5. **Qué páginas nuestras ya cubren cada nombre**: ninguna. «pousada gêmeos» y «pedrinho pesca» (100–1.000 cada una) caerían hoy en la guía de Itá Ibaté y en la de Paso de la Patria, que no los nombran. Nombrarlos en una ficha solo con la regla de `docs/prestadores.md` (listado con información pública, marcado sin verificar, con enlace de corrección o baja; sin contacto ni fotos ajenas; precio solo con fecha).

## Borradores de correo (para que los envíe una persona, uno a uno)

Siguen la regla de `docs/prestadores.md` («Correo de registro del pedido a los operadores», plantilla B, operador sin acuerdo): texto plano, sin logo ni pie de «mensaje automático», saludo por el nombre, firmado por quien lo envía, `Reply-To` a una bandeja que lee una persona, una línea natural para dejar de recibirlos. No hay pedido de un pescador todavía, así que no se menciona ninguno, y no se inventan toques personales («vi tu web…»). No lleva cifras de comisión (ni nuestras ni ajenas): solo pregunta si existe. Los datos entre llaves se completan al enviar. Si el operador vende por una agencia, usar la línea opcional.

### Portugués (operadores de Brasil: Gêmeos, La Regina, Hambaré, Matute, Puerto Yacarey)

**Assunto:** Consulta da Pescamapa: pescadores que perguntam pela {pousada}

> Olá, {nome}:
>
> Sou {nome de quem envia}, da Pescamapa (pescamapa.com), um guia para pescar na Argentina: licença, regras, temporadas e opções para viajar, tudo com a fonte à vista. Escrevemos em português para quem vem do Brasil.
>
> Quando um pescador nos conta o que procura (datas, quantas pessoas, que peixe), perguntamos aos operadores que encaixam, sem passar o nome nem o contato dele, e respondemos com as opções comparadas. A {pousada}, em {destino}, é uma das que aparecem nas buscas de quem pesca em Corrientes.
>
> Queria saber duas coisas: vocês têm estabelecida alguma comissão para os clientes que indicamos? E preferem receber esses pedidos por e-mail ou por WhatsApp? Não precisa decidir nada hoje.
>
> {Linha opcional, se vendem por agência: Se vocês vendem também por agências, diga-me com quem devo falar sobre isso.}
>
> Se preferirem que não escrevamos por pedidos assim, me avisem e deixo de fazê-lo.
>
> Um abraço,
> {nome de quem envia}
> Pescamapa

Nota: la línea opcional va solo si se sabe que venden por agencia. Revisar el tono con un hablante nativo de portugués antes del primer envío.

### Español (operadores que respondan en español, o para la copia interna)

**Asunto:** Consulta de Pescamapa: pescadores que preguntan por {posada}

> Hola, {nombre}:
>
> Soy {nombre de quien envía}, de Pescamapa (pescamapa.com), una guía para pescar en Argentina: licencia, reglas, temporadas y opciones para viajar, todo con la fuente a la vista. Escribimos también en portugués para quien viene de Brasil.
>
> Cuando un pescador nos cuenta qué busca (fechas, cuántos son, qué pez), consultamos a los operadores que encajan, sin pasar su nombre ni su contacto, y le respondemos con las opciones comparadas. {Posada}, en {destino}, es una de las que aparecen en las búsquedas de quienes pescan en Corrientes.
>
> Quería preguntarles dos cosas: ¿tienen establecida alguna comisión para los clientes que les derivamos? ¿Y prefieren recibir esos pedidos por correo o por WhatsApp? No hace falta decidir nada hoy.
>
> {Línea opcional, si venden por agencia: Si también venden por agencias, avísenme con quién tengo que hablar de esto.}
>
> Si prefieren que no les escribamos por pedidos así, díganmelo y dejo de hacerlo.
>
> Un saludo,
> {nombre de quien envía}
> Pescamapa

## Qué decide el dueño después

- Si se crea ficha (con la regla de `docs/prestadores.md`) para Gêmeos, Hambaré, Matute, La Regina y Puerto Yacarey; Pedrinho Pesca y Romani Pesca son agencias: se listarían como «operador», no como alojamiento.
- Quién envía los correos y desde qué bandeja (`Reply-To`).
- Si se pregunta primero a los operadores o a las agencias que los venden.
- Cuándo corregir la frase de la guía de Itá Ibaté (es/en y cuerpo en pt) sobre «el único lodge con página propia».

## Qué no se verificó

Que los operadores existan hoy tal como dicen sus sitios (solo se leyeron las páginas); capacidades y características (los dos sitios de La Regina se contradicen); que las agencias vendan hoy a esos operadores (se leyó solo Romani Pesca); la relación entre Pedrinho Pesca y un alojamiento; precios vigentes. Las páginas de Gêmeos y de Pedrinho Pesca cargan en parte con JavaScript: lo que no salió en el texto leído puede estar en la página.
