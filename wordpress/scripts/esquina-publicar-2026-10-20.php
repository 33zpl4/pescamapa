<?php
/**
 * Publishes Esquina (#38, Spanish) at the owner's request, 20/10/2026.
 * "Reglamento verificado" stays empty (the guide just doesn't show it) until
 * someone reads the official page; outfitters stay non-public until real.
 * Run with wp-script.yml (script = esquina-publicar-2026-10-20).
 */

$p = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'esquina', 'numberposts' => 1 ] )[0] ?? null;
if ( ! $p ) {
	WP_CLI::error( 'No encontré Esquina.' );
}
if ( 'publish' === $p->post_status ) {
	WP_CLI::log( 'Esquina ya estaba publicada: ' . get_permalink( $p->ID ) );
	return;
}
// Clear the draft date so WordPress stamps the publication time (order on the home page).
wp_update_post( [ 'ID' => $p->ID, 'post_status' => 'publish', 'post_date' => current_time( 'mysql' ), 'post_date_gmt' => current_time( 'mysql', true ) ] );
update_field( 'actualizado', wp_date( 'Ymd' ), $p->ID );
flush_rewrite_rules( false );
WP_CLI::success( 'Esquina publicada: ' . get_permalink( $p->ID ) );
