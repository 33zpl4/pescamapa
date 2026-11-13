<?php
/**
 * Province hub: every published destination of one province, with the four ways in (guide, fishing guides in the town,
 * packages, river level today, calendar), then the notes about those destinations. /litoral/corrientes/, /pt/…, /en/….
 * Only exists when the province has at least one published destination (rutas.php). Everything is read from the guides.
 */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$region   = (string) get_query_var( 'pm_region' );
$prov     = (string) get_query_var( 'pm_provincia' );
$destinos = pm_destinos_provincia( $region, $prov, $idioma );
$a        = [ 'prov' => PM_PROVINCIAS[ $prov ] ?? $prov, 'region' => PM_REGIONES[ $region ][ $idioma ] ?? $region, 'n' => (string) count( $destinos ) ];
$claves   = array_column( $destinos, 'clave' );
$notas    = array_slice( array_values( array_filter( pm_notas_lista( $idioma ), fn( $n ) => in_array( $n['destino'], $claves, true ) ) ), 0, 6 );
$ultima   = max( array_map( fn( $d ) => (string) get_post_modified_time( 'Y-m-d', true, get_post( $d['id'] ) ), $destinos ) ?: [ '' ] );
get_header(); ?>
<div class="v-destino v-rio v-provincia">
	<header class="top">
		<div class="shell">
			<nav aria-label="<?php pm_e( 'ficha.migas' ); ?>" class="migas">
				<ol>
					<li><a href="<?php echo esc_url( pm_url_inicio( $idioma ) ); ?>"><?php pm_e( 'ficha.inicio' ); ?></a></li>
					<li><span><?php echo esc_html( $a['region'] ); ?></span></li>
					<li><span aria-current="page"><?php echo esc_html( $a['prov'] ); ?></span></li>
				</ol>
			</nav>
			<div class="eyebrow"><?php pm_e( 'prov.eyebrow', $a ); ?></div>
			<h1><?php pm_e( 'prov.h1', $a ); ?></h1>
			<p class="bajada"><?php pm_e( 'prov.bajada', $a ); ?></p>
			<?php if ( $ultima ) : ?><p class="confianza"><?php pm_e( 'ficha.actualizado' ); ?> <time datetime="<?php echo esc_attr( $ultima ); ?>"><?php echo esc_html( pm_fecha( $ultima, $idioma ) ); ?></time></p><?php endif; ?>
		</div>
	</header>
	<div class="shell cuerpo">
		<?php // Optional intro per province (ui.json `prov.intro.<provincia>`): dated facts already published in the guides; none by default. ?>
		<?php if ( pm_t_existe( "prov.intro.$prov" ) ) : ?><p class="prov-intro"><?php echo esc_html( pm_t( "prov.intro.$prov", $a ) ); ?></p><?php endif; ?>
		<h2 id="destinos"><?php pm_e( 'prov.destinos.h2' ); ?></h2>
		<div class="prov-destinos">
			<?php foreach ( $destinos as $d ) : $rio = pm_rio_destino( $d['clave'] ); $np = count( pm_prestadores_destino( $d['clave'] ) ); ?>
				<article class="prov-destino" id="<?php echo esc_attr( $d['clave'] ); ?>">
					<?php if ( $d['foto'] ) : ?><a class="foto" href="<?php echo esc_url( $d['url'] ); ?>"><?php echo pm_foto( $d['foto'], $d['nombre'], '(max-width: 720px) 100vw, 360px' ); // phpcs:ignore -- escaped inside ?></a><?php endif; ?>
					<div>
						<h3><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] ); ?></a></h3>
						<?php if ( $d['bajada'] ) : ?><p><?php echo esc_html( $d['bajada'] ); ?></p><?php endif; ?>
						<p class="enlaces">
							<a href="<?php echo esc_url( $d['url'] ); ?>"><?php pm_e( 'prov.guia', [ 'lugar' => $d['nombre'] ] ); ?></a>
							<?php if ( pm_destino_guias( $d['clave'], $idioma ) ) : ?><a href="<?php echo esc_url( pm_url_guias( $d['clave'], $idioma ) ); ?>"><?php pm_e( 'prov.guias', [ 'lugar' => $d['nombre'], 'n' => (string) $np ] ); ?></a><?php endif; ?>
							<?php if ( pm_destino_paquetes( $d['clave'], $idioma ) ) : ?><a href="<?php echo esc_url( pm_url_paquetes( $d['clave'], $idioma ) ); ?>"><?php pm_e( 'prov.paquetes' ); ?></a><?php endif; ?>
							<?php if ( ! empty( $rio['rio'] ) ) : ?><a href="<?php echo esc_url( pm_url_rio_punto( $d['clave'], $idioma ) ); ?>"><?php pm_e( 'prov.rio' ); ?> · <?php echo esc_html( pm_rio_num( $rio['rio']['altura'] ) . ' m' ); ?></a><?php endif; ?>
							<?php if ( $d['temporada'] || $d['reglamento']['resumen'] ) : ?><a href="<?php echo esc_url( pm_url_calendario( $idioma ) . '#' . $d['clave'] ); ?>"><?php pm_e( 'prov.calendario' ); ?></a><?php endif; ?>
						</p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php get_template_part( 'parciales/notas-relacionadas', null, [ 'notas' => $notas, 'h2' => 'prov.notas.h2', 'vars' => $a ] ); ?>
		<p class="cta-nota"><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'prov.cta', $a ); ?></a></p>
	</div>
</div>
<?php get_footer();
