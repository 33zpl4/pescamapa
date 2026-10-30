<?php
/**
 * Jurassic Lake guide, 30/10/2026 (owner): "Lago Strobel" in Spanish, "Jurassic Lake" in English. Changes the guide name (what the map, the cards
 * and the breadcrumbs show) and, in Spanish, the H1, the SEO title and the subtitle. The slugs stay (the URLs are already out), the English H1 keeps
 * "Jurassic Lake (Lago Strobel)". Only the exact old values are replaced. Idempotent. Dry run first.
 * Run with wp-script.yml (script = jurassic-nombre-2026-10-30).
 */

$aplicar = true;

$nuevo = [
	'es' => [
		'titulo'          => 'Lago Strobel',
		'h1'              => 'Pesca en el Lago Strobel (Jurassic Lake), Santa Cruz: reglas, permiso y cómo llegar',
		'seo_title'       => 'Pesca en el Lago Strobel: reglas, permiso y acceso',
		'bajada'          => 'El Lago Strobel, que los pescadores extranjeros conocen como Jurassic Lake, en el centro de Santa Cruz. Reglas de la temporada 2026/27, permiso de no residente y cómo se llega.',
	],
	'en' => [
		'titulo'          => 'Jurassic Lake',
	],
];

foreach ( $nuevo as $idioma => $n ) {
	$p = pm_buscar( 'pm_destino', 'jurassic-lake', $idioma );
	if ( ! $p ) {
		WP_CLI::error( "No encontré la guía jurassic-lake ($idioma)." );
	}
	if ( 'es' === $idioma ) {
		if ( mb_strlen( $n['seo_title'] ) > 60 ) {
			WP_CLI::error( 'seo_title de más de 60 caracteres.' );
		}
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": jurassic-lake ($idioma) #{$p->ID}: «{$p->post_title}» -> «{$n['titulo']}»" . ( isset( $n['h1'] ) ? '; H1, SEO title y subtítulo nuevos' : '' ) );
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_title' => $n['titulo'] ] );
	foreach ( [ 'h1', 'seo_title', 'bajada' ] as $campo ) {
		if ( isset( $n[ $campo ] ) ) {
			update_field( $campo, $n[ $campo ], $p->ID );
		}
	}
	$ok = get_the_title( $p->ID ) === $n['titulo'] && ( ! isset( $n['h1'] ) || pm_campo( 'h1', $p->ID ) === $n['h1'] );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": jurassic-lake ($idioma) se llama «" . get_the_title( $p->ID ) . '»' );
	$ok || WP_CLI::error( 'el nombre no quedó.' );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
