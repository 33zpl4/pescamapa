# Página "Armá tu viaje" (formulario de cotización)

Decidido el 26/10/2026: no enlazamos guías ni lodges concretos en las notas. Todo pedido de viaje llega a Pescamapa por este formulario, y nosotros armamos las opciones (guía + alojamiento) y cobramos **comisión** al prestador; no revendemos por ahora. Los tráficos y los pedidos reales deciden si más adelante pasamos a vender paquetes propios.

- URLs: `/armar-viaje/`, `/pt/planejar-viagem/`, `/en/plan-your-trip/`. Vista `themes/pescamapa/vistas/viaje.php`, textos `viaje.*` en `data/ui.json`.
- Mismo endpoint y misma bandeja que el formulario de las guías (`inc/consultas.php`, Consultas en wp-admin, aviso por email). Campos nuevos: duración, experiencia, alojamiento, país. Las opciones de elección se guardan con la etiqueta en español para que el email se lea natural.
- Se preselecciona con `?destino=<clave>&especie=<clave>`: las notas enlazan así ("Armá tu viaje a Goya").
- Obligatorios: nombre y contacto. Anti-spam: honeypot `pm_web`, tiempo mínimo y límite por IP (igual que el resto).
- No hay promesa de plazo de respuesta en la página; si querés una ("en 48 h"), se agrega en `viaje.como.2.d`.
- Falta enlazar desde la navegación, el pie y el sitemap: se hace después de mirar la captura (`capturas.yml`) y dar el visto bueno de diseño.
- Qué mirar en las consultas: país, época, experiencia y modalidad; de ahí sale qué contenido escribir (ver `docs/estrategia-contenido.md`).
- **Origen de la consulta (9/11/2026):** el pie de página guarda en `sessionStorage` la primera página de la visita, el sitio de origen y el `utm_source`, y los formularios los envían en `pm_entrada`, `pm_referente` y `pm_campana`. La consulta guarda además `canal` (lista `PM_CANALES` en `inc/consultas.php`; sumar ahí un asistente o buscador nuevo). Quien deja un email recibe un acuse de texto fijo (`acuse.*` en `data/ui.json`).
