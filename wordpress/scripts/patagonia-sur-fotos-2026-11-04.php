<?php
/**
 * Río Grande and Río Gallegos guides: cover photos, 4/11/2026. Supplied by the owner with their credits:
 *  - Río Grande: "Facundo Santana" (angler in silhouette, bent rod at sunset, 1500 x 949 px).
 *  - Río Gallegos: "tripin.travel" (angler holding a sea-run brown trout, 1400 x 1050 px; converted from webp to jpg).
 * Origin "cedida": supplied by the owner; the exact licence terms were not checked here. Set on the Spanish guide (the English one reads it).
 * Idempotent: an attachment is reused by file name. Dry run first. Run with wp-script.yml (script = patagonia-sur-fotos-2026-11-04);
 * the files live in wordpress/fotos/ and wp-script.yml copies them to /tmp/pm-fotos/.
 */

$aplicar = false;
$carpeta = '/tmp/pm-fotos';
$fotos   = [
	'rio-grande' => [
		'archivo' => 'rio-grande-pescador-atardecer.jpg', 'autor' => 'Facundo Santana', 'origen' => 'cedida',
		'titulo'  => 'Angler in silhouette at sunset with a fly rod bent in an arc over a wide river',
		'alt'     => 'An angler stands in the shallows of a wide river at sunset with his fly rod bent in a high arc against a dark blue sky',
	],
	'rio-gallegos' => [
		'archivo' => 'rio-gallegos-trucha-de-mar.jpg', 'autor' => 'tripin.travel', 'origen' => 'cedida',
		'titulo'  => 'Smiling angler in a brimmed hat holding a large silver sea-run brown trout in shallow water',
		'alt'     => 'A smiling angler in a brimmed hat kneels in shallow water holding a large silver sea-run brown trout, with steppe hills behind',
	],
];

$ok_todo = true;
foreach ( $fotos as $clave => $foto ) {
	$es = pm_buscar( 'pm_destino', $clave, 'es' );
	if ( ! $es ) {
		WP_CLI::error( "No encontré la guía $clave (es)." );
	}
	$antes = (int) get_post_thumbnail_id( $es->ID );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave (es) #{$es->ID}: foto {$foto['archivo']} de {$foto['autor']}" . ( $antes ? " (hoy tiene la foto #$antes, se reemplaza)" : ' (hoy sin foto)' ) );
	$ya  = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $foto['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
	$att = $ya ? (int) $ya[0] : 0;
	$ruta = "$carpeta/{$foto['archivo']}";
	if ( ! $att ) {
		if ( ! is_file( $ruta ) ) {
			WP_CLI::error( "falta {$foto['archivo']} en $carpeta" );
		}
		if ( ! $aplicar ) {
			WP_CLI::log( '  SIMULACRO: cargaría ' . $foto['archivo'] . ' (' . size_format( filesize( $ruta ) ) . ')' );
			continue;
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
		WP_CLI::log( "  SIMULACRO: la foto ya está cargada (#$att); solo se asignaría" );
		continue;
	}
	set_post_thumbnail( $es->ID, $att );
	$ok = (int) get_post_thumbnail_id( $es->ID ) === $att && pm_campo( 'foto_autor', $att ) === $foto['autor'] && wp_attachment_is_image( $att );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto $clave #$att ({$foto['archivo']}, {$foto['autor']}) " . wp_get_attachment_url( $att ) );
	$ok_todo = $ok_todo && $ok;
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok_todo || WP_CLI::error( 'alguna foto no quedó.' );
WP_CLI::log( 'APLICADO' );
