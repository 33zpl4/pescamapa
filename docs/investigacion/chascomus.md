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
