<?php
/**
 * Step 4 of docs/plan-dorado-surubi-2026-11-16.md (16/11/2026): (1) new note "National Surubí Festival 2027 in Goya" (clave fiesta-nacional-del-surubi-2027, es/en/pt; type "datos",
 * species surubi, destination goya, cover photo = the Goya guide's) with the dates of the 50th edition (28/4 to 2/5/2027), the entry cap, the prizes, what happened with the cap, the 2026 edition,
 * how the contest works (the municipal magazine of 2025) and the Portuguese names; (2) the surubí species page (es/en/pt): a section "Names for the surubí and where it lives" (Embrapa 2015: surubim, pintado;
 * FishBase: habitat and range), a link to the note and two FAQ entries. Facts read with investigar.yml on 16/11/2026: Elonce (2 notes), Canal 26, Perfil and the official Goya magazine of 2025 (branch investigacion,
 * folder surubi-fiesta-20261116-0943), Embrapa Pesca e Aquicultura (surubi-nombres-pt-20261116-0954) and FishBase (surubi-nombres-20261116-0945). The official site of the event was not found by search.
 * NOT written, on purpose: names of people or teams, the scoring of the 2025 magazine (the PDF text is ambiguous), ticket prices, fish sizes beyond what these pages say, any "best"/ranking.
 * Idempotent: an existing note (same clave and language) or species section is not touched. Dry run first ($aplicar = false). Run with wp-script.yml (script = surubi-fiesta-2026-11-16).
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
$clave   = 'fiesta-nacional-del-surubi-2027';
$hoy     = '20261116';

$u = [
	'e1'   => 'https://www.elonce.com/sociedad/fiesta-nacional-del-surubi-habra-1-000-millones-en-premios-y-esperan-1400-equipos.htm',
	'e2'   => 'https://www.elonce.com/sociedad/fiesta-del-surubi-agotaron-los-cupos-en-dos-horas-300-equipos-quedaron-afuera-y-analizan-una-ampliacion.htm',
	'c26'  => 'https://www.canal26.com/turismo/2026/04/15/confirman-fechas-y-artistas-que-tocaran-en-la-fiesta-nacional-del-surubi-estara-la-joaqui-kapanga-y-el-polaco/',
	'perf' => 'https://www.perfil.com/noticias/nea/fiesta-nacional-del-surubi-en-goya-los-ganadores-del-mundial-de-pesca-y-quien-saco-la-pieza-mayor.phtml',
	'rev'  => 'https://www.goya.gob.ar/sites/default/files/revista_surubi_2025_para_web.pdf',
	'disp' => 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf',
	'emb'  => 'https://www.infoteca.cnptia.embrapa.br/infoteca/bitstream/doc/1039656/1/CNPASA2015dc21.pdf',
	'fb'   => 'https://www.fishbase.se/summary/Pseudoplatystoma-corruscans.html',
];
// Where each source was read: Elonce, Canal 26, Perfil and the municipal magazine with investigar.yml on 16/11/2026 (folder surubi-fiesta-20261116-0943 of the branch investigacion); Embrapa and FishBase the same day (surubi-nombres*).
$link = fn( string $k, string $txt ) => '<a href="' . esc_url( $u[ $k ] ) . '" rel="noopener">' . $txt . '</a>';

$N = [];
$N['es'] = [
	'titulo' => 'Fiesta Nacional del Surubí 2027 en Goya: fechas, cupo y premios',
	'slug'   => 'fiesta-nacional-del-surubi-2027',
	'bajada' => 'La 50.ª edición, del 28 de abril al 2 de mayo de 2027: cupo de equipos, premios, cómo es el concurso y qué pasó en 2026. Con fuentes y fechas.',
	'seo_title' => 'Fiesta Nacional del Surubí 2027: fechas, cupo y premios',
	'seo_description' => 'La 50.ª Fiesta Nacional del Surubí es del 28 de abril al 2 de mayo de 2027 en Goya: tope de 1.400 equipos, premios y cómo es el concurso. Con fuentes.',
	'datos' => [
		'La 50.ª Fiesta Nacional del Surubí («Bodas de Oro») se hace del 28 de abril al 2 de mayo de 2027 en Goya, Corrientes (Elonce, 10/8/2026).',
		'Tope de 1.400 equipos (unos 4.200 pescadores): la inscripción de 2027 completó el cupo en unas dos horas, con 1.413 registros y unos 300 equipos en lista de espera (Elonce, 23/8/2026).',
		'Premios por 1.000 millones de pesos, la cifra más alta de la historia del evento según Elonce (10/8/2026).',
		'Es un concurso de pesca embarcada con 12 zonas de pesca de 7 a 8 km cada una; en Goya cada pieza se devuelve (revista oficial de la Fiesta, 2025).',
		'En 2026 (49.ª edición, del 27/4 al 3/5) la pieza mayor fue un surubí de 119 cm (Perfil, 4/5/2026).',
		'En portugués, el surubí es «surubim» o «pintado» (Embrapa, 2015).',
	],
	'p0' => '<strong>Respuesta corta.</strong> La 50.ª Fiesta Nacional del Surubí se hace del <strong>28 de abril al 2 de mayo de 2027</strong> en Goya (Corrientes). Según {e1|Elonce (10/8/2026)}, la Comisión Municipal de Pesca (Comupe) abrió las inscripciones con un tope de <strong>1.400 equipos</strong> y premios por <strong>1.000 millones de pesos</strong>. Según {e2|Elonce (23/8/2026)}, el cupo se completó en unas dos horas, con 1.413 registros y unos 300 equipos en lista de espera, y la Comupe analizaba ampliarlo.',
	'h1' => 'Fechas, cupo y premios de la edición 2027',
	'l1' => [
		'<strong>Fechas:</strong> del 28 de abril al 2 de mayo de 2027, en Goya, Corrientes: la 50.ª edición, que Elonce llama «Edición de Oro» ({e1|Elonce, 10/8/2026}).',
		'<strong>Inscripción:</strong> por un sistema web, con pago al contado o en cuotas; cada equipo tiene que anotar como mínimo a dos pescadores, con CUIT o CUIL (para el cobro de premios) y la talla de la ropa oficial del aniversario ({e1|Elonce, 10/8/2026}).',
		'<strong>Cupo:</strong> 1.400 equipos, que según Elonce son unos 4.200 pescadores; la Comupe explica el tope por la seguridad en el río, la fiscalización, la logística y la capacidad de la cena de entrega de premios ({e2|Elonce, 23/8/2026}).',
		'<strong>Qué pasó con el cupo:</strong> se registraron 1.413 equipos en unas dos horas y unos 300 quedaron afuera, en lista de espera; la Comupe dijo que analizaba ampliar la cantidad ({e2|Elonce, 23/8/2026}).',
		'<strong>Premios:</strong> 1.000 millones de pesos repartidos entre las categorías, la cifra más alta de la historia del evento ({e1|Elonce, 10/8/2026}).',
	],
	'h2' => 'Cómo es el concurso',
	'p2' => 'Elonce lo describe como «el torneo de pesca embarcada más grande del mundo». En la revista oficial de la Fiesta de 2025 ({rev|Municipalidad de Goya}, edición 48.ª, leída el 16/11/2026), el Fiscal General del concurso dice que es un concurso embarcado «sin bajarse durante quince horas»; que se mantienen 12 zonas de pesca de entre 7 y 8 kilómetros cada una; y la misma revista dice que «en Goya y en el Mundial de Pesca no hay extracción sin devolución de cada pieza cobrada». Es coherente con la regla de Corrientes: el surubí es solo pesca y devolución ({disp|Disposición 853/22}).',
	'h3' => 'Lo que pasó en 2026',
	'p3' => 'La 49.ª edición fue del 27 de abril al 3 de mayo de 2026 y tuvo como epicentro el predio Costa Surubí, con más de 1.000 lanchas anunciadas ({c26|Canal 26, 15/4/2026}). Según {perf|Perfil (4/5/2026)}, participaron unas 1.400 embarcaciones; ganó por acumulación de puntos un equipo de Santa Fe, con 42,40 puntos y 3 piezas, y la pieza mayor fue un surubí de 119 cm, fiscalizado en la Zona 11.',
	'h4' => 'Si querés ir',
	'p4' => 'Para competir en 2027 hay que estar en un equipo inscripto: según Elonce (23/8/2026) el cupo se completó y quedó una lista de espera. Para pescar surubí en otras fechas, mirá {n:cuando-pescar-surubi|cuándo pescar el surubí}, la {g:goya|guía de Goya} y {esp:surubi|la ficha del surubí}. <strong>Contanos qué buscás y te armamos la mejor opción para vos:</strong> <a href="{viaje}">pedir opciones</a>, sin costo y con respuesta en 48 horas.',
	'h5' => 'Cómo se llama el pez',
	'p5' => 'En la Argentina se dice surubí. En Brasil, según {emb|Embrapa Pesca e Aquicultura (diciembre de 2015)}, las especies del género <em>Pseudoplatystoma</em> se conocen popularmente como «surubim» o «pintado», y el mismo nombre puede designar especies distintas según la región. Más en {esp:surubi|la ficha del surubí}.',
	'faq_h' => 'Preguntas frecuentes sobre la Fiesta Nacional del Surubí',
	'faq' => [
		[ '¿Cuándo es la Fiesta Nacional del Surubí 2027?', 'Del 28 de abril al 2 de mayo de 2027, en Goya, Corrientes: es la 50.ª edición (Elonce, 10/8/2026).' ],
		[ '¿Cuántos equipos participan?', 'El tope es de 1.400 equipos, unos 4.200 pescadores según Elonce (23/8/2026). En 2026, Perfil (4/5/2026) contó unas 1.400 embarcaciones.' ],
		[ '¿Cómo me inscribo?', 'Por un sistema web, por equipo (mínimo dos pescadores), con pago al contado o en cuotas (Elonce, 10/8/2026). El cupo de 2027 se completó en unas dos horas y hay lista de espera (Elonce, 23/8/2026): consultá a la Comupe por una ampliación.' ],
		[ '¿Te quedás con el surubí que pescás en el concurso?', 'No: la revista oficial de la Fiesta (2025) dice que en Goya no hay extracción sin devolución de cada pieza cobrada, y en Corrientes el surubí es solo pesca y devolución (Disposición 853/22).' ],
		[ '¿Qué tamaño tuvo la pieza mayor de 2026?', 'Un surubí de 119 cm, fiscalizado en la Zona 11, según Perfil (4/5/2026).' ],
	],
	'f' => [
		'e1'   => 'Elonce (10/8/2026): Fiesta Nacional del Surubí, 1.000 millones en premios y tope de 1.400 equipos',
		'e2'   => 'Elonce (23/8/2026): cupo agotado en dos horas, 300 equipos afuera',
		'c26'  => 'Canal 26 (15/4/2026): fechas y programa de la 49.ª Fiesta Nacional del Surubí',
		'perf' => 'Perfil (4/5/2026): ganadores y pieza mayor de la 49.ª Fiesta Nacional del Surubí',
		'rev'  => 'Municipalidad de Goya: revista de la Fiesta Nacional del Surubí 2025 (48.ª edición)',
		'disp' => 'Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)',
		'emb'  => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (nombres populares)',
	],
	'orig' => 'Fiesta Nacional del Surubí 2027, 16/11/2026: hechos leídos con investigar.yml en Elonce (dos notas), Canal 26, Perfil y la revista oficial de la Municipalidad de Goya de 2025 (carpeta surubi-fiesta-20261116-0943 de la rama investigacion). No se escriben nombres de personas ni equipos, ni el puntaje de la revista de 2025 (la lectura del PDF es ambigua), ni precios de entradas. El sitio oficial del evento no se encontró en la búsqueda.',
];
$N['en'] = [
	'titulo' => 'National Surubí Festival 2027 in Goya: dates, entry cap and prizes',
	'slug'   => 'national-surubi-festival-goya-2027',
	'bajada' => 'The 50th edition, from 28 April to 2 May 2027: entry cap, prizes, how the contest works and what happened in 2026. With sources and dates.',
	'seo_title' => 'National Surubí Festival 2027 in Goya: dates and entry cap',
	'seo_description' => 'The 50th National Surubí Festival runs 28 April to 2 May 2027 in Goya, Corrientes: 1,400-team cap, prizes and how the contest works. With sources.',
	'datos' => [
		'The 50th National Surubí Festival (the “Golden Anniversary”) runs from 28 April to 2 May 2027 in Goya, Corrientes (Elonce, 10/8/2026).',
		'Cap of 1,400 teams (about 4,200 anglers): entries for 2027 filled in about two hours, with 1,413 registrations and about 300 teams on a waiting list (Elonce, 23/8/2026).',
		'Prizes worth 1,000 million pesos, the highest in the event’s history according to Elonce (10/8/2026).',
		'It is a boat-based contest with 12 fishing zones of 7 to 8 km each; in Goya every fish is released (the festival’s official magazine, 2025).',
		'In 2026 (49th edition, 27/4 to 3/5) the biggest fish was a 119 cm surubí (Perfil, 4/5/2026).',
		'In Portuguese, surubí is “surubim” or “pintado” (Embrapa, 2015).',
	],
	'p0' => '<strong>Short answer.</strong> The 50th National Surubí Festival runs from <strong>28 April to 2 May 2027</strong> in Goya (Corrientes). According to {e1|Elonce (10/8/2026)}, the Municipal Fishing Commission (Comupe) opened entries with a cap of <strong>1,400 teams</strong> and prizes worth <strong>1,000 million pesos</strong>. According to {e2|Elonce (23/8/2026)}, the cap filled in about two hours, with 1,413 registrations and about 300 teams on a waiting list, and Comupe was considering raising it.',
	'h1' => 'Dates, entry cap and prizes for 2027',
	'l1' => [
		'<strong>Dates:</strong> 28 April to 2 May 2027, in Goya, Corrientes: the 50th edition, which Elonce calls the “Gold Edition” ({e1|Elonce, 10/8/2026}).',
		'<strong>Entry:</strong> through a web system, paying in full or in instalments; each team must list at least two anglers, with tax ID (for collecting prizes) and the size of the official anniversary clothing ({e1|Elonce, 10/8/2026}).',
		'<strong>Cap:</strong> 1,400 teams, which Elonce puts at about 4,200 anglers; Comupe explains the cap by river safety, officiating, logistics and the capacity of the prize dinner ({e2|Elonce, 23/8/2026}).',
		'<strong>What happened with the cap:</strong> 1,413 teams registered in about two hours and about 300 were left out, on a waiting list; Comupe said it was considering raising the number ({e2|Elonce, 23/8/2026}).',
		'<strong>Prizes:</strong> 1,000 million pesos shared among the categories, the highest figure in the event’s history ({e1|Elonce, 10/8/2026}).',
	],
	'h2' => 'How the contest works',
	'p2' => 'Elonce describes it as “the biggest boat-fishing tournament in the world”. In the festival’s official magazine for 2025 ({rev|Municipality of Goya}, 48th edition, read on 16/11/2026), the contest’s chief official says it is a boat contest where nobody gets off “for fifteen hours”; that the 12 fishing zones of 7 to 8 kilometres each are kept; and the same magazine says “in Goya and in the Fishing World Cup there is no taking of any fish without releasing it” (our translation). That fits the Corrientes rule: surubí is catch and release only ({disp|Disposición 853/22}).',
	'h3' => 'What happened in 2026',
	'p3' => 'The 49th edition ran from 27 April to 3 May 2026 with the Costa Surubí grounds as its centre, with more than 1,000 boats announced ({c26|Canal 26, 15/4/2026}). According to {perf|Perfil (4/5/2026)}, about 1,400 boats took part; a team from Santa Fe won on accumulated points, with 42.40 points and 3 fish, and the biggest fish was a 119 cm surubí, officiated in Zone 11.',
	'h4' => 'If you want to go',
	'p4' => 'To compete in 2027 you must be on a registered team: according to Elonce (23/8/2026) the cap filled and a waiting list formed. To fish surubí on other dates, see {n:cuando-pescar-surubi|when to fish surubí}, the {g:goya|Goya guide} and {esp:surubi|the surubí page}. <strong>Tell us what you are looking for and we will put together the best option for you:</strong> <a href="{viaje}">request options</a>, at no cost and with a reply within 48 hours.',
	'h5' => 'What the fish is called',
	'p5' => 'In Argentina it is surubí. In Brazil, according to {emb|Embrapa Pesca e Aquicultura (December 2015)}, the species of the genus <em>Pseudoplatystoma</em> are popularly known as “surubim” or “pintado”, and the same name can refer to different species depending on the region. More on {esp:surubi|the surubí page}.',
	'faq_h' => 'National Surubí Festival: frequently asked questions',
	'faq' => [
		[ 'When is the National Surubí Festival 2027?', 'From 28 April to 2 May 2027, in Goya, Corrientes: it is the 50th edition (Elonce, 10/8/2026).' ],
		[ 'How many teams take part?', 'The cap is 1,400 teams, about 4,200 anglers according to Elonce (23/8/2026). In 2026, Perfil (4/5/2026) counted about 1,400 boats.' ],
		[ 'How do I enter?', 'Through a web system, by team (at least two anglers), paying in full or in instalments (Elonce, 10/8/2026). The 2027 cap filled in about two hours and there is a waiting list (Elonce, 23/8/2026): ask Comupe about an increase.' ],
		[ 'Do you keep the surubí you catch in the contest?', 'No: the festival’s official magazine (2025) says that in Goya no fish is taken without releasing it, and in Corrientes surubí is catch and release only (Disposición 853/22).' ],
		[ 'How big was the biggest fish of 2026?', 'A 119 cm surubí, officiated in Zone 11, according to Perfil (4/5/2026).' ],
	],
];
$N['en']['f'] = [
	'e1'   => 'Elonce (10/8/2026): National Surubí Festival, 1,000 million pesos in prizes and a 1,400-team cap',
	'e2'   => 'Elonce (23/8/2026): entries filled in two hours, 300 teams left out',
	'c26'  => 'Canal 26 (15/4/2026): dates and programme of the 49th National Surubí Festival',
	'perf' => 'Perfil (4/5/2026): winners and biggest fish of the 49th National Surubí Festival',
	'rev'  => 'Municipality of Goya: National Surubí Festival magazine 2025 (48th edition)',
	'disp' => 'Boletín Oficial de Corrientes: Disposición 853/22 (sizes and bag limits)',
	'emb'  => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (popular names)',
];
$N['pt'] = [
	'titulo' => 'Festa Nacional do Surubim 2027 em Goya: datas, vagas e prêmios',
	'slug'   => 'festa-nacional-do-surubim-goya-2027',
	'bajada' => 'A 50ª edição, de 28 de abril a 2 de maio de 2027: limite de equipes, prêmios, como é o concurso e o que aconteceu em 2026. Com fontes e datas.',
	'seo_title' => 'Festa Nacional do Surubim 2027 em Goya: datas e vagas',
	'seo_description' => 'A 50ª Festa Nacional do Surubi (surubim) será de 28 de abril a 2 de maio de 2027 em Goya: limite de 1.400 equipes, prêmios e como é o concurso.',
	'datos' => [
		'A 50ª Festa Nacional do Surubi («Bodas de Ouro») será de 28 de abril a 2 de maio de 2027 em Goya, Corrientes (Elonce, 10/8/2026).',
		'Limite de 1.400 equipes (cerca de 4.200 pescadores): a inscrição de 2027 completou as vagas em cerca de duas horas, com 1.413 registros e cerca de 300 equipes em lista de espera (Elonce, 23/8/2026).',
		'Prêmios de 1.000 milhões de pesos, o maior valor da história do evento segundo a Elonce (10/8/2026).',
		'É um concurso de pesca embarcada com 12 zonas de pesca de 7 a 8 km cada uma; em Goya cada peixe é devolvido (revista oficial da Festa, 2025).',
		'Em 2026 (49ª edição, de 27/4 a 3/5) o maior peixe foi um surubi de 119 cm (Perfil, 4/5/2026).',
		'Em português, o surubi argentino é «surubim» ou «pintado» (Embrapa, 2015).',
	],
	'p0' => '<strong>Resposta curta.</strong> A 50ª Festa Nacional do Surubi será de <strong>28 de abril a 2 de maio de 2027</strong> em Goya (Corrientes). Segundo a {e1|Elonce (10/8/2026)}, a Comissão Municipal de Pesca (Comupe) abriu as inscrições com um limite de <strong>1.400 equipes</strong> e prêmios de <strong>1.000 milhões de pesos</strong>. Segundo a {e2|Elonce (23/8/2026)}, as vagas se esgotaram em cerca de duas horas, com 1.413 registros e cerca de 300 equipes em lista de espera, e a Comupe analisava ampliá-las.',
	'h1' => 'Datas, vagas e prêmios da edição 2027',
	'l1' => [
		'<strong>Datas:</strong> de 28 de abril a 2 de maio de 2027, em Goya, Corrientes: a 50ª edição, que a Elonce chama de «Edição de Ouro» ({e1|Elonce, 10/8/2026}).',
		'<strong>Inscrição:</strong> por um sistema web, com pagamento à vista ou parcelado; cada equipe precisa registrar no mínimo dois pescadores, com CUIT ou CUIL (para receber prêmios) e o tamanho da roupa oficial do aniversário ({e1|Elonce, 10/8/2026}).',
		'<strong>Limite:</strong> 1.400 equipes, que segundo a Elonce são cerca de 4.200 pescadores; a Comupe explica o limite pela segurança no rio, a fiscalização, a logística e a capacidade do jantar de entrega de prêmios ({e2|Elonce, 23/8/2026}).',
		'<strong>O que aconteceu com as vagas:</strong> 1.413 equipes se registraram em cerca de duas horas e cerca de 300 ficaram de fora, em lista de espera; a Comupe disse que analisava ampliar a quantidade ({e2|Elonce, 23/8/2026}).',
		'<strong>Prêmios:</strong> 1.000 milhões de pesos distribuídos entre as categorias, o maior valor da história do evento ({e1|Elonce, 10/8/2026}).',
	],
	'h2' => 'Como é o concurso',
	'p2' => 'A Elonce o descreve como «o maior torneio de pesca embarcada do mundo». Na revista oficial da Festa de 2025 ({rev|Prefeitura de Goya}, 48ª edição, lida em 16/11/2026), o Fiscal Geral do concurso diz que é um concurso embarcado «sem descer durante quinze horas»; que se mantêm 12 zonas de pesca de entre 7 e 8 quilômetros cada uma; e a mesma revista diz que «em Goya e no Mundial de Pesca não há extração sem devolução de cada peça capturada» (tradução nossa). É coerente com a regra de Corrientes: o surubim é só pesca e soltura ({disp|Disposición 853/22}).',
	'h3' => 'O que aconteceu em 2026',
	'p3' => 'A 49ª edição foi de 27 de abril a 3 de maio de 2026, com o parque Costa Surubí como epicentro e mais de 1.000 lanchas anunciadas ({c26|Canal 26, 15/4/2026}). Segundo a {perf|Perfil (4/5/2026)}, participaram cerca de 1.400 embarcações; venceu por acúmulo de pontos uma equipe de Santa Fe, com 42,40 pontos e 3 peças, e o maior peixe foi um surubi de 119 cm, fiscalizado na Zona 11.',
	'h4' => 'Se você quer ir',
	'p4' => 'Para competir em 2027 é preciso estar em uma equipe inscrita: segundo a Elonce (23/8/2026) as vagas se esgotaram e ficou uma lista de espera. Para pescar surubim em outras datas, veja {n:cuando-pescar-surubi|quando pescar o surubim}, o {g:goya|guia de Goya} e {esp:surubi|a ficha do surubim}. <strong>Conte o que você procura e montamos a melhor opção para você:</strong> <a href="{viaje}">pedir opções</a>, sem custo e com resposta em 48 horas.',
	'h5' => 'Como se chama o peixe',
	'p5' => 'Na Argentina se diz surubí. No Brasil, segundo a {emb|Embrapa Pesca e Aquicultura (dezembro de 2015)}, as espécies do gênero <em>Pseudoplatystoma</em> são conhecidas popularmente como «surubim» ou «pintado», e o mesmo nome pode designar espécies diferentes conforme a região. Mais em {esp:surubi|a ficha do surubim}.',
	'faq_h' => 'Perguntas frequentes sobre a Festa Nacional do Surubi',
	'faq' => [
		[ 'Quando é a Festa Nacional do Surubi 2027?', 'De 28 de abril a 2 de maio de 2027, em Goya, Corrientes: é a 50ª edição (Elonce, 10/8/2026).' ],
		[ 'Quantas equipes participam?', 'O limite é de 1.400 equipes, cerca de 4.200 pescadores segundo a Elonce (23/8/2026). Em 2026, a Perfil (4/5/2026) contou cerca de 1.400 embarcações.' ],
		[ 'Como me inscrevo?', 'Por um sistema web, por equipe (no mínimo dois pescadores), com pagamento à vista ou parcelado (Elonce, 10/8/2026). As vagas de 2027 se esgotaram em cerca de duas horas e há lista de espera (Elonce, 23/8/2026): pergunte à Comupe sobre uma ampliação.' ],
		[ 'Fica-se com o surubim pescado no concurso?', 'Não: a revista oficial da Festa (2025) diz que em Goya não há extração sem devolução de cada peça capturada, e em Corrientes o surubim é só pesca e soltura (Disposición 853/22).' ],
		[ 'Qual foi o tamanho do maior peixe de 2026?', 'Um surubi de 119 cm, fiscalizado na Zona 11, segundo a Perfil (4/5/2026).' ],
	],
];
$N['pt']['f'] = [
	'e1'   => 'Elonce (10/8/2026): Festa Nacional do Surubi, 1.000 milhões em prêmios e limite de 1.400 equipes',
	'e2'   => 'Elonce (23/8/2026): vagas esgotadas em duas horas, 300 equipes de fora',
	'c26'  => 'Canal 26 (15/4/2026): datas e programação da 49ª Festa Nacional do Surubi',
	'perf' => 'Perfil (4/5/2026): vencedores e maior peixe da 49ª Festa Nacional do Surubi',
	'rev'  => 'Prefeitura de Goya: revista da Festa Nacional do Surubi 2025 (48ª edição)',
	'disp' => 'Boletín Oficial de Corrientes: Disposición 853/22 (tamanhos e cotas)',
	'emb'  => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (nomes populares)',
];

// ---- Species page (surubí): names, where it lives, the festival, two FAQ ----
$E = [];
$E['es'] = [
	'h' => 'Nombres del surubí y dónde vive', 'ya' => '/nombres del surubí/iu', 'antes' => '/preguntas frecuentes/iu',
	'p' => [
		'En la Argentina se dice surubí. FishBase llama a <em>Pseudoplatystoma corruscans</em> «spotted sorubim» (surubí manchado) y la CARU habla del «surubí pintado». En Brasil, según {emb|Embrapa Pesca e Aquicultura (diciembre de 2015)}, las especies de este género se conocen popularmente como «surubim» o «pintado», con otros nombres como «sorubim», «surubim pintado», «piracajara», «jarapoca», «moleque» o «brutelo»; Embrapa aclara que el mismo nombre popular puede designar especies distintas según la región. En portugués, entonces, se busca «surubim» o «pintado».',
		'Dónde vive: según {fb|FishBase (leída el 16/11/2026)}, es un pez de agua dulce, de fondo y que migra por el río, de las cuencas del São Francisco y del Paraná, en Sudamérica, y se alimenta sobre todo de peces.',
		'Cada año, Goya hace la Fiesta Nacional del Surubí: la 50.ª edición es del 28 de abril al 2 de mayo de 2027. Fechas, cupo y premios: {n:fiesta-nacional-del-surubi-2027|Fiesta Nacional del Surubí 2027 en Goya}.',
	],
	'faq' => [
		[ '¿Cómo se dice surubí en portugués?', '«Surubim» o «pintado», según Embrapa Pesca e Aquicultura (2015); el mismo nombre puede designar especies distintas según la región de Brasil.' ],
		[ '¿Cuándo es la Fiesta Nacional del Surubí 2027?', 'Del 28 de abril al 2 de mayo de 2027, en Goya, Corrientes, según Elonce (10/8/2026). Detalle: {n:fiesta-nacional-del-surubi-2027|Fiesta Nacional del Surubí 2027 en Goya}.' ],
	],
	'f' => [ 'emb' => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (nombres populares)', 'e1' => 'Elonce (10/8/2026): Fiesta Nacional del Surubí, 1.000 millones en premios y tope de 1.400 equipos' ],
];
$E['en'] = [
	'h' => 'Names for the surubí and where it lives', 'ya' => '/names for the surub/iu', 'antes' => '/frequently asked|preguntas frecuentes/iu',
	'p' => [
		'In Argentina it is called surubí. FishBase calls <em>Pseudoplatystoma corruscans</em> the “spotted sorubim” and CARU speaks of the “surubí pintado”. In Brazil, according to {emb|Embrapa Pesca e Aquicultura (December 2015)}, the species of this genus are popularly known as “surubim” or “pintado”, with other names such as “sorubim”, “surubim pintado”, “piracajara”, “jarapoca”, “moleque” or “brutelo”; Embrapa points out that the same popular name can refer to different species depending on the region. In Portuguese, then, people search for “surubim” or “pintado”.',
		'Where it lives: according to {fb|FishBase (read on 16/11/2026)}, it is a freshwater fish that lives near the bottom and migrates along the river, from the São Francisco and Paraná basins in South America, and it feeds mainly on fish.',
		'Every year Goya holds the National Surubí Festival: the 50th edition runs from 28 April to 2 May 2027. Dates, entry cap and prizes: {n:fiesta-nacional-del-surubi-2027|National Surubí Festival 2027 in Goya}.',
	],
	'faq' => [
		[ 'What is the surubí called in Portuguese?', '“Surubim” or “pintado”, according to Embrapa Pesca e Aquicultura (2015); the same name can refer to different species depending on the region of Brazil.' ],
		[ 'When is the National Surubí Festival 2027?', 'From 28 April to 2 May 2027, in Goya, Corrientes, according to Elonce (10/8/2026). Details: {n:fiesta-nacional-del-surubi-2027|National Surubí Festival 2027 in Goya}.' ],
	],
	'f' => [ 'emb' => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (popular names)', 'e1' => 'Elonce (10/8/2026): National Surubí Festival, 1,000 million pesos in prizes and a 1,400-team cap' ],
];
$E['pt'] = [
	'h' => 'Nomes do surubim e onde ele vive', 'ya' => '/nomes do surubim/iu', 'antes' => '/perguntas frequentes|preguntas frecuentes/iu',
	'p' => [
		'Na Argentina se diz surubí. A FishBase chama <em>Pseudoplatystoma corruscans</em> de «spotted sorubim» (surubim manchado) e a CARU fala do «surubí pintado». No Brasil, segundo a {emb|Embrapa Pesca e Aquicultura (dezembro de 2015)}, as espécies deste gênero são conhecidas popularmente como «surubim» ou «pintado», com outros nomes como «sorubim», «surubim pintado», «piracajara», «jarapoca», «moleque» ou «brutelo»; a Embrapa destaca que o mesmo nome popular pode designar espécies diferentes conforme a região.',
		'Onde vive: segundo a {fb|FishBase (lida em 16/11/2026)}, é um peixe de água doce, de fundo e que migra pelo rio, das bacias do São Francisco e do Paraná, na América do Sul, e se alimenta sobretudo de peixes.',
		'Todo ano, Goya faz a Festa Nacional do Surubi: a 50ª edição será de 28 de abril a 2 de maio de 2027. Datas, vagas e prêmios: {n:fiesta-nacional-del-surubi-2027|Festa Nacional do Surubim 2027 em Goya}.',
	],
	'faq' => [
		[ 'Como se diz surubí em português?', '«Surubim» ou «pintado», segundo a Embrapa Pesca e Aquicultura (2015); o mesmo nome pode designar espécies diferentes conforme a região do Brasil.' ],
		[ 'Quando é a Festa Nacional do Surubi 2027?', 'De 28 de abril a 2 de maio de 2027, em Goya, Corrientes, segundo a Elonce (10/8/2026). Detalhe: {n:fiesta-nacional-del-surubi-2027|Festa Nacional do Surubim 2027 em Goya}.' ],
	],
	'f' => [ 'emb' => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (nomes populares)', 'e1' => 'Elonce (10/8/2026): Festa Nacional do Surubi, 1.000 milhões em prêmios e limite de 1.400 equipes' ],
];

/** {key|text} -> external link; {g:|n:|esp:clave|text} -> internal; {viaje}. */
$res = function ( string $html, string $i, array $extra_u = [] ) use ( $u ): string {
	$html = preg_replace_callback( '/\{(?:(g|n|esp):)?([a-z0-9-]+)\|([^}]+)\}/u', function ( $m ) use ( $i, $u ) {
		[ , $tipo, $c, $txt ] = $m;
		if ( '' === $tipo ) {
			isset( $u[ $c ] ) || WP_CLI::error( "URL externa desconocida: $c" );
			return '<a href="' . esc_url( $u[ $c ] ) . '" rel="noopener">' . $txt . '</a>';
		}
		$cpt = [ 'g' => 'pm_destino', 'n' => 'pm_nota', 'esp' => 'pm_especie' ][ $tipo ];
		$p   = pm_buscar( $cpt, $c, $i ) ?? pm_buscar( $cpt, $c, 'es' );
		if ( ! $p ) {
			// The new note itself does not exist yet in a dry run: not an error.
			return $txt;
		}
		return '<a href="' . esc_url( get_permalink( $p->ID ) ) . '">' . $txt . '</a>';
	}, $html );
	return str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) . '?destino=goya&especie=surubi' ), $html );
};

// ---------------- Part 1: the note ----------------
$cambios = 0;
$goya    = pm_buscar( 'pm_destino', 'goya', 'es' );
$att     = $goya ? (int) get_post_thumbnail_id( $goya->ID ) : 0;
( $att && wp_attachment_is_image( $att ) ) || WP_CLI::warning( 'la guía de Goya no tiene foto de portada: la nota nueva no tendrá foto.' );
$existe = fn( string $i ) => array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i );
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n = $N[ $i ];
	if ( $ya = $existe( $i ) ) {
		WP_CLI::log( "$clave ($i) #" . array_values( $ya )[0]->ID . ' ya existe: no toco nada.' );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	$cuerpo = pm_sc_p( $res( $n['p0'], $i ) )
		. pm_sc_h2( $n['h1'] ) . pm_sc_lista( array_map( fn( $x ) => $res( $x, $i ), $n['l1'] ) )
		. pm_sc_h2( $n['h2'] ) . pm_sc_p( $res( $n['p2'], $i ) )
		. pm_sc_h2( $n['h3'] ) . pm_sc_p( $res( $n['p3'], $i ) )
		. pm_sc_h2( $n['h4'] ) . pm_sc_p( $res( $n['p4'], $i ) )
		. pm_sc_h2( $n['h5'] ) . pm_sc_p( $res( $n['p5'], $i ) )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] );
	! preg_match( '/\{[a-z0-9:|-]+/', $cuerpo, $m ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	$fuentes = [];
	foreach ( $n['f'] as $k => $nombre ) {
		$fuentes[] = [ 'nombre' => $nombre, 'url' => $u[ $k ], 'oficial' => in_array( $k, [ 'rev', 'disp' ], true ) ? 1 : 0, 'consultada' => $hoy ];
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . ' fuentes, ' . count( $n['faq'] ) . " FAQ; title $tl / description $dl; portada #$att" );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ), 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'datos', 'nota_fecha' => $hoy, 'nota_especie' => 'surubi', 'nota_destino' => 'goya', 'nota_origen' => $N['es']['orig'] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		$att && set_post_thumbnail( $id, $att );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_post( $id )->post_name === $n['slug'] && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] )
		&& count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && false !== strpos( $cont, '119' ) && false !== strpos( $cont, 'especie=surubi' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
if ( $aplicar ) {
	$nota_es = pm_buscar( 'pm_nota', $clave, 'es' );
	$e = $nota_es ? pm_nota( $nota_es->ID ) : null;
	WP_CLI::log( ( $e && 'surubi' === $e['especie'] && 'goya' === $e['destino'] && $e['fecha'] && ( ! $att || $e['foto'] === $att ) ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee especie ' . ( $e['especie'] ?? '?' ) . ', destino ' . ( $e['destino'] ?? '?' ) . ', fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
	flush_rewrite_rules( false );
}

// ---------------- Part 2: the species page ----------------
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$s = $E[ $i ];
	$p = pm_buscar( 'pm_especie', 'surubi', $i );
	if ( ! $p ) {
		WP_CLI::warning( "ficha surubí ($i): no existe." );
		continue;
	}
	$c = $p->post_content;
	if ( null !== pm_sc_pos_encabezado( $c, $s['ya'] ) ) {
		WP_CLI::log( "ficha surubí ($i) #{$p->ID}: ya tiene la sección «{$s['h']}»; no toco nada." );
		continue;
	}
	$pos = pm_sc_pos_encabezado( $c, $s['antes'] );
	if ( null === $pos ) {
		WP_CLI::warning( "ficha surubí ($i) #{$p->ID}: no encuentro el encabezado de preguntas frecuentes; no toco nada." );
		continue;
	}
	$bloque = pm_sc_h2( $s['h'] );
	foreach ( $s['p'] as $par ) {
		$bloque .= pm_sc_p( $res( $par, $i ) );
	}
	$nuevo = substr( $c, 0, $pos ) . $bloque . substr( $c, $pos );
	// Append the two questions to the existing pm-faq group (before the first </div> that follows its opening tag).
	$ap = strpos( $nuevo, '<div class="wp-block-group pm-faq">' );
	$cierre = false === $ap ? false : strpos( $nuevo, '</div>', $ap );
	$faq_html = '';
	foreach ( $s['faq'] as [ $q, $a ] ) {
		$faq_html .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>" . $res( $a, $i ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	if ( false !== $cierre ) {
		$nuevo = substr( $nuevo, 0, $cierre ) . "\n\n" . rtrim( $faq_html ) . substr( $nuevo, $cierre );
	}
	! preg_match( '/\{[a-z0-9:|-]+/', $nuevo, $mm ) || WP_CLI::error( "ficha surubí ($i): marcador sin resolver {$mm[0]}" );
	$filas = pm_filas( 'fuentes', $p->ID );
	$urls  = array_column( $filas, 'url' );
	$nuevas = 0;
	foreach ( $s['f'] as $k => $nombre ) {
		if ( ! in_array( $u[ $k ], $urls, true ) ) {
			$filas[] = [ 'nombre' => $nombre, 'url' => $u[ $k ], 'oficial' => 0, 'consultada' => $hoy ];
			$nuevas++;
		}
	}
	$faq_antes = count( pm_faqs_contenido( $c ) );
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": ficha surubí ($i) #{$p->ID} sección «{$s['h']}», FAQ $faq_antes → " . ( false === $cierre ? 'SIN GRUPO FAQ (no se agregan)' : $faq_antes + count( $s['faq'] ) ) . ", $nuevas fuentes nuevas; palabras " . str_word_count( wp_strip_all_tags( $c ) ) . ' → ' . str_word_count( wp_strip_all_tags( $nuevo ) ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$slug = $p->post_name;
	$r = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	update_field( 'fuentes', $filas, $p->ID );
	update_field( 'actualizado', $hoy, $p->ID );
	$g  = get_post( $p->ID );
	$ok = null !== pm_sc_pos_encabezado( $g->post_content, $s['ya'] ) && $g->post_name === $slug && count( pm_filas( 'fuentes', $p->ID ) ) === count( $urls ) + $nuevas
		&& ( false === $cierre || count( pm_faqs_contenido( $g->post_content ) ) === $faq_antes + count( $s['faq'] ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": ficha surubí ($i) #{$p->ID}, FAQ " . count( pm_faqs_contenido( $g->post_content ) ) . ', ' . count( pm_filas( 'fuentes', $p->ID ) ) . ' fuentes ' . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "ficha surubí ($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
