<?php
/**
 * Outfitters (guides, lodges, operators): one central record each, in
 * Spanish, filled from calls and visits. What local guides told us matters
 * when choosing one is what the form asks: the fishing style, the boat and
 * gear, what the package includes, the season, photos and references.
 *
 * Contact data is internal and never printed: every inquiry from the site
 * comes to Pescamapa (inc/consultas.php), and we pass it on. A record shows
 * in the destination guide as a card with a "Consultar" button when either
 * the outfitter agreed ("Aceptó aparecer") or we list it from public
 * information ("Listado con información pública"; owner ruling 21/10/2026,
 * docs/prestadores.md). The second kind is labelled as unverified and carries
 * a correction/removal link. Prices show only with a date.
 *
 * Option labels for the three languages live in data/ui.json → opciones.
 * Field names that exist in other forms carry a "prest_" prefix: scripts
 * write fields by name, and SCF resolves a name to the first field it finds.
 */

defined( 'ABSPATH' ) || exit;

/** Days after which a card's data or price is due for a new check (owner, 1/11/2026). */
const PM_PREST_REVISION_DIAS = 90;

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
			[ 'key' => 'field_pm_r_msg', 'label' => '', 'type' => 'message', 'message' => 'El <strong>título</strong> es el nombre del servicio (ej. "Pesca con Agus — Goya"). El <strong>contacto</strong> es interno: nunca se publica; las consultas de la web llegan a Pescamapa y nosotros se las pasamos. La ficha aparece en la guía del destino si <strong>aceptó aparecer</strong> o si la cargamos como <strong>listado con información pública</strong> (queda marcada como sin verificar, con un enlace para corregirla o pedir la baja). Reglas: <code>docs/prestadores.md</code>.' ],

			[ 'key' => 'field_pm_r_tab_quien', 'label' => 'Quién y dónde', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_tipo', 'name' => 'tipo', 'label' => 'Qué ofrece', 'type' => 'checkbox', 'choices' => pm_opciones( 'tipo' ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_destino', 'name' => 'prest_destino', 'label' => 'Destino', 'type' => 'select', 'choices' => [], 'allow_null' => 1, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Si su localidad todavía no tiene guía, dejalo vacío y anotala abajo.' ],
			[ 'key' => 'field_pm_r_localidad', 'name' => 'localidad', 'label' => 'Localidad', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_descripcion', 'name' => 'descripcion', 'label' => 'En dos líneas (se publica)', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Cómo trabaja, desde cuándo, qué lo distingue. Sin teléfonos ni redes.' ],
			[ 'key' => 'field_pm_r_fotos', 'name' => 'fotos', 'label' => 'Fotos (complejo, lancha, salidas)', 'type' => 'gallery', 'return_format' => 'id', 'instructions' => 'Sin límite. La primera es la portada y se muestran hasta 4 en la ficha. Cada foto con su autor (biblioteca de medios).' ],

			[ 'key' => 'field_pm_r_tab_contacto', 'label' => 'Contacto (interno)', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_responsable', 'name' => 'responsable', 'label' => 'Dueño o quien opera', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_whatsapp', 'name' => 'whatsapp', 'label' => 'WhatsApp (con código de área)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_email', 'name' => 'email', 'label' => 'Email', 'type' => 'email', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_instagram', 'name' => 'instagram', 'label' => 'Instagram (usuario o enlace)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_web', 'name' => 'web', 'label' => 'Web', 'type' => 'url' ],

			[ 'key' => 'field_pm_r_tab_pesca', 'label' => 'Pesca y equipo', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_modalidades', 'name' => 'prest_modalidades', 'label' => 'Modalidades', 'type' => 'checkbox', 'choices' => pm_opciones( 'modalidad' ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_especies', 'name' => 'prest_especies', 'label' => 'Especies', 'type' => 'checkbox', 'choices' => [], 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_equipo', 'name' => 'equipo', 'label' => 'Lancha y equipo', 'type' => 'checkbox', 'choices' => pm_opciones( 'equipo' ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_lancha', 'name' => 'lancha', 'label' => 'Lancha (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Tipo y largo. Ej. "Tracker de 5,5 m con plataforma de lanzamiento".' ],
			[ 'key' => 'field_pm_r_motor', 'name' => 'motor', 'label' => 'Motor (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "Fuera de borda 90 HP + eléctrico de proa".' ],
			[ 'key' => 'field_pm_r_capacidad', 'name' => 'capacidad', 'label' => 'Cantidad de pescadores', 'type' => 'number', 'min' => 1, 'max' => 20, 'wrapper' => [ 'width' => 25 ] ],
			[ 'key' => 'field_pm_r_capacidad_unidad', 'name' => 'prest_capacidad_unidad', 'label' => 'La cantidad es…', 'type' => 'select', 'choices' => [ 'lancha' => 'Por lancha (guías)', 'semana' => 'Por semana (lodges)', 'total' => 'Capacidad total' ], 'default_value' => 'lancha', 'wrapper' => [ 'width' => 25 ], 'instructions' => 'Cambia la frase: "Hasta N pescadores por lancha", "Recibe N pescadores por semana" o "Capacidad: N pescadores".' ],
			[ 'key' => 'field_pm_r_temporada', 'name' => 'prest_temporada', 'label' => 'Cuándo trabaja (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "Todo el año; dorado de septiembre a marzo".' ],

			[ 'key' => 'field_pm_r_idiomas', 'name' => 'prest_idiomas', 'label' => 'Idiomas en que atiende (se publica)', 'type' => 'checkbox', 'choices' => [ 'es' => 'Español', 'pt' => 'Portugués', 'en' => 'Inglés', 'fr' => 'Francés', 'de' => 'Alemán', 'it' => 'Italiano' ], 'layout' => 'horizontal', 'instructions' => 'Los idiomas que habla quien guía o atiende. Ej. si un guía del servicio habla portugués, marcá Portugués.' ],
			[ 'key' => 'field_pm_r_accesible', 'name' => 'prest_accesible', 'label' => 'Accesibilidad (solo se publica si es Sí)', 'type' => 'select', 'choices' => [ '' => 'Sin dato', 'si' => 'Sí, ofrece servicio accesible', 'no' => 'No' ], 'default_value' => '', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Para personas con movilidad reducida (embarque, muelle, baño). Con "Sí" la tarjeta muestra ♿; "No" queda como dato interno.' ],

			[ 'key' => 'field_pm_r_tab_paquete', 'label' => 'Paquete y precio', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_incluye', 'name' => 'incluye', 'label' => 'Qué incluye', 'type' => 'checkbox', 'choices' => pm_opciones( 'incluye' ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_r_precio', 'name' => 'precio', 'label' => 'Precio de referencia (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "Desde $ 180.000 por lancha, día completo".' ],
			[ 'key' => 'field_pm_r_precio_fecha', 'name' => 'precio_fecha', 'label' => 'Fecha del precio', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Sin fecha, el precio no se muestra.' ],
			[ 'key' => 'field_pm_r_programa', 'name' => 'prest_programa', 'label' => 'Programa (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Duración y formato. Ej. "7 noches, 6 días de pesca con guía; sábado a sábado". Opcional.' ],
			[ 'key' => 'field_pm_r_estadia', 'name' => 'prest_estadia_minima', 'label' => 'Estadía mínima (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "3 noches" o "Solo semanas completas". Opcional.' ],
			[ 'key' => 'field_pm_r_acompanante', 'name' => 'prest_acompanante', 'label' => 'Acompañante que no pesca (se publica con la fecha del precio)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Tarifa y condiciones. Es un precio: sin "Fecha del precio" la ficha dice "a consultar" y no muestra el monto. Solo lo que el prestador publica, nunca un precio de otra fuente.' ],
			[ 'key' => 'field_pm_r_no_incluye', 'name' => 'prest_no_incluye', 'label' => 'Qué no incluye (se publica)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "Vuelos, equipo de pesca, licencia, propinas". Complementa "Qué incluye". Opcional.' ],
			[ 'key' => 'field_pm_r_oferta', 'name' => 'prest_oferta', 'label' => 'Condición especial para quienes piden por Pescamapa (se publica)', 'type' => 'text', 'maxlength' => 140, 'instructions' => 'Una frase concreta que ya acordaron con nosotros. Ej. "Carnada incluida" o "5 % menos". Se publica solo si el Estado (pestaña "Referencias y estado") es "Trabajamos juntos". Ver docs/prestadores.md.' ],

			...pm_prestador_campos_traduccion(),

			[ 'key' => 'field_pm_r_tab_relacion', 'label' => 'Referencias y estado (interno)', 'type' => 'tab' ],
			[ 'key' => 'field_pm_r_referencias', 'name' => 'referencias', 'label' => 'Referencias', 'type' => 'textarea', 'rows' => 4, 'instructions' => 'Quién lo recomendó, qué dicen otros pescadores, reseñas que viste.' ],
			[ 'key' => 'field_pm_r_estado', 'name' => 'estado', 'label' => 'Estado', 'type' => 'select', 'choices' => [ 'relevado' => 'Relevado (todavía no hablamos)', 'contactado' => 'Contactado', 'acuerdo' => 'Trabajamos juntos', 'pausado' => 'Pausado' ], 'default_value' => 'relevado', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_comision', 'name' => 'comision', 'label' => 'Acuerdo comercial', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "10 % por consulta concretada" (interno).' ],
			[ 'key' => 'field_pm_r_ejemplo', 'name' => 'datos_ejemplo', 'label' => 'Datos de ejemplo (todavía no confirmados)', 'type' => 'true_false', 'ui' => 1, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Mientras esté marcado, la ficha NO se publica (aunque haya aceptado aparecer). Destildalo cuando reemplaces todos los datos por los reales.' ],
			[ 'key' => 'field_pm_r_consentimiento', 'name' => 'prest_consentimiento', 'label' => 'Aceptó aparecer en Pescamapa', 'type' => 'true_false', 'ui' => 1, 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_publico', 'name' => 'prest_publico', 'label' => 'Listado con información pública (todavía sin su permiso)', 'type' => 'true_false', 'ui' => 1, 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Se publica marcado como "sin verificar", con enlace para corregir o pedir la baja. Solo datos públicos y con nuestras palabras; fotos solo si las tenemos con permiso.' ],
			[ 'key' => 'field_pm_r_fuente', 'name' => 'prest_fuente', 'label' => 'Fuente de los datos (su web o publicación)', 'type' => 'url', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'No se muestra en la web (dueño, 17/11/2026): queda como registro interno. Anotá la fecha en "Datos verificados el".' ],
			[ 'key' => 'field_pm_r_verificado', 'name' => 'verificado', 'label' => 'Datos verificados el', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_listado_msg', 'label' => '', 'type' => 'message', 'message' => '<strong>Marca de listado oficial (se publica).</strong> Solo si el lugar figura en un listado de un organismo de turismo (gobierno), leído por nosotros. La tarjeta dice "En el listado de pesca de &lt;fuente&gt; (consultado el &lt;fecha&gt;)" y enlaza a la página oficial. Las tres partes son necesarias; si falta una, no se muestra. Reglas: <code>docs/prestadores.md</code>.' ],
			[ 'key' => 'field_pm_r_listado_fuente', 'name' => 'prest_listado_fuente', 'label' => 'Listado oficial: nombre de la fuente', 'type' => 'text', 'wrapper' => [ 'width' => 50 ], 'instructions' => 'Ej. "Turismo de Corrientes".' ],
			[ 'key' => 'field_pm_r_listado_fecha', 'name' => 'prest_listado_fecha', 'label' => 'Listado oficial: fecha de consulta', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_r_listado_url', 'name' => 'prest_listado_url', 'label' => 'Listado oficial: página del organismo', 'type' => 'url', 'instructions' => 'La página oficial donde figura (dominio de gobierno o del ente de turismo). Sin teléfonos ni datos de contacto.' ],
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

// Free-text fields shown on the card that exist in a version per language (Spanish is the base, the others fall back to it).
const PM_PREST_TEXTOS = [
	'descripcion' => [ 'meta' => 'descripcion', 'label' => 'En dos líneas', 'tipo' => 'textarea' ],
	'lancha'      => [ 'meta' => 'lancha', 'label' => 'Lancha', 'tipo' => 'text' ],
	'motor'       => [ 'meta' => 'motor', 'label' => 'Motor', 'tipo' => 'text' ],
	'temporada'   => [ 'meta' => 'prest_temporada', 'label' => 'Cuándo trabaja', 'tipo' => 'text' ],
	'precio'      => [ 'meta' => 'precio', 'label' => 'Precio de referencia', 'tipo' => 'text' ],
	'programa'    => [ 'meta' => 'prest_programa', 'label' => 'Programa', 'tipo' => 'text' ],
	'estadia'     => [ 'meta' => 'prest_estadia_minima', 'label' => 'Estadía mínima', 'tipo' => 'text' ],
	'acompanante' => [ 'meta' => 'prest_acompanante', 'label' => 'Acompañante que no pesca (con la moneda)', 'tipo' => 'text' ],
	'no_incluye'  => [ 'meta' => 'prest_no_incluye', 'label' => 'Qué no incluye', 'tipo' => 'text' ],
];

/** ACF tab with the pt/en version of each free-text field (empty = the Spanish text is shown, marked lang="es"). */
function pm_prestador_campos_traduccion(): array {
	$campos = [ [ 'key' => 'field_pm_r_tab_trad', 'label' => 'Traducciones (se publican)', 'type' => 'tab' ],
		[ 'key' => 'field_pm_r_trad_msg', 'label' => '', 'type' => 'message', 'message' => 'Versión en portugués e inglés de los textos libres. Si queda vacío, la ficha muestra el texto en español en esa versión. En el precio, escribí la moneda (ej. "From ARS 300,000 per outing").' ] ];
	foreach ( PM_PREST_TEXTOS as $nombre => $t ) {
		foreach ( [ 'pt' => 'portugués', 'en' => 'inglés' ] as $i => $idioma ) {
			$campos[] = [ 'key' => "field_pm_r_{$nombre}_{$i}", 'name' => "{$t['meta']}_{$i}", 'label' => "{$t['label']} ({$idioma})", 'type' => $t['tipo'], 'wrapper' => [ 'width' => 'textarea' === $t['tipo'] ? 100 : 50 ] ] + ( 'textarea' === $t['tipo'] ? [ 'rows' => 3 ] : [] );
		}
	}
	return $campos;
}

/** The text of a free field in the current language; falls back to Spanish and reports it. [ text, is_fallback ] */
function pm_prestador_texto( int $post_id, string $nombre ): array {
	$meta = PM_PREST_TEXTOS[ $nombre ]['meta'];
	$base = trim( (string) pm_campo( $meta, $post_id ) );
	$i    = pm_lang();
	if ( 'es' !== $i ) {
		$t = trim( (string) pm_campo( "{$meta}_{$i}", $post_id ) );
		if ( '' !== $t ) {
			return [ $t, false ];
		}
	}
	return [ $base, 'es' !== $i && '' !== $base ];
}

// --- Read model -----------------------------------------------------------------

function pm_prestador( int $post_id ): array {
	$lista = fn( $n ) => array_values( (array) ( pm_campo( $n, $post_id ) ?: [] ) );
	return [
		'id'             => $post_id,
		'nombre'         => get_the_title( $post_id ),
		'tipo'           => $lista( 'tipo' ),
		'destino'        => (string) pm_campo( 'prest_destino', $post_id ),
		'localidad'      => (string) pm_campo( 'localidad', $post_id ),
		'descripcion'    => pm_prestador_texto( $post_id, 'descripcion' )[0],
		'modalidades'    => $lista( 'prest_modalidades' ),
		'especies'       => $lista( 'prest_especies' ),
		'equipo'         => $lista( 'equipo' ),
		'lancha'         => pm_prestador_texto( $post_id, 'lancha' )[0],
		'motor'          => pm_prestador_texto( $post_id, 'motor' )[0],
		'capacidad'      => (int) pm_campo( 'capacidad', $post_id ),
		'capacidad_unidad' => in_array( $u = (string) pm_campo( 'prest_capacidad_unidad', $post_id ), [ 'semana', 'total' ], true ) ? $u : 'lancha',
		'temporada'      => pm_prestador_texto( $post_id, 'temporada' )[0],
		'programa'       => pm_prestador_texto( $post_id, 'programa' )[0],
		'estadia'        => pm_prestador_texto( $post_id, 'estadia' )[0],
		'acompanante'    => pm_prestador_texto( $post_id, 'acompanante' )[0],
		'no_incluye'     => pm_prestador_texto( $post_id, 'no_incluye' )[0],
		'idiomas'        => $lista( 'prest_idiomas' ),
		'accesible'      => (string) pm_campo( 'prest_accesible', $post_id ),
		'incluye'        => $lista( 'incluye' ),
		'precio'         => pm_prestador_texto( $post_id, 'precio' )[0],
		// Which of those texts are the Spanish fallback in a pt/en page (the card marks them lang="es").
		'en_espanol'     => array_keys( array_filter( array_combine( array_keys( PM_PREST_TEXTOS ), array_map( fn( $n ) => pm_prestador_texto( $post_id, $n )[1], array_keys( PM_PREST_TEXTOS ) ) ) ) ),
		'precio_fecha'   => (string) pm_campo( 'precio_fecha', $post_id ),
		'fotos'          => array_map( 'intval', $lista( 'fotos' ) ) ?: array_filter( [ (int) get_post_thumbnail_id( $post_id ) ] ),
		'consentimiento' => (bool) pm_campo( 'prest_consentimiento', $post_id ),
		// Raw meta: these two are read by the front end and llms.txt only.
		'oferta'         => (string) get_post_meta( $post_id, 'prest_oferta', true ),
		'acuerdo'        => 'acuerdo' === (string) get_post_meta( $post_id, 'estado', true ),
		'publico'        => (bool) pm_campo( 'prest_publico', $post_id ),
		'fuente'         => (string) pm_campo( 'prest_fuente', $post_id ),
		'ejemplo'        => (bool) pm_campo( 'datos_ejemplo', $post_id ),
		// Official listing mark: all three parts or nothing (pm_prestador_listado()).
		'listado'        => pm_prestador_listado_datos( $post_id ),
	];
}

/**
 * Official listing a card appears in (a tourism body of a province, e.g. "Turismo de Corrientes"): [ fuente, url, fecha ]
 * when the three are set, else []. The URL must be a page we may link (official or government domain).
 */
function pm_prestador_listado_datos( int $post_id ): array {
	$fuente = trim( (string) pm_campo( 'prest_listado_fuente', $post_id ) );
	$fecha  = (string) pm_campo( 'prest_listado_fecha', $post_id );
	$url    = trim( (string) pm_campo( 'prest_listado_url', $post_id ) );
	if ( '' === $fuente || '' === $fecha || '' === $url ) {
		return [];
	}
	return [ 'fuente' => $fuente, 'url' => $url, 'fecha' => $fecha ];
}

/** Escaped HTML of the listing sentence ("En el listado de pesca de <a>Turismo de Corrientes</a> (consultado el …)"); '' when the card has none. */
function pm_prestador_listado( array $r ): string {
	$l = $r['listado'] ?? [];
	if ( ! $l ) {
		return '';
	}
	$fuente = esc_html( $l['fuente'] );
	if ( pm_fuente_enlazable( [ 'url' => $l['url'], 'oficial' => false ] ) ) {
		$fuente = '<a href="' . esc_url( $l['url'] ) . '" rel="noopener">' . $fuente . '</a>';
	}
	return wp_kses( pm_t( 'prest.listado', [ 'fuente' => $fuente, 'fecha' => esc_html( pm_fecha( $l['fecha'] ) ) ] ), [ 'a' => [ 'href' => true, 'rel' => true ] ] );
}

/** The capacity sentence in the current language; the default unit ("lancha") is the original wording. */
function pm_prestador_capacidad( array $r ): string {
	if ( ! $r['capacidad'] ) {
		return '';
	}
	$clave = [ 'semana' => 'prest.capacidad.semana', 'total' => 'prest.capacidad.total' ][ $r['capacidad_unidad'] ?? 'lancha' ] ?? 'prest.capacidad';
	return pm_t( $clave, [ 'n' => (string) $r['capacidad'] ] );
}

/** Public: agreed to appear, or listed from public information; never example data. */
function pm_prestador_publico( array $r ): bool {
	return ( $r['consentimiento'] || $r['publico'] ) && ! $r['ejemplo'];
}

/**
 * The special condition for anglers who ask through Pescamapa: shown only for a
 * public outfitter we work with ("Trabajamos juntos"), so an offer nobody
 * agreed to never goes out. A short sentence a person, or an LLM, can repeat.
 */
function pm_prestador_oferta( array $r ): string {
	return ( pm_prestador_publico( $r ) && $r['acuerdo'] ) ? trim( $r['oferta'] ) : '';
}

/**
 * Outfitters of a destination. Visitors get only the public ones; editors
 * (logged in) also see the rest, marked as a preview, so the section can be
 * filled in place.
 */
function pm_prestadores_destino( string $destino, bool $con_borradores = false ): array {
	$out = [];
	foreach ( pm_posts( 'pm_prestador' ) as $p ) {
		$r = pm_prestador( $p->ID );
		if ( $r['destino'] === $destino && ( $con_borradores || pm_prestador_publico( $r ) ) ) {
			$out[] = $r;
		}
	}
	// Public cards first; among them the operators we work with (stated agreement + consent) go on top.
	$arriba = fn( $r ) => $r['acuerdo'] && $r['consentimiento'];
	usort( $out, fn( $a, $b ) => [ pm_prestador_publico( $b ), $arriba( $b ) ] <=> [ pm_prestador_publico( $a ), $arriba( $a ) ] );
	return $out;
}

/**
 * A price text cut for the short answer (20/11/2026): up to the first sentence end or opening parenthesis. A period between two digits
 * ("US$ 9.990") is part of the figure, not a sentence end. Trailing punctuation is trimmed; very long texts are cut at 110 characters.
 */
function pm_precio_corto( string $precio ): string {
	$partes = preg_split( '/\s*\(|(?<!\d)\.(?!\d)/u', trim( $precio ), 2 );
	$corto  = rtrim( (string) ( $partes[0] ?? '' ), " \t\n\r,;:." );
	return mb_strlen( $corto ) > 110 ? rtrim( mb_substr( $corto, 0, 107 ), " ,;:." ) . '…' : $corto;
}

/**
 * Is this card a weekly lodge programme? Rule (documented in docs/prestadores.md, "Preguntas de la guía por ciudad"): the card's capacity unit is
 * "semana", or its programme, minimum stay, companion rate or price text names a week (semana / week; "semanas completas", "per week", "por semana").
 */
function pm_prestador_semanal( array $r ): bool {
	if ( 'semana' === ( $r['capacidad_unidad'] ?? '' ) ) {
		return true;
	}
	foreach ( [ 'programa', 'estadia', 'acompanante', 'precio' ] as $k ) {
		if ( preg_match( '/\b(semanas?|weeks?|week-long|s[áa]bado a s[áa]bado|saturday to saturday)\b/iu', (string) ( $r[ $k ] ?? '' ) ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Does the city page talk about lodges by the week (true) or about boat and day outings (false)? Lodge version when at least one public card is a weekly
 * programme and the weekly cards are at least as many as the cards that describe a boat (a boat text, or a capacity per boat). Mixed or no data: the
 * boat and day version, which is the historical one.
 * @param array<int, array<string, mixed>> $prestadores pm_prestadores_destino() rows
 */
function pm_guias_es_lodge( array $prestadores ): bool {
	$semanales = 0;
	$lanchas   = 0;
	foreach ( $prestadores as $r ) {
		if ( pm_prestador_semanal( $r ) ) {
			++$semanales;
		} elseif ( ! empty( $r['lancha'] ) || ( 'lancha' === ( $r['capacidad_unidad'] ?? '' ) && ! empty( $r['capacidad'] ) ) ) {
			++$lanchas;
		}
	}
	return $semanales > 0 && $semanales >= $lanchas;
}

/** The first card with a dated price, as "Place: short price (checked …)" in plain text for a FAQ answer; '' when none has one. */
function pm_guias_ejemplo_precio( array $prestadores, string $idioma ): string {
	foreach ( $prestadores as $r ) {
		if ( ! empty( $r['precio'] ) && ! empty( $r['precio_fecha'] ) ) {
			return ( $r['nombre'] ?? '' ) . ': ' . pm_t( 'guias.resumen.precio', [ 'precio' => pm_precio_corto( (string) $r['precio'] ), 'cuando' => pm_fecha_relativa( (string) $r['precio_fecha'], $idioma ) ], $idioma );
		}
	}
	return '';
}

/** Most recent check date (Y-m-d) among the public cards: "verificado" or the price date. '' when none has one. */
function pm_prestadores_consultada( array $prestadores ): string {
	$max = '';
	foreach ( $prestadores as $r ) {
		if ( ! pm_prestador_publico( $r ) ) {
			continue; // editor-only previews do not count
		}
		$max = max( $max, (string) pm_campo( 'verificado', $r['id'] ), (string) $r['precio_fecha'] );
	}
	return $max;
}

// Admin list: where, what, state, consent.
add_filter( 'manage_pm_prestador_posts_columns', function ( $cols ) {
	return [ 'cb' => $cols['cb'], 'title' => 'Nombre', 'pm_donde' => 'Dónde', 'pm_tipo' => 'Qué ofrece', 'pm_estado' => 'Estado', 'pm_visible' => 'En la web', 'pm_revisado' => 'Revisado', 'date' => $cols['date'] ];
} );
add_action( 'manage_pm_prestador_posts_custom_column', function ( $col, $id ) {
	$r = pm_prestador( $id );
	if ( 'pm_donde' === $col ) {
		echo esc_html( $r['destino'] ?: $r['localidad'] );
	} elseif ( 'pm_tipo' === $col ) {
		echo esc_html( implode( ', ', array_map( fn( $t ) => pm_opcion( 'tipo', $t, 'es' ), $r['tipo'] ) ) );
	} elseif ( 'pm_estado' === $col ) {
		echo esc_html( (string) pm_campo( 'estado', $id ) );
	} elseif ( 'pm_revisado' === $col ) {
		// Internal: when the data (or the price) was last checked and whether a new check is due (90 days).
		$f = max( (string) pm_campo( 'verificado', $id ), $r['precio_fecha'] );
		if ( $f ) {
			$dias = (int) floor( ( time() - strtotime( $f ) ) / DAY_IN_SECONDS );
			echo esc_html( pm_fecha( $f, 'es' ) . " ({$dias} d)" . ( $dias > PM_PREST_REVISION_DIAS ? ' ⚠ revisar' : '' ) );
		} else {
			echo esc_html( '— sin fecha' );
		}
	} elseif ( 'pm_visible' === $col ) {
		echo $r['ejemplo'] ? esc_html( '🧪 datos de ejemplo' ) : ( $r['consentimiento'] ? '✅' : ( $r['publico'] ? esc_html( '🌐 listado público (sin verificar)' ) : esc_html( '— no se publica' ) ) );
	}
}, 10, 2 );
