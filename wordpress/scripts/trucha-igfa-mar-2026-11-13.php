<?php
/**
 * Trout species page (es/en/pt), 13/11/2026: adds to the IGFA paragraph of the "Tamaños" section (surubi-trucha-tamanos-2026-11-13) that the IGFA
 * lists the brown trout under one name, "Trout, brown (Salmo trutta)", whose alternative names include "sea trout", with no separate category for
 * the sea form. Read on the IGFA page (research folder fuentes-2-3-20261113-2217, file 03: header "also called … sea trout …", one list of
 * all-tackle, length and line-class records, no sea-run category).
 * NOT added, on purpose: the Giese adult means (length 66.5 / 62.8 / 57.5 cm, weight 3.8 / 4.0 / 3.2 kg). The repository page and the thesis PDF
 * (https://rdi.uncoma.edu.ar/bitstreams/…/download) answer the runner with an anti-robot page; no file we could open contains those numbers.
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = trucha-igfa-mar-2026-11-13).
 */
$aplicar = true;
$hoy     = '20261113';

$t = [
	'es' => [
		'ancla' => 'Ninguno es de la Patagonia (IGFA, leído el 13/11/2026).',
		'nuevo' => ' La IGFA lista el récord de la trucha marrón bajo un solo nombre, «Trout, brown (Salmo trutta)», que incluye «sea trout» entre sus nombres alternativos, sin una categoría aparte para la forma de mar.',
	],
	'en' => [
		'ancla' => 'None is from Patagonia (IGFA, read 13/11/2026).',
		'nuevo' => ' The IGFA lists the brown trout record under a single name, “Trout, brown (Salmo trutta)”, which includes “sea trout” among its alternative names, with no separate category for the sea form.',
	],
	'pt' => [
		'ancla' => 'Nenhum é da Patagônia (IGFA, lido em 13/11/2026).',
		'nuevo' => ' A IGFA lista o recorde da truta marrom sob um único nome, «Trout, brown (Salmo trutta)», que inclui «sea trout» entre seus nomes alternativos, sem uma categoria à parte para a forma marinha.',
	],
];

$mal = 0;
foreach ( $t as $i => $x ) {
	$p = pm_buscar( 'pm_especie', 'trucha', $i );
	if ( ! $p ) {
		WP_CLI::warning( "trucha ($i): no existe" );
		$mal++;
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, trim( $x['nuevo'] ) ) ) {
		WP_CLI::log( "· trucha ($i) #{$p->ID}: ya estaba" );
		continue;
	}
	if ( 1 !== substr_count( $c, $x['ancla'] ) ) {
		WP_CLI::warning( "trucha ($i): no encontré (una vez) la frase «{$x['ancla']}»; no toco nada" );
		$mal++;
		continue;
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": trucha ($i) #{$p->ID}: «{$x['ancla']}» + «" . trim( $x['nuevo'] ) . '»' );
	if ( ! $aplicar ) {
		continue;
	}
	$nuevo = str_replace( $x['ancla'], $x['ancla'] . $x['nuevo'], $c );
	pm_sc_guardar_t( $p->ID, $nuevo, trim( $x['nuevo'] ) );
	update_field( 'actualizado', $hoy, $p->ID );
	update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "VERIFIED: trucha ($i) #{$p->ID} " . get_permalink( $p->ID ) );
}

function pm_sc_guardar_t( int $id, string $nuevo, string $debe ): void {
	$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $nuevo ) ], true );
	if ( is_wp_error( $r ) ) {
		WP_CLI::error( 'trucha: ' . $r->get_error_message() );
	}
	if ( false === strpos( get_post( $id )->post_content, $debe ) ) {
		WP_CLI::error( 'trucha: la lectura de vuelta no contiene la frase nueva.' );
	}
}
$mal && WP_CLI::error( "$mal avisos: revisar." );
WP_CLI::success( $aplicar ? 'Aplicado.' : 'Simulacro terminado.' );
