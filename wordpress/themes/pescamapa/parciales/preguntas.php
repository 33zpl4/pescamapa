<?php
/**
 * "What we ask each place for you": the same questions Pescamapa puts to every place and guide that fits a request.
 * Reusable block (city guides page, trip form). $args: id (heading id), nivel ('h2' | 'h3', default h2).
 * Text in ui.json (preguntas.*), three languages; the closing line carries the 48 hours the site already promises (viaje.garantia).
 */
defined( 'ABSPATH' ) || exit;

$id    = (string) ( $args['id'] ?? 'preguntas-h' );
$nivel = $args['nivel'] ?? 'h2';
$nivel = in_array( $nivel, [ 'h2', 'h3' ], true ) ? $nivel : 'h2';
?>
<section class="elegir preguntas-lugar" aria-labelledby="<?php echo esc_attr( $id ); ?>">
	<<?php echo $nivel; // phpcs:ignore -- whitelisted above ?> id="<?php echo esc_attr( $id ); ?>"><?php pm_e( 'preguntas.h' ); ?></<?php echo $nivel; // phpcs:ignore -- whitelisted above ?>>
	<p class="intro"><?php pm_e( 'preguntas.p' ); ?></p>
	<ol>
		<?php for ( $n = 1; $n <= 6; $n++ ) : ?>
			<li><strong><?php pm_e( "preguntas.$n.t" ); ?></strong><?php pm_e( "preguntas.$n.d" ); ?></li>
		<?php endfor; ?>
	</ol>
	<p class="cierre"><?php pm_e( 'preguntas.cierre' ); ?></p>
</section>
