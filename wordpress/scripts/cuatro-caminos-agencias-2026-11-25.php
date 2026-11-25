<?php
/**
 * Note «Cómo pedir presupuestos de pesca en la Argentina: cuatro caminos» (clave como-pedir-presupuestos-pesca-argentina, es), 25/11/2026.
 * Corrects what the note says about agencies with what their own pages publish (six agencies of Spain, the United States and Brazil,
 * read on 25/11/2026 at the owner's request; none is named): the packages are land packages (lodge, guide, meals, transfers) and the
 * flights go apart; most do not publish the price; some sell only to groups of four; a deposit of 20 to 50 % is asked to book.
 * The note said the agency route came «with flights from your country»: no page read supports it.
 * Exact-string replacements on the stored text; if an old string is missing (and its new one is not there) nothing is written.
 * Dry run by default. Run: wp-script, script = cuatro-caminos-agencias-2026-11-25.
 */
$aplicar = false;

$clave = 'como-pedir-presupuestos-pesca-argentina';
$p     = pm_buscar( 'pm_nota', $clave, 'es' );
$p || WP_CLI::error( "No encuentro la nota $clave (es)." );
$id = $p->ID;

$cuerpo = [
	[ 'tener lodge, traslados y, según la agencia, vuelos en un solo trato.', 'un paquete armado (lodge, guía, comidas y traslados) en un solo trato.' ],
	[ 'la propuesta del paquete, que se pide por consulta.', 'la propuesta del paquete; la mayoría no publica el precio y hay que pedirlo.' ],
	[ 'el precio del paquete; preguntá el mínimo de personas.', 'el precio del paquete, con una seña del 20 al 50 % para reservar. Los vuelos van aparte. Preguntá el mínimo de personas: algunas venden solo a grupos de cuatro.' ],
	[ '<strong>Quiero todo resuelto, con vuelos desde mi país.</strong> Una agencia: un solo trato para el paquete completo. Si salís de España,', '<strong>Quiero un paquete armado por una agencia de mi país.</strong> Una agencia: un solo trato para lodge, guía, comidas y traslados. Los vuelos van aparte. Si salís de España,' ],
];
$datos = [
	[ 'Una agencia que arma el paquete: el que resuelve todo junto, con vuelos y traslados, desde tu país.', 'Una agencia que arma el paquete: lodge, guía, comidas y traslados en un solo trato; los vuelos van aparte.' ],
];
$fuente_nueva = [ 'nombre' => 'Páginas de seis agencias de viajes de pesca de España, Estados Unidos y Brasil (qué incluye el paquete, mínimo de personas, precio publicado, seña; leídas el 25/11/2026)', 'url' => '', 'oficial' => 0, 'consultada' => '20261125' ];

$antes   = (string) get_post_field( 'post_content', $id );
$despues = $antes;
$faltan  = [];
$cambios = 0;
foreach ( $cuerpo as [ $old, $new ] ) {
	$n_old = substr_count( $despues, $old );
	if ( 1 === $n_old ) {
		$despues = str_replace( $old, $new, $despues );
		$cambios++;
		WP_CLI::log( "CUERPO pendiente: «{$old}» → «{$new}»" );
	} elseif ( false !== strpos( $despues, $new ) ) {
		WP_CLI::log( "CUERPO hecho: «{$new}»" );
	} else {
		$faltan[] = "cuerpo: «{$old}» aparece {$n_old} veces";
	}
}
$filas     = pm_filas( 'nota_datos', $id );
$filas_new = $filas;
foreach ( $datos as [ $old, $new ] ) {
	$hit = array_keys( array_filter( $filas, fn( $f ) => $old === trim( (string) ( $f['dato'] ?? '' ) ) ) );
	if ( 1 === count( $hit ) ) {
		$filas_new[ $hit[0] ]['dato'] = $new;
		$cambios++;
		WP_CLI::log( "DATO pendiente: «{$old}» → «{$new}»" );
	} elseif ( array_filter( $filas, fn( $f ) => $new === trim( (string) ( $f['dato'] ?? '' ) ) ) ) {
		WP_CLI::log( "DATO hecho: «{$new}»" );
	} else {
		$faltan[] = "nota_datos: no encuentro «{$old}»";
	}
}
$fuentes     = pm_filas( 'fuentes', $id );
$tiene       = (bool) array_filter( $fuentes, fn( $f ) => $fuente_nueva['nombre'] === (string) ( $f['nombre'] ?? '' ) );
$fuentes_new = $tiene ? $fuentes : array_merge( $fuentes, [ $fuente_nueva ] );
if ( ! $tiene ) {
	$cambios++;
	WP_CLI::log( 'FUENTE pendiente: ' . $fuente_nueva['nombre'] );
}
if ( $faltan ) {
	WP_CLI::error( "No escribo nada. Falta:\n- " . implode( "\n- ", $faltan ) );
}
if ( ! $aplicar ) {
	WP_CLI::success( "Simulacro terminado: $cambios cambios pendientes." );
	return;
}
if ( $despues !== $antes ) {
	$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $despues ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
}
update_field( 'nota_datos', array_values( $filas_new ), $id );
if ( ! $tiene ) {
	add_row( 'fuentes', $fuente_nueva, $id ); // only the new row is written: the existing rows and their dates stay as stored
}
update_post_meta( $id, '_pm_es_editado', time() );
wp_cache_flush();

$g    = (string) get_post_field( 'post_content', $id );
$gd   = implode( ' | ', array_map( fn( $f ) => (string) ( $f['dato'] ?? '' ), pm_filas( 'nota_datos', $id ) ) );
$resp = wp_remote_get( add_query_arg( 'pm_v', time(), get_permalink( $id ) ), [ 'timeout' => 25 ] );
$html = is_wp_error( $resp ) ? '' : wp_strip_all_tags( html_entity_decode( (string) wp_remote_retrieve_body( $resp ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
$ok   = true;
foreach ( $cuerpo as [ $old, $new ] ) {
	$ok = $ok && false === strpos( $g, $old ) && false !== strpos( $g, $new );
}
$ok = $ok && false !== strpos( $gd, $datos[0][1] ) && false === strpos( $gd, 'con vuelos y traslados' )
	&& count( pm_filas( 'fuentes', $id ) ) === count( $fuentes_new )
	&& false !== strpos( $html, 'Los vuelos van aparte' ) && false === strpos( $html, 'con vuelos desde mi país' ) && false === strpos( $html, 'con vuelos y traslados, desde tu país' );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave (es) #$id contenido, datos clave, fuentes y render " . get_permalink( $id ) );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( "Aplicado: $cambios cambios." );
