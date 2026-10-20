<?php
defined( 'ABSPATH' ) || exit;
$idioma = pm_lang();
$inicio = pm_url_inicio( $idioma );
?>
</main>
<footer>
	<div class="shell inner">
		<div>
			<a class="brand" href="<?php echo esc_url( $inicio ); ?>">PESCAMAPA</a>
			<p><?php pm_e( 'marca.lema' ); ?></p>
			<p class="firma"><?php pm_e( 'pie.firma' ); ?> <span>Cheyenne Justiniano</span></p>
		</div>
		<nav aria-label="Pie">
			<a href="<?php echo esc_url( $inicio . '#destinos' ); ?>"><?php pm_e( 'nav.destinos' ); ?></a>
			<a href="<?php echo esc_url( pm_url_mapa( $idioma ) ); ?>"><?php pm_e( 'nav.mapa' ); ?></a>
			<a href="<?php echo esc_url( $inicio . '#prestadores' ); ?>"><?php pm_e( 'pie.prestadores' ); ?></a>
			<a href="<?php echo esc_url( pm_url_creditos( $idioma ) ); ?>"><?php pm_e( 'nav.creditos' ); ?></a>
		</nav>
	</div>
	<div class="shell">
		<p class="legal"><?php pm_e( 'pie.legal' ); ?> © <?php echo esc_html( gmdate( 'Y' ) ); ?> Pescamapa</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
