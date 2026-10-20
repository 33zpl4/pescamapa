<?php
/** Home, shared by /, /pt/ and /en/. */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$destinos = pm_destinos_lista( $idioma );
$proximos = pm_proximos_lista( $idioma );
$primera  = $destinos[0] ?? null;

$puntos_hero = [];
foreach ( $proximos as $p ) {
	$puntos_hero[] = [ 'nombre' => $p['nombre'], 'coordenadas' => $p['coordenadas'] ];
}
foreach ( $destinos as $d ) {
	$puntos_hero[] = [ 'nombre' => $d['nombre'], 'coordenadas' => $d['coordenadas'], 'url' => $d['url'], 'destacado' => true ];
}
$pins = [];
foreach ( $destinos as $d ) {
	$pins[] = [ 'nombre' => $d['nombre'], 'coordenadas' => $d['coordenadas'], 'url' => $d['url'], 'detalle' => PM_PROVINCIAS[ $d['provincia'] ] ?? '', 'tipo' => 'destino' ];
}
foreach ( $proximos as $p ) {
	$pins[] = [ 'nombre' => $p['nombre'], 'coordenadas' => $p['coordenadas'], 'detalle' => $p['provincia'], 'tipo' => 'proximo' ];
}

get_header(); ?>
<div class="v-inicio">
	<section class="hero on-dark">
		<?php echo pm_curvas(); // phpcs:ignore -- generated SVG ?>
		<div class="shell hero-grid">
			<div class="hero-text">
				<div class="eyebrow"><?php pm_e( 'inicio.eyebrow' ); ?></div>
				<h1><?php pm_e( 'inicio.h1a' ); ?><br><?php pm_e( 'inicio.h1b' ); ?><br><em><?php pm_e( 'inicio.h1c' ); ?></em></h1>
				<p class="copy"><?php pm_e( 'inicio.copy' ); ?></p>
				<div class="actions">
					<a class="button primary" href="#destinos"><?php pm_e( 'inicio.explorar' ); ?></a>
					<a class="button secondary" href="<?php echo esc_url( pm_url_mapa( $idioma ) ); ?>"><?php pm_e( 'inicio.vermapa' ); ?></a>
				</div>
				<ul class="stats">
					<li><strong><?php pm_e( 'inicio.stat1a' ); ?></strong><?php pm_e( 'inicio.stat1b' ); ?></li>
					<li><strong><?php pm_e( 'inicio.stat2a' ); ?></strong><?php pm_e( 'inicio.stat2b' ); ?></li>
					<li><strong><?php pm_e( 'inicio.stat3a' ); ?></strong><?php pm_e( 'inicio.stat3b' ); ?></li>
				</ul>
			</div>
			<div class="hero-map">
				<?php echo pm_mapa_hero( $puntos_hero, pm_t( 'inicio.hero.mapa' ) ); // phpcs:ignore -- escaped inside ?>
				<div class="tag tag-top"><strong><?php pm_e( 'inicio.tag1.h' ); ?></strong><?php pm_e( 'inicio.tag1.p' ); ?></div>
				<?php if ( $primera ) : ?>
					<a class="tag tag-bottom" href="<?php echo esc_url( $primera['url'] ); ?>"><strong><?php pm_e( 'inicio.tag2.h' ); ?></strong><?php pm_e( 'inicio.tag2.p' ); ?></a>
				<?php endif; ?>
				<span class="map-note"><?php pm_e( 'inicio.hero.nota' ); ?></span>
			</div>
		</div>
	</section>

	<div class="strip">
		<div class="shell strip-inner">
			<span>✳ <?php pm_e( 'inicio.strip1' ); ?></span>
			<span>✳ <?php pm_e( 'inicio.strip2' ); ?></span>
			<span>✳ <?php pm_e( 'inicio.strip3' ); ?></span>
		</div>
	</div>

	<section id="destinos" class="section">
		<div class="shell">
			<div class="head">
				<div>
					<div class="eyebrow"><?php pm_e( 'inicio.destinos.eyebrow' ); ?></div>
					<h2><?php pm_e( 'inicio.destinos.h2' ); ?></h2>
				</div>
				<p><?php pm_e( 'inicio.destinos.p' ); ?></p>
			</div>
			<div class="grid">
				<?php foreach ( $destinos as $d ) : ?>
					<a class="card live" href="<?php echo esc_url( $d['url'] ); ?>" hreflang="<?php echo esc_attr( pm_meta_idioma( $d['idioma'] )['html'] ); ?>">
						<div class="img river">
							<?php echo pm_foto( $d['foto'], $d['nombre'], '(max-width: 700px) 100vw, 380px', [ 'clase' => 'cover', 'prioridad' => true, 'enlaces' => false ] ); // phpcs:ignore ?>
							<span class="pill"><?php echo esc_html( pm_pill( $d['region'], $d['provincia'] ) ); ?></span>
						</div>
						<div class="body">
							<h3><?php echo esc_html( $d['nombre'] ); ?></h3>
							<p><?php echo esc_html( $d['bajada'] ); ?></p>
							<ul class="chips">
								<?php foreach ( $d['especies'] as $clave ) :
									$e = pm_especie_en( $clave, $idioma ); ?>
									<li><?php echo esc_html( $e['nombre'] ?? $clave ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div class="foot"><span><?php pm_e( 'inicio.guia' ); ?></span><span class="arrow" aria-hidden="true">↗</span></div>
					</a>
				<?php endforeach; ?>
				<?php foreach ( $proximos as $p ) : ?>
					<article class="card soon">
						<div class="img <?php echo esc_attr( $p['region'] ); ?>">
							<?php echo pm_foto( $p['foto'], "{$p['nombre']}, {$p['provincia']}", '(max-width: 700px) 100vw, 380px', [ 'clase' => 'cover' ] ); // phpcs:ignore ?>
							<span class="pill"><?php echo esc_html( mb_strtoupper( ( PM_REGIONES[ $p['region'] ][ $idioma ] ?? '' ) . ' · ' . $p['provincia'] ) ); ?></span>
						</div>
						<div class="body">
							<div class="loc"><?php echo esc_html( $p['agua'] ); ?></div>
							<h3><?php echo esc_html( $p['nombre'] ); ?></h3>
							<ul class="chips">
								<?php foreach ( $p['especies'] as $e ) : ?>
									<li><?php echo esc_html( pm_etiqueta( $e ) ); ?></li>
								<?php endforeach; ?>
							</ul>
						</div>
						<div class="foot"><span><?php pm_e( 'inicio.proximamente' ); ?></span></div>
					</article>
				<?php endforeach; ?>
			</div>
			<p class="note"><?php pm_e( 'inicio.nota' ); ?></p>
		</div>
	</section>

	<section class="section map-section">
		<div class="shell map-grid">
			<div>
				<div class="eyebrow"><?php pm_e( 'inicio.mapa.eyebrow' ); ?></div>
				<h2><?php pm_e( 'inicio.mapa.h2' ); ?></h2>
				<p><?php pm_e( 'inicio.mapa.p' ); ?></p>
				<a class="button dark" href="<?php echo esc_url( pm_url_mapa( $idioma ) ); ?>"><?php pm_e( 'inicio.mapa.cta' ); ?></a>
			</div>
			<?php echo pm_mapa_interactivo( $pins, pm_t( 'mapa.etiqueta' ), [ 'alto' => '460px' ] ); // phpcs:ignore ?>
		</div>
	</section>

	<section class="section">
		<div class="shell">
			<div class="head">
				<div>
					<div class="eyebrow"><?php pm_e( 'inicio.prop.eyebrow' ); ?></div>
					<h2><?php pm_e( 'inicio.prop.h2' ); ?></h2>
				</div>
			</div>
			<div class="purpose">
				<?php foreach ( [ 1, 2, 3 ] as $n ) : ?>
					<article><div class="num"><?php pm_e( "inicio.prop{$n}.n" ); ?></div><h3><?php pm_e( "inicio.prop{$n}.h" ); ?></h3><p><?php pm_e( "inicio.prop{$n}.p" ); ?></p></article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section id="prestadores" class="section join">
		<div class="shell join-inner">
			<div>
				<div class="eyebrow"><?php pm_e( 'inicio.prest.eyebrow' ); ?></div>
				<h2><?php pm_e( 'inicio.prest.h2' ); ?></h2>
				<p><?php pm_e( 'inicio.prest.p' ); ?></p>
			</div>
		</div>
	</section>
</div>
<?php get_footer();
