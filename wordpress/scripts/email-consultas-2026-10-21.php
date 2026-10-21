<?php
/**
 * 21/10/2026: inquiries go to info@pescamapa.com (Cloudflare Email Routing is
 * set up). Sets the option only when it is empty or still the admin address,
 * so a different destination chosen later is never overwritten.
 * Run with wp-script.yml (script = email-consultas-2026-10-21).
 */

$destino = 'info@pescamapa.com';
$actual  = (string) get_option( 'pm_email_consultas' );

if ( $destino === $actual ) {
	WP_CLI::log( "· pm_email_consultas ya es $destino" );
} elseif ( '' === $actual || $actual === get_option( 'admin_email' ) ) {
	update_option( 'pm_email_consultas', $destino );
	WP_CLI::log( "✓ pm_email_consultas = $destino (antes: " . ( $actual ?: 'vacío' ) . ')' );
} else {
	WP_CLI::warning( "pm_email_consultas es $actual; no lo cambio" );
}
