<?php
/**
 * "Guides who fish for <species>, with prices": one line per town that has a public outfitter listing this species,
 * with the first dated price (agreements come first in pm_prestadores_destino). Args: especie (clave), idioma.
 * Nothing is written here: it reads the same cards as the city pages. Prints nothing when no town qualifies.
 */
defined( 'ABSPATH' ) || exit;

$especie = (string) ( $args['especie'] ?? '' );
$idioma  = (string) ( $args['idioma'] ?? pm_lang() );
$nombre  = pm_ui()['etiquetas'][ $especie ][ $idioma ] ?? $especie;
if ( '' === $especie ) {
	return;
}
// A guide may list the species under a narrower key (the Río Grande lodges list "trucha-marron-anadroma"): it still is trout.
$claves = [ 'trucha' => [ 'trucha', 'trucha-marron-anadroma' ] ][ $especie ] ?? [ $especie ];
$filas  = [];
foreach ( pm_destinos_lista( $idioma ) as $d ) {
	if ( ! pm_destino_guias( $d['clave'], $idioma ) ) {
		continue;
	}
	$lista = array_values( array_filter( pm_prestadores_destino( $d['clave'] ), fn( $r ) => (bool) array_intersect( $claves, $r['especies'] ) ) );
	if ( ! $lista ) {
		continue;
	}
	$con_precio = array_values( array_filter( $lista, fn( $r ) => $r['precio'] && $r['precio_fecha'] ) );
	$filas[]    = [ 'd' => $d, 'n' => count( $lista ), 'precio' => $con_precio[0] ?? null ];
}
if ( ! $filas ) {
	return;
}
?>
<section class="guias-especie" aria-labelledby="gesp-h">
	<h2 id="gesp-h"><?php pm_e( 'gesp.h2', [ 'especie' => mb_strtolower( $nombre ) ] ); ?></h2>
	<p><?php pm_e( 'gesp.p' ); ?></p>
	<ul>
		<?php foreach ( $filas as $f ) : ?>
			<li>
				<strong><?php pm_e( 'gesp.linea', [ 'n' => (string) $f['n'], 'lugar' => $f['d']['nombre'] ] ); ?></strong>:
				<?php if ( $f['precio'] ) : ?><?php pm_e( 'gesp.precio', [ 'precio' => $f['precio']['precio'], 'nombre' => $f['precio']['nombre'], 'cuando' => pm_fecha_relativa( $f['precio']['precio_fecha'], $idioma ) ] ); ?><?php else : ?><?php pm_e( 'gesp.sinprecio' ); ?><?php endif; ?>
				· <a href="<?php echo esc_url( pm_url_guias( $f['d']['clave'], $idioma ) ); ?>"><?php pm_e( 'gesp.link' ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
