<?php
/**
 * Interviews with local guides, outfitters and anglers: our own content, with
 * a name and a date, which is what search engines and AI answers cite.
 *
 * - One post per language sharing `clave`, like guides (Spanish is the source;
 *   the person, destination, date, consent and photo live on the Spanish one).
 * - The body is the full conversation; "Citas" are the short quotes that also
 *   show in the destination guide ("Lo que dicen los que pescan ahí").
 * - Nothing is public without the person's consent (checkbox): no page, no
 *   quotes, no sitemap entry.
 */

defined( 'ABSPATH' ) || exit;

const PM_TEMAS_CITA = [
	'temporada' => 'Temporada y altura del río',
	'tecnica'   => 'Técnicas y carnadas',
	'equipo'    => 'Equipo y lancha',
	'elegir'    => 'Cómo elegir guía o alojamiento',
	'lugar'     => 'El lugar y el río',
	'consejo'   => 'Consejo para el que viene',
];

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$solo_es = [ [ [ 'field' => 'field_pm_n_idioma', 'operator' => '==', 'value' => 'es' ] ] ];

	acf_add_local_field_group( [
		'key'      => 'group_pm_entrevista',
		'title'    => 'Datos de la entrevista',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'pm_entrevista' ] ] ],
		'position' => 'acf_after_title',
		'fields'   => array_merge(
			array_slice( pm_campos_comunes( 'n' ), 1 ),
			[
				[ 'key' => 'field_pm_n_tab_persona', 'label' => 'Persona (vale para los tres idiomas)', 'type' => 'tab', 'conditional_logic' => $solo_es ],
				[ 'key' => 'field_pm_n_persona_msg', 'label' => '', 'type' => 'message', 'conditional_logic' => $solo_es, 'message' => 'El <strong>título</strong> de la entrevista es el titular de la página (ej. "Agustín López: «En Goya el surubí se busca con el río bajando»"). La <strong>foto</strong> de la persona va en "Imagen destacada" (con su crédito).' ],
				[ 'key' => 'field_pm_n_persona', 'name' => 'persona', 'label' => 'Nombre de la persona entrevistada', 'type' => 'text', 'required' => 1, 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_n_destino', 'name' => 'destino', 'label' => 'Destino', 'type' => 'select', 'choices' => [], 'allow_null' => 1, 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Sus citas aparecen en la guía de este destino.' ],
				[ 'key' => 'field_pm_n_fecha', 'name' => 'fecha', 'label' => 'Fecha de la entrevista', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'required' => 1, 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_n_consentimiento', 'name' => 'consentimiento', 'label' => 'Aceptó que publiquemos su nombre, foto y frases', 'type' => 'true_false', 'ui' => 1, 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Sin esta marca la entrevista no se ve en la web (ni sus citas).' ],

				[ 'key' => 'field_pm_n_tab_textos', 'label' => 'Textos', 'type' => 'tab' ],
				[ 'key' => 'field_pm_n_rol', 'name' => 'rol', 'label' => 'Quién es, en una línea', 'type' => 'text', 'required' => 1, 'instructions' => 'Ej. "Guía de pesca en Goya desde 1998". Va debajo de su nombre en cada cita.' ],
				[ 'key' => 'field_pm_n_bajada', 'name' => 'bajada', 'label' => 'Subtítulo', 'type' => 'textarea', 'rows' => 2, 'required' => 1 ],
				[
					'key' => 'field_pm_n_citas', 'name' => 'citas', 'label' => 'Citas destacadas', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar cita',
					'instructions' => 'Frases textuales, cortas (1 a 3 oraciones), que respondan algo concreto. Aparecen en la guía del destino.',
					'sub_fields' => [
						[ 'key' => 'field_pm_n_ci_cita', 'name' => 'cita', 'label' => 'Cita', 'type' => 'textarea', 'rows' => 2, 'required' => 1 ],
						[ 'key' => 'field_pm_n_ci_tema', 'name' => 'tema', 'label' => 'Tema', 'type' => 'select', 'choices' => PM_TEMAS_CITA, 'required' => 1 ],
					],
				],
			],
			pm_campos_seo( 'n' )
		),
	] );
} );

add_filter( 'acf/load_field/key=field_pm_n_destino', function ( $field ) {
	$field['choices'] = [];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		$field['choices'][ $d['clave'] ] = $d['nombre'];
	}
	return $field;
} );

// --- Read model -----------------------------------------------------------------

function pm_entrevista( int $post_id ): array {
	$idioma = pm_idioma_de( $post_id );
	$fuente = 'es' === $idioma ? get_post( $post_id ) : pm_buscar( 'pm_entrevista', pm_clave_de( $post_id ), 'es' );
	$fid    = $fuente ? $fuente->ID : $post_id;
	return [
		'id'             => $post_id,
		'idioma'         => $idioma,
		'clave'          => pm_clave_de( $post_id ),
		'titulo'         => get_the_title( $post_id ),
		'url'            => get_permalink( $post_id ),
		'persona'        => (string) pm_campo( 'persona', $fid ),
		'destino'        => (string) pm_campo( 'destino', $fid ),
		'fecha'          => (string) pm_campo( 'fecha', $fid ),
		'consentimiento' => (bool) pm_campo( 'consentimiento', $fid ),
		'foto'           => (int) get_post_thumbnail_id( $fid ),
		'rol'            => (string) pm_campo( 'rol', $post_id ),
		'bajada'         => (string) pm_campo( 'bajada', $post_id ),
		'citas'          => pm_sin_pendientes( pm_filas( 'citas', $post_id ) ),
		'seo_title'      => (string) pm_campo( 'seo_title', $post_id ),
		'seo_description' => (string) pm_campo( 'seo_description', $post_id ),
	];
}

/** Public interviews (with consent), one per clave: this language, else Spanish. Newest first. */
function pm_entrevistas_lista( string $idioma, string $destino = '' ): array {
	$por_clave = [];
	foreach ( pm_posts( 'pm_entrevista' ) as $p ) {
		$clave = pm_clave_de( $p->ID );
		$i     = pm_idioma_de( $p->ID );
		if ( $i === $idioma || ( 'es' === $i && ! isset( $por_clave[ $clave ] ) ) ) {
			$por_clave[ $clave ] = $p->ID;
		}
	}
	$out = array_filter( array_map( 'pm_entrevista', array_values( $por_clave ) ), fn( $e ) => $e['consentimiento'] && ( ! $destino || $e['destino'] === $destino ) );
	usort( $out, fn( $a, $b ) => strcmp( $b['fecha'], $a['fecha'] ) );
	return array_values( $out );
}

function pm_alternas_entrevista( string $clave ): array {
	$out = [];
	foreach ( pm_posts( 'pm_entrevista' ) as $p ) {
		if ( pm_clave_de( $p->ID ) === $clave ) {
			$out[ pm_idioma_de( $p->ID ) ] = get_permalink( $p->ID );
		}
	}
	return $out;
}

/** Quotes for a destination guide, each with who said it. */
function pm_citas_destino( string $destino, string $idioma, int $max = 6 ): array {
	$out = [];
	foreach ( pm_entrevistas_lista( $idioma, $destino ) as $e ) {
		foreach ( $e['citas'] as $c ) {
			$out[] = $c + [ 'persona' => $e['persona'], 'rol' => $e['rol'], 'url' => $e['url'], 'idioma' => $e['idioma'], 'fecha' => $e['fecha'] ];
		}
	}
	return array_slice( $out, 0, $max );
}

// No consent, no page: visitors get a 404 (editors can still preview).
add_action( 'template_redirect', function () {
	if ( is_singular( 'pm_entrevista' ) && ! current_user_can( 'edit_posts' ) && ! pm_entrevista( get_queried_object_id() )['consentimiento'] ) {
		global $wp_query;
		$wp_query->set_404();
		status_header( 404 );
	}
}, 2 );

add_filter( 'wp_sitemaps_posts_query_args', function ( $args, $tipo ) {
	if ( 'pm_entrevista' === $tipo ) {
		$ok = array_map( fn( $e ) => $e['id'], array_merge( ...array_map( 'pm_entrevistas_lista', pm_idiomas() ) ) );
		$args['post__in'] = $ok ?: [ 0 ];
	}
	return $args;
}, 10, 2 );

function pm_jsonld_entrevista( array $e ): array {
	$persona = [ '@type' => 'Person', 'name' => $e['persona'], 'description' => $e['rol'] ];
	$d       = $e['destino'] ? pm_buscar( 'pm_destino', $e['destino'], $e['idioma'] ) ?? pm_buscar( 'pm_destino', $e['destino'], 'es' ) : null;
	$art     = [
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => $e['titulo'],
		'description'   => $e['seo_description'] ?: $e['bajada'],
		'url'           => $e['url'],
		'datePublished' => $e['fecha'],
		'inLanguage'    => pm_meta_idioma( $e['idioma'] )['html'],
		'author'        => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( $e['idioma'] ) ],
		'about'         => $persona,
		'mentions'      => $persona,
	];
	if ( $e['foto'] ) {
		$art['image'] = wp_get_attachment_image_url( $e['foto'], 'large' );
	}
	if ( $d ) {
		$art['contentLocation'] = [ '@type' => 'Place', 'name' => get_the_title( $d ), 'url' => get_permalink( $d ) ];
	}
	return [
		$art,
		[
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => [
				[ '@type' => 'ListItem', 'position' => 1, 'name' => pm_t( 'ficha.inicio', [], $e['idioma'] ), 'item' => pm_url_inicio( $e['idioma'] ) ],
				[ '@type' => 'ListItem', 'position' => 2, 'name' => pm_t( 'entrevistas.h1', [], $e['idioma'] ), 'item' => pm_url_entrevistas( $e['idioma'] ) ],
				[ '@type' => 'ListItem', 'position' => 3, 'name' => $e['titulo'], 'item' => $e['url'] ],
			],
		],
	];
}

// Admin list: who, destination, consent.
add_filter( 'manage_pm_entrevista_posts_columns', function ( $cols ) {
	$cols['pm_persona'] = 'Persona';
	$cols['pm_permiso'] = 'Permiso';
	return $cols;
}, 30 );
add_action( 'manage_pm_entrevista_posts_custom_column', function ( $col, $id ) {
	if ( 'pm_persona' === $col ) {
		$e = pm_entrevista( $id );
		echo esc_html( trim( $e['persona'] . ( $e['destino'] ? " · {$e['destino']}" : '' ), ' ·' ) );
	} elseif ( 'pm_permiso' === $col ) {
		echo pm_entrevista( $id )['consentimiento'] ? '✅' : '⛔ sin permiso (no se ve)';
	}
}, 10, 2 );
