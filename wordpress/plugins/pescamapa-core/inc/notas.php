<?php
/**
 * Notes: our own articles (facts, how-tos, stories from the river), built
 * from interviews, material people share with us and public sources. Same
 * language model as guides and interviews: one post per language sharing
 * `clave`; Spanish is the source and holds the shared data.
 *
 * Provenance is the point: every note carries a public list of sources
 * (shown on the page and emitted as `citation` in the JSON-LD) and an
 * internal "material de origen" field (where each fact came from, what is
 * still unconfirmed). Notes never carry exact fishing spots, and a rule or
 * closure is stated only with its official source.
 *
 * Field names carry a "nota_" prefix where another form already has the same
 * name with a different meaning (SCF resolves a name to the first field it
 * finds).
 */

defined( 'ABSPATH' ) || exit;

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	$solo_es = [ [ [ 'field' => 'field_pm_t_idioma', 'operator' => '==', 'value' => 'es' ] ] ];

	acf_add_local_field_group( [
		'key'      => 'group_pm_nota',
		'title'    => 'Datos de la nota',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'pm_nota' ] ] ],
		'position' => 'acf_after_title',
		'fields'   => array_merge(
			pm_campos_comunes( 't' ),
			[
				[ 'key' => 'field_pm_t_tab_nota', 'label' => 'La nota (vale para los tres idiomas)', 'type' => 'tab', 'conditional_logic' => $solo_es ],
				[ 'key' => 'field_pm_t_msg', 'label' => '', 'type' => 'message', 'conditional_logic' => $solo_es, 'message' => 'El <strong>título</strong> es el titular. La <strong>foto</strong> es la "Imagen destacada" (con su autor en la biblioteca). Estos datos se cargan <strong>solo en español</strong> y valen para los tres idiomas.' ],
				[ 'key' => 'field_pm_t_tipo', 'name' => 'nota_tipo', 'label' => 'Tipo de nota', 'type' => 'select', 'choices' => pm_opciones( 'nota_tipo' ), 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_t_destino', 'name' => 'nota_destino', 'label' => 'Destino (si es de un lugar)', 'type' => 'select', 'choices' => [], 'allow_null' => 1, 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_t_especie', 'name' => 'nota_especie', 'label' => 'Especie (si habla de una)', 'type' => 'select', 'choices' => [], 'allow_null' => 1, 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'La nota aparece en las guías de los destinos de esa especie y en su ficha.' ],
				[ 'key' => 'field_pm_t_fecha', 'name' => 'nota_fecha', 'label' => 'Fecha de publicación', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'conditional_logic' => $solo_es, 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_t_origen', 'name' => 'nota_origen', 'label' => 'Material de origen (interno, no se publica)', 'type' => 'textarea', 'rows' => 5, 'conditional_logic' => $solo_es, 'instructions' => 'De dónde salió cada dato: entrevista, video, quién lo contó, qué falta confirmar. Los enlaces públicos van en "Fuentes".' ],

				[ 'key' => 'field_pm_t_tab_textos', 'label' => 'Textos', 'type' => 'tab' ],
				[ 'key' => 'field_pm_t_bajada', 'name' => 'bajada', 'label' => 'Subtítulo', 'type' => 'textarea', 'rows' => 2 ],
				[
					'key' => 'field_pm_t_datos', 'name' => 'nota_datos', 'label' => 'Datos clave', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar dato',
					'instructions' => 'De 3 a 6 datos concretos, uno por fila: salen en un cuadro arriba del texto. Nada de fechas de vedas ni ubicaciones exactas.',
					'sub_fields' => [ [ 'key' => 'field_pm_t_da_dato', 'name' => 'dato', 'label' => 'Dato', 'type' => 'text' ] ],
				],
				[ 'key' => 'field_pm_t_autor', 'name' => 'autor', 'label' => 'Autor', 'type' => 'text', 'default_value' => 'Equipo Pescamapa', 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_t_actualizado', 'name' => 'actualizado', 'label' => 'Última actualización', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 50 ] ],
			],
			pm_campos_seo( 't' ),
			[
				[ 'key' => 'field_pm_t_tab_fuentes', 'label' => 'Fuentes', 'type' => 'tab' ],
				pm_campo_fuentes( 't' ),
			]
		),
	] );
} );

add_filter( 'acf/load_field/key=field_pm_t_especie', function ( $field ) {
	$field['choices'] = [];
	foreach ( pm_especies_lista( 'es' ) as $e ) {
		$field['choices'][ $e['clave'] ] = $e['nombre'];
	}
	return $field;
} );

add_filter( 'acf/load_field/key=field_pm_t_destino', function ( $field ) {
	$field['choices'] = [];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		$field['choices'][ $d['clave'] ] = $d['nombre'];
	}
	return $field;
} );

// --- Read model -----------------------------------------------------------------

function pm_nota( int $post_id ): array {
	$idioma = pm_idioma_de( $post_id );
	$fuente = 'es' === $idioma ? get_post( $post_id ) : pm_buscar( 'pm_nota', pm_clave_de( $post_id ), 'es' );
	$fid    = $fuente ? $fuente->ID : $post_id;
	$fecha  = (string) pm_campo( 'nota_fecha', $fid ) ?: substr( (string) get_post_field( 'post_date', $fid ), 0, 10 );
	return [
		'id'              => $post_id,
		'idioma'          => $idioma,
		'clave'           => pm_clave_de( $post_id ),
		'titulo'          => get_the_title( $post_id ),
		'url'             => get_permalink( $post_id ),
		'tipo'            => (string) pm_campo( 'nota_tipo', $fid ),
		'destino'         => (string) pm_campo( 'nota_destino', $fid ),
		'especie'         => (string) pm_campo( 'nota_especie', $fid ),
		'fecha'           => $fecha,
		'foto'            => (int) get_post_thumbnail_id( $fid ),
		'bajada'          => (string) pm_campo( 'bajada', $post_id ),
		'datos'           => array_values( array_filter( array_map( fn( $f ) => (string) ( $f['dato'] ?? '' ), pm_sin_pendientes( pm_filas( 'nota_datos', $post_id ) ) ) ) ),
		'autor'           => (string) pm_campo( 'autor', $post_id ),
		'actualizado'     => (string) pm_campo( 'actualizado', $post_id ),
		'fuentes'         => array_values( array_filter( pm_filas( 'fuentes', $post_id ), fn( $f ) => ! empty( $f['nombre'] ) ) ),
		'seo_title'       => (string) pm_campo( 'seo_title', $post_id ),
		'seo_description' => (string) pm_campo( 'seo_description', $post_id ),
	];
}

/** Public notes, one per clave: this language, else Spanish. Newest first. */
function pm_notas_lista( string $idioma, string $destino = '' ): array {
	$por_clave = [];
	foreach ( pm_posts( 'pm_nota' ) as $p ) {
		$clave = pm_clave_de( $p->ID );
		$i     = pm_idioma_de( $p->ID );
		if ( $i === $idioma || ( 'es' === $i && ! isset( $por_clave[ $clave ] ) ) ) {
			$por_clave[ $clave ] = $p->ID;
		}
	}
	$out = array_filter( array_map( 'pm_nota', array_values( $por_clave ) ), fn( $n ) => ! $destino || $n['destino'] === $destino );
	usort( $out, fn( $a, $b ) => strcmp( $b['fecha'], $a['fecha'] ) );
	return array_values( $out );
}

/** Is there any public note? (Menus and sitemaps list the index only then.) */
function pm_hay_notas(): bool {
	return (bool) pm_posts( 'pm_nota' );
}

function pm_alternas_nota( string $clave ): array {
	$out = [];
	foreach ( pm_posts( 'pm_nota' ) as $p ) {
		if ( pm_clave_de( $p->ID ) === $clave ) {
			$out[ pm_idioma_de( $p->ID ) ] = get_permalink( $p->ID );
		}
	}
	return $out;
}

function pm_jsonld_nota( array $n ): array {
	$d   = $n['destino'] ? pm_buscar( 'pm_destino', $n['destino'], $n['idioma'] ) ?? pm_buscar( 'pm_destino', $n['destino'], 'es' ) : null;
	$art = [
		'@context'      => 'https://schema.org',
		'@type'         => 'Article',
		'headline'      => $n['titulo'],
		'description'   => $n['seo_description'] ?: $n['bajada'],
		'url'           => $n['url'],
		'datePublished' => $n['fecha'],
		'inLanguage'    => pm_meta_idioma( $n['idioma'] )['html'],
		'author'        => [ '@type' => 'Organization', 'name' => 'Pescamapa', 'url' => pm_url_inicio( $n['idioma'] ) ],
	];
	if ( $n['actualizado'] ) {
		$art['dateModified'] = $n['actualizado'];
	}
	if ( $n['foto'] ) {
		$art['image'] = wp_get_attachment_image_url( $n['foto'], 'large' );
	}
	if ( $d ) {
		$art['contentLocation'] = [ '@type' => 'Place', 'name' => get_the_title( $d ), 'url' => get_permalink( $d ) ];
	}
	$citas = array_values( array_filter( array_map( fn( $f ) => ! empty( $f['url'] ) ? [ '@type' => 'CreativeWork', 'name' => $f['nombre'], 'url' => $f['url'] ] : null, $n['fuentes'] ) ) );
	if ( $citas ) {
		$art['citation'] = $citas;
	}
	return [
		$art,
		[
			'@context'        => 'https://schema.org',
			'@type'           => 'BreadcrumbList',
			'itemListElement' => [
				[ '@type' => 'ListItem', 'position' => 1, 'name' => pm_t( 'ficha.inicio', [], $n['idioma'] ), 'item' => pm_url_inicio( $n['idioma'] ) ],
				[ '@type' => 'ListItem', 'position' => 2, 'name' => pm_t( 'notas.h1', [], $n['idioma'] ), 'item' => pm_url_notas( $n['idioma'] ) ],
				[ '@type' => 'ListItem', 'position' => 3, 'name' => $n['titulo'], 'item' => $n['url'] ],
			],
		],
	];
}

// Admin list: type and destination.
add_filter( 'manage_pm_nota_posts_columns', function ( $cols ) {
	$cols['pm_tipo_nota'] = 'Tipo';
	return $cols;
}, 30 );
add_action( 'manage_pm_nota_posts_custom_column', function ( $col, $id ) {
	if ( 'pm_tipo_nota' === $col ) {
		$n = pm_nota( $id );
		echo esc_html( trim( ( $n['tipo'] ? pm_opcion( 'nota_tipo', $n['tipo'], 'es' ) : '' ) . ( $n['destino'] ? " · {$n['destino']}" : '' ), ' ·' ) );
	}
}, 10, 2 );

/** Notes that belong in a guide or a species page: about that destino, or about one of its species. Newest first. */
function pm_notas_para( string $idioma, string $destino = '', array $especies = [], int $limite = 6 ): array {
	$out = array_filter( pm_notas_lista( $idioma ), fn( $n ) => ( $destino && $n['destino'] === $destino ) || ( $n['especie'] && in_array( $n['especie'], $especies, true ) ) );
	return array_slice( array_values( $out ), 0, $limite );
}
