<?php
/**
 * 21/10/2026: "flycast" is the local name for fly fishing, so the modality
 * reads "Mosca (flycast)" (es), "Fly (flycast)" (pt), "Fly fishing (flycast)"
 * (en). Edits only a line of the guide's `modalidades` that is exactly the
 * old wording, in every guide and language; anything an editor wrote
 * differently is left alone. Idempotent.
 * Run with wp-script.yml (script = flycast-2026-10-21).
 */

$viejo = [ 'es' => 'Mosca', 'pt' => 'Fly', 'en' => 'Fly fishing' ];

foreach ( pm_posts( 'pm_destino' ) as $p ) {
	$i      = pm_idioma_de( $p->ID );
	$lineas = preg_split( '/\R/', (string) get_post_meta( $p->ID, 'modalidades', true ) );
	$hecho  = false;
	foreach ( $lineas as &$l ) {
		if ( trim( $l ) === $viejo[ $i ] ) {
			$l     = $viejo[ $i ] . ' (flycast)';
			$hecho = true;
		}
	}
	unset( $l );
	if ( $hecho ) {
		update_field( 'modalidades', implode( "\n", $lineas ), $p->ID );
		update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
		WP_CLI::log( "✓ {$p->post_name} ($i): modalidad con flycast" );
	} else {
		WP_CLI::log( "· {$p->post_name} ($i): sin cambios" );
	}
}
