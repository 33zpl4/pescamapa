<?php
/**
 * Bariloche (es/en), 30/10/2026: publishes the reference price and what the lake package includes.
 * Owner (30/10/2026): "we do have enough info; good to publish; we receive the request and pass the lead to one of the
 * providers". Data: a real market quote read the same day for Thu 5/11/2026, 2 anglers, 09:30-16:30, Bahía López:
 * USD 650 in total, taxes included, private; included: private guide, full equipment (rod, reel, flies), BBQ lunch and drinks,
 * fishing licences, boat rental and fuel. The competitor is not named. The page already says the price is a reference
 * ("Precio de referencia al ...; el final depende del grupo, las fechas y el nivel del agua"). The "from 450" of that
 * agency is NOT used: its unit is not confirmed.
 *
 * Only a package whose price and "incluye" are still empty is touched (a value loaded by hand is left alone); the rest of
 * the row (title, internal note) is written back as it is. Dry run first ($aplicar = false); run with wp-script.yml
 * (script = paquetes-precio-bariloche-2026-10-30).
 */

$aplicar = false;
$destino = 'bariloche';

$datos = [
	'es' => [
		'unidad'  => 'por salida privada de 2 pescadores, jornada completa',
		'incluye' => [ 'Guía privado', 'Equipo de pesca completo (caña, reel y moscas)', 'Asado al mediodía con bebidas', 'Permisos de pesca', 'Lancha y combustible' ],
	],
	'en' => [
		'unidad'  => 'for a private trip of 2 anglers, full day',
		'incluye' => [ 'Private guide', 'Full fishing equipment (rod, reel and flies)', 'Barbecue lunch with drinks', 'Fishing permits', 'Boat and fuel' ],
	],
];

foreach ( $datos as $i => $d ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$filas = pm_filas( 'paquetes', $p->ID );
	$n     = 0;
	foreach ( $filas as &$f ) {
		if ( '' !== trim( (string) ( $f['desde_monto'] ?? '' ) ) || '' !== trim( (string) ( $f['incluye'] ?? '' ) ) ) {
			continue;
		}
		$f['desde_monto']  = 650;
		$f['desde_moneda'] = 'USD';
		$f['desde_unidad'] = $d['unidad'];
		$f['desde_fecha']  = '20261030';
		$f['incluye']      = implode( "\n", $d['incluye'] );
		$f['interno']      = trim( (string) ( $f['interno'] ?? '' ) ) . ' PUBLICADO el 30/10/2026 por decisión del dueño: "desde USD 650 por salida privada de 2 pescadores, jornada completa" (cotización de mercado) y el "incluye" de esa cotización. Las consultas las pasamos a un operador; la propuesta final puede variar.';
		++$n;
	}
	unset( $f );
	WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se cargan' : 'se cargarían' ) . " precio e incluye en $n de " . count( $filas ) . ' paquetes → ' . pm_url_paquetes( $destino, $i ) );
	if ( $aplicar && $n ) {
		update_field( 'paquetes', $filas, $p->ID );
		$leidos = pm_paquetes( $p->ID );
		$l      = $leidos[0] ?? [];
		WP_CLI::log( '  VERIFIED: precio ' . ( ! empty( $l['precio'] ) ? $l['precio']['moneda'] . ' ' . $l['precio']['monto'] . ' ' . $l['precio']['unidad'] . ' (' . $l['precio']['fecha'] . ')' : 'NO' ) . '; incluye ' . count( $l['incluye'] ?? [] ) . ' líneas; el interno no sale: ' . ( false === strpos( wp_json_encode( $leidos ), 'PUBLICADO' ) ? 'sí' : 'NO' ) );
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
