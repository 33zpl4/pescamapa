<?php
/**
 * Goya (es/en/pt), 28/10/2026: package titles with the place and the search words ("pesca de dorado en Goya",
 * "paseo en lancha Goya", "pesca nocturna Goya"). Only a title that is still EXACTLY the one the first script wrote is
 * changed; everything else in the rows (prices, "incluye", notes) is written back as it is. If someone edited a title
 * by hand it is left alone. Run with wp-script.yml (script = paquetes-titulos-goya-2026-10-28).
 */

$aplicar = true;
$destino = 'goya';

$titulos = [
	'es' => [
		'Dorado en la Reserva Natural Isoró: jornada completa' => 'Pesca de dorado en la Reserva Natural Isoró (Goya): jornada completa',
		'Dorado en la Reserva Natural Isoró: media jornada'    => 'Pesca de dorado en la Reserva Natural Isoró (Goya): media jornada',
		'Pesca con carnada en el río Paraná'                  => 'Pesca con carnada en el río Paraná, Goya',
		'Pesca nocturna'                                      => 'Pesca nocturna en Goya',
		'Paseo náutico por la reserva'                        => 'Paseo en lancha por la Reserva Natural Isoró, Goya',
		'Packs de varios días: alojamiento y pesca'           => 'Packs de varios días en Goya: alojamiento y pesca',
	],
	'en' => [
		'Dorado in the Isoró Nature Reserve: full day' => 'Dorado fishing in the Isoró Nature Reserve (Goya): full day',
		'Dorado in the Isoró Nature Reserve: half day' => 'Dorado fishing in the Isoró Nature Reserve (Goya): half day',
		'Bait fishing on the Paraná River'             => 'Bait fishing on the Paraná River, Goya',
		'Night fishing'                                => 'Night fishing in Goya',
		'Boat tour of the reserve'                     => 'Boat tour of the Isoró Nature Reserve, Goya',
		'Multi-day packs: lodging and fishing'         => 'Multi-day packs in Goya: lodging and fishing',
	],
	'pt' => [
		'Dourado na Reserva Natural Isoró: dia completo'    => 'Pescaria de dourado na Reserva Natural Isoró (Goya): dia completo',
		'Dourado na Reserva Natural Isoró: meio dia'        => 'Pescaria de dourado na Reserva Natural Isoró (Goya): meio dia',
		'Pesca com isca natural no rio Paraná'              => 'Pesca com isca natural no rio Paraná, Goya',
		'Pesca noturna'                                     => 'Pesca noturna em Goya',
		'Passeio náutico pela reserva'                      => 'Passeio de barco pela Reserva Natural Isoró, Goya',
		'Pacotes de vários dias: hospedagem e pesca'        => 'Pacotes de vários dias em Goya: hospedagem e pesca',
	],
];

foreach ( $titulos as $i => $mapa ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$filas = pm_filas( 'paquetes', $p->ID );
	$n     = 0;
	foreach ( $filas as &$f ) {
		if ( isset( $mapa[ $f['titulo'] ?? '' ] ) ) {
			$f['titulo'] = $mapa[ $f['titulo'] ];
			++$n;
		}
	}
	unset( $f );
	WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se cambian' : 'se cambiarían' ) . " $n de " . count( $filas ) . ' títulos' );
	if ( $aplicar && $n ) {
		update_field( 'paquetes', $filas, $p->ID );
		$leidos = array_column( pm_paquetes( $p->ID ), 'titulo' );
		WP_CLI::log( '  VERIFIED: ' . count( array_intersect( $leidos, array_values( $mapa ) ) ) . ' títulos nuevos leídos de vuelta' );
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
