<?php
/**
 * 22/10/2026: the provincial Turismo page "Trámites y permisos" lists a contact for
 * fishing licences (phone 3794 909039, ventadelicencias@gmail.com; owner, 22/10/2026).
 * Owner ruling: what can't be verified online isn't chased; the tip points to whom to ask.
 *
 * Appends one sentence with that contact to every FAQ answer, in every guide and
 * language, whose answer still says the licence is done "en el portal de Recursos
 * Naturales" (the licence FAQs). Idempotent: rows that already carry the phone are skipped.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = licencia-contacto-2026-10-22).
 */

$aplicar = false;

$frase = [
	'es' => ' Si no ves el precio o no podés tramitarla online, consultá a la venta de licencias de Turismo de Corrientes (3794 909039, ventadelicencias@gmail.com; datos de corrientes.tur.ar, consultados el 22/10/2026) o preguntale a tu guía.',
	'pt' => ' Se não encontrar o preço ou não conseguir tirá-la online, consulte a venda de licenças do Turismo de Corrientes (3794 909039, ventadelicencias@gmail.com; dados de corrientes.tur.ar, consultados em 22/10/2026) ou pergunte ao seu guia.',
	'en' => ' If you can\'t see the price or can\'t get it online, ask the Corrientes Tourism licence office (3794 909039, ventadelicencias@gmail.com; details from corrientes.tur.ar, checked 22/10/2026) or ask your guide.',
];
$marca = '3794 909039';
$busca = [ 'portal de Recursos Naturales', 'portal de Recursos Naturais', 'Recursos Naturales portal', 'Natural Resources portal', 'Recursos Naturales' ];

$tocadas = 0;
foreach ( pm_posts( 'pm_destino' ) as $p ) {
	$i     = pm_idioma_de( $p->ID );
	$filas = pm_filas( 'faqs', $p->ID );
	$cambio = false;
	foreach ( $filas as $k => $f ) {
		$r = (string) ( $f['respuesta'] ?? '' );
		if ( ! preg_match( '/licen[cs]/iu', (string) ( $f['pregunta'] ?? '' ) . $r ) || false !== strpos( $r, $marca ) ) {
			continue;
		}
		$menciona = false;
		foreach ( $busca as $b ) {
			if ( false !== stripos( $r, $b ) ) {
				$menciona = true;
				break;
			}
		}
		if ( ! $menciona || ! preg_match( '/portal|online|internet/iu', $r ) ) {
			continue;
		}
		WP_CLI::log( sprintf( '%s (%s) faq %d «%s»', $p->post_name, $i, $k, mb_substr( (string) $f['pregunta'], 0, 70 ) ) );
		$filas[ $k ]['respuesta'] = rtrim( $r ) . ( $frase[ $i ] ?? $frase['es'] );
		$cambio = true;
		$tocadas++;
	}
	if ( $cambio && $aplicar ) {
		$antes = count( $filas );
		update_field( 'faqs', $filas, $p->ID );
		$leidas = pm_filas( 'faqs', $p->ID );
		$n = count( array_filter( $leidas, fn( $f ) => false !== strpos( (string) ( $f['respuesta'] ?? '' ), $marca ) ) );
		WP_CLI::log( "  VERIFIED: filas $antes -> " . count( $leidas ) . "; respuestas con el contacto: $n" );
	}
}
WP_CLI::log( "$tocadas respuesta(s) a tocar. " . ( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' ) );
