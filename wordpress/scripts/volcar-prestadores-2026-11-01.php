<?php
/**
 * Read-only: every outfitter card, grouped by destination, with what the public page would show (type, species, modalities,
 * includes, dated price, source, verification level). Used on 1/11/2026 before adding the Litoral day guides, so a card is
 * never created twice. Prints no contact data. Run with wp-script.yml (script = volcar-prestadores-2026-11-01).
 */

$por_destino = [];
foreach ( pm_posts( 'pm_prestador' ) as $p ) {
	$r = pm_prestador( $p->ID );
	$por_destino[ $r['destino'] ?: '(sin destino)' ][] = $r;
}
ksort( $por_destino );
foreach ( $por_destino as $destino => $lista ) {
	WP_CLI::log( "== $destino (" . count( $lista ) . ')' );
	foreach ( $lista as $r ) {
		$nivel = $r['ejemplo'] ? 'EJEMPLO' : ( ( $r['acuerdo'] && $r['consentimiento'] ) ? 'acuerdo' : ( $r['consentimiento'] ? 'confirmada' : ( $r['publico'] ? 'publica' : 'NO PUBLICA' ) ) );
		WP_CLI::log( sprintf( '  #%d %s | %s | %s | %s | precio: %s (%s) | fuente: %s | %s', $r['id'], $r['nombre'], implode( ',', $r['tipo'] ), implode( ',', $r['modalidades'] ), implode( ',', $r['incluye'] ), $r['precio'] ?: '-', $r['precio_fecha'] ?: '-', $r['fuente'] ?: '-', $nivel ) );
	}
}
WP_CLI::log( 'FIN (solo lectura)' );
