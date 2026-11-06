# Qué sirve de dos análisis de Ahrefs (6/11/2026) y qué no

Fuentes: "Los errores de SEO técnico que Google Travel se puede permitir y tu OTA no" (Natzir Turrado, 30/10/2026, España, hoteles y vuelos) y "AI Overviews en Latam" (Ana Fernandez, 23/10/2026, 2.998 búsquedas populares de MX/CO/AR/CL/PE). Pegados por el dueño; son datos de otros mercados y de búsquedas muy populares, no de nuestro nicho.

## Lo que se trasladó y qué se hizo
| Hallazgo | Para nosotros | Resultado |
| --- | --- | --- |
| Google Travel redirige (302) a ChatGPT, Claude y Perplexity: una IA solo cita lo que puede leer | Verificar que ningún filtro nos haga lo mismo | `sonda-bots.yml` (6/11/2026): 9 user agents (OAI-SearchBot, ChatGPT-User, GPTBot, PerplexityBot, ClaudeBot, Claude-SearchBot, Bingbot, Googlebot, Chrome) reciben 200 y el mismo contenido en 6 URLs; `robots.txt` los permite por nombre. **Límite:** simula el nombre desde una IP de GitHub; no prueba que un bot real pase un filtro anti-bots del hosting/CDN (eso se ve en los registros del servidor). |
| Googlebot indexa solo los primeros 2 MiB del HTML | Que nada importante quede después | HTML de 10 a 150 KB (la portada es la más pesada); título en el byte 176, canonical ~1.100, JSON-LD ~2.100. |
| Soft 404 (cualquier URL devuelve 200) | Que lo inventado dé 404 | 10 URLs inventadas (notas, especies, provincias, guías, río, calendario, raíz, en/pt): todas 404. |
| En AI Mode los precios de terceros salen sin enlace | Medir si nos usan aunque no nos citen | `consultar-ia.mjs` guarda `huellas`: cifras que solo calculamos nosotros (totales por grupo, precio por día de pesca). `medir-ia` cuenta las respuestas con huellas. Línea base: 0 de 61 respuestas guardadas. |
| ChatGPT responde con su propio índice, no abriendo la página | Estar indexado donde ese índice mira | **Sin hacer.** IndexNow no está en el sitio. No hay evidencia en los artículos de que ayude a ChatGPT; es barato y queda como candidato. Pregunta para el dueño: ¿Bing Webmaster Tools está configurado? |
| AI Overview en ~95 % de las búsquedas "requisitos" y ~98 % de "comparar" en Argentina (68 % en "precio") | Las páginas de licencia y de comparación de precios compiten contra un resumen ya armado | Confirma el formato: dato oficial con fecha, comparación con criterios, cálculo por grupo, enlace a la norma. Pregunta de diseño: "después del resumen, ¿qué le falta saber a la persona?". |
| Con resumen de IA el CTR del primer resultado baja ~58 % (cifra general, no Latam) | El éxito de una página es que la citen, no solo que la visiten | Medir citas y huellas; Search Console para visitas. |

## Lo que no se traslada
- Casi todo el primer artículo es de Google Travel (migración de dominios, canonicals, DMA, Core Web Vitals): no aplica a un WordPress chico.
- Las cifras de AI Overviews son de búsquedas populares en español; las nuestras son de cola larga y en tres idiomas. No sabemos qué porcentaje de nuestras consultas muestra resumen de IA (haría falta Ahrefs: filtro "incluir objetivo en Vista general de IA").

## Pendiente
- Registros del servidor/CDN: ¿llegan los bots reales de OpenAI y Anthropic? (dueño o hosting).
- Decidir IndexNow y Bing Webmaster Tools.
