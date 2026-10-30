<?php
/**
 * Bariloche (es/en), 30/10/2026: photo of the Limay package card. The owner supplied it with the credit "Claudio Antonelli"
 * (spelling as the owner wrote it): an angler in a green rowboat plays a fish on a calm river, under dry golden hills and a blue
 * sky; the alt text does not name the river's stretch. The file is 600 x 800 px (120 KB), already small.
 * Needs the "foto" field of the package (plugin deploy first). One attachment, set on the Limay row of both languages.
 * Origin "cedida": supplied by the owner; the exact licence terms were not checked here.
 * Idempotent: the attachment is reused by file name; a row that already has a photo is left alone. Dry run first
 * ($aplicar = false); run with wp-script.yml (script = paquetes-foto-limay-2026-10-30); the file lives in wordpress/fotos/.
 */

$aplicar = true;
$carpeta = '/tmp/pm-fotos';
$foto    = [
	'archivo' => 'limay-pescador-bote.jpg', 'autor' => 'Claudio Antonelli', 'origen' => 'cedida',
	'titulo'  => 'Fly angler in a green rowboat playing a fish on a calm river',
	'alt'     => 'An angler in a green rowboat, rod bent, plays a fish on a calm river below dry golden hills and a blue sky',
];
$filas_titulo = [
	'es' => 'Pesca con mosca en el río Limay (Bariloche): descenso en bote a remo, jornada completa',
	'en' => 'Fly fishing on the Limay River (Bariloche): a full-day rowboat float',
];

$es = pm_buscar( 'pm_destino', 'bariloche', 'es' );
if ( ! $es ) {
	WP_CLI::error( 'No encontré la guía bariloche (es).' );
}

$ya  = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $foto['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
$att = $ya ? (int) $ya[0] : 0;
$ruta = "$carpeta/{$foto['archivo']}";
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": foto {$foto['archivo']} de {$foto['autor']}; " . ( $att ? "adjunto ya cargado (#$att)" : 'adjunto nuevo' ) );

if ( ! $att ) {
	if ( ! is_file( $ruta ) ) {
		WP_CLI::error( "falta {$foto['archivo']} en $carpeta" );
	}
	if ( $aplicar ) {
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
	} else {
		WP_CLI::log( 'SIMULACRO: cargaría ' . $foto['archivo'] . ' (' . size_format( filesize( $ruta ) ) . ')' );
	}
}

foreach ( $filas_titulo as $i => $titulo ) {
	$p = pm_buscar( 'pm_destino', 'bariloche', $i );
	if ( ! $p ) {
		WP_CLI::log( "bariloche ($i): no hay guía en este idioma." );
		continue;
	}
	$filas = pm_filas( 'paquetes', $p->ID );
	$n     = 0;
	foreach ( $filas as &$f ) {
		if ( ( $f['titulo'] ?? '' ) === $titulo && ! (int) ( $f['foto'] ?? 0 ) ) {
			$f['foto'] = $att ?: 1;
			++$n;
		}
	}
	unset( $f );
	WP_CLI::log( "bariloche ($i) #{$p->ID}: " . ( $aplicar ? 'se pone' : 'se pondría' ) . " la foto en $n de " . count( $filas ) . ' paquetes' );
	if ( $aplicar && $n ) {
		update_field( 'paquetes', $filas, $p->ID );
		$leidos = pm_paquetes( $p->ID );
		$ok     = false;
		foreach ( $leidos as $l ) {
			if ( $l['titulo'] === $titulo && (int) $l['foto'] === $att && pm_campo( 'foto_autor', $att ) === $foto['autor'] && wp_attachment_is_image( $att ) ) {
				$ok = true;
			}
		}
		WP_CLI::log( ( $ok ? '  VERIFIED' : '  FALLÓ' ) . ": foto #$att en el paquete del Limay ({$foto['autor']}) " . wp_get_attachment_url( $att ) );
		$ok || WP_CLI::error( 'la foto no quedó en el paquete.' );
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
