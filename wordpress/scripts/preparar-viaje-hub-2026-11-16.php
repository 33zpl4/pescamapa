<?php
/**
 * Hub note "How to plan a fishing trip to Argentina, step by step" (es/en/pt siblings, clave como-preparar-viaje-pesca-argentina), 16/11/2026.
 * Why: owner's review (16/11/2026): note 4 of 4 after the permit step by step, the trip cost overview and how a fishing week works. A numbered hub that LINKS to everything that
 * already exists (calendar, dorado and trout pillars, guides, permit, cost and week notes) in eight steps, with an "in one line" box on top, a checklist at the end and a FAQ.
 * New facts come only from public pages already read with investigar.yml (branch investigacion), always attributed and dated; flights, transfers and luggage use only what those
 * pages say (none is an official source and the note says so); no flight prices, no commissions, no names of people, phones or e-mails, no "agency"/"operator" towards the visitor.
 * CTA of 16/11/2026: "tell us what you are looking for and we build the best option for you" (anchored to the reader's request); buttons to the request form with ?especie.
 * Needs the week note (script semana-lodge-2026-11-16) applied first. Inbound links: one paragraph after the short answer of the permit, cost and week notes and of the dorado and trout pillars.
 * Cover photo: the one already loaded for the note `precio-viaje-dorado-lodge` (Commons, provisional, see docs/estado.md).
 * Idempotent: a note that exists (same clave + language) is never touched. Dry run first ($aplicar = false). Run with wp-script.yml (script = preparar-viaje-hub-2026-11-16).
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

if ( ! function_exists( 'pm_pp_olista' ) ) {
	/** Ordered list block. */
	function pm_pp_olista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">{$li}</ol>\n<!-- /wp:list -->\n\n";
	}
}

$aplicar = false;
$hoy          = '20261116';
$clave        = 'como-preparar-viaje-pesca-argentina';
$foto_de      = 'precio-viaje-dorado-lodge'; // note whose cover photo is reused (Commons, sunrise over the Paraná River, no people)
$especie_nota = '';
$nota_tipo    = 'como';
$sem          = 'semana-de-pesca-en-lodge-argentina';

// External links: public pages already read with investigar.yml (branch investigacion); the date of each is in $fuente_nombre.
$u = [
	'sff_info'  => 'https://setflyfishing.com/en/trip-information-package/',
	'sff_itati' => 'https://setflyfishing.com/en/itati-lodge/',
	'nw_faq'    => 'https://www.nervouswaters.com/faqs/',
	'nw_pira'   => 'https://www.nervouswaters.com/the-lodges/pira-lodge/',
	'nw_suinda' => 'https://www.nervouswaters.com/the-lodges/suinda-lodge/',
	'fwt_pira'  => 'https://farbank.com/products/fly-water-travel-pira-lodge',
	'yd_pinti'  => 'https://www.yellowdogflyfishing.com/products/pintis-dorado-fly',
	'ta_plat'   => 'https://www.tresamigosoutfitters.com/los-plateados/',
	'tdf_pdf'   => 'https://prodyambiente.tierradelfuego.gob.ar/wp-content/uploads/2026/10/Resolucion-N259-Temporada-pesca-2026.pdf',
	'rm_faq'    => 'https://www.riomansolodge.com/faqs/',
];

$N = [];

// =====================================================================================================================================
// ESPAÑOL
// =====================================================================================================================================
$N['es'] = [
	'titulo'          => 'Cómo preparar un viaje de pesca a la Argentina, paso a paso',
	'slug'            => 'como-preparar-un-viaje-de-pesca-a-la-argentina',
	'seo_title'       => 'Cómo preparar un viaje de pesca a la Argentina, paso a paso',
	'seo_description' => 'Cómo preparar un viaje de pesca a la Argentina en 8 pasos: especie y época, estilo, permiso, costo, la semana, vuelos y equipaje, qué llevar y pedir opciones.',
	'bajada'          => 'Ocho pasos para preparar un viaje de pesca a la Argentina, cada uno con la página donde está el detalle: especie, zona y época; estilo; permiso; costo; cómo funciona la semana; vuelos, traslados y equipaje; qué llevar; y cómo pedir opciones.',
	'datos'           => [
		'No hay licencia nacional: cada región tiene su permiso (nota de permiso paso a paso, leída el 16/11/2026).',
		'Lo habitual en un lodge es una semana de 7 noches y 6 días de pesca, de sábado a sábado, en habitación doble y con guía compartido de a dos (páginas leídas entre el 30/10 y el 16/11/2026).',
		'Los vuelos internacionales llegan a Ezeiza y los internos salen de Aeroparque; Set Fly Fishing avisa que la seguridad aeroportuaria argentina no admite cañas ni reels en la cabina (leído el 15/11/2026).',
		'Ninguna página que leímos dice con cuántos meses de anticipación reservar; las semanas tienen de 6 a 12 cupos.',
		'Si preferís no escribirle a cada lugar, contanos qué buscás y te armamos la mejor opción para vos: respuesta en 48 horas, gratis.',
	],
	'p0'              => '<strong>Respuesta corta.</strong> Un viaje de pesca a la Argentina se prepara en <strong>ocho pasos</strong>: elegir especie, zona y época; elegir el estilo que buscás; saber qué permiso hace falta; saber cuánto cuesta; entender cómo es una semana de pesca; resolver vuelos, traslados y equipaje; armar qué llevar; y pedir opciones. Cada paso lleva a la página donde está el detalle, con su fuente y la fecha en que la leímos. Esta página ordena; no reemplaza el detalle ni es una cotización.',
	'resp'            => [
		'<strong>1. Especie, zona y época:</strong> el {cal|calendario de pesca 2026/27} y los pilares del {n:golden-dorado-guia|dorado} y de la {n:trucha-patagonia-pilar|trucha en la Patagonia}.',
		'<strong>2. Estilo:</strong> tamaño, cantidad, pesca a la vista y fauna, con lo que dicen las páginas de cada lugar.',
		'<strong>3. Permiso:</strong> {n:permiso-pesca-argentina-paso-a-paso|permiso de pesca en Argentina, paso a paso}.',
		'<strong>4. Costo:</strong> {n:cuanto-cuesta-viaje-pesca-argentina|cuánto cuesta un viaje de pesca a la Argentina 2026/27}.',
		'<strong>5. La semana:</strong> {n:semana-de-pesca-en-lodge-argentina|cómo funciona una semana de pesca en un lodge}.',
		'<strong>6. Vuelos, traslados y equipaje:</strong> aeropuertos, cañas en el equipaje despachado, seguro y documentos.',
		'<strong>7. Qué llevar:</strong> la sección de equipo del pilar del dorado y {n:reglas-equipo-patagonia|equipo y reglas de la Patagonia}.',
		'<strong>8. Pedir opciones:</strong> contanos qué buscás y te armamos la mejor opción para vos.',
	],
	'h_1'             => 'Paso 1. Elegí especie, zona y época',
	'p_1'             => 'La especie casi siempre decide la zona y la época. El {cal|calendario de pesca} junta las temporadas y las vedas de todas nuestras guías. Ejemplos de lo que dicen las páginas que leímos: en Itatí Lodge, la temporada es de septiembre a mayo y conviene evitar enero y febrero por el calor ({x:sff_itati|Set Fly Fishing}, 15/11/2026); en Los Plateados, la temporada del steelhead va de marzo a mayo ({x:ta_plat|Tres Amigos Outfitters}, 16/11/2026); en el río Grande, la pesca está habilitada del 30 de octubre de 2026 al 18 de abril de 2027 ({x:tdf_pdf|Resolución 259/26}, leída el 16/11/2026). Para elegir con más detalle: {n:golden-dorado-guia|el pilar del dorado}, {n:cuando-pescar-dorado|cuándo pescar el dorado}, {n:trucha-patagonia-pilar|el pilar de la trucha en la Patagonia} y {n:cuando-pescar-trucha-patagonia|cuándo pescar trucha en la Patagonia}. Las guías por destino están en {esp:dorado|la ficha del dorado} y {esp:trucha|la ficha de la trucha}.',
	'h_2'             => 'Paso 2. Elegí qué estilo buscás',
	'p_2'             => 'Tamaño, cantidad, pesca a la vista y fauna cambian de un lugar a otro. Lo que dice la página de cada uno, atribuido y con la fecha de lectura:',
	'tabla_cols'      => [ 'Qué buscás', 'Lo que dice la página', 'Dónde leer más' ],
	'tabla_rows'      => [
		[ '<strong>Tamaño</strong>', 'Itatí Lodge: dorados de 12 a 30 libras o más (de 5 a 14 kg) ({x:sff_itati|Set Fly Fishing}, 15/11/2026). Pirá Lodge: de 5 a 20 libras o más (de 2 a 9 kg) ({x:nw_pira|Nervous Waters}, 13/11/2026). Pinti’s: promedio de 6 a 8 libras (de 3 a 4 kg) y posibilidades reales de peces de 20 libras (9 kg) ({x:yd_pinti|Yellow Dog}, 13/11/2026).', '{g:itati|Itatí}, {g:esteros-del-ibera|Esteros del Iberá}, {g:ita-ibate|Itá Ibaté}' ],
		[ '<strong>Cantidad</strong>', 'Pirá Lodge: la pesca del dorado «no es un juego de números»; de 5 a 15 peces por día según las condiciones ({x:fwt_pira|Fly Water Travel}, 13/11/2026).', '{g:esteros-del-ibera|Esteros del Iberá}' ],
		[ '<strong>Pesca a la vista</strong>', 'Suindá Lodge: pesca a la vista de dorados en bancos de arena ({x:nw_suinda|Nervous Waters}, 13/11/2026). Pinti’s: pesca a la vista de dorados, pirá pitá y pacú con el agua clara ({x:yd_pinti|Yellow Dog}, 13/11/2026).', '{g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}' ],
		[ '<strong>Fauna y paisaje</strong>', 'Pirá Lodge: más de 350 especies de aves en los Esteros del Iberá, un humedal de casi 3 millones de acres ({x:nw_pira|Nervous Waters}, 13/11/2026).', '{g:esteros-del-ibera|Esteros del Iberá}' ],
		[ '<strong>Steelhead</strong>', 'Los Plateados: temporada de marzo a mayo y programa de 4 pescadores ({x:ta_plat|Tres Amigos Outfitters}, 16/11/2026).', '{g:rio-santa-cruz|Río Santa Cruz}' ],
		[ '<strong>Viento y clima</strong>', 'Las Buitreras: el viento es una constante (Fly Water Travel, 13/11/2026). Río Manso Lodge: de noviembre a abril los días van de 13 a 24 °C ({x:rm_faq|Río Manso Lodge}, 30/10/2026).', '{g:rio-gallegos|Río Gallegos}, {g:bariloche|Bariloche}' ],
	],
	'p_2c'            => 'Todas las guías de trucha y de dorado, con sus fuentes, están en {g:san-martin-junin|San Martín y Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel y Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos}, {g:rio-grande|Río Grande}, {g:goya|Goya}, {g:esquina|Esquina} y {g:paso-de-la-patria|Paso de la Patria}.',
	'h_3'             => 'Paso 3. Saber qué permiso hace falta',
	'p_3'             => 'No hay una licencia nacional: cada región tiene la suya y cada una un portal. Cómo se saca, qué piden y cómo se paga, con las páginas oficiales leídas el 16/11/2026: {n:permiso-pesca-argentina-paso-a-paso|permiso de pesca en Argentina, paso a paso}. Los valores por región, en pesos y en dólares aproximados: {n:licencias-extranjeros-argentina|licencia de pesca para extranjeros en Argentina 2026/27}.',
	'h_4'             => 'Paso 4. Saber cuánto cuesta',
	'p_4'             => 'Las cifras que publican los lugares van desde US$ 410 el medio día con guía cerca de Bariloche hasta US$ 12.950 a US$ 14.950 por una semana de trucha de mar en Río Grande, según {n:cuanto-cuesta-viaje-pesca-argentina|nuestra vista general}, con la fuente y la fecha de cada cifra. Dos precios solo se comparan cuando cubren lo mismo: {n:paquete-incluye-no-incluye|qué incluye un paquete de pesca y qué cuesta aparte}.',
	'h_5'             => 'Paso 5. Entender cómo funciona la semana',
	'p_5'             => 'En las páginas que leímos, una semana en un lodge suele ser de 7 noches y 6 días de pesca, de sábado a sábado, en habitación doble y con dos pescadores por guía; también hay media semana y paquetes por noches. {n:semana-de-pesca-en-lodge-argentina|Cómo funciona una semana de pesca en un lodge} da los formatos, los días de llegada y salida, qué pasa con un acompañante que no pesca y qué incluye.',
	'h_6'             => 'Paso 6. Vuelos, traslados y equipaje',
	'p_6'             => 'Todo lo que sigue es lo que dicen las páginas que leímos, y ninguna es una fuente oficial: confirmá con la aerolínea y con el consulado.',
	'l_6'             => [
		'<strong>Aeropuertos:</strong> los vuelos internacionales llegan a Ezeiza (EZE) y los internos salen de Aeroparque (AEP), a una hora entre uno y otro según Set Fly Fishing (15/11/2026). Para el dorado en Corrientes, Set Fly Fishing nombra el aeropuerto de Corrientes (CNQ) como el preferido y el de Resistencia (RES) como alternativa; para Santa Cruz, Tres Amigos Outfitters dice que se vuela a El Calafate o a Río Gallegos ({x:ta_plat|su página de Los Plateados}, 16/11/2026).',
		'<strong>Una noche en Buenos Aires:</strong> Nervous Waters recomienda pasar una noche en la ciudad antes del vuelo al lugar ({x:nw_faq|sus preguntas frecuentes}, 15/11/2026); Set Fly Fishing aconseja llegar con margen para no perder días de pesca por una demora ({x:sff_info|su guía de viaje}, 15/11/2026).',
		'<strong>Cañas y reels:</strong> Set Fly Fishing avisa que la seguridad aeroportuaria argentina no admite cañas ni reels en la cabina de los vuelos internos: van en el equipaje despachado. También recomienda llevar en el equipaje de mano una muda de ropa por si se demora el equipaje.',
		'<strong>Documentos:</strong> Set Fly Fishing pide un pasaporte con al menos 6 meses de vigencia después del viaje, y Nervous Waters y Set Fly Fishing dicen que los ciudadanos de EE. UU., Canadá y la Unión Europea no necesitan visa para turismo. No es una fuente oficial: confirmalo con el consulado argentino de tu país. El permiso de pesca va aparte: paso 3.',
		'<strong>Seguro de viaje:</strong> Set Fly Fishing lo recomienda y lo pone en su lista de preparación a 90 días o más de la salida (15/11/2026).',
		'<strong>Precios de vuelos:</strong> no los publicamos; cambian por fecha y aerolínea. Algunos lugares incluyen los vuelos chárter entre Buenos Aires y el lugar y otros no: se dice en cada oferta.',
	],
	'h_7'             => 'Paso 7. Qué llevar',
	'p_7'             => 'El equipo depende de la especie. Para el dorado, la sección de equipo del {n:golden-dorado-guia|pilar del dorado} reúne cañas, líneas, tippets y moscas con lo que publican varios lugares; para la Patagonia, {n:reglas-equipo-patagonia|equipo y reglas de la Patagonia} dice qué exigen los reglamentos. Además, según Set Fly Fishing (15/11/2026): enchufes de tipo C o I y 220 V; repelente de insectos en las zonas de dorado, donde hay mosquitos y tábanos; copias de las primeras páginas del pasaporte en distintos bolsos y una copia digital; y efectivo para las propinas, que recomienda en dólares.',
	'h_8'             => 'Paso 8. Pedí opciones',
	'p_8'             => '<strong>Contanos qué buscás y te armamos la mejor opción para vos.</strong> Fechas aproximadas, cuántos son, si viaja alguien que no pesca y qué querés pescar: consultamos a los lugares que encajan con tu pedido y en 48 horas te respondemos con lo que hay para tus fechas, lado a lado. Es gratis para vos y pagás lo mismo que si reservaras directo.',
	'cta'             => 'Pedir opciones: <a class="button dark" href="{viaje:dorado}">para dorado →</a> <a class="button dark" href="{viaje:trucha}">para trucha →</a> <a href="{viaje}">otra especie o no sé todavía</a>.',
	'h_check'         => 'Checklist antes de reservar',
	'check_l'         => [
		'Especie, zona y época elegidas, con las fechas de apertura y las vedas revisadas en el {cal|calendario}.',
		'Permiso de pesca identificado por región y cómo se saca ({n:permiso-pesca-argentina-paso-a-paso|paso a paso}).',
		'Precio comparado solo entre ofertas que cubren lo mismo: licencia, traslados, vuelos, guía, equipo y propinas.',
		'Duración, día de llegada y día de salida; habitación doble o individual; acompañante que no pesca.',
		'Pasaporte vigente (6 meses después del viaje según Set Fly Fishing) y seguro de viaje.',
		'Vuelos con margen: una noche en Buenos Aires antes del vuelo al lugar.',
		'Cañas y reels en el equipaje despachado; una muda de ropa en el de mano.',
		'Copias del pasaporte y del permiso, impresas y en el teléfono, y efectivo para las propinas.',
	],
	'faq_h'           => 'Preguntas frecuentes',
	'faq'             => [
		[ '¿Cómo se prepara un viaje de pesca a la Argentina?', 'En ocho pasos: especie, zona y época; estilo; permiso; costo; cómo funciona la semana; vuelos, traslados y equipaje; qué llevar; y pedir opciones. Cada paso de esta página lleva al detalle con su fuente y su fecha.' ],
		[ '¿Qué elijo primero: la especie, la zona o la fecha?', 'La especie, porque casi siempre decide la zona y la época. Con eso mirás el {cal|calendario de pesca}: por ejemplo, Itatí Lodge publica temporada de septiembre a mayo (Set Fly Fishing, 15/11/2026) y Los Plateados, de marzo a mayo (Tres Amigos Outfitters, 16/11/2026).' ],
		[ '¿Necesito un permiso para pescar?', 'Sí. No hay licencia nacional: hace falta el permiso de la región donde pescás, y cada región tiene su portal. Está explicado en {n:permiso-pesca-argentina-paso-a-paso|permiso de pesca en Argentina, paso a paso} (páginas oficiales leídas el 16/11/2026).' ],
		[ '¿Cuánto cuesta un viaje de pesca a la Argentina?', 'Depende del tipo: las cifras que publican los lugares van desde US$ 410 por medio día con guía hasta US$ 12.950 a US$ 14.950 por una semana de trucha de mar, cada una con su fuente y fecha en {n:cuanto-cuesta-viaje-pesca-argentina|nuestra vista general}.' ],
		[ '¿Cómo es una semana de pesca en un lodge?', 'En las páginas que leímos, 7 noches y 6 días de pesca, de sábado a sábado, en habitación doble y con dos pescadores por guía; hay también media semana y paquetes de 3 a 6 noches. El detalle está en {n:semana-de-pesca-en-lodge-argentina|cómo funciona una semana de pesca en un lodge}.' ],
		[ '¿Conviene escribirle yo a cada lugar o pedir opciones acá?', 'Podés hacer las dos cosas. Si le escribís a cada lugar, cada uno te responde con su formato, sus fechas y su lista de qué incluye. Acá escribís una vez, contás qué buscás y en 48 horas comparás las opciones lado a lado. El precio del lugar es el mismo en los dos casos: acá es gratis para vos y pagás lo mismo que si reservaras directo.' ],
		[ '¿Puedo llevar las cañas en el avión?', 'Set Fly Fishing avisa que la seguridad aeroportuaria argentina no admite cañas ni reels en la cabina y que van en el equipaje despachado (15/11/2026). Confirmalo con tu aerolínea: cada una tiene sus reglas de equipaje.' ],
		[ '¿Qué documentos necesito?', 'Pasaporte vigente (Set Fly Fishing pide al menos 6 meses de vigencia después del viaje) y el permiso de pesca de la región. Según Nervous Waters y Set Fly Fishing, los ciudadanos de EE. UU., Canadá y la Unión Europea no necesitan visa para turismo; no es una fuente oficial, confirmalo con el consulado argentino de tu país.' ],
		[ '¿Con cuánta anticipación conviene reservar?', 'Ninguna página que leímos dice cuántos meses antes. Set Fly Fishing publica una lista de preparación: a 90 días o más de la salida, el pago final, los pasajes y el seguro (15/11/2026), y las semanas tienen de 6 a 12 cupos. Lo prudente es preguntar con tiempo.' ],
		[ '¿Qué pasa si viaja alguien que no pesca?', 'Se puede, y se cobra aparte en los lugares que leímos. Los detalles y los precios publicados están en {n:semana-de-pesca-en-lodge-argentina|cómo funciona una semana de pesca en un lodge}. Es de lo primero para decirnos al pedir opciones.' ],
	],
	'cierre'          => '<em>Esta página ordena el viaje y enlaza al detalle. Todo lo atribuido sale de páginas públicas leídas entre el 30 de octubre y el 16 de noviembre de 2026, cada una con su fecha, y de nuestras notas con fecha; cambia cada temporada. No estuvimos en estos lugares: confirmá fechas, condiciones y documentos antes de reservar.</em>',
];

// =====================================================================================================================================
// ENGLISH
// =====================================================================================================================================
$N['en'] = [
	'titulo'          => 'How to plan a fishing trip to Argentina, step by step',
	'slug'            => 'how-to-plan-a-fishing-trip-to-argentina',
	'seo_title'       => 'How to plan a fishing trip to Argentina, step by step',
	'seo_description' => 'How to plan a fishing trip to Argentina in 8 steps: species and season, style, permit, cost, the week, flights and luggage, what to pack and asking for options.',
	'bajada'          => 'Eight steps to plan a fishing trip to Argentina, each with the page where the detail is: species, area and season; style; permit; cost; how the week works; flights, transfers and luggage; what to pack; and how to ask for options.',
	'datos'           => [
		'There is no national fishing license: each region has its own permit (permit step-by-step note, read on 16/11/2026).',
		'The usual pattern at a lodge is a week of 7 nights and 6 fishing days, Saturday to Saturday, in a double room with a guide shared by two anglers (pages read between 30/10 and 16/11/2026).',
		'International flights land at Ezeiza and domestic ones leave from Aeroparque; Set Fly Fishing warns that Argentine airport security does not allow rods or reels in the cabin (read on 15/11/2026).',
		'No page we read says how many months ahead to book; weeks have 6 to 12 spots.',
		'If you would rather not write to each place, tell us what you are looking for and we will build the best option for you: reply within 48 hours, free.',
	],
	'p0'              => '<strong>Short answer.</strong> A fishing trip to Argentina comes together in <strong>eight steps</strong>: choose species, area and season; choose the style you want; know which permit you need; know what it costs; understand what a fishing week looks like; sort out flights, transfers and luggage; work out what to pack; and ask for options. Each step leads to the page with the detail, with its source and the date we read it. This page puts things in order; it does not replace the detail and it is not a quote.',
	'resp'            => [
		'<strong>1. Species, area and season:</strong> the {cal|2026/27 fishing calendar} and the pillars on {n:golden-dorado-guia|golden dorado} and {n:trucha-patagonia-pilar|trout in Patagonia}.',
		'<strong>2. Style:</strong> size, numbers, sight fishing and wildlife, with what each place’s page says.',
		'<strong>3. Permit:</strong> {n:permiso-pesca-argentina-paso-a-paso|Argentina fishing license, step by step}.',
		'<strong>4. Cost:</strong> {n:cuanto-cuesta-viaje-pesca-argentina|how much an Argentina fishing trip costs in 2026/27}.',
		'<strong>5. The week:</strong> {n:semana-de-pesca-en-lodge-argentina|how a fishing week at a lodge works}.',
		'<strong>6. Flights, transfers and luggage:</strong> airports, rods in checked baggage, insurance and documents.',
		'<strong>7. What to pack:</strong> the gear section of the dorado pillar and {n:reglas-equipo-patagonia|gear and rules for Patagonia}.',
		'<strong>8. Ask for options:</strong> tell us what you are looking for and we will build the best option for you.',
	],
	'h_1'             => 'Step 1. Choose species, area and season',
	'p_1'             => 'The species almost always decides the area and the season. The {cal|fishing calendar} gathers the seasons and closed seasons of all our guides. Examples of what the pages we read say: at Itatí Lodge the season is September to May and January and February are best avoided because of the heat ({x:sff_itati|Set Fly Fishing}, 15/11/2026); at Los Plateados the steelhead season runs March to May ({x:ta_plat|Tres Amigos Outfitters}, 16/11/2026); on the Río Grande, fishing is open from 30 October 2026 to 18 April 2027 ({x:tdf_pdf|Resolución 259/26}, read on 16/11/2026). To choose in more detail: {n:golden-dorado-guia|the dorado pillar}, {n:cuando-pescar-dorado|when to fish dorado}, {n:trucha-patagonia-pilar|the trout in Patagonia pillar} and {n:cuando-pescar-trucha-patagonia|when to fish trout in Patagonia}. The guides by destination are on {esp:dorado|the dorado page} and {esp:trucha|the trout page}.',
	'h_2'             => 'Step 2. Choose the style you want',
	'p_2'             => 'Size, numbers, sight fishing and wildlife change from place to place. What each place’s page says, attributed and with the date we read it:',
	'tabla_cols'      => [ 'What you want', 'What the page says', 'Read more' ],
	'tabla_rows'      => [
		[ '<strong>Size</strong>', 'Itatí Lodge: dorado of 12 to 30 pounds or more (5 to 14 kg) ({x:sff_itati|Set Fly Fishing}, 15/11/2026). Pirá Lodge: 5 to 20 pounds or more (2 to 9 kg) ({x:nw_pira|Nervous Waters}, 13/11/2026). Pinti’s: an average of 6 to 8 pounds (3 to 4 kg) and real chances at 20-pound fish (9 kg) ({x:yd_pinti|Yellow Dog}, 13/11/2026).', '{g:itati|Itatí}, {g:esteros-del-ibera|Esteros del Iberá}, {g:ita-ibate|Itá Ibaté}' ],
		[ '<strong>Numbers</strong>', 'Pirá Lodge: dorado fishing “is not a numbers game”; five to 15 fish a day depending on conditions ({x:fwt_pira|Fly Water Travel}, 13/11/2026).', '{g:esteros-del-ibera|Esteros del Iberá}' ],
		[ '<strong>Sight fishing</strong>', 'Suindá Lodge: sight-casting to dorado on sandbanks ({x:nw_suinda|Nervous Waters}, 13/11/2026). Pinti’s: sight fishing for dorado, pirá pitá and pacú in clear water ({x:yd_pinti|Yellow Dog}, 13/11/2026).', '{g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}' ],
		[ '<strong>Wildlife and scenery</strong>', 'Pirá Lodge: more than 350 bird species in the Esteros del Iberá, a wetland of nearly 3 million acres ({x:nw_pira|Nervous Waters}, 13/11/2026).', '{g:esteros-del-ibera|Esteros del Iberá}' ],
		[ '<strong>Steelhead</strong>', 'Los Plateados: a March to May season and a program of 4 anglers ({x:ta_plat|Tres Amigos Outfitters}, 16/11/2026).', '{g:rio-santa-cruz|Santa Cruz River}' ],
		[ '<strong>Wind and weather</strong>', 'Las Buitreras: wind is a constant (Fly Water Travel, 13/11/2026). Río Manso Lodge: from November to April days run 13 to 24 °C ({x:rm_faq|Río Manso Lodge}, 30/10/2026).', '{g:rio-gallegos|Río Gallegos}, {g:bariloche|Bariloche}' ],
	],
	'p_2c'            => 'All our trout and dorado guides, with their sources, are at {g:san-martin-junin|San Martín and Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel and Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos}, {g:rio-grande|Río Grande}, {g:goya|Goya}, {g:esquina|Esquina} and {g:paso-de-la-patria|Paso de la Patria}.',
	'h_3'             => 'Step 3. Know which permit you need',
	'p_3'             => 'There is no national license: each region has its own and each has a portal. How to get it, what they ask for and how it is paid, from the official pages read on 16/11/2026: {n:permiso-pesca-argentina-paso-a-paso|Argentina fishing license, step by step}. The values by region, in pesos and approximate dollars: {n:licencias-extranjeros-argentina|fishing license for foreigners in Argentina 2026/27}.',
	'h_4'             => 'Step 4. Know what it costs',
	'p_4'             => 'The figures places publish run from US$ 410 for half a day with a guide near Bariloche to US$ 12,950 to US$ 14,950 for a week of sea-run trout in Río Grande, according to {n:cuanto-cuesta-viaje-pesca-argentina|our overview}, with the source and date of each figure. Two prices are only comparable when they cover the same things: {n:paquete-incluye-no-incluye|what a fishing package includes and what is extra}.',
	'h_5'             => 'Step 5. Understand how the week works',
	'p_5'             => 'On the pages we read, a week at a lodge is usually 7 nights and 6 fishing days, Saturday to Saturday, in a double room with two anglers per guide; there are also half weeks and packages by nights. {n:semana-de-pesca-en-lodge-argentina|How a fishing week at a lodge works} gives the formats, the arrival and departure days, what happens with a companion who does not fish and what is included.',
	'h_6'             => 'Step 6. Flights, transfers and luggage',
	'p_6'             => 'Everything below is what the pages we read say, and none is an official source: confirm with the airline and the consulate.',
	'l_6'             => [
		'<strong>Airports:</strong> international flights land at Ezeiza (EZE) and domestic flights leave from Aeroparque (AEP), an hour apart according to Set Fly Fishing (15/11/2026). For dorado in Corrientes, Set Fly Fishing names Corrientes airport (CNQ) as the preferred one and Resistencia (RES) as the alternative; for Santa Cruz, Tres Amigos Outfitters says you fly to El Calafate or Río Gallegos ({x:ta_plat|its Los Plateados page}, 16/11/2026).',
		'<strong>A night in Buenos Aires:</strong> Nervous Waters recommends spending a night in the city before the flight to the place ({x:nw_faq|its FAQ}, 15/11/2026); Set Fly Fishing advises arriving with a margin so a delay does not cost fishing days ({x:sff_info|its travel guide}, 15/11/2026).',
		'<strong>Rods and reels:</strong> Set Fly Fishing warns that Argentine airport security does not allow rods or reels in the cabin on domestic flights: they go in checked baggage. It also recommends a change of clothes in your carry-on in case your luggage is delayed.',
		'<strong>Documents:</strong> Set Fly Fishing asks for a passport valid for at least 6 months after the trip, and Nervous Waters and Set Fly Fishing say citizens of the US, Canada and the European Union do not need a visa for tourism. It is not an official source: confirm with the Argentine consulate in your country. The fishing permit is separate: step 3.',
		'<strong>Trip insurance:</strong> Set Fly Fishing recommends it and puts it on its checklist at 90 days or more before departure (15/11/2026).',
		'<strong>Flight prices:</strong> we do not publish them; they change by date and airline. Some places include charter flights between Buenos Aires and the place and others do not: each offer says so.',
	],
	'h_7'             => 'Step 7. What to pack',
	'p_7'             => 'Gear depends on the species. For dorado, the gear section of the {n:golden-dorado-guia|dorado pillar} gathers rods, lines, tippets and flies from what several places publish; for Patagonia, {n:reglas-equipo-patagonia|gear and rules for Patagonia} says what the regulations require. In addition, according to Set Fly Fishing (15/11/2026): type C or I plugs and 220 V; insect repellent in the dorado areas, where there are mosquitoes and horseflies; copies of the first pages of your passport in different bags and a digital copy; and cash for tips, which it recommends in US dollars.',
	'h_8'             => 'Step 8. Ask for options',
	'p_8'             => '<strong>Tell us what you are looking for and we will build the best option for you.</strong> Approximate dates, how many of you, whether someone who does not fish is coming and what you want to fish: we ask the places that fit your request and within 48 hours we reply with what is available for your dates, side by side. It is free for you and you pay the same as if you booked directly.',
	'cta'             => 'Ask for options: <a class="button dark" href="{viaje:dorado}">for dorado →</a> <a class="button dark" href="{viaje:trucha}">for trout →</a> <a href="{viaje}">another species or not sure yet</a>.',
	'h_check'         => 'Checklist before you book',
	'check_l'         => [
		'Species, area and season chosen, with the opening dates and closed seasons checked in the {cal|calendar}.',
		'Fishing permit identified by region and how to get it ({n:permiso-pesca-argentina-paso-a-paso|step by step}).',
		'Price compared only between offers that cover the same things: license, transfers, flights, guide, gear and tips.',
		'Length, arrival day and departure day; double or single room; a companion who does not fish.',
		'Valid passport (6 months after the trip according to Set Fly Fishing) and trip insurance.',
		'Flights with a margin: a night in Buenos Aires before the flight to the place.',
		'Rods and reels in checked baggage; a change of clothes in your carry-on.',
		'Copies of your passport and permit, printed and on your phone, and cash for tips.',
	],
	'faq_h'           => 'Frequently asked questions',
	'faq'             => [
		[ 'How do I plan a fishing trip to Argentina?', 'In eight steps: species, area and season; style; permit; cost; how the week works; flights, transfers and luggage; what to pack; and asking for options. Each step on this page leads to the detail with its source and date.' ],
		[ 'What do I choose first: the species, the area or the date?', 'The species, because it almost always decides the area and the season. Then check the {cal|fishing calendar}: for example, Itatí Lodge publishes a September to May season (Set Fly Fishing, 15/11/2026) and Los Plateados a March to May one (Tres Amigos Outfitters, 16/11/2026).' ],
		[ 'Do I need a permit to fish?', 'Yes. There is no national license: you need the permit of the region where you fish, and each region has its own portal. It is explained in {n:permiso-pesca-argentina-paso-a-paso|Argentina fishing license, step by step} (official pages read on 16/11/2026).' ],
		[ 'How much does a fishing trip to Argentina cost?', 'It depends on the type: the figures places publish run from US$ 410 for half a day with a guide to US$ 12,950 to US$ 14,950 for a week of sea-run trout, each with its source and date in {n:cuanto-cuesta-viaje-pesca-argentina|our overview}.' ],
		[ 'What is a fishing week at a lodge like?', 'On the pages we read, 7 nights and 6 fishing days, Saturday to Saturday, in a double room with two anglers per guide; there are also half weeks and packages of 3 to 6 nights. The detail is in {n:semana-de-pesca-en-lodge-argentina|how a fishing week at a lodge works}.' ],
		[ 'Should I write to each place myself or ask for options here?', 'You can do both. If you write to each place, each one answers with its own format, dates and list of what is included. Here you write once, tell us what you are looking for and within 48 hours you compare the options side by side. The place’s price is the same either way: here it is free for you and you pay the same as if you booked directly.' ],
		[ 'Can I take my rods on the plane?', 'Set Fly Fishing warns that Argentine airport security does not allow rods or reels in the cabin and that they go in checked baggage (15/11/2026). Confirm with your airline: each has its own baggage rules.' ],
		[ 'What documents do I need?', 'A valid passport (Set Fly Fishing asks for at least 6 months of validity after the trip) and the fishing permit of the region. According to Nervous Waters and Set Fly Fishing, citizens of the US, Canada and the European Union do not need a visa for tourism; it is not an official source, so confirm with the Argentine consulate in your country.' ],
		[ 'How far ahead should I book?', 'No page we read says how many months ahead. Set Fly Fishing publishes a checklist: 90 days or more before departure, the final payment, flights and insurance (15/11/2026), and weeks have 6 to 12 spots. Asking early is the prudent thing.' ],
		[ 'What if someone who does not fish comes along?', 'It is possible, and it is paid separately at the places we read. The details and published prices are in {n:semana-de-pesca-en-lodge-argentina|how a fishing week at a lodge works}. It is one of the first things to tell us when you ask for options.' ],
	],
	'cierre'          => '<em>This page puts the trip in order and links to the detail. Everything attributed comes from public pages read between 30 October and 16 November 2026, each with its date, and from our dated notes; it changes every season. We have not stayed at these places: confirm dates, conditions and documents before booking.</em>',
];

// =====================================================================================================================================
// PORTUGUÊS
// =====================================================================================================================================
$N['pt'] = [
	'titulo'          => 'Como planejar uma viagem de pesca na Argentina, passo a passo',
	'slug'            => 'como-planejar-uma-viagem-de-pesca-na-argentina',
	'seo_title'       => 'Como planejar uma viagem de pesca na Argentina passo a passo',
	'seo_description' => 'Como planejar uma viagem de pesca na Argentina em 8 passos: espécie e época, estilo, licença, custo, a semana, voos e bagagem, o que levar e como pedir opções.',
	'bajada'          => 'Oito passos para planejar uma viagem de pesca na Argentina, cada um com a página onde está o detalhe: espécie, região e época; estilo; licença; custo; como funciona a semana; voos, traslados e bagagem; o que levar; e como pedir opções.',
	'datos'           => [
		'Não existe licença nacional: cada região tem a sua licença (nota da licença passo a passo, lida em 16/11/2026).',
		'O habitual em uma pousada é uma semana de 7 noites e 6 dias de pesca, de sábado a sábado, em quarto duplo e com guia compartilhado por dois (páginas lidas entre 30/10 e 16/11/2026).',
		'Os voos internacionais chegam a Ezeiza e os internos saem de Aeroparque; a Set Fly Fishing avisa que a segurança aeroportuária argentina não admite varas nem molinetes na cabine (lido em 15/11/2026).',
		'Nenhuma página que lemos diz com quantos meses de antecedência reservar; as semanas têm de 6 a 12 vagas.',
		'Se você prefere não escrever para cada lugar, conte o que procura e montamos a melhor opção para você: resposta em 48 horas, grátis.',
	],
	'p0'              => '<strong>Resposta curta.</strong> Uma viagem de pesca na Argentina se planeja em <strong>oito passos</strong>: escolher espécie, região e época; escolher o estilo que você procura; saber qual licença é preciso; saber quanto custa; entender como é uma semana de pesca; resolver voos, traslados e bagagem; montar o que levar; e pedir opções. Cada passo leva à página onde está o detalhe, com a sua fonte e a data em que a lemos. Esta página organiza; não substitui o detalhe nem é uma cotação.',
	'resp'            => [
		'<strong>1. Espécie, região e época:</strong> o {cal|calendário de pesca 2026/27} e os pilares do {n:golden-dorado-guia|dourado} e da {n:trucha-patagonia-pilar|truta na Patagônia}.',
		'<strong>2. Estilo:</strong> tamanho, quantidade, pesca à vista e fauna, com o que dizem as páginas de cada lugar.',
		'<strong>3. Licença:</strong> {n:permiso-pesca-argentina-paso-a-paso|licença de pesca na Argentina, passo a passo}.',
		'<strong>4. Custo:</strong> {n:cuanto-cuesta-viaje-pesca-argentina|quanto custa pescar na Argentina em 2026/27}.',
		'<strong>5. A semana:</strong> {n:semana-de-pesca-en-lodge-argentina|como funciona uma semana de pesca em uma pousada}.',
		'<strong>6. Voos, traslados e bagagem:</strong> aeroportos, varas na bagagem despachada, seguro e documentos.',
		'<strong>7. O que levar:</strong> a seção de equipamento do pilar do dourado e {n:reglas-equipo-patagonia|equipamento e regras da Patagônia}.',
		'<strong>8. Pedir opções:</strong> conte o que você procura e montamos a melhor opção para você.',
	],
	'h_1'             => 'Passo 1. Escolha espécie, região e época',
	'p_1'             => 'A espécie quase sempre decide a região e a época. O {cal|calendário de pesca} reúne as temporadas e os períodos de defeso de todos os nossos guias. Exemplos do que dizem as páginas que lemos: na Itatí Lodge, a temporada vai de setembro a maio e convém evitar janeiro e fevereiro pelo calor ({x:sff_itati|Set Fly Fishing}, 15/11/2026); em Los Plateados, a temporada do steelhead vai de março a maio ({x:ta_plat|Tres Amigos Outfitters}, 16/11/2026); no rio Grande, a pesca está liberada de 30 de outubro de 2026 a 18 de abril de 2027 ({x:tdf_pdf|Resolución 259/26}, lida em 16/11/2026). Para escolher com mais detalhe: {n:golden-dorado-guia|o pilar do dourado}, {n:cuando-pescar-dorado|quando pescar dourado}, {n:trucha-patagonia-pilar|o pilar da truta na Patagônia} e {n:cuando-pescar-trucha-patagonia|quando pescar truta na Patagônia}. Os guias por destino estão em {esp:dorado|a ficha do dourado} e {esp:trucha|a ficha da truta}.',
	'h_2'             => 'Passo 2. Escolha o estilo que você procura',
	'p_2'             => 'Tamanho, quantidade, pesca à vista e fauna mudam de um lugar para outro. O que diz a página de cada um, atribuído e com a data de leitura:',
	'tabla_cols'      => [ 'O que você procura', 'O que diz a página', 'Onde ler mais' ],
	'tabla_rows'      => [
		[ '<strong>Tamanho</strong>', 'Itatí Lodge: dourados de 12 a 30 libras ou mais (de 5 a 14 kg) ({x:sff_itati|Set Fly Fishing}, 15/11/2026). Pirá Lodge: de 5 a 20 libras ou mais (de 2 a 9 kg) ({x:nw_pira|Nervous Waters}, 13/11/2026). Pinti’s: média de 6 a 8 libras (de 3 a 4 kg) e chances reais de peixes de 20 libras (9 kg) ({x:yd_pinti|Yellow Dog}, 13/11/2026).', '{g:itati|Itatí}, {g:esteros-del-ibera|Esteros del Iberá}, {g:ita-ibate|Itá Ibaté}' ],
		[ '<strong>Quantidade</strong>', 'Pirá Lodge: a pesca do dourado «não é um jogo de números»; de 5 a 15 peixes por dia conforme as condições ({x:fwt_pira|Fly Water Travel}, 13/11/2026).', '{g:esteros-del-ibera|Esteros del Iberá}' ],
		[ '<strong>Pesca à vista</strong>', 'Suindá Lodge: pesca à vista de dourados em bancos de areia ({x:nw_suinda|Nervous Waters}, 13/11/2026). Pinti’s: pesca à vista de dourados, pirá pitá e pacu com a água clara ({x:yd_pinti|Yellow Dog}, 13/11/2026).', '{g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}' ],
		[ '<strong>Fauna e paisagem</strong>', 'Pirá Lodge: mais de 350 espécies de aves nos Esteros del Iberá, uma área alagada de quase 3 milhões de acres ({x:nw_pira|Nervous Waters}, 13/11/2026).', '{g:esteros-del-ibera|Esteros del Iberá}' ],
		[ '<strong>Steelhead</strong>', 'Los Plateados: temporada de março a maio e programa de 4 pescadores ({x:ta_plat|Tres Amigos Outfitters}, 16/11/2026).', '{g:rio-santa-cruz|Río Santa Cruz}' ],
		[ '<strong>Vento e clima</strong>', 'Las Buitreras: o vento é constante (Fly Water Travel, 13/11/2026). Río Manso Lodge: de novembro a abril os dias ficam entre 13 e 24 °C ({x:rm_faq|Río Manso Lodge}, 30/10/2026).', '{g:rio-gallegos|Río Gallegos}, {g:bariloche|Bariloche}' ],
	],
	'p_2c'            => 'Todos os nossos guias de truta e de dourado, com as suas fontes, estão em {g:san-martin-junin|San Martín e Junín de los Andes}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel e Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos}, {g:rio-grande|Río Grande}, {g:goya|Goya}, {g:esquina|Esquina} e {g:paso-de-la-patria|Paso de la Patria}.',
	'h_3'             => 'Passo 3. Saber qual licença é preciso',
	'p_3'             => 'Não existe licença nacional: cada região tem a sua e cada uma tem um portal. Como tirar, o que pedem e como se paga, com as páginas oficiais lidas em 16/11/2026: {n:permiso-pesca-argentina-paso-a-paso|licença de pesca na Argentina, passo a passo}. Os valores por região, em pesos e em dólares aproximados: {n:licencias-extranjeros-argentina|licença de pesca para estrangeiros na Argentina 2026/27}.',
	'h_4'             => 'Passo 4. Saber quanto custa',
	'p_4'             => 'Os valores que os lugares publicam vão de US$ 410 por meio dia com guia perto de Bariloche a US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar em Río Grande, segundo {n:cuanto-cuesta-viaje-pesca-argentina|a nossa visão geral}, com a fonte e a data de cada valor. Dois preços só se comparam quando cobrem o mesmo: {n:paquete-incluye-no-incluye|o que inclui um pacote de pesca e o que custa à parte}.',
	'h_5'             => 'Passo 5. Entender como funciona a semana',
	'p_5'             => 'Nas páginas que lemos, uma semana em uma pousada costuma ser de 7 noites e 6 dias de pesca, de sábado a sábado, em quarto duplo e com dois pescadores por guia; também há meia semana e pacotes por noites. {n:semana-de-pesca-en-lodge-argentina|Como funciona uma semana de pesca em uma pousada} traz os formatos, os dias de chegada e saída, o que acontece com um acompanhante que não pesca e o que está incluído.',
	'h_6'             => 'Passo 6. Voos, traslados e bagagem',
	'p_6'             => 'Tudo o que segue é o que dizem as páginas que lemos, e nenhuma é fonte oficial: confirme com a companhia aérea e com o consulado.',
	'l_6'             => [
		'<strong>Aeroportos:</strong> os voos internacionais chegam a Ezeiza (EZE) e os internos saem de Aeroparque (AEP), a uma hora de distância segundo a Set Fly Fishing (15/11/2026). Para o dourado em Corrientes, a Set Fly Fishing cita o aeroporto de Corrientes (CNQ) como o preferido e o de Resistencia (RES) como alternativa; para Santa Cruz, a Tres Amigos Outfitters diz que se voa a El Calafate ou a Río Gallegos ({x:ta_plat|a página de Los Plateados}, 16/11/2026).',
		'<strong>Uma noite em Buenos Aires:</strong> a Nervous Waters recomenda passar uma noite na cidade antes do voo até o lugar ({x:nw_faq|as perguntas frequentes}, 15/11/2026); a Set Fly Fishing aconselha chegar com folga para não perder dias de pesca por um atraso ({x:sff_info|o guia de viagem}, 15/11/2026).',
		'<strong>Varas e molinetes:</strong> a Set Fly Fishing avisa que a segurança aeroportuária argentina não admite varas nem molinetes na cabine dos voos internos: vão na bagagem despachada. Recomenda também levar uma muda de roupa na bagagem de mão, caso a bagagem atrase.',
		'<strong>Documentos:</strong> a Set Fly Fishing pede passaporte com pelo menos 6 meses de validade depois da viagem, e a Nervous Waters e a Set Fly Fishing dizem que cidadãos dos EUA, do Canadá e da União Europeia não precisam de visto para turismo. Não é fonte oficial: confirme com o consulado argentino do seu país. A licença de pesca é à parte: passo 3.',
		'<strong>Seguro-viagem:</strong> a Set Fly Fishing o recomenda e o põe na sua lista de preparação a 90 dias ou mais da saída (15/11/2026).',
		'<strong>Preços de voos:</strong> não os publicamos; mudam por data e companhia. Alguns lugares incluem os voos fretados entre Buenos Aires e o lugar e outros não: cada oferta diz.',
	],
	'h_7'             => 'Passo 7. O que levar',
	'p_7'             => 'O equipamento depende da espécie. Para o dourado, a seção de equipamento do {n:golden-dorado-guia|pilar do dourado} reúne varas, linhas, tippets e moscas com o que vários lugares publicam; para a Patagônia, {n:reglas-equipo-patagonia|equipamento e regras da Patagônia} diz o que os regulamentos exigem. Além disso, segundo a Set Fly Fishing (15/11/2026): tomadas do tipo C ou I e 220 V; repelente de insetos nas áreas de dourado, onde há mosquitos e mutucas; cópias das primeiras páginas do passaporte em bolsas diferentes e uma cópia digital; e dinheiro em espécie para as gorjetas, que recomenda em dólares.',
	'h_8'             => 'Passo 8. Peça opções',
	'p_8'             => '<strong>Conte o que você procura e montamos a melhor opção para você.</strong> Datas aproximadas, quantos são, se viaja alguém que não pesca e o que você quer pescar: consultamos os lugares que combinam com o seu pedido e em 48 horas respondemos com o que há para as suas datas, lado a lado. É grátis para você e você paga o mesmo que pagaria reservando direto.',
	'cta'             => 'Pedir opções: <a class="button dark" href="{viaje:dorado}">para dourado →</a> <a class="button dark" href="{viaje:trucha}">para truta →</a> <a href="{viaje}">outra espécie ou ainda não sei</a>.',
	'h_check'         => 'Checklist antes de reservar',
	'check_l'         => [
		'Espécie, região e época escolhidas, com as datas de abertura e os períodos de defeso conferidos no {cal|calendário}.',
		'Licença de pesca identificada por região e como tirar ({n:permiso-pesca-argentina-paso-a-paso|passo a passo}).',
		'Preço comparado só entre ofertas que cobrem o mesmo: licença, traslados, voos, guia, equipamento e gorjetas.',
		'Duração, dia de chegada e dia de saída; quarto duplo ou individual; acompanhante que não pesca.',
		'Passaporte válido (6 meses depois da viagem segundo a Set Fly Fishing) e seguro-viagem.',
		'Voos com folga: uma noite em Buenos Aires antes do voo até o lugar.',
		'Varas e molinetes na bagagem despachada; uma muda de roupa na de mão.',
		'Cópias do passaporte e da licença, impressas e no celular, e dinheiro em espécie para as gorjetas.',
	],
	'faq_h'           => 'Perguntas frequentes',
	'faq'             => [
		[ 'Como se planeja uma viagem de pesca na Argentina?', 'Em oito passos: espécie, região e época; estilo; licença; custo; como funciona a semana; voos, traslados e bagagem; o que levar; e pedir opções. Cada passo desta página leva ao detalhe com a sua fonte e a sua data.' ],
		[ 'O que escolho primeiro: a espécie, a região ou a data?', 'A espécie, porque quase sempre decide a região e a época. Com isso você olha o {cal|calendário de pesca}: por exemplo, a Itatí Lodge publica temporada de setembro a maio (Set Fly Fishing, 15/11/2026) e Los Plateados, de março a maio (Tres Amigos Outfitters, 16/11/2026).' ],
		[ 'Preciso de licença para pescar?', 'Sim. Não existe licença nacional: é preciso a licença da região onde você pesca, e cada região tem o seu portal. Está explicado em {n:permiso-pesca-argentina-paso-a-paso|licença de pesca na Argentina, passo a passo} (páginas oficiais lidas em 16/11/2026).' ],
		[ 'Quanto custa uma viagem de pesca na Argentina?', 'Depende do tipo: os valores que os lugares publicam vão de US$ 410 por meio dia com guia a US$ 12.950 a US$ 14.950 por uma semana de truta-do-mar, cada um com a sua fonte e data em {n:cuanto-cuesta-viaje-pesca-argentina|nossa visão geral}.' ],
		[ 'Como é uma semana de pesca em uma pousada?', 'Nas páginas que lemos, 7 noites e 6 dias de pesca, de sábado a sábado, em quarto duplo e com dois pescadores por guia; há também meia semana e pacotes de 3 a 6 noites. O detalhe está em {n:semana-de-pesca-en-lodge-argentina|como funciona uma semana de pesca em uma pousada}.' ],
		[ 'Convém escrever eu mesmo para cada lugar ou pedir opções aqui?', 'Você pode fazer as duas coisas. Se escreve para cada lugar, cada um responde com o seu formato, as suas datas e a sua lista do que inclui. Aqui você escreve uma vez, conta o que procura e em 48 horas compara as opções lado a lado. O preço do lugar é o mesmo nos dois casos: aqui é grátis para você e você paga o mesmo que pagaria reservando direto.' ],
		[ 'Posso levar as varas no avião?', 'A Set Fly Fishing avisa que a segurança aeroportuária argentina não admite varas nem molinetes na cabine e que vão na bagagem despachada (15/11/2026). Confirme com a sua companhia aérea: cada uma tem as suas regras de bagagem.' ],
		[ 'Que documentos preciso?', 'Passaporte válido (a Set Fly Fishing pede pelo menos 6 meses de validade depois da viagem) e a licença de pesca da região. Segundo a Nervous Waters e a Set Fly Fishing, cidadãos dos EUA, do Canadá e da União Europeia não precisam de visto para turismo; não é fonte oficial, confirme com o consulado argentino do seu país.' ],
		[ 'Com quanta antecedência convém reservar?', 'Nenhuma página que lemos diz com quantos meses. A Set Fly Fishing publica uma lista de preparação: a 90 dias ou mais da saída, o pagamento final, as passagens e o seguro (15/11/2026), e as semanas têm de 6 a 12 vagas. O prudente é perguntar com tempo.' ],
		[ 'E se viaja alguém que não pesca?', 'É possível, e é cobrado à parte nos lugares que lemos. Os detalhes e os preços publicados estão em {n:semana-de-pesca-en-lodge-argentina|como funciona uma semana de pesca em uma pousada}. É das primeiras coisas para nos dizer ao pedir opções.' ],
	],
	'cierre'          => '<em>Esta página organiza a viagem e leva ao detalhe. Tudo o que é atribuído vem de páginas públicas lidas entre 30 de outubro e 16 de novembro de 2026, cada uma com a sua data, e de nossas notas com data; muda a cada temporada. Não estivemos nesses lugares: confirme datas, condições e documentos antes de reservar.</em>',
];

$f_ = fn( string $es, string $en, string $pt, string $f, int $o = 0 ) => [ 'es' => $es, 'en' => $en, 'pt' => $pt, 'f' => $f, 'o' => $o ];
$fuente_nombre = [
	'sff_info'  => $f_( 'Set Fly Fishing: guía de información de viaje (aeropuertos, equipaje, documentos, seguro, lista de preparación)', 'Set Fly Fishing: trip information package (airports, luggage, documents, insurance, checklist)', 'Set Fly Fishing: guia de informações de viagem (aeroportos, bagagem, documentos, seguro, lista de preparação)', '20261115' ),
	'sff_itati' => $f_( 'Set Fly Fishing: Itati Lodge (tamaño de los dorados, temporada)', 'Set Fly Fishing: Itati Lodge (dorado size, season)', 'Set Fly Fishing: Itati Lodge (tamanho dos dourados, temporada)', '20261115' ),
	'nw_faq'    => $f_( 'Nervous Waters: preguntas frecuentes (cómo llegar, visa, una noche en Buenos Aires)', 'Nervous Waters: frequently asked questions (getting there, visa, a night in Buenos Aires)', 'Nervous Waters: perguntas frequentes (como chegar, visto, uma noite em Buenos Aires)', '20261115' ),
	'nw_pira'   => $f_( 'Nervous Waters: Pirá Lodge (tamaño del dorado, aves)', 'Nervous Waters: Pirá Lodge (dorado size, birds)', 'Nervous Waters: Pirá Lodge (tamanho do dourado, aves)', '20261113' ),
	'nw_suinda' => $f_( 'Nervous Waters: Suindá Lodge (pesca a la vista en bancos de arena)', 'Nervous Waters: Suindá Lodge (sight-casting on sandbanks)', 'Nervous Waters: Suindá Lodge (pesca à vista em bancos de areia)', '20261113' ),
	'fwt_pira'  => $f_( 'Fly Water Travel: Pira Lodge (cantidad de peces por día)', 'Fly Water Travel: Pira Lodge (fish per day)', 'Fly Water Travel: Pira Lodge (peixes por dia)', '20261113' ),
	'yd_pinti'  => $f_( 'Yellow Dog Flyfishing: Pinti’s Dorado on the Fly (tamaño, pesca a la vista)', 'Yellow Dog Flyfishing: Pinti’s Dorado on the Fly (size, sight fishing)', 'Yellow Dog Flyfishing: Pinti’s Dorado on the Fly (tamanho, pesca à vista)', '20261113' ),
	'ta_plat'   => $f_( 'Tres Amigos Outfitters: Los Plateados (temporada del steelhead, cómo llegar)', 'Tres Amigos Outfitters: Los Plateados (steelhead season, getting there)', 'Tres Amigos Outfitters: Los Plateados (temporada do steelhead, como chegar)', '20261116' ),
	'tdf_pdf'   => $f_( 'Resolución 259/26 de la Secretaría de Ambiente de Tierra del Fuego (temporada de pesca 2026)', 'Resolución 259/26 of the Tierra del Fuego Secretaría de Ambiente (2026 fishing season)', 'Resolución 259/26 da Secretaría de Ambiente da Terra do Fogo (temporada de pesca 2026)', '20261116', 1 ),
	'rm_faq'    => $f_( 'Río Manso Lodge: preguntas frecuentes (temperaturas de la temporada)', 'Río Manso Lodge: frequently asked questions (season temperatures)', 'Río Manso Lodge: perguntas frequentes (temperaturas da temporada)', '20261030' ),
];

$origen = 'Hub «Cómo preparar un viaje de pesca a la Argentina, paso a paso», 16/11/2026 (pedido del dueño tras una conversación con Grok que mostró los huecos de un buen sitio de preparación de viaje; nota 4 de 4). Ordena en ocho pasos y ENLAZA a lo ya publicado (calendario, pilares del dorado y de la trucha, guías, notas de permiso, costo y semana); los datos nuevos salen solo de páginas públicas ya leídas con investigar.yml (rama investigacion: Set Fly Fishing y Nervous Waters del 15/11/2026, Fly Water Travel, Nervous Waters y Yellow Dog del 13/11/2026, Tres Amigos Outfitters del 16/11/2026, Resolución 259/26 del 16/11/2026, Río Manso Lodge del 30/10/2026), siempre atribuidos y con fecha. Vuelos, traslados y equipaje solo con lo que dicen esas páginas (ninguna es fuente oficial: la nota lo dice y manda a confirmar con la aerolínea y el consulado); sin precios de vuelos. Sin porcentajes ni comisiones, sin nombres de personas, teléfonos ni correos. «Contanos qué buscás y te armamos la mejor opción para vos» es la frase del dueño del 16/11/2026, anclada al pedido del lector. Foto provisoria: la de la nota precio-viaje-dorado-lodge (Commons, rodoluca). Docs: docs/estado.md.';

$armar = function ( array $n ): string {
	$pasos = '';
	foreach ( [ 1, 3, 4, 5 ] as $k ) {
		$pasos .= pm_sc_h2( $n[ "h_$k" ] ) . pm_sc_p( $n[ "p_$k" ] );
		if ( 1 === $k ) {
			$pasos .= pm_sc_h2( $n['h_2'] ) . pm_sc_p( $n['p_2'] ) . pm_tp_tabla( $n['tabla_cols'], $n['tabla_rows'] ) . pm_sc_p( $n['p_2c'] );
		}
	}
	return pm_sc_p( $n['p0'] ) . pm_sc_lista( $n['resp'] ) . $pasos
		. pm_sc_h2( $n['h_6'] ) . pm_sc_p( $n['p_6'] ) . pm_sc_lista( $n['l_6'] )
		. pm_sc_h2( $n['h_7'] ) . pm_sc_p( $n['p_7'] )
		. pm_sc_h2( $n['h_8'] ) . pm_sc_p( $n['p_8'] ) . pm_sc_p( $n['cta'] )
		. pm_sc_h2( $n['h_check'] ) . pm_sc_lista( $n['check_l'] )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] )
		. pm_sc_p( $n['cierre'] );
};

$enlace_ancla = [
	'es' => 'Cómo preparar un viaje de pesca a la Argentina, paso a paso',
	'en' => 'How to plan a fishing trip to Argentina, step by step',
	'pt' => 'Como planejar uma viagem de pesca na Argentina, passo a passo',
];
$destinos_enlace = [
	[
		'tipo' => 'pm_nota', 'claves' => [ 'permiso-pesca-argentina-paso-a-paso', 'cuanto-cuesta-viaje-pesca-argentina', $sem, 'golden-dorado-guia', 'trucha-patagonia-pilar' ],
		'ancla' => [ 'es' => '/Respuesta corta/u', 'en' => '/Short answer/u', 'pt' => '/Resposta curta/u' ],
		'texto' => [
			'es' => 'Para ordenar todo el viaje, de la especie al pedido de opciones, en ocho pasos: %s.',
			'en' => 'To put the whole trip in order, from the species to asking for options, in eight steps: %s.',
			'pt' => 'Para organizar toda a viagem, da espécie ao pedido de opções, em oito passos: %s.',
		],
	],
];

/** Resolve {g:clave}, {n:clave|texto}, {g:clave|texto}, {u:clave}, {viaje}, {viaje:especie}, {cal}, {esp:clave}; fails on anything left over. */
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
	$html = preg_replace_callback( '/\{x:([a-z0-9_]+)\|([^}]+)\}/', function ( $m ) use ( $u ) {
		isset( $u[ $m[1] ] ) || WP_CLI::error( "enlace externo desconocido {$m[1]}" );
		return '<a href="' . esc_url( $u[ $m[1] ] ) . '" rel="noopener">' . $m[2] . '</a>';
	}, $html );
	$html = preg_replace_callback( '/\{u:([a-z0-9_]+)\}/', function ( $m ) use ( $u ) {
		isset( $u[ $m[1] ] ) || WP_CLI::error( "enlace externo desconocido {$m[1]}" );
		return esc_url( $u[ $m[1] ] );
	}, $html );
	$html = preg_replace_callback( '/\{esp:([a-z0-9-]+)\|([^}]+)\}/', function ( $m ) use ( $i ) {
		pm_buscar( 'pm_especie', $m[1], $i ) || pm_buscar( 'pm_especie', $m[1], 'es' ) || WP_CLI::error( "No encuentro la especie {$m[1]}." );
		return '<a href="' . esc_url( pm_url_especie( $m[1], $i ) ) . '">' . $m[2] . '</a>';
	}, $html );
	$html = preg_replace_callback( '/\{viaje:([a-z0-9-]+)\}/', fn( $m ) => esc_url( pm_url_viaje( $i ) . '?especie=' . $m[1] ), $html );
	$html = preg_replace_callback( '/\{cal\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( pm_url_calendario( $i ) ) . '">' . $m[1] . '</a>', $html );
	$html = str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) ), $html );
	$html = str_replace( '{cal}', esc_url( pm_url_calendario( $i ) ), $html );
	if ( preg_match( '/\{(n:|g:|u:|x:|viaje|cal|esp:)[^}]*\}?/', $html, $m ) ) {
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
	$plano  = wp_strip_all_tags( $cuerpo . ' ' . $n['titulo'] . ' ' . $n['bajada'] . ' ' . implode( ' ', $n['datos'] ) );
	// Copy rules (owner, 16/11/2026): no "agency"/"operator" as a category towards the visitor, no commissions, deposits or refunds, no rankings, no contact data.
	if ( preg_match( '/PENDIENTE|no pudimos|n[ãa]o pudemos|couldn.t|agencia|agency|agência|operador|operator|operadora|comisi[óo]n|commission|comiss[ãa]o|dep[óo]sito|deposit|reembolso|refund|mejor precio|best price|cheapest|más barat|mais barat|los mejores|the best lodges|os melhores|ahorr|@[a-z0-9-]+\.[a-z]|\+\d\d [\d ]{6,}/iu', $plano, $mm ) ) {
		WP_CLI::error( "$i: el texto tiene una expresión prohibida: {$mm[0]}." );
	}
	$datos   = array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] );
	$fuentes = [];
	foreach ( $fuente_nombre as $k => $f ) {
		$fuentes[] = [ 'nombre' => $f[ $i ], 'url' => $f['url'] ?? $u[ $k ] ?? '', 'oficial' => $f['o'], 'consultada' => $f['f'] ];
	}
	foreach ( $fuentes as $f ) {
		$f['url'] || WP_CLI::error( "$i: fuente sin URL: {$f['nombre']}" );
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
		if ( $especie_nota ) {
			$campos['nota_especie'] = $especie_nota;
		}
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
	$ok   = get_post( $id )->post_name === $n['slug'] && pm_clave_de( $id ) === $clave && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $datos ) && false !== strpos( $cont, 'pm-faq' ) && count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && ! preg_match( '/\{(n|g|u|x|esp):|\{viaje|\{cal\}/', $cont );
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
			$pos = pm_sc_pos_tras_parrafo( $cont, $dest['ancla'][ $i ] );
			$via = 'tras el párrafo ancla';
			if ( null === $pos ) {
				$pos = pm_sc_pos_tras_parrafo( $cont, '/./u' );
				$via = 'tras el primer párrafo';
			}
			if ( null === $pos ) {
				WP_CLI::warning( "{$dest['tipo']} $c ($i) #{$p->ID}: sin lugar para el enlace; no lo agrego a ciegas." );
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
