<?php
/**
 * Shared pieces of the trip-request forms (/armar-viaje/ and the short card on every guide):
 * the field renderers (so both forms feed the same fields), what the person typed before a
 * validation error (kept in a short-lived transient, shown again by the form) and the
 * server-side validation and Spanish summaries (`cuando`, `personas`) built from the new fields.
 * Everything works without JavaScript; the theme script only adds steppers and reveals.
 */

defined( 'ABSPATH' ) || exit;

const PM_CONSULTA_MAX_PESCADORES   = 99;
const PM_CONSULTA_MAX_ACOMPANANTES = 30;

// --- What was typed before an error ---------------------------------------------------

/** Posted names kept to refill the form after a validation error. */
const PM_CONSULTA_PREVIO = [ 'nombre', 'contacto', 'contacto_via', 'contacto_email', 'contacto_tel', 'cuando', 'personas', 'pescadores', 'acompanantes', 'fecha_desde', 'fecha_hasta', 'flexibilidad', 'estadia', 'equipo_propio', 'duracion', 'experiencia', 'alojamiento', 'modalidad', 'especie', 'pais', 'mensaje', 'pm_destino' ];

/** Keeps what was typed (no spam fields, nothing internal) for 30 minutes; returns the token for the redirect. */
function pm_consulta_guardar_previo( array $errores ): string {
	$datos = [];
	foreach ( PM_CONSULTA_PREVIO as $k ) {
		$datos[ $k ] = mb_substr( sanitize_textarea_field( wp_unslash( (string) ( $_POST[ $k ] ?? '' ) ) ), 0, 2000 );
	}
	foreach ( [ 'fijo', 'meses' ] as $k ) {
		$datos[ $k ] = array_slice( array_map( fn( $c ) => sanitize_key( is_string( $c ) ? $c : '' ), (array) wp_unslash( $_POST[ $k ] ?? [] ) ), 0, 20 );
	}
	$token = strtolower( wp_generate_password( 16, false ) );
	set_transient( "pm_previo_{$token}", [ 'datos' => $datos, 'errores' => array_values( $errores ) ], 30 * MINUTE_IN_SECONDS );
	return $token;
}

/** [ 'datos' => [...], 'errores' => [...] ] of the failed attempt this page was redirected from; empty otherwise. */
function pm_consulta_previo(): array {
	static $r = null;
	if ( null === $r ) {
		$r     = [ 'datos' => [], 'errores' => [] ];
		$token = strtolower( preg_replace( '/[^a-z0-9]/i', '', (string) ( $_GET['pm_r'] ?? '' ) ) );
		if ( 'error' === pm_consulta_resultado() && 16 === strlen( $token ) ) {
			$g = get_transient( "pm_previo_{$token}" );
			if ( is_array( $g ) ) {
				$r = array_merge( $r, $g );
			}
		}
	}
	return $r;
}

function pm_previo( string $k, string $def = '' ): string {
	$v = pm_consulta_previo()['datos'][ $k ] ?? $def;
	return is_string( $v ) ? $v : $def;
}

function pm_previo_lista( string $k ): array {
	$v = pm_consulta_previo()['datos'][ $k ] ?? [];
	return is_array( $v ) ? $v : [];
}

function pm_consulta_tiene_error( string $e ): bool {
	return in_array( $e, pm_consulta_previo()['errores'], true );
}

/** Inline error under a field: "<p>" with an id the input points at through aria-describedby. */
function pm_error_campo( string $error, string $id ): string {
	return pm_consulta_tiene_error( $error ) ? '<p class="error-campo" id="' . esc_attr( $id ) . '">' . esc_html( pm_t( 'viaje.err.' . ( [ 'contacto_email' => 'email', 'contacto_tel' => 'whatsapp' ][ $error ] ?? $error ) ) ) . '</p>' : '';
}

// --- Renderers ----------------------------------------------------------------------

/**
 * A group of chips for one option list: radios, or checkboxes ($multiple: name[] and several can be ticked).
 * $lista: key in ui.json "opciones"; $campo: posted name; $solo: show only these keys; $marcado: key(s) ticked.
 */
function pm_chips( string $lista, string $campo, string $leyenda, $marcado = null, bool $multiple = false, string $ayuda = '', ?array $solo = null, string $prefijo = 'viaje' ): void {
	$claves  = array_keys( pm_ui()['opciones'][ $lista ] );
	$claves  = null === $solo ? $claves : array_values( array_intersect( $claves, $solo ) );
	$marcado = array_map( 'strval', (array) $marcado );
	echo '<fieldset class="opciones" data-campo="' . esc_attr( $campo ) . '"><legend>' . esc_html( $leyenda ) . '</legend><div class="chips-radio">';
	foreach ( $claves as $k ) {
		$id = "{$prefijo}-{$campo}-{$k}";
		printf(
			'<span><input type="%1$s" name="%2$s" id="%3$s" value="%4$s"%6$s><label for="%3$s">%5$s</label></span>',
			$multiple ? 'checkbox' : 'radio',
			esc_attr( $campo . ( $multiple ? '[]' : '' ) ),
			esc_attr( $id ),
			esc_attr( (string) $k ),
			esc_html( pm_opcion( $lista, (string) $k ) ),
			in_array( (string) $k, $marcado, true ) ? ' checked' : ''
		);
	}
	echo '</div>' . ( '' !== $ayuda ? '<small>' . esc_html( $ayuda ) . '</small>' : '' ) . '</fieldset>';
}

/** The next 12 months, first of the month first: [ 'YYYY-MM' => [ month number, year ] ]. */
function pm_consulta_meses( int $cuantos = 12 ): array {
	$mes = new DateTimeImmutable( 'first day of this month 00:00', wp_timezone() );
	$out = [];
	for ( $i = 0; $i < $cuantos; $i++ ) {
		$m                       = $mes->modify( "+{$i} month" );
		$out[ $m->format( 'Y-m' ) ] = [ (int) $m->format( 'n' ), (int) $m->format( 'Y' ) ];
	}
	return $out;
}

function pm_consulta_mes_etiqueta( string $ym, string $idioma ): string {
	[ $y, $m ] = array_map( 'intval', explode( '-', $ym ) );
	return pm_t( "viaje.mes.{$m}", [], $idioma ) . " {$y}";
}

/**
 * Dates as two native date inputs (the system calendar on every phone and desktop), the flexibility chips and,
 * for the long form, the month chips ("I do not know yet"). Without JavaScript everything is visible.
 */
function pm_campos_fechas( bool $largo, string $prefijo = 'viaje' ): void {
	$hoy   = wp_date( 'Y-m-d' );
	$limite = wp_date( 'Y-m-d', strtotime( '+2 years', time() ) );
	echo '<div class="fechas-bloque" data-fechas role="group" aria-labelledby="' . esc_attr( $prefijo ) . '-fechas-t">';
	echo '<div class="fechas-exactas"><p class="titulo-campo" id="' . esc_attr( $prefijo ) . '-fechas-t">' . esc_html( pm_t( 'viaje.fechas' ) ) . '</p><div class="grilla grilla-2">';
	foreach ( [ 'desde' => 'fecha_desde', 'hasta' => 'fecha_hasta' ] as $que => $campo ) {
		printf(
			'<div class="campo"><label for="%1$s-%2$s">%3$s</label><input type="date" id="%1$s-%2$s" name="%2$s" min="%4$s" max="%5$s" value="%6$s" autocomplete="off"></div>',
			esc_attr( $prefijo ),
			esc_attr( $campo ),
			esc_html( pm_t( "viaje.{$que}" ) ),
			esc_attr( $hoy ),
			esc_attr( $limite ),
			esc_attr( pm_previo( $campo ) )
		);
	}
	echo '</div>' . ( $largo ? '<small class="ayuda">' . esc_html( pm_t( 'viaje.fechas.ayuda' ) ) . '</small>' : '' ) . '</div>';
	pm_chips( 'flexibilidad', 'flexibilidad', pm_t( 'viaje.flex' ), pm_previo( 'flexibilidad' ), false, '', $largo ? null : [ 'exacta', 'dias', 'semana', 'mes' ], $prefijo );
	if ( $largo ) {
		// Compact grid: short month names, the year once per group; the full "mes año" stays in the accessible name.
		echo '<fieldset class="opciones meses" data-meses><legend>' . esc_html( pm_t( 'viaje.meses' ) ) . '</legend>';
		$marcados = pm_previo_lista( 'meses' );
		$grupos   = [];
		foreach ( pm_consulta_meses() as $ym => [ $m, $y ] ) {
			$grupos[ $y ][ $ym ] = $m;
		}
		foreach ( $grupos as $y => $lista ) {
			echo '<div class="meses-anio"><span class="anio" id="' . esc_attr( $prefijo ) . '-anio-' . (int) $y . '">' . (int) $y . '</span><div class="chips-radio" role="group" aria-labelledby="' . esc_attr( $prefijo ) . '-anio-' . (int) $y . '">';
			foreach ( $lista as $ym => $m ) {
				printf(
					'<span><input type="checkbox" name="meses[]" id="%1$s-mes-%2$s" value="%2$s"%5$s><label for="%1$s-mes-%2$s"><span aria-hidden="true">%3$s</span><span class="visually-hidden">%4$s</span></label></span>',
					esc_attr( $prefijo ),
					esc_attr( $ym ),
					esc_html( pm_t( "viaje.mes.corto.{$m}" ) ),
					esc_html( pm_consulta_mes_etiqueta( $ym, pm_lang() ) ),
					in_array( $ym, $marcados, true ) ? ' checked' : ''
				);
			}
			echo '</div></div>';
		}
		echo '<small>' . esc_html( pm_t( 'viaje.meses.ayuda' ) ) . '</small></fieldset>';
	}
	echo '</div>';
}

/** Anglers (default 1) and companions who do not fish (default 0): plain number inputs; JS adds the - / + buttons. */
function pm_campos_personas( string $prefijo = 'viaje', bool $ayudas = true ): void {
	echo '<div class="grilla grilla-2 personas">';
	$campos = [
		'pescadores'   => [ 1, PM_CONSULTA_MAX_PESCADORES, '1' ],
		'acompanantes' => [ 0, PM_CONSULTA_MAX_ACOMPANANTES, '0' ],
	];
	foreach ( $campos as $campo => [ $min, $max, $def ] ) {
		$id  = "{$prefijo}-{$campo}";
		$val = pm_previo( $campo, $def );
		printf(
			'<div class="campo"><label for="%1$s">%2$s</label><div class="stepper" data-menos="%3$s" data-mas="%4$s"><input type="number" id="%1$s" name="%5$s" min="%6$d" max="%7$d" step="1" value="%8$s" inputmode="numeric"%9$s></div>%10$s</div>',
			esc_attr( $id ),
			esc_html( pm_t( "viaje.{$campo}" ) ),
			esc_attr( pm_t( 'viaje.menos' ) ),
			esc_attr( pm_t( 'viaje.mas' ) ),
			esc_attr( $campo ),
			$min,
			$max,
			esc_attr( $val ),
			$ayudas ? ' aria-describedby="' . esc_attr( $id ) . '-a"' : '',
			$ayudas ? '<small id="' . esc_attr( $id ) . '-a">' . esc_html( pm_t( "viaje.{$campo}.ayuda" ) ) . '</small>' : ''
		);
	}
	echo '</div>';
}

// --- Validation and summaries ---------------------------------------------------------

/** A real calendar date inside the sane window (yesterday, for time zones, up to two years ahead) as Y-m-d, or ''. */
function pm_consulta_fecha( string $s ): string {
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $s, $m ) || ! checkdate( (int) $m[2], (int) $m[3], (int) $m[1] ) ) {
		return '';
	}
	return ( $s >= wp_date( 'Y-m-d', time() - DAY_IN_SECONDS ) && $s <= wp_date( 'Y-m-d', strtotime( '+2 years', time() ) ) ) ? $s : '';
}

/** Posted months: only the next 12 (one extra of margin for cached pages), in calendar order. */
function pm_consulta_meses_validos( $marcados ): array {
	$validos  = array_keys( pm_consulta_meses( 13 ) );
	$marcados = array_map( fn( $c ) => is_string( $c ) ? $c : '', (array) $marcados );
	return array_values( array_intersect( $validos, $marcados ) );
}

/** Whole number inside [min, max]; $def when it is not a number. */
function pm_consulta_entero( $v, int $min, int $max, int $def ): int {
	$v = is_string( $v ) ? trim( $v ) : '';
	return preg_match( '/^\d{1,4}$/', $v ) ? max( $min, min( $max, (int) $v ) ) : $def;
}

function pm_fecha_es( string $ymd ): string {
	return substr( $ymd, 8, 2 ) . '/' . substr( $ymd, 5, 2 ) . '/' . substr( $ymd, 0, 4 );
}

/**
 * Human-readable "when", in Spanish, for the notice, the admin box and the list: what the person typed (old form),
 * the dates, the months, the flexibility and the days of fishing. "11/01/2027 al 13/01/2027 · ± una semana · 2–3 días de pesca".
 * $d keys: cuando (typed), fecha_desde, fecha_hasta (Y-m-d or ''), meses (['YYYY-MM']), flexibilidad (key), estadia (key).
 */
function pm_consulta_componer_cuando( array $d ): string {
	$partes = [];
	if ( '' !== ( $d['cuando'] ?? '' ) ) {
		$partes[] = $d['cuando'];
	}
	$desde = $d['fecha_desde'] ?? '';
	$hasta = $d['fecha_hasta'] ?? '';
	if ( $desde && $hasta ) {
		$partes[] = $desde === $hasta ? pm_fecha_es( $desde ) : pm_fecha_es( $desde ) . ' al ' . pm_fecha_es( $hasta );
	} elseif ( $desde ) {
		$partes[] = 'desde el ' . pm_fecha_es( $desde );
	} elseif ( $hasta ) {
		$partes[] = 'hasta el ' . pm_fecha_es( $hasta );
	}
	$flex = $d['flexibilidad'] ?? '';
	if ( ! empty( $d['meses'] ) ) {
		$partes[] = ( 'nose' === $flex ? 'fechas sin definir, ' : '' ) . 'meses posibles: ' . implode( ', ', array_map( fn( $ym ) => pm_consulta_mes_etiqueta( $ym, 'es' ), $d['meses'] ) );
	} elseif ( 'nose' === $flex ) {
		$partes[] = 'fechas sin definir';
	}
	if ( $flex && 'nose' !== $flex && isset( pm_ui()['opciones']['flexibilidad'][ $flex ] ) ) {
		$partes[] = pm_opcion( 'flexibilidad', $flex, 'es' );
	}
	$estadia = $d['estadia'] ?? '';
	if ( $estadia && isset( pm_ui()['opciones']['estadia'][ $estadia ] ) ) {
		$partes[] = pm_opcion( 'estadia', $estadia, 'es' ) . ' de pesca';
	}
	return implode( ' · ', $partes );
}

/** "1 pescador", "2 pescadores + 1 acompañante que no pesca", "3 pescadores + 2 acompañantes que no pescan". */
function pm_consulta_componer_personas( int $pescadores, int $acompanantes ): string {
	$s = 1 === $pescadores ? '1 pescador' : "{$pescadores} pescadores";
	if ( $acompanantes > 0 ) {
		$s .= 1 === $acompanantes ? ' + 1 acompañante que no pesca' : " + {$acompanantes} acompañantes que no pescan";
	}
	return $s;
}

/**
 * The contact of the new form: the chosen channel (email or WhatsApp), validated; falls back to the other one when
 * only that one was filled. Returns [ contacto, via, error ] where error is '' | contacto | contacto_email | contacto_tel.
 */
function pm_consulta_resolver_contacto(): array {
	$via   = 'whatsapp' === sanitize_key( wp_unslash( $_POST['contacto_via'] ?? '' ) ) ? 'whatsapp' : 'email';
	$email = sanitize_email( wp_unslash( $_POST['contacto_email'] ?? '' ) );
	$crudo = trim( sanitize_text_field( wp_unslash( $_POST['contacto_email'] ?? '' ) ) );
	$tel   = trim( preg_replace( '/[^\d+\s().-]/', '', sanitize_text_field( wp_unslash( $_POST['contacto_tel'] ?? '' ) ) ) );
	$ok_e  = '' !== $email && is_email( $email );
	$ok_t  = preg_match( '/^\+?[\d\s().-]*$/', $tel ) && strlen( preg_replace( '/\D/', '', $tel ) ) >= 7 && strlen( preg_replace( '/\D/', '', $tel ) ) <= 15;
	$orden = 'email' === $via ? [ [ 'email', $ok_e, $email ], [ 'whatsapp', $ok_t, $tel ] ] : [ [ 'whatsapp', $ok_t, $tel ], [ 'email', $ok_e, $email ] ];
	foreach ( $orden as [ $canal, $valido, $valor ] ) {
		if ( $valido ) {
			return [ mb_substr( $valor, 0, 120 ), $canal, '' ];
		}
	}
	// Nothing valid: say what is wrong with the chosen channel.
	$escribio = 'email' === $via ? '' !== $crudo : '' !== $tel;
	return [ '', $via, $escribio ? ( 'email' === $via ? 'contacto_email' : 'contacto_tel' ) : 'contacto' ];
}
