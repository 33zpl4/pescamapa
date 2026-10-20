<?php
/**
 * Plugin Name: Pescamapa — núcleo
 * Description: Tipos de contenido (destinos, especies, próximos destinos, entrevistas, prestadores, consultas), idiomas, URLs, reglas de publicación, SEO y créditos de fotos de Pescamapa. Versionado en git: no se edita desde el panel.
 * Version: 1.0.0
 * Requires PHP: 8.1
 * Requires Plugins: secure-custom-fields
 * Text Domain: pescamapa
 *
 * Data lives in WordPress; behaviour lives here, in git. The theme only
 * renders (wordpress/themes/pescamapa). Field names keep the ones the Astro
 * site used, so the content model reads the same in both.
 */

defined( 'ABSPATH' ) || exit;

const PM_VERSION = '1.0.0';
define( 'PM_DIR', plugin_dir_path( __FILE__ ) );
define( 'PM_URL', plugin_dir_url( __FILE__ ) );

require PM_DIR . 'inc/i18n.php';
require PM_DIR . 'inc/tipos.php';
require PM_DIR . 'inc/campos.php';
require PM_DIR . 'inc/datos.php';
require PM_DIR . 'inc/rutas.php';
require PM_DIR . 'inc/reglas.php';
require PM_DIR . 'inc/fotos.php';
require PM_DIR . 'inc/mapa.php';
require PM_DIR . 'inc/seo.php';
require PM_DIR . 'inc/admin.php';
require PM_DIR . 'inc/editor.php';
require PM_DIR . 'inc/entrevistas.php';
require PM_DIR . 'inc/prestadores.php';
require PM_DIR . 'inc/consultas.php';

register_activation_hook( __FILE__, function () {
	pm_registrar_tipos();
	pm_registrar_rutas();
	pm_crear_rol_editor();
	flush_rewrite_rules( false );
} );

register_deactivation_hook( __FILE__, function () {
	flush_rewrite_rules( false );
} );
