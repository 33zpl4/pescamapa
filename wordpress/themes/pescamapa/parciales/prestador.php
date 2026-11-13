<?php
/** Outfitter card in a destination guide (no contact data). $args: the pm_prestador() array. */
defined( 'ABSPATH' ) || exit;

$r     = $args;
$es    = fn( string $campo ) => in_array( $campo, $r['en_espanol'] ?? [], true ) ? ' lang="es"' : ''; // text still in Spanish on a pt/en page
// 'vista-previa' cards only reach logged-in editors (pm_prestadores_destino).
$filas = [
	'prest.modalidades' => array_map( fn( $k ) => pm_opcion( 'modalidad', $k ), $r['modalidades'] ),
	'prest.lancha'      => array_filter( [ $r['lancha'], 'lancha' === $r['capacidad_unidad'] ? pm_prestador_capacidad( $r ) : '' ] ),
	'prest.capacidad_fila' => 'lancha' === $r['capacidad_unidad'] ? [] : array_filter( [ pm_prestador_capacidad( $r ) ] ),
	'prest.motor'       => array_filter( [ $r['motor'] ] ),
	'prest.equipo'      => array_map( fn( $k ) => pm_opcion( 'equipo', $k ), $r['equipo'] ),
	'prest.incluye'     => array_map( fn( $k ) => pm_opcion( 'incluye', $k ), $r['incluye'] ),
	'prest.programa'    => array_filter( [ $r['programa'] ] ),
	'prest.estadia'     => array_filter( [ $r['estadia'] ] ),
	'prest.acompanante' => array_filter( [ $r['acompanante'] ] ), // price-like: dated or "a consultar", see below
	'prest.no_incluye'  => array_filter( [ $r['no_incluye'] ] ),
	'prest.temporada'   => array_filter( [ $r['temporada'] ] ),
	'prest.idiomas'     => array_map( fn( $k ) => pm_opcion( 'idioma', $k ), $r['idiomas'] ),
	'prest.accesible'   => 'si' === $r['accesible'] ? [ '♿ ' . pm_t( 'prest.accesible.si' ) ] : [],
];
?>
<article id="prestador-<?php echo (int) $r['id']; ?>" class="prestador<?php echo pm_prestador_publico( $r ) ? '' : ' vista-previa'; echo $r['fotos'] ? '' : ' sin-fotos'; ?>">
	<?php if ( ! pm_prestador_publico( $r ) ) : ?>
		<p class="sello">Vista previa, solo editores: <?php echo esc_html( $r['ejemplo'] ? 'datos de ejemplo, reemplazalos y destildá "Datos de ejemplo".' : 'marcá "Aceptó aparecer" o "Listado con información pública".' ); ?> <a href="<?php echo esc_url( get_edit_post_link( $r['id'] ) ); ?>">Editar</a></p>
	<?php endif; ?>
	<?php if ( $r['fotos'] ) : ?>
		<div class="galeria">
			<?php echo pm_foto( $r['fotos'][0], $r['nombre'], '(max-width: 720px) 100vw, 320px', [ 'clase' => 'portada' ] ); // phpcs:ignore -- escaped inside ?>
			<?php $mini = array_slice( $r['fotos'], 1, 3 ); ?>
			<?php if ( $mini ) : ?>
				<div class="miniaturas">
					<?php foreach ( $mini as $f ) {
						echo pm_foto( (int) $f, $r['nombre'], '110px', [ 'clase' => 'mini', 'tamano' => 'medium' ] ); // phpcs:ignore -- escaped inside
					} ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<div class="cuerpo">
		<?php if ( $r['acuerdo'] && $r['consentimiento'] ) : ?>
			<p class="acuerdo"><strong><?php pm_e( 'prest.acuerdo' ); ?></strong> <span><?php pm_e( 'prest.acuerdo_nota' ); ?></span></p>
		<?php endif; ?>
		<p class="tipo"><?php echo esc_html( implode( ' · ', array_map( fn( $k ) => pm_opcion( 'tipo', $k ), $r['tipo'] ) ) ); ?></p>
		<h3><?php echo esc_html( $r['nombre'] ); ?></h3>
		<?php if ( $r['descripcion'] ) : ?><p<?php echo $es( 'descripcion' ); // phpcs:ignore ?>><?php echo esc_html( $r['descripcion'] ); ?></p><?php endif; ?>
		<dl>
			<?php foreach ( $filas as $clave => $valores ) :
				if ( $valores ) : ?>
					<div><dt><?php pm_e( $clave ); ?></dt><?php if ( 'prest.acompanante' === $clave ) : // Same rule as the price: the figure only shows with its review date. ?>
						<dd><?php if ( $r['precio_fecha'] ) { echo wp_kses( pm_t( 'prest.precio', [ 'precio' => '<span' . $es( 'acompanante' ) . '>' . esc_html( implode( ' · ', $valores ) ) . '</span>', 'cuando' => esc_html( pm_fecha_relativa( $r['precio_fecha'] ) ) ] ), [ 'span' => [ 'lang' => true ] ] ); } else { pm_e( 'prest.acompanante_consultar' ); } ?></dd>
					<?php else : ?>
						<dd<?php echo $es( [ 'prest.lancha' => 'lancha', 'prest.motor' => 'motor', 'prest.temporada' => 'temporada', 'prest.programa' => 'programa', 'prest.estadia' => 'estadia', 'prest.no_incluye' => 'no_incluye' ][ $clave ] ?? '' ); // phpcs:ignore ?>><?php echo esc_html( implode( ' · ', $valores ) ); ?></dd>
					<?php endif; ?></div>
				<?php endif;
			endforeach; ?>
		</dl>
		<?php if ( $r['precio'] && $r['precio_fecha'] ) : ?>
			<p class="precio"><?php echo wp_kses( pm_t( 'prest.precio', [ 'precio' => '<span' . $es( 'precio' ) . '>' . esc_html( $r['precio'] ) . '</span>', 'cuando' => esc_html( pm_fecha_relativa( $r['precio_fecha'] ) ) ] ), [ 'span' => [ 'lang' => true ] ] ); ?></p>
		<?php else : ?>
			<p class="precio"><?php pm_e( 'prest.precio_consultar' ); ?></p>
		<?php endif; ?>
		<?php if ( $oferta = pm_prestador_oferta( $r ) ) : ?>
			<p class="oferta"><?php pm_e( 'prest.oferta', [ 'oferta' => $oferta ] ); ?></p>
		<?php endif; ?>
		<?php if ( ! $r['consentimiento'] && pm_prestador_publico( $r ) ) :
			$asunto = rawurlencode( pm_t( 'prest.baja_asunto', [ 'nombre' => $r['nombre'] ] ) ); ?>
			<p class="sin-verificar note">
				<?php pm_e( 'prest.sin_verificar' ); ?>
				<a href="mailto:<?php echo esc_attr( PM_EMAIL_PUBLICO ); ?>?subject=<?php echo $asunto; // phpcs:ignore -- encoded above ?>"><?php pm_e( 'prest.corregir' ); ?></a>
				<?php if ( $r['fuente'] ) : ?>· <?php pm_e( 'prest.fuente' ); ?>: <a href="<?php echo esc_url( $r['fuente'] ); ?>" rel="noopener nofollow"><?php echo esc_html( preg_replace( '/^www\./', '', (string) wp_parse_url( $r['fuente'], PHP_URL_HOST ) ) ); ?></a><?php endif; ?>
			</p>
		<?php endif; ?>
		<a class="button dark" href="#consulta" data-prestador="<?php echo (int) $r['id']; ?>" data-nombre="<?php echo esc_attr( $r['nombre'] ); ?>"><?php pm_e( 'prest.consultar', [ 'lugar' => $r['lugar'] ?? '' ] ); ?></a>
	</div>
</article>
