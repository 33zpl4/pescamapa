# Cómo dirigimos la investigación (Claude como director, Gemini/ChatGPT como investigadores)

Gemini y ChatGPT buscan en la web mejor y más barato que nosotros desde el sandbox (que no llega a internet). Claude **no investiga: dirige**. El dueño es el mensajero.

## El circuito

1. **Claude escribe la pregunta exacta**, lista para pegar, una por tema (nunca "contame todo sobre X"). Dice qué fuente se espera (resolución, tabla, URL oficial) y qué hacer si no existe: *"si no lo encontrás, decí NO CONFIRMADO; no estimes"*.
2. **El dueño la pega** en Gemini o ChatGPT y devuelve la respuesta (texto, docx, PDF o captura). Si el dato está en una imagen o cartel, el dueño la pasa tal cual.
3. **Claude audita antes de usar nada** (ver abajo) y guarda el resultado en `docs/investigacion/<ciudad>.md`: qué se usa y con qué fuente, qué se descarta y por qué, qué queda pendiente.
4. **Pendientes → ronda 2**: Claude redacta la siguiente pregunta, más angosta, solo para lo que faltó.
5. **Lo que no se confirma online no se persigue** (decisión 22/10/2026): se publica "no lo podemos confirmar; preguntale a tu guía o a la autoridad" y se sigue.

## Para qué sirve cada uno

| Tipo de información | Dónde pedirla | Notas |
| --- | --- | --- |
| Descubrir fuentes: qué organismo publica X, qué URL, si hay RSS/API | Gemini o ChatGPT | Pedir siempre la URL abierta, no un resumen |
| Contexto de una ciudad (especies, accesos, fiestas, torneos, prestadores existentes) | Gemini o ChatGPT | Todo es candidato, nada es hecho hasta auditarlo |
| Aranceles, tallas, cupos, vedas | Gemini/ChatGPT **solo para ubicar la resolución**; el dato se lee en la norma | Revistas y prensa no son norma |
| Prestadores (contacto, precios, servicios) | No investigación: el dueño o el propio sitio del prestador | Solo se publica lo que se confirma en su sitio |
| Cualquier cosa con cartel, tabla o PDF escaneado | El dueño pasa la imagen; Claude la lee | Recortar y ampliar antes de leer precios |

## Qué falló y cómo auditamos (La Paz, 22/10/2026)

- Casi todos los enlaces venían como `google.com/search?q=…` o direcciones **adivinadas sin abrir**. Regla: un enlace que nadie abrió no es fuente.
- Aparecieron prestadores que parecían inventados (uno de Santa Fe en una ciudad de Entre Ríos), leyes con número sin fuente, y fechas de fiesta que chocaban con la fuente abierta. Regla: contrastar cada dato fuerte con una segunda fuente; si no hay, va como `PENDIENTE:` oculto.
- Tallas de una revista tomadas por norma; tallas de otro río (CARU, Uruguay) aplicadas a otro (Paraná). Regla: comprobar jurisdicción y año.
- Un agente aplicó las reglas de una provincia a otra. Regla: lo primero que Claude verifica es la provincia.

Checklist de auditoría por dato: ¿quién lo dice?, ¿la URL abre y lo dice?, ¿de qué año?, ¿qué jurisdicción?, ¿hay segunda fuente?, ¿si falla, qué decimos? (la frase aprobada: *"si no lo ves online, preguntale a tu guía"*).

## Cómo escribe Claude la pregunta

- Una pregunta, un dato, una jurisdicción: *"¿Cuál es el arancel 2026 de la licencia de pesca deportiva para no residentes en Entre Ríos (por día y anual)? Dame la resolución y su enlace."*
- Pide la fuente y la fecha de la fuente, no solo el valor.
- Pide el enlace a la norma o a la página oficial donde se tramita.
- Cierra con la salida de escape: "NO CONFIRMADO".
- Le dice al dueño en qué herramienta pegarla y qué adjuntar al volver.

Ver también: `docs/nueva-ciudad.md` (receta completa), `docs/vigilancia.md` (cómo se vigilan las fuentes después de publicar), `docs/investigacion/la-paz.md` (ejemplo de dossier auditado).
