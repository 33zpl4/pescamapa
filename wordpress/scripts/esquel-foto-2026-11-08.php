<?php
/**
 * Esquel y Trevelin guide: first cover photo, 8/11/2026 (it had none). The owner supplied it with the source
 * "pesca.chubut.gob.ar" (the province's fishing site): a fly fisherman checks his fly box beside a river, snow-capped mountains
 * and lupines behind. Converted from the 1280 x 854 WebP to JPEG quality 76 (117 KB); WordPress serves WebP. Credit shown on the
 * photo: the website. Origin "cedida" (the owner's rule: credit the source; remove it if the owner of the photo asks, licence
 * terms not checked). Set on the Spanish guide (en/pt read it from it). Idempotent: the attachment is reused by file name.
 * Dry run first. Run with wp-script.yml (script = esquel-foto-2026-11-08); the file lives in wordpress/fotos/.
 */

$aplicar = true;
$carpeta = '/tmp/pm-fotos';
$foto    = [
	'archivo' => 'esquel-trevelin-pescador-moscas.jpg', 'autor' => 'pesca.chubut.gob.ar', 'origen' => 'cedida',
	'titulo'  => 'Fly fisherman checking his fly box beside a river with snow-capped mountains and lupines',
	'alt'     => 'A fly fisherman in a cap and sunglasses holds an open fly box beside a clear river, with snow-capped mountains and purple lupines behind him',
];

$es = pm_buscar( 'pm_destino', 'esquel-trevelin', 'es' );
if ( ! $es ) {
	WP_CLI::error( 'No encontré la guía esquel-trevelin (es).' );
}
$antes = (int) get_post_thumbnail_id( $es->ID );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": esquel-trevelin (es) #{$es->ID}: foto {$foto['archivo']} de {$foto['autor']}" . ( $antes ? " (hoy tiene la foto #$antes, se reemplaza)" : ' (hoy sin foto)' ) );

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
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto esquel-trevelin #$att ({$foto['archivo']}, {$foto['autor']}) " . wp_get_attachment_url( $att ) );
$ok || WP_CLI::error( 'la foto no quedó.' );
WP_CLI::log( 'APLICADO' );
