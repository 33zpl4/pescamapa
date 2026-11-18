<?php
/**
 * About: who makes Pescamapa, how the data is researched and how the site pays its way. Texts ns.* in data/ui.json.
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
		<section aria-labelledby="que-h">
			<h2 id="que-h"><?php pm_e( 'ns.que.h' ); ?></h2>
			<p><?php pm_e( 'marca.definicion' ); ?></p>
			<p><?php pm_e( 'marca.saber' ); ?></p>
		</section>
		<section aria-labelledby="porque-h">
			<h2 id="porque-h"><?php pm_e( 'ns.porque.h' ); ?></h2>
			<p><?php pm_e( 'ns.porque.p' ); ?></p>
		</section>
		<section aria-labelledby="personas-h">
			<h2 id="personas-h"><?php pm_e( 'ns.personas.h' ); ?></h2>
			<?php foreach ( [ 1, 2 ] as $n ) : ?>
				<h3><?php pm_e( "ns.p$n.nombre" ); ?></h3>
				<p><?php pm_e( "ns.p$n.texto" ); ?></p>
			<?php endforeach; ?>
		</section>
		<section aria-labelledby="como-h">
			<h2 id="como-h"><?php pm_e( 'ns.como.h' ); ?></h2>
			<ul><?php foreach ( [ 1, 2, 3 ] as $n ) : ?><li><?php pm_e( "ns.como.$n" ); ?></li><?php endforeach; ?></ul>
		</section>
		<section aria-labelledby="goya-h">
			<h2 id="goya-h"><?php pm_e( 'ns.goya.h' ); ?></h2>
			<p><?php pm_e( 'ns.goya.p' ); ?></p>
		</section>
		<section id="como-funciona" aria-labelledby="plata-h">
			<h2 id="plata-h"><?php pm_e( 'ns.plata.h' ); ?></h2>
			<p><?php pm_e( 'ns.plata.p' ); ?></p>
			<p><?php pm_e( 'ns.plata.p2' ); ?></p>
		</section>
		<section class="respuesta" aria-labelledby="contacto-h">
			<h2 id="contacto-h"><?php pm_e( 'ns.contacto.h' ); ?></h2>
			<p><?php pm_e( 'ns.contacto.p' ); ?> <a href="mailto:<?php echo esc_attr( PM_EMAIL_PUBLICO ); ?>"><?php echo esc_html( PM_EMAIL_PUBLICO ); ?></a></p>
			<p><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'ns.contacto.viaje' ); ?></a></p>
		</section>
	</div>
</div>
<?php get_footer();
