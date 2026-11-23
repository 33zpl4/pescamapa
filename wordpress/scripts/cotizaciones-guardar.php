<?php
/**
 * Stores the weekly reference values fetched by wordpress/build/cotizaciones-traer.py (workflow cotizaciones.yml) in the option "pm_cotizaciones"
 * (autoload yes: the licence calculator and the cost estimator read it on every request). Reads /tmp/pm-cotizaciones.json.
 *
 * Validation is pm_cot_validar() (inc/cotizaciones.php), the SAME function the site reads with: rate 100–100000 pesos per US$, fuel price 100–20000 pesos per
 * litre, a real date that is not in the future, an https source URL. A block that fails is rejected and the PREVIOUS value stays; so does one dated older than
 * the stored one. The run fails only when no block passes validation (so a broken source is never shown as "all fine").
 * Dry run first ($aplicar = false); the workflow flips it. Idempotent: the option is replaced whole. Prints values, dates and sources only: nothing personal.
 */

$aplicar = false;
$ruta    = '/tmp/pm-cotizaciones.json';

$datos = is_readable( $ruta ) ? json_decode( file_get_contents( $ruta ), true ) : null;
if ( ! is_array( $datos ) ) {
	WP_CLI::error( "No hay datos válidos en $ruta." );
}

$hoy     = pm_cot_hoy();
$previo  = pm_cot_guardado( null, $hoy ); // blocks already stored and valid (no freshness test)
$final   = array_filter( $previo );
$validos = 0; // blocks that passed validation (stored, or already covered by a newer stored value)
foreach ( [ 'dolar' => PM_COT_DOLAR_DIAS, 'nafta' => PM_COT_NAFTA_DIAS ] as $tipo => $max_dias ) {
	[ $bloque, $error ] = pm_cot_validar( $tipo, $datos[ $tipo ] ?? null, $hoy );
	if ( ! $bloque ) {
		WP_CLI::log( "CONSERVA $tipo anterior: $error" . ( $previo[ $tipo ] ? ' (valor guardado del ' . $previo[ $tipo ]['fecha'] . ')' : ' (no hay valor guardado: el sitio no mostrará esa aproximación)' ) );
		continue;
	}
	++$validos;
	if ( $previo[ $tipo ] && $previo[ $tipo ]['fecha'] > $bloque['fecha'] ) {
		WP_CLI::log( "CONSERVA $tipo anterior: el guardado (" . $previo[ $tipo ]['fecha'] . ") es más nuevo que el leído (" . $bloque['fecha'] . ')' );
		continue;
	}
	$final[ $tipo ] = $bloque;
	$valor = 'dolar' === $tipo ? '$ ' . $bloque['valor'] . ' por US$' : '$ ' . $bloque['precio'] . ' el litro (' . ( $bloque['bandera'] ?: 'todas las banderas' ) . ', ' . $bloque['estaciones'] . ' estaciones)';
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $tipo = $valor, del " . $bloque['fecha'] . ', leído el ' . $bloque['capturado'] . ', fuente ' . $bloque['fuente_url'] );
	if ( ! pm_cot_fresca( $bloque['fecha'], $hoy, $max_dias ) ) {
		WP_CLI::warning( "$tipo ya está vencido por su fecha (más de $max_dias días): el sitio no lo va a usar." );
	}
}
if ( ! $validos ) {
	WP_CLI::error( 'Ningún valor nuevo válido; se conserva lo guardado.' );
}
if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado.' );
	return;
}
$final['actualizado'] = $hoy;
update_option( 'pm_cotizaciones', $final, true );
$leido = pm_cot_guardado( null, $hoy );
$ok    = true;
foreach ( [ 'dolar', 'nafta' ] as $tipo ) {
	$ok = $ok && ( ( $final[ $tipo ] ?? null ) == $leido[ $tipo ] );
}
$ok = $ok && in_array( $GLOBALS['wpdb']->get_var( "SELECT autoload FROM {$GLOBALS['wpdb']->options} WHERE option_name = 'pm_cotizaciones'" ), [ 'yes', 'on', 'auto-on' ], true ); // autoload: the tools read it on every request
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': pm_cotizaciones guardado' . ( isset( $final['dolar'] ) ? ', dólar ' . $final['dolar']['valor'] . ' del ' . $final['dolar']['fecha'] : '' ) . ( isset( $final['nafta'] ) ? ', nafta ' . $final['nafta']['precio'] . ' del ' . $final['nafta']['fecha'] : '' ) );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( 'Cotizaciones guardadas.' );
