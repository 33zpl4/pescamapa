<?php
/**
 * Paso de la Patria (Corrientes): English version of the Spanish guide, 29/10/2026. Why: English speakers search "golden dorado
 * fishing argentina" (docs/seo-palabras-clave-en-2026-10-29.md) and Paso de la Patria, the home of the Fiesta Nacional del Dorado, had
 * Spanish and Portuguese only. Faithful translation of paso-de-la-patria-2026-10-21 (es): same figures, same sources, same hidden
 * PENDIENTE notes; it adds no data. If an editor changed the Spanish guide after 21/10/2026, this translation does not include that change.
 * Shared fields (region, province, location, species, river station, photo) are inherited from the Spanish guide; the outfitters
 * ("listado con información pública") belong to the destination, so they show up here too.
 *
 * Idempotent: if the English guide exists it is not touched. Dry run first. Run with wp-script.yml (script = paso-de-la-patria-en-2026-10-29).
 */

$aplicar = false;
$hoy     = '20261021'; // the date of the Spanish text and of its sources, not today's

function pm_pe_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_pe_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_pe_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_pe_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$fuentes = [
	[ 'Corrientes Official Gazette: Disposición 853/22 (size and bag limits)', 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf', 1 ],
	[ 'Recursos Naturales de Corrientes', 'https://www.recnaturalesctes.gob.ar/', 1 ],
	[ 'Corrientes Tourism Ministry: procedures and permits (fishing licence)', 'https://corrientes.tur.ar/tramites-y-permisos/', 1 ],
	[ 'Corrientes Tourism Ministry: regional licence agreement (scope)', 'https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/', 1 ],
	[ 'Corrientes Tourism Ministry: Paso de la Patria', 'https://corrientes.tur.ar/destinos/paso-de-la-patria/', 1 ],
	[ 'Corrientes Tourism Ministry: getting here by road', 'https://corrientes.tur.ar/como-llegar-por-tierra/', 1 ],
	[ 'Argentina.gob.ar: fishing on the Paraná', 'https://www.argentina.gob.ar/node/509695', 1 ],
	[ 'Municipality of Paso de la Patria: rules and boating controls', 'https://www.pasodelapatria.gob.ar/?q=taxonomy%2Fterm%2F745', 1 ],
	[ 'Municipality of Paso de la Patria: 61st National Dorado Festival', 'https://pasodelapatria.gob.ar/noticias/fiesta-nacional-del-dorado-un-exito-total', 1 ],
	[ 'Municipality of Paso de la Patria: Torneo Apertura 2026', 'https://www.pasodelapatria.gob.ar/noticias/apertura-de-la-temporada-de-pesca-del-dorado', 1 ],
	[ 'INA: Paraná level at Paso de la Patria', 'https://alerta.ina.gov.ar/a5/secciones?seriesId=18', 1 ],
	[ 'Argentina.gob.ar: Paso de la Patria international crossing', 'https://www.argentina.gob.ar/seguridad/pasosinternacionales/detalle/rio/76/Puerto-Paso-de-la-Patria-Paso-de-la-Patria', 1 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$cuerpo = implode( "\n\n", [
	pm_pe_p( 'Paso de la Patria is on the <strong>Paraná River</strong>, near where it meets the Paraguay River and about 36 km from the city of Corrientes. For provincial and national tourism the <strong>golden dorado</strong> is the destination\'s emblem; surubí, pacú and boga are also fished.' ),
	pm_pe_h( 'What you can keep: the basic rules of Corrientes' ),
	pm_pe_p( '<strong>Disposición 853/22</strong> of the Natural Resources Directorate (Corrientes Official Gazette, 26 December 2022) applies until another act changes it. For the most sought-after species it says:' ),
	pm_pe_lista( [
		'<strong>Dorado and surubí</strong> (spotted and striped): catch and release only. They cannot be kept.',
		'<strong>Pacú</strong>: minimum size 45 cm; bag limit 1 per valid licence and a single fish per boat, no matter how many anglers are aboard.',
		'<strong>Patí</strong>: minimum size 70 cm; bag limit 2.',
		'<strong>Boga</strong>: minimum size 45 cm; bag limit 2 per angler with a valid licence and up to 6 per boat (if there are more anglers aboard, those 6 are shared among them).',
		'<strong>Tararira</strong> (trahira): minimum size 30 cm; bag limit 5.',
	] ),
	pm_pe_p( 'The same provision sets these other minimum sizes and bag limits: armado 45 cm (limit 5); yellow catfish 25 cm (5); corvina 25 cm (5); mandure 40 cm (5); moncholo 30 cm (5); palometa 20 cm (10); stingray 80 cm disc diameter (1); sábalo 40 cm (3); salmón 45 cm (2); virreyna 30 cm (5).' ),
	pm_pe_p( 'Species in the annex may only be moved within the town where they were caught: taking them to another destination, domestic or international, is forbidden. Before you go, also check that no extraordinary closure applies on the stretch you will fish.' ),
	pm_pe_h( 'Fishing licence' ),
	pm_pe_p( 'The licence is bought online on the Natural Resources portal of Corrientes and carried together with your identity document. The regional licence-recognition agreement (Santa Fe, Entre Ríos and Chaco) is for residents of those provinces and does not cover stretches under exclusively Corrientes jurisdiction. We found no rule recognising licences from other countries: visitors from abroad need the Corrientes licence.' ),
	pm_pe_pendiente( 'price and duration of each category and rules for foreigners: copy them from the portal with a date.' ),
	pm_pe_h( 'Closed seasons' ),
	pm_pe_p( 'The province can order extraordinary closures on stretches of the Paraná. In 2026 there were closures of that kind on stretches near Paso de la Patria, to protect concentrations of dorado and surubí. They change every year: before you go, check the current ones with Natural Resources.' ),
	pm_pe_h( 'Tournaments' ),
	pm_pe_p( 'The <strong>National Dorado Festival</strong> (Fiesta Nacional del Dorado) is organised by the Municipality: in 2026 it was its 61st edition, from 14 to 17 August, catch and release. The season-opening <strong>Torneo Apertura</strong> for the dorado ran from 2 to 5 April, also catch and release. The dates of the next editions have not been published yet.' ),
	pm_pe_h( 'Guides, lodges and inns' ),
	pm_pe_p( 'There are guides, inns and lodges with boat fishing. Below are the ones we found with public information; we have not verified them with each one yet, which is why they are marked.' ),
	pm_pe_h( 'Getting there' ),
	pm_pe_p( 'Take <strong>provincial route 9</strong>, reached from national route 12. According to the Corrientes Tourism Ministry it is about 36 km from the city of Corrientes and about 20 km from Resistencia; from Posadas, about 309 km. There is a minibus from Corrientes. The international crossing at Paso de la Patria is a passenger boat to Paso de Patria in Paraguay: there is no direct land crossing from Brazil.' ),
	pm_pe_pendiente( 'calendar by species and month, river-height ranges, tackle by species, shore areas where fishing is allowed, ramps and fuel, and how to enter from Brazil with your own boat.' ),
] );

$campos = [
	'idioma'      => 'en',
	'clave'       => 'paso-de-la-patria',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'fuentes'     => $fuentes_filas,
	'reglamento_autoridad' => 'Dirección de Recursos Naturales de Corrientes',
	'reglamento_url'       => 'https://www.recnaturalesctes.gob.ar/',
	'h1'          => 'Sport fishing in Paso de la Patria, Corrientes: golden dorado, rules and licence',
	'bajada'      => 'The golden dorado is the emblem of Paso de la Patria, on the Paraná near its confluence with the Paraguay. What you can keep, which licence you need and when the tournaments are.',
	'modalidades' => "Guided boat fishing\nBaitcasting and spinning\nTrolling\nFly fishing (flycast)\nBait fishing",
	'epoca'       => 'Depends on the species, the river level and the closures in force.',
	'seo_title'   => 'Fishing in Paso de la Patria: golden dorado, limits, licence',
	'seo_description' => 'Fishing guide to Paso de la Patria, Corrientes: golden dorado and surubí on the Paraná, size and bag limits, licence, dorado festivals and how to get there.',
	'temporada'   => [
		[ 'meses' => 'April', 'que' => 'Torneo Apertura of the dorado season (in 2026, 2 to 5 April, catch and release).' ],
		[ 'meses' => 'August', 'que' => 'National Dorado Festival (61st edition in 2026, 14 to 17 August, catch and release). Peak demand for lodging: book early.' ],
		[ 'meses' => 'Calendar by species', 'que' => 'PENDIENTE: confirm with a technical source or local guides.' ],
		[ 'meses' => 'Before every trip', 'que' => 'Check the Paraná level at the Paso de la Patria station (INA), the forecast and the closures in force.' ],
	],
	'reglamento_resumen' => 'Fishing in Corrientes requires a sport fishing licence, bought online on the Natural Resources portal. Disposición 853/22 (Official Gazette, 26/12/2022, in force until another act replaces it) sets dorado and surubí as catch and release only, and gives minimum sizes and bag limits for pacú (45 cm, 1 per licence and a single fish per boat), patí (70 cm, 2), boga (45 cm, 2 per angler and up to 6 per boat) and tararira (30 cm, 5). Fish you keep may only be moved within the town where they were caught. The province can order extraordinary closures on stretches; they change every year, so check the current ones with Natural Resources before you go.',
	'aguas' => [
		[ 'nombre' => 'Paraná River off Paso de la Patria', 'nota' => 'Main channel. Check the level at the INA Paso de la Patria station.', 'lat' => '', 'lon' => '' ],
		[ 'nombre' => 'Paraná–Paraguay confluence', 'nota' => 'A geographic feature of the area. Check the closures in force before fishing here.', 'lat' => '', 'lon' => '' ],
	],
	'faqs' => [
		[ 'pregunta' => 'Which licence do I need to fish in Paso de la Patria?', 'respuesta' => 'The sport fishing licence of Corrientes. You get it online on the Natural Resources portal and carry it with your identity document. Price and duration depend on the category and are shown when you apply.' ],
		[ 'pregunta' => 'Is a licence from another province or country valid?', 'respuesta' => 'The regional recognition agreement (Santa Fe, Entre Ríos and Chaco) is for residents of those provinces and does not cover stretches under exclusively Corrientes jurisdiction. We found no rule recognising licences from other countries: get the Corrientes one.' ],
		[ 'pregunta' => 'What size and bag limits apply in Paso de la Patria?', 'respuesta' => 'Those of Disposición 853/22 of Natural Resources (Official Gazette, 26/12/2022, in force until another act changes it): dorado and surubí, catch and release only; pacú, 45 cm and 1 per licence with a single fish per boat; patí, 70 cm and 2; boga, 45 cm and 2 per angler (up to 6 per boat); tararira, 30 cm and 5. The annex also covers other species such as armado, mandure, palometa or sábalo.' ],
		[ 'pregunta' => 'Can I take fish to another city?', 'respuesta' => 'No. Under Disposición 853/22, species in the annex may only be moved within the town where they were caught: taking them to another destination, domestic or international, is forbidden.' ],
		[ 'pregunta' => 'Are there closed seasons?', 'respuesta' => 'They can exist. The province orders extraordinary closures on stretches of the Paraná, and in 2026 there were some in areas near Paso de la Patria. They change every year: check the current ones with Natural Resources before you go.' ],
		[ 'pregunta' => 'When is the National Dorado Festival?', 'respuesta' => 'In 2026 it was the 61st edition, from 14 to 17 August, catch and release, organised by the Municipality. The date of the next one has not been published yet.' ],
		[ 'pregunta' => 'When is the Torneo Apertura?', 'respuesta' => 'In 2026 it ran from 2 to 5 April, catch and release. The date of the next one has not been published yet.' ],
		[ 'pregunta' => 'How do I get there from Corrientes or Resistencia?', 'respuesta' => 'By provincial route 9, reached from national route 12. According to the Corrientes Tourism Ministry it is about 36 km from the city of Corrientes and about 20 km from Resistencia. There is a minibus from Corrientes.' ],
		[ 'pregunta' => 'Can I come in from Brazil?', 'respuesta' => 'The international crossing at Paso de la Patria is a passenger boat to Paso de Patria in Paraguay: there is no direct land crossing from Brazil.' ],
		[ 'pregunta' => 'Where can I see the river level?', 'respuesta' => 'At the Paso de la Patria station of the INA hydrological alert system, which shows the level of the Paraná.' ],
	],
];

// SEO limits the site uses: title <= 60, description 70-160.
if ( mb_strlen( $campos['seo_title'] ) > 60 || mb_strlen( $campos['seo_description'] ) > 160 || mb_strlen( $campos['seo_description'] ) < 70 ) {
	WP_CLI::error( 'SEO fuera de rango: title ' . mb_strlen( $campos['seo_title'] ) . ', description ' . mb_strlen( $campos['seo_description'] ) );
}

if ( pm_buscar( 'pm_destino', 'paso-de-la-patria', 'en' ) ) {
	WP_CLI::log( 'paso-de-la-patria (en) ya existe: no toco nada.' );
	return;
}
if ( ! pm_buscar( 'pm_destino', 'paso-de-la-patria', 'es' ) ) {
	WP_CLI::error( 'No encontré la guía en español.' );
}
WP_CLI::log( 'paso-de-la-patria (en): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . ' /fishing-in-paso-de-la-patria/ con ' . count( $campos['faqs'] ) . ' preguntas y ' . count( $fuentes_filas ) . ' fuentes; title ' . mb_strlen( $campos['seo_title'] ) . ', description ' . mb_strlen( $campos['seo_description'] ) );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$id = wp_insert_post( [
	'post_type'    => 'pm_destino',
	'post_status'  => 'publish',
	'post_title'   => 'Paso de la Patria',
	'post_name'    => 'fishing-in-paso-de-la-patria',
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
WP_CLI::log( "✓ paso-de-la-patria (en) #$id: " . get_permalink( $id ) );
WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
WP_CLI::log( 'APLICADO' );
