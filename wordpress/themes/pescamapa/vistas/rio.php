<?php
/** River levels today, all stations: /altura-del-rio/, /pt/altura-do-rio/, /en/river-levels/. */
defined( 'ABSPATH' ) || exit;

$idioma  = pm_lang();
$datos   = pm_rio_datos();
$filas   = [ 'litoral' => [], 'ba' => [] ];
foreach ( pm_rio_config() as $clave => $c ) {
	$x = $datos['destinos'][ $clave ] ?? null;
	if ( ! $x || empty( $x['rio'] ) ) { // weather-only guides (lagoons, Patagonia) show it in their own guide, not in a river table
		continue;
	}
	$g = pm_buscar( 'pm_destino', $clave, $idioma ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	if ( $g ) {
		$filas[ $c['grupo'] ?? 'litoral' ][] = [ 'clave' => $clave, 'nombre' => get_the_title( $g ), 'url' => pm_url_rio_punto( $clave, $idioma ), 'x' => $x ];
	} elseif ( ! empty( $c['nombre'] ) ) { // a place without a guide: name from the config, no link
		$filas[ $c['grupo'] ?? 'litoral' ][] = [ 'clave' => $clave, 'nombre' => $c['nombre'], 'url' => pm_url_rio_punto( $clave, $idioma ), 'x' => $x ];
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
		<?php $cn = pm_rio_conteo(); if ( $cn['n'] ) : ?>
			<p class="rio-resumen"><?php echo esc_html( pm_t( 'rio.resumen', $cn ) ); ?>
				<?php foreach ( [ 'litoral', 'ba' ] as $g ) : if ( $filas[ $g ] ) : ?> <a class="chip" href="#<?php echo esc_attr( $g ); ?>"><?php pm_e( 'rio.grupo.' . $g ); ?></a><?php endif; endforeach; ?></p>
		<?php endif; ?>
		<?php if ( ! $filas['litoral'] && ! $filas['ba'] ) : ?>
			<p><?php pm_e( 'rio.vacio' ); ?></p>
		<?php else : foreach ( $filas as $grupo => $lista ) : if ( ! $lista ) { continue; } ?>
			<h2 id="<?php echo esc_attr( $grupo ); ?>"><?php pm_e( 'rio.grupo.' . $grupo ); ?></h2>
			<div class="tabla-rio">
				<table>
					<thead><tr>
						<th><?php pm_e( 'rio.col.lugar' ); ?></th><th><?php pm_e( 'rio.col.altura' ); ?></th><th><?php pm_e( 'rio.col.tendencia' ); ?></th><th><?php pm_e( 'rio.col.alerta' ); ?></th><th><?php pm_e( 'rio.col.viento' ); ?></th><th><?php pm_e( 'rio.col.lluvia' ); ?></th>
					</tr></thead>
					<tbody>
					<?php foreach ( $lista as $f ) :
						$r = $f['x']['rio'] ?? null;
						[ $pas, $prox ] = pm_rio_clima_partes( $f['x']['clima'] ?? [] );
						$est = $r ? pm_rio_estado( $r ) : ''; ?>
						<tr>
							<th scope="row"><?php echo $f['url'] ? '<a href="' . esc_url( $f['url'] ) . '">' . esc_html( $f['nombre'] ) . '</a>' : esc_html( $f['nombre'] ); // phpcs:ignore ?><?php if ( $r ) : ?><small><?php echo esc_html( $r['curso'] ); ?></small><?php endif; ?></th>
							<td><?php echo $r ? esc_html( pm_rio_num( $r['altura'] ) . ' m' ) . '<small>' . esc_html( pm_fecha( $r['fecha'] ) ) . '</small>' : '—'; // phpcs:ignore ?></td>
							<td><?php echo $r && pm_rio_mareal( $f['clave'] ) ? esc_html( pm_t( 'rio.mareal.corto' ) ) : ( $r && $r['tendencia'] ? esc_html( pm_rio_tendencia_texto( $r ) ) : '—' ); ?></td>
							<td><?php echo $r && $est ? '<span class="rio-' . esc_attr( $est ) . '">' . esc_html( pm_t( 'rio.col.estado.' . $est ) ) . '</span><small>' . esc_html( pm_rio_num( $r['alerta'] ) . ' / ' . pm_rio_num( $r['evacuacion'] ?? 0 ) . ' m' ) . '</small>' : '—'; // phpcs:ignore ?></td>
							<td><?php echo $prox ? esc_html( pm_rio_num( max( array_map( fn( $d ) => (float) $d['viento_max'], array_slice( $prox, 0, 2 ) ) ), 0 ) . ' km/h' ) : '—'; ?></td>
							<td><?php echo $pas ? esc_html( pm_rio_num( array_sum( array_map( fn( $d ) => (float) $d['lluvia'], $pas ) ), 1 ) . ' mm' ) : '—'; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endforeach; endif; ?>
		<div class="prose">
			<h2><?php pm_e( 'rio.leer.h2' ); ?></h2>
			<ul>
				<?php foreach ( [ 1, 2, 3, 4, 5 ] as $n ) : ?><li><?php pm_e( "rio.leer.$n" ); ?></li><?php endforeach; ?>
			</ul>
			<h2><?php pm_e( 'rio.fuentes.h2' ); ?></h2>
			<p><?php echo wp_kses( pm_t( 'rio.fuentes.p', pm_rio_enlaces() ), [ 'a' => [ 'href' => true, 'rel' => true ] ] ); ?></p>
			<p><a href="<?php echo esc_url( pm_url_pique( $idioma ) ); ?>"><?php pm_e( 'rio.pique' ); ?></a></p>
			<p><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'rio.cta' ); ?></a><?php pm_cta_nota(); ?></p>
		</div>
	</div>
</div>
<?php get_footer();
