<?php
/**
 * Photos: WordPress makes the sizes and srcset; we add WebP output and the
 * credit overlay (author + licence, required by CC BY / BY-SA and owed to
 * our own photographers too). Credits live on the attachment (inc/campos.php).
 */

defined( 'ABSPATH' ) || exit;

// Every generated size is WebP: a phone photo of several MB ends in ~100–200 KB.
add_filter( 'image_editor_output_format', function ( $formatos ) {
	$formatos['image/jpeg'] = 'image/webp';
	$formatos['image/png']  = 'image/webp';
	return $formatos;
} );

add_filter( 'big_image_size_threshold', fn() => 2000 );
add_filter( 'jpeg_quality', fn() => 75 );
add_filter( 'wp_editor_set_quality', fn() => 75 );

function pm_credito( int $att ): array {
	return [
		'autor'        => (string) pm_campo( 'foto_autor', $att ),
		'licencia'     => (string) pm_campo( 'foto_licencia', $att ),
		'licencia_url' => (string) pm_campo( 'foto_licencia_url', $att ),
		'pagina'       => (string) pm_campo( 'foto_pagina', $att ),
		'archivo'      => get_the_title( $att ),
	];
}

/**
 * <figure> with responsive image and credit.
 * $enlaces = false when the figure sits inside a link (no nested <a>).
 */
function pm_foto( int $att, string $alt, string $sizes, array $op = [] ): string {
	if ( ! $att || ! wp_attachment_is_image( $att ) ) {
		return '';
	}
	$op = $op + [ 'clase' => '', 'prioridad' => false, 'enlaces' => true, 'tamano' => 'large' ];
	$img = wp_get_attachment_image( $att, $op['tamano'], false, [
		'alt'           => $alt,
		'sizes'         => $sizes,
		'loading'       => $op['prioridad'] ? 'eager' : 'lazy',
		'fetchpriority' => $op['prioridad'] ? 'high' : 'auto',
		'decoding'      => 'async',
	] );
	$c = pm_credito( $att );
	$credito = '';
	if ( $c['autor'] ) {
		$etiqueta = esc_html( pm_t( 'foto' ) );
		if ( $op['enlaces'] && $c['pagina'] ) {
			$autor = '<a href="' . esc_url( $c['pagina'] ) . '" rel="noopener">' . esc_html( $c['autor'] ) . '</a>';
			$lic   = $c['licencia'] ? ', ' . ( $c['licencia_url'] ? '<a href="' . esc_url( $c['licencia_url'] ) . '" rel="noopener license">' . esc_html( $c['licencia'] ) . '</a>' : esc_html( $c['licencia'] ) ) : '';
		} else {
			$autor = esc_html( $c['autor'] );
			$lic   = $c['licencia'] ? ', ' . esc_html( $c['licencia'] ) : '';
		}
		$editada = $c['licencia'] ? ' · ' . esc_html( pm_t( 'foto.editada' ) ) : '';
		$credito = "<figcaption>{$etiqueta}: {$autor}{$lic}{$editada}</figcaption>";
	}
	return '<figure class="foto ' . esc_attr( $op['clase'] ) . '">' . $img . $credito . '</figure>';
}
