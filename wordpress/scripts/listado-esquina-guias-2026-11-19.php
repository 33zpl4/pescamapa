<?php
/**
 * Fondo del embudo, Esquina (19/11/2026). Lo confirmó el dueño leyendo la página oficial «Experiencia de Pesca – Esquina» de Turismo de Corrientes:
 * 1) Posada Hambaré figura ahí escrita «Posada Ambare» (el cruce del 19/11 buscaba «hambare» y no la marcó). Se carga la marca de listado en su ficha.
 * 2) El listado nombra dos guías de pesca de Esquina que no teníamos: Carlos Leguizamón y Esteban Masine. Fichas nuevas de «información pública»:
 *    solo que son guías de pesca de Esquina y que figuran en el listado. Sin contacto, sin foto, sin especies ni tarifa (el listado no las da por guía).
 * El resto del listado (posadas, apart, hostería, estancia) no se carga: no dice que ofrezcan pesca.
 * Igual que listado-corrientes-fichas-2026-11-19: el script lee la página, solo comprueba presencia de nombres y NO imprime texto de ella (lleva
 * teléfonos y correos). Marca y fichas solo si el nombre está en la página al correr. Idempotente. Simulacro primero ($aplicar = false).
 * Correr con wp-script.yml (script = listado-esquina-guias-2026-11-19; aplicar=true para el real).
 */

$aplicar = false;
$hoy     = '20261119';
$fuente  = 'Turismo de Corrientes';
$url     = 'https://corrientes.tur.ar/tour/experiencia-de-pesca-esquina/';

function pm_le_norm( string $t ): string {
	$t = mb_strtolower( remove_accents( html_entity_decode( $t, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
	return trim( preg_replace( '/\s+/u', ' ', preg_replace( '/[^a-z0-9]+/u', ' ', $t ) ) );
}
function pm_le_esta( string $texto, string $frase ): bool {
	return '' !== $frase && false !== strpos( ' ' . $texto . ' ', ' ' . pm_le_norm( $frase ) . ' ' );
}
function pm_le_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}

// ---- official page (presence only) -------------------------------------------------------------------------------------------------------
$r = wp_remote_get( $url, [ 'timeout' => 20, 'redirection' => 5, 'limit_response_size' => 2000000, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)' ] );
is_wp_error( $r ) && WP_CLI::error( 'no pude leer la página oficial: ' . $r->get_error_message() );
$html  = (string) wp_remote_retrieve_body( $r );
$html  = preg_replace( '#<(script|style|noscript|svg|template)\b.*?</\1\s*>#is', ' ', preg_replace( '#<!--.*?-->#s', ' ', $html ) );
$texto = pm_le_norm( preg_replace( '#<[^>]*>#', ' ', $html ) );
$st    = (int) wp_remote_retrieve_response_code( $r );
WP_CLI::log( "página oficial Esquina: status $st, " . mb_strlen( $texto ) . ' caracteres de texto (no se imprime)' );
( 200 === $st && mb_strlen( $texto ) >= 300 ) || WP_CLI::error( 'la página oficial no devolvió una lectura útil' );

$marca = [ 'prest_listado_fuente' => $fuente, 'prest_listado_fecha' => $hoy, 'prest_listado_url' => $url ];

// ---- 1) Hambaré ----------------------------------------------------------------------------------------------------------------------------
$hid = pm_le_id( 'Posada Hambaré' );
$hid || WP_CLI::error( 'no encuentro una única ficha «Posada Hambaré»' );
$hesta = pm_le_esta( $texto, 'posada ambare' ) || pm_le_esta( $texto, 'posada hambare' );
$hya   = pm_prestador_listado_datos( $hid );
WP_CLI::log( "«Posada Hambaré» #$hid: " . ( $hesta ? 'figura en el listado (como «Posada Ambare»)' : 'NO se ve en el listado' ) . ( $hya ? " · ya tiene marca ({$hya['fuente']}, {$hya['fecha']})" : ' · sin marca' ) );
$hesta || WP_CLI::error( 'Hambaré no aparece: no se marca nada' );

// ---- 2) the two guides -----------------------------------------------------------------------------------------------------------------------
$comun = [
	'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado',
	'tipo' => [ 'guia' ], 'prest_destino' => 'esquina', 'localidad' => 'Esquina (Corrientes)',
	'descripcion'    => 'Guía de pesca de Esquina. Figura en el listado de pesca de Turismo de Corrientes. Salidas, qué incluyen y a cuánto: a consultar.',
	'descripcion_pt' => 'Guia de pesca de Esquina. Consta da lista de pesca de Turismo de Corrientes. Saídas, o que incluem e quanto custam: a consultar.',
	'descripcion_en' => 'Fishing guide in Esquina. Appears in the Turismo de Corrientes fishing listing. Trips, what they include and the price: to be asked.',
	'precio' => '', 'precio_fecha' => '', 'prest_fuente' => $url, 'verificado' => $hoy,
];
$guias = [
	[ 'nombre' => 'Carlos Leguizamón', 'alias' => 'carlos leguizamon' ],
	[ 'nombre' => 'Esteban Masine', 'alias' => 'esteban masine' ],
];
$prohibido = '/@|https?:\/\/|instagram|facebook|whatsapp|tel[eé]fono|\+\d{2}|\d{6,}|\boperador|\bmejor(es)?\b|r[eé]cord|habilitad|registro/iu';
foreach ( [ 'descripcion', 'descripcion_pt', 'descripcion_en', 'localidad' ] as $k ) {
	preg_match( $prohibido, $comun[ $k ], $m ) && WP_CLI::error( "texto público ($k) lleva algo que no se publica: «{$m[0]}»" );
}
$plan = [];
foreach ( $guias as $g ) {
	$esta = pm_le_esta( $texto, $g['alias'] );
	$pid  = pm_le_id( $g['nombre'] );
	WP_CLI::log( ( $pid ? "EXISTE #$pid" : 'NUEVA' ) . " «{$g['nombre']}»: " . ( $esta ? 'figura en el listado' : 'NO se ve en el listado (no se crea)' ) );
	$esta && $plan[] = [ $g, $pid ];
}

WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': marca en Hambaré' . ( $hya ? ' (ya estaba)' : '' ) . ' + ' . count( $plan ) . ' fichas de guía' );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

// ---- apply ----------------------------------------------------------------------------------------------------------------------------------
if ( ! $hya ) {
	foreach ( $marca as $k => $v ) {
		update_field( $k, $v, $hid );
	}
	WP_CLI::log( "✓ marca de listado en «Posada Hambaré» #$hid" );
}
$ids = [];
foreach ( $plan as [ $g, $pid ] ) {
	if ( ! $pid ) {
		$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $g['nombre'] ], true );
		is_wp_error( $pid ) && WP_CLI::error( $pid->get_error_message() );
		foreach ( $comun + [ 'notas' => 'Listado con información pública: solo figura como guía de pesca en la página «Experiencia de Pesca – Esquina» de Turismo de Corrientes (leída el 19/11/2026; contacto fuera del repo). Pendiente: escribirle.' ] as $k => $v ) {
			'' !== $v && update_field( $k, $v, $pid );
		}
		WP_CLI::log( "✓ ficha «{$g['nombre']}» creada #$pid" );
	} else {
		WP_CLI::log( "= ficha «{$g['nombre']}» #$pid ya existía: no se toca el contenido" );
	}
	if ( ! pm_prestador_listado_datos( $pid ) ) {
		foreach ( $marca as $k => $v ) {
			update_field( $k, $v, $pid );
		}
	}
	$ids[ $g['nombre'] ] = (int) $pid;
}
wp_cache_flush();

// ---- read back ------------------------------------------------------------------------------------------------------------------------------
$bien = true;
foreach ( [ 'Posada Hambaré' => $hid ] + $ids as $nombre => $pid ) {
	$l  = pm_prestador_listado_datos( $pid );
	$ok = $l && $fuente === $l['fuente'] && $url === $l['url'];
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": marca «{$nombre}» #$pid " . ( $l ? $l['fuente'] . ' · ' . $l['fecha'] . ' · ' . $l['url'] : '(sin marca)' ) );
	$bien = $bien && $ok;
}
$en_lista = array_map( fn( $r ) => (int) $r['id'], pm_prestadores_destino( 'esquina' ) );
foreach ( $ids as $nombre => $pid ) {
	$f      = pm_prestador( $pid );
	$pub    = wp_json_encode( [ $f['descripcion'], $f['localidad'], $f['precio'] ], JSON_UNESCAPED_UNICODE );
	$ok     = in_array( $pid, $en_lista, true ) && ! preg_match( $prohibido, $pub ) && '' === $f['precio'] && ! $f['consentimiento'] && ! $f['fotos'];
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» #$pid sale en Esquina como información pública, sin contactos ni precio" );
	$bien = $bien && $ok;
}
WP_CLI::log( $bien ? 'VERIFIED: todo leído de vuelta' : 'FALLÓ: hay campos que no coinciden (ver arriba)' );
