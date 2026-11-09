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
	[ $provincia, pm_url_provincia( $d['region'], $d['provincia'], $idioma ) ],
	[ $d['nombre'], $d['url'] ],
];
$rio_hoy     = pm_rio_destino( $d['clave'] );
$hoy_texto   = '';
if ( ! empty( $rio_hoy['rio'] ) ) { // today's reading, or the coming days' wind when the place has no gauge
	$hoy_texto = pm_t( 'ficha.res.hoy.rio', [ 'altura' => pm_rio_num( $rio_hoy['rio']['altura'] ), 'fecha' => pm_fecha( $rio_hoy['rio']['fecha'] ) ] );
} elseif ( ! empty( $rio_hoy['clima'] ) ) {
	$prox = pm_rio_clima_partes( $rio_hoy['clima'] )[1];
	$hoy_texto = $prox ? pm_t( 'ficha.res.hoy.clima', [ 'linea' => pm_rio_resumen_clima( array_slice( $prox, 0, 3 ) ) ] ) : '';
}
$citas       = pm_citas_destino( $d['clave'], $idioma );
$paquetes    = pm_paquetes( get_queried_object_id() );
$prestadores = pm_prestadores_destino( $d['clave'], current_user_can( 'edit_posts' ) );
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
				<?php $esp = array_filter( array_map( fn( $c ) => pm_especie_en( $c, $idioma ) ? [ $c, pm_especie_en( $c, $idioma ) ] : null, $d['especies'] ) ); // a guide without species sheets shows no empty cell ?>
				<?php if ( $esp ) : ?><div><dt><?php pm_e( 'ficha.especies' ); ?></dt><dd><ul class="chips">
					<?php foreach ( $esp as [ $clave, $e ] ) : ?>
						<li><a href="<?php echo esc_url( pm_url_especie( $clave, $idioma ) ); ?>"><?php echo esc_html( $e['nombre'] ); ?></a></li>
					<?php endforeach; ?>
				</ul></dd></div><?php endif; ?>
				<div><dt><?php pm_e( 'ficha.modalidades' ); ?></dt><dd><?php echo esc_html( implode( ' · ', $d['modalidades'] ) ); ?></dd></div>
				<div><dt><?php pm_e( 'ficha.cuando' ); ?></dt><dd><?php echo esc_html( $d['epoca'] ); ?></dd></div>
				<div><dt><?php pm_e( 'ficha.res.reglamento' ); ?></dt><dd><a href="#reglamento"><?php echo esc_html( $d['reglamento']['autoridad'] ); ?></a></dd></div>
				<?php if ( $hoy_texto ) : ?><div><dt><?php pm_e( 'ficha.res.hoy' ); ?></dt><dd><a href="#rio"><?php echo esc_html( $hoy_texto ); ?></a></dd></div><?php endif; ?>
				<?php if ( $paquetes ) : ?><div><dt><?php pm_e( 'ficha.res.paquetes' ); ?></dt><dd><a href="<?php echo esc_url( pm_url_paquetes( $d['clave'], $idioma ) ); ?>"><?php pm_e( 'paquetes.res.link', [ 'lugar' => $d['nombre'] ] ); ?></a></dd></div><?php endif; ?>
				<div><dt><?php pm_e( 'ficha.guias' ); ?></dt><dd><a href="#prestadores"><?php pm_e( 'ficha.prestadores.link' ); ?></a></dd></div>
			</dl>
		</section>

		<nav class="shell ir-a" aria-label="<?php pm_e( 'ficha.ir.h' ); ?>">
			<?php foreach ( array_filter( [
				'rio'        => $hoy_texto ? 'ficha.ir.rio' : '',
				'temporada'  => ( $d['temporada'] || pm_temporada_hay( $d ) ) ? 'ficha.ir.calendario' : '',
				'reglamento' => 'ficha.ir.reglamento',
				'prestadores' => 'ficha.ir.guias',
				'preguntas'  => $d['faqs'] ? 'ficha.ir.preguntas' : '',
				'fuentes'    => $d['fuentes'] ? 'ficha.ir.fuentes' : '',
			] ) as $ancla => $clave ) : ?>
				<a href="#<?php echo esc_attr( $ancla ); ?>"><?php pm_e( $clave ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php get_template_part( 'parciales/rio', null, [ 'clave' => $d['clave'], 'lugar' => $d['nombre'] ] ); ?>

		<div class="shell layout">
			<div class="prose">
				<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>

				<?php if ( $d['temporada'] || pm_temporada_hay( $d ) ) : ?>
					<h2 id="temporada"><?php pm_e( 'ficha.calendario' ); ?></h2>
					<?php get_template_part( 'parciales/barra-meses', null, [ 'd' => $d ] ); ?>
				<?php endif; ?>
				<?php if ( $d['temporada'] ) : ?>
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
				<?php if ( pm_destino_guias( $d['clave'], $idioma ) ) : ?>
					<p class="a-guias"><a href="<?php echo esc_url( pm_url_guias( $d['clave'], $idioma ) ); ?>"><?php pm_e( 'guias.desde_guia', [ 'lugar' => $d['nombre'] ] ); ?></a></p>
				<?php endif; ?>
				<div class="elegir">
					<h3><?php pm_e( 'ficha.elegir.h3' ); ?></h3>
					<ol>
						<?php for ( $n = 1; $n <= 6; $n++ ) : ?><li><?php pm_e( "ficha.elegir.$n" ); ?></li><?php endfor; ?>
					</ol>
					<p class="note"><?php pm_e( 'ficha.elegir.fuente' ); ?></p>
				</div>
				<?php get_template_part( 'parciales/notas-relacionadas', null, [ 'notas' => pm_notas_para( $idioma, $d['clave'], $d['especies'] ), 'h2' => 'ficha.notas.h2', 'vars' => [ 'lugar' => $d['nombre'] ] ] ); ?>
				<?php get_template_part( 'parciales/consulta', null, [ 'destino' => $d['clave'], 'lugar' => $d['nombre'], 'especies' => $d['especies'] ] ); ?>

				<?php $faqs_todas = pm_faqs_destino( $d ); if ( $faqs_todas ) : ?>
					<h2 id="preguntas"><?php pm_e( 'ficha.faqs' ); ?></h2>
					<?php foreach ( $faqs_todas as $f ) : ?>
						<details><summary><?php echo esc_html( $f['pregunta'] ); ?></summary><p><?php echo esc_html( $f['respuesta'] ); ?></p></details>
					<?php endforeach; ?>
				<?php endif; ?>

				<?php if ( $d['mas'] ) : ?>
					<h2 id="mas"><?php pm_e( 'ficha.mas.h2', [ 'lugar' => $d['nombre'] ] ); ?></h2>
					<ul class="mas">
						<?php foreach ( $d['mas'] as $m ) :
							$pago = ! empty( $m['patrocinado'] ); ?>
							<li>
								<a href="<?php echo esc_url( $m['url'] ); ?>" rel="<?php echo $pago ? 'sponsored noopener' : 'noopener'; ?>">
									<strong><?php echo esc_html( $m['nombre'] ); ?></strong>
									<?php if ( ! empty( $m['texto'] ) ) : ?><span class="texto"><?php echo esc_html( $m['texto'] ); ?></span><?php endif; ?>
									<span class="sitio"><?php echo esc_html( preg_replace( '/^www\./', '', (string) wp_parse_url( $m['url'], PHP_URL_HOST ) ) ); ?> ↗</span>
								</a>
								<?php if ( $pago ) : ?><span class="sello-pago"><?php pm_e( 'ficha.mas.patrocinado' ); ?></span><?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $d['fuentes'] ) : ?>
					<details id="fuentes" class="fuentes-det">
					<summary><?php pm_e( 'ficha.fuentes.consultadas', [ 'n' => (string) count( $d['fuentes'] ) ] ); ?></summary>
					<ul class="fuentes">
						<?php foreach ( $d['fuentes'] as $f ) : ?>
							<li><?php if ( ! empty( $f['url'] ) ) : ?><a href="<?php echo esc_url( $f['url'] ); ?>" rel="noopener"><?php echo esc_html( $f['nombre'] ); ?></a><?php else : echo esc_html( $f['nombre'] ); endif; ?><?php if ( ! empty( $f['oficial'] ) ) : ?><span class="ofi"><?php pm_e( 'ficha.oficial' ); ?></span><?php endif; ?> <?php if ( ! empty( $f['consultada'] ) ) : ?> <span class="note"><?php pm_e( 'ficha.consultada' ); ?> <?php echo esc_html( pm_fecha( $f['consultada'] ) ); ?></span><?php endif; ?></li>
						<?php endforeach; ?>
					</ul>
					</details>
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
<?php get_template_part( 'parciales/barra-pedir', null, [ 'url' => '#consulta', 'hasta' => '#consulta' ] ); ?>
<?php get_footer();
