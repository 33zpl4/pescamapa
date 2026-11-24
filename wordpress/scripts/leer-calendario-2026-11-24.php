<?php
/**
 * 24/11/2026, SOLO LECTURA: vuelca, por guía publicada en español, lo que el calendario nuevo necesita para armar
 * wordpress/plugins/pescamapa-core/data/calendario.json (clave, región, provincia, especies, fechas oficiales, filas de
 * temporada, autoridad y fecha de verificación de la guía). No escribe nada. Solo texto ya público de las guías de destino
 * (nada de fichas de prestadores ni contactos). Uso: wp-script con script = leer-calendario-2026-11-24.
 */
foreach ( pm_destinos_lista( 'es' ) as $d ) {
	WP_CLI::log( '=== ' . $d['clave'] . ' | ' . $d['nombre'] . ' | ' . $d['region'] . '/' . $d['provincia'] . ' | especies: ' . implode( ',', $d['especies'] ) );
	WP_CLI::log( 'abre=' . $d['abre'] . ' cierra=' . $d['cierra'] . ' pico=' . implode( ',', $d['pico'] ) . ' quien=' . $d['pico_quien'] );
	WP_CLI::log( 'nota=' . $d['temporada_nota'] );
	WP_CLI::log( 'epoca=' . $d['epoca'] );
	foreach ( $d['temporada'] as $x ) {
		WP_CLI::log( '  fila: ' . $x['meses'] . ' => ' . $x['que'] );
	}
	WP_CLI::log( 'reg: ' . $d['reglamento']['autoridad'] . ' | ' . $d['reglamento']['url'] . ' | verificado ' . $d['reglamento']['verificado'] . ' | actualizado ' . $d['actualizado'] );
	WP_CLI::log( 'guias=' . ( pm_destino_guias( $d['clave'], 'es' ) ? 'si' : 'no' ) . ' ; idiomas=' . implode( ',', array_keys( pm_alternas_destino( $d['clave'] ) ) ) );
}
WP_CLI::log( '--- especies publicadas (es)' );
foreach ( pm_especies_lista( 'es' ) as $e ) {
	WP_CLI::log( $e['clave'] . ' = ' . $e['nombre'] );
}
