<?php
/**
 * Answer page "Pedir presupuesto a varios lodges de pesca a la vez" (20/11/2026; docs/investigacion/fondo-embudo-2026-11-19.md).
 * /pedir-presupuesto-pesca/, /pt/pedir-orcamento-pesca/, /en/get-fishing-lodge-quotes/ (theme view vistas/presupuesto.php).
 * The destinations list and the FAQ are built here from data, so the visible page, the FAQPage JSON-LD and llms.txt cannot drift.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Destinations that have a city page ("Guías de pesca en <lugar>") in this language, name and province only (no counts: owner 20/11/2026). Nothing is invented: a destination without public places is not listed.
 */
function pm_presupuesto_destinos( string $idioma ): array {
	static $cache = [];
	if ( isset( $cache[ $idioma ] ) ) {
		return $cache[ $idioma ];
	}
	$out = [];
	foreach ( pm_destinos_lista( $idioma ) as $d ) {
		$clave = (string) ( $d['clave'] ?? '' );
		if ( '' === $clave || ! pm_buscar( 'pm_destino', $clave, $idioma ) ) {
			continue;
		}
		$lugares = pm_prestadores_destino( $clave ); // public only
		if ( ! $lugares ) {
			continue;
		}
		$out[] = [
			'clave'     => $clave,
			'nombre'    => (string) ( $d['nombre'] ?? $clave ),
			'provincia' => (string) ( PM_PROVINCIAS[ $d['provincia'] ?? '' ] ?? '' ),
			'url'       => pm_url_guias( $clave, $idioma ),
		];
	}
	return $cache[ $idioma ] = $out;
}

/** "Goya, Esquina y Paso de la Patria" in the language of the page. */
function pm_presupuesto_lista_nombres( array $destinos ): string {
	$nombres = array_column( $destinos, 'nombre' );
	if ( count( $nombres ) < 2 ) {
		return (string) ( $nombres[0] ?? '' );
	}
	return implode( ', ', array_slice( $nombres, 0, -1 ) ) . pm_t( 'guias.cta.y' ) . end( $nombres );
}

/** FAQ of the page as [pregunta, respuesta] pairs (visible list and FAQPage JSON-LD read this). The destinations question only exists with destinations. */
function pm_presupuesto_faqs(): array {
	$faqs = [];
	foreach ( [ 1, 2, 3, 4, 5 ] as $n ) {
		$faqs[] = [ 'pregunta' => pm_t( "pres.faq.$n.q" ), 'respuesta' => pm_t( "pres.faq.$n.a" ) ];
	}
	$destinos = pm_presupuesto_destinos( pm_lang() );
	if ( $destinos ) {
		$faqs[] = [ 'pregunta' => pm_t( 'pres.faq.6.q' ), 'respuesta' => pm_t( 'pres.faq.6.a', [ 'destinos' => pm_presupuesto_lista_nombres( $destinos ) ] ) ];
	}
	return $faqs;
}
