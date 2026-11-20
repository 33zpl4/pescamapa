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
