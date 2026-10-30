<?php
/**
 * Bariloche (es/en), 30/10/2026: second package of the page, the Limay River float (full day, rowboat, fly).
 * Owner (30/10/2026): another market offer as reference; "for the time being we list the same price (the reference), and
 * if later we get a local operator we'll adjust". Data: the offer's page plus real quotes read the same day for
 * Thu 19/11/2026, 07:00-16:00: 1 angler USD 550, 2 anglers USD 650, taxes included, private; included: private guide, full
 * equipment (rod, reel, flies), BBQ lunch and drinks, fishing licences, boat rental. The competitor is not named.
 *
 * Not published: the weight of the fish, the length of the float and the meeting point (the offer's page says Dina Huapi, its
 * quote says another place), because they come from one commercial page and are not confirmed. The rules of the stretch are
 * the ones already on the guide (National Parks annex 2026/27); the page keeps saying the guide confirms what applies on the day.
 *
 * Idempotent: a package with the same title, or a block with the same title, is not added twice. Dry run first
 * ($aplicar = false); run with wp-script.yml (script = paquetes-limay-bariloche-2026-10-30).
 */

$aplicar = false;
$destino = 'bariloche';
$hoy     = '20261030';

$lineas = fn( array $l ) => implode( "\n", $l );

$interno = 'No se publica. Referencia de mercado leída el 30/10/2026 en el flujo de reserva de una agencia europea (competidora, no se nombra): jueves 19/11/2026, 07:00 a 16:00; 1 pescador USD 550, 2 pescadores USD 650, con impuestos; incluye guía privado, equipo completo, asado con bebidas, licencias, bote. Solo jornada completa según su página. Punto de encuentro: su página dice Dina Huapi y su cotización otro lugar: sin confirmar, no se publica. Se lista al mismo precio de la referencia por ahora (dueño, 30/10/2026); si hay un operador local, se ajusta. Peso de los peces y largo del descenso: dichos de la agencia, no se publican.';

$paquete = [
	'es' => [
		'titulo'     => 'Pesca con mosca en el río Limay (Bariloche): descenso en bote a remo, jornada completa',
		'resumen'    => 'Descenso de un día en bote a remo por el río Limay, desde el lago Nahuel Huapi, pescando con mosca a la deriva y tirando el streamer hacia la orilla. Salida temprano, con asado al mediodía. Las reglas del tramo y los horarios de pesca de cada fecha los confirma el guía.',
		'modalidad'  => 'Mosca (streamer y mosca seca), desde un bote a remo sin motor',
		'duracion'   => 'Jornada completa, con salida temprano',
		'capacidad'  => 'Salida privada para tu grupo',
		'incluye'    => [ 'Guía privado', 'Equipo de pesca completo (caña, reel y moscas)', 'Asado al mediodía con bebidas', 'Permisos de pesca', 'Bote' ],
		'no_incluye' => [ 'Vuelos y traslados hasta Bariloche' ],
		'unidad'     => 'para 1 pescador, jornada completa; 2 pescadores: US$ 650 en total',
	],
	'en' => [
		'titulo'     => 'Fly fishing on the Limay River (Bariloche): a full-day rowboat float',
		'resumen'    => 'A one-day float down the Limay River in a rowboat, starting at Lake Nahuel Huapi, fly fishing as you drift and casting the streamer towards the bank. An early start, with a barbecue at midday. The guide confirms the rules of the stretch and the permitted fishing hours for your date.',
		'modalidad'  => 'Fly fishing (streamer and dry fly), from a rowboat without a motor',
		'duracion'   => 'Full day, with an early start',
		'capacidad'  => 'Private trip for your group',
		'incluye'    => [ 'Private guide', 'Full fishing equipment (rod, reel and flies)', 'Barbecue lunch with drinks', 'Fishing permits', 'Boat' ],
		'no_incluye' => [ 'Flights and transfers to Bariloche' ],
		'unidad'     => 'for 1 angler, full day; 2 anglers: US$ 650 in total',
	],
];

$bloque = [
	'es' => [
		'titulo'        => 'Reglas del Limay en el Parque Nacional, temporada 2026/27',
		'texto'         => $lineas( [
			'El Limay superior, dentro del Parque Nacional, se pesca del 1 de noviembre de 2026 al 31 de mayo de 2027.',
			'Se permite embarcar sin motor, salvo en los primeros 400 m debajo del lago. Del lago a Rincón Chico es solo mosca y devolución obligatoria.',
			'El límite es un salmónido y, desde el 1 de abril de 2027, se devuelven todos. El anexo lista además los horarios de pesca permitidos por mes.',
		] ),
		'fuente_nombre' => 'Reglamento de pesca 2026/27: ambientes de Parques Nacionales',
		'fuente_url'    => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/',
		'consultada'    => $hoy,
	],
	'en' => [
		'titulo'        => 'Limay rules inside the National Park, 2026/27 season',
		'texto'         => $lineas( [
			'The upper Limay, inside the National Park, is open from 1 November 2026 to 31 May 2027.',
			'Boats without a motor are allowed except in the first 400 m below the lake. From the lake to Rincón Chico it is fly only and catch and release.',
			'The limit is one salmonid and, from 1 April 2027, every fish is released. The annex also lists the permitted fishing hours by month.',
		] ),
		'fuente_nombre' => 'Fishing regulations 2026/27: National Parks waters',
		'fuente_url'    => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/',
		'consultada'    => $hoy,
	],
];

foreach ( [ 'es', 'en' ] as $i ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$d      = $paquete[ $i ];
	$filas  = pm_filas( 'paquetes', $p->ID );
	$info   = pm_filas( 'paquetes_info', $p->ID );
	$hay_p  = in_array( $d['titulo'], array_column( $filas, 'titulo' ), true );
	$hay_b  = in_array( $bloque[ $i ]['titulo'], array_column( $info, 'titulo' ), true );
	if ( ! $hay_p ) {
		$filas[] = [
			'publicado' => 1, 'titulo' => $d['titulo'], 'resumen' => $d['resumen'], 'modalidad' => $d['modalidad'], 'duracion' => $d['duracion'],
			'capacidad' => $d['capacidad'], 'incluye' => $lineas( $d['incluye'] ), 'no_incluye' => $lineas( $d['no_incluye'] ), 'variantes' => '',
			'desde_monto' => 550, 'desde_moneda' => 'USD', 'desde_unidad' => $d['unidad'], 'desde_fecha' => $hoy, 'interno' => $interno,
		];
	}
	if ( ! $hay_b ) {
		$info[] = $bloque[ $i ];
	}
	WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se agregan' : 'se agregarían' ) . ' ' . ( $hay_p ? 0 : 1 ) . ' paquete y ' . ( $hay_b ? 0 : 1 ) . ' bloque → ' . pm_url_paquetes( $destino, $i ) );
	if ( $aplicar && ( ! $hay_p || ! $hay_b ) ) {
		update_field( 'paquetes', $filas, $p->ID );
		update_field( 'paquetes_info', $info, $p->ID );
		$leidos = pm_paquetes( $p->ID );
		$l      = $leidos[ count( $leidos ) - 1 ] ?? [];
		WP_CLI::log( '  VERIFIED: ' . count( $leidos ) . ' paquetes publicados; último: ' . ( $l['titulo'] ?? '?' ) . ' · precio ' . ( ! empty( $l['precio'] ) ? $l['precio']['moneda'] . ' ' . $l['precio']['monto'] . ' ' . $l['precio']['unidad'] . ' (' . $l['precio']['fecha'] . ')' : 'NO' ) . '; incluye ' . count( $l['incluye'] ?? [] ) . ' líneas; ' . count( pm_paquetes_info( $p->ID ) ) . ' bloques; el interno no sale: ' . ( false === strpos( wp_json_encode( $leidos ), 'Referencia de mercado' ) ? 'sí' : 'NO' ) );
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
