<?php
/**
 * URLs, same as the Astro site:
 *   [/pt|/en]/<region>/<provincia>/<slug>/   destination guides
 *   /especies/  /pt/especies/  /en/species/   species index
 *   /paquetes-de-pesca/<destino>/  /pt/pacotes-de-pesca/…  /en/fishing-packages/…   packages page (only with published packages)
 *   /guias-de-pesca/<destino>/  /pt/guias-de-pesca/…  /en/fishing-guides/…   city page: every public outfitter of the destination (only with ≥1)
 *   /especies/<clave>/  /pt/especies/…  /en/species/…
 *   /mapa/  /pt/mapa/  /en/map/
 *   /creditos/  /pt/creditos/  /en/credits/  (301 to the home: the page was removed)
 *   /terminos/  /pt/termos/  /en/terms/   and   /privacidad/  /pt/privacidade/  /en/privacy/
 *   /entrevistas/[<slug>/]  /pt/entrevistas/…  /en/interviews/…
 *   /notas/[<slug>/]  /pt/notas/…  /en/notes/…
 *   /pique/  /pt/pique/  /en/bite-zones/   community bite-zone map (inc/pique.php)
 *   /armar-viaje/  /pt/planejar-viagem/  /en/plan-your-trip/   trip request form (inc/consultas.php)
 *   /  /pt/  /en/                            home
 * Views without a post (home, map, credits) are rendered by the theme's
 * vistas/<name>.php.
 */

defined( 'ABSPATH' ) || exit;

function pm_registrar_rutas(): void {
	$regiones = implode( '|', array_keys( PM_REGIONES ) );
	add_rewrite_rule( "^(?:(pt|en)/)?(?:{$regiones})/[a-z0-9-]+/([^/]+)/?$", 'index.php?pm_destino=$matches[2]&pm_lang=$matches[1]', 'top' );

	foreach ( pm_idiomas() as $i ) {
		$pre = 'es' === $i ? '' : "{$i}/";
		$seg = PM_SEGMENTOS[ $i ];
		add_rewrite_rule( "^{$pre}{$seg['especies']}/?$", "index.php?pm_vista=especies&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['especies']}/([^/]+)/?$", "index.php?pm_vista=especie&pm_especie_clave=\$matches[1]&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['mapa']}/?$", "index.php?pm_vista=mapa&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['creditos']}/?$", "index.php?pm_vista=creditos&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['entrevistas']}/?$", "index.php?pm_vista=entrevistas&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['entrevistas']}/([^/]+)/?$", "index.php?pm_entrevista=\$matches[1]&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['pique']}/?$", "index.php?pm_vista=pique&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['viaje']}/?$", "index.php?pm_vista=viaje&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['terminos']}/?$", "index.php?pm_vista=terminos&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['privacidad']}/?$", "index.php?pm_vista=privacidad&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['paquetes']}/([a-z0-9-]+)/?$", "index.php?pm_vista=paquetes&pm_paquete_clave=\$matches[1]&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['guias']}/([a-z0-9-]+)/?$", "index.php?pm_vista=guias&pm_guias_clave=\$matches[1]&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['calendario']}/?$", "index.php?pm_vista=calendario&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['paraguias']}/?$", "index.php?pm_vista=paraguias&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['rio']}/?$", "index.php?pm_vista=rio&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['rio']}/([a-z0-9-]+)/?$", "index.php?pm_vista=rio-punto&pm_rio_clave=\$matches[1]&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['notas']}/?$", "index.php?pm_vista=notas&pm_lang={$i}", 'top' );
		add_rewrite_rule( "^{$pre}{$seg['notas']}/([^/]+)/?$", "index.php?pm_nota=\$matches[1]&pm_lang={$i}", 'top' );
		if ( 'es' !== $i ) {
			add_rewrite_rule( "^{$i}/?$", "index.php?pm_vista=inicio&pm_lang={$i}", 'top' );
		}
	}
}
add_action( 'init', 'pm_registrar_rutas' );

add_filter( 'query_vars', function ( $vars ) {
	return array_merge( $vars, [ 'pm_lang', 'pm_vista', 'pm_especie_clave', 'pm_rio_clave', 'pm_paquete_clave', 'pm_guias_clave' ] );
} );

// Species: resolve clave + language to the post.
add_filter( 'request', function ( $qv ) {
	if ( ( $qv['pm_vista'] ?? '' ) === 'especie' ) {
		$p = pm_buscar( 'pm_especie', (string) ( $qv['pm_especie_clave'] ?? '' ), pm_idioma_valido( $qv['pm_lang'] ?? 'es' ) );
		if ( $p ) {
			return [ 'p' => $p->ID, 'post_type' => 'pm_especie', 'pm_lang' => $qv['pm_lang'], 'pm_vista' => 'especie' ];
		}
		return [ 'error' => '404' ];
	}
	if ( ( $qv['pm_vista'] ?? '' ) === 'paquetes' && ! pm_destino_paquetes( (string) ( $qv['pm_paquete_clave'] ?? '' ), pm_idioma_valido( $qv['pm_lang'] ?? 'es' ) ) ) {
		return [ 'error' => '404' ]; // no published packages in this language: no page
	}
	if ( ( $qv['pm_vista'] ?? '' ) === 'guias' && ! pm_destino_guias( (string) ( $qv['pm_guias_clave'] ?? '' ), pm_idioma_valido( $qv['pm_lang'] ?? 'es' ) ) ) {
		return [ 'error' => '404' ]; // no guide in this language or no public outfitter: no page
	}
	if ( ( $qv['pm_vista'] ?? '' ) === 'rio-punto' && ! isset( pm_rio_config()[ (string) ( $qv['pm_rio_clave'] ?? '' ) ] ) ) {
		return [ 'error' => '404' ];
	}
	return $qv;
} );

// Permalinks.
add_filter( 'post_type_link', function ( $url, $post ) {
	if ( 'pm_destino' === $post->post_type ) {
		$d      = pm_destino_ruta( $post->ID );
		return $d ? pm_url( "{$d['region']}/{$d['provincia']}/{$post->post_name}", pm_idioma_de( $post->ID ) ) : $url;
	}
	if ( 'pm_especie' === $post->post_type ) {
		return pm_url_especie( pm_clave_de( $post->ID ), pm_idioma_de( $post->ID ) );
	}
	if ( 'pm_entrevista' === $post->post_type ) {
		$i = pm_idioma_de( $post->ID );
		return pm_url( PM_SEGMENTOS[ $i ]['entrevistas'] . '/' . $post->post_name, $i );
	}
	if ( 'pm_nota' === $post->post_type ) {
		$i = pm_idioma_de( $post->ID );
		return pm_url( PM_SEGMENTOS[ $i ]['notas'] . '/' . $post->post_name, $i );
	}
	return $url;
}, 10, 2 );

/** Region and province of a destination, from its Spanish version. */
function pm_destino_ruta( int $post_id ): ?array {
	$fuente = 'es' === pm_idioma_de( $post_id ) ? $post_id : ( pm_buscar( 'pm_destino', pm_clave_de( $post_id ), 'es' )->ID ?? 0 );
	$region = $fuente ? (string) get_post_meta( $fuente, 'region', true ) : '';
	$prov   = $fuente ? (string) get_post_meta( $fuente, 'provincia', true ) : '';
	return ( $region && $prov ) ? [ 'region' => $region, 'provincia' => $prov ] : null;
}

// Decide the language and the view, and keep URLs canonical.
add_action( 'template_redirect', function () {
	// The photo-credits page is gone (each photo carries its own credit); the old URL goes home.
	if ( 'creditos' === get_query_var( 'pm_vista' ) ) {
		wp_safe_redirect( pm_url_inicio( get_query_var( 'pm_lang' ) ?: 'es' ), 301 );
		exit;
	}
	if ( is_singular( PM_TIPOS_IDIOMA ) ) {
		$id = get_queried_object_id();
		pm_lang( pm_idioma_de( $id ) );
		// A guide reached through the wrong language prefix or region goes to its real URL.
		$real = get_permalink( $id );
		$pedida = home_url( add_query_arg( [] ) );
		if ( wp_parse_url( $real, PHP_URL_PATH ) !== wp_parse_url( $pedida, PHP_URL_PATH ) ) {
			wp_safe_redirect( $real, 301 );
			exit;
		}
		return;
	}
	pm_lang( get_query_var( 'pm_lang' ) ?: 'es' );
}, 1 );

add_filter( 'redirect_canonical', function ( $redirect ) {
	return ( get_query_var( 'pm_vista' ) || is_singular( PM_TIPOS_IDIOMA ) ) ? false : $redirect;
} );

/** Name of the custom view for this request, or ''. */
function pm_vista(): string {
	if ( is_404() ) {
		return '';
	}
	if ( is_singular( 'pm_destino' ) ) {
		return 'destino';
	}
	if ( is_singular( 'pm_especie' ) ) {
		return 'especie';
	}
	if ( is_singular( 'pm_entrevista' ) ) {
		return 'entrevista';
	}
	if ( is_singular( 'pm_nota' ) ) {
		return 'nota';
	}
	$v = (string) get_query_var( 'pm_vista' );
	if ( $v ) {
		return $v;
	}
	return is_front_page() || is_home() ? 'inicio' : '';
}

add_filter( 'template_include', function ( $plantilla ) {
	$vista = pm_vista();
	if ( $vista ) {
		$archivo = locate_template( "vistas/{$vista}.php" );
		if ( $archivo ) {
			return $archivo;
		}
	}
	return $plantilla;
} );

// The virtual views are not "not found" just because no post matched.
add_filter( 'pre_handle_404', function ( $corto, $query ) {
	if ( in_array( $query->get( 'pm_vista' ), [ 'inicio', 'mapa', 'creditos', 'entrevistas', 'notas', 'especies', 'pique', 'viaje', 'terminos', 'privacidad', 'rio', 'rio-punto', 'paquetes', 'guias', 'calendario', 'paraguias' ], true ) ) {
		status_header( 200 );
		return true;
	}
	return $corto;
}, 10, 2 );
