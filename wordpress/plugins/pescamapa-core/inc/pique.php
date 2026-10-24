<?php
/**
 * Zona de pique (docs/pique.md): a community map of where fish are biting.
 *
 * Privacy by design: the browser snaps the position to a ~1 km grid cell and
 * sends only the cell's integer indexes. The server never receives, and so
 * never stores, an exact point. A report is: cell, species, day, result.
 * The only thing kept about the sender is a keyed hash of the IP, to stop
 * duplicates and floods (it cannot be reversed without the site's salts).
 *
 * Reports are private posts (type pm_reporte, wp-admin → Reportes de pique).
 * To hide one, move it to the trash. The public map shows a report only after
 * a delay (default 48 h), so a spot is never visible while someone is on it.
 * Spam: honeypot, minimum fill time and a per-IP hourly limit, as in consultas.php.
 */

defined( 'ABSPATH' ) || exit;

/** Grid: 0.01° ≈ 1.1 km north-south, ≈ 0.9 km east-west at Argentine latitudes. Indexes are floor(deg × 100). */
const PM_PIQUE_PASO = 100;
/** Argentina's bounding box in cell indexes (lat −56…−21, lon −74…−53). */
const PM_PIQUE_LIMITES = [ 'lat' => [ -5600, -2100 ], 'lon' => [ -7400, -5300 ] ];
const PM_PIQUE_RESULTADOS = [ 'bueno', 'regular', 'nada' ];
/** Species offered in the form (labels live in data/ui.json → etiquetas). */
const PM_PIQUE_ESPECIES = [ 'dorado', 'surubi', 'pati', 'pacu', 'boga', 'tararira', 'pejerrey', 'trucha', 'otro' ];
const PM_PIQUE_DIAS_ATRAS = 60;
const PM_PIQUE_DIAS_VISIBLE = 180;
const PM_PIQUE_MAX_HORA = 5;

function pm_registrar_tipo_pique(): void {
	register_post_type( 'pm_reporte', [
		'labels'        => [ 'name' => 'Reportes de pique', 'singular_name' => 'Reporte de pique', 'menu_name' => 'Pique', 'not_found' => 'Todavía no hay reportes.' ],
		'public'        => false,
		'show_ui'       => true,
		'show_in_rest'  => false,
		'menu_icon'     => 'dashicons-location',
		'menu_position' => 5,
		'supports'      => [ 'title' ],
		'capabilities'  => [ 'create_posts' => 'do_not_allow' ],
		'map_meta_cap'  => true,
		'rewrite'       => false,
	] );
}
add_action( 'init', 'pm_registrar_tipo_pique' );

function pm_pique_envio_url(): string {
	return admin_url( 'admin-post.php' );
}

/** Hidden fields of the report form (the JS adds the cell). */
function pm_pique_ocultos(): string {
	return '<input type="hidden" name="action" value="pm_pique"><input type="hidden" name="pm_t" value="' . esc_attr( (string) time() ) . '">'
		. '<div class="pm-trampa" aria-hidden="true"><label>Web <input type="text" name="pm_web" tabindex="-1" autocomplete="off"></label></div>';
}

/**
 * Store one report. Returns the post id, 0 for a duplicate (counts as done) or WP_Error.
 *
 * @param array $r celda_lat, celda_lon (ints), especie, resultado, dia (Ymd), huella
 */
function pm_pique_guardar( array $r ) {
	[ $lat, $lon ] = [ (int) $r['celda_lat'], (int) $r['celda_lon'] ];
	$l = PM_PIQUE_LIMITES;
	if ( $lat < $l['lat'][0] || $lat > $l['lat'][1] || $lon < $l['lon'][0] || $lon > $l['lon'][1] ) {
		return new WP_Error( 'celda', 'Zona fuera de la Argentina' );
	}
	if ( ! in_array( $r['especie'], PM_PIQUE_ESPECIES, true ) || ! in_array( $r['resultado'], PM_PIQUE_RESULTADOS, true ) ) {
		return new WP_Error( 'datos', 'Datos inválidos' );
	}
	$hoy   = (int) wp_date( 'Ymd' );
	$desde = (int) wp_date( 'Ymd', time() - PM_PIQUE_DIAS_ATRAS * DAY_IN_SECONDS );
	$dia   = (int) $r['dia'];
	if ( $dia > $hoy || $dia < $desde || ! checkdate( (int) substr( (string) $dia, 4, 2 ), (int) substr( (string) $dia, 6, 2 ), (int) substr( (string) $dia, 0, 4 ) ) ) {
		return new WP_Error( 'dia', 'Fecha inválida' );
	}
	$repetido = get_posts( [
		'post_type' => 'pm_reporte', 'post_status' => 'private', 'fields' => 'ids', 'posts_per_page' => 1, 'no_found_rows' => true,
		'meta_query' => [
			[ 'key' => 'huella', 'value' => $r['huella'] ],
			[ 'key' => 'celda', 'value' => "{$lat},{$lon}" ],
			[ 'key' => 'especie', 'value' => $r['especie'] ],
			[ 'key' => 'dia', 'value' => (string) $dia ],
		],
	] );
	if ( $repetido ) {
		return 0;
	}
	return wp_insert_post( [
		'post_type'   => 'pm_reporte',
		'post_status' => 'private',
		'post_title'  => sprintf( '%s · %s · %s', $r['especie'], $r['resultado'], pm_pique_celda_texto( $lat, $lon ) ),
		'meta_input'  => [
			'celda'      => "{$lat},{$lon}",
			'celda_lat'  => $lat,
			'celda_lon'  => $lon,
			'especie'    => $r['especie'],
			'resultado'  => $r['resultado'],
			'dia'        => (string) $dia,
			'huella'     => $r['huella'],
		],
	], true );
}

/** Southwest corner of a cell as text, e.g. "-27.84, -58.12". Cells, not points. */
function pm_pique_celda_texto( int $lat, int $lon ): string {
	return number_format( $lat / PM_PIQUE_PASO, 2, '.', '' ) . ', ' . number_format( $lon / PM_PIQUE_PASO, 2, '.', '' );
}

function pm_pique_huella(): string {
	return hash_hmac( 'sha256', (string) ( $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '' ), wp_salt( 'auth' ) );
}

function pm_pique_responder( bool $ok, string $error = '' ): void {
	wp_send_json( $ok ? [ 'ok' => true ] : [ 'ok' => false, 'error' => $error ], $ok ? 200 : 400 );
}

function pm_recibir_pique(): void {
	$t = (int) ( $_POST['pm_t'] ?? 0 );
	if ( ! empty( $_POST['pm_web'] ) || ! $t || time() - $t < 3 ) {
		pm_pique_responder( true ); // Quietly drop bots.
	}
	$huella = pm_pique_huella();
	$clave  = 'pm_pique_' . substr( $huella, 0, 32 );
	$veces  = (int) get_transient( $clave );
	if ( $veces >= PM_PIQUE_MAX_HORA ) {
		pm_pique_responder( false, 'limite' );
	}
	set_transient( $clave, $veces + 1, HOUR_IN_SECONDS );

	$id = pm_pique_guardar( [
		'celda_lat' => (int) ( $_POST['celda_lat'] ?? 0 ),
		'celda_lon' => (int) ( $_POST['celda_lon'] ?? 0 ),
		'especie'   => sanitize_key( $_POST['especie'] ?? '' ),
		'resultado' => sanitize_key( $_POST['resultado'] ?? '' ),
		'dia'       => preg_replace( '/\D/', '', (string) wp_unslash( $_POST['dia'] ?? '' ) ),
		'huella'    => $huella,
	] );
	pm_pique_responder( ! is_wp_error( $id ), is_wp_error( $id ) ? $id->get_error_code() : '' );
}
add_action( 'admin_post_nopriv_pm_pique', 'pm_recibir_pique' );
add_action( 'admin_post_pm_pique', 'pm_recibir_pique' );

// --- Aggregation ----------------------------------------------------------------

/** Seconds before a report can show on the public map. */
function pm_pique_demora(): int {
	return (int) apply_filters( 'pm_pique_demora', 2 * DAY_IN_SECONDS );
}

/**
 * Cells with at least one visible report: lat, lon (cell indexes), n, ultimo (Ymd),
 * especies (key => n), bueno (n of "bueno"). Only recent, non-trashed reports older than the delay.
 */
function pm_pique_celdas(): array {
	$cache = get_transient( 'pm_pique_celdas' );
	if ( is_array( $cache ) ) {
		return $cache;
	}
	$ids = get_posts( [
		'post_type' => 'pm_reporte', 'post_status' => 'private', 'fields' => 'ids', 'posts_per_page' => 5000, 'no_found_rows' => true,
		'date_query' => [ [ 'column' => 'post_date_gmt', 'before' => gmdate( 'Y-m-d H:i:s', time() - pm_pique_demora() ) ] ],
		'meta_query' => [ [ 'key' => 'dia', 'value' => wp_date( 'Ymd', time() - PM_PIQUE_DIAS_VISIBLE * DAY_IN_SECONDS ), 'compare' => '>=', 'type' => 'NUMERIC' ] ],
	] );
	$celdas = [];
	foreach ( $ids as $id ) {
		$c = (string) get_post_meta( $id, 'celda', true );
		$e = (string) get_post_meta( $id, 'especie', true );
		$d = (string) get_post_meta( $id, 'dia', true );
		if ( ! isset( $celdas[ $c ] ) ) {
			[ $lat, $lon ] = array_map( 'intval', explode( ',', $c ) + [ 0, 0 ] );
			$celdas[ $c ] = [ 'lat' => $lat, 'lon' => $lon, 'n' => 0, 'ultimo' => '', 'especies' => [], 'bueno' => 0 ];
		}
		$celdas[ $c ]['n']++;
		$celdas[ $c ]['ultimo']        = max( $celdas[ $c ]['ultimo'], $d );
		$celdas[ $c ]['especies'][ $e ] = ( $celdas[ $c ]['especies'][ $e ] ?? 0 ) + 1;
		if ( 'bueno' === get_post_meta( $id, 'resultado', true ) ) {
			$celdas[ $c ]['bueno']++;
		}
	}
	$celdas = array_values( $celdas );
	set_transient( 'pm_pique_celdas', $celdas, 15 * MINUTE_IN_SECONDS );
	return $celdas;
}

add_action( 'transition_post_status', function ( $nuevo, $viejo, $post ) {
	if ( 'pm_reporte' === $post->post_type ) {
		delete_transient( 'pm_pique_celdas' );
	}
}, 10, 3 );

/** Totals for the headline: reports, cells, latest day. */
function pm_pique_resumen(): array {
	$celdas = pm_pique_celdas();
	return [ 'reportes' => array_sum( array_column( $celdas, 'n' ) ), 'zonas' => count( $celdas ), 'ultimo' => $celdas ? max( array_column( $celdas, 'ultimo' ) ) : '' ];
}

/** Cells ready for the JS, with species already in the reader's language. */
function pm_pique_celdas_publicas(): array {
	return array_map( function ( $c ) {
		arsort( $c['especies'] );
		$esp = [];
		foreach ( $c['especies'] as $k => $n ) {
			$esp[] = [ pm_etiqueta( $k ), $n ];
		}
		return [ 'lat' => $c['lat'], 'lon' => $c['lon'], 'n' => $c['n'], 'bueno' => $c['bueno'], 'ultimo' => pm_fecha( $c['ultimo'] ), 'especies' => $esp ];
	}, pm_pique_celdas() );
}

function pm_url_pique( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['pique'], $i );
}

// --- wp-admin -------------------------------------------------------------------

add_filter( 'manage_pm_reporte_posts_columns', function () {
	return [ 'cb' => '<input type="checkbox">', 'title' => 'Reporte', 'pm_dia' => 'Día de pesca', 'pm_celda' => 'Zona (esquina SO)', 'date' => 'Llegó' ];
} );
add_action( 'manage_pm_reporte_posts_custom_column', function ( $col, $id ) {
	if ( 'pm_dia' === $col ) {
		echo esc_html( pm_fecha( (string) get_post_meta( $id, 'dia', true ), 'es' ) );
	} elseif ( 'pm_celda' === $col ) {
		echo esc_html( pm_pique_celda_texto( (int) get_post_meta( $id, 'celda_lat', true ), (int) get_post_meta( $id, 'celda_lon', true ) ) );
	}
}, 10, 2 );
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && 'pm_reporte' === $q->get( 'post_type' ) && ! $q->get( 'post_status' ) ) {
		$q->set( 'post_status', [ 'private', 'publish' ] );
	}
} );
