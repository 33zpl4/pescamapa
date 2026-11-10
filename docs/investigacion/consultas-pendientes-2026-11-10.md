# Consultas pendientes para completar el sitio (10/11/2026)

Circuito de `docs/investigacion/README.md`: Claude redacta, el dueño pega en Gemini o ChatGPT (con búsqueda web) y devuelve la respuesta, Claude audita y guarda. Estas son preguntas para un asistente externo del dueño, no llamadas a la API (regla del 6/11: las APIs solo para consultas quirúrgicas a pedido).

**Qué páginas se sirven de cada ronda y por qué importa** salió del análisis de Search Console del 10/11 (`scripts/gsc-consultas.mjs`) y de `docs/revision-negocio-2026-11-09.md` (los dos productos que pagan: semanas de lodge para extranjeros, en inglés, y grupos al dorado, sobre todo brasileños, en portugués; Goya es donde primero se puede cerrar un viaje).

## Orden sugerido (por valor para el negocio)

1. R1 Tarifas de lodges 2026/27 (comparador, silo A). Lo que se puede leer en la web del operador lo lee Claude gratis con `investigar.yml`; el asistente solo ubica las páginas.
2. R2 Brasil a Corrientes: aduana, pagos, ruta (producto en portugués).
3. R3 Goya: entrevistas a guías y condiciones (primer viaje cerrable).
4. R4 Truchas de la Patagonia (búsquedas "rainbow/brook/brown trout patagonia", ya con impresiones).
5. R5 Licencias de las demás provincias (completa el hub, que ya rankea "argentina fishing license").
6. R6 Dorado: récord, tamaños, reglas.
7. R7 Eventos de fecha fija (Fiesta del Surubí, del Dorado).
8. R8 Desde EE. UU.: equipaje de cañas, aduana de equipo, pagos.
9. R9 Temporada por zona con fuente técnica y caudales de la Patagonia.
10. R10 Parques Nacionales (Los Alerces, Lago Puelo): lo abre el dueño.

## Encabezado que va delante de TODAS las preguntas

> Actuá como investigador. Respondé solo con lo que leíste en una página que abriste. Para cada dato dame: el valor, la URL exacta que abre, la fecha de esa fuente y si es norma u organismo oficial, el propio operador, o prensa/blog. Si no lo encontrás, escribí NO CONFIRMADO; no estimes ni completes con lo que "suele ser". Separá lo que leíste de lo que inferís. Si dos fuentes difieren, mostrá las dos. No uses enlaces de google.com/search ni direcciones que no abriste.

## R1. Tarifas de lodges de pesca con mosca en la Patagonia, temporada 2026/27 (silo A)

Para: comparador de lodges con tarifa fechada. Pegar en: ChatGPT con búsqueda o Gemini. Después: Claude lee las páginas que se devuelvan con `investigar.yml`.

1. Para cada uno de estos lodges, ¿cuál es la URL de la página propia donde publican tarifas o paquetes 2026/27 o 2027: Río Manso (Río Negro), Kau Tapen (Tierra del Fuego), Limay Lodge, San Huberto, La Pilarica, AHO Fishing, Las Pampas Lodge, Challhuaquen, Jurassic Lake (Laguna Verde / Estancia)? Solo la página del operador, no agencias.
2. ¿Qué lodges de Patagonia norte (Neuquén, Río Negro, Chubut) publican una tarifa por semana con fechas 2026/27 que cualquiera pueda leer? Listá nombre, URL y la moneda.
3. De los que publican tarifa: ¿qué incluyen y qué cobran aparte (licencia, traslados desde el aeropuerto, bebidas, propinas, habitación individual, acompañante que no pesca, día de llegada o salida)?
4. ¿Qué condiciones de reserva publican: seña, plazo de pago del saldo, cancelación y reprogramación?
5. ¿Hay lodges de Patagonia que publiquen ahora una tarifa distinta por mes (temporada alta y baja)? Mostrá las dos tarifas y las fechas.
6. ¿Qué lodges dicen explícitamente en su web que trabajan con agentes o agencias y pagan comisión? Citá la frase.

## R2. Pesca desde Brasil a Corrientes (producto en portugués)

Para: completar la nota "pesca desde Brasil" y un paquete de dorado para grupos. Pegar en: ChatGPT/Gemini. Pedir las respuestas en portugués solo si ayuda; las fuentes son oficiales de Argentina y Brasil.

1. Aduana argentina: ¿qué dice la norma para un turista brasileño que ingresa en auto con cañas, reeles, equipo y combustible? ¿Hay que declarar el equipo? Fuente oficial (ARCA/Aduana).
2. Documentación del vehículo: ¿qué exige Argentina a un auto brasileño en el paso Uruguaiana–Paso de los Libres y en São Borja–Santo Tomé (permiso de circulación, seguro Mercosur/Carta Verde, licencia)? Enlace oficial.
3. Migraciones: ¿puede un brasileño entrar solo con el RG? Dame el texto oficial de Migraciones Argentina y la fecha de la fuente.
4. Pagos: ¿qué medios de pago aceptan los prestadores turísticos de Corrientes con tarjeta brasileña? ¿Pix, tarjeta internacional, reales o dólares en efectivo? (Solo lo que diga un organismo o un prestador en su web.)
5. Ruta a Goya: distancia y tiempo en auto desde Uruguaiana/Paso de los Libres, y desde Santo Tomé, con el camino (rutas). Si no encontrás una fuente de distancia, NO CONFIRMADO.
6. Paso de los Libres–Uruguaiana: horario del paso y el trámite; ¿hay espera típica documentada por la Prefectura/Gendarmería?
7. ¿Qué agencias o peñas de Brasil organizan viajes de pesca de dorado a Corrientes y publican precio en reales con fecha? Listá nombre, URL, precio y qué incluye.

## R3. Goya: entrevista a guías de pesca y condiciones (primer viaje cerrable)

Para: página de paquetes de Goya con precio, fotos y condiciones (hoy "lo cotizamos"). No es investigación en la web: lo responde cada guía o alojamiento. Reglas de consentimiento en `docs/contenido.md` (pedir permiso por escrito para citar y para las fotos).

Bloque A, para publicar (se cita con permiso):
1. Nombre, desde cuándo pesca y de qué río o zona sale (Paraná frente a Goya, riachos, Isoró).
2. Qué especies pesca y en qué meses es mejor cada una.
3. Cómo es un día típico: hora de salida, duración, almuerzo, dónde se pesca.
4. Qué trae el cliente y qué pone usted (cañas, carnada, señuelos, chaleco, hielo).
5. Cuántas personas por lancha y qué pasa con grupos de 3 o 4.
6. Cómo cambia la pesca con la altura del río (creciente, bajante). ¿Qué hace con el cliente si el río no está bueno?
7. Qué se equivoca más el visitante extranjero o brasileño.
8. Qué le gustaría que supiera un pescador antes de venir.

Bloque B, comercial (lo trata solo el dueño; no se publica tal cual):
1. Precio de una jornada de dorado por lancha (1 a 3 pescadores), con fecha, y qué incluye y qué no.
2. Idiomas que habla el guía o el alojamiento.
3. Seña, forma de pago, cancelación por río o clima.
4. Si trabajaría con un agente que le trae clientes: ¿qué condición le parece razonable?
5. Fotos propias que pueda ceder (lancha, pesca, alojamiento) y cómo quiere que se cite el crédito.
6. Alojamientos de confianza donde manda a sus clientes y a cuánto está la noche.

## R4. Truchas de la Patagonia (arcoíris, marrón, fontinalis)

Para: ficha `/en/species/trout/` y `/especies/trucha/`. Hoy la sección es un párrafo y cita el reglamento 2025/26. Búsquedas que ya tienen impresiones: "rainbow trout patagonia", "patagonia brook trout", "brown trout patagonia", "tipos de truchas en la patagonia".

1. ¿Cuándo y cómo se introdujeron en la Patagonia argentina la trucha arcoíris, la marrón y la fontinalis? Fuente científica o de un organismo de fauna.
2. Para cada una: ¿en qué cuencas, ríos y lagos principales de Neuquén, Río Negro, Chubut y Santa Cruz se la encuentra según una fuente de fauna provincial, Parques Nacionales o un paper?
3. La fontinalis: ¿dónde es abundante y cuál es el tamaño típico? (Hoy decimos "algunos lagos y ríos del centro de la región", sin fuente.)
4. Tamaños típicos y récords por especie en la Patagonia argentina, con la fuente del récord (provincial o IGFA).
5. ¿Cambian el cupo, la medida mínima o la modalidad según la especie en el Reglamento Patagónico 2026/27? Cita el artículo.
6. Ciclo biológico: ¿cuándo desova cada especie en la Patagonia y cómo afecta la temporada y el cierre de ríos? Fuente técnica.
7. ¿Qué otras especies de salmónidos se pescan (salmón encerrado, trucha lacustre, trucha de arroyo)? Solo con fuente.

## R5. Licencias de las demás provincias (completa el hub)

Para: el hub "Licencia de pesca para extranjeros en Argentina 2026/27". Una pregunta por provincia, siempre la norma y el enlace donde se tramita.

1. Entre Ríos: arancel 2026 de la licencia para extranjeros no residentes (día, semana, anual), norma que lo fija y dónde se tramita.
2. Santa Fe: lo mismo.
3. Chaco: lo mismo.
4. Misiones: lo mismo.
5. Buenos Aires: lo mismo (licencia de pesca continental, extranjeros).
6. Chubut: ¿existe un permiso de 10 días para extranjeros de países limítrofes como el de Río Negro? Mostrá la página oficial de venta o la norma.
7. Acuerdo regional de reconocimiento de licencias del Litoral: texto oficial, provincias firmantes y fecha. Si no hay texto, NO CONFIRMADO.

## R6. Dorado: récord, tamaños y reglas (ficha en inglés, español y portugués)

1. Récord mundial de dorado (Salminus brasiliensis): peso, fecha, lugar y enlace a la página oficial de IGFA, para línea (all-tackle) y para mosca.
2. Peso y largo típicos en el Paraná medio, según una fuente científica o de Recursos Naturales.
3. Rango de la especie: ríos y cuencas donde se encuentra y el límite sur.
4. Corrientes: medida mínima, cupo y devolución del dorado según la disposición vigente 2026/27 (número y artículo).
5. Entre Ríos: lo mismo, con su norma.
6. Cómo cambia la actividad del dorado con la altura del río, según un organismo o un operador con nombre.
7. Nombres comunes: dorado, dourado, "golden dorado", y si "river tiger" se usa en pesca deportiva.

## R7. Eventos de fecha fija con demanda (Goya y Paso de la Patria)

Para: paquetes por evento, publicados con meses de anticipación.

1. Fiesta Nacional del Surubí (Goya): fecha 2026 y fecha anunciada para 2027, organizador, reglamento, categorías, inscripción y cupo de embarcaciones.
2. Fiesta Nacional del Dorado (Paso de la Patria): lo mismo.
3. ¿Qué otros torneos de pesca de más de 100 embarcaciones hay en Corrientes y Entre Ríos y en qué mes?
4. Para cada uno: ¿hay un cupo o límite de plazas de alojamiento en la ciudad esa semana, según el municipio o la cámara de turismo?

## R8. Viajar a pescar desde EE. UU. (solo lo que podemos agregar con datos de Argentina)

Para: hub de origen. Visa, seguro y trámites de entrada se enlazan a la fuente oficial, no se escriben (decisión del 8/11).

1. Entrada: URL oficial de la Dirección Nacional de Migraciones y del Departamento de Estado para ciudadanos de EE. UU. Solo el enlace y la fecha de la página.
2. Equipaje de cañas: ¿qué reglas publican Aerolíneas Argentinas, Flybondi y JetSMART para tubos de cañas y equipo en vuelos internos (peso, medidas, tarifa)? Enlace a cada página oficial.
3. Aduana: ¿qué dice la norma sobre entrar con cañas, reeles y waders? ¿Hay algo sobre botas con suela de fieltro o equipo usado (SENASA, Didymo)?
4. Pagos: ¿qué tipo de cambio usa una tarjeta extranjera en Argentina hoy y qué dice el Banco Central o un organismo oficial para el turista?
5. Aeropuertos por zona de pesca: ¿qué aeropuertos sirven a Bariloche, San Martín de los Andes, Esquel, Río Grande, Río Gallegos y Corrientes? Con la línea que opera (Aerolíneas, Flybondi, JetSMART) y si hay vuelo directo desde Buenos Aires.

## R9. Temporada por zona con fuente técnica y caudales de la Patagonia

1. ¿Qué organismo publica caudales o alturas de los ríos Limay, Collón Curá, Chimehuín, Malleo, Futaleufú y Santa Cruz, con serie diaria o horaria y un enlace público? (Autoridad Interjurisdiccional de las Cuencas, Hidrología de Neuquén, SNIH.)
2. ¿Hay temperatura del agua publicada para esos ríos?
3. ¿Qué fuente técnica (no un blog) describe la eclosión de insectos y los mejores meses por río en Patagonia norte?
4. Fechas de apertura y cierre de la temporada 2026/27 de cada provincia, con la norma (para comparar con lo que ya publicamos).
5. ¿Cuál es el mejor momento para pescar en noviembre frente a marzo en cada zona, según la guía o el lodge, citando quién lo dice y la fecha?

## R10. Parques Nacionales: Los Alerces y Lago Puelo (lo abre el dueño)

Los runners reciben 403 en `reglamentodepesca.org.ar`; lo abre el dueño o pasa el PDF.

1. Anexo "ambientes Parques Nacionales del Sur" 2026/27: reglas y fechas de los lagos Futalaufquen, Verde, Menéndez y Arrayanes (modalidad, cupo, medida, devolución).
2. Lago Puelo: reglas del anexo del Parque.
3. ¿El permiso del Parque es el patagónico o hay un cargo extra para extranjeros (entrada al Parque, pesca)?
4. ¿Hay ambientes cerrados o con cupo en 2026/27?

Cada respuesta se audita con el checklist de `docs/investigacion/README.md` (quién lo dice, URL abierta, año, jurisdicción, segunda fuente, qué decimos si falla) antes de usarla en una página. Lo que no se confirma queda como "no lo podemos confirmar; preguntale a tu guía".
