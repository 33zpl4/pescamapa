<?php
/** Species page, any language. */
defined( 'ABSPATH' ) || exit;

$e        = pm_especie( get_queried_object_id() );
$idioma   = $e['idioma'];
$destinos = pm_destinos_con_especie( $e['clave'], $idioma );
// The way from the facts to an inquiry (13/11/2026): the species is preselected in the trip form (?especie=<clave>).
$url_pedir = pm_url_viaje( $idioma ) . '?' . http_build_query( [ 'especie' => $e['clave'] ] );

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
		<p class="cta-nota"><?php pm_e( 'especie.cta', [ 'especie' => mb_strtolower( $e['nombre'] ) ] ); ?> <a class="button dark" href="<?php echo esc_url( $url_pedir ); ?>"><?php pm_e( 'inicio.armar' ); ?></a></p>
		<div class="prose"><?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?></div>
		<?php if ( $e['fuentes'] ) : ?>
			<details id="fuentes" class="fuentes-det">
				<summary><?php pm_e( 'ficha.fuentes.consultadas', [ 'n' => (string) count( array_filter( $e['fuentes'], fn( $f ) => ! empty( $f['nombre'] ) ) ) ] ); ?></summary>
				<ul class="fuentes">
					<?php foreach ( $e['fuentes'] as $f ) : if ( empty( $f['nombre'] ) ) { continue; } ?>
						<li><?php echo pm_fuente_nombre_html( $f ); // phpcs:ignore -- escaped inside ?><?php if ( ! empty( $f['oficial'] ) ) : ?> <span class="ofi"><?php pm_e( 'ficha.oficial' ); ?></span><?php endif; ?></li>
					<?php endforeach; ?>
				</ul>
			</details>
		<?php endif; ?>
		<?php get_template_part( 'parciales/guias-especie', null, [ 'especie' => $e['clave'], 'idioma' => $idioma ] ); ?>
		<?php get_template_part( 'parciales/notas-relacionadas', null, [ 'notas' => pm_notas_para( $idioma, '', [ $e['clave'] ] ), 'h2' => 'especie.notas.h2' ] ); ?>
		<?php if ( $destinos ) : ?>
			<h2><?php pm_e( 'especie.donde' ); ?> <?php echo esc_html( mb_strtolower( $e['nombre'] ) ); ?></h2>
			<ul class="chips">
				<?php foreach ( $destinos as $d ) : ?>
					<li><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] . ', ' . ( PM_PROVINCIAS[ $d['provincia'] ] ?? '' ) ); ?></a></li>
					<?php if ( pm_destino_paquetes( $d['clave'], $idioma ) ) : ?><li><a href="<?php echo esc_url( pm_url_paquetes( $d['clave'], $idioma ) ); ?>"><?php pm_e( 'paquetes.especie.link', [ 'lugar' => $d['nombre'] ] ); ?></a></li><?php endif; ?>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<p class="especies-todas"><a href="<?php echo esc_url( pm_url_especies( $idioma ) ); ?>"><?php pm_e( 'especies.todas' ); ?></a></p>
	</div>
</div>
<?php get_template_part( 'parciales/barra-pedir', null, [ 'url' => $url_pedir, 'hasta' => '.cta-nota' ] ); ?>
<?php get_footer();
