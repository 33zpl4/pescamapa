<?php
/**
 * Links to the three new Corrientes lodge guides (Itatí, Itá Ibaté, Esteros del Iberá), 14/11/2026, es / en / pt.
 *  1. The dorado pillar note (clave golden-dorado-guia): one paragraph right after the table of stretches ("Where do you fish golden dorado in
 *     Argentina?", the first table that mentions Itatí), listing the three guides. If no such table is found in a language the note is skipped and
 *     logged: nothing is guessed.
 *  2. Each new guide: one paragraph right after its "Para leer antes de ir" list, pointing to the other two new guides and to Paso de la Patria
 *     (same river, same rules).
 * The dorado species page needs no patch: it lists every guide whose "Especies" field has dorado (the three new ones do) and, under "Guías que
 * pescan dorado", every town with a public card. The licence hub and the price notes are not touched: they already link to guides by key.
 * Idempotent: a block whose links are already in the text is left alone. Dry run first ($aplicar = false).
 * Run with wp-script.yml (script = corrientes-enlaces-2026-11-14). Run it AFTER itati-, ita-ibate- and esteros-del-ibera-2026-11-14.
 */

$aplicar = false;

function pm_ce_guia( string $clave, string $i ): ?WP_Post {
	$ps = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	foreach ( $ps as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}
function pm_ce_nota( string $clave, string $i ): ?WP_Post {
	$ps = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	foreach ( $ps as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}
function pm_ce_enlace( string $clave, string $i ): ?array {
	$p = pm_ce_guia( $clave, $i );
	return $p ? [ get_permalink( $p->ID ), get_the_title( $p->ID ) ] : null;
}
function pm_ce_a( array $e ): string {
	return '<a href="' . esc_url( $e[0] ) . '">' . esc_html( $e[1] ) . '</a>';
}
function pm_ce_bloque_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
}

$nuevas = [ 'itati', 'ita-ibate', 'esteros-del-ibera' ];
$textos = [
	'es' => [
		'pilar' => '<strong>Lodges con guía en Corrientes:</strong> %s, con lo que publica cada operador y la fecha en que lo leímos.',
		'otros' => '<strong>Otros destinos de dorado en Corrientes:</strong> %s.',
		'y'     => ' y ',
		'leer'  => 'Para leer antes de ir',
	],
	'en' => [
		'pilar' => '<strong>Guided lodges in Corrientes:</strong> %s, with what each operator publishes and the date we read it.',
		'otros' => '<strong>Other dorado destinations in Corrientes:</strong> %s.',
		'y'     => ' and ',
		'leer'  => 'Read before you go',
	],
	'pt' => [
		'pilar' => '<strong>Lodges com guia em Corrientes:</strong> %s, com o que cada operador publica e a data em que lemos.',
		'otros' => '<strong>Outros destinos de dourado em Corrientes:</strong> %s.',
		'y'     => ' e ',
		'leer'  => 'Para ler antes de ir',
	],
];
$lista = function ( array $e, string $y ): string {
	$e = array_map( 'pm_ce_a', $e );
	return count( $e ) > 1 ? implode( ', ', array_slice( $e, 0, -1 ) ) . $y . end( $e ) : (string) reset( $e );
};

$plan = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$t = $textos[ $i ];
	$todas = [];
	foreach ( $nuevas as $c ) {
		$e = pm_ce_enlace( $c, $i );
		if ( ! $e ) {
			WP_CLI::warning( "guía {$c} ({$i}): no existe; este idioma se omite" );
			continue 2;
		}
		$todas[ $c ] = $e;
	}

	// 1. Pillar note.
	$nota = pm_ce_nota( 'golden-dorado-guia', $i );
	if ( ! $nota ) {
		WP_CLI::warning( "nota golden-dorado-guia ({$i}): no existe" );
	} elseif ( false !== strpos( $nota->post_content, $todas['itati'][0] ) ) {
		WP_CLI::log( "pilar ({$i}) #{$nota->ID}: ya enlaza a Itatí, no toco nada" );
	} else {
		preg_match_all( '#<!-- wp:table.*?<!-- /wp:table -->#su', $nota->post_content, $m, PREG_OFFSET_CAPTURE );
		$pos = null;
		foreach ( $m[0] as [ $bloque, $desde ] ) {
			if ( false !== stripos( $bloque, 'Itat' ) ) {
				$pos = $desde + strlen( $bloque );
				break;
			}
		}
		if ( null === $pos ) {
			WP_CLI::warning( "pilar ({$i}) #{$nota->ID}: no encontré la tabla de tramos con Itatí; se omite" );
		} else {
			$html = sprintf( $t['pilar'], $lista( $todas, $t['y'] ) );
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": pilar ({$i}) #{$nota->ID} {$nota->post_name}: párrafo tras la tabla (posición {$pos} de " . strlen( $nota->post_content ) . "): {$html}" );
			$plan[] = [ 'tipo' => 'nota', 'id' => $nota->ID, 'i' => $i, 'texto' => substr( $nota->post_content, 0, $pos ) . "\n\n" . rtrim( pm_ce_bloque_p( $html ) ) . substr( $nota->post_content, $pos ), 'contiene' => $todas['esteros-del-ibera'][0] ];
		}
	}

	// 2. Cross-links in each new guide.
	$paso = pm_ce_enlace( 'paso-de-la-patria', $i );
	foreach ( $nuevas as $c ) {
		$g = pm_ce_guia( $c, $i );
		$otros = array_values( array_filter( array_merge( array_diff_key( $todas, [ $c => 1 ] ), $paso ? [ 'paso' => $paso ] : [] ) ) );
		if ( false !== strpos( $g->post_content, $otros[0][0] ) ) {
			WP_CLI::log( "guía {$c} ({$i}) #{$g->ID}: ya enlaza a {$otros[0][1]}, no toco nada" );
			continue;
		}
		// After the list that follows the "Read before you go" heading.
		if ( ! preg_match( '#<h2[^>]*>' . preg_quote( $t['leer'], '#' ) . '</h2>\s*<!-- /wp:heading -->\s*<!-- wp:list.*?<!-- /wp:list -->#su', $g->post_content, $mm, PREG_OFFSET_CAPTURE ) ) {
			WP_CLI::warning( "guía {$c} ({$i}) #{$g->ID}: no encontré la sección «{$t['leer']}»; se omite" );
			continue;
		}
		$pos  = $mm[0][1] + strlen( $mm[0][0] );
		$html = sprintf( $t['otros'], $lista( $otros, $t['y'] ) );
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía {$c} ({$i}) #{$g->ID}: párrafo tras «{$t['leer']}»: {$html}" );
		$plan[] = [ 'tipo' => 'guia', 'id' => $g->ID, 'i' => $i, 'texto' => substr( $g->post_content, 0, $pos ) . "\n\n" . rtrim( pm_ce_bloque_p( $html ) ) . substr( $g->post_content, $pos ), 'contiene' => $otros[0][0] ];
	}
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada (' . count( $plan ) . ' cambios planeados)' );
	return;
}

$ok = true;
foreach ( $plan as $x ) {
	$r = wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['texto'] ) ], true );
	if ( is_wp_error( $r ) ) {
		WP_CLI::error( $r->get_error_message() );
	}
	'guia' === $x['tipo'] && update_field( 'actualizado', '20261114', $x['id'] );
}
foreach ( $plan as $x ) {
	$p  = get_post( $x['id'] );
	$bb = false !== strpos( $p->post_content, $x['contiene'] );
	WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['tipo']} ({$x['i']}) #{$x['id']} {$p->post_name} enlaza a {$x['contiene']}" );
	$ok = $ok && $bb;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
