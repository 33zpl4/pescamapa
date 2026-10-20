<?php
defined( 'ABSPATH' ) || exit;
if ( ! function_exists( 'pm_t' ) ) {
	wp_die( 'Pescamapa: falta activar el plugin "Pescamapa — núcleo".', 'Pescamapa', [ 'response' => 503 ] );
}
$idioma  = pm_lang();
$seo     = pm_seo();
$oscuro  = 'inicio' === pm_vista();
$inicio  = pm_url_inicio( $idioma );
$alternas = $seo['alternas'];
?><!doctype html>
<html <?php language_attributes(); ?> data-build="<?php echo esc_attr( trim( (string) @file_get_contents( get_template_directory() . '/build.txt' ) ) ); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( $seo['borrador'] && ! is_404() ) : ?>
	<div class="borrador" role="note"><div class="shell"><strong><?php pm_e( 'borrador.titulo' ); ?></strong> <?php pm_e( 'borrador.texto' ); ?></div></div>
<?php endif; ?>
<header class="nav<?php echo $oscuro ? ' oscuro' : ''; ?>">
	<div class="shell inner">
		<a class="brand" href="<?php echo esc_url( $inicio ); ?>" aria-label="Pescamapa — <?php pm_e( 'ficha.inicio' ); ?>"><span class="mark" aria-hidden="true">↗</span> PESCAMAPA</a>
		<div class="right">
			<nav aria-label="Principal">
				<a class="opt" href="<?php echo esc_url( $inicio . '#destinos' ); ?>"><?php pm_e( 'nav.destinos' ); ?></a>
				<a class="opt" href="<?php echo esc_url( pm_url_mapa( $idioma ) ); ?>"><?php pm_e( 'nav.mapa' ); ?></a>
				<a class="cta" href="<?php echo esc_url( $inicio . '#prestadores' ); ?>"><?php pm_e( 'nav.prestadores' ); ?></a>
			</nav>
			<nav class="idiomas" aria-label="<?php pm_e( 'nav.idioma' ); ?>">
				<?php foreach ( pm_idiomas() as $i ) :
					$m = pm_meta_idioma( $i ); ?>
					<a href="<?php echo esc_url( $alternas[ $i ] ?? pm_url_inicio( $i ) ); ?>" hreflang="<?php echo esc_attr( $m['html'] ); ?>" lang="<?php echo esc_attr( $m['html'] ); ?>" title="<?php echo esc_attr( $m['nombre'] ); ?>"<?php echo $i === $idioma ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $m['corto'] ); ?></a>
				<?php endforeach; ?>
			</nav>
		</div>
	</div>
</header>
<main id="contenido">
