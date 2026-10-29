<?php
/**
 * Cover photos for two English-market notes, 29/10/2026 (owner supplied the photos and the credits):
 *   - golden-dorado-guia (pillar guide, English only): "dorado-saltando-con-senuelo.jpg", Francois Botha.
 *   - tallas-cupos-vedas (size, bag limits and closed seasons; es/pt/en): "pescador-silueta-al-atardecer.jpg", Sandra Seitamaa.
 * The photo is set on the note that carries the cover: the Spanish note, or the English one when there is no Spanish
 * (pm_nota() reads the photo from there). Photos live in wordpress/fotos/ and wp-script.yml copies them to /tmp/pm-fotos/.
 * Licence: the owner supplied both with a photographer credit ("licencia"); the exact licence terms were not checked here.
 *
 * Idempotent: a photo already loaded (same file name) is reused. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = notas-fotos-2-2026-10-29).
 */

$aplicar = true;
$carpeta = '/tmp/pm-fotos';

$fotos = [
	'golden-dorado-guia' => [
		'idioma' => 'en', 'archivo' => 'dorado-saltando-con-senuelo.jpg', 'autor' => 'Francois Botha', 'origen' => 'licencia',
		'titulo' => 'Golden dorado leaping out of the water with a lure in its mouth',
		'alt'    => 'A golden dorado leaps out of the water with a lure in its mouth, throwing up droplets',
	],
	'tallas-cupos-vedas' => [
		'idioma' => 'es', 'archivo' => 'pescador-silueta-al-atardecer.jpg', 'autor' => 'Sandra Seitamaa', 'origen' => 'licencia',
		'titulo' => 'Silueta de un pescador con caña de spinning al atardecer',
		'alt'    => 'Silueta de un pescador con una caña de spinning contra un cielo violeta al atardecer',
	],
];

function pm_f2_nota( string $clave, string $idioma ): ?WP_Post {
	foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		if ( pm_idioma_de( $p->ID ) === $idioma ) {
			return $p;
		}
	}
	return null;
}

function pm_f2_foto( array $f, int $padre, string $carpeta, bool $aplicar ): int {
	$ya = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $f['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( $ya ) {
		return (int) $ya[0];
	}
	$ruta = "$carpeta/{$f['archivo']}";
	if ( ! is_file( $ruta ) ) {
		WP_CLI::warning( "falta {$f['archivo']} en $carpeta: sin foto" );
		return 0;
	}
	if ( ! $aplicar ) {
		WP_CLI::log( 'SIMULACRO: cargaría ' . $f['archivo'] . ' (' . size_format( filesize( $ruta ) ) . ')' );
		return 0;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $f['archivo'] );
	copy( $ruta, $tmp );
	$att = media_handle_sideload( [ 'name' => $f['archivo'], 'tmp_name' => $tmp ], $padre, $f['titulo'] );
	if ( is_wp_error( $att ) ) {
		@unlink( $tmp );
		WP_CLI::error( $att->get_error_message() );
	}
	update_post_meta( $att, '_pm_archivo_origen', $f['archivo'] );
	update_post_meta( $att, '_wp_attachment_image_alt', $f['alt'] );
	update_field( 'foto_autor', $f['autor'], $att );
	update_field( 'foto_origen', $f['origen'], $att );
	return (int) $att;
}

foreach ( $fotos as $clave => $f ) {
	$nota = pm_f2_nota( $clave, $f['idioma'] );
	if ( ! $nota ) {
		WP_CLI::error( "$clave ({$f['idioma']}): no existe la nota." );
	}
	$antes = (int) get_post_thumbnail_id( $nota->ID );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ({$f['idioma']}) #{$nota->ID} «{$nota->post_title}»: foto {$f['archivo']} de {$f['autor']}" . ( $antes ? " (hoy tiene la foto #$antes, se reemplaza)" : ' (hoy sin foto)' ) );
	$att = pm_f2_foto( $f, $nota->ID, $carpeta, $aplicar );
	if ( ! $att ) {
		continue;
	}
	set_post_thumbnail( $nota->ID, $att );
	$ok = (int) get_post_thumbnail_id( $nota->ID ) === $att && pm_campo( 'foto_autor', $att ) === $f['autor'] && wp_attachment_is_image( $att );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto $clave #$att ({$f['archivo']}, {$f['autor']}) " . wp_get_attachment_url( $att ) );
	$ok || WP_CLI::error( "$clave: la foto no quedó." );
}
WP_CLI::success( $aplicar ? 'Fotos aplicadas.' : 'Simulacro terminado.' );
