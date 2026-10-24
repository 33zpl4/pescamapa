# Mapa de zonas de pique (decidido 23/10/2026, MVP construido 24/10/2026)

**Idea (negocio de pesca → dueño):** un mapa de dónde hay pique y de qué especie, que las otras webs no tienen. Sirve a pescadores, da motivo para volver y puede abrir la puerta a una app.

## Decisiones del dueño

- **Granularidad ~500 m–1 km**, no 10–20 m. Más gente se anima a compartirlo y protege los pesqueros. (Coincide con la regla vieja: *áreas, no puntos de pesca*.)
- **Lo alimenta mucha gente**, no solo prestadores. Los datos de operadores son bienvenidos, pero depender solo de ellos no sirve. Se les pide igual.
- **Lanzar vacío está bien**: todo es cómo se presenta. Tono de invitación: *"¿Pescaste en esta zona? Indicanoslo. Construimos entre todos un mapa de zonas de pique."* (redacción a afinar).
- Nada de app por ahora: web móvil (ubicación del navegador o tocar el mapa).

## Diseño propuesto (MVP)

- Tipo privado `pm_reporte` (plugin, `inc/pique.php`): **celda de cuadrícula** (~1 km, calculada en el navegador), especie, fecha, resultado opcional. **Nunca se guardan lat/lon exactos.**
- Envío público con el patrón de `inc/consultas.php`: honeypot `pm_web`, tiempo mínimo `pm_t`, límite por IP por hora (`HTTP_CF_CONNECTING_IP`).
- Salida agregada con demora (~48 h) y mínimo de reportes por celda para mostrarla; etiqueta "reportado por pescadores, sin verificar"; aviso de respetar vedas y reglamento.
- Vista `/pique/` (+ pt/en en `PM_SEGMENTOS`), sitemap, meta en `seo.php`, textos en `data/ui.json`, JS sobre Leaflet (como `assets/mapa.js`), CSS en `extra.css` solo con tokens; prueba en `test-wp.yml` (envío, agregado, honeypot, límite, sin lat/lon guardados); `node wordpress/build/revisar-marca.mjs`.
- Abiertas: moderación, borrado a pedido, política de privacidad, cómo pedirle datos a los operadores (preguntas para esa conversación aún sin preparar).

## Qué quedó construido (MVP, 24/10/2026)

- Vista `/pique/` (`/pt/pique/`, `/en/bite-zones/`): mapa Leaflet, resumen con fecha, formulario, aviso "sin verificar", enlaces en menú y pie, sitemap y `llms.txt`. Código: `inc/pique.php`, `vistas/pique.php`, `assets/pique.js`.
- **Celda de 0,01° (~1 km)**, calculada en el navegador. Al servidor solo llegan los índices enteros de la celda, la especie, el día (hasta 60 días atrás) y el resultado (hubo pique / poco / no picó). Nunca un punto exacto ni un nombre. Se guarda además un hash con clave de la IP, solo para descartar duplicados y limitar abuso (5 por hora).
- Reportes = tipo privado `pm_reporte` (wp-admin → Pique). **Moderar = mover a la papelera.** Aparecen en el mapa a las **48 h** (filtro `pm_pique_demora`) y hasta 180 días. Se muestra cada celda desde 1 reporte, con su cantidad.
- Probado en `test-wp.yml`: estado vacío, envío real, duplicado, celda fuera de Argentina, especie inválida, honeypot, campos guardados (solo celda), demora de 48 h, resumen, límite por hora. `verify-live.yml` revisa que la página exista.
- **No verificado**: el mapa en un navegador real (Leaflet, toque, geolocalización) ni en el teléfono. Pedir captura y prueba a mano al dueño.
- Pendiente: pedir datos a operadores (preguntas sin preparar), política de privacidad publicada, borrado a pedido (hoy: info@pescamapa.com y papelera), mínimo de reportes por celda si aparece abuso, filtro por especie, enlace desde cada guía a su zona.
