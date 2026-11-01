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
			<p class="firma"><?php pm_e( 'pie.firma' ); ?> <span>Cheyenne Justiniano</span> <?php pm_e( 'pie.y' ); ?> <span>Alberto Ayala</span></p>
		</div>
		<nav aria-label="Pie">
			<a href="<?php echo esc_url( $inicio . '#destinos' ); ?>"><?php pm_e( 'nav.destinos' ); ?></a>
			<a href="<?php echo esc_url( pm_url_mapa( $idioma ) ); ?>"><?php pm_e( 'nav.mapa' ); ?></a>
			<a href="<?php echo esc_url( pm_url_pique( $idioma ) ); ?>"><?php pm_e( 'nav.pique' ); ?></a>
			<a href="<?php echo esc_url( pm_url_entrevistas( $idioma ) ); ?>"><?php pm_e( 'nav.entrevistas' ); ?></a>
			<?php if ( pm_hay_notas() ) : ?><a href="<?php echo esc_url( pm_url_notas( $idioma ) ); ?>"><?php pm_e( 'nav.notas' ); ?></a><?php endif; ?>
			<a href="<?php echo esc_url( pm_url_rio( $idioma ) ); ?>"><?php pm_e( 'nav.rio' ); ?></a>
			<a href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'nav.viaje' ); ?></a>
			<a href="<?php echo esc_url( pm_url_calendario( $idioma ) ); ?>"><?php pm_e( 'cal.eyebrow' ); ?></a>
			<a href="<?php echo esc_url( pm_url_paraguias( $idioma ) ); ?>"><?php pm_e( 'pie.prestadores' ); ?></a>
		</nav>
	</div>
	<div class="shell">
		<p class="legal"><?php pm_e( 'pie.legal' ); ?> © <?php echo esc_html( gmdate( 'Y' ) ); ?> Pescamapa <span class="pie-legal-enlaces"><a href="<?php echo esc_url( pm_url_terminos( $idioma ) ); ?>"><?php pm_e( 'legal.terminos.corto' ); ?></a><a href="<?php echo esc_url( pm_url_privacidad( $idioma ) ); ?>"><?php pm_e( 'legal.privacidad.corto' ); ?></a></span></p>
	</div>
</footer>
<script>
// Mobile menu: the header links used to be hidden below 760 px with nothing to open them.
(() => {
	const h = document.querySelector('header.nav'), b = h && h.querySelector('.menu-btn');
	if (!b) return;
	const set = (abierto) => { h.classList.toggle('menu-abierto', abierto); b.setAttribute('aria-expanded', abierto ? 'true' : 'false'); };
	b.addEventListener('click', () => set(b.getAttribute('aria-expanded') !== 'true'));
	document.addEventListener('keydown', (e) => { if (e.key === 'Escape') set(false); });
	h.querySelectorAll('#menu-principal a').forEach((a) => a.addEventListener('click', () => set(false)));
})();
</script>
<?php
// Browser in another of our languages: suggest that version, never redirect
// (search engines crawl without a language; hreflang does the steering).
$sugerencias = [];
foreach ( pm_seo()['alternas'] as $i => $url ) {
	if ( $i !== $idioma ) {
		$sugerencias[ $i ] = [ 'url' => $url, 'texto' => pm_t( 'idioma.sugerencia', [], $i ) ];
	}
}
if ( $sugerencias ) : ?>
<script>
(() => {
	const alt = <?php echo wp_json_encode( $sugerencias, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ); ?>;
	let visto = null;
	try { visto = localStorage.getItem('pm-idioma-visto'); } catch (e) {}
	if (visto) return;
	const pref = (navigator.languages || [navigator.language]).map((l) => (l || '').slice(0, 2).toLowerCase());
	const i = pref.find((l) => l === '<?php echo esc_js( $idioma ); ?>' || alt[l]);
	if (!i || !alt[i]) return;
	const bar = document.createElement('div');
	bar.className = 'sugerencia-idioma';
	bar.lang = i;
	bar.innerHTML = '<a></a><button type="button" aria-label="×">×</button>';
	bar.querySelector('a').href = alt[i].url;
	bar.querySelector('a').textContent = alt[i].texto;
	const cerrar = () => { try { localStorage.setItem('pm-idioma-visto', '1'); } catch (e) {} };
	bar.querySelector('a').addEventListener('click', cerrar);
	bar.querySelector('button').addEventListener('click', () => { cerrar(); bar.remove(); });
	document.body.append(bar);
})();
</script>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
