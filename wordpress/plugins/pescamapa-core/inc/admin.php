<?php
/**
 * wp-admin for non-technical editors: language/state/key columns, a filter
 * by language, and a simpler menu.
 */

defined( 'ABSPATH' ) || exit;

foreach ( [ 'pm_destino', 'pm_especie' ] as $tipo ) {
	add_filter( "manage_{$tipo}_posts_columns", function ( $cols ) {
		$nuevas = [];
		foreach ( $cols as $k => $v ) {
			$nuevas[ $k ] = $v;
			if ( 'title' === $k ) {
				$nuevas['pm_idioma'] = 'Idioma';
				$nuevas['pm_estado'] = 'Completa';
				$nuevas['pm_clave']  = 'Clave';
			}
		}
		return $nuevas;
	} );
	add_action( "manage_{$tipo}_posts_custom_column", function ( $col, $id ) {
		if ( 'pm_idioma' === $col ) {
			echo esc_html( strtoupper( pm_idioma_de( $id ) ) );
		} elseif ( 'pm_estado' === $col ) {
			$n = count( pm_sugerencias( $id ) );
			echo $n ? esc_html( "Faltan {$n} cosas" ) : '✅';
		} elseif ( 'pm_clave' === $col ) {
			echo '<code>' . esc_html( pm_clave_de( $id ) ) . '</code>';
		}
	}, 10, 2 );
}

add_filter( 'manage_pm_proximo_posts_columns', function ( $cols ) {
	return [ 'cb' => $cols['cb'], 'title' => 'Nombre', 'pm_foto' => 'Foto', 'pm_orden' => 'Orden', 'date' => $cols['date'] ];
} );
add_action( 'manage_pm_proximo_posts_custom_column', function ( $col, $id ) {
	if ( 'pm_orden' === $col ) {
		echo (int) ( pm_campo( 'orden', $id ) ?: 99 );
	} elseif ( 'pm_foto' === $col ) {
		echo get_the_post_thumbnail( $id, [ 60, 40 ] );
	}
}, 10, 2 );

// Filter lists by language.
add_action( 'restrict_manage_posts', function ( $tipo ) {
	if ( ! in_array( $tipo, [ 'pm_destino', 'pm_especie' ], true ) ) {
		return;
	}
	$actual = $_GET['pm_idioma'] ?? '';
	echo '<select name="pm_idioma"><option value="">Todos los idiomas</option>';
	foreach ( [ 'es' => 'Español', 'pt' => 'Português', 'en' => 'English' ] as $v => $l ) {
		echo '<option value="' . esc_attr( $v ) . '"' . selected( $actual, $v, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select>';
} );
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() && $q->is_main_query() && ! empty( $_GET['pm_idioma'] ) ) {
		$q->set( 'meta_query', [ [ 'key' => 'idioma', 'value' => sanitize_key( $_GET['pm_idioma'] ) ] ] );
	}
} );

// Editors don't need blog posts, comments or tools.
add_action( 'admin_menu', function () {
	if ( current_user_can( 'manage_options' ) ) {
		return;
	}
	remove_menu_page( 'edit.php' );
	remove_menu_page( 'edit-comments.php' );
	remove_menu_page( 'tools.php' );
}, 99 );

// The block editor's featured image box, renamed for what it is here.
add_filter( 'admin_post_thumbnail_html', function ( $html, $post_id ) {
	$tipo = get_post_type( $post_id );
	if ( 'pm_destino' === $tipo && 'es' !== pm_idioma_de( $post_id ) ) {
		return '<p>La foto se carga solo en la versión en español y vale para los tres idiomas.</p>' . $html;
	}
	return $html;
}, 10, 2 );
