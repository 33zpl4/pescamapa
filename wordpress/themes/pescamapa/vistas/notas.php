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
			<a class="tarjeta" href="<?php echo esc_url( $n['url'] ); ?>"<?php echo $n['idioma'] !== $idioma ? ' hreflang="' . esc_attr( pm_meta_idioma( $n['idioma'] )['html'] ) . '"' : ''; ?>>
				<?php echo pm_foto( $n['foto'], $n['titulo'], '(max-width: 720px) 100vw, 360px', [ 'enlaces' => false ] ); // phpcs:ignore ?>
				<?php if ( $n['tipo'] ) : ?><span class="quien"><?php echo esc_html( pm_opcion( 'nota_tipo', $n['tipo'], $n['idioma'] ) ); ?></span><?php endif; ?>
				<strong><?php echo esc_html( $n['titulo'] ); ?></strong>
				<span class="note"><?php echo esc_html( pm_fecha( $n['fecha'], $n['idioma'] ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div>
<?php get_footer();
