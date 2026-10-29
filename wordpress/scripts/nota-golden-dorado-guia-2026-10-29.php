<?php
/**
 * Pillar guide "Golden dorado fishing in Argentina" (English), 29/10/2026. Why: the English keyword export
 * (docs/seo-palabras-clave-en-2026-10-29.md, priority 1) shows ~500/month searches for golden dorado trips and we had the pieces but no page
 * that joins them. This page only summarises and links what our own pages already verified (season note, Paso de la Patria / Esquina /
 * Goya guides, size-and-bag-limits note, Corrientes Disposición 853/22). It deliberately has NO methods-by-stretch and NO prices: those
 * wait for research round R1/R5 and a dated source.
 *
 * English only for now (no Spanish source note): pm_nota() reads its type/date from the post itself when there is no Spanish one.
 * Idempotent: an existing note with this clave in English is never touched. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = nota-golden-dorado-guia-2026-10-29). Run AFTER paso-de-la-patria-en-2026-10-29 so {paso} links the English guide.
 */

$aplicar = false;
$hoy     = '20261029';
$clave   = 'golden-dorado-guia';

function pm_g_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_g_h2( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_g_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_g_tabla( array $cabecera, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
function pm_g_caja( string $titulo, array $items, string $pie ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph -->\n<p><em>{$pie}</em></p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->";
}
/** Link to a destination guide in English (else the Spanish one); plain name if there is none. */
function pm_g_destino( string $c, string $nombre ): string {
	$p = pm_buscar( 'pm_destino', $c, 'en' ) ?? pm_buscar( 'pm_destino', $c, 'es' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $nombre ) . '</a>' : esc_html( $nombre );
}
function pm_g_nota( string $c, string $texto ): string {
	$p = pm_buscar( 'pm_nota', $c, 'en' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $texto ) . '</a>' : esc_html( $texto );
}

$n = [
	'titulo'          => 'Golden dorado fishing in Argentina: where to go, when, the rules and how to plan',
	'slug'            => 'golden-dorado-fishing-argentina',
	'bajada'          => 'Where golden dorado are fished on the Paraná, the months operators repeat, the catch-and-release rule in Corrientes, the licence and how to plan the trip. Sourced and dated.',
	'seo_title'       => 'Golden dorado fishing in Argentina: a planning guide',
	'seo_description' => 'Golden dorado on the Paraná: where to fish, best months, catch-and-release rules in Corrientes, the licence and how to plan the trip, with sources.',
	'datos'           => [
		'Golden dorado (Salminus brasiliensis) is fished on the Paraná River; our guides cover the Upper Paraná (Paso de la Patria) and the middle Paraná (Goya, Esquina, La Paz).',
		'Operators on the Upper Paraná in Corrientes repeat October–November and March–May as the best months; some put the peak earlier, from June to October.',
		'In Corrientes golden dorado and surubí are catch and release only (Disposición 853/22, Official Gazette, 26 December 2022).',
		'Visitors from abroad need the Corrientes sport fishing licence, bought online on the provincial Natural Resources portal; we found no rule recognising licences from other countries.',
		'Extraordinary closures change from year to year: check the current ones with the local authority or your guide before you go.',
	],
];

$cuerpo = implode( "\n\n", [
	pm_g_p( '<strong>Short answer.</strong> Golden dorado is the fish that brings most anglers from abroad to the Paraná River in Corrientes. The months operators repeat most are <strong>October–November</strong> and <strong>March–May</strong> on the Upper Paraná; you need the <strong>Corrientes sport fishing licence</strong>; and in Corrientes the dorado is <strong>catch and release only</strong>. Below is where to go, when, which rules apply and how to plan, with a source and a date for each fact.' ),
	pm_g_h2( 'Where do you fish golden dorado in Argentina?' ),
	pm_g_p( 'Our guides cover two stretches of the Paraná. The choice depends on the kind of trip you want.' ),
	pm_g_tabla( [ 'Stretch', 'Places', 'What to read next' ], [
		[ '<strong>Upper Paraná</strong>', 'Itatí, Ituzaingó, Paso de la Patria', 'Clear water and, according to a Flylords film we cite in our season note, the best potential for big fish. Paso de la Patria is the home of the National Dorado Festival: see our {paso} guide.' ],
		[ '<strong>Middle Paraná</strong>', 'Goya, Esquina, La Paz', 'More side channels and islands. We found no source with months for these stretches, so ask a local guide: see the {goya} and {esquina} guides.' ],
	] ),
	pm_g_p( 'Dorado is also fished outside Corrientes, but every province has its own rules and we only publish what we verified. For the Paraná in Corrientes we have the rules below.' ),
	pm_g_h2( 'When is the best time?' ),
	pm_g_p( 'The windows differ by operator and by stretch, so we do not give a single answer. On the Upper Paraná in Corrientes the sources we checked repeat <strong>October to November</strong> and <strong>late March to May</strong>; some operators put the peak earlier, from June to October, following the baitfish. The heat of January and February slows daytime fishing. Outside the window you can still fish, with a different technique and lower expectations.' ),
	pm_g_p( 'Every source, table and caveat is in {estacion}.' ),
	pm_g_h2( 'What are the rules in Corrientes?' ),
	pm_g_lista( [
		'<strong>Golden dorado and surubí: catch and release only.</strong> You cannot keep them. Source: <a href="https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf" rel="noopener">Disposición 853/22</a> of Recursos Naturales (Official Gazette of Corrientes, 26 December 2022; in force until another act replaces it).',
		'Other species have minimum sizes and bag limits (pacú, patí, boga, tararira). They are in our table: {tallas}.',
		'Extraordinary closures can be ordered on stretches of the river and they change every year (see the box below).',
	] ),
	pm_g_caja( 'Up to date · closures and festivals, 29 October 2026', [
		'Since 9 September 2026 an extraordinary closure applies to a stretch of the Paraná between km 1109 and 1150, ordered by the Dirección de Recursos Naturales of Corrientes to protect an exceptional concentration of golden dorado. Press reports give no end date (<a href="https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana" rel="noopener">El Litoral</a>, <a href="https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml" rel="noopener">Perfil</a>, in Spanish). Confirm it is still in force before you go.',
		'National Dorado Festival, Paso de la Patria: in 2026 it was the 61st edition, 14 to 17 August, catch and release (<a href="https://pasodelapatria.gob.ar/noticias/fiesta-nacional-del-dorado-un-exito-total" rel="noopener">Municipality</a>). The season-opening Torneo Apertura ran from 2 to 5 April 2026. The dates of the next editions have not been published.',
		'There is no consolidated official calendar of closures by province. A local guide always knows which stretches are open.',
	], 'This box is updated once a year; the rest of the guide does not change.' ),
	pm_g_h2( 'Which licence do you need?' ),
	pm_g_p( 'The <strong>Corrientes sport fishing licence</strong>, bought online on the Natural Resources portal and carried with your identity document (<a href="https://corrientes.tur.ar/tramites-y-permisos/" rel="noopener">Corrientes Tourism Ministry: procedures and permits</a>). The regional recognition agreement (Santa Fe, Entre Ríos and Chaco) is for residents of those provinces and does not cover stretches under exclusively Corrientes jurisdiction (<a href="https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/" rel="noopener">agreement</a>). We found no rule recognising licences from other countries: visitors from abroad need the Corrientes one. The price and duration of each category are shown when you apply.' ),
	pm_g_h2( 'How do you plan the trip?' ),
	pm_g_lista( [
		'<strong>Pick the stretch and the month</strong> from the two sections above; ask the guide for the calendar and how far ahead you need to book.',
		'<strong>The festival weeks fill up.</strong> Around the National Dorado Festival in August, lodging in Paso de la Patria is in high demand: book early.',
		'<strong>Check the closures</strong> for your dates with your guide or Recursos Naturales.',
		'<strong>Carry your licence and your identity document</strong> on the boat.',
	] ),
	pm_g_p( '<strong>Want us to help plan it?</strong> Tell us your dates, group size and what you are after in <a href="{viaje}">the trip form</a> and we will write back with guide and lodging options, at no cost or commitment.' ),
	pm_g_h2( 'What this guide does not cover yet' ),
	pm_g_p( 'Methods by stretch (fly, lures, bait), published daily rates and the Delta near Buenos Aires are not here because we have not verified them with a source and a date. We add them when we do.' ),
	pm_g_p( '<em>We have not fished with the operators or people cited in the linked notes. This guide summarises public information and our own pages; the interpretation is ours and may contain errors.</em>' ),
] );

$f = fn( $nombre, $url, $ofi = 0 ) => [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $ofi, 'consultada' => $hoy ];
$fuentes = [
	$f( 'Disposición 853/22, Recursos Naturales de Corrientes (Official Gazette, 26/12/2022)', 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf', 1 ),
	$f( 'Corrientes Tourism Ministry: procedures and permits (fishing licence)', 'https://corrientes.tur.ar/tramites-y-permisos/', 1 ),
	$f( 'Corrientes Tourism Ministry: regional licence agreement (scope)', 'https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/', 1 ),
	$f( 'Municipality of Paso de la Patria: 61st National Dorado Festival', 'https://pasodelapatria.gob.ar/noticias/fiesta-nacional-del-dorado-un-exito-total', 1 ),
	$f( 'El Litoral: extraordinary closure to protect a dorado shoal (9/9/2026)', 'https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana' ),
	$f( 'Perfil: extraordinary closure on the Paraná (dorado concentration)', 'https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml' ),
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
foreach ( [ 'cuando-pescar-dorado', 'tallas-cupos-vedas' ] as $c ) {
	pm_buscar( 'pm_nota', $c, 'en' ) || $faltan[] = "nota $c (en)";
}
foreach ( [ 'paso-de-la-patria', 'goya', 'esquina' ] as $c ) {
	pm_buscar( 'pm_destino', $c, 'en' ) || $faltan[] = "guía $c (en)";
}
$enlaces = [
	'{paso}'     => pm_g_destino( 'paso-de-la-patria', 'Paso de la Patria' ),
	'{goya}'     => pm_g_destino( 'goya', 'Goya' ),
	'{esquina}'  => pm_g_destino( 'esquina', 'Esquina' ),
	'{estacion}' => pm_g_nota( 'cuando-pescar-dorado', 'our season note “When to fish golden dorado in Argentina”' ),
	'{tallas}'   => pm_g_nota( 'tallas-cupos-vedas', 'size, bag limits and closed seasons' ),
	'{viaje}'    => esc_url( ( function_exists( 'pm_url_viaje' ) ? pm_url_viaje( 'en' ) : home_url( '/en/plan-a-trip/' ) ) . '?destino=paso-de-la-patria&especie=dorado' ),
];
$cuerpo = strtr( $cuerpo, $enlaces );
if ( preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) ) {
	WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
}
if ( $faltan ) {
	WP_CLI::warning( 'No existen en inglés (el enlace cae al español o al nombre): ' . implode( ', ', $faltan ) );
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave (en) «{$n['titulo']}» /{$n['slug']}/ — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., ' . count( $n['datos'] ) . " datos, " . count( $fuentes ) . " fuentes; title $tl / description $dl car." );
if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado.' );
	return;
}

$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}
$campos = [
	'clave' => $clave, 'idioma' => 'en', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
	'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ),
	'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'],
	'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_especie' => 'dorado',
	'nota_origen' => 'Compilada el 29/10/2026 solo con lo que ya verificaron nuestras páginas (nota de temporada del dorado, guías de Paso de la Patria/Esquina/Goya, Disposición 853/22). Sin métodos por tramo ni precios: esperan la ronda R1/R5 de docs/investigacion/rondas-ingles.md. Solo en inglés por ahora; versión en español y portugués después.',
];
foreach ( $campos as $k => $v ) {
	update_field( $k, $v, $id );
}
$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'caja-anio' );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave (en) #$id " . get_permalink( $id ) );
$ok || WP_CLI::error( "$clave (en): la lectura de vuelta no coincide." );
flush_rewrite_rules( false );
WP_CLI::success( 'Nota creada.' );
