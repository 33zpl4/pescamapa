<?php
/**
 * Solo lectura (20/11/2026): nombres de los lugares y guías que figuran en las páginas «Experiencia de pesca» de Turismo de Corrientes para
 * Itatí, Itá Ibaté, Ituzaingó y Yahapé (descubiertas con listado-corrientes-descubrir-2026-11-20), y cruce con nuestras fichas públicas.
 * De cada renglón de la lista se imprime SOLO el nombre: se corta antes del primer dígito, «+», «@», «http», «www», «tel» o guion separador, y
 * un renglón que aun así tenga dígitos o «@» no se imprime. Nunca se imprimen teléfonos, correos ni enlaces. No escribe nada.
 * Correr con wp-script.yml (script = listado-corrientes-nombres-2026-11-20).
 */

$aplicar = false;
$paginas = [
	'itati'     => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-itati/',
	'ita-ibate' => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-ita-ibate/',
	'ituzaingo' => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-ituzaingo/',
	'yahape'    => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-yahape/',
];
$ua = [ 'timeout' => 20, 'redirection' => 5, 'limit_response_size' => 2000000, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)' ];

function pm_ln_nombre( string $li ): string {
	$t = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $li ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
	$t = preg_split( '/(\d|\+|@|https?:|www\.|\btel\b|\btel[eé]fono|\bcel\b|\s[–—-]\s|\s[–—-]$|:)/iu', $t )[0] ?? '';
	$t = trim( $t, " \t\n\r\0\x0B-–—,.;()" );
	return ( '' === $t || preg_match( '/\d|@/', $t ) || mb_strlen( $t ) > 80 ) ? '' : $t;
}

$textos = [];
foreach ( $paginas as $clave => $url ) {
	$r  = wp_remote_get( $url, $ua );
	$st = is_wp_error( $r ) ? 'error' : (int) wp_remote_retrieve_response_code( $r );
	WP_CLI::log( "== $clave · $st · $url" );
	if ( 200 !== $st ) {
		continue;
	}
	$html = (string) wp_remote_retrieve_body( $r );
	// The listing sits in the tour description; take the content area, then every <li> in it.
	$zona = $html;
	if ( preg_match( '#(Operador|Prestador|Guías|Guias|Alojamiento).*#is', $html, $m ) ) {
		$zona = $m[0];
	}
	$zona = preg_split( '#Informaci[oó]n r[aá]pida|Imperdibles|También puede interesarte#iu', $zona )[0] ?? $zona;
	preg_match_all( '#<li[^>]*>(.*?)</li>#is', $zona, $lis );
	$n = 0;
	foreach ( $lis[1] as $li ) {
		$nom = pm_ln_nombre( $li );
		if ( '' !== $nom && ! preg_match( '/^(lanchas|gu[ií]as profesionales|equipos|servicios|inicio|destinos|planifica|agenda|detalles|institucional|tr[aá]mites|agencias|informaci[oó]n|mapa)/iu', $nom ) ) {
			WP_CLI::log( "  · $nom" );
			$n++;
		}
	}
	WP_CLI::log( "  ($n nombres)" );
	$t = mb_strtolower( remove_accents( wp_strip_all_tags( $html ) ) );
	$textos[ $clave ] = $t;
	foreach ( pm_prestadores_destino( $clave ) as $p ) {
		$k = mb_strtolower( remove_accents( preg_split( '/\s[—–-]\s|\s\(/u', (string) $p['nombre'] )[0] ) );
		WP_CLI::log( '  ficha nuestra «' . $p['nombre'] . '» #' . $p['id'] . ': ' . ( false !== strpos( $t, $k ) ? 'FIGURA' : 'no figura con ese nombre' ) . ( $p['listado'] ? ' (ya tiene marca)' : '' ) );
	}
	usleep( 300000 );
}
WP_CLI::log( '== esteros-del-ibera (sin página propia): cruce con las cuatro páginas' );
foreach ( pm_prestadores_destino( 'esteros-del-ibera' ) as $p ) {
	$k  = mb_strtolower( remove_accents( preg_split( '/\s[—–-]\s|\s\(/u', (string) $p['nombre'] )[0] ) );
	$en = array_keys( array_filter( $textos, fn( $t ) => false !== strpos( $t, $k ) ) );
	WP_CLI::log( '  ficha nuestra «' . $p['nombre'] . '» #' . $p['id'] . ': ' . ( $en ? 'FIGURA en ' . implode( ', ', $en ) : 'no figura con ese nombre' ) );
}
WP_CLI::log( 'VERIFIED: lectura terminada (no se escribió nada)' );
