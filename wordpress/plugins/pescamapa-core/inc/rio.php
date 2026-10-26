<?php
/**
 * River conditions: the height of the river (INA a5 API, scales of the Prefectura) and the weather
 * (Open-Meteo) for each destination listed in data/rio.json. The data is fetched every day by the
 * workflow rio.yml (wordpress/build/rio-traer.py) and stored by scripts/rio-guardar.php in the
 * option "pm_rio"; pages only read it. Without data nothing is shown (never an empty box).
 */

defined( 'ABSPATH' ) || exit;

/** Older than this many days the number is still shown, with a warning and its date. */
const PM_RIO_VIEJO_DIAS = 10;

function pm_rio_config(): array {
	static $c = null;
	return $c ??= (array) ( json_decode( file_get_contents( PM_DIR . 'data/rio.json' ), true )['destinos'] ?? [] );
}

function pm_rio_datos(): array {
	$o = get_option( 'pm_rio' );
	return is_array( $o ) ? $o : [];
}

/** Stored conditions of a destination (rio, clima) + when they were captured, or null. */
function pm_rio_destino( string $clave ): ?array {
	$d = pm_rio_datos();
	$x = $d['destinos'][ $clave ] ?? null;
	return $x ? $x + [ 'capturado' => (string) ( $d['capturado'] ?? '' ) ] : null;
}

function pm_url_rio( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['rio'], $i );
}

/** The two source links, for the credit lines (Open-Meteo asks for attribution with a link; INA is the publisher). */
function pm_rio_enlaces(): array {
	return [
		'ina'   => '<a href="https://alerta.ina.gob.ar/" rel="noopener">INA (alerta.ina.gob.ar)</a>',
		'meteo' => '<a href="https://open-meteo.com/" rel="noopener">Open-Meteo.com</a>',
	];
}

/** 1.234,56 in es/pt, 1,234.56 in en. */
function pm_rio_num( $n, int $dec = 2 ): string {
	return 'en' === pm_lang() ? number_format( (float) $n, $dec, '.', ',' ) : number_format( (float) $n, $dec, ',', '.' );
}

/** Where the river stands against the official thresholds: bajo | alerta | evacuacion | ''. */
function pm_rio_estado( array $r ): string {
	if ( ! isset( $r['altura'] ) ) {
		return '';
	}
	if ( isset( $r['evacuacion'] ) && $r['altura'] >= $r['evacuacion'] ) {
		return 'evacuacion';
	}
	if ( isset( $r['alerta'] ) && $r['altura'] >= $r['alerta'] ) {
		return 'alerta';
	}
	return isset( $r['alerta'] ) ? 'bajo' : '';
}

/** Days between a YYYY-MM-DD date and today in Argentina. */
function pm_rio_dias( string $ymd ): int {
	$hoy = new DateTime( 'now', new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
	$dia = new DateTime( $ymd . ' 00:00:00', new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
	return (int) $dia->diff( $hoy->setTime( 0, 0 ) )->format( '%r%a' );
}

/** Forecast rows split at today: [ pasados (last 3 days), proximos ]. */
function pm_rio_clima_partes( array $dias ): array {
	$hoy = ( new DateTime( 'now', new DateTimeZone( 'America/Argentina/Buenos_Aires' ) ) )->format( 'Y-m-d' );
	return [
		array_values( array_filter( $dias, fn( $d ) => $d['fecha'] < $hoy ) ),
		array_values( array_filter( $dias, fn( $d ) => $d['fecha'] >= $hoy ) ),
	];
}

/** Short summary line for a list of weather days: strongest wind and total rain. */
function pm_rio_resumen_clima( array $dias ): string {
	if ( ! $dias ) {
		return '';
	}
	$viento = max( array_map( fn( $d ) => (float) ( $d['viento_max'] ?? 0 ), $dias ) );
	$lluvia = array_sum( array_map( fn( $d ) => (float) ( $d['lluvia'] ?? 0 ), $dias ) );
	return pm_t( 'rio.clima.linea', [ 'v' => pm_rio_num( $viento, 0 ), 'l' => pm_rio_num( $lluvia, 1 ) ] );
}
