<?php
/**
 * Trout species page (es/en/pt), 13/11/2026 (follow-up): adds the adult means of the Giese thesis to the sea-run paragraph, right after the
 * abstract's statement that Gallegos adults are longer and that weights do not differ among the three rivers. Owner policy 13/11/2026: descriptive
 * figures that several independent reports agree on, with a named source, are published attributed. EVIDENCE LEVEL: ATTRIBUTED, not read by us:
 * the figures come from the owner's pasted summaries of the thesis and two independent assistant replies (one: mean total lengths about 63 to 67 cm
 * and mean weights about 3.8 to 4 kg for anadromous adults of the Gallegos, Grande and Irigoyen; the other: the table of Chapter 3, Gallegos 66.5 cm
 * and 3.8 kg, Grande 62.8 cm and 4.0 kg, Irigoyen 57.5 cm and 3.2 kg). The thesis PDF answered the runner with an anti-robot page on 13/11/2026 and
 * the repository abstract does not contain them. No year, no claim about how the fish were sampled, and the observed ranges (from one reply only) are left out.
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = giese-medias-2026-11-13).
 */
$aplicar = true;
$hoy     = '20261113';

$t = [
	'es' => [
		'ancla' => 'no se observaron diferencias de peso entre los tres ríos.',
		'nuevo' => ' Según la tesis doctoral de Giese (Universidad Nacional del Comahue, «Estructuración geográfica en poblaciones de trucha marrón migratoria en ríos de la Patagonia Austral»), en los adultos muestreados para el estudio el largo medio fue de 66,5 cm y el peso medio de 3,8 kg en el río Gallegos; de 62,8 cm y 4,0 kg en el Grande; y de 57,5 cm y 3,2 kg en el Irigoyen.',
	],
	'en' => [
		'ancla' => 'no weight differences were observed among the three rivers.',
		'nuevo' => ' According to Giese\'s doctoral thesis (Universidad Nacional del Comahue, “Estructuración geográfica en poblaciones de trucha marrón migratoria en ríos de la Patagonia Austral”), in the adults sampled for the study the mean length was 66.5 cm and the mean weight 3.8 kg in the Gallegos River; 62.8 cm and 4.0 kg in the Grande; and 57.5 cm and 3.2 kg in the Irigoyen.',
	],
	'pt' => [
		'ancla' => 'não se observaram diferenças de peso entre os três rios.',
		'nuevo' => ' Segundo a tese de doutorado de Giese (Universidad Nacional del Comahue, «Estructuración geográfica en poblaciones de trucha marrón migratoria en ríos de la Patagonia Austral»), nos adultos amostrados para o estudo o comprimento médio foi de 66,5 cm e o peso médio de 3,8 kg no rio Gallegos; de 62,8 cm e 4,0 kg no Grande; e de 57,5 cm e 3,2 kg no Irigoyen.',
	],
];

$mal = 0;
foreach ( $t as $i => $x ) {
	$p = pm_buscar( 'pm_especie', 'trucha', $i );
	if ( ! $p ) {
		WP_CLI::warning( "trucha ($i): no existe" );
		$mal++;
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, trim( $x['nuevo'] ) ) ) {
		WP_CLI::log( "· trucha ($i) #{$p->ID}: ya estaba" );
		continue;
	}
	if ( 1 !== substr_count( $c, $x['ancla'] ) ) {
		WP_CLI::warning( "trucha ($i): no encontré (una vez) la frase «{$x['ancla']}»; no toco nada" );
		$mal++;
		continue;
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": trucha ($i) #{$p->ID}: «{$x['ancla']}» + «" . trim( $x['nuevo'] ) . '»' );
	if ( ! $aplicar ) {
		continue;
	}
	$nuevo = str_replace( $x['ancla'], $x['ancla'] . $x['nuevo'], $c );
	pm_sc_guardar_t( $p->ID, $nuevo, trim( $x['nuevo'] ) );
	update_field( 'actualizado', $hoy, $p->ID );
	update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "VERIFIED: trucha ($i) #{$p->ID} " . get_permalink( $p->ID ) );
}

function pm_sc_guardar_t( int $id, string $nuevo, string $debe ): void {
	$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $nuevo ) ], true );
	if ( is_wp_error( $r ) ) {
		WP_CLI::error( 'trucha: ' . $r->get_error_message() );
	}
	if ( false === strpos( get_post( $id )->post_content, $debe ) ) {
		WP_CLI::error( 'trucha: la lectura de vuelta no contiene la frase nueva.' );
	}
}
$mal && WP_CLI::error( "$mal avisos: revisar." );
WP_CLI::success( $aplicar ? 'Aplicado.' : 'Simulacro terminado.' );
