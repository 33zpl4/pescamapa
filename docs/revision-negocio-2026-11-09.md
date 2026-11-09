# Revisión del negocio (9/11/2026)

Pedido del dueño: revisar la idea completa, encontrar las palancas más grandes y actuar. Esto es una opinión de la sesión a partir de lo que hay en el repo; no reemplaza ninguna decisión del dueño. Donde propone cambiar una decisión ya tomada, lo dice.

## Veredicto en cinco líneas

1. La idea es buena y el hueco existe: nadie junta reglamento leído, precios fechados y opciones locales, y el ticket del viaje de pesca a la Argentina es alto.
2. A tres semanas del lanzamiento el sitio tiene mucho contenido y **ninguna prueba del negocio**: 0 consultas recibidas, 0 operadores con acuerdo, 0 viajes derivados.
3. El tráfico que hay no es el que paga: 10 clics por semana, estables dos semanas seguidas, casi todos de altura del río y del mapa (informe del 8/11, issue 159). Es público argentino de salida de día, donde la comisión no sostiene nada (ya lo dijo el dueño el 1/11).
4. El riesgo principal no es que falte contenido. Es seguir publicando seis meses sin haber cerrado nunca un viaje y descubrir tarde que el eslabón roto era otro (el operador no paga, el pescador reserva directo, nadie responde a tiempo).
5. La palanca más grande es **cerrar un viaje a mano cuanto antes**, con un operador que haya dicho que sí. Todo lo demás se ordena detrás.

## Dónde está la plata (cuenta gruesa)

Precios leídos y ya publicados en el sitio. La comisión del 10 % es un **supuesto mío para comparar órdenes de magnitud**, no un dato: la cifra real sale del primer acuerdo.

| Producto | Ticket | Comisión al 10 % |
| --- | --- | --- |
| Salida de día en Bariloche (bote, 1 o 2 pescadores) | US$ 550 | US$ 55 |
| Paquete de dorado en Corrientes, 3 días, grupo de 3 (Hambaré, US$ 855 por persona) | US$ 2.565 | US$ 257 |
| Semana en lodge de Patagonia, 2 pescadores (Rio Manso, US$ 7.000 por persona) | US$ 14.000 | US$ 1.400 |
| Semana de trucha marina, 2 pescadores (Kau Tapen, US$ 12.950 a 14.950) | US$ 25.900 a 29.900 | US$ 2.600 a 3.000 |

Una semana de lodge vale entre 25 y 50 salidas de día. El negocio son dos productos: **semanas de lodge para extranjeros** (inglés) y **grupos al dorado, sobre todo brasileños** (portugués). Las salidas de día sirven de puerta de entrada y de relación con el guía, no de ingreso.

Cuenta al revés, con supuestos gruesos (1 a 3 % de las visitas con intención piden cotización; 1 de cada 10 a 20 consultas se concreta): un viaje por mes pide del orden de **500 a 1.500 visitas mensuales con intención de contratar**. Hoy hay unos 40 clics por mes y casi ninguno es de ese tipo. La brecha es de más de diez veces y con un dominio nuevo el SEO solo tarda meses.

## Las palancas, en orden

### 1. Cerrar el circuito una vez (oferta)

Hoy el formulario promete opciones "de guías y alojamientos" y hay precios "desde" publicados (paquetes de Bariloche a US$ 400 y US$ 550) que ningún operador confirmó. Si mañana entra una consulta buena, no hay a quién pasársela con una comisión acordada.

- **Qué hacer:** dos acuerdos, uno por producto: un lodge u outfitter de Patagonia norte y un operador de dorado en Corrientes que reciba grupos. Con uno de cada lado alcanza para prometer propuestas concretas.
- **Cambio que propongo a una decisión previa:** `pendientes-y-mejoras.md` 3.4 deja la campaña a guías y lodges "para cuando haya tráfico que mostrarles". Yo la haría ahora. Escribirles a las más de 30 fichas ya publicadas ("ya figurás, ¿corregimos algo?") da cuatro cosas a la vez: datos verificados, fotos cedidas, enlaces desde sus sitios y la conversación sobre la comisión. No hace falta tráfico para esa conversación: a un lodge le alcanza con "si te llevo un cliente, ¿pagás comisión de agente?". Los mensajes están en `docs/mensajes-prestadores.md`.
- **Hipótesis a confirmar en la primera llamada:** que los lodges pagan comisión a cualquier agente que les lleve una reserva. Si es así, el lado de la oferta en el producto caro es fácil y el cuello queda en la demanda.

### 2. Demanda que paga, y probarla sin esperar al SEO

- **Foco:** congelar la amplitud (destinos nuevos, completar todo en tres idiomas) hasta tener las primeras consultas, y poner el trabajo en las páginas de los dos productos que pagan: comparación de lodges con tarifa fechada (silo A de la estrategia) y paquetes de dorado para grupos en portugués. Cada página nueva suma además deuda de mantenimiento: precios y normas que vencen a los 90 días o cada temporada.
- **Experimento que propongo (contradice el punto 6 de `estrategia-seo-2026-10-31.md`, que dice no pagar publicidad hasta ver consultas):** US$ 150 a 300 en anuncios de búsqueda durante dos semanas, sobre tres o cuatro términos de intención alta, hacia las páginas de precios y el formulario. No como canal, como medición: dice en dos semanas si esas páginas convierten una visita en una consulta, algo que el SEO no va a poder decir en meses. Si con 200 visitas con intención no entra ninguna consulta, el problema es la página o la confianza, no el tráfico.
- **Decisión del dueño:** hacerlo o no, y con qué tope.

### 3. Autoridad del dominio

Las búsquedas en inglés que pagan ("trout patagonia", "golden dorado", "argentina fishing license") aparecen en posición 60 a 80. Con el contenido que ya hay, lo que falta ahí son enlaces entrantes, no más páginas.

- Los prestadores listados son la fuente natural de enlaces (misma gestión que la palanca 1).
- El hub de licencias y la nota "por tu cuenta" son las dos piezas que un foro o un club enlazaría: llevarlas a mano a las comunidades donde se pregunta eso.
- La altura del río es lo único que ya rankea: un recuadro para insertar ("altura del río en Goya hoy, por Pescamapa") en sitios de guías, clubes y municipios daría enlaces desde el Litoral. No está hecho; es un trabajo chico si el dueño lo quiere.
- Bing Webmaster Tools: sigue sin respuesta si está configurado. Se importa desde Search Console en minutos y es el índice del que se alimenta parte de la búsqueda de los asistentes.

### 4. Confianza para un ticket alto

Quien va a pagar US$ 7.000 por persona quiere saber quién está del otro lado. El sitio no tiene página "Quiénes somos": los nombres aparecen solo en la política de privacidad. Las agencias con las que se compite llevan décadas.

- Página "Quiénes somos" con las personas, dónde están, cómo se investiga y **el respaldo de la agencia registrada** (Bitácora, con su número de legajo si corresponde). Necesita datos y visto bueno del dueño; no la escribí.
- Un plazo de respuesta publicado ("respondemos en 24 horas") y un WhatsApp de Pescamapa en el formulario. En Argentina y Brasil el WhatsApp convierte más que un formulario; en el público de EE. UU. importa menos.

### 5. Medir el embudo (hecho hoy)

No había forma de saber qué página o qué canal trae a quien consulta, ni si alguna vez llegó alguien desde un asistente de IA, que es la tesis del proyecto. Ver "Qué se hizo hoy".

### 6. Velocidad de respuesta (hecho en parte hoy)

En una consulta de viaje, quien responde primero suele quedarse con el cliente. Faltan del lado del dueño: probar el formulario real de punta a punta (5.1, nunca se hizo) y un contacto por región para poder cotizar en el día (3.1).

## Riesgos que conviene mirar de frente

| Riesgo | Por qué importa | Qué lo baja |
| --- | --- | --- |
| Precios "desde" sin operador que los sostenga (paquetes de Bariloche) | La primera consulta puede terminar en "al final cuesta otra cosa": se pierde la credibilidad, que es el activo | Palanca 1: confirmar con un operador o pasar a "a consultar" |
| La tesis "las IA nos van a referir" no está probada | 0 de 61 respuestas medidas usan nuestras cifras; ChatGPT cita a los lodges y a Hooked | No depender de un solo canal; desde hoy cada consulta dice si vino de un asistente |
| Fuga: la ficha nombra al operador y enlaza su sitio | El pescador puede reservar directo; la comisión es de buena fe | Aceptarlo en el nivel 1; con acuerdo, la "condición especial por Pescamapa" es lo que hace volver |
| Fotos tomadas de sitios de terceros con crédito (regla del 8/11) | Citar la fuente no es tener permiso; alcanza un reclamo para perder la relación con ese actor | Decisión del dueño ya tomada; priorizar fotos cedidas al contactar prestadores |
| Un solo servidor, sin alerta de caída | El 1/11 estuvo caído dos horas y se notó por los workflows | Un monitor gratuito de disponibilidad con aviso al teléfono |
| Deuda de mantenimiento | 11 guías, más de 30 fichas y 15 notas con datos fechados en tres idiomas | Congelar amplitud; `fichas-por-revisar` |

## Tablero para los próximos 30 días

Mirar una vez por semana, junto al informe del lunes:

| Indicador | Hoy | Meta a 30 días |
| --- | --- | --- |
| Operadores con acuerdo | 0 | 2 (uno por producto) |
| Consultas reales recibidas | 0 | 5 |
| Consultas respondidas en menos de 24 h | sin dato | todas |
| Cotizaciones enviadas con un operador detrás | 0 | 1 |
| Clics semanales fuera de altura del río y mapa | menos de 10 | 30 |

Si a los 30 días hay acuerdos y consultas, se vuelve a ampliar contenido con datos de qué canal y qué página las trajo. Si hay tráfico pagado de prueba y ninguna consulta, se trabaja la página y la confianza antes de escribir nada más.

## Qué se hizo hoy

En la rama `claude/determined-wright-q3oxnq` (sin mergear ni desplegar; falta pasar `test-wp` en un PR):

1. **Origen de cada consulta.** Cada formulario envía por qué página entró la persona al sitio, desde qué sitio llegó y el `utm_source`, y la consulta guarda un **Canal** (ChatGPT, Perplexity, Gemini, Google, Bing, WhatsApp, o el dominio). Se ve en el aviso por correo, en la consulta y en una columna nueva de wp-admin → Consultas. Sin cookies: se guarda en la pestaña del navegador y solo viaja con la consulta. Política de privacidad actualizada en los tres idiomas. Código: `inc/consultas.php`, `footer.php`.
2. **Acuse de recibo por correo** a quien deja un email, en su idioma, con texto fijo (no devuelve nada de lo que la persona escribió, para que el formulario no sirva para mandarle mensajes a un tercero) y respuesta a `info@pescamapa.com`. No promete plazo: repite el "te escribimos pronto" de la página.
3. **Responder con un clic:** el aviso interno lleva `Reply-To` con el email del pescador.
4. Pruebas en `test-wp.yml` para los campos nuevos.

## Qué necesita al dueño (en orden)

1. Enviar una consulta de prueba desde el formulario real y confirmar que llega el aviso y el acuse (pendiente 5.1).
2. Escribirles esta semana a los prestadores ya listados, empezando por un operador de dorado en Corrientes y uno de Patagonia norte, y preguntar por la comisión.
3. Decidir el experimento de anuncios (palanca 2) y su tope.
4. Decidir qué pasa con los "desde" de Bariloche hasta que un operador los confirme.
5. Pasar los datos para "Quiénes somos" y decir si hay un WhatsApp de Pescamapa para publicar.
6. Confirmar Bing Webmaster Tools.
