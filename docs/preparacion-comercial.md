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
2. **La capa "cuánto cuesta" por destino.** Un bloque fijo en cada guía: "Cuánto cuesta salir a pescar en X", con rango **fechado y con fuente** cuando un prestador lo confirme; mientras no haya dato, "a consultar" + formulario. Es el H2 que responde "tarifas / precio" sin inventar.
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
