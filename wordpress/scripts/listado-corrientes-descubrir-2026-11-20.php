<?php
/**
 * Descubrir (solo lectura, 20/11/2026): qué páginas «Experiencia de pesca» / excursiones de pesca publica Turismo de Corrientes (corrientes.tur.ar)
 * para Itatí, Esteros del Iberá (Colonia Carlos Pellegrini, Ituzaingó, Concepción…), Itá Ibaté, Yahapé y el resto de la provincia.
 * Lee los sitemaps XML del sitio y prueba direcciones candidatas. Imprime SOLO direcciones (URLs), su estado HTTP y si el texto menciona «pesca»;
 * nunca texto de las páginas (llevan teléfonos y correos). No escribe nada en la base: $aplicar no se usa.
 * Correr con wp-script.yml (script = listado-corrientes-descubrir-2026-11-20).
 */

$aplicar = false;
$base    = 'https://corrientes.tur.ar';
$ua      = [ 'timeout' => 20, 'redirection' => 5, 'limit_response_size' => 4000000, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)' ];
$patron  = '#pesca|itati|ibera|ibate|ituzaingo|yahape|pellegrini|concepcion|san-cosme|empedrado|bella-vista|santa-lucia|lavalle|paso-de-la-patria|esquina|goya#i';

$visto = [];
$cola  = [ "$base/wp-sitemap.xml", "$base/sitemap_index.xml", "$base/sitemap.xml" ];
$urls  = [];
while ( $cola && count( $visto ) < 40 ) {
	$s = array_shift( $cola );
	if ( isset( $visto[ $s ] ) ) {
		continue;
	}
	$visto[ $s ] = 1;
	$r  = wp_remote_get( $s, $ua );
	$st = is_wp_error( $r ) ? 'error' : (int) wp_remote_retrieve_response_code( $r );
	WP_CLI::log( "sitemap $s: $st" );
	if ( 200 !== $st ) {
		continue;
	}
	preg_match_all( '#<loc>\s*([^<\s]+)\s*</loc>#i', (string) wp_remote_retrieve_body( $r ), $m );
	foreach ( $m[1] as $u ) {
		$u = html_entity_decode( $u );
		if ( preg_match( '#\.xml(\?|$)#i', $u ) ) {
			$cola[] = $u;
		} elseif ( preg_match( $patron, $u ) ) {
			$urls[ $u ] = 1;
		}
	}
}
foreach ( [ 'experiencia-de-pesca-itati', 'experiencia-de-pesca-ita-ibate', 'experiencia-de-pesca-ituzaingo', 'experiencia-de-pesca-ibera', 'experiencia-de-pesca-yahape', 'experiencia-de-pesca-colonia-carlos-pellegrini', 'experiencia-de-pesca-san-cosme', 'experiencia-de-pesca-empedrado', 'experiencia-de-pesca-bella-vista' ] as $slug ) {
	$urls[ "$base/tour/$slug/" ] = 1;
}
WP_CLI::log( count( $urls ) . ' direcciones candidatas' );
foreach ( array_keys( $urls ) as $u ) {
	$r  = wp_remote_get( $u, $ua );
	$st = is_wp_error( $r ) ? 'error' : (int) wp_remote_retrieve_response_code( $r );
	$t  = 200 === $st ? mb_strtolower( remove_accents( wp_strip_all_tags( (string) wp_remote_retrieve_body( $r ) ) ) ) : '';
	$h  = '';
	if ( 200 === $st && preg_match( '#<title>([^<]{0,120})</title>#i', (string) wp_remote_retrieve_body( $r ), $tm ) ) {
		$h = trim( html_entity_decode( $tm[1] ) );
	}
	WP_CLI::log( sprintf( '%s · %s · pesca=%s · operadores=%s · «%s»', $st, $u, false !== strpos( $t, 'pesca' ) ? 'sí' : 'no', false !== strpos( $t, 'operadores turisticos' ) ? 'sí' : 'no', $h ) );
	usleep( 300000 );
}
WP_CLI::log( 'VERIFIED: lectura terminada (no se escribió nada)' );
