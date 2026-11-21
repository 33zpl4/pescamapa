<?php
/**
 * REPLACES the live data note "El Paraná en Goya" (clave parana-goya-20-noviembre-2026; es #870, en #871, pt #872) in place, 21/11/2026
 * (owner, 20-21/11/2026): the note stops being about "20 de noviembre" and becomes the story of the month, "Goya nunca tuvo un noviembre
 * así" (peak 6,10 m on 5/11, the highest for any November since the INA series starts in 1903; 34 days over the alert level; what the INA
 * expects, with dates and links). It stays an informative piece (nota_informativa = 1: no commercial CTA), type "datos", destination goya.
 *
 * What it changes, per language (es, en, pt), on the three existing rows found by clave + language:
 *   title, slug (month-based, only if the 301 from the old URL can be proven, see below), post content (whole body, with the new
 *   infographics and the FAQ group `pm-faq` that feeds the FAQPage JSON-LD), subtitle (bajada), datos clave (6), sources (INA report,
 *   forecast, weekly report and INA-SMN bulletin with their dates), SEO title and description, "actualizado" (21/11/2026; nota_fecha, the
 *   publication date, is NOT touched), and, on the Spanish row (it holds the shared data): featured image, nota_origen. nota_tipo,
 *   nota_destino and nota_informativa are kept (nota_informativa is only set if it was off). Attachments are uploaded idempotently
 *   (_pm_archivo_origen): 3 PNG (1600x1200) + 2 CSV; the 3 old attachments stay in the media library, unused (not deleted).
 *
 * Slugs. The old slugs name "20 de noviembre". The site routes /notas/<slug>/ to the pm_nota query var, which sets `name` and
 * `post_type`, so WordPress core's wp_old_slug_redirect() / redirect_guess_404_permalink() can answer 301 from the old slug
 * (_wp_old_slug meta, written by wp_update_post). That is a mechanism, not a proof, so the script proves it: (1) before touching
 * anything it requests the three current URLs from the server itself (loopback) and only continues with slugs if they answer 200;
 * (2) after the rename it requests each OLD url with redirection=0 and requires 301 with a Location whose path is the new permalink,
 * and the new url 200; (3) if any of that fails it puts the three old slugs back at once (the content stays replaced) and says so.
 * Set $cambiar_slugs = false to keep the current slugs without trying. An old URL is never left answering 404 by this script.
 *
 * Files (wordpress/fotos/, copied to /tmp/pm-fotos/ by wp-script.yml): parana-goya-nunca-en-noviembre.png,
 * parana-goya-el-rio-dia-por-dia.png, parana-la-misma-agua-rio-abajo.png, parana-goya-estadistica-diaria-1903-2026.csv,
 * parana-goya-mensual-1903-2026.csv. The PNGs come from wordpress/build/rio-nota-noviembre-graficos.py (inputs in
 * wordpress/build/rio-nota-noviembre/); every number below is checked there against resumen.json and the daily files (the generator
 * stops if one differs). The INA texts are quoted verbatim from docs/borradores/parana-goya-noviembre-2026.md.
 *
 * Dry run first ($aplicar = false): prints before/after, attachments, the loopback test and the redirect mechanism; writes nothing.
 * Run with wp-script.yml (script = nota-parana-goya-reemplazo-2026-11-21; aplicar=true for the real run). Idempotent: a second run
 * rewrites the same content and finds the slugs already in place.
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
$cambiar_slugs = true;
$clave          = 'parana-goya-20-noviembre-2026';
$hoy            = '20261121';
$lectura        = '20261120';
$carpeta        = '/tmp/pm-fotos';
$marcas_cta     = [ 'class="cta-nota"', 'class="barra-pedir"', 'class="guias-especie"', 'cta-gratis', 'banda-gratis' ];

// Official links (all from docs/borradores/parana-goya-noviembre-2026.md; consulted 20/11/2026).
$ENL = [
	'ina'     => 'https://www.ina.gob.ar/alerta/index.php',
	'diario'  => 'https://alerta.ina.gob.ar/ina/06-INFORMES/diario/pdf/reporte_diario_2026-11-20.pdf',
	'prono'   => 'https://www.ina.gov.ar/archivos/alerta/TabProno20261120_.pdf',
	'semanal' => 'https://www.ina.gov.ar/archivos/alerta/2026Nov18_10480mapat.pdf',
	'boletin' => 'https://www.ina.gov.ar/archivos/alerta/boletin_hidroclim_2026_11.pdf',
	'pna'     => 'https://contenidosweb.prefecturanaval.gob.ar/alturas/',
	'coe'     => 'https://www.goya.gob.ar/?q=coe-comite-operativo-emergencia',
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
		'archivo' => 'parana-goya-el-rio-dia-por-dia.png', 'titulo' => 'El Paraná en Goya día por día, septiembre a noviembre de 2026',
		'alt' => [
			'es' => 'Curva de la altura diaria del Paraná en Goya del 1 de septiembre al 20 de noviembre de 2026: sube de 2,57 m el 4 de septiembre, cruza el nivel de alerta (5,20 m) el 18 de octubre y el de evacuación (5,70 m) el 29 de octubre, marca 6,10 m del 3 al 5 de noviembre y baja a 5,68 m el 20; 34 días sobre el alerta y 22 sobre el de evacuación. Aparte, el pronóstico del INA emitido el 20 de noviembre: 5,39 m el 24/11 y 5,45 m el 1/12, con rango probable de 5,00 a 5,70 m',
			'en' => 'Curve of the daily Paraná level at Goya from 1 September to 20 November 2026: it rises from 2.57 m on 4 September, crosses the alert level (5.20 m) on 18 October and the evacuation level (5.70 m) on 29 October, peaks at 6.10 m from 3 to 5 November and falls to 5.68 m on 20 November; 34 days above the alert level and 22 above the evacuation level. Separately, the INA forecast issued on 20 November: 5.39 m on 24/11 and 5.45 m on 1/12, with a probable range of 5.00 to 5.70 m',
			'pt' => 'Curva do nível diário do Paraná em Goya de 1.º de setembro a 20 de novembro de 2026: sobe de 2,57 m em 4 de setembro, cruza o nível de alerta (5,20 m) em 18 de outubro e o de evacuação (5,70 m) em 29 de outubro, marca 6,10 m de 3 a 5 de novembro e baixa para 5,68 m em 20 de novembro; 34 dias acima do alerta e 22 acima do de evacuação. À parte, a previsão do INA emitida em 20 de novembro: 5,39 m em 24/11 e 5,45 m em 1/12, com faixa provável de 5,00 a 5,70 m',
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
$CSV_D = 'parana-goya-estadistica-diaria-1903-2026.csv';
$CSV_M = 'parana-goya-mensual-1903-2026.csv';

// ---- Text. Tokens {clave|texto} become links: ina diario prono semanal boletin pna coe (official), r (river page of Goya), csvd csvm (downloads). ----
$N = [];
$N['es'] = [
	'titulo' => 'Goya nunca tuvo un noviembre así: el Paraná, en su nivel más alto para el mes en 124 años',
	'slug'   => 'parana-goya-noviembre-2026',
	'bajada' => 'El Paraná llegó a 6,10 m en la escala de Goya el 5 de noviembre, el máximo para un noviembre desde 1903. Lleva 34 días sobre el nivel de alerta y el INA ve elevada probabilidad de que siga por encima. Qué pasó, por qué es inusual y qué se espera.',
	'seo_title' => 'Paraná en Goya: el noviembre más alto en 124 años',
	'seo_description' => 'El Paraná llegó a 6,10 m en Goya el 5/11/2026, el máximo para un noviembre desde 1903. Lleva 34 días sobre el nivel de alerta. Datos y qué prevé el INA.',
	'datos' => [
		'6,10 m el 5 de noviembre de 2026: el máximo del Paraná en Goya para un noviembre en 124 años de registros (el anterior, 5,80 m, en 1998).',
		'34 días seguidos sobre el nivel de alerta (5,20 m), desde el 18 de octubre; 22 días sobre el de evacuación (5,70 m), algo que no ocurría desde marzo de 2016.',
		'Media del 1 al 20 de noviembre: 5,93 m. La mayor media de un noviembre completo, hasta ahora: 5,51 m (1997).',
		'No es la mayor crecida: el récord de la serie es 7,20 m, el 13 de junio de 1992. Lo inusual es la época del año.',
		'Subió 3,53 m en 62 días: de 2,57 m el 4 de septiembre a 6,10 m el 5 de noviembre.',
		'El INA prevé (pronóstico del 20/11) 5,39 m en Goya el 24 de noviembre y 5,45 m el 1 de diciembre, con un rango probable de 5,00 a 5,70 m.',
	],
	'cuerpo' => [
		[ 'p', '<strong>El río Paraná llegó a 6,10 m en la escala de Goya el 5 de noviembre: es la altura más alta que se registró en ese puerto en un mes de noviembre desde que empieza la serie del Instituto Nacional del Agua (INA), en 1903. El máximo anterior para el mes era 5,80 m, de 1998. Lleva 34 días seguidos sobre el nivel de alerta (5,20 m) y estuvo 22 días sobre el de evacuación (5,70 m), algo que no ocurría desde marzo de 2016. El INA estima elevada probabilidad de que siga por encima del nivel de alerta, con posibles repuntes.</strong>' ],
		[ 'p', '<em>Lecturas hasta el 20 de noviembre de 2026. Datos: {ina|Instituto Nacional del Agua} (escalas de Prefectura Naval Argentina). Elaboración: Pescamapa.</em>' ],
		[ 'h2', 'Qué pasó' ],
		[ 'p', 'El año empezó con el río bajo: hasta junio, 171 de los 181 días estuvieron por debajo de la mediana de la serie para cada fecha. Después giró. El 4 de septiembre el río medía 2,57 m; desde ahí subió, con algún parón, hasta los 6,10 m del 5 de noviembre: 3,53 m en 62 días. Cruzó el nivel de alerta el 18 de octubre (5,28 m) y el de evacuación el 29 de octubre.' ],
		[ 'p', 'El pico llegó entre el 3 y el 5 de noviembre, con 6,10 m. Desde entonces baja de a poco: 5,86 m el 14 de noviembre y 5,68 m el 20. Los 20 días de noviembre estuvieron por encima del nivel de alerta, y 19 de los 20, por encima del de evacuación; la media del 1 al 20 fue 5,93 m. En lo que va del año son 34 días seguidos sobre el alerta y 22 sobre el de evacuación.' ],
		[ 'fig', 'dias', 'Altura diaria del Paraná en Goya, del 1 de septiembre al 20 de noviembre de 2026, y pronóstico del INA emitido el 20/11 (aparte, con su rango). Datos: INA. Elaboración: Pescamapa.' ],
		[ 'p', 'Una aclaración sobre los niveles: el nivel de alerta (5,20 m) y el de evacuación (5,70 m) son marcas de referencia de la escala de Goya que publica el INA. No son una orden de evacuación: eso lo decide Defensa Civil.' ],
		[ 'h2', 'Por qué no se parece a ningún noviembre' ],
		[ 'p', 'Goya tiene lecturas de altura desde 1903. En esos 124 años ningún noviembre tuvo el río tan alto: el máximo de 2026 (6,10 m) supera al de todos los anteriores, y el mayor era el de 1998, con 5,80 m. Con la media pasa lo mismo: la de 2026 hasta el día 20 (5,93 m) ya supera a la de cualquier noviembre completo; la mayor, 5,51 m, era la de 1997.' ],
		[ 'p', 'Solo dos noviembres de la serie pasaron los 5,70 m, el nivel de evacuación de la escala: 1998 y 2026.' ],
		[ 'fig', 'nunca', 'La máxima de cada noviembre en Goya, de 1903 a 2026. Datos: INA. Elaboración: Pescamapa.' ],
		[ 'p', '<strong>No es la mayor crecida.</strong> El récord de Goya es 7,20 m, el 13 de junio de 1992, y el pico de 2026 es el 11.º más alto de los 124 años de la serie (un pico por año). Lo inusual es la época: un río tan alto en noviembre no se había visto.' ],
		[ 'p', 'La última vez que el río estuvo en 6,10 m o más fue entre el 24 de diciembre de 2015 y el 2 de febrero de 2016 (máximo, 6,46 m). Antes de este episodio, la última vez sobre el nivel de evacuación fue en marzo de 2016, y sobre el de alerta, del 6 de noviembre al 1 de diciembre de 2023 (máximo, 5,67 m).' ],
		[ 'h2', 'Qué se espera' ],
		[ 'p', 'Todo lo que sigue es del INA (el último punto, del INA junto con el Servicio Meteorológico Nacional), con su fecha; no es un pronóstico de Pescamapa. El INA actualiza el pronóstico del Paraná medio los martes y los viernes. Esta nota cita el que se emitió el 20 de noviembre; para lo más reciente, mirá {ina|Alerta Hidrológico}.' ],
		[ 'ul', [
			'<strong>Reporte diario del INA, 20/11/2026</strong> ({diario|PDF}): «en Goya-Reconquista se espera que persista oscilante/leve descenso en aguas medias altas/altas, todavía con elevada probabilidad de sostenerse por encima de los niveles de Alerta […] estabilizándose progresivamente hacia el 26/11, pudiendo repuntar nuevamente a continuación». Para el tramo Corrientes–La Paz: «se estabilizaría sosteniéndose oscilante en aguas medias altas, pudiendo exhibir nuevamente un repunte hacia aguas altas, actualmente con mayores chances a partir del 26/11».',
			'<strong>Pronóstico numérico del INA, emitido el 20/11/2026</strong> ({prono|PDF}): para Goya, 5,39 m el 24 de noviembre y 5,45 m el 1 de diciembre, con un rango probable de 5,00 a 5,70 m en ambas fechas.',
			'<strong>Informe semanal del INA n.º 10480, 18/11/2026</strong> ({semanal|PDF}): «todavía con elevada probabilidad de sostener excedencia del nivel de Alerta en Goya-Reconquista, al menos durante los próximos 5 días, si bien con aproximación progresiva»; y «la perspectiva hidrológica actual favorece la persistencia de marcas por encima de lo normal para la época del año durante los próximos 15 días, con altas chances de estabilización y, a continuación, probabilidad de nuevos repuntes».',
			'<strong>Boletín de perspectivas hidroclimáticas INA–SMN, 19/11/2026</strong> ({boletin|PDF}): «el escenario más probable durante los próximos 30 días sugiere una tendencia de base sostenida en aguas medias altas, con mayor permanencia en valores por encima de lo normal para la época, con probabilidad [de] incrementos de punta y la posibilidad de que vuelvan a registrarse valores significativos». Para noviembre–enero, el pronóstico trimestral prevé lluvias superiores a lo normal en el Litoral, «principalmente […] Corrientes y Misiones».',
		] ],
		[ 'h2', 'La misma agua, río abajo' ],
		[ 'p', 'La crecida de octubre se movió de norte a sur. Pasó por Corrientes con 7,42 m el 23 de octubre; llegó a Goya el 3 de noviembre y marcó 6,10 m hasta el 5; en Esquina, con 5,60 m, fue el 6 y 7 de noviembre; en La Paz, con 6,30 m, el 14 de noviembre; y en Santa Fe midió 5,88 m el 20 de noviembre, la lectura más alta hasta ahora. De Corrientes a Goya la cresta tardó entre 11 y 13 días, y unos 22 hasta La Paz.' ],
		[ 'p', 'Las cinco escalas pasaron su propio nivel de evacuación. En Corrientes, Esquina y La Paz, noviembre de 2026 también tiene, por ahora, la mayor media del mes de su serie; en Santa Fe, el mayor máximo.' ],
		[ 'fig', 'abajo', 'La cresta de la crecida, de Corrientes a Santa Fe: fecha y altura del pico en cada escala, con sus propios niveles. Datos: INA. Elaboración: Pescamapa.' ],
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
			'<strong>Noviembre incompleto:</strong> 2026 tiene 20 de los 30 días, así que se compara de dos formas: contra los noviembres completos de cada año (con al menos el 90 % de los días con dato) y contra el mismo tramo, del 1 al 20 de noviembre, de cada año. En las dos, 2026 queda primero en media y en máximo. En el primer gráfico, el punto hueco marca un noviembre con datos incompletos.',
			'<strong>Un episodio</strong> son días seguidos en o sobre un valor.',
			'<strong>Qué no es:</strong> es altura de escala, no profundidad, y cada escala tiene su propio cero: de una estación a otra se comparan las fechas, no las alturas.',
		] ],
		[ 'h2', 'Usá los gráficos y los datos' ],
		[ 'p', 'Podés usar estos gráficos y números citando a Pescamapa con enlace a esta nota.' ],
		[ 'p', 'Datos para descargar: {csvd|estadística diaria de Goya, 1903–2026 (CSV)} (por día del calendario: años con dato, mínimo, percentiles 10, 25, 50, 75 y 90, máximo, el año más bajo y el más alto, y las lecturas de 2026) y {csvm|serie mensual de Goya desde 1903 (CSV)} (media, máximo y días con dato de cada mes de cada año). Nombres de columnas en español; alturas en metros.' ],
		[ 'h2', 'Preguntas frecuentes' ],
		[ 'faq', [
			[ '¿Cuánto llegó a medir el Paraná en Goya en noviembre de 2026?', '6,10 m, del 3 al 5 de noviembre, en la escala de Goya del INA: la mayor altura para un noviembre desde que empieza la serie, en 1903 (el máximo anterior era 5,80 m, en 1998). El 20 de noviembre medía 5,68 m.' ],
			[ '¿Es la mayor crecida de Goya?', 'No. El récord de la serie es 7,20 m, el 13 de junio de 1992. Lo inusual de 2026 es la época: ningún noviembre en 124 años tuvo el río tan alto.' ],
			[ '¿Qué significa nivel de alerta y nivel de evacuación?', 'Son marcas de referencia de la escala de Goya que publica el INA: 5,20 m (alerta) y 5,70 m (evacuación). No son una orden de evacuación: esa decisión la toma Defensa Civil.' ],
			[ '¿Qué se espera para las próximas semanas?', 'Según el INA, en su pronóstico emitido el 20 de noviembre de 2026: 5,39 m en Goya el 24 de noviembre y 5,45 m el 1 de diciembre, con un rango probable de 5,00 a 5,70 m. Su reporte diario de ese día dice que seguiría por encima de los niveles de alerta, con estabilización hacia el 26/11 y posibles repuntes. El INA actualiza los martes y los viernes: lo último está en {ina|Alerta Hidrológico}.' ],
		] ],
		[ 'h2', 'Para medios' ],
		[ 'p', '<strong>Resumen:</strong> el río Paraná alcanzó 6,10 metros en la escala de Goya el 5 de noviembre, la mayor altura para un noviembre en los 124 años de la serie del Instituto Nacional del Agua (INA); el máximo anterior para el mes era 5,80 metros, en 1998. Está sobre el nivel de alerta (5,20 metros) desde el 18 de octubre y estuvo 22 días sobre el de evacuación (5,70 metros), algo que no ocurría desde marzo de 2016. El 20 de noviembre medía 5,68 metros y bajaba lentamente. El INA estima elevada probabilidad de que siga sobre el nivel de alerta, con posibles repuntes. No es la mayor crecida: el récord es 7,20 metros, de 1992.' ],
		[ 'ul', [
			'<strong>6,10 m:</strong> pico del Paraná en Goya el 5/11/2026, la mayor altura para un noviembre desde 1903 (anterior: 5,80 m, 1998). Fuente: INA, escala de Prefectura Naval Argentina, serie diaria de Goya.',
			'<strong>34 días</strong> seguidos sobre el nivel de alerta (5,20 m) al 20/11/2026, y <strong>22 días</strong> sobre el de evacuación (5,70 m), la primera vez desde marzo de 2016. Fuente: INA (niveles y lecturas); cálculo de Pescamapa.',
			'<strong>Última vez a 6,10 m o más: entre el 24/12/2015 y el 2/2/2016</strong> (máximo, 6,46 m). Fuente: INA; cálculo de Pescamapa.',
		] ],
		[ 'p', 'Al citar, conviene aclarar que los niveles de alerta y de evacuación son referencias de la escala del INA y no una orden de evacuación. Datos y método: en esta nota, con los CSV para descargar.' ],
		[ 'contacto', 'Contacto: ' ],
	],
	'f' => [
		'ina'     => 'Instituto Nacional del Agua (INA): alturas diarias de las escalas de Prefectura Naval Argentina, 1903–2026',
		'diario'  => 'INA: reporte diario de la Cuenca del Plata, 20/11/2026',
		'prono'   => 'INA: pronóstico numérico del Paraná, emitido el 20/11/2026',
		'semanal' => 'INA: informe semanal n.º 10480, 18/11/2026',
		'boletin' => 'INA–SMN: boletín de perspectivas hidroclimáticas, 19/11/2026',
	],
	'orig' => 'Reemplazo del 21/11/2026 (decisión del dueño, 20–21/11): la nota deja de ser «el 20 de noviembre» y pasa a ser la historia del mes. Datos: lectura del 20/11/2026 con wordpress/build/rio-historia.py, rama rio-historia-desde-1903 (serie 1903–2026), copiada en wordpress/build/rio-nota-noviembre/; gráficos: wordpress/build/rio-nota-noviembre-graficos.py, que comprueba cada cifra contra esos archivos. Textos del INA citados literalmente de docs/borradores/parana-goya-noviembre-2026.md (lectura de las fuentes oficiales el 20/11/2026); el pronóstico numérico es el emitido el 20/11 y el INA actualiza martes y viernes: si sale uno nuevo, se actualiza la nota con su fecha, sin extrapolar. Solo INA e INA–SMN en «Qué se espera». Sin Itaipú, sin consejos propios, sin pesca. Prefectura (alturas) no se pudo abrir desde Actions: el enlace es el que usa el INA como fuente. «Desde 1903» supone cero de escala fijo (sin verificar). Lecturas de 2026 sin validar por el INA.',
];
$N['en'] = [
	'titulo' => 'Goya has never had a November like this: the Paraná at its highest level for the month in 124 years',
	'slug'   => 'parana-river-goya-november-2026',
	'bajada' => 'The Paraná reached 6.10 m at the Goya gauge on 5 November, the highest for any November since 1903. It has been above the alert level for 34 days and the INA sees a high probability it will stay above. What happened, why it is unusual and what to expect.',
	'seo_title' => 'Paraná at Goya: the highest November in 124 years',
	'seo_description' => 'The Paraná hit 6.10 m at Goya on 5 Nov 2026, the highest for a November since 1903, and has been above the alert level for 34 days. Data and INA outlook.',
	'datos' => [
		'6.10 m on 5 November 2026: the Paraná’s highest reading at Goya for a November in 124 years of records (the previous one, 5.80 m, in 1998).',
		'34 days in a row above the alert level (5.20 m), since 18 October; 22 days above the evacuation level (5.70 m), which had not happened since March 2016.',
		'Average for 1 to 20 November: 5.93 m. The highest average for a full November so far: 5.51 m (1997).',
		'It is not the biggest flood: the series record is 7.20 m, on 13 June 1992. What is unusual is the time of year.',
		'It rose 3.53 m in 62 days: from 2.57 m on 4 September to 6.10 m on 5 November.',
		'The INA expects (forecast of 20/11) 5.39 m at Goya on 24 November and 5.45 m on 1 December, with a probable range of 5.00 to 5.70 m.',
	],
	'cuerpo' => [
		[ 'p', '<strong>The Paraná River reached 6.10 m at the Goya gauge on 5 November: the highest level recorded at that port in any November since the National Water Institute (INA) series begins, in 1903. The previous maximum for the month was 5.80 m, in 1998. The river has been above the alert level (5.20 m) for 34 days in a row and spent 22 days above the evacuation level (5.70 m), which had not happened since March 2016. The INA sees a high probability that it will stay above the alert level, with possible new rises.</strong>' ],
		[ 'p', '<em>Readings up to 20 November 2026. Data: {ina|National Water Institute (INA)} (Argentine Naval Prefecture gauges). Prepared by Pescamapa.</em>' ],
		[ 'h2', 'What happened' ],
		[ 'p', 'The year began with the river low: until June, 171 of 181 days were below the series median for each date. Then it turned. On 4 September the river stood at 2.57 m; from there it rose, with a pause or two, to 6.10 m on 5 November: 3.53 m in 62 days. It crossed the alert level on 18 October (5.28 m) and the evacuation level on 29 October.' ],
		[ 'p', 'The peak came between 3 and 5 November, at 6.10 m. Since then it has been falling slowly: 5.86 m on 14 November and 5.68 m on 20 November. All 20 days of November were above the alert level, and 19 of the 20 above the evacuation level; the average for 1 to 20 November was 5.93 m. So far this year that makes 34 days in a row above the alert level and 22 above the evacuation level.' ],
		[ 'fig', 'dias', 'Daily Paraná level at Goya, from 1 September to 20 November 2026, and the INA forecast issued on 20/11 (shown apart, with its range). Data: INA. Prepared by Pescamapa.' ],
		[ 'p', 'A note on the levels: the alert level (5.20 m) and the evacuation level (5.70 m) are reference marks of the Goya gauge published by the INA. They are not an evacuation order: that is decided by Civil Defence (Defensa Civil).' ],
		[ 'h2', 'Why it is like no other November' ],
		[ 'p', 'Goya has level readings since 1903. In those 124 years no November had the river this high: the 2026 maximum (6.10 m) is above every earlier one, and the previous highest was 1998, at 5.80 m. The average tells the same story: the 2026 average up to the 20th (5.93 m) already exceeds that of any full November; the highest, 5.51 m, was 1997.' ],
		[ 'p', 'Only two Novembers in the series passed 5.70 m, the gauge’s evacuation level: 1998 and 2026.' ],
		[ 'fig', 'nunca', 'The highest reading of each November at Goya, 1903 to 2026. Data: INA. Prepared by Pescamapa.' ],
		[ 'p', '<strong>It is not the biggest flood.</strong> The Goya record is 7.20 m, on 13 June 1992, and the 2026 peak is the 11th highest of the 124 years in the series (one peak per year). What is unusual is the season: a river this high in November had not been seen.' ],
		[ 'p', 'The last time the river stood at 6.10 m or more was between 24 December 2015 and 2 February 2016 (maximum 6.46 m). Before this episode, the last time above the evacuation level was March 2016, and above the alert level, 6 November to 1 December 2023 (maximum 5.67 m).' ],
		[ 'h2', 'What to expect' ],
		[ 'p', 'Everything below comes from the INA (the last item, from the INA together with the National Weather Service), with its date; it is not a Pescamapa forecast. The INA updates the middle Paraná forecast on Tuesdays and Fridays. This note cites the one issued on 20 November; for the latest, see {ina|Alerta Hidrológico}. Quotes are in the original Spanish.' ],
		[ 'ul', [
			'<strong>INA daily report, 20/11/2026</strong> ({diario|PDF}): «en Goya-Reconquista se espera que persista oscilante/leve descenso en aguas medias altas/altas, todavía con elevada probabilidad de sostenerse por encima de los niveles de Alerta […] estabilizándose progresivamente hacia el 26/11, pudiendo repuntar nuevamente a continuación» (at Goya-Reconquista it is expected to keep fluctuating or falling slightly at medium-high to high levels, still very likely to stay above the Alert levels […] gradually stabilising towards 26/11, and possibly rising again afterwards). For the Corrientes–La Paz stretch: «se estabilizaría sosteniéndose oscilante en aguas medias altas, pudiendo exhibir nuevamente un repunte hacia aguas altas, actualmente con mayores chances a partir del 26/11».',
			'<strong>INA numerical forecast, issued 20/11/2026</strong> ({prono|PDF}): for Goya, 5.39 m on 24 November and 5.45 m on 1 December, with a probable range of 5.00 to 5.70 m on both dates.',
			'<strong>INA weekly report no. 10480, 18/11/2026</strong> ({semanal|PDF}): «todavía con elevada probabilidad de sostener excedencia del nivel de Alerta en Goya-Reconquista, al menos durante los próximos 5 días, si bien con aproximación progresiva»; and «la perspectiva hidrológica actual favorece la persistencia de marcas por encima de lo normal para la época del año durante los próximos 15 días, con altas chances de estabilización y, a continuación, probabilidad de nuevos repuntes» (still very likely to stay above the Alert level for at least 5 days; levels above normal for the season over the next 15 days, with a good chance of stabilising and then new rises).',
			'<strong>INA–SMN hydroclimatic outlook bulletin, 19/11/2026</strong> ({boletin|PDF}): «el escenario más probable durante los próximos 30 días sugiere una tendencia de base sostenida en aguas medias altas, con mayor permanencia en valores por encima de lo normal para la época, con probabilidad [de] incrementos de punta y la posibilidad de que vuelvan a registrarse valores significativos» (levels above normal for the season over the next 30 days, with possible peaks). For November to January, the quarterly outlook expects above-normal rainfall in the Litoral, «principalmente […] Corrientes y Misiones».',
		] ],
		[ 'h2', 'The same water, further downriver' ],
		[ 'p', 'The October flood moved from north to south. It passed Corrientes at 7.42 m on 23 October; reached Goya on 3 November and stood at 6.10 m until the 5th; at Esquina, at 5.60 m, it was 6 and 7 November; at La Paz, at 6.30 m, 14 November; and at Santa Fe it read 5.88 m on 20 November, the highest reading so far. The crest took 11 to 13 days from Corrientes to Goya, and about 22 to La Paz.' ],
		[ 'p', 'All five gauges passed their own evacuation level. At Corrientes, Esquina and La Paz, November 2026 also has, so far, the highest average for the month in its series; at Santa Fe, the highest maximum.' ],
		[ 'fig', 'abajo', 'The flood crest from Corrientes to Santa Fe: date and height of the peak at each gauge, with its own levels. Data: INA. Prepared by Pescamapa.' ],
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
			'<strong>Incomplete November:</strong> 2026 has 20 of the 30 days, so it is compared in two ways: against the full Novembers of every year (with data on at least 90 % of the days) and against the same stretch, 1 to 20 November, of every year. In both, 2026 ranks first in average and in maximum. In the first chart, a hollow dot marks a November with incomplete data.',
			'<strong>An episode</strong> is consecutive days at or above a value.',
			'<strong>What it is not:</strong> it is gauge height, not depth, and every gauge has its own zero: between stations we compare dates, not heights.',
		] ],
		[ 'h2', 'Use the charts and the data' ],
		[ 'p', 'You can use these charts and numbers if you credit Pescamapa with a link to this note.' ],
		[ 'p', 'Data to download: {csvd|Goya daily statistics, 1903–2026 (CSV)} (by calendar day: years with data, minimum, 10th, 25th, 50th, 75th and 90th percentiles, maximum, the lowest and the highest year, and the 2026 readings) and {csvm|Goya monthly series since 1903 (CSV)} (average, maximum and days with data for each month of each year). Column names in Spanish; heights in metres.' ],
		[ 'h2', 'Frequently asked questions' ],
		[ 'faq', [
			[ 'How high did the Paraná get at Goya in November 2026?', '6.10 m, from 3 to 5 November, at the INA’s Goya gauge: the highest level for a November since the series begins, in 1903 (the previous maximum was 5.80 m, in 1998). On 20 November it stood at 5.68 m.' ],
			[ 'Is it Goya’s biggest flood?', 'No. The series record is 7.20 m, on 13 June 1992. What is unusual in 2026 is the season: no November in 124 years had the river this high.' ],
			[ 'What do the alert level and the evacuation level mean?', 'They are reference marks of the Goya gauge published by the INA: 5.20 m (alert) and 5.70 m (evacuation). They are not an evacuation order: that decision is taken by Civil Defence (Defensa Civil).' ],
			[ 'What is expected for the coming weeks?', 'According to the INA, in its forecast issued on 20 November 2026: 5.39 m at Goya on 24 November and 5.45 m on 1 December, with a probable range of 5.00 to 5.70 m. Its daily report of that day says the river would stay above the alert levels, stabilising towards 26/11 with possible new rises. The INA updates on Tuesdays and Fridays: the latest is at {ina|Alerta Hidrológico}.' ],
		] ],
		[ 'h2', 'For the media' ],
		[ 'p', '<strong>Summary:</strong> the Paraná River reached 6.10 metres at the Goya gauge on 5 November, the highest level for a November in the 124 years of the National Water Institute (INA) series; the previous maximum for the month was 5.80 metres, in 1998. It has been above the alert level (5.20 metres) since 18 October and spent 22 days above the evacuation level (5.70 metres), which had not happened since March 2016. On 20 November it stood at 5.68 metres and was slowly falling. The INA sees a high probability that it will stay above the alert level, with possible new rises. It is not the biggest flood: the record is 7.20 metres, from 1992.' ],
		[ 'ul', [
			'<strong>6.10 m:</strong> Paraná peak at Goya on 5/11/2026, the highest level for a November since 1903 (previous: 5.80 m, 1998). Source: INA, Argentine Naval Prefecture gauge, daily series for Goya.',
			'<strong>34 days</strong> in a row above the alert level (5.20 m) as of 20/11/2026, and <strong>22 days</strong> above the evacuation level (5.70 m), the first time since March 2016. Source: INA (levels and readings); calculation by Pescamapa.',
			'<strong>Last time at 6.10 m or more: between 24/12/2015 and 2/2/2016</strong> (maximum 6.46 m). Source: INA; calculation by Pescamapa.',
		] ],
		[ 'p', 'When quoting, please note that the alert and evacuation levels are INA gauge reference marks and not an evacuation order. Data and method: in this note, with the CSV files to download.' ],
		[ 'contacto', 'Contact: ' ],
	],
	'f' => [
		'ina'     => 'National Water Institute (INA): daily readings of the Argentine Naval Prefecture gauges, 1903–2026',
		'diario'  => 'INA: daily report for the Río de la Plata basin, 20/11/2026',
		'prono'   => 'INA: numerical forecast for the Paraná, issued 20/11/2026',
		'semanal' => 'INA: weekly report no. 10480, 18/11/2026',
		'boletin' => 'INA–SMN: hydroclimatic outlook bulletin, 19/11/2026',
	],
];
$N['pt'] = [
	'titulo' => 'Goya nunca teve um novembro assim: o Paraná, no nível mais alto para o mês em 124 anos',
	'slug'   => 'rio-parana-goya-novembro-2026',
	'bajada' => 'O Paraná chegou a 6,10 m na escala de Goya em 5 de novembro, o máximo para um novembro desde 1903. Está há 34 dias acima do nível de alerta e o INA vê alta probabilidade de que continue acima. O que aconteceu, por que é incomum e o que se espera.',
	'seo_title' => 'Paraná em Goya: o novembro mais alto em 124 anos',
	'seo_description' => 'O Paraná chegou a 6,10 m em Goya em 5/11/2026, o máximo para um novembro desde 1903. Há 34 dias acima do nível de alerta. Dados e o que prevê o INA.',
	'datos' => [
		'6,10 m em 5 de novembro de 2026: o máximo do Paraná em Goya para um novembro em 124 anos de registros (o anterior, 5,80 m, em 1998).',
		'34 dias seguidos acima do nível de alerta (5,20 m), desde 18 de outubro; 22 dias acima do de evacuação (5,70 m), algo que não ocorria desde março de 2016.',
		'Média de 1 a 20 de novembro: 5,93 m. A maior média de um novembro completo, até agora: 5,51 m (1997).',
		'Não é a maior cheia: o recorde da série é 7,20 m, em 13 de junho de 1992. O incomum é a época do ano.',
		'Subiu 3,53 m em 62 dias: de 2,57 m em 4 de setembro a 6,10 m em 5 de novembro.',
		'O INA prevê (previsão de 20/11) 5,39 m em Goya em 24 de novembro e 5,45 m em 1.º de dezembro, com faixa provável de 5,00 a 5,70 m.',
	],
	'cuerpo' => [
		[ 'p', '<strong>O rio Paraná chegou a 6,10 m na escala de Goya em 5 de novembro: é o nível mais alto registrado nesse porto em um mês de novembro desde que começa a série do Instituto Nacional da Água (INA), em 1903. O máximo anterior para o mês era 5,80 m, de 1998. Está há 34 dias seguidos acima do nível de alerta (5,20 m) e ficou 22 dias acima do de evacuação (5,70 m), algo que não ocorria desde março de 2016. O INA estima alta probabilidade de que continue acima do nível de alerta, com possíveis novas subidas.</strong>' ],
		[ 'p', '<em>Leituras até 20 de novembro de 2026. Dados: {ina|Instituto Nacional da Água (INA)} (escalas da Prefeitura Naval Argentina). Elaboração: Pescamapa.</em>' ],
		[ 'h2', 'O que aconteceu' ],
		[ 'p', 'O ano começou com o rio baixo: até junho, 171 dos 181 dias ficaram abaixo da mediana da série para cada data. Depois virou. Em 4 de setembro o rio marcava 2,57 m; dali subiu, com uma ou outra pausa, até os 6,10 m de 5 de novembro: 3,53 m em 62 dias. Cruzou o nível de alerta em 18 de outubro (5,28 m) e o de evacuação em 29 de outubro.' ],
		[ 'p', 'O pico veio entre 3 e 5 de novembro, com 6,10 m. Desde então baixa aos poucos: 5,86 m em 14 de novembro e 5,68 m em 20. Os 20 dias de novembro ficaram acima do nível de alerta, e 19 dos 20, acima do de evacuação; a média de 1 a 20 foi 5,93 m. No ano são 34 dias seguidos acima do alerta e 22 acima do de evacuação.' ],
		[ 'fig', 'dias', 'Nível diário do Paraná em Goya, de 1.º de setembro a 20 de novembro de 2026, e previsão do INA emitida em 20/11 (à parte, com sua faixa). Dados: INA. Elaboração: Pescamapa.' ],
		[ 'p', 'Um esclarecimento sobre os níveis: o nível de alerta (5,20 m) e o de evacuação (5,70 m) são marcas de referência da escala de Goya publicadas pelo INA. Não são uma ordem de evacuação: isso quem decide é a Defesa Civil.' ],
		[ 'h2', 'Por que não se parece com nenhum novembro' ],
		[ 'p', 'Goya tem leituras de nível desde 1903. Nesses 124 anos nenhum novembro teve o rio tão alto: o máximo de 2026 (6,10 m) supera o de todos os anteriores, e o maior era o de 1998, com 5,80 m. Com a média acontece o mesmo: a de 2026 até o dia 20 (5,93 m) já supera a de qualquer novembro completo; a maior, 5,51 m, era a de 1997.' ],
		[ 'p', 'Só dois novembros da série passaram dos 5,70 m, o nível de evacuação da escala: 1998 e 2026.' ],
		[ 'fig', 'nunca', 'A máxima de cada novembro em Goya, de 1903 a 2026. Dados: INA. Elaboração: Pescamapa.' ],
		[ 'p', '<strong>Não é a maior cheia.</strong> O recorde de Goya é 7,20 m, em 13 de junho de 1992, e o pico de 2026 é o 11.º mais alto dos 124 anos da série (um pico por ano). O incomum é a época: um rio tão alto em novembro não se tinha visto.' ],
		[ 'p', 'A última vez que o rio esteve em 6,10 m ou mais foi entre 24 de dezembro de 2015 e 2 de fevereiro de 2016 (máximo, 6,46 m). Antes deste episódio, a última vez acima do nível de evacuação foi em março de 2016, e acima do de alerta, de 6 de novembro a 1.º de dezembro de 2023 (máximo, 5,67 m).' ],
		[ 'h2', 'O que se espera' ],
		[ 'p', 'Tudo o que segue é do INA (o último item, do INA junto com o Serviço Meteorológico Nacional), com sua data; não é uma previsão da Pescamapa. O INA atualiza a previsão do Paraná médio às terças e sextas. Esta nota cita a emitida em 20 de novembro; para o mais recente, consulte o {ina|Alerta Hidrológico}. As citações estão no espanhol original.' ],
		[ 'ul', [
			'<strong>Relatório diário do INA, 20/11/2026</strong> ({diario|PDF}): «en Goya-Reconquista se espera que persista oscilante/leve descenso en aguas medias altas/altas, todavía con elevada probabilidad de sostenerse por encima de los niveles de Alerta […] estabilizándose progresivamente hacia el 26/11, pudiendo repuntar nuevamente a continuación» (em Goya-Reconquista espera-se que continue oscilando ou baixando levemente em águas médias altas a altas, ainda com elevada probabilidade de ficar acima dos níveis de Alerta […] estabilizando-se aos poucos até 26/11, podendo voltar a subir depois). Para o trecho Corrientes–La Paz: «se estabilizaría sosteniéndose oscilante en aguas medias altas, pudiendo exhibir nuevamente un repunte hacia aguas altas, actualmente con mayores chances a partir del 26/11».',
			'<strong>Previsão numérica do INA, emitida em 20/11/2026</strong> ({prono|PDF}): para Goya, 5,39 m em 24 de novembro e 5,45 m em 1.º de dezembro, com faixa provável de 5,00 a 5,70 m nas duas datas.',
			'<strong>Informe semanal do INA n.º 10480, 18/11/2026</strong> ({semanal|PDF}): «todavía con elevada probabilidad de sostener excedencia del nivel de Alerta en Goya-Reconquista, al menos durante los próximos 5 días, si bien con aproximación progresiva»; e «la perspectiva hidrológica actual favorece la persistencia de marcas por encima de lo normal para la época del año durante los próximos 15 días, con altas chances de estabilización y, a continuación, probabilidad de nuevos repuntes» (ainda com elevada probabilidade de ficar acima do nível de Alerta por pelo menos 5 dias; níveis acima do normal para a época nos próximos 15 dias, com boas chances de estabilização e, depois, de novas subidas).',
			'<strong>Boletim de perspectivas hidroclimáticas INA–SMN, 19/11/2026</strong> ({boletin|PDF}): «el escenario más probable durante los próximos 30 días sugiere una tendencia de base sostenida en aguas medias altas, con mayor permanencia en valores por encima de lo normal para la época, con probabilidad [de] incrementos de punta y la posibilidad de que vuelvan a registrarse valores significativos» (níveis acima do normal para a época nos próximos 30 dias, com possíveis picos). Para novembro a janeiro, a previsão trimestral indica chuvas acima do normal no Litoral, «principalmente […] Corrientes y Misiones».',
		] ],
		[ 'h2', 'A mesma água, rio abaixo' ],
		[ 'p', 'A cheia de outubro se moveu de norte a sul. Passou por Corrientes com 7,42 m em 23 de outubro; chegou a Goya em 3 de novembro e marcou 6,10 m até o dia 5; em Esquina, com 5,60 m, foi em 6 e 7 de novembro; em La Paz, com 6,30 m, em 14 de novembro; e em Santa Fe marcou 5,88 m em 20 de novembro, a leitura mais alta até agora. De Corrientes a Goya a crista levou de 11 a 13 dias, e cerca de 22 até La Paz.' ],
		[ 'p', 'As cinco escalas passaram do seu próprio nível de evacuação. Em Corrientes, Esquina e La Paz, novembro de 2026 também tem, por ora, a maior média do mês da sua série; em Santa Fe, o maior máximo.' ],
		[ 'fig', 'abajo', 'A crista da cheia, de Corrientes a Santa Fe: data e altura do pico em cada escala, com seus próprios níveis. Dados: INA. Elaboração: Pescamapa.' ],
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
			'<strong>Novembro incompleto:</strong> 2026 tem 20 dos 30 dias, então se compara de duas formas: com os novembros completos de cada ano (com dado em pelo menos 90 % dos dias) e com o mesmo trecho, de 1 a 20 de novembro, de cada ano. Nas duas, 2026 fica em primeiro em média e em máximo. No primeiro gráfico, o ponto vazado marca um novembro com dados incompletos.',
			'<strong>Um episódio</strong> são dias seguidos em ou acima de um valor.',
			'<strong>O que não é:</strong> é altura de escala, não profundidade, e cada escala tem o seu próprio zero: de uma estação a outra se comparam as datas, não as alturas.',
		] ],
		[ 'h2', 'Use os gráficos e os dados' ],
		[ 'p', 'Você pode usar estes gráficos e números citando a Pescamapa com link para esta nota.' ],
		[ 'p', 'Dados para baixar: {csvd|estatística diária de Goya, 1903–2026 (CSV)} (por dia do calendário: anos com dado, mínimo, percentis 10, 25, 50, 75 e 90, máximo, o ano mais baixo e o mais alto, e as leituras de 2026) e {csvm|série mensal de Goya desde 1903 (CSV)} (média, máximo e dias com dado de cada mês de cada ano). Nomes das colunas em espanhol; alturas em metros.' ],
		[ 'h2', 'Perguntas frequentes' ],
		[ 'faq', [
			[ 'Quanto chegou a medir o Paraná em Goya em novembro de 2026?', '6,10 m, de 3 a 5 de novembro, na escala de Goya do INA: o maior nível para um novembro desde que começa a série, em 1903 (o máximo anterior era 5,80 m, em 1998). Em 20 de novembro marcava 5,68 m.' ],
			[ 'É a maior cheia de Goya?', 'Não. O recorde da série é 7,20 m, em 13 de junho de 1992. O incomum em 2026 é a época: nenhum novembro em 124 anos teve o rio tão alto.' ],
			[ 'O que significam nível de alerta e nível de evacuação?', 'São marcas de referência da escala de Goya publicadas pelo INA: 5,20 m (alerta) e 5,70 m (evacuação). Não são uma ordem de evacuação: essa decisão é da Defesa Civil.' ],
			[ 'O que se espera para as próximas semanas?', 'Segundo o INA, na previsão emitida em 20 de novembro de 2026: 5,39 m em Goya em 24 de novembro e 5,45 m em 1.º de dezembro, com faixa provável de 5,00 a 5,70 m. O relatório diário desse dia diz que continuaria acima dos níveis de alerta, com estabilização até 26/11 e possíveis novas subidas. O INA atualiza às terças e sextas: o mais recente está no {ina|Alerta Hidrológico}.' ],
		] ],
		[ 'h2', 'Para a imprensa' ],
		[ 'p', '<strong>Resumo:</strong> o rio Paraná alcançou 6,10 metros na escala de Goya em 5 de novembro, o maior nível para um novembro nos 124 anos da série do Instituto Nacional da Água (INA); o máximo anterior para o mês era 5,80 metros, em 1998. Está acima do nível de alerta (5,20 metros) desde 18 de outubro e ficou 22 dias acima do de evacuação (5,70 metros), algo que não ocorria desde março de 2016. Em 20 de novembro marcava 5,68 metros e baixava lentamente. O INA estima alta probabilidade de que continue acima do nível de alerta, com possíveis novas subidas. Não é a maior cheia: o recorde é 7,20 metros, de 1992.' ],
		[ 'ul', [
			'<strong>6,10 m:</strong> pico do Paraná em Goya em 5/11/2026, o maior nível para um novembro desde 1903 (anterior: 5,80 m, 1998). Fonte: INA, escala da Prefeitura Naval Argentina, série diária de Goya.',
			'<strong>34 dias</strong> seguidos acima do nível de alerta (5,20 m) até 20/11/2026, e <strong>22 dias</strong> acima do de evacuação (5,70 m), a primeira vez desde março de 2016. Fonte: INA (níveis e leituras); cálculo da Pescamapa.',
			'<strong>Última vez em 6,10 m ou mais: entre 24/12/2015 e 2/2/2016</strong> (máximo, 6,46 m). Fonte: INA; cálculo da Pescamapa.',
		] ],
		[ 'p', 'Ao citar, convém esclarecer que os níveis de alerta e de evacuação são referências da escala do INA e não uma ordem de evacuação. Dados e método: nesta nota, com os CSV para baixar.' ],
		[ 'contacto', 'Contato: ' ],
	],
	'f' => [
		'ina'     => 'Instituto Nacional da Água (INA): leituras diárias das escalas da Prefeitura Naval Argentina, 1903–2026',
		'diario'  => 'INA: relatório diário da Bacia do Prata, 20/11/2026',
		'prono'   => 'INA: previsão numérica do Paraná, emitida em 20/11/2026',
		'semanal' => 'INA: informe semanal n.º 10480, 18/11/2026',
		'boletin' => 'INA–SMN: boletim de perspectivas hidroclimáticas, 19/11/2026',
	],
];

// ---- Attachments (own work: credit "Pescamapa"); same idempotent loader as the first note ----
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
	wp_update_post( [ 'ID' => $att, 'post_content' => "Elaboración propia de Pescamapa ($hoy), hecha con wordpress/build/rio-nota-noviembre-graficos.py (PNG) o rio-historia.py (CSV) a partir de la lectura del INA del 20/11/2026." ] );
	return (int) $att;
};
foreach ( $A as $k => $a ) {
	$adj[ $k ] = $subir( $a['archivo'], $a['titulo'], $a['alt'] );
}
$adj['csvd'] = $subir( $CSV_D, 'Paraná en Goya: estadística diaria 1903–2026 (CSV)', null );
$adj['csvm'] = $subir( $CSV_M, 'Paraná en Goya: serie mensual desde 1903 (CSV)', null );
$url_de  = fn( int $id ) => $id ? (string) wp_get_attachment_url( $id ) : '';
$url_img = fn( int $id ) => $id ? ( (string) wp_get_attachment_image_url( $id, 'large' ) ?: $url_de( $id ) ) : '';

// The three attachments of the first version (left in the library, unused after this run).
$viejos = [];
foreach ( [ 'parana-goya-2026-el-ano.png', 'parana-goya-20-de-noviembre.png', 'parana-crecida-rio-abajo.png', 'parana-goya-historico-20-noviembre.csv' ] as $f ) {
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

$palabras_malas = [ 'operador', 'operator', 'operadora', 'vosotros' ];
$antes          = [];
$plan           = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p      = $filas[ $i ];
	$n      = $N[ $i ];
	$tl     = mb_strlen( $n['seo_title'] );
	$dl     = mb_strlen( $n['seo_description'] );
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
	$nf = count( pm_faqs_contenido( $cuerpo ) );
	$nf === 4 || WP_CLI::error( "$i: la FAQ debe leerse con 4 preguntas (leo $nf)" );
	$antes[ $i ] = [
		'titulo' => $p->post_title, 'slug' => $p->post_name, 'palabras' => str_word_count( wp_strip_all_tags( $p->post_content ) ),
		'datos'  => count( pm_filas( 'nota_datos', $p->ID ) ), 'faq' => count( pm_faqs_contenido( $p->post_content ) ), 'fuentes' => count( pm_filas( 'fuentes', $p->ID ) ),
		'seo_title' => (string) pm_campo( 'seo_title', $p->ID ), 'actualizado' => (string) pm_campo( 'actualizado', $p->ID ),
	];
	$plan[ $i ] = [ 'cuerpo' => $cuerpo, 'tl' => $tl, 'dl' => $dl, 'palabras' => str_word_count( wp_strip_all_tags( $cuerpo ) ) ];
	WP_CLI::log( "ANTES  [$i] #{$p->ID} «{$antes[$i]['titulo']}» /{$antes[$i]['slug']}/ — {$antes[$i]['palabras']} palabras, {$antes[$i]['datos']} datos, {$antes[$i]['faq']} FAQ, {$antes[$i]['fuentes']} fuentes, actualizado {$antes[$i]['actualizado']}, SEO «{$antes[$i]['seo_title']}»" );
	WP_CLI::log( "DESPUÉS[$i] «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — {$plan[$i]['palabras']} palabras, 6 datos, 4 FAQ, " . count( $n['f'] ) . " fuentes, actualizado $hoy; title $tl / description $dl" );
}
$es = $filas['es'];
WP_CLI::log( 'es: tipo ' . pm_campo( 'nota_tipo', $es->ID ) . ', destino ' . pm_campo( 'nota_destino', $es->ID ) . ', fecha ' . pm_campo( 'nota_fecha', $es->ID ) . ' (no se toca), informativa ' . ( pm_campo( 'nota_informativa', $es->ID ) ? 'sí' : 'NO (se activa)' ) . ', portada #' . (int) get_post_thumbnail_id( $es->ID ) . ' -> ' . ( $adj['nunca'] ? '#' . $adj['nunca'] : 'portada nueva (el adjunto se crea al aplicar)' ) );
WP_CLI::log( 'Adjuntos de la versión anterior que quedan sin uso en la biblioteca (no se borran): ' . ( $viejos ? implode( ', ', array_map( fn( $f, $id ) => "$f #$id", array_keys( $viejos ), $viejos ) ) : 'ninguno encontrado' ) );

// ---- Slugs: can the old URLs be proven to answer 301? ----
$cambia = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	if ( $cambiar_slugs && $filas[ $i ]->post_name !== $N[ $i ]['slug'] ) {
		$cambia[ $i ] = true;
	}
}
$ruta_de = fn( string $url ) => rtrim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/' );
$pedir   = function ( string $url ) {
	$r = wp_remote_get( add_query_arg( 'pm_verif', (string) time(), $url ), [ 'timeout' => 30, 'redirection' => 0 ] );
	if ( is_wp_error( $r ) ) {
		return [ 0, '', $r->get_error_message(), '' ];
	}
	$loc = wp_remote_retrieve_header( $r, 'location' );
	return [ (int) wp_remote_retrieve_response_code( $r ), is_array( $loc ) ? (string) reset( $loc ) : (string) $loc, '', wp_remote_retrieve_body( $r ) ];
};
$bucle_ok = true;
if ( $cambia ) {
	WP_CLI::log( 'Slugs: ' . implode( ', ', array_map( fn( $i ) => "$i /{$filas[$i]->post_name}/ -> /{$N[$i]['slug']}/", array_keys( $cambia ) ) ) );
	WP_CLI::log( 'Mecanismo: wp_old_slug_redirect en template_redirect ' . ( false !== has_action( 'template_redirect', 'wp_old_slug_redirect' ) ? 'registrado' : 'NO registrado' ) . '; redirect_canonical ' . ( false !== has_action( 'template_redirect', 'redirect_canonical' ) ? 'registrado' : 'NO registrado' ) . '; pm_nota publica: ' . ( is_post_type_viewable( 'pm_nota' ) ? 'sí' : 'no' ) );
	foreach ( array_keys( $cambia ) as $i ) {
		[ $cod, , $err ] = $pedir( get_permalink( $filas[ $i ] ) );
		WP_CLI::log( "Loopback [$i] " . get_permalink( $filas[ $i ] ) . ': ' . ( $err ? "no se pudo pedir ($err)" : "HTTP $cod" ) );
		if ( 200 !== $cod ) {
			$bucle_ok = false;
		}
	}
	if ( ! $bucle_ok ) {
		WP_CLI::warning( 'El servidor no puede pedirse a sí mismo las URL actuales (no responden 200): sin esa prueba no se puede garantizar el 301, así que NO se cambian los slugs en esta corrida.' );
		$cambia = [];
	}
} else {
	WP_CLI::log( 'Slugs: sin cambios (' . ( $cambiar_slugs ? 'ya son los nuevos' : '$cambiar_slugs = false' ) . ').' );
}

if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado: no se escribió nada. Con aplicar=true: ' . ( count( $A ) + 2 ) . ' adjuntos (los que falten), 3 notas reemplazadas' . ( $cambia ? ', slugs nuevos con prueba del 301 (y vuelta atrás si falla)' : ', slugs sin cambio' ) . '.' );
	return;
}

// ---- Apply ----
$viejos_slug = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = $filas[ $i ];
	$n = $N[ $i ];
	$viejos_slug[ $i ] = $p->post_name;
	$nuevo_slug        = isset( $cambia[ $i ] ) ? $n['slug'] : $p->post_name;
	$choque = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'name' => $nuevo_slug, 'numberposts' => 1, 'fields' => 'ids', 'exclude' => [ $p->ID ] ] );
	! $choque || WP_CLI::error( "$i: el slug $nuevo_slug ya lo usa la nota #{$choque[0]}" );
	$id = wp_update_post( [ 'ID' => $p->ID, 'post_title' => $n['titulo'], 'post_name' => $nuevo_slug, 'post_content' => wp_slash( $plan[ $i ]['cuerpo'] ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	if ( isset( $cambia[ $i ] ) ) {
		$olds = get_post_meta( $id, '_wp_old_slug' );
		in_array( $viejos_slug[ $i ], $olds, true ) || add_post_meta( $id, '_wp_old_slug', $viejos_slug[ $i ] );
	}
	$fuentes = [];
	foreach ( [ 'ina', 'diario', 'prono', 'semanal', 'boletin' ] as $k ) {
		$fuentes[] = [ 'nombre' => $n['f'][ $k ], 'url' => $ENL[ $k ], 'oficial' => 1, 'consultada' => $lectura ];
	}
	$campos = [ 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ), 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos['nota_origen'] = $N['es']['orig'];
		if ( ! pm_campo( 'nota_informativa', $id ) ) {
			$campos['nota_informativa'] = 1;
		}
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		set_post_thumbnail( $id, $adj['nunca'] );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	clean_post_cache( $id );
	WP_CLI::log( "APLICA: [$i] #$id actualizada /" . get_post_field( 'post_name', $id ) . '/' );
}

// ---- Read-back (DB) and render checks ----
$todo_ok = true;
$nota_es = pm_nota( $filas['es']->ID );
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$id   = $filas[ $i ]->ID;
	$n    = $N[ $i ];
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_the_title( $id ) === $n['titulo'] && count( pm_filas( 'fuentes', $id ) ) === 5 && count( pm_filas( 'nota_datos', $id ) ) === 6
		&& count( pm_faqs_contenido( $cont ) ) === 4 && (string) pm_campo( 'actualizado', $id ) !== '' && (string) pm_campo( 'bajada', $id ) === $n['bajada']
		&& (string) pm_campo( 'seo_title', $id ) === $n['seo_title'] && (string) pm_campo( 'seo_description', $id ) === $n['seo_description']
		&& false !== strpos( $cont, pm_url_rio_punto( 'goya', $i ) ) && false !== strpos( $cont, esc_url( $url_de( $adj['csvd'] ) ) ) && false !== strpos( $cont, esc_url( $url_de( $adj['csvm'] ) ) )
		&& false !== strpos( $cont, 'en' === $i ? '6.10 m' : '6,10 m' );
	foreach ( [ 'nunca', 'dias', 'abajo' ] as $k ) {
		$ok = $ok && 1 === preg_match( '/wp-image-' . $adj[ $k ] . '\b/', $cont );
	}
	foreach ( $viejos as $vid ) {
		$ok = $ok && 0 === preg_match( '/wp-image-' . $vid . '\b/', $cont ) && false === strpos( $cont, '/' . basename( (string) get_post_meta( $vid, '_wp_attached_file', true ) ) );
	}
	$todo_ok = $todo_ok && $ok;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": contenido [$i] #$id «" . get_the_title( $id ) . '»' );
}
$nota_es = pm_nota( $filas['es']->ID );
$ok_es   = 'goya' === $nota_es['destino'] && 'datos' === $nota_es['tipo'] && true === $nota_es['informativa'] && (int) $nota_es['foto'] === $adj['nunca'] && '20261120' === str_replace( '-', '', $nota_es['fecha'] );
$todo_ok = $todo_ok && $ok_es;
WP_CLI::log( ( $ok_es ? 'VERIFIED' : 'FALLÓ' ) . ": fila es lee destino {$nota_es['destino']}, tipo {$nota_es['tipo']}, informativa " . ( $nota_es['informativa'] ? 'sí' : 'NO' ) . ", fecha {$nota_es['fecha']}, portada #{$nota_es['foto']}" );

$render_ok = true;
$redir_ok  = true;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$id  = $filas[ $i ]->ID;
	$url = get_permalink( $id );
	[ $cod, , $err, $html ] = $pedir( $url );
	if ( 200 !== $cod ) {
		WP_CLI::log( "RENDER [$i]: no se pudo verificar $url (" . ( $err ?: "HTTP $cod" ) . ')' );
		$render_ok = false;
		continue;
	}
	$estan = array_values( array_filter( $marcas_cta, fn( $m ) => false !== strpos( $html, $m ) ) );
	$r_ok  = false !== strpos( $html, esc_html( $N[ $i ]['titulo'] ) ) && ! $estan && false !== strpos( $html, 'class="prose"' ) && 1 === preg_match( '/wp-image-' . $adj['abajo'] . '\b/', $html );
	$render_ok = $render_ok && $r_ok;
	WP_CLI::log( ( $r_ok ? 'VERIFIED' : 'FALLÓ' ) . ": render [$i] $url con el titular nuevo, sin CTA" . ( $estan ? ' (siguen: ' . implode( ', ', $estan ) . ')' : '' ) );
	if ( isset( $cambia[ $i ] ) ) {
		$vieja = preg_replace( '#/' . preg_quote( $N[ $i ]['slug'], '#' ) . '/?$#', '/' . $viejos_slug[ $i ] . '/', $url );
		[ $c2, $loc, $e2 ] = $pedir( $vieja );
		$bien = 301 === $c2 && $ruta_de( $loc ) === $ruta_de( $url );
		$redir_ok = $redir_ok && $bien;
		WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": 301 [$i] $vieja -> " . ( $e2 ?: "HTTP $c2 " . $loc ) );
	}
}

if ( $cambia && ! $redir_ok ) {
	// The 301 could not be proven for every language: put the three old slugs back now (content stays replaced).
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		if ( isset( $cambia[ $i ] ) ) {
			wp_update_post( [ 'ID' => $filas[ $i ]->ID, 'post_name' => $viejos_slug[ $i ] ] );
			clean_post_cache( $filas[ $i ]->ID );
			WP_CLI::log( "VUELTA ATRÁS: [$i] /" . get_post_field( 'post_name', $filas[ $i ]->ID ) . '/' );
		}
	}
	WP_CLI::warning( 'No se pudo probar el 301 de las URL viejas a las nuevas: los tres slugs quedaron como estaban (el contenido sí se reemplazó). No se deja ninguna URL vieja en 404.' );
	$todo_ok = false;
}
flush_rewrite_rules( false );
( $todo_ok && $render_ok ) || WP_CLI::error( 'Alguna lectura de vuelta falló: revisar las líneas FALLÓ / RENDER de arriba.' );
WP_CLI::success( 'Aplicado: nota parana-goya reemplazada en es, en y pt' . ( $cambia ? ' con slugs nuevos y 301 verificado.' : ' (slugs sin cambio).' ) );
