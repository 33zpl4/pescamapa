<?php
/**
 * Outgoing mail through Resend (added 4/11/2026). The server has no mail program (wp_mail() fails with "No se pudo
 * establecer la función de correo", checked with the email-prueba script), and the domain's SPF only authorises
 * Cloudflare Email Routing, which receives but does not send. With a Resend API key in place, every wp_mail() call
 * (the inquiry notices) goes out through Resend's API, from info@pescamapa.com, DKIM-signed by the verified domain.
 * Replies come back to info@pescamapa.com and Cloudflare routes them to the owner's inbox.
 *
 * The key is read from the PM_RESEND_KEY constant (wp-config.php) or from the pm_resend_key option, which the
 * "Set the mail key" workflow writes from a repo secret. With no key this file does nothing and wp_mail() behaves as before.
 */

defined( 'ABSPATH' ) || exit;

/** The Resend API key, '' when none is configured. */
function pm_correo_clave(): string {
	if ( defined( 'PM_RESEND_KEY' ) && PM_RESEND_KEY ) {
		return (string) PM_RESEND_KEY;
	}
	return trim( (string) get_option( 'pm_resend_key', '' ) );
}

/** "Name <a@b>" or "a@b" → a@b. */
function pm_correo_direccion( string $s ): string {
	return preg_match( '/<([^>]+)>/', $s, $m ) ? trim( $m[1] ) : trim( $s );
}

/** The JSON body for Resend's /emails endpoint from wp_mail() arguments. Pure, so it can be tested without sending. */
function pm_correo_payload( array $atts ): array {
	$para = $atts['to'] ?? [];
	$para = array_values( array_filter( array_map( 'pm_correo_direccion', is_array( $para ) ? $para : explode( ',', (string) $para ) ) ) );
	$cab  = $atts['headers'] ?? [];
	$cab  = is_array( $cab ) ? $cab : preg_split( '/\r\n|\r|\n/', (string) $cab );
	$resp = '';
	$html = false;
	foreach ( $cab as $linea ) {
		if ( preg_match( '/^reply-to:\s*(.+)$/i', (string) $linea, $m ) ) {
			$resp = pm_correo_direccion( $m[1] );
		} elseif ( preg_match( '/^content-type:\s*text\/html/i', (string) $linea ) ) {
			$html = true;
		}
	}
	$p = [
		'from'    => 'Pescamapa <' . PM_EMAIL_PUBLICO . '>',
		'to'      => $para,
		'subject' => (string) ( $atts['subject'] ?? '' ),
		$html ? 'html' : 'text' => (string) ( $atts['message'] ?? '' ),
	];
	if ( $resp ) {
		$p['reply_to'] = $resp;
	}
	return $p;
}

/**
 * Short-circuit wp_mail(): null (do nothing) without a key or with attachments; true/false once Resend answered.
 * A failure fires wp_mail_failed like the core function would.
 */
add_filter( 'pre_wp_mail', function ( $retorno, array $atts ) {
	$clave = pm_correo_clave();
	if ( ! $clave || ! empty( $atts['attachments'] ) ) {
		return $retorno;
	}
	$p = pm_correo_payload( $atts );
	if ( ! $p['to'] ) {
		return $retorno;
	}
	$r = wp_remote_post( 'https://api.resend.com/emails', [
		'timeout' => 15,
		'headers' => [ 'Authorization' => 'Bearer ' . $clave, 'Content-Type' => 'application/json' ],
		'body'    => wp_json_encode( $p ),
	] );
	if ( is_wp_error( $r ) ) {
		do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', 'Resend: ' . $r->get_error_message(), $p ) );
		return false;
	}
	$codigo = (int) wp_remote_retrieve_response_code( $r );
	if ( $codigo < 200 || $codigo >= 300 ) {
		do_action( 'wp_mail_failed', new WP_Error( 'wp_mail_failed', "Resend respondió $codigo: " . mb_substr( (string) wp_remote_retrieve_body( $r ), 0, 300 ), $p ) );
		return false;
	}
	return true;
}, 10, 2 );
