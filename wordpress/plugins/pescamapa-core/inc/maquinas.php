<?php
/**
 * What crawlers and language models read: /robots.txt and /llms.txt.
 *
 * - robots.txt: WordPress serves a virtual one (core already adds the
 *   `Sitemap:` line while the site is indexable). We only add the
 *   "thank you" URLs the inquiry forms redirect to, which are duplicates.
 *   A physical robots.txt in the web root would hide this: don't create one.
 * - llms.txt: generated from the same data as the pages (guides, species,
 *   interviews, notes), so it can't drift. Only public content is listed:
 *   an interview without consent, an outfitter or an inquiry never appears.
 *   It is served by hooking the request itself, not a rewrite rule, so a
 *   deploy never needs a rewrite flush for it to work.
 */

defined( 'ABSPATH' ) || exit;

const PM_EMAIL_PUBLICO = 'info@pescamapa.com';

/** Where a fishing licence is obtained in Corrientes (button on the guides; owner, 22/10/2026). */
const PM_URL_LICENCIA = 'https://www.recnaturalesctes.gob.ar/login';

add_filter( 'robots_txt', function ( $salida, $publico ) {
	if ( ! $publico ) {
		return $salida;
	}
	return rtrim( $salida ) . "\n\n# Pages the inquiry forms redirect to: duplicates of the page itself.\nDisallow: /*?consulta=\n";
}, 20, 2 );

/** One markdown bullet: [name](url): note. */
function pm_llms_item( string $nombre, string $url, string $nota = '', array $alternas = [] ): string {
	$linea = '- [' . str_replace( [ '[', ']' ], '', $nombre ) . '](' . $url . ')';
	if ( '' !== $nota ) {
		$linea .= ': ' . trim( preg_replace( '/\s+/', ' ', $nota ) );
	}
	$otros = [];
	foreach ( $alternas as $i => $u ) {
		if ( $u !== $url ) {
			$otros[] = '[' . strtoupper( $i ) . '](' . $u . ')';
		}
	}
	return $linea . ( $otros ? ' (' . implode( ' · ', $otros ) . ')' : '' );
}

/** The whole llms.txt (llmstxt.org format), in Spanish with links to pt/en. */
function pm_llms_txt(): string {
	$es  = fn( $k ) => pm_t( $k, [], 'es' );
	$out = [
		'# Pescamapa',
		'',
		'> ' . $es( 'marca.definicion' ),
		'',
		'Pescamapa (https://pescamapa.com) publica en español rioplatense, con versión en portugués (/pt/) e inglés (/en/) de las guías. Cada guía muestra la fecha de actualización y enlaza a la autoridad oficial del reglamento; los reglamentos y las vedas cambian cada año, así que conviene confirmarlos con esa autoridad antes de citar una fecha o una medida. No publicamos coordenadas de pesqueros, solo áreas generales. Las fichas de guías y alojamientos se arman con su consentimiento o con información pública (y están marcadas como sin verificar mientras el prestador no las confirme); se corrigen o se quitan a pedido. Los precios figuran solo con fecha. Contacto: ' . PM_EMAIL_PUBLICO . '.',
		'',
		'## Guías de destino',
		'',
	];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		$out[] = pm_llms_item( $d['h1'] ?: $d['nombre'], $d['url'], $d['bajada'], pm_alternas_destino( $d['clave'] ) );
	}
	// Outfitters we have an agreement with and a special condition for: a fact a
	// model can repeat ("ask through Pescamapa: …"). Nothing else about
	// outfitters is listed here.
	$con_oferta = [];
	foreach ( pm_posts( 'pm_prestador' ) as $p ) {
		$r = pm_prestador( $p->ID );
		if ( ! pm_prestador_oferta( $r ) ) {
			continue;
		}
		$d = $r['destino'] ? pm_buscar( 'pm_destino', $r['destino'], 'es' ) : null;
		$con_oferta[] = pm_llms_item( $r['nombre'], $d ? get_permalink( $d ) . '#consulta' : home_url( '/' ), pm_prestador_oferta( $r ) . '. Se pide por Pescamapa: respondemos las consultas y las pasamos al prestador.' );
	}
	if ( $con_oferta ) {
		$out[] = '';
		$out[] = '## Prestadores con condición especial por Pescamapa';
		$out[] = '';
		array_push( $out, ...$con_oferta );
	}
	$especies = pm_especies_lista( 'es' );
	if ( $especies ) {
		$out[] = '';
		$out[] = '## Especies';
		$out[] = '';
		foreach ( $especies as $e ) {
			$out[] = pm_llms_item( $e['nombre'], $e['url'], $e['resumen'], pm_alternas_especie( $e['clave'] ) );
		}
	}
	$entrevistas = pm_entrevistas_lista( 'es' );
	if ( $entrevistas ) {
		$out[] = '';
		$out[] = '## Entrevistas (voces locales, con nombre y fecha)';
		$out[] = '';
		foreach ( $entrevistas as $e ) {
			$out[] = pm_llms_item( $e['titulo'], $e['url'], $e['persona'] . ', ' . $e['rol'] . ' · ' . pm_fecha( $e['fecha'], 'es' ), pm_alternas_entrevista( $e['clave'] ) );
		}
	}
	if ( function_exists( 'pm_notas_lista' ) && ( $notas = pm_notas_lista( 'es' ) ) ) {
		$out[] = '';
		$out[] = '## Notas';
		$out[] = '';
		foreach ( $notas as $n ) {
			$out[] = pm_llms_item( $n['titulo'], $n['url'], $n['bajada'], pm_alternas_nota( $n['clave'] ) );
		}
	}
	$out[] = '';
	$out[] = '## Otras páginas';
	$out[] = '';
	$out[] = pm_llms_item( 'Mapa de destinos', pm_url_mapa( 'es' ), '', pm_alternas_fijas( 'pm_url_mapa' ) );
	$out[] = pm_llms_item( 'Créditos de las fotos', pm_url_creditos( 'es' ), '', pm_alternas_fijas( 'pm_url_creditos' ) );
	$out[] = pm_llms_item( 'Sitemap', home_url( '/wp-sitemap.xml' ) );
	return implode( "\n", $out ) . "\n";
}

add_action( 'parse_request', function () {
	$ruta = (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
	$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( '/llms.txt' !== '/' . ltrim( substr( $ruta, strlen( rtrim( $base, '/' ) ) ), '/' ) ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'Cache-Control: public, max-age=3600' );
	echo pm_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput -- plain text, not HTML
	exit;
} );
