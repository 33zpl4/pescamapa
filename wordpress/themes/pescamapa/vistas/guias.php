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

// Short answer: how many of each type, and the dated rates the providers publish (at most three).
$por_tipo = [];
foreach ( $prestadores as $r ) {
	foreach ( $r['tipo'] as $t ) {
		$por_tipo[ $t ] = ( $por_tipo[ $t ] ?? 0 ) + 1;
	}
}
$tipos   = implode( ' · ', array_map( fn( $t, $n ) => $n . ' × ' . pm_opcion( 'tipo', $t ), array_keys( $por_tipo ), $por_tipo ) );
$precios = array_values( array_filter( $prestadores, fn( $r ) => $r['precio'] && $r['precio_fecha'] ) );
// Places that appear in an official listing (the mark on each card): how many, and from which source.
$en_listado = array_values( array_filter( $prestadores, fn( $r ) => $r['listado'] ) );
// Request links: the main one preselects this destination; the second adds the neighbours that make sense to compare (?tambien=).
$vecinas   = pm_destinos_para_comparar( $clave, $idioma );
$url_pedir = add_query_arg( 'destino', $clave, pm_url_viaje( $idioma ) );
$sep       = pm_t( 'guias.cta.y' );
$otras     = count( $vecinas ) > 1 ? implode( ', ', array_slice( array_column( $vecinas, 'nombre' ), 0, -1 ) ) . $sep . end( $vecinas )['nombre'] : ( $vecinas ? $vecinas[0]['nombre'] : '' );
// "En una mirada": at most six rows, the same order as the cards; what is not published says "a consultar".
$mirada    = array_slice( $prestadores, 0, 6 );
$consultar = pm_t( 'guias.mirada.consultar' );
$es_attr   = fn( array $r, string $campo ) => in_array( $campo, $r['en_espanol'] ?? [], true ) ? ' lang="es"' : '';

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
				<li><?php pm_e( 'guias.resumen.n', $a + [ 'n' => (string) count( $prestadores ), 'tipos' => $tipos ] ); ?></li>
				<?php if ( $precios ) : ?>
					<li><?php pm_e( 'guias.resumen.precios' ); ?>
						<ul>
							<?php foreach ( array_slice( $precios, 0, 3 ) as $r ) : ?>
								<li><a href="#prestador-<?php echo (int) $r['id']; ?>"><?php pm_e( 'guias.resumen.precio', [ 'precio' => $r['precio'], 'nombre' => $r['nombre'], 'cuando' => pm_fecha_relativa( $r['precio_fecha'], $idioma ) ] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</li>
				<?php else : ?>
					<li><?php pm_e( 'guias.resumen.sinprecio' ); ?></li>
				<?php endif; ?>
				<li><?php pm_e( 'guias.resumen.licencia', $a ); ?> <a href="<?php echo esc_url( $d['url'] . '#reglamento' ); ?>"><?php pm_e( 'paquetes.guia.intro', $a ); ?></a></li>
				<?php if ( $en_listado ) : ?><li><?php pm_e( 'guias.resumen.listado', [ 'fuente' => $en_listado[0]['listado']['fuente'], 'n' => (string) count( $en_listado ), 'total' => (string) count( $prestadores ) ] ); ?></li><?php endif; ?>
				<li><?php pm_e( 'guias.resumen.como' ); ?></li>
			</ul>
		</section>
		<?php if ( $mirada ) : ?>
		<section class="mirada" aria-labelledby="mirada-h">
			<h2 id="mirada-h"><?php pm_e( 'guias.mirada.h' ); ?></h2>
			<p class="note"><?php pm_e( 'guias.mirada.p' ); ?></p>
			<div class="tabla-mirada">
				<table>
					<thead><tr>
						<?php foreach ( [ 'lugar', 'formato', 'incluye', 'temporada', 'precio' ] as $col ) : ?><th scope="col"><?php pm_e( "guias.mirada.$col" ); ?></th><?php endforeach; ?>
					</tr></thead>
					<tbody>
						<?php foreach ( $mirada as $r ) :
							$formato = $r['programa'] ?: implode( ' · ', array_map( fn( $k ) => pm_opcion( 'modalidad', $k ), $r['modalidades'] ) );
							$incluye = implode( ', ', array_map( fn( $k ) => pm_opcion( 'incluye', $k ), $r['incluye'] ) );
							?>
							<tr>
								<th scope="row" data-label="<?php pm_e( 'guias.mirada.lugar' ); ?>"><a href="#prestador-<?php echo (int) $r['id']; ?>"><?php echo esc_html( $r['nombre'] ); ?></a></th>
								<td data-label="<?php pm_e( 'guias.mirada.formato' ); ?>"<?php echo $r['programa'] ? $es_attr( $r, 'programa' ) : ''; // phpcs:ignore ?>><?php echo esc_html( $formato ?: $consultar ); ?></td>
								<td data-label="<?php pm_e( 'guias.mirada.incluye' ); ?>"><?php echo esc_html( $incluye ?: $consultar ); ?></td>
								<td data-label="<?php pm_e( 'guias.mirada.temporada' ); ?>"<?php echo $es_attr( $r, 'temporada' ); // phpcs:ignore ?>><?php echo esc_html( $r['temporada'] ?: $consultar ); ?></td>
								<td data-label="<?php pm_e( 'guias.mirada.precio' ); ?>"><?php
									if ( $r['precio'] && $r['precio_fecha'] ) {
										echo wp_kses( pm_t( 'prest.precio', [ 'precio' => '<span' . $es_attr( $r, 'precio' ) . '>' . esc_html( $r['precio'] ) . '</span>', 'cuando' => esc_html( pm_fecha_relativa( $r['precio_fecha'] ) ) ] ), [ 'span' => [ 'lang' => true ] ] );
									} else {
										echo esc_html( $consultar );
									} ?></td>
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
				get_template_part( 'parciales/prestador', null, $r + [ 'lugar' => $d['nombre'] ] );
			} ?>
		</section>
		<section class="armadas" aria-labelledby="armadas-h">
			<h2 id="armadas-h"><?php pm_e( 'guias.paquetes.h' ); ?></h2>
			<p><?php pm_e( 'guias.paquetes.p', $a ); ?></p>
			<p>
				<?php if ( $hay_paq ) : ?><a class="button dark" href="<?php echo esc_url( pm_url_paquetes( $clave, $idioma ) ); ?>"><?php pm_e( 'guias.paquetes.link', $a ); ?></a><?php endif; ?>
				<a class="button" href="#consulta"><?php pm_e( 'guias.paquetes.pedir', $a ); ?></a>
			</p>
		</section>
		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'guias.faq.h', $a ); ?></h2>
			<?php foreach ( pm_guias_faqs( $d, $clave ) as $f ) : ?>
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
// The card buttons are wired by the inquiry partial itself. The mobile bar disappears once the form is on screen.
const barra = document.querySelector('.barra-pedir'), form = document.querySelector('#consulta');
if (barra && form && 'IntersectionObserver' in window) {
	new IntersectionObserver((e) => barra.classList.toggle('oculta', e[0].isIntersecting)).observe(form);
}
</script>
<?php get_footer();
