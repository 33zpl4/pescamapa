<?php
/**
 * pt/en versions of the free texts of the eight public provider cards (the English guide page showed Spanish
 * text inside English labels). Writes only the <campo>_pt / <campo>_en fields, never the Spanish ones, and only
 * when the Spanish text is still the one translated here (an editor's change is reported and skipped).
 * Dry run first ($aplicar = false); run with wp-script.yml. Idempotent.
 */

$aplicar = true;

$es_base = [
	'agus-guia-de-pesca-en-goya' => [
		'descripcion' => 'Apart hotel en Goya (Av. Millán Medina 7410), con vistas al río según su ficha de Booking, donde figura entre los mejor valorados (octubre de 2026). Ofrece excursiones de pesca. Departamentos con cocina equipada, aire acondicionado, baño privado, terraza o balcón y jardín; piscina al aire libre (de temporada), estacionamiento privado y wifi gratis, desayuno disponible; admite mascotas bajo petición. Atienden en español, inglés y portugués.',
		'lancha' => 'ASTILLERO LA PAZ - ALP Guaycurú 630 ',
		'motor' => 'Potencia, velocidad y bajo  consumo de combustible para jornadas de pesca extensas',
		'prest_temporada' => 'Todo el año',
		'precio' => 'Desde $ 300.000 por salida de pesca + $150.000 alojamiento',
	],
	'experiencia-isoro' => [
		'descripcion' => 'Pesca deportiva con devolución y paseos náuticos por la Reserva Natural Isoró y el río Paraná, con base en Goya (la boca del Isoró queda 4 km aguas abajo del puerto). En la reserva, dorado con flycast, baitcast y spinning; en el Paraná, pesca con carnada anclada o a garete, también de noche. Media jornada o jornada completa (11 a 12 horas de pesca). Packs de un día de pesca, o de 1 a 7 noches de alojamiento con días de pesca. Paseos náuticos de hasta 5 personas para observar aves y fotografiar, de media jornada o de 8 a 10 horas.',
		'lancha' => 'Astillero La Paz (ALP), modelo Guaycurú 630, con plataformas de casteo en proa y popa',
		'precio' => 'Desde $ 300.000 por salida',
	],
	'fansfishing' => [ 'descripcion' => 'Salidas de pesca guiadas y blog sobre la pesca de la zona.' ],
	'tata-lodge' => [ 'descripcion' => 'Lodge de pesca.' ],
	'posada-del-paso' => [ 'descripcion' => 'Posada con servicio de pesca embarcada propio.', 'lancha' => 'Lancha de 6,40 m', 'motor' => 'Motor de 90 hp' ],
	'del-paso-fishing' => [ 'descripcion' => 'Guía de pesca con lancha; ofrece también paquetes con alojamiento.' ],
	'pousada-la-pesca' => [ 'descripcion' => 'Pousada brasileña con paquetes de pesca y traslado en Paso de la Patria (en portugués).' ],
	'victor-flores-pesca' => [ 'descripcion' => 'Guía de pesca con excursiones embarcadas en La Paz.' ],
];

$trad = [
	'agus-guia-de-pesca-en-goya' => [
		'en' => [
			'descripcion' => 'Apart hotel in Goya (Av. Millán Medina 7410), with river views according to its Booking listing, where it ranks among the best rated (October 2026). Offers fishing excursions. Apartments with a fully equipped kitchen, air conditioning, private bathroom, terrace or balcony and garden; outdoor pool (seasonal), private parking and free wifi, breakfast available; pets allowed on request. Staff speak Spanish, English and Portuguese.',
			'lancha' => 'ASTILLERO LA PAZ - ALP Guaycurú 630',
			'motor' => 'Power, speed and low fuel consumption for long fishing days',
			'prest_temporada' => 'All year',
			'precio' => 'From ARS 300,000 per fishing outing + ARS 150,000 lodging',
		],
		'pt' => [
			'descripcion' => 'Apart-hotel em Goya (Av. Millán Medina 7410), com vista para o rio segundo sua página no Booking, onde aparece entre os mais bem avaliados (outubro de 2026). Oferece excursões de pesca. Apartamentos com cozinha equipada, ar-condicionado, banheiro privativo, terraço ou varanda e jardim; piscina ao ar livre (sazonal), estacionamento privativo e wifi grátis, café da manhã disponível; aceita animais sob consulta. Atendem em espanhol, inglês e português.',
			'lancha' => 'ASTILLERO LA PAZ - ALP Guaycurú 630',
			'motor' => 'Potência, velocidade e baixo consumo de combustível para longas jornadas de pesca',
			'prest_temporada' => 'O ano todo',
			'precio' => 'A partir de ARS 300.000 por saída de pesca + ARS 150.000 hospedagem',
		],
	],
	'experiencia-isoro' => [
		'en' => [
			'descripcion' => 'Catch-and-release sport fishing and boat tours of the Isoró Nature Reserve and the Paraná River, based in Goya (the mouth of the Isoró is 4 km downstream from the port). In the reserve, dorado on flycast, baitcast and spinning; on the Paraná, bait fishing, anchored or drifting, including at night. Half day or full day (11 to 12 hours of fishing). One-day fishing packs, or 1 to 7 nights of lodging with fishing days. Boat tours for up to 5 people for birdwatching and photography, half day or 8 to 10 hours.',
			'lancha' => 'Astillero La Paz (ALP) boat, Guaycurú 630 model, with casting platforms at bow and stern',
			'precio' => 'From ARS 300,000 per outing',
		],
		'pt' => [
			'descripcion' => 'Pesca esportiva com devolução e passeios náuticos pela Reserva Natural Isoró e pelo rio Paraná, com base em Goya (a foz do Isoró fica 4 km rio abaixo do porto). Na reserva, dourado com flycast, baitcast e spinning; no Paraná, pesca com isca, ancorada ou à deriva, também à noite. Meio período ou dia inteiro (11 a 12 horas de pesca). Pacotes de um dia de pesca, ou de 1 a 7 noites de hospedagem com dias de pesca. Passeios náuticos de até 5 pessoas para observar aves e fotografar, de meio período ou de 8 a 10 horas.',
			'lancha' => 'Barco do Astillero La Paz (ALP), modelo Guaycurú 630, com plataformas de arremesso na proa e na popa',
			'precio' => 'A partir de ARS 300.000 por saída',
		],
	],
	'fansfishing' => [ 'en' => [ 'descripcion' => 'Guided fishing trips and a blog about fishing in the area.' ], 'pt' => [ 'descripcion' => 'Saídas de pesca guiadas e blog sobre a pesca da região.' ] ],
	'tata-lodge' => [ 'en' => [ 'descripcion' => 'Fishing lodge.' ], 'pt' => [ 'descripcion' => 'Lodge de pesca.' ] ],
	'posada-del-paso' => [
		'en' => [ 'descripcion' => 'Inn with its own boat-fishing service.', 'lancha' => '6.40 m boat', 'motor' => '90 hp motor' ],
		'pt' => [ 'descripcion' => 'Pousada com serviço próprio de pesca embarcada.', 'lancha' => 'Lancha de 6,40 m', 'motor' => 'Motor de 90 hp' ],
	],
	'del-paso-fishing' => [ 'en' => [ 'descripcion' => 'Fishing guide with a boat; also offers packages with lodging.' ], 'pt' => [ 'descripcion' => 'Guia de pesca com lancha; oferece também pacotes com hospedagem.' ] ],
	'pousada-la-pesca' => [ 'en' => [ 'descripcion' => 'Brazilian pousada with fishing and transfer packages in Paso de la Patria (in Portuguese).' ], 'pt' => [ 'descripcion' => 'Pousada brasileira com pacotes de pesca e traslado em Paso de la Patria (em português).' ] ],
	'victor-flores-pesca' => [ 'en' => [ 'descripcion' => 'Fishing guide with boat excursions in La Paz.' ], 'pt' => [ 'descripcion' => 'Guia de pesca com excursões embarcadas em La Paz.' ] ],
];

foreach ( $trad as $slug => $por_idioma ) {
	$p = get_posts( [ 'post_type' => 'pm_prestador', 'name' => $slug, 'post_status' => 'any', 'numberposts' => 1 ] )[0] ?? null;
	if ( ! $p ) {
		WP_CLI::warning( "$slug: no existe la ficha, se omite." );
		continue;
	}
	foreach ( $es_base[ $slug ] as $campo => $esperado ) {
		$actual = (string) get_field( $campo, $p->ID );
		if ( $actual !== $esperado ) {
			WP_CLI::warning( "$slug: el texto en español de '$campo' cambió desde que se tradujo; no se toca (revisar a mano)." );
			foreach ( $por_idioma as $i => $campos ) {
				unset( $trad[ $slug ][ $i ][ $campo ] );
				$por_idioma[ $i ] = $trad[ $slug ][ $i ];
			}
		}
	}
	foreach ( $por_idioma as $i => $campos ) {
		foreach ( $campos as $campo => $valor ) {
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": #{$p->ID} $slug {$campo}_{$i} = " . mb_substr( $valor, 0, 70 ) . ( mb_strlen( $valor ) > 70 ? '…' : '' ) );
			if ( ! $aplicar ) {
				continue;
			}
			update_field( "{$campo}_{$i}", $valor, $p->ID );
			$ok = (string) get_field( "{$campo}_{$i}", $p->ID ) === $valor;
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #{$p->ID} {$campo}_{$i}" );
			$ok || WP_CLI::error( "$slug {$campo}_{$i}: la lectura de vuelta no coincide." );
		}
	}
}
WP_CLI::success( $aplicar ? 'Traducciones guardadas.' : 'Simulacro terminado.' );
