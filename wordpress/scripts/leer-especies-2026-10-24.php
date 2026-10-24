<?php
/** Read-only: print each species record (id, language, key, title, text length, fields) and the start of its text. */
$esp = get_posts( [ 'post_type' => 'pm_especie', 'post_status' => 'any', 'posts_per_page' => 50, 'orderby' => 'ID', 'order' => 'ASC' ] );
WP_CLI::log( count( $esp ) . ' fichas de especie' );
foreach ( $esp as $p ) {
	$meta = [];
	foreach ( get_post_meta( $p->ID ) as $k => $v ) {
		if ( 0 !== strpos( $k, '_' ) ) {
			$meta[ $k ] = wp_trim_words( (string) maybe_unserialize( $v[0] ) ?: '', 14 );
		}
	}
	$txt = trim( wp_strip_all_tags( $p->post_content ) );
	WP_CLI::log( sprintf( '#%d [%s] "%s" idioma=%s clave=%s texto=%d car.', $p->ID, $p->post_status, $p->post_title, get_field( 'idioma', $p->ID ), get_field( 'clave', $p->ID ), mb_strlen( $txt ) ) );
	WP_CLI::log( '   campos: ' . wp_json_encode( $meta, JSON_UNESCAPED_UNICODE ) );
	WP_CLI::log( '   texto: ' . mb_substr( preg_replace( '/\s+/', ' ', $txt ), 0, 700 ) );
}
