<?php
/** Read-only: every prestador record (id, title, status, example/public flags, type, place, photos, price). */
$todos = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'posts_per_page' => 100, 'orderby' => 'ID', 'order' => 'ASC' ] );
WP_CLI::log( count( $todos ) . ' fichas' );
foreach ( $todos as $p ) {
	$f = fn( $k ) => wp_json_encode( get_field( $k, $p->ID ), JSON_UNESCAPED_UNICODE );
	WP_CLI::log( sprintf( '#%d [%s] "%s" destino=%s tipo=%s ejemplo=%s publico=%s consent=%s estado=%s fotos=%s precio=%s fecha=%s capac=%s', $p->ID, $p->post_status, $p->post_title, $f( 'prest_destino' ), $f( 'tipo' ), $f( 'datos_ejemplo' ), $f( 'prest_publico' ), $f( 'prest_consentimiento' ), $f( 'estado' ), $f( 'fotos' ), $f( 'precio' ), $f( 'precio_fecha' ), $f( 'capacidad' ) ) );
}
