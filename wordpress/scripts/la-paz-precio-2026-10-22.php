<?php
/**
 * 22/10/2026 (owner): add the 2026 entry fee of the Fiesta Provincial del Dorado Entrerriano
 * to the La Paz guides even though the edition has passed: a dated reference for readers
 * and language models, and the baseline for the next edition. From the official poster:
 * up to 31/08 $550.000 per modality (up to 3 instalments); after that date $600.000
 * ($800.000 for both modalities). Inserts one sentence in the festival paragraph and one
 * in the FAQ answer, es and pt. Idempotent (skips a guide that already has "$550.000").
 * Run after la-paz-fiesta-2026-10-22. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = la-paz-precio-2026-10-22).
 */

$aplicar = false;

$lang = [
	'es' => [
		'ancla'    => '(el 26). La Paz tiene además',
		'parrafo'  => '(el 26). La inscripción de 2026 costó $550.000 por modalidad si se pagaba hasta el 31/08 (en hasta 3 cuotas) y $600.000 después de esa fecha ($800.000 por las dos modalidades), según el afiche oficial. La Paz tiene además',
		'ancla_f'  => 'según el afiche oficial. La fecha de la próxima',
		'faq'      => 'según el afiche oficial. Inscripción 2026: $550.000 por modalidad hasta el 31/08 (hasta en 3 cuotas) y $600.000 después ($800.000 por las dos modalidades). La fecha de la próxima',
	],
	'pt' => [
		'ancla'    => '(dia 26). La Paz tem também',
		'parrafo'  => '(dia 26). A inscrição de 2026 custou $550.000 por modalidade se paga até 31/08 (em até 3 parcelas) e $600.000 depois dessa data ($800.000 pelas duas modalidades), em pesos argentinos, segundo o cartaz oficial. La Paz tem também',
		'ancla_f'  => 'segundo o cartaz oficial. A data da próxima',
		'faq'      => 'segundo o cartaz oficial. Inscrição em 2026: $550.000 por modalidade até 31/08 (em até 3 parcelas) e $600.000 depois ($800.000 pelas duas modalidades), em pesos argentinos. A data da próxima',
	],
];

$tocadas = 0;
foreach ( pm_posts( 'pm_destino' ) as $p ) {
	if ( 'la-paz' !== pm_clave_de( $p->ID ) ) {
		continue;
	}
	$i = pm_idioma_de( $p->ID );
	if ( ! isset( $lang[ $i ] ) ) {
		continue;
	}
	$t = $lang[ $i ];
	$c = (string) $p->post_content;
	if ( false !== strpos( $c, '$550.000' ) ) {
		WP_CLI::log( "la-paz ($i): ya tiene el precio, no toco nada." );
		continue;
	}
	if ( false === strpos( $c, $t['ancla'] ) ) {
		WP_CLI::warning( "la-paz ($i): no encuentro el párrafo de la fiesta (¿corrió la-paz-fiesta?); no toco nada." );
		continue;
	}
	$nuevo = str_replace( $t['ancla'], $t['parrafo'], $c );
	$faqs  = pm_filas( 'faqs', $p->ID );
	$hay   = false;
	foreach ( $faqs as $k => $f ) {
		if ( false !== strpos( (string) ( $f['respuesta'] ?? '' ), $t['ancla_f'] ) ) {
			$faqs[ $k ]['respuesta'] = str_replace( $t['ancla_f'], $t['faq'], $f['respuesta'] );
			$hay = true;
		}
	}
	WP_CLI::log( sprintf( 'la-paz (%s) #%d: párrafo sí, pregunta %s', $i, $p->ID, $hay ? 'sí' : 'NO' ) );
	$tocadas++;
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ] );
	if ( $hay ) {
		update_field( 'faqs', $faqs, $p->ID );
	}
	$leido = get_post( $p->ID )->post_content;
	$rows  = pm_filas( 'faqs', $p->ID );
	$en_faq = count( array_filter( $rows, fn( $f ) => false !== strpos( (string) ( $f['respuesta'] ?? '' ), '$550.000' ) ) );
	WP_CLI::log( '  VERIFIED: precio en el texto ' . ( false !== strpos( $leido, '$550.000' ) ? 'sí' : 'NO' ) . "; en $en_faq respuesta(s); preguntas " . count( $rows ) . ' (antes ' . count( $faqs ) . ')' );
}
WP_CLI::log( "$tocadas guía(s) a actualizar. " . ( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' ) );
