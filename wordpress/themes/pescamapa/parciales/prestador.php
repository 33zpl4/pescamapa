<?php
/** Outfitter card in a destination guide (no contact data). $args: the pm_prestador() array. */
defined( 'ABSPATH' ) || exit;

$r     = $args;
// 'vista-previa' cards only reach logged-in editors (pm_prestadores_destino).
$filas = [
	'prest.modalidades' => array_map( fn( $k ) => pm_opcion( 'modalidad', $k ), $r['modalidades'] ),
	'prest.lancha'      => array_filter( [ $r['lancha'], $r['capacidad'] ? pm_t( 'prest.capacidad', [ 'n' => (string) $r['capacidad'] ] ) : '' ] ),
	'prest.motor'       => array_filter( [ $r['motor'] ] ),
	'prest.equipo'      => array_map( fn( $k ) => pm_opcion( 'equipo', $k ), $r['equipo'] ),
	'prest.incluye'     => array_map( fn( $k ) => pm_opcion( 'incluye', $k ), $r['incluye'] ),
	'prest.temporada'   => array_filter( [ $r['temporada'] ] ),
];
?>
<article class="prestador<?php echo pm_prestador_publico( $r ) ? '' : ' vista-previa'; ?>">
	<?php if ( ! pm_prestador_publico( $r ) ) : ?>
		<p class="sello">Vista previa, solo editores: <?php echo esc_html( $r['ejemplo'] ? 'datos de ejemplo, reemplazalos y destildá "Datos de ejemplo".' : 'falta que acepte aparecer.' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $r['id'] ) ); ?>">Editar</a></p>
	<?php endif; ?>
	<?php if ( $r['fotos'] ) {
		echo pm_foto( $r['fotos'][0], $r['nombre'], '(max-width: 720px) 100vw, 320px', [ 'clase' => 'portada' ] ); // phpcs:ignore -- escaped inside
	} ?>
	<div class="cuerpo">
		<p class="tipo"><?php echo esc_html( implode( ' · ', array_map( fn( $k ) => pm_opcion( 'tipo', $k ), $r['tipo'] ) ) ); ?></p>
		<h3><?php echo esc_html( $r['nombre'] ); ?></h3>
		<?php if ( $r['descripcion'] ) : ?><p><?php echo esc_html( $r['descripcion'] ); ?></p><?php endif; ?>
		<dl>
			<?php foreach ( $filas as $clave => $valores ) :
				if ( $valores ) : ?>
					<div><dt><?php pm_e( $clave ); ?></dt><dd><?php echo esc_html( implode( ' · ', $valores ) ); ?></dd></div>
				<?php endif;
			endforeach; ?>
		</dl>
		<?php if ( $r['precio'] && $r['precio_fecha'] ) : ?><p class="precio"><?php pm_e( 'prest.precio', [ 'precio' => $r['precio'], 'fecha' => pm_fecha( $r['precio_fecha'] ) ] ); ?></p><?php endif; ?>
		<a class="button dark" href="#consulta" data-prestador="<?php echo (int) $r['id']; ?>" data-nombre="<?php echo esc_attr( $r['nombre'] ); ?>"><?php pm_e( 'prest.consultar' ); ?></a>
	</div>
</article>
