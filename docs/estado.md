# Estado del proyecto — para retomar en otra sesión

Última actualización: 20/10/2026. Actualizá este archivo al cerrar cada sesión.

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
- **Esquina**: **borrador, no publicado** (decisión del dueño). El script
  `wordpress/scripts/esquina-2026-10-20.php` la crea como borrador en
  español con lo que la investigación pudo respaldar; lo dudoso quedó como
  notas PENDIENTE. Investigación (solo resúmenes de búsqueda, no páginas
  abiertas): `docs/investigacion/esquina.md`.

## Decisiones del dueño (no relitigar)

| Fecha | Decisión |
| --- | --- |
| 20/10/2026 | Sin carteles "en construcción", sin noindex, sin bloquear publicación. Completitud = guía interna. |
| 20/10/2026 | Cheyenne edita solo en español; nosotros traducimos. Plantillas fijas; si necesita algo más, lo pide. |
| 20/10/2026 | Todas las consultas llegan a Pescamapa; el contacto de prestadores es interno. |
| 20/10/2026 | "Quotes" = citas de entrevistas (contenido y SEO), no cotizaciones de precio. |
| 20/10/2026 | Prioridad de destinos por oportunidad SEO: `docs/seo-oportunidades.md` y wireframes en `docs/seo-guia-wireframes.md` (original: `docs/fuentes/guia-desarrollo-wireframes-seo.docx`). |
| 20/10/2026 | Idioma del navegador: sugerir, nunca redirigir. |
| 20/10/2026 | Una guía nueva armada con investigación de escritorio queda en **borrador** hasta pasar el protocolo de verificación (abajo). |
| 20/10/2026 | Goya está en el Paraná medio (km ~960–990): no lleva vedas extraordinarias del Alto Paraná. |
| 20/10/2026 | **Vedas: mínimo esfuerzo.** Poco tráfico y mucho mantenimiento. Cada guía lleva un párrafo genérico (vedas por desove y extraordinarias por tramo, cambian cada año) y el enlace a la autoridad. Sin fechas ni kilómetros que haya que perseguir. |
| antes | Fotos solo de Commons con licencia libre o cedidas con crédito; nunca de Google. Prestadores solo con consentimiento y precios fechados. |

## Pendientes, en orden

1. **Verificar Esquina** (borrador #38 en wp-admin) con el protocolo de abajo y recién ahí publicarla
   (y traducirla).
2. **Primeras entrevistas**: Agus y su papá (Goya). Guía de preguntas en
   `docs/entrevistas.md`. Hace falta su permiso para nombre, foto y frases.
3. **Cargar prestadores**: los 10 de Esquina que relevó la investigación
   (sección 10 de `docs/investigacion/esquina.md`) y los de Goya. Estado
   "Relevado" hasta hablar con ellos.
4. **Email de consultas**: confirmar que el servidor envía correo (probar el
   formulario) y fijar el destinatario: `wp option update pm_email_consultas
   <email>`. Si no llega, igual quedan en wp-admin → Consultas.
5. **Próximas guías** (matriz SEO): Paso de la Patria → La Paz → San Martín
   de los Andes / Junín de los Andes (mosca) → Río Grande → lagunas
   (Junín/Laguna de Gómez, Chascomús, Lobos).
6. **Eventos con URL estable** (`/eventos/fiesta-nacional-del-surubi/`,
   Fiesta del Pacú, Fiesta del Dorado): todavía no existe el tipo.
7. **Fichas de especie faltantes**: pacú, patí, boga, tararira, trucha,
   pejerrey (hoy solo existen dorado y surubí).

## Protocolo para pasar una guía a "verificada" (dueño, 20/10/2026)

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
| — | — | — | (todavía ninguna) |

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

Catalina Porras (catalinaporras.com): falta actualizar los secrets
`WP_PATH=/home/u851435760/domains/catalinaporras.com/public_html` y
`SITE_URL=https://catalinaporras.com`; el dominio y el email siguen en
Webnode (pidió el código EPP para transferir).

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
