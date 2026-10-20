<?php
/** Photo credits: /creditos/, /pt/creditos/, /en/credits/. */
defined( 'ABSPATH' ) || exit;

get_header(); ?>
<div class="v-creditos">
	<div class="shell wrap">
		<h1><?php pm_e( 'creditos.h1' ); ?></h1>
		<p><?php pm_e( 'creditos.p' ); ?></p>
		<ul>
			<?php foreach ( pm_fotos_con_credito() as $c ) : ?>
				<li>
					<?php if ( $c['pagina'] ) : ?><a href="<?php echo esc_url( $c['pagina'] ); ?>" rel="noopener"><?php echo esc_html( $c['archivo'] ); ?></a><?php else : echo esc_html( $c['archivo'] ); endif; ?>,
					<?php pm_e( 'creditos.de' ); ?> <?php echo esc_html( $c['autor'] ); ?>.
					<?php if ( $c['licencia'] ) : ?>
						<?php pm_e( 'creditos.licencia' ); ?>: <?php if ( $c['licencia_url'] ) : ?><a href="<?php echo esc_url( $c['licencia_url'] ); ?>" rel="noopener license"><?php echo esc_html( $c['licencia'] ); ?></a><?php else : echo esc_html( $c['licencia'] ); endif; ?>.
						<span class="note"><?php pm_e( 'creditos.editada' ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php get_footer();
