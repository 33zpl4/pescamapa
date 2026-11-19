<?php
/** Full map page: /mapa/, /pt/mapa/, /en/map/. */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$destinos = pm_destinos_lista( $idioma );
$proximos = pm_proximos_lista( $idioma );
$pins     = [];
foreach ( $destinos as $d ) {
	$pins[] = [ 'nombre' => $d['nombre'], 'coordenadas' => $d['coordenadas'], 'url' => $d['url'], 'detalle' => implode( ' · ', array_filter( [ PM_PROVINCIAS[ $d['provincia'] ] ?? '', pm_especies_linea( $d ) ] ) ), 'tipo' => 'destino' ];
}
foreach ( $proximos as $p ) {
	$pins[] = [ 'nombre' => $p['nombre'], 'coordenadas' => $p['coordenadas'], 'detalle' => $p['provincia'], 'tipo' => 'proximo' ];
}

get_header(); ?>
<div class="v-mapa">
	<div class="shell wrap">
		<h1><?php pm_e( 'mapa.h1' ); ?></h1>
		<p class="note"><?php pm_e( 'mapa.leyenda' ); ?></p>
		<?php echo pm_mapa_interactivo( $pins, pm_t( 'mapa.etiqueta' ), [ 'alto' => 'min(75vh, 720px)' ] ); // phpcs:ignore ?>
		<h2><?php pm_e( 'mapa.destinos' ); ?></h2>
		<ul class="lista">
			<?php foreach ( $destinos as $d ) : ?>
				<li><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] ); ?></a>, <?php echo esc_html( PM_PROVINCIAS[ $d['provincia'] ] ?? '' ); ?></li>
			<?php endforeach; ?>
			<?php foreach ( $proximos as $p ) : ?>
				<li><?php echo esc_html( "{$p['nombre']}, {$p['provincia']}" ); ?> <span class="note"><?php pm_e( 'mapa.proximamente' ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php get_footer();
