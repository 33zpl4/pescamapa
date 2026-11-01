<?php
/**
 * Litoral, 1/11/2026: seven day-trip guides of Esquina and Paso de la Patria as "listado con información pública" (level 1,
 * docs/prestadores.md), the first content of P10 (docs/ideas-contenido-seo-2026-10-31.md) and point 3 of the plan against Hooked
 * (docs/competencia-hooked-2026-11-01.md §5). Owner ruling of 1/11/2026 ("modo Uber"): a public profile is enough to list a guide,
 * attributed and dated, without contact data; anyone who asks to be removed is removed at once.
 *
 * Source of every fact: the guide's public profile on hooked.com.ar (province page of Corrientes, read with investigar.yml on
 * 1/11/2026, folder hooked-2-20261101-0522) and, for Del Paso Fishing, its public profile on fishingbooker.com (web search of the
 * same day). Written in our words. NO PRICES: what those platforms show is the platform's price, not the guide's own channel; it stays
 * as internal reference (notes) until the guide publishes it himself or confirms it with us.
 *
 * Spanish base texts + English and Portuguese versions. Idempotent by title. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = prestadores-litoral-dia-2026-11-01).
 */

$aplicar = false;
$hoy     = '20261101';

$nota = fn( string $fuente, string $precio_ref ) => "Listado con información pública (perfil público en $fuente, leído el 1/11/2026 por investigar.yml / búsqueda web). Pescamapa no confirmó contacto, disponibilidad ni habilitación. Precio de referencia INTERNO visto en ese perfil (no publicar): $precio_ref. Falta contactarlo y cruzarlo con el registro de guías de Corrientes.";

$prestadores = [
	[
		'nombre'  => 'Rebo Pesca',
		'destino' => 'esquina',
		'loc'     => 'Esquina, Corrientes',
		'fuente'  => 'https://hooked.com.ar/provinces/corrientes',
		'mod'     => [ 'carnada', 'spinning', 'trolling', 'mosca' ],
		'esp'     => [ 'dorado', 'surubi', 'pacu' ],
		'cap'     => 0,
		'es'      => 'Guía profesional de pesca en Esquina, Corrientes. Según su perfil público (leído el 1/11/2026): salidas personalizadas para pescadores de todos los niveles, con carnada, baitcasting, trolling y pesca con mosca; dorado, surubí pintado y atigrado y pacú. Se lo conoce por haber guiado una de las capturas de dorado más grandes registradas en el país (30 kg, devuelto al agua).',
		'en'      => 'Professional fishing guide in Esquina, Corrientes. According to his public profile (read on 1 November 2026): tailor-made trips for anglers of every level, with bait, baitcasting, trolling and fly fishing; dorado, spotted and striped surubí and pacú. Known for guiding one of the largest dorado catches recorded in the country (30 kg, released).',
		'pt'      => 'Guia profissional de pesca em Esquina, Corrientes. Segundo seu perfil público (lido em 1/11/2026): saídas personalizadas para pescadores de todos os níveis, com isca natural, baitcasting, trolling e pesca com mosca; dourado, surubi pintado e cachara e pacu. Conhecido por ter guiado uma das maiores capturas de dourado registradas no país (30 kg, devolvido à água).',
		'ref'     => 'desde ARS 480.000 por lancha completa (Hooked, 1/11/2026)',
	],
	[
		'nombre'  => 'Beto Aguilar',
		'destino' => 'esquina',
		'loc'     => 'Esquina, Corrientes',
		'fuente'  => 'https://hooked.com.ar/provinces/corrientes',
		'mod'     => [],
		'esp'     => [ 'dorado', 'pacu', 'surubi' ],
		'cap'     => 4,
		'es'      => 'Guía de pesca en Esquina, Corrientes. Según su perfil público (leído el 1/11/2026): salidas diarias de jornada completa o media jornada en lancha para hasta cuatro pescadores, buscando dorado, pacú y surubí.',
		'en'      => 'Fishing guide in Esquina, Corrientes. According to his public profile (read on 1 November 2026): daily full-day or half-day trips by boat for up to four anglers, after dorado, pacú and surubí.',
		'pt'      => 'Guia de pesca em Esquina, Corrientes. Segundo seu perfil público (lido em 1/11/2026): saídas diárias de dia inteiro ou meio dia em barco para até quatro pescadores, atrás de dourado, pacu e surubi.',
		'ref'     => 'sin tarifa publicada (Hooked, "consultá disponibilidad", 1/11/2026)',
	],
	[
		'nombre'  => 'César Martín Pesca',
		'destino' => 'esquina',
		'loc'     => 'Esquina, Corrientes',
		'fuente'  => 'https://hooked.com.ar/provinces/corrientes',
		'mod'     => [],
		'esp'     => [ 'dorado', 'surubi', 'pacu', 'boga' ],
		'cap'     => 4,
		'lancha'  => 'Don Pino III, 6,2 m, 115 HP',
		'lancha_en' => 'Don Pino III, 6.2 m, 115 HP',
		'lancha_pt' => 'Don Pino III, 6,2 m, 115 HP',
		'es'      => 'Guía de pesca en Esquina, Corrientes, a bordo de la lancha Don Pino III. Según su perfil público (leído el 1/11/2026): embarcación de 6,2 m y 115 HP para hasta cuatro pescadores; dorado, surubí atigrado y pintado, pacú y boga.',
		'en'      => 'Fishing guide in Esquina, Corrientes, aboard the boat Don Pino III. According to his public profile (read on 1 November 2026): a 6.2 m, 115 HP boat for up to four anglers; dorado, striped and spotted surubí, pacú and boga.',
		'pt'      => 'Guia de pesca em Esquina, Corrientes, a bordo do barco Don Pino III. Segundo seu perfil público (lido em 1/11/2026): embarcação de 6,2 m e 115 HP para até quatro pescadores; dourado, surubi cachara e pintado, pacu e boga.',
		'ref'     => 'sin tarifa publicada (Hooked, "consultá disponibilidad", 1/11/2026)',
	],
	[
		'nombre'  => 'Del Paso Fishing',
		'destino' => 'paso-de-la-patria',
		'loc'     => 'Paso de la Patria, Corrientes',
		'fuente'  => 'https://fishingbooker.com/charters/view/34091',
		'tipo'    => [ 'guia', 'operador' ],
		'mod'     => [ 'carnada', 'spinning', 'trolling', 'mosca' ],
		'esp'     => [ 'dorado', 'surubi', 'pacu', 'boga', 'pati' ],
		'cap'     => 3,
		'incluye' => [ 'equipos', 'carnada', 'traslados' ],
		'es'      => 'Operador de pesca en Paso de la Patria, Corrientes, con el capitán Ezequiel Argerich. Según sus perfiles públicos (leídos el 1/11/2026): salidas en el Paraná con carnada viva, pesca de fondo, trolling y mosca; dorado, surubí, pacú, boga y patí; lancha para hasta tres pescadores; equipo, carnada, señuelos, bebidas y traslado desde el hotel o el muelle en Paso de la Patria; también paquetes con alojamiento y lancha Tracker para grupos de hasta nueve.',
		'en'      => 'Fishing operator in Paso de la Patria, Corrientes, with captain Ezequiel Argerich. According to its public profiles (read on 1 November 2026): trips on the Paraná with live bait, bottom fishing, trolling and fly; dorado, surubí, pacú, boga and patí; boat for up to three anglers; tackle, bait, lures, drinks and transfers from your hotel or the dock in Paso de la Patria; also packages with lodging and a Tracker boat for groups of up to nine.',
		'pt'      => 'Operador de pesca em Paso de la Patria, Corrientes, com o capitão Ezequiel Argerich. Segundo seus perfis públicos (lidos em 1/11/2026): saídas no Paraná com isca viva, pesca de fundo, trolling e mosca; dourado, surubi, pacu, boga e patí; barco para até três pescadores; equipamento, isca, iscas artificiais, bebidas e traslado do hotel ou do píer em Paso de la Patria; também pacotes com hospedagem e barco Tracker para grupos de até nove.',
		'ref'     => 'desde US$ 925 hasta 3 personas (FishingBooker, 1/11/2026; precio de plataforma)',
	],
	[
		'nombre'  => 'El Negro Cano',
		'destino' => 'paso-de-la-patria',
		'loc'     => 'Paso de la Patria, Corrientes',
		'fuente'  => 'https://hooked.com.ar/provinces/corrientes',
		'mod'     => [],
		'esp'     => [ 'dorado', 'surubi', 'pacu', 'boga' ],
		'cap'     => 4,
		'lancha'  => 'Electra 560, motor 4 tiempos',
		'lancha_en' => 'Electra 560, four-stroke engine',
		'lancha_pt' => 'Electra 560, motor 4 tempos',
		'es'      => 'Guía de pesca en Paso de la Patria e Isla del Cerrito, Corrientes. Según su perfil público (leído el 1/11/2026): lancha Electra 560 con motor de cuatro tiempos para hasta cuatro pescadores; dorado, surubí atigrado y pintado, pacú y boga.',
		'en'      => 'Fishing guide in Paso de la Patria and Isla del Cerrito, Corrientes. According to his public profile (read on 1 November 2026): an Electra 560 boat with a four-stroke engine for up to four anglers; dorado, striped and spotted surubí, pacú and boga.',
		'pt'      => 'Guia de pesca em Paso de la Patria e Isla del Cerrito, Corrientes. Segundo seu perfil público (lido em 1/11/2026): barco Electra 560 com motor de quatro tempos para até quatro pescadores; dourado, surubi cachara e pintado, pacu e boga.',
		'ref'     => 'sin tarifa publicada (Hooked, "consultá disponibilidad", 1/11/2026)',
	],
	[
		'nombre'  => 'José Titi',
		'destino' => 'paso-de-la-patria',
		'loc'     => 'Paso de la Patria, Corrientes',
		'fuente'  => 'https://hooked.com.ar/provinces/corrientes',
		'tipo'    => [ 'guia', 'operador' ],
		'mod'     => [],
		'esp'     => [ 'dorado', 'surubi', 'pacu', 'pati' ],
		'cap'     => 0,
		'es'      => 'Guía de pesca en Paso de la Patria, Corrientes. Según su perfil público (leído el 1/11/2026): salidas de jornada completa con opción de alojamiento; dorado, surubí atigrado, pacú y patí.',
		'en'      => 'Fishing guide in Paso de la Patria, Corrientes. According to his public profile (read on 1 November 2026): full-day trips with an optional lodging package; dorado, striped surubí, pacú and patí.',
		'pt'      => 'Guia de pesca em Paso de la Patria, Corrientes. Segundo seu perfil público (lido em 1/11/2026): saídas de dia inteiro com opção de hospedagem; dourado, surubi cachara, pacu e patí.',
		'ref'     => 'sin tarifa publicada (Hooked, "consultá disponibilidad", 1/11/2026)',
	],
	[
		'nombre'  => 'Juan Pablo Calderaro',
		'destino' => 'paso-de-la-patria',
		'loc'     => 'Paso de la Patria, Corrientes',
		'fuente'  => 'https://hooked.com.ar/provinces/corrientes',
		'mod'     => [],
		'esp'     => [ 'dorado', 'surubi', 'pacu' ],
		'cap'     => 0,
		'es'      => 'Guía de pesca en Paso de la Patria, Corrientes. Según su perfil público (leído el 1/11/2026): salidas de jornada completa o medio día; dorado, surubí atigrado y pintado y pacú.',
		'en'      => 'Fishing guide in Paso de la Patria, Corrientes. According to his public profile (read on 1 November 2026): full-day or half-day trips; dorado, striped and spotted surubí and pacú.',
		'pt'      => 'Guia de pesca em Paso de la Patria, Corrientes. Segundo seu perfil público (lido em 1/11/2026): saídas de dia inteiro ou meio dia; dourado, surubi cachara e pintado e pacu.',
		'ref'     => 'sin tarifa publicada (Hooked, "consultá disponibilidad", 1/11/2026)',
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
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$p['nombre']} → ficha pública sin verificar en {$p['destino']}; especies " . implode( ',', $esp ) . ( count( $esp ) < count( $p['esp'] ) ? ' (sin etiqueta: ' . implode( ',', array_diff( $p['esp'], $esp ) ) . ')' : '' ) . '; sin precio publicado' );
	if ( ! $aplicar ) {
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $p['nombre'] ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	$f = [
		'tipo' => $p['tipo'] ?? [ 'guia' ], 'prest_destino' => $p['destino'], 'localidad' => $p['loc'],
		'descripcion' => $p['es'], 'descripcion_en' => $p['en'], 'descripcion_pt' => $p['pt'],
		'prest_modalidades' => $p['mod'], 'prest_especies' => $esp, 'equipo' => [ 'lancha' ], 'capacidad' => $p['cap'],
		'lancha' => $p['lancha'] ?? 'Lancha', 'lancha_en' => $p['lancha_en'] ?? 'Boat', 'lancha_pt' => $p['lancha_pt'] ?? 'Barco', 'motor' => '',
		'prest_idiomas' => [ 'es' ], 'incluye' => $p['incluye'] ?? [], 'precio' => '', 'precio_fecha' => '',
		'prest_publico' => 1, 'prest_fuente' => $p['fuente'], 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'web' => '', 'notas' => $nota( wp_parse_url( $p['fuente'], PHP_URL_HOST ), $p['ref'] ),
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

// Read-back: public, on its destination, no price shown (none is dated), the internal note never reaches the card.
$ok = true;
foreach ( $creados as $nombre => [ $pid, $destino ] ) {
	$r = null;
	foreach ( pm_prestadores_destino( $destino ) as $x ) {
		if ( (int) $x['id'] === (int) $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && '' === $r['precio'] && ! isset( $r['notas'] );
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$pid " . ( $r ? "en $destino, público, sin acuerdo, sin precio" : "no aparece en la lista de $destino" ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna ficha no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO: ' . count( $creados ) . ' fichas' );
