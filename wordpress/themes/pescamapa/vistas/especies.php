<?php
/** Species index: one card per species with its rule summary and the guides where it is fished. */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$especies = [];
foreach ( pm_especies_lista( 'es' ) as $base ) {
	$e = pm_especie_en( $base['clave'], $idioma );
	if ( $e ) {
		$e['destinos'] = pm_destinos_con_especie( $e['clave'], $idioma );
		$especies[]    = $e;
	}
}
usort( $especies, fn( $a, $b ) => strcoll( $a['nombre'], $b['nombre'] ) );

get_header(); ?>
<div class="v-destino v-especies">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'especies.eyebrow' ); ?></div>
			<h1><?php pm_e( 'especies.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'especies.description' ); ?></p>
		</div>
	</header>
	<div class="shell lista">
		<?php if ( ! $especies ) : ?>
			<p><?php pm_e( 'especies.vacio' ); ?></p>
		<?php endif; ?>
		<?php foreach ( $especies as $e ) : ?>
			<article class="tarjeta">
				<h2><a href="<?php echo esc_url( $e['url'] ); ?>"<?php echo $e['idioma'] !== $idioma ? ' hreflang="' . esc_attr( pm_meta_idioma( $e['idioma'] )['html'] ) . '"' : ''; ?>><?php echo esc_html( $e['nombre'] ); ?></a></h2>
				<?php if ( $e['cientifico'] ) : ?><span class="quien"><i><?php echo esc_html( $e['cientifico'] ); ?></i></span><?php endif; ?>
				<?php if ( $e['resumen'] ) : ?><p><?php echo esc_html( $e['resumen'] ); ?></p><?php endif; ?>
				<?php if ( $e['destinos'] ) : ?>
					<div class="donde">
						<span class="quien"><?php pm_e( 'especies.donde' ); ?></span>
						<ul class="chips">
							<?php foreach ( $e['destinos'] as $d ) : ?>
								<li><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</article>
		<?php endforeach; ?>
	</div>
</div>
<?php get_footer();
