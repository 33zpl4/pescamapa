<?php
/**
 * Second batch, 1/11/2026 ("publish more"): seven day guides of Chascomús, La Paz and Bariloche (Limay) listed from public
 * profiles (docs/prestadores.md rule 4, Uber mode). Written in our words, no platform named on the card, no contact; the
 * source URL and the read date live in the internal notes. None of the seven publishes a rate ("consultá disponibilidad"),
 * so no price is set. Source read with investigar.yml on 1/11/2026 (folder hooked-3-localidades-20261101-0716).
 * Goya, Junín, San Martín de los Andes and Santa Cruz had no guides listed there: nothing to add for them from this source.
 *
 * Idempotent by title. Dry run first ($aplicar = false); run with wp-script.yml (script = prestadores-tanda2-2026-11-01).
 */

$aplicar = false;
$hoy     = '20261101';
$nota    = fn( string $url ) => "Fuente interna: perfil público leído el 1/11/2026 en $url\nListado con información pública. Pescamapa no confirmó contacto, disponibilidad ni habilitación. Sin tarifa publicada en ese perfil (\"consultá disponibilidad\"). Revisar a los 90 días.";

$prestadores = [
	// Chascomús
	[
		'nombre' => 'Bordacharpapita', 'destino' => 'chascomus', 'loc' => 'Chascomús, Buenos Aires',
		'fuente' => 'https://hooked.com.ar/provinces/buenos-aires/chascomus',
		'mod' => [ 'carnada' ], 'esp' => [ 'pejerrey' ], 'cap' => 4,
		'lancha' => 'Tracker 6,20 m', 'lancha_en' => 'Tracker, 6.2 m', 'lancha_pt' => 'Tracker 6,20 m',
		'es' => 'Guía de pejerrey en la laguna de Chascomús y en Salada Grande (General Madariaga), Buenos Aires. Lancha Tracker de 6,20 m para hasta cuatro pescadores.',
		'en' => 'Pejerrey (silverside) guide on Chascomús lagoon and Salada Grande (General Madariaga), Buenos Aires. A 6.2 m Tracker boat for up to four anglers.',
		'pt' => 'Guia de peixe-rei na lagoa de Chascomús e em Salada Grande (General Madariaga), Buenos Aires. Barco Tracker de 6,20 m para até quatro pescadores.',
	],
	[
		'nombre' => 'José Vargas', 'destino' => 'chascomus', 'loc' => 'General Madariaga, Buenos Aires',
		'fuente' => 'https://hooked.com.ar/provinces/buenos-aires/chascomus',
		'mod' => [ 'carnada' ], 'esp' => [ 'pejerrey' ], 'cap' => 0,
		'es' => 'Guía de salidas de pejerrey en las lagunas de General Madariaga, Buenos Aires, con salida desde el Club de Pesca y Náutica local.',
		'en' => 'Guide for pejerrey (silverside) trips on the lagoons of General Madariaga, Buenos Aires, departing from the local fishing and boating club.',
		'pt' => 'Guia de saídas de peixe-rei nas lagoas de General Madariaga, Buenos Aires, saindo do clube de pesca e náutica local.',
	],
	[
		'nombre' => 'Luis Décima', 'destino' => 'chascomus', 'loc' => 'Chascomús, Buenos Aires',
		'fuente' => 'https://hooked.com.ar/provinces/buenos-aires/chascomus',
		'mod' => [ 'carnada', 'spinning' ], 'esp' => [ 'pejerrey', 'tararira' ], 'cap' => 0, 'incluye' => [ 'equipos', 'carnada' ],
		'es' => 'Guía de salidas diarias de pejerrey y tararira en la zona de lagunas de Chascomús, Buenos Aires, con equipos y carnada viva incluidos.',
		'en' => 'Daily guided trips for pejerrey (silverside) and tararira on the Chascomús lagoons, Buenos Aires, with tackle and live bait included.',
		'pt' => 'Saídas diárias guiadas de peixe-rei e traíra na região das lagoas de Chascomús, Buenos Aires, com equipamento e isca viva incluídos.',
	],
	// La Paz
	[
		'nombre' => 'Juan Pesca Guiada', 'destino' => 'la-paz', 'loc' => 'La Paz, Entre Ríos',
		'fuente' => 'https://hooked.com.ar/provinces/entre-rios/la-paz',
		'mod' => [ 'carnada', 'spinning', 'mosca' ], 'esp' => [ 'dorado', 'boga' ], 'cap' => 0,
		'es' => 'Guía de salidas de pesca de sol a sol en La Paz, Entre Ríos: dorado y boga con carnada, baitcasting y mosca.',
		'en' => 'Guide for dawn-to-dusk fishing trips in La Paz, Entre Ríos: dorado and boga with bait, baitcasting and fly.',
		'pt' => 'Guia de saídas de pesca do amanhecer ao anoitecer em La Paz, Entre Ríos: dourado e boga com isca natural, baitcasting e mosca.',
	],
	// Bariloche / Limay
	[
		'nombre' => 'Lucas Pesca', 'destino' => 'bariloche', 'loc' => 'Bariloche, Río Negro',
		'fuente' => 'https://hooked.com.ar/provinces/rio-negro/bariloche',
		'mod' => [ 'mosca', 'spinning' ], 'esp' => [ 'trucha' ], 'cap' => 2,
		'es' => 'Guía de jornadas de pesca de truchas en el río Limay, Bariloche, con mosca y spinning: salidas de ocho horas para dos pescadores. Trucha arcoíris, marrón y de arroyo.',
		'en' => 'Guide for trout days on the Limay River, Bariloche, with fly and spinning: eight-hour trips for two anglers. Rainbow, brown and brook trout.',
		'pt' => 'Guia de dias de pesca de truta no rio Limay, Bariloche, com mosca e spinning: saídas de oito horas para dois pescadores. Truta arco-íris, marrom e de riacho.',
	],
	[
		'nombre' => 'Expediciones Baruzzi (Andrés Makek)', 'destino' => 'bariloche', 'loc' => 'Bariloche, Río Negro',
		'fuente' => 'https://hooked.com.ar/provinces/rio-negro/rio-limay',
		'mod' => [ 'mosca' ], 'esp' => [ 'trucha' ], 'cap' => 4,
		'lancha' => 'Balsas y botes con plataforma', 'lancha_en' => 'Rafts and boats with casting platforms', 'lancha_pt' => 'Balsas e botes com plataforma',
		'es' => 'Guía de pesca de truchas en el río Limay y el lago Nahuel Huapi, Bariloche, con balsas y botes con plataforma para hasta cuatro pescadores. Trucha marrón, arcoíris y de arroyo.',
		'en' => 'Trout guide on the Limay River and Nahuel Huapi lake, Bariloche, with rafts and boats with casting platforms for up to four anglers. Brown, rainbow and brook trout.',
		'pt' => 'Guia de pesca de truta no rio Limay e no lago Nahuel Huapi, Bariloche, com balsas e botes com plataforma para até quatro pescadores. Truta marrom, arco-íris e de riacho.',
	],
	[
		'nombre' => 'Eze Faccio', 'destino' => 'bariloche', 'loc' => 'Bariloche, Río Negro',
		'fuente' => 'https://hooked.com.ar/provinces/rio-negro/rio-limay',
		'mod' => [ 'mosca' ], 'esp' => [ 'trucha' ], 'cap' => 0, 'incluye' => [ 'comida', 'bebidas' ],
		'lancha' => 'Drift boat en el Limay, balsa en el Manso, tracker en los lagos', 'lancha_en' => 'Drift boat on the Limay, raft on the Manso, tracker on the lakes', 'lancha_pt' => 'Drift boat no Limay, balsa no Manso, tracker nos lagos',
		'es' => 'Guía de pesca de truchas en Bariloche: flotada del río Limay en drift boat, río Manso en balsa y lagos Roca y Fonck en tracker. Almuerzo y bebidas incluidos. Trucha marrón, arcoíris y de arroyo.',
		'en' => 'Trout guide in Bariloche: Limay River float by drift boat, Manso River by raft and lakes Roca and Fonck by tracker boat. Lunch and drinks included. Brown, rainbow and brook trout.',
		'pt' => 'Guia de pesca de truta em Bariloche: flutuada no rio Limay em drift boat, rio Manso em balsa e lagos Roca e Fonck em tracker. Almoço e bebidas incluídos. Truta marrom, arco-íris e de riacho.',
	],
];

$especies_ok = array_keys( pm_ui()['etiquetas'] );
$creados     = [];
foreach ( $prestadores as $p ) {
	pm_buscar( 'pm_destino', $p['destino'], 'es' ) || WP_CLI::error( "No encontré la guía {$p['destino']} (es)." );
	$ya = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $p['nombre'], 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( $ya ) {
		WP_CLI::log( "· {$p['nombre']} ya existe (#{$ya[0]}): no se toca" );
		continue;
	}
	$esp = array_values( array_intersect( $p['esp'], $especies_ok ) );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$p['nombre']} → ficha pública sin verificar en {$p['destino']}; especies " . implode( ',', $esp ) . '; sin precio publicado' );
	if ( ! $aplicar ) {
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $p['nombre'] ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	$f = [
		'tipo' => [ 'guia' ], 'prest_destino' => $p['destino'], 'localidad' => $p['loc'],
		'descripcion' => $p['es'], 'descripcion_en' => $p['en'], 'descripcion_pt' => $p['pt'],
		'prest_modalidades' => $p['mod'], 'prest_especies' => $esp, 'equipo' => [ 'lancha' ], 'capacidad' => $p['cap'],
		'lancha' => $p['lancha'] ?? '', 'lancha_en' => $p['lancha_en'] ?? '', 'lancha_pt' => $p['lancha_pt'] ?? '', 'motor' => '',
		'prest_idiomas' => [ 'es' ], 'incluye' => $p['incluye'] ?? [], 'precio' => '', 'precio_fecha' => '',
		'prest_publico' => 1, 'prest_fuente' => '', 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'web' => '', 'notas' => $nota( $p['fuente'] ),
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	$creados[ $p['nombre'] ] = [ $pid, $p['destino'] ];
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok = true;
foreach ( $creados as $nombre => [ $pid, $destino ] ) {
	$r = null;
	foreach ( pm_prestadores_destino( $destino ) as $x ) {
		if ( (int) $x['id'] === (int) $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && '' === $r['precio'] && '' === $r['fuente'];
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$pid " . ( $r ? "en $destino, público, sin acuerdo, sin precio, sin fuente visible" : "no aparece en la lista de $destino" ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna ficha no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO: ' . count( $creados ) . ' fichas' );
