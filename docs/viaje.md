# Página "Armá tu viaje" (formulario de cotización)

Decidido el 26/10/2026: no enlazamos guías ni lodges concretos en las notas. Todo pedido de viaje llega a Pescamapa por este formulario, y nosotros armamos las opciones (guía + alojamiento) y cobramos **comisión** al prestador; no revendemos por ahora. Los tráficos y los pedidos reales deciden si más adelante pasamos a vender paquetes propios.

- URLs: `/armar-viaje/`, `/pt/planejar-viagem/`, `/en/plan-your-trip/`. Vista `themes/pescamapa/vistas/viaje.php`, textos `viaje.*` en `data/ui.json`.
- Mismo endpoint y misma bandeja que el formulario de las guías (`inc/consultas.php`, Consultas en wp-admin, aviso por email). Las opciones de elección se guardan con la etiqueta en español para que el email se lea natural. Los campos están en la sección "Formulario (UX del 13/11/2026)" de abajo.
- Se preselecciona con `?destino=<clave>&especie=<clave>`: las notas enlazan así ("Armá tu viaje a Goya").
- Obligatorios: nombre y contacto. Anti-spam: honeypot `pm_web`, tiempo mínimo y límite por IP (igual que el resto).
- Plazo de respuesta prometido: **24–48 horas** (dueño, 19/11/2026; antes 48 horas, 13/11/2026), en `viaje.garantia`, `viaje.gracias.p`, `acuse.viaje`, `contraste.con.3` y las demás claves del plazo (es/pt/en, más `llms.txt`). `acuse.prestador` y `consulta.ok.prestador` (alta de prestadores) no lo llevan; la confirmación del pescador en las fichas es `consulta.ok.pescador` (con 48 horas). La clave compartida `consulta.ok` ya no existe (13/11/2026).
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

**Seguimiento al cliente (15/11/2026):** al pasar un cliente a un operador, el cierre del mensaje es «el operador sigue desde aquí; te escribo en unos días para asegurarme de que todo va bien» (con una fecha real, cumplida), no «tratá directo con el operador». Frases en es/en/pt y reglas en `docs/prestadores.md` → «Seguimiento al cliente».

## Formulario (UX del 13/11/2026)

**Por qué:** el primer pedido real (un pescador extranjero, lodge de gama alta) mostró lo que el formulario no captaba: fechas aproximadas ("alrededor del 11 al 13, con una semana de margen"), un acompañante que no pesca y comparte la habitación, sin equipo ni waders, estadía corta (2-3 días), especie fija y lugar abierto. El equipo tenía que escribir por email para saber qué era fijo y qué flexible.

**Estructura** (3 secciones; solo nombre y contacto son obligatorios):

1. **Qué y dónde:** destino, especie, `fijo` (casillas), modalidad, alojamiento.
2. **Cuándo y quiénes:** fechas, flexibilidad, meses, días de pesca, pescadores, acompañantes, experiencia, equipo propio.
3. **Cómo te escribimos:** nombre, país, canal de contacto (email o WhatsApp), mensaje. Al lado del botón, una sola línea (`viaje.garantia`, ver «Hechos confirmados el 19/11/2026»).

**Campos y metas** (vista `vistas/viaje.php`; renderers compartidos en `pescamapa-core/inc/consulta-campos.php`; la tarjeta corta de cada guía, `parciales/consulta.php`, usa los mismos: fechas, flexibilidad, pescadores, acompañantes):

| Campo posteado | Meta | Notas |
|---|---|---|
| `fecha_desde`, `fecha_hasta` | `fecha_desde`, `fecha_hasta` (Y-m-d) | dos `<input type="date">`, `min` = hoy; ver "Por qué fechas nativas" |
| `flexibilidad` (exacta, dias, semana, mes, nose) | `flexibilidad` (etiqueta en español) | chips |
| `meses[]` (hasta 12, `AAAA-MM`) | `meses` ("enero 2027, febrero 2027") | chips; con JS solo aparecen con "Todavía no sé" |
| `estadia` (uno, dos-tres, cuatro-cinco, semana, mas) | `estadia` | días de pesca, aparte de las fechas de viaje |
| `pescadores` (1-99, por defecto 1), `acompanantes` (0-30, por defecto 0) | `pescadores`, `acompanantes` | con 0 acompañantes el meta no se guarda |
| `equipo_propio` (si, parte, no) | `equipo_propio` | lista `traeequipo`; "no" incluye waders |
| `fijo[]` | `fijo` | ahora solo **Especie** y **Lugar** (las fechas y la estadía tienen su campo). La lista conserva las claves viejas `fechas` y `estadia` para que un POST viejo se siga aceptando |
| `contacto_via` (email, whatsapp), `contacto_email`, `contacto_tel` | `contacto` + `contacto_via` | `contacto` sigue siendo el valor elegido (el acuse usa `is_email`) |
| `cuando`, `personas` (viejos) | `cuando`, `personas` | siguen aceptándose |

**Resúmenes en español** (el aviso al equipo, "Lo que nos escribió" y la lista de Consultas leen `cuando` y `personas`): se arman en `pm_consulta_componer_cuando()` y `pm_consulta_componer_personas()`. Ejemplos: `12/01/2027 al 14/01/2027 · ± una semana · 2–3 días de pesca`; `fechas sin definir, meses posibles: enero 2027, febrero 2027`; `1 pescador + 1 acompañante que no pesca`; `3 pescadores`. Si llega un `cuando` escrito (formulario viejo) va primero, tal cual.

**Compatibilidad:** un POST con solo `cuando`/`personas`/`contacto`/`duracion` (páginas en caché, tarjeta vieja) se guarda como antes. Los resúmenes nuevos solo reemplazan a los viejos si llegaron los campos nuevos (`personas` solo si llegó `pescadores` o `acompanantes`).

**Validación en el servidor (nunca se confía en el cliente):** una fecha que no existe (30 de febrero), pasada (más de un día atrás) o a más de 2 años se descarta sin rechazar el pedido; `fecha_hasta` anterior a `fecha_desde` se descarta; meses fuera de los próximos 12 (más uno de margen por caché) se ignoran; los números se acotan a 1-99 y 0-30; una opción que no está en la lista se ignora. El pedido solo se rechaza por nombre vacío o contacto vacío o inválido (email con `is_email`; WhatsApp de 7 a 15 dígitos).

**Errores y datos conservados:** si el pedido falla, lo escrito se guarda 30 minutos en un transient (`pm_previo_<token>`, token de 16 caracteres en `?pm_r=`), el formulario vuelve con todo completado y el error junto al campo (`aria-invalid`, `aria-describedby`, foco en el primero con JS). Solo los pedidos de viaje; no se guarda nada si no hay error. Anti-spam intacto: honeypot `pm_web`, tiempo mínimo (4 s) y 6 pedidos por hora por IP (un exceso también devuelve el formulario con lo escrito).

**Por qué fechas nativas (`<input type="date">`):** abre el calendario del sistema en todos los teléfonos y escritorios, sin librería (no hay dependencia que mantener ni que pese), con teclado y lector de pantalla del navegador. El formato lo pone el navegador según el idioma del dispositivo, y el valor viaja siempre como `AAAA-MM-DD`. El rango se ayuda con `min`/`max` y, con JS, "hasta" no puede ser anterior a "desde"; sin JS, lo valida el servidor.

**Sin JavaScript:** todos los campos son inputs comunes; los chips de meses siempre se ven; ambos campos de contacto se ven (la elección email/WhatsApp oculta el otro con CSS `:has()`; sin `:has()` se ven los dos y se usa el que se llene). Con JS: botones − / + en los contadores, meses solo con "Todavía no sé" (que oculta las fechas), un chip elegido se desmarca al tocarlo de nuevo, `required` sigue al canal elegido.

**Más liviano en el teléfono (13/11/2026, pedido del dueño):** (1) meses en grilla de 4 columnas (6 desde 600 px), nombres cortos (`viaje.mes.corto.N`) con el año una vez como etiqueta Bebas (`.anio`); el nombre completo "enero 2027" va en texto oculto dentro del `label` y es lo que se guarda y se compone en `cuando`; (2) chips más densos (alto 44 px, relleno 9 px, hueco 6 px, texto 14 px); (3) modalidad, alojamiento y experiencia viven en un `<details class="afinar">` cerrado por defecto ("Afinar el pedido (opcional)") al final del paso 2; se abre solo si algún campo trae valor, hoy solo al volver de un error (ninguno se preselecciona por la URL); (4) botón "Pedir mis opciones" (y el de la tarjeta de paquetes), barra fija `viaje.barra` "Pedir opciones", "Acompañantes (no pescan)"; a ≤ 420 px cada contador va en su bloque (etiqueta en una línea, contador y ayuda debajo). La opción de flexibilidad "mes" ahora dice "Dentro del mes" (es/pt/en) para que las cinco quepan en dos filas a 390 px. Alto del formulario a 390 px: 2811 → 2192 px (es).

**Qué no se verificó:** los selectores de fecha reales de iOS/Android, lectores de pantalla, el comportamiento de `:has()` en navegadores viejos, y el correo (el aviso con los resúmenes se prueba leyendo "Lo que nos escribió", no el email enviado).

## Hechos confirmados por el dueño (19/11/2026) y cambios

- Mandamos las opciones en **24–48 horas**.
- El teléfono y el email del visitante **no** se pasan enseguida a los lugares: le mandamos las opciones nosotros y recién cuando elige un lugar lo conectamos.
- El visitante **paga lo mismo** que reservando directo.
- **Presupuesto (opcional)** por pescador y por día, en US$ (los lodges y guías cotizan en dólares): lista `opciones.presupuesto` de `ui.json` (`hasta-150`, `150-300`, `300-500`, `mas-500`, `nose`), campo `presupuesto` en el paso 2 (visible, no dentro de «Afinar»). Mismo recorrido que `alojamiento`: chips → lista blanca en `consultas.php` (una clave inventada se ignora, nunca rechaza el pedido) → meta `presupuesto` con la etiqueta en español (p. ej. «US$ 150–300») → aviso por email y caja del admin (`PM_CONSULTA_CAMPOS`) → se conserva tras un error (`PM_CONSULTA_PREVIO`). Sin precarga por URL. La tarjeta corta de las guías no lo lleva.
- **Línea junto al botón** (`viaje.garantia`, la única; reemplaza «Te respondemos en 48 horas. Es gratis…»). `viaje.privacidad` perdió la cláusula repetida de «le damos tu nombre… únicamente al lugar».
- **Cómo funciona:** tres pasos numerados dentro de la columna «Con Pescamapa» del bloque `parciales/contraste.php` (home y formulario). Se quitó el bloque «Cómo funciona» aparte de la página del formulario (`viaje.como.*`) para no decir lo mismo dos veces; la columna lateral conserva solo «Qué le preguntamos a cada lugar».
- Home: el bloque «Dónde conviene ir en <mes>» va después del contraste.
- Tests: `.github/workflows/test-wp.yml` (render en 3 idiomas, envío con presupuesto, valor inventado, conservación tras error, orden de la home). No se corrió en Actions.
