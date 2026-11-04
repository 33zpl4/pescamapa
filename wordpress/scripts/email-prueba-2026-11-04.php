<?php
/**
 * 4/11/2026: ONE test message through wp_mail() to the inquiry destination (pm_email_consultas), the same path as the
 * inquiry notices. Captures the failure reason if wp_mail refuses, and prints the sender and recipient used.
 * Sends nothing else, writes nothing. Run with wp-script.yml (script = email-prueba-2026-11-04).
 */

$para  = get_option( 'pm_email_consultas' ) ?: get_option( 'admin_email' );
$error = '';
add_action( 'wp_mail_failed', function ( $e ) use ( &$error ) {
	$error = $e->get_error_message();
} );
$remitente = null;
add_action( 'phpmailer_init', function ( $m ) use ( &$remitente ) {
	$remitente = $m->From . ' via ' . $m->Mailer . ( 'smtp' === $m->Mailer ? ' ' . $m->Host . ':' . $m->Port : '' ) . ' (sendmail: ' . $m->Sendmail . ')';
} );

$asunto = '[Pescamapa] Prueba de correo ' . gmdate( 'Y-m-d H:i' ) . ' UTC';
$ok     = wp_mail( $para, $asunto, "Prueba del aviso de consultas de Pescamapa.\nSi leés esto, el servidor puede enviar correo a $para.\nNo requiere respuesta." );

WP_CLI::log( "Para: $para" );
WP_CLI::log( 'Asunto: ' . $asunto );
WP_CLI::log( 'Remitente/transporte: ' . ( $remitente ?? '(phpmailer no se inició)' ) );
WP_CLI::log( 'wp_mail devolvió: ' . ( $ok ? 'true (aceptado por el transporte local, no prueba la entrega)' : 'false' ) );
WP_CLI::log( 'Error: ' . ( $error ?: '(ninguno)' ) );
WP_CLI::log( 'FIN (un solo envío)' );
