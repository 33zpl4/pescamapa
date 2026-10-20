<?php
/**
 * <head> and sitemaps without an SEO plugin: the data model already has the
 * title, description, languages and draft state, so we print them directly.
 *   - title, description, canonical, hreflang (es, pt-BR, en, x-default → es)
 *   - Open Graph
 *   - noindex only on the 404 page; every published ficha is indexable
 *   - JSON-LD: WebSite (home), TouristDestination + BreadcrumbList + FAQPage,
 *     Article + BreadcrumbList (interviews)
 *   - core sitemap: guides, species and the fixed views
 */

defined( 'ABSPATH' ) || exit;

remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'feed_links_extra', 3 );
add_filter( 'xmlrpc_enabled', '__return_false' );

/** What the current request is about: title, description, alternates, draft, JSON-LD. */
function pm_seo(): array {
	static $s = null;
	if ( null !== $s ) {
		return $s;
	}
	$vista = pm_vista();
	$s = [ 'title' => get_bloginfo( 'name' ), 'description' => pm_t( 'marca.lema' ), 'alternas' => [], 'noindex' => false, 'jsonld' => [] ];

	if ( 'inicio' === $vista ) {
		$s['title']       = pm_t( 'inicio.title' );
		$s['description'] = pm_t( 'inicio.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_inicio' );
		$s['jsonld'][]    = [
			'@context'    => 'https://schema.org',
			'@type'       => 'WebSite',
			'name'        => 'Pescamapa',
			'url'         => pm_url_inicio( pm_lang() ),
			'inLanguage'  => pm_meta_idioma()['html'],
			'description' => pm_t( 'marca.lema' ),
			'creator'     => [ '@type' => 'Person', 'name' => 'Cheyenne Justiniano' ],
		];
	} elseif ( 'mapa' === $vista ) {
		$s['title']       = pm_t( 'mapa.title' );
		$s['description'] = pm_t( 'mapa.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_mapa' );
	} elseif ( 'creditos' === $vista ) {
		$s['title']       = pm_t( 'creditos.title' );
		$s['description'] = pm_t( 'creditos.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_creditos' );
	} elseif ( 'destino' === $vista ) {
		$d = pm_destino( get_queried_object_id() );
		$s['title']       = $d['seo_title'] ?: $d['nombre'];
		$s['description'] = $d['seo_description'];
		$s['alternas']    = pm_alternas_destino( $d['clave'] );
		$s['jsonld']      = pm_jsonld_destino( $d );
		if ( $d['foto'] ) {
			$s['imagen'] = wp_get_attachment_image_url( $d['foto'], 'large' );
		}
	} elseif ( 'especie' === $vista ) {
		$e = pm_especie( get_queried_object_id() );
		$s['title']       = $e['seo_title'] ?: $e['nombre'];
		$s['description'] = $e['seo_description'];
		$s['alternas']    = pm_alternas_especie( $e['clave'] );
	} elseif ( 'entrevistas' === $vista ) {
		$s['title']       = pm_t( 'entrevistas.title' );
		$s['description'] = pm_t( 'entrevistas.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_entrevistas' );
	} elseif ( 'entrevista' === $vista ) {
		$e = pm_entrevista( get_queried_object_id() );
		$s['title']       = $e['seo_title'] ?: $e['titulo'];
		$s['description'] = $e['seo_description'] ?: $e['bajada'];
		$s['alternas']    = pm_alternas_entrevista( $e['clave'] );
		$s['jsonld']      = pm_jsonld_entrevista( $e );
		if ( $e['foto'] ) {
			$s['imagen'] = wp_get_attachment_image_url( $e['foto'], 'large' );
		}
	} elseif ( is_404() ) {
		$s['title']    = 'Página no encontrada | Pescamapa';
		$s['noindex'] = true;
	}
	return $s;
}

function pm_jsonld_destino( array $d ): array {
	$provincia = PM_PROVINCIAS[ $d['provincia'] ] ?? $d['provincia'];
	$region    = PM_REGIONES[ $d['region'] ][ $d['idioma'] ] ?? $d['region'];
	$migas     = [
		[ 'name' => pm_t( 'ficha.inicio' ), 'item' => pm_url_inicio( $d['idioma'] ) ],
		[ 'name' => $region ],
		[ 'name' => $provincia ],
		[ 'name' => $d['nombre'], 'item' => $d['url'] ],
	];
	$out = [
		[
			'@context'         => 'https://schema.org',
			'@type'            => 'TouristDestination',
			'name'             => "{$d['nombre']}, {$provincia}",
			'description'      => $d['seo_description'],
			'url'              => $d['url'],
			'geo'              => [ '@type' => 'GeoCoordinates', 'latitude' => $d['coordenadas'][0], 'longitude' => $d['coordenadas'][1] ],
			'containedInPlace' => [ '@type' => 'AdministrativeArea', 'name' => "{$provincia}, Argentina" ],
			'touristType'      => $d['modalidades'],
			'inLanguage'       => pm_meta_idioma( $d['idioma'] )['html'],
		],
		[
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array_map( fn( $m, $i ) => [ '@type' => 'ListItem', 'position' => $i + 1 ] + $m, $migas, array_keys( $migas ) ),
		],
	];
	$faqs = $d['faqs'];
	if ( $faqs ) {
		$out[] = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array_values( array_map( fn( $f ) => [ '@type' => 'Question', 'name' => $f['pregunta'], 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $f['respuesta'] ] ], $faqs ) ),
		];
	}
	return $out;
}

add_filter( 'pre_get_document_title', function ( $t ) {
	return pm_vista() || is_404() ? pm_seo()['title'] : $t;
} );

add_filter( 'wp_robots', function ( $robots ) {
	if ( pm_seo()['noindex'] ) {
		$robots['noindex'] = true;
		unset( $robots['max-image-preview'] );
	}
	return $robots;
} );

add_action( 'wp_head', function () {
	$s      = pm_seo();
	$url    = home_url( add_query_arg( [] ) );
	$canon  = strtok( $url, '?' );
	$idioma = pm_meta_idioma();
	echo '<meta name="description" content="' . esc_attr( $s['description'] ) . "\">\n";
	echo '<link rel="canonical" href="' . esc_url( $canon ) . "\">\n";
	foreach ( $s['alternas'] as $i => $u ) {
		echo '<link rel="alternate" hreflang="' . esc_attr( pm_meta_idioma( $i )['hreflang'] ) . '" href="' . esc_url( $u ) . "\">\n";
	}
	if ( count( $s['alternas'] ) > 1 ) {
		echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $s['alternas']['es'] ?? $canon ) . "\">\n";
	}
	$og = [
		'og:type'        => 'website',
		'og:site_name'   => 'Pescamapa',
		'og:locale'      => $idioma['og'],
		'og:title'       => $s['title'],
		'og:description' => $s['description'],
		'og:url'         => $canon,
	];
	if ( ! empty( $s['imagen'] ) ) {
		$og['og:image'] = $s['imagen'];
	}
	foreach ( $og as $p => $v ) {
		echo '<meta property="' . esc_attr( $p ) . '" content="' . esc_attr( $v ) . "\">\n";
	}
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="theme-color" content="#11251f">' . "\n";
	foreach ( $s['jsonld'] as $j ) {
		echo '<script type="application/ld+json">' . wp_json_encode( $j, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
	}
}, 2 );

// --- Sitemaps -------------------------------------------------------------------

add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return in_array( $name, [ 'users', 'taxonomies' ], true ) ? false : $provider;
}, 10, 2 );

add_filter( 'wp_sitemaps_post_types', function ( $tipos ) {
	unset( $tipos['pm_proximo'], $tipos['pm_prestador'], $tipos['pm_consulta'], $tipos['attachment'] );
	return $tipos;
} );


// Fixed views (home, map, credits) in every language.
add_action( 'init', function () {
	if ( ! class_exists( 'WP_Sitemaps_Provider' ) ) {
		return;
	}
	require_once PM_DIR . 'inc/sitemap-vistas.php';
	wp_register_sitemap_provider( 'vistas', new PM_Sitemap_Vistas() );
} );
