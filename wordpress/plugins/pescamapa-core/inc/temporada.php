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
