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


## 5. Lo que respondió la investigación (22/10/2026, Gemini/ChatGPT)

- **No hay RSS ni API oficial** en los dominios del Estado correntino: la vigilancia por huella de texto es el camino.
- **Cadencias:** vedas extraordinarias, impredecibles (horas a días; ojo en aguas altas y bajas); veda de desove, anual (se define entre octubre y noviembre); aranceles de licencia, semestral o anual; tallas y cupos (Disposición 853/22), muy estables; torneos, anuales (Goya abril-mayo; Paso de la Patria marzo-abril y agosto; Esquina febrero-marzo).
- **Sin confirmar:** que `recursosnaturales.corrientes.gob.ar` esté activa (el informe dice que no responde; usamos `recnaturalesctes.gob.ar`); los selectores CSS que sugirió (no los usamos: `vigilar.mjs` compara el texto de la página entera, sin selectores); que las disposiciones menores de Recursos Naturales se publiquen en el cuerpo de los PDF diarios del Boletín.
- **Boletín Oficial:** su portada cambia a diario, por eso solo se vigila que responda. Las disposiciones nuevas se siguen por la página de etiquetas de decretos y, en las ventanas críticas (octubre-noviembre), con una consulta mensual a Gemini/ChatGPT.

## 6. Primer informe (22/10/2026): qué encontró

- 27 fuentes: 6 sin cambios, 16 registradas (línea base), 5 sin respuesta.
- **Boletín Oficial de Corrientes** (3 fuentes) manda la cadena de certificados incompleta (`UNABLE_TO_VERIFY_LEAF_SIGNATURE`): el informe las marca caídas aunque el sitio ande. No se desactiva la verificación TLS. Opciones, a decidir: agregar el certificado intermedio del Boletín al workflow (`NODE_EXTRA_CA_CERTS`), o dejar que se lea a mano.
- **`licenciasrecursosnaturales.gob.ar` no existe (ENOTFOUND)**: la URL del portal de licencias que teníamos es incorrecta o está dada de baja. Pendiente: preguntar a Gemini/ChatGPT cuál es el portal vigente (es dato para las FAQ de licencia).
- **`esquina.gob.ar` no existe**: probamos `esquinacorrientes.gob.ar` (sin confirmar).

- **Enlace oficial de Recursos Naturales (22/10/2026, dueño):** `https://recursosnaturales.corrientes.gob.ar/`, referenciado desde Turismo: Trámites y permisos (`corrientes.tur.ar/tramites-y-permisos/`, que sí abre y ya se vigila). Ambos están en la lista; el primero figuraba "caído" en la investigación y falta ver si responde desde GitHub. El portal `licenciasrecursosnaturales.gob.ar` sigue sin existir: la licencia se busca desde la página de Turismo.
