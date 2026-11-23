<?php
/**
 * Calculadora de licencia de pesca (23/11/2026): /calculadora-licencia-de-pesca/, /pt/calculadora-licenca-de-pesca/, /en/fishing-licence-calculator/.
 * The tool first: form (GET, works without JavaScript, shareable URL) → result box → how we calculate → table of every region → FAQ (= FAQPage JSON-LD,
 * inc/seo.php) → one CTA after the result and one at the end. The calculation is pm_licencia_calcular() (inc/licencia.php); this file only prints.
 * Result URLs (?donde=…&quien=…) canonicalise to the bare page (wp_head strips the query string).
 */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$e        = pm_licencia_entrada();
$res      = $e['enviado'] ? pm_licencia_calcular( $e['region'], $e['quien'], $e['dias'], $e['extras'] ) : null;
$destinos = pm_licencia_destinos( $idioma );
$faqs     = pm_licencia_faqs();
$viaje    = pm_url_viaje( $idioma ) . ( $e['destino'] ? '?destino=' . rawurlencode( $e['destino'] ) : '' );
[ $desde, $hasta ] = pm_licencia_fechas();
$sel_donde = $e['destino'] ? 'destino:' . $e['destino'] : ( $e['region'] ? 'region:' . $e['region'] : '' );
$sel_quien = $res ? $res['quien'] : ( $e['quien'] ?: 'extranjero' );
$sel_dias  = $res ? $res['dias'] : $e['dias'];
$sel_extra = $res ? $res['extras_ok'] : [];
$fecha     = fn( $y ) => $y ? pm_fecha( $y ) : '';

get_header(); ?>
<div class="v-destino v-rio v-licencia">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'lic.eyebrow' ); ?></div>
			<h1><?php pm_e( 'lic.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'lic.bajada' ); ?></p>
		</div>
	</header>
	<div class="shell cuerpo">
		<section id="calculadora" class="lic-form" aria-labelledby="calc-h">
			<h2 id="calc-h"><?php pm_e( 'lic.form.h' ); ?></h2>
			<form method="get" action="<?php echo esc_url( pm_url_licencia( $idioma ) . '#resultado' ); ?>" id="lic-form">
				<div class="grilla">
					<div class="campo">
						<label for="lic-donde"><?php pm_e( 'lic.donde' ); ?></label>
						<select id="lic-donde" name="donde" required>
							<option value=""><?php pm_e( 'lic.elegir' ); ?></option>
							<optgroup label="<?php echo esc_attr( pm_t( 'lic.donde.regiones' ) ); ?>">
								<?php foreach ( PM_LICENCIA_REGIONES as $r ) : ?>
									<option value="region:<?php echo esc_attr( $r ); ?>" data-region="<?php echo esc_attr( $r ); ?>"<?php selected( $sel_donde, "region:$r" ); ?>><?php pm_e( "lic.region.$r" ); ?></option>
								<?php endforeach; ?>
							</optgroup>
							<?php if ( $destinos ) : ?>
								<optgroup label="<?php echo esc_attr( pm_t( 'lic.donde.destinos' ) ); ?>">
									<?php foreach ( $destinos as $d ) : ?>
										<option value="destino:<?php echo esc_attr( $d['clave'] ); ?>" data-region="<?php echo esc_attr( $d['region'] ); ?>"<?php selected( $sel_donde, 'destino:' . $d['clave'] ); ?>><?php echo esc_html( $d['nombre'] . ( $d['provincia'] ? ', ' . $d['provincia'] : '' ) ); ?></option>
									<?php endforeach; ?>
								</optgroup>
							<?php endif; ?>
						</select>
					</div>
					<div class="campo">
						<label for="lic-quien"><?php pm_e( 'lic.quien' ); ?></label>
						<select id="lic-quien" name="quien">
							<?php foreach ( PM_LICENCIA_QUIENES as $q ) :
								$regs = pm_licencia_regiones_de_quien( $q );
								if ( ! $regs ) { continue; }
								$oculta = $res && ! in_array( $res['region'], $regs, true ); ?>
								<option value="<?php echo esc_attr( $q ); ?>" data-regiones="<?php echo esc_attr( implode( ' ', $regs ) ); ?>"<?php selected( $sel_quien, $q ); ?><?php echo $oculta ? ' hidden disabled' : ''; ?>><?php pm_e( "lic.quien.$q" ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="campo">
						<label for="lic-dias"><?php pm_e( 'lic.dias' ); ?></label>
						<select id="lic-dias" name="dias">
							<?php for ( $n = 1; $n <= 30; $n++ ) : ?>
								<option value="<?php echo (int) $n; ?>"<?php selected( $sel_dias, $n ); ?>><?php echo esc_html( pm_licencia_dias_txt( $n ) ); ?></option>
							<?php endfor; ?>
							<option value="temporada"<?php selected( $sel_dias, 'temporada' ); ?>><?php pm_e( 'lic.dias.temporada' ); ?></option>
						</select>
					</div>
				</div>
				<fieldset class="opciones lic-extras" id="lic-extras">
					<legend><?php pm_e( 'lic.extras' ); ?> <small><?php pm_e( 'lic.extras.nota' ); ?></small></legend>
					<?php foreach ( PM_LICENCIA_EXTRAS as $x ) :
						$pares  = pm_licencia_pares_de_extra( $x );
						$oculta = $res && ! in_array( $res['region'] . ':' . $res['quien'], $pares, true ); ?>
						<label class="lic-extra" data-pares="<?php echo esc_attr( implode( ' ', $pares ) ); ?>"<?php echo $oculta ? ' hidden' : ''; ?>>
							<input type="checkbox" name="extra[]" value="<?php echo esc_attr( $x ); ?>"<?php checked( in_array( $x, $sel_extra, true ) ); ?><?php echo $oculta ? ' disabled' : ''; ?>>
							<span><?php pm_e( "lic.extra.$x" ); ?></span>
						</label>
					<?php endforeach; ?>
				</fieldset>
				<p class="lic-enviar"><button type="submit" class="button dark"><?php pm_e( 'lic.calcular' ); ?></button></p>
			</form>
		</section>

		<div id="resultado" class="lic-resultado" aria-live="polite">
		<?php if ( $res ) :
			$unidad  = $res['unidad'];
			$donde_t = $e['destino'] ? ( array_column( $destinos, 'nombre', 'clave' )[ $e['destino'] ] ?? pm_t( 'lic.region.' . $res['region'] ) ) : pm_t( 'lic.region.' . $res['region'] );
			$fuente  = $res['fuente'];
			$hay_ex  = (bool) $res['extras']; ?>
			<section class="lic-res" aria-labelledby="res-h">
				<h2 id="res-h"><?php pm_e( 'lic.res.h' ); ?></h2>
				<p class="lic-resumen"><?php echo esc_html( pm_t( 'lic.res.resumen', [ 'quien' => pm_t( 'lic.quien.' . $res['quien'] ), 'donde' => $donde_t, 'dias' => pm_licencia_dias_txt( $res['dias'] ) ] ) ); ?></p>
				<?php if ( $res['ajustes']['quien'] ) : ?>
					<p class="lic-nota"><?php echo esc_html( pm_t( 'lic.res.ajuste', [ 'donde' => pm_t( 'lic.region.' . $res['region'] ), 'quien' => pm_t( 'lic.quien.' . $res['quien'] ) ] ) ); ?></p>
				<?php endif; ?>

				<?php if ( 'ok' === $res['estado'] ) : ?>
					<p class="lic-rotulo"><?php pm_e( $hay_ex ? ( $res['completo'] ? 'lic.res.total' : 'lic.res.total.parcial' ) : 'lic.res.barato' ); ?></p>
					<p class="lic-total"><strong><?php echo esc_html( pm_licencia_monto( $res['total'], $unidad ) ); ?></strong></p>
					<p class="lic-plan"><?php echo $hay_ex ? '<strong>' . esc_html( pm_t( 'lic.res.ordinario' ) ) . ':</strong> ' : ''; ?><?php echo esc_html( pm_licencia_plan_txt( $res['mejor'], $unidad ) ); ?></p>
					<?php foreach ( $res['extras'] as $x ) : ?>
						<p class="lic-plan"><?php if ( 'ok' === $x['estado'] ) : ?>
							<strong><?php pm_e( 'lic.extra.' . $x['clave'] ); ?>:</strong> <?php echo esc_html( pm_licencia_plan_txt( $x['mejor'], $unidad ) ); ?>
						<?php else : echo esc_html( pm_t( 'lic.res.extra.sincob', [ 'extra' => pm_t( 'lic.extra.' . $x['clave'] ) ] ) ); endif; ?></p>
					<?php endforeach; ?>
					<?php if ( $res['ahorro'] && $res['referencia'] ) : ?>
						<p class="lic-ahorro"><?php echo esc_html( pm_t( 'lic.res.vs', [ 'ref' => pm_licencia_plan_corto( $res['referencia'] ), 'total' => pm_licencia_monto( $res['referencia']['total'], $unidad ), 'ahorro' => pm_licencia_monto( $res['ahorro'], $unidad ) ] ) ); ?></p>
					<?php endif; ?>
					<?php if ( count( $res['planes'] ) > 1 ) : ?>
						<h3><?php pm_e( 'lic.res.opciones.h' ); ?></h3>
						<ul class="lic-planes">
							<?php foreach ( $res['planes'] as $k => $p ) : ?>
								<li<?php echo 0 === $k ? ' class="mejor"' : ''; ?>><?php echo esc_html( pm_licencia_plan_txt( $p, $unidad ) ); ?><?php if ( 0 === $k ) : ?> <span class="lic-pill"><?php pm_e( 'lic.res.mejor' ); ?></span><?php endif; ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				<?php elseif ( 'gratis' === $res['estado'] ) : ?>
					<p class="lic-total"><strong><?php echo esc_html( pm_licencia_monto( 0, 'ARS' ) ); ?></strong></p>
					<p class="lic-plan"><?php pm_e( 'lic.res.gratis' ); ?></p>
				<?php elseif ( 'sin_cobertura' === $res['estado'] ) : ?>
					<p class="lic-plan"><?php pm_e( 'temporada' === $res['dias'] ? 'lic.res.sincob.temporada' : 'lic.res.sincob' ); ?></p>
				<?php else : ?>
					<p class="lic-plan"><?php pm_e( 'lic.res.sinvalor' ); ?></p>
				<?php endif; ?>

				<?php
				$notas = [];
				if ( 'litros' === $unidad ) { $notas[] = pm_t( 'lic.res.litros' ); }
				if ( $res['solo_en'] ) { $notas[] = pm_t( 'lic.res.soloen', [ 'lugar' => $res['solo_en'] ] ); }
				foreach ( [ $res['solo_en'] ? '' : 'lic.nota.region.' . $res['region'], 'lic.nota.' . $res['quien'] . '.' . $res['region'] ] as $k ) { // the unified-permit line does not fit a Río Negro-only category
					if ( '' !== $k && pm_t_existe( $k ) ) { $notas[] = pm_t( $k ); }
				}
				if ( 'ok' === $res['estado'] && $res['mejor'] && array_filter( $res['mejor']['items'], fn( $i ) => 'fin de semana' === $i['periodo'] ) ) { $notas[] = pm_t( 'lic.nota.finde' ); }
				if ( 'ok' === $res['estado'] ) { $notas[] = pm_t( 'lic.res.supuesto' ); }
				if ( $notas ) : ?><ul class="lic-notas"><?php foreach ( array_unique( $notas ) as $n ) : ?><li><?php echo esc_html( $n ); ?></li><?php endforeach; ?></ul><?php endif; ?>

				<dl class="lic-fuente">
					<?php if ( $res['donde'] ) : ?>
						<div><dt><?php pm_e( 'lic.res.donde.h' ); ?></dt><dd><?php echo esc_html( pm_t( 'lic.como.' . $res['region'] ) ); ?>
							<?php foreach ( $res['donde'] as $i => $d ) : ?><?php echo $i ? ' · ' : ' '; ?><a href="<?php echo esc_url( (string) ( $d['url'] ?? '' ) ); ?>" rel="noopener"><?php echo esc_html( (string) ( $d['nombre'] ?? '' ) ); ?></a><?php endforeach; ?></dd></div>
					<?php endif; ?>
					<div><dt><?php pm_e( 'lic.res.fuente' ); ?></dt><dd><a href="<?php echo esc_url( $fuente['url'] ); ?>" rel="noopener"><?php echo esc_html( $fuente['nombre'] ); ?></a> · <?php echo esc_html( pm_t( 'lic.res.leido', [ 'fecha' => $fecha( $res['consultada'] ) ] ) ); ?></dd></div>
				</dl>
				<?php if ( $vig = pm_licencia_vigencia_txt( $res['region'], $res['vigencia'] ) ) : ?><p class="lic-vig"><?php echo esc_html( pm_t( 'lic.res.vigencia', [ 'texto' => $vig ] ) ); ?></p><?php endif; ?>
				<p class="lic-aviso"><strong><?php pm_e( 'lic.aviso' ); ?></strong></p>
				<div class="lic-cta">
					<p><?php echo esc_html( $e['destino'] ? pm_t( 'lic.cta.res.destino', [ 'destino' => $donde_t ] ) : pm_t( 'lic.cta.res' ) ); ?></p>
					<p class="cta-fila"><a class="button primary" href="<?php echo esc_url( $viaje ); ?>"><?php pm_e( 'lic.cta.boton' ); ?></a><?php pm_cta_nota(); ?></p>
				</div>
			</section>
		<?php endif; ?>
		</div>

		<section class="lic-como" aria-labelledby="como-h">
			<h2 id="como-h"><?php pm_e( 'lic.como.h' ); ?></h2>
			<ul>
				<li><?php pm_e( 'lic.como.1', [ 'desde' => $fecha( $desde ), 'hasta' => $fecha( $hasta ) ] ); ?></li>
				<?php foreach ( [ 2, 3, 4 ] as $n ) : ?><li><?php pm_e( "lic.como.$n" ); ?></li><?php endforeach; ?>
			</ul>
			<p class="lic-aviso"><strong><?php pm_e( 'lic.aviso' ); ?></strong></p>
		</section>

		<section class="lic-tabla" aria-labelledby="tabla-h">
			<h2 id="tabla-h"><?php pm_e( 'lic.tabla.h' ); ?></h2>
			<p class="note"><?php pm_e( 'lic.tabla.i' ); ?></p>
			<div class="tabla-rio tabla-apilada">
				<table>
					<thead><tr><th scope="col"><?php pm_e( 'lic.col.region' ); ?></th><th scope="col"><?php pm_e( 'lic.col.quien' ); ?></th><th scope="col"><?php pm_e( 'lic.col.valores' ); ?></th><th scope="col"><?php pm_e( 'lic.col.leido' ); ?></th></tr></thead>
					<tbody>
					<?php foreach ( pm_licencia_tabla() as $f ) : ?>
						<tr>
							<th scope="row"><?php pm_e( 'lic.region.' . $f['region'] ); ?></th>
							<td data-label="<?php echo esc_attr( pm_t( 'lic.col.quien' ) ); ?>"><?php echo esc_html( $f['quien'] ); ?></td>
							<td data-label="<?php echo esc_attr( pm_t( 'lic.col.valores' ) ); ?>"><?php echo esc_html( $f['valores'] ); ?></td>
							<td data-label="<?php echo esc_attr( pm_t( 'lic.col.leido' ) ); ?>"><?php echo esc_html( $fecha( $f['consultada'] ) ); ?><?php if ( $f['fuente_url'] ) : ?><small><a href="<?php echo esc_url( $f['fuente_url'] ); ?>" rel="noopener"><?php pm_e( 'lic.tabla.fuente' ); ?></a></small><?php endif; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>

		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'lic.faq.h' ); ?></h2>
			<?php foreach ( $faqs as $f ) : ?>
				<h3><?php echo esc_html( $f['pregunta'] ); ?></h3>
				<p><?php echo esc_html( $f['respuesta'] ); ?></p>
			<?php endforeach; ?>
		</section>

		<section class="fin" aria-labelledby="fin-h">
			<h2 id="fin-h"><?php pm_e( 'lic.cta.h' ); ?></h2>
			<p><?php pm_e( 'lic.cta.p' ); ?></p>
			<p><?php pm_e( 'lic.cta.viaje' ); ?> <a href="<?php echo esc_url( pm_estimador_url( '', '' !== pm_estimador_zona_de_destino( (string) $e['destino'] ) ? (string) $e['destino'] : '' ) ); ?>"><?php pm_e( 'lic.cta.viaje.enlace' ); ?></a></p>
			<p class="cta-fila"><a class="button primary" href="<?php echo esc_url( $viaje ); ?>"><?php pm_e( 'lic.cta.boton' ); ?></a><?php pm_cta_nota(); ?></p>
		</section>
	</div>
</div>
<script>
// Progressive enhancement (the page works without it): show only the categories and add-ons that exist for the chosen place, and
// recalculate without a reload by asking the server (same URL, same function) and swapping the result box.
(() => {
	const f = document.getElementById('lic-form');
	if (!f || !('fetch' in window) || !window.DOMParser || !window.URLSearchParams) return;
	const donde = f.elements.donde, quien = f.elements.quien, extras = document.getElementById('lic-extras');
	const region = () => { const o = donde.selectedOptions[0]; return o ? o.dataset.region || '' : ''; };
	const ajustar = () => {
		const r = region();
		let valida = null;
		[...quien.options].forEach((o) => {
			const ok = !r || (o.dataset.regiones || '').split(' ').includes(r);
			o.hidden = !ok; o.disabled = !ok;
			if (ok && !valida) valida = o;
		});
		if (quien.selectedOptions[0] && quien.selectedOptions[0].disabled && valida) quien.value = valida.value;
		extras.querySelectorAll('.lic-extra').forEach((l) => {
			const ok = !r || (l.dataset.pares || '').split(' ').includes(r + ':' + quien.value);
			l.hidden = !ok; l.querySelector('input').disabled = !ok;
			if (!ok) l.querySelector('input').checked = false;
		});
	};
	donde.addEventListener('change', ajustar);
	quien.addEventListener('change', ajustar);
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
