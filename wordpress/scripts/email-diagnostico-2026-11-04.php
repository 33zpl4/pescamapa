<?php
/**
 * 4/11/2026, READ ONLY: what the server has for sending and receiving mail, before the first live test.
 * Prints: the inquiry destination option, the admin address, mail-related plugins, how PHP sends (sendmail_path, mail()),
 * DNS of the domain (MX, SPF, DMARC, common DKIM selectors) and the last inquiries (date and type only, never contact data).
 * Writes nothing. Run with wp-script.yml (script = email-diagnostico-2026-11-04).
 */

$ok = fn( $v ) => $v ? '✓' : '✗';
WP_CLI::log( '== Destino de los avisos' );
WP_CLI::log( 'pm_email_consultas: ' . ( get_option( 'pm_email_consultas' ) ?: '(vacío)' ) );
WP_CLI::log( 'admin_email:        ' . get_option( 'admin_email' ) );
WP_CLI::log( 'PM_EMAIL_PUBLICO:   ' . ( defined( 'PM_EMAIL_PUBLICO' ) ? PM_EMAIL_PUBLICO : '(no definido)' ) );

WP_CLI::log( '== Plugins activos con pinta de correo' );
$activos = (array) get_option( 'active_plugins', [] );
$mail    = array_values( array_filter( $activos, fn( $p ) => preg_match( '/mail|smtp|sendgrid|mailgun|postmark|resend|ses|brevo|sendinblue/i', $p ) ) );
WP_CLI::log( $mail ? implode( ', ', $mail ) : '(ninguno)' );
WP_CLI::log( 'Todos los activos: ' . implode( ', ', array_map( fn( $p ) => strtok( $p, '/' ), $activos ) ) );
WP_CLI::log( 'wp_mail reemplazada por un plugin: ' . $ok( ( new ReflectionFunction( 'wp_mail' ) )->getFileName() && false === strpos( ( new ReflectionFunction( 'wp_mail' ) )->getFileName(), 'wp-includes/pluggable.php' ) ) );

WP_CLI::log( '== Cómo envía PHP' );
WP_CLI::log( 'sendmail_path: ' . ( ini_get( 'sendmail_path' ) ?: '(vacío)' ) );
WP_CLI::log( 'SMTP/smtp_port php.ini: ' . ( ini_get( 'SMTP' ) ?: '-' ) . ':' . ( ini_get( 'smtp_port' ) ?: '-' ) );
WP_CLI::log( 'mail() disponible: ' . $ok( function_exists( 'mail' ) && ! in_array( 'mail', array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) ), true ) ) );
$exec_ok = function_exists( 'shell_exec' ) && ! in_array( 'shell_exec', array_map( 'trim', explode( ',', (string) ini_get( 'disable_functions' ) ) ), true );
if ( $exec_ok ) {
	WP_CLI::log( 'sendmail/postfix en el servidor: ' . trim( (string) shell_exec( 'command -v sendmail postfix exim4 msmtp 2>/dev/null | tr "\n" " "' ) ) );
	WP_CLI::log( 'hostname: ' . trim( (string) shell_exec( 'hostname -f 2>/dev/null || hostname' ) ) );
}
$host = wp_parse_url( home_url(), PHP_URL_HOST );
$from = 'wordpress@' . preg_replace( '/^www\./', '', (string) $host );
WP_CLI::log( "Remitente por defecto de wp_mail: $from" );

WP_CLI::log( '== DNS del dominio' );
$dom = preg_replace( '/^www\./', '', (string) $host );
$mx  = dns_get_record( $dom, DNS_MX ) ?: [];
WP_CLI::log( 'MX: ' . ( $mx ? implode( ' | ', array_map( fn( $r ) => $r['pri'] . ' ' . $r['target'], $mx ) ) : '(ninguno)' ) );
$txt = array_map( fn( $r ) => $r['txt'] ?? '', dns_get_record( $dom, DNS_TXT ) ?: [] );
$spf = array_values( array_filter( $txt, fn( $t ) => 0 === strpos( $t, 'v=spf1' ) ) );
WP_CLI::log( 'SPF: ' . ( $spf ? implode( ' | ', $spf ) : '(no hay)' ) );
$dm = array_map( fn( $r ) => $r['txt'] ?? '', dns_get_record( '_dmarc.' . $dom, DNS_TXT ) ?: [] );
WP_CLI::log( 'DMARC: ' . ( $dm ? implode( ' | ', $dm ) : '(no hay)' ) );
foreach ( [ 'default', 'google', 'selector1', 'selector2', 'k1', 'mail', 'cf2024-1', 'resend', 's1', 's2' ] as $sel ) {
	$d = dns_get_record( "{$sel}._domainkey.{$dom}", DNS_TXT ) ?: [];
	$c = dns_get_record( "{$sel}._domainkey.{$dom}", DNS_CNAME ) ?: [];
	if ( $d || $c ) {
		WP_CLI::log( "DKIM {$sel}: encontrado" );
	}
}

WP_CLI::log( '== Últimas consultas guardadas (fecha y tipo, sin datos de contacto)' );
$ids = get_posts( [ 'post_type' => 'pm_consulta', 'post_status' => 'any', 'numberposts' => 8, 'orderby' => 'date', 'order' => 'DESC', 'fields' => 'ids' ] );
foreach ( $ids as $id ) {
	WP_CLI::log( '#' . $id . ' ' . get_post_field( 'post_date', $id ) . ' ' . get_post_meta( $id, 'tipo', true ) . ' ' . get_post_meta( $id, 'estado', true ) );
}
if ( ! $ids ) {
	WP_CLI::log( '(ninguna consulta guardada)' );
}
WP_CLI::log( 'FIN (solo lectura)' );
