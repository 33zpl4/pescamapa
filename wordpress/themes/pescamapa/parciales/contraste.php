<?php
/**
 * "Today / With Pescamapa" contrast block (owner, 18/11/2026; redesigned 19/11/2026): sells the utility (one inquiry instead of ten),
 * the number is the hook. Dimmed "today" box (10 inquiries, three crosses) vs. forest "with Pescamapa" box (1 inquiry, three ticks).
 * The "with" column doubles as the how-it-works steps (19/11/2026: numbered, one phrase each), so the trip page has no separate steps block.
 * Shared by the home and the trip request page.
 * Args: `id` (heading id, unique per page), `nivel` (heading tag, h2 by default),
 * `cta` ('pedir' = button to the request form, default; 'form' = link to the #consulta anchor of the form on the same page).
 */
defined( 'ABSPATH' ) || exit;

$id    = sanitize_key( $args['id'] ?? 'contraste' );
$nivel = $args['nivel'] ?? 'h2';
$nivel = in_array( $nivel, [ 'h2', 'h3' ], true ) ? $nivel : 'h2';
$cta   = 'form' === ( $args['cta'] ?? '' ) ? 'form' : 'pedir';
$href  = 'form' === $cta ? '#consulta' : pm_url_viaje( pm_lang() );
?>
<div class="contraste" role="group" aria-labelledby="<?php echo esc_attr( $id ); ?>">
	<<?php echo $nivel; ?> id="<?php echo esc_attr( $id ); ?>"><?php pm_e( 'contraste.h' ); ?></<?php echo $nivel; ?>>
	<div class="contraste-par">
		<article class="hoy">
			<p class="rotulo"><?php pm_e( 'contraste.hoy.t' ); ?></p>
			<p class="gancho"><?php pm_e( 'contraste.hoy.n' ); ?></p>
			<ul>
				<?php foreach ( [ 1, 2, 3 ] as $n ) : ?>
					<li><span class="marca" aria-hidden="true">✗</span><span><?php pm_e( "contraste.hoy.$n" ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		</article>
		<span class="flecha" aria-hidden="true">↓</span>
		<article class="con">
			<p class="rotulo"><?php pm_e( 'contraste.con.t' ); ?></p>
			<p class="gancho"><?php pm_e( 'contraste.con.n' ); ?></p>
			<ol>
				<?php foreach ( [ 1, 2, 3 ] as $n ) : ?>
					<li><span class="marca" aria-hidden="true"><?php echo (int) $n; ?></span><span><strong><?php pm_e( "contraste.con.$n" ); ?></strong><small><?php pm_e( "contraste.con.$n.d" ); ?></small></span></li>
				<?php endforeach; ?>
			</ol>
		</article>
		<p class="contraste-cta"><a class="button primary" href="<?php echo esc_url( $href ); ?>"><?php pm_e( 'form' === $cta ? 'contraste.cta.form' : 'contraste.cta.pedir' ); ?></a></p>
	</div>
</div>
