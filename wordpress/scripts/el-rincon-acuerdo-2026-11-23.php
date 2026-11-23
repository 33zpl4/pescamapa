<?php
/**
 * El Rincón Lodge: acuerdo con Pescamapa (23/11/2026). El dueño confirmó que el acuerdo de comisión con el lodge está establecido (por correo).
 * Pasa la ficha a estado «acuerdo» y anota la comisión (campo interno). Efecto público de acuerdo + consentimiento: la marca «Con acuerdo con
 * Pescamapa» y el lugar en el grupo de arriba de la lista (docs/prestadores.md). Las tarifas del lodge NO se escriben en ningún lado (pidió que no
 * se muestren; se cotizan a quien pide opciones). Sin datos de contacto. Idempotente. Simulacro primero.
 * Correr con wp-script.yml (script = el-rincon-acuerdo-2026-11-23; aplicar=true para el real).
 */

$aplicar = false;
$nombre  = 'El Rincón Lodge';
$campos  = [
	'estado'   => 'acuerdo',
	'comision' => '15 % sobre reservas de pescadores que llegan por Pescamapa (acordado por correo; confirmado por el dueño el 23/11/2026)',
];

$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
1 === count( $ids ) || WP_CLI::error( 'Se esperaba una sola ficha «' . $nombre . '», hay ' . count( $ids ) );
$pid = (int) $ids[0];
(int) pm_campo( 'prest_consentimiento', $pid ) || WP_CLI::error( 'La ficha no tiene consentimiento: no se marca el acuerdo.' );
'' === trim( (string) pm_campo( 'precio', $pid ) ) || WP_CLI::error( 'La ficha tiene un precio cargado: el lodge pidió no mostrarlo.' );

$cambios = [];
foreach ( $campos as $k => $v ) {
	$antes = (string) pm_campo( $k, $pid );
	WP_CLI::log( ( $antes === $v ? '  = ' : '  ~ ' ) . $k . ( $antes === $v ? ' sin cambios' : ": «{$antes}» -> «{$v}»" ) );
	$antes === $v || $cambios[ $k ] = $v;
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: ' . count( $cambios ) . ' campos cambiarían; no se escribió nada' );
	return;
}
foreach ( $cambios as $k => $v ) {
	update_field( $k, $v, $pid );
}
wp_cache_flush();

$bien = true;
foreach ( $campos as $k => $v ) {
	$ok = (string) pm_campo( $k, $pid ) === $v;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $k" . ( 'estado' === $k ? " = $v" : '' ) );
	$bien = $bien && $ok;
}
$orden = [];
$r     = null;
foreach ( pm_prestadores_destino( 'rio-gallegos' ) as $x ) {
	$orden[] = $x['nombre'] . ( $x['acuerdo'] && $x['consentimiento'] ? ' [acuerdo]' : '' );
	(int) $x['id'] === $pid && $r = $x;
}
$ok = $r && $r['acuerdo'] && $r['consentimiento'] && '' === $r['precio'];
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': la ficha sale con acuerdo y sin precio. Orden: ' . implode( ' > ', $orden ) );
$bien && $ok ? WP_CLI::success( 'APLICADO' ) : WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
