<?php
/**
 * Bariloche (es/en), 30/10/2026: the lake package price goes from USD 650 (market quote for 2 anglers) to "desde USD 600",
 * the figure an agent who organises these packages said he can offer (owner, 30/10/2026: "if we get interested people,
 * we'd do with him"). The unit is kept neutral ("per private trip, full day"): the group size behind the 600 was not
 * given, and the page already says the final price depends on the group, the dates and the water level.
 *
 * Only the row that still holds exactly the value the previous script wrote (650 USD, 30/10/2026) is changed; a price loaded
 * by hand is left alone. "Incluye" is NOT touched: it comes from the market quote and has not been confirmed with this agent.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = paquetes-precio-600-bariloche-2026-10-30).
 */

$aplicar = false;
$destino = 'bariloche';

$unidad = [
	'es' => 'por salida privada, jornada completa',
	'en' => 'per private trip, full day',
];

foreach ( $unidad as $i => $u ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$filas = pm_filas( 'paquetes', $p->ID );
	$n     = 0;
	foreach ( $filas as &$f ) {
		if ( 650.0 !== (float) ( $f['desde_monto'] ?? 0 ) || 'USD' !== ( $f['desde_moneda'] ?? '' ) || '20261030' !== str_replace( '-', '', (string) ( $f['desde_fecha'] ?? '' ) ) ) {
			continue;
		}
		$f['desde_monto']  = 600;
		$f['desde_unidad'] = $u;
		$f['interno']      = trim( (string) ( $f['interno'] ?? '' ) ) . ' ACTUALIZADO el 30/10/2026: desde USD 600 por salida privada, jornada completa, según un agente que organiza estos paquetes y lo ofrece (dueño); tamaño de grupo detrás del 600 y "incluye" de ese agente sin confirmar (el "incluye" publicado viene de la cotización de mercado). Si hay interesados se trabaja con él.';
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
