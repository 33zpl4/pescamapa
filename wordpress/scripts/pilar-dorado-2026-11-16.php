<?php
/**
 * Dorado pillar note (clave golden-dorado-guia, es/en/pt), 16/11/2026, step 2 of docs/plan-dorado-surubi-2026-11-16.md.
 * Adds (1) the section "Which destination suits you? Size, sight fishing, wildlife and numbers" (a table of what the pages of the places say, each fact
 * attributed and dated: Nervous Waters pages for Suindá and Pirá, the page of Dorados on the Fly, Globe-Truiteur, all read with investigar.yml on 13 and 15/11/2026; the
 * Flylords film and the flights from Buenos Aires were already in our own pages), (2) a visible FAQ of 8 questions (a pm-faq group: the FAQPage JSON-LD is built from it)
 * that carries the Keyword Planner variants of 15/11/2026 ("golden dorado fishing argentina", "golden dorado fly fishing argentina", "argentina dorado", "dorado fish argentina",
 * "golden dorado fishing buenos aires", "dorado fishing in buenos aires"; Brazil: pousadas), (3) a list of the cost, permit, week and planning-hub notes, (4) the new call to action
 * ("Contanos qué buscás y te armamos la mejor opción para vos", button with ?especie=dorado), replacing the old paragraph, (5) "operadores" -> "lugares" in the sentences it
 * touches and an honest line about the Delta in "what this guide does not cover".
 * NOT written, on purpose: sizes, price ranges or total costs from an assistant answer without a source read; any ranking or "the best"; names of people; contacts; commissions.
 * Idempotent: a language whose pillar already has the section is not touched. Dry run first ($aplicar = false).
 * Run with wp-script.yml (script = pilar-dorado-2026-11-16).
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
	/** Replace the text of one heading (exact plain text), keeping its markup; null when it is not there. */
	function pm_sc_renombrar( string $c, string $viejo, string $nuevo ): ?string {
		$n = 0;
		$r = preg_replace_callback( '#(<h[1-6][^>]*>)(.*?)(</h[1-6]>)#su', function ( $m ) use ( $viejo, $nuevo, &$n ) {
			if ( 0 === $n && trim( wp_strip_all_tags( $m[2] ) ) === $viejo ) {
				$n++;
				return $m[1] . $nuevo . $m[3];
			}
			return $m[0];
		}, $c );
		return $n ? $r : null;
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
	function pm_sc_campo( int $id, string $campo, string $valor, string $etiqueta ): void {
		update_field( $campo, $valor, $id );
		if ( pm_campo( $campo, $id ) !== $valor ) {
			WP_CLI::error( "$etiqueta: el campo $campo no quedó como se esperaba." );
		}
	}
	function pm_sc_modo( bool $aplicar ): string {
		return $aplicar ? 'APLICA' : 'SIMULACRO';
	}
}



$aplicar = true;
$clave   = 'golden-dorado-guia';
$hoy     = '20261116';
$leido13 = '13/11/2026';

// Public pages used by the new sections (all read with investigar.yml; dates in each text).
$u = [
	'sui'   => 'https://www.nervouswaters.com/the-lodges/suinda-lodge/',
	'pira'  => 'https://www.nervouswaters.com/the-lodges/pira-lodge/',
	'dof'   => 'https://dorados-on-the-fly.vercel.app/',
	'globe' => 'https://www.globe-truiteur.com/en/2026/07/27/fly-fishing-gear-golden-dorado/',
	'wa'    => 'https://www.welcomeargentina.com/pesca/riodelaplata.html',
	'fb'    => 'https://www.fishbase.se/summary/Salminus-brasiliensis.html',
];

$T = [];
$T['es'] = [
	'h_estilo' => '¿Qué destino te conviene? Tamaño, pesca a la vista, fauna y cantidad',
	'ya'       => '/qué destino te conviene/iu',
	'antes_de' => '/mejor época/iu',
	'estilo_i' => 'No hay un destino mejor para todos: depende de lo que busques. Esto es lo que dicen las páginas que leímos, cada dato con su fuente y su fecha. No es un ranking.',
	'cab'      => [ 'Si buscás…', 'Dónde mirar', 'Qué dicen las páginas que leímos' ],
	'filas'    => [
		[ '<strong>Peces grandes</strong>', 'Alto Paraná: {g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}, {g:paso-de-la-patria|Paso de la Patria}', 'Suindá, en Itatí, dice que su tramo «produce de forma constante algunos de los dorados más grandes de todo el sistema del Paraná» (traducción nuestra; {u:sui|página}, leída el 13/11/2026). En Itá Ibaté, la página de Dorados on the Fly dice que el dorado de la zona promedia de 6 a 8 libras (de 2,7 a 3,6 kg) y que hay chances de peces de unas 30 libras (13,6 kg) ({u:dof|página}, 13/11/2026). Un documental de Flylords que citamos en {n:cuando-pescar-dorado|nuestra nota de temporada} habla del mejor potencial de peces grandes en el Alto Paraná.' ],
		[ '<strong>Pesca a la vista (aguas claras)</strong>', 'Alto Paraná y {g:esteros-del-ibera|Esteros del Iberá}', 'Suindá dice que lo suyo es lanzar a dorados a la vista en bancos de arena, en agua poco profunda; Pirá, en los Esteros del Iberá, dice que el agua cristalina permite lanzar tanto a ciegas como a peces a la vista ({u:pira|página}, 13/11/2026). Según Globe-Truiteur (27/7/2026), en el Paraná se busca pez grande en agua grande, y en el río Corriente, de aguas claras, la pesca se vuelve más visual ({u:globe|artículo}, leído el 15/11/2026).' ],
		[ '<strong>Fauna y paisaje</strong>', '{g:esteros-del-ibera|Esteros del Iberá}', 'Pirá dice que está en la reserva de los Esteros del Iberá, con más de 3,2 millones de acres de humedal (unas 1,3 millones de hectáreas), y ofrece además de la pesca cabalgatas, avistaje de aves, salidas nocturnas de fauna y observación de estrellas ({u:pira|página}, 13/11/2026).' ],
		[ '<strong>Cantidad de peces</strong>', '—', 'Las páginas que leímos hablan de tamaño y de estilo, no de peces por día. Todavía no publicamos una cifra de peces por día para ningún tramo: preguntale al lugar por su promedio de la temporada.' ],
		[ '<strong>Surubí, riachos e islas</strong>', 'Paraná medio: {g:goya|Goya}, {g:esquina|Esquina}, {g:la-paz|La Paz}', 'Nuestras guías de Goya y Esquina tratan sobre todo del surubí y del dorado entre riachos e islas: temporada, reglas, licencia y precios con fecha. La de La Paz (Entre Ríos) reúne licencia, aranceles y reglas.' ],
		[ '<strong>Cerca de Buenos Aires</strong>', '{g:itati|Itatí}, {g:esteros-del-ibera|Esteros del Iberá}', 'Los lugares que leímos están a un vuelo de una hora de Buenos Aires a Corrientes o Resistencia, y de ahí a entre 40 minutos y 4,5 horas por tierra (páginas de Itatí Lodge, Suindá y Pirá, 13/11/2026; el detalle está en cada guía). Del Delta, mirá la pregunta de más abajo.' ],
	],
	'h_faq'    => 'Preguntas frecuentes sobre la pesca de dorado en la Argentina',
	'faq_ya'   => '/preguntas frecuentes sobre la pesca de dorado/iu',
	'faq'      => [
		[ '¿Qué es el dorado y dónde vive?', 'Es un pez de agua dulce (<em>Salminus brasiliensis</em>). Según FishBase ({u:fb|leída el 16/11/2026}), el máximo publicado es de 100 cm de largo estándar y 31,4 kg; la IGFA registra 25,28 kg como récord todo equipo (solo el peso). Más en {esp:dorado|la ficha del dorado}.' ],
		[ '¿Dónde se pesca el dorado en la Argentina?', 'Nuestras guías cubren el Alto Paraná ({g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}, {g:paso-de-la-patria|Paso de la Patria}), los {g:esteros-del-ibera|Esteros del Iberá} y el Paraná medio ({g:goya|Goya}, {g:esquina|Esquina}, {g:la-paz|La Paz}). La tabla de arriba resume qué dice cada lugar sobre tamaño, pesca a la vista, fauna y cantidad.' ],
		[ '¿Cuándo es la mejor época para pescar dorado?', 'Las páginas que leímos no coinciden: Suindá dice octubre a mayo, Itatí Lodge dice septiembre a mayo y evitar enero y febrero por el calor, y Pirá dice todo el año (páginas leídas el 13/11/2026). Las dos ventanas que más se repiten son octubre–noviembre y fines de marzo–mayo. Fuentes y salvedades en {n:cuando-pescar-dorado|nuestra nota de temporada}.' ],
		[ '¿Se puede pescar dorado con mosca?', 'Sí, según las páginas de los lugares: Suindá y Pirá hablan de pesca con mosca y también de spinning; Itatí Lodge describe equipo de mosca; y la página de Dorados on the Fly habla de pesca a la vista con mosca y de moscas secas para pirá pitá y pacú. Qué llevar está en la sección de equipo de esta guía.' ],
		[ '¿Se puede pescar dorado cerca de Buenos Aires?', 'Los lugares de Corrientes que leímos están a un vuelo de una hora de Buenos Aires a Corrientes o Resistencia, más el traslado por tierra (detalle en {g:itati|Itatí} y en {g:esteros-del-ibera|Esteros del Iberá}). Del Delta, una página de turismo ({u:wa|Welcome Argentina}, sin fecha de publicación visible, leída el 16/11/2026) dice que con el calor llegan dorado, surubí y patí a los pozos del Río de la Plata y a ríos del Delta, aunque no alcanzan los portes de aguas arriba. No publicamos reglas, lugares ni tarifas del Delta porque no leímos una fuente con fecha; cada provincia tiene sus reglas.' ],
		[ '¿Cuánto cuesta un viaje de pesca de dorado?', 'Depende del lugar y de las noches. Suindá publica, para 2026-27, de US$ 4.050 por 3 noches a US$ 7.950 por 7 noches, por persona en habitación doble y con guía compartido (página leída el 13/11/2026); Itatí Lodge no publica tarifa. Hay una opción más barata, en posada: {n:viaje-dorado-amigos-precios|viaje de dorado con amigos}. Comparación por noches: {n:precio-viaje-dorado-lodge|cuánto cuesta un viaje de pesca de dorado}; viaje completo: {n:cuanto-cuesta-viaje-pesca-argentina|cuánto cuesta un viaje de pesca a la Argentina}.' ],
		[ '¿Hace falta licencia y la incluye el lugar?', 'Sí, la de Corrientes; para quien vive fuera de la Argentina, la de turista extranjero, que se calcula en litros de nafta súper (16 litros por un día hasta 36 por seis). Suindá la lista como no incluida y Uguay dice que la incluye. Paso a paso: {n:permiso-pesca-argentina-paso-a-paso|permiso de pesca paso a paso}; todas las regiones: {n:licencias-extranjeros-argentina|licencia de pesca para extranjeros}.' ],
		[ '¿Puedo quedarme con el dorado que pesque?', 'No en Corrientes: la Disposición 853/22 de Recursos Naturales (Boletín Oficial, 26/12/2022) fija que dorado y surubí son solo pesca y devolución. Las reglas de otras provincias son otras: mirá la guía de cada destino.' ],
	],
	'h_seguir' => 'Para seguir con tu viaje',
	'seguir_i' => 'Cada nota tiene su fuente y su fecha:',
	'seguir'   => [ 'precio-viaje-dorado-lodge', 'viaje-dorado-amigos-precios', 'cuanto-cuesta-viaje-pesca-argentina', 'permiso-pesca-argentina-paso-a-paso', 'semana-de-pesca-en-lodge-argentina', 'como-preparar-viaje-pesca-argentina' ],
	'cta'      => '<strong>Contanos qué buscás y te armamos la mejor opción para vos.</strong> Decinos fechas, cuántos van y qué querés pescar en <a href="{viaje}">el formulario del viaje</a>: sin costo, y te respondemos en 48 horas.',
	'cta_viejo' => '/¿Querés que te ayudemos a armarlo\?/u',
	'cubre'    => '/no cubre|does not cover|não cobre/iu',
	'cubre_viejo' => 'Los métodos por tramo (mosca, señuelos, carnada), las tarifas por día publicadas y el Delta cerca de Buenos Aires no están acá porque no los verificamos con una fuente y una fecha. Los sumamos cuando lo hagamos.',
	'cubre_nuevo' => 'Las reglas del Delta cerca de Buenos Aires, sus lugares y sus tarifas no están acá: de esa zona leímos una sola página de turismo, sin fecha, y no los verificamos con una fuente y una fecha. Los sumamos cuando lo hagamos. Los métodos por tramo están en la sección de equipo y las tarifas publicadas por cada lugar, en las notas de precios.',
	'reemplazos' => [
		[ 'Los operadores del Alto Paraná en Corrientes repiten', 'Los lugares del Alto Paraná en Corrientes que leímos repiten' ],
		[ 'Los meses que más repiten los operadores son', 'Los meses que más repiten los lugares que leímos son' ],
		[ 'los meses que repiten los operadores,', 'los meses que repiten los lugares que leímos,' ],
		[ 'Las ventanas cambian según el operador y el tramo', 'Las ventanas cambian según el lugar y el tramo' ],
		[ 'algunos operadores ponen el pico antes', 'algunos lugares ponen el pico antes' ],
		[ 'con lo que publica cada operador y la fecha', 'con lo que publica cada lugar y la fecha' ],
		[ 'No pescamos con los operadores ni con las personas citadas', 'No pescamos en los lugares ni con las personas citadas' ],
	],
	'dato'     => 'Según las páginas de los lugares que leímos (13/11/2026): peces grandes y pesca a la vista en el Alto Paraná, aguas claras y fauna en los Esteros del Iberá.',
	'f'        => [
		'sui'   => 'Nervous Waters: Suindá Lodge (el Alto Paraná, en Itatí)',
		'pira'  => 'Nervous Waters: Pirá Lodge (Esteros del Iberá)',
		'dof'   => 'Dorados on the Fly, página del lugar (Itá Ibaté)',
		'globe' => 'Globe-Truiteur (27/7/2026): lista de equipo de un guía, del Paraná a las aguas claras de Corrientes',
		'wa'    => 'Welcome Argentina: la pesca en el Río de la Plata y el Delta (sin fecha de publicación visible)',
		'fb'    => 'FishBase: Salminus brasiliensis (largo estándar y peso máximos publicados)',
	],
];
$T['en'] = [
	'h_estilo' => 'Which destination suits you? Size, sight fishing, wildlife and numbers',
	'ya'       => '/which destination suits you/iu',
	'antes_de' => '/best time/iu',
	'estilo_i' => 'There is no single best destination: it depends on what you are after. This is what the pages we read say, each fact with its source and date. It is not a ranking.',
	'cab'      => [ 'If you want…', 'Where to look', 'What the pages we read say' ],
	'filas'    => [
		[ '<strong>Big fish</strong>', 'Upper Paraná: {g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}, {g:paso-de-la-patria|Paso de la Patria}', 'Suindá, in Itatí, says its stretch “consistently produces some of the largest dorado across the whole Paraná system” ({u:sui|page} read on 13/11/2026). In Itá Ibaté, the Dorados on the Fly page says dorado in the area average 6 to 8 pounds (2.7 to 3.6 kg) with chances at fish of around 30 pounds (13.6 kg) ({u:dof|page}, 13/11/2026). A Flylords film we cite in {n:cuando-pescar-dorado|our season note} points to the Upper Paraná as having the best potential for big fish.' ],
		[ '<strong>Sight fishing (clear water)</strong>', 'Upper Paraná and the {g:esteros-del-ibera|Iberá Wetlands}', 'Suindá says its signature is sandbank sight-casting to dorado in shallow water; Pirá, in the Iberá Wetlands, says its crystal-clear water allows both blind and true sight-casting ({u:pira|page}, 13/11/2026). According to Globe-Truiteur (27/7/2026), on the Paraná you hunt big fish in big water, and on the clear río Corriente the fishing turns more visual ({u:globe|article}, read on 15/11/2026).' ],
		[ '<strong>Wildlife and scenery</strong>', '{g:esteros-del-ibera|Iberá Wetlands}', 'Pirá says it stands on the shore of the Iberá Marshland Nature Reserve, with more than 3.2 million acres of wetland (about 1.3 million hectares), and besides fishing it lists horseback riding, birdwatching, night wildlife outings and stargazing ({u:pira|page}, 13/11/2026).' ],
		[ '<strong>Numbers of fish</strong>', '—', 'The pages we read talk about size and style, not fish per day. We do not publish a daily number for any stretch yet: ask the place for its season average.' ],
		[ '<strong>Surubí, side channels and islands</strong>', 'Middle Paraná: {g:goya|Goya}, {g:esquina|Esquina}, {g:la-paz|La Paz}', 'Our Goya and Esquina guides are mostly about surubí and dorado among side channels and islands: season, rules, licence and dated prices. The La Paz (Entre Ríos) guide gathers licence, fees and rules.' ],
		[ '<strong>Close to Buenos Aires</strong>', '{g:itati|Itatí}, {g:esteros-del-ibera|Iberá Wetlands}', 'The places we read are one flight of about an hour from Buenos Aires to Corrientes or Resistencia, then 40 minutes to 4.5 hours by land (Itatí Lodge, Suindá and Pirá pages, 13/11/2026; details in each guide). For the Delta, see the question below.' ],
	],
	'h_faq'    => 'Golden dorado fishing in Argentina: frequently asked questions',
	'faq_ya'   => '/golden dorado fishing in argentina: frequently asked/iu',
	'faq'      => [
		[ 'What is the golden dorado and where does it live?', 'A freshwater fish (<em>Salminus brasiliensis</em>). According to FishBase ({u:fb|read on 16/11/2026}), the published maximum is 100 cm standard length and 31.4 kg; the IGFA records 25.28 kg as the all-tackle record (weight only). More on {esp:dorado|the golden dorado page}.' ],
		[ 'Where can you fish golden dorado in Argentina?', 'Our guides cover the Upper Paraná ({g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}, {g:paso-de-la-patria|Paso de la Patria}), the {g:esteros-del-ibera|Iberá Wetlands} and the middle Paraná ({g:goya|Goya}, {g:esquina|Esquina}, {g:la-paz|La Paz}). The table above sums up what each place says about size, sight fishing, wildlife and numbers.' ],
		[ 'When is the best time to fish golden dorado?', 'The pages we read do not agree: Suindá says October to May, Itatí Lodge says September to May and to avoid January and February for the heat, and Pirá says year-round (pages read on 13/11/2026). The two windows repeated most are October–November and late March–May. Sources and caveats in {n:cuando-pescar-dorado|our season note}.' ],
		[ 'Can you fly fish for golden dorado in Argentina?', 'Yes, according to the pages of the places: Suindá and Pirá talk about fly fishing and also spinning; Itatí Lodge describes fly gear; and the Dorados on the Fly page talks about sight fishing with the fly and dry flies for pirá pitá and pacú. What to bring is in the gear section of this guide.' ],
		[ 'Is there golden dorado fishing near Buenos Aires?', 'The Corrientes places we read are one flight of about an hour from Buenos Aires to Corrientes or Resistencia, plus the transfer by land (details in {g:itati|Itatí} and {g:esteros-del-ibera|Iberá Wetlands}). For the Delta, a tourism page ({u:wa|Welcome Argentina}, no visible publication date, read on 16/11/2026) says that in summer dorado, surubí and patí reach the deep holes of the Río de la Plata and Delta rivers, though they do not reach the sizes found upriver. We publish no rules, places or rates for the Delta because we have not read a dated source; every province has its own rules.' ],
		[ 'How much does a golden dorado fishing trip cost?', 'It depends on the place and the nights. Suindá publishes, for 2026-27, from US$ 4,050 for 3 nights to US$ 7,950 for 7 nights, per person in a double room with a shared guide (page read on 13/11/2026); Itatí Lodge does not publish a rate. A cheaper option, at an inn: {n:viaje-dorado-amigos-precios|golden dorado trip with friends}. Comparison by nights: {n:precio-viaje-dorado-lodge|how much does a dorado fishing trip cost}; whole trip: {n:cuanto-cuesta-viaje-pesca-argentina|Argentina fishing trip cost}.' ],
		[ 'Do I need a licence, and does the place include it?', 'Yes, the Corrientes one; for people who live outside Argentina, the foreign-tourist licence, set in litres of premium petrol (16 litres for one day up to 36 for six). Suindá lists it as not included and Uguay says it includes it. Step by step: {n:permiso-pesca-argentina-paso-a-paso|fishing permit step by step}; all regions: {n:licencias-extranjeros-argentina|fishing licence for foreigners}.' ],
		[ 'Can I keep the golden dorado I catch?', 'Not in Corrientes: Disposición 853/22 of Recursos Naturales (Official Gazette, 26/12/2022) sets golden dorado and surubí as catch and release only. Other provinces have other rules: see each destination guide.' ],
	],
	'h_seguir' => 'To keep planning your trip',
	'seguir_i' => 'Each note has its source and its date:',
	'seguir'   => [ 'precio-viaje-dorado-lodge', 'viaje-dorado-amigos-precios', 'cuanto-cuesta-viaje-pesca-argentina', 'permiso-pesca-argentina-paso-a-paso', 'semana-de-pesca-en-lodge-argentina', 'como-preparar-viaje-pesca-argentina' ],
	'cta'      => '<strong>Tell us what you are looking for and we will put together the best option for you.</strong> Give us your dates, how many are going and what you want to catch in <a href="{viaje}">the trip form</a>: at no cost, and we reply within 48 hours.',
	'cta_viejo' => '/Want us to help plan it\?/u',
	'cubre'    => '/no cubre|does not cover|não cobre/iu',
	'cubre_viejo' => 'Methods by stretch (fly, lures, bait), published daily rates and the Delta near Buenos Aires are not here because we have not verified them with a source and a date. We add them when we do.',
	'cubre_nuevo' => 'The rules for the Delta near Buenos Aires, its places and its rates are not here: for that area we read a single tourism page, undated, and we have not verified them with a source and a date. We add them when we do. Methods by stretch are in the gear section and the rates each place publishes are in the price notes.',
	'reemplazos' => [
		[ 'Operators on the Upper Paraná in Corrientes repeat', 'The places we read on the Upper Paraná in Corrientes repeat' ],
		[ 'The months operators repeat most are', 'The months the places we read repeat most are' ],
		[ 'the months operators repeat', 'the months the places we read repeat' ],
		[ 'windows differ by operator and by stretch', 'windows differ by place and by stretch' ],
		[ 'some operators put the peak earlier', 'some places put the peak earlier' ],
		[ 'We have not fished with the operators or people cited', 'We have not fished at the places or with the people cited' ],
	],
	'dato'     => 'According to the pages of the places we read (13/11/2026): big fish and sight fishing on the Upper Paraná, clear water and wildlife in the Iberá Wetlands.',
	'f'        => [
		'sui'   => 'Nervous Waters: Suindá Lodge (Upper Paraná, at Itatí)',
		'pira'  => 'Nervous Waters: Pirá Lodge (Iberá Wetlands)',
		'dof'   => 'Dorados on the Fly, the place’s own page (Itá Ibaté)',
		'globe' => 'Globe-Truiteur (27/7/2026): a guide’s gear list, from the Paraná to the clear water of Corrientes',
		'wa'    => 'Welcome Argentina: fishing in the Río de la Plata and the Delta (no visible publication date)',
		'fb'    => 'FishBase: Salminus brasiliensis (published maximum standard length and weight)',
	],
];
$T['pt'] = [
	'h_estilo' => 'Qual destino combina com você? Tamanho, pesca à vista, fauna e quantidade',
	'ya'       => '/qual destino combina com você/iu',
	'antes_de' => '/melhor época/iu',
	'estilo_i' => 'Não há um destino melhor para todos: depende do que você procura. Isto é o que dizem as páginas que lemos, cada dado com sua fonte e sua data. Não é um ranking.',
	'cab'      => [ 'Se você procura…', 'Onde olhar', 'O que dizem as páginas que lemos' ],
	'filas'    => [
		[ '<strong>Peixes grandes</strong>', 'Alto Paraná: {g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}, {g:paso-de-la-patria|Paso de la Patria}', 'A Suindá, em Itatí, diz que seu trecho «produz de forma constante alguns dos maiores dourados de todo o sistema do Paraná» (tradução nossa; {u:sui|página}, lida em 13/11/2026). Em Itá Ibaté, a página da Dorados on the Fly diz que o dourado da região tem em média de 6 a 8 libras (de 2,7 a 3,6 kg) e que há chances de peixes de cerca de 30 libras (13,6 kg) ({u:dof|página}, 13/11/2026). Um documentário da Flylords que citamos em {n:cuando-pescar-dorado|nossa nota de temporada} fala do melhor potencial de peixes grandes no Alto Paraná.' ],
		[ '<strong>Pesca à vista (águas claras)</strong>', 'Alto Paraná e {g:esteros-del-ibera|Esteros do Iberá}', 'A Suindá diz que sua marca é lançar a dourados à vista em bancos de areia, em água rasa; a Pirá, nos Esteros do Iberá, diz que a água cristalina permite lançar tanto às cegas quanto a peixes à vista ({u:pira|página}, 13/11/2026). Segundo a Globe-Truiteur (27/7/2026), no Paraná se caça peixe grande em água grande, e no rio Corriente, de águas claras, a pesca fica mais visual ({u:globe|artigo}, lido em 15/11/2026).' ],
		[ '<strong>Fauna e paisagem</strong>', '{g:esteros-del-ibera|Esteros do Iberá}', 'A Pirá diz que fica na reserva dos Esteros do Iberá, com mais de 3,2 milhões de acres de área alagada (cerca de 1,3 milhão de hectares), e oferece, além da pesca, cavalgadas, observação de aves, saídas noturnas de fauna e observação de estrelas ({u:pira|página}, 13/11/2026).' ],
		[ '<strong>Quantidade de peixes</strong>', '—', 'As páginas que lemos falam de tamanho e de estilo, não de peixes por dia. Ainda não publicamos um número de peixes por dia para nenhum trecho: pergunte ao lugar a média da temporada.' ],
		[ '<strong>Surubim, riachos e ilhas</strong>', 'Paraná médio: {g:goya|Goya}, {g:esquina|Esquina}, {g:la-paz|La Paz}', 'Nossos guias de Goya e Esquina tratam sobretudo do surubim e do dourado entre riachos e ilhas: temporada, regras, licença e preços com data. O de La Paz (Entre Ríos) reúne licença, tarifas e regras.' ],
		[ '<strong>Perto de Buenos Aires</strong>', '{g:itati|Itatí}, {g:esteros-del-ibera|Esteros do Iberá}', 'Os lugares que lemos ficam a um voo de cerca de uma hora de Buenos Aires a Corrientes ou Resistencia, e dali entre 40 minutos e 4,5 horas por terra (páginas da Itatí Lodge, da Suindá e da Pirá, 13/11/2026; o detalhe está em cada guia). Sobre o Delta, veja a pergunta abaixo.' ],
	],
	'h_faq'    => 'Perguntas frequentes sobre a pesca de dourado na Argentina',
	'faq_ya'   => '/perguntas frequentes sobre a pesca de dourado/iu',
	'faq'      => [
		[ 'O que é o dourado e onde ele vive?', 'É um peixe de água doce (<em>Salminus brasiliensis</em>). Segundo a FishBase ({u:fb|lida em 16/11/2026}), o máximo publicado é de 100 cm de comprimento padrão e 31,4 kg; a IGFA registra 25,28 kg como recorde em qualquer equipamento (só o peso). Mais em {esp:dorado|a ficha do dourado}.' ],
		[ 'Onde se pesca dourado na Argentina?', 'Nossos guias cobrem o Alto Paraná ({g:itati|Itatí}, {g:ita-ibate|Itá Ibaté}, {g:paso-de-la-patria|Paso de la Patria}), os {g:esteros-del-ibera|Esteros do Iberá} e o Paraná médio ({g:goya|Goya}, {g:esquina|Esquina}, {g:la-paz|La Paz}). A tabela acima resume o que cada lugar diz sobre tamanho, pesca à vista, fauna e quantidade.' ],
		[ 'Qual é a melhor época para pescar dourado?', 'As páginas que lemos não coincidem: a Suindá diz outubro a maio, a Itatí Lodge diz setembro a maio e evitar janeiro e fevereiro pelo calor, e a Pirá diz o ano todo (páginas lidas em 13/11/2026). As duas janelas mais repetidas são outubro–novembro e fim de março–maio. Fontes e ressalvas em {n:cuando-pescar-dorado|nossa nota de temporada}.' ],
		[ 'Dá para pescar dourado com mosca?', 'Sim, segundo as páginas dos lugares: a Suindá e a Pirá falam de pesca com mosca e também de spinning; a Itatí Lodge descreve equipamento de mosca; e a página da Dorados on the Fly fala de pesca à vista com mosca e de moscas secas para pirá pitá e pacu. O que levar está na seção de equipamento deste guia.' ],
		[ 'Dá para pescar dourado perto de Buenos Aires?', 'Os lugares de Corrientes que lemos ficam a um voo de cerca de uma hora de Buenos Aires a Corrientes ou Resistencia, mais o traslado por terra (detalhe em {g:itati|Itatí} e em {g:esteros-del-ibera|Esteros do Iberá}). Do Delta, uma página de turismo ({u:wa|Welcome Argentina}, sem data de publicação visível, lida em 16/11/2026) diz que, com o calor, dourado, surubim e pati chegam aos poços do Rio da Prata e a rios do Delta, embora não alcancem os portes de rio acima. Não publicamos regras, lugares nem tarifas do Delta porque não lemos uma fonte com data; cada província tem suas regras.' ],
		[ 'Quanto custa uma viagem de pesca de dourado e que pousadas aparecem nas suas páginas?', 'Depende do lugar e das noites. A Suindá publica, para 2026-27, de US$ 4.050 por 3 noites a US$ 7.950 por 7 noites, por pessoa em quarto duplo e com guia compartilhado (página lida em 13/11/2026); a Itatí Lodge não publica tarifa. Uma opção mais barata, em pousada: {n:viaje-dorado-amigos-precios|viagem de dourado com amigos}. Comparação por noites: {n:precio-viaje-dorado-lodge|quanto custa uma viagem de pesca de dourado}; viagem completa: {n:cuanto-cuesta-viaje-pesca-argentina|quanto custa pescar na Argentina}. Os lugares estão nos guias de {g:itati|Itatí}, {g:ita-ibate|Itá Ibaté} e {g:esteros-del-ibera|Esteros do Iberá}.' ],
		[ 'Preciso de licença e o lugar a inclui?', 'Sim, a de Corrientes; para quem mora fora da Argentina, a de turista estrangeiro, calculada em litros de gasolina premium (16 litros por um dia até 36 por seis). A Suindá a lista como não incluída e a Uguay diz que a inclui. Passo a passo: {n:permiso-pesca-argentina-paso-a-paso|licença de pesca passo a passo}; todas as regiões: {n:licencias-extranjeros-argentina|licença de pesca para estrangeiros}.' ],
		[ 'Posso ficar com o dourado que pescar?', 'Não em Corrientes: a Disposición 853/22 dos Recursos Naturales (Boletim Oficial, 26/12/2022) fixa que dourado e surubim são só pesca e soltura. Outras províncias têm outras regras: veja o guia de cada destino.' ],
	],
	'h_seguir' => 'Para seguir com a sua viagem',
	'seguir_i' => 'Cada nota tem sua fonte e sua data:',
	'seguir'   => [ 'precio-viaje-dorado-lodge', 'viaje-dorado-amigos-precios', 'cuanto-cuesta-viaje-pesca-argentina', 'permiso-pesca-argentina-paso-a-paso', 'semana-de-pesca-en-lodge-argentina', 'como-preparar-viaje-pesca-argentina' ],
	'cta'      => '<strong>Conte o que você procura e montamos a melhor opção para você.</strong> Diga as datas, quantos vão e o que quer pescar no <a href="{viaje}">formulário da viagem</a>: sem custo, e respondemos em 48 horas.',
	'cta_viejo' => '/Quer que a gente ajude a montar\?/u',
	'cubre'    => '/no cubre|does not cover|não cobre/iu',
	'cubre_viejo' => 'Os métodos por trecho (mosca, iscas artificiais, isca natural), as tarifas diárias publicadas e o Delta perto de Buenos Aires não estão aqui porque não os verificamos com uma fonte e uma data. Vamos incluí-los quando fizermos isso.',
	'cubre_nuevo' => 'As regras do Delta perto de Buenos Aires, seus lugares e suas tarifas não estão aqui: dessa região lemos uma única página de turismo, sem data, e não os verificamos com uma fonte e uma data. Vamos incluí-los quando fizermos isso. Os métodos por trecho estão na seção de equipamento e as tarifas publicadas por cada lugar, nas notas de preços.',
	'reemplazos' => [
		[ 'Os operadores do Alto Paraná em Corrientes repetem', 'Os lugares que lemos no Alto Paraná em Corrientes repetem' ],
		[ 'Os meses que os operadores mais repetem são', 'Os meses que os lugares que lemos mais repetem são' ],
		[ 'os meses que os operadores repetem', 'os meses que os lugares que lemos repetem' ],
		[ 'conforme o operador e o trecho', 'conforme o lugar e o trecho' ],
		[ 'alguns operadores colocam o pico antes', 'alguns lugares colocam o pico antes' ],
		[ 'Não pescamos com os operadores nem com as pessoas citadas', 'Não pescamos nos lugares nem com as pessoas citadas' ],
	],
	'dato'     => 'Segundo as páginas dos lugares que lemos (13/11/2026): peixes grandes e pesca à vista no Alto Paraná, águas claras e fauna nos Esteros do Iberá.',
	'f'        => [
		'sui'   => 'Nervous Waters: Suindá Lodge (Alto Paraná, em Itatí)',
		'pira'  => 'Nervous Waters: Pirá Lodge (Esteros do Iberá)',
		'dof'   => 'Dorados on the Fly, página do próprio lugar (Itá Ibaté)',
		'globe' => 'Globe-Truiteur (27/7/2026): lista de equipamento de um guia, do Paraná às águas claras de Corrientes',
		'wa'    => 'Welcome Argentina: a pesca no Rio da Prata e no Delta (sem data de publicação visível)',
		'fb'    => 'FishBase: Salminus brasiliensis (comprimento padrão e peso máximos publicados)',
	],
];

/** {g:clave|texto} guide, {n:clave|texto} note, {esp:clave|texto} species page, {u:clave|texto} external page; {viaje}. Language first, Spanish as the fallback. */
$res = function ( string $html, string $i ) use ( $u ): string {
	$html = preg_replace_callback( '/\{(g|n|esp|u):([a-z0-9-]+)\|([^}]+)\}/u', function ( $m ) use ( $i, $u ) {
		[ , $tipo, $c, $txt ] = $m;
		if ( 'u' === $tipo ) {
			isset( $u[ $c ] ) || WP_CLI::error( "URL externa desconocida: $c" );
			return '<a href="' . esc_url( $u[ $c ] ) . '" rel="noopener">' . $txt . '</a>';
		}
		$cpt = [ 'g' => 'pm_destino', 'n' => 'pm_nota', 'esp' => 'pm_especie' ][ $tipo ];
		$p   = pm_buscar( $cpt, $c, $i ) ?? pm_buscar( $cpt, $c, 'es' );
		if ( ! $p ) {
			WP_CLI::warning( "no existe $cpt $c: va como texto." );
			return $txt;
		}
		return '<a href="' . esc_url( get_permalink( $p->ID ) ) . '">' . $txt . '</a>';
	}, $html );
	return str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) . '?especie=dorado' ), $html );
};

$tabla = function ( array $cab, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cab ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table {\"className\":\"pm-estilo\"} -->\n<figure class=\"wp-block-table pm-estilo\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->\n\n";
};

$cambios = 0;
$esp_p   = pm_buscar( 'pm_nota', $clave, 'es' );
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$t = $T[ $i ];
	$p = pm_buscar( 'pm_nota', $clave, $i );
	if ( ! $p ) {
		WP_CLI::warning( "($i): no existe la nota $clave; no toco nada." );
		continue;
	}
	$c = $p->post_content;
	if ( null !== pm_sc_pos_encabezado( $c, $t['ya'] ) ) {
		WP_CLI::log( "($i) #{$p->ID}: ya tiene la sección «{$t['h_estilo']}»; no toco nada." );
		continue;
	}
	$pos_estilo = pm_sc_pos_encabezado( $c, $t['antes_de'] );
	$pos_cubre  = pm_sc_pos_encabezado( $c, $t['cubre'] );
	if ( null === $pos_estilo || null === $pos_cubre ) {
		WP_CLI::warning( "($i) #{$p->ID}: no encuentro los encabezados de anclaje (época / no cubre); no toco nada." );
		continue;
	}
	// Seguir: the notes' own titles, in the language of the page.
	$seguir = [];
	foreach ( $t['seguir'] as $nc ) {
		$np = pm_buscar( 'pm_nota', $nc, $i ) ?? pm_buscar( 'pm_nota', $nc, 'es' );
		$np && $seguir[] = pm_sc_a( get_permalink( $np->ID ), esc_html( get_the_title( $np->ID ) ) );
	}
	$bloque_estilo = pm_sc_h2( $t['h_estilo'] ) . pm_sc_p( $t['estilo_i'] ) . $tabla( $t['cab'], array_map( fn( $f ) => array_map( fn( $x ) => $res( $x, $i ), $f ), $t['filas'] ) );
	$bloque_faq    = pm_sc_h2( $t['h_faq'] ) . pm_sc_faq( array_map( fn( $f ) => [ $f[0], $res( $f[1], $i ) ], $t['faq'] ) )
		. pm_sc_h2( $t['h_seguir'] ) . pm_sc_p( $t['seguir_i'] ) . pm_sc_lista( $seguir ) . pm_sc_p( $res( $t['cta'], $i ) );
	// 1) the "we have not read…" line, 2) the old CTA paragraph, 3) wording ("operadores" -> "lugares"), then insert (later offset first).
	$n_reemp = 0;
	foreach ( $t['reemplazos'] as [ $v, $nv ] ) {
		if ( false !== strpos( $c, $v ) ) {
			$c = str_replace( $v, $nv, $c );
			$n_reemp++;
		}
	}
	$cubre_ok = false !== strpos( $c, $t['cubre_viejo'] );
	$cubre_ok && ( $c = str_replace( $t['cubre_viejo'], $t['cubre_nuevo'], $c ) );
	$cta_ok = false;
	$c = preg_replace_callback( '#<!-- wp:paragraph[^>]*-->\s*<p[^>]*>(.*?)</p>\s*<!-- /wp:paragraph -->\s*#su', function ( $m ) use ( $t, &$cta_ok ) {
		if ( ! $cta_ok && preg_match( $t['cta_viejo'], wp_strip_all_tags( $m[1] ) ) ) {
			$cta_ok = true;
			return '';
		}
		return $m[0];
	}, $c );
	$pos_estilo = pm_sc_pos_encabezado( $c, $t['antes_de'] );
	$pos_cubre  = pm_sc_pos_encabezado( $c, $t['cubre'] );
	$nuevo      = substr( $c, 0, $pos_estilo ) . $bloque_estilo . substr( $c, $pos_estilo );
	$pos_cubre  = pm_sc_pos_encabezado( $nuevo, $t['cubre'] );
	$nuevo      = substr( $nuevo, 0, $pos_cubre ) . $bloque_faq . substr( $nuevo, $pos_cubre );
	! preg_match( '/\{[a-z:|-]+/', $nuevo, $mm ) || WP_CLI::error( "($i): queda un marcador sin resolver: {$mm[0]}" );
	// Fields: bajada/datos wording, one new key fact (max 6), sources.
	$datos = array_values( array_filter( array_map( fn( $f ) => (string) ( $f['dato'] ?? '' ), pm_filas( 'nota_datos', $p->ID ) ) ) );
	$datos = array_map( function ( $d ) use ( $t ) {
		foreach ( $t['reemplazos'] as [ $v, $nv ] ) {
			$d = str_replace( $v, $nv, $d );
		}
		return $d;
	}, $datos );
	if ( count( $datos ) < 6 && ! in_array( $t['dato'], $datos, true ) ) {
		$datos[] = $t['dato'];
	}
	$bajada = (string) pm_campo( 'bajada', $p->ID );
	foreach ( $t['reemplazos'] as [ $v, $nv ] ) {
		$bajada = str_replace( $v, $nv, $bajada );
	}
	$filas_f = pm_filas( 'fuentes', $p->ID );
	$urls    = array_column( $filas_f, 'url' );
	$nuevas  = 0;
	foreach ( $t['f'] as $k => $nombre ) {
		if ( ! in_array( $u[ $k ], $urls, true ) ) {
			$filas_f[] = [ 'nombre' => $nombre, 'url' => $u[ $k ], 'oficial' => 0, 'consultada' => in_array( $k, [ 'wa', 'fb' ], true ) ? '20261116' : ( 'globe' === $k ? '20261115' : '20261113' ) ];
			$nuevas++;
		}
	}
	$faqs_antes = count( pm_faqs_contenido( $p->post_content ) );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ($i) #{$p->ID} sección de estilo (" . count( $t['filas'] ) . ' filas) antes de «época», FAQ de ' . count( $t['faq'] ) . " preguntas antes de «no cubre», CTA nuevo (viejo reemplazado: " . ( $cta_ok ? 'sí' : 'NO' ) . "), $n_reemp reemplazos de palabras, línea «no cubre» " . ( $cubre_ok ? 'reescrita' : 'NO encontrada' ) . ", $nuevas fuentes nuevas, " . count( $datos ) . ' datos clave; palabras ' . str_word_count( wp_strip_all_tags( $p->post_content ) ) . ' → ' . str_word_count( wp_strip_all_tags( $nuevo ) ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$slug = $p->post_name;
	$r = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	update_field( 'nota_datos', array_map( fn( $d ) => [ 'dato' => $d ], $datos ), $p->ID );
	update_field( 'bajada', $bajada, $p->ID );
	update_field( 'fuentes', $filas_f, $p->ID );
	update_field( 'actualizado', $hoy, $p->ID );
	if ( 'es' === $i ) {
		update_post_meta( $p->ID, '_pm_es_editado', time() );
	} else {
		update_post_meta( $p->ID, '_pm_traducido', time() + 1 );
	}
	$g  = get_post( $p->ID );
	$ok = null !== pm_sc_pos_encabezado( $g->post_content, $t['ya'] ) && $g->post_name === $slug && count( pm_faqs_contenido( $g->post_content ) ) === count( $t['faq'] )
		&& false !== strpos( $g->post_content, 'especie=dorado' ) && pm_sc_pos_encabezado( $g->post_content, $t['ya'] ) < pm_sc_pos_encabezado( $g->post_content, $t['cubre'] )
		&& count( pm_filas( 'fuentes', $p->ID ) ) >= count( $urls ) + $nuevas && count( pm_filas( 'nota_datos', $p->ID ) ) === count( $datos );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": pilar del dorado ($i) #{$p->ID}, FAQ " . count( pm_faqs_contenido( $g->post_content ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $p->ID ) ) . ' fuentes, slug ' . $g->post_name . ' ' . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios notas." );
