<?php
/**
 * Esquina (Corrientes), 20/10/2026: second destination guide, Spanish first.
 * Run with wp-script.yml (script = esquina-2026-10-20).
 *
 * Built from docs/investigacion/esquina.md, following the owner's wireframe
 * (docs/seo-guia-wireframes.md). Only what the research could back goes in
 * the public text; everything still to confirm is a PENDIENTE note (hidden
 * on the site, listed for editors under "Para completar la ficha"). The
 * research only saw search summaries, so the regulation is NOT marked as
 * verified.
 *
 * Created as a DRAFT (owner, 20/10/2026): Esquina stays unpublished until it
 * passes the verification protocol in docs/estado.md (Recursos Naturales
 * page read in full, two local guides contacted, log entry).
 *
 * Idempotent: if a Spanish guide with clave "esquina" exists, it does nothing
 * (edits from then on happen in wp-admin). Portuguese and English come later,
 * once the Spanish text is reviewed (the "Traducciones" column flags them).
 */

if ( get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'esquina', 'fields' => 'ids' ] ) ) {
	WP_CLI::log( 'esquina (es) ya existe: no toco nada.' );
	return;
}

$hoy = '20261020';

function pm_esq_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_esq_h( string $texto, int $nivel = 2 ): string {
	$attr = 2 === $nivel ? '' : ' {"level":' . $nivel . '}';
	return "<!-- wp:heading{$attr} -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->";
}
function pm_esq_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$cuerpo = implode( "\n\n", [
	pm_esq_p( 'Esquina está en el sur de Corrientes, donde el <strong>río Corriente</strong> desemboca en el <strong>Paraná</strong>. Ese encuentro arma un delta de islas, riachos, lagunas y bañados que es la razón por la que se viene a pescar acá: aguas grandes del Paraná a un lado, aguas más quietas y claras del Corriente al otro.' ),
	pm_esq_h( 'El delta de Esquina: zonas de pesca y ambientes' ),
	pm_esq_h( 'El río Corriente y sus riachos', 3 ),
	pm_esq_p( 'Agua más clara y más lenta que la del Paraná. Es el ambiente que eligen los lodges para buscar <strong>dorado con mosca</strong>, y en los bañados chicos también aparecen dorados grandes y tarariras.' ),
	pm_esq_pendiente( 'confirmar con un guía local el canal Torello, nombres de riachos y lagunas, bancos de arena y si se usan señuelos livianos en el Corriente.' ),
	pm_esq_h( 'El cauce principal del Paraná', 3 ),
	pm_esq_p( 'Frente a Esquina el Paraná es río de lancha: <strong>trolling</strong> y pesca embarcada con guía para <strong>surubí</strong> y dorado. En los últimos años la prensa regional reportó surubíes de más de 40 kilos sacados (y devueltos) en esta zona.' ),
	pm_esq_pendiente( 'pozones, correderas y pesca fondeada en Esquina: validar con guía local.' ),
	pm_esq_h( 'Especies objetivo y modalidades' ),
	pm_esq_p( 'Las estrellas son el <strong>dorado</strong> y el <strong>surubí</strong>; también se pescan <strong>patí</strong> y <strong>pacú</strong>, la especie que le da nombre a la fiesta del pueblo. Se pesca embarcado, con spinning, trolling y mosca. El <strong>dorado</strong> es Monumento Natural Provincial de Corrientes (Ley 6730): se pesca con devolución.' ),
	pm_esq_p( '<strong>Cómo cambia con la altura del río.</strong> Como en todo el Paraná correntino, en bajante los peces se concentran en las bocas de arroyos y en los pozones, y rinden los señuelos y el trolling; en creciente el agua entra a islas y bañados, los peces se dispersan y conviene buscarlos contra las barrancas y en los riachos profundos. Antes de reservar, mirá la altura del río.' ),
	pm_esq_pendiente( 'boga, baitcasting y técnica por especie en Esquina: confirmar con guía local.' ),
	pm_esq_h( 'Cuándo ir: calendario real, no "todo el año"' ),
	pm_esq_p( 'Que haya pesca con devolución abierta no significa que todas las especies rindan igual todo el año. Las épocas del calendario son una referencia regional: las define el río (crecidas y bajantes), la temperatura del agua y las vedas que disponga la provincia. Preguntale siempre a tu guía qué rinde en tu fecha.' ),
	pm_esq_h( 'Guías de pesca, cabañas y posadas' ),
	pm_esq_p( 'Hay lodges con paquetes de varios días (sobre todo de pesca con mosca, con pensión completa, guía y lancha) y guías locales que ofrecen jornadas en lancha. Una jornada típica incluye lancha, combustible, guía y carnada; algunos prestan el equipo. Pedí siempre por escrito qué incluye y qué no.' ),
	pm_esq_pendiente( 'precios 2026 de una jornada con guía independiente; tracker y almuerzo en isla.' ),
	pm_esq_h( 'Cómo llegar, qué llevar y qué hacer además de pescar' ),
	pm_esq_p( 'Esquina está a unos <strong>600 km de Buenos Aires</strong> y a unos <strong>330 km de la ciudad de Corrientes</strong>, con colectivos directos desde la capital provincial. Para dorado con mosca, los lodges recomiendan cañas de 9 pies y medio, línea 7 u 8 (flotante, intermedia y de hundimiento rápido), moscas tipo baitfish y poppers, y líder de acero. Para spinning y trolling, consultá el equipo con tu guía.' ),
	pm_esq_p( 'Para quien acompaña: playas sobre el río Corriente, turismo rural en estancias, fotografía y observación de aves. En febrero, la <strong>Fiesta Nacional del Pacú</strong> llena el pueblo: en 2026 fue del 8 al 15 de febrero (39.ª edición).' ),
	pm_esq_pendiente( 'distancias y rutas desde Rosario, Córdoba y la frontera con Brasil; fecha de la Fiesta del Pacú 2027; sitio oficial del municipio.' ),
] );

$id = wp_insert_post( [
	'post_type'    => 'pm_destino',
	'post_status'  => 'draft',
	'post_title'   => 'Esquina',
	'post_name'    => 'pesca-en-esquina',
	'post_content' => wp_slash( $cuerpo ),
], true );
if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}

$campos = [
	'idioma'                => 'es',
	'clave'                 => 'esquina',
	'region'                => 'litoral',
	'provincia'             => 'corrientes',
	'lat'                   => -30.014,
	'lon'                   => -59.527,
	'especies'              => [ 'dorado', 'surubi' ],
	'alturas_url'           => 'https://alerta.ina.gob.ar/',
	'h1'                    => 'Pesca deportiva en Esquina, Corrientes: guía completa de especies, temporadas y servicios',
	'bajada'                => 'Donde el río Corriente se abre al Paraná hay islas, riachos y bañados. Dorado, surubí y pacú con devolución, y cómo leer el río antes de ir.',
	'modalidades'           => "Embarcada con guía\nSpinning\nTrolling\nMosca",
	'epoca'                 => 'Dorado de octubre a abril y surubí de marzo a agosto como referencia; todo depende de la altura del río.',
	'seo_title'             => 'Pesca en Esquina, Corrientes: dorado, surubí y temporadas',
	'seo_description'       => 'Guía de pesca en Esquina, Corrientes: dorado y surubí en el Paraná y el río Corriente, cuándo ir, licencia, vedas, guías y la Fiesta del Pacú.',
	'autor'                 => 'Equipo Pescamapa',
	'actualizado'           => $hoy,
	'temporada'             => [
		[ 'meses' => 'Octubre – abril', 'que' => 'Dorado: más activo con aguas templadas. Mosca en el río Corriente y sus riachos; señuelos y trolling en el Paraná. Siempre con devolución.' ],
		[ 'meses' => 'Marzo – agosto', 'que' => 'Surubí: referencia regional, en pozones y veriles del Paraná. Ejemplares grandes se reportan también fuera de esos meses.' ],
		[ 'meses' => 'Diciembre – marzo', 'que' => 'Pacú: temporada de verano, cerca de camalotales y barrancas con árboles.' ],
		[ 'meses' => 'Febrero', 'que' => 'Fiesta Nacional del Pacú (en 2026, del 8 al 15). Pico de demanda de alojamiento: reservá con tiempo.' ],
		[ 'meses' => 'Antes de cada salida', 'que' => 'Mirá la altura del Paraná, el pronóstico y las vedas vigentes. Las épocas son orientativas y cambian con el río.' ],
	],
	'reglamento_autoridad'  => 'Dirección de Recursos Naturales de Corrientes',
	'reglamento_url'        => 'https://www.recnaturalesctes.gob.ar/',
	'reglamento_resumen'    => 'Para pescar en Corrientes hace falta licencia de pesca deportiva: se tramita online en el portal de Recursos Naturales. Si tenés licencia vigente como residente de Santa Fe, Entre Ríos o Chaco, el acuerdo regional de reconocimiento recíproco te permite pescar respetando las normas de Corrientes; confirmá el alcance antes de viajar. El dorado es Monumento Natural Provincial (Ley 6730) y se pesca con devolución. La provincia dispone vedas extraordinarias por tramo del Paraná (en 2026 las hubo aguas arriba, lejos de Esquina): verificá siempre el tramo que vas a pescar.',
	'aguas'                 => [
		[ 'nombre' => 'Río Paraná frente a Esquina', 'nota' => 'Cauce principal: pesca embarcada con guía, trolling y spinning para surubí y dorado.', 'lat' => '', 'lon' => '' ],
		[ 'nombre' => 'Río Corriente y su delta', 'nota' => 'Aguas más claras y lentas, riachos y bañados: el ambiente del dorado con mosca.', 'lat' => '', 'lon' => '' ],
		[ 'nombre' => 'Islas y riachos del Paraná', 'nota' => 'Riachos, lagunas y bañados entre islas. Conviene ir con guía.', 'lat' => '', 'lon' => '' ],
	],
	'faqs'                  => [
		[ 'pregunta' => '¿Qué se pesca en Esquina?', 'respuesta' => 'Sobre todo dorado y surubí; también patí y pacú, y tarariras en los bañados. Es la oferta típica, no una garantía de captura: depende del río y de la época.' ],
		[ 'pregunta' => '¿Hace falta licencia?', 'respuesta' => 'Sí. En Corrientes se necesita licencia de pesca deportiva, que se tramita online en el portal de Recursos Naturales. Si sos de Santa Fe, Entre Ríos o Chaco, mirá el acuerdo regional de reconocimiento recíproco y confirmá su alcance.' ],
		[ 'pregunta' => '¿Me puedo llevar un dorado?', 'respuesta' => 'No. El dorado es Monumento Natural Provincial de Corrientes (Ley 6730) y se pesca con devolución.' ],
		[ 'pregunta' => '¿Cuándo es la Fiesta Nacional del Pacú?', 'respuesta' => 'En febrero. En 2026 fue del 8 al 15 de febrero (39.ª edición). Es el momento de más demanda de alojamiento en Esquina.' ],
		[ 'pregunta' => '¿Qué fechas abarca la veda en Corrientes?', 'respuesta' => 'No hay un calendario fijo: la provincia dispone vedas extraordinarias por tramo del Paraná, en general para proteger la reproducción. Antes de salir, consultá en Recursos Naturales si hay alguna vigente en el tramo de Esquina.' ],
		[ 'pregunta' => '¿Cómo llego desde Buenos Aires?', 'respuesta' => 'Son unos 600 km por ruta. Desde la ciudad de Corrientes son unos 330 km, con colectivos directos.' ],
		[ 'pregunta' => '¿Se puede pescar de costa o hace falta lancha? PENDIENTE', 'respuesta' => 'PENDIENTE: confirmar con la Municipalidad y guías dónde se puede pescar de costa.' ],
		[ 'pregunta' => '¿Cuánto cuesta una jornada de pesca embarcada? PENDIENTE', 'respuesta' => 'PENDIENTE: pedir precios 2026 a guías locales.' ],
	],
	'fuentes'               => [
		[ 'nombre' => 'Recursos Naturales de Corrientes', 'url' => 'https://www.recnaturalesctes.gob.ar/', 'oficial' => 1, 'consultada' => $hoy ],
		[ 'nombre' => 'Senado de Corrientes: Ley 6730 (dorado, Monumento Natural Provincial)', 'url' => 'https://senadoctes.gov.ar/normativas/leyes/6730.pdf', 'oficial' => 1, 'consultada' => $hoy ],
		[ 'nombre' => 'Gobierno de Santa Fe: acuerdo de reconocimiento recíproco de licencias con Entre Ríos, Chaco y Corrientes', 'url' => 'https://www.santafe.gob.ar/noticias/noticia/287531/', 'oficial' => 1, 'consultada' => $hoy ],
		[ 'nombre' => 'Argentina.travel: Esquina', 'url' => 'https://www.argentina.travel/pt/actividades/esquina', 'oficial' => 1, 'consultada' => $hoy ],
		[ 'nombre' => 'INA: sistema de alerta hidrológico (altura del Paraná)', 'url' => 'https://alerta.ina.gob.ar/', 'oficial' => 1, 'consultada' => $hoy ],
		[ 'nombre' => 'Welcome Argentina: pescar en Esquina', 'url' => 'https://www.welcomeargentina.com/esquina/pesca-esquina.html', 'oficial' => 0, 'consultada' => $hoy ],
		[ 'nombre' => 'La Nación: Esquina, el río y la fauna', 'url' => 'https://www.lanacion.com.ar/revista-lugares/esquina-la-localidad-correntina-que-hizo-feliz-a-maradona-tiene-un-rio-increible-y-mucha-fauna-nid03052024/', 'oficial' => 0, 'consultada' => $hoy ],
		[ 'nombre' => 'Prensa Mercosur: Fiesta Nacional del Pacú 2026', 'url' => 'https://prensamercosur.org/2026/02/05/la-fiesta-nacional-del-pacu-impulsa-el-turismo-y-la-agenda-de-verano-en-corrientes/', 'oficial' => 0, 'consultada' => $hoy ],
	],
];
foreach ( $campos as $k => $v ) {
	update_field( $k, $v, $id );
}

// Photo: reuse the "coming soon" card's photo, if it had one.
$proximo = get_page_by_path( 'esquina', OBJECT, 'pm_proximo' );
if ( $proximo && get_post_thumbnail_id( $proximo->ID ) ) {
	set_post_thumbnail( $id, get_post_thumbnail_id( $proximo->ID ) );
	WP_CLI::log( 'foto: la de la tarjeta "próximamente"' );
} else {
	WP_CLI::warning( 'sin foto: cargala desde el panel (Imagen destacada)' );
}

update_post_meta( $id, '_pm_es_editado', time() );
flush_rewrite_rules( false );
WP_CLI::success( "Esquina creada como borrador (es) #$id: revisala en wp-admin → Destinos." );
