# Dossier: Chascomús y las lagunas bonaerenses (pejerrey)

Fecha: 2026-10-26. Interno, no se publica. Decisión del dueño (26/10/2026): abrir guía de **pejerrey en Chascomús** como contenido propio de Buenos Aires (audiencia AMBA/escapada de un día, otro público que el inbound del Litoral; sirve para autoridad, tráfico y "altura/viento" de la zona). Circuito y auditoría: `docs/investigacion/README.md`. Receta de la guía: `docs/nueva-ciudad.md`.

## 1. Jurisdicción (lo primero que se verifica)

**Provincia de Buenos Aires.** No aplica nada de Corrientes, Entre Ríos ni Neuquén: el script `licencia-contacto-2026-10-22` y los textos de licencia/reglamento de esas guías **no se reutilizan** (hay que excluir la clave nueva). Autoridad y norma: **a confirmar** (no se escribe de memoria). Las lagunas pampásicas son aguas provinciales; los clubes y muelles de Chascomús suelen ser municipales o de entidades privadas: confirmar quién administra cada acceso.

## 2. Qué necesita la guía (y de dónde sale hoy)

| Bloque de la guía | Dato que falta | Fuente esperada |
| --- | --- | --- |
| Licencia | ¿Hace falta licencia de pesca deportiva en PBA? ¿Cuánto cuesta (residente/no residente, diaria/anual)? ¿Dónde se tramita? | Resolución/norma provincial + página oficial del trámite |
| Reglamento | Talla mínima, cupo por pescador y por día, artes permitidas, horarios, vedas del pejerrey y de otras especies de la laguna | Resolución vigente (no revistas ni notas) |
| Temporada | ¿Cuándo se pesca mejor el pejerrey? (ya no "todo el año" sin matiz) | Fuente técnica (organismo, club, guía con sitio propio), con fecha |
| Dónde se baja al agua | Accesos públicos, muelles, clubes, costos y estado, con fecha (área general, **nunca** coordenadas de pesqueros) | Sitio oficial del municipio / club |
| Fiestas y torneos | Fecha y lugar de la edición vigente (ver regla de fiestas: una ciudad suele tener varias) | Sitio oficial o prensa con fecha |
| Prestadores | Guías, lanchas, hospedaje con sitio propio activo | Su sitio propio (`docs/prestadores.md`); nada de listados de terceros |
| Condiciones | Altura: **no existe estación oficial** en estas lagunas (verificado 26/10/2026 en la API del INA). Se muestra **viento y lluvia** (Open-Meteo), que es lo que decide la salida | Ya resuelto: entrada solo-clima en `rio.json` al publicar la guía |

## 3. Qué ya sabemos y qué no

- INA a5 no tiene estación de altura en Chascomús ni lagunas bonaerenses (solo La Picasa, Santa Fe). Lo único oficial cerca es el Río de la Plata (Buenos Aires, La Plata): no es el mismo cuerpo de agua, no se usa como proxy.
- **Todo lo demás es candidato hasta auditarlo.** Nada del listado de abajo se publica sin una fuente abierta por una persona.

## 4. Ronda 1: preguntas para Gemini/ChatGPT (el dueño pega una por vez y devuelve)

Pedir en todas: enlace abierto, fecha de la fuente y, para normas, el número y el año. Cerrar con *"si no lo encontrás, decí NO CONFIRMADO; no estimes"*. Pegar en Gemini o ChatGPT (cualquiera de los dos; si pueden, pasar el PDF de la norma en vez de un resumen).

1. *¿Se necesita licencia de pesca deportiva para pescar en la laguna de Chascomús (Provincia de Buenos Aires)? ¿Qué organismo la emite, cuánto cuesta en 2026 (residente, no residente, extranjero; diaria, anual) y dónde se tramita? Dame la resolución o ley y el enlace oficial del trámite.*
2. *¿Cuál es la norma vigente de pesca continental de la Provincia de Buenos Aires para pejerrey en lagunas como Chascomús: talla mínima, cupo por pescador y por día, artes y carnadas permitidas, horarios, cantidad de líneas? Dame el texto de la resolución (número y año) y el enlace.*
3. *¿Hay vedas (de pejerrey u otras especies) o restricciones por época en la laguna de Chascomús y en las lagunas Encadenadas para 2026-2027? Dame la norma con su fecha. Si la veda cambió en los últimos años, decime el año de cada versión.*
4. *¿Cuáles son los accesos para pescar en la laguna de Chascomús (muelles, clubes de pesca, costanera, balnearios)? Para cada uno: quién lo administra, si cobra entrada o canon, si alquila lanchas y qué dice su sitio oficial. Solo datos que figuren en el sitio del municipio o del propio club, con enlace y fecha. No me des coordenadas de puntos de pesca.*
5. *¿Cuándo se pesca mejor el pejerrey en Chascomús (meses, condiciones de agua, viento), según organismos, clubes o guías con sitio propio? Dame la fuente de cada afirmación y evitá "todo el año" sin matiz.*
6. *¿Qué fiestas o torneos de pesca del pejerrey se hacen en Chascomús (nombre, fecha de la edición 2026 o 2027, lugar, organizador)? Dame el sitio oficial o la nota con fecha. Si hay más de una, listalas todas.*
7. *¿Qué guías de pesca, lancheros y alojamientos orientados a pescadores de Chascomús tienen sitio propio activo con contacto y, si lo publican, tarifa? Solo datos que figuren en su propio sitio.* (Para ubicar candidatos: se confirman en su sitio, `docs/prestadores.md`.)
8. *¿Qué otras lagunas cercanas a Chascomús (sistema Encadenadas y alrededores) se pescan, y cuáles tienen acceso público?* (Para decidir si la guía es solo Chascomús o "Chascomús y lagunas cercanas".)

## 5. Qué hago cuando vuelvan las respuestas

1. Auditar con la checklist del `README.md` (¿quién lo dice?, ¿la URL abre?, ¿año?, ¿jurisdicción?, ¿segunda fuente?). Guardar abajo, en este archivo, qué se usa y qué se descarta.
2. Crear el script `wordpress/scripts/chascomus-<fecha>.php` (modelo: `san-martin-junin-2026-10-24.php`: poco dato confirmado, es/en), con simulacro y lectura de vuelta. Lo dudoso va como `PENDIENTE:` (no se ve en la web).
3. **Ficha de especie: pejerrey** (hoy solo existen dorado, surubí y trucha): con fuente, sin tallas ni cupos que no estén en la norma.
4. Sumar `chascomus` a `rio.json` (solo clima: lat -35.57, lon -58.01) y al mapa del inicio (verificar que cae en su lugar).
5. Sumar sus fuentes a `vigilancia/fuentes.json`; agregar la guía a `verify-live.yml`.
6. Una nota "Cuándo pescar el pejerrey" (evergreen + recuadro anual) cuando haya fuentes; ver `docs/estrategia-contenido.md`.

## 6. Notas de contenido

- Audiencia: escapada de un día desde Buenos Aires; la guía responde primero "cómo llegar, qué permiso, cuándo, dónde alquilar lancha". El CTA de viaje armado tiene menos peso que en el Litoral: ofrecer también la consulta a un guía.
- Respetar "áreas, no puntos de pesca" y "sin 'se pesca todo el año' sin matiz".

## 7. Ronda 1: respuesta recibida (27/10/2026) y auditoría

El dueño pegó la respuesta de la ronda 1 (8 puntos). **Ninguna URL del informe se pudo abrir desde el sandbox**, así que ninguna cuenta como fuente abierta. Se contrastó con resúmenes de buscador de páginas oficiales (`normas.gba.gob.ar`, `gba.gob.ar/desarrollo_agrario`) y con notas de turismo de 2026. Patrón ya conocido (`README.md`): hechos con aire de precisión, enlaces envueltos o genéricos ("Confirmado en portal municipal" con solo la portada), y rangos vagos ("15 o 20").

| Punto | Lo que dice el informe | Contraste | Veredicto |
| --- | --- | --- | --- |
| Marco legal | Ley 11.477 + Decreto reglamentario 3237/95 | El buscador devuelve el Decreto 3237/95 (que reglamenta la Ley 11.477) en `normas.gba.gob.ar`, con modificaciones de la Ley 12.481 y los Decretos 1366/01 y 442/10 | **Confirmado** (fuente a abrir: `normas.gba.gob.ar/documentos/V9A2qFPV.html`) |
| Licencia obligatoria | "Sí, es obligatoria" | El art. 21 del Decreto 3237/95 (texto del Decreto 1366/01) tiene un apartado "De las licencias para la Pesca Deportiva y Recreativa" | **Existe el régimen de licencias**; tipos y montos **NO CONFIRMADOS** |
| Autoridad | MDA, Dirección de Actividades Pesqueras y Acuicultura | Las disposiciones citan la Dirección Provincial de Pesca (Ministerio de Desarrollo Agrario) | **Dirección de Pesca del Ministerio de Desarrollo Agrario** (nombre exacto a confirmar al abrir su sitio) |
| URL de licencias `maa.gba.gov.ar/pesca/licencias/` | | No aparece en ninguna búsqueda; el dominio parece del ministerio anterior | **No se usa** |
| Arancel 2026 | NO CONFIRMADO | Una norma provincial con tasas en australes (A. 6,00 federada / A. 30,00 no federada) es histórica | **Sigue NO CONFIRMADO**; no se publica ningún monto |
| Resolución "N° 19/96" | | Sin respaldo | **No se cita** |
| Talla mínima **25 cm** | | El Decreto 2/56 (que reglamenta la Ley 5781) fijaba **28 cm** para aguas interiores bonaerenses y habilitaba al ministerio a modificarla por ambiente | **Conflicto 25 vs 28**: no se publica ninguna talla hasta ver la norma vigente |
| Cupo "25 por pescador", "15 o 20" | | La página oficial de vedas lista cupos **por laguna** (Chasicó 30; Cochicó y Del Monte 20; Cuero de Zorro y La Salada de Coronel Granada 15); Chascomús no figura en ese extracto | **No se publica un cupo general**; ver ronda 2 |
| Veda de pejerrey | 1/9 al 1/12; sáb, dom y feriados; cupo reducido; sin oleaje en juncales | La página oficial de vedas (`gba.gob.ar/desarrollo_agrario/DAPA/vedas`): **1/9 al 30/11**, toda la provincia, Disposiciones 483/2019 y 116/2021, pesca solo sáb, dom y feriados (algunas lagunas también viernes) | **Se publica con fecha y fuente**: veda 1/9-30/11 y días de pesca; "cupo reducido" y "oleaje en juncales" **no** (sin respaldo). Abrir la página antes de publicar |
| Artes (1 caña, 3 anzuelos, sin pesca nocturna a motor) | | Sin respaldo | **No se publican** |
| Accesos: costanera municipal, muelle público/mirador, zona del Club de Pesca, bajadas de lanchas, alquiler de botes, guardería náutica | | Notas de turismo de 2026 (canal26, welcomeargentina) nombran la zona del club, la costanera, el muelle público y las bajadas de lanchas | **Se nombran como "áreas"** sin costos ni horarios |
| "Club de Pescadores de Chascomús" (muelle de hormigón, canon por bajada, camping) y "Club de Regatas" | | El buscador **no encuentra** un club con ese nombre ni su sitio | **No se publica**: detalle sin fuente |
| Temporada otoño-invierno, viento 10-20 km/h, >30-35 km/h peligroso | | Plausible, pero sin fuente; las cifras de viento son del agente | **No se publican como hecho**; la guía muestra el viento real del día (Open-Meteo) |
| Fiestas y torneos | NO CONFIRMADO | | **Nada que publicar** |
| Prestadores | NO CONFIRMADO (sin sitios propios) | | **Ninguno** (consistente con `docs/prestadores.md`) |
| Encadenadas: Vitel, Adela, del Burro, Chis Chis, Tablillas, Las Barrancas; pesqueros "La Cascada", "Don Bartolo" | | Los nombres de lagunas son conocidos; los pesqueros y los detalles de acceso no tienen fuente | **Solo se nombran las lagunas** como contexto; ni pesqueros ni accesos |

**Qué alcanza hoy para una guía honesta (mínima):** régimen (Ley 11.477 y Decreto 3237/95), que existe licencia de pesca deportiva (valores: "consultalos en el sitio del Ministerio"), la **veda del pejerrey 1/9-30/11 con sus días de pesca** (con año y enlace a la página oficial), accesos nombrados como áreas, y el viento/lluvia del día. Sin talla, cupo, artes, horarios ni costos.

**Ronda 2 (pegar una por vez; "si no podés abrirlo, decí NO CONFIRMADO"):**
1. *Abrí https://www.gba.gob.ar/desarrollo_agrario/DAPA/vedas y transcribime textual la sección del pejerrey: período, días permitidos, y la tabla de cupos por laguna. ¿Aparecen Chascomús y las Encadenadas (Vitel, Adela, del Burro, Chis Chis, Tablillas, Las Barrancas)? Dame las Disposiciones citadas con su número y año.*
2. *¿Cuál es la medida mínima vigente del pejerrey en aguas públicas de la Provincia de Buenos Aires (25 o 28 cm)? Dame la norma vigente (número y año) que la fija y el enlace a su texto en normas.gba.gob.ar.*
3. *Abrí el texto actualizado del Decreto 3237/95 (normas.gba.gob.ar/documentos/V9A2qFPV.html), artículo 21, apartado "De las licencias para la Pesca Deportiva y Recreativa": ¿qué tipos de licencia hay y cuál es la tasa vigente de cada una? Y dame la página oficial donde se saca la licencia hoy.*
4. *En el sitio oficial de turismo o de la Municipalidad de Chascomús, ¿qué dicen de la pesca en la laguna (accesos, muelle, bajadas de lanchas)? Dame el enlace profundo y la fecha de la página.*

**Vigilancia:** cuando se confirme la página de vedas, sumarla a `vigilancia/fuentes.json` como `texto` (cambia cada año).

## 8. Ronda 2: respuesta recibida (27/10/2026) y auditoría

El dueño pegó las cuatro respuestas. Otra vez **ninguna URL se pudo abrir desde el sandbox**; se contrastó con resúmenes de buscador de las mismas páginas oficiales.

| Punto | Lo que dice el informe | Contraste | Veredicto |
| --- | --- | --- | --- |
| Veda pejerrey: período | 1/9 al 30/11, toda la provincia, Disposición 89/08 con renovación automática | La página oficial (`gba.gob.ar/desarrollo_agrario/DAPA/vedas`) dice lo mismo en el resumen de buscador. Una norma antigua cierra a las 00 h del 1/12 | **Se publica**: "1 de septiembre al 30 de noviembre" con enlace a la página oficial |
| Días de pesca en veda | sáb, dom y feriados (viernes también en Chasicó, Cochicó y Del Monte); sin torneos de pejerrey; sin pesca comercial | Coincide con el buscador (Disposiciones 483/2019 y 116/2021) | **Se publica** |
| Navegación en veda | sin motonáutica; fuera de borda a 15 km/h máx. | Solo en la transcripción; no apareció en el buscador | **Se publica con "según la página oficial"** solo si el dueño abre la página y lo ve (ver abajo) |
| **Cupo de Chascomús y "resto de las lagunas"** | **10 piezas** (cita textual "DIEZ (10) piezas para el resto de las lagunas bonaerenses") | El buscador devuelve **dos versiones**: notas viejas (infocampo, texto original de la Disp. 89/08) dicen 10, y la página oficial vigente, según su resumen, dice **"Hasta 15 ejemplares para el resto de las lagunas bonaerenses"** (Disp. 483/2019 y 116/2021) | **CONFLICTO 10 vs 15: no se publica ningún cupo.** El dueño abre la página y copia esa línea |
| Cupos de lagunas puntuales | Chasicó 30; Cochicó y Hinojo Grande 20; La Salada Grande, Cuero de Zorro, **De Gómez (Junín)**, La Brava, Salada de Coronel Granada, Sauce Grande, Mar Chiquita, Alsina 15; ríos, arroyos y canales 15 | Los 30/20/15 de Chasicó, Cochicó, Del Monte, Cuero de Zorro y Coronel Granada coinciden con el buscador | Útiles para la futura guía de Junín (De Gómez): **confirmar contra la página** |
| Medida mínima **25 cm** (Disposición 19/96) | | **No confirmada.** El buscador solo trae el Decreto 2/56 (28 cm; menciona también 25 cm para pesca deportiva y 50 piezas, que pudo haber cambiado) y una disposición reciente de **La Pampa** con 25 cm. Además el informe de la ronda 1 la llamó "Resolución 19/96" y ahora "Disposición 19/96": inconsistente. La cita a la "Disp. 217/2007" no se pudo abrir | **No se publica talla.** Pendiente: el dueño abre las páginas de normativa del MDA |
| Licencia: categorías | Federadas; no federadas: anual (1 año), turística (20 días), de concurso (3 días), costera menor marítima (1 año); exentos: menores de 14 y jubilados | El buscador confirma que el art. 21 (Decreto 1366/01 y 442/10) tiene el apartado "De las licencias para la Pesca Deportiva y Recreativa", pero el texto se corta ahí | **Plausible, sin abrir.** Se publica solo "existe licencia de pesca deportiva (Decreto 3237/95, art. 21)" y se enlaza el decreto actualizado. Las categorías, tras abrir `normas.gba.gob.ar/documentos/V9A2qFPV.html` |
| Valores | No figuran en el decreto; salen de la ley impositiva y resoluciones del MDA | Coherente con el hallazgo de la ronda 1 (tasas históricas en australes) | **No se publica ningún monto**: "consultá el valor vigente en el sitio del Ministerio" |
| Página del trámite | `gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva` y `.../pesca/pesca_deportiva`; el aplicativo en `maa.gba.gob.ar/pesca/licencias/` (en la ronda 1, `maa.gba.gov.ar`) | Las dos de `gba.gob.ar` tienen el patrón de la web oficial; la del aplicativo cambió de dominio entre respuestas | **El dueño abre las dos de `gba.gob.ar`** y dice cuál carga; el aplicativo no se enlaza |
| Chascomús turismo | `chascomus.gob.ar/turismo/nota/85/la-laguna-.html`: la mayor de las 7 Encadenadas, 3.044 ha, pesca deportiva y deportes acuáticos "sin motor", camino de 30 km, muelle de pescadores y mirador en la costanera | El buscador confirma 3.044 ha y los 30 km (otras fuentes dan ~3.000 y 3.012 ha); **"sin motor"** no lo confirma ninguna fuente | **Se publica**: mayor de las 7 Encadenadas, camino de circunvalación de 30 km, muelle y mirador en la costanera, "según el sitio municipal". **No** se publica "sin motor". La transcripción vino con texto mezclado: el dueño abre la página y confirma |
| Bajadas de lanchas | La ficha municipal no trae inventario ni régimen | Coincide con las notas de turismo (existen bajadas y guardería náutica) | **Sin inventario**: "preguntá en la costanera o a tu guía". Candidato para la futura sección de rampas |

**Lo que el dueño tiene que abrir (5 minutos):**
1. `https://www.gba.gob.ar/desarrollo_agrario/DAPA/vedas`: copiar **textual** la línea del cupo "para el resto de las lagunas bonaerenses" (¿10 o 15?) y si dice algo de la velocidad de navegación (15 km/h).
2. `https://www.gba.gob.ar/node/31890` y `https://www.gba.gob.ar/desarrollo_agrario/pesca/pesca_deportiva`: ¿hay una página con la **medida mínima** del pejerrey? Copiar lo que diga.
3. `https://www.gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva`: ¿abre? ¿qué valores muestra?
4. `https://chascomus.gob.ar/turismo/nota/85/la-laguna-.html`: ¿abre, y dice algo de motores?

**Mínima guía publicable hoy (con fuente y año):** Ley 11.477 y Decreto 3237/95; existe licencia (valor: ver el Ministerio); veda del pejerrey 1/9-30/11 con sus días de pesca; la laguna (3.044 ha, 30 km, muelle y mirador); viento y lluvia del día. Sin talla, cupo, categorías ni accesos con costo.

**Vigilancia:** cuando se confirme, sumar `gba.gob.ar/desarrollo_agrario/DAPA/vedas` a `vigilancia/fuentes.json` como `texto` (cambia por año y por disposición).

## 9. Lo que abrió el dueño (27/10/2026) y la guía publicable

- **Regla oficial del Ministerio de Desarrollo Agrario** (imagen enviada por el dueño: "Ayudanos a conservar el pejerrey. **Talla mínima de captura 25 cm.** Especie autóctona", con los logos del MDA y de la Provincia): **confirma 25 cm** (el 28 cm del Decreto 2/56 y el "Disposición/Resolución 19/96" del informe quedan descartados). Se publica citando "la regla oficial de medición del Ministerio (material de difusión)".
- **Veda**: el dueño pegó el texto con la **Disposición 89/08**, 1/9 al 30/11, sáb/dom/feriados, sin motonáutica y fuera de borda a 15 km/h. La línea "para el resto de las lagunas bonaerenses" figura como **10** en su pegado, pero un resumen de buscador de la página vigente dice **15**: puede haber dos textos en la misma página (el anuncio de 2008 y la tabla de las Disposiciones 483/2019 y 116/2021). **El cupo sigue sin publicarse**; pendiente: ver la tabla "Hasta N ejemplares".
- **Licencia** (`.../obtenga_su_licencia_de_pesca_deportiva`, abre): hay que tener licencia; **menores de 14 deben tramitarla, es gratuita** (el informe decía "exceptuados": estaba mal); jubilados sin cargo y sin vencimiento (art. 21 del Decreto 3237/95); sin jubilación, varones mayores de 65 y mujeres de 60 con residencia en PBA, licencia equivalente (Resolución 46/05); trámite en la Dirección Provincial de Pesca (calle 51 esq. 12, Torre I, piso 8, (221) 429-5315). **No muestra valores ni categorías**: se publican solo estos hechos.
- **`chascomus.gob.ar/turismo/nota/85/la-laguna-.html` redirige a `turismo.chascomus.gob.ar`**, un portal general: el texto citado por el informe no es verificable tal cual. Se publican 3.044 ha, el camino de 30 km y el muelle de pescadores/mirador **atribuidos al sitio de turismo de Chascomús** y a notas de prensa de 2026 (que coinciden). Pendiente: buscar en ese portal una sección de pesca y de accesos.
- **Rampas**: sin inventario. OpenStreetMap trae poquísimo y casi sin nombres (sonda del 27/10/2026: 0 en Goya, 1 en Esquina, 1 en Paso de la Patria, 1 en La Paz, 2 en Corrientes, 3 en Concordia, 1 en San Martín; Chascomús, Tigre y Junín dieron error 502 del servicio). No sirve como inventario. Ver `docs/rampas.md`.

**Guía publicada con esto:** `wordpress/scripts/chascomus-2026-10-27.php` (es + en, simulacro primero). Sin cupo, sin valores, sin categorías de licencia, sin clubes ni prestadores, sin "sin motor". Clave `chascomus` (hereda foto y pin de la tarjeta "próximamente"); `rio.json` suma `chascomus` solo-clima; el hub del río ya no lista guías solo-clima (San Martín y Junín dejan de salir en la tabla del Litoral).
**Pendientes:** cupo vigente, talla/cupo en otras disposiciones, ficha de la especie pejerrey, sección de accesos con administrador y costo, prestadores, pt.
