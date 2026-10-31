<?php
/**
 * Second fix on the live note "Fly fishing Argentina on a budget" (en), 31/10/2026. Owner: do not give the "520-550" range ("it's weird");
 * the Limay package at "from 550" is fair because it includes the permit, lunch and more. So the note now says "around USD 550 per boat"
 * (what the operators' own pages show, 520-550, with the licence usually extra), drops the table that named four operators and their
 * rates (the pages stay in the sources list), and links our Bariloche packages page from the call to action.
 * All-or-nothing: every fragment must be found exactly once, otherwise nothing is written.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = nota-por-tu-cuenta-ajuste2-2026-10-31).
 */

$aplicar = false;
$pkg     = esc_url( pm_url_paquetes( 'bariloche', 'en' ) );

$cambios = [
	[ '(USD 520 to 550 per boat for one or two anglers on the Limay, as four operators publish it)', '(around USD 550 per boat for one or two anglers on the Limay, going by the rates operators publish)' ],
	[ 'USD 520 to 550 per boat</td><td>Four operators, rates read on 31 October 2026 (table below)', 'Around USD 550 per boat</td><td>Operators\' own websites, rates read on 31 October 2026' ],
	[ 'and the guided day is USD 520 to 550 for the boat.', 'and the guided day is about USD 550 for the boat.' ],
	[ 'Ask every guide whether the permit is included. Of the four operators below, three list the licence as not included, and two of those offer to arrange it for USD 35 to 40. The fourth does not say on its page.', 'Ask every guide whether the permit is included: most operators we read list the licence as an extra, and some offer to arrange it for around USD 35 to 40.' ],
	[ 'These are the rates the operators publish, read on 31 October 2026. They are not quotes for your dates and we have not fished with them, so this is not a recommendation.', 'Rates on several operators\' own websites, read on 31 October 2026, cluster around USD 550 for a full day with a boat for one or two anglers, and the licence is usually extra. They are published rates, not quotes for your dates, and we have not fished with these operators, so this is not a recommendation.' ],
	[ 'We pass the request to a local guide and write back with options, at no cost or commitment.', 'We pass the request to a local guide and write back with options, at no cost or commitment. Our <a href="' . $pkg . '">Bariloche packages page</a> shows a full day on the Limay, with what it includes and a reference price.' ],
];
$patron_tabla = '/<!-- wp:table -->\s*<figure class="wp-block-table"><table><thead><tr><th>Operator<\/th>.*?<\/figure>\s*<!-- \/wp:table -->\s*/s';
$dato_antes   = 'A full day with a guide and a boat on the Limay is published at USD 520 to 550 per boat for one or two anglers; the licence is usually extra.';
$dato_despues = 'A full day with a guide and a boat on the Limay is published at around USD 550 per boat for one or two anglers; the licence is usually extra.';

$notas = array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'patagonia-por-tu-cuenta', 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === 'en' );
if ( 1 !== count( $notas ) ) {
	WP_CLI::error( 'no encontré exactamente una nota patagonia-por-tu-cuenta (en).' );
}
$nota = array_values( $notas )[0];
$c    = $nota->post_content;
$mal  = [];
foreach ( $cambios as $i => [ $a, $d ] ) {
	$n = substr_count( $c, $a );
	WP_CLI::log( "  fragmento " . ( $i + 1 ) . ": $n vez/veces" );
	1 === $n || $mal[] = $i + 1;
}
$tablas = preg_match_all( $patron_tabla, $c );
WP_CLI::log( "  tabla de operadores: $tablas vez/veces" );
1 === $tablas || $mal[] = 'tabla';
$filas = pm_filas( 'nota_datos', $nota->ID );
$hay   = array_keys( array_filter( array_column( $filas, 'dato' ), fn( $d ) => $d === $dato_antes ) );
WP_CLI::log( '  dato de la caja de claves: ' . count( $hay ) . ' vez/veces' );
1 === count( $hay ) || $mal[] = 'dato';
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": nota #{$nota->ID}" . ( $mal ? ' — NO COINCIDE: ' . implode( ', ', $mal ) . '; no se escribe nada' : '' ) );
if ( $mal ) {
	return;
}
if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado.' );
	return;
}
foreach ( $cambios as [ $a, $d ] ) {
	$c = str_replace( $a, $d, $c );
}
$c = preg_replace( $patron_tabla, '', $c, 1 );
wp_update_post( [ 'ID' => $nota->ID, 'post_content' => wp_slash( $c ) ] );
$filas[ $hay[0] ]['dato'] = $dato_despues;
update_field( 'nota_datos', $filas, $nota->ID );
$leido = get_post_field( 'post_content', $nota->ID );
$datos = array_column( pm_filas( 'nota_datos', $nota->ID ), 'dato' );
$ok    = false === strpos( $leido, '520 to 550' ) && false === strpos( $leido, 'Trout Bariloche</a>' ) && false !== strpos( $leido, 'around USD 550 per boat' ) && false !== strpos( $leido, 'Bariloche packages page' ) && in_array( $dato_despues, $datos, true ) && false === strpos( implode( ' ', $datos ), '520' );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota #{$nota->ID} " . get_permalink( $nota->ID ) );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
WP_CLI::success( 'Nota corregida.' );
