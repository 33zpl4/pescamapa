<?php
/**
 * 22/10/2026 (owner OK): the default WordPress sample content, "Hola mundo" (post) and
 * "Página de ejemplo" (page), is in the sitemap and unknown to Google: noise.
 * Sets them to draft (recoverable from the admin; the URLs stop answering). Only the
 * default ones: matched by slug AND by still being the stock text, so a real page that
 * later reuses the slug is never touched. Idempotent. Dry run found exactly the 2 stock pages (#1, #2); applied;
 * run with wp-script.yml (script = paginas-ejemplo-2026-10-22).
 */

$aplicar = true;

$candidatas = [
	[ 'post', 'hola-mundo', 'Bienvenido a WordPress' ],
	[ 'page', 'pagina-ejemplo', 'Esta es una página de ejemplo' ],
];
$tocadas = 0;
foreach ( $candidatas as [ $tipo, $slug, $texto ] ) {
	$p = get_posts( [ 'name' => $slug, 'post_type' => $tipo, 'post_status' => 'any', 'numberposts' => 1 ] );
	if ( ! $p ) {
		WP_CLI::log( "$slug: no existe (nada para hacer)" );
		continue;
	}
	$p = $p[0];
	$stock = false !== stripos( $p->post_content, $texto ) || false !== stripos( $p->post_content, 'Welcome to WordPress' ) || false !== stripos( $p->post_content, 'This is an example page' );
	WP_CLI::log( sprintf( '%s (#%d): estado %s, texto de fábrica: %s', $slug, $p->ID, $p->post_status, $stock ? 'sí' : 'NO' ) );
	if ( 'publish' !== $p->post_status ) {
		continue;
	}
	if ( ! $stock ) {
		WP_CLI::warning( "  $slug fue editada: no se toca" );
		continue;
	}
	$tocadas++;
	if ( $aplicar ) {
		wp_update_post( [ 'ID' => $p->ID, 'post_status' => 'draft' ] );
		WP_CLI::log( '  VERIFIED: estado ahora ' . get_post_status( $p->ID ) );
	}
}
WP_CLI::log( "$tocadas página(s) a despublicar. " . ( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' ) );
