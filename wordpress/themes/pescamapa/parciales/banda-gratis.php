<?php
/**
 * Slim mid-page reminder (owner, 20/11/2026): the service is free for the angler. One per page, after the middle section of a long
 * guide (destination guide: after the regulations; city page: after the provider list). The button goes to the form on the same page
 * (#consulta); the cta microcopy is left out here because the sentence already says it.
 */
defined( 'ABSPATH' ) || exit;
?>
<aside class="banda-gratis" aria-label="<?php echo esc_attr( pm_t( 'cta.gratis' ) ); ?>">
	<p><span class="gratis-pill"><?php pm_e( 'cta.gratis' ); ?></span> <?php pm_e( 'banda.p' ); ?></p>
	<a class="button dark" href="#consulta"><?php pm_e( 'contraste.cta.pedir' ); ?></a>
</aside>
