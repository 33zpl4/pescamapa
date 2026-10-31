<?php
/**
 * Fix on the live note "Fly fishing Argentina on a budget" (en), 31/10/2026: it said "tell us in the form below", but a note page has
 * no form at the bottom. The sentence now points to the trip form (same link as the call to action). Exact-fragment replace: if the
 * fragment is not found exactly once, nothing is written. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = nota-por-tu-cuenta-ajuste-2026-10-31).
 */

$aplicar = true;
$viaje   = esc_url( ( function_exists( 'pm_url_viaje' ) ? pm_url_viaje( 'en' ) : home_url( '/en/plan-your-trip/' ) ) . '?destino=bariloche&especie=trucha' );
$antes   = 'If a rate has changed, tell us in the form below and we will update this note.';
$despues = 'If a rate has changed, tell us through <a href="' . $viaje . '">the trip form</a> and we will update this note.';

$notas = array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'patagonia-por-tu-cuenta', 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === 'en' );
if ( 1 !== count( $notas ) ) {
	WP_CLI::error( 'no encontré exactamente una nota patagonia-por-tu-cuenta (en).' );
}
$nota = array_values( $notas )[0];
$c    = $nota->post_content;
$n    = substr_count( $c, $antes );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": nota #{$nota->ID}: el fragmento aparece $n vez/veces" );
if ( 1 !== $n ) {
	WP_CLI::log( 'No se escribe nada.' );
	return;
}
if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado.' );
	return;
}
wp_update_post( [ 'ID' => $nota->ID, 'post_content' => wp_slash( str_replace( $antes, $despues, $c ) ) ] );
$leido = get_post_field( 'post_content', $nota->ID );
$ok    = false === strpos( $leido, $antes ) && false !== strpos( $leido, 'the trip form</a> and we will update this note.' );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota #{$nota->ID} " . get_permalink( $nota->ID ) );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
WP_CLI::success( 'Nota corregida.' );
