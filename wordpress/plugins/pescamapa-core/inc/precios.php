<?php
/**
 * «Estimador de costo de un viaje de pesca» (23/11/2026): /cuanto-cuesta-viaje-de-pesca/, /pt/quanto-custa-viagem-de-pesca/, /en/fishing-trip-cost-estimator/
 * (theme view vistas/estimador.php). A free tool: where + format + anglers + companions + days or weeks → a RANGE built only from the dated, sourced
 * prices that Pescamapa has published, with the arithmetic in one line and the way out: «pedí opciones» with the choice carried to the trip form.
 *
 * Data: data/precios.json, the ONE copy, written by wordpress/build/precios-2026-11/generar.py (the same dataset as the unpublished note of 20/11/2026,
 * minus the rows the owner excluded on 23/11). Rules (orchestrator, stated to the owner): ranges of published prices, never "the price" and never an
 * average for Argentina; n is stated for every range; no currency conversion (USD stays USD, pesos stay pesos in their own series); no provider names
 * (the data only carries a place CODE to count how many places a range comes from); a group with fewer than PM_EST_MIN_LUGARES places lists the
 * individual published prices instead of a range.
 * Since 23/11/2026 (owner) a peso amount also gets an APPROXIMATE «≈ US$» next to it at the BCRA's official rate (inc/cotizaciones.php: weekly, dated, sourced,
 * hidden when stale); the published prices themselves are never converted, and USD prices get no peso figure (a lodge's dollar price is not paid at the official rate).
 *
 * The calculation is a pure function (pm_estimador_calcular), no WordPress calls, unit-tested with `wp eval` (see test-wp.yml). Whole-dollar rounding:
 * the unit figures shown in the arithmetic line are the ones multiplied, so the line can be checked by hand.
 */

defined( 'ABSPATH' ) || exit;

/** Formats, in the order of the form. */
const PM_EST_FORMATOS = [ 'dia', 'paquete', 'semana' ];

/** A range needs prices from at least this many places; fewer → the published prices one by one. */
const PM_EST_MIN_LUGARES = 3;

const PM_EST_MAX_PESCADORES   = 8;
const PM_EST_MAX_ACOMPANANTES = 4;

/** Format → [ min, max, default ] of the "how many" field: fishing days (dia, paquete) or weeks (semana). */
const PM_EST_CANTIDAD = [ 'dia' => [ 1, 14, 1 ], 'paquete' => [ 2, 5, 3 ], 'semana' => [ 1, 4, 1 ] ];

/** Budget chips of the trip form (per angler per day), upper limit in USD → key. */
const PM_EST_PRESUPUESTO = [ 300 => 'hasta-300', 600 => '300-600', 1000 => '600-1000' ];

/** The dataset. Empty arrays when the file is missing or malformed (nothing breaks). */
function pm_estimador_datos(): array {
	static $d = null;
	if ( null === $d ) {
		$ruta = PM_DIR . 'data/precios.json';
		$j    = is_readable( $ruta ) ? json_decode( (string) file_get_contents( $ruta ), true ) : null;
		$z    = [];
		foreach ( is_array( $j['zonas'] ?? null ) ? $j['zonas'] : [] as $clave => $c ) {
			$formatos = is_array( $c ) ? array_values( array_intersect( PM_EST_FORMATOS, (array) ( $c['formatos'] ?? [] ) ) ) : [];
			if ( is_string( $clave ) && $formatos ) {
				$z[ $clave ] = [ 'licencia' => is_string( $c['licencia'] ?? null ) ? $c['licencia'] : '', 'formatos' => $formatos ];
			}
		}
		$filas = [];
		foreach ( is_array( $j['filas'] ?? null ) ? $j['filas'] : [] as $f ) {
			if ( is_array( $f ) && isset( $z[ $f['zona'] ?? '' ] ) && in_array( $f['formato'] ?? '', PM_EST_FORMATOS, true ) && is_numeric( $f['monto'] ?? null )
				&& in_array( $f['moneda'] ?? '', [ 'USD', 'ARS' ], true ) && in_array( $f['calc'] ?? '', [ 'bote', 'pescador', 'paquete' ], true ) && is_numeric( $f['dias'] ?? null ) && $f['dias'] > 0 ) {
				$filas[] = $f;
			}
		}
		$d = [
			'zonas'    => $z,
			'destinos' => array_filter( is_array( $j['destinos'] ?? null ) ? $j['destinos'] : [], fn( $zona ) => is_string( $zona ) && isset( $z[ $zona ] ) ),
			'filas'    => $filas,
			'incluye'  => is_array( $j['incluye'] ?? null ) ? $j['incluye'] : [],
			'fechas'   => array_values( array_filter( (array) ( $j['fechas'] ?? [] ), 'is_string' ) ),
			'generado' => is_string( $j['generado'] ?? null ) ? $j['generado'] : '',
		];
	}
	return $d;
}

/** Zones that have data, in the order of the dataset. */
function pm_estimador_zonas(): array {
	return array_keys( pm_estimador_datos()['zonas'] );
}

/** Formats a zone has data for. */
function pm_estimador_formatos( string $zona ): array {
	return pm_estimador_datos()['zonas'][ $zona ]['formatos'] ?? [];
}

/** Zone of a destination guide ('' when the dataset has no unambiguous price for it). Language independent: the dataset's own keys. */
function pm_estimador_zona_de_destino( string $clave ): string {
	return pm_estimador_datos()['destinos'][ $clave ] ?? '';
}

/** The one destination guide of a zone, when the zone has exactly one ('' otherwise): what the trip form can be preselected with. */
function pm_estimador_destino_unico( string $zona ): string {
	$claves = array_keys( array_filter( pm_estimador_datos()['destinos'], fn( $z ) => $z === $zona ) );
	return 1 === count( $claves ) ? (string) $claves[0] : '';
}

/** Published price series of a zone and format: one per currency and unit (USD first, then the longer one). [ [ 'moneda', 'calc', 'filas' ] ]. */
function pm_estimador_series( string $zona, string $formato ): array {
	$por = [];
	foreach ( pm_estimador_datos()['filas'] as $f ) {
		if ( $f['zona'] === $zona && $f['formato'] === $formato ) {
			$por[ $f['moneda'] . '|' . $f['calc'] ][] = $f;
		}
	}
	uksort( $por, fn( $a, $b ) => [ 'USD' === substr( $b, 0, 3 ), count( $por[ $b ] ) ] <=> [ 'USD' === substr( $a, 0, 3 ), count( $por[ $a ] ) ] );
	$out = [];
	foreach ( $por as $k => $filas ) {
		[ $moneda, $calc ] = explode( '|', $k );
		$out[]             = [ 'moneda' => $moneda, 'calc' => $calc, 'filas' => $filas ];
	}
	return $out;
}

/** Whole number inside [min, max]; $def when it is not a plain number (a number outside the range is pulled to the nearest end). */
function pm_estimador_entero( $v, int $min, int $max, int $def ): int {
	if ( is_int( $v ) ) {
		return max( $min, min( $max, $v ) );
	}
	return is_string( $v ) && preg_match( '/^\d{1,3}$/', trim( $v ) ) ? max( $min, min( $max, (int) $v ) ) : $def;
}

/** Round half up to a whole number. */
function pm_estimador_redondear( float $x ): int {
	return (int) floor( $x + 0.5 );
}

/**
 * One price series for a party. Range mode (prices from PM_EST_MIN_LUGARES places or more): unit-price range × units. List mode: every published
 * price with its own total. Units: bote = boats needed (ceil(anglers / boat capacity)) × days; pescador = anglers × days (guided day) or × weeks (lodge week);
 * paquete = anglers × days × the per-fishing-day equivalent of each published package (package price ÷ its fishing days, rounded to the dollar).
 *
 * @return array moneda, calc, modo ('rango'|'lista'), n, lugares, desde/hasta (read dates), unidades, barcos (bote only), min/max (unit price shown in the
 *               arithmetic), total_min/total_max, por_dia_min/por_dia_max (per angler per fishing day), items (list mode), filas.
 */
function pm_estimador_serie( array $s, string $formato, int $pescadores, int $cantidad ): array {
	$filas   = $s['filas'];
	$fechas  = array_values( array_filter( array_map( fn( $f ) => (string) ( $f['fecha'] ?? '' ), $filas ) ) );
	sort( $fechas );
	$lugares = count( array_unique( array_map( fn( $f ) => (string) ( $f['lugar'] ?? '' ), $filas ) ) );
	$calc    = $s['calc'];
	$tope    = fn( $f ) => (float) ( $f['monto_max'] ?? null ) > 0 ? (float) $f['monto_max'] : (float) $f['monto'];
	$cap     = 'bote' === $calc ? max( 1, (int) min( array_map( fn( $f ) => (int) ( $f['capacidad'] ?? 2 ), $filas ) ) ) : 1;
	$barcos  = 'bote' === $calc ? (int) ceil( $pescadores / $cap ) : null;
	$unidades = 'bote' === $calc ? $barcos * $cantidad : $pescadores * $cantidad;
	$r = [
		'moneda'   => $s['moneda'],
		'calc'     => $calc,
		'modo'     => $lugares >= PM_EST_MIN_LUGARES ? 'rango' : 'lista',
		'n'        => count( $filas ),
		'lugares'  => $lugares,
		'desde'    => $fechas[0] ?? '',
		'hasta'    => $fechas ? end( $fechas ) : '',
		'unidades' => $unidades,
		'barcos'   => $barcos,
		'filas'    => $filas,
		'items'    => [],
	];
	// Per angler per fishing day of one published price.
	$por_dia = fn( $monto, $f ) => match ( true ) {
		'bote' === $calc     => pm_estimador_redondear( $barcos * $monto / $pescadores ),
		'pescador' === $calc && 'dia' === $formato => pm_estimador_redondear( $monto ),
		default              => pm_estimador_redondear( $monto / (float) $f['dias'] ),
	};
	if ( 'rango' === $r['modo'] ) {
		if ( 'paquete' === $calc ) { // the unit is the per-fishing-day equivalent of each package
			$lo = pm_estimador_redondear( min( array_map( fn( $f ) => $f['monto'] / $f['dias'], $filas ) ) );
			$hi = pm_estimador_redondear( max( array_map( fn( $f ) => $tope( $f ) / $f['dias'], $filas ) ) );
		} else {
			$lo = pm_estimador_redondear( min( array_column( $filas, 'monto' ) ) );
			$hi = pm_estimador_redondear( max( array_map( $tope, $filas ) ) );
		}
		$r['min']       = $lo;
		$r['max']       = $hi;
		$r['total_min'] = $unidades * $lo;
		$r['total_max'] = $unidades * $hi;
		if ( 'bote' === $calc ) {
			$r['por_dia_min'] = pm_estimador_redondear( $barcos * $lo / $pescadores );
			$r['por_dia_max'] = pm_estimador_redondear( $barcos * $hi / $pescadores );
		} elseif ( 'paquete' === $calc || 'semana' === $formato ) { // each price over its own fishing days
			$r['por_dia_min'] = pm_estimador_redondear( min( array_map( fn( $f ) => $f['monto'] / $f['dias'], $filas ) ) );
			$r['por_dia_max'] = pm_estimador_redondear( max( array_map( fn( $f ) => $tope( $f ) / $f['dias'], $filas ) ) );
		} else {
			$r['por_dia_min'] = $lo;
			$r['por_dia_max'] = $hi;
		}
		return $r;
	}
	usort( $filas, fn( $a, $b ) => [ $a['monto'], (string) $a['id'] ] <=> [ $b['monto'], (string) $b['id'] ] );
	foreach ( $filas as $f ) {
		$u  = 'paquete' === $calc ? $pescadores : $unidades; // a package has a fixed length: the days field does not apply
		$mx = $tope( $f );
		$r['items'][] = [
			'fila'      => $f,
			'unidades'  => $u,
			'total_min' => pm_estimador_redondear( $u * $f['monto'] ),
			'total_max' => pm_estimador_redondear( $u * $mx ),
			'por_dia'   => $por_dia( $f['monto'], $f ),
		];
	}
	$r['total_min']   = min( array_column( $r['items'], 'total_min' ) );
	$r['total_max']   = max( array_column( $r['items'], 'total_max' ) );
	$r['por_dia_min'] = min( array_column( $r['items'], 'por_dia' ) );
	$r['por_dia_max'] = max( array_column( $r['items'], 'por_dia' ) );
	return $r;
}

/**
 * The calculation. Every input is whitelisted: an unknown zone → the first zone with data; a format the zone has no data for → its first format
 * (`ajustes` says so); anglers 1–8 and companions 0–4 (anything not a plain number → 1 and 0); days or weeks inside the range of the format
 * (a number outside it is pulled to the nearest end and `ajustes.cantidad` says so; anything else → the format's default).
 *
 * @return array zona, formato, pescadores, acompanantes, cantidad, ajustes, series (see pm_estimador_serie), desde/hasta (read dates over every series).
 */
function pm_estimador_calcular( string $zona, string $formato, $pescadores = 1, $acompanantes = 0, $cantidad = null ): array {
	$zonas    = pm_estimador_zonas();
	$zona_ok  = in_array( $zona, $zonas, true );
	$zona     = $zona_ok ? $zona : ( $zonas[0] ?? '' );
	$formatos = pm_estimador_formatos( $zona );
	$fmt_ok   = in_array( $formato, $formatos, true );
	$formato  = $fmt_ok ? $formato : ( $formatos[0] ?? 'dia' );
	[ $min, $max, $def ] = PM_EST_CANTIDAD[ $formato ];
	$pesc     = pm_estimador_entero( $pescadores, 1, PM_EST_MAX_PESCADORES, 1 );
	$acomp    = pm_estimador_entero( $acompanantes, 0, PM_EST_MAX_ACOMPANANTES, 0 );
	$cant     = pm_estimador_entero( $cantidad, $min, $max, $def );
	$pedida   = is_int( $cantidad ) || ( is_string( $cantidad ) && preg_match( '/^\d{1,3}$/', trim( $cantidad ) ) );
	$series   = [];
	foreach ( $zona ? pm_estimador_series( $zona, $formato ) : [] as $s ) {
		$series[] = pm_estimador_serie( $s, $formato, $pesc, $cant );
	}
	$fechas = array_values( array_filter( array_merge( array_column( $series, 'desde' ), array_column( $series, 'hasta' ) ) ) );
	sort( $fechas );
	return [
		'zona'         => $zona,
		'formato'      => $formato,
		'pescadores'   => $pesc,
		'acompanantes' => $acomp,
		'cantidad'     => $cant,
		'ajustes'      => [ 'zona' => ! $zona_ok && '' !== $zona, 'formato' => ! $fmt_ok, 'cantidad' => $pedida && (int) $cantidad !== $cant ],
		'series'       => $series,
		'desde'        => $fechas[0] ?? '',
		'hasta'        => $fechas ? end( $fechas ) : '',
	];
}

// --- Input, destinations, links ----------------------------------------------------------

/**
 * What the visitor asked for, from GET (or an array in tests). `enviado` is true only when the form was sent (a valid place and a format are there);
 * a link from a guide that only preselects the destination shows the form, not a result. Nothing here trusts the URL.
 */
function pm_estimador_entrada( ?array $get = null ): array {
	$get   ??= $_GET; // phpcs:ignore WordPress.Security.NonceVerification -- read-only calculator, nothing is saved
	$texto = fn( $k ) => is_string( $get[ $k ] ?? null ) ? strtolower( trim( $get[ $k ] ) ) : '';
	$zona    = '';
	$destino = '';
	if ( preg_match( '/^(zona|destino):([a-z0-9-]{1,60})$/', $texto( 'donde' ), $m ) ) {
		if ( 'zona' === $m[1] && in_array( $m[2], pm_estimador_zonas(), true ) ) {
			$zona = $m[2];
		} elseif ( 'destino' === $m[1] && '' !== pm_estimador_zona_de_destino( $m[2] ) ) {
			$destino = $m[2];
			$zona    = pm_estimador_zona_de_destino( $m[2] );
		}
	}
	$formato = in_array( $texto( 'formato' ), PM_EST_FORMATOS, true ) ? $texto( 'formato' ) : '';
	return [
		'enviado'      => '' !== $zona && '' !== $formato,
		'zona'         => $zona,
		'destino'      => $destino,
		'formato'      => $formato,
		'pescadores'   => pm_estimador_entero( $get['pescadores'] ?? null, 1, PM_EST_MAX_PESCADORES, 1 ),
		'acompanantes' => pm_estimador_entero( $get['acompanantes'] ?? null, 0, PM_EST_MAX_ACOMPANANTES, 0 ),
		'dias'         => $get['dias'] ?? null,
		'semanas'      => $get['semanas'] ?? null,
	];
}

/** The "how many" value of a request for its format: weeks for a lodge week, fishing days otherwise (raw, calcular() whitelists it). */
function pm_estimador_cantidad_pedida( array $e ) {
	return 'semana' === $e['formato'] ? $e['semanas'] : $e['dias'];
}

/** Destination guides the dataset has an unambiguous price zone for (name and province from the guide: no list typed here). [clave, nombre, provincia, zona]. */
function pm_estimador_destinos( string $idioma ): array {
	static $cache = [];
	if ( ! isset( $cache[ $idioma ] ) ) {
		$out = [];
		foreach ( pm_destinos_lista( $idioma ) as $d ) {
			$zona = pm_estimador_zona_de_destino( (string) ( $d['clave'] ?? '' ) );
			if ( '' !== $zona ) {
				$out[] = [ 'clave' => (string) $d['clave'], 'nombre' => (string) $d['nombre'], 'provincia' => (string) ( PM_PROVINCIAS[ $d['provincia'] ?? '' ] ?? '' ), 'zona' => $zona ];
			}
		}
		$cache[ $idioma ] = $out;
	}
	return $cache[ $idioma ];
}

/** Link to the estimator, with a destination or a zone preselected (shows the form, no result). */
function pm_estimador_url( string $zona = '', string $destino = '' ): string {
	$base = pm_url_estimador( pm_lang() );
	if ( '' !== $destino ) {
		return $base . '?donde=destino:' . rawurlencode( $destino ) . '#calculadora';
	}
	return '' !== $zona ? $base . '?donde=zona:' . rawurlencode( $zona ) . '#calculadora' : $base;
}

/** The licence calculator with the region preselected when the choice maps to one (a destination guide first, then the zone; several provinces → none). */
function pm_estimador_licencia_url( string $zona, string $destino = '' ): string {
	if ( '' !== $destino && '' !== pm_licencia_region_de_destino( $destino ) ) {
		return pm_licencia_url( '', $destino );
	}
	$region = (string) ( pm_estimador_datos()['zonas'][ $zona ]['licencia'] ?? '' );
	return '' !== $region ? pm_licencia_url( $region ) : pm_licencia_url();
}

/** Key of the trip form's "days of fishing" chips for a number of fishing days. */
function pm_estimador_estadia( int $dias ): string {
	return $dias <= 1 ? 'uno' : ( $dias <= 3 ? 'dos-tres' : ( $dias <= 5 ? 'cuatro-cinco' : ( $dias <= 7 ? 'semana' : 'mas' ) ) );
}

/** Key of the trip form's budget chips (per angler per day, USD) for the middle of the estimate's per-day range; '' when the estimate is in pesos. */
function pm_estimador_presupuesto( array $res ): string {
	$s = $res['series'][0] ?? null;
	if ( ! $s || 'USD' !== $s['moneda'] ) {
		return '';
	}
	$medio = ( $s['por_dia_min'] + $s['por_dia_max'] ) / 2;
	foreach ( PM_EST_PRESUPUESTO as $tope => $clave ) {
		if ( $medio <= $tope ) {
			return $clave;
		}
	}
	return 'mas-1000';
}

/** Parameters the trip form is opened with: what the visitor chose (destination, anglers, companions, days of fishing, budget chip). */
function pm_estimador_viaje_params( array $res, string $destino = '' ): array {
	$destino = '' !== $destino ? $destino : pm_estimador_destino_unico( $res['zona'] );
	$dias    = 'semana' === $res['formato'] ? 6 * $res['cantidad'] : $res['cantidad'];
	$p       = [ 'destino' => $destino, 'pescadores' => $res['pescadores'], 'acompanantes' => $res['acompanantes'] ?: '', 'estadia' => pm_estimador_estadia( $dias ), 'presupuesto' => pm_estimador_presupuesto( $res ) ];
	return array_filter( $p, fn( $v ) => '' !== $v && null !== $v );
}

function pm_estimador_viaje_url( array $res, string $destino = '' ): string {
	return add_query_arg( array_map( 'strval', pm_estimador_viaje_params( $res, $destino ) ), pm_url_viaje( pm_lang() ) );
}

// --- Text ------------------------------------------------------------------------------------

/** Number with the separators of the page's language; one decimal only when it has one (2,5 días de pesca). */
function pm_estimador_num( $v ): string {
	$dec = abs( (float) $v - round( (float) $v ) ) > 0.001 ? 1 : 0;
	return 'en' === pm_lang() ? number_format( (float) $v, $dec, '.', ',' ) : number_format( (float) $v, $dec, ',', '.' );
}

/** "US$ 3.120" / "ARS $ 666.000" (the site's money helper for dollars; pesos with the licence tool's convention, pm_cot_ars). Never a conversion. */
function pm_estimador_monto( $v, string $moneda ): string {
	return 'ARS' === $moneda ? pm_cot_ars( $v ) : pm_paquete_precio_txt( [ 'monto' => $v, 'moneda' => $moneda ], pm_lang() );
}

/** "≈ US$ 2.050–2.760" (or "≈ US$ 97" when both ends are the same) for a range in pesos at the fresh rate; '' without a rate. Each end is rounded to whole dollars. */
function pm_estimador_usd_rango( $lo, $hi, ?array $dolar ): string {
	if ( ! $dolar ) {
		return '';
	}
	$a = pm_cot_usd( $lo, (float) $dolar['valor'] );
	$b = pm_cot_usd( $hi, (float) $dolar['valor'] );
	return '≈ ' . pm_cot_usd_txt( $a ) . ( $a === $b ? '' : '–' . pm_cot_num( $b ) );
}

/** "US$ 520–690" (one symbol, the en dash between the numbers) or "US$ 520" when both ends are equal. */
function pm_estimador_rango( $lo, $hi, string $moneda ): string {
	return $lo === $hi ? pm_estimador_monto( $lo, $moneda ) : pm_estimador_monto( $lo, $moneda ) . '–' . pm_estimador_num( $hi );
}

/** "1 pescador", "3 pescadores". */
function pm_estimador_pescadores_txt( int $n ): string {
	return pm_t( 1 === $n ? 'est.pescadores.uno' : 'est.pescadores.n', [ 'n' => (string) $n ] );
}

function pm_estimador_acompanantes_txt( int $n ): string {
	return pm_t( 1 === $n ? 'est.acomp.uno' : 'est.acomp.n', [ 'n' => (string) $n ] );
}

/** "3 días" / "2 semanas" for the amount of the chosen format. */
function pm_estimador_cantidad_txt( string $formato, int $n ): string {
	if ( 'semana' === $formato ) {
		return pm_t( 1 === $n ? 'est.semana.uno' : 'est.semana.n', [ 'n' => (string) $n ] );
	}
	return pm_licencia_dias_txt( $n );
}

/** "1 bote", "2 botes". */
function pm_estimador_botes_txt( int $n ): string {
	return pm_t( 1 === $n ? 'est.bote.uno' : 'est.bote.n', [ 'n' => (string) $n ] );
}

/** Read dates: "leídos entre el 31 de octubre y el 11 de noviembre de 2026" or "leído el …" when they coincide. */
function pm_estimador_leido( string $desde, string $hasta ): string {
	return $desde === $hasta ? pm_t( 'est.leido.uno', [ 'fecha' => pm_fecha( $desde ) ] ) : pm_t( 'est.leido.rango', [ 'desde' => pm_fecha( $desde ), 'hasta' => pm_fecha( $hasta ) ] );
}

/** The arithmetic of a range-mode series in one line: "2 botes × 3 días × US$ 520–690 por bote y por día = US$ 3.120–4.140". */
function pm_estimador_aritmetica( array $s, string $formato, int $pescadores, int $cantidad ): string {
	$v = [
		'barcos'     => pm_estimador_botes_txt( (int) $s['barcos'] ),
		'pescadores' => pm_estimador_pescadores_txt( $pescadores ),
		'cantidad'   => pm_estimador_cantidad_txt( $formato, $cantidad ),
		'precio'     => pm_estimador_rango( $s['min'], $s['max'], $s['moneda'] ),
		'total'      => pm_estimador_rango( $s['total_min'], $s['total_max'], $s['moneda'] ),
	];
	$clave = 'bote' === $s['calc'] ? 'bote' : ( 'paquete' === $s['calc'] ? 'paquete' : ( 'semana' === $formato ? 'semana' : 'dia' ) );
	return pm_t( "est.ar.$clave", $v );
}

/** Label of a published package or day in list mode, without any name: "3 noches, 2,5 días de pesca" / "Día con guía" / "Semana de 7 noches". */
function pm_estimador_item_etiqueta( array $f, string $formato ): string {
	if ( 'paquete' === $formato ) {
		$dias = pm_estimador_num( $f['dias'] );
		return ! empty( $f['noches'] ) ? pm_t( 'est.item.paquete', [ 'noches' => pm_estimador_num( $f['noches'] ), 'dias' => $dias ] ) : pm_t( 'est.item.paquete.sn', [ 'dias' => $dias ] );
	}
	return pm_t( 'semana' === $formato ? 'est.item.semana' : 'est.item.dia' );
}

/** Unit a published price is quoted in ("por bote para 1 o 2 pescadores"). */
function pm_estimador_unidad_txt( string $calc, string $formato ): string {
	return pm_t( 'bote' === $calc ? 'est.unidad.bote' : ( 'paquete' === $calc ? 'est.unidad.paquete' : ( 'semana' === $formato ? 'est.unidad.semana' : 'est.unidad.dia' ) ) );
}

/** One published price of list mode as a line: "3 noches, 2,5 días de pesca: desde US$ 1.260 por persona (leído el 5 de noviembre de 2026)". */
function pm_estimador_item_txt( array $it, array $s, string $formato ): string {
	$f     = $it['fila'];
	$monto = ( ! empty( $f['desde'] ) ? pm_t( 'est.desde' ) . ' ' : '' ) . pm_estimador_rango( $f['monto'], (float) ( $f['monto_max'] ?? 0 ) > 0 ? $f['monto_max'] : $f['monto'], $f['moneda'] );
	return pm_t( 'est.item', [ 'etiqueta' => pm_estimador_item_etiqueta( $f, $formato ), 'monto' => $monto, 'unidad' => pm_estimador_unidad_txt( $s['calc'], $formato ), 'fecha' => pm_fecha( (string) $f['fecha'] ) ] );
}

/** The arithmetic of one published price in list mode: "2 botes × 3 días × $ 666.000 = $ 3.996.000" (a package: "2 pescadores × US$ 1.260 = US$ 2.520"). */
function pm_estimador_item_cuenta( array $it, array $s, string $formato, int $pescadores, int $cantidad, ?array $dolar = null ): string {
	$f = $it['fila'];
	$v = [
		'barcos'     => pm_estimador_botes_txt( (int) $s['barcos'] ),
		'pescadores' => pm_estimador_pescadores_txt( $pescadores ),
		'cantidad'   => pm_estimador_cantidad_txt( $formato, $cantidad ),
		'precio'     => pm_estimador_rango( $f['monto'], (float) ( $f['monto_max'] ?? 0 ) > 0 ? $f['monto_max'] : $f['monto'], $f['moneda'] ),
		'total'      => pm_estimador_rango( $it['total_min'], $it['total_max'], $f['moneda'] ),
	];
	$clave = 'bote' === $s['calc'] ? 'bote' : ( 'paquete' === $s['calc'] ? 'paquete' : ( 'semana' === $formato ? 'semana' : 'dia' ) );
	$usd   = 'ARS' === $f['moneda'] ? pm_estimador_usd_rango( $it['total_min'], $it['total_max'], $dolar ) : '';
	return pm_t( "est.ar.item.$clave", $v ) . ( '' !== $usd ? " ($usd)" : '' );
}

/**
 * Where, for the headline. A destination guide that is the ONLY one of its zone (Bariloche, El Calafate) keeps its name. A destination whose zone has several
 * (Río Gallegos → southern Patagonia) names the zone the prices come from: "lodges de la Patagonia sur (zona de Río Gallegos)" — the range is the zone's, not the town's.
 * No destination: the short name of the zone.
 */
function pm_estimador_donde_txt( string $zona, string $destino_nombre = '' ): string {
	$corto = pm_t( "est.zona.$zona.corto" );
	if ( '' === $destino_nombre ) {
		return $corto;
	}
	if ( '' !== pm_estimador_destino_unico( $zona ) || false !== mb_stripos( $corto, $destino_nombre ) ) {
		return $destino_nombre; // one guide in the zone, or the zone's own name already says it
	}
	return pm_t( 'est.donde.zona', [ 'zona' => $corto, 'destino' => $destino_nombre ] );
}

/** True when the headline names a zone because the chosen destination is one of several in it (the "sale de N precios" line then says "de la zona"). */
function pm_estimador_es_de_zona( string $zona, string $destino_nombre ): bool {
	return '' !== $destino_nombre && '' === pm_estimador_destino_unico( $zona ) && false === mb_stripos( pm_t( "est.zona.$zona.corto" ), $destino_nombre );
}

/**
 * What a format usually includes, counted from the dataset's reading of the listings (resumen.json `incluye_*`): [ 'tipo' => 'dia'|'lodge'|'', 'items' => [ [ key, count ] ], 'fichas' => n ].
 * Only the guided day near Bariloche and the lodge week have such a count; anything else returns tipo ''.
 */
function pm_estimador_incluye( string $zona, string $formato ): array {
	$inc = pm_estimador_datos()['incluye'];
	if ( 'bariloche' === $zona && 'dia' === $formato ) {
		$c = (array) ( $inc['dia_bote'] ?? [] );
		$k = [ 'equipo', 'comida', 'traslados', 'bebidas', 'licencia' ];
		$t = 'dia';
	} elseif ( 'semana' === $formato ) {
		$c = (array) ( $inc['semana'] ?? [] );
		$k = [ 'alojamiento', 'comida', 'bebidas', 'traslados', 'equipos', 'licencia' ];
		$t = 'lodge';
	} else {
		return [ 'tipo' => '', 'items' => [], 'fichas' => 0 ];
	}
	$fichas = (int) ( $c['fichas'] ?? 0 );
	if ( $fichas < 1 ) {
		return [ 'tipo' => '', 'items' => [], 'fichas' => 0 ];
	}
	$items = [];
	foreach ( $k as $clave ) {
		if ( isset( $c[ $clave ] ) ) {
			$items[] = [ $clave, (int) $c[ $clave ] ];
		}
	}
	return [ 'tipo' => $t, 'items' => $items, 'fichas' => $fichas ];
}

/** The line "Qué suele incluir" for a zone and format, with the counts "en 5 de 5" taken from the data. '' when there is no count. */
function pm_estimador_incluye_txt( string $zona, string $formato ): string {
	$i = pm_estimador_incluye( $zona, $formato );
	if ( '' === $i['tipo'] ) {
		return '';
	}
	$con  = [];
	$sin  = [];
	foreach ( $i['items'] as [ $clave, $n ] ) {
		$nombre = pm_t( "est.inc.$clave" );
		if ( 0 === $n ) {
			$sin[] = $nombre;
		} else {
			$con[] = pm_t( 'est.inc.cuenta', [ 'cosa' => $nombre, 'n' => (string) $n, 'de' => (string) $i['fichas'] ] );
		}
	}
	$t = pm_t( "est.inc.{$i['tipo']}", [ 'fichas' => (string) $i['fichas'], 'lista' => implode( '; ', $con ) ] );
	return $sin ? $t . ' ' . pm_t( 'est.inc.ninguna', [ 'cosa' => implode( ', ', $sin ) ] ) : $t;
}

/** Read dates of the whole dataset: [ earliest, latest ] (Y-m-d). */
function pm_estimador_fechas(): array {
	$f = pm_estimador_datos()['fechas'];
	if ( 2 === count( $f ) ) {
		return [ $f[0], $f[1] ];
	}
	$todas = array_filter( array_map( fn( $x ) => (string) ( $x['fecha'] ?? '' ), pm_estimador_datos()['filas'] ) );
	return $todas ? [ min( $todas ), max( $todas ) ] : [ '', '' ];
}

/** How many published prices of which kind of source stand behind the tool (own site read by us / sent by the place / a platform or agency listing). */
function pm_estimador_fuentes(): array {
	$c = [ 'propia' => 0, 'programa' => 0, 'listado' => 0 ];
	foreach ( pm_estimador_datos()['filas'] as $f ) {
		if ( isset( $c[ $f['fuente'] ?? '' ] ) ) {
			++$c[ $f['fuente'] ];
		}
	}
	return $c;
}

/**
 * Reference table of every zone × format × currency: what the published prices are (range or the prices one by one) and how many there are.
 * Rows: zona, formato (text), valores, n (text), leido (text).
 */
function pm_estimador_tabla(): array {
	$out = [];
	foreach ( pm_estimador_zonas() as $z ) {
		foreach ( pm_estimador_formatos( $z ) as $fmt ) {
			foreach ( pm_estimador_series( $z, $fmt ) as $s ) {
				$r = pm_estimador_serie( $s, $fmt, 2, PM_EST_CANTIDAD[ $fmt ][2] );
				if ( 'rango' === $r['modo'] ) {
					if ( 'bote' === $s['calc'] ) { // per boat, from the published figures themselves
						$val = pm_t( 'est.tabla.bote', [ 'rango' => pm_estimador_rango( $r['min'], $r['max'], $s['moneda'] ) ] );
					} elseif ( 'paquete' === $s['calc'] ) {
						$val = pm_t( 'est.tabla.paquete', [ 'rango' => pm_estimador_rango( $r['min'], $r['max'], $s['moneda'] ) ] );
					} else {
						$val = pm_t( 'est.tabla.semana', [ 'rango' => pm_estimador_rango( $r['min'], $r['max'], $s['moneda'] ), 'dia' => pm_estimador_rango( $r['por_dia_min'], $r['por_dia_max'], $s['moneda'] ) ] );
					}
				} else {
					$ord    = $r['filas'];
					usort( $ord, fn( $a, $b ) => $a['monto'] <=> $b['monto'] );
					$montos = array_map( fn( $f ) => pm_estimador_monto( $f['monto'], $f['moneda'] ), $ord );
					$val    = pm_t( 'est.tabla.lista', [ 'montos' => implode( ' · ', array_unique( $montos ) ), 'unidad' => pm_estimador_unidad_txt( $s['calc'], $fmt ) ] );
				}
				$out[] = [
					'zona'    => $z,
					'formato' => pm_t( "est.formato.$fmt" ) . ( 'ARS' === $s['moneda'] ? ' · ' . pm_t( 'est.tabla.pesos' ) : '' ),
					'valores' => $val,
					'n'       => 1 === $r['n'] ? pm_t( 'est.tabla.n.uno' ) : ( 1 === $r['lugares'] ? pm_t( 'est.tabla.n.unlugar', [ 'n' => (string) $r['n'] ] ) : pm_t( 'est.tabla.n', [ 'n' => (string) $r['n'], 'lugares' => (string) $r['lugares'] ] ) ),
					'leido'   => $r['desde'] === $r['hasta'] ? pm_fecha( $r['desde'] ) : pm_t( 'est.tabla.leido.rango', [ 'desde' => pm_fecha( $r['desde'] ), 'hasta' => pm_fecha( $r['hasta'] ) ] ),
				];
			}
		}
	}
	return $out;
}

/** FAQ as [ pregunta, respuesta ] pairs; the amounts and counts come from the dataset, so they follow the next refresh. */
function pm_estimador_faqs(): array {
	$serie = function ( string $zona, string $fmt, string $moneda = 'USD' ): array {
		foreach ( pm_estimador_series( $zona, $fmt ) as $s ) {
			if ( $s['moneda'] === $moneda ) {
				return pm_estimador_serie( $s, $fmt, 2, PM_EST_CANTIDAD[ $fmt ][2] );
			}
		}
		return [ 'min' => 0, 'max' => 0, 'n' => 0, 'lugares' => 0, 'moneda' => $moneda, 'desde' => '', 'hasta' => '' ];
	};
	$rango = fn( array $r ) => pm_estimador_rango( $r['min'] ?? 0, $r['max'] ?? 0, $r['moneda'] );
	$bar   = $serie( 'bariloche', 'dia' );
	$cor   = $serie( 'corrientes', 'semana' );
	$pn    = $serie( 'patagonia-norte', 'semana' );
	$ps    = $serie( 'patagonia-sur', 'semana' );
	$inc   = pm_estimador_datos()['incluye'];
	[ $desde, $hasta ] = pm_estimador_fechas();
	$v = [
		'bar' => $rango( $bar ), 'bar_n' => $bar['n'] ?? 0, 'cor' => $rango( $cor ), 'cor_n' => $cor['n'] ?? 0, 'pn' => $rango( $pn ), 'pn_n' => $pn['n'] ?? 0, 'ps' => $rango( $ps ), 'ps_n' => $ps['n'] ?? 0,
		'dia_fichas' => (int) ( $inc['dia_bote']['fichas'] ?? 0 ), 'dia_lic' => (int) ( $inc['dia_bote']['licencia'] ?? 0 ), 'lod_fichas' => (int) ( $inc['semana']['fichas'] ?? 0 ), 'lod_lic' => (int) ( $inc['semana']['licencia'] ?? 0 ),
		'desde' => pm_fecha( $desde ), 'hasta' => pm_fecha( $hasta ),
		'inc_dia' => pm_estimador_incluye_txt( 'bariloche', 'dia' ), 'inc_lodge' => pm_estimador_incluye_txt( 'corrientes', 'semana' ),
	];
	$faqs = [];
	foreach ( [ 1, 2, 3, 4 ] as $n ) {
		$faqs[] = [ 'pregunta' => pm_t( "est.faq.$n.q" ), 'respuesta' => pm_t( "est.faq.$n.a", $v ) ];
	}
	return $faqs;
}
