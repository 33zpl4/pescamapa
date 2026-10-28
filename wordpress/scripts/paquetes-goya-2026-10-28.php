<?php
/**
 * Goya (es/en/pt), 28/10/2026: the "Paquetes de pesca en Goya" page. Loads the packages that the operator's 2026
 * brochure (Drive: "Prestador Experiencia Isoro") supports, in our words and WITHOUT naming the guide:
 * Isoró reserve dorado (full day / half day), Paraná bait fishing, night fishing, boat tour, multi-day packs.
 *
 * Published without price on purpose (owner, 28/10/2026: the page starts gaining exposure; prices within 24 h):
 * each card says the price is quoted for the group and dates. Prices ("desde" with amount, currency, unit and DATE),
 * what is included and capacity are NOT in the brochure and are left empty: they are loaded in wp-admin
 * (Goya guide → tab "Paquetes") as soon as the guides confirm them. The "interno" field stays empty.
 *
 * Idempotent: a language that already has packages is not touched. Dry run first ($aplicar = false); run with
 * wp-script.yml (script = paquetes-goya-2026-10-28).
 */

$aplicar = true;
$destino = 'goya';

$lineas = fn( array $l ) => implode( "\n", $l );

$paquetes = [
	'es' => [
		[ 'titulo' => 'Dorado en la Reserva Natural Isoró: jornada completa', 'resumen' => 'Pesca deportiva con devolución en un laberinto de riachos, lagunas y monte nativo, a 4 km aguas abajo del puerto de Goya. Dorado con señuelos artificiales entre camalotes, correderas y aguas tranquilas.', 'modalidad' => 'Flycast, baitcast o spinning, con señuelos artificiales', 'duracion' => 'Jornada completa: 11 a 12 horas de pesca' ],
		[ 'titulo' => 'Dorado en la Reserva Natural Isoró: media jornada', 'resumen' => 'La misma pesca con devolución en la reserva, en una salida más corta, pensada también para salir en familia.', 'modalidad' => 'Flycast, baitcast o spinning, con señuelos artificiales', 'duracion' => 'Media jornada' ],
		[ 'titulo' => 'Pesca con carnada en el río Paraná', 'resumen' => 'Pesca con devolución en el río Paraná, con base en Goya, anclados o a la deriva (a garete).', 'modalidad' => 'Pesca anclada o a garete, con carnada', 'duracion' => 'Media jornada o jornada completa' ],
		[ 'titulo' => 'Pesca nocturna', 'resumen' => 'Salida de noche, con pesca anclada y carnada.', 'modalidad' => 'Pesca anclada, con carnada' ],
		[ 'titulo' => 'Paseo náutico por la reserva', 'resumen' => 'Recorrido en lancha por la Reserva Natural Isoró y los arroyos de alrededor para conocer la flora y la fauna, observar aves y sacar fotos. También hay expediciones nocturnas.', 'duracion' => 'Media jornada o jornada completa (8 a 10 horas, ideal para fotografía)', 'capacidad' => 'Hasta 5 personas por embarcación' ],
		[ 'titulo' => 'Packs de varios días: alojamiento y pesca', 'resumen' => 'Combinamos días de pesca con días de alojamiento en Goya, según tu grupo.', 'variantes' => [ '1 día de pesca', '1 día de alojamiento + 1 día de pesca', '2 días de alojamiento + 1 día de pesca', '3 días de alojamiento + 2 días de pesca', '4 días de alojamiento + 3 días de pesca', '5 días de alojamiento + 4 días de pesca', '7 días de alojamiento + 6 días de pesca' ] ],
	],
	'en' => [
		[ 'titulo' => 'Dorado in the Isoró Nature Reserve: full day', 'resumen' => 'Catch-and-release sport fishing in a maze of creeks, lagoons and native forest, 4 km downstream from the port of Goya. Dorado on artificial lures among floating islands of camalote, runs and calm water.', 'modalidad' => 'Flycast, baitcast or spinning, with artificial lures', 'duracion' => 'Full day: 11 to 12 hours of fishing' ],
		[ 'titulo' => 'Dorado in the Isoró Nature Reserve: half day', 'resumen' => 'The same catch-and-release fishing in the reserve, on a shorter trip that also works for a family outing.', 'modalidad' => 'Flycast, baitcast or spinning, with artificial lures', 'duracion' => 'Half day' ],
		[ 'titulo' => 'Bait fishing on the Paraná River', 'resumen' => 'Catch-and-release fishing on the Paraná River from Goya, anchored or drifting.', 'modalidad' => 'Anchored or drift fishing, with bait', 'duracion' => 'Half day or full day' ],
		[ 'titulo' => 'Night fishing', 'resumen' => 'A night trip, anchored and with bait.', 'modalidad' => 'Anchored fishing, with bait' ],
		[ 'titulo' => 'Boat tour of the reserve', 'resumen' => 'A boat ride through the Isoró Nature Reserve and the surrounding creeks to see the flora and fauna, watch birds and take photos. Night expeditions are also available.', 'duracion' => 'Half day or full day (8 to 10 hours, ideal for photography)', 'capacidad' => 'Up to 5 people per boat' ],
		[ 'titulo' => 'Multi-day packs: lodging and fishing', 'resumen' => 'We combine fishing days with lodging days in Goya, according to your group.', 'variantes' => [ '1 day of fishing', '1 day of lodging + 1 day of fishing', '2 days of lodging + 1 day of fishing', '3 days of lodging + 2 days of fishing', '4 days of lodging + 3 days of fishing', '5 days of lodging + 4 days of fishing', '7 days of lodging + 6 days of fishing' ] ],
	],
	'pt' => [
		[ 'titulo' => 'Dourado na Reserva Natural Isoró: dia completo', 'resumen' => 'Pesca esportiva com devolução num labirinto de riachos, lagoas e mata nativa, a 4 km rio abaixo do porto de Goya. Dourado com iscas artificiais entre camalotes, corredeiras e águas calmas.', 'modalidad' => 'Flycast, baitcast ou spinning, com iscas artificiais', 'duracion' => 'Dia completo: 11 a 12 horas de pesca' ],
		[ 'titulo' => 'Dourado na Reserva Natural Isoró: meio dia', 'resumen' => 'A mesma pesca com devolução na reserva, num passeio mais curto, pensado também para sair em família.', 'modalidad' => 'Flycast, baitcast ou spinning, com iscas artificiais', 'duracion' => 'Meio dia' ],
		[ 'titulo' => 'Pesca com isca natural no rio Paraná', 'resumen' => 'Pesca com devolução no rio Paraná, com base em Goya, ancorados ou à deriva.', 'modalidad' => 'Pesca ancorada ou à deriva, com isca natural', 'duracion' => 'Meio dia ou dia completo' ],
		[ 'titulo' => 'Pesca noturna', 'resumen' => 'Saída à noite, com pesca ancorada e isca natural.', 'modalidad' => 'Pesca ancorada, com isca natural' ],
		[ 'titulo' => 'Passeio náutico pela reserva', 'resumen' => 'Passeio de barco pela Reserva Natural Isoró e pelos arroios ao redor para conhecer a flora e a fauna, observar aves e fotografar. Também há expedições noturnas.', 'duracion' => 'Meio dia ou dia completo (8 a 10 horas, ideal para fotografia)', 'capacidad' => 'Até 5 pessoas por embarcação' ],
		[ 'titulo' => 'Pacotes de vários dias: hospedagem e pesca', 'resumen' => 'Combinamos dias de pesca com dias de hospedagem em Goya, conforme o seu grupo.', 'variantes' => [ '1 dia de pesca', '1 dia de hospedagem + 1 dia de pesca', '2 dias de hospedagem + 1 dia de pesca', '3 dias de hospedagem + 2 dias de pesca', '4 dias de hospedagem + 3 dias de pesca', '5 dias de hospedagem + 4 dias de pesca', '7 dias de hospedagem + 6 dias de pesca' ] ],
	],
];

foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma, no se carga." );
		continue;
	}
	if ( pm_filas( 'paquetes', $p->ID ) ) {
		WP_CLI::log( "$destino ($i) #{$p->ID}: ya tiene paquetes, no los toco." );
		continue;
	}
	$filas = array_map( fn( $x ) => [
		'publicado' => 1, 'titulo' => $x['titulo'], 'resumen' => $x['resumen'] ?? '', 'modalidad' => $x['modalidad'] ?? '', 'duracion' => $x['duracion'] ?? '',
		'capacidad' => $x['capacidad'] ?? '', 'incluye' => '', 'no_incluye' => '', 'variantes' => $lineas( $x['variantes'] ?? [] ),
		'desde_monto' => '', 'desde_moneda' => '', 'desde_unidad' => '', 'desde_fecha' => '', 'interno' => '',
	], $paquetes[ $i ] );
	WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se cargan' : 'se cargarían' ) . ' ' . count( $filas ) . ' paquetes (sin precio) → ' . pm_url_paquetes( $destino, $i ) );
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'paquetes', $filas, $p->ID );
	$leidos = pm_paquetes( $p->ID );
	WP_CLI::log( '  VERIFIED: ' . count( $leidos ) . ' paquetes publicados leídos de vuelta, ' . count( array_filter( array_column( $leidos, 'precio' ) ) ) . ' con precio' );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
