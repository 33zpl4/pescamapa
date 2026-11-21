# Contenido propio — cómo se alimenta (manual, por ahora)

Decisión del dueño (21/10/2026): al principio la máquina de contenido es
manual. Se comparte el material; Claude arma las citas, notas y entrevistas;
más adelante se automatiza con una API (el esquema de abajo ya lo prepara).

## Cómo se entrega material

Por cada fuente, mandar en el chat:

1. **Enlace** (video, nota, publicación) o el audio/transcripción de una charla.
2. **Quién y cuándo**: nombre, rol, lugar, fecha. Si habla una persona,
   **si aceptó que publiquemos nombre, foto y frases** (sin eso, el material
   sirve para entender el tema pero no se cita).
3. **Hechos y resúmenes** que el dueño ya sacó (o el análisis del video con
   Gemini). Cuanto más literal el dato, mejor; los tiempos del video
   (`mm:ss`) ayudan a verificar.
4. Qué querés que salga: citas para una guía, una nota, una entrevista.

## Qué hace Claude

- Separa **hechos** (cifras, técnicas, temporadas) de **opiniones** y **citas
  textuales**; marca como `PENDIENTE` lo que no tiene respaldo.
- Redacta en español rioplatense, con las reglas de `docs/marca.md`, y deja
  el borrador como script (`wordpress/scripts/…`) o como texto para cargar
  en wp-admin → Notas / Entrevistas.
- Nunca publica sin fuente, ubicaciones exactas, vedas con fechas o datos
  de reglamento que no tengan enlace a la autoridad.
- Registra el material en `contenido/registro.md` (qué se entregó, qué se
  usó y dónde salió), y la nota guarda el origen en su campo interno
  "Material de origen".

## Reglas del material de terceros

- Un video o artículo ajeno se **cita y se acredita**; no se copia su texto ni
  se reutilizan sus imágenes. Los hechos sueltos se pueden usar; la redacción
  es nuestra.
- Lo que cuenta un guía sobre su propio servicio (precio, lancha) va a su
  ficha de prestador, con fecha, no a una nota.
- Una cita se corrige solo de forma mínima (muletillas) y siempre con el
  permiso de quien habló.

## Esquema para la automatización futura

`contenido/registro.md` ya tiene las columnas que una API necesitaría:
`id`, `fecha`, `tipo` (video | audio | texto | charla), `url`, `persona`,
`permiso` (sí | no | pendiente), `destino`, `resumen`, `usado_en`. Cuando se
automatice, cada fila pasa a ser un registro y los hechos extraídos llevan
`fuente_id` + tiempo.

## Fotos (regla del dueño, 29/10/2026)

Solo se cargan fotos que el dueño aporta como disponibles o permitidas, **siempre con el crédito del fotógrafo** (`foto_autor` y `foto_origen`: propia, cedida o licencia). No se marca la licencia como pendiente ni se busca otra fuente cada vez; si una foto tiene condiciones especiales, el dueño lo avisa al pasarla. Las fotos viven en `wordpress/fotos/` (máx. 1600 px de ancho) y se cargan con un script de `wordpress/scripts/` (simulacro primero).

## Notas con datos propios (20/11/2026)

Una nota fechada con números calculados por nosotros (no una página que se actualiza sola) lleva: los números exactos de `wordpress/build/rio-historia.py` (copia en una carpeta de `wordpress/build/` para que la nota se pueda reproducir), infografías en 4:3 (la portada se recorta a 4:3) generadas desde esos archivos, la fecha de lectura y la fuente en la nota y en cada gráfico, el CSV para descargar, y la frase «Podés usar estos gráficos y números citando a Pescamapa con enlace a esta nota». Sin pronóstico ni efecto sobre la pesca. Primera: `nota-parana-goya-2026-11-20`.

## Piezas informativas (sin llamados comerciales) (20/11/2026)

Regla del dueño: hay notas hechas **solo para indexar y ganar enlaces**, no para vender. Se marcan en la fila en español con la casilla **«Pieza informativa (sin llamados a pedir opciones)»** (campo `nota_informativa`, vale para es/en/pt por `clave`, como `nota_destino`). Usarla en notas de datos propios (ver arriba) cuando el objetivo es que la citen; no en notas que deberían llevar al lector a pedir opciones.

**Qué oculta** (`vistas/nota.php`): el cuadro «¿Querés pescar esto? … Pedir opciones» (`.cta-nota`), la barra fija del celular «Pedir opciones» (`.barra-pedir`) y el bloque «Guías que pescan…» con precios (`.guias-especie`). Esta vista no trae formulario, «Gratis para vos» (`.cta-gratis`) ni banda de mitad de página; si alguna vez se agregan, deben respetar el mismo flag. **Qué queda:** el menú (con «Armá tu viaje»: navegación global), migas, datos clave, cuerpo con sus enlaces a la guía del destino y a la página del río, fuentes, FAQ, el botón final a la guía del destino y el pie.

Primera: `nota-parana-goya-2026-11-20`, marcada con `wordpress/scripts/nota-informativa-parana-goya-2026-11-20.php` (simulacro primero). Prueba en `test-wp.yml` (flag activo: sin marcadores en los tres idiomas; apagado: igual que antes).

### Actualizar una nota de datos ya publicada (21/11/2026)

Una nota con fecha en el título («el 20 de noviembre») envejece al día siguiente. Cuando el dato crece, **no se publica otra nota: se reemplaza la misma fila** (misma `clave`, mismos IDs) con un script de actualización, y el titular pasa a hablar del período (el mes), no del día. Pauta, de `nota-parana-goya-reemplazo-2026-11-21.php` (copiarlo):

- **Encuentra las filas por `clave` + idioma** y reemplaza título, subtítulo, datos clave, cuerpo, FAQ, fuentes, SEO y portada. No toca `nota_fecha` (fecha de primera publicación); sube `actualizado`. Mantiene `nota_tipo`, `nota_destino` y `nota_informativa`.
- **Qué se cuenta primero:** lo que le pasa a la gente del lugar, luego por qué es raro (contra el calendario, no solo contra el récord: «no es la mayor crecida»), luego qué se espera y de quién (solo fuentes oficiales, con fecha, enlace y cita literal). Aclaraciones una vez cada una: niveles de alerta/evacuación = referencias de la escala (no una orden), «desde 1903» supone cero de escala fijo, lecturas del año sin validar, altura de escala no es profundidad.
- **Gráficos nuevos con nombres nuevos** (el adjunto se reutiliza por `_pm_archivo_origen`: un archivo con el mismo nombre no se vuelve a subir). Los viejos quedan en la biblioteca sin uso y se sacan de `wordpress/fotos/`. Generador y entradas en `wordpress/build/` (para este caso, `rio-nota-noviembre-graficos.py` + `rio-nota-noviembre-png.mjs --chequeo`, que además avisa de textos fuera del lienzo o superpuestos y deja una copia a 390 px para mirar). Cada cifra del texto sale de un archivo y el generador se detiene si una difiere.
- **Cambiar los slugs solo con prueba del 301.** WordPress guarda `_wp_old_slug` y el enrutador de notas (`pm_nota`) fija `name` y `post_type`, así que el núcleo debería redirigir; el script no lo da por hecho: pide al propio servidor la URL vieja con `redirection=0` y exige 301 al permalink nuevo; si falla, devuelve los tres slugs viejos al instante (el contenido queda reemplazado). Antes de renombrar comprueba que el servidor puede pedirse a sí mismo las URL actuales (200). Nunca dejar una URL vieja en 404.
- **Simulacro → lectura del log → aplicar → `VERIFIED`** por idioma (contenido, FAQ leída con `pm_faqs_contenido`, adjuntos nuevos y ninguno viejo, fila es con destino/tipo/informativa, render con el titular nuevo y sin marcadores de CTA).
- Una **pieza informativa** que se cita («Para medios»: resumen de ~100 palabras, tres cifras con fuente y el contacto `PM_EMAIL_PUBLICO`) va al final de la nota.
