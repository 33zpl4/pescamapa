<?php
/**
 * La Paz (Entre Ríos): English version of the Spanish guide, 29/10/2026. Why: English speakers search "golden dorado fishing
 * argentina" (docs/seo-palabras-clave-en-2026-10-29.md) and La Paz, home of the Fiesta Provincial del Dorado Entrerriano, had Spanish
 * and Portuguese only. Faithful translation of the LIVE Spanish guide as read on 29/10/2026 (leer-la-paz-2026-10-29: base text of
 * la-paz-2026-10-22 plus the festival and 2026 fee patches): same figures, same sources, same hidden PENDIENTE notes; it adds no
 * data. The only addition for English search is "golden dorado" in the H1, title and description. Shared fields (region, province,
 * location, species, river station, photo) are inherited from the Spanish guide; the outfitter ("listado con información pública")
 * belongs to the destination, so it shows up here too.
 *
 * Idempotent: if the English guide exists it is not touched. Dry run first ($aplicar = false). Run with wp-script.yml
 * (script = la-paz-en-2026-10-29). One wp-script at a time.
 */

$aplicar = false;
$hoy     = '20261022'; // the date of the fees table and of the sources, not today's

function pm_lpe_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_lpe_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_lpe_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_lpe_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

// Same sources as the Spanish guide (names translated, URLs unchanged).
$fuentes = [
	[ 'Entre Ríos Natural Resources and Enforcement Directorate: hunting and fishing permits and licences table (fees)', 'https://www.entrerios.gov.ar/', 1 ],
	[ 'Government of Entre Ríos: how to get the sport fishing licence or permit', 'https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva', 1 ],
	[ 'Elonce: the province updated hunting and fishing fees (29/05/2026)', 'https://www.elonce.com/uid-875603', 0 ],
	[ 'Government of Santa Fe: Res. 0336/25, regional validity of sport fishing licences', 'https://www.santafe.gob.ar/ms/ambiente/wp-content/uploads/sites/93/2026/03/RES.-0336-25-VALIDEZ-REGIONAL-LICENCIAS-PESC-DEPORTIVA.pdf', 1 ],
	[ 'Turismo La Paz (Entre Ríos)', 'https://lapazentrerios.tur.ar/', 1 ],
	[ 'Municipality of La Paz (Entre Ríos)', 'https://lapaz.gob.ar/', 1 ],
	[ 'INA: hydrological alert system (Paraná level)', 'https://alerta.ina.gob.ar/', 1 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$cuerpo = implode( "\n\n", [
	pm_lpe_p( 'La Paz is the seat of La Paz department, in <strong>Entre Ríos</strong>, on the bank of the <strong>Paraná River</strong>. It is a different province from Corrientes: the rules and the licence are those of Entre Ríos. The most sought-after species are the <strong>golden dorado</strong> and the <strong>surubí</strong>.' ),
	pm_lpe_pendiente( 'confirm the species, the distances and the river stretch with an open official source.' ),
	pm_lpe_h( 'Fishing licence in Entre Ríos' ),
	pm_lpe_p( 'You need a sport fishing licence to fish in Entre Ríos. According to the permits and licences table of the <strong>Entre Ríos Natural Resources and Enforcement Directorate</strong> (consulted on 22/10/2026), the fees are:' ),
	pm_lpe_lista( [
		'Annual licence for Entre Ríos residents: <strong>$15,444.44</strong> (Argentine pesos).',
		'Annual licence for residents of other provinces: <strong>$24,251.60</strong>.',
		'4-day temporary permit for residents of other provinces: <strong>$5,488.52</strong>.',
		'Permit for foreigners (5 days): <strong>$24,251.60</strong>.',
	] ),
	pm_lpe_p( 'Fees are adjusted, and according to the press the province last updated them on 29 May 2026: confirm the amount when you apply for the licence through the <a href="https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva" rel="noopener">Government of Entre Ríos procedure</a>. There is also a regional agreement for reciprocal recognition of licences in the Litoral that includes Entre Ríos (Resolution 0336/25 of Santa Fe): if you hold a licence from another province in the region, confirm the conditions with the Directorate before you travel.' ),
	pm_lpe_h( 'Sizes, bag limits and closed seasons' ),
	pm_lpe_p( 'We could not confirm online the current resolution on <strong>minimum sizes, bag limits and closed seasons</strong> in Entre Ríos, so we do not publish them. Before you go, ask the Entre Ríos Natural Resources and Enforcement Directorate or your guide, who knows what applies that year. Spawning closures and extraordinary closures change every year.' ),
	pm_lpe_pendiente( 'current resolution on sizes, bag limits and closed seasons (is 276/2007 still in force?): ask the Directorate, or Gemini with a link.' ),
	pm_lpe_h( 'Festivals and tournaments' ),
	pm_lpe_p( 'The <strong>Fiesta Provincial del Dorado Entrerriano</strong> (Entre Ríos Provincial Dorado Festival) stands out: in 2026 it was its 33rd edition, on 25 and 26 September, with the 12th lure fishing meet (on the 25th) and the 33rd bait fishing meet (on the 26th). The 2026 entry fee was $550,000 per modality if paid by 31/08 (in up to 3 instalments) and $600,000 after that date ($800,000 for both modalities), according to the official poster. La Paz also has other festivals and tournaments. The dates of the next edition have not been published yet: they are announced by the <a href="https://lapaz.gob.ar/" rel="noopener">Municipality of La Paz</a> and <a href="https://lapazentrerios.tur.ar/" rel="noopener">Turismo La Paz</a>.' ),
	pm_lpe_h( 'Guides and lodging' ),
	pm_lpe_p( 'Below is the guide we found with public information; we have not verified it with the guide yet, which is why it is marked.' ),
	pm_lpe_h( 'Getting there' ),
	pm_lpe_p( 'La Paz is on <strong>national route 12</strong>, north of Paraná. To go out in your own boat you need the nautical permit and clearance from the Prefectura. The river level is checked in the INA alert system.' ),
] );

$campos = [
	'idioma'      => 'en',
	'clave'       => 'la-paz',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'fuentes'     => $fuentes_filas,
	'reglamento_autoridad' => 'Dirección de Recursos Naturales y Fiscalización de Entre Ríos',
	'reglamento_url'       => 'https://www.entrerios.gov.ar/',
	'h1'          => 'Sport fishing in La Paz, Entre Ríos: golden dorado, licence and fees',
	'bajada'      => 'La Paz, on the Paraná: golden dorado and surubí. Which licence you need in Entre Ríos and what it costs (fees as of 22/10/2026), and what to check before you go.',
	'modalidades' => "Guided boat fishing\nBaitcasting and spinning\nTrolling\nBait fishing",
	'epoca'       => 'Depends on the species, the river level and the closures in force.',
	'seo_title'   => 'Fishing in La Paz, Entre Ríos: licence, fees and rules',
	'seo_description' => 'Fishing guide to La Paz, Entre Ríos: golden dorado and surubí on the Paraná, the Entre Ríos licence and its fees, what to check before you go, dorado festival.',
	'temporada'   => [
		[ 'meses' => 'September', 'que' => 'Fiesta Provincial del Dorado Entrerriano (33rd edition in 2026: 25 and 26 September). Other festivals and tournaments: dates not confirmed.' ],
		[ 'meses' => 'Before every trip', 'que' => 'Check the Paraná level (INA) and confirm the closures in force with the Entre Ríos Natural Resources and Enforcement Directorate.' ],
	],
	'reglamento_resumen' => 'You need a sport fishing licence to fish in Entre Ríos. According to the table of the Entre Ríos Natural Resources and Enforcement Directorate (consulted on 22/10/2026): annual licence for Entre Ríos residents $15,444.44; annual licence for residents of other provinces $24,251.60; 4-day temporary permit (other provinces) $5,488.52; foreigners (5 days) $24,251.60. We could not confirm online the current resolution on sizes, bag limits and closed seasons: ask the Directorate or your guide before you go.',
	'aguas' => [
		[ 'nombre' => 'Paraná River off La Paz', 'nota' => 'Check the level in the INA alert system. Confirm the closures in force before fishing.', 'lat' => '', 'lon' => '' ],
	],
	'faqs' => [
		[ 'pregunta' => 'Which licence do I need to fish in La Paz, Entre Ríos?', 'respuesta' => 'The sport fishing licence of Entre Ríos. You get it through the procedures portal of the Government of Entre Ríos, and the fees are set by the Natural Resources and Enforcement Directorate.' ],
		[ 'pregunta' => 'How much does the licence cost for an Argentine tourist?', 'respuesta' => 'According to the table of the Entre Ríos Natural Resources and Enforcement Directorate (consulted on 22/10/2026): annual licence for residents of other provinces, $24,251.60; 4-day temporary permit, $5,488.52. Foreigners: 5-day permit, $24,251.60. Fees are adjusted: confirm them when you apply.' ],
		[ 'pregunta' => 'Is a licence from another province valid?', 'respuesta' => 'There is a regional agreement for reciprocal recognition of licences in the Litoral that includes Entre Ríos (Resolution 0336/25 of Santa Fe). Confirm the exact conditions with the Entre Ríos Natural Resources and Enforcement Directorate before you travel.' ],
		[ 'pregunta' => 'What size and bag limits apply in La Paz?', 'respuesta' => 'We could not confirm online the current resolution on sizes, bag limits and closed seasons in Entre Ríos, so we do not publish them. Ask the Entre Ríos Natural Resources and Enforcement Directorate or your guide before you go.' ],
		[ 'pregunta' => 'Are there closed seasons?', 'respuesta' => 'Yes, there can be spawning closures and extraordinary closures. The dates change every year and we could not confirm them online: ask the Entre Ríos Natural Resources and Enforcement Directorate.' ],
		[ 'pregunta' => 'When is the fishing festival in La Paz?', 'respuesta' => 'The Fiesta Provincial del Dorado Entrerriano stands out. In 2026 it was its 33rd edition, on 25 and 26 September: on the 25th the 12th lure fishing meet, and on the 26th the 33rd bait fishing meet, with prizes and draws worth more than $40,000,000 according to the official poster. 2026 entry fee: $550,000 per modality until 31/08 (in up to 3 instalments) and $600,000 after that ($800,000 for both modalities). The date of the next edition has not been published yet, and La Paz also has other festivals and tournaments.' ],
		[ 'pregunta' => 'How do I get to La Paz?', 'respuesta' => 'By national route 12, north of the city of Paraná.' ],
		[ 'pregunta' => 'Where can I see the river level?', 'respuesta' => 'In the INA hydrological alert system, which shows the level of the Paraná.' ],
	],
];

// SEO limits the site uses: title <= 60, description 70-160.
if ( mb_strlen( $campos['seo_title'] ) > 60 || mb_strlen( $campos['seo_description'] ) > 160 || mb_strlen( $campos['seo_description'] ) < 70 ) {
	WP_CLI::error( 'SEO fuera de rango: title ' . mb_strlen( $campos['seo_title'] ) . ', description ' . mb_strlen( $campos['seo_description'] ) );
}

if ( pm_buscar( 'pm_destino', 'la-paz', 'en' ) ) {
	WP_CLI::log( 'la-paz (en) ya existe: no toco nada.' );
	return;
}
if ( ! pm_buscar( 'pm_destino', 'la-paz', 'es' ) ) {
	WP_CLI::error( 'No encontré la guía en español.' );
}
WP_CLI::log( 'la-paz (en): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . ' /fishing-in-la-paz/ con ' . count( $campos['faqs'] ) . ' preguntas y ' . count( $fuentes_filas ) . ' fuentes; title ' . mb_strlen( $campos['seo_title'] ) . ', description ' . mb_strlen( $campos['seo_description'] ) );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$id = wp_insert_post( [
	'post_type'    => 'pm_destino',
	'post_status'  => 'publish',
	'post_title'   => 'La Paz',
	'post_name'    => 'fishing-in-la-paz',
	'post_content' => wp_slash( $cuerpo ),
], true );
if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}
foreach ( $campos as $k => $v ) {
	update_field( $k, $v, $id );
}
update_post_meta( $id, '_pm_traducido', time() + 1 );
flush_rewrite_rules( false );
WP_CLI::log( "✓ la-paz (en) #$id: " . get_permalink( $id ) );
WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
WP_CLI::log( 'APLICADO' );
