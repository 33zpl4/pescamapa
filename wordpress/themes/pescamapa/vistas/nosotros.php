<?php
/**
 * About: what Pescamapa is and why it exists, how it works and how the site pays its way. Texts ns.* in data/ui.json.
 * /quienes-somos/, /pt/quem-somos/, /en/about/. No photos yet (owner, 9/11/2026).
 */
defined( 'ABSPATH' ) || exit;

$idioma = pm_lang();
get_header(); ?>
<div class="v-destino v-rio v-paraguias v-nosotros">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'ns.eyebrow' ); ?></div>
			<h1><?php pm_e( 'ns.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'ns.bajada' ); ?></p>
		</div>
	</header>
	<div class="shell cuerpo">
		<section aria-labelledby="empezo-h">
			<h2 id="empezo-h"><?php pm_e( 'ns.empezo.h' ); ?></h2>
			<p><?php pm_e( 'ns.empezo.p' ); ?></p>
		</section>
		<section aria-labelledby="como-h">
			<h2 id="como-h"><?php pm_e( 'ns.como.h' ); ?></h2>
			<p><?php pm_e( 'ns.como.p' ); ?></p>
		</section>
		<section aria-labelledby="no-h">
			<h2 id="no-h"><?php pm_e( 'ns.no.h' ); ?></h2>
			<p><?php pm_e( 'ns.no.p' ); ?></p>
		</section>
		<section id="como-funciona" aria-labelledby="plata-h">
			<h2 id="plata-h"><?php pm_e( 'ns.plata.h' ); ?></h2>
			<p><?php pm_e( 'ns.plata.p' ); ?></p>
		</section>
		<section aria-labelledby="quien-h">
			<h2 id="quien-h"><?php pm_e( 'ns.quien.h' ); ?></h2>
			<p><?php pm_e( 'ns.quien.p' ); ?></p>
		</section>
		<section class="respuesta" aria-labelledby="contacto-h">
			<h2 id="contacto-h"><?php pm_e( 'ns.contacto.h' ); ?></h2>
			<p><?php pm_e( 'ns.contacto.p' ); ?> <a href="mailto:<?php echo esc_attr( PM_EMAIL_PUBLICO ); ?>"><?php echo esc_html( PM_EMAIL_PUBLICO ); ?></a></p>
			<p><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'ns.contacto.viaje' ); ?></a></p>
		</section>
	</div>
</div>
<?php get_footer();
