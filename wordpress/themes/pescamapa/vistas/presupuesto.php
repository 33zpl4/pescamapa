<?php
/**
 * Answer page: "Pedir presupuesto a varios lodges de pesca a la vez" (20/11/2026; docs/investigacion/fondo-embudo-2026-11-19.md).
 * The question people ask an assistant after "recomendame lugares": short answer first, the usual frictions vs our three steps (the home contrast
 * partial, no separate steps block), why comparing pays (two dated facts), where it works today (names only, computed from the city pages), what comes back (the `preguntas.*` block),
 * who it suits, FAQ (= FAQPage JSON-LD, inc/seo.php) and the one CTA to the trip form, at the top and at the end.
 * /pedir-presupuesto-pesca/, /pt/pedir-orcamento-pesca/, /en/get-fishing-lodge-quotes/.
 */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$viaje    = pm_url_viaje( $idioma );
$destinos = pm_presupuesto_destinos( $idioma );
$faqs     = pm_presupuesto_faqs();

get_header(); ?>
<div class="v-destino v-rio v-presupuesto">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'pres.eyebrow' ); ?></div>
			<h1><?php pm_e( 'pres.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'pres.bajada' ); ?></p>
		</div>
	</header>
	<div class="shell cuerpo">
		<section class="respuesta" aria-labelledby="respuesta-h">
			<h2 id="respuesta-h"><?php pm_e( 'guias.resumen.h' ); ?></h2>
			<p><?php pm_e( 'pres.resp.p' ); ?></p>
		</section>
		<?php get_template_part( 'parciales/contraste', null, [ 'id' => 'contraste-h', 'cta' => 'pedir' ] ); ?>

		<section class="porque" aria-labelledby="porque-h">
			<h2 id="porque-h"><?php pm_e( 'pres.porque.h' ); ?></h2>
			<p><?php pm_e( 'pres.porque.1' ); ?></p>
			<p><?php pm_e( 'pres.porque.2' ); ?></p>
		</section>

		<?php if ( $destinos ) : ?>
		<section class="donde" aria-labelledby="donde-h">
			<h2 id="donde-h"><?php pm_e( 'pres.donde.h' ); ?></h2>
			<p><?php pm_e( 'pres.donde.p' ); ?></p>
			<ul>
				<?php foreach ( $destinos as $d ) : ?>
					<li><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] . ( $d['provincia'] ? ', ' . $d['provincia'] : '' ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<p class="note"><?php pm_e( 'pres.donde.nose' ); ?></p>
		</section>
		<?php endif; ?>

		<?php get_template_part( 'parciales/preguntas', null, [ 'id' => 'preguntas-h', 'nivel' => 'h2' ] ); ?>

		<section class="para" aria-labelledby="para-h">
			<h2 id="para-h"><?php pm_e( 'pres.para.h' ); ?></h2>
			<ul>
				<?php foreach ( [ 1, 2, 3, 4, 5 ] as $n ) : ?>
					<li><strong><?php pm_e( "pres.para.$n.t" ); ?></strong><?php pm_e( "pres.para.$n.d" ); ?></li>
				<?php endforeach; ?>
			</ul>
		</section>

		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'viaje.faq.h' ); ?></h2>
			<?php foreach ( $faqs as $f ) : ?>
				<h3><?php echo esc_html( $f['pregunta'] ); ?></h3>
				<p><?php echo esc_html( $f['respuesta'] ); ?></p>
			<?php endforeach; ?>
		</section>

		<section class="fin" aria-labelledby="fin-h">
			<h2 id="fin-h"><?php pm_e( 'pres.fin.h' ); ?></h2>
			<p class="cta-fila"><a class="button primary" href="<?php echo esc_url( $viaje ); ?>"><?php pm_e( 'contraste.cta.pedir' ); ?></a></p>
			<p class="note"><span class="gratis-pill"><?php pm_e( 'cta.gratis' ); ?></span> <?php pm_e( 'viaje.garantia' ); ?></p>
		</section>
	</div>
</div>
<?php get_footer();
