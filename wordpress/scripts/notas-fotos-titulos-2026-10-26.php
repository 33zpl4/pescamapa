<?php
/**
 * The three "when to fish" notes, 26/10/2026 (owner's review of the list):
 *   1. different display dates (the field "Fecha de publicación"; "Actualizado" stays the real date),
 *   2. dorado and surubí titles that no longer repeat one template (slugs are NOT touched: no URL changes),
 *   3. a cover photo on each note, with the photographer's name (photos are in wordpress/fotos/, copied to
 *      /tmp/pm-fotos/ by wp-script.yml; without the folder the photo step is skipped, as in the test run).
 *
 * Idempotent: titles, SEO fields and dates are set to fixed values; a photo already loaded (same file name)
 * is reused. Dry run first ($aplicar = false); run with wp-script.yml (script = notas-fotos-titulos-2026-10-26).
 */

$aplicar = true;
$carpeta = '/tmp/pm-fotos';

$titulos = [
	'cuando-pescar-dorado' => [
		'es' => [ 'Cuándo pescar el dorado en el Paraná: dos ventanas y por qué las fuentes no coinciden', 'Cuándo pescar dorado: dos ventanas en el Paraná | Pescamapa', null ],
		'en' => [ 'When to fish golden dorado on the Paraná: two windows, and why the sources disagree', 'Best time to fish golden dorado: two windows | Pescamapa', null ],
		'pt' => [ 'Quando pescar o dourado no Paraná: duas janelas e por que as fontes não coincidem', 'Quando pescar dourado: duas janelas no Paraná | Pescamapa', null ],
	],
	'cuando-pescar-surubi' => [
		'es' => [ 'Cuándo pescar el surubí: no tiene una temporada, se mueve con el agua', 'Cuándo pescar surubí: se mueve con el agua | Pescamapa', 'Otoño y primavera en el Paraná medio, los meses fríos en el Alto Paraná y octubre en Concordia.' ],
		'en' => [ 'When to fish surubí: there is no single season, it follows the water', 'When to fish surubí: it follows the water | Pescamapa', 'Autumn and spring on the middle Paraná, the cold months on the Upper Paraná, and October at Concordia.' ],
		'pt' => [ 'Quando pescar o surubi: não há uma única temporada, ele segue a água', 'Quando pescar surubi: ele segue a água | Pescamapa', 'Outono e primavera no Paraná médio, os meses frios no Alto Paraná e outubro em Concordia.' ],
	],
];

// Display dates (Spanish note; the other languages read it from there). Dorado and surubí have species pages from 24/10.
$fechas = [ 'cuando-pescar-dorado' => '20261024', 'cuando-pescar-surubi' => '20261025', 'cuando-pescar-trucha-patagonia' => '20261026' ];

$fotos = [
	'cuando-pescar-dorado' => [
		'archivo' => 'dorado-tres-pescadores.jpg', 'autor' => 'Tomas Frattini', 'origen' => 'cedida',
		'titulo' => 'Tres pescadores sosteniendo un dorado grande a bordo', 'alt' => 'Tres pescadores sonríen a bordo con un dorado grande entre los tres',
	],
	'cuando-pescar-surubi' => [
		'archivo' => 'surubi-pescador-a-bordo.jpg', 'autor' => 'Augusto Frattini', 'origen' => 'cedida',
		'titulo' => 'Pescador sosteniendo un surubí, con camalotes al fondo', 'alt' => 'Un pescador sonríe a bordo con un surubí atigrado en las manos',
	],
	'cuando-pescar-trucha-patagonia' => [
		'archivo' => 'pescador-con-mosca-en-un-lago.jpg', 'autor' => 'Agustin Gorordo Coggiola', 'origen' => 'licencia',
		'titulo' => 'Pescador con mosca, con el agua a la cintura, en la orilla de un lago con bosque y colinas', 'alt' => 'Un pescador con mosca en el agua, a la orilla de un lago con bosque y colinas al fondo',
	],
];

function pm_nf_nota( string $clave, string $idioma ): ?WP_Post {
	foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		if ( pm_idioma_de( $p->ID ) === $idioma ) {
			return $p;
		}
	}
	return null;
}

/** The attachment for a file name: the one already loaded, else sideload it from $carpeta. 0 when the file is not there. */
function pm_nf_foto( array $f, int $padre, string $carpeta, bool $aplicar ): int {
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

foreach ( $titulos as $clave => $por_idioma ) {
	foreach ( $por_idioma as $i => [ $titulo, $seo_title, $bajada ] ) {
		$p = pm_nf_nota( $clave, $i );
		if ( ! $p ) {
			WP_CLI::error( "$clave ($i): no existe la nota." );
		}
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) «{$p->post_title}» → «{$titulo}» (seo " . mb_strlen( $seo_title ) . ' car.)' . ( $bajada ? ' + bajada' : '' ) );
		if ( mb_strlen( $seo_title ) > 60 ) {
			WP_CLI::error( "$clave ($i): el título SEO pasa de 60 caracteres." );
		}
		if ( ! $aplicar ) {
			continue;
		}
		wp_update_post( [ 'ID' => $p->ID, 'post_title' => $titulo ] );
		update_field( 'seo_title', $seo_title, $p->ID );
		if ( $bajada ) {
			update_field( 'bajada', $bajada, $p->ID );
		}
		update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
		$ok = get_post_field( 'post_title', $p->ID ) === $titulo && pm_campo( 'seo_title', $p->ID ) === $seo_title && ( ! $bajada || pm_campo( 'bajada', $p->ID ) === $bajada );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": título $clave ($i) #{$p->ID} " . get_permalink( $p->ID ) );
		$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
	}
}

foreach ( $fechas as $clave => $fecha ) {
	$es = pm_nf_nota( $clave, 'es' );
	if ( ! $es ) {
		WP_CLI::error( "$clave: no existe la nota en español." );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": fecha $clave → $fecha" );
	if ( $aplicar ) {
		update_field( 'nota_fecha', $fecha, $es->ID );
		$ok = str_replace( '-', '', (string) pm_campo( 'nota_fecha', $es->ID ) ) === $fecha;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": fecha $clave #{$es->ID} = " . pm_campo( 'nota_fecha', $es->ID ) );
		$ok || WP_CLI::error( "$clave: la fecha no quedó." );
	}
}

foreach ( $fotos as $clave => $f ) {
	$es = pm_nf_nota( $clave, 'es' );
	$att = pm_nf_foto( $f, $es->ID, $carpeta, $aplicar );
	if ( ! $att ) {
		continue;
	}
	set_post_thumbnail( $es->ID, $att );
	$ok = (int) get_post_thumbnail_id( $es->ID ) === $att && pm_campo( 'foto_autor', $att ) === $f['autor'] && wp_attachment_is_image( $att );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto $clave #$att ({$f['archivo']}, {$f['autor']}) " . wp_get_attachment_url( $att ) );
	$ok || WP_CLI::error( "$clave: la foto no quedó." );
}
WP_CLI::success( $aplicar ? 'Títulos, fechas y fotos aplicados.' : 'Simulacro terminado.' );
