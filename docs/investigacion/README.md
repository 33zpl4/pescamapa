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

## Lo aprendido el 30/10/2026 (Patagonia)

- **Las páginas del reglamento las abre el dueño.** `reglamentodepesca.org.ar`, `pescacontinental.gob.ar`, los portales de Neuquén y de Santa Cruz y `olsenfamily.com.ar` devuelven 403 o "fetch failed" a los runners de GitHub (bloquean datacenters), incluso con navegador. El dueño abre la página en su navegador y **pega el texto**: es la fuente oficial. Si no es posible, un PDF subido; recién después, copias de terceros (AICACYP, clubes), siempre dichas como tales.
- **`investigar.yml` ahora lee PDF** (`pdftotext`). Un PDF escaneado no tiene texto: se renderiza a imagen y se lee página por página (así se leyó la Disposición 241/2026 de Santa Cruz).
- **Dos copias del mismo reglamento pueden no coincidir.** Se compararon palabra por palabra el Word de 16 páginas y la copia de un club: solo difirieron dos líneas (las exclusiones de Strobel y Barrancoso). Regla: lo que figura en una sola copia no se publica.
- **Un texto de IA con citas se audita línea por línea.** Una "hoja de ruta" trajo enlaces inventados (`/rio-negro/`, `/neuquen/`), artículos citados sin poder abrirlos y fichas de ríos que contradecían el anexo leído (Manso Medio "solo mosca"). Se usó solo lo que se pudo cotejar; el resto quedó "reportado" hasta leerlo.
- **Dominios por verificar.** Un dominio adivinado puede ser otro negocio: `limayriverlodge.com` es una agencia; el lodge es `limaylodge.com`.
- **Un dato de fuente secundaria se publica con la fuente y la fecha** ("según ANBariloche el 16/10/2026", "según el operador"); si dos fuentes difieren (permiso de zona preferencial: 10.000 en el anexo, 12.500 en el anuncio) se publica la de fecha conocida y se avisa de la diferencia.
- Mapa y dossiers de Patagonia: `docs/patagonia-fuentes-y-guias.md`, `docs/investigacion/lodges-lote1-dossiers-2026-10-30.md`, `docs/investigacion/patagonia-anexos-2026-27-fuentes-2026-10-30.md`, `docs/investigacion/fuentes/`, `docs/investigacion/jurassic-lake-r2-auditoria-2026-10-30.md`.

