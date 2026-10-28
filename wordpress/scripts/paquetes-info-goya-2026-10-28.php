<?php
/**
 * Goya (pt), 28/10/2026: "Antes de viajar" blocks on the packages page for Brazilian visitors: documents to enter
 * Argentina and minors. Written in our words from the official Argentine tourism site (argentina.travel) and the
 * government's minors page (argentina.gob.ar), as found by search on 28/10/2026 (the pages themselves could not be
 * opened from the sandbox: the owner confirms them with Migraciones before treating them as final).
 * The lodge's "RG with at most 10 years" is stated only as a practical recommendation, not as a requirement.
 * NOT included on purpose: "how to get there from Brazil" (no verified source yet) and any price.
 *
 * Idempotent: a language that already has blocks is not touched. Run with wp-script.yml (script = paquetes-info-goya-2026-10-28).
 */

$aplicar = true;
$destino = 'goya';
$hoy     = '20261028';

$bloques = [
	'pt' => [
		[
			'titulo'        => 'Documentos para entrar na Argentina (brasileiros)',
			'texto'         => implode( "\n", [
				'Para entrar como turista, o brasileiro precisa de um RG físico em bom estado de conservação ou de um passaporte válido.',
				'Versões digitais de qualquer documento (incluindo o RG digital) não são aceitas pela Migraciones, e a CNH não vale como documento de entrada.',
				'Muitos guias brasileiros recomendam um RG emitido há menos de 10 anos, com foto atual e sem rasuras. É uma recomendação prática, não um requisito que conste na fonte oficial; se o seu RG é antigo, leve o passaporte.',
			] ),
			'fuente_nombre' => 'Argentina.travel: documentação necessária para turistas brasileiros',
			'fuente_url'    => 'https://argentina.travel/pt/novidades/requisitos-para-entrar-na-argentina-documentos-necessarios',
			'consultada'    => $hoy,
		],
		[
			'titulo'        => 'Menores de idade',
			'texto'         => implode( "\n", [
				'Menores de 13 anos que não viajam com quem exerce a responsabilidade parental precisam de uma autorização para entrar na Argentina, além do documento.',
				'Confira as regras e os documentos exigidos no site oficial antes de comprar a viagem.',
			] ),
			'fuente_nombre' => 'Argentina.gob.ar: ingressar no país com um menor de idade',
			'fuente_url'    => 'https://www.argentina.gob.ar/servicio/ingresar-al-pais-con-un-menor-de-edad',
			'consultada'    => $hoy,
		],
	],
];

foreach ( $bloques as $i => $filas ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma, no se carga." );
		continue;
	}
	if ( pm_filas( 'paquetes_info', $p->ID ) ) {
		WP_CLI::log( "$destino ($i) #{$p->ID}: ya tiene bloques, no los toco." );
		continue;
	}
	WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se cargan' : 'se cargarían' ) . ' ' . count( $filas ) . ' bloques → ' . pm_url_paquetes( $destino, $i ) );
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'paquetes_info', $filas, $p->ID );
	$leidos = pm_paquetes_info( $p->ID );
	WP_CLI::log( '  VERIFIED: ' . count( $leidos ) . ' bloques leídos de vuelta, ' . count( array_filter( array_column( $leidos, 'url' ) ) ) . ' con fuente' );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
