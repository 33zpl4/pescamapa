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

/**
 * Fields the forms send: name => [label, max length]. Shown in the notice and in "Lo que nos escribió".
 * `cuando` and `personas` are also BUILT from the newer fields (pm_consulta_componer_*), so the team reads one natural
 * line; the newer fields are saved as their own metas too (PM_CONSULTA_METAS_EXTRA) and are not repeated in the notice.
 */
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
	'fijo'      => [ 'Fijo (lo demás es flexible)', 120 ],
	'equipo_propio' => [ 'Equipo propio', 80 ],
	'especie'   => [ 'Especie', 40 ],
	'destinos_extra' => [ 'También sirven', 200 ],
	'localidad' => [ 'Localidad', 80 ],
	'ofrece'    => [ 'Qué ofrece', 200 ],
	'mensaje'   => [ 'Mensaje', 2000 ],
];

/** Metas saved from the newer form fields, not shown one by one (their summary is `cuando` / `personas`). */
const PM_CONSULTA_METAS_EXTRA = [ 'fecha_desde', 'fecha_hasta', 'flexibilidad', 'meses', 'estadia', 'pescadores', 'acompanantes', 'contacto_via' ];

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
		// Filled by the footer script: first page of this visit, the outside site it came from, utm_source.
		'pm_entrada'  => '',
		'pm_referente' => '',
		'pm_campana'  => '',
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

/**
 * Which channel brought the visit: fragment of the referrer host or of utm_source => label. First match wins, so the
 * assistants go before the search engines that host them (gemini.google.com, copilot.microsoft.com).
 */
const PM_CANALES = [
	'chatgpt' => 'ChatGPT', 'openai' => 'ChatGPT', 'perplexity' => 'Perplexity', 'gemini' => 'Gemini', 'copilot' => 'Copilot', 'claude' => 'Claude',
	'google' => 'Google', 'bing' => 'Bing', 'duckduckgo' => 'DuckDuckGo', 'yahoo' => 'Yahoo', 'yandex' => 'Yandex',
	'facebook' => 'Facebook', 'instagram' => 'Instagram', 'whatsapp' => 'WhatsApp', 'wa.me' => 'WhatsApp', 'youtube' => 'YouTube', 'reddit' => 'Reddit',
];

/** Host of a referrer URL (or a bare host), lowercase and without www.; '' for our own site or anything unusable. */
function pm_consulta_host( string $referente ): string {
	$host = (string) wp_parse_url( false === strpos( $referente, '//' ) ? "//{$referente}" : $referente, PHP_URL_HOST );
	$host = preg_replace( '/^www\./', '', strtolower( $host ) );
	$host = preg_match( '/^[a-z0-9.-]{3,100}$/', $host ) ? $host : '';
	$aca  = preg_replace( '/^www\./', '', strtolower( (string) wp_parse_url( home_url(), PHP_URL_HOST ) ) );
	return $host === $aca ? '' : $host;
}

/** Channel label for the admin and the notice: a known name, else the referrer host, else '' (direct or unknown). */
function pm_consulta_canal( string $host, string $campana ): string {
	foreach ( [ strtolower( $campana ), $host ] as $pista ) {
		foreach ( PM_CANALES as $trozo => $nombre ) {
			if ( '' !== $pista && false !== strpos( $pista, $trozo ) ) {
				return $nombre;
			}
		}
	}
	return $host ?: mb_substr( $campana, 0, 60 );
}

function pm_recibir_consulta(): void {
	$volver = wp_validate_redirect( esc_url_raw( wp_unslash( $_POST['pm_volver'] ?? '' ) ), home_url( '/' ) );
	$ir     = function ( string $r, array $extra = [] ) use ( $volver ): void {
		wp_safe_redirect( add_query_arg( [ 'consulta' => $r ] + $extra, $volver ) . '#consulta', 303 );
		exit;
	};
	$tipo   = 'prestador' === ( $_POST['pm_tipo'] ?? '' ) ? 'prestador' : 'viaje';
	// A trip request that fails validation goes back with what was typed (30 minutes) and the fields to fix.
	$fallar = function ( array $errores ) use ( $ir, $tipo ): void {
		$ir( 'error', 'viaje' === $tipo ? [ 'pm_r' => pm_consulta_guardar_previo( $errores ) ] : [] );
	};

	$t = (int) ( $_POST['pm_t'] ?? 0 );
	if ( ! empty( $_POST['pm_web'] ) || ! $t || time() - $t < 4 ) {
		$ir( 'ok' ); // Quietly drop bots.
	}
	$ip    = 'pm_consultas_' . md5( (string) ( $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$veces = (int) get_transient( $ip );
	if ( $veces >= 6 ) {
		$fallar( [ 'limite' ] );
	}
	set_transient( $ip, $veces + 1, HOUR_IN_SECONDS );

	$datos = [];
	foreach ( PM_CONSULTA_CAMPOS as $k => [ , $max ] ) {
		$v = 'mensaje' === $k ? sanitize_textarea_field( wp_unslash( $_POST[ $k ] ?? '' ) ) : sanitize_text_field( wp_unslash( $_POST[ $k ] ?? '' ) );
		$datos[ $k ] = mb_substr( $v, 0, $max );
	}
	// Choice fields arrive as keys; keep the Spanish label so the email and the admin read naturally.
	$etiqueta_es = function ( string $campo, string $lista ): array {
		$clave = sanitize_key( wp_unslash( $_POST[ $campo ] ?? '' ) );
		return isset( pm_ui()['opciones'][ $lista ][ $clave ] ) ? [ $clave, pm_opcion( $lista, $clave, 'es' ) ] : [ '', '' ];
	};
	foreach ( [ 'duracion' => 'duracion', 'experiencia' => 'experiencia', 'alojamiento' => 'alojamiento', 'equipo_propio' => 'traeequipo' ] as $campo => $lista ) {
		$datos[ $campo ] = $etiqueta_es( $campo, $lista )[1];
	}
	[ $estadia, $datos['estadia'] ]           = $etiqueta_es( 'estadia', 'estadia' );
	[ $flexibilidad, $datos['flexibilidad'] ] = $etiqueta_es( 'flexibilidad', 'flexibilidad' );
	// Optional "what is fixed" checkboxes (fijo[]): saved as one line of Spanish labels in the list's order; '' when none ticked.
	$marcadas = array_map( fn( $c ) => sanitize_key( is_string( $c ) ? $c : '' ), (array) wp_unslash( $_POST['fijo'] ?? [] ) );
	$datos['fijo'] = implode( ', ', array_map( fn( $k ) => pm_opcion( 'fijo', $k, 'es' ), array_values( array_intersect( array_keys( pm_ui()['opciones']['fijo'] ), $marcadas ) ) ) );

	// Dates and months (never trusted from the client: anything that is not a real, sensible date is dropped), people and the summaries.
	$desde = pm_consulta_fecha( sanitize_text_field( wp_unslash( $_POST['fecha_desde'] ?? '' ) ) );
	$hasta = pm_consulta_fecha( sanitize_text_field( wp_unslash( $_POST['fecha_hasta'] ?? '' ) ) );
	if ( $desde && $hasta && $hasta < $desde ) {
		$hasta = '';
	}
	$meses = pm_consulta_meses_validos( wp_unslash( $_POST['meses'] ?? [] ) );
	$datos['fecha_desde'] = $desde;
	$datos['fecha_hasta'] = $hasta;
	$datos['meses']       = implode( ', ', array_map( fn( $ym ) => pm_consulta_mes_etiqueta( $ym, 'es' ), $meses ) );
	$cuando = pm_consulta_componer_cuando( [ 'cuando' => $datos['cuando'], 'fecha_desde' => $desde, 'fecha_hasta' => $hasta, 'meses' => $meses, 'flexibilidad' => $flexibilidad, 'estadia' => $estadia ] );
	$datos['cuando'] = mb_substr( $cuando, 0, 400 );
	if ( isset( $_POST['pescadores'] ) || isset( $_POST['acompanantes'] ) ) {
		$pesc = pm_consulta_entero( wp_unslash( $_POST['pescadores'] ?? '' ), 1, PM_CONSULTA_MAX_PESCADORES, 1 );
		$acom = pm_consulta_entero( wp_unslash( $_POST['acompanantes'] ?? '' ), 0, PM_CONSULTA_MAX_ACOMPANANTES, 0 );
		$datos['pescadores']   = (string) $pesc;
		$datos['acompanantes'] = (string) $acom;
		$datos['personas']     = pm_consulta_componer_personas( $pesc, $acom );
	}

	// Contact: the new form sends contacto_email / contacto_tel and the chosen channel; the old forms send `contacto`.
	$errores = [];
	if ( isset( $_POST['contacto_email'] ) || isset( $_POST['contacto_tel'] ) ) {
		[ $datos['contacto'], $datos['contacto_via'], $error ] = pm_consulta_resolver_contacto();
		if ( $error ) {
			$errores[] = $error;
		}
	} elseif ( '' === $datos['contacto'] ) {
		$errores[] = 'contacto';
	}
	if ( '' === $datos['nombre'] ) {
		array_unshift( $errores, 'nombre' );
	}
	if ( $errores ) {
		$fallar( $errores );
	}
	$destino   = sanitize_key( $_POST['pm_destino'] ?? '' );
	// "También me sirven": other published guides the person would take too (validated, never the main one, at most five); '' when none.
	$extra = pm_consulta_destinos_extra( wp_unslash( $_POST['destinos_extra'] ?? [] ), $destino );
	$datos['destinos_extra'] = $extra ? pm_consulta_destinos_extra_linea( $extra ) : '';
	$prestador = (int) ( $_POST['pm_prestador'] ?? 0 );
	if ( $prestador && 'pm_prestador' !== get_post_type( $prestador ) ) {
		$prestador = 0;
	}

	// Attribution, sent by the footer script (no cookies): which page and which channel bring the people who ask.
	$entrada   = sanitize_text_field( wp_unslash( $_POST['pm_entrada'] ?? '' ) );
	$entrada   = '/' === ( $entrada[0] ?? '' ) && '/' !== ( $entrada[1] ?? '' ) ? mb_substr( $entrada, 0, 200 ) : '';
	$referente = pm_consulta_host( sanitize_text_field( wp_unslash( $_POST['pm_referente'] ?? '' ) ) );
	$campana   = mb_substr( sanitize_text_field( wp_unslash( $_POST['pm_campana'] ?? '' ) ), 0, 60 );

	$lugar  = $destino ?: $datos['localidad'];
	$titulo = ( 'prestador' === $tipo ? 'Prestador: ' : 'Viaje: ' ) . $datos['nombre'] . ( $lugar ? " — {$lugar}" : '' );
	$id     = wp_insert_post( [
		'post_type'   => 'pm_consulta',
		'post_status' => 'private',
		'post_title'  => $titulo,
		'meta_input'  => array_filter( $datos + [ 'entrada' => $entrada, 'referente' => $referente, 'campana' => $campana, 'canal' => pm_consulta_canal( $referente, $campana ) ] ) + [
			'tipo'      => $tipo,
			'destino'   => $destino,
			'prestador' => $prestador,
			'idioma'    => pm_idioma_valido( sanitize_key( $_POST['pm_idioma'] ?? 'es' ) ),
			'origen'    => $volver,
			'estado'    => 'nueva',
		],
	], true );
	if ( is_wp_error( $id ) ) {
		$fallar( [ 'general' ] );
	}
	pm_avisar_consulta( $id );
	pm_acusar_consulta( $id );
	$ir( 'ok' );
}
add_action( 'admin_post_nopriv_pm_consulta', 'pm_recibir_consulta' );
add_action( 'admin_post_pm_consulta', 'pm_recibir_consulta' );

/** Plain text for an email subject or body: no HTML entities (WordPress stores and filters titles with them). */
function pm_texto_plano( string $s ): string {
	return html_entity_decode( $s, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
}

/** Subject of the team notice: the Consulta's raw title as plain text ("O’Connor", never "O&#8217;Connor"). */
function pm_asunto_aviso( int $id ): string {
	return '[Pescamapa] ' . pm_texto_plano( (string) get_post_field( 'post_title', $id, 'raw' ) );
}

/**
 * wp_mail() that remembers what happened on the Consulta: correo_<que>_estado (enviado | fallo), correo_<que>_el
 * (site time) and, on failure, correo_<que>_error when wp_mail_failed told us why. $que: aviso | acuse.
 */
function pm_enviar_registrando( int $id, string $que, string $para, string $asunto, string $cuerpo, array $cabeceras = [] ): bool {
	$error  = '';
	$oyente = function ( $e ) use ( &$error ) {
		$error = $e instanceof WP_Error ? $e->get_error_message() : '';
	};
	add_action( 'wp_mail_failed', $oyente );
	$ok = (bool) wp_mail( $para, $asunto, $cuerpo, $cabeceras );
	remove_action( 'wp_mail_failed', $oyente );
	pm_consulta_registrar_correo( $id, $que, $ok ? 'enviado' : 'fallo', $ok ? '' : $error );
	return $ok;
}

function pm_consulta_registrar_correo( int $id, string $que, string $estado, string $error = '' ): void {
	update_post_meta( $id, "correo_{$que}_estado", $estado );
	update_post_meta( $id, "correo_{$que}_el", current_time( 'mysql' ) );
	if ( '' !== $error ) {
		update_post_meta( $id, "correo_{$que}_error", mb_substr( $error, 0, 300 ) );
	} else {
		delete_post_meta( $id, "correo_{$que}_error" );
	}
}

/** Best-effort email; the Consulta post is the record either way. */
function pm_avisar_consulta( int $id ): void {
	$para   = get_option( 'pm_email_consultas' ) ?: get_option( 'admin_email' );
	$lineas = [];
	foreach ( pm_consulta_datos( $id ) as $etiqueta => $valor ) {
		$lineas[] = "{$etiqueta}: {$valor}";
	}
	$lineas[] = '';
	$lineas[] = 'Abrir: ' . admin_url( "post.php?post={$id}&action=edit" );
	// "Reply" in the inbox answers the angler directly when they left an email.
	$contacto = (string) get_post_meta( $id, 'contacto', true );
	pm_enviar_registrando( $id, 'aviso', $para, pm_asunto_aviso( $id ), implode( "\n", $lineas ), is_email( $contacto ) ? [ 'Reply-To: ' . $contacto ] : [] );
}

/**
 * Up to two helpful links for a trip request, in the sender's language: the destination guide (when there is a
 * published one in that language) and, for en/pt, the licence note for foreigners (when it exists in that language).
 * Each is [ label, url ]; [] when nothing resolves.
 */
function pm_acuse_enlaces( string $destino, string $idioma ): array {
	$out = [];
	$guia = '' !== $destino ? pm_buscar( 'pm_destino', $destino, $idioma ) : null;
	if ( $guia ) {
		$out[] = [ pm_texto_plano( get_the_title( $guia->ID ) ), get_permalink( $guia->ID ) ];
	}
	if ( in_array( $idioma, [ 'en', 'pt' ], true ) ) {
		$nota = pm_buscar( 'pm_nota', PM_NOTA_LICENCIA_EXTRANJEROS, $idioma );
		if ( $nota ) {
			$out[] = [ pm_texto_plano( get_the_title( $nota->ID ) ), get_permalink( $nota->ID ) ];
		}
	}
	return $out;
}

/** Clave of the foreigners' fishing-licence note (es: /notas/licencia-pesca-extranjeros-argentina-2026-27/). */
const PM_NOTA_LICENCIA_EXTRANJEROS = 'licencias-extranjeros-argentina';

/**
 * Body of the acknowledgement: the fixed text, then (trip requests only) the optional links block, then the signature.
 * Pure given the site content: nothing the person typed goes back out.
 */
function pm_acuse_cuerpo( string $tipo, string $idioma, string $destino = '' ): string {
	$cuerpo = pm_t( 'prestador' === $tipo ? 'acuse.prestador' : 'acuse.viaje', [], $idioma );
	$enlaces = 'prestador' === $tipo ? [] : pm_acuse_enlaces( $destino, $idioma );
	if ( $enlaces ) {
		$cuerpo .= "\n\n" . pm_t( 'acuse.ayuda', [], $idioma );
		foreach ( $enlaces as [ $etiqueta, $url ] ) {
			$cuerpo .= "\n\n{$etiqueta}\n{$url}";
		}
	}
	return $cuerpo . "\n\nPescamapa\n" . pm_url_inicio( $idioma );
}

/**
 * Receipt to the person who asked, when the contact is an email: in their language, a fixed text (nothing they typed
 * goes back out, so the form cannot be used to send someone else a message), and replies land in our inbox.
 */
function pm_acusar_consulta( int $id ): void {
	$contacto = (string) get_post_meta( $id, 'contacto', true );
	if ( ! is_email( $contacto ) ) {
		pm_consulta_registrar_correo( $id, 'acuse', 'sin_email' );
		return;
	}
	$idioma = (string) get_post_meta( $id, 'idioma', true );
	$cuerpo = pm_acuse_cuerpo( (string) get_post_meta( $id, 'tipo', true ), $idioma, (string) get_post_meta( $id, 'destino', true ) );
	pm_enviar_registrando( $id, 'acuse', $contacto, pm_t( 'acuse.asunto', [], $idioma ), $cuerpo, [ 'Reply-To: ' . PM_EMAIL_PUBLICO ] );
}

/** What happened to one of the two emails: [ estado, fecha, error ]; estado '' when there is no record (old Consultas). */
function pm_consulta_correo( int $id, string $que ): array {
	return [
		(string) get_post_meta( $id, "correo_{$que}_estado", true ),
		(string) get_post_meta( $id, "correo_{$que}_el", true ),
		(string) get_post_meta( $id, "correo_{$que}_error", true ),
	];
}

/** One-line summary for the Consultas list: "Aviso ✓ · Acuse ✓", "Acuse ✗", "Acuse — (sin email)", "sin dato". */
function pm_correos_resumen( int $id ): string {
	$partes = [];
	foreach ( [ 'aviso' => 'Aviso', 'acuse' => 'Acuse' ] as $que => $nombre ) {
		$estado = pm_consulta_correo( $id, $que )[0];
		if ( 'enviado' === $estado ) {
			$partes[] = "{$nombre} ✓";
		} elseif ( 'fallo' === $estado ) {
			$partes[] = "{$nombre} ✗";
		} elseif ( 'sin_email' === $estado ) {
			$partes[] = "{$nombre} — (sin email)";
		}
	}
	return $partes ? implode( ' · ', $partes ) : 'sin dato';
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
		$out['Sobre el prestador'] = pm_texto_plano( get_the_title( $prestador ) );
	}
	$out['Idioma'] = strtoupper( (string) get_post_meta( $id, 'idioma', true ) );
	$out['Página'] = (string) get_post_meta( $id, 'origen', true );
	foreach ( [ 'canal' => 'Canal', 'referente' => 'Vino desde', 'entrada' => 'Entró al sitio por', 'campana' => 'Campaña (utm_source)' ] as $k => $etiqueta ) {
		$v = (string) get_post_meta( $id, $k, true );
		if ( '' !== $v ) {
			$out[ $etiqueta ] = $v;
		}
	}
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
	add_meta_box( 'pm_consulta_correos', 'Correos', function ( $post ) {
		$estados = [ 'enviado' => 'Enviado', 'fallo' => 'No se pudo enviar', 'sin_email' => 'No corresponde (el contacto no es un email)' ];
		echo '<table class="widefat striped"><tbody>';
		foreach ( [ 'aviso' => 'Aviso al equipo', 'acuse' => 'Acuse al remitente' ] as $que => $etiqueta ) {
			[ $estado, $el, $error ] = pm_consulta_correo( $post->ID, $que );
			$txt = $estados[ $estado ] ?? 'sin dato';
			if ( $el ) {
				$txt .= ' · ' . $el;
			}
			if ( $error ) {
				$txt .= ' · ' . $error;
			}
			echo '<tr><th style="width:160px">' . esc_html( $etiqueta ) . '</th><td>' . esc_html( $txt ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}, null, 'normal', 'default' );
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
	return [ 'cb' => $cols['cb'], 'title' => 'Consulta', 'pm_contacto' => 'Contacto', 'pm_cuando' => 'Cuándo / personas', 'pm_canal' => 'Canal', 'pm_correos' => 'Correos', 'pm_estado' => 'Estado', 'date' => 'Llegó' ];
} );
add_action( 'manage_pm_consulta_posts_custom_column', function ( $col, $id ) {
	if ( 'pm_contacto' === $col ) {
		echo pm_enlace_contacto( (string) get_post_meta( $id, 'contacto', true ) ); // phpcs:ignore -- escaped inside
	} elseif ( 'pm_cuando' === $col ) {
		echo esc_html( trim( get_post_meta( $id, 'cuando', true ) . ' · ' . get_post_meta( $id, 'personas', true ), ' ·' ) );
	} elseif ( 'pm_canal' === $col ) {
		echo esc_html( (string) get_post_meta( $id, 'canal', true ) ?: '—' );
	} elseif ( 'pm_correos' === $col ) {
		echo esc_html( pm_correos_resumen( (int) $id ) );
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
