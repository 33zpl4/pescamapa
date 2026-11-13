<?php
/**
 * Phone-only bar fixed to the bottom that leads to the quote form (long guides and notes: the form is many screens down).
 * $args: url (where it goes), hasta (CSS selector; the bar hides while that element is on screen).
 */
defined( 'ABSPATH' ) || exit;
?>
<a class="barra-pedir" href="<?php echo esc_url( $args['url'] ); ?>"><?php pm_e( 'viaje.barra' ); ?></a>
<script>
(() => {
	const barra = document.querySelector('.barra-pedir'), hasta = document.querySelector(<?php echo wp_json_encode( $args['hasta'] ); ?>);
	if (barra && hasta && 'IntersectionObserver' in window) {
		new IntersectionObserver((e) => barra.classList.toggle('oculta', e[0].isIntersecting)).observe(hasta);
	}
})();
</script>
