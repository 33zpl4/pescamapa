<?php
/**
 * Jurassic Lake guide: cover photo, 30/10/2026. The owner supplied it with the credit "Red Kulper" (spelling as typed; confirm it).
 * It shows an angler wading in spray on a turquoise lake in steppe country; the alt text does not name the lake. The photo is 770 x 513 px:
 * fine for the card and the guide, small for a very large hero. Set on the Spanish guide (the English one reads the photo from it).
 * Origin "cedida": supplied by the owner; the exact licence terms were not checked here.
 * Idempotent: the attachment is reused by file name. Dry run first. Run with wp-script.yml (script = jurassic-lake-foto-2026-10-30);
 * the file lives in wordpress/fotos/ and wp-script.yml copies it to /tmp/pm-fotos/.
 */

$aplicar = false;
$carpeta = '/tmp/pm-fotos';
$foto    = [
	'archivo' => 'jurassic-lake-pescador-oleaje.jpg', 'autor' => 'Red Kulper', 'origen' => 'cedida',
	'titulo'  => 'Angler wading in the spray on the shore of a turquoise Patagonian lake',
	'alt'     => 'An angler stands in the wind-blown spray at the edge of a turquoise lake, with steppe hills behind',
];

$es = pm_buscar( 'pm_destino', 'jurassic-lake', 'es' );
if ( ! $es ) {
	WP_CLI::error( 'No encontré la guía jurassic-lake (es).' );
}
$antes = (int) get_post_thumbnail_id( $es->ID );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": jurassic-lake (es) #{$es->ID}: foto {$foto['archivo']} de {$foto['autor']}" . ( $antes ? " (hoy tiene la foto #$antes, se reemplaza)" : ' (hoy sin foto)' ) );

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
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto jurassic-lake #$att ({$foto['archivo']}, {$foto['autor']}) " . wp_get_attachment_url( $att ) );
$ok || WP_CLI::error( 'la foto no quedó.' );
WP_CLI::log( 'APLICADO' );
