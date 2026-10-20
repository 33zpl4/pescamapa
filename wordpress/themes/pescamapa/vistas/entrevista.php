<?php
/** One interview, any language (the post carries its language). */
defined( 'ABSPATH' ) || exit;

$e       = pm_entrevista( get_queried_object_id() );
$idioma  = $e['idioma'];
$destino = $e['destino'] ? ( pm_buscar( 'pm_destino', $e['destino'], $idioma ) ?? pm_buscar( 'pm_destino', $e['destino'], 'es' ) ) : null;

get_header(); ?>
<div class="v-destino v-entrevista">
	<article>
		<header class="top">
			<div class="shell">
				<nav aria-label="<?php pm_e( 'ficha.migas' ); ?>" class="migas">
					<ol>
						<li><a href="<?php echo esc_url( pm_url_inicio( $idioma ) ); ?>"><?php pm_e( 'ficha.inicio' ); ?></a></li>
						<li><a href="<?php echo esc_url( pm_url_entrevistas( $idioma ) ); ?>"><?php pm_e( 'entrevistas.h1' ); ?></a></li>
						<li><span aria-current="page"><?php echo esc_html( $e['persona'] ); ?></span></li>
					</ol>
				</nav>
				<div class="eyebrow"><?php pm_e( 'entrevista.eyebrow' ); ?></div>
				<h1><?php echo esc_html( $e['titulo'] ); ?></h1>
				<p class="bajada"><?php echo esc_html( $e['bajada'] ); ?></p>
				<p class="confianza"><strong><?php echo esc_html( $e['persona'] ); ?></strong>, <?php echo esc_html( $e['rol'] ); ?> · <time datetime="<?php echo esc_attr( $e['fecha'] ); ?>"><?php pm_e( 'entrevista.fecha', [ 'fecha' => pm_fecha( $e['fecha'] ) ] ); ?></time></p>
			</div>
		</header>
		<div class="shell layout">
			<div class="prose">
				<?php if ( $e['citas'] ) : ?>
					<aside class="resumen-citas">
						<h2><?php pm_e( 'entrevista.citas' ); ?></h2>
						<ul>
							<?php foreach ( $e['citas'] as $c ) : ?><li>«<?php echo esc_html( $c['cita'] ); ?>»</li><?php endforeach; ?>
						</ul>
					</aside>
				<?php endif; ?>
				<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
				<?php if ( $destino ) : ?>
					<p><a class="button dark" href="<?php echo esc_url( get_permalink( $destino ) ); ?>"><?php pm_e( 'entrevista.guia', [ 'lugar' => get_the_title( $destino ) ] ); ?></a></p>
				<?php endif; ?>
			</div>
			<aside class="aside">
				<?php echo pm_foto( $e['foto'], $e['persona'], '(max-width: 960px) 100vw, 400px', [ 'clase' => 'portada', 'prioridad' => true ] ); // phpcs:ignore ?>
			</aside>
		</div>
	</article>
</div>
<?php get_footer();
