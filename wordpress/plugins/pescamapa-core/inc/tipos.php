<?php
/**
 * Content types. URLs are built by inc/rutas.php, so every type registers
 * with rewrite => false.
 */

defined( 'ABSPATH' ) || exit;

const PM_REGIONES = [
	'litoral'      => [ 'es' => 'Litoral', 'pt' => 'Litoral', 'en' => 'Litoral' ],
	'patagonia'    => [ 'es' => 'Patagonia', 'pt' => 'Patagônia', 'en' => 'Patagonia' ],
	'pampa'        => [ 'es' => 'Pampa y lagunas', 'pt' => 'Pampa e lagoas', 'en' => 'Pampas and lagoons' ],
	'costa'        => [ 'es' => 'Costa atlántica', 'pt' => 'Costa atlântica', 'en' => 'Atlantic coast' ],
	'centro-norte' => [ 'es' => 'Centro y norte', 'pt' => 'Centro e norte', 'en' => 'Centre and north' ],
];

const PM_PROVINCIAS = [
	'buenos-aires'     => 'Buenos Aires',
	'catamarca'        => 'Catamarca',
	'chaco'            => 'Chaco',
	'chubut'           => 'Chubut',
	'cordoba'          => 'Córdoba',
	'corrientes'       => 'Corrientes',
	'entre-rios'       => 'Entre Ríos',
	'formosa'          => 'Formosa',
	'jujuy'            => 'Jujuy',
	'la-pampa'         => 'La Pampa',
	'la-rioja'         => 'La Rioja',
	'mendoza'          => 'Mendoza',
	'misiones'         => 'Misiones',
	'neuquen'          => 'Neuquén',
	'rio-negro'        => 'Río Negro',
	'salta'            => 'Salta',
	'san-juan'         => 'San Juan',
	'san-luis'         => 'San Luis',
	'santa-cruz'       => 'Santa Cruz',
	'santa-fe'         => 'Santa Fe',
	'santiago-del-estero' => 'Santiago del Estero',
	'tierra-del-fuego' => 'Tierra del Fuego',
	'tucuman'          => 'Tucumán',
];

/** Types written per language (one post per language, shared `clave`). */
const PM_TIPOS_IDIOMA = [ 'pm_destino', 'pm_especie', 'pm_entrevista' ];

function pm_registrar_tipos(): void {
	register_post_type( 'pm_destino', [
		'labels'       => [
			'name'          => 'Guías de destino',
			'singular_name' => 'Guía de destino',
			'add_new_item'  => 'Nueva guía de destino',
			'edit_item'     => 'Editar guía de destino',
			'all_items'     => 'Todas las guías',
			'menu_name'     => 'Destinos',
		],
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-location-alt',
		'menu_position' => 5,
		'supports'     => [ 'title', 'editor', 'thumbnail', 'revisions' ],
		'has_archive'  => false,
		'rewrite'      => false,
		'query_var'    => 'pm_destino',
	] );

	register_post_type( 'pm_especie', [
		'labels'       => [
			'name'          => 'Especies',
			'singular_name' => 'Especie',
			'add_new_item'  => 'Nueva especie',
			'edit_item'     => 'Editar especie',
			'menu_name'     => 'Especies',
		],
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-palmtree',
		'menu_position' => 6,
		'supports'     => [ 'title', 'editor', 'revisions' ],
		'has_archive'  => false,
		'rewrite'      => false,
		'query_var'    => false,
	] );

	register_post_type( 'pm_proximo', [
		'labels'       => [
			'name'          => 'Próximos destinos',
			'singular_name' => 'Próximo destino',
			'add_new_item'  => 'Nuevo próximo destino',
			'edit_item'     => 'Editar próximo destino',
			'menu_name'     => 'Próximos destinos',
		],
		'description'  => 'Tarjetas "Próximamente" del inicio y puntos grises del mapa. Un formulario para los tres idiomas.',
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-flag',
		'menu_position' => 7,
		'supports'     => [ 'title', 'thumbnail' ],
		'rewrite'      => false,
	] );

	register_post_type( 'pm_entrevista', [
		'labels'       => [
			'name'          => 'Entrevistas',
			'singular_name' => 'Entrevista',
			'add_new_item'  => 'Nueva entrevista',
			'edit_item'     => 'Editar entrevista',
			'menu_name'     => 'Entrevistas',
		],
		'description'  => 'Charlas con guías, prestadores y pescadores locales. Las citas aparecen también en la guía del destino.',
		'public'       => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-format-quote',
		'menu_position' => 8,
		'supports'     => [ 'title', 'editor', 'thumbnail', 'revisions' ],
		'has_archive'  => false,
		'rewrite'      => false,
		'query_var'    => 'pm_entrevista',
	] );

	// Internal: never rendered on its own; published fichas show a card in
	// the destination guide, without contact data (inquiries come to us).
	register_post_type( 'pm_prestador', [
		'labels'       => [
			'name'          => 'Prestadores',
			'singular_name' => 'Prestador',
			'add_new_item'  => 'Nuevo prestador',
			'edit_item'     => 'Editar prestador',
			'menu_name'     => 'Prestadores',
		],
		'description'  => 'Guías, alojamientos y operadores. El contacto es interno: las consultas de la web llegan a Pescamapa.',
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-groups',
		'menu_position' => 9,
		'supports'     => [ 'title', 'thumbnail', 'revisions' ],
		'rewrite'      => false,
	] );

	// Inquiries sent from the site's forms (inc/consultas.php). Created only by
	// the form; editors read them and track their state.
	register_post_type( 'pm_consulta', [
		'labels'       => [
			'name'          => 'Consultas',
			'singular_name' => 'Consulta',
			'edit_item'     => 'Consulta',
			'menu_name'     => 'Consultas',
			'not_found'     => 'Todavía no llegaron consultas.',
		],
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => false,
		'menu_icon'    => 'dashicons-email-alt',
		'menu_position' => 4,
		'supports'     => [ 'title' ],
		'capabilities' => [ 'create_posts' => 'do_not_allow' ],
		'map_meta_cap' => true,
		'rewrite'      => false,
	] );
}
add_action( 'init', 'pm_registrar_tipos' );

/** Content editors: write and publish content, nothing else. */
function pm_crear_rol_editor(): void {
	remove_role( 'pescamapa_editor' );
	add_role( 'pescamapa_editor', 'Editor de Pescamapa', [
		'read'                   => true,
		'upload_files'           => true,
		'edit_posts'             => true,
		'edit_others_posts'      => true,
		'edit_published_posts'   => true,
		'publish_posts'          => true,
		'delete_posts'           => true,
		'delete_others_posts'    => true,
		'delete_published_posts' => true,
		'read_private_posts'     => true,
		'edit_private_posts'     => true,
		'delete_private_posts'   => true,
	] );
}
