<?php
/**
 * 4/11/2026: official season dates (and, where a named source says so, the best months) of the Patagonia guides already live.
 * Each date comes from the guide's own sourced text, read from the norm (see the fuentes of each guide):
 *  - bariloche: season opens 1/11/2026; Lake Nahuel Huapi and Mascardi close 1/5/2027; the Limay inside the Park closes 31/5/2027.
 *  - san-martin-junin: opens 1/11/2026; the rivers of the annex close 31/5/2027, some waters 1/5/2027.
 *  - jurassic-lake: permit season 1/10/2026 to 30/9/2027 (Disposición 241/26); Strobel is open all year; Barrancoso 1/10/2026 to 3/5/2027.
 *  - rio-grande: opens 30/10/2026; the Río Grande and tributaries close 18/4/2027 (Resolución S.A. 259/2026); the rest of the province 2/5/2027.
 *    Best months 2 to 4 per KD Flyfishing (the seller of trips to Wuijoor Lodge), who puts late February to mid-April as prime.
 *  - rio-gallegos: opens 1/11/2026; the Gallegos closes 30/5/2027 (Anexo I 2026/27); Gallegos Chico, Chico Sur and Penitente 3/5/2027.
 *    Best months 1 and 2 per Karku Fly Fishing (January and February for the biggest fish).
 * "Best months" are an operator's opinion and are always shown with the name. Idempotent: it only writes what differs.
 * Dry run first ($aplicar = false). Run with wp-script.yml (script = temporada-fechas-2026-11-04).
 */

$aplicar = false;

$datos = [
	'bariloche' => [
		'abre' => '20261101', 'cierra' => '20270501', 'pico' => '', 'quien' => '',
		'nota' => [
			'es' => 'El Limay dentro del Parque cierra el 31 de mayo de 2027; el lago Nahuel Huapi y el Mascardi, el 1 de mayo de 2027.',
			'en' => 'The Limay inside the Park closes on 31 May 2027; Lake Nahuel Huapi and Mascardi close on 1 May 2027.',
			'pt' => 'O Limay dentro do Parque fecha em 31 de maio de 2027; o lago Nahuel Huapi e o Mascardi fecham em 1º de maio de 2027.',
		],
	],
	'san-martin-junin' => [
		'abre' => '20261101', 'cierra' => '20270531', 'pico' => '', 'quien' => '',
		'nota' => [
			'es' => 'Los ríos del anexo cierran el 31 de mayo de 2027; algunos ambientes cierran el 1 de mayo de 2027.',
			'en' => 'The rivers in the annex close on 31 May 2027; some waters close on 1 May 2027.',
			'pt' => 'Os rios do anexo fecham em 31 de maio de 2027; alguns ambientes fecham em 1º de maio de 2027.',
		],
	],
	'jurassic-lake' => [
		'abre' => '20261001', 'cierra' => '20270930', 'pico' => '', 'quien' => '',
		'nota' => [
			'es' => 'El lago Strobel está abierto todo el año; el río Barrancoso va del 1 de octubre de 2026 al 3 de mayo de 2027.',
			'en' => 'Lake Strobel is open all year; the Barrancoso River runs from 1 October 2026 to 3 May 2027.',
			'pt' => 'O lago Strobel está aberto o ano todo; o rio Barrancoso vai de 1º de outubro de 2026 a 3 de maio de 2027.',
		],
	],
	'rio-grande' => [
		'abre' => '20261030', 'cierra' => '20270418', 'pico' => '2,3,4', 'quien' => 'KD Flyfishing, vendedor de viajes a Wuijoor Lodge',
		'nota' => [
			'es' => 'Fechas del río Grande y sus afluentes; el resto de la provincia cierra el 2 de mayo de 2027.',
			'en' => 'Dates for the Río Grande and its tributaries; the rest of the province closes on 2 May 2027.',
			'pt' => 'Datas do rio Grande e seus afluentes; o resto da província fecha em 2 de maio de 2027.',
		],
	],
	'rio-gallegos' => [
		'abre' => '20261101', 'cierra' => '20270530', 'pico' => '1,2', 'quien' => 'Karku Fly Fishing',
		'nota' => [
			'es' => 'Gallegos Chico, Chico Sur y Penitente cierran el 3 de mayo de 2027.',
			'en' => 'Gallegos Chico, Chico Sur and Penitente close on 3 May 2027.',
			'pt' => 'Gallegos Chico, Chico Sur e Penitente fecham em 3 de maio de 2027.',
		],
	],
];

$cambios = 0;
foreach ( $datos as $clave => $x ) {
	$es = pm_buscar( 'pm_destino', $clave, 'es' );
	if ( ! $es ) {
		WP_CLI::warning( "$clave: no hay guía en español, se salta" );
		continue;
	}
	$comunes = [ 'temporada_abre' => $x['abre'], 'temporada_cierra' => $x['cierra'], 'pico_meses' => $x['pico'], 'pico_quien' => $x['quien'] ];
	foreach ( $comunes as $campo => $valor ) {
		$actual = str_replace( '-', '', (string) pm_campo( $campo, $es->ID ) );
		if ( $actual === (string) $valor ) {
			continue;
		}
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave (es) $campo: «{$actual}» → «{$valor}»" );
		++$cambios;
		if ( $aplicar ) {
			update_field( $campo, $valor, $es->ID );
		}
	}
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$p = pm_buscar( 'pm_destino', $clave, $i );
		if ( ! $p ) {
			WP_CLI::log( "· $clave ($i): sin guía en ese idioma" );
			continue;
		}
		if ( (string) pm_campo( 'temporada_nota', $p->ID ) === $x['nota'][ $i ] ) {
			continue;
		}
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) temporada_nota" );
		++$cambios;
		if ( $aplicar ) {
			update_field( 'temporada_nota', $x['nota'][ $i ], $p->ID );
		}
	}
}
if ( ! $aplicar ) {
	WP_CLI::log( "SIMULACRO: $cambios cambios, no se escribió nada" );
	return;
}

$ok = true;
foreach ( $datos as $clave => $x ) {
	$es = pm_buscar( 'pm_destino', $clave, 'es' );
	if ( ! $es ) {
		continue;
	}
	$d    = pm_destino( $es->ID );
	$bien = pm_temporada_hay( $d ) && str_replace( '-', '', $d['abre'] ) === $x['abre'] && str_replace( '-', '', $d['cierra'] ) === $x['cierra'] && '' !== $d['temporada_nota'];
	$faq  = pm_temporada_faq( $d );
	$bien = $bien && 1 === count( $faq ) && false !== strpos( $faq[0]['respuesta'], pm_fecha( $d['cierra'], 'es' ) );
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $clave {$d['abre']} a {$d['cierra']}; pico " . ( $d['pico'] ? implode( ',', $d['pico'] ) . " ({$d['pico_quien']})" : 'no' ) . '; respuesta: ' . $faq[0]['respuesta'] );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna guía no se leyó de vuelta como se esperaba.' );
WP_CLI::log( "APLICADO: $cambios cambios" );
