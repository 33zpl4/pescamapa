<?php
/**
 * Estimador de costo de un viaje de pesca (23/11/2026): /cuanto-cuesta-viaje-de-pesca/, /pt/quanto-custa-viagem-de-pesca/, /en/fishing-trip-cost-estimator/.
 * Same pattern as the licence calculator (vistas/licencia.php): the tool first (GET form, works without JavaScript, shareable URL) → result box → how we
 * calculate → table of every zone and format → FAQ (= FAQPage JSON-LD, inc/seo.php) → one closing CTA. The result carries its own CTA to the trip form with
 * what was chosen. The calculation is pm_estimador_calcular() (inc/precios.php); this file only prints. Result URLs canonicalise to the bare page (wp_head).
 * It reuses the licence calculator's look (class v-licencia, lic-* rules in extra.css); the estimator-only pieces are the est-* classes.
 */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$e        = pm_estimador_entrada();
$res      = $e['enviado'] ? pm_estimador_calcular( $e['zona'], $e['formato'], $e['pescadores'], $e['acompanantes'], pm_estimador_cantidad_pedida( $e ) ) : null;
$destinos = pm_estimador_destinos( $idioma );
$faqs     = pm_estimador_faqs();
$zonas    = pm_estimador_zonas();
[ $desde, $hasta ] = pm_estimador_fechas();
$sel_donde   = $e['destino'] ? 'destino:' . $e['destino'] : ( $e['zona'] ? 'zona:' . $e['zona'] : '' );
$sel_formato = $res ? $res['formato'] : ( $e['formato'] ?: ( pm_estimador_formatos( $e['zona'] ?: ( $zonas[0] ?? '' ) )[0] ?? 'dia' ) );
$sel_pesc    = $res ? $res['pescadores'] : $e['pescadores'];
$sel_acomp   = $res ? $res['acompanantes'] : $e['acompanantes'];
$sel_dias    = $res ? ( 'semana' === $res['formato'] ? 3 : $res['cantidad'] ) : pm_estimador_entero( $e['dias'], 1, 14, 3 );
$sel_semanas = $res ? ( 'semana' === $res['formato'] ? $res['cantidad'] : 1 ) : pm_estimador_entero( $e['semanas'], 1, 4, 1 );
$fecha       = fn( $y ) => $y ? pm_fecha( $y ) : '';
$fuentes     = pm_estimador_fuentes();
$n_precios   = array_sum( $fuentes );
$n_lugares   = count( array_unique( array_column( pm_estimador_datos()['filas'], 'lugar' ) ) );
$donde_t     = '';
if ( $res ) {
	$destino_nombre = $e['destino'] ? ( array_column( $destinos, 'nombre', 'clave' )[ $e['destino'] ] ?? '' ) : '';
	$donde_t        = pm_estimador_donde_txt( $res['zona'], $res['zona'] === $e['zona'] ? $destino_nombre : '' );
	$viaje          = pm_estimador_viaje_url( $res, $res['zona'] === $e['zona'] ? $e['destino'] : '' );
	$licencia_url   = pm_estimador_licencia_url( $res['zona'], $res['zona'] === $e['zona'] ? $e['destino'] : '' );
	$cant_t         = pm_estimador_cantidad_txt( $res['formato'], $res['cantidad'] );
} else {
	$viaje = pm_url_viaje( $idioma );
}

get_header(); ?>
<div class="v-destino v-rio v-licencia v-estimador">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'est.eyebrow' ); ?></div>
			<h1><?php pm_e( 'est.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'est.bajada' ); ?></p>
		</div>
	</header>
	<div class="shell cuerpo">
		<section id="calculadora" class="lic-form" aria-labelledby="calc-h">
			<h2 id="calc-h"><?php pm_e( 'est.form.h' ); ?></h2>
			<form method="get" action="<?php echo esc_url( pm_url_estimador( $idioma ) . '#resultado' ); ?>" id="est-form">
				<div class="grilla">
					<div class="campo">
						<label for="est-donde"><?php pm_e( 'est.donde' ); ?></label>
						<select id="est-donde" name="donde" required>
							<option value=""><?php pm_e( 'est.elegir' ); ?></option>
							<optgroup label="<?php echo esc_attr( pm_t( 'est.donde.zonas' ) ); ?>">
								<?php foreach ( $zonas as $z ) : ?>
									<option value="zona:<?php echo esc_attr( $z ); ?>" data-zona="<?php echo esc_attr( $z ); ?>"<?php selected( $sel_donde, "zona:$z" ); ?>><?php pm_e( "est.zona.$z" ); ?></option>
								<?php endforeach; ?>
							</optgroup>
							<?php if ( $destinos ) : ?>
								<optgroup label="<?php echo esc_attr( pm_t( 'est.donde.destinos' ) ); ?>">
									<?php foreach ( $destinos as $d ) : ?>
										<option value="destino:<?php echo esc_attr( $d['clave'] ); ?>" data-zona="<?php echo esc_attr( $d['zona'] ); ?>"<?php selected( $sel_donde, 'destino:' . $d['clave'] ); ?>><?php echo esc_html( $d['nombre'] . ( $d['provincia'] ? ', ' . $d['provincia'] : '' ) ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							<?php endif; ?>
						</select>
					</div>
					<div class="campo">
						<label for="est-formato"><?php pm_e( 'est.formato' ); ?></label>
						<select id="est-formato" name="formato">
							<?php foreach ( PM_EST_FORMATOS as $f ) :
								$en_zonas = array_values( array_filter( $zonas, fn( $z ) => in_array( $f, pm_estimador_formatos( $z ), true ) ) );
								$oculta   = $res && ! in_array( $res['zona'], $en_zonas, true ); ?>
								<option value="<?php echo esc_attr( $f ); ?>" data-zonas="<?php echo esc_attr( implode( ' ', $en_zonas ) ); ?>"<?php selected( $sel_formato, $f ); ?><?php echo $oculta ? ' hidden disabled' : ''; ?>><?php pm_e( "est.formato.$f" ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="campo">
						<label for="est-pescadores"><?php pm_e( 'est.pescadores' ); ?></label>
						<select id="est-pescadores" name="pescadores">
							<?php for ( $n = 1; $n <= PM_EST_MAX_PESCADORES; $n++ ) : ?>
								<option value="<?php echo (int) $n; ?>"<?php selected( $sel_pesc, $n ); ?>><?php echo (int) $n; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="campo">
						<label for="est-acompanantes"><?php pm_e( 'est.acompanantes' ); ?></label>
						<select id="est-acompanantes" name="acompanantes">
							<?php for ( $n = 0; $n <= PM_EST_MAX_ACOMPANANTES; $n++ ) : ?>
								<option value="<?php echo (int) $n; ?>"<?php selected( $sel_acomp, $n ); ?>><?php echo (int) $n; ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="campo" id="est-c-dias">
						<label for="est-dias"><?php pm_e( 'est.dias' ); ?></label>
						<select id="est-dias" name="dias">
							<?php for ( $n = PM_EST_CANTIDAD['dia'][0]; $n <= PM_EST_CANTIDAD['dia'][1]; $n++ ) : ?>
								<option value="<?php echo (int) $n; ?>"<?php selected( $sel_dias, $n ); ?>><?php echo esc_html( pm_licencia_dias_txt( $n ) ); ?></option>
							<?php endfor; ?>
						</select>
					</div>
					<div class="campo" id="est-c-semanas">
						<label for="est-semanas"><?php pm_e( 'est.semanas' ); ?></label>
						<select id="est-semanas" name="semanas">
							<?php for ( $n = PM_EST_CANTIDAD['semana'][0]; $n <= PM_EST_CANTIDAD['semana'][1]; $n++ ) : ?>
								<option value="<?php echo (int) $n; ?>"<?php selected( $sel_semanas, $n ); ?>><?php echo esc_html( pm_estimador_cantidad_txt( 'semana', $n ) ); ?></option>
							<?php endfor; ?>
						</select>
					</div>
				</div>
				<p class="note est-ayuda"><?php pm_e( 'est.form.ayuda' ); ?></p>
				<p class="lic-enviar"><button type="submit" class="button dark"><?php pm_e( 'est.calcular' ); ?></button></p>
			</form>
		</section>

		<div id="resultado" class="lic-resultado" aria-live="polite">
		<?php if ( $res ) :
			$pesc_t  = pm_estimador_pescadores_txt( $res['pescadores'] );
			$vars    = [ 'pescadores' => $pesc_t, 'cantidad' => $cant_t, 'donde' => $donde_t ];
			$inc_txt = pm_estimador_incluye_txt( $res['zona'], $res['formato'] );
			$fijo    = pm_t( 'est.fijo', [ 'leido' => pm_estimador_leido( $res['desde'], $res['hasta'] ) ] ); ?>
			<section class="lic-res est-res" aria-labelledby="res-h">
				<h2 id="res-h"><?php pm_e( 'est.res.h' ); ?></h2>
				<?php if ( $res['ajustes']['formato'] ) : ?>
					<p class="lic-nota"><?php echo esc_html( pm_t( 'est.res.ajuste.formato', [ 'zona' => pm_estimador_donde_txt( $res['zona'] ), 'formato' => pm_t( 'est.formato.' . $e['formato'] ) ] ) ); ?></p>
				<?php endif; ?>
				<?php if ( $res['ajustes']['cantidad'] ) : ?>
					<p class="lic-nota"><?php echo esc_html( pm_t( 'est.res.ajuste.' . ( 'semana' === $res['formato'] ? 'semanas' : ( 'paquete' === $res['formato'] ? 'paquete' : 'dias' ) ), [ 'min' => (string) PM_EST_CANTIDAD[ $res['formato'] ][0], 'max' => (string) PM_EST_CANTIDAD[ $res['formato'] ][1], 'n' => (string) $res['cantidad'] ] ) ); ?></p>
				<?php endif; ?>

				<?php foreach ( $res['series'] as $k => $s ) :
					$moneda = $s['moneda'];
					if ( $k > 0 ) : ?>
						<div class="est-otra"><h3><?php pm_e( 'ARS' === $moneda ? 'est.res.pesos.h' : 'est.res.otra.h' ); ?></h3>
					<?php endif; ?>
					<?php if ( 'rango' === $s['modo'] ) : ?>
						<p class="est-titular"><?php echo wp_kses( pm_t( 'est.res.titular', $vars + [ 'min' => '<strong>' . esc_html( pm_estimador_monto( $s['total_min'], $moneda ) ) . '</strong>', 'max' => '<strong>' . esc_html( pm_estimador_monto( $s['total_max'], $moneda ) ) . '</strong>' ] ), [ 'strong' => [] ] ); ?></p>
						<p class="lic-plan est-cuenta"><span class="est-rotulo"><?php pm_e( 'est.res.cuenta' ); ?></span> <?php echo esc_html( pm_estimador_aritmetica( $s, $res['formato'], $res['pescadores'], $res['cantidad'] ) ); ?></p>
						<p class="lic-plan est-pordia"><?php echo esc_html( pm_t( 'est.res.pordia', [ 'rango' => pm_estimador_rango( $s['por_dia_min'], $s['por_dia_max'], $moneda ) ] ) ); ?></p>
						<p class="lic-vig est-base"><?php echo esc_html( pm_t( 'est.res.base', [ 'n' => (string) $s['n'], 'lugares' => (string) $s['lugares'], 'leido' => pm_estimador_leido( $s['desde'], $s['hasta'] ) ] ) ); ?></p>
					<?php elseif ( 1 === $s['n'] ) :
						$it = $s['items'][0]; ?>
						<p class="est-titular"><?php echo wp_kses( pm_t( 'est.res.titular.uno', $vars + [ 'total' => '<strong>' . esc_html( pm_estimador_rango( $it['total_min'], $it['total_max'], $moneda ) ) . '</strong>' ] ), [ 'strong' => [] ] ); ?></p>
						<p class="lic-plan est-cuenta"><span class="est-rotulo"><?php pm_e( 'est.res.cuenta' ); ?></span> <?php echo esc_html( pm_estimador_item_cuenta( $it, $s, $res['formato'], $res['pescadores'], $res['cantidad'] ) ); ?></p>
						<p class="lic-plan est-pordia"><?php echo esc_html( pm_t( 'est.res.pordia', [ 'rango' => pm_estimador_monto( $it['por_dia'], $moneda ) ] ) ); ?></p>
						<p class="lic-vig est-base"><?php echo esc_html( pm_t( 'est.item', [ 'etiqueta' => pm_estimador_item_etiqueta( $it['fila'], $res['formato'] ), 'monto' => ( ! empty( $it['fila']['desde'] ) ? pm_t( 'est.desde' ) . ' ' : '' ) . pm_estimador_rango( $it['fila']['monto'], (float) ( $it['fila']['monto_max'] ?? 0 ) > 0 ? $it['fila']['monto_max'] : $it['fila']['monto'], $moneda ), 'unidad' => pm_estimador_unidad_txt( $s['calc'], $res['formato'] ), 'fecha' => pm_fecha( (string) $it['fila']['fecha'] ) ] ) ); ?> <?php pm_e( 'est.res.unico' ); ?></p>
					<?php else : ?>
						<p class="est-titular est-titular-lista"><?php echo esc_html( 'paquete' === $res['formato'] ? pm_t( 'est.res.titular.paquetes', $vars ) : pm_t( 'est.res.titular.lista', $vars ) ); ?></p>
						<ul class="lic-planes est-lista">
							<?php foreach ( $s['items'] as $it ) : ?>
								<li><?php echo esc_html( pm_estimador_item_txt( $it, $s, $res['formato'] ) ); ?><br><strong><?php echo esc_html( pm_estimador_item_cuenta( $it, $s, $res['formato'], $res['pescadores'], $res['cantidad'] ) ); ?></strong></li>
							<?php endforeach; ?>
						</ul>
						<p class="lic-vig est-base"><?php echo esc_html( pm_t( 'est.res.base.lista', [ 'n' => (string) $s['n'], 'lugares' => (string) $s['lugares'], 'min' => (string) PM_EST_MIN_LUGARES ] ) ); ?><?php echo 'paquete' === $res['formato'] ? ' ' . esc_html( pm_t( 'est.res.paquete.fijo' ) ) : ''; ?></p>
					<?php endif; ?>
					<?php if ( $k > 0 ) : ?></div><?php endif; ?>
				<?php endforeach; ?>

				<?php if ( $res['acompanantes'] > 0 ) : ?>
					<p class="lic-nota"><?php echo esc_html( pm_t( 'est.res.acomp', [ 'n' => (string) $res['acompanantes'] ] ) ); ?></p>
				<?php endif; ?>
				<p class="lic-aviso est-fijo"><strong><?php echo esc_html( $fijo ); ?></strong></p>

				<div class="lic-cta est-cta">
					<p><?php pm_e( 'est.cta.res' ); ?></p>
					<p class="cta-fila"><a class="button primary" href="<?php echo esc_url( $viaje ); ?>"><?php pm_e( 'est.cta.boton' ); ?></a><?php pm_cta_nota(); ?></p>
				</div>

				<h3><?php pm_e( 'est.inc.h' ); ?></h3>
				<p class="est-incluye"><?php echo esc_html( '' !== $inc_txt ? $inc_txt : pm_t( 'est.inc.sindato' ) ); ?></p>
				<h3><?php pm_e( 'est.aparte.h' ); ?></h3>
				<ul class="lic-notas est-aparte">
					<li><?php pm_e( 'est.aparte.licencia' ); ?> <a href="<?php echo esc_url( $licencia_url ); ?>"><?php pm_e( 'est.aparte.licencia.enlace' ); ?></a></li>
					<li><?php pm_e( 'est.aparte.vuelos' ); ?></li>
					<li><?php pm_e( 'est.aparte.propinas' ); ?></li>
					<li><?php pm_e( 'est.aparte.equipo' ); ?></li>
				</ul>
			</section>
		<?php endif; ?>
		</div>

		<section class="lic-como" aria-labelledby="como-h">
			<h2 id="como-h"><?php pm_e( 'est.como.h' ); ?></h2>
			<ul>
				<li><?php pm_e( 'est.como.1', [ 'n' => (string) $n_precios, 'lugares' => (string) $n_lugares, 'desde' => $fecha( $desde ), 'hasta' => $fecha( $hasta ), 'propia' => (string) $fuentes['propia'], 'programa' => (string) $fuentes['programa'], 'listado' => (string) $fuentes['listado'] ] ); ?></li>
				<?php foreach ( [ 2, 3, 4, 5 ] as $n ) : ?><li><?php pm_e( "est.como.$n", [ 'min' => (string) PM_EST_MIN_LUGARES ] ); ?></li><?php endforeach; ?>
			</ul>
			<p class="lic-aviso"><strong><?php echo esc_html( pm_t( 'est.fijo', [ 'leido' => pm_estimador_leido( $desde, $hasta ) ] ) ); ?></strong></p>
		</section>

		<section class="lic-tabla" aria-labelledby="tabla-h">
			<h2 id="tabla-h"><?php pm_e( 'est.tabla.h' ); ?></h2>
			<p class="note"><?php pm_e( 'est.tabla.i', [ 'min' => (string) PM_EST_MIN_LUGARES ] ); ?></p>
			<div class="tabla-rio tabla-apilada">
				<table>
					<thead><tr><th scope="col"><?php pm_e( 'est.col.zona' ); ?></th><th scope="col"><?php pm_e( 'est.col.formato' ); ?></th><th scope="col"><?php pm_e( 'est.col.valores' ); ?></th><th scope="col"><?php pm_e( 'est.col.n' ); ?></th><th scope="col"><?php pm_e( 'est.col.leido' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( pm_estimador_tabla() as $f ) : ?>
						<tr>
							<th scope="row"><?php pm_e( 'est.zona.' . $f['zona'] ); ?></th>
							<td data-label="<?php echo esc_attr( pm_t( 'est.col.formato' ) ); ?>"><?php echo esc_html( $f['formato'] ); ?></td>
							<td data-label="<?php echo esc_attr( pm_t( 'est.col.valores' ) ); ?>"><?php echo esc_html( $f['valores'] ); ?></td>
							<td data-label="<?php echo esc_attr( pm_t( 'est.col.n' ) ); ?>"><?php echo esc_html( $f['n'] ); ?></td>
							<td data-label="<?php echo esc_attr( pm_t( 'est.col.leido' ) ); ?>"><?php echo esc_html( $f['leido'] ); ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>

		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'est.faq.h' ); ?></h2>
			<?php foreach ( $faqs as $f ) : ?>
				<h3><?php echo esc_html( $f['pregunta'] ); ?></h3>
				<p><?php echo esc_html( $f['respuesta'] ); ?></p>
			<?php endforeach; ?>
		</section>

		<section class="fin" aria-labelledby="fin-h">
			<h2 id="fin-h"><?php pm_e( 'est.cta.h' ); ?></h2>
			<p><?php pm_e( 'est.cta.p' ); ?></p>
			<p class="cta-fila"><a class="button primary" href="<?php echo esc_url( $viaje ); ?>"><?php pm_e( $res ? 'est.cta.boton' : 'est.cta.boton.cierre' ); ?></a><?php pm_cta_nota(); ?></p>
		</section>
	</div>
</div>
<script>
// Progressive enhancement (the page works without it): show only the formats the chosen place has prices for, the days or the weeks field that fits the
// format, and recalculate without a reload by asking the server (same URL, same function) and swapping the result box.
(() => {
	const f = document.getElementById('est-form');
	if (!f || !('fetch' in window) || !window.DOMParser || !window.URLSearchParams) return;
	const donde = f.elements.donde, formato = f.elements.formato, dias = f.elements.dias, semanas = f.elements.semanas;
	const cDias = document.getElementById('est-c-dias'), cSemanas = document.getElementById('est-c-semanas');
	const zona = () => { const o = donde.selectedOptions[0]; return o ? o.dataset.zona || '' : ''; };
	const ajustar = () => {
		const z = zona();
		let valida = null;
		[...formato.options].forEach((o) => {
			const ok = !z || (o.dataset.zonas || '').split(' ').includes(z);
			o.hidden = !ok; o.disabled = !ok;
			if (ok && !valida) valida = o;
		});
		if (formato.selectedOptions[0] && formato.selectedOptions[0].disabled && valida) formato.value = valida.value;
		const semana = formato.value === 'semana', paquete = formato.value === 'paquete';
		cDias.hidden = semana; dias.disabled = semana;
		cSemanas.hidden = !semana; semanas.disabled = !semana;
		[...dias.options].forEach((o) => { const n = +o.value, ok = !paquete || (n >= 2 && n <= 5); o.hidden = !ok; o.disabled = !ok; });
		if (dias.selectedOptions[0] && dias.selectedOptions[0].disabled) dias.value = '3';
	};
	donde.addEventListener('change', ajustar);
	formato.addEventListener('change', ajustar);
	ajustar();
	f.addEventListener('submit', (ev) => {
		if (!f.checkValidity()) return;
		ev.preventDefault();
		const url = f.action.split('#')[0] + '?' + new URLSearchParams(new FormData(f)).toString();
		fetch(url, { headers: { Accept: 'text/html' } })
			.then((r) => (r.ok ? r.text() : Promise.reject(new Error(String(r.status)))))
			.then((t) => {
				const nuevo = new DOMParser().parseFromString(t, 'text/html').getElementById('resultado'), actual = document.getElementById('resultado');
				if (!nuevo || !actual) throw new Error('sin resultado');
				actual.replaceWith(nuevo);
				history.replaceState(null, '', url + '#resultado');
				nuevo.scrollIntoView({ behavior: 'smooth', block: 'start' });
			})
			.catch(() => f.submit());
	});
})();
</script>
<?php get_footer();
