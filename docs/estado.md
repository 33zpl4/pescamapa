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
- **Esquina**: guía en español creada por script
  (`wordpress/scripts/esquina-2026-10-20.php`) con lo que la investigación
  pudo respaldar; lo dudoso quedó como notas PENDIENTE (no se ven en la web).
  Investigación completa: `docs/investigacion/esquina.md`.

## Decisiones del dueño (no relitigar)

| Fecha | Decisión |
| --- | --- |
| 20/10/2026 | Sin carteles "en construcción", sin noindex, sin bloquear publicación. Completitud = guía interna. |
| 20/10/2026 | Cheyenne edita solo en español; nosotros traducimos. Plantillas fijas; si necesita algo más, lo pide. |
| 20/10/2026 | Todas las consultas llegan a Pescamapa; el contacto de prestadores es interno. |
| 20/10/2026 | "Quotes" = citas de entrevistas (contenido y SEO), no cotizaciones de precio. |
| 20/10/2026 | Prioridad de destinos por oportunidad SEO: `docs/seo-oportunidades.md` y wireframes en `docs/seo-guia-wireframes.md` (original: `docs/fuentes/guia-desarrollo-wireframes-seo.docx`). |
| 20/10/2026 | Idioma del navegador: sugerir, nunca redirigir. |
| antes | Fotos solo de Commons con licencia libre o cedidas con crédito; nunca de Google. Prestadores solo con consentimiento y precios fechados. |

## Pendientes, en orden

1. **Correr `esquina-2026-10-20`** (Actions → Run a WordPress script) si
   todavía no se corrió, y revisar la guía en el sitio. Cargar foto si el
   script avisó que no había.
2. **Revisar la veda de Goya "km 1109–1150"**: la investigación de Esquina no
   la encontró (sí encontró km 1232–1238, Disp. 618/2026, y km 1180–1239 desde
   el 9/9/2026). Confirmar con Recursos Naturales antes de dejarla.
3. **Primeras entrevistas**: Agus y su papá (Goya). Guía de preguntas en
   `docs/entrevistas.md`. Hace falta su permiso para nombre, foto y frases.
4. **Cargar prestadores**: los 10 de Esquina que relevó la investigación
   (sección 10 de `docs/investigacion/esquina.md`) y los de Goya. Estado
   "Relevado" hasta hablar con ellos.
5. **Email de consultas**: confirmar que el servidor envía correo (probar el
   formulario) y fijar el destinatario: `wp option update pm_email_consultas
   <email>`. Si no llega, igual quedan en wp-admin → Consultas.
6. **Traducir Esquina** a pt/en después de la revisión del español.
7. **Próximas guías** (matriz SEO): Paso de la Patria → La Paz → San Martín
   de los Andes / Junín de los Andes (mosca) → Río Grande → lagunas
   (Junín/Laguna de Gómez, Chascomús, Lobos).
8. **Eventos con URL estable** (`/eventos/fiesta-nacional-del-surubi/`,
   Fiesta del Pacú, Fiesta del Dorado): todavía no existe el tipo.
9. **Fichas de especie faltantes**: pacú, patí, boga, tararira, trucha,
   pejerrey (hoy solo existen dorado y surubí).

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
