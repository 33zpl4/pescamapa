<?php
/**
 * La Paz guide (Entre Ríos): cover photo, 31/10/2026. The owner supplied it with the credit "Roque Mario Erazun" (spelling as the
 * owner wrote it): fishermen and families at a canoe on the shore of the Paraná at sunset. Cropped by 2 px per side (the file had a
 * 1 px pale frame) and optimised from 1600 x 990 px (213 KB) to 1200 x 741 px, JPEG quality 76 (89 KB); WordPress serves WebP.
 * Set on the Spanish guide (the other languages read the photo from it). If the guide already had a photo, the old attachment stays
 * in the media library, only the featured image changes. Origin "cedida": supplied by the owner; the exact licence terms were not checked here.
 * Idempotent: the attachment is reused by file name. Dry run first. Run with wp-script.yml (script = la-paz-foto-2026-10-31);
 * the file lives in wordpress/fotos/ and wp-script.yml copies it to /tmp/pm-fotos/.
 */

$aplicar = true;
$carpeta = '/tmp/pm-fotos';
$foto    = [
	'archivo' => 'la-paz-atardecer-canoa.jpg', 'autor' => 'Roque Mario Erazun', 'origen' => 'cedida',
	'titulo'  => 'Fishermen and a canoe on the Paraná shore at sunset',
	'alt'     => 'People gather around a canoe on the shore of a wide river at sunset, under an orange and purple sky',
];

$es = pm_buscar( 'pm_destino', 'la-paz', 'es' );
if ( ! $es ) {
	WP_CLI::error( 'No encontré la guía la-paz (es).' );
}
$antes = (int) get_post_thumbnail_id( $es->ID );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": la-paz (es) #{$es->ID}: foto {$foto['archivo']} de {$foto['autor']}" . ( $antes ? " (hoy tiene la foto #$antes, se reemplaza)" : ' (hoy sin foto)' ) );

$ya = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $foto['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
$att = $ya ? (int) $ya[0] : 0;
$ruta = "$carpeta/{$foto['archivo']}";
if ( ! $att ) {
	if ( ! is_file( $ruta ) ) {
		WP_CLI::error( "falta {$foto['archivo']} en $carpeta" );
	}
	if ( ! $aplicar ) {
		WP_CLI::log( 'SIMULACRO: cargaría ' . $foto['archivo'] . ' (' . size_format( filesize( $ruta ) ) . ')' );
		WP_CLI::log( 'SIMULACRO: no se escribió nada' );
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $foto['archivo'] );
	copy( $ruta, $tmp );
	$att = media_handle_sideload( [ 'name' => $foto['archivo'], 'tmp_name' => $tmp ], $es->ID, $foto['titulo'] );
	if ( is_wp_error( $att ) ) {
		@unlink( $tmp );
		WP_CLI::error( $att->get_error_message() );
	}
	update_post_meta( $att, '_pm_archivo_origen', $foto['archivo'] );
	update_post_meta( $att, '_wp_attachment_image_alt', $foto['alt'] );
	update_field( 'foto_autor', $foto['autor'], $att );
	update_field( 'foto_origen', $foto['origen'], $att );
} elseif ( ! $aplicar ) {
	WP_CLI::log( "SIMULACRO: la foto ya está cargada (#$att); solo se asignaría" );
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
set_post_thumbnail( $es->ID, $att );
$ok = (int) get_post_thumbnail_id( $es->ID ) === $att && pm_campo( 'foto_autor', $att ) === $foto['autor'] && wp_attachment_is_image( $att );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto la-paz #$att ({$foto['archivo']}, {$foto['autor']}) " . wp_get_attachment_url( $att ) );
$ok || WP_CLI::error( 'la foto no quedó.' );
WP_CLI::log( 'APLICADO' );
