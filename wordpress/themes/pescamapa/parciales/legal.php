<?php
/** A legal document (terms or privacy policy). Args: doc. */
defined( 'ABSPATH' ) || exit;

$l = pm_legal( $args['doc'] );
?>
<div class="v-legal">
	<div class="shell wrap">
		<h1><?php echo esc_html( $l['h1'] ?? '' ); ?></h1>
		<p class="note"><?php pm_e( 'legal.actualizado' ); ?> <time datetime="<?php echo esc_attr( $l['actualizado'] ?? '' ); ?>"><?php echo esc_html( pm_fecha( $l['actualizado'] ?? '' ) ); ?></time></p>
		<?php foreach ( $l['secciones'] ?? [] as $sec ) : ?>
			<h2><?php echo esc_html( $sec['h'] ); ?></h2>
			<?php foreach ( $sec['p'] ?? [] as $p ) : ?><p><?php echo pm_legal_texto( $p ); // phpcs:ignore -- escaped inside ?></p><?php endforeach; ?>
			<?php if ( ! empty( $sec['li'] ) ) : ?>
				<ul><?php foreach ( $sec['li'] as $li ) : ?><li><?php echo pm_legal_texto( $li ); // phpcs:ignore -- escaped inside ?></li><?php endforeach; ?></ul>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>
