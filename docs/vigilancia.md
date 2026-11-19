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

- **Certificado incompleto (22/10/2026, resuelto):** el Boletín Oficial y `recursosnaturales.corrientes.gob.ar` no mandan el certificado intermedio y el informe los daba por caídos aunque el sitio andaba. `vigilar.mjs` ahora hace lo que un navegador: lee en el certificado del servidor la dirección del intermedio ("CA Issuers"), lo descarga y reintenta **con la verificación activada** contra las raíces del sistema más ese intermedio. No se saltea nunca la verificación: si la cadena no cierra con una raíz de confianza, sigue figurando como caída. Probado en local con una cadena armada a propósito; falta ver el efecto en el próximo informe real.

- **Segundo informe (22/10/2026), con el arreglo del certificado:** el Boletín Oficial (portada, decretos y Disposición 853/22) ya responde y quedó en línea base; las páginas de ejemplo de WordPress salieron del sitemap (24 páginas, 22 indexadas). Se quitó `licenciasrecursosnaturales.gob.ar` (dominio inexistente, 3 semanas en rojo). `recursosnaturales.corrientes.gob.ar` responde 404 en la raíz: el dominio existe pero esa ruta no.

## 7. Limpieza de fuentes y recorte por fuente (19/11/2026)

El informe del 16/11 marcaba 7 fuentes caídas (tres de hace 7 a 9 semanas) y nueve «cambios», casi todos ruido (noticias y contadores de las páginas municipales). Cambios:

**Capacidad nueva de `vigilar.mjs`** (`scripts/vigilar-recorte.mjs`, prueba: `node scripts/vigilar-recorte.test.mjs`). Campos opcionales por fuente en `vigilancia/fuentes.json`, solo para `"vigilar": "texto"`:
- `desde` / `hasta`: se compara solo el texto desde la primera aparición de una frase ancla hasta la primera aparición de otra (sin distinguir mayúsculas). Si el ancla no está, no se recorta (el cambio de estructura de la página se ve como cambio de texto, no se esconde).
- `ignorar`: lista de expresiones regulares que se quitan antes de comparar (contadores, fechas de «hoy»).
- `contiene`: lista de palabras; se comparan solo las frases que contienen alguna. Es lo que corta el ruido de noticias en páginas que mezclan avisos con novedades.
- `conocida`: en una fuente que cae de forma ya sabida, el informe la pone en una línea de «Caídas ya conocidas (sin acción)» en vez de en «No responden». El estado sigue contando los fallos semana a semana.
- El texto guardado la semana pasada se vuelve a recortar con las reglas de hoy antes de comparar: agregar un recorte nunca figura como cambio de la página.

**Fuentes corregidas o cambiadas (cada una con su motivo):**
| Fuente | Cambio | Motivo |
|---|---|---|
| `goya-surubi` | URL `goyasurubi.com.ar` → `https://www.goyasurubi.com/index/` | el `.com.ar` no resolvía (EAI_AGAIN); el sitio oficial de la Fiesta es `goyasurubi.com` |
| `er-boletin` | URL `boletinoficial.entrerios.gov.ar` → `https://www.entrerios.gov.ar/boletin/` | el dominio viejo daba ENOTFOUND 7 semanas; el oficial que usamos es el nuevo. Sigue en `estado` |
| `er-tramites` | quitada | 404 (URL adivinada); su reemplazo ya estaba en la lista: `er-licencia-tramite` (portal de trámites, trámite 708), vigilado con texto |
| `recnat-oficial` | pasa a `estado` + `conocida` | 502 desde hace 9 semanas; no hay ruta vigente conocida (es el enlace que cita Turismo). No se borra: es oficial. El sitio que sí responde y se vigila con texto es `recnat-home` |
| `lapaz-municipio` | `conocida` | certificado autofirmado (`DEPTH_ZERO_SELF_SIGNED_CERT`); no se desactiva la verificación TLS. La información de La Paz se sigue por `lapaz-turismo` y `er-licencia-tramite` |
| `nqn-permisos` | `conocida` | la prensa de octubre de 2026 cita `cazaypesca.neuquen.gob.ar/permisos` como el portal vigente de compra; da timeout desde los runners de GitHub, así que no prueba que el enlace de la guía esté roto. Los valores se leen en `reglamento-patagonico` y `nqn-prensa-permisos` |

**Ruido recortado:** `pdlp-normativa`, `pdlp-vedas` y `pdlp-torneos` (misma portada municipal) con `contiene` distinto por tema (náutica y reglamento, vedas y dorado, torneos e inscripción); `turismo-pdlp` con `hasta: "Información reciente"` (la lista de novedades) y `acuerdo-regional` con `hasta: "Publicación anterior"` (enlaces a otras notas). `paso-internacional` con `ignorar` de «(última actualización …)» (el sello de fecha de Prefectura y del SMN cambiaba cada semana). Las frases clave son una elección razonable, no un recorte medido: si una página cambia de estructura y el informe deja de ver lo pertinente, se ajustan en el JSON.

**Fuentes nuevas** (hoy sostienen guías): `caru-veda-dorado` (aviso de la CARU de la veda 2026 del dorado, 1/9 al 31/12) y `concordia-pesca` (página municipal de pesca deportiva de Concordia). El sitio oficial de la Fiesta es la corrección de `goya-surubi`, no una fuente aparte.

**Costo del informe:** el paso de Search Console inspeccionaba 60 URL del sitemap una por una (la mayor parte de los ~9 minutos); ahora son seis a la vez (`scripts/informe-gsc.mjs`), mismo resultado. Sin verificar en vivo hasta el próximo informe (ver `docs/estado.md`).


**Resultado del informe del 19/11/2026** (run 44567979657 en la rama, issue «Informe semanal — 19/11/2026»; el anterior se cerró solo): 49 fuentes, **2 sin respuesta** (antes 7) más 3 caídas ya conocidas en una línea, **2 cambios** (antes 9) y 2 fuentes nuevas registradas. Los dos cambios que quedaron eran del propio ajuste: `goya-surubi` (cambió la URL, se compara con el texto viejo del `.com.ar`) y `paso-internacional` (sello de fecha, ya ignorado). Las dos caídas son `boletin-decretos` (TIMEOUT, primera semana) y el listado de Chubut (429, 2 semanas; el sitio solo abre bien desde un navegador).
