<?php
/** Privacy policy: text in data/legal.json. */
defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'parciales/legal', null, [ 'doc' => 'privacidad' ] );
get_footer();
