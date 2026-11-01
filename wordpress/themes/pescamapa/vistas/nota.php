<?php
/** One note, any language (the post carries its language). */
defined( 'ABSPATH' ) || exit;

$n       = pm_nota( get_queried_object_id() );
$idioma  = $n['idioma'];
$destino = $n['destino'] ? ( pm_buscar( 'pm_destino', $n['destino'], $idioma ) ?? pm_buscar( 'pm_destino', $n['destino'], 'es' ) ) : null;
$foto    = pm_foto( $n['foto'], $n['titulo'], '(max-width: 960px) 100vw, 400px', [ 'clase' => 'portada', 'prioridad' => true ] );

get_header(); ?>
<div class="v-destino v-nota">
	<article>
		<header class="top">
			<div class="shell">
				<nav aria-label="<?php pm_e( 'ficha.migas' ); ?>" class="migas">
					<ol>
						<li><a href="<?php echo esc_url( pm_url_inicio( $idioma ) ); ?>"><?php pm_e( 'ficha.inicio' ); ?></a></li>
						<li><a href="<?php echo esc_url( pm_url_notas( $idioma ) ); ?>"><?php pm_e( 'notas.h1' ); ?></a></li>
						<li><span aria-current="page"><?php echo esc_html( $n['titulo'] ); ?></span></li>
					</ol>
				</nav>
				<?php if ( $n['tipo'] ) : ?><div class="eyebrow"><?php echo esc_html( pm_opcion( 'nota_tipo', $n['tipo'], $idioma ) ); ?></div><?php endif; ?>
				<h1><?php echo esc_html( $n['titulo'] ); ?></h1>
				<?php if ( $n['bajada'] ) : ?><p class="bajada"><?php echo esc_html( $n['bajada'] ); ?></p><?php endif; ?>
				<p class="confianza">
					<?php pm_e( 'ficha.por' ); ?> <?php echo esc_html( $n['autor'] ?: 'Equipo Pescamapa' ); ?> · <time datetime="<?php echo esc_attr( $n['fecha'] ); ?>"><?php echo esc_html( pm_fecha( $n['fecha'], $idioma ) ); ?></time>
					<?php if ( $n['actualizado'] && $n['actualizado'] !== $n['fecha'] ) : ?>
						· <?php pm_e( 'ficha.actualizado' ); ?> <time datetime="<?php echo esc_attr( $n['actualizado'] ); ?>"><?php echo esc_html( pm_fecha( $n['actualizado'], $idioma ) ); ?></time>
					<?php endif; ?>
				</p>
			</div>
		</header>
		<div class="shell layout<?php echo $foto ? '' : ' sin-foto'; ?>">
			<div class="prose">
				<?php if ( $n['datos'] ) : ?>
					<aside class="datos" aria-labelledby="datos-h">
						<h2 id="datos-h"><?php pm_e( 'nota.datos' ); ?></h2>
						<ul>
							<?php foreach ( $n['datos'] as $dato ) : ?><li><?php echo esc_html( $dato ); ?></li><?php endforeach; ?>
						</ul>
					</aside>
				<?php endif; ?>
				<?php // The way from a fact to an inquiry: one line and the form, before the body (most readers arrive from a search or an AI answer with one question). ?>
				<p class="cta-nota"><?php pm_e( 'nota.cta' ); ?> <a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) . '?' . http_build_query( array_filter( [ 'destino' => $n['destino'], 'especie' => $n['especie'] ] ) ) ); ?>"><?php pm_e( 'nota.cta.boton' ); ?></a></p>
				<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
				<?php if ( $n['especie'] ) { get_template_part( 'parciales/guias-especie', null, [ 'especie' => $n['especie'], 'idioma' => $idioma ] ); } ?>

				<?php if ( $n['fuentes'] ) : ?>
					<h2 id="fuentes"><?php pm_e( 'ficha.fuentes' ); ?></h2>
					<ul class="fuentes">
						<?php foreach ( $n['fuentes'] as $f ) : ?>
							<li>
								<?php if ( ! empty( $f['url'] ) ) : ?><a href="<?php echo esc_url( $f['url'] ); ?>" rel="noopener"><?php echo esc_html( $f['nombre'] ); ?></a><?php else : echo esc_html( $f['nombre'] ); endif; ?>
								<?php if ( ! empty( $f['oficial'] ) ) : ?><span class="ofi"><?php pm_e( 'ficha.oficial' ); ?></span><?php endif; ?>
								<?php if ( ! empty( $f['consultada'] ) ) : ?><span class="note"><?php pm_e( 'ficha.consultada' ); ?> <?php echo esc_html( pm_fecha( $f['consultada'], $idioma ) ); ?></span><?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $destino ) : ?>
					<p><a class="button dark" href="<?php echo esc_url( get_permalink( $destino ) ); ?>"><?php pm_e( 'nota.guia', [ 'lugar' => get_the_title( $destino ) ] ); ?></a></p>
				<?php endif; ?>
			</div>
			<?php if ( $foto ) : ?>
				<aside class="aside"><?php echo $foto; // phpcs:ignore -- escaped inside ?></aside>
			<?php endif; ?>
		</div>
	</article>
</div>
<?php get_footer();
