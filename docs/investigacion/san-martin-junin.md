# Dossier: San Martín y Junín de los Andes (Neuquén)

Fecha: 2026-10-24. Interno, no se publica. Quinta guía (siguiente de la matriz SEO después de La Paz, `docs/seo-oportunidades.md`). Estructura de contenido: `docs/seo-guia-wireframes.md` (modelo 2, guía combinada).

> **Cómo se hizo esta vez.** No hubo informe de Gemini/ChatGPT: Claude buscó con la herramienta de búsqueda del sandbox y **no pudo abrir ninguna página** (`getaddrinfo ENOTFOUND` en `cazaypesca.neuquen.gob.ar`, `turismo.neuquen.gob.ar` y `noticiasnqn.com.ar`). Lo de abajo sale de **resúmenes de buscador**, no de páginas abiertas. Por la regla de `docs/investigacion/README.md` ("un enlace que nadie abrió no es fuente") solo se publicó lo que **dos medios distintos** dicen igual y lo que la estructura de la guía necesita. Todo lo demás quedó como "no lo podemos confirmar" y nota `PENDIENTE:` oculta.

## 1. Jurisdicción (lo primero que se verifica)

**Neuquén, Patagonia.** Autoridad: Dirección Provincial de Fauna (Caza y Pesca de Neuquén); dentro del Parque Nacional Lanín, también Parques Nacionales. Reglas: Reglamento de Pesca Continental Patagónico (unificado entre provincias patagónicas) más disposiciones por ambiente. **No aplica nada de Corrientes ni de Entre Ríos**: el script `licencia-contacto-2026-10-22` excluye la clave `san-martin-junin`.

## 2. Qué se publicó (y con qué respaldo)

| Dato publicado | Respaldo | Estado |
| --- | --- | --- |
| Venta online de permisos 2026-2027 desde el 15/10/2026, en `cazaypesca.neuquen.gob.ar/permisos` | Noticias NQN (15/10/2026) y ANBariloche (16/10/2026), vía buscador | Dos medios coinciden; página oficial **sin abrir** |
| Temporada en la Patagonia empieza el 1 de noviembre; en algunos ambientes cierra el 1 de mayo de 2027 | Las mismas notas | Se publica con "según la prensa" y "cada ambiente tiene su fecha" |
| Reglamento de Pesca Continental Patagónico compartido entre provincias | Las mismas notas | Idem |
| El sitio de permisos trae un asistente (chatbot) con las disposiciones por ambiente | Las mismas notas | Idem |
| Ambientes de la zona: Chimehuín, Malleo, Collón Curá, Correntoso, Lácar | Folleto de pesca 2026 de Neuquén Turismo (lista que devolvió el buscador) y `seo-guia-wireframes.md` | Solo se nombran, sin reglas |
| Aeropuerto de Chapelco (CPC); Junín a ~40 km por RN 234 | Conocimiento general, sin fuente abierta | Hay nota `PENDIENTE` para confirmar |

## 3. Qué **no** se publicó

- **Valores del permiso 2026-2027** (residentes, no residentes, extranjeros, diario, semanal, temporada): ninguna nota los trae. Regla del 22/10/2026: sin fuente oficial vigente no se publican aranceles. Se dice "no pudimos confirmarlo; mirálo en el sitio oficial o preguntale a tu guía".
- **Reglas por ambiente** (solo mosca en La Boca del Chimehuín, cupos, devolución obligatoria, cierres, horarios) y **qué rige dentro del Lanín** (incluido si hay que pagar ingreso al parque): sin fuente abierta.
- **Limpieza de equipos por didymo**: está en el wireframe, sin fuente abierta.
- **Fecha del cierre y excepciones por ambiente**, y la nota de 2021 sobre apertura anticipada del Huechulafquen (1 de octubre): vieja, no se usa.
- **Prestadores**: ninguno cargado (no hay fuente abierta ni consentimiento). Se invita a guías y lodges a escribir a info@pescamapa.com.
- **Especie**: la guía no marca especies porque no existe todavía la ficha de trucha (solo dorado y surubí); ver pendiente en `docs/estado.md`.

## 4. Pendientes: ronda 1 para Gemini/ChatGPT (el dueño pega y devuelve)

Pegar cada una por separado; pedir enlace abierto y fecha de la fuente; cerrar con "si no lo encontrás, decí NO CONFIRMADO; no estimes".

1. *¿Cuáles son los valores del permiso de pesca deportiva de Neuquén para la temporada 2026-2027, por categoría (residente, argentino no residente, extranjero; diario, semanal, temporada)? Dame la disposición y el enlace oficial donde se publican.*
2. *¿Cuál es el Reglamento General de Pesca Continental Patagónico vigente para 2026-2027 y dónde está el texto (URL)? ¿Qué disposiciones específicas hay para los ríos Chimehuín (La Boca, arriba, abajo) y Malleo?*
3. *¿Qué normas de pesca rigen dentro del Parque Nacional Lanín (lago Lácar, lagos Huechulafquen, Paimún, Epulafquen) además de las provinciales? ¿Se cobra ingreso al parque o un permiso aparte? Dame la fuente de Parques Nacionales.*
4. *¿Qué exigen las autoridades de Neuquén sobre limpieza y desinfección de equipos de pesca (didymo)? Dame la norma o el aviso oficial.*
5. *¿Cuáles son las fechas de cierre de la temporada 2026-2027 por ambiente (y excepciones de apertura) en la zona San Martín–Junín–Aluminé? Dame el calendario oficial.*
6. *¿Qué guías de pesca con mosca y lodges de San Martín y Junín de los Andes tienen sitio propio activo, con contacto y tarifa de jornada publicada hoy?* (Solo para ubicar candidatos: se confirman en su sitio, `docs/prestadores.md`.)

Al volver, auditar con la checklist de `docs/investigacion/README.md` y completar el script `san-martin-junin-2026-10-24` (o escribir uno nuevo `san-martin-junin-<fecha>.php`, como `la-paz-precio-2026-10-22`).

## 5. Vigilancia

Se cargaron tres fuentes "solo responde" en `vigilancia/fuentes.json` (`nqn-permisos`, `nqn-folleto-pesca`, `nqn-prensa-permisos`) para que el informe semanal diga si abren desde GitHub. Pasar a `texto` cuando se confirmen.

## 6. Mapa y foto

Pin = el de la tarjeta "próximamente" (lat -39.94, lon -71.07, Junín de los Andes); la guía hereda su foto (Commons, "SAN MARTIN DE LOS ANDES LACAR.JPG", crédito en `src/data/fotos.json`). La tarjeta se oculta sola porque la guía usa la misma `clave` (`san-martin-junin`). Verificar en vivo que el pin cae en su lugar y que la etiqueta no pisa a otra.

## 7. Ronda 1: respuestas de Gemini (24/10/2026) y auditoría

El dueño pegó cinco respuestas (permiso, reglamento Chimehuín/Malleo, didymo, fechas por ambiente, prestadores). **Claude no pudo abrir ninguna URL** (el sandbox no resuelve dominios externos), así que ninguna cuenta como "fuente abierta". Lo que se pudo hacer: contrastar las respuestas entre sí y con la regla de `README.md`.

**Qué se usa y qué no**

| Tema | Veredicto | Por qué |
| --- | --- | --- |
| Valores del permiso | Sigue **NO CONFIRMADO** | Gemini tampoco los encontró. No se publican (regla 22/10/2026). Ver el sitio oficial al comprar. |
| Portal del reglamento: `reglamentodepesca.org.ar` | **Se enlaza** desde la guía como lugar donde se consultan las reglas por ambiente | El dominio es el portal interprovincial conocido; pero ningún enlace profundo (PDF, `faq-items/…`) se usa. Abrirlo y confirmar. |
| Reglas por tramo de Chimehuín y Malleo (La Boca solo mosca, devolución obligatoria, sin plomadas) | **No se publican**; quedan como candidatas | La propia respuesta las llama "históricas, sujetas a ratificación 2026-2027" y marca NO CONFIRMADO el texto vigente. |
| Fechas por ambiente: cierre extendido al **31/05/2027** (Chimehuín, Malleo, Aluminé, Collón Curá, Lácar, Huechulafquen); lagos Aluminé y Moquehue "todo el año"; Quillén 1/05 | **No se publican** | Contradice la respuesta 2 de la misma ronda (fechas NO CONFIRMADO) y la prensa, que dice 1/05/2027 en ambientes puntuales. Confianza "Alta" con URLs profundas que el agente dijo no poder abrir: patrón conocido (La Paz). Apuntar para ronda 2 con el PDF del reglamento 2026-2027 abierto. |
| Didymo: decreto 137/12, Disp. 842/12, Res. 571/13, art. 28 del Reglamento, "Resolución 199/2024" en InfoLEG, prohibición de suelas de fieltro desde 1/11/2024 | **Solo la idea general**: el reglamento exige limpiar, desinfectar y secar el equipo entre ambientes | Los números de norma y la prohibición del fieltro no se publican sin abrir el texto (el "199/2024" de APN no cuadra con un reglamento patagónico). El protocolo (lavandina, salmuera, 30 min, 48 h de secado) viene de argentina.gob.ar según Gemini: abrir antes de citar cifras. |
| Lanín: ingreso al parque / permiso aparte (pregunta 3) | **Sin respuesta** | Reenviar la pregunta 3 sola. |
| Prestadores | **Ninguno se carga**: son candidatos | Varios enlaces vienen envueltos en `google.com/search?q=` (San Huberto, Nervous Waters, Chime Lodge, la asociación de guías), los correos pueden estar adivinados y ninguna tarifa está confirmada. Los rangos de USD 550-750 y 1.200 son una estimación del agente, no un dato. Regla: abrir el sitio propio de cada uno (`docs/prestadores.md`). |

**Candidatos de prestadores (sin cargar, para abrir sus sitios):** Tipiliuke Lodge, San Huberto Lodge, Northern Patagonia Lodge (Nervous Waters), Patagonia River Guides, Andes Drifters, Chime Lodge, Tres Ríos Lodge, y el directorio de guías de Neuquén. Pendiente de cada uno: sitio abierto, contacto real y tarifa publicada.

**Ronda 2 (pegar en Gemini/ChatGPT, una por vez):**
1. *Abrí el PDF del Reglamento de Pesca Deportiva Continental Patagónico 2026-2027 en reglamentodepesca.org.ar y transcribime textual lo que dice de los ríos Chimehuín y Malleo: modalidad, devolución, fechas de apertura y cierre. Dame la URL del PDF abierto y su fecha. Si no podés abrirlo, decí NO CONFIRMADO.*
2. *Lo mismo para el lago Lácar y el Huechulafquen, y qué cobra o exige Parques Nacionales (Lanín) por ingreso o permiso.*
3. *Texto del artículo del reglamento 2026-2027 sobre limpieza de equipos (didymo) y si prohíbe las suelas de fieltro; enlace al PDF.*
4. *Para cada prestador de la lista de candidatos: ¿el sitio abre?, ¿qué contacto y qué tarifa publica hoy? Solo datos que figuren en su propio sitio.*

## 8. Fuente oficial abierta: Reglamento, Parte Primera (pegado por el dueño, 24/10/2026)

El dueño copió el texto de https://reglamentodepesca.org.ar/reglamento/ (portal interprovincial: Neuquén, Río Negro, Chubut y Parques Nacionales). **Es la primera fuente oficial leída de esta guía.** Dos advertencias:

- **Es la edición 2025-2026**, no la 2026-2027: art. 7 "Se inicia el 01/11/2025 y finaliza el 01/05/2026"; art. 5 los permisos de temporada vencen el 31/10/2026; art. 18 sacrificio en lagos del 01/12/2025 al 31/03/2026. Las fechas y los valores se publican **con su año** (decisión 22/10/2026: un precio pasado se publica con su año).
- **Falta la Parte Segunda** (Anexos por jurisdicción y ambiente): sin ella no hay reglas de Chimehuín, Malleo, Lácar, etc., ni fechas de cierre por ambiente. Los "31 de mayo" de Gemini siguen sin respaldo.

**Qué se publica (art. citado):**

| Dato | Art. |
| --- | --- |
| Permiso personal, con DNI; categorías y valores 2025-2026: residente país $10.000 diario / $20.000 10 días / $40.000 temporada; menores 13-17 temporada $20.000; gratis mayores de 65, jubilados, pensionados, menores de hasta 12 y discapacidad; extranjeros no residentes $50.000 / $140.000 / $280.000; trolling adicional $20.000 / $40.000 / $80.000 | 2, 3 |
| Los permisos del reglamento valen en Neuquén, Río Negro, Chubut y Parques Nacionales | 4 |
| Solo de día; un equipo por pescador; un señuelo artificial con un único anzuelo | 6, 13, 16 |
| Ríos y arroyos (y 200 m de su naciente o desembocadura en un lago): devolución obligatoria, anzuelo simple sin rebaba o aplastada | 17, 23 |
| Lagos: un ejemplar por día del 01/12 al 31/03 (2025-2026), con excepciones | 18 |
| Prohibido calzado de vadeo con suela de fieltro; prohibido el plomo en anclas, plomadas y señuelos | 22.18, 22.21 |
| Desinfección de equipos, embarcaciones y vehículos cuando lo requiera la autoridad; extranjeros con equipos nuevos o desinfectados | 27, 28 |

**Corrige a Gemini:** el fieltro está prohibido (art. 22.21, confirmado) pero el "Artículo 28" y los números de decreto/resolución del informe no se citan: el texto solo dice "cuando lo requiera la autoridad de aplicación". El protocolo con lavandina y salmuera sigue sin fuente abierta.

**Todavía abierto:** valores 2026-2027; Anexo de Neuquén por ambiente; Lanín (Parques Nacionales); prestadores. Ronda 2 §7, preguntas 1 a 4 (la 3 ya no hace falta salvo el protocolo de desinfección). Vigilancia: `reglamento-patagonico` pasa a `texto` en `fuentes.json`: cuando el portal cambie a la edición 2026-2027, actualizar la guía.

**PDF recibido (24/10/2026, `pesca.pdf`, 15 páginas):** es el folleto oficial "Reglamento de Pesca Deportiva Continental Patagónico 2025/2026" (portada con AAPM, Chubut, Neuquén, Río Negro y APN), páginas 1 a 15: créditos, instrucciones, sumario y la **Parte Primera completa** (arts. 1 a 29, páginas 7 a 13, con su traducción al inglés desde la 14). **Coincide palabra por palabra con el texto del portal**: confirma permisos, valores, fechas 2025-2026, devolución obligatoria en ríos, fieltro (art. 22.21) y desinfección (arts. 27-28). **No trae los Anexos**: según el sumario, el **Anexo Neuquén está en las páginas 33 a 49**, Parques Nacionales del Sur en 59 a 82, y "Alerta Didymo" en la 99. Falta pedir esas páginas (33-49, 59-82 y 87-90/99) para cargar las reglas por ambiente.

## 9. Anexos recibidos (24/10/2026): `pesca-2.pdf` y `pesca-3.pdf`

- **`pesca-2.pdf` = págs. 33 a 49, Anexo Neuquén 2025/2026 completo.** Se usó para la guía: autoridad (Ministerio de Seguridad, fiscaliza la Dirección Provincial de Fauna); guías solo si están habilitados como prestadores turísticos de Neuquén (nómina en neuquentur.gob.ar); **permiso adicional de Zona Preferencial** (Boca del Chimehuín y Correntoso: $40.000 temporada, $20.000 10 días, $10.000 diario); dropper salvo Boca y Correntoso; sin motor en ríos (excepto Limay); sin pescar desde puentes; límites y fechas del listado alfabético. Por ambiente: **Chimehuín Zona I** (naciente a 250 m aguas abajo del puente: solo mosca, devolución total, cierre 31/05/2026), **Zona II** (hasta el Collón Curá: M, S, SBM, devolución de salmónidos, 1 perca), **Malleo** (del límite del Lanín al puente de la ruta 23: solo mosca y devolución total; de ahí al Aluminé: M o SBM, 1 perca; cierre 31/05/2026), **Collón Curá** (M, S, SBM, 2 percas, cierre 31/05/2026), **Quilquihue** (cierre 31/05/2026), **Lolog** (2 salmónidos hasta el 01/05; cierre 31/10/2026). Contactos de la Dirección de Fauna: San Martín (Mariano Moreno 465, 0297 241-2211), Junín (Roca 29, 0297 249 1277).
- **Esto corrige/confirma a Gemini:** el cierre "31 de mayo" de Chimehuín, Malleo y Collón Curá era **correcto para 2025-2026** (no se sabe para 2026-2027, y la guía lo dice así). **Lácar y Huechulafquen NO están en el Anexo Neuquén**: son del Parque Nacional Lanín, y Gemini los mezcló. El Correntoso (Zona II, solo mosca) es de la zona de Villa La Angostura, no de San Martín/Junín: se sacó del texto principal.
- **`pesca-3.pdf` = págs. 59 a 64, Anexo Parques Nacionales del Sur, parte general.** Sirve para Lanín: solo se pesca en los ambientes listados (todo lo no mencionado está excluido); prohibido sacrificar autóctonas; prohibidos los motores de dos tiempos a carburación (incluso apagados); sin navegación a motor en ríos y **sin flotación (drift) de ríos del parque salvo Arrayanes, Frey, Hua Hum, Limay, Manso, Rivadavia y Puelo**; sin perros ni mascotas (huillín); acceso por senderos; dropper y pesca al hilo permitidos; sin fieltro; "día de pesca" = luz diurna según Hidrografía Naval. Intendencia del PN Lanín: San Martín de los Andes, Perito Moreno 1008, 2972-427233, lanin@apn.gob.ar.
- **Todavía falta: págs. 65 a 82** (listado alfabético de ambientes de Parques Nacionales del Sur: Lácar, Huechulafquen, Paimún, Epulafquen, Malleo alto, Quillén, Ruca Choroy…) y si hay ingreso al parque o permiso aparte. Y la edición 2026-2027 de todo el reglamento. La pregunta 3 de la ronda 2 queda reducida a pedir esas páginas.

## 10. Listado de Parques Nacionales del Sur (págs. 65-82, `pesca-4.pdf`, 24/10/2026)

Cargado en la guía (edición 2025-2026, con su año): **Lácar** (pesca del 01/11/2025 al 31/05/2026 y durante octubre de 2026; LD 2 salmónidos por día todo el año); **Huechulafquen** (mismas fechas; LD 2; arrastre habilitado salvo del arroyo Blanco al Chimehuín y 500 m hasta Quilantral; alerta didymo); **Paimún** lago (LD 2) y río (solo curso medio e inferior, LD 1); **Epulafquen** (LD 2); **Malleo** dentro del parque (sin navegación, solo mosca, devolución total); **Lolog** (LD 2, compartido). Regla general repetida en cada ambiente: prohibido el calzado con suela de fieltro. "Los cuerpos de agua no listados: no se permite la pesca en ninguna de sus formas."

**Observaciones:** el reglamento **no menciona ningún ingreso al parque ni permiso aparte** para pescar (el único permiso exclusivo de PN es el de Laguna Blanca, $20.000, que no es de esta zona): la guía lo dice y manda a consultar a la Intendencia del Lanín. La fecha "durante octubre del 2026" indica que Lácar y Huechulafquen **ya están abiertos en octubre de 2026** bajo esta edición; se publica así, con el año. No aparecen en el listado el **Chimehuín** ni el **Collón Curá** (son de Neuquén, ya cargados).

**Pendiente:** la edición 2026-2027 del reglamento (todas las fechas, valores y límites); confirmar con la Intendencia del Lanín si cobra ingreso; prestadores (ronda 2). Con esto el Anexo Neuquén y el de Parques Nacionales quedan leídos para esta zona.
