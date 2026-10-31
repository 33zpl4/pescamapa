<?php
/**
 * 31/10/2026: seven outfitter cards had their description in English in the base field, which is the Spanish one (the scripts of
 * 30/10 for Bariloche, Junín and Jurassic Lake wrote the English text there), so the Spanish and Portuguese pages showed English.
 * This moves the English text to descripcion_en and writes the Spanish description. A card is touched only if its base text
 * still starts with the English text those scripts wrote AND descripcion_en is empty: a hand-edited card is left alone.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = prestadores-idioma-2026-10-31).
 */

$aplicar = true;

$fichas = [
	'Rio Manso Lodge' => [
		'prefijo' => 'Lodge near Bariloche with guided fishing.',
		'es'      => 'Lodge cerca de Bariloche con pesca guiada. Su propio sitio publica para 2026-27 US$ 1.050 por pescador y noche o US$ 7.000 por semana, por persona en habitación doble (leído el 30/10/2026). Dice que trabaja en tierras privadas y que no está dentro de un parque nacional; sus páginas no coinciden sobre si la licencia de pesca está incluida: preguntá.',
	],
	'Limay River Lodge' => [
		'prefijo' => 'Lodge on the Limay River, built on riverfront land bought in 2013',
		'es'      => 'Lodge sobre el río Limay, construido en un terreno con frente al río comprado en 2013; primera temporada en 2015 (historia que cuenta el propio operador). Su página de pesca no cargó en nuestro lector, así que no hay nada más confirmado: ni tarifa ni capacidad.',
	],
	'Tipiliuke Lodge' => [
		'prefijo' => 'Lodge in the lower Chimehuín valley',
		'es'      => 'Lodge en el valle bajo del Chimehuín, a diez minutos del aeropuerto de Chapelco, con nueve habitaciones y pesca con mosca en el Chimehuín y el Quilquihue (descripción del operador). No publica tarifa: pide que escribas por su formulario de reservas.',
	],
	'Hostería San Huberto' => [
		'prefijo' => 'Lodge in the Junín de los Andes area.',
		'es'      => 'Lodge en la zona de Junín de los Andes. Su sitio (olsenfamily.com.ar) no respondió a nuestro lector el 30/10/2026, así que no hay nada más confirmado: ni río, ni tarifa, ni capacidad.',
	],
	'Jurassic Lake Lodge' => [
		'prefijo' => 'Lodge on the south-west shore of Lago Strobel',
		'es'      => 'Lodge en la costa sudoeste del lago Strobel, cerca de la boca del río Barrancoso (descripción del operador). Según su propio sitio: 10 habitaciones, pista de aterrizaje privada y vuelo chárter desde El Calafate los sábados.',
	],
	'Estancia Laguna Verde' => [
		'prefijo' => 'Estancia and fly-fishing lodge on the south coast of Lago Strobel',
		'es'      => 'Estancia y lodge de pesca con mosca en la costa sur del lago Strobel (descripción del operador). Se vende también a través de agencias de viajes.',
	],
	'Tres Amigos Outfitters' => [
		'prefijo' => 'Outfitter that sells a trip combining Jurassic Lake and Glacier King',
		'es'      => 'Operador que vende un viaje que combina Jurassic Lake y Glacier King (según su propia página).',
	],
];

$n = 0;
foreach ( $fichas as $nombre => $f ) {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( ! $ids ) {
		WP_CLI::warning( "$nombre: no existe" );
		continue;
	}
	$pid    = (int) $ids[0];
	$actual = (string) pm_campo( 'descripcion', $pid );
	$en     = (string) pm_campo( 'descripcion_en', $pid );
	if ( 0 !== strpos( $actual, $f['prefijo'] ) || '' !== trim( $en ) ) {
		WP_CLI::log( "· $nombre #$pid: no coincide (base editada o traducción ya cargada), no se toca" );
		continue;
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $nombre #$pid: inglés → descripcion_en; español nuevo (" . mb_strlen( $f['es'] ) . ' car.)' );
	++$n;
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'descripcion_en', $actual, $pid );
	update_field( 'descripcion', $f['es'], $pid );
	$ok = pm_campo( 'descripcion', $pid ) === $f['es'] && pm_campo( 'descripcion_en', $pid ) === $actual;
	WP_CLI::log( ( $ok ? '  VERIFIED' : '  FALLÓ' ) . ": $nombre" );
	$ok || WP_CLI::error( "$nombre: la lectura de vuelta no coincide." );
}
WP_CLI::log( ( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' ) . " ($n fichas)" );
