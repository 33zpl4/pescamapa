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
 * «Temporadas, en una mirada» of the calendar page (23/11/2026): one row per published guide that carries BOTH official
 * season dates (temporada_abre / temporada_cierra), grouped by region in the page's order, with the authority, its link and the
 * date the regulation was verified. The data model has no separate closed-season (veda) field, so nothing is inferred: a row says
 * «season from … to …» and, against today (Argentina), «open today», «closed today: opens on …» or «closed today: the season ended on …».
 * The lead sentence counts those rows and names the next season that opens. Guides without both dates are left out of the block.
 * Cached per language and day in a transient (key carries the pm_mes_ver option, bumped when a guide is saved).
 * @return array{hoy: string, grupos: array<string, array<int, array<string, mixed>>>, abiertas: int, cerradas: int, total: int, proxima: array<string, string>, lead: string, faq: array<string, string>}
 */
function pm_vedas_resumen( string $idioma, ?string $hoy = null ): array {
	$idioma = pm_idioma_valido( $idioma );
	$hoy    = $hoy ?: wp_date( 'Y-m-d', null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
	$clave  = 'pm_vedas_' . (int) get_option( 'pm_mes_ver', 1 ) . "_{$idioma}_{$hoy}";
	$cache  = get_transient( $clave );
	if ( is_array( $cache ) ) {
		return $cache;
	}
	$grupos   = [];
	$abiertas = 0;
	$cerradas = 0;
	$proxima  = [];
	foreach ( pm_destinos_lista( $idioma ) as $d ) {
		if ( ! pm_temporada_hay( $d ) ) {
			continue;
		}
		$estado = pm_temporada_estado( $d, $hoy );
		$r      = (array) ( $d['reglamento'] ?? [] );
		if ( 'abierta' === $estado ) {
			$abiertas++;
		} else {
			$cerradas++;
		}
		if ( 'antes' === $estado && ( ! $proxima || $d['abre'] < $proxima['ymd'] ) ) {
			$proxima = [ 'ymd' => $d['abre'], 'lugar' => (string) ( $d['nombre'] ?? '' ), 'clave' => (string) ( $d['clave'] ?? '' ) ];
		}
		$grupos[ ( $d['region'] ?? '' ) ?: 'otros' ][] = [
			'clave'      => (string) ( $d['clave'] ?? '' ),
			'nombre'     => (string) ( $d['nombre'] ?? '' ),
			'url'        => (string) ( $d['url'] ?? '' ),
			'provincia'  => (string) ( PM_PROVINCIAS[ $d['provincia'] ?? '' ] ?? ( $d['provincia'] ?? '' ) ),
			'estado'     => $estado,
			'abre'       => (string) $d['abre'],
			'cierra'     => (string) $d['cierra'],
			'autoridad'  => (string) ( $r['autoridad'] ?? '' ),
			'fuente_url' => (string) ( $r['url'] ?? '' ),
			'verificado' => (string) ( $r['verificado'] ?? '' ),
		];
	}
	$total = $abiertas + $cerradas;
	$lead  = '';
	if ( $total ) {
		$lead = pm_t( 'cal.vm.lead', [ 'hoy' => pm_fecha( $hoy, $idioma ), 'a' => (string) $abiertas, 'c' => (string) $cerradas, 't' => (string) $total ], $idioma );
		if ( $proxima ) {
			$lead .= ' ' . pm_t( 'cal.vm.proxima', [ 'lugar' => $proxima['lugar'], 'fecha' => pm_fecha( $proxima['ymd'], $idioma ) ], $idioma );
		}
	}
	$out = [
		'hoy'      => $hoy,
		'grupos'   => $grupos,
		'abiertas' => $abiertas,
		'cerradas' => $cerradas,
		'total'    => $total,
		'proxima'  => $proxima,
		'lead'     => $lead,
		'faq'      => $lead ? [ 'pregunta' => pm_t( 'cal.vm.faq.q', [], $idioma ), 'respuesta' => $lead . ' ' . pm_t( 'cal.vm.faq.a', [], $idioma ) ] : [],
	];
	set_transient( $clave, $out, 6 * HOUR_IN_SECONDS );
	return $out;
}
