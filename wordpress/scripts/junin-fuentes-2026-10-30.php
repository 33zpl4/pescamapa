<?php
/**
 * Junín y San Martín guide (es/en), 30/10/2026: adds the official pages of the regulation as sources, as the owner clarified (the text he pasted is
 * the official site's: reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/). Four rows per language: Anexo Neuquén and Anexo Río Negro (general
 * points, read from the owner's copy of the page), and the two environment lists (read in the AICACYP copies already listed). Rows are added only
 * if their URL is not there yet; nothing is removed. Idempotent. Dry run first. Run with wp-script.yml (script = junin-fuentes-2026-10-30).
 */

$aplicar = false;
$hoy     = '20261030';
$R       = 'https://reglamentodepesca.org.ar/reglamento-items/';

$nuevas = [
	'en' => [
		[ 'Regulation of Continental Sport Fishing of Patagonia (official site): Neuquén annex, general points, read on 30/10/2026', $R . 'anexo-neuquen/' ],
		[ 'Regulation of Continental Sport Fishing of Patagonia (official site): Río Negro annex, general points, read on 30/10/2026', $R . 'anexo-rio-negro/' ],
		[ 'Regulation 2026/27 (official site): list of environments of Neuquén; we read the entries in the AICACYP copy listed above', $R . 'ambientes-neuquen/' ],
		[ 'Regulation 2026/27 (official site): list of environments of the National Parks of the south; read in the AICACYP copy listed above', $R . 'ambientes-parques-nacionales-del-sur/' ],
	],
	'es' => [
		[ 'Reglamento de Pesca Deportiva Continental Patagónico (sitio oficial): Anexo Neuquén, puntos generales, leído el 30/10/2026', $R . 'anexo-neuquen/' ],
		[ 'Reglamento de Pesca Deportiva Continental Patagónico (sitio oficial): Anexo Río Negro, puntos generales, leído el 30/10/2026', $R . 'anexo-rio-negro/' ],
		[ 'Reglamento 2026/27 (sitio oficial): listado de ambientes de Neuquén; leímos las fichas en la copia de AICACYP listada arriba', $R . 'ambientes-neuquen/' ],
		[ 'Reglamento 2026/27 (sitio oficial): listado de ambientes de los Parques Nacionales del sur; leído en la copia de AICACYP listada arriba', $R . 'ambientes-parques-nacionales-del-sur/' ],
	],
];

foreach ( $nuevas as $idioma => $filas_nuevas ) {
	$p = pm_buscar( 'pm_destino', 'junin-de-los-andes', $idioma );
	if ( ! $p ) {
		WP_CLI::error( "No encontré la guía junin-de-los-andes ($idioma)." );
	}
	$filas = pm_filas( 'fuentes', $p->ID );
	$urls  = array_column( $filas, 'url' );
	$suma  = 0;
	foreach ( $filas_nuevas as [ $nombre, $url ] ) {
		if ( in_array( $url, $urls, true ) ) {
			continue;
		}
		$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => 1, 'consultada' => $hoy ];
		$suma++;
	}
	WP_CLI::log( "junin-de-los-andes ($idioma) #{$p->ID}: " . ( $aplicar ? 'se agregan' : 'se agregarían' ) . " $suma fuentes a las " . count( $urls ) . ' que hay' );
	if ( ! $aplicar || ! $suma ) {
		continue;
	}
	update_field( 'fuentes', $filas, $p->ID );
	$leida = pm_filas( 'fuentes', $p->ID );
	$ok    = count( $leida ) === count( $filas ) && in_array( $R . 'anexo-neuquen/', array_column( $leida, 'url' ), true );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": junin-de-los-andes ($idioma) " . count( $leida ) . ' fuentes leídas de vuelta' );
	$ok || WP_CLI::error( 'las fuentes no quedaron.' );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
