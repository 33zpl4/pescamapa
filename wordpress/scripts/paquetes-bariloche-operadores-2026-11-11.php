<?php
/**
 * Bariloche (es/en/pt), 11/11/2026: five day-trip packages, one per operator, with the price each one publishes.
 * Owner (11/11/2026): the site must be the place where the trip is ASKED for, so every operator gets a package with a real
 * price; we publish with public data, say so on the card, and tell them "confirm or correct your listing, or we take it down".
 * Supersedes the owner's rule of 28/10 (competitors' offers neither listed nor named) for these operators, and replaces
 * relying on the unconfirmed "desde" figures.
 *
 * Sources and dates, always on the card:
 *   - own website, read by Claude on 31/10/2026 (investigar.yml): Trout Bariloche 520, Patagonia in the Loop 550 (half day 430),
 *     Patagonia Drift Fly Fishing 550 (Limay) / 580 (lakes), Southern Rivers Patagonia 530;
 *   - TripAdvisor listing pasted by the owner on 11/11/2026 (the operator's own site NOT read): Southern Rivers Patagonia 660,
 *     Outfitters Patagonia 690. For Southern Rivers both prices are shown, so nobody reads a platform price as the site's.
 * Not published: Trout Bariloche's TripAdvisor "from USD 100" (the unit is not stated), reviews, ratings, review text.
 * Also: the Southern Rivers card gets the 11/11 price and languages; Outfitters Patagonia gets a public, unverified card.
 *
 * Idempotent: a package with the same title, a card already carrying the 11/11 price and an existing card are not touched.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = paquetes-bariloche-operadores-2026-11-11).
 */
$aplicar = true;
$destino = 'bariloche';
$hoy     = '20261111';
$ta_outfitters = 'https://www.tripadvisor.com/AttractionProductReview-g312848-d11455647-Fly_Fishing_Bariloche-San_Carlos_de_Bariloche_Province_of_Rio_Negro_Patagonia.html';

$T = [
	'es' => [
		'dia' => 'día completo de pesca con mosca en el Limay', 'mod' => 'Mosca, desde bote de deriva o vadeando', 'cap' => 'Hasta 2 pescadores por bote (salida privada)',
		'unidad' => 'por bote, 1 o 2 pescadores, día completo',
		'lic' => 'Licencia de pesca (para un extranjero, $ 50.000 por día, unos US$ 32 con US$ 1 = $ 1.545)', 'prop' => 'Propinas',
		'web' => 'Salida privada de día completo desde Bariloche, con los datos que el propio operador publica en su sitio (leído el 31 de octubre de 2026). Pescamapa no confirmó disponibilidad ni condiciones con el operador. Si sos el operador y algo cambió, escribinos y lo corregimos o lo sacamos.',
		'ta'  => 'Salida privada de día completo desde Bariloche, con los datos que el operador publica en TripAdvisor (11 de noviembre de 2026); su propio sitio no se leyó. Pescamapa no confirmó disponibilidad ni condiciones con el operador. Si sos el operador y algo cambió, escribinos y lo corregimos o lo sacamos.',
		'srp' => 'Salida privada de día completo desde Bariloche: US$ 660 en TripAdvisor (11 de noviembre de 2026) y US$ 530 en el sitio del operador (31 de octubre de 2026). Pescamapa no confirmó disponibilidad ni condiciones con el operador. Si sos el operador y algo cambió, escribinos y lo corregimos o lo sacamos.',
	],
	'en' => [
		'dia' => 'full-day fly fishing on the Limay', 'mod' => 'Fly fishing, from a drift boat or wading', 'cap' => 'Up to 2 anglers per boat (private trip)',
		'unidad' => 'per boat, 1 or 2 anglers, full day',
		'lic' => 'Fishing licence (for a foreigner, $ 50,000 per day, about US$ 32 at US$ 1 = $ 1,545)', 'prop' => 'Gratuities',
		'web' => 'Private full-day trip from Bariloche, using what the operator itself publishes on its website (read on 31 October 2026). Pescamapa has not confirmed availability or terms with the operator. If you are the operator and something has changed, write to us and we will correct it or take it down.',
		'ta'  => 'Private full-day trip from Bariloche, using what the operator publishes on TripAdvisor (11 November 2026); its own website was not read. Pescamapa has not confirmed availability or terms with the operator. If you are the operator and something has changed, write to us and we will correct it or take it down.',
		'srp' => 'Private full-day trip from Bariloche: US$ 660 on TripAdvisor (11 November 2026) and US$ 530 on the operator\'s website (31 October 2026). Pescamapa has not confirmed availability or terms with the operator. If you are the operator and something has changed, write to us and we will correct it or take it down.',
	],
	'pt' => [
		'dia' => 'dia inteiro de pesca com mosca no Limay', 'mod' => 'Mosca, de bote de deriva ou caminhando na água', 'cap' => 'Até 2 pescadores por bote (saída privada)',
		'unidad' => 'por bote, 1 ou 2 pescadores, dia inteiro',
		'lic' => 'Licença de pesca (para um estrangeiro, $ 50.000 por dia, cerca de US$ 32 com US$ 1 = $ 1.545)', 'prop' => 'Gorjetas',
		'web' => 'Saída privada de dia inteiro a partir de Bariloche, com os dados que o próprio operador publica em seu site (lido em 31 de outubro de 2026). A Pescamapa não confirmou disponibilidade nem condições com o operador. Se você é o operador e algo mudou, escreva para nós e corrigimos ou tiramos.',
		'ta'  => 'Saída privada de dia inteiro a partir de Bariloche, com os dados que o operador publica no TripAdvisor (11 de novembro de 2026); o site dele não foi lido. A Pescamapa não confirmou disponibilidade nem condições com o operador. Se você é o operador e algo mudou, escreva para nós e corrigimos ou tiramos.',
		'srp' => 'Saída privada de dia inteiro a partir de Bariloche: US$ 660 no TripAdvisor (11 de novembro de 2026) e US$ 530 no site do operador (31 de outubro de 2026). A Pescamapa não confirmou disponibilidade nem condições com o operador. Se você é o operador e algo mudou, escreva para nós e corrigimos ou tiramos.',
	],
];
// incl / noinc / var per operator and language. 'fuente' picks the summary text; monto/fecha are the published price.
$ops = [
	[ 'nombre' => 'Trout Bariloche', 'fuente' => 'web', 'monto' => 520, 'fecha' => '2026-10-31',
	  'incl' => [ 'es' => [ 'Traslados', 'Equipo de pesca', 'Comida' ], 'en' => [ 'Transfers', 'Fishing equipment', 'Lunch' ], 'pt' => [ 'Traslados', 'Equipamento de pesca', 'Refeição' ] ],
	  'var' => [ 'es' => [], 'en' => [], 'pt' => [] ], 'extra_no' => [ 'es' => [], 'en' => [], 'pt' => [] ] ],
	[ 'nombre' => 'Southern Rivers Patagonia', 'fuente' => 'srp', 'monto' => 660, 'fecha' => '2026-11-11',
	  'incl' => [ 'es' => [ 'Traslados', 'Equipo de pesca', 'Almuerzo', 'Bebidas (cerveza y vino Malbec)' ], 'en' => [ 'Transfers', 'Fishing equipment', 'Lunch', 'Drinks (beer and Malbec wine)' ], 'pt' => [ 'Traslados', 'Equipamento de pesca', 'Almoço', 'Bebidas (cerveja e vinho Malbec)' ] ],
	  'var' => [ 'es' => [ 'En su sitio: US$ 530 por 1 o 2 pescadores (31/10/2026)' ], 'en' => [ 'On its website: US$ 530 for 1 or 2 anglers (31/10/2026)' ], 'pt' => [ 'No site: US$ 530 para 1 ou 2 pescadores (31/10/2026)' ] ],
	  'extra_no' => [ 'es' => [ 'Si el operador gestiona la licencia: US$ 35 por persona (según TripAdvisor)' ], 'en' => [ 'If the operator arranges the licence: US$ 35 per person (according to TripAdvisor)' ], 'pt' => [ 'Se o operador providencia a licença: US$ 35 por pessoa (segundo o TripAdvisor)' ] ] ],
	[ 'nombre' => 'Patagonia in the Loop', 'fuente' => 'web', 'monto' => 550, 'fecha' => '2026-10-31',
	  'incl' => [ 'es' => [ 'Traslados', 'Equipo de pesca', 'Comida', 'Bebidas' ], 'en' => [ 'Transfers', 'Fishing equipment', 'Lunch', 'Drinks' ], 'pt' => [ 'Traslados', 'Equipamento de pesca', 'Refeição', 'Bebidas' ] ],
	  'var' => [ 'es' => [ 'Medio día: US$ 430 por 1 o 2 pescadores' ], 'en' => [ 'Half day: US$ 430 for 1 or 2 anglers' ], 'pt' => [ 'Meio dia: US$ 430 para 1 ou 2 pescadores' ] ], 'extra_no' => [ 'es' => [], 'en' => [], 'pt' => [] ] ],
	[ 'nombre' => 'Patagonia Drift Fly Fishing', 'fuente' => 'web', 'monto' => 550, 'fecha' => '2026-10-31',
	  'incl' => [ 'es' => [ 'Equipo de pesca', 'Comida', 'Bebidas' ], 'en' => [ 'Fishing equipment', 'Lunch', 'Drinks' ], 'pt' => [ 'Equipamento de pesca', 'Refeição', 'Bebidas' ] ],
	  'var' => [ 'es' => [ 'Lagos del Parque Nacional: US$ 580 por 1 o 2 pescadores' ], 'en' => [ 'National Park lakes: US$ 580 for 1 or 2 anglers' ], 'pt' => [ 'Lagos do Parque Nacional: US$ 580 para 1 ou 2 pescadores' ] ], 'extra_no' => [ 'es' => [], 'en' => [], 'pt' => [] ] ],
	[ 'nombre' => 'Outfitters Patagonia', 'fuente' => 'ta', 'monto' => 690, 'fecha' => '2026-11-11',
	  'incl' => [ 'es' => [ 'Equipo de pesca completo', 'Almuerzo', 'Bebidas ilimitadas (cerveza, vino y gaseosas)', 'Guía', 'Traslado desde el hotel' ], 'en' => [ 'All fishing gear', 'Lunch', 'Unlimited drinks (beer, wine and soft drinks)', 'Guide', 'Hotel pickup' ], 'pt' => [ 'Equipamento de pesca completo', 'Almoço', 'Bebidas ilimitadas (cerveja, vinho e refrigerantes)', 'Guia', 'Traslado desde o hotel' ] ],
	  'var' => [ 'es' => [ 'Río Limay, río Manso o río Pichi Leufú, según el día' ], 'en' => [ 'Limay River, Manso River or Pichi Leufú River, depending on the day' ], 'pt' => [ 'Rio Limay, rio Manso ou rio Pichi Leufú, conforme o dia' ] ], 'extra_no' => [ 'es' => [], 'en' => [], 'pt' => [] ] ],
];
$interno = 'No se publica. Listado con información pública (docs/paquetes-bariloche-fichas-2026-11-11.md). Sin acuerdo ni comisión: mensaje "confirmá o corregí tu ficha; si preferís, la sacamos". Comisión: la decide el dueño.';

$cambios = 0;
foreach ( $T as $i => $t ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$filas  = pm_filas( 'paquetes', $p->ID );
	$tit_ex = array_map( fn( $f ) => (string) ( $f['titulo'] ?? '' ), $filas );
	$nuevas = 0;
	foreach ( $ops as $o ) {
		$titulo = "{$o['nombre']}: {$t['dia']}";
		if ( in_array( $titulo, $tit_ex, true ) ) {
			continue;
		}
		$filas[] = [
			'publicado' => 1, 'titulo' => $titulo, 'resumen' => $t[ $o['fuente'] ], 'modalidad' => $t['mod'], 'duracion' => '', 'capacidad' => $t['cap'],
			'incluye' => implode( "\n", $o['incl'][ $i ] ), 'no_incluye' => implode( "\n", array_merge( [ $t['lic'], $t['prop'] ], $o['extra_no'][ $i ] ) ),
			'variantes' => implode( "\n", $o['var'][ $i ] ),
			'desde_monto' => $o['monto'], 'desde_moneda' => 'USD', 'desde_unidad' => $t['unidad'], 'desde_fecha' => $o['fecha'], 'interno' => $interno,
		];
		$nuevas++;
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $destino ($i) #{$p->ID}: $nuevas paquetes nuevos (hoy " . count( $tit_ex ) . ') → ' . pm_url_paquetes( $destino, $i ) );
	$cambios += $nuevas;
	if ( $aplicar && $nuevas ) {
		update_field( 'paquetes', $filas, $p->ID );
		$leidos = pm_paquetes( $p->ID );
		$ok     = count( $leidos ) >= $nuevas;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $destino ($i): " . count( $leidos ) . ' paquetes publicados' );
		$ok || WP_CLI::error( "$destino ($i): la lectura de vuelta no coincide." );
	}
}

// Provider cards: Southern Rivers gets the 11/11 price; Outfitters Patagonia gets a public, unverified card.
$srp = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => 'Southern Rivers Patagonia', 'numberposts' => 1 ] );
if ( $srp ) {
	$id = $srp[0]->ID;
	if ( '20261111' === str_replace( '-', '', (string) pm_campo( 'precio_fecha', $id ) ) ) {
		WP_CLI::log( 'Southern Rivers Patagonia: la ficha ya tiene el precio del 11/11, no toco nada.' );
	} else {
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha Southern Rivers Patagonia #$id: precio US$ 660 (TripAdvisor, 11/11/2026) y US$ 530 (su sitio, 31/10/2026); idiomas pt, en, es" );
		$cambios++;
		if ( $aplicar ) {
			update_field( 'precio', 'US$ 660 por bote, día completo en el Limay, 1 o 2 pescadores (TripAdvisor, 11/11/2026); US$ 530 en su sitio (31/10/2026). Licencia aparte: US$ 35 por persona si la gestiona el operador', $id );
			update_field( 'precio_en', 'USD 660 per boat, full day on the Limay, 1 or 2 anglers (TripAdvisor, 11/11/2026); USD 530 on its website (31/10/2026). Licence extra: USD 35 per person if the operator arranges it', $id );
			update_field( 'precio_fecha', $hoy, $id );
			update_field( 'prest_idiomas', [ 'pt', 'en', 'es' ], $id );
			WP_CLI::log( ( '20261111' === str_replace( '-', '', (string) pm_campo( 'precio_fecha', $id ) ) ? 'VERIFIED' : 'FALLÓ' ) . ': ficha Southern Rivers Patagonia' );
		}
	}
} else {
	WP_CLI::warning( 'No encontré la ficha de Southern Rivers Patagonia.' );
}
$of = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => 'Outfitters Patagonia', 'numberposts' => 1, 'fields' => 'ids' ] );
if ( $of ) {
	WP_CLI::log( "Outfitters Patagonia ya existe (#{$of[0]}): no se toca." );
} else {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': ficha nueva Outfitters Patagonia, listado con información pública sin verificar, en bariloche' );
	$cambios++;
	if ( $aplicar ) {
		$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => 'Outfitters Patagonia' ], true );
		if ( is_wp_error( $pid ) ) {
			WP_CLI::error( $pid->get_error_message() );
		}
		$f = [
			'tipo' => [ 'guia', 'operador' ], 'prest_destino' => 'bariloche', 'localidad' => 'Bariloche, Río Negro',
			'descripcion'    => 'Operador de Bariloche con salidas privadas de día completo en el Limay, el Manso y el Pichi Leufú (Parque Nacional Nahuel Huapi). Según su listado en TripAdvisor (11 de noviembre de 2026): US$ 690 por grupo de hasta 2, de 8 a 10 horas, con equipo, almuerzo, bebidas, guía y traslado desde el hotel; la licencia del Parque Nacional y las propinas no están incluidas. Su sitio propio no se leyó.',
			'descripcion_en' => 'Bariloche operator running private full-day trips on the Limay, the Manso and the Pichi Leufú (Nahuel Huapi National Park). According to its TripAdvisor listing (11 November 2026): USD 690 per group of up to 2, 8 to 10 hours, with gear, lunch, drinks, guide and hotel pickup; the National Park licence and gratuities are not included. Its own website was not read.',
			'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha' ], 'equipo' => [ 'lancha', 'equipos' ], 'capacidad' => 2,
			'lancha' => 'Bote de deriva', 'lancha_en' => 'Drift boat', 'motor' => '', 'prest_idiomas' => [ 'en', 'es' ],
			'incluye' => [ 'traslados', 'equipos', 'comida', 'bebidas' ], 'precio' => 'US$ 690 por grupo de hasta 2, día completo de 8 a 10 horas (TripAdvisor, 11/11/2026; licencia aparte)',
			'precio_en' => 'USD 690 per group of up to 2, full day of 8 to 10 hours (TripAdvisor, 11/11/2026; licence extra)', 'precio_fecha' => $hoy,
			'prest_publico' => 1, 'prest_fuente' => $ta_outfitters, 'estado' => 'relevado', 'verificado' => $hoy,
			'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'web' => $ta_outfitters,
			'notas' => 'Listado con información pública (listado de TripAdvisor pegado por el dueño el 11/11/2026). Pescamapa no confirmó contacto, disponibilidad ni habilitación. Mensaje: "confirmá o corregí tu ficha; si preferís, la sacamos". Comisión: la decide el dueño.',
		];
		foreach ( $f as $k => $v ) {
			update_field( $k, $v, $pid );
		}
		$r = null;
		foreach ( pm_prestadores_destino( 'bariloche', false ) as $x ) {
			if ( (int) $x['id'] === (int) $pid ) {
				$r = $x;
			}
		}
		$bien = $r && $r['precio'] && pm_prestador_publico( $r ) && ! $r['acuerdo'];
		WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": Outfitters Patagonia #$pid" );
		$bien || WP_CLI::error( 'la ficha de Outfitters Patagonia no quedó pública.' );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
