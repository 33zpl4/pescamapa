# Prepararnos para las búsquedas de alta intención (28/10/2026)

Decisión del dueño (28/10/2026): hay que prepararse para las consultas con intención de contratar ("guía de pesca en X tarifas", "alquiler de botes", "pacotes de pescaria"),
**posicionarnos** y **ganar dinero con el tiempo**. Sigue valiendo la estrategia de `docs/prestadores.md` (tráfico gratis y neutral primero, comisión después, contacto de
prestadores interno). Esto no cambia el orden: arma la cañería para que, cuando llegue la intención, el camino hasta la comisión ya exista y se mida.

## Qué ya existe (no rehacer)
- Ficha de prestador con precio de referencia **y fecha**, "qué incluye", condición especial por Pescamapa, acuerdo comercial (interno), estado y verificación (`inc/prestadores.php`).
- Consulta "Armá tu viaje" / `#consulta` en cada guía: guarda destino, especie, fecha, personas, **página de origen**, idioma, estado (nueva → derivada → concretada) y a qué prestador se derivó (`inc/consultas.php`).
- Reglas de publicación: precio solo con fecha; contacto interno; sin ordenar por comisión; todo enlace pago rotulado (`docs/modelo-alojamientos.md`).

## Qué falta, en orden
1. **Medir antes de vender.** Qué búsqueda trae a quien consulta: guardar el *landing* y la consulta de entrada (sin cookies ni datos personales extra) en cada consulta, y mirar Search Console por página. Sin esto no sabemos cuál palabra paga.
2. **Página propia "Cuánto cuesta pescar en X" (corregido 28/10/2026, a pedido del dueño: las guías ya son largas y un bloque enterrado no rankea por "precio").**
   Una página por destino **solo si tiene contenido real**, no una cáscara de "a consultar". Contenido que se puede sostener sin inventar: costo de la licencia (con la fuente oficial, si existe),
   de qué se compone el precio de una salida (lancha, días, qué incluye) y **qué preguntar antes de contratar**, rangos de prestadores **solo si están confirmados y fechados**, alojamiento con fecha,
   y el formulario. En la guía queda un resumen de dos líneas con enlace a la página. Destinos sin ningún dato fechado: solo el resumen en la guía hasta que haya.
   Piloto candidato: Goya (ya tiene fichas con precio cargado; verificar fechas antes de publicar).
3. **Oferta de prestadores relevada, no solo listada.** Por destino prioritario (Goya, Esquina, Paso de la Patria, La Paz, San Martín de los Andes, Chascomús): 3–5 prestadores con sitio propio, relevados con `docs/mensajes-prestadores.md`, estado "relevado" → "acordado". Sin oferta no hay comisión.
4. **Páginas por intención, solo donde hay datos.** "Guías de pesca en X" (lista + qué incluye + cómo pedir) y "Alquiler de botes / bajadas" para Chascomús cuando haya fuentes. En portugués, "pacotes de pescaria" para Corrientes. No se crean vacías.
5. **Posicionamiento.** Una frase que se repite en todas: *"Lo investigamos nosotros, mostramos las fuentes y, si querés, te armamos la salida."* Diferencia: neutral (no ordenamos por comisión), fechado, con la fuente a la vista.
6. **Cobro (último, solo con consultas):** comisión directa → afiliado rotulado → destacado. Sin maquinaria hasta que lleguen consultas (decisión 24/10/2026).

## Reglas que no se mueven
Nada de precios sin fecha y fuente; nada de "el mejor"; nada de contacto de prestadores; nada de fotos ajenas; sin ordenar por comisión; claims con fuente (dorado, "más pescado", etc.).

## Decisiones que necesito del dueño
- Cuánto del precio se muestra en la ficha pública (rango fechado vs. "a consultar" siempre) una vez definida la fase 2.
- Prioridad de destinos para relevar prestadores primero.
- Si se abre una página de "Guías de pesca en X" por destino aun con pocos prestadores confirmados (recomendado: no hasta tener 3).
- La página "Cuánto cuesta": ¿piloto en Goya?

## Modelo de paquetes propios (dueño, 28/10/2026) y auditoría de la lista de Goya

**Modelo:** vendemos opciones armadas por nosotros. La página del destino muestra qué opciones hay (modalidad, duración, qué incluye) y un **"desde"** como referencia. Si llega una
consulta de un grupo concreto, el dueño habla con los guías y ve cuál puede adaptar el paquete. Los guías no se nombran en la página (el contacto y la identidad de cada uno siguen internos).

**Lista pegada el 28/10 sobre Goya (otra IA):** no se publica nada de ahí.
- Rango "ARS 60.000–150.000 por jornada", fechado en julio de 2026, sin fuente: **no se usa** (precio viejo en pesos, sin respaldo).
- Cuatro prestadores/alojamientos con teléfonos y WhatsApp, sin enlace a sus sitios; "recomendados por el Ministerio de Turismo provincial" sin fuente. Sirven solo como **candidatos a relevar**; los teléfonos no van al repo ni a la web.
- Modalidades y especies que menciona ya las cubrimos en la guía de Goya. "La licencia puede no estar incluida": hay que preguntarlo a cada guía.
- Ninguno de los dos prestadores que ya tenemos cargados aparece en esa lista: la oferta real de Goya es más grande que lo que tenemos.

**Condiciones antes de publicar un "desde":**
1. Un guía lo confirmó y acepta sostenerlo, con **fecha, unidad (por lancha o por persona), qué incluye y qué no** (licencia, alojamiento, traslado). Un "desde" que no podemos honrar es publicidad engañosa.
2. Moneda: el peso se desactualiza en semanas. Decidir entre pesos con fecha visible, dólares de referencia, o ambos (y reales para Brasil).
3. **Revisar con abogado/contador antes de vender paquetes en nombre propio:** en la Argentina el rol de agencia de viajes (registro, seguro, facturación, defensa del consumidor) es distinto del de intermediario que deriva consultas. No lo afirmamos: se consulta.
4. La neutralidad se redefine: ya no es "no ordenamos por comisión" a secas, es "te mostramos las opciones y qué incluye cada una; nuestra comisión no cambia lo que pagás".

**Qué se construye (cuando se confirme):** un tipo de contenido "paquete" por destino: título, modalidad, duración, capacidad, qué incluye, qué no incluye, desde (monto, moneda, unidad, **fecha**), estado (acordado / no), guías que pueden armarlo (interno), notas. La página "Paquetes de pesca en X" lista solo los acordados; sin ninguno, no se publica.

## Hecho (28/10/2026): página "Paquetes de pesca en Goya"
- Decisión del dueño: publicarla ya, **sin precio**, para que empiece a posicionar; los precios llegan en ≤24 h y se cargan en wp-admin (guía de Goya → pestaña "Paquetes").
- URL: `/paquetes-de-pesca/goya/` (`/pt/pacotes-de-pesca/…`, `/en/fishing-packages/…`). Una página por destino **y por idioma**, solo si ese idioma tiene paquetes publicados (si no, 404). Lleva hreflang, sitemap, JSON-LD (ItemList de TouristTrip + migas) y entrada en `llms.txt`; la guía enlaza desde su resumen.
- Cada paquete: título, resumen, modalidad, duración, capacidad, qué incluye / qué no, variantes, "desde" (monto + moneda + unidad + **fecha**: sin los cuatro, no se muestra), y un campo interno que nunca sale. Sin precio dice "lo cotizamos según tu grupo y tus fechas"; sin "incluye" dice "lo confirmamos al cotizar".
- Contenido cargado: `wordpress/scripts/paquetes-goya-2026-10-28.php` (6 paquetes por idioma, desde el brochure del prestador, sin nombrarlo). **No** hay precios, ni qué incluye, ni capacidad de pesca: el brochure no los trae.
- Para sumar otro destino: cargar la pestaña "Paquetes" en su guía (por idioma); la página aparece sola.
- Cheyenne está registrada como agencia en Bitácora y se factura por ahí (dueño, 28/10/2026): queda resuelto el rol de agencia; falta dejar por escrito voucher, condiciones y cancelación.

## SEO de la página de paquetes (28/10/2026, a pedido del dueño: el objetivo es rankear)
Hecho en la página `/paquetes-de-pesca/<destino>/` (es/pt/en):
- **Consulta objetivo en cada lugar donde Google lo mira:** title (≤60, con "opciones y precios"), meta description (~140, con modalidades y qué incluye), H1, bajada, primer párrafo y títulos de los paquetes ("Pesca de dorado en la Reserva Natural Isoró (Goya)", "Pesca nocturna en Goya", "Paseo en lancha…, Goya"). En portugués se usa "pescaria" (cómo buscan los brasileños) y "Argentina" en el H1.
- **Estructura:** un H1, H2 por sección (cómo funciona, cada paquete, antes de viajar, preguntas frecuentes), H3 dentro; migas; enlace a la guía de arriba (reglamento, licencia, calendario).
- **Contenido que responde la intención:** intro, qué incluye cada opción, preguntas frecuentes (cuánto cuesta, qué incluye, licencia, cómo cotizar) y "Antes de viajar" con fuente y fecha. Precio "desde" fechado cuando llegue.
- **Datos estructurados:** ItemList de TouristTrip (con `offers` solo si hay precio fechado), WebPage con `dateModified`, FAQPage y BreadcrumbList; imagen social con la foto de la guía.
- **Confianza y frescura:** "Por … · Actualizado el …" visible.
- **Enlaces internos:** desde el resumen de la guía y desde las fichas de especie donde se pesca (anclas "Paquetes de pesca en Goya"); hreflang entre idiomas, sitemap y `llms.txt`.
Falta (no se puede hacer desde el repo): pedir la indexación en Search Console, mirar impresiones y posición por consulta a los 7–14 días y reforzar lo que quede en 8–20; pedir enlaces entrantes (prestadores, notas); fotos propias de las salidas con texto alternativo cuando las ceda el prestador; reseñas reales de quienes viajen.
