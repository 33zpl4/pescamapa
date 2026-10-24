# Estado del proyecto — para retomar en otra sesión

Última actualización: 24/10/2026 (5.ª guía: San Martín y Junín de los Andes, en rama). Actualizá este archivo al cerrar cada sesión.

## Objetivo (del dueño)

**Ser útil para la gente que quiere pescar**, y que Google y las IA nos
refieran. No gastar tiempo en lo que no es prioridad. Las entrevistas a
gente local generan contenido propio; las consultas de pescadores llegan
todas a nosotros (centralizar datos y cobrar comisión más adelante).

## Qué está online

- **pescamapa.com = WordPress** en el servidor aaPanel de FansTemple, detrás
  de Cloudflare (ver `docs/wordpress.md`). Indexable, con sitemap
  `wp-sitemap.xml`. Español, `/pt/` y `/en/`.
- **Goya** (es/pt/en): reglamento, calendario y FAQs actualizados el
  20/10/2026 (`wordpress/scripts/goya-2026-10-20.php`). Sin fecha de
  "reglamento verificado" (nadie lo confirmó con la autoridad).
- **Panel para Cheyenne**: rol "Editor de Pescamapa", solo español, menú
  corto. Las traducciones las propagamos nosotros (columna "Traducciones").
- **Entrevistas, prestadores y consultas** (PR del 20/10/2026, ver
  `docs/wordpress.md` → "Entrevistas, prestadores y consultas").
- **Panel sin bloqueos** (21/10): ningún campo es obligatorio; el editor
  ve solo "Autor de la foto". `test-wp` entra como editora, cambia una foto
  y guarda en cada PR.
- **Rama `claude/confident-feynman-4oj6zh` (21/10/2026, sin mergear)**:
  definición y lema nuevos, `llms.txt` y `robots.txt` (`inc/maquinas.php`),
  reglas de marca (`docs/marca.md`), tipo **Notas** (`/notas/`),
  bloque **"Más para hacer"** en las guías (con marca de publicidad),
  registro de cuándo se concreta una consulta (base de las reseñas,
  `docs/resenas.md`). Para que Goya muestre el enlace a Bitácora de Viajes y
  las consultas lleguen a `info@pescamapa.com`, después del merge correr
  `goya-mas-2026-10-21` y `email-consultas-2026-10-21` con `wp-script.yml`.
- **Revisión mensual de fotos**: issue automático (`revisar-fotos.yml`); el
  primero es el #23.
- **Esquina**: **publicada el 20/10/2026** (2.ª guía, solo español),
  reglamento verificado por el equipo del dueño el 20/10/2026. Lo dudoso
  sigue como notas PENDIENTE (no se ven). Investigación:
  `docs/investigacion/esquina.md`.

## Al 24/10/2026 (leer primero)

- **4 guías publicadas**: Goya, Esquina, Paso de la Patria (Corrientes, es/pt/en) y **La Paz, Entre Ríos** (es/pt, con aranceles de la tabla oficial, fiesta y precio 2026). Todo mergeado en `main` y desplegado; `verify-live` en verde. Contacto de licencias de Corrientes (Corrientes Turismo) en todas las guías correntinas.
- **Informe semanal** (`informe-semanal.yml`): Search Console + vigilancia de fuentes, un issue por lunes. Ya muestra impresiones reales (el sitio rankea). `docs/vigilancia.md`.
- **Mapa del inicio** verificado (proyección) y con etiquetas separadas (`docs/wordpress.md`).
- Páginas de ejemplo de WordPress despublicadas (404).
- **Siguiente sesión**: sumar ciudades con la receta de `docs/nueva-ciudad.md`. La **zona de pique** (`/pique/`) tiene su MVP construido: ver `docs/pique.md` (falta probarlo en un teléfono real).
- Pendientes del dueño: captura del mapa del inicio y de La Paz en vivo; ronda 2 de Gemini para Entre Ríos (tallas, cupos, vedas); OK para limpiar URLs muertas de `vigilancia/fuentes.json` (Entre Ríos: `boletinoficial.entrerios.gov.ar`, `entrerios.gov.ar/tramites` eran supuestas); revisar en Search Console el canonical http/https del inicio.

- **5.ª guía: San Martín y Junín de los Andes (Neuquén, es/en), en rama, sin mergear ni correr** (24/10/2026). Script `san-martin-junin-2026-10-24` (`$aplicar = false`: primero simulacro con `wp-script.yml`, leer el log, después poner `true`, mergear y correr). Dossier y **ronda 1 de preguntas para Gemini** en `docs/investigacion/san-martin-junin.md`. El sandbox no pudo abrir ningún sitio oficial de Neuquén: la guía publica solo lo que dos medios coinciden (venta de permisos desde el 15/10/2026, temporada desde el 1 de noviembre, reglamento patagónico unificado) y dice "no lo podemos confirmar" sobre **valores del permiso y reglas por ambiente**. Sin prestadores. **Reglamento (Parte Primera) leído el 24/10/2026** (pegado por el dueño, `docs/investigacion/san-martin-junin.md` §8): es la edición 2025-2026; la guía publica sus valores de permiso y reglas generales con su año. **Anexo Neuquén (págs. 33-49) y la parte general del anexo de Parques Nacionales (págs. 59-64) ya cargados** (`docs/investigacion/san-martin-junin.md` §9); la guía lleva reglas por río (Chimehuín, Malleo, Collón Curá, Quilquihue, Lolog) con su año 2025-2026. Listado de Parques Nacionales (págs. 65-82) también cargado (Lácar, Huechulafquen, Paimún, Epulafquen, Malleo en el parque; `docs/investigacion/san-martin-junin.md` §10). Falta solo la edición 2026-2027, confirmar con la Intendencia del Lanín si cobra ingreso, y los prestadores. Ronda 1 de Gemini recibida y auditada (`docs/investigacion/san-martin-junin.md` §7): casi nada se pudo usar (sin URL abierta, fechas que se contradicen); la guía sumó el enlace a `reglamentodepesca.org.ar` y una línea genérica de didymo. **Falta ronda 2** (PDF del reglamento 2026-2027 abierto, Lanín, prestadores). Hasta correr el script, `verify-live` falla en esa guía (esperado).

## Decisiones del dueño (no relitigar)

| Fecha | Decisión |
| --- | --- |
| 20/10/2026 | Sin carteles "en construcción", sin noindex, sin bloquear publicación. Completitud = guía interna. |
| 20/10/2026 | Cheyenne edita solo en español; nosotros traducimos. Plantillas fijas; si necesita algo más, lo pide. |
| 20/10/2026 | Todas las consultas llegan a Pescamapa; el contacto de prestadores es interno. |
| 20/10/2026 | "Quotes" = citas de entrevistas (contenido y SEO), no cotizaciones de precio. |
| 20/10/2026 | Prioridad de destinos por oportunidad SEO: `docs/seo-oportunidades.md` y wireframes en `docs/seo-guia-wireframes.md` (original: `docs/fuentes/guia-desarrollo-wireframes-seo.docx`). |
| 20/10/2026 | Idioma del navegador: sugerir, nunca redirigir. |
| 21/10/2026 | Ningún campo del panel es obligatorio: guardar nunca se bloquea. Para una foto alcanza con el autor; nada de trámite de licencias "estilo NYT". Revisión mensual de fotos por issue (`revisar-fotos.yml`). |
| 20/10/2026 | Las guías se listan en orden de publicación (Goya 1.ª, Esquina 2.ª…), no alfabético (`pm_destinos_lista`). |
| 20/10/2026 | Email: Cloudflare Email Routing para `info@pescamapa.com` (en pausa: el routing quedó en "Syncing"; falta destino y regla). Después: `wp option update pm_email_consultas info@pescamapa.com`. |
| 20/10/2026 | La fecha "Reglamento verificado" es **opcional**: se muestra si está, nunca se pide ni bloquea. El protocolo de abajo es una buena práctica, no un requisito para publicar. |
| 20/10/2026 | Goya está en el Paraná medio (km ~960–990): no lleva vedas extraordinarias del Alto Paraná. |
| 20/10/2026 | **Vedas: mínimo esfuerzo.** Poco tráfico y mucho mantenimiento. Cada guía lleva un párrafo genérico (vedas por desove y extraordinarias por tramo, cambian cada año) y el enlace a la autoridad. Sin fechas ni kilómetros que haya que perseguir. |
| 21/10/2026 | **Definición**: "Pescamapa es la guía para pescar en toda la Argentina: dónde ir, cuándo, qué dice el reglamento y con qué guía local salir, con datos al día y la voz de quienes pescan ahí." Lema: "Dónde, cuándo y con quién pescar en Argentina." |
| 21/10/2026 | Todo lo que se publica respeta `docs/marca.md` (colores, tipografías, tamaños, componentes) y se verifica con `revisar-marca.mjs`. |
| 21/10/2026 | "Más para hacer" es un bloque repetible por ciudad: hoy el enlace a Bitácora de Viajes (Goya); a futuro, espacio que se puede vender (marcado "Publicidad", `rel="sponsored"`). |
| 21/10/2026 | Reseñas de prestadores: se prepara la infraestructura (registro de viajes concretados), no se lanzan todavía. Plan en `docs/resenas.md`. |
| 21/10/2026 | Contenido propio: por ahora se alimenta a mano (enlace + hechos + resúmenes en el chat) y Claude arma citas, notas y entrevistas; la API viene después. `docs/contenido.md`. |
| 22/10/2026 | Vigilancia semanal automática (Search Console + fuentes que pueden vencer) en un issue por semana, sin modelos. Se dice en público solo cuando lleve varias semanas corriendo. `docs/vigilancia.md`. |
| 22/10/2026 | **Lo que no se puede verificar online no se persigue.** Si un dato (p. ej. el arancel de la licencia para turista argentino) no está publicado o actualizado en una web oficial, se dice que no lo podemos confirmar y se indica qué hacer (preguntarle a la guía local o consultar a Recursos Naturales). Que las webs públicas estén desactualizadas no es responsabilidad nuestra; no se cavan agujeros. Los aranceles de la licencia no se publican sin fuente oficial vigente. |
| 22/10/2026 | Frase para las guías cuando un dato no está online: "si no lo ves online, preguntale a tu guía". |
| 22/10/2026 | Las ciudades tienen varias fiestas: se destaca una sin sobre-indexar. Un precio pasado se publica con su año. |
| 23/10/2026 | **Zona de pique**: mapa comunitario a ~500 m–1 km (no 10–20 m), alimentado por muchos pescadores y por prestadores, puede lanzarse vacío como invitación. `docs/pique.md`. |
| 21/10/2026 | `info@pescamapa.com` ya enruta (Cloudflare Email Routing). |
| 21/10/2026 | **Prestadores: listar primero, verificar después.** Se publican con información pública, marcados "sin verificar" y con enlace para corregir o pedir la baja (info@pescamapa.com). Reemplaza "solo con consentimiento". `docs/prestadores.md`. |
| 21/10/2026 | **Regla de oro**: cada página responde con un dato concreto, fechado y con fuente una pregunta real, arriba y en texto; muchos lectores serán LLM. `docs/estrategia-contenido.md` (prueba de cinco preguntas, preguntas de control mensuales en `docs/preguntas-llm.md`). |
| 21/10/2026 | **Modelo**: la oferta viaja con la respuesta (condición especial por Pescamapa, publicada en la tarjeta y en llms.txt), somos el camino fácil (idioma, precio, disponibilidad) y cobramos comisión del lado del prestador. Primero Esquina y Goya. `docs/prestadores.md`, `docs/mensajes-prestadores.md`. |
| antes | Fotos solo de Commons con licencia libre o cedidas con crédito; nunca de Google. Prestadores solo con consentimiento y precios fechados. |

## Pendientes, en orden

0. **Foto de Goya**: la socia subió "Experiencia Isoro (1)" (vertical) pero
   el cambio no llegó a guardarse por el bloqueo de validación, ya
   corregido. Que lo reintente (ver `docs/guia-editor.md`).
1. **Reemplazar los datos de ejemplo de los prestadores** (wp-admin →
   Prestadores, columna "En la web" = 🧪). Por cada uno: datos reales,
   permiso para aparecer, destildar "Datos de ejemplo". Sobre todo **lancha,
   capacidad y motor**, que los guías señalaron como clave.
2. **Traducir Esquina** a pt/en (hoy solo español).
3. **Primeras entrevistas**: Agus y su papá (Goya). Guía de preguntas en
   `docs/entrevistas.md`. Hace falta su permiso para nombre, foto y frases.
4. **Email de consultas**: el routing de `info@pescamapa.com` está listo;
   falta fijar el destinatario (script `email-consultas-2026-10-21`) y
   confirmar que el servidor envía correo (probar el formulario). Si no llega,
   igual quedan en wp-admin → Consultas.
5. **Próximas guías** (matriz SEO; receta en `docs/nueva-ciudad.md`; hechas Paso de la Patria, La Paz y San Martín/Junín en rama): completar
   San Martín/Junín con la ronda 1 de Gemini (permiso, reglas por ambiente, Lanín) → Río Grande → lagunas
   (Junín/Laguna de Gómez, Chascomús, Lobos).
6. **Eventos con URL estable** (`/eventos/fiesta-nacional-del-surubi/`,
   Fiesta del Pacú, Fiesta del Dorado): todavía no existe el tipo.
7. **Fichas de especie faltantes**: pacú, patí, boga, tararira, trucha,
   pejerrey (hoy solo existen dorado y surubí).

## Protocolo de verificación (opcional, buena práctica)

Los resúmenes de buscadores repiten fórmulas ("se pesca todo el año") y no
dicen si una disposición sigue vigente ni si un guía sigue activo. Antes de
publicar una guía armada desde el escritorio:

1. **Reglamento específico**: leer completa la página de Recursos Naturales
   (u autoridad local). Para Esquina: si hay veda total o parcial de artes en
   la confluencia Corriente–Paraná y si el pacú en arroyos interiores tiene
   veda temporal.
2. **Directorio comercial real**: no cargar guías de listados de turismo
   viejos. Contactar al menos **2 guías o complejos** locales: precio de la
   jornada hoy, si incluye combustible, embarcación habilitada por
   Prefectura, y **consentimiento expreso** para figurar.
3. **Bitácora**: anotar fecha y responsable de la verificación (en la guía:
   fecha "Reglamento verificado el…"; acá: quién y qué fuente).

Bitácora de verificaciones:

| Fecha | Guía | Quién | Qué se verificó |
| --- | --- | --- | --- |
| 20/10/2026 | Esquina | Equipo del dueño | Reglamento: licencia y categorías, río Corriente solo pesca deportiva, pacú 45 cm, régimen de vedas. |

## Vedas extraordinarias 2026 (para las guías de Paso de la Patria e Itatí)

Solo como contexto (por la decisión de "vedas: mínimo esfuerzo", no se
publican fechas ni km). Según el dueño (20/10/2026):
- Mayo 2026: km 1232–1238 (Confluencia / "Los Cables"), por concentración de
  surubíes (Disposición 618 de Recursos Naturales).
- Septiembre 2026: ampliada a km 1180–1239 (riacho INTA → Confluencia,
  jurisdicción de Paso de la Patria), pesca deportiva, comercial y de
  subsistencia, por migración y desove de dorados y sábalos.
- El km 1109–1150 fue una veda extraordinaria de Chaco (zona Empedrado /
  Derqui). La Disposición 1054 levantó ambos tramos a fines de septiembre de 2026.
- Históricamente hubo cortes de emergencia en Itá Ibaté y Yahapé (km ~1100–1160)
  por bajantes. Nada de esto afecta a Goya (km ~970–990) ni a Esquina (km 853).

## Otro proyecto del mismo dueño (no mezclar)

La fábrica de sitios (`33zpl4/sites`, Hostinger) tiene su propio
`docs/estado.md`. Ahí está, por ejemplo, la mudanza de Catalina Porras a
catalinaporras.com. No usar ese hosting para Pescamapa.

## Mapa de workflows (`.github/workflows/`)

| Workflow | Cuándo | Qué hace |
| --- | --- | --- |
| `test-wp.yml` | Cada PR que toca `wordpress/**` | WordPress descartable: páginas, consultas, editora que cambia una foto. **Mergear solo en verde** |
| `deploy-wp.yml` | Push a `main` con `wordpress/**` | rsync del plugin y el tema, ajustes, verificación |
| `wp-script.yml` | A mano (`script=`) | Corre `wordpress/scripts/<script>.php` en el servidor |
| `verify-live.yml` | Semanal y a mano | Revisa lo servido: páginas, hreflang, sin PENDIENTE, formularios, redirección de www |
| `capturas.yml` | A mano | Capturas de pantalla a la rama `capturas` |
| `revisar-fotos.yml` | Mensual y a mano | Issue "Revisión de fotos" |
| `probe-wp.yml` | A mano | Diagnóstico de solo lectura: versiones, log, subida de fotos, validación de formularios |
| `fotos.yml`, `vhost-alias.yml` | A mano | Candidatas de Commons; alias de vhost (histórico) |

## Cómo se trabaja

- Rama `claude/wordpress-studio-agentic-9i5h7t` → PR → **`test-wp.yml` en
  verde** → merge a `main` (deploy automático, `deploy-wp.yml`) →
  `verify-live.yml`.
- El sandbox no llega al servidor ni a wordpress.org: todo se prueba y se
  verifica desde GitHub Actions. Nunca afirmar que algo anda sin un run en
  verde o una captura (`capturas.yml`).
- Cambios de contenido en nombre del dueño: scripts idempotentes en
  `wordpress/scripts/` + `wp-script.yml`. Nunca pisar ediciones de wp-admin
  ni empujar la base de datos.
