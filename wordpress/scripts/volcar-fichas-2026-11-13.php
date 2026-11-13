<?php
/**
 * Read-only, 13/11/2026: prints the LIVE es/en/pt guides of jurassic-lake, rio-grande and rio-gallegos (body, faqs, fuentes, aguas) and every
 * pm_prestador card of those three destinations (all card fields, per language, in full) so the card and guide patches are written against
 * what is published today, and the page "precio-pesca-patagonia-sur" (price note). Writes nothing.
 * Run with wp-script.yml (script = volcar-fichas-2026-11-13).
 */
foreach ( [ 'jurassic-lake', 'rio-grande', 'rio-gallegos' ] as $clave ) {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$p = pm_buscar( 'pm_destino', $clave, $i );
		if ( ! $p ) {
			WP_CLI::log( "#### GUIA $clave ($i): NO EXISTE" );
			continue;
		}
		WP_CLI::log( "#### GUIA $clave ($i) #{$p->ID} slug={$p->post_name}" );
		foreach ( [ 'faqs', 'fuentes', 'temporada' ] as $k ) {
			WP_CLI::log( "@@ $k: " . wp_json_encode( pm_filas( $k, $p->ID ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
		}
		WP_CLI::log( '@@ cuerpo:' );
		WP_CLI::log( $p->post_content );
		WP_CLI::log( "#### fin GUIA $clave ($i)" );
	}
}
$campos = [ 'tipo', 'prest_destino', 'localidad', 'capacidad', 'prest_capacidad_unidad', 'incluye', 'precio_fecha', 'prest_publico', 'prest_consentimiento', 'estado', 'verificado', 'prest_fuente', 'prest_idiomas', 'equipo', 'prest_modalidades', 'prest_especies', 'datos_ejemplo', 'comision', 'notas', 'web' ];
$texto  = [ 'descripcion', 'precio', 'prest_temporada', 'prest_programa', 'prest_estadia_minima', 'prest_acompanante', 'prest_no_incluye', 'lancha', 'motor' ];
foreach ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'numberposts' => -1 ] ) as $c ) {
	$d = (string) pm_campo( 'prest_destino', $c->ID );
	if ( ! in_array( $d, [ 'jurassic-lake', 'rio-grande', 'rio-gallegos' ], true ) ) {
		continue;
	}
	WP_CLI::log( "#### CARD #{$c->ID} «{$c->post_title}» destino=$d status={$c->post_status}" );
	foreach ( $campos as $k ) {
		$v = pm_campo( $k, $c->ID );
		WP_CLI::log( "@@ $k: " . ( is_array( $v ) ? implode( ',', $v ) : str_replace( "\n", ' | ', (string) $v ) ) );
	}
	foreach ( $texto as $k ) {
		foreach ( [ '', '_en', '_pt' ] as $s ) {
			WP_CLI::log( "@@ {$k}{$s}: " . str_replace( "\n", ' | ', (string) pm_campo( $k . $s, $c->ID ) ) );
		}
	}
	WP_CLI::log( '@@ fotos: ' . implode( ',', (array) ( pm_campo( 'fotos', $c->ID ) ?: [] ) ) );
}
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$ya = array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'precio-pesca-patagonia-sur', 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i );
	foreach ( $ya as $p ) {
		WP_CLI::log( "#### NOTA precio-pesca-patagonia-sur ($i) #{$p->ID} slug={$p->post_name} titulo={$p->post_title}" );
		WP_CLI::log( 'datos: ' . wp_json_encode( pm_filas( 'nota_datos', $p->ID ), JSON_UNESCAPED_UNICODE ) );
		WP_CLI::log( 'bajada: ' . pm_campo( 'bajada', $p->ID ) );
		WP_CLI::log( $p->post_content );
		WP_CLI::log( "#### fin NOTA ($i)" );
	}
}
