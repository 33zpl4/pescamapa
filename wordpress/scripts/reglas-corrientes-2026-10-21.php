<?php
/**
 * 21/10/2026: adds the "sizes and quotas" FAQ (Disposición 853/22, Boletín
 * Oficial de Corrientes, 26/12/2022, transcribed from the official Anexo I by
 * the owner) to the Goya and Esquina guides, in the languages each one has.
 * Adds one row if that question is not there yet; never edits a row.
 * Run with wp-script.yml (script = reglas-corrientes-2026-10-21).
 */

$faq = [
	'es' => [
		'pregunta'  => '¿Qué tallas y cupos rigen en Corrientes?',
		'respuesta' => 'Los de la Disposición 853/22 de Recursos Naturales (Boletín Oficial, 26/12/2022, vigente hasta que otro acto la modifique): dorado y surubí, solo pesca y devolución; pacú, 45 cm y cupo de 1 por licencia y un solo ejemplar por embarcación; patí, 70 cm y cupo de 2; boga, 45 cm y cupo de 2 por pescador (hasta 6 por embarcación); tararira, 30 cm y cupo de 5. El anexo incluye también otras especies, como armado, mandure, palometa o sábalo. Lo que se retiene solo puede movilizarse dentro de la localidad donde se pescó: está prohibido llevarlo a otro destino, nacional o internacional.',
	],
	'pt' => [
		'pregunta'  => 'Que tamanhos e limites valem em Corrientes?',
		'respuesta' => 'Os da Disposição 853/22 de Recursos Naturais (Diário Oficial, 26/12/2022, vigente até que outro ato a modifique): dourado e surubim, somente pesca e devolução; pacu, 45 cm e limite de 1 por licença e um único exemplar por embarcação; patí, 70 cm e limite de 2; boga, 45 cm e limite de 2 por pescador (até 6 por embarcação); traíra, 30 cm e limite de 5. O anexo inclui também outras espécies, como armado, mandure, palometa ou sábalo. O que for retido só pode circular dentro da localidade onde foi pescado: é proibido levá-lo para outro destino, nacional ou internacional.',
	],
	'en' => [
		'pregunta'  => 'What size and catch limits apply in Corrientes?',
		'respuesta' => 'Those of Disposition 853/22 of Recursos Naturales (Official Gazette, 26/12/2022, in force until another act changes it): dorado and surubí, catch and release only; pacú, 45 cm minimum and a limit of 1 per licence and a single fish per boat; patí, 70 cm and a limit of 2; boga, 45 cm and a limit of 2 per angler (up to 6 per boat); tararira, 30 cm and a limit of 5. The annex also covers other species, such as armado, mandure, palometa or sábalo. Fish you keep may only be moved within the town where they were caught: taking them to any other destination, national or international, is prohibited.',
	],
];

foreach ( [ 'goya' => [ 'es', 'pt', 'en' ], 'esquina' => [ 'es' ] ] as $clave => $idiomas ) {
	foreach ( $idiomas as $i ) {
		$p = pm_buscar( 'pm_destino', $clave, $i );
		if ( ! $p ) {
			WP_CLI::log( "· $clave ($i) no existe" );
			continue;
		}
		$filas = pm_filas( 'faqs', $p->ID );
		if ( array_filter( $filas, fn( $f ) => ( $f['pregunta'] ?? '' ) === $faq[ $i ]['pregunta'] ) ) {
			WP_CLI::log( "· $clave ($i): la pregunta ya estaba" );
			continue;
		}
		$filas[] = $faq[ $i ];
		update_field( 'faqs', $filas, $p->ID );
		update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
		WP_CLI::log( "✓ $clave ($i): agregada la pregunta de tallas y cupos" );
	}
}
