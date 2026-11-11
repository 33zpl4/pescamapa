# Banco de paquetes de plataformas (11/11/2026)

Listados públicos de TripAdvisor y GetYourGuide que pegó el dueño el 11/11/2026. **Es material interno, no se publica**: sirve como banco para armar paquetes cuando el destino tenga guía propia y el operador valide. No se copian descripciones, fotos ni reseñas; solo hechos (precio, duración, qué incluye, licencia), con fuente y fecha. Criterio del dueño: lo que guía todo es lo que le sirve al pescador; el operador valida o lo sacamos.

Los precios están en la moneda en que se publicaron. GetYourGuide (versión es-ES) publica en euros; no se convierten porque no tenemos tipo de cambio euro con fuente.

## Ya publicados en Bariloche (docs/paquetes-bariloche-fichas-2026-11-11.md)

Trout Bariloche, Southern Rivers Patagonia, Patagonia in the Loop, Patagonia Drift Fly Fishing, Outfitters Patagonia. Un operador, un paquete: el listado de TripAdvisor "Fly Fishing Trips in Bariloche" (desde US$ 100, sin unidad clara) se firma "Trout Bariloche" en las respuestas del dueño, pero no se pudo confirmar que sea el mismo; no se publicó.

## GetYourGuide (leído por el dueño, 11/11/2026)

| Operador | Destino | Qué es | Duración | Precio publicado | Licencia | Notas |
| --- | --- | --- | --- | --- | --- | --- |
| Escenario Sur Viajes y Turismo | Bariloche, río Limay, privado | Bote de 3,30 m, spinning, trolling y mosca; box lunch y bebidas sin alcohol | 9 h | € 511 por grupo de hasta 2 | **No queda claro**: figura entre lo incluido, pero "Qué llevar" pide el permiso | Español, portugués, inglés; recogida incluida |
| Patagonia Trout & Fly | Bariloche, lago Moreno, privado | Lancha, mosca o spinning, captura y suelta; muelle de Bahía López | 4 h | € 506 por grupo de hasta 2 | La pueden gestionar | Almuerzo US$ 70 aparte; transporte no incluido; inglés, portugués, español |
| SALUM Tour & Travel | El Calafate, grupo reducido (máximo 3) | Pesca vadeando, ríos (Rico, Santa Cruz, Pelque) o lagos (Roca, Argentino); mosca o spinning; temporada de noviembre a abril | 5 a 9 h | € 264 por persona (TripAdvisor: US$ 340 por adulto) | Incluida (permiso, seguro, equipo, recogida) | Captura y suelta obligatoria; español, inglés |
| Ezequiel Navarro | Ushuaia, un día completo | Ríos, lagunas y lagos; truchas de 1 a 4 kg | 12 h | € 851 por persona | Incluida | Recogida, guía, equipo, refrigerios; español, inglés |
| Savage Wine | Mendoza, río Mendoza, privado | Mosca, truchas arcoíris y marrones, almuerzo y vino | 6 h | € 269 por persona | Incluida (la gestiona el proveedor) | Producto de enoturismo; español, inglés, portugués |
| Big Bite Patagonia | Lago Puelo (Chubut), en embarcación | Paseo y pesca familiar, sin experiencia; opciones de 1, 2, 4 y 8 horas | 1 a 8 h | € 175 por grupo de hasta 5 | **No incluida**; tampoco almuerzo ni traslado | Sin pesca con mosca incluida; español |

Fuera del banco: "Ushuaia: excursión privada a Puerto Almanza" (€ 403 por grupo de 4) es un paseo turístico, no de pesca.

## TripAdvisor (pegado por el dueño, 11/11/2026)

| Operador | Destino | Precio publicado | Notas |
| --- | --- | --- | --- |
| Pioneros Fly Fishing | Mendoza, Estancia La Carrera, 10 h | US$ 780 por adulto | Equipo incluido, no dice licencia; otro listado "pesca en río andino privado con asado": desde US$ 540 por adulto |

Los demás listados de TripAdvisor (Bariloche y El Calafate) están en `docs/paquetes-bariloche-fichas-2026-11-11.md`.

## Qué falta para usarlos

1. **Euro en la tarjeta de paquete:** hoy solo admite pesos, dólares y reales (`desde_moneda` en `inc/campos.php`).
2. **Guía de destino propia** para El Calafate, Ushuaia, Lago Puelo y Mendoza (los paquetes cuelgan de una guía de destino). Hoy solo Bariloche la tiene.
3. **Validación del operador** en todos los casos.
