<?php
/**
 * Esquina (es/en/pt), 28/10/2026: the "Paquetes de pesca en Esquina" page, same model as Goya.
 *
 * Content: the ways of fishing the municipality itself lists for Esquina (live bait, spinning, baitcasting, trolling,
 * fly cast, drifting "al garete") and the package formats the market offers (a day trip with lunch on the island,
 * multi-day packs with lodging, basic and premium all-inclusive). Owner's rule (28/10/2026): a competitor's
 * offer can be offered by us; competitors are neither listed nor named, and their prices are only an internal
 * benchmark (docs/referencias-esquina-mercado.md), never published.
 *
 * Published WITHOUT price, without "incluye" and without capacity: each card says it is quoted for the group and
 * dates / that what is included is confirmed in the quote. Prices ("desde" with amount, currency, unit and DATE)
 * are loaded in wp-admin (Esquina guide → tab "Paquetes") when a guide confirms them. Also loads, in Portuguese, the
 * same two "Antes de viajar" blocks (documents and minors) as Goya.
 *
 * Idempotent: a language that already has packages / blocks is not touched. Run with wp-script.yml
 * (script = paquetes-esquina-2026-10-28).
 */

$aplicar = true;
$destino = 'esquina';
$hoy     = '20261028';

$lineas = fn( array $l ) => implode( "\n", $l );

$paquetes = [
	'es' => [
		[ 'titulo' => 'Pesca con carnada viva en Esquina: jornada completa', 'resumen' => 'Salida de un día en lancha con guía, pescando con carnada viva (como la morena) en el río Corriente y el Paraná, con almuerzo en la isla.', 'modalidad' => 'Carnada viva', 'duracion' => 'Jornada completa' ],
		[ 'titulo' => 'Pesca con señuelos en Esquina: spinning y baitcasting', 'resumen' => 'Pesca activa con señuelos artificiales: lanzar y recoger, buscando los lugares con el guía.', 'modalidad' => 'Spinning o baitcasting, con señuelos artificiales', 'duracion' => 'Media jornada o jornada completa' ],
		[ 'titulo' => 'Pesca con mosca (fly cast) en Esquina', 'resumen' => 'Salida pensada para quien pesca con mosca. Confirmamos con el guía el equipo disponible según tu experiencia.', 'modalidad' => 'Fly cast (mosca)', 'duracion' => 'Media jornada o jornada completa' ],
		[ 'titulo' => 'Pesca al garete y trolling en Esquina', 'resumen' => 'Dos formas de pescar desde la lancha en movimiento: a la deriva con la corriente (al garete) o arrastrando el señuelo (trolling).', 'modalidad' => 'Al garete o trolling' ],
		[ 'titulo' => 'Todo incluido en Esquina: versión básica y premium', 'resumen' => 'Un día de pesca con almuerzo en la isla, cena y alojamiento esa noche. Armamos una versión básica y otra premium con más cantidades y bebidas, según lo que busques.', 'duracion' => 'Un día de pesca y una noche', 'variantes' => [ 'Básica: jornada completa, almuerzo en la isla, merienda, cena y alojamiento esa noche, con combustible y carnada en cantidad acotada', 'Premium: lo de la básica, con combustible y carnada sin límite y bebidas con y sin alcohol' ] ],
		[ 'titulo' => 'Packs de varios días en Esquina: alojamiento y pesca', 'resumen' => 'Combinamos varios días de pesca con noches de alojamiento en Esquina, para grupos de 2 a 4 pescadores por lancha.', 'variantes' => [ '2 días de pesca y 3 noches', '3 días de pesca y 4 noches', '4 días de pesca y 5 noches' ] ],
	],
	'en' => [
		[ 'titulo' => 'Live-bait fishing in Esquina: full day', 'resumen' => 'A day trip by boat with a guide, fishing with live bait (such as morena) on the Corriente and Paraná rivers, with lunch on the island.', 'modalidad' => 'Live bait', 'duracion' => 'Full day' ],
		[ 'titulo' => 'Lure fishing in Esquina: spinning and baitcasting', 'resumen' => 'Active fishing with artificial lures: cast and retrieve, finding the spots with the guide.', 'modalidad' => 'Spinning or baitcasting, with artificial lures', 'duracion' => 'Half day or full day' ],
		[ 'titulo' => 'Fly fishing (fly cast) in Esquina', 'resumen' => 'A trip for anglers who fish with a fly rod. We confirm with the guide which tackle is available for your level.', 'modalidad' => 'Fly cast', 'duracion' => 'Half day or full day' ],
		[ 'titulo' => 'Drift fishing and trolling in Esquina', 'resumen' => 'Two ways of fishing from a moving boat: drifting with the current or trolling a lure behind the boat.', 'modalidad' => 'Drifting or trolling' ],
		[ 'titulo' => 'All-inclusive in Esquina: basic and premium', 'resumen' => 'A day of fishing with lunch on the island, dinner and a night of lodging. We put together a basic and a premium version with larger amounts and drinks, according to what you are after.', 'duracion' => 'One fishing day and one night', 'variantes' => [ 'Basic: full day, lunch on the island, afternoon snack, dinner and that night\'s lodging, with a limited amount of fuel and bait', 'Premium: everything in basic, with unlimited fuel and bait and drinks with and without alcohol' ] ],
		[ 'titulo' => 'Multi-day packs in Esquina: lodging and fishing', 'resumen' => 'We combine several fishing days with nights of lodging in Esquina, for groups of 2 to 4 anglers per boat.', 'variantes' => [ '2 fishing days and 3 nights', '3 fishing days and 4 nights', '4 fishing days and 5 nights' ] ],
	],
	'pt' => [
		[ 'titulo' => 'Pescaria com isca viva em Esquina: dia completo', 'resumen' => 'Saída de um dia de barco com guia, pescando com isca viva (como a morena) no rio Corriente e no Paraná, com almoço na ilha.', 'modalidad' => 'Isca viva', 'duracion' => 'Dia completo' ],
		[ 'titulo' => 'Pescaria com iscas artificiais em Esquina: spinning e baitcasting', 'resumen' => 'Pesca ativa com iscas artificiais: lançar e recolher, procurando os pontos com o guia.', 'modalidad' => 'Spinning ou baitcasting, com iscas artificiais', 'duracion' => 'Meio dia ou dia completo' ],
		[ 'titulo' => 'Pescaria com mosca (fly cast) em Esquina', 'resumen' => 'Saída pensada para quem pesca com mosca. Confirmamos com o guia o equipamento disponível conforme a sua experiência.', 'modalidad' => 'Fly cast (mosca)', 'duracion' => 'Meio dia ou dia completo' ],
		[ 'titulo' => 'Pesca à deriva e trolling em Esquina', 'resumen' => 'Duas formas de pescar do barco em movimento: à deriva com a correnteza ou arrastando a isca (trolling).', 'modalidad' => 'À deriva ou trolling' ],
		[ 'titulo' => 'Tudo incluído em Esquina: versão básica e premium', 'resumen' => 'Um dia de pesca com almoço na ilha, jantar e uma noite de hospedagem. Montamos uma versão básica e outra premium, com mais quantidades e bebidas, conforme o que você procura.', 'duracion' => 'Um dia de pesca e uma noite', 'variantes' => [ 'Básica: dia completo, almoço na ilha, lanche, jantar e hospedagem naquela noite, com combustível e isca em quantidade limitada', 'Premium: o da básica, com combustível e isca sem limite e bebidas com e sem álcool' ] ],
		[ 'titulo' => 'Pacotes de vários dias em Esquina: hospedagem e pesca', 'resumen' => 'Combinamos vários dias de pesca com noites de hospedagem em Esquina, para grupos de 2 a 4 pescadores por barco.', 'variantes' => [ '2 dias de pesca e 3 noites', '3 dias de pesca e 4 noites', '4 dias de pesca e 5 noites' ] ],
	],
];

$info = [
	'pt' => [
		[
			'titulo'        => 'Documentos para entrar na Argentina (brasileiros)',
			'texto'         => implode( "\n", [
				'Para entrar como turista, o brasileiro precisa de um RG físico em bom estado de conservação ou de um passaporte válido.',
				'Versões digitais de qualquer documento (incluindo o RG digital) não são aceitas pela Migraciones, e a CNH não vale como documento de entrada.',
				'Muitos guias brasileiros recomendam um RG emitido há menos de 10 anos, com foto atual e sem rasuras. É uma recomendação prática, não um requisito que conste na fonte oficial; se o seu RG é antigo, leve o passaporte.',
			] ),
			'fuente_nombre' => 'Argentina.travel: documentação necessária para turistas brasileiros',
			'fuente_url'    => 'https://argentina.travel/pt/novidades/requisitos-para-entrar-na-argentina-documentos-necessarios',
			'consultada'    => $hoy,
		],
		[
			'titulo'        => 'Menores de idade',
			'texto'         => implode( "\n", [
				'Menores de 13 anos que não viajam com quem exerce a responsabilidade parental precisam de uma autorização para entrar na Argentina, além do documento.',
				'Confira as regras e os documentos exigidos no site oficial antes de comprar a viagem.',
			] ),
			'fuente_nombre' => 'Argentina.gob.ar: ingressar no país com um menor de idade',
			'fuente_url'    => 'https://www.argentina.gob.ar/servicio/ingresar-al-pais-con-un-menor-de-edad',
			'consultada'    => $hoy,
		],
	],
];

foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma, no se carga." );
		continue;
	}
	if ( pm_filas( 'paquetes', $p->ID ) ) {
		WP_CLI::log( "$destino ($i) #{$p->ID}: ya tiene paquetes, no los toco." );
	} else {
		$filas = array_map( fn( $x ) => [
			'publicado' => 1, 'titulo' => $x['titulo'], 'resumen' => $x['resumen'] ?? '', 'modalidad' => $x['modalidad'] ?? '', 'duracion' => $x['duracion'] ?? '',
			'capacidad' => '', 'incluye' => '', 'no_incluye' => '', 'variantes' => $lineas( $x['variantes'] ?? [] ),
			'desde_monto' => '', 'desde_moneda' => '', 'desde_unidad' => '', 'desde_fecha' => '', 'interno' => '',
		], $paquetes[ $i ] );
		WP_CLI::log( "$destino ($i) #{$p->ID}: " . ( $aplicar ? 'se cargan' : 'se cargarían' ) . ' ' . count( $filas ) . ' paquetes (sin precio) → ' . pm_url_paquetes( $destino, $i ) );
		if ( $aplicar ) {
			update_field( 'paquetes', $filas, $p->ID );
			$leidos = pm_paquetes( $p->ID );
			WP_CLI::log( '  VERIFIED: ' . count( $leidos ) . ' paquetes publicados leídos de vuelta, ' . count( array_filter( array_column( $leidos, 'precio' ) ) ) . ' con precio' );
		}
	}
	if ( isset( $info[ $i ] ) ) {
		if ( pm_filas( 'paquetes_info', $p->ID ) ) {
			WP_CLI::log( "$destino ($i) #{$p->ID}: ya tiene bloques 'Antes de viajar', no los toco." );
		} elseif ( $aplicar ) {
			update_field( 'paquetes_info', $info[ $i ], $p->ID );
			$leidos = pm_paquetes_info( $p->ID );
			WP_CLI::log( '  VERIFIED: ' . count( $leidos ) . ' bloques "Antes de viajar" leídos de vuelta, ' . count( array_filter( array_column( $leidos, 'url' ) ) ) . ' con fuente' );
		}
	}
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
