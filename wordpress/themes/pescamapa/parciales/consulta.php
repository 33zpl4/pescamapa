<?php
/**
 * Quote request for a destination guide. Every inquiry comes to Pescamapa
 * (pescamapa-core/inc/consultas.php). $args: destino (clave), lugar, especies (claves).
 */
defined( 'ABSPATH' ) || exit;

$resultado = pm_consulta_resultado();
?>
<section id="consulta" class="consulta" aria-labelledby="consulta-h">
	<h2 id="consulta-h"><?php pm_e( 'consulta.h2' ); ?></h2>
	<p><?php pm_e( 'consulta.p', [ 'lugar' => $args['lugar'] ] ); ?></p>
	<?php if ( 'ok' === $resultado ) : ?>
		<p class="aviso ok" role="status"><?php pm_e( 'consulta.ok' ); ?></p>
	<?php else : ?>
		<?php if ( 'error' === $resultado ) : ?><p class="aviso error" role="alert"><?php pm_e( 'consulta.error' ); ?></p><?php endif; ?>
		<form method="post" action="<?php echo esc_url( pm_consulta_url_envio() ); ?>">
			<?php echo pm_consulta_ocultos( 'viaje', [ 'pm_destino' => $args['destino'], 'pm_prestador' => '' ] ); // phpcs:ignore -- escaped inside ?>
			<p class="sobre" hidden><?php pm_e( 'consulta.sobre' ); ?> <strong></strong></p>
			<div class="grilla">
				<label><?php pm_e( 'consulta.nombre' ); ?> <input name="nombre" required maxlength="80" autocomplete="name"></label>
				<label><?php pm_e( 'consulta.contacto' ); ?> <input name="contacto" required maxlength="120" autocomplete="tel"></label>
				<label><?php pm_e( 'consulta.cuando' ); ?> <input name="cuando" maxlength="80"></label>
				<label><?php pm_e( 'consulta.personas' ); ?> <input name="personas" type="number" min="1" max="99" value="2" inputmode="numeric"></label>
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
			<label><?php pm_e( 'consulta.mensaje' ); ?> <textarea name="mensaje" rows="3" maxlength="2000"></textarea></label>
			<button class="button dark" type="submit"><?php pm_e( 'consulta.enviar' ); ?></button>
			<p class="note"><?php pm_e( 'consulta.privacidad' ); ?> <?php echo pm_enlace_privacidad(); // phpcs:ignore -- escaped inside ?></p>
		</form>
	<?php endif; ?>
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
