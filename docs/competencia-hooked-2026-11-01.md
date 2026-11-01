# Hooked (hooked.com.ar): qué es, por qué lo recomienda ChatGPT y cómo competimos (1/11/2026)

Fuente: dos corridas de *Investigar sitios* (rama `investigacion`, carpetas `hooked-20261101-0520` y `hooked-2-20261101-0522`): portada,
sitemap (419 URLs), `llms.txt`, `robots.txt`, listado de guías, una ficha de guía (El Chino Pesca, Berisso), provincias de Corrientes y
Buenos Aires, calendario, especies, "quiénes somos", planes y precios para guías y lodges, páginas "para guías / lodges / operadores /
gobiernos" y dos notas del blog. Todo leído con navegador el 1/11/2026. **Nada se copia**; esto sirve para decidir qué hacemos nosotros.
Disparador: una respuesta de ChatGPT al dueño que lo pone primero para "contratar un viaje de pesca en Argentina".

## 1. Qué es Hooked

Un **marketplace de guías de pesca con reserva y pago online** (Mercado Pago), hecho por dos fundadores (marketing y producto) más una
asesora de relaciones institucionales. Lanzado en 2026 (el sitemap tiene fechas del 2/8 al 16/10/2026; el blog arranca el 13/9/2026).

| | Dato leído el 1/11/2026 |
| --- | --- |
| Guías | **101 perfiles** ("verificados"). La mayoría en Buenos Aires (Río de la Plata: Berisso, Quilmes, La Plata; lagunas; costa). Corrientes: 7 (Esquina 3, Paso de la Patria 4). Patagonia: casi nada real (un guía en Bariloche/Limay "consultá disponibilidad"; las tarjetas "Carlos M., Lucía R., Martín S." de Río Negro son maquetas que se repiten en la página de venta a guías) |
| Cobertura editorial | 24 provincias, **86 localidades**, 54 especies, 9 modalidades, un calendario especie × mes, blog "La Posta del Pique" (6 notas) |
| Modelo | **Cobra al guía una suscripción**, no comisión: Starter $12.500, Pro $22.500 (reserva online con seña), Business $39.500 ARS/mes (precio fundador 50 % el primer año); lodges desde $92.000 a $544.400 ARS/mes; servicios extra (contenido para Instagram, pauta, asistente de WhatsApp) desde $50.000 ARS/mes. Al pescador no le cobra nada. Página "para gobiernos" que vende reportes de reservas por jurisdicción |
| Cómo reserva el pescador | Si el guía tiene plan Pro: fecha, tipo de salida, horario, pescadores, extras, pago. Si no: "consultá disponibilidad" (manda fecha y grupo, el guía confirma). En el listado la mayoría está en "consultá" |
| Idiomas | Solo español rioplatense (`es-AR`). **Sin hreflang, sin inglés ni portugués** |
| SEO técnico | Muy bueno: título con precio ("Reservá con El Chino Pesca en Berisso — desde $100.000"), descripción ≤150, canonical, og:image por guía, `BreadcrumbList` en todo, `LocalBusiness` + `Person` + `Service` + `Offer` + `FAQPage` en cada ficha de guía, `CollectionPage` + `State` en provincias, `BlogPosting` + `FAQPage` en el blog, `ItemList` en listados. ≈60–100 enlaces internos por página |
| Preparado para asistentes de IA | `llms.txt` de 4.800 palabras (cómo funciona, planes, especies, modalidades, 24 provincias, 86 localidades, FAQ) y `robots.txt` que **permite explícitamente** GPTBot, OAI-SearchBot, ChatGPT-User y ClaudeBot. Esa es la razón de que ChatGPT lo cite primero: le dieron el resumen hecho |

### La ficha de guía (El Chino Pesca) es una página de producto

Orden de la página: título con "Reservá tu salida" + localidad + 4,9 ★ (199 reseñas) → galería (48 imágenes) → "Qué vas a pescar" (chips de
especie con foto) → "La experiencia" (relato en segunda persona) → cuatro cajas (dificultad, duración 8 h, grupo "4 lanchas · 5 por lancha",
modalidades) → **Incluye / No incluye** (lancha, caña, reel, línea, carnada, combustible / desayuno, almuerzo, **licencia**) → punto de
encuentro (el exacto se da al confirmar) → "Conocé a tu guía" (10 años, habla español) → reseñas con distribución 5–1 → embarcaciones
(modelo, motor, plazas) → galería → 10 preguntas frecuentes → caja de reserva fija: **$100.000 ARS por pescador**, fecha, tipo de salida,
horario, extras (desayuno + almuerzo $10.000), "reembolso total hasta 48 h antes", Mercado Pago.

Qué nos enseña: la unidad del precio (por pescador / por lancha), el "no incluye" al lado del "incluye", la embarcación con plazas, y la
política de cancelación a la vista son lo que un pescador compara. Las reseñas parecen importadas de Google ("hace 2 meses", sin fecha
exacta): no lo podemos confirmar.

## 2. Dónde es débil (y es exactamente donde nosotros somos fuertes)

1. **No hay reglamento.** Ni en la provincia, ni en la localidad, ni en la ficha: no dice qué licencia hace falta, cuánto cuesta, tallas,
   cupos ni vedas. La ficha de El Chino dice "licencia de pesca: no incluye" y nada más. Su calendario marca el dorado "en temporada los 12
   meses" sin mencionar las vedas provinciales que nosotros documentamos con fuente.
2. **Textos de destino genéricos de dos líneas** ("Capital mundial del dorado: palizadas, rollos y pasos de cardumen…"), iguales en
   `llms.txt`, en la provincia y en la localidad. No hay nivel del río, accesos, cómo llegar ni fuentes.
3. **Solo español.** El pescador brasileño o estadounidense no tiene nada ahí; el guía Yporá Pesca (Chaco) dice atender en portugués, pero
   la plataforma no.
4. **Patagonia y pesca con mosca internacional casi vacías**: su negocio es el pescador argentino de día (Río de la Plata, lagunas, costa,
   Litoral). Los lodges (planes de $92.000+) todavía no aparecen listados.
5. **Blog chico y sin fuentes**: seis notas "por el guía", reportes de pique con nombres de parajes y colores de señuelo; útiles, pero sin
   datos verificables ni fechas de verificación.
6. **La reserva real está en pocos perfiles**: en el listado, 6 de 10 guías dicen "consultá disponibilidad". Hoy es más directorio que
   marketplace.

## 3. Dónde es más fuerte que nosotros

1. **Volumen y cobertura del mercado argentino**: 101 guías con precio, 86 localidades, 54 especies. Nosotros: 8 destinos, 4 especies,
   ≈20 fichas, ninguna de guía de día en el Litoral con tarifa fechada.
2. **La ficha como producto** (precio por unidad, incluye/no incluye, lancha, política de cancelación, reseñas, FAQ, JSON-LD de oferta).
3. **URL propia por guía** (`/guides/<slug>`) con el precio en el título: posiciona para "[nombre del guía]" y "guía de pesca en [localidad]".
   Nuestras fichas viven dentro de la guía del destino, sin URL propia.
4. **Roster de proveedores con incentivo**: el guía paga y mantiene su perfil, fotos y fechas. Nosotros redactamos cada ficha a mano desde su
   sitio.
5. **Preparación para IA** explícita (`llms.txt` largo, bots permitidos por nombre). Nosotros ya tenemos `llms.txt` generado y "Cómo se
   contrata"; nos falta que liste prestadores y localidades.

## 4. Decisión de posicionamiento: no copiar el marketplace, ganar la decisión previa

Hooked vende **la reserva**. Nosotros vendemos **la decisión informada y la derivación**: qué dice el reglamento, cuándo ir, cuánto
cuesta de verdad, con quién salir, y el formulario que pasa la consulta a un operador con acuerdo. Para el pescador argentino somos la
página que lee antes de reservar en Hooked (o con el guía); para el internacional somos la única opción en su idioma. No construimos
reserva y pago hasta tener operadores con acuerdo y consultas medidas (estrategia v2, fase 61–90 días).

## 5. La base para competir en el dominio argentino (plan)

Orden por valor y por costo. Lo marcado **(dueño)** necesita una decisión suya.

| # | Qué | Por qué | Costo | Estado |
| --- | --- | --- | --- | --- |
| 1 | **Ficha de prestador como página de producto**: sumar `no_incluye`, `duracion`, `grupo` (lanchas × plazas), `unidad` del precio (por pescador / por lancha), `punto_de_encuentro` (zona, no exacto), `cancelacion` (texto del operador, fechado), `experiencia` (años). Mostrar "Incluye / No incluye" en dos columnas como en Hooked | Es lo que el pescador compara; hoy la tarjeta mezcla todo en "descripción" | Campos ACF + parcial + CSS; un script para migrar las 4 de Bariloche | Propuesto |
| 2 | **URL propia por prestador** (`/guias-de-pesca/<slug>/`, es/pt/en) con `BreadcrumbList` y, **solo en nivel 2 (con acuerdo)**, `LocalBusiness` + `Service` + `Offer`. Las de nivel 1 llevan `ItemList` desde la guía y una página propia con `noindex` hasta verificar **(dueño)** | Sin URL propia no competimos por "[guía] pesca" ni por "guía de pesca en Esquina"; la decisión del índice por región ya estaba pendiente (estrategia v2 §4) | Un tipo de vista nueva + ruta + sitemap + hreflang | **(dueño)** |
| 3 | **Litoral con guías de día y tarifa fechada (P2/P10 del backlog, ahora prioridad 1 del dominio argentino)**: Paso de la Patria, Esquina, Goya, La Paz. Nombres a descubrir en Hooked, registros provinciales e Instagram; **dato y precio solo del canal propio del guía** (su web, su Instagram, su Facebook) | Ahí Hooked tiene 7 guías y nosotros 0 con tarifa; es nuestro contenido más leído | Ronda `investigar` por guía + script de fichas (molde `prestadores-bariloche-dia-2026-10-31`) | Propuesto |
| 4 | **"Qué se pesca cada mes, y qué está vedado"**: calendario especie × mes × provincia que cruce nuestras vedas y tallas con fuente y fecha | Su calendario no tiene vedas; es la pregunta que un asistente de IA responde mal con sus datos | Una vista nueva que lea los campos de reglamento que ya existen; nota pilar es/pt/en | Propuesto |
| 5 | **`llms.txt` con prestadores y localidades**: una línea por ficha pública (nombre, destino, especies, "según el operador, leído el…") y una por agua | Hooked le dio a ChatGPT 86 localidades y 101 guías para citar; nosotros le damos 8 guías y 6 notas | `inc/maquinas.php` | Propuesto |
| 6 | **Parte del río** (en vez de "reporte del pique"): nota corta mensual por destino con el nivel del río (dato propio), la veda vigente y una cita atribuida de un operador con acuerdo | Es su formato que más engancha, pero el nuestro tiene dato verificable | Plantilla de nota + dato de nivel que ya tenemos | Después del primer acuerdo |
| 7 | **Reseñas: no inventamos ni importamos.** Mostrar en la ficha de nivel 2 el enlace a sus reseñas públicas (Google) con la fecha de lectura y el número, nunca el texto | Evita el riesgo de reseñas copiadas; mantiene la honestidad que es nuestro activo | Un campo `resenas_url` + `resenas_fecha` | Propuesto |
| 8 | **Página "Para guías y lodges"** propia: qué damos (consultas calificadas de pescadores de es/pt/en, ficha con fuente, sin suscripción), qué pedimos (confirmar datos, respetar la comisión), formulario. Hoy ese pescador-proveedor cae en "¿Es tu negocio?" por mail | Hooked tiene cinco páginas de venta al proveedor; nosotros ninguna, y el canal "¿Es tu negocio?" ya es la prospección entrante (`docs/prestadores.md` §2) | Una página es/pt/en | Propuesto |

**Regla que no cambia:** un perfil en Hooked **no es "el sitio del operador"**: es un canal intermediario, como una agencia (`docs/prestadores.md`,
regla 5). Sirve para descubrir nombres, localidades y especies; el precio y el "incluye" se publican solo si el propio guía lo dice en un
canal suyo. Si lo único que existe es el perfil de Hooked, la ficha va sin precio y con "según su perfil público" como fuente **(dueño:
confirmar si aceptamos esa atribución)**.

## 6. Qué no sabemos

- Tráfico, posiciones y reservas reales de Hooked: no hay datos. Las "199 reseñas" y "101 guías" son lo que muestra su sitio.
- Cuántos de los 101 guías tienen reserva online activa (en la portada y el listado la mayoría está en "consultá disponibilidad").
- Si los lodges pagan los planes que publican: no vimos ningún lodge listado.
- Si ChatGPT lo recomienda por su `llms.txt` o por otras señales: es la hipótesis más simple y coincide con que el texto de ChatGPT repite
  frases de ese archivo ("comparar guías, especies, precios y modalidades", "reserva y pago online en determinados servicios").

## 7. Rulings del dueño (1/11/2026)

- **URL por ciudad, sí** ("Guías de pesca en <ciudad>", es/pt/en, guías listados en nivel 1). URL por guía: no por ahora.
- **Precios de Hooked = base interna** para diseñar nuestros paquetes (Esquina desde ARS 480.000 por lancha, Berisso ARS 100.000 por pescador, leídos el 1/11/2026). Nunca se publican como dato.
- **Se contacta a los guías** con oferta concreta (ficha gratis + consultas calificadas, comisión solo en paquetes); sin cuota fija hasta medir demanda.
- **Modelo todavía abierto**: listar guías uno a uno vs paquetes propios. Dato para la decisión: Hooked cobra suscripción fija y no muestra comisión; a nosotros la comisión por derivación no nos paga en la salida de día argentina (15 % de ARS 100.000 ≈ USD 10) y sí en paquetes de varios días y en el extranjero.

## 8. Qué se hizo hoy y qué sigue

- Hecho: dos lecturas completas, este documento, puntero en `CLAUDE.md`, entrada en `docs/estado.md`, backlog actualizado (P10 sube, P12 nuevo).
- Sigue (en orden): el dueño decide los puntos 2 y la atribución de la regla; mientras, 1, 3, 4 y 5 se pueden empezar sin esperar.
