<?php
/**
 * For guides and lodges: what Pescamapa gives (listing in three languages, qualified inquiries, no fee), what it asks
 * (confirmed data, three own photos, same-day answers), how it works, correction/removal, FAQ and the "prestador" form
 * (same handler as the home section). /para-guias-y-lodges/, /pt/para-guias-e-lodges/, /en/for-guides-and-lodges/.
 */
defined( 'ABSPATH' ) || exit;

$idioma = pm_lang();
get_header(); ?>
<div class="v-destino v-rio v-paraguias">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'pg.eyebrow' ); ?></div>
			<h1><?php pm_e( 'pg.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'pg.bajada' ); ?></p>
		</div>
	</header>
	<div class="shell cuerpo">
		<div class="columnas">
			<section aria-labelledby="damos-h">
				<h2 id="damos-h"><?php pm_e( 'pg.damos.h' ); ?></h2>
				<ul><?php foreach ( [ 1, 2, 3 ] as $n ) : ?><li><?php pm_e( "pg.damos.$n" ); ?></li><?php endforeach; ?></ul>
			</section>
			<section aria-labelledby="pedimos-h">
				<h2 id="pedimos-h"><?php pm_e( 'pg.pedimos.h' ); ?></h2>
				<ul><?php foreach ( [ 1, 2, 3 ] as $n ) : ?><li><?php pm_e( "pg.pedimos.$n" ); ?></li><?php endforeach; ?></ul>
			</section>
		</div>
		<section aria-labelledby="como-h">
			<h2 id="como-h"><?php pm_e( 'pg.como.h' ); ?></h2>
			<ol><?php foreach ( [ 1, 2, 3 ] as $n ) : ?><li><?php pm_e( "pg.como.$n" ); ?></li><?php endforeach; ?></ol>
		</section>
		<section class="respuesta" aria-labelledby="ya-h">
			<h2 id="ya-h"><?php pm_e( 'pg.ya.h' ); ?></h2>
			<p><?php pm_e( 'pg.ya.p' ); ?> <a href="mailto:<?php echo esc_attr( PM_EMAIL_PUBLICO ); ?>?subject=<?php echo rawurlencode( pm_t( 'pg.ya.h' ) ); ?>"><?php echo esc_html( PM_EMAIL_PUBLICO ); ?></a></p>
		</section>
		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'pg.faq.h' ); ?></h2>
			<?php foreach ( [ 1, 2, 3, 4 ] as $n ) : ?>
				<h3><?php pm_e( "pg.faq.$n.q" ); ?></h3>
				<p><?php pm_e( "pg.faq.$n.a" ); ?></p>
			<?php endforeach; ?>
		</section>
		<section id="consulta" class="consulta" aria-labelledby="form-h">
			<h2 id="form-h"><?php pm_e( 'pg.form.h' ); ?></h2>
			<?php if ( 'ok' === pm_consulta_resultado() ) : ?>
				<p class="aviso ok" role="status"><?php pm_e( 'consulta.ok' ); ?></p>
			<?php else : ?>
				<?php if ( 'error' === pm_consulta_resultado() ) : ?><p class="aviso error" role="alert"><?php pm_e( 'consulta.error' ); ?></p><?php endif; ?>
				<form method="post" action="<?php echo esc_url( pm_consulta_url_envio() ); ?>">
					<?php echo pm_consulta_ocultos( 'prestador' ); // phpcs:ignore -- escaped inside ?>
					<div class="grid">
						<label><?php pm_e( 'consulta.nombre' ); ?> <input name="nombre" required maxlength="80" autocomplete="name"></label>
						<label><?php pm_e( 'consulta.contacto' ); ?> <input name="contacto" required maxlength="120" autocomplete="tel"></label>
						<label><?php pm_e( 'inicio.prest.localidad' ); ?> <input name="localidad" maxlength="80"></label>
						<label><?php pm_e( 'inicio.prest.ofrece' ); ?> <input name="ofrece" maxlength="200"></label>
					</div>
					<label><?php pm_e( 'pg.form.mensaje' ); ?> <textarea name="mensaje" rows="4" maxlength="2000"></textarea></label>
					<button class="button primary" type="submit"><?php pm_e( 'pg.form.enviar' ); ?></button>
					<p class="note"><?php pm_e( 'inicio.prest.privacidad' ); ?> <?php echo pm_enlace_privacidad(); // phpcs:ignore -- escaped inside ?></p>
				</form>
			<?php endif; ?>
		</section>
	</div>
</div>
<?php get_footer();
