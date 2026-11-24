<?php
/**
 * Season dates of a guide (added 4/11/2026, idea taken from the PESQA review: docs/competencia-pesqa-2026-11-04.md).
 * A guide may carry the official opening and closing dates (fields temporada_abre / temporada_cierra, common to the three languages),
 * optionally the best months an operator or a technical source claims (pico_meses + pico_quien, always attributed), and a per-language note
 * for waters with other dates. From them: a 12-month bar (open by the rule / best time by the named source / closed), and the question
 * "Can I fish in <place> now?" answered against today's date. The answer carries its date ("As of 4 November 2026…") so a cached page is
 * never wrong, only old. Without opening and closing dates nothing is shown: the module never guesses.
 */

defined( 'ABSPATH' ) || exit;

/** Has this guide both official dates? */
function pm_temporada_hay( array $d ): bool {
	return ! empty( $d['abre'] ) && ! empty( $d['cierra'] ) && $d['abre'] <= $d['cierra'];
}

function pm_meses_cortos( string $idioma ): array {
	$m = [
		'es' => [ 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic' ],
		'pt' => [ 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez' ],
		'en' => [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' ],
	];
	return $m[ $idioma ] ?? $m['es'];
}

/**
 * State of each month 1..12: 'cerrado', 'abierto' (inside the official season) or 'pico' (open and named as best by a source).
 * A month counts as open when any day of it falls inside the season. The best months only count when a source is named.
 */
function pm_temporada_meses( array $d ): array {
	$out = array_fill( 1, 12, 'cerrado' );
	if ( ! pm_temporada_hay( $d ) ) {
		return $out;
	}
	$ini = new DateTime( substr( $d['abre'], 0, 7 ) . '-01' );
	$fin = new DateTime( substr( $d['cierra'], 0, 7 ) . '-01' );
	for ( $n = 0; $ini <= $fin && $n < 13; $n++, $ini->modify( '+1 month' ) ) {
		$out[ (int) $ini->format( 'n' ) ] = 'abierto';
	}
	if ( '' !== $d['pico_quien'] ) {
		foreach ( $d['pico'] as $m ) {
			if ( 'abierto' === $out[ $m ] ) {
				$out[ $m ] = 'pico';
			}
		}
	}
	return $out;
}

/** 'antes' (not open yet), 'abierta' or 'cerrada' on the given day (Y-m-d, Argentina's date by default). */
function pm_temporada_estado( array $d, ?string $hoy = null ): string {
	$hoy = $hoy ?: wp_date( 'Y-m-d', null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
	if ( $hoy < $d['abre'] ) {
		return 'antes';
	}
	return $hoy > $d['cierra'] ? 'cerrada' : 'abierta';
}

/** The computed question and answer, dated; [] when the guide has no dates. */
function pm_temporada_faq( array $d, ?string $hoy = null ): array {
	if ( ! pm_temporada_hay( $d ) ) {
		return [];
	}
	$i      = $d['idioma'];
	$hoy    = $hoy ?: wp_date( 'Y-m-d', null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
	$estado = pm_temporada_estado( $d, $hoy );
	$a      = [ 'hoy' => pm_fecha( $hoy, $i ), 'abre' => pm_fecha( $d['abre'], $i ), 'cierra' => pm_fecha( $d['cierra'], $i ), 'lugar' => $d['nombre'] ];
	$resp   = pm_t( 'temp.faq.' . $estado, $a, $i );
	if ( $d['temporada_nota'] ) {
		$resp .= ' ' . rtrim( $d['temporada_nota'], '.' ) . '.';
	}
	$aut = $d['reglamento']['autoridad'];
	$resp .= ' ' . pm_t( $aut ? 'temp.faq.fuente' : 'temp.faq.sinfuente', [ 'fuente' => $aut ], $i );
	return [ [ 'pregunta' => pm_t( 'temp.faq.q', $a, $i ), 'respuesta' => $resp ] ];
}

/** The guide's own FAQs with the computed one first. */
function pm_faqs_destino( array $d ): array {
	return array_merge( pm_temporada_faq( $d ), $d['faqs'] );
}

/**
 * Questions and answers of the "Guías de pesca en <lugar>" page: the four fixed ones plus, when the guide carries official
 * season dates, "When is the fishing season in <lugar>?" (13/11/2026). The answer is composed from the same fields the guide
 * shows (opening and closing dates, the per-language note for waters with other dates, the authority and the date the
 * regulation was checked), so it names its source and cannot disagree with the guide. The question wording may be overridden
 * per guide in ui.json (`guias.faq.temp.q.<clave>`), e.g. fly fishing in Esquel or the sea-run brown trout of the Río Grande.
 * @return array<int, array{pregunta: string, respuesta: string}>
 */
function pm_guias_faqs( array $d, string $clave, ?array $prestadores = null ): array {
	$i   = $d['idioma'] ?? 'es';
	$a   = [ 'lugar' => $d['nombre'] ?? '' ];
	$out = [];
	// Cost and "what is included" depend on the city's own cards (20/11/2026): weekly lodge programmes (Río Gallegos) vs boat-and-day guides.
	$cards = $prestadores ?? pm_prestadores_destino( $clave );
	$lodge = pm_guias_es_lodge( $cards );
	foreach ( [ 1, 2, 3, 4 ] as $n ) {
		$resp = pm_t( "guias.faq.$n.a" . ( $lodge && in_array( $n, [ 1, 2 ], true ) ? '.lodge' : '' ), $a, $i );
		if ( $lodge && 1 === $n ) {
			$ej    = pm_guias_ejemplo_precio( $cards, $i );
			$resp = strtr( $resp, [ '%ejemplo%' => '' !== $ej ? pm_t( 'guias.faq.1.ejemplo', [ 'ejemplo' => $ej ], $i ) : '' ] );
		}
		$out[] = [ 'pregunta' => pm_t( "guias.faq.$n.q", $a, $i ), 'respuesta' => $resp ];
	}
	// Who is authorised: the same advice everywhere; the sentence about the provincial listing only where we read one (Corrientes).
	$hab = pm_t( 'guias.faq.5.a', $a, $i ) . ( 'corrientes' === ( $d['provincia'] ?? '' ) ? ' ' . pm_t( 'guias.faq.5.corrientes', $a, $i ) : '' );
	$out[] = [ 'pregunta' => pm_t( 'guias.faq.5.q', $a, $i ), 'respuesta' => $hab ];
	if ( pm_temporada_hay( $d ) ) {
		$resp = pm_t( 'guias.faq.temp.a', $a + [ 'abre' => pm_fecha( $d['abre'], $i ), 'cierra' => pm_fecha( $d['cierra'], $i ) ], $i );
		if ( $d['temporada_nota'] ) {
			$resp .= ' ' . rtrim( $d['temporada_nota'], '.' ) . '.';
		}
		$aut   = $d['reglamento']['autoridad'];
		$resp .= ' ' . pm_t( $aut ? 'temp.faq.fuente' : 'temp.faq.sinfuente', [ 'fuente' => $aut ], $i );
		if ( ! empty( $d['reglamento']['verificado'] ) ) {
			$resp .= ' ' . pm_t( 'guias.faq.temp.verif', [ 'fecha' => pm_fecha( $d['reglamento']['verificado'], $i ) ], $i );
		}
		$out[] = [ 'pregunta' => pm_t_clave( 'guias.faq.temp.q', $clave, $a, $i ), 'respuesta' => $resp ];
	}
	return $out;
}

/** Month name (1..12) in the page language, for headings: "noviembre", "novembro", "November". */
function pm_mes_nombre( int $m, string $idioma ): string {
	$n = [
		'es' => [ 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' ],
		'pt' => [ 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro' ],
		'en' => [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],
	];
	return ( $n[ $idioma ] ?? $n['es'] )[ $m - 1 ] ?? '';
}

/**
 * Home block "Where to go this month" (19/11/2026): published guides whose own season data says today is inside the season.
 * Only data already published with its source is used: the official opening/closing dates (temporada_abre / temporada_cierra) and,
 * for the best months, pico_meses only when pico_quien names who says so. Levels: 'pico' (open and named best by a source)
 * before 'abierto' (open by the rule); inside a level, the guides' usual order. Up to $max; [] when fewer than 2 qualify,
 * so the block is not rendered. Cached per language and day in a transient; the version option is bumped when a guide or species
 * is saved, so edits show at once.
 * @return array<int, array{clave: string, nombre: string, url: string, idioma: string, especies: string, nivel: string, quien: string}>
 */
function pm_destinos_del_mes( string $idioma, int $max = 6, ?string $hoy = null ): array {
	$idioma = pm_idioma_valido( $idioma );
	$hoy    = $hoy ?: wp_date( 'Y-m-d', null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
	$clave  = 'pm_mes_' . (int) get_option( 'pm_mes_ver', 1 ) . "_{$idioma}_{$hoy}_{$max}";
	$cache  = get_transient( $clave );
	if ( is_array( $cache ) ) {
		return $cache;
	}
	$mes = (int) substr( $hoy, 5, 2 );
	$out = [];
	foreach ( pm_destinos_lista( $idioma ) as $d ) {
		if ( ! pm_temporada_hay( $d ) || 'abierta' !== pm_temporada_estado( $d, $hoy ) ) {
			continue;
		}
		$nivel = pm_temporada_meses( $d )[ $mes ] ?? 'cerrado';
		if ( 'cerrado' === $nivel ) {
			continue;
		}
		$out[] = [
			'clave'    => (string) ( $d['clave'] ?? '' ),
			'nombre'   => (string) ( $d['nombre'] ?? '' ),
			'url'      => (string) ( $d['url'] ?? '' ),
			'idioma'   => (string) ( $d['idioma'] ?? $idioma ),
			'especies' => pm_especies_linea( $d, 2, $idioma ),
			'nivel'    => $nivel,
			'quien'    => 'pico' === $nivel ? (string) ( $d['pico_quien'] ?? '' ) : '',
		];
	}
	usort( $out, fn( $a, $b ) => ( 'pico' === $b['nivel'] ) <=> ( 'pico' === $a['nivel'] ) );
	$out = count( $out ) >= 2 ? array_slice( $out, 0, max( 2, $max ) ) : [];
	set_transient( $clave, $out, 6 * HOUR_IN_SECONDS );
	return $out;
}

/** A guide or a species changed: the month block recomputes on the next visit. */
add_action( 'save_post', function ( $post_id, $post ) {
	if ( $post instanceof WP_Post && in_array( $post->post_type, [ 'pm_destino', 'pm_especie' ], true ) ) {
		update_option( 'pm_mes_ver', (int) get_option( 'pm_mes_ver', 1 ) + 1, false );
	}
}, 20, 2 );

/**
 * Calendar page (rebuilt 24/11/2026, docs/ux-paginas.md): one card per published guide with a 12-month bar per species, the state
 * of today in one pill, one short sentence with OUR reading and the sources folded away. Months come from two places only:
 *  - guides with official dates (temporada_abre / temporada_cierra): the open months from those fields, the best months from
 *    pico_meses only when pico_quien backs them (same rule as the guide's own bar);
 *  - the rest: data/calendario.json, keyed by guide clave, filled only from what the published guide already says
 *    (see its `_meta`). A clave absent from the file simply has no species rows: the card says the months are still to confirm.
 * A closed season (veda) is painted only when the file lists its months; veda null shows «Veda: fecha por confirmar».
 */
function pm_calendario_datos(): array {
	static $datos = null;
	if ( null === $datos ) {
		$j     = json_decode( (string) @file_get_contents( PM_DIR . 'data/calendario.json' ), true );
		$datos = is_array( $j ) ? $j : [];
	}
	return $datos;
}

/** "1 de noviembre", "1 November": a date without the year, for the one-line reading. */
function pm_fecha_corta( ?string $ymd, string $idioma ): string {
	if ( ! $ymd ) {
		return '';
	}
	$t = pm_fecha( $ymd, $idioma );
	return trim( (string) preg_replace( '/\s+(de\s+)?\d{4}$/u', '', $t ) );
}

/** Rank of a month state, to pick the best one of a card: best time > fished > closed season > closed or out of season > no data. */
function pm_calendario_rango( string $estado ): int {
	return [ 'mejor' => 4, 'pesca' => 3, 'veda' => 2, 'cerrado' => 1, 'sin' => 0 ][ $estado ] ?? 0;
}

/**
 * State of each month 1..12 for one species entry of a guide: 'mejor', 'pesca', 'veda', 'cerrado' (out of season) or 'sin' (the file
 * has no fished months for it). Null when there is nothing to draw (no official dates and no months in the file).
 * @return array<int, string>|null
 */
function pm_calendario_meses( array $d, array $e ): ?array {
	$veda = array_values( array_filter( array_map( 'intval', (array) ( $e['veda'] ?? [] ) ), fn( $m ) => $m >= 1 && $m <= 12 ) );
	if ( pm_temporada_hay( $d ) ) {
		$out = array_map( fn( $s ) => [ 'pico' => 'mejor', 'abierto' => 'pesca' ][ $s ] ?? 'cerrado', pm_temporada_meses( $d ) );
	} else {
		$pesca = $e['pesca'] ?? null;
		$mejor = array_values( array_filter( array_map( 'intval', (array) ( $e['mejor'] ?? [] ) ), fn( $m ) => $m >= 1 && $m <= 12 ) );
		if ( ! is_array( $pesca ) && ! $mejor && ! $veda ) {
			return null;
		}
		$out = array_fill( 1, 12, is_array( $pesca ) || $mejor ? 'cerrado' : 'sin' );
		foreach ( (array) $pesca as $m ) {
			if ( (int) $m >= 1 && (int) $m <= 12 ) {
				$out[ (int) $m ] = 'pesca';
			}
		}
		foreach ( $mejor as $m ) {
			$out[ $m ] = 'mejor';
		}
	}
	foreach ( $veda as $m ) {
		$out[ $m ] = 'veda';
	}
	return $out;
}

/** State of a species today: the month's state, and 'cerrado' outside the official dates even inside an open month. */
function pm_calendario_hoy( array $meses, string $abre, string $cierra, string $hoy ): string {
	if ( '' !== $abre && '' !== $cierra && ( $hoy < $abre || $hoy > $cierra ) ) {
		return 'cerrado';
	}
	return $meses[ (int) substr( $hoy, 5, 2 ) ] ?? 'sin';
}

/** The pill text of a state (the closed ones say when the season opens or ended when the guide has dates). */
function pm_calendario_pill( string $estado, string $abre, string $cierra, string $hoy, string $idioma ): string {
	if ( 'cerrado' === $estado && '' !== $abre && $hoy < $abre ) {
		return pm_t( 'cal.pill.antes', [ 'fecha' => pm_fecha( $abre, $idioma ) ], $idioma );
	}
	if ( 'cerrado' === $estado && '' !== $cierra && $hoy > $cierra ) {
		return pm_t( 'cal.pill.despues', [ 'fecha' => pm_fecha( $cierra, $idioma ) ], $idioma );
	}
	return pm_t( 'cal.pill.' . ( 'cerrado' === $estado ? 'fuera' : $estado ), [], $idioma );
}

/** Name of a species in the page language: its page when it exists, else the file's fallback name, else the key. */
function pm_calendario_especie_nombre( string $clave, string $idioma ): string {
	$e = pm_especie_en( $clave, $idioma );
	if ( $e && '' !== trim( (string) $e['nombre'] ) ) {
		return trim( (string) $e['nombre'] );
	}
	$f = pm_calendario_datos()['_especies'][ $clave ][ $idioma ] ?? '';
	return '' !== $f ? (string) $f : ucfirst( $clave );
}

/**
 * Cards of the calendar page, one per published guide of this language (Spanish as fallback), in the guides' order.
 * Cached per language and day (key carries pm_mes_ver, bumped when a guide is saved, and the file's modification time).
 * @return array{hoy: string, regiones: array<string, string>, especies: array<string, string>, tarjetas: array<int, array<string, mixed>>}
 */
function pm_calendario_tarjetas( string $idioma, ?string $hoy = null ): array {
	$idioma = pm_idioma_valido( $idioma );
	$hoy    = $hoy ?: wp_date( 'Y-m-d', null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
	$clave  = 'pm_cal_' . (int) get_option( 'pm_mes_ver', 1 ) . '_' . (int) @filemtime( PM_DIR . 'data/calendario.json' ) . "_{$idioma}_{$hoy}";
	$cache  = get_transient( $clave );
	if ( is_array( $cache ) ) {
		return $cache;
	}
	$datos    = pm_calendario_datos();
	$tarjetas = [];
	$usadas   = [];
	foreach ( pm_destinos_lista( $idioma ) as $d ) {
		$k       = (string) ( $d['clave'] ?? '' );
		$abre    = pm_temporada_hay( $d ) ? (string) $d['abre'] : '';
		$cierra  = '' !== $abre ? (string) $d['cierra'] : '';
		$filas   = [];
		$estados = [];
		$entradas = (array) ( $datos[ $k ] ?? [] );
		if ( ! $entradas && '' !== $abre ) {
			// A guide with official dates but no entry in the file still gets its bar (one per species it lists, no sentence).
			foreach ( array_slice( array_filter( (array) ( $d['especies'] ?? [] ), 'is_string' ), 0, 3 ) as $esp ) {
				$entradas[] = [ 'especie' => $esp ];
			}
		}
		foreach ( $entradas as $e ) {
			$esp   = (string) ( $e['especie'] ?? '' );
			$meses = '' !== $esp && is_array( $e ) ? pm_calendario_meses( $d, $e ) : null;
			if ( null === $meses ) {
				continue;
			}
			$hoy_e = pm_calendario_hoy( $meses, $abre, $cierra, $hoy );
			$frase = (string) ( $e['frase'][ $idioma ] ?? $e['frase']['es'] ?? '' );
			$filas[] = [
				'especie'  => $esp,
				'nombre'   => (string) ( $e['etiqueta'][ $idioma ] ?? $e['etiqueta']['es'] ?? '' ) ?: pm_calendario_especie_nombre( $esp, $idioma ),
				'meses'    => $meses,
				'hoy'      => $hoy_e,
				'pill'     => pm_calendario_pill( $hoy_e, $abre, $cierra, $hoy, $idioma ),
				'frase'    => strtr( $frase, [ '%abre%' => pm_fecha_corta( $abre, $idioma ), '%cierra%' => pm_fecha_corta( $cierra, $idioma ) ] ),
				'nota'     => (string) ( $e['nota_fuentes'][ $idioma ] ?? $e['nota_fuentes']['es'] ?? '' ),
				'revisado' => (string) ( $e['revisado'] ?? '' ),
				'veda_pendiente' => ! array_key_exists( 'veda', $e ) || null === $e['veda'],
			];
			$estados[ $esp ] = $hoy_e;
		}
		$especies = array_values( array_unique( array_merge( array_column( $filas, 'especie' ), array_filter( (array) ( $d['especies'] ?? [] ), 'is_string' ) ) ) );
		foreach ( $especies as $esp ) {
			$estados[ $esp ] = $estados[ $esp ] ?? 'sin';
			$usadas[ $esp ]  = true;
		}
		$mejor = 'sin';
		foreach ( $estados as $s ) {
			if ( pm_calendario_rango( $s ) > pm_calendario_rango( $mejor ) ) {
				$mejor = $s;
			}
		}
		$r        = (array) ( $d['reglamento'] ?? [] );
		$rev      = array_filter( array_column( $filas, 'revisado' ) );
		$rev_ymd  = $rev ? max( $rev ) : ( ( $t = strtotime( (string) ( $d['actualizado'] ?? '' ) ) ) ? gmdate( 'Y-m-d', $t ) : '' );
		$guias    = pm_destino_guias( $k, $idioma );
		$tarjetas[] = [
			'clave'     => $k,
			'nombre'    => (string) ( $d['nombre'] ?? '' ),
			'url'       => (string) ( $d['url'] ?? '' ),
			'provincia' => (string) ( PM_PROVINCIAS[ $d['provincia'] ?? '' ] ?? ( $d['provincia'] ?? '' ) ),
			'region'    => ( $d['region'] ?? '' ) ?: 'otros',
			'especies'  => $especies,
			'estados'   => $estados,
			'estado'    => $mejor,
			'pill'      => pm_calendario_pill( $mejor, $abre, $cierra, $hoy, $idioma ),
			'abre'      => $abre,
			'cierra'    => $cierra,
			'filas'     => $filas,
			'cta_url'   => $guias ? pm_url_guias( $k, $idioma ) : add_query_arg( 'destino', $k, pm_url_viaje( $idioma ) ),
			'autoridad' => (string) ( $r['autoridad'] ?? '' ),
			'fuente_url' => (string) ( $r['url'] ?? '' ),
			'verificado' => (string) ( $r['verificado'] ?? '' ),
			'revisado'  => $rev_ymd,
			'nota_guia' => (string) ( $d['temporada_nota'] ?? '' ),
		];
	}
	$orden = array_keys( PM_REGIONES );
	usort( $tarjetas, function ( $a, $b ) use ( $orden ) {
		$ra = array_search( $a['region'], $orden, true );
		$rb = array_search( $b['region'], $orden, true );
		return ( false === $ra ? 99 : $ra ) <=> ( false === $rb ? 99 : $rb );
	} );
	$regiones = [];
	foreach ( array_merge( $orden, [ 'otros' ] ) as $g ) {
		if ( in_array( $g, array_column( $tarjetas, 'region' ), true ) ) {
			$regiones[ $g ] = PM_REGIONES[ $g ][ $idioma ] ?? $g;
		}
	}
	$especies = [];
	foreach ( array_merge( [ 'dorado', 'surubi', 'pacu', 'trucha', 'pejerrey' ], array_keys( $usadas ) ) as $esp ) {
		if ( isset( $usadas[ $esp ] ) && ! isset( $especies[ $esp ] ) ) {
			$especies[ $esp ] = pm_calendario_especie_nombre( $esp, $idioma );
		}
	}
	$out = [ 'hoy' => $hoy, 'regiones' => $regiones, 'especies' => $especies, 'tarjetas' => $tarjetas ];
	set_transient( $clave, $out, 6 * HOUR_IN_SECONDS );
	return $out;
}

/** Does a card match the chosen region and species ('' = all)? */
function pm_calendario_coincide( array $t, string $donde, string $que ): bool {
	return ( '' === $donde || $t['region'] === $donde ) && ( '' === $que || in_array( $que, $t['especies'], true ) );
}

/**
 * The one-line result: «Dorado · Litoral: 12 destinos. Hoy, 24 de noviembre, se pesca en 12.»
 * @return array{n: int, m: int, texto: string}
 */
function pm_calendario_resumen( array $cal, string $donde, string $que, string $idioma ): array {
	$n = 0;
	$m = 0;
	foreach ( $cal['tarjetas'] as $t ) {
		if ( ! pm_calendario_coincide( $t, $donde, $que ) ) {
			continue;
		}
		$n++;
		$s = '' !== $que ? ( $t['estados'][ $que ] ?? 'sin' ) : $t['estado'];
		if ( 'mejor' === $s || 'pesca' === $s ) {
			$m++;
		}
	}
	$que_txt = array_filter( [ '' !== $que ? ( $cal['especies'][ $que ] ?? '' ) : '', '' !== $donde ? ( $cal['regiones'][ $donde ] ?? '' ) : '' ] );
	$cabeza  = $que_txt ? implode( ' · ', $que_txt ) . ': ' : '';
	if ( 0 === $n ) {
		return [ 'n' => 0, 'm' => 0, 'texto' => pm_t( 'cal.res.n0', [], $idioma ) ];
	}
	$texto = $cabeza . ( 1 === $n ? pm_t( 'cal.res.n1', [], $idioma ) : pm_t( 'cal.res.n', [ 'n' => (string) $n ], $idioma ) ) . '. '
		. pm_t( 0 === $m ? 'cal.res.hoy0' : 'cal.res.hoy', [ 'hoy' => pm_fecha_corta( $cal['hoy'], $idioma ), 'm' => (string) $m ], $idioma );
	return [ 'n' => $n, 'm' => $m, 'texto' => $texto ];
}
