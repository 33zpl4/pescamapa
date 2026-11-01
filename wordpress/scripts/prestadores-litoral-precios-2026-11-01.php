<?php
/**
 * Litoral cards, second pass (owner, 1/11/2026): publish the price a guide shows on a public platform, and stop naming the
 * platform on the card. Rationale (docs/prestadores.md, rule 4): the data is public and attributed internally; naming a
 * competitor's marketplace on our own card sends the reader there. The source URL and the read date stay in the internal
 * notes (never shown), and the card keeps "Información pública, todavía sin verificar" with the correction/removal link.
 *
 * Changes per card: descripcion (es/en/pt) without "según su perfil público (leído el…)", prest_fuente emptied (the
 * platform URL moves into notas), precio + precio_fecha where the platform shows one. Idempotent: only touches cards whose
 * description still contains the old phrase. Dry run first; run with wp-script.yml (script = prestadores-litoral-precios-2026-11-01).
 */

$aplicar = false;
$hoy     = '20261101';
$ref     = 'Fuente interna: perfil público leído el 1/11/2026 en ';

$cambios = [
	'Rebo Pesca' => [
		'precio'    => 'Desde $ 480.000 por lancha completa, jornada',
		'precio_en' => 'From ARS 480,000 per boat, full day',
		'precio_pt' => 'A partir de ARS 480.000 por barco, diária',
		'es' => 'Guía profesional de pesca en Esquina, Corrientes. Salidas personalizadas para pescadores de todos los niveles, con carnada, baitcasting, trolling y pesca con mosca; dorado, surubí pintado y atigrado y pacú. Se lo conoce por haber guiado una de las capturas de dorado más grandes registradas en el país (30 kg, devuelto al agua).',
		'en' => 'Professional fishing guide in Esquina, Corrientes. Tailor-made trips for anglers of every level, with bait, baitcasting, trolling and fly fishing; dorado, spotted and striped surubí and pacú. Known for guiding one of the largest dorado catches recorded in the country (30 kg, released).',
		'pt' => 'Guia profissional de pesca em Esquina, Corrientes. Saídas personalizadas para pescadores de todos os níveis, com isca natural, baitcasting, trolling e pesca com mosca; dourado, surubi pintado e cachara e pacu. Conhecido por ter guiado uma das maiores capturas de dourado registradas no país (30 kg, devolvido à água).',
	],
	'Beto Aguilar' => [
		'es' => 'Guía de pesca en Esquina, Corrientes. Salidas diarias de jornada completa o media jornada en lancha para hasta cuatro pescadores, buscando dorado, pacú y surubí.',
		'en' => 'Fishing guide in Esquina, Corrientes. Daily full-day or half-day trips by boat for up to four anglers, after dorado, pacú and surubí.',
		'pt' => 'Guia de pesca em Esquina, Corrientes. Saídas diárias de dia inteiro ou meio dia em barco para até quatro pescadores, atrás de dourado, pacu e surubi.',
	],
	'César Martín Pesca' => [
		'es' => 'Guía de pesca en Esquina, Corrientes, a bordo de la lancha Don Pino III: embarcación de 6,2 m y 115 HP para hasta cuatro pescadores. Dorado, surubí atigrado y pintado, pacú y boga.',
		'en' => 'Fishing guide in Esquina, Corrientes, aboard the boat Don Pino III: a 6.2 m, 115 HP boat for up to four anglers. Dorado, striped and spotted surubí, pacú and boga.',
		'pt' => 'Guia de pesca em Esquina, Corrientes, a bordo do barco Don Pino III: embarcação de 6,2 m e 115 HP para até quatro pescadores. Dourado, surubi cachara e pintado, pacu e boga.',
	],
	'El Negro Cano' => [
		'es' => 'Guía de pesca en Paso de la Patria e Isla del Cerrito, Corrientes. Lancha Electra 560 con motor de cuatro tiempos para hasta cuatro pescadores; dorado, surubí atigrado y pintado, pacú y boga.',
		'en' => 'Fishing guide in Paso de la Patria and Isla del Cerrito, Corrientes. An Electra 560 boat with a four-stroke engine for up to four anglers; dorado, striped and spotted surubí, pacú and boga.',
		'pt' => 'Guia de pesca em Paso de la Patria e Isla del Cerrito, Corrientes. Barco Electra 560 com motor de quatro tempos para até quatro pescadores; dourado, surubi cachara e pintado, pacu e boga.',
	],
	'José Titi' => [
		'es' => 'Guía de pesca en Paso de la Patria, Corrientes. Salidas de jornada completa con opción de alojamiento; dorado, surubí atigrado, pacú y patí.',
		'en' => 'Fishing guide in Paso de la Patria, Corrientes. Full-day trips with an optional lodging package; dorado, striped surubí, pacú and patí.',
		'pt' => 'Guia de pesca em Paso de la Patria, Corrientes. Saídas de dia inteiro com opção de hospedagem; dourado, surubi cachara, pacu e patí.',
	],
	'Juan Pablo Calderaro' => [
		'es' => 'Guía de pesca en Paso de la Patria, Corrientes. Salidas de jornada completa o medio día; dorado, surubí atigrado y pintado y pacú.',
		'en' => 'Fishing guide in Paso de la Patria, Corrientes. Full-day or half-day trips; dorado, striped and spotted surubí and pacú.',
		'pt' => 'Guia de pesca em Paso de la Patria, Corrientes. Saídas de dia inteiro ou meio dia; dourado, surubi cachara e pintado e pacu.',
	],
];

$tocadas = [];
foreach ( $cambios as $nombre => $c ) {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( ! $ids ) {
		WP_CLI::warning( "$nombre: no existe, se salta" );
		continue;
	}
	$id   = $ids[0];
	$desc = (string) pm_campo( 'descripcion', $id );
	if ( false === strpos( $desc, 'Según su perfil público' ) ) {
		WP_CLI::log( "· $nombre (#$id) ya está en la forma nueva: no se toca" );
		continue;
	}
	$fuente = (string) pm_campo( 'prest_fuente', $id );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $nombre (#$id) → descripción sin mención de plataforma; fuente «{$fuente}» pasa a notas; precio: " . ( $c['precio'] ?? 'sin publicar' ) );
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'descripcion', $c['es'], $id );
	update_field( 'descripcion_en', $c['en'], $id );
	update_field( 'descripcion_pt', $c['pt'], $id );
	update_field( 'notas', $ref . $fuente . "\n" . (string) pm_campo( 'notas', $id ), $id );
	update_field( 'prest_fuente', '', $id );
	update_field( 'verificado', $hoy, $id );
	if ( isset( $c['precio'] ) ) {
		update_field( 'precio', $c['precio'], $id );
		update_field( 'precio_en', $c['precio_en'], $id );
		update_field( 'precio_pt', $c['precio_pt'], $id );
		update_field( 'precio_fecha', $hoy, $id );
	}
	$tocadas[ $nombre ] = $id;
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok = true;
foreach ( $tocadas as $nombre => $id ) {
	$r    = pm_prestador( $id );
	$bien = false === strpos( $r['descripcion'], 'perfil público' ) && '' === $r['fuente'] && pm_prestador_publico( $r ) && ( ! isset( $cambios[ $nombre ]['precio'] ) || ( $r['precio'] === $cambios[ $nombre ]['precio'] && '2026-11-01' === $r['precio_fecha'] ) );
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$id precio «{$r['precio']}» ({$r['precio_fecha']}), sin fuente visible" );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna ficha no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'APLICADO: ' . count( $tocadas ) . ' fichas' );
