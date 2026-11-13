<?php
/**
 * Golden dorado, 13/11/2026 (Search Console follow-up: "needs a source" items of PR 172).
 *  - Species page (es/en/pt): maximum length (FishBase 100 cm SL; CARU "about 1 m SL") and, labelled apart, the highest published weight
 *    31.4 kg (FishBase, ref. Machacek 2007: a records listing, not certified, higher than the IGFA record); new H2 "where it lives"
 *    (FishBase and CARU, with the places where sources differ); IUCN "Least Concern" via the FishBase mirror; FAQ "where does it live".
 *  - RECORD ATTRIBUTION FIX: the IGFA page read on 13/11/2026 shows only the weight (55 lb 11 oz / 25.28 kg). River, year and angler come
 *    from the press (Diario Uno, 24/10/2026). Key facts, record paragraph, FAQ answer, SEO description and the pillar note's Uruguay River
 *    paragraph now say so. Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = dorado-rango-record-2026-11-13).
 * Left out on purpose: Peru/Madeira/Salí-Dulce (no source read); the São Francisco and Magdalena (only in the IGFA text; said as a difference).
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


$aplicar = false;
$hoy     = '20261113';
$cambios = 0;

$u_fb    = 'https://www.fishbase.se/summary/Salminus-brasiliensis.html';
$u_caru  = 'https://caru.org.uy/nuevositio/wp-content/uploads/2024/04/PECES-Interior.pdf';
$u_igfa  = 'https://igfa.org/game-fish-database/?search_term_1=126&search_type=SpeciesID';
$u_brit  = 'https://www.fishbase.se/references/FBRefSummary.php?ID=36796';
$u_mach  = 'https://www.fishbase.se/references/FBRefSummary.php?ID=72380';

/** One question of a pm-faq group, as the blocks pm_sc_faq() writes. */
function pm_dr_faq_item( string $q, string $a ): string {
	return "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
}

$t = [
	'es' => [
		'rx_datos' => '#<li><strong>Récord mundial \(IGFA, todo equipo\):</strong>.*?</li>#su',
		'datos' => '<li><strong>Récord mundial (IGFA, todo equipo):</strong> 25,28 kg (55 libras y 11 onzas). La IGFA publica solo el peso; el río, el año y el pescador (Andre De Button, río Uruguay, 2006) son de la prensa (Diario Uno, consultado el 24/10/2026).</li>',
		'record' => 'El récord mundial del dorado en la categoría todo equipo (<em>all-tackle</em>) de la IGFA, la International Game Fish Association, es de <strong>25,28 kg (55 libras y 11 onzas)</strong>. Es lo único que muestra la ficha de la IGFA (leída el 13/11/2026): no trae río, año ni pescador. Según la prensa (Diario Uno, consultado el 24/10/2026), lo pescó Andre De Button en 2006 en el río Uruguay, en la frontera entre Argentina y Uruguay. La IGFA certifica sus récords con control de la línea y del terminal, pesaje en balanza certificada, fotografías y declaración de testigos.',
		'faq_record' => 'El récord mundial del dorado en la categoría todo equipo (all-tackle) de la IGFA es de 25,28 kg (55 libras y 11 onzas); la ficha de la IGFA muestra solo el peso. Según la prensa (Diario Uno, consultado el 24/10/2026), lo pescó Andre De Button en 2006 en el río Uruguay, en la frontera entre Argentina y Uruguay.',
		'faq_tam_extra' => ' Como máximos, FishBase da 100 cm de longitud estándar y la CARU «alrededor de 1 m»; el mayor peso publicado es 31,4 kg (FishBase, a partir de un sitio que lista récords, no un récord certificado).',
		'max' => 'Los máximos son otra cosa que el tamaño habitual. <strong>Longitud máxima:</strong> 100 cm de longitud estándar, según FishBase (referencia: Britski et al., 1999, <em>Peixes do Pantanal</em>); la CARU dice «alrededor de 1 m de longitud estándar» (Serra et al., 2019, reedición CARU 2023, <em>Peces del bajo Río Uruguay – Especies destacadas</em>). <strong>Mayor peso publicado:</strong> 31,4 kg, según FishBase (referencia: Machacek, 2007, un sitio web que lista récords de pesca: no es un récord certificado). Es más que el récord de la IGFA (25,28 kg); no son comparables y no los combinamos, porque uno sale de un listado sin certificación y el otro de una federación que verifica sus récords. Fuentes leídas el 13/11/2026.',
		'h2' => 'Dónde vive el dorado',
		'donde' => [
			'Según FishBase (página de la especie, leída el 13/11/2026), el dorado vive en «Sudamérica: cuencas de los ríos Paraná, Paraguay y Uruguay; drenaje de la Laguna dos Patos, alto Chaparé y cuenca del río Mamoré en Bolivia. La presencia en el resto del Amazonas es muy dudosa» (traducción nuestra del original en inglés).',
			'La CARU, la Comisión Administradora del Río Uruguay, dice en <em>Peces del bajo Río Uruguay – Especies destacadas</em> (Serra et al., 2019, reedición CARU 2023): «Cuencas del Río de la Plata, Laguna de los Patos y ríos Chaparé y Mamoré en la región alta del río Amazonas (Argentina, Bolivia, Brasil, Paraguay y Uruguay). Es una especie frecuente en el bajo río Uruguay.» Agrega que en sus migraciones reproductivas entre los ríos Paraná y Uruguay puede llegar a recorrer más de 800 km, a razón de unos 20 km diarios.',
			'Las fuentes difieren en los bordes: el texto de la IGFA sobre la especie (leído el 13/11/2026) todavía menciona los sistemas del São Francisco y del Magdalena, que ni FishBase ni la CARU incluyen. Publicamos solo lo que dicen esas dos fuentes y no sumamos otras cuencas sin una fuente.',
			'<strong>Conservación:</strong> FishBase informa la categoría «Preocupación menor» (<em>Least Concern</em>) de la Lista Roja de la UICN, evaluada el 7 de octubre de 2020 (espejo de FishBase, versión 2025-2 de la UICN; la página de la UICN no pudimos leerla). Los listados nacionales son otros: la CARU recoge, por ejemplo, «Vulnerable» para Brasil en el río Uruguay.',
		],
		'faq_q' => '¿Dónde vive el dorado?',
		'faq_a' => 'En las cuencas de los ríos Paraná, Paraguay y Uruguay (FishBase) o, según la CARU, en las cuencas del Río de la Plata, la Laguna de los Patos y los ríos Chaparé y Mamoré, en Argentina, Bolivia, Brasil, Paraguay y Uruguay; es frecuente en el bajo río Uruguay. Las fuentes difieren en los bordes (la IGFA todavía menciona el São Francisco y el Magdalena) y publicamos solo lo que coincide en FishBase y la CARU.',
		'rx_pend' => '/PENDIENTE: tamaño y peso típicos y máximo/u',
		'pend' => 'tamaño y peso típicos de la especie con una fuente científica que los mida: los rangos de arriba son de la pesca deportiva y de la bibliografía general. El máximo ya tiene fuente (FishBase y CARU). Lo local va en la guía de cada destino.',
		'seo_description' => 'Dorado (Salminus brasiliensis): récord IGFA de 25,28 kg, dónde vive, cuánto mide y pesa y la regla de pesca y devolución en Corrientes.',
		'rx_reglas' => '/Reglas para pescarlo/u',
		'fuentes' => [
			[ 'FishBase: Salminus brasiliensis, página de la especie (distribución, tamaño máximo, UICN; leída el 13/11/2026)', $u_fb ],
			[ 'FishBase: referencia Britski, Silimon y Lopes (1999), Peixes do Pantanal (longitud máxima de 100 cm)', $u_brit ],
			[ 'FishBase: referencia Machacek (2007), World Records Freshwater Fishing (peso máximo publicado de 31,4 kg; listado, no certificado)', $u_mach ],
			[ 'CARU: Serra et al. (2019, reedición 2023), Peces del bajo Río Uruguay – Especies destacadas (distribución, migración)', $u_caru ],
			[ 'IGFA: base de datos de peces de caza, dorado (récord todo equipo de 25,28 kg, solo el peso; leída el 13/11/2026)', $u_igfa ],
		],
	],
	'en' => [
		'rx_datos' => '#<li><strong>World record \(IGFA, all-tackle\):</strong>.*?</li>#su',
		'datos' => '<li><strong>World record (IGFA, all-tackle):</strong> 25.28 kg (55 lb 11 oz). The IGFA publishes only the weight; the river, the year and the angler (Andre De Button, Uruguay River, 2006) come from the press (Diario Uno, consulted 24 October 2026).</li>',
		'record' => 'The IGFA (International Game Fish Association) all-tackle world record for the golden dorado is <strong>25.28 kg (55 lb 11 oz)</strong>. That is all the IGFA page shows (read 13 November 2026): no river, year or angler. According to the press (Diario Uno, consulted 24 October 2026), Andre De Button caught it in 2006 in the Uruguay River, on the border between Argentina and Uruguay. The IGFA certifies its records with line and leader testing, certified scale weighing, photographs and signed witness statements.',
		'faq_record' => 'The IGFA all-tackle world record for the golden dorado is 25.28 kg (55 lb 11 oz); the IGFA page shows only the weight. According to the press (Diario Uno, consulted 24 October 2026), Andre De Button caught it in 2006 in the Uruguay River, on the border between Argentina and Uruguay.',
		'faq_tam_extra' => ' As maximums, FishBase gives 100 cm standard length and CARU “about 1 m”; the highest published weight is 31.4 kg (FishBase, from a records-listing site, not a certified record).',
		'max' => 'Maximums are a different thing from the usual size. <strong>Maximum length:</strong> 100 cm standard length, according to FishBase (reference: Britski et al., 1999, <em>Peixes do Pantanal</em>); CARU says “about 1 m standard length” (Serra et al., 2019, CARU reissue 2023, <em>Peces del bajo Río Uruguay – Especies destacadas</em>, our translation). <strong>Highest published weight:</strong> 31.4 kg, according to FishBase (reference: Machacek, 2007, a website that lists fishing records: not a certified record). It is higher than the IGFA record (25.28 kg); the two are not comparable and we do not merge them, because one comes from an uncertified listing and the other from an organization that verifies its records. Sources read on 13 November 2026.',
		'h2' => 'Golden dorado range: where it lives',
		'donde' => [
			'According to FishBase (species page, read 13 November 2026), the golden dorado lives in “South America: Paraná, Paraguay, and Uruguay River basins; Laguna dos Patos drainage, upper Chaparé and Mamoré River basin in Bolivia. Occurrence in the remaining Amazon River highly doubtful.”',
			'CARU, the Uruguay River Administrative Commission, says in <em>Peces del bajo Río Uruguay – Especies destacadas</em> (Serra et al., 2019, CARU reissue 2023; our translation): Plata basin, Laguna de los Patos and the Chaparé and Mamoré rivers in the upper Amazon region (Argentina, Bolivia, Brazil, Paraguay and Uruguay), and “a frequent species in the lower Uruguay River”. It adds that in its spawning migrations between the Paraná and Uruguay rivers it can cover more than 800 km, at about 20 km a day.',
			'The sources differ at the edges: the IGFA text on the species (read 13 November 2026) still lists the São Francisco and Magdalena systems, which neither FishBase nor CARU include. We publish only what those two sources say and do not add other basins without a source.',
			'<strong>Conservation:</strong> FishBase reports the IUCN Red List category “Least Concern”, assessed on 7 October 2020 (FishBase mirror, IUCN version 2025-2; we could not read the IUCN page itself). National listings are different: CARU records, for example, “Vulnerable” for Brazil in the Uruguay River.',
		],
		'faq_q' => 'Where does the golden dorado live?',
		'faq_a' => 'In the Paraná, Paraguay and Uruguay river basins (FishBase) or, according to CARU, in the Plata basin, the Laguna de los Patos and the Chaparé and Mamoré rivers, across Argentina, Bolivia, Brazil, Paraguay and Uruguay; it is frequent in the lower Uruguay River. The sources differ at the edges (the IGFA still lists the São Francisco and Magdalena) and we publish only what FishBase and CARU agree on.',
		'rx_pend' => '/PENDIENTE: typical size and biological maximum/u',
		'pend' => 'typical size and weight of the species from a scientific source that measures them: the ranges above come from sport fishing and general literature. The maximum now has a source (FishBase and CARU). Local data goes in each destination guide.',
		'seo_description' => 'Golden dorado (Salminus brasiliensis): IGFA record of 25.28 kg, where it lives, how big it gets and the catch-and-release rule in Corrientes.',
		'rx_reglas' => '/Rules for fishing it/u',
		'fuentes' => [
			[ 'FishBase: Salminus brasiliensis, species page (distribution, maximum size, IUCN; read 13/11/2026)', $u_fb ],
			[ 'FishBase: reference Britski, Silimon and Lopes (1999), Peixes do Pantanal (maximum length of 100 cm)', $u_brit ],
			[ 'FishBase: reference Machacek (2007), World Records Freshwater Fishing (highest published weight of 31.4 kg; a listing, not certified)', $u_mach ],
			[ 'CARU: Serra et al. (2019, reissue 2023), Peces del bajo Río Uruguay – Especies destacadas (distribution, migration)', $u_caru ],
			[ 'IGFA: game fish database, dorado (all-tackle record of 25.28 kg, weight only; read 13/11/2026)', $u_igfa ],
		],
	],
	'pt' => [
		'rx_datos' => '#<li><strong>Recorde mundial \(IGFA, qualquer equipamento\):</strong>.*?</li>#su',
		'datos' => '<li><strong>Recorde mundial (IGFA, qualquer equipamento):</strong> 25,28 kg (55 libras e 11 onças). A IGFA publica só o peso; o rio, o ano e o pescador (Andre De Button, rio Uruguai, 2006) são da imprensa (Diario Uno, consultado em 24/10/2026).</li>',
		'record' => 'O recorde mundial do dourado na categoria <em>all-tackle</em> da IGFA, a International Game Fish Association, é de <strong>25,28 kg (55 libras e 11 onças)</strong>. É só o que a página da IGFA mostra (lida em 13/11/2026): não traz rio, ano nem pescador. Segundo a imprensa (Diario Uno, consultado em 24/10/2026), foi pescado por Andre De Button em 2006 no rio Uruguai, na fronteira entre Argentina e Uruguai. A IGFA certifica seus recordes com verificação da linha e do terminal, pesagem em balança certificada, fotografias e declaração de testemunhas.',
		'faq_record' => 'O recorde mundial do dourado na categoria todo equipamento (all-tackle) da IGFA é de 25,28 kg (55 libras e 11 onças); a página da IGFA mostra só o peso. Segundo a imprensa (Diario Uno, consultado em 24/10/2026), foi pescado por Andre De Button em 2006 no rio Uruguai, na fronteira entre a Argentina e o Uruguai.',
		'faq_tam_extra' => ' Como máximos, a FishBase dá 100 cm de comprimento padrão e a CARU «cerca de 1 m»; o maior peso publicado é 31,4 kg (FishBase, a partir de um site que lista recordes, não um recorde certificado).',
		'max' => 'Os máximos são outra coisa que o tamanho habitual. <strong>Comprimento máximo:</strong> 100 cm de comprimento padrão, segundo a FishBase (referência: Britski et al., 1999, <em>Peixes do Pantanal</em>); a CARU diz «cerca de 1 m de comprimento padrão» (Serra et al., 2019, reedição CARU 2023, <em>Peces del bajo Río Uruguay – Especies destacadas</em>, tradução nossa). <strong>Maior peso publicado:</strong> 31,4 kg, segundo a FishBase (referência: Machacek, 2007, um site que lista recordes de pesca: não é um recorde certificado). É mais que o recorde da IGFA (25,28 kg); não são comparáveis e não os combinamos, porque um vem de uma lista sem certificação e o outro de uma federação que verifica seus recordes. Fontes lidas em 13/11/2026.',
		'h2' => 'Onde vive o dourado',
		'donde' => [
			'Segundo a FishBase (página da espécie, lida em 13/11/2026), o dourado vive na «América do Sul: bacias dos rios Paraná, Paraguai e Uruguai; drenagem da Lagoa dos Patos, alto Chaparé e bacia do rio Mamoré, na Bolívia. A ocorrência no restante do Amazonas é muito duvidosa» (tradução nossa do original em inglês).',
			'A CARU, a Comissão Administradora do Rio Uruguai, diz em <em>Peces del bajo Río Uruguay – Especies destacadas</em> (Serra et al., 2019, reedição CARU 2023; tradução nossa): bacias do Rio da Prata, Lagoa dos Patos e rios Chaparé e Mamoré, na região alta do rio Amazonas (Argentina, Bolívia, Brasil, Paraguai e Uruguai), e «espécie frequente no baixo rio Uruguai». Acrescenta que, em suas migrações reprodutivas entre os rios Paraná e Uruguai, pode percorrer mais de 800 km, a cerca de 20 km por dia.',
			'As fontes divergem nas bordas: o texto da IGFA sobre a espécie (lido em 13/11/2026) ainda menciona os sistemas do São Francisco e do Magdalena, que nem a FishBase nem a CARU incluem. Publicamos só o que essas duas fontes dizem e não somamos outras bacias sem fonte.',
			'<strong>Conservação:</strong> a FishBase informa a categoria «Pouco preocupante» (<em>Least Concern</em>) da Lista Vermelha da UICN, avaliada em 7 de outubro de 2020 (espelho da FishBase, versão 2025-2 da UICN; não conseguimos ler a página da UICN). As listas nacionais são outras: a CARU registra, por exemplo, «Vulnerável» para o Brasil no rio Uruguai.',
		],
		'faq_q' => 'Onde vive o dourado?',
		'faq_a' => 'Nas bacias dos rios Paraná, Paraguai e Uruguai (FishBase) ou, segundo a CARU, nas bacias do Rio da Prata, da Lagoa dos Patos e dos rios Chaparé e Mamoré, na Argentina, Bolívia, Brasil, Paraguai e Uruguai; é frequente no baixo rio Uruguai. As fontes divergem nas bordas (a IGFA ainda menciona o São Francisco e o Magdalena) e publicamos só o que coincide na FishBase e na CARU.',
		'rx_pend' => '/PENDIENTE: tamanho e peso típicos e máximo/u',
		'pend' => 'tamanho e peso típicos da espécie com uma fonte científica que os meça: as faixas acima vêm da pesca esportiva e da bibliografia geral. O máximo já tem fonte (FishBase e CARU). O local vai no guia de cada destino.',
		'seo_description' => 'Dourado (Salminus brasiliensis): recorde IGFA de 25,28 kg, onde vive, quanto mede e pesa e a regra de pesque e solte em Corrientes.',
		'rx_reglas' => '/Regras para pescá-lo/u',
		'fuentes' => [
			[ 'FishBase: Salminus brasiliensis, página da espécie (distribuição, tamanho máximo, UICN; lida em 13/11/2026)', $u_fb ],
			[ 'FishBase: referência Britski, Silimon e Lopes (1999), Peixes do Pantanal (comprimento máximo de 100 cm)', $u_brit ],
			[ 'FishBase: referência Machacek (2007), World Records Freshwater Fishing (maior peso publicado de 31,4 kg; lista, não certificado)', $u_mach ],
			[ 'CARU: Serra et al. (2019, reedição 2023), Peces del bajo Río Uruguay – Especies destacadas (distribuição, migração)', $u_caru ],
			[ 'IGFA: base de dados de peixes de pesca esportiva, dourado (recorde todo equipamento de 25,28 kg, só o peso; lida em 13/11/2026)', $u_igfa ],
		],
	],
];

foreach ( $t as $i => $x ) {
	$p = pm_buscar( 'pm_especie', 'dorado', $i );
	if ( ! $p ) {
		WP_CLI::warning( "dorado ($i): no existe, se salta." );
		continue;
	}
	$c     = $p->post_content;
	$nuevo = $c;
	$tareas = [];

	// 1. Key facts line.
	if ( preg_match( $x['rx_datos'], $nuevo, $m ) && $m[0] !== $x['datos'] ) {
		$nuevo = preg_replace_callback( $x['rx_datos'], fn() => $x['datos'], $nuevo, 1 );
		$tareas[] = 'datos clave: récord = solo peso IGFA, resto de la prensa';
	} elseif ( ! preg_match( $x['rx_datos'], $nuevo ) ) {
		WP_CLI::warning( "dorado ($i): no encontré la línea del récord en los datos clave." );
	}
	// 2. Record paragraph (section) and FAQ answer: paragraphs that name De Button.
	$hechos = [ 'sec' => false, 'faq' => false ];
	$nuevo = preg_replace_callback( '#(<!-- wp:paragraph[^>]*-->\s*<p[^>]*>)(.*?)(</p>\s*<!-- /wp:paragraph -->)#su', function ( $m ) use ( $x, &$hechos ) {
		$txt = wp_strip_all_tags( $m[2] );
		if ( false === strpos( $txt, 'De Button' ) || false === strpos( $txt, 'IGFA' ) ) {
			return $m[0];
		}
		if ( preg_match( '/certific/iu', $txt ) ) {
			$hechos['sec'] = true;
			return $m[1] . $x['record'] . $m[3];
		}
		$hechos['faq'] = true;
		return $m[1] . $x['faq_record'] . $m[3];
	}, $nuevo );
	$hechos['sec'] && $nuevo !== $c && $tareas[] = 'párrafo del récord: peso IGFA, resto «según la prensa»';
	$hechos['faq'] && $tareas[] = 'FAQ del récord reescrita';
	if ( ! $hechos['sec'] || ! $hechos['faq'] ) {
		WP_CLI::warning( "dorado ($i): párrafo del récord " . ( $hechos['sec'] ? 'ok' : 'NO encontrado' ) . ', FAQ ' . ( $hechos['faq'] ? 'ok' : 'NO encontrada' ) );
	}
	// 3. Max size + "where it lives" before the rules heading.
	if ( false === strpos( $nuevo, $x['h2'] ) ) {
		$pos = pm_sc_pos_encabezado( $nuevo, $x['rx_reglas'] );
		if ( null === $pos ) {
			WP_CLI::warning( "dorado ($i): no encontré el H2 de reglas; no agrego «{$x['h2']}»." );
		} else {
			$bloque = pm_sc_p( $x['max'] ) . pm_sc_h2( $x['h2'] );
			foreach ( $x['donde'] as $par ) {
				$bloque .= pm_sc_p( $par );
			}
			$nuevo = pm_sc_insertar( $nuevo, $pos, $bloque );
			$tareas[] = "máximos + H2 «{$x['h2']}» (FishBase, CARU, IGFA, UICN)";
		}
	}
	// 4. FAQ: the new question, and the maximums appended to the size answer.
	if ( false === strpos( $nuevo, $x['faq_q'] ) ) {
		$n = 0;
		$nuevo = preg_replace_callback( '#(<div class="wp-block-group pm-faq">.*?)(</div>\s*<!-- /wp:group -->)#su', function ( $m ) use ( $x, &$n ) {
			$n++;
			return $m[1] . "\n\n" . rtrim( pm_dr_faq_item( $x['faq_q'], $x['faq_a'] ) ) . $m[2];
		}, $nuevo, 1 );
		$n ? $tareas[] = "FAQ «{$x['faq_q']}»" : WP_CLI::warning( "dorado ($i): no encontré el grupo pm-faq." );
	}
	if ( false === strpos( $nuevo, '31,4' ) && false === strpos( $nuevo, '31.4' ) ) {
		WP_CLI::warning( "dorado ($i): el peso máximo no quedó en el texto." );
	}
	if ( false === strpos( $nuevo, trim( $x['faq_tam_extra'] ) ) ) {
		$nuevo = preg_replace_callback( '#(<h3[^>]*>[^<]*(?:mide y pesa|big does|mede e pesa)[^<]*</h3>\s*<!-- /wp:heading -->\s*<!-- wp:paragraph -->\s*<p>)(.*?)(</p>)#su', fn( $m ) => $m[1] . $m[2] . $x['faq_tam_extra'] . $m[3], $nuevo, 1, $k );
		$k ? $tareas[] = 'FAQ de tamaño: máximos' : WP_CLI::warning( "dorado ($i): no encontré la FAQ de tamaño." );
	}
	// 5. Hidden pending marker (size and maximum "needs a source").
	$nuevo = preg_replace_callback( '#<!-- wp:paragraph -->\s*<p>PENDIENTE: [^<]*</p>\s*<!-- /wp:paragraph -->#su', function ( $m ) use ( $x, &$tareas ) {
		if ( ! preg_match( $x['rx_pend'], $m[0] ) ) {
			return $m[0];
		}
		$tareas[] = 'marca PENDIENTE de tamaño máximo reducida a lo que falta';
		return pm_sc_p( 'PENDIENTE: ' . $x['pend'] ) ;
	}, $nuevo );

	// Fields: description for Google, sources, date.
	$campos_ok = true;
	$desc_ahora = (string) pm_campo( 'seo_description', $p->ID );
	$cambia_desc = $desc_ahora !== $x['seo_description'];
	$src_ahora = (array) pm_campo( 'fuentes', $p->ID );
	$urls_ahora = array_column( array_filter( $src_ahora, 'is_array' ), 'url' );
	$faltan_src = array_values( array_filter( $x['fuentes'], fn( $s ) => ! in_array( $s[1], $urls_ahora, true ) ) );
	if ( mb_strlen( $x['seo_description'] ) > 160 ) {
		WP_CLI::error( "dorado ($i): seo_description de " . mb_strlen( $x['seo_description'] ) . ' car.' );
	}
	$cambia_desc && $tareas[] = 'seo_description: «' . $desc_ahora . '» → «' . $x['seo_description'] . '» (' . mb_strlen( $x['seo_description'] ) . ')';
	$faltan_src && $tareas[] = count( $faltan_src ) . ' fuentes nuevas';

	if ( ! $tareas && ! $cambia_desc && ! $faltan_src && $nuevo === $c ) {
		WP_CLI::log( "dorado ($i) #{$p->ID}: ya está." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": dorado ($i) #{$p->ID}: " . implode( ' · ', $tareas ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	if ( $nuevo !== $c ) {
		pm_sc_guardar( $p->ID, $nuevo, [ $x['h2'], $x['faq_q'], 'Diario Uno' ], "dorado ($i)" );
	}
	if ( $cambia_desc ) {
		pm_sc_campo( $p->ID, 'seo_description', $x['seo_description'], "dorado ($i)" );
	}
	if ( $faltan_src ) {
		$filas = array_map( function ( $f ) {
			$f['consultada'] = isset( $f['consultada'] ) ? str_replace( '-', '', (string) $f['consultada'] ) : '';
			return $f;
		}, array_values( array_filter( $src_ahora, 'is_array' ) ) );
		foreach ( $faltan_src as $s ) {
			$filas[] = [ 'nombre' => $s[0], 'url' => $s[1], 'oficial' => 0, 'consultada' => $hoy ];
		}
		update_field( 'fuentes', $filas, $p->ID );
		count( (array) pm_campo( 'fuentes', $p->ID ) ) === count( $filas ) || WP_CLI::error( "dorado ($i): las fuentes no quedaron." );
	}
	update_field( 'actualizado', $hoy, $p->ID );
	$leido = get_post( $p->ID )->post_content;
	$ok = false !== strpos( $leido, $x['h2'] ) && false !== strpos( $leido, $x['faq_q'] ) && false === strpos( $leido, 'Andre De Button en 2006 en el río Uruguay, en la frontera entre Argentina y Uruguay. La IGFA' )
		&& ( false !== strpos( $leido, 'prensa' ) || false !== strpos( $leido, 'press' ) || false !== strpos( $leido, 'imprensa' ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": dorado ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' — H2, FAQ y atribución leídos de vuelta' );
	$ok || WP_CLI::error( "dorado ($i): la lectura de vuelta no coincide." );
}

// --- Pillar note: the record sentence of "Uruguay River: Concordia and Salto Grande" ------------------------------
$pil = [
	'es' => [
		'viejo' => 'De este río es el récord mundial del dorado: el récord todo equipo de la IGFA, 25,28 kg (55 libras y 11 onzas), lo pescó Andre De Button en 2006 en el río Uruguay, en la frontera entre Argentina y Uruguay (dato publicado por Diario Uno, consultado el 24/10/2026;',
		'nuevo' => 'La IGFA registra 25,28 kg (55 libras y 11 onzas) como récord mundial todo equipo del dorado, y la ficha de la IGFA muestra solo el peso. Según la prensa (Diario Uno, consultado el 24/10/2026), lo pescó Andre De Button en 2006 en este río, en la frontera entre Argentina y Uruguay (está en',
	],
	'en' => [
		'viejo' => 'The golden dorado world record comes from this river: the IGFA all-tackle record, 25.28 kg (55 lb 11 oz), was caught by Andre De Button in 2006 in the Uruguay River, on the border between Argentina and Uruguay (figure reported by Diario Uno, consulted 24 October 2026;',
		'nuevo' => 'The IGFA records 25.28 kg (55 lb 11 oz) as the golden dorado all-tackle world record, and the IGFA page shows only the weight. According to the press (Diario Uno, consulted 24 October 2026), Andre De Button caught it in 2006 in this river, on the border between Argentina and Uruguay (it is on',
	],
	'pt' => [
		'viejo' => 'Deste rio é o recorde mundial do dourado: o recorde todo equipamento da IGFA, 25,28 kg (55 libras e 11 onças), foi pescado por Andre De Button em 2006 no rio Uruguai, na fronteira entre a Argentina e o Uruguai (dado publicado pelo Diario Uno, consultado em 24/10/2026;',
		'nuevo' => 'A IGFA registra 25,28 kg (55 libras e 11 onças) como recorde mundial todo equipamento do dourado, e a página da IGFA mostra só o peso. Segundo a imprensa (Diario Uno, consultado em 24/10/2026), foi pescado por Andre De Button em 2006 neste rio, na fronteira entre a Argentina e o Uruguai (está em',
	],
];
foreach ( $pil as $i => $x ) {
	$p = pm_buscar( 'pm_nota', 'golden-dorado-guia', $i );
	if ( ! $p ) {
		WP_CLI::warning( "pilar dorado ($i): no existe." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, $x['nuevo'] ) ) {
		WP_CLI::log( "pilar dorado ($i) #{$p->ID}: la atribución ya está corregida." );
		continue;
	}
	if ( false === strpos( $c, $x['viejo'] ) ) {
		WP_CLI::warning( "pilar dorado ($i) #{$p->ID}: no encontré la frase del récord tal cual; " . ( false !== strpos( $c, 'De Button' ) ? 'menciona a De Button: revisar a mano' : 'no menciona a De Button' ) . '.' );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": pilar dorado ($i) #{$p->ID} " . get_permalink( $p->ID ) . ': frase del récord reescrita (peso IGFA, resto «según la prensa»)' );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, str_replace( $x['viejo'], $x['nuevo'], $c ), [ $x['nuevo'] ], "pilar dorado ($i)" );
		WP_CLI::log( "VERIFIED: pilar dorado ($i) #{$p->ID}" );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
