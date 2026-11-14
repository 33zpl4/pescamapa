<?php
/**
 * Provisional cover photos from Wikimedia Commons for the guides that had none, 14/11/2026 (owner: "para las guías sin foto,
 * usá por ahora la foto de Wikimedia Commons; después busco mejores"). Source of truth: /tmp/pm-fotos/fotos-wp.json, written by
 * scripts/commons.py bajar-wp (author, licence, licence URL, Commons page and date read come straight from Commons) next to the
 * JPEG files (wordpress/fotos/commons-<clave>.jpg, <= 1600 px). Credit fields set exactly as the other Commons photos:
 * foto_origen=licencia, foto_autor, foto_licencia, foto_licencia_url, foto_pagina. The photo goes on the Spanish guide (en/pt inherit by clave).
 * NEVER replaces a photo a guide already has. The attachment description (internal, never shown) carries the "provisional" mark.
 * Idempotent: the attachment is reused by file name. Dry run first ($aplicar = false). Run with wp-script.yml.
 */

$aplicar = false;
$carpeta = '/tmp/pm-fotos';
$datos   = json_decode( (string) @file_get_contents( "$carpeta/fotos-wp.json" ), true );
if ( ! is_array( $datos ) || ! $datos ) {
	WP_CLI::error( 'no pude leer fotos-wp.json' );
}
$alts = json_decode( (string) @file_get_contents( "$carpeta/fotos-wp-textos.json" ), true );
if ( ! is_array( $alts ) ) {
	WP_CLI::error( 'no pude leer fotos-wp-textos.json' );
}

$ok_todo = true;
foreach ( $datos as $clave => $f ) {
	$es = pm_buscar( 'pm_destino', $clave, 'es' );
	if ( ! $es ) {
		WP_CLI::error( "No encontré la guía $clave (es)." );
	}
	$antes = (int) get_post_thumbnail_id( $es->ID );
	if ( $antes && wp_attachment_is_image( $antes ) ) {
		WP_CLI::log( "OMITE: $clave (es) #{$es->ID} ya tiene la foto #$antes; no se reemplaza" );
		continue;
	}
	$t = $alts[ $clave ] ?? null;
	if ( ! $t ) {
		WP_CLI::error( "falta texto alternativo de $clave" );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave (es) #{$es->ID}: {$f['archivo']} | autor={$f['autor']} | {$f['licencia']} | {$f['licencia_url']} | {$f['pagina']} | leída {$f['leida']} | {$f['ancho']}x{$f['alto']}" );
	$ya  = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $f['archivo_local'], 'numberposts' => 1, 'fields' => 'ids' ] );
	$att = $ya ? (int) $ya[0] : 0;
	$ruta = "$carpeta/{$f['archivo_local']}";
	if ( ! $att ) {
		if ( ! is_file( $ruta ) ) {
			WP_CLI::error( "falta {$f['archivo_local']} en $carpeta" );
		}
		$dim = @getimagesize( $ruta );
		WP_CLI::log( '  archivo ' . $f['archivo_local'] . ' ' . size_format( filesize( $ruta ) ) . ( $dim ? " {$dim[0]}x{$dim[1]}" : ' (¡no se lee como imagen!)' ) );
		if ( ! $aplicar ) {
			WP_CLI::log( '  SIMULACRO: cargaría el archivo y lo asignaría' );
			continue;
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$tmp = wp_tempnam( $f['archivo_local'] );
		copy( $ruta, $tmp );
		$att = media_handle_sideload( [ 'name' => $f['archivo_local'], 'tmp_name' => $tmp ], $es->ID, $t['titulo'] );
		if ( is_wp_error( $att ) ) {
			@unlink( $tmp );
			WP_CLI::error( $att->get_error_message() );
		}
		update_post_meta( $att, '_pm_archivo_origen', $f['archivo_local'] );
		update_post_meta( $att, '_wp_attachment_image_alt', $t['alt'] );
		wp_update_post( [ 'ID' => $att, 'post_content' => "FOTO PROVISORIA (14/11/2026): {$f['archivo']} de Wikimedia Commons, leída {$f['leida']}. El dueño la reemplazará por una mejor. {$f['pagina']}" ] );
	} elseif ( ! $aplicar ) {
		WP_CLI::log( "  SIMULACRO: ya está cargada (#$att); solo se asignaría" );
		continue;
	}
	update_field( 'foto_autor', $f['autor'], $att );
	update_field( 'foto_origen', 'licencia', $att );
	update_field( 'foto_licencia', $f['licencia'], $att );
	update_field( 'foto_licencia_url', $f['licencia_url'], $att );
	update_field( 'foto_pagina', $f['pagina'], $att );
	set_post_thumbnail( $es->ID, $att );

	// Read back: es has it, en and pt inherit it by key, and the credit renders on the photo.
	$ok = (int) get_post_thumbnail_id( $es->ID ) === $att && wp_attachment_is_image( $att )
		&& pm_campo( 'foto_autor', $att ) === $f['autor'] && pm_campo( 'foto_pagina', $att ) === $f['pagina'] && pm_campo( 'foto_licencia', $att ) === $f['licencia'];
	$herencia = [];
	foreach ( [ 'en', 'pt' ] as $i ) {
		$p = pm_buscar( 'pm_destino', $clave, $i );
		$herencia[ $i ] = $p ? ( pm_destino( $p->ID )['foto'] === $att ? 'ok' : 'NO' ) : 'sin versión';
		$ok = $ok && 'NO' !== $herencia[ $i ];
	}
	$html = pm_foto( $att, $t['alt'], '400px' );
	$ok   = $ok && false !== strpos( $html, '<figcaption>' ) && false !== strpos( $html, $f['pagina'] ) && false !== strpos( $html, esc_html( $f['autor'] ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto $clave #$att ({$f['archivo']}) herencia en/pt=" . wp_json_encode( $herencia ) . ' crédito en figcaption=' . ( false !== strpos( $html, '<figcaption>' ) ? 'sí' : 'NO' ) . ' ' . wp_get_attachment_url( $att ) );
	$ok_todo = $ok_todo && $ok;
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok_todo || WP_CLI::error( 'alguna foto no quedó.' );
WP_CLI::log( 'APLICADO' );
