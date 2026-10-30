<?php
/**
 * Junín de los Andes and San Martín de los Andes (Neuquén), 30/10/2026: destination guide in Spanish (source of the shared data) and English
 * (the one the search plan targets). Facts, from docs/investigacion/patagonia-anexos-2026-27-fuentes-2026-10-30.md and lodges-lote1-dossiers:
 *  - 2026/27 annexes of the Patagonian regulation (Neuquén, Parques Nacionales), read in the AICACYP copies (third party; the official site does not answer).
 *  - Permit values: ANBariloche note of 16/10/2026 on the Neuquén announcement; Río Negro portal permisosdepesca.com.ar shows the same numbers.
 *  - Tipiliuke: its own pages (location, lodge, fly fishing, booking), read on 30/10/2026. San Huberto: its site answers 403 to our reader: listed
 *    with its domain only. Spring Creek has no confirmed domain: not listed.
 *  - Cross validity of permits: wording from the general regulation seen in a search excerpt (not in the 2026/27 PDF): worded "confirm when you buy".
 * Operators are "listado con información pública" (docs/prestadores.md). No rates, no photo (the owner supplies one with its credit).
 *
 * Idempotent: a guide that exists is not touched. Dry run first. Run with wp-script.yml (script = junin-san-martin-2026-10-30).
 */

$aplicar = true;
$hoy     = '20261030';

function pm_js_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_js_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_js_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_js_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'anb'  => 'https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027',
	'neu'  => 'https://cazaypesca.neuquen.gob.ar/permisos/',
	'annn' => 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Neuquen.pdf',
	'annp' => 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Parques-Nacionales.pdf',
	'rn'   => 'https://www.permisosdepesca.com.ar/',
	'perf' => 'https://weekend.perfil.com/noticias/pesca/temporada-de-pesca-2026-2027-en-la-patagonia-cuanto-costaran-las-licencias-y-que-cambios-habra.phtml',
	'tloc' => 'https://tipiliuke.com/location',
	'tfly' => 'https://tipiliuke.com/activities/fly-fishing',
	'tlod' => 'https://tipiliuke.com/the-lodge',
];
$fuentes_en = [
	[ 'ANBariloche: sale of 2026-2027 fishing permits starts (16/10/2026), values and rules announced by the Neuquén Fauna Directorate', $u['anb'], 0 ],
	[ 'Neuquén hunting and fishing portal: permits (where permits are bought; we could not open it)', $u['neu'], 1 ],
	[ 'Regulation 2026/27, list of environments of Neuquén (Annex), copy hosted by AICACYP (third party; the official site did not answer on 30/10/2026)', $u['annn'], 0 ],
	[ 'Regulation 2026/27, list of environments of the National Parks of the south (Annex), copy hosted by AICACYP (third party)', $u['annp'], 0 ],
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina: 2026/2027 permit values (Río Negro portal; same numbers)', $u['rn'], 1 ],
	[ 'Perfil Weekend: 2026-2027 season, permit values and changes decided by the Advisory Commission', $u['perf'], 0 ],
	[ 'Tipiliuke Lodge: location and how to get there (operator)', $u['tloc'], 0 ],
	[ 'Tipiliuke Lodge: fly fishing (operator)', $u['tfly'], 0 ],
	[ 'Tipiliuke Lodge: the lodge (operator)', $u['tlod'], 0 ],
];
$fuentes_es = [
	[ 'ANBariloche: arranca la venta de permisos de pesca 2026-2027 (16/10/2026), valores y reglas anunciados por la Dirección de Fauna de Neuquén', $u['anb'], 0 ],
	[ 'Portal de caza y pesca de Neuquén: permisos (donde se compran; no pudimos abrirlo)', $u['neu'], 1 ],
	[ 'Reglamento 2026/27, listado de ambientes de Neuquén (Anexo), copia alojada en AICACYP (terceros; el sitio oficial no respondió el 30/10/2026)', $u['annn'], 0 ],
	[ 'Reglamento 2026/27, listado de ambientes de los Parques Nacionales del sur (Anexo), copia alojada en AICACYP (terceros)', $u['annp'], 0 ],
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina: valores de permisos 2026/2027 (portal de Río Negro; mismos números)', $u['rn'], 1 ],
	[ 'Perfil Weekend: temporada 2026-2027, valores de permisos y cambios decididos por la Comisión Consultiva', $u['perf'], 0 ],
	[ 'Tipiliuke Lodge: ubicación y cómo llegar (operador)', $u['tloc'], 0 ],
	[ 'Tipiliuke Lodge: pesca con mosca (operador)', $u['tfly'], 0 ],
	[ 'Tipiliuke Lodge: el lodge (operador)', $u['tlod'], 0 ],
];
$filas = fn( array $f ) => array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $f );

// --- English -----------------------------------------------------------------------
$cuerpo_en = implode( "\n\n", [
	pm_js_p( '<strong>Junín de los Andes</strong> and <strong>San Martín de los Andes</strong>, in the south-west of Neuquén, are the historic base for fly fishing in northern Patagonia. Three rivers run close by: the <strong>Chimehuín</strong>, the <strong>Malleo</strong> and the <strong>Quilquihue</strong>, with the Collón Curá and the Aluminé a little further out. This page covers the 2026/27 rules, the permit and how people get there. It is a planning guide: confirm everything with your operator and the provincial authority before you travel.' ),
	pm_js_h( 'The permit and what it costs' ),
	pm_js_p( 'Fishing permits for the 2026/27 season are sold online on the Neuquén hunting and fishing portal. The season starts on <strong>1 November 2026</strong> and runs for a year, although some waters close on 1 May 2027. You need your ID, or your passport if you are a non-resident foreigner. Values announced by the provincial Fauna Directorate on 16 October 2026, in Argentine pesos:' ),
	pm_js_lista( [
		'<strong>Non-resident foreigners</strong>: one day AR$ 50,000; ten days AR$ 140,000; whole season AR$ 280,000. Residents of bordering countries: ten days AR$ 50,000.',
		'<strong>Argentine residents</strong>: one day AR$ 12,500; ten days AR$ 25,000; season AR$ 50,000.',
		'<strong>Trolling</strong> (an extra permit on top of the ordinary one): AR$ 25,000 per day, AR$ 50,000 for ten days, AR$ 100,000 for the season.',
		'<strong>Preferential zone</strong> (for example the mouth of the Chimehuín): an extra AR$ 12,500 per day, AR$ 25,000 for ten days or AR$ 50,000 for the season.',
	] ),
	pm_js_p( 'The Río Negro permit portal shows the same numbers. According to the general regulation (we saw this wording in an excerpt, not in the 2026/27 PDF), permits issued by Neuquén, Río Negro, Chubut and the National Parks in the standard categories and prices are valid across those jurisdictions, while additional permits (trolling, preferential zones) are valid only where they are issued. Confirm this when you buy. The provincial ministry also recommends hiring only guides and agencies that are authorised by the province.' ),
	pm_js_h( 'Rules by river, 2026/27' ),
	pm_js_p( 'These are the entries of the 2026/27 annex for Neuquén, which we read in a copy hosted by a third party because the official site did not answer. Dates, limits and zones can change: ask your operator which rules apply on the day.' ),
	pm_js_lista( [
		'<strong>Chimehuín</strong> (1 November 2026 to 31 May 2027). Zone I, from the outflow of the lake to 250 m downstream of the signed bridge, is a <strong>preferential zone with a compulsory additional permit</strong>: fly only, barbless hook, and every fish must be released. Zone II, from the end of Zone I to the confluence with the Collón Curá: fly, spinning and spinning with a fly-weight lure; bag limit of one perch; all salmonids released; single barbless hook.',
		'<strong>Malleo</strong> (1 November 2026 to 31 May 2027). From the National Park boundary to the Route 23 bridge: fly only and every fish released. From the Route 23 bridge to the Aluminé: fly or light spinning, one perch, all salmonids released, barbless hook. The part inside Lanín National Park is covered by the National Parks annex.',
		'<strong>Quilquihue</strong> (1 November 2026 to 31 May 2027). From the mouth to the end of the lagoon: fly or light spinning, two perch, all salmonids released, barbless hook. From the end of the lagoon to the Chimehuín: fly and spinning, single barbless hook.',
		'<strong>Collón Curá and Aluminé rivers</strong>: two perch, all salmonids released, single barbless hook, same season.',
		'<strong>Huechulafquen and Lolog lakes</strong> (inside Lanín National Park): bag limit of two salmonids; felt-soled wading boots are banned in the National Park waters we read.',
	] ),
	pm_js_p( 'In general, "release" in these entries means every salmonid goes back alive, and a single barbless hook is the norm. Where the annex says nothing about felt soles, as in the Neuquén entries, we found no ban, but the National Parks annex bans felt soles water by water: the safe choice is rubber soles. Always disinfect your gear against the Didymo alga.' ),
	pm_js_h( 'Getting there' ),
	pm_js_p( 'The nearest commercial airport is <strong>Chapelco</strong>, at San Martín de los Andes; the usual route from Buenos Aires is a flight of about two hours, according to Tipiliuke Lodge. Tipiliuke places itself 22 km from Junín de los Andes and 35 km from San Martín, and about three hours by road from Bariloche along the Seven Lakes Road. The last stretch to the lodge is a 6 km gravel road. These are the operator\'s own times: do not plan a tight connection.' ),
	pm_js_h( 'When to go' ),
	pm_js_p( 'The general season starts on 1 November 2026 and the annex entries above close on 31 May 2027. Tipiliuke describes its own fishing season as early November to May. The Neuquén tourism ministry mentions local events such as the Fiesta Nacional de la Trucha and the Fiesta Provincial de las Marrones Migratorias; we have no confirmed dates yet.' ),
	pm_js_pendiente( 'calendar by month and by river with a technical source; hatches; river levels and where to check them; photo with its credit; the Collón Curá and Aluminé section; guide list of the province.' ),
	pm_js_h( 'Lodges and outfitters' ),
	pm_js_p( 'Below are the ones we found with public information; we have not contacted them yet, which is why they are marked. Tipiliuke does not publish a rate: it asks you to write through its booking form. We show no prices because we have confirmed none with an operator.' ),
] );

// --- Spanish (source of the shared data) -------------------------------------------------
$cuerpo_es = implode( "\n\n", [
	pm_js_p( '<strong>Junín de los Andes</strong> y <strong>San Martín de los Andes</strong>, en el suroeste de Neuquén, son la base histórica de la pesca con mosca en el norte de la Patagonia. Cerca corren tres ríos: el <strong>Chimehuín</strong>, el <strong>Malleo</strong> y el <strong>Quilquihue</strong>, y un poco más lejos el Collón Curá y el Aluminé. Esta página reúne las reglas 2026/27, el permiso y cómo se llega. Es una guía para planificar: confirmá todo con el operador y con la autoridad provincial antes de viajar.' ),
	pm_js_h( 'El permiso y cuánto cuesta' ),
	pm_js_p( 'Los permisos de la temporada 2026/27 se compran online en el portal de caza y pesca de Neuquén. La temporada empieza el <strong>1 de noviembre de 2026</strong> y dura un año, aunque algunos ambientes cierran el 1 de mayo de 2027. Hace falta el DNI, o el pasaporte si sos extranjero no residente. Valores anunciados por la Dirección Provincial de Fauna el 16 de octubre de 2026, en pesos argentinos:' ),
	pm_js_lista( [
		'<strong>Extranjeros no residentes</strong>: un día $50.000; diez días $140.000; temporada $280.000. Residentes de países limítrofes: diez días $50.000.',
		'<strong>Residentes en el país</strong>: un día $12.500; diez días $25.000; temporada $50.000.',
		'<strong>Trolling</strong> (permiso adicional que se suma al ordinario): $25.000 por día, $50.000 por diez días, $100.000 por temporada.',
		'<strong>Zona preferencial</strong> (por ejemplo la boca del Chimehuín): adicional de $12.500 por día, $25.000 por diez días o $50.000 por temporada.',
	] ),
	pm_js_p( 'El portal de permisos de Río Negro muestra los mismos números. Según el reglamento general (vimos esa redacción en un extracto, no en el PDF 2026/27), los permisos que expiden Neuquén, Río Negro, Chubut y Parques Nacionales en las categorías y valores estándar valen en todas esas jurisdicciones, mientras que los adicionales (trolling, zonas preferenciales) valen solo donde se emiten. Confirmalo al comprar. El ministerio provincial recomienda además contratar solo guías y agencias habilitados por la provincia.' ),
	pm_js_h( 'Reglas por río, 2026/27' ),
	pm_js_p( 'Son las fichas del anexo 2026/27 de Neuquén, que leímos en una copia alojada por terceros porque el sitio oficial no respondió. Fechas, límites y zonas pueden cambiar: preguntale al operador qué reglas valen ese día.' ),
	pm_js_lista( [
		'<strong>Chimehuín</strong> (1 de noviembre de 2026 al 31 de mayo de 2027). La Zona I, desde la boca del lago hasta 250 m aguas abajo del puente señalizado, es <strong>zona preferencial con permiso adicional obligatorio</strong>: solo mosca, anzuelo sin rebaba y devolución de todos los peces. La Zona II, del final de la Zona I a la confluencia con el Collón Curá: mosca, spinning y spinning con señuelo mosca; límite de una perca; devolución de todos los salmónidos; anzuelo simple sin rebaba.',
		'<strong>Malleo</strong> (1 de noviembre de 2026 al 31 de mayo de 2027). Del límite con el Parque Nacional al puente de la ruta 23: solo mosca y devolución de todos los peces. Del puente de la ruta 23 al Aluminé: mosca o spinning liviano, una perca, devolución de salmónidos, anzuelo sin rebaba. El tramo dentro del Parque Nacional Lanín se rige por el anexo de Parques Nacionales.',
		'<strong>Quilquihue</strong> (1 de noviembre de 2026 al 31 de mayo de 2027). De la boca al final de la laguna: mosca o spinning liviano, dos percas, devolución de salmónidos, anzuelo sin rebaba. Del final de la laguna al Chimehuín: mosca y spinning, anzuelo simple sin rebaba.',
		'<strong>Ríos Collón Curá y Aluminé</strong>: dos percas, devolución de salmónidos, anzuelo simple sin rebaba, misma temporada.',
		'<strong>Lagos Huechulafquen y Lolog</strong> (dentro del Parque Nacional Lanín): límite de dos salmónidos; en las aguas del Parque que leímos se prohíben las botas de vadeo con suela de fieltro.',
	] ),
	pm_js_p( 'En general, "devolución" en estas fichas significa que todos los salmónidos vuelven vivos al agua, y el anzuelo simple sin rebaba es la norma. Donde el anexo no dice nada sobre el fieltro, como en las fichas de Neuquén, no encontramos una prohibición, pero el anexo de Parques Nacionales lo prohíbe ambiente por ambiente: lo prudente son las suelas de goma. Desinfectá siempre el equipo por el alga Didymo.' ),
	pm_js_h( 'Cómo llegar' ),
	pm_js_p( 'El aeropuerto comercial más cercano es <strong>Chapelco</strong>, en San Martín de los Andes; el trayecto habitual desde Buenos Aires es un vuelo de unas dos horas, según Tipiliuke Lodge. Tipiliuke se ubica a 22 km de Junín de los Andes y a 35 km de San Martín, y a unas tres horas por ruta de Bariloche por el Camino de los Siete Lagos. El último tramo al lodge es un camino de ripio de 6 km. Son los tiempos del propio operador: no armes conexiones ajustadas.' ),
	pm_js_h( 'Cuándo ir' ),
	pm_js_p( 'La temporada general empieza el 1 de noviembre de 2026 y las fichas del anexo cierran el 31 de mayo de 2027. Tipiliuke describe su propia temporada de pesca de principios de noviembre a mayo. El ministerio de turismo de Neuquén menciona fiestas locales como la Fiesta Nacional de la Trucha y la Fiesta Provincial de las Marrones Migratorias; todavía no tenemos fechas confirmadas.' ),
	pm_js_pendiente( 'calendario por mes y por río con una fuente técnica; eclosiones; alturas de los ríos y dónde consultarlas; foto con su crédito; tramo del Collón Curá y del Aluminé; listado de guías de la provincia.' ),
	pm_js_h( 'Lodges y operadores' ),
	pm_js_p( 'Abajo están los que encontramos con información pública; todavía no los contactamos, por eso figuran marcados. Tipiliuke no publica tarifa: pide escribir por su formulario de reservas. No mostramos precios porque no confirmamos ninguno con un operador.' ),
] );

$comun = [
	'clave'       => 'junin-de-los-andes',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'reglamento_autoridad' => 'Dirección Provincial de Fauna de Neuquén',
	'reglamento_url'       => 'https://cazaypesca.neuquen.gob.ar/permisos/',
	'reglamento_verificado' => $hoy,
];

$guias = [
	'es' => [
		'titulo' => 'Junín y San Martín de los Andes', 'slug' => 'pesca-en-junin-de-los-andes', 'cuerpo' => $cuerpo_es, 'fuentes' => $filas( $fuentes_es ),
		'campos' => [
			'h1'          => 'Pesca con mosca en Junín y San Martín de los Andes: ríos, reglas y permiso 2026/27',
			'bajada'      => 'Chimehuín, Malleo y Quilquihue en el suroeste de Neuquén. Las reglas por río de la temporada 2026/27, el permiso de no residente y cómo se llega.',
			'modalidades' => "Mosca (flycast)\nSpinning liviano\nFlotada en bote\nCaptura y devolución",
			'epoca'       => 'Del 1 de noviembre de 2026 al 31 de mayo de 2027 en los ríos; algunos lagos hasta octubre de 2027.',
			'seo_title'   => 'Pesca con mosca en Junín de los Andes: ríos y reglas',
			'seo_description' => 'Guía de pesca con mosca en Junín y San Martín de los Andes: reglas 2026/27 del Chimehuín, Malleo y Quilquihue, permisos de Neuquén y lodges.',
			'temporada'   => [
				[ 'meses' => 'Noviembre', 'que' => 'Apertura de la temporada 2026/27 (1 de noviembre de 2026).' ],
				[ 'meses' => 'Hasta mayo', 'que' => 'Cierre de los ríos del anexo el 31 de mayo de 2027; algunos ambientes cierran el 1 de mayo.' ],
				[ 'meses' => 'Calendario por mes', 'que' => 'PENDIENTE: eclosiones, alturas y mejores meses con una fuente técnica.' ],
			],
			'reglamento_resumen' => 'Reglamento General de Pesca Deportiva Continental Patagónico, temporada 2026/27. Anexo de ambientes de Neuquén y de Parques Nacionales leídos el 30/10/2026 en copias alojadas por terceros (AICACYP); el sitio oficial no respondió. Permisos en cazaypesca.neuquen.gob.ar; valores 2026/27 de la Dirección Provincial de Fauna (16/10/2026). La validez cruzada de permisos y las reglas generales (fieltro, Didymo) no se leyeron en el texto general 2026/27.',
			'aguas' => [
				[ 'nombre' => 'Río Chimehuín', 'nota' => 'Zona I preferencial con permiso adicional, solo mosca y devolución total. Zona II hasta el Collón Curá.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Malleo', 'nota' => 'Solo mosca y devolución arriba del puente de la ruta 23; mosca o spinning liviano abajo.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Quilquihue', 'nota' => 'Mosca o spinning liviano; devolución de salmónidos.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => '¿Qué permiso necesito en Junín de los Andes y cuánto cuesta?', 'respuesta' => 'El permiso de pesca de Neuquén, que se compra online en el portal de caza y pesca de la provincia. Para extranjeros no residentes en 2026/27: un día $50.000, diez días $140.000 y temporada $280.000 (pesos argentinos, anunciados el 16/10/2026). Confirmá el valor vigente antes de pagar.' ],
				[ 'pregunta' => '¿Cuándo empieza la temporada?', 'respuesta' => 'El 1 de noviembre de 2026. Los ríos del anexo que leímos cierran el 31 de mayo de 2027, y algunos ambientes cierran el 1 de mayo.' ],
				[ 'pregunta' => '¿Hace falta un permiso adicional para el Chimehuín?', 'respuesta' => 'Para la Zona I (de la boca del lago a 250 m aguas abajo del puente señalizado) sí: es zona preferencial con permiso adicional obligatorio, de $12.500 por día, $25.000 por diez días o $50.000 por temporada según los valores anunciados.' ],
				[ 'pregunta' => '¿Puedo llevarme los peces?', 'respuesta' => 'En la Zona I del Chimehuín, en el Malleo arriba del puente de la ruta 23 y en el resto de los ríos que leímos se devuelven todos los salmónidos. Solo se retienen las percas dentro del límite diario de cada tramo. Revisá la ficha del tramo antes de pescar.' ],
				[ 'pregunta' => '¿Qué anzuelo y qué modalidad se permiten?', 'respuesta' => 'Anzuelo simple sin rebaba en los tramos que leímos. En la Zona I del Chimehuín y arriba del puente de la ruta 23 en el Malleo, solo mosca; en otros tramos, mosca y spinning liviano con señuelo mosca.' ],
				[ 'pregunta' => '¿Sirven los permisos de otra provincia patagónica?', 'respuesta' => 'Según el reglamento general, los permisos ordinarios de Neuquén, Río Negro, Chubut y Parques Nacionales en las mismas categorías y valores valen en todas esas jurisdicciones, y los adicionales solo donde se emiten. Vimos esa redacción en un extracto y no en el PDF 2026/27: confirmalo al comprar.' ],
				[ 'pregunta' => '¿Puedo usar botas de vadeo con suela de fieltro?', 'respuesta' => 'En las aguas de Parques Nacionales que leímos (por ejemplo Huechulafquen y Lolog) están prohibidas. En las fichas de Neuquén no vimos una prohibición, pero lo prudente son las suelas de goma. Desinfectá el equipo por el alga Didymo.' ],
				[ 'pregunta' => '¿Cómo llego desde Buenos Aires?', 'respuesta' => 'El aeropuerto comercial más cercano es Chapelco, en San Martín de los Andes, a unas dos horas de vuelo desde Buenos Aires según Tipiliuke Lodge. Desde Bariloche son unas tres horas por ruta, según el mismo operador.' ],
				[ 'pregunta' => '¿Cuánto cuesta un viaje con lodge?', 'respuesta' => 'Tipiliuke no publica tarifa y pide escribir por su formulario de reservas. No tenemos una tarifa vigente confirmada con ningún operador, por eso no publicamos ninguna.' ],
				[ 'pregunta' => '¿Dónde busco guías habilitados?', 'respuesta' => 'El ministerio de turismo de Neuquén publica un listado de prestadores de pesca habilitados y recomienda contratar solo a esos guías y agencias.' ],
			],
		],
	],
	'en' => [
		'titulo' => 'Junín and San Martín de los Andes', 'slug' => 'fishing-in-junin-de-los-andes', 'cuerpo' => $cuerpo_en, 'fuentes' => $filas( $fuentes_en ),
		'campos' => [
			'h1'          => 'Fly fishing in Junín and San Martín de los Andes: rivers, rules and 2026/27 permit',
			'bajada'      => 'Chimehuín, Malleo and Quilquihue in south-west Neuquén. The 2026/27 rules by river, the non-resident permit and how people get there.',
			'modalidades' => "Fly fishing\nLight spinning\nDrift boat floats\nCatch and release",
			'epoca'       => '1 November 2026 to 31 May 2027 on the rivers; some lakes until October 2027.',
			'seo_title'   => 'Fly fishing Junín de los Andes: rivers, rules, permit',
			'seo_description' => 'Fly fishing guide to Junín and San Martín de los Andes: 2026/27 rules for the Chimehuín, Malleo and Quilquihue, Neuquén permit prices and lodges.',
			'temporada'   => [
				[ 'meses' => 'November', 'que' => '2026/27 season opens (1 November 2026).' ],
				[ 'meses' => 'Until May', 'que' => 'Rivers in the annex close on 31 May 2027; some waters close on 1 May.' ],
				[ 'meses' => 'Calendar by month', 'que' => 'PENDIENTE: hatches, river levels and best months, with a technical source.' ],
			],
			'reglamento_resumen' => 'General Regulation of Continental Sport Fishing of Patagonia, 2026/27 season. Annexes for the environments of Neuquén and of the National Parks read on 30/10/2026 in copies hosted by a third party (AICACYP); the official site did not answer. Permits at cazaypesca.neuquen.gob.ar; 2026/27 values from the Provincial Fauna Directorate (16/10/2026). Cross validity of permits and the general rules (felt soles, Didymo) were not read in the general 2026/27 text.',
			'aguas' => [
				[ 'nombre' => 'Chimehuín River', 'nota' => 'Zone I is a preferential zone with an additional permit, fly only and full release. Zone II runs to the Collón Curá.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Malleo River', 'nota' => 'Fly only and full release above the Route 23 bridge; fly or light spinning below.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Quilquihue River', 'nota' => 'Fly or light spinning; salmonids released.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => 'Which permit do I need in Junín de los Andes and how much is it?', 'respuesta' => 'The Neuquén fishing permit, bought online on the province\'s hunting and fishing portal. For non-resident foreigners in 2026/27: one day AR$ 50,000, ten days AR$ 140,000, season AR$ 280,000 (Argentine pesos, announced on 16 October 2026). Confirm the current amount before you pay.' ],
				[ 'pregunta' => 'When does the season start?', 'respuesta' => 'On 1 November 2026. The rivers in the annex we read close on 31 May 2027, and some waters close on 1 May.' ],
				[ 'pregunta' => 'Is an additional permit needed for the Chimehuín?', 'respuesta' => 'For Zone I (from the outflow of the lake to 250 m downstream of the signed bridge), yes: it is a preferential zone with a compulsory additional permit, AR$ 12,500 per day, AR$ 25,000 for ten days or AR$ 50,000 for the season according to the announced values.' ],
				[ 'pregunta' => 'Can I keep the fish I catch?', 'respuesta' => 'In Zone I of the Chimehuín, on the Malleo above the Route 23 bridge and on the other rivers we read, all salmonids are released. Only perch can be kept, within the daily limit of each stretch. Check the stretch you will fish before you go.' ],
				[ 'pregunta' => 'What hook and method are allowed?', 'respuesta' => 'A single barbless hook on the stretches we read. Fly only in Zone I of the Chimehuín and on the Malleo above the Route 23 bridge; elsewhere, fly and light spinning with a fly-weight lure.' ],
				[ 'pregunta' => 'Is a permit from another Patagonian province valid?', 'respuesta' => 'According to the general regulation, ordinary permits from Neuquén, Río Negro, Chubut and the National Parks in the same categories and prices are valid across those jurisdictions, and additional permits only where they are issued. We saw that wording in an excerpt, not in the 2026/27 PDF: confirm it when you buy.' ],
				[ 'pregunta' => 'Can I wear felt-soled wading boots?', 'respuesta' => 'Not in the National Park waters we read (for example Huechulafquen and Lolog). We found no ban in the Neuquén entries, but rubber soles are the safe choice. Disinfect your gear against the Didymo alga.' ],
				[ 'pregunta' => 'How do I get there from Buenos Aires?', 'respuesta' => 'The nearest commercial airport is Chapelco, at San Martín de los Andes, about two hours by air from Buenos Aires according to Tipiliuke Lodge. From Bariloche it is about three hours by road, according to the same operator.' ],
				[ 'pregunta' => 'How much does a lodge trip cost?', 'respuesta' => 'Tipiliuke publishes no rate and asks you to write through its booking form. We have no current rate confirmed with any operator, so we publish none.' ],
				[ 'pregunta' => 'Where do I find authorised guides?', 'respuesta' => 'The Neuquén tourism ministry publishes a list of authorised fishing providers and recommends hiring only those guides and agencies.' ],
			],
		],
	],
];

foreach ( $guias as $i => $g ) {
	$t = mb_strlen( $g['campos']['seo_title'] );
	$d = mb_strlen( $g['campos']['seo_description'] );
	if ( $t > 60 || $d > 160 || $d < 70 ) {
		WP_CLI::error( "SEO fuera de rango ($i): title $t, description $d" );
	}
}

// --- Create (Spanish first) -------------------------------------------------------------
foreach ( [ 'es', 'en' ] as $i ) {
	$g = $guias[ $i ];
	if ( pm_buscar( 'pm_destino', 'junin-de-los-andes', $i ) ) {
		WP_CLI::log( "junin-de-los-andes ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "junin-de-los-andes ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $g['fuentes'] ) . ' fuentes (title ' . mb_strlen( $g['campos']['seo_title'] ) . ', desc ' . mb_strlen( $g['campos']['seo_description'] ) . ')' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $g['cuerpo'] ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i, 'fuentes' => $g['fuentes'] ];
	if ( 'es' === $i ) {
		$campos += [ 'region' => 'patagonia', 'provincia' => 'neuquen', 'lat' => -39.95, 'lon' => -71.07, 'especies' => [ 'trucha' ] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "✓ junin-de-los-andes ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
}

// --- Outfitters: "listado con información pública" (docs/prestadores.md) ----------------------
$prestadores = [
	[ 'Tipiliuke Lodge', [ 'alojamiento' ], 'https://tipiliuke.com/', 'Lodge in the lower Chimehuín valley, ten minutes from Chapelco airport, with nine rooms and fly fishing on the Chimehuín and Quilquihue (operator\'s description). It publishes no rate: it asks you to write through its booking form.', 'Junín de los Andes, Neuquén' ],
	[ 'Hostería San Huberto', [ 'alojamiento' ], 'https://olsenfamily.com.ar/', 'Lodge in the Junín de los Andes area. Its website (olsenfamily.com.ar) did not answer our reader on 30/10/2026, so nothing else is confirmed: no river, rate or capacity.', 'Junín de los Andes, Neuquén' ],
];
foreach ( $prestadores as [ $nombre, $tipo, $url, $desc, $loc ] ) {
	if ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] ) ) {
		WP_CLI::log( "· prestador $nombre ya existe" );
		continue;
	}
	if ( ! $aplicar ) {
		WP_CLI::log( "· prestador $nombre: se crearía (listado público, sin verificar)" );
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::warning( $pid->get_error_message() );
		continue;
	}
	$f = [
		'tipo' => $tipo, 'prest_destino' => 'junin-de-los-andes', 'localidad' => $loc, 'descripcion' => $desc,
		'prest_modalidades' => [ 'mosca' ], 'lancha' => '', 'motor' => '',
		'prest_publico' => 1, 'prest_fuente' => $url, 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0,
		'notas' => 'Listado con información pública (30/10/2026). Pescamapa no confirmó contacto, tarifa, idiomas ni disponibilidad. Falta contactarlo.',
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	WP_CLI::log( "✓ prestador $nombre #$pid (listado público, sin verificar)" );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
