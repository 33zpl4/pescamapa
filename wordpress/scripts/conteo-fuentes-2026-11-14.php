<?php
/**
 * SOLO LECTURA (14/11/2026): cuenta las fuentes que citan las guías (pm_destino) y las notas (pm_nota) publicadas.
 * Mismo criterio que pm_fuentes_conteo(): únicas = misma URL (sin esquema ni www ni barra final) cuenta una vez, una fila sin
 * URL cuenta por nombre. No escribe nada.
 */
$por_tipo = [];
$todas    = [];
$filas    = 0;
$fecha    = '';
foreach ( [ 'pm_destino', 'pm_nota' ] as $tipo ) {
	$posts = get_posts( [ 'post_type' => $tipo, 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true ] );
	$f_t   = 0;
	$u_t   = [];
	foreach ( $posts as $p ) {
		for ( $i = 0, $n = (int) get_post_meta( $p->ID, 'fuentes', true ); $i < $n; $i++ ) {
			$nombre = trim( (string) get_post_meta( $p->ID, "fuentes_{$i}_nombre", true ) );
			$url    = trim( (string) get_post_meta( $p->ID, "fuentes_{$i}_url", true ) );
			if ( '' === $nombre && '' === $url ) {
				continue;
			}
			++$f_t;
			$k           = '' !== $url ? strtolower( rtrim( preg_replace( '#^https?://(www\.)?#i', '', $url ), '/' ) ) : mb_strtolower( $nombre );
			$u_t[ $k ]   = true;
			$todas[ $k ] = true;
		}
		$fecha = max( $fecha, substr( (string) $p->post_modified, 0, 10 ) );
	}
	$filas += $f_t;
	echo sprintf( "%s: %d publicados, %d filas de fuentes, %d únicas\n", $tipo, count( $posts ), $f_t, count( $u_t ) );
}
echo sprintf( "TOTAL: %d filas, %d fuentes únicas, última modificación %s\n", $filas, count( $todas ), $fecha );
if ( function_exists( 'pm_fuentes_conteo' ) ) {
	echo 'pm_fuentes_conteo(): ' . wp_json_encode( pm_fuentes_conteo() ) . "\n";
}
