<?php
/**
 * Note "Argentina fishing license: how to get it, step by step" (es/en/pt siblings, clave permiso-pesca-argentina-paso-a-paso), 16/11/2026.
 * Why: the owner's review (16/11/2026) showed a gap in what a trip-preparation site must have: how to actually get the permit. Query targets: "argentina fishing
 * license", "patagonia fishing license" (en), "permiso de pesca patagonia", "licencia de pesca argentina extranjeros" (es), "licença de pesca argentina" (pt).
 * Built from the official pages read on 16/11/2026 with investigar.yml (runs permisos-1, permisos-2, permisos-3): Neuquén (cazaypesca.neuquen.gob.ar/permisos),
 * Río Negro (permisosdepesca.com.ar: home, how to buy, FAQ), Santa Cruz (home, values, application form, regulation art. 5), Tierra del Fuego (prodyambiente page,
 * Resolución 259/26 PDF, SIPED: home, about, non-resident form), Corrientes (tourism page, Recursos Naturales portal and registration) and Entre Ríos (procedure 708);
 * plus values already published on the site with their source and date (hub, Patagonian permit note, Corrientes-from-Brazil note, La Paz guide).
 * NOT written, on purpose: which portal is "the" Patagonian one (pescacontinental.gob.ar did not answer, not even with a browser, and the Chubut portal returned 403: neither
 * is mentioned as read; each official page says what it says), how a foreigner pays with a foreign card (no portal says it), phone numbers and e-mails, names of people.
 * Inbound links: one paragraph in the licence hub and the Patagonian permit note, and at the end of the permit section of eleven guides (skipped when already linked).
 * Cover photo: the owner's Unsplash photo already loaded for the note `reglas-equipo-patagonia` (provisional, see docs/estado.md).
 * FAQ: group block `pm-faq` (pm_sc_faq): the visible FAQ is the FAQPage JSON-LD source.
 * Idempotent: a note that exists (same clave + language) is never touched. Dry run first ($aplicar = false). Run with wp-script.yml
 * (script = permiso-paso-a-paso-2026-11-16).
 */
// --- Helpers shared by the seo-consultas-2026-11-13 scripts (copied in each file: wp-script.yml sends one file) ---
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

$aplicar = false;
$hoy     = '20261116';
$clave   = 'permiso-pesca-argentina-paso-a-paso';
$foto_de = 'reglas-equipo-patagonia'; // note whose cover photo is reused (owner's Unsplash photo, lake in Río Negro)

// External links: official pages read on 16/11/2026 (Actions run permisos-1/2/3 of investigar.yml) and sources already published on the site.
$u = [
	'nqn'      => 'https://cazaypesca.neuquen.gob.ar/permisos',
	'rn'       => 'https://www.permisosdepesca.com.ar/',
	'rn_como'  => 'https://www.permisosdepesca.com.ar/permisos-de-pesca--news--3-1002',
	'rn_faq'   => 'https://www.permisosdepesca.com.ar/permisos-de-pesca--news--3-1001',
	'regl'     => 'https://reglamentodepesca.org.ar/',
	'sc'       => 'https://pesca.santacruz.gob.ar/',
	'sc_alta'  => 'https://pesca.santacruz.gob.ar/permisos/alta_permiso/',
	'sc_valor' => 'https://pesca.santacruz.gob.ar/valores/',
	'tdf'      => 'https://prodyambiente.tierradelfuego.gob.ar/pesca-deportiva-tdf/',
	'tdf_pdf'  => 'https://prodyambiente.tierradelfuego.gob.ar/wp-content/uploads/2026/10/Resolucion-N259-Temporada-pesca-2026.pdf',
	'siped'    => 'https://siped.ar/pesca-deportiva',
	'siped_a'  => 'https://siped.ar/acerca',
	'siped_f'  => 'https://siped.ar/no-residente-pais/form',
	'saptdf'   => 'http://saptdf.ar/',
	'ctes_tur' => 'https://corrientes.tur.ar/tramites-y-permisos/',
	'ctes'     => 'https://www.recnaturalesctes.gob.ar/',
	'ctes_reg' => 'https://www.recnaturalesctes.gob.ar/signup',
	'er'       => 'https://portal.entrerios.gov.ar/pf/tramite/708',
];

$N = [];

// =====================================================================================================================================
// ENGLISH
// =====================================================================================================================================
$N['en'] = [
	'titulo'          => 'Argentina fishing license: how to get it, step by step',
	'slug'            => 'argentina-fishing-license-step-by-step',
	'seo_title'       => 'Argentina fishing license: how to get it, step by step',
	'seo_description' => 'Argentina fishing license for foreigners: which permit you need by region, where to get it online, what documents they ask for and how to pay. Read 16/11/2026.',
	'bajada'          => 'Which permit you need depending on where you fish in Argentina, where each one is issued, what the official portals ask for and how they are paid: read on 16 November 2026.',
	'datos'           => [
		'There is no national fishing license in Argentina: you need the permit of the region where you fish, and each region has its own portal.',
		'Patagonia (Neuquén, Río Negro, Chubut and the National Parks) shares one permit, sold online on the Neuquén portal and on Río Negro’s; Santa Cruz, Tierra del Fuego and Corrientes each issue their own.',
		'Santa Cruz: online request, with passport accepted as ID; the portal says payment is by bank transfer or MercadoPago in pesos and that it does not accept transfers in foreign currency.',
		'Tierra del Fuego: online registration on siped.ar, then the permit is issued at a point of sale with your ID, paid on the spot.',
		'No official source fixes a lead time; according to one lodge, the permit is arranged a few days before arrival. Read on 16/11/2026.',
	],
	'p0'              => '<strong>Short answer.</strong> To fish in Argentina as a foreign tourist you need the fishing permit of the region where the water is. This page walks through each one with the official pages we read on 16 November 2026: who issues it, where it is processed, what the portal asks for and how it is paid. Prices are not repeated here except in one table, each with its source and date.',
	'resp'            => [
		'<strong>Which permit:</strong> the one of the region. Patagonia (Neuquén, Río Negro, Chubut, National Parks) shares one; Santa Cruz, Tierra del Fuego and Corrientes have their own (table below).',
		'<strong>Where:</strong> online in Patagonia, Santa Cruz and Corrientes; in Tierra del Fuego you register on siped.ar and the permit is issued at a point of sale.',
		'<strong>Can a foreigner pay online?</strong> Santa Cruz says it takes bank transfers in pesos and MercadoPago, not foreign-currency transfers. The other portals we read do not say which payment methods they take.',
		'<strong>Documents:</strong> an ID document; Santa Cruz and Tierra del Fuego list the passport as an option on their forms.',
		'<strong>How early:</strong> no official source gives a lead time; according to one lodge, the permit is arranged a few days before arrival.',
	],
	'h_tabla'         => 'Which permit do I need depending on where I go?',
	'tabla_i'         => 'Official pages read on 16/11/2026. The permit is personal and non-transferable (stated by the Río Negro and Santa Cruz portals).',
	'tabla_cols'      => [ 'Where you fish', 'Who issues it', 'Where it is processed', 'Options for non-resident foreigners' ],
	'tabla_rows'      => [
		[ '<strong>Patagonia</strong>: Neuquén, Río Negro, Chubut and National Parks ({g:san-martin-junin|San Martín and Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel and Trevelin})', 'Neuquén’s Provincial Wildlife Directorate; Río Negro’s permits are sold by the Mesa Directiva de Pesca Deportiva Zona Andina', '<a href="{u:nqn}" rel="noopener">cazaypesca.neuquen.gob.ar</a> and <a href="{u:rn}" rel="noopener">permisosdepesca.com.ar</a>', '1 day, 10 days or full season (plus a 10-day permit for neighbouring countries)' ],
		[ '<strong>Santa Cruz</strong> ({g:rio-santa-cruz|Santa Cruz River}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos}, {g:el-calafate|El Calafate})', 'Secretaría de Estado de Pesca y Acuicultura', '<a href="{u:sc}" rel="noopener">pesca.santacruz.gob.ar</a>', '1 day, weekly or full season' ],
		[ '<strong>Tierra del Fuego</strong> ({g:rio-grande|Río Grande})', 'Secretaría de Ambiente (Ministerio de Producción y Ambiente), Dirección General de Recursos Hídricos', '<a href="{u:siped}" rel="noopener">siped.ar</a> and points of sale; information on <a href="{u:tdf}" rel="noopener">prodyambiente.tierradelfuego.gob.ar</a>', '1 day, 3 days, 7 days or full season' ],
		[ '<strong>Corrientes</strong> ({g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria})', 'Dirección de Recursos Naturales (Ministerio de Turismo)', '<a href="{u:ctes}" rel="noopener">recnaturalesctes.gob.ar</a>; <a href="{u:ctes_tur}" rel="noopener">Tourism Ministry page</a>', 'Tourist license of 1 to 6 days, set in liters of gasoline' ],
		[ '<strong>Entre Ríos</strong> ({g:la-paz|La Paz})', 'Secretaría de Agricultura, Ganadería y Pesca', '<a href="{u:er}" rel="noopener">portal.entrerios.gov.ar</a>', '5-day permit for foreigners' ],
	],
	'h_pat'           => 'Patagonia: Neuquén, Río Negro, Chubut and National Parks',
	'pat_i'           => 'Two official sites sell the Patagonian permit with the same values: the Neuquén Wildlife Directorate portal and the Río Negro Mesa Directiva portal. We read both; the 2026/2027 regulation is published on the <a href="{u:regl}" rel="noopener">interprovincial regulation site</a>. According to Río Negro’s page, its electronic permit is valid in all fishing waters of Patagonia except Tierra del Fuego and Santa Cruz.',
	'pat_s'           => [
		'Choose where to buy: <a href="{u:nqn}" rel="noopener">Neuquén’s “Comprar permiso”</a> page, which lists the non-resident categories and says non-resident foreigners pay, or <a href="{u:rn}" rel="noopener">Río Negro’s portal</a>.',
		'Choose the non-resident category: 1 day, 10 days or full season. A 10-day permit for non-residents from neighbouring countries appears on both portals.',
		'Fill in the form and select the payment method (<a href="{u:rn_como}" rel="noopener">Río Negro’s “how to buy”</a>). The pages we read do not list the payment methods.',
		'You receive the credential by e-mail. Río Negro says to print it and carry it, or show it on your phone, and to check the spam folder if it does not arrive (<a href="{u:rn_faq}" rel="noopener">its FAQ</a>).',
		'Trolling, Neuquén’s preferential zone and Laguna Blanca need an extra permit, listed on the Neuquén portal; the trolling permit must be bought together with an ordinary one.',
	],
	'pat_c'           => 'Values, validity and what changes by province are in <a href="{n:licencia-pesca-patagonia-precios}">our Patagonian permit note</a>, including Chubut’s digital permit. Neuquén’s portal also lets you request its trolling or preferential-zone permit by attaching a permit from Río Negro or Chubut.',
	'h_sc'            => 'Santa Cruz',
	'sc_i'            => 'The Santa Cruz portal says the 2026-2027 permits are available for the season that runs from 01/10/2026 to 30/09/2027 and that the procedure is done online (read 16/11/2026).',
	'sc_s'            => [
		'Go to <a href="{u:sc}" rel="noopener">pesca.santacruz.gob.ar</a> and choose “Solicitar Permiso”.',
		'Have your documents ready: the portal’s list names the front of the ID document and the proof of payment. The form offers passport as a document type and a box “No soy residente del país” (I am not a resident of the country).',
		'Choose the permit type; the non-resident categories are daily, weekly and full season (<a href="{u:sc_valor}" rel="noopener">values page</a>).',
		'Pay by bank transfer or MercadoPago, in pesos, using the data on the values page, and attach the proof of payment. The page says it does not accept transfers in foreign currency, and that a MercadoPago payment must carry the holder’s full name.',
		'The portal says the permit is valid to fish from the moment it is created and that the request stays pending administrative validation.',
	],
	'sc_c'            => 'The Santa Cruz River steelhead, Jurassic Lake, the Río Gallegos and El Calafate are all in Santa Cruz: see the guides linked in the table.',
	'h_tdf'           => 'Tierra del Fuego',
	'tdf_i'           => 'The 2026-2027 season runs from 30 October 2026 to 2 May 2027 (Resolución 259/26 of the Secretaría de Ambiente, <a href="{u:tdf}" rel="noopener">page read on 16/11/2026</a>).',
	'tdf_s'           => [
		'Register on <a href="{u:siped}" rel="noopener">siped.ar</a>. The “No residente país” form asks for name, surname, document type (ID card or passport), document number, date of birth and local address or lodging, among other data (<a href="{u:siped_f}" rel="noopener">form</a>). SIPED says you can load your data, including a photo, before the permit is issued.',
		'Go to a point of sale with your ID document. SIPED says the registration is completed there, the virtual credential is generated and the permit is issued, paid at that moment. The points of sale are listed on <a href="{u:siped}" rel="noopener">siped.ar</a> (Ushuaia, Río Grande and Tolhuin).',
		'You get a proof of issue, which you must show with your ID document to the inspector.',
		'The plastic credential is collected at the point of sale; SIPED says it is valid for two years, but each season’s permit is paid again (<a href="{u:siped_a}" rel="noopener">SIPED, “Acerca”</a>).',
	],
	'tdf_c'           => 'The preferential zones (Grande and Menéndez rivers, Ewan Sur, Irigoyen and Lake Fagnano) have an additional permit: see the table in <a href="{n:licencias-extranjeros-argentina}">fishing license for foreigners in Argentina 2026/27</a>. The Secretaría de Ambiente also runs an access system for some fishing grounds (SAPTDF); the page we read describes it for Tierra del Fuego residents, so ask your guide or the Dirección General de Recursos Hídricos what applies to you.',
	'h_ctes'          => 'Corrientes',
	'ctes_i'          => 'The Corrientes Tourism Ministry lists fishing licenses under “Trámites y permisos” and sends you to the Dirección de Recursos Naturales portal (read 16/11/2026).',
	'ctes_s'          => [
		'Open the <a href="{u:ctes}" rel="noopener">Recursos Naturales portal</a> and choose “Registrarme”. The registration asks for ID type and number, e-mail, name, phone, location, a profile photo and a password (<a href="{u:ctes_reg}" rel="noopener">registration</a>).',
		'Buy the tourist license for foreigners, which is set in liters of gasoline and runs from 1 to 6 days: the table and the calculation are in <a href="{n:pesca-desde-brasil-corrientes}">fishing in Corrientes from Brazil</a>.',
		'The license carries a QR code that the inspector checks; carry it printed or on your phone.',
	],
	'ctes_c'          => 'The pages we read do not detail the payment methods for a foreigner; the Dirección de Recursos Naturales is the one to ask (contact details on its portal).',
	'h_er'            => 'Entre Ríos',
	'er_i'            => 'Entre Ríos publishes the procedure “Obtener licencia y permiso de caza o pesca deportiva” (last updated 02/10/2026 on the portal, read 16/11/2026).',
	'er_s'            => [
		'Document the portal lists: the ID document of the holder.',
		'Generate the license or permit and make the payment.',
		'It is a personal procedure, done before fishing, at one of the offices the portal lists (Paraná, La Paz, Concordia, Victoria and Villa Paranacito, among others).',
	],
	'er_c'            => 'The 5-day permit for foreigners is in the table below, from the Entre Ríos price list that our <a href="{g:la-paz}">La Paz guide</a> cites (read 22/10/2026).',
	'h_costo'         => 'What it costs, with source and date',
	'costo_i'         => 'Values for a non-resident foreigner, in Argentine pesos. We do not convert them here: dollar equivalents, trolling and the other categories are in our permit notes.',
	'costo_cols'      => [ 'Region', 'One day', 'Season', 'Source and date' ],
	'costo_rows'      => [
		[ 'Patagonia', '$ 50.000', '$ 280.000', 'Neuquén and Río Negro portals, 16/11/2026 (10 days: $ 140.000)' ],
		[ 'Santa Cruz', '$ 90.000', '$ 450.000', 'Santa Cruz portal, 16/11/2026 (weekly: $ 300.000)' ],
		[ 'Tierra del Fuego', '$ 78.000', '$ 260.000', 'Resolución 259/26, read 16/11/2026 (3 days: $ 117.000; 7 days: $ 195.000)' ],
		[ 'Corrientes', 'about $ 35.000', '–', 'Our calculation in {n:pesca-desde-brasil-corrientes|fishing in Corrientes from Brazil}' ],
		[ 'Entre Ríos', '5 days: $ 24.251,60', '–', 'Price list read on 22/10/2026, in the {g:la-paz|La Paz guide}' ],
	],
	'costo_c'         => 'More detail, with dollars: <a href="{n:licencias-extranjeros-argentina}">fishing license for foreigners in Argentina 2026/27</a> and <a href="{n:licencia-pesca-patagonia-precios}">Patagonian license 2026/27</a>.',
	'h_llevar'        => 'What to carry and what to keep',
	'llevar_l'        => [
		'<strong>Your passport</strong> or the identity document you used: Río Negro says to show the permit and an identity document at every inspection.',
		'<strong>The permit</strong>, printed and on your phone: Río Negro says either is valid; in Tierra del Fuego you show the proof of issue with your ID document.',
		'<strong>The proof of payment and the confirmation e-mail</strong>: Santa Cruz asks for the proof when you apply, and Río Negro asks you to send your ID number if the credential does not arrive.',
		'<strong>A copy that works without signal</strong>: a screenshot or a PDF saved on your phone; many fishing waters have no coverage.',
		'<strong>The rules of the water</strong> you fish: see our <a href="{n:reglas-equipo-patagonia}">gear and rules note for Patagonia</a>.',
	],
	'h_guias'         => 'Permit section of each guide',
	'guias_p'         => 'Each destination guide has its own permit section: {g:san-martin-junin|San Martín and Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel and Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-santa-cruz|Santa Cruz River}, {g:rio-gallegos|Río Gallegos}, {g:rio-grande|Río Grande}, {g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria} and {g:la-paz|La Paz}.',
	'cta'             => '<strong>Planning the trip?</strong> Tell us your dates and group in <a href="{viaje}">the request form</a> and we send you options from the operators that fit; the license is one of the things to ask about in writing.',
	'faq_h'           => 'Frequently asked questions',
	'faq'             => [
		[ 'Do I need a fishing permit in Argentina as a tourist?', 'Yes. There is no national license: you need the permit of the region where you fish, from the portals listed in the table above (read on 16/11/2026). Santa Cruz’s portal states that the permit is personal, nominative and non-transferable.' ],
		[ 'Can the lodge get the permit for me?', 'It depends on the operator. In the nine packages we compared (November 2026) the license was included in 4, not included in 3 and unspecified in 2; one Bariloche operator arranges it and charges it separately, at US$ 40 per person per day. According to one lodge, the permit is arranged a few days before arrival. Ask in writing who buys it and in whose name.' ],
		[ 'Is one permit valid in every province?', 'No. Neuquén, Río Negro, Chubut and the National Parks share one permit, and Río Negro’s page says its electronic permit is valid in all Patagonia except Tierra del Fuego and Santa Cruz. Santa Cruz’s permit is valid only inside that province (Reglamento, art. 5), and Tierra del Fuego and Corrientes issue their own.' ],
		[ 'Can a foreigner pay online with a card from abroad?', 'The official pages we read do not say. Santa Cruz names two methods, bank transfer and MercadoPago in pesos, and says it does not accept transfers in foreign currency. In Tierra del Fuego the permit is paid at the point of sale. The Neuquén and Río Negro pages do not list payment methods, and neither does the Corrientes registration. Ask the issuing body before the trip.' ],
		[ 'How far ahead should I get it?', 'No official source fixes a lead time. Santa Cruz says the permit is valid to fish from the moment it is created; Río Negro says the credential arrives by e-mail after the purchase; in Tierra del Fuego you need a visit to a point of sale. According to one lodge, the permit is arranged a few days before arrival. A season permit’s validity differs by region: see the notes linked above.' ],
		[ 'Is a permit from another province valid in Patagonia?', 'Within Neuquén, Río Negro, Chubut and the National Parks, the ordinary permit is common (Río Negro’s FAQ, 16/11/2026). Categories that belong to one province are valid only where they are issued (<a href="{n:licencia-pesca-patagonia-precios}">Patagonian permit note</a>), and Neuquén’s portal lets you request its trolling or preferential-zone permit by attaching one from Río Negro or Chubut. A Santa Cruz, Tierra del Fuego or Corrientes permit does not replace the Patagonian one.' ],
		[ 'Which document do I need, a passport?', 'Santa Cruz and Tierra del Fuego offer passport as a document type on their forms. Río Negro asks you to carry an identity document with the permit. Entre Ríos lists the holder’s ID document (DNI) on its portal. Bring your passport and check the form of the region you fish.' ],
		[ 'The permit did not arrive after I paid. What do I do?', 'Río Negro says to check the spam folder and, if it is not there, to write to the Mesa Directiva with your ID number (details on its portal). For the other regions, ask the issuing body listed in the table.' ],
		[ 'Do I have to print the permit?', 'Río Negro says to print it and carry it, and also allows showing it on your phone. In Tierra del Fuego you show the proof of issue with your ID document. Many waters have no signal, so keep an offline copy.' ],
		[ 'How much does the permit cost?', 'For a non-resident foreigner: Patagonia $ 50.000 per day and $ 280.000 per season; Santa Cruz $ 90.000 and $ 450.000; Tierra del Fuego $ 78.000 and $ 260.000 (all read on 16/11/2026, in pesos). The table above gives sources and the other categories.' ],
	],
	'cierre'          => '<em>We have not applied for these permits ourselves. This page summarises what the official portals say, read on 16 November 2026, and values already published on this site with their source and date. Procedures change every season: your guide and the issuing body have them up to date.</em>',
];

// =====================================================================================================================================
// ESPAÑOL
// =====================================================================================================================================
$N['es'] = [
	'titulo'          => 'Permiso de pesca en Argentina: cómo sacarlo, paso a paso',
	'slug'            => 'permiso-de-pesca-argentina-paso-a-paso',
	'seo_title'       => 'Permiso de pesca en Argentina: cómo sacarlo, paso a paso',
	'seo_description' => 'Permiso de pesca en Argentina para extranjeros: cuál hace falta según la región, dónde se tramita, qué documentos piden y cómo se paga (leído el 16/11/2026).',
	'bajada'          => 'Qué permiso necesitás según adónde pescás en Argentina, dónde se emite cada uno, qué piden los portales oficiales y cómo se paga: leído el 16 de noviembre de 2026.',
	'datos'           => [
		'En Argentina no hay un permiso de pesca nacional: hace falta el de la región donde pescás, y cada región tiene su portal.',
		'La Patagonia (Neuquén, Río Negro, Chubut y Parques Nacionales) comparte un permiso, que se vende en línea en el portal de Neuquén y en el de Río Negro; Santa Cruz, Tierra del Fuego y Corrientes emiten el suyo.',
		'Santa Cruz: solicitud en línea, con pasaporte como documento posible; el portal dice que se paga por transferencia bancaria o MercadoPago en pesos y que no acepta transferencias en moneda extranjera.',
		'Tierra del Fuego: registro en línea en siped.ar y emisión del permiso en un punto de venta con tu documento, pagando en el momento.',
		'Ninguna fuente oficial fija un plazo; según un lodge, el permiso se gestiona unos días antes de la llegada. Leído el 16/11/2026.',
	],
	'p0'              => '<strong>Respuesta corta.</strong> Para pescar en Argentina como turista extranjero hace falta el permiso de pesca de la región donde está el agua. Esta página recorre cada uno con las páginas oficiales que leímos el 16 de noviembre de 2026: quién lo emite, dónde se tramita, qué pide el portal y cómo se paga. Los precios no se repiten acá salvo en una tabla, cada uno con su fuente y su fecha.',
	'resp'            => [
		'<strong>Qué permiso:</strong> el de la región. La Patagonia (Neuquén, Río Negro, Chubut, Parques Nacionales) comparte uno; Santa Cruz, Tierra del Fuego y Corrientes tienen el suyo (tabla de abajo).',
		'<strong>Dónde:</strong> en línea en la Patagonia, Santa Cruz y Corrientes; en Tierra del Fuego te registrás en siped.ar y el permiso se emite en un punto de venta.',
		'<strong>¿Un extranjero puede pagar en línea?</strong> Santa Cruz dice que recibe transferencias bancarias en pesos y MercadoPago, no transferencias en moneda extranjera. Los otros portales que leímos no dicen qué medios de pago aceptan.',
		'<strong>Documentos:</strong> un documento de identidad; Santa Cruz y Tierra del Fuego ofrecen el pasaporte como opción en sus formularios.',
		'<strong>Cuánto antes:</strong> ninguna fuente oficial da un plazo; según un lodge, el permiso se gestiona unos días antes de la llegada.',
	],
	'h_tabla'         => '¿Qué permiso necesito según adónde voy?',
	'tabla_i'         => 'Páginas oficiales leídas el 16/11/2026. El permiso es personal e intransferible (lo dicen los portales de Río Negro y Santa Cruz).',
	'tabla_cols'      => [ 'Dónde pescás', 'Quién lo emite', 'Dónde se tramita', 'Opciones para extranjeros no residentes' ],
	'tabla_rows'      => [
		[ '<strong>Patagonia</strong>: Neuquén, Río Negro, Chubut y Parques Nacionales ({g:san-martin-junin|San Martín y Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel y Trevelin})', 'Dirección Provincial de Fauna de Neuquén; los permisos de Río Negro los vende la Mesa Directiva de Pesca Deportiva Zona Andina', '<a href="{u:nqn}" rel="noopener">cazaypesca.neuquen.gob.ar</a> y <a href="{u:rn}" rel="noopener">permisosdepesca.com.ar</a>', '1 día, 10 días o temporada (más un permiso de 10 días para países limítrofes)' ],
		[ '<strong>Santa Cruz</strong> ({g:rio-santa-cruz|río Santa Cruz}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos}, {g:el-calafate|El Calafate})', 'Secretaría de Estado de Pesca y Acuicultura', '<a href="{u:sc}" rel="noopener">pesca.santacruz.gob.ar</a>', '1 día, semanal o temporada' ],
		[ '<strong>Tierra del Fuego</strong> ({g:rio-grande|Río Grande})', 'Secretaría de Ambiente (Ministerio de Producción y Ambiente), Dirección General de Recursos Hídricos', '<a href="{u:siped}" rel="noopener">siped.ar</a> y puntos de venta; información en <a href="{u:tdf}" rel="noopener">prodyambiente.tierradelfuego.gob.ar</a>', '1 día, 3 días, 7 días o temporada' ],
		[ '<strong>Corrientes</strong> ({g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria})', 'Dirección de Recursos Naturales (Ministerio de Turismo)', '<a href="{u:ctes}" rel="noopener">recnaturalesctes.gob.ar</a>; <a href="{u:ctes_tur}" rel="noopener">página del Ministerio de Turismo</a>', 'Licencia de turista de 1 a 6 días, fijada en litros de nafta' ],
		[ '<strong>Entre Ríos</strong> ({g:la-paz|La Paz})', 'Secretaría de Agricultura, Ganadería y Pesca', '<a href="{u:er}" rel="noopener">portal.entrerios.gov.ar</a>', 'Permiso de 5 días para extranjeros' ],
	],
	'h_pat'           => 'Patagonia: Neuquén, Río Negro, Chubut y Parques Nacionales',
	'pat_i'           => 'Dos sitios oficiales venden el permiso patagónico, con los mismos valores: el portal de la Dirección de Fauna de Neuquén y el de la Mesa Directiva de Río Negro. Leímos los dos; el reglamento 2026/2027 se publica en el <a href="{u:regl}" rel="noopener">sitio interprovincial del reglamento</a>. Según la página de Río Negro, su permiso electrónico vale en todos los ambientes de pesca de la Patagonia, salvo Tierra del Fuego y Santa Cruz.',
	'pat_s'           => [
		'Elegí dónde comprar: la página <a href="{u:nqn}" rel="noopener">«Comprar permiso» de Neuquén</a>, que lista las categorías de no residentes y dice que el extranjero no residente paga, o el <a href="{u:rn}" rel="noopener">portal de Río Negro</a>.',
		'Elegí la categoría de no residente: 1 día, 10 días o temporada. En los dos portales aparece también un permiso de 10 días para no residentes de países limítrofes.',
		'Completá el formulario y seleccioná la forma de pago (<a href="{u:rn_como}" rel="noopener">«Cómo comprar» de Río Negro</a>). Las páginas que leímos no detallan los medios de pago.',
		'Recibís la credencial por correo electrónico. Río Negro dice que hay que imprimirla y llevarla, o mostrarla desde el celular, y revisar la casilla de spam si no llega (<a href="{u:rn_faq}" rel="noopener">sus preguntas frecuentes</a>).',
		'El trolling, la zona preferencial de Neuquén y la laguna Blanca llevan un permiso adicional, listado en el portal de Neuquén; el de trolling se compra junto con un permiso ordinario.',
	],
	'pat_c'           => 'Los valores, la validez y lo que cambia según la provincia están en <a href="{n:licencia-pesca-patagonia-precios}">nuestra nota del permiso patagónico</a>, con el permiso digital de Chubut. El portal de Neuquén también permite pedir su permiso de trolling o de zona preferencial adjuntando uno de Río Negro o de Chubut.',
	'h_sc'            => 'Santa Cruz',
	'sc_i'            => 'El portal de Santa Cruz dice que los permisos 2026-2027 están disponibles para la temporada que va del 01/10/2026 al 30/09/2027 y que el trámite se hace en línea (leído el 16/11/2026).',
	'sc_s'            => [
		'Entrá a <a href="{u:sc}" rel="noopener">pesca.santacruz.gob.ar</a> y elegí «Solicitar Permiso».',
		'Tené a mano los documentos: la lista del portal nombra el frente del documento de identidad y el comprobante de pago. El formulario ofrece pasaporte como tipo de documento y una casilla «No soy residente del país».',
		'Elegí el tipo de permiso; las categorías de no residentes son diario, semanal y temporada (<a href="{u:sc_valor}" rel="noopener">página de valores</a>).',
		'Pagá por transferencia bancaria o MercadoPago, en pesos, con los datos de la página de valores, y adjuntá el comprobante. La página dice que no acepta transferencias en moneda extranjera y que el pago por MercadoPago debe informar el nombre completo del titular.',
		'El portal dice que el permiso queda apto para pescar desde su creación y que la solicitud queda a la espera de la validación administrativa.',
	],
	'sc_c'            => 'El steelhead del río Santa Cruz, Jurassic Lake, el río Gallegos y El Calafate están todos en Santa Cruz: mirá las guías enlazadas en la tabla.',
	'h_tdf'           => 'Tierra del Fuego',
	'tdf_i'           => 'La temporada 2026-2027 va del 30 de octubre de 2026 al 2 de mayo de 2027 (Resolución 259/26 de la Secretaría de Ambiente, <a href="{u:tdf}" rel="noopener">página leída el 16/11/2026</a>).',
	'tdf_s'           => [
		'Registrate en <a href="{u:siped}" rel="noopener">siped.ar</a>. El formulario «No residente país» pide nombre, apellido, tipo de documento (DNI o pasaporte), número, fecha de nacimiento y domicilio local o alojamiento, entre otros datos (<a href="{u:siped_f}" rel="noopener">formulario</a>). SIPED dice que podés cargar tus datos, con una foto, antes de que se emita el permiso.',
		'Andá a un punto de venta con tu documento. SIPED dice que ahí se completa el registro, se genera la credencial virtual y se emite el permiso, que se paga en ese momento. Los puntos de venta están listados en <a href="{u:siped}" rel="noopener">siped.ar</a> (Ushuaia, Río Grande y Tolhuin).',
		'Recibís un comprobante de emisión, que tenés que exhibir junto con tu documento ante el inspector.',
		'La credencial plástica se retira en el punto de venta; SIPED dice que vale dos años, pero el permiso de cada temporada se paga de nuevo (<a href="{u:siped_a}" rel="noopener">SIPED, «Acerca»</a>).',
	],
	'tdf_c'           => 'Las zonas preferenciales (ríos Grande y Menéndez, Ewan Sur, Irigoyen y lago Fagnano) llevan un permiso adicional: mirá la tabla en <a href="{n:licencias-extranjeros-argentina}">licencia de pesca para extranjeros en Argentina 2026/27</a>. La Secretaría de Ambiente tiene además un sistema de acceso a algunos cotos (SAPTDF); la página que leímos lo describe para residentes de Tierra del Fuego, así que preguntale a tu guía o a la Dirección General de Recursos Hídricos qué te corresponde.',
	'h_ctes'          => 'Corrientes',
	'ctes_i'          => 'El Ministerio de Turismo de Corrientes lista las licencias de pesca en «Trámites y permisos» y te manda al portal de la Dirección de Recursos Naturales (leído el 16/11/2026).',
	'ctes_s'          => [
		'Abrí el <a href="{u:ctes}" rel="noopener">portal de Recursos Naturales</a> y elegí «Registrarme». El registro pide tipo y número de identificación, correo electrónico, nombre, teléfono, ubicación, foto de perfil y contraseña (<a href="{u:ctes_reg}" rel="noopener">registro</a>).',
		'Comprá la licencia de turista extranjero, que se fija en litros de nafta y va de 1 a 6 días: la tabla y el cálculo están en <a href="{n:pesca-desde-brasil-corrientes}">pesca en Corrientes desde Brasil</a>.',
		'La licencia lleva un código QR que el inspector verifica; llevala impresa o en el teléfono.',
	],
	'ctes_c'          => 'Las páginas que leímos no detallan los medios de pago para un extranjero; a quien hay que preguntarle es a la Dirección de Recursos Naturales (datos de contacto en su portal).',
	'h_er'            => 'Entre Ríos',
	'er_i'            => 'Entre Ríos publica el trámite «Obtener licencia y permiso de caza o pesca deportiva» (última actualización 02/10/2026 en el portal, leído el 16/11/2026).',
	'er_s'            => [
		'Documentación que lista el portal: el documento de identidad del titular.',
		'Generá la licencia o el permiso y hacé el pago.',
		'Es un trámite personal, antes de pescar, en una de las oficinas que lista el portal (Paraná, La Paz, Concordia, Victoria y Villa Paranacito, entre otras).',
	],
	'er_c'            => 'El permiso de 5 días para extranjeros está en la tabla de abajo, del cuadro de valores de Entre Ríos que cita nuestra <a href="{g:la-paz}">guía de La Paz</a> (leído el 22/10/2026).',
	'h_costo'         => 'Cuánto cuesta, con fuente y fecha',
	'costo_i'         => 'Valores para un extranjero no residente, en pesos argentinos. No los convertimos acá: los dólares aproximados, el trolling y las demás categorías están en nuestras notas de permisos.',
	'costo_cols'      => [ 'Región', 'Un día', 'Temporada', 'Fuente y fecha' ],
	'costo_rows'      => [
		[ 'Patagonia', '$ 50.000', '$ 280.000', 'Portales de Neuquén y Río Negro, 16/11/2026 (10 días: $ 140.000)' ],
		[ 'Santa Cruz', '$ 90.000', '$ 450.000', 'Portal de Santa Cruz, 16/11/2026 (semanal: $ 300.000)' ],
		[ 'Tierra del Fuego', '$ 78.000', '$ 260.000', 'Resolución 259/26, leída el 16/11/2026 (3 días: $ 117.000; 7 días: $ 195.000)' ],
		[ 'Corrientes', 'unos $ 35.000', '–', 'Cálculo nuestro en {n:pesca-desde-brasil-corrientes|pesca en Corrientes desde Brasil}' ],
		[ 'Entre Ríos', '5 días: $ 24.251,60', '–', 'Cuadro leído el 22/10/2026, en la {g:la-paz|guía de La Paz}' ],
	],
	'costo_c'         => 'Más detalle, con dólares: <a href="{n:licencias-extranjeros-argentina}">licencia de pesca para extranjeros en Argentina 2026/27</a> y <a href="{n:licencia-pesca-patagonia-precios}">licencia de la Patagonia 2026/27</a>.',
	'h_llevar'        => 'Qué llevar y qué guardar',
	'llevar_l'        => [
		'<strong>Tu pasaporte</strong> o el documento de identidad que usaste: Río Negro dice que hay que exhibir el permiso y un documento que acredite la identidad en cada control.',
		'<strong>El permiso</strong>, impreso y en el teléfono: Río Negro dice que valen los dos; en Tierra del Fuego se exhibe el comprobante de emisión junto con el documento.',
		'<strong>El comprobante de pago y el correo de confirmación</strong>: Santa Cruz pide el comprobante al solicitar, y Río Negro pide que escribas con tu número de documento si la credencial no llega.',
		'<strong>Una copia que funcione sin señal</strong>: una captura o un PDF guardado en el teléfono; muchos ambientes de pesca no tienen cobertura.',
		'<strong>Las reglas del ambiente</strong> donde pescás: mirá nuestra <a href="{n:reglas-equipo-patagonia}">nota de equipo y reglas de la Patagonia</a>.',
	],
	'h_guias'         => 'La sección del permiso de cada guía',
	'guias_p'         => 'Cada guía de destino tiene su propia sección de permiso: {g:san-martin-junin|San Martín y Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel y Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-santa-cruz|río Santa Cruz}, {g:rio-gallegos|Río Gallegos}, {g:rio-grande|Río Grande}, {g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria} y {g:la-paz|La Paz}.',
	'cta'             => '<strong>¿Estás armando el viaje?</strong> Contanos fechas y grupo en <a href="{viaje}">el formulario de pedido</a> y te mandamos opciones de los operadores que encajan; la licencia es una de las cosas para preguntar por escrito.',
	'faq_h'           => 'Preguntas frecuentes',
	'faq'             => [
		[ '¿Hace falta permiso de pesca en Argentina siendo turista?', 'Sí. No hay una licencia nacional: hace falta el permiso de la región donde pescás, en los portales de la tabla de arriba (leídos el 16/11/2026). El portal de Santa Cruz aclara que el permiso es personal, nominativo e intransferible.' ],
		[ '¿Lo puede sacar el lodge por mí?', 'Depende del operador. En los nueve paquetes que comparamos (noviembre de 2026) la licencia estaba incluida en 4, no incluida en 3 y sin especificar en 2; un operador de Bariloche la gestiona y la cobra aparte, a US$ 40 por persona por día. Según un lodge, el permiso se gestiona unos días antes de la llegada. Preguntá por escrito quién lo compra y a nombre de quién.' ],
		[ '¿Un permiso vale en todas las provincias?', 'No. Neuquén, Río Negro, Chubut y los Parques Nacionales comparten un permiso, y la página de Río Negro dice que su permiso electrónico vale en toda la Patagonia salvo Tierra del Fuego y Santa Cruz. El permiso de Santa Cruz vale solo dentro de esa provincia (Reglamento, art. 5), y Tierra del Fuego y Corrientes emiten el suyo.' ],
		[ '¿Puedo pagar con una tarjeta del exterior?', 'Las páginas oficiales que leímos no lo dicen. Santa Cruz nombra dos medios, transferencia bancaria y MercadoPago en pesos, y dice que no acepta transferencias en moneda extranjera. En Tierra del Fuego el permiso se paga en el punto de venta. Las páginas de Neuquén y Río Negro no listan medios de pago, ni tampoco el registro de Corrientes. Preguntale al organismo que emite antes del viaje.' ],
		[ '¿Cuánto antes lo saco?', 'Ninguna fuente oficial fija un plazo. Santa Cruz dice que el permiso queda apto para pescar desde su creación; Río Negro dice que la credencial llega por correo tras la compra; en Tierra del Fuego hace falta pasar por un punto de venta. Según un lodge, el permiso se gestiona unos días antes de la llegada. La vigencia de un permiso de temporada cambia según la región: mirá las notas enlazadas arriba.' ],
		[ '¿Vale un permiso de otra provincia en la Patagonia?', 'Dentro de Neuquén, Río Negro, Chubut y los Parques Nacionales el permiso ordinario es común (preguntas frecuentes de Río Negro, 16/11/2026). Las categorías propias de una provincia valen solo donde se emiten (<a href="{n:licencia-pesca-patagonia-precios}">nota del permiso patagónico</a>), y el portal de Neuquén permite pedir su trolling o su zona preferencial adjuntando un permiso de Río Negro o Chubut. Un permiso de Santa Cruz, Tierra del Fuego o Corrientes no reemplaza al patagónico.' ],
		[ '¿Qué documento hace falta, el pasaporte?', 'Santa Cruz y Tierra del Fuego ofrecen el pasaporte como tipo de documento en sus formularios. Río Negro pide llevar un documento que acredite la identidad junto con el permiso. Entre Ríos lista el documento de identidad (DNI) del titular en su portal. Llevá tu pasaporte y revisá el formulario de la región donde pescás.' ],
		[ 'Pagué y no me llegó el permiso, ¿qué hago?', 'Río Negro dice que hay que revisar la casilla de spam y, si no está, escribir a la Mesa Directiva con tu número de documento (datos en su portal). Para las otras regiones, consultá al organismo que figura en la tabla.' ],
		[ '¿Hay que imprimir el permiso?', 'Río Negro dice que hay que imprimirlo y llevarlo, y también admite mostrarlo desde el celular. En Tierra del Fuego se exhibe el comprobante de emisión con el documento. Muchos ambientes no tienen señal: guardá una copia sin conexión.' ],
		[ '¿Cuánto cuesta el permiso?', 'Para un extranjero no residente: Patagonia $ 50.000 por día y $ 280.000 por temporada; Santa Cruz $ 90.000 y $ 450.000; Tierra del Fuego $ 78.000 y $ 260.000 (todo leído el 16/11/2026, en pesos). La tabla de arriba da las fuentes y las demás categorías.' ],
	],
	'cierre'          => '<em>No tramitamos estos permisos nosotros mismos. Esta página resume lo que dicen los portales oficiales, leídos el 16 de noviembre de 2026, y valores ya publicados en este sitio con su fuente y su fecha. Los trámites cambian cada temporada: tu guía y el organismo que emite los tienen al día.</em>',
];

// =====================================================================================================================================
// PORTUGUÊS
// =====================================================================================================================================
$N['pt'] = [
	'titulo'          => 'Licença de pesca na Argentina: como tirar, passo a passo',
	'slug'            => 'licenca-de-pesca-argentina-passo-a-passo',
	'seo_title'       => 'Licença de pesca na Argentina: como tirar, passo a passo',
	'seo_description' => 'Licença de pesca na Argentina para estrangeiros: qual é preciso em cada região, onde tirar on-line, que documentos pedem e como pagar. Lido em 16/11/2026.',
	'bajada'          => 'Qual licença você precisa conforme onde pesca na Argentina, onde cada uma é emitida, o que pedem os portais oficiais e como se paga: lido em 16 de novembro de 2026.',
	'datos'           => [
		'Na Argentina não existe licença de pesca nacional: é preciso a da região onde você pesca, e cada região tem o seu portal.',
		'A Patagônia (Neuquén, Río Negro, Chubut e Parques Nacionais) compartilha uma licença, vendida on-line no portal de Neuquén e no de Río Negro; Santa Cruz, Terra do Fogo e Corrientes emitem a sua.',
		'Santa Cruz: solicitação on-line, com passaporte como documento possível; o portal diz que o pagamento é por transferência bancária ou MercadoPago em pesos e que não aceita transferências em moeda estrangeira.',
		'Terra do Fogo: cadastro on-line em siped.ar e emissão da licença em um ponto de venda com o seu documento, pagando na hora.',
		'Nenhuma fonte oficial fixa um prazo; segundo um lodge, a licença é providenciada poucos dias antes da chegada. Lido em 16/11/2026.',
	],
	'p0'              => '<strong>Resposta curta.</strong> Para pescar na Argentina como turista estrangeiro é preciso a licença de pesca da região onde fica a água. Esta página percorre cada uma com as páginas oficiais que lemos em 16 de novembro de 2026: quem emite, onde se tira, o que o portal pede e como se paga. Os preços não se repetem aqui, exceto em uma tabela, cada um com sua fonte e sua data.',
	'resp'            => [
		'<strong>Qual licença:</strong> a da região. A Patagônia (Neuquén, Río Negro, Chubut, Parques Nacionais) compartilha uma; Santa Cruz, Terra do Fogo e Corrientes têm a sua (tabela abaixo).',
		'<strong>Onde:</strong> on-line na Patagônia, em Santa Cruz e em Corrientes; na Terra do Fogo você se cadastra em siped.ar e a licença é emitida em um ponto de venda.',
		'<strong>Um estrangeiro pode pagar on-line?</strong> Santa Cruz diz que recebe transferências bancárias em pesos e MercadoPago, não transferências em moeda estrangeira. Os outros portais que lemos não dizem quais meios de pagamento aceitam.',
		'<strong>Documentos:</strong> um documento de identidade; Santa Cruz e Terra do Fogo oferecem o passaporte como opção nos formulários.',
		'<strong>Com quanta antecedência:</strong> nenhuma fonte oficial dá um prazo; segundo um lodge, a licença é providenciada poucos dias antes da chegada.',
	],
	'h_tabla'         => 'Qual licença preciso conforme aonde vou?',
	'tabla_i'         => 'Páginas oficiais lidas em 16/11/2026. A licença é pessoal e intransferível (dizem os portais de Río Negro e Santa Cruz).',
	'tabla_cols'      => [ 'Onde você pesca', 'Quem emite', 'Onde se tira', 'Opções para estrangeiros não residentes' ],
	'tabla_rows'      => [
		[ '<strong>Patagônia</strong>: Neuquén, Río Negro, Chubut e Parques Nacionais ({g:san-martin-junin|San Martín e Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel e Trevelin})', 'Direção Provincial de Fauna de Neuquén; as licenças de Río Negro são vendidas pela Mesa Directiva de Pesca Deportiva Zona Andina', '<a href="{u:nqn}" rel="noopener">cazaypesca.neuquen.gob.ar</a> e <a href="{u:rn}" rel="noopener">permisosdepesca.com.ar</a>', '1 dia, 10 dias ou temporada (mais uma licença de 10 dias para países limítrofes)' ],
		[ '<strong>Santa Cruz</strong> ({g:rio-santa-cruz|rio Santa Cruz}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos}, {g:el-calafate|El Calafate})', 'Secretaría de Estado de Pesca y Acuicultura', '<a href="{u:sc}" rel="noopener">pesca.santacruz.gob.ar</a>', '1 dia, semanal ou temporada' ],
		[ '<strong>Terra do Fogo</strong> ({g:rio-grande|Río Grande})', 'Secretaría de Ambiente (Ministério de Produção e Ambiente), Direção Geral de Recursos Hídricos', '<a href="{u:siped}" rel="noopener">siped.ar</a> e pontos de venda; informações em <a href="{u:tdf}" rel="noopener">prodyambiente.tierradelfuego.gob.ar</a>', '1 dia, 3 dias, 7 dias ou temporada' ],
		[ '<strong>Corrientes</strong> ({g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria})', 'Direção de Recursos Naturais (Ministério do Turismo)', '<a href="{u:ctes}" rel="noopener">recnaturalesctes.gob.ar</a>; <a href="{u:ctes_tur}" rel="noopener">página do Ministério do Turismo</a>', 'Licença de turista de 1 a 6 dias, fixada em litros de gasolina' ],
		[ '<strong>Entre Ríos</strong> ({g:la-paz|La Paz})', 'Secretaría de Agricultura, Ganadería y Pesca', '<a href="{u:er}" rel="noopener">portal.entrerios.gov.ar</a>', 'Licença de 5 dias para estrangeiros' ],
	],
	'h_pat'           => 'Patagônia: Neuquén, Río Negro, Chubut e Parques Nacionais',
	'pat_i'           => 'Dois sites oficiais vendem a licença patagônica, com os mesmos valores: o portal da Direção de Fauna de Neuquén e o da Mesa Directiva de Río Negro. Lemos os dois; o regulamento 2026/2027 é publicado no <a href="{u:regl}" rel="noopener">site interprovincial do regulamento</a>. Segundo a página de Río Negro, sua licença eletrônica vale em todos os ambientes de pesca da Patagônia, exceto Terra do Fogo e Santa Cruz.',
	'pat_s'           => [
		'Escolha onde comprar: a página <a href="{u:nqn}" rel="noopener">«Comprar permiso» de Neuquén</a>, que lista as categorias de não residentes e diz que o estrangeiro não residente paga, ou o <a href="{u:rn}" rel="noopener">portal de Río Negro</a>.',
		'Escolha a categoria de não residente: 1 dia, 10 dias ou temporada. Nos dois portais aparece também uma licença de 10 dias para não residentes de países limítrofes.',
		'Preencha o formulário e selecione a forma de pagamento (<a href="{u:rn_como}" rel="noopener">«Como comprar» de Río Negro</a>). As páginas que lemos não detalham os meios de pagamento.',
		'Você recebe a credencial por e-mail. Río Negro diz que é preciso imprimi-la e levá-la, ou mostrá-la no celular, e conferir a caixa de spam se ela não chegar (<a href="{u:rn_faq}" rel="noopener">suas perguntas frequentes</a>).',
		'O trolling, a zona preferencial de Neuquén e a laguna Blanca levam uma licença adicional, listada no portal de Neuquén; a de trolling é comprada junto com uma licença ordinária.',
	],
	'pat_c'           => 'Os valores, a validade e o que muda por província estão na <a href="{n:licencia-pesca-patagonia-precios}">nossa nota da licença patagônica</a>, com a licença digital de Chubut. O portal de Neuquén também permite pedir sua licença de trolling ou de zona preferencial anexando uma de Río Negro ou de Chubut.',
	'h_sc'            => 'Santa Cruz',
	'sc_i'            => 'O portal de Santa Cruz diz que as licenças 2026-2027 estão disponíveis para a temporada que vai de 01/10/2026 a 30/09/2027 e que o trâmite é feito on-line (lido em 16/11/2026).',
	'sc_s'            => [
		'Entre em <a href="{u:sc}" rel="noopener">pesca.santacruz.gob.ar</a> e escolha «Solicitar Permiso».',
		'Deixe os documentos à mão: a lista do portal cita a frente do documento de identidade e o comprovante de pagamento. O formulário oferece passaporte como tipo de documento e uma caixa «No soy residente del país» (não sou residente no país).',
		'Escolha o tipo de licença; as categorias de não residentes são diária, semanal e temporada (<a href="{u:sc_valor}" rel="noopener">página de valores</a>).',
		'Pague por transferência bancária ou MercadoPago, em pesos, com os dados da página de valores, e anexe o comprovante. A página diz que não aceita transferências em moeda estrangeira e que o pagamento por MercadoPago deve informar o nome completo do titular.',
		'O portal diz que a licença fica apta para pescar desde a sua criação e que a solicitação continua pendente de validação administrativa.',
	],
	'sc_c'            => 'O steelhead do rio Santa Cruz, Jurassic Lake, o rio Gallegos e El Calafate ficam todos em Santa Cruz: veja os guias com link na tabela.',
	'h_tdf'           => 'Terra do Fogo',
	'tdf_i'           => 'A temporada 2026-2027 vai de 30 de outubro de 2026 a 2 de maio de 2027 (Resolución 259/26 da Secretaría de Ambiente, <a href="{u:tdf}" rel="noopener">página lida em 16/11/2026</a>).',
	'tdf_s'           => [
		'Cadastre-se em <a href="{u:siped}" rel="noopener">siped.ar</a>. O formulário «No residente país» pede nome, sobrenome, tipo de documento (DNI ou passaporte), número, data de nascimento e endereço local ou hospedagem, entre outros dados (<a href="{u:siped_f}" rel="noopener">formulário</a>). O SIPED diz que você pode carregar seus dados, com uma foto, antes de a licença ser emitida.',
		'Vá a um ponto de venda com o seu documento. O SIPED diz que lá o cadastro é concluído, a credencial virtual é gerada e a licença é emitida, paga naquele momento. Os pontos de venda estão listados em <a href="{u:siped}" rel="noopener">siped.ar</a> (Ushuaia, Río Grande e Tolhuin).',
		'Você recebe um comprovante de emissão, que deve exibir junto com o documento ao fiscal.',
		'A credencial plástica é retirada no ponto de venda; o SIPED diz que vale dois anos, mas a licença de cada temporada é paga de novo (<a href="{u:siped_a}" rel="noopener">SIPED, «Acerca»</a>).',
	],
	'tdf_c'           => 'As zonas preferenciais (rios Grande e Menéndez, Ewan Sur, Irigoyen e lago Fagnano) levam uma licença adicional: veja a tabela em <a href="{n:licencias-extranjeros-argentina}">licença de pesca para estrangeiros na Argentina 2026/27</a>. A Secretaría de Ambiente tem ainda um sistema de acesso a alguns cotos (SAPTDF); a página que lemos o descreve para residentes da Terra do Fogo, então pergunte ao seu guia ou à Direção Geral de Recursos Hídricos o que vale para você.',
	'h_ctes'          => 'Corrientes',
	'ctes_i'          => 'O Ministério do Turismo de Corrientes lista as licenças de pesca em «Trámites y permisos» e leva você ao portal da Direção de Recursos Naturais (lido em 16/11/2026).',
	'ctes_s'          => [
		'Abra o <a href="{u:ctes}" rel="noopener">portal de Recursos Naturais</a> e escolha «Registrarme». O cadastro pede tipo e número de identificação, e-mail, nome, telefone, localização, foto de perfil e senha (<a href="{u:ctes_reg}" rel="noopener">cadastro</a>).',
		'Compre a licença de turista estrangeiro, fixada em litros de gasolina e de 1 a 6 dias: a tabela e o cálculo estão em <a href="{n:pesca-desde-brasil-corrientes}">pesca em Corrientes desde o Brasil</a>.',
		'A licença leva um código QR que o fiscal verifica; leve-a impressa ou no celular.',
	],
	'ctes_c'          => 'As páginas que lemos não detalham os meios de pagamento para um estrangeiro; quem deve responder é a Direção de Recursos Naturais (contatos no portal).',
	'h_er'            => 'Entre Ríos',
	'er_i'            => 'Entre Ríos publica o trâmite «Obtener licencia y permiso de caza o pesca deportiva» (última atualização 02/10/2026 no portal, lido em 16/11/2026).',
	'er_s'            => [
		'Documentação que o portal lista: o documento de identidade do titular.',
		'Gere a licença ou permissão e faça o pagamento.',
		'É um trâmite pessoal, antes de pescar, em um dos escritórios que o portal lista (Paraná, La Paz, Concordia, Victoria e Villa Paranacito, entre outros).',
	],
	'er_c'            => 'A licença de 5 dias para estrangeiros está na tabela abaixo, do quadro de valores de Entre Ríos que cita o nosso <a href="{g:la-paz}">guia de La Paz</a> (lido em 22/10/2026).',
	'h_costo'         => 'Quanto custa, com fonte e data',
	'costo_i'         => 'Valores para um estrangeiro não residente, em pesos argentinos. Não os convertemos aqui: os dólares aproximados, o trolling e as demais categorias estão nas nossas notas de licenças.',
	'costo_cols'      => [ 'Região', 'Um dia', 'Temporada', 'Fonte e data' ],
	'costo_rows'      => [
		[ 'Patagônia', '$ 50.000', '$ 280.000', 'Portais de Neuquén e Río Negro, 16/11/2026 (10 dias: $ 140.000)' ],
		[ 'Santa Cruz', '$ 90.000', '$ 450.000', 'Portal de Santa Cruz, 16/11/2026 (semanal: $ 300.000)' ],
		[ 'Terra do Fogo', '$ 78.000', '$ 260.000', 'Resolución 259/26, lida em 16/11/2026 (3 dias: $ 117.000; 7 dias: $ 195.000)' ],
		[ 'Corrientes', 'cerca de $ 35.000', '–', 'Cálculo nosso em {n:pesca-desde-brasil-corrientes|pesca em Corrientes desde o Brasil}' ],
		[ 'Entre Ríos', '5 dias: $ 24.251,60', '–', 'Quadro lido em 22/10/2026, no {g:la-paz|guia de La Paz}' ],
	],
	'costo_c'         => 'Mais detalhes, com dólares: <a href="{n:licencias-extranjeros-argentina}">licença de pesca para estrangeiros na Argentina 2026/27</a> e <a href="{n:licencia-pesca-patagonia-precios}">licença da Patagônia 2026/27</a>.',
	'h_llevar'        => 'O que levar e o que guardar',
	'llevar_l'        => [
		'<strong>Seu passaporte</strong> ou o documento de identidade que você usou: Río Negro diz que é preciso exibir a licença e um documento que comprove a identidade em cada fiscalização.',
		'<strong>A licença</strong>, impressa e no celular: Río Negro diz que valem as duas; na Terra do Fogo exibe-se o comprovante de emissão junto com o documento.',
		'<strong>O comprovante de pagamento e o e-mail de confirmação</strong>: Santa Cruz pede o comprovante ao solicitar, e Río Negro pede que você escreva com o número do documento se a credencial não chegar.',
		'<strong>Uma cópia que funcione sem sinal</strong>: uma captura de tela ou um PDF salvo no celular; muitos ambientes de pesca não têm cobertura.',
		'<strong>As regras do ambiente</strong> onde você pesca: veja a nossa <a href="{n:reglas-equipo-patagonia}">nota de equipamento e regras da Patagônia</a>.',
	],
	'h_guias'         => 'A seção da licença de cada guia',
	'guias_p'         => 'Cada guia de destino tem a sua própria seção de licença: {g:san-martin-junin|San Martín e Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel e Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-santa-cruz|rio Santa Cruz}, {g:rio-gallegos|Río Gallegos}, {g:rio-grande|Río Grande}, {g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria} e {g:la-paz|La Paz}.',
	'cta'             => '<strong>Está montando a viagem?</strong> Conte as datas e o grupo no <a href="{viaje}">formulário de pedido</a> e enviamos opções dos operadores que se encaixam; a licença é uma das coisas para perguntar por escrito.',
	'faq_h'           => 'Perguntas frequentes',
	'faq'             => [
		[ 'Preciso de licença de pesca na Argentina como turista?', 'Sim. Não existe licença nacional: é preciso a licença da região onde você pesca, nos portais da tabela acima (lidos em 16/11/2026). O portal de Santa Cruz esclarece que a licença é pessoal, nominativa e intransferível.' ],
		[ 'O lodge pode tirar a licença por mim?', 'Depende do operador. Nos nove pacotes que comparamos (novembro de 2026) a licença estava incluída em 4, não incluída em 3 e sem especificar em 2; um operador de Bariloche a providencia e a cobra à parte, a US$ 40 por pessoa por dia. Segundo um lodge, a licença é providenciada poucos dias antes da chegada. Pergunte por escrito quem a compra e em nome de quem.' ],
		[ 'Uma licença vale em todas as províncias?', 'Não. Neuquén, Río Negro, Chubut e os Parques Nacionais compartilham uma licença, e a página de Río Negro diz que sua licença eletrônica vale em toda a Patagônia, exceto Terra do Fogo e Santa Cruz. A licença de Santa Cruz vale só dentro dessa província (Reglamento, art. 5), e Terra do Fogo e Corrientes emitem a sua.' ],
		[ 'Posso pagar com cartão do exterior?', 'As páginas oficiais que lemos não dizem. Santa Cruz cita dois meios, transferência bancária e MercadoPago em pesos, e diz que não aceita transferências em moeda estrangeira. Na Terra do Fogo a licença é paga no ponto de venda. As páginas de Neuquén e Río Negro não listam meios de pagamento, nem o cadastro de Corrientes. Pergunte ao órgão que emite antes da viagem.' ],
		[ 'Com quanta antecedência tiro?', 'Nenhuma fonte oficial fixa um prazo. Santa Cruz diz que a licença fica apta para pescar desde a sua criação; Río Negro diz que a credencial chega por e-mail após a compra; na Terra do Fogo é preciso passar por um ponto de venda. Segundo um lodge, a licença é providenciada poucos dias antes da chegada. A validade de uma licença de temporada muda conforme a região: veja as notas com link acima.' ],
		[ 'Uma licença de outra província vale na Patagônia?', 'Dentro de Neuquén, Río Negro, Chubut e dos Parques Nacionais a licença ordinária é comum (perguntas frequentes de Río Negro, 16/11/2026). As categorias próprias de uma província valem só onde são emitidas (<a href="{n:licencia-pesca-patagonia-precios}">nota da licença patagônica</a>), e o portal de Neuquén permite pedir seu trolling ou sua zona preferencial anexando uma licença de Río Negro ou Chubut. Uma licença de Santa Cruz, Terra do Fogo ou Corrientes não substitui a patagônica.' ],
		[ 'Que documento é preciso, o passaporte?', 'Santa Cruz e Terra do Fogo oferecem o passaporte como tipo de documento nos formulários. Río Negro pede que você leve um documento que comprove a identidade junto com a licença. Entre Ríos lista o documento de identidade (DNI) do titular no portal. Leve o passaporte e confira o formulário da região onde pesca.' ],
		[ 'Paguei e a licença não chegou, o que faço?', 'Río Negro diz para conferir a caixa de spam e, se não estiver lá, escrever à Mesa Directiva com o número do documento (dados no portal). Para as outras regiões, consulte o órgão que consta na tabela.' ],
		[ 'Preciso imprimir a licença?', 'Río Negro diz que é preciso imprimi-la e levá-la, e também admite mostrá-la no celular. Na Terra do Fogo exibe-se o comprovante de emissão com o documento. Muitos ambientes não têm sinal: guarde uma cópia sem conexão.' ],
		[ 'Quanto custa a licença?', 'Para um estrangeiro não residente: Patagônia $ 50.000 por dia e $ 280.000 por temporada; Santa Cruz $ 90.000 e $ 450.000; Terra do Fogo $ 78.000 e $ 260.000 (tudo lido em 16/11/2026, em pesos). A tabela acima traz as fontes e as demais categorias.' ],
	],
	'cierre'          => '<em>Não tiramos essas licenças nós mesmos. Esta página resume o que dizem os portais oficiais, lidos em 16 de novembro de 2026, e valores já publicados neste site com sua fonte e sua data. Os trâmites mudam a cada temporada: o seu guia e o órgão que emite os têm em dia.</em>',
];

// Sources (name per language; date = when the page was read).
$fuente_nombre = [
	'nqn'      => [ 'es' => 'Dirección Provincial de Fauna de Neuquén: comprar permiso de pesca, temporada 2026-2027', 'en' => 'Neuquén Provincial Wildlife Directorate: buy a fishing permit, 2026-2027 season', 'pt' => 'Direção Provincial de Fauna de Neuquén: comprar licença de pesca, temporada 2026-2027', 'f' => '20261116', 'o' => 1 ],
	'rn'       => [ 'es' => 'Mesa Directiva de Pesca Deportiva Zona Andina: permisos de pesca de Río Negro (venta en línea)', 'en' => 'Mesa Directiva de Pesca Deportiva Zona Andina: Río Negro fishing permits (online sale)', 'pt' => 'Mesa Directiva de Pesca Deportiva Zona Andina: licenças de pesca de Río Negro (venda on-line)', 'f' => '20261116', 'o' => 1 ],
	'rn_como'  => [ 'es' => 'Mesa Directiva de Pesca Deportiva Zona Andina: cómo comprar el permiso electrónico', 'en' => 'Mesa Directiva de Pesca Deportiva Zona Andina: how to buy the electronic permit', 'pt' => 'Mesa Directiva de Pesca Deportiva Zona Andina: como comprar a licença eletrônica', 'f' => '20261116', 'o' => 1 ],
	'rn_faq'   => [ 'es' => 'Mesa Directiva de Pesca Deportiva Zona Andina: preguntas frecuentes', 'en' => 'Mesa Directiva de Pesca Deportiva Zona Andina: frequently asked questions', 'pt' => 'Mesa Directiva de Pesca Deportiva Zona Andina: perguntas frequentes', 'f' => '20261116', 'o' => 1 ],
	'regl'     => [ 'es' => 'Reglamento de Pesca Deportiva Continental Patagónico 2026/2027 (sitio interprovincial)', 'en' => 'Continental Patagonia General Sportfishing Rules and Regulations 2026/2027 (interprovincial site)', 'pt' => 'Regulamento de Pesca Esportiva Continental Patagônico 2026/2027 (site interprovincial)', 'f' => '20261116', 'o' => 1 ],
	'sc'       => [ 'es' => 'Pesca y Acuicultura de Santa Cruz: portal oficial, temporada 2026-2027', 'en' => 'Santa Cruz Fishing and Aquaculture: official portal, 2026-2027 season', 'pt' => 'Pesca e Aquicultura de Santa Cruz: portal oficial, temporada 2026-2027', 'f' => '20261116', 'o' => 1 ],
	'sc_alta'  => [ 'es' => 'Santa Cruz: solicitar permiso de pesca (trámite en línea)', 'en' => 'Santa Cruz: apply for a fishing permit (online procedure)', 'pt' => 'Santa Cruz: solicitar licença de pesca (trâmite on-line)', 'f' => '20261116', 'o' => 1 ],
	'sc_valor' => [ 'es' => 'Santa Cruz: valores y categorías 2026/2027 (Disposición N.º 242/2026) y datos de pago', 'en' => 'Santa Cruz: values and categories 2026/2027 (Disposición No. 242/2026) and payment details', 'pt' => 'Santa Cruz: valores e categorias 2026/2027 (Disposición n.º 242/2026) e dados de pagamento', 'f' => '20261116', 'o' => 1 ],
	'tdf'      => [ 'es' => 'Ministerio de Producción y Ambiente de Tierra del Fuego: pesca deportiva, temporada 2026-2027', 'en' => 'Tierra del Fuego Ministry of Production and Environment: sport fishing, 2026-2027 season', 'pt' => 'Ministério de Produção e Ambiente da Terra do Fogo: pesca esportiva, temporada 2026-2027', 'f' => '20261116', 'o' => 1 ],
	'tdf_pdf'  => [ 'es' => 'Resolución 259/26 de la Secretaría de Ambiente de Tierra del Fuego (temporada de pesca 2026)', 'en' => 'Resolución 259/26 of the Tierra del Fuego Secretaría de Ambiente (2026 fishing season)', 'pt' => 'Resolución 259/26 da Secretaría de Ambiente da Terra do Fogo (temporada de pesca 2026)', 'f' => '20261116', 'o' => 1 ],
	'siped'    => [ 'es' => 'SIPED, Sistema de Información de Pesca Deportiva de Tierra del Fuego: puntos de venta', 'en' => 'SIPED, Tierra del Fuego Sport Fishing Information System: points of sale', 'pt' => 'SIPED, Sistema de Informação de Pesca Esportiva da Terra do Fogo: pontos de venda', 'f' => '20261116', 'o' => 1 ],
	'siped_a'  => [ 'es' => 'SIPED: acerca del sistema y cómo conseguir el permiso', 'en' => 'SIPED: about the system and how to get the permit', 'pt' => 'SIPED: sobre o sistema e como conseguir a licença', 'f' => '20261116', 'o' => 1 ],
	'siped_f'  => [ 'es' => 'SIPED: registro de no residentes del país', 'en' => 'SIPED: registration of non-residents of the country', 'pt' => 'SIPED: cadastro de não residentes no país', 'f' => '20261116', 'o' => 1 ],
	'ctes_tur' => [ 'es' => 'Ministerio de Turismo de Corrientes: trámites y permisos', 'en' => 'Corrientes Ministry of Tourism: procedures and permits', 'pt' => 'Ministério do Turismo de Corrientes: trâmites e permissões', 'f' => '20261116', 'o' => 1 ],
	'ctes'     => [ 'es' => 'Dirección de Recursos Naturales de Corrientes: portal de licencias', 'en' => 'Corrientes Dirección de Recursos Naturales: licence portal', 'pt' => 'Direção de Recursos Naturais de Corrientes: portal de licenças', 'f' => '20261116', 'o' => 1 ],
	'ctes_reg' => [ 'es' => 'Dirección de Recursos Naturales de Corrientes: registro de usuarios', 'en' => 'Corrientes Dirección de Recursos Naturales: user registration', 'pt' => 'Direção de Recursos Naturais de Corrientes: cadastro de usuários', 'f' => '20261116', 'o' => 1 ],
	'er'       => [ 'es' => 'Entre Ríos: obtener licencia y permiso de caza o pesca deportiva (portal de trámites)', 'en' => 'Entre Ríos: obtain a hunting or sport fishing license and permit (procedures portal)', 'pt' => 'Entre Ríos: obter licença e permissão de caça ou pesca esportiva (portal de trâmites)', 'f' => '20261116', 'o' => 1 ],
];

$nota_tipo = 'como';
$origen    = 'Permiso de pesca paso a paso, 16/11/2026 (pedido del dueño tras una conversación con Grok que mostró el hueco). Armada con las páginas oficiales leídas el 16/11/2026 con investigar.yml (corridas permisos-1, permisos-2 y permisos-3): portal de Fauna de Neuquén, portal de la Mesa Directiva de Río Negro (permisosdepesca.com.ar), Santa Cruz (inicio, valores, solicitud, reglamento art. 5), Tierra del Fuego (prodyambiente, SIPED, Resolución 259/26), Corrientes (página de turismo y portal de Recursos Naturales, registro) y Entre Ríos (trámite 708); y con nuestras notas de valores ya publicadas. No se pudieron leer pescacontinental.gob.ar (sin respuesta, también con navegador), reglamentodepesca.org.ar/reglamento (solo la portada) ni el portal de Chubut (403): no se mencionan como leídos. No se dice cómo paga un extranjero con tarjeta porque ningún portal leído lo dice. «Según un lodge» (permiso unos días antes de la llegada) es dato del dueño, sin nombre. Foto provisoria: la del dueño ya cargada en la nota reglas-equipo-patagonia (Unsplash, Agustín Gorordo Coggiola).';

$armar = function ( array $n ): string {
	return pm_sc_p( $n['p0'] ) . pm_sc_lista( $n['resp'] )
		. pm_sc_h2( $n['h_tabla'] ) . pm_sc_p( $n['tabla_i'] ) . pm_tp_tabla( $n['tabla_cols'], $n['tabla_rows'] )
		. pm_sc_h2( $n['h_pat'] ) . pm_sc_p( $n['pat_i'] ) . pm_pp_olista( $n['pat_s'] ) . pm_sc_p( $n['pat_c'] )
		. pm_sc_h2( $n['h_sc'] ) . pm_sc_p( $n['sc_i'] ) . pm_pp_olista( $n['sc_s'] ) . pm_sc_p( $n['sc_c'] )
		. pm_sc_h2( $n['h_tdf'] ) . pm_sc_p( $n['tdf_i'] ) . pm_pp_olista( $n['tdf_s'] ) . pm_sc_p( $n['tdf_c'] )
		. pm_sc_h2( $n['h_ctes'] ) . pm_sc_p( $n['ctes_i'] ) . pm_pp_olista( $n['ctes_s'] ) . pm_sc_p( $n['ctes_c'] )
		. pm_sc_h2( $n['h_er'] ) . pm_sc_p( $n['er_i'] ) . pm_pp_olista( $n['er_s'] ) . pm_sc_p( $n['er_c'] )
		. pm_sc_h2( $n['h_costo'] ) . pm_sc_p( $n['costo_i'] ) . pm_tp_tabla( $n['costo_cols'], $n['costo_rows'] ) . pm_sc_p( $n['costo_c'] )
		. pm_sc_h2( $n['h_llevar'] ) . pm_sc_lista( $n['llevar_l'] )
		. pm_sc_h2( $n['h_guias'] ) . pm_sc_p( $n['guias_p'] ) . pm_sc_p( $n['cta'] )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] )
		. pm_sc_p( $n['cierre'] );
};

$enlace_ancla = [
	'es' => 'Permiso de pesca en Argentina: cómo sacarlo, paso a paso',
	'en' => 'Argentina fishing license: how to get it, step by step',
	'pt' => 'Licença de pesca na Argentina: como tirar, passo a passo',
];
$guias_permiso = [ 'san-martin-junin', 'bariloche', 'esquel-trevelin', 'jurassic-lake', 'rio-santa-cruz', 'rio-gallegos', 'rio-grande', 'goya', 'esquina', 'paso-de-la-patria', 'la-paz' ];
$destinos_enlace = [
	[
		'tipo' => 'pm_nota', 'claves' => [ 'licencias-extranjeros-argentina', 'licencia-pesca-patagonia-precios' ], 'donde' => 'parrafo',
		'ancla' => [ 'es' => '/Respuesta corta/u', 'en' => '/Short answer/u', 'pt' => '/Resposta curta/u' ],
		'texto' => [
			'es' => 'Para saber cómo se tramita, dónde y qué documentos piden los portales oficiales: %s.',
			'en' => 'To see how each permit is processed, where, and what documents the official portals ask for: %s.',
			'pt' => 'Para saber como se tira, onde e que documentos os portais oficiais pedem: %s.',
		],
	],
	[
		'tipo' => 'pm_destino', 'claves' => $guias_permiso, 'donde' => 'permiso',
		'texto' => [
			'es' => 'Cómo sacar el permiso paso a paso, con los portales oficiales: %s.',
			'en' => 'How to get the permit step by step, with the official portals: %s.',
			'pt' => 'Como tirar a licença passo a passo, com os portais oficiais: %s.',
		],
	],
];

if ( ! function_exists( 'pm_pp_olista' ) ) {
	/** Ordered list block. */
	function pm_pp_olista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">{$li}</ol>\n<!-- /wp:list -->\n\n";
	}
}

/** Resolve {g:clave}, {n:clave|texto}, {g:clave|texto}, {u:clave} and {viaje}; fails on anything left over. */
$resolver = function ( string $html, string $i ) use ( &$u ): string {
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
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\}/', fn( $m ) => esc_url( $nota( $m[1] ) ), $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\}/', fn( $m ) => esc_url( $guia( $m[1] ) ), $html );
	$html = preg_replace_callback( '/\{u:([a-z0-9_]+)\}/', function ( $m ) use ( $u ) {
		isset( $u[ $m[1] ] ) || WP_CLI::error( "enlace externo desconocido {$m[1]}" );
		return esc_url( $u[ $m[1] ] );
	}, $html );
	$html = str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) ), $html );
	if ( preg_match( '/\{(n:|g:|u:|viaje)[^}]*\}?/', $html, $m ) ) {
		WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
	}
	return $html;
};

// Cover photo: the attachment already loaded for another note (credit and per-language alt live on the attachment).
$nota_foto = pm_buscar( 'pm_nota', $foto_de, 'es' );
$att       = $nota_foto ? (int) get_post_thumbnail_id( $nota_foto->ID ) : 0;
$att || WP_CLI::error( "la nota {$foto_de} (es) no tiene foto de portada: la foto es obligatoria." );

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
	count( $n['faq'] ) >= 8 && count( $n['faq'] ) <= 10 || WP_CLI::error( "$i: la FAQ debe tener entre 8 y 10 preguntas." );
	$cuerpo = $resolver( $armar( $n ), $i );
	if ( preg_match( '/PENDIENTE|no pudimos|agencia|agency|agência|pudemos/iu', wp_strip_all_tags( $cuerpo ), $mm ) ) {
		WP_CLI::error( "$i: el texto tiene una palabra prohibida: {$mm[0]}." );
	}
	$datos   = array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] );
	$fuentes = [];
	foreach ( $fuente_nombre as $k => $f ) {
		$fuentes[] = [ 'nombre' => $f[ $i ], 'url' => $f['url'] ?? $u[ $k ] ?? $f['fn']( $i ), 'oficial' => $f['o'], 'consultada' => $f['f'] ];
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
		$campos += [ 'nota_tipo' => $nota_tipo, 'nota_fecha' => $hoy, 'nota_origen' => $origen ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		set_post_thumbnail( $id, $att );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_post( $id )->post_name === $n['slug'] && pm_clave_de( $id ) === $clave && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $datos ) && false !== strpos( $cont, 'pm-faq' ) && count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && ! preg_match( '/\{(n|g|u):|\{viaje\}/', $cont );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
$urls_nuevas = [];
if ( $aplicar ) {
	$nota_es = $existe( 'es' )[0] ?? null;
	$e       = $nota_es ? pm_nota( $nota_es->ID ) : null;
	$ok      = $e && $e['fecha'] && (int) $e['foto'] === $att;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
	$ok || WP_CLI::error( 'la nota es no lee fecha o foto como se esperaba.' );
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$x = $existe( $i )[0] ?? null;
		$x || WP_CLI::error( "falta la nota ($i) para el hreflang." );
		$urls_nuevas[ $i ] = get_permalink( $x->ID );
	}
	WP_CLI::log( 'VERIFIED: tres notas con la misma clave (hreflang es/en/pt se arma por clave): ' . implode( ' | ', $urls_nuevas ) );
	flush_rewrite_rules( false );
} else {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$urls_nuevas[ $i ] = trailingslashit( pm_url_notas( $i ) ) . $N[ $i ]['slug'] . '/';
	}
}

// --- Inbound links: one paragraph in each target (skipped when the new URL is already there) ---
foreach ( $destinos_enlace as $dest ) {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$url  = $urls_nuevas[ $i ];
		$link = pm_sc_a( $url, esc_html( $enlace_ancla[ $i ] ) );
		foreach ( $dest['claves'] as $c ) {
			$p = pm_buscar( $dest['tipo'], $c, $i );
			if ( ! $p ) {
				WP_CLI::log( "{$dest['tipo']} $c ($i): no existe, no enlazo." );
				continue;
			}
			$cont = $p->post_content;
			if ( false !== strpos( $cont, $url ) ) {
				WP_CLI::log( "{$dest['tipo']} $c ($i) #{$p->ID}: ya enlaza." );
				continue;
			}
			$pos = null;
			$via = '';
			if ( 'permiso' === $dest['donde'] ) {
				foreach ( pm_sc_encabezados( $cont ) as $k => [ $off, $txt ] ) {
					if ( preg_match( '/permis|licen[cç]|permit|licen[cs]e/iu', $txt ) ) {
						$pos = pm_sc_encabezados( $cont )[ $k + 1 ][0] ?? strlen( $cont );
						$via = 'al final de la sección del permiso';
						break;
					}
				}
			} else {
				$pos = pm_sc_pos_tras_parrafo( $cont, $dest['ancla'][ $i ] );
				$via = 'tras el párrafo ancla';
				if ( null === $pos ) {
					$pos = pm_sc_pos_tras_parrafo( $cont, '/./u' );
					$via = 'tras el primer párrafo';
				}
			}
			if ( null === $pos ) {
				WP_CLI::warning( "{$dest['tipo']} $c ($i) #{$p->ID}: sin lugar para el enlace ({$dest['donde']}); no lo agrego a ciegas." );
				continue;
			}
			WP_CLI::log( pm_sc_modo( $aplicar ) . ": {$dest['tipo']} $c ($i) #{$p->ID}: enlace $via" );
			$cambios++;
			if ( $aplicar ) {
				pm_sc_guardar( $p->ID, pm_sc_insertar( $cont, $pos, pm_sc_p( sprintf( $dest['texto'][ $i ], $link ) ) ), [ $url ], "{$dest['tipo']} $c ($i)" );
				WP_CLI::log( "VERIFIED: {$dest['tipo']} $c ($i) #{$p->ID} enlaza la página nueva" );
			}
		}
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
