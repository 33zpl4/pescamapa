<?php
/** River level at one measuring point: /altura-del-rio/<clave>/, /pt/altura-do-rio/<clave>/, /en/river-levels/<clave>/. */
defined( 'ABSPATH' ) || exit;

$idioma = pm_lang();
$clave  = (string) get_query_var( 'pm_rio_clave' );
$x      = pm_rio_destino( $clave );
$r      = $x['rio'] ?? null;
$clima  = $x['clima'] ?? [];
$lugar  = pm_rio_nombre( $clave );
$curso  = $r['curso'] ?? ( pm_rio_config()[ $clave ]['rio'] ?? '' );
$mareal = pm_rio_mareal( $clave );
$estado = $r ? pm_rio_estado( $r ) : '';
$viejo  = $r && pm_rio_dias( $r['fecha'] ) > PM_RIO_VIEJO_DIAS;
$guia   = pm_buscar( 'pm_destino', $clave, $idioma ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
[ $pasados, $proximos ] = pm_rio_clima_partes( $clima );
get_header(); ?>
<div class="v-destino v-rio v-rio-punto">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php echo esc_html( $curso ); ?></div>
			<h1><?php echo esc_html( pm_t( 'rio.p.h1', [ 'lugar' => $lugar ] ) ); ?></h1>
			<?php if ( $r ) : ?>
				<p class="bajada"><?php echo esc_html( pm_t( $mareal ? 'rio.p.respuesta.mareal' : 'rio.p.respuesta', [ 'lugar' => $lugar, 'curso' => $curso, 'altura' => pm_rio_num( $r['altura'] ), 'fecha' => pm_fecha( $r['fecha'] ), 'tend' => pm_rio_tendencia_texto( $r ) ] ) ); ?><?php if ( $estado ) : ?> <?php echo esc_html( pm_t( 'rio.p.nivel', [ 'alerta' => pm_rio_num( $r['alerta'] ), 'evac' => pm_rio_num( $r['evacuacion'] ?? 0 ) ] ) ); ?><?php endif; ?></p>
			<?php endif; ?>
			<p class="confianza"><a href="<?php echo esc_url( pm_url_rio( $idioma ) ); ?>"><?php pm_e( 'rio.p.todos' ); ?></a></p>
		</div>
	</header>
	<div class="shell rio-hub">
		<?php if ( $r ) : ?>
			<div class="rio-grid">
				<div class="rio-altura">
					<div class="rio-num"><?php echo esc_html( pm_rio_num( $r['altura'] ) ); ?> <span>m</span></div>
					<p class="rio-que"><?php echo esc_html( pm_t( 'rio.altura', [ 'estacion' => $r['estacion'] ] ) ); ?> · <?php echo esc_html( pm_fecha( $r['fecha'] ) ); ?></p>
					<?php if ( $r['tendencia'] && ! $mareal ) : ?><p class="rio-tend rio-<?php echo esc_attr( $r['tendencia'] ); ?>"><?php echo esc_html( pm_rio_tendencia_texto( $r ) ); ?></p><?php endif; ?>
					<?php if ( $estado ) : ?><p class="rio-estado rio-<?php echo esc_attr( $estado ); ?>"><?php echo esc_html( pm_t( 'rio.estado.' . $estado, [ 'alerta' => pm_rio_num( $r['alerta'] ), 'evac' => pm_rio_num( $r['evacuacion'] ?? 0 ) ] ) ); ?></p><?php endif; ?>
					<?php if ( $viejo ) : ?><p class="aviso"><?php pm_e( 'rio.viejo' ); ?></p><?php endif; ?>
				</div>
				<?php if ( $clima ) : ?>
					<div class="rio-clima">
						<h3><?php pm_e( 'rio.clima.h3' ); ?></h3>
						<ul>
							<?php if ( $pasados ) : ?><li><strong><?php pm_e( 'rio.clima.pasados' ); ?>:</strong> <?php echo esc_html( pm_rio_resumen_clima( $pasados ) ); ?></li><?php endif; ?>
							<?php if ( $proximos ) : ?><li><strong><?php pm_e( 'rio.clima.proximos' ); ?>:</strong> <?php echo esc_html( pm_rio_resumen_clima( $proximos ) ); ?></li><?php endif; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( ! empty( $r['serie'] ) ) : ?>
				<h2><?php pm_e( 'rio.p.serie.h2' ); ?></h2>
				<div class="tabla-rio tabla-rio-serie">
					<table>
						<thead><tr><th><?php pm_e( 'rio.p.serie.fecha' ); ?></th><th><?php pm_e( 'rio.p.serie.altura' ); ?></th></tr></thead>
						<tbody>
						<?php foreach ( array_reverse( $r['serie'] ) as $p ) : ?>
							<tr><th scope="row"><?php echo esc_html( pm_fecha( $p['fecha'] ) ); ?></th><td><?php echo esc_html( pm_rio_num( $p['m'] ) . ' m' ); ?></td></tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		<?php endif; ?>
		<div class="prose">
			<?php // What the reading means for fishing: the sentence follows the state (tidal point, above alert, rising, falling, steady), then the guide's own calendar and the town's guides. ?>
			<section class="rio-pescar" aria-labelledby="pescar-h">
				<h2 id="pescar-h"><?php pm_e( 'rio.pescar.h2' ); ?></h2>
				<?php
				$pescar = ! $r || $viejo ? 'sindato' : ( $mareal ? 'mareal' : ( $r['tendencia'] ?: 'estable' ) );
				if ( $r && ! $viejo && in_array( $estado, [ 'alerta', 'evacuacion' ], true ) ) : ?><p><?php pm_e( 'rio.pescar.alerta' ); ?></p><?php endif; ?>
				<p><?php pm_e( 'rio.pescar.' . $pescar ); ?></p>
				<?php if ( $guia ) : ?>
					<p class="enlaces">
						<a href="<?php echo esc_url( get_permalink( $guia ) . '#temporada' ); ?>"><?php echo esc_html( pm_t( 'rio.pescar.temporada', [ 'lugar' => $lugar ] ) ); ?></a>
						<?php if ( pm_destino_guias( $clave, $idioma ) ) : ?><a href="<?php echo esc_url( pm_url_guias( $clave, $idioma ) ); ?>"><?php echo esc_html( pm_t( 'rio.pescar.guias', [ 'lugar' => $lugar ] ) ); ?></a><?php endif; ?>
					</p>
				<?php endif; ?>
			</section>
			<?php if ( $r ) : ?><p class="note"><?php pm_e( 'rio.nota.niveles' ); ?></p><?php endif; ?>
			<?php if ( 'ba' === ( pm_rio_config()[ $clave ]['grupo'] ?? '' ) ) : ?><p class="note"><?php pm_e( 'rio.leer.5' ); ?></p><?php endif; ?>
			<p class="note"><?php echo wp_kses( pm_t( 'rio.fuente', pm_rio_enlaces() + [ 'capturado' => esc_html( pm_fecha( substr( (string) ( $x['capturado'] ?? '' ), 0, 10 ) ) ) ] ), [ 'a' => [ 'href' => true, 'rel' => true ] ] ); ?></p>
			<?php if ( $guia ) : ?><p><a class="button dark" href="<?php echo esc_url( get_permalink( $guia ) ); ?>"><?php echo esc_html( pm_t( 'rio.p.guia', [ 'lugar' => $lugar ] ) ); ?></a></p><?php endif; ?>
			<?php $cerca = pm_rio_cercanos( $clave ); if ( $cerca ) : ?>
				<h2><?php pm_e( 'rio.p.cerca.h2' ); ?></h2>
				<ul>
					<?php foreach ( $cerca as $k ) : $rk = pm_rio_datos()['destinos'][ $k ]['rio']; ?>
						<li><a href="<?php echo esc_url( pm_url_rio_punto( $k, $idioma ) ); ?>"><?php echo esc_html( pm_rio_nombre( $k ) ); ?></a> · <?php echo esc_html( pm_rio_num( $rk['altura'] ) . ' m' ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<p><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'rio.cta' ); ?></a></p>
		</div>
	</div>
</div>
<?php get_footer();
