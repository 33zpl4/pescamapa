<?php
/**
 * Pescamapa theme: rendering only. Everything about data, languages, URLs
 * and publication rules is in the pescamapa-core plugin; without it the
 * theme shows a maintenance notice instead of failing.
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'style', 'script', 'figure', 'caption' ] );
	add_theme_support( 'responsive-embeds' );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	$ver = fn( $f ) => (string) filemtime( "$dir/$f" );

	wp_enqueue_style( 'pescamapa', "$uri/assets/pescamapa.css", [], $ver( 'assets/pescamapa.css' ) );
	wp_enqueue_style( 'pescamapa-extra', "$uri/assets/extra.css", [ 'pescamapa' ], $ver( 'assets/extra.css' ) );
	wp_register_style( 'leaflet', "$uri/assets/leaflet/leaflet.css", [], '1.9.4' );
	wp_register_script( 'leaflet', "$uri/assets/leaflet/leaflet.js", [], '1.9.4', [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_register_script( 'pm-mapa', "$uri/assets/mapa.js", [ 'leaflet' ], $ver( 'assets/mapa.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );

	wp_register_script( 'pm-pique', "$uri/assets/pique.js", [ 'leaflet' ], $ver( 'assets/pique.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );
	wp_register_script( 'pm-calendario', "$uri/assets/calendario.js", [], $ver( 'assets/calendario.js' ), [ 'in_footer' => true, 'strategy' => 'defer' ] );

	if ( function_exists( 'pm_vista' ) && in_array( pm_vista(), [ 'inicio', 'mapa', 'destino' ], true ) ) {
		wp_enqueue_style( 'leaflet' );
		wp_enqueue_script( 'pm-mapa' );
	}
	// No emoji script, no classic theme block styles we don't use.
	wp_dequeue_style( 'classic-theme-styles' );
} );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

add_action( 'wp_head', function () {
	echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() . '/assets/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
	$fuente = get_template_directory_uri() . '/assets/fonts/';
	foreach ( [ 'playfair-display-latin-wght-normal.woff2', 'outfit-latin-wght-normal.woff2', 'bebas-neue-latin-400-normal.woff2' ] as $f ) {
		echo '<link rel="preload" href="' . esc_url( $fuente . $f ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
	}
}, 1 );

/** Escaped interface string. */
function pm_e( string $clave, array $vars = [] ): void {
	echo esc_html( pm_t( $clave, $vars ) );
}

/**
 * Microcopy under every primary button that leads to the trip form (owner, 20/11/2026): the lime «Gratis para vos» pill
 * plus "reply in 24-48 h, same price as booking direct". One helper so the wording lives in ui.json (cta.gratis / cta.resto).
 * $texto: ui key for the second part (the answer page closing passes viaje.garantia, which already says it all).
 */
function pm_cta_nota( string $texto = 'cta.resto' ): void {
	echo '<span class="cta-gratis"><span class="gratis-pill">' . esc_html( pm_t( 'cta.gratis' ) ) . '</span> <span class="gratis-resto">' . esc_html( pm_t( $texto ) ) . '</span></span>';
}

/** Region · province pill text. */
function pm_pill( string $region, string $provincia ): string {
	$r = PM_REGIONES[ $region ][ pm_lang() ] ?? $region;
	$p = PM_PROVINCIAS[ $provincia ] ?? $provincia;
	return mb_strtoupper( "$r · $p" );
}
