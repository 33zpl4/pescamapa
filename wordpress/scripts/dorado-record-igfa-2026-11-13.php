<?php
/**
 * Golden dorado record attribution, 13/11/2026. The IGFA record page itself was read in browser mode (research folder fuentes-2-3-20261113-2217,
 * file 01): "Weight 25.28 kg (55 lb 11 oz); Location Uruguay River, Concordia, Argentina; Catch Date 11-Jan-2006; Angler Andre L.S. de Botton;
 * Status Approved". PR 173 had written the opposite ("the IGFA publishes only the weight; river, year and angler come from the press"). Now:
 *  - Species page (es/en/pt): key-facts line, record paragraph, FAQ answer, SEO description and sources say what the IGFA page says; El País
 *    (Uruguay, 13/01/2011: places the catch at the Salto Grande dam, gives only the year) is added as a different description of the place;
 *    Weekend (Perfil, 16/07/2019: length 1.10 m, previous record by Armando Giúdice, Toledo, Corrientes, 27/09/1984) as optional detail.
 *  - Pillar note golden-dorado-guia (es/en/pt): the Uruguay River paragraph (and its duplicated "está en está en" typo).
 *  - La Zona notes (es/en/pt): the record paragraph and the IGFA source row.
 *  - Read-only sweep: no page, FAQ, source or description may still say "the IGFA publishes only the weight".
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = dorado-record-igfa-2026-11-13).
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



if ( ! function_exists( 'pm_t_cambiar' ) ) {
	/** Replace $viejo by $nuevo in $texto; null when $viejo is not there. */
	function pm_t_cambiar( string $texto, string $viejo, string $nuevo ): ?string {
		return false === strpos( $texto, $viejo ) ? null : str_replace( $viejo, $nuevo, $texto );
	}
}

$aplicar = false;
$hoy     = '20261113';
$cambios = 0;

$u_igfa_db  = 'https://igfa.org/game-fish-database/?search_term_1=126&search_type=SpeciesID';
$u_igfa_rec = 'https://igfa.org/member-services/world-record/common-name/Dorado';
$u_elpais   = 'https://www.elpais.com.uy/informacion/pesca-deportiva-y-ecologica-del-dorado-en-salto-grande';
$u_weekend  = 'https://weekend.perfil.com/noticias/pesca/como-conseguir-records-pesqueros-2.phtml';

$T = [
	'es' => [
		'rx_datos' => '#<li><strong>Récord mundial \(IGFA, todo equipo\):</strong>.*?</li>#su',
		'datos'    => '<li><strong>Récord mundial (IGFA, todo equipo):</strong> 25,28 kg (55 libras y 11 onzas), pescado el 11 de enero de 2006 por Andre L. S. de Botton en el río Uruguay, en Concordia, Argentina (IGFA, leído el 13/11/2026). El País (Uruguay, 13/1/2011) ubica la captura en la represa de Salto Grande.</li>',
		'marca_record' => 'Es lo único que muestra la ficha de la IGFA',
		'record'   => 'El récord mundial del dorado en la categoría todo equipo (<em>all-tackle</em>) de la IGFA, la International Game Fish Association, es de <strong>25,28 kg (55 libras y 11 onzas)</strong>. Según la página del récord de la IGFA (leída el 13/11/2026), lo pescó Andre L. S. de Botton el 11 de enero de 2006 en el río Uruguay, en Concordia, Argentina, y su estado es «aprobado». El País (Uruguay, 13/1/2011) ubica la misma captura en la represa de Salto Grande y da solo el año: la prensa describe distinto el lugar. Weekend (Perfil, 16/7/2019) agrega que el pez midió 1,10 m y que superó por casi dos kilos al récord anterior, de Armando Giúdice (Toledo, Corrientes, 27 de septiembre de 1984). La IGFA certifica sus récords con control de la línea y del terminal, pesaje en balanza certificada, fotografías y declaración de testigos.',
		'marca_faq' => 'la ficha de la IGFA muestra solo el peso',
		'faq_record' => 'El récord mundial del dorado en la categoría todo equipo (all-tackle) de la IGFA es de 25,28 kg (55 libras y 11 onzas): según la página del récord de la IGFA (leída el 13/11/2026), lo pescó Andre L. S. de Botton el 11 de enero de 2006 en el río Uruguay, en Concordia, Argentina. El País (Uruguay, 13/1/2011) ubica la captura en la represa de Salto Grande, así que la prensa describe distinto el lugar.',
		'seo_description' => 'Dorado (Salminus brasiliensis): récord IGFA de 25,28 kg (Concordia, 2006), dónde vive, cuánto mide y pesa y la regla de pesca en Corrientes.',
		'fuente_igfa' => 'IGFA: récord mundial todo equipo del dorado (25,28 kg, 11/1/2006, río Uruguay, Concordia; página del récord leída en navegador el 13/11/2026)',
		'fuente_elpais' => 'El País (Uruguay), 13/1/2011: Pesca deportiva y ecológica del dorado en Salto Grande (ubica la captura del récord en la represa; prensa)',
		'fuente_weekend' => 'Weekend (Perfil), 16/7/2019: Dónde pican los más grandes, dorados (medida del récord y récord anterior; prensa)',
		'pil_rx'   => '#La IGFA registra 25,28 kg.*?\(está en está en#su',
		'pil_nuevo' => 'La IGFA registra 25,28 kg (55 libras y 11 onzas) como récord mundial todo equipo del dorado: según la página del récord de la IGFA (leída el 13/11/2026), lo pescó Andre L. S. de Botton el 11 de enero de 2006 en el río Uruguay, en Concordia. El País (Uruguay, 13/1/2011) ubica la captura en la represa de Salto Grande, así que la prensa describe distinto el lugar (más en',
		'zona_viejo' => '; la ficha de la IGFA muestra solo el peso. Según la prensa (Diario Uno, consultado el 24/10/2026), se pescó en el río Uruguay en 2006.',
		'zona_nuevo' => ' (55 libras y 11 onzas), pescado el 11/1/2006 por Andre L. S. de Botton en el río Uruguay, en Concordia, Argentina, según la página del récord de la IGFA (leída el 13/11/2026). El País (Uruguay, 13/1/2011) ubica la captura en la represa de Salto Grande: la prensa describe distinto el lugar.',
	],
	'en' => [
		'rx_datos' => '#<li><strong>World record \(IGFA, all-tackle\):</strong>.*?</li>#su',
		'datos'    => '<li><strong>World record (IGFA, all-tackle):</strong> 25.28 kg (55 lb 11 oz), caught on 11 January 2006 by Andre L. S. de Botton in the Uruguay River at Concordia, Argentina (IGFA, read 13 November 2026). El País (Uruguay, 13 January 2011) places the catch at the Salto Grande dam.</li>',
		'marca_record' => 'That is all the IGFA page shows',
		'record'   => 'The IGFA (International Game Fish Association) all-tackle world record for the golden dorado is <strong>25.28 kg (55 lb 11 oz)</strong>. According to the IGFA record page (read 13 November 2026), Andre L. S. de Botton caught it on 11 January 2006 in the Uruguay River at Concordia, Argentina, and its status is “approved”. El País (Uruguay, 13 January 2011) places the same catch at the Salto Grande dam and gives only the year: the press describes the place differently. Weekend (Perfil, 16 July 2019) adds that the fish measured 1.10 m and beat the previous record, by Armando Giúdice (Toledo, Corrientes, 27 September 1984), by almost two kilos. The IGFA certifies its records with line and leader testing, certified scale weighing, photographs and signed witness statements.',
		'marca_faq' => 'the IGFA page shows only the weight',
		'faq_record' => 'The IGFA all-tackle world record for the golden dorado is 25.28 kg (55 lb 11 oz): according to the IGFA record page (read 13 November 2026), Andre L. S. de Botton caught it on 11 January 2006 in the Uruguay River at Concordia, Argentina. El País (Uruguay, 13 January 2011) places the catch at the Salto Grande dam, so the press describes the place differently.',
		'seo_description' => 'Golden dorado (Salminus brasiliensis): IGFA record of 25.28 kg (Concordia, 2006), where it lives, how big it gets and the Corrientes release rule.',
		'fuente_igfa' => 'IGFA: all-tackle world record for dorado (25.28 kg, 11/1/2006, Uruguay River, Concordia; record page read in browser mode on 13/11/2026)',
		'fuente_elpais' => 'El País (Uruguay), 13/1/2011: Pesca deportiva y ecológica del dorado en Salto Grande (places the record catch at the dam; press)',
		'fuente_weekend' => 'Weekend (Perfil), 16/7/2019: Dónde pican los más grandes, dorados (record length and previous record; press)',
		'pil_rx'   => '#The IGFA records 25\.28 kg.*?\(it is on it is on#su',
		'pil_nuevo' => 'The IGFA records 25.28 kg (55 lb 11 oz) as the golden dorado all-tackle world record: according to the IGFA record page (read 13 November 2026), Andre L. S. de Botton caught it on 11 January 2006 in the Uruguay River at Concordia. El País (Uruguay, 13 January 2011) places the catch at the Salto Grande dam, so the press describes the place differently (more on',
		'zona_viejo' => '; the IGFA page shows only the weight. According to the press (Diario Uno, consulted 24 October 2026), it was caught in the Uruguay River in 2006.',
		'zona_nuevo' => ' (55 lb 11 oz), caught on 11/1/2006 by Andre L. S. de Botton in the Uruguay River at Concordia, Argentina, according to the IGFA record page (read 13 November 2026). El País (Uruguay, 13 January 2011) places the catch at the Salto Grande dam: the press describes the place differently.',
	],
	'pt' => [
		'rx_datos' => '#<li><strong>Recorde mundial \(IGFA, qualquer equipamento\):</strong>.*?</li>#su',
		'datos'    => '<li><strong>Recorde mundial (IGFA, qualquer equipamento):</strong> 25,28 kg (55 libras e 11 onças), pescado em 11 de janeiro de 2006 por Andre L. S. de Botton no rio Uruguai, em Concordia, Argentina (IGFA, lido em 13/11/2026). O País (Uruguai, 13/1/2011) situa a captura na represa de Salto Grande.</li>',
		'marca_record' => 'É só o que a página da IGFA mostra',
		'record'   => 'O recorde mundial do dourado na categoria <em>all-tackle</em> da IGFA, a International Game Fish Association, é de <strong>25,28 kg (55 libras e 11 onças)</strong>. Segundo a página do recorde da IGFA (lida em 13/11/2026), foi pescado por Andre L. S. de Botton em 11 de janeiro de 2006 no rio Uruguai, em Concordia, Argentina, e seu status é «aprovado». O jornal El País (Uruguai, 13/1/2011) situa a mesma captura na represa de Salto Grande e dá só o ano: a imprensa descreve o lugar de outro modo. A Weekend (Perfil, 16/7/2019) acrescenta que o peixe media 1,10 m e superou por quase dois quilos o recorde anterior, de Armando Giúdice (Toledo, Corrientes, 27 de setembro de 1984). A IGFA certifica seus recordes com verificação da linha e do terminal, pesagem em balança certificada, fotografias e declaração de testemunhas.',
		'marca_faq' => 'a página da IGFA mostra só o peso',
		'faq_record' => 'O recorde mundial do dourado na categoria todo equipamento (all-tackle) da IGFA é de 25,28 kg (55 libras e 11 onças): segundo a página do recorde da IGFA (lida em 13/11/2026), foi pescado por Andre L. S. de Botton em 11 de janeiro de 2006 no rio Uruguai, em Concordia, Argentina. O jornal El País (Uruguai, 13/1/2011) situa a captura na represa de Salto Grande, então a imprensa descreve o lugar de outro modo.',
		'seo_description' => 'Dourado (Salminus brasiliensis): recorde IGFA de 25,28 kg (Concordia, 2006), onde vive, quanto mede e pesa e a regra de soltura em Corrientes.',
		'fuente_igfa' => 'IGFA: recorde mundial qualquer equipamento do dourado (25,28 kg, 11/1/2006, rio Uruguai, Concordia; página do recorde lida em navegador em 13/11/2026)',
		'fuente_elpais' => 'El País (Uruguai), 13/1/2011: Pesca deportiva y ecológica del dorado en Salto Grande (situa a captura do recorde na represa; imprensa)',
		'fuente_weekend' => 'Weekend (Perfil), 16/7/2019: Dónde pican los más grandes, dorados (medida do recorde e recorde anterior; imprensa)',
		'pil_rx'   => '#A IGFA registra 25,28 kg.*?\(está em está em#su',
		'pil_nuevo' => 'A IGFA registra 25,28 kg (55 libras e 11 onças) como recorde mundial todo equipamento do dourado: segundo a página do recorde da IGFA (lida em 13/11/2026), foi pescado por Andre L. S. de Botton em 11 de janeiro de 2006 no rio Uruguai, em Concordia. O jornal El País (Uruguai, 13/1/2011) situa a captura na represa de Salto Grande, então a imprensa descreve o lugar de outro modo (mais em',
		'zona_viejo' => '; a página da IGFA mostra só o peso. Segundo a imprensa (Diario Uno, consultado em 24/10/2026), foi pescado no rio Uruguai em 2006.',
		'zona_nuevo' => ' (55 libras e 11 onças), pescado em 11/1/2006 por Andre L. S. de Botton no rio Uruguai, em Concordia, Argentina, segundo a página do recorde da IGFA (lida em 13/11/2026). O jornal El País (Uruguai, 13/1/2011) situa a captura na represa de Salto Grande: a imprensa descreve o lugar de outro modo.',
	],
];

/** Rewrite the IGFA source row (name and URL) and add the extra sources whose URL is missing; returns the rows to write or null. */
function pm_a_fuentes( int $id, string $u_db, string $u_rec, string $nombre_igfa, array $extra, string $hoy ): ?array {
	$filas = array_map( function ( $f ) {
		$f['consultada'] = isset( $f['consultada'] ) ? str_replace( '-', '', (string) $f['consultada'] ) : '';
		return $f;
	}, array_values( array_filter( pm_filas( 'fuentes', $id ), 'is_array' ) ) );
	$antes = $filas;
	foreach ( $filas as $k => $f ) {
		if ( in_array( $f['url'] ?? '', [ $u_db, $u_rec ], true ) ) {
			$filas[ $k ]['nombre'] = $nombre_igfa;
			$filas[ $k ]['url']    = $u_rec;
		}
	}
	$urls = array_column( $filas, 'url' );
	if ( ! in_array( $u_rec, $urls, true ) ) {
		$filas[] = [ 'nombre' => $nombre_igfa, 'url' => $u_rec, 'oficial' => 0, 'consultada' => $hoy ];
		$urls[]  = $u_rec;
	}
	foreach ( $extra as [ $nombre, $url ] ) {
		if ( ! in_array( $url, $urls, true ) ) {
			$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => 0, 'consultada' => $hoy ];
			$urls[]  = $url;
		}
	}
	return $filas !== $antes ? $filas : null;
}

// --- 1. Species page ------------------------------------------------------------------------------------------------
foreach ( $T as $i => $x ) {
	$p = pm_buscar( 'pm_especie', 'dorado', $i );
	if ( ! $p ) {
		WP_CLI::warning( "dorado ($i): no existe, se salta." );
		continue;
	}
	$c      = $p->post_content;
	$nuevo  = $c;
	$tareas = [];
	if ( preg_match( $x['rx_datos'], $nuevo, $m ) && $m[0] !== $x['datos'] ) {
		$nuevo    = preg_replace_callback( $x['rx_datos'], fn() => $x['datos'], $nuevo, 1 );
		$tareas[] = 'datos clave: récord con río, fecha y pescador de la IGFA';
	} elseif ( ! preg_match( $x['rx_datos'], $nuevo ) ) {
		WP_CLI::warning( "dorado ($i): no encontré la línea del récord en los datos clave." );
	}
	$hechos = [ 'sec' => false, 'faq' => false ];
	$nuevo  = preg_replace_callback( '#(<!-- wp:paragraph[^>]*-->\s*<p[^>]*>)(.*?)(</p>\s*<!-- /wp:paragraph -->)#su', function ( $m ) use ( $x, &$hechos ) {
		if ( false !== strpos( $m[2], $x['marca_record'] ) ) {
			$hechos['sec'] = true;
			return $m[1] . $x['record'] . $m[3];
		}
		if ( false !== strpos( $m[2], $x['marca_faq'] ) ) {
			$hechos['faq'] = true;
			return $m[1] . $x['faq_record'] . $m[3];
		}
		return $m[0];
	}, $nuevo );
	$hechos['sec'] && $tareas[] = 'párrafo del récord reescrito (IGFA: río, fecha, pescador; El País y Weekend aparte)';
	$hechos['faq'] && $tareas[] = 'FAQ del récord reescrita';
	$ya = false !== strpos( $nuevo, $x['record'] ) && false !== strpos( $nuevo, $x['faq_record'] );
	if ( ! $ya && ( ! $hechos['sec'] || ! $hechos['faq'] ) ) {
		WP_CLI::warning( "dorado ($i): párrafo del récord " . ( $hechos['sec'] ? 'ok' : 'NO encontrado' ) . ', FAQ ' . ( $hechos['faq'] ? 'ok' : 'NO encontrada' ) );
	}
	$desc_ahora = (string) pm_campo( 'seo_description', $p->ID );
	if ( mb_strlen( $x['seo_description'] ) > 160 ) {
		WP_CLI::error( "dorado ($i): seo_description de " . mb_strlen( $x['seo_description'] ) . ' car.' );
	}
	$cambia_desc = $desc_ahora !== $x['seo_description'];
	$cambia_desc && $tareas[] = 'seo_description: «' . $desc_ahora . '» → «' . $x['seo_description'] . '»';
	$fuentes_n = pm_a_fuentes( $p->ID, $u_igfa_db, $u_igfa_rec, $x['fuente_igfa'], [ [ $x['fuente_elpais'], $u_elpais ], [ $x['fuente_weekend'], $u_weekend ] ], $hoy );
	$fuentes_n && $tareas[] = 'fuentes: IGFA (página del récord), El País, Weekend';
	if ( ! $tareas ) {
		WP_CLI::log( "dorado ($i) #{$p->ID}: ya está." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": dorado ($i) #{$p->ID}: " . implode( ' · ', $tareas ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$nuevo !== $c && pm_sc_guardar( $p->ID, $nuevo, [ $x['faq_record'] ], "dorado ($i)" );
	$cambia_desc && pm_sc_campo( $p->ID, 'seo_description', $x['seo_description'], "dorado ($i)" );
	if ( $fuentes_n ) {
		update_field( 'fuentes', $fuentes_n, $p->ID );
		count( (array) pm_campo( 'fuentes', $p->ID ) ) === count( $fuentes_n ) || WP_CLI::error( "dorado ($i): las fuentes no quedaron." );
	}
	update_field( 'actualizado', $hoy, $p->ID );
	$leido = get_post( $p->ID )->post_content;
	$ok    = false !== strpos( $leido, $x['datos'] ) && false !== strpos( $leido, $x['record'] ) && false !== strpos( $leido, $x['faq_record'] ) && false === strpos( $leido, $x['marca_faq'] );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": dorado ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' — datos clave, párrafo y FAQ del récord leídos de vuelta' );
	$ok || WP_CLI::error( "dorado ($i): la lectura de vuelta no coincide." );
}

// --- 2. Pillar note golden-dorado-guia ------------------------------------------------------------------------------
foreach ( $T as $i => $x ) {
	$p = pm_buscar( 'pm_nota', 'golden-dorado-guia', $i );
	if ( ! $p ) {
		WP_CLI::warning( "pilar dorado ($i): no existe." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, $x['pil_nuevo'] ) ) {
		WP_CLI::log( "pilar dorado ($i) #{$p->ID}: ya está." );
		continue;
	}
	if ( ! preg_match( $x['pil_rx'], $c ) ) {
		WP_CLI::warning( "pilar dorado ($i) #{$p->ID}: no encontré la frase del récord tal cual; revisar a mano." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": pilar dorado ($i) #{$p->ID} " . get_permalink( $p->ID ) . ': frase del récord reescrita (IGFA: río, fecha y pescador; El País aparte); se arregla también el «está en está en» duplicado' );
	$cambios++;
	if ( $aplicar ) {
		$nuevo = preg_replace_callback( $x['pil_rx'], fn() => $x['pil_nuevo'], $c, 1 );
		pm_sc_guardar( $p->ID, $nuevo, [ $x['pil_nuevo'] ], "pilar dorado ($i)" );
		WP_CLI::log( "VERIFIED: pilar dorado ($i) #{$p->ID}" );
	}
}

// --- 3. La Zona notes ---------------------------------------------------------------------------------------------------
foreach ( $T as $i => $x ) {
	$p = pm_buscar( 'pm_nota', 'la-zona-salto-grande', $i );
	if ( ! $p ) {
		WP_CLI::warning( "la zona ($i): no existe." );
		continue;
	}
	$c      = $p->post_content;
	$tareas = [];
	$nuevo  = $c;
	if ( false === strpos( $c, $x['zona_nuevo'] ) ) {
		$r = pm_t_cambiar( $c, $x['zona_viejo'], $x['zona_nuevo'] );
		if ( null === $r ) {
			WP_CLI::warning( "la zona ($i) #{$p->ID}: no encontré la frase del récord tal cual; revisar a mano." );
		} else {
			$nuevo    = $r;
			$tareas[] = 'párrafo del récord (IGFA: río, fecha y pescador; El País aparte)';
		}
	}
	$fuentes_n = pm_a_fuentes( $p->ID, $u_igfa_db, $u_igfa_rec, $x['fuente_igfa'], [ [ $x['fuente_elpais'], $u_elpais ] ], $hoy );
	$fuentes_n && $tareas[] = 'fuentes: IGFA (página del récord) y El País';
	if ( ! $tareas ) {
		WP_CLI::log( "la zona ($i) #{$p->ID}: ya está." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": la zona ($i) #{$p->ID} " . get_permalink( $p->ID ) . ': ' . implode( ' · ', $tareas ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$nuevo !== $c && pm_sc_guardar( $p->ID, $nuevo, [ $x['zona_nuevo'] ], "la zona ($i)" );
	if ( $fuentes_n ) {
		update_field( 'fuentes', $fuentes_n, $p->ID );
		count( (array) pm_campo( 'fuentes', $p->ID ) ) === count( $fuentes_n ) || WP_CLI::error( "la zona ($i): las fuentes no quedaron." );
	}
	WP_CLI::log( "VERIFIED: la zona ($i) #{$p->ID}" );
}

// --- Sweep (read only): the unsupported sentence must be gone from every page, field and source name --------------------
$rx = '/(IGFA|la ficha|the IGFA page)[^.]{0,40}(solo (muestra )?el peso|only (shows )?the weight|publishes only the weight|s[oó] (mostra )?o peso)|solo el peso|weight only|só o peso/iu';
foreach ( [ 'pm_destino', 'pm_nota', 'pm_especie', 'pm_prestador', 'page' ] as $tipo ) {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'numberposts' => -1 ] ) as $q ) {
		$txt = wp_strip_all_tags( $q->post_content );
		foreach ( [ 'faqs', 'fuentes' ] as $f ) {
			$txt .= ' ' . wp_json_encode( (array) pm_filas( $f, $q->ID ), JSON_UNESCAPED_UNICODE );
		}
		$txt .= ' ' . (string) pm_campo( 'seo_description', $q->ID );
		if ( preg_match( $rx, $txt, $m ) ) {
			WP_CLI::warning( "BARRIDO: queda «{$m[0]}» en $tipo #{$q->ID} «{$q->post_title}»" );
		}
	}
}
WP_CLI::log( 'BARRIDO terminado (sin avisos arriba = la frase «solo el peso» ya no está en ninguna página, FAQ, fuente ni descripción).' );
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
