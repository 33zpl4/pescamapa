<?php
/**
 * Plugin Name: Pescamapa — núcleo
 * Description: Tipos de contenido (destinos, especies, próximos destinos, entrevistas, prestadores, consultas), idiomas, URLs, reglas de publicación, SEO y créditos de fotos de Pescamapa. Versionado en git: no se edita desde el panel.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Requires Plugins: secure-custom-fields
 * Text Domain: pescamapa
 *
 * Data lives in WordPress; behaviour lives here, in git. The theme only
 * renders (wordpress/themes/pescamapa). Field names keep the ones the Astro
 * site used, so the content model reads the same in both.
 */

defined( 'ABSPATH' ) || exit;

const PM_VERSION = '1.0.0';
define( 'PM_DIR', plugin_dir_path( __FILE__ ) );
define( 'PM_URL', plugin_dir_url( __FILE__ ) );

/*
 * Errores de PHP (19/11/2026): nunca a la página, siempre al registro.
 * El servidor tiene display_errors=On (php.ini de aaPanel); un aviso llegó a la portada con rutas internas.
 * Solo peticiones web (no WP-CLI: ahí los errores deben verse en la salida de wp-script y deploy-wp).
 * Registro (20/11/2026): wp-content/pm-logs/errores-php.php. El primer renglón es `<?php exit; ?>`: si alguien pide el archivo por la web,
 * PHP lo ejecuta y sale sin mostrar nada. /www/wwwlogs NO sirve: el pool web de PHP-FPM no lo ve (open_basedir/aislamiento de aaPanel),
 * así que los avisos iban al stderr de FPM y el paso del deploy leía un archivo al que la web nunca escribía.
 * El archivo se crea con esa cabecera si no existe (web o WP-CLI). Si no se puede crear o escribir, NO se calla: la opción
 * 'pm_registro_estado' queda en «falla: motivo» y la leen la autoprueba y leer-vivo. No usa uploads. No se toca wp-config ni el servidor.
 * error_reporting E_ALL: con WP_DEBUG=false WordPress deja afuera notices y deprecations; aquí se registran (sin mostrarse).
 */
if ( ! defined( 'PM_ERROR_LOG' ) ) {
	define( 'PM_ERROR_LOG', WP_CONTENT_DIR . '/pm-logs/errores-php.php' );
}
$pm_registro_ok     = false;
$pm_registro_motivo = '';
$pm_dir_log         = dirname( PM_ERROR_LOG );
if ( ! is_dir( $pm_dir_log ) ) {
	@wp_mkdir_p( $pm_dir_log ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
}
if ( is_dir( $pm_dir_log ) && is_writable( $pm_dir_log ) ) {
	if ( ! file_exists( $pm_dir_log . '/index.php' ) ) {
		@file_put_contents( $pm_dir_log . '/index.php', "<?php\n" ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
	}
	if ( ! file_exists( PM_ERROR_LOG ) ) {
		@file_put_contents( PM_ERROR_LOG, "<?php exit; ?>\n" ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
	}
	if ( ! is_file( PM_ERROR_LOG ) ) {
		$pm_registro_motivo = 'no se pudo crear el archivo del registro';
	} elseif ( ! is_writable( PM_ERROR_LOG ) ) {
		$pm_registro_motivo = 'el archivo del registro existe pero este usuario no puede escribirlo';
	} elseif ( 0 !== strpos( (string) file_get_contents( PM_ERROR_LOG, false, null, 0, 14 ), '<?php exit; ?>' ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
		$pm_registro_motivo = 'el registro no empieza con la cabecera <?php exit; ?> (se leería desde la web)';
	} else {
		$pm_registro_ok = true;
	}
} else {
	$pm_registro_motivo = 'wp-content/pm-logs no existe o no es escribible para este usuario';
}
$pm_registro_estado = $pm_registro_ok ? 'ok' : 'falla: ' . $pm_registro_motivo;
if ( get_option( 'pm_registro_estado' ) !== $pm_registro_estado ) {
	update_option( 'pm_registro_estado', $pm_registro_estado, false ); // solo cuando cambia: no escribe en cada petición
}
if ( 'cli' !== PHP_SAPI && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	@ini_set( 'display_errors', '0' ); // phpcs:ignore WordPress.PHP.IniSet
	@ini_set( 'html_errors', '0' ); // phpcs:ignore WordPress.PHP.IniSet
	@ini_set( 'log_errors', '1' ); // phpcs:ignore WordPress.PHP.IniSet
	error_reporting( E_ALL ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
	// Respeta WP_DEBUG_LOG si alguien lo activó (ya fijó su propio error_log).
	if ( $pm_registro_ok && ! ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) ) {
		@ini_set( 'error_log', PM_ERROR_LOG ); // phpcs:ignore WordPress.PHP.IniSet
	}
}
unset( $pm_dir_log, $pm_registro_ok, $pm_registro_motivo, $pm_registro_estado );

/*
 * Autoprueba de errores de PHP (19/11/2026). Comprueba de punta a punta que un aviso de PHP NO sale en la página y SÍ queda en PM_ERROR_LOG.
 * Solo actúa si la petición web trae ?pm_autoprueba=<token> y el token coincide con el transient 'pm_autoprueba' vigente, que crea el
 * wp-script autoprueba-errores (un solo uso: se borra al usarse; vence a los 5 minutos). Sin token válido no hace nada.
 * Emite un E_USER_WARNING controlado y la página sigue rindiendo normal. Ver docs/wordpress.md, «Errores de PHP».
 */
add_action( 'init', function () {
	if ( ! isset( $_GET['pm_autoprueba'] ) || ! is_string( $_GET['pm_autoprueba'] ) || 'cli' === PHP_SAPI ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	$token  = (string) wp_unslash( $_GET['pm_autoprueba'] ); // phpcs:ignore WordPress.Security.NonceVerification
	$vigente = get_transient( 'pm_autoprueba' );
	if ( '' === $token || ! is_string( $vigente ) || '' === $vigente ) {
		return; // sin autoprueba armada: no hace nada (ni escribe)
	}
	if ( ! hash_equals( $vigente, $token ) ) {
		set_transient( 'pm_autoprueba_motivo', 'token distinto', 300 ); // solo con una autoprueba armada: ayuda a diagnosticar
		return;
	}
	// Diagnóstico (solo con una autoprueba armada): cómo ve PHP-web su configuración de errores y si puede escribir el registro.
	$pm_ini = 'manejador alcanzado; error_log=' . var_export( ini_get( 'error_log' ), true ) . ' log_errors=' . var_export( ini_get( 'log_errors' ), true )
		. ' display_errors=' . var_export( ini_get( 'display_errors' ), true ) . ' error_reporting=' . error_reporting()
		. ' sapi=' . PHP_SAPI . ' usuario=' . ( function_exists( 'posix_geteuid' ) && function_exists( 'posix_getpwuid' ) ? ( posix_getpwuid( posix_geteuid() )['name'] ?? '?' ) : get_current_user() )
		. ' registro_estado=' . var_export( get_option( 'pm_registro_estado' ), true ) . ' dir_escribible=' . var_export( is_writable( dirname( PM_ERROR_LOG ) ), true ) . ' dir_existe=' . var_export( is_dir( dirname( PM_ERROR_LOG ) ), true )
		. ' ini_set_habilitado=' . var_export( function_exists( 'ini_set' ) && false === strpos( (string) ini_get( 'disable_functions' ), 'ini_set' ), true );
	set_transient( 'pm_autoprueba_motivo', $pm_ini, 300 );
	delete_transient( 'pm_autoprueba' ); // de un solo uso
	trigger_error( 'Pescamapa autoprueba controlada', E_USER_WARNING ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
}, 1 );

require PM_DIR . 'inc/i18n.php';
require PM_DIR . 'inc/tipos.php';
require PM_DIR . 'inc/campos.php';
require PM_DIR . 'inc/datos.php';
require PM_DIR . 'inc/temporada.php';
require PM_DIR . 'inc/rutas.php';
require PM_DIR . 'inc/reglas.php';
require PM_DIR . 'inc/fotos.php';
require PM_DIR . 'inc/mapa.php';
require PM_DIR . 'inc/seo.php';
require PM_DIR . 'inc/admin.php';
require PM_DIR . 'inc/editor.php';
require PM_DIR . 'inc/entrevistas.php';
require PM_DIR . 'inc/prestadores.php';
require PM_DIR . 'inc/notas.php';
require PM_DIR . 'inc/consultas.php';
require PM_DIR . 'inc/consulta-campos.php';
require PM_DIR . 'inc/correo.php';
require PM_DIR . 'inc/pique.php';
require PM_DIR . 'inc/legal.php';
require PM_DIR . 'inc/rio.php';
require PM_DIR . 'inc/maquinas.php';

register_activation_hook( __FILE__, function () {
	pm_registrar_tipos();
	pm_registrar_rutas();
	pm_crear_rol_editor();
	flush_rewrite_rules( false );
} );

register_deactivation_hook( __FILE__, function () {
	flush_rewrite_rules( false );
} );
