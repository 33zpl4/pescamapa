<?php
/** River levels today, all stations: /altura-del-rio/, /pt/altura-do-rio/, /en/river-levels/. */
defined( 'ABSPATH' ) || exit;

$idioma  = pm_lang();
$datos   = pm_rio_datos();
$filas   = [];
foreach ( pm_rio_config() as $clave => $c ) {
	$x = $datos['destinos'][ $clave ] ?? null;
	$g = pm_buscar( 'pm_destino', $clave, $idioma ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	if ( $x && $g ) {
		$filas[] = [ 'clave' => $clave, 'nombre' => get_the_title( $g ), 'url' => get_permalink( $g ), 'x' => $x ];
	}
}
get_header(); ?>
<div class="v-destino v-rio">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'rio.eyebrow' ); ?></div>
			<h1><?php pm_e( 'rio.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'rio.bajada' ); ?></p>
			<?php if ( ! empty( $datos['capturado'] ) ) : ?><p class="confianza"><?php echo esc_html( pm_t( 'rio.actualizado', [ 'capturado' => pm_fecha( substr( $datos['capturado'], 0, 10 ) ) ] ) ); ?></p><?php endif; ?>
		</div>
	</header>
	<div class="shell rio-hub">
		<?php if ( ! $filas ) : ?>
			<p><?php pm_e( 'rio.vacio' ); ?></p>
		<?php else : ?>
			<div class="tabla-rio">
				<table>
					<thead><tr>
						<th><?php pm_e( 'rio.col.lugar' ); ?></th><th><?php pm_e( 'rio.col.altura' ); ?></th><th><?php pm_e( 'rio.col.tendencia' ); ?></th><th><?php pm_e( 'rio.col.alerta' ); ?></th><th><?php pm_e( 'rio.col.viento' ); ?></th><th><?php pm_e( 'rio.col.lluvia' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $filas as $f ) :
						$r = $f['x']['rio'] ?? null;
						[ $pas, $prox ] = pm_rio_clima_partes( $f['x']['clima'] ?? [] );
						$est = $r ? pm_rio_estado( $r ) : ''; ?>
						<tr>
							<th scope="row"><a href="<?php echo esc_url( $f['url'] . '#rio' ); ?>"><?php echo esc_html( $f['nombre'] ); ?></a><?php if ( $r ) : ?><small><?php echo esc_html( $r['curso'] ); ?></small><?php endif; ?></th>
							<td><?php echo $r ? esc_html( pm_rio_num( $r['altura'] ) . ' m' ) . '<small>' . esc_html( pm_fecha( $r['fecha'] ) ) . '</small>' : '—'; // phpcs:ignore ?></td>
							<td><?php echo $r && $r['tendencia'] ? esc_html( pm_t( 'rio.' . $r['tendencia'], [ 'delta' => pm_rio_num( abs( (float) $r['delta_3d'] ) ) ] ) ) : '—'; ?></td>
							<td><?php echo $r && $est ? '<span class="rio-' . esc_attr( $est ) . '">' . esc_html( pm_t( 'rio.col.estado.' . $est ) ) . '</span><small>' . esc_html( pm_rio_num( $r['alerta'] ) . ' / ' . pm_rio_num( $r['evacuacion'] ?? 0 ) . ' m' ) . '</small>' : '—'; // phpcs:ignore ?></td>
							<td><?php echo $prox ? esc_html( pm_rio_num( max( array_map( fn( $d ) => (float) $d['viento_max'], array_slice( $prox, 0, 2 ) ) ), 0 ) . ' km/h' ) : '—'; ?></td>
							<td><?php echo $pas ? esc_html( pm_rio_num( array_sum( array_map( fn( $d ) => (float) $d['lluvia'], $pas ) ), 1 ) . ' mm' ) : '—'; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		<div class="prose">
			<h2><?php pm_e( 'rio.leer.h2' ); ?></h2>
			<ul>
				<?php foreach ( [ 1, 2, 3, 4 ] as $n ) : ?><li><?php pm_e( "rio.leer.$n" ); ?></li><?php endforeach; ?>
			</ul>
			<h2><?php pm_e( 'rio.fuentes.h2' ); ?></h2>
			<p><?php pm_e( 'rio.fuentes.p' ); ?></p>
			<p><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'rio.cta' ); ?></a></p>
		</div>
	</div>
</div>
<?php get_footer();
