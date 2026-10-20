<?php
/**
 * wp-admin for the content editor (role pescamapa_editor): Spanish only,
 * few menus, few fields, fixed templates.
 *
 * - Editors see and edit only the Spanish fichas. Portuguese and English are
 *   ours: when a Spanish ficha changes, its translations are flagged as
 *   pending (column "Traducciones" for admins) and we propagate them with
 *   wordpress/scripts (wp-script.yml).
 * - Language and key fields are hidden: new fichas are Spanish and their key
 *   comes from the URL slug.
 * - The body accepts only simple blocks (paragraph, heading, list, quote,
 *   image): layout stays in the theme.
 * - The admin menu shows only what she uses; the dashboard opens the guides.
 *
 * Administrators keep the full interface.
 */

defined( 'ABSPATH' ) || exit;

function pm_es_editor(): bool {
	return is_user_logged_in() && ! current_user_can( 'manage_options' );
}

// --- Spanish only ------------------------------------------------------------

add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() || ! pm_es_editor() ) {
		return;
	}
	if ( in_array( $q->get( 'post_type' ), PM_TIPOS_IDIOMA, true ) ) {
		$q->set( 'meta_query', [
			'relation' => 'OR',
			[ 'key' => 'idioma', 'value' => 'es' ],
			[ 'key' => 'idioma', 'compare' => 'NOT EXISTS' ],
		] );
	}
}, 20 );

// Editors can't open a translation, even by URL.
add_filter( 'map_meta_cap', function ( $caps, $cap, $user_id, $args ) {
	if ( in_array( $cap, [ 'edit_post', 'delete_post' ], true ) && ! empty( $args[0] ) && ! user_can( $user_id, 'manage_options' ) ) {
		$p = get_post( (int) $args[0] );
		if ( $p && in_array( $p->post_type, PM_TIPOS_IDIOMA, true ) && 'es' !== pm_idioma_de( $p->ID ) ) {
			return [ 'do_not_allow' ];
		}
	}
	return $caps;
}, 10, 4 );

// Fields editors don't need: language, key, translated names.
const PM_CAMPOS_SOLO_ADMIN = [ 'idioma', 'clave', 'nombre_pt', 'nombre_en', 'agua_pt', 'agua_en' ];

add_filter( 'acf/prepare_field', function ( $field ) {
	if ( ! pm_es_editor() ) {
		return $field;
	}
	if ( in_array( $field['_name'] ?? $field['name'] ?? '', PM_CAMPOS_SOLO_ADMIN, true ) ) {
		return false;
	}
	// Conditions on the (now hidden) language field would hide the shared tab.
	if ( ! empty( $field['conditional_logic'] ) && str_contains( wp_json_encode( $field['conditional_logic'] ), '_idioma' ) ) {
		$field['conditional_logic'] = 0;
	}
	return $field;
} );

// New fichas: Spanish, key from the slug; and remember when Spanish changed.
add_action( 'acf/save_post', function ( $post_id ) {
	if ( ! is_numeric( $post_id ) ) {
		return;
	}
	$post_id = (int) $post_id;
	$tipo    = get_post_type( $post_id );
	if ( ! in_array( $tipo, PM_TIPOS_IDIOMA, true ) ) {
		return;
	}
	if ( '' === (string) get_post_meta( $post_id, 'idioma', true ) ) {
		update_field( 'idioma', 'es', $post_id );
	}
	if ( '' === pm_clave_de( $post_id ) ) {
		$slug = get_post_field( 'post_name', $post_id ) ?: sanitize_title( get_the_title( $post_id ) );
		update_field( 'clave', $slug, $post_id );
	}
	if ( 'es' === pm_idioma_de( $post_id ) ) {
		update_post_meta( $post_id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $post_id, '_pm_traducido', time() );
	}
}, 30 );

// --- Translations pending (for us) --------------------------------------------

/** Languages whose version is missing or older than the Spanish edit. */
function pm_traducciones_pendientes( int $es_id ): array {
	$tipo    = get_post_type( $es_id );
	$clave   = pm_clave_de( $es_id );
	$editado = (int) get_post_meta( $es_id, '_pm_es_editado', true );
	$out     = [];
	foreach ( [ 'pt', 'en' ] as $i ) {
		$t = pm_buscar( $tipo, $clave, $i );
		if ( ! $t ) {
			$out[ $i ] = 'falta';
		} elseif ( $editado && (int) get_post_meta( $t->ID, '_pm_traducido', true ) < $editado ) {
			$out[ $i ] = 'desactualizada';
		}
	}
	return $out;
}

foreach ( PM_TIPOS_IDIOMA as $tipo ) {
	add_filter( "manage_{$tipo}_posts_columns", function ( $cols ) {
		if ( current_user_can( 'manage_options' ) ) {
			$cols['pm_trad'] = 'Traducciones';
		}
		return $cols;
	}, 20 );
	add_action( "manage_{$tipo}_posts_custom_column", function ( $col, $id ) {
		if ( 'pm_trad' !== $col || 'es' !== pm_idioma_de( $id ) ) {
			return;
		}
		$p = pm_traducciones_pendientes( $id );
		echo $p ? esc_html( '🔄 ' . implode( ', ', array_map( fn( $i, $e ) => strtoupper( $i ) . " ($e)", array_keys( $p ), $p ) ) ) : '✅';
	}, 10, 2 );
}

// --- Simple body: only basic blocks ---------------------------------------------

add_filter( 'allowed_block_types_all', function ( $permitidos, $contexto ) {
	if ( pm_es_editor() && in_array( $contexto->post->post_type ?? '', PM_TIPOS_IDIOMA, true ) ) {
		return [ 'core/paragraph', 'core/heading', 'core/list', 'core/list-item', 'core/quote', 'core/image' ];
	}
	return $permitidos;
}, 10, 2 );

// --- Short menu -----------------------------------------------------------------

add_action( 'admin_menu', function () {
	if ( ! pm_es_editor() ) {
		return;
	}
	foreach ( [ 'index.php', 'edit.php', 'edit-comments.php', 'tools.php' ] as $m ) {
		remove_menu_page( $m );
	}
	global $menu;
	foreach ( $menu as &$item ) {
		if ( 'upload.php' === ( $item[2] ?? '' ) ) {
			$item[0] = 'Fotos';
		}
	}
}, 99 );

// The dashboard is noise for her: open the guides instead.
add_action( 'load-index.php', function () {
	if ( pm_es_editor() ) {
		wp_safe_redirect( admin_url( 'edit.php?post_type=pm_destino' ) );
		exit;
	}
} );
add_filter( 'login_redirect', function ( $to, $pedido, $user ) {
	return ( $user instanceof WP_User && ! $user->has_cap( 'manage_options' ) ) ? admin_url( 'edit.php?post_type=pm_destino' ) : $to;
}, 10, 3 );

// Fewer distractions in the top bar.
add_action( 'admin_bar_menu', function ( $bar ) {
	if ( pm_es_editor() ) {
		foreach ( [ 'wp-logo', 'comments', 'new-content', 'updates' ] as $n ) {
			$bar->remove_node( $n );
		}
	}
}, 99 );
