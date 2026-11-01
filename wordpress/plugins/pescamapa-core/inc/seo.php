<?php
/**
 * <head> and sitemaps without an SEO plugin: the data model already has the
 * title, description, languages and draft state, so we print them directly.
 *   - title, description, canonical, hreflang (es, pt-BR, en, x-default → es)
 *   - Open Graph
 *   - noindex only on the 404 page; every published ficha is indexable
 *   - JSON-LD: WebSite (home), TouristDestination + BreadcrumbList + FAQPage,
 *     Article + BreadcrumbList (interviews, notes)
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
			'creator'     => [
				[ '@type' => 'Person', 'name' => 'Cheyenne Justiniano' ],
				[ '@type' => 'Person', 'name' => 'Alberto Ayala' ],
			],
		];
	} elseif ( 'mapa' === $vista ) {
		$s['title']       = pm_t( 'mapa.title' );
		$s['description'] = pm_t( 'mapa.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_mapa' );
	} elseif ( 'pique' === $vista ) {
		$s['title']       = pm_t( 'pique.title' );
		$s['description'] = pm_t( 'pique.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_pique' );
	} elseif ( 'viaje' === $vista ) {
		$s['title']       = pm_t( 'viaje.title' );
		$s['description'] = pm_t( 'viaje.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_viaje' );
	} elseif ( 'rio' === $vista ) {
		$s['title']       = pm_t( 'rio.title' );
		$s['description'] = pm_t( 'rio.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_rio' );
		$cap              = (string) ( pm_rio_datos()['capturado'] ?? '' );
		$s['jsonld'][]    = array_filter( [
			'@context'     => 'https://schema.org',
			'@type'        => 'WebPage',
			'name'         => pm_t( 'rio.h1' ),
			'description'  => pm_t( 'rio.description' ),
			'url'          => pm_url_rio( pm_lang() ),
			'inLanguage'   => pm_meta_idioma()['html'],
			'dateModified' => $cap,
			'isPartOf'     => [ '@type' => 'WebSite', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
			'isBasedOn'    => 'https://alerta.ina.gob.ar/',
		] );
	} elseif ( 'calendario' === $vista ) {
		$s['title']       = pm_t( 'cal.title' );
		$s['description'] = pm_t( 'cal.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_calendario' );
		$destinos         = pm_destinos_lista( pm_lang() );
		$fechas           = array_filter( array_map( fn( $d ) => $d['reglamento']['verificado'], $destinos ) );
		$s['jsonld'][]    = array_filter( [
			'@context'     => 'https://schema.org',
			'@type'        => 'WebPage',
			'name'         => pm_t( 'cal.h1' ),
			'description'  => pm_t( 'cal.description' ),
			'url'          => pm_url_calendario( pm_lang() ),
			'inLanguage'   => pm_meta_idioma()['html'],
			'dateModified' => $fechas ? max( $fechas ) : '',
			'isPartOf'     => [ '@type' => 'WebSite', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
			'publisher'    => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
			'hasPart'      => array_map( fn( $d ) => [ '@type' => 'WebPageElement', 'name' => $d['nombre'], 'url' => pm_url_calendario( pm_lang() ) . '#' . $d['clave'], 'about' => [ '@type' => 'Place', 'name' => $d['nombre'], 'url' => $d['url'] ] ], $destinos ),
		] );
		$s['jsonld'][]    = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array_map( fn( $n ) => [ '@type' => 'Question', 'name' => pm_t( "cal.faq.$n.q" ), 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => pm_t( "cal.faq.$n.a" ) ] ], [ 1, 2, 3 ] ),
		];
		$s['jsonld'][]    = [
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => [
				[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Pescamapa', 'item' => pm_url_inicio( pm_lang() ) ],
				[ '@type' => 'ListItem', 'position' => 2, 'name' => pm_t( 'cal.eyebrow' ), 'item' => pm_url_calendario( pm_lang() ) ],
			],
		];
	} elseif ( 'paraguias' === $vista ) {
		$s['title']       = pm_t( 'pg.title' );
		$s['description'] = pm_t( 'pg.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_paraguias' );
		$s['jsonld'][]    = [
			'@context'    => 'https://schema.org',
			'@type'       => 'WebPage',
			'name'        => pm_t( 'pg.h1' ),
			'description' => pm_t( 'pg.description' ),
			'url'         => pm_url_paraguias( pm_lang() ),
			'inLanguage'  => pm_meta_idioma()['html'],
			'isPartOf'    => [ '@type' => 'WebSite', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
			'publisher'   => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ), 'email' => PM_EMAIL_PUBLICO ],
		];
		$s['jsonld'][]    = [
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array_map( fn( $n ) => [ '@type' => 'Question', 'name' => pm_t( "pg.faq.$n.q" ), 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => pm_t( "pg.faq.$n.a" ) ] ], [ 1, 2, 3, 4 ] ),
		];
	} elseif ( 'rio-punto' === $vista ) {
		$k  = (string) get_query_var( 'pm_rio_clave' );
		$x  = pm_rio_destino( $k );
		$r  = $x['rio'] ?? null;
		$a  = [ 'lugar' => pm_rio_nombre( $k ), 'curso' => $r['curso'] ?? '' ];
		$s['title']       = pm_t( 'rio.p.title', $a );
		$s['description'] = $r ? pm_t( pm_rio_mareal( $k ) ? 'rio.p.description.mareal' : 'rio.p.description', $a + [ 'altura' => pm_rio_num( $r['altura'] ), 'fecha' => pm_fecha( $r['fecha'] ), 'tend' => mb_strtolower( pm_rio_tendencia_texto( $r ) ) ] ) : pm_t( 'rio.description' );
		$s['alternas']    = pm_alternas_fijas( fn( $i ) => pm_url_rio_punto( $k, $i ) );
		$s['jsonld'][]    = array_filter( [
			'@context'     => 'https://schema.org',
			'@type'        => 'WebPage',
			'name'         => pm_t( 'rio.p.h1', $a ),
			'description'  => $s['description'],
			'url'          => pm_url_rio_punto( $k, pm_lang() ),
			'inLanguage'   => pm_meta_idioma()['html'],
			'dateModified' => (string) ( $x['capturado'] ?? '' ),
			'isPartOf'     => [ '@type' => 'WebSite', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
			'isBasedOn'    => 'https://alerta.ina.gob.ar/',
		] );
		$s['jsonld'][]    = [
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => [
				[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Pescamapa', 'item' => pm_url_inicio( pm_lang() ) ],
				[ '@type' => 'ListItem', 'position' => 2, 'name' => pm_t( 'rio.h1' ), 'item' => pm_url_rio( pm_lang() ) ],
				[ '@type' => 'ListItem', 'position' => 3, 'name' => $a['lugar'], 'item' => pm_url_rio_punto( $k, pm_lang() ) ],
			],
		];
	} elseif ( 'terminos' === $vista || 'privacidad' === $vista ) {
		$l                = pm_legal( $vista );
		$s['title']       = $l['title'] ?? '';
		$s['description'] = $l['description'] ?? '';
		$s['alternas']    = pm_alternas_fijas( 'terminos' === $vista ? 'pm_url_terminos' : 'pm_url_privacidad' );
	} elseif ( 'destino' === $vista ) {
		$d = pm_destino( get_queried_object_id() );
		$s['title']       = $d['seo_title'] ?: $d['nombre'];
		$s['description'] = $d['seo_description'];
		$s['alternas']    = pm_alternas_destino( $d['clave'] );
		$s['jsonld']      = pm_jsonld_destino( $d );
		if ( $d['foto'] ) {
			$s['imagen'] = wp_get_attachment_image_url( $d['foto'], 'large' );
		}
	} elseif ( 'paquetes' === $vista ) {
		$k   = (string) get_query_var( 'pm_paquete_clave' );
		$dp  = pm_buscar( 'pm_destino', $k, pm_lang() );
		$d   = $dp ? pm_destino( $dp->ID ) : null;
		$paq = $d ? pm_paquetes( $dp->ID ) : [];
		$a   = [ 'lugar' => $d['nombre'] ?? '' ];
		$s['title']       = pm_t( 'paquetes.title', $a );
		$s['description'] = ( $dp ? trim( (string) pm_campo( 'paquetes_description', $dp->ID ) ) : '' ) ?: pm_t( 'paquetes.description', $a );
		$s['alternas']    = pm_alternas_paquetes( $k );
		if ( $d ) {
			$items = [];
			foreach ( $paq as $n => $p ) {
				$trip = array_filter( [ '@type' => 'TouristTrip', 'name' => $p['titulo'], 'description' => $p['resumen'], 'provider' => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ] ] );
				if ( $p['precio'] ) {
					$trip['offers'] = [ '@type' => 'Offer', 'price' => $p['precio']['monto'], 'priceCurrency' => $p['precio']['moneda'], 'validFrom' => $p['precio']['fecha'] ];
				}
				$items[] = [ '@type' => 'ListItem', 'position' => $n + 1, 'item' => $trip ];
			}
			$s['jsonld'][] = [ '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => pm_t( 'paquetes.h1', $a ), 'itemListElement' => $items ];
			$s['jsonld'][] = [
				'@context'     => 'https://schema.org',
				'@type'        => 'WebPage',
				'name'         => pm_t( 'paquetes.h1', $a ),
				'description'  => $s['description'],
				'url'          => pm_url_paquetes( $k, pm_lang() ),
				'inLanguage'   => pm_meta_idioma()['html'],
				'dateModified' => get_post_modified_time( 'Y-m-d', true, $dp ),
				'isPartOf'     => [ '@type' => 'WebSite', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
				'about'        => [ '@type' => 'Place', 'name' => $d['nombre'] ],
			];
			$s['jsonld'][] = [
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => array_map( fn( $n ) => [ '@type' => 'Question', 'name' => pm_t( "paquetes.faq.$n.q", $a ), 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => pm_t( "paquetes.faq.$n.a", $a ) ] ], [ 1, 2, 3, 4 ] ),
			];
			if ( $d['foto'] ) {
				$s['imagen'] = wp_get_attachment_image_url( $d['foto'], 'large' );
			}
			$s['jsonld'][] = [
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => [
					[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Pescamapa', 'item' => pm_url_inicio( pm_lang() ) ],
					[ '@type' => 'ListItem', 'position' => 2, 'name' => $d['nombre'], 'item' => $d['url'] ],
					[ '@type' => 'ListItem', 'position' => 3, 'name' => pm_t( 'paquetes.h1', $a ), 'item' => pm_url_paquetes( $k, pm_lang() ) ],
				],
			];
		}
	} elseif ( 'guias' === $vista ) {
		$k   = (string) get_query_var( 'pm_guias_clave' );
		$dp  = pm_buscar( 'pm_destino', $k, pm_lang() );
		$d   = $dp ? pm_destino( $dp->ID ) : null;
		$a   = [ 'lugar' => $d['nombre'] ?? '' ];
		$s['title']       = pm_t( 'guias.title', $a );
		$s['description'] = pm_t( 'guias.description', $a );
		$s['alternas']    = pm_alternas_guias( $k );
		if ( $d ) {
			$url   = pm_url_guias( $k, pm_lang() );
			$items = [];
			foreach ( pm_prestadores_destino( $k ) as $n => $r ) {
				// Only an operator with a stated agreement is described as a business; a public-information
				// listing is just a named item, so the page never asserts more than it verified.
				$item = [ '@type' => 'ListItem', 'position' => $n + 1, 'name' => $r['nombre'], 'url' => $url . '#prestador-' . $r['id'] ];
				if ( $r['acuerdo'] && $r['consentimiento'] ) {
					$item['item'] = array_filter( [
						'@type'       => 'LocalBusiness',
						'name'        => $r['nombre'],
						'description' => $r['descripcion'],
						'url'         => $url . '#prestador-' . $r['id'],
						'areaServed'  => [ '@type' => 'Place', 'name' => $d['nombre'] ],
						'image'       => $r['fotos'] ? wp_get_attachment_image_url( $r['fotos'][0], 'large' ) : null,
					] );
				}
				$items[] = $item;
			}
			$s['jsonld'][] = [ '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => pm_t( 'guias.h1', $a ), 'numberOfItems' => count( $items ), 'itemListElement' => $items ];
			$s['jsonld'][] = [
				'@context'     => 'https://schema.org',
				'@type'        => 'CollectionPage',
				'name'         => pm_t( 'guias.h1', $a ),
				'description'  => $s['description'],
				'url'          => $url,
				'inLanguage'   => pm_meta_idioma()['html'],
				'dateModified' => get_post_modified_time( 'Y-m-d', true, $dp ),
				'isPartOf'     => [ '@type' => 'WebSite', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
				'publisher'    => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( pm_lang() ) ],
				'about'        => [ '@type' => 'Place', 'name' => $d['nombre'] ],
			];
			$s['jsonld'][] = [
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => array_map( fn( $n ) => [ '@type' => 'Question', 'name' => pm_t( "guias.faq.$n.q", $a ), 'acceptedAnswer' => [ '@type' => 'Answer', 'text' => pm_t( "guias.faq.$n.a", $a ) ] ], [ 1, 2, 3, 4 ] ),
			];
			if ( $d['foto'] ) {
				$s['imagen'] = wp_get_attachment_image_url( $d['foto'], 'large' );
			}
			$s['jsonld'][] = [
				'@context'        => 'https://schema.org',
				'@type'           => 'BreadcrumbList',
				'itemListElement' => [
					[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Pescamapa', 'item' => pm_url_inicio( pm_lang() ) ],
					[ '@type' => 'ListItem', 'position' => 2, 'name' => $d['nombre'], 'item' => $d['url'] ],
					[ '@type' => 'ListItem', 'position' => 3, 'name' => pm_t( 'guias.h1', $a ), 'item' => $url ],
				],
			];
		}
	} elseif ( 'especies' === $vista ) {
		$s['title']       = pm_t( 'especies.title' );
		$s['description'] = pm_t( 'especies.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_especies' );
		$s['jsonld']      = [
			'@context'   => 'https://schema.org',
			'@type'      => 'BreadcrumbList',
			'itemListElement' => [
				[ '@type' => 'ListItem', 'position' => 1, 'name' => 'Pescamapa', 'item' => pm_url_inicio( pm_lang() ) ],
				[ '@type' => 'ListItem', 'position' => 2, 'name' => pm_t( 'especies.h1' ), 'item' => pm_url_especies( pm_lang() ) ],
			],
		];
	} elseif ( 'especie' === $vista ) {
		$e = pm_especie( get_queried_object_id() );
		$s['title']       = $e['seo_title'] ?: $e['nombre'];
		$s['description'] = $e['seo_description'];
		$s['alternas']    = pm_alternas_especie( $e['clave'] );
		$s['jsonld']      = pm_jsonld_especie( $e );
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
	} elseif ( 'notas' === $vista ) {
		$s['title']       = pm_t( 'notas.title' );
		$s['description'] = pm_t( 'notas.description' );
		$s['alternas']    = pm_alternas_fijas( 'pm_url_notas' );
	} elseif ( 'nota' === $vista ) {
		$n = pm_nota( get_queried_object_id() );
		$s['title']       = $n['seo_title'] ?: $n['titulo'];
		$s['description'] = $n['seo_description'] ?: $n['bajada'];
		$s['alternas']    = pm_alternas_nota( $n['clave'] );
		$s['jsonld']      = pm_jsonld_nota( $n );
		if ( $n['foto'] ) {
			$s['imagen'] = wp_get_attachment_image_url( $n['foto'], 'large' );
		}
	} elseif ( is_404() ) {
		$s['title']    = 'Página no encontrada | Pescamapa';
		$s['noindex'] = true;
	}
	return $s;
}

function pm_jsonld_especie( array $e ): array {
	$mod = preg_match( '/^(\d{4})(\d{2})(\d{2})$/', $e['actualizado'], $m ) ? "$m[1]-$m[2]-$m[3]" : $e['actualizado'];
	$art = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Article',
		'headline'    => $e['seo_title'] ?: $e['nombre'],
		'description' => $e['seo_description'] ?: $e['resumen'],
		'url'         => $e['url'],
		'about'       => array_filter( [ '@type' => 'Taxon', 'name' => $e['cientifico'] ?: null, 'alternateName' => $e['nombre'] ] ),
		'inLanguage'  => pm_meta_idioma( $e['idioma'] )['html'],
		'author'      => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( $e['idioma'] ) ],
	];
	if ( $mod ) {
		$art['dateModified'] = $mod;
	}
	$citas = array_values( array_filter( array_map( fn( $f ) => ! empty( $f['url'] ) ? [ '@type' => 'CreativeWork', 'name' => $f['nombre'], 'url' => $f['url'] ] : null, $e['fuentes'] ) ) );
	if ( $citas ) {
		$art['citation'] = $citas;
	}
	return [
		$art,
		[
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => [
				[ '@type' => 'ListItem', 'position' => 1, 'name' => pm_t( 'ficha.inicio', [], $e['idioma'] ), 'item' => pm_url_inicio( $e['idioma'] ) ],
				[ '@type' => 'ListItem', 'position' => 2, 'name' => $e['nombre'], 'item' => $e['url'] ],
			],
		],
	];
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
			'dateModified'     => $d['actualizado'] ?: null, // the date the guide shows; what an answer engine should quote as 'as of'
			'publisher'        => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( $d['idioma'] ) ],
		],
		[
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => array_map( fn( $m, $i ) => [ '@type' => 'ListItem', 'position' => $i + 1 ] + $m, $migas, array_keys( $migas ) ),
		],
	];
	$out[0] = array_filter( $out[0], fn( $v ) => null !== $v );
	// Sources as structured data (not a long list the reader has to scan).
	$citas = array_values( array_filter( array_map( fn( $f ) => ! empty( $f['url'] ) ? [ '@type' => 'CreativeWork', 'name' => $f['nombre'], 'url' => $f['url'] ] : null, $d['fuentes'] ) ) );
	if ( $citas ) {
		$out[0]['citation'] = $citas;
	}
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
