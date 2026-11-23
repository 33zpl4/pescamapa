<?php
/**
 * Río Gallegos guide (es, en, pt): add El Rincón Lodge and fix the sentences that no longer matched what the listed places are today, 23/11/2026.
 * Source of El Rincón's facts: only what wordpress/scripts/el-rincon-lodge-2026-11-23.php already publishes (Estancia Rincón de los Morros, 1 h 40 min by
 * vehicle from the city of Río Gallegos, 20 km of the Gallegos and 35 km of the Penitente on both banks, original 1902 main house on the Penitente with
 * 4 single and 2 double rooms, main sea-trout run mid-December to end of April according to the lodge). No person, contact or rate is written here.
 *
 * What changes (replace-by-sentence, idempotent; any "falta" aborts the write):
 *  - "Lodges y operadores" paragraph: Karku "asked not to publish" sentence becomes what WE do (we ask for the rates of Karku, River House and El Rincón
 *    for the reader's dates); one new El Rincón sentence group after the River House sentence.
 *  - "Cómo llegar" paragraph and the "¿Cómo llego?" FAQ: El Rincón's access as the lodge states it (no transfers claimed).
 *  - The cost FAQ: same "we ask for the rates" sentence replaces the Karku-only one.
 * Regulation, season, licence and source content are NOT touched. Field `actualizado` is set.
 * Dry run first ($aplicar = false). Run with wp-script.yml (script = rio-gallegos-el-rincon-2026-11-23).
 */

$aplicar = false;
$hoy     = '20261123';

function pm_rr_cambiar( string $texto, string $viejo, string $nuevo ): array {
	if ( false !== strpos( $texto, $nuevo ) ) {
		return [ $texto, 'ya' ];
	}
	if ( 1 !== substr_count( $texto, $viejo ) ) {
		return [ $texto, 'falta' ];
	}
	return [ str_replace( $viejo, $nuevo, $texto ), 'cambia' ];
}

$P_COSTO = [
	'es' => '¿Cuánto cuesta una semana en un lodge del Gallegos?',
	'en' => 'How much does a week at a Gallegos lodge cost?',
	'pt' => 'Quanto custa uma semana em um lodge do Gallegos?',
];
$P_LLEGO = [ 'es' => '¿Cómo llego?', 'en' => 'How do I get there?', 'pt' => 'Como chego?' ];

$guias = [
	'es' => [
		'cuerpo' => [
			[ 'Karku Fly Fishing pidió no publicar la suya y la da a quien consulta por una cotización. Solid Adventures ofrece además',
			  'Karku Fly Fishing, River House y El Rincón Lodge trabajan con nosotros: te pedimos su tarifa para tus fechas y te la mandamos junto con las otras opciones. Solid Adventures ofrece además' ],
			[ '(cañas, reels, líneas, waders y botas), según el operador.',
			  '(cañas, reels, líneas, waders y botas), según el operador. El Rincón Lodge está en la Estancia Rincón de los Morros, a 1 h 40 min en vehículo de la ciudad de Río Gallegos. Según el lodge, da acceso a 20 km del río Gallegos y 35 km del río Penitente, en ambas márgenes, y su casa principal original, de 1902, está sobre el Penitente, con 4 habitaciones individuales y 2 dobles; ubica la entrada principal de trucha marina de mediados de diciembre a fines de abril.' ],
			[ 'Son los datos de cada operador o directorio; no verificamos las distancias.',
			  'El Rincón Lodge dice que su estancia está a 1 h 40 min en vehículo de la ciudad de Río Gallegos. Son los datos de cada operador o directorio; no verificamos las distancias.' ],
		],
		'faqs' => [
			$P_COSTO['es'] => [ 'Karku Fly Fishing pidió no publicar su tarifa: la da a quien consulta por una cotización.',
				'Las tarifas de Karku Fly Fishing, River House y El Rincón Lodge te las pedimos nosotros para tus fechas.' ],
			$P_LLEGO['es'] => [ 'Los Morros Lodge figura en un directorio local',
				'El Rincón Lodge informa que su estancia está a 1 h 40 min en vehículo de la ciudad de Río Gallegos. Los Morros Lodge figura en un directorio local' ],
		],
		'viejas' => [ 'Karku Fly Fishing pidió no publicar la suya', 'Karku Fly Fishing pidió no publicar su tarifa: la da' ],
	],
	'en' => [
		'cuerpo' => [
			[ 'Karku Fly Fishing asked us not to publish its own and gives it to anglers who ask for a quote. Solid Adventures also offers',
			  'Karku Fly Fishing, River House and El Rincón Lodge work with us: we ask for their rates for your dates and send them to you with the other options. Solid Adventures also offers' ],
			[ '(rods, reels, lines, waders and boots), according to the operator.',
			  '(rods, reels, lines, waders and boots), according to the operator. El Rincón Lodge is at Estancia Rincón de los Morros, 1 h 40 min by vehicle from the city of Río Gallegos. According to the lodge, it gives access to 20 km of the Río Gallegos and 35 km of the Río Penitente, on both banks, and its original 1902 main house stands on the Penitente, with 4 single rooms and 2 doubles; it places the main sea-trout run from mid-December to the end of April.' ],
			[ 'These are each operator\'s or directory\'s figures; we have not verified the distances.',
			  'El Rincón Lodge says its estancia is 1 h 40 min by vehicle from the city of Río Gallegos. These are each operator\'s or directory\'s figures; we have not verified the distances.' ],
		],
		'faqs' => [
			$P_COSTO['en'] => [ 'Karku Fly Fishing asked us not to publish its rate: it gives it to anglers who ask for a quote.',
				'We ask Karku Fly Fishing, River House and El Rincón Lodge for their rates for your dates.' ],
			$P_LLEGO['en'] => [ 'Los Morros Lodge appears in a local directory',
				'El Rincón Lodge reports that its estancia is 1 h 40 min by vehicle from the city of Río Gallegos. Los Morros Lodge appears in a local directory' ],
		],
		'viejas' => [ 'Karku Fly Fishing asked us not to publish its own', 'Karku Fly Fishing asked us not to publish its rate' ],
	],
	'pt' => [
		'cuerpo' => [
			[ 'a Karku Fly Fishing pediu que não publiquemos a sua e informa a quem pede uma cotação. A Solid Adventures oferece também',
			  'a Karku Fly Fishing, a River House e o El Rincón Lodge trabalham conosco: pedimos a tarifa deles para as suas datas e enviamos junto com as outras opções. A Solid Adventures oferece também' ],
			[ '(varas, reels, linhas, waders e botas), segundo o operador.',
			  '(varas, reels, linhas, waders e botas), segundo o operador. O El Rincón Lodge fica na Estancia Rincón de los Morros, a 1 h 40 min de veículo da cidade de Río Gallegos. Segundo o lodge, dá acesso a 20 km do rio Gallegos e 35 km do rio Penitente, nas duas margens, e sua casa principal original, de 1902, fica no Penitente, com 4 quartos individuais e 2 duplos; situa a entrada principal de truta marinha de meados de dezembro ao fim de abril.' ],
			[ 'São os dados de cada operador ou diretório; não verificamos as distâncias.',
			  'O El Rincón Lodge diz que sua estância fica a 1 h 40 min de veículo da cidade de Río Gallegos. São os dados de cada operador ou diretório; não verificamos as distâncias.' ],
		],
		'faqs' => [
			$P_COSTO['pt'] => [ 'A Karku Fly Fishing pediu que não publiquemos a sua tarifa: informa a quem pede uma cotação.',
				'As tarifas da Karku Fly Fishing, da River House e do El Rincón Lodge nós pedimos para as suas datas.' ],
			$P_LLEGO['pt'] => [ 'O Los Morros Lodge consta num diretório local',
				'O El Rincón Lodge informa que sua estância fica a 1 h 40 min de veículo da cidade de Río Gallegos. O Los Morros Lodge consta num diretório local' ],
		],
		'viejas' => [ 'pediu que não publiquemos a sua e informa', 'pediu que não publiquemos a sua tarifa: informa' ],
	],
];

$plan = [];
foreach ( $guias as $i => $g ) {
	$p = pm_buscar( 'pm_destino', 'rio-gallegos', $i );
	if ( ! $p ) {
		WP_CLI::error( "guía rio-gallegos ($i): no existe" );
	}
	$texto   = $p->post_content;
	$estados = [];
	foreach ( $g['cuerpo'] as $n => [ $viejo, $nuevo ] ) {
		[ $texto, $e ] = pm_rr_cambiar( $texto, $viejo, $nuevo );
		$estados[]     = "cuerpo #$n: $e";
		'cambia' === $e && WP_CLI::log( "  guía ($i) cuerpo #$n\n      ANTES:   $viejo\n      DESPUÉS: $nuevo" );
	}
	$filas = pm_filas( 'faqs', $p->ID );
	foreach ( $g['faqs'] as $pregunta => [ $viejo, $nuevo ] ) {
		$hallada = false;
		foreach ( $filas as $k => $f ) {
			if ( ( $f['pregunta'] ?? '' ) === $pregunta ) {
				$hallada = true;
				[ $filas[ $k ]['respuesta'], $e ] = pm_rr_cambiar( (string) $f['respuesta'], $viejo, $nuevo );
				$estados[] = 'FAQ «' . mb_substr( $pregunta, 0, 40 ) . "»: $e";
				'cambia' === $e && WP_CLI::log( "  guía ($i) FAQ «{$pregunta}»\n      ANTES:   $viejo\n      DESPUÉS: $nuevo" );
			}
		}
		$hallada || $estados[] = 'FAQ «' . mb_substr( $pregunta, 0, 40 ) . '»: falta (pregunta no encontrada)';
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía rio-gallegos ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' | ' . implode( ' | ', $estados ) );
	foreach ( $estados as $e ) {
		if ( false !== strpos( $e, 'falta' ) ) {
			WP_CLI::warning( "guía ($i): $e" );
		}
	}
	$plan[ $i ] = [ 'id' => $p->ID, 'texto' => $texto, 'filas' => $filas, 'estados' => $estados ];
}

foreach ( $plan as $i => $x ) {
	foreach ( $x['estados'] as $e ) {
		if ( false !== strpos( $e, 'falta' ) ) {
			WP_CLI::error( "no escribo nada: guía ($i): $e" );
		}
	}
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada (todas las sustituciones resuelven a cambia o ya)' );
	return;
}

// --- Apply ---------------------------------------------------------------------------------------
$t = time();
foreach ( $plan as $i => $x ) {
	wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['texto'] ) ] );
	update_field( 'faqs', $x['filas'], $x['id'] );
	update_field( 'actualizado', $hoy, $x['id'] );
	update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
}

// --- Read back -----------------------------------------------------------------------------------
$ok = true;
foreach ( $plan as $i => $x ) {
	$p     = get_post( $x['id'] );
	$filas = pm_filas( 'faqs', $x['id'] );
	$json  = wp_json_encode( $filas, JSON_UNESCAPED_UNICODE );
	$todo  = $p->post_content . $json;
	$bien  = true;
	foreach ( $guias[ $i ]['cuerpo'] as [ , $nuevo ] ) {
		$bien = $bien && false !== strpos( $p->post_content, $nuevo );
	}
	foreach ( $guias[ $i ]['faqs'] as $pregunta => [ , $nuevo ] ) {
		$hit = false;
		foreach ( $filas as $f ) {
			$hit = $hit || ( ( $f['pregunta'] ?? '' ) === $pregunta && false !== strpos( (string) $f['respuesta'], $nuevo ) );
		}
		$bien = $bien && $hit;
	}
	$rincon = false !== strpos( $p->post_content, 'El Rincón Lodge' ) && false !== strpos( $json, 'El Rincón Lodge' );
	$viejas = 0;
	foreach ( $guias[ $i ]['viejas'] as $v ) {
		$viejas += substr_count( $todo, $v );
	}
	$bien   = $bien && $rincon && 0 === $viejas && '20261123' === preg_replace( '/\D/', '', (string) pm_campo( 'actualizado', $x['id'] ) );
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": guía rio-gallegos ($i) #{$x['id']}: " . count( $guias[ $i ]['cuerpo'] ) . ' párrafos y ' . count( $guias[ $i ]['faqs'] ) . ' FAQ leídos de vuelta, El Rincón en cuerpo y FAQ=' . (int) $rincon . ', frases viejas que quedan=' . $viejas . ', ' . count( $filas ) . ' FAQs en total (fuente del JSON-LD) ' . get_permalink( $x['id'] ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'APLICADO' );
