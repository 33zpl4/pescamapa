<?php
/**
 * Goya and Esquina (es/en/pt; Esquina en/pt exist only after esquina-traducciones-2026-10-28 has been applied), 28/10/2026: the intro and the Google description of the packages page say what is
 * really offered in each place (the generic text no longer lists night fishing or boat tours, which Esquina does not
 * have). Only fills an EMPTY field: anything an editor wrote is left alone. Run with wp-script.yml
 * (script = paquetes-intros-2026-10-28).
 */

$aplicar = true;

$t = [
	'goya' => [
		'es' => [ 'Armamos salidas de pesca en Goya: dorado con devolución en la Reserva Natural Isoró, pesca con carnada en el Paraná, pesca nocturna, paseos en lancha y packs de varios días con alojamiento. Cada opción dice qué incluye y el precio se cotiza según tu grupo y tus fechas.', 'Paquetes de pesca en Goya: dorado con devolución en la Reserva Isoró, carnada en el Paraná, nocturna, paseos en lancha y packs con alojamiento. Cotización sin costo.' ],
		'en' => [ 'We put together fishing trips in Goya: catch-and-release dorado in the Isoró Nature Reserve, bait fishing on the Paraná, night fishing, boat tours and multi-day packs with lodging. Each option says what it includes, and the price is quoted for your group and dates.', 'Fishing packages in Goya: catch-and-release dorado in the Isoró Reserve, bait fishing on the Paraná, night trips, boat tours and lodging packs. Free quote.' ],
		'pt' => [ 'Montamos saídas de pescaria em Goya: dourado com devolução na Reserva Natural Isoró, pesca com isca natural no Paraná, pesca noturna, passeios de barco e pacotes de vários dias com hospedagem. Cada opção diz o que inclui e o preço é cotado conforme o seu grupo e as suas datas.', 'Pacotes de pescaria em Goya, Argentina: dourado com devolução na Reserva Isoró, isca natural no Paraná, pesca noturna, passeios e pacotes com hospedagem. Cotação sem custo.' ],
	],
	'esquina' => [
		'es' => [ 'Armamos salidas de pesca en Esquina: carnada viva, señuelos (spinning y baitcasting), mosca, al garete y trolling, y packs de varios días con alojamiento. Cada opción dice qué incluye y el precio se cotiza según tu grupo y tus fechas.', 'Paquetes de pesca en Esquina: carnada viva, señuelos, mosca, al garete y trolling, todo incluido y packs de varios días con alojamiento. Cotización sin costo.' ],
		'en' => [ 'We put together fishing trips in Esquina: live bait, lures (spinning and baitcasting), fly fishing, drift fishing and trolling, plus multi-day packs with lodging. Each option says what it includes, and the price is quoted for your group and dates.', 'Fishing packages in Esquina, Argentina: live bait, lures, fly fishing, drift fishing and trolling, all-inclusive and multi-day packs with lodging. Free quote.' ],
		'pt' => [ 'Montamos saídas de pescaria em Esquina: isca viva, iscas artificiais (spinning e baitcasting), mosca, pesca à deriva e corrico, e pacotes de vários dias com hospedagem. Cada opção diz o que inclui e o preço é cotado conforme o seu grupo e as suas datas.', 'Pacotes de pescaria em Esquina, Argentina: isca viva, iscas artificiais, mosca, pesca à deriva e corrico, tudo incluído e pacotes de vários dias com hospedagem. Cotação sem custo.' ],
	],
];

foreach ( $t as $destino => $idiomas ) {
	foreach ( $idiomas as $i => [ $intro, $desc ] ) {
		$p = pm_buscar( 'pm_destino', $destino, $i );
		if ( ! $p ) {
			WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
			continue;
		}
		foreach ( [ 'paquetes_intro' => $intro, 'paquetes_description' => $desc ] as $campo => $valor ) {
			if ( '' !== trim( (string) pm_campo( $campo, $p->ID ) ) ) {
				WP_CLI::log( "$destino ($i) #{$p->ID}: $campo ya tiene texto, no lo toco." );
				continue;
			}
			WP_CLI::log( "$destino ($i) #{$p->ID}: $campo " . ( $aplicar ? 'se carga' : 'se cargaría' ) . ' (' . mb_strlen( $valor ) . ' car.)' );
			if ( $aplicar ) {
				update_field( $campo, $valor, $p->ID );
				WP_CLI::log( '  VERIFIED: ' . ( trim( (string) pm_campo( $campo, $p->ID ) ) === $valor ? 'leído de vuelta' : 'NO coincide' ) );
			}
		}
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
