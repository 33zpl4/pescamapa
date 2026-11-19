<?php
/**
 * Home block "Where to go this month": guides whose published season data says this month is in season (pm_destinos_del_mes).
 * Prints nothing when fewer than two qualify. Args: idioma.
 */
defined( 'ABSPATH' ) || exit;

$idioma = pm_idioma_valido( (string) ( $args['idioma'] ?? pm_lang() ) );
$items  = pm_destinos_del_mes( $idioma );
if ( ! $items ) {
	return;
}
$mes = pm_mes_nombre( (int) wp_date( 'n', null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) ), $idioma );
?>
<section id="este-mes" class="section mes-destinos" aria-labelledby="este-mes-h">
	<div class="shell">
		<div class="head">
			<div>
				<div class="eyebrow"><?php pm_e( 'inicio.mes.eyebrow' ); ?></div>
				<h2 id="este-mes-h"><?php pm_e( 'inicio.mes.h2', [ 'mes' => $mes ] ); ?></h2>
			</div>
			<p><?php pm_e( 'inicio.mes.p' ); ?></p>
		</div>
		<ul class="mes-lista">
			<?php foreach ( $items as $it ) : ?>
				<li>
					<a href="<?php echo esc_url( $it['url'] ); ?>" hreflang="<?php echo esc_attr( pm_meta_idioma( $it['idioma'] )['html'] ); ?>">
						<strong><?php echo esc_html( $it['nombre'] ); ?></strong>
						<?php if ( '' !== $it['especies'] ) : ?><span class="especies-linea"><?php echo esc_html( $it['especies'] ); ?></span><?php endif; ?>
						<span class="nivel nivel--<?php echo esc_attr( $it['nivel'] ); ?>"><?php echo 'pico' === $it['nivel'] && '' !== $it['quien'] ? esc_html( pm_t( 'inicio.mes.pico', [ 'quien' => $it['quien'] ] ) ) : esc_html( pm_t( 'inicio.mes.abierto' ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
