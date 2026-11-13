# Página "Armá tu viaje" (formulario de cotización)

Decidido el 26/10/2026: no enlazamos guías ni lodges concretos en las notas. Todo pedido de viaje llega a Pescamapa por este formulario, y nosotros armamos las opciones (guía + alojamiento) y cobramos **comisión** al prestador; no revendemos por ahora. Los tráficos y los pedidos reales deciden si más adelante pasamos a vender paquetes propios.

- URLs: `/armar-viaje/`, `/pt/planejar-viagem/`, `/en/plan-your-trip/`. Vista `themes/pescamapa/vistas/viaje.php`, textos `viaje.*` en `data/ui.json`.
- Mismo endpoint y misma bandeja que el formulario de las guías (`inc/consultas.php`, Consultas en wp-admin, aviso por email). Campos nuevos: duración, experiencia, alojamiento, país y, desde el 13/11/2026, **"¿Qué es fijo y qué es flexible?"** (opcional, casillas `fijo[]`: especie, fechas, lugar, duración de la estadía; se guarda como una línea de etiquetas en español en el meta `fijo`, sale en el aviso y en la caja "Lo que nos escribió"; sirve para saber qué tan amplio buscar). Las opciones de elección se guardan con la etiqueta en español para que el email se lea natural.
- Se preselecciona con `?destino=<clave>&especie=<clave>`: las notas enlazan así ("Armá tu viaje a Goya").
- Obligatorios: nombre y contacto. Anti-spam: honeypot `pm_web`, tiempo mínimo y límite por IP (igual que el resto).
- Plazo de respuesta prometido (dueño, 13/11/2026): **48 horas**, en `viaje.como.2.d`, `viaje.gracias.p` y `acuse.viaje` (es/pt/en). `acuse.prestador` y `consulta.ok.prestador` (alta de prestadores) no lo llevan; la confirmación del pescador en las fichas es `consulta.ok.pescador` (con 48 horas). La clave compartida `consulta.ok` ya no existe (13/11/2026).
- Falta enlazar desde la navegación, el pie y el sitemap: se hace después de mirar la captura (`capturas.yml`) y dar el visto bueno de diseño.
- Qué mirar en las consultas: país, época, experiencia y modalidad; de ahí sale qué contenido escribir (ver `docs/estrategia-contenido.md`).
- **Origen de la consulta (9/11/2026):** el pie de página guarda en `sessionStorage` la primera página de la visita, el sitio de origen y el `utm_source`, y los formularios los envían en `pm_entrada`, `pm_referente` y `pm_campana`. La consulta guarda además `canal` (lista `PM_CANALES` en `inc/consultas.php`; sumar ahí un asistente o buscador nuevo). Quien deja un email recibe un acuse de texto fijo (`acuse.*` en `data/ui.json`).

## Posicionamiento (13/11/2026)

**Decisión del dueño:** para el pescador, Pescamapa pasa a ser un servicio de "pedís una vez, nosotros les preguntamos a los operadores y comparamos por vos" (como un corredor que compara hipotecas: un solo pedido, él consulta a varios).

Mensaje aprobado (es; en/pt en `data/ui.json`):

- Título: **"Decinos qué querés pescar. Nosotros encontramos dónde."**
- Cuerpo: "Les preguntamos a los operadores que encajan con tu viaje y en 48 horas te respondemos con las opciones comparadas: quién tiene lugar, a qué precio y qué incluye. Un solo pedido en vez de diez. No te cobramos nada: si reservás, nos paga el operador, y vos pagás lo mismo que reservando directo."

**Reglas de copy (duras):** nunca "mejor precio" / "best price" / "cheapest"; "los operadores que encajan con tu viaje", nunca "todos los operadores"; nunca presentar a los operadores como intercambiables; la promesa de 48 horas se mantiene; ni cifras ni afirmaciones inventadas (nada de "cientos de pescadores" ni cantidad de operadores); marca según `docs/marca.md`. Comparamos operadores tengan o no acuerdo con nosotros y decimos quién nos paga comisión (una frase en "Cómo se sostiene", `ns.plata.p`).

**Consentimiento:** usamos los datos solo para responder; consultamos a los operadores que encajan **sin nombre ni contacto**; el nombre y el contacto se le dan a un operador solo cuando el pescador dice que sigamos con ese (`viaje.privacidad`, `consulta.privacidad`, política de privacidad y términos alineados el 13/11/2026).

**El problema de la fuga:** un pescador encontró a un operador en nuestra página y le escribió directo, y la comisión se pierde. Tres contramedidas decididas:

1. **Registro del lead ante el operador:** apenas llega el pedido le mandamos al operador el pedido **sin datos personales**; ese correo fechado es el registro. Condiciones propuestas: el cliente cuenta como nuestro si lo registramos nosotros o si el cliente nombra a Pescamapa; vigencia de 12 meses (borrador en `docs/condiciones-operadores.md`).
2. **Decirle al pescador cómo nos pagamos y pedirle que nos mencione:** `acuse.viaje` lo dice ("Si le escribís directo a un operador, decile que venís de Pescamapa") y la página explica la comisión.
3. **Que el pedido sea el camino más fácil del sitio:** el botón de cada ficha pasó de "Consultar disponibilidad" a "Pedir opciones para pescar en <lugar>" (pide el viaje, no ese operador; el operador elegido queda como primera referencia en el meta `prestador`), y la portada lleva un bloque con el mensaje.

Pendiente (no hecho): el envío automático del correo de registro al operador (comportamiento nuevo de correo saliente: lo decide el dueño) y el aviso en la ficha de que se muestran datos de contacto (hoy no se publican contactos).
