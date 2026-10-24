# Mapa de zonas de pique (decidido 23/10/2026, NO construido todavía)

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
