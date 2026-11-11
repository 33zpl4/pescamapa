<?php
/**
 * Bariloche (es/en/pt), 11/11/2026: Viator listings pasted by the owner.
 *   1. One new package: Guides Patagonia, full day on the Limay (EUR 599.69 per group of up to 2, Viator, 11/11/2026).
 *   2. Outfitters Patagonia package: adds the half-day variant (EUR 445.22 per group, Viator, 11/11/2026). Nothing else in that row changes.
 * Only facts (price, duration, inclusions, licence) with source and date; no reviews, no copied descriptions.
 * Not published: Trout Bariloche's Viator price (EUR 90.86 does not match the operator's own US$ 520 per boat; ask the operator),
 * and the resellers (Tangol): not the guide.
 * The EUR symbol in the price needs the plugin support that ships when main is merged; until then the amount is not shown in euros.
 *
 * Idempotent: a package with the same title and a variant line already present are not touched.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = paquetes-bariloche-viator-2026-11-11).
 */
$aplicar = false;
$destino = 'bariloche';

$T = [
	'es' => [
		'dia' => 'día completo de pesca con mosca en el Limay', 'mod' => 'Mosca, desde bote de deriva o vadeando', 'cap' => 'Hasta 2 pescadores por bote (salida privada)',
		'unidad' => 'por grupo de hasta 2, día completo de 10 horas', 'dur' => '10 horas',
		'prop' => 'Propinas',
		'ta'  => 'Salida privada de día completo desde Bariloche, con los datos que el operador publica en Viator (11 de noviembre de 2026); su propio sitio no se leyó. Pescamapa no confirmó disponibilidad ni condiciones con el operador. Si sos el operador y algo cambió, escribinos y lo corregimos o lo sacamos.',
		'incl' => [ 'Traslado desde el alojamiento', 'Bote de pesca', 'Equipo de pesca', 'Almuerzo completo', 'Bebidas (con alcohol)', 'Instrucción personalizada' ],
		'lic' => 'Si el operador gestiona la licencia: US$ 30 por persona (según Viator)',
		'vtit' => 'Outfitters Patagonia:',
		'var' => 'Medio día (5 a 6 horas): € 445,22 por grupo, según Viator (11/11/2026)',
		'licx' => 'Licencia de pesca (para un extranjero, $ 50.000 por día, unos US$ 32 con US$ 1 = $ 1.545)',
	],
	'en' => [
		'dia' => 'full-day fly fishing on the Limay', 'mod' => 'Fly fishing, from a drift boat or wading', 'cap' => 'Up to 2 anglers per boat (private trip)',
		'unidad' => 'per group of up to 2, full day of 10 hours', 'dur' => '10 hours',
		'prop' => 'Gratuities',
		'ta'  => 'Private full-day trip from Bariloche, using what the operator publishes on Viator (11 November 2026); its own website was not read. Pescamapa has not confirmed availability or terms with the operator. If you are the operator and something has changed, write to us and we will correct it or take it down.',
		'incl' => [ 'Pickup from your accommodation', 'Fishing boat', 'Fishing equipment', 'Full lunch', 'Drinks (including alcohol)', 'Personalised instruction' ],
		'lic' => 'If the operator arranges the licence: US$ 30 per person (according to Viator)',
		'vtit' => 'Outfitters Patagonia:',
		'var' => 'Half day (5 to 6 hours): € 445.22 per group, according to Viator (11/11/2026)',
		'licx' => 'Fishing licence (for a foreigner, $ 50,000 per day, about US$ 32 at US$ 1 = $ 1,545)',
	],
	'pt' => [
		'dia' => 'dia inteiro de pesca com mosca no Limay', 'mod' => 'Mosca, de bote de deriva ou caminhando na água', 'cap' => 'Até 2 pescadores por bote (saída privada)',
		'unidad' => 'por grupo de até 2, dia inteiro de 10 horas', 'dur' => '10 horas',
		'prop' => 'Gorjetas',
		'ta'  => 'Saída privada de dia inteiro a partir de Bariloche, com os dados que o operador publica no Viator (11 de novembro de 2026); o site dele não foi lido. A Pescamapa não confirmou disponibilidade nem condições com o operador. Se você é o operador e algo mudou, escreva para nós e corrigimos ou tiramos.',
		'incl' => [ 'Traslado desde a hospedagem', 'Bote de pesca', 'Equipamento de pesca', 'Almoço completo', 'Bebidas (com álcool)', 'Instrução personalizada' ],
		'lic' => 'Se o operador providencia a licença: US$ 30 por pessoa (segundo o Viator)',
		'vtit' => 'Outfitters Patagonia:',
		'var' => 'Meio dia (5 a 6 horas): € 445,22 por grupo, segundo o Viator (11/11/2026)',
		'licx' => 'Licença de pesca (para um estrangeiro, $ 50.000 por dia, cerca de US$ 32 com US$ 1 = $ 1.545)',
	],
];
$interno = 'No se publica. Listado con información pública (Viator, pegado por el dueño el 11/11/2026; docs/paquetes-banco-plataformas-2026-11-11.md). Sin acuerdo ni comisión: mensaje "confirmá o corregí tu ficha; si preferís, la sacamos". Comisión: la decide el dueño.';

$cambios = 0;
foreach ( $T as $i => $t ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$filas  = pm_filas( 'paquetes', $p->ID );
	$tit_ex = array_map( fn( $f ) => (string) ( $f['titulo'] ?? '' ), $filas );
	$nuevo  = 0;
	$var    = 0;

	$titulo = "Guides Patagonia: {$t['dia']}";
	if ( ! in_array( $titulo, $tit_ex, true ) ) {
		$filas[] = [
			'publicado' => 1, 'titulo' => $titulo, 'resumen' => $t['ta'], 'modalidad' => $t['mod'], 'duracion' => $t['dur'], 'capacidad' => $t['cap'],
			'incluye' => implode( "\n", $t['incl'] ), 'no_incluye' => implode( "\n", [ $t['licx'], $t['prop'], $t['lic'] ] ),
			'variantes' => '',
			'desde_monto' => 599.69, 'desde_moneda' => 'EUR', 'desde_unidad' => $t['unidad'], 'desde_fecha' => '2026-11-11', 'interno' => $interno,
		];
		$nuevo = 1;
	}

	foreach ( $filas as $k => $f ) {
		if ( 0 !== strpos( (string) ( $f['titulo'] ?? '' ), $t['vtit'] ) ) {
			continue;
		}
		$actual = (string) ( $f['variantes'] ?? '' );
		if ( false !== strpos( $actual, $t['var'] ) ) {
			continue;
		}
		$filas[ $k ]['variantes'] = '' === trim( $actual ) ? $t['var'] : rtrim( $actual ) . "\n" . $t['var'];
		$var = 1;
		break;
	}

	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $destino ($i) #{$p->ID}: $nuevo paquete nuevo, $var variante (hoy " . count( $tit_ex ) . ' paquetes) → ' . pm_url_paquetes( $destino, $i ) );
	$cambios += $nuevo + $var;
	if ( $aplicar && ( $nuevo || $var ) ) {
		$antes = count( $tit_ex );
		update_field( 'paquetes', $filas, $p->ID );
		$leidos = pm_filas( 'paquetes', $p->ID );
		$tits   = array_map( fn( $f ) => (string) ( $f['titulo'] ?? '' ), $leidos );
		$ok     = count( $leidos ) === $antes + $nuevo && in_array( $titulo, $tits, true );
		foreach ( $leidos as $f ) {
			if ( 0 === strpos( (string) ( $f['titulo'] ?? '' ), $t['vtit'] ) ) {
				$ok = $ok && false !== strpos( (string) ( $f['variantes'] ?? '' ), $t['var'] );
			}
		}
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $destino ($i): " . count( $leidos ) . ' paquetes guardados' );
		$ok || WP_CLI::error( "$destino ($i): la lectura de vuelta no coincide." );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
