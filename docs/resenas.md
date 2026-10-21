# Reseñas de prestadores — diseño y plan

Decisión del dueño (21/10/2026): queremos reseñas por prestador, al estilo de
los sitios de free tours (el guía recibe opiniones y eso da confianza), pero
**no desde el día uno**: se prepara la infraestructura y se lanza cuando haya
volumen. Este documento fija el diseño para no rehacerlo después.

## Principio

Solo opina quien viajó. Todas las consultas pasan por Pescamapa, así que ya
tenemos la señal que otros sitios pagan caro: **quién contrató a quién y
cuándo**. Una reseña se pide únicamente después de una consulta marcada
**"concretada"** y derivada a ese prestador.

## Fases

| Fase | Qué | Cuándo |
| --- | --- | --- |
| 0 — hoy | Se registra **cuándo** se concreta cada viaje (`concretada_el` en la consulta, wp-admin → Consultas) y a quién se derivó. Nada público | Hecho (21/10/2026) |
| 1 | Testimonios: después del viaje pedimos por WhatsApp 2–3 líneas, con permiso expreso, y se cargan como cita en la tarjeta del prestador. Sin estrellas | Con los primeros viajes concretados |
| 2 | Reseñas verificadas: enlace personal (token, un solo uso, vence) enviado al contacto de la consulta concretada; formulario con puntaje 1–5, texto y fecha del viaje; moderación antes de publicar; derecho a réplica del prestador | ~10 viajes concretados en el destino |
| 3 | Puntaje promedio visible y marcado `aggregateRating` | Con volumen suficiente por prestador (propuesta: 5+) |

## Modelo de datos previsto (Fase 2)

Tipo interno `pm_resena` (privado, como `pm_consulta`):

- `prestador` (post), `consulta` (post de origen), `token` (hash), `vence`,
  `puntaje` (1–5), `texto`, `nombre_pila`, `fecha_viaje`,
  `estado` (pendiente | publicada | rechazada), `replica` (texto del prestador).
- Una reseña por consulta. El enlace se arma desde la consulta
  (botón "Pedir reseña" que copia el enlace para WhatsApp/email).
- Mostrar: tarjeta del prestador con "N reseñas" y las últimas dos.

## Reglas

- **Sin incentivos** a cambio de reseñas positivas; las negativas se publican
  si cumplen las normas (sin insultos ni datos personales).
- El prestador ve la reseña y puede responder; no puede borrarla.
- Nombre de pila y fecha del viaje visibles; contacto del pescador, nunca.
- **Estrellas en Google**: las reseñas que un sitio recoge sobre las
  empresas que lista pueden no mostrar estrellas, y marcarlas mal es
  riesgo de penalización. Se marca `aggregateRating` solo en Fase 3 y
  revisando la política vigente en ese momento.
- Las reseñas son contenido de terceros: cualquier cambio en los términos
  (privacidad, moderación) se revisa antes de lanzar la Fase 2.

## Qué falta para lanzar la Fase 1

Que alguien marque las consultas como "concretada" (hoy el estado existe pero
nadie lo usa porque casi no hay tráfico). Ese hábito es la infraestructura
real: sin él, no hay a quién pedirle nada.
