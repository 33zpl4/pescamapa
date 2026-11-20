<?php
/**
 * The question with buttons (home, top and bottom; 14/11/2026): which fish -> the existing trip form, species preselected
 * with ?especie=<clave> (vistas/viaje.php reads it; an unknown key is ignored). Second path: only the rules -> the calendar
 * page (rules and closed seasons per destination). Args: $args['id'] makes the heading id unique.
 */
defined( 'ABSPATH' ) || exit;
$idioma = pm_lang();
$id     = 'pregunta-' . ( $args['id'] ?? 'a' );
$viaje  = pm_url_viaje( $idioma );
?>
<div class="pregunta" role="group" aria-labelledby="<?php echo esc_attr( $id ); ?>">
	<p class="pregunta-h" id="<?php echo esc_attr( $id ); ?>"><?php pm_e( 'inicio.q.h' ); ?></p>
	<ul class="opciones">
		<?php foreach ( [ 'dorado', 'trucha', 'surubi' ] as $clave ) : ?>
			<li><a class="button primary" href="<?php echo esc_url( add_query_arg( 'especie', $clave, $viaje ) ); ?>"><?php echo esc_html( pm_etiqueta( $clave ) ); ?></a></li>
		<?php endforeach; ?>
		<li><a class="button primary" href="<?php echo esc_url( $viaje ); ?>"><?php pm_e( 'inicio.q.nose' ); ?></a></li>
	</ul>
	<?php if ( 'arriba' !== ( $args['id'] ?? '' ) ) { pm_cta_nota(); } // the hero already says «Gratis y sin compromiso» (inicio.copy) ?>
	<p class="solo"><a href="<?php echo esc_url( pm_url_calendario( $idioma ) ); ?>"><?php pm_e( 'inicio.q.reglas' ); ?></a> <span><?php pm_e( 'inicio.q.reglas.d' ); ?></span></p>
</div>
