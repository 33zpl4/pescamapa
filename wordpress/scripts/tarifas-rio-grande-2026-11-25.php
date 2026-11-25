<?php
/**
 * Corrige la cifra de la semana de trucha de mar en el río Grande (Tierra del Fuego), 25/11/2026.
 * Por qué: las notas decían «US$ 12.950 a US$ 14.950» como si fuera el rango del río. Es la tarifa de los dos lodges más caros, en habitación
 * individual (único tipo que publican). Tarifas publicadas 2026/27, 7 noches y 6 días de pesca, por persona, comprobadas por el equipo en las
 * páginas de lodges y agencias el 25/11/2026: habitación doble de US$ 7.900 (primera semana de enero) a US$ 10.300 (semanas de más demanda);
 * habitación individual de US$ 8.900 a US$ 14.950.
 *
 * Qué cambia (solo las frases que llevan la cifra vieja como rango del río; reemplazo por cadena exacta contra el texto vivo):
 *   - cuanto-cuesta-viaje-pesca-argentina (es/en/pt): respuesta corta, fila de la tabla (cifra y fecha de lectura), viñeta «La fecha», tres FAQ, dato clave 4.
 *   - precio-pesca-patagonia-sur (es/en/pt): respuesta corta, datos clave 1 y 2, meta description («desde US$ 7.900»).
 *   - como-preparar-viaje-pesca-argentina (es/en/pt): párrafo p_4 y FAQ («hasta US$ 14.950 por una semana ... en habitación individual»).
 *   - paquetes-de-pesca-argentina (es/en/pt): la línea que copia el dato clave 1 de la nota de Patagonia sur.
 * Qué NO cambia: las tablas por lodge de precio-pesca-patagonia-sur (cada fila es la tarifa de un lodge en habitación individual, con su fecha),
 * las fichas de los lodges y data/precios.json (cada fila es un lodge, ya marcada single). `actualizado` no se toca (es «última actualización»
 * de la nota entera, no la fecha de lectura de los precios).
 * Además: suma las siete páginas de tarifas a las fuentes de las dos notas de precio (consultadas el 25/11/2026) y una línea de corrección en nota_origen (es).
 *
 * Si una cadena vieja no está (ni la nueva), el script se detiene antes de escribir nada y lista lo que falta. Idempotente: la segunda corrida no cambia nada.
 * Simulacro primero ($aplicar = false). Se corre con wp-script.yml (script = tarifas-rio-grande-2026-11-25).
 */
$aplicar = false;

if ( ! function_exists( 'pm_tf_plano' ) ) {
	function pm_tf_plano( string $h ): string {
		return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $h ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
	}
	/** Replace $old by $new in $c. Scope (optional): the one <tr>/<li>/<p> block containing $scope is the only place searched. Returns [ estado, nuevo ]. */
	function pm_tf_reemplazar( string $c, array $r ): array {
		$old = $r['old'];
		$new = $r['new'];
		if ( empty( $r['scope'] ) ) {
			$n = substr_count( $c, $old );
			if ( 1 === $n ) {
				return [ 'pendiente', str_replace( $old, $new, $c ) ];
			}
			if ( $n > 1 ) {
				return [ 'ambigua', $c ];
			}
			return [ false !== strpos( $c, $new ) ? 'hecho' : 'falta', $c ];
		}
		$tag = $r['tag'] ?? 'tr';
		preg_match_all( "#<{$tag}\\b[^>]*>.*?</{$tag}>#su", $c, $m, PREG_OFFSET_CAPTURE );
		$bloques = array_values( array_filter( $m[0], fn( $b ) => false !== strpos( $b[0], $r['scope'] ) ) );
		if ( 1 !== count( $bloques ) ) {
			return [ 'falta', $c ];
		}
		[ $bloque, $pos ] = $bloques[0];
		$n = substr_count( $bloque, $old );
		if ( 1 === $n ) {
			return [ 'pendiente', substr_replace( $c, str_replace( $old, $new, $bloque ), $pos, strlen( $bloque ) ) ];
		}
		if ( $n > 1 ) {
			return [ 'ambigua', $c ];
		}
		return [ false !== strpos( $bloque, $new ) ? 'hecho' : 'falta', $c ];
	}
}

// ---------------------------------------------------------------------------------------------------------------------------------
// Texts
// ---------------------------------------------------------------------------------------------------------------------------------
$H = [
	'es' => 'Una semana de trucha de mar en el río Grande (7 noches, 6 días de pesca) cuesta de US$ 7.900 a US$ 10.300 por persona en habitación doble y de US$ 8.900 a US$ 14.950 en habitación individual, según el lodge y la semana (tarifas publicadas para 2026/27, leídas el 25/11/2026).',
	'en' => 'A week of sea-run trout fishing on the Río Grande (7 nights, 6 fishing days) costs US$ 7,900 to US$ 10,300 per person in a double room and US$ 8,900 to US$ 14,950 in a single room, depending on the lodge and the week (rates published for 2026/27, read on 25/11/2026).',
	'pt' => 'Uma semana de truta-do-mar no rio Grande (7 noites, 6 dias de pesca) custa de US$ 7.900 a US$ 10.300 por pessoa em quarto duplo e de US$ 8.900 a US$ 14.950 em quarto individual, conforme o lodge e a semana (tarifas publicadas para 2026/27, lidas em 25/11/2026).',
];
$LIC = [
	'es' => 'La licencia va aparte.',
	'en' => 'The license is extra.',
	'pt' => 'A licença é à parte.',
];
$LIC_LODGES = [
	'es' => 'La licencia va aparte (los lodges la cotizan en unos US$ 300 a 550 por pescador).',
	'en' => 'The license is extra (lodges quote it at about US$ 300 to 550 per angler).',
	'pt' => 'A licença é à parte (os lodges a cotam em cerca de US$ 300 a 550 por pescador).',
];
$INCL = [
	'es' => 'Lo habitual: alojamiento, todas las comidas y bebidas, traslado desde el aeropuerto de Río Grande y guía compartido (dos pescadores por guía), y en algunos lodges equipo de préstamo. No incluye pasajes ni propinas.',
	'en' => 'What the week usually includes: lodging, all meals and drinks, transfer from Río Grande airport and a shared guide (two anglers per guide), and loan rods and reels at some lodges. Flights and tips are not included.',
	'pt' => 'O que a semana costuma incluir: hospedagem, todas as refeições e bebidas, traslado desde o aeroporto de Río Grande e guia compartilhado (dois pescadores por guia), e em alguns lodges equipamento de empréstimo. Não inclui passagens nem gorjetas.',
];
$ORIGEN = 'Corrección 25/11/2026: la cifra de la semana de trucha de mar en el río Grande («US$ 12.950 a US$ 14.950») era la tarifa de los dos lodges más caros, solo en habitación individual, presentada como el rango del río. Ahora es el rango de las tarifas publicadas para 2026/27 en habitación doble (US$ 7.900 a 10.300) e individual (US$ 8.900 a 14.950), tarifas comprobadas por el equipo en las páginas de lodges y agencias el 25/11/2026.';

$FUENTES_URLS = [
	[ 'kautapen.com', 'https://www.kautapen.com/rates.html', '' ],
	[ 'vmarialodge.com', 'https://www.vmarialodge.com/rates.html', '' ],
	[ 'aardvarkmcleod.com', 'https://www.aardvarkmcleod.com/availability/argentina-estancia-maria-behety-la-villa-2027-availability/', '' ],
	[ 'tridentflyfishing.com', 'https://www.tridentflyfishing.com/products/maria-behety-fly-fishing-lodge', ' (1/2)' ],
	[ 'tridentflyfishing.com', 'https://www.tridentflyfishing.com/products/la-villa-de-maria-behety-fly-fishing-lodge', ' (2/2)' ],
	[ 'farbank.com', 'https://farbank.com/products/fly-water-travel-estancia-despedida', '' ],
	[ 'flyfishingpatagonia.com', 'https://www.flyfishingpatagonia.com/sea-run-brown-fishing/sea-run-brown-lodges/estancia-san-jose-sea-run-brown-trout-fishing/', '' ],
];
$FUENTE_PLANTILLA = [
	'es' => '%s: tarifas publicadas 2026/27 de la semana de trucha de mar%s',
	'en' => '%s: published 2026/27 rates for the sea-run trout week%s',
	'pt' => '%s: tarifas publicadas 2026/27 da semana de truta-do-mar%s',
];

$PLAN = []; // [ clave ][ idioma ] => [ 'contenido' => [ r… ], 'datos' => [ r… ], 'seo_description' => [ old, new ] | null, 'fuentes' => bool, 'origen' => bool ]

// ---- cuanto-cuesta-viaje-pesca-argentina -------------------------------------------------------------------------------------------
$c = 'cuanto-cuesta-viaje-pesca-argentina';
$PLAN[ $c ]['es'] = [
	'contenido' => [
		[ 'old' => 'hasta <strong>US$ 12.950 a US$ 14.950 por una semana de trucha de mar</strong>', 'new' => 'hasta <strong>US$ 14.950 por una semana de trucha de mar en habitación individual</strong>' ],
		[ 'old' => 'De US$ 12.950 a US$ 14.950 por una semana, según las fechas (habitación single)', 'new' => 'De US$ 7.900 a US$ 10.300 en habitación doble y de US$ 8.900 a US$ 14.950 en individual, por persona y semana, según el lodge y la semana' ],
		[ 'scope' => 'Trucha de mar, Río Grande (Tierra del Fuego)', 'tag' => 'tr', 'old' => 'leído el 4/11 y el 13/11/2026', 'new' => 'leído el 25/11/2026' ],
		[ 'old' => 'la semana de Río Grande cuesta US$ 12.950, US$ 13.950 o US$ 14.950 según las fechas, y', 'new' => 'la semana de Río Grande en habitación doble va de US$ 7.900 (primera semana de enero) a US$ 10.300 (de fines de enero a mediados de marzo), y' ],
		[ 'old' => 'hasta US$ 12.950 a US$ 14.950 por una semana de trucha de mar en Río Grande (4/11/2026)', 'new' => 'hasta US$ 14.950 por una semana de trucha de mar en Río Grande en habitación individual (25/11/2026)' ],
		[ 'old' => 'En Tierra del Fuego, una semana de trucha de mar cuesta de US$ 12.950 a US$ 14.950 (4/11/2026).', 'new' => 'En Tierra del Fuego, una semana de trucha de mar (7 noches, 6 días de pesca) cuesta de US$ 7.900 a US$ 10.300 por persona en habitación doble y de US$ 8.900 a US$ 14.950 en individual (25/11/2026).' ],
		[ 'old' => 'Un lodge de Río Grande publica de US$ 12.950 a US$ 14.950 por persona por una semana, según las fechas, en habitación single y con guía compartido (leído el 4/11/2026); otro lodge publica el mismo rango para 2027 (leído el 13/11/2026). El precio no incluye pasajes, licencia ni propinas.', 'new' => $H['es'] . ' ' . $LIC_LODGES['es'] . ' ' . $INCL['es'] ],
	],
	'datos' => [
		[ 'old' => 'de US$ 12.950 a US$ 14.950 por persona por semana', 'new' => 'de US$ 7.900 a US$ 10.300 por persona por semana en habitación doble y de US$ 8.900 a US$ 14.950 en individual' ],
	],
	'seo_description' => null, 'fuentes' => true, 'origen' => true,
];
$PLAN[ $c ]['en'] = [
	'contenido' => [
		[ 'old' => 'to <strong>US$ 12,950 to US$ 14,950 for a week of sea-run trout</strong>', 'new' => 'to <strong>US$ 14,950 for a week of sea-run trout in a single room</strong>' ],
		[ 'old' => 'US$ 12,950 to US$ 14,950 for a week, depending on the dates (single room)', 'new' => 'US$ 7,900 to US$ 10,300 in a double room and US$ 8,900 to US$ 14,950 in a single room, per person per week, depending on the lodge and the week' ],
		[ 'scope' => 'Sea-run trout, Río Grande (Tierra del Fuego)', 'tag' => 'tr', 'old' => 'read 4/11 and 13/11/2026', 'new' => 'read 25/11/2026' ],
		[ 'old' => 'the Río Grande week costs US$ 12,950, US$ 13,950 or US$ 14,950 depending on the dates, and', 'new' => 'the Río Grande week in a double room runs from US$ 7,900 (first week of January) to US$ 10,300 (late January to mid-March), and' ],
		[ 'old' => 'to US$ 12,950 to US$ 14,950 for a week of sea-run trout in Río Grande (4/11/2026)', 'new' => 'to US$ 14,950 for a week of sea-run trout in Río Grande in a single room (25/11/2026)' ],
		[ 'old' => 'In Tierra del Fuego, a week of sea-run trout is US$ 12,950 to US$ 14,950 (4/11/2026).', 'new' => 'In Tierra del Fuego, a week of sea-run trout (7 nights, 6 fishing days) is US$ 7,900 to US$ 10,300 per person in a double room and US$ 8,900 to US$ 14,950 in a single room (25/11/2026).' ],
		[ 'old' => 'A Río Grande lodge publishes US$ 12,950 to US$ 14,950 per person for a week, depending on the dates, in a single room and with a shared guide (read 4/11/2026); another lodge publishes the same range for 2027 (read 13/11/2026). The price does not include flights, the license or tips.', 'new' => $H['en'] . ' ' . $LIC_LODGES['en'] . ' ' . $INCL['en'] ],
	],
	'datos' => [
		[ 'old' => 'US$ 12,950 to US$ 14,950 per person for a week', 'new' => 'US$ 7,900 to US$ 10,300 per person for a week in a double room and US$ 8,900 to US$ 14,950 in a single room' ],
	],
	'seo_description' => null, 'fuentes' => true, 'origen' => false,
];
$PLAN[ $c ]['pt'] = [
	'contenido' => [
		[ 'old' => 'a <strong>US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar</strong>', 'new' => 'a <strong>US$ 14.950 por uma semana de truta-do-mar em quarto individual</strong>' ],
		[ 'old' => 'De US$ 12.950 a US$ 14.950 por uma semana, conforme as datas (quarto single)', 'new' => 'De US$ 7.900 a US$ 10.300 em quarto duplo e de US$ 8.900 a US$ 14.950 em quarto individual, por pessoa e semana, conforme o lodge e a semana' ],
		[ 'scope' => 'Truta-do-mar, Río Grande (Terra do Fogo)', 'tag' => 'tr', 'old' => 'lido em 4/11 e 13/11/2026', 'new' => 'lido em 25/11/2026' ],
		[ 'old' => 'a semana de Río Grande custa US$ 12.950, US$ 13.950 ou US$ 14.950 conforme as datas, e', 'new' => 'a semana de Río Grande em quarto duplo vai de US$ 7.900 (primeira semana de janeiro) a US$ 10.300 (do fim de janeiro a meados de março), e' ],
		[ 'old' => 'a US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar em Río Grande (4/11/2026)', 'new' => 'a US$ 14.950 por uma semana de truta-do-mar em Río Grande em quarto individual (25/11/2026)' ],
		[ 'old' => 'Na Terra do Fogo, uma semana de truta-do-mar custa de US$ 12.950 a US$ 14.950 (4/11/2026).', 'new' => 'Na Terra do Fogo, uma semana de truta-do-mar (7 noites, 6 dias de pesca) custa de US$ 7.900 a US$ 10.300 por pessoa em quarto duplo e de US$ 8.900 a US$ 14.950 em quarto individual (25/11/2026).' ],
		[ 'old' => 'Um lodge de Río Grande publica de US$ 12.950 a US$ 14.950 por pessoa por uma semana, conforme as datas, em quarto single e com guia compartilhado (lido em 4/11/2026); outro lodge publica a mesma faixa para 2027 (lido em 13/11/2026). O preço não inclui passagens, licença nem gorjetas.', 'new' => $H['pt'] . ' ' . $LIC_LODGES['pt'] . ' ' . $INCL['pt'] ],
	],
	'datos' => [
		[ 'old' => 'de US$ 12.950 a US$ 14.950 por pessoa por semana', 'new' => 'de US$ 7.900 a US$ 10.300 por pessoa por semana em quarto duplo e de US$ 8.900 a US$ 14.950 em quarto individual' ],
	],
	'seo_description' => null, 'fuentes' => true, 'origen' => false,
];

// ---- precio-pesca-patagonia-sur ----------------------------------------------------------------------------------------------------
$c = 'precio-pesca-patagonia-sur';
$d0 = [
	'es' => 'Una semana en un lodge de trucha de mar en Río Grande (Tierra del Fuego) cuesta de US$ 12.950 a US$ 14.950 por persona, según la fecha, en la temporada 2026/27.',
	'en' => 'A week at a sea trout lodge in Río Grande (Tierra del Fuego) costs from US$ 12,950 to US$ 14,950 per person, depending on the date, in the 2026/27 season.',
	'pt' => 'Uma semana em um lodge de truta-do-mar em Río Grande (Tierra del Fuego) custa de US$ 12.950 a US$ 14.950 por pessoa, conforme a data, na temporada 2026/27.',
];
$PLAN[ $c ]['es'] = [
	'contenido' => [
		[ 'old' => 'Una <strong>semana de trucha de mar en un lodge de Río Grande</strong> (Tierra del Fuego) cuesta <strong>de US$ 12.950 a US$ 14.950 por persona</strong>.', 'new' => 'Una <strong>semana de trucha de mar en el río Grande</strong> (Tierra del Fuego; 7 noches, 6 días de pesca) cuesta <strong>de US$ 7.900 a US$ 10.300 por persona en habitación doble y de US$ 8.900 a US$ 14.950 en habitación individual</strong>, según el lodge y la semana (tarifas publicadas para 2026/27, leídas el 25/11/2026). ' . $LIC_LODGES['es'] ],
	],
	'datos' => [
		[ 'old' => $d0['es'], 'new' => $H['es'] . ' ' . $LIC['es'] ],
		[ 'old' => 'La tarifa de ese lodge es en habitación single y con guía compartido, e incluye comidas, bebidas y traslado desde el aeropuerto.', 'new' => 'Lo habitual: incluye alojamiento, comidas, bebidas y traslado desde el aeropuerto de Río Grande, con guía compartido (dos pescadores por guía); no incluye pasajes, propinas ni licencia.' ],
	],
	'seo_description' => [ 'Precio de pescar en la Patagonia sur: una semana de trucha de mar en Río Grande desde US$ 12.950 y una salida de día en El Calafate desde $ 440.000.', 'Precio de pescar en la Patagonia sur: una semana de trucha de mar en Río Grande desde US$ 7.900 y una salida de día en El Calafate desde $ 440.000.' ],
	'fuentes' => true, 'origen' => true,
];
$PLAN[ $c ]['en'] = [
	'contenido' => [
		[ 'old' => 'A <strong>week of sea trout at a Río Grande lodge</strong> (Tierra del Fuego) costs <strong>from US$ 12,950 to US$ 14,950 per person</strong>.', 'new' => 'A <strong>week of sea-run trout on the Río Grande</strong> (Tierra del Fuego; 7 nights, 6 fishing days) costs <strong>US$ 7,900 to US$ 10,300 per person in a double room and US$ 8,900 to US$ 14,950 in a single room</strong>, depending on the lodge and the week (rates published for 2026/27, read on 25/11/2026). ' . $LIC_LODGES['en'] ],
	],
	'datos' => [
		[ 'old' => $d0['en'], 'new' => $H['en'] . ' ' . $LIC['en'] ],
		[ 'old' => 'That lodge rate is for a single room and shared guide, and includes meals, drinks, and transfer from the airport.', 'new' => 'What the week usually includes: lodging, meals, drinks and the transfer from the airport, with a shared guide (two anglers per guide); flights, tips and the license are extra.' ],
	],
	'seo_description' => [ 'Fishing prices in southern Patagonia: a week of sea-run trout in Río Grande from US$ 12,950 and a day trip in El Calafate from $ 440,000.', 'Fishing prices in southern Patagonia: a week of sea-run trout in Río Grande from US$ 7,900 and a day trip in El Calafate from $ 440,000.' ],
	'fuentes' => true, 'origen' => false,
];
$PLAN[ $c ]['pt'] = [
	'contenido' => [
		[ 'old' => 'Uma <strong>semana de truta-do-mar em um lodge de Río Grande</strong> (Tierra del Fuego) custa <strong>de US$ 12.950 a US$ 14.950 por pessoa</strong>.', 'new' => 'Uma <strong>semana de truta-do-mar no rio Grande</strong> (Tierra del Fuego; 7 noites, 6 dias de pesca) custa <strong>de US$ 7.900 a US$ 10.300 por pessoa em quarto duplo e de US$ 8.900 a US$ 14.950 em quarto individual</strong>, conforme o lodge e a semana (tarifas publicadas para 2026/27, lidas em 25/11/2026). ' . $LIC_LODGES['pt'] ],
	],
	'datos' => [
		[ 'old' => $d0['pt'], 'new' => $H['pt'] . ' ' . $LIC['pt'] ],
		[ 'old' => 'A tarifa desse lodge é em quarto single e com guia compartilhado, e inclui refeições, bebidas e traslado desde o aeroporto.', 'new' => 'O que a semana costuma incluir: hospedagem, refeições, bebidas e traslado desde o aeroporto de Río Grande, com guia compartilhado (dois pescadores por guia); passagens, gorjetas e licença ficam por fora.' ],
	],
	'seo_description' => [ 'Preço para pescar na Patagônia sul: uma semana de truta-do-mar em Río Grande a partir de US$ 12.950 e um passeio de dia em El Calafate desde $ 440.000.', 'Preço para pescar na Patagônia sul: uma semana de truta-do-mar em Río Grande a partir de US$ 7.900 e um passeio de dia em El Calafate desde $ 440.000.' ],
	'fuentes' => true, 'origen' => false,
];

// ---- como-preparar-viaje-pesca-argentina (hub) ------------------------------------------------------------------------------------
$c = 'como-preparar-viaje-pesca-argentina';
$PLAN[ $c ]['es'] = [ 'contenido' => [
	[ 'old' => 'hasta US$ 12.950 a US$ 14.950 por una semana de trucha de mar en Río Grande, según', 'new' => 'hasta US$ 14.950 por una semana de trucha de mar en Río Grande en habitación individual, según' ],
	[ 'old' => 'hasta US$ 12.950 a US$ 14.950 por una semana de trucha de mar, cada una', 'new' => 'hasta US$ 14.950 por una semana de trucha de mar en habitación individual, cada una' ],
], 'datos' => [], 'seo_description' => null, 'fuentes' => false, 'origen' => false ];
$PLAN[ $c ]['en'] = [ 'contenido' => [
	[ 'old' => 'to US$ 12,950 to US$ 14,950 for a week of sea-run trout in Río Grande, according', 'new' => 'to US$ 14,950 for a week of sea-run trout in Río Grande in a single room, according' ],
	[ 'old' => 'to US$ 12,950 to US$ 14,950 for a week of sea-run trout, each', 'new' => 'to US$ 14,950 for a week of sea-run trout in a single room, each' ],
], 'datos' => [], 'seo_description' => null, 'fuentes' => false, 'origen' => false ];
$PLAN[ $c ]['pt'] = [ 'contenido' => [
	[ 'old' => 'a US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar em Río Grande, segundo', 'new' => 'a US$ 14.950 por uma semana de truta-do-mar em Río Grande em quarto individual, segundo' ],
	[ 'old' => 'a US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar, cada um', 'new' => 'a US$ 14.950 por uma semana de truta-do-mar em quarto individual, cada um' ],
], 'datos' => [], 'seo_description' => null, 'fuentes' => false, 'origen' => false ];

// ---- paquetes-de-pesca-argentina (index that copies datos[0] of the Patagonia sur note) ----------------------------------------------
$c = 'paquetes-de-pesca-argentina';
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$PLAN[ $c ][ $i ] = [ 'contenido' => [ [ 'old' => $d0[ $i ], 'new' => $H[ $i ] . ' ' . $LIC[ $i ] ] ], 'datos' => [], 'seo_description' => null, 'fuentes' => false, 'origen' => false ];
}

// ---------------------------------------------------------------------------------------------------------------------------------
// Phase 1: plan against the LIVE text; nothing is written here
// ---------------------------------------------------------------------------------------------------------------------------------
$ops    = [];
$faltan = [];
$pend   = 0;
$hechas = 0;
foreach ( $PLAN as $clave => $por_idioma ) {
	foreach ( $por_idioma as $i => $p ) {
		$post = pm_buscar( 'pm_nota', $clave, $i );
		if ( ! $post ) {
			$faltan[] = "$clave ($i): la nota no existe";
			continue;
		}
		$id     = $post->ID;
		$op     = [ 'id' => $id, 'clave' => $clave, 'i' => $i, 'contenido' => $post->post_content, 'cambia_contenido' => false, 'datos' => null, 'seo' => null, 'fuentes' => null, 'origen' => null, 'checks' => [] ];
		WP_CLI::log( "== $clave ($i) #$id " . get_permalink( $id ) );

		foreach ( $p['contenido'] as $k => $r ) {
			[ $estado, $nuevo ] = pm_tf_reemplazar( $op['contenido'], $r );
			WP_CLI::log( "  contenido[$k] $estado" . ( ! empty( $r['scope'] ) ? ' (dentro del bloque que lleva «' . $r['scope'] . '»)' : '' ) );
			if ( 'pendiente' === $estado ) {
				WP_CLI::log( '    BEFORE: ' . pm_tf_plano( $r['old'] ) );
				WP_CLI::log( '    AFTER:  ' . pm_tf_plano( $r['new'] ) );
				$op['contenido']        = $nuevo;
				$op['cambia_contenido'] = true;
				$op['checks'][]         = $r;
				$pend++;
			} elseif ( 'hecho' === $estado ) {
				$op['checks'][] = $r;
				$hechas++;
			} else {
				$faltan[] = "$clave ($i) contenido[$k] ($estado): no encuentro «" . pm_tf_plano( $r['old'] ) . '»';
			}
		}

		if ( $p['datos'] ) {
			$filas = pm_filas( 'nota_datos', $id );
			$mod   = false;
			foreach ( $p['datos'] as $k => $r ) {
				$con_old = array_keys( array_filter( $filas, fn( $f ) => false !== strpos( (string) ( $f['dato'] ?? '' ), $r['old'] ) ) );
				$con_new = array_keys( array_filter( $filas, fn( $f ) => false !== strpos( (string) ( $f['dato'] ?? '' ), $r['new'] ) ) );
				if ( 1 === count( $con_old ) ) {
					$j = $con_old[0];
					WP_CLI::log( "  nota_datos[$j] pendiente" );
					WP_CLI::log( '    BEFORE: ' . $filas[ $j ]['dato'] );
					$filas[ $j ]['dato'] = str_replace( $r['old'], $r['new'], $filas[ $j ]['dato'] );
					WP_CLI::log( '    AFTER:  ' . $filas[ $j ]['dato'] );
					$mod = true;
					$pend++;
				} elseif ( ! $con_old && $con_new ) {
					WP_CLI::log( "  nota_datos[{$con_new[0]}] hecho" );
					$hechas++;
				} else {
					$faltan[] = "$clave ($i) nota_datos: no encuentro (o hay más de una fila con) «{$r['old']}»";
				}
				$op['checks_datos'][] = $r;
			}
			$op['datos'] = $mod ? $filas : null;
		}

		if ( $p['seo_description'] ) {
			[ $old, $new ] = $p['seo_description'];
			$act = (string) get_post_meta( $id, 'seo_description', true );
			$len = mb_strlen( $new );
			if ( $act === $old ) {
				if ( $len < 70 || $len > 160 ) {
					$faltan[] = "$clave ($i) seo_description fuera de rango ($len car.)";
				}
				WP_CLI::log( "  seo_description pendiente ($len car.)" );
				WP_CLI::log( "    BEFORE: $act" );
				WP_CLI::log( "    AFTER:  $new" );
				$op['seo'] = $new;
				$pend++;
			} elseif ( $act === $new ) {
				WP_CLI::log( '  seo_description hecho' );
				$hechas++;
			} else {
				$faltan[] = "$clave ($i) seo_description: el texto vivo no es el esperado: «$act»";
			}
			$op['seo_esperado'] = $new;
		}

		if ( $p['fuentes'] ) {
			$filas = pm_filas( 'fuentes', $id );
			$urls  = array_map( fn( $f ) => (string) ( $f['url'] ?? '' ), $filas );
			// Keep the stored dates in the canonical Ymd form ACF saves.
			$filas = array_map( function ( $f ) {
				if ( isset( $f['consultada'] ) && preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', (string) $f['consultada'], $m ) ) {
					$f['consultada'] = $m[1] . $m[2] . $m[3];
				}
				return $f;
			}, $filas );
			$antes = $filas;
			$nuevas = 0;
			foreach ( $FUENTES_URLS as [ $dominio, $url, $suf ] ) {
				if ( in_array( $url, $urls, true ) ) {
					continue;
				}
				$filas[] = [ 'nombre' => sprintf( $FUENTE_PLANTILLA[ $i ], $dominio, $suf ), 'url' => $url, 'oficial' => 0, 'consultada' => '20261125' ];
				$nuevas++;
			}
			WP_CLI::log( "  fuentes: " . count( $antes ) . " existentes, $nuevas nuevas (consultada 25/11/2026)" );
			if ( $nuevas ) {
				$op['fuentes'] = $filas;
				$op['fuentes_antes'] = $antes;
				$pend++;
			} else {
				$hechas++;
			}
		}

		if ( $p['origen'] ) {
			$act = (string) get_post_meta( $id, 'nota_origen', true );
			if ( '' === $act ) {
				WP_CLI::log( '  nota_origen: vacío en esta nota, no la toco' );
			} elseif ( false !== strpos( $act, 'Corrección 25/11/2026' ) ) {
				WP_CLI::log( '  nota_origen hecho' );
				$hechas++;
			} else {
				WP_CLI::log( '  nota_origen pendiente: se agrega al final la línea de corrección' );
				$op['origen'] = rtrim( $act ) . ' ' . $ORIGEN;
				$pend++;
			}
		}
		$ops[] = $op;
	}
}

if ( $faltan ) {
	foreach ( $faltan as $f ) {
		WP_CLI::warning( 'FALTA: ' . $f );
	}
	WP_CLI::error( count( $faltan ) . ' reemplazo(s) no se pueden hacer con el texto vivo; no se escribió nada.' );
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $pend cambios pendientes, $hechas ya hechos, en " . count( $ops ) . ' notas.' );
if ( ! $aplicar || ! $pend ) {
	// What stays (information): remaining occurrences of the old figures per note.
	foreach ( $ops as $op ) {
		$resto = preg_match_all( '/1[234][.,]950/u', pm_tf_plano( $op['contenido'] ) );
		WP_CLI::log( "  {$op['clave']} ({$op['i']}) #{$op['id']}: quedan $resto apariciones de 12.950/13.950/14.950 en el contenido tras el cambio" . ( $resto ? ' (tablas por lodge y la nueva cifra tope)' : '' ) );
	}
	WP_CLI::log( $pend ? 'Simulacro: nada escrito.' : 'Nada que cambiar: ya aplicado.' );
	return;
}

// ---------------------------------------------------------------------------------------------------------------------------------
// Phase 2: write and read back
// ---------------------------------------------------------------------------------------------------------------------------------
foreach ( $ops as $op ) {
	$id = $op['id'];
	if ( $op['cambia_contenido'] ) {
		$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $op['contenido'] ) ], true );
		is_wp_error( $r ) && WP_CLI::error( "{$op['clave']} ({$op['i']}): " . $r->get_error_message() );
	}
	if ( null !== $op['datos'] ) {
		update_field( 'nota_datos', $op['datos'], $id );
	}
	if ( null !== $op['seo'] ) {
		update_field( 'seo_description', $op['seo'], $id );
	}
	if ( null !== $op['fuentes'] ) {
		update_field( 'fuentes', $op['fuentes'], $id );
	}
	if ( null !== $op['origen'] ) {
		update_field( 'nota_origen', $op['origen'], $id );
	}
}

$normal = fn( string $h ): string => str_replace( [ "\u{2019}", "\u{00A0}" ], [ "'", ' ' ], pm_tf_plano( $h ) );
foreach ( $ops as $op ) {
	$id    = $op['id'];
	$tag   = "{$op['clave']} ({$op['i']}) #$id";
	$ok    = true;
	$malos = [];
	// 1. Stored content
	clean_post_cache( $id );
	$guardado = (string) get_post_field( 'post_content', $id );
	foreach ( $op['checks'] as $r ) {
		if ( empty( $r['scope'] ) && false !== strpos( $guardado, $r['old'] ) ) {
			$malos[] = 'contenido aún tiene: ' . pm_tf_plano( $r['old'] );
		}
		if ( false === strpos( $guardado, $r['new'] ) ) {
			$malos[] = 'contenido no tiene: ' . pm_tf_plano( $r['new'] );
		}
	}
	// 2. Stored fields
	foreach ( $op['checks_datos'] ?? [] as $r ) {
		$txt = implode( "\n", array_map( fn( $f ) => (string) ( $f['dato'] ?? '' ), pm_filas( 'nota_datos', $id ) ) );
		if ( false !== strpos( $txt, $r['old'] ) || false === strpos( $txt, $r['new'] ) ) {
			$malos[] = 'nota_datos no quedó como se esperaba: ' . $r['new'];
		}
	}
	if ( isset( $op['seo_esperado'] ) && (string) get_post_meta( $id, 'seo_description', true ) !== $op['seo_esperado'] ) {
		$malos[] = 'seo_description no quedó como se esperaba';
	}
	if ( null !== $op['fuentes'] ) {
		$ahora = pm_filas( 'fuentes', $id );
		$n_ok  = count( $ahora ) === count( $op['fuentes'] );
		$u_ok  = ! array_diff( array_column( $op['fuentes'], 'url' ), array_column( $ahora, 'url' ) );
		$f_ok  = true;
		foreach ( $op['fuentes_antes'] as $k => $f ) {
			$c1 = preg_replace( '/\D/', '', (string) ( $f['consultada'] ?? '' ) );
			$c2 = preg_replace( '/\D/', '', (string) ( $ahora[ $k ]['consultada'] ?? '' ) );
			$f_ok = $f_ok && $c1 === $c2;
		}
		if ( ! ( $n_ok && $u_ok && $f_ok ) ) {
			$malos[] = 'fuentes: filas ' . count( $ahora ) . ' (esperadas ' . count( $op['fuentes'] ) . '), urls ' . ( $u_ok ? 'ok' : 'faltan' ) . ', fechas anteriores ' . ( $f_ok ? 'intactas' : 'CAMBIARON' );
		}
	}
	if ( null !== $op['origen'] && false === strpos( (string) get_post_meta( $id, 'nota_origen', true ), 'Corrección 25/11/2026' ) ) {
		$malos[] = 'nota_origen sin la línea de corrección';
	}
	// 3. Render (text match, not class attributes; cache-buster in the query)
	$resp = wp_remote_get( add_query_arg( 'tf', (string) time(), get_permalink( $id ) ), [ 'timeout' => 25 ] );
	$render = 'no verificado (sin respuesta)';
	if ( ! is_wp_error( $resp ) && 200 === (int) wp_remote_retrieve_response_code( $resp ) ) {
		$pagina = $normal( (string) wp_remote_retrieve_body( $resp ) );
		$falta  = 0;
		foreach ( $op['checks'] as $r ) {
			if ( false === strpos( $pagina, $normal( $r['new'] ) ) ) {
				$falta++;
				$malos[] = 'render sin: ' . pm_tf_plano( $r['new'] );
			}
			if ( empty( $r['scope'] ) && false !== strpos( $pagina, $normal( $r['old'] ) ) ) {
				$falta++;
				$malos[] = 'render aún con: ' . pm_tf_plano( $r['old'] );
			}
		}
		$render = $falta ? "$falta diferencias" : 'ok';
	} else {
		WP_CLI::warning( "$tag: no pude leer el render (" . ( is_wp_error( $resp ) ? $resp->get_error_message() : 'HTTP ' . wp_remote_retrieve_response_code( $resp ) ) . ')' );
	}
	WP_CLI::log( ( $malos ? 'FALLÓ' : 'VERIFIED' ) . ": $tag contenido guardado, campos y render ($render) " . get_permalink( $id ) );
	foreach ( $malos as $m ) {
		WP_CLI::warning( "  $m" );
	}
	$malos && WP_CLI::error( "$tag: la lectura de vuelta no coincide." );
}
WP_CLI::log( 'VERIFIED: ' . count( $ops ) . ' notas corregidas y leídas de vuelta.' );
