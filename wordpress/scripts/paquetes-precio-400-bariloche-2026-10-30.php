<?php
/**
 * Bariloche (es/en), 30/10/2026: the lake package price becomes "desde USD 400" for one angler, with two anglers at USD 600
 * in total. Owner (30/10/2026), after talking to the agent who organises these packages: part of the costs (boat, fuel,
 * guide) is shared, so a second angler adds USD 200, not another 400. The unit says both numbers, so nobody reads the
 * 600 as a per-person price.
 *
 * Only the row that still holds exactly the value the previous script wrote (600 USD, "por salida privada, jornada completa",
 * 30/10/2026) is changed; a price loaded by hand is left alone. "Incluye" is NOT touched: it comes from a market quote and has
 * not been confirmed with the agent. Dry run first ($aplicar = false); run with wp-script.yml
 * (script = paquetes-precio-400-bariloche-2026-10-30).
 */

$aplicar = false;
$destino = 'bariloche';

$nuevo = [
	'es' => [ 'antes' => 'por salida privada, jornada completa', 'unidad' => 'para 1 pescador, jornada completa; 2 pescadores: US$ 600 en total' ],
	'en' => [ 'antes' => 'per private trip, full day', 'unidad' => 'for 1 angler, full day; 2 anglers: US$ 600 in total' ],
];

foreach ( $nuevo as $i => $n_ ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$filas = pm_filas( 'paquetes', $p->ID );
	$n     = 0;
	foreach ( $filas as &$f ) {
		if ( 600.0 !== (float) ( $f['desde_monto'] ?? 0 ) || 'USD' !== ( $f['desde_moneda'] ?? '' ) || $n_['antes'] !== ( $f['desde_unidad'] ?? '' ) || '20261030' !== str_replace( '-', '', (string) ( $f['desde_fecha'] ?? '' ) ) ) {
			continue;
		}
		$f['desde_monto']  = 400;
		$f['desde_unidad'] = $n_['unidad'];
		$f['interno']      = trim( (string) ( $f['interno'] ?? '' ) ) . ' ACTUALIZADO el 30/10/2026 (2.ª vez): desde USD 400 para 1 pescador; 2 pescadores USD 600 en total, porque parte de los costos se reparte entre más de una persona (dueño, tras hablar con el agente). "Incluye" sin confirmar con el agente.';
		++$n;
	}
	unset( $f );
	WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se cambia' : 'se cambiaría' ) . " el precio en $n de " . count( $filas ) . ' paquetes → ' . pm_url_paquetes( $destino, $i ) );
	if ( $aplicar && $n ) {
		update_field( 'paquetes', $filas, $p->ID );
		$leidos = pm_paquetes( $p->ID );
		$l      = $leidos[0] ?? [];
		WP_CLI::log( '  VERIFIED: precio ' . ( ! empty( $l['precio'] ) ? $l['precio']['moneda'] . ' ' . $l['precio']['monto'] . ' ' . $l['precio']['unidad'] . ' (' . $l['precio']['fecha'] . ')' : 'NO' ) . '; incluye ' . count( $l['incluye'] ?? [] ) . ' líneas; el interno no sale: ' . ( false === strpos( wp_json_encode( $leidos ), 'ACTUALIZADO' ) ? 'sí' : 'NO' ) );
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
