<?php
/**
 * Month bar of a guide: open by the rule, best time by a named source, closed. Args: d (pm_destino row). Prints nothing without
 * official opening and closing dates. The "current month" mark is set by the server and refreshed in the browser, so a cached page
 * still highlights the right month.
 */
defined( 'ABSPATH' ) || exit;

$d = $args['d'] ?? [];
if ( ! $d || ! pm_temporada_hay( $d ) ) {
	return;
}
$idioma = $d['idioma'];
$meses  = pm_temporada_meses( $d );
$cortos = pm_meses_cortos( $idioma );
$actual = (int) wp_date( 'n', null, new DateTimeZone( 'America/Argentina/Buenos_Aires' ) );
$hay_pico = in_array( 'pico', $meses, true );
?>
<div class="barra-meses" data-abre="<?php echo esc_attr( $d['abre'] ); ?>" data-cierra="<?php echo esc_attr( $d['cierra'] ); ?>">
	<p class="barra-meses-t"><strong><?php pm_e( 'temp.barra.h', [ 'abre' => pm_fecha( $d['abre'], $idioma ), 'cierra' => pm_fecha( $d['cierra'], $idioma ) ] ); ?></strong></p>
	<ol class="meses" aria-label="<?php echo esc_attr( pm_t( 'temp.barra.aria' ) ); ?>">
		<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
			<li class="mes mes--<?php echo esc_attr( $meses[ $m ] ); ?><?php echo $m === $actual ? ' mes--actual' : ''; ?>" data-m="<?php echo (int) $m; ?>"<?php echo $m === $actual ? ' aria-current="date"' : ''; ?>>
				<span class="mes-n"><?php echo esc_html( $cortos[ $m - 1 ] ); ?></span>
				<span class="visually-hidden"> <?php pm_e( 'temp.sr.' . $meses[ $m ] ); ?></span>
			</li>
		<?php endfor; ?>
	</ol>
	<ul class="leyenda">
		<li><i class="mes mes--abierto" aria-hidden="true"></i> <?php pm_e( 'temp.leyenda.abierto' ); ?></li>
		<?php if ( $hay_pico ) : ?><li><i class="mes mes--pico" aria-hidden="true"></i> <?php pm_e( 'temp.leyenda.pico', [ 'quien' => $d['pico_quien'] ] ); ?></li><?php endif; ?>
		<li><i class="mes mes--cerrado" aria-hidden="true"></i> <?php pm_e( 'temp.leyenda.cerrado' ); ?></li>
	</ul>
	<?php if ( $d['temporada_nota'] ) : ?><p class="note"><?php echo esc_html( $d['temporada_nota'] ); ?></p><?php endif; ?>
	<p class="note"><?php pm_e( 'temp.barra.aviso' ); ?></p>
</div>
<script>
(function () {
	var m = new Date().toLocaleString('en-US', { timeZone: 'America/Argentina/Buenos_Aires', month: 'numeric' }), act = parseInt(m, 10);
	document.querySelectorAll('.barra-meses .meses .mes').forEach(function (li) {
		var es = parseInt(li.getAttribute('data-m'), 10) === act;
		li.classList.toggle('mes--actual', es);
		if (es) { li.setAttribute('aria-current', 'date'); } else { li.removeAttribute('aria-current'); }
	});
})();
</script>
