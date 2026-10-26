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
			<?php if ( $e['actualizado'] ) : ?>
				<p class="confianza"><?php pm_e( 'ficha.actualizado' ); ?> <time datetime="<?php echo esc_attr( $e['actualizado'] ); ?>"><?php echo esc_html( pm_fecha( $e['actualizado'] ) ); ?></time></p>
			<?php endif; ?>
		</div>
	</header>
	<div class="shell body">
		<div class="prose"><?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?></div>
		<?php if ( $e['fuentes'] ) : ?>
			<details id="fuentes" class="fuentes-det">
				<summary><?php pm_e( 'ficha.fuentes.consultadas', [ 'n' => (string) count( array_filter( $e['fuentes'], fn( $f ) => ! empty( $f['nombre'] ) ) ) ] ); ?></summary>
				<ul class="fuentes">
					<?php foreach ( $e['fuentes'] as $f ) : if ( empty( $f['nombre'] ) ) { continue; } ?>
						<li><?php if ( ! empty( $f['url'] ) ) : ?><a href="<?php echo esc_url( $f['url'] ); ?>" rel="noopener"><?php echo esc_html( $f['nombre'] ); ?></a><?php else : echo esc_html( $f['nombre'] ); endif; ?><?php if ( ! empty( $f['oficial'] ) ) : ?> <span class="ofi"><?php pm_e( 'ficha.oficial' ); ?></span><?php endif; ?></li>
					<?php endforeach; ?>
				</ul>
			</details>
		<?php endif; ?>
		<?php get_template_part( 'parciales/notas-relacionadas', null, [ 'notas' => pm_notas_para( $idioma, '', [ $e['clave'] ] ), 'h2' => 'especie.notas.h2' ] ); ?>
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
