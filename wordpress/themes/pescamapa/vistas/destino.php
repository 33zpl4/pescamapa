<?php
/** Destination guide, any language (the post carries its language). */
defined( 'ABSPATH' ) || exit;

$d         = pm_destino( get_queried_object_id() );
$idioma    = $d['idioma'];
$provincia = PM_PROVINCIAS[ $d['provincia'] ] ?? $d['provincia'];
$region    = PM_REGIONES[ $d['region'] ][ $idioma ] ?? $d['region'];
$migas     = [
	[ pm_t( 'ficha.inicio' ), pm_url_inicio( $idioma ) ],
	[ $region, null ],
	[ $provincia, null ],
	[ $d['nombre'], $d['url'] ],
];
$citas       = pm_citas_destino( $d['clave'], $idioma );
$prestadores = pm_prestadores_destino( $d['clave'] );
$pins = [ [ 'nombre' => $d['nombre'], 'coordenadas' => $d['coordenadas'], 'tipo' => 'destino', 'detalle' => $provincia ] ];
foreach ( $d['aguas'] as $a ) {
	if ( $a['coordenadas'] ) {
		$pins[] = [ 'nombre' => $a['nombre'], 'coordenadas' => $a['coordenadas'], 'tipo' => 'agua' ];
	}
}

get_header(); ?>
<div class="v-destino">
	<article>
		<header class="top">
			<div class="shell">
				<nav aria-label="<?php pm_e( 'ficha.migas' ); ?>" class="migas">
					<ol>
						<?php foreach ( $migas as $i => [ $nombre, $url ] ) : ?>
							<li><?php if ( $url && $i < count( $migas ) - 1 ) : ?><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $nombre ); ?></a><?php else : ?><span<?php echo $i === count( $migas ) - 1 ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $nombre ); ?></span><?php endif; ?></li>
						<?php endforeach; ?>
					</ol>
				</nav>
				<h1><?php echo esc_html( $d['h1'] ?: $d['nombre'] ); ?></h1>
				<p class="bajada"><?php echo esc_html( $d['bajada'] ); ?></p>
				<p class="confianza">
					<?php pm_e( 'ficha.por' ); ?> <?php echo esc_html( $d['autor'] ); ?> · <?php pm_e( 'ficha.actualizado' ); ?> <time datetime="<?php echo esc_attr( $d['actualizado'] ); ?>"><?php echo esc_html( pm_fecha( $d['actualizado'] ) ); ?></time>
					<?php if ( $d['reglamento']['verificado'] ) : ?>
						· <?php pm_e( 'ficha.verificado' ); ?> <?php echo esc_html( pm_fecha( $d['reglamento']['verificado'] ) ); ?>
					<?php endif; ?>
					<?php if ( $d['alturas_url'] ) : ?>
						· <a href="<?php echo esc_url( $d['alturas_url'] ); ?>" rel="noopener"><?php pm_e( 'ficha.alturas' ); ?></a>
					<?php endif; ?>
				</p>
			</div>
		</header>

		<section class="shell resumen" aria-label="<?php pm_e( 'ficha.resumen' ); ?>">
			<dl>
				<div><dt><?php pm_e( 'ficha.especies' ); ?></dt><dd><ul class="chips">
					<?php foreach ( $d['especies'] as $clave ) :
						$e = pm_especie_en( $clave, $idioma );
						if ( $e ) : ?>
							<li><a href="<?php echo esc_url( pm_url_especie( $clave, $idioma ) ); ?>"><?php echo esc_html( $e['nombre'] ); ?></a></li>
						<?php endif;
					endforeach; ?>
				</ul></dd></div>
				<div><dt><?php pm_e( 'ficha.modalidades' ); ?></dt><dd><?php echo esc_html( implode( ' · ', $d['modalidades'] ) ); ?></dd></div>
				<div><dt><?php pm_e( 'ficha.cuando' ); ?></dt><dd><?php echo esc_html( $d['epoca'] ); ?></dd></div>
				<div><dt><?php pm_e( 'ficha.guias' ); ?></dt><dd><a href="#prestadores"><?php pm_e( 'ficha.prestadores.link' ); ?></a></dd></div>
			</dl>
		</section>

		<div class="shell layout">
			<div class="prose">
				<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>

				<?php if ( $d['temporada'] ) : ?>
					<h2 id="temporada"><?php pm_e( 'ficha.calendario' ); ?></h2>
					<table>
						<thead><tr><th scope="col"><?php pm_e( 'ficha.epoca' ); ?></th><th scope="col"><?php pm_e( 'ficha.que' ); ?></th></tr></thead>
						<tbody>
							<?php foreach ( $d['temporada'] as $x ) : ?>
								<tr><td><?php echo esc_html( $x['meses'] ); ?></td><td><?php echo esc_html( $x['que'] ); ?></td></tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<?php if ( $citas ) : ?>
					<h2 id="voces"><?php pm_e( 'ficha.citas.h2' ); ?></h2>
					<div class="citas">
						<?php foreach ( $citas as $c ) : ?>
							<figure class="cita"<?php echo $c['idioma'] !== $idioma ? ' lang="' . esc_attr( pm_meta_idioma( $c['idioma'] )['html'] ) . '"' : ''; ?>>
								<blockquote cite="<?php echo esc_url( $c['url'] ); ?>"><p><?php echo esc_html( $c['cita'] ); ?></p></blockquote>
								<figcaption><strong><?php echo esc_html( $c['persona'] ); ?></strong>, <?php echo esc_html( $c['rol'] ); ?> · <a href="<?php echo esc_url( $c['url'] ); ?>"><?php pm_e( 'ficha.citas.leer' ); ?></a></figcaption>
							</figure>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<h2 id="reglamento"><?php pm_e( 'ficha.reglamento' ); ?></h2>
				<div class="regla">
					<p><?php echo esc_html( $d['reglamento']['resumen'] ); ?></p>
					<p><strong><?php pm_e( 'ficha.autoridad' ); ?>:</strong>
						<?php if ( $d['reglamento']['url'] ) : ?><a href="<?php echo esc_url( $d['reglamento']['url'] ); ?>" rel="noopener"><?php echo esc_html( $d['reglamento']['autoridad'] ); ?></a><?php else : echo esc_html( $d['reglamento']['autoridad'] ); endif; ?>
					</p>
					<p class="note">
						<?php if ( $d['reglamento']['verificado'] ) { echo esc_html( pm_t( 'ficha.ultima' ) . ' ' . pm_fecha( $d['reglamento']['verificado'] ) . '.' ); } ?>
						<?php pm_e( 'ficha.cambian' ); ?>
					</p>
				</div>

				<h2 id="prestadores"><?php pm_e( 'ficha.prestadores.h2' ); ?></h2>
				<p><?php pm_e( 'ficha.prestadores.p', [ 'lugar' => $d['nombre'] ] ); ?></p>
				<?php foreach ( $prestadores as $r ) {
					get_template_part( 'parciales/prestador', null, $r );
				} ?>
				<div class="elegir">
					<h3><?php pm_e( 'ficha.elegir.h3' ); ?></h3>
					<ol>
						<?php for ( $n = 1; $n <= 6; $n++ ) : ?><li><?php pm_e( "ficha.elegir.$n" ); ?></li><?php endfor; ?>
					</ol>
					<p class="note"><?php pm_e( 'ficha.elegir.fuente' ); ?></p>
				</div>
				<?php get_template_part( 'parciales/consulta', null, [ 'destino' => $d['clave'], 'lugar' => $d['nombre'], 'especies' => $d['especies'] ] ); ?>

				<?php if ( $d['faqs'] ) : ?>
					<h2 id="preguntas"><?php pm_e( 'ficha.faqs' ); ?></h2>
					<?php foreach ( $d['faqs'] as $f ) : ?>
						<details><summary><?php echo esc_html( $f['pregunta'] ); ?></summary><p><?php echo esc_html( $f['respuesta'] ); ?></p></details>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( $d['fuentes'] ) : ?>
					<h2 id="fuentes"><?php pm_e( 'ficha.fuentes' ); ?></h2>
					<ul class="fuentes">
						<?php foreach ( $d['fuentes'] as $f ) : ?>
							<li><a href="<?php echo esc_url( $f['url'] ); ?>" rel="noopener"><?php echo esc_html( $f['nombre'] ); ?></a><?php if ( ! empty( $f['oficial'] ) ) : ?><span class="ofi"><?php pm_e( 'ficha.oficial' ); ?></span><?php endif; ?> <span class="note"><?php pm_e( 'ficha.consultada' ); ?> <?php echo esc_html( pm_fecha( $f['consultada'] ) ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<aside class="aside">
				<?php echo pm_foto( $d['foto'], "{$d['nombre']}, {$provincia}", '(max-width: 960px) 100vw, 400px', [ 'clase' => 'portada', 'prioridad' => true ] ); // phpcs:ignore ?>
				<h2 class="visually-hidden"><?php pm_e( 'ficha.mapa' ); ?></h2>
				<?php echo pm_mapa_interactivo( $pins, pm_t( 'ficha.mapa.de' ) . ' ' . $d['nombre'], [ 'centro' => $d['coordenadas'], 'zoom' => 10, 'alto' => '420px' ] ); // phpcs:ignore ?>
				<p class="note"><?php pm_e( 'mapa.orientativo' ); ?></p>
				<?php if ( $d['aguas'] ) : ?>
					<ul class="aguas">
						<?php foreach ( $d['aguas'] as $a ) : ?>
							<li><strong><?php echo esc_html( $a['nombre'] ); ?></strong><span><?php echo esc_html( $a['nota'] ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</aside>
		</div>
	</article>
</div>
<?php get_footer();
