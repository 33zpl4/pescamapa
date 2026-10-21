<?php
/**
 * Read-only diagnosis (21/10/2026): the "Fuentes" list of the Goya guide
 * renders empty rows. Prints, for each Goya version, how the repeater is
 * stored (raw meta) and what get_field() returns. Changes nothing.
 * Run with wp-script.yml (script = diagnostico-fuentes).
 */

foreach ( [ 'es', 'pt', 'en' ] as $i ) {
	$p = pm_buscar( 'pm_destino', 'goya', $i );
	if ( ! $p ) {
		WP_CLI::log( "goya ($i): no existe" );
		continue;
	}
	$id = $p->ID;
	WP_CLI::log( "== goya ($i) #$id" );
	WP_CLI::log( 'meta fuentes (cantidad): ' . var_export( get_post_meta( $id, 'fuentes', true ), true ) );
	WP_CLI::log( 'meta _fuentes (ref): ' . var_export( get_post_meta( $id, '_fuentes', true ), true ) );
	WP_CLI::log( 'meta fuentes_0_nombre: ' . var_export( get_post_meta( $id, 'fuentes_0_nombre', true ), true ) );
	WP_CLI::log( 'meta _fuentes_0_nombre (ref): ' . var_export( get_post_meta( $id, '_fuentes_0_nombre', true ), true ) );
	WP_CLI::log( 'meta fuentes_0_url: ' . var_export( get_post_meta( $id, 'fuentes_0_url', true ), true ) );
	$f = pm_filas( 'fuentes', $id );
	WP_CLI::log( 'get_field filas: ' . count( $f ) . ' · primera: ' . wp_json_encode( $f[0] ?? null, JSON_UNESCAPED_UNICODE ) );
	WP_CLI::log( 'meta mas (cantidad): ' . var_export( get_post_meta( $id, 'mas', true ), true ) . ' · mas_0_nombre: ' . var_export( get_post_meta( $id, 'mas_0_nombre', true ), true ) );
	$campo = function_exists( 'acf_maybe_get_field' ) ? acf_maybe_get_field( 'fuentes', $id ) : null;
	WP_CLI::log( 'campo resuelto por nombre: ' . ( $campo['key'] ?? '?' ) . ' · subcampos: ' . implode( ',', array_map( fn( $s ) => $s['name'], $campo['sub_fields'] ?? [] ) ) );
}
