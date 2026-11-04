<?php
/**
 * Río Grande (Tierra del Fuego), 4/11/2026: destination guide in Spanish (source of the shared data) and English, plus four public-information
 * outfitter cards. Everything stated comes from what was read; what is missing is marked as such, not hidden. Facts:
 *  - Rules, season dates and permit values 2026/27: Resolución S.A. N° 259/2026, Secretaría de Ambiente (Ministerio de Producción y Ambiente de
 *    Tierra del Fuego), PDF read on 4/11/2026 with investigar.yml (docs/investigacion/ → rama investigacion, carpeta res-259-26-tdf-20261104),
 *    and the ministry page "Pesca deportiva TDF" (same day). The page still shows "hasta el 13 de abril de 2025" for the Río Grande; the resolution
 *    says 18 April 2027 and that is what the guide uses (the discrepancy is said openly).
 *  - Lodges: their own sites (Kau Tapen Rates 2027, Location; AHO Fishing Río Grande; San José), and KD Flyfishing, a trip seller, for Wuijoor Lodge.
 *    A rate appears only with its season and source. Agencies' rates are not used.
 *  - NOT read: Estancia María Behety, La Villa, Despedida Lodge (their sites did not open for our reader); Boletín Oficial text; river levels.
 * Idempotent: a guide that exists is not touched. Dry run first ($aplicar = false). Run with wp-script.yml (script = rio-grande-2026-11-04).
 */

$aplicar = false;
$hoy     = '20261104';

function pm_rg_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_rg_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_rg_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_rg_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'res'  => 'https://prodyambiente.tierradelfuego.gob.ar/wp-content/uploads/2026/10/Resolucion-N259-Temporada-pesca-2026.pdf',
	'pag'  => 'https://prodyambiente.tierradelfuego.gob.ar/pesca-deportiva-tdf/',
	'ktr'  => 'https://www.kautapen.com/rates.html',
	'ktl'  => 'https://www.kautapen.com/location.html',
	'aho'  => 'https://www.argentinaheritagefishing.com/rio-grande',
	'sj'   => 'https://sanjoseflyfishing.com/',
	'wuij' => 'https://kdflyfishing.com/destination/wuijoor-lodge',
	'rn24' => 'https://www.rionegro.com.ar/?p=3837803',
];
$fuentes_es = [
	[ 'Resolución S.A. N.º 259/2026 de la Secretaría de Ambiente de Tierra del Fuego: reglamento, permisos y valores de la temporada 2026/27 (PDF oficial, leído el 4/11/2026)', $u['res'], 1 ],
	[ 'Ministerio de Producción y Ambiente de Tierra del Fuego: pesca deportiva, temporada 2026-2027 (página oficial, leída el 4/11/2026)', $u['pag'], 1 ],
	[ 'Kau Tapen Lodge: tarifas 2027 (operador)', $u['ktr'], 0 ],
	[ 'Kau Tapen Lodge: ubicación y cómo llegar (operador)', $u['ktl'], 0 ],
	[ 'AHO Fishing: Río Grande (operador)', $u['aho'], 0 ],
	[ 'San José Lodge (operador)', $u['sj'], 0 ],
	[ 'KD Flyfishing: Wuijoor Lodge, río Menéndez (vendedor de viajes)', $u['wuij'], 0 ],
	[ 'Diario Río Negro, 15/10/2024: dónde vale el permiso patagónico (prensa, de la temporada 2024/25)', $u['rn24'], 0 ],
];
$fuentes_en = [
	[ 'Resolución S.A. No. 259/2026 of the Tierra del Fuego Environment Secretariat: regulation, permits and values for the 2026/27 season (official PDF, read on 4 November 2026)', $u['res'], 1 ],
	[ 'Tierra del Fuego Ministry of Production and Environment: sport fishing, 2026-2027 season (official page, read on 4 November 2026)', $u['pag'], 1 ],
	[ 'Kau Tapen Lodge: 2027 rates (operator)', $u['ktr'], 0 ],
	[ 'Kau Tapen Lodge: location and how to get there (operator)', $u['ktl'], 0 ],
	[ 'AHO Fishing: Río Grande (operator)', $u['aho'], 0 ],
	[ 'San José Lodge (operator)', $u['sj'], 0 ],
	[ 'KD Flyfishing: Wuijoor Lodge, Menéndez River (trip seller)', $u['wuij'], 0 ],
	[ 'Diario Río Negro, 15 October 2024: where the Patagonian permit is valid (press, 2024/25 season)', $u['rn24'], 0 ],
];
$filas = fn( array $f ) => array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $f );

// --- Spanish (source of the shared data) -------------------------------------------------
$cuerpo_es = implode( "\n\n", [
	pm_rg_p( '<strong>Río Grande</strong>, en el norte de la isla de Tierra del Fuego, es la base para pescar la <strong>trucha marrón anádroma</strong> (la que vive en el mar y sube al río a desovar) en el <strong>río Grande</strong> y en su afluente el <strong>Menéndez</strong>. Los lodges están en estancias a entre 50 y 60 minutos del aeropuerto. Esta página reúne las reglas 2026/27 leídas del texto oficial, el permiso y lo que cuesta, lo que publican los lodges y lo que todavía no confirmamos.' ),
	pm_rg_h( 'La temporada 2026/27' ),
	pm_rg_p( 'La Secretaría de Ambiente abrió la temporada con la <strong>Resolución S.A. N.º 259/2026</strong>: empezó el <strong>30 de octubre de 2026</strong> y termina el <strong>2 de mayo de 2027</strong> inclusive; después rige la veda en toda la provincia. En el <strong>río Grande y sus afluentes</strong> (Candelaria, Ona, Menéndez, Rasmussen, Moneta y Herminita) la pesca se habilita el 30 de octubre y cierra el <strong>18 de abril de 2027</strong>, inclusive.' ),
	pm_rg_p( 'Una aclaración para que no te confundas: la página del Ministerio todavía dice, para el río Grande, "hasta el 13 de abril de 2025", una fecha de la temporada pasada que quedó sin actualizar. Seguimos la resolución, que es el texto que firma la norma, y por eso publicamos el 18 de abril de 2027.' ),
	pm_rg_h( 'Reglas generales que leímos' ),
	pm_rg_lista( [
		'<strong>Devolución obligatoria</strong> de todas las piezas en los ambientes provinciales desde el <strong>1 de marzo de 2027</strong> hasta el cierre. Antes de esa fecha, y salvo en los ambientes con reglas propias, cada pescador puede sacrificar <strong>un ejemplar por día</strong> y no puede transportar más de uno.',
		'<strong>Modalidades permitidas:</strong> cuchara o spinning, y mosca. Un solo señuelo artificial por caña, con un anzuelo simple o doble. En spinning se permite el buldo y la mosca.',
		'<strong>Prohibido:</strong> el trolling o arrastre desde una embarcación a motor, la carnada, el lastre, el garfio, las redes, los espineles y los señuelos con pilas o baterías. La sanción alcanza al pescador y al conductor de la embarcación.',
		'<strong>Pesca solo de día</strong>, entre los crepúsculos que publica el Servicio de Hidrografía Naval. La pesca nocturna está prohibida en toda la provincia.',
		'El salmón chinook y el salmón coho figuran entre las especies permitidas, sujetas a las modalidades y condiciones de cada ambiente.',
	] ),
	pm_rg_h( 'El río Grande y sus afluentes' ),
	pm_rg_lista( [
		'Solo se permite la pesca con <strong>devolución obligatoria de todos los salmónidos</strong>, del 30 de octubre de 2026 al 18 de abril de 2027.',
		'Los pescadores registrados en el <strong>Sistema de Accesos de Pesca de Tierra del Fuego (SAPTDF)</strong> y con <strong>reserva previa</strong> acceden al sector "El Tropezón" y a los demás accesos habilitados. Quien reserva y no avisa con 24 horas de anticipación puede ser suspendido del sistema.',
		'<strong>Río Claro:</strong> solo un tramo, con devolución obligatoria, a pie, con reserva en el SAPTDF y un cupo de 10 cañas por día.',
		'<strong>Prohibido pescar todo el año</strong> en el arroyo Mimica, el río La Unión o In, el chorrillo Los Salmones, la laguna San Luis y el tramo del río Valdez entre el puente de la ruta nacional 3 y el lago Fagnano. El lago Yakush y el río Indio están cerrados mientras dure el cierre preventivo de la Reserva Provincial Corazón de la Isla.',
	] ),
	pm_rg_h( 'El permiso y cuánto cuesta' ),
	pm_rg_p( 'El permiso es <strong>provincial</strong>: se registra en <strong>www.siped.ar</strong> y se vende en comercios autorizados de Río Grande, Ushuaia y Tolhuin (entre ellos Angler\'s Aventuras Fly Shop, El Robalo y la Asociación Riograndense de Pesca con Mosca, en Río Grande). Los valores de la resolución para <strong>extranjeros no residentes</strong>, en pesos argentinos:' ),
	pm_rg_lista( [
		'<strong>Permiso ordinario:</strong> un día $78.000; tres días $117.000; siete días $195.000; temporada $260.000.',
		'<strong>Permiso adicional por zona preferencial, ríos Grande y Menéndez:</strong> un día $59.000; tres días $163.000; siete días $260.000; temporada $390.000. Es adicional al ordinario.',
		'Para extranjeros de países limítrofes, el adicional de los ríos Grande y Menéndez es de $65.000 por día, $78.000 por tres días, $130.000 por siete días y $175.000 por temporada.',
		'<strong>Residentes en el país:</strong> un día $26.000; tres días $39.000; siete días $65.000; temporada $90.000; de 13 a 17 años, temporada $20.000. Sin cargo para menores de 12 años, personas con discapacidad, jubilados y pensionados, solo para pesca con devolución.',
	] ),
	pm_rg_p( 'Según la prensa de 2024, el permiso patagónico no vale en Tierra del Fuego ni en Santa Cruz. Un lodge puede incluir o no el permiso en su tarifa: leé qué dice cada uno.' ),
	pm_rg_h( 'Cuándo van los peces' ),
	pm_rg_p( 'Los operadores no coinciden, y lo decimos tal cual. AHO Fishing dice que hacia mediados de noviembre empiezan a volver del mar y que a fines de enero la mayor parte de la población ya está en el río. KD Flyfishing, que vende viajes a Wuijoor Lodge, ubica la temporada de truchas de mar entre diciembre y abril, con fines de febrero a mediados de abril como los meses de mayor producción. Kau Tapen Lodge abre su temporada 2027 el 26 de diciembre de 2026 y la cierra el 17 de abril de 2027. Para elegir la semana, preguntale a tu lodge.' ),
	pm_rg_h( 'Cómo llegar' ),
	pm_rg_p( 'Kau Tapen Lodge describe el vuelo de Buenos Aires a Río Grande como de unas tres horas y el traslado del aeropuerto al lodge como de 50 a 60 minutos. KD Flyfishing da 50 minutos desde el aeropuerto de Río Grande a Wuijoor Lodge. Ushuaia es la alternativa, a unas tres horas por ruta. Son los tiempos de cada operador.' ),
	pm_rg_pendiente( 'Estancia María Behety, La Villa María Behety y Despedida Lodge (sus sitios no abrieron en nuestro lector); texto de la resolución en el Boletín Oficial; calendario por mes con una fuente técnica; alturas del río; foto con su crédito; guías independientes.' ),
	pm_rg_h( 'Lodges y operadores' ),
	pm_rg_p( 'Abajo están los que encontramos con información pública; todavía no los contactamos, por eso figuran marcados. Una tarifa se muestra solo cuando el operador la publica con su temporada.' ),
] );

// --- English ---------------------------------------------------------------------------
$cuerpo_en = implode( "\n\n", [
	pm_rg_p( '<strong>Río Grande</strong>, in the north of the island of Tierra del Fuego, is the base for fishing <strong>sea-run brown trout</strong> (the trout that lives at sea and returns to the river to spawn) on the <strong>Río Grande</strong> and its tributary the <strong>Menéndez</strong>. The lodges sit on estancias 50 to 60 minutes from the airport. This page brings together the 2026/27 rules read from the official text, the permit and what it costs, what the lodges publish and what we have not confirmed yet.' ),
	pm_rg_h( 'The 2026/27 season' ),
	pm_rg_p( 'The Environment Secretariat opened the season with <strong>Resolución S.A. No. 259/2026</strong>: it began on <strong>30 October 2026</strong> and ends on <strong>2 May 2027</strong> inclusive; after that a closed season applies across the province. On the <strong>Río Grande and its tributaries</strong> (Candelaria, Ona, Menéndez, Rasmussen, Moneta and Herminita) fishing opens on 30 October and closes on <strong>18 April 2027</strong>, inclusive.' ),
	pm_rg_p( 'A note so you are not confused: the ministry page still says, for the Río Grande, "until 13 April 2025", a date from last season that was never updated. We follow the resolution, which is the signed text, so we publish 18 April 2027.' ),
	pm_rg_h( 'General rules we have read' ),
	pm_rg_lista( [
		'<strong>Mandatory release</strong> of every fish in provincial waters from <strong>1 March 2027</strong> to the close of the season. Before that date, except on waters with their own rules, each angler may keep <strong>one fish a day</strong> and may not carry more than one.',
		'<strong>Allowed methods:</strong> spoon or spinning, and fly. One artificial lure per rod, with a single or double hook. Spinning anglers may use a bubble float and a fly.',
		'<strong>Banned:</strong> trolling or dragging from a motorised boat, bait, lead weights, gaffs, nets, longlines and lures with batteries. The penalty reaches both the angler and the boat operator.',
		'<strong>Daylight fishing only</strong>, between the twilight times published by the Naval Hydrographic Service. Night fishing is banned across the province.',
		'Chinook and coho salmon are among the permitted species, subject to the methods and conditions of each water.',
	] ),
	pm_rg_h( 'The Río Grande and its tributaries' ),
	pm_rg_lista( [
		'Only <strong>catch and release of all salmonids</strong> is allowed, from 30 October 2026 to 18 April 2027.',
		'Anglers registered in the <strong>Tierra del Fuego Fishing Access System (SAPTDF)</strong> with a <strong>prior booking</strong> can reach the "El Tropezón" sector and the other open accesses. Booking and not cancelling 24 hours ahead can get you suspended from the system.',
		'<strong>Río Claro:</strong> one stretch only, catch and release, on foot, booked through SAPTDF, with a quota of 10 rods a day.',
		'<strong>Fishing is banned all year</strong> on the Mimica stream, the La Unión or In River, the Los Salmones brook, Laguna San Luis and the stretch of the Valdez River between the Route 3 bridge and Lake Fagnano. Lake Yakush and the Indio River are closed while the preventive closure of the Corazón de la Isla Provincial Reserve lasts.',
	] ),
	pm_rg_h( 'The permit and what it costs' ),
	pm_rg_p( 'The permit is <strong>provincial</strong>: you register at <strong>www.siped.ar</strong> and buy it at authorised shops in Río Grande, Ushuaia and Tolhuin (among them Angler\'s Aventuras Fly Shop, El Robalo and the Asociación Riograndense de Pesca con Mosca, in Río Grande). The resolution\'s values for <strong>non-resident foreigners</strong>, in Argentine pesos:' ),
	pm_rg_lista( [
		'<strong>Ordinary permit:</strong> one day AR$ 78,000; three days AR$ 117,000; seven days AR$ 195,000; season AR$ 260,000.',
		'<strong>Additional preferential-zone permit, Grande and Menéndez rivers:</strong> one day AR$ 59,000; three days AR$ 163,000; seven days AR$ 260,000; season AR$ 390,000. It comes on top of the ordinary permit.',
		'For foreigners from bordering countries, the additional permit for the Grande and Menéndez is AR$ 65,000 a day, AR$ 78,000 for three days, AR$ 130,000 for seven days and AR$ 175,000 for the season.',
		'<strong>Residents of Argentina:</strong> one day AR$ 26,000; three days AR$ 39,000; seven days AR$ 65,000; season AR$ 90,000; ages 13 to 17, season AR$ 20,000. No charge for children under 12, people with disabilities, retirees and pensioners, for catch-and-release fishing only.',
	] ),
	pm_rg_p( 'According to 2024 press, the Patagonian permit is not valid in Tierra del Fuego or Santa Cruz. A lodge may or may not include the permit in its rate: read what each one says.' ),
	pm_rg_h( 'When the fish are in' ),
	pm_rg_p( 'The operators do not agree, and we say so. AHO Fishing says the fish start returning from the sea around mid-November and that by the end of January most of the run is in the river. KD Flyfishing, which sells trips to Wuijoor Lodge, puts the sea-trout season between December and April, with late February to mid-April as the prime months. Kau Tapen Lodge opens its 2027 season on 26 December 2026 and closes it on 17 April 2027. To choose your week, ask your lodge.' ),
	pm_rg_h( 'Getting there' ),
	pm_rg_p( 'Kau Tapen Lodge describes the flight from Buenos Aires to Río Grande as about three hours and the transfer from the airport to the lodge as 50 to 60 minutes. KD Flyfishing gives 50 minutes from Río Grande airport to Wuijoor Lodge. Ushuaia is the alternative, about three hours by road. These are each operator\'s own times.' ),
	pm_rg_pendiente( 'Estancia María Behety, La Villa María Behety and Despedida Lodge (their sites did not open in our reader); the text of the resolution in the Boletín Oficial; calendar by month with a technical source; river levels; photo with its credit; independent guides.' ),
	pm_rg_h( 'Lodges and outfitters' ),
	pm_rg_p( 'Below are the ones we found with public information; we have not contacted them yet, which is why they are marked. A rate is shown only when the operator publishes it with its season.' ),
] );

$comun = [
	'clave'       => 'rio-grande',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'reglamento_autoridad' => 'Secretaría de Ambiente de Tierra del Fuego (Ministerio de Producción y Ambiente)',
	'reglamento_url'       => $u['pag'],
	'reglamento_verificado' => $hoy,
];

$guias = [
	'es' => [
		'titulo' => 'Río Grande (Tierra del Fuego)', 'slug' => 'pesca-en-rio-grande', 'cuerpo' => $cuerpo_es, 'fuentes' => $filas( $fuentes_es ),
		'campos' => [
			'h1'          => 'Pesca en Río Grande, Tierra del Fuego: trucha de mar, reglas y permiso 2026/27',
			'bajada'      => 'La trucha marrón anádroma del río Grande y del Menéndez. Las reglas 2026/27 leídas del texto oficial, el permiso, las tarifas que publican los lodges y lo que falta confirmar.',
			'modalidades' => "Mosca (flycast) con caña de dos manos y de una mano\nSpinning (cuchara)\nCaptura y devolución",
			'epoca'       => 'Del 30 de octubre de 2026 al 2 de mayo de 2027; en el río Grande y sus afluentes, hasta el 18 de abril de 2027.',
			'seo_title'   => 'Pesca en Río Grande, Tierra del Fuego: reglas y permiso',
			'seo_description' => 'Guía de pesca de trucha marrón anádroma en Río Grande, Tierra del Fuego: reglas 2026/27, permiso, tarifas publicadas por los lodges y cómo llegar.',
			'temporada'   => [
				[ 'meses' => '30 de octubre', 'que' => 'Abre la temporada 2026/27 en toda la provincia (Resolución S.A. 259/2026).' ],
				[ 'meses' => 'Diciembre a abril', 'que' => 'Temporada de los lodges: Kau Tapen del 26 de diciembre de 2026 al 17 de abril de 2027; Wuijoor Lodge de diciembre a abril, con lo mejor de fines de febrero a mediados de abril (según el vendedor de viajes).' ],
				[ 'meses' => '1 de marzo', 'que' => 'Desde esta fecha la devolución de las piezas es obligatoria en toda la provincia.' ],
				[ 'meses' => '18 de abril', 'que' => 'Cierra la pesca en el río Grande y sus afluentes (2027). El resto de la provincia cierra el 2 de mayo de 2027.' ],
				[ 'meses' => 'Calendario por mes', 'que' => 'PENDIENTE: cuándo entran los peces y cómo cambia el agua mes a mes, con una fuente técnica.' ],
			],
			'reglamento_resumen' => 'Resolución S.A. N.º 259/2026 de la Secretaría de Ambiente de Tierra del Fuego (reglamento, permisos y valores de la temporada 2026/27), leída en el PDF oficial el 4/11/2026. Temporada del 30/10/2026 al 2/5/2027; en el río Grande y sus afluentes, devolución obligatoria hasta el 18/4/2027. Los valores de permiso son los de la resolución; confirmalos al comprar.',
			'aguas' => [
				[ 'nombre' => 'Río Grande', 'nota' => 'Devolución obligatoria de todos los salmónidos; acceso con reserva en el SAPTDF; hasta el 18 de abril de 2027.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Menéndez', 'nota' => 'Afluente del Grande, con el mismo régimen de devolución obligatoria.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Claro', 'nota' => 'Solo un tramo, a pie, con reserva en el SAPTDF y 10 cañas por día.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => '¿Cuándo es la temporada de pesca en Río Grande?', 'respuesta' => 'La temporada 2026/27 empezó el 30 de octubre de 2026 y termina el 2 de mayo de 2027. En el río Grande y sus afluentes la pesca cierra el 18 de abril de 2027 (Resolución S.A. 259/2026). La página del Ministerio todavía muestra la fecha de la temporada pasada, pero la resolución dice 2027.' ],
				[ 'pregunta' => '¿Puedo quedarme con los peces que pesco?', 'respuesta' => 'En el río Grande y sus afluentes, no: solo se permite la pesca con devolución obligatoria de todos los salmónidos. En otros ambientes de la provincia podés sacrificar un ejemplar por día hasta el 28 de febrero de 2027; desde el 1 de marzo, la devolución es obligatoria en toda la provincia.' ],
				[ 'pregunta' => '¿Qué permiso necesito y cuánto cuesta?', 'respuesta' => 'El permiso provincial de Tierra del Fuego, que se registra en www.siped.ar. Para extranjeros no residentes en 2026/27: un día $78.000, tres días $117.000, siete días $195.000, temporada $260.000. Para pescar en los ríos Grande y Menéndez se suma un permiso adicional de zona preferencial: un día $59.000, tres días $163.000, siete días $260.000, temporada $390.000. Valores de la Resolución 259/2026, en pesos.' ],
				[ 'pregunta' => '¿Me sirve el permiso patagónico?', 'respuesta' => 'Según la prensa de 2024, el permiso patagónico no vale en Tierra del Fuego ni en Santa Cruz. El de Tierra del Fuego es provincial. Confirmalo con tu lodge o con la Secretaría de Ambiente antes de comprar.' ],
				[ 'pregunta' => '¿Se puede pescar con carnada o hacer trolling?', 'respuesta' => 'No. Solo se permite la cuchara o spinning y la mosca, con un único señuelo artificial y un anzuelo simple o doble. Están prohibidos la carnada, el trolling desde una embarcación a motor, el lastre, el garfio, las redes y los señuelos con pilas.' ],
				[ 'pregunta' => '¿Se puede pescar de noche?', 'respuesta' => 'No. Solo se pesca entre los crepúsculos que publica el Servicio de Hidrografía Naval, y la pesca nocturna está prohibida en toda la provincia.' ],
				[ 'pregunta' => '¿Cuánto cuesta una semana en un lodge de Río Grande?', 'respuesta' => 'Kau Tapen Lodge publica en su sitio, para la temporada del 26 de diciembre de 2026 al 17 de abril de 2027, US$ 13.950 por persona y semana del 26/12 al 9/1, US$ 14.950 del 9/1 al 20/3 y US$ 12.950 del 20/3 al 17/4, en habitación single y con guía compartido (sitio del lodge, leído el 4/11/2026). Los demás operadores que encontramos no publican tarifa.' ],
				[ 'pregunta' => '¿Qué incluye la tarifa de un lodge?', 'respuesta' => 'En el caso de Kau Tapen: alojamiento, traslado desde el aeropuerto de Río Grande, todas las comidas y bebidas, guía profesional (dos pescadores por guía) y equipo de préstamo. No incluye pasajes, la licencia de pesca (el lodge calcula unos US$ 550 por pescador), la lavandería ni las propinas. Pide un depósito del 50 % y no es reembolsable. Cada lodge incluye cosas distintas: pedí el detalle.' ],
				[ 'pregunta' => '¿Cuál es el mejor momento para ir?', 'respuesta' => 'Depende de a quién le preguntes. AHO Fishing dice que los peces empiezan a volver a mediados de noviembre y que a fines de enero la mayor parte ya está en el río; un vendedor de viajes a Wuijoor Lodge ubica lo mejor entre fines de febrero y mediados de abril. Preguntale a tu lodge por la semana que elegís.' ],
				[ 'pregunta' => '¿Cómo llego a Río Grande?', 'respuesta' => 'Hay vuelos de Buenos Aires a Río Grande, de unas tres horas según Kau Tapen Lodge, y el traslado al lodge lleva de 50 a 60 minutos. También se puede llegar por Ushuaia, a unas tres horas por ruta.' ],
			],
		],
	],
	'en' => [
		'titulo' => 'Río Grande (Tierra del Fuego)', 'slug' => 'fishing-in-rio-grande', 'cuerpo' => $cuerpo_en, 'fuentes' => $filas( $fuentes_en ),
		'campos' => [
			'h1'          => 'Fishing in Río Grande, Tierra del Fuego: sea-run brown trout, rules and 2026/27 permit',
			'bajada'      => 'The sea-run brown trout of the Río Grande and the Menéndez. The 2026/27 rules read from the official text, the permit, the rates the lodges publish and what is still to confirm.',
			'modalidades' => "Fly fishing with two-handed and single-handed rods\nSpinning (spoon)\nCatch and release",
			'epoca'       => '30 October 2026 to 2 May 2027; on the Río Grande and its tributaries, until 18 April 2027.',
			'seo_title'   => 'Fishing in Río Grande, Tierra del Fuego: rules, permit',
			'seo_description' => 'Fishing guide to sea-run brown trout in Río Grande, Tierra del Fuego: 2026/27 rules, permit prices, lodge rates as published and how to get there.',
			'temporada'   => [
				[ 'meses' => '30 October', 'que' => '2026/27 season opens across the province (Resolución S.A. 259/2026).' ],
				[ 'meses' => 'December to April', 'que' => 'Lodge season: Kau Tapen from 26 December 2026 to 17 April 2027; Wuijoor Lodge December to April, best from late February to mid-April (per the trip seller).' ],
				[ 'meses' => '1 March', 'que' => 'From this date release is mandatory across the province.' ],
				[ 'meses' => '18 April', 'que' => 'Fishing closes on the Río Grande and its tributaries (2027). The rest of the province closes on 2 May 2027.' ],
				[ 'meses' => 'Calendar by month', 'que' => 'PENDIENTE: when the fish come in and how the water changes month by month, with a technical source.' ],
			],
			'reglamento_resumen' => 'Resolución S.A. No. 259/2026 of the Tierra del Fuego Environment Secretariat (regulation, permits and values for the 2026/27 season), read in the official PDF on 4 November 2026. Season from 30 October 2026 to 2 May 2027; on the Río Grande and its tributaries, mandatory release until 18 April 2027. Permit values are those of the resolution; confirm them when you buy.',
			'aguas' => [
				[ 'nombre' => 'Río Grande', 'nota' => 'Mandatory release of all salmonids; access by booking on SAPTDF; until 18 April 2027.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Menéndez River', 'nota' => 'Tributary of the Grande, with the same mandatory release.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Claro River', 'nota' => 'One stretch only, on foot, booked on SAPTDF, 10 rods a day.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => 'When is the fishing season in Río Grande?', 'respuesta' => 'The 2026/27 season began on 30 October 2026 and ends on 2 May 2027. On the Río Grande and its tributaries fishing closes on 18 April 2027 (Resolución S.A. 259/2026). The ministry page still shows last season\'s date, but the resolution says 2027.' ],
				[ 'pregunta' => 'Can I keep the fish I catch?', 'respuesta' => 'On the Río Grande and its tributaries, no: only catch and release of all salmonids is allowed. On other waters of the province you may keep one fish a day until 28 February 2027; from 1 March release is mandatory across the province.' ],
				[ 'pregunta' => 'Which permit do I need and how much is it?', 'respuesta' => 'The Tierra del Fuego provincial permit, registered at www.siped.ar. For non-resident foreigners in 2026/27: one day AR$ 78,000, three days AR$ 117,000, seven days AR$ 195,000, season AR$ 260,000. To fish the Grande and Menéndez rivers you add a preferential-zone permit: one day AR$ 59,000, three days AR$ 163,000, seven days AR$ 260,000, season AR$ 390,000. Values from Resolución 259/2026, in pesos.' ],
				[ 'pregunta' => 'Is the Patagonian permit valid?', 'respuesta' => 'According to 2024 press, the Patagonian permit is not valid in Tierra del Fuego or Santa Cruz. The Tierra del Fuego one is provincial. Confirm with your lodge or the Environment Secretariat before buying.' ],
				[ 'pregunta' => 'Can I use bait or troll?', 'respuesta' => 'No. Only spoon or spinning and fly are allowed, with a single artificial lure and a single or double hook. Bait, trolling from a motorised boat, lead weights, gaffs, nets and lures with batteries are banned.' ],
				[ 'pregunta' => 'Can I fish at night?', 'respuesta' => 'No. Fishing is allowed only between the twilight times published by the Naval Hydrographic Service, and night fishing is banned across the province.' ],
				[ 'pregunta' => 'How much does a week at a Río Grande lodge cost?', 'respuesta' => 'Kau Tapen Lodge publishes on its website, for the season from 26 December 2026 to 17 April 2027, USD 13,950 per person per week from 26 Dec to 9 Jan, USD 14,950 from 9 Jan to 20 Mar and USD 12,950 from 20 Mar to 17 Apr, in a single room with a shared guide (lodge website, read on 4 November 2026). The other operators we found publish no rate.' ],
				[ 'pregunta' => 'What does a lodge rate include?', 'respuesta' => 'For Kau Tapen: lodging, transfer from Río Grande airport, all meals and drinks, a professional guide (two anglers per guide) and loaner equipment. It does not include flights, the fishing licence (the lodge estimates about USD 550 per angler), laundry or tips. It asks for a 50% deposit and it is non-refundable. Each lodge includes different things: ask for the detail.' ],
				[ 'pregunta' => 'What is the best time to go?', 'respuesta' => 'It depends on whom you ask. AHO Fishing says the fish start returning in mid-November and that by the end of January most of the run is in the river; a trip seller for Wuijoor Lodge puts the best from late February to mid-April. Ask your lodge about the week you choose.' ],
				[ 'pregunta' => 'How do I get to Río Grande?', 'respuesta' => 'There are flights from Buenos Aires to Río Grande, about three hours according to Kau Tapen Lodge, and the transfer to the lodge takes 50 to 60 minutes. You can also arrive through Ushuaia, about three hours by road.' ],
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
	if ( pm_buscar( 'pm_destino', 'rio-grande', $i ) ) {
		WP_CLI::log( "rio-grande ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "rio-grande ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $g['fuentes'] ) . ' fuentes (title ' . mb_strlen( $g['campos']['seo_title'] ) . ', description ' . mb_strlen( $g['campos']['seo_description'] ) . ')' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $g['cuerpo'] ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i, 'fuentes' => $g['fuentes'] ];
	if ( 'es' === $i ) {
		$campos += [ 'region' => 'patagonia', 'provincia' => 'tierra-del-fuego', 'lat' => -53.787, 'lon' => -67.709, 'especies' => [ 'trucha' ] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "✓ rio-grande ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
}

// --- Outfitters: "listado con información pública" (docs/prestadores.md) ----------------------
$nota_interna = 'Listado con información pública (sitio leído el 4/11/2026 con investigar.yml). Pescamapa no confirmó contacto, disponibilidad ni habilitación. Falta contactarlo.';
$prestadores  = [
	[
		'nombre' => 'Kau Tapen Lodge', 'tipo' => [ 'alojamiento' ], 'fuente' => $u['ktr'], 'loc' => 'Río Grande, Tierra del Fuego', 'cap' => 10,
		'es' => 'Lodge sobre el río Grande, en funcionamiento desde 1984 según su sitio. Aloja hasta 10 pescadores (a veces 12) en habitaciones individuales con baño. Su sitio publica para la temporada del 26 de diciembre de 2026 al 17 de abril de 2027 entre US$ 12.950 y US$ 14.950 por persona y semana según la fecha, en habitación single y con guía compartido (dos pescadores por guía). Incluye traslado desde el aeropuerto de Río Grande, comidas y bebidas, guía y equipo de préstamo; no incluye pasajes, la licencia de pesca, la lavandería ni las propinas. Pide un depósito del 50 % no reembolsable.',
		'en' => 'Lodge on the Río Grande, operating since 1984 according to its website. It sleeps up to 10 anglers (sometimes 12) in single rooms with a bathroom. Its website publishes, for the season from 26 December 2026 to 17 April 2027, between USD 12,950 and USD 14,950 per person per week depending on the date, in a single room with a shared guide (two anglers per guide). It includes the transfer from Río Grande airport, meals and drinks, a guide and loaner equipment; it does not include flights, the fishing licence, laundry or tips. It asks for a 50% non-refundable deposit.',
		'precio' => 'US$ 12.950 a 14.950 por persona y semana, según la fecha (temporada 26/12/2026 al 17/4/2027; habitación single, guía compartido)',
		'precio_en' => 'USD 12,950 to 14,950 per person per week, depending on the date (season 26 Dec 2026 to 17 Apr 2027; single room, shared guide)',
		'incluye' => [ 'alojamiento', 'comida', 'bebidas', 'traslados', 'equipos' ], 'equipo' => [ 'equipos' ], 'idiomas' => [ 'en' ],
	],
	[
		'nombre' => 'San José Lodge', 'tipo' => [ 'alojamiento' ], 'fuente' => $u['sj'], 'loc' => 'Estancia San José, Río Grande, Tierra del Fuego', 'cap' => 0,
		'es' => 'Lodge de pesca de trucha marrón anádroma en la Estancia San José, a una hora de la ciudad de Río Grande según su sitio. Su sitio no publica tarifas ni capacidad; solo invita a planificar el viaje.',
		'en' => 'Lodge for sea-run brown trout at Estancia San José, an hour from the city of Río Grande according to its website. Its website publishes no rates or capacity; it only invites you to plan your trip.',
		'precio' => '', 'precio_en' => '', 'incluye' => [], 'equipo' => [], 'idiomas' => [ 'en' ],
	],
	[
		'nombre' => 'AHO Fishing, Río Grande', 'tipo' => [ 'operador' ], 'fuente' => $u['aho'], 'loc' => 'Río Grande, Tierra del Fuego', 'cap' => 8,
		'es' => 'Programa de pesca en un lodge sobre el río Grande. Según su sitio tiene cuatro habitaciones dobles con baño privado para un máximo de ocho huéspedes, con comidas y bebidas incluidas, y solo se permiten cuatro cañas en el río a la vez. Recomienda cañas de dos manos de 7 a 9 y de 12 a 15 pies. No publica tarifa.',
		'en' => 'Fishing programme at a lodge on the Río Grande. According to its website it has four double rooms with private bathrooms for a maximum of eight guests, with meals and drinks included, and only four rods are allowed on the river at once. It recommends two-handed 7 to 9 weight rods of 12 to 15 feet. It publishes no rate.',
		'precio' => '', 'precio_en' => '', 'incluye' => [ 'alojamiento', 'comida', 'bebidas' ], 'equipo' => [], 'idiomas' => [ 'en' ],
	],
	[
		'nombre' => 'Wuijoor Lodge', 'tipo' => [ 'alojamiento' ], 'fuente' => $u['wuij'], 'loc' => 'Estancia Laura, río Menéndez, Tierra del Fuego', 'cap' => 6,
		'es' => 'Lodge sobre el río Menéndez, dentro de la Estancia Laura, a unos 60 km al sudoeste de la ciudad de Río Grande, con capacidad para seis pescadores en tres habitaciones dobles. Fundado en 2023 y abierto para pescar en 2024, según KD Flyfishing, que vende viajes a este lodge. La estadía habitual es de siete noches con seis días de pesca; el traslado desde el aeropuerto de Río Grande lleva 50 minutos. No publicamos tarifa: la página que leímos no la muestra.',
		'en' => 'Lodge on the Menéndez River, within Estancia Laura, about 60 km south-west of the city of Río Grande, with room for six anglers in three double rooms. Founded in 2023 and open for fishing in 2024, according to KD Flyfishing, which sells trips to this lodge. The usual stay is seven nights with six fishing days; the transfer from Río Grande airport takes 50 minutes. We publish no rate: the page we read shows none.',
		'precio' => '', 'precio_en' => '', 'incluye' => [], 'equipo' => [], 'idiomas' => [ 'en' ],
	],
];
$creados = [];
foreach ( $prestadores as $p ) {
	if ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $p['nombre'], 'numberposts' => 1, 'fields' => 'ids' ] ) ) {
		WP_CLI::log( "· prestador {$p['nombre']} ya existe" );
		continue;
	}
	if ( ! $aplicar ) {
		WP_CLI::log( "· prestador {$p['nombre']}: se crearía (listado público, sin verificar)" . ( $p['precio'] ? ' con precio fechado' : ', sin precio' ) );
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $p['nombre'] ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	$f = [
		'tipo' => $p['tipo'], 'prest_destino' => 'rio-grande', 'localidad' => $p['loc'],
		'descripcion' => $p['es'], 'descripcion_en' => $p['en'],
		'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha-marron-anadroma' ], 'equipo' => $p['equipo'], 'capacidad' => $p['cap'],
		'lancha' => '', 'motor' => '', 'prest_idiomas' => $p['idiomas'], 'incluye' => $p['incluye'],
		'precio' => $p['precio'], 'precio_en' => $p['precio_en'], 'precio_fecha' => $p['precio'] ? $hoy : '',
		'prest_publico' => 1, 'prest_fuente' => $p['fuente'], 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'web' => $p['fuente'], 'notas' => $nota_interna,
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	$creados[ $p['nombre'] ] = [ $pid, (bool) $p['precio'] ];
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok = true;
foreach ( $creados as $nombre => [ $pid, $con_precio ] ) {
	$r = null;
	foreach ( pm_prestadores_destino( 'rio-grande' ) as $x ) {
		if ( (int) $x['id'] === (int) $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && ( ! $con_precio || ( '' !== $r['precio'] && '2026-11-04' === $r['precio_fecha'] ) );
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$pid " . ( $r ? 'aparece en la lista de rio-grande' . ( $con_precio ? ' con precio fechado' : '' ) : 'no aparece en la lista de rio-grande' ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna ficha no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
