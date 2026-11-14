<?php
/**
 * Note card for the home block and the /notas/ index (14/11/2026). One markup, so both grids look the same.
 * Args: nota (pm_nota row), idioma (page language), tipo (bool: show the note type).
 * With a photo: pm_foto() (4:3 via .tarjeta .foto). Without: a brand placeholder with the same 4:3 box (.foto-vacia), so a
 * grid never has cards with an image next to cards without one at different heights. The placeholder is decoration
 * (aria-hidden); the title carries the meaning.
 */
defined( 'ABSPATH' ) || exit;

$n      = $args['nota'] ?? [];
$idioma = $args['idioma'] ?? pm_lang();
if ( ! $n ) {
	return;
}
?>
<a class="tarjeta" href="<?php echo esc_url( $n['url'] ); ?>"<?php echo $n['idioma'] !== $idioma ? ' hreflang="' . esc_attr( pm_meta_idioma( $n['idioma'] )['html'] ) . '"' : ''; ?>>
	<?php if ( pm_nota_tiene_foto( $n ) ) : ?>
		<?php echo pm_foto( $n['foto'], pm_alt_foto( (int) $n['foto'], $n['idioma'], $n['titulo'] ), '(max-width: 720px) 100vw, 360px', [ 'enlaces' => false ] ); // phpcs:ignore ?>
	<?php else : ?>
		<span class="foto foto-vacia" aria-hidden="true"><?php get_template_part( 'parciales/icono', null, [ 'n' => 'nota' ] ); ?></span>
	<?php endif; ?>
	<?php if ( ! empty( $args['tipo'] ) && $n['tipo'] ) : ?><span class="quien"><?php echo esc_html( pm_opcion( 'nota_tipo', $n['tipo'], $n['idioma'] ) ); ?></span><?php endif; ?>
	<strong><?php echo esc_html( $n['titulo'] ); ?></strong>
	<span class="note"><?php echo esc_html( pm_fecha( $n['fecha'], $n['idioma'] ) ); ?></span>
</a>
