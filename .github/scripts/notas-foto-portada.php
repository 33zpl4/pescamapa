<?php
// Notes with a photo come first in the home block; cards without a photo get the brand placeholder (14/11/2026).
// Creates 3 newer notes without photo + 1 older note with a generated photo, asserts the order and the markup, cleans up.
$ver = function ( bool $ok, string $msg ) { if ( ! $ok ) { WP_CLI::error( "✖ notas-foto-portada: $msg" ); } WP_CLI::log( "✓ $msg" ); };
require_once ABSPATH . 'wp-admin/includes/image.php';
$ids = [];
$nota = function ( string $clave, string $fecha, int $att = 0 ) use ( &$ids ) {
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => strtoupper( $clave ), 'post_name' => $clave ] );
	$ids[] = $id;
	foreach ( [ 'idioma' => 'es', 'clave' => $clave, 'nota_fecha' => $fecha ] as $k => $v ) { update_field( $k, $v, $id ); }
	if ( $att ) { set_post_thumbnail( $id, $att ); }
	return $id;
};
// A real image attachment (GD), 4:3, with an author so the credit renders.
$png = wp_upload_bits( 'portada-prueba.png', null, '' );
$im  = imagecreatetruecolor( 400, 300 );
imagefill( $im, 0, 0, imagecolorallocate( $im, 60, 90, 70 ) );
imagepng( $im, $png['file'] );
$att = wp_insert_attachment( [ 'post_mime_type' => 'image/png', 'post_title' => 'portada-prueba', 'post_status' => 'inherit' ], $png['file'] );
wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $png['file'] ) );
update_field( 'foto_autor', 'AUTOR-PORTADA', $att );

$con = $nota( 'portada-con-foto', '20200101', $att );
foreach ( [ 'portada-sin-foto-a', 'portada-sin-foto-b', 'portada-sin-foto-c' ] as $c ) { $nota( $c, '20261114' ); }

$lista = pm_notas_lista( 'es' );
$pos_lista = array_search( 'portada-con-foto', array_column( $lista, 'clave' ), true );
$ver( false !== $pos_lista && $pos_lista >= 3, "la nota con foto es la más vieja (posición $pos_lista en el índice), así que sin la regla no entraría en la portada" );
$por = pm_notas_portada( 'es', 3 );
$ver( 3 === count( $por ), 'la portada trae 3 notas' );
$ver( 'portada-con-foto' === $por[0]['clave'], 'la nota con foto va primera aunque sea la más vieja' );
$ver( pm_nota_tiene_foto( $por[0] ) && ! pm_nota_tiene_foto( $por[1] ), 'solo la primera tiene foto; las otras completan' );
$hay_mas_con_foto = count( array_filter( $lista, 'pm_nota_tiene_foto' ) );
$por_todas = pm_notas_portada( 'es', 50 );
$ver( count( $por_todas ) === count( $lista ), 'con un máximo grande salen todas (completa con las demás)' );
$vistas_con = 0;
foreach ( $por_todas as $i => $n ) { if ( pm_nota_tiene_foto( $n ) ) { $vistas_con++; $ver( $i < $hay_mas_con_foto, "la nota con foto #$i está antes de todas las que no tienen" ); } }

$tarjeta = function ( array $n ) {
	ob_start();
	get_template_part( 'parciales/nota-tarjeta', null, [ 'nota' => $n, 'idioma' => 'es', 'tipo' => true ] );
	return ob_get_clean();
};
$h_con = $tarjeta( $por[0] );
$h_sin = $tarjeta( $por[1] );
$ver( false !== strpos( $h_con, '<figure class="foto' ) && false === strpos( $h_con, 'foto-vacia' ), 'tarjeta con foto: figure, sin placeholder' );
$ver( false !== strpos( $h_con, 'AUTOR-PORTADA' ), 'tarjeta con foto: lleva el crédito del autor' );
$ver( false !== strpos( $h_sin, 'class="foto foto-vacia"' ) && false !== strpos( $h_sin, 'aria-hidden="true"' ) && false !== strpos( $h_sin, '<svg' ), 'tarjeta sin foto: placeholder de marca decorativo con ícono' );
$ver( false === strpos( $h_sin, '<figure' ) && false !== strpos( $h_sin, '<strong>' ) && false !== strpos( $h_sin, 'class="note"' ), 'tarjeta sin foto: título y fecha igual que la otra' );

foreach ( $ids as $id ) { wp_delete_post( $id, true ); }
wp_delete_attachment( $att, true );
WP_CLI::success( 'notas-foto-portada: ok' );
