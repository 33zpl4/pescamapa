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

$errores = pm_consulta_previo()['errores'];
$v       = 'pm_previo';
// Species / destination: what came back after an error wins over the URL preselection.
$destino = $v( 'pm_destino' ) ?: $destino;
$especie = $v( 'especie' ) ?: $especie;
$via     = 'whatsapp' === $v( 'contacto_via' ) ? 'whatsapp' : 'email';

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
				<?php if ( 'error' === $resultado ) : ?>
					<p class="aviso error" role="alert"><?php echo esc_html( in_array( 'limite', $errores, true ) ? pm_t( 'viaje.err.limite' ) : pm_t( $errores && ! in_array( 'general', $errores, true ) ? 'viaje.error.campos' : 'viaje.error' ) ); ?></p>
				<?php endif; ?>
				<form class="pm-form" method="post" action="<?php echo esc_url( pm_consulta_url_envio() ); ?>" data-enviando="<?php echo esc_attr( pm_t( 'viaje.enviando' ) ); ?>">
					<?php echo pm_consulta_ocultos( 'viaje' ); // phpcs:ignore -- escaped inside ?>
					<p class="intro-form"><?php pm_e( 'viaje.intro' ); ?></p>

					<fieldset class="paso">
						<legend><span class="num" aria-hidden="true">1</span> <?php pm_e( 'viaje.paso1' ); ?></legend>
						<div class="grilla">
							<div class="campo"><label for="viaje-destino"><?php pm_e( 'viaje.destino' ); ?></label>
								<select id="viaje-destino" name="pm_destino">
									<option value=""><?php pm_e( 'viaje.destino.nose' ); ?></option>
									<?php foreach ( $destinos as $d ) : ?>
										<option value="<?php echo esc_attr( $d['clave'] ); ?>"<?php selected( $destino, $d['clave'] ); ?>><?php echo esc_html( $d['nombre'] . ', ' . ( PM_PROVINCIAS[ $d['provincia'] ] ?? '' ) ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="campo"><label for="viaje-especie"><?php pm_e( 'viaje.especie' ); ?></label>
								<select id="viaje-especie" name="especie">
									<option value=""><?php pm_e( 'consulta.indistinto' ); ?></option>
									<?php foreach ( $especies as $k => $nombre ) : ?>
										<option value="<?php echo esc_attr( $k ); ?>"<?php selected( $especie, $k ); ?>><?php echo esc_html( $nombre ); ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<?php
						pm_chips( 'fijo', 'fijo', pm_t( 'viaje.fijo' ), pm_previo_lista( 'fijo' ), true, pm_t( 'viaje.fijo.ayuda' ), [ 'especie', 'lugar' ] );
						?>
					</fieldset>

					<fieldset class="paso">
						<legend><span class="num" aria-hidden="true">2</span> <?php pm_e( 'viaje.paso2' ); ?></legend>
						<?php
						pm_campos_fechas( true );
						pm_chips( 'estadia', 'estadia', pm_t( 'viaje.estadia' ), $v( 'estadia' ) );
						pm_campos_personas();
						pm_chips( 'traeequipo', 'equipo_propio', pm_t( 'viaje.equipo' ), $v( 'equipo_propio' ) );
						?>
						<?php // Optional questions: closed by default, open when one has a value (data restored after an error). ?>
						<details class="afinar"<?php echo $v( 'modalidad' ) . $v( 'alojamiento' ) . $v( 'experiencia' ) ? ' open' : ''; ?>>
							<summary><?php pm_e( 'viaje.afinar' ); ?></summary>
							<div class="afinar-campos">
								<?php
								pm_chips( 'modalidad', 'modalidad', pm_t( 'viaje.modalidad' ), $v( 'modalidad' ) );
								pm_chips( 'alojamiento', 'alojamiento', pm_t( 'viaje.alojamiento' ), $v( 'alojamiento' ) );
								pm_chips( 'experiencia', 'experiencia', pm_t( 'viaje.experiencia' ), $v( 'experiencia' ) );
								?>
							</div>
						</details>
					</fieldset>

					<fieldset class="paso">
						<legend><span class="num" aria-hidden="true">3</span> <?php pm_e( 'viaje.paso3' ); ?></legend>
						<div class="grilla">
							<div class="campo"><label for="viaje-nombre"><?php pm_e( 'viaje.nombre' ); ?></label>
								<input id="viaje-nombre" name="nombre" required maxlength="80" autocomplete="name" value="<?php echo esc_attr( $v( 'nombre' ) ); ?>"<?php echo in_array( 'nombre', $errores, true ) ? ' aria-invalid="true" aria-describedby="err-nombre"' : ''; ?>>
								<?php echo pm_error_campo( 'nombre', 'err-nombre' ); // phpcs:ignore -- escaped inside ?>
							</div>
							<div class="campo"><label for="viaje-pais"><?php pm_e( 'viaje.pais' ); ?></label>
								<input id="viaje-pais" name="pais" maxlength="60" autocomplete="country-name" value="<?php echo esc_attr( $v( 'pais' ) ); ?>">
							</div>
						</div>
						<fieldset class="opciones contacto-via">
							<legend><?php pm_e( 'viaje.contacto' ); ?></legend>
							<div class="chips-radio">
								<?php foreach ( [ 'email', 'whatsapp' ] as $c ) : ?>
									<span><input type="radio" name="contacto_via" id="viaje-via-<?php echo esc_attr( $c ); ?>" value="<?php echo esc_attr( $c ); ?>"<?php checked( $via, $c ); ?>><label for="viaje-via-<?php echo esc_attr( $c ); ?>"><?php pm_e( "viaje.via.{$c}" ); ?></label></span>
								<?php endforeach; ?>
							</div>
						</fieldset>
						<div class="contacto-campos">
							<div class="campo c-email"><label for="viaje-email"><?php pm_e( 'viaje.email' ); ?></label>
								<input id="viaje-email" name="contacto_email" type="email" inputmode="email" autocomplete="email" maxlength="120" autocapitalize="none" spellcheck="false" value="<?php echo esc_attr( $v( 'contacto_email' ) ); ?>"<?php echo pm_consulta_tiene_error( 'contacto_email' ) || ( 'email' === $via && pm_consulta_tiene_error( 'contacto' ) ) ? ' aria-invalid="true" aria-describedby="err-email"' : ''; ?>>
								<?php echo pm_error_campo( 'contacto_email', 'err-email' ) . ( 'email' === $via ? pm_error_campo( 'contacto', 'err-email' ) : '' ); // phpcs:ignore -- escaped inside ?>
							</div>
							<div class="campo c-tel"><label for="viaje-tel"><?php pm_e( 'viaje.whatsapp' ); ?></label>
								<input id="viaje-tel" name="contacto_tel" type="tel" inputmode="tel" autocomplete="tel" maxlength="30" placeholder="+54 9 11 …" value="<?php echo esc_attr( $v( 'contacto_tel' ) ); ?>"<?php echo pm_consulta_tiene_error( 'contacto_tel' ) || ( 'whatsapp' === $via && pm_consulta_tiene_error( 'contacto' ) ) ? ' aria-invalid="true" aria-describedby="err-tel"' : ''; ?>>
								<small><?php pm_e( 'viaje.contacto.ayuda' ); ?></small>
								<?php echo pm_error_campo( 'contacto_tel', 'err-tel' ) . ( 'whatsapp' === $via ? pm_error_campo( 'contacto', 'err-tel' ) : '' ); // phpcs:ignore -- escaped inside ?>
							</div>
						</div>
						<div class="campo"><label for="viaje-mensaje"><?php pm_e( 'viaje.mensaje' ); ?></label>
							<textarea id="viaje-mensaje" name="mensaje" rows="4" maxlength="2000" aria-describedby="viaje-mensaje-a"><?php echo esc_textarea( $v( 'mensaje' ) ); ?></textarea>
							<small id="viaje-mensaje-a"><?php pm_e( 'viaje.mensaje.ayuda' ); ?></small>
						</div>
					</fieldset>

					<div class="enviar">
						<button class="button primary" type="submit"><?php pm_e( 'viaje.enviar' ); ?></button>
						<p class="garantia"><?php pm_e( 'viaje.garantia' ); ?></p>
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
<?php if ( 'ok' !== $resultado ) : ?>
<div class="v-viaje v-viaje-mas">
	<div class="shell">
		<?php get_template_part( 'parciales/contraste', null, [ 'id' => 'contraste-h' ] ); ?>
		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'viaje.faq.h' ); ?></h2>
			<?php foreach ( [ 1, 2, 3, 4 ] as $n ) : ?>
				<h3><?php pm_e( "viaje.faq.$n.q" ); ?></h3>
				<p><?php pm_e( "viaje.faq.$n.a" ); ?></p>
			<?php endforeach; ?>
		</section>
	</div>
</div>
<?php endif; ?>
<?php get_template_part( 'parciales/viaje-js' ); ?>
<?php get_footer();
