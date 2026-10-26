<?php
/** Read-only: the free-text fields shown on every provider card (slug, status, and each text), to translate them. */
$todos = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'posts_per_page' => 100, 'orderby' => 'ID', 'order' => 'ASC' ] );
WP_CLI::log( count( $todos ) . ' fichas' );
foreach ( $todos as $p ) {
	WP_CLI::log( "--- #{$p->ID} [{$p->post_status}] {$p->post_name} \"{$p->post_title}\" ejemplo=" . wp_json_encode( (bool) get_field( 'datos_ejemplo', $p->ID ) ) );
	foreach ( [ 'descripcion', 'lancha', 'motor', 'prest_temporada', 'precio', 'precio_fecha' ] as $k ) {
		WP_CLI::log( "  $k = " . wp_json_encode( get_field( $k, $p->ID ), JSON_UNESCAPED_UNICODE ) );
	}
	WP_CLI::log( '  prest_oferta = ' . wp_json_encode( get_post_meta( $p->ID, 'prest_oferta', true ), JSON_UNESCAPED_UNICODE ) );
}
