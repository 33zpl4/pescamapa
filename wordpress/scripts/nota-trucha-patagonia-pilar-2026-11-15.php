<?php
/**
 * Pillar note "Trout fishing in Patagonia: brown, rainbow and sea-run" (es/en/pt siblings, clave trucha-patagonia-pilar), 15/11/2026.
 * Why: Search Console shows English queries about trout in Patagonia ("patagonia brown trout fishing", "brown trout fishing in patagonia", "trout fishing in
 * patagonia", "trout patagonia", "patagonia trout") and no English page of ours gathers the topic (docs/seo-consultas-2026-11-13.md, "Pilar EN de la Patagonia").
 * Built ONLY from facts already published on the site, each with the source and the date it was read when it was published (nothing new was read for this
 * note): the season note (26/10), the species page "Trucha" (10 and 13/11: Macchi and Vigliano 2014, Giese abstract, IGFA), the Río Santa Cruz guide (14/11:
 * Disposición 241/2026 recital and Annex I, Lázari 2024, Riva Rossi 2003, Pascual 2001), and the guides of each base. NOT written, on purpose: typical sizes and
 * records (the species page has them, attributed), prices (three dated notes are linked), operator names, anything about "best".
 * Also: one link paragraph to the pillar on the species page and on the season note (idempotent: skipped when the pillar URL is already there).
 * Cover photo: the one already loaded for the note `precio-semana-trucha-patagonia-norte` (Commons, Boca del Chimehuín; provisional, see docs/estado.md).
 * FAQ: group block `pm-faq` (pm_sc_faq): the visible FAQ is the FAQPage JSON-LD source.
 * Idempotent: a note that exists (same clave + language) is never touched. Dry run first ($aplicar = false). Run with wp-script.yml
 * (script = nota-trucha-patagonia-pilar-2026-11-15).
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
	function pm_sc_modo( bool $aplicar ): string {
		return $aplicar ? 'APLICA' : 'SIMULACRO';
	}
}
if ( ! function_exists( 'pm_tp_tabla' ) ) {
	function pm_tp_tabla( array $cabecera, array $filas ): string {
		$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
		$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
		return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->\n\n";
	}
	/** The dated yearly box (same markup as the other notes): the only place with dates that change every season. */
	function pm_tp_caja( string $titulo, array $items, string $pie ): string {
		$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
		return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph -->\n<p><em>{$pie}</em></p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n";
	}
	/** Guide (pm_destino) of a clave in a language, else in Spanish. */
	function pm_tp_guia( string $clave, string $i ): ?WP_Post {
		return pm_buscar( 'pm_destino', $clave, $i ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	}
}

$aplicar = true;
$hoy     = '20261115';
$clave   = 'trucha-patagonia-pilar';
$foto_de = 'precio-semana-trucha-patagonia-norte'; // note whose cover photo is reused (Commons, Boca del Chimehuín)

// External links (all read and published before; the date each was read is in the sources list below).
$u = [
	'macchi'   => 'https://www.scielo.org.ar/scielo.php?script=sci_arttext&pid=S1667-782X2014000200005',
	'bo'       => 'https://boletinoficial.santacruz.gob.ar/legislacion/disposiciones-completas/60897',
	'pdf'      => 'https://boletinoficial.santacruz.gob.ar/adjuntos/DISPOSICION_N_241_-_REGLAMENTO_2.pdf',
	'lazari'   => 'https://doi.org/10.1111/jfb.15722',
	'rr3'      => 'https://bibliotecadigital.exactas.uba.ar/collection/ecologiaaustral/document/ecologiaaustral_v013_n02_p151',
	'llompart' => 'https://bibliotecadigital.exactas.uba.ar/collection/ecologiaaustral/document/ecologiaaustral_v034_n02_p272',
	'giese'    => 'https://rdi.uncoma.edu.ar/handle/uncomaid/18463',
	'igfa'     => 'https://igfa.org/member-services/world-record/common-name/Trout,%20brown',
	'moen'     => 'https://www.youtube.com/watch?v=2dRfHmdFmSI',
	'fb_rg'    => 'https://farbank.com/blogs/fly-water-travel/rio-grande-seasonal-breakdown',
	'fb_jl'    => 'https://farbank.com/blogs/fly-water-travel/when-is-the-best-time-to-fish-at-jurassic-lake-lodge',
	'yd'       => 'https://www.yellowdogflyfishing.com/blogs/back-stage-pass/frequently-asked-questions-fly-fishing-patagonia',
	'enc'      => 'https://www.aardvarkmcleod.com/autumn-highlights-at-el-encuentro-lodge-seasons/',
	'regl'     => 'https://reglamentodepesca.org.ar/reglamento/',
	'nqn'      => 'https://cazaypesca.neuquen.gob.ar/permisos',
	'anb'      => 'https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027',
	'noticias' => 'https://www.noticiasnqn.com.ar/noticias/2026/10/15/356687-neuquen-habilito-la-compra-de-permisos-para-la-nueva-temporada-de-pesca',
];

$N = [];

// =====================================================================================================================================
// ENGLISH
// =====================================================================================================================================
$N['en'] = [
	'titulo'          => 'Trout fishing in Patagonia, Argentina: brown, rainbow and sea-run',
	'slug'            => 'trout-fishing-in-patagonia',
	'seo_title'       => 'Trout fishing in Patagonia, Argentina: brown and rainbow',
	'seo_description' => 'Trout fishing in Patagonia, Argentina: brown, rainbow and sea-run trout, where to go, when, which permit and how to book a fishing trip, with sources.',
	'bajada'          => 'Which trout you can fish in Argentine Patagonia, where, when and with which permit, and what the sources say about brown, rainbow and sea-run fish: with dates and links.',
	'datos'           => [
		'Three trout are fished in Patagonia: rainbow, brown and brook. None is native: the first official stocking of salmonids was in 1904 (Macchi and Vigliano, Ecología Austral, 2014).',
		'Two have a sea-run form: the brown trout of the Río Grande and the Río Gallegos, and the rainbow trout (steelhead) of the Santa Cruz River.',
		'The season opens in early November and closes between April and May; each water has its own dates.',
		'You need the season permit of the province whose water you fish; the permit notes are linked below.',
		'No price is quoted here: dated prices are in three separate notes.',
	],
	'p0'              => '<strong>Short answer.</strong> Argentine Patagonia has three different trout fisheries that people often mix up: rainbow and brown trout that live in rivers and lakes, sea-run brown trout in the far south, and the steelhead, a sea-run rainbow, in the Santa Cruz River.',
	'resp'            => [
		'<strong>Which trout:</strong> rainbow, brown and brook; none is native. The sea-run forms are the brown trout of the Río Grande and the Río Gallegos and the rainbow (steelhead) of the Santa Cruz River.',
		'<strong>Where:</strong> from Neuquén to Tierra del Fuego; the table below links to our guide for each base.',
		'<strong>When:</strong> the season opens in early November and closes between April and May, with different dates for each water; the best window depends on the zone ({n:cuando-pescar-trucha-patagonia|month-by-month note}).',
		'<strong>Permit:</strong> the season permit of the province where you fish ({n:licencias-extranjeros-argentina|permits for foreigners by region}; {n:licencia-pesca-patagonia-precios|the Patagonian permit}).',
		'<strong>Cost:</strong> not quoted here; dated prices are in three notes, linked in the cost section below.',
	],
	'h_donde'         => 'Where to go on a trout fishing trip in Patagonia',
	'donde_i'         => 'Our guide for each base, from north to south. The rules, the permit and the fishing lodges and fishing guides that publish information are in the guide itself.',
	'donde_cols'      => [ 'Base', 'Waters in the guide', 'Trout in the guide' ],
	'donde_rows'      => [
		[ '<a href="{g:san-martin-junin}">San Martín and Junín de los Andes</a> (Neuquén)', 'Chimehuín, Malleo and Quilquihue rivers; Collón Curá and Aluminé a little further out', 'Trout in rivers and lakes; rules by water' ],
		[ '<a href="{g:bariloche}">Bariloche</a> (Río Negro)', 'Lake Nahuel Huapi; Manso, Traful and upper Limay rivers', 'Trout in rivers and lakes; rules by water' ],
		[ '<a href="{g:esquel-trevelin}">Esquel and Trevelin</a> (Chubut)', 'Futaleufú, Carrileufú, lakes Rosario and Fontana, rivers around Los Alerces National Park', 'Brown, rainbow and brook trout, in the operators’ descriptions' ],
		[ '<a href="{g:jurassic-lake}">Jurassic Lake (Lago Strobel)</a> (Santa Cruz)', 'Lago Strobel and the Barrancoso River', 'Rainbow trout' ],
		[ '<a href="{g:rio-santa-cruz}">Santa Cruz River at Piedra Buena</a> (Santa Cruz)', 'Santa Cruz River', 'Steelhead: sea-run rainbow trout' ],
		[ '<a href="{g:rio-gallegos}">Río Gallegos</a> (Santa Cruz)', 'Río Gallegos', 'Sea-run brown trout' ],
		[ '<a href="{g:rio-grande}">Río Grande</a> (Tierra del Fuego)', 'Río Grande and its tributary the Menéndez', 'Sea-run brown trout' ],
	],
	'h_marron'        => 'Brown trout in Patagonia and Tierra del Fuego (Salmo trutta)',
	'm1'              => 'The brown trout is not native. <a href="{u:macchi}" rel="noopener">Macchi and Vigliano (<em>Ecología Austral</em>, 2014)</a> analysed the stocking of 54 basins between 1904 and 2011: brown trout was stocked in 67.3% of them, rainbow in 96.4% and brook trout in 72.7% (read 10/11/2026).',
	'm2'              => '<strong>Resident browns</strong> are brown trout that never go to the sea. Which species you meet depends on the water and on its stocking history, so our guides list the waters and the rules for each one and do not rank them. One Esquel-area lodge says that March and early April can be exceptional for browns: it is one operator’s description, in our <a href="{g:esquel-trevelin}">Esquel guide</a> (read 4/11/2026).',
	'm3'              => '<strong>Sea-run brown trout</strong> live part of their life in the Atlantic and come up a river to spawn. Our guides cover two rivers where they are fished: the <a href="{g:rio-grande}">Río Grande</a>, in Tierra del Fuego, and the <a href="{g:rio-gallegos}">Río Gallegos</a>, in Santa Cruz. According to Todd Moen, in a <a href="{u:moen}" rel="noopener">Catch Magazine video</a>, the introduced browns of the Río Grande developed the habit of migrating to the Atlantic to feed because the river holds little forage. Farbank writes that fish start entering the Río Grande in November and keep entering until April, and that operators concentrate the best between the first week of January and the first week of April (<a href="{u:fb_rg}" rel="noopener">Río Grande seasonal breakdown</a>, read 26/10/2026).',
	'm4'              => 'A thesis from the Universidad Nacional del Comahue (<a href="{u:giese}" rel="noopener">Giese</a>) compares the adult populations of the Gallegos, Grande and Irigoyen rivers. According to its abstract, adults were longer in the Gallegos than in the Fuegian rivers and no weight differences were observed among the three rivers (abstract read 13/11/2026). Sizes and records, each with its source, are on the {esp:trucha|trout species page}.',
	'h_arco'          => 'Rainbow trout (Oncorhynchus mykiss)',
	'a1'              => 'The rainbow is the most widely stocked trout in Patagonia: 96.4% of the basins analysed by Macchi and Vigliano (2014).',
	'a2'              => '<strong>Resident rainbows.</strong> Lago Strobel, which anglers abroad know as Jurassic Lake, is in central Santa Cruz and is known for its rainbow trout; its main inflow is the Barrancoso River. “Jurassic Lake” is a lodge’s trade mark, not the name of the lake. Rules, permit and how to get there are in our <a href="{g:jurassic-lake}">Jurassic Lake guide</a>.',
	'h_steel'         => 'The steelhead of the Santa Cruz River',
	's1'              => 'The <a href="{g:rio-santa-cruz}">Santa Cruz River</a>, in Santa Cruz province, is the river of a rainbow trout that goes down to the sea and comes back up: the steelhead. Disposición 241/2026 of the provincial Fisheries and Aquaculture Secretariat calls it, in its recitals, “la única población silvestre de linaje atlántico en el continente americano” (“the only wild population of Atlantic lineage in the American continent”; quoted word for word, our translation; <a href="{u:bo}" rel="noopener">Boletín Oficial de Santa Cruz</a>, read 13/11/2026).',
	's2'              => '<a href="{u:rr3}" rel="noopener">Riva Rossi, Arguimbau and Pascual (2003, <em>Ecología Austral</em>)</a> sum it up this way: the Santa Cruz is “el único ambiente en el mundo donde la trucha arco iris introducida desarrolló poblaciones migratorias facultativas” (“the only environment in the world where introduced rainbow trout developed facultative migratory populations”; our translation), meaning that part of the fish complete their cycle in fresh water and part go to the sea. Pascual et al. (2001) documented the first case of anadromy in this introduced trout, and according to <a href="{u:lazari}" rel="noopener">Lázari et al. (2024, <em>Journal of Fish Biology</em>)</a> the rainbow was stocked in the basin between 1906 and 1910, presumably from California or Oregon (read 14/11/2026).',
	's3'              => '<strong>When:</strong> there is no official steelhead “season” date: the river is open all year. Riva Rossi et al. (2003) describe a fishery from February to May, and <a href="{u:llompart}" rel="noopener">Llompart et al. (2024)</a> counted peaks of fishing effort near Piedra Buena from March to May and from September to October. Annex I of the 2026/27 regulation makes catch-and-release mandatory for the steelhead from 15/03/2027 to 30/09/2027 and sets a quota of one fish per angler per season (<a href="{u:pdf}" rel="noopener">Disposición 241/2026</a>, read 13/11/2026). We do not present that as permission to keep a fish: the full text, the permit and the information the operators publish are in the <a href="{g:rio-santa-cruz}">Santa Cruz River guide</a>.',
	'h_mar'           => 'Sea-run trout: what the word covers',
	'mar1'            => '“Sea-run” is used for two different fish in Patagonia. Both are introduced trout whose sea-going habit developed in Patagonian rivers; they do not share a species, a river or a calendar.',
	'mar_cols'        => [ 'Form', 'Fish', 'Rivers', 'When, according to the sources' ],
	'mar_rows'        => [
		[ 'Sea-run brown trout', 'Salmo trutta', '<a href="{g:rio-grande}">Río Grande</a>, <a href="{g:rio-gallegos}">Río Gallegos</a>', 'Río Grande: fish start entering in November and keep entering until April, with the best between early January and early April (Farbank). Río Gallegos: the lodges’ prime season runs from late December to April (our guide)' ],
		[ 'Steelhead', 'Oncorhynchus mykiss (rainbow)', '<a href="{g:rio-santa-cruz}">Santa Cruz River</a>', 'Autumn, from late February to May (studies and operators, see the guide); the river is open all year' ],
	],
	'mar2'            => 'We give no typical size here: the figures differ between sources and between rivers. The {esp:trucha|species page} has the ones we can name, each attributed. One fact on records: the IGFA keeps a single category for the brown trout, with “sea trout” among its alternative names and no separate category for the sea form (<a href="{u:igfa}" rel="noopener">IGFA</a>, read 13/11/2026).',
	'h_cuando'        => 'When to fish trout in Patagonia',
	'c1'              => 'The season opens in early November and closes between April and May, with different dates for each water. The windows below are what operators and guides say, not official dates; the long version, month by month and by type of river, is in {n:cuando-pescar-trucha-patagonia|When to fish trout in Patagonia}.',
	'c_cols'          => [ 'Zone', 'Window, according to the sources' ],
	'c_rows'          => [
		[ '<strong>North</strong> (Neuquén and Río Negro)', 'Operators describe the season as November to April, with January to March the most requested (<a href="{u:yd}" rel="noopener">Yellow Dog Fly Fishing</a>)' ],
		[ '<strong>Centre</strong> (Esquel)', 'A lodge in the area gives 1 November to 30 April, with high snowmelt water at the start, and March–April as the best time for dry flies (<a href="{u:enc}" rel="noopener">El Encuentro Lodge</a>)' ],
		[ '<strong>South</strong> (Jurassic Lake)', 'The lodge season runs from September to May: the start, until mid-January, is the peak, and from mid-March to May the autumn is exceptional again (<a href="{u:fb_jl}" rel="noopener">Farbank</a>)' ],
		[ '<strong>Tierra del Fuego</strong> (Río Grande)', 'Fish start entering in November and keep entering until April; operators concentrate the best between the first week of January and the first week of April (<a href="{u:fb_rg}" rel="noopener">Farbank</a>)' ],
	],
	'c2'              => 'The Santa Cruz River steelhead is an autumn fishery and has its own section above.',
	'h_permiso'       => 'Which permit you need',
	'pe1'             => 'To fish trout in Patagonia you need the season permit of the province where the water is. Each province sets its own dates and values, and whether the Patagonian permit is valid on the water you want depends on the province and the year. The notes below explain it, with dates and sources:',
	'pe_l'            => [
		'{n:licencias-extranjeros-argentina|Fishing license for foreigners in Argentina 2026/27: costs by region}',
		'{n:licencia-pesca-patagonia-precios|Patagonian Argentina fishing license 2026/27: cost and validity}',
		'{n:reglas-equipo-patagonia|Fishing gear and rules in Patagonia: felt soles, hooks and disinfection (2026/27)}',
	],
	'caja_t'          => 'Up to date · 15/11/2026',
	'caja'            => [
		'According to the press of 15 and 16 October 2026, Neuquén’s 2026-2027 season opens on 1 November 2026 and permits are sold online since 15 October (<a href="{u:anb}" rel="noopener">ANBariloche</a>, <a href="{u:noticias}" rel="noopener">NoticiasNQN</a>; <a href="{u:nqn}" rel="noopener">Caza y Pesca de Neuquén</a>).',
		'Santa Cruz’s 2026/27 regulation is Disposición 241/2026, in force from 1/10/2026 (<a href="{u:bo}" rel="noopener">Boletín Oficial de Santa Cruz</a>, read 13/11/2026).',
		'Dates, permits and fees change every season: your guide and the provincial authority have them up to date.',
	],
	'caja_pie'        => 'This box is updated once a year; the rest of the note does not change.',
	'h_costo'         => 'How much a trout fishing trip costs',
	'co1'             => 'We do not repeat prices here: each one is valid for one operator, a number of nights and a season. Dated prices for fishing lodges and guided fly fishing days, with their source, are in three notes:',
	'co_l'            => [
		'{n:precio-semana-trucha-patagonia-norte|How much does a week of trout fishing cost in Neuquén and Chubut: all-inclusive lodges (2026/27)}',
		'{n:precio-pesca-bariloche-limay|How much does a day of fly fishing cost in Bariloche and the Limay River (2026/27)}',
		'{n:precio-pesca-patagonia-sur|How much does it cost to fish in Tierra del Fuego and El Calafate: sea trout and one-day trips (2026/27)}',
	],
	'cta'             => '<strong>Planning a trout fishing trip?</strong> Tell us your dates and group size in <a href="{viaje}">the form</a>; we ask the operators that fit and reply within 48 hours with the options side by side, at no cost to you.',
	'faq_h'           => 'Frequently asked questions',
	'faq'             => [
		[ 'When is the best time to fish trout in Patagonia?', 'The season opens in early November and closes between April and May, with different dates for each water (Patagonian regulation, as in our guides). The best window depends on the zone: operators describe January to March as the most requested in the north (Yellow Dog Fly Fishing), the start of the season and the autumn at Lago Strobel (Farbank), the autumn for the Santa Cruz River steelhead, and January to early April for sea-run browns in Tierra del Fuego (Farbank). These are operators’ descriptions read on 26/10/2026, not official dates; the details are in {n:cuando-pescar-trucha-patagonia|When to fish trout in Patagonia}.' ],
		[ 'Do I need a permit to fish trout in Patagonia?', 'Yes. You need the season permit of the province where the water is; each province sets its own dates and values, and in the National Parks the park’s own rules apply as well (our guides, read between 30/10 and 14/11/2026). Whether the Patagonian permit is valid on a given water depends on the province and the year: see {n:licencia-pesca-patagonia-precios|the Patagonian permit note} and {n:licencias-extranjeros-argentina|the permits-by-region note}. For Neuquén, the press of 15 and 16 October 2026 says permits are sold online since 15 October.' ],
		[ 'Which is better in Patagonia, brown or rainbow trout?', 'Neither is better in general: they are different fisheries and which one you meet depends on the water. Both were introduced (the first official stocking of salmonids was in 1904) and both were stocked widely: rainbow in 96.4% and brown in 67.3% of the 54 basins analysed by Macchi and Vigliano (<em>Ecología Austral</em>, 2014; read 10/11/2026). We do not rank species or places: ask your guide about the water and the month.' ],
		[ 'What is a sea-run brown trout, and where is it fished?', 'It is a brown trout that lives part of its life in the Atlantic and comes up a river to spawn. Our guides cover two rivers: the Río Grande (Tierra del Fuego) and the Río Gallegos (Santa Cruz). According to Todd Moen (Catch Magazine video), the introduced browns of the Río Grande began to migrate to the ocean to feed because the river holds little forage. The IGFA lists the brown trout record under a single name, “Trout, brown (Salmo trutta)”, with “sea trout” among its alternative names and no separate category for the sea form (read 13/11/2026).' ],
		[ 'Is there steelhead in Argentina?', 'Yes, in one river: the Santa Cruz River, near Comandante Luis Piedra Buena. The steelhead is a rainbow trout that goes to the sea and comes back. Disposición 241/2026 calls it, in its recitals, “la única población silvestre de linaje atlántico en el continente americano” (quoted word for word; our translation: “the only wild population of Atlantic lineage in the American continent”), and Riva Rossi, Arguimbau and Pascual (2003) call the river “the only environment in the world where introduced rainbow trout developed facultative migratory populations” (our translation). Studies and operators place the run in the autumn, from late February to May; the river is open all year. See the {g:rio-santa-cruz|Santa Cruz River guide}.' ],
		[ 'Can I keep the trout I catch in Patagonia?', 'It depends on the water and the season. Many Patagonian waters are fly-only, with barbless hooks and mandatory release, and others allow a limit (Patagonian regulation, as in our guides). For the Santa Cruz River steelhead, Annex I of Disposición 241/2026 sets a quota of one fish per angler per season and mandatory release from 15/03/2027 to 30/09/2027 (read 13/11/2026); we do not present that as permission to keep a fish. Confirm with your guide or the provincial authority before you fish, and see {n:reglas-equipo-patagonia|the gear and rules note}.' ],
		[ 'Where can I book a trout fishing trip or a fishing lodge in Patagonia?', 'Our guides cover San Martín and Junín de los Andes (Neuquén), Bariloche (Río Negro), Esquel and Trevelin (Chubut), Lago Strobel or “Jurassic Lake”, the Santa Cruz River and the Río Gallegos (Santa Cruz) and the Río Grande (Tierra del Fuego). The table above links to each one, with its waters, rules and permit, and the fishing lodges and fishing guides that publish information. To compare a fly fishing package, send your dates in the form linked in this note and we ask the operators that fit.' ],
		[ 'How much does a trout fishing trip to Patagonia cost?', 'We do not quote a price here, because each price is valid for one operator, a number of nights and a season. Dated prices for fishing lodges, guided days and fly fishing packages, with their source, are in three notes: a week of trout fishing in Neuquén and Chubut, a day of fly fishing in Bariloche and the Limay, and Tierra del Fuego and El Calafate (all 2026/27). Their links are in the cost section above.' ],
	],
	'cierre'          => '<em>We have not spoken with the operators or authors cited, nor fished with them. This note summarises public information already published on this site, each fact with its source and the date it was read; the reading is ours and may contain mistakes.</em>',
];

// =====================================================================================================================================
// ESPAÑOL
// =====================================================================================================================================
$N['es'] = [
	'titulo'          => 'Pesca de trucha en la Patagonia: marrón, arco iris y de mar',
	'slug'            => 'pesca-de-trucha-en-la-patagonia',
	'seo_title'       => 'Pesca de trucha en la Patagonia: marrón, arco iris, de mar',
	'seo_description' => 'Trucha marrón, arco iris y de mar en la Patagonia argentina: dónde se pesca cada una, cuándo es la temporada y qué permiso hace falta, con fuentes y fechas.',
	'bajada'          => 'Qué truchas se pescan en la Patagonia argentina, dónde, cuándo y con qué permiso, y qué dicen las fuentes sobre la marrón, la arco iris y las de mar: con fechas y enlaces.',
	'datos'           => [
		'En la Patagonia se pescan tres truchas: arco iris, marrón y de arroyo (fontinalis). Ninguna es nativa: la primera siembra oficial de salmónidos fue en 1904 (Macchi y Vigliano, Ecología Austral, 2014).',
		'Dos tienen una forma de mar: la trucha marrón del río Grande y del río Gallegos, y la arco iris (steelhead) del río Santa Cruz.',
		'La temporada abre a comienzos de noviembre y cierra entre abril y mayo; cada ambiente tiene sus propias fechas.',
		'Hace falta el permiso de temporada de la provincia donde pescás; las notas del permiso están enlazadas abajo.',
		'Acá no se da ningún precio: los precios fechados están en tres notas aparte.',
	],
	'p0'              => '<strong>Respuesta corta.</strong> En la Patagonia hay tres pescas de trucha distintas que suelen confundirse: las truchas arco iris y marrón que viven en ríos y lagos, la trucha marrón de mar en el extremo sur y el steelhead, una arco iris de mar, en el río Santa Cruz.',
	'resp'            => [
		'<strong>Qué trucha:</strong> arco iris, marrón y de arroyo; ninguna es nativa. Las formas de mar son la trucha marrón del río Grande y del río Gallegos y la arco iris (steelhead) del río Santa Cruz.',
		'<strong>Dónde:</strong> de Neuquén a Tierra del Fuego; la tabla de abajo enlaza a nuestra guía de cada base.',
		'<strong>Cuándo:</strong> la temporada abre a comienzos de noviembre y cierra entre abril y mayo, con fechas distintas para cada ambiente; la mejor ventana depende de la zona ({n:cuando-pescar-trucha-patagonia|nota mes por mes}).',
		'<strong>Permiso:</strong> el permiso de temporada de la provincia donde pescás ({n:licencias-extranjeros-argentina|permisos para extranjeros por región}; {n:licencia-pesca-patagonia-precios|el permiso patagónico}).',
		'<strong>Cuánto cuesta:</strong> no se da acá; los precios fechados están en tres notas, enlazadas en la sección de costo.',
	],
	'h_donde'         => 'Dónde pescar trucha en la Patagonia',
	'donde_i'         => 'Nuestra guía de cada base, de norte a sur. Las reglas, el permiso y los operadores que publican información están en la guía.',
	'donde_cols'      => [ 'Base', 'Aguas en la guía', 'Trucha en la guía' ],
	'donde_rows'      => [
		[ '<a href="{g:san-martin-junin}">San Martín y Junín de los Andes</a> (Neuquén)', 'Ríos Chimehuín, Malleo y Quilquihue; Collón Curá y Aluminé un poco más lejos', 'Trucha en ríos y lagos; reglas por ambiente' ],
		[ '<a href="{g:bariloche}">Bariloche</a> (Río Negro)', 'Lago Nahuel Huapi; ríos Manso, Traful y Limay superior', 'Trucha en ríos y lagos; reglas por ambiente' ],
		[ '<a href="{g:esquel-trevelin}">Esquel y Trevelin</a> (Chubut)', 'Futaleufú, Carrileufú, lagos Rosario y Fontana, ríos del entorno del Parque Nacional Los Alerces', 'Trucha marrón, arco iris y de arroyo, en la descripción de los operadores' ],
		[ '<a href="{g:jurassic-lake}">Jurassic Lake (lago Strobel)</a> (Santa Cruz)', 'Lago Strobel y río Barrancoso', 'Trucha arco iris' ],
		[ '<a href="{g:rio-santa-cruz}">Río Santa Cruz en Piedra Buena</a> (Santa Cruz)', 'Río Santa Cruz', 'Steelhead: trucha arco iris de mar' ],
		[ '<a href="{g:rio-gallegos}">Río Gallegos</a> (Santa Cruz)', 'Río Gallegos', 'Trucha marrón de mar' ],
		[ '<a href="{g:rio-grande}">Río Grande</a> (Tierra del Fuego)', 'Río Grande y su afluente el Menéndez', 'Trucha marrón de mar' ],
	],
	'h_marron'        => 'Trucha marrón (Salmo trutta)',
	'm1'              => 'La trucha marrón no es nativa. <a href="{u:macchi}" rel="noopener">Macchi y Vigliano (<em>Ecología Austral</em>, 2014)</a> analizaron las siembras de 54 cuencas entre 1904 y 2011: la marrón se sembró en el 67,3 % de ellas, la arco iris en el 96,4 % y la fontinalis en el 72,7 % (leído el 10/11/2026).',
	'm2'              => '<strong>Las marrones residentes</strong> son truchas marrones que nunca van al mar. Cuál encontrás depende del ambiente y de su historia de siembras, así que nuestras guías enumeran las aguas y las reglas de cada una y no las ordenan. Un lodge de la zona de Esquel dice que marzo y comienzos de abril pueden ser excepcionales para las marrones: es la descripción de un operador, en nuestra <a href="{g:esquel-trevelin}">guía de Esquel</a> (leída el 4/11/2026).',
	'm3'              => '<strong>Las truchas marrones de mar</strong> viven parte de su vida en el Atlántico y suben a un río a desovar. Nuestras guías cubren dos ríos donde se pescan: el <a href="{g:rio-grande}">río Grande</a>, en Tierra del Fuego, y el <a href="{g:rio-gallegos}">río Gallegos</a>, en Santa Cruz. Según Todd Moen, en un <a href="{u:moen}" rel="noopener">video de Catch Magazine</a>, las marrones introducidas del río Grande desarrollaron el hábito de migrar al Atlántico para alimentarse porque el río tiene poco forraje. Farbank escribe que los peces empiezan a entrar al río Grande en noviembre y siguen entrando hasta abril, y que los operadores concentran lo mejor entre la primera semana de enero y la primera de abril (<a href="{u:fb_rg}" rel="noopener">Río Grande seasonal breakdown</a>, leído el 26/10/2026).',
	'm4'              => 'Una tesis de la Universidad Nacional del Comahue (<a href="{u:giese}" rel="noopener">Giese</a>) compara las poblaciones adultas de los ríos Gallegos, Grande e Irigoyen. Según su resumen, los adultos fueron más largos en el Gallegos que en los ríos fueguinos y no se observaron diferencias de peso entre los tres ríos (resumen leído el 13/11/2026). Los tamaños y los récords, cada uno con su fuente, están en la {esp:trucha|ficha de la trucha}.',
	'h_arco'          => 'Trucha arco iris (Oncorhynchus mykiss)',
	'a1'              => 'La arco iris es la trucha más sembrada de la Patagonia: el 96,4 % de las cuencas que analizaron Macchi y Vigliano (2014).',
	'a2'              => '<strong>Las arco iris residentes.</strong> El lago Strobel, que los pescadores de afuera conocen como Jurassic Lake, está en el centro de Santa Cruz y es conocido por sus truchas arco iris; su afluente principal es el río Barrancoso. «Jurassic Lake» es una marca registrada de un lodge, no el nombre del lago. Las reglas, el permiso y cómo llegar están en nuestra <a href="{g:jurassic-lake}">guía de Jurassic Lake</a>.',
	'h_steel'         => 'El steelhead del río Santa Cruz',
	's1'              => 'El <a href="{g:rio-santa-cruz}">río Santa Cruz</a>, en la provincia de Santa Cruz, es el río de una trucha arco iris que baja al mar y vuelve a subir: el steelhead. La Disposición 241/2026 de la Secretaría de Estado de Pesca y Acuicultura la llama, en sus considerandos, «la única población silvestre de linaje atlántico en el continente americano» (cita textual; <a href="{u:bo}" rel="noopener">Boletín Oficial de Santa Cruz</a>, leído el 13/11/2026).',
	's2'              => '<a href="{u:rr3}" rel="noopener">Riva Rossi, Arguimbau y Pascual (2003, <em>Ecología Austral</em>)</a> lo resumen así: el Santa Cruz es «el único ambiente en el mundo donde la trucha arco iris introducida desarrolló poblaciones migratorias facultativas», es decir, una parte de los peces completa su ciclo en agua dulce y otra viaja al mar. Pascual et al. (2001) documentaron el primer caso de anadromía en esta trucha introducida, y según <a href="{u:lazari}" rel="noopener">Lázari et al. (2024, <em>Journal of Fish Biology</em>)</a> la arco iris se sembró en la cuenca entre 1906 y 1910, presumiblemente desde California u Oregón (leído el 14/11/2026).',
	's3'              => '<strong>Cuándo:</strong> no hay una fecha oficial de «temporada» para el steelhead: el río está abierto todo el año. Riva Rossi et al. (2003) describen una pesquería de febrero a mayo, y <a href="{u:llompart}" rel="noopener">Llompart et al. (2024)</a> contaron picos de esfuerzo de pesca cerca de Piedra Buena de marzo a mayo y de septiembre a octubre. El Anexo I del reglamento 2026/27 establece la captura y devolución obligatoria del steelhead del 15/03/2027 al 30/09/2027 y un cupo de un ejemplar por pescador y por temporada (<a href="{u:pdf}" rel="noopener">Disposición 241/2026</a>, leída el 13/11/2026). No lo presentamos como un permiso para quedarse con un pez: el texto completo, el permiso y lo que publican los operadores están en la <a href="{g:rio-santa-cruz}">guía del río Santa Cruz</a>.',
	'h_mar'           => 'Trucha de mar: qué abarca la palabra',
	'mar1'            => '«De mar» se usa en la Patagonia para dos peces distintos. Los dos son truchas introducidas cuyo hábito de ir al mar se desarrolló en ríos patagónicos; no comparten especie, río ni calendario.',
	'mar_cols'        => [ 'Forma', 'Pez', 'Ríos', 'Cuándo, según las fuentes' ],
	'mar_rows'        => [
		[ 'Trucha marrón de mar', 'Salmo trutta', '<a href="{g:rio-grande}">Río Grande</a>, <a href="{g:rio-gallegos}">río Gallegos</a>', 'Río Grande: los peces empiezan a entrar en noviembre y siguen entrando hasta abril, con lo mejor entre comienzos de enero y comienzos de abril (Farbank). Río Gallegos: la temporada fuerte de los lodges va de fines de diciembre a abril (nuestra guía)' ],
		[ 'Steelhead', 'Oncorhynchus mykiss (arco iris)', '<a href="{g:rio-santa-cruz}">Río Santa Cruz</a>', 'Otoño, de fines de febrero a mayo (estudios y operadores, ver la guía); el río está abierto todo el año' ],
	],
	'mar2'            => 'No damos un tamaño típico: las cifras difieren entre fuentes y entre ríos. La {esp:trucha|ficha de la especie} tiene las que podemos nombrar, cada una atribuida. Un dato sobre récords: la IGFA tiene una sola categoría para la trucha marrón, con «sea trout» entre sus nombres alternativos y sin una categoría aparte para la forma de mar (<a href="{u:igfa}" rel="noopener">IGFA</a>, leída el 13/11/2026).',
	'h_cuando'        => 'Cuándo pescar trucha en la Patagonia',
	'c1'              => 'La temporada abre a comienzos de noviembre y cierra entre abril y mayo, con fechas distintas para cada ambiente. Las ventanas de abajo son lo que dicen operadores y guías, no fechas oficiales; la versión larga, mes por mes y por tipo de río, está en {n:cuando-pescar-trucha-patagonia|Cuándo pescar trucha en la Patagonia}.',
	'c_cols'          => [ 'Zona', 'Ventana, según las fuentes' ],
	'c_rows'          => [
		[ '<strong>Norte</strong> (Neuquén y Río Negro)', 'Los operadores describen la temporada como noviembre a abril, con enero a marzo como lo más pedido (<a href="{u:yd}" rel="noopener">Yellow Dog Fly Fishing</a>)' ],
		[ '<strong>Centro</strong> (Esquel)', 'Un lodge de la zona indica del 1 de noviembre al 30 de abril, con agua alta de deshielo al principio, y marzo-abril como lo mejor para la seca (<a href="{u:enc}" rel="noopener">El Encuentro Lodge</a>)' ],
		[ '<strong>Sur</strong> (Jurassic Lake)', 'La temporada del lodge va de septiembre a mayo: el comienzo, hasta mediados de enero, es el pico, y de mediados de marzo a mayo el otoño vuelve a ser excepcional (<a href="{u:fb_jl}" rel="noopener">Farbank</a>)' ],
		[ '<strong>Tierra del Fuego</strong> (río Grande)', 'Los peces empiezan a entrar en noviembre y siguen entrando hasta abril; los operadores concentran lo mejor entre la primera semana de enero y la primera de abril (<a href="{u:fb_rg}" rel="noopener">Farbank</a>)' ],
	],
	'c2'              => 'El steelhead del río Santa Cruz es una pesca de otoño y tiene su propia sección más arriba.',
	'h_permiso'       => 'Qué permiso hace falta',
	'pe1'             => 'Para pescar trucha en la Patagonia hace falta el permiso de temporada de la provincia donde está el ambiente. Cada provincia fija sus fechas y sus valores, y que el permiso patagónico valga en el ambiente que querés depende de la provincia y del año. Las notas de abajo lo explican, con fechas y fuentes:',
	'pe_l'            => [
		'{n:licencias-extranjeros-argentina|Licencia de pesca para extranjeros en Argentina 2026/27: costos por región}',
		'{n:licencia-pesca-patagonia-precios|Licencia de pesca de la Patagonia argentina 2026/27: costo y validez}',
		'{n:reglas-equipo-patagonia|Equipo y reglas de pesca en la Patagonia: suela de fieltro, anzuelos y desinfección (2026/27)}',
	],
	'caja_t'          => 'Al día · 15/11/2026',
	'caja'            => [
		'Según la prensa del 15 y 16 de octubre de 2026, la temporada 2026-2027 de Neuquén abre el 1 de noviembre de 2026 y los permisos se venden online desde el 15 de octubre (<a href="{u:anb}" rel="noopener">ANBariloche</a>, <a href="{u:noticias}" rel="noopener">NoticiasNQN</a>; <a href="{u:nqn}" rel="noopener">Caza y Pesca de Neuquén</a>).',
		'El reglamento 2026/27 de Santa Cruz es la Disposición 241/2026, en vigor desde el 1/10/2026 (<a href="{u:bo}" rel="noopener">Boletín Oficial de Santa Cruz</a>, leído el 13/11/2026).',
		'Las fechas, los permisos y las tarifas cambian cada temporada: tu guía y la autoridad de la provincia los tienen al día.',
	],
	'caja_pie'        => 'Esta caja se actualiza una vez por año; el resto de la nota no cambia.',
	'h_costo'         => 'Cuánto cuesta un viaje de pesca de trucha',
	'co1'             => 'No repetimos precios acá: cada uno vale para un operador, una cantidad de noches y una temporada. Los precios fechados, con su fuente, están en tres notas:',
	'co_l'            => [
		'{n:precio-semana-trucha-patagonia-norte|Cuánto cuesta una semana de pesca de trucha en Neuquén y Chubut: lodges todo incluido (2026/27)}',
		'{n:precio-pesca-bariloche-limay|Cuánto cuesta un día de pesca con mosca en Bariloche y el río Limay (2026/27)}',
		'{n:precio-pesca-patagonia-sur|Cuánto cuesta pescar en Tierra del Fuego y El Calafate: trucha de mar y salidas de un día (2026/27)}',
	],
	'cta'             => '<strong>¿Querés que les preguntemos a los operadores que encajan?</strong> Contanos tus fechas y cuántos van en <a href="{viaje}">el formulario</a>; preguntamos a los operadores que encajan y en 48 horas te respondemos con las opciones comparadas, sin costo para vos.',
	'faq_h'           => 'Preguntas frecuentes',
	'faq'             => [
		[ '¿Cuándo es la mejor época para pescar trucha en la Patagonia?', 'La temporada abre a comienzos de noviembre y cierra entre abril y mayo, con fechas distintas para cada ambiente (reglamento patagónico, como en nuestras guías). La mejor ventana depende de la zona: los operadores describen enero a marzo como lo más pedido en el norte (Yellow Dog Fly Fishing), el comienzo de la temporada y el otoño en el lago Strobel (Farbank), el otoño para el steelhead del río Santa Cruz y de enero a comienzos de abril para las marrones de mar en Tierra del Fuego (Farbank). Son descripciones de operadores leídas el 26/10/2026, no fechas oficiales; el detalle está en {n:cuando-pescar-trucha-patagonia|Cuándo pescar trucha en la Patagonia}.' ],
		[ '¿Hace falta permiso para pescar trucha en la Patagonia?', 'Sí. Hace falta el permiso de temporada de la provincia donde está el ambiente; cada provincia fija sus fechas y sus valores, y en los Parques Nacionales rigen además las reglas del parque (nuestras guías, leídas entre el 30/10 y el 14/11/2026). Que el permiso patagónico valga en un ambiente depende de la provincia y del año: mirá {n:licencia-pesca-patagonia-precios|la nota del permiso patagónico} y {n:licencias-extranjeros-argentina|la nota de permisos por región}. Para Neuquén, la prensa del 15 y 16 de octubre de 2026 dice que los permisos se venden online desde el 15 de octubre.' ],
		[ '¿Cuál es mejor en la Patagonia, la trucha marrón o la arco iris?', 'Ninguna es mejor en general: son pescas distintas y cuál encontrás depende del ambiente. Las dos fueron introducidas (la primera siembra oficial de salmónidos fue en 1904) y las dos se sembraron mucho: la arco iris en el 96,4 % y la marrón en el 67,3 % de las 54 cuencas que analizaron Macchi y Vigliano (<em>Ecología Austral</em>, 2014; leído el 10/11/2026). No ordenamos especies ni lugares: preguntale a tu guía por el ambiente y el mes.' ],
		[ '¿Qué es una trucha marrón de mar y dónde se pesca?', 'Es una trucha marrón que vive parte de su vida en el Atlántico y sube a un río a desovar. Nuestras guías cubren dos ríos: el río Grande (Tierra del Fuego) y el río Gallegos (Santa Cruz). Según Todd Moen (video de Catch Magazine), las marrones introducidas del río Grande empezaron a migrar al océano para alimentarse porque el río tiene poco forraje. La IGFA lista el récord de la trucha marrón bajo un solo nombre, «Trout, brown (Salmo trutta)», con «sea trout» entre sus nombres alternativos y sin una categoría aparte para la forma de mar (leído el 13/11/2026).' ],
		[ '¿Hay steelhead en la Argentina?', 'Sí, en un río: el río Santa Cruz, cerca de Comandante Luis Piedra Buena. El steelhead es una trucha arco iris que va al mar y vuelve. La Disposición 241/2026 la llama, en sus considerandos, «la única población silvestre de linaje atlántico en el continente americano» (cita textual), y Riva Rossi, Arguimbau y Pascual (2003) dicen que el río es «el único ambiente en el mundo donde la trucha arco iris introducida desarrolló poblaciones migratorias facultativas». Los estudios y los operadores ubican la subida en el otoño, de fines de febrero a mayo; el río está abierto todo el año. Mirá la {g:rio-santa-cruz|guía del río Santa Cruz}.' ],
		[ '¿Puedo quedarme con la trucha que pesco en la Patagonia?', 'Depende del ambiente y de la temporada. Muchos ambientes patagónicos son solo para mosca, con anzuelo sin rebaba y devolución obligatoria, y otros permiten un límite (reglamento patagónico, como en nuestras guías). Para el steelhead del río Santa Cruz, el Anexo I de la Disposición 241/2026 fija un cupo de un ejemplar por pescador y por temporada y la devolución obligatoria del 15/03/2027 al 30/09/2027 (leído el 13/11/2026); no lo presentamos como un permiso para quedarse con un pez. Confirmalo con tu guía o con la autoridad de la provincia antes de pescar, y mirá {n:reglas-equipo-patagonia|la nota de equipo y reglas}.' ],
		[ '¿Dónde se puede pescar trucha en la Patagonia?', 'Nuestras guías cubren San Martín y Junín de los Andes (Neuquén), Bariloche (Río Negro), Esquel y Trevelin (Chubut), el lago Strobel o «Jurassic Lake», el río Santa Cruz y el río Gallegos (Santa Cruz) y el río Grande (Tierra del Fuego). La tabla de arriba enlaza a cada una, con sus aguas, sus reglas y su permiso.' ],
		[ '¿Cuánto cuesta un viaje de pesca de trucha a la Patagonia?', 'No damos un precio acá, porque cada uno vale para un operador, una cantidad de noches y una temporada. Los precios fechados, con su fuente, están en tres notas: una semana de pesca de trucha en Neuquén y Chubut, un día de pesca con mosca en Bariloche y el Limay, y Tierra del Fuego y El Calafate (todas 2026/27). Los enlaces están en la sección de costo de arriba.' ],
	],
	'cierre'          => '<em>No hablamos con los operadores ni con las personas citadas, ni pescamos con ellos. Esta nota resume información pública ya publicada en este sitio, cada dato con su fuente y la fecha en que se leyó; la interpretación es nuestra y puede tener errores.</em>',
];

// =====================================================================================================================================
// PORTUGUÊS
// =====================================================================================================================================
$N['pt'] = [
	'titulo'          => 'Pesca de truta na Patagônia: marrom, arco-íris e anádroma',
	'slug'            => 'pesca-de-truta-na-patagonia',
	'seo_title'       => 'Pesca de truta na Patagônia: marrom, arco-íris, anádroma',
	'seo_description' => 'Truta marrom, arco-íris e anádroma na Patagônia argentina: onde se pesca cada uma, quando é a temporada e qual licença é preciso, com fontes e datas.',
	'bajada'          => 'Quais trutas se pescam na Patagônia argentina, onde, quando e com qual licença, e o que dizem as fontes sobre a marrom, a arco-íris e as do mar: com datas e links.',
	'datos'           => [
		'Na Patagônia se pescam três trutas: arco-íris, marrom e de riacho (fontinalis). Nenhuma é nativa: a primeira semeadura oficial de salmonídeos foi em 1904 (Macchi e Vigliano, Ecología Austral, 2014).',
		'Duas têm uma forma marinha: a truta marrom do rio Grande e do rio Gallegos, e a arco-íris (steelhead) do rio Santa Cruz.',
		'A temporada abre no início de novembro e fecha entre abril e maio; cada ambiente tem suas próprias datas.',
		'É preciso a licença de temporada da província onde você pesca; as notas da licença estão linkadas abaixo.',
		'Aqui não se dá nenhum preço: os preços datados estão em três notas à parte.',
	],
	'p0'              => '<strong>Resposta curta.</strong> Na Patagônia há três pescarias de truta diferentes que costumam ser confundidas: as trutas arco-íris e marrom que vivem em rios e lagos, a truta marrom anádroma no extremo sul e o steelhead, uma arco-íris anádroma, no rio Santa Cruz.',
	'resp'            => [
		'<strong>Qual truta:</strong> arco-íris, marrom e de riacho; nenhuma é nativa. As formas anádromas são a truta marrom do rio Grande e do rio Gallegos e a arco-íris (steelhead) do rio Santa Cruz.',
		'<strong>Onde:</strong> de Neuquén à Terra do Fogo; a tabela abaixo leva ao nosso guia de cada base.',
		'<strong>Quando:</strong> a temporada abre no início de novembro e fecha entre abril e maio, com datas diferentes para cada ambiente; a melhor janela depende da zona ({n:cuando-pescar-trucha-patagonia|nota mês a mês}).',
		'<strong>Licença:</strong> a licença de temporada da província onde você pesca ({n:licencias-extranjeros-argentina|licenças para estrangeiros por região}; {n:licencia-pesca-patagonia-precios|a licença patagônica}).',
		'<strong>Quanto custa:</strong> não é dito aqui; os preços datados estão em três notas, linkadas na seção de custo.',
	],
	'h_donde'         => 'Onde pescar truta na Patagônia',
	'donde_i'         => 'O nosso guia de cada base, do norte ao sul. As regras, a licença e os operadores que publicam informação estão no guia.',
	'donde_cols'      => [ 'Base', 'Águas no guia', 'Truta no guia' ],
	'donde_rows'      => [
		[ '<a href="{g:san-martin-junin}">San Martín e Junín de los Andes</a> (Neuquén)', 'Rios Chimehuín, Malleo e Quilquihue; Collón Curá e Aluminé um pouco mais longe', 'Truta em rios e lagos; regras por ambiente' ],
		[ '<a href="{g:bariloche}">Bariloche</a> (Río Negro)', 'Lago Nahuel Huapi; rios Manso, Traful e Limay superior', 'Truta em rios e lagos; regras por ambiente' ],
		[ '<a href="{g:esquel-trevelin}">Esquel e Trevelin</a> (Chubut)', 'Futaleufú, Carrileufú, lagos Rosario e Fontana, rios do entorno do Parque Nacional Los Alerces', 'Truta marrom, arco-íris e de riacho, na descrição dos operadores' ],
		[ '<a href="{g:jurassic-lake}">Jurassic Lake (lago Strobel)</a> (Santa Cruz)', 'Lago Strobel e rio Barrancoso', 'Truta arco-íris' ],
		[ '<a href="{g:rio-santa-cruz}">Rio Santa Cruz em Piedra Buena</a> (Santa Cruz)', 'Rio Santa Cruz', 'Steelhead: truta arco-íris anádroma' ],
		[ '<a href="{g:rio-gallegos}">Río Gallegos</a> (Santa Cruz)', 'Río Gallegos', 'Truta marrom anádroma' ],
		[ '<a href="{g:rio-grande}">Río Grande</a> (Terra do Fogo)', 'Río Grande e seu afluente o Menéndez', 'Truta marrom anádroma' ],
	],
	'h_marron'        => 'Truta marrom (Salmo trutta)',
	'm1'              => 'A truta marrom não é nativa. <a href="{u:macchi}" rel="noopener">Macchi e Vigliano (<em>Ecología Austral</em>, 2014)</a> analisaram as semeaduras de 54 bacias entre 1904 e 2011: a marrom foi semeada em 67,3 % delas, a arco-íris em 96,4 % e a fontinalis em 72,7 % (lido em 10/11/2026).',
	'm2'              => '<strong>As marrons residentes</strong> são trutas marrons que nunca vão ao mar. Qual você encontra depende do ambiente e da sua história de semeaduras, então nossos guias listam as águas e as regras de cada uma e não as ordenam. Um lodge da região de Esquel diz que março e o início de abril podem ser excepcionais para as marrons: é a descrição de um operador, no nosso <a href="{g:esquel-trevelin}">guia de Esquel</a> (lido em 4/11/2026).',
	'm3'              => '<strong>As trutas marrons anádromas</strong> vivem parte da vida no Atlântico e sobem a um rio para desovar. Nossos guias cobrem dois rios onde elas são pescadas: o <a href="{g:rio-grande}">Río Grande</a>, na Terra do Fogo, e o <a href="{g:rio-gallegos}">Río Gallegos</a>, em Santa Cruz. Segundo Todd Moen, em um <a href="{u:moen}" rel="noopener">vídeo da Catch Magazine</a>, as marrons introduzidas do Río Grande desenvolveram o hábito de migrar para o Atlântico para se alimentar porque o rio tem pouco alimento. A Farbank escreve que os peixes começam a entrar no Río Grande em novembro e seguem entrando até abril, e que os operadores concentram o melhor entre a primeira semana de janeiro e a primeira de abril (<a href="{u:fb_rg}" rel="noopener">Río Grande seasonal breakdown</a>, lido em 26/10/2026).',
	'm4'              => 'Uma tese da Universidad Nacional del Comahue (<a href="{u:giese}" rel="noopener">Giese</a>) compara as populações adultas dos rios Gallegos, Grande e Irigoyen. Segundo seu resumo, os adultos foram mais longos no Gallegos que nos rios fueguinos e não se observaram diferenças de peso entre os três rios (resumo lido em 13/11/2026). Os tamanhos e os recordes, cada um com sua fonte, estão na {esp:trucha|ficha da truta}.',
	'h_arco'          => 'Truta arco-íris (Oncorhynchus mykiss)',
	'a1'              => 'A arco-íris é a truta mais semeada da Patagônia: 96,4 % das bacias que Macchi e Vigliano (2014) analisaram.',
	'a2'              => '<strong>As arco-íris residentes.</strong> O lago Strobel, que os pescadores de fora conhecem como Jurassic Lake, fica no centro de Santa Cruz e é conhecido por suas trutas arco-íris; seu afluente principal é o rio Barrancoso. «Jurassic Lake» é uma marca registrada de um lodge, não o nome do lago. As regras, a licença e como chegar estão no nosso <a href="{g:jurassic-lake}">guia de Jurassic Lake</a>.',
	'h_steel'         => 'O steelhead do rio Santa Cruz',
	's1'              => 'O <a href="{g:rio-santa-cruz}">rio Santa Cruz</a>, na província de Santa Cruz, é o rio de uma truta arco-íris que desce ao mar e volta a subir: o steelhead. A Disposición 241/2026 da Secretaría de Estado de Pesca y Acuicultura a chama, em seus considerandos, de «la única población silvestre de linaje atlántico en el continente americano» («a única população silvestre de linhagem atlântica no continente americano»; citação textual, tradução nossa; <a href="{u:bo}" rel="noopener">Boletín Oficial de Santa Cruz</a>, lido em 13/11/2026).',
	's2'              => '<a href="{u:rr3}" rel="noopener">Riva Rossi, Arguimbau e Pascual (2003, <em>Ecología Austral</em>)</a> resumem assim: o Santa Cruz é «el único ambiente en el mundo donde la trucha arco iris introducida desarrolló poblaciones migratorias facultativas» («o único ambiente no mundo onde a truta arco-íris introduzida desenvolveu populações migratórias facultativas»; tradução nossa), ou seja, uma parte dos peixes completa seu ciclo em água doce e outra viaja ao mar. Pascual et al. (2001) documentaram o primeiro caso de anadromia nesta truta introduzida, e segundo <a href="{u:lazari}" rel="noopener">Lázari et al. (2024, <em>Journal of Fish Biology</em>)</a> a arco-íris foi semeada na bacia entre 1906 e 1910, presumivelmente da Califórnia ou do Oregon (lido em 14/11/2026).',
	's3'              => '<strong>Quando:</strong> não há uma data oficial de «temporada» para o steelhead: o rio está aberto o ano todo. Riva Rossi et al. (2003) descrevem uma pescaria de fevereiro a maio, e <a href="{u:llompart}" rel="noopener">Llompart et al. (2024)</a> contaram picos de esforço de pesca perto de Piedra Buena de março a maio e de setembro a outubro. O Anexo I do regulamento 2026/27 estabelece a captura e soltura obrigatória do steelhead de 15/03/2027 a 30/09/2027 e uma cota de um exemplar por pescador e por temporada (<a href="{u:pdf}" rel="noopener">Disposición 241/2026</a>, lida em 13/11/2026). Não apresentamos isso como permissão para ficar com um peixe: o texto completo, a licença e o que os operadores publicam estão no <a href="{g:rio-santa-cruz}">guia do rio Santa Cruz</a>.',
	'h_mar'           => 'Truta do mar: o que a palavra abrange',
	'mar1'            => '«Anádroma» é usada na Patagônia para dois peixes diferentes. Os dois são trutas introduzidas cujo hábito de ir ao mar se desenvolveu em rios patagônicos; não compartilham espécie, rio nem calendário.',
	'mar_cols'        => [ 'Forma', 'Peixe', 'Rios', 'Quando, segundo as fontes' ],
	'mar_rows'        => [
		[ 'Truta marrom anádroma', 'Salmo trutta', '<a href="{g:rio-grande}">Río Grande</a>, <a href="{g:rio-gallegos}">Río Gallegos</a>', 'Río Grande: os peixes começam a entrar em novembro e seguem entrando até abril, com o melhor entre o início de janeiro e o início de abril (Farbank). Río Gallegos: a temporada forte dos lodges vai do fim de dezembro a abril (nosso guia)' ],
		[ 'Steelhead', 'Oncorhynchus mykiss (arco-íris)', '<a href="{g:rio-santa-cruz}">Rio Santa Cruz</a>', 'Outono, do fim de fevereiro a maio (estudos e operadores, veja o guia); o rio está aberto o ano todo' ],
	],
	'mar2'            => 'Não damos um tamanho típico: os números diferem entre fontes e entre rios. A {esp:trucha|ficha da espécie} tem os que podemos nomear, cada um atribuído. Um dado sobre recordes: a IGFA tem uma única categoria para a truta marrom, com «sea trout» entre seus nomes alternativos e sem uma categoria à parte para a forma marinha (<a href="{u:igfa}" rel="noopener">IGFA</a>, lida em 13/11/2026).',
	'h_cuando'        => 'Quando pescar truta na Patagônia',
	'c1'              => 'A temporada abre no início de novembro e fecha entre abril e maio, com datas diferentes para cada ambiente. As janelas abaixo são o que dizem operadores e guias, não datas oficiais; a versão longa, mês a mês e por tipo de rio, está em {n:cuando-pescar-trucha-patagonia|Quando pescar truta na Patagônia}.',
	'c_cols'          => [ 'Zona', 'Janela, segundo as fontes' ],
	'c_rows'          => [
		[ '<strong>Norte</strong> (Neuquén e Río Negro)', 'Os operadores descrevem a temporada como novembro a abril, com janeiro a março como o mais procurado (<a href="{u:yd}" rel="noopener">Yellow Dog Fly Fishing</a>)' ],
		[ '<strong>Centro</strong> (Esquel)', 'Um lodge da região indica de 1.º de novembro a 30 de abril, com água alta de degelo no começo, e março-abril como o melhor para a seca (<a href="{u:enc}" rel="noopener">El Encuentro Lodge</a>)' ],
		[ '<strong>Sul</strong> (Jurassic Lake)', 'A temporada do lodge vai de setembro a maio: o começo, até meados de janeiro, é o pico, e de meados de março a maio o outono volta a ser excepcional (<a href="{u:fb_jl}" rel="noopener">Farbank</a>)' ],
		[ '<strong>Terra do Fogo</strong> (Río Grande)', 'Os peixes começam a entrar em novembro e seguem entrando até abril; os operadores concentram o melhor entre a primeira semana de janeiro e a primeira de abril (<a href="{u:fb_rg}" rel="noopener">Farbank</a>)' ],
	],
	'c2'              => 'O steelhead do rio Santa Cruz é uma pescaria de outono e tem sua própria seção mais acima.',
	'h_permiso'       => 'Qual licença é preciso',
	'pe1'             => 'Para pescar truta na Patagônia é preciso a licença de temporada da província onde fica o ambiente. Cada província fixa suas datas e seus valores, e se a licença patagônica vale no ambiente que você quer depende da província e do ano. As notas abaixo explicam, com datas e fontes:',
	'pe_l'            => [
		'{n:licencias-extranjeros-argentina|Licença de pesca para estrangeiros na Argentina 2026/27: custos por região}',
		'{n:licencia-pesca-patagonia-precios|Licença de pesca da Patagônia argentina 2026/27: custo e validade}',
		'{n:reglas-equipo-patagonia|Equipamento e regras de pesca na Patagônia: sola de feltro, anzóis e desinfecção (2026/27)}',
	],
	'caja_t'          => 'Em dia · 15/11/2026',
	'caja'            => [
		'Segundo a imprensa de 15 e 16 de outubro de 2026, a temporada 2026-2027 de Neuquén abre em 1.º de novembro de 2026 e as licenças são vendidas online desde 15 de outubro (<a href="{u:anb}" rel="noopener">ANBariloche</a>, <a href="{u:noticias}" rel="noopener">NoticiasNQN</a>; <a href="{u:nqn}" rel="noopener">Caza y Pesca de Neuquén</a>).',
		'O regulamento 2026/27 de Santa Cruz é a Disposición 241/2026, em vigor desde 1/10/2026 (<a href="{u:bo}" rel="noopener">Boletín Oficial de Santa Cruz</a>, lido em 13/11/2026).',
		'As datas, as licenças e as tarifas mudam a cada temporada: seu guia e a autoridade da província as têm em dia.',
	],
	'caja_pie'        => 'Esta caixa é atualizada uma vez por ano; o resto da nota não muda.',
	'h_costo'         => 'Quanto custa uma viagem de pesca de truta',
	'co1'             => 'Não repetimos preços aqui: cada um vale para um operador, um número de noites e uma temporada. Os preços datados, com sua fonte, estão em três notas:',
	'co_l'            => [
		'{n:precio-semana-trucha-patagonia-norte|Quanto custa uma semana de pesca de truta em Neuquén e Chubut: lodges com tudo incluído (2026/27)}',
		'{n:precio-pesca-bariloche-limay|Quanto custa um dia de pesca com mosca em Bariloche e no rio Limay (2026/27)}',
		'{n:precio-pesca-patagonia-sur|Quanto custa pescar na Terra do Fogo e em El Calafate: truta do mar e saídas de um dia (2026/27)}',
	],
	'cta'             => '<strong>Quer que a gente pergunte aos operadores que combinam?</strong> Conte suas datas e quantos vão no <a href="{viaje}">formulário</a>; perguntamos aos operadores que combinam e, em 48 horas, respondemos com as opções comparadas, sem custo para você.',
	'faq_h'           => 'Perguntas frequentes',
	'faq'             => [
		[ 'Qual é a melhor época para pescar truta na Patagônia?', 'A temporada abre no início de novembro e fecha entre abril e maio, com datas diferentes para cada ambiente (regulamento patagônico, como em nossos guias). A melhor janela depende da zona: os operadores descrevem janeiro a março como o mais procurado no norte (Yellow Dog Fly Fishing), o começo da temporada e o outono no lago Strobel (Farbank), o outono para o steelhead do rio Santa Cruz e de janeiro ao início de abril para as marrons anádromas na Terra do Fogo (Farbank). São descrições de operadores lidas em 26/10/2026, não datas oficiais; o detalhe está em {n:cuando-pescar-trucha-patagonia|Quando pescar truta na Patagônia}.' ],
		[ 'Preciso de licença para pescar truta na Patagônia?', 'Sim. É preciso a licença de temporada da província onde fica o ambiente; cada província fixa suas datas e seus valores, e nos Parques Nacionais valem também as regras do parque (nossos guias, lidos entre 30/10 e 14/11/2026). Se a licença patagônica vale em um ambiente depende da província e do ano: veja {n:licencia-pesca-patagonia-precios|a nota da licença patagônica} e {n:licencias-extranjeros-argentina|a nota de licenças por região}. Para Neuquén, a imprensa de 15 e 16 de outubro de 2026 diz que as licenças são vendidas online desde 15 de outubro.' ],
		[ 'Qual é melhor na Patagônia, a truta marrom ou a arco-íris?', 'Nenhuma é melhor em geral: são pescarias diferentes e qual você encontra depende do ambiente. As duas foram introduzidas (a primeira semeadura oficial de salmonídeos foi em 1904) e as duas foram muito semeadas: a arco-íris em 96,4 % e a marrom em 67,3 % das 54 bacias que Macchi e Vigliano analisaram (<em>Ecología Austral</em>, 2014; lido em 10/11/2026). Não ordenamos espécies nem lugares: pergunte ao seu guia sobre o ambiente e o mês.' ],
		[ 'O que é uma truta marrom anádroma e onde se pesca?', 'É uma truta marrom que vive parte da vida no Atlântico e sobe a um rio para desovar. Nossos guias cobrem dois rios: o Río Grande (Terra do Fogo) e o Río Gallegos (Santa Cruz). Segundo Todd Moen (vídeo da Catch Magazine), as marrons introduzidas do Río Grande começaram a migrar para o oceano para se alimentar porque o rio tem pouco alimento. A IGFA lista o recorde da truta marrom sob um único nome, «Trout, brown (Salmo trutta)», com «sea trout» entre seus nomes alternativos e sem uma categoria à parte para a forma marinha (lido em 13/11/2026).' ],
		[ 'Há steelhead na Argentina?', 'Sim, em um rio: o rio Santa Cruz, perto de Comandante Luis Piedra Buena. O steelhead é uma truta arco-íris que vai ao mar e volta. A Disposición 241/2026 a chama, em seus considerandos, de «la única población silvestre de linaje atlántico en el continente americano» (citação textual), e Riva Rossi, Arguimbau e Pascual (2003) dizem que o rio é «o único ambiente no mundo onde a truta arco-íris introduzida desenvolveu populações migratórias facultativas» (tradução nossa). Os estudos e os operadores situam a subida no outono, do fim de fevereiro a maio; o rio está aberto o ano todo. Veja o {g:rio-santa-cruz|guia do rio Santa Cruz}.' ],
		[ 'Posso ficar com a truta que pesco na Patagônia?', 'Depende do ambiente e da temporada. Muitos ambientes patagônicos são só para mosca, com anzol sem farpa e soltura obrigatória, e outros permitem um limite (regulamento patagônico, como em nossos guias). Para o steelhead do rio Santa Cruz, o Anexo I da Disposición 241/2026 fixa uma cota de um exemplar por pescador e por temporada e a soltura obrigatória de 15/03/2027 a 30/09/2027 (lido em 13/11/2026); não apresentamos isso como permissão para ficar com um peixe. Confirme com seu guia ou com a autoridade da província antes de pescar, e veja {n:reglas-equipo-patagonia|a nota de equipamento e regras}.' ],
		[ 'Onde se pode pescar truta na Patagônia?', 'Nossos guias cobrem San Martín e Junín de los Andes (Neuquén), Bariloche (Río Negro), Esquel e Trevelin (Chubut), o lago Strobel ou «Jurassic Lake», o rio Santa Cruz e o Río Gallegos (Santa Cruz) e o Río Grande (Terra do Fogo). A tabela acima leva a cada um, com suas águas, suas regras e sua licença.' ],
		[ 'Quanto custa uma viagem de pesca de truta à Patagônia?', 'Não damos um preço aqui, porque cada um vale para um operador, um número de noites e uma temporada. Os preços datados, com sua fonte, estão em três notas: uma semana de pesca de truta em Neuquén e Chubut, um dia de pesca com mosca em Bariloche e no Limay, e Terra do Fogo e El Calafate (todas 2026/27). Os links estão na seção de custo acima.' ],
	],
	'cierre'          => '<em>Não falamos com os operadores nem com as pessoas citadas, nem pescamos com eles. Esta nota resume informação pública já publicada neste site, cada dado com sua fonte e a data em que foi lido; a interpretação é nossa e pode ter erros.</em>',
];

// Sources (name per language; date = when the fact was read, as published before).
$fuente_nombre = [
	'macchi'   => [ 'es' => 'Macchi, P. y Vigliano, P. (2014): Introducción de salmónidos en la Patagonia: los fantasmas del manejo pasado, presente y futuro. Ecología Austral 24(2)', 'en' => 'Macchi, P. and Vigliano, P. (2014): Salmonid introduction in Patagonia: the ghost of past, present and future management. Ecología Austral 24(2)', 'pt' => 'Macchi, P. e Vigliano, P. (2014): Introdução de salmonídeos na Patagônia: os fantasmas do manejo passado, presente e futuro. Ecología Austral 24(2)', 'f' => '20261110', 'o' => 0 ],
	'bo'       => [ 'es' => 'Boletín Oficial de Santa Cruz: Disposición 241/2026 (reglamento general 2026/27 y Anexo I)', 'en' => 'Boletín Oficial de Santa Cruz: Disposición 241/2026 (2026/27 general regulation and Annex I)', 'pt' => 'Boletín Oficial de Santa Cruz: Disposición 241/2026 (regulamento geral 2026/27 e Anexo I)', 'f' => '20261113', 'o' => 1 ],
	'pdf'      => [ 'es' => 'Disposición 241/2026, texto del reglamento con el Anexo I (PDF del Boletín Oficial de Santa Cruz)', 'en' => 'Disposición 241/2026, text of the regulation with Annex I (PDF of the Boletín Oficial de Santa Cruz)', 'pt' => 'Disposición 241/2026, texto do regulamento com o Anexo I (PDF do Boletín Oficial de Santa Cruz)', 'f' => '20261113', 'o' => 1 ],
	'lazari'   => [ 'es' => 'Lázari et al. (2024): Journal of Fish Biology, DOI 10.1111/jfb.15722 (origen de la trucha arco iris del río Santa Cruz)', 'en' => 'Lázari et al. (2024): Journal of Fish Biology, DOI 10.1111/jfb.15722 (origin of the Santa Cruz River rainbow trout)', 'pt' => 'Lázari et al. (2024): Journal of Fish Biology, DOI 10.1111/jfb.15722 (origem da truta arco-íris do rio Santa Cruz)', 'f' => '20261114', 'o' => 0 ],
	'rr3'      => [ 'es' => 'Riva Rossi, Arguimbau y Pascual (2003): La migración reproductiva de la trucha arco iris anádroma del río Santa Cruz mediante radio-marcado, Ecología Austral 13', 'en' => 'Riva Rossi, Arguimbau and Pascual (2003): La migración reproductiva de la trucha arco iris anádroma del río Santa Cruz mediante radio-marcado, Ecología Austral 13', 'pt' => 'Riva Rossi, Arguimbau e Pascual (2003): La migración reproductiva de la trucha arco iris anádroma del río Santa Cruz mediante radio-marcado, Ecología Austral 13', 'f' => '20261114', 'o' => 0 ],
	'llompart' => [ 'es' => 'Llompart et al. (2024): La pesca recreativa en la cuenca baja del río Santa Cruz ante escenarios de represamiento, Ecología Austral 34(2)', 'en' => 'Llompart et al. (2024): La pesca recreativa en la cuenca baja del río Santa Cruz ante escenarios de represamiento, Ecología Austral 34(2)', 'pt' => 'Llompart et al. (2024): La pesca recreativa en la cuenca baja del río Santa Cruz ante escenarios de represamiento, Ecología Austral 34(2)', 'f' => '20261114', 'o' => 0 ],
	'giese'    => [ 'es' => 'Giese, A. C.: Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral, tesis, Universidad Nacional del Comahue (resumen)', 'en' => 'Giese, A. C.: Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral, thesis, Universidad Nacional del Comahue (abstract)', 'pt' => 'Giese, A. C.: Estructuración geográfica en poblaciones de trucha marrón (Salmo trutta L.) migratoria en ríos de la Patagonia Austral, tese, Universidad Nacional del Comahue (resumo)', 'f' => '20261113', 'o' => 0 ],
	'igfa'     => [ 'es' => 'IGFA: récord mundial de la trucha marrón (una sola categoría, «sea trout» entre los nombres alternativos)', 'en' => 'IGFA: world record for brown trout (a single category, “sea trout” among the alternative names)', 'pt' => 'IGFA: recorde mundial da truta marrom (uma única categoria, «sea trout» entre os nomes alternativos)', 'f' => '20261113', 'o' => 0 ],
	'moen'     => [ 'es' => 'Catch Magazine (Todd Moen): Fly fishing for huge sea-run brown trout (video)', 'en' => 'Catch Magazine (Todd Moen): Fly fishing for huge sea-run brown trout (video)', 'pt' => 'Catch Magazine (Todd Moen): Fly fishing for huge sea-run brown trout (vídeo)', 'f' => '20261026', 'o' => 0 ],
	'fb_rg'    => [ 'es' => 'Farbank: Río Grande seasonal breakdown', 'en' => 'Farbank: Río Grande seasonal breakdown', 'pt' => 'Farbank: Río Grande seasonal breakdown', 'f' => '20261026', 'o' => 0 ],
	'fb_jl'    => [ 'es' => 'Farbank: when is the best time to fish at Jurassic Lake Lodge', 'en' => 'Farbank: when is the best time to fish at Jurassic Lake Lodge', 'pt' => 'Farbank: when is the best time to fish at Jurassic Lake Lodge', 'f' => '20261026', 'o' => 0 ],
	'yd'       => [ 'es' => 'Yellow Dog Fly Fishing: frequently asked questions, fly fishing Patagonia', 'en' => 'Yellow Dog Fly Fishing: frequently asked questions, fly fishing Patagonia', 'pt' => 'Yellow Dog Fly Fishing: frequently asked questions, fly fishing Patagonia', 'f' => '20261026', 'o' => 0 ],
	'enc'      => [ 'es' => 'El Encuentro Lodge (Esquel): temporadas', 'en' => 'El Encuentro Lodge (Esquel): seasons', 'pt' => 'El Encuentro Lodge (Esquel): temporadas', 'f' => '20261026', 'o' => 0 ],
	'regl'     => [ 'es' => 'Reglamento de Pesca Deportiva Continental Patagónico (portal interprovincial)', 'en' => 'Regulation of Continental Sport Fishing of Patagonia (interprovincial portal)', 'pt' => 'Regulamento de Pesca Esportiva Continental Patagônico (portal interprovincial)', 'f' => '20261026', 'o' => 1 ],
	'nqn'      => [ 'es' => 'Caza y Pesca de Neuquén (Dirección Provincial de Fauna): permisos de pesca', 'en' => 'Caza y Pesca de Neuquén (Provincial Wildlife Directorate): fishing permits', 'pt' => 'Caza y Pesca de Neuquén (Direção Provincial de Fauna): licenças de pesca', 'f' => '20261026', 'o' => 1 ],
	'anb'      => [ 'es' => 'ANBariloche (16/10/2026): arranca la venta de permisos de pesca para la temporada 2026-2027', 'en' => 'ANBariloche (16/10/2026): sale of fishing permits for the 2026-2027 season begins', 'pt' => 'ANBariloche (16/10/2026): começa a venda de licenças de pesca para a temporada 2026-2027', 'f' => '20261026', 'o' => 0 ],
	'noticias' => [ 'es' => 'NoticiasNQN (15/10/2026): Neuquén habilitó la compra de permisos para la nueva temporada de pesca', 'en' => 'NoticiasNQN (15/10/2026): Neuquén opened the purchase of permits for the new fishing season', 'pt' => 'NoticiasNQN (15/10/2026): Neuquén habilitou a compra de licenças para a nova temporada de pesca', 'f' => '20261026', 'o' => 0 ],
];

/** Resolve {g:clave}, {n:clave|texto}, {g:clave|texto}, {esp:trucha|texto}, {u:clave} and {viaje}; fails on anything left over. */
$resolver = function ( string $html, string $i ) use ( $u ): string {
	$nota = function ( string $c ) use ( $i ): string {
		$por = [];
		foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $c, 'numberposts' => -1, 'fields' => 'ids' ] ) as $id ) {
			$por[ pm_idioma_de( (int) $id ) ] = (int) $id;
		}
		$id = $por[ $i ] ?? ( $por['es'] ?? 0 );
		if ( ! $id ) {
			WP_CLI::error( "No encuentro la nota {$c} (ni en español)." );
		}
		isset( $por[ $i ] ) || WP_CLI::warning( "nota {$c} no existe en {$i}: el enlace va a la versión en español" );
		return get_permalink( $id );
	};
	$guia = function ( string $c ) use ( $i ): string {
		$g = pm_tp_guia( $c, $i );
		$g || WP_CLI::error( "No encuentro la guía {$c}." );
		pm_idioma_de( $g->ID ) === $i || WP_CLI::warning( "guía {$c} no existe en {$i}: el enlace va a la versión en español" );
		return get_permalink( $g->ID );
	};
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( $nota( $m[1] ) ) . '">' . $m[2] . '</a>', $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( $guia( $m[1] ) ) . '">' . $m[2] . '</a>', $html );
	$html = preg_replace_callback( '/\{esp:trucha\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( pm_url_especie( 'trucha', $i ) ) . '">' . $m[1] . '</a>', $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\}/', fn( $m ) => esc_url( $guia( $m[1] ) ), $html );
	$html = preg_replace_callback( '/\{u:([a-z0-9_]+)\}/', function ( $m ) use ( $u ) {
		isset( $u[ $m[1] ] ) || WP_CLI::error( "enlace externo desconocido {$m[1]}" );
		return esc_url( $u[ $m[1] ] );
	}, $html );
	$html = str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) . '?especie=trucha' ), $html );
	if ( preg_match( '/\{(n:|g:|esp:|u:|viaje)[^}]*\}?/', $html, $m ) ) {
		WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
	}
	return $html;
};

// Cover photo: the attachment already loaded for another note (credit and per-language alt live on the attachment).
$nota_foto = pm_buscar( 'pm_nota', $foto_de, 'es' );
$att       = $nota_foto ? (int) get_post_thumbnail_id( $nota_foto->ID ) : 0;
$att || WP_CLI::warning( "la nota {$foto_de} (es) no tiene foto de portada: la nota nueva tampoco la tendrá." );

$existe = fn( string $i ) => array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i ) );
$cambios = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n = $N[ $i ];
	if ( $ya = $existe( $i ) ) {
		WP_CLI::log( "$clave ($i) #{$ya[0]->ID} ya existe: no toco nada." );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	count( $n['faq'] ) >= 6 && count( $n['faq'] ) <= 8 || WP_CLI::error( "$i: la FAQ debe tener entre 6 y 8 preguntas." );
	$cuerpo = pm_sc_p( $n['p0'] ) . pm_sc_lista( $n['resp'] )
		. pm_sc_h2( $n['h_donde'] ) . pm_sc_p( $n['donde_i'] ) . pm_tp_tabla( $n['donde_cols'], $n['donde_rows'] )
		. pm_sc_h2( $n['h_marron'] ) . pm_sc_p( $n['m1'] ) . pm_sc_p( $n['m2'] ) . pm_sc_p( $n['m3'] ) . pm_sc_p( $n['m4'] )
		. pm_sc_h2( $n['h_arco'] ) . pm_sc_p( $n['a1'] ) . pm_sc_p( $n['a2'] )
		. pm_sc_h2( $n['h_steel'], 3 ) . pm_sc_p( $n['s1'] ) . pm_sc_p( $n['s2'] ) . pm_sc_p( $n['s3'] )
		. pm_sc_h2( $n['h_mar'] ) . pm_sc_p( $n['mar1'] ) . pm_tp_tabla( $n['mar_cols'], $n['mar_rows'] ) . pm_sc_p( $n['mar2'] )
		. pm_sc_h2( $n['h_cuando'] ) . pm_sc_p( $n['c1'] ) . pm_tp_tabla( $n['c_cols'], $n['c_rows'] ) . pm_sc_p( $n['c2'] )
		. pm_sc_h2( $n['h_permiso'] ) . pm_sc_p( $n['pe1'] ) . pm_sc_lista( $n['pe_l'] )
		. pm_tp_caja( $n['caja_t'], $n['caja'], $n['caja_pie'] )
		. pm_sc_h2( $n['h_costo'] ) . pm_sc_p( $n['co1'] ) . pm_sc_lista( $n['co_l'] ) . pm_sc_p( $n['cta'] )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] )
		. pm_sc_p( $n['cierre'] );
	$cuerpo = $resolver( $cuerpo, $i );
	$datos  = array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] );
	$fuentes = [];
	foreach ( $fuente_nombre as $k => $f ) {
		$fuentes[] = [ 'nombre' => $f[ $i ], 'url' => $u[ $k ], 'oficial' => $f['o'], 'consultada' => $f['f'] ];
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . ' fuentes, ' . count( $n['faq'] ) . " FAQ; title $tl / description $dl; portada #$att" );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => $datos, 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_especie' => 'trucha', 'nota_origen' => 'Pilar de la trucha en la Patagonia, 15/11/2026 (consultas en inglés de Search Console). Armada solo con hechos ya publicados en el sitio, cada uno con su fuente y su fecha de lectura (nota de temporada del 26/10, ficha de trucha del 10 y 13/11, guía del río Santa Cruz del 14/11 y guías de cada base); no se leyó ninguna fuente nueva. Sin tamaños ni récords (están en la ficha, atribuidos), sin precios (tres notas fechadas enlazadas), sin nombres de operadores. Foto provisoria: la de la nota precio-semana-trucha-patagonia-norte (Commons, Boca del Chimehuín). Docs: docs/estado.md.' ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		$att && set_post_thumbnail( $id, $att );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_post( $id )->post_name === $n['slug'] && pm_clave_de( $id ) === $clave && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $datos ) && false !== strpos( $cont, 'pm-faq' ) && count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && ! preg_match( '/\{(n|g|u|esp):|\{viaje\}/', $cont );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
if ( $aplicar ) {
	// pm_buscar() / pm_alternas_nota() read a per-process cache filled before the notes were created: look the new notes up directly.
	$nota_es = $existe( 'es' )[0] ?? null;
	$e       = $nota_es ? pm_nota( $nota_es->ID ) : null;
	$ok      = $e && 'trucha' === $e['especie'] && $e['fecha'] && ( ! $att || (int) $e['foto'] === $att );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee especie ' . ( $e['especie'] ?? '?' ) . ', fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
	$ok || WP_CLI::error( 'la nota es no lee especie, fecha o foto como se esperaba.' );
	$alt = [];
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$x = $existe( $i )[0] ?? null;
		$x || WP_CLI::error( "falta la nota ($i) para el hreflang." );
		$alt[ $i ] = get_permalink( $x->ID );
	}
	WP_CLI::log( 'VERIFIED: tres notas con la misma clave (hreflang es/en/pt se arma por clave): ' . implode( ' | ', $alt ) );
	flush_rewrite_rules( false );
}

// --- Inbound links: one paragraph on the species page "Trucha" and one on the season note (skipped when the pillar URL is already there) ---
$enlace = [
	'es' => [
		'ficha' => '<strong>Guía completa:</strong> %s (marrón, arco iris y de mar, dónde, cuándo y con qué permiso).',
		'nota'  => 'Si querés el panorama completo (trucha marrón, arco iris y de mar, dónde se pesca y con qué permiso), leé %s.',
		'ancla_ficha' => '/cuándo pescar trucha en la Patagonia/u',
		'ancla_nota'  => '/Respuesta corta/u',
		'h_tam'       => '/Tamaños|Tipos de truchas/u',
	],
	'en' => [
		'ficha' => '<strong>Full guide:</strong> %s (brown, rainbow and sea-run, where, when and with which permit).',
		'nota'  => 'For the full picture (brown, rainbow and sea-run trout, where to fish and which permit you need), read %s.',
		'ancla_ficha' => '/when to fish trout in Patagonia/u',
		'ancla_nota'  => '/Short answer/u',
		'h_tam'       => '/How big|Types of trout/u',
	],
	'pt' => [
		'ficha' => '<strong>Guia completo:</strong> %s (marrom, arco-íris e anádroma, onde, quando e com qual licença).',
		'nota'  => 'Para o panorama completo (truta marrom, arco-íris e anádroma, onde se pesca e qual licença é preciso), leia %s.',
		'ancla_ficha' => '/quando pescar truta na Patagônia/u',
		'ancla_nota'  => '/Resposta curta/u',
		'h_tam'       => '/Tamanhos|Tipos de trutas/u',
	],
];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$url  = trailingslashit( pm_url_notas( $i ) ) . $N[ $i ]['slug'] . '/';
	$link = pm_sc_a( $url, esc_html( $N[ $i ]['titulo'] ) );
	foreach ( [ 'ficha' => [ 'pm_especie', 'trucha' ], 'nota' => [ 'pm_nota', 'cuando-pescar-trucha-patagonia' ] ] as $que => [ $tipo, $c ] ) {
		$p = pm_buscar( $tipo, $c, $i );
		if ( ! $p ) {
			WP_CLI::warning( "$que ($i): no existe ($c), no enlazo." );
			continue;
		}
		$cont = $p->post_content;
		if ( false !== strpos( $cont, $url ) ) {
			WP_CLI::log( "$que ($i) #{$p->ID}: ya enlaza el pilar." );
			continue;
		}
		$pos = pm_sc_pos_tras_parrafo( $cont, $enlace[ $i ][ 'ancla_' . $que ] );
		$via = 'tras el párrafo ancla';
		if ( null === $pos ) {
			$pos = 'ficha' === $que ? pm_sc_pos_encabezado( $cont, $enlace[ $i ]['h_tam'] ) : null;
			$via = 'antes del encabezado de tamaños';
		}
		if ( null === $pos ) {
			$pos = 'nota' === $que ? ( pm_sc_pos_tras_parrafo( $cont, '/./u' ) ?? 0 ) : 0;
			$via = 'tras el primer párrafo (o al principio)';
		}
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": $que ($i) #{$p->ID}: párrafo con el enlace al pilar, $via" );
		$cambios++;
		if ( $aplicar ) {
			$bloque = pm_sc_p( sprintf( $enlace[ $i ][ $que ], $link ) );
			pm_sc_guardar( $p->ID, pm_sc_insertar( $cont, $pos, $bloque ), [ $url ], "$que ($i)" );
			WP_CLI::log( "VERIFIED: $que ($i) #{$p->ID} enlaza el pilar" );
		}
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
