<?php
/**
 * Autoprueba de errores de PHP (19/11/2026). Solo lectura salvo un transient temporal ('pm_autoprueba', 5 min, de un solo uso).
 * Comprueba que un aviso de PHP provocado a propósito (pescamapa-core.php, manejador de ?pm_autoprueba=<token>):
 *   1) NO aparece en el HTML de la portada (ni '<b>Warning</b>' ni 'autoprueba controlada'),
 *   2) SÍ queda como línea nueva en el registro PM_ERROR_LOG,
 *   3) esa línea la detectaría el paso «Check the PHP error log» de deploy-wp.yml (misma expresión).
 * Imprime OK/FALLA y la línea encontrada sin rutas del servidor. Borra el transient al terminar.
 * Cuándo correrla: una vez al mes y tras cambiar el servidor (php.ini, aaPanel, versión de PHP). Ver docs/wordpress.md.
 * Run with wp-script.yml (script = autoprueba-errores). No necesita `aplicar`.
 */
$log = defined( 'PM_ERROR_LOG' ) ? PM_ERROR_LOG : '';
$fallas = [];
$marca  = 'Pescamapa autoprueba controlada';

$token = wp_generate_password( 32, false, false );
set_transient( 'pm_autoprueba', $token, 300 );

$offset = ( '' !== $log && is_file( $log ) ) ? (int) filesize( $log ) : 0;
$url    = add_query_arg( [ 'pm_autoprueba' => $token, 'pm_t' => time() ], home_url( '/' ) );
$r      = wp_remote_get( $url, [ 'timeout' => 20, 'redirection' => 3, 'user-agent' => 'Pescamapa-autoprueba/1.0', 'sslverify' => true ] );

if ( is_wp_error( $r ) ) {
	$fallas[] = 'no se pudo pedir la portada: ' . $r->get_error_message();
	$html     = '';
} else {
	$estado = (int) wp_remote_retrieve_response_code( $r );
	$html   = (string) wp_remote_retrieve_body( $r );
	echo "Portada: status {$estado}, " . strlen( $html ) . " bytes\n";
	if ( 200 !== $estado ) {
		$fallas[] = "la portada respondió {$estado}";
	}
	if ( false !== strpos( $html, '<b>Warning</b>' ) ) {
		$fallas[] = 'el HTML muestra <b>Warning</b> (PHP está mostrando errores en la página)';
	}
	if ( false !== stripos( $html, 'autoprueba controlada' ) ) {
		$fallas[] = 'el HTML contiene «autoprueba controlada» (el aviso se imprimió en la página)';
	}
}

// Diagnóstico temporal: ¿el manejador corre en otras rutas (distingue caché de origen de un fallo del manejador)?
$cab_nombres = ! is_wp_error( $r ) ? implode( ',', array_keys( (array) wp_remote_retrieve_headers( $r )->getAll() ) ) : '';
echo "Nombres de cabeceras de la portada: {$cab_nombres}\n";
foreach ( [ '/wp-json/', '/llms.txt', '/?pm_x=' . time(), '/wp-login.php' ] as $ruta ) {
	$t2 = wp_generate_password( 32, false, false );
	set_transient( 'pm_autoprueba', $t2, 300 );
	$r2 = wp_remote_get( add_query_arg( [ 'pm_autoprueba' => $t2 ], home_url( $ruta ) ), [ 'timeout' => 20, 'redirection' => 0, 'headers' => [ 'Cache-Control' => 'no-cache', 'Pragma' => 'no-cache' ], 'user-agent' => 'Pescamapa-autoprueba/1.0' ] );
	$c2 = is_wp_error( $r2 ) ? 'error: ' . $r2->get_error_message() : (string) wp_remote_retrieve_response_code( $r2 );
	echo "  ruta {$ruta}: status {$c2}, token consumido=" . ( false === get_transient( 'pm_autoprueba' ) ? 'sí' : 'no' ) . "\n";
	delete_transient( 'pm_autoprueba' );
}
// Diagnóstico (sin rutas de otros sitios): qué pudo impedir que el manejador corriera o que el registro se escribiera.
if ( ! is_wp_error( $r ) ) {
	$cab = [];
	foreach ( [ 'server', 'cf-cache-status', 'age', 'x-cache', 'x-fastcgi-cache', 'cache-control', 'x-powered-by' ] as $h ) {
		$v = wp_remote_retrieve_header( $r, $h );
		if ( '' !== $v && ! is_array( $v ) ) {
			$cab[] = "$h: $v";
		}
	}
	echo 'Cabeceras: ' . ( $cab ? implode( ' | ', $cab ) : '(ninguna de interés)' ) . "\n";
}
$dir_log = '' !== $log ? dirname( $log ) : '';
echo 'Diagnóstico: caché de objetos externa=' . ( wp_using_ext_object_cache() ? 'sí' : 'no' )
	. '; directorio del registro existe=' . ( $dir_log && is_dir( $dir_log ) ? 'sí' : 'no' )
	. ', escribible=' . ( $dir_log && is_writable( $dir_log ) ? 'sí' : 'no' )
	. '; registro existe=' . ( $log && file_exists( $log ) ? 'sí' : 'no' )
	. '; el plugin desplegado trae el manejador=' . ( false !== strpos( (string) file_get_contents( PM_DIR . 'pescamapa-core.php' ), 'pm_autoprueba' ) ? 'sí' : 'no' )
	. '; portada trae el comentario de caché=' . ( preg_match( '#<!--[^>]*(cache|caché)[^>]*-->#i', $html, $mc ) ? mb_substr( $mc[0], 0, 120 ) : 'no' ) . "\n";

// El transient se usa y se borra solo en el manejador; si sigue ahí, el manejador no corrió.
$sigue = get_transient( 'pm_autoprueba' );
delete_transient( 'pm_autoprueba' );
if ( false !== $sigue ) {
	$fallas[] = 'el manejador no consumió el token (¿el plugin desplegado no tiene la autoprueba?)';
}

// El registro: línea nueva con la marca. Se espera un instante a que PHP-FPM la escriba.
$linea = '';
if ( '' === $log || ! is_file( $log ) || ! is_readable( $log ) ) {
	$fallas[] = 'el registro no existe o no es legible' . ( '' === $log ? ' (PM_ERROR_LOG sin definir)' : '' );
} else {
	for ( $i = 0; $i < 5 && '' === $linea; $i++ ) {
		clearstatcache( true, $log );
		$tam = (int) filesize( $log );
		if ( $tam < $offset ) {
			$offset = 0;
		}
		$nuevo = (string) file_get_contents( $log, false, null, $offset );
		foreach ( preg_split( '/\R/', $nuevo ) as $l ) {
			if ( false !== strpos( $l, $marca ) ) {
				$linea = $l;
				break;
			}
		}
		if ( '' === $linea ) {
			sleep( 1 );
		}
	}
	if ( '' === $linea ) {
		$fallas[] = 'el registro no recibió ninguna línea nueva con «' . $marca . '»';
	} else {
		// Misma expresión que deploy-wp.yml, paso «Check the PHP error log».
		$err   = '#PHP (Warning|Notice|Deprecated|Fatal error|Parse error)#';
		$suyos = '#wp-content/(themes/pescamapa|plugins/pescamapa-core)/#';
		if ( ! preg_match( $err, $linea ) || ! preg_match( $suyos, $linea ) ) {
			$fallas[] = 'la línea del registro NO la detectaría el paso del deploy (no cumple «PHP Warning» + ruta del tema o del plugin)';
		} else {
			echo "El paso del deploy la detectaría (PHP Warning + ruta de pescamapa-core).\n";
		}
		echo 'Línea del registro: ' . mb_substr( preg_replace( '#/[^\s]*?(wp-content/)#', '$1', $linea ), 0, 300 ) . "\n";
	}
}

if ( $fallas ) {
	foreach ( $fallas as $f ) {
		echo "FALLA: {$f}\n";
	}
	WP_CLI::error( 'Autoprueba de errores de PHP: FALLA' );
}
echo "OK: el aviso quedó en el registro y no salió en la página.\n";
WP_CLI::success( 'Autoprueba de errores de PHP: OK' );
