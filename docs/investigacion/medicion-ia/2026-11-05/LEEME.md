# Línea base de medición en IA, día 0 (5/11/2026)

Script: `scripts/consultar-ia.mjs` (ChatGPT `gpt-4.1` con búsqueda web; Gemini `gemini-3.5-flash` por el endpoint compatible, **sin búsqueda**). Preguntas: las 20 de `docs/preguntas-llm.md` más la ancla "dorado con amigos". Respuestas completas en `control/pN-<proveedor>.json` y `dorado-amigos-*.json`.

## Resultado
- **Citas a Pescamapa: 0 de 42 respuestas** (línea base esperada: las páginas de precios se publicaron hoy).
- Respuestas con al menos una URL: ChatGPT 15 de 20; Gemini 4 de 20 (Gemini cita lo que recuerda, sin buscar).
- Respuestas vacías o con error tras reintentos: p5-gemini, p12-gemini, p14-gemini, p15-gemini, p17-gemini.

## Dominios que sí se citan (para saber con quién competimos)
- **ChatGPT:** google.com (9), corrientes.com.ar (3), reglamentodepesca.org.ar (3), youtube.com (2), oneoutdoors.org (2), eltransitoibera.com (1), cardumi.com (1), viajacongus.com (1), pescaargentina.com (1), misiones.gob.ar (1), goyasurubi.com (1), festivalesargentina.com (1)
- **Gemini:** licencias.corrientes.gob.ar (1), pescadeportiva.gob.ar (1), reglamentodepesca.org.ar (1), pesca.chubut.gov.ar (1)

## Cómo leerlo
- Repetir a los 14 días (19/11/2026) y a los 30 (5/12/2026) con las mismas preguntas y comparar: ¿aparece pescamapa.com?, ¿qué dominios nuevos entran?, ¿cambia el precio que citan?
- Un dominio citado en muchas respuestas (p. ej. reglamentos o portales oficiales) marca qué tipo de fuente recomiendan los asistentes para reglamento; los lodges con escalera de precios marcan el formato para paquetes.
- Gemini sin búsqueda no mide posicionamiento: mide lo que ya "sabe". Para medir Gemini con Search hay que cargar la clave con `x-goog-api-key`.
