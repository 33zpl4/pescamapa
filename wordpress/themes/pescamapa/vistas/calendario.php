<?php
/**
 * Calendar: what is fished each month and what is closed, destination by destination, with the authority, the verification
 * date and the link to each guide. Aggregates the `temporada` rows and the regulation summary of every published guide in
 * this language (Spanish guide as fallback): nothing is written twice. /calendario-de-pesca/, /pt/calendario-de-pesca/, /en/fishing-calendar/.
 */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$destinos = pm_destinos_lista( $idioma );
$grupos   = [];
foreach ( $destinos as $d ) {
	$grupos[ $d['region'] ?: 'otros' ][] = $d;
}
$orden  = array_merge( array_keys( PM_REGIONES ), [ 'otros' ] );
$tabla  = pm_buscar( 'pm_nota', 'tallas-cupos-vedas', $idioma ) ?? pm_buscar( 'pm_nota', 'tallas-cupos-vedas', 'es' );
$fechas = array_filter( array_map( fn( $d ) => $d['reglamento']['verificado'], $destinos ) );
$ultima = $fechas ? max( $fechas ) : '';
get_header(); ?>
<div class="v-destino v-rio v-calendario">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'cal.eyebrow' ); ?></div>
			<h1><?php pm_e( 'cal.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'cal.bajada' ); ?></p>
			<?php if ( $ultima ) : ?><p class="confianza"><?php pm_e( 'ficha.verificado' ); ?> <time datetime="<?php echo esc_attr( $ultima ); ?>"><?php echo esc_html( pm_fecha( $ultima, $idioma ) ); ?></time></p><?php endif; ?>
		</div>
	</header>
	<div class="shell cuerpo">
		<section class="respuesta" aria-labelledby="respuesta-h">
			<h2 id="respuesta-h"><?php pm_e( 'cal.resumen.h' ); ?></h2>
			<ul>
				<li><?php pm_e( 'cal.resumen.1' ); ?></li>
				<li><?php pm_e( 'cal.resumen.2', [ 'n' => (string) count( $destinos ) ] ); ?></li>
				<?php if ( $tabla ) : ?><li><?php pm_e( 'cal.resumen.3' ); ?> <a href="<?php echo esc_url( get_permalink( $tabla ) ); ?>"><?php echo esc_html( get_the_title( $tabla ) ); ?></a></li><?php endif; ?>
			</ul>
		</section>
		<nav class="chips" aria-label="<?php pm_e( 'cal.eyebrow' ); ?>">
			<?php foreach ( $orden as $g ) : if ( empty( $grupos[ $g ] ) ) { continue; } ?>
				<a class="chip" href="#<?php echo esc_attr( $g ); ?>"><?php echo esc_html( PM_REGIONES[ $g ][ $idioma ] ?? $g ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php foreach ( $orden as $g ) : if ( empty( $grupos[ $g ] ) ) { continue; } ?>
			<h2 id="<?php echo esc_attr( $g ); ?>"><?php echo esc_html( PM_REGIONES[ $g ][ $idioma ] ?? $g ); ?></h2>
			<?php foreach ( $grupos[ $g ] as $d ) : $r = $d['reglamento']; ?>
				<article class="cal-destino" id="<?php echo esc_attr( $d['clave'] ); ?>">
					<h3><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] ); ?></a> <small><?php echo esc_html( $d['provincia'] ); ?></small></h3>
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
					<?php if ( $r['resumen'] ) : ?>
						<h4><?php pm_e( 'cal.reg.h' ); ?></h4>
						<?php echo wp_kses_post( wpautop( $r['resumen'] ) ); ?>
					<?php endif; ?>
					<p class="note">
						<?php if ( $r['autoridad'] ) : ?><?php pm_e( 'cal.reg.autoridad' ); ?>: <?php if ( $r['url'] ) : ?><a href="<?php echo esc_url( $r['url'] ); ?>" rel="noopener"><?php echo esc_html( $r['autoridad'] ); ?></a><?php else : ?><?php echo esc_html( $r['autoridad'] ); ?><?php endif; ?>. <?php endif; ?>
						<?php if ( $r['verificado'] ) : ?><?php pm_e( 'cal.reg.verificado', [ 'fecha' => pm_fecha( $r['verificado'], $idioma ) ] ); ?>.<?php else : ?><?php pm_e( 'cal.reg.sinfecha' ); ?><?php endif; ?>
					</p>
					<p class="enlaces">
						<a href="<?php echo esc_url( $d['url'] . '#reglamento' ); ?>"><?php pm_e( 'cal.guia', [ 'lugar' => $d['nombre'] ] ); ?></a>
						<?php if ( pm_destino_guias( $d['clave'], $idioma ) ) : ?><a href="<?php echo esc_url( pm_url_guias( $d['clave'], $idioma ) ); ?>"><?php pm_e( 'cal.guias', [ 'lugar' => $d['nombre'] ] ); ?></a><?php endif; ?>
					</p>
				</article>
			<?php endforeach; ?>
		<?php endforeach; ?>
		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'cal.faq.h' ); ?></h2>
			<?php foreach ( [ 1, 2, 3 ] as $n ) : ?>
				<h3><?php pm_e( "cal.faq.$n.q" ); ?></h3>
				<p><?php pm_e( "cal.faq.$n.a" ); ?></p>
			<?php endforeach; ?>
		</section>
		<p class="cta-nota"><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'nav.viaje' ); ?></a><?php pm_cta_nota(); ?></p>
	</div>
</div>
<?php get_footer();
