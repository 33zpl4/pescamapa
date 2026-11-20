<?php
/**
 * Fondo del embudo, Itatí e Itá Ibaté (20/11/2026). Fichas de «información pública» para los lugares y guías que figuran en las páginas
 * «Experiencia de Pesca – Itatí» y «– Ita Ibate» de Turismo de Corrientes (nombres leídos con listado-corrientes-nombres-2026-11-20; ninguna
 * de nuestras fichas de esas zonas figura en ellas). Criterio igual que en Esquina (19/11): se cargan posadas, bases y guías de pesca; NO los
 * hoteles, aparts ni estancias, porque la página no dice que ofrezcan pesca (quedan fuera Hotel del Río y Estancia Don Cindo). Posada Puerto Paraíso
 * figura en Itatí, Itá Ibaté, Yahapé y Esquina: ya tiene ficha (Esquina, #858) y no se duplica. La Regina, Gemeos y Don Vidal además se venden como
 * posadas de pesca en el catálogo de BCA Fishing (agencia brasileña; leído por el dueño el 19/11/2026).
 * Texto público: solo que figura en el listado, con su localidad. Sin contacto, sin foto, sin especies, sin tarifa. Marca de listado con la URL de su
 * página. El script lee cada página y crea la ficha solo si el nombre está en ella al correr; no imprime texto de las páginas. Idempotente.
 * Simulacro primero ($aplicar = false). Correr con wp-script.yml (script = listado-corrientes-itati-itaibate-2026-11-20; aplicar=true para el real).
 */

$aplicar = false;
$hoy     = '20261120';
$fuente  = 'Turismo de Corrientes';
$paginas = [
	'itati'     => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-itati/',
	'ita-ibate' => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-ita-ibate/',
];
$lugares = [ 'itati' => [ 'Itatí', 'Itatí', 'Itatí' ], 'ita-ibate' => [ 'Itá Ibaté', 'Itá Ibaté', 'Itá Ibaté' ] ];
$nuevas  = [
	[ 'itati', 'La Regina', 'la regina', 'lugar' ],
	[ 'itati', 'Paraná on the Fly', 'parana on the fly', 'guia' ],
	[ 'itati', 'Puerto Yacarey', 'puerto yacarey', 'lugar' ],
	[ 'ita-ibate', 'Gemeos', 'gemeos', 'lugar' ],
	[ 'ita-ibate', 'Don Vidal', 'don vidal', 'lugar' ],
	[ 'ita-ibate', 'Piedra Alta', 'piedra alta', 'lugar' ],
	[ 'ita-ibate', 'Ramón Rivero (Power Pesca)', 'ramon rivero', 'guia' ],
];

function pm_li_norm( string $t ): string {
	$t = mb_strtolower( remove_accents( html_entity_decode( $t, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
	return trim( preg_replace( '/\s+/u', ' ', preg_replace( '/[^a-z0-9]+/u', ' ', $t ) ) );
}
function pm_li_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}

$textos = [];
foreach ( $paginas as $d => $url ) {
	$r = wp_remote_get( $url, [ 'timeout' => 20, 'redirection' => 5, 'limit_response_size' => 2000000, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)' ] );
	is_wp_error( $r ) && WP_CLI::error( "no pude leer $url: " . $r->get_error_message() );
	$st   = (int) wp_remote_retrieve_response_code( $r );
	$html = preg_replace( '#<(script|style|noscript|svg|template)\b.*?</\1\s*>#is', ' ', (string) wp_remote_retrieve_body( $r ) );
	$textos[ $d ] = pm_li_norm( preg_replace( '#<[^>]*>#', ' ', $html ) );
	WP_CLI::log( "página oficial $d: status $st, " . mb_strlen( $textos[ $d ] ) . ' caracteres (no se imprime)' );
	( 200 === $st && mb_strlen( $textos[ $d ] ) >= 300 ) || WP_CLI::error( "la página oficial de $d no devolvió una lectura útil" );
}

$prohibido = '/@|https?:\/\/|instagram|facebook|whatsapp|tel[eé]fono|\+\d{2}|\d{6,}|\boperador|\bmejor(es)?\b|r[eé]cord|habilitad|registro/iu';
$plan = [];
foreach ( $nuevas as [ $d, $nombre, $alias, $tipo ] ) {
	[ $es, $pt, $en ] = $lugares[ $d ];
	$campos = [
		'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado',
		'prest_destino' => $d, 'localidad' => "$es (Corrientes)",
		'precio' => '', 'precio_fecha' => '', 'prest_fuente' => $paginas[ $d ], 'verificado' => $hoy,
		'notas' => "Listado con información pública: figura en la página «Experiencia de Pesca» de $es de Turismo de Corrientes (leída el 20/11/2026; contacto fuera del repo). Pendiente: escribirle.",
	];
	if ( 'guia' === $tipo ) {
		$campos += [
			'tipo'           => [ 'guia' ],
			'descripcion'    => "Guía de pesca de $es. Figura en el listado de pesca de Turismo de Corrientes. Salidas, qué incluyen y a cuánto: a consultar.",
			'descripcion_pt' => "Guia de pesca de $pt. Consta da lista de pesca de Turismo de Corrientes. Saídas, o que incluem e quanto custam: a consultar.",
			'descripcion_en' => "Fishing guide in $en. Appears in the Turismo de Corrientes fishing listing. Trips, what they include and the price: to be asked.",
		];
	} else {
		$campos += [
			'descripcion'    => "Figura en el listado de pesca de Turismo de Corrientes para $es. Qué ofrece, qué incluye y a cuánto: a consultar.",
			'descripcion_pt' => "Consta da lista de pesca de Turismo de Corrientes para $pt. O que oferece, o que inclui e quanto custa: a consultar.",
			'descripcion_en' => "Appears in the Turismo de Corrientes fishing listing for $en. What it offers, what is included and the price: to be asked.",
		];
	}
	foreach ( [ 'descripcion', 'descripcion_pt', 'descripcion_en', 'localidad' ] as $k ) {
		preg_match( $prohibido, $campos[ $k ], $m ) && WP_CLI::error( "texto público de «$nombre» ($k) lleva algo que no se publica: «{$m[0]}»" );
	}
	$esta = false !== strpos( ' ' . $textos[ $d ] . ' ', ' ' . pm_li_norm( $alias ) . ' ' );
	$pid  = pm_li_id( $nombre );
	WP_CLI::log( ( $pid ? "EXISTE #$pid" : 'NUEVA' ) . " «{$nombre}» ($d): " . ( $esta ? 'figura en el listado' : 'NO se ve en el listado (no se crea)' ) );
	$esta && $plan[] = [ $nombre, $d, $pid, $campos ];
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': ' . count( $plan ) . ' fichas' );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

$ids = [];
foreach ( $plan as [ $nombre, $d, $pid, $campos ] ) {
	if ( ! $pid ) {
		$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
		is_wp_error( $pid ) && WP_CLI::error( $pid->get_error_message() );
		foreach ( $campos as $k => $v ) {
			'' !== $v && update_field( $k, $v, $pid );
		}
		WP_CLI::log( "✓ ficha «{$nombre}» creada #$pid" );
	} else {
		WP_CLI::log( "= ficha «{$nombre}» #$pid ya existía: no se toca el contenido" );
	}
	if ( ! pm_prestador_listado_datos( $pid ) ) {
		foreach ( [ 'prest_listado_fuente' => $fuente, 'prest_listado_fecha' => $hoy, 'prest_listado_url' => $paginas[ $d ] ] as $k => $v ) {
			update_field( $k, $v, $pid );
		}
	}
	$ids[] = [ $nombre, $d, (int) $pid ];
}
wp_cache_flush();

$bien = true;
foreach ( $ids as [ $nombre, $d, $pid ] ) {
	$l     = pm_prestador_listado_datos( $pid );
	$f     = pm_prestador( $pid );
	$lista = array_map( fn( $r ) => (int) $r['id'], pm_prestadores_destino( $d ) );
	$pub   = wp_json_encode( [ $f['descripcion'], $f['localidad'], $f['precio'] ], JSON_UNESCAPED_UNICODE );
	$ok    = $l && $paginas[ $d ] === $l['url'] && in_array( $pid, $lista, true ) && ! preg_match( $prohibido, $pub ) && '' === $f['precio'] && ! $f['consentimiento'] && ! $f['fotos'];
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» #$pid sale en $d como información pública con marca de listado, sin contactos ni precio" );
	$bien = $bien && $ok;
}
WP_CLI::log( $bien ? 'VERIFIED: todo leído de vuelta' : 'FALLÓ: hay campos que no coinciden (ver arriba)' );
