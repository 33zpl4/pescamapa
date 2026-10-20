<?php
/**
 * Outfitters (guides, lodges, operators): one central record each, in
 * Spanish, filled from calls and visits. What local guides told us matters
 * when choosing one is what the form asks: the fishing style, the boat and
 * gear, what the package includes, the season, photos and references.
 *
 * Contact data is internal and never printed: every inquiry from the site
 * comes to Pescamapa (inc/consultas.php), and we pass it on. A record shows
 * in the destination guide only with the outfitter's consent ("Aceptó
 * aparecer"), with dated prices, as a card with a "Consultar" button.
 *
 * Option labels for the three languages live in data/ui.json → opciones.
 */

defined( 'ABSPATH' ) || exit;

/** Choices of an option list, in Spanish for the form. */
function pm_opciones( string $lista ): array {
	return array_map( fn( $o ) => $o['es'], pm_ui()['opciones'][ $lista ] ?? [] );
}

/** Label of an option in the request language. */
function pm_opcion( string $lista, string $clave, ?string $idioma = null ): string {
	return pm_ui()['opciones'][ $lista ][ $clave ][ $idioma ?? pm_lang() ] ?? $clave;
}

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group( [
		'key'      => 'group_pm_prestador',
		'title'    => 'Ficha del prestador',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'pm_prestador' ] ] ],
		'position' => 'acf_after_title',
		'fields'   => [
			[ 'key' => 'field_pm_r_msg', 'label' => '', 'type' => 'message', 'message' => 'El <strong>título</strong> es el nombre del servicio (ej. "Pesca con Agus — Goya"). El <strong>contacto</strong> es interno: nunca se publica; las consultas de la web llegan a Pescamapa y nosotros se las pasamos. La ficha aparece en la guía del destino solo si <strong>aceptó aparecer</strong>.' ],

			[ 'key' => 'field_pm_r_tab_quien', 'label' => 'Quién y dónde', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_tipo', 'name' => 'tipo', 'label' => 'Qué ofrece', 'type' => 'checkbox', 'choices' => pm_opciones( 'tipo' ), 'layout' => 'horizontal', 'required' => 1 ],
			[ 'key' => 'field_pm_r_destino', 'name' => 'destino', 'label' => 'Destino', 'type' => 'select', 'choices' => [], 'allow_null' => 1, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Si su localidad todavía no tiene guía, dejalo vacío y anotala abajo.' ],
			[ 'key' => 'field_pm_r_localidad', 'name' => 'localidad', 'label' => 'Localidad', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_descripcion', 'name' => 'descripcion', 'label' => 'En dos líneas (se publica)', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Cómo trabaja, desde cuándo, qué lo distingue. Sin teléfonos ni redes.' ],

			[ 'key' => 'field_pm_r_tab_contacto', 'label' => 'Contacto (interno)', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_responsable', 'name' => 'responsable', 'label' => 'Dueño o quien opera', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_whatsapp', 'name' => 'whatsapp', 'label' => 'WhatsApp (con código de área)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_email', 'name' => 'email', 'label' => 'Email', 'type' => 'email', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_instagram', 'name' => 'instagram', 'label' => 'Instagram (usuario o enlace)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_web', 'name' => 'web', 'label' => 'Web', 'type' => 'url' ],

			[ 'key' => 'field_pm_r_tab_pesca', 'label' => 'Pesca y equipo', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_modalidades', 'name' => 'modalidades', 'label' => 'Modalidades', 'type' => 'checkbox', 'choices' => pm_opciones( 'modalidad' ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_especies', 'name' => 'especies', 'label' => 'Especies', 'type' => 'checkbox', 'choices' => [], 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_equipo', 'name' => 'equipo', 'label' => 'Lancha y equipo', 'type' => 'checkbox', 'choices' => pm_opciones( 'equipo' ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_capacidad', 'name' => 'capacidad', 'label' => 'Pescadores por lancha', 'type' => 'number', 'min' => 1, 'max' => 20, 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_temporada', 'name' => 'temporada', 'label' => 'Cuándo trabaja (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "Todo el año; dorado de septiembre a marzo".' ],

			[ 'key' => 'field_pm_r_tab_paquete', 'label' => 'Paquete y precio', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_incluye', 'name' => 'incluye', 'label' => 'Qué incluye', 'type' => 'checkbox', 'choices' => pm_opciones( 'incluye' ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_precio', 'name' => 'precio', 'label' => 'Precio de referencia (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "Desde $ 180.000 por lancha, día completo".' ],
			[ 'key' => 'field_pm_r_precio_fecha', 'name' => 'precio_fecha', 'label' => 'Fecha del precio', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Sin fecha, el precio no se muestra.' ],
			[ 'key' => 'field_pm_r_fotos', 'name' => 'fotos', 'label' => 'Fotos (complejo, lancha, salidas)', 'type' => 'gallery', 'return_format' => 'id', 'instructions' => 'La primera es la portada. Cada foto con su autor (biblioteca de medios).' ],

			[ 'key' => 'field_pm_r_tab_relacion', 'label' => 'Referencias y estado (interno)', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_referencias', 'name' => 'referencias', 'label' => 'Referencias', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Quién lo recomendó, qué dicen otros pescadores, reseñas que viste.' ],
			[ 'key' => 'field_pm_r_estado', 'name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'choices' => [ 'relevado' => 'Relevado (todavía no hablamos)', 'contactado' => 'Contactado', 'acuerdo' => 'Trabajamos juntos', 'pausado' => 'Pausado' ], 'default_value' => 'relevado', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_comision', 'name' => 'comision', 'label' => 'Acuerdo comercial', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "10 % por consulta concretada" (interno).' ],
			[ 'key' => 'field_pm_r_consentimiento', 'name' => 'consentimiento', 'label' => 'Aceptó aparecer en Pescamapa', 'type' => 'true_false', 'ui' => 1, 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_verificado', 'name' => 'verificado', 'label' => 'Datos verificados el', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_notas', 'name' => 'notas', 'label' => 'Notas internas', 'type' => 'textarea', 'rows' => 4 ],
		],
	] );
} );

add_filter( 'acf/load_field/key=field_pm_r_destino', function ( $field ) {
	$field['choices'] = [];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		$field['choices'][ $d['clave'] ] = $d['nombre'];
	}
	return $field;
} );
add_filter( 'acf/load_field/key=field_pm_r_especies', function ( $field ) {
	$field['choices'] = [];
	foreach ( pm_especies_lista( 'es' ) as $e ) {
		$field['choices'][ $e['clave'] ] = $e['nombre'];
	}
	return $field;
} );

// --- Read model -----------------------------------------------------------------

function pm_prestador( int $post_id ): array {
	$lista = fn( $n ) => array_values( (array) ( pm_campo( $n, $post_id ) ?: [] ) );
	return [
		'id'             => $post_id,
		'nombre'         => get_the_title( $post_id ),
		'tipo'           => $lista( 'tipo' ),
		'destino'        => (string) pm_campo( 'destino', $post_id ),
		'localidad'      => (string) pm_campo( 'localidad', $post_id ),
		'descripcion'    => (string) pm_campo( 'descripcion', $post_id ),
		'modalidades'    => $lista( 'modalidades' ),
		'especies'       => $lista( 'especies' ),
		'equipo'         => $lista( 'equipo' ),
		'capacidad'      => (int) pm_campo( 'capacidad', $post_id ),
		'temporada'      => (string) pm_campo( 'temporada', $post_id ),
		'incluye'        => $lista( 'incluye' ),
		'precio'         => (string) pm_campo( 'precio', $post_id ),
		'precio_fecha'   => (string) pm_campo( 'precio_fecha', $post_id ),
		'fotos'          => array_map( 'intval', $lista( 'fotos' ) ) ?: array_filter( [ (int) get_post_thumbnail_id( $post_id ) ] ),
		'consentimiento' => (bool) pm_campo( 'consentimiento', $post_id ),
	];
}

/** Outfitters that agreed to appear in this destination's guide. */
function pm_prestadores_destino( string $destino ): array {
	$out = [];
	foreach ( pm_posts( 'pm_prestador' ) as $p ) {
		$r = pm_prestador( $p->ID );
		if ( $r['consentimiento'] && $r['destino'] === $destino ) {
			$out[] = $r;
		}
	}
	return $out;
}

// Admin list: where, what, state, consent.
add_filter( 'manage_pm_prestador_posts_columns', function ( $cols ) {
	return [ 'cb' => $cols['cb'], 'title' => 'Nombre', 'pm_donde' => 'Dónde', 'pm_tipo' => 'Qué ofrece', 'pm_estado' => 'Estado', 'pm_visible' => 'En la web', 'date' => $cols['date'] ];
} );
add_action( 'manage_pm_prestador_posts_custom_column', function ( $col, $id ) {
	$r = pm_prestador( $id );
	if ( 'pm_donde' === $col ) {
		echo esc_html( $r['destino'] ?: $r['localidad'] );
	} elseif ( 'pm_tipo' === $col ) {
		echo esc_html( implode( ', ', array_map( fn( $t ) => pm_opcion( 'tipo', $t, 'es' ), $r['tipo'] ) ) );
	} elseif ( 'pm_estado' === $col ) {
		echo esc_html( (string) pm_campo( 'estado', $id ) );
	} elseif ( 'pm_visible' === $col ) {
		echo $r['consentimiento'] ? '✅' : '—';
	}
}, 10, 2 );
