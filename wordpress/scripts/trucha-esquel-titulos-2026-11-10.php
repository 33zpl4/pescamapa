<?php
/**
 * Search Console follow-up, 10/11/2026 (docs/investigacion/auditoria-rondas-r1-r2-r4-2026-11-10.md and the query-by-page analysis):
 *  1. Trout species page (es/en/pt): the <title> now says what people search ("Rainbow, brown and brook trout in Patagonia",
 *     "Tipos de trucha en la Patagonia", "Tipos de truta na Patagônia") and there is one section per species with ONLY what was checked
 *     (stocking percentages and the brook trout's retreat, Macchi and Vigliano 2014; the season is the page's own sentence). Sizes and
 *     records stay unpublished until a source is read.
 *  2. Esquel y Trevelin guide, English only: "Fly fishing" in the H1, the <title> and the description (query "fly fishing esquel argentina").
 * Idempotent: a page that already has the new section / title is left alone. Simulacro first ($aplicar = false).
 * Run with wp-script.yml (script = trucha-esquel-titulos-2026-11-10).
 */
$aplicar = false;
$especie = [
	'es' => [
		'seo_title' => 'Tipos de trucha en la Patagonia: especies y reglas',
		'marca'     => 'Trucha arcoíris en la Patagonia',
		'secciones' => [
			[ 'Trucha arcoíris en la Patagonia', 'Es la especie que más se sembró: llegó al 96,4 % de las cuencas y subcuencas de la Patagonia, según el análisis de 2014 de Macchi y Vigliano. Se la pesca durante la temporada de cada provincia, que abre en noviembre y cierra entre abril y mayo, con excepciones por ambiente.' ],
			[ 'Trucha marrón en la Patagonia', 'Se sembró en el 67,3 % de las cuencas y subcuencas. En el Río Grande, en Tierra del Fuego, hay una forma que migra al mar y vuelve con tamaños enormes: está contada más abajo. En cualquier ambiente, mirá las reglas del anexo de la provincia.' ],
			[ 'Trucha de arroyo o fontinalis en la Patagonia', 'Se sembró en el 72,7 % de las cuencas, pero hoy se la registra en el 42,5 % de aquellas donde se la introdujo, y en el norte de la Patagonia solo en las nacientes de los ríos Negro, Limay y Chubut. Preguntale a tu guía en qué agua conviene buscarla.' ],
		],
		'cierre'    => 'El tamaño típico y el récord de cada especie no los publicamos hasta tener una fuente leída (el registro de la IGFA o una fuente de fauna provincial).',
	],
	'en' => [
		'seo_title' => 'Rainbow, brown and brook trout in Patagonia',
		'marca'     => 'Rainbow trout in Patagonia',
		'secciones' => [
			[ 'Rainbow trout in Patagonia', 'It is the most stocked species: it reached 96.4% of Patagonia\'s basins and sub-basins, according to the 2014 analysis by Macchi and Vigliano. It is fished during each province\'s season, which opens in November and closes between April and May, with exceptions by water.' ],
			[ 'Brown trout in Patagonia', 'It was stocked in 67.3% of the basins and sub-basins. On the Río Grande, in Tierra del Fuego, there is a form that migrates to the sea and comes back at huge sizes: it is described below. On any water, check the rules in the province\'s annex.' ],
			[ 'Brook trout (fontinalis) in Patagonia', 'It was stocked in 72.7% of the basins, but today it is recorded in 42.5% of those where it was introduced, and in northern Patagonia only in the headwaters of the Negro, Limay and Chubut rivers. Ask your guide which water is worth trying.' ],
		],
		'cierre'    => 'We do not publish typical sizes or records for each species until we have read a source (the IGFA record or a provincial fisheries source).',
	],
	'pt' => [
		'seo_title' => 'Tipos de truta na Patagônia: espécies e regras',
		'marca'     => 'Truta arco-íris na Patagônia',
		'secciones' => [
			[ 'Truta arco-íris na Patagônia', 'É a espécie mais semeada: chegou a 96,4 % das bacias e sub-bacias da Patagônia, segundo a análise de 2014 de Macchi e Vigliano. É pescada durante a temporada de cada província, que abre em novembro e fecha entre abril e maio, com exceções por ambiente.' ],
			[ 'Truta marrom na Patagônia', 'Foi semeada em 67,3 % das bacias e sub-bacias. No Río Grande, na Terra do Fogo, existe uma forma que migra para o mar e volta com tamanhos enormes: está contada mais abaixo. Em qualquer ambiente, veja as regras do anexo da província.' ],
			[ 'Truta de riacho ou fontinalis na Patagônia', 'Foi semeada em 72,7 % das bacias, mas hoje está registrada em 42,5 % daquelas onde foi introduzida, e no norte da Patagônia só nas nascentes dos rios Negro, Limay e Chubut. Pergunte ao seu guia em que água vale a pena procurá-la.' ],
		],
		'cierre'    => 'O tamanho típico e o recorde de cada espécie não os publicamos até ter uma fonte lida (o registro da IGFA ou uma fonte de fauna provincial).',
	],
];
$cambios = 0;
foreach ( $especie as $i => $e ) {
	$p = pm_buscar( 'pm_especie', 'trucha', $i );
	if ( ! $p ) {
		WP_CLI::log( "trucha ($i): no existe, se salta." );
		continue;
	}
	$tl = mb_strlen( $e['seo_title'] );
	if ( $tl > 60 ) {
		WP_CLI::error( "title demasiado largo ($i): $tl" );
	}
	$c = $p->post_content;
	$titulo_igual = pm_campo( 'seo_title', $p->ID ) === $e['seo_title'];
	$tiene_secc   = false !== strpos( $c, $e['marca'] );
	if ( $titulo_igual && $tiene_secc ) {
		WP_CLI::log( "trucha ($i): ya está, no toco nada." );
		continue;
	}
	$nuevo = $c;
	if ( ! $tiene_secc ) {
		preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
		// Headings: 0 key facts, 1 the species, 2 when and how they arrived (10/11), 3 the sea-run brown trout: the sections go before 3.
		if ( count( $m[0] ) < 4 || ! preg_match( '/mar|sea|Río Grande/iu', wp_strip_all_tags( $m[1][3][0] ) ) ) {
			WP_CLI::warning( "trucha ($i): no encontré el encabezado esperado (trucha de mar); se salta." );
			continue;
		}
		$bloque = '';
		foreach ( $e['secciones'] as [ $h, $texto ] ) {
			$bloque .= "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$h}</h2>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$texto}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		$bloque .= "<!-- wp:paragraph -->\n<p>{$e['cierre']}</p>\n<!-- /wp:paragraph -->\n\n";
		$nuevo   = substr( $c, 0, $m[0][3][1] ) . $bloque . substr( $c, $m[0][3][1] );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": trucha ($i) #{$p->ID}: title «{$e['seo_title']}» ($tl)" . ( $tiene_secc ? '' : ', tres secciones por especie' ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	if ( $nuevo !== $c ) {
		wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ] );
	}
	update_field( 'seo_title', $e['seo_title'], $p->ID );
	$ok = pm_campo( 'seo_title', $p->ID ) === $e['seo_title'] && false !== strpos( get_post( $p->ID )->post_content, $e['marca'] );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": trucha ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "trucha ($i): la lectura de vuelta no coincide." );
}

// Esquel y Trevelin, English.
$g = pm_buscar( 'pm_destino', 'esquel-trevelin', 'en' );
if ( ! $g ) {
	WP_CLI::error( 'No encontré la guía esquel-trevelin (en).' );
}
$h1   = 'Fly fishing in Esquel and Trevelin, Chubut: rules and 2026/27 permit';
$st   = 'Fly fishing in Esquel and Trevelin, Chubut: rules, permit';
$sd   = 'Fly fishing in Esquel and Trevelin, Chubut: 2026/27 season and rules from the official annex, Futaleufú and Carrileufú rivers, lakes, permit and lodges.';
$lt   = mb_strlen( $st );
$ld   = mb_strlen( $sd );
if ( $lt > 60 || $ld > 160 || $ld < 70 ) {
	WP_CLI::error( "SEO fuera de rango (esquel en): title $lt, description $ld" );
}
if ( pm_campo( 'seo_title', $g->ID ) === $st && pm_campo( 'h1', $g->ID ) === $h1 ) {
	WP_CLI::log( 'esquel-trevelin (en): ya está, no toco nada.' );
} else {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": esquel-trevelin (en) #{$g->ID}: h1 «$h1», title ($lt), description ($ld)" );
	$cambios++;
	if ( $aplicar ) {
		update_field( 'h1', $h1, $g->ID );
		update_field( 'seo_title', $st, $g->ID );
		update_field( 'seo_description', $sd, $g->ID );
		$ok = pm_campo( 'h1', $g->ID ) === $h1 && pm_campo( 'seo_title', $g->ID ) === $st && pm_campo( 'seo_description', $g->ID ) === $sd;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": esquel-trevelin (en) #{$g->ID} " . get_permalink( $g->ID ) );
		$ok || WP_CLI::error( 'esquel-trevelin (en): la lectura de vuelta no coincide.' );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
