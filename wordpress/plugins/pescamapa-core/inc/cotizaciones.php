<?php
/**
 * Weekly reference values for the licence calculator and the cost estimator (23/11/2026).
 *
 * OWNER DECISION (23/11/2026): these two tools show APPROXIMATE conversions, always labelled approximate, dated and sourced. It reverses the earlier
 * «never convert» rule for them (not for the rest of the site). Two values, fetched weekly by wordpress/build/cotizaciones-traer.py (workflow
 * cotizaciones.yml) and stored by wordpress/scripts/cotizaciones-guardar.php in the option `pm_cotizaciones` (same pattern as `pm_rio`):
 *   - dolar: the BCRA's official USD quotation (pesos per US$): valor, nombre, fuente_url, fecha (the rate's date), capturado (fetch date).
 *   - nafta: price per litre of nafta súper at the pump in Corrientes capital (Secretaría de Energía, resolution 314/2016): precio, producto, bandera,
 *     localidad, estaciones, fuente_url, fecha (price date), capturado.
 * A value older than PM_COT_DOLAR_DIAS / PM_COT_NAFTA_DIAS days (by its own date), missing or malformed is NOT used: the tools then show only what the
 * sources publish, as before. Nothing is invented, nothing is cached across requests (tests change the option).
 *
 * The pure helpers take every input as an argument (no WordPress calls except the language/ui helpers of the text functions), so they are unit-tested
 * with `wp eval` (step «Cotizaciones» of test-wp.yml). The arithmetic of the tools stays in pesos/litres/cents (inc/licencia.php): this is a presentation layer.
 */

defined( 'ABSPATH' ) || exit;

const PM_COT_DOLAR_DIAS = 21;
const PM_COT_NAFTA_DIAS = 45;

/** Plausible ranges (pesos per US$, pesos per litre): anything outside is a broken read and is rejected, never stored. */
const PM_COT_DOLAR_RANGO = [ 100, 100000 ];
const PM_COT_NAFTA_RANGO = [ 100, 20000 ];

/** Today's date in Argentina (UTC-3), Y-m-d. */
function pm_cot_hoy(): string {
	return gmdate( 'Y-m-d', time() - 3 * 3600 );
}

/** Whole days from `$fecha` to `$hoy` (negative when `$fecha` is later); null when either is not a Y-m-d date. */
function pm_cot_dias_desde( string $fecha, string $hoy ): ?int {
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $fecha ) || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $hoy ) ) {
		return null;
	}
	$a = strtotime( $fecha . ' 12:00:00 UTC' );
	$b = strtotime( $hoy . ' 12:00:00 UTC' );
	return ( false === $a || false === $b ) ? null : (int) round( ( $b - $a ) / 86400 );
}

/** Is a value dated `$fecha` still usable on `$hoy`? Up to `$max` days old; a date one day ahead (clock skew) passes, further ahead does not. */
function pm_cot_fresca( string $fecha, string $hoy, int $max ): bool {
	$d = pm_cot_dias_desde( $fecha, $hoy );
	return null !== $d && $d >= -1 && $d <= $max;
}

/**
 * Validates one stored block. `$tipo` 'dolar' or 'nafta'. Returns [ clean block or null, error text or '' ]. Used by the reader and by cotizaciones-guardar.php,
 * so what the script accepts is exactly what the site reads.
 */
function pm_cot_validar( string $tipo, $x, ?string $hoy = null ): array {
	$hoy = $hoy ?? pm_cot_hoy();
	if ( ! is_array( $x ) ) {
		return [ null, "$tipo: falta el bloque" ];
	}
	$campo  = 'dolar' === $tipo ? 'valor' : 'precio';
	[ $lo, $hi ] = 'dolar' === $tipo ? PM_COT_DOLAR_RANGO : PM_COT_NAFTA_RANGO;
	$v = $x[ $campo ] ?? null;
	if ( ! is_numeric( $v ) || (float) $v < $lo || (float) $v > $hi ) {
		return [ null, "$tipo: $campo fuera de rango ($lo a $hi)" ];
	}
	$fecha = is_string( $x['fecha'] ?? null ) ? $x['fecha'] : '';
	$d     = pm_cot_dias_desde( $fecha, $hoy );
	if ( null === $d || $d < -1 ) {
		return [ null, "$tipo: fecha inválida o futura ($fecha)" ];
	}
	$url = is_string( $x['fuente_url'] ?? null ) ? $x['fuente_url'] : '';
	if ( ! preg_match( '#^https://[a-z0-9.-]+/#i', $url ) ) {
		return [ null, "$tipo: falta la URL de la fuente" ];
	}
	$cap = is_string( $x['capturado'] ?? null ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $x['capturado'] ) ? $x['capturado'] : $hoy;
	$txt = fn( $k ) => is_string( $x[ $k ] ?? null ) ? mb_substr( trim( $x[ $k ] ), 0, 200 ) : '';
	$out = [ $campo => round( (float) $v, 2 ), 'fecha' => $fecha, 'fuente_url' => $url, 'capturado' => $cap, 'nombre' => $txt( 'nombre' ) ];
	if ( 'nafta' === $tipo ) {
		$out += [ 'producto' => $txt( 'producto' ), 'bandera' => $txt( 'bandera' ), 'localidad' => $txt( 'localidad' ), 'estaciones' => max( 0, (int) ( $x['estaciones'] ?? 0 ) ) ];
	}
	return [ $out, '' ];
}

/** The stored option as it is (validated block by block, no freshness test; a broken option reads as empty): [ 'dolar' => block|null, 'nafta' => block|null ]. */
function pm_cot_guardado( $crudo = null, ?string $hoy = null ): array {
	$o = null === $crudo ? get_option( 'pm_cotizaciones' ) : $crudo; // `$crudo` only in tests and the guardar script: any type, a broken one reads as empty
	$o = is_array( $o ) ? $o : [];
	return [ 'dolar' => pm_cot_validar( 'dolar', $o['dolar'] ?? null, $hoy )[0], 'nafta' => pm_cot_validar( 'nafta', $o['nafta'] ?? null, $hoy )[0] ];
}

/** What the tools may use today: each value only when fresh. [ 'dolar' => block|null, 'nafta' => block|null ]. */
function pm_cotizaciones( $crudo = null, ?string $hoy = null ): array {
	$hoy = $hoy ?? pm_cot_hoy();
	$g   = pm_cot_guardado( $crudo, $hoy );
	return [
		'dolar' => $g['dolar'] && pm_cot_fresca( $g['dolar']['fecha'], $hoy, PM_COT_DOLAR_DIAS ) ? $g['dolar'] : null,
		'nafta' => $g['nafta'] && pm_cot_fresca( $g['nafta']['fecha'], $hoy, PM_COT_NAFTA_DIAS ) ? $g['nafta'] : null,
	];
}

// --- Arithmetic (pure) --------------------------------------------------------------------------

/** Pesos → whole US$ at the stored rate, rounded half up (140000 / 1450 = 96.55 → 97). */
function pm_cot_usd( $ars, float $tc ): int {
	return (int) floor( (float) $ars / $tc + 0.5 );
}

/** Litres × price per litre → pesos, rounded to the nearest 100 (24 × 2177 = 52 248 → 52 200). It is an approximation: the portal sets the final amount. */
function pm_cot_nafta_pesos( $litros, float $precio ): int {
	return (int) ( round( (float) $litros * $precio / 100 ) * 100 );
}

// --- Text (language of the page) -------------------------------------------------------------

/** Number with the language's separators; decimals only when the number has them (24.251,60). */
function pm_cot_num( $v, ?string $idioma = null ): string {
	$idioma = $idioma ?? pm_lang();
	$v      = round( (float) $v, 2 );
	$dec    = abs( $v - round( $v ) ) > 0.001 ? 2 : 0;
	return 'en' === $idioma ? number_format( $v, $dec, '.', ',' ) : number_format( $v, $dec, ',', '.' );
}

/** One convention per language for pesos: es «ARS $ 140.000», pt «ARS 140.000», en «ARS 140,000» (used everywhere in both tools). */
function pm_cot_ars( $v, ?string $idioma = null ): string {
	$idioma = $idioma ?? pm_lang();
	return ( 'es' === $idioma ? 'ARS $ ' : 'ARS ' ) . pm_cot_num( $v, $idioma );
}

/** "US$ 97". */
function pm_cot_usd_txt( int $n, ?string $idioma = null ): string {
	return 'US$ ' . pm_cot_num( $n, $idioma );
}

/** "≈ US$ 97" for an amount in pesos at the rate block; '' without a (fresh) rate. */
function pm_cot_aprox_usd( $ars, ?array $dolar ): string {
	return $dolar ? '≈ ' . pm_cot_usd_txt( pm_cot_usd( $ars, (float) $dolar['valor'] ) ) : '';
}

/** The small line under a result that uses the rate; '' without a (fresh) rate. */
function pm_cot_linea_dolar( ?array $dolar ): string {
	return $dolar ? pm_t( 'cot.dolar.linea', [ 'fecha' => pm_fecha( $dolar['fecha'] ), 'tc' => pm_cot_num( $dolar['valor'] ) ] ) : '';
}
