<?php
/**
 * Diagnóstico SOLO LECTURA (19/11/2026): cómo muestra y registra errores PHP el servidor.
 * No escribe nada. No imprime claves de wp-config (solo las constantes de depuración).
 * Aviso: wp-cli corre PHP CLI; los valores de ini_get() son los de la CLI, no los de php-fpm.
 * Por eso también lee (solo las líneas de errores) los php.ini / pools de aaPanel y pide la portada para ver si hay avisos.
 */
$def = function ( $n ) {
	return defined( $n ) ? var_export( constant( $n ), true ) : '(no definida)';
};
echo "== Constantes\n";
echo '  WP_DEBUG: ' . $def( 'WP_DEBUG' ) . "\n";
echo '  WP_DEBUG_DISPLAY: ' . $def( 'WP_DEBUG_DISPLAY' ) . "\n";
echo '  WP_DEBUG_LOG: ' . $def( 'WP_DEBUG_LOG' ) . ( defined( 'WP_DEBUG_LOG' ) && is_string( WP_DEBUG_LOG ) ? '  (es ruta)' : '  (no es ruta)' ) . "\n";
echo "== PHP (CLI)\n";
echo '  PHP_VERSION: ' . PHP_VERSION . '  SAPI: ' . PHP_SAPI . "\n";
echo "  display_errors: " . var_export( ini_get( 'display_errors' ), true ) . "\n";
echo "  log_errors: " . var_export( ini_get( 'log_errors' ), true ) . "\n";
echo "  error_log: " . var_export( ini_get( 'error_log' ), true ) . "\n";
echo '  error_reporting(): ' . error_reporting() . "\n";
echo '  php.ini cargado: ' . var_export( php_ini_loaded_file(), true ) . "\n";
echo '  SERVER_SOFTWARE: ' . ( isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '(no está)' ) . "\n";
echo "== Rutas\n";
echo '  ABSPATH: ' . ABSPATH . "\n";
$sup = dirname( rtrim( ABSPATH, '/' ) );
echo '  dirname(ABSPATH): ' . $sup . '  escribible: ' . var_export( is_writable( $sup ), true ) . "\n";
echo '  WP_CONTENT_DIR: ' . WP_CONTENT_DIR . '  escribible: ' . var_export( is_writable( WP_CONTENT_DIR ), true ) . "\n";
echo '  open_basedir: ' . var_export( ini_get( 'open_basedir' ), true ) . "\n";
echo '  usuario: ' . ( function_exists( 'posix_getpwuid' ) ? posix_getpwuid( posix_geteuid() )['name'] : '?' ) . "\n";
foreach ( [ $sup . '/logs', $sup . '/wwwlogs', '/www/wwwlogs', '/www/server/php' ] as $d ) {
	echo "  {$d}: existe=" . var_export( is_dir( $d ), true ) . ' escribible=' . var_export( is_writable( $d ), true ) . "\n";
}

echo "== Líneas de errores en configuración de PHP/FPM (aaPanel)\n";
$conf = array_merge(
	(array) glob( '/www/server/php/*/etc/php.ini' ),
	(array) glob( '/www/server/php/*/etc/php-fpm.conf' ),
	(array) glob( '/www/server/php/*/etc/php-fpm.d/*.conf' ),
	(array) glob( '/www/server/php/*/etc/php-cli.ini' )
);
if ( ! $conf ) {
	echo "  (no encontré archivos en /www/server/php/*/etc)\n";
}
foreach ( $conf as $f ) {
	if ( ! is_readable( $f ) ) {
		echo "  {$f}: no legible\n";
		continue;
	}
	$out = [];
	foreach ( file( $f ) as $l ) {
		$l = trim( $l );
		if ( '' !== $l && ';' !== $l[0] && '#' !== $l[0] && preg_match( '/(display_errors|log_errors|error_log|error_reporting|html_errors|php_admin_(value|flag)|php_(value|flag))/i', $l ) ) {
			$out[] = $l;
		}
	}
	echo "  {$f}: " . ( $out ? '' : "(sin líneas de errores)\n" );
	if ( $out ) {
		echo "\n";
		foreach ( $out as $l ) {
			echo "      {$l}\n";
		}
	}
}

echo "== debug.log\n";
$log = WP_CONTENT_DIR . '/debug.log';
echo "  {$log}: existe=" . var_export( file_exists( $log ), true );
if ( file_exists( $log ) ) {
	echo ' tamaño=' . filesize( $log ) . ' bytes, modificado=' . gmdate( 'c', filemtime( $log ) );
}
echo "\n";
$r = wp_remote_get( content_url( 'debug.log' ), [ 'timeout' => 15, 'redirection' => 0 ] );
echo '  GET ' . content_url( 'debug.log' ) . ' -> ' . ( is_wp_error( $r ) ? $r->get_error_message() : wp_remote_retrieve_response_code( $r ) ) . "\n";
$cand = array_filter( array_unique( [ ini_get( 'error_log' ), is_string( $def2 = ( defined( 'WP_DEBUG_LOG' ) ? WP_DEBUG_LOG : '' ) ) ? $def2 : '', $log ] ) );
foreach ( $cand as $c ) {
	if ( '1' === $c || ! is_readable( $c ) || ! is_file( $c ) ) {
		continue;
	}
	$sz = filesize( $c );
	echo "== Contenido de {$c} ({$sz} bytes; si pasa de 200 KB, solo el final)\n";
	$txt = $sz > 204800 ? file_get_contents( $c, false, null, $sz - 204800 ) : file_get_contents( $c );
	echo $txt . "\n== fin\n";
}

echo "== Portada (es/en/pt): ¿avisos de PHP en el HTML?\n";
foreach ( [ home_url( '/' ), home_url( '/en/' ), home_url( '/pt/' ) ] as $u ) {
	$h = wp_remote_get( $u, [ 'timeout' => 20 ] );
	$b = is_wp_error( $h ) ? '' : (string) wp_remote_retrieve_body( $h );
	echo "  {$u}: " . ( is_wp_error( $h ) ? $h->get_error_message() : wp_remote_retrieve_response_code( $h ) ) . ' avisos=' . preg_match_all( '#<b>(Warning|Notice|Deprecated|Fatal error|Parse error)</b>:#', $b ) . "\n";
}
echo "\nNo se escribió nada.\n";
