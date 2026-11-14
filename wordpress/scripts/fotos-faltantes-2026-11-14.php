<?php
/**
 * SOLO LECTURA (14/11/2026): lista las guías (pm_destino) y los próximos (pm_proximo) publicados y dice cuáles no tienen foto.
 * La foto de una guía vive solo en la versión en español (pt/en la heredan por clave, inc/datos.php): se mira la imagen
 * destacada de la fila es, y se comprueba que el adjunto sea una imagen y tenga archivo. Muestra autor/licencia de las que tienen.
 * No escribe nada.
 */
$uploads = wp_get_upload_dir();
$estado  = function ( int $att ): string {
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
$sin = [];
foreach ( [ 'pm_destino', 'pm_proximo' ] as $tipo ) {
	echo "== $tipo ==\n";
	$posts = get_posts( [ 'post_type' => $tipo, 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true, 'orderby' => 'ID', 'order' => 'ASC' ] );
	foreach ( $posts as $p ) {
		$idioma = pm_idioma_de( $p->ID );
		$clave  = pm_clave_de( $p->ID );
		$es     = 'es' === $idioma || 'pm_proximo' === $tipo ? $p : pm_buscar( $tipo, $clave, 'es' );
		$fid    = $es ? $es->ID : $p->ID;
		$att    = (int) get_post_thumbnail_id( $fid );
		$propia = (int) get_post_thumbnail_id( $p->ID );
		$st     = $estado( $att );
		$nota   = ( 'es' !== $idioma && $propia && $propia !== $att ) ? " [propia #$propia distinta de la es]" : '';
		echo sprintf( "#%d %s [%s] %s | %s | lat=%s lon=%s prov=%s | %s%s\n", $p->ID, $clave, $idioma ?: '-', get_the_title( $p->ID ), $tipo, pm_campo( 'lat', $fid ), pm_campo( 'lon', $fid ), pm_campo( 'provincia', $fid ), $st, $nota );
		if ( 0 !== strpos( $st, 'ok' ) ) {
			$sin[ "$tipo:$clave" ] = ( $sin[ "$tipo:$clave" ] ?? [] ) + [ $idioma => $p->ID ];
		}
	}
}
echo "\n== RESUMEN: sin foto o rota ==\n";
foreach ( $sin as $k => $ids ) {
	echo $k . ' ' . wp_json_encode( $ids ) . "\n";
}
echo 'TOTAL claves sin foto: ' . count( $sin ) . "\nSOLO LECTURA: no se escribió nada\n";
