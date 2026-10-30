<?php
/**
 * Bariloche and Nahuel Huapi (Río Negro), 30/10/2026: destination guide in Spanish (source of the shared data) and English.
 * Published with what is read; what is missing is marked, not hidden (owner, 30/10/2026: "publish with what you have"). Facts:
 *  - Permit values and issuer: Mesa Directiva de Pesca Deportiva Zona Andina portal (permisosdepesca.com.ar), read on 30/10/2026.
 *  - Annex Río Negro (general points: validity in four jurisdictions, daylight, live fish, felt soles): text copied by the owner from the
 *    regulation site (docs/investigacion/fuentes/anexo-rio-negro-2026-27-copiado-2026-10-30.md).
 *  - Environments inside Nahuel Huapi National Park (lake, Manso, Traful, Limay section, Mascardi): 2026/27 annex read in the AICACYP copy (third party).
 *  - NOT read: the list of Río Negro environments (Limay in provincial jurisdiction, Ñirihuau, Pichileufu): the guide says so.
 *  - Lodges: their own sites (Rio Manso Lodge rates and FAQ, Limay River Lodge history). A rate is shown only with its season and source.
 * Idempotent: a guide that exists is not touched. Dry run first. Run with wp-script.yml (script = bariloche-2026-10-30).
 */

$aplicar = true;
$hoy     = '20261030';

function pm_bq_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_bq_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_bq_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_bq_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'rn'   => 'https://www.permisosdepesca.com.ar/',
	'anrn' => 'https://reglamentodepesca.org.ar/reglamento-items/anexo-rio-negro/',
	'annc' => 'https://reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/',
	'annp' => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/',
	'annpc' => 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Parques-Nacionales.pdf',
	'annn' => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-neuquen/',
	'annnc' => 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Neuquen.pdf',
	'perf' => 'https://weekend.perfil.com/noticias/pesca/temporada-de-pesca-2026-2027-en-la-patagonia-cuanto-costaran-las-licencias-y-que-cambios-habra.phtml',
	'rml'  => 'https://www.riomansolodge.com/lodge-rates-details/',
	'rmf'  => 'https://www.riomansolodge.com/faqs/',
	'lrl'  => 'https://limaylodge.com/about-us/',
	'tloc' => 'https://tipiliuke.com/location',
];
$fuentes_en = [
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina (Bariloche): 2026/2027 permit values and online sale', $u['rn'], 1 ],
	[ 'Regulation of Continental Sport Fishing of Patagonia (official site): Río Negro annex, general points, read on 30/10/2026', $u['anrn'], 1 ],
	[ 'Regulation of Continental Sport Fishing of Patagonia (official site): Neuquén annex, general points, read on 30/10/2026', $u['annc'], 1 ],
	[ 'Regulation 2026/27 (official site): list of environments of the National Parks of the south; we read the entries in a copy hosted by AICACYP (third party), because our reader cannot open the official site', $u['annp'], 1 ],
	[ 'Regulation 2026/27 (official site): list of environments of Neuquén (Limay below the dams); read in the same AICACYP copy', $u['annn'], 1 ],
	[ 'Perfil Weekend: 2026-2027 season, trolling permits and changes decided by the Advisory Commission', $u['perf'], 0 ],
	[ 'Rio Manso Lodge: 2026-27 rates and details (operator)', $u['rml'], 0 ],
	[ 'Rio Manso Lodge: frequently asked questions (operator)', $u['rmf'], 0 ],
	[ 'Limay River Lodge: about us (operator)', $u['lrl'], 0 ],
	[ 'Tipiliuke Lodge: location, with the flight from Buenos Aires to Bariloche (operator)', $u['tloc'], 0 ],
];
$fuentes_es = [
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina (Bariloche): valores 2026/2027 y venta online de permisos', $u['rn'], 1 ],
	[ 'Reglamento de Pesca Deportiva Continental Patagónico (sitio oficial): Anexo Río Negro, puntos generales, leído el 30/10/2026', $u['anrn'], 1 ],
	[ 'Reglamento de Pesca Deportiva Continental Patagónico (sitio oficial): Anexo Neuquén, puntos generales, leído el 30/10/2026', $u['annc'], 1 ],
	[ 'Reglamento 2026/27 (sitio oficial): listado de ambientes de los Parques Nacionales del sur; leímos las fichas en una copia alojada en AICACYP (terceros), porque nuestro lector no puede abrir el sitio oficial', $u['annp'], 1 ],
	[ 'Reglamento 2026/27 (sitio oficial): listado de ambientes de Neuquén (Limay aguas abajo de los diques); leído en la misma copia de AICACYP', $u['annn'], 1 ],
	[ 'Perfil Weekend: temporada 2026-2027, permisos de trolling y cambios decididos por la Comisión Consultiva', $u['perf'], 0 ],
	[ 'Rio Manso Lodge: tarifas y detalles 2026-27 (operador)', $u['rml'], 0 ],
	[ 'Rio Manso Lodge: preguntas frecuentes (operador)', $u['rmf'], 0 ],
	[ 'Limay River Lodge: quiénes somos (operador)', $u['lrl'], 0 ],
	[ 'Tipiliuke Lodge: ubicación, con el vuelo de Buenos Aires a Bariloche (operador)', $u['tloc'], 0 ],
];
$filas = fn( array $f ) => array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $f );

// --- English -----------------------------------------------------------------------
$cuerpo_en = implode( "\n\n", [
	pm_bq_p( '<strong>San Carlos de Bariloche</strong> is the base for fishing on <strong>Lake Nahuel Huapi</strong> and the rivers of the Nahuel Huapi National Park: the <strong>Manso</strong>, the <strong>Traful</strong> and the upper <strong>Limay</strong>, with the Limay below the dams a longer drive away. This page covers the 2026/27 rules we have read, the permit and what we still have not read. It is a planning guide: confirm everything with your guide and the authority before you travel.' ),
	pm_bq_h( 'The permit and what it costs' ),
	pm_bq_p( 'The permit for Río Negro is issued by the <strong>Mesa Directiva de Pesca Deportiva Zona Andina</strong> (Av. 12 de Octubre and Onelli, Bariloche), which sells it online. The 2026/27 season starts on <strong>1 November 2026</strong>. Values on its portal, in Argentine pesos and with VAT:' ),
	pm_bq_lista( [
		'<strong>Non-resident foreigners</strong>: one day AR$ 50,000; ten days AR$ 140,000; whole season AR$ 280,000. Residents of bordering countries: ten days AR$ 50,000.',
		'<strong>Argentine residents</strong>: one day AR$ 12,500; ten days AR$ 25,000; season AR$ 50,000; ages 13 to 17, season AR$ 25,000. No charge for people over 65, children up to 12 and people with disabilities.',
		'<strong>Trolling</strong> (extra permit on top of the ordinary one): AR$ 25,000 per day, AR$ 50,000 for ten days, AR$ 100,000 for the season, according to Perfil on the Advisory Commission decision. The Río Negro portal lists the permit but its amounts did not appear in what we read.',
	] ),
	pm_bq_p( 'According to the Río Negro annex of the 2026/27 regulation, the Patagonian fishing permit is valid in Río Negro, Neuquén, Chubut and the National Parks, as long as its authenticity can be checked on the spot (the Neuquén annex says the same for an online permit, with its QR code and your ID). So one ordinary permit covers the lake, the rivers inside the National Park and the provincial waters. From 2026/27 Río Negro also sells a separate social licence for the Río Negro river and other waters far from here: it is not valid in the rest of Patagonia. Take your ID or passport and the permit with you.' ),
	pm_bq_h( 'Rules we have read: Nahuel Huapi National Park, 2026/27' ),
	pm_bq_p( 'These are the entries of the 2026/27 National Parks annex, read in a copy hosted by a third party because the official site did not answer. Waters not listed in the annex are not open to fishing. Always ask your guide which rules apply on the day.' ),
	pm_bq_lista( [
		'<strong>Lake Nahuel Huapi</strong>: open from 1 November 2026 to 1 May 2027, with a daily limit of two salmonids. From 2 May to 31 May 2027 (and in October 2027) the limit is one salmonid under 40 cm, and fishing is banned within 200 m of river and stream mouths. Fishing is banned in the west of the Campanario arm until 31 December 2026. Motor navigation is banned within 300 m of the cliffs on Isla Victoria and Isla Fray Menéndez, and there are landing bans on parts of the Blest and Tristeza arms and some islands.',
		'<strong>Manso River</strong>: the upper Manso (source to Lake Mascardi) is closed to fishing. The middle Manso (Mascardi to Lake Hess, and the fourth waterfall to Lake Steffen) is catch and release; fishing from a boat without a motor is allowed from 1 November 2026 to 30 April 2027, while fishing and navigation are banned between Lake Hess and the fourth waterfall. The lower Manso (Lake Steffen to the Villegas confluence) is fly only, with a limit of two salmonids, and boats without a motor are allowed from 1 November 2026 to 31 March 2027. Didymo alert: treat your gear.',
		'<strong>Traful River</strong>: navigation is banned in every form; fly only and catch and release; the only public access is at the confluence with the Minero River. <strong>Lake Traful</strong>: one rainbow trout under 40 cm per day in November (barbless hook), two between 1 December 2026 and 31 March 2027; no fishing within 100 m of the Bosque Sumergido.',
		'<strong>Upper Limay</strong> (inside the National Park): 1 November 2026 to 31 May 2027; boats without a motor are allowed except in the first 400 m below the lake; from the lake to Rincón Chico it is fly only and catch and release; the limit is one salmonid and from 1 April 2027 every fish is released. The annex also lists permitted fishing hours by month.',
		'<strong>Lake Mascardi</strong>: 1 November 2026 to 1 May 2027, two salmonids. <strong>Lake Steffen</strong>: one salmonid; boats with engines over 50 HP are banned.',
		'<strong>Felt soles are banned</strong> in the National Park waters we read, and in all waters under Río Negro jurisdiction (Río Negro annex). Disinfect your gear against the Didymo alga.',
	] ),
	pm_bq_h( 'What we have not read yet' ),
	pm_bq_p( 'We have <strong>not read</strong> the list of environments in Río Negro jurisdiction (for example the Limay downstream, the Ñirihuau and the Pichileufu), so we publish no rules for them: ask the Mesa Directiva (+54 294 442-1515) or your guide. Below the dams, the Neuquén annex divides the Limay into zones, from the Piedra del Águila dam down to the paraje Álamo Guacho, with fly-only stretches and release rules; those are covered in the Neuquén annex. In Neuquén waters fishing is banned within 500 m of the dams and from bridges and footbridges, and hiring a guide who is not an authorised tourism provider of the province is an offence. The Río Negro annex also states that fishing is for daylight hours and that transporting live fish is banned without authorisation.' ),
	pm_bq_h( 'Getting there and when to go' ),
	pm_bq_p( 'Bariloche has its own airport; Tipiliuke Lodge describes the flight from Buenos Aires as about two hours. Rio Manso Lodge says its guest transfers from the Bariloche area are included and that the lodge is about 1.5 to 2 hours by road from the nearest regional airport. These are the operators\' own times. The season starts on 1 November 2026; Lake Nahuel Huapi closes on 1 May 2027 and the Limay inside the Park on 31 May 2027.' ),
	pm_bq_pendiente( 'Río Negro environments (Limay downstream, Ñirihuau, Pichileufu); the general text of the 2026/27 regulation (Articles 4 and 22); calendar by month with a technical source; river levels; photo with its credit; guide list.' ),
	pm_bq_h( 'Lodges and outfitters' ),
	pm_bq_p( 'Below are the ones we found with public information; we have not contacted them yet, which is why they are marked. A rate is shown only when the operator publishes it with its season.' ),
] );

// --- Spanish (source of the shared data) -------------------------------------------------
$cuerpo_es = implode( "\n\n", [
	pm_bq_p( '<strong>San Carlos de Bariloche</strong> es la base para pescar en el <strong>lago Nahuel Huapi</strong> y en los ríos del Parque Nacional Nahuel Huapi: el <strong>Manso</strong>, el <strong>Traful</strong> y el <strong>Limay</strong> superior; el Limay aguas abajo de los diques queda más lejos. Esta página reúne las reglas 2026/27 que leímos, el permiso y lo que todavía no leímos. Es una guía para planificar: confirmá todo con tu guía y con la autoridad antes de viajar.' ),
	pm_bq_h( 'El permiso y cuánto cuesta' ),
	pm_bq_p( 'El permiso de Río Negro lo emite la <strong>Mesa Directiva de Pesca Deportiva Zona Andina</strong> (Av. 12 de Octubre y Onelli, Bariloche), que lo vende online. La temporada 2026/27 empieza el <strong>1 de noviembre de 2026</strong>. Valores de su portal, en pesos argentinos e IVA incluido:' ),
	pm_bq_lista( [
		'<strong>Extranjeros no residentes</strong>: un día $50.000; diez días $140.000; temporada $280.000. Residentes de países limítrofes: diez días $50.000.',
		'<strong>Residentes en el país</strong>: un día $12.500; diez días $25.000; temporada $50.000; de 13 a 17 años, temporada $25.000. Sin cargo para mayores de 65, hasta 12 años y personas con discapacidad.',
		'<strong>Trolling</strong> (permiso adicional que se suma al ordinario): $25.000 por día, $50.000 por diez días, $100.000 por temporada, según Perfil sobre la decisión de la Comisión Consultiva. El portal de Río Negro lista el permiso pero los montos no aparecieron en lo que leímos.',
	] ),
	pm_bq_p( 'Según el anexo de Río Negro del reglamento 2026/27, el permiso patagónico de pesca vale en Río Negro, Neuquén, Chubut y Parques Nacionales, siempre que su autenticidad pueda comprobarse en el lugar (el anexo de Neuquén dice lo mismo para el permiso online, con su código QR y tu documento). O sea que un permiso ordinario cubre el lago, los ríos dentro del Parque Nacional y las aguas provinciales. Desde 2026/27 Río Negro vende además una licencia social aparte para el río Negro y otras aguas lejos de acá: no vale en el resto de la Patagonia. Llevá el DNI o el pasaporte y el permiso.' ),
	pm_bq_h( 'Reglas que leímos: Parque Nacional Nahuel Huapi, 2026/27' ),
	pm_bq_p( 'Son las fichas del anexo 2026/27 de Parques Nacionales, leídas en una copia alojada por terceros porque el sitio oficial no respondió. En las aguas que no figuran en el anexo no se puede pescar. Preguntale siempre a tu guía qué reglas valen ese día.' ),
	pm_bq_lista( [
		'<strong>Lago Nahuel Huapi</strong>: abierto del 1 de noviembre de 2026 al 1 de mayo de 2027, con límite diario de dos salmónidos. Del 2 al 31 de mayo de 2027 (y en octubre de 2027) el límite es un salmónido de menos de 40 cm y está prohibido pescar a menos de 200 m de las bocas de ríos y arroyos. Hasta el 31 de diciembre de 2026 no se pesca en el oeste del brazo Campanario. Se prohíbe navegar a motor a menos de 300 m de los paredones de la isla Victoria y de la isla Fray Menéndez, y hay prohibiciones de desembarco en partes de los brazos Blest y Tristeza y en algunas islas.',
		'<strong>Río Manso</strong>: el Manso Superior (nacientes al lago Mascardi) no se pesca. El Manso Medio (Mascardi a lago Hess y salto 4 a lago Steffen) es de devolución obligatoria; se permite la pesca embarcada sin motor del 1 de noviembre de 2026 al 30 de abril de 2027, y entre el lago Hess y el salto 4 están prohibidas la pesca y la navegación. El Manso Inferior (lago Steffen a la confluencia con el Villegas) es solo mosca, con límite de dos salmónidos, y se permite embarcar sin motor del 1 de noviembre de 2026 al 31 de marzo de 2027. Alerta de Didymo: tratá el equipo.',
		'<strong>Río Traful</strong>: se prohíbe la navegación en todas sus formas; solo mosca y devolución obligatoria; el único acceso público es por la confluencia con el río Minero. <strong>Lago Traful</strong>: una trucha arco iris de menos de 40 cm por día en noviembre (anzuelo sin rebaba), dos entre el 1 de diciembre de 2026 y el 31 de marzo de 2027; no se pesca a menos de 100 m del Bosque Sumergido.',
		'<strong>Limay superior</strong> (dentro del Parque Nacional): del 1 de noviembre de 2026 al 31 de mayo de 2027; se permite embarcar sin motor salvo en los primeros 400 m debajo del lago; del lago a Rincón Chico es solo mosca y devolución obligatoria; el límite es un salmónido y desde el 1 de abril de 2027 se devuelven todos. El anexo lista además los horarios de pesca permitidos por mes.',
		'<strong>Lago Mascardi</strong>: del 1 de noviembre de 2026 al 1 de mayo de 2027, dos salmónidos. <strong>Lago Steffen</strong>: un salmónido; se prohíben las embarcaciones con motores de más de 50 HP.',
		'<strong>Se prohíben las suelas de fieltro</strong> en las aguas del Parque que leímos y en todas las aguas de jurisdicción de Río Negro (anexo de Río Negro). Desinfectá el equipo por el alga Didymo.',
	] ),
	pm_bq_h( 'Lo que todavía no leímos' ),
	pm_bq_p( '<strong>No leímos</strong> el listado de ambientes de jurisdicción de Río Negro (por ejemplo el Limay aguas abajo, el Ñirihuau y el Pichileufu), así que no publicamos reglas para ellos: consultá a la Mesa Directiva (+54 294 442-1515) o a tu guía. Aguas abajo de los diques, el anexo de Neuquén divide el Limay en zonas, desde la presa de Piedra del Águila hasta el paraje Álamo Guacho, con tramos solo mosca y reglas de devolución; eso está en el anexo de Neuquén. En aguas de Neuquén está prohibido pescar a menos de 500 m de las presas y desde puentes y pasarelas, y contratar un guía que no esté habilitado como prestador turístico de la provincia es una infracción. El anexo de Río Negro dice además que se pesca en las horas de luz y que está prohibido transportar peces vivos sin autorización.' ),
	pm_bq_h( 'Cómo llegar y cuándo ir' ),
	pm_bq_p( 'Bariloche tiene aeropuerto propio; Tipiliuke Lodge describe el vuelo desde Buenos Aires como de unas dos horas. Rio Manso Lodge dice que incluye los traslados desde la zona de Bariloche y que el lodge está a 1,5 a 2 horas por ruta del aeropuerto regional más cercano. Son los tiempos de cada operador. La temporada empieza el 1 de noviembre de 2026; el lago Nahuel Huapi cierra el 1 de mayo de 2027 y el Limay dentro del Parque el 31 de mayo de 2027.' ),
	pm_bq_pendiente( 'ambientes de Río Negro (Limay aguas abajo, Ñirihuau, Pichileufu); el texto general del reglamento 2026/27 (arts. 4 y 22); calendario por mes con una fuente técnica; alturas de los ríos; foto con su crédito; listado de guías.' ),
	pm_bq_h( 'Lodges y operadores' ),
	pm_bq_p( 'Abajo están los que encontramos con información pública; todavía no los contactamos, por eso figuran marcados. Una tarifa se muestra solo cuando el operador la publica con su temporada.' ),
] );

$comun = [
	'clave'       => 'bariloche',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'reglamento_autoridad' => 'Mesa Directiva de Pesca Deportiva Zona Andina (Río Negro) y Administración de Parques Nacionales',
	'reglamento_url'       => 'https://www.permisosdepesca.com.ar/',
	'reglamento_verificado' => $hoy,
];

$guias = [
	'es' => [
		'titulo' => 'Bariloche y el Nahuel Huapi', 'slug' => 'pesca-en-bariloche', 'cuerpo' => $cuerpo_es, 'fuentes' => $filas( $fuentes_es ),
		'campos' => [
			'h1'          => 'Pesca en Bariloche y el Nahuel Huapi: lago, ríos, reglas y permiso 2026/27',
			'bajada'      => 'El lago Nahuel Huapi y los ríos Manso, Traful y Limay del Parque Nacional. Las reglas 2026/27 que leímos, el permiso de Río Negro y lo que falta leer.',
			'modalidades' => "Mosca (flycast)\nSpinning\nPesca embarcada sin motor\nCaptura y devolución",
			'epoca'       => 'Del 1 de noviembre de 2026 al 1 de mayo de 2027 en el lago; el Limay dentro del Parque hasta el 31 de mayo de 2027.',
			'seo_title'   => 'Pesca en Bariloche y el Nahuel Huapi: reglas y permiso',
			'seo_description' => 'Guía de pesca en Bariloche y el Nahuel Huapi: reglas 2026/27 del lago y de los ríos Manso, Traful y Limay, permisos de Río Negro y lodges.',
			'temporada'   => [
				[ 'meses' => 'Noviembre', 'que' => 'Apertura de la temporada 2026/27 (1 de noviembre de 2026).' ],
				[ 'meses' => 'Hasta el 1 de mayo', 'que' => 'Cierre del lago Nahuel Huapi y del Mascardi (1 de mayo de 2027); el Limay dentro del Parque cierra el 31 de mayo de 2027.' ],
				[ 'meses' => 'Calendario por mes', 'que' => 'PENDIENTE: eclosiones, alturas y mejores meses con una fuente técnica.' ],
			],
			'reglamento_resumen' => 'Reglamento General de Pesca Deportiva Continental Patagónico, temporada 2026/27. Anexo de ambientes de Parques Nacionales y de Neuquén leídos el 30/10/2026 en copias alojadas por terceros (AICACYP); puntos generales del anexo de Río Negro copiados del sitio del reglamento por el dueño el mismo día. Valores de permisos del portal de la Mesa Directiva Zona Andina. No se leyó el listado de ambientes de Río Negro ni el texto general (arts. 4 y 22).',
			'aguas' => [
				[ 'nombre' => 'Lago Nahuel Huapi', 'nota' => 'Dos salmónidos por día; ninguna pesca a menos de 200 m de las bocas de ríos entre el 2 y el 31 de mayo.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Manso', 'nota' => 'Superior cerrado; medio de devolución obligatoria; inferior solo mosca.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Traful', 'nota' => 'Solo mosca, devolución obligatoria, sin navegación; acceso público por el río Minero.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Limay (tramo del Parque)', 'nota' => 'Solo mosca y devolución hasta Rincón Chico; sin motor.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => '¿Qué permiso necesito para pescar en Bariloche y cuánto cuesta?', 'respuesta' => 'El permiso patagónico de Río Negro, que se compra online en el portal de la Mesa Directiva Zona Andina. Para extranjeros no residentes en 2026/27: un día $50.000, diez días $140.000 y temporada $280.000 (pesos argentinos, IVA incluido). Confirmá el valor vigente antes de pagar.' ],
				[ 'pregunta' => '¿Necesito un permiso aparte para el Parque Nacional?', 'respuesta' => 'Según el anexo de Río Negro del reglamento 2026/27, el permiso patagónico vale en Río Negro, Neuquén, Chubut y Parques Nacionales. No leímos un permiso aparte del Parque para el lago y los ríos: confirmalo al comprar.' ],
				[ 'pregunta' => '¿Cuándo empieza la temporada?', 'respuesta' => 'El 1 de noviembre de 2026. El lago Nahuel Huapi y el Mascardi cierran el 1 de mayo de 2027, y el Limay dentro del Parque el 31 de mayo de 2027.' ],
				[ 'pregunta' => '¿Cuántos peces puedo llevarme del lago Nahuel Huapi?', 'respuesta' => 'Hasta el 1 de mayo de 2027, dos salmónidos por día. Del 2 al 31 de mayo de 2027, un salmónido de menos de 40 cm. Los guías de la zona suelen devolver todo.' ],
				[ 'pregunta' => '¿Qué reglas tiene el río Manso?', 'respuesta' => 'El Manso Superior no se pesca. El Manso Medio es de devolución obligatoria y se puede pescar embarcado sin motor del 1 de noviembre de 2026 al 30 de abril de 2027. El Manso Inferior, del lago Steffen al río Villegas, es solo mosca con límite de dos salmónidos.' ],
				[ 'pregunta' => '¿Cómo se pesca el río Traful?', 'respuesta' => 'Solo con mosca y con devolución obligatoria, sin navegar. El único acceso público habilitado es por la confluencia con el río Minero.' ],
				[ 'pregunta' => '¿Puedo usar botas con suela de fieltro?', 'respuesta' => 'No. Están prohibidas en las aguas del Parque Nacional que leímos y en todas las aguas de jurisdicción de Río Negro. Desinfectá el equipo por el alga Didymo.' ],
				[ 'pregunta' => '¿Qué reglas tienen los ríos de Río Negro fuera del Parque?', 'respuesta' => 'Todavía no leímos el listado de ambientes de Río Negro (Limay aguas abajo, Ñirihuau, Pichileufu), así que no publicamos reglas para ellos. Consultá a la Mesa Directiva, al 0294 442-1515, o a tu guía.' ],
				[ 'pregunta' => '¿Cuánto cuesta una estadía en un lodge?', 'respuesta' => 'Rio Manso Lodge publica para 2026-27 US$ 1.050 por pescador y noche o US$ 7.000 por semana, por persona en habitación doble (su propio sitio, leído el 30/10/2026). Los otros operadores no publican tarifa o no pudimos leerla.' ],
				[ 'pregunta' => '¿Cómo llego a Bariloche?', 'respuesta' => 'Bariloche tiene aeropuerto propio; según Tipiliuke Lodge el vuelo desde Buenos Aires dura unas dos horas. Rio Manso Lodge incluye los traslados desde la zona de Bariloche.' ],
			],
		],
	],
	'en' => [
		'titulo' => 'Bariloche and Nahuel Huapi', 'slug' => 'fishing-in-bariloche', 'cuerpo' => $cuerpo_en, 'fuentes' => $filas( $fuentes_en ),
		'campos' => [
			'h1'          => 'Fishing in Bariloche and Nahuel Huapi: lake, rivers, rules and 2026/27 permit',
			'bajada'      => 'Lake Nahuel Huapi and the Manso, Traful and Limay rivers of the National Park. The 2026/27 rules we have read, the Río Negro permit and what is still to read.',
			'modalidades' => "Fly fishing\nSpinning\nFishing from a boat without a motor\nCatch and release",
			'epoca'       => '1 November 2026 to 1 May 2027 on the lake; the Limay inside the Park until 31 May 2027.',
			'seo_title'   => 'Fishing in Bariloche and Nahuel Huapi: rules, permit',
			'seo_description' => 'Fishing guide to Bariloche and Nahuel Huapi: 2026/27 rules for the lake and the Manso, Traful and Limay rivers, Río Negro permit prices and lodges.',
			'temporada'   => [
				[ 'meses' => 'November', 'que' => '2026/27 season opens (1 November 2026).' ],
				[ 'meses' => 'Until 1 May', 'que' => 'Lake Nahuel Huapi and Lake Mascardi close on 1 May 2027; the Limay inside the Park closes on 31 May 2027.' ],
				[ 'meses' => 'Calendar by month', 'que' => 'PENDIENTE: hatches, river levels and best months, with a technical source.' ],
			],
			'reglamento_resumen' => 'General Regulation of Continental Sport Fishing of Patagonia, 2026/27 season. Annexes for the National Parks and Neuquén read on 30/10/2026 in copies hosted by a third party (AICACYP); general points of the Río Negro annex copied from the regulation site by the owner the same day. Permit values from the portal of the Mesa Directiva Zona Andina. The list of Río Negro environments and the general text (Articles 4 and 22) were not read.',
			'aguas' => [
				[ 'nombre' => 'Lake Nahuel Huapi', 'nota' => 'Two salmonids a day; no fishing within 200 m of river mouths from 2 to 31 May.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Manso River', 'nota' => 'Upper closed; middle catch and release; lower fly only.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Traful River', 'nota' => 'Fly only, catch and release, no navigation; public access via the Minero River.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Limay River (National Park section)', 'nota' => 'Fly only and release down to Rincón Chico; no motors.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => 'Which permit do I need to fish in Bariloche and how much is it?', 'respuesta' => 'The Patagonian permit of Río Negro, bought online on the portal of the Mesa Directiva Zona Andina. For non-resident foreigners in 2026/27: one day AR$ 50,000, ten days AR$ 140,000, season AR$ 280,000 (Argentine pesos, VAT included). Confirm the current amount before you pay.' ],
				[ 'pregunta' => 'Do I need a separate permit for the National Park?', 'respuesta' => 'According to the Río Negro annex of the 2026/27 regulation, the Patagonian permit is valid in Río Negro, Neuquén, Chubut and the National Parks. We did not read a separate Park permit for the lake and rivers: confirm it when you buy.' ],
				[ 'pregunta' => 'When does the season start?', 'respuesta' => 'On 1 November 2026. Lake Nahuel Huapi and Lake Mascardi close on 1 May 2027, and the Limay inside the Park on 31 May 2027.' ],
				[ 'pregunta' => 'How many fish can I keep from Lake Nahuel Huapi?', 'respuesta' => 'Until 1 May 2027, two salmonids a day. From 2 to 31 May 2027, one salmonid under 40 cm. Local guides usually release everything.' ],
				[ 'pregunta' => 'What are the rules on the Manso River?', 'respuesta' => 'The upper Manso is closed to fishing. The middle Manso is catch and release, and fishing from a boat without a motor is allowed from 1 November 2026 to 30 April 2027. The lower Manso, from Lake Steffen to the Villegas, is fly only with a limit of two salmonids.' ],
				[ 'pregunta' => 'How do I fish the Traful River?', 'respuesta' => 'Fly only and catch and release, with no navigation. The only public access is at the confluence with the Minero River.' ],
				[ 'pregunta' => 'Can I wear felt-soled boots?', 'respuesta' => 'No. They are banned in the National Park waters we read and in all waters under Río Negro jurisdiction. Disinfect your gear against the Didymo alga.' ],
				[ 'pregunta' => 'What are the rules on Río Negro rivers outside the Park?', 'respuesta' => 'We have not read the list of Río Negro environments yet (the Limay downstream, the Ñirihuau, the Pichileufu), so we publish no rules for them. Ask the Mesa Directiva on +54 294 442-1515, or your guide.' ],
				[ 'pregunta' => 'How much does a lodge stay cost?', 'respuesta' => 'Rio Manso Lodge publishes for 2026-27 US$ 1,050 per angler per night or US$ 7,000 per week, per person in a double room (its own site, read on 30/10/2026). The other operators publish no rate or we could not read it.' ],
				[ 'pregunta' => 'How do I get to Bariloche?', 'respuesta' => 'Bariloche has its own airport; according to Tipiliuke Lodge the flight from Buenos Aires takes about two hours. Rio Manso Lodge includes transfers from the Bariloche area.' ],
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

foreach ( [ 'es', 'en' ] as $i ) {
	$g = $guias[ $i ];
	if ( pm_buscar( 'pm_destino', 'bariloche', $i ) ) {
		WP_CLI::log( "bariloche ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "bariloche ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $g['fuentes'] ) . ' fuentes (title ' . mb_strlen( $g['campos']['seo_title'] ) . ', desc ' . mb_strlen( $g['campos']['seo_description'] ) . ')' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $g['cuerpo'] ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i, 'fuentes' => $g['fuentes'] ];
	if ( 'es' === $i ) {
		$campos += [ 'region' => 'patagonia', 'provincia' => 'rio-negro', 'lat' => -41.13, 'lon' => -71.31, 'especies' => [ 'trucha' ] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "✓ bariloche ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
}

// --- Outfitters: "listado con información pública" (docs/prestadores.md) ----------------------
$prestadores = [
	[ 'Rio Manso Lodge', [ 'alojamiento' ], 'https://www.riomansolodge.com/lodge-rates-details/', 'Lodge near Bariloche with guided fishing. Its own site publishes for 2026-27 US$ 1,050 per angler per night or US$ 7,000 per week, per person in a double room (read on 30/10/2026). It says it works on private land and is not inside a national park; its pages disagree on whether the fishing licence is included, so ask.', 'Bariloche area, Río Negro' ],
	[ 'Limay River Lodge', [ 'alojamiento' ], 'https://limaylodge.com/', 'Lodge on the Limay River, built on riverfront land bought in 2013; first season 2015 (the operator\'s own history). Its fishing page did not load in our reader, so nothing else is confirmed: no rate, no capacity.', 'Limay River, Patagonia' ],
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
		'tipo' => $tipo, 'prest_destino' => 'bariloche', 'localidad' => $loc, 'descripcion' => $desc,
		'prest_modalidades' => [ 'mosca' ], 'lancha' => '', 'motor' => '',
		'prest_publico' => 1, 'prest_fuente' => $url, 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0,
		'notas' => 'Listado con información pública (sitio del operador leído el 30/10/2026). Pescamapa no confirmó contacto, tarifa, idiomas ni disponibilidad. Falta contactarlo.',
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
