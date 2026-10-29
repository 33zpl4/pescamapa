<?php
/**
 * READ ONLY (writes nothing). Prints the live Spanish La Paz (Entre Ríos) guide: body, SEO fields, season rows, FAQs,
 * sources, waters and regulation summary, so the English version is translated from what is live today and not from the
 * original script (editors and later scripts changed it: festival, 2026 fee). Run with wp-script.yml
 * (script = leer-la-paz-2026-10-29).
 */

$p = pm_buscar( 'pm_destino', 'la-paz', 'es' );
if ( ! $p ) {
	WP_CLI::error( 'No existe la guía de La Paz (es).' );
}
WP_CLI::log( "== #{$p->ID} {$p->post_status} " . get_permalink( $p ) );
foreach ( [ 'h1', 'bajada', 'seo_title', 'seo_description', 'epoca', 'modalidades', 'reglamento_resumen', 'reglamento_autoridad', 'reglamento_url', 'reglamento_verificado' ] as $k ) {
	WP_CLI::log( "[$k] " . str_replace( "\n", ' | ', (string) pm_campo( $k, $p->ID ) ) );
}
WP_CLI::log( '[cuerpo]' );
WP_CLI::log( trim( preg_replace( "/<!-- \/?wp:[^>]*-->\s*/", '', $p->post_content ) ) );
foreach ( [ 'temporada', 'faqs', 'fuentes', 'aguas' ] as $r ) {
	WP_CLI::log( "[$r]" );
	foreach ( pm_filas( $r, $p->ID ) as $i => $f ) {
		WP_CLI::log( "  $i: " . wp_json_encode( $f, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	}
}
WP_CLI::log( 'SOLO LECTURA: no se escribió nada' );
