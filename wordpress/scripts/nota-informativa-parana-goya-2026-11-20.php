<?php
/**
 * Pieza informativa (owner, 20/11/2026): marca la nota «El Paraná en Goya» (clave parana-goya-20-noviembre-2026, es #870, en #871, pt #872)
 * con el campo `nota_informativa`. El campo se guarda SOLO en la fila en español y vale para los tres idiomas (igual que nota_destino /
 * nota_tipo: pm_nota() lo lee de la fila es por clave). Con el campo activo, vistas/nota.php no muestra el cuadro «¿Querés pescar esto?»
 * (.cta-nota), la barra fija del celular (.barra-pedir) ni el bloque de guías con precios (.guias-especie).
 * Simulacro por defecto ($aplicar = false). Idempotente. Solo escribe ese campo. Lee de vuelta (VERIFIED) y pide las tres URLs desde el
 * servidor para comprobar que los marcadores de CTA no están; si el pedido no se puede hacer, lo dice (la meta igual queda verificada).
 * Con wp-script.yml (script = nota-informativa-parana-goya-2026-11-20; aplicar=true para la corrida real).
 */

$aplicar = false;
$clave   = 'parana-goya-20-noviembre-2026';
$marcas  = [ 'class="cta-nota"', 'class="barra-pedir"', 'class="guias-especie"', 'cta-gratis', 'banda-gratis' ];

$es = pm_buscar( 'pm_nota', $clave, 'es' );
$es || WP_CLI::error( "No encuentro la nota {$clave} en es." );
$filas = [ 'es' => $es ];
foreach ( [ 'en', 'pt' ] as $i ) {
	$p = pm_buscar( 'pm_nota', $clave, $i );
	$p || WP_CLI::error( "No encuentro la nota {$clave} en {$i}." );
	$filas[ $i ] = $p;
}
foreach ( $filas as $i => $p ) {
	WP_CLI::log( "#{$p->ID} [{$i}] " . get_permalink( $p->ID ) );
}
$hoy = (bool) pm_campo( 'nota_informativa', $es->ID );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": nota_informativa en la fila es #{$es->ID}: " . ( $hoy ? 'ya activa' : 'apagada -> activa' ) );

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
if ( ! $hoy ) {
	update_field( 'nota_informativa', 1, $es->ID );
}
clean_post_cache( $es->ID );

$ok = true;
foreach ( $filas as $i => $p ) {
	$n   = pm_nota( $p->ID );
	$ok  = $ok && $n['informativa'];
	WP_CLI::log( "meta [{$i}] #{$p->ID} informativa=" . ( $n['informativa'] ? 'sí' : 'NO' ) );
}
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota_informativa activa en es, en y pt ({$clave})" );

foreach ( $filas as $i => $p ) {
	$r = wp_remote_get( add_query_arg( 'pm_verif', time(), get_permalink( $p->ID ) ), [ 'timeout' => 30, 'redirection' => 3 ] );
	if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) ) {
		WP_CLI::log( "RENDER [{$i}]: no se pudo pedir la página (" . ( is_wp_error( $r ) ? $r->get_error_message() : wp_remote_retrieve_response_code( $r ) ) . "): solo verificada la meta" );
		continue;
	}
	$html   = wp_remote_retrieve_body( $r );
	$estan  = array_values( array_filter( $marcas, fn( $m ) => false !== strpos( $html, $m ) ) );
	$cuerpo = false !== strpos( $html, 'class="prose"' );
	WP_CLI::log( ( ! $estan && $cuerpo ? 'VERIFIED' : 'FALLÓ' ) . ": render [{$i}] sin CTA " . ( $estan ? '(siguen: ' . implode( ', ', $estan ) . ')' : '' ) . ( $cuerpo ? '' : ' (no veo el cuerpo de la nota; ¿caché?)' ) );
}
