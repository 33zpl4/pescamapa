<?php
/**
 * Dated informative note "Cuánto cuesta pescar con guía en Argentina: precios publicados a noviembre de 2026" (clave precios-pesca-guiada-2026-11,
 * es + en + pt), 20/11/2026. Original-data piece, informative (nota_informativa = 1: no commercial calls), national (no nota_destino), type "datos".
 * Every number comes from wordpress/build/precios-2026-11/ (generar.py -> precios.json, precios.csv, resumen.json; graficos.py + precios-png.mjs make
 * the two infographics). The prices are ONLY those already published on the site with a reading date; nothing is converted between currencies; no
 * provider is named (zones and codes only); no external links. The $D array below is checked against resumen.json by wordpress/build/precios-2026-11/verificar.py.
 * Files (wordpress/fotos/, copied to /tmp/pm-fotos/ by wp-script.yml): precios-pesca-guiada-por-pescador-y-dia.png (cover), precios-pesca-fichas-con-precio.png,
 * precios-pesca-guiada-2026-11.csv. Attachments are reused by _pm_archivo_origen; credit "Pescamapa", own work.
 * Idempotent: an existing note (same clave and language) is not touched. Dry run first ($aplicar = false). Run with wp-script.yml (script = nota-precios-pesca-guiada-2026-11-20).
 */
if ( ! function_exists( 'pm_pg_p' ) ) {
	function pm_pg_h2( string $t ): string {
		return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->\n\n";
	}
	function pm_pg_p( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	function pm_pg_lista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
	}
	function pm_pg_tabla( array $cabecera, array $filas ): string {
		$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
		$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
		return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->\n\n";
	}
	/** FAQ as a group block with the class pm-faq (visible and read by pm_faqs_contenido() for the FAQPage JSON-LD). */
	function pm_pg_faq( array $faqs ): string {
		$in = '';
		foreach ( $faqs as [ $q, $a ] ) {
			$in .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( $in ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	function pm_pg_figura( int $att, string $url, string $alt, string $pie ): string {
		return "<!-- wp:image {\"id\":{$att},\"sizeSlug\":\"large\",\"linkDestination\":\"none\"} -->\n<figure class=\"wp-block-image size-large\" style=\"margin:1.5em 0\"><img src=\"" . esc_url( $url ) . "\" alt=\"" . esc_attr( $alt ) . "\" class=\"wp-image-{$att}\"/><figcaption class=\"wp-element-caption\">{$pie}</figcaption></figure>\n<!-- /wp:image -->\n\n";
	}
}

$aplicar = false;
$clave   = 'precios-pesca-guiada-2026-11';
$hoy     = '20261120';
$leido   = '20261113';
$carpeta = '/tmp/pm-fotos';

// ---- The numbers (typed once; wordpress/build/precios-2026-11/verificar.py compares them with resumen.json) ----
$D = [
	'filas' => 71, 'usables' => 57, 'fuera' => 14, 'oferentes' => 32, 'usd' => 63, 'ars' => 4, 'eur' => 2, 'brl' => 2, 'propia' => 47, 'listado' => 23, 'programa' => 1,
	'bar_n' => 7, 'bar_lug' => 5, 'bar_min' => 520, 'bar_max' => 690, 'bar_med' => 550, 'bar_pp_min' => 260, 'bar_pp_max' => 345,
	'cor_n' => 7, 'cor_min' => 6400, 'cor_max' => 7950, 'cor_med' => 7850, 'cor_d_min' => 1067, 'cor_d_max' => 1325,
	'pn_n' => 6, 'pn_min' => 5800, 'pn_max' => 9200, 'pn_med' => 6975, 'pn_dn' => 5, 'pn_d_min' => 967, 'pn_d_max' => 1533,
	'ps_n' => 4, 'ps_min' => 8000, 'ps_max' => 14950, 'ps_med' => 11470, 'ps_d_min' => 1231, 'ps_d_max' => 2300,
	'esq_n' => 6, 'esq_min' => 605, 'esq_max' => 1305, 'esq_d_min' => 276, 'esq_d_max' => 353,
	'lp_n' => 23, 'lp_lug' => 8, 'lp_min' => 3950, 'lp_max' => 7595, 'lp_med' => 5200, 'lp_d_min' => 1250, 'lp_d_max' => 2025,
	'fichas' => 58, 'con_precio' => 13, 'sin_precio' => 45, 'pct' => 22,
];
$sep = [ 'es' => [ ',', '.' ], 'pt' => [ ',', '.' ], 'en' => [ '.', ',' ] ];
$num = fn( $v, string $i ) => number_format( $v, 0, $sep[ $i ][0], $sep[ $i ][1] );
$dec = fn( float $v, string $i ) => number_format( $v, 2, $sep[ $i ][0], $sep[ $i ][1] );
$sub = function ( string $t, string $i ) use ( $D, $num, $dec ): string {
	$t = str_replace( [ '[[eur1]]', '[[eur2]]', '[[esq_med]]' ], [ $dec( 599.69, $i ), $dec( 445.22, $i ), $dec( 932.5, $i ) ], $t );
	return preg_replace_callback( '/\[\[([a-z0-9_]+)\]\]/', function ( $m ) use ( $D, $num, $i ) {
		isset( $D[ $m[1] ] ) || WP_CLI::error( "número desconocido [[{$m[1]}]]" );
		return $num( $D[ $m[1] ], $i );
	}, $t );
};

// ---- Attachments: [ key => file, title, alt per language ] ----
$A = [
	'dia' => [
		'archivo' => 'precios-pesca-guiada-por-pescador-y-dia.png', 'titulo' => 'US$ por pescador y por día de pesca, por destino y formato (noviembre de 2026)',
		'alt' => [
			'es' => 'Gráfico de puntos con los precios publicados en US$ por pescador y por día de pesca: día con guía en Bariloche de 260 a 345 (7 precios), posada de Esquina de 276 a 353 (6), Delta y Santa Fe de 480 a 900 (3), paquetes de lodge de 1.250 a 2.025 (23) y semanas de 7 noches de 1.067 a 1.325 en Corrientes (7), de 967 a 1.533 en la Patagonia norte (5) y de 1.231 a 2.300 en la Patagonia sur (4)',
			'en' => 'Dot chart of the published prices in US$ per angler per fishing day: guided day in Bariloche 260 to 345 (7 prices), Esquina guesthouse 276 to 353 (6), Delta and Santa Fe 480 to 900 (3), lodge packages 1,250 to 2,025 (23) and 7-night weeks 1,067 to 1,325 in Corrientes (7), 967 to 1,533 in northern Patagonia (5) and 1,231 to 2,300 in southern Patagonia (4)',
			'pt' => 'Gráfico de pontos com os preços publicados em US$ por pescador e por dia de pesca: dia com guia em Bariloche de 260 a 345 (7 preços), pousada de Esquina de 276 a 353 (6), Delta e Santa Fe de 480 a 900 (3), pacotes de lodge de 1.250 a 2.025 (23) e semanas de 7 noites de 1.067 a 1.325 em Corrientes (7), de 967 a 1.533 na Patagônia norte (5) e de 1.231 a 2.300 na Patagônia sul (4)',
		],
	],
	'fichas' => [
		'archivo' => 'precios-pesca-fichas-con-precio.png', 'titulo' => 'Cuántas fichas de Pescamapa publican su precio, por zona',
		'alt' => [
			'es' => 'Barras horizontales con las fichas de lugares que tienen precio con fecha, por zona: Corrientes 4 de 24, Patagonia sur 4 de 13, Bariloche 5 de 9, Chubut y Neuquén 0 de 6, Entre Ríos y Buenos Aires 0 de 6; en total 13 de 58',
			'en' => 'Horizontal bars with the place listings that have a dated price, by zone: Corrientes 4 of 24, southern Patagonia 4 of 13, Bariloche 5 of 9, Chubut and Neuquén 0 of 6, Entre Ríos and Buenos Aires 0 of 6; 13 of 58 in total',
			'pt' => 'Barras horizontais com as fichas de lugares que têm preço com data, por zona: Corrientes 4 de 24, Patagônia sul 4 de 13, Bariloche 5 de 9, Chubut e Neuquén 0 de 6, Entre Ríos e Buenos Aires 0 de 6; no total 13 de 58',
		],
	],
];
$CSV = 'precios-pesca-guiada-2026-11.csv';

$N = [];
$N['es'] = [
	'titulo'          => 'Cuánto cuesta pescar con guía en Argentina: precios publicados a noviembre de 2026',
	'slug'            => 'cuanto-cuesta-pescar-con-guia-argentina-noviembre-2026',
	'bajada'          => 'Rangos de los precios publicados que relevamos: día con guía, semana en un lodge y paquetes, por pescador y por día de pesca, con la fecha y la fuente de cada uno.',
	'seo_title'       => 'Cuánto cuesta pescar con guía en Argentina (nov. 2026)',
	'seo_description' => 'Precios publicados de pesca con guía en Argentina, nov. 2026: día cerca de Bariloche US$ [[bar_min]]–[[bar_max]] por bote; semana en lodge desde US$ [[pn_min]]. Datos y método.',
	'datos'           => [
		'Día completo con guía cerca de Bariloche: US$ [[bar_min]] a [[bar_max]] por bote para 1 o 2 pescadores, mediana [[bar_med]] (n = [[bar_n]] precios de [[bar_lug]] lugares; leídos el 31/10 y el 11/11/2026).',
		'Semana de 7 noches en un lodge, por persona: US$ [[cor_min]] a [[cor_max]] en Corrientes (n = [[cor_n]]), [[pn_min]] a [[pn_max]] en la Patagonia norte (n = [[pn_n]]) y [[ps_min]] a [[ps_max]] en la Patagonia sur (n = [[ps_n]] lugares).',
		'Por pescador y por día de pesca, la semana en un lodge sale entre US$ [[pn_d_min]] y [[ps_d_max]] según el destino; el día con guía en Bariloche, entre US$ [[bar_pp_min]] y [[bar_pp_max]] si van dos en el bote.',
		'Los días con guía del Litoral y de El Calafate se publican en pesos: desde $ 480.000 por lancha en Esquina (1/11/2026) y $ 720.000 por pescador en El Calafate (5/11/2026).',
		'Solo [[con_precio]] de las [[fichas]] fichas de lugares de Pescamapa publican un precio con fecha.',
	],
	'orig'            => 'Todo sale de wordpress/build/precios-2026-11/ (generar.py: precios.json, precios.csv, resumen.json; graficos.py y precios-png.mjs para las dos infografías). Son solo precios ya publicados en el sitio (fichas, paquetes y notas de precios) con fecha de lectura entre el 31/10 y el 13/11/2026; sin conversión entre monedas; sin tarifas de acuerdos ni cifras de agencias que no estén en el sitio; sin nombres de lugares (zonas y códigos). Recuento de fichas hecho con los scripts aplicados registrados en el repositorio al 19/11/2026: la base en vivo no se consultó. Dos fichas de Goya con cifras en pesos sin fecha de lectura no cuentan. Las notas de precios anteriores citan «desde US$ 500» en Bariloche: viene de un lugar sin identificar con certeza y no se cuenta dos veces.',
	'f'               => 'Precios publicados por cada lugar en las fichas, paquetes y notas de precios de Pescamapa (lecturas del 31/10 al 13/11/2026)',
	'p0'              => '<strong>Un día de pesca con guía cerca de Bariloche cuesta de US$ [[bar_min]] a [[bar_max]] por bote para uno o dos pescadores</strong> (mediana [[bar_med]]; n = [[bar_n]] precios de [[bar_lug]] lugares). Una semana de 7 noches en un lodge cuesta, por persona, de US$ [[cor_min]] a [[cor_max]] en Corrientes (n = [[cor_n]]), de [[pn_min]] a [[pn_max]] en la Patagonia norte (n = [[pn_n]]) y de [[ps_min]] a [[ps_max]] en la Patagonia sur (n = [[ps_n]]). En el Litoral el día con guía se publica en pesos: desde $ 480.000 por lancha en Esquina (n = 1, 1/11/2026).',
	'fecha'           => '<em>Precios leídos entre el 31 de octubre y el 13 de noviembre de 2026, en la moneda en que cada lugar los publica. Elaboración: Pescamapa. Es un recuento de los precios publicados que relevamos, no una encuesta ni el promedio del país.</em>',
	'h1'              => 'Los precios, por destino y formato',
	'p1'              => 'Cada fila es un destino y un formato. «n» es la cantidad de precios publicados (de lugares, donde dice «lugares»); el rango y la mediana aparecen solo con 3 o más. La última columna divide por los pescadores y los días de pesca que publica cada lugar.',
	'cab'             => [ 'Destino y formato', 'n', 'Precio publicado (rango)', 'Mediana', 'Por pescador y por día de pesca', 'Leído' ],
	'filas'           => [
		[ '<strong>Bariloche</strong>, día completo con guía en bote, por bote (1 o 2 pescadores)', '[[bar_n]] precios, [[bar_lug]] lugares', 'US$ [[bar_min]]–[[bar_max]]', 'US$ [[bar_med]]', 'US$ [[bar_pp_min]]–[[bar_pp_max]] (dos en el bote)', '31/10 y 11/11' ],
		[ '<strong>Esquina</strong>, jornada en lancha, «desde», por lancha', '1', '$ 480.000', '—', 'no publica cuántos pescadores', '1/11' ],
		[ '<strong>Bariloche</strong>, día de 9 horas para hasta 2 personas, reserva en una plataforma', '1', '$ 666.000', '—', '$ 333.000 (dos personas)', '5/11' ],
		[ '<strong>El Calafate</strong>, día completo y medio día, por pescador', '1 y 1', '$ 720.000 y $ 440.000', '—', '$ 720.000 (día completo)', '5/11' ],
		[ '<strong>Bariloche</strong>, día completo y medio día, por grupo de hasta 2, en euros', '1 y 1', '€ [[eur1]] y € [[eur2]]', '—', 'no se calcula (euros)', '11/11' ],
		[ '<strong>Esquina</strong>, posada, paquete de 2 a 4 días de pesca, «desde», por persona', '[[esq_n]]', 'US$ [[esq_min]]–[[esq_max]]', 'US$ [[esq_med]]', 'US$ [[esq_d_min]]–[[esq_d_max]]', '4 y 5/11' ],
		[ '<strong>Delta del Paraná</strong>, barco-hotel de 3 y de 4 noches, «desde», por persona', '2', 'US$ 1.260 y 1.680', '—', 'US$ 504 y 480', '5/11' ],
		[ '<strong>Lodges</strong>, paquetes de 3 a 6 noches (Corrientes, Neuquén y Santa Cruz), por persona', '[[lp_n]] precios, [[lp_lug]] lugares', 'US$ [[lp_min]]–[[lp_max]]', 'US$ [[lp_med]]', 'US$ [[lp_d_min]]–[[lp_d_max]]', '5, 10 y 13/11' ],
		[ '<strong>Corrientes</strong>, semana de 7 noches y 6 días, por persona', '[[cor_n]] lugares', 'US$ [[cor_min]]–[[cor_max]]', 'US$ [[cor_med]]', 'US$ [[cor_d_min]]–[[cor_d_max]]', '13/11' ],
		[ '<strong>Patagonia norte</strong> (Neuquén, Río Negro y Chubut), semana de 7 noches, por persona', '[[pn_n]] lugares', 'US$ [[pn_min]]–[[pn_max]]', 'US$ [[pn_med]]', 'US$ [[pn_d_min]]–[[pn_d_max]] (n = [[pn_dn]]: un lugar no publica los días de pesca)', '5 y 10/11' ],
		[ '<strong>Patagonia sur</strong> (Santa Cruz y Tierra del Fuego), semana de 7 noches, por persona', '[[ps_n]] lugares', 'US$ [[ps_min]]–[[ps_max]]', 'US$ [[ps_med]]', 'US$ [[ps_d_min]]–[[ps_d_max]]', '10 y 13/11' ],
		[ '<strong>Paraná de Santa Fe</strong>, 4 días de pesca, «desde», por persona', '1', 'US$ 3.600', '—', 'US$ 900', '5/11' ],
	],
	'p1b'             => 'Rango de las semanas: del precio más bajo al más alto publicado (algunos lugares publican precios por fecha); mediana: de los precios más bajos de cada lugar. En la Patagonia sur y en Corrientes hay dos lugares del mismo grupo con la misma tarifa; cada uno cuenta como un lugar. Los pesos y los euros figuran como se publican.',
	'cap1'            => 'US$ por pescador y por día de pesca, por formato, con n. Precios leídos entre el 31/10 y el 13/11/2026. Fuente: precios publicados por cada lugar. Elaboración: Pescamapa.',
	'h2'              => 'Por día y por bote, o por semana y por pescador',
	'p2'              => 'Los lugares no publican en la misma unidad. Un guía cobra por bote para uno o dos pescadores; un lodge, por persona y por semana en habitación doble (salvo donde se aclara que es individual); una posada, por persona según el grupo en la lancha. Para compararlos dividimos por los pescadores y los días de pesca que el propio texto publica: US$ 550 por bote ÷ 2 pescadores = US$ 275 por pescador; US$ 9.990 la semana ÷ 6 días de pesca = US$ 1.665 por día. Si va un solo pescador en el bote paga el precio entero. Lo que no se puede dividir con certeza queda fuera del cálculo y figura en el CSV con el motivo.',
	'h3'              => 'Qué suele incluir cada formato',
	'p3'              => 'En las 7 fichas de lodge por semana con precio, alojamiento, comidas y bebidas figuran en 7 de 7; los traslados en 5 de 7, el equipo de préstamo en 4 de 7 y la licencia de pesca en 3 de 7. En las 5 fichas de día con guía en Bariloche, el equipo y el almuerzo figuran en 5 de 5, los traslados y las bebidas en 4 de 5, y ninguna dice incluir la licencia (cuatro dicen que va aparte y una no lo aclara). Por eso dos precios solo se comparan si cubren lo mismo: {n:paquete-incluye-no-incluye|qué incluye un paquete de pesca en Argentina} y {n:licencia-pesca-patagonia-precios|cuánto cuesta la licencia en la Patagonia}.',
	'h4'              => 'Dólares y pesos',
	'p4'              => 'De los [[filas]] precios publicados, [[usd]] están en dólares, [[ars]] en pesos, [[eur]] en euros y [[brl]] en reales. Todas las semanas y todos los paquetes de lodge están en dólares. Los días con guía de Esquina, de El Calafate y de una plataforma de reservas de Bariloche están en pesos; los de los guías de Bariloche, en dólares, y dos en euros. No convertimos entre monedas: el peso se mueve y cada lugar fija el suyo, así que confirmá la moneda y el tipo de cambio al reservar. Un paquete en reales (R$ 1.800 por pescador y por día con dos en la lancha y R$ 1.600 con tres, leído el 10/11/2026) figura en el CSV.',
	'h5'              => 'Cuántos lugares publican su precio y cuántos no',
	'p5'              => 'De las [[fichas]] fichas públicas de lugares que registramos al 19/11/2026, [[con_precio]] tienen un precio con fecha de lectura ([[pct]] %) y [[sin_precio]] no. Por zona: Bariloche 5 de 9, Patagonia sur 4 de 13, Corrientes 4 de 24, Chubut y Neuquén 0 de 6, Entre Ríos y Buenos Aires 0 de 6. Es un recuento de nuestras fichas, no del total de lugares que existen. Por eso la tabla tiene pocos lugares por destino.',
	'cap2'            => 'Fichas de lugares con precio fechado, por zona. Registro de Pescamapa al 19/11/2026. Elaboración: Pescamapa.',
	'h6'              => 'Cómo lo calculamos',
	'l6'              => [
		'<strong>Fuente:</strong> precios ya publicados en fichas, paquetes y notas de precios de Pescamapa, cada uno con su fecha de lectura (del 31/10 al 13/11/2026). Son [[filas]] precios de [[oferentes]] lugares y agencias: [[propia]] de la página del propio lugar o de su grupo, [[programa]] de un programa enviado a Pescamapa y [[listado]] de listados de plataformas o agencias.',
		'<strong>Qué entra:</strong> [[usables]] de los [[filas]] precios entran en la comparación. Los otros [[fuera]] quedan fuera por moneda (euros y reales), por ser medio día, por no aclarar cuántos pescadores o cuántos días de pesca cubren, o por un posible lugar repetido; el CSV da el motivo de cada uno. Quedan afuera también las tarifas que un lugar pidió no publicar y las cifras sin fecha de lectura.',
		'<strong>Cuenta:</strong> precio ÷ pescadores ÷ días de pesca publicados. El rango va del precio más bajo al más alto publicado; la mediana, solo con 3 o más; en los lugares con precios por fecha, la mediana usa el más bajo.',
		'<strong>Qué no es:</strong> no es una encuesta ni representa el mercado. Mezcla precios «desde», temporadas distintas (2026/27 y 2027) y habitación doble salvo donde se dice individual. Los paquetes de una posada de Esquina se leyeron el 4 y el 5/11/2026; el 19/11 su portada ya no mostraba tarifas. Otras notas nuestras citan «desde US$ 500» por un día en Bariloche: viene de un lugar que no pudimos identificar con certeza y aquí no se cuenta dos veces.',
		'<strong>Sin conversión de monedas:</strong> los pesos, los euros y los reales se muestran como se publican. Los precios cambian: confirmalos con el lugar antes de reservar.',
	],
	'h7'              => 'Usá los números y los gráficos',
	'p7'              => 'Podés usar estos números y gráficos citando a Pescamapa con enlace a esta nota.',
	'p7b'             => '<a href="{csv}" download>Descargá los precios (CSV)</a>: una fila por precio publicado, con destino, formato, monto y moneda como se publican, unidad, pescadores cubiertos, noches, días de pesca, fecha de lectura, tipo de fuente, el valor por pescador y por día de pesca con su fórmula y, si no entra en la comparación, el motivo. Los lugares figuran con un código, no con su nombre. Los nombres de las columnas están en español.',
	'p7c'             => 'Cada precio con su fecha está en las guías: {g:bariloche|Bariloche}, {g:esquina|Esquina}, {g:itati|Itatí}, {g:esteros-del-ibera|Esteros del Iberá}, {g:junin-de-los-andes|Junín de los Andes}, {g:esquel-trevelin|Esquel y Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|río Gallegos}, {g:rio-grande|Río Grande} y {g:el-calafate|El Calafate}. Más contexto en {n:cuanto-cuesta-viaje-pesca-argentina|cuánto cuesta un viaje de pesca en Argentina}, {n:precio-pesca-bariloche-limay|cuánto cuesta pescar en Bariloche}, {n:precio-semana-trucha-patagonia-norte|una semana de trucha en la Patagonia norte}, {n:precio-pesca-patagonia-sur|la Patagonia sur}, {n:precio-viaje-dorado-lodge|un viaje de dorado en lodge} y {n:semana-de-pesca-en-lodge-argentina|cómo funciona una semana en un lodge}.',
	'faq_h'           => 'Preguntas frecuentes',
	'faq'             => [
		[ '¿Cuánto cuesta un día de pesca con guía en Argentina?', 'Depende del lugar y de la moneda. Cerca de Bariloche, de US$ [[bar_min]] a [[bar_max]] por bote para uno o dos pescadores (mediana [[bar_med]]; n = [[bar_n]] precios leídos el 31/10 y el 11/11/2026): de US$ [[bar_pp_min]] a [[bar_pp_max]] por pescador si van dos. En Esquina se publicó desde $ 480.000 por lancha (1/11/2026) y en El Calafate $ 720.000 por pescador por un día completo (5/11/2026), ambos en pesos.' ],
		[ '¿Cuánto cuesta una semana en un lodge de pesca en la Patagonia?', 'Por persona y por 7 noches, de US$ [[pn_min]] a [[pn_max]] en la Patagonia norte (n = [[pn_n]] lugares; leídos el 5 y el 10/11/2026) y de US$ [[ps_min]] a [[ps_max]] en la Patagonia sur (n = [[ps_n]] lugares; 10 y 13/11/2026). Por día de pesca son US$ [[pn_d_min]] a [[pn_d_max]] en la norte (n = [[pn_dn]]) y US$ [[ps_d_min]] a [[ps_d_max]] en la sur (n = [[ps_n]]). Qué incluye cambia de un lugar a otro.' ],
		[ '¿El precio es por persona o por lancha?', 'En los días con guía, por lancha o por bote: un precio para uno o dos pescadores (un lugar de El Calafate cobra por pescador). En los lodges y los paquetes, por persona. Para compararlos dividimos por los pescadores y los días de pesca publicados: US$ 550 por bote ÷ 2 = US$ 275 por pescador; con un solo pescador se paga el precio entero.' ],
	],
];

$N['en'] = [
	'titulo'          => 'How much guided fishing costs in Argentina: published prices as of November 2026',
	'slug'            => 'how-much-guided-fishing-costs-argentina-november-2026',
	'bajada'          => 'Ranges of the published prices we surveyed: a guided day, a week at a lodge and packages, per angler and per fishing day, with the date and source of each.',
	'seo_title'       => 'How much guided fishing costs in Argentina (Nov 2026)',
	'seo_description' => 'Published prices for guided fishing in Argentina, Nov 2026: a day near Bariloche US$ [[bar_min]]–[[bar_max]] per boat; a week at a lodge from US$ [[pn_min]]. Data and method.',
	'datos'           => [
		'Full day with a guide near Bariloche: US$ [[bar_min]] to [[bar_max]] per boat for 1 or 2 anglers, median [[bar_med]] (n = [[bar_n]] prices from [[bar_lug]] places; read on 31/10 and 11/11/2026).',
		'A 7-night week at a lodge, per person: US$ [[cor_min]] to [[cor_max]] in Corrientes (n = [[cor_n]]), [[pn_min]] to [[pn_max]] in northern Patagonia (n = [[pn_n]]) and [[ps_min]] to [[ps_max]] in southern Patagonia (n = [[ps_n]] places).',
		'Per angler per fishing day, a week at a lodge comes to between US$ [[pn_d_min]] and [[ps_d_max]] depending on the destination; a guided day in Bariloche, between US$ [[bar_pp_min]] and [[bar_pp_max]] with two in the boat.',
		'Guided days in the Litoral and El Calafate are published in pesos: from ARS 480,000 per boat in Esquina (1/11/2026) and ARS 720,000 per angler in El Calafate (5/11/2026).',
		'Only [[con_precio]] of the [[fichas]] place listings on Pescamapa publish a dated price.',
	],
	'f'               => 'Prices published by each place in Pescamapa listings, packages and price notes (read 31/10 to 13/11/2026)',
	'p0'              => '<strong>A guided fishing day near Bariloche costs US$ [[bar_min]] to [[bar_max]] per boat for one or two anglers</strong> (median [[bar_med]]; n = [[bar_n]] prices from [[bar_lug]] places). A 7-night week at a lodge costs, per person, US$ [[cor_min]] to [[cor_max]] in Corrientes (n = [[cor_n]]), [[pn_min]] to [[pn_max]] in northern Patagonia (n = [[pn_n]]) and [[ps_min]] to [[ps_max]] in southern Patagonia (n = [[ps_n]]). In the Litoral a guided day is published in pesos: from ARS 480,000 per boat in Esquina (n = 1, 1/11/2026).',
	'fecha'           => '<em>Prices read between 31 October and 13 November 2026, in the currency each place publishes them in. Prepared by Pescamapa. This is a count of the published prices we surveyed, not a survey and not a national average.</em>',
	'h1'              => 'The prices, by destination and format',
	'p1'              => 'Each row is a destination and a format. “n” is the number of published prices (of places, where it says “places”); the range and the median appear only with 3 or more. The last column divides by the anglers and fishing days each place publishes.',
	'cab'             => [ 'Destination and format', 'n', 'Published price (range)', 'Median', 'Per angler per fishing day', 'Read' ],
	'filas'           => [
		[ '<strong>Bariloche</strong>, full guided day by boat, per boat (1 or 2 anglers)', '[[bar_n]] prices, [[bar_lug]] places', 'US$ [[bar_min]]–[[bar_max]]', 'US$ [[bar_med]]', 'US$ [[bar_pp_min]]–[[bar_pp_max]] (two in the boat)', '31/10 and 11/11' ],
		[ '<strong>Esquina</strong>, boat day, “from”, per boat', '1', 'ARS 480,000', '—', 'does not say how many anglers', '1/11' ],
		[ '<strong>Bariloche</strong>, 9-hour day for up to 2 people, booked on a platform', '1', 'ARS 666,000', '—', 'ARS 333,000 (two people)', '5/11' ],
		[ '<strong>El Calafate</strong>, full day and half day, per angler', '1 and 1', 'ARS 720,000 and ARS 440,000', '—', 'ARS 720,000 (full day)', '5/11' ],
		[ '<strong>Bariloche</strong>, full day and half day, per group of up to 2, in euros', '1 and 1', '€ [[eur1]] and € [[eur2]]', '—', 'not calculated (euros)', '11/11' ],
		[ '<strong>Esquina</strong>, guesthouse, package of 2 to 4 fishing days, “from”, per person', '[[esq_n]]', 'US$ [[esq_min]]–[[esq_max]]', 'US$ [[esq_med]]', 'US$ [[esq_d_min]]–[[esq_d_max]]', '4 and 5/11' ],
		[ '<strong>Paraná Delta</strong>, boat-hotel of 3 and of 4 nights, “from”, per person', '2', 'US$ 1,260 and 1,680', '—', 'US$ 504 and 480', '5/11' ],
		[ '<strong>Lodges</strong>, packages of 3 to 6 nights (Corrientes, Neuquén and Santa Cruz), per person', '[[lp_n]] prices, [[lp_lug]] places', 'US$ [[lp_min]]–[[lp_max]]', 'US$ [[lp_med]]', 'US$ [[lp_d_min]]–[[lp_d_max]]', '5, 10 and 13/11' ],
		[ '<strong>Corrientes</strong>, week of 7 nights and 6 days, per person', '[[cor_n]] places', 'US$ [[cor_min]]–[[cor_max]]', 'US$ [[cor_med]]', 'US$ [[cor_d_min]]–[[cor_d_max]]', '13/11' ],
		[ '<strong>Northern Patagonia</strong> (Neuquén, Río Negro and Chubut), week of 7 nights, per person', '[[pn_n]] places', 'US$ [[pn_min]]–[[pn_max]]', 'US$ [[pn_med]]', 'US$ [[pn_d_min]]–[[pn_d_max]] (n = [[pn_dn]]: one place does not publish its fishing days)', '5 and 10/11' ],
		[ '<strong>Southern Patagonia</strong> (Santa Cruz and Tierra del Fuego), week of 7 nights, per person', '[[ps_n]] places', 'US$ [[ps_min]]–[[ps_max]]', 'US$ [[ps_med]]', 'US$ [[ps_d_min]]–[[ps_d_max]]', '10 and 13/11' ],
		[ '<strong>Paraná of Santa Fe</strong>, 4 fishing days, “from”, per person', '1', 'US$ 3,600', '—', 'US$ 900', '5/11' ],
	],
	'p1b'             => 'Range of the weeks: from the lowest to the highest published price (some places publish prices by date); median: of each place’s lowest price. In southern Patagonia and in Corrientes two places of the same group publish the same rate; each counts as one place. Pesos and euros are shown as published.',
	'cap1'            => 'US$ per angler per fishing day, by format, with n. Prices read between 31/10 and 13/11/2026. Source: prices published by each place. Prepared by Pescamapa.',
	'h2'              => 'Per day and per boat, or per week and per angler',
	'p2'              => 'Places do not publish in the same unit. A guide charges per boat for one or two anglers; a lodge, per person per week in a double room (unless it says single); a guesthouse, per person depending on the group in the boat. To compare them we divide by the anglers and fishing days the text itself publishes: US$ 550 per boat ÷ 2 anglers = US$ 275 per angler; US$ 9,990 for the week ÷ 6 fishing days = US$ 1,665 per day. With a single angler in the boat you pay the full price. What cannot be divided with certainty is left out of the calculation and appears in the CSV with the reason.',
	'h3'              => 'What each format usually includes',
	'p3'              => 'In the 7 weekly lodge listings with a price, lodging, meals and drinks appear in 7 of 7; transfers in 5 of 7, loaner gear in 4 of 7 and the fishing licence in 3 of 7. In the 5 guided-day listings in Bariloche, gear and lunch appear in 5 of 5, transfers and drinks in 4 of 5, and none says it includes the licence (four say it is extra and one does not say). That is why two prices are only comparable when they cover the same things: {n:paquete-incluye-no-incluye|what an Argentina fishing package includes} and {n:licencia-pesca-patagonia-precios|what the licence costs in Patagonia}.',
	'h4'              => 'Dollars and pesos',
	'p4'              => 'Of the [[filas]] published prices, [[usd]] are in dollars, [[ars]] in pesos, [[eur]] in euros and [[brl]] in reais. All the weeks and all the lodge packages are in dollars. The guided days in Esquina, El Calafate and on a Bariloche booking platform are in pesos; those of the Bariloche guides are in dollars, and two in euros. We do not convert between currencies: the peso moves and each place sets its own, so confirm the currency and the exchange rate when you book. One package in reais (R$ 1,800 per angler per day with two in the boat and R$ 1,600 with three, read on 10/11/2026) is in the CSV.',
	'h5'              => 'How many places publish their price, and how many do not',
	'p5'              => 'Of the [[fichas]] public place listings we had on 19/11/2026, [[con_precio]] have a price with a reading date ([[pct]]%) and [[sin_precio]] do not. By zone: Bariloche 5 of 9, southern Patagonia 4 of 13, Corrientes 4 of 24, Chubut and Neuquén 0 of 6, Entre Ríos and Buenos Aires 0 of 6. It is a count of our listings, not of all the places that exist. That is why the table has few places per destination.',
	'cap2'            => 'Place listings with a dated price, by zone. Pescamapa records as of 19/11/2026. Prepared by Pescamapa.',
	'h6'              => 'How we calculated it',
	'l6'              => [
		'<strong>Source:</strong> prices already published in Pescamapa listings, packages and price notes, each with its reading date (31/10 to 13/11/2026). They are [[filas]] prices from [[oferentes]] places and agencies: [[propia]] from the place’s own page or its group’s, [[programa]] from a programme sent to Pescamapa and [[listado]] from platform or agency listings.',
		'<strong>What counts:</strong> [[usables]] of the [[filas]] prices enter the comparison. The other [[fuera]] are left out because of currency (euros and reais), being half days, not saying how many anglers or fishing days they cover, or a possibly repeated place; the CSV gives the reason for each. Also left out are rates a place asked us not to publish and figures without a reading date.',
		'<strong>Calculation:</strong> price ÷ anglers ÷ published fishing days. The range runs from the lowest to the highest published price; the median only with 3 or more; for places with prices by date, the median uses the lowest.',
		'<strong>What it is not:</strong> it is not a survey and does not represent the market. It mixes “from” prices, different seasons (2026/27 and 2027) and double rooms unless it says single. The packages of an Esquina guesthouse were read on 4 and 5/11/2026; on 19/11 its homepage no longer showed rates. Other notes of ours cite “from US$ 500” for a day in Bariloche: it comes from a place we could not identify with certainty and is not counted twice here.',
		'<strong>No currency conversion:</strong> pesos, euros and reais are shown as published. Prices change: confirm them with the place before booking.',
	],
	'h7'              => 'Use the numbers and the charts',
	'p7'              => 'You can use these numbers and charts if you credit Pescamapa with a link to this note.',
	'p7b'             => '<a href="{csv}" download>Download the prices (CSV)</a>: one row per published price, with destination, format, amount and currency as published, unit, anglers covered, nights, fishing days, reading date, source type, the value per angler per fishing day with its formula and, if it is not in the comparison, the reason. Places appear with a code, not their name. Column names are in Spanish.',
	'p7c'             => 'Each price with its date is in the guides: {g:bariloche|Bariloche}, {g:esquina|Esquina}, {g:itati|Itatí}, {g:esteros-del-ibera|Esteros del Iberá}, {g:junin-de-los-andes|Junín de los Andes}, {g:esquel-trevelin|Esquel and Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos}, {g:rio-grande|Río Grande} and {g:el-calafate|El Calafate}. More context in {n:cuanto-cuesta-viaje-pesca-argentina|what a fishing trip in Argentina costs}, {n:precio-pesca-bariloche-limay|what fishing in Bariloche costs}, {n:precio-semana-trucha-patagonia-norte|a trout week in northern Patagonia}, {n:precio-pesca-patagonia-sur|southern Patagonia}, {n:precio-viaje-dorado-lodge|a dorado trip at a lodge} and {n:semana-de-pesca-en-lodge-argentina|how a week at a lodge works}.',
	'faq_h'           => 'Frequently asked questions',
	'faq'             => [
		[ 'How much does a day of guided fishing cost in Argentina?', 'It depends on the place and the currency. Near Bariloche, US$ [[bar_min]] to [[bar_max]] per boat for one or two anglers (median [[bar_med]]; n = [[bar_n]] prices read on 31/10 and 11/11/2026): US$ [[bar_pp_min]] to [[bar_pp_max]] per angler with two. In Esquina, from ARS 480,000 per boat was published (1/11/2026) and in El Calafate ARS 720,000 per angler for a full day (5/11/2026), both in pesos.' ],
		[ 'How much does a week at a fishing lodge in Patagonia cost?', 'Per person for 7 nights, US$ [[pn_min]] to [[pn_max]] in northern Patagonia (n = [[pn_n]] places; read on 5 and 10/11/2026) and US$ [[ps_min]] to [[ps_max]] in southern Patagonia (n = [[ps_n]] places; 10 and 13/11/2026). Per fishing day that is US$ [[pn_d_min]] to [[pn_d_max]] in the north (n = [[pn_dn]]) and US$ [[ps_d_min]] to [[ps_d_max]] in the south (n = [[ps_n]]). What is included changes from place to place.' ],
		[ 'Is the price per person or per boat?', 'For guided days, per boat: one price for one or two anglers (one place in El Calafate charges per angler). For lodges and packages, per person. To compare them we divide by the published anglers and fishing days: US$ 550 per boat ÷ 2 = US$ 275 per angler; with a single angler you pay the full price.' ],
	],
];

$N['pt'] = [
	'titulo'          => 'Quanto custa pescar com guia na Argentina: preços publicados em novembro de 2026',
	'slug'            => 'quanto-custa-pescar-com-guia-argentina-novembro-2026',
	'bajada'          => 'Faixas dos preços publicados que levantamos: dia com guia, semana em um lodge e pacotes, por pescador e por dia de pesca, com a data e a fonte de cada um.',
	'seo_title'       => 'Quanto custa pescar com guia na Argentina (nov. 2026)',
	'seo_description' => 'Preços publicados de pesca com guia na Argentina, nov. 2026: dia perto de Bariloche US$ [[bar_min]]–[[bar_max]] por barco; semana em lodge desde US$ [[pn_min]]. Dados e método.',
	'datos'           => [
		'Dia inteiro com guia perto de Bariloche: US$ [[bar_min]] a [[bar_max]] por barco para 1 ou 2 pescadores, mediana [[bar_med]] (n = [[bar_n]] preços de [[bar_lug]] lugares; lidos em 31/10 e 11/11/2026).',
		'Semana de 7 noites em um lodge, por pessoa: US$ [[cor_min]] a [[cor_max]] em Corrientes (n = [[cor_n]]), [[pn_min]] a [[pn_max]] na Patagônia norte (n = [[pn_n]]) e [[ps_min]] a [[ps_max]] na Patagônia sul (n = [[ps_n]] lugares).',
		'Por pescador e por dia de pesca, a semana em um lodge fica entre US$ [[pn_d_min]] e [[ps_d_max]] conforme o destino; o dia com guia em Bariloche, entre US$ [[bar_pp_min]] e [[bar_pp_max]] com dois no barco.',
		'Os dias com guia do Litoral e de El Calafate são publicados em pesos: a partir de ARS 480.000 por barco em Esquina (1/11/2026) e ARS 720.000 por pescador em El Calafate (5/11/2026).',
		'Só [[con_precio]] das [[fichas]] fichas de lugares da Pescamapa publicam um preço com data.',
	],
	'f'               => 'Preços publicados por cada lugar em fichas, pacotes e notas de preços da Pescamapa (lidos de 31/10 a 13/11/2026)',
	'p0'              => '<strong>Um dia de pesca com guia perto de Bariloche custa de US$ [[bar_min]] a [[bar_max]] por barco para um ou dois pescadores</strong> (mediana [[bar_med]]; n = [[bar_n]] preços de [[bar_lug]] lugares). Uma semana de 7 noites em um lodge custa, por pessoa, de US$ [[cor_min]] a [[cor_max]] em Corrientes (n = [[cor_n]]), de [[pn_min]] a [[pn_max]] na Patagônia norte (n = [[pn_n]]) e de [[ps_min]] a [[ps_max]] na Patagônia sul (n = [[ps_n]]). No Litoral o dia com guia é publicado em pesos: a partir de ARS 480.000 por barco em Esquina (n = 1, 1/11/2026).',
	'fecha'           => '<em>Preços lidos entre 31 de outubro e 13 de novembro de 2026, na moeda em que cada lugar os publica. Elaboração: Pescamapa. É uma contagem dos preços publicados que levantamos, não uma pesquisa nem a média do país.</em>',
	'h1'              => 'Os preços, por destino e formato',
	'p1'              => 'Cada linha é um destino e um formato. «n» é a quantidade de preços publicados (de lugares, onde diz «lugares»); a faixa e a mediana aparecem só com 3 ou mais. A última coluna divide pelos pescadores e pelos dias de pesca que cada lugar publica.',
	'cab'             => [ 'Destino e formato', 'n', 'Preço publicado (faixa)', 'Mediana', 'Por pescador e por dia de pesca', 'Lido' ],
	'filas'           => [
		[ '<strong>Bariloche</strong>, dia inteiro com guia em barco, por barco (1 ou 2 pescadores)', '[[bar_n]] preços, [[bar_lug]] lugares', 'US$ [[bar_min]]–[[bar_max]]', 'US$ [[bar_med]]', 'US$ [[bar_pp_min]]–[[bar_pp_max]] (dois no barco)', '31/10 e 11/11' ],
		[ '<strong>Esquina</strong>, jornada em barco, «a partir de», por barco', '1', 'ARS 480.000', '—', 'não diz quantos pescadores', '1/11' ],
		[ '<strong>Bariloche</strong>, dia de 9 horas para até 2 pessoas, reserva em uma plataforma', '1', 'ARS 666.000', '—', 'ARS 333.000 (duas pessoas)', '5/11' ],
		[ '<strong>El Calafate</strong>, dia inteiro e meio dia, por pescador', '1 e 1', 'ARS 720.000 e ARS 440.000', '—', 'ARS 720.000 (dia inteiro)', '5/11' ],
		[ '<strong>Bariloche</strong>, dia inteiro e meio dia, por grupo de até 2, em euros', '1 e 1', '€ [[eur1]] e € [[eur2]]', '—', 'não se calcula (euros)', '11/11' ],
		[ '<strong>Esquina</strong>, pousada, pacote de 2 a 4 dias de pesca, «a partir de», por pessoa', '[[esq_n]]', 'US$ [[esq_min]]–[[esq_max]]', 'US$ [[esq_med]]', 'US$ [[esq_d_min]]–[[esq_d_max]]', '4 e 5/11' ],
		[ '<strong>Delta do Paraná</strong>, barco-hotel de 3 e de 4 noites, «a partir de», por pessoa', '2', 'US$ 1.260 e 1.680', '—', 'US$ 504 e 480', '5/11' ],
		[ '<strong>Lodges</strong>, pacotes de 3 a 6 noites (Corrientes, Neuquén e Santa Cruz), por pessoa', '[[lp_n]] preços, [[lp_lug]] lugares', 'US$ [[lp_min]]–[[lp_max]]', 'US$ [[lp_med]]', 'US$ [[lp_d_min]]–[[lp_d_max]]', '5, 10 e 13/11' ],
		[ '<strong>Corrientes</strong>, semana de 7 noites e 6 dias, por pessoa', '[[cor_n]] lugares', 'US$ [[cor_min]]–[[cor_max]]', 'US$ [[cor_med]]', 'US$ [[cor_d_min]]–[[cor_d_max]]', '13/11' ],
		[ '<strong>Patagônia norte</strong> (Neuquén, Río Negro e Chubut), semana de 7 noites, por pessoa', '[[pn_n]] lugares', 'US$ [[pn_min]]–[[pn_max]]', 'US$ [[pn_med]]', 'US$ [[pn_d_min]]–[[pn_d_max]] (n = [[pn_dn]]: um lugar não publica os dias de pesca)', '5 e 10/11' ],
		[ '<strong>Patagônia sul</strong> (Santa Cruz e Terra do Fogo), semana de 7 noites, por pessoa', '[[ps_n]] lugares', 'US$ [[ps_min]]–[[ps_max]]', 'US$ [[ps_med]]', 'US$ [[ps_d_min]]–[[ps_d_max]]', '10 e 13/11' ],
		[ '<strong>Paraná de Santa Fe</strong>, 4 dias de pesca, «a partir de», por pessoa', '1', 'US$ 3.600', '—', 'US$ 900', '5/11' ],
	],
	'p1b'             => 'Faixa das semanas: do preço mais baixo ao mais alto publicado (alguns lugares publicam preços por data); mediana: dos preços mais baixos de cada lugar. Na Patagônia sul e em Corrientes há dois lugares do mesmo grupo com a mesma tarifa; cada um conta como um lugar. Os pesos e os euros aparecem como são publicados.',
	'cap1'            => 'US$ por pescador e por dia de pesca, por formato, com n. Preços lidos entre 31/10 e 13/11/2026. Fonte: preços publicados por cada lugar. Elaboração: Pescamapa.',
	'h2'              => 'Por dia e por barco, ou por semana e por pescador',
	'p2'              => 'Os lugares não publicam na mesma unidade. Um guia cobra por barco para um ou dois pescadores; um lodge, por pessoa e por semana em quarto duplo (salvo onde se diz que é individual); uma pousada, por pessoa conforme o grupo no barco. Para compará-los dividimos pelos pescadores e pelos dias de pesca que o próprio texto publica: US$ 550 por barco ÷ 2 pescadores = US$ 275 por pescador; US$ 9.990 a semana ÷ 6 dias de pesca = US$ 1.665 por dia. Se vai um só pescador no barco, paga o preço inteiro. O que não pode ser dividido com certeza fica fora do cálculo e aparece no CSV com o motivo.',
	'h3'              => 'O que cada formato costuma incluir',
	'p3'              => 'Nas 7 fichas de lodge por semana com preço, hospedagem, refeições e bebidas aparecem em 7 de 7; os traslados em 5 de 7, o equipamento emprestado em 4 de 7 e a licença de pesca em 3 de 7. Nas 5 fichas de dia com guia em Bariloche, o equipamento e o almoço aparecem em 5 de 5, os traslados e as bebidas em 4 de 5, e nenhuma diz incluir a licença (quatro dizem que é à parte e uma não esclarece). Por isso dois preços só se comparam se cobrem o mesmo: {n:paquete-incluye-no-incluye|o que inclui um pacote de pesca na Argentina} e {n:licencia-pesca-patagonia-precios|quanto custa a licença na Patagônia}.',
	'h4'              => 'Dólares e pesos',
	'p4'              => 'Dos [[filas]] preços publicados, [[usd]] estão em dólares, [[ars]] em pesos, [[eur]] em euros e [[brl]] em reais. Todas as semanas e todos os pacotes de lodge estão em dólares. Os dias com guia de Esquina, de El Calafate e de uma plataforma de reservas de Bariloche estão em pesos; os dos guias de Bariloche, em dólares, e dois em euros. Não convertemos entre moedas: o peso se mexe e cada lugar fixa o seu, então confirme a moeda e a taxa de câmbio ao reservar. Um pacote em reais (R$ 1.800 por pescador e por dia com dois no barco e R$ 1.600 com três, lido em 10/11/2026) está no CSV.',
	'h5'              => 'Quantos lugares publicam o preço e quantos não',
	'p5'              => 'Das [[fichas]] fichas públicas de lugares que registramos em 19/11/2026, [[con_precio]] têm um preço com data de leitura ([[pct]] %) e [[sin_precio]] não. Por zona: Bariloche 5 de 9, Patagônia sul 4 de 13, Corrientes 4 de 24, Chubut e Neuquén 0 de 6, Entre Ríos e Buenos Aires 0 de 6. É uma contagem das nossas fichas, não do total de lugares que existem. Por isso a tabela tem poucos lugares por destino.',
	'cap2'            => 'Fichas de lugares com preço datado, por zona. Registro da Pescamapa em 19/11/2026. Elaboração: Pescamapa.',
	'h6'              => 'Como calculamos',
	'l6'              => [
		'<strong>Fonte:</strong> preços já publicados em fichas, pacotes e notas de preços da Pescamapa, cada um com sua data de leitura (de 31/10 a 13/11/2026). São [[filas]] preços de [[oferentes]] lugares e agências: [[propia]] da página do próprio lugar ou do seu grupo, [[programa]] de um programa enviado à Pescamapa e [[listado]] de listagens de plataformas ou agências.',
		'<strong>O que entra:</strong> [[usables]] dos [[filas]] preços entram na comparação. Os outros [[fuera]] ficam de fora por moeda (euros e reais), por serem meio dia, por não esclarecer quantos pescadores ou quantos dias de pesca cobrem, ou por um possível lugar repetido; o CSV dá o motivo de cada um. Ficam de fora também as tarifas que um lugar pediu para não publicar e os valores sem data de leitura.',
		'<strong>Conta:</strong> preço ÷ pescadores ÷ dias de pesca publicados. A faixa vai do preço mais baixo ao mais alto publicado; a mediana, só com 3 ou mais; nos lugares com preços por data, a mediana usa o mais baixo.',
		'<strong>O que não é:</strong> não é uma pesquisa nem representa o mercado. Mistura preços «a partir de», temporadas diferentes (2026/27 e 2027) e quarto duplo, salvo onde se diz individual. Os pacotes de uma pousada de Esquina foram lidos em 4 e 5/11/2026; em 19/11 a página inicial dela já não mostrava tarifas. Outras notas nossas citam «a partir de US$ 500» por um dia em Bariloche: vem de um lugar que não conseguimos identificar com certeza e aqui não é contado duas vezes.',
		'<strong>Sem conversão de moedas:</strong> os pesos, os euros e os reais aparecem como são publicados. Os preços mudam: confirme-os com o lugar antes de reservar.',
	],
	'h7'              => 'Use os números e os gráficos',
	'p7'              => 'Você pode usar estes números e gráficos citando a Pescamapa com link para esta nota.',
	'p7b'             => '<a href="{csv}" download>Baixe os preços (CSV)</a>: uma linha por preço publicado, com destino, formato, valor e moeda como publicados, unidade, pescadores cobertos, noites, dias de pesca, data de leitura, tipo de fonte, o valor por pescador e por dia de pesca com sua fórmula e, se não entra na comparação, o motivo. Os lugares aparecem com um código, não com o nome. Os nomes das colunas estão em espanhol.',
	'p7c'             => 'Cada preço com a sua data está nos guias: {g:bariloche|Bariloche}, {g:esquina|Esquina}, {g:itati|Itatí}, {g:esteros-del-ibera|Esteros del Iberá}, {g:junin-de-los-andes|Junín de los Andes}, {g:esquel-trevelin|Esquel e Trevelin}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|rio Gallegos}, {g:rio-grande|Río Grande} e {g:el-calafate|El Calafate}. Mais contexto em {n:cuanto-cuesta-viaje-pesca-argentina|quanto custa uma viagem de pesca na Argentina}, {n:precio-pesca-bariloche-limay|quanto custa pescar em Bariloche}, {n:precio-semana-trucha-patagonia-norte|uma semana de truta na Patagônia norte}, {n:precio-pesca-patagonia-sur|a Patagônia sul}, {n:precio-viaje-dorado-lodge|uma viagem de dourado em lodge} e {n:semana-de-pesca-en-lodge-argentina|como funciona uma semana em um lodge}.',
	'faq_h'           => 'Perguntas frequentes',
	'faq'             => [
		[ 'Quanto custa um dia de pesca com guia na Argentina?', 'Depende do lugar e da moeda. Perto de Bariloche, de US$ [[bar_min]] a [[bar_max]] por barco para um ou dois pescadores (mediana [[bar_med]]; n = [[bar_n]] preços lidos em 31/10 e 11/11/2026): de US$ [[bar_pp_min]] a [[bar_pp_max]] por pescador com dois. Em Esquina foi publicado a partir de ARS 480.000 por barco (1/11/2026) e em El Calafate ARS 720.000 por pescador por um dia inteiro (5/11/2026), ambos em pesos.' ],
		[ 'Quanto custa uma semana em um lodge de pesca na Patagônia?', 'Por pessoa e por 7 noites, de US$ [[pn_min]] a [[pn_max]] na Patagônia norte (n = [[pn_n]] lugares; lidos em 5 e 10/11/2026) e de US$ [[ps_min]] a [[ps_max]] na Patagônia sul (n = [[ps_n]] lugares; 10 e 13/11/2026). Por dia de pesca são US$ [[pn_d_min]] a [[pn_d_max]] no norte (n = [[pn_dn]]) e US$ [[ps_d_min]] a [[ps_d_max]] no sul (n = [[ps_n]]). O que está incluído muda de um lugar para outro.' ],
		[ 'O preço é por pessoa ou por barco?', 'Nos dias com guia, por barco: um preço para um ou dois pescadores (um lugar de El Calafate cobra por pescador). Nos lodges e nos pacotes, por pessoa. Para compará-los dividimos pelos pescadores e pelos dias de pesca publicados: US$ 550 por barco ÷ 2 = US$ 275 por pescador; com um só pescador paga-se o preço inteiro.' ],
	],
];


// ---- Attachments (own work: credit "Pescamapa") ----
$adj = [];
$subir = function ( string $archivo, string $titulo, ?array $alt ) use ( $aplicar, $carpeta, $hoy ) {
	$ruta = "$carpeta/$archivo";
	is_file( $ruta ) || WP_CLI::error( "falta $archivo en $carpeta" );
	$ya  = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $archivo, 'numberposts' => 1, 'fields' => 'ids' ] );
	$att = $ya ? (int) $ya[0] : 0;
	$dim = @getimagesize( $ruta );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": adjunto $archivo " . ( $dim ? "{$dim[0]}x{$dim[1]} " : '' ) . size_format( filesize( $ruta ) ) . ( $att ? " (ya existe #$att)" : ' (nuevo)' ) );
	if ( ! $aplicar || $att ) {
		return $att;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $archivo );
	copy( $ruta, $tmp );
	$att = media_handle_sideload( [ 'name' => $archivo, 'tmp_name' => $tmp ], 0, $titulo );
	if ( is_wp_error( $att ) ) {
		@unlink( $tmp );
		WP_CLI::error( "$archivo: " . $att->get_error_message() );
	}
	update_post_meta( $att, '_pm_archivo_origen', $archivo );
	if ( $alt ) {
		update_post_meta( $att, '_wp_attachment_image_alt', $alt['es'] );
		foreach ( [ 'es', 'en', 'pt' ] as $i ) {
			update_post_meta( $att, "_pm_alt_$i", $alt[ $i ] );
		}
	}
	update_field( 'foto_autor', 'Pescamapa', $att );
	update_field( 'foto_origen', 'propia', $att );
	wp_update_post( [ 'ID' => $att, 'post_content' => "Elaboración propia de Pescamapa ($hoy), hecha con wordpress/build/precios-2026-11/ a partir de los precios ya publicados en el sitio (lecturas del 31/10 al 13/11/2026)." ] );
	return (int) $att;
};
foreach ( $A as $k => $a ) {
	$adj[ $k ] = $subir( $a['archivo'], $a['titulo'], $a['alt'] );
}
$adj['csv'] = $subir( $CSV, 'Precios publicados de pesca con guía en Argentina, noviembre de 2026 (CSV)', null );
$url_de  = fn( int $id ) => $id ? (string) wp_get_attachment_url( $id ) : '';
$url_img = fn( int $id ) => $id ? ( (string) wp_get_attachment_image_url( $id, 'large' ) ?: $url_de( $id ) ) : '';

// ---- Link resolver: {n:clave|texto} (note), {g:clave|texto} (guide); falls back to Spanish ----
$res = function ( string $html, string $i ) use ( $adj, $url_de ): string {
	$nota = function ( string $c ) use ( $i ): string {
		$por = [];
		foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $c, 'numberposts' => -1, 'fields' => 'ids' ] ) as $id ) {
			$por[ pm_idioma_de( (int) $id ) ] = (int) $id;
		}
		$id = $por[ $i ] ?? ( $por['es'] ?? 0 );
		$id || WP_CLI::error( "No encuentro la nota {$c} (ni en español)." );
		isset( $por[ $i ] ) || WP_CLI::warning( "nota {$c} no existe en {$i}: el enlace va a la versión en español" );
		return get_permalink( $id );
	};
	$guia = function ( string $c ) use ( $i ): string {
		$g = pm_buscar( 'pm_destino', $c, $i ) ?? pm_buscar( 'pm_destino', $c, 'es' );
		$g || WP_CLI::error( "No encuentro la guía {$c}." );
		pm_idioma_de( $g->ID ) === $i || WP_CLI::warning( "guía {$c} no existe en {$i}: el enlace va a la versión en español" );
		return get_permalink( $g->ID );
	};
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( $nota( $m[1] ) ) . '">' . $m[2] . '</a>', $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( $guia( $m[1] ) ) . '">' . $m[2] . '</a>', $html );
	return str_replace( '{csv}', esc_url( $url_de( $adj['csv'] ) ?: '#csv-pendiente-en-simulacro' ), $html );
};

// ---- The notes ----
$existe = fn( string $i ) => array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i );
$cambios = 0;
$es_id   = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n  = $N[ $i ];
	$S  = fn( string $t ) => $res( $sub( $t, $i ), $i );
	if ( $ya = $existe( $i ) ) {
		WP_CLI::log( "$clave ($i) #" . array_values( $ya )[0]->ID . ' ya existe: no toco nada.' );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $S( $n['seo_description'] ) );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	$fig = fn( string $k, string $cap ) => pm_pg_figura( $adj[ $k ], $url_img( $adj[ $k ] ) ?: '#imagen-pendiente-en-simulacro', $A[ $k ]['alt'][ $i ], esc_html( $S( $cap ) ) );
	$cuerpo = pm_pg_p( $S( $n['p0'] ) ) . pm_pg_p( $S( $n['fecha'] ) )
		. pm_pg_h2( $n['h1'] ) . pm_pg_p( $S( $n['p1'] ) ) . pm_pg_tabla( $n['cab'], array_map( fn( $f ) => array_map( $S, $f ), $n['filas'] ) ) . pm_pg_p( $S( $n['p1b'] ) ) . $fig( 'dia', $n['cap1'] )
		. pm_pg_h2( $n['h2'] ) . pm_pg_p( $S( $n['p2'] ) )
		. pm_pg_h2( $n['h3'] ) . pm_pg_p( $S( $n['p3'] ) )
		. pm_pg_h2( $n['h4'] ) . pm_pg_p( $S( $n['p4'] ) )
		. pm_pg_h2( $n['h5'] ) . pm_pg_p( $S( $n['p5'] ) ) . $fig( 'fichas', $n['cap2'] )
		. pm_pg_h2( $n['h6'] ) . pm_pg_lista( array_map( $S, $n['l6'] ) )
		. pm_pg_h2( $n['h7'] ) . pm_pg_p( $S( $n['p7'] ) ) . pm_pg_p( $S( $n['p7b'] ) ) . pm_pg_p( $S( $n['p7c'] ) )
		. pm_pg_h2( $n['faq_h'] ) . pm_pg_faq( array_map( fn( $q ) => [ $q[0], $S( $q[1] ) ], $n['faq'] ) );
	( ! preg_match( '/\{[a-z0-9:|-]+|\[\[/', $cuerpo, $m ) ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	foreach ( [ 'operador', 'operator', 'operadora', 'vosotros', 'TripAdvisor', 'Viator', 'Hooked' ] as $mala ) {
		false === stripos( $cuerpo, $mala ) || WP_CLI::error( "$i: palabra no permitida «$mala»" );
	}
	$datos  = array_map( fn( $d ) => [ 'dato' => $S( $d ) ], $n['datos'] );
	$fuentes = [ [ 'nombre' => $n['f'], 'url' => pm_url_notas( $i ), 'oficial' => 0, 'consultada' => $leido ] ];
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $datos ) . ' datos, ' . count( $n['faq'] ) . " FAQ; title $tl / description $dl; portada #{$adj['dia']}" );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $S( $n['bajada'] ), 'nota_datos' => $datos, 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $S( $n['seo_description'] ) ];
	if ( 'es' === $i ) {
		// national piece: no nota_destino; informative: no commercial calls (vistas/nota.php)
		$campos += [ 'nota_tipo' => 'datos', 'nota_fecha' => $hoy, 'nota_informativa' => 1, 'nota_origen' => $N['es']['orig'] ];
		$es_id = $id;
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		set_post_thumbnail( $id, $adj['dia'] );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_post( $id )->post_name === $n['slug'] && count( pm_filas( 'fuentes', $id ) ) === 1 && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] )
		&& count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && false !== strpos( $cont, 'wp-image-' . $adj['dia'] ) && false !== strpos( $cont, 'wp-image-' . $adj['fichas'] )
		&& false !== strpos( $cont, esc_url( $url_de( $adj['csv'] ) ) ) && false !== strpos( $cont, $num( $D['pn_med'], $i ) ) && false !== strpos( $cont, 'class="wp-block-table"' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
if ( $aplicar ) {
	$nota_es = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $x ) => 'es' === pm_idioma_de( $x->ID ) ) )[0] ?? null;
	$e = $nota_es ? pm_nota( $nota_es->ID ) : null;
	WP_CLI::log( ( $e && '' === (string) $e['destino'] && 'datos' === $e['tipo'] && $e['informativa'] && $e['foto'] === $adj['dia'] ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee destino «' . ( $e['destino'] ?? '?' ) . '», tipo ' . ( $e['tipo'] ?? '?' ) . ', informativa ' . ( ! empty( $e['informativa'] ) ? 'sí' : 'NO' ) . ', fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
	flush_rewrite_rules( false );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
