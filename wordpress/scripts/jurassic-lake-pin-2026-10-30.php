<?php
/**
 * Jurassic Lake guide: the map pin (lat -48.367, lon -71.2, from a Wikipedia mirror) falls about 11 km north of the lake on the map
 * (seen in the 30/10/2026 screenshot against the protected-landscape polygon). Moves it to the middle of the lake: lat -48.41, lon -71.2 (a first move to -48.47 left it on the south shore, seen in the second screenshot).
 * The pin marks a general area, not a fishing spot. Only the Spanish guide holds the coordinates. Idempotent. Dry run first.
 * Run with wp-script.yml (script = jurassic-lake-pin-2026-10-30).
 */

$aplicar = true;
$lat     = -48.41;
$lon     = -71.2;

$es = pm_buscar( 'pm_destino', 'jurassic-lake', 'es' );
if ( ! $es ) {
	WP_CLI::error( 'No encontré la guía jurassic-lake (es).' );
}
$antes = [ pm_campo( 'lat', $es->ID ), pm_campo( 'lon', $es->ID ) ];
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": jurassic-lake (es) #{$es->ID}: pin {$antes[0]}, {$antes[1]} -> $lat, $lon" );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
update_field( 'lat', $lat, $es->ID );
update_field( 'lon', $lon, $es->ID );
$ok = (float) pm_campo( 'lat', $es->ID ) === $lat && (float) pm_campo( 'lon', $es->ID ) === $lon;
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": pin jurassic-lake " . pm_campo( 'lat', $es->ID ) . ', ' . pm_campo( 'lon', $es->ID ) );
$ok || WP_CLI::error( 'el pin no quedó.' );
WP_CLI::log( 'APLICADO' );
