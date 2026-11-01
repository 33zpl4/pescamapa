<?php
/** Home, shared by /, /pt/ and /en/. */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$destinos = pm_destinos_lista( $idioma );
$proximos = pm_proximos_lista( $idioma );

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
					<a class="button primary" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'inicio.armar' ); ?></a>
					<a class="button secondary" href="#destinos"><?php pm_e( 'inicio.explorar' ); ?></a>
				</div>
				<ul class="stats">
					<li><strong><?php pm_e( 'inicio.stat1a' ); ?></strong><?php pm_e( 'inicio.stat1b' ); ?></li>
					<li><strong><?php pm_e( 'inicio.stat2a' ); ?></strong><?php pm_e( 'inicio.stat2b' ); ?></li>
					<li><strong><?php pm_e( 'inicio.stat3a' ); ?></strong><?php pm_e( 'inicio.stat3b' ); ?></li>
				</ul>
			</div>
			<div class="hero-map">
				<?php echo pm_mapa_hero( $puntos_hero, pm_t( 'inicio.hero.mapa' ) ); // phpcs:ignore -- escaped inside ?>
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

	<?php $notas_inicio = array_slice( pm_notas_lista( $idioma ), 0, 3 ); ?>
	<?php if ( $notas_inicio ) : ?>
	<section class="section notas-inicio" id="notas">
		<div class="shell">
			<div class="head">
				<div>
					<div class="eyebrow"><?php pm_e( 'inicio.notas.eyebrow' ); ?></div>
					<h2><?php pm_e( 'inicio.notas.h2' ); ?></h2>
				</div>
				<a class="button dark" href="<?php echo esc_url( pm_url_notas( $idioma ) ); ?>"><?php pm_e( 'inicio.notas.cta' ); ?></a>
			</div>
			<div class="lista">
				<?php foreach ( $notas_inicio as $n ) : ?>
					<a class="tarjeta" href="<?php echo esc_url( $n['url'] ); ?>"<?php echo $n['idioma'] !== $idioma ? ' hreflang="' . esc_attr( pm_meta_idioma( $n['idioma'] )['html'] ) . '"' : ''; ?>>
						<?php echo pm_foto( $n['foto'], $n['titulo'], '(max-width: 720px) 100vw, 360px', [ 'enlaces' => false ] ); // phpcs:ignore ?>
						<strong><?php echo esc_html( $n['titulo'] ); ?></strong>
						<span class="note"><?php echo esc_html( pm_fecha( $n['fecha'], $n['idioma'] ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

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
				<p><a href="<?php echo esc_url( pm_url_paraguias( $idioma ) ); ?>"><?php pm_e( 'inicio.prest.pagina' ); ?></a></p>
			</div>
			<div id="consulta" class="consulta">
				<?php if ( 'ok' === pm_consulta_resultado() ) : ?>
					<p class="aviso ok" role="status"><?php pm_e( 'consulta.ok' ); ?></p>
				<?php else : ?>
					<?php if ( 'error' === pm_consulta_resultado() ) : ?><p class="aviso error" role="alert"><?php pm_e( 'consulta.error' ); ?></p><?php endif; ?>
					<form method="post" action="<?php echo esc_url( pm_consulta_url_envio() ); ?>">
						<?php echo pm_consulta_ocultos( 'prestador' ); // phpcs:ignore -- escaped inside ?>
						<div class="grilla">
							<label><?php pm_e( 'consulta.nombre' ); ?> <input name="nombre" required maxlength="80" autocomplete="name"></label>
							<label><?php pm_e( 'consulta.contacto' ); ?> <input name="contacto" required maxlength="120" autocomplete="tel"></label>
							<label><?php pm_e( 'inicio.prest.localidad' ); ?> <input name="localidad" maxlength="80"></label>
							<label><?php pm_e( 'inicio.prest.ofrece' ); ?> <input name="ofrece" maxlength="200"></label>
						</div>
						<button class="button primary" type="submit"><?php pm_e( 'inicio.prest.enviar' ); ?></button>
						<p class="note"><?php pm_e( 'inicio.prest.privacidad' ); ?> <?php echo pm_enlace_privacidad(); // phpcs:ignore -- escaped inside ?></p>
					</form>
				<?php endif; ?>
			</div>
		</div>
	</section>
</div>
<?php get_footer();
