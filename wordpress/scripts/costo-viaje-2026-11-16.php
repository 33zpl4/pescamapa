<?php
/**
 * Note "Argentina fishing trip cost 2026/27: lodges, packages, day trips" (es/en/pt siblings, clave cuanto-cuesta-viaje-pesca-argentina), 16/11/2026.
 * Why: owner's review (16/11/2026): the site has the regional price notes but no overview that answers "how much does a fishing trip to Argentina cost" and sends to them.
 * This is the OVERVIEW (it does not repeat the regional tables): one table by type of trip, what changes the cost, what a price usually includes, how to use the comparison.
 * Every figure comes from a dated note already published (Bariloche, northern Patagonia, Tierra del Fuego and El Calafate, dorado lodges, dorado with friends, what a package
 * includes) or from the Santa Cruz River guide (steelhead rate reference, labelled as having no season). No new operator page was read, no commission percentages, no
 * superlatives, no "agency". Needs the permit note (script permiso-paso-a-paso-2026-11-16) applied first: the cost page links to it.
 * Inbound links: one paragraph after the short answer of the six regional notes, the licence hub, the Patagonian permit note, the trout pillar and the permit note.
 * Cover photo: the owner's Unsplash photo already loaded for the note `precio-pesca-patagonia-sur` (provisional, see docs/estado.md).
 * Idempotent: a note that exists (same clave + language) is never touched. Dry run first ($aplicar = false). Run with wp-script.yml
 * (script = costo-viaje-2026-11-16).
 */
// --- Helpers shared by the seo-consultas-2026-11-13 scripts (copied in each file: wp-script.yml sends one file) ---
// --- Helpers shared by the seo-consultas-2026-11-13 scripts (copied in each file: wp-script.yml sends one file) ---
if ( ! function_exists( 'pm_sc_h2' ) ) {
	function pm_sc_h2( string $texto, int $nivel = 2 ): string {
		return "<!-- wp:heading" . ( 2 === $nivel ? '' : ' {"level":' . $nivel . '}' ) . " -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->\n\n";
	}
	function pm_sc_p( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	function pm_sc_lista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
	}
	/** FAQ as a group block with the class pm-faq: visible on the page and read by pm_faqs_contenido() for the FAQPage JSON-LD. */
	function pm_sc_faq( array $faqs ): string {
		$in = '';
		foreach ( $faqs as [ $q, $a ] ) {
			$in .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( $in ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	function pm_sc_a( string $url, string $texto ): string {
		return '<a href="' . esc_url( $url ) . '">' . $texto . '</a>';
	}
	/** Heading blocks with offset: [ [ offset, texto plano ], … ]. */
	function pm_sc_encabezados( string $c ): array {
		preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
		$out = [];
		foreach ( $m[0] as $n => $b ) {
			$out[] = [ $b[1], trim( wp_strip_all_tags( $m[1][ $n ][0] ) ), strlen( $b[0] ) ];
		}
		return $out;
	}
	/** Offset of the first heading whose text matches $regex, or null. */
	function pm_sc_pos_encabezado( string $c, string $regex ): ?int {
		foreach ( pm_sc_encabezados( $c ) as [ $pos, $texto ] ) {
			if ( preg_match( $regex, $texto ) ) {
				return $pos;
			}
		}
		return null;
	}
	/** Offset just after the first paragraph block whose plain text matches $regex, or null. */
	function pm_sc_pos_tras_parrafo( string $c, string $regex ): ?int {
		preg_match_all( '#<!-- wp:paragraph[^>]*-->\s*<p[^>]*>(.*?)</p>\s*<!-- /wp:paragraph -->\s*#su', $c, $m, PREG_OFFSET_CAPTURE );
		foreach ( $m[0] as $n => $b ) {
			if ( preg_match( $regex, wp_strip_all_tags( $m[1][ $n ][0] ) ) ) {
				return $b[1] + strlen( $b[0] );
			}
		}
		return null;
	}
	function pm_sc_insertar( string $c, int $pos, string $bloque ): string {
		return substr( $c, 0, $pos ) . $bloque . substr( $c, $pos );
	}
	/** Save post content and fail loudly when the read-back differs. */
	function pm_sc_guardar( int $id, string $nuevo, array $debe_contener, string $etiqueta ): void {
		$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $nuevo ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( $etiqueta . ': ' . $r->get_error_message() );
		}
		$leido = get_post( $id )->post_content;
		foreach ( $debe_contener as $t ) {
			if ( false === strpos( $leido, $t ) ) {
				WP_CLI::error( "$etiqueta: la lectura de vuelta no contiene «{$t}»." );
			}
		}
	}
	function pm_sc_modo( bool $aplicar ): string {
		return $aplicar ? 'APLICA' : 'SIMULACRO';
	}
}
if ( ! function_exists( 'pm_tp_tabla' ) ) {
	function pm_tp_tabla( array $cabecera, array $filas ): string {
		$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
		$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
		return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->\n\n";
	}
	/** The dated yearly box (same markup as the other notes): the only place with dates that change every season. */
	function pm_tp_caja( string $titulo, array $items, string $pie ): string {
		$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
		return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph -->\n<p><em>{$pie}</em></p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n";
	}
	/** Guide (pm_destino) of a clave in a language, else in Spanish. */
	function pm_tp_guia( string $clave, string $i ): ?WP_Post {
		return pm_buscar( 'pm_destino', $clave, $i ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	}
}

$aplicar = false;
$hoy     = '20261116';
$clave   = 'cuanto-cuesta-viaje-pesca-argentina';
$foto_de = 'precio-pesca-patagonia-sur'; // note whose cover photo is reused (owner's Unsplash photo, Fitz Roy)
$u       = [];

$N = [];

// =====================================================================================================================================
// ENGLISH
// =====================================================================================================================================
$N['en'] = [
	'titulo'          => 'Argentina fishing trip cost 2026/27: lodges, packages, day trips',
	'slug'            => 'argentina-fishing-trip-cost-2026-27',
	'seo_title'       => 'Argentina fishing trip cost 2026/27: lodges and day trips',
	'seo_description' => 'Argentina fishing trip cost 2026/27: day trips, dorado packages, a week at a trout lodge, sea-run trout and Jurassic Lake, with each price’s source and date.',
	'bajada'          => 'What fishing trips in Argentina cost, by type, with the figures operators publish and the date each was read, and what changes the price.',
	'datos'           => [
		'Day trips: from US$ 410 for half a day and US$ 500 for a full day near Bariloche, per boat for one or two anglers (read 6/11/2026); in El Calafate AR$ 440,000 and AR$ 720,000 per angler (5/11/2026).',
		'Dorado: a multi-day package at a guesthouse in Esquina from US$ 605 per person; an all-inclusive lodge from US$ 4,050 (3 nights) to US$ 7,950 (7 nights).',
		'A week at a trout lodge in northern Patagonia: US$ 5,800 to US$ 9,200 for 7 nights and 6 fishing days (5/11/2026).',
		'Sea-run trout in Río Grande, Tierra del Fuego: US$ 12,950 to US$ 14,950 per person for a week; Jurassic Lake: US$ 8,000 per angler for 7 nights.',
		'Flights, the license and tips are usually not in the price; each figure is the operator’s, for its own dates.',
	],
	'p0'              => '<strong>Short answer.</strong> A fishing trip to Argentina costs very different amounts depending on what you buy: from <strong>US$ 410 for half a day with a guide</strong> to <strong>US$ 12,950 to US$ 14,950 for a week of sea-run trout</strong>. This page is the overview: each figure comes from one of our dated notes, which link to the detail and the conditions. They are the prices operators publish, with the date we read them; they are not quotes for your dates.',
	'h_tabla'         => 'Prices by type of trip',
	'tabla_i'         => 'Per person unless stated. Dollars and pesos are not converted into each other: each operator sets its own exchange rate when you book.',
	'tabla_cols'      => [ 'Type of trip', 'Published price', 'What the price covers', 'Source and date' ],
	'tabla_rows'      => [
		[ '<strong>Day trip, Bariloche and the Limay</strong>', 'From US$ 410 for half a day and US$ 500 for a full day (Manso: US$ 600), per boat for one or two anglers', 'Guide, boat, equipment, transfers; license and tips extra', '{n:precio-pesca-bariloche-limay|Day of fly fishing in Bariloche}, prices as of 6/11/2026' ],
		[ '<strong>Day trip, El Calafate</strong>', 'AR$ 440,000 for half a day and AR$ 720,000 for a full day, per angler (pesos)', 'Hotel transfer, guide, equipment, drinks, insurance and fishing permits', '{n:precio-pesca-patagonia-sur|Tierra del Fuego and El Calafate}, 5/11/2026' ],
		[ '<strong>Dorado package at a guesthouse, Corrientes</strong>', 'From US$ 605 per person in a group of three and US$ 705 in a group of two, for 3 nights and 2 fishing days', 'Lodging, meals, boat, guide, bait, fuel and license', '{n:viaje-dorado-amigos-precios|Dorado trip with friends}, "from" prices of 4/11/2026' ],
		[ '<strong>All-inclusive dorado lodge, Corrientes</strong>', 'From US$ 4,050 (3 nights, 2 fishing days) to US$ 7,950 (7 nights, 6 days), double room', 'Lodging, meals and drinks, guide, loan equipment; no flights, transfers or license', '{n:precio-viaje-dorado-lodge|Dorado fishing trip cost}, read 13/11/2026' ],
		[ '<strong>A week at a trout lodge, northern Patagonia</strong>', 'US$ 5,800 to US$ 9,200 for 7 nights and 6 fishing days; one Neuquén lodge publishes US$ 3,950 (3 nights) to US$ 6,950 (7 nights)', 'All inclusive, double room, shared guide; license depends on the lodge', '{n:precio-semana-trucha-patagonia-norte|A week of trout fishing in Neuquén and Chubut}, 5/11/2026' ],
		[ '<strong>Sea-run trout, Río Grande (Tierra del Fuego)</strong>', 'US$ 12,950 to US$ 14,950 for a week, depending on the dates (single room)', 'Lodging, airport transfer, meals and drinks, guide, loan equipment; no flights, license or tips', '{n:precio-pesca-patagonia-sur|Tierra del Fuego and El Calafate}, read 4/11 and 13/11/2026' ],
		[ '<strong>Jurassic Lake (Santa Cruz)</strong>', 'US$ 4,200 (3 nights), US$ 5,200 (4 nights) and US$ 8,000 (7 nights), per angler', 'According to the lodge’s pre-trip page: license, ground transfer from El Calafate and drinks; charter flight and tips extra', '{n:precio-pesca-patagonia-sur|Tierra del Fuego and El Calafate}, read 10 and 13/11/2026; {g:jurassic-lake|Jurassic Lake guide}' ],
		[ '<strong>Santa Cruz River, steelhead</strong>', 'A reference with no season: US$ 4,990 for a week and US$ 2,990 for half a week, according to Where Wise Men Fish (page of 5/03/2025)', 'Seven nights, six fishing days, transfers, meals, wine and beer, and the fishing permit', '{g:rio-santa-cruz|Santa Cruz River guide}: we do not know the season of that rate, so it is not a 2027 price' ],
	],
	'h_cambia'        => 'What changes the cost',
	'cambia_l'        => [
		'<strong>A full week or half a week:</strong> the price per fishing day falls with each extra day. At one Neuquén lodge it goes from US$ 1,975 with 2 fishing days to US$ 1,158 with 6; at the dorado lodges of the table, from US$ 2,025 to US$ 1,325 (our calculations, in the notes linked above).',
		'<strong>A companion who does not fish:</strong> US$ 650 per night at the dorado lodges and at a Río Grande lodge, US$ 800 per night at a lodge on the Manso River, and AR$ 185,000 (half day) or AR$ 335,000 (full day) in El Calafate.',
		'<strong>Single room or boat:</strong> a 60% surcharge at the dorado lodges of the price ladder and 50% for an individual boat at a lodge on the middle Paraná. The Río Grande rate is already for a single room.',
		'<strong>The license, apart from the price:</strong> it was included in 4 of the nine packages we compared, not included in 3 and unspecified in 2 ({n:paquete-incluye-no-incluye|what a package includes}). A Río Grande lodge estimates about US$ 550 per angler per week for it. How to get it: {n:permiso-pesca-argentina-paso-a-paso|Argentina fishing license, step by step}.',
		'<strong>Flights and transfers:</strong> the Río Grande price starts at the airport, the dorado lodges do not include flights, and the Jurassic Lake charter flight is extra.',
		'<strong>Rented equipment:</strong> a lodge on the Manso River charges US$ 80 per angler per day for a full set (US$ 40 for rod and reel or for boots and waders); the dorado lodges of the price ladder lend equipment.',
		'<strong>Tips:</strong> Jurassic Lake’s page suggests US$ 80 to 100 per person per day.',
		'<strong>The date:</strong> the Río Grande week costs US$ 12,950, US$ 13,950 or US$ 14,950 depending on the dates, and the Manso River lodge raises its week from US$ 7,000 to US$ 7,700 from 1 January 2027.',
	],
	'h_incluye'       => 'What a price usually includes and what it does not',
	'incluye_p'       => 'In the pages operators publish and we have read, the price usually covers lodging, meals, drinks, a guide and loan equipment. What usually stays outside: flights and transfers to the lodge, the fishing license, tips and laundry. It is not the same at every operator, which is why two numbers are only comparable when they cover the same things: see <a href="{n:paquete-incluye-no-incluye}">what an Argentina fishing package includes and what is extra</a>.',
	'h_comparar'      => 'How to use the comparison',
	'comparar_p'      => 'Each price is one operator’s, for certain dates, a number of nights and a type of room. Read it as the starting point of a conversation, not as a quote. Before comparing two options, check that they cover the same days of fishing, the same guide ratio, the license, the transfers and the flights. If you tell us your dates, your group and what you want to fish in <a href="{viaje}">the request form</a>, we ask the operators that fit and send you the options side by side: request options and ask each one for the same list in writing.',
	'h_notas'         => 'The detail, region by region',
	'notas_l'         => [
		'{n:precio-pesca-bariloche-limay|How much does a day of fly fishing cost in Bariloche and the Limay River (2026/27)}',
		'{n:precio-semana-trucha-patagonia-norte|How much does a week of trout fishing cost in Neuquén and Chubut: all-inclusive lodges (2026/27)}',
		'{n:precio-pesca-patagonia-sur|How much does it cost to fish in Tierra del Fuego and El Calafate: sea trout and one-day trips (2026/27)}',
		'{n:precio-viaje-dorado-lodge|How much does a dorado fishing trip in Argentina cost: all-inclusive lodges by number of nights (2026/27)}',
		'{n:viaje-dorado-amigos-precios|Golden dorado fishing trip with friends: how many days, how much it costs, and where to stay (2026/27)}',
		'{n:paquete-incluye-no-incluye|What’s included in an Argentina fishing package and what’s extra (2026/27 examples)}',
	],
	'cta'             => '<strong>Want to compare with your dates?</strong> <a href="{viaje}">Request options</a>: tell us when you travel, how many are going and what you want to fish, and we reply within 48 hours with the options of the operators that fit.',
	'faq_h'           => 'Frequently asked questions',
	'faq'             => [
		[ 'How much does a fishing trip to Argentina cost?', 'It depends on the type. The published figures we have read go from US$ 410 for half a day with a guide near Bariloche (per boat, 6/11/2026) to US$ 12,950 to US$ 14,950 for a week of sea-run trout in Río Grande (4/11/2026). In between: a dorado package at a guesthouse from US$ 605 per person, a dorado lodge from US$ 4,050 for 3 nights, and a week at a trout lodge in northern Patagonia from US$ 5,800 to US$ 9,200. The table above gives the source and the date of each.' ],
		[ 'How much does a week at a fishing lodge in Patagonia cost?', 'For a week of 7 nights and 6 fishing days in northern Patagonia, the lodges we read publish US$ 5,800 to US$ 9,200 per person in a double room (5/11/2026), and a lodge on the Manso River US$ 7,000 for 2026/27 and US$ 7,700 from 1 January 2027 (read 10/11/2026). In Tierra del Fuego, a week of sea-run trout is US$ 12,950 to US$ 14,950 (4/11/2026).' ],
		[ 'How much does a day of fishing with a guide cost?', 'Near Bariloche, from US$ 410 for half a day and US$ 500 for a full day, per boat for one or two anglers, with the license extra (6/11/2026). In El Calafate, AR$ 440,000 for half a day and AR$ 720,000 for a full day per angler, with the permits included in that price (5/11/2026).' ],
		[ 'How much does a dorado fishing trip cost?', 'In Corrientes, a guesthouse in Esquina publishes packages from US$ 605 per person (group of three, 3 nights, 2 fishing days) and US$ 705 (group of two) as of 4/11/2026; all-inclusive lodges publish US$ 4,050 for 3 nights and US$ 7,950 for 7 nights per person in a double room (read 13/11/2026). Flights are not included.' ],
		[ 'How much does sea-run trout fishing in Tierra del Fuego cost?', 'A Río Grande lodge publishes US$ 12,950 to US$ 14,950 per person for a week, depending on the dates, in a single room and with a shared guide (read 4/11/2026); another lodge publishes the same range for 2027 (read 13/11/2026). The price does not include flights, the license or tips.' ],
		[ 'Is the fishing license included in the price?', 'It depends on the operator: it was included in 4 of the nine packages we compared, not included in 3 and unspecified in 2. Ask in writing. How to get the permit and what it costs: <a href="{n:permiso-pesca-argentina-paso-a-paso}">Argentina fishing license, step by step</a>.' ],
		[ 'Are flights included?', 'In the lodges we read, no: the Río Grande price starts at the airport, the dorado lodges leave out flights and transfers, and the Jurassic Lake charter flight is extra. A few operators include charter flights or transfers; the detail is in each regional note.' ],
		[ 'Are the prices in dollars or in pesos?', 'In the currency each operator publishes: lodges in dollars, day trips in El Calafate in pesos, and Bariloche in both, from different operators. We do not convert one into the other because each operator sets its own exchange rate when you book.' ],
		[ 'Does a companion who does not fish pay the same?', 'Not usually: the companion pays less than an angler, but still pays. US$ 650 per night at the dorado lodges and at a Río Grande lodge, US$ 800 per night at a lodge on the Manso River, and AR$ 185,000 or AR$ 335,000 for a day in El Calafate.' ],
		[ 'How do I compare options?', 'Compare only what covers the same: days of fishing, guide ratio, license, transfers and flights. If you send us your dates and group in <a href="{viaje}">the request form</a>, we ask the operators that fit and reply within 48 hours with the options side by side.' ],
	],
	'cierre'          => '<em>These are the prices operators publish, each with the date we read it, and they change. We have not stayed at these lodges or fished with these operators. Confirm the price, what it includes and the dates with the operator before booking.</em>',
];

// =====================================================================================================================================
// ESPAÑOL
// =====================================================================================================================================
$N['es'] = [
	'titulo'          => 'Cuánto cuesta un viaje de pesca a la Argentina 2026/27',
	'slug'            => 'cuanto-cuesta-viaje-de-pesca-argentina-2026-27',
	'seo_title'       => 'Cuánto cuesta un viaje de pesca a la Argentina 2026/27',
	'seo_description' => 'Cuánto cuesta un viaje de pesca a la Argentina 2026/27: salida de día, dorado, semana en lodge de trucha, trucha de mar y Jurassic Lake, con fuente y fecha.',
	'bajada'          => 'Cuánto cuestan los viajes de pesca a la Argentina, por tipo, con las cifras que publican los operadores y la fecha en que las leímos, y qué cambia el precio.',
	'datos'           => [
		'Salidas de día: desde US$ 410 el medio día y US$ 500 el día completo cerca de Bariloche, por bote para uno o dos pescadores (leído el 6/11/2026); en El Calafate, $ 440.000 y $ 720.000 por pescador (5/11/2026).',
		'Dorado: un paquete de varios días en una posada de Esquina desde US$ 605 por persona; un lodge todo incluido desde US$ 4.050 (3 noches) hasta US$ 7.950 (7 noches).',
		'Una semana en un lodge de trucha de la Patagonia norte: de US$ 5.800 a US$ 9.200 por 7 noches y 6 días de pesca (5/11/2026).',
		'Trucha de mar en Río Grande, Tierra del Fuego: de US$ 12.950 a US$ 14.950 por persona por semana; Jurassic Lake: US$ 8.000 por pescador por 7 noches.',
		'Los vuelos, la licencia y las propinas suelen quedar fuera del precio; cada cifra es del operador, para sus fechas.',
	],
	'p0'              => '<strong>Respuesta corta.</strong> Un viaje de pesca a la Argentina cuesta montos muy distintos según lo que compres: desde <strong>US$ 410 el medio día con guía</strong> hasta <strong>US$ 12.950 a US$ 14.950 por una semana de trucha de mar</strong>. Esta página es la vista general: cada cifra sale de una de nuestras notas con fecha, que enlazan al detalle y a las condiciones. Son los precios que publican los operadores, con la fecha en que los leímos; no son una cotización para tus fechas.',
	'h_tabla'         => 'Precios por tipo de viaje',
	'tabla_i'         => 'Por persona salvo que se diga otra cosa. Los dólares y los pesos no se convierten entre sí: cada operador fija su tipo de cambio al reservar.',
	'tabla_cols'      => [ 'Tipo de viaje', 'Precio publicado', 'Qué cubre el precio', 'Fuente y fecha' ],
	'tabla_rows'      => [
		[ '<strong>Salida de día, Bariloche y el Limay</strong>', 'Desde US$ 410 el medio día y US$ 500 el día completo (Manso: US$ 600), por bote para uno o dos pescadores', 'Guía, bote, equipo, traslados; licencia y propinas aparte', '{n:precio-pesca-bariloche-limay|Un día de pesca con mosca en Bariloche}, precios al 6/11/2026' ],
		[ '<strong>Salida de día, El Calafate</strong>', '$ 440.000 el medio día y $ 720.000 el día completo, por pescador (pesos)', 'Traslado desde el hotel, guía, equipo, bebidas, seguro y permisos de pesca', '{n:precio-pesca-patagonia-sur|Tierra del Fuego y El Calafate}, 5/11/2026' ],
		[ '<strong>Paquete de dorado en una posada, Corrientes</strong>', 'Desde US$ 605 por persona en un grupo de tres y US$ 705 en uno de dos, por 3 noches y 2 días de pesca', 'Alojamiento, comidas, lancha, guía, carnada, combustible y licencia', '{n:viaje-dorado-amigos-precios|Viaje de dorado con amigos}, precios «desde» del 4/11/2026' ],
		[ '<strong>Lodge de dorado todo incluido, Corrientes</strong>', 'Desde US$ 4.050 (3 noches, 2 días de pesca) hasta US$ 7.950 (7 noches, 6 días), en habitación doble', 'Alojamiento, comidas y bebidas, guía, equipo de préstamo; sin pasajes, traslados ni licencia', '{n:precio-viaje-dorado-lodge|Cuánto cuesta un viaje de pesca de dorado}, leído el 13/11/2026' ],
		[ '<strong>Semana en un lodge de trucha, Patagonia norte</strong>', 'De US$ 5.800 a US$ 9.200 por 7 noches y 6 días de pesca; un lodge de Neuquén publica de US$ 3.950 (3 noches) a US$ 6.950 (7 noches)', 'Todo incluido, habitación doble, guía compartido; la licencia depende del lodge', '{n:precio-semana-trucha-patagonia-norte|Una semana de pesca de trucha en Neuquén y Chubut}, 5/11/2026' ],
		[ '<strong>Trucha de mar, Río Grande (Tierra del Fuego)</strong>', 'De US$ 12.950 a US$ 14.950 por una semana, según las fechas (habitación single)', 'Alojamiento, traslado desde el aeropuerto, comidas y bebidas, guía, equipo de préstamo; sin pasajes, licencia ni propinas', '{n:precio-pesca-patagonia-sur|Tierra del Fuego y El Calafate}, leído el 4/11 y el 13/11/2026' ],
		[ '<strong>Jurassic Lake (Santa Cruz)</strong>', 'US$ 4.200 (3 noches), US$ 5.200 (4 noches) y US$ 8.000 (7 noches), por pescador', 'Según la página de información previa del lodge: licencia, traslado terrestre desde El Calafate y bebidas; vuelo chárter y propinas aparte', '{n:precio-pesca-patagonia-sur|Tierra del Fuego y El Calafate}, leído el 10 y el 13/11/2026; {g:jurassic-lake|guía de Jurassic Lake}' ],
		[ '<strong>Río Santa Cruz, steelhead</strong>', 'Una referencia sin temporada: US$ 4.990 por la semana y US$ 2.990 por media semana, según Where Wise Men Fish (página del 5/03/2025)', 'Siete noches, seis días de pesca, traslados, comidas, vino y cerveza, y el permiso de pesca', '{g:rio-santa-cruz|Guía del río Santa Cruz}: no sabemos a qué temporada corresponde esa tarifa, así que no es un precio de 2027' ],
	],
	'h_cambia'        => 'Qué cambia el costo',
	'cambia_l'        => [
		'<strong>Semana completa o media semana:</strong> el precio por día de pesca baja con cada día extra. En un lodge de Neuquén pasa de US$ 1.975 con 2 días de pesca a US$ 1.158 con 6; en los lodges de dorado de la tabla, de US$ 2.025 a US$ 1.325 (cálculos nuestros, en las notas enlazadas arriba).',
		'<strong>Acompañante que no pesca:</strong> US$ 650 por noche en los lodges de dorado y en un lodge de Río Grande, US$ 800 por noche en un lodge del río Manso, y $ 185.000 (medio día) o $ 335.000 (día completo) en El Calafate.',
		'<strong>Habitación o lancha individual:</strong> un recargo del 60 % en los lodges de dorado de la escalera de precios y del 50 % por la lancha individual en un lodge del Paraná medio. La tarifa de Río Grande ya es en habitación single.',
		'<strong>La licencia, aparte del precio:</strong> estaba incluida en 4 de los nueve paquetes que comparamos, no incluida en 3 y sin especificar en 2 ({n:paquete-incluye-no-incluye|qué incluye un paquete}). Un lodge de Río Grande la calcula en unos US$ 550 por pescador por semana. Cómo sacarla: {n:permiso-pesca-argentina-paso-a-paso|permiso de pesca en Argentina, paso a paso}.',
		'<strong>Vuelos y traslados:</strong> el precio de Río Grande empieza en el aeropuerto, los lodges de dorado no incluyen pasajes y el vuelo chárter de Jurassic Lake es aparte.',
		'<strong>Alquiler de equipo:</strong> un lodge del río Manso cobra US$ 80 por pescador por día por el equipo completo (US$ 40 por caña y reel o por botas y waders); los lodges de dorado de la escalera de precios prestan el equipo.',
		'<strong>Propinas:</strong> la página de Jurassic Lake sugiere de US$ 80 a 100 por persona por día.',
		'<strong>La fecha:</strong> la semana de Río Grande cuesta US$ 12.950, US$ 13.950 o US$ 14.950 según las fechas, y el lodge del río Manso sube su semana de US$ 7.000 a US$ 7.700 desde el 1 de enero de 2027.',
	],
	'h_incluye'       => 'Qué suele incluir un precio y qué no',
	'incluye_p'       => 'En las páginas que publican los operadores y que leímos, el precio suele cubrir alojamiento, comidas, bebidas, guía y equipo de préstamo. Lo que suele quedar afuera: pasajes y traslados hasta el lodge, la licencia de pesca, las propinas y la lavandería. No es igual en todos los operadores, por eso dos números solo se comparan cuando cubren lo mismo: mirá <a href="{n:paquete-incluye-no-incluye}">qué incluye un paquete de pesca en Argentina y qué cuesta aparte</a>.',
	'h_comparar'      => 'Cómo usar la comparación',
	'comparar_p'      => 'Cada precio es de un operador, para unas fechas, una cantidad de noches y un tipo de habitación. Leelo como el punto de partida de una conversación, no como una cotización. Antes de comparar dos opciones, fijate que cubran los mismos días de pesca, la misma relación de guías, la licencia, los traslados y los vuelos. Si nos contás tus fechas, tu grupo y qué querés pescar en <a href="{viaje}">el formulario de pedido</a>, consultamos a los operadores que encajan y te mandamos las opciones lado a lado: pedí opciones y pedile a cada uno la misma lista por escrito.',
	'h_notas'         => 'El detalle, región por región',
	'notas_l'         => [
		'{n:precio-pesca-bariloche-limay|Cuánto cuesta un día de pesca con mosca en Bariloche y el Río Limay (2026/27)}',
		'{n:precio-semana-trucha-patagonia-norte|Cuánto cuesta una semana de pesca de trucha en Neuquén y Chubut: lodges todo incluido (2026/27)}',
		'{n:precio-pesca-patagonia-sur|Cuánto cuesta pescar en Tierra del Fuego y El Calafate: trucha de mar y salidas de un día (2026/27)}',
		'{n:precio-viaje-dorado-lodge|Cuánto cuesta un viaje de pesca de dorado en Argentina: lodges todo incluido, por cantidad de noches (2026/27)}',
		'{n:viaje-dorado-amigos-precios|Viaje de pesca de dorado con amigos: cuántos días, cuánto cuesta y dónde alojarse (2026/27)}',
		'{n:paquete-incluye-no-incluye|Qué incluye un paquete de pesca en Argentina y qué cuesta aparte (ejemplos 2026/27)}',
	],
	'cta'             => '<strong>¿Querés comparar con tus fechas?</strong> <a href="{viaje}">Pedí opciones</a>: contanos cuándo viajás, cuántos son y qué querés pescar, y en 48 horas te respondemos con las opciones de los operadores que encajan.',
	'faq_h'           => 'Preguntas frecuentes',
	'faq'             => [
		[ '¿Cuánto cuesta un viaje de pesca a la Argentina?', 'Depende del tipo. Las cifras publicadas que leímos van desde US$ 410 el medio día con guía cerca de Bariloche (por bote, 6/11/2026) hasta US$ 12.950 a US$ 14.950 por una semana de trucha de mar en Río Grande (4/11/2026). En el medio: un paquete de dorado en una posada desde US$ 605 por persona, un lodge de dorado desde US$ 4.050 por 3 noches y una semana en un lodge de trucha de la Patagonia norte de US$ 5.800 a US$ 9.200. La tabla de arriba da la fuente y la fecha de cada una.' ],
		[ '¿Cuánto cuesta una semana en un lodge de pesca en la Patagonia?', 'Por una semana de 7 noches y 6 días de pesca en la Patagonia norte, los lodges que leímos publican de US$ 5.800 a US$ 9.200 por persona en habitación doble (5/11/2026), y un lodge del río Manso US$ 7.000 para 2026/27 y US$ 7.700 desde el 1 de enero de 2027 (leído el 10/11/2026). En Tierra del Fuego, una semana de trucha de mar cuesta de US$ 12.950 a US$ 14.950 (4/11/2026).' ],
		[ '¿Cuánto cuesta una salida de un día con guía?', 'Cerca de Bariloche, desde US$ 410 el medio día y US$ 500 el día completo, por bote para uno o dos pescadores, con la licencia aparte (6/11/2026). En El Calafate, $ 440.000 el medio día y $ 720.000 el día completo por pescador, con los permisos incluidos en ese precio (5/11/2026).' ],
		[ '¿Cuánto cuesta un viaje de pesca de dorado?', 'En Corrientes, una posada de Esquina publica paquetes desde US$ 605 por persona (grupo de tres, 3 noches, 2 días de pesca) y US$ 705 (grupo de dos) al 4/11/2026; los lodges todo incluido publican US$ 4.050 por 3 noches y US$ 7.950 por 7 noches por persona en habitación doble (leído el 13/11/2026). Los pasajes no están incluidos.' ],
		[ '¿Cuánto cuesta pescar trucha de mar en Tierra del Fuego?', 'Un lodge de Río Grande publica de US$ 12.950 a US$ 14.950 por persona por una semana, según las fechas, en habitación single y con guía compartido (leído el 4/11/2026); otro lodge publica el mismo rango para 2027 (leído el 13/11/2026). El precio no incluye pasajes, licencia ni propinas.' ],
		[ '¿La licencia de pesca está incluida en el precio?', 'Depende del operador: estaba incluida en 4 de los nueve paquetes que comparamos, no incluida en 3 y sin especificar en 2. Preguntalo por escrito. Cómo sacar el permiso y cuánto cuesta: <a href="{n:permiso-pesca-argentina-paso-a-paso}">permiso de pesca en Argentina, paso a paso</a>.' ],
		[ '¿Los vuelos están incluidos?', 'En los lodges que leímos, no: el precio de Río Grande empieza en el aeropuerto, los lodges de dorado dejan afuera pasajes y traslados, y el vuelo chárter de Jurassic Lake es aparte. Algunos operadores incluyen vuelos chárter o traslados; el detalle está en cada nota regional.' ],
		[ '¿Los precios están en dólares o en pesos?', 'En la moneda que publica cada operador: los lodges en dólares, las salidas de día de El Calafate en pesos, y Bariloche en las dos, de operadores distintos. No convertimos una en otra porque cada operador fija su tipo de cambio al reservar.' ],
		[ '¿El acompañante que no pesca paga lo mismo?', 'No suele: paga menos que un pescador, pero paga. US$ 650 por noche en los lodges de dorado y en un lodge de Río Grande, US$ 800 por noche en un lodge del río Manso, y $ 185.000 o $ 335.000 por un día en El Calafate.' ],
		[ '¿Cómo comparo opciones?', 'Compará solo lo que cubre lo mismo: días de pesca, relación de guías, licencia, traslados y vuelos. Si nos mandás tus fechas y tu grupo en <a href="{viaje}">el formulario de pedido</a>, consultamos a los operadores que encajan y respondemos en 48 horas con las opciones lado a lado.' ],
	],
	'cierre'          => '<em>Son los precios que publican los operadores, cada uno con la fecha en que lo leímos, y cambian. No nos alojamos en estos lodges ni pescamos con estos operadores. Confirmá el precio, qué incluye y las fechas con el operador antes de reservar.</em>',
];

// =====================================================================================================================================
// PORTUGUÊS
// =====================================================================================================================================
$N['pt'] = [
	'titulo'          => 'Quanto custa pescar na Argentina: valores 2026/27',
	'slug'            => 'quanto-custa-pescar-na-argentina-2026-27',
	'seo_title'       => 'Quanto custa pescar na Argentina: valores 2026/27',
	'seo_description' => 'Quanto custa pescar na Argentina em 2026/27: saída de um dia, pacote de dourado, semana em lodge de truta, truta-do-mar e Jurassic Lake, com fonte e data.',
	'bajada'          => 'Quanto custam as viagens de pesca à Argentina, por tipo, com os valores que os operadores publicam e a data em que os lemos, e o que muda o preço.',
	'datos'           => [
		'Saídas de um dia: a partir de US$ 410 a meia diária e US$ 500 o dia inteiro perto de Bariloche, por barco para um ou dois pescadores (lido em 6/11/2026); em El Calafate, $ 440.000 e $ 720.000 por pescador (5/11/2026).',
		'Dourado: um pacote de vários dias em uma pousada de Esquina a partir de US$ 605 por pessoa; um lodge all inclusive de US$ 4.050 (3 noites) a US$ 7.950 (7 noites).',
		'Uma semana em um lodge de truta da Patagônia norte: de US$ 5.800 a US$ 9.200 por 7 noites e 6 dias de pesca (5/11/2026).',
		'Truta-do-mar em Río Grande, Terra do Fogo: de US$ 12.950 a US$ 14.950 por pessoa por semana; Jurassic Lake: US$ 8.000 por pescador por 7 noites.',
		'Voos, licença e gorjetas costumam ficar fora do preço; cada valor é do operador, para as suas datas.',
	],
	'p0'              => '<strong>Resposta curta.</strong> Uma viagem de pesca à Argentina custa valores muito diferentes conforme o que você compra: de <strong>US$ 410 a meia diária com guia</strong> a <strong>US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar</strong>. Esta página é a visão geral: cada valor sai de uma das nossas notas com data, que levam ao detalhe e às condições. São os preços que os operadores publicam, com a data em que os lemos; não são uma cotação para as suas datas.',
	'h_tabla'         => 'Valores por tipo de viagem',
	'tabla_i'         => 'Por pessoa, salvo indicação em contrário. Dólares e pesos não são convertidos entre si: cada operador fixa o seu câmbio na reserva.',
	'tabla_cols'      => [ 'Tipo de viagem', 'Preço publicado', 'O que o preço cobre', 'Fonte e data' ],
	'tabla_rows'      => [
		[ '<strong>Saída de um dia, Bariloche e o Limay</strong>', 'A partir de US$ 410 a meia diária e US$ 500 o dia inteiro (Manso: US$ 600), por barco para um ou dois pescadores', 'Guia, barco, equipamento, traslados; licença e gorjetas à parte', '{n:precio-pesca-bariloche-limay|Um dia de pesca com mosca em Bariloche}, preços de 6/11/2026' ],
		[ '<strong>Saída de um dia, El Calafate</strong>', '$ 440.000 a meia diária e $ 720.000 o dia inteiro, por pescador (pesos)', 'Traslado do hotel, guia, equipamento, bebidas, seguro e licenças de pesca', '{n:precio-pesca-patagonia-sur|Terra do Fogo e El Calafate}, 5/11/2026' ],
		[ '<strong>Pacote de dourado em uma pousada, Corrientes</strong>', 'A partir de US$ 605 por pessoa em um grupo de três e US$ 705 em um de dois, por 3 noites e 2 dias de pesca', 'Hospedagem, refeições, lancha, guia, isca, combustível e licença', '{n:viaje-dorado-amigos-precios|Viagem de dourado com amigos}, preços «a partir de» de 4/11/2026' ],
		[ '<strong>Lodge de dourado all inclusive, Corrientes</strong>', 'De US$ 4.050 (3 noites, 2 dias de pesca) a US$ 7.950 (7 noites, 6 dias), em quarto duplo', 'Hospedagem, refeições e bebidas, guia, equipamento de empréstimo; sem passagens, traslados nem licença', '{n:precio-viaje-dorado-lodge|Quanto custa uma viagem de pesca de dourado}, lido em 13/11/2026' ],
		[ '<strong>Semana em um lodge de truta, Patagônia norte</strong>', 'De US$ 5.800 a US$ 9.200 por 7 noites e 6 dias de pesca; um lodge de Neuquén publica de US$ 3.950 (3 noites) a US$ 6.950 (7 noites)', 'All inclusive, quarto duplo, guia compartilhado; a licença depende do lodge', '{n:precio-semana-trucha-patagonia-norte|Uma semana de pesca de truta em Neuquén e Chubut}, 5/11/2026' ],
		[ '<strong>Truta-do-mar, Río Grande (Terra do Fogo)</strong>', 'De US$ 12.950 a US$ 14.950 por uma semana, conforme as datas (quarto single)', 'Hospedagem, traslado do aeroporto, refeições e bebidas, guia, equipamento de empréstimo; sem passagens, licença nem gorjetas', '{n:precio-pesca-patagonia-sur|Terra do Fogo e El Calafate}, lido em 4/11 e 13/11/2026' ],
		[ '<strong>Jurassic Lake (Santa Cruz)</strong>', 'US$ 4.200 (3 noites), US$ 5.200 (4 noites) e US$ 8.000 (7 noites), por pescador', 'Segundo a página de informações prévias do lodge: licença, traslado terrestre desde El Calafate e bebidas; voo fretado e gorjetas à parte', '{n:precio-pesca-patagonia-sur|Terra do Fogo e El Calafate}, lido em 10 e 13/11/2026; {g:jurassic-lake|guia de Jurassic Lake}' ],
		[ '<strong>Rio Santa Cruz, steelhead</strong>', 'Uma referência sem temporada: US$ 4.990 pela semana e US$ 2.990 pela meia semana, segundo Where Wise Men Fish (página de 5/03/2025)', 'Sete noites, seis dias de pesca, traslados, refeições, vinho e cerveja, e a licença de pesca', '{g:rio-santa-cruz|Guia do rio Santa Cruz}: não sabemos a que temporada corresponde essa tarifa, então não é um preço de 2027' ],
	],
	'h_cambia'        => 'O que muda o custo',
	'cambia_l'        => [
		'<strong>Semana inteira ou meia semana:</strong> o preço por dia de pesca cai a cada dia extra. Em um lodge de Neuquén vai de US$ 1.975 com 2 dias de pesca a US$ 1.158 com 6; nos lodges de dourado da tabela, de US$ 2.025 a US$ 1.325 (cálculos nossos, nas notas com link acima).',
		'<strong>Acompanhante que não pesca:</strong> US$ 650 por noite nos lodges de dourado e em um lodge de Río Grande, US$ 800 por noite em um lodge do rio Manso, e $ 185.000 (meia diária) ou $ 335.000 (dia inteiro) em El Calafate.',
		'<strong>Quarto ou lancha individual:</strong> um acréscimo de 60 % nos lodges de dourado da escada de preços e de 50 % pela lancha individual em um lodge do Paraná médio. A tarifa de Río Grande já é em quarto single.',
		'<strong>A licença, à parte do preço:</strong> estava incluída em 4 dos nove pacotes que comparamos, não incluída em 3 e sem especificar em 2 ({n:paquete-incluye-no-incluye|o que inclui um pacote}). Um lodge de Río Grande a calcula em cerca de US$ 550 por pescador por semana. Como tirar: {n:permiso-pesca-argentina-paso-a-paso|licença de pesca na Argentina, passo a passo}.',
		'<strong>Voos e traslados:</strong> o preço de Río Grande começa no aeroporto, os lodges de dourado não incluem passagens e o voo fretado de Jurassic Lake é à parte.',
		'<strong>Aluguel de equipamento:</strong> um lodge do rio Manso cobra US$ 80 por pescador por dia pelo equipamento completo (US$ 40 por vara e molinete ou por botas e waders); os lodges de dourado da escada de preços emprestam o equipamento.',
		'<strong>Gorjetas:</strong> a página de Jurassic Lake sugere de US$ 80 a 100 por pessoa por dia.',
		'<strong>A data:</strong> a semana de Río Grande custa US$ 12.950, US$ 13.950 ou US$ 14.950 conforme as datas, e o lodge do rio Manso sobe a sua semana de US$ 7.000 para US$ 7.700 a partir de 1º de janeiro de 2027.',
	],
	'h_incluye'       => 'O que um preço costuma incluir e o que não',
	'incluye_p'       => 'Nas páginas que os operadores publicam e que lemos, o preço costuma cobrir hospedagem, refeições, bebidas, guia e equipamento de empréstimo. O que costuma ficar de fora: passagens e traslados até o lodge, a licença de pesca, as gorjetas e a lavanderia. Não é igual em todos os operadores, por isso dois números só se comparam quando cobrem o mesmo: veja <a href="{n:paquete-incluye-no-incluye}">o que inclui um pacote de pesca na Argentina e o que é cobrado à parte</a>.',
	'h_comparar'      => 'Como usar a comparação',
	'comparar_p'      => 'Cada preço é de um operador, para certas datas, uma quantidade de noites e um tipo de quarto. Leia-o como o ponto de partida de uma conversa, não como uma cotação. Antes de comparar duas opções, confira se cobrem os mesmos dias de pesca, a mesma proporção de guias, a licença, os traslados e os voos. Se você nos contar suas datas, seu grupo e o que quer pescar no <a href="{viaje}">formulário de pedido</a>, consultamos os operadores que se encaixam e enviamos as opções lado a lado: peça opções e peça a cada um a mesma lista por escrito.',
	'h_notas'         => 'O detalhe, região por região',
	'notas_l'         => [
		'{n:precio-pesca-bariloche-limay|Quanto custa um dia de pesca com mosca em Bariloche e no Rio Limay (2026/27)}',
		'{n:precio-semana-trucha-patagonia-norte|Quanto custa uma semana de pesca de truta em Neuquén e Chubut: lodges all inclusive (2026/27)}',
		'{n:precio-pesca-patagonia-sur|Quanto custa pescar em Tierra del Fuego e El Calafate: truta-do-mar e saídas de um dia (2026/27)}',
		'{n:precio-viaje-dorado-lodge|Quanto custa uma viagem de pesca de dourado na Argentina: lodges all inclusive, por quantidade de noites (2026/27)}',
		'{n:viaje-dorado-amigos-precios|Viagem de pesca de dourado com amigos: quantos dias, quanto custa e onde se hospedar (2026/27)}',
		'{n:paquete-incluye-no-incluye|O que inclui um pacote de pesca na Argentina e o que é cobrado à parte (2026/27)}',
	],
	'cta'             => '<strong>Quer comparar com as suas datas?</strong> <a href="{viaje}">Peça opções</a>: conte quando viaja, quantos são e o que quer pescar, e em 48 horas respondemos com as opções dos operadores que se encaixam.',
	'faq_h'           => 'Perguntas frequentes',
	'faq'             => [
		[ 'Quanto custa uma viagem de pesca à Argentina?', 'Depende do tipo. Os valores publicados que lemos vão de US$ 410 a meia diária com guia perto de Bariloche (por barco, 6/11/2026) a US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar em Río Grande (4/11/2026). No meio: um pacote de dourado em uma pousada a partir de US$ 605 por pessoa, um lodge de dourado a partir de US$ 4.050 por 3 noites e uma semana em um lodge de truta da Patagônia norte de US$ 5.800 a US$ 9.200. A tabela acima traz a fonte e a data de cada um.' ],
		[ 'Quanto custa uma semana em um lodge de pesca na Patagônia?', 'Por uma semana de 7 noites e 6 dias de pesca na Patagônia norte, os lodges que lemos publicam de US$ 5.800 a US$ 9.200 por pessoa em quarto duplo (5/11/2026), e um lodge do rio Manso US$ 7.000 para 2026/27 e US$ 7.700 a partir de 1º de janeiro de 2027 (lido em 10/11/2026). Na Terra do Fogo, uma semana de truta-do-mar custa de US$ 12.950 a US$ 14.950 (4/11/2026).' ],
		[ 'Quanto custa uma saída de um dia com guia?', 'Perto de Bariloche, a partir de US$ 410 a meia diária e US$ 500 o dia inteiro, por barco para um ou dois pescadores, com a licença à parte (6/11/2026). Em El Calafate, $ 440.000 a meia diária e $ 720.000 o dia inteiro por pescador, com as licenças incluídas nesse preço (5/11/2026).' ],
		[ 'Quanto custa uma viagem de pesca de dourado?', 'Em Corrientes, uma pousada de Esquina publica pacotes a partir de US$ 605 por pessoa (grupo de três, 3 noites, 2 dias de pesca) e US$ 705 (grupo de dois) em 4/11/2026; os lodges all inclusive publicam US$ 4.050 por 3 noites e US$ 7.950 por 7 noites por pessoa em quarto duplo (lido em 13/11/2026). As passagens não estão incluídas.' ],
		[ 'Quanto custa pescar truta-do-mar na Terra do Fogo?', 'Um lodge de Río Grande publica de US$ 12.950 a US$ 14.950 por pessoa por uma semana, conforme as datas, em quarto single e com guia compartilhado (lido em 4/11/2026); outro lodge publica a mesma faixa para 2027 (lido em 13/11/2026). O preço não inclui passagens, licença nem gorjetas.' ],
		[ 'A licença de pesca está incluída no preço?', 'Depende do operador: estava incluída em 4 dos nove pacotes que comparamos, não incluída em 3 e sem especificar em 2. Pergunte por escrito. Como tirar a licença e quanto custa: <a href="{n:permiso-pesca-argentina-paso-a-paso}">licença de pesca na Argentina, passo a passo</a>.' ],
		[ 'Os voos estão incluídos?', 'Nos lodges que lemos, não: o preço de Río Grande começa no aeroporto, os lodges de dourado deixam de fora passagens e traslados, e o voo fretado de Jurassic Lake é à parte. Alguns operadores incluem voos fretados ou traslados; o detalhe está em cada nota regional.' ],
		[ 'Os preços estão em dólares ou em pesos?', 'Na moeda que cada operador publica: os lodges em dólares, as saídas de um dia de El Calafate em pesos, e Bariloche nas duas, de operadores diferentes. Não convertemos uma na outra porque cada operador fixa o seu câmbio na reserva.' ],
		[ 'O acompanhante que não pesca paga o mesmo?', 'Em geral não: paga menos que um pescador, mas paga. US$ 650 por noite nos lodges de dourado e em um lodge de Río Grande, US$ 800 por noite em um lodge do rio Manso, e $ 185.000 ou $ 335.000 por um dia em El Calafate.' ],
		[ 'Como comparo opções?', 'Compare só o que cobre o mesmo: dias de pesca, proporção de guias, licença, traslados e voos. Se você nos enviar suas datas e seu grupo no <a href="{viaje}">formulário de pedido</a>, consultamos os operadores que se encaixam e respondemos em 48 horas com as opções lado a lado.' ],
	],
	'cierre'          => '<em>São os preços que os operadores publicam, cada um com a data em que o lemos, e mudam. Não nos hospedamos nesses lodges nem pescamos com esses operadores. Confirme o preço, o que inclui e as datas com o operador antes de reservar.</em>',
];

// Sources: our dated regional notes (each carries the operator pages and the dates read), plus the Santa Cruz River and Jurassic Lake guides.
$fn_nota = function ( string $c ) {
	return function ( string $i ) use ( $c ) {
		$p = pm_buscar( 'pm_nota', $c, $i ) ?? pm_buscar( 'pm_nota', $c, 'es' );
		return $p ? get_permalink( $p->ID ) : '';
	};
};
$fn_guia = function ( string $c ) {
	return function ( string $i ) use ( $c ) {
		$p = pm_tp_guia( $c, $i );
		return $p ? get_permalink( $p->ID ) : '';
	};
};
$fuente_nombre = [
	'bar' => [ 'es' => 'Pescamapa: cuánto cuesta un día de pesca con mosca en Bariloche y el Río Limay (precios al 6/11/2026)', 'en' => 'Pescamapa: how much does a day of fly fishing cost in Bariloche and the Limay River (prices as of 6/11/2026)', 'pt' => 'Pescamapa: quanto custa um dia de pesca com mosca em Bariloche e no Rio Limay (preços de 6/11/2026)', 'f' => '20261106', 'o' => 0, 'fn' => $fn_nota( 'precio-pesca-bariloche-limay' ) ],
	'nor' => [ 'es' => 'Pescamapa: cuánto cuesta una semana de pesca de trucha en Neuquén y Chubut (5/11/2026)', 'en' => 'Pescamapa: how much does a week of trout fishing cost in Neuquén and Chubut (5/11/2026)', 'pt' => 'Pescamapa: quanto custa uma semana de pesca de truta em Neuquén e Chubut (5/11/2026)', 'f' => '20261105', 'o' => 0, 'fn' => $fn_nota( 'precio-semana-trucha-patagonia-norte' ) ],
	'sur' => [ 'es' => 'Pescamapa: cuánto cuesta pescar en Tierra del Fuego y El Calafate (leído entre el 4 y el 13/11/2026)', 'en' => 'Pescamapa: how much does it cost to fish in Tierra del Fuego and El Calafate (read between 4 and 13/11/2026)', 'pt' => 'Pescamapa: quanto custa pescar em Tierra del Fuego e El Calafate (lido entre 4 e 13/11/2026)', 'f' => '20261113', 'o' => 0, 'fn' => $fn_nota( 'precio-pesca-patagonia-sur' ) ],
	'dor' => [ 'es' => 'Pescamapa: cuánto cuesta un viaje de pesca de dorado en Argentina (leído el 13/11/2026)', 'en' => 'Pescamapa: how much does a dorado fishing trip in Argentina cost (read 13/11/2026)', 'pt' => 'Pescamapa: quanto custa uma viagem de pesca de dourado na Argentina (lido em 13/11/2026)', 'f' => '20261113', 'o' => 0, 'fn' => $fn_nota( 'precio-viaje-dorado-lodge' ) ],
	'ami' => [ 'es' => 'Pescamapa: viaje de pesca de dorado con amigos (precios «desde» del 4/11/2026)', 'en' => 'Pescamapa: golden dorado fishing trip with friends ("from" prices of 4/11/2026)', 'pt' => 'Pescamapa: viagem de pesca de dourado com amigos (preços «a partir de» de 4/11/2026)', 'f' => '20261104', 'o' => 0, 'fn' => $fn_nota( 'viaje-dorado-amigos-precios' ) ],
	'inc' => [ 'es' => 'Pescamapa: qué incluye un paquete de pesca en Argentina y qué cuesta aparte', 'en' => 'Pescamapa: what is included in an Argentina fishing package and what is extra', 'pt' => 'Pescamapa: o que inclui um pacote de pesca na Argentina e o que é cobrado à parte', 'f' => '20261113', 'o' => 0, 'fn' => $fn_nota( 'paquete-incluye-no-incluye' ) ],
	'sc'  => [ 'es' => 'Pescamapa: guía del río Santa Cruz (referencia de tarifa de steelhead sin temporada, página del 5/03/2025)', 'en' => 'Pescamapa: Santa Cruz River guide (steelhead rate reference with no season, page of 5/03/2025)', 'pt' => 'Pescamapa: guia do rio Santa Cruz (referência de tarifa de steelhead sem temporada, página de 5/03/2025)', 'f' => '20261114', 'o' => 0, 'fn' => $fn_guia( 'rio-santa-cruz' ) ],
	'jur' => [ 'es' => 'Pescamapa: guía de Jurassic Lake (Lago Strobel)', 'en' => 'Pescamapa: Jurassic Lake (Lago Strobel) guide', 'pt' => 'Pescamapa: guia de Jurassic Lake (Lago Strobel)', 'f' => '20261113', 'o' => 0, 'fn' => $fn_guia( 'jurassic-lake' ) ],
];

$nota_tipo = 'datos';
$origen    = 'Cuánto cuesta un viaje de pesca a la Argentina 2026/27, vista general, 16/11/2026 (pedido del dueño). Cada cifra sale de una nota nuestra ya publicada con su fecha de lectura (Bariloche 6/11, Patagonia norte 5/11 y 10/11, Patagonia sur y Río Grande 4/11 y 13/11, Jurassic Lake 10/11 y 13/11, dorado 13/11, posada de Esquina 4/11, qué incluye un paquete) y de la guía del río Santa Cruz (referencia de steelhead sin temporada, página del 5/03/2025: se rotula como no vigente). No se leyó ninguna página nueva de operador ni se repiten las tablas completas de las notas regionales. Sin porcentajes de comisión, sin superlativos, sin la palabra agencia. Foto provisoria: la del dueño ya cargada en la nota precio-pesca-patagonia-sur (Unsplash, Samuel Quek). La clave en portugués no choca con las notas de precios existentes (slug quanto-custa-pescar-na-argentina-2026-27).';

$armar = function ( array $n ): string {
	return pm_sc_p( $n['p0'] )
		. pm_sc_h2( $n['h_tabla'] ) . pm_sc_p( $n['tabla_i'] ) . pm_tp_tabla( $n['tabla_cols'], $n['tabla_rows'] )
		. pm_sc_h2( $n['h_cambia'] ) . pm_sc_lista( $n['cambia_l'] )
		. pm_sc_h2( $n['h_incluye'] ) . pm_sc_p( $n['incluye_p'] )
		. pm_sc_h2( $n['h_comparar'] ) . pm_sc_p( $n['comparar_p'] )
		. pm_sc_h2( $n['h_notas'] ) . pm_sc_lista( $n['notas_l'] ) . pm_sc_p( $n['cta'] )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] )
		. pm_sc_p( $n['cierre'] );
};

$enlace_ancla = [
	'es' => 'Cuánto cuesta un viaje de pesca a la Argentina 2026/27',
	'en' => 'Argentina fishing trip cost 2026/27: lodges, packages, day trips',
	'pt' => 'Quanto custa pescar na Argentina: valores 2026/27',
];
$ancla_corta = [ 'es' => '/Respuesta corta/u', 'en' => '/Short answer/u', 'pt' => '/Resposta curta/u' ];
$destinos_enlace = [
	[
		'tipo' => 'pm_nota', 'donde' => 'parrafo', 'ancla' => $ancla_corta,
		'claves' => [ 'precio-pesca-bariloche-limay', 'precio-semana-trucha-patagonia-norte', 'precio-pesca-patagonia-sur', 'precio-viaje-dorado-lodge', 'viaje-dorado-amigos-precios', 'paquete-incluye-no-incluye', 'licencias-extranjeros-argentina', 'licencia-pesca-patagonia-precios', 'trucha-patagonia-pilar', 'permiso-pesca-argentina-paso-a-paso' ],
		'texto' => [
			'es' => 'Para ver los precios de todos los tipos de viaje juntos, con la fuente y la fecha de cada uno: %s.',
			'en' => 'To see the prices of every type of trip together, with the source and date of each: %s.',
			'pt' => 'Para ver os preços de todos os tipos de viagem juntos, com a fonte e a data de cada um: %s.',
		],
	],
];

if ( ! function_exists( 'pm_pp_olista' ) ) {
	/** Ordered list block. */
	function pm_pp_olista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">{$li}</ol>\n<!-- /wp:list -->\n\n";
	}
}

/** Resolve {g:clave}, {n:clave|texto}, {g:clave|texto}, {u:clave} and {viaje}; fails on anything left over. */
$resolver = function ( string $html, string $i ) use ( &$u ): string {
	$nota = function ( string $c ) use ( $i ): string {
		$por = [];
		foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $c, 'numberposts' => -1, 'fields' => 'ids' ] ) as $id ) {
			$por[ pm_idioma_de( (int) $id ) ] = (int) $id;
		}
		$id = $por[ $i ] ?? ( $por['es'] ?? 0 );
		if ( ! $id ) {
			WP_CLI::error( "No encuentro la nota {$c} (ni en español)." );
		}
		isset( $por[ $i ] ) || WP_CLI::warning( "nota {$c} no existe en {$i}: el enlace va a la versión en español" );
		return get_permalink( $id );
	};
	$guia = function ( string $c ) use ( $i ): string {
		$g = pm_tp_guia( $c, $i );
		$g || WP_CLI::error( "No encuentro la guía {$c}." );
		pm_idioma_de( $g->ID ) === $i || WP_CLI::warning( "guía {$c} no existe en {$i}: el enlace va a la versión en español" );
		return get_permalink( $g->ID );
	};
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( $nota( $m[1] ) ) . '">' . $m[2] . '</a>', $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( $guia( $m[1] ) ) . '">' . $m[2] . '</a>', $html );
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\}/', fn( $m ) => esc_url( $nota( $m[1] ) ), $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\}/', fn( $m ) => esc_url( $guia( $m[1] ) ), $html );
	$html = preg_replace_callback( '/\{u:([a-z0-9_]+)\}/', function ( $m ) use ( $u ) {
		isset( $u[ $m[1] ] ) || WP_CLI::error( "enlace externo desconocido {$m[1]}" );
		return esc_url( $u[ $m[1] ] );
	}, $html );
	$html = str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) ), $html );
	if ( preg_match( '/\{(n:|g:|u:|viaje)[^}]*\}?/', $html, $m ) ) {
		WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
	}
	return $html;
};

// Cover photo: the attachment already loaded for another note (credit and per-language alt live on the attachment).
$nota_foto = pm_buscar( 'pm_nota', $foto_de, 'es' );
$att       = $nota_foto ? (int) get_post_thumbnail_id( $nota_foto->ID ) : 0;
$att || WP_CLI::error( "la nota {$foto_de} (es) no tiene foto de portada: la foto es obligatoria." );

$existe = fn( string $i ) => array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i ) );
$cambios = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n = $N[ $i ];
	if ( $ya = $existe( $i ) ) {
		WP_CLI::log( "$clave ($i) #{$ya[0]->ID} ya existe: no toco nada." );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	count( $n['faq'] ) >= 8 && count( $n['faq'] ) <= 10 || WP_CLI::error( "$i: la FAQ debe tener entre 8 y 10 preguntas." );
	$cuerpo = $resolver( $armar( $n ), $i );
	if ( preg_match( '/PENDIENTE|no pudimos|agencia|agency|agência|pudemos/iu', wp_strip_all_tags( $cuerpo ) ) ) {
		WP_CLI::error( "$i: el texto tiene una palabra prohibida." );
	}
	$datos   = array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] );
	$fuentes = [];
	foreach ( $fuente_nombre as $k => $f ) {
		$fuentes[] = [ 'nombre' => $f[ $i ], 'url' => $f['url'] ?? $u[ $k ] ?? $f['fn']( $i ), 'oficial' => $f['o'], 'consultada' => $f['f'] ];
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . ' fuentes, ' . count( $n['faq'] ) . " FAQ; title $tl / description $dl; portada #$att" );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => $datos, 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => $nota_tipo, 'nota_fecha' => $hoy, 'nota_origen' => $origen ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		set_post_thumbnail( $id, $att );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_post( $id )->post_name === $n['slug'] && pm_clave_de( $id ) === $clave && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $datos ) && false !== strpos( $cont, 'pm-faq' ) && count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && ! preg_match( '/\{(n|g|u):|\{viaje\}/', $cont );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
$urls_nuevas = [];
if ( $aplicar ) {
	$nota_es = $existe( 'es' )[0] ?? null;
	$e       = $nota_es ? pm_nota( $nota_es->ID ) : null;
	$ok      = $e && $e['fecha'] && (int) $e['foto'] === $att;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
	$ok || WP_CLI::error( 'la nota es no lee fecha o foto como se esperaba.' );
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$x = $existe( $i )[0] ?? null;
		$x || WP_CLI::error( "falta la nota ($i) para el hreflang." );
		$urls_nuevas[ $i ] = get_permalink( $x->ID );
	}
	WP_CLI::log( 'VERIFIED: tres notas con la misma clave (hreflang es/en/pt se arma por clave): ' . implode( ' | ', $urls_nuevas ) );
	flush_rewrite_rules( false );
} else {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$urls_nuevas[ $i ] = trailingslashit( pm_url_notas( $i ) ) . $N[ $i ]['slug'] . '/';
	}
}

// --- Inbound links: one paragraph in each target (skipped when the new URL is already there) ---
foreach ( $destinos_enlace as $dest ) {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$url  = $urls_nuevas[ $i ];
		$link = pm_sc_a( $url, esc_html( $enlace_ancla[ $i ] ) );
		foreach ( $dest['claves'] as $c ) {
			$p = pm_buscar( $dest['tipo'], $c, $i );
			if ( ! $p ) {
				WP_CLI::log( "{$dest['tipo']} $c ($i): no existe, no enlazo." );
				continue;
			}
			$cont = $p->post_content;
			if ( false !== strpos( $cont, $url ) ) {
				WP_CLI::log( "{$dest['tipo']} $c ($i) #{$p->ID}: ya enlaza." );
				continue;
			}
			$pos = null;
			$via = '';
			if ( 'permiso' === $dest['donde'] ) {
				foreach ( pm_sc_encabezados( $cont ) as $k => [ $off, $txt ] ) {
					if ( preg_match( '/permis|licen[cç]|permit|licen[cs]e/iu', $txt ) ) {
						$pos = pm_sc_encabezados( $cont )[ $k + 1 ][0] ?? strlen( $cont );
						$via = 'al final de la sección del permiso';
						break;
					}
				}
			} else {
				$pos = pm_sc_pos_tras_parrafo( $cont, $dest['ancla'][ $i ] );
				$via = 'tras el párrafo ancla';
				if ( null === $pos ) {
					$pos = pm_sc_pos_tras_parrafo( $cont, '/./u' );
					$via = 'tras el primer párrafo';
				}
			}
			if ( null === $pos ) {
				WP_CLI::warning( "{$dest['tipo']} $c ($i) #{$p->ID}: sin lugar para el enlace ({$dest['donde']}); no lo agrego a ciegas." );
				continue;
			}
			WP_CLI::log( pm_sc_modo( $aplicar ) . ": {$dest['tipo']} $c ($i) #{$p->ID}: enlace $via" );
			$cambios++;
			if ( $aplicar ) {
				pm_sc_guardar( $p->ID, pm_sc_insertar( $cont, $pos, pm_sc_p( sprintf( $dest['texto'][ $i ], $link ) ) ), [ $url ], "{$dest['tipo']} $c ($i)" );
				WP_CLI::log( "VERIFIED: {$dest['tipo']} $c ($i) #{$p->ID} enlaza la página nueva" );
			}
		}
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
