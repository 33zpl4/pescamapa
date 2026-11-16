<?php
/**
 * 16/11/2026 (pedido del dueño): two copy fixes on notes already published.
 * (1) Hub «cómo preparar un viaje» (es/en/pt): the visa sentence attributed to two commercial pages ("US, Canada and EU citizens need no visa") is replaced by wording that points to the official page of the
 *     Dirección Nacional de Migraciones (https://www.argentina.gob.ar/migraciones/turistas, read 16/11/2026 with investigar.yml: it defines the tourist stay, up to three months, extendable, and the documents, but does NOT list which countries need a visa),
 *     plus "confirm entry requirements with the Argentine consulate of your country". No nationality claim is made.
 * (2) Visitor-facing «operadores» / «operators» in the permit-step-by-step and trip-cost notes (es/en/pt) becomes «lugares y guías» / «places and guides» / «lugares e guias» where it is a simple phrase (singular «operador» and
 *     the few non-simple phrases are left alone and listed in the log).
 * Text fields (bajada, seo_description) get the same replacements. Idempotent: a phrase already replaced is skipped. Dry run first ($aplicar = false). Run with wp-script.yml (script = copy-visa-operadores-2026-11-16).
 */
// --- Helpers (copied from the other 2026-11-16 scripts: wp-script.yml sends one file) ---
if ( ! function_exists( 'pm_sc_guardar' ) ) {
	/** Save post content and fail loudly when the read-back differs. */
	function pm_sc_guardar( int $id, string $nuevo, array $debe_contener, string $etiqueta ): void {
		$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $nuevo ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( $etiqueta . ': ' . $r->get_error_message() );
		}
		$leido = get_post( $id )->post_content;
		foreach ( $debe_contener as $t ) {
			if ( false === strpos( $leido, $t ) ) {
				WP_CLI::error( "$etiqueta: la lectura de vuelta no contiene «{$t}»." );
			}
		}
	}
	function pm_sc_campo( int $id, string $campo, string $valor, string $etiqueta ): void {
		update_field( $campo, $valor, $id );
		if ( pm_campo( $campo, $id ) !== $valor ) {
			WP_CLI::error( "$etiqueta: el campo $campo no quedó como se esperaba." );
		}
	}
	function pm_sc_modo( bool $aplicar ): string {
		return $aplicar ? 'APLICA' : 'SIMULACRO';
	}
}

$aplicar = true;

$url_dnm = 'https://www.argentina.gob.ar/migraciones/turistas';

// ---- (1) Visa sentence of the hub: [ old text as published, new text ]. ----
$visa = [
	'es' => [
		[ ', y Nervous Waters y Set Fly Fishing dicen que los ciudadanos de EE. UU., Canadá y la Unión Europea no necesitan visa para turismo. No es una fuente oficial: confirmalo con el consulado argentino de tu país. El permiso',
		  '. Sobre la visa: la Dirección Nacional de Migraciones define al turista como el extranjero que entra por descanso o esparcimiento, con una permanencia autorizada de hasta tres meses, prorrogable (<a href="' . $url_dnm . '" rel="noopener">página oficial, leída el 16/11/2026</a>), pero esa página no lista qué países necesitan visa. Confirmá los requisitos de entrada con el consulado argentino de tu país. El permiso' ],
		[ ' Según Nervous Waters y Set Fly Fishing, los ciudadanos de EE. UU., Canadá y la Unión Europea no necesitan visa para turismo; no es una fuente oficial, confirmalo con el consulado argentino de tu país.',
		  ' La visa depende de tu nacionalidad: la página oficial de la Dirección Nacional de Migraciones define la estadía de turista (hasta tres meses, prorrogable) pero no lista qué países necesitan visa, así que confirmá los requisitos de entrada con el consulado argentino de tu país.' ],
	],
	'en' => [
		[ ', and Nervous Waters and Set Fly Fishing say citizens of the US, Canada and the European Union do not need a visa for tourism. It is not an official source: confirm with the Argentine consulate in your country. The fishing permit',
		  '. About the visa: the Dirección Nacional de Migraciones (Argentina\'s immigration office) defines a tourist as a foreigner entering for rest or leisure, with an authorized stay of up to three months, extendable (<a href="' . $url_dnm . '" rel="noopener">official page, read on 16/11/2026</a>), but that page does not list which countries need a visa. Confirm the entry requirements with the Argentine consulate in your country. The fishing permit' ],
		[ ' According to Nervous Waters and Set Fly Fishing, citizens of the US, Canada and the European Union do not need a visa for tourism; it is not an official source, so confirm with the Argentine consulate in your country.',
		  ' Whether you need a visa depends on your nationality: the official page of the Dirección Nacional de Migraciones defines the tourist stay (up to three months, extendable) but does not list which countries need a visa, so confirm the entry requirements with the Argentine consulate in your country.' ],
	],
	'pt' => [
		[ ', e a Nervous Waters e a Set Fly Fishing dizem que cidadãos dos EUA, do Canadá e da União Europeia não precisam de visto para turismo. Não é fonte oficial: confirme com o consulado argentino do seu país. A licença',
		  '. Sobre o visto: a Dirección Nacional de Migraciones define o turista como o estrangeiro que entra por descanso ou lazer, com permanência autorizada de até três meses, prorrogável (<a href="' . $url_dnm . '" rel="noopener">página oficial, lida em 16/11/2026</a>), mas essa página não lista quais países precisam de visto. Confirme os requisitos de entrada com o consulado argentino do seu país. A licença' ],
		[ ' Segundo a Nervous Waters e a Set Fly Fishing, cidadãos dos EUA, do Canadá e da União Europeia não precisam de visto para turismo; não é fonte oficial, confirme com o consulado argentino do seu país.',
		  ' Precisar de visto depende da sua nacionalidade: a página oficial da Dirección Nacional de Migraciones define a estadia de turista (até três meses, prorrogável), mas não lista quais países precisam de visto; confirme os requisitos de entrada com o consulado argentino do seu país.' ],
	],
];

// ---- (2) «operadores» → «lugares y guías» in simple phrases: [ old, new ] (str_replace, every occurrence). ----
$ops = [
	'permiso-pesca-argentina-paso-a-paso' => [
		'es' => [ [ 'opciones de los operadores que encajan', 'opciones de los lugares y guías que encajan' ] ],
		'en' => [ [ 'options from the operators that fit', 'options from the places and guides that fit' ] ],
		'pt' => [ [ 'opções dos operadores que se encaixam', 'opções dos lugares e guias que se encaixam' ] ],
	],
	'cuanto-cuesta-viaje-pesca-argentina' => [
		'es' => [
			[ 'las cifras que publican los operadores', 'las cifras que publican los lugares y guías' ],
			[ 'precios que publican los operadores', 'precios que publican los lugares y guías' ],
			[ 'páginas que publican los operadores', 'páginas que publican los lugares y guías' ],
			[ 'No es igual en todos los operadores', 'No es igual en todos los lugares y guías' ],
			[ 'consultamos a los operadores que encajan', 'consultamos con los lugares y guías que encajan' ],
			[ 'Algunos operadores incluyen', 'Algunos lugares y guías incluyen' ],
			[ 'de operadores distintos', 'de lugares y guías distintos' ],
			[ 'con estos operadores', 'con estos lugares y guías' ],
			[ 'opciones de los operadores que encajan', 'opciones de los lugares y guías que encajan' ],
		],
		'en' => [
			[ 'figures operators publish', 'figures places and guides publish' ],
			[ 'prices operators publish', 'prices places and guides publish' ],
			[ 'we ask the operators that fit', 'we ask the places and guides that fit' ],
			[ 'A few operators include', 'A few places and guides include' ],
			[ 'with these operators', 'with these places and guides' ],
			[ 'pages operators publish', 'pages places and guides publish' ],
			[ 'from different operators', 'from different places and guides' ],
			[ 'options of the operators that fit', 'options of the places and guides that fit' ],
		],
		'pt' => [
			[ 'que os operadores publicam', 'que os lugares e guias publicam' ],
			[ 'em todos os operadores', 'em todos os lugares e guias' ],
			[ 'consultamos os operadores que se encaixam', 'consultamos os lugares e guias que se encaixam' ],
			[ 'Alguns operadores incluem', 'Alguns lugares e guias incluem' ],
			[ 'de operadores diferentes', 'de lugares e guias diferentes' ],
			[ 'com esses operadores', 'com esses lugares e guias' ],
			[ 'opções dos operadores que se encaixam', 'opções dos lugares e guias que se encaixam' ],
		],
	],
];

$cambios = 0;

/** Apply [old,new] pairs to $texto; returns [ nuevo texto, aplicados, ya hechos ]. */
$aplicar_pares = function ( string $texto, array $pares ): array {
	$hechos = 0;
	$ya     = 0;
	foreach ( $pares as [ $viejo, $nuevo ] ) {
		if ( false !== strpos( $texto, $viejo ) ) {
			$texto = str_replace( $viejo, $nuevo, $texto );
			$hechos++;
		} elseif ( false !== strpos( $texto, $nuevo ) ) {
			$ya++;
		}
	}
	return [ $texto, $hechos, $ya ];
};

// ---- Hub: visa ----
foreach ( $visa as $i => $pares ) {
	$p = pm_buscar( 'pm_nota', 'como-preparar-viaje-pesca-argentina', $i );
	if ( ! $p ) {
		WP_CLI::error( "hub ($i): la nota no existe." );
	}
	[ $nuevo, $hechos, $ya ] = $aplicar_pares( $p->post_content, $pares );
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": hub ($i) #{$p->ID}: visa, $hechos frases reemplazadas, $ya ya estaban." );
	if ( $hechos + $ya !== count( $pares ) ) {
		WP_CLI::error( "hub ($i): no encontré la frase de visa tal como se publicó; no toco nada." );
	}
	if ( 0 === $hechos ) {
		continue;
	}
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, $nuevo, [ $url_dnm ], "hub ($i)" );
		$leido = get_post( $p->ID )->post_content;
		$ok    = ! preg_match( '/no necesitan visa|do not need a visa|n[ãa]o precisam de visto/u', $leido ) && count( pm_faqs_contenido( $leido ) ) > 0;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": hub ($i) #{$p->ID}: sin la frase atribuida a páginas comerciales, con el enlace oficial y FAQ legible" );
		$ok || WP_CLI::error( "hub ($i): la lectura de vuelta no es la esperada." );
	}
}

// ---- Permit and cost notes: operadores ----
foreach ( $ops as $clave => $por_idioma ) {
	foreach ( $por_idioma as $i => $pares ) {
		$p = pm_buscar( 'pm_nota', $clave, $i );
		if ( ! $p ) {
			WP_CLI::error( "$clave ($i): la nota no existe." );
		}
		[ $nuevo, $hechos, $ya ] = $aplicar_pares( $p->post_content, $pares );
		$campos_nuevos = [];
		foreach ( [ 'bajada', 'seo_description', 'seo_title' ] as $campo ) {
			$valor = (string) pm_campo( $campo, $p->ID );
			[ $v2, $h2 ] = $aplicar_pares( $valor, $pares );
			if ( $h2 ) {
				$campos_nuevos[ $campo ] = $v2;
			}
		}
		$resto = preg_match_all( '/operadores|operators/iu', wp_strip_all_tags( $nuevo ) );
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) #{$p->ID}: $hechos frases en el cuerpo, $ya ya estaban, campos a tocar: " . ( $campos_nuevos ? implode( ', ', array_keys( $campos_nuevos ) ) : 'ninguno' ) . "; «operadores/operators» que quedan en el cuerpo: $resto" );
		foreach ( [ '/(.{50})(operadores|operators)(.{40})/su' ] as $rx ) {
			if ( preg_match_all( $rx, wp_strip_all_tags( $nuevo ), $m ) ) {
				foreach ( $m[0] as $frag ) {
					WP_CLI::log( '    queda: …' . trim( $frag ) . '…' );
				}
			}
		}
		if ( ! $hechos && ! $campos_nuevos ) {
			continue;
		}
		$cambios++;
		if ( $aplicar ) {
			if ( $hechos ) {
				pm_sc_guardar( $p->ID, $nuevo, [], "$clave ($i)" );
			}
			foreach ( $campos_nuevos as $campo => $valor ) {
				pm_sc_campo( $p->ID, $campo, $valor, "$clave ($i)" );
			}
			$leido = get_post( $p->ID )->post_content;
			$mal   = 0;
			foreach ( $pares as [ $viejo ] ) {
				$mal += substr_count( $leido, $viejo );
			}
			WP_CLI::log( ( 0 === $mal ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #{$p->ID}: sin las frases viejas" );
			0 === $mal || WP_CLI::error( "$clave ($i): quedaron frases viejas." );
		}
	}
}

// ---- Information only: other published notes that still say «operadores» / «operators» (not touched) ----
$otros = [];
foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'numberposts' => -1 ] ) as $n ) {
	$k = pm_clave_de( $n->ID );
	if ( isset( $ops[ $k ] ) ) {
		continue;
	}
	$c = preg_match_all( '/operadores|operators/iu', wp_strip_all_tags( $n->post_content ) );
	if ( $c ) {
		$otros[] = $k . ' (' . pm_idioma_de( $n->ID ) . ') #' . $n->ID . ': ' . $c;
	}
}
WP_CLI::log( 'Otras notas con «operadores/operators» (no se tocan): ' . ( $otros ? implode( '; ', $otros ) : 'ninguna' ) );

WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
