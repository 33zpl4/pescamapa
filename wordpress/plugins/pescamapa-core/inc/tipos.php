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
	] );
}
