<?php
/**
 * City page: every public outfitter of one destination ("Guías de pesca en <lugar>"), the short answer an assistant can
 * repeat (how many, what they publish, dated rates, licence, how to ask), the trips Pescamapa puts together, FAQ and the form.
 * Cards are the same partial as in the guide; contact data never reaches the page.
 */
defined( 'ABSPATH' ) || exit;

$idioma      = pm_lang();
$clave       = (string) get_query_var( 'pm_guias_clave' );
$dp          = pm_buscar( 'pm_destino', $clave, $idioma );
$d           = pm_destino( $dp->ID );
$prestadores = pm_prestadores_destino( $clave ); // public only, agreements first
$a           = [ 'lugar' => $d['nombre'] ];
$modificado  = get_post_modified_time( 'Y-m-d', true, $dp );
$hay_paq     = (bool) pm_destino_paquetes( $clave, $idioma );

// Short answer: how many places, how many have an agreement with Pescamapa (consent cards), and the dated rates the providers publish (at most three).
$n_lugares = count( $prestadores );
$n_acuerdo = count( array_filter( $prestadores, fn( $r ) => ! empty( $r['consentimiento'] ) ) );
$lugares   = pm_t( 1 === $n_lugares ? 'guias.resumen.lugares.uno' : 'guias.resumen.lugares.otros', [ 'n' => (string) $n_lugares ] );
$con_acuerdo = $n_acuerdo > 0 ? pm_t( 1 === $n_acuerdo ? 'guias.resumen.acuerdo.uno' : 'guias.resumen.acuerdo.otros', [ 'a' => (string) $n_acuerdo ] ) : '';
$precios = array_values( array_filter( $prestadores, fn( $r ) => $r['precio'] && $r['precio_fecha'] ) );
// Request links: the main one preselects this destination; the second adds the neighbours that make sense to compare (?tambien=).
$vecinas   = pm_destinos_para_comparar( $clave, $idioma );
$url_pedir = add_query_arg( 'destino', $clave, pm_url_viaje( $idioma ) );
$sep       = pm_t( 'guias.cta.y' );
$otras     = count( $vecinas ) > 1 ? implode( ', ', array_slice( array_column( $vecinas, 'nombre' ), 0, -1 ) ) . $sep . end( $vecinas )['nombre'] : ( $vecinas ? $vecinas[0]['nombre'] : '' );
// "En una mirada" (20/11/2026): each row is built only from what the card already shows. A place with nothing besides its name
// is left out (it stays in the cards); a column with no data in any row disappears; fewer than two rows: no table.
$fmt_lancha = function ( array $r ): string {
	if ( ! $r['capacidad'] ) {
		return '';
	}
	return 'lancha' === $r['capacidad_unidad'] ? pm_t( 'guias.mirada.lancha', [ 'n' => (string) $r['capacidad'] ] ) : pm_prestador_capacidad( $r );
};
$mirada = [];
foreach ( $prestadores as $r ) {
	$formato = array_filter( [
		$r['programa'] ?: implode( ' · ', array_map( fn( $k ) => pm_opcion( 'modalidad', $k ), $r['modalidades'] ) ),
		$fmt_lancha( $r ),
	] );
	// "Equipos de pesca" + "Equipos de pesca prestados" say the same: keep the more specific one (prestados).
	$claves_inc = in_array( 'equipos', $r['equipo'] ?? [], true ) ? array_values( array_diff( $r['incluye'] ?? [], [ 'equipos' ] ) ) : ( $r['incluye'] ?? [] );
	$incluye = array_values( array_unique( array_merge(
		array_map( fn( $k ) => pm_opcion( 'incluye', $k ), $claves_inc ),
		array_map( fn( $k ) => pm_opcion( 'equipo', $k ), $r['equipo'] ?? [] )
	) ) );
	$fila = [
		'r'         => $r,
		'formato'   => implode( ' · ', $formato ),
		'incluye'   => implode( ', ', $incluye ),
		'temporada' => (string) $r['temporada'],
		'precio'    => ( $r['precio'] && $r['precio_fecha'] ) ? $r['precio'] : '',
		'en_es'     => [ 'formato' => (bool) $r['programa'], 'temporada' => in_array( 'temporada', $r['en_espanol'] ?? [], true ), 'precio' => in_array( 'precio', $r['en_espanol'] ?? [], true ) ],
	];
	if ( $fila['formato'] || $fila['incluye'] || $fila['temporada'] || $fila['precio'] ) {
		$mirada[] = $fila;
	}
}
// Table order (20/11/2026, docs/prestadores.md): agreement first (as in the cards), then dated price, then more filled columns
// (formato, incluye, temporada), then card order. Only the table; the card list keeps its own order.
foreach ( $mirada as $i => $f ) {
	$mirada[ $i ]['idx']    = $i;
	$mirada[ $i ]['llenas'] = count( array_filter( [ $f['formato'], $f['incluye'], $f['temporada'] ] ) );
	$mirada[ $i ]['arriba'] = ! empty( $f['r']['acuerdo'] ) && ! empty( $f['r']['consentimiento'] );
}
usort( $mirada, fn( $a, $b ) => [ $b['arriba'], '' !== $b['precio'], $b['llenas'], $a['idx'] ] <=> [ $a['arriba'], '' !== $a['precio'], $a['llenas'], $b['idx'] ] );
$mirada   = array_slice( $mirada, 0, 6 );
$columnas = array_values( array_filter( [ 'formato', 'incluye', 'temporada', 'precio' ], fn( $c ) => array_filter( array_column( $mirada, $c ) ) ) );
if ( count( $mirada ) < 2 ) {
	$mirada = [];
}
$consultar = pm_t( 'guias.mirada.consultar' ); // read aloud by screen readers where a cell has no data

get_header(); ?>
<div class="v-destino v-paquetes v-guias">
	<header class="top">
		<div class="shell">
			<nav aria-label="<?php pm_e( 'ficha.migas' ); ?>" class="migas">
				<ol>
					<li><a href="<?php echo esc_url( pm_url_inicio( $idioma ) ); ?>"><?php pm_e( 'ficha.inicio' ); ?></a></li>
					<li><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] ); ?></a></li>
					<li><span aria-current="page"><?php pm_e( 'guias.eyebrow' ); ?></span></li>
				</ol>
			</nav>
			<div class="eyebrow"><?php pm_e( 'guias.eyebrow' ); ?></div>
			<h1><?php echo esc_html( pm_t_clave( 'guias.h1', $clave, $a ) ); ?></h1>
			<p class="bajada"><?php pm_e( 'guias.bajada', $a ); ?></p>
			<p class="confianza"><?php pm_e( 'ficha.por' ); ?> <?php echo esc_html( $d['autor'] ?: 'Equipo Pescamapa' ); ?> · <?php pm_e( 'ficha.actualizado' ); ?> <time datetime="<?php echo esc_attr( $modificado ); ?>"><?php echo esc_html( pm_fecha( $modificado, $idioma ) ); ?></time></p>
		</div>
	</header>
	<div class="shell cuerpo">
		<?php if ( $d['foto'] ) : ?>
			<div class="portada-ciudad"><?php echo pm_foto( $d['foto'], pm_alt_foto( (int) $d['foto'], $idioma, $d['nombre'] ), '(max-width: 960px) 100vw, 760px', [ 'clase' => 'portada', 'prioridad' => true ] ); // phpcs:ignore -- escaped inside ?></div>
		<?php endif; ?>
		<section class="respuesta" aria-labelledby="respuesta-h">
			<h2 id="respuesta-h"><?php pm_e( 'guias.resumen.h' ); ?></h2>
			<ul>
				<li><?php pm_e( 'guias.resumen.n', $a + [ 'lugares' => $lugares, 'acuerdo' => $con_acuerdo ] ); ?></li>
				<?php if ( $precios ) : ?>
					<li><?php pm_e( 'guias.resumen.precios' ); ?>
						<ul>
							<?php foreach ( array_slice( $precios, 0, 3 ) as $r ) : ?>
								<li><a href="#prestador-<?php echo (int) $r['id']; ?>"><?php echo esc_html( $r['nombre'] ); ?></a>: <?php pm_e( 'guias.resumen.precio', [ 'precio' => pm_precio_corto( (string) $r['precio'] ), 'cuando' => pm_fecha_relativa( $r['precio_fecha'], $idioma ) ] ); ?></li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php else : ?>
					<li><?php pm_e( 'guias.resumen.sinprecio' ); ?></li>
				<?php endif; ?>
				<li><?php pm_e( 'guias.resumen.licencia', $a ); ?> <a href="<?php echo esc_url( $d['url'] . '#reglamento' ); ?>"><?php pm_e( 'paquetes.guia.intro', $a ); ?></a></li>
				<li><?php pm_e( 'guias.resumen.como' ); ?> <a href="<?php echo esc_url( pm_url_presupuesto( $idioma ) ); ?>"><?php pm_e( 'pres.enlace.corto' ); ?></a></li>
			</ul>
		</section>
		<?php if ( $mirada ) : ?>
		<section class="mirada" aria-labelledby="mirada-h">
			<h2 id="mirada-h"><?php pm_e( 'guias.mirada.h' ); ?></h2>
			<p class="note"><?php pm_e( 'guias.mirada.p' ); ?></p>
			<div class="tabla-mirada">
				<table>
					<thead><tr>
						<th scope="col"><?php pm_e( 'guias.mirada.lugar' ); ?></th>
						<?php foreach ( $columnas as $col ) : ?><th scope="col"><?php pm_e( "guias.mirada.$col" ); ?></th><?php endforeach; ?>
					</tr></thead>
					<tbody>
						<?php foreach ( $mirada as $f ) : $r = $f['r']; ?>
							<tr>
								<th scope="row"><a href="#prestador-<?php echo (int) $r['id']; ?>"><?php echo esc_html( $r['nombre'] ); ?></a></th>
								<?php foreach ( $columnas as $col ) :
									$lang = ! empty( $f['en_es'][ $col ] ) ? ' lang="es"' : ''; ?>
									<?php if ( '' === $f[ $col ] ) : ?>
										<td class="vacio" data-label="<?php pm_e( "guias.mirada.$col" ); ?>"><span aria-hidden="true">&mdash;</span><span class="visually-hidden"><?php echo esc_html( $consultar ); ?></span></td>
									<?php elseif ( 'precio' === $col ) : ?>
										<td data-label="<?php pm_e( 'guias.mirada.precio' ); ?>"><?php echo wp_kses( pm_t( 'prest.precio', [ 'precio' => '<span' . $lang . '>' . esc_html( $f['precio'] ) . '</span>', 'cuando' => esc_html( pm_fecha_relativa( $r['precio_fecha'] ) ) ] ), [ 'span' => [ 'lang' => true ] ] ); ?></td>
									<?php else : ?>
										<td data-label="<?php pm_e( "guias.mirada.$col" ); ?>"<?php echo $lang; // phpcs:ignore ?>><?php echo esc_html( $f[ $col ] ); ?></td>
									<?php endif; ?>
								<?php endforeach; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>
		<?php endif; ?>
		<p class="cta-opciones">
			<a class="button primary" href="<?php echo esc_url( $url_pedir ); ?>"><?php pm_e( 'guias.cta.pedir', $a ); ?></a>
			<?php if ( $vecinas ) : ?>
				<a class="enlace-comparar" href="<?php echo esc_url( add_query_arg( 'tambien', implode( ',', array_column( $vecinas, 'clave' ) ), $url_pedir ) ); ?>"><?php pm_e( 'guias.cta.comparar', [ 'otras' => $otras ] ); ?></a>
			<?php endif; ?>
		</p>
		<?php get_template_part( 'parciales/preguntas', null, [ 'id' => 'preguntas-h' ] ); ?>
		<section class="lista-prestadores" aria-labelledby="lista-h">
			<h2 id="lista-h"><?php pm_e( 'guias.lista.h', $a ); ?></h2>
			<p><?php pm_e( 'guias.lista.p' ); ?></p>
			<?php if ( $consultada = pm_prestadores_consultada( $prestadores ) ) : ?><p class="note nota-fuentes"><?php pm_e( 'ficha.prestadores.nota', [ 'fecha' => pm_mes_anio( $consultada, $idioma ) ] ); ?></p><?php endif; ?>
			<?php foreach ( $prestadores as $r ) {
				get_template_part( 'parciales/prestador', null, $r + [ 'lugar' => $d['nombre'], 'pedir' => false ] ); // the request buttons are the one on top and the one after the cards
			} ?>
		</section>
		<?php if ( $hay_paq ) : // Only when the destination has trips put together: no intro and lone link otherwise. ?>
		<section class="armadas" aria-labelledby="armadas-h">
			<h2 id="armadas-h"><?php pm_e( 'guias.paquetes.h' ); ?></h2>
			<p><?php pm_e( 'guias.paquetes.p', $a ); ?></p>
			<p>
				<a class="button dark" href="<?php echo esc_url( pm_url_paquetes( $clave, $idioma ) ); ?>"><?php pm_e( 'guias.paquetes.link', $a ); ?></a>
				<a class="button" href="#consulta"><?php pm_e( 'guias.paquetes.pedir', $a ); ?></a>
			</p>
		</section>
		<?php endif; ?>
		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'guias.faq.h', $a ); ?></h2>
			<?php foreach ( pm_guias_faqs( $d, $clave, $prestadores ) as $f ) : ?>
				<h3><?php echo esc_html( $f['pregunta'] ); ?></h3>
				<p><?php echo esc_html( $f['respuesta'] ); ?></p>
			<?php endforeach; ?>
		</section>
		<p><a href="<?php echo esc_url( $d['url'] ); ?>"><?php pm_e( 'guias.guia', $a ); ?></a></p>
		<?php get_template_part( 'parciales/consulta', null, [ 'destino' => $d['clave'], 'lugar' => $d['nombre'], 'especies' => $d['especies'], 'boutique' => true ] ); ?>
	</div>
</div>
<a class="barra-pedir" href="#consulta"><?php pm_e( 'guias.barra' ); ?></a>
<script>
// The mobile bar disappears once the form is on screen.
const barra = document.querySelector('.barra-pedir'), form = document.querySelector('#consulta');
if (barra && form && 'IntersectionObserver' in window) {
	new IntersectionObserver((e) => barra.classList.toggle('oculta', e[0].isIntersecting)).observe(form);
}
</script>
<?php get_footer();
