<?php
/**
 * Río Gallegos (Santa Cruz), 4/11/2026: destination guide in Spanish (source of the shared data) and English, plus two public-information
 * outfitter cards. Everything stated comes from what was read; what is missing is marked as such. Facts:
 *  - Rules for the Gallegos, Gallegos Chico, Chico Sur and Penitente rivers, 2026/27: Anexo I "Ambientes de Santa Cruz" on the official portal
 *    pesca.santacruz.gob.ar/ambientes/, read on 4/11/2026 with investigar.yml (rama investigacion, carpetas rio-gallegos-fuentes-20261104b…).
 *    The annex says it lists only "some of the most outstanding" environments and that the general regulation applies unless stated otherwise:
 *    the general regulation was NOT read, and the guide says so.
 *  - Permit values 2026/27: official portal pesca.santacruz.gob.ar/valores/, read on 4/11/2026.
 *  - Operators: Karku Fly Fishing (its site: the-camp, the-fishing, faqs, terms) and Estancia Las Buitreras (Solid Adventures page).
 *    Neither publishes a rate, so no price is shown. Agencies' rates are not used.
 *  - NOT read: Bella Sofía, Los Plateados, Truchaike, Los Morros (no site that opened); the general regulation (felt, hooks); river levels.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = rio-gallegos-2026-11-04).
 */

$aplicar = true;
$hoy     = '20261104';

function pm_gl_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_gl_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_gl_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_gl_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'amb'  => 'https://pesca.santacruz.gob.ar/ambientes/',
	'val'  => 'https://pesca.santacruz.gob.ar/valores/',
	'inicio' => 'https://pesca.santacruz.gob.ar/',
	'kcamp' => 'https://karkuflyfishing.com/the-camp/',
	'kfish' => 'https://karkuflyfishing.com/the-fishing/',
	'kfaq'  => 'https://karkuflyfishing.com/faqs/',
	'ktc'   => 'https://karkuflyfishing.com/terms-conditions/',
	'lb'    => 'https://www.solidadventures.com/flyfishing-patagonia-searun-browntrout-lasbuitreras/',
	'gs'    => 'https://www.guiasantacruz.net/rio-gallegos/guia-turistica/',
	'rn24'  => 'https://www.rionegro.com.ar/?p=3837803',
];
$fuentes_es = [
	[ 'Gobierno de Santa Cruz, Pesca y Acuicultura: Anexo I, Ambientes de Santa Cruz, temporada 2026/2027 (portal oficial, leído el 4/11/2026)', $u['amb'], 1 ],
	[ 'Gobierno de Santa Cruz, Pesca y Acuicultura: valores y categorías de los permisos 2026/2027 (portal oficial, leído el 4/11/2026)', $u['val'], 1 ],
	[ 'Gobierno de Santa Cruz, Pesca y Acuicultura: portal de trámites y temporada 2026-2027', $u['inicio'], 1 ],
	[ 'Karku Fly Fishing: el campamento (operador)', $u['kcamp'], 0 ],
	[ 'Karku Fly Fishing: la pesca y la temporada (operador)', $u['kfish'], 0 ],
	[ 'Karku Fly Fishing: preguntas frecuentes y términos (operador)', $u['kfaq'], 0 ],
	[ 'Estancia Las Buitreras, Solid Adventures (operador)', $u['lb'], 0 ],
	[ 'Guía Santa Cruz: Río Gallegos, alojamientos y lodges de pesca (directorio local)', $u['gs'], 0 ],
	[ 'Diario Río Negro, 15/10/2024: dónde vale el permiso patagónico (prensa, de la temporada 2024/25)', $u['rn24'], 0 ],
];
$fuentes_en = [
	[ 'Government of Santa Cruz, Fishing and Aquaculture: Annex I, Environments of Santa Cruz, 2026/2027 season (official portal, read on 4 November 2026)', $u['amb'], 1 ],
	[ 'Government of Santa Cruz, Fishing and Aquaculture: permit values and categories 2026/2027 (official portal, read on 4 November 2026)', $u['val'], 1 ],
	[ 'Government of Santa Cruz, Fishing and Aquaculture: permit portal and 2026-2027 season', $u['inicio'], 1 ],
	[ 'Karku Fly Fishing: the camp (operator)', $u['kcamp'], 0 ],
	[ 'Karku Fly Fishing: the fishing and the season (operator)', $u['kfish'], 0 ],
	[ 'Karku Fly Fishing: FAQs and terms (operator)', $u['kfaq'], 0 ],
	[ 'Estancia Las Buitreras, Solid Adventures (operator)', $u['lb'], 0 ],
	[ 'Guía Santa Cruz: Río Gallegos, lodging and fishing lodges (local directory)', $u['gs'], 0 ],
	[ 'Diario Río Negro, 15 October 2024: where the Patagonian permit is valid (press, 2024/25 season)', $u['rn24'], 0 ],
];
$filas = fn( array $f ) => array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $f );

// --- Spanish -------------------------------------------------------------------------------
$cuerpo_es = implode( "\n\n", [
	pm_gl_p( '<strong>Río Gallegos</strong>, al sur de Santa Cruz, es la base para pescar la <strong>trucha marrón anádroma</strong> (la que vive en el mar y sube al río a desovar) en el <strong>río Gallegos</strong>, un río de poca pendiente que serpentea por la pampa hasta el estuario. Los lodges están en estancias a entre 50 minutos y varias horas de la ciudad. Esta página reúne las reglas 2026/27 leídas del anexo oficial, el permiso y lo que cuesta, lo que publican los operadores y lo que todavía no confirmamos.' ),
	pm_gl_h( 'Las reglas del río Gallegos, 2026/27' ),
	pm_gl_p( 'Son las fichas del <strong>Anexo I, Ambientes de Santa Cruz, temporada 2026/2027</strong>, que publica el portal oficial de Pesca y Acuicultura. El propio anexo aclara que lista solo algunos de los ambientes más destacados y que, salvo indicación contraria, rige el reglamento general.' ),
	pm_gl_lista( [
		'<strong>Río Gallegos:</strong> del <strong>1 de noviembre de 2026 al 30 de mayo de 2027</strong>. El régimen predominante es de <strong>captura y devolución obligatoria</strong> en ambas márgenes, desde las nacientes hasta el estuario marino.',
		'<strong>Ventana especial de febrero:</strong> del 1 al 28 de febrero de 2027 el límite diario es de <strong>un ejemplar de trucha marrón anádroma</strong>.',
		'<strong>Zona de veda:</strong> ambas márgenes, aguas arriba y abajo del <strong>puente Güer Aike</strong>, en una distancia lineal de 200 m.',
		'<strong>Gallegos Chico:</strong> del 1 de noviembre de 2026 al 3 de mayo de 2027, <strong>solo mosca</strong> y devolución obligatoria. Hay veda total desde el puente Nuevo (ruta nacional 40) hasta su desembocadura.',
		'<strong>Chico Sur:</strong> del 1 de noviembre de 2026 al 3 de mayo de 2027, captura y devolución obligatoria absoluta y <strong>solo mosca</strong>.',
		'<strong>Penitente:</strong> del 1 de noviembre de 2026 al 3 de mayo de 2027, captura y devolución obligatoria.',
	] ),
	pm_gl_p( 'El anexo define el <strong>área de exclusión</strong> como la franja de 200 m (o la que se indique) en ambas márgenes desde las nacientes y desembocaduras de los cursos vinculados, donde no se puede pescar.' ),
	pm_gl_h( 'El permiso y cuánto cuesta' ),
	pm_gl_p( 'El permiso es <strong>provincial</strong> y se tramita en línea en el portal oficial. La temporada 2026/27 va del 1 de octubre de 2026 al 30 de septiembre de 2027. Valores publicados por el portal el 4/11/2026, en pesos argentinos:' ),
	pm_gl_lista( [
		'<strong>Extranjeros no residentes:</strong> diario $90.000; semanal $300.000; temporada $450.000.',
		'<strong>Trolling</strong> (permiso adicional que se suma al ordinario): $20.000 diario, $30.000 semanal, $40.000 por temporada.',
		'<strong>Residentes en el país:</strong> diario $15.000; fin de semana $20.000; semanal $25.000; temporada $40.000. Sin cargo para mayores de 65, jubilados, pensionados, menores de 12 años y personas con discapacidad.',
	] ),
	pm_gl_p( 'Según la prensa de 2024, el permiso patagónico no vale en Santa Cruz ni en Tierra del Fuego. Karku Fly Fishing dice en su sitio que se ocupa de los permisos de sus huéspedes: preguntale a tu lodge qué incluye.' ),
	pm_gl_h( 'Cuándo van los peces' ),
	pm_gl_p( 'Según Karku Fly Fishing, la temporada principal del río Gallegos va de fines de diciembre a abril. Dice que al principio llegan peces plateados recién salidos del mar, que enero y febrero son los meses de los ejemplares más grandes y agresivos, y que marzo y abril traen una actividad pareja y muchos peces de tamaño medio. Es la descripción de un operador: para elegir la semana, preguntale a tu lodge.' ),
	pm_gl_h( 'Cómo llegar' ),
	pm_gl_p( 'Karku Fly Fishing dice que su estancia está a 50 minutos en auto del aeropuerto de Río Gallegos y a cinco minutos del río, y que incluye los traslados desde el aeropuerto. Estancia Las Buitreras está a unos diez minutos a pie del río, según su operador. El directorio Guía Santa Cruz ubica Los Morros Lodge sobre la ruta 40, a 180 km de la ciudad. Son los datos de cada operador o directorio; no verificamos las distancias.' ),
	pm_gl_pendiente( 'el reglamento general de pesca de Santa Cruz (suelas de fieltro, anzuelos, desinfección); Bella Sofía, Los Plateados, Truchaike y Los Morros (sin sitio propio que abriera); calendario por mes con una fuente técnica; alturas del río; foto con su crédito; guías independientes.' ),
	pm_gl_h( 'Lodges y operadores' ),
	pm_gl_p( 'Abajo están los que encontramos con información pública; todavía no los contactamos, por eso figuran marcados. Una tarifa se muestra solo cuando el operador la publica con su temporada, y ninguno de estos dos publica la suya.' ),
] );

// --- English -------------------------------------------------------------------------------
$cuerpo_en = implode( "\n\n", [
	pm_gl_p( '<strong>Río Gallegos</strong>, in the south of Santa Cruz, is the base for fishing <strong>sea-run brown trout</strong> (the trout that lives at sea and returns to the river to spawn) on the <strong>Río Gallegos</strong>, a low-gradient river that meanders across the pampa to the estuary. The lodges sit on estancias between 50 minutes and several hours from the city. This page brings together the 2026/27 rules read from the official annex, the permit and what it costs, what the operators publish and what we have not confirmed yet.' ),
	pm_gl_h( 'Río Gallegos rules, 2026/27' ),
	pm_gl_p( 'These are the entries of <strong>Annex I, Environments of Santa Cruz, 2026/2027 season</strong>, published on the official Fishing and Aquaculture portal. The annex itself says it lists only some of the most outstanding environments and that, unless stated otherwise, the general regulation applies.' ),
	pm_gl_lista( [
		'<strong>Río Gallegos:</strong> <strong>1 November 2026 to 30 May 2027</strong>. The prevailing regime is <strong>mandatory catch and release</strong> on both banks, from the sources to the sea estuary.',
		'<strong>February window:</strong> from 1 to 28 February 2027 the daily limit is <strong>one sea-run brown trout</strong>.',
		'<strong>Closed zone:</strong> both banks, upstream and downstream of the <strong>Güer Aike bridge</strong>, over a linear distance of 200 m.',
		'<strong>Gallegos Chico:</strong> 1 November 2026 to 3 May 2027, <strong>fly only</strong> and mandatory release. Fishing is banned from the Puente Nuevo (Route 40) to its mouth.',
		'<strong>Chico Sur:</strong> 1 November 2026 to 3 May 2027, absolute catch and release and <strong>fly only</strong>.',
		'<strong>Penitente:</strong> 1 November 2026 to 3 May 2027, mandatory catch and release.',
	] ),
	pm_gl_p( 'The annex defines the <strong>exclusion area</strong> as the 200 m strip (or the one indicated) on both banks from the sources and mouths of the linked watercourses, where fishing is not allowed.' ),
	pm_gl_h( 'The permit and what it costs' ),
	pm_gl_p( 'The permit is <strong>provincial</strong> and is processed online on the official portal. The 2026/27 season runs from 1 October 2026 to 30 September 2027. Values published by the portal on 4 November 2026, in Argentine pesos:' ),
	pm_gl_lista( [
		'<strong>Non-resident foreigners:</strong> day AR$ 90,000; week AR$ 300,000; season AR$ 450,000.',
		'<strong>Trolling</strong> (an extra permit on top of the ordinary one): AR$ 20,000 per day, AR$ 30,000 per week, AR$ 40,000 per season.',
		'<strong>Argentine residents:</strong> day AR$ 15,000; weekend AR$ 20,000; week AR$ 25,000; season AR$ 40,000. No charge for people over 65, retirees, pensioners, children under 12 and people with disabilities.',
	] ),
	pm_gl_p( 'According to 2024 press, the Patagonian permit is not valid in Santa Cruz or Tierra del Fuego. Karku Fly Fishing says on its website that it takes care of its guests\' permits: ask your lodge what is included.' ),
	pm_gl_h( 'When the fish are in' ),
	pm_gl_p( 'According to Karku Fly Fishing, the prime season on the Río Gallegos runs from late December to April. It says fresh silver fish arrive early on, that January and February are the months for the biggest, most aggressive fish, and that March and April bring steady action and many average-sized fish. This is one operator\'s description: to choose your week, ask your lodge.' ),
	pm_gl_h( 'Getting there' ),
	pm_gl_p( 'Karku Fly Fishing says its estancia is a 50-minute drive from Río Gallegos airport and five minutes from the river, and that it includes airport transfers. Estancia Las Buitreras is about a ten-minute walk from the river, according to its operator. The Guía Santa Cruz directory places Los Morros Lodge on Route 40, 180 km from the city. These are each operator\'s or directory\'s figures; we have not verified the distances.' ),
	pm_gl_pendiente( 'the general fishing regulation of Santa Cruz (felt soles, hooks, disinfection); Bella Sofía, Los Plateados, Truchaike and Los Morros (no site of their own that opened); calendar by month with a technical source; river levels; photo with its credit; independent guides.' ),
	pm_gl_h( 'Lodges and outfitters' ),
	pm_gl_p( 'Below are the ones we found with public information; we have not contacted them yet, which is why they are marked. A rate is shown only when the operator publishes it with its season, and neither of these two publishes one.' ),
] );

$comun = [
	'clave'       => 'rio-gallegos',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'reglamento_autoridad' => 'Gobierno de Santa Cruz, Pesca y Acuicultura',
	'reglamento_url'       => $u['amb'],
	'reglamento_verificado' => $hoy,
];

$guias = [
	'es' => [
		'titulo' => 'Río Gallegos (Santa Cruz)', 'slug' => 'pesca-en-rio-gallegos', 'cuerpo' => $cuerpo_es, 'fuentes' => $filas( $fuentes_es ),
		'campos' => [
			'h1'          => 'Pesca en Río Gallegos, Santa Cruz: trucha de mar, reglas y permiso 2026/27',
			'bajada'      => 'La trucha marrón anádroma del río Gallegos. Las reglas 2026/27 leídas del anexo oficial, el permiso, lo que publican los lodges y lo que falta confirmar.',
			'modalidades' => "Mosca (flycast) con caña de dos manos\nCaptura y devolución",
			'epoca'       => 'Del 1 de noviembre de 2026 al 30 de mayo de 2027 en el río Gallegos; la temporada de los lodges va de fines de diciembre a abril.',
			'seo_title'   => 'Pesca en Río Gallegos, Santa Cruz: reglas y permiso',
			'seo_description' => 'Guía de pesca de trucha marrón anádroma en el río Gallegos, Santa Cruz: reglas 2026/27 del anexo oficial, permiso, lodges y cómo llegar.',
			'temporada'   => [
				[ 'meses' => '1 de noviembre', 'que' => 'Abre el río Gallegos (2026/27), con captura y devolución obligatoria.' ],
				[ 'meses' => 'Fines de diciembre a abril', 'que' => 'Temporada principal según Karku Fly Fishing: enero y febrero para los ejemplares más grandes, marzo y abril para la actividad pareja (operador).' ],
				[ 'meses' => 'Febrero', 'que' => 'Del 1 al 28 de febrero de 2027 se permite un ejemplar de trucha marrón anádroma por pescador y por día.' ],
				[ 'meses' => '30 de mayo', 'que' => 'Cierra el río Gallegos (2027). Gallegos Chico, Chico Sur y Penitente cierran el 3 de mayo.' ],
				[ 'meses' => 'Calendario por mes', 'que' => 'PENDIENTE: cuándo entran los peces y cómo cambia el agua mes a mes, con una fuente técnica.' ],
			],
			'reglamento_resumen' => 'Anexo I, Ambientes de Santa Cruz, temporada 2026/2027, leído en el portal oficial de Pesca y Acuicultura el 4/11/2026. El río Gallegos abre el 1/11/2026 y cierra el 30/5/2027, con captura y devolución obligatoria salvo una ventana del 1 al 28/2/2027 con un ejemplar de trucha marrón anádroma. No leímos el reglamento general; los valores de permiso son los del portal, confirmalos al pagar.',
			'aguas' => [
				[ 'nombre' => 'Río Gallegos', 'nota' => 'Captura y devolución obligatoria; ventana de febrero con un ejemplar; veda de 200 m en el puente Güer Aike.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Gallegos Chico', 'nota' => 'Solo mosca y devolución; veda total desde el puente Nuevo hasta la desembocadura.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Chico Sur', 'nota' => 'Solo mosca y devolución absoluta.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => '¿Cuándo se puede pescar en el río Gallegos?', 'respuesta' => 'Del 1 de noviembre de 2026 al 30 de mayo de 2027, según el Anexo I de ambientes de Santa Cruz. El permiso de la temporada 2026/27 vale del 1 de octubre de 2026 al 30 de septiembre de 2027. Los lodges describen su temporada principal de fines de diciembre a abril.' ],
				[ 'pregunta' => '¿Puedo quedarme con los peces?', 'respuesta' => 'En general no: el régimen predominante en el río Gallegos es de captura y devolución obligatoria en ambas márgenes, desde las nacientes hasta el estuario. La excepción es una ventana del 1 al 28 de febrero de 2027, con un límite diario de un ejemplar de trucha marrón anádroma.' ],
				[ 'pregunta' => '¿Qué permiso necesito y cuánto cuesta?', 'respuesta' => 'El permiso provincial de Santa Cruz, que se tramita en línea en el portal de Pesca y Acuicultura. Para extranjeros no residentes en 2026/27: diario $90.000, semanal $300.000 y temporada $450.000. El trolling es un permiso adicional. Valores del portal oficial, en pesos, leídos el 4/11/2026.' ],
				[ 'pregunta' => '¿Me sirve el permiso patagónico?', 'respuesta' => 'Según la prensa de 2024, el permiso patagónico no vale en Santa Cruz ni en Tierra del Fuego, así que hace falta el permiso provincial. Confirmalo con tu lodge o en el portal oficial antes de pagar.' ],
				[ 'pregunta' => '¿Hay ríos solo para mosca?', 'respuesta' => 'Sí. El Gallegos Chico y el Chico Sur son solo mosca, con devolución obligatoria. El anexo que leímos no impone mosca en el cauce principal del Gallegos, pero no leímos el reglamento general, así que preguntale a tu guía.' ],
				[ 'pregunta' => '¿Dónde está prohibido pescar?', 'respuesta' => 'En el río Gallegos, a 200 m aguas arriba y abajo del puente Güer Aike, sobre ambas márgenes. En el Gallegos Chico, desde el puente Nuevo de la ruta nacional 40 hasta su desembocadura. Y en general a 200 m de las nacientes y desembocaduras de los cursos vinculados.' ],
				[ 'pregunta' => '¿Cuánto cuesta una semana en un lodge del Gallegos?', 'respuesta' => 'No lo podemos decir con una fuente del propio operador: Karku Fly Fishing y Estancia Las Buitreras no publican sus tarifas en los sitios que leímos. Hay agencias que sí publican precios, pero no las usamos porque no sabemos qué incluyen ni de qué temporada son. Pedile el detalle al lodge.' ],
				[ 'pregunta' => '¿Qué incluye un lodge del Gallegos?', 'respuesta' => 'Según Karku Fly Fishing: traslados desde el aeropuerto de Río Gallegos, guías, alojamiento en una cabaña (dos habitaciones individuales y una doble, con baño compartido), desayuno, almuerzo y cena, y se ocupa de los permisos. No incluye lavandería, equipo, bebidas alcohólicas ni propinas (sugiere como mínimo el 12 % del costo del viaje). Pide un 50 % de depósito no reembolsable y el saldo 90 días antes.' ],
				[ 'pregunta' => '¿Cuál es el mejor momento para ir?', 'respuesta' => 'Karku Fly Fishing ubica la temporada principal de fines de diciembre a abril: enero y febrero para los ejemplares más grandes y agresivos, marzo y abril para una actividad más pareja. Es la opinión de un operador; el río abre en noviembre.' ],
				[ 'pregunta' => '¿Cómo llego?', 'respuesta' => 'Por el aeropuerto de Río Gallegos. Karku Fly Fishing dice que su estancia está a 50 minutos en auto y que ofrece los traslados. Los Morros Lodge figura en un directorio local a 180 km de la ciudad por la ruta 40.' ],
			],
		],
	],
	'en' => [
		'titulo' => 'Río Gallegos (Santa Cruz)', 'slug' => 'fishing-in-rio-gallegos', 'cuerpo' => $cuerpo_en, 'fuentes' => $filas( $fuentes_en ),
		'campos' => [
			'h1'          => 'Fishing in Río Gallegos, Santa Cruz: sea-run brown trout, rules and 2026/27 permit',
			'bajada'      => 'The sea-run brown trout of the Río Gallegos. The 2026/27 rules read from the official annex, the permit, what the lodges publish and what is still to confirm.',
			'modalidades' => "Fly fishing with two-handed rods\nCatch and release",
			'epoca'       => '1 November 2026 to 30 May 2027 on the Río Gallegos; the lodges\' prime season runs from late December to April.',
			'seo_title'   => 'Fishing in Río Gallegos, Santa Cruz: rules, permit',
			'seo_description' => 'Fishing guide to sea-run brown trout on the Río Gallegos, Santa Cruz: 2026/27 rules from the official annex, permit prices, lodges and how to get there.',
			'temporada'   => [
				[ 'meses' => '1 November', 'que' => 'The Río Gallegos opens (2026/27), with mandatory catch and release.' ],
				[ 'meses' => 'Late December to April', 'que' => 'Prime season according to Karku Fly Fishing: January and February for the biggest fish, March and April for steady action (operator).' ],
				[ 'meses' => 'February', 'que' => 'From 1 to 28 February 2027 one sea-run brown trout per angler per day is allowed.' ],
				[ 'meses' => '30 May', 'que' => 'The Río Gallegos closes (2027). Gallegos Chico, Chico Sur and Penitente close on 3 May.' ],
				[ 'meses' => 'Calendar by month', 'que' => 'PENDIENTE: when the fish come in and how the water changes month by month, with a technical source.' ],
			],
			'reglamento_resumen' => 'Annex I, Environments of Santa Cruz, 2026/2027 season, read on the official Fishing and Aquaculture portal on 4 November 2026. The Río Gallegos opens on 1 November 2026 and closes on 30 May 2027, with mandatory catch and release except for a window from 1 to 28 February 2027 with one sea-run brown trout. We did not read the general regulation; permit values are the portal\'s, confirm them when you pay.',
			'aguas' => [
				[ 'nombre' => 'Río Gallegos', 'nota' => 'Mandatory catch and release; February window with one fish; 200 m closure at the Güer Aike bridge.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Gallegos Chico', 'nota' => 'Fly only and release; closed from the Puente Nuevo to the mouth.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Chico Sur', 'nota' => 'Fly only and absolute release.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => 'When can I fish the Río Gallegos?', 'respuesta' => 'From 1 November 2026 to 30 May 2027, according to Annex I of the Santa Cruz environments. The 2026/27 permit is valid from 1 October 2026 to 30 September 2027. The lodges describe their prime season as late December to April.' ],
				[ 'pregunta' => 'Can I keep the fish?', 'respuesta' => 'Generally no: the prevailing regime on the Río Gallegos is mandatory catch and release on both banks, from the sources to the estuary. The exception is a window from 1 to 28 February 2027, with a daily limit of one sea-run brown trout.' ],
				[ 'pregunta' => 'Which permit do I need and how much is it?', 'respuesta' => 'The Santa Cruz provincial permit, processed online on the Fishing and Aquaculture portal. For non-resident foreigners in 2026/27: day AR$ 90,000, week AR$ 300,000 and season AR$ 450,000. Trolling is an extra permit. Values from the official portal, in pesos, read on 4 November 2026.' ],
				[ 'pregunta' => 'Is the Patagonian permit valid?', 'respuesta' => 'According to 2024 press, the Patagonian permit is not valid in Santa Cruz or Tierra del Fuego, so you need the provincial one. Confirm with your lodge or on the official portal before you pay.' ],
				[ 'pregunta' => 'Are there fly-only rivers?', 'respuesta' => 'Yes. The Gallegos Chico and the Chico Sur are fly only, with mandatory release. The annex we read does not impose fly on the main Gallegos channel, but we did not read the general regulation, so ask your guide.' ],
				[ 'pregunta' => 'Where is fishing banned?', 'respuesta' => 'On the Río Gallegos, 200 m upstream and downstream of the Güer Aike bridge, on both banks. On the Gallegos Chico, from the Puente Nuevo on Route 40 to its mouth. And in general within 200 m of the sources and mouths of linked watercourses.' ],
				[ 'pregunta' => 'How much does a week at a Gallegos lodge cost?', 'respuesta' => 'We cannot say from the operator\'s own source: Karku Fly Fishing and Estancia Las Buitreras publish no rates on the sites we read. Some agencies do publish prices, but we do not use them because we do not know what they include or which season they are from. Ask the lodge for the detail.' ],
				[ 'pregunta' => 'What does a Gallegos lodge include?', 'respuesta' => 'According to Karku Fly Fishing: transfers from Río Gallegos airport, guides, lodging in a cabin (two single rooms and one double, shared bathroom), breakfast, lunch and dinner, and it takes care of permits. It does not include laundry, equipment, alcoholic drinks or tips (it suggests at least 12% of the trip cost). It asks for a 50% non-refundable deposit and the balance 90 days before.' ],
				[ 'pregunta' => 'What is the best time to go?', 'respuesta' => 'Karku Fly Fishing puts the prime season from late December to April: January and February for the biggest, most aggressive fish, March and April for steadier action. It is one operator\'s opinion; the river opens in November.' ],
				[ 'pregunta' => 'How do I get there?', 'respuesta' => 'Through Río Gallegos airport. Karku Fly Fishing says its estancia is a 50-minute drive and that it provides transfers. Los Morros Lodge appears in a local directory 180 km from the city on Route 40.' ],
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
	if ( 10 !== count( $g['campos']['faqs'] ) ) {
		WP_CLI::error( "Se esperaban 10 preguntas ($i)" );
	}
}

foreach ( [ 'es', 'en' ] as $i ) {
	$g = $guias[ $i ];
	if ( pm_buscar( 'pm_destino', 'rio-gallegos', $i ) ) {
		WP_CLI::log( "rio-gallegos ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "rio-gallegos ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $g['fuentes'] ) . ' fuentes (title ' . mb_strlen( $g['campos']['seo_title'] ) . ', description ' . mb_strlen( $g['campos']['seo_description'] ) . ')' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $g['cuerpo'] ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i, 'fuentes' => $g['fuentes'] ];
	if ( 'es' === $i ) {
		$campos += [ 'region' => 'patagonia', 'provincia' => 'santa-cruz', 'lat' => -51.623, 'lon' => -69.216, 'especies' => [ 'trucha' ] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "✓ rio-gallegos ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
}

// --- Outfitters: "listado con información pública" (docs/prestadores.md) ----------------------
$nota_interna = 'Listado con información pública (sitio leído el 4/11/2026 con investigar.yml). Pescamapa no confirmó contacto, tarifa, disponibilidad ni habilitación. No publica tarifa en el sitio que leímos. Falta contactarlo.';
$prestadores  = [
	[
		'nombre' => 'Karku Fly Fishing', 'tipo' => [ 'alojamiento', 'operador' ], 'fuente' => $u['kcamp'], 'loc' => 'Estancia cerca de Río Gallegos, Santa Cruz', 'cap' => 4,
		'es' => 'Campamento de pesca con mosca en una estancia centenaria a 50 minutos del aeropuerto de Río Gallegos, sobre el río Gallegos. Según su sitio, recibe solo cuatro pescadores por semana, con más de 40 km de agua de acceso exclusivo y más de 40 pozones con nombre. Aloja en una cabaña con dos habitaciones individuales y una doble, con baño compartido, energía solar y Wi-Fi. Incluye traslados desde el aeropuerto, guías, desayuno, almuerzo y cena, y dice que se ocupa de los permisos; no incluye lavandería, equipo, bebidas alcohólicas ni propinas. Pide un 50 % de depósito no reembolsable. No publica tarifa.',
		'en' => 'Fly-fishing camp on a century-old estancia 50 minutes from Río Gallegos airport, on the Río Gallegos. According to its website it takes only four anglers a week, with more than 40 km of exclusive water and more than 40 named pools. It sleeps guests in a cabin with two single rooms and one double, shared bathroom, solar power and Wi-Fi. It includes airport transfers, guides, breakfast, lunch and dinner, and says it takes care of permits; it does not include laundry, equipment, alcoholic drinks or tips. It asks for a 50% non-refundable deposit. It publishes no rate.',
		'incluye' => [ 'alojamiento', 'comida', 'traslados', 'licencia' ], 'equipo' => [], 'idiomas' => [ 'en', 'es' ],
	],
	[
		'nombre' => 'Estancia Las Buitreras (Solid Adventures)', 'tipo' => [ 'alojamiento', 'operador' ], 'fuente' => $u['lb'], 'loc' => 'Estancia Las Buitreras, río Gallegos, Santa Cruz', 'cap' => 12,
		'es' => 'Lodge sobre el río Gallegos que opera Solid Adventures, a unos diez minutos a pie del río. Según su sitio tiene seis habitaciones dobles y dos individuales, todas con baño privado, y puede alojar hasta 14 huéspedes pero recibe un máximo de 12 pescadores por semana. Los guías hablan inglés. No publica tarifa en la página que leímos.',
		'en' => 'Lodge on the Río Gallegos run by Solid Adventures, about a ten-minute walk from the river. According to its website it has six double rooms and two single rooms, all with private bathrooms, and can sleep up to 14 guests but takes a maximum of 12 anglers a week. The guides speak English. It publishes no rate on the page we read.',
		'incluye' => [ 'alojamiento' ], 'equipo' => [], 'idiomas' => [ 'en' ],
	],
];
$creados = [];
foreach ( $prestadores as $p ) {
	if ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $p['nombre'], 'numberposts' => 1, 'fields' => 'ids' ] ) ) {
		WP_CLI::log( "· prestador {$p['nombre']} ya existe" );
		continue;
	}
	if ( ! $aplicar ) {
		WP_CLI::log( "· prestador {$p['nombre']}: se crearía (listado público, sin verificar, sin precio)" );
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $p['nombre'] ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	$f = [
		'tipo' => $p['tipo'], 'prest_destino' => 'rio-gallegos', 'localidad' => $p['loc'],
		'descripcion' => $p['es'], 'descripcion_en' => $p['en'],
		'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha-marron-anadroma' ], 'equipo' => $p['equipo'], 'capacidad' => $p['cap'],
		'lancha' => '', 'motor' => '', 'prest_idiomas' => $p['idiomas'], 'incluye' => $p['incluye'],
		'precio' => '', 'precio_fecha' => '',
		'prest_publico' => 1, 'prest_fuente' => $p['fuente'], 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'web' => $p['fuente'], 'notas' => $nota_interna,
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	$creados[ $p['nombre'] ] = $pid;
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok = true;
foreach ( $creados as $nombre => $pid ) {
	$r = null;
	foreach ( pm_prestadores_destino( 'rio-gallegos' ) as $x ) {
		if ( (int) $x['id'] === (int) $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && '' === $r['precio'];
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$pid " . ( $r ? 'aparece en la lista de rio-gallegos, sin precio' : 'no aparece en la lista de rio-gallegos' ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna ficha no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
