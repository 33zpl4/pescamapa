<?php
/**
 * Experiencia Isoró (#48): restore "Incluye" and "Equipo" exactly as Cheyenne entered them on 20/10/2026.
 * Owner (24/10/2026): she knows the operator personally, the data is correct and stays. Also notes it
 * on the record. Idempotent. Dry run first ($aplicar = false), then apply with wp-script.yml.
 */

$aplicar = false;
$incluye = [ 'carnada', 'comida', 'bebidas', 'combustible', 'traslados', 'equipos', 'licencia' ];
$equipo  = [ 'lancha', 'motor_electrico', 'sonda', 'equipos', 'chalecos' ];
$nota    = "\nDueño (24/10/2026): precio, 'Incluye' y 'Equipo' los cargó Cheyenne, que conoce personalmente al prestador. Datos correctos, se conservan (restaurados el 24/10/2026).";

$p = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => 'Experiencia Isoró', 'posts_per_page' => 3 ] );
if ( 1 !== count( $p ) ) {
	WP_CLI::error( 'Esperaba exactamente una ficha "Experiencia Isoró"; encontré ' . count( $p ) . '.' );
}
$id = $p[0]->ID;
WP_CLI::log( "#$id incluye: " . wp_json_encode( get_field( 'incluye', $id ) ) . ' → ' . wp_json_encode( $incluye ) );
WP_CLI::log( "#$id equipo:  " . wp_json_encode( get_field( 'equipo', $id ) ) . ' → ' . wp_json_encode( $equipo ) );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": restaurar incluye y equipo de #$id; precio=" . wp_json_encode( get_field( 'precio', $id ) ) . ' fecha=' . wp_json_encode( get_field( 'precio_fecha', $id ) ) );
if ( ! $aplicar ) {
	return;
}
update_field( 'incluye', $incluye, $id );
update_field( 'equipo', $equipo, $id );
$notas = (string) get_field( 'notas', $id );
if ( false === strpos( $notas, 'los cargó Cheyenne' ) ) {
	update_field( 'notas', $notas . $nota, $id );
}
$ok = $incluye === array_values( (array) get_field( 'incluye', $id ) ) && $equipo === array_values( (array) get_field( 'equipo', $id ) );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #$id " . get_the_title( $id ) );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( 'Incluye y Equipo restaurados.' );
