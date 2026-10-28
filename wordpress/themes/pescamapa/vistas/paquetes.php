<?php
/** Fishing packages of one destination: options we put together, what each includes, dated "desde" price when confirmed. */
defined( 'ABSPATH' ) || exit;

$idioma = pm_lang();
$dp     = pm_buscar( 'pm_destino', (string) get_query_var( 'pm_paquete_clave' ), $idioma );
$d      = pm_destino( $dp->ID );
$paq    = pm_paquetes( $dp->ID );
$info   = pm_paquetes_info( $dp->ID );
$a      = [ 'lugar' => $d['nombre'] ];
$modificado = get_post_modified_time( 'Y-m-d', true, $dp );

get_header(); ?>
<div class="v-destino v-paquetes">
	<header class="top">
		<div class="shell">
			<nav aria-label="<?php pm_e( 'ficha.migas' ); ?>" class="migas">
				<ol>
					<li><a href="<?php echo esc_url( pm_url_inicio( $idioma ) ); ?>"><?php pm_e( 'ficha.inicio' ); ?></a></li>
					<li><a href="<?php echo esc_url( $d['url'] ); ?>"><?php echo esc_html( $d['nombre'] ); ?></a></li>
					<li><span aria-current="page"><?php pm_e( 'paquetes.eyebrow' ); ?></span></li>
				</ol>
			</nav>
			<div class="eyebrow"><?php pm_e( 'paquetes.eyebrow' ); ?></div>
			<h1><?php pm_e( 'paquetes.h1', $a ); ?></h1>
			<p class="bajada"><?php pm_e( 'paquetes.bajada', $a ); ?></p>
			<p class="confianza"><?php pm_e( 'ficha.por' ); ?> <?php echo esc_html( $d['autor'] ?: 'Equipo Pescamapa' ); ?> · <?php pm_e( 'ficha.actualizado' ); ?> <time datetime="<?php echo esc_attr( $modificado ); ?>"><?php echo esc_html( pm_fecha( $modificado, $idioma ) ); ?></time></p>
		</div>
	</header>
	<div class="shell cuerpo">
		<p class="intro"><?php pm_e( 'paquetes.intro', $a ); ?></p>
		<p><a href="<?php echo esc_url( $d['url'] ); ?>"><?php pm_e( 'paquetes.guia.intro', $a ); ?></a></p>
		<section class="como" aria-labelledby="como-h">
			<h2 id="como-h"><?php pm_e( 'paquetes.como.h' ); ?></h2>
			<ol>
				<li><?php pm_e( 'paquetes.como.1' ); ?></li>
				<li><?php pm_e( 'paquetes.como.2' ); ?></li>
				<li><?php pm_e( 'paquetes.como.3' ); ?></li>
			</ol>
		</section>
		<div class="lista">
			<?php foreach ( $paq as $p ) : ?>
				<article class="paquete">
					<h2><?php echo esc_html( $p['titulo'] ); ?></h2>
					<?php if ( $p['resumen'] ) : ?><p><?php echo esc_html( $p['resumen'] ); ?></p><?php endif; ?>
					<dl>
						<?php foreach ( [ 'modalidad', 'duracion', 'capacidad' ] as $c ) : if ( $p[ $c ] ) : ?>
							<div><dt><?php pm_e( "paquetes.$c" ); ?></dt><dd><?php echo esc_html( $p[ $c ] ); ?></dd></div>
						<?php endif; endforeach; ?>
					</dl>
					<?php if ( $p['variantes'] ) : ?>
						<h3><?php pm_e( 'paquetes.variantes' ); ?></h3>
						<ul><?php foreach ( $p['variantes'] as $v ) : ?><li><?php echo esc_html( $v ); ?></li><?php endforeach; ?></ul>
					<?php endif; ?>
					<?php if ( $p['incluye'] ) : ?>
						<h3><?php pm_e( 'paquetes.incluye' ); ?></h3>
						<ul><?php foreach ( $p['incluye'] as $v ) : ?><li><?php echo esc_html( $v ); ?></li><?php endforeach; ?></ul>
					<?php else : ?>
						<p class="note"><?php pm_e( 'paquetes.incluye.vacio' ); ?></p>
					<?php endif; ?>
					<?php if ( $p['no_incluye'] ) : ?>
						<h3><?php pm_e( 'paquetes.no_incluye' ); ?></h3>
						<ul><?php foreach ( $p['no_incluye'] as $v ) : ?><li><?php echo esc_html( $v ); ?></li><?php endforeach; ?></ul>
					<?php endif; ?>
					<div class="precio">
						<?php if ( $p['precio'] ) : ?>
							<strong><?php pm_e( 'paquetes.precio.desde', [ 'precio' => pm_paquete_precio_txt( $p['precio'], $idioma ), 'unidad' => $p['precio']['unidad'] ] ); ?></strong>
							<span class="note"><?php pm_e( 'paquetes.precio.fecha', [ 'fecha' => pm_fecha( $p['precio']['fecha'], $idioma ) ] ); ?></span>
						<?php else : ?>
							<span><?php pm_e( 'paquetes.precio.consultar' ); ?></span>
						<?php endif; ?>
					</div>
					<a class="button dark pedir" href="#consulta" data-mensaje="<?php echo esc_attr( pm_t( 'paquetes.msg', [ 'titulo' => $p['titulo'] ] ) ); ?>"><?php pm_e( 'paquetes.cta' ); ?></a>
				</article>
			<?php endforeach; ?>
		</div>
		<?php if ( $info ) : ?>
			<section class="antes" aria-labelledby="antes-h">
				<h2 id="antes-h"><?php pm_e( 'paquetes.antes.h' ); ?></h2>
				<?php foreach ( $info as $b ) : ?>
					<div class="bloque">
						<h3><?php echo esc_html( $b['titulo'] ); ?></h3>
						<?php foreach ( $b['parrafos'] as $t ) : ?><p><?php echo esc_html( $t ); ?></p><?php endforeach; ?>
						<?php if ( $b['fuente'] ) : ?>
							<p class="note"><?php pm_e( 'paquetes.fuente' ); ?>: <?php if ( $b['url'] ) : ?><a href="<?php echo esc_url( $b['url'] ); ?>" rel="noopener"><?php echo esc_html( $b['fuente'] ); ?></a><?php else : echo esc_html( $b['fuente'] ); endif; ?><?php if ( $b['consultada'] ) : ?>, <?php pm_e( 'paquetes.consultada', [ 'fecha' => pm_fecha( $b['consultada'], $idioma ) ] ); ?><?php endif; ?></p>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</section>
		<?php endif; ?>
		<section class="faq" aria-labelledby="faq-h">
			<h2 id="faq-h"><?php pm_e( 'paquetes.faq.h', $a ); ?></h2>
			<?php foreach ( [ 1, 2, 3, 4 ] as $n ) : ?>
				<h3><?php pm_e( "paquetes.faq.$n.q", $a ); ?></h3>
				<p><?php pm_e( "paquetes.faq.$n.a", $a ); ?></p>
			<?php endforeach; ?>
		</section>
		<p class="note"><?php pm_e( 'paquetes.licencia', $a ); ?></p>
		<p><a href="<?php echo esc_url( $d['url'] ); ?>"><?php pm_e( 'paquetes.guia', $a ); ?></a></p>
		<?php get_template_part( 'parciales/consulta', null, [ 'destino' => $d['clave'], 'lugar' => $d['nombre'], 'especies' => $d['especies'] ] ); ?>
	</div>
</div>
<script>
// "Pedir cotización" on a package: put the option in the message of the form below.
document.querySelectorAll('.paquete .pedir').forEach((b) => b.addEventListener('click', () => {
	const m = document.querySelector('#consulta textarea[name="mensaje"]');
	if (m) m.value = b.dataset.mensaje;
}));
</script>
<?php get_footer();
