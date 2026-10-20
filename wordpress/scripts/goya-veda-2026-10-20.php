<?php
/**
 * Goya, 20/10/2026 (owner's review): the "km 1109–1150" closure does not
 * apply to Goya. The 2026 extraordinary closures were upstream in the Alto
 * Paraná (km 1232–1238 from May; km 1180–1239 from September, Paso de la
 * Patria): they belong in those guides, not here. Replaces that sentence in
 * the regulation summary (es/pt/en) with the general rule. Idempotent; warns
 * if "1109" shows up anywhere else in the guide.
 * Run with wp-script.yml (script = goya-veda-2026-10-20).
 */

$cambios = [
	'es' => [
		'La provincia puede disponer vedas temporales: en septiembre de 2026 dispuso una veda extraordinaria entre los km 1109 y 1150 del Paraná, aguas arriba de Goya (cerca del km 978). Verificá siempre el tramo que vas a pescar.',
		'La provincia dispone vedas por desove y, cuando hace falta, vedas extraordinarias por tramo del Paraná (las de 2026 fueron aguas arriba, en el Alto Paraná, lejos de Goya). Verificá siempre las vigentes para el tramo que vas a pescar.',
	],
	'pt' => [
		'A província pode decretar defesos temporários: em setembro de 2026 decretou um defeso extraordinário entre os km 1109 e 1150 do Paraná, rio acima de Goya (perto do km 978). Confira sempre o trecho onde vai pescar.',
		'A província decreta defesos de desova e, quando necessário, defesos extraordinários por trecho do Paraná (os de 2026 foram rio acima, no Alto Paraná, longe de Goya). Confira sempre os vigentes para o trecho onde vai pescar.',
	],
	'en' => [
		'The province can order temporary closures: in September 2026 it closed the Paraná between km 1109 and 1150, upstream of Goya (around km 978). Always check the stretch you plan to fish.',
		'The province sets spawning closures and, when needed, extraordinary closures on stretches of the Paraná (the 2026 ones were upstream, in the Alto Paraná, far from Goya). Always check the current ones for the stretch you plan to fish.',
	],
];

foreach ( $cambios as $i => [ $viejo, $nuevo ] ) {
	$p = pm_buscar( 'pm_destino', 'goya', $i );
	if ( ! $p ) {
		WP_CLI::warning( "goya ($i) no existe" );
		continue;
	}
	$resumen = (string) get_post_meta( $p->ID, 'reglamento_resumen', true );
	if ( str_contains( $resumen, $viejo ) ) {
		update_field( 'reglamento_resumen', str_replace( $viejo, $nuevo, $resumen ), $p->ID );
		update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
		WP_CLI::log( "✓ goya ($i): veda corregida" );
	} elseif ( str_contains( $resumen, $nuevo ) ) {
		WP_CLI::log( "· goya ($i): ya estaba corregida" );
	} else {
		WP_CLI::warning( "goya ($i): el resumen cambió en wp-admin; no lo toco" );
	}
	$todo = $p->post_content . wp_json_encode( get_post_meta( $p->ID ) );
	if ( str_contains( str_replace( $viejo, '', $todo ), '1109' ) ) {
		WP_CLI::warning( "goya ($i): \"1109\" aparece en otro lugar de la guía; revisalo a mano" );
	}
}
