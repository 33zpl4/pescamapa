# Primera medición con Gemini + Google Search (6/11/2026)

Corrida del workflow `medir-ia.yml` (carpeta `2026-11-06-0756` de la rama `medicion-ia`). **Solo Gemini** (`gemini-3.5-flash` con Google Search): faltó el secreto `OPENAI_API_KEY`, ChatGPT salió "omitido". 21 preguntas, ninguna con error.

## Resultado
- **Pescamapa figura entre las fuentes consultadas en 2 de 21 respuestas:** la 13 (licencia de pesca en la Patagonia 2026/27) y la 14 (temporada de trucha de mar en Tierra del Fuego y semana en un lodge de Río Grande). No aparece en el texto de la respuesta, solo en las fuentes.
- Las páginas de precios publicadas el 5/11 todavía no aparecen (un día de vida, sin indexar).

## Con quién competimos, por tema (dominios más citados)
- **Reglamento y licencias (preguntas 2, 10, 13, 18, 20):** `reglamentodepesca.org.ar`, `aicacyp.ar`, `permisosdepesca.com.ar`, `chubut.gob.ar`, `argentina.gob.ar`, `recnaturalesctes.gob.ar`. Gana quien publica **el valor del permiso con la norma y la fecha**.
- **Precios de lodges y viajes (9, 11, 12, 15):** `nervouswaters.com`, `paranasunrise.com`, `yellowdogflyfishing.com`, `fishingbooker.com`, `patagonia-trashumante.com`, `aardvarkmcleod.com`, `lapilaricalodge.com.ar`.
- **Guía/día en Argentina (3, 8, 19):** `hooked.com.ar`, `pesqa.ar`, `fishingbooker.com`, `outfitterspatagonia.com`, `southernriverspatagonia.com`.
- **Portugués (7, 8):** `fishingbraziladventures.com.br`, `pousadapuertoyacarey.com.br`, `pescamaisargentina.com.br`: el público brasileño tiene poca oferta en su idioma.

## Qué aprendimos (y qué se hizo)
1. **Gemini con búsqueda contesta con valores concretos de la licencia patagónica 2026/27.** Verificados contra el texto del reglamento oficial (actualizado el 26/10/2026): extranjeros $ 50.000 / $ 140.000 / $ 280.000; residentes $ 12.500 / $ 25.000 / $ 50.000; trolling adicional; sin cargo mayores de 65, menores de 12, discapacidad. → **Se creó la página "Licencia de pesca en la Patagonia 2026/27" (es/en/pt)** y se enlazó desde las de precios.
2. **Corrección de moneda (no era un dato falso):** la nota de Bariloche traía la licencia en dólares (US$ 30/90/180, de un operador). Son los valores oficiales en pesos ($ 50.000 / $ 140.000 / $ 280.000) convertidos a dólares con un tipo de cambio de ~1.550–1.670, así que eran coherentes. Se reemplazó por la cifra oficial en pesos, que es la fuente primaria, en es/en/pt. (Primero lo describí como un error nuestro: era una conversión, no un error.)
3. Las respuestas de IA a veces dan valores sin fuente verificable (ChatGPT dio un precio de Pescar que no existe); los rangos de Gemini (p. ej. US$ 550–700 el día en Bariloche) no se pueden descartar: son compatibles con tarifas "desde" más altas. Publicar el dato verificado, con fecha y como piso o rango según corresponda, es lo que se puede citar.
4. Los redireccionadores de grounding de Google (`vertexaisearch.cloud.google.com/grounding-api-redirect/…`) **no se pueden leer** (robots.txt) ni desde el sandbox: para ver la fuente real hay que pedir la URL a ChatGPT o buscarla por dominio.

## Huecos que muestra la medición (candidatos a guía, por orden)
- **Licencia/permiso en Corrientes**: la pregunta 2 y la 10 la responden `argentina.gob.ar`, `recnaturalesctes.gob.ar` y notas de prensa; nosotros no tenemos el valor (no está publicado en el sitio del Ministerio).
- **Reglamento y temporada de Chubut** (preguntas 18 y 20): la fuente oficial `pesca.chubut.gob.ar` solo tiene la nota de la temporada 2025/26; lo vigente está en el Anexo Chubut de `reglamentodepesca.org.ar`, que ya usamos en la guía de Esquel y Trevelin.
- **Guía de pesca con mosca en Bariloche con precios** (19): se citan agencias de EE.UU.; nuestra nota tiene 3 precios publicados y no pasa de ahí.
- **Pesca en portugués desde Brasil** (7, 8): casi todos los dominios son brasileños; tenemos el pilar del dorado en pt pero no el de licencia y costos para el brasileño.
- **Jurassic Lake** (15): lo citan los operadores de EE.UU.; tenemos la guía, sin precio.

## Próximos pasos
- Cargar `OPENAI_API_KEY` como secreto para medir ChatGPT desde `medir-ia.yml`.
- Repetir el lunes 9/11 y a los 14 y 30 días; comparar si las páginas de precios y la de licencia entran como fuente.
