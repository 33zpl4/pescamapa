<?php
/**
 * Stores the river conditions fetched by wordpress/build/rio-traer.py (workflow rio.yml) in the option
 * "pm_rio". Reads /tmp/pm-rio.json. Validates: only destinations listed in data/rio.json, heights between
 * -2 and 60 m, dates as YYYY-MM-DD; anything else stops the run and the previous data stays.
 * Dry run first ($aplicar = false); the workflow flips it. Idempotent: the option is replaced whole.
 */

$aplicar = false;
$ruta    = '/tmp/pm-rio.json';

$datos = is_readable( $ruta ) ? json_decode( file_get_contents( $ruta ), true ) : null;
if ( ! is_array( $datos ) || empty( $datos['destinos'] ) || empty( $datos['capturado'] ) ) {
	WP_CLI::error( "No hay datos válidos en $ruta." );
}

$validos = array_keys( pm_rio_config() );
$fecha   = fn( $f ) => is_string( $f ) && preg_match( '/^\d{4}-\d{2}-\d{2}$/', $f );
$limpio  = [];
foreach ( $datos['destinos'] as $clave => $it ) {
	if ( ! in_array( $clave, $validos, true ) ) {
		WP_CLI::error( "Destino desconocido en los datos: $clave" );
	}
	$x = [];
	if ( ! empty( $it['rio'] ) ) {
		$r = $it['rio'];
		if ( ! is_numeric( $r['altura'] ?? null ) || $r['altura'] < -2 || $r['altura'] > 60 || ! $fecha( $r['fecha'] ?? null ) ) {
			WP_CLI::error( "$clave: altura o fecha inválidas." );
		}
		$x['rio'] = [
			'estacion'   => (string) $r['estacion'],
			'curso'      => (string) ( $r['curso'] ?? '' ),
			'fecha'      => $r['fecha'],
			'altura'     => (float) $r['altura'],
			'tendencia'  => in_array( $r['tendencia'] ?? null, [ 'sube', 'baja', 'estable' ], true ) ? $r['tendencia'] : null,
			'delta_3d'   => is_numeric( $r['delta_3d'] ?? null ) ? (float) $r['delta_3d'] : null,
			'alerta'     => is_numeric( $r['alerta'] ?? null ) ? (float) $r['alerta'] : null,
			'evacuacion' => is_numeric( $r['evacuacion'] ?? null ) ? (float) $r['evacuacion'] : null,
		];
	}
	if ( ! empty( $it['clima'] ) ) {
		foreach ( $it['clima'] as $d ) {
			if ( ! $fecha( $d['fecha'] ?? null ) ) {
				WP_CLI::error( "$clave: fecha de clima inválida." );
			}
			$x['clima'][] = [
				'fecha'      => $d['fecha'],
				'viento_max' => is_numeric( $d['viento_max'] ?? null ) ? (float) $d['viento_max'] : null,
				'lluvia'     => is_numeric( $d['lluvia'] ?? null ) ? (float) $d['lluvia'] : null,
			];
		}
	}
	if ( $x ) {
		$limpio[ $clave ] = $x;
	}
}

$final = [ 'capturado' => (string) $datos['capturado'], 'destinos' => $limpio ];
foreach ( $limpio as $clave => $x ) {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave — " . ( isset( $x['rio'] ) ? $x['rio']['altura'] . ' m el ' . $x['rio']['fecha'] . ' (' . ( $x['rio']['tendencia'] ?? 's/d' ) . ')' : 'solo clima' ) . ( isset( $x['clima'] ) ? ', clima ' . count( $x['clima'] ) . ' días' : '' ) );
}
if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado.' );
	return;
}
update_option( 'pm_rio', $final, false );
$ok = pm_rio_datos() == $final;
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': pm_rio guardado, capturado ' . $final['capturado'] . ', ' . count( $limpio ) . ' destinos' );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( 'Condiciones del río guardadas.' );
