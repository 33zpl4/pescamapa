<?php
/**
 * Trip request: /armar-viaje/, /pt/planejar-viagem/, /en/plan-your-trip/.
 * Posts to the same handler as the guide forms (pescamapa-core/inc/consultas.php):
 * every request comes to Pescamapa and nothing here links to a named outfitter.
 * ?destino=<clave>&especie=<clave> preselect the fields (links from notes and guides).
 */
defined( 'ABSPATH' ) || exit;

$idioma    = pm_lang();
$resultado = pm_consulta_resultado();
$destinos  = pm_destinos_lista( $idioma );
$claves    = array_column( $destinos, 'clave' );
$destino   = sanitize_key( $_GET['destino'] ?? '' );
$destino   = in_array( $destino, $claves, true ) ? $destino : '';
$especies  = [];
foreach ( pm_especies_lista( $idioma ) as $e ) {
	$especies[ $e['clave'] ] = $e['nombre'];
}
$especie = sanitize_key( $_GET['especie'] ?? '' );
$especie = isset( $especies[ $especie ] ) ? $especie : '';

/** A group of radio "chips" for one option list. */
$chips = function ( string $campo, string $leyenda, ?string $valor = null ): void {
	$claves = array_keys( pm_ui()['opciones'][ $campo ] );
	echo '<fieldset class="opciones"><legend>' . esc_html( $leyenda ) . '</legend><div class="chips-radio">';
	foreach ( $claves as $k ) {
		$id = "viaje-{$campo}-{$k}";
		printf(
			'<span><input type="radio" name="%1$s" id="%2$s" value="%3$s"%5$s><label for="%2$s">%4$s</label></span>',
			esc_attr( $campo ),
			esc_attr( $id ),
			esc_attr( $k ),
			esc_html( pm_opcion( $campo, $k ) ),
			checked( $valor, $k, false )
		);
	}
	echo '</div></fieldset>';
};

get_header(); ?>
<div class="v-destino v-viaje">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'viaje.eyebrow' ); ?></div>
			<h1><?php pm_e( 'viaje.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'viaje.bajada' ); ?></p>
		</div>
	</header>
	<div class="shell viaje-layout<?php echo 'ok' === $resultado ? ' solo' : ''; ?>">
		<section id="consulta" class="consulta viaje-form" aria-labelledby="viaje-h">
			<h2 id="viaje-h" class="visually-hidden"><?php pm_e( 'viaje.h1' ); ?></h2>
			<?php if ( 'ok' === $resultado ) : ?>
				<div class="gracias" role="status">
					<h2><?php pm_e( 'viaje.gracias.h' ); ?></h2>
					<p><?php pm_e( 'viaje.gracias.p' ); ?></p>
					<p><?php pm_e( 'viaje.gracias.mas' ); ?></p>
					<p><a class="button dark" href="<?php echo esc_url( pm_url_inicio( $idioma ) . '#destinos' ); ?>"><?php pm_e( 'viaje.gracias.ver' ); ?></a></p>
				</div>
			<?php else : ?>
				<?php if ( 'error' === $resultado ) : ?><p class="aviso error" role="alert"><?php pm_e( 'viaje.error' ); ?></p><?php endif; ?>
				<form method="post" action="<?php echo esc_url( pm_consulta_url_envio() ); ?>" data-enviando="<?php echo esc_attr( pm_t( 'viaje.enviando' ) ); ?>">
					<?php echo pm_consulta_ocultos( 'viaje' ); // phpcs:ignore -- escaped inside ?>

					<fieldset class="paso">
						<legend><span class="num" aria-hidden="true">1</span> <?php pm_e( 'viaje.paso1' ); ?></legend>
						<div class="grilla">
							<label><?php pm_e( 'viaje.destino' ); ?>
								<select name="pm_destino">
									<option value=""><?php pm_e( 'viaje.destino.nose' ); ?></option>
									<?php foreach ( $destinos as $d ) : ?>
										<option value="<?php echo esc_attr( $d['clave'] ); ?>"<?php selected( $destino, $d['clave'] ); ?>><?php echo esc_html( $d['nombre'] . ', ' . ( PM_PROVINCIAS[ $d['provincia'] ] ?? '' ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
							<label><?php pm_e( 'viaje.cuando' ); ?>
								<input name="cuando" maxlength="80" placeholder="<?php echo esc_attr( pm_t( 'viaje.cuando.ejemplo' ) ); ?>">
								<small><?php pm_e( 'viaje.cuando.ayuda' ); ?></small>
							</label>
							<label><?php pm_e( 'viaje.duracion' ); ?>
								<select name="duracion">
									<?php foreach ( array_keys( pm_ui()['opciones']['duracion'] ) as $k ) : ?>
										<option value="<?php echo esc_attr( $k ); ?>"<?php selected( 'nose', $k ); ?>><?php echo esc_html( pm_opcion( 'duracion', $k ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
							<label><?php pm_e( 'viaje.personas' ); ?>
								<input name="personas" type="number" min="1" max="99" value="2" inputmode="numeric">
							</label>
						</div>
					</fieldset>

					<fieldset class="paso">
						<legend><span class="num" aria-hidden="true">2</span> <?php pm_e( 'viaje.paso2' ); ?></legend>
						<div class="grilla">
							<label><?php pm_e( 'viaje.especie' ); ?>
								<select name="especie">
									<option value=""><?php pm_e( 'consulta.indistinto' ); ?></option>
									<?php foreach ( $especies as $k => $nombre ) : ?>
										<option value="<?php echo esc_attr( $k ); ?>"<?php selected( $especie, $k ); ?>><?php echo esc_html( $nombre ); ?></option>
									<?php endforeach; ?>
								</select>
							</label>
						</div>
						<?php
						$chips( 'modalidad', pm_t( 'viaje.modalidad' ) );
						$chips( 'experiencia', pm_t( 'viaje.experiencia' ) );
						$chips( 'alojamiento', pm_t( 'viaje.alojamiento' ) );
						?>
					</fieldset>

					<fieldset class="paso">
						<legend><span class="num" aria-hidden="true">3</span> <?php pm_e( 'viaje.paso3' ); ?></legend>
						<div class="grilla">
							<label><?php pm_e( 'viaje.nombre' ); ?>
								<input name="nombre" required maxlength="80" autocomplete="name">
							</label>
							<label><?php pm_e( 'viaje.contacto' ); ?>
								<input name="contacto" required maxlength="120" autocomplete="email">
								<small><?php pm_e( 'viaje.contacto.ayuda' ); ?></small>
							</label>
							<label><?php pm_e( 'viaje.pais' ); ?>
								<input name="pais" maxlength="60" autocomplete="country-name">
							</label>
						</div>
						<label><?php pm_e( 'viaje.mensaje' ); ?>
							<textarea name="mensaje" rows="4" maxlength="2000"></textarea>
							<small><?php pm_e( 'viaje.mensaje.ayuda' ); ?></small>
						</label>
					</fieldset>

					<div class="enviar">
						<button class="button primary" type="submit"><?php pm_e( 'viaje.enviar' ); ?></button>
						<p class="note"><?php pm_e( 'viaje.privacidad' ); ?> <?php echo pm_enlace_privacidad(); // phpcs:ignore -- escaped inside ?></p>
					</div>
				</form>
			<?php endif; ?>
		</section>

		<?php if ( 'ok' !== $resultado ) : ?>
		<aside class="viaje-como" aria-labelledby="como-h">
			<h2 id="como-h"><?php pm_e( 'viaje.como.h' ); ?></h2>
			<ol>
				<?php foreach ( [ 1, 2, 3 ] as $n ) : ?>
					<li>
						<strong><?php pm_e( "viaje.como.{$n}.t" ); ?></strong>
						<span><?php pm_e( "viaje.como.{$n}.d" ); ?></span>
					</li>
				<?php endforeach; ?>
			</ol>
		</aside>
		<?php endif; ?>
	</div>
</div>
<script>
// One tap, one request: the hourly limit would otherwise count a double click twice.
document.querySelectorAll('.viaje-form form').forEach((f) => f.addEventListener('submit', () => {
	const b = f.querySelector('button[type="submit"]');
	if (b) { b.disabled = true; b.textContent = f.dataset.enviando; }
}));
</script>
<?php get_footer();
