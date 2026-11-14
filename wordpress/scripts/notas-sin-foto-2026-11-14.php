<?php
/**
 * SOLO LECTURA (14/11/2026): lista las notas (pm_nota) publicadas y dice cuáles no tienen foto de portada.
 * La foto de una nota vive en la versión en español (pm_nota() la lee de ahí; en/pt la heredan por clave). Se mira la imagen
 * destacada de la fila es, que el adjunto sea una imagen y tenga archivo, y si pt/en tienen una propia (no se usaría).
 * Marca también qué notas entran hoy en la portada (primeras 3 de cada idioma). No escribe nada.
 */
$estado = function ( int $att ): string {
	if ( ! $att ) {
		return 'SIN FOTO';
	}
	if ( ! wp_attachment_is_image( $att ) ) {
		return "ROTA (adjunto #$att no es imagen)";
	}
	$ruta = get_attached_file( $att );
	if ( ! $ruta || ! is_file( $ruta ) ) {
		return "ROTA (adjunto #$att sin archivo)";
	}
	$m = wp_get_attachment_metadata( $att );
	return sprintf( 'ok #%d %dx%d autor="%s" origen=%s lic="%s"', $att, $m['width'] ?? 0, $m['height'] ?? 0, pm_campo( 'foto_autor', $att ), pm_campo( 'foto_origen', $att ), pm_campo( 'foto_licencia', $att ) );
};
$posts = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true, 'orderby' => 'ID', 'order' => 'ASC' ] );
echo 'notas publicadas (filas): ' . count( $posts ) . "\n== por fila ==\n";
$sin = [];
foreach ( $posts as $p ) {
	$idioma = pm_idioma_de( $p->ID );
	$clave  = pm_clave_de( $p->ID );
	$es     = 'es' === $idioma ? $p : pm_buscar( 'pm_nota', $clave, 'es' );
	$fid    = $es ? $es->ID : $p->ID;
	$att    = (int) get_post_thumbnail_id( $fid );
	$propia = (int) get_post_thumbnail_id( $p->ID );
	$st     = $estado( $att );
	$nota   = ( 'es' !== $idioma && $propia && $propia !== $att ) ? " [propia #$propia distinta de la es]" : ( ( 'es' !== $idioma && $propia ) ? ' [también tiene propia igual]' : '' );
	echo sprintf( "#%d %s [%s] «%s» | tipo=%s destino=%s especie=%s fecha=%s | %s%s\n", $p->ID, $clave, $idioma ?: '-', get_the_title( $p->ID ), pm_campo( 'nota_tipo', $fid ), pm_campo( 'nota_destino', $fid ), pm_campo( 'nota_especie', $fid ), pm_campo( 'nota_fecha', $fid ), $st, $nota );
	if ( 0 !== strpos( $st, 'ok' ) ) {
		$sin[ $clave ][ $idioma ] = $p->ID;
	}
}
echo "\n== portada (primeras 3 de pm_notas_lista) ==\n";
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	foreach ( array_slice( pm_notas_lista( $i ), 0, 3 ) as $n ) {
		echo sprintf( "[%s] %s «%s» foto=%s\n", $i, $n['clave'], $n['titulo'], $n['foto'] ? '#' . $n['foto'] : 'SIN' );
	}
}
echo "\n== lista por idioma (cuántas con y sin foto) ==\n";
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$l = pm_notas_lista( $i );
	echo sprintf( "[%s] total=%d con foto=%d sin foto=%d\n", $i, count( $l ), count( array_filter( $l, fn( $n ) => $n['foto'] ) ), count( array_filter( $l, fn( $n ) => ! $n['foto'] ) ) );
}
echo "\n== RESUMEN: claves sin foto o rota ==\n";
foreach ( $sin as $k => $ids ) {
	echo $k . ' ' . wp_json_encode( $ids ) . "\n";
}
echo 'TOTAL claves sin foto: ' . count( $sin ) . "\nSOLO LECTURA: no se escribió nada\n";
