<?php
/**
 * Search Console follow-up, 13/11/2026 (docs/seo-consultas-2026-11-13.md), fix 1: the licence hub for foreigners.
 *  - EN <title> "Argentina fishing license for foreigners 2026/27: prices" and H2 "Argentina fishing license 2026/27: which permit do you need?"
 *    (renames the existing "Which permit do I need..." section, so the page does not carry two).
 *  - FAQ (es/en/pt): three questions answered ONLY from the page's own sourced text (rates, sources read 4-8 Nov 2026, package comparison).
 *    Written as a group block `pm-faq`; the FAQPage JSON-LD comes from pm_faqs_contenido() (plugin, ships with the merge).
 *  - Patagonia licence note (es/en/pt): one paragraph linking to the hub ("Argentina fishing license").
 *  - Guides: reports every guide that does not link to the hub; adds the link where the hub applies and a permit heading exists.
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = seo-consultas-licencias-2026-11-13).
 */
// --- Helpers shared by the seo-consultas-2026-11-13 scripts (copied in each file: wp-script.yml sends one file) ---
if ( ! function_exists( 'pm_sc_h2' ) ) {
	function pm_sc_h2( string $texto, int $nivel = 2 ): string {
		return "<!-- wp:heading" . ( 2 === $nivel ? '' : ' {"level":' . $nivel . '}' ) . " -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->\n\n";
	}
	function pm_sc_p( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	function pm_sc_lista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
	}
	/** FAQ as a group block with the class pm-faq: visible on the page and read by pm_faqs_contenido() for the FAQPage JSON-LD. */
	function pm_sc_faq( array $faqs ): string {
		$in = '';
		foreach ( $faqs as [ $q, $a ] ) {
			$in .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( $in ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	function pm_sc_a( string $url, string $texto ): string {
		return '<a href="' . esc_url( $url ) . '">' . $texto . '</a>';
	}
	/** Heading blocks with offset: [ [ offset, texto plano ], … ]. */
	function pm_sc_encabezados( string $c ): array {
		preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
		$out = [];
		foreach ( $m[0] as $n => $b ) {
			$out[] = [ $b[1], trim( wp_strip_all_tags( $m[1][ $n ][0] ) ), strlen( $b[0] ) ];
		}
		return $out;
	}
	/** Offset of the first heading whose text matches $regex, or null. */
	function pm_sc_pos_encabezado( string $c, string $regex ): ?int {
		foreach ( pm_sc_encabezados( $c ) as [ $pos, $texto ] ) {
			if ( preg_match( $regex, $texto ) ) {
				return $pos;
			}
		}
		return null;
	}
	/** Offset just after the first paragraph block whose plain text matches $regex, or null. */
	function pm_sc_pos_tras_parrafo( string $c, string $regex ): ?int {
		preg_match_all( '#<!-- wp:paragraph[^>]*-->\s*<p[^>]*>(.*?)</p>\s*<!-- /wp:paragraph -->\s*#su', $c, $m, PREG_OFFSET_CAPTURE );
		foreach ( $m[0] as $n => $b ) {
			if ( preg_match( $regex, wp_strip_all_tags( $m[1][ $n ][0] ) ) ) {
				return $b[1] + strlen( $b[0] );
			}
		}
		return null;
	}
	function pm_sc_insertar( string $c, int $pos, string $bloque ): string {
		return substr( $c, 0, $pos ) . $bloque . substr( $c, $pos );
	}
	/** Replace the text of one heading (exact plain text), keeping its markup; null when it is not there. */
	function pm_sc_renombrar( string $c, string $viejo, string $nuevo ): ?string {
		$n = 0;
		$r = preg_replace_callback( '#(<h[1-6][^>]*>)(.*?)(</h[1-6]>)#su', function ( $m ) use ( $viejo, $nuevo, &$n ) {
			if ( 0 === $n && trim( wp_strip_all_tags( $m[2] ) ) === $viejo ) {
				$n++;
				return $m[1] . $nuevo . $m[3];
			}
			return $m[0];
		}, $c );
		return $n ? $r : null;
	}
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
$clave_hub = 'licencias-extranjeros-argentina';
$cambios   = 0;

// 1. Hub: EN title and H2, FAQ in the three languages (every answer comes from the page's own sourced text).
$hub = [
	'en' => [
		'seo_title' => 'Argentina fishing license for foreigners 2026/27: prices',
		'h2'        => [ 'Which permit do I need depending on where I go?', 'Argentina fishing license 2026/27: which permit do you need?' ],
		'faq_h'     => 'Frequently asked questions',
		'faq'       => [
			[ 'Do I need a fishing license in Argentina as a tourist?', 'Yes. There is no national license: each region has its own, and a non-resident foreigner pays the permit of the region where they fish: Patagonia, Tierra del Fuego, Santa Cruz or Corrientes (rates in the table above; the sources are listed at the bottom of this page, read between 4 and 8 November 2026). If you go with a lodge or guide, the license may or may not be included: in the nine packages we compared it was included in 4, not included in 3 and unspecified in 2, so ask in writing.' ],
			[ 'How much is a fishing license for foreigners in Argentina?', 'For a non-resident foreigner in 2026/27: Patagonia, $ 50,000 for one day and $ 280,000 for the season; Tierra del Fuego (standard permit), $ 78,000 for one day and $ 260,000 for the season, plus an additional permit in preferred areas such as the Grande and Menéndez rivers; Santa Cruz, $ 90,000 for one day and $ 450,000 for the season; Corrientes, about $ 35,000 for one day (our calculation from liters of “súper” gasoline). Amounts are in Argentine pesos; the page shows approximate US dollars at US$ 1 = $ 1,545 (xe.com, 8 November 2026), and the peso fluctuates.' ],
			[ 'Can I buy a fishing license in Argentina online?', 'It depends on the region, and we only say what we have read. Santa Cruz: the permit is processed online on the official portal. Corrientes: the foreign tourist license is obtained online. Tierra del Fuego: the seller registers you in the Sport Fishing Information System (siped.ar) and checks your ID; you carry your ID and proof of payment, or the digital permit from the “pesca deportiva” app. For Patagonia this page does not say how it is bought online: confirm it with the seller or your guide.' ],
		],
	],
	'es' => [
		'seo_title' => null,
		'h2'        => null,
		'faq_h'     => 'Preguntas frecuentes',
		'faq'       => [
			[ '¿Necesito licencia de pesca en Argentina como turista?', 'Sí. No hay una licencia nacional: cada región tiene la suya, y un extranjero no residente paga el permiso de la región donde pesca: Patagonia, Tierra del Fuego, Santa Cruz o Corrientes (valores en la tabla de arriba; las fuentes están al final de esta página, leídas entre el 4 y el 8 de noviembre de 2026). Si vas con un lodge o un guía, la licencia puede estar incluida o no: en los nueve paquetes que comparamos estaba incluida en 4, no incluida en 3 y sin especificar en 2; pedilo por escrito.' ],
			[ '¿Cuánto cuesta la licencia de pesca para extranjeros en Argentina?', 'Para un extranjero no residente en 2026/27: Patagonia, $ 50.000 por un día y $ 280.000 por la temporada; Tierra del Fuego (permiso ordinario), $ 78.000 por un día y $ 260.000 por la temporada, más un permiso adicional en zonas preferenciales como los ríos Grande y Menéndez; Santa Cruz, $ 90.000 por un día y $ 450.000 por la temporada; Corrientes, unos $ 35.000 por un día (cálculo nuestro a partir de litros de nafta “súper”). Los montos son en pesos argentinos; la página muestra dólares aproximados con US$ 1 = $ 1.545 (xe.com, 8 de noviembre de 2026), y el peso se mueve.' ],
			[ '¿Se puede sacar la licencia de pesca de Argentina por internet?', 'Depende de la región, y decimos solo lo que leímos. Santa Cruz: el permiso se tramita en línea en el portal oficial. Corrientes: la licencia de turista extranjero se obtiene en línea. Tierra del Fuego: el vendedor te registra en el Sistema de Información de Pesca Deportiva (siped.ar) y controla tu documento; llevás el documento y el comprobante de pago, o el permiso digital de la app “pesca deportiva”. Para la Patagonia esta página no dice cómo se compra en línea: confirmalo con el vendedor o con tu guía.' ],
		],
	],
	'pt' => [
		'seo_title' => null,
		'h2'        => null,
		'faq_h'     => 'Perguntas frequentes',
		'faq'       => [
			[ 'Preciso de licença de pesca na Argentina como turista?', 'Sim. Não existe uma licença nacional: cada região tem a sua, e um estrangeiro não residente paga a licença da região onde pesca: Patagônia, Terra do Fogo, Santa Cruz ou Corrientes (valores na tabela acima; as fontes estão no fim desta página, lidas entre 4 e 8 de novembro de 2026). Se for com um lodge ou guia, a licença pode estar incluída ou não: nos nove pacotes que comparamos estava incluída em 4, não incluída em 3 e sem especificar em 2; peça por escrito.' ],
			[ 'Quanto custa a licença de pesca para estrangeiros na Argentina?', 'Para um estrangeiro não residente em 2026/27: Patagônia, $ 50.000 por um dia e $ 280.000 pela temporada; Terra do Fogo (licença ordinária), $ 78.000 por um dia e $ 260.000 pela temporada, mais uma licença adicional em zonas preferenciais como os rios Grande e Menéndez; Santa Cruz, $ 90.000 por um dia e $ 450.000 pela temporada; Corrientes, cerca de $ 35.000 por um dia (cálculo nosso a partir de litros de gasolina “súper”). Os valores são em pesos argentinos; a página mostra dólares aproximados com US$ 1 = $ 1.545 (xe.com, 8 de novembro de 2026), e o peso varia.' ],
			[ 'Dá para tirar a licença de pesca da Argentina pela internet?', 'Depende da região, e dizemos só o que lemos. Santa Cruz: a licença é tramitada on-line no portal oficial. Corrientes: a licença de turista estrangeiro é obtida on-line. Terra do Fogo: o vendedor registra você no Sistema de Informação de Pesca Esportiva (siped.ar) e confere seu documento; você leva o documento e o comprovante de pagamento, ou a licença digital do app “pesca deportiva”. Para a Patagônia esta página não diz como se compra on-line: confirme com o vendedor ou com o seu guia.' ],
		],
	],
];
foreach ( $hub as $i => $h ) {
	$p = pm_buscar( 'pm_nota', $clave_hub, $i );
	if ( ! $p ) {
		WP_CLI::warning( "hub ($i): no existe, se salta." );
		continue;
	}
	$c      = $p->post_content;
	$nuevo  = $c;
	$antes  = [ 'seo_title' => pm_campo( 'seo_title', $p->ID ) ];
	$tareas = [];
	if ( $h['seo_title'] && $antes['seo_title'] !== $h['seo_title'] ) {
		if ( mb_strlen( $h['seo_title'] ) > 65 ) {
			WP_CLI::error( "title demasiado largo ($i)" );
		}
		$tareas[] = 'seo_title';
	}
	if ( $h['h2'] ) {
		if ( false !== strpos( $nuevo, $h['h2'][1] ) ) {
			// ya está
		} elseif ( null !== ( $r = pm_sc_renombrar( $nuevo, $h['h2'][0], $h['h2'][1] ) ) ) {
			$nuevo    = $r;
			$tareas[] = 'h2';
		} else {
			WP_CLI::warning( "hub ($i): no encontré el H2 «{$h['h2'][0]}»; no lo cambio." );
		}
	}
	if ( false === strpos( $nuevo, 'pm-faq' ) ) {
		$nuevo   .= "\n\n" . pm_sc_h2( $h['faq_h'] ) . pm_sc_faq( $h['faq'] );
		$tareas[] = 'faq';
	}
	if ( ! $tareas ) {
		WP_CLI::log( "hub ($i) #{$p->ID}: ya está, no toco nada." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": hub ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' → ' . implode( ', ', $tareas ) );
	WP_CLI::log( "  ANTES seo_title: «{$antes['seo_title']}»" . ( in_array( 'h2', $tareas, true ) ? " · H2: «{$h['h2'][0]}»" : '' ) );
	WP_CLI::log( '  DESPUÉS ' . ( in_array( 'seo_title', $tareas, true ) ? "seo_title: «{$h['seo_title']}» (" . mb_strlen( $h['seo_title'] ) . ')' : 'seo_title igual' ) . ( in_array( 'h2', $tareas, true ) ? " · H2: «{$h['h2'][1]}»" : '' ) . ( in_array( 'faq', $tareas, true ) ? ' · FAQ: ' . implode( ' | ', array_column( $h['faq'], 0 ) ) : '' ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	if ( $nuevo !== $c ) {
		pm_sc_guardar( $p->ID, $nuevo, array_merge( in_array( 'h2', $tareas, true ) ? [ $h['h2'][1] ] : [], in_array( 'faq', $tareas, true ) ? [ 'pm-faq', $h['faq'][0][0] ] : [] ), "hub ($i)" );
	}
	if ( in_array( 'seo_title', $tareas, true ) ) {
		pm_sc_campo( $p->ID, 'seo_title', $h['seo_title'], "hub ($i)" );
	}
	WP_CLI::log( "VERIFIED: hub ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' · FAQ del contenido: ' . ( function_exists( 'pm_faqs_contenido' ) ? count( pm_faqs_contenido( get_post( $p->ID )->post_content ) ) . ' preguntas' : '(JSON-LD de FAQ llega con el merge)' ) );
}

// 2. Patagonia licence note: link to the hub with the anchor "Argentina fishing license" (and equivalents).
$enlace_pat = [
	'en' => 'Fishing elsewhere in Argentina? See the %s for foreigners 2026/27: rates by region.',
	'es' => '¿Vas a pescar en otra región? Mirá la %s 2026/27: valores por región.',
	'pt' => 'Vai pescar em outra região? Veja a %s 2026/27: valores por região.',
];
$ancla_pat = [ 'en' => 'Argentina fishing license', 'es' => 'licencia de pesca para extranjeros en Argentina', 'pt' => 'licença de pesca para estrangeiros na Argentina' ];
$hubs      = [];
foreach ( [ 'en', 'es', 'pt' ] as $i ) {
	$n = pm_buscar( 'pm_nota', $clave_hub, $i );
	$n || WP_CLI::error( "No encontré el hub ($i)." );
	$hubs[ $i ] = get_permalink( $n->ID );
}
foreach ( $enlace_pat as $i => $plantilla ) {
	$p = pm_buscar( 'pm_nota', 'licencia-pesca-patagonia-precios', $i );
	if ( ! $p ) {
		WP_CLI::warning( "nota Patagonia ($i): no existe." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, $hubs[ $i ] ) ) {
		WP_CLI::log( "nota Patagonia ($i) #{$p->ID}: ya enlaza al hub." );
		continue;
	}
	$par = pm_sc_p( sprintf( $plantilla, pm_sc_a( $hubs[ $i ], $ancla_pat[ $i ] ) ) );
	$pos = pm_sc_pos_tras_parrafo( $c, '/Short answer|Respuesta corta|Resposta curta/iu' );
	$donde = 'tras la respuesta corta';
	if ( null === $pos ) {
		$pos   = strlen( $c );
		$donde = 'al final';
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": nota Patagonia ($i) #{$p->ID}: enlace al hub «{$ancla_pat[$i]}» $donde" );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, pm_sc_insertar( $c, $pos, "\n" . $par ), [ $hubs[ $i ] ], "nota Patagonia ($i)" );
		WP_CLI::log( "VERIFIED: nota Patagonia ($i) #{$p->ID} enlaza a {$hubs[ $i ]}" );
	}
}

// 3. Guides: report every guide that does not link to the hub; add the link where the hub applies (provinces it covers) and a permit heading exists.
$cubiertas = [ 'neuquen', 'rio-negro', 'chubut', 'tierra-del-fuego', 'santa-cruz', 'corrientes' ];
$texto_g   = [
	'es' => 'Todos los permisos por región, en pesos y en dólares aproximados: <a href="%s">licencia de pesca para extranjeros en Argentina 2026/27</a>.',
	'en' => 'All permits by region, in pesos and approximate dollars: <a href="%s">fishing license for foreigners in Argentina 2026/27</a>.',
	'pt' => 'Todas as licenças por região, em pesos e em dólares aproximados: <a href="%s">licença de pesca para estrangeiros na Argentina 2026/27</a>.',
];
$huecos = [];
foreach ( $texto_g as $i => $plantilla ) {
	foreach ( pm_destinos_lista( $i ) as $d ) {
		$p = get_post( $d['id'] );
		if ( false !== strpos( $p->post_content, $hubs[ $i ] ) ) {
			WP_CLI::log( "guía {$d['clave']} ($i): ya enlaza al hub." );
			continue;
		}
		if ( ! in_array( $d['provincia'], $cubiertas, true ) ) {
			WP_CLI::log( "HUECO (fuera de lo que cubre el hub) guía {$d['clave']} ($i), provincia {$d['provincia']}." );
			continue;
		}
		$c   = $p->post_content;
		$pos = null;
		foreach ( pm_sc_encabezados( $c ) as $k => [ $off, $txt ] ) {
			if ( preg_match( '/permis|licen[cç]|permit|licen[cs]e/iu', $txt ) ) {
				$sig = pm_sc_encabezados( $c )[ $k + 1 ][0] ?? null;
				$pos = $sig ?? strlen( $c );
				break;
			}
		}
		if ( null === $pos ) {
			$huecos[] = "{$d['clave']} ($i)";
			WP_CLI::warning( "HUECO guía {$d['clave']} ($i): sin encabezado de permiso; no enlaza al hub y no lo agrego a ciegas." );
			continue;
		}
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": guía {$d['clave']} ($i) #{$p->ID}: enlace al hub al final de la sección del permiso" );
		$cambios++;
		if ( $aplicar ) {
			pm_sc_guardar( $p->ID, pm_sc_insertar( $c, $pos, pm_sc_p( sprintf( $plantilla, esc_url( $hubs[ $i ] ) ) ) ), [ $hubs[ $i ] ], "guía {$d['clave']} ($i)" );
			WP_CLI::log( "VERIFIED: guía {$d['clave']} ($i) enlaza a {$hubs[ $i ]}" );
		}
	}
}
$huecos && WP_CLI::log( 'GUÍAS SIN ENLACE Y SIN ENCABEZADO DE PERMISO: ' . implode( ', ', $huecos ) );
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
