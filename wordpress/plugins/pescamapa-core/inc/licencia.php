<?php
/**
 * «Calculadora de licencia de pesca» (23/11/2026): /calculadora-licencia-de-pesca/, /pt/calculadora-licenca-de-pesca/, /en/fishing-licence-calculator/
 * (theme view vistas/licencia.php). A free tool: where you fish + who you are + how many days → the permit options with the price AS PUBLISHED
 * and the cheapest valid combination, computed from data/licencias.json (ONE copy, written by wordpress/build/licencias-2026-27/generar-datos.py).
 *
 * Rules (owner): the published values are never altered; a value the source does not give is said so. Since 23/11/2026 (owner) the RESULT also shows
 * approximations, always labelled approximate, dated and sourced (inc/cotizaciones.php): the amount in US$ at the BCRA's official rate, and Corrientes'
 * litres of nafta súper as pesos at the pump price in Corrientes capital. They are a presentation layer on top of the calculation; without a fresh
 * reference value the tool shows what the sources publish, as before.
 * The calculation is a pure function (pm_licencia_calcular) with no WordPress calls, so it is unit-tested with `wp eval`.
 * Amounts are handled in integer cents (Entre Ríos has 24251.60) and handed back as int when whole, float otherwise.
 */

defined( 'ABSPATH' ) || exit;

/** Regions of the dataset, in the order of the form. */
const PM_LICENCIA_REGIONES = [ 'patagonia', 'santa-cruz', 'tierra-del-fuego', 'corrientes', 'entre-rios', 'buenos-aires', 'santa-fe' ];

/** Province of a destination guide (PM_PROVINCIAS key) → region of the dataset. The permit is issued per jurisdiction, so this is a fact, not a list of places. */
const PM_LICENCIA_PROVINCIAS = [
	'neuquen' => 'patagonia', 'rio-negro' => 'patagonia', 'chubut' => 'patagonia',
	'santa-cruz' => 'santa-cruz', 'tierra-del-fuego' => 'tierra-del-fuego', 'corrientes' => 'corrientes',
	'entre-rios' => 'entre-rios', 'buenos-aires' => 'buenos-aires', 'santa-fe' => 'santa-fe',
];

/** Buyer categories, in the order the form lists them. */
const PM_LICENCIA_QUIENES = [ 'extranjero', 'limitrofe', 'residente-pais', 'residente-otra', 'residente-prov', 'joven', 'exento', 'menor14', 'jubilado', 'general' ];

/** Add-on permits (they are bought on top of the ordinary one). */
const PM_LICENCIA_EXTRAS = [ 'trolling', 'zona-grande', 'zona-ewan' ];

/** The whole dataset: ['filas' => [...], 'donde' => [region => [[nombre, url]]]]. Empty arrays when the file is missing (nothing breaks). */
function pm_licencia_datos(): array {
	static $d = null;
	if ( null === $d ) {
		$ruta = PM_DIR . 'data/licencias.json';
		$j    = is_readable( $ruta ) ? json_decode( (string) file_get_contents( $ruta ), true ) : null;
		$d    = [
			'filas' => is_array( $j['filas'] ?? null ) ? array_values( array_filter( $j['filas'], 'is_array' ) ) : [],
			'donde' => is_array( $j['donde'] ?? null ) ? $j['donde'] : [],
		];
	}
	return $d;
}

/** Rows of one region, base permits (`$extra` false) or add-ons (true). */
function pm_licencia_filas( string $region, bool $extra = false ): array {
	return array_values( array_filter( pm_licencia_datos()['filas'], fn( $f ) => ( $f['region_clave'] ?? '' ) === $region && ( 'extra' === ( $f['tipo'] ?? '' ) ) === $extra ) );
}

/** Buyer categories that exist for a region (they have a base row: a price, a free category or a "no value read"). */
function pm_licencia_quienes( string $region ): array {
	$hay = array_unique( array_map( fn( $f ) => (string) ( $f['quien_clave'] ?? '' ), pm_licencia_filas( $region ) ) );
	return array_values( array_filter( PM_LICENCIA_QUIENES, fn( $q ) => in_array( $q, $hay, true ) ) );
}

/** Add-ons a buyer of this category can add in this region. */
function pm_licencia_extras( string $region, string $quien ): array {
	$out = [];
	foreach ( pm_licencia_filas( $region, true ) as $f ) {
		if ( in_array( $quien, (array) ( $f['aplica_a'] ?? [] ), true ) ) {
			$out[] = (string) ( $f['extra_clave'] ?? '' );
		}
	}
	return array_values( array_filter( PM_LICENCIA_EXTRAS, fn( $e ) => in_array( $e, $out, true ) ) );
}

/** 1–30 or 'temporada'; anything else → 1. */
function pm_licencia_dias( $dias ) {
	if ( 'temporada' === $dias ) {
		return 'temporada';
	}
	if ( is_int( $dias ) || ( is_string( $dias ) && ctype_digit( $dias ) && strlen( $dias ) <= 3 ) ) {
		$n = (int) $dias;
		return ( $n >= 1 && $n <= 30 ) ? $n : 1;
	}
	return 1;
}

function pm_licencia_centavos( $valor ): int {
	return (int) round( (float) $valor * 100 );
}

/** Cents → int when whole, float otherwise. */
function pm_licencia_num( int $c ) {
	return 0 === $c % 100 ? intdiv( $c, 100 ) : $c / 100;
}

/**
 * A purchase plan: [[tramo, cantidad], ...] → items, total and number of permits.
 * `tramo` = ['fila' => row, 'dias' => int|'temporada', 'cents' => int].
 */
function pm_licencia_plan( array $partes ): array {
	$items = [];
	$total = 0;
	$n     = 0;
	foreach ( $partes as [ $t, $cant ] ) {
		$sub     = $t['cents'] * $cant;
		$total  += $sub;
		$n      += $cant;
		$items[] = [
			'periodo'  => (string) ( $t['fila']['periodo'] ?? '' ),
			'dias'     => $t['dias'],
			'cant'     => $cant,
			'valor'    => pm_licencia_num( $t['cents'] ),
			'subtotal' => pm_licencia_num( $sub ),
			'fila'     => (int) ( $t['fila']['id'] ?? 0 ),
		];
	}
	return [ 'items' => $items, 'total' => pm_licencia_num( $total ), 'cents' => $total, 'permisos' => $n ];
}

/**
 * Cheapest way to be covered for `$dias` days with the permits of `$filas` (one region, one buyer, one permit family).
 * Permits of N days can be bought any number of times; a season permit covers everything; a "fin de semana" permit is only considered for exactly 2 days.
 * Dynamic programming over the days, ties broken by fewer permits. The days are assumed to fall inside the period each permit covers.
 *
 * @return array ['estado' => 'ok'|'sin_cobertura', 'mejor' => plan|null, 'planes' => plans sorted by price, 'referencia' => the shortest permit repeated, 'ahorro' => number]
 */
function pm_licencia_cubrir( array $filas, $dias ): array {
	$tramos    = [];
	$temporada = null;
	$finde     = null;
	foreach ( $filas as $f ) {
		if ( ! in_array( $f['tipo'] ?? '', [ 'permiso', 'extra' ], true ) ) { // a priced row: ordinary permit or add-on
			continue;
		}
		$d = $f['dias'] ?? null;
		$t = [ 'fila' => $f, 'dias' => $d, 'cents' => pm_licencia_centavos( $f['valor'] ?? 0 ) ];
		if ( is_int( $d ) && $d > 0 ) {
			$tramos[] = $t;
		} elseif ( 'temporada' === $d && ( ! $temporada || $t['cents'] < $temporada['cents'] ) ) {
			$temporada = $t;
		} elseif ( 'fin-de-semana' === $d ) {
			$finde = [ 'dias' => 2 ] + $t;
		}
	}
	usort( $tramos, fn( $a, $b ) => $a['dias'] <=> $b['dias'] );
	$vacio = [ 'estado' => 'sin_cobertura', 'mejor' => null, 'planes' => [], 'referencia' => null, 'ahorro' => 0 ];

	if ( 'temporada' === $dias ) {
		if ( ! $temporada ) {
			return $vacio;
		}
		$p = pm_licencia_plan( [ [ $temporada, 1 ] ] );
		return [ 'estado' => 'ok', 'mejor' => $p, 'planes' => [ $p ], 'referencia' => null, 'ahorro' => 0 ];
	}

	$n         = (int) $dias;
	$candidatos = [];
	if ( $tramos ) {
		// best[k] = [cents, permits, tramo index] to cover k days.
		$best = [ 0 => [ 0, 0, -1 ] ];
		for ( $k = 1; $k <= $n; $k++ ) {
			$mejor_k = null;
			foreach ( $tramos as $i => $t ) {
				$prev = $best[ max( 0, $k - $t['dias'] ) ];
				$c    = [ $prev[0] + $t['cents'], $prev[1] + 1, $i ];
				if ( null === $mejor_k || $c[0] < $mejor_k[0] || ( $c[0] === $mejor_k[0] && $c[1] < $mejor_k[1] ) ) {
					$mejor_k = $c;
				}
			}
			$best[ $k ] = $mejor_k;
		}
		$cuenta = [];
		for ( $k = $n; $k > 0; $k = max( 0, $k - $tramos[ $best[ $k ][2] ]['dias'] ) ) {
			$i            = $best[ $k ][2];
			$cuenta[ $i ] = ( $cuenta[ $i ] ?? 0 ) + 1;
		}
		krsort( $cuenta ); // longest permit first: "10 días + 2 × 1 día"
		$partes = [];
		foreach ( $cuenta as $i => $cant ) {
			$partes[] = [ $tramos[ $i ], $cant ];
		}
		$candidatos[] = pm_licencia_plan( $partes );
		foreach ( $tramos as $t ) { // each permit alone, repeated as often as needed
			$candidatos[] = pm_licencia_plan( [ [ $t, (int) ceil( $n / $t['dias'] ) ] ] );
		}
	}
	if ( $temporada ) {
		$candidatos[] = pm_licencia_plan( [ [ $temporada, 1 ] ] );
	}
	if ( $finde && 2 === $n ) {
		$candidatos[] = pm_licencia_plan( [ [ $finde, 1 ] ] );
	}
	if ( ! $candidatos ) {
		return $vacio;
	}
	$vistos = [];
	$planes = [];
	foreach ( $candidatos as $p ) {
		$firma = implode( '|', array_map( fn( $it ) => $it['fila'] . 'x' . $it['cant'], $p['items'] ) );
		if ( ! isset( $vistos[ $firma ] ) ) {
			$vistos[ $firma ] = true;
			$planes[]         = $p;
		}
	}
	usort( $planes, fn( $a, $b ) => [ $a['cents'], $a['permisos'] ] <=> [ $b['cents'], $b['permisos'] ] );
	$ref = $tramos ? pm_licencia_plan( [ [ $tramos[0], (int) ceil( $n / $tramos[0]['dias'] ) ] ] ) : null;
	return [
		'estado'     => 'ok',
		'mejor'      => $planes[0],
		'planes'     => $planes,
		'referencia' => $ref,
		'ahorro'     => $ref && $ref['cents'] > $planes[0]['cents'] ? pm_licencia_num( $ref['cents'] - $planes[0]['cents'] ) : 0,
	];
}

/**
 * The calculation. Every input is whitelisted: an unknown region → Patagonia; a buyer category the region does not have → the region's first
 * (`ajustes.quien` says so); days outside 1–30 → 1; add-ons that do not exist for that region and buyer are dropped.
 *
 * @return array estado: 'ok' (priced), 'gratis' (free category), 'sin_valor' (the source we read gives no value), 'sin_cobertura' (no permit for those days);
 *               unidad: 'ARS'|'litros'; mejor/planes/referencia/ahorro as in pm_licencia_cubrir(); extras: add-ons with their own plan;
 *               total: base + add-ons (null when the base has no price); fuente/vigencia/consultada from the base row; donde: where to buy.
 */
function pm_licencia_calcular( string $region, string $quien, $dias = 1, array $extras = [] ): array {
	$region  = in_array( $region, PM_LICENCIA_REGIONES, true ) ? $region : 'patagonia';
	$quienes = pm_licencia_quienes( $region );
	$quien_ok = in_array( $quien, $quienes, true );
	$ajusta  = ! $quien_ok;
	$quien   = $quien_ok ? $quien : ( $quienes[0] ?? 'general' );
	$dias    = pm_licencia_dias( $dias );
	$validos = pm_licencia_extras( $region, $quien );
	$extras  = array_values( array_intersect( $validos, array_filter( $extras, 'is_string' ) ) );

	$base     = array_values( array_filter( pm_licencia_filas( $region ), fn( $f ) => ( $f['quien_clave'] ?? '' ) === $quien ) );
	$permisos = array_values( array_filter( $base, fn( $f ) => 'permiso' === ( $f['tipo'] ?? '' ) ) );
	$gratis   = array_values( array_filter( $base, fn( $f ) => 'gratis' === ( $f['tipo'] ?? '' ) ) );
	$fila     = $permisos[0] ?? $gratis[0] ?? $base[0] ?? [];
	$litros   = false !== strpos( (string) ( $fila['unidad'] ?? '' ), 'litros' );

	$r = [
		'region'     => $region,
		'quien'      => $quien,
		'dias'       => $dias,
		'extras_ok'  => $extras,
		'ajustes'    => [ 'quien' => $ajusta ],
		'unidad'     => $litros ? 'litros' : 'ARS',
		'estado'     => 'sin_valor',
		'mejor'      => null,
		'planes'     => [],
		'referencia' => null,
		'ahorro'     => 0,
		'extras'     => [],
		'total'      => null,
		'completo'   => false,
		'fila'       => (int) ( $fila['id'] ?? 0 ),
		'solo_en'    => (string) ( $fila['solo_en'] ?? '' ),
		'vigencia'   => (string) ( $fila['vigencia'] ?? '' ),
		'consultada' => (string) ( $fila['consultada'] ?? '' ),
		'fuente'     => [ 'nombre' => (string) ( $fila['fuente'] ?? '' ), 'url' => (string) ( $fila['fuente_url'] ?? '' ), 'oficial' => ! empty( $fila['fuente_oficial'] ) ],
		'donde'      => array_values( array_filter( (array) ( pm_licencia_datos()['donde'][ $region ] ?? [] ), 'is_array' ) ),
	];

	if ( $permisos ) {
		$c = pm_licencia_cubrir( $permisos, $dias );
		$r = array_merge( $r, array_intersect_key( $c, array_flip( [ 'estado', 'mejor', 'planes', 'referencia', 'ahorro' ] ) ) );
	} elseif ( $gratis ) {
		$r['estado'] = 'gratis';
		$r['total']  = 0;
		$r['completo'] = true;
		return $r;
	} else {
		return $r; // sin_valor
	}
	if ( 'ok' !== $r['estado'] ) {
		return $r;
	}

	$suma     = $r['mejor']['cents'];
	$completo = true;
	foreach ( $extras as $e ) {
		$filas = array_values( array_filter( pm_licencia_filas( $region, true ), fn( $f ) => ( $f['extra_clave'] ?? '' ) === $e && in_array( $quien, (array) ( $f['aplica_a'] ?? [] ), true ) ) );
		$c     = pm_licencia_cubrir( $filas, $dias );
		$f0    = $filas[0] ?? [];
		$r['extras'][] = [
			'clave'      => $e,
			'estado'     => $c['estado'],
			'mejor'      => $c['mejor'],
			'planes'     => $c['planes'],
			'consultada' => (string) ( $f0['consultada'] ?? '' ),
			'fuente'     => [ 'nombre' => (string) ( $f0['fuente'] ?? '' ), 'url' => (string) ( $f0['fuente_url'] ?? '' ) ],
		];
		if ( 'ok' === $c['estado'] ) {
			$suma += $c['mejor']['cents'];
		} else {
			$completo = false;
		}
	}
	$r['total']    = pm_licencia_num( $suma );
	$r['completo'] = $completo;
	return $r;
}

// --- Input, destinations, text --------------------------------------------------------

/**
 * What the visitor asked for, from GET (or an array in tests). `enviado` is true only when the form was sent (a valid place and a `quien` are there);
 * a link from a guide that only preselects the destination shows the form, not a result. Nothing here trusts the URL.
 */
function pm_licencia_entrada( ?array $get = null ): array {
	$get    ??= $_GET; // phpcs:ignore WordPress.Security.NonceVerification -- read-only calculator, nothing is saved
	$texto  = fn( $k ) => is_string( $get[ $k ] ?? null ) ? strtolower( trim( $get[ $k ] ) ) : '';
	$region = '';
	$destino = '';
	if ( preg_match( '/^(region|destino):([a-z0-9-]{1,60})$/', $texto( 'donde' ), $m ) ) {
		if ( 'region' === $m[1] && in_array( $m[2], PM_LICENCIA_REGIONES, true ) ) {
			$region = $m[2];
		} elseif ( 'destino' === $m[1] && isset( pm_licencia_destinos_mapa()[ $m[2] ] ) ) {
			$destino = $m[2];
			$region  = pm_licencia_destinos_mapa()[ $m[2] ];
		}
	}
	$quien = preg_match( '/^[a-z0-9-]{1,30}$/', $texto( 'quien' ) ) ? $texto( 'quien' ) : '';
	$extra = [];
	foreach ( is_array( $get['extra'] ?? null ) ? array_slice( $get['extra'], 0, 6 ) : [] as $e ) {
		if ( is_string( $e ) && in_array( strtolower( $e ), PM_LICENCIA_EXTRAS, true ) ) {
			$extra[] = strtolower( $e );
		}
	}
	return [
		'enviado' => '' !== $region && '' !== $quien,
		'region'  => $region,
		'destino' => $destino,
		'quien'   => $quien,
		'dias'    => pm_licencia_dias( $texto( 'dias' ) ),
		'extras'  => array_values( array_unique( $extra ) ),
	];
}

/** Destination guides whose province has a licence in the dataset (name and province from the guide: no list typed here). [clave, nombre, provincia, region]. */
function pm_licencia_destinos( string $idioma ): array {
	static $cache = [];
	if ( ! isset( $cache[ $idioma ] ) ) {
		$out = [];
		foreach ( pm_destinos_lista( $idioma ) as $d ) {
			$region = PM_LICENCIA_PROVINCIAS[ $d['provincia'] ?? '' ] ?? '';
			if ( '' !== $region && '' !== (string) ( $d['clave'] ?? '' ) ) {
				$out[] = [ 'clave' => (string) $d['clave'], 'nombre' => (string) $d['nombre'], 'provincia' => (string) ( PM_PROVINCIAS[ $d['provincia'] ] ?? '' ), 'region' => $region ];
			}
		}
		$cache[ $idioma ] = $out;
	}
	return $cache[ $idioma ];
}

/** clave → region for every destination (language independent). */
function pm_licencia_destinos_mapa(): array {
	static $m = null;
	return $m ??= array_column( pm_licencia_destinos( 'es' ), 'region', 'clave' );
}

/** Region of a destination guide, or '' when its province has no licence in the dataset. */
function pm_licencia_region_de_destino( string $clave ): string {
	return pm_licencia_destinos_mapa()[ $clave ] ?? '';
}

function pm_licencia_url( string $region = '', string $destino = '' ): string {
	$base = pm_url_licencia( pm_lang() );
	if ( '' !== $destino ) {
		return $base . '?donde=destino:' . rawurlencode( $destino ) . '#calculadora';
	}
	return '' !== $region ? $base . '?donde=region:' . rawurlencode( $region ) . '#calculadora' : $base;
}

/** "3 días", "1 día" (number of days in the language of the page). */
function pm_licencia_dias_txt( $dias ): string {
	return 'temporada' === $dias ? pm_t( 'lic.dias.temporada' ) : pm_t( 1 === (int) $dias ? 'lic.dias.uno' : 'lic.dias.n', [ 'n' => (string) (int) $dias ] );
}

/** Period label of a permit row ("1 día", "10 días", "temporada", "anual", "fin de semana") in the language of the page. */
function pm_licencia_periodo_txt( string $periodo ): string {
	if ( preg_match( '/^(\d+) días?$/u', $periodo, $m ) ) {
		return pm_licencia_dias_txt( (int) $m[1] );
	}
	$k = [ 'temporada' => 'temporada', 'anual' => 'anual', 'fin de semana' => 'finde', 'sin vencimiento' => 'sinvenc', '—' => 'sinvenc' ][ $periodo ] ?? '';
	return '' !== $k ? pm_t( 'lic.periodo.' . $k ) : $periodo;
}

/**
 * An amount as the page shows it. Pesos: "ARS $ 140.000" (one convention per language, pm_cot_ars). Litres of nafta súper: with a fresh fuel price in `$cot`
 * the approximate peso amount "≈ ARS $ 52.200" (litres × price, nearest 100); without it "24 litros de nafta súper" as the source publishes.
 * `$cot` = pm_cotizaciones() or null (null = no approximation, what the reference table and the FAQ use).
 */
function pm_licencia_monto( $valor, string $unidad, ?array $cot = null ): string {
	if ( 'litros' === $unidad ) {
		$n = $cot['nafta'] ?? null;
		if ( $n ) {
			return '≈ ' . pm_cot_ars( pm_cot_nafta_pesos( $valor, (float) $n['precio'] ) );
		}
		return pm_cot_num( $valor ) . ' ' . pm_t( 'lic.unidad.litros' );
	}
	return pm_cot_ars( $valor );
}

/** The amount in pesos (int|float) a total stands for, or null when it is in litres and there is no fresh fuel price. Pesos pass through. */
function pm_licencia_pesos( $valor, string $unidad, ?array $cot = null ) {
	if ( 'litros' !== $unidad ) {
		return $valor;
	}
	$n = $cot['nafta'] ?? null;
	return $n ? pm_cot_nafta_pesos( $valor, (float) $n['precio'] ) : null;
}

/** "≈ US$ 97" for a total (pesos, or litres with a fresh fuel price) when the rate is fresh; '' otherwise. */
function pm_licencia_usd( $valor, string $unidad, ?array $cot = null ): string {
	$pesos = pm_licencia_pesos( $valor, $unidad, $cot );
	return null === $pesos ? '' : pm_cot_aprox_usd( $pesos, $cot['dolar'] ?? null );
}

/** Headline of a priced result: [ 'monto' => "ARS $ 140.000" | "≈ ARS $ 52.200" | "24 litros de nafta súper", 'usd' => "≈ US$ 97" | '', 'modo' => 'ars'|'aprox'|'litros' ]. */
function pm_licencia_titular( array $res, ?array $cot = null ): array {
	$u = (string) $res['unidad'];
	return [
		'monto' => pm_licencia_monto( $res['total'], $u, $cot ),
		'usd'   => pm_licencia_usd( $res['total'], $u, $cot ),
		'modo'  => 'litros' !== $u ? 'ars' : ( ( $cot['nafta'] ?? null ) ? 'aprox' : 'litros' ),
	];
}

/** "24 litros" (the short form used in parentheses). */
function pm_licencia_litros_txt( $n ): string {
	return pm_t( 'lic.litros.n', [ 'n' => pm_cot_num( $n ) ] );
}

/** The one-line explanation under a Corrientes result with a fresh fuel price: litres × price per litre, where the price comes from, that the portal sets the final amount. */
function pm_licencia_nafta_linea( $litros, array $nafta ): string {
	return pm_t( '' !== (string) ( $nafta['bandera'] ?? '' ) ? 'lic.res.nafta' : 'lic.res.nafta.sinbandera', [
		'litros'  => pm_cot_num( $litros ),
		'precio'  => pm_cot_num( $nafta['precio'] ),
		'bandera' => (string) ( $nafta['bandera'] ?? '' ),
		'fecha'   => pm_fecha( (string) $nafta['fecha'] ),
	] );
}

/** Will the result show any US$ figure? (Then the rate line goes under it.) */
function pm_licencia_usa_dolar( array $res, ?array $cot = null ): bool {
	return 'ok' === $res['estado'] && '' !== pm_licencia_usd( $res['total'], (string) $res['unidad'], $cot );
}

/**
 * One plan as a line of arithmetic: "10 días + 2 × 1 día = ARS $ 140.000 + ARS $ 100.000 = ARS $ 240.000 (≈ US$ 166)" (a single permit: "10 días = ARS $ 140.000 (≈ US$ 97)").
 * Corrientes with a fresh fuel price: "3 días ≈ ARS $ 48.000 (24 litros · ≈ US$ 33)"; several permits list their litres: "(36 + 32 = 68 litros · ≈ US$ 94)".
 * The US$ figure only goes after the plan's total, never on the parts.
 */
function pm_licencia_plan_txt( array $plan, string $unidad, ?array $cot = null ): string {
	$partes = [];
	$sumas  = [];
	$litros = [];
	foreach ( (array) ( $plan['items'] ?? [] ) as $it ) {
		$p        = pm_licencia_periodo_txt( (string) $it['periodo'] );
		$partes[] = $it['cant'] > 1 ? $it['cant'] . ' × ' . $p : $p;
		$sumas[]  = pm_licencia_monto( $it['subtotal'], $unidad, $cot );
		$litros[] = pm_cot_num( $it['subtotal'] );
	}
	$total_v = $plan['total'] ?? 0;
	$total   = pm_licencia_monto( $total_v, $unidad, $cot );
	$usd     = pm_licencia_usd( $total_v, $unidad, $cot );
	if ( 'litros' === $unidad && ( $cot['nafta'] ?? null ) ) {
		$det = [ ( count( $litros ) > 1 ? implode( ' + ', $litros ) . ' = ' : '' ) . pm_licencia_litros_txt( $total_v ) ];
		if ( '' !== $usd ) {
			$det[] = $usd;
		}
		return implode( ' + ', $partes ) . ' ' . $total . ' (' . implode( ' · ', $det ) . ')'; // the total already starts with «≈»: no «= ≈»
	}
	return implode( ' + ', $partes ) . ' = ' . ( count( $sumas ) > 1 ? implode( ' + ', $sumas ) . ' = ' : '' ) . $total . ( '' !== $usd ? ' (' . $usd . ')' : '' );
}

/** Validity text of a region in the language of the page (ui.json carries it for the three languages; the data row has the Spanish original, and a test keeps them equal).
 *  Regions without a `lic.vig.<region>` text (Corrientes: its "vigencia" cell says the litres are repriced at purchase, which the result already says) show no line.
 *  A category that holds only in one jurisdiction (Río Negro's) has a longer text in the row: it says so in the «Vale solo en…» note, and the season is the region's. */
function pm_licencia_vigencia_txt( string $region, string $es ): string {
	return pm_t_existe( "lic.vig.$region" ) ? pm_t( "lic.vig.$region" ) : '';
}

/** The single published value of a permit (for the FAQ and the reference table): '' when there is none. */
function pm_licencia_valor_txt( string $region, string $quien, $dias, ?array $cot = null ): string {
	foreach ( pm_licencia_filas( $region ) as $f ) {
		if ( ( $f['quien_clave'] ?? '' ) === $quien && 'permiso' === ( $f['tipo'] ?? '' ) && ( $f['dias'] ?? null ) === $dias ) {
			return pm_licencia_monto( $f['valor'], false !== strpos( (string) ( $f['unidad'] ?? '' ), 'litros' ) ? 'litros' : 'ARS', $cot );
		}
	}
	return '';
}

/** Latest date read in the dataset (Y-m-d) and the earliest, for "Leído entre…". */
function pm_licencia_fechas(): array {
	$f = array_filter( array_map( fn( $x ) => (string) ( $x['consultada'] ?? '' ), pm_licencia_datos()['filas'] ) );
	return $f ? [ min( $f ), max( $f ) ] : [ '', '' ];
}

/** FAQ as [pregunta, respuesta] pairs. The amounts come from the data, so the answers follow the next season's values. */
function pm_licencia_faqs(): array {
	$cot = function_exists( 'pm_cotizaciones' ) ? pm_cotizaciones() : null;
	$cot = is_array( $cot ) && ( $cot['nafta'] ?? null ) ? [ 'nafta' => $cot['nafta'] ] : null; // only Corrientes changes: the other amounts stay as published
	$v = [
		'pat1'  => pm_licencia_valor_txt( 'patagonia', 'extranjero', 1 ),
		'pat'   => pm_licencia_valor_txt( 'patagonia', 'extranjero', 'temporada' ),
		'sc1'   => pm_licencia_valor_txt( 'santa-cruz', 'extranjero', 1 ),
		'sc'    => pm_licencia_valor_txt( 'santa-cruz', 'extranjero', 'temporada' ),
		'tdf1'  => pm_licencia_valor_txt( 'tierra-del-fuego', 'extranjero', 1 ),
		'tdf'   => pm_licencia_valor_txt( 'tierra-del-fuego', 'extranjero', 'temporada' ),
		'er5'   => pm_licencia_valor_txt( 'entre-rios', 'extranjero', 5 ),
		'ctes1' => pm_licencia_valor_txt( 'corrientes', 'extranjero', 1, $cot ), // pesos (≈) with a fresh fuel price, litres without
		'ctes6' => pm_licencia_valor_txt( 'corrientes', 'extranjero', 6, $cot ),
	];
	$faqs = [];
	foreach ( [ 1, 2, 3, 4, 5 ] as $n ) {
		$faqs[] = [ 'pregunta' => pm_t( "lic.faq.$n.q" ), 'respuesta' => pm_t( "lic.faq.$n.a", $v ) ];
	}
	return $faqs;
}

/** A plan without its prices: "3 × 1 día", "10 días + 2 × 1 día". */
function pm_licencia_plan_corto( array $plan ): string {
	$partes = [];
	foreach ( (array) ( $plan['items'] ?? [] ) as $it ) {
		$p        = pm_licencia_periodo_txt( (string) $it['periodo'] );
		$partes[] = $it['cant'] > 1 ? $it['cant'] . ' × ' . $p : $p;
	}
	return implode( ' + ', $partes );
}

/** Regions that have this buyer category (for the form's data-regiones). */
function pm_licencia_regiones_de_quien( string $quien ): array {
	return array_values( array_filter( PM_LICENCIA_REGIONES, fn( $r ) => in_array( $quien, pm_licencia_quienes( $r ), true ) ) );
}

/** "region:quien" pairs for which an add-on exists (for the form's data-pares). */
function pm_licencia_pares_de_extra( string $extra ): array {
	$out = [];
	foreach ( PM_LICENCIA_REGIONES as $r ) {
		foreach ( pm_licencia_quienes( $r ) as $q ) {
			if ( in_array( $extra, pm_licencia_extras( $r, $q ), true ) ) {
				$out[] = "$r:$q";
			}
		}
	}
	return $out;
}

/**
 * Reference table of every region: one row per region × buyer (ordinary permits, listed from fewer to more days, season last) and one per add-on.
 * Rows: region, quien (text), valores (text), consultada, fuente_url, extra (bool).
 */
function pm_licencia_tabla(): array {
	$orden = fn( $f ) => is_int( $f['dias'] ?? null ) ? $f['dias'] : ( 'fin-de-semana' === ( $f['dias'] ?? '' ) ? 1.5 : 999 );
	$valores = function ( array $filas ) use ( $orden ): string {
		usort( $filas, fn( $a, $b ) => $orden( $a ) <=> $orden( $b ) );
		$t = [];
		foreach ( $filas as $f ) {
			if ( 'sin_valor' === ( $f['tipo'] ?? '' ) ) {
				return pm_t( 'lic.tabla.sinvalor' );
			}
			if ( 'gratis' === ( $f['tipo'] ?? '' ) ) {
				return pm_t( 'lic.tabla.gratis' );
			}
			$t[] = pm_licencia_periodo_txt( (string) $f['periodo'] ) . ' ' . pm_licencia_monto( $f['valor'], false !== strpos( (string) ( $f['unidad'] ?? '' ), 'litros' ) ? 'litros' : 'ARS' );
		}
		return implode( ' · ', $t );
	};
	$out = [];
	foreach ( PM_LICENCIA_REGIONES as $r ) {
		$base = pm_licencia_filas( $r );
		foreach ( pm_licencia_quienes( $r ) as $q ) {
			$f = array_values( array_filter( $base, fn( $x ) => ( $x['quien_clave'] ?? '' ) === $q ) );
			$out[] = [ 'region' => $r, 'quien' => pm_t( "lic.quien.$q" ), 'valores' => $valores( $f ), 'consultada' => (string) ( $f[0]['consultada'] ?? '' ), 'fuente_url' => (string) ( $f[0]['fuente_url'] ?? '' ), 'extra' => false ];
		}
		foreach ( PM_LICENCIA_EXTRAS as $e ) {
			$por_quien = [];
			foreach ( pm_licencia_filas( $r, true ) as $x ) {
				if ( ( $x['extra_clave'] ?? '' ) === $e ) {
					$por_quien[ implode( ',', (array) ( $x['aplica_a'] ?? [] ) ) ][] = $x;
				}
			}
			foreach ( $por_quien as $aplica => $f ) {
				$out[] = [
					'region'     => $r,
					'quien'      => pm_t( 'lic.tabla.extra', [ 'extra' => pm_t( "lic.extra.$e" ) ] ) . ' · ' . implode( ' / ', array_map( fn( $q ) => pm_t( "lic.quien.$q" ), explode( ',', $aplica ) ) ),
					'valores'    => $valores( $f ),
					'consultada' => (string) ( $f[0]['consultada'] ?? '' ),
					'fuente_url' => (string) ( $f[0]['fuente_url'] ?? '' ),
					'extra'      => true,
				];
			}
		}
	}
	return $out;
}
