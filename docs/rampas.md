# Rampas y bajadas de lancha: qué datos hay (27/10/2026)

**Hoy no tenemos un inventario de rampas públicas, y no hay una fuente abierta única que lo tenga.** Es el pendiente "rampas y acceso" (repetidor `accesos` en la guía; ver `docs/wordpress.md`).

## Qué se probó
- `datos.gob.ar` (portal de datos abiertos): la búsqueda no devolvió ningún conjunto de rampas ni bajadas de lancha en Argentina. No se abrió el portal desde el sandbox.
- **OpenStreetMap** (`leisure=slipway`), consulta de solo lectura desde GitHub Actions (`sonda-rio.yml`, 27/10/2026, cuadros de ~44 km): Goya 0, Esquina 1 (sin nombre), Paso de la Patria 1 ("Poco Hilo", con cobro), La Paz (Entre Ríos) 1 ("Bajada de lanchas"), Corrientes 2 (una: "Rampa de la Guardería Náutica Stella Maris", privada, con cobro, asfalto), Concordia 3 (sin nombre), San Martín de los Andes 1 (sin nombre). Chascomús, Tigre/Delta y Junín dieron error 502 del servicio público (Overpass): **sin medir**. La consulta de todo el país se colgó. Conclusión: cobertura muy baja, casi sin nombre, acceso ni costo: **no alcanza como inventario**. Licencia ODbL: atribución y compartir igual la base derivada.

## Quién lo tiene (a pedirle, ciudad por ciudad)
Municipios (turismo y deportes), clubes de pesca y náuticos, Prefectura (sobre habilitación de muelles y señalización, no listado de rampas), guías y prestadores (cada uno conoce sus bajadas), y notas de turismo con fecha. Ninguno publica un listado único.

## Cómo se haría
1. Una ronda de investigación **por ciudad** para Gemini/ChatGPT: "¿qué rampas o bajadas de lancha hay en X?" con administrador, costo, horario, **sitio propio abierto y fecha**; auditar con la checklist de `docs/investigacion/README.md`.
2. Cargar solo lo confirmado, **como área** (zona, nombre público, administrador, costo con fecha, estado con fecha, fuente y fecha de consulta): nada de coordenadas exactas de pesqueros ("áreas, no puntos de pesca").
3. Lo no confirmado: "no lo podemos confirmar; preguntale a tu guía o al municipio".
