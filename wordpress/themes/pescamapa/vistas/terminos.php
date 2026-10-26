<?php
/** Terms of use: text in data/legal.json. */
defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'parciales/legal', null, [ 'doc' => 'terminos' ] );
get_footer();
