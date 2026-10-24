<?php
/**
 * San Martín y Junín de los Andes (Neuquén), 24/10/2026: fifth destination guide, in Spanish and English.
 * Built from docs/investigacion/san-martin-junin.md. Next in the SEO matrix after La Paz
 * (docs/seo-oportunidades.md). One combined guide, as in docs/seo-guia-wireframes.md (model 2).
 * Same `clave` as the "próximamente" card (san-martin-junin), which hides itself.
 *
 * Patagonia, not the Litoral: a different authority (Dirección Provincial de Fauna de Neuquén
 * and, inside the park, Parques Nacionales) and a different rule book (Reglamento de Pesca
 * Continental Patagónico). Nothing from Corrientes or Entre Ríos applies.
 *
 * What is published: only what two press outlets agree on and what the guide's structure needs
 * (permit sales open since 15/10/2026 on the official site, season opens 1 November, one rule
 * book for the Patagonian provinces). Permit fees, per-water rules, Lanín park rules and the
 * outfitters were NOT confirmed (this sandbox cannot open the official sites): they say
 * "we couldn't confirm it, ask the Dirección or your guide" and live as hidden PENDIENTE notes.
 * No outfitter is listed (no opened source).
 *
 * Idempotent: a guide that exists is never touched. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = san-martin-junin-2026-10-24).
 */

$aplicar = false;
$hoy     = '20261024';

function pm_smj_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_smj_h( string $texto ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$texto}</h2>\n<!-- /wp:heading -->";
}
function pm_smj_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_smj_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$permisos_url = 'https://cazaypesca.neuquen.gob.ar/permisos';

$fuentes = [
	[ 'Caza y Pesca de Neuquén (Dirección Provincial de Fauna): permisos de pesca', $permisos_url, 1 ],
	[ 'Neuquén Turismo: folleto digital de pesca 2026', 'https://turismo.neuquen.gob.ar/wp-content/uploads/2026/06/Folleto_Pesca_Digital-2026.pdf', 1 ],
	[ 'Noticias NQN: Neuquén habilitó la compra de permisos para la nueva temporada de pesca (15/10/2026)', 'https://www.noticiasnqn.com.ar/noticias/2026/10/15/356687-neuquen-habilito-la-compra-de-permisos-para-la-nueva-temporada-de-pesca', 0 ],
	[ 'ANBariloche: arranca la venta de permisos de pesca para la temporada 2026-2027 (16/10/2026)', 'https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027', 0 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$comun = [
	'clave'                => 'san-martin-junin',
	'autor'                => 'Equipo Pescamapa',
	'actualizado'          => $hoy,
	'fuentes'              => $fuentes_filas,
	'reglamento_autoridad' => 'Dirección Provincial de Fauna de Neuquén',
	'reglamento_url'       => $permisos_url,
];

$guias = [];

// --- Español ---------------------------------------------------------------
$guias['es'] = [
	'titulo' => 'San Martín y Junín de los Andes',
	'slug'   => 'pesca-san-martin-junin-de-los-andes',
	'cuerpo' => implode( "\n\n", [
		pm_smj_p( 'San Martín de los Andes y Junín de los Andes están en el sudoeste de <strong>Neuquén</strong>, en plena <strong>Patagonia</strong>, a unos 40 km una de la otra por la ruta nacional 234. Es otra pesca que la del Litoral: truchas de río y de lago, y sobre todo <strong>pesca con mosca</strong>. Los ambientes más nombrados son el <strong>río Chimehuín</strong>, el <strong>Malleo</strong>, el <strong>Collón Curá</strong>, el <strong>Correntoso</strong> y los lagos de la zona, como el <strong>Lácar</strong>.' ),
		pm_smj_pendiente( 'confirmar con una fuente oficial abierta la distancia, las especies (arco iris, marrón, fontinalis) y qué ambientes están en el Parque Nacional Lanín.' ),
		pm_smj_h( 'Temporada 2026-2027: no toda la Patagonia abre el mismo día' ),
		pm_smj_p( 'Según la prensa regional (consultada el 24/10/2026), la <strong>temporada de pesca en la Patagonia empieza el 1 de noviembre</strong> y los permisos de la temporada 2026-2027 se venden online desde el 15 de octubre de 2026. En algunos ambientes la temporada termina antes (según esas notas, el 1 de mayo de 2027) y hay excepciones por ambiente, así que <strong>cada río y cada lago tiene su propia fecha y su propia modalidad</strong>: confirmala antes de salir.' ),
		pm_smj_h( 'Permiso de pesca en Neuquén' ),
		pm_smj_p( 'Para pescar hace falta el permiso de la temporada. Se compra online en el sitio oficial de <a href="' . $permisos_url . '" rel="noopener">Caza y Pesca de Neuquén</a>, que según las notas incluye un asistente para consultar las disposiciones de cada ambiente. El permiso es personal y se exhibe ante el control.' ),
		pm_smj_p( 'No pudimos confirmar online los <strong>valores del permiso 2026-2027</strong> (residentes, argentinos no residentes, extranjeros, diario, semanal y de temporada), por eso no los publicamos. Mirálos en el sitio oficial al comprarlo, o preguntale a tu guía.' ),
		pm_smj_pendiente( 'valores del permiso 2026-2027 por categoría, con captura o página oficial abierta (ronda 1 de Gemini, pregunta 1).' ),
		pm_smj_h( 'Reglamento y reglas por ambiente' ),
		pm_smj_p( 'Las provincias patagónicas comparten un <strong>Reglamento de Pesca Continental Patagónico</strong>, y cada ambiente tiene después sus propias disposiciones: modalidad permitida (por ejemplo, solo mosca en ciertos tramos), cupos, devolución obligatoria, cierres y horarios. Dentro del Parque Nacional Lanín (por ejemplo, el lago Lácar y los lagos de la cuenca del Huechulafquen) pueden sumarse las normas de Parques Nacionales.' ),
		pm_smj_p( 'No pudimos confirmar online las reglas ambiente por ambiente de esta temporada, y por eso no las publicamos: <strong>si no lo ves online, preguntale a tu guía</strong> o a la Dirección Provincial de Fauna. Las reglas cambian de una temporada a otra.' ),
		pm_smj_pendiente( 'reglas 2026-2027 de Chimehuín (La Boca, Chimehuín arriba y abajo), Malleo, Collón Curá, Correntoso, Lácar, Lolog; qué rige dentro del Lanín y si hay que pagar ingreso al parque; limpieza de equipos por didymo (ronda 1, preguntas 2 a 4).' ),
		pm_smj_h( 'Guías y alojamientos' ),
		pm_smj_p( 'Para pescar con mosca en estos ríos lo habitual es salir con un guía: conoce el nivel del río, qué se pesca ese día y los accesos (vadeo o flotada). Todavía no cargamos guías ni lodges: no publicamos ninguno sin una fuente que hayamos podido abrir. Si sos guía o tenés un lodge en la zona, escribinos a info@pescamapa.com.' ),
		pm_smj_h( 'Cómo llegar' ),
		pm_smj_p( 'San Martín de los Andes tiene el aeropuerto de Chapelco (CPC). Junín de los Andes queda a unos 40 km por la ruta nacional 234. Los accesos a muchos puntos de pesca son de ripio: preguntá por su estado antes de salir.' ),
		pm_smj_pendiente( 'confirmar distancia San Martín–Junín (≈40 km RN 234), vuelos a Chapelco y estado de los accesos de ripio.' ),
	] ),
	'campos' => [
		'h1'          => 'Pesca con mosca en San Martín y Junín de los Andes: temporada 2026-2027, permiso y dónde preguntar',
		'bajada'      => 'Chimehuín, Malleo, Collón Curá y lagos como el Lácar, en Neuquén. Cuándo abre la temporada 2026-2027, cómo se compra el permiso y qué consultar antes de salir.',
		'modalidades' => "Mosca (vadeo y flotada)\nSpinning y trolling en lagos, donde esté permitido\nSalida guiada",
		'epoca'       => 'La temporada de pesca en la Patagonia empieza el 1 de noviembre; cada ambiente tiene su fecha.',
		'seo_title'    => 'Pesca con mosca en San Martín y Junín de los Andes (2026/2027)',
		'seo_description' => 'Guía de pesca en San Martín y Junín de los Andes, Neuquén: Chimehuín, Malleo y lago Lácar, temporada 2026-2027 (abre el 1 de noviembre), permiso de pesca y qué consultar antes de salir.',
		'temporada'   => [
			[ 'meses' => 'Desde el 1 de noviembre', 'que' => 'Abre la temporada 2026-2027 en la Patagonia. Algunos ambientes cierran antes (según la prensa, el 1 de mayo de 2027) y hay excepciones por ambiente.' ],
			[ 'meses' => 'Antes de cada salida', 'que' => 'Confirmá la modalidad, el cupo y los cierres de ese río o lago con la Dirección Provincial de Fauna o con tu guía.' ],
		],
		'reglamento_resumen' => 'La temporada de pesca en la Patagonia empieza el 1 de noviembre; los permisos 2026-2027 se venden online desde el 15 de octubre de 2026 en el sitio de Caza y Pesca de Neuquén (consultado el 24/10/2026). Las provincias patagónicas comparten un reglamento continental y cada ambiente tiene sus disposiciones; dentro del Parque Nacional Lanín pueden sumarse las de Parques Nacionales. No pudimos confirmar online los valores del permiso ni las reglas por ambiente: consultalos en el sitio oficial o con tu guía antes de salir.',
		'aguas' => [
			[ 'nombre' => 'Río Chimehuín', 'nota' => 'Sale del lago Huechulafquen y pasa por Junín de los Andes. Confirmá modalidad y cierres del tramo antes de pescar.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Río Malleo', 'nota' => 'Confirmá modalidad y cierres antes de pescar.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Lago Lácar', 'nota' => 'Frente a San Martín de los Andes. Puede regir también Parques Nacionales: confirmá.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => '¿Cuándo empieza la temporada de pesca 2026-2027 en San Martín y Junín de los Andes?', 'respuesta' => 'Según la prensa regional (consultada el 24/10/2026), la temporada de pesca en la Patagonia empieza el 1 de noviembre. En algunos ambientes termina antes (el 1 de mayo de 2027, según esas notas) y hay excepciones por río o lago: confirmá la fecha de tu ambiente con la Dirección Provincial de Fauna o con tu guía.' ],
			[ 'pregunta' => '¿Dónde se compra el permiso de pesca de Neuquén?', 'respuesta' => 'Online, en el sitio de Caza y Pesca de Neuquén (cazaypesca.neuquen.gob.ar/permisos). Según la prensa, la venta de la temporada 2026-2027 abrió el 15 de octubre de 2026.' ],
			[ 'pregunta' => '¿Cuánto cuesta el permiso de pesca en Neuquén?', 'respuesta' => 'No pudimos confirmar online los valores del permiso 2026-2027, así que no los publicamos. Están en el sitio oficial de Caza y Pesca de Neuquén al comprarlo; si no los ves online, preguntale a tu guía.' ],
			[ 'pregunta' => '¿Qué reglamento rige?', 'respuesta' => 'Las provincias patagónicas comparten un Reglamento de Pesca Continental Patagónico y cada ambiente tiene sus propias disposiciones (modalidad, cupo, devolución, cierres). Dentro del Parque Nacional Lanín pueden sumarse las de Parques Nacionales. No pudimos confirmar online las reglas de cada ambiente de esta temporada: consultalas con la Dirección Provincial de Fauna o con tu guía.' ],
			[ 'pregunta' => '¿Hace falta un guía para pescar con mosca acá?', 'respuesta' => 'No es obligatorio, pero es lo habitual: el guía conoce el nivel del río, qué se pesca ese día y los accesos. Por ahora no listamos guías porque no pudimos confirmar ninguno con una fuente abierta.' ],
			[ 'pregunta' => '¿Cómo llego a San Martín y Junín de los Andes?', 'respuesta' => 'San Martín de los Andes tiene el aeropuerto de Chapelco (CPC). Junín de los Andes queda a unos 40 km por la ruta nacional 234. Los accesos a muchos puntos de pesca son de ripio.' ],
		],
	],
];

// --- English ---------------------------------------------------------------
$guias['en'] = [
	'titulo' => 'San Martín and Junín de los Andes',
	'slug'   => 'fishing-san-martin-junin-de-los-andes',
	'cuerpo' => implode( "\n\n", [
		pm_smj_p( 'San Martín de los Andes and Junín de los Andes are in southwestern <strong>Neuquén</strong>, in <strong>Patagonia</strong>, about 40 km apart on national route 234. It is a different kind of fishing from the Litoral: river and lake trout, and above all <strong>fly fishing</strong>. The best-known waters are the <strong>Chimehuín</strong>, <strong>Malleo</strong>, <strong>Collón Curá</strong> and <strong>Correntoso</strong> rivers and the lakes of the area, such as <strong>Lácar</strong>.' ),
		pm_smj_pendiente( 'confirm distance, species (rainbow, brown, brook) and which waters are inside Lanín National Park with an open official source.' ),
		pm_smj_h( '2026-2027 season: not all of Patagonia opens on the same day' ),
		pm_smj_p( 'According to regional press (checked 24/10/2026), the <strong>fishing season in Patagonia opens on 1 November</strong>, and permits for the 2026-2027 season have been on sale online since 15 October 2026. On some waters the season ends earlier (1 May 2027, according to those reports) and there are exceptions, so <strong>each river and lake has its own dates and its own allowed method</strong>: confirm them before you go.' ),
		pm_smj_h( 'Fishing permit in Neuquén' ),
		pm_smj_p( 'You need the season permit to fish. It is bought online on the official <a href="' . $permisos_url . '" rel="noopener">Caza y Pesca de Neuquén</a> site, which according to the reports includes a chatbot for the rules of each water. The permit is personal and must be shown to inspectors.' ),
		pm_smj_p( 'We could not confirm the <strong>2026-2027 permit fees</strong> online (residents, other Argentines, foreigners, daily, weekly and season), so we do not publish them. Check them on the official site when you buy, or ask your guide.' ),
		pm_smj_h( 'Rules and water-by-water regulations' ),
		pm_smj_p( 'The Patagonian provinces share a <strong>Patagonian continental fishing regulation</strong>, and each water then has its own rules: allowed method (for example, fly only on some stretches), limits, mandatory release, closures and hours. Inside Lanín National Park (for example Lake Lácar and the Huechulafquen lakes) National Parks rules may apply as well.' ),
		pm_smj_p( 'We could not confirm this season\'s water-by-water rules online, so we do not publish them: <strong>if you can\'t see it online, ask your guide</strong> or the provincial wildlife directorate. Rules change from one season to the next.' ),
		pm_smj_h( 'Guides and lodges' ),
		pm_smj_p( 'Most anglers fly fish these rivers with a guide: they know the river level, what is working that day and the access points (wading or drift boat). We have not listed any guide or lodge yet because we have no source we could open. If you are a guide or run a lodge here, write to info@pescamapa.com.' ),
		pm_smj_h( 'Getting there' ),
		pm_smj_p( 'San Martín de los Andes has Chapelco airport (CPC). Junín de los Andes is about 40 km away on national route 234. Access roads to many fishing spots are gravel: ask about their condition before you go.' ),
	] ),
	'campos' => [
		'h1'          => 'Fly fishing in San Martín and Junín de los Andes: 2026-2027 season, permit and who to ask',
		'bajada'      => 'Chimehuín, Malleo, Collón Curá and lakes such as Lácar, in Neuquén. When the 2026-2027 season opens, how to buy the permit and what to check before you go.',
		'modalidades' => "Fly fishing (wading and drift boat)\nSpinning and trolling on lakes, where allowed\nGuided day",
		'epoca'       => 'The Patagonian fishing season opens on 1 November; each water has its own dates.',
		'seo_title'    => 'Fly fishing in San Martín and Junín de los Andes (2026/2027)',
		'seo_description' => 'Fishing guide to San Martín and Junín de los Andes, Neuquén: Chimehuín, Malleo and Lake Lácar, the 2026-2027 season (opens 1 November), the fishing permit and what to check before you go.',
		'temporada'   => [
			[ 'meses' => 'From 1 November', 'que' => 'The 2026-2027 season opens in Patagonia. Some waters close earlier (1 May 2027, according to the press) and there are exceptions.' ],
			[ 'meses' => 'Before every trip', 'que' => 'Confirm the method, limit and closures for that river or lake with the provincial wildlife directorate or your guide.' ],
		],
		'reglamento_resumen' => 'The Patagonian fishing season opens on 1 November; 2026-2027 permits have been on sale online since 15 October 2026 on the Caza y Pesca de Neuquén site (checked 24/10/2026). The Patagonian provinces share one regulation and each water has its own rules; inside Lanín National Park National Parks rules may apply too. We could not confirm the permit fees or the water-by-water rules online: check the official site or ask your guide before you go.',
		'aguas' => [
			[ 'nombre' => 'Chimehuín River', 'nota' => 'Flows out of Lake Huechulafquen past Junín de los Andes. Confirm the method and closures for the stretch before fishing.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Malleo River', 'nota' => 'Confirm the method and closures before fishing.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Lake Lácar', 'nota' => 'In front of San Martín de los Andes. National Parks rules may apply too: confirm.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'When does the 2026-2027 fishing season start in San Martín and Junín de los Andes?', 'respuesta' => 'According to regional press (checked 24/10/2026), the fishing season in Patagonia opens on 1 November. On some waters it ends earlier (1 May 2027, according to those reports) and there are exceptions by river or lake: confirm the dates for your water with the provincial wildlife directorate or your guide.' ],
			[ 'pregunta' => 'Where do I buy the Neuquén fishing permit?', 'respuesta' => 'Online, on the Caza y Pesca de Neuquén site (cazaypesca.neuquen.gob.ar/permisos). According to the press, sales for the 2026-2027 season opened on 15 October 2026.' ],
			[ 'pregunta' => 'How much is the fishing permit in Neuquén?', 'respuesta' => 'We could not confirm the 2026-2027 permit fees online, so we do not publish them. They are on the official Caza y Pesca de Neuquén site when you buy; if you can\'t see them online, ask your guide.' ],
			[ 'pregunta' => 'Which regulations apply?', 'respuesta' => 'The Patagonian provinces share one continental fishing regulation and each water has its own rules (method, limit, release, closures). Inside Lanín National Park National Parks rules may apply too. We could not confirm each water\'s rules for this season online: ask the provincial wildlife directorate or your guide.' ],
			[ 'pregunta' => 'Do I need a guide to fly fish here?', 'respuesta' => 'It is not mandatory, but it is the usual way: a guide knows the river level, what is working that day and the access points. We do not list guides yet because we could not confirm any with an open source.' ],
			[ 'pregunta' => 'How do I get to San Martín and Junín de los Andes?', 'respuesta' => 'San Martín de los Andes has Chapelco airport (CPC). Junín de los Andes is about 40 km away on national route 234. Access roads to many fishing spots are gravel.' ],
		],
	],
];

// --- Create ---------------------------------------------------------------------
foreach ( [ 'es', 'en' ] as $i ) {
	$g  = $guias[ $i ];
	$ya = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'san-martin-junin', 'numberposts' => -1 ] );
	$existe = array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $i );
	if ( $existe ) {
		WP_CLI::log( "san-martin-junin ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "san-martin-junin ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $fuentes_filas ) . ' fuentes' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [
		'post_type'    => 'pm_destino',
		'post_status'  => 'publish',
		'post_title'   => $g['titulo'],
		'post_name'    => $g['slug'],
		'post_content' => wp_slash( $g['cuerpo'] ),
	], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i ];
	if ( 'es' === $i ) {
		// Same pin as the "próximamente" card (Junín de los Andes); no trout species sheet exists yet.
		$campos += [ 'region' => 'patagonia', 'provincia' => 'neuquen', 'lat' => -39.94, 'lon' => -71.07, 'especies' => [] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	if ( 'es' === $i ) {
		$proximo = get_page_by_path( 'san-martin-junin', OBJECT, 'pm_proximo' );
		if ( $proximo && get_post_thumbnail_id( $proximo->ID ) ) {
			set_post_thumbnail( $id, get_post_thumbnail_id( $proximo->ID ) );
			WP_CLI::log( 'foto: la de la tarjeta "próximamente"' );
		} else {
			WP_CLI::warning( 'sin foto: cargala desde el panel (Imagen destacada)' );
		}
	}
	WP_CLI::log( "✓ san-martin-junin ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas y ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes leídas de vuelta' );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
