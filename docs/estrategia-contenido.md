# Estrategia de contenido — ser útiles para las personas y para los LLM

Decisión del dueño (21/10/2026), igual que en Momuto: cada vez más gente
investiga con un LLM y le pasa el resultado a quien viaja. **Una parte
grande de nuestro tráfico serán LLM.** Todo lo que agregamos se decide con
esta regla.

## La regla de oro

> **Cada página responde, con un dato concreto, fechado y con fuente, una
> pregunta que alguien (o un LLM en su nombre) realmente hace, y lo dice
> arriba, en texto.**

Lo que cumple eso se cita. Lo que no (superlativos, "los mejores precios",
"aventuras inigualables") no sirve a una persona ni a un modelo.

## Prueba de cinco preguntas (antes de agregar cualquier cosa)

1. ¿Qué pregunta real responde? (cuándo ir, cuánto cuesta, qué licencia, qué
   equipo, con quién, hay veda…). Si no hay pregunta, no se agrega.
2. ¿Hay un **dato** (número, rango, nombre, fecha) y no una opinión?
3. ¿Tiene **fecha y fuente**? Sin fuente: "sin verificar" o "a consultar",
   dicho tal cual. Decir lo que no sabemos también es un dato útil.
4. ¿Está **arriba** y en texto (cuadro de datos, tabla, FAQ visible), no
   escondido en una imagen o un párrafo largo?
5. ¿Es algo que **solo nosotros** podemos decir (entrevista con nombre,
   precio consultado hoy, lo que preguntan los pescadores)? Lo genérico ya
   está en todos lados; lo propio es lo que se cita.

## Mecánica (cómo se cita en la práctica)

- Hechos con números y rangos, sin tibieza ("dorado: de septiembre a marzo",
  con la altura del río y la fuente), y matices cuando importan.
- Cuadro de "Datos clave" y tabla arriba; FAQ visible **y** en JSON-LD;
  citas con nombre, rol y fecha; fuentes enlazadas (`citation`).
- URLs estables; se actualiza en el lugar, con "actualizado el".
- `/llms.txt` se genera de los mismos datos (no se desactualiza). Los
  rastreadores de IA (GPTBot, ClaudeBot, PerplexityBot, Google-Extended) están
  permitidos: `robots.txt` no los bloquea y no debe hacerlo.
- Idiomas con intención: portugués para el pescador brasileño, inglés para el
  de afuera, escritos para ese lector (no traducidos a ciegas).
- Datos propios = foso: consultas agregadas (qué piden, cuándo, para cuántos),
  precios consultados con fecha, tiempos de respuesta de los prestadores,
  entrevistas. Se publican como tendencia o rango con ventana de medición
  ("consultas de oct–dic 2026"), nunca como recuento crudo de tráfico.

## Fuentes de terceros (agencias, lodges, otros sitios)

Se usan como **pista de que algo existe**, no como texto. Un sitio comercial
suele traer poco dato citable (ej. doradosconmosca.com.ar: sin precios, sin
lodges con nombre en la página, sin fechas). Lo que sacamos: que existe, qué
vende y dónde (con su enlace y la fecha en que lo vimos). Los hechos
(temporada, equipo, precio) se confirman con el sitio del lodge, una charla o
una fuente oficial. Nunca se copian sus frases ni sus fotos. Agencias que
venden paquetes de otros: se listan como "operador" y no reemplazan la ficha
del lodge, para que la consulta pase por nosotros.

## Cómo medimos (sin herramientas)

`docs/preguntas-llm.md`: 10 preguntas reales de pescadores (es/pt/en). Una
vez por mes se las hacemos a ChatGPT, Gemini y Perplexity y anotamos si nos
citan, qué dicen y qué dato falta. Lo que no cita define la próxima nota o
ficha. (Esto lo corre una persona en cada asistente; los resultados se pegan
en el chat.)
