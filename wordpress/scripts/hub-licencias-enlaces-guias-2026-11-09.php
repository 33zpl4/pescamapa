<?php
/**
 * Link from the Río Grande and Esquel y Trevelin guides (es/en/pt) to the licence hub for foreigners, 9/11/2026.
 * Why: for "argentina fishing license" Search Console showed the home page and the Patagonia licence note, not the hub (published 8/11),
 * so Google could not tell which page answers the question. A link from the pages that already appear helps it choose the hub.
 * What: one paragraph at the END of the permit section (just before the next heading), found by a heading that mentions permit / licence.
 * If no such heading is found the guide is skipped and logged: nothing is guessed. Idempotent: a guide that already links to the hub
 * is left alone. Simulacro first ($aplicar = false). Run with wp-script.yml (script = hub-licencias-enlaces-guias-2026-11-09).
 */
$aplicar = false;
$guias   = [ 'rio-grande', 'esquel-trevelin' ];
$texto   = [
	'es' => 'Todos los permisos por región, en pesos y en dólares aproximados: <a href="%s">licencia de pesca para extranjeros en Argentina 2026/27</a>.',
	'en' => 'All permits by region, in pesos and approximate dollars: <a href="%s">fishing license for foreigners in Argentina 2026/27</a>.',
	'pt' => 'Todas as licenças por região, em pesos e em dólares aproximados: <a href="%s">licença de pesca para estrangeiros na Argentina 2026/27</a>.',
];
$hubs = [];
foreach ( array_keys( $texto ) as $i ) {
	$nota = pm_buscar( 'pm_nota', 'licencias-extranjeros-argentina', $i );
	if ( ! $nota ) {
		WP_CLI::error( "No encontré el hub de licencias ($i)." );
	}
	$hubs[ $i ] = get_permalink( $nota->ID );
}
$cambios = 0;
foreach ( $guias as $clave ) {
	foreach ( $texto as $i => $plantilla ) {
		$p = pm_buscar( 'pm_destino', $clave, $i );
		if ( ! $p ) {
			WP_CLI::log( "$clave ($i): no existe, se salta." );
			continue;
		}
		$c = $p->post_content;
		if ( false !== strpos( $c, $hubs[ $i ] ) ) {
			WP_CLI::log( "$clave ($i): ya enlaza al hub, no toco nada." );
			continue;
		}
		// Heading blocks with their offsets.
		preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
		$pos = null;
		foreach ( $m[0] as $n => $bloque ) {
			if ( preg_match( '/permis|licen[cç]|permit|licen[cs]e/iu', wp_strip_all_tags( $m[1][ $n ][0] ) ) ) {
				$pos = $m[0][ $n + 1 ][1] ?? strlen( $c ); // start of the next heading, or the end of the content
				break;
			}
		}
		if ( null === $pos ) {
			WP_CLI::warning( "$clave ($i): no encontré el encabezado del permiso; se salta." );
			continue;
		}
		$parrafo = "<!-- wp:paragraph -->\n<p>" . sprintf( $plantilla, esc_url( $hubs[ $i ] ) ) . "</p>\n<!-- /wp:paragraph -->\n\n";
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) #{$p->ID}: enlace al hub al final de la sección del permiso (posición $pos de " . strlen( $c ) . ')' );
		$cambios++;
		if ( ! $aplicar ) {
			continue;
		}
		$nuevo = substr( $c, 0, $pos ) . $parrafo . substr( $c, $pos );
		$r     = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( $r->get_error_message() );
		}
		$ok = false !== strpos( get_post( $p->ID )->post_content, $hubs[ $i ] );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) enlaza a {$hubs[ $i ]}" );
		$ok || WP_CLI::error( "el enlace no quedó en $clave ($i)." );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios guías." );
