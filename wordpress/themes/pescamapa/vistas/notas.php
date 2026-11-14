<?php
/** Notes index, newest first. */
defined( 'ABSPATH' ) || exit;

$idioma = pm_lang();
$notas  = pm_notas_lista( $idioma );

get_header(); ?>
<div class="v-destino v-notas">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'notas.eyebrow' ); ?></div>
			<h1><?php pm_e( 'notas.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'notas.description' ); ?></p>
		</div>
	</header>
	<div class="shell lista">
		<?php if ( ! $notas ) : ?>
			<p><?php pm_e( 'notas.vacio' ); ?></p>
		<?php endif; ?>
		<?php foreach ( $notas as $n ) : ?>
			<?php get_template_part( 'parciales/nota-tarjeta', null, [ 'nota' => $n, 'idioma' => $idioma, 'tipo' => true ] ); ?>
		<?php endforeach; ?>
	</div>
</div>
<?php get_footer();
