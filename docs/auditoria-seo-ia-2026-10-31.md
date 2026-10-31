# Auditoría SEO con la lente de las respuestas por IA (31/10/2026)

Pedido del dueño (31/10/2026): revisar que el sitio esté optimizado según la estrategia (`docs/estrategia-seo-2026-10-31.md`), sabiendo que **cada vez más gente recibe la respuesta directamente de una IA** (el resumen de Gemini en Google, ChatGPT, Perplexity) y no entra al sitio para la mayoría de las consultas. La tesis que ordena todo: *solo sobreviven los sitios construidos sabiendo que casi toda la información se va a servir por LLM, y que la visita, cuando ocurre, viene con una intención concreta.* Lo que sigue es qué significa eso para Pescamapa, qué ya cumplimos, qué cambié hoy y qué falta.

## 1. Cómo gana un sitio cuando la respuesta la da la IA

Tres cosas, en este orden:

1. **Ser la fuente que el modelo cita.** Un modelo cita páginas con un dato concreto, fechado, atribuido a una fuente primaria y fácil de extraer (una frase, una tabla, un bloque de preguntas y respuestas). No cita adjetivos ni textos largos sin fecha.
2. **Que la cita diga qué hacemos.** Cuando el modelo nombra a Pescamapa, el lector tiene que entender en una línea que acá se pide una cotización a un guía local, sin costo. Si solo somos "una fuente del permiso", no pasa nada después.
3. **Que la visita, cuando ocurre, encuentre el camino en un segundo.** El que llega desde una respuesta de IA ya leyó el dato: viene a hacer algo (comparar, pedir, reservar). La página tiene que ofrecer ese paso arriba, no al final.

Lo que no cambia: Google sigue indexando y los resúmenes de IA se alimentan de páginas indexadas. Las bases clásicas (título, descripción, canónica, hreflang, sitemap, datos estructurados, velocidad) siguen siendo la condición de entrada.

## 2. Qué ya cumplíamos (verificado en el código el 31/10/2026)

| Pieza | Estado | Dónde |
| --- | --- | --- |
| Títulos y descripciones con límites (≤60 / 70–160), canónica, Open Graph | ✓ en todas las vistas, con chequeo en cada script | `inc/seo.php` |
| hreflang es / pt-BR / en + x-default → es | ✓ | `inc/seo.php` |
| Sitemap de WordPress con guías, especies, notas, paquetes y vistas fijas | ✓ | `inc/seo.php`, `inc/sitemap-vistas.php` |
| JSON-LD por tipo de página: `TouristDestination` + `FAQPage` + `BreadcrumbList` (guías), `Article` con `citation` y `datePublished` (notas, especies), `ItemList` de `TouristTrip` con `Offer` fechado (paquetes), `WebSite` (inicio) | ✓ | `inc/seo.php`, `inc/notas.php` |
| `llms.txt` generado desde los mismos datos que las páginas (guías, especies, entrevistas, notas, paquetes) | ✓ | `inc/maquinas.php` |
| `robots.txt` sin bloqueos a rastreadores de IA (GPTBot, Google-Extended, ClaudeBot, PerplexityBot): decisión implícita de dejar entrar a todos | ✓ (no se bloquea nada) | `inc/maquinas.php` |
| "Respuesta corta" arriba en las notas; caja "Datos clave"; caja "Resumen" en las guías (especies, modalidades, cuándo, licencia, guías) | ✓ | vistas |
| Fecha visible de actualización y "fuente consultada el…" en cada dato | ✓ | guías y notas |
| Formulario de cotización al final de cada guía y de cada página de paquetes; botón "Pedir cotización" por paquete; país del que viaja | ✓ | `parciales/consulta.php` |
| Fichas de prestador con precio fechado, "qué incluye" y "información pública / operador con acuerdo" | ✓ | `parciales/prestador.php` |

Esto es más de lo que tiene la mayoría de los sitios del rubro. El problema no era la base técnica: era el **camino de la cita a la consulta**, y algunos huecos de datos estructurados.

## 3. Lo que faltaba y cambié hoy

| Hueco | Cambio | Por qué importa para la IA |
| --- | --- | --- |
| La guía de destino no decía en JSON-LD cuándo se actualizó ni quién la publica | `TouristDestination` lleva ahora `dateModified` (la fecha que muestra la página) y `publisher` Pescamapa | Los motores de respuesta prefieren el dato con "al día X"; sin fecha, un valor de permiso de 2026 compite con uno de 2023 |
| Las notas tenían el dato arriba y el único llamado a la acción al final (ir a la guía) | Bajo "Datos clave" de cada nota, una línea y el botón "Pedir cotización" al formulario, con destino y especie preseleccionados | El que llega desde una respuesta de IA viene con una pregunta ya resuelta: necesita el paso siguiente arriba |
| `llms.txt` describía el contenido, no el servicio | Sección "Cómo se contrata": qué hace Pescamapa (recibe la consulta y la pasa a un guía local, sin costo), qué son los precios publicados, qué significa "información pública" y los tres formularios por idioma | Es lo que un asistente puede repetirle a alguien que pregunta "¿cómo contrato?" |
| Huecos de idioma (lo que existía en un idioma y faltaba en otro) | Pilar del dorado en es/pt; guías de Bariloche, Jurassic Lake, Chascomús y San Martín–Junín en pt; paquetes de Bariloche en pt; 11 fichas con descripción en pt; 7 fichas con el español en su campo | Un modelo que responde en portugués cita páginas en portugués; con hreflang correcto, cada idioma es una entrada |

## 4. Lo que sigue faltando (en orden de valor)

1. **Medir si nos citan.** `docs/preguntas-llm.md` tiene 10 preguntas de control y un registro vacío. Correrlas una vez por mes en ChatGPT, Gemini (con el resumen de Google) y Perplexity, y anotar: ¿nos cita?, ¿qué dato usó?, ¿qué dijo que faltaba? Es la única medición directa; Search Console no separa el tráfico que viene de un resumen de IA. **Esto lo corre una persona.**
2. **Un hecho citable por pregunta.** Para cada pregunta de control, la página que la responde tiene que tener la respuesta en una frase con fecha y fuente (ya pasa en permisos, vedas y tallas). Falta en: precio de un guía de día en Esquina/Goya (sin tarifa fechada), equipo para dorado con mosca (sin fuente), fechas de la Fiesta del Surubí 2027 (sin publicar).
3. **Un índice de prestadores por región** como página propia ("Guías y lodges de la Patagonia"), para que los silos A y B tengan una URL que un modelo pueda citar como "lista de operadores con precios". Decisión de producto pendiente (ver estrategia, sección 4).
4. **Fotos en las fichas** (el campo existe): una ficha con foto se cita y se clica más; nunca de los sitios de los operadores.
5. **Ronda 3 de Keyword Planner** por país e idioma (`docs/investigacion/ronda-3-keyword-planner-2026-10-31.md`): sin ella no sabemos qué preguntas regionales en español y portugués faltan.
6. **El texto general del reglamento patagónico** (arts. 4, 13, 17, 22, 27, 28): hoy el fieltro y la rebaba se citan por ambiente; con el artículo, la frase es citable en toda la Patagonia.
7. **Velocidad y Core Web Vitals**: no medidos en esta auditoría (el sandbox no llega al sitio). Pedir un informe de PageSpeed del dueño para la guía de Bariloche en móvil.

## 5. Reglas que salen de esta auditoría (para toda pieza nueva)

- **Primero la respuesta, después el contexto.** Toda nota abre con "Respuesta corta"; toda guía tiene la caja "Resumen". Un dato nuevo va en una frase sola, con fecha y fuente, no dentro de un párrafo largo.
- **Toda página tiene un paso siguiente arriba**: en las guías, el enlace a "Prestadores de la zona" en la caja Resumen y el formulario al final; en las notas, el botón bajo "Datos clave"; en los paquetes, "Pedir cotización" en cada tarjeta.
- **Cada hecho lleva "al DD/MM/AAAA"** visible y en JSON-LD (`dateModified`, `validFrom` en precios).
- **Lo que no se puede afirmar se dice así**: "no lo pudimos confirmar; preguntale a…". Un modelo prefiere una página que marca sus límites a una que inventa.
- **Un idioma por audiencia, los tres por página**: todo lo que existe en un idioma se completa en los otros (inventario: `inventario-idiomas-2026-10-31`, de solo lectura).
- **`llms.txt` se genera solo**: no se edita a mano; si una página nueva no aparece, falta en el modelo de datos.

## 6. Qué no verifiqué

- No pude consultar a ninguna IA desde el sandbox: no sé hoy si nos citan. La medición de la sección 4.1 la corre una persona.
- No medí velocidad ni Core Web Vitals.
- No revisé los resultados de búsqueda por país (quién domina cada consulta): eso es parte de la ronda 3.
