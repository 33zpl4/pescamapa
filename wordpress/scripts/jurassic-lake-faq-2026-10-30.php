<?php
/**
 * Jurassic Lake guide (es/en), 30/10/2026: rewrite the answer about fishing near the mouth of the Barrancoso. Owner: 100 m from the
 * mouth is still "near the mouth"; the beats are fished respecting the minimum legal distance, there is nothing to hide or to hedge.
 * Replaces only the FAQ row whose question matches; leaves every other field alone. Idempotent. Dry run first.
 * Run with wp-script.yml (script = jurassic-lake-faq-2026-10-30).
 */

$aplicar = false;

$nuevo = [
	'en' => [
		'viejas' => [ 'Can I fish at the mouth of the Barrancoso?' ],
		'pregunta' => 'Can I fish near the mouth of the Barrancoso?',
		'respuesta' => 'Yes, but not right at it. Annex I sets a minimum distance from river mouths: 100 m for the Barrancoso\'s own source and mouth, and 200 m on both banks around the mouths of the rivers linked to Lago Strobel. The lodges describe their beats as next to the mouth and work respecting that minimum legal distance. Ask your operator which sectors are open on your dates.',
	],
	'es' => [
		'viejas' => [ '¿Se puede pescar en la desembocadura del Barrancoso?' ],
		'pregunta' => '¿Se puede pescar cerca de la desembocadura del Barrancoso?',
		'respuesta' => 'Sí, pero no encima. El Anexo I fija una distancia mínima de las desembocaduras: 100 m desde las nacientes y la desembocadura del propio Barrancoso, y 200 m sobre ambas márgenes alrededor de las desembocaduras de los ríos vinculados al Strobel. Los lodges describen sus sectores como cercanos a la boca y trabajan respetando esa distancia mínima legal. Preguntale al operador qué sectores están habilitados en tus fechas.',
	],
];

foreach ( $nuevo as $idioma => $n ) {
	$p = pm_buscar( 'pm_destino', 'jurassic-lake', $idioma );
	if ( ! $p ) {
		WP_CLI::error( "No encontré la guía jurassic-lake ($idioma)." );
	}
	$filas = pm_filas( 'faqs', $p->ID );
	$hecho = false;
	foreach ( $filas as $k => $f ) {
		if ( in_array( $f['pregunta'] ?? '', $n['viejas'], true ) ) {
			$filas[ $k ] = [ 'pregunta' => $n['pregunta'], 'respuesta' => $n['respuesta'] ];
			$hecho = true;
		} elseif ( ( $f['pregunta'] ?? '' ) === $n['pregunta'] ) {
			$hecho = 'ya';
		}
	}
	if ( 'ya' === $hecho ) {
		WP_CLI::log( "jurassic-lake ($idioma): la respuesta nueva ya está." );
		continue;
	}
	if ( ! $hecho ) {
		WP_CLI::error( "jurassic-lake ($idioma): no encontré la pregunta vieja." );
	}
	WP_CLI::log( "jurassic-lake ($idioma) #{$p->ID}: " . ( $aplicar ? 'se reescribe' : 'se reescribiría' ) . " la pregunta sobre la boca del Barrancoso ({$n['pregunta']}); " . count( $filas ) . ' preguntas en total' );
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'faqs', $filas, $p->ID );
	$leida = pm_filas( 'faqs', $p->ID );
	$ok    = count( $leida ) === count( $filas ) && in_array( $n['pregunta'], array_column( $leida, 'pregunta' ), true );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": jurassic-lake ($idioma) " . count( $leida ) . ' preguntas, la nueva está' );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
