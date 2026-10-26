<?php
/** Short list of related notes (guides and species pages). Args: notas (pm_nota rows), h2 (UI key), vars (replacements). */
defined( 'ABSPATH' ) || exit;

$notas = $args['notas'] ?? [];
if ( ! $notas ) {
	return;
}
?>
<section class="notas-rel" aria-labelledby="notas-rel-h">
	<h2 id="notas-rel-h"><?php pm_e( $args['h2'], $args['vars'] ?? [] ); ?></h2>
	<ul>
		<?php foreach ( $notas as $n ) : ?>
			<li>
				<a href="<?php echo esc_url( $n['url'] ); ?>"<?php echo $n['idioma'] !== pm_lang() ? ' hreflang="' . esc_attr( pm_meta_idioma( $n['idioma'] )['html'] ) . '"' : ''; ?>><?php echo esc_html( $n['titulo'] ); ?></a>
				<span class="note"><?php echo esc_html( pm_fecha( $n['fecha'], $n['idioma'] ) ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
