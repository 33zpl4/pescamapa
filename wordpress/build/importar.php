<?php
/**
 * One-time migration of the Astro content into WordPress. Runs ON THE SERVER:
 *
 *   wp eval-file importar.php contenido.json /ruta/a/fotos
 *
 * Idempotent and respectful of edits made in wp-admin (same contract as the
 * site factory): an item is created when missing, updated while untouched
 * since the last import, and SKIPPED once someone edited it in wp-admin.
 * Photos are imported once (matched by original file name) with their
 * credits.
 */

if ( empty( $args[0] ) || ! is_readable( $args[0] ) || empty( $args[1] ) || ! is_dir( $args[1] ) ) {
	WP_CLI::error( 'uso: wp eval-file importar.php contenido.json /ruta/fotos' );
}
if ( ! function_exists( 'update_field' ) ) {
	WP_CLI::error( 'Secure Custom Fields no está activo.' );
}
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';

$datos = json_decode( file_get_contents( $args[0] ), true );
$dir   = rtrim( $args[1], '/' );

// --- Photos -----------------------------------------------------------------
$fotos = [];
foreach ( $datos['fotos'] as $archivo => $c ) {
	$ya = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_origen', 'meta_value' => $archivo, 'fields' => 'ids', 'numberposts' => 1 ] );
	if ( $ya ) {
		$fotos[ $archivo ] = (int) $ya[0];
		continue;
	}
	$ruta = "$dir/$archivo";
	if ( ! is_readable( $ruta ) ) {
		WP_CLI::warning( "falta la foto $archivo" );
		continue;
	}
	$tmp = wp_tempnam( $archivo );
	copy( $ruta, $tmp );
	$id = media_handle_sideload( [ 'name' => $archivo, 'tmp_name' => $tmp ], 0, $c['titulo'] );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "foto $archivo: " . $id->get_error_message() );
		continue;
	}
	update_post_meta( $id, '_pm_origen', $archivo );
	update_post_meta( $id, '_wp_attachment_image_alt', $c['titulo'] );
	update_field( 'foto_autor', $c['autor'], $id );
	update_field( 'foto_origen', $c['origen'], $id );
	update_field( 'foto_licencia', $c['licencia'], $id );
	update_field( 'foto_licencia_url', $c['licencia_url'], $id );
	update_field( 'foto_pagina', $c['pagina'], $id );
	$fotos[ $archivo ] = (int) $id;
	WP_CLI::log( "✓ foto $archivo → #$id" );
}

// --- Posts ------------------------------------------------------------------
function pm_imp_buscar( string $tipo, string $clave, ?string $idioma ): ?WP_Post {
	$meta = [ [ 'key' => '_pm_clave_importada', 'value' => $clave ] ];
	if ( $idioma ) {
		$meta[] = [ 'key' => 'idioma', 'value' => $idioma ];
	}
	$p = get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'meta_query' => $meta, 'numberposts' => 1 ] );
	return $p[0] ?? null;
}

$resumen = [ 'creados' => 0, 'actualizados' => 0, 'respetados' => 0 ];
foreach ( [ 'especies', 'destinos', 'proximos' ] as $grupo ) {
	foreach ( $datos[ $grupo ] as $item ) {
		$tipo   = $item['tipo'];
		$idioma = $item['idioma'] ?? null;
		$previo = pm_imp_buscar( $tipo, $item['clave'], $idioma );
		$etiqueta = "{$tipo} {$item['clave']}" . ( $idioma ? " ({$idioma})" : '' );

		if ( $previo ) {
			$importado = (string) get_post_meta( $previo->ID, '_pm_importado_en', true );
			if ( $importado && strtotime( $previo->post_modified_gmt . ' UTC' ) > strtotime( $importado . ' UTC' ) + 5 ) {
				WP_CLI::log( "= $etiqueta: editado en wp-admin, no se toca" );
				$resumen['respetados']++;
				continue;
			}
		}

		$post = [
			'post_type'    => $tipo,
			'post_status'  => 'publish',
			'post_title'   => $item['titulo'],
			'post_name'    => $item['slug'],
			'post_content' => $item['contenido'] ?? '',
		];
		if ( $previo ) {
			$post['ID'] = $previo->ID;
		}
		$id = wp_insert_post( wp_slash( $post ), true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::warning( "$etiqueta: " . $id->get_error_message() );
			continue;
		}
		update_post_meta( $id, '_pm_clave_importada', $item['clave'] );
		foreach ( $item['campos'] as $nombre => $valor ) {
			update_field( $nombre, $valor, $id );
		}
		if ( ! empty( $item['foto'] ) && isset( $fotos[ $item['foto'] ] ) ) {
			set_post_thumbnail( $id, $fotos[ $item['foto'] ] );
		}
		// Re-read the post: the import time must not be older than its last write.
		clean_post_cache( $id );
		update_post_meta( $id, '_pm_importado_en', get_post( $id )->post_modified_gmt );
		$previo ? $resumen['actualizados']++ : $resumen['creados']++;
		WP_CLI::log( ( $previo ? '↻ ' : '+ ' ) . "$etiqueta → #$id" );
	}
}

flush_rewrite_rules( false );
WP_CLI::success( sprintf( 'importación: %d creados, %d actualizados, %d respetados (editados a mano)', $resumen['creados'], $resumen['actualizados'], $resumen['respetados'] ) );
