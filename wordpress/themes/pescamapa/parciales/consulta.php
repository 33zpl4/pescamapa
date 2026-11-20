<?php
/**
 * Quote request for a destination guide. Every inquiry comes to Pescamapa
 * (pescamapa-core/inc/consultas.php). $args: destino (clave), lugar, especies (claves).
 */
defined( 'ABSPATH' ) || exit;

$resultado = pm_consulta_resultado();
?>
<?php $boutique = ! empty( $args['boutique'] ); ?>
<section id="consulta" class="consulta<?php echo $boutique ? ' consulta--boutique' : ''; ?>" aria-labelledby="consulta-h">
	<?php if ( $boutique ) : ?>
		<div class="pitch">
			<span class="eyebrow"><?php pm_e( 'consulta.boutique.eyebrow' ); ?></span>
			<h2 id="consulta-h"><?php pm_e( 'consulta.boutique.h2' ); ?></h2>
			<p><?php pm_e( 'consulta.p', [ 'lugar' => $args['lugar'] ] ); ?></p>
			<ul class="ticks">
				<li><?php pm_e( 'consulta.boutique.t1' ); ?></li>
				<li><?php pm_e( 'consulta.boutique.t2' ); ?></li>
				<li><?php pm_e( 'consulta.boutique.t3' ); ?></li>
			</ul>
		</div>
	<?php else : ?>
		<h2 id="consulta-h"><?php pm_e( 'consulta.h2', [ 'lugar' => $args['lugar'] ] ); ?></h2>
		<p><?php pm_e( 'consulta.p', [ 'lugar' => $args['lugar'] ] ); ?></p>
	<?php endif; ?>
	<div class="formulario">
	<?php if ( 'ok' === $resultado ) : ?>
		<p class="aviso ok" role="status"><?php pm_e( 'consulta.ok.pescador' ); ?></p>
	<?php else : ?>
		<?php if ( 'error' === $resultado ) : ?><p class="aviso error" role="alert"><?php pm_e( 'consulta.error' ); ?></p><?php endif; ?>
		<form class="pm-form" method="post" action="<?php echo esc_url( pm_consulta_url_envio() ); ?>">
			<?php echo pm_consulta_ocultos( 'viaje', [ 'pm_destino' => $args['destino'], 'pm_prestador' => '' ] ); // phpcs:ignore -- escaped inside ?>
			<p class="sobre" hidden><?php pm_e( 'consulta.sobre' ); ?> <strong></strong><br><?php pm_e( 'consulta.sobre.linea' ); ?></p>
			<div class="grilla">
				<label><?php pm_e( 'consulta.nombre' ); ?> <input name="nombre" required maxlength="80" autocomplete="name" value="<?php echo esc_attr( pm_previo( 'nombre' ) ); ?>"></label>
				<label><?php pm_e( 'consulta.contacto' ); ?> <input name="contacto" required maxlength="120" autocomplete="tel" value="<?php echo esc_attr( pm_previo( 'contacto' ) ); ?>"<?php echo $boutique ? ' placeholder="' . esc_attr( pm_t( 'consulta.ph.contacto' ) ) . '"' : ''; ?>></label>
				<label><?php pm_e( 'consulta.pais' ); ?> <input name="pais" maxlength="60" autocomplete="country-name" value="<?php echo esc_attr( pm_previo( 'pais' ) ); ?>"></label>
				<label><?php pm_e( 'consulta.modalidad' ); ?>
					<select name="modalidad">
						<option value=""><?php pm_e( 'consulta.indistinto' ); ?></option>
						<?php foreach ( array_keys( pm_ui()['opciones']['modalidad'] ) as $m ) : ?>
							<option value="<?php echo esc_attr( $m ); ?>"><?php echo esc_html( pm_opcion( 'modalidad', $m ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label><?php pm_e( 'consulta.especie' ); ?>
					<select name="especie">
						<option value=""><?php pm_e( 'consulta.indistinto' ); ?></option>
						<?php foreach ( $args['especies'] as $clave ) :
							$e = pm_especie_en( $clave, pm_lang() );
							if ( $e ) : ?>
								<option value="<?php echo esc_attr( $clave ); ?>"><?php echo esc_html( $e['nombre'] ); ?></option>
							<?php endif;
						endforeach; ?>
					</select>
				</label>
			</div>
			<?php
			// Same fields as the long form (/armar-viaje/): dates with the system calendar, how flexible, anglers and companions.
			pm_campos_fechas( false, 'consulta' );
			pm_campos_personas( 'consulta', false );
			// Optional budget per angler per day: same list, same server whitelist and same meta as the long form (pm_consulta in consultas.php).
			pm_chips( 'presupuesto', 'presupuesto', pm_t( 'viaje.presupuesto' ), pm_previo( 'presupuesto' ), false, pm_t( 'viaje.presupuesto.ayuda' ), null, 'consulta' );
			?>
			<label><?php pm_e( 'consulta.mensaje' ); ?> <textarea name="mensaje" rows="3" maxlength="2000"><?php echo esc_textarea( pm_previo( 'mensaje' ) ); ?></textarea></label>
			<button class="button dark" type="submit"><?php pm_e( $boutique ? 'consulta.boutique.enviar' : 'consulta.enviar' ); ?></button>
			<?php pm_cta_nota(); ?>
			<p class="note"><?php pm_e( 'consulta.privacidad' ); ?> <?php echo pm_enlace_privacidad(); // phpcs:ignore -- escaped inside ?></p>
		</form>
	<?php endif; ?>
	</div>
</section>
<script>
// "Consultar" on an outfitter card: fill the form with who it is about.
document.querySelectorAll('[data-prestador]').forEach((b) => b.addEventListener('click', () => {
	const f = document.querySelector('#consulta form');
	if (!f) return;
	f.pm_prestador.value = b.dataset.prestador;
	const s = f.querySelector('.sobre');
	s.hidden = false;
	s.querySelector('strong').textContent = b.dataset.nombre;
}));
</script>
<?php if ( 'ok' !== $resultado ) { get_template_part( 'parciales/viaje-js' ); } ?>
