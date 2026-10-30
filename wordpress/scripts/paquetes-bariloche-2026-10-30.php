<?php
/**
 * Bariloche (es/en), 30/10/2026: the "Paquetes de pesca en Bariloche y el Nahuel Huapi" page, same model as Goya and Esquina.
 *
 * Owner (30/10/2026): a European agency's day on Lake Nahuel Huapi is an anchor price and a package type we can list; every
 * form goes to Pescamapa (inquiry, proposal), and we are already talking to people who would offer the service.
 * Owner's rule of 28/10/2026 still holds: a competitor's offer can be offered by us, the competitor is neither listed nor
 * named, and its price is only an internal benchmark. So: the package is published WITHOUT price (the card says it is
 * quoted for the group and dates), the "from 450" goes in the internal field. That figure has no unit and no date on the
 * source page, and the page only shows a "desde" with amount, currency, unit AND date.
 *
 * Nothing about what is included is published: the only source for "licence, boat, fuel, lunch included" is the agency's
 * own sales page, so "incluye" stays empty until an operator confirms. Rules of the lake come from the National Parks
 * annex 2026/27 (official site), the permit values from the Río Negro portal, both read on 30/10/2026.
 *
 * Idempotent: a language that already has packages / blocks is not touched. Dry run first ($aplicar = false); run with
 * wp-script.yml (script = paquetes-bariloche-2026-10-30).
 */

$aplicar = false;
$destino = 'bariloche';
$hoy     = '20261030';

$lineas = fn( array $l ) => implode( "\n", $l );

$interno = 'No se publica. Ancla de mercado (30/10/2026): una agencia europea vende esta jornada "desde 450 $" (sin unidad ni fecha visibles en su página; moneda y base por persona o por lancha NO confirmadas). Dice incluir guía, equipo, bote y combustible, licencia, asado con bebidas; salida privada; embarque en Bahía López (Circuito Chico), unos 15 min de Bariloche; unos 30 min de navegación; bote con motor eléctrico; media jornada posible; temporada fuerte de noviembre a abril. Todo es dicho por la agencia: confirmar con un operador antes de publicar incluye, unidad o precio. Hay conversaciones abiertas con operadores (dueño, 30/10/2026); todavía sin acuerdo firmado.';

$paquetes = [
	'es' => [
		[
			'titulo'    => 'Lago Nahuel Huapi en lancha: jornada de pesca con asado en la playa',
			'resumen'   => 'Salida privada en lancha con guía por el lago Nahuel Huapi, pescando donde el lago recibe ríos y cascadas, con un asado en la playa a mediodía. Las reglas de captura de cada fecha las confirma el guía.',
			'modalidad' => 'Mosca con streamer y línea hundidora, o spinning',
			'duracion'  => 'Jornada completa; también se puede armar media jornada',
			'capacidad' => 'Salida privada para tu grupo; la cantidad por lancha se confirma en la propuesta',
			'no_incluye' => [ 'Vuelos y traslados hasta Bariloche' ],
			'variantes' => [ 'Jornada completa con asado en la playa', 'Media jornada, mañana o tarde' ],
		],
	],
	'en' => [
		[
			'titulo'    => 'Lake Nahuel Huapi by boat: a fishing day with a beach barbecue',
			'resumen'   => 'A private boat trip with a guide on Lake Nahuel Huapi, fishing where the lake meets rivers and waterfalls, with a barbecue on the beach at midday. The guide confirms the catch rules for your date.',
			'modalidad' => 'Fly fishing with a streamer and sinking line, or spinning',
			'duracion'  => 'Full day; a half day can also be arranged',
			'capacidad' => 'Private trip for your group; the number of people per boat is confirmed in the proposal',
			'no_incluye' => [ 'Flights and transfers to Bariloche' ],
			'variantes' => [ 'Full day with a barbecue on the beach', 'Half day, morning or afternoon' ],
		],
	],
];

$bloques = [
	'es' => [
		[
			'titulo'        => 'Reglas del lago en la temporada 2026/27',
			'texto'         => $lineas( [
				'El lago Nahuel Huapi está abierto del 1 de noviembre de 2026 al 1 de mayo de 2027, con un límite diario de dos salmónidos.',
				'Del 2 al 31 de mayo de 2027 el límite es un salmónido de menos de 40 cm y no se pesca a menos de 200 m de las bocas de ríos y arroyos.',
				'Se prohíbe navegar a motor a menos de 300 m de los paredones de la isla Victoria y de la isla Fray Menéndez, y hay prohibiciones de desembarco en partes de los brazos Blest y Tristeza y en algunas islas.',
			] ),
			'fuente_nombre' => 'Reglamento de pesca 2026/27: ambientes de Parques Nacionales',
			'fuente_url'    => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/',
			'consultada'    => $hoy,
		],
		[
			'titulo'        => 'El permiso de pesca',
			'texto'         => $lineas( [
				'El permiso de Río Negro lo vende online la Mesa Directiva de Pesca Deportiva Zona Andina. Valores de la temporada 2026/27 para no residentes, en pesos argentinos: un día $50.000, diez días $140.000, temporada $280.000.',
				'Si el permiso va incluido en la propuesta lo decimos en la propuesta; si no, lo sacás vos antes de la salida.',
			] ),
			'fuente_nombre' => 'Permisos de pesca (Mesa Directiva de Pesca Deportiva Zona Andina)',
			'fuente_url'    => 'https://www.permisosdepesca.com.ar/',
			'consultada'    => $hoy,
		],
	],
	'en' => [
		[
			'titulo'        => 'Lake rules for the 2026/27 season',
			'texto'         => $lineas( [
				'Lake Nahuel Huapi is open from 1 November 2026 to 1 May 2027, with a daily limit of two salmonids.',
				'From 2 to 31 May 2027 the limit is one salmonid under 40 cm and fishing is banned within 200 m of river and stream mouths.',
				'Motor navigation is banned within 300 m of the cliffs of Isla Victoria and Isla Fray Menéndez, and there are landing bans on parts of the Blest and Tristeza arms and on some islands.',
			] ),
			'fuente_nombre' => 'Fishing regulations 2026/27: National Parks waters',
			'fuente_url'    => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/',
			'consultada'    => $hoy,
		],
		[
			'titulo'        => 'The fishing permit',
			'texto'         => $lineas( [
				'The Río Negro permit is sold online by the Mesa Directiva de Pesca Deportiva Zona Andina. Values for the 2026/27 season for non-residents, in Argentine pesos: one day AR$ 50,000, ten days AR$ 140,000, whole season AR$ 280,000.',
				'If the permit is included in your proposal we say so in the proposal; if not, you buy it before the trip.',
			] ),
			'fuente_nombre' => 'Fishing permits (Mesa Directiva de Pesca Deportiva Zona Andina)',
			'fuente_url'    => 'https://www.permisosdepesca.com.ar/',
			'consultada'    => $hoy,
		],
	],
];

foreach ( [ 'es', 'en' ] as $i ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma, no se carga." );
		continue;
	}
	if ( pm_filas( 'paquetes', $p->ID ) || pm_filas( 'paquetes_info', $p->ID ) ) {
		WP_CLI::log( "$destino ($i) #{$p->ID}: ya tiene paquetes o bloques, no los toco." );
		continue;
	}
	$filas = array_map( fn( $x ) => [
		'publicado' => 1, 'titulo' => $x['titulo'], 'resumen' => $x['resumen'], 'modalidad' => $x['modalidad'], 'duracion' => $x['duracion'],
		'capacidad' => $x['capacidad'], 'incluye' => '', 'no_incluye' => $lineas( $x['no_incluye'] ), 'variantes' => $lineas( $x['variantes'] ),
		'desde_monto' => '', 'desde_moneda' => '', 'desde_unidad' => '', 'desde_fecha' => '', 'interno' => $interno,
	], $paquetes[ $i ] );
	WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se cargan' : 'se cargarían' ) . ' ' . count( $filas ) . ' paquete(s) sin precio y ' . count( $bloques[ $i ] ) . ' bloques → ' . pm_url_paquetes( $destino, $i ) );
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'paquetes', $filas, $p->ID );
	update_field( 'paquetes_info', $bloques[ $i ], $p->ID );
	$leidos = pm_paquetes( $p->ID );
	$info   = pm_paquetes_info( $p->ID );
	WP_CLI::log( '  VERIFIED: ' . count( $leidos ) . ' paquete(s) publicados leídos de vuelta, ' . count( array_filter( array_column( $leidos, 'precio' ) ) ) . ' con precio, ' . count( $info ) . ' bloques; el interno no sale: ' . ( false === strpos( wp_json_encode( $leidos ), 'Ancla' ) ? 'sí' : 'NO' ) );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
