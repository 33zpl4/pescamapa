<?php
/** Species page, any language. */
defined( 'ABSPATH' ) || exit;

$e        = pm_especie( get_queried_object_id() );
$idioma   = $e['idioma'];
$destinos = pm_destinos_con_especie( $e['clave'], $idioma );

get_header(); ?>
<div class="v-especie">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'especie.eyebrow' ); ?></div>
			<h1><?php echo esc_html( $e['nombre'] ); ?> <small><i><?php echo esc_html( $e['cientifico'] ); ?></i></small></h1>
			<p><?php echo esc_html( $e['resumen'] ); ?></p>
		</div>
	</header>
	<div class="shell body">
		<div class="prose"><?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?></div>
		<?php if ( $destinos ) : ?>
			<h2><?php pm_e( 'especie.donde' ); ?> <?php echo esc_html( mb_strtolower( $e['nombre'] ) ); ?></h2>
			<ul class="chips">
				<?php foreach ( $destinos as $d ) : ?>
					<li><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] . ', ' . ( PM_PROVINCIAS[ $d['provincia'] ] ?? '' ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</div>
<?php get_footer();
