<?php
/** Community bite-zone map: /pique/, /pt/pique/, /en/bite-zones/ (data and rules: pescamapa-core/inc/pique.php, docs/pique.md). */
defined( 'ABSPATH' ) || exit;

$idioma  = pm_lang();
$celdas  = pm_pique_celdas_publicas();
$resumen = pm_pique_resumen();
$destinos = array_map( fn( $d ) => [ 'nombre' => $d['nombre'], 'coordenadas' => $d['coordenadas'] ], pm_destinos_lista( $idioma ) );
$hoy     = wp_date( 'Y-m-d' );
$desde   = wp_date( 'Y-m-d', time() - PM_PIQUE_DIAS_ATRAS * DAY_IN_SECONDS );
$textos  = [];
foreach ( [ 'ubicacion', 'sinubicacion', 'ir', 'ir.elegi', 'zona', 'zonanada', 'gracias', 'error', 'limite', 'popup.reportes', 'popup.reporte1', 'popup.bueno', 'popup.ultimo' ] as $k ) {
	$textos[ $k ] = pm_t( "pique.{$k}" );
}
wp_enqueue_style( 'leaflet' );
wp_enqueue_script( 'pm-pique' );

get_header(); ?>
<div class="v-pique">
	<div class="shell wrap">
		<h1><?php pm_e( 'pique.h1' ); ?></h1>
		<p class="lead"><?php pm_e( 'pique.intro' ); ?></p>
		<p class="resumen" role="status">
			<?php if ( $resumen['reportes'] ) {
				pm_e( 1 === $resumen['reportes'] && 1 === $resumen['zonas'] ? 'pique.resumen1' : 'pique.resumen', [ 'reportes' => $resumen['reportes'], 'zonas' => $resumen['zonas'], 'fecha' => pm_fecha( $resumen['ultimo'] ) ] );
			} else {
				pm_e( 'pique.vacio' );
			} ?>
		</p>
		<div class="pique-mapa" role="region" aria-label="<?php echo esc_attr( pm_t( 'pique.etiqueta' ) ); ?>" data-celdas="<?php echo esc_attr( wp_json_encode( $celdas ) ); ?>" data-destinos="<?php echo esc_attr( wp_json_encode( array_values( $destinos ) ) ); ?>" data-textos="<?php echo esc_attr( wp_json_encode( $textos ) ); ?>" data-envio="<?php echo esc_url( pm_pique_envio_url() ); ?>"></div>
		<noscript><p class="note"><?php pm_e( 'mapa.sinjs' ); ?></p></noscript>
		<p class="note"><?php pm_e( 'pique.leyenda' ); ?></p>
		<p class="aviso-pique"><?php pm_e( 'pique.aviso' ); ?></p>

		<section class="consulta pique-form" aria-labelledby="pique-h">
			<h2 id="pique-h"><?php pm_e( 'pique.h2' ); ?></h2>
			<p><?php pm_e( 'pique.paso1' ); ?></p>
			<p><button type="button" class="pique-geo button dark"><?php pm_e( 'pique.ubicacion' ); ?></button></p>
			<p class="pique-zona note" aria-live="polite" data-vacio="<?php echo esc_attr( pm_t( 'pique.zonanada' ) ); ?>"><?php pm_e( 'pique.zonanada' ); ?></p>
			<p class="aviso" role="status" hidden></p>
			<form method="post" action="<?php echo esc_url( pm_pique_envio_url() ); ?>">
				<?php echo pm_pique_ocultos(); // phpcs:ignore -- escaped inside ?>
				<input type="hidden" name="celda_lat" value="">
				<input type="hidden" name="celda_lon" value="">
				<div class="grilla">
					<label><?php pm_e( 'pique.especie' ); ?>
						<select name="especie" required>
							<?php foreach ( PM_PIQUE_ESPECIES as $k ) : ?>
								<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( pm_etiqueta( $k ) ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<label><?php pm_e( 'pique.dia' ); ?>
						<input type="date" name="dia" required value="<?php echo esc_attr( $hoy ); ?>" min="<?php echo esc_attr( $desde ); ?>" max="<?php echo esc_attr( $hoy ); ?>">
					</label>
				</div>
				<fieldset class="resultados">
					<legend><?php pm_e( 'pique.resultado' ); ?></legend>
					<?php foreach ( PM_PIQUE_RESULTADOS as $i => $r ) : ?>
						<label><input type="radio" name="resultado" value="<?php echo esc_attr( $r ); ?>"<?php echo 0 === $i ? ' checked' : ''; ?>> <?php pm_e( "pique.res.{$r}" ); ?></label>
					<?php endforeach; ?>
				</fieldset>
				<button type="submit" class="button dark" disabled><?php pm_e( 'pique.enviar' ); ?></button>
				<p class="note"><?php pm_e( 'pique.privacidad' ); ?></p>
			</form>
		</section>
		<p><a href="<?php echo esc_url( pm_url_mapa( $idioma ) ); ?>"><?php pm_e( 'pique.mapa' ); ?></a></p>
	</div>
</div>
<?php get_footer();
