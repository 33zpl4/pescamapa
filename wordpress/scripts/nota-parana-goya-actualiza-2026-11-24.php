<?php
/**
 * UPDATES the live data note "El Paraná en Goya" (clave parana-goya-20-noviembre-2026; es #870, en #871, pt #872) with the INA readings
 * of 24/11/2026 (the note of 21/11 used readings to 20/11). Same rows, same URLs: the headline («Goya nunca tuvo un noviembre así...»)
 * holds on the MAXIMUM (6,10 m, already above every earlier November, and a maximum cannot fall) and on the mean of the month so far
 * (5,87 m for 1-24 Nov against 5,51 m, the best full November, 1997); the figures that moved are the days over the alert level (38), the
 * last reading (5,47 m on 24/11), the mean, the drop since the peak (0,63 m) and what the INA says (daily report of 24/11 + the numeric
 * forecast of 20/11, which has not been re-issued). Every figure comes from wordpress/build/rio-nota-noviembre/ (rio-historia.py, branch
 * rio-historia-desde-1903, run of 24/11/2026); derivation in docs/borradores/parana-goya-noviembre-2026.md (section of 24/11).
 *
 * It replaces, per language (es, en, pt), on the three existing rows found by clave + language: post content, subtitle (bajada), datos
 * clave (6), sources (3: INA portal, INA daily report 24/11, INA numeric forecast 20/11), SEO description and "actualizado". It does NOT
 * touch: title, slug (no redirect machinery is needed), nota_fecha (first publication), nota_tipo, nota_destino, nota_informativa
 * (no commercial CTA), the featured image (the cover, infographic 1, keeps the same figures) nor the translation timestamps logic
 * beyond marking es edited / pt, en translated like the 21/11 script. Attachments are uploaded idempotently by file name
 * (_pm_archivo_origen): the new infographic 2 (parana-goya-el-rio-dia-por-dia-24-nov.png) and the two CSV regenerated to 24/11
 * (...-24-nov.csv); the three they replace stay in the media library, unused (not deleted). Infographics 1 and 3 are the ones of 20/11:
 * the peaks they print (6,10 m; 7,42 / 6,10 / 5,60 / 6,30 / 5,88 m) did not change.
 *
 * Dry run first ($aplicar = false): prints before/after, attachments; writes nothing. Run with wp-script.yml
 * (script = nota-parana-goya-actualiza-2026-11-24; aplicar=true for the real run). Idempotent: a second run rewrites the same content.
 */
if ( ! function_exists( 'pm_pr_p' ) ) {
	function pm_pr_h2( string $t ): string {
		return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->\n\n";
	}
	function pm_pr_p( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	function pm_pr_lista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
	}
	/** FAQ as a group block with the class pm-faq (visible and read by pm_faqs_contenido() for the FAQPage JSON-LD). */
	function pm_pr_faq( array $faqs ): string {
		$in = '';
		foreach ( $faqs as [ $q, $a ] ) {
			$in .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( $in ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	function pm_pr_figura( int $att, string $url, string $alt, string $pie ): string {
		return "<!-- wp:image {\"id\":{$att},\"sizeSlug\":\"large\",\"linkDestination\":\"none\"} -->\n<figure class=\"wp-block-image size-large\" style=\"margin:1.5em 0\"><img src=\"" . esc_url( $url ) . "\" alt=\"" . esc_attr( $alt ) . "\" class=\"wp-image-{$att}\"/><figcaption class=\"wp-element-caption\">{$pie}</figcaption></figure>\n<!-- /wp:image -->\n\n";
	}
}

$aplicar = false;
$clave          = 'parana-goya-20-noviembre-2026';
$hoy            = '20261124';
$lectura        = '20261124';
$carpeta        = '/tmp/pm-fotos';
$marcas_cta     = [ 'class="cta-nota"', 'class="barra-pedir"', 'class="guias-especie"', 'cta-gratis', 'banda-gratis' ];

// Official links (INA daily report of 24/11/2026 and numeric forecast of 20/11/2026; consulted 24/11/2026).
$ENL = [
	'ina'    => 'https://www.ina.gob.ar/alerta/index.php',
	'diario' => 'https://alerta.ina.gob.ar/ina/06-INFORMES/diario/pdf/reporte_diario_2026-11-24.pdf',
	'prono'  => 'https://www.ina.gov.ar/archivos/alerta/TabProno20261120_.pdf',
	'pna'    => 'https://contenidosweb.prefecturanaval.gob.ar/alturas/',
	'coe'    => 'https://www.goya.gob.ar/?q=coe-comite-operativo-emergencia',
];

// ---- Attachments: [ key => file, title, alt per language ] ----
$A = [
	'nunca' => [
		'archivo' => 'parana-goya-nunca-en-noviembre.png', 'titulo' => 'Goya: el máximo de cada noviembre, 1903–2026',
		'alt' => [
			'es' => 'Gráfico de varillas con la máxima del Paraná en Goya en cada noviembre de 1903 a 2026: 2026 marca 6,10 m, por encima de todos los demás; el máximo anterior para el mes es 5,80 m, de 1998. Dos líneas marcan el nivel de alerta (5,20 m) y el de evacuación (5,70 m), referencias de la escala; solo 1998 y 2026 pasaron la segunda en un noviembre',
			'en' => 'Stem chart of the highest Paraná reading at Goya in each November from 1903 to 2026: 2026 reaches 6.10 m, above every other year; the previous maximum for the month is 5.80 m, in 1998. Two lines mark the alert level (5.20 m) and the evacuation level (5.70 m), gauge reference marks; only 1998 and 2026 passed the second one in a November',
			'pt' => 'Gráfico de hastes com a máxima do Paraná em Goya em cada novembro de 1903 a 2026: 2026 marca 6,10 m, acima de todos os outros; o máximo anterior para o mês é 5,80 m, de 1998. Duas linhas marcam o nível de alerta (5,20 m) e o de evacuação (5,70 m), referências da escala; só 1998 e 2026 passaram o segundo em um novembro',
		],
	],
	'dias' => [
		'archivo' => 'parana-goya-el-rio-dia-por-dia-24-nov.png', 'titulo' => 'El Paraná en Goya día por día, septiembre a noviembre de 2026 (al 24/11)',
		'alt' => [
			'es' => 'Curva de la altura diaria del Paraná en Goya del 1 de septiembre al 24 de noviembre de 2026: sube de 2,57 m el 4 de septiembre, cruza el nivel de alerta (5,20 m) el 18 de octubre y el de evacuación (5,70 m) el 29 de octubre, marca 6,10 m del 3 al 5 de noviembre, vuelve por debajo del de evacuación el 20 de noviembre y baja a 5,47 m el 24; 38 días seguidos sobre el alerta y 22 sobre el de evacuación (del 29/10 al 19/11). Aparte, el pronóstico del INA emitido el 20 de noviembre: 5,39 m el 24/11 y 5,45 m el 1/12, con rango probable de 5,00 a 5,70 m',
			'en' => 'Curve of the daily Paraná level at Goya from 1 September to 24 November 2026: it rises from 2.57 m on 4 September, crosses the alert level (5.20 m) on 18 October and the evacuation level (5.70 m) on 29 October, peaks at 6.10 m from 3 to 5 November, drops back below the evacuation level on 20 November and falls to 5.47 m on 24 November; 38 days in a row above the alert level and 22 above the evacuation level (29 October to 19 November). Separately, the INA forecast issued on 20 November: 5.39 m on 24/11 and 5.45 m on 1/12, with a probable range of 5.00 to 5.70 m',
			'pt' => 'Curva do nível diário do Paraná em Goya de 1.º de setembro a 24 de novembro de 2026: sobe de 2,57 m em 4 de setembro, cruza o nível de alerta (5,20 m) em 18 de outubro e o de evacuação (5,70 m) em 29 de outubro, marca 6,10 m de 3 a 5 de novembro, volta a ficar abaixo do de evacuação em 20 de novembro e baixa para 5,47 m em 24 de novembro; 38 dias seguidos acima do alerta e 22 acima do de evacuação (de 29/10 a 19/11). À parte, a previsão do INA emitida em 20 de novembro: 5,39 m em 24/11 e 5,45 m em 1.º/12, com faixa provável de 5,00 a 5,70 m',
		],
	],
	'abajo' => [
		'archivo' => 'parana-la-misma-agua-rio-abajo.png', 'titulo' => 'La misma agua, río abajo: de Corrientes a Santa Fe',
		'alt' => [
			'es' => 'Línea de tiempo de la cresta de la crecida de norte a sur: Corrientes 7,42 m el 23 de octubre, Goya 6,10 m del 3 al 5 de noviembre, Esquina 5,60 m el 6 y 7 de noviembre, La Paz 6,30 m el 14 de noviembre y Santa Fe 5,88 m el 20 de noviembre (la lectura más alta hasta ahora); cada escala con sus propios niveles de alerta y de evacuación, y las cinco por encima del de evacuación',
			'en' => 'Timeline of the flood crest from north to south: Corrientes 7.42 m on 23 October, Goya 6.10 m from 3 to 5 November, Esquina 5.60 m on 6 and 7 November, La Paz 6.30 m on 14 November and Santa Fe 5.88 m on 20 November (the highest reading so far); each gauge with its own alert and evacuation levels, and all five above their evacuation level',
			'pt' => 'Linha do tempo da crista da cheia de norte a sul: Corrientes 7,42 m em 23 de outubro, Goya 6,10 m de 3 a 5 de novembro, Esquina 5,60 m em 6 e 7 de novembro, La Paz 6,30 m em 14 de novembro e Santa Fe 5,88 m em 20 de novembro (a leitura mais alta até agora); cada escala com seus próprios níveis de alerta e de evacuação, e as cinco acima do de evacuação',
		],
	],
];
$CSV_D = 'parana-goya-estadistica-diaria-1903-2026-24-nov.csv';
$CSV_M = 'parana-goya-mensual-1903-2026-24-nov.csv';

// ---- Text. Tokens {clave|texto} become links: ina diario prono pna coe (official), r (river page of Goya), csvd csvm (downloads). ----
$N = [];
$N['es'] = [
	'titulo' => 'Goya nunca tuvo un noviembre así: el Paraná, en su nivel más alto para el mes en 124 años',
	'bajada' => 'El Paraná llegó a 6,10 m en la escala de Goya el 5 de noviembre, el máximo para un noviembre desde 1903. Lleva 38 días sobre el nivel de alerta (el 24/11 medía 5,47 m) y el INA ve elevada probabilidad de que siga por encima. Qué pasó, por qué es inusual y qué se espera.',
	'seo_title' => 'Paraná en Goya: el noviembre más alto en 124 años',
	'seo_description' => 'El Paraná llegó a 6,10 m en Goya el 5/11/2026, el máximo para un noviembre desde 1903. Lleva 38 días sobre el nivel de alerta. Datos y qué prevé el INA.',
	'datos' => [
		'6,10 m el 5 de noviembre de 2026: el máximo del Paraná en Goya para un noviembre en 124 años de registros (el anterior, 5,80 m, en 1998).',
		'38 días seguidos sobre el nivel de alerta (5,20 m) al 24 de noviembre, desde el 18 de octubre; 22 días sobre el de evacuación (5,70 m), del 29 de octubre al 19 de noviembre, algo que no ocurría desde marzo de 2016.',
		'Media del 1 al 24 de noviembre: 5,87 m. La mayor media de un noviembre completo, hasta ahora: 5,51 m (1997).',
		'Última lectura del INA, 24 de noviembre: 5,47 m, 0,63 m por debajo del pico; unos 5 cm por día desde el 19/11. Sigue 0,27 m sobre el nivel de alerta.',
		'No es la mayor crecida: el récord de la serie es 7,20 m, el 13 de junio de 1992. Lo inusual es la época del año.',
		'El INA (reporte diario del 24/11) espera que el río persista oscilante o con leve descenso en aguas medias altas, con elevada probabilidad de seguir sobre el nivel de alerta. Su pronóstico numérico del 20/11 daba 5,39 m para el 24/11 (se midieron 5,47 m) y 5,45 m para el 1 de diciembre.',
	],
	'cuerpo' => [
		[ 'p', '<strong>El río Paraná llegó a 6,10 m en la escala de Goya el 5 de noviembre: es la altura más alta que se registró en ese puerto en un mes de noviembre desde que empieza la serie del Instituto Nacional del Agua (INA), en 1903. El máximo anterior para el mes era 5,80 m, de 1998. Lleva 38 días seguidos sobre el nivel de alerta (5,20 m) y estuvo 22 días sobre el de evacuación (5,70 m), algo que no ocurría desde marzo de 2016. El 24 de noviembre medía 5,47 m y bajaba de a poco. El INA estima elevada probabilidad de que siga por encima del nivel de alerta, con posibles repuntes.</strong>' ],
		[ 'p', '<em>Lecturas hasta el 24 de noviembre de 2026. Datos: {ina|Instituto Nacional del Agua} (escalas de Prefectura Naval Argentina). Elaboración: Pescamapa.</em>' ],
		[ 'h2', 'Qué pasó' ],
		[ 'p', 'El año empezó con el río bajo: hasta junio, 171 de los 181 días estuvieron por debajo de la mediana de la serie para cada fecha. Después giró. El 4 de septiembre el río medía 2,57 m; desde ahí subió, con algún parón, hasta los 6,10 m del 5 de noviembre: 3,53 m en 62 días. Cruzó el nivel de alerta el 18 de octubre (5,28 m) y el de evacuación el 29 de octubre.' ],
		[ 'p', 'El pico llegó entre el 3 y el 5 de noviembre, con 6,10 m. Desde entonces baja de a poco: 5,86 m el 14 de noviembre, 5,68 m el 20 (con lo que quedó otra vez por debajo del nivel de evacuación, después de 22 días seguidos arriba, del 29 de octubre al 19 de noviembre) y 5,47 m el 24: 0,63 m menos que en el pico. Los 24 días de noviembre estuvieron por encima del nivel de alerta, y 19 de los 24, por encima del de evacuación; la media del 1 al 24 fue 5,87 m. En lo que va del año son 38 días seguidos sobre el alerta, y el río sigue 0,27 m por encima de ese nivel.' ],
		[ 'fig', 'dias', 'Altura diaria del Paraná en Goya, del 1 de septiembre al 24 de noviembre de 2026, y pronóstico del INA emitido el 20/11 (aparte, con su rango). Datos: INA. Elaboración: Pescamapa.' ],
		[ 'p', 'Una aclaración sobre los niveles: el nivel de alerta (5,20 m) y el de evacuación (5,70 m) son marcas de referencia de la escala de Goya que publica el INA. No son una orden de evacuación: eso lo decide Defensa Civil.' ],
		[ 'h2', 'Por qué no se parece a ningún noviembre' ],
		[ 'p', 'Goya tiene lecturas de altura desde 1903. En esos 124 años ningún noviembre tuvo el río tan alto: el máximo de 2026 (6,10 m) supera al de todos los anteriores, y el mayor era el de 1998, con 5,80 m. Con la media pasa lo mismo: la de 2026 del 1 al 24 de noviembre (5,87 m) ya supera a la de cualquier noviembre completo; la mayor, 5,51 m, era la de 1997.' ],
		[ 'p', 'El mes cierra el 30, así que la media todavía puede moverse. Para que bajara de 5,51 m, el río tendría que promediar menos de 4,07 m en los seis días que faltan (25 al 30), es decir, estar más de 1,4 m por debajo de la última lectura. El máximo del mes, 6,10 m, ya no puede bajar.' ],
		[ 'p', 'Solo dos noviembres de la serie pasaron los 5,70 m, el nivel de evacuación de la escala: 1998 y 2026.' ],
		[ 'fig', 'nunca', 'La máxima de cada noviembre en Goya, de 1903 a 2026 (lecturas hasta el 20/11; el máximo no cambió). Datos: INA. Elaboración: Pescamapa.' ],
		[ 'p', '<strong>No es la mayor crecida.</strong> El récord de Goya es 7,20 m, el 13 de junio de 1992, y el pico de 2026 es el 11.º más alto de los 124 años de la serie (un pico por año). Lo inusual es la época: un río tan alto en noviembre no se había visto.' ],
		[ 'p', 'La última vez que el río estuvo en 6,10 m o más fue entre el 24 de diciembre de 2015 y el 2 de febrero de 2016 (máximo, 6,46 m). Antes de este episodio, la última vez sobre el nivel de evacuación fue en marzo de 2016, y sobre el de alerta, del 6 de noviembre al 1 de diciembre de 2023 (máximo, 5,67 m).' ],
		[ 'h2', 'Qué se espera' ],
		[ 'p', 'Todo lo que sigue es del INA, con su fecha; no es un pronóstico de Pescamapa. El INA actualiza el pronóstico numérico del Paraná medio los martes y los viernes: el que se cita acá es el emitido el 20 de noviembre y, en la consulta del 24 de noviembre, todavía no había uno nuevo. Para lo más reciente, mirá {ina|Alerta Hidrológico}.' ],
		[ 'ul', [
			'<strong>Reporte diario del INA, 24/11/2026</strong> ({diario|PDF}): «en Goya/Reconquista - La Paz se registran marcas en aguas medias altas, con excedencia del nivel de Alerta en Goya y La Paz, y excedencia de los niveles de Alerta y Evacuación en Reconquista. Este sector del tramo se espera que persista oscilante/leve descenso en aguas medias altas, estabilizando progresivamente hacia plazo extendido y con chances de nuevos repuntes hacia mayor plazo, todavía con elevada probabilidad de sostenerse por encima de los niveles de Alerta». Para el tramo de más arriba, desde Corrientes, el mismo reporte prevé a 7 días una gradual estabilización en aguas medias altas, con perspectiva de al menos mantenerse estable o de un ascenso gradual, y todavía con posibilidad de nuevos repuntes.',
			'<strong>Pronóstico numérico del INA, emitido el 20/11/2026</strong> ({prono|PDF}): para Goya, 5,39 m el 24 de noviembre y 5,45 m el 1 de diciembre, con un rango probable de 5,00 a 5,70 m en ambas fechas. El 24 de noviembre la escala marcó 5,47 m (lectura diaria del INA, sin validar): 0,08 m más que el valor central y dentro del rango. El pronóstico del 1 de diciembre sigue vigente.',
		] ],
		[ 'h2', 'La misma agua, río abajo' ],
		[ 'p', 'La crecida de octubre se movió de norte a sur. Pasó por Corrientes con 7,42 m el 23 de octubre; llegó a Goya el 3 de noviembre y marcó 6,10 m hasta el 5; en Esquina, con 5,60 m, fue el 6 y 7 de noviembre; en La Paz, con 6,30 m, el 14 de noviembre; y en Santa Fe midió 5,88 m el 20 de noviembre, la lectura más alta hasta ahora. De Corrientes a Goya la cresta tardó entre 11 y 13 días, y unos 22 hasta La Paz.' ],
		[ 'p', 'Las cinco escalas pasaron su propio nivel de evacuación. Con las lecturas del 1 al 24 de noviembre, Esquina y La Paz tienen la mayor media del mes de su serie; Corrientes, la mayor entre los noviembres completos (y la segunda contra el mismo tramo, del 1 al 24); y Santa Fe, el mayor máximo.' ],
		[ 'fig', 'abajo', 'La cresta de la crecida, de Corrientes a Santa Fe: fecha y altura del pico en cada escala, con sus propios niveles (lecturas hasta el 20/11; los picos no cambiaron). Datos: INA. Elaboración: Pescamapa.' ],
		[ 'h2', 'Dónde mirar' ],
		[ 'ul', [
			'Alturas, reporte diario, informe semanal y pronósticos: {ina|Alerta Hidrológico del INA}.',
			'Estado de los ríos, de la {pna|Prefectura Naval Argentina} (las escalas de las que sale el dato).',
			'Municipalidad de Goya: {coe|Comité Operativo de Emergencia (COE)}.',
		] ],
		[ 'p', 'Las indicaciones ante crecidas las dan Defensa Civil y el COE: esta nota no las reemplaza. La altura de hoy se actualiza todos los días en {r|Altura del río en Goya hoy}.' ],
		[ 'h2', 'Cómo lo calculamos' ],
		[ 'ul', [
			'<strong>Fuente:</strong> lecturas diarias de la escala de Goya que publica el {ina|Instituto Nacional del Agua} (INA), de las escalas de Prefectura Naval Argentina, de 1903 a 2026: un valor por día (el promedio de las lecturas del día, hora local). Las lecturas de 2026 son del INA y todavía no están validadas.',
			'<strong>«Desde 1903»</strong> es la serie diaria del INA para la escala de Goya, y supone que el cero de la escala no cambió en más de un siglo (el catálogo del INA da un cero fijo; no lo pudimos verificar).',
			'<strong>Niveles:</strong> alerta 5,20 m y evacuación 5,70 m, los que publica el INA para esa escala.',
			'<strong>Noviembre incompleto:</strong> 2026 tiene 24 de los 30 días, así que se compara de dos formas: contra los noviembres completos de cada año (con al menos el 90 % de los días con dato) y contra el mismo tramo, del 1 al 24 de noviembre, de cada año. En las dos, 2026 queda primero en media y en máximo. En el primer gráfico, el punto hueco marca un noviembre con datos incompletos.',
			'<strong>Un episodio</strong> son días seguidos en o sobre un valor.',
			'<strong>Qué no es:</strong> es altura de escala, no profundidad, y cada escala tiene su propio cero: de una estación a otra se comparan las fechas, no las alturas.',
		] ],
		[ 'h2', 'Usá los gráficos y los datos' ],
		[ 'p', 'Podés usar estos gráficos y números citando a Pescamapa con enlace a esta nota.' ],
		[ 'p', 'Datos para descargar (al 24 de noviembre): {csvd|estadística diaria de Goya, 1903–2026 (CSV)} (por día del calendario: años con dato, mínimo, percentiles 10, 25, 50, 75 y 90, máximo, el año más bajo y el más alto, y las lecturas de 2026) y {csvm|serie mensual de Goya desde 1903 (CSV)} (media, máximo y días con dato de cada mes de cada año). Nombres de columnas en español; alturas en metros.' ],
		[ 'h2', 'Preguntas frecuentes' ],
		[ 'faq', [
			[ '¿Cuánto llegó a medir el Paraná en Goya en noviembre de 2026?', '6,10 m, del 3 al 5 de noviembre, en la escala de Goya del INA: la mayor altura para un noviembre desde que empieza la serie, en 1903 (el máximo anterior era 5,80 m, en 1998). El 24 de noviembre medía 5,47 m, 0,63 m menos que en el pico: volvió por debajo del nivel de evacuación el 20 de noviembre y sigue 0,27 m sobre el de alerta.' ],
			[ '¿Es la mayor crecida de Goya?', 'No. El récord de la serie es 7,20 m, el 13 de junio de 1992. Lo inusual de 2026 es la época: ningún noviembre en 124 años tuvo el río tan alto.' ],
			[ '¿Qué significa nivel de alerta y nivel de evacuación?', 'Son marcas de referencia de la escala de Goya que publica el INA: 5,20 m (alerta) y 5,70 m (evacuación). No son una orden de evacuación: esa decisión la toma Defensa Civil.' ],
			[ '¿Qué se espera para las próximas semanas?', 'Según el INA, en su reporte diario del 24 de noviembre de 2026: el río seguiría oscilante o con leve descenso en aguas medias altas, con elevada probabilidad de sostenerse por encima de los niveles de alerta y chances de nuevos repuntes más adelante. Su último pronóstico numérico, emitido el 20 de noviembre, daba 5,39 m para el 24/11 (se midieron 5,47 m) y 5,45 m para el 1 de diciembre, con un rango probable de 5,00 a 5,70 m. El INA actualiza los martes y los viernes: lo último está en {ina|Alerta Hidrológico}.' ],
		] ],
		[ 'h2', 'Para medios' ],
		[ 'p', '<strong>Resumen:</strong> el río Paraná alcanzó 6,10 metros en la escala de Goya el 5 de noviembre, la mayor altura para un noviembre en los 124 años de la serie del Instituto Nacional del Agua (INA); el máximo anterior para el mes era 5,80 metros, en 1998. Está sobre el nivel de alerta (5,20 metros) desde el 18 de octubre (38 días seguidos al 24 de noviembre) y estuvo 22 días sobre el de evacuación (5,70 metros), algo que no ocurría desde marzo de 2016. El 24 de noviembre medía 5,47 metros y bajaba lentamente. El INA estima elevada probabilidad de que siga sobre el nivel de alerta, con posibles repuntes. No es la mayor crecida: el récord es 7,20 metros, de 1992.' ],
		[ 'ul', [
			'<strong>6,10 m:</strong> pico del Paraná en Goya el 5/11/2026, la mayor altura para un noviembre desde 1903 (anterior: 5,80 m, 1998). Fuente: INA, escala de Prefectura Naval Argentina, serie diaria de Goya.',
			'<strong>38 días</strong> seguidos sobre el nivel de alerta (5,20 m) al 24/11/2026, y <strong>22 días</strong> sobre el de evacuación (5,70 m, del 29/10 al 19/11), la primera vez desde marzo de 2016. Fuente: INA (niveles y lecturas); cálculo de Pescamapa.',
			'<strong>5,87 m:</strong> media del 1 al 24 de noviembre de 2026, contra 5,51 m de la mayor de un noviembre completo (1997). Fuente: INA; cálculo de Pescamapa.',
		] ],
		[ 'p', 'Al citar, conviene aclarar que los niveles de alerta y de evacuación son referencias de la escala del INA y no una orden de evacuación. Datos y método: en esta nota, con los CSV para descargar.' ],
		[ 'contacto', 'Contacto: ' ],
	],
	'f' => [
		'ina'    => 'Instituto Nacional del Agua (INA): alturas diarias de las escalas de Prefectura Naval Argentina, 1903–2026',
		'diario' => 'INA: reporte diario de la Cuenca del Plata, 24/11/2026',
		'prono'  => 'INA: pronóstico numérico del Paraná, emitido el 20/11/2026',
	],
	'orig' => 'Actualización del 24/11/2026 de la nota del 21/11: lecturas del INA hasta el 24/11/2026 (wordpress/build/rio-historia.py, rama rio-historia-desde-1903, copiada en wordpress/build/rio-nota-noviembre/); gráfico 2 y CSV regenerados con wordpress/build/rio-nota-noviembre-graficos.py (comprueba cada cifra contra esos archivos); gráficos 1 y 3, los del 20/11 (sus picos no cambiaron). Cálculo en docs/borradores/parana-goya-noviembre-2026.md (sección del 24/11). «Qué se espera»: reporte diario del INA del 24/11 (cita literal del tramo Goya/Reconquista - La Paz) y pronóstico numérico del INA emitido el 20/11, que no se había vuelto a emitir en la consulta del 24/11; sin pronóstico propio. Solo INA, sin Itaipú, sin consejos propios, sin pesca. «Desde 1903» supone cero de escala fijo (sin verificar). Lecturas de 2026 sin validar por el INA. El titular se mantiene: el máximo (6,10 m) supera a todos los noviembres anteriores y no puede bajar; la media del mes (5,87 m al 24/11) supera al mayor noviembre completo (5,51 m, 1997) y solo bajaría de eso si el río promediara menos de 4,07 m del 25 al 30.',
];
$N['en'] = [
	'titulo' => 'Goya has never had a November like this: the Paraná at its highest level for the month in 124 years',
	'bajada' => 'The Paraná reached 6.10 m at the Goya gauge on 5 November, the highest for any November since 1903. It has been above the alert level for 38 days (on 24/11 it read 5.47 m) and the INA sees a high probability it will stay above. What happened, why it is unusual and what to expect.',
	'seo_title' => 'Paraná at Goya: the highest November in 124 years',
	'seo_description' => 'The Paraná hit 6.10 m at Goya on 5 Nov 2026, the highest for a November since 1903, and has been above the alert level for 38 days. Data and INA outlook.',
	'datos' => [
		'6.10 m on 5 November 2026: the Paraná’s highest reading at Goya for a November in 124 years of records (the previous one, 5.80 m, in 1998).',
		'38 days in a row above the alert level (5.20 m) as of 24 November, since 18 October; 22 days above the evacuation level (5.70 m), from 29 October to 19 November, which had not happened since March 2016.',
		'Average for 1 to 24 November: 5.87 m. The highest average for a full November so far: 5.51 m (1997).',
		'Latest INA reading, 24 November: 5.47 m, 0.63 m below the peak; about 5 cm a day since 19/11. It is still 0.27 m above the alert level.',
		'It is not the biggest flood: the series record is 7.20 m, on 13 June 1992. What is unusual is the time of year.',
		'The INA (daily report of 24/11) expects the river to keep fluctuating or falling slightly at medium-high levels, with a high probability of staying above the alert level. Its numerical forecast of 20/11 gave 5.39 m for 24/11 (5.47 m was read) and 5.45 m for 1 December.',
	],
	'cuerpo' => [
		[ 'p', '<strong>The Paraná River reached 6.10 m at the Goya gauge on 5 November: the highest level recorded at that port in any November since the National Water Institute (INA) series begins, in 1903. The previous maximum for the month was 5.80 m, in 1998. The river has been above the alert level (5.20 m) for 38 days in a row and spent 22 days above the evacuation level (5.70 m), which had not happened since March 2016. On 24 November it stood at 5.47 m and was slowly falling. The INA sees a high probability that it will stay above the alert level, with possible new rises.</strong>' ],
		[ 'p', '<em>Readings up to 24 November 2026. Data: {ina|National Water Institute (INA)} (Argentine Naval Prefecture gauges). Prepared by Pescamapa.</em>' ],
		[ 'h2', 'What happened' ],
		[ 'p', 'The year began with the river low: until June, 171 of 181 days were below the series median for each date. Then it turned. On 4 September the river stood at 2.57 m; from there it rose, with a pause or two, to 6.10 m on 5 November: 3.53 m in 62 days. It crossed the alert level on 18 October (5.28 m) and the evacuation level on 29 October.' ],
		[ 'p', 'The peak came between 3 and 5 November, at 6.10 m. Since then it has been falling slowly: 5.86 m on 14 November, 5.68 m on 20 November (back below the evacuation level after 22 days in a row above it, from 29 October to 19 November) and 5.47 m on 24 November: 0.63 m less than at the peak. All 24 days of November were above the alert level, and 19 of the 24 above the evacuation level; the average for 1 to 24 November was 5.87 m. So far this year that makes 38 days in a row above the alert level, and the river is still 0.27 m above it.' ],
		[ 'fig', 'dias', 'Daily Paraná level at Goya, from 1 September to 24 November 2026, and the INA forecast issued on 20/11 (shown apart, with its range). Data: INA. Prepared by Pescamapa.' ],
		[ 'p', 'A note on the levels: the alert level (5.20 m) and the evacuation level (5.70 m) are reference marks of the Goya gauge published by the INA. They are not an evacuation order: that is decided by Civil Defence (Defensa Civil).' ],
		[ 'h2', 'Why it is like no other November' ],
		[ 'p', 'Goya has level readings since 1903. In those 124 years no November had the river this high: the 2026 maximum (6.10 m) is above every earlier one, and the previous highest was 1998, at 5.80 m. The average tells the same story: the 2026 average for 1 to 24 November (5.87 m) already exceeds that of any full November; the highest, 5.51 m, was 1997.' ],
		[ 'p', 'The month ends on the 30th, so the average can still move. For it to fall below 5.51 m, the river would have to average under 4.07 m over the six days left (25 to 30 November), that is, more than 1.4 m below the latest reading. The month’s maximum, 6.10 m, can no longer fall.' ],
		[ 'p', 'Only two Novembers in the series passed 5.70 m, the gauge’s evacuation level: 1998 and 2026.' ],
		[ 'fig', 'nunca', 'The highest reading of each November at Goya, 1903 to 2026 (readings to 20/11; the maximum has not changed). Data: INA. Prepared by Pescamapa.' ],
		[ 'p', '<strong>It is not the biggest flood.</strong> The Goya record is 7.20 m, on 13 June 1992, and the 2026 peak is the 11th highest of the 124 years in the series (one peak per year). What is unusual is the season: a river this high in November had not been seen.' ],
		[ 'p', 'The last time the river stood at 6.10 m or more was between 24 December 2015 and 2 February 2016 (maximum 6.46 m). Before this episode, the last time above the evacuation level was March 2016, and above the alert level, 6 November to 1 December 2023 (maximum 5.67 m).' ],
		[ 'h2', 'What to expect' ],
		[ 'p', 'Everything below comes from the INA, with its date; it is not a Pescamapa forecast. The INA updates the middle Paraná numerical forecast on Tuesdays and Fridays: the one cited here was issued on 20 November and, when checked on 24 November, there was no newer one. For the latest, see {ina|Alerta Hidrológico}. Quotes are in the original Spanish.' ],
		[ 'ul', [
			'<strong>INA daily report, 24/11/2026</strong> ({diario|PDF}): «en Goya/Reconquista - La Paz se registran marcas en aguas medias altas, con excedencia del nivel de Alerta en Goya y La Paz, y excedencia de los niveles de Alerta y Evacuación en Reconquista. Este sector del tramo se espera que persista oscilante/leve descenso en aguas medias altas, estabilizando progresivamente hacia plazo extendido y con chances de nuevos repuntes hacia mayor plazo, todavía con elevada probabilidad de sostenerse por encima de los niveles de Alerta» (at Goya/Reconquista - La Paz levels are medium-high, above the Alert level at Goya and La Paz and above the Alert and Evacuation levels at Reconquista; for this stretch it is expected to keep fluctuating or falling slightly at medium-high levels, gradually stabilising over the longer term with chances of new rises further out, still very likely to stay above the Alert levels). For the stretch further up, from Corrientes, the same report expects over 7 days a gradual stabilisation at medium-high levels, with the outlook of at least staying stable or rising gradually, and still a possibility of new rises.',
			'<strong>INA numerical forecast, issued 20/11/2026</strong> ({prono|PDF}): for Goya, 5.39 m on 24 November and 5.45 m on 1 December, with a probable range of 5.00 to 5.70 m on both dates. On 24 November the gauge read 5.47 m (INA daily reading, not yet validated): 0.08 m above the central value and within the range. The forecast for 1 December is still the current one.',
		] ],
		[ 'h2', 'The same water, further downriver' ],
		[ 'p', 'The October flood moved from north to south. It passed Corrientes at 7.42 m on 23 October; reached Goya on 3 November and stood at 6.10 m until the 5th; at Esquina, at 5.60 m, it was 6 and 7 November; at La Paz, at 6.30 m, 14 November; and at Santa Fe it read 5.88 m on 20 November, the highest reading so far. The crest took 11 to 13 days from Corrientes to Goya, and about 22 to La Paz.' ],
		[ 'p', 'All five gauges passed their own evacuation level. With the readings for 1 to 24 November, Esquina and La Paz have the highest average for the month in their series; Corrientes, the highest among full Novembers (and second against the same stretch, 1 to 24); and Santa Fe, the highest maximum.' ],
		[ 'fig', 'abajo', 'The flood crest from Corrientes to Santa Fe: date and height of the peak at each gauge, with its own levels (readings to 20/11; the peaks have not changed). Data: INA. Prepared by Pescamapa.' ],
		[ 'h2', 'Where to look' ],
		[ 'ul', [
			'Levels, daily report, weekly report and forecasts: the {ina|INA Hydrological Alert (Alerta Hidrológico)}.',
			'River status, from the {pna|Argentine Naval Prefecture} (the gauges the data comes from).',
			'Municipality of Goya: {coe|Emergency Operations Committee (COE)}.',
		] ],
		[ 'p', 'Guidance during floods comes from Civil Defence and the COE: this note does not replace it. Today’s level is updated every day at {r|River level at Goya today}.' ],
		[ 'h2', 'How we calculated it' ],
		[ 'ul', [
			'<strong>Source:</strong> daily readings of the Goya gauge published by the {ina|National Water Institute} (INA), from the Argentine Naval Prefecture gauges, 1903 to 2026: one value per day (the average of the day’s readings, local time). The 2026 readings come from the INA and have not been validated yet.',
			'<strong>“Since 1903”</strong> is the INA daily series for the Goya gauge, and assumes the gauge zero did not change in over a century (the INA catalogue gives a fixed zero; we could not verify it).',
			'<strong>Levels:</strong> alert 5.20 m and evacuation 5.70 m, as published by the INA for this gauge.',
			'<strong>Incomplete November:</strong> 2026 has 24 of the 30 days, so it is compared in two ways: against the full Novembers of every year (with data on at least 90 % of the days) and against the same stretch, 1 to 24 November, of every year. In both, 2026 ranks first in average and in maximum. In the first chart, a hollow dot marks a November with incomplete data.',
			'<strong>An episode</strong> is consecutive days at or above a value.',
			'<strong>What it is not:</strong> it is gauge height, not depth, and every gauge has its own zero: between stations we compare dates, not heights.',
		] ],
		[ 'h2', 'Use the charts and the data' ],
		[ 'p', 'You can use these charts and numbers if you credit Pescamapa with a link to this note.' ],
		[ 'p', 'Data to download (as of 24 November): {csvd|Goya daily statistics, 1903–2026 (CSV)} (by calendar day: years with data, minimum, 10th, 25th, 50th, 75th and 90th percentiles, maximum, the lowest and the highest year, and the 2026 readings) and {csvm|Goya monthly series since 1903 (CSV)} (average, maximum and days with data for each month of each year). Column names in Spanish; heights in metres.' ],
		[ 'h2', 'Frequently asked questions' ],
		[ 'faq', [
			[ 'How high did the Paraná get at Goya in November 2026?', '6.10 m, from 3 to 5 November, at the INA’s Goya gauge: the highest level for a November since the series begins, in 1903 (the previous maximum was 5.80 m, in 1998). On 24 November it stood at 5.47 m, 0.63 m below the peak: it went back below the evacuation level on 20 November and is still 0.27 m above the alert level.' ],
			[ 'Is it Goya’s biggest flood?', 'No. The series record is 7.20 m, on 13 June 1992. What is unusual in 2026 is the season: no November in 124 years had the river this high.' ],
			[ 'What do the alert level and the evacuation level mean?', 'They are reference marks of the Goya gauge published by the INA: 5.20 m (alert) and 5.70 m (evacuation). They are not an evacuation order: that decision is taken by Civil Defence (Defensa Civil).' ],
			[ 'What is expected for the coming weeks?', 'According to the INA, in its daily report of 24 November 2026: the river would keep fluctuating or falling slightly at medium-high levels, with a high probability of staying above the alert levels and chances of new rises later on. Its latest numerical forecast, issued on 20 November, gave 5.39 m for 24/11 (5.47 m was read) and 5.45 m for 1 December, with a probable range of 5.00 to 5.70 m. The INA updates on Tuesdays and Fridays: the latest is at {ina|Alerta Hidrológico}.' ],
		] ],
		[ 'h2', 'For the media' ],
		[ 'p', '<strong>Summary:</strong> the Paraná River reached 6.10 metres at the Goya gauge on 5 November, the highest level for a November in the 124 years of the National Water Institute (INA) series; the previous maximum for the month was 5.80 metres, in 1998. It has been above the alert level (5.20 metres) since 18 October (38 days in a row as of 24 November) and spent 22 days above the evacuation level (5.70 metres), which had not happened since March 2016. On 24 November it stood at 5.47 metres and was slowly falling. The INA sees a high probability that it will stay above the alert level, with possible new rises. It is not the biggest flood: the record is 7.20 metres, from 1992.' ],
		[ 'ul', [
			'<strong>6.10 m:</strong> Paraná peak at Goya on 5/11/2026, the highest level for a November since 1903 (previous: 5.80 m, 1998). Source: INA, Argentine Naval Prefecture gauge, daily series for Goya.',
			'<strong>38 days</strong> in a row above the alert level (5.20 m) as of 24/11/2026, and <strong>22 days</strong> above the evacuation level (5.70 m, 29/10 to 19/11), the first time since March 2016. Source: INA (levels and readings); calculation by Pescamapa.',
			'<strong>5.87 m:</strong> average for 1 to 24 November 2026, against 5.51 m for the highest full November (1997). Source: INA; calculation by Pescamapa.',
		] ],
		[ 'p', 'When quoting, please note that the alert and evacuation levels are INA gauge reference marks and not an evacuation order. Data and method: in this note, with the CSV files to download.' ],
		[ 'contacto', 'Contact: ' ],
	],
	'f' => [
		'ina'    => 'National Water Institute (INA): daily readings of the Argentine Naval Prefecture gauges, 1903–2026',
		'diario' => 'INA: daily report for the Río de la Plata basin, 24/11/2026',
		'prono'  => 'INA: numerical forecast for the Paraná, issued 20/11/2026',
	],
];
$N['pt'] = [
	'titulo' => 'Goya nunca teve um novembro assim: o Paraná, no nível mais alto para o mês em 124 anos',
	'bajada' => 'O Paraná chegou a 6,10 m na escala de Goya em 5 de novembro, o máximo para um novembro desde 1903. Está há 38 dias acima do nível de alerta (em 24/11 marcava 5,47 m) e o INA vê alta probabilidade de que continue acima. O que aconteceu, por que é incomum e o que se espera.',
	'seo_title' => 'Paraná em Goya: o novembro mais alto em 124 anos',
	'seo_description' => 'O Paraná chegou a 6,10 m em Goya em 5/11/2026, o máximo para um novembro desde 1903. Há 38 dias acima do nível de alerta. Dados e o que prevê o INA.',
	'datos' => [
		'6,10 m em 5 de novembro de 2026: o máximo do Paraná em Goya para um novembro em 124 anos de registros (o anterior, 5,80 m, em 1998).',
		'38 dias seguidos acima do nível de alerta (5,20 m) até 24 de novembro, desde 18 de outubro; 22 dias acima do de evacuação (5,70 m), de 29 de outubro a 19 de novembro, algo que não ocorria desde março de 2016.',
		'Média de 1 a 24 de novembro: 5,87 m. A maior média de um novembro completo, até agora: 5,51 m (1997).',
		'Última leitura do INA, 24 de novembro: 5,47 m, 0,63 m abaixo do pico; cerca de 5 cm por dia desde 19/11. Continua 0,27 m acima do nível de alerta.',
		'Não é a maior cheia: o recorde da série é 7,20 m, em 13 de junho de 1992. O incomum é a época do ano.',
		'O INA (relatório diário de 24/11) espera que o rio continue oscilando ou baixando levemente em águas médias altas, com alta probabilidade de seguir acima do nível de alerta. Sua previsão numérica de 20/11 dava 5,39 m para 24/11 (foram medidos 5,47 m) e 5,45 m para 1.º de dezembro.',
	],
	'cuerpo' => [
		[ 'p', '<strong>O rio Paraná chegou a 6,10 m na escala de Goya em 5 de novembro: é o nível mais alto registrado nesse porto em um mês de novembro desde que começa a série do Instituto Nacional da Água (INA), em 1903. O máximo anterior para o mês era 5,80 m, de 1998. Está há 38 dias seguidos acima do nível de alerta (5,20 m) e ficou 22 dias acima do de evacuação (5,70 m), algo que não ocorria desde março de 2016. Em 24 de novembro marcava 5,47 m e baixava aos poucos. O INA estima alta probabilidade de que continue acima do nível de alerta, com possíveis novas subidas.</strong>' ],
		[ 'p', '<em>Leituras até 24 de novembro de 2026. Dados: {ina|Instituto Nacional da Água (INA)} (escalas da Prefeitura Naval Argentina). Elaboração: Pescamapa.</em>' ],
		[ 'h2', 'O que aconteceu' ],
		[ 'p', 'O ano começou com o rio baixo: até junho, 171 dos 181 dias ficaram abaixo da mediana da série para cada data. Depois virou. Em 4 de setembro o rio marcava 2,57 m; dali subiu, com uma ou outra pausa, até os 6,10 m de 5 de novembro: 3,53 m em 62 dias. Cruzou o nível de alerta em 18 de outubro (5,28 m) e o de evacuação em 29 de outubro.' ],
		[ 'p', 'O pico veio entre 3 e 5 de novembro, com 6,10 m. Desde então baixa aos poucos: 5,86 m em 14 de novembro, 5,68 m em 20 (voltando a ficar abaixo do nível de evacuação, depois de 22 dias seguidos acima, de 29 de outubro a 19 de novembro) e 5,47 m em 24: 0,63 m a menos que no pico. Os 24 dias de novembro ficaram acima do nível de alerta, e 19 dos 24, acima do de evacuação; a média de 1 a 24 foi 5,87 m. No ano são 38 dias seguidos acima do alerta, e o rio segue 0,27 m acima desse nível.' ],
		[ 'fig', 'dias', 'Nível diário do Paraná em Goya, de 1.º de setembro a 24 de novembro de 2026, e previsão do INA emitida em 20/11 (à parte, com sua faixa). Dados: INA. Elaboração: Pescamapa.' ],
		[ 'p', 'Um esclarecimento sobre os níveis: o nível de alerta (5,20 m) e o de evacuação (5,70 m) são marcas de referência da escala de Goya publicadas pelo INA. Não são uma ordem de evacuação: isso quem decide é a Defesa Civil.' ],
		[ 'h2', 'Por que não se parece com nenhum novembro' ],
		[ 'p', 'Goya tem leituras de nível desde 1903. Nesses 124 anos nenhum novembro teve o rio tão alto: o máximo de 2026 (6,10 m) supera o de todos os anteriores, e o maior era o de 1998, com 5,80 m. Com a média acontece o mesmo: a de 2026 de 1 a 24 de novembro (5,87 m) já supera a de qualquer novembro completo; a maior, 5,51 m, era a de 1997.' ],
		[ 'p', 'O mês termina no dia 30, então a média ainda pode se mexer. Para cair abaixo de 5,51 m, o rio teria de ter média inferior a 4,07 m nos seis dias que faltam (25 a 30), isto é, ficar mais de 1,4 m abaixo da última leitura. O máximo do mês, 6,10 m, já não pode baixar.' ],
		[ 'p', 'Só dois novembros da série passaram dos 5,70 m, o nível de evacuação da escala: 1998 e 2026.' ],
		[ 'fig', 'nunca', 'A máxima de cada novembro em Goya, de 1903 a 2026 (leituras até 20/11; o máximo não mudou). Dados: INA. Elaboração: Pescamapa.' ],
		[ 'p', '<strong>Não é a maior cheia.</strong> O recorde de Goya é 7,20 m, em 13 de junho de 1992, e o pico de 2026 é o 11.º mais alto dos 124 anos da série (um pico por ano). O incomum é a época: um rio tão alto em novembro não se tinha visto.' ],
		[ 'p', 'A última vez que o rio esteve em 6,10 m ou mais foi entre 24 de dezembro de 2015 e 2 de fevereiro de 2016 (máximo, 6,46 m). Antes deste episódio, a última vez acima do nível de evacuação foi em março de 2016, e acima do de alerta, de 6 de novembro a 1.º de dezembro de 2023 (máximo, 5,67 m).' ],
		[ 'h2', 'O que se espera' ],
		[ 'p', 'Tudo o que segue é do INA, com sua data; não é uma previsão da Pescamapa. O INA atualiza a previsão numérica do Paraná médio às terças e sextas: a citada aqui foi emitida em 20 de novembro e, na consulta de 24 de novembro, ainda não havia uma nova. Para o mais recente, consulte o {ina|Alerta Hidrológico}. As citações estão no espanhol original.' ],
		[ 'ul', [
			'<strong>Relatório diário do INA, 24/11/2026</strong> ({diario|PDF}): «en Goya/Reconquista - La Paz se registran marcas en aguas medias altas, con excedencia del nivel de Alerta en Goya y La Paz, y excedencia de los niveles de Alerta y Evacuación en Reconquista. Este sector del tramo se espera que persista oscilante/leve descenso en aguas medias altas, estabilizando progresivamente hacia plazo extendido y con chances de nuevos repuntes hacia mayor plazo, todavía con elevada probabilidad de sostenerse por encima de los niveles de Alerta» (em Goya/Reconquista - La Paz os níveis são médios altos, acima do nível de Alerta em Goya e La Paz e acima dos níveis de Alerta e Evacuação em Reconquista; para este trecho espera-se que continue oscilando ou baixando levemente em águas médias altas, estabilizando-se aos poucos no prazo mais longo, com chances de novas subidas mais adiante, ainda com elevada probabilidade de ficar acima dos níveis de Alerta). Para o trecho mais acima, a partir de Corrientes, o mesmo relatório prevê em 7 dias uma estabilização gradual em águas médias altas, com perspectiva de pelo menos continuar estável ou subir aos poucos, e ainda possibilidade de novas subidas.',
			'<strong>Previsão numérica do INA, emitida em 20/11/2026</strong> ({prono|PDF}): para Goya, 5,39 m em 24 de novembro e 5,45 m em 1.º de dezembro, com faixa provável de 5,00 a 5,70 m nas duas datas. Em 24 de novembro a escala marcou 5,47 m (leitura diária do INA, ainda não validada): 0,08 m acima do valor central e dentro da faixa. A previsão de 1.º de dezembro continua vigente.',
		] ],
		[ 'h2', 'A mesma água, rio abaixo' ],
		[ 'p', 'A cheia de outubro se moveu de norte a sul. Passou por Corrientes com 7,42 m em 23 de outubro; chegou a Goya em 3 de novembro e marcou 6,10 m até o dia 5; em Esquina, com 5,60 m, foi em 6 e 7 de novembro; em La Paz, com 6,30 m, em 14 de novembro; e em Santa Fe marcou 5,88 m em 20 de novembro, a leitura mais alta até agora. De Corrientes a Goya a crista levou de 11 a 13 dias, e cerca de 22 até La Paz.' ],
		[ 'p', 'As cinco escalas passaram do seu próprio nível de evacuação. Com as leituras de 1 a 24 de novembro, Esquina e La Paz têm a maior média do mês da sua série; Corrientes, a maior entre os novembros completos (e a segunda contra o mesmo trecho, de 1 a 24); e Santa Fe, o maior máximo.' ],
		[ 'fig', 'abajo', 'A crista da cheia, de Corrientes a Santa Fe: data e altura do pico em cada escala, com seus próprios níveis (leituras até 20/11; os picos não mudaram). Dados: INA. Elaboração: Pescamapa.' ],
		[ 'h2', 'Onde consultar' ],
		[ 'ul', [
			'Níveis, relatório diário, informe semanal e previsões: o {ina|Alerta Hidrológico do INA}.',
			'Estado dos rios, da {pna|Prefeitura Naval Argentina} (as escalas de onde sai o dado).',
			'Município de Goya: {coe|Comitê Operativo de Emergência (COE)}.',
		] ],
		[ 'p', 'As orientações diante de cheias são dadas pela Defesa Civil e pelo COE: esta nota não as substitui. O nível de hoje é atualizado todos os dias em {r|Nível do rio em Goya hoje}.' ],
		[ 'h2', 'Como calculamos' ],
		[ 'ul', [
			'<strong>Fonte:</strong> leituras diárias da escala de Goya publicadas pelo {ina|Instituto Nacional da Água} (INA), das escalas da Prefeitura Naval Argentina, de 1903 a 2026: um valor por dia (a média das leituras do dia, hora local). As leituras de 2026 são do INA e ainda não foram validadas.',
			'<strong>«Desde 1903»</strong> é a série diária do INA para a escala de Goya, e supõe que o zero da escala não mudou em mais de um século (o catálogo do INA dá um zero fixo; não conseguimos verificar).',
			'<strong>Níveis:</strong> alerta 5,20 m e evacuação 5,70 m, os que o INA publica para essa escala.',
			'<strong>Novembro incompleto:</strong> 2026 tem 24 dos 30 dias, então se compara de duas formas: com os novembros completos de cada ano (com dado em pelo menos 90 % dos dias) e com o mesmo trecho, de 1 a 24 de novembro, de cada ano. Nas duas, 2026 fica em primeiro em média e em máximo. No primeiro gráfico, o ponto vazado marca um novembro com dados incompletos.',
			'<strong>Um episódio</strong> são dias seguidos em ou acima de um valor.',
			'<strong>O que não é:</strong> é altura de escala, não profundidade, e cada escala tem o seu próprio zero: de uma estação a outra se comparam as datas, não as alturas.',
		] ],
		[ 'h2', 'Use os gráficos e os dados' ],
		[ 'p', 'Você pode usar estes gráficos e números citando a Pescamapa com link para esta nota.' ],
		[ 'p', 'Dados para baixar (em 24 de novembro): {csvd|estatística diária de Goya, 1903–2026 (CSV)} (por dia do calendário: anos com dado, mínimo, percentis 10, 25, 50, 75 e 90, máximo, o ano mais baixo e o mais alto, e as leituras de 2026) e {csvm|série mensal de Goya desde 1903 (CSV)} (média, máximo e dias com dado de cada mês de cada ano). Nomes das colunas em espanhol; alturas em metros.' ],
		[ 'h2', 'Perguntas frequentes' ],
		[ 'faq', [
			[ 'Quanto chegou a medir o Paraná em Goya em novembro de 2026?', '6,10 m, de 3 a 5 de novembro, na escala de Goya do INA: o maior nível para um novembro desde que começa a série, em 1903 (o máximo anterior era 5,80 m, em 1998). Em 24 de novembro marcava 5,47 m, 0,63 m a menos que no pico: voltou a ficar abaixo do nível de evacuação em 20 de novembro e segue 0,27 m acima do de alerta.' ],
			[ 'É a maior cheia de Goya?', 'Não. O recorde da série é 7,20 m, em 13 de junho de 1992. O incomum em 2026 é a época: nenhum novembro em 124 anos teve o rio tão alto.' ],
			[ 'O que significam nível de alerta e nível de evacuação?', 'São marcas de referência da escala de Goya publicadas pelo INA: 5,20 m (alerta) e 5,70 m (evacuação). Não são uma ordem de evacuação: essa decisão é da Defesa Civil.' ],
			[ 'O que se espera para as próximas semanas?', 'Segundo o INA, no relatório diário de 24 de novembro de 2026: o rio continuaria oscilando ou baixando levemente em águas médias altas, com alta probabilidade de ficar acima dos níveis de alerta e chances de novas subidas mais adiante. Sua última previsão numérica, emitida em 20 de novembro, dava 5,39 m para 24/11 (foram medidos 5,47 m) e 5,45 m para 1.º de dezembro, com faixa provável de 5,00 a 5,70 m. O INA atualiza às terças e sextas: o mais recente está no {ina|Alerta Hidrológico}.' ],
		] ],
		[ 'h2', 'Para a imprensa' ],
		[ 'p', '<strong>Resumo:</strong> o rio Paraná alcançou 6,10 metros na escala de Goya em 5 de novembro, o maior nível para um novembro nos 124 anos da série do Instituto Nacional da Água (INA); o máximo anterior para o mês era 5,80 metros, em 1998. Está acima do nível de alerta (5,20 metros) desde 18 de outubro (38 dias seguidos até 24 de novembro) e ficou 22 dias acima do de evacuação (5,70 metros), algo que não ocorria desde março de 2016. Em 24 de novembro marcava 5,47 metros e baixava lentamente. O INA estima alta probabilidade de que continue acima do nível de alerta, com possíveis novas subidas. Não é a maior cheia: o recorde é 7,20 metros, de 1992.' ],
		[ 'ul', [
			'<strong>6,10 m:</strong> pico do Paraná em Goya em 5/11/2026, o maior nível para um novembro desde 1903 (anterior: 5,80 m, 1998). Fonte: INA, escala da Prefeitura Naval Argentina, série diária de Goya.',
			'<strong>38 dias</strong> seguidos acima do nível de alerta (5,20 m) até 24/11/2026, e <strong>22 dias</strong> acima do de evacuação (5,70 m, de 29/10 a 19/11), a primeira vez desde março de 2016. Fonte: INA (níveis e leituras); cálculo da Pescamapa.',
			'<strong>5,87 m:</strong> média de 1 a 24 de novembro de 2026, contra 5,51 m da maior de um novembro completo (1997). Fonte: INA; cálculo da Pescamapa.',
		] ],
		[ 'p', 'Ao citar, convém esclarecer que os níveis de alerta e de evacuação são referências da escala do INA e não uma ordem de evacuação. Dados e método: nesta nota, com os CSV para baixar.' ],
		[ 'contacto', 'Contato: ' ],
	],
	'f' => [
		'ina'    => 'Instituto Nacional da Água (INA): leituras diárias das escalas da Prefeitura Naval Argentina, 1903–2026',
		'diario' => 'INA: relatório diário da Bacia do Prata, 24/11/2026',
		'prono'  => 'INA: previsão numérica do Paraná, emitida em 20/11/2026',
	],
];

// ---- Attachments (own work: credit "Pescamapa"); same idempotent loader as the 21/11 script ----
$adj   = [];
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
	wp_update_post( [ 'ID' => $att, 'post_content' => "Elaboración propia de Pescamapa ($hoy), hecha con wordpress/build/rio-nota-noviembre-graficos.py (PNG) o rio-historia.py (CSV) a partir de la lectura del INA del 24/11/2026." ] );
	return (int) $att;
};
foreach ( $A as $k => $a ) {
	$adj[ $k ] = $subir( $a['archivo'], $a['titulo'], $a['alt'] );
}
$adj['csvd'] = $subir( $CSV_D, 'Paraná en Goya: estadística diaria 1903–2026 (CSV, al 24/11)', null );
$adj['csvm'] = $subir( $CSV_M, 'Paraná en Goya: serie mensual desde 1903 (CSV, al 24/11)', null );
$url_de  = fn( int $id ) => $id ? (string) wp_get_attachment_url( $id ) : '';
$url_img = fn( int $id ) => $id ? ( (string) wp_get_attachment_image_url( $id, 'large' ) ?: $url_de( $id ) ) : '';

// The attachments of the 21/11 version that this one replaces (left in the library, unused after this run).
$viejos = [];
foreach ( [ 'parana-goya-el-rio-dia-por-dia.png', 'parana-goya-estadistica-diaria-1903-2026.csv', 'parana-goya-mensual-1903-2026.csv' ] as $f ) {
	$q = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $f, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( $q ) {
		$viejos[ $f ] = (int) $q[0];
	}
}

// ---- Link resolver ----
$res = function ( string $html, string $i ) use ( $ENL, $adj, $url_de ): string {
	return preg_replace_callback( '/\{([a-z]+)\|([^}]+)\}/u', function ( $m ) use ( $i, $ENL, $adj, $url_de ) {
		[ , $tipo, $txt ] = $m;
		if ( isset( $ENL[ $tipo ] ) ) {
			return '<a href="' . esc_url( $ENL[ $tipo ] ) . '" rel="noopener">' . $txt . '</a>';
		}
		if ( 'r' === $tipo ) {
			return '<a href="' . esc_url( pm_url_rio_punto( 'goya', $i ) ) . '">' . $txt . '</a>';
		}
		if ( 'csvd' === $tipo || 'csvm' === $tipo ) {
			return '<a href="' . esc_url( $url_de( $adj[ $tipo ] ) ?: '#csv-pendiente-en-simulacro' ) . '" download>' . $txt . '</a>';
		}
		return $m[0]; // left as is: the unresolved-marker check below stops the run
	}, $html );
};

// ---- Body builder ----
$construir = function ( array $n, string $i ) use ( $res, $adj, $A, $url_img ): string {
	$out = '';
	foreach ( $n['cuerpo'] as $b ) {
		switch ( $b[0] ) {
			case 'h2':
				$out .= pm_pr_h2( $b[1] );
				break;
			case 'p':
				$out .= pm_pr_p( $res( $b[1], $i ) );
				break;
			case 'ul':
				$out .= pm_pr_lista( array_map( fn( $x ) => $res( $x, $i ), $b[1] ) );
				break;
			case 'fig':
				$out .= pm_pr_figura( $adj[ $b[1] ], $url_img( $adj[ $b[1] ] ) ?: '#imagen-pendiente-en-simulacro', $A[ $b[1] ]['alt'][ $i ], esc_html( $b[2] ) );
				break;
			case 'faq':
				$out .= pm_pr_faq( array_map( fn( $q ) => [ $q[0], $res( $q[1], $i ) ], $b[1] ) );
				break;
			case 'contacto':
				if ( defined( 'PM_EMAIL_PUBLICO' ) ) {
					$out .= pm_pr_p( esc_html( $b[1] ) . '<a href="mailto:' . esc_attr( PM_EMAIL_PUBLICO ) . '">' . esc_html( PM_EMAIL_PUBLICO ) . '</a>' );
				}
				break;
		}
	}
	return $out;
};

// ---- Find the three rows ----
$filas = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$q = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i ) );
	count( $q ) === 1 || WP_CLI::error( "No encuentro exactamente una nota $clave en $i (hay " . count( $q ) . ').' );
	$filas[ $i ] = $q[0];
}

// Figures that must be in each language's body (new numbers) and that must be gone (old ones).
$debe = [
	'es' => [ '5,47 m', '5,87 m', '38 días', '0,63 m', '4,07 m', '24 de los 30 días' ],
	'en' => [ '5.47 m', '5.87 m', '38 days', '0.63 m', '4.07 m', '24 of the 30 days' ],
	'pt' => [ '5,47 m', '5,87 m', '38 dias', '0,63 m', '4,07 m', '24 dos 30 dias' ],
];
$no_debe = [
	'es' => [ '34 días', '5,93 m', '20 de los 30 días', 'reporte_diario_2026-11-20' ],
	'en' => [ '34 days', '5.93 m', '20 of the 30 days', 'reporte_diario_2026-11-20' ],
	'pt' => [ '34 dias', '5,93 m', '20 dos 30 dias', 'reporte_diario_2026-11-20' ],
];

$palabras_malas = [ 'operador', 'operator', 'operadora', 'vosotros' ];
$antes          = [];
$plan           = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p  = $filas[ $i ];
	$n  = $N[ $i ];
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	count( $n['datos'] ) === 6 || WP_CLI::error( "$i: los datos clave deben ser 6" );
	$cuerpo = $construir( $n, $i );
	! preg_match( '/\{[a-z0-9:|-]+/', $cuerpo, $m ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	foreach ( $palabras_malas as $mala ) {
		false === stripos( $cuerpo, $mala ) || WP_CLI::error( "$i: palabra no permitida «$mala»" );
	}
	foreach ( $marcas_cta as $mc ) {
		false === strpos( $cuerpo, $mc ) || WP_CLI::error( "$i: el cuerpo contiene un marcador comercial ($mc)" );
	}
	foreach ( $debe[ $i ] as $x ) {
		false !== strpos( $cuerpo . ' ' . implode( ' ', $n['datos'] ), $x ) || WP_CLI::error( "$i: falta la cifra nueva «$x»" );
	}
	foreach ( $no_debe[ $i ] as $x ) {
		false === strpos( $cuerpo . ' ' . implode( ' ', $n['datos'] ) . ' ' . $n['bajada'] . ' ' . $n['seo_description'], $x ) || WP_CLI::error( "$i: queda la cifra vieja «$x»" );
	}
	$nf = count( pm_faqs_contenido( $cuerpo ) );
	$nf === 4 || WP_CLI::error( "$i: la FAQ debe leerse con 4 preguntas (leo $nf)" );
	$antes[ $i ] = [
		'titulo' => $p->post_title, 'slug' => $p->post_name, 'palabras' => str_word_count( wp_strip_all_tags( $p->post_content ) ),
		'datos'  => count( pm_filas( 'nota_datos', $p->ID ) ), 'faq' => count( pm_faqs_contenido( $p->post_content ) ), 'fuentes' => count( pm_filas( 'fuentes', $p->ID ) ),
		'seo_title' => (string) pm_campo( 'seo_title', $p->ID ), 'actualizado' => (string) pm_campo( 'actualizado', $p->ID ),
	];
	$plan[ $i ] = [ 'cuerpo' => $cuerpo, 'tl' => $tl, 'dl' => $dl, 'palabras' => str_word_count( wp_strip_all_tags( $cuerpo ) ) ];
	$mismo = $p->post_title === $n['titulo'] ? 'titular igual' : 'TITULAR DISTINTO (no se toca)';
	WP_CLI::log( "ANTES  [$i] #{$p->ID} «{$antes[$i]['titulo']}» /{$antes[$i]['slug']}/ — {$antes[$i]['palabras']} palabras, {$antes[$i]['datos']} datos, {$antes[$i]['faq']} FAQ, {$antes[$i]['fuentes']} fuentes, actualizado {$antes[$i]['actualizado']}" );
	WP_CLI::log( "DESPUÉS[$i] ($mismo; slug sin cambio) — {$plan[$i]['palabras']} palabras, 6 datos, 4 FAQ, " . count( $n['f'] ) . " fuentes, actualizado $hoy; SEO description $dl car." );
}
$es = $filas['es'];
$antes_es = [
	'fecha' => (string) pm_campo( 'nota_fecha', $es->ID ), 'tipo' => (string) pm_campo( 'nota_tipo', $es->ID ), 'destino' => (string) pm_campo( 'nota_destino', $es->ID ),
	'informativa' => (bool) pm_campo( 'nota_informativa', $es->ID ), 'foto' => (int) get_post_thumbnail_id( $es->ID ),
];
WP_CLI::log( 'es: tipo ' . $antes_es['tipo'] . ', destino ' . $antes_es['destino'] . ', fecha ' . $antes_es['fecha'] . ' (no se toca), informativa ' . ( $antes_es['informativa'] ? 'sí (no se toca)' : 'NO' ) . ', portada #' . $antes_es['foto'] . ' (no se toca)' );
WP_CLI::log( 'Adjuntos de la versión anterior que quedan sin uso en la biblioteca (no se borran): ' . ( $viejos ? implode( ', ', array_map( fn( $f, $id ) => "$f #$id", array_keys( $viejos ), $viejos ) ) : 'ninguno encontrado' ) );

$pedir = function ( string $url ) {
	$r = wp_remote_get( add_query_arg( 'pm_verif', (string) time(), $url ), [ 'timeout' => 30, 'redirection' => 0 ] );
	if ( is_wp_error( $r ) ) {
		return [ 0, $r->get_error_message(), '' ];
	}
	return [ (int) wp_remote_retrieve_response_code( $r ), '', wp_remote_retrieve_body( $r ) ];
};

if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado: no se escribió nada. Con aplicar=true: ' . count( array_filter( [ $adj['dias'], $adj['csvd'], $adj['csvm'] ], fn( $x ) => ! $x ) ) . ' adjuntos nuevos (los que falten), 3 notas actualizadas (contenido, bajada, datos clave, fuentes, SEO description, actualizado); slugs, titular, fecha, tipo, destino, informativa y portada sin tocar.' );
	return;
}

// ---- Apply ----
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = $filas[ $i ];
	$n = $N[ $i ];
	$id = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $plan[ $i ]['cuerpo'] ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$fuentes = [];
	foreach ( [ 'ina', 'diario', 'prono' ] as $k ) {
		$fuentes[] = [ 'nombre' => $n['f'][ $k ], 'url' => $ENL[ $k ], 'oficial' => 1, 'consultada' => $lectura ];
	}
	$campos = [ 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ), 'fuentes' => $fuentes, 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos['nota_origen'] = $N['es']['orig'];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	clean_post_cache( $id );
	WP_CLI::log( "APLICA: [$i] #$id actualizada /" . get_post_field( 'post_name', $id ) . '/' );
}

// ---- Read-back (DB) and render checks ----
$todo_ok = true;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$id   = $filas[ $i ]->ID;
	$n    = $N[ $i ];
	$cont = get_post_field( 'post_content', $id );
	$faqs = pm_faqs_contenido( $cont );
	$ok   = get_the_title( $id ) === $antes[ $i ]['titulo'] && get_post_field( 'post_name', $id ) === $antes[ $i ]['slug']
		&& count( pm_filas( 'fuentes', $id ) ) === 3 && count( pm_filas( 'nota_datos', $id ) ) === 6 && count( $faqs ) === 4
		&& (string) pm_campo( 'actualizado', $id ) !== '' && (string) pm_campo( 'bajada', $id ) === $n['bajada']
		&& (string) pm_campo( 'seo_description', $id ) === $n['seo_description']
		&& false !== strpos( $cont, pm_url_rio_punto( 'goya', $i ) ) && false !== strpos( $cont, esc_url( $url_de( $adj['csvd'] ) ) ) && false !== strpos( $cont, esc_url( $url_de( $adj['csvm'] ) ) );
	foreach ( $debe[ $i ] as $x ) {
		$ok = $ok && false !== strpos( $cont, $x );
	}
	foreach ( $no_debe[ $i ] as $x ) {
		$ok = $ok && false === strpos( $cont, $x );
	}
	$faq_txt = wp_json_encode( $faqs, JSON_UNESCAPED_UNICODE );
	$ok      = $ok && false !== strpos( (string) $faq_txt, 'en' === $i ? '5.47 m' : '5,47 m' );
	foreach ( [ 'nunca', 'dias', 'abajo' ] as $k ) {
		$ok = $ok && 1 === preg_match( '/wp-image-' . $adj[ $k ] . '\b/', $cont );
	}
	foreach ( $viejos as $vid ) {
		$ok = $ok && 0 === preg_match( '/wp-image-' . $vid . '\b/', $cont ) && false === strpos( $cont, '/' . basename( (string) get_post_meta( $vid, '_wp_attached_file', true ) ) );
	}
	$todo_ok = $todo_ok && $ok;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": contenido [$i] #$id «" . get_the_title( $id ) . '» (cifras nuevas, sin las viejas, adjunto nuevo presente y viejos ausentes, FAQ leída con ' . count( $faqs ) . ' preguntas, slug sin cambio)' );
}
$nota_es = pm_nota( $filas['es']->ID );
$ok_es   = $antes_es['destino'] === $nota_es['destino'] && 'goya' === $nota_es['destino'] && $antes_es['tipo'] === $nota_es['tipo'] && 'datos' === $nota_es['tipo'] && true === $nota_es['informativa']
	&& (int) $nota_es['foto'] === $antes_es['foto'] && $antes_es['fecha'] === (string) pm_campo( 'nota_fecha', $filas['es']->ID ) && '20261124' === str_replace( '-', '', (string) pm_campo( 'actualizado', $filas['es']->ID ) );
$todo_ok = $todo_ok && $ok_es;
WP_CLI::log( ( $ok_es ? 'VERIFIED' : 'FALLÓ' ) . ": fila es sin cambios en destino {$nota_es['destino']}, tipo {$nota_es['tipo']}, informativa " . ( $nota_es['informativa'] ? 'sí' : 'NO' ) . ", fecha {$nota_es['fecha']}, portada #{$nota_es['foto']}; actualizado " . pm_campo( 'actualizado', $filas['es']->ID ) );

$render_ok = true;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$id  = $filas[ $i ]->ID;
	$url = get_permalink( $id );
	[ $cod, $err, $html ] = $pedir( $url );
	if ( 200 !== $cod ) {
		WP_CLI::log( "RENDER [$i]: no se pudo verificar $url (" . ( $err ?: "HTTP $cod" ) . ')' );
		$render_ok = false;
		continue;
	}
	$estan = array_values( array_filter( $marcas_cta, fn( $m ) => false !== strpos( $html, $m ) ) );
	$r_ok  = false !== strpos( $html, esc_html( $N[ $i ]['titulo'] ) ) && ! $estan && false !== strpos( $html, 'class="prose"' ) && 1 === preg_match( '/wp-image-' . $adj['dias'] . '\b/', $html );
	$render_ok = $render_ok && $r_ok;
	WP_CLI::log( ( $r_ok ? 'VERIFIED' : 'FALLÓ' ) . ": render [$i] $url con el titular, el gráfico nuevo y sin CTA" . ( $estan ? ' (siguen: ' . implode( ', ', $estan ) . ')' : '' ) );
}
( $todo_ok && $render_ok ) || WP_CLI::error( 'Alguna lectura de vuelta falló: revisar las líneas FALLÓ / RENDER de arriba.' );
WP_CLI::success( 'Aplicado: nota parana-goya actualizada al 24/11 en es, en y pt (slugs, titular, fecha, tipo, destino, informativa y portada sin cambio).' );
