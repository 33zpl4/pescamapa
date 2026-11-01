# Estado del proyecto — para retomar en otra sesión

Última actualización: 31/10/2026 (idiomas completados en es/pt/en, estrategia SEO v2, auditoría con lente de IA, fichas de guías de día en Bariloche). Actualizá este archivo al cerrar cada sesión.

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

## Al 30/10/2026 (leer primero)

- **Patagonia entra al sitio.** Guías nuevas o puestas al día el 30/10/2026: **Lago Strobel / Jurassic Lake** (Santa Cruz, es "Lago Strobel", en "Jurassic Lake"), **San Martín y Junín de los Andes** (Neuquén, contenido 2026/27 cargado en la guía del 24/10) y **Bariloche y el Nahuel Huapi** (Río Negro, #161 es / #162 en, en vivo y verificadas). Todo el detalle, las fuentes y los valores de permisos: **`docs/patagonia-fuentes-y-guias.md`**.
- **Estrategia de listados refinada** (dueño, 29-30/10): listar ya, con información pública y baja visible; dos niveles de ficha; formulario "concierge"; fotos solo cedidas con crédito; precios solo con fecha y fuente. Resumen en `docs/prestadores.md` → "Estrategia de listados (30/10/2026)".
- **Fuentes oficiales del reglamento: pegadas desde el navegador del dueño.** `reglamentodepesca.org.ar` y los portales de Neuquén y Santa Cruz bloquean los runners de GitHub. Lo que se pegó se lee como fuente oficial (anexos Neuquén y Río Negro, puntos generales); los listados de ambientes se leyeron en copias de AICACYP. Faltan el listado de ambientes de Río Negro y el texto general. `docs/investigacion/README.md` → "Lo aprendido el 30/10/2026".
- **Dossiers de lodges** (lote 1, ocho lodges): `docs/investigacion/lodges-lote1-dossiers-2026-10-30.md`. Regla: solo vale el dominio del lodge o una fuente regulatoria; una tarifa sin temporada es `NO CONFIRMADO`; las agencias no son fuente.
- **Inicio:** se saca la tarjeta "¿Qué querés pescar?" del mapa del hero (`vistas/inicio.php`); se unen las dos guías de San Martín/Junín (había una duplicada por un error mío: ver el recap de Patagonia) y el mapa muestra "Lago Strobel" en español y "Jurassic Lake" en inglés.
- **Error que no se repite:** antes de crear una guía, buscar si ya existe (clave y nombre en `wordpress/scripts/` y en la lista de guías vivas).
- **Paquetes de Bariloche (30/10/2026, dueño):** página "Paquetes de pesca en Bariloche" en vivo (`/paquetes-de-pesca/bariloche/`, `/en/fishing-packages/bariloche/`), paquete "Truchas del lago Nahuel Huapi (Bariloche): jornada en lancha con asado en la playa" (nombre que usa el mercado). **Con precio de referencia publicado por decisión del dueño:** desde US$ 400 para 1 pescador, jornada completa; 2 pescadores: US$ 600 en total (parte de los costos se reparte), fechado 30/10/2026 (lo ofrece un agente que organiza estos paquetes y con quien se trabaja si hay interesados). Antes salió a US$ 650 por 2 pescadores según una cotización de mercado del 5/11/2026 (competidora sin nombrar); de esa cotización viene el "incluye" publicado, todavía sin confirmar con el agente (guía, equipo, asado con bebidas, permisos, lancha y combustible). El "desde 450" de la agencia no se usa: unidad no confirmada. Las consultas entran por el formulario ("Pedir cotización") y se pasan a un operador; la propuesta final puede variar. Scripts `paquetes-bariloche`, `paquetes-titulo-bariloche`, `paquetes-precio-bariloche` (2026-10-30). Pendiente: que el agente confirme qué incluye (el "incluye" publicado viene de la cotización de mercado) y "Bahía López" como punto de encuentro.
- **Segundo paquete de Bariloche: Limay (30/10/2026, dueño):** "Pesca con mosca en el río Limay (Bariloche): descenso en bote a remo, jornada completa", en la misma página de paquetes. **Se lista al precio de la referencia de mercado por ahora** (dueño): desde US$ 550 para 1 pescador, 2 pescadores US$ 650 en total (cotizaciones del 19/11/2026, 07:00–16:00; competidora sin nombrar); se ajusta cuando haya un operador local. Reglas del tramo del anexo de Parques 2026/27 (bloque "Antes de viajar"). No se publican el peso de los peces, el largo del descenso ni el punto de encuentro (la agencia da dos lugares distintos). Script `paquetes-limay-bariloche` (2026-10-30).
- **Foto en la tarjeta de un paquete (30/10/2026):** los paquetes ahora admiten una foto opcional (campo "Foto" en la pestaña Paquetes de la guía; el crédito sale sobre la imagen como en las demás fotos). Primera: Limay, crédito "Claudio Antonelli" (cedida por el dueño; ortografía como la escribió, sin confirmar). Script `paquetes-foto-limay-2026-10-30`; va después del deploy del plugin.
- **Foto de Paso de la Patria reemplazada (31/10/2026, dueño):** un dorado saltando, crédito "Jim Klug" (cedida; ortografía como la escribió el dueño), optimizada de 324 a 171 KB (1000×949). Reemplaza la de Wikimedia (Playa Punta Mitre), que sigue en la biblioteca de medios. Script `paso-foto-2026-10-31`.
- **Foto de La Paz reemplazada (31/10/2026, dueño):** canoa y pescadores en la costa del Paraná al atardecer, crédito "Roque Mario Erazun" (cedida; ortografía como la escribió el dueño), recortada 2 px por lado (traía un borde claro) y optimizada de 213 KB (1600×990) a 89 KB (1200×741). La anterior (#8) sigue en la biblioteca de medios. Script `la-paz-foto-2026-10-31`.
- **Cuatro ideas de contenido en inglés guardadas (31/10/2026, dueño):** (1) pescar la Patagonia por tu cuenta, (2) golden dorado: delta vs Corrientes, (3) lodges de la Patagonia (qué compran USD 10.000 por semana), (4) río Limay superior/medio/boca. Se trabaja **la 1 primero**; el resto espera. Evaluación contra nuestras reglas y qué frena a cada una: `docs/ideas-contenido-seo-2026-10-31.md`. Directiva de investigación y dos auditorías externas (permisos y bioseguridad con base; presupuesto de 7 días y accesos sin confirmar): `docs/investigacion/diy-patagonia-directiva-y-auditorias-2026-10-31.md`.
- **Idea 1 publicada (31/10/2026): "Fly fishing Argentina on a budget: how to fish Patagonia without a lodge"** (`/en/notes/fly-fishing-argentina-on-a-budget/`, clave `patagonia-por-tu-cuenta`, solo en inglés por ahora; portada de Chad Shmukler). Contiene lo leído por nosotros: permisos y zona preferencial, reglas de equipo, agua pública y camino de sirga (arts. 235 y 1974 del CCyC leídos en argentina.gob.ar), **una sola cifra para el día con guía ("alrededor de USD 550 por barco", 1–2 pescadores; las páginas de cuatro operadores muestran 520–550 y quedan en las fuentes, sin tabla ni nombres en el texto, por decisión del dueño)** y la balsa de Villa Llanquín; el cierre enlaza al formulario y a la página de paquetes de Bariloche. **No** trae presupuesto total ni precios de auto, cabañas, combustible o supermercado (esperan la ronda de logística), ni el artículo 22 del reglamento (no se leyó). Scripts `nota-por-tu-cuenta-en`, `-ajuste` y `-ajuste2` (2026-10-31). **Paquete del Limay (desde USD 550 / 650): el dueño lo considera justo (incluye permiso, almuerzo y más); el "incluye" sigue sin confirmar con el agente.** **Versiones en español y portugués (pedido del dueño, hechas el mismo 31/10/2026)** con el script `nota-por-tu-cuenta-es-pt-2026-10-31`: la nota en español pasa a ser la fuente de la foto, el tipo, la fecha y la especie; la portuguesa enlaza las guías en español (no hay guías de Bariloche ni Junín en portugués) y lo dice en el enlace.
- **Estrategia SEO y comercial v2 (31/10/2026, noche):** `docs/estrategia-seo-2026-10-31.md` manda sobre `seo-oportunidades.md` y la lectura de la ronda 1. Tesis: dos productos (Patagonia, Litoral), dos mercados (internacional EN; regional ES/PT, que necesita su propia extracción), una capa comercial compartida; seis silos servidos con los tipos de página que ya existen (sin árbol de URLs nuevo); puntaje de 5 criterios para decidir qué publicar; correcciones de método (CPC ≠ comisión, índice de competencia ≠ dificultad SEO, no tirar <20, un país por export). **Los CSV de la ronda 2 no están en el repo: sus números quedan como reportados.** Backlog reescrito con puntaje: `docs/ideas-contenido-seo-2026-10-31.md` (P1 guías de día, P2 dorado Corrientes con precios, P3 costo del viaje, P4 directorio de lodges, P5 fichas por marca, P6 Limay, P7 pilar Patagonia, P8 delta, P9 Tierra del Fuego, P10 regional ES/PT, P11 hecho). Protocolo de la ronda 3 para el dueño: `docs/investigacion/ronda-3-keyword-planner-2026-10-31.md`. **Primera pieza de P1:** 4 fichas de guías de día en Bariloche (Trout Bariloche, Southern Rivers Patagonia, Patagonia in the Loop, Patagonia Drift), nivel 1 "información pública", tarifas fechadas 31/10/2026 de sus propios sitios, es + en; script `prestadores-bariloche-dia-2026-10-31`.
- **Idiomas completados (31/10/2026, regla del dueño: lo que exista en un idioma se completa en los otros):** pilar del dorado en es (#179 `/notas/pesca-del-dorado-en-la-argentina/`) y pt (#180); guía de Bariloche en pt (#181 `/pt/patagonia/rio-negro/pesca-em-bariloche/`) con sus paquetes en pt (`/pt/pacotes-de-pesca/bariloche/`, USD 400 / 550); descripción en pt de 11 fichas; 7 fichas con el español en su campo base (antes mostraban inglés en la página en español); guías en pt de Jurassic Lake (#182 `/pt/patagonia/santa-cruz/pesca-no-jurassic-lake/`), Chascomús (#183 `/pt/pampa/buenos-aires/pesca-em-chascomus/`) y San Martín–Junín (#184 `/pt/patagonia/neuquen/pesca-com-mosca-san-martin-junin-de-los-andes/`), en vivo y con `VERIFIED` (scripts `*-pt-2026-10-31`, traducidas de la versión inglesa **viva**, volcada con `volcar-guias-en-2026-10-31`, no del script original). Inventario de solo lectura: `inventario-idiomas-2026-10-31` (correrlo antes de dar por cerrado un idioma). Las traducciones las hizo la sesión, sin revisión nativa.
- **Hooked leído a fondo (1/11/2026):** `hooked.com.ar` es un marketplace de guías con reserva por Mercado Pago (101 guías, 86 localidades, cobra suscripción al guía, solo español, sin reglamento, `llms.txt` enorme y bots de IA permitidos: por eso ChatGPT lo recomienda primero). Análisis, debilidades y el plan de 8 puntos para el dominio argentino (ficha como página de producto, URL propia por prestador, guías de día del Litoral con tarifa, calendario con vedas, `llms.txt` con prestadores, página "para guías"): `docs/competencia-hooked-2026-11-01.md`. Dos decisiones del dueño pendientes: URL propia por prestador y si "según su perfil público en Hooked" vale como fuente. Corridas `hooked` y `hooked-2` en la rama `investigacion`.
- **Auditoría SEO con la lente de las respuestas por IA (31/10/2026):** `docs/auditoria-seo-ia-2026-10-31.md`. Base técnica ya cumplida (hreflang, sitemap, JSON-LD por vista, `llms.txt` generado, robots sin bloqueos a rastreadores de IA, respuesta corta arriba). Cambios de hoy: `dateModified` y `publisher` en el JSON-LD de las guías; llamado "Pedir cotización" bajo los "Datos clave" de cada nota (con destino y especie); sección "Cómo se contrata" en `llms.txt`. Verificado en vivo tras el deploy (PR #122, corrida 41189936186; `investigar` verificacion-20261031 y capturas 41190009391): `llms.txt` con la sección y los tres formularios, `dateModified`/`publisher` en el JSON-LD de Bariloche (es) y Chascomús (pt), hreflang es/en/pt-BR/x-default, el llamado bajo los "Datos clave" en las notas en inglés y español, y las guías pt de Jurassic Lake y San Martín–Junín renderizadas. Pendiente que corre una persona: las 10 preguntas de control de `docs/preguntas-llm.md` una vez por mes (única medición directa de si nos citan); PageSpeed de una guía en móvil.
- **Fotos cargadas hoy** (cedidas por el dueño, con crédito, ortografía confirmada): Jurassic Lake "Red Kulper", Junín "Chad Shmukler", Bariloche "arique.com" (adjunto #165, redimensionada de 1500×2000 / 443 KB a 1000×1333 / 217 KB; script `bariloche-foto-2026-10-30`).

## Al 24/10/2026

- **4 guías publicadas**: Goya, Esquina, Paso de la Patria (Corrientes, es/pt/en) y **La Paz, Entre Ríos** (es/pt, con aranceles de la tabla oficial, fiesta y precio 2026). Todo mergeado en `main` y desplegado; `verify-live` en verde. Contacto de licencias de Corrientes (Corrientes Turismo) en todas las guías correntinas.
- **Informe semanal** (`informe-semanal.yml`): Search Console + vigilancia de fuentes, un issue por lunes. Ya muestra impresiones reales (el sitio rankea). `docs/vigilancia.md`.
- **Mapa del inicio** verificado (proyección) y con etiquetas separadas (`docs/wordpress.md`).
- Páginas de ejemplo de WordPress despublicadas (404).
- **Siguiente sesión**: sumar ciudades con la receta de `docs/nueva-ciudad.md`. La **zona de pique** (`/pique/`) tiene su MVP construido: ver `docs/pique.md` (falta probarlo en un teléfono real).
- Pendientes del dueño: captura del mapa del inicio y de La Paz en vivo; ronda 2 de Gemini para Entre Ríos (tallas, cupos, vedas); OK para limpiar URLs muertas de `vigilancia/fuentes.json` (Entre Ríos: `boletinoficial.entrerios.gov.ar`, `entrerios.gov.ar/tramites` eran supuestas); revisar en Search Console el canonical http/https del inicio.

- **5.ª guía: San Martín y Junín de los Andes (Neuquén, es/en): en vivo desde el 24/10/2026** (PR #55 y #57; guías #94 es y #95 en; `wp-script` corrida 40 y `verify-live` en verde). El dueño confirmó el mismo día que aparece y se ve bien (inicio, mapa y etiqueta). Script `san-martin-junin-2026-10-24`. Fuentes: folleto oficial 2025/2026 del Reglamento (Parte Primera, Anexo Neuquén y Anexo Parques Nacionales del Sur, pasados por el dueño) y dos medios para la temporada 2026-2027; **todo dato del reglamento va con su año 2025-2026**. Dossier, auditoría de Gemini y lo no usado: `docs/investigacion/san-martin-junin.md` §7 a §10. **Falta**: la edición 2026-2027 del reglamento (la vigila `reglamento-patagonico` en `vigilancia/fuentes.json`), confirmar con la Intendencia del Lanín si cobra ingreso, y los prestadores (ninguno cargado).
- **Alojamientos y prestadores de Goya** (24/10/2026): modelo en `docs/modelo-alojamientos.md`. Publicados: Ogaraity (#46) y Experiencia Isoró (#48), con fotos. **Isoró Fishing Camp** está como borrador oculto (`isoro-fishing-camp-2026-10-24`): solo se sabe que es un operador grande de Goya con un Instagram exitoso; el texto largo que llegó es de origen dudoso y quedó solo en las notas internas. **Próxima sesión**: ver su Instagram, pedirles datos y fotos cedidas, completar y publicar. También: fotos extra con autor, y contactar a Ogaraity e Isoró (condición especial por Pescamapa).

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
| 26/10/2026 | **Posicionamiento**: la definición pasó a "todo lo que necesitás saber antes de ir a pescar: cuándo ir, qué dice el reglamento, con quién salir; lo investigamos y mostramos las fuentes; y si querés, te armamos el viaje". Sin superlativos sin fuente. El botón de la cabecera es "Armá tu viaje"; no enlazamos guías ni lodges concretos en las notas, solo el formulario; cobramos comisión, no revendemos (por ahora). Ver `docs/viaje.md`, `docs/guia-maestra-patagonia.md`. |
| 21/10/2026 | Todo lo que se publica respeta `docs/marca.md` (colores, tipografías, tamaños, componentes) y se verifica con `revisar-marca.mjs`. |
| 21/10/2026 | "Más para hacer" es un bloque repetible por ciudad: hoy el enlace a Bitácora de Viajes (Goya); a futuro, espacio que se puede vender (marcado "Publicidad", `rel="sponsored"`). |
| 21/10/2026 | Reseñas de prestadores: se prepara la infraestructura (registro de viajes concretados), no se lanzan todavía. Plan en `docs/resenas.md`. |
| 21/10/2026 | Contenido propio: por ahora se alimenta a mano (enlace + hechos + resúmenes en el chat) y Claude arma citas, notas y entrevistas; la API viene después. `docs/contenido.md`. |
| 22/10/2026 | Vigilancia semanal automática (Search Console + fuentes que pueden vencer) en un issue por semana, sin modelos. Se dice en público solo cuando lleve varias semanas corriendo. `docs/vigilancia.md`. |
| 22/10/2026 | **Lo que no se puede verificar online no se persigue.** Si un dato (p. ej. el arancel de la licencia para turista argentino) no está publicado o actualizado en una web oficial, se dice que no lo podemos confirmar y se indica qué hacer (preguntarle a la guía local o consultar a Recursos Naturales). Que las webs públicas estén desactualizadas no es responsabilidad nuestra; no se cavan agujeros. Los aranceles de la licencia no se publican sin fuente oficial vigente. |
| 22/10/2026 | Frase para las guías cuando un dato no está online: "si no lo ves online, preguntale a tu guía". |
| 22/10/2026 | Las ciudades tienen varias fiestas: se destaca una sin sobre-indexar. Un precio pasado se publica con su año. |
| 24/10/2026 | **Fichas de prestadores**: lo que carga Cheyenne tras conocerlos se conserva; Experiencia Isoró ≠ Isoró Fishing Camp; sin referencias cruzadas públicas entre prestadores; las fichas muestran hasta 4 fotos (cedidas por ellos). `docs/modelo-alojamientos.md`. |
| 24/10/2026 | **Alojamientos**: se listan (tipo Alojamiento). Fotos solo cedidas; reseñas y puntaje de Booking no se copian; precio con fecha; cobro: afiliado → comisión directa → destacado, sin maquinaria hasta que haya consultas. `docs/modelo-alojamientos.md`. |
| 23/10/2026 | **Zona de pique**: mapa comunitario a ~500 m–1 km (no 10–20 m), alimentado por muchos pescadores y por prestadores, puede lanzarse vacío como invitación. `docs/pique.md`. |
| 21/10/2026 | `info@pescamapa.com` ya enruta (Cloudflare Email Routing). |
| 21/10/2026 | **Prestadores: listar primero, verificar después.** Se publican con información pública, marcados "sin verificar" y con enlace para corregir o pedir la baja (info@pescamapa.com). Reemplaza "solo con consentimiento". `docs/prestadores.md`. |
| 21/10/2026 | **Regla de oro**: cada página responde con un dato concreto, fechado y con fuente una pregunta real, arriba y en texto; muchos lectores serán LLM. `docs/estrategia-contenido.md` (prueba de cinco preguntas, preguntas de control mensuales en `docs/preguntas-llm.md`). |
| 21/10/2026 | **Modelo**: la oferta viaja con la respuesta (condición especial por Pescamapa, publicada en la tarjeta y en llms.txt), somos el camino fácil (idioma, precio, disponibilidad) y cobramos comisión del lado del prestador. Primero Esquina y Goya. `docs/prestadores.md`, `docs/mensajes-prestadores.md`. |
| 24/10/2026 | **Claude lanza los workflows (`wp-script`, `verify-live`) directamente, bajo demanda**: el dueño no tiene que ejecutarlos. Flujo: PR → `test-wp` en verde → merge → simulacro → `$aplicar = true` por PR → en vivo → `verify-live`. Nota: dos corridas de `wp-script` a la vez chocan en `/tmp/pm-script.php` del servidor (`Permission denied`); esperar a que termine la otra y reintentar. |
| antes | Fotos solo de Commons con licencia libre o cedidas con crédito; nunca de Google. Prestadores solo con consentimiento y precios fechados. |
| 28/10/2026 | **Competencia y paquetes (dueño):** el lodge brasileño de Goya cuyo material pegó el dueño es **competidor**: no se lista ni se nombra. **Todo lo que ofrezca se puede ofrecer** (paquetes por tamaño de grupo, alojamiento todo incluido, licencia/combustible/carnada incluidos, traslados, condiciones para agencias y grupos); sus detalles sirven de guía. Cheyenne factura como agencia por Bitácora. Página "Paquetes de pesca en Goya" en vivo desde el 28/10/2026, sin precios hasta que lleguen. `docs/preparacion-comercial.md`, `docs/referencias-rincon-del-dorado.md`. |
| 27/10/2026 | **Estrategia de negocio (dueño, todavía flexible):** fase 1 = tráfico con información útil, gratis e imparcial; fase 2 = cobrar comisión (cobrarle a los guías por figurar, o vender nosotros los paquetes, como Yellow Dog). Para eso **no se listan todos los datos del prestador**: el contacto sigue interno y las consultas pasan por Pescamapa. No se vende "directorio abierto" como diferencial ni se critica a quien hace lo mismo: hoy ganamos poco o nada y el modelo no está cerrado. Detalle en `docs/prestadores.md`. |
| 29/10/2026 | **Modelo de dos niveles** (Gemini): ficha informativa (sin acuerdo) y "Operador con acuerdo con Pescamapa" (primero en la lista, con la línea de comisión al operador). Cifras de comisión y precios del análisis externo: referencia **interna**, nunca públicas. |
| 29/10/2026 | **Fotos**: solo las que el dueño aporta como disponibles o permitidas, siempre con el crédito del fotógrafo (`docs/contenido.md`). Bitácora de Viajes **no** es un prestador de pesca: va en "otras cosas para hacer". |
| 30/10/2026 | **Publicar con lo que hay**: no se bloquea una guía por lo que falta; lo no leído se dice y se marca `NO CONFIRMADO` / PENDIENTE interno, y se completa después. |
| 30/10/2026 | **Listar ya, afirmar solo lo leído.** Un prestador entra como "información pública, sin verificar" con sitio y baja visibles; los datos concretos van "según el operador", con fuente y fecha. Un sitio que se contradice (licencia de Rio Manso) o que no abre (San Huberto, 403) se publica sin ese dato. No se usa "auditada" para el nivel básico. |
| 30/10/2026 | **Una distancia de exclusión que figura en una sola de dos copias del reglamento no se publica** (Strobel 200 m, Barrancoso 100 m). Se publican las reglas del texto firmado (arts. 11 y 15 a). |
| 30/10/2026 | **La boca del Barrancoso**: "cerca de la boca" respetando la distancia mínima legal; no hay contradicción con lo que dicen los lodges. |
| 30/10/2026 | **Nombres**: "Lago Strobel" en español, "Jurassic Lake" en inglés; los slugs ya publicados no cambian. |

## Pendientes, en orden

**Al 30/10/2026 (primero):**

- Pegar desde el navegador: `reglamentodepesca.org.ar/reglamento-items/ambientes-de-rio-negro/` y el texto general 2026/27 → cerrar Bariloche (Limay río negrino, Ñirihuau, Pichileufu), validez cruzada y fieltro.
- **Anexo I oficial de Santa Cruz** (con o sin las líneas de exclusión) y la resolución de valores.
- Guía de equipo y viento para Jurassic Lake; versión es/pt de la guía pilar del dorado.
- Lodges: contactar a los de las fichas de información pública; San Huberto (403), Spring Creek (sin dominio), Estancia Tecka (sin portal). Primer operador con acuerdo: abre el nivel 2.
- Consulta legal sobre el término "agencia" y sobre vender paquetes ajenos (pendiente con el dueño).
- Pedir indexación de las URLs nuevas en Search Console (el dueño lo hace a mano).

**Lista anterior:**

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
5. **Próximas guías** (matriz SEO; receta en `docs/nueva-ciudad.md`; hechas Paso de la Patria, La Paz y San Martín/Junín): Río Grande → lagunas
   (Junín/Laguna de Gómez, Chascomús, Lobos).
6. **Eventos con URL estable** (`/eventos/fiesta-nacional-del-surubi/`,
   Fiesta del Pacú, Fiesta del Dorado): todavía no existe el tipo.
7. **Fichas de especie**: dorado y surubí publicadas el 24/10/2026 (es/en/pt; dorado: récord IGFA 25,28 kg con Diario Uno como fuente, tamaño típico 50–75 cm / 2,5–6 kg; surubí: pesca y devolución en Corrientes). Sin citar operadores de viaje (decisión del dueño). Faltan: pacú, patí, boga, tararira, trucha,
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
| `investigar.yml` | A mano (`urls`, `etiqueta`, `navegador`) | Lee páginas y PDF (con `pdftotext`) respetando robots.txt y deja el texto en la rama `investigacion`. No abre los sitios que bloquean datacenters |
| `probe-movil.yml` | A mano | Playwright con un iPhone: toques del hero y menú. Solo lectura |
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
- **Reglas de trabajo aprendidas el 30/10/2026**: (1) un `wp-script` a la vez; (2) después de un squash-merge de la rama, volver a partir de `origin/main` (`git checkout -B <rama> origin/main`) y subir con `--force-with-lease`; (3) `list_workflow_runs` sin filtros devuelve cientos de KB: guardarlo y leerlo con un script; (4) si un texto de una IA cita artículos o enlaces, se contrasta contra la fuente abierta antes de usar nada (varias URLs estaban inventadas); (5) buscar si la guía ya existe antes de crearla.
