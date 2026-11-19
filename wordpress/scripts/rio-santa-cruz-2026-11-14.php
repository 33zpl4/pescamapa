<?php
/**
 * Río Santa Cruz (Piedra Buena), 14/11/2026: NEW destination guide on the steelhead river (es, en, pt, published), one operator card (Los Plateados,
 * Tres Amigos Outfitters) and the El Calafate guide reduced to a pointer. Owner-approved gap ("steelhead, high-ticket, foreign demand, no coverage").
 * Facts and where they come from (research branch `investigacion`, folders santacruz-1/2/3-20261114-*, fuentes-2-1-20261113-2207):
 *  - Rules: Disposición 241/2026, Anexo I, entry "SANTA CRUZ (RÍO)" (PDF text read in full, fuentes-2-1 file 21) and Art. 16 of the General Regulation;
 *    the considerandos quote ("única población silvestre de linaje atlántico en el continente americano") is on the Boletín Oficial page (file 24).
 *    The Anexo is quoted word for word and never presented as a permit; the one-steelhead quota is not turned into an authorisation to keep a fish.
 *    The trolling coordinates and the hydroelectric closure coordinates are official but are NOT published (areas, not points).
 *  - Fish: Lázari et al. 2024 (J Fish Biol, DOI 10.1111/jfb.15722; file santacruz-1/20), Riva Rossi, Arguimbau and Pascual 2003 (Ecología Austral 13:151-159;
 *    abstract santacruz-1/19, details from the paper as the owner supplied them), Riva Rossi, Lessa and Pascual 2004 (CJFAS 61:1095-1101), Pascual et al. 2001
 *    (Trans Am Fish Soc 130:53-67), Llompart et al. 2024 (Ecología Austral 34(2); santacruz-1/17). Nothing is said about abundance or the state of the population.
 *  - Town and access: Welcome Argentina (240 km from Río Gallegos), Visit Argentina (Ruta 3, river 500 m wide, Isla Pavón), InterPatagonia (festival, "Capital
 *    Nacional de la Trucha Steelhead"). Pin: Comandante Luis Piedra Buena, -49.9831 / -68.9100 (Wikipedia es, supplied by the owner): a town point, not a fishing spot.
 *  - Operator: Tres Amigos Outfitters' own pages (los-plateados, santa-cruz of 4/6/2021, facts of 20/05/2021, 2021 season report). Its undated rates
 *    (half week / full week), the "up to 14 guests / 12 fishermen" line (contradicts "4 anglers"), the first-person reports, phone numbers, guide names and
 *    anything about boats are NOT written. The card is "información pública, sin verificar" (no consent, no agreement), price empty ("a consultar"), no photo.
 *  - Agency references (owner's rule of 14/11/2026: what the press and operators report consistently is published, attributed): Where Wise Men Fish (page of
 *    5/03/2025: US$ 4,990 / 2,990, licence included), Fly Water Travel (Feb 2024: US$ 4,990, licence not in the included list), Sportquest Holidays (page of
 *    2026: licence included, season end of March to early May, weights, 386 km, 3.5 h by road). First Cast Fly Fishing and the Glacier King / Guanaco programmes are not used.
 *  - Permit: ARS 90,000 / 300,000 / 450,000 as already published in the El Calafate and Río Gallegos guides (official portal, read 4/11/2026).
 * Calafate: the block "El río Santa Cruz en detalle" (created by santa-cruz-anexo-i-2026-11-13) becomes a one-paragraph pointer to this guide and its two FAQs
 * (róbalo, chinook) move here, in es, en and pt.
 * Idempotent: an existing guide is never touched; an existing card only gets its empty fields filled; the Calafate block is replaced once (a pointer already
 * there is left alone). Dry run first ($aplicar = false). Run with wp-script.yml (script = rio-santa-cruz-2026-11-14).
 */

$aplicar = true;
$hoy     = '20261114'; // date of the guide text
$leido   = '20261114'; // date the pages and papers were read (branch `investigacion`); the Boletín Oficial text was read on 13/11/2026

// ---- helpers (same pattern as itati-2026-11-14 / santa-cruz-anexo-i-2026-11-13) -------------------------------------------------------
function pm_rsc_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_rsc_h( string $texto, int $nivel = 2 ): string {
	$attr = 2 === $nivel ? '' : ' {"level":' . $nivel . '}';
	return "<!-- wp:heading{$attr} -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->";
}
function pm_rsc_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_rsc_tabla( array $cabecera, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
function pm_rsc_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}
/** Blocks as the data writes them: ["p", html] ["h2", text] ["h3", text] ["ul", [items]] ["table", [head], [rows]] ["pend", text]. */
function pm_rsc_bloques( array $bloques ): string {
	$out = [];
	foreach ( $bloques as $b ) {
		switch ( $b[0] ) {
			case 'p':
				$out[] = pm_rsc_p( $b[1] );
				break;
			case 'h2':
				$out[] = pm_rsc_h( $b[1], 2 );
				break;
			case 'h3':
				$out[] = pm_rsc_h( $b[1], 3 );
				break;
			case 'ul':
				$out[] = pm_rsc_lista( $b[1] );
				break;
			case 'table':
				$out[] = pm_rsc_tabla( $b[1], $b[2] );
				break;
			case 'pend':
				$out[] = pm_rsc_pendiente( $b[1] );
				break;
			default:
				WP_CLI::error( 'bloque desconocido: ' . $b[0] );
		}
	}
	return implode( "\n\n", $out );
}
/** The guide of this clave in this language, by direct query (pm_buscar caches the list and would not see a post created in this run). */
function pm_rsc_guia( string $clave, string $i ): ?WP_Post {
	$ps = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	foreach ( $ps as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}
/** {n:clave} note URL, {g:clave} guide URL (Spanish when the language has none), {esp:clave} species URL, {viaje}; fails on anything left over. */
function pm_rsc_resolver( string $html, string $i, string $clave_destino ): string {
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\}/', function ( $m ) use ( $i ) {
		$ids = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $m[1], 'numberposts' => -1, 'fields' => 'ids' ] );
		$por = [];
		foreach ( $ids as $id ) {
			$por[ pm_idioma_de( (int) $id ) ] = (int) $id;
		}
		if ( ! isset( $por[ $i ] ) ) {
			WP_CLI::warning( "nota {$m[1]} no existe en {$i}: el enlace va a la versión en español" );
		}
		$id = $por[ $i ] ?? ( $por['es'] ?? 0 );
		if ( ! $id ) {
			WP_CLI::error( "No encuentro la nota {$m[1]} (ni en español)." );
		}
		return esc_url( get_permalink( $id ) );
	}, $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\}/', function ( $m ) use ( $i ) {
		$g = pm_rsc_guia( $m[1], $i ) ?: pm_rsc_guia( $m[1], 'es' );
		if ( ! $g ) {
			WP_CLI::error( "No encuentro la guía {$m[1]}." );
		}
		if ( pm_idioma_de( $g->ID ) !== $i ) {
			WP_CLI::warning( "guía {$m[1]} no existe en {$i}: el enlace va a la versión en español" );
		}
		return esc_url( get_permalink( $g->ID ) );
	}, $html );
	$html = str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) . '?destino=' . $clave_destino . '&especie=trucha' ), $html );
	$html = str_replace( '{esp:trucha}', esc_url( pm_url_especie( 'trucha', $i ) ), $html );
	if ( preg_match( '/\{(n:[a-z0-9-]*|g:[a-z0-9-]*|esp:[a-z-]*|viaje)\}/', $html, $m ) ) {
		WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
	}
	return $html;
}
function pm_rsc_resolver_todo( $v, string $i, string $clave ) {
	if ( is_string( $v ) ) {
		return pm_rsc_resolver( $v, $i, $clave );
	}
	if ( is_array( $v ) ) {
		return array_map( fn( $x ) => pm_rsc_resolver_todo( $x, $i, $clave ), $v );
	}
	return $v;
}
function pm_rsc_ver( $v ): string {
	return is_array( $v ) ? implode( ',', array_map( fn( $x ) => is_array( $x ) ? wp_json_encode( $x, JSON_UNESCAPED_UNICODE ) : (string) $x, $v ) ) : (string) $v;
}
function pm_rsc_norm( string $k, $v ): string {
	if ( in_array( $k, [ 'precio_fecha', 'verificado', 'actualizado' ], true ) ) {
		return preg_replace( '/\D/', '', (string) $v );
	}
	if ( in_array( $k, [ 'prest_consentimiento', 'prest_publico', 'datos_ejemplo', 'capacidad' ], true ) ) {
		return (string) (int) $v;
	}
	if ( in_array( $k, [ 'lat', 'lon' ], true ) ) {
		return (string) round( (float) $v, 4 );
	}
	return is_array( $v ) ? implode( ',', $v ) : trim( (string) $v );
}
/** Repeater rows reduced to the keys we wrote, dates as digits, oficial as 0/1: comparable with what was sent. */
function pm_rsc_filas( array $filas, array $claves ): array {
	return array_map( function ( $f ) use ( $claves ) {
		$o = [];
		foreach ( $claves as $c ) {
			$v = $f[ $c ] ?? '';
			$o[ $c ] = 'consultada' === $c ? preg_replace( '/\D/', '', (string) $v ) : ( 'oficial' === $c ? (int) ! empty( $v ) : ( in_array( $c, [ 'lat', 'lon' ], true ) ? ( '' === (string) $v ? '' : (string) round( (float) $v, 4 ) ) : (string) $v ) );
		}
		return $o;
	}, $filas );
}
const PM_RSC_REPETIDORES = [
	'fuentes'   => [ 'nombre', 'url', 'oficial', 'consultada' ],
	'temporada' => [ 'meses', 'que' ],
	'aguas'     => [ 'nombre', 'nota', 'lat', 'lon' ],
	'faqs'      => [ 'pregunta', 'respuesta' ],
];
function pm_rsc_ficha_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}
/** Heading blocks with offset: [ [ offset, plain text ], … ]. */
function pm_rsc_encabezados( string $c ): array {
	preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
	$out = [];
	foreach ( $m[0] as $n => $b ) {
		$out[] = [ $b[1], trim( wp_strip_all_tags( $m[1][ $n ][0] ) ) ];
	}
	return $out;
}
/** Never published: what the owner or the sources ruled out (contacts, the operator's undated rates, the 14-guests line, boats of the lodge, other programmes, other agencies). */
const PM_RSC_PROHIBIDO = '/First Cast|Crazy Fishing|Glacier|Guanaco|Buitreras|\b14 (guests|hu[eé]spedes|convidados)|\b12 (fishermen|pescadores)|zodiac|outboard|fuera de borda|\+54|@|Claudio|Biott|Morrish|Crawford|Lucas Young|5[.,]200|US\$ ?3[.,]000/iu';
/** The same list without the contact patterns, for the rendered page (the theme may add its own markup with an @ in it). */
const PM_RSC_PROHIBIDO_HTML = '/First Cast|Crazy Fishing|Glacier|Guanaco|Buitreras|\b14 (guests|hu[eé]spedes|convidados)|\b12 (fishermen|pescadores)|zodiac|outboard|fuera de borda|Claudio|Biott|Morrish|Crawford|Lucas Young|5[.,]200|US\$ ?3[.,]000/iu';
// ---- end helpers -----------------------------------------------------------------------------------------------------------------------

$u_bo1  = 'https://boletinoficial.santacruz.gob.ar/legislacion/disposiciones-completas/60895';
$u_bo2  = 'https://boletinoficial.santacruz.gob.ar/legislacion/disposiciones-completas/60897';
$u_pdf  = 'https://boletinoficial.santacruz.gob.ar/adjuntos/DISPOSICION_N_241_-_REGLAMENTO_2.pdf';
$u_val  = 'https://pesca.santacruz.gob.ar/valores/';
$u_amb  = 'https://pesca.santacruz.gob.ar/ambientes/';
$u_lp   = 'https://www.tresamigosoutfitters.com/los-plateados/';
$u_ess  = 'https://www.tresamigosoutfitters.com/santa-cruz/';
$u_fact = 'https://www.tresamigosoutfitters.com/facts-about-santa-cruz-steelhead/';
$u_rep  = 'https://www.tresamigosoutfitters.com/santa-cruz-steelhead-2021-season-report/';
$u_sq   = 'https://www.sportquestholidays.com/tour/steelhead-santa-cruz-river/';
$u_ww   = 'https://www.wherewisemenfish.com/holidays/santa-cruz-steelhead/s7216/';
$u_fwt  = 'https://farbank.com/blogs/fly-water-travel/atlantic-steelhead-and-jurassic-trout-combo';
$u_wa   = 'https://www.welcomeargentina.com/piedrabuena/';
$u_at   = 'https://www.argentina.travel/actividades/comandante-luis-piedrabuena';
$u_ip   = 'https://www.interpatagonia.com/piedrabuena/ria-santa-cruz.html';
$u_rr3  = 'https://bibliotecadigital.exactas.uba.ar/collection/ecologiaaustral/document/ecologiaaustral_v013_n02_p151';
$u_ll   = 'https://bibliotecadigital.exactas.uba.ar/collection/ecologiaaustral/document/ecologiaaustral_v034_n02_p272';
$u_lz   = 'https://doi.org/10.1111/jfb.15722';
$u_rr4  = 'https://doi.org/10.1139/f04-056';

$G = [];

// =====================================================================================================================================
// ESPAÑOL
// =====================================================================================================================================
$G['es'] = [
	'titulo' => 'Río Santa Cruz (Piedra Buena)',
	'slug'   => 'pesca-en-el-rio-santa-cruz',
	'autoridad' => 'Secretaría de Estado de Pesca y Acuicultura de Santa Cruz',
	'cuerpo' => [
		[ 'p', 'El <strong>río Santa Cruz</strong>, en la provincia de Santa Cruz, es el río de una trucha arco iris que baja al mar y vuelve a subir: el <strong>steelhead</strong>. La Disposición 241/2026 de la Secretaría de Estado de Pesca y Acuicultura, en sus considerandos, la llama «la única población silvestre de linaje atlántico en el continente americano» (cita textual). <strong>Comandante Luis Piedra Buena</strong> (Piedra Buena) es la ciudad sobre el río, a unos 240 km de Río Gallegos por la Ruta Nacional 3, según Welcome Argentina. El Anexo I del reglamento 2026/27 deja el río abierto todo el año y fija «un (1) ejemplar de Trucha Steelhead» por pescador y por temporada, con captura y devolución obligatoria del 15/03/2027 al 30/09/2027 (el texto completo está más abajo). Los operadores y los trabajos que leímos ubican la pesca del steelhead en el otoño austral, de fines de febrero o marzo a mayo. El único lodge con página propia sobre el río que encontramos es <strong>Los Plateados</strong>, de Tres Amigos Outfitters; abajo está su tarjeta, con lo que publica.' ],
		[ 'h2', 'Datos clave' ],
		[ 'table', [ 'Dato', 'Qué dice', 'Fuente' ], [
			[ 'Río y ciudad base', 'Unos 386 km desde el lago Argentino hasta el Atlántico; ciudad base: Comandante Luis Piedra Buena', 'Sportquest Holidays (página de 2026)' ],
			[ 'Distancia', 'Unos 240 km de Río Gallegos por la Ruta Nacional 3', 'Welcome Argentina' ],
			[ 'Especies con reglas propias', 'Steelhead, salmón chinook y róbalo; perca y trucha marrón entran en el límite diario', 'Anexo I, Disposición 241/2026' ],
			[ 'Steelhead', 'Cupo de un (1) ejemplar por pescador y por temporada; captura y devolución obligatoria del 15/03/2027 al 30/09/2027', 'Anexo I, Disposición 241/2026' ],
			[ 'Salmón chinook', 'Del 01/08/2027 al 30/11/2027; un (1) ejemplar por pescador y por día', 'Anexo I, Disposición 241/2026' ],
			[ 'Apertura', 'Todo el año; sin carnada natural (salvo excepciones) y sin lastres de plomo', 'Anexo I, Disposición 241/2026' ],
			[ 'Cuándo, según los operadores', 'De fines de febrero o marzo a mayo', 'Tres Amigos Outfitters y Sportquest Holidays' ],
			[ 'Permiso de Santa Cruz (extranjero no residente, 2026/27)', '$ 90.000 por día, $ 300.000 por semana, $ 450.000 por temporada', 'Portal de Pesca y Acuicultura de Santa Cruz (leído el 4/11/2026)' ],
			[ 'Programa del lodge', 'Seis días de pesca, de sábado a viernes, para 4 pescadores', 'Tres Amigos Outfitters (leída el 14/11/2026)' ],
			[ 'Referencia de precio de una agencia', 'US$ 4.990 por semana completa y US$ 2.990 por media semana', 'Where Wise Men Fish (página del 5/03/2025)' ],
		] ],
		[ 'h2', 'De dónde viene el steelhead del Santa Cruz' ],
		[ 'p', 'La trucha arco iris no es nativa del río. Según <a href="' . $u_lz . '" rel="noopener">Lázari et al. (2024, <em>Journal of Fish Biology</em>, DOI 10.1111/jfb.15722)</a>, en la cuenca del Santa Cruz se sembraron ejemplares importados, presumiblemente de California u Oregón, entre 1906 y 1910, y para la década de 1920 había poblaciones naturalizadas y bien distribuidas en toda la cuenca. Un estudio de ADN mitocondrial (<a href="' . $u_rr4 . '" rel="noopener">Riva Rossi, Lessa y Pascual, 2004, <em>Canadian Journal of Fisheries and Aquatic Sciences</em> 61:1095-1101</a>) señaló al río McCloud, California, como el origen más probable; Lázari et al. (2024) leen esos resultados como una mezcla de varias fuentes californianas y ven una contribución del McCloud que no habría sido la única. Lázari et al. relacionan a los peces del Santa Cruz con poblaciones de California y Oregón que tienen un componente anádromo, y encontraron que los peces residentes y los anádromos del río forman una sola población que se cruza entre sí.' ],
		[ 'p', 'El primer caso documentado de anadromía en esta trucha introducida es de Pascual et al. (2001, <em>Transactions of the American Fisheries Society</em> 130:53-67). <a href="' . $u_rr3 . '" rel="noopener">Riva Rossi, Arguimbau y Pascual (2003, <em>Ecología Austral</em> 13:151-159)</a> lo resumen así: el Santa Cruz es «el único ambiente en el mundo donde la trucha arco iris introducida desarrolló poblaciones migratorias facultativas», es decir, una parte de los peces completa su ciclo en agua dulce y otra viaja al mar. Ese trabajo siguió con radiotransmisores a siete peces que subían del mar en el otoño de 2000: medían entre 490 y 780 mm de longitud furcal (el peso no figura); el pico de entrada fue a comienzos de abril; describe una pesquería de febrero a mayo, sobre todo en los primeros 50 km del río; el desove empezó en agosto, con su pico en septiembre y octubre y agua de 4 a 10 °C; y los recorridos de los peces marcados fueron de 0,6 a 279,5 km (Tabla 1).' ],
		[ 'h2', 'Cuándo ir: lo que dicen las fuentes' ],
		[ 'p', 'No hay una fecha oficial de «temporada» para el steelhead: el río está abierto todo el año y el Anexo I solo marca cuándo rige la devolución obligatoria. Lo que sigue son fechas de operadores y de trabajos publicados, y no coinciden semana a semana.' ],
		[ 'table', [ 'Fuente', 'Qué dice', 'Fecha de la fuente' ], [
			[ 'Los Plateados (Tres Amigos Outfitters), página del lodge', '«March / May»; «From late February till May» los peces entran desde el océano', 'Leída el 14/11/2026' ],
			[ 'Tres Amigos Outfitters, nota «Facts about Santa Cruz Steelhead»', 'Corrida de otoño que empieza a fines de febrero, con el mejor momento de mediados de marzo a mediados de mayo', '20/05/2021' ],
			[ 'Sportquest Holidays (agencia)', 'De fines de marzo a principios de mayo, con el pico en abril', 'Página modificada el 20/01/2026' ],
			[ 'Riva Rossi, Arguimbau y Pascual (2003)', 'Pico de entrada de los peces a comienzos de abril; pesquería de febrero a mayo', 'Temporada 2000' ],
			[ 'Llompart et al. (2024)', 'Esfuerzo de pesca con picos de marzo a mayo y de septiembre a octubre, que coinciden con la subida del steelhead y del chinook', 'Abril de 2018 a abril de 2019' ],
			[ 'Anexo I, Disposición 241/2026', 'Devolución obligatoria del steelhead del 15/03/2027 al 30/09/2027; salmón chinook del 01/08/2027 al 30/11/2027', 'En vigor desde el 1/10/2026' ],
			[ 'InterPatagonia', 'Piedra Buena es «la Capital Nacional de la Trucha Steelhead» y entre fines de marzo y principios de abril es sede de una fiesta internacional', 'Sin fecha en la página' ],
		] ],
		[ 'p', 'Lo único en lo que coinciden las fuentes es que se trata de una pesca del otoño austral. Las temporadas que publican los operadores caen en su mayor parte después del 15/03/2027, cuando empieza la devolución obligatoria. Para ver la trucha de toda la Patagonia mes por mes, mirá <a href="{n:cuando-pescar-trucha-patagonia}">cuándo pescar trucha en la Patagonia</a>.' ],
		[ 'h2', 'Qué dice el reglamento (Anexo I, 2026/27)' ],
		[ 'p', 'Esto es lo que dice la ficha «SANTA CRUZ (RÍO)» del Anexo I de la Disposición 241/2026. Citamos textual lo importante: no es un permiso ni una interpretación nuestra.' ],
		[ 'ul', [
			'<strong>Régimen general:</strong> «Apertura continuada durante todo el año. Prohibición expresa de uso de carnadas naturales, salvo excepciones. Se prohíbe el uso de lastres de plomo.»',
			'<strong>Anzuelo:</strong> «Obligatoriedad permanente del uso de un (1) anzuelo simple o doble con o sin rebaba, salvo excepciones.»',
			'<strong>Didymo:</strong> «Cuenca afectada por Didymo, desinfección obligatoria en estaciones fijas habilitadas antes de transitar a otros ambientes.»',
			'<strong>Steelhead:</strong> «Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead (Oncorhynchus mykiss)» y «A partir del 15/03/2027 y hasta el 30/09/27, se establece la Captura y Devolución Obligatoria para la Trucha Steelhead (Oncorhynchus mykiss)».',
			'<strong>Límite diario general:</strong> «Dos (2) ejemplares en total por pescador. Un (1) ejemplar de Perca (Percichthys trucha) y Un (1) ejemplar de Trucha Marrón (Salmo trutta).»',
			'<strong>Zona de pesca de costa autorizada:</strong> «desde el sector de bajada a la ribera ubicado a la altura del Monumento Héroes de Malvinas, en proximidades de la calle Bella Vista hasta el cruce del Interconectado Provincial sobre ambas márgenes (Norte y Sur)», e «incluye la Isla Pavón en su margen desde el Puente sobre Ruta Nacional N°3 hasta la zona denominada localmente como “La Noria”».',
			'<strong>Salmón chinook:</strong> «Temporada acotada desde el 01/08/2027 hasta el 30/11/2027», con señuelo libre y un anzuelo simple o doble; «Límite Diario (LD): Un (1) ejemplar en total por pescador; el resto de las especies capturadas incidentalmente deben ser devueltas de forma obligatoria».',
			'<strong>Róbalo:</strong> «Habilitación continua durante todo el año», con spinning, flycasting o línea de fondo de hasta tres anzuelos y carnada libre, sin plomo; «Hasta diez (10) ejemplares por pescador por jornada», con talla mínima de treinta centímetros de longitud total; «Se autoriza de manera exclusiva el sacrificio del róbalo, debiendo liberarse las demás especies capturadas».',
			'<strong>Trolling:</strong> «Autorizado de manera exclusiva para Guías de Pesca debidamente registrados en el R.P.A.T. que cuenten con unidades flotantes motorizadas homologadas y cumpliendo con los requisitos del Artículo 16° del Reglamento General», en dos tramos: del puente de la Ruta Nacional 3 a la toma de agua de Puerto Santa Cruz, y de la Chacra de Teira (Tapera Los Frutales) al cruce del Interconectado.',
			'<strong>Motores (art. 16):</strong> «Queda prohibida la navegación con motor en todos los ríos de la Provincia (Consultar excepciones en el Listado de Ambientes, Anexo I).»',
			'<strong>Veda total en las obras hidroeléctricas:</strong> el tramo «desde la Estancia El Rincón (zona de “Los Laberintos”) hasta el sector denominado “La Barrancosa”», delimitado por los campamentos de obra de Cóndor Cliff a los 70° 07\' O y el punto fijado en los 70° 10\' 00\'\' O.',
		] ],
		[ 'p', 'Fuente: Disposición 241/2026 (22/9/2026), Boletín Oficial de Santa Cruz n.º 6164 (8/10/2026) y n.º 6166 (15/10/2026), en vigor desde el 1/10/2026; texto del Anexo I leído el 13/11/2026. Sobre el steelhead, el Anexo dice solo lo citado arriba: no lo presentamos como un permiso para quedarse con un pez. Si tu salida cae antes del 15/03/2027, preguntale a tu guía o a la Secretaría cómo se aplica el cupo.' ],
		[ 'h2', 'Permiso' ],
		[ 'p', 'Hace falta el permiso de pesca de Santa Cruz, que se tramita en línea en el portal de Pesca y Acuicultura. Para un extranjero no residente en 2026/27: <strong>$ 90.000 por un día, $ 300.000 por una semana y $ 450.000 por la temporada</strong> (unos US$ 58, US$ 194 y US$ 291 con US$ 1 = $ 1.545, del 8 de noviembre de 2026). Valores del portal oficial, leídos el 4 de noviembre de 2026. El trolling es un permiso adicional. Comparamos los permisos de todas las provincias en <a href="{n:licencias-extranjeros-argentina}">licencia de pesca para extranjeros en Argentina 2026/27</a>.' ],
		[ 'p', 'Si el permiso viene dentro del paquete depende de la agencia: Where Wise Men Fish (página del 5/03/2025) y Sportquest Holidays (página de 2026) lo incluyen; Fly Water Travel (febrero de 2024) no lo lista entre lo incluido. La página de Los Plateados no lo aclara: preguntalo antes de reservar.' ],
		[ 'h2', 'Cómo llegar' ],
		[ 'p', 'Según Welcome Argentina, Piedra Buena está a 240 km de Río Gallegos; según Visit Argentina (Inprotur) se llega «en ómnibus o en vehículo particular por la Ruta Nacional 3». Tres Amigos Outfitters dice, para el conjunto de sus programas, que su gente recibe a los pasajeros en los aeropuertos de El Calafate (FTE) o de Río Gallegos (RGL), y en una nota de 2021 describe el trayecto desde Buenos Aires: vuelo de Aeroparque a Río Gallegos y traslado al lodge. Sportquest Holidays (2026) cuenta unas 3,5 horas por ruta desde Río Gallegos, y Where Wise Men Fish (página del 5/03/2025) ofrece traslados desde Río Gallegos o El Calafate. Visit Argentina describe un río «de quinientos metros de ancho» junto a la ciudad; la isla Pavón, donde se instaló el comandante Piedra Buena en 1859, tiene hoy el museo de la ciudad. Para El Calafate, mirá <a href="{g:el-calafate}">la guía de pesca de El Calafate</a>, y para el aeropuerto de llegada, <a href="{g:rio-gallegos}">la guía de Río Gallegos</a>.' ],
		[ 'h2', 'Quién guía en el río' ],
		[ 'p', 'Encontramos un solo lodge con página propia sobre el río: <strong>Los Plateados</strong>, de Tres Amigos Outfitters. Las demás páginas que lo venden son agencias. No encontramos guías independientes de Piedra Buena con página propia. Abajo está la tarjeta de Los Plateados, con lo que su página dice; no tiene nada confirmado con el operador.' ],
		[ 'p', '<strong>Cuánto cuesta.</strong> La tarifa que publica Los Plateados en su página no indica el año, por eso no la mostramos. Como referencia, Where Wise Men Fish (página del 5/03/2025) publica una tarifa de US$ 4.990 por la semana completa y US$ 2.990 por media semana: incluye siete noches con pensión completa, seis días de pesca, traslados desde Río Gallegos o El Calafate, comidas, vino y cerveza de la casa y el permiso de pesca. No sabemos a qué temporada corresponde esa tarifa, así que no es el precio de 2027. Fly Water Travel publicó en febrero de 2024 la misma cifra, US$ 4.990 por persona en un programa de seis noches y seis días, sin el permiso entre lo incluido. Para un precio vigente, <a href="{viaje}">pedinos el viaje</a>.' ],
		[ 'h2', 'Qué llevar' ],
		[ 'p', 'Tres Amigos Outfitters recomienda en una nota publicada el 4/6/2021, para su lodge:' ],
		[ 'ul', [
			'Caña de dos manos (Spey) de líneas 8 a 9, línea tipo Skagit y puntas hundidoras de distinto largo (una de 12 pies T-14 «para todo uso»), tippet de 15 libras y moscas del 2 al 6.',
			'Waders respirables y botas de suela de goma: la suela de fieltro es aceptable, pero no las botas con suelas intercambiables o clavos de metal.',
			'Pinzas para sacar el anzuelo y aplastar la rebaba, cortahilos y bastón de vadeo.',
		] ],
		[ 'p', 'La misma nota dice que se pesca sobre todo desde la margen norte (la izquierda del río) y que el ancho del río y el viento piden dominar dos lances: el Snap-T con viento de frente y el Reverse Double Spey con viento a favor.' ],
		[ 'h2', 'Las represas' ],
		[ 'p', 'El propio Anexo I cierra por completo el tramo afectado por «las obras civiles de los aprovechamientos hidroeléctricos» (arriba). Llompart et al. (2024), que contaron pescadores cerca de Piedra Buena entre abril de 2018 y abril de 2019, escriben que aguas arriba «se están construyendo dos represas» y que los pescadores entrevistados las consideraban una amenaza importante para la pesquería. Tres Amigos Outfitters, en una nota del 26/5/2021, opinaba que las obras podían demorarse de tres a cinco años más. Los plazos cambian: no los damos por hechos.' ],
		[ 'h2', 'Para leer antes de ir' ],
		[ 'ul', [
			'<a href="{esp:trucha}">La trucha</a>: la especie, sus tamaños y los récords.',
			'<a href="{n:cuando-pescar-trucha-patagonia}">Cuándo pescar trucha en la Patagonia</a>.',
			'<a href="{n:licencias-extranjeros-argentina}">Licencia de pesca para extranjeros en Argentina 2026/27</a>.',
			'<a href="{g:el-calafate}">Pesca en El Calafate</a>, en el lago Argentino.',
			'<a href="{g:rio-gallegos}">Pesca en Río Gallegos</a>, la trucha marrón de mar.',
		] ],
		[ 'p', '<strong>¿Querés que te ayudemos a elegir?</strong> Decinos qué querés pescar y cuándo en <a href="{viaje}">el formulario del viaje</a> y consultamos a los operadores que encajan; te respondemos con las opciones, sin costo ni compromiso.' ],
		[ 'pend', 'tarifa de Los Plateados con año (su página no lo trae) y qué incluye, incluida la licencia; qué tramo del río pesca el lodge frente a la zona de costa del Anexo I; permiso de trolling; guías independientes de Piedra Buena; fecha de la fiesta del steelhead 2027; una sola semana «mejor» (las fuentes no coinciden); foto con su crédito (foto pendiente).' ],
	],
	'campos' => [
		'h1' => 'Pesca de steelhead en el río Santa Cruz (Piedra Buena): reglas, permiso y lodge',
		'bajada' => 'La trucha arco iris que sube del mar por el río Santa Cruz. Qué dice el Anexo I 2026/27, cuándo se sale a pescar, qué permiso hace falta y lo que publica el único lodge que encontramos.',
		'modalidades' => "Mosca (flycast)\nSpinning y baitcasting\nTrolling (solo guías R.P.A.T.)",
		'epoca' => 'Fines de febrero o marzo a mayo para el steelhead, según los operadores; el río está abierto todo el año.',
		'seo_title' => 'Pesca de steelhead en el río Santa Cruz: reglas y permiso',
		'seo_description' => 'Steelhead en el río Santa Cruz (Piedra Buena): reglas del Anexo I 2026/27, permiso, cuándo ir según las fuentes y el lodge que encontramos.',
		'reglamento_resumen' => 'Anexo I de la Disposición 241/2026, ficha «SANTA CRUZ (RÍO)»: «Apertura continuada durante todo el año», sin carnadas naturales (salvo excepciones) ni lastres de plomo, un anzuelo simple o doble; «Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead» y captura y devolución obligatoria de ese pez del 15/03/2027 al 30/09/2027; límite diario general de dos ejemplares (una perca y una trucha marrón); salmón chinook del 01/08/2027 al 30/11/2027, un ejemplar por día; róbalo con su propio régimen; trolling solo para guías registrados en el R.P.A.T. en dos tramos; navegación con motor prohibida en los ríos (art. 16, con las excepciones del Anexo I); Didymo. Texto leído del Boletín Oficial de Santa Cruz (n.º 6164 y n.º 6166) el 13/11/2026.',
		'temporada' => [
			[ 'meses' => 'Todo el año', 'que' => 'El río Santa Cruz está abierto todo el año («Apertura continuada durante todo el año»), con las reglas por especie de abajo.' ],
			[ 'meses' => 'Fines de febrero o marzo a mayo', 'que' => 'Steelhead según los operadores: Los Plateados dice «March / May» y que desde fines de febrero los peces entran desde el océano; Sportquest Holidays, de fines de marzo a principios de mayo, con el pico en abril.' ],
			[ 'meses' => '15/03/2027 al 30/09/2027', 'que' => 'Captura y devolución obligatoria del steelhead (Anexo I, Disposición 241/2026).' ],
			[ 'meses' => '01/08/2027 al 30/11/2027', 'que' => 'Temporada acotada del salmón chinook, un ejemplar por pescador y por día (Anexo I, Disposición 241/2026).' ],
			[ 'meses' => 'Antes de cada salida', 'que' => 'Las reglas cambian cada temporada: confirmalas con la Secretaría de Estado de Pesca y Acuicultura o con tu guía.' ],
		],
		'aguas' => [
			[ 'nombre' => 'Río Santa Cruz, zona de Piedra Buena', 'nota' => 'La zona de pesca de costa autorizada del Anexo I va de la bajada a la ribera del Monumento Héroes de Malvinas (calle Bella Vista) al cruce del Interconectado Provincial, e incluye la isla Pavón. Consultá el texto completo arriba.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => '¿Cuándo se pesca el steelhead en el río Santa Cruz?', 'respuesta' => 'Los operadores lo ubican en el otoño austral: Los Plateados (Tres Amigos Outfitters) dice «March / May» y que desde fines de febrero hasta mayo los peces entran desde el océano; Sportquest Holidays, de fines de marzo a principios de mayo, con el pico en abril. Riva Rossi, Arguimbau y Pascual (2003) midieron el pico de entrada a comienzos de abril, en la temporada 2000, y Llompart et al. (2024) contaron más esfuerzo de pesca de marzo a mayo. El río está abierto todo el año, pero del 15/03/2027 al 30/09/2027 el steelhead es de captura y devolución obligatoria (Anexo I de la Disposición 241/2026).' ],
			[ 'pregunta' => '¿Puedo quedarme con un steelhead?', 'respuesta' => 'El Anexo I de la Disposición 241/2026 dice, textual: «Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead (Oncorhynchus mykiss)» y «A partir del 15/03/2027 y hasta el 30/09/27, se establece la Captura y Devolución Obligatoria para la Trucha Steelhead». No lo presentamos como un permiso para sacrificar un pez: si tu salida cae antes del 15/03/2027, preguntale a tu guía o a la Secretaría de Estado de Pesca y Acuicultura. Las temporadas que publican los operadores caen en su mayor parte después de esa fecha.' ],
			[ 'pregunta' => '¿Qué permiso necesito y cuánto cuesta?', 'respuesta' => 'El permiso de pesca de Santa Cruz, que se tramita en línea en el portal de Pesca y Acuicultura. Para un extranjero no residente en 2026/27: $ 90.000 por un día, $ 300.000 por una semana y $ 450.000 por la temporada (unos US$ 58, US$ 194 y US$ 291 con US$ 1 = $ 1.545, del 8/11/2026). Valores del portal oficial, leídos el 4/11/2026. El trolling es un permiso adicional.' ],
			[ 'pregunta' => '¿Cuánto cuesta una semana de steelhead en el río Santa Cruz?', 'respuesta' => 'La página de Los Plateados publica una tarifa sin año, por eso no la mostramos: precio a consultar. Como referencia, Where Wise Men Fish (página del 5/03/2025) publica una tarifa de US$ 4.990 por la semana completa y US$ 2.990 por media semana, con siete noches de pensión completa, seis días de pesca, traslados desde Río Gallegos o El Calafate, comidas, vino y cerveza de la casa y permiso; Fly Water Travel publicó la misma cifra, US$ 4.990, en febrero de 2024 para seis noches y seis días. No son precios de 2027: pedí uno actual.' ],
			[ 'pregunta' => '¿El permiso de pesca viene incluido en el paquete?', 'respuesta' => 'Depende de la agencia. Where Wise Men Fish (página del 5/03/2025) y Sportquest Holidays (página de 2026) lo incluyen; Fly Water Travel (febrero de 2024) no lo lista entre lo incluido, y la página de Los Plateados no lo aclara. Preguntalo antes de reservar.' ],
			[ 'pregunta' => '¿Se puede pescar con carnada o con plomo?', 'respuesta' => 'Para el steelhead y el resto de las especies de salmónidos, no: el Anexo I dice «Prohibición expresa de uso de carnadas naturales, salvo excepciones. Se prohíbe el uso de lastres de plomo.» y fija «un (1) anzuelo simple o doble con o sin rebaba». El róbalo tiene su propio régimen, con carnada libre y sin plomo.' ],
			[ 'pregunta' => '¿Se puede pescar salmón chinook en el río Santa Cruz?', 'respuesta' => 'Del 01/08/2027 al 30/11/2027, con un ejemplar por día y por pescador; las demás especies que se capturen se devuelven. La zona de costa va de la bajada urbana de la calle Bella Vista al cruce del Interconectado Provincial (Anexo I de la Disposición 241/2026). Llompart et al. (2024) ubican el pico de esfuerzo de septiembre y octubre en la subida del chinook; no encontramos un operador que venda chinook en el tramo bajo.' ],
			[ 'pregunta' => '¿Se puede pescar róbalo en el río Santa Cruz?', 'respuesta' => 'Sí, todo el año, con spinning, mosca o línea de fondo con hasta tres anzuelos y carnada libre, sin plomo. El cupo es de diez por pescador y por jornada, con talla mínima de 30 cm, y es la única especie que se puede sacrificar. La zona va del Monumento Héroes de Malvinas, cerca de la calle Bella Vista, hasta el alambrado de la Estancia La Esperanza, en la margen norte (Anexo I de la Disposición 241/2026).' ],
			[ 'pregunta' => '¿Se puede navegar con motor en el río Santa Cruz?', 'respuesta' => 'El artículo 16 del Reglamento General dice: «Queda prohibida la navegación con motor en todos los ríos de la Provincia (Consultar excepciones en el Listado de Ambientes, Anexo I)». Para el río Santa Cruz, el Anexo I autoriza el trolling de manera exclusiva a guías registrados en el R.P.A.T., con unidades motorizadas homologadas, en dos tramos. Cómo se aplica a tu salida, preguntalo al operador o a la Secretaría de Estado de Pesca y Acuicultura.' ],
			[ 'pregunta' => '¿Cómo llego a Piedra Buena y al río?', 'respuesta' => 'Piedra Buena está a unos 240 km de Río Gallegos por la Ruta Nacional 3, según Welcome Argentina, y se llega en ómnibus o en auto (Visit Argentina). Según los operadores, reciben a los pasajeros en los aeropuertos de Río Gallegos (RGL) o de El Calafate (FTE); Sportquest Holidays cuenta unas 3,5 horas por ruta desde Río Gallegos.' ],
			[ 'pregunta' => '¿Qué tamaño tiene el steelhead del río Santa Cruz?', 'respuesta' => 'Las cifras son de operadores y no coinciden: Tres Amigos Outfitters habla, en una nota de 2021 sobre sus registros desde 2014, de un promedio de 8 libras y un máximo de 21,5 libras, y su página del lodge dice que pueden superar las 20 libras; Sportquest Holidays (2026) da de 5 a 29 libras, con un promedio de 12. El trabajo de Riva Rossi, Arguimbau y Pascual (2003) midió siete peces radiomarcados de 490 a 780 mm de longitud furcal; no da pesos.' ],
			[ 'pregunta' => '¿De dónde viene el steelhead del río Santa Cruz?', 'respuesta' => 'Según Lázari et al. (2024), la trucha arco iris se sembró en la cuenca entre 1906 y 1910, presumiblemente desde California u Oregón. Riva Rossi, Lessa y Pascual (2004) analizaron su ADN mitocondrial y señalaron al río McCloud, California, como el origen más probable, aunque Lázari et al. (2024) concluyen que el McCloud no fue la única fuente; Pascual et al. (2001) documentaron por primera vez la anadromía de esta trucha introducida. La Disposición 241/2026 dice, en sus considerandos, que es «la única población silvestre de linaje atlántico en el continente americano».' ],
		],
	],
	'fuentes' => [
		[ 'Boletín Oficial de Santa Cruz: Disposición 241/2026 del 22/9/2026, Reglamento General de Pesca Deportiva Continental 2026/2027 (BO 6164, 8/10/2026; leída el 13/11/2026)', $u_bo1, 1 ],
		[ 'Boletín Oficial de Santa Cruz: Disposición 241/2026, tercera parte (BO 6166, 15/10/2026; leída el 13/11/2026)', $u_bo2, 1 ],
		[ 'Boletín Oficial de Santa Cruz: Disposición 241/2026, texto del reglamento y del Anexo I (PDF)', $u_pdf, 1 ],
		[ 'Pesca y Acuicultura de Santa Cruz: Anexo I, ambientes 2026/27', $u_amb, 1 ],
		[ 'Pesca y Acuicultura de Santa Cruz: valores de los permisos (leídos el 4/11/2026)', $u_val, 1 ],
		[ 'Lázari et al. (2024): Ancestry and genetic structure of resident and anadromous rainbow trout in Argentina, Journal of Fish Biology', $u_lz, 0 ],
		[ 'Riva Rossi, Lessa y Pascual (2004): The origin of introduced rainbow trout in the Santa Cruz River, Patagonia, Argentina, as inferred from mitochondrial DNA, Canadian Journal of Fisheries and Aquatic Sciences 61:1095-1101', $u_rr4, 0 ],
		[ 'Riva Rossi, Arguimbau y Pascual (2003): La migración reproductiva de la trucha arco iris anádroma del río Santa Cruz mediante radio-marcado, Ecología Austral 13', $u_rr3, 0 ],
		[ 'Llompart et al. (2024): La pesca recreativa en la cuenca baja del río Santa Cruz ante escenarios de represamiento, Ecología Austral 34(2)', $u_ll, 0 ],
		[ 'Welcome Argentina: Comandante Luis Piedra Buena', $u_wa, 0 ],
		[ 'Visit Argentina (Inprotur): Comandante Luis Piedrabuena', $u_at, 0 ],
		[ 'InterPatagonia: Por el río Santa Cruz (Cte. Luis Piedra Buena)', $u_ip, 0 ],
		[ 'Tres Amigos Outfitters: Los Plateados (operador; leída el 14/11/2026)', $u_lp, 0 ],
		[ 'Tres Amigos Outfitters: Santa Cruz fishing essentials (equipo, 4/6/2021)', $u_ess, 0 ],
		[ 'Tres Amigos Outfitters: Facts about Santa Cruz Steelhead (20/5/2021)', $u_fact, 0 ],
		[ 'Tres Amigos Outfitters: Santa Cruz steelhead 2021 season report (26/5/2021)', $u_rep, 0 ],
		[ 'Sportquest Holidays: Santa Cruz River steelhead (agencia; página modificada el 20/1/2026)', $u_sq, 0 ],
		[ 'Where Wise Men Fish: Santa Cruz steelhead (agencia; tarifa de la página del 5/3/2025)', $u_ww, 0 ],
		[ 'Fly Water Travel: blog del 21/2/2024 (agencia; tarifa)', $u_fwt, 0 ],
	],
];

// =====================================================================================================================================
// ENGLISH
// =====================================================================================================================================
$G['en'] = [
	'titulo' => 'Río Santa Cruz (Piedra Buena)',
	'slug'   => 'fishing-the-santa-cruz-river',
	'autoridad' => 'Secretaría de Estado de Pesca y Acuicultura de Santa Cruz',
	'cuerpo' => [
		[ 'p', 'The <strong>Santa Cruz River</strong>, in Santa Cruz province, is the river of a rainbow trout that goes down to the sea and comes back up: the <strong>steelhead</strong>. Disposición 241/2026 of the provincial Fisheries and Aquaculture Secretariat calls it, in its recitals, “la única población silvestre de linaje atlántico en el continente americano” (“the only wild population of Atlantic lineage in the American continent”; quoted word for word, our translation). <strong>Comandante Luis Piedra Buena</strong> (Piedra Buena) is the town on the river, about 240 km from Río Gallegos on National Route 3, according to Welcome Argentina. Annex I of the 2026/27 regulation leaves the river open all year and sets “un (1) ejemplar de Trucha Steelhead” (one steelhead) per angler per season, with mandatory catch and release from 15/03/2027 to 30/09/2027 (the full text is below). The operators and the studies we read place steelhead fishing in the austral autumn, from late February or March to May. The only lodge with its own page on the river that we found is <strong>Los Plateados</strong>, run by Tres Amigos Outfitters; its card below shows what it publishes.' ],
		[ 'h2', 'Key facts' ],
		[ 'table', [ 'Fact', 'What it says', 'Source' ], [
			[ 'River and base town', 'About 386 km from Lake Argentino to the Atlantic; base town: Comandante Luis Piedra Buena', 'Sportquest Holidays (2026 page)' ],
			[ 'Distance', 'About 240 km from Río Gallegos on National Route 3', 'Welcome Argentina' ],
			[ 'Species with their own rules', 'Steelhead, chinook salmon and róbalo; perch and brown trout fall under the daily limit', 'Annex I, Disposición 241/2026' ],
			[ 'Steelhead', 'Quota of one (1) fish per angler per season; mandatory catch and release from 15/03/2027 to 30/09/2027', 'Annex I, Disposición 241/2026' ],
			[ 'Chinook salmon', '01/08/2027 to 30/11/2027; one (1) fish per angler per day', 'Annex I, Disposición 241/2026' ],
			[ 'Opening', 'All year; no natural bait (with exceptions) and no lead weights', 'Annex I, Disposición 241/2026' ],
			[ 'When, according to the operators', 'From late February or March to May', 'Tres Amigos Outfitters and Sportquest Holidays' ],
			[ 'Santa Cruz permit (non-resident foreigner, 2026/27)', 'AR$ 90,000 per day, AR$ 300,000 per week, AR$ 450,000 per season', 'Santa Cruz Fisheries and Aquaculture portal (read 4/11/2026)' ],
			[ 'Lodge programme', 'Six fishing days, Saturday to Friday, for 4 anglers', 'Tres Amigos Outfitters (read 14/11/2026)' ],
			[ 'An agency’s price reference', 'US$ 4,990 for a full week and US$ 2,990 for a half week', 'Where Wise Men Fish (page of 5/03/2025)' ],
		] ],
		[ 'h2', 'Where the Santa Cruz steelhead comes from' ],
		[ 'p', 'The rainbow trout is not native to the river. According to <a href="' . $u_lz . '" rel="noopener">Lázari et al. (2024, <em>Journal of Fish Biology</em>, DOI 10.1111/jfb.15722)</a>, imported fish, presumably from California or Oregon, were stocked in the Santa Cruz basin between 1906 and 1910, and by the 1920s there were naturalised populations well distributed across the basin. A mitochondrial DNA study (<a href="' . $u_rr4 . '" rel="noopener">Riva Rossi, Lessa and Pascual, 2004, <em>Canadian Journal of Fisheries and Aquatic Sciences</em> 61:1095-1101</a>) pointed to the McCloud River, California, as the most likely origin; Lázari et al. (2024) read those results as a mixture of several Californian sources and see a McCloud contribution that would not have been the only one. Lázari et al. relate the Santa Cruz fish to California and Oregon populations that have an anadromous component, and found that the river’s resident and anadromous fish form a single interbreeding population.' ],
		[ 'p', 'The first documented case of anadromy in this introduced trout is Pascual et al. (2001, <em>Transactions of the American Fisheries Society</em> 130:53-67). <a href="' . $u_rr3 . '" rel="noopener">Riva Rossi, Arguimbau and Pascual (2003, <em>Ecología Austral</em> 13:151-159)</a> sum it up this way: the Santa Cruz is “el único ambiente en el mundo donde la trucha arco iris introducida desarrolló poblaciones migratorias facultativas” (“the only environment in the world where introduced rainbow trout developed facultative migratory populations”; our translation), meaning that part of the fish complete their cycle in fresh water and part travel to the sea. That study followed seven fish with radio transmitters as they came up from the sea in the autumn of 2000: they measured between 490 and 780 mm fork length (no weights are given); entry peaked in early April; it describes a fishery from February to May, mostly in the first 50 km of the river; spawning began in August, peaking in September and October in water of 4 to 10 °C; and the tagged fish travelled from 0.6 to 279.5 km (Table 1).' ],
		[ 'h2', 'When to go: what the sources say' ],
		[ 'p', 'There is no official steelhead “season” date: the river is open all year and Annex I only marks when mandatory release applies. What follows are dates from operators and published studies, and they do not match week by week.' ],
		[ 'table', [ 'Source', 'What it says', 'Date of the source' ], [
			[ 'Los Plateados (Tres Amigos Outfitters), lodge page', '“March / May”; “From late February till May” the fish enter from the ocean', 'Read 14/11/2026' ],
			[ 'Tres Amigos Outfitters, post “Facts about Santa Cruz Steelhead”', 'An autumn run that starts in late February, with prime time from mid-March to mid-May', '20/05/2021' ],
			[ 'Sportquest Holidays (agency)', 'From the end of March to early May, peaking in April', 'Page modified 20/01/2026' ],
			[ 'Riva Rossi, Arguimbau and Pascual (2003)', 'Fish entry peaking in early April; fishery from February to May', '2000 season' ],
			[ 'Llompart et al. (2024)', 'Fishing effort with peaks from March to May and from September to October, matching the steelhead and chinook runs', 'April 2018 to April 2019' ],
			[ 'Annex I, Disposición 241/2026', 'Mandatory release of steelhead from 15/03/2027 to 30/09/2027; chinook salmon from 01/08/2027 to 30/11/2027', 'In force since 1/10/2026' ],
			[ 'InterPatagonia', 'Piedra Buena is “the National Capital of the Steelhead Trout” and hosts an international festival between late March and early April', 'No date on the page' ],
		] ],
		[ 'p', 'The only thing the sources agree on is that this is an austral-autumn fishery. The seasons operators publish fall mostly after 15/03/2027, when mandatory release starts. To see trout across all of Patagonia month by month, read <a href="{n:cuando-pescar-trucha-patagonia}">when to fish trout in Patagonia</a>.' ],
		[ 'h2', 'What the regulation says (Annex I, 2026/27)' ],
		[ 'p', 'This is what the “SANTA CRUZ (RÍO)” entry of Annex I of Disposición 241/2026 says. We quote the important parts word for word (the Spanish is the official text; the English is our translation): it is not a permit and not our interpretation.' ],
		[ 'ul', [
			'<strong>General regime:</strong> “Apertura continuada durante todo el año. Prohibición expresa de uso de carnadas naturales, salvo excepciones. Se prohíbe el uso de lastres de plomo.” (Open all year; natural bait expressly banned, with exceptions; lead weights banned.)',
			'<strong>Hook:</strong> “Obligatoriedad permanente del uso de un (1) anzuelo simple o doble con o sin rebaba, salvo excepciones.” (One single or double hook, barbed or barbless, with exceptions.)',
			'<strong>Didymo:</strong> “Cuenca afectada por Didymo, desinfección obligatoria en estaciones fijas habilitadas antes de transitar a otros ambientes.” (Basin affected by Didymo; mandatory disinfection at fixed stations before moving to other waters.)',
			'<strong>Steelhead:</strong> “Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead (Oncorhynchus mykiss)” (a total quota of one steelhead per angler per season) and “A partir del 15/03/2027 y hasta el 30/09/27, se establece la Captura y Devolución Obligatoria para la Trucha Steelhead (Oncorhynchus mykiss)” (from 15/03/2027 to 30/09/27, mandatory catch and release of steelhead).',
			'<strong>General daily limit:</strong> “Dos (2) ejemplares en total por pescador. Un (1) ejemplar de Perca (Percichthys trucha) y Un (1) ejemplar de Trucha Marrón (Salmo trutta).” (Two fish in total per angler: one perch and one brown trout.)',
			'<strong>Authorised shore fishing zone:</strong> “desde el sector de bajada a la ribera ubicado a la altura del Monumento Héroes de Malvinas, en proximidades de la calle Bella Vista hasta el cruce del Interconectado Provincial sobre ambas márgenes (Norte y Sur)”, which “incluye la Isla Pavón en su margen desde el Puente sobre Ruta Nacional N°3 hasta la zona denominada localmente como ‘La Noria’” (from the river access at the Héroes de Malvinas monument, near Bella Vista street, to the crossing of the Provincial Interconnected power line, on both banks; it includes Isla Pavón from the Route 3 bridge to the area locally called “La Noria”).',
			'<strong>Chinook salmon:</strong> “Temporada acotada desde el 01/08/2027 hasta el 30/11/2027”, with a free lure and one single or double hook; “Límite Diario (LD): Un (1) ejemplar en total por pescador; el resto de las especies capturadas incidentalmente deben ser devueltas de forma obligatoria” (daily limit of one fish per angler; every other species caught incidentally must be released).',
			'<strong>Róbalo:</strong> “Habilitación continua durante todo el año”, with spinning, flycasting or a bottom line with up to three hooks and free bait, no lead; “Hasta diez (10) ejemplares por pescador por jornada” (up to ten per angler per day), minimum size thirty centimetres total length; “Se autoriza de manera exclusiva el sacrificio del róbalo, debiendo liberarse las demás especies capturadas” (only róbalo may be kept; all other species must be released).',
			'<strong>Trolling:</strong> “Autorizado de manera exclusiva para Guías de Pesca debidamente registrados en el R.P.A.T. que cuenten con unidades flotantes motorizadas homologadas y cumpliendo con los requisitos del Artículo 16° del Reglamento General” (authorised only for fishing guides registered in the R.P.A.T. with approved motorised floating units that meet Article 16), on two stretches: from the Route 3 bridge to the Puerto Santa Cruz water intake, and from the Chacra de Teira (Tapera Los Frutales) to the Interconnected power line crossing.',
			'<strong>Motors (Article 16):</strong> “Queda prohibida la navegación con motor en todos los ríos de la Provincia (Consultar excepciones en el Listado de Ambientes, Anexo I).” (Motor navigation is banned on all the province’s rivers; see the exceptions in the list of waters, Annex I.)',
			'<strong>Total closure at the hydroelectric works:</strong> the stretch “desde la Estancia El Rincón (zona de ‘Los Laberintos’) hasta el sector denominado ‘La Barrancosa’”, bounded by the Cóndor Cliff work camps at 70° 07\' W and the point fixed at 70° 10\' 00\'\' W.',
		] ],
		[ 'p', 'Source: Disposición 241/2026 (22/9/2026), Santa Cruz Official Gazette no. 6164 (8/10/2026) and no. 6166 (15/10/2026), in force since 1/10/2026; text of Annex I read on 13/11/2026. On steelhead, the Annex says only what is quoted above: we do not present it as a permit to keep a fish. If your trip falls before 15/03/2027, ask your guide or the Secretariat how the quota applies.' ],
		[ 'h2', 'Permit' ],
		[ 'p', 'You need the Santa Cruz fishing permit, bought online on the Fisheries and Aquaculture portal. For a non-resident foreigner in 2026/27: <strong>AR$ 90,000 for one day, AR$ 300,000 for a week and AR$ 450,000 for the season</strong> (about US$ 58, US$ 194 and US$ 291 at US$ 1 = AR$ 1,545, as of 8 November 2026). Values from the official portal, read on 4 November 2026. Trolling is an extra permit. We compare the permits of every province in <a href="{n:licencias-extranjeros-argentina}">fishing licence for foreigners in Argentina 2026/27</a>.' ],
		[ 'p', 'Whether the permit comes inside the package depends on the agency: Where Wise Men Fish (page of 5/03/2025) and Sportquest Holidays (2026 page) include it; Fly Water Travel (February 2024) does not list it among what is included. The Los Plateados page does not say: ask before you book.' ],
		[ 'h2', 'Getting there' ],
		[ 'p', 'According to Welcome Argentina, Piedra Buena is 240 km from Río Gallegos; according to Visit Argentina (Inprotur) you get there “by bus or private vehicle on National Route 3”. Tres Amigos Outfitters says, for all its programmes, that its staff meet guests at the airports of El Calafate (FTE) or Río Gallegos (RGL), and a 2021 post describes the trip from Buenos Aires: a flight from Aeroparque to Río Gallegos and a transfer to the lodge. Sportquest Holidays (2026) counts about 3.5 hours by road from Río Gallegos, and Where Wise Men Fish (page of 5/03/2025) offers transfers from Río Gallegos or El Calafate. Visit Argentina describes a river “five hundred metres wide” by the town; Isla Pavón, where Commander Piedra Buena settled in 1859, now holds the city museum. For El Calafate, see <a href="{g:el-calafate}">our El Calafate fishing guide</a>, and for the arrival airport, <a href="{g:rio-gallegos}">our Río Gallegos guide</a>.' ],
		[ 'h2', 'Who guides on the river' ],
		[ 'p', 'We found a single lodge with its own page on the river: <strong>Los Plateados</strong>, run by Tres Amigos Outfitters. The other pages that sell it are agencies. We found no independent Piedra Buena guides with a page of their own. Below is the Los Plateados card, with what its page says; nothing on it is confirmed with the operator.' ],
		[ 'p', '<strong>What it costs.</strong> The rate Los Plateados publishes on its page carries no year, so we do not show it. As a reference, Where Wise Men Fish (page of 5/03/2025) publishes a rate of US$ 4,990 for a full week and US$ 2,990 for a half week: it includes seven nights full board, six fishing days, transfers from Río Gallegos or El Calafate, meals, house wine and beer and the fishing permit. We do not know which season that rate belongs to, so it is not the 2027 price. Fly Water Travel published the same figure in February 2024, US$ 4,990 per person for a six-night, six-day programme, without the permit among what is included. For a current price, <a href="{viaje}">ask us for the trip</a>.' ],
		[ 'h2', 'What to bring' ],
		[ 'p', 'Tres Amigos Outfitters recommends, in a post published on 4/6/2021, for its lodge:' ],
		[ 'ul', [
			'A two-handed (Spey) rod in 8 to 9 weight, a Skagit-style line and sink tips of different lengths (a 12-foot T-14 as an “all-rounder”), 15-pound tippet and flies in sizes 2 to 6.',
			'Breathable waders and rubber-soled boots: felt soles are acceptable, but not boots with interchangeable soles or metal studs.',
			'Pliers to remove the hook and crush the barb, nippers and a wading staff.',
		] ],
		[ 'p', 'The same post says the fishing is mostly done from the north bank (river left) and that the river’s width and the wind call for two casts: the Snap-T with an upstream wind and the Reverse Double Spey with a downstream wind.' ],
		[ 'h2', 'The dams' ],
		[ 'p', 'Annex I itself closes completely the stretch affected by “las obras civiles de los aprovechamientos hidroeléctricos” (the civil works of the hydroelectric projects; above). Llompart et al. (2024), who counted anglers near Piedra Buena between April 2018 and April 2019, write that two dams “are being built” upstream and that the anglers interviewed saw them as a major threat to the fishery. Tres Amigos Outfitters, in a post of 26/5/2021, thought the works could be delayed another three to five years. Timelines change: we do not take them as given.' ],
		[ 'h2', 'Read before you go' ],
		[ 'ul', [
			'<a href="{esp:trucha}">Trout</a>: the species, its sizes and records.',
			'<a href="{n:cuando-pescar-trucha-patagonia}">When to fish trout in Patagonia</a>.',
			'<a href="{n:licencias-extranjeros-argentina}">Fishing licence for foreigners in Argentina 2026/27</a>.',
			'<a href="{g:el-calafate}">Fishing in El Calafate</a>, on Lake Argentino.',
			'<a href="{g:rio-gallegos}">Fishing in Río Gallegos</a>, sea-run brown trout.',
		] ],
		[ 'p', '<strong>Want us to help you choose?</strong> Tell us what you want to fish and when in <a href="{viaje}">the trip form</a> and we ask the operators that fit; we reply with the options, at no cost and with no commitment.' ],
		[ 'pend', 'Los Plateados rate with a year (its page gives none) and what it includes, licence included; which stretch of the river the lodge fishes against the Annex I shore zone; trolling permit; independent Piedra Buena guides; the 2027 steelhead festival date; a single “best” week (the sources differ); photo with its credit (photo pending).' ],
	],
	'campos' => [
		'h1' => 'Steelhead fishing on the Santa Cruz River (Piedra Buena): rules, permit and lodge',
		'bajada' => 'The rainbow trout that runs up the Santa Cruz River from the sea. What Annex I 2026/27 says, when trips run, which permit you need and what the only lodge we found publishes.',
		'modalidades' => "Fly fishing (flycast)\nSpinning and baitcasting\nTrolling (R.P.A.T. guides only)",
		'epoca' => 'Late February or March to May for steelhead, according to the operators; the river is open all year.',
		'seo_title' => 'Steelhead fishing on the Santa Cruz River: rules, permit',
		'seo_description' => 'Steelhead on the Santa Cruz River (Piedra Buena): Annex I 2026/27 rules, permit, when to go according to the sources and the lodge we found.',
		'reglamento_resumen' => 'Annex I of Disposición 241/2026, “SANTA CRUZ (RÍO)” entry: “Apertura continuada durante todo el año” (open all year), no natural bait (with exceptions) and no lead weights, one single or double hook; “Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead” (a quota of one steelhead per angler per season) and mandatory catch and release of that fish from 15/03/2027 to 30/09/2027; general daily limit of two fish (one perch and one brown trout); chinook salmon from 01/08/2027 to 30/11/2027, one fish per day; róbalo under its own regime; trolling only for guides registered in the R.P.A.T. on two stretches; motor navigation banned on rivers (Article 16, with the Annex I exceptions); Didymo. Text read from the Santa Cruz Official Gazette (no. 6164 and no. 6166) on 13/11/2026.',
		'temporada' => [
			[ 'meses' => 'All year', 'que' => 'The Santa Cruz River is open all year (“Apertura continuada durante todo el año”), with the rules by species below.' ],
			[ 'meses' => 'Late February or March to May', 'que' => 'Steelhead according to the operators: Los Plateados says “March / May” and that from late February the fish enter from the ocean; Sportquest Holidays, from the end of March to early May, peaking in April.' ],
			[ 'meses' => '15/03/2027 to 30/09/2027', 'que' => 'Mandatory catch and release of steelhead (Annex I, Disposición 241/2026).' ],
			[ 'meses' => '01/08/2027 to 30/11/2027', 'que' => 'Short chinook salmon season, one fish per angler per day (Annex I, Disposición 241/2026).' ],
			[ 'meses' => 'Before every trip', 'que' => 'The rules change every season: confirm them with the Fisheries and Aquaculture Secretariat or your guide.' ],
		],
		'aguas' => [
			[ 'nombre' => 'Santa Cruz River, Piedra Buena area', 'nota' => 'The authorised shore fishing zone in Annex I runs from the river access at the Héroes de Malvinas monument (Bella Vista street) to the crossing of the Provincial Interconnected power line, and includes Isla Pavón. See the full text above.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'When is steelhead fishing on the Santa Cruz River?', 'respuesta' => 'The operators place it in the austral autumn: Los Plateados (Tres Amigos Outfitters) says “March / May” and that from late February to May the fish enter from the ocean; Sportquest Holidays, from the end of March to early May, peaking in April. Riva Rossi, Arguimbau and Pascual (2003) measured the entry peak in early April, in the 2000 season, and Llompart et al. (2024) counted more fishing effort from March to May. The river is open all year, but from 15/03/2027 to 30/09/2027 steelhead is mandatory catch and release (Annex I of Disposición 241/2026).' ],
			[ 'pregunta' => 'Can I keep a steelhead?', 'respuesta' => 'Annex I of Disposición 241/2026 says, word for word: “Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead (Oncorhynchus mykiss)” (a total quota of one steelhead per angler per season) and “A partir del 15/03/2027 y hasta el 30/09/27, se establece la Captura y Devolución Obligatoria para la Trucha Steelhead” (from 15/03/2027 to 30/09/27, mandatory catch and release). We do not present it as a permit to keep a fish: if your trip falls before 15/03/2027, ask your guide or the Fisheries and Aquaculture Secretariat. The seasons operators publish fall mostly after that date.' ],
			[ 'pregunta' => 'Which permit do I need and how much is it?', 'respuesta' => 'The Santa Cruz fishing permit, bought online on the Fisheries and Aquaculture portal. For a non-resident foreigner in 2026/27: AR$ 90,000 for one day, AR$ 300,000 for a week and AR$ 450,000 for the season (about US$ 58, US$ 194 and US$ 291 at US$ 1 = AR$ 1,545, as of 8/11/2026). Values from the official portal, read on 4/11/2026. Trolling is an extra permit.' ],
			[ 'pregunta' => 'How much does a week of steelhead fishing on the Santa Cruz River cost?', 'respuesta' => 'The Los Plateados page publishes a rate with no year, so we do not show it: price on request. As a reference, Where Wise Men Fish (page of 5/03/2025) publishes a rate of US$ 4,990 for a full week and US$ 2,990 for a half week, with seven nights full board, six fishing days, transfers from Río Gallegos or El Calafate, meals, house wine and beer and the permit; Fly Water Travel published the same figure, US$ 4,990, in February 2024 for six nights and six days. These are not 2027 prices: ask for a current one.' ],
			[ 'pregunta' => 'Is the fishing permit included in the package?', 'respuesta' => 'It depends on the agency. Where Wise Men Fish (page of 5/03/2025) and Sportquest Holidays (2026 page) include it; Fly Water Travel (February 2024) does not list it among what is included, and the Los Plateados page does not say. Ask before you book.' ],
			[ 'pregunta' => 'Can I fish with bait or with lead?', 'respuesta' => 'For steelhead and the other salmonids, no: Annex I says “Prohibición expresa de uso de carnadas naturales, salvo excepciones. Se prohíbe el uso de lastres de plomo.” (natural bait expressly banned, with exceptions; lead weights banned) and sets “un (1) anzuelo simple o doble con o sin rebaba” (one single or double hook, barbed or barbless). Róbalo has its own regime, with free bait and no lead.' ],
			[ 'pregunta' => 'Can I fish chinook salmon on the Santa Cruz River?', 'respuesta' => 'From 01/08/2027 to 30/11/2027, one fish per day per angler; other species caught must be released. The shore zone runs from the urban access at Bella Vista street to the crossing of the Provincial Interconnected power line (Annex I of Disposición 241/2026). Llompart et al. (2024) tie the September–October effort peak to the chinook run; we found no operator selling chinook on the lower river.' ],
			[ 'pregunta' => 'Can I fish róbalo on the Santa Cruz River?', 'respuesta' => 'Yes, all year, with spinning, fly or a bottom line with up to three hooks and free bait, no lead. The limit is ten per angler per day, minimum size 30 cm, and it is the only species that may be kept. The zone runs from the Héroes de Malvinas monument, near Bella Vista street, to the fence of Estancia La Esperanza on the north bank (Annex I of Disposición 241/2026).' ],
			[ 'pregunta' => 'Can I use a motor on the Santa Cruz River?', 'respuesta' => 'Article 16 of the General Regulation says: “Queda prohibida la navegación con motor en todos los ríos de la Provincia (Consultar excepciones en el Listado de Ambientes, Anexo I)” (motor navigation is banned on all the province’s rivers; see the exceptions in the list of waters, Annex I). For the Santa Cruz River, Annex I authorises trolling only for guides registered in the R.P.A.T., with approved motorised units, on two stretches. How it applies to your trip, ask the operator or the Fisheries and Aquaculture Secretariat.' ],
			[ 'pregunta' => 'How do I get to Piedra Buena and the river?', 'respuesta' => 'Piedra Buena is about 240 km from Río Gallegos on National Route 3, according to Welcome Argentina, and you get there by bus or car (Visit Argentina). According to the operators, they meet guests at the airports of Río Gallegos (RGL) or El Calafate (FTE); Sportquest Holidays counts about 3.5 hours by road from Río Gallegos.' ],
			[ 'pregunta' => 'How big is the Santa Cruz steelhead?', 'respuesta' => 'The figures come from operators and do not match: Tres Amigos Outfitters speaks, in a 2021 post on its records since 2014, of an average of 8 pounds and a maximum of 21.5 pounds, and its lodge page says they can exceed 20 pounds; Sportquest Holidays (2026) gives 5 to 29 pounds, with an average of 12. The study by Riva Rossi, Arguimbau and Pascual (2003) measured seven radio-tagged fish of 490 to 780 mm fork length; it gives no weights.' ],
			[ 'pregunta' => 'Where does the Santa Cruz steelhead come from?', 'respuesta' => 'According to Lázari et al. (2024), rainbow trout were stocked in the basin between 1906 and 1910, presumably from California or Oregon. Riva Rossi, Lessa and Pascual (2004) analysed its mitochondrial DNA and pointed to the McCloud River, California, as the most likely origin, although Lázari et al. (2024) conclude that the McCloud was not the only source; Pascual et al. (2001) first documented anadromy in this introduced trout. Disposición 241/2026 says, in its recitals, that it is “la única población silvestre de linaje atlántico en el continente americano” (the only wild population of Atlantic lineage in the American continent).' ],
		],
	],
	'fuentes' => [
		[ 'Santa Cruz Official Gazette: Disposición 241/2026 of 22/9/2026, General Regulation of Continental Sport Fishing 2026/2027 (BO 6164, 8/10/2026; read 13/11/2026)', $u_bo1, 1 ],
		[ 'Santa Cruz Official Gazette: Disposición 241/2026, part 3 (BO 6166, 15/10/2026; read 13/11/2026)', $u_bo2, 1 ],
		[ 'Santa Cruz Official Gazette: Disposición 241/2026, text of the regulation and of Annex I (PDF)', $u_pdf, 1 ],
		[ 'Santa Cruz Fisheries and Aquaculture: Annex I, waters 2026/27', $u_amb, 1 ],
		[ 'Santa Cruz Fisheries and Aquaculture: permit values (read 4/11/2026)', $u_val, 1 ],
		[ 'Lázari et al. (2024): Ancestry and genetic structure of resident and anadromous rainbow trout in Argentina, Journal of Fish Biology', $u_lz, 0 ],
		[ 'Riva Rossi, Lessa and Pascual (2004): The origin of introduced rainbow trout in the Santa Cruz River, Patagonia, Argentina, as inferred from mitochondrial DNA, Canadian Journal of Fisheries and Aquatic Sciences 61:1095-1101', $u_rr4, 0 ],
		[ 'Riva Rossi, Arguimbau and Pascual (2003): the spawning migration of anadromous rainbow trout in the Santa Cruz River through radio-tracking, Ecología Austral 13', $u_rr3, 0 ],
		[ 'Llompart et al. (2024): recreational fishing in the lower Santa Cruz basin in the face of damming scenarios, Ecología Austral 34(2)', $u_ll, 0 ],
		[ 'Welcome Argentina: Comandante Luis Piedra Buena', $u_wa, 0 ],
		[ 'Visit Argentina (Inprotur): Comandante Luis Piedrabuena', $u_at, 0 ],
		[ 'InterPatagonia: Por el río Santa Cruz (Cte. Luis Piedra Buena)', $u_ip, 0 ],
		[ 'Tres Amigos Outfitters: Los Plateados (operator; read 14/11/2026)', $u_lp, 0 ],
		[ 'Tres Amigos Outfitters: Santa Cruz fishing essentials (gear, 4/6/2021)', $u_ess, 0 ],
		[ 'Tres Amigos Outfitters: Facts about Santa Cruz Steelhead (20/5/2021)', $u_fact, 0 ],
		[ 'Tres Amigos Outfitters: Santa Cruz steelhead 2021 season report (26/5/2021)', $u_rep, 0 ],
		[ 'Sportquest Holidays: Santa Cruz River steelhead (agency; page modified 20/1/2026)', $u_sq, 0 ],
		[ 'Where Wise Men Fish: Santa Cruz steelhead (agency; rate from the page of 5/3/2025)', $u_ww, 0 ],
		[ 'Fly Water Travel: blog of 21/2/2024 (agency; rate)', $u_fwt, 0 ],
	],
];

// =====================================================================================================================================
// PORTUGUÊS
// =====================================================================================================================================
$G['pt'] = [
	'titulo' => 'Río Santa Cruz (Piedra Buena)',
	'slug'   => 'pesca-no-rio-santa-cruz',
	'autoridad' => 'Secretaría de Estado de Pesca y Acuicultura de Santa Cruz',
	'cuerpo' => [
		[ 'p', 'O <strong>rio Santa Cruz</strong>, na província de Santa Cruz, é o rio de uma truta arco-íris que desce ao mar e volta a subir: o <strong>steelhead</strong>. A Disposición 241/2026 da Secretaria de Pesca e Aquicultura da província, em seus considerandos, a chama de «la única población silvestre de linaje atlántico en el continente americano» («a única população silvestre de linhagem atlântica no continente americano»; citação textual, tradução nossa). <strong>Comandante Luis Piedra Buena</strong> (Piedra Buena) é a cidade às margens do rio, a cerca de 240 km de Río Gallegos pela Ruta Nacional 3, segundo o Welcome Argentina. O Anexo I do regulamento 2026/27 deixa o rio aberto o ano todo e fixa «un (1) ejemplar de Trucha Steelhead» (um steelhead) por pescador e por temporada, com captura e devolução obrigatória de 15/03/2027 a 30/09/2027 (o texto completo está mais abaixo). Os operadores e os trabalhos que lemos situam a pesca do steelhead no outono austral, do fim de fevereiro ou março a maio. O único lodge com página própria sobre o rio que encontramos é o <strong>Los Plateados</strong>, da Tres Amigos Outfitters; abaixo está o cartão dele, com o que publica.' ],
		[ 'h2', 'Dados principais' ],
		[ 'table', [ 'Dado', 'O que diz', 'Fonte' ], [
			[ 'Rio e cidade-base', 'Cerca de 386 km do lago Argentino ao Atlântico; cidade-base: Comandante Luis Piedra Buena', 'Sportquest Holidays (página de 2026)' ],
			[ 'Distância', 'Cerca de 240 km de Río Gallegos pela Ruta Nacional 3', 'Welcome Argentina' ],
			[ 'Espécies com regras próprias', 'Steelhead, salmão-chinook e robalo; perca e truta marrom entram no limite diário', 'Anexo I, Disposición 241/2026' ],
			[ 'Steelhead', 'Cota de um (1) exemplar por pescador e por temporada; captura e devolução obrigatória de 15/03/2027 a 30/09/2027', 'Anexo I, Disposición 241/2026' ],
			[ 'Salmão-chinook', 'De 01/08/2027 a 30/11/2027; um (1) exemplar por pescador e por dia', 'Anexo I, Disposición 241/2026' ],
			[ 'Abertura', 'O ano todo; sem isca natural (salvo exceções) e sem chumbo', 'Anexo I, Disposición 241/2026' ],
			[ 'Quando, segundo os operadores', 'Do fim de fevereiro ou março a maio', 'Tres Amigos Outfitters e Sportquest Holidays' ],
			[ 'Licença de Santa Cruz (estrangeiro não residente, 2026/27)', 'AR$ 90.000 por dia, AR$ 300.000 por semana, AR$ 450.000 por temporada', 'Portal de Pesca e Aquicultura de Santa Cruz (lido em 4/11/2026)' ],
			[ 'Programa do lodge', 'Seis dias de pesca, de sábado a sexta, para 4 pescadores', 'Tres Amigos Outfitters (lida em 14/11/2026)' ],
			[ 'Referência de preço de uma agência', 'US$ 4.990 por semana completa e US$ 2.990 por meia semana', 'Where Wise Men Fish (página de 5/03/2025)' ],
		] ],
		[ 'h2', 'De onde vem o steelhead do Santa Cruz' ],
		[ 'p', 'A truta arco-íris não é nativa do rio. Segundo <a href="' . $u_lz . '" rel="noopener">Lázari et al. (2024, <em>Journal of Fish Biology</em>, DOI 10.1111/jfb.15722)</a>, exemplares importados, presumivelmente da Califórnia ou do Oregon, foram soltos na bacia do Santa Cruz entre 1906 e 1910, e na década de 1920 já havia populações naturalizadas e bem distribuídas por toda a bacia. Um estudo de DNA mitocondrial (<a href="' . $u_rr4 . '" rel="noopener">Riva Rossi, Lessa e Pascual, 2004, <em>Canadian Journal of Fisheries and Aquatic Sciences</em> 61:1095-1101</a>) apontou o rio McCloud, na Califórnia, como a origem mais provável; Lázari et al. (2024) leem esses resultados como uma mistura de várias fontes californianas e veem uma contribuição do McCloud que não teria sido a única. Lázari et al. relacionam os peixes do Santa Cruz com populações da Califórnia e do Oregon que têm um componente anádromo, e concluíram que os peixes residentes e os anádromos do rio formam uma única população que se cruza entre si.' ],
		[ 'p', 'O primeiro caso documentado de anadromia nessa truta introduzida é de Pascual et al. (2001, <em>Transactions of the American Fisheries Society</em> 130:53-67). <a href="' . $u_rr3 . '" rel="noopener">Riva Rossi, Arguimbau e Pascual (2003, <em>Ecología Austral</em> 13:151-159)</a> resumem assim: o Santa Cruz é «el único ambiente en el mundo donde la trucha arco iris introducida desarrolló poblaciones migratorias facultativas» («o único ambiente do mundo onde a truta arco-íris introduzida desenvolveu populações migratórias facultativas»; tradução nossa), ou seja, parte dos peixes completa o ciclo em água doce e outra parte viaja ao mar. Esse trabalho acompanhou com radiotransmissores sete peixes que subiam do mar no outono de 2000: mediam entre 490 e 780 mm de comprimento furcal (o peso não consta); o pico de entrada foi no começo de abril; descreve uma pescaria de fevereiro a maio, sobretudo nos primeiros 50 km do rio; a desova começou em agosto, com pico em setembro e outubro e água de 4 a 10 °C; e os deslocamentos dos peixes marcados foram de 0,6 a 279,5 km (Tabela 1).' ],
		[ 'h2', 'Quando ir: o que dizem as fontes' ],
		[ 'p', 'Não há uma data oficial de «temporada» para o steelhead: o rio está aberto o ano todo e o Anexo I só marca quando vale a devolução obrigatória. O que segue são datas de operadores e de trabalhos publicados, e elas não coincidem semana a semana.' ],
		[ 'table', [ 'Fonte', 'O que diz', 'Data da fonte' ], [
			[ 'Los Plateados (Tres Amigos Outfitters), página do lodge', '«March / May»; «From late February till May» os peixes entram do oceano', 'Lida em 14/11/2026' ],
			[ 'Tres Amigos Outfitters, texto «Facts about Santa Cruz Steelhead»', 'Corrida de outono que começa no fim de fevereiro, com o melhor momento de meados de março a meados de maio', '20/05/2021' ],
			[ 'Sportquest Holidays (agência)', 'Do fim de março ao começo de maio, com pico em abril', 'Página modificada em 20/01/2026' ],
			[ 'Riva Rossi, Arguimbau e Pascual (2003)', 'Pico de entrada dos peixes no começo de abril; pescaria de fevereiro a maio', 'Temporada de 2000' ],
			[ 'Llompart et al. (2024)', 'Esforço de pesca com picos de março a maio e de setembro a outubro, que coincidem com a subida do steelhead e do chinook', 'Abril de 2018 a abril de 2019' ],
			[ 'Anexo I, Disposición 241/2026', 'Devolução obrigatória do steelhead de 15/03/2027 a 30/09/2027; salmão-chinook de 01/08/2027 a 30/11/2027', 'Em vigor desde 1/10/2026' ],
			[ 'InterPatagonia', 'Piedra Buena é «la Capital Nacional de la Trucha Steelhead» e entre o fim de março e o começo de abril sedia uma festa internacional', 'Sem data na página' ],
		] ],
		[ 'p', 'A única coisa em que as fontes coincidem é que se trata de uma pesca do outono austral. As temporadas que os operadores publicam caem em sua maior parte depois de 15/03/2027, quando começa a devolução obrigatória. Para ver a truta de toda a Patagônia mês a mês, leia <a href="{n:cuando-pescar-trucha-patagonia}">quando pescar truta na Patagônia</a>.' ],
		[ 'h2', 'O que diz o regulamento (Anexo I, 2026/27)' ],
		[ 'p', 'Isto é o que diz a ficha «SANTA CRUZ (RÍO)» do Anexo I da Disposición 241/2026. Citamos textualmente o que importa (o espanhol é o texto oficial; o português é tradução nossa): não é uma licença nem uma interpretação nossa.' ],
		[ 'ul', [
			'<strong>Regime geral:</strong> «Apertura continuada durante todo el año. Prohibición expresa de uso de carnadas naturales, salvo excepciones. Se prohíbe el uso de lastres de plomo.» (Aberto o ano todo; isca natural expressamente proibida, salvo exceções; chumbo proibido.)',
			'<strong>Anzol:</strong> «Obligatoriedad permanente del uso de un (1) anzuelo simple o doble con o sin rebaba, salvo excepciones.» (Um anzol simples ou duplo, com ou sem farpa, salvo exceções.)',
			'<strong>Didymo:</strong> «Cuenca afectada por Didymo, desinfección obligatoria en estaciones fijas habilitadas antes de transitar a otros ambientes.» (Bacia afetada pelo Didymo; desinfecção obrigatória em estações fixas antes de passar a outros ambientes.)',
			'<strong>Steelhead:</strong> «Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead (Oncorhynchus mykiss)» (cota total de um steelhead por pescador por temporada) e «A partir del 15/03/2027 y hasta el 30/09/27, se establece la Captura y Devolución Obligatoria para la Trucha Steelhead (Oncorhynchus mykiss)» (de 15/03/2027 a 30/09/27, captura e devolução obrigatória do steelhead).',
			'<strong>Limite diário geral:</strong> «Dos (2) ejemplares en total por pescador. Un (1) ejemplar de Perca (Percichthys trucha) y Un (1) ejemplar de Trucha Marrón (Salmo trutta).» (Dois exemplares no total por pescador: uma perca e uma truta marrom.)',
			'<strong>Zona de pesca de costa autorizada:</strong> «desde el sector de bajada a la ribera ubicado a la altura del Monumento Héroes de Malvinas, en proximidades de la calle Bella Vista hasta el cruce del Interconectado Provincial sobre ambas márgenes (Norte y Sur)», e «incluye la Isla Pavón en su margen desde el Puente sobre Ruta Nacional N°3 hasta la zona denominada localmente como “La Noria”» (da descida à margem junto ao Monumento Héroes de Malvinas, perto da rua Bella Vista, até o cruzamento da linha elétrica Interconectado Provincial, nas duas margens; inclui a Isla Pavón, da ponte da Ruta 3 até a zona chamada localmente de «La Noria»).',
			'<strong>Salmão-chinook:</strong> «Temporada acotada desde el 01/08/2027 hasta el 30/11/2027», com isca artificial livre e um anzol simples ou duplo; «Límite Diario (LD): Un (1) ejemplar en total por pescador; el resto de las especies capturadas incidentalmente deben ser devueltas de forma obligatoria» (limite diário de um exemplar por pescador; as demais espécies capturadas incidentalmente devem ser devolvidas).',
			'<strong>Robalo:</strong> «Habilitación continua durante todo el año», com spinning, mosca ou linha de fundo de até três anzóis e isca livre, sem chumbo; «Hasta diez (10) ejemplares por pescador por jornada» (até dez por pescador por dia), tamanho mínimo de trinta centímetros de comprimento total; «Se autoriza de manera exclusiva el sacrificio del róbalo, debiendo liberarse las demás especies capturadas» (só o robalo pode ser abatido; as demais espécies devem ser soltas).',
			'<strong>Trolling:</strong> «Autorizado de manera exclusiva para Guías de Pesca debidamente registrados en el R.P.A.T. que cuenten con unidades flotantes motorizadas homologadas y cumpliendo con los requisitos del Artículo 16° del Reglamento General» (autorizado só para guias de pesca registrados no R.P.A.T., com embarcações motorizadas homologadas que cumpram o artigo 16), em dois trechos: da ponte da Ruta Nacional 3 até a captação de água de Puerto Santa Cruz, e da Chacra de Teira (Tapera Los Frutales) até o cruzamento do Interconectado.',
			'<strong>Motores (art. 16):</strong> «Queda prohibida la navegación con motor en todos los ríos de la Provincia (Consultar excepciones en el Listado de Ambientes, Anexo I).» (Navegação a motor proibida em todos os rios da província; consulte as exceções na lista de ambientes, Anexo I.)',
			'<strong>Proibição total nas obras hidrelétricas:</strong> o trecho «desde la Estancia El Rincón (zona de “Los Laberintos”) hasta el sector denominado “La Barrancosa”», delimitado pelos acampamentos de obra de Cóndor Cliff a 70° 07\' O e pelo ponto fixado em 70° 10\' 00\'\' O.',
		] ],
		[ 'p', 'Fonte: Disposición 241/2026 (22/9/2026), Boletim Oficial de Santa Cruz n.º 6164 (8/10/2026) e n.º 6166 (15/10/2026), em vigor desde 1/10/2026; texto do Anexo I lido em 13/11/2026. Sobre o steelhead, o Anexo diz só o que está citado acima: não o apresentamos como licença para ficar com um peixe. Se a sua saída cai antes de 15/03/2027, pergunte ao guia ou à Secretaria como a cota se aplica.' ],
		[ 'h2', 'Licença' ],
		[ 'p', 'É preciso a licença de pesca de Santa Cruz, tirada online no portal de Pesca e Aquicultura. Para um estrangeiro não residente em 2026/27: <strong>AR$ 90.000 por um dia, AR$ 300.000 por uma semana e AR$ 450.000 pela temporada</strong> (cerca de US$ 58, US$ 194 e US$ 291 com US$ 1 = AR$ 1.545, de 8 de novembro de 2026). Valores do portal oficial, lidos em 4 de novembro de 2026. O trolling é uma licença adicional. Comparamos as licenças de todas as províncias em <a href="{n:licencias-extranjeros-argentina}">licença de pesca para estrangeiros na Argentina 2026/27</a>.' ],
		[ 'p', 'Se a licença vem dentro do pacote depende da agência: a Where Wise Men Fish (página de 5/03/2025) e a Sportquest Holidays (página de 2026) a incluem; a Fly Water Travel (fevereiro de 2024) não a lista entre o que está incluído. A página do Los Plateados não esclarece: pergunte antes de reservar.' ],
		[ 'h2', 'Como chegar' ],
		[ 'p', 'Segundo o Welcome Argentina, Piedra Buena fica a 240 km de Río Gallegos; segundo o Visit Argentina (Inprotur), chega-se «de ônibus ou em veículo particular pela Ruta Nacional 3». A Tres Amigos Outfitters diz, para o conjunto de seus programas, que sua equipe recebe os passageiros nos aeroportos de El Calafate (FTE) ou de Río Gallegos (RGL), e um texto de 2021 descreve o trajeto desde Buenos Aires: voo do Aeroparque a Río Gallegos e traslado até o lodge. A Sportquest Holidays (2026) conta cerca de 3,5 horas por estrada desde Río Gallegos, e a Where Wise Men Fish (página de 5/03/2025) oferece traslados desde Río Gallegos ou El Calafate. O Visit Argentina descreve um rio «de quinhentos metros de largura» junto à cidade; na Isla Pavón, onde o comandante Piedra Buena se instalou em 1859, fica hoje o museu da cidade. Para El Calafate, veja <a href="{g:el-calafate}">nosso guia de pesca de El Calafate</a>, e para o aeroporto de chegada, <a href="{g:rio-gallegos}">nosso guia de Río Gallegos</a>.' ],
		[ 'h2', 'Quem guia no rio' ],
		[ 'p', 'Encontramos um único lodge com página própria sobre o rio: o <strong>Los Plateados</strong>, da Tres Amigos Outfitters. As outras páginas que o vendem são agências. Não encontramos guias independentes de Piedra Buena com página própria. Abaixo está o cartão do Los Plateados, com o que a página dele diz; nada nele está confirmado com o operador.' ],
		[ 'p', '<strong>Quanto custa.</strong> A tarifa que o Los Plateados publica na página não indica o ano, por isso não a mostramos. Como referência, a Where Wise Men Fish (página de 5/03/2025) publica uma tarifa de US$ 4.990 pela semana completa e US$ 2.990 pela meia semana: inclui sete noites com pensão completa, seis dias de pesca, traslados desde Río Gallegos ou El Calafate, refeições, vinho e cerveja da casa e a licença de pesca. Não sabemos a que temporada essa tarifa corresponde, então não é o preço de 2027. A Fly Water Travel publicou em fevereiro de 2024 o mesmo valor, US$ 4.990 por pessoa num programa de seis noites e seis dias, sem a licença entre o que está incluído. Para um preço vigente, <a href="{viaje}">peça a viagem para nós</a>.' ],
		[ 'h2', 'O que levar' ],
		[ 'p', 'A Tres Amigos Outfitters recomenda, num texto publicado em 4/6/2021, para o lodge dela:' ],
		[ 'ul', [
			'Vara de duas mãos (Spey) de linha 8 a 9, linha tipo Skagit e pontas de afundamento de comprimentos diferentes (uma de 12 pés T-14 «para todo uso»), tippet de 15 libras e moscas do 2 ao 6.',
			'Waders respiráveis e botas de sola de borracha: a sola de feltro é aceitável, mas não as botas com solas intercambiáveis ou pinos de metal.',
			'Alicate para tirar o anzol e amassar a farpa, cortador de linha e bastão de vadeio.',
		] ],
		[ 'p', 'O mesmo texto diz que se pesca sobretudo da margem norte (a esquerda do rio) e que a largura do rio e o vento exigem dominar dois arremessos: o Snap-T com vento de frente e o Reverse Double Spey com vento a favor.' ],
		[ 'h2', 'As represas' ],
		[ 'p', 'O próprio Anexo I fecha por completo o trecho afetado por «las obras civiles de los aprovechamientos hidroeléctricos» (as obras civis dos aproveitamentos hidrelétricos; acima). Llompart et al. (2024), que contaram pescadores perto de Piedra Buena entre abril de 2018 e abril de 2019, escrevem que rio acima «estão sendo construídas duas represas» e que os pescadores entrevistados as consideravam uma ameaça importante para a pescaria. A Tres Amigos Outfitters, num texto de 26/5/2021, opinava que as obras podiam atrasar de três a cinco anos mais. Os prazos mudam: não os damos como certos.' ],
		[ 'h2', 'Para ler antes de ir' ],
		[ 'ul', [
			'<a href="{esp:trucha}">A truta</a>: a espécie, seus tamanhos e recordes.',
			'<a href="{n:cuando-pescar-trucha-patagonia}">Quando pescar truta na Patagônia</a>.',
			'<a href="{n:licencias-extranjeros-argentina}">Licença de pesca para estrangeiros na Argentina 2026/27</a>.',
			'<a href="{g:el-calafate}">Pesca em El Calafate</a>, no lago Argentino.',
			'<a href="{g:rio-gallegos}">Pesca em Río Gallegos</a>, a truta marrom de mar.',
		] ],
		[ 'p', '<strong>Quer que ajudemos você a escolher?</strong> Diga o que quer pescar e quando <a href="{viaje}">no formulário da viagem</a> e consultamos os operadores que se encaixam; respondemos com as opções, sem custo e sem compromisso.' ],
		[ 'pend', 'tarifa do Los Plateados com ano (a página não traz) e o que inclui, licença incluída; que trecho do rio o lodge pesca em relação à zona de costa do Anexo I; licença de trolling; guias independentes de Piedra Buena; data da festa do steelhead 2027; uma única semana «melhor» (as fontes divergem); foto com crédito (foto pendente).' ],
	],
	'campos' => [
		'h1' => 'Pesca de steelhead no rio Santa Cruz (Piedra Buena): regras, licença e lodge',
		'bajada' => 'A truta arco-íris que sobe do mar pelo rio Santa Cruz. O que diz o Anexo I 2026/27, quando se sai para pescar, que licença é preciso e o que publica o único lodge que encontramos.',
		'modalidades' => "Fly (flycast)\nSpinning e baitcasting\nTrolling (só guias R.P.A.T.)",
		'epoca' => 'Do fim de fevereiro ou março a maio para o steelhead, segundo os operadores; o rio está aberto o ano todo.',
		'seo_title' => 'Pesca de steelhead no rio Santa Cruz: regras e licença',
		'seo_description' => 'Steelhead no rio Santa Cruz (Piedra Buena): regras do Anexo I 2026/27, licença, quando ir segundo as fontes e o lodge que encontramos.',
		'reglamento_resumen' => 'Anexo I da Disposición 241/2026, ficha «SANTA CRUZ (RÍO)»: «Apertura continuada durante todo el año» (aberto o ano todo), sem isca natural (salvo exceções) nem chumbo, um anzol simples ou duplo; «Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead» (cota de um steelhead por pescador por temporada) e captura e devolução obrigatória desse peixe de 15/03/2027 a 30/09/2027; limite diário geral de dois exemplares (uma perca e uma truta marrom); salmão-chinook de 01/08/2027 a 30/11/2027, um exemplar por dia; robalo com regime próprio; trolling só para guias registrados no R.P.A.T. em dois trechos; navegação a motor proibida nos rios (art. 16, com as exceções do Anexo I); Didymo. Texto lido do Boletim Oficial de Santa Cruz (n.º 6164 e n.º 6166) em 13/11/2026.',
		'temporada' => [
			[ 'meses' => 'O ano todo', 'que' => 'O rio Santa Cruz está aberto o ano todo («Apertura continuada durante todo el año»), com as regras por espécie abaixo.' ],
			[ 'meses' => 'Fim de fevereiro ou março a maio', 'que' => 'Steelhead segundo os operadores: o Los Plateados diz «March / May» e que desde o fim de fevereiro os peixes entram do oceano; a Sportquest Holidays, do fim de março ao começo de maio, com pico em abril.' ],
			[ 'meses' => '15/03/2027 a 30/09/2027', 'que' => 'Captura e devolução obrigatória do steelhead (Anexo I, Disposición 241/2026).' ],
			[ 'meses' => '01/08/2027 a 30/11/2027', 'que' => 'Temporada restrita do salmão-chinook, um exemplar por pescador e por dia (Anexo I, Disposición 241/2026).' ],
			[ 'meses' => 'Antes de cada saída', 'que' => 'As regras mudam a cada temporada: confirme com a Secretaria de Pesca e Aquicultura ou com o seu guia.' ],
		],
		'aguas' => [
			[ 'nombre' => 'Rio Santa Cruz, zona de Piedra Buena', 'nota' => 'A zona de pesca de costa autorizada do Anexo I vai da descida à margem junto ao Monumento Héroes de Malvinas (rua Bella Vista) até o cruzamento do Interconectado Provincial, e inclui a Isla Pavón. Veja o texto completo acima.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Quando se pesca o steelhead no rio Santa Cruz?', 'respuesta' => 'Os operadores o situam no outono austral: o Los Plateados (Tres Amigos Outfitters) diz «March / May» e que do fim de fevereiro a maio os peixes entram do oceano; a Sportquest Holidays, do fim de março ao começo de maio, com pico em abril. Riva Rossi, Arguimbau e Pascual (2003) mediram o pico de entrada no começo de abril, na temporada de 2000, e Llompart et al. (2024) contaram mais esforço de pesca de março a maio. O rio está aberto o ano todo, mas de 15/03/2027 a 30/09/2027 o steelhead é de captura e devolução obrigatória (Anexo I da Disposición 241/2026).' ],
			[ 'pregunta' => 'Posso ficar com um steelhead?', 'respuesta' => 'O Anexo I da Disposición 241/2026 diz, textualmente: «Cupo en total por pescador por temporada de un (1) ejemplar de Trucha Steelhead (Oncorhynchus mykiss)» (cota total de um steelhead por pescador por temporada) e «A partir del 15/03/2027 y hasta el 30/09/27, se establece la Captura y Devolución Obligatoria para la Trucha Steelhead» (de 15/03/2027 a 30/09/27, captura e devolução obrigatória). Não o apresentamos como licença para abater um peixe: se a sua saída cai antes de 15/03/2027, pergunte ao guia ou à Secretaria de Pesca e Aquicultura. As temporadas que os operadores publicam caem em sua maior parte depois dessa data.' ],
			[ 'pregunta' => 'Que licença preciso e quanto custa?', 'respuesta' => 'A licença de pesca de Santa Cruz, tirada online no portal de Pesca e Aquicultura. Para um estrangeiro não residente em 2026/27: AR$ 90.000 por um dia, AR$ 300.000 por uma semana e AR$ 450.000 pela temporada (cerca de US$ 58, US$ 194 e US$ 291 com US$ 1 = AR$ 1.545, de 8/11/2026). Valores do portal oficial, lidos em 4/11/2026. O trolling é uma licença adicional.' ],
			[ 'pregunta' => 'Quanto custa uma semana de steelhead no rio Santa Cruz?', 'respuesta' => 'A página do Los Plateados publica uma tarifa sem ano, por isso não a mostramos: preço sob consulta. Como referência, a Where Wise Men Fish (página de 5/03/2025) publica uma tarifa de US$ 4.990 pela semana completa e US$ 2.990 pela meia semana, com sete noites de pensão completa, seis dias de pesca, traslados desde Río Gallegos ou El Calafate, refeições, vinho e cerveja da casa e licença; a Fly Water Travel publicou o mesmo valor, US$ 4.990, em fevereiro de 2024 para seis noites e seis dias. Não são preços de 2027: peça um atual.' ],
			[ 'pregunta' => 'A licença de pesca vem incluída no pacote?', 'respuesta' => 'Depende da agência. A Where Wise Men Fish (página de 5/03/2025) e a Sportquest Holidays (página de 2026) a incluem; a Fly Water Travel (fevereiro de 2024) não a lista entre o que está incluído, e a página do Los Plateados não esclarece. Pergunte antes de reservar.' ],
			[ 'pregunta' => 'Posso pescar com isca ou com chumbo?', 'respuesta' => 'Para o steelhead e as demais espécies de salmonídeos, não: o Anexo I diz «Prohibición expresa de uso de carnadas naturales, salvo excepciones. Se prohíbe el uso de lastres de plomo.» (isca natural expressamente proibida, salvo exceções; chumbo proibido) e fixa «un (1) anzuelo simple o doble con o sin rebaba» (um anzol simples ou duplo, com ou sem farpa). O robalo tem regime próprio, com isca livre e sem chumbo.' ],
			[ 'pregunta' => 'Posso pescar salmão-chinook no rio Santa Cruz?', 'respuesta' => 'De 01/08/2027 a 30/11/2027, com um exemplar por dia e por pescador; as demais espécies capturadas são devolvidas. A zona de costa vai da descida urbana da rua Bella Vista até o cruzamento do Interconectado Provincial (Anexo I da Disposición 241/2026). Llompart et al. (2024) ligam o pico de esforço de setembro e outubro à subida do chinook; não encontramos operador que venda chinook no trecho baixo.' ],
			[ 'pregunta' => 'Posso pescar robalo no rio Santa Cruz?', 'respuesta' => 'Sim, o ano todo, com spinning, mosca ou linha de fundo com até três anzóis e isca livre, sem chumbo. O limite é de dez por pescador e por dia, com tamanho mínimo de 30 cm, e é a única espécie que pode ser abatida. A zona vai do Monumento Héroes de Malvinas, perto da rua Bella Vista, até o alambrado da Estancia La Esperanza, na margem norte (Anexo I da Disposición 241/2026).' ],
			[ 'pregunta' => 'Posso usar motor no rio Santa Cruz?', 'respuesta' => 'O artigo 16 do Regulamento Geral diz: «Queda prohibida la navegación con motor en todos los ríos de la Provincia (Consultar excepciones en el Listado de Ambientes, Anexo I)» (navegação a motor proibida em todos os rios da província; consulte as exceções na lista de ambientes, Anexo I). Para o rio Santa Cruz, o Anexo I autoriza o trolling só para guias registrados no R.P.A.T., com embarcações motorizadas homologadas, em dois trechos. Como isso se aplica à sua saída, pergunte ao operador ou à Secretaria de Pesca e Aquicultura.' ],
			[ 'pregunta' => 'Como chego a Piedra Buena e ao rio?', 'respuesta' => 'Piedra Buena fica a cerca de 240 km de Río Gallegos pela Ruta Nacional 3, segundo o Welcome Argentina, e chega-se de ônibus ou de carro (Visit Argentina). Segundo os operadores, eles recebem os passageiros nos aeroportos de Río Gallegos (RGL) ou de El Calafate (FTE); a Sportquest Holidays conta cerca de 3,5 horas por estrada de Río Gallegos.' ],
			[ 'pregunta' => 'Que tamanho tem o steelhead do rio Santa Cruz?', 'respuesta' => 'Os números são de operadores e não coincidem: a Tres Amigos Outfitters fala, num texto de 2021 sobre seus registros desde 2014, de uma média de 8 libras e um máximo de 21,5 libras, e a página do lodge diz que podem passar de 20 libras; a Sportquest Holidays (2026) dá de 5 a 29 libras, com média de 12. O trabalho de Riva Rossi, Arguimbau e Pascual (2003) mediu sete peixes radiomarcados de 490 a 780 mm de comprimento furcal; não dá pesos.' ],
			[ 'pregunta' => 'De onde vem o steelhead do rio Santa Cruz?', 'respuesta' => 'Segundo Lázari et al. (2024), a truta arco-íris foi solta na bacia entre 1906 e 1910, presumivelmente da Califórnia ou do Oregon. Riva Rossi, Lessa e Pascual (2004) analisaram seu DNA mitocondrial e apontaram o rio McCloud, na Califórnia, como a origem mais provável, embora Lázari et al. (2024) concluam que o McCloud não foi a única fonte; Pascual et al. (2001) documentaram pela primeira vez a anadromia dessa truta introduzida. A Disposición 241/2026 diz, em seus considerandos, que é «la única población silvestre de linaje atlántico en el continente americano» (a única população silvestre de linhagem atlântica no continente americano).' ],
		],
	],
	'fuentes' => [
		[ 'Boletim Oficial de Santa Cruz: Disposición 241/2026 de 22/9/2026, Regulamento Geral de Pesca Esportiva Continental 2026/2027 (BO 6164, 8/10/2026; lido em 13/11/2026)', $u_bo1, 1 ],
		[ 'Boletim Oficial de Santa Cruz: Disposición 241/2026, terceira parte (BO 6166, 15/10/2026; lido em 13/11/2026)', $u_bo2, 1 ],
		[ 'Boletim Oficial de Santa Cruz: Disposición 241/2026, texto do regulamento e do Anexo I (PDF)', $u_pdf, 1 ],
		[ 'Pesca e Aquicultura de Santa Cruz: Anexo I, ambientes 2026/27', $u_amb, 1 ],
		[ 'Pesca e Aquicultura de Santa Cruz: valores das licenças (lidos em 4/11/2026)', $u_val, 1 ],
		[ 'Lázari et al. (2024): Ancestry and genetic structure of resident and anadromous rainbow trout in Argentina, Journal of Fish Biology', $u_lz, 0 ],
		[ 'Riva Rossi, Lessa e Pascual (2004): The origin of introduced rainbow trout in the Santa Cruz River, Patagonia, Argentina, as inferred from mitochondrial DNA, Canadian Journal of Fisheries and Aquatic Sciences 61:1095-1101', $u_rr4, 0 ],
		[ 'Riva Rossi, Arguimbau e Pascual (2003): a migração reprodutiva da truta arco-íris anádroma do rio Santa Cruz por radiomarcação, Ecología Austral 13', $u_rr3, 0 ],
		[ 'Llompart et al. (2024): a pesca recreativa na bacia baixa do rio Santa Cruz diante de cenários de represamento, Ecología Austral 34(2)', $u_ll, 0 ],
		[ 'Welcome Argentina: Comandante Luis Piedra Buena', $u_wa, 0 ],
		[ 'Visit Argentina (Inprotur): Comandante Luis Piedrabuena', $u_at, 0 ],
		[ 'InterPatagonia: Por el río Santa Cruz (Cte. Luis Piedra Buena)', $u_ip, 0 ],
		[ 'Tres Amigos Outfitters: Los Plateados (operador; lida em 14/11/2026)', $u_lp, 0 ],
		[ 'Tres Amigos Outfitters: Santa Cruz fishing essentials (equipamento, 4/6/2021)', $u_ess, 0 ],
		[ 'Tres Amigos Outfitters: Facts about Santa Cruz Steelhead (20/5/2021)', $u_fact, 0 ],
		[ 'Tres Amigos Outfitters: Santa Cruz steelhead 2021 season report (26/5/2021)', $u_rep, 0 ],
		[ 'Sportquest Holidays: Santa Cruz River steelhead (agência; página modificada em 20/1/2026)', $u_sq, 0 ],
		[ 'Where Wise Men Fish: Santa Cruz steelhead (agência; tarifa da página de 5/3/2025)', $u_ww, 0 ],
		[ 'Fly Water Travel: blog de 21/2/2024 (agência; tarifa)', $u_fwt, 0 ],
	],
];

$clave = 'rio-santa-cruz';
$orden = [ 'es', 'en', 'pt' ];
$compartido = [ 'region' => 'patagonia', 'provincia' => 'santa-cruz', 'lat' => -49.9831, 'lon' => -68.9100, 'especies' => [ 'trucha' ] ];

// ---- The operator card (listado con información pública; docs/prestadores.md) -------------------------------------------------------------
$nombre_ficha = 'Los Plateados (Tres Amigos Outfitters)';
$ficha = [
	'tipo' => [ 'alojamiento', 'operador' ], 'prest_destino' => $clave, 'localidad' => 'Río Santa Cruz inferior, Santa Cruz',
	'descripcion' => 'Cabaña sobre la orilla del río Santa Cruz, a unas 50 millas (unos 80 km) río arriba del estuario según su página, operada por Tres Amigos Outfitters. Aloja a 4 pescadores en dos habitaciones dobles y la página dice que corre desde 2015. Pesca steelhead con mosca: recomienda caña de dos manos (Spey) de líneas 8 a 9 y se pesca sobre todo desde la margen norte. Según la página, los peces entran desde el océano de fines de febrero a mayo. La tarifa que figura en su página no indica el año, por eso no la mostramos.',
	'descripcion_en' => 'Cabin on the bank of the Santa Cruz River, about 50 miles (some 80 km) upstream from the estuary according to its page, run by Tres Amigos Outfitters. It sleeps 4 anglers in two double rooms and the page says it has been running since 2015. Steelhead fishing with the fly: it recommends a two-handed (Spey) rod in 8 to 9 weight and the fishing is mostly done from the north bank. According to the page, the fish enter from the ocean from late February to May. The rate shown on its page carries no year, so we do not show it.',
	'descripcion_pt' => 'Cabana à beira do rio Santa Cruz, a cerca de 50 milhas (uns 80 km) rio acima do estuário segundo a página, operada pela Tres Amigos Outfitters. Hospeda 4 pescadores em dois quartos duplos e a página diz que funciona desde 2015. Pesca de steelhead com mosca: recomenda vara de duas mãos (Spey) de linha 8 a 9 e pesca-se sobretudo da margem norte. Segundo a página, os peixes entram do oceano do fim de fevereiro a maio. A tarifa que consta na página não indica o ano, por isso não a mostramos.',
	'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha' ],
	'prest_temporada' => 'Marzo a mayo (según su página; los peces entran desde fines de febrero)',
	'prest_temporada_en' => 'March to May (according to its page; the fish enter from late February)',
	'prest_temporada_pt' => 'Março a maio (segundo a página; os peixes entram desde o fim de fevereiro)',
	'prest_programa' => '6 días de pesca, de sábado a viernes, 4 pescadores (según su página)',
	'prest_programa_en' => '6 fishing days, Saturday to Friday, 4 anglers (according to its page)',
	'prest_programa_pt' => '6 dias de pesca, de sábado a sexta, 4 pescadores (segundo a página)',
	'capacidad' => 4, 'prest_capacidad_unidad' => 'semana',
	'precio' => '', 'precio_fecha' => '',
	'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado', 'verificado' => $leido,
	'prest_fuente' => $u_lp, 'web' => $u_lp,
	'notas' => 'Listado con información pública (página del operador leída el 13 y el 14/11/2026 con investigar.yml, rama investigacion: fuentes-2-1-20261113-2207/26 y santacruz-2-20261114-0956/03; equipo y viaje de su nota del 4/6/2021, santacruz-1-20261114-0943/03). Solo lo que dice la página del operador. Su tarifa (media semana y semana completa, sin año) NO se publica por decisión del dueño (14/11/2026): precio vacío ("a consultar"). La misma página dice que aloja hasta 14 huéspedes y 12 pescadores por semana, lo que contradice los 4 pescadores y las dos habitaciones dobles: probablemente texto de otro programa; no se usa. Los otros programas de Tres Amigos (Glacier King, Río Guanaco) no son de este río y no entran. Contacto no publicado. Sin consentimiento ni acuerdo; sin foto. Pendiente de preguntarle: tarifa 2027 con año, qué incluye (y si la licencia), depósito, acompañante que no pesca, qué tramo pesca frente a la zona de costa del Anexo I y cómo encuadra sus traslados en embarcación con el artículo 16.',
];

// --- Guards: SEO lengths, FAQ count, nothing ruled out in any visible text ------------------------------------------------------------------
foreach ( $orden as $i ) {
	$c  = $G[ $i ]['campos'];
	$tl = mb_strlen( $c['seo_title'] );
	$dl = mb_strlen( $c['seo_description'] );
	WP_CLI::log( "SEO ({$i}): title {$tl}, description {$dl}" );
	if ( $tl > 60 || $dl < 70 || $dl > 160 ) {
		WP_CLI::error( "SEO fuera de rango ({$i}): title {$tl}, description {$dl}" );
	}
	if ( count( $c['faqs'] ) < 8 || count( $c['faqs'] ) > 12 ) {
		WP_CLI::error( "FAQs fuera de rango ({$i}): " . count( $c['faqs'] ) );
	}
	$visible = wp_json_encode( [ $c, array_filter( $G[ $i ]['cuerpo'], fn( $b ) => 'pend' !== $b[0] ), $G[ $i ]['fuentes'] ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( preg_match( PM_RSC_PROHIBIDO, $visible, $m ) ) {
		WP_CLI::error( "texto no publicable en la guía ({$i}): {$m[0]}" );
	}
}
$visible = wp_json_encode( array_diff_key( $ficha, [ 'notas' => 1 ] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
if ( preg_match( PM_RSC_PROHIBIDO, $visible, $m ) ) {
	WP_CLI::error( "texto no publicable en la ficha: {$m[0]}" );
}
if ( preg_match( '/@|\+\d{2}[ \d]{6,}/u', wp_json_encode( array_diff_key( $ficha, [ 'web' => 1, 'prest_fuente' => 1, 'notas' => 1 ] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) ) {
	WP_CLI::error( 'contacto en un texto público de la ficha' );
}
if ( '' !== trim( $ficha['precio'] ) || '' !== trim( $ficha['precio_fecha'] ) ) {
	WP_CLI::error( 'la ficha no lleva precio' );
}

// --- 1. Card ------------------------------------------------------------------------------------------------------------------------------
$pid = pm_rsc_ficha_id( $nombre_ficha );
if ( ! $pid ) {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre_ficha}» NUEVA, campos:" );
	foreach ( $ficha as $k => $v ) {
		WP_CLI::log( "  + {$k}: " . pm_rsc_ver( $v ) );
	}
	$plan_ficha = [ 0, $ficha ];
} else {
	// Exists: only fields that are empty today are filled; nothing an editor wrote is replaced.
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre_ficha}» #{$pid} ya existe: solo se llenan los campos vacíos" );
	$cambios = [];
	foreach ( $ficha as $k => $nuevo ) {
		$antes = pm_campo( $k, $pid );
		$vacio = '' === pm_rsc_norm( $k, $antes ) || ( is_array( $antes ) && ! $antes );
		if ( $vacio && '' !== pm_rsc_norm( $k, $nuevo ) ) {
			WP_CLI::log( "  ~ {$k}: vacío → " . pm_rsc_ver( $nuevo ) );
			$cambios[ $k ] = $nuevo;
		} else {
			WP_CLI::log( "  = {$k} se deja (" . ( pm_rsc_norm( $k, $antes ) === pm_rsc_norm( $k, $nuevo ) ? 'igual' : 'ya tiene otro valor' ) . ')' );
		}
	}
	$plan_ficha = [ $pid, $cambios ];
}

// --- 2. Guides (es, then en and pt with the same clave) -----------------------------------------------------------------------------------
$plan = [];
foreach ( $orden as $i ) {
	$g  = $G[ $i ];
	$ya = pm_rsc_guia( $clave, $i );
	$cuerpo = pm_rsc_bloques( pm_rsc_resolver_todo( $g['cuerpo'], $i, $clave ) );
	$campos = pm_rsc_resolver_todo( $g['campos'], $i, $clave );
	$campos['fuentes'] = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $leido ], $g['fuentes'] );
	$campos += [ 'idioma' => $i, 'clave' => $clave, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'reglamento_autoridad' => $g['autoridad'], 'reglamento_url' => $u_amb ];
	if ( 'es' === $i ) {
		$campos += $compartido;
	}
	WP_CLI::log( ( $ya ? "{$clave} ({$i}) ya existe #{$ya->ID}: no toco nada" : ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$clave} ({$i}) se crea /{$g['slug']}/" ) . ' · ' . count( $campos['faqs'] ) . ' preguntas, ' . count( $campos['fuentes'] ) . ' fuentes, ' . count( $campos['temporada'] ) . ' filas de temporada, ' . count( $g['cuerpo'] ) . ' bloques, ' . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' caracteres de cuerpo' );
	if ( ! $ya ) {
		WP_CLI::log( "  titulo: {$g['titulo']} | h1: {$campos['h1']}" );
		WP_CLI::log( "  seo_title: {$campos['seo_title']}" );
		WP_CLI::log( "  seo_description: {$campos['seo_description']}" );
		WP_CLI::log( "  bajada: {$campos['bajada']}" );
		WP_CLI::log( "  epoca: {$campos['epoca']}" );
		WP_CLI::log( '  preguntas: ' . implode( ' | ', array_column( $campos['faqs'], 'pregunta' ) ) );
	}
	$plan[ $i ] = [ 'ya' => $ya, 'titulo' => $g['titulo'], 'slug' => $g['slug'], 'cuerpo' => $cuerpo, 'campos' => $campos ];
}

// --- 3. El Calafate: the block "El río Santa Cruz en detalle" becomes a pointer; its two FAQs move to this guide ------------------------
$cal_textos = [
	'es' => [
		'viejo' => 'El río Santa Cruz en detalle', 'nuevo' => 'El río Santa Cruz',
		'texto' => 'El tramo bajo del río Santa Cruz, donde se pesca el steelhead, tiene su propia guía, centrada en Comandante Luis Piedra Buena: <a href="%s">Río Santa Cruz (Piedra Buena)</a>. Ahí están las reglas del Anexo I para el steelhead, el salmón chinook y el róbalo, la zona de pesca de costa (que incluye la isla Pavón), el trolling y el lodge que encontramos.',
		'faqs' => [ '¿Se puede pescar róbalo en el río Santa Cruz?', '¿Cuándo se pesca el salmón chinook en el río Santa Cruz?' ],
	],
	'en' => [
		'viejo' => 'The Santa Cruz River in detail', 'nuevo' => 'The Santa Cruz River',
		'texto' => 'The lower Santa Cruz River, where steelhead are fished, has its own guide, centred on Comandante Luis Piedra Buena: <a href="%s">Río Santa Cruz (Piedra Buena)</a>. It has the Annex I rules for steelhead, chinook salmon and róbalo, the shore fishing zone (which includes Isla Pavón), trolling and the lodge we found.',
		'faqs' => [ 'Can I fish róbalo in the Santa Cruz River?', 'When can I fish Chinook salmon in the Santa Cruz River?' ],
	],
	'pt' => [
		'viejo' => 'O rio Santa Cruz em detalhe', 'nuevo' => 'O rio Santa Cruz',
		'texto' => 'O trecho baixo do rio Santa Cruz, onde se pesca o steelhead, tem seu próprio guia, centrado em Comandante Luis Piedra Buena: <a href="%s">Río Santa Cruz (Piedra Buena)</a>. Ali estão as regras do Anexo I para o steelhead, o salmão-chinook e o robalo, a zona de pesca de costa (que inclui a Isla Pavón), o trolling e o lodge que encontramos.',
		'faqs' => [ 'Posso pescar robalo no rio Santa Cruz?', 'Quando se pesca o salmão-chinook no rio Santa Cruz?' ],
	],
];
$plan_cal = [];
foreach ( $orden as $i ) {
	$t  = $cal_textos[ $i ];
	$p  = pm_rsc_guia( 'el-calafate', $i );
	$url_nueva = home_url( ( 'es' === $i ? '' : "/{$i}" ) . '/patagonia/santa-cruz/' . $G[ $i ]['slug'] . '/' );
	if ( ! $p ) {
		WP_CLI::warning( "guía el-calafate ($i): no existe, se omite" );
		continue;
	}
	$c      = $p->post_content;
	$estado = [];
	$nuevo  = $c;
	if ( false !== strpos( $c, $url_nueva ) ) {
		$estado[] = 'bloque: ya es un puntero';
	} else {
		$enc = pm_rsc_encabezados( $c );
		$ini = null;
		$fin = strlen( $c );
		foreach ( $enc as $n => [ $pos, $txt ] ) {
			if ( $txt === $t['viejo'] ) {
				$ini = $pos;
				$fin = $enc[ $n + 1 ][0] ?? strlen( $c );
			}
		}
		if ( null === $ini ) {
			WP_CLI::warning( "guía el-calafate ($i): no encontré el encabezado «{$t['viejo']}»; no toco el bloque" );
			$estado[] = 'bloque: falta (encabezado no encontrado)';
		} else {
			$bloque_nuevo = pm_rsc_h( $t['nuevo'], 2 ) . "\n\n" . pm_rsc_p( sprintf( $t['texto'], esc_url( $url_nueva ) ) ) . "\n\n";
			WP_CLI::log( "  guía el-calafate ($i) bloque «{$t['viejo']}»\n      ANTES (" . ( $fin - $ini ) . " bytes):   " . substr( $c, $ini, $fin - $ini ) . "\n      DESPUÉS: {$bloque_nuevo}" );
			$nuevo    = substr( $c, 0, $ini ) . $bloque_nuevo . substr( $c, $fin );
			$estado[] = 'bloque: cambia';
		}
	}
	$filas  = pm_filas( 'faqs', $p->ID );
	$filas_n = array_values( array_filter( $filas, fn( $f ) => ! in_array( $f['pregunta'] ?? '', $t['faqs'], true ) ) );
	if ( count( $filas_n ) !== count( $filas ) ) {
		foreach ( $filas as $f ) {
			in_array( $f['pregunta'] ?? '', $t['faqs'], true ) && WP_CLI::log( "  guía el-calafate ($i) FAQ que sale (la respuesta pasa a la guía nueva):\n      {$f['pregunta']}\n      {$f['respuesta']}" );
		}
		$estado[] = 'FAQ: salen ' . ( count( $filas ) - count( $filas_n ) );
	} else {
		$estado[] = 'FAQ: ya no están';
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía el-calafate ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' | ' . implode( ' | ', $estado ) );
	$plan_cal[ $i ] = [ 'id' => $p->ID, 'texto' => $nuevo, 'cambia_texto' => $nuevo !== $c, 'filas' => $filas_n, 'cambia_faqs' => count( $filas_n ) !== count( $filas ), 'url' => $url_nueva, 'viejo' => $t['viejo'], 'faqs' => $t['faqs'] ];
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
foreach ( $plan_cal as $i => $x ) {
	// A block that should change but was not found stops the run before anything is written.
	if ( ! $x['cambia_texto'] && false === strpos( get_post( $x['id'] )->post_content, $x['url'] ) ) {
		WP_CLI::error( "no escribo nada: el bloque de El Calafate ($i) no está donde se esperaba." );
	}
}

// --- Apply --------------------------------------------------------------------------------------------------------------------------------
[ $pid, $cambios ] = $plan_ficha;
if ( ! $pid ) {
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre_ficha ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	$pid = (int) $pid;
	WP_CLI::log( "✓ ficha «{$nombre_ficha}» creada #{$pid}" );
}
foreach ( $cambios as $k => $v ) {
	update_field( $k, $v, $pid );
}
$t = time();
foreach ( $orden as $i ) {
	$x = $plan[ $i ];
	if ( $x['ya'] ) {
		continue;
	}
	if ( 'es' !== $i && ! pm_rsc_guia( $clave, 'es' ) ) {
		WP_CLI::error( "No hay guía en español de {$clave}: no creo la de {$i}." );
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $x['titulo'], 'post_name' => $x['slug'], 'post_content' => wp_slash( $x['cuerpo'] ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	foreach ( $x['campos'] as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
	$plan[ $i ]['id'] = (int) $id;
	WP_CLI::log( "✓ {$clave} ({$i}) publicada #{$id}: " . get_permalink( $id ) );
	if ( 'es' === $i ) {
		WP_CLI::warning( 'sin foto: "foto pendiente" (no hay una con crédito); cargala desde el panel (Imagen destacada)' );
	}
}
foreach ( $orden as $i ) {
	if ( $plan[ $i ]['ya'] ) {
		$plan[ $i ]['id'] = $plan[ $i ]['ya']->ID;
	}
}
foreach ( $plan_cal as $i => $x ) {
	if ( $x['cambia_texto'] ) {
		$r = wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['texto'] ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( $r->get_error_message() );
		}
	}
	if ( $x['cambia_faqs'] ) {
		update_field( 'faqs', pm_rsc_filas( $x['filas'], PM_RSC_REPETIDORES['faqs'] ), $x['id'] );
	}
	( $x['cambia_texto'] || $x['cambia_faqs'] ) && update_field( 'actualizado', $hoy, $x['id'] );
}

// --- Read back ----------------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $ficha as $k => $v ) {
	$b = pm_rsc_norm( $k, pm_campo( $k, $pid ) ) === pm_rsc_norm( $k, $v );
	if ( ! $b ) {
		$vacio_antes = ! empty( $plan_ficha[0] ) && ! array_key_exists( $k, $plan_ficha[1] );
		WP_CLI::log( ( $vacio_antes ? 'DISTINTO (ya tenía otro valor; no se tocó)' : 'FALLÓ' ) . ": «{$nombre_ficha}» #{$pid} {$k} = " . pm_rsc_ver( pm_campo( $k, $pid ) ) );
		$ok = $ok && $vacio_antes;
		continue;
	}
	WP_CLI::log( "VERIFIED: «{$nombre_ficha}» #{$pid} {$k} = " . pm_rsc_ver( pm_campo( $k, $pid ) ) );
}
$publico = '';
foreach ( [ '', '_en', '_pt' ] as $s ) {
	foreach ( [ 'descripcion', 'precio', 'prest_temporada', 'prest_programa', 'prest_estadia_minima', 'prest_acompanante', 'prest_no_incluye' ] as $k ) {
		$publico .= ' ' . pm_campo( $k . $s, $pid );
	}
}
$limpio = ! preg_match( PM_RSC_PROHIBIDO, $publico );
WP_CLI::log( ( $limpio ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre_ficha}» ningún texto público (es/en/pt) lleva nada de lo descartado" );
$ok = $ok && $limpio;
$sin_precio = '' === trim( (string) pm_campo( 'precio', $pid ) ) && '' === trim( (string) pm_campo( 'precio_fecha', $pid ) ) && '' === trim( (string) pm_campo( 'prest_acompanante', $pid ) );
WP_CLI::log( ( $sin_precio ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre_ficha}» sin precio ni acompañante (a consultar)" );
$ok = $ok && $sin_precio;
$r = null;
foreach ( pm_prestadores_destino( $clave ) as $x ) {
	if ( (int) $x['id'] === $pid ) {
		$r = $x;
	}
}
$bien = $r && pm_prestador_publico( $r ) && ! $r['consentimiento'] && ! $r['acuerdo'];
WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre_ficha}» sale en la lista de {$clave} como información pública (publico=" . (int) ( $r['publico'] ?? 0 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ') capacidad=' . ( $r['capacidad'] ?? '' ) . ' unidad=' . ( $r['capacidad_unidad'] ?? '' ) . ' precio_fecha=' . ( $r['precio_fecha'] ?? '' ) );
$ok = $ok && $bien;
foreach ( $orden as $i ) {
	$x  = $plan[ $i ];
	$id = $x['id'];
	$p  = get_post( $id );
	$b  = $p && 'publish' === $p->post_status && $p->post_name === $x['slug'] && pm_idioma_de( $id ) === $i && pm_clave_de( $id ) === $clave;
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) #{$id} publicada, slug {$p->post_name}, idioma " . pm_idioma_de( $id ) . ', clave ' . pm_clave_de( $id ) . ' ' . get_permalink( $id ) );
	$ok = $ok && $b;
	if ( $x['ya'] ) {
		continue; // an existing guide is not compared (it may have been edited)
	}
	foreach ( $x['campos'] as $k => $v ) {
		if ( isset( PM_RSC_REPETIDORES[ $k ] ) ) {
			$leido_ = pm_rsc_filas( pm_filas( $k, $id ), PM_RSC_REPETIDORES[ $k ] );
			$env    = pm_rsc_filas( $v, PM_RSC_REPETIDORES[ $k ] );
			$bb     = wp_json_encode( $leido_, JSON_UNESCAPED_UNICODE ) === wp_json_encode( $env, JSON_UNESCAPED_UNICODE );
			WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) {$k}: " . count( $leido_ ) . ' filas leídas, ' . count( $env ) . ' enviadas' );
			$ok = $ok && $bb;
			continue;
		}
		if ( 'es' !== $i && array_key_exists( $k, $compartido ) ) {
			continue;
		}
		$bb = pm_rsc_norm( $k, pm_campo( $k, $id ) ) === pm_rsc_norm( $k, $v );
		WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) {$k} = " . mb_substr( pm_rsc_ver( pm_campo( $k, $id ) ), 0, 140 ) );
		$ok = $ok && $bb;
	}
	// The body as the visitor gets it: the hidden notes are gone, nothing ruled out, every link resolved.
	$html = apply_filters( 'the_content', $p->post_content );
	$sin  = false === strpos( $html, 'PENDIENTE' ) && ! preg_match( PM_RSC_PROHIBIDO_HTML, $html ) && false === strpos( $html, '{n:' ) && false === strpos( $html, '{g:' ) && false === strpos( $html, '{viaje}' ) && false === strpos( $html, '{esp:' );
	WP_CLI::log( ( $sin ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) cuerpo publicado sin notas PENDIENTE, sin lo descartado y sin marcadores (" . mb_strlen( wp_strip_all_tags( $html ) ) . ' caracteres visibles, ' . substr_count( $html, '<a ' ) . ' enlaces, ' . substr_count( $html, '<table' ) . ' tablas)' );
	$ok = $ok && $sin;
}
$es = $plan['es']['id'];
foreach ( $compartido as $k => $v ) {
	$bb = pm_rsc_norm( $k, pm_campo( $k, $es ) ) === pm_rsc_norm( $k, $v );
	WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} (es) compartido {$k} = " . pm_rsc_ver( pm_campo( $k, $es ) ) );
	$ok = $ok && $bb;
}
foreach ( $plan_cal as $i => $x ) {
	$p    = get_post( $x['id'] );
	$real = get_permalink( $plan[ $i ]['id'] );
	$tiene = false !== strpos( $p->post_content, $x['url'] ) && false === strpos( $p->post_content, $x['viejo'] ) && false !== strpos( $real, $G[ $i ]['slug'] );
	$preg = array_column( pm_filas( 'faqs', $x['id'] ), 'pregunta' );
	$sin_faq = ! array_intersect( $x['faqs'], $preg );
	WP_CLI::log( ( $tiene ? 'VERIFIED' : 'FALLÓ' ) . ": El Calafate ($i) #{$x['id']} enlaza a {$x['url']} (el permalink real es {$real}) y ya no tiene «{$x['viejo']}»" );
	WP_CLI::log( ( $sin_faq ? 'VERIFIED' : 'FALLÓ' ) . ": El Calafate ($i) #{$x['id']} sin las dos FAQ del río Santa Cruz (" . count( $preg ) . ' FAQ en total)' );
	$ok = $ok && $tiene && $sin_faq;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
