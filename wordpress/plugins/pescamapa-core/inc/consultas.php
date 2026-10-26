<?php
/**
 * Inquiries: every request from the site comes to Pescamapa, never straight
 * to an outfitter. That keeps the data central and lets us broker (and later
 * charge for) the business we bring.
 *
 * Two forms post here (wp-admin/admin-post.php, action pm_consulta):
 *   - "viaje": an angler asks for a quote in a destination guide (optionally
 *     about a given outfitter's card);
 *   - "prestador": a guide or lodge asks to join (home page).
 * Each one becomes a private "Consulta" post (wp-admin → Consultas), and an
 * email goes to the pm_email_consultas option (admin email by default).
 * Spam: a honeypot field, a minimum fill time and a per-IP hourly limit.
 */

defined( 'ABSPATH' ) || exit;

const PM_CONSULTA_ESTADOS = [ 'nueva' => 'Nueva', 'respondida' => 'Respondida', 'derivada' => 'Derivada a un prestador', 'concretada' => 'Concretada', 'cerrada' => 'Cerrada sin viaje' ];

/** Fields the forms send: name => [label, max length]. */
const PM_CONSULTA_CAMPOS = [
	'nombre'    => [ 'Nombre', 80 ],
	'contacto'  => [ 'WhatsApp o email', 120 ],
	'cuando'    => [ 'Cuándo', 80 ],
	'personas'  => [ 'Personas', 3 ],
	'modalidad' => [ 'Modalidad', 40 ],
	'duracion'  => [ 'Duración', 40 ],
	'experiencia' => [ 'Experiencia', 40 ],
	'alojamiento' => [ 'Alojamiento', 40 ],
	'pais'      => [ 'País desde el que viaja', 60 ],
	'especie'   => [ 'Especie', 40 ],
	'localidad' => [ 'Localidad', 80 ],
	'ofrece'    => [ 'Qué ofrece', 200 ],
	'mensaje'   => [ 'Mensaje', 2000 ],
];

function pm_url_viaje( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['viaje'], $i );
}

function pm_consulta_url_envio(): string {
	return admin_url( 'admin-post.php' );
}

/** Hidden fields every form carries. */
function pm_consulta_ocultos( string $tipo, array $extra = [] ): string {
	$campos = [
		'action'      => 'pm_consulta',
		'pm_tipo'     => $tipo,
		'pm_idioma'   => pm_lang(),
		'pm_volver'   => strtok( home_url( add_query_arg( [] ) ), '?' ),
		'pm_t'        => (string) time(),
	] + $extra;
	$html = '';
	foreach ( $campos as $k => $v ) {
		$html .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '">';
	}
	// Honeypot: people never see it, bots fill it.
	$html .= '<div class="pm-trampa" aria-hidden="true"><label>Web <input type="text" name="pm_web" tabindex="-1" autocomplete="off"></label></div>';
	return $html;
}

/** "ok" | "error" | "" for the form's own message after redirecting back. */
function pm_consulta_resultado(): string {
	$r = sanitize_key( $_GET['consulta'] ?? '' );
	return in_array( $r, [ 'ok', 'error' ], true ) ? $r : '';
}

function pm_recibir_consulta(): void {
	$volver = wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['pm_volver'] ?? '' ) ), home_url( '/' ) );
	$ir     = function ( string $r ) use ( $volver ): void {
		wp_safe_redirect( add_query_arg( 'consulta', $r, $volver ) . '#consulta', 303 );
		exit;
	};

	$t = (int) ( $_POST['pm_t'] ?? 0 );
	if ( ! empty( $_POST['pm_web'] ) || ! $t || time() - $t < 4 ) {
		$ir( 'ok' ); // Quietly drop bots.
	}
	$ip    = 'pm_consultas_' . md5( (string) ( $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$veces = (int) get_transient( $ip );
	if ( $veces >= 6 ) {
		$ir( 'error' );
	}
	set_transient( $ip, $veces + 1, HOUR_IN_SECONDS );

	$datos = [];
	foreach ( PM_CONSULTA_CAMPOS as $k => [ , $max ] ) {
		$v = 'mensaje' === $k ? sanitize_textarea_field( wp_unslash( $_POST[ $k ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_POST[ $k ] ?? '' ) );
		$datos[ $k ] = mb_substr( $v, 0, $max );
	}
	// Choice fields arrive as keys; keep the Spanish label so the email and the admin read naturally.
	foreach ( [ 'duracion', 'experiencia', 'alojamiento' ] as $campo ) {
		$clave = sanitize_key( wp_unslash( $_POST[ $campo ] ?? '' ) );
		$datos[ $campo ] = isset( pm_ui()['opciones'][ $campo ][ $clave ] ) ? pm_opcion( $campo, $clave, 'es' ) : '';
	}
	if ( '' === $datos['nombre'] || '' === $datos['contacto'] ) {
		$ir( 'error' );
	}
	$tipo      = 'prestador' === ( $_POST['pm_tipo'] ?? '' ) ? 'prestador' : 'viaje';
	$destino   = sanitize_key( $_POST['pm_destino'] ?? '' );
	$prestador = (int) ( $_POST['pm_prestador'] ?? 0 );
	if ( $prestador && 'pm_prestador' !== get_post_type( $prestador ) ) {
		$prestador = 0;
	}

	$lugar  = $destino ?: $datos['localidad'];
	$titulo = ( 'prestador' === $tipo ? 'Prestador: ' : 'Viaje: ' ) . $datos['nombre'] . ( $lugar ? " — {$lugar}" : '' );
	$id     = wp_insert_post( [
		'post_type'   => 'pm_consulta',
		'post_status' => 'private',
		'post_title'  => $titulo,
		'meta_input'  => array_filter( $datos ) + [
			'tipo'      => $tipo,
			'destino'   => $destino,
			'prestador' => $prestador,
			'idioma'    => pm_idioma_valido( sanitize_key( $_POST['pm_idioma'] ?? 'es' ) ),
			'origen'    => $volver,
			'estado'    => 'nueva',
		],
	], true );
	if ( is_wp_error( $id ) ) {
		$ir( 'error' );
	}
	pm_avisar_consulta( $id );
	$ir( 'ok' );
}
add_action( 'admin_post_nopriv_pm_consulta', 'pm_recibir_consulta' );
add_action( 'admin_post_pm_consulta', 'pm_recibir_consulta' );

/** Best-effort email; the Consulta post is the record either way. */
function pm_avisar_consulta( int $id ): void {
	$para   = get_option( 'pm_email_consultas' ) ?: get_option( 'admin_email' );
	$lineas = [];
	foreach ( pm_consulta_datos( $id ) as $etiqueta => $valor ) {
		$lineas[] = "{$etiqueta}: {$valor}";
	}
	$lineas[] = '';
	$lineas[] = 'Abrir: ' . admin_url( "post.php?post={$id}&action=edit" );
	wp_mail( $para, '[Pescamapa] ' . get_the_title( $id ), implode( "\n", $lineas ) );
}

/** Label => value of what the person sent, for the email and the admin box. */
function pm_consulta_datos( int $id ): array {
	$out = [ 'Tipo' => 'prestador' === get_post_meta( $id, 'tipo', true ) ? 'Prestador que quiere sumarse' : 'Pescador que pide cotización' ];
	foreach ( [ 'destino' => 'Destino' ] + array_map( fn( $c ) => $c[0], PM_CONSULTA_CAMPOS ) as $k => $etiqueta ) {
		$v = (string) get_post_meta( $id, $k, true );
		if ( '' !== $v ) {
			$out[ $etiqueta ] = $v;
		}
	}
	$prestador = (int) get_post_meta( $id, 'prestador', true );
	if ( $prestador ) {
		$out['Sobre el prestador'] = get_the_title( $prestador );
	}
	$out['Idioma'] = strtoupper( (string) get_post_meta( $id, 'idioma', true ) );
	$out['Página'] = (string) get_post_meta( $id, 'origen', true );
	$out['Fecha']  = get_the_date( 'd/m/Y H:i', $id );
	return $out;
}

/** wa.me link when the contact looks like a phone number. */
function pm_enlace_contacto( string $contacto ): string {
	if ( is_email( $contacto ) ) {
		return '<a href="mailto:' . esc_attr( $contacto ) . '">' . esc_html( $contacto ) . '</a>';
	}
	$num = preg_replace( '/\D/', '', $contacto );
	if ( strlen( $num ) >= 8 ) {
		return '<a href="https://wa.me/' . esc_attr( $num ) . '" target="_blank" rel="noopener">' . esc_html( $contacto ) . '</a>';
	}
	return esc_html( $contacto );
}

// --- wp-admin -------------------------------------------------------------------

add_action( 'add_meta_boxes_pm_consulta', function ( $post ) {
	add_meta_box( 'pm_consulta_datos', 'Lo que nos escribió', function ( $post ) {
		echo '<table class="widefat striped"><tbody>';
		foreach ( pm_consulta_datos( $post->ID ) as $etiqueta => $valor ) {
			$celda = 'WhatsApp o email' === $etiqueta ? pm_enlace_contacto( $valor ) : nl2br( esc_html( $valor ) );
			echo '<tr><th style="width:160px">' . esc_html( $etiqueta ) . '</th><td>' . $celda . '</td></tr>'; // phpcs:ignore -- escaped above
		}
		echo '</tbody></table>';
	}, null, 'normal', 'high' );
	add_meta_box( 'pm_consulta_seguimiento', 'Seguimiento', function ( $post ) {
		wp_nonce_field( 'pm_consulta_seguimiento', 'pm_consulta_nonce' );
		$estado = (string) get_post_meta( $post->ID, 'estado', true );
		echo '<p><label>Estado<br><select name="pm_estado" style="width:100%">';
		foreach ( PM_CONSULTA_ESTADOS as $k => $l ) {
			echo '<option value="' . esc_attr( $k ) . '"' . selected( $estado, $k, false ) . '>' . esc_html( $l ) . '</option>';
		}
		echo '</select></label></p>';
		$concretada = (int) get_post_meta( $post->ID, 'concretada_el', true );
		if ( $concretada ) {
			echo '<p class="description">Concretada el ' . esc_html( wp_date( 'd/m/Y', $concretada ) ) . '</p>';
		}
		$actual = (int) get_post_meta( $post->ID, 'derivada_a', true );
		echo '<p><label>Derivada a<br><select name="pm_derivada_a" style="width:100%"><option value="0">—</option>';
		foreach ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ] ) as $p ) {
			echo '<option value="' . (int) $p->ID . '"' . selected( $actual, $p->ID, false ) . '>' . esc_html( $p->post_title ) . '</option>';
		}
		echo '</select></label></p>';
		echo '<p><label>Notas<br><textarea name="pm_notas" rows="5" style="width:100%">' . esc_textarea( (string) get_post_meta( $post->ID, 'notas', true ) ) . '</textarea></label></p>';
	}, null, 'side', 'high' );
} );

add_action( 'save_post_pm_consulta', function ( $id ) {
	if ( ! isset( $_POST['pm_consulta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['pm_consulta_nonce'] ), 'pm_consulta_seguimiento' ) || ! current_user_can( 'edit_post', $id ) ) {
		return;
	}
	$estado = sanitize_key( $_POST['pm_estado'] ?? 'nueva' );
	update_post_meta( $id, 'estado', isset( PM_CONSULTA_ESTADOS[ $estado ] ) ? $estado : 'nueva' );
	// Review groundwork (docs/resenas.md): remember WHEN a trip became real, once.
	if ( 'concretada' === $estado && ! get_post_meta( $id, 'concretada_el', true ) ) {
		update_post_meta( $id, 'concretada_el', time() );
	}
	update_post_meta( $id, 'derivada_a', (int) ( $_POST['pm_derivada_a'] ?? 0 ) );
	update_post_meta( $id, 'notas', sanitize_textarea_field( wp_unslash( $_POST['pm_notas'] ?? '' ) ) );
} );

add_filter( 'manage_pm_consulta_posts_columns', function ( $cols ) {
	return [ 'cb' => $cols['cb'], 'title' => 'Consulta', 'pm_contacto' => 'Contacto', 'pm_cuando' => 'Cuándo / personas', 'pm_estado' => 'Estado', 'date' => 'Llegó' ];
} );
add_action( 'manage_pm_consulta_posts_custom_column', function ( $col, $id ) {
	if ( 'pm_contacto' === $col ) {
		echo pm_enlace_contacto( (string) get_post_meta( $id, 'contacto', true ) ); // phpcs:ignore -- escaped inside
	} elseif ( 'pm_cuando' === $col ) {
		echo esc_html( trim( get_post_meta( $id, 'cuando', true ) . ' · ' . get_post_meta( $id, 'personas', true ), ' ·' ) );
	} elseif ( 'pm_estado' === $col ) {
		$e = (string) get_post_meta( $id, 'estado', true );
		echo esc_html( ( 'nueva' === $e ? '🟠 ' : '' ) . ( PM_CONSULTA_ESTADOS[ $e ] ?? $e ) );
	}
}, 10, 2 );

// Private posts are this type's normal state: list them all by default.
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && 'pm_consulta' === $q->get( 'post_type' ) && ! $q->get( 'post_status' ) ) {
		$q->set( 'post_status', [ 'private', 'publish' ] );
	}
} );

// Count of new inquiries next to the menu item.
add_action( 'admin_menu', function () {
	global $menu;
	$nuevas = count( get_posts( [ 'post_type' => 'pm_consulta', 'post_status' => 'private', 'meta_key' => 'estado', 'meta_value' => 'nueva', 'fields' => 'ids', 'posts_per_page' => 50, 'no_found_rows' => true ] ) );
	if ( ! $nuevas ) {
		return;
	}
	foreach ( $menu as &$item ) {
		if ( 'edit.php?post_type=pm_consulta' === ( $item[2] ?? '' ) ) {
			$item[0] .= ' <span class="awaiting-mod">' . $nuevas . '</span>';
		}
	}
}, 100 );
