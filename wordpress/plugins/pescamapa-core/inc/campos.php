<?php
/**
 * Editor forms, registered in code with Secure Custom Fields (the
 * WordPress.org fork of ACF, repeaters included). Field names match the
 * Astro content model. Fields shared by every language (region, province,
 * location, species, photo) only show on the Spanish version; translations
 * inherit them (inc/datos.php).
 */

defined( 'ABSPATH' ) || exit;

/** Read a field with SCF when present, raw meta otherwise. */
function pm_campo( string $nombre, int $post_id ) {
	if ( function_exists( 'get_field' ) ) {
		return get_field( $nombre, $post_id );
	}
	return get_post_meta( $post_id, $nombre, true );
}

function pm_opciones_regiones(): array {
	return array_map( fn( $r ) => $r['es'], PM_REGIONES );
}

const PM_SOLO_ES = [ [ [ 'field' => 'field_pm_d_idioma', 'operator' => '==', 'value' => 'es' ] ] ];

function pm_campos_comunes( string $tipo ): array {
	$p = "field_pm_{$tipo}_";
	return [
		[
			'key'           => $p . 'chequeo',
			'name'          => '',
			'label'         => 'Para completar la ficha',
			'type'          => 'message',
			'message'       => '',
			'new_lines'     => '',
		],
		[
			'key'           => $p . 'idioma',
			'name'          => 'idioma',
			'label'         => 'Idioma',
			'type'          => 'select',
			'choices'       => [ 'es' => 'Español', 'pt' => 'Português', 'en' => 'English' ],
			'default_value' => 'es',
			'wrapper'       => [ 'width' => 50 ],
		],
		[
			'key'          => $p . 'clave',
			'name'         => 'clave',
			'label'        => 'Clave',
			'type'         => 'text',
			'instructions' => 'Identificador igual en los tres idiomas, en minúsculas y sin tildes (ej. goya, paso-de-la-patria). Une las traducciones.',
			'wrapper'      => [ 'width' => 50 ],
		],
	];
}

function pm_campo_fuentes( string $tipo ): array {
	return [
		'key'          => "field_pm_{$tipo}_fuentes",
		'name'         => 'fuentes',
		'label'        => 'Fuentes',
		'type'         => 'repeater',
		'layout'       => 'table',
		'button_label' => 'Agregar fuente',
		'sub_fields'   => [
			[ 'key' => "field_pm_{$tipo}_fu_nombre", 'name' => 'nombre', 'label' => 'Nombre', 'type' => 'text' ],
			[ 'key' => "field_pm_{$tipo}_fu_url", 'name' => 'url', 'label' => 'Enlace', 'type' => 'url' ],
			[ 'key' => "field_pm_{$tipo}_fu_oficial", 'name' => 'oficial', 'label' => '¿Oficial?', 'type' => 'true_false', 'ui' => 1 ],
			[ 'key' => "field_pm_{$tipo}_fu_consultada", 'name' => 'consultada', 'label' => 'Consultada', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d' ],
		],
	];
}

function pm_campos_seo( string $tipo ): array {
	return [
		[
			'key'          => "field_pm_{$tipo}_seo_title",
			'name'         => 'seo_title',
			'label'        => 'Título para Google',
			'type'         => 'text',
			'maxlength'    => 65,
			'instructions' => 'Máximo 65 caracteres.',
		],
		[
			'key'          => "field_pm_{$tipo}_seo_description",
			'name'         => 'seo_description',
			'label'        => 'Descripción para Google',
			'type'         => 'textarea',
			'rows'         => 3,
			'maxlength'    => 160,
			'instructions' => 'Entre 70 y 160 caracteres: lo que aparece debajo del título en Google.',
		],
	];
}

add_action( 'acf/include_fields', function () {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$en_es = PM_SOLO_ES;

	// --- Guías de destino -----------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_pm_destino',
		'title'    => 'Datos de la guía',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'pm_destino' ] ] ],
		'position' => 'acf_after_title',
		'style'    => 'default',
		'fields'   => array_merge(
			pm_campos_comunes( 'd' ),
			[
				[
					'key'   => 'field_pm_d_tab_comun',
					'label' => 'Ubicación, especies y foto (valen para los tres idiomas)',
					'type'  => 'tab',
					'conditional_logic' => $en_es,
				],
				[
					'key'          => 'field_pm_d_comun_msg',
					'label'        => '',
					'type'         => 'message',
					'message'      => 'Estos datos se cargan <strong>solo en la versión en español</strong> y valen para los tres idiomas. La <strong>foto</strong> es la "Imagen destacada" (columna derecha) de la versión en español.',
					'conditional_logic' => $en_es,
				],
				[ 'key' => 'field_pm_d_region', 'name' => 'region', 'label' => 'Región', 'type' => 'select', 'choices' => pm_opciones_regiones(), 'conditional_logic' => $en_es, 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_d_provincia', 'name' => 'provincia', 'label' => 'Provincia', 'type' => 'select', 'choices' => PM_PROVINCIAS, 'conditional_logic' => $en_es, 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_d_lat', 'name' => 'lat', 'label' => 'Latitud (ej. -29.14)', 'type' => 'number', 'step' => 'any', 'min' => -56, 'max' => -21, 'conditional_logic' => $en_es, 'instructions' => 'Clic derecho en Google Maps sobre el lugar → el primer número.', 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_d_lon', 'name' => 'lon', 'label' => 'Longitud (ej. -59.26)', 'type' => 'number', 'step' => 'any', 'min' => -74, 'max' => -53, 'conditional_logic' => $en_es, 'instructions' => 'El segundo número.', 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_d_alturas_url', 'name' => 'alturas_url', 'label' => 'Enlace a la altura del río (oficial, se actualiza solo)', 'type' => 'url', 'conditional_logic' => $en_es, 'instructions' => 'Ej. la página de alturas de Prefectura o del INA para este puerto. Aparece arriba, junto a la fecha.' ],
				[ 'key' => 'field_pm_d_especies', 'name' => 'especies', 'label' => 'Especies', 'type' => 'checkbox', 'choices' => [], 'layout' => 'horizontal', 'conditional_logic' => $en_es, 'instructions' => 'Las opciones salen de las Especies cargadas en español.' ],

				[ 'key' => 'field_pm_d_tab_textos', 'label' => 'Textos', 'type' => 'tab' ],
				[ 'key' => 'field_pm_d_h1', 'name' => 'h1', 'label' => 'Titular de la página', 'type' => 'text' ],
				[ 'key' => 'field_pm_d_bajada', 'name' => 'bajada', 'label' => 'Subtítulo (también va en la tarjeta del inicio)', 'type' => 'textarea', 'rows' => 2 ],
				[ 'key' => 'field_pm_d_modalidades', 'name' => 'modalidades', 'label' => 'Modalidades (una por línea)', 'type' => 'textarea', 'rows' => 4 ],
				[ 'key' => 'field_pm_d_epoca', 'name' => 'epoca', 'label' => 'Cuándo ir (una línea)', 'type' => 'text' ],
			],
			pm_campos_seo( 'd' ),
			[
				[ 'key' => 'field_pm_d_autor', 'name' => 'autor', 'label' => 'Autor', 'type' => 'text', 'default_value' => 'Equipo Pescamapa', 'wrapper' => [ 'width' => 50 ] ],
				[ 'key' => 'field_pm_d_actualizado', 'name' => 'actualizado', 'label' => 'Última actualización', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 50 ] ],

				[ 'key' => 'field_pm_d_tab_agua', 'label' => 'Aguas y calendario', 'type' => 'tab' ],
				[
					'key' => 'field_pm_d_aguas', 'name' => 'aguas', 'label' => 'Aguas (ríos, lagunas, zonas)', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar agua',
					'sub_fields' => [
						[ 'key' => 'field_pm_d_ag_nombre', 'name' => 'nombre', 'label' => 'Nombre', 'type' => 'text' ],
						[ 'key' => 'field_pm_d_ag_nota', 'name' => 'nota', 'label' => 'Descripción', 'type' => 'textarea', 'rows' => 2 ],
						[ 'key' => 'field_pm_d_ag_lat', 'name' => 'lat', 'label' => 'Latitud (opcional; solo zonas generales, nunca un pesquero exacto)', 'type' => 'number', 'step' => 'any', 'wrapper' => [ 'width' => 50 ] ],
						[ 'key' => 'field_pm_d_ag_lon', 'name' => 'lon', 'label' => 'Longitud (opcional)', 'type' => 'number', 'step' => 'any', 'wrapper' => [ 'width' => 50 ] ],
					],
				],
				[
					'key' => 'field_pm_d_temporada', 'name' => 'temporada', 'label' => 'Calendario', 'type' => 'repeater', 'layout' => 'table', 'button_label' => 'Agregar época',
					'sub_fields' => [
						[ 'key' => 'field_pm_d_te_meses', 'name' => 'meses', 'label' => 'Época', 'type' => 'text' ],
						[ 'key' => 'field_pm_d_te_que', 'name' => 'que', 'label' => 'Qué esperar', 'type' => 'textarea', 'rows' => 2 ],
					],
				],

				[ 'key' => 'field_pm_d_tab_reglamento', 'label' => 'Reglamento', 'type' => 'tab' ],
				[ 'key' => 'field_pm_d_reg_autoridad', 'name' => 'reglamento_autoridad', 'label' => 'Autoridad', 'type' => 'text' ],
				[ 'key' => 'field_pm_d_reg_url', 'name' => 'reglamento_url', 'label' => 'Enlace oficial', 'type' => 'url' ],
				[ 'key' => 'field_pm_d_reg_verificado', 'name' => 'reglamento_verificado', 'label' => 'Reglamento verificado el (opcional)', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'instructions' => 'Opcional. Si alguien contrastó el reglamento con la autoridad, poné la fecha: la guía muestra "Reglamento verificado el…". Si no, dejalo vacío.' ],
				[ 'key' => 'field_pm_d_reg_resumen', 'name' => 'reglamento_resumen', 'label' => 'Resumen', 'type' => 'textarea', 'rows' => 4 ],

				[ 'key' => 'field_pm_d_tab_faqs', 'label' => 'Preguntas y fuentes', 'type' => 'tab' ],
				[
					'key' => 'field_pm_d_faqs', 'name' => 'faqs', 'label' => 'Preguntas frecuentes', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar pregunta',
					'sub_fields' => [
						[ 'key' => 'field_pm_d_fq_pregunta', 'name' => 'pregunta', 'label' => 'Pregunta', 'type' => 'text' ],
						[ 'key' => 'field_pm_d_fq_respuesta', 'name' => 'respuesta', 'label' => 'Respuesta', 'type' => 'textarea', 'rows' => 3 ],
					],
				],
				pm_campo_fuentes( 'd' ),

				[ 'key' => 'field_pm_d_tab_mas', 'label' => 'Más para hacer', 'type' => 'tab' ],
				[ 'key' => 'field_pm_d_mas_msg', 'label' => '', 'type' => 'message', 'message' => 'Enlaces a otras cosas para hacer en la zona (excursiones, paseos, otros sitios). Salen al final de la guía, en una sección discreta. Si alguien <strong>paga</strong> por figurar, tildá "Es publicidad": la web lo marca como tal y el enlace va con <code>rel="sponsored"</code>.' ],
				[
					'key' => 'field_pm_d_mas', 'name' => 'mas', 'label' => 'Más para hacer', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar enlace',
					'sub_fields' => [
						[ 'key' => 'field_pm_d_mas_nombre', 'name' => 'nombre', 'label' => 'Nombre', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
						[ 'key' => 'field_pm_d_mas_url', 'name' => 'url', 'label' => 'Enlace', 'type' => 'url', 'wrapper' => [ 'width' => 50 ] ],
						[ 'key' => 'field_pm_d_mas_texto', 'name' => 'texto', 'label' => 'Una línea que lo describa', 'type' => 'text', 'maxlength' => 160 ],
						[ 'key' => 'field_pm_d_mas_pago', 'name' => 'patrocinado', 'label' => 'Es publicidad (pagan por figurar)', 'type' => 'true_false', 'ui' => 1, 'wrapper' => [ 'width' => 50 ] ],
					],
				],
				[ 'key' => 'field_pm_d_tab_paquetes', 'label' => 'Paquetes', 'type' => 'tab' ],
				[ 'key' => 'field_pm_d_paq_msg', 'label' => '', 'type' => 'message', 'message' => 'Opciones de pesca que Pescamapa arma en este destino (página "Paquetes de pesca en…"). No se nombra al guía. <strong>Si una opción está tildada "Publicada" y no tiene precio, la página dice que se cotiza al pedirla.</strong> El precio "desde" solo se muestra si están el monto, la moneda, la unidad (por lancha, por persona) y la <strong>fecha</strong> del precio. Cargá los datos en cada idioma.', 'new_lines' => 'br', 'esc_html' => 0 ],
				[ 'key' => 'field_pm_d_paq_intro', 'name' => 'paquetes_intro', 'label' => 'Introducción de la página (opcional; si queda vacío, va un texto general)', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'Dos o tres líneas con lo que de verdad se ofrece acá (modalidades, especies, packs). Es lo primero que lee quien llega.' ],
				[ 'key' => 'field_pm_d_paq_desc', 'name' => 'paquetes_description', 'label' => 'Descripción para Google (opcional, hasta 160 caracteres)', 'type' => 'text', 'maxlength' => 160 ],
				[
					'key' => 'field_pm_d_paquetes', 'name' => 'paquetes', 'label' => 'Paquetes', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar paquete',
					'sub_fields' => [
						[ 'key' => 'field_pm_d_paq_publicado', 'name' => 'publicado', 'label' => 'Publicada', 'type' => 'true_false', 'ui' => 1, 'wrapper' => [ 'width' => 20 ] ],
						[ 'key' => 'field_pm_d_paq_titulo', 'name' => 'titulo', 'label' => 'Título', 'type' => 'text', 'wrapper' => [ 'width' => 80 ] ],
						[ 'key' => 'field_pm_d_paq_foto', 'name' => 'foto', 'label' => 'Foto (opcional)', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail', 'library' => 'all', 'instructions' => 'El autor y el origen se cargan en la foto (Medios). Sale con el crédito sobre la imagen.' ],
						[ 'key' => 'field_pm_d_paq_resumen', 'name' => 'resumen', 'label' => 'De qué se trata (2 o 3 líneas)', 'type' => 'textarea', 'rows' => 3 ],
						[ 'key' => 'field_pm_d_paq_modalidad', 'name' => 'modalidad', 'label' => 'Modalidad', 'type' => 'text', 'wrapper' => [ 'width' => 34 ] ],
						[ 'key' => 'field_pm_d_paq_duracion', 'name' => 'duracion', 'label' => 'Duración', 'type' => 'text', 'wrapper' => [ 'width' => 33 ] ],
						[ 'key' => 'field_pm_d_paq_capacidad', 'name' => 'capacidad', 'label' => 'Capacidad', 'type' => 'text', 'wrapper' => [ 'width' => 33 ] ],
						[ 'key' => 'field_pm_d_paq_incluye', 'name' => 'incluye', 'label' => 'Qué incluye (una línea cada cosa; vacío si no está confirmado)', 'type' => 'textarea', 'rows' => 3, 'wrapper' => [ 'width' => 50 ] ],
						[ 'key' => 'field_pm_d_paq_no_incluye', 'name' => 'no_incluye', 'label' => 'Qué no incluye (una línea cada cosa)', 'type' => 'textarea', 'rows' => 3, 'wrapper' => [ 'width' => 50 ] ],
						[ 'key' => 'field_pm_d_paq_variantes', 'name' => 'variantes', 'label' => 'Variantes (una por línea, p. ej. los packs)', 'type' => 'textarea', 'rows' => 3 ],
						[ 'key' => 'field_pm_d_paq_monto', 'name' => 'desde_monto', 'label' => 'Precio desde (monto)', 'type' => 'number', 'min' => 0, 'step' => 'any', 'wrapper' => [ 'width' => 25 ] ],
						[ 'key' => 'field_pm_d_paq_moneda', 'name' => 'desde_moneda', 'label' => 'Moneda', 'type' => 'select', 'choices' => [ 'ARS' => 'Pesos (ARS)', 'USD' => 'Dólares (USD)', 'BRL' => 'Reales (BRL)' ], 'allow_null' => 1, 'wrapper' => [ 'width' => 25 ] ],
						[ 'key' => 'field_pm_d_paq_unidad', 'name' => 'desde_unidad', 'label' => 'Por (ej. "por lancha", "por persona")', 'type' => 'text', 'wrapper' => [ 'width' => 25 ] ],
						[ 'key' => 'field_pm_d_paq_fecha', 'name' => 'desde_fecha', 'label' => 'Fecha del precio', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 25 ] ],
						[ 'key' => 'field_pm_d_paq_interno', 'name' => 'interno', 'label' => 'Interno (no se publica): qué guías pueden armarlo, condiciones, comisión', 'type' => 'textarea', 'rows' => 2 ],
					],
				],
				[
					'key' => 'field_pm_d_paq_info', 'name' => 'paquetes_info', 'label' => 'Antes de viajar (bloques con fuente, en la página de paquetes)', 'type' => 'repeater', 'layout' => 'block', 'button_label' => 'Agregar bloque',
					'instructions' => 'Documentos, cómo llegar, qué llevar, condiciones. Cada bloque lleva su fuente y la fecha en que se consultó. Si no hay bloques, la sección no aparece.',
					'sub_fields' => [
						[ 'key' => 'field_pm_d_pi_titulo', 'name' => 'titulo', 'label' => 'Título', 'type' => 'text' ],
						[ 'key' => 'field_pm_d_pi_texto', 'name' => 'texto', 'label' => 'Texto (un párrafo por línea)', 'type' => 'textarea', 'rows' => 5 ],
						[ 'key' => 'field_pm_d_pi_fuente', 'name' => 'fuente_nombre', 'label' => 'Fuente (nombre)', 'type' => 'text', 'wrapper' => [ 'width' => 40 ] ],
						[ 'key' => 'field_pm_d_pi_url', 'name' => 'fuente_url', 'label' => 'Fuente (enlace)', 'type' => 'url', 'wrapper' => [ 'width' => 40 ] ],
						[ 'key' => 'field_pm_d_pi_fecha', 'name' => 'consultada', 'label' => 'Consultada el', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d', 'wrapper' => [ 'width' => 20 ] ],
					],
				],
			]
		),
	] );

	// --- Especies ---------------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_pm_especie',
		'title'    => 'Datos de la especie',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'pm_especie' ] ] ],
		'position' => 'acf_after_title',
		'fields'   => array_merge(
			pm_campos_comunes( 'e' ),
			[
				[ 'key' => 'field_pm_e_cientifico', 'name' => 'cientifico', 'label' => 'Nombre científico', 'type' => 'text' ],
				[ 'key' => 'field_pm_e_resumen', 'name' => 'resumen', 'label' => 'Resumen (subtítulo)', 'type' => 'textarea', 'rows' => 2 ],
			],
			pm_campos_seo( 'e' ),
			[
				[ 'key' => 'field_pm_e_actualizado', 'name' => 'actualizado', 'label' => 'Última actualización', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d' ],
				pm_campo_fuentes( 'e' ),
			]
		),
	] );

	// --- Próximos destinos -------------------------------------------------------
	acf_add_local_field_group( [
		'key'      => 'group_pm_proximo',
		'title'    => 'Próximo destino (vale para los tres idiomas)',
		'location' => [ [ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'pm_proximo' ] ] ],
		'position' => 'acf_after_title',
		'fields'   => [
			[ 'key' => 'field_pm_p_msg', 'label' => '', 'type' => 'message', 'message' => 'El <strong>título</strong> es el nombre en español. La <strong>foto</strong> es la "Imagen destacada". El "slug" (enlace permanente) es la clave: cuando se publica una guía con la misma clave, esta tarjeta se oculta sola.' ],
			[ 'key' => 'field_pm_p_nombre_pt', 'name' => 'nombre_pt', 'label' => 'Nombre en portugués (si cambia)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_p_nombre_en', 'name' => 'nombre_en', 'label' => 'Nombre en inglés (si cambia)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_p_provincia', 'name' => 'provincia', 'label' => 'Provincia', 'type' => 'select', 'choices' => PM_PROVINCIAS, 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_p_region', 'name' => 'region', 'label' => 'Región', 'type' => 'select', 'choices' => pm_opciones_regiones(), 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_p_agua', 'name' => 'agua', 'label' => 'Río, lago o zona (español)', 'type' => 'text' ],
			[ 'key' => 'field_pm_p_agua_pt', 'name' => 'agua_pt', 'label' => 'Río/zona en portugués (si cambia)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_p_agua_en', 'name' => 'agua_en', 'label' => 'Río/zona en inglés (si cambia)', 'type' => 'text', 'wrapper' => [ 'width' => 50 ] ],
			[ 'key' => 'field_pm_p_especies', 'name' => 'especies', 'label' => 'Especies y modalidades', 'type' => 'checkbox', 'choices' => array_map( fn( $e ) => $e['es'], pm_ui()['etiquetas'] ), 'layout' => 'horizontal' ],
			[ 'key' => 'field_pm_p_lat', 'name' => 'lat', 'label' => 'Latitud (ej. -27.32)', 'type' => 'number', 'step' => 'any', 'min' => -56, 'max' => -21, 'instructions' => 'Clic derecho en Google Maps sobre la ciudad → el primer número.', 'wrapper' => [ 'width' => 33 ] ],
			[ 'key' => 'field_pm_p_lon', 'name' => 'lon', 'label' => 'Longitud (ej. -58.57)', 'type' => 'number', 'step' => 'any', 'min' => -74, 'max' => -53, 'instructions' => 'El segundo número.', 'wrapper' => [ 'width' => 33 ] ],
			[ 'key' => 'field_pm_p_orden', 'name' => 'orden', 'label' => 'Orden (1, 2, 3…)', 'type' => 'number', 'default_value' => 99, 'wrapper' => [ 'width' => 33 ] ],
		],
	] );

	// --- Créditos de foto (en la biblioteca de medios) -------------------------
	acf_add_local_field_group( [
		'key'      => 'group_pm_foto',
		'title'    => 'Crédito de la foto',
		'location' => [ [ [ 'param' => 'attachment', 'operator' => '==', 'value' => 'all' ] ] ],
		'fields'   => [
			[ 'key' => 'field_pm_f_autor', 'name' => 'foto_autor', 'label' => 'Autor de la foto', 'type' => 'text', 'instructions' => 'Quién la sacó. En la web sale "Foto: Nombre Apellido".' ],
			[ 'key' => 'field_pm_f_origen', 'name' => 'foto_origen', 'label' => 'Origen', 'type' => 'select', 'choices' => [ 'propia' => 'Propia (la sacaste vos)', 'cedida' => 'Cedida con permiso del autor', 'licencia' => 'Licencia libre (Wikimedia, etc.)' ], 'allow_null' => 1 ],
			[ 'key' => 'field_pm_f_licencia', 'name' => 'foto_licencia', 'label' => 'Licencia (solo si es libre, ej. CC BY-SA 4.0)', 'type' => 'text' , 'conditional_logic' => [ [ [ 'field' => 'field_pm_f_origen', 'operator' => '==', 'value' => 'licencia' ] ] ] ],
			[ 'key' => 'field_pm_f_licencia_url', 'name' => 'foto_licencia_url', 'label' => 'Enlace a la licencia', 'type' => 'url' , 'conditional_logic' => [ [ [ 'field' => 'field_pm_f_origen', 'operator' => '==', 'value' => 'licencia' ] ] ] ],
			[ 'key' => 'field_pm_f_pagina', 'name' => 'foto_pagina', 'label' => 'Página de origen de la foto', 'type' => 'url' , 'conditional_logic' => [ [ [ 'field' => 'field_pm_f_origen', 'operator' => '==', 'value' => 'licencia' ] ] ] ],
		],
	] );
} );

// Species choices come from the Spanish species posts.
add_filter( 'acf/load_field/key=field_pm_d_especies', function ( $field ) {
	$field['choices'] = [];
	foreach ( pm_especies_lista( 'es' ) as $e ) {
		$field['choices'][ $e['clave'] ] = $e['nombre'];
	}
	return $field;
} );

// The "publication check" message lists what is missing for this post.
foreach ( [ 'field_pm_d_chequeo', 'field_pm_e_chequeo', 'field_pm_t_chequeo' ] as $clave_campo ) {
	add_filter( "acf/prepare_field/key={$clave_campo}", function ( $field ) {
		$post_id = (int) ( $_GET['post'] ?? 0 );
		if ( ! $post_id ) {
			$field['message'] = 'Guardá la ficha; acá vas a ver qué conviene completar. Nada de esto bloquea la publicación ni se muestra en la web.';
			return $field;
		}
		$faltan = pm_sugerencias( $post_id );
		$field['message'] = $faltan
			? '<strong>Para que la ficha esté completa conviene:</strong><ul style="margin:.5em 0 0 1.2em;list-style:disc">' . implode( '', array_map( fn( $f ) => '<li>' . esc_html( $f ) . '</li>', $faltan ) ) . '</ul><p style="margin-top:.5em;opacity:.7">Es solo una guía interna: la ficha se publica igual y nada de esto aparece en la web.</p>'
			: '✅ Ficha completa.';
		return $field;
	} );
}
