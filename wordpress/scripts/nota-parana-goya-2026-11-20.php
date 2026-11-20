<?php
/**
 * Dated editorial note "El Paraná en Goya: la altura más alta para un 20 de noviembre desde 1990" (clave parana-goya-20-noviembre-2026,
 * es + en + pt), 20/11/2026. Content piece (not an auto-updating page): every number comes from wordpress/build/rio-nota-goya/resumen.json
 * and datos-goya.csv (output of rio-historia.py, branch rio-historia, reading of 20/11/2026; the infographics are generated from the same
 * two files by wordpress/build/rio-nota-graficos.py). Nothing is typed from memory; no forecast, no fishing claim (the "what it means for
 * fishing" section only links the live river page and the Goya guide). Type "datos", destination goya (the Goya guide and the Corrientes
 * province page list it by nota_destino, and the CTA of the note preselects Goya). Cover = infographic 1 (4:3, so the crop keeps it whole);
 * infographics 2 and 3 and the CSV download sit in the body.
 * Files (wordpress/fotos/, copied to /tmp/pm-fotos/ by wp-script.yml): parana-goya-2026-el-ano.png, parana-goya-20-de-noviembre.png,
 * parana-crecida-rio-abajo.png, parana-goya-historico-20-noviembre.csv. Attachments are reused by _pm_archivo_origen; credit "Pescamapa", own work.
 * Idempotent: an existing note (same clave and language) is not touched. Dry run first ($aplicar = false).
 * Run with wp-script.yml (script = nota-parana-goya-2026-11-20).
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
$clave   = 'parana-goya-20-noviembre-2026';
$hoy     = '20261120';
$carpeta = '/tmp/pm-fotos';
$INA     = 'https://alerta.ina.gob.ar/';

// ---- The attachments: [ key => file, title, alt per language ] ----
$A = [
	'ano' => [
		'archivo' => 'parana-goya-2026-el-ano.png', 'titulo' => 'Paraná en Goya: 2026 contra 1990–2025',
		'alt' => [
			'es' => 'Gráfico de líneas de la altura del río Paraná en Goya durante 2026 contra la banda habitual de 1990 a 2025: la línea de 2026 queda baja hasta agosto, sube desde septiembre y el 20 de noviembre llega a 5,68 m, sobre toda la banda; también se ven las líneas de 2021 (año más bajo) y 1998 (año más alto)',
			'en' => 'Line chart of the Paraná River level at Goya during 2026 against the usual band of 1990 to 2025: the 2026 line stays low until August, rises from September and reaches 5.68 m on 20 November, above the whole band; the lines for 2021 (lowest year) and 1998 (highest year) are also drawn',
			'pt' => 'Gráfico de linhas do nível do rio Paraná em Goya durante 2026 contra a faixa habitual de 1990 a 2025: a linha de 2026 fica baixa até agosto, sobe desde setembro e em 20 de novembro chega a 5,68 m, acima de toda a faixa; também aparecem as linhas de 2021 (ano mais baixo) e 1998 (ano mais alto)',
		],
	],
	'dia' => [
		'archivo' => 'parana-goya-20-de-noviembre.png', 'titulo' => 'El 20 de noviembre en Goya: 2026 contra 36 años',
		'alt' => [
			'es' => 'Barras horizontales con la altura del Paraná en Goya cada 20 de noviembre de 1990 a 2025: mínimo 1,20 m, máximo 5,63 m, percentiles 10 a 90 de 2,33 a 5,06 m, mediana 3,93 m; una línea vertical marca 2026 con 5,68 m, a la derecha de todas las barras',
			'en' => 'Horizontal bars with the Paraná level at Goya on every 20 November from 1990 to 2025: minimum 1.20 m, maximum 5.63 m, 10th to 90th percentiles from 2.33 to 5.06 m, median 3.93 m; a vertical line marks 2026 at 5.68 m, to the right of every bar',
			'pt' => 'Barras horizontais com o nível do Paraná em Goya em cada 20 de novembro de 1990 a 2025: mínimo 1,20 m, máximo 5,63 m, percentis 10 a 90 de 2,33 a 5,06 m, mediana 3,93 m; uma linha vertical marca 2026 com 5,68 m, à direita de todas as barras',
		],
	],
	'rio' => [
		'archivo' => 'parana-crecida-rio-abajo.png', 'titulo' => 'La crecida viaja río abajo: cambio en 30 días por estación',
		'alt' => [
			'es' => 'Barras con el cambio de la altura del Paraná en 30 días, de norte a sur: Paso de la Patria baja 1,77 m, Corrientes baja 1,43 m, Goya sube 0,17 m, Esquina sube 0,69 m, La Paz sube 0,88 m y Santa Fe sube 2,03 m; las que bajan van rayadas y a la izquierda del cero',
			'en' => 'Bars with the 30-day change of the Paraná level from north to south: Paso de la Patria falls 1.77 m, Corrientes falls 1.43 m, Goya rises 0.17 m, Esquina rises 0.69 m, La Paz rises 0.88 m and Santa Fe rises 2.03 m; the ones that fall are striped and left of zero',
			'pt' => 'Barras com a variação do nível do Paraná em 30 dias, de norte a sul: Paso de la Patria baixa 1,77 m, Corrientes baixa 1,43 m, Goya sobe 0,17 m, Esquina sobe 0,69 m, La Paz sobe 0,88 m e Santa Fe sobe 2,03 m; as que baixam são listradas e ficam à esquerda do zero',
		],
	],
];
$CSV = 'parana-goya-historico-20-noviembre.csv';

$N = [];
$N['es'] = [
	'titulo' => 'El Paraná en Goya: la altura más alta para un 20 de noviembre desde 1990',
	'slug'   => 'parana-goya-20-de-noviembre-2026',
	'bajada' => 'El 20 de noviembre de 2026 el río Paraná midió 5,68 m en Goya, más que en cualquier otro 20 de noviembre de la serie. Gráficos, datos para descargar y cómo lo calculamos.',
	'seo_title' => 'Paraná en Goya: la mayor altura de un 20 de noviembre',
	'seo_description' => 'El Paraná mide 5,68 m en Goya el 20/11/2026: más que cualquier otro 20 de noviembre desde 1990 (lo habitual: 3,93 m). Gráficos, datos y método.',
	'datos' => [
		'5,68 m el 20 de noviembre de 2026: la altura más alta para esa fecha de la serie 1990–2026 (el máximo anterior era 5,63 m, en 1997).',
		'Lo habitual para un 20 de noviembre (mediana de 36 años): 3,93 m. La lectura está 1,75 m por encima.',
		'Subió 2,67 m en 90 días; en los últimos 30 días, +0,17 m.',
		'El mismo día de 2021 (el año de menor altura media) midió 1,45 m; el de 1998 (el de mayor), 4,08 m.',
		'Récords de la serie en Goya: 0,47 m (24/1/2022) y 7,20 m (13/6/1992).',
		'Es altura de escala: no es profundidad ni se compara con la de otra estación.',
	],
	'p0' => '<strong>En Goya, el río Paraná mide hoy 5,68 m: más que en cualquier otro 20 de noviembre desde 1990.</strong> Lo habitual para esta fecha es 3,93 m.',
	'fecha' => '<em>Lectura del 20 de noviembre de 2026. Datos: {ina|Instituto Nacional del Agua} (escalas de Prefectura Naval Argentina). Elaboración: Pescamapa.</em>',
	'h1' => 'Subió casi tres metros en tres meses',
	'p1' => 'Hace 90 días el Paraná en Goya estaba 2,67 m más abajo que hoy. En los últimos 30 días casi no cambió: +0,17 m. Ninguno de los 36 años de 1990 a 2025 llegó a esa altura un 20 de noviembre: el más alto fue 5,63 m, en 1997, y el más bajo 1,20 m, en 2020.',
	'cap2' => 'Cada 20 de noviembre de 1990 a 2025 contra 2026. Datos: INA. Elaboración: Pescamapa.',
	'h2' => 'La crecida está bajando por el río',
	'p2' => 'No es solo Goya: Esquina, La Paz y Santa Fe también están en su altura más alta para un 20 de noviembre desde 1990. Pero el cambio de los últimos 30 días muestra hacia dónde se mueve el agua. Río arriba ya baja: Paso de la Patria −1,77 m y Corrientes −1,43 m. Río abajo sigue subiendo: La Paz +0,88 m y Santa Fe +2,03 m. Goya, en el medio, casi no cambió (+0,17 m). Es el patrón de una crecida que avanza río abajo. En 90 días, las seis estaciones del tramo, de Paso de la Patria a Santa Fe, subieron entre 2,67 y 2,81 m.',
	'cap3' => 'Cambio de la altura en 30 días, de norte a sur. Datos: INA. Elaboración: Pescamapa.',
	'h3' => 'Un año de dos mitades',
	'p3' => 'Hasta mitad de año, 2026 estuvo bajo: del 1 de enero al 20 de noviembre la altura media en Goya fue 3,28 m, el 8.º puesto entre los 37 años de la serie ordenados de menor a mayor (los tres más bajos del mismo tramo: 2021, con 1,76 m; 2020, con 1,98 m; y 2022, con 2,18 m). Después el río subió. Para medir el contraste: el 20 de noviembre de 2021, el año de menor altura media de la referencia (1,72 m), Goya midió 1,45 m; en 1998, el de mayor (5,22 m), 4,08 m. Este año, 5,68 m.',
	'h4' => 'Qué significa para quien va a pescar a Goya',
	'p4' => 'Antes de salir, mirá {r|la altura del río en Goya hoy}, que se actualiza todos los días, y {g|la guía de Goya}, con la sección sobre cómo cambia la pesca con la altura del río. La altura de escala mide el nivel en ese punto, no la profundidad.',
	'h5' => 'Cómo lo calculamos',
	'l5' => [
		'<strong>Fuente:</strong> la altura de la escala de Goya que publica el {ina|Instituto Nacional del Agua} (INA), de las escalas de Prefectura Naval Argentina. Las lecturas de 2026 son del INA y no están validadas.',
		'<strong>Un valor por día:</strong> el promedio de las lecturas del día (hora local), al centímetro. Se descartan los valores imposibles y los ceros de escala que son huecos de datos.',
		'<strong>Referencia:</strong> 1990–2025. Cada día se compara con el mismo día del calendario: para el 20 de noviembre hay 36 años con dato.',
		'<strong>Percentil:</strong> la parte de esos años que estuvo por debajo de la lectura. Para el 20 de noviembre de 2026 son los 36: percentil 100. Las bandas del primer gráfico son los percentiles 10 a 90 (clara) y 25 a 75 (oscura) de cada día, suavizados con un promedio de 15 días.',
		'<strong>Qué no es:</strong> es altura de escala, no profundidad, y cada estación tiene su propio cero: se comparan los cambios, no las alturas entre estaciones. No es un pronóstico.',
	],
	'h6' => 'Usá los gráficos y los datos',
	'p6' => 'Podés usar estos gráficos y números citando a Pescamapa con enlace a esta nota.',
	'p6b' => '<a href="{csv}" download>Descargá los datos de Goya (CSV)</a>: una fila por día del calendario, con los años con dato, el mínimo, los percentiles 10, 25, 50, 75 y 90 y el máximo de 1990 a 2025, el valor de ese día en 2021 y en 1998 y el de 2026 hasta el 20 de noviembre, todo en metros.',
	'faq_h' => 'Preguntas frecuentes',
	'faq' => [
		[ '¿Cuánto mide el Paraná en Goya hoy?', 'El 20 de noviembre de 2026 midió 5,68 m en la escala de Goya (INA). La altura de hoy se actualiza todos los días en {r|Altura del río en Goya}.' ],
		[ '¿Es la mayor crecida?', 'No. Es la mayor altura para un 20 de noviembre desde 1990, pero el récord de la serie en Goya es 7,20 m, el 13 de junio de 1992.' ],
		[ '¿De dónde salen los datos?', 'De las alturas diarias de la escala de Goya del {ina|Instituto Nacional del Agua}, de 1990 a 2026. Pescamapa calculó la comparación con el mismo día de cada año; está explicada arriba, en «Cómo lo calculamos».' ],
	],
	'f' => 'Instituto Nacional del Agua (INA): alturas diarias de las escalas de Prefectura Naval Argentina, 1990–2026',
	'orig' => 'Lectura del 20/11/2026 con wordpress/build/rio-historia.py (workflow River history, rama rio-historia): resumen.json y datos-goya.csv, copiados en wordpress/build/rio-nota-goya/. Gráficos hechos por wordpress/build/rio-nota-graficos.py desde esos dos archivos. No se afirma pronóstico, profundidad, comparación entre estaciones ni efecto sobre la pesca. Las lecturas de 2026 son del INA sin validar. La sección para quien va a pescar solo enlaza la página del río y la guía.',
];
$N['en'] = [
	'titulo' => 'The Paraná at Goya: the highest river level for a 20 November since 1990',
	'slug'   => 'parana-river-goya-20-november-2026',
	'bajada' => 'On 20 November 2026 the Paraná River stood at 5.68 m at Goya, higher than on any other 20 November in the series. Charts, data to download and how we calculated it.',
	'seo_title' => 'Paraná at Goya: highest level for a 20 November',
	'seo_description' => 'The Paraná reads 5.68 m at Goya on 20/11/2026: higher than any other 20 November since 1990 (usual: 3.93 m). Charts, data and method.',
	'datos' => [
		'5.68 m on 20 November 2026: the highest reading for that date in the 1990–2026 series (the previous maximum was 5.63 m, in 1997).',
		'The usual level for 20 November (median of 36 years): 3.93 m. The reading is 1.75 m above it.',
		'It rose 2.67 m in 90 days; in the last 30 days, +0.17 m.',
		'On the same day in 2021 (the lowest-average year) it read 1.45 m; in 1998 (the highest), 4.08 m.',
		'Series records at Goya: 0.47 m (24/1/2022) and 7.20 m (13/6/1992).',
		'This is gauge height: not depth, and not comparable with another station.',
	],
	'p0' => '<strong>At Goya, the Paraná River stands today at 5.68 m: higher than on any other 20 November since 1990.</strong> The usual level for this date is 3.93 m.',
	'fecha' => '<em>Reading of 20 November 2026. Data: {ina|National Water Institute (INA)} (Argentine Naval Prefecture gauges). Prepared by Pescamapa.</em>',
	'h1' => 'It rose almost three metres in three months',
	'p1' => 'Ninety days ago the Paraná at Goya was 2.67 m lower than today. In the last 30 days it barely changed: +0.17 m. None of the 36 years from 1990 to 2025 reached that level on 20 November: the highest was 5.63 m, in 1997, and the lowest 1.20 m, in 2020.',
	'cap2' => 'Every 20 November from 1990 to 2025 against 2026. Data: INA. Prepared by Pescamapa.',
	'h2' => 'The flood is moving down the river',
	'p2' => 'It is not only Goya: Esquina, La Paz and Santa Fe are also at their highest level for a 20 November since 1990. But the change over the last 30 days shows where the water is heading. Upriver it is already falling: Paso de la Patria −1.77 m and Corrientes −1.43 m. Downriver it is still rising: La Paz +0.88 m and Santa Fe +2.03 m. Goya, in the middle, barely changed (+0.17 m). It is the pattern of a flood moving downriver. Over 90 days, all six stations of the stretch, from Paso de la Patria to Santa Fe, rose between 2.67 and 2.81 m.',
	'cap3' => 'Change in level over 30 days, north to south. Data: INA. Prepared by Pescamapa.',
	'h3' => 'A year of two halves',
	'p3' => 'Until mid-year, 2026 was low: from 1 January to 20 November the average level at Goya was 3.28 m, 8th of the 37 years in the series ranked from lowest to highest (the three lowest for the same stretch: 2021, 1.76 m; 2020, 1.98 m; and 2022, 2.18 m). Then the river rose. For the contrast: on 20 November 2021, the lowest-average year of the reference (1.72 m), Goya read 1.45 m; in 1998, the highest (5.22 m), 4.08 m. This year, 5.68 m.',
	'h4' => 'What it means if you are going to fish at Goya',
	'p4' => 'Before you go, check {r|today’s river level at Goya}, updated every day, and {g|the Goya guide}, with its section on how fishing changes with the river level. Gauge height measures the level at that point, not the depth.',
	'h5' => 'How we calculated it',
	'l5' => [
		'<strong>Source:</strong> the Goya gauge height published by the {ina|National Water Institute} (INA), from the Argentine Naval Prefecture gauges. The 2026 readings come from INA and are not validated.',
		'<strong>One value per day:</strong> the average of the day’s readings (local time), to the centimetre. Impossible values and gauge zeros that are data gaps are discarded.',
		'<strong>Reference:</strong> 1990–2025. Each day is compared with the same calendar day: for 20 November there are 36 years of data.',
		'<strong>Percentile:</strong> the share of those years that stood below the reading. For 20 November 2026 that is all 36: percentile 100. The bands of the first chart are the 10th to 90th (light) and 25th to 75th (dark) percentiles of each day, smoothed with a 15-day average.',
		'<strong>What it is not:</strong> it is gauge height, not depth, and every station has its own zero: we compare changes, not heights between stations. It is not a forecast.',
	],
	'h6' => 'Use the charts and the data',
	'p6' => 'You can use these charts and numbers if you credit Pescamapa with a link to this note.',
	'p6b' => '<a href="{csv}" download>Download the Goya data (CSV)</a> (column names in Spanish): one row per calendar day, with the years with data, the minimum, the 10th, 25th, 50th, 75th and 90th percentiles and the maximum for 1990 to 2025, the value for that day in 2021 and in 1998 and the 2026 one up to 20 November, all in metres.',
	'faq_h' => 'Frequently asked questions',
	'faq' => [
		[ 'How high is the Paraná at Goya today?', 'On 20 November 2026 it read 5.68 m at the Goya gauge (INA). Today’s level is updated every day at {r|River level at Goya}.' ],
		[ 'Is this the biggest flood?', 'No. It is the highest level for a 20 November since 1990, but the series record at Goya is 7.20 m, on 13 June 1992.' ],
		[ 'Where does the data come from?', 'From the daily readings of the Goya gauge of the {ina|National Water Institute}, 1990 to 2026. Pescamapa calculated the comparison with the same day of each year; it is explained above, in “How we calculated it”.' ],
	],
	'f' => 'National Water Institute (INA): daily readings of the Argentine Naval Prefecture gauges, 1990–2026',
];
$N['pt'] = [
	'titulo' => 'O Paraná em Goya: o nível mais alto para um 20 de novembro desde 1990',
	'slug'   => 'rio-parana-goya-20-de-novembro-2026',
	'bajada' => 'Em 20 de novembro de 2026 o rio Paraná marcou 5,68 m em Goya, mais do que em qualquer outro 20 de novembro da série. Gráficos, dados para baixar e como calculamos.',
	'seo_title' => 'Paraná em Goya: maior nível de um 20 de novembro',
	'seo_description' => 'O Paraná marca 5,68 m em Goya em 20/11/2026: mais do que em qualquer 20 de novembro desde 1990 (o habitual: 3,93 m). Gráficos, dados e método.',
	'datos' => [
		'5,68 m em 20 de novembro de 2026: o nível mais alto para essa data da série 1990–2026 (o máximo anterior era 5,63 m, em 1997).',
		'O habitual para um 20 de novembro (mediana de 36 anos): 3,93 m. A leitura está 1,75 m acima.',
		'Subiu 2,67 m em 90 dias; nos últimos 30 dias, +0,17 m.',
		'No mesmo dia de 2021 (o ano de menor nível médio) marcou 1,45 m; no de 1998 (o de maior), 4,08 m.',
		'Recordes da série em Goya: 0,47 m (24/1/2022) e 7,20 m (13/6/1992).',
		'É altura de escala: não é profundidade nem se compara com a de outra estação.',
	],
	'p0' => '<strong>Em Goya, o rio Paraná marca hoje 5,68 m: mais do que em qualquer outro 20 de novembro desde 1990.</strong> O habitual para esta data é 3,93 m.',
	'fecha' => '<em>Leitura de 20 de novembro de 2026. Dados: {ina|Instituto Nacional da Água (INA)} (escalas da Prefeitura Naval Argentina). Elaboração: Pescamapa.</em>',
	'h1' => 'Subiu quase três metros em três meses',
	'p1' => 'Há 90 dias o Paraná em Goya estava 2,67 m mais baixo do que hoje. Nos últimos 30 dias quase não mudou: +0,17 m. Nenhum dos 36 anos de 1990 a 2025 chegou a esse nível em um 20 de novembro: o mais alto foi 5,63 m, em 1997, e o mais baixo 1,20 m, em 2020.',
	'cap2' => 'Cada 20 de novembro de 1990 a 2025 contra 2026. Dados: INA. Elaboração: Pescamapa.',
	'h2' => 'A cheia está descendo pelo rio',
	'p2' => 'Não é só Goya: Esquina, La Paz e Santa Fe também estão no nível mais alto para um 20 de novembro desde 1990. Mas a variação dos últimos 30 dias mostra para onde a água se move. Rio acima já baixa: Paso de la Patria −1,77 m e Corrientes −1,43 m. Rio abaixo continua subindo: La Paz +0,88 m e Santa Fe +2,03 m. Goya, no meio, quase não mudou (+0,17 m). É o padrão de uma cheia que avança rio abaixo. Em 90 dias, as seis estações do trecho, de Paso de la Patria a Santa Fe, subiram entre 2,67 e 2,81 m.',
	'cap3' => 'Variação do nível em 30 dias, de norte a sul. Dados: INA. Elaboração: Pescamapa.',
	'h3' => 'Um ano de duas metades',
	'p3' => 'Até meados do ano, 2026 esteve baixo: de 1.º de janeiro a 20 de novembro o nível médio em Goya foi 3,28 m, o 8.º lugar entre os 37 anos da série ordenados do menor para o maior (os três mais baixos do mesmo trecho: 2021, com 1,76 m; 2020, com 1,98 m; e 2022, com 2,18 m). Depois o rio subiu. Para medir o contraste: em 20 de novembro de 2021, o ano de menor nível médio da referência (1,72 m), Goya marcou 1,45 m; em 1998, o de maior (5,22 m), 4,08 m. Este ano, 5,68 m.',
	'h4' => 'O que significa para quem vai pescar em Goya',
	'p4' => 'Antes de sair, consulte {r|o nível do rio em Goya hoje}, atualizado todos os dias, e {g|o guia de Goya}, com a seção sobre como a pesca muda com o nível do rio. A altura de escala mede o nível naquele ponto, não a profundidade.',
	'h5' => 'Como calculamos',
	'l5' => [
		'<strong>Fonte:</strong> a altura da escala de Goya publicada pelo {ina|Instituto Nacional da Água} (INA), das escalas da Prefeitura Naval Argentina. As leituras de 2026 são do INA e não estão validadas.',
		'<strong>Um valor por dia:</strong> a média das leituras do dia (hora local), ao centímetro. Descartam-se os valores impossíveis e os zeros de escala que são lacunas de dados.',
		'<strong>Referência:</strong> 1990–2025. Cada dia é comparado com o mesmo dia do calendário: para 20 de novembro há 36 anos com dado.',
		'<strong>Percentil:</strong> a parte desses anos que ficou abaixo da leitura. Para 20 de novembro de 2026 são os 36: percentil 100. As faixas do primeiro gráfico são os percentis 10 a 90 (clara) e 25 a 75 (escura) de cada dia, suavizados com média de 15 dias.',
		'<strong>O que não é:</strong> é altura de escala, não profundidade, e cada estação tem o seu próprio zero: comparam-se as variações, não as alturas entre estações. Não é previsão.',
	],
	'h6' => 'Use os gráficos e os dados',
	'p6' => 'Você pode usar estes gráficos e números citando a Pescamapa com link para esta nota.',
	'p6b' => '<a href="{csv}" download>Baixe os dados de Goya (CSV)</a> (nomes das colunas em espanhol): uma linha por dia do calendário, com os anos com dado, o mínimo, os percentis 10, 25, 50, 75 e 90 e o máximo de 1990 a 2025, o valor desse dia em 2021 e em 1998 e o de 2026 até 20 de novembro, tudo em metros.',
	'faq_h' => 'Perguntas frequentes',
	'faq' => [
		[ 'Quanto marca o Paraná em Goya hoje?', 'Em 20 de novembro de 2026 marcou 5,68 m na escala de Goya (INA). O nível de hoje é atualizado todos os dias em {r|Nível do rio em Goya}.' ],
		[ 'É a maior cheia?', 'Não. É o maior nível para um 20 de novembro desde 1990, mas o recorde da série em Goya é 7,20 m, em 13 de junho de 1992.' ],
		[ 'De onde vêm os dados?', 'Das leituras diárias da escala de Goya do {ina|Instituto Nacional da Água}, de 1990 a 2026. A Pescamapa calculou a comparação com o mesmo dia de cada ano; está explicada acima, em «Como calculamos».' ],
	],
	'f' => 'Instituto Nacional da Água (INA): leituras diárias das escalas da Prefeitura Naval Argentina, 1990–2026',
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
	wp_update_post( [ 'ID' => $att, 'post_content' => "Elaboración propia de Pescamapa ($hoy), hecha con wordpress/build/rio-nota-graficos.py a partir de la lectura del INA del 20/11/2026." ] );
	return (int) $att;
};
foreach ( $A as $k => $a ) {
	$adj[ $k ] = $subir( $a['archivo'], $a['titulo'], $a['alt'] );
}
$adj['csv'] = $subir( $CSV, 'Paraná en Goya: histórico por día del calendario, 1990–2026 (CSV)', null );
$url_de = fn( int $id ) => $id ? (string) wp_get_attachment_url( $id ) : '';
$url_img = fn( int $id ) => $id ? ( (string) wp_get_attachment_image_url( $id, 'large' ) ?: $url_de( $id ) ) : '';

// ---- Link resolver ----
$res = function ( string $html, string $i ) use ( $INA, $adj, $url_de ): string {
	$html = preg_replace_callback( '/\{(r|g|ina)\|([^}]+)\}/u', function ( $m ) use ( $i, $INA ) {
		[ , $tipo, $txt ] = $m;
		if ( 'ina' === $tipo ) {
			return '<a href="' . esc_url( $INA ) . '" rel="noopener">' . $txt . '</a>';
		}
		if ( 'r' === $tipo ) {
			return '<a href="' . esc_url( pm_url_rio_punto( 'goya', $i ) ) . '">' . $txt . '</a>';
		}
		$p = pm_buscar( 'pm_destino', 'goya', $i ) ?? pm_buscar( 'pm_destino', 'goya', 'es' );
		return $p ? '<a href="' . esc_url( get_permalink( $p->ID ) ) . '">' . $txt . '</a>' : $txt;
	}, $html );
	return str_replace( '{csv}', esc_url( $url_de( $adj['csv'] ) ?: '#csv-pendiente-en-simulacro' ), $html );
};

// ---- The notes ----
$existe = fn( string $i ) => array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i );
$cambios = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n = $N[ $i ];
	if ( $ya = $existe( $i ) ) {
		WP_CLI::log( "$clave ($i) #" . array_values( $ya )[0]->ID . ' ya existe: no toco nada.' );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	$fig = fn( string $k, string $cap ) => pm_pg_figura( $adj[ $k ], $url_img( $adj[ $k ] ) ?: '#imagen-pendiente-en-simulacro', $A[ $k ]['alt'][ $i ], esc_html( $cap ) );
	$cuerpo = pm_pg_p( $res( $n['p0'], $i ) ) . pm_pg_p( $res( $n['fecha'], $i ) )
		. pm_pg_h2( $n['h1'] ) . pm_pg_p( $res( $n['p1'], $i ) ) . $fig( 'dia', $n['cap2'] )
		. pm_pg_h2( $n['h2'] ) . pm_pg_p( $res( $n['p2'], $i ) ) . $fig( 'rio', $n['cap3'] )
		. pm_pg_h2( $n['h3'] ) . pm_pg_p( $res( $n['p3'], $i ) )
		. pm_pg_h2( $n['h4'] ) . pm_pg_p( $res( $n['p4'], $i ) )
		. pm_pg_h2( $n['h5'] ) . pm_pg_lista( array_map( fn( $x ) => $res( $x, $i ), $n['l5'] ) )
		. pm_pg_h2( $n['h6'] ) . pm_pg_p( $res( $n['p6'], $i ) ) . pm_pg_p( $res( $n['p6b'], $i ) )
		. pm_pg_h2( $n['faq_h'] ) . pm_pg_faq( array_map( fn( $q ) => [ $q[0], $res( $q[1], $i ) ], $n['faq'] ) );
	! preg_match( '/\{[a-z0-9:|-]+/', $cuerpo, $m ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	foreach ( [ 'operador', 'operator', 'operadora', 'vosotros' ] as $mala ) {
		false === stripos( $cuerpo, $mala ) || WP_CLI::error( "$i: palabra no permitida «$mala»" );
	}
	$fuentes = [ [ 'nombre' => $n['f'], 'url' => $INA, 'oficial' => 1, 'consultada' => $hoy ] ];
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $n['faq'] ) . " FAQ; title $tl / description $dl; portada #{$adj['ano']}" );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ), 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'datos', 'nota_fecha' => $hoy, 'nota_destino' => 'goya', 'nota_origen' => $N['es']['orig'] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		set_post_thumbnail( $id, $adj['ano'] );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_post( $id )->post_name === $n['slug'] && count( pm_filas( 'fuentes', $id ) ) === 1 && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] )
		&& count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && false !== strpos( $cont, 'wp-image-' . $adj['dia'] ) && false !== strpos( $cont, 'wp-image-' . $adj['rio'] )
		&& false !== strpos( $cont, esc_url( $url_de( $adj['csv'] ) ) ) && false !== strpos( $cont, pm_url_rio_punto( 'goya', $i ) ) && false !== strpos( $cont, 'en' === $i ? '5.68' : '5,68' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
if ( $aplicar ) {
	// pm_buscar() caches the published notes before this run created them: look the new one up directly.
	$nota_es = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $x ) => 'es' === pm_idioma_de( $x->ID ) ) )[0] ?? null;
	$e = $nota_es ? pm_nota( $nota_es->ID ) : null;
	$en_lista = false;
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		foreach ( pm_notas_para( $i, 'goya', [], 50 ) as $x ) {
			if ( $x['clave'] === $clave ) {
				$en_lista = true;
			}
		}
	}
	WP_CLI::log( ( $e && 'goya' === $e['destino'] && 'datos' === $e['tipo'] && $e['foto'] === $adj['ano'] && $en_lista ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee destino ' . ( $e['destino'] ?? '?' ) . ', tipo ' . ( $e['tipo'] ?? '?' ) . ', fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) . ', listada en la guía de Goya: ' . ( $en_lista ? 'sí' : 'no' ) );
	flush_rewrite_rules( false );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
