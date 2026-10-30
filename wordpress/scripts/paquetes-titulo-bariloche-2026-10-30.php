<?php
/**
 * Bariloche (es/en), 30/10/2026: title of the lake package. The owner gave the name the market uses for this day,
 * "Lake trout of Nahuel Huapi" (the words anglers search), so the title leads with it in each language and keeps the
 * place. Only a title that is still EXACTLY the one the first script wrote is changed; everything else in the rows (the
 * internal note, the empty price) is written back as it is. A title edited by hand is left alone.
 * Run with wp-script.yml (script = paquetes-titulo-bariloche-2026-10-30).
 */

$aplicar = true;
$destino = 'bariloche';

$titulos = [
	'es' => [
		'Lago Nahuel Huapi en lancha: jornada de pesca con asado en la playa' => 'Truchas del lago Nahuel Huapi (Bariloche): jornada en lancha con asado en la playa',
	],
	'en' => [
		'Lake Nahuel Huapi by boat: a fishing day with a beach barbecue' => 'Lake trout of Nahuel Huapi (Bariloche): a day by boat with a beach barbecue',
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
