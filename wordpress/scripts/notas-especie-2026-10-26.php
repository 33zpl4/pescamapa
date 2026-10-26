<?php
/**
 * Marks the species of the three "when to fish" notes (field "Especie" of the note), so each note shows in
 * the guides of the destinos that have that species and on the species page. Spanish note only (the field
 * holds for the three languages). Idempotent; dry run first ($aplicar = false); run with wp-script.yml
 * (script = notas-especie-2026-10-26).
 */

$aplicar = false;

$mapa = [ 'cuando-pescar-dorado' => 'dorado', 'cuando-pescar-surubi' => 'surubi', 'cuando-pescar-trucha-patagonia' => 'trucha' ];

foreach ( $mapa as $clave => $especie ) {
	$es = null;
	foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		if ( 'es' === pm_idioma_de( $p->ID ) ) {
			$es = $p;
		}
	}
	if ( ! $es ) {
		WP_CLI::error( "$clave: no existe la nota en español." );
	}
	if ( ! pm_buscar( 'pm_especie', $especie, 'es' ) ) {
		WP_CLI::error( "$clave: no existe la especie «{$especie}»." );
	}
	$antes = (string) pm_campo( 'nota_especie', $es->ID );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave #{$es->ID} especie «{$antes}» → «{$especie}»" );
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'nota_especie', $especie, $es->ID );
	$ok = pm_campo( 'nota_especie', $es->ID ) === $especie;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave #{$es->ID} especie = " . pm_campo( 'nota_especie', $es->ID ) );
	$ok || WP_CLI::error( "$clave: la especie no quedó." );
}
WP_CLI::success( $aplicar ? 'Especies de las notas marcadas.' : 'Simulacro terminado.' );
