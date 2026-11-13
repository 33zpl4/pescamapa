<?php
/**
 * Search Console follow-up, 13/11/2026, fix 2: species pages (trout, golden dorado, surubí; es/en/pt).
 *  - Trout: section "Types of trout in Patagonia" (links to the Río Grande, Río Gallegos, Bariloche, San Martín/Junín and Esquel guides;
 *    brook trout only what the page already sources: Macchi and Vigliano 2014) + FAQ "what kinds of trout" / "where brook trout".
 *  - Dorado: size H2 reworded, link to the trip-cost note under the key facts, FAQ (record, size, catch and release), each answer taken from
 *    the page's own text and sources (IGFA via Diario Uno 24/10/2026; Ringuelet et al. 1967; Disposición 853/22).
 *  - Surubí: FAQ from the Disposición 853/22 sentence on the page and from the season note (cuando-pescar-surubi), links to the guides and packages.
 * The request button is a template change (vistas/especie.php). Idempotent. Simulacro first. Run with wp-script.yml
 * (script = seo-consultas-especies-2026-11-13).
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
$cambios = 0;

/** Permalink of a guide in this language (else Spanish); '' when it does not exist. First clave that exists wins. */
function pm_sc_guia_url( array $claves, string $i ): string {
	foreach ( $claves as $k ) {
		$p = pm_buscar( 'pm_destino', $k, $i ) ?? pm_buscar( 'pm_destino', $k, 'es' );
		if ( $p ) {
			return get_permalink( $p->ID );
		}
	}
	return '';
}
function pm_sc_guia_a( array $claves, string $i ): string {
	foreach ( $claves as $k ) {
		$p = pm_buscar( 'pm_destino', $k, $i ) ?? pm_buscar( 'pm_destino', $k, 'es' );
		if ( $p ) {
			return pm_sc_a( get_permalink( $p->ID ), esc_html( get_the_title( $p->ID ) ) );
		}
	}
	return '';
}

// --- Trout: "Types of trout in Patagonia" + FAQ -----------------------------------------------------------------
$trucha = [
	'es' => [
		'h2' => 'Tipos de truchas en la Patagonia', 'faq_h' => 'Preguntas frecuentes',
		'intro' => 'En la Patagonia se pescan tres truchas: arcoíris, marrón y de arroyo (fontinalis), descritas más abajo. Cuál encontrás depende del agua:',
		'items' => [
			'<strong>Trucha marrón de mar:</strong> el Río Grande, en Tierra del Fuego (%1$s), y el Río Gallegos, en Santa Cruz (%2$s).',
			'<strong>Arcoíris y marrón</strong>, las más buscadas: los ríos de cordillera, los arroyos de vertiente y los lagos de las guías de %3$s, %4$s y %5$s.',
			'<strong>Trucha de arroyo:</strong> hoy solo se la registra en las nacientes de los ríos Negro, Limay y Chubut (Macchi y Vigliano, 2014); preguntale a tu guía en qué agua conviene buscarla.',
		],
		'cierre' => 'Cada agua tiene su propio reglamento: con guía, la salida y la licencia se arman para el agua que elijas.',
		'faq' => [
			[ '¿Qué tipos de truchas hay en la Patagonia?', 'Tres: arcoíris (Oncorhynchus mykiss), marrón (Salmo trutta) y de arroyo o fontinalis (Salvelinus fontinalis). Ninguna es nativa: la primera introducción oficial de salmónidos en la Patagonia fue en 1904 (Macchi y Vigliano, Ecología Austral, 2014). La marrón tiene en el Río Grande, en Tierra del Fuego, una forma que migra al mar y vuelve con tamaños enormes.' ],
			[ '¿Dónde se pesca la trucha de arroyo en la Patagonia?', 'Hoy se la registra en el 42,5 % de las cuencas donde se la introdujo y, en el norte de la Patagonia, solo en las nacientes de los ríos Negro, Limay y Chubut (Macchi y Vigliano, 2014). Preguntale a tu guía en qué agua conviene buscarla; cada agua tiene sus reglas.' ],
		],
	],
	'en' => [
		'h2' => 'Types of trout in Patagonia', 'faq_h' => 'Frequently asked questions',
		'intro' => 'Three trout are fished in Patagonia: rainbow, brown and brook trout (fontinalis), described below. Which one you meet depends on the water:',
		'items' => [
			'<strong>Sea-run brown trout:</strong> the Río Grande, in Tierra del Fuego (%1$s), and the Río Gallegos, in Santa Cruz (%2$s).',
			'<strong>Rainbow and brown</strong>, the most sought after: the mountain rivers, spring creeks and lakes covered by our %3$s, %4$s and %5$s guides.',
			'<strong>Brook trout:</strong> today it is recorded only in the headwaters of the Negro, Limay and Chubut rivers (Macchi and Vigliano, 2014); ask your guide which water is worth trying.',
		],
		'cierre' => 'Each water has its own rules: with a guide, the trip and the license are set up for the water you choose.',
		'faq' => [
			[ 'What kinds of trout are there in Patagonia?', 'Three: rainbow (Oncorhynchus mykiss), brown (Salmo trutta) and brook trout or fontinalis (Salvelinus fontinalis). None is native: the first official introduction of salmonids in Patagonia was in 1904 (Macchi and Vigliano, Ecología Austral, 2014). Brown trout has, on the Río Grande in Tierra del Fuego, a form that migrates to the sea and comes back at huge sizes.' ],
			[ 'Where can you catch brook trout in Patagonia?', 'Today it is recorded in 42.5% of the basins where it was introduced and, in northern Patagonia, only in the headwaters of the Negro, Limay and Chubut rivers (Macchi and Vigliano, 2014). Ask your guide which water is worth trying; each water has its own rules.' ],
		],
	],
	'pt' => [
		'h2' => 'Tipos de truta na Patagônia', 'faq_h' => 'Perguntas frequentes',
		'intro' => 'Na Patagônia pescam-se três trutas: arco-íris, marrom e de riacho (fontinalis), descritas mais abaixo. Qual você encontra depende da água:',
		'items' => [
			'<strong>Truta marrom do mar:</strong> o Río Grande, na Terra do Fogo (%1$s), e o Río Gallegos, em Santa Cruz (%2$s).',
			'<strong>Arco-íris e marrom</strong>, as mais procuradas: os rios de cordilheira, os riachos de nascente e os lagos dos guias de %3$s, %4$s e %5$s.',
			'<strong>Truta de riacho:</strong> hoje só está registrada nas nascentes dos rios Negro, Limay e Chubut (Macchi e Vigliano, 2014); pergunte ao seu guia em que água vale a pena procurá-la.',
		],
		'cierre' => 'Cada água tem o seu regulamento: com guia, a saída e a licença são montadas para a água que você escolher.',
		'faq' => [
			[ 'Que tipos de truta existem na Patagônia?', 'Três: arco-íris (Oncorhynchus mykiss), marrom (Salmo trutta) e de riacho ou fontinalis (Salvelinus fontinalis). Nenhuma é nativa: a primeira introdução oficial de salmonídeos na Patagônia foi em 1904 (Macchi e Vigliano, Ecología Austral, 2014). A marrom tem, no Río Grande, na Terra do Fogo, uma forma que migra para o mar e volta com tamanhos enormes.' ],
			[ 'Onde se pesca a truta de riacho na Patagônia?', 'Hoje ela está registrada em 42,5 % das bacias onde foi introduzida e, no norte da Patagônia, só nas nascentes dos rios Negro, Limay e Chubut (Macchi e Vigliano, 2014). Pergunte ao seu guia em que água vale a pena procurá-la; cada água tem suas regras.' ],
		],
	],
];
foreach ( $trucha as $i => $t ) {
	$p = pm_buscar( 'pm_especie', 'trucha', $i );
	if ( ! $p ) {
		WP_CLI::log( "trucha ($i): no existe, se salta." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, $t['h2'] ) ) {
		WP_CLI::log( "trucha ($i) #{$p->ID}: ya tiene «{$t['h2']}», no toco nada." );
		continue;
	}
	$enl = [
		pm_sc_guia_a( [ 'rio-grande' ], $i ), pm_sc_guia_a( [ 'rio-gallegos' ], $i ), pm_sc_guia_a( [ 'bariloche' ], $i ),
		pm_sc_guia_a( [ 'san-martin-junin', 'junin-de-los-andes' ], $i ), pm_sc_guia_a( [ 'esquel-trevelin' ], $i ),
	];
	if ( in_array( '', $enl, true ) ) {
		WP_CLI::warning( "trucha ($i): falta alguna guía enlazada (" . implode( '|', array_map( 'strlen', $enl ) ) . '); se salta.' );
		continue;
	}
	$items = array_map( fn( $s ) => vsprintf( $s, $enl ), $t['items'] );
	$bloque = pm_sc_h2( $t['h2'] ) . pm_sc_p( $t['intro'] ) . pm_sc_lista( $items ) . pm_sc_p( $t['cierre'] );
	$pos = pm_sc_pos_encabezado( $c, '/Río Grande/u' );
	$donde = 'antes de la sección del Río Grande';
	if ( null === $pos ) {
		WP_CLI::warning( "trucha ($i): no encontré el encabezado del Río Grande; se salta." );
		continue;
	}
	$nuevo = pm_sc_insertar( $c, $pos, $bloque );
	$nuevo .= "\n\n" . pm_sc_h2( $t['faq_h'] ) . pm_sc_faq( $t['faq'] );
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": trucha ($i) #{$p->ID}: H2 «{$t['h2']}» $donde + FAQ: " . implode( ' | ', array_column( $t['faq'], 0 ) ) );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, $nuevo, [ $t['h2'], 'pm-faq', $t['faq'][0][0] ], "trucha ($i)" );
		WP_CLI::log( "VERIFIED: trucha ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	}
}

// --- Dorado: size H2, trip-cost link, FAQ ------------------------------------------------------------------------
$dorado = [
	'es' => [
		'viejo' => 'Cuánto pesa un dorado', 'nuevo' => 'Cuánto pesa y mide un dorado: tamaño promedio y máximo',
		'ir' => 'Si estás armando el viaje: %s.', 'faq_h' => 'Preguntas frecuentes',
		'faq' => [
			[ '¿Cuál es el récord mundial del dorado?', 'El récord mundial del dorado en la categoría todo equipo (all-tackle) de la IGFA es de 25,28 kg (55 libras y 11 onzas). Lo pescó Andre De Button en 2006 en el río Uruguay, en la frontera entre Argentina y Uruguay (dato publicado por Diario Uno, consultado el 24/10/2026).' ],
			[ '¿Cuánto mide y pesa un dorado?', 'El dorado adulto que se pesca con regularidad en el Litoral mide entre 50 y 75 cm y pesa entre 2,5 y 6 kg; los guías cuentan que lo habitual son peces de 3 a 5 kg, y se habla de trofeo a partir de los 8 a 10 kg. Son rangos aproximados de la pesca deportiva y de la bibliografía de peces del Plata (Ringuelet, Aramburu y Alonso de Aramburu, 1967; INALI-CONICET), no un valor medido en un único estudio. El récord mundial es de 25,28 kg.' ],
			[ '¿El dorado es de pesca y devolución?', 'En Corrientes sí: es solo pesca y devolución, no se retiene (Disposición 853/22 de la Dirección de Recursos Naturales, Boletín Oficial de Corrientes, 26 de diciembre de 2022, vigente hasta que otro acto la modifique). Las reglas de otras provincias y las vedas por tramo cambian cada año: mirá la guía de cada destino o preguntale a tu guía.' ],
		],
	],
	'en' => [
		'viejo' => 'How big is a golden dorado', 'nuevo' => 'How big is a golden dorado: average and maximum size',
		'ir' => 'Planning the trip? %s.', 'faq_h' => 'Frequently asked questions',
		'faq' => [
			[ 'What is the golden dorado world record?', 'The IGFA all-tackle world record for the golden dorado is 25.28 kg (55 lb 11 oz), caught by Andre De Button in 2006 in the Uruguay River, on the border between Argentina and Uruguay (figure reported by Diario Uno, consulted 24 October 2026).' ],
			[ 'How big does a golden dorado get?', 'The adult golden dorado regularly caught in the Litoral measures 50 to 75 cm and weighs 2.5 to 6 kg (5.5 to 13 lb); guides say 3 to 5 kg fish are the norm, and trophy is used from 8 to 10 kg up. These are approximate ranges from sport fishing and from the literature on Río de la Plata fishes (Ringuelet, Aramburu and Alonso de Aramburu, 1967; INALI-CONICET), not a value measured in a single study. The world record is 25.28 kg.' ],
			[ 'Is golden dorado catch and release?', 'In Corrientes, yes: it is catch and release only and cannot be kept (Disposición 853/22 of the Dirección de Recursos Naturales, Boletín Oficial de Corrientes, 26 December 2022, in force until another act changes it). Rules in other provinces and closed seasons by river stretch change every year: check the guide of each destination or ask your guide.' ],
		],
	],
	'pt' => [
		'viejo' => 'Quanto pesa um dourado', 'nuevo' => 'Quanto pesa e mede um dourado: tamanho médio e máximo',
		'ir' => 'Se você está montando a viagem: %s.', 'faq_h' => 'Perguntas frequentes',
		'faq' => [
			[ 'Qual é o recorde mundial do dourado?', 'O recorde mundial do dourado na categoria todo equipamento (all-tackle) da IGFA é de 25,28 kg (55 libras e 11 onças). Foi pescado por Andre De Button em 2006 no rio Uruguai, na fronteira entre a Argentina e o Uruguai (dado publicado pelo Diario Uno, consultado em 24/10/2026).' ],
			[ 'Quanto mede e pesa um dourado?', 'O dourado adulto pescado com regularidade no Litoral mede entre 50 e 75 cm e pesa entre 2,5 e 6 kg; os guias contam que o habitual são peixes de 3 a 5 kg, e fala-se em troféu a partir de 8 a 10 kg. São faixas aproximadas da pesca esportiva e da bibliografia de peixes do Prata (Ringuelet, Aramburu e Alonso de Aramburu, 1967; INALI-CONICET), não um valor medido em um único estudo. O recorde mundial é de 25,28 kg.' ],
			[ 'O dourado é de pesque e solte?', 'Em Corrientes, sim: é só pesque e solte, não se retém (Disposición 853/22 da Direção de Recursos Naturais, Boletim Oficial de Corrientes, 26 de dezembro de 2022, vigente até que outro ato a modifique). As regras de outras províncias e os defesos por trecho mudam a cada ano: veja o guia de cada destino ou pergunte ao seu guia.' ],
		],
	],
];
foreach ( $dorado as $i => $t ) {
	$p = pm_buscar( 'pm_especie', 'dorado', $i );
	if ( ! $p ) {
		WP_CLI::log( "dorado ($i): no existe, se salta." );
		continue;
	}
	$c     = $p->post_content;
	$nuevo = $c;
	$tareas = [];
	if ( false === strpos( $c, $t['nuevo'] ) ) {
		$r = pm_sc_renombrar( $nuevo, $t['viejo'], $t['nuevo'] );
		if ( null === $r ) {
			WP_CLI::warning( "dorado ($i): no encontré el H2 «{$t['viejo']}»; no lo cambio." );
		} else {
			$nuevo = $r;
			$tareas[] = "H2 «{$t['viejo']}» → «{$t['nuevo']}»";
		}
	}
	$nota = pm_buscar( 'pm_nota', 'precio-viaje-dorado-lodge', $i );
	if ( $nota && false === strpos( $nuevo, get_permalink( $nota->ID ) ) ) {
		// After the key-facts list (the first list block).
		if ( preg_match( '#<!-- /wp:list -->\s*#u', $nuevo, $m, PREG_OFFSET_CAPTURE ) ) {
			$pos = $m[0][1] + strlen( $m[0][0] );
			$nuevo = pm_sc_insertar( $nuevo, $pos, pm_sc_p( sprintf( $t['ir'], pm_sc_a( get_permalink( $nota->ID ), esc_html( get_the_title( $nota->ID ) ) ) ) ) );
			$tareas[] = 'enlace a la nota de precios del viaje bajo los datos clave';
		} else {
			WP_CLI::warning( "dorado ($i): no encontré la lista de datos clave; no enlazo la nota de precios." );
		}
	}
	if ( false === strpos( $nuevo, 'pm-faq' ) ) {
		$nuevo .= "\n\n" . pm_sc_h2( $t['faq_h'] ) . pm_sc_faq( $t['faq'] );
		$tareas[] = 'FAQ: ' . implode( ' | ', array_column( $t['faq'], 0 ) );
	}
	if ( ! $tareas ) {
		WP_CLI::log( "dorado ($i) #{$p->ID}: ya está." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": dorado ($i) #{$p->ID}: " . implode( ' · ', $tareas ) );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, $nuevo, [ 'pm-faq' ], "dorado ($i)" );
		WP_CLI::log( "VERIFIED: dorado ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	}
}

// --- Surubí: FAQ only from the season note and the Disposición already on the page ------------------------------
$surubi = [
	'es' => [
		'slug' => 'surubi', 'faq_h' => 'Preguntas frecuentes',
		'faq' => [
			[ '¿Se puede quedar con un surubí en Corrientes?', 'No. En Corrientes el surubí, manchado y atigrado, es solo pesca y devolución (Disposición 853/22 de la Dirección de Recursos Naturales, Boletín Oficial de Corrientes, 26 de diciembre de 2022). Las reglas de otras provincias cambian: mirá la guía de cada destino.' ],
			[ '¿Cuándo se pesca el surubí?', 'No tiene una sola temporada: se mueve con el agua. Según nuestra nota de temporada, en el Paraná medio (Goya, Esquina, La Paz) la actividad se junta en otoño y primavera; en el Alto Paraná, en los meses fríos; y en Concordia (río Uruguay) los guías hablan de una corrida de fines de septiembre a mediados de noviembre, con el pico en octubre. Solo la ventana de Concordia está dicha con meses en las fuentes que consultamos; las otras dos son lectura nuestra. Detalle: %s.' ],
			[ '¿Dónde se pesca surubí con guía?', 'Nuestras guías cubren %1$s, %2$s, %3$s y %4$s. Goya y Esquina tienen además %5$s.' ],
		],
	],
	'en' => [
		'slug' => 'surubi', 'faq_h' => 'Frequently asked questions',
		'faq' => [
			[ 'Can you keep a surubí in Corrientes?', 'No. In Corrientes the surubí, spotted and striped, is catch and release only (Disposición 853/22 of the Dirección de Recursos Naturales, Boletín Oficial de Corrientes, 26 December 2022). Rules in other provinces change: check the guide of each destination.' ],
			[ 'When is the best time to fish surubí?', 'It has no single season: it moves with the water. According to our season note, on the middle Paraná (Goya, Esquina, La Paz) activity comes together in autumn and spring; on the Upper Paraná, in the cold months; and at Concordia (Uruguay River) guides talk about a run from late September to mid-November, peaking in October. Only the Concordia window is stated in months by the sources we checked; the other two are our reading. Details: %s.' ],
			[ 'Where can you fish surubí with a guide?', 'Our guides cover %1$s, %2$s, %3$s and %4$s. Goya and Esquina also have %5$s.' ],
		],
	],
	'pt' => [
		'slug' => 'surubim', 'faq_h' => 'Perguntas frequentes',
		'faq' => [
			[ 'Dá para ficar com um surubim em Corrientes?', 'Não. Em Corrientes o surubim, pintado e cachara, é só pesque e solte (Disposición 853/22 da Direção de Recursos Naturais, Boletim Oficial de Corrientes, 26 de dezembro de 2022). As regras de outras províncias mudam: veja o guia de cada destino.' ],
			[ 'Quando se pesca o surubim?', 'Não tem uma única temporada: ele se move com a água. Segundo a nossa nota de temporada, no Paraná médio (Goya, Esquina, La Paz) a atividade se concentra no outono e na primavera; no Alto Paraná, nos meses frios; e em Concordia (rio Uruguai) os guias falam de uma corrida do fim de setembro a meados de novembro, com pico em outubro. Só a janela de Concordia está dita com meses nas fontes que consultamos; as outras duas são leitura nossa. Detalhe: %s.' ],
			[ 'Onde se pesca surubim com guia?', 'Nossos guias cobrem %1$s, %2$s, %3$s e %4$s. Goya e Esquina têm também %5$s.' ],
		],
	],
];
foreach ( $surubi as $i => $t ) {
	$p = pm_buscar( 'pm_especie', 'surubi', $i );
	if ( ! $p ) {
		WP_CLI::log( "surubí ($i): no existe, se salta." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, 'pm-faq' ) ) {
		WP_CLI::log( "surubí ($i) #{$p->ID}: ya tiene FAQ." );
		continue;
	}
	$nota = pm_buscar( 'pm_nota', 'cuando-pescar-surubi', $i ) ?? pm_buscar( 'pm_nota', 'cuando-pescar-surubi', 'es' );
	$g    = [ pm_sc_guia_a( [ 'goya' ], $i ), pm_sc_guia_a( [ 'esquina' ], $i ), pm_sc_guia_a( [ 'paso-de-la-patria' ], $i ), pm_sc_guia_a( [ 'la-paz' ], $i ) ];
	$paq  = [];
	foreach ( [ 'goya', 'esquina' ] as $k ) {
		if ( pm_destino_paquetes( $k, $i ) ) {
			$paq[] = pm_sc_a( pm_url_paquetes( $k, $i ), esc_html( pm_t( 'paquetes.especie.link', [ 'lugar' => ucfirst( $k ) ], $i ) ) );
		}
	}
	if ( ! $nota || in_array( '', $g, true ) || 2 !== count( $paq ) ) {
		WP_CLI::warning( "surubí ($i): falta la nota de temporada, alguna guía o los paquetes de Goya/Esquina; se salta." );
		continue;
	}
	$faq = $t['faq'];
	$faq[1][1] = sprintf( $faq[1][1], pm_sc_a( get_permalink( $nota->ID ), esc_html( get_the_title( $nota->ID ) ) ) );
	$faq[2][1] = vsprintf( $faq[2][1], array_merge( $g, [ implode( ' · ', $paq ) ] ) );
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": surubí ($i) #{$p->ID}: FAQ: " . implode( ' | ', array_column( $faq, 0 ) ) );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, $c . "\n\n" . pm_sc_h2( $t['faq_h'] ) . pm_sc_faq( $faq ), [ 'pm-faq', $faq[0][0] ], "surubí ($i)" );
		WP_CLI::log( "VERIFIED: surubí ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
