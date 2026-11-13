<?php
/**
 * Sizes and records, 13/11/2026 (research folders fuentes-2-1-20261113-2207, fuentes-2-2-20261113-2209, fuentes-2-3-20261113-2217).
 * Owner policy 13/11/2026: descriptive facts that several independent reports agree on and whose underlying source is named are published as
 * attributed ("según X"), without a caveat; regulations, prices and permits still need a primary source.
 *  - Surubí species page (es/en/pt): "Tamaño y récord" section, key-facts line and FAQ "¿Cuánto pesa un surubí?": CARU guide (175 cm, 80 kg),
 *    FishBase maximums (182 cm TL, 100 kg; Godinho et al. 1997 and Tavares 1997, both IBAMA), Balboni, Vargas and Colautti 2021 (commercial
 *    landings, Puerto Antequera; modal lengths 1,000 and 1,300 mm, no range and no mean; the regulation they cite, 1,000-1,200 mm), IBAMA 2009
 *    records guide (pintado 53.5 kg, Paraná River, Corrientes, 16/02/2000, João Neto; the guide does not say IGFA, so neither do we).
 *  - Trout species page (es/en/pt): "Tamaños" section replacing the sentence "we do not publish sizes": Baigún 2001 (experimental gill nets,
 *    tables 4.1.4 to 4.1.6; net samples, not angler catches), Giese (abstract: Gallegos adults longer, no weight difference; the adult means that
 *    "several summaries" report are NOT in any page we could open, so they are not published), reglamentodepesca.org.ar (quoted, publisher not
 *    named), IGFA all-tackle records shown apart; FAQ "¿Cuánto pesan las truchas de la Patagonia?".
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = surubi-trucha-tamanos-2026-11-13).
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

$u = [
	'fb_sum'   => 'https://www.fishbase.se/summary/Pseudoplatystoma-corruscans',
	'fb_g'     => 'https://www.fishbase.se/references/FBRefSummary.php?ID=26333',
	'fb_t'     => 'https://www.fishbase.se/references/FBRefSummary.php?ID=26762',
	'caru'     => 'https://www.caru.org.uy/web/pdfs_publicaciones/Peces-del-Rio-Uruguay.pdf',
	'balboni'  => 'https://www.ni.bio.br/1982-0224-2020-0101/',
	'ibama'    => 'https://www.gov.br/ibama/pt-br/phocadownload/biodiversidade/biodiversidade-aquatica/gestao-pesqueira/publicacoes/2009-guia-de-recordes-peixes-de-agua-doce-2009-parte2.pdf',
	'baigun'   => 'https://bibliotecadigital.exactas.uba.ar/collection/tesis/document/tesis_n3376_Baigun',
	'giese'    => 'https://rdi.uncoma.edu.ar/handle/uncomaid/18463',
	'regl'     => 'https://reglamentodepesca.org.ar/peces-de-la-patagonia/',
	'igfa_rb'  => 'https://igfa.org/member-services/world-record/common-name/Trout,%20rainbow',
	'igfa_br'  => 'https://igfa.org/member-services/world-record/common-name/Trout,%20brown',
	'igfa_bk'  => 'https://igfa.org/member-services/world-record/common-name/Trout,%20brook',
];

/** One question of a pm-faq group, as the blocks pm_sc_faq() writes. */
function pm_b_faq_item( string $q, string $a ): string {
	return "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
}
function pm_b_fuentes( int $id, array $quiero, string $hoy ): ?array {
	$filas = array_map( function ( $f ) {
		$f['consultada'] = isset( $f['consultada'] ) ? str_replace( '-', '', (string) $f['consultada'] ) : '';
		return $f;
	}, array_values( array_filter( pm_filas( 'fuentes', $id ), 'is_array' ) ) );
	$urls  = array_column( $filas, 'url' );
	$nuevo = false;
	foreach ( $quiero as [ $nombre, $url, $oficial ] ) {
		if ( ! in_array( $url, $urls, true ) ) {
			$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $oficial ? 1 : 0, 'consultada' => $hoy ];
			$nuevo   = true;
		}
	}
	return $nuevo ? $filas : null;
}

// --- Surubí ------------------------------------------------------------------------------------------------------------
$S = [
	'es' => [
		'h2' => 'Tamaño y récord del surubí',
		'datos_marca' => '<strong>Nombre científico:</strong>',
		'datos_li' => '<li><strong>Tamaño:</strong> la CARU registra surubíes pintados medidos de alrededor de 175 cm y 80 kg; los máximos publicados por FishBase son 182 cm y 100 kg. Récord registrado: 53,5 kg, río Paraná, Corrientes, 16/2/2000 (guía de récords del IBAMA, 2009).</li>',
		'parrafos' => [
			'Según la CARU (Comisión Administradora del Río Uruguay), el surubí pintado (<em>Pseudoplatystoma corruscans</em>) es «el pez de mayor longitud de los ríos de la Cuenca del Plata»: «se han medido ejemplares de alrededor de 175 cm de longitud y 80 kg de peso; pero hay referencias de ejemplares mucho mayores. Las hembras son de mayor tamaño que los machos» (guía ilustrada de las especies más comunes del río Uruguay inferior y el embalse de Salto Grande, texto de Sara Beatriz Sverlij).',
			'<strong>Máximos publicados.</strong> FishBase da 182 cm de longitud total y un peso máximo publicado de 100 kg, a partir de dos estudios brasileños publicados por el IBAMA: Godinho et al. (1997, sobre el río São Francisco) y Tavares (1997). Son máximos de la bibliografía, no el tamaño de un pez habitual.',
			'<strong>Desembarques comerciales.</strong> Balboni, Vargas y Colautti (2021, <em>Neotropical Ichthyology</em> 19(2): e200101) midieron 1.192 surubíes pintados desembarcados en Puerto Antequera (Chaco), en la confluencia de los ríos Paraná y Paraguay, capturados por la pesca comercial con redes a la deriva entre febrero de 2010 y febrero de 2012. La distribución de tamaños fue bimodal, con modas de 1.000 y 1.300 mm de longitud total. Son datos de la pesca comercial, no de pesca deportiva. El artículo menciona, como la norma vigente, una talla de captura «mínimo de 1.000 mm y máximo de 1.200 mm» y discute que esa regla extrae más peces de un sexo que del otro; no es la regla de esta página: en Corrientes el surubí es solo pesca y devolución.',
			'<strong>Récord registrado.</strong> La guía de récords de peces de agua dulce del IBAMA (2009) trae, para el pintado, un récord de <strong>53,5 kg (117 libras y 15 onzas)</strong>, pescado en el río Paraná, en Corrientes, Argentina, el 16 de febrero de 2000, por João Neto. Es el dato de esa guía; la guía no dice qué organismo lo certificó. Los máximos de la bibliografía (100 kg) y el récord registrado (53,5 kg) no son comparables y no los combinamos.',
		],
		'faq_q' => '¿Cuánto pesa un surubí?',
		'faq_a' => 'Según la CARU, se han medido surubíes pintados de alrededor de 175 cm y 80 kg, con referencias de ejemplares mucho mayores; FishBase da como máximos publicados 182 cm y 100 kg. El récord registrado en la guía del IBAMA (2009) es de 53,5 kg, en el río Paraná, Corrientes (16/2/2000). En los desembarques comerciales de Puerto Antequera (Chaco, 2010 a 2012) los tamaños más frecuentes fueron 1.000 y 1.300 mm de longitud total (Balboni et al., 2021). Son máximos y datos de la pesca comercial: no tenemos un peso «habitual» de pesca deportiva con fuente.',
		'rx_pend' => '#PENDIENTE: ambiente, comportamiento estacional, técnicas, tamaño y peso típicos y récord, con fuente\.#u',
		'pend' => 'PENDIENTE: ambiente, comportamiento estacional, técnicas y tamaño habitual en la pesca deportiva, con fuente (lo publicado son máximos, desembarques comerciales y un récord).',
		'fuentes' => [
			[ 'FishBase: Pseudoplatystoma corruscans, página de la especie (longitud máxima 182 cm y peso máximo publicado 100 kg; leída el 13/11/2026)', 'fb_sum', 0 ],
			[ 'FishBase: referencia Godinho et al. (1997), Pesca e biologia do surubim no rio São Francisco (IBAMA; longitud máxima)', 'fb_g', 0 ],
			[ 'FishBase: referencia Tavares (1997), O surubim (IBAMA; peso máximo publicado)', 'fb_t', 0 ],
			[ 'CARU: guía ilustrada de las especies más comunes del río Uruguay inferior y el embalse de Salto Grande (surubí pintado: 175 cm y 80 kg; PDF leído el 13/11/2026)', 'caru', 1 ],
			[ 'Balboni, Vargas y Colautti (2021), Neotropical Ichthyology 19(2): e200101: edad y crecimiento del surubí pintado en Puerto Antequera, Chaco', 'balboni', 0 ],
			[ 'IBAMA (2009): Guia de Recordes, Peixes de Água Doce, parte 2 (pintado: 53,5 kg, río Paraná, Corrientes, 16/2/2000; PDF leído el 13/11/2026)', 'ibama', 1 ],
		],
	],
	'en' => [
		'h2' => 'Surubí size and record',
		'datos_marca' => '<strong>Scientific name:</strong>',
		'datos_li' => '<li><strong>Size:</strong> CARU records spotted surubí measured at around 175 cm and 80 kg; the maximums published by FishBase are 182 cm and 100 kg. Recorded record: 53.5 kg, Paraná River, Corrientes, 16/2/2000 (IBAMA records guide, 2009).</li>',
		'parrafos' => [
			'According to CARU (the Uruguay River Administrative Commission), the spotted surubí (<em>Pseudoplatystoma corruscans</em>) is “the longest fish in the rivers of the Plata basin”: “specimens of around 175 cm in length and 80 kg in weight have been measured; but there are references to much larger fish. Females are larger than males” (illustrated guide to the most common species of the lower Uruguay River and the Salto Grande reservoir, text by Sara Beatriz Sverlij; our translation).',
			'<strong>Published maximums.</strong> FishBase gives 182 cm total length and a maximum published weight of 100 kg, from two Brazilian studies published by IBAMA: Godinho et al. (1997, on the São Francisco River) and Tavares (1997). They are maximums from the literature, not the size of a usual fish.',
			'<strong>Commercial landings.</strong> Balboni, Vargas and Colautti (2021, <em>Neotropical Ichthyology</em> 19(2): e200101) measured 1,192 spotted surubí landed at Puerto Antequera (Chaco), at the confluence of the Paraná and Paraguay rivers, caught by commercial fishing with drifting nets between February 2010 and February 2012. The size distribution was bimodal, with modal total lengths of 1,000 and 1,300 mm. These are commercial-landing data, not sport-fishing data. The paper mentions, as the regulation in force, a catch size of “a minimum of 1,000 mm and maximum of 1,200 mm” and argues that this rule removes more fish of one sex than of the other; it is not the rule on this page: in Corrientes the surubí is catch and release only.',
			'<strong>Recorded record.</strong> The IBAMA records guide to freshwater fish (2009) lists, for the pintado, a record of <strong>53.5 kg (117 lb 15 oz)</strong>, caught in the Paraná River, in Corrientes, Argentina, on 16 February 2000 by João Neto. That is the guide\'s figure; the guide does not say which body certified it. The maximums from the literature (100 kg) and the recorded record (53.5 kg) are not comparable and we do not merge them.',
		],
		'faq_q' => 'How much does a surubí weigh?',
		'faq_a' => 'According to CARU, spotted surubí of around 175 cm and 80 kg have been measured, with references to much larger fish; FishBase gives 182 cm and 100 kg as published maximums. The record in the IBAMA guide (2009) is 53.5 kg, from the Paraná River, Corrientes (16/2/2000). In the commercial landings at Puerto Antequera (Chaco, 2010 to 2012) the most frequent sizes were 1,000 and 1,300 mm total length (Balboni et al., 2021). These are maximums and commercial-fishing data: we have no sourced “usual” sport-fishing weight.',
		'rx_pend' => '#PENDIENTE: habitat, seasonal behaviour, techniques, typical size and weight and record, with sources\.#u',
		'pend' => 'PENDIENTE: habitat, seasonal behaviour, techniques and usual size in sport fishing, with sources (what is published are maximums, commercial landings and one record).',
		'fuentes' => [
			[ 'FishBase: Pseudoplatystoma corruscans, species page (maximum length 182 cm and maximum published weight 100 kg; read 13/11/2026)', 'fb_sum', 0 ],
			[ 'FishBase: reference Godinho et al. (1997), Pesca e biologia do surubim no rio São Francisco (IBAMA; maximum length)', 'fb_g', 0 ],
			[ 'FishBase: reference Tavares (1997), O surubim (IBAMA; maximum published weight)', 'fb_t', 0 ],
			[ 'CARU: illustrated guide to the most common species of the lower Uruguay River and the Salto Grande reservoir (spotted surubí: 175 cm and 80 kg; PDF read 13/11/2026)', 'caru', 1 ],
			[ 'Balboni, Vargas and Colautti (2021), Neotropical Ichthyology 19(2): e200101: age and growth of the spotted surubí at Puerto Antequera, Chaco', 'balboni', 0 ],
			[ 'IBAMA (2009): Guia de Recordes, Peixes de Água Doce, part 2 (pintado: 53.5 kg, Paraná River, Corrientes, 16/2/2000; PDF read 13/11/2026)', 'ibama', 1 ],
		],
	],
	'pt' => [
		'h2' => 'Tamanho e recorde do surubim',
		'datos_marca' => '<strong>Nome científico:</strong>',
		'datos_li' => '<li><strong>Tamanho:</strong> a CARU registra surubins pintados medidos de cerca de 175 cm e 80 kg; os máximos publicados pela FishBase são 182 cm e 100 kg. Recorde registrado: 53,5 kg, rio Paraná, Corrientes, 16/2/2000 (guia de recordes do IBAMA, 2009).</li>',
		'parrafos' => [
			'Segundo a CARU (Comissão Administradora do Rio Uruguai), o surubim pintado (<em>Pseudoplatystoma corruscans</em>) é «o peixe de maior comprimento dos rios da Bacia do Prata»: «foram medidos exemplares de cerca de 175 cm de comprimento e 80 kg de peso; mas há referências de exemplares muito maiores. As fêmeas são maiores que os machos» (guia ilustrado das espécies mais comuns do rio Uruguai inferior e do reservatório de Salto Grande, texto de Sara Beatriz Sverlij; tradução nossa).',
			'<strong>Máximos publicados.</strong> A FishBase dá 182 cm de comprimento total e um peso máximo publicado de 100 kg, a partir de dois estudos brasileiros publicados pelo IBAMA: Godinho et al. (1997, sobre o rio São Francisco) e Tavares (1997). São máximos da bibliografia, não o tamanho de um peixe comum.',
			'<strong>Desembarques comerciais.</strong> Balboni, Vargas e Colautti (2021, <em>Neotropical Ichthyology</em> 19(2): e200101) mediram 1.192 surubins pintados desembarcados em Puerto Antequera (Chaco), na confluência dos rios Paraná e Paraguai, capturados pela pesca comercial com redes à deriva entre fevereiro de 2010 e fevereiro de 2012. A distribuição de tamanhos foi bimodal, com modas de 1.000 e 1.300 mm de comprimento total. São dados da pesca comercial, não da pesca esportiva. O artigo menciona, como norma vigente, um tamanho de captura «mínimo de 1.000 mm e máximo de 1.200 mm» e discute que essa regra retira mais peixes de um sexo que do outro; não é a regra desta página: em Corrientes o surubim é só pesque e solte.',
			'<strong>Recorde registrado.</strong> O guia de recordes de peixes de água doce do IBAMA (2009) traz, para o pintado, um recorde de <strong>53,5 kg (117 libras e 15 onças)</strong>, pescado no rio Paraná, em Corrientes, Argentina, em 16 de fevereiro de 2000, por João Neto. É o dado desse guia; o guia não diz qual entidade o certificou. Os máximos da bibliografia (100 kg) e o recorde registrado (53,5 kg) não são comparáveis e não os combinamos.',
		],
		'faq_q' => 'Quanto pesa um surubim?',
		'faq_a' => 'Segundo a CARU, foram medidos surubins pintados de cerca de 175 cm e 80 kg, com referências de exemplares muito maiores; a FishBase dá como máximos publicados 182 cm e 100 kg. O recorde registrado no guia do IBAMA (2009) é de 53,5 kg, no rio Paraná, Corrientes (16/2/2000). Nos desembarques comerciais de Puerto Antequera (Chaco, 2010 a 2012) os tamanhos mais frequentes foram 1.000 e 1.300 mm de comprimento total (Balboni et al., 2021). São máximos e dados da pesca comercial: não temos um peso «comum» da pesca esportiva com fonte.',
		'rx_pend' => '#PENDIENTE: ambiente, comportamento sazonal, técnicas, tamanho e peso típicos e recorde, com fonte\.#u',
		'pend' => 'PENDIENTE: ambiente, comportamento sazonal, técnicas e tamanho comum na pesca esportiva, com fonte (o publicado são máximos, desembarques comerciais e um recorde).',
		'fuentes' => [
			[ 'FishBase: Pseudoplatystoma corruscans, página da espécie (comprimento máximo 182 cm e peso máximo publicado 100 kg; lida em 13/11/2026)', 'fb_sum', 0 ],
			[ 'FishBase: referência Godinho et al. (1997), Pesca e biologia do surubim no rio São Francisco (IBAMA; comprimento máximo)', 'fb_g', 0 ],
			[ 'FishBase: referência Tavares (1997), O surubim (IBAMA; peso máximo publicado)', 'fb_t', 0 ],
			[ 'CARU: guia ilustrado das espécies mais comuns do rio Uruguai inferior e do reservatório de Salto Grande (surubim pintado: 175 cm e 80 kg; PDF lido em 13/11/2026)', 'caru', 1 ],
			[ 'Balboni, Vargas e Colautti (2021), Neotropical Ichthyology 19(2): e200101: idade e crescimento do surubim pintado em Puerto Antequera, Chaco', 'balboni', 0 ],
			[ 'IBAMA (2009): Guia de Recordes, Peixes de Água Doce, parte 2 (pintado: 53,5 kg, rio Paraná, Corrientes, 16/2/2000; PDF lido em 13/11/2026)', 'ibama', 1 ],
		],
	],
];

// --- Trout -------------------------------------------------------------------------------------------------------------
$R = [
	'es' => [
		'h2' => 'Tamaños de las truchas en la Patagonia',
		'rx_viejo' => 'El tamaño típico y el récord de cada especie no los publicamos hasta tener una fuente leída',
		'intro' => 'Los datos medidos más completos que leímos son de una tesis doctoral de la Universidad de Buenos Aires (Baigún, 2001, <em>Ecología pesquera de lagos y embalses patagónicos</em>, tablas 4.1.4 a 4.1.6), hecha con pesca experimental con baterías de redes de enmalle, una vez por ambiente en verano, entre 1984 y 1987. <strong>Son muestras de red de esos lagos y embalses, no capturas de pescadores</strong>; sirven como orden de magnitud:',
		'lista' => [
			'<strong>Trucha arcoíris:</strong> longitud media por ambiente de 25,5 a 54,3 cm y peso medio de 0,14 a 2,2 kg; el ejemplar más grande de las redes midió 78,6 cm y pesó 6,1 kg.',
			'<strong>Trucha de arroyo (fontinalis):</strong> longitud media de 22,3 a 47,5 cm y peso medio de 0,15 a 1,96 kg; el mayor, 51,5 cm y 2,8 kg.',
			'<strong>Trucha marrón</strong> (siete ambientes; Mascardi figura dos veces): longitud media de 43,6 a 53,4 cm y peso medio de 0,76 a 1,7 kg; el mayor, 66,5 cm y 2,45 kg. En la tabla de la marrón la columna del peso medio está rotulada «(mm)»; por los valores la leemos como gramos.',
		],
		'giese' => '<strong>Trucha marrón de mar.</strong> Una tesis de la Universidad Nacional del Comahue (Giese, Centro Regional Universitario Bariloche, <em>Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral</em>) compara las poblaciones adultas de los ríos Gallegos, Grande e Irigoyen. Según su resumen, el largo de los adultos, al considerar la edad marina y el año de esmoltificación, fue mayor en el río Gallegos que en los ríos fueguinos (Grande e Irigoyen), y no se observaron diferencias de peso entre los tres ríos.',
		'regl' => '<strong>Lo que dice un portal del reglamento.</strong> Según reglamentodepesca.org.ar (página «Peces de la Patagonia»; el sitio no nombra a quien la publica), la trucha marrón «alcanza los mayores portes (Ej. 16,3 Kg en el lago Nahuel Huapi)» y en el río Grande, Tierra del Fuego, «se han homologado 7 récord mundiales» sobre la variedad que migra al mar; de la trucha de arroyo dice que «en ambientes más productivos, es frecuente encontrar portes que superan los 4 Kg».',
		'igfa' => '<strong>Récords de la IGFA (todo equipo), aparte: no son tamaños habituales.</strong> Arcoíris: 21,77 kg (48 libras), Sean Konrad, lago Diefenbaker (Canadá), 5/9/2009. Marrón: 20,10 kg (44 libras y 5 onzas), Seumas Petrie, Ohau Canal, Twizel (Nueva Zelanda), 27/10/2020. De arroyo: 6,57 kg (14 libras y 8 onzas), Dr. J. W. Cook, río Nipigon (Ontario, Canadá), 21/7/1915. Ninguno es de la Patagonia (IGFA, leído el 13/11/2026).',
		'faq_q' => '¿Cuánto pesan las truchas de la Patagonia?',
		'faq_a' => 'En las pescas experimentales con redes de enmalle de Baigún (2001, tesis de la UBA; veranos de 1984 a 1987), los pesos medios por ambiente fueron de 0,14 a 2,2 kg en la arcoíris, de 0,15 a 1,96 kg en la de arroyo y de 0,76 a 1,7 kg en la marrón; los mayores ejemplares de esas redes pesaron 6,1 kg (arcoíris), 2,8 kg (de arroyo) y 2,45 kg (marrón). Son muestras de red, no capturas de pescadores. Según reglamentodepesca.org.ar, la marrón alcanza los mayores portes (16,3 kg en el lago Nahuel Huapi) y la de arroyo supera los 4 kg en ambientes productivos. Los récords de la IGFA (21,77 kg la arcoíris, 20,10 kg la marrón, 6,57 kg la de arroyo) son de Canadá y Nueva Zelanda.',
		'rx_pend' => '#PENDIENTE: tamaño y peso típicos y máximo biológico de cada especie con una fuente de referencia \(FishBase u otra publicación científica\), y los récords de la IGFA por especie\.#u',
		'pend' => 'PENDIENTE: tamaño y peso típicos de la pesca deportiva por especie y por agua con una fuente de referencia (lo publicado son redes experimentales de 1984 a 1987, el texto de un portal y los récords de la IGFA).',
		'fuentes' => [
			[ 'Baigún, C. (2001): Ecología pesquera de lagos y embalses patagónicos (Argentina), tesis doctoral, Universidad de Buenos Aires (tablas 4.1.4 a 4.1.6; leída el 13/11/2026)', 'baigun', 0 ],
			[ 'Giese, A. C.: Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral, tesis, Universidad Nacional del Comahue (resumen leído el 13/11/2026)', 'giese', 0 ],
			[ 'reglamentodepesca.org.ar: Peces de la Patagonia (leída el 13/11/2026; el sitio no nombra a su editor)', 'regl', 0 ],
			[ 'IGFA: récord mundial todo equipo de la trucha arcoíris (21,77 kg; leído el 13/11/2026)', 'igfa_rb', 0 ],
			[ 'IGFA: récord mundial todo equipo de la trucha marrón (20,10 kg; leído el 13/11/2026)', 'igfa_br', 0 ],
			[ 'IGFA: récord mundial todo equipo de la trucha de arroyo (6,57 kg; leído el 13/11/2026)', 'igfa_bk', 0 ],
		],
	],
	'en' => [
		'h2' => 'How big Patagonian trout get',
		'rx_viejo' => 'We do not publish typical sizes or records for each species until we have read a source',
		'intro' => 'The most complete measured data we read come from a doctoral thesis at the University of Buenos Aires (Baigún, 2001, <em>Ecología pesquera de lagos y embalses patagónicos</em>, tables 4.1.4 to 4.1.6), based on experimental fishing with batteries of gill nets, once per water in summer, between 1984 and 1987. <strong>They are net samples from those lakes and reservoirs, not anglers\' catches</strong>; they give an order of magnitude:',
		'lista' => [
			'<strong>Rainbow trout:</strong> mean length per water from 25.5 to 54.3 cm and mean weight from 0.14 to 2.2 kg; the largest fish in the nets measured 78.6 cm and weighed 6.1 kg.',
			'<strong>Brook trout (fontinalis):</strong> mean length from 22.3 to 47.5 cm and mean weight from 0.15 to 1.96 kg; the largest, 51.5 cm and 2.8 kg.',
			'<strong>Brown trout</strong> (seven waters; Mascardi appears twice): mean length from 43.6 to 53.4 cm and mean weight from 0.76 to 1.7 kg; the largest, 66.5 cm and 2.45 kg. In the brown trout table the mean-weight column is headed “(mm)”; from the values we read it as grams.',
		],
		'giese' => '<strong>Sea-run brown trout.</strong> A thesis from the Universidad Nacional del Comahue (Giese, Centro Regional Universitario Bariloche, <em>Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral</em>) compares the adult populations of the Gallegos, Grande and Irigoyen rivers. According to its abstract, the length of adults, taking marine age and smolt year into account, was greater in the Gallegos than in the Fuegian rivers (Grande and Irigoyen), and no weight differences were observed among the three rivers.',
		'regl' => '<strong>What a regulation portal says.</strong> According to reglamentodepesca.org.ar (page “Peces de la Patagonia”; the site does not name its publisher), the brown trout “reaches the largest sizes (e.g. 16.3 kg in Lake Nahuel Huapi)” and in the Río Grande, Tierra del Fuego, “7 world records have been homologated” for the variety that migrates to the sea; of the brook trout it says that “in more productive waters, sizes above 4 kg are frequent” (our translation of the Spanish original).',
		'igfa' => '<strong>IGFA all-tackle records, shown apart: they are not usual sizes.</strong> Rainbow: 21.77 kg (48 lb), Sean Konrad, Lake Diefenbaker (Canada), 5/9/2009. Brown: 20.10 kg (44 lb 5 oz), Seumas Petrie, Ohau Canal, Twizel (New Zealand), 27/10/2020. Brook: 6.57 kg (14 lb 8 oz), Dr. J. W. Cook, Nipigon River (Ontario, Canada), 21/7/1915. None is from Patagonia (IGFA, read 13/11/2026).',
		'faq_q' => 'How much do Patagonian trout weigh?',
		'faq_a' => 'In Baigún\'s experimental gill-net fishing (2001, UBA thesis; summers 1984 to 1987), mean weights per water were 0.14 to 2.2 kg for rainbow, 0.15 to 1.96 kg for brook and 0.76 to 1.7 kg for brown trout; the largest fish in those nets weighed 6.1 kg (rainbow), 2.8 kg (brook) and 2.45 kg (brown). They are net samples, not anglers\' catches. According to reglamentodepesca.org.ar, the brown trout reaches the largest sizes (16.3 kg in Lake Nahuel Huapi) and the brook trout exceeds 4 kg in productive waters. The IGFA records (21.77 kg rainbow, 20.10 kg brown, 6.57 kg brook) come from Canada and New Zealand.',
		'rx_pend' => '#PENDIENTE: typical size and weight and biological maximum of each species from a reference source \(FishBase or another scientific publication\), and IGFA records by species\.#u',
		'pend' => 'PENDIENTE: typical size and weight in sport fishing by species and by water, from a reference source (what is published is experimental netting of 1984 to 1987, the text of a portal and the IGFA records).',
		'fuentes' => [
			[ 'Baigún, C. (2001): Ecología pesquera de lagos y embalses patagónicos (Argentina), doctoral thesis, Universidad de Buenos Aires (tables 4.1.4 to 4.1.6; read 13/11/2026)', 'baigun', 0 ],
			[ 'Giese, A. C.: Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral, thesis, Universidad Nacional del Comahue (abstract read 13/11/2026)', 'giese', 0 ],
			[ 'reglamentodepesca.org.ar: Peces de la Patagonia (read 13/11/2026; the site does not name its publisher)', 'regl', 0 ],
			[ 'IGFA: all-tackle world record for rainbow trout (21.77 kg; read 13/11/2026)', 'igfa_rb', 0 ],
			[ 'IGFA: all-tackle world record for brown trout (20.10 kg; read 13/11/2026)', 'igfa_br', 0 ],
			[ 'IGFA: all-tackle world record for brook trout (6.57 kg; read 13/11/2026)', 'igfa_bk', 0 ],
		],
	],
	'pt' => [
		'h2' => 'Tamanhos das trutas na Patagônia',
		'rx_viejo' => 'O tamanho típico e o recorde de cada espécie não os publicamos até ter uma fonte lida',
		'intro' => 'Os dados medidos mais completos que lemos são de uma tese de doutorado da Universidade de Buenos Aires (Baigún, 2001, <em>Ecología pesquera de lagos y embalses patagónicos</em>, tabelas 4.1.4 a 4.1.6), feita com pesca experimental com baterias de redes de emalhar, uma vez por ambiente no verão, entre 1984 e 1987. <strong>São amostras de rede desses lagos e reservatórios, não capturas de pescadores</strong>; servem como ordem de grandeza:',
		'lista' => [
			'<strong>Truta arco-íris:</strong> comprimento médio por ambiente de 25,5 a 54,3 cm e peso médio de 0,14 a 2,2 kg; o maior exemplar das redes mediu 78,6 cm e pesou 6,1 kg.',
			'<strong>Truta de riacho (fontinalis):</strong> comprimento médio de 22,3 a 47,5 cm e peso médio de 0,15 a 1,96 kg; o maior, 51,5 cm e 2,8 kg.',
			'<strong>Truta marrom</strong> (sete ambientes; Mascardi aparece duas vezes): comprimento médio de 43,6 a 53,4 cm e peso médio de 0,76 a 1,7 kg; o maior, 66,5 cm e 2,45 kg. Na tabela da marrom a coluna do peso médio está rotulada «(mm)»; pelos valores a lemos como gramas.',
		],
		'giese' => '<strong>Truta marrom do mar.</strong> Uma tese da Universidad Nacional del Comahue (Giese, Centro Regional Universitario Bariloche, <em>Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral</em>) compara as populações adultas dos rios Gallegos, Grande e Irigoyen. Segundo seu resumo, o comprimento dos adultos, considerando a idade marinha e o ano de esmoltificação, foi maior no rio Gallegos que nos rios fueguinos (Grande e Irigoyen), e não se observaram diferenças de peso entre os três rios.',
		'regl' => '<strong>O que diz um portal do regulamento.</strong> Segundo reglamentodepesca.org.ar (página «Peces de la Patagonia»; o site não nomeia quem a publica), a truta marrom «alcança os maiores portes (ex.: 16,3 kg no lago Nahuel Huapi)» e no rio Grande, Terra do Fogo, «foram homologados 7 recordes mundiais» na variedade que migra ao mar; da truta de riacho diz que «em ambientes mais produtivos, é frequente encontrar portes que superam os 4 kg» (tradução nossa do original em espanhol).',
		'igfa' => '<strong>Recordes da IGFA (qualquer equipamento), à parte: não são tamanhos comuns.</strong> Arco-íris: 21,77 kg (48 libras), Sean Konrad, lago Diefenbaker (Canadá), 5/9/2009. Marrom: 20,10 kg (44 libras e 5 onças), Seumas Petrie, Ohau Canal, Twizel (Nova Zelândia), 27/10/2020. De riacho: 6,57 kg (14 libras e 8 onças), Dr. J. W. Cook, rio Nipigon (Ontário, Canadá), 21/7/1915. Nenhum é da Patagônia (IGFA, lido em 13/11/2026).',
		'faq_q' => 'Quanto pesam as trutas da Patagônia?',
		'faq_a' => 'Nas pescas experimentais com redes de emalhar de Baigún (2001, tese da UBA; verões de 1984 a 1987), os pesos médios por ambiente foram de 0,14 a 2,2 kg na arco-íris, de 0,15 a 1,96 kg na de riacho e de 0,76 a 1,7 kg na marrom; os maiores exemplares dessas redes pesaram 6,1 kg (arco-íris), 2,8 kg (de riacho) e 2,45 kg (marrom). São amostras de rede, não capturas de pescadores. Segundo reglamentodepesca.org.ar, a marrom alcança os maiores portes (16,3 kg no lago Nahuel Huapi) e a de riacho supera os 4 kg em ambientes produtivos. Os recordes da IGFA (21,77 kg a arco-íris, 20,10 kg a marrom, 6,57 kg a de riacho) são do Canadá e da Nova Zelândia.',
		'rx_pend' => '#PENDIENTE: tamanho e peso típicos e máximo biológico de cada espécie com uma fonte de referência \(FishBase ou outra publicação científica\), e os recordes da IGFA por espécie\.#u',
		'pend' => 'PENDIENTE: tamanho e peso típicos da pesca esportiva por espécie e por água com uma fonte de referência (o publicado são redes experimentais de 1984 a 1987, o texto de um portal e os recordes da IGFA).',
		'fuentes' => [
			[ 'Baigún, C. (2001): Ecología pesquera de lagos y embalses patagónicos (Argentina), tese de doutorado, Universidad de Buenos Aires (tabelas 4.1.4 a 4.1.6; lida em 13/11/2026)', 'baigun', 0 ],
			[ 'Giese, A. C.: Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral, tese, Universidad Nacional del Comahue (resumo lido em 13/11/2026)', 'giese', 0 ],
			[ 'reglamentodepesca.org.ar: Peces de la Patagonia (lida em 13/11/2026; o site não nomeia seu editor)', 'regl', 0 ],
			[ 'IGFA: recorde mundial qualquer equipamento da truta arco-íris (21,77 kg; lido em 13/11/2026)', 'igfa_rb', 0 ],
			[ 'IGFA: recorde mundial qualquer equipamento da truta marrom (20,10 kg; lido em 13/11/2026)', 'igfa_br', 0 ],
			[ 'IGFA: recorde mundial qualquer equipamento da truta de riacho (6,57 kg; lido em 13/11/2026)', 'igfa_bk', 0 ],
		],
	],
];

/** Add a question to the pm-faq group of the page unless it is there; returns the new content. */
function pm_b_faq( string $c, string $q, string $a, array &$tareas ): string {
	if ( false !== strpos( $c, $q ) ) {
		return $c;
	}
	$n = 0;
	$r = preg_replace_callback( '#(<div class="wp-block-group pm-faq">.*?)(</div>\s*<!-- /wp:group -->)#su', function ( $m ) use ( $q, $a, &$n ) {
		$n++;
		return $m[1] . "\n\n" . rtrim( pm_b_faq_item( $q, $a ) ) . $m[2];
	}, $c, 1 );
	$n ? $tareas[] = "FAQ «{$q}»" : WP_CLI::warning( "no encontré el grupo pm-faq para «{$q}»." );
	return $n ? $r : $c;
}
/** Replace the hidden PENDIENTE paragraph that matches $rx. */
function pm_b_pend( string $c, string $rx, string $pend, array &$tareas ): string {
	$n = 0;
	$r = preg_replace_callback( '#<!-- wp:paragraph -->\s*<p>PENDIENTE: [^<]*</p>\s*<!-- /wp:paragraph -->#su', function ( $m ) use ( $rx, $pend, &$n ) {
		if ( ! preg_match( $rx, $m[0] ) ) {
			return $m[0];
		}
		$n++;
		return pm_sc_p( $pend );
	}, $c );
	$n && $tareas[] = 'marca PENDIENTE reducida a lo que falta';
	return $r;
}
function pm_b_cerrar( int $id, string $etiqueta, string $c, string $nuevo, ?array $fuentes_n, array $debe, string $hoy, bool $aplicar, int &$cambios, array $tareas ): void {
	if ( ! $tareas && ! $fuentes_n && $nuevo === $c ) {
		WP_CLI::log( "$etiqueta #{$id}: ya está." );
		return;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $etiqueta #{$id}: " . implode( ' · ', $tareas ) . ( $fuentes_n ? ' · fuentes nuevas' : '' ) );
	$cambios++;
	if ( ! $aplicar ) {
		return;
	}
	$nuevo !== $c && pm_sc_guardar( $id, $nuevo, $debe, $etiqueta );
	if ( $fuentes_n ) {
		update_field( 'fuentes', $fuentes_n, $id );
		count( (array) pm_campo( 'fuentes', $id ) ) === count( $fuentes_n ) || WP_CLI::error( "$etiqueta: las fuentes no quedaron." );
	}
	update_field( 'actualizado', $hoy, $id );
	$leido = get_post( $id )->post_content;
	$ok    = true;
	foreach ( $debe as $t ) {
		false === strpos( $leido, $t ) && ( $ok = false );
	}
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $etiqueta #{$id} " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$etiqueta: la lectura de vuelta no coincide." );
}

// --- 1. Surubí ---------------------------------------------------------------------------------------------------------
foreach ( $S as $i => $x ) {
	$p = pm_buscar( 'pm_especie', 'surubi', $i );
	if ( ! $p ) {
		WP_CLI::warning( "surubi ($i): no existe, se salta." );
		continue;
	}
	$c      = $p->post_content;
	$nuevo  = $c;
	$tareas = [];
	if ( false === strpos( $nuevo, $x['datos_li'] ) ) {
		$n = 0;
		$nuevo = preg_replace_callback( '#(<div class="wp-block-group datos">.*?)(</ul>)#su', function ( $m ) use ( $x, &$n ) {
			$n++;
			return $m[1] . $x['datos_li'] . $m[2];
		}, $nuevo, 1 );
		$n ? $tareas[] = 'datos clave: tamaño y récord' : WP_CLI::warning( "surubi ($i): no encontré la lista de datos clave." );
	}
	if ( false === strpos( $nuevo, $x['h2'] ) ) {
		$pos = pm_sc_pos_encabezado( $nuevo, '/^(Preguntas frecuentes|Frequently asked questions|Perguntas frequentes)$/u' );
		if ( null === $pos ) {
			WP_CLI::warning( "surubi ($i): no encontré el H2 de preguntas frecuentes." );
		} else {
			$b = pm_sc_h2( $x['h2'] );
			foreach ( $x['parrafos'] as $par ) {
				$b .= pm_sc_p( $par );
			}
			$nuevo    = pm_sc_insertar( $nuevo, $pos, $b );
			$tareas[] = "H2 «{$x['h2']}» (CARU, FishBase, Balboni et al., IBAMA)";
		}
	}
	$nuevo = pm_b_faq( $nuevo, $x['faq_q'], $x['faq_a'], $tareas );
	$nuevo = pm_b_pend( $nuevo, $x['rx_pend'], $x['pend'], $tareas );
	$quiero    = array_map( fn( $f ) => [ $f[0], $u[ $f[1] ], $f[2] ], $x['fuentes'] );
	$fuentes_n = pm_b_fuentes( $p->ID, $quiero, $hoy );
	pm_b_cerrar( $p->ID, "surubi ($i)", $c, $nuevo, $fuentes_n, [ $x['h2'], $x['faq_q'] ], $hoy, $aplicar, $cambios, $tareas );
}

// --- 2. Trout ----------------------------------------------------------------------------------------------------------
foreach ( $R as $i => $x ) {
	$p = pm_buscar( 'pm_especie', 'trucha', $i );
	if ( ! $p ) {
		WP_CLI::warning( "trucha ($i): no existe, se salta." );
		continue;
	}
	$c      = $p->post_content;
	$nuevo  = $c;
	$tareas = [];
	if ( false === strpos( $nuevo, $x['h2'] ) ) {
		$n     = 0;
		$bloque = pm_sc_h2( $x['h2'] ) . pm_sc_p( $x['intro'] ) . pm_sc_lista( $x['lista'] ) . pm_sc_p( $x['giese'] ) . pm_sc_p( $x['regl'] ) . pm_sc_p( $x['igfa'] );
		$nuevo = preg_replace_callback( '#<!-- wp:paragraph -->\s*<p>([^<]*)</p>\s*<!-- /wp:paragraph -->\s*#su', function ( $m ) use ( $x, $bloque, &$n ) {
			if ( 0 === $n && false !== strpos( $m[1], $x['rx_viejo'] ) ) {
				$n++;
				return $bloque;
			}
			return $m[0];
		}, $nuevo );
		$n ? $tareas[] = "H2 «{$x['h2']}» (Baigún 2001, Giese, reglamentodepesca.org.ar, IGFA) en lugar de la frase «no publicamos tamaños»" : WP_CLI::warning( "trucha ($i): no encontré la frase «no publicamos tamaños»." );
	}
	$nuevo = pm_b_faq( $nuevo, $x['faq_q'], $x['faq_a'], $tareas );
	$nuevo = pm_b_pend( $nuevo, $x['rx_pend'], $x['pend'], $tareas );
	$quiero    = array_map( fn( $f ) => [ $f[0], $u[ $f[1] ], $f[2] ], $x['fuentes'] );
	$fuentes_n = pm_b_fuentes( $p->ID, $quiero, $hoy );
	pm_b_cerrar( $p->ID, "trucha ($i)", $c, $nuevo, $fuentes_n, [ $x['h2'], $x['faq_q'] ], $hoy, $aplicar, $cambios, $tareas );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
