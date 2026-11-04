<?php
/**
 * 4/11/2026: Limay River Lodge was listed under the Bariloche guide, but the lodge is on the middle Limay (Neuquén), 3-4 hours
 * by road from the Neuquén, San Martín and Bariloche airports (Orvis listing; relevamiento del 4/11/2026), not near Bariloche.
 * It is hidden from the public lists until a guide for the middle Limay exists; nothing is deleted. Idempotent.
 * Dry run by default; run with wp-script.yml (script = limay-river-lodge-2026-11-04).
 */

$aplicar = true;
$nota    = "\n4/11/2026: oculta de la guía de Bariloche. El lodge está en el Limay Medio (Neuquén), a 3-4 h de los aeropuertos de Neuquén, San Martín y Bariloche (Orvis; relevamiento del 4/11/2026). Vuelve a publicarse con una guía del Limay Medio.";

$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => 'Limay River Lodge', 'numberposts' => 1, 'fields' => 'ids' ] );
if ( ! $ids ) {
	WP_CLI::error( 'No encontré la ficha Limay River Lodge.' );
}
$id = $ids[0];
$r  = pm_prestador( $id );
WP_CLI::log( "Limay River Lodge #$id destino=" . pm_campo( 'prest_destino', $id ) . ' público=' . ( pm_campo( 'prest_publico', $id ) ? 'sí' : 'no' ) . ' en la lista pública de bariloche=' . ( in_array( $id, array_column( pm_prestadores_destino( 'bariloche' ), 'id' ), true ) ? 'sí' : 'no' ) );
if ( ! pm_campo( 'prest_publico', $id ) && ! pm_campo( 'prest_consentimiento', $id ) ) {
	WP_CLI::log( '· ya está oculta: no se toca' );
	return;
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': prest_publico = 0 y nota interna' );
if ( ! $aplicar ) {
	return;
}
update_field( 'prest_publico', 0, $id );
update_field( 'notas', (string) pm_campo( 'notas', $id ) . $nota, $id );
$fuera = ! in_array( $id, array_column( pm_prestadores_destino( 'bariloche' ), 'id' ), true );
WP_CLI::log( ( $fuera ? 'VERIFIED' : 'FALLÓ' ) . ": Limay River Lodge #$id ya no figura en la lista pública de Bariloche" );
$fuera || WP_CLI::error( 'sigue en la lista pública.' );
