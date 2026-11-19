<?php
/**
 * "Today / With Pescamapa" contrast block (owner, 18/11/2026): sells the utility (one inquiry instead of ten), not a label.
 * Shared by the home and the trip request page. Args: `id` (heading id, unique per page), `nivel` (heading tag, h2 by default).
 */
defined( 'ABSPATH' ) || exit;

$id    = sanitize_key( $args['id'] ?? 'contraste' );
$nivel = $args['nivel'] ?? 'h2';
$nivel = in_array( $nivel, [ 'h2', 'h3' ], true ) ? $nivel : 'h2';
?>
<div class="contraste" role="group" aria-labelledby="<?php echo esc_attr( $id ); ?>">
	<<?php echo $nivel; ?> id="<?php echo esc_attr( $id ); ?>"><?php pm_e( 'contraste.h' ); ?></<?php echo $nivel; ?>>
	<div class="contraste-par">
		<article class="hoy">
			<p class="rotulo"><?php pm_e( 'contraste.hoy.t' ); ?></p>
			<p><?php pm_e( 'contraste.hoy.p' ); ?></p>
		</article>
		<article class="con">
			<p class="rotulo"><?php pm_e( 'contraste.con.t' ); ?></p>
			<p><?php pm_e( 'contraste.con.p' ); ?></p>
		</article>
	</div>
</div>
