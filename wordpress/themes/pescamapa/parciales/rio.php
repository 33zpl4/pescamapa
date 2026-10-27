<?php
/** "Cómo está el río" block of a guide. Args: clave, lugar. Prints nothing without data. */
defined( 'ABSPATH' ) || exit;

$x = pm_rio_destino( $args['clave'] ?? '' );
if ( ! $x ) {
	return;
}
$r      = $x['rio'] ?? null;
$clima  = $x['clima'] ?? [];
[ $pasados, $proximos ] = pm_rio_clima_partes( $clima );
$estado = $r ? pm_rio_estado( $r ) : '';
$viejo  = $r && pm_rio_dias( $r['fecha'] ) > PM_RIO_VIEJO_DIAS;
?>
<section class="shell rio" id="rio" aria-labelledby="rio-h">
	<h2 id="rio-h"><?php $r ? pm_e( 'rio.h2' ) : pm_e( 'rio.h2.clima' ); ?></h2>
	<div class="rio-grid">
		<?php if ( $r ) : ?>
			<div class="rio-altura">
				<div class="rio-num"><?php echo esc_html( pm_rio_num( $r['altura'] ) ); ?> <span>m</span></div>
				<p class="rio-que"><?php echo esc_html( pm_t( 'rio.altura', [ 'estacion' => $r['estacion'] ] ) ); ?> · <?php echo esc_html( pm_fecha( $r['fecha'] ) ); ?></p>
				<?php if ( $r['tendencia'] ) : ?>
					<p class="rio-tend rio-<?php echo esc_attr( $r['tendencia'] ); ?>"><?php echo esc_html( pm_t( 'rio.' . $r['tendencia'], [ 'delta' => pm_rio_num( abs( (float) $r['delta_3d'] ) ) ] ) ); ?></p>
				<?php endif; ?>
				<?php if ( $estado ) : ?>
					<p class="rio-estado rio-<?php echo esc_attr( $estado ); ?>"><?php echo esc_html( pm_t( 'rio.estado.' . $estado, [ 'alerta' => pm_rio_num( $r['alerta'] ), 'evac' => pm_rio_num( $r['evacuacion'] ?? 0 ) ] ) ); ?></p>
				<?php endif; ?>
				<?php if ( $viejo ) : ?><p class="aviso"><?php pm_e( 'rio.viejo' ); ?></p><?php endif; ?>
			</div>
		<?php endif; ?>
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
	<?php if ( $r ) : ?><p class="note"><?php pm_e( 'rio.nota.niveles' ); ?></p><?php endif; ?>
	<p class="note"><?php echo wp_kses( pm_t( $r ? 'rio.fuente' : 'rio.fuente.clima', pm_rio_enlaces() + [ 'capturado' => esc_html( pm_fecha( substr( $x['capturado'], 0, 10 ) ) ) ] ), [ 'a' => [ 'href' => true, 'rel' => true ] ] ); ?><?php if ( $r ) : ?> <a href="<?php echo esc_url( pm_url_rio( pm_lang() ) ); ?>"><?php pm_e( 'rio.todos' ); ?></a><?php endif; ?></p>
</section>
