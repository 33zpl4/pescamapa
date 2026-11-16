<?php
/**
 * Note "How a fishing week at an Argentina lodge works" (es/en/pt siblings, clave semana-de-pesca-en-lodge-argentina), 16/11/2026.
 * Why: owner's review (16/11/2026): a conversation with Grok showed the gaps of a good trip-preparation site; this is note 3 of 4 (after the permit step by step and the trip cost
 * overview): what a week at a lodge looks like (formats, arrival and departure days, anglers per guide, room, companion, what is included, weather, when to book, how availability is confirmed).
 * Built ONLY from public pages already read with investigar.yml (branch investigacion: Fly Water Travel pages of Itatí, Pirá, Dorados on the Fly, Las Buitreras and Glencross; Nervous Waters
 * Pirá, Suindá and FAQ; Set Fly Fishing Itatí and trip information; Yellow Dog on Pinti’s; Jurassic Lake rates and location; Tres Amigos Outfitters Los Plateados; Río Manso Lodge rates and
 * FAQ) and from our dated price notes. Each fact names the page and the date it was read. NOT written, on purpose: that some places let you fish fewer days at the week's price (known from real
 * enquiries, no public page says it), "book 12-18 months ahead" (no source), weekly prices (they live in the price notes), payment and cancellation terms, names of people, phones, e-mails,
 * commissions. Copy rules of 16/11/2026: no "agency"/"operator" towards the visitor; the CTA is "tell us what you are looking for and we build the best option for you".
 * Inbound links: one paragraph after the short answer of four price notes. The hub note (script preparar-viaje-hub-2026-11-16) links to this one afterwards.
 * Cover photo: the one already loaded for the note `paquete-incluye-no-incluye` (Commons, provisional, see docs/estado.md).
 * FAQ: group block `pm-faq` (pm_sc_faq): the visible FAQ is the FAQPage JSON-LD source.
 * Idempotent: a note that exists (same clave + language) is never touched. Dry run first ($aplicar = false). Run with wp-script.yml (script = semana-lodge-2026-11-16).
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
$clave        = 'semana-de-pesca-en-lodge-argentina';
$foto_de      = 'paquete-incluye-no-incluye'; // note whose cover photo is reused (Commons, silhouette of a boat at sunset)
$especie_nota = '';
$nota_tipo    = 'como';

// External links: public pages read between 30/10/2026 and 16/11/2026 (Actions runs of investigar.yml, branch investigacion); the date of each is in $fuente_nombre.
$u = [
	'fwt_itati' => 'https://farbank.com/products/fly-water-travel-itati-lodge',
	'fwt_pira'  => 'https://farbank.com/products/fly-water-travel-pira-lodge',
	'fwt_pinti' => 'https://farbank.com/products/fly-water-travel-dorados-on-the-fly',
	'fwt_buit'  => 'https://farbank.com/products/fly-water-travel-las-buitreras-lodge',
	'fwt_glen'  => 'https://farbank.com/products/fly-water-travel-estancia-glencross',
	'nw_pira'   => 'https://www.nervouswaters.com/the-lodges/pira-lodge/',
	'nw_suinda' => 'https://www.nervouswaters.com/the-lodges/suinda-lodge/',
	'nw_faq'    => 'https://www.nervouswaters.com/faqs/',
	'sff_itati' => 'https://setflyfishing.com/en/itati-lodge/',
	'sff_info'  => 'https://setflyfishing.com/en/trip-information-package/',
	'yd_pinti'  => 'https://www.yellowdogflyfishing.com/products/pintis-dorado-fly',
	'jl_rates'  => 'https://www.jurassiclake.com/rates',
	'jl_loc'    => 'https://www.jurassiclake.com/location',
	'ta_plat'   => 'https://www.tresamigosoutfitters.com/los-plateados/',
	'ta_sc'     => 'https://www.tresamigosoutfitters.com/santa-cruz/',
	'rm_rates'  => 'https://www.riomansolodge.com/lodge-rates-details/',
	'rm_faq'    => 'https://www.riomansolodge.com/faqs/',
];

$N = [];

// =====================================================================================================================================
// ESPAÑOL
// =====================================================================================================================================
$N['es'] = [
	'titulo'          => 'Cómo funciona una semana de pesca en un lodge en Argentina',
	'slug'            => 'como-funciona-una-semana-de-pesca-en-un-lodge',
	'seo_title'       => 'Cómo funciona una semana de pesca en un lodge en Argentina',
	'seo_description' => 'Cómo funciona una semana de pesca en un lodge en Argentina: formatos, llegada y salida, pescadores por guía, habitación y qué incluye. Leído en nov. de 2026.',
	'bajada'          => 'Cómo se arma una semana de pesca en un lodge: formatos (semana completa, media semana y paquetes por noches), días de llegada y salida, pescadores por guía, habitación, acompañante y qué suele incluir. Cada dato con la página que lo dice y la fecha en que la leímos.',
	'datos'           => [
		'Lo habitual en las páginas que leímos: 7 noches y 6 días de pesca (6,5 en algunas), con llegada y salida en sábado; hay media semana y paquetes de 3 a 6 noches (leídas entre el 30/10 y el 16/11/2026).',
		'Pescadores por guía o bote: de a dos en los lugares de dorado y de trucha de mar que leímos; un guía cada dos o tres pescadores en Jurassic Lake.',
		'La habitación doble es la base de los precios; la individual se cobra aparte: 50 % según la página de un lugar de Itá Ibaté y 60 % en uno de Corrientes según nuestra nota.',
		'El acompañante que no pesca existe y se paga aparte: US$ 450 por noche en un lugar, US$ 650 en otros dos y US$ 800 en un tercero (páginas leídas el 10 y el 13/11/2026).',
		'Ninguna página dice con cuántos meses de anticipación reservar; una de ellas pone el pasaje y el seguro de viaje «a 90 días o más» de la salida.',
	],
	'p0'              => '<strong>Respuesta corta.</strong> Una semana de pesca en un lodge es, en casi todas las páginas que leímos, un programa cerrado de <strong>7 noches y 6 días de pesca</strong> (6,5 en algunas), de sábado a sábado, en habitación doble, con comidas y guía compartido de a dos; según el lugar incluye también equipo, traslados y licencia. Además hay media semana y paquetes de 3 a 6 noches. Esta página junta lo que dicen las páginas de varios lugares, cada dato con su fuente y la fecha en que la leímos. No es una regla general ni una cotización para tus fechas.',
	'resp'            => [
		'<strong>Formatos:</strong> semana completa de 7 noches, media semana (3 o 4 noches) y paquetes de 3 a 6 noches.',
		'<strong>Llegada y salida:</strong> de sábado a sábado en la mayoría; hay semanas de viernes a viernes, de sábado a viernes y medias semanas de martes a sábado o de sábado a martes.',
		'<strong>Pesca:</strong> dos sesiones por día, mañana y tarde, con almuerzo y pausa en el medio; de a dos pescadores por guía en casi todos.',
		'<strong>Habitación:</strong> doble por defecto; la individual y el acompañante que no pesca se cobran aparte.',
		'<strong>Cambia de un lugar a otro:</strong> licencia, vuelos, traslados, equipo y propinas.',
		'<strong>Disponibilidad:</strong> la confirmamos nosotros cuando nos pedís opciones.',
	],
	'h_formatos'      => 'Los formatos: semana completa, media semana y paquetes por noches',
	'formatos_i'      => 'Lo que publica la página de cada lugar, leída entre el 30/10 y el 16/11/2026. Algunas páginas cuentan medio día de pesca más (6,5 días) en la semana de 7 noches; la columna copia lo que dice cada una.',
	'tabla_cols'      => [ 'Lugar', 'Formatos publicados', 'Llegada y salida', 'Pescadores', 'Fuente y fecha' ],
	'tabla_rows'      => [
		[ '<strong>Itatí Lodge</strong> ({g:itati|Itatí}, Corrientes)', '7 noches y 6 días de pesca; 4 noches y 3 días', 'Día 1: llegada a Buenos Aires y noche en hotel (no incluida); día 2: vuelo chárter al lugar', '8 por semana (Fly Water Travel); 10 en 5 habitaciones (Set Fly Fishing)', '{x:fwt_itati|Fly Water Travel}, 13/11/2026; {x:sff_itati|Set Fly Fishing}, 15/11/2026' ],
		[ '<strong>Pirá Lodge</strong> ({g:esteros-del-ibera|Esteros del Iberá}, Corrientes)', '7 noches y 6 días; 6 y 5; 5 y 4; 4 y 3; 3 y 2', 'Viaje de ida de dos días, seis días de pesca seguidos y regreso el día 9 del itinerario', '10', '{x:fwt_pira|Fly Water Travel} y {x:nw_pira|Nervous Waters}, 13/11/2026' ],
		[ '<strong>Suindá Lodge</strong> ({g:itati|Itatí}, Corrientes)', 'Los mismos cinco programas, de 7 noches y 6 días a 3 noches y 2 días', 'La página no lo dice', '10', '{x:nw_suinda|Nervous Waters}, 13/11/2026' ],
		[ '<strong>Pinti’s Dorado on the Fly</strong> ({g:ita-ibate|Itá Ibaté}, Corrientes)', 'Lo típico: 7 noches y 6 días; también 6 y 5, 5 y 4, 4 y 3', 'La mayoría de los paquetes van de sábado a sábado; se aceptan estadías más cortas (Yellow Dog)', '8', '{x:yd_pinti|Yellow Dog Flyfishing} y {x:fwt_pinti|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Las Buitreras</strong> ({g:rio-gallegos|río Gallegos}, Santa Cruz)', 'Semana de sábado a sábado, con seis días de pesca completos de domingo a viernes', 'Sábado: vuelo a El Calafate y traslado; sábado siguiente: regreso', '12', '{x:fwt_buit|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Glencross</strong> (alto río Gallegos, Santa Cruz)', 'Semana completa de sábado a sábado; media semana de miércoles a sábado o de sábado a miércoles', 'Sábado: búsqueda en el aeropuerto de Río Gallegos', '6', '{x:fwt_glen|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Jurassic Lake</strong> ({g:jurassic-lake|Santa Cruz})', '7 noches y 6,5 días (sábado a sábado); 4 noches y 3,5 días (martes a sábado, enero a abril); 3 noches y 2,5 días (sábado a martes, enero a abril)', 'Vuelo chárter desde El Calafate cada sábado por la mañana', 'Un guía cada dos o tres', '{x:jl_rates|jurassiclake.com, tarifas}, 10/11/2026; {x:jl_loc|ubicación}, 30/10/2026' ],
		[ '<strong>Los Plateados</strong> ({g:rio-santa-cruz|río Santa Cruz}, steelhead)', 'Media semana o semana completa; programa de 6 días de pesca', 'De sábado a viernes', '4 (máximo 12 por semana en el lugar)', '{x:ta_plat|Tres Amigos Outfitters}, 16/11/2026' ],
		[ '<strong>Villa María Lodge</strong> ({g:rio-grande|Río Grande}, Tierra del Fuego)', '7 noches y 6,5 días de pesca', 'De viernes a viernes', 'Dos por guía', '{n:precio-pesca-patagonia-sur|Nuestra nota de precios} (Nervous Waters, 13/11/2026)' ],
	],
	'formatos_c'      => 'Para una semana de trucha en la Patagonia norte, {n:precio-semana-trucha-patagonia-norte|nuestra nota de precios} dice que los lugares de una cadena publican semanas de sábado a sábado y que algunos aceptan estadías de otra duración (5/11/2026).',
	'h_dia'           => 'Cómo es un día de pesca',
	'dia_i'            => 'Según las páginas, el día se parte en dos sesiones, una a la mañana y otra a la tarde, con el almuerzo y una pausa en el medio. Lo que publican:',
	'dia_l'           => [
		'<strong>Pirá Lodge:</strong> se sale a las 8:00 o antes, se pesca hasta cerca de las 12:30, se vuelve a almorzar y se sale otra vez cerca de las 16:30 hasta el atardecer; la cena es cerca de las 21:00 ({x:fwt_pira|Fly Water Travel}, 13/11/2026).',
		'<strong>Pinti’s Dorado on the Fly:</strong> la mañana va de las 7:30 a las 12:00 y la tarde de las 15:45 hasta las 21:00 o 22:00, con almuerzo y siesta en el medio ({x:yd_pinti|Yellow Dog}, 13/11/2026).',
		'<strong>Itatí Lodge:</strong> pesca temprano, almuerzo en el lugar y vuelta a pescar después de una pausa corta ({x:sff_itati|Set Fly Fishing}, 15/11/2026).',
		'<strong>Las Buitreras:</strong> dos sesiones de 4 a 5 horas con una pausa larga al mediodía; cada guía trabaja el mismo tramo toda la semana y los pescadores rotan de tramo y de guía en cada sesión ({x:fwt_buit|Fly Water Travel}, 13/11/2026).',
		'<strong>Jurassic Lake:</strong> se pesca vadeando o desde la orilla, no embarcado ({x:jl_rates|jurassiclake.com}, 10/11/2026).',
		'<strong>Un día libre:</strong> según el lugar y la duración, Set Fly Fishing recomienda considerar un día de descanso en el medio del viaje para recorrer la zona ({x:sff_info|su guía de viaje}, 15/11/2026).',
	],
	'h_semana'        => 'Cómo se arma la semana, de punta a punta',
	'semana_s'        => [
		'<strong>Buenos Aires.</strong> Los itinerarios que leímos pasan por Buenos Aires: los vuelos internacionales llegan a Ezeiza (EZE) y los internos salen de Aeroparque (AEP), a una hora de viaje entre uno y otro según Set Fly Fishing (15/11/2026). Nervous Waters recomienda pasar una noche en la ciudad antes del vuelo al lugar (15/11/2026).',
		'<strong>Vuelo y traslado.</strong> Para el dorado en Corrientes, Set Fly Fishing nombra el aeropuerto de Corrientes (CNQ) como el preferido y el de Resistencia (RES) como alternativa; en Santa Cruz, Las Buitreras pide un vuelo a El Calafate y un traslado, y Jurassic Lake sale en chárter desde El Calafate los sábados.',
		'<strong>Día de llegada.</strong> Río Manso Lodge dice en sus preguntas frecuentes que el día de llegada se dedica a instalarse, conocer a los guías y a una cena de bienvenida, y que se empieza a pescar a la mañana siguiente (leído el 30/10/2026). Las demás páginas no lo aclaran.',
		'<strong>Los días de pesca.</strong> Seis seguidos en Pirá Lodge, según el itinerario de Fly Water Travel, y de domingo a viernes en Las Buitreras.',
		'<strong>Salida.</strong> En Pirá Lodge es el día 9 del itinerario; en Las Buitreras, el sábado, con vuelo a El Calafate y conexión a Buenos Aires.',
	],
	'h_grupo'         => 'Cuántos pescadores por guía o bote',
	'grupo_p'         => 'Lo habitual es de a dos. Pirá Lodge: dos por guía ({x:fwt_pira|Fly Water Travel}); Itatí Lodge: dos a la vez en el bote ({x:fwt_itati|Fly Water Travel}), ambas páginas leídas el 13/11/2026. Pinti’s: dos pescadores por bote ({x:yd_pinti|Yellow Dog}, 13/11/2026). Las Buitreras y Glencross: dos por guía ({x:fwt_buit|Fly Water Travel}, 13/11/2026). En Jurassic Lake, un guía acompaña a dos o tres pescadores según el tramo ({x:jl_rates|jurassiclake.com}, 10/11/2026). En los lugares de Nervous Waters que leímos, la base es habitación doble y guía compartido, y la habitación y el bote individuales dependen de la disponibilidad y pueden costar más ({x:nw_faq|sus preguntas frecuentes}, 15/11/2026).',
	'h_hab'           => 'Habitación doble y recargo por individual',
	'hab_p'           => 'Los precios por persona de estas páginas son en habitación doble. Si viajás solo y querés habitación y bote propios, el recargo cambia mucho: 50 % para habitación y bote individuales según la página de Pinti’s en Yellow Dog (13/11/2026); 60 % del paquete en un lugar de Corrientes y US$ 200 por noche en uno de Chubut según {n:paquete-incluye-no-incluye|nuestra nota de qué incluye un paquete}; un suplemento de guía individual de US$ 250 por persona por día en Río Manso Lodge ({x:rm_rates|su página de tarifas}, 10/11/2026). Kau Tapen y Villa María traen habitación single por defecto ({x:nw_faq|Nervous Waters}, 15/11/2026).',
	'h_acomp'         => 'Si viaja alguien que no pesca',
	'acomp_p'         => 'Existe y se cobra aparte. Pinti’s Dorado on the Fly publica US$ 450 por noche para quien no pesca y avisa que no es una buena opción para acompañantes, porque no ofrece actividades guiadas sin pesca ({x:yd_pinti|Yellow Dog}, 13/11/2026). Villa María y Kau Tapen: US$ 650 por noche ({n:precio-pesca-patagonia-sur|nuestra nota}, Nervous Waters, 13/11/2026). Río Manso Lodge: US$ 800 por noche o US$ 5.400 por semana, con actividades guiadas sin pesca incluidas ({x:rm_rates|su página de tarifas}, 10/11/2026). Pirá Lodge menciona cabalgatas, observación de aves y salidas nocturnas ({x:nw_pira|Nervous Waters}, 13/11/2026), y Set Fly Fishing enumera paseos a caballo, bodegas, aves y spa ({x:sff_info|su guía de viaje}, 15/11/2026). La página de Los Plateados no menciona actividades sin pesca. Si viaja alguien que no pesca, es de lo primero para decirnos.',
	'h_incluye'       => 'Qué suele incluir y qué no',
	'incluye_i'       => 'Cambia de un lugar a otro, y por eso dos precios solo se comparan cuando cubren lo mismo: {n:paquete-incluye-no-incluye|qué incluye un paquete de pesca en Argentina y qué cuesta aparte}. Lo que dicen estas páginas:',
	'incluye_l'       => [
		'<strong>Licencia:</strong> incluida en Itatí Lodge y no incluida en Pirá Lodge, según Fly Water Travel (13/11/2026); Nervous Waters dice que pide los datos para gestionarla antes de la llegada (15/11/2026). Cómo se saca: {n:permiso-pesca-argentina-paso-a-paso|permiso de pesca en Argentina, paso a paso}.',
		'<strong>Vuelos y traslados:</strong> Itatí Lodge incluye los vuelos chárter entre Buenos Aires y el lugar; los pasajes internacionales quedan fuera donde se mencionan (Itatí, Pirá). Las noches en Buenos Aires tampoco están incluidas.',
		'<strong>Equipo:</strong> préstamo de cañas, reels y líneas incluido en Pirá Lodge, en Pinti’s y en Suindá Lodge («sin costo adicional»); Río Manso Lodge cobra US$ 80 por pescador por día el equipo completo. Para el viaje, Set Fly Fishing avisa que la seguridad aeroportuaria argentina no admite cañas ni reels en la cabina de los vuelos internos: van en el equipaje despachado (15/11/2026).',
		'<strong>Lavandería:</strong> incluida en Pinti’s ({x:yd_pinti|Yellow Dog}) y no incluida en Itatí Lodge ({x:fwt_itati|Fly Water Travel}).',
		'<strong>Propinas:</strong> no incluidas. Set Fly Fishing sugiere entre el 12 % y el 20 % del costo del viaje en un fondo común que se reparte entre guías, cocina y limpieza (15/11/2026); Jurassic Lake sugiere de US$ 80 a 100 por persona por día ({n:precio-pesca-patagonia-sur|nuestra nota}).',
	],
	'h_clima'         => 'Clima, viento y por qué muchas ofertas son de semana',
	'clima_p1'        => 'Las páginas no explican por qué las ofertas son de semana. Lo que sí muestran: salidas fijas alrededor de un día (Jurassic Lake sale en chárter cada sábado por la mañana, según su página de ubicación), viajes largos hasta el lugar (Fly Water Travel advierte que Pirá Lodge pide estar preparado para un programa de viaje largo) y pocos cupos por semana, de 6 a 12 pescadores en la tabla de arriba.',
	'clima_l'         => [
		'<strong>Dorado y calor:</strong> según Set Fly Fishing (15/11/2026), de diciembre a marzo los días promedian de 27 a 32 °C y llegan a unos 38 °C, aunque es raro; en abril y mayo y de septiembre a noviembre van de 16 a 25 °C. Su página de Itatí Lodge dice «septiembre a mayo (evitar enero y febrero por el calor)». Las dos sesiones por día evitan el calor del mediodía ({x:fwt_pira|Fly Water Travel}).',
		'<strong>Trucha en la Patagonia:</strong> Río Manso Lodge dice que de noviembre a abril los días van de 13 a 24 °C y que puede llover en cualquier momento ({x:rm_faq|sus preguntas frecuentes}, 30/10/2026).',
		'<strong>Viento:</strong> Fly Water Travel dice de Las Buitreras que el viento es una constante y hay que esperarlo; Tres Amigos Outfitters recomienda una línea pensada para el viento frecuente en {x:ta_sc|su guía de Santa Cruz} (14/11/2026).',
		'<strong>Si el clima o el agua no dejan pescar:</strong> en Pirá Lodge se puede volver a la pileta o salir a observar naturaleza o a cabalgar (Fly Water Travel, 13/11/2026). Ninguna página que leímos dice qué pasa con el precio en ese caso: preguntalo por escrito.',
	],
	'clima_c'         => 'Las fechas de cada temporada y las vedas están en el {cal|calendario de pesca}.',
	'h_menos'         => 'Qué pasa si querés menos días',
	'menos_p'         => 'Algunas páginas publican programas más cortos con su propio precio: de 3 a 6 noches en Pirá Lodge y Suindá Lodge ({x:nw_pira|Nervous Waters}, 13/11/2026), 4 noches y 3 días en Itatí Lodge, media semana en Glencross y en Los Plateados, y 3 y 4 noches en Jurassic Lake. Otras lo dicen en una frase: Yellow Dog escribe que en Pinti’s las estadías más cortas también son bienvenidas; Fly Water Travel, que en Pirá Lodge se aceptan estadías de distinta duración; Tres Amigos Outfitters, que puede adaptar la duración de la estadía (16/11/2026); y Jurassic Lake ofrece itinerarios a medida, con llegada desde otra ciudad o estadía más larga (10/11/2026). Cómo cambia el precio con menos días lo dice la tarifa de cada lugar: preguntalo por escrito.',
	'h_reservar'      => 'Cuándo conviene reservar',
	'reservar_p'      => 'Ninguna de las páginas que leímos dice con cuánta anticipación conviene reservar la semana. Lo que sí publica una de ellas, Set Fly Fishing, es una lista de preparación: a 90 días o más de la salida, el pago final, los pasajes aéreos y el seguro de viaje; a 60 días o más, los datos de vuelo y el equipo reunido; a 7 días, volver a confirmar los vuelos (15/11/2026). Es la línea de tiempo de un lugar, no una regla. Como cada semana tiene pocos cupos (de 6 a 12 pescadores en la tabla), lo prudente es preguntar con tiempo.',
	'h_confirmar'     => 'Cómo se confirma la disponibilidad',
	'confirmar_p'     => 'Cada página manda a consultar la disponibilidad con el propio lugar; la de Los Plateados trae un botón «Check availability». Cuando nos pedís opciones, lo hacemos nosotros: consultamos a los lugares que encajan con tu pedido y en 48 horas te respondemos con lo que hay para tus fechas, lado a lado. El precio es el del lugar: pagás lo mismo que si reservaras directo.',
	'cta'             => '<strong>Contanos qué buscás y te armamos la mejor opción para vos.</strong> Fechas, cuántos son, si viaja alguien que no pesca y qué querés pescar: <a href="{viaje}">pedí tus opciones acá</a> y te respondemos en 48 horas.',
	'faq_h'           => 'Preguntas frecuentes',
	'faq'             => [
		[ '¿Cuánto dura una semana de pesca en un lodge?', 'En las páginas que leímos, 7 noches y 6 días de pesca; Jurassic Lake y Villa María cuentan 6,5 días de pesca en la semana de 7 noches (leídas entre el 10 y el 13/11/2026). Los Plateados cuenta 6 días de pesca de sábado a viernes (16/11/2026).' ],
		[ '¿Qué día se llega y qué día se sale?', 'La mayoría de las semanas van de sábado a sábado (Las Buitreras, Glencross, Jurassic Lake y, según Yellow Dog, la mayoría de los paquetes de Pinti’s). Los Plateados va de sábado a viernes y la semana de Villa María, de viernes a viernes. Las medias semanas son de martes a sábado o de sábado a martes en Jurassic Lake, y de miércoles a sábado o de sábado a miércoles en Glencross.' ],
		[ '¿Hay media semana o paquetes más cortos?', 'Sí, en varios: de 3 a 6 noches en Pirá Lodge y Suindá Lodge, 4 noches y 3 días en Itatí Lodge, media semana en Glencross y en Los Plateados, y programas de 3 y 4 noches en Jurassic Lake (de enero a abril). Qué cuesta cada uno lo dice la página de cada lugar; algunas notas nuestras reúnen los precios con su fecha: {n:precio-viaje-dorado-lodge|cuánto cuesta un viaje de pesca de dorado}.' ],
		[ '¿Cuántos pescadores van por guía o bote?', 'De a dos en Pirá Lodge, Itatí Lodge, Pinti’s, Las Buitreras y Glencross; un guía cada dos o tres pescadores en Jurassic Lake, según el tramo. Fuentes y fechas, más arriba.' ],
		[ '¿Voy en habitación doble? ¿Cuánto cuesta la individual?', 'Los precios de estas páginas son por persona en habitación doble. La individual se cobra aparte: 50 % para habitación y bote en Pinti’s (Yellow Dog, 13/11/2026), 60 % del paquete en un lugar de Corrientes y US$ 200 por noche en uno de Chubut (nuestra nota de qué incluye un paquete), y US$ 250 por persona por día de guía individual en Río Manso Lodge (10/11/2026).' ],
		[ '¿Puede venir un acompañante que no pesca?', 'Sí, y se cobra aparte: US$ 450 por noche en Pinti’s (que avisa que no ofrece actividades guiadas sin pesca), US$ 650 en Villa María y Kau Tapen, y US$ 800 por noche en Río Manso Lodge, con actividades guiadas sin pesca incluidas. Fuentes y fechas, más arriba.' ],
		[ '¿Qué incluye una semana y qué no?', 'Depende del lugar. En las páginas que leímos suelen estar el alojamiento, las comidas, la guía y el equipo de préstamo; la licencia, los pasajes, las noches en Buenos Aires, la lavandería y las propinas cambian de un lugar a otro. Itatí Lodge incluye licencia y vuelos chárter; Pirá Lodge no incluye licencia ni pasajes (Fly Water Travel, 13/11/2026).' ],
		[ '¿Qué pasa si el clima o el río no dejan pescar?', 'Ninguna página que leímos explica qué pasa con el precio. Sí dicen qué se hace ese día: en Pirá Lodge se puede volver a la pileta, observar la naturaleza o cabalgar (Fly Water Travel, 13/11/2026). Preguntalo por escrito, junto con las condiciones de pago y de cambio de fecha.' ],
		[ '¿Con cuánta anticipación conviene reservar?', 'Ninguna página que leímos lo dice. Set Fly Fishing publica una lista: a 90 días o más de la salida, el pago final, los pasajes y el seguro; a 60 días o más, los datos de vuelo (15/11/2026). Como las semanas tienen de 6 a 12 cupos, lo prudente es preguntar con tiempo.' ],
		[ '¿Cómo se confirma la disponibilidad de una semana?', 'La confirma el propio lugar. Cuando nos pedís opciones consultamos nosotros a los lugares que encajan y te respondemos en 48 horas, lado a lado. Es gratis para vos y el precio es el del lugar.' ],
	],
	'cierre'          => '<em>Todo lo de esta página sale de páginas públicas leídas entre el 30 de octubre y el 16 de noviembre de 2026, cada una con su fecha, y de nuestras notas con fecha. Cambian cada temporada. No estuvimos en estos lugares: confirmá las fechas, qué incluye y las condiciones con el lugar antes de reservar.</em>',
];

// =====================================================================================================================================
// ENGLISH
// =====================================================================================================================================
$N['en'] = [
	'titulo'          => 'How a fishing week at an Argentina lodge works',
	'slug'            => 'how-a-fishing-week-at-an-argentina-lodge-works',
	'seo_title'       => 'How a fishing week at an Argentina lodge works',
	'seo_description' => 'How a fishing week at an Argentina lodge works: formats, arrival and departure days, anglers per guide, rooms and what is included. Read in November 2026.',
	'bajada'          => 'How a fishing week at a lodge is put together: formats (full week, half week and packages by nights), arrival and departure days, anglers per guide, rooms, a companion who does not fish and what is usually included. Each fact with the page that says it and the date we read it.',
	'datos'           => [
		'The usual pattern on the pages we read: 7 nights and 6 fishing days (6.5 on some), arriving and leaving on a Saturday; there are half weeks and packages of 3 to 6 nights (read between 30/10 and 16/11/2026).',
		'Anglers per guide or boat: two at the dorado and sea-run trout places we read; one guide for two or three anglers at Jurassic Lake.',
		'A double room is the base of the prices; a single room costs extra: 50% according to the page of a place at Itá Ibaté and 60% at one in Corrientes according to our note.',
		'A companion who does not fish is possible and paid separately: US$ 450 a night at one place, US$ 650 at two others and US$ 800 at a third (pages read on 10 and 13/11/2026).',
		'No page says how many months ahead to book; one of them puts the flight and trip insurance “90+ days out” from departure.',
	],
	'p0'              => '<strong>Short answer.</strong> On almost every page we read, a fishing week at a lodge is a fixed program of <strong>7 nights and 6 fishing days</strong> (6.5 on some), Saturday to Saturday, in a double room, with meals and a guide shared by two anglers; depending on the place it also includes gear, transfers and the license. There are also half weeks and packages of 3 to 6 nights. This page gathers what the pages of several places say, each fact with its source and the date we read it. It is not a general rule and not a quote for your dates.',
	'resp'            => [
		'<strong>Formats:</strong> a full week of 7 nights, a half week (3 or 4 nights) and packages of 3 to 6 nights.',
		'<strong>Arrival and departure:</strong> Saturday to Saturday at most places; there are Friday-to-Friday and Saturday-to-Friday weeks, and half weeks from Tuesday to Saturday or Saturday to Tuesday.',
		'<strong>Fishing:</strong> two sessions a day, morning and afternoon, with lunch and a break in between; two anglers per guide at almost all of them.',
		'<strong>Room:</strong> double by default; a single room and a companion who does not fish are charged extra.',
		'<strong>It changes from place to place:</strong> license, flights, transfers, gear and tips.',
		'<strong>Availability:</strong> we confirm it when you ask us for options.',
	],
	'h_formatos'      => 'The formats: full week, half week and packages by nights',
	'formatos_i'      => 'What each place’s page publishes, read between 30/10 and 16/11/2026. Some pages count an extra half fishing day (6.5 days) in the 7-night week; the column copies what each one says.',
	'tabla_cols'      => [ 'Place', 'Published formats', 'Arrival and departure', 'Anglers', 'Source and date' ],
	'tabla_rows'      => [
		[ '<strong>Itatí Lodge</strong> ({g:itati|Itatí}, Corrientes)', '7 nights and 6 fishing days; 4 nights and 3 days', 'Day 1: arrive in Buenos Aires and overnight at a hotel (not included); day 2: charter flight to the lodge', '8 a week (Fly Water Travel); 10 in 5 rooms (Set Fly Fishing)', '{x:fwt_itati|Fly Water Travel}, 13/11/2026; {x:sff_itati|Set Fly Fishing}, 15/11/2026' ],
		[ '<strong>Pirá Lodge</strong> ({g:esteros-del-ibera|Esteros del Iberá}, Corrientes)', '7 nights and 6 days; 6 and 5; 5 and 4; 4 and 3; 3 and 2', 'Two days of travel in, six fishing days in a row and departure on day 9 of the itinerary', '10', '{x:fwt_pira|Fly Water Travel} and {x:nw_pira|Nervous Waters}, 13/11/2026' ],
		[ '<strong>Suindá Lodge</strong> ({g:itati|Itatí}, Corrientes)', 'The same five programs, from 7 nights and 6 days to 3 nights and 2 days', 'The page does not say', '10', '{x:nw_suinda|Nervous Waters}, 13/11/2026' ],
		[ '<strong>Pinti’s Dorado on the Fly</strong> ({g:ita-ibate|Itá Ibaté}, Corrientes)', 'Typical: 7 nights and 6 days; also 6 and 5, 5 and 4, 4 and 3', 'Most packages run Saturday to Saturday; shorter stays are accepted (Yellow Dog)', '8', '{x:yd_pinti|Yellow Dog Flyfishing} and {x:fwt_pinti|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Las Buitreras</strong> ({g:rio-gallegos|Río Gallegos}, Santa Cruz)', 'A Saturday-to-Saturday week, with six full fishing days from Sunday to Friday', 'Saturday: flight to El Calafate and transfer; the next Saturday: return', '12', '{x:fwt_buit|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Glencross</strong> (upper Río Gallegos, Santa Cruz)', 'Full week from Saturday to Saturday; half week from Wednesday to Saturday or Saturday to Wednesday', 'Saturday: pick-up at Río Gallegos airport', '6', '{x:fwt_glen|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Jurassic Lake</strong> ({g:jurassic-lake|Santa Cruz})', '7 nights and 6.5 days (Saturday to Saturday); 4 nights and 3.5 days (Tuesday to Saturday, January to April); 3 nights and 2.5 days (Saturday to Tuesday, January to April)', 'Charter flight from El Calafate every Saturday morning', 'One guide for two or three', '{x:jl_rates|jurassiclake.com, rates}, 10/11/2026; {x:jl_loc|location}, 30/10/2026' ],
		[ '<strong>Los Plateados</strong> ({g:rio-santa-cruz|Santa Cruz River}, steelhead)', 'Half week or full week; a 6-fishing-day program', 'Saturday to Friday', '4 (maximum 12 a week at the place)', '{x:ta_plat|Tres Amigos Outfitters}, 16/11/2026' ],
		[ '<strong>Villa María Lodge</strong> ({g:rio-grande|Río Grande}, Tierra del Fuego)', '7 nights and 6.5 fishing days', 'Friday to Friday', 'Two per guide', '{n:precio-pesca-patagonia-sur|Our price note} (Nervous Waters, 13/11/2026)' ],
	],
	'formatos_c'      => 'For a trout week in northern Patagonia, {n:precio-semana-trucha-patagonia-norte|our price note} says that the places of one chain publish Saturday-to-Saturday weeks and that some accept stays of another length (5/11/2026).',
	'h_dia'           => 'What a fishing day looks like',
	'dia_i'            => 'According to the pages, the day is split into two sessions, one in the morning and one in the afternoon, with lunch and a break in between. What they publish:',
	'dia_l'           => [
		'<strong>Pirá Lodge:</strong> you leave at 8:00 or earlier, fish until about 12:30, return for lunch and head out again around 16:30 until dusk; dinner is around 21:00 ({x:fwt_pira|Fly Water Travel}, 13/11/2026).',
		'<strong>Pinti’s Dorado on the Fly:</strong> the morning runs from 7:30 to 12:00 and the afternoon from 15:45 until 21:00 or 22:00, with lunch and a siesta in between ({x:yd_pinti|Yellow Dog}, 13/11/2026).',
		'<strong>Itatí Lodge:</strong> early fishing, lunch at the lodge and back out after a short break ({x:sff_itati|Set Fly Fishing}, 15/11/2026).',
		'<strong>Las Buitreras:</strong> two sessions of 4 to 5 hours with a long midday break; each guide works the same beat all week and anglers rotate beats and guides each session ({x:fwt_buit|Fly Water Travel}, 13/11/2026).',
		'<strong>Jurassic Lake:</strong> you fish wading or from the bank, not from a boat ({x:jl_rates|jurassiclake.com}, 10/11/2026).',
		'<strong>A day off:</strong> depending on the place and the length of the trip, Set Fly Fishing recommends considering a rest day in the middle of the trip to look around ({x:sff_info|its travel guide}, 15/11/2026).',
	],
	'h_semana'        => 'How the week is put together, end to end',
	'semana_s'        => [
		'<strong>Buenos Aires.</strong> The itineraries we read go through Buenos Aires: international flights land at Ezeiza (EZE) and domestic flights leave from Aeroparque (AEP), about an hour apart according to Set Fly Fishing (15/11/2026). Nervous Waters recommends spending a night in the city before the flight to the place (15/11/2026).',
		'<strong>Flight and transfer.</strong> For dorado in Corrientes, Set Fly Fishing names Corrientes airport (CNQ) as the preferred one and Resistencia (RES) as the alternative; in Santa Cruz, Las Buitreras asks for a flight to El Calafate and a transfer, and Jurassic Lake runs a charter from El Calafate on Saturdays.',
		'<strong>Arrival day.</strong> Río Manso Lodge says in its FAQ that arrival day is for settling in, meeting the guides and a welcome dinner, and that fishing starts the next morning (read 30/10/2026). The other pages do not say.',
		'<strong>The fishing days.</strong> Six in a row at Pirá Lodge, according to the Fly Water Travel itinerary, and Sunday to Friday at Las Buitreras.',
		'<strong>Departure.</strong> At Pirá Lodge it is day 9 of the itinerary; at Las Buitreras, Saturday, with a flight to El Calafate and a connection to Buenos Aires.',
	],
	'h_grupo'         => 'How many anglers per guide or boat',
	'grupo_p'         => 'Two is the usual number. Pirá Lodge: two per guide ({x:fwt_pira|Fly Water Travel}); Itatí Lodge: two at a time in the boat ({x:fwt_itati|Fly Water Travel}), both pages read on 13/11/2026. Pinti’s: two anglers per boat ({x:yd_pinti|Yellow Dog}, 13/11/2026). Las Buitreras and Glencross: two per guide ({x:fwt_buit|Fly Water Travel}, 13/11/2026). At Jurassic Lake, one guide takes two or three anglers depending on the beat ({x:jl_rates|jurassiclake.com}, 10/11/2026). At the Nervous Waters places we read, the base is a double room and a shared guide, and a single room and boat depend on availability and can cost more ({x:nw_faq|their FAQ}, 15/11/2026).',
	'h_hab'           => 'Double room and the single-room surcharge',
	'hab_p'           => 'The per-person prices on these pages are for a double room. If you travel alone and want your own room and boat, the surcharge varies a lot: 50% for a single room and boat according to the Pinti’s page on Yellow Dog (13/11/2026); 60% of the package at one place in Corrientes and US$ 200 a night at one in Chubut according to {n:paquete-incluye-no-incluye|our note on what a package includes}; a single-guide supplement of US$ 250 per angler per day at Río Manso Lodge ({x:rm_rates|its rates page}, 10/11/2026). Kau Tapen and Villa María come with a single room by default ({x:nw_faq|Nervous Waters}, 15/11/2026).',
	'h_acomp'         => 'If someone who does not fish comes along',
	'acomp_p'         => 'It is possible and paid separately. Pinti’s Dorado on the Fly publishes US$ 450 a night for a non-angler and warns that it is not a great option for companions, because it offers no guided non-fishing activities ({x:yd_pinti|Yellow Dog}, 13/11/2026). Villa María and Kau Tapen: US$ 650 a night ({n:precio-pesca-patagonia-sur|our note}, Nervous Waters, 13/11/2026). Río Manso Lodge: US$ 800 a night or US$ 5,400 a week, with guided non-fishing activities included ({x:rm_rates|its rates page}, 10/11/2026). Pirá Lodge mentions horseback riding, birdwatching and night wildlife outings ({x:nw_pira|Nervous Waters}, 13/11/2026), and Set Fly Fishing lists horseback rides, wine tasting, birdwatching and spa ({x:sff_info|its travel guide}, 15/11/2026). The Los Plateados page mentions no non-fishing activities. If someone who does not fish is coming, it is one of the first things to tell us.',
	'h_incluye'       => 'What is usually included and what is not',
	'incluye_i'       => 'It changes from place to place, which is why two prices are only comparable when they cover the same things: {n:paquete-incluye-no-incluye|what an Argentina fishing package includes and what is extra}. What these pages say:',
	'incluye_l'       => [
		'<strong>License:</strong> included at Itatí Lodge and not included at Pirá Lodge, according to Fly Water Travel (13/11/2026); Nervous Waters says it asks for your details to arrange it before arrival (15/11/2026). How to get it: {n:permiso-pesca-argentina-paso-a-paso|Argentina fishing license, step by step}.',
		'<strong>Flights and transfers:</strong> Itatí Lodge includes the charter flights between Buenos Aires and the lodge; international flights are left out where they are mentioned (Itatí, Pirá). Nights in Buenos Aires are not included either.',
		'<strong>Gear:</strong> loan rods, reels and lines are included at Pirá Lodge, at Pinti’s and at Suindá Lodge (“at no additional cost”); Río Manso Lodge charges US$ 80 per angler per day for a full set. For the trip, Set Fly Fishing warns that Argentine airport security does not allow rods or reels in the cabin on domestic flights: they go in checked baggage (15/11/2026).',
		'<strong>Laundry:</strong> included at Pinti’s ({x:yd_pinti|Yellow Dog}) and not included at Itatí Lodge ({x:fwt_itati|Fly Water Travel}).',
		'<strong>Tips:</strong> not included. Set Fly Fishing suggests 12% to 20% of the cost of the trip in a shared pool split between guides, kitchen and housekeeping staff (15/11/2026); Jurassic Lake suggests US$ 80 to 100 per person per day ({n:precio-pesca-patagonia-sur|our note}).',
	],
	'h_clima'         => 'Weather, wind and why many offers are by the week',
	'clima_p1'        => 'The pages do not explain why offers are by the week. What they do show: fixed departures around one day (Jurassic Lake flies a charter every Saturday morning, according to its location page), long journeys to the place (Fly Water Travel warns that Pirá Lodge calls for being ready for a long travel program) and few spots per week, 6 to 12 anglers in the table above.',
	'clima_l'         => [
		'<strong>Dorado and heat:</strong> according to Set Fly Fishing (15/11/2026), from December to March days average 27 to 32 °C and can reach about 38 °C, though that is rare; in April and May and from September to November they run 16 to 25 °C. Its Itatí Lodge page says “September–May (avoid Jan–Feb for heat)”. Two sessions a day avoid the midday heat ({x:fwt_pira|Fly Water Travel}).',
		'<strong>Trout in Patagonia:</strong> Río Manso Lodge says that from November to April days run 13 to 24 °C and that it can rain at any time ({x:rm_faq|its FAQ}, 30/10/2026).',
		'<strong>Wind:</strong> Fly Water Travel says of Las Buitreras that wind is a constant and should be expected; Tres Amigos Outfitters recommends a fly line built for the often windy conditions in {x:ta_sc|its Santa Cruz guide} (14/11/2026).',
		'<strong>If the weather or the water stops the fishing:</strong> at Pirá Lodge you can go back to the pool or go out to watch wildlife or ride horses (Fly Water Travel, 13/11/2026). No page we read says what happens to the price in that case: ask in writing.',
	],
	'clima_c'         => 'The dates of each season and the closed seasons are in the {cal|fishing calendar}.',
	'h_menos'         => 'What if you want fewer days',
	'menos_p'         => 'Some pages publish shorter programs with their own price: 3 to 6 nights at Pirá Lodge and Suindá Lodge ({x:nw_pira|Nervous Waters}, 13/11/2026), 4 nights and 3 days at Itatí Lodge, a half week at Glencross and at Los Plateados, and 3 and 4 nights at Jurassic Lake. Others say it in a sentence: Yellow Dog writes that at Pinti’s shorter stays are also welcome; Fly Water Travel, that Pirá Lodge accepts stays of varying length; Tres Amigos Outfitters, that it can customize the length of the stay (16/11/2026); and Jurassic Lake offers tailor-made itineraries, arriving from another city or staying longer (10/11/2026). How the price changes with fewer days is in each place’s rate: ask in writing.',
	'h_reservar'      => 'When to book',
	'reservar_p'      => 'None of the pages we read says how far ahead to book the week. What one of them, Set Fly Fishing, does publish is a preparation checklist: 90 days or more before departure, the final payment, the flights and the trip insurance; 60 days or more, the flight details and the gear gathered; 7 days out, re-confirm the flights (15/11/2026). It is the timeline of one place, not a rule. Since each week has few spots (6 to 12 anglers in the table), asking early is the prudent thing.',
	'h_confirmar'     => 'How availability is confirmed',
	'confirmar_p'     => 'Each page sends you to check availability with the place itself; the Los Plateados page has a “Check availability” button. When you ask us for options, we do it: we ask the places that fit your request and within 48 hours we reply with what is available for your dates, side by side. The price is the place’s own: you pay the same as if you booked directly.',
	'cta'             => '<strong>Tell us what you are looking for and we will build the best option for you.</strong> Dates, how many of you, whether someone who does not fish is coming and what you want to fish: <a href="{viaje}">ask for your options here</a> and we reply within 48 hours.',
	'faq_h'           => 'Frequently asked questions',
	'faq'             => [
		[ 'How long is a fishing week at a lodge?', 'On the pages we read, 7 nights and 6 fishing days; Jurassic Lake and Villa María count 6.5 fishing days in the 7-night week (read between 10 and 13/11/2026). Los Plateados counts 6 fishing days from Saturday to Friday (16/11/2026).' ],
		[ 'Which day do you arrive and which day do you leave?', 'Most weeks run Saturday to Saturday (Las Buitreras, Glencross, Jurassic Lake and, according to Yellow Dog, most Pinti’s packages). Los Plateados runs Saturday to Friday and the Villa María week, Friday to Friday. Half weeks run Tuesday to Saturday or Saturday to Tuesday at Jurassic Lake, and Wednesday to Saturday or Saturday to Wednesday at Glencross.' ],
		[ 'Is there a half week or are there shorter packages?', 'Yes, at several: 3 to 6 nights at Pirá Lodge and Suindá Lodge, 4 nights and 3 days at Itatí Lodge, a half week at Glencross and at Los Plateados, and 3- and 4-night programs at Jurassic Lake (January to April). What each costs is on each place’s page; some of our notes gather the prices with their date: {n:precio-viaje-dorado-lodge|how much a dorado fishing trip costs}.' ],
		[ 'How many anglers go per guide or boat?', 'Two at Pirá Lodge, Itatí Lodge, Pinti’s, Las Buitreras and Glencross; one guide for two or three anglers at Jurassic Lake, depending on the beat. Sources and dates above.' ],
		[ 'Do I stay in a double room? How much is a single room?', 'The prices on these pages are per person in a double room. A single room is extra: 50% for room and boat at Pinti’s (Yellow Dog, 13/11/2026), 60% of the package at one place in Corrientes and US$ 200 a night at one in Chubut (our note on what a package includes), and US$ 250 per person per day for a single guide at Río Manso Lodge (10/11/2026).' ],
		[ 'Can a companion who does not fish come?', 'Yes, and it is paid separately: US$ 450 a night at Pinti’s (which warns it offers no guided non-fishing activities), US$ 650 at Villa María and Kau Tapen, and US$ 800 a night at Río Manso Lodge, with guided non-fishing activities included. Sources and dates above.' ],
		[ 'What does a week include and what does it not?', 'It depends on the place. On the pages we read, lodging, meals, the guide and loan gear are usually in; the license, flights, nights in Buenos Aires, laundry and tips change from place to place. Itatí Lodge includes the license and charter flights; Pirá Lodge includes neither the license nor flights (Fly Water Travel, 13/11/2026).' ],
		[ 'What happens if the weather or the river stops the fishing?', 'No page we read explains what happens to the price. They do say what is done that day: at Pirá Lodge you can go back to the pool, watch wildlife or ride horses (Fly Water Travel, 13/11/2026). Ask in writing, along with the payment and date-change terms.' ],
		[ 'How far ahead should I book?', 'No page we read says. Set Fly Fishing publishes a checklist: 90 days or more before departure, the final payment, flights and insurance; 60 days or more, flight details (15/11/2026). Since weeks have 6 to 12 spots, asking early is the prudent thing.' ],
		[ 'How is availability for a week confirmed?', 'The place itself confirms it. When you ask us for options, we ask the places that fit and reply within 48 hours, side by side. It is free for you and the price is the place’s own.' ],
	],
	'cierre'          => '<em>Everything on this page comes from public pages read between 30 October and 16 November 2026, each with its date, and from our dated notes. They change every season. We have not stayed at these places: confirm the dates, what is included and the conditions with the place before booking.</em>',
];

// =====================================================================================================================================
// PORTUGUÊS
// =====================================================================================================================================
$N['pt'] = [
	'titulo'          => 'Como funciona uma semana de pesca em uma pousada na Argentina',
	'slug'            => 'como-funciona-uma-semana-de-pesca-em-uma-pousada',
	'seo_title'       => 'Como funciona uma semana de pesca em pousada na Argentina',
	'seo_description' => 'Como funciona uma semana de pesca em uma pousada na Argentina: formatos, chegada e saída, pescadores por guia, quarto e o que inclui. Lido em novembro de 2026.',
	'bajada'          => 'Como se monta uma semana de pesca em uma pousada: formatos (semana completa, meia semana e pacotes por noites), dias de chegada e saída, pescadores por guia, quarto, acompanhante e o que costuma estar incluído. Cada dado com a página que o diz e a data em que a lemos.',
	'datos'           => [
		'O habitual nas páginas que lemos: 7 noites e 6 dias de pesca (6,5 em algumas), com chegada e saída no sábado; há meia semana e pacotes de 3 a 6 noites (lidas entre 30/10 e 16/11/2026).',
		'Pescadores por guia ou barco: dois nos lugares de dourado e de truta-do-mar que lemos; um guia para dois ou três pescadores em Jurassic Lake.',
		'O quarto duplo é a base dos preços; o individual é cobrado à parte: 50 % segundo a página de um lugar de Itá Ibaté e 60 % em um de Corrientes segundo a nossa nota.',
		'O acompanhante que não pesca existe e é pago à parte: US$ 450 por noite em um lugar, US$ 650 em outros dois e US$ 800 em um terceiro (páginas lidas em 10 e 13/11/2026).',
		'Nenhuma página diz com quantos meses de antecedência reservar; uma delas põe a passagem e o seguro de viagem «a 90 dias ou mais» da saída.',
	],
	'p0'              => '<strong>Resposta curta.</strong> Em quase todas as páginas que lemos, uma semana de pesca em uma pousada é um programa fechado de <strong>7 noites e 6 dias de pesca</strong> (6,5 em algumas), de sábado a sábado, em quarto duplo, com refeições e guia compartilhado por dois pescadores; conforme o lugar, inclui também equipamento, traslados e licença. Há ainda meia semana e pacotes de 3 a 6 noites. Esta página reúne o que dizem as páginas de vários lugares, cada dado com a sua fonte e a data em que o lemos. Não é uma regra geral nem uma cotação para as suas datas.',
	'resp'            => [
		'<strong>Formatos:</strong> semana completa de 7 noites, meia semana (3 ou 4 noites) e pacotes de 3 a 6 noites.',
		'<strong>Chegada e saída:</strong> de sábado a sábado na maioria; há semanas de sexta a sexta, de sábado a sexta e meias semanas de terça a sábado ou de sábado a terça.',
		'<strong>Pesca:</strong> duas sessões por dia, manhã e tarde, com almoço e pausa no meio; dois pescadores por guia em quase todos.',
		'<strong>Quarto:</strong> duplo por padrão; o individual e o acompanhante que não pesca são cobrados à parte.',
		'<strong>Muda de um lugar para outro:</strong> licença, voos, traslados, equipamento e gorjetas.',
		'<strong>Disponibilidade:</strong> nós a confirmamos quando você pede opções.',
	],
	'h_formatos'      => 'Os formatos: semana completa, meia semana e pacotes por noites',
	'formatos_i'      => 'O que a página de cada lugar publica, lida entre 30/10 e 16/11/2026. Algumas páginas contam meio dia de pesca a mais (6,5 dias) na semana de 7 noites; a coluna copia o que cada uma diz.',
	'tabla_cols'      => [ 'Lugar', 'Formatos publicados', 'Chegada e saída', 'Pescadores', 'Fonte e data' ],
	'tabla_rows'      => [
		[ '<strong>Itatí Lodge</strong> ({g:itati|Itatí}, Corrientes)', '7 noites e 6 dias de pesca; 4 noites e 3 dias', 'Dia 1: chegada a Buenos Aires e noite em hotel (não incluída); dia 2: voo fretado até o lugar', '8 por semana (Fly Water Travel); 10 em 5 quartos (Set Fly Fishing)', '{x:fwt_itati|Fly Water Travel}, 13/11/2026; {x:sff_itati|Set Fly Fishing}, 15/11/2026' ],
		[ '<strong>Pirá Lodge</strong> ({g:esteros-del-ibera|Esteros del Iberá}, Corrientes)', '7 noites e 6 dias; 6 e 5; 5 e 4; 4 e 3; 3 e 2', 'Dois dias de viagem de ida, seis dias de pesca seguidos e volta no dia 9 do roteiro', '10', '{x:fwt_pira|Fly Water Travel} e {x:nw_pira|Nervous Waters}, 13/11/2026' ],
		[ '<strong>Suindá Lodge</strong> ({g:itati|Itatí}, Corrientes)', 'Os mesmos cinco programas, de 7 noites e 6 dias a 3 noites e 2 dias', 'A página não diz', '10', '{x:nw_suinda|Nervous Waters}, 13/11/2026' ],
		[ '<strong>Pinti’s Dorado on the Fly</strong> ({g:ita-ibate|Itá Ibaté}, Corrientes)', 'O típico: 7 noites e 6 dias; também 6 e 5, 5 e 4, 4 e 3', 'A maioria dos pacotes vai de sábado a sábado; estadias mais curtas são aceitas (Yellow Dog)', '8', '{x:yd_pinti|Yellow Dog Flyfishing} e {x:fwt_pinti|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Las Buitreras</strong> ({g:rio-gallegos|rio Gallegos}, Santa Cruz)', 'Semana de sábado a sábado, com seis dias de pesca completos de domingo a sexta', 'Sábado: voo a El Calafate e traslado; sábado seguinte: volta', '12', '{x:fwt_buit|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Glencross</strong> (alto rio Gallegos, Santa Cruz)', 'Semana completa de sábado a sábado; meia semana de quarta a sábado ou de sábado a quarta', 'Sábado: busca no aeroporto de Río Gallegos', '6', '{x:fwt_glen|Fly Water Travel}, 13/11/2026' ],
		[ '<strong>Jurassic Lake</strong> ({g:jurassic-lake|Santa Cruz})', '7 noites e 6,5 dias (sábado a sábado); 4 noites e 3,5 dias (terça a sábado, de janeiro a abril); 3 noites e 2,5 dias (sábado a terça, de janeiro a abril)', 'Voo fretado desde El Calafate todo sábado de manhã', 'Um guia para dois ou três', '{x:jl_rates|jurassiclake.com, tarifas}, 10/11/2026; {x:jl_loc|localização}, 30/10/2026' ],
		[ '<strong>Los Plateados</strong> ({g:rio-santa-cruz|rio Santa Cruz}, steelhead)', 'Meia semana ou semana completa; programa de 6 dias de pesca', 'De sábado a sexta', '4 (máximo de 12 por semana no lugar)', '{x:ta_plat|Tres Amigos Outfitters}, 16/11/2026' ],
		[ '<strong>Villa María Lodge</strong> ({g:rio-grande|Río Grande}, Terra do Fogo)', '7 noites e 6,5 dias de pesca', 'De sexta a sexta', 'Dois por guia', '{n:precio-pesca-patagonia-sur|Nossa nota de preços} (Nervous Waters, 13/11/2026)' ],
	],
	'formatos_c'      => 'Para uma semana de truta na Patagônia norte, {n:precio-semana-trucha-patagonia-norte|a nossa nota de preços} diz que os lugares de uma rede publicam semanas de sábado a sábado e que alguns aceitam estadias de outra duração (5/11/2026).',
	'h_dia'           => 'Como é um dia de pesca',
	'dia_i'            => 'Segundo as páginas, o dia se divide em duas sessões, uma de manhã e outra à tarde, com almoço e uma pausa no meio. O que publicam:',
	'dia_l'           => [
		'<strong>Pirá Lodge:</strong> sai-se às 8h00 ou antes, pesca-se até perto das 12h30, volta-se para almoçar e sai-se de novo por volta das 16h30 até o entardecer; o jantar é perto das 21h00 ({x:fwt_pira|Fly Water Travel}, 13/11/2026).',
		'<strong>Pinti’s Dorado on the Fly:</strong> a manhã vai das 7h30 às 12h00 e a tarde das 15h45 até as 21h00 ou 22h00, com almoço e sesta no meio ({x:yd_pinti|Yellow Dog}, 13/11/2026).',
		'<strong>Itatí Lodge:</strong> pesca cedo, almoço no lugar e volta a pescar depois de uma pausa curta ({x:sff_itati|Set Fly Fishing}, 15/11/2026).',
		'<strong>Las Buitreras:</strong> duas sessões de 4 a 5 horas com uma pausa longa ao meio-dia; cada guia trabalha o mesmo trecho a semana toda e os pescadores se revezam de trecho e de guia a cada sessão ({x:fwt_buit|Fly Water Travel}, 13/11/2026).',
		'<strong>Jurassic Lake:</strong> pesca-se vadeando ou da margem, não embarcado ({x:jl_rates|jurassiclake.com}, 10/11/2026).',
		'<strong>Um dia livre:</strong> conforme o lugar e a duração, a Set Fly Fishing recomenda considerar um dia de descanso no meio da viagem para conhecer a região ({x:sff_info|seu guia de viagem}, 15/11/2026).',
	],
	'h_semana'        => 'Como a semana se monta, de ponta a ponta',
	'semana_s'        => [
		'<strong>Buenos Aires.</strong> Os roteiros que lemos passam por Buenos Aires: os voos internacionais chegam a Ezeiza (EZE) e os internos saem de Aeroparque (AEP), a cerca de uma hora de distância segundo a Set Fly Fishing (15/11/2026). A Nervous Waters recomenda passar uma noite na cidade antes do voo até o lugar (15/11/2026).',
		'<strong>Voo e traslado.</strong> Para o dourado em Corrientes, a Set Fly Fishing cita o aeroporto de Corrientes (CNQ) como o preferido e o de Resistencia (RES) como alternativa; em Santa Cruz, Las Buitreras pede um voo a El Calafate e um traslado, e Jurassic Lake faz um voo fretado desde El Calafate aos sábados.',
		'<strong>Dia de chegada.</strong> A Río Manso Lodge diz nas perguntas frequentes que o dia de chegada é para se instalar, conhecer os guias e jantar de boas-vindas, e que se começa a pescar na manhã seguinte (lido em 30/10/2026). As demais páginas não esclarecem.',
		'<strong>Os dias de pesca.</strong> Seis seguidos em Pirá Lodge, segundo o roteiro da Fly Water Travel, e de domingo a sexta em Las Buitreras.',
		'<strong>Saída.</strong> Em Pirá Lodge é o dia 9 do roteiro; em Las Buitreras, o sábado, com voo a El Calafate e conexão para Buenos Aires.',
	],
	'h_grupo'         => 'Quantos pescadores por guia ou barco',
	'grupo_p'         => 'O habitual são dois. Pirá Lodge: dois por guia ({x:fwt_pira|Fly Water Travel}); Itatí Lodge: dois de cada vez no barco ({x:fwt_itati|Fly Water Travel}), ambas as páginas lidas em 13/11/2026. Pinti’s: dois pescadores por barco ({x:yd_pinti|Yellow Dog}, 13/11/2026). Las Buitreras e Glencross: dois por guia ({x:fwt_buit|Fly Water Travel}, 13/11/2026). Em Jurassic Lake, um guia acompanha dois ou três pescadores conforme o trecho ({x:jl_rates|jurassiclake.com}, 10/11/2026). Nos lugares da Nervous Waters que lemos, a base é quarto duplo e guia compartilhado, e o quarto e o barco individuais dependem da disponibilidade e podem custar mais ({x:nw_faq|as perguntas frequentes}, 15/11/2026).',
	'h_hab'           => 'Quarto duplo e acréscimo por individual',
	'hab_p'           => 'Os preços por pessoa destas páginas são em quarto duplo. Se você viaja sozinho e quer quarto e barco só seus, o acréscimo muda muito: 50 % para quarto e barco individuais segundo a página de Pinti’s na Yellow Dog (13/11/2026); 60 % do pacote em um lugar de Corrientes e US$ 200 por noite em um de Chubut segundo {n:paquete-incluye-no-incluye|a nossa nota sobre o que inclui um pacote}; um suplemento de guia individual de US$ 250 por pessoa por dia na Río Manso Lodge ({x:rm_rates|a página de tarifas}, 10/11/2026). Kau Tapen e Villa María trazem quarto individual por padrão ({x:nw_faq|Nervous Waters}, 15/11/2026).',
	'h_acomp'         => 'Se viaja alguém que não pesca',
	'acomp_p'         => 'Existe e é cobrado à parte. Pinti’s Dorado on the Fly publica US$ 450 por noite para quem não pesca e avisa que não é uma boa opção para acompanhantes, porque não oferece atividades guiadas sem pesca ({x:yd_pinti|Yellow Dog}, 13/11/2026). Villa María e Kau Tapen: US$ 650 por noite ({n:precio-pesca-patagonia-sur|nossa nota}, Nervous Waters, 13/11/2026). Río Manso Lodge: US$ 800 por noite ou US$ 5.400 por semana, com atividades guiadas sem pesca incluídas ({x:rm_rates|a página de tarifas}, 10/11/2026). Pirá Lodge menciona cavalgadas, observação de aves e saídas noturnas ({x:nw_pira|Nervous Waters}, 13/11/2026), e a Set Fly Fishing lista passeios a cavalo, vinícolas, aves e spa ({x:sff_info|seu guia de viagem}, 15/11/2026). A página de Los Plateados não menciona atividades sem pesca. Se viaja alguém que não pesca, é das primeiras coisas para nos dizer.',
	'h_incluye'       => 'O que costuma estar incluído e o que não',
	'incluye_i'       => 'Muda de um lugar para outro, e por isso dois preços só se comparam quando cobrem o mesmo: {n:paquete-incluye-no-incluye|o que inclui um pacote de pesca na Argentina e o que custa à parte}. O que dizem estas páginas:',
	'incluye_l'       => [
		'<strong>Licença:</strong> incluída na Itatí Lodge e não incluída na Pirá Lodge, segundo a Fly Water Travel (13/11/2026); a Nervous Waters diz que pede os dados para providenciá-la antes da chegada (15/11/2026). Como tirar: {n:permiso-pesca-argentina-paso-a-paso|licença de pesca na Argentina, passo a passo}.',
		'<strong>Voos e traslados:</strong> a Itatí Lodge inclui os voos fretados entre Buenos Aires e o lugar; as passagens internacionais ficam de fora onde são mencionadas (Itatí, Pirá). As noites em Buenos Aires também não estão incluídas.',
		'<strong>Equipamento:</strong> empréstimo de varas, molinetes e linhas incluído na Pirá Lodge, em Pinti’s e na Suindá Lodge («sem custo adicional»); a Río Manso Lodge cobra US$ 80 por pescador por dia o equipamento completo. Para a viagem, a Set Fly Fishing avisa que a segurança aeroportuária argentina não admite varas nem molinetes na cabine dos voos internos: vão na bagagem despachada (15/11/2026).',
		'<strong>Lavanderia:</strong> incluída em Pinti’s ({x:yd_pinti|Yellow Dog}) e não incluída na Itatí Lodge ({x:fwt_itati|Fly Water Travel}).',
		'<strong>Gorjetas:</strong> não incluídas. A Set Fly Fishing sugere entre 12 % e 20 % do custo da viagem em um fundo comum dividido entre guias, cozinha e limpeza (15/11/2026); Jurassic Lake sugere de US$ 80 a 100 por pessoa por dia ({n:precio-pesca-patagonia-sur|nossa nota}).',
	],
	'h_clima'         => 'Clima, vento e por que muitas ofertas são de semana',
	'clima_p1'        => 'As páginas não explicam por que as ofertas são de semana. O que mostram: saídas fixas em torno de um dia (Jurassic Lake faz um voo fretado todo sábado de manhã, segundo a página de localização), viagens longas até o lugar (a Fly Water Travel avisa que a Pirá Lodge pede estar preparado para um programa de viagem longo) e poucas vagas por semana, de 6 a 12 pescadores na tabela acima.',
	'clima_l'         => [
		'<strong>Dourado e calor:</strong> segundo a Set Fly Fishing (15/11/2026), de dezembro a março os dias têm média de 27 a 32 °C e chegam a uns 38 °C, embora seja raro; em abril e maio e de setembro a novembro ficam entre 16 e 25 °C. A página da Itatí Lodge diz «setembro a maio (evitar janeiro e fevereiro pelo calor)». As duas sessões por dia evitam o calor do meio-dia ({x:fwt_pira|Fly Water Travel}).',
		'<strong>Truta na Patagônia:</strong> a Río Manso Lodge diz que de novembro a abril os dias ficam entre 13 e 24 °C e que pode chover a qualquer momento ({x:rm_faq|as perguntas frequentes}, 30/10/2026).',
		'<strong>Vento:</strong> a Fly Water Travel diz de Las Buitreras que o vento é constante e deve ser esperado; a Tres Amigos Outfitters recomenda uma linha pensada para o vento frequente no {x:ta_sc|seu guia de Santa Cruz} (14/11/2026).',
		'<strong>Se o clima ou a água não deixam pescar:</strong> na Pirá Lodge pode-se voltar à piscina ou sair para observar a natureza ou cavalgar (Fly Water Travel, 13/11/2026). Nenhuma página que lemos diz o que acontece com o preço nesse caso: pergunte por escrito.',
	],
	'clima_c'         => 'As datas de cada temporada e os períodos de defeso estão no {cal|calendário de pesca}.',
	'h_menos'         => 'E se você quer menos dias',
	'menos_p'         => 'Algumas páginas publicam programas mais curtos com preço próprio: de 3 a 6 noites na Pirá Lodge e na Suindá Lodge ({x:nw_pira|Nervous Waters}, 13/11/2026), 4 noites e 3 dias na Itatí Lodge, meia semana em Glencross e em Los Plateados, e 3 e 4 noites em Jurassic Lake. Outras dizem em uma frase: a Yellow Dog escreve que em Pinti’s as estadias mais curtas também são bem-vindas; a Fly Water Travel, que a Pirá Lodge aceita estadias de duração variada; a Tres Amigos Outfitters, que pode adaptar a duração da estadia (16/11/2026); e Jurassic Lake oferece roteiros sob medida, chegando de outra cidade ou ficando mais tempo (10/11/2026). Como o preço muda com menos dias está na tarifa de cada lugar: pergunte por escrito.',
	'h_reservar'      => 'Quando convém reservar',
	'reservar_p'      => 'Nenhuma das páginas que lemos diz com quanta antecedência convém reservar a semana. O que uma delas, a Set Fly Fishing, publica é uma lista de preparação: a 90 dias ou mais da saída, o pagamento final, as passagens aéreas e o seguro de viagem; a 60 dias ou mais, os dados de voo e o equipamento reunido; a 7 dias, reconfirmar os voos (15/11/2026). É a linha do tempo de um lugar, não uma regra. Como cada semana tem poucas vagas (de 6 a 12 pescadores na tabela), o prudente é perguntar com tempo.',
	'h_confirmar'     => 'Como se confirma a disponibilidade',
	'confirmar_p'     => 'Cada página manda consultar a disponibilidade com o próprio lugar; a de Los Plateados tem um botão «Check availability». Quando você nos pede opções, nós fazemos isso: consultamos os lugares que combinam com o seu pedido e em 48 horas respondemos com o que há para as suas datas, lado a lado. O preço é o do lugar: você paga o mesmo que pagaria reservando direto.',
	'cta'             => '<strong>Conte o que você procura e montamos a melhor opção para você.</strong> Datas, quantos são, se viaja alguém que não pesca e o que você quer pescar: <a href="{viaje}">peça as suas opções aqui</a> e respondemos em 48 horas.',
	'faq_h'           => 'Perguntas frequentes',
	'faq'             => [
		[ 'Quanto dura uma semana de pesca em uma pousada?', 'Nas páginas que lemos, 7 noites e 6 dias de pesca; Jurassic Lake e Villa María contam 6,5 dias de pesca na semana de 7 noites (lidas entre 10 e 13/11/2026). Los Plateados conta 6 dias de pesca de sábado a sexta (16/11/2026).' ],
		[ 'Em que dia se chega e em que dia se sai?', 'A maioria das semanas vai de sábado a sábado (Las Buitreras, Glencross, Jurassic Lake e, segundo a Yellow Dog, a maioria dos pacotes de Pinti’s). Los Plateados vai de sábado a sexta e a semana de Villa María, de sexta a sexta. As meias semanas são de terça a sábado ou de sábado a terça em Jurassic Lake, e de quarta a sábado ou de sábado a quarta em Glencross.' ],
		[ 'Existe meia semana ou pacotes mais curtos?', 'Sim, em vários: de 3 a 6 noites na Pirá Lodge e na Suindá Lodge, 4 noites e 3 dias na Itatí Lodge, meia semana em Glencross e em Los Plateados, e programas de 3 e 4 noites em Jurassic Lake (de janeiro a abril). Quanto custa cada um está na página de cada lugar; algumas notas nossas reúnem os preços com a data: {n:precio-viaje-dorado-lodge|quanto custa uma viagem de pesca de dourado}.' ],
		[ 'Quantos pescadores vão por guia ou barco?', 'Dois na Pirá Lodge, Itatí Lodge, Pinti’s, Las Buitreras e Glencross; um guia para dois ou três pescadores em Jurassic Lake, conforme o trecho. Fontes e datas acima.' ],
		[ 'Fico em quarto duplo? Quanto custa o individual?', 'Os preços destas páginas são por pessoa em quarto duplo. O individual é cobrado à parte: 50 % para quarto e barco em Pinti’s (Yellow Dog, 13/11/2026), 60 % do pacote em um lugar de Corrientes e US$ 200 por noite em um de Chubut (nossa nota sobre o que inclui um pacote), e US$ 250 por pessoa por dia de guia individual na Río Manso Lodge (10/11/2026).' ],
		[ 'Pode ir um acompanhante que não pesca?', 'Sim, e é cobrado à parte: US$ 450 por noite em Pinti’s (que avisa que não oferece atividades guiadas sem pesca), US$ 650 em Villa María e Kau Tapen, e US$ 800 por noite na Río Manso Lodge, com atividades guiadas sem pesca incluídas. Fontes e datas acima.' ],
		[ 'O que uma semana inclui e o que não?', 'Depende do lugar. Nas páginas que lemos, costumam estar a hospedagem, as refeições, o guia e o equipamento emprestado; a licença, as passagens, as noites em Buenos Aires, a lavanderia e as gorjetas mudam de um lugar para outro. A Itatí Lodge inclui licença e voos fretados; a Pirá Lodge não inclui licença nem passagens (Fly Water Travel, 13/11/2026).' ],
		[ 'O que acontece se o clima ou o rio não deixam pescar?', 'Nenhuma página que lemos explica o que acontece com o preço. Elas dizem o que se faz nesse dia: na Pirá Lodge pode-se voltar à piscina, observar a natureza ou cavalgar (Fly Water Travel, 13/11/2026). Pergunte por escrito, junto com as condições de pagamento e de troca de data.' ],
		[ 'Com quanta antecedência convém reservar?', 'Nenhuma página que lemos diz. A Set Fly Fishing publica uma lista: a 90 dias ou mais da saída, o pagamento final, as passagens e o seguro; a 60 dias ou mais, os dados de voo (15/11/2026). Como as semanas têm de 6 a 12 vagas, o prudente é perguntar com tempo.' ],
		[ 'Como se confirma a disponibilidade de uma semana?', 'Quem confirma é o próprio lugar. Quando você nos pede opções, consultamos os lugares que combinam e respondemos em 48 horas, lado a lado. É grátis para você e o preço é o do lugar.' ],
	],
	'cierre'          => '<em>Tudo nesta página vem de páginas públicas lidas entre 30 de outubro e 16 de novembro de 2026, cada uma com a sua data, e de nossas notas com data. Mudam a cada temporada. Não estivemos nesses lugares: confirme as datas, o que inclui e as condições com o lugar antes de reservar.</em>',
];

// Sources (name per language; date = when the page was read).
$f_ = fn( string $es, string $en, string $pt, string $f ) => [ 'es' => $es, 'en' => $en, 'pt' => $pt, 'f' => $f, 'o' => 0 ];
$fuente_nombre = [
	'fwt_itati' => $f_( 'Fly Water Travel: Itati Lodge (programas, tarifas 2026/27, qué incluye)', 'Fly Water Travel: Itati Lodge (programs, 2026/27 rates, what is included)', 'Fly Water Travel: Itati Lodge (programas, tarifas 2026/27, o que inclui)', '20261113' ),
	'fwt_pira'  => $f_( 'Fly Water Travel: Pira Lodge (itinerario, horarios, tarifas 2026-27, qué incluye)', 'Fly Water Travel: Pira Lodge (itinerary, schedule, 2026-27 rates, what is included)', 'Fly Water Travel: Pira Lodge (roteiro, horários, tarifas 2026-27, o que inclui)', '20261113' ),
	'fwt_pinti' => $f_( 'Fly Water Travel: Dorados on the Fly (programas y capacidad)', 'Fly Water Travel: Dorados on the Fly (programs and capacity)', 'Fly Water Travel: Dorados on the Fly (programas e capacidade)', '20261113' ),
	'fwt_buit'  => $f_( 'Fly Water Travel: Las Buitreras Lodge (itinerario de sábado a sábado, sesiones, viento)', 'Fly Water Travel: Las Buitreras Lodge (Saturday-to-Saturday itinerary, sessions, wind)', 'Fly Water Travel: Las Buitreras Lodge (roteiro de sábado a sábado, sessões, vento)', '20261113' ),
	'fwt_glen'  => $f_( 'Fly Water Travel: Estancia Glencross (semana completa y media semana)', 'Fly Water Travel: Estancia Glencross (full week and half week)', 'Fly Water Travel: Estancia Glencross (semana completa e meia semana)', '20261113' ),
	'nw_pira'   => $f_( 'Nervous Waters: Pirá Lodge (programas de 3 a 7 noches, actividades)', 'Nervous Waters: Pirá Lodge (3- to 7-night programs, activities)', 'Nervous Waters: Pirá Lodge (programas de 3 a 7 noites, atividades)', '20261113' ),
	'nw_suinda' => $f_( 'Nervous Waters: Suindá Lodge (programas de 3 a 7 noches, equipo de préstamo)', 'Nervous Waters: Suindá Lodge (3- to 7-night programs, loaner equipment)', 'Nervous Waters: Suindá Lodge (programas de 3 a 7 noites, equipamento emprestado)', '20261113' ),
	'nw_faq'    => $f_( 'Nervous Waters: preguntas frecuentes (habitación, guía compartido, licencia, aeropuerto)', 'Nervous Waters: frequently asked questions (room, shared guide, license, airport)', 'Nervous Waters: perguntas frequentes (quarto, guia compartilhado, licença, aeroporto)', '20261115' ),
	'sff_itati' => $f_( 'Set Fly Fishing: Itati Lodge (programas, habitaciones, temporada)', 'Set Fly Fishing: Itati Lodge (programs, rooms, season)', 'Set Fly Fishing: Itati Lodge (programas, quartos, temporada)', '20261115' ),
	'sff_info'  => $f_( 'Set Fly Fishing: guía de información de viaje (aeropuertos, equipaje, propinas, clima, lista de preparación)', 'Set Fly Fishing: trip information package (airports, luggage, gratuities, weather, checklist)', 'Set Fly Fishing: guia de informações de viagem (aeroportos, bagagem, gorjetas, clima, lista de preparação)', '20261115' ),
	'yd_pinti'  => $f_( 'Yellow Dog Flyfishing: Pinti’s Dorado on the Fly (estadía típica, horarios, recargos, qué incluye)', 'Yellow Dog Flyfishing: Pinti’s Dorado on the Fly (typical stay, schedule, surcharges, what is included)', 'Yellow Dog Flyfishing: Pinti’s Dorado on the Fly (estadia típica, horários, acréscimos, o que inclui)', '20261113' ),
	'jl_rates'  => $f_( 'Jurassic Lake Lodge: tarifas y programas', 'Jurassic Lake Lodge: rates and programs', 'Jurassic Lake Lodge: tarifas e programas', '20261110' ),
	'jl_loc'    => $f_( 'Jurassic Lake Lodge: cómo llegar (vuelo chárter de los sábados)', 'Jurassic Lake Lodge: getting there (Saturday charter flight)', 'Jurassic Lake Lodge: como chegar (voo fretado de sábado)', '20261030' ),
	'ta_plat'   => $f_( 'Tres Amigos Outfitters: Los Plateados (programa, días, pescadores, preguntas frecuentes)', 'Tres Amigos Outfitters: Los Plateados (program, days, anglers, FAQ)', 'Tres Amigos Outfitters: Los Plateados (programa, dias, pescadores, perguntas frequentes)', '20261116' ),
	'ta_sc'     => $f_( 'Tres Amigos Outfitters: guía de Santa Cruz para Los Plateados (equipo y viento)', 'Tres Amigos Outfitters: Santa Cruz guide for Los Plateados (gear and wind)', 'Tres Amigos Outfitters: guia de Santa Cruz para Los Plateados (equipamento e vento)', '20261114' ),
	'rm_rates'  => $f_( 'Río Manso Lodge: tarifas y detalles 2026-27 (acompañante, suplementos, equipo)', 'Río Manso Lodge: 2026-27 rates and details (non-angler, supplements, gear)', 'Río Manso Lodge: tarifas e detalhes 2026-27 (acompanhante, suplementos, equipamento)', '20261110' ),
	'rm_faq'    => $f_( 'Río Manso Lodge: preguntas frecuentes (día de llegada, clima)', 'Río Manso Lodge: frequently asked questions (arrival day, weather)', 'Río Manso Lodge: perguntas frequentes (dia de chegada, clima)', '20261030' ),
];

$origen = 'Cómo funciona una semana de pesca en un lodge, 16/11/2026 (pedido del dueño tras una conversación con Grok que mostró el hueco). Armada solo con páginas públicas ya leídas con investigar.yml (rama investigacion: corrientes-1 y gallegos-1 del 13/11/2026, equipo-dorado del 15/11/2026, r1-tarifas-lodges del 10/11/2026, lodges-lote1 y lodges-lote1d del 30/10/2026, santacruz-1 del 14/11/2026 y saptdf-1 del 16/11/2026) y con nuestras notas de precios con fecha. Cada dato dice la página y la fecha. No se publica como regla que algunos lugares dejan pescar menos días al precio de la semana (lo conocemos por consultas reales, ninguna página lo dice): solo lo que las páginas dicen de estadías más cortas. Sin precios de semana (están en las notas de precios), sin nombres de personas, teléfonos ni correos, sin comisiones ni condiciones de pago o cancelación. La frase «la mejor opción para vos» se ancla al pedido del lector (regla del dueño, 16/11/2026). Foto provisoria: la de la nota paquete-incluye-no-incluye (Commons, Lucas Solari). Docs: docs/estado.md.';

$armar = function ( array $n ): string {
	return pm_sc_p( $n['p0'] ) . pm_sc_lista( $n['resp'] )
		. pm_sc_h2( $n['h_formatos'] ) . pm_sc_p( $n['formatos_i'] ) . pm_tp_tabla( $n['tabla_cols'], $n['tabla_rows'] ) . pm_sc_p( $n['formatos_c'] )
		. pm_sc_h2( $n['h_dia'] ) . pm_sc_p( $n['dia_i'] ) . pm_sc_lista( $n['dia_l'] )
		. pm_sc_h2( $n['h_semana'] ) . pm_pp_olista( $n['semana_s'] )
		. pm_sc_h2( $n['h_grupo'] ) . pm_sc_p( $n['grupo_p'] )
		. pm_sc_h2( $n['h_hab'] ) . pm_sc_p( $n['hab_p'] )
		. pm_sc_h2( $n['h_acomp'] ) . pm_sc_p( $n['acomp_p'] )
		. pm_sc_h2( $n['h_incluye'] ) . pm_sc_p( $n['incluye_i'] ) . pm_sc_lista( $n['incluye_l'] )
		. pm_sc_h2( $n['h_clima'] ) . pm_sc_p( $n['clima_p1'] ) . pm_sc_lista( $n['clima_l'] ) . pm_sc_p( $n['clima_c'] )
		. pm_sc_h2( $n['h_menos'] ) . pm_sc_p( $n['menos_p'] )
		. pm_sc_h2( $n['h_reservar'] ) . pm_sc_p( $n['reservar_p'] )
		. pm_sc_h2( $n['h_confirmar'] ) . pm_sc_p( $n['confirmar_p'] ) . pm_sc_p( $n['cta'] )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] )
		. pm_sc_p( $n['cierre'] );
};

// Inbound links: one paragraph after the short answer of the notes that talk about weeks, prices and what a package includes.
$enlace_ancla = [
	'es' => 'Cómo funciona una semana de pesca en un lodge en Argentina',
	'en' => 'How a fishing week at an Argentina lodge works',
	'pt' => 'Como funciona uma semana de pesca em uma pousada na Argentina',
];
$destinos_enlace = [
	[
		'tipo' => 'pm_nota', 'claves' => [ 'precio-semana-trucha-patagonia-norte', 'precio-viaje-dorado-lodge', 'paquete-incluye-no-incluye', 'cuanto-cuesta-viaje-pesca-argentina' ],
		'ancla' => [ 'es' => '/Respuesta corta/u', 'en' => '/Short answer/u', 'pt' => '/Resposta curta/u' ],
		'texto' => [
			'es' => 'Cómo es la semana en sí (formatos, llegada y salida, pescadores por guía, habitación y acompañante): %s.',
			'en' => 'What the week itself looks like (formats, arrival and departure, anglers per guide, room and companion): %s.',
			'pt' => 'Como é a semana em si (formatos, chegada e saída, pescadores por guia, quarto e acompanhante): %s.',
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
