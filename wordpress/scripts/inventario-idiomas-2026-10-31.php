<?php
/**
 * Read-only, 31/10/2026: which languages each piece of content exists in, and which outfitter cards lack a translated description.
 * Owner's rule (31/10/2026): what exists in one language and is missing in another gets completed. This lists the gaps so the work
 * is planned from facts, not memory. Writes nothing. Run with wp-script.yml (script = inventario-idiomas-2026-10-31).
 */

$idiomas = [ 'es', 'pt', 'en' ];
foreach ( [ 'pm_destino' => 'Guías', 'pm_nota' => 'Notas', 'pm_especie' => 'Especies' ] as $tipo => $rotulo ) {
	$por = [];
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'publish', 'numberposts' => -1 ] ) as $p ) {
		$por[ pm_clave_de( $p->ID ) ][ pm_idioma_de( $p->ID ) ] = $p->ID;
	}
	ksort( $por );
	WP_CLI::log( "== $rotulo (" . count( $por ) . ' claves)' );
	foreach ( $por as $clave => $l ) {
		$faltan = array_diff( $idiomas, array_keys( $l ) );
		WP_CLI::log( sprintf( '  %-32s %s%s', $clave, implode( ' ', array_map( fn( $i ) => isset( $l[ $i ] ) ? $i : '--', $idiomas ) ), $faltan ? '   FALTA: ' . implode( ',', $faltan ) : '' ) );
	}
}

WP_CLI::log( '== Paquetes (por guía en español: idiomas con paquetes publicados)' );
foreach ( get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'numberposts' => -1 ] ) as $p ) {
	if ( 'es' !== pm_idioma_de( $p->ID ) ) {
		continue;
	}
	$c   = pm_clave_de( $p->ID );
	$con = array_filter( $idiomas, fn( $i ) => pm_destino_paquetes( $c, $i ) );
	if ( $con ) {
		WP_CLI::log( sprintf( '  %-32s %s%s', $c, implode( ' ', $con ), count( $con ) < 3 ? '   FALTA: ' . implode( ',', array_diff( $idiomas, $con ) ) : '' ) );
	}
}

WP_CLI::log( '== Fichas de prestador públicas sin descripción traducida (cae al español)' );
foreach ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'numberposts' => -1 ] ) as $p ) {
	$r = pm_prestador( $p->ID );
	if ( ! pm_prestador_publico( $r ) ) {
		continue;
	}
	$faltan = array_filter( [ 'pt', 'en' ], fn( $i ) => '' === trim( (string) pm_campo( "descripcion_$i", $p->ID ) ) );
	$base   = (string) pm_campo( 'descripcion', $p->ID );
	$ingles = preg_match( '/\b(the|with|and|its|according)\b/', $base ) ? ' (BASE EN INGLÉS)' : ''; // English function words; "Lodge cerca de…" is Spanish
	if ( $faltan || $ingles ) {
		WP_CLI::log( sprintf( '  #%-4d %-32s destino=%-20s falta: %s%s', $p->ID, $p->post_title, $r['destino'] ?: '-', implode( ',', $faltan ) ?: '-', $ingles ) );
	}
}
WP_CLI::log( 'FIN (solo lectura)' );
