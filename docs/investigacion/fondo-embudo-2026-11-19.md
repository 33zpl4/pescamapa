# Fondo del embudo: del «recomendame lugares en X» al pedido (19/11/2026)

**Estado: relevamiento y propuesta.** No cambia nada del sitio. Sin teléfonos, correos ni otros contactos (el repo es público de forma temporal). Los textos del visitante están en español; en/pt se traducen después de que el dueño apruebe.

## 0. De dónde sale

Una conversación con ChatGPT (aportada por el dueño) recorrió el fondo del embudo: «dame dos recomendaciones en cada lugar» (Paso de la Patria, Goya, Esquina) → ChatGPT nombra lugares con lo que publica cada uno, citando el listado de prestadores de pesca de Turismo de Corrientes, y aconseja «pedí el total para dos personas, una pescando, tres jornadas, y preguntá qué pasa si no se puede salir por el clima» → el usuario pregunta cómo evitar escribir uno por uno. Ese último paso es lo que Pescamapa ya hace con el formulario; falta que la página lo diga en el momento en que el pescador lo necesita, y que el formulario acepte varias localidades.

Lugares que nombró: Hotel Spa Jardín del Paraná y Posada del Paso (Paso de la Patria); El Gordo y el Flaco y Campamento La Amistad (Goya); Posada Hambaré y Posada del Muelle (Esquina).

## 1. El listado oficial de Turismo de Corrientes

**No es un PDF ni una sola página: son páginas HTML por localidad, «Experiencia de pesca», en `corrientes.tur.ar`** (sitio del Ministerio de Turismo de la Provincia de Corrientes; `turismo.corrientes.gob.ar` es el dominio anterior). Cada página lista nombres de lugares y guías de esa zona con sus datos de contacto (que no se copian aquí), más un texto general sobre formatos (medio día, jornada completa) y especies.

Páginas leídas el 19/11/2026 (una corrida de `leer-vivo` sobre la rama; solo presencia de nombres y palabras, sin extractos en las oficiales):

| Página | Estado | Qué se vio |
|---|---|---|
| `corrientes.tur.ar/tour/experiencia-de-pesca-paso-de-la-patria/` | 200 | Figura «Jardín del Paraná» (escrito sin tilde). **«Posada del Paso» no aparece con ese nombre.** Menciona «licencia»; el texto «actualiz…» está presente (la fecha no se leyó) |
| `corrientes.tur.ar/tour/excursiones-de-pesca/` (título: «Experiencia de Pesca – Goya») | 200 | Figuran «El Gordo y el Flaco» y «La Amistad». Menciona «medio día», «jornada completa» y «licencia» |
| `corrientes.tur.ar/tour/experiencia-de-pesca-esquina/` | 200 | Figuran «Hambar…» (Hambaré) y «Posada del Muelle». Menciona «jornada completa» y «licencia» |
| `…/experiencia/pesca-deportiva-parana-medio/` y `…/pesca-deportiva-en-el-alto-parana/` | **404** | Direcciones que devolvía un buscador; hoy no existen (la corrida de lectura también probó `…/experiencia-de-pesca-goya/`: 404) |

- **Datos por prestador:** nombre y contacto. En las tres páginas buscadas **no aparecen** las palabras «combustible», «carnada», «almuerzo», «traslado», «paquete», «tarifa» ni «precio»: el listado no trae precios ni inclusiones por lugar.
- **Fecha de actualización visible:** **no se leyó** (la lectura no imprimió fechas para evitar extractos con contactos). Un buscador marcó una de las páginas viejas como de unos 650 días y otra de unos 1.980, lo que sugiere que **las fichas del listado pueden estar desactualizadas**; es dato del buscador, no verificado.
- **Qué no se vio:** cómo está armada cada entrada (si todas traen los mismos campos) y si el sitio dice en algún lado que este listado es el «Registro Provincial de Prestadores» o una habilitación. Por eso la marca de la sección 4(a) dice «listado», no «registro habilitado».
- **Pendiente (otra corrida, si el dueño la quiere):** una lectura con extracto de la parte de arriba de cada página para la fecha y la estructura, con un filtro que descarte los números de teléfono antes de imprimir.

**Incidente de la corrida (resuelto):** el extracto de la web propia de un hotel imprimió el teléfono público de su pie. Se borraron los logs de esa corrida por la API a los pocos minutos; el número no está en ningún archivo del repo ni en esta nota. Regla para la próxima lectura: nada de `contexto:` sobre páginas con datos de contacto sin un filtro previo.

## 2. Cruce: los seis lugares de ChatGPT

Estado de fichas: último listado interno (run 43846689869, 16/11/2026, 62 fichas, sin contactos); no se releyó en vivo hoy. «Página propia» leída con la corrida del 19/11 (portada o la página de pesca que se indica).

| Lugar | Zona | En el listado oficial (19/11) | Ficha nuestra | Página propia con información pública | Qué publica (según su página) | Precio público con fecha |
|---|---|---|---|---|---|---|
| Hotel Spa Jardín del Paraná | Paso de la Patria | **Sí** (sin tilde) | **No** | Sí, leída el 19/11 | Hotel con spa, piscina y parque de 10.000 m² a 3 cuadras del río; «pesca guiada de dorado y otras especies» con guías; dorado, surubí, pacú y boga. Qué incluye el servicio de pesca: **no se leyó** (el texto se cortó) | No publica en lo leído |
| Posada del Paso | Paso de la Patria | **No con ese nombre** | **Sí**: #73, información pública, sin tarifa, sin consentimiento | Sí, `…/pesca/` leída el 19/11 | Lanchas de 6,40 m con motor de 90 hp de 4 tiempos, tarima, cajas térmicas, equipamiento de pesca completo y, entre lo que lista, «licencias de pesca» y «combustible libre»; puerto frente a la posada. Enlaza una página de paquetes que **no se leyó** | No en la página leída |
| El Gordo y el Flaco | Goya | **Sí** | **No** | **No encontrada** (la búsqueda del 19/11 no devolvió una web propia) | Solo lo que dice el listado: formatos de medio día y jornada completa, dorado, surubí, pacú | No |
| Campamento La Amistad | Goya | **Sí** | **No** | **No encontrada** (la búsqueda trae un homónimo en Santa Fe: no es este lugar) | Ídem | No |
| Posada Hambaré | Esquina | **Sí** | **No** (ni pública ni interna; solo está en notas de precios y en un borrador) | Sí, `pousadahambare.com.br` (portada leída el 19/11; páginas de paquetes leídas el 4 y 5/11) | Posada con vista a la confluencia de los ríos Corrientes y Paraná; dorado, pacú y surubí. Paquete de 2 días de pesca y 3 noches con alojamiento, comidas, bebidas, lancha, guía, carnada, combustible y licencia; el equipo no está incluido | **Sí, con fecha:** desde US$ 605 por persona en grupo de 3 y US$ 705 en grupo de 2 (2 días de pesca, 3 noches), leído el 4/11/2026; 3 y 4 días para grupo de 3: US$ 855 y US$ 1.105 (5/11). **Temporada de las tarifas: no publicada** |
| Posada del Muelle | Esquina | **Sí** | **No** | **No encontrada** (solo redes y portales de terceros; una publicación del propio lugar no se leyó) | Solo lo del listado | No |

**Lecturas del cruce**

1. **Cinco de los seis figuran en el listado oficial; Posada del Paso no**, con el nombre que usó ChatGPT. De dónde la sacó ChatGPT: no se sabe (puede ser su propia web, que sí existe). Una ausencia de nombre no prueba que no esté listada con otra grafía.
2. **Solo uno de los seis tiene ficha** (Posada del Paso) y es la que menos dice: no tiene tarifa. **De los seis, el que más tiene para mostrar con fecha es Hambaré, y no tiene ficha.** Es el candidato más barato para una ficha nueva (información pública atribuida, con las inclusiones del paquete y la tarifa «desde», leída el 4/11). Releerla antes de publicar: pasaron 15 días y el sitio muestra dos tablas distintas (ver `docs/borradores/viaje-dorado-amigos-2026-11-05.md`).
3. **Goya y el muelle de Esquina quedan sin datos propios.** Con el listado oficial solo se puede decir que figuran; sin web ni precio. Para esos tres lugares la vía es escribirles (sección 4(e)), no una ficha vacía.
4. **Fichas públicas que ya tenemos en las tres guías** (nombres de servicios, sin contactos): Paso de la Patria: FansFishing, Tatá Lodge, Posada del Paso, Del Paso Fishing, Pousada La Pesca y tres guías individuales; Goya: Ogaraity Apart Hotel y Experiencia Isoró (ambas con consentimiento y fotos) y la interna «Isoró Fishing Camp» (ejemplo, no pública); Esquina: Rebo Pesca y dos guías individuales públicas, más siete internas marcadas de ejemplo. **Ninguna de esas fichas está cruzada todavía con el listado oficial** (se puede hacer en el mismo paso que la marca de 4(a)).

## 3. Formulario: varias localidades y acompañante que no pesca

**Acompañante (ya resuelto).** `/armar-viaje/` y la tarjeta corta de cada guía piden `pescadores` (1 a 99, por defecto 1) y `acompanantes` (0 a 30, por defecto 0; etiqueta «Acompañantes (no pescan)»). El servidor compone la línea que lee el equipo: «1 pescador + 1 acompañante que no pesca», y la guarda en el meta `personas` (`pm_consulta_componer_personas`). Llega al aviso por correo y a «Lo que nos escribió» de Consultas. «Tres jornadas» cae en la opción «2–3 días» de `estadia` (solo en el formulario largo; la tarjeta corta no la tiene). Qué falta: nada obligatorio.

**Varias localidades (falta).** Hoy se elige **un** destino (`pm_destino`, un `<select>`; el servidor hace `sanitize_key` y guarda un solo `destino`). Las otras vías para decir «Paso, Goya o Esquina»: la casilla «Lugar» de «¿Qué es fijo y qué es flexible?» (si no se marca, se trata «otras zonas u otros lugares» como flexible, sin decir cuáles) y el mensaje libre. El equipo tiene que leer el texto para saber cuáles. El campo `localidad` (texto libre) existe pero solo lo usa el formulario de prestadores.

**Cambio mínimo propuesto (sin implementar):**

| Qué | Dónde | Detalle |
|---|---|---|
| Casillas «También me sirven» (hasta 5) | `vistas/viaje.php`, paso 1, bajo el selector de destino | Chips de casillas con las guías publicadas (`pm_destinos_lista`), con la ayuda «Marcá los que te sirven además del primero; los vemos todos y te comparamos». Se oculta el que está elegido arriba. Con JS: solo se muestran si «Lugar» no está marcado como fijo; sin JS, siempre visibles |
| Campo nuevo | `PM_CONSULTA_CAMPOS`: `destinos_extra` (etiqueta «También sirven», 200 caracteres) | Se lee `destinos_extra[]`, se valida contra las claves de destinos publicadas, se descartan repetidos y el principal, tope 5, y se guarda como una línea de nombres en español («Goya, Esquina»), igual que `fijo`. Entra solo en el aviso y en «Lo que nos escribió» (`pm_consulta_datos` ya recorre `PM_CONSULTA_CAMPOS`); la lista de Consultas y el acuse no cambian |
| Reenvío tras error | `consulta-campos.php` | `pm_previo_lista( 'destinos_extra' )`, igual que `fijo` |
| Preselección desde la página de la ciudad | `?destino=paso-de-la-patria&tambien=goya,esquina` | La página de guías por ciudad y las notas arman el enlace; el servidor ignora claves inválidas |
| Textos | `ui.json` es/pt/en | `viaje.tambien` (leyenda) y `viaje.tambien.ayuda` |
| Prueba | `Test WordPress` | Un envío con dos extras, uno inválido y uno igual al principal: llega «También sirven: Goya»; sin extras no cambia nada |

Cómo llega: el mismo aviso que hoy, con una línea más; el canal, el estado y el acuse no cambian. **Opcional, requiere decisión del dueño** (es comportamiento de correo saliente): que el acuse enlace también las guías de los lugares extra, hoy enlaza solo la del principal. Tamaño: unas 60 líneas de PHP, tres claves de texto por idioma y una prueba. No hay migración: los pedidos viejos no tienen el campo.

## 4. Propuesta

### (a) Página de guías por ciudad como respuesta a «recomendame lugares en X»

Es la vista `guias.php` (`/<región>/<provincia>/guias-de-pesca-en-<lugar>/`), que ya tiene respuesta corta, lista, paquetes, «Cómo elegir», FAQ y formulario. Orden propuesto y cambios:

1. **Respuesta corta** (ya existe). Agregar la línea «Los lugares que figuran en el listado de pesca de Turismo de Corrientes: N de N fichas» solo cuando algún lugar figura. Sacar de `guias.resumen.como` el «Pescamapa no le cobra al pescador» (es una negación defensiva; ver la regla del 19/11).
2. **«En una mirada»** (nuevo): tabla de hasta 6 filas (lugar, formato de salida, qué incluye según su página, temporada, precio con fecha o «a consultar»). Es lo que un asistente puede repetir y lo que el pescador compara. Sale de los mismos campos de las fichas; sin datos nuevos.
3. **CTA único arriba:** «Pedir opciones para 2 jornadas en {lugar}» con `?destino=` ya elegido y, si la ciudad tiene vecinas, un segundo enlace «Comparar {lugar}, {vecina 1} y {vecina 2}» con `&tambien=`.
4. **Bloque «Qué le preguntamos a cada lugar por vos»** (sección 4(b)), reemplaza «Cómo elegir guía» (se solapa: seis preguntas genéricas para que las haga el pescador).
5. **Tarjetas** (`parciales/prestador.php`), en este orden: nivel («Con acuerdo con Pescamapa» o «Información pública»), tipo y nombre, **marca de listado** (abajo), una línea de qué hace, qué incluye (según el propio lugar, solo lo leído), formato y temporada, precio con fecha («precio revisado en…») o «Precio: a consultar», «Datos al <fecha>» y el enlace de corrección. **Sin contacto, sin enlace al sitio del lugar** (regla del 17/11). Botón: «Pedir opciones para pescar en {lugar}», ya preselecciona el destino y deja el lugar como primera referencia.
6. **Marca de listado**, solo si el lugar figura: «En el listado de pesca de Turismo de Corrientes (consultado el 19/11/2026)». Es una fuente oficial: se puede enlazar a la página de la localidad en `corrientes.tur.ar` (una vez, en la nota de la sección, no en cada tarjeta). **Decisión del dueño:** la propuesta del dueño decía «registro de prestadores»; lo leído se llama «Experiencia de pesca» y no se ve que sea un registro ni una habilitación. Hasta confirmarlo, «listado». Dato nuevo en la ficha: `listado_oficial` (zona) y `listado_consultado` (fecha), que además vence a los 90 días como el resto.
7. **Salidas armadas, FAQ, formulario** (ya existen). Agregar a la FAQ «¿Qué le pregunto a un guía antes de reservar?» con el bloque de (b) como respuesta, que la FAQ visible repita en el JSON-LD.

Orden de las tarjetas: se mantiene «primero los lugares con acuerdo, después los de información pública» (`guias.lista.p`), con la regla de que **la comparación que mandamos** se ordena por cuánto encaja, nunca por comisión. Son dos cosas distintas y conviene que la página lo diga bien cuando haya más de un acuerdo por ciudad.

### (b) Bloque reutilizable «Qué le preguntamos a cada lugar por vos»

Texto final (es). Una frase de apertura y seis ítems; sin negaciones, sin «operador», sin promesas de precio.

> **Qué le preguntamos a cada lugar por vos**
> Cuando pedís opciones, les hacemos las mismas preguntas a los lugares y guías que encajan con tu viaje. Así recibís respuestas que se pueden comparar lado a lado.
>
> 1. **El total para tu grupo**, contando a quien te acompaña aunque no pesque.
> 2. **Las jornadas de pesca** y las noches que querés, en tus fechas.
> 3. **Qué incluye**: lancha, guía, combustible, carnada, licencia, comidas, traslados y equipo.
> 4. **Qué pasa si el clima no deja salir**: si se reprograma la jornada o cómo se ajusta el total.
> 5. **La seña o el anticipo** y cómo se paga.
> 6. **Qué fechas tienen lugar** y hasta cuándo te sostienen la reserva.
>
> Las respuestas llegan juntas, en hasta 48 horas, y reservás directo con el que elijas.

Notas de uso: va en la página de guías de cada ciudad, en `/armar-viaje/` (como «Qué vamos a preguntar») y, con una línea más («Podés hacerles las mismas preguntas vos»), en las notas de costo. Claves nuevas de `ui.json`: `preguntas.h`, `preguntas.p`, `preguntas.1` a `preguntas.6`, `preguntas.cierre`. El ítem 4 pide una respuesta, no la afirma: no promete reprogramación. «Reservás directo» está en la línea aprobada del 19/11; **el plazo de 48 horas es el prometido en el sitio** (`viaje.garantia`). Revisión de copy pendiente en `scripts/check-copy-rules.js` (sin «operador», sin «mejor precio»). En/pt después de la aprobación.

### (c) La línea sobre habilitación

El dueño dejó abierta la pregunta («¿los guías están habilitados?», omitida de la FAQ por falta de respuesta documentada). Lo que hay hoy y qué se puede decir:

- **Con fuente oficial (Corrientes):** Turismo de Corrientes publica listados de pesca por localidad (leído el 19/11/2026). Se puede decir que el lugar **figura** en ese listado. No se puede decir «habilitado» con lo leído.
- **Marco legal que un buscador mencionó y que no se leyó:** una ley provincial de turismo (la 6.309) que crea un Registro Provincial de Prestadores de Servicios Turísticos con inscripción obligatoria, y la 6.505 de turismo activo. **No se citan hasta leer el texto en el Boletín Oficial.** Pendiente de verificación antes de escribir un artículo o un número.
- **Dónde no hay fuente oficial leída (otras provincias):** consejo.

Texto propuesto (es), para la FAQ y la sección de la guía:

> **¿Cómo sé si un guía o un lugar está habilitado?**
> Pedile a cada uno su número de inscripción como prestador de turismo de la provincia y, si sale en lancha, la documentación de la embarcación. En Corrientes, Turismo de la provincia publica un listado de pesca por localidad: en cada ficha marcamos si el lugar figura, con la fecha en que lo consultamos. Figurar en un listado muestra que el lugar existe y que la provincia lo conoce; la inscripción te la confirma el propio prestador.

Si el dueño prefiere una versión más corta para la tarjeta: «Pedí el número de inscripción del prestador antes de reservar.» **Revisar la frase «la provincia lo conoce»**: es una inferencia, no algo que el sitio afirme. Se puede sacar.

### (d) Prioridad de destinos después de los tres del Litoral

Base: `docs/seguimiento-palabras-clave.md` (apariciones en funciones de IA de Google, 18/11, **período no indicado**; Search Console 3 al 17/11, impresiones de 1 a 8). **Son números chicos: ordenan, no deciden.** Lo que más pesa es dónde hay plata y fichas fechadas.

| # | Destino | Por qué | Qué falta para el mismo bloque |
|---|---|---|---|
| 1 | **Río Gallegos** | 15 apariciones en IA; tiene los dos acuerdos con comisión (Karku, River House; las tarifas de ambos no se publican, por pedido y por decisión) | Página de guías por ciudad con la marca de acuerdo y la línea sin tarifa; el bloque (b) con «cotizamos para tu semana» |
| 2 | **Bariloche** | 18 apariciones; varias fichas públicas, cinco con tarifa con fecha | El «En una mirada» sale casi hecho |
| 3 | **Itatí, Esteros del Iberá, Itá Ibaté** | Mismo listado oficial de Corrientes (hay páginas por localidad); fichas de lodges con tarifa con fecha y portugués con demanda por nombre | Cruzar fichas con el listado; tres páginas de ciudad |
| 4 | **Chascomús** | 37 apariciones en IA, el mayor número; guías individuales de ticket bajo | Decidir si el pedido de un día de pesca justifica el formulario largo |
| 5 | **La Paz y Concordia (Entre Ríos)** | Guías recientes (Concordia del 18/11), medición desde el 2/12 | Esperar la medición |
| 6 | **San Martín / Junín de los Andes** | 12 apariciones; fichas sin precio | Primero fichas con precio |

Los tres del Litoral (Paso, Goya, Esquina) son el piloto del bloque y de las casillas de varias localidades.

### (e) Lugares para que el dueño les escriba (solo nombre y por qué)

Regla de correos: plural («nosotros»), con la frase de seguimiento aprobada. Sin contactos aquí.

| Prioridad | Lugar | Por qué |
|---|---|---|
| 1 | **Posada Hambaré** (Esquina) | El único con precio fechado y paquete con inclusiones; hay demanda por nombre desde Brasil; falta la temporada de sus tarifas y una ficha |
| 2 | **Hotel Spa Jardín del Paraná** (Paso de la Patria) | Ofrece pesca guiada en su web; figura en el listado oficial; falta saber qué incluye y a cuánto |
| 3 | **Posada del Paso** (Paso de la Patria) | Ya tiene ficha pública sin tarifa; su página dice lo que ofrece pero no hay precio; ChatGPT lo nombró, aunque no figura con ese nombre en el listado |
| 4 | **Posada del Muelle** (Esquina) | Nombrado por ChatGPT y en el listado oficial; sin web propia leída |
| 5 | **El Gordo y el Flaco** (Goya) | En el listado oficial y nombrado por ChatGPT; sin ficha ni web |
| 6 | **Campamento La Amistad** (Goya) | Ídem; cuidado con el homónimo de Santa Fe |
| — | **Ogaraity Apart Hotel** y **Experiencia Isoró** (Goya) | Ya con consentimiento y fotos: pasar a «Con acuerdo» y completar el precio fechado es más barato que sumar nombres nuevos |
| — | **La Zona Lodge** y **El Rincón** | Ya pendientes (`docs/sesion-2026-11-17-al-19.md`) |

Qué preguntarles al escribir: las seis del bloque (b), más temporada de las tarifas, y si aceptan figurar con información propia. Con cada respuesta, una ficha con consentimiento deja de ser «información pública sin verificar».

## 5. Qué no se verificó

- La fecha de actualización de cada página del listado y cómo está armada cada entrada.
- Que el listado sea un registro o una habilitación (no se ve dicho).
- El texto de la Ley 6.309 y de la 6.505 (solo mencionadas por un buscador).
- Las fichas en vivo hoy (se usó el listado interno del 16/11) y que Posada del Paso siga igual.
- Contenido de la página de paquetes de Posada del Paso, la sección de pesca de Jardín del Paraná (se cortó) y las páginas de Hambaré desde el 5/11.
- El formulario: se leyó el código, no se probó un envío.
- Las cifras de IA y de Search Console son las del seguimiento, de cantidades pequeñas y sin período en una de las fuentes.
