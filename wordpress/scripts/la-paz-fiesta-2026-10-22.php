<?php
/**
 * 22/10/2026: La Paz (Entre Ríos). The owner sent the official poster of the 33.ª Fiesta
 * Provincial del Dorado Entrerriano (25 y 26 de septiembre de 2026; 25/09 XII Encuentro de
 * pesca con señuelos, 26/09 XXXIII Encuentro de pesca con carnada). Replaces the "dates not
 * found" paragraph, the hidden PENDIENTE note about the festival, the season row and the FAQ
 * answer in the Spanish and Portuguese guides. Wording per the owner: "destaca su fiesta",
 * without ruling out other festivals. Idempotent (skips a guide that already has the text).
 * Dry run first ($aplicar = false); run with wp-script.yml (script = la-paz-fiesta-2026-10-22).
 */

$aplicar = false;

$lang = [
	'es' => [
		'viejo' => 'La Paz tiene fiestas y torneos de pesca embarcada con devolución. No encontramos publicadas las fechas de la próxima edición: las anuncian la <a href="https://lapaz.gob.ar/" rel="noopener">Municipalidad de La Paz</a> y <a href="https://lapazentrerios.tur.ar/" rel="noopener">Turismo La Paz</a>.',
		'nuevo' => 'Destaca la <strong>Fiesta Provincial del Dorado Entrerriano</strong>: en 2026 fue su 33.ª edición, el 25 y 26 de septiembre, con el 12.º Encuentro de pesca con señuelos (el 25) y el 33.º Encuentro de pesca con carnada (el 26). La Paz tiene además otras fiestas y torneos. Las fechas de la próxima edición todavía no están publicadas: las anuncian la <a href="https://lapaz.gob.ar/" rel="noopener">Municipalidad de La Paz</a> y <a href="https://lapazentrerios.tur.ar/" rel="noopener">Turismo La Paz</a>.',
		'pregunta' => '¿Cuándo es la fiesta de pesca de La Paz?',
		'respuesta' => 'Destaca la Fiesta Provincial del Dorado Entrerriano. En 2026 fue su 33.ª edición, el 25 y 26 de septiembre: el 25, el 12.º Encuentro de pesca con señuelos, y el 26, el 33.º Encuentro de pesca con carnada, con premios y sorteos por más de $40.000.000 según el afiche oficial. La fecha de la próxima edición todavía no está publicada, y La Paz tiene además otras fiestas y torneos.',
		'meses' => 'Septiembre',
		'que' => 'Fiesta Provincial del Dorado Entrerriano (33.ª edición en 2026: 25 y 26 de septiembre). Otras fiestas y torneos: fechas sin confirmar.',
	],
	'pt' => [
		'viejo' => 'La Paz tem festas e torneios de pesca embarcada com devolução. Não encontramos publicadas as datas da próxima edição: quem as anuncia é a <a href="https://lapaz.gob.ar/" rel="noopener">Prefeitura de La Paz</a> e o <a href="https://lapazentrerios.tur.ar/" rel="noopener">Turismo La Paz</a>.',
		'nuevo' => 'Destaca-se a <strong>Festa Provincial do Dourado Entrerriano</strong>: em 2026 foi a 33.ª edição, em 25 e 26 de setembro, com o 12.º Encontro de pesca com iscas artificiais (dia 25) e o 33.º Encontro de pesca com isca natural (dia 26). La Paz tem também outras festas e torneios. As datas da próxima edição ainda não foram publicadas: quem as anuncia é a <a href="https://lapaz.gob.ar/" rel="noopener">Prefeitura de La Paz</a> e o <a href="https://lapazentrerios.tur.ar/" rel="noopener">Turismo La Paz</a>.',
		'pregunta' => 'Quando é a festa de pesca de La Paz?',
		'respuesta' => 'Destaca-se a Festa Provincial do Dourado Entrerriano. Em 2026 foi a 33.ª edição, em 25 e 26 de setembro: dia 25, o 12.º Encontro de pesca com iscas artificiais, e dia 26, o 33.º Encontro de pesca com isca natural, com prêmios e sorteios de mais de $40.000.000 (pesos argentinos) segundo o cartaz oficial. A data da próxima edição ainda não foi publicada, e La Paz tem também outras festas e torneios.',
		'meses' => 'Setembro',
		'que' => 'Festa Provincial do Dourado Entrerriano (33.ª edição em 2026: 25 e 26 de setembro). Outras festas e torneios: datas não confirmadas.',
	],
];

$tocadas = 0;
foreach ( pm_posts( 'pm_destino' ) as $p ) {
	if ( 'la-paz' !== pm_clave_de( $p->ID ) ) {
		continue;
	}
	$i = pm_idioma_de( $p->ID );
	if ( ! isset( $lang[ $i ] ) ) {
		continue;
	}
	$t = $lang[ $i ];
	$contenido = (string) $p->post_content;
	if ( false !== strpos( $contenido, '33.ª' ) ) {
		WP_CLI::log( "la-paz ($i): ya tiene el texto de la fiesta, no toco nada." );
		continue;
	}
	if ( false === strpos( $contenido, $t['viejo'] ) ) {
		WP_CLI::warning( "la-paz ($i): no encuentro el párrafo viejo; no toco nada." );
		continue;
	}
	$nuevo = str_replace( $t['viejo'], $t['nuevo'], $contenido );
	// Drop the hidden internal note about the festival name/date (a whole quote block).
	$nuevo = preg_replace( '#<!-- wp:quote -->\s*<blockquote class="wp-block-quote"><!-- wp:paragraph -->\s*<p>PENDIENTE: nombre oficial y fecha de la fiesta emblema[^<]*</p>\s*<!-- /wp:paragraph --></blockquote>\s*<!-- /wp:quote -->\s*#u', '', $nuevo );
	$faqs = pm_filas( 'faqs', $p->ID );
	$hay  = false;
	foreach ( $faqs as $k => $f ) {
		if ( ( $f['pregunta'] ?? '' ) === $t['pregunta'] ) {
			$faqs[ $k ]['respuesta'] = $t['respuesta'];
			$hay = true;
		}
	}
	$temporada = pm_filas( 'temporada', $p->ID );
	if ( $temporada ) {
		$temporada[0] = [ 'meses' => $t['meses'], 'que' => $t['que'] ];
	}
	WP_CLI::log( sprintf( 'la-paz (%s) #%d: párrafo %s, nota PENDIENTE %s, pregunta %s, temporada %s', $i, $p->ID, 'sí', false === strpos( $nuevo, 'nombre oficial y fecha de la fiesta' ) ? 'quitada' : 'sigue', $hay ? 'sí' : 'NO', $temporada ? 'sí' : 'NO' ) );
	$tocadas++;
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ] );
	update_field( 'faqs', $faqs, $p->ID );
	if ( $temporada ) {
		update_field( 'temporada', $temporada, $p->ID );
	}
	update_field( 'actualizado', '20261022', $p->ID );
	$leido = get_post( $p->ID )->post_content;
	$rows  = pm_filas( 'faqs', $p->ID );
	WP_CLI::log( '  VERIFIED: texto nuevo ' . ( false !== strpos( $leido, '33.ª' ) ? 'sí' : 'NO' ) . '; nota vieja ' . ( false === strpos( $leido, 'nombre oficial y fecha de la fiesta' ) ? 'fuera' : 'SIGUE' ) . '; preguntas ' . count( $rows ) . ' (antes ' . count( $faqs ) . ')' );
}
WP_CLI::log( "$tocadas guía(s) a actualizar. " . ( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' ) );
