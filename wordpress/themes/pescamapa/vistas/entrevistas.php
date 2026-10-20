<?php
/** Interviews index, newest first. */
defined( 'ABSPATH' ) || exit;

$idioma      = pm_lang();
$entrevistas = pm_entrevistas_lista( $idioma );

get_header(); ?>
<div class="v-destino v-entrevistas">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'entrevistas.eyebrow' ); ?></div>
			<h1><?php pm_e( 'entrevistas.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'entrevistas.description' ); ?></p>
		</div>
	</header>
	<div class="shell lista">
		<?php if ( ! $entrevistas ) : ?>
			<p><?php pm_e( 'entrevistas.vacio' ); ?></p>
		<?php endif; ?>
		<?php foreach ( $entrevistas as $e ) : ?>
			<a class="tarjeta" href="<?php echo esc_url( $e['url'] ); ?>"<?php echo $e['idioma'] !== $idioma ? ' hreflang="' . esc_attr( pm_meta_idioma( $e['idioma'] )['html'] ) . '"' : ''; ?>>
				<?php echo pm_foto( $e['foto'], $e['persona'], '(max-width: 720px) 100vw, 360px', [ 'enlaces' => false ] ); // phpcs:ignore ?>
				<span class="quien"><?php echo esc_html( $e['persona'] . ' · ' . $e['rol'] ); ?></span>
				<strong><?php echo esc_html( $e['titulo'] ); ?></strong>
				<span class="note"><?php echo esc_html( pm_fecha( $e['fecha'] ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>
</div>
<?php get_footer();
