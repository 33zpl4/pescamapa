# Vigilancia semanal (Search Console + fuentes)

Cada lunes (`informe-semanal.yml`, también a pedido) se abre **un issue "Informe semanal — dd/mm/aaaa"** y se cierra el anterior. Sin modelos: no gasta tokens.

## 1. Search Console

`scripts/informe-gsc.mjs`: clics, impresiones y posición de la semana contra la anterior, páginas y búsquedas principales, y estado de indexación de hasta 60 URLs del sitemap. Mientras no estén los secretos, el informe lo dice y sigue.

**Pasos del dueño (una vez):**
1. search.google.com/search-console → agregar propiedad **Dominio** `pescamapa.com`; verificar con el registro TXT en Cloudflare (DNS).
2. Enviar `https://pescamapa.com/wp-sitemap.xml` en "Sitemaps".
3. console.cloud.google.com → proyecto nuevo → habilitar "Google Search Console API" → crear **cuenta de servicio** → clave JSON.
4. En Search Console → Configuración → Usuarios y permisos → agregar el email de la cuenta de servicio con permiso **Restringido**.
5. GitHub → Settings → Secrets → Actions: `GSC_SERVICE_ACCOUNT` (el JSON entero) y `GSC_SITE` (`sc-domain:pescamapa.com`).
6. Lanzar el workflow "Weekly report" a mano y leer el issue.

## 2. Vigilancia de fuentes

`vigilancia/fuentes.json` lista las páginas de las que dependen las guías (reglamento, licencia, municipio, turismo, prestadores). Cada una dice qué vigilar (`texto`, `pdf` o solo `estado`), qué guía afecta y qué hacer si cambia.

`scripts/vigilar.mjs` baja cada URL, normaliza el texto y compara su huella con la semana anterior. El informe marca ❌ lo que no responde, ⚠️ lo que cambió (con las frases nuevas y las que desaparecieron) y 🆕 lo registrado por primera vez. **No decide nada**: una persona (o Claude, si se lo pasan) mira el cambio y actualiza la guía.

El estado vive en la rama `vigilancia-estado` (no se mergea ni se despliega).

Límites: un sitio que cambia de diseño dispara falsos cambios; uno que carga con JavaScript puede verse vacío; un PDF escaneado solo se compara por bytes. Corregir la fuente en el JSON cuando pase.

## 3. Cómo sumar fuentes

Agregar un objeto a `fuentes.json` (`id`, `nombre`, `url`, `vigilar`, `afecta`, `accion`). Preguntarle a Gemini/ChatGPT por cada tema: *"¿En qué URL oficial se publica X, hay RSS o buscador por palabra clave, cada cuánto cambia y qué fragmento mínimo conviene vigilar?"*

## 4. Qué se puede decir en público

Solo lo cierto: "revisamos cada semana las fuentes oficiales de las que depende cada guía" **cuando el informe corra de verdad varias semanas seguidas**. Cada guía ya muestra su fecha de actualización; no prometer un plazo de reacción.
