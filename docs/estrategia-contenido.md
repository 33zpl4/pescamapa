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

## Corolario: simple primero (dueño, 21/10/2026)

Casi todo el tráfico será de LLM, y la gente rara vez navega el sitio. Entonces
**solo se junta y se construye lo que un LLM puede citar**. Cada guía es:

1. **Cuadro de datos clave** arriba: números, rangos, nombres, fecha y fuente
   (tallas y cupos, licencia, cómo llegar, eventos, altura del río).
2. **FAQ visible y en JSON-LD**: unas 10 preguntas reales, cada una con su
   respuesta corta y su fuente. La investigación de un destino son esas 10
   preguntas, no 30.
3. **Prestadores** con nombre, qué ofrecen, lancha/motor/capacidad si son
   públicos y enlace. Sin precio: "a consultar".
4. **Voces con nombre** (entrevistas y citas) y **portugués** donde hay vacío.

No se invierte en: infraestructura que nadie va a ver (reseñas más allá de lo ya
registrado, interruptores de idioma, ajustes visuales finos) ni en verificar a
mano cada dato menor. **Se verifica a mano solo lo que, si está mal, daña a
alguien: reglamento, vedas y licencia.** Lo demás se publica con fuente y
"consultado el…" y se corrige cuando alguien avisa.

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

## Qué va en una ficha de especie y qué en una guía (24/10/2026)

La ficha de **especie** (`/especies/dorado/`) habla de la especie: nombre científico, récords con su fuente, tamaños de referencia (publicación científica o IGFA), reglas generales y enlaces a las guías. **Lo local** (tamaños que se pescan en un lugar, temporada, vedas del tramo, prestadores) va en la **guía del destino**. No se mezclan: una cifra de Goya no explica la especie, y una cifra de la especie no describe Goya. Fuente de una ficha de especie: IGFA, FishBase, normas; un operador de viajes solo con su nombre delante.

## Notas largas: parte que no caduca + caja del año (owner, 26/10/2026)

Una nota se escribe para durar. Lo que cambia con los años (vedas vigentes, fechas de torneos, normas nuevas) **no va mezclado en el texto**: va en una caja aparte, fechada ("al 26/10/2026"), que se actualiza una vez por año. El cuerpo habla en general ("puede haber vedas en ciertos momentos") y manda al guía local, que siempre está al tanto: **no asustamos ni dejamos dudas, orientamos**. Qué no hacemos: publicar datos que no podemos mantener al día. Las vedas no se copian con fecha en el cuerpo; solo la caja del año, con fuente y fecha, y sin un calendario que haya que sostener.

### Títulos, fechas y fotos de las notas (dueño, 26/10/2026)

- **Cada título dice lo propio de la nota**, no una plantilla con una palabra cambiada ("Cuándo pescar el dorado en el Paraná: dos ventanas y por qué las fuentes no coinciden" / "…el surubí: no tiene una temporada, se mueve con el agua"). El slug no se toca (las URLs no cambian). SEO title ≤ 60.
- **La fecha que se muestra** es el campo "Fecha de publicación"; "Actualizado" queda siempre con la fecha real. Las notas se publican espaciadas: lo ideal es una por semana, y así el historial es real.
- **Toda nota lleva foto de portada** (4:3, recortada) con el nombre de quien la sacó; Unsplash no exige etiqueta, se pone solo el nombre. Las fotos viven en `wordpress/fotos/` y se cargan con un script (`wp-script.yml` las copia al servidor; ejemplo: `notas-fotos-titulos-2026-10-26`).
