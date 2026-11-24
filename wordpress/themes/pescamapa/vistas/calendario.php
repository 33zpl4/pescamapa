<?php
/**
 * Calendar (rebuilt 24/11/2026, rule in docs/ux-paginas.md): the visitor asks "when do I go?", we answer. Two filters (where / what),
 * one card per destination with a 12-month bar per species, the state of today in one pill, one sentence with our reading and the
 * sources folded in a <details>. Cards come from pm_calendario_tarjetas() (inc/temporada.php: official dates of each guide plus
 * data/calendario.json). Works without JavaScript: the chips are links to ?donde=…&que=… and this file filters on the server; with
 * JavaScript (assets/calendario.js) the chips become buttons and filter in place. /calendario-de-pesca/, /pt/calendario-de-pesca/, /en/fishing-calendar/.
 */
defined( 'ABSPATH' ) || exit;

$idioma   = pm_lang();
$destinos = pm_destinos_lista( $idioma );
$cal      = pm_calendario_tarjetas( $idioma );
$tabla    = pm_buscar( 'pm_nota', 'tallas-cupos-vedas', $idioma ) ?? pm_buscar( 'pm_nota', 'tallas-cupos-vedas', 'es' );
$fechas   = array_filter( array_map( fn( $d ) => $d['reglamento']['verificado'], $destinos ) );
$ultima   = $fechas ? max( $fechas ) : '';

// The chosen filters (only values that exist), from ?donde=…&que=… . The page is not cached per query, so JavaScript repeats this on load.
$pide  = fn( $k ) => isset( $_GET[ $k ] ) ? sanitize_key( wp_unslash( (string) $_GET[ $k ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$donde = isset( $cal['regiones'][ $pide( 'donde' ) ] ) ? $pide( 'donde' ) : '';
$que   = isset( $cal['especies'][ $pide( 'que' ) ] ) ? $pide( 'que' ) : '';
$res   = pm_calendario_resumen( $cal, $donde, $que, $idioma );
$base  = pm_url_calendario( $idioma );
$href  = fn( $d, $q ) => esc_url( add_query_arg( array_filter( [ 'donde' => $d, 'que' => $q ] ), $base ) . '#cal-resultados' );
$meses_cortos = pm_meses_cortos( $idioma );
$mes_hoy      = (int) substr( $cal['hoy'], 5, 2 );
$clase_mes    = [ 'mejor' => 'mes--pico', 'pesca' => 'mes--abierto', 'veda' => 'mes--veda', 'cerrado' => 'mes--cerrado', 'sin' => 'mes--cerrado' ];
$sr_mes       = [ 'mejor' => 'mejor', 'pesca' => 'pesca', 'veda' => 'veda', 'cerrado' => 'cerrado', 'sin' => 'sin' ];
$plantillas   = [];
foreach ( [ 'mejor', 'pesca', 'veda', 'fuera', 'sin', 'antes', 'despues' ] as $p ) {
	$plantillas[ 'data-t-' . $p ] = pm_t( 'cal.pill.' . $p );
}
foreach ( [ 'n', 'n1', 'n0', 'hoy', 'hoy0' ] as $p ) {
	$plantillas[ 'data-r-' . $p ] = pm_t( 'cal.res.' . $p );
}
get_header(); ?>
<div class="v-destino v-rio v-calendario">
	<header class="top">
		<div class="shell">
			<div class="eyebrow"><?php pm_e( 'cal.eyebrow' ); ?></div>
			<h1><?php pm_e( 'cal.h1' ); ?></h1>
			<p class="bajada"><?php pm_e( 'cal.bajada' ); ?></p>
			<?php if ( $ultima ) : ?><p class="confianza"><?php pm_e( 'ficha.verificado' ); ?> <time datetime="<?php echo esc_attr( $ultima ); ?>"><?php echo esc_html( pm_fecha( $ultima, $idioma ) ); ?></time></p><?php endif; ?>
		</div>
	</header>
	<div class="shell cuerpo">
		<div id="cal" class="cal" data-hoy="<?php echo esc_attr( $cal['hoy'] ); ?>" data-idioma="<?php echo esc_attr( $idioma ); ?>"<?php foreach ( $plantillas as $k => $v ) { echo ' ' . esc_attr( $k ) . '="' . esc_attr( $v ) . '"'; } ?>>
			<section class="cal-filtros" aria-label="<?php pm_e( 'cal.f.aria' ); ?>">
				<div class="cal-grupo" role="group" aria-labelledby="cal-g-donde">
					<p class="cal-gl" id="cal-g-donde"><?php pm_e( 'cal.f.donde' ); ?></p>
					<ul class="cal-chips">
						<li><a class="cal-chip" href="<?php echo $href( '', $que ); ?>" data-filtro="donde" data-valor=""<?php echo '' === $donde ? ' aria-current="true"' : ''; ?>><?php pm_e( 'cal.f.todo' ); ?></a></li>
						<?php foreach ( $cal['regiones'] as $k => $nombre ) : ?>
							<li><a class="cal-chip" href="<?php echo $href( $k, $que ); ?>" data-filtro="donde" data-valor="<?php echo esc_attr( $k ); ?>"<?php echo $k === $donde ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $nombre ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
				<div class="cal-grupo" role="group" aria-labelledby="cal-g-que">
					<p class="cal-gl" id="cal-g-que"><?php pm_e( 'cal.f.que' ); ?></p>
					<ul class="cal-chips">
						<li><a class="cal-chip" href="<?php echo $href( $donde, '' ); ?>" data-filtro="que" data-valor=""<?php echo '' === $que ? ' aria-current="true"' : ''; ?>><?php pm_e( 'cal.f.todo' ); ?></a></li>
						<?php foreach ( $cal['especies'] as $k => $nombre ) : ?>
							<li><a class="cal-chip" href="<?php echo $href( $donde, $k ); ?>" data-filtro="que" data-valor="<?php echo esc_attr( $k ); ?>"<?php echo $k === $que ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $nombre ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
			<p class="cal-res" id="cal-resultados" aria-live="polite" data-fecha="<?php echo esc_attr( pm_fecha_corta( $cal['hoy'], $idioma ) ); ?>"><?php echo esc_html( $res['texto'] ); ?></p>
			<ul class="cal-ley" aria-label="<?php pm_e( 'cal.ley.aria' ); ?>">
				<li><i class="mes mes--pico" aria-hidden="true"></i> <?php pm_e( 'cal.ley.mejor' ); ?></li>
				<li><i class="mes mes--abierto" aria-hidden="true"></i> <?php pm_e( 'cal.ley.pesca' ); ?></li>
				<li><i class="mes mes--veda" aria-hidden="true"></i> <?php pm_e( 'cal.ley.veda' ); ?></li>
				<li><i class="mes mes--cerrado" aria-hidden="true"></i> <?php pm_e( 'cal.ley.cerrado' ); ?></li>
				<li><i class="mes mes--actual" aria-hidden="true"></i> <?php pm_e( 'cal.ley.hoy' ); ?></li>
			</ul>
			<div class="cal-lista">
				<?php foreach ( $cal['tarjetas'] as $t ) :
					$coincide = pm_calendario_coincide( $t, $donde, $que );
					$nfilas   = count( $t['filas'] );
					$aut_link = $t['fuente_url'] && pm_fuente_enlazable( [ 'url' => $t['fuente_url'], 'oficial' => 1 ] );
					$notas    = array_values( array_filter( array_map( fn( $f ) => $f['nota'] ? ( $nfilas > 1 ? $f['nombre'] . ': ' : '' ) . $f['nota'] : '', $t['filas'] ) ) );
					if ( $t['nota_guia'] ) {
						$notas[] = $t['nota_guia'];
					}
					?>
					<article class="cal-card<?php echo '' !== $que ? ' cal-card--que' : ''; ?>" id="<?php echo esc_attr( $t['clave'] ); ?>" data-region="<?php echo esc_attr( $t['region'] ); ?>" data-especies="<?php echo esc_attr( implode( ' ', $t['especies'] ) ); ?>"<?php echo $coincide ? '' : ' hidden'; ?>>
						<h3><a href="<?php echo esc_url( $t['url'] ); ?>"><?php echo esc_html( $t['nombre'] ); ?></a></h3>
						<?php if ( $t['provincia'] && false === mb_stripos( (string) $t['nombre'], (string) $t['provincia'] ) ) : ?><p class="cal-prov"><?php echo esc_html( $t['provincia'] ); ?></p><?php endif; ?>
						<?php
						$estado_pill = '' !== $que ? ( $t['estados'][ $que ] ?? 'sin' ) : $t['estado'];
						$texto_pill  = $t['pill'];
						if ( '' !== $que ) {
							foreach ( $t['filas'] as $f ) {
								if ( $f['especie'] === $que ) {
									$texto_pill = $f['pill'];
								}
							}
							if ( 'sin' === $estado_pill ) {
								$texto_pill = pm_t( 'cal.pill.sin' );
							}
						}
						?>
						<p class="cal-pill cal-pill--<?php echo esc_attr( $estado_pill ); ?>" data-pill><?php echo esc_html( $texto_pill ); ?></p>
						<?php foreach ( $t['filas'] as $i => $f ) :
							$codigo = '';
							foreach ( $f['meses'] as $s ) {
								$codigo .= [ 'mejor' => 'm', 'pesca' => 'p', 'veda' => 'v', 'cerrado' => 'c', 'sin' => 's' ][ $s ];
							}
							$oculta = '' !== $que && $f['especie'] !== $que;
							?>
							<div class="cal-esp<?php echo $i > 0 ? ' cal-esp--mas' : ''; ?>" data-esp="<?php echo esc_attr( $f['especie'] ); ?>" data-meses="<?php echo esc_attr( $codigo ); ?>"<?php echo $t['abre'] ? ' data-abre="' . esc_attr( $t['abre'] ) . '" data-cierra="' . esc_attr( $t['cierra'] ) . '"' : ''; ?><?php echo $oculta ? ' hidden' : ''; ?>>
								<p class="cal-esp-n"><?php echo esc_html( $f['nombre'] ); ?></p>
								<div class="barra-meses cal-barra">
									<ol class="meses" aria-label="<?php echo esc_attr( pm_t( 'cal.barra.aria', [ 'especie' => $f['nombre'], 'lugar' => $t['nombre'] ] ) ); ?>">
										<?php for ( $m = 1; $m <= 12; $m++ ) :
											$s = $f['meses'][ $m ];
											?>
											<li class="mes <?php echo esc_attr( $clase_mes[ $s ] ); ?><?php echo $m === $mes_hoy ? ' mes--actual' : ''; ?>" data-m="<?php echo (int) $m; ?>"<?php echo $m === $mes_hoy ? ' aria-current="date"' : ''; ?>>
												<span class="mes-i" aria-hidden="true"><?php echo esc_html( mb_substr( $meses_cortos[ $m - 1 ], 0, 1 ) ); ?></span><span class="mes-n" aria-hidden="true"><?php echo esc_html( $meses_cortos[ $m - 1 ] ); ?></span>
												<span class="visually-hidden"><?php echo esc_html( $meses_cortos[ $m - 1 ] . ': ' . pm_t( 'cal.sr.' . $sr_mes[ $s ] ) ); ?></span>
											</li>
										<?php endfor; ?>
									</ol>
								</div>
								<?php if ( $f['frase'] ) : ?><p class="cal-frase"><?php echo esc_html( $f['frase'] ); ?></p><?php endif; ?>
								<?php if ( ! empty( $f['veda_txt'] ) ) : ?><p class="cal-veda"><?php echo esc_html( $f['veda_txt'] ); ?></p><?php elseif ( $f['veda_pendiente'] ) : ?><p class="cal-veda"><?php pm_e( 'cal.veda.pendiente' ); ?></p><?php endif; ?>
							</div>
						<?php endforeach; ?>
						<p class="cal-sin"<?php echo ( $nfilas && ( '' === $que || in_array( $que, array_column( $t['filas'], 'especie' ), true ) ) ) ? ' hidden' : ''; ?>><?php pm_e( 'cal.sin.meses' ); ?></p>
						<?php if ( $nfilas > 1 && '' === $que ) : ?>
							<button type="button" class="cal-mas" hidden aria-expanded="false" data-mas="<?php echo esc_attr( pm_t( 'cal.mas', [ 'n' => (string) ( $nfilas - 1 ) ] ) ); ?>" data-menos="<?php echo esc_attr( pm_t( 'cal.menos' ) ); ?>"><?php echo esc_html( pm_t( 'cal.mas', [ 'n' => (string) ( $nfilas - 1 ) ] ) ); ?></button>
						<?php endif; ?>
						<p class="cal-acc">
							<a class="button cal-btn" href="<?php echo esc_url( $t['url'] ); ?>"><?php pm_e( 'cal.btn.guia' ); ?></a>
							<a class="button dark cal-btn" href="<?php echo esc_url( $t['cta_url'] ); ?>"><?php pm_e( 'cal.btn.opciones' ); ?></a>
						</p>
						<details class="cal-fuentes">
							<summary><?php echo esc_html( $t['revisado'] ? pm_t( 'cal.fuentes', [ 'fecha' => pm_fecha( $t['revisado'], $idioma ) ] ) : pm_t( 'cal.fuentes.sin' ) ); ?></summary>
							<?php if ( $t['autoridad'] ) : ?>
								<p><?php pm_e( 'cal.fuentes.aut' ); ?>: <?php if ( $aut_link ) : ?><a href="<?php echo esc_url( $t['fuente_url'] ); ?>" rel="noopener"><?php echo esc_html( $t['autoridad'] ); ?></a><?php else : ?><?php echo esc_html( $t['autoridad'] ); ?><?php endif; ?>.</p>
							<?php endif; ?>
							<?php if ( $t['verificado'] ) : ?><p><?php pm_e( 'cal.fuentes.verificado', [ 'fecha' => pm_fecha( $t['verificado'], $idioma ) ] ); ?>.</p><?php endif; ?>
							<?php foreach ( $notas as $n ) : ?><p><?php echo esc_html( $n ); ?></p><?php endforeach; ?>
							<p><a href="<?php echo esc_url( $t['url'] . '#reglamento' ); ?>"><?php pm_e( 'cal.fuentes.guia' ); ?></a></p>
						</details>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
		<?php if ( $tabla ) : ?><p class="cal-tabla"><?php pm_e( 'cal.tabla' ); ?> <a href="<?php echo esc_url( get_permalink( $tabla ) ); ?>"><?php echo esc_html( get_the_title( $tabla ) ); ?></a></p><?php endif; ?>
		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'cal.faq.h' ); ?></h2>
			<?php foreach ( [ 1, 2, 3, 4 ] as $n ) : ?>
				<h3><?php pm_e( "cal.faq.$n.q" ); ?></h3>
				<p><?php pm_e( "cal.faq.$n.a" ); ?></p>
			<?php endforeach; ?>
		</section>
		<p class="cta-nota"><a class="button dark" href="<?php echo esc_url( pm_url_viaje( $idioma ) ); ?>"><?php pm_e( 'nav.viaje' ); ?></a><?php pm_cta_nota(); ?></p>
	</div>
</div>
<?php wp_enqueue_script( 'pm-calendario' ); get_footer();
