# «Quiénes somos»: propuesta de reemplazo (18/11/2026) — NO publicada

Orden del dueño (18/11/2026, punto 3): que la sección no se centre en la persona sino en el perfil y la intención con que se creó Pescamapa. Los nombres propios pueden quedar, pero el foco va en el propósito. **Nada de esto está publicado**: espera el visto bueno del dueño. No se inventó ningún hecho; cada afirmación de la propuesta sale de una fuente del repo (tabla de hechos más abajo). Lo que falta está en «Preguntas al dueño».

Dónde vive hoy el texto: claves `ns.*` de `wordpress/plugins/pescamapa-core/data/ui.json`, vista `wordpress/themes/pescamapa/vistas/nosotros.php` (`/quienes-somos/`, `/pt/quem-somos/`, `/en/about/`). La sección «Cómo funciona y cómo se sostiene» (`ns.plata.*`) ya se corrigió en este PR (punto 1 y 2) y no cambia con esta propuesta.

## 1. Texto actual (publicado hoy)

Estructura: titular, bajada, «Qué es Pescamapa», «Por qué existe», **«Las personas» (dos fichas con nombre y trayectoria)**, «Cómo trabajamos» (3 puntos), «En Goya», «Cómo funciona y cómo se sostiene», «Escribinos». Además, `ns.description` (meta description) y `ns.llms` (llms.txt) nombran a las dos personas, y `inc/seo.php` publica a ambas como `creator` (WebSite) y `founder` (AboutPage) en JSON-LD.

### Español

- **Eyebrow / H1:** Quiénes somos / Quiénes hacen Pescamapa
- **Title:** Quiénes somos: quién hace Pescamapa y cómo trabaja | Pescamapa
- **Description:** Pescamapa es un servicio gratuito para pescadores que viajan a la Argentina. Lo hacen Alberto Ayala y Cheyenne Justiniano, con raíz en Goya; así investigamos y así se sostiene.
- **Bajada:** Somos dos personas con raíz en Goya, Corrientes. Investigamos cada destino, mostramos las fuentes y respondemos las consultas nosotros.
- **Por qué existe:** Al mirar cómo se ofrece la pesca en la Argentina encontramos lo mismo una y otra vez: precios repartidos en muchos sitios o desactualizados, reglamentos difíciles de ubicar y contactos que son el WhatsApp personal de alguien. Pescamapa junta esa información en un solo lugar, le pone fecha y fuente, y la mantiene al día.
- **Las personas — Alberto Ayala:** Fundador de MOMUTO y de otros negocios en internet desde hace 15 años. Trabaja en marketing digital, posicionamiento en buscadores y en asistentes de IA, y desarrollo web. Tiene familia en Goya, donde pasa cada vez más tiempo. En Pescamapa se ocupa del sitio y de los datos.
- **Las personas — Cheyenne Justiniano:** Técnica en Turismo y guía de turismo. Cofundadora de Bitácora de Viajes, una agencia de turismo receptivo de Goya, y técnica de la oficina de turismo de la ciudad. Guía salidas de naturaleza y avistaje de aves en la Isla de las Damas y la Reserva Natural Isoró. No es guía de pesca: en Pescamapa aporta el conocimiento del lugar y la relación con los guías de pesca y los alojamientos de la zona.
- **Cómo trabajamos:** (1) Leemos la norma vigente y la página del propio lugar, y anotamos la fecha en que lo hicimos. Cada guía muestra sus fuentes. (2) Lo que no pudimos confirmar, lo decimos. Un precio se publica solo con su fecha. (3) Las fichas de guías y alojamientos armadas con información pública están marcadas como sin verificar, y se corrigen o se quitan cuando el prestador lo pide.
- **En Goya:** En Goya conocemos en persona a guías de pesca y alojamientos. Si tu consulta es para esa zona, la resolvemos con gente del lugar que conocemos.

### English

- **H1 / Title:** Who makes Pescamapa / About us: who makes Pescamapa and how we work | Pescamapa
- **Description:** Pescamapa is a free service for anglers travelling to Argentina. Made by Alberto Ayala and Cheyenne Justiniano, rooted in Goya; how we research and how the site pays its way.
- **Bajada:** We are two people with roots in Goya, Corrientes. We research each destination, show our sources and answer the inquiries ourselves.
- **Why it exists:** Looking at how fishing is sold in Argentina, we kept finding the same things: prices scattered across many sites or out of date, regulations that are hard to find, and contacts that are somebody's personal WhatsApp. Pescamapa puts that information in one place, with a date and a source, and keeps it current.
- **The people — Alberto Ayala:** Founder of MOMUTO and of other online businesses over the past 15 years. He works in digital marketing, search and AI-assistant visibility, and web development. He has family in Goya, where he spends more and more time. At Pescamapa he looks after the site and the data.
- **The people — Cheyenne Justiniano:** A tourism technician and tour guide. Co-founder of Bitácora de Viajes, an inbound travel agency in Goya, and a technician at the city's tourism office. She guides nature and birdwatching outings on Isla de las Damas and in the Isoró Nature Reserve. She is not a fishing guide: at Pescamapa she brings local knowledge and the relationships with the area's fishing guides and lodging.
- **How we work:** (1) We read the regulation in force and the place's own page, and note the date we did. Every guide shows its sources. (2) What we could not confirm, we say so. A price is published only with its date. (3) Listings for guides and lodging built from public information are marked as unverified, and are corrected or removed when the provider asks.
- **In Goya:** In Goya we know fishing guides and lodging in person. If your inquiry is for that area, we sort it out with local people we know.

### Português

- **H1 / Title:** Quem faz a Pescamapa / Quem somos: quem faz a Pescamapa e como trabalha | Pescamapa
- **Description:** A Pescamapa é um serviço gratuito para pescadores que viajam para a Argentina. Feita por Alberto Ayala e Cheyenne Justiniano, com raízes em Goya; como pesquisamos e como o site se sustenta.
- **Bajada:** Somos duas pessoas com raízes em Goya, Corrientes. Pesquisamos cada destino, mostramos as fontes e respondemos às consultas nós mesmos.
- **Por que existe:** Ao olhar como a pesca é oferecida na Argentina, encontramos sempre a mesma coisa: preços espalhados por muitos sites ou desatualizados, regulamentos difíceis de achar e contatos que são o WhatsApp pessoal de alguém. A Pescamapa reúne essa informação em um só lugar, com data e fonte, e a mantém em dia.
- **As pessoas — Alberto Ayala:** Fundador da MOMUTO e de outros negócios na internet há 15 anos. Trabalha com marketing digital, posicionamento em buscadores e em assistentes de IA, e desenvolvimento web. Tem família em Goya, onde passa cada vez mais tempo. Na Pescamapa cuida do site e dos dados.
- **As pessoas — Cheyenne Justiniano:** Técnica em Turismo e guia de turismo. Cofundadora da Bitácora de Viajes, uma agência de turismo receptivo de Goya, e técnica do escritório de turismo da cidade. Guia passeios de natureza e observação de aves na Isla de las Damas e na Reserva Natural Isoró. Não é guia de pesca: na Pescamapa traz o conhecimento do lugar e a relação com os guias de pesca e as hospedagens da região.
- **Como trabalhamos:** (1) Lemos a norma vigente e a página do próprio lugar, e anotamos a data em que fizemos isso. Cada guia mostra as suas fontes. (2) O que não conseguimos confirmar, nós dizemos. Um preço só é publicado com a sua data. (3) As fichas de guias e hospedagens montadas com informação pública estão marcadas como não verificadas, e são corrigidas ou retiradas quando o prestador pede.
- **Em Goya:** Em Goya conhecemos pessoalmente guias de pesca e hospedagens. Se a sua consulta é para essa região, resolvemos com gente do lugar que conhecemos.

## 2. Propuesta (el foco pasa al propósito; las personas quedan en una sección corta al final)

Orden: para quién → el problema → cómo trabajamos → qué nos distingue → quiénes hay detrás (corto, con los nombres) → escribinos. Las secciones «Cómo funciona y cómo se sostiene» y «Una aclaración sobre Goya» (punto 1) se mantienen después de «Qué nos distingue». Las frases entre [corchetes] dependen de la respuesta del dueño y no se publican si no hay dato.

### Español

- **H1:** Qué es Pescamapa y por qué existe
- **Bajada:** Un servicio gratuito para pescadores que viajan a la Argentina: reunimos lo que hay que saber antes de ir, con la fuente a la vista, y te ayudamos a comparar con quién salir.
- **Para quién y qué problema resuelve:** Para quien piensa pescar en la Argentina, sea del país o de afuera (por eso el sitio está en español, portugués e inglés). Quien organiza un viaje de pesca encuentra lo mismo una y otra vez: precios repartidos en muchos sitios o desactualizados, reglamentos difíciles de ubicar y contactos que son el WhatsApp personal de alguien. Pescamapa junta esa información en un solo lugar, le pone fecha y fuente, y la mantiene al día. [Origen: una frase sobre por qué empezó, a completar por el dueño.]
- **Cómo trabajamos:** Investigamos: leemos la norma vigente y la página del propio lugar. Mostramos las fuentes: cada guía dice de dónde sale cada dato. Fechamos los datos: un precio se publica solo con su fecha. Decimos lo que no pudimos confirmar. Corregimos cuando alguien nos avisa: las fichas armadas con información pública están marcadas como sin verificar, y se corrigen o se quitan cuando el lugar o el guía lo pide.
- **Qué no hacemos:** No vendemos viajes: si querés, consultamos a los lugares y guías que encajan y te devolvemos una comparación; reservás y pagás directo con el que elijas.
- **Qué nos distingue:** Que lo que decimos tiene fuente y fecha, y que lo que no sabemos lo decimos. Que comparamos los lugares y guías que encajan con tu viaje tengan o no un acuerdo con nosotros. Que la información verificada (reglamento, temporada, vedas) y la consulta de opciones están en el mismo lugar. [En Goya, además, conocemos en persona a guías de pesca y alojamientos.]
- **Quiénes hay detrás:** Pescamapa la hacen Alberto Ayala y Cheyenne Justiniano, con raíz en Goya, Corrientes. [Una línea por persona, solo con los datos que el dueño confirme: ver preguntas.]

### English

- **H1:** What Pescamapa is and why it exists
- **Bajada:** A free service for anglers travelling to Argentina: we bring together what you need to know before you go, with the source in plain view, and help you compare who to go with.
- **Who it is for and what problem it solves:** For anyone planning to fish in Argentina, local or from abroad (which is why the site is in Spanish, Portuguese and English). People organising a fishing trip keep finding the same things: prices scattered across many sites or out of date, regulations that are hard to find, and contacts that are somebody's personal WhatsApp. Pescamapa puts that information in one place, with a date and a source, and keeps it current. [Origin: one sentence on why it started, for the owner to supply.]
- **How we work:** We research: we read the regulation in force and the place's own page. We show our sources: every guide says where each fact comes from. We date the data: a price is published only with its date. We say what we could not confirm. We correct when someone tells us: listings built from public information are marked as unverified, and are corrected or removed when the place or guide asks.
- **What we do not do:** We do not sell trips: if you want, we ask the places and guides that fit and send you a comparison; you book and pay directly with the one you choose.
- **What sets us apart:** What we say has a source and a date, and what we do not know, we say. We compare the places and guides that fit your trip whether or not they have an agreement with us. Verified information (rules, season, closed seasons) and asking for options are in the same place. [In Goya, we also know fishing guides and lodging in person.]
- **Who is behind it:** Pescamapa is made by Alberto Ayala and Cheyenne Justiniano, rooted in Goya, Corrientes. [One line per person, only with the facts the owner confirms: see questions.]

### Português

- **H1:** O que é a Pescamapa e por que existe
- **Bajada:** Um serviço gratuito para pescadores que viajam para a Argentina: reunimos o que é preciso saber antes de ir, com a fonte à vista, e ajudamos a comparar com quem sair.
- **Para quem e que problema resolve:** Para quem pensa em pescar na Argentina, seja do país ou de fora (por isso o site está em espanhol, português e inglês). Quem organiza uma viagem de pesca encontra sempre a mesma coisa: preços espalhados por muitos sites ou desatualizados, regulamentos difíceis de achar e contatos que são o WhatsApp pessoal de alguém. A Pescamapa reúne essa informação em um só lugar, com data e fonte, e a mantém em dia. [Origem: uma frase sobre por que começou, a completar pelo dono.]
- **Como trabalhamos:** Pesquisamos: lemos a norma vigente e a página do próprio lugar. Mostramos as fontes: cada guia diz de onde vem cada dado. Datamos os dados: um preço só é publicado com a sua data. Dizemos o que não conseguimos confirmar. Corrigimos quando alguém nos avisa: as fichas montadas com informação pública estão marcadas como não verificadas, e são corrigidas ou retiradas quando o lugar ou o guia pede.
- **O que não fazemos:** Não vendemos viagens: se você quiser, consultamos os lugares e guias que combinam e devolvemos uma comparação; você reserva e paga direto com o que escolher.
- **O que nos diferencia:** O que dizemos tem fonte e data, e o que não sabemos, dizemos. Comparamos os lugares e guias que combinam com a sua viagem tenham ou não um acordo conosco. A informação verificada (regulamento, temporada, defesos) e o pedido de opções estão no mesmo lugar. [Em Goya, além disso, conhecemos pessoalmente guias de pesca e hospedagens.]
- **Quem está por trás:** A Pescamapa é feita por Alberto Ayala e Cheyenne Justiniano, com raízes em Goya, Corrientes. [Uma linha por pessoa, só com os dados que o dono confirmar: veja as perguntas.]

### Meta (para las tres lenguas, a publicar con el resto)

- **Description es:** Qué es Pescamapa y por qué existe: un servicio gratuito para pescadores que viajan a la Argentina. Investigamos, mostramos las fuentes, fechamos los datos y corregimos cuando nos avisan; no vendemos viajes.
- **Description en:** What Pescamapa is and why it exists: a free service for anglers travelling to Argentina. We research, show our sources, date our data and correct when told; we do not sell trips.
- **Description pt:** O que é a Pescamapa e por que existe: um serviço gratuito para pescadores que viajam para a Argentina. Pesquisamos, mostramos as fontes, datamos os dados e corrigimos quando nos avisam; não vendemos viagens.
- **`ns.llms` (llms.txt):** el propósito y el método (investigación con fuente y fecha, correcciones a pedido, no vende viajes) y, al final, «hecho por Alberto Ayala y Cheyenne Justiniano»; sin trayectorias.

## 3. Qué hechos se usan y de dónde

| Afirmación | Fuente en el repo |
| --- | --- |
| Servicio gratuito para pescadores que viajan a la Argentina | `marca.definicion` (ui.json); `docs/viaje.md` «Posicionamiento (13/11/2026)» |
| Español, portugués e inglés; extranjeros y del país | `CLAUDE.md` («Idiomas»); `docs/estrategia-contenido.md` («Idiomas con intención»); `inc/maquinas.php` («Se atiende en español, portugués e inglés») |
| El problema (precios dispersos, reglamentos difíciles, WhatsApp personal) | `ns.porque.p` (texto ya publicado) |
| Leemos la norma y la página del lugar, fechamos, mostramos fuentes, un precio solo con fecha, decimos lo que no pudimos confirmar | `ns.como.1`, `ns.como.2` (ya publicados); `docs/estrategia-contenido.md` (prueba de cinco preguntas) |
| Fichas con información pública marcadas sin verificar; se corrigen o se quitan a pedido | `ns.como.3`; `docs/prestadores.md` → «Estrategia de listados» (punto 2: puerta de salida siempre visible) |
| No vendemos el viaje; reservás y pagás directo; comparamos tengan o no acuerdo; comisión de algunos lugares y guías | Orden del dueño del 18/11/2026 (punto 2) y `ns.plata.p` corregido en este PR |
| Información verificada (SABER) y consulta de opciones juntas | `marca.saber`, `inicio.prop.eyebrow` («Saber e ir»), `docs/sesion-2026-11-14-al-16.md` |
| Raíz en Goya, Corrientes; conocemos en persona guías y alojamientos de Goya | `ns.bajada`, `ns.goya.p` (ya publicados); `docs/revision-negocio-2026-11-09.md` |
| Los nombres (Alberto Ayala, Cheyenne Justiniano) | `ns.p1.nombre`, `ns.p2.nombre`; `legal.json`; `inc/seo.php` |

No se usa: la trayectoria de Alberto (MOMUTO, 15 años, marketing) ni la de Cheyenne (Bitácora, oficina de turismo, naturaleza) en el cuerpo; quedan, si el dueño quiere, en una línea cada una dentro de «Quiénes hay detrás».

## 4. Preguntas concretas al dueño

1. **Origen.** ¿Por qué y cuándo empezó Pescamapa? El repo no lo dice (solo el problema que encontraron, `ns.porque.p`). ¿Hubo un viaje, una consulta, una experiencia puntual que lo disparó y que quieran contar? Una frase alcanza.
2. **Perfil.** ¿Quién es el lector que tienen en mente? La propuesta dice «del país o de afuera» a partir de los tres idiomas; ¿quieren nombrar al pescador brasileño y al angloparlante en particular (la estrategia de contenido los menciona), o no?
3. **Las personas.** ¿Siguen los nombres? Si sí: ¿una línea cada uno con la trayectoria actual (Alberto: MOMUTO y negocios en internet; Cheyenne: técnica en turismo y guía de naturaleza), o solo los nombres y el rol en Pescamapa («el sitio y los datos» / «el conocimiento del lugar y la relación con guías y alojamientos»)?
4. **Bitácora y la oficina de turismo.** La propuesta no menciona a Bitácora ni el cargo de Cheyenne en la oficina de turismo (la divulgación de Bitácora vive en «Cómo funciona», punto 1). ¿Se mantiene así? `docs/revision-negocio-2026-11-09.md` pide cuidar que el cargo público no se use para derivar consultas.
5. **Goya.** ¿«Raíz en Goya» sigue siendo parte de la identidad o es solo el origen de las dos personas? En la propuesta queda entre corchetes («conocemos en persona a guías y alojamientos de Goya»).
6. **Quién responde.** El texto actual dice «respondemos las consultas nosotros» y el acuse dice «lo lee una persona». ¿Se mantiene como promesa pública?
7. **Qué nos distingue.** La propuesta usa solo lo comprobable (fuente y fecha, lo que no sabemos lo decimos, comparamos tengan o no acuerdo). ¿Hay algo más que sea verdad y verificable (por ejemplo, entrevistas con nombre, precios consultados con fecha) que quieran destacar? Sin superlativos ni rankings sin fuente.
8. **Datos de registro y contacto.** ¿Hay figura legal, número de legajo o WhatsApp de Pescamapa para publicar? La revisión del 9/11 lo dejaba pendiente. No se escribió nada.
9. **Fotos.** Sigue sin fotos por decisión del 9/11; ¿se mantiene?
10. **Fuera de esta sección (si se aprueba).** Habría que alinear `inc/seo.php` (`creator` del WebSite y `founder` del AboutPage, que hoy publican nombre y trayectoria), `ns.description`, `ns.llms` y los checks de `test-wp.yml` que buscan «Quiénes hacen Pescamapa» y «No es guía de pesca». `legal.json` («un proyecto de Cheyenne Justiniano y Alberto Ayala» en términos y privacidad) no se toca: identifica a quien trata los datos y es decisión legal del dueño.
