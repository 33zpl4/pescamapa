<?php
/**
 * Goya, 21/10/2026: "Más para hacer en Goya" with the link to Bitácora de
 * Viajes (bitacoragoya.com.ar), the sister site that runs excursions around
 * the city. Adds one row to the guide's `mas` list in each language, only
 * if that list is empty or lacks the link (never touches rows an editor
 * added). Run AFTER the plugin with the `mas` field is deployed.
 * Run with wp-script.yml (script = goya-mas-2026-10-21).
 */

$url  = 'https://bitacoragoya.com.ar/';
$fila = [
	'es' => [ 'nombre' => 'Bitácora de Viajes', 'texto' => '¿Buscás otras actividades en Goya? Excursiones por la ciudad y sus alrededores.' ],
	'pt' => [ 'nombre' => 'Bitácora de Viajes', 'texto' => 'Procurando outras atividades em Goya? Excursões pela cidade e arredores.' ],
	'en' => [ 'nombre' => 'Bitácora de Viajes', 'texto' => 'Looking for other things to do in Goya? Excursions around the city and its surroundings.' ],
];

foreach ( $fila as $i => $f ) {
	$p = pm_buscar( 'pm_destino', 'goya', $i );
	if ( ! $p ) {
		WP_CLI::warning( "goya ($i) no existe" );
		continue;
	}
	$filas = pm_filas( 'mas', $p->ID );
	if ( array_filter( $filas, fn( $m ) => ( $m['url'] ?? '' ) === $url ) ) {
		WP_CLI::log( "· goya ($i): el enlace ya estaba" );
		continue;
	}
	$filas[] = $f + [ 'url' => $url, 'patrocinado' => 0 ];
	update_field( 'mas', $filas, $p->ID );
	update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "✓ goya ($i): agregado 'Más para hacer'" );
}
