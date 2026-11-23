<?php
/**
 * READ-ONLY: prints every paragraph / FAQ / field of the Río Gallegos guide (es, en, pt) that mentions the lodges, rates or access,
 * with the field name and an index. Never writes. Email- and phone-looking strings are stripped before printing.
 * Run with wp-script.yml (script = rio-gallegos-leer-2026-11-23), aplicar=false.
 */

$aplicar = false;

function pm_rgl_limpio( string $s ): string {
	$s = preg_replace( '/[\w.+-]+@[\w-]+\.[\w.-]+/u', '[email]', $s );
	return preg_replace( '/\+?\d[\d\s().-]{7,}\d/u', '[tel]', $s );
}
function pm_rgl_hit( string $s ): bool {
	return (bool) preg_match( '/Karku|Buitreras|River House|Glencross|Rinc[oó]n|lodge|tarifa|llego|get there|getting there|chego|rate|price|precio|operador|operator|operadores|outfitter|habilitad|contact/iu', $s );
}
function pm_rgl_recorrer( $v, string $ruta ): void {
	if ( is_array( $v ) ) {
		foreach ( $v as $k => $x ) {
			pm_rgl_recorrer( $x, $ruta . '.' . $k );
		}
		return;
	}
	if ( ! is_string( $v ) || '' === $v ) {
		return;
	}
	if ( pm_rgl_hit( $v ) ) {
		WP_CLI::log( "  [$ruta] " . pm_rgl_limpio( $v ) );
	}
}

foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = pm_buscar( 'pm_destino', 'rio-gallegos', $i );
	if ( ! $p ) {
		WP_CLI::log( "($i) no existe" );
		continue;
	}
	WP_CLI::log( "=== ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' actualizado=' . pm_campo( 'actualizado', $p->ID ) );
	$partes = preg_split( "/\n{2,}/", $p->post_content );
	foreach ( $partes as $n => $t ) {
		if ( pm_rgl_hit( $t ) ) {
			WP_CLI::log( "  [cuerpo #$n] " . pm_rgl_limpio( $t ) );
		}
	}
	$f = function_exists( 'get_fields' ) ? get_fields( $p->ID ) : [];
	foreach ( (array) $f as $k => $v ) {
		pm_rgl_recorrer( $v, $k );
	}
}
WP_CLI::log( 'SOLO LECTURA: no se escribió nada' );
