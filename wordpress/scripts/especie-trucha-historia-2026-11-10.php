<?php
/**
 * Trout species page (es/en/pt), 10/11/2026: a section on when and how the trout arrived, with the one source Claude opened and
 * checked in full: Macchi and Vigliano, Ecología Austral 24(2), 2014 (SciELO Argentina). Also replaces the unsourced sentence about
 * brook trout ("present in some lakes and rivers in the centre of the region") with what the paper says, and refreshes the sources.
 * Not published (assistants contradict each other or the page was not opened): arrival dates per species, sizes, records, spawning.
 * Idempotent: a page that already mentions 1904 is left alone. Simulacro first ($aplicar = false).
 * Run with wp-script.yml (script = especie-trucha-historia-2026-11-10).
 */
$aplicar = true;
$hoy     = '20261110';
$url_paper = 'https://www.scielo.org.ar/scielo.php?script=sci_arttext&pid=S1667-782X2014000200005';
$sec = [
	'es' => [
		'h' => 'Cuándo y cómo llegaron',
		'p' => [
			'Ninguna de las tres es nativa. La primera introducción oficial de salmónidos en la Patagonia fue en 1904, y para sostenerla se construyeron dos criaderos: el de Nahuel Huapi, sobre el río Limay (1904 a 1931), y otro sobre el río Santa Cruz (1904 a 1913). Un análisis de las siembras entre 1904 y 2011 en 54 cuencas (Macchi y Vigliano, <em>Ecología Austral</em>, 2014) encontró que la arcoíris se sembró en el 96,4 % de las cuencas y subcuencas, la fontinalis en el 72,7 % y la marrón en el 67,3 %.',
			'La fontinalis es la que más retrocedió: se registra en 17 cuencas, el 42,5 % de aquellas donde se la introdujo. Hasta los años 40 estaba en el 100 % de las cuencas de los ríos Negro, Limay y Chubut; hoy solo aparece en sus nacientes. Los autores atribuyen ese retroceso, aparentemente, al aumento de las siembras de arcoíris y marrón.',
		],
		'viejo' => [ 'presente en algunos lagos y ríos del centro de la región' ],
		'nuevo' => 'que hoy aparece solo en las nacientes de los ríos Negro, Limay y Chubut, según un estudio de 2014',
		'fuente' => 'Macchi, P. y Vigliano, P. (2014): Introducción de salmónidos en la Patagonia: los fantasmas del manejo pasado, presente y futuro. Ecología Austral 24(2)',
	],
	'en' => [
		'h' => 'When and how they arrived',
		'p' => [
			'None of the three is native. The first official introduction of salmonids in Patagonia was in 1904, and two hatcheries were built to support it: the Nahuel Huapi hatchery, on the Limay River (1904 to 1931), and another on the Santa Cruz River (1904 to 1913). An analysis of the stocking between 1904 and 2011 across 54 drainages (Macchi and Vigliano, <em>Ecología Austral</em>, 2014) found that rainbow trout was stocked in 96.4% of the basins and sub-basins, brook trout in 72.7% and brown trout in 67.3%.',
			'Brook trout is the one that has lost the most ground: it is recorded in 17 basins, 42.5% of those where it was introduced. Until the 1940s it was in 100% of the Negro, Limay and Chubut river basins; today it is found only in their headwaters. The authors attribute that decline, apparently, to the increase in rainbow and brown trout stocking.',
		],
		'viejo' => [ 'present in some lakes and rivers in the centre of the region' ],
		'nuevo' => 'which today is found only in the headwaters of the Negro, Limay and Chubut rivers, according to a 2014 study',
		'fuente' => 'Macchi, P. and Vigliano, P. (2014): Salmonid introduction in Patagonia: the ghost of past, present and future management. Ecología Austral 24(2)',
	],
	'pt' => [
		'h' => 'Quando e como chegaram',
		'p' => [
			'Nenhuma das três é nativa. A primeira introdução oficial de salmonídeos na Patagônia foi em 1904, e para sustentá-la foram construídos dois criadouros: o de Nahuel Huapi, no rio Limay (1904 a 1931), e outro no rio Santa Cruz (1904 a 1913). Uma análise das semeaduras entre 1904 e 2011 em 54 bacias (Macchi e Vigliano, <em>Ecología Austral</em>, 2014) encontrou que a arco-íris foi semeada em 96,4 % das bacias e sub-bacias, a fontinalis em 72,7 % e a marrom em 67,3 %.',
			'A fontinalis é a que mais perdeu terreno: está registrada em 17 bacias, 42,5 % daquelas onde foi introduzida. Até os anos 40 estava em 100 % das bacias dos rios Negro, Limay e Chubut; hoje só aparece em suas nascentes. Os autores atribuem esse recuo, aparentemente, ao aumento das semeaduras de arco-íris e marrom.',
		],
		'viejo' => [ 'presente em alguns lagos e rios do centro da região', 'presente em alguns lagos e rios do centro da região' ],
		'nuevo' => 'que hoje só aparece nas nascentes dos rios Negro, Limay e Chubut, segundo um estudo de 2014',
		'fuente' => 'Macchi, P. e Vigliano, P. (2014): Introdução de salmonídeos na Patagônia: os fantasmas do manejo passado, presente e futuro. Ecología Austral 24(2)',
	],
];
$cambios = 0;
foreach ( $sec as $i => $s ) {
	$p = pm_buscar( 'pm_especie', 'trucha', $i );
	if ( ! $p ) {
		WP_CLI::log( "trucha ($i): no existe, se salta." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, '1904' ) ) {
		WP_CLI::log( "trucha ($i): ya menciona 1904, no toco nada." );
		continue;
	}
	preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
	if ( count( $m[0] ) < 3 ) {
		WP_CLI::warning( "trucha ($i): esperaba al menos 3 encabezados y hay " . count( $m[0] ) . '; se salta.' );
		continue;
	}
	$pos    = $m[0][2][1]; // before the third heading: after "Datos clave" and the species section, before the sea-run brown trout
	$bloque = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$s['h']}</h2>\n<!-- /wp:heading -->\n\n";
	foreach ( $s['p'] as $par ) {
		$bloque .= "<!-- wp:paragraph -->\n<p>{$par}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	$nuevo = substr( $c, 0, $pos ) . $bloque . substr( $c, $pos );
	$frase = false;
	foreach ( $s['viejo'] as $v ) {
		if ( false !== strpos( $nuevo, $v ) ) {
			$nuevo = str_replace( $v, $s['nuevo'], $nuevo );
			$frase = true;
			break;
		}
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": trucha ($i) #{$p->ID}: sección nueva antes del encabezado «" . wp_strip_all_tags( $m[1][2][0] ) . '»; frase de la fontinalis ' . ( $frase ? 'reemplazada' : 'NO encontrada (queda como estaba)' ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ] );
	$fuentes = pm_filas( 'fuentes', $p->ID );
	$lista   = [];
	foreach ( $fuentes as $f ) {
		$nombre = (string) ( $f['nombre'] ?? '' );
		if ( false !== strpos( $nombre, 'Reglamento de Pesca Deportiva Continental Patagónico 2025/2026' ) ) {
			$nombre = str_replace( '2025/2026 (portal interprovincial)', '2026/27 (texto actualizado el 26 de octubre de 2026, portal interprovincial)', $nombre );
		}
		$lista[] = [ 'nombre' => $nombre, 'url' => (string) ( $f['url'] ?? '' ), 'oficial' => ! empty( $f['oficial'] ) ? 1 : 0, 'consultada' => (string) ( $f['consultada'] ?? $hoy ) ];
	}
	$lista[] = [ 'nombre' => $s['fuente'], 'url' => $url_paper, 'oficial' => 0, 'consultada' => $hoy ];
	update_field( 'fuentes', $lista, $p->ID );
	update_field( 'actualizado', $hoy, $p->ID );
	$g  = get_post( $p->ID )->post_content;
	$ok = false !== strpos( $g, '1904' ) && count( pm_filas( 'fuentes', $p->ID ) ) === count( $lista );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": trucha ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "trucha ($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios fichas." );
