<?php
/**
 * Dorado pillar note (clave golden-dorado-guia, es/en/pt), 15/11/2026: new section "What to bring for golden dorado fishing in Argentina" (rod, reel, lines, leaders, flies and
 * lures, other items, what the lodge lends). Why: Keyword Planner Brazil, 15/11/2026: "vara para pesca de dourado na argentina" and "equipamento para pesca de dourado na argentina"
 * (10-100 searches a month each, high competition) and nothing of ours answers them.
 * Sources: ONLY what three operators publish on public pages, read with investigar.yml on 15/11/2026 (folders operadores-br-20261115-2106 and equipo-dorado-20261115-2108 of the
 * branch investigacion), each fact attributed in the text ("according to X, page read on 15/11/2026"): Nervous Waters (Suindá Lodge page, Upper Paraná, Corrientes, and its FAQ),
 * Set Fly Fishing (Trip Information Package, a fly-fishing operator with lodges in Corrientes) and Pousada Puerto Yacarey (Itatí, in Portuguese). Where they disagree (leader
 * material) both are shown. NOT written, on purpose: brands or products, affiliates, "the best", fish sizes or records, prices of rentals or breakage fees.
 * Also adds the three pages to the note's sources (skipping URLs already there). Idempotent: skipped when a heading "What to bring / Qué llevar / O que levar" already exists.
 * Dry run first ($aplicar = false). Run with wp-script.yml (script = dorado-equipo-2026-11-15).
 */
$aplicar = false;
$clave   = 'golden-dorado-guia';
$hoy     = '20261115';

// --- Helpers (copied in each script: wp-script.yml sends one file) ---
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
	function pm_sc_a( string $url, string $texto ): string {
		return '<a href="' . esc_url( $url ) . '" rel="noopener">' . $texto . '</a>';
	}
	/** Offset of the first heading block whose plain text matches $regex, or null. */
	function pm_sc_pos_encabezado( string $c, string $regex ): ?int {
		preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
		foreach ( $m[0] as $n => $b ) {
			if ( preg_match( $regex, trim( wp_strip_all_tags( $m[1][ $n ][0] ) ) ) ) {
				return $b[1];
			}
		}
		return null;
	}
}

$u = [
	'sui'  => 'https://www.nervouswaters.com/the-lodges/suinda-lodge/',
	'nwf'  => 'https://www.nervouswaters.com/faqs/',
	'set'  => 'https://setflyfishing.com/en/trip-information-package/',
	'yac'  => 'https://www.pescanaargentina.com.br/hospedagem.html',
];

$S = [];
$S['pt'] = [
	'h'      => 'O que levar para pescar dourado na Argentina: vara, carretel, linhas e iscas',
	'buscar' => '/n[ãa]o cobre/iu',
	'ya'     => '/O que levar para pescar/iu',
	'intro'  => 'Não recomendamos marcas nem produtos. Esta seção reúne o que três operadores com lodges ou pousadas em Corrientes publicam em suas páginas, cada dado com o nome de quem o diz (páginas lidas em 15/11/2026). As listas variam de um operador para outro: antes de comprar equipamento, confirme com o seu.',
	'bloques' => [
		[ 'Vara', 'Para mosca, segundo a Nervous Waters (página do Suindá Lodge, em Itatí), as varas são de 9 pés: para linha 8 no dourado com linha flutuante, para linha 9 nos dourados maiores (com linha flutuante ou de afundamento) e para linha 6 ou 7 no pacu e no pirapitá. A Set Fly Fishing lista uma vara de 9 pés para linha 8 no dourado, uma para linha 6 no pacu e no pirapitá, e pede levar uma de reserva. Para spinning e baitcasting, a Nervous Waters indica varas de ação média a média-pesada, de 6 a 8 pés, para linhas de 8 a 20 libras. A Pousada Puerto Yacarey, em Itatí, fala em «varas de ação média a pesada».' ],
		[ 'Carretel, linhas e backing', 'A Nervous Waters pede carretéis de boa qualidade, com freio eficiente e pelo menos 100 jardas de backing de 30 a 50 libras, e linhas tropicais (flutuantes de 6 ou 7, de 8 e de 9, uma intermediária de 9 e uma de afundamento rápido de 275 a 350 grãos). Avisa para não levar linhas flutuantes de água fria, porque o calor as deixa moles e difíceis de lançar. A Set Fly Fishing pede 150 jardas de backing de 30 libras; para o Alto Paraná, uma linha flutuante tropical e uma intermediária ou de ponta intermediária; para os Esteros do Iberá, duas flutuantes tropicais, uma para superfície e outra para streamers. Para spinning, a Nervous Waters indica molinete de tamanho 2000 a 3000 ou carretilha de recolhimento rápido, com multifilamento de 30 a 40 libras.' ],
		[ 'Líderes e ponta de aço', 'Os dois operadores de mosca coincidem em um ponto: o dourado corta o náilon, e a ponta de aço (<em>wire bite tippet</em>) não é opcional. A Set Fly Fishing pede ponta de aço de 40 libras para dourado; a Nervous Waters, de 40 a 50 libras, com 12 a 15 polegadas, sobre um líder afunilado ou de fluorcarbono grosso. No resto do líder divergem: a Set Fly Fishing pede monofilamento de cerca de 30 e 40 libras e escreve que não deve ser fluorcarbono; a Nervous Waters usa fluorcarbono de 50 a 60 libras. A Pousada Puerto Yacarey cita multifilamento e líderes de fluorcarbono. Para spinning, a Nervous Waters diz que o líder de aço, de 30 a 50 libras, é indispensável.' ],
		[ 'Moscas e iscas', 'Segundo a Nervous Waters, o dourado costuma preferir streamers escuros, em anzóis robustos de 2/0 a 5/0 e de 5 a 7 polegadas; para pacu e pirapitá, moscas secas e imitações de fruta, com varas mais leves. A Set Fly Fishing diz que os guias fornecem moscas e completam a seleção com padrões locais; para quem quiser montar as suas, cita deceivers, crease flies, T-walks e imitações de rato. Em pesca com isca artificial, a Nervous Waters diz que a lista de iscas é interminável e que cada pescador leve as suas; a Pousada Puerto Yacarey cita poppers, minnows e jigs de cores vivas.' ],
		[ 'Outros itens', 'Da lista da Set Fly Fishing: óculos polarizados com cordão (sugere levar dois pares), protetor solar e bálsamo labial, capa de chuva, um bom alicate, calçado confortável de barco que possa molhar, lanterna ou lanterna de cabeça, repelente de insetos, garrafa reutilizável, boné e proteção solar para pescoço e braços. Nos voos dentro da Argentina, a Set Fly Fishing lembra que a segurança aeroportuária local não aceita varas e carretéis na cabine: vão na bagagem despachada.' ],
		[ 'O que o lodge empresta', 'A Nervous Waters diz que seus lodges têm varas, carretéis e linhas de mosca para quem precisar (avise antes da chegada) e que moscas e líderes são vendidos lá; na página do Suindá Lodge diz ter equipamento emprestado de mosca e de spinning. A Pousada Puerto Yacarey diz que orienta os hóspedes sobre o equipamento ideal antes da chegada. Se o equipamento está incluído no preço muda de um operador para outro: veja %s.' ],
	],
	'nota_ancla' => 'nossa nota sobre o que inclui um pacote de pesca na Argentina',
	'fuentes_i'  => 'Páginas lidas em 15/11/2026: %s, %s, %s e %s.',
	'f_n'        => [ 'Nervous Waters (Suindá Lodge)', 'Nervous Waters (perguntas frequentes)', 'Set Fly Fishing (informações de viagem)', 'Pousada Puerto Yacarey' ],
	'fuentes'    => [ 'Nervous Waters: página do Suindá Lodge (equipamento)', 'Nervous Waters: perguntas frequentes (equipamento emprestado)', 'Set Fly Fishing: Trip Information Package (equipamento para dourado)', 'Pousada Puerto Yacarey: hospedagem (equipamento)' ],
];
$S['es'] = [
	'h'      => 'Qué llevar para pescar dorado en la Argentina: caña, reel, líneas y señuelos',
	'buscar' => '/no cubre/iu',
	'ya'     => '/Qué llevar para pescar/iu',
	'intro'  => 'No recomendamos marcas ni productos. Esta sección reúne lo que tres operadores con lodges o posadas en Corrientes publican en sus páginas, cada dato con el nombre de quien lo dice (páginas leídas el 15/11/2026). Las listas varían de un operador a otro: antes de comprar equipo, confirmalo con el tuyo.',
	'bloques' => [
		[ 'Caña', 'Para mosca, según Nervous Waters (página de Suindá Lodge, en Itatí), las cañas son de 9 pies: para línea 8 en el dorado con línea flotante, para línea 9 en los dorados más grandes (con línea flotante o hundidora) y para línea 6 o 7 en el pacú y el pirapitá. Set Fly Fishing lista una caña de 9 pies para línea 8 en el dorado, una para línea 6 en el pacú y el pirapitá, y pide llevar una de repuesto. Para spinning y baitcasting, Nervous Waters indica cañas de acción media a media-pesada, de 6 a 8 pies, para líneas de 8 a 20 libras. La Pousada Puerto Yacarey, en Itatí, habla de «varas de ação média a pesada» (cañas de acción media a pesada).' ],
		[ 'Reel, líneas y backing', 'Nervous Waters pide reeles de buena calidad, con freno eficiente y al menos 100 yardas de backing de 30 a 50 libras, y líneas tropicales (flotantes de 6 o 7, de 8 y de 9, una intermedia de 9 y una de hundimiento rápido de 275 a 350 granos). Avisa que no hay que llevar líneas flotantes de agua fría, porque el calor las deja blandas y difíciles de lanzar. Set Fly Fishing pide 150 yardas de backing de 30 libras; para el Alto Paraná, una línea flotante tropical y una intermedia o de punta intermedia; para los Esteros del Iberá, dos flotantes tropicales, una para superficie y otra para streamers. Para spinning, Nervous Waters indica reel frontal de tamaño 2000 a 3000 o baitcaster de recogida rápida, con multifilamento de 30 a 40 libras.' ],
		[ 'Líderes y punta de acero', 'Los dos operadores de mosca coinciden en un punto: el dorado corta el nylon, y la punta de acero (<em>wire bite tippet</em>) no es opcional. Set Fly Fishing pide punta de acero de 40 libras para dorado; Nervous Waters, de 40 a 50 libras, de 12 a 15 pulgadas, sobre un líder cónico o de fluorocarbono grueso. En el resto del líder difieren: Set Fly Fishing pide monofilamento de unas 30 y 40 libras y escribe que no debe ser fluorocarbono; Nervous Waters usa fluorocarbono de 50 a 60 libras. La Pousada Puerto Yacarey menciona multifilamento y líderes de fluorocarbono. Para spinning, Nervous Waters dice que el líder de acero, de 30 a 50 libras, es imprescindible.' ],
		[ 'Moscas y señuelos', 'Según Nervous Waters, el dorado suele preferir streamers oscuros, en anzuelos robustos de 2/0 a 5/0 y de 5 a 7 pulgadas; para pacú y pirapitá, moscas secas e imitaciones de fruta, con cañas más livianas. Set Fly Fishing dice que los guías proveen moscas y completan la selección con patrones locales; para quien quiera atar las suyas, menciona deceivers, crease flies, T-walks e imitaciones de ratón. En pesca con señuelo, Nervous Waters dice que la lista de señuelos es interminable y que cada pescador lleve los suyos; la Pousada Puerto Yacarey menciona poppers, minnows y jigs de colores vivos.' ],
		[ 'Otros elementos', 'De la lista de Set Fly Fishing: anteojos polarizados con cordón (sugiere llevar dos pares), protector solar y bálsamo labial, campera y pantalón de lluvia, una buena pinza, calzado cómodo de lancha que pueda mojarse, linterna o frontal, repelente de insectos, botella reutilizable, gorra y protección solar para cuello y brazos. En los vuelos dentro de la Argentina, Set Fly Fishing recuerda que la seguridad aeroportuaria local no acepta cañas ni reeles en cabina: van en el equipaje despachado.' ],
		[ 'Qué presta el lodge', 'Nervous Waters dice que sus lodges tienen cañas, reeles y líneas de mosca para quien los necesite (avisá antes de llegar) y que las moscas y los líderes se venden allí; en la página de Suindá Lodge dice tener equipo prestado de mosca y de spinning. La Pousada Puerto Yacarey dice que orienta a los huéspedes sobre el equipo ideal antes de la llegada. Si el equipo está incluido en el precio cambia de un operador a otro: mirá %s.' ],
	],
	'nota_ancla' => 'nuestra nota sobre qué incluye un paquete de pesca en Argentina',
	'fuentes_i'  => 'Páginas leídas el 15/11/2026: %s, %s, %s y %s.',
	'f_n'        => [ 'Nervous Waters (Suindá Lodge)', 'Nervous Waters (preguntas frecuentes)', 'Set Fly Fishing (información de viaje)', 'Pousada Puerto Yacarey' ],
	'fuentes'    => [ 'Nervous Waters: página de Suindá Lodge (equipo)', 'Nervous Waters: preguntas frecuentes (equipo prestado)', 'Set Fly Fishing: Trip Information Package (equipo para dorado)', 'Pousada Puerto Yacarey: hospedagem (equipo)' ],
];
$S['en'] = [
	'h'      => 'What to bring for golden dorado fishing in Argentina: rod, reel, lines and flies',
	'buscar' => '/does not cover/iu',
	'ya'     => '/What to bring for/iu',
	'intro'  => 'We do not recommend brands or products. This section gathers what three operators with lodges or posadas in Corrientes publish on their pages, each fact with the name of who says it (pages read on 15/11/2026). The lists differ from one operator to the next: before buying gear, check with yours.',
	'bloques' => [
		[ 'Rod', 'For fly fishing, according to Nervous Waters (Suindá Lodge page, in Itatí), the rods are 9 feet: an 8-weight for dorado on a floating line, a 9-weight for larger dorado (floating or sinking line) and a 6- or 7-weight for pacú and pirá pitá. Set Fly Fishing lists a 9-foot 8-weight for dorado, a 6-weight for pacú and pirá pitá, and asks you to bring a backup. For spinning and baitcasting, Nervous Waters suggests medium to medium-heavy rods of 6 to 8 feet, rated for 8 to 20 lb line. Pousada Puerto Yacarey, in Itatí, speaks of «varas de ação média a pesada» (medium to heavy action rods).' ],
		[ 'Reel, lines and backing', 'Nervous Waters asks for good-quality reels with an excellent drag and at least 100 yards of 30 to 50 lb backing, and tropical lines (floating 6 or 7-weight, 8-weight and 9-weight, a 9-weight intermediate and a fast-sinking 275 to 350 grain). It warns against bringing cold-water floating lines, because the heat makes them soft and hard to cast. Set Fly Fishing asks for 150 yards of 30 lb backing; for the Upper Paraná, a tropical floating line and a tropical intermediate or intermediate-tip line; for the Esteros del Iberá, two tropical floating lines, one for the surface and one for streamers. For spinning, Nervous Waters suggests a 2000 to 3000 open-face reel or a fast-retrieve baitcaster, with 30 to 40 lb braid.' ],
		[ 'Leaders and wire', 'The two fly-fishing operators agree on one point: dorado cut nylon, and a wire bite tippet is not optional. Set Fly Fishing asks for 40 lb wire for dorado; Nervous Waters, 40 to 50 lb and 12 to 15 inches, on a tapered or heavy fluorocarbon leader. On the rest of the leader they differ: Set Fly Fishing asks for roughly 30 and 40 lb monofilament and writes that it should not be fluorocarbon; Nervous Waters uses 50 to 60 lb fluorocarbon. Pousada Puerto Yacarey mentions braided line and fluorocarbon leaders. For spinning, Nervous Waters says a 30 to 50 lb steel leader is a must.' ],
		[ 'Flies and lures', 'According to Nervous Waters, dorado usually prefer dark streamers, on stout 2/0 to 5/0 hooks and 5 to 7 inches long; for pacú and pirá pitá, dry flies and fruit patterns on lighter rods. Set Fly Fishing says the guides provide flies and add local patterns; for those who want to tie their own, it lists deceivers, crease flies, T-walks and mouse patterns. For lure fishing, Nervous Waters says the list of lures is never-ending and that each angler should bring their own; Pousada Puerto Yacarey mentions poppers, minnows and jigs in bright colours.' ],
		[ 'Other items', 'From the Set Fly Fishing list: polarised sunglasses with a strap (it suggests two pairs), sunscreen and lip balm, rain jacket and pants, a good pair of pliers, comfortable boat shoes that can get wet, a flashlight or headlamp, insect repellent, a reusable water bottle, a hat and sun protection for neck and arms. On flights within Argentina, Set Fly Fishing reminds you that local airport security does not accept rods and reels in the cabin: they go in checked baggage.' ],
		[ 'What the lodge lends', 'Nervous Waters says its lodges have fly rods, reels and lines for those who need them (tell them before arrival) and that flies and leaders are sold there; on the Suindá Lodge page it says it has loaner fly and spinning equipment. Pousada Puerto Yacarey says it advises guests on the right gear before they arrive. Whether gear is included in the price differs from one operator to another: see %s.' ],
	],
	'nota_ancla' => 'our note on what a fishing package in Argentina includes',
	'fuentes_i'  => 'Pages read on 15/11/2026: %s, %s, %s and %s.',
	'f_n'        => [ 'Nervous Waters (Suindá Lodge)', 'Nervous Waters (FAQ)', 'Set Fly Fishing (trip information)', 'Pousada Puerto Yacarey' ],
	'fuentes'    => [ 'Nervous Waters: Suindá Lodge page (equipment)', 'Nervous Waters: FAQ (loaner equipment)', 'Set Fly Fishing: Trip Information Package (gear for dorado)', 'Pousada Puerto Yacarey: hospedagem (equipment)' ],
];

$nota = function ( string $c, string $i ): ?WP_Post {
	return array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $c, 'numberposts' => -1 ] ), fn( $x ) => pm_idioma_de( $x->ID ) === $i ) )[0] ?? null;
};
$cambios = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$s = $S[ $i ];
	$p = $nota( $clave, $i );
	if ( ! $p ) {
		WP_CLI::warning( "($i): no existe la nota $clave; no toco nada." );
		continue;
	}
	$c = $p->post_content;
	if ( null !== pm_sc_pos_encabezado( $c, $s['ya'] ) ) {
		WP_CLI::log( "($i) #{$p->ID}: ya tiene la sección «{$s['h']}»; no toco nada." );
		continue;
	}
	$pos = pm_sc_pos_encabezado( $c, $s['buscar'] );
	if ( null === $pos ) {
		WP_CLI::warning( "($i) #{$p->ID}: no encuentro el encabezado «no cubre / não cobre / does not cover»; no toco nada." );
		continue;
	}
	$paq = $nota( 'paquete-incluye-no-incluye', $i );
	$enl = $paq ? pm_sc_a( get_permalink( $paq->ID ), $s['nota_ancla'] ) : $s['nota_ancla'];
	$bloque = pm_sc_h2( $s['h'] ) . pm_sc_p( $s['intro'] );
	foreach ( $s['bloques'] as [ $h3, $txt ] ) {
		$bloque .= pm_sc_h2( $h3, 3 ) . pm_sc_p( false !== strpos( $txt, '%s' ) ? sprintf( $txt, $enl ) : $txt );
	}
	$f_links = [ pm_sc_a( $u['sui'], $s['f_n'][0] ), pm_sc_a( $u['nwf'], $s['f_n'][1] ), pm_sc_a( $u['set'], $s['f_n'][2] ), pm_sc_a( $u['yac'], $s['f_n'][3] ) ];
	$bloque .= pm_sc_p( '<em>' . sprintf( $s['fuentes_i'], ...$f_links ) . '</em>' );
	$nuevo   = substr( $c, 0, $pos ) . $bloque . substr( $c, $pos );
	// Sources: add the four pages (skipping URLs already there).
	$filas = pm_filas( 'fuentes', $p->ID );
	$urls  = array_column( $filas, 'url' );
	$nuevas = 0;
	foreach ( [ 'sui', 'nwf', 'set', 'yac' ] as $k => $kk ) {
		if ( ! in_array( $u[ $kk ], $urls, true ) ) {
			$filas[] = [ 'nombre' => $s['fuentes'][ $k ], 'url' => $u[ $kk ], 'oficial' => 0, 'consultada' => $hoy ];
			$nuevas++;
		}
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ($i) #{$p->ID} sección «{$s['h']}» (" . mb_strlen( $s['h'] ) . ' car.), ' . str_word_count( wp_strip_all_tags( $bloque ) ) . " palabras, antes del encabezado «no cubre»; $nuevas fuentes nuevas; enlace a " . ( $paq ? "nota #{$paq->ID}" : 'ninguna nota (falta)' ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$antes_fuentes = count( pm_filas( 'fuentes', $p->ID ) );
	$slug = $p->post_name;
	$r = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	update_field( 'fuentes', $filas, $p->ID );
	$g  = get_post( $p->ID );
	$ok = false !== strpos( $g->post_content, $s['h'] ) && $g->post_name === $slug && count( pm_filas( 'fuentes', $p->ID ) ) === $antes_fuentes + $nuevas
		&& null !== pm_sc_pos_encabezado( $g->post_content, $s['buscar'] ) && pm_sc_pos_encabezado( $g->post_content, $s['ya'] ) < pm_sc_pos_encabezado( $g->post_content, $s['buscar'] );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": pilar ($i) #{$p->ID} con la sección de equipo y " . count( pm_filas( 'fuentes', $p->ID ) ) . ' fuentes, slug ' . $slug . ' ' . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios notas." );
