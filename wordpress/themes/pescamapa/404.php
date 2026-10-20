<?php
/** One 404 for every language: short and trilingual. */
defined( 'ABSPATH' ) || exit;
get_header(); ?>
<div class="shell v-404" style="padding-block: 80px">
	<h1>Acá no pica nada.</h1>
	<p>La página que buscás no existe o cambió de dirección.</p>
	<p lang="pt-BR">Aqui não tem peixe: esta página não existe. <a href="<?php echo esc_url( pm_url_inicio( 'pt' ) ); ?>">Ir para o início</a></p>
	<p lang="en">Nothing biting here: this page doesn’t exist. <a href="<?php echo esc_url( pm_url_inicio( 'en' ) ); ?>">Go to the home page</a></p>
	<p><a class="button dark" href="<?php echo esc_url( pm_url_inicio( 'es' ) ); ?>">Volver al inicio</a> <a class="button dark" href="<?php echo esc_url( pm_url_mapa( 'es' ) ); ?>">Abrir el mapa</a></p>
</div>
<?php get_footer();
