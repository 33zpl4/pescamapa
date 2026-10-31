<?php
/**
 * Note "Fly fishing Argentina on a budget" (English), 31/10/2026: idea 1 of docs/ideas-contenido-seo-2026-10-31.md. Keywords:
 * `fly fishing argentina on a budget`, `fly fishing in argentina without a guide`. Built only from what we read ourselves:
 *  - permit values: Diario 7 Lagos (16/10/2026, reports the provincial values) and the Río Negro portal (30/10/2026);
 *  - Neuquén fauna director (Neuquén Informa, 28/10/2026): fines, rivers that rise overnight, the new office at the mouth of the Chimehuín;
 *  - 2026/27 annexes (Neuquén, Río Negro, National Parks) read on 30/10/2026: validity, felt soles, bridges and dams, unauthorised guides;
 *  - Civil and Commercial Code arts. 235 and 1974 (argentina.gob.ar, read 31/10/2026);
 *  - day rates of four operators (their own pages, read 31/10/2026) and the ferry of Villa Llanquín (Vialidad Rionegrina, read 31/10/2026).
 * It deliberately has NO trip total, no car/cabin/fuel/grocery prices and no named access points: they wait for a sourced research
 * round (docs/investigacion/diy-patagonia-directiva-y-auditorias-2026-10-31.md, line 2). The general articles of the Reglamento
 * (felt soles art. 22, imported gear arts. 27-28) were NOT read: the note says so. The Limay reference price of our packages page
 * (USD 550 / 650) is NOT quoted here: it sits above the operators' own rates (USD 520-550 per boat), see the note to the owner.
 *
 * English only for now (as the golden dorado pillar): pm_nota() reads its type/date from the post itself when there is no Spanish one.
 * Cover photo: the existing attachment "junin-pescador-volcan.jpg" (credit Chad Shmukler).
 * Idempotent: an existing note with this clave in English is never touched. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = nota-por-tu-cuenta-en-2026-10-31).
 */

$aplicar = true;
$hoy     = '20261031';
$clave   = 'patagonia-por-tu-cuenta';

function pm_dy_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_dy_h2( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_dy_lista( array $items, bool $ordenada = false ): string {
	$li  = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	$tag = $ordenada ? 'ol' : 'ul';
	$att = $ordenada ? ' {"ordered":true}' : '';
	return "<!-- wp:list{$att} -->\n<{$tag} class=\"wp-block-list\">{$li}</{$tag}>\n<!-- /wp:list -->";
}
function pm_dy_tabla( array $cabecera, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
function pm_dy_caja( string $titulo, array $items, string $pie ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph {\"className\":\"note\"} -->\n<p class=\"note\">{$pie}</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->";
}
function pm_dy_destino( string $c, string $nombre ): string {
	$p = pm_buscar( 'pm_destino', $c, 'en' ) ?? pm_buscar( 'pm_destino', $c, 'es' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $nombre ) . '</a>' : esc_html( $nombre );
}
function pm_dy_nota( string $c, string $texto ): string {
	$p = pm_buscar( 'pm_nota', $c, 'en' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $texto ) . '</a>' : esc_html( $texto );
}

$n = [
	'titulo'          => 'Fly fishing Argentina on a budget: how to fish Patagonia without a lodge',
	'slug'            => 'fly-fishing-argentina-on-a-budget',
	'bajada'          => 'What a do-it-yourself Patagonia trip needs: the 2026/27 permit and what it costs, the rules that shape your gear, how access to public water really works and what a day with a guide costs. Sourced and dated.',
	'seo_title'       => 'Fly fishing Argentina on a budget: the DIY guide',
	'seo_description' => 'Fish Patagonia without a lodge: the 2026/27 permit and its cost, gear rules, public water access and what a day with a guide costs, with sources.',
	'datos'           => [
		'A non-resident permit for 2026/27 costs ARS 50,000 a day, ARS 140,000 for 10 days or ARS 280,000 for the season in Neuquén and Río Negro (permit valid in Río Negro, Neuquén, Chubut and the National Parks).',
		'Preferential zones need an extra permit (ARS 12,500 a day, 25,000 for 10 days, 50,000 for the season, as announced by Neuquén).',
		'Felt-soled boots are banned in all Río Negro waters and in each National Park water that lists the ban: do not bring felt anywhere in Patagonia.',
		'A full day with a guide and a boat on the Limay is published at USD 520 to 550 per boat for one or two anglers; the licence is usually extra.',
		'A river being public does not give you the right to cross private land to reach it; the legal way in is a public road, a bridge approach, a boat ramp or the owner\'s permission.',
	],
];

$cuerpo = implode( "\n\n", [
	pm_dy_p( '<strong>Short answer.</strong> Yes, you can fish Patagonia without a lodge. The fixed costs we can source today are the <strong>permit</strong> (ARS 140,000 for 10 days for a non-resident in Neuquén and Río Negro in 2026/27) and, if you want a boat, <strong>a day with a local guide</strong> (USD 520 to 550 per boat for one or two anglers on the Limay, as four operators publish it). We have not priced the car, the cabins, fuel or groceries with a source yet, so this guide gives <strong>no trip total</strong>: it tells you what is certain and what is still open.' ),
	pm_dy_h2( 'What does a trip on your own cost?' ),
	pm_dy_p( 'This is what we can source, with the date we read it. Everything else is left out on purpose.' ),
	pm_dy_tabla( [ 'Item', 'What we found', 'Source' ], [
		[ '<strong>Fishing permit, non-resident</strong>', 'ARS 50,000 a day, ARS 140,000 for 10 days, ARS 280,000 for the season, per angler', 'Neuquén values reported on 16 October 2026; Río Negro portal read on 30 October 2026' ],
		[ '<strong>Extra permit, preferential zones</strong>', 'ARS 12,500 a day, ARS 25,000 for 10 days, ARS 50,000 for the season', 'Neuquén values reported on 16 October 2026' ],
		[ '<strong>Guide and boat, full day, 1 or 2 anglers</strong>', 'USD 520 to 550 per boat', 'Four operators, rates read on 31 October 2026 (table below)' ],
		[ '<strong>Car, cabin for two, fuel, groceries</strong>', 'Not priced yet', 'We add them when we have a source and a date' ],
	] ),
	pm_dy_p( '<strong>A worked example.</strong> Two anglers on a ten-day trip with one day on a boat: two 10-day permits are 2 × ARS 140,000 = ARS 280,000, and the guided day is USD 520 to 550 for the boat. We do not convert pesos to dollars, because the rate moves: use the day\'s rate when you plan.' ),
	pm_dy_p( 'Ask every guide whether the permit is included. Of the four operators below, three list the licence as not included, and two of those offer to arrange it for USD 35 to 40. The fourth does not say on its page.' ),
	pm_dy_h2( 'The permit: what it covers and where to buy it' ),
	pm_dy_lista( [
		'<strong>One permit, several jurisdictions.</strong> According to the 2026/27 annexes of Neuquén and Río Negro, the Patagonian permit is valid in Río Negro, Neuquén, Chubut and the National Parks.',
		'<strong>Where to buy.</strong> In Neuquén, on the official Caza y Pesca platform (<a href="https://cazaypesca.neuquen.gob.ar/permisos/" rel="noopener">cazaypesca.neuquen.gob.ar/permisos</a>). On the Bariloche side, from the Mesa Directiva de Pesca Deportiva Zona Andina (<a href="https://www.permisosdepesca.com.ar/" rel="noopener">permisosdepesca.com.ar</a>). Non-residents need their passport.',
		'<strong>Extras.</strong> Some zones need an extra permit, for example parts of the Chimehuín and the Correntoso (see our {junin} guide). Trolling has its own add-on: ARS 25,000 a day, ARS 50,000 for 10 days, ARS 100,000 for the season.',
		'<strong>Season.</strong> It starts on 1 November 2026. Some waters close earlier: Neuquén says certain lagoons and rivers end on 1 May 2027. Read the entry of each water on the portal before you plan, and see {trucha} for the months by river.',
		'<strong>Buy it on the provincial portals.</strong> If a guide or a lodge sells the permit inside a package, compare the price with the table above.',
	] ),
	pm_dy_h2( 'The rules that shape what you pack' ),
	pm_dy_lista( [
		'<strong>No felt soles.</strong> Felt-soled wading boots are banned in all waters under Río Negro jurisdiction, and in the National Park waters whose entry says so (each one repeats the ban). The Neuquén annex we read does not mention felt, and we have not read the general articles of the Reglamento. The safe rule is not to bring felt anywhere in Patagonia: rubber soles, with studs if you like.',
		'<strong>Barbless flies.</strong> Several waters we read require a single barbless hook or a crushed barb, for example Lago Traful. Tie or buy flies with the barb crushed and you are covered.',
		'<strong>Clean, dry gear.</strong> The Reglamento has articles on equipment coming from abroad and on disinfection that we have not read yet. Bring waders and boots that are clean and completely dry, and ask at the permit office or your guide.',
		'<strong>Fly only, and release.</strong> Many waters are fly-only and catch and release. The Upper Limay inside the Nahuel Huapi National Park is one: boats without a motor, fly only and release from the lake to Rincón Chico. Each water has its own entry; see {bariloche} and {junin}.',
		'<strong>Neuquén: dams, bridges and guides.</strong> You may not fish within 500 m of a dam or from a bridge or footbridge, and hiring a guide who is not authorised is an offence. On 28 October 2026 the head of Neuquén\'s wildlife service said the minimum fine is now ARS 1,800,000, up to ARS 390 million.',
		'<strong>Rivers rise overnight.</strong> The same official warned that rain in the mountains can raise a river from one day to the next, even upstream of where you stand. Check the forecast and the river before you wade.',
	] ),
	pm_dy_h2( 'Public water, private land: three questions' ),
	pm_dy_p( 'A river can be public while its bank is private. Before you drive an hour to a spot, answer three questions.' ),
	pm_dy_lista( [
		'<strong>Is this water open to fishing on your dates?</strong> Each river and lake has an entry in the Reglamento with its season, method and limits.',
		'<strong>Who owns the bank?</strong> The Civil and Commercial Code puts rivers in the public domain, including their water, beaches and bed up to the line of ordinary high water (art. 235). It also says that the owner of land next to a waterway fit for transport by water must leave a 15 m strip free and may do nothing there that impairs that activity (art. 1974, the <em>camino de sirga</em>). That protects navigation. We have not found in these articles a general right for anglers to cross private land, so do not treat them as one. Whether a given river counts as fit for transport by water is a legal question: ask a local guide or the tourist office. This is not legal advice.',
		'<strong>What is the legal way in?</strong> A public road, a bridge approach, a boat ramp or the landowner\'s permission. The National Parks annex names the public access to the Traful River: the confluence with the Minero River.',
	], true ),
	pm_dy_p( 'We do not list access points we have not verified. When we have a source and a date for a public ramp or approach, it goes in the destination guide.' ),
	pm_dy_h2( 'When a day with a guide is worth it' ),
	pm_dy_p( 'Some water is made for a boat. On the Upper Limay boats without a motor are the way in, and a drift boat covers stretches you cannot reach on foot. Many independent anglers plan their own trip and add one or two days with a guide.' ),
	pm_dy_tabla( [ 'Operator', 'Full day', 'What the page says' ], [
		[ '<a href="https://troutbariloche.com/day-trips/" rel="noopener">Trout Bariloche</a>', 'USD 520', '1 or 2 anglers; an extra angler aged 11 or older pays USD 60; licence USD 40 on request' ],
		[ '<a href="https://southernriverspatagonia.com/limay-full-day-fly-fishing" rel="noopener">Southern Rivers Patagonia</a>', 'USD 530', 'Total for 1 or 2 anglers; fishing licence and gratuities not included' ],
		[ '<a href="https://www.patagoniaintheloop.com/" rel="noopener">Patagonia in the Loop</a>', 'USD 550', '1 or 2 anglers, 9 hours; half day USD 430; licence USD 35 on request' ],
		[ '<a href="https://www.patagoniadrift.com.ar/en/fly-fishing-day-trips/" rel="noopener">Patagonia Drift Fly Fishing</a>', 'USD 550', '1 or 2 anglers; full-day Limay float' ],
	] ),
	pm_dy_p( 'These are the rates the operators publish, read on 31 October 2026. They are not quotes for your dates and we have not fished with them, so this is not a recommendation. In Neuquén the province keeps an official list of authorised guides and agencies at <a href="https://turismo.neuquen.gob.ar/actividades/?turapi_prestacion=Pesca" rel="noopener">turismo.neuquen.gob.ar</a>. If a rate has changed, tell us in the form below and we will update this note.' ),
	pm_dy_p( '<strong>Want a road trip with a day or two on a boat?</strong> Tell us your dates, how many of you are going and your experience in <a href="{viaje}">the trip form</a>. We pass the request to a local guide and write back with options, at no cost or commitment.' ),
	pm_dy_h2( 'Getting around' ),
	pm_dy_p( 'One practical detail: the Villa Llanquín cable ferry carries vehicles across the Limay between Route 40 and Villa Llanquín. It is free and runs from 8:00 to 20:00, according to Río Negro\'s road agency (<a href="https://gobierno.rionegro.gov.ar/servicio/213/balsas" rel="noopener">Vialidad Rionegrina</a>). That page says nothing about fishing access there.' ),
	pm_dy_caja( 'Up to date · 2026/27 season, 31 October 2026', [
		'The season starts on 1 November 2026. Permit values are the ones in the table above; Neuquén sells them online since 15 October. For the preferential-zone extra, the annex text we read shows ARS 10,000, 20,000 and 40,000, while Neuquén announced ARS 12,500, 25,000 and 50,000: confirm the amount when you buy.',
		'Neuquén\'s wildlife service opens an office next to the mouth of the Chimehuín on 3 November 2026, after fifteen years without a presence there (<a href="https://www.neuqueninforma.gob.ar/noticias/2026/10/28/266319-neuquen-abre-la-temporada-de-pesca-con-una-nueva-oficina-a-la-vera-del-rio-chimehuin" rel="noopener">Neuquén Informa</a>).',
		'The operators\' rates are the ones published on 31 October 2026 and can change.',
	], 'This box is updated once a year; the rest of the guide does not change.' ),
	pm_dy_h2( 'What this guide does not cover yet' ),
	pm_dy_p( 'Car rental (4x2 or 4x4 on gravel roads), cabins for two in Junín de los Andes, San Martín de los Andes and Dina Huapi, fuel prices, groceries, tackle recommendations, the disinfection stations and named public access points are not here because we have not verified them with a source and a date. We add them when we do, and then the trip total.' ),
	pm_dy_p( '<em>We have not fished with the operators or people cited here. This guide summarises public information and our own pages; the interpretation is ours and may contain errors.</em>' ),
] );

$f = fn( $nombre, $url, $ofi = 0, $fecha = null ) => [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $ofi, 'consultada' => $fecha ?? $hoy ];
$fuentes = [
	$f( 'Diario 7 Lagos: the season starts on 1 November and permits are on sale (16/10/2026; reports the provincial values)', 'https://www.diario7lagos.com.ar/noticias/2026/10/16/80723-la-temporada-de-pesca-comienza-el-1-de-noviembre-y-ya-se-pueden-comprar-permisos' ),
	$f( 'Neuquén Informa: new office at the mouth of the Chimehuín, fines and river warnings (28/10/2026)', 'https://www.neuqueninforma.gob.ar/noticias/2026/10/28/266319-neuquen-abre-la-temporada-de-pesca-con-una-nueva-oficina-a-la-vera-del-rio-chimehuin', 1 ),
	$f( 'Permisos de Pesca: Mesa Directiva de Pesca Deportiva Zona Andina (Río Negro)', 'https://www.permisosdepesca.com.ar/', 1, '20261030' ),
	$f( 'Reglamento 2026/27: Annex Neuquén', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/', 1, '20261030' ),
	$f( 'Reglamento 2026/27: Annex Río Negro', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-rio-negro/', 1, '20261030' ),
	$f( 'Reglamento 2026/27: National Parks waters', 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/', 1, '20261030' ),
	$f( 'AICACYP: Reglamento 2026/27, National Parks waters (copy, PDF)', 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Parques-Nacionales.pdf', 0, '20261030' ),
	$f( 'Civil and Commercial Code (Ley 26.994), arts. 235 and 1974', 'https://www.argentina.gob.ar/normativa/nacional/ley-26994-235975/actualizacion', 1 ),
	$f( 'Turismo Neuquén: official list of fishing providers', 'https://turismo.neuquen.gob.ar/actividades/?turapi_prestacion=Pesca', 1 ),
	$f( 'Vialidad Rionegrina: ferries (Villa Llanquín)', 'https://gobierno.rionegro.gov.ar/servicio/213/balsas', 1 ),
	$f( 'Trout Bariloche: day trips and rates', 'https://troutbariloche.com/day-trips/' ),
	$f( 'Southern Rivers Patagonia: Limay full-day trip', 'https://southernriverspatagonia.com/limay-full-day-fly-fishing' ),
	$f( 'Patagonia in the Loop: rates', 'https://www.patagoniaintheloop.com/' ),
	$f( 'Patagonia Drift Fly Fishing: day trips', 'https://www.patagoniadrift.com.ar/en/fly-fishing-day-trips/' ),
];

// Guards: SEO lengths, and the pieces this page links to must exist (a link to nothing is worse than none).
$tl = mb_strlen( $n['seo_title'] );
$dl = mb_strlen( $n['seo_description'] );
if ( $tl > 60 || $dl < 70 || $dl > 160 ) {
	WP_CLI::error( "SEO fuera de rango: title $tl / description $dl" );
}
$ya = array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === 'en' );
if ( $ya ) {
	WP_CLI::log( "$clave (en) ya existe: no toco nada." );
	return;
}
$faltan = [];
pm_buscar( 'pm_nota', 'cuando-pescar-trucha-patagonia', 'en' ) || $faltan[] = 'nota cuando-pescar-trucha-patagonia (en)';
foreach ( [ 'bariloche', 'san-martin-junin' ] as $c ) {
	pm_buscar( 'pm_destino', $c, 'en' ) || $faltan[] = "guía $c (en)";
}
$ya_foto = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => 'junin-pescador-volcan.jpg', 'numberposts' => 1, 'fields' => 'ids' ] );
$att     = $ya_foto ? (int) $ya_foto[0] : 0;
if ( ! $att ) {
	$faltan[] = 'foto junin-pescador-volcan.jpg (no está cargada)';
}
$enlaces = [
	'{bariloche}' => pm_dy_destino( 'bariloche', 'our Bariloche guide' ),
	'{junin}'     => pm_dy_destino( 'san-martin-junin', 'San Martín and Junín de los Andes' ),
	'{trucha}'    => pm_dy_nota( 'cuando-pescar-trucha-patagonia', 'our note on when to fish trout in Patagonia' ),
	'{viaje}'     => esc_url( ( function_exists( 'pm_url_viaje' ) ? pm_url_viaje( 'en' ) : home_url( '/en/plan-a-trip/' ) ) . '?destino=bariloche&especie=trucha' ),
];
$cuerpo = strtr( $cuerpo, $enlaces );
if ( preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) ) {
	WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
}
if ( $faltan ) {
	WP_CLI::warning( 'Faltan: ' . implode( ', ', $faltan ) );
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave (en) «{$n['titulo']}» /{$n['slug']}/ — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., ' . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . " fuentes; title $tl / description $dl car.; foto #" . ( $att ?: 'NINGUNA' ) );
if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado.' );
	return;
}
if ( ! $att ) {
	WP_CLI::error( 'la foto de portada no está cargada: no se publica sin foto.' );
}

$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}
$campos = [
	'clave' => $clave, 'idioma' => 'en', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
	'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ),
	'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'],
	'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_especie' => 'trucha',
	'nota_origen' => 'Compilada el 31/10/2026 solo con lo que leímos nosotros (valores de permisos en prensa provincial y portal de Río Negro, anexos 2026/27, arts. 235 y 1974 del CCyC, tarifas de cuatro operadores en sus páginas). Sin presupuesto total, sin precios de auto, cabañas, combustible ni supermercado, sin accesos con nombre: esperan la ronda de logística (docs/investigacion/diy-patagonia-directiva-y-auditorias-2026-10-31.md). Los artículos generales del reglamento (fieltro art. 22, equipos importados arts. 27-28) no se leyeron: la nota lo dice. Solo en inglés por ahora; versión en español después.',
];
foreach ( $campos as $k => $v ) {
	update_field( $k, $v, $id );
}
set_post_thumbnail( $id, $att );
$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'caja-anio' ) && (int) get_post_thumbnail_id( $id ) === $att && 'Chad Shmukler' === pm_campo( 'foto_autor', $att );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave (en) #$id foto #$att " . get_permalink( $id ) );
$ok || WP_CLI::error( "$clave (en): la lectura de vuelta no coincide." );
flush_rewrite_rules( false );
WP_CLI::success( 'Nota creada.' );
