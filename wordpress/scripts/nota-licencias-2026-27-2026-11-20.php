<?php
/**
 * Dated reference note «Licencia de pesca en Argentina 2026–27: cuánto cuesta en cada provincia» (clave licencias-pesca-argentina-2026-27-precios,
 * es + en + pt), 20/11/2026. Content piece made to be cited (link-earning), not to sell: marked `nota_informativa` = 1 by this same script (the
 * field lives on the Spanish row and covers the three languages: no «¿Querés pescar esto?» box, no sticky «Pedir opciones» bar). Type «datos»,
 * no destination (national piece).
 * Every figure comes from wordpress/plugins/pescamapa-core/data/licencias.json (generar-datos.py): values already published on this site WITH their
 * official source and the date we read it (Patagonian regulation read 6/11, Neuquén and Río Negro portals 16/11, Santa Cruz portal 4/11, Tierra del
 * Fuego Resolución 259/26 4/11, Corrientes Disposición 1144/2024 6/11, Entre Ríos «Costos Permisos y Licencias Oct2026» 18/11, Buenos Aires 27/10,
 * Santa Fe agreement 20/10). Nothing is estimated, converted or typed from memory; what was not read is said so (Buenos Aires prices, Santa Fe, Chaco,
 * Misiones, Corrientes prices in pesos). The infographics are made from the same JSON by wordpress/build/licencias-2026-27/graficos.py + png.mjs.
 * Files (wordpress/fotos/, copied to /tmp/pm-fotos/ by wp-script.yml): licencias-pesca-2026-27-precios.png (cover), licencias-pesca-2026-27-donde-se-saca.png,
 * licencias-pesca-2026-27.csv. Attachments are reused by _pm_archivo_origen; credit «Pescamapa», own work.
 * Idempotent: an existing note (same clave and language) is not touched; the informativa flag is only turned on if it is off. Dry run first ($aplicar = false).
 * Run with wp-script.yml (script = nota-licencias-2026-27-2026-11-20).
 */
if ( ! function_exists( 'pm_lc_p' ) ) {
	function pm_lc_h2( string $t ): string {
		return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->\n\n";
	}
	function pm_lc_p( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	function pm_lc_lista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
	}
	/** Same table markup as the other notes (core table block). */
	function pm_lc_tabla( array $cabecera, array $filas ): string {
		$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
		$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
		return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->\n\n";
	}
	/** FAQ as a group block with the class pm-faq (visible and read by pm_faqs_contenido() for the FAQPage JSON-LD). */
	function pm_lc_faq( array $faqs ): string {
		$in = '';
		foreach ( $faqs as [ $q, $a ] ) {
			$in .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( $in ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	function pm_lc_figura( int $att, string $url, string $alt, string $pie ): string {
		return "<!-- wp:image {\"id\":{$att},\"sizeSlug\":\"large\",\"linkDestination\":\"none\"} -->\n<figure class=\"wp-block-image size-large\" style=\"margin:1.5em 0\"><img src=\"" . esc_url( $url ) . "\" alt=\"" . esc_attr( $alt ) . "\" class=\"wp-image-{$att}\"/><figcaption class=\"wp-element-caption\">{$pie}</figcaption></figure>\n<!-- /wp:image -->\n\n";
	}
}

$aplicar = false;
$clave   = 'licencias-pesca-argentina-2026-27-precios';
$hoy     = '20261120';
$carpeta = '/tmp/pm-fotos';

// External links: official authorities only (pages read on the dates of the dataset).
$u = [
	'regl'  => 'https://reglamentodepesca.org.ar/reglamento/',
	'nqn'   => 'https://cazaypesca.neuquen.gob.ar/permisos',
	'rn'    => 'https://www.permisosdepesca.com.ar/',
	'rnlim' => 'https://www.permisosdepesca.com.ar/ofertas.php',
	'sc'    => 'https://pesca.santacruz.gob.ar/valores/',
	'tdf'   => 'https://prodyambiente.tierradelfuego.gob.ar/wp-content/uploads/2026/10/Resolucion-N259-Temporada-pesca-2026.pdf',
	'siped' => 'https://siped.ar/pesca-deportiva',
	'ctes'  => 'https://www.recnaturalesctes.gob.ar/',
	'er'    => 'https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva',
	'ba'    => 'https://www.gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva',
	'sf'    => 'https://www.santafe.gob.ar/noticias/noticia/287531/',
];

// ---- Attachments ----
$A = [
	'precios' => [
		'archivo' => 'licencias-pesca-2026-27-precios.png', 'titulo' => 'Licencias de pesca 2026/27: valor para extranjeros no residentes',
		'alt' => [
			'es' => 'Barras con el valor del permiso de pesca de un extranjero no residente en 2026/27, en pesos. Un día: Patagonia $ 50.000, Tierra del Fuego $ 78.000, Santa Cruz $ 90.000. Temporada: Patagonia $ 280.000, Tierra del Fuego $ 260.000, Santa Cruz $ 450.000. Aparte, sin comparar: Entre Ríos, 5 días, $ 24.251,60, y Corrientes, 16 litros de nafta súper por un día',
			'en' => 'Bars with the value of the fishing permit for a non-resident foreigner in 2026/27, in pesos. One day: Patagonia AR$ 50,000, Tierra del Fuego AR$ 78,000, Santa Cruz AR$ 90,000. Season: Patagonia AR$ 280,000, Tierra del Fuego AR$ 260,000, Santa Cruz AR$ 450,000. Outside the comparison: Entre Ríos, 5 days, AR$ 24,251.60, and Corrientes, 16 litres of super petrol for one day',
			'pt' => 'Barras com o valor da licença de pesca de um estrangeiro não residente em 2026/27, em pesos. Um dia: Patagônia AR$ 50.000, Terra do Fogo AR$ 78.000, Santa Cruz AR$ 90.000. Temporada: Patagônia AR$ 280.000, Terra do Fogo AR$ 260.000, Santa Cruz AR$ 450.000. À parte, sem comparar: Entre Ríos, 5 dias, AR$ 24.251,60, e Corrientes, 16 litros de gasolina comum por um dia',
		],
	],
	'donde' => [
		'archivo' => 'licencias-pesca-2026-27-donde-se-saca.png', 'titulo' => 'Dónde se saca la licencia de pesca en cada región',
		'alt' => [
			'es' => 'Lista de seis regiones con el sitio oficial donde se tramita la licencia: Patagonia (cazaypesca.neuquen.gob.ar y permisosdepesca.com.ar), Santa Cruz (pesca.santacruz.gob.ar, en línea), Tierra del Fuego (siped.ar y puntos de venta), Corrientes (recnaturalesctes.gob.ar, en línea), Entre Ríos (portal.entrerios.gov.ar, trámite 708) y Buenos Aires (gba.gob.ar, Desarrollo Agrario), cada una con la fecha de lectura',
			'en' => 'List of six regions with the official site where the licence is issued: Patagonia (cazaypesca.neuquen.gob.ar and permisosdepesca.com.ar), Santa Cruz (pesca.santacruz.gob.ar, online), Tierra del Fuego (siped.ar and points of sale), Corrientes (recnaturalesctes.gob.ar, online), Entre Ríos (portal.entrerios.gov.ar, procedure 708) and Buenos Aires (gba.gob.ar, Agrarian Development), each with the date we read it',
			'pt' => 'Lista de seis regiões com o site oficial onde a licença é emitida: Patagônia (cazaypesca.neuquen.gob.ar e permisosdepesca.com.ar), Santa Cruz (pesca.santacruz.gob.ar, online), Terra do Fogo (siped.ar e pontos de venda), Corrientes (recnaturalesctes.gob.ar, online), Entre Ríos (portal.entrerios.gov.ar, trâmite 708) e Buenos Aires (gba.gob.ar, Desenvolvimento Agrário), cada uma com a data de leitura',
		],
	],
];
$CSV = 'licencias-pesca-2026-27.csv';

$N = [];
$N['es'] = [
	'titulo' => 'Licencia de pesca en Argentina 2026–27: cuánto cuesta en cada provincia',
	'slug'   => 'licencia-de-pesca-argentina-2026-27-precios',
	'bajada' => 'Valores de la licencia de pesca deportiva de la temporada 2026/27 en la Patagonia, Santa Cruz, Tierra del Fuego, Corrientes y Entre Ríos, con la fuente oficial y la fecha en que la leímos.',
	'seo_title' => 'Licencia de pesca en Argentina 2026–27: precios',
	'seo_description' => 'Licencia de pesca por provincia, temporada 2026/27: un día desde $ 50.000 para extranjeros. Tabla con fuente oficial y fecha, y datos para descargar.',
	'datos' => [
		'Un extranjero no residente paga entre {m:50000} (Patagonia) y {m:90000} (Santa Cruz) por un día de pesca en 2026/27; Tierra del Fuego, {m:78000}.',
		'Por la temporada: Tierra del Fuego {m:260000}, Patagonia {m:280000}, Santa Cruz {m:450000}.',
		'Entre Ríos: {m:24251.6} por 5 días para extranjeros (octubre de 2026). Corrientes fija su licencia en litros de nafta súper: 16 por un día, 36 por seis.',
		'Un mismo permiso vale en Neuquén, Río Negro, Chubut y los Parques Nacionales; Santa Cruz y Tierra del Fuego emiten el suyo.',
		'En la página de Buenos Aires que leímos no figura el valor de la licencia; no leímos fuentes oficiales de Santa Fe, Chaco ni Misiones.',
		'Datos leídos entre el 20/10 y el 18/11/2026; los valores cambian cada temporada.',
	],
	'p0' => '<strong>Para pescar un día en Argentina, un extranjero no residente paga entre {m:50000} (Patagonia) y {m:90000} (Santa Cruz) en la temporada 2026/27; Tierra del Fuego está en el medio, con {m:78000}.</strong> No hay una licencia nacional: cada región emite la suya y publica sus valores. Entre Ríos cobra {m:24251.6} por 5 días, Corrientes fija la suya en litros de nafta súper y en la página de Buenos Aires que leímos no figura el valor.',
	'fecha' => '<em>Datos leídos entre el 20 de octubre y el 18 de noviembre de 2026. Fuentes: los organismos de cada provincia (enlazados en la tabla). Elaboración: Pescamapa.</em>',
	'h1' => 'Los valores, región por región',
	'tabla_i' => 'Pesos argentinos tal como los publica cada organismo, sin convertir. «Leído» es la fecha en que abrimos cada página. Los permisos de un día, de varios días y de temporada se listan en ese orden.',
	'tabla_cols' => [ 'Región', 'Valores', 'Dónde se saca', 'Leído' ],
	'tabla_rows' => [
		[ '<strong>Patagonia</strong>: Neuquén, Río Negro, Chubut y Parques Nacionales ({g:san-martin-junin|San Martín y Junín}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel y Trevelin})',
			'<strong>Extranjero no residente:</strong> 1 día {m:50000}; 10 días {m:140000}; temporada {m:280000}.<br><strong>Residente en el país:</strong> {m:12500}; {m:25000}; {m:50000}.<br><strong>Trolling</strong> (se suma al ordinario): {m:25000}; {m:50000}; {m:100000}.<br><strong>Río Negro, extranjero de país limítrofe:</strong> 10 días {m:50000} (vale solo en Río Negro).',
			'<a href="{u:nqn}" rel="noopener">Neuquén: Dirección de Fauna</a>; <a href="{u:rn}" rel="noopener">Río Negro: Mesa Directiva de Pesca Deportiva</a>; <a href="{u:regl}" rel="noopener">reglamento interprovincial</a>',
			'16/11/2026 (valores del reglamento: 6/11/2026)' ],
		[ '<strong>Santa Cruz</strong> ({g:rio-santa-cruz|río Santa Cruz}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos})',
			'<strong>Extranjero no residente:</strong> 1 día {m:90000}; 7 días {m:300000}; temporada {m:450000}.<br><strong>Residente en el país:</strong> 1 día {m:15000}; fin de semana {m:20000}; 7 días {m:25000}; temporada {m:40000}.<br><strong>Trolling</strong> (adicional): {m:20000}; {m:30000}; {m:40000}.',
			'<a href="{u:sc}" rel="noopener">Pesca y Acuicultura de Santa Cruz</a> (en línea)',
			'4/11/2026' ],
		[ '<strong>Tierra del Fuego</strong> ({g:rio-grande|Río Grande})',
			'<strong>Extranjero no residente:</strong> 1 día {m:78000}; 3 días {m:117000}; 7 días {m:195000}; temporada {m:260000}.<br><strong>Residente en el país:</strong> {m:26000}; {m:39000}; {m:65000}; temporada {m:90000}.<br><strong>Zona preferencial, ríos Grande y Menéndez</strong> (adicional, extranjero): {m:59000}; {m:163000}; {m:260000}; temporada {m:390000}.',
			'<a href="{u:siped}" rel="noopener">SIPED</a> y puntos de venta; <a href="{u:tdf}" rel="noopener">Resolución 259/26</a>',
			'4/11/2026' ],
		[ '<strong>Corrientes</strong> ({g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria})',
			'<strong>Licencia de turista extranjero</strong>, en litros de nafta súper: 1 día 16; 2 días 20; 3 días 24; 4 días 28; 6 días 36. El importe en pesos lo calcula el portal al comprar. Valores para residentes: no publicados en la fuente leída.',
			'<a href="{u:ctes}" rel="noopener">Recursos Naturales de Corrientes</a> (en línea)',
			'6/11/2026' ],
		[ '<strong>Entre Ríos</strong> ({g:la-paz|La Paz})',
			'<strong>Extranjero:</strong> 5 días {m:24251.6}.<br><strong>Residente de otra provincia:</strong> permiso de 4 días {m:5488.52}; licencia anual {m:24251.6}.<br><strong>Residente de Entre Ríos:</strong> licencia anual {m:15444.44}. Valores de octubre de 2026.',
			'<a href="{u:er}" rel="noopener">Portal de trámites del Gobierno de Entre Ríos</a> (trámite 708)',
			'18/11/2026' ],
		[ '<strong>Buenos Aires</strong> ({g:chascomus|Chascomús})',
			'Sin cargo: menores de 14 años y jubilados (también hay que tramitarla). Para el resto, el valor no figura en la página leída ni en el Decreto 3237/95.',
			'<a href="{u:ba}" rel="noopener">Ministerio de Desarrollo Agrario</a>',
			'27/10/2026' ],
		[ '<strong>Santa Fe</strong>',
			'No leímos valores. Solo el acuerdo de reconocimiento recíproco de licencias con Entre Ríos, Chaco y Corrientes.',
			'<a href="{u:sf}" rel="noopener">Gobierno de Santa Fe: el acuerdo</a>',
			'20/10/2026' ],
	],
	'cap1' => 'Permiso de un extranjero no residente, en pesos, 2026/27. Fuentes: organismos provinciales (ver nota). Elaboración: Pescamapa.',
	'h2' => 'Qué cambia entre provincias',
	'p2' => 'Ni las categorías ni las unidades coinciden. La Patagonia vende permisos de 1 día, 10 días y temporada; Santa Cruz, de 1 día, 7 días y temporada; Tierra del Fuego, de 1, 3 y 7 días y temporada; Entre Ríos, de 5 días para extranjeros; y Corrientes, de 1 a 6 días en litros de nafta súper. Con la misma unidad, los tres que publican un precio por día se ordenan así para un extranjero: Patagonia {m:50000}, Tierra del Fuego {m:78000}, Santa Cruz {m:90000}. Por la temporada el orden cambia: Tierra del Fuego {m:260000}, Patagonia {m:280000}, Santa Cruz {m:450000}. El permiso de 5 días de Entre Ríos ({m:24251.6}, octubre de 2026) cuesta menos que un día en la Patagonia. Además, Tierra del Fuego suma un permiso adicional en las zonas preferenciales y la Patagonia y Santa Cruz suman uno de trolling.',
	'h3' => 'La Patagonia tiene un permiso unificado',
	'p3' => 'El permiso ordinario de Neuquén, Río Negro, Chubut y la Administración de Parques Nacionales tiene las mismas categorías y valores y sirve en las cuatro jurisdicciones; Santa Cruz y Tierra del Fuego no lo usan. Se vende en línea en los portales de Neuquén y de Río Negro, con los mismos valores (leídos el 16/11/2026). La temporada de pesca va del 1 de noviembre de 2026 al 1 de mayo de 2027 y el permiso de temporada vale hasta el 31 de octubre de 2027. Río Negro vende además un permiso de 10 días para extranjeros de países limítrofes a {m:50000}, frente a {m:140000} del común, y el reglamento dice que las categorías que no figuran en el cuadro común valen solo donde se emiten. Más detalle en {n:licencia-pesca-patagonia-precios|nuestra nota de la licencia patagónica}.',
	'h4' => 'Si pescás en un Parque Nacional',
	'p4' => 'En la Patagonia, la Administración de Parques Nacionales emite el mismo permiso unificado y, según el reglamento, los permisos de las cuatro jurisdicciones valen en todos los ambientes de pesca de las cuatro. Cada anexo trae sus excepciones por río y lago: el del Parque, en la copia que leímos el 30/10/2026, dice que en los cuerpos de agua que no lista no está permitida la pesca en ninguna de sus formas. Antes de pescar un ambiente puntual, abrí el reglamento 2026/27 en el {u:regl|sitio interprovincial}.',
	'h5' => 'Residentes, no residentes y extranjeros',
	'p5' => 'Las regiones separan por residencia, no por nacionalidad: en la Patagonia, Santa Cruz y Tierra del Fuego hay una categoría «residente en el país» más barata que la del extranjero no residente. Por ejemplo, un día cuesta {m:12500} en la Patagonia, {m:15000} en Santa Cruz y {m:26000} en Tierra del Fuego para un residente, contra {m:50000}, {m:90000} y {m:78000} para un extranjero no residente. Las tres tienen categorías sin cargo (por ejemplo, jubilados y pensionados; en Tierra del Fuego, solo para pesca con devolución). En Entre Ríos la licencia anual cuesta {m:15444.44} para un residente de la provincia y {m:24251.6} para uno de otra provincia, y Buenos Aires la da sin cargo a menores de 14 años y jubilados. En Corrientes, según la disposición, las exenciones valen solo para quien tiene domicilio en la provincia.',
	'cap2' => 'Dónde se tramita cada licencia y cuándo leímos la página. Fuentes: organismos provinciales (ver nota). Elaboración: Pescamapa.',
	'h6' => 'Cómo lo armamos',
	'l6' => [
		'<strong>Leímos la página o la norma de cada organismo</strong> (enlazadas en la tabla) entre el 20 de octubre y el 18 de noviembre de 2026; la fecha de lectura está en cada fila.',
		'<strong>Pesos tal como se publican:</strong> no convertimos a dólares ni calculamos importes. Corrientes se muestra en litros de nafta súper, como lo fija la Disposición 1144/2024 (27/9/2024); el valor en pesos de ese anexo es de octubre de 2024 y no es el de hoy, así que no lo usamos.',
		'<strong>Los precios cambian cada temporada:</strong> antes de pagar, confirmá el valor en el enlace oficial.',
		'<strong>Qué falta:</strong> el valor de la licencia de Buenos Aires (no figura en la página leída); los valores para residentes de Corrientes; Santa Fe, Chaco y Misiones (no leímos sus fuentes oficiales; de Santa Fe, solo el acuerdo de reconocimiento recíproco); los permisos adicionales de Tierra del Fuego para residentes y los demás adicionales de zona.',
		'<strong>Una rareza que figura así:</strong> el adicional diario de los ríos Grande y Menéndez para extranjeros de países limítrofes ($ 65.000) es más caro que el del extranjero general ($ 59.000). Es lo que dice la resolución; confirmalo con la Secretaría de Ambiente.',
	],
	'h7' => 'Usá la tabla y los gráficos',
	'p7' => 'Podés usar esta tabla y este gráfico citando a Pescamapa con enlace a esta nota.',
	'p7b' => '<a href="{csv}" download>Descargá los datos (CSV)</a>: una fila por región, categoría y período, con el valor, la moneda o unidad, la vigencia, la fuente oficial con su enlace y la fecha en que la leímos.',
	'p7c' => 'Para el trámite paso a paso, mirá {n:permiso-pesca-argentina-paso-a-paso|cómo sacar el permiso de pesca en Argentina} y {n:pesca-desde-brasil-corrientes|la licencia de Corrientes desde Brasil}.',
	'faq_h' => 'Preguntas frecuentes',
	'faq' => [
		[ '¿Necesito licencia para pescar en Argentina?', 'Sí. No hay una licencia nacional: hace falta la de la región donde pescás. En Buenos Aires hasta los menores de 14 años deben tramitarla, aunque es gratuita. El portal de Santa Cruz aclara que el permiso es personal e intransferible. Los valores están en la tabla de arriba.' ],
		[ '¿Dónde se saca la licencia?', 'En el organismo de cada región. En la Patagonia, en línea en el portal de Neuquén o en el de Río Negro; en Santa Cruz y Corrientes, en línea en su portal; en Tierra del Fuego, registrándote en SIPED y retirando el permiso en un punto de venta; en Entre Ríos, en el portal de trámites del Gobierno (trámite 708); en Buenos Aires, siguiendo el instructivo del Ministerio de Desarrollo Agrario. Los enlaces oficiales están en la tabla.' ],
		[ '¿Sirve la licencia de una provincia en otra?', 'Depende. El permiso unificado vale en Neuquén, Río Negro, Chubut y los Parques Nacionales; el de Santa Cruz solo vale en esa provincia (Reglamento, art. 5), y Tierra del Fuego y Corrientes emiten el suyo. Las categorías propias de una provincia, como el permiso de 10 días de Río Negro para países limítrofes, valen solo donde se emiten. Santa Fe publicó un acuerdo de reconocimiento recíproco con Entre Ríos, Chaco y Corrientes: confirmá su alcance antes de viajar.' ],
		[ '¿Cuánto cuesta la licencia para un extranjero?', 'En 2026/27, por un día: Patagonia {m:50000}, Tierra del Fuego {m:78000} y Santa Cruz {m:90000}; por la temporada: {m:280000}, {m:260000} y {m:450000}. Entre Ríos cobra {m:24251.6} por 5 días (octubre de 2026) y Corrientes fija la licencia en litros de nafta súper: 16 por un día y 36 por seis. Fuentes y fechas, en la tabla.' ],
	],
	'f' => [
		'regl' => 'Reglamento General de Pesca Deportiva Continental Patagónico: categorías y valores 2026/27 (texto actualizado el 26/10/2026)',
		'nqn' => 'Dirección Provincial de Fauna de Neuquén: comprar permiso de pesca, temporada 2026-2027',
		'rn' => 'Mesa Directiva de Pesca Deportiva Zona Andina: permisos de pesca de Río Negro (venta en línea)',
		'rnlim' => 'Mesa Directiva de Pesca Deportiva Zona Andina: permiso para extranjeros no residentes de país limítrofe',
		'sc' => 'Pesca y Acuicultura de Santa Cruz: valores de los permisos 2026/27',
		'tdf' => 'Resolución 259/2026 de la Secretaría de Ambiente de Tierra del Fuego (temporada 2026/27)',
		'ctes' => 'Dirección de Recursos Naturales de Corrientes: portal de licencias; Disposición 1144/2024',
		'er' => 'Gobierno de Entre Ríos: obtener licencia y permiso de caza o pesca deportiva (trámite 708, costos de octubre de 2026)',
		'ba' => 'Ministerio de Desarrollo Agrario de Buenos Aires: obtenga su licencia de pesca deportiva',
		'sf' => 'Gobierno de Santa Fe: acuerdo de reconocimiento recíproco de licencias',
	],
	'orig' => 'Elaborado el 20/11/2026 con wordpress/build/licencias-2026-27/ (generar-datos.py → licencias.json y licencias.csv; graficos.py y png.mjs para las infografías). Cada valor viene de una página o script del repo con su fuente oficial y fecha de lectura (ver el JSON). No se estimó, convirtió ni completó nada de memoria; Corrientes en litros porque el valor en pesos del anexo es de octubre de 2024. Faltan: valor de Buenos Aires, residentes de Corrientes, Santa Fe, Chaco y Misiones.',
];
$N['en'] = [
	'titulo' => 'Fishing licence in Argentina 2026–27: what it costs, region by region',
	'slug'   => 'fishing-licence-argentina-2026-27-prices',
	'bajada' => 'Sport fishing licence values for the 2026/27 season in Patagonia, Santa Cruz, Tierra del Fuego, Corrientes and Entre Ríos, with the official source and the date we read it.',
	'seo_title' => 'Argentina fishing licence 2026–27: prices by region',
	'seo_description' => 'Fishing licence prices by Argentine province, 2026/27: one day from AR$ 50,000 for foreigners. Table with official sources and dates, plus data to download.',
	'datos' => [
		'A non-resident foreigner pays between {m:50000} (Patagonia) and {m:90000} (Santa Cruz) for one day of fishing in 2026/27; Tierra del Fuego, {m:78000}.',
		'For the season: Tierra del Fuego {m:260000}, Patagonia {m:280000}, Santa Cruz {m:450000}.',
		'Entre Ríos: {m:24251.6} for 5 days for foreigners (October 2026). Corrientes sets its licence in litres of super petrol: 16 for one day, 36 for six.',
		'One permit is valid in Neuquén, Río Negro, Chubut and the National Parks; Santa Cruz and Tierra del Fuego issue their own.',
		'The Buenos Aires page we read does not show the licence price; we did not read official sources for Santa Fe, Chaco or Misiones.',
		'Data read between 20/10 and 18/11/2026; values change every season.',
	],
	'p0' => '<strong>To fish for a day in Argentina, a non-resident foreigner pays between {m:50000} (Patagonia) and {m:90000} (Santa Cruz) in the 2026/27 season; Tierra del Fuego is in the middle, at {m:78000}.</strong> There is no national licence: each region issues its own and publishes its values. Entre Ríos charges {m:24251.6} for 5 days, Corrientes sets its licence in litres of super petrol, and the Buenos Aires page we read does not show the price.',
	'fecha' => '<em>Data read between 20 October and 18 November 2026. Sources: each province’s authority (linked in the table). Prepared by Pescamapa.</em>',
	'h1' => 'The values, region by region',
	'tabla_i' => 'Argentine pesos as each authority publishes them, not converted. “Read” is the date we opened each page. One-day, multi-day and season permits are listed in that order.',
	'tabla_cols' => [ 'Region', 'Values', 'Where to get it', 'Read' ],
	'tabla_rows' => [
		[ '<strong>Patagonia</strong>: Neuquén, Río Negro, Chubut and National Parks ({g:san-martin-junin|San Martín and Junín}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel and Trevelin})',
			'<strong>Non-resident foreigner:</strong> 1 day {m:50000}; 10 days {m:140000}; season {m:280000}.<br><strong>Resident of Argentina:</strong> {m:12500}; {m:25000}; {m:50000}.<br><strong>Trolling</strong> (on top of the ordinary permit): {m:25000}; {m:50000}; {m:100000}.<br><strong>Río Negro, foreigner from a bordering country:</strong> 10 days {m:50000} (valid only in Río Negro).',
			'<a href="{u:nqn}" rel="noopener">Neuquén: Wildlife Directorate</a>; <a href="{u:rn}" rel="noopener">Río Negro: Sport Fishing Board</a>; <a href="{u:regl}" rel="noopener">interprovincial regulation</a>',
			'16/11/2026 (regulation values: 6/11/2026)' ],
		[ '<strong>Santa Cruz</strong> ({g:rio-santa-cruz|Santa Cruz River}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos})',
			'<strong>Non-resident foreigner:</strong> 1 day {m:90000}; 7 days {m:300000}; season {m:450000}.<br><strong>Resident of Argentina:</strong> 1 day {m:15000}; weekend {m:20000}; 7 days {m:25000}; season {m:40000}.<br><strong>Trolling</strong> (additional): {m:20000}; {m:30000}; {m:40000}.',
			'<a href="{u:sc}" rel="noopener">Santa Cruz Fishing and Aquaculture</a> (online)',
			'4/11/2026' ],
		[ '<strong>Tierra del Fuego</strong> ({g:rio-grande|Río Grande})',
			'<strong>Non-resident foreigner:</strong> 1 day {m:78000}; 3 days {m:117000}; 7 days {m:195000}; season {m:260000}.<br><strong>Resident of Argentina:</strong> {m:26000}; {m:39000}; {m:65000}; season {m:90000}.<br><strong>Preferential zone, Grande and Menéndez rivers</strong> (additional, foreigner): {m:59000}; {m:163000}; {m:260000}; season {m:390000}.',
			'<a href="{u:siped}" rel="noopener">SIPED</a> and points of sale; <a href="{u:tdf}" rel="noopener">Resolución 259/26</a>',
			'4/11/2026' ],
		[ '<strong>Corrientes</strong> ({g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria})',
			'<strong>Foreign tourist licence</strong>, in litres of super petrol: 1 day 16; 2 days 20; 3 days 24; 4 days 28; 6 days 36. The portal calculates the peso amount when you buy. Values for residents: not published in the source we read.',
			'<a href="{u:ctes}" rel="noopener">Corrientes Natural Resources</a> (online)',
			'6/11/2026' ],
		[ '<strong>Entre Ríos</strong> ({g:la-paz|La Paz})',
			'<strong>Foreigner:</strong> 5 days {m:24251.6}.<br><strong>Resident of another province:</strong> 4-day permit {m:5488.52}; annual licence {m:24251.6}.<br><strong>Resident of Entre Ríos:</strong> annual licence {m:15444.44}. October 2026 values.',
			'<a href="{u:er}" rel="noopener">Entre Ríos Government procedures portal</a> (procedure 708)',
			'18/11/2026' ],
		[ '<strong>Buenos Aires</strong> ({g:chascomus|Chascomús})',
			'Free: children under 14 and retirees (they must still apply). For everyone else, the price does not appear on the page we read or in Decree 3237/95.',
			'<a href="{u:ba}" rel="noopener">Ministry of Agrarian Development</a>',
			'27/10/2026' ],
		[ '<strong>Santa Fe</strong>',
			'We did not read values. Only the reciprocal recognition agreement on licences with Entre Ríos, Chaco and Corrientes.',
			'<a href="{u:sf}" rel="noopener">Santa Fe Government: the agreement</a>',
			'20/10/2026' ],
	],
	'cap1' => 'Permit for a non-resident foreigner, in pesos, 2026/27. Sources: provincial authorities (see note). Prepared by Pescamapa.',
	'h2' => 'What changes between provinces',
	'p2' => 'Neither the categories nor the units match. Patagonia sells 1-day, 10-day and season permits; Santa Cruz, 1-day, 7-day and season; Tierra del Fuego, 1, 3 and 7 days and season; Entre Ríos, 5 days for foreigners; and Corrientes, 1 to 6 days in litres of super petrol. In the same unit, the three that publish a daily price rank like this for a foreigner: Patagonia {m:50000}, Tierra del Fuego {m:78000}, Santa Cruz {m:90000}. For the season the order changes: Tierra del Fuego {m:260000}, Patagonia {m:280000}, Santa Cruz {m:450000}. The 5-day Entre Ríos permit ({m:24251.6}, October 2026) costs less than one day in Patagonia. In addition, Tierra del Fuego adds a permit for the preferential zones, and Patagonia and Santa Cruz add one for trolling.',
	'h3' => 'Patagonia has a single permit',
	'p3' => 'The ordinary permit of Neuquén, Río Negro, Chubut and the National Parks Administration has the same categories and values and works in all four jurisdictions; Santa Cruz and Tierra del Fuego do not use it. It is sold online on the Neuquén and Río Negro portals, with the same values (read on 16/11/2026). The fishing season runs from 1 November 2026 to 1 May 2027 and the season permit is valid until 31 October 2027. Río Negro also sells a 10-day permit for foreigners from bordering countries at {m:50000}, against {m:140000} for the common one, and the regulation says that categories not in the common table are valid only where they are issued. More detail in {n:licencia-pesca-patagonia-precios|our Patagonian licence note}.',
	'h4' => 'If you fish in a National Park',
	'p4' => 'In Patagonia, the National Parks Administration issues the same single permit and, according to the regulation, the permits of the four jurisdictions are valid in all fishing waters of the four. Each annex has its exceptions by river and lake: the Parks annex, in the copy we read on 30/10/2026, says that fishing is not allowed in any form in the bodies of water it does not list. Before fishing a specific water, open the 2026/27 regulation on the {u:regl|interprovincial site}.',
	'h5' => 'Residents, non-residents and foreigners',
	'p5' => 'The regions split by residence, not nationality: in Patagonia, Santa Cruz and Tierra del Fuego there is a “resident of Argentina” category that is cheaper than the non-resident foreigner one. For example, one day costs {m:12500} in Patagonia, {m:15000} in Santa Cruz and {m:26000} in Tierra del Fuego for a resident, against {m:50000}, {m:90000} and {m:78000} for a non-resident foreigner. All three have free categories (for example retirees and pensioners; in Tierra del Fuego, for catch-and-release only). In Entre Ríos the annual licence costs {m:15444.44} for a resident of the province and {m:24251.6} for one from another province, and Buenos Aires gives it free to children under 14 and retirees. In Corrientes, according to the regulation, exemptions apply only to people domiciled in the province.',
	'cap2' => 'Where each licence is issued and when we read the page. Sources: provincial authorities (see note). Prepared by Pescamapa.',
	'h6' => 'How we put it together',
	'l6' => [
		'<strong>We read each authority’s page or regulation</strong> (linked in the table) between 20 October and 18 November 2026; the read date is on each row.',
		'<strong>Pesos as published:</strong> we do not convert to dollars or calculate amounts. Corrientes is shown in litres of super petrol, as set by Disposición 1144/2024 (27/9/2024); the peso value in that annex is from October 2024 and is not today’s, so we do not use it.',
		'<strong>Prices change every season:</strong> before paying, confirm the value on the official link.',
		'<strong>What is missing:</strong> the Buenos Aires licence price (not on the page we read); resident values for Corrientes; Santa Fe, Chaco and Misiones (we did not read their official sources; for Santa Fe, only the reciprocal recognition agreement); Tierra del Fuego’s additional permits for residents and the other zone additions.',
		'<strong>An oddity, as published:</strong> the daily additional permit for the Grande and Menéndez rivers for foreigners from bordering countries (AR$ 65,000) is more expensive than the general foreigner’s (AR$ 59,000). That is what the resolution says; confirm it with the Secretaría de Ambiente.',
	],
	'h7' => 'Use the table and the charts',
	'p7' => 'You can use this table and this chart if you credit Pescamapa with a link to this note.',
	'p7b' => '<a href="{csv}" download>Download the data (CSV)</a> (column names in Spanish): one row per region, category and period, with the value, currency or unit, validity, the official source with its link and the date we read it.',
	'p7c' => 'For the step-by-step procedure, see {n:permiso-pesca-argentina-paso-a-paso|how to get a fishing permit in Argentina} and {n:pesca-desde-brasil-corrientes|the Corrientes licence from Brazil}.',
	'faq_h' => 'Frequently asked questions',
	'faq' => [
		[ 'Do I need a licence to fish in Argentina?', 'Yes. There is no national licence: you need the one for the region where you fish. In Buenos Aires even children under 14 must apply, although it is free. The Santa Cruz portal states that the permit is personal and non-transferable. The values are in the table above.' ],
		[ 'Where do I get the licence?', 'From each region’s authority. In Patagonia, online on the Neuquén or the Río Negro portal; in Santa Cruz and Corrientes, online on their portal; in Tierra del Fuego, by registering on SIPED and collecting the permit at a point of sale; in Entre Ríos, on the Government procedures portal (procedure 708); in Buenos Aires, following the Ministry of Agrarian Development’s instructions. The official links are in the table.' ],
		[ 'Is a licence from one province valid in another?', 'It depends. The single permit is valid in Neuquén, Río Negro, Chubut and the National Parks; Santa Cruz’s is valid only in that province (Regulation, art. 5), and Tierra del Fuego and Corrientes issue their own. Categories specific to a province, such as Río Negro’s 10-day permit for bordering countries, are valid only where they are issued. Santa Fe published a reciprocal recognition agreement with Entre Ríos, Chaco and Corrientes: confirm its scope before travelling.' ],
		[ 'How much is the licence for a foreigner?', 'In 2026/27, for one day: Patagonia {m:50000}, Tierra del Fuego {m:78000} and Santa Cruz {m:90000}; for the season: {m:280000}, {m:260000} and {m:450000}. Entre Ríos charges {m:24251.6} for 5 days (October 2026) and Corrientes sets the licence in litres of super petrol: 16 for one day and 36 for six. Sources and dates are in the table.' ],
	],
	'f' => [
		'regl' => 'Continental Patagonia General Sport Fishing Regulation: 2026/27 categories and values (text updated 26/10/2026)',
		'nqn' => 'Neuquén Provincial Wildlife Directorate: buy a fishing permit, 2026-2027 season',
		'rn' => 'Mesa Directiva de Pesca Deportiva Zona Andina: Río Negro fishing permits (online sales)',
		'rnlim' => 'Mesa Directiva de Pesca Deportiva Zona Andina: permit for non-resident foreigners from a bordering country',
		'sc' => 'Santa Cruz Fishing and Aquaculture: 2026/27 permit values',
		'tdf' => 'Resolución 259/2026 of the Tierra del Fuego Secretaría de Ambiente (2026/27 season)',
		'ctes' => 'Corrientes Dirección de Recursos Naturales: licence portal; Disposición 1144/2024',
		'er' => 'Entre Ríos Government: obtain a hunting or sport fishing licence and permit (procedure 708, October 2026 costs)',
		'ba' => 'Buenos Aires Ministry of Agrarian Development: get your sport fishing licence',
		'sf' => 'Santa Fe Government: reciprocal licence recognition agreement',
	],
];
$N['pt'] = [
	'titulo' => 'Licença de pesca na Argentina 2026–27: quanto custa em cada região',
	'slug'   => 'licenca-de-pesca-argentina-2026-27-precos',
	'bajada' => 'Valores da licença de pesca esportiva da temporada 2026/27 na Patagônia, Santa Cruz, Terra do Fogo, Corrientes e Entre Ríos, com a fonte oficial e a data em que a lemos.',
	'seo_title' => 'Licença de pesca na Argentina 2026–27: preços',
	'seo_description' => 'Licença de pesca por província argentina, 2026/27: um dia desde AR$ 50.000 para estrangeiros. Tabela com fonte oficial e data, e dados para baixar.',
	'datos' => [
		'Um estrangeiro não residente paga entre {m:50000} (Patagônia) e {m:90000} (Santa Cruz) por um dia de pesca em 2026/27; Terra do Fogo, {m:78000}.',
		'Pela temporada: Terra do Fogo {m:260000}, Patagônia {m:280000}, Santa Cruz {m:450000}.',
		'Entre Ríos: {m:24251.6} por 5 dias para estrangeiros (outubro de 2026). Corrientes fixa a licença em litros de gasolina comum: 16 por um dia, 36 por seis.',
		'Uma mesma licença vale em Neuquén, Río Negro, Chubut e nos Parques Nacionais; Santa Cruz e Terra do Fogo emitem a sua.',
		'Na página de Buenos Aires que lemos não consta o valor da licença; não lemos fontes oficiais de Santa Fe, Chaco nem Misiones.',
		'Dados lidos entre 20/10 e 18/11/2026; os valores mudam a cada temporada.',
	],
	'p0' => '<strong>Para pescar um dia na Argentina, um estrangeiro não residente paga entre {m:50000} (Patagônia) e {m:90000} (Santa Cruz) na temporada 2026/27; Terra do Fogo fica no meio, com {m:78000}.</strong> Não existe licença nacional: cada região emite a sua e publica os valores. Entre Ríos cobra {m:24251.6} por 5 dias, Corrientes fixa a sua em litros de gasolina comum e na página de Buenos Aires que lemos o valor não consta.',
	'fecha' => '<em>Dados lidos entre 20 de outubro e 18 de novembro de 2026. Fontes: os órgãos de cada província (com link na tabela). Elaboração: Pescamapa.</em>',
	'h1' => 'Os valores, região por região',
	'tabla_i' => 'Pesos argentinos como cada órgão os publica, sem converter. «Lido» é a data em que abrimos cada página. As licenças de um dia, de vários dias e de temporada aparecem nessa ordem.',
	'tabla_cols' => [ 'Região', 'Valores', 'Onde tirar', 'Lido' ],
	'tabla_rows' => [
		[ '<strong>Patagônia</strong>: Neuquén, Río Negro, Chubut e Parques Nacionais ({g:san-martin-junin|San Martín e Junín}, {g:bariloche|Bariloche}, {g:esquel-trevelin|Esquel e Trevelin})',
			'<strong>Estrangeiro não residente:</strong> 1 dia {m:50000}; 10 dias {m:140000}; temporada {m:280000}.<br><strong>Residente no país:</strong> {m:12500}; {m:25000}; {m:50000}.<br><strong>Trolling</strong> (soma-se à ordinária): {m:25000}; {m:50000}; {m:100000}.<br><strong>Río Negro, estrangeiro de país limítrofe:</strong> 10 dias {m:50000} (vale só em Río Negro).',
			'<a href="{u:nqn}" rel="noopener">Neuquén: Direção de Fauna</a>; <a href="{u:rn}" rel="noopener">Río Negro: Mesa Directiva de Pesca Deportiva</a>; <a href="{u:regl}" rel="noopener">regulamento interprovincial</a>',
			'16/11/2026 (valores do regulamento: 6/11/2026)' ],
		[ '<strong>Santa Cruz</strong> ({g:rio-santa-cruz|rio Santa Cruz}, {g:jurassic-lake|Jurassic Lake}, {g:rio-gallegos|Río Gallegos})',
			'<strong>Estrangeiro não residente:</strong> 1 dia {m:90000}; 7 dias {m:300000}; temporada {m:450000}.<br><strong>Residente no país:</strong> 1 dia {m:15000}; fim de semana {m:20000}; 7 dias {m:25000}; temporada {m:40000}.<br><strong>Trolling</strong> (adicional): {m:20000}; {m:30000}; {m:40000}.',
			'<a href="{u:sc}" rel="noopener">Pesca e Aquicultura de Santa Cruz</a> (online)',
			'4/11/2026' ],
		[ '<strong>Terra do Fogo</strong> ({g:rio-grande|Río Grande})',
			'<strong>Estrangeiro não residente:</strong> 1 dia {m:78000}; 3 dias {m:117000}; 7 dias {m:195000}; temporada {m:260000}.<br><strong>Residente no país:</strong> {m:26000}; {m:39000}; {m:65000}; temporada {m:90000}.<br><strong>Zona preferencial, rios Grande e Menéndez</strong> (adicional, estrangeiro): {m:59000}; {m:163000}; {m:260000}; temporada {m:390000}.',
			'<a href="{u:siped}" rel="noopener">SIPED</a> e pontos de venda; <a href="{u:tdf}" rel="noopener">Resolución 259/26</a>',
			'4/11/2026' ],
		[ '<strong>Corrientes</strong> ({g:goya|Goya}, {g:esquina|Esquina}, {g:paso-de-la-patria|Paso de la Patria})',
			'<strong>Licença de turista estrangeiro</strong>, em litros de gasolina comum: 1 dia 16; 2 dias 20; 3 dias 24; 4 dias 28; 6 dias 36. O valor em pesos é calculado pelo portal na compra. Valores para residentes: não publicados na fonte lida.',
			'<a href="{u:ctes}" rel="noopener">Recursos Naturais de Corrientes</a> (online)',
			'6/11/2026' ],
		[ '<strong>Entre Ríos</strong> ({g:la-paz|La Paz})',
			'<strong>Estrangeiro:</strong> 5 dias {m:24251.6}.<br><strong>Residente de outra província:</strong> licença de 4 dias {m:5488.52}; licença anual {m:24251.6}.<br><strong>Residente de Entre Ríos:</strong> licença anual {m:15444.44}. Valores de outubro de 2026.',
			'<a href="{u:er}" rel="noopener">Portal de trâmites do Governo de Entre Ríos</a> (trâmite 708)',
			'18/11/2026' ],
		[ '<strong>Buenos Aires</strong> ({g:chascomus|Chascomús})',
			'Sem custo: menores de 14 anos e aposentados (também precisam tirá-la). Para os demais, o valor não consta na página lida nem no Decreto 3237/95.',
			'<a href="{u:ba}" rel="noopener">Ministério de Desenvolvimento Agrário</a>',
			'27/10/2026' ],
		[ '<strong>Santa Fe</strong>',
			'Não lemos valores. Só o acordo de reconhecimento recíproco de licenças com Entre Ríos, Chaco e Corrientes.',
			'<a href="{u:sf}" rel="noopener">Governo de Santa Fe: o acordo</a>',
			'20/10/2026' ],
	],
	'cap1' => 'Licença de um estrangeiro não residente, em pesos, 2026/27. Fontes: órgãos provinciais (ver nota). Elaboração: Pescamapa.',
	'h2' => 'O que muda entre as províncias',
	'p2' => 'Nem as categorias nem as unidades coincidem. A Patagônia vende licenças de 1 dia, 10 dias e temporada; Santa Cruz, de 1 dia, 7 dias e temporada; Terra do Fogo, de 1, 3 e 7 dias e temporada; Entre Ríos, de 5 dias para estrangeiros; e Corrientes, de 1 a 6 dias em litros de gasolina comum. Na mesma unidade, as três que publicam um preço por dia se ordenam assim para um estrangeiro: Patagônia {m:50000}, Terra do Fogo {m:78000}, Santa Cruz {m:90000}. Pela temporada a ordem muda: Terra do Fogo {m:260000}, Patagônia {m:280000}, Santa Cruz {m:450000}. A licença de 5 dias de Entre Ríos ({m:24251.6}, outubro de 2026) custa menos que um dia na Patagônia. Além disso, Terra do Fogo soma uma licença adicional nas zonas preferenciais e a Patagônia e Santa Cruz somam uma de trolling.',
	'h3' => 'A Patagônia tem uma licença unificada',
	'p3' => 'A licença ordinária de Neuquén, Río Negro, Chubut e da Administração de Parques Nacionais tem as mesmas categorias e valores e serve nas quatro jurisdições; Santa Cruz e Terra do Fogo não a usam. É vendida online nos portais de Neuquén e de Río Negro, com os mesmos valores (lidos em 16/11/2026). A temporada de pesca vai de 1.º de novembro de 2026 a 1.º de maio de 2027 e a licença de temporada vale até 31 de outubro de 2027. Río Negro vende também uma licença de 10 dias para estrangeiros de países limítrofes a {m:50000}, contra {m:140000} da comum, e o regulamento diz que as categorias que não constam do quadro comum valem só onde são emitidas. Mais detalhes em {n:licencia-pesca-patagonia-precios|nossa nota da licença patagônica}.',
	'h4' => 'Se você pesca em um Parque Nacional',
	'p4' => 'Na Patagônia, a Administração de Parques Nacionais emite a mesma licença unificada e, segundo o regulamento, as licenças das quatro jurisdições valem em todos os ambientes de pesca das quatro. Cada anexo traz suas exceções por rio e lago: o do Parque, na cópia que lemos em 30/10/2026, diz que nos corpos de água que não lista não é permitida a pesca de nenhuma forma. Antes de pescar um ambiente específico, abra o regulamento 2026/27 no {u:regl|site interprovincial}.',
	'h5' => 'Residentes, não residentes e estrangeiros',
	'p5' => 'As regiões separam por residência, não por nacionalidade: na Patagônia, em Santa Cruz e em Terra do Fogo há uma categoria «residente no país» mais barata que a do estrangeiro não residente. Por exemplo, um dia custa {m:12500} na Patagônia, {m:15000} em Santa Cruz e {m:26000} em Terra do Fogo para um residente, contra {m:50000}, {m:90000} e {m:78000} para um estrangeiro não residente. As três têm categorias sem custo (por exemplo, aposentados e pensionistas; em Terra do Fogo, só para pesca com devolução). Em Entre Ríos a licença anual custa {m:15444.44} para um residente da província e {m:24251.6} para um de outra província, e Buenos Aires a concede sem custo a menores de 14 anos e aposentados. Em Corrientes, segundo a disposição, as isenções valem só para quem tem domicílio na província.',
	'cap2' => 'Onde se tira cada licença e quando lemos a página. Fontes: órgãos provinciais (ver nota). Elaboração: Pescamapa.',
	'h6' => 'Como montamos',
	'l6' => [
		'<strong>Lemos a página ou a norma de cada órgão</strong> (com link na tabela) entre 20 de outubro e 18 de novembro de 2026; a data de leitura está em cada linha.',
		'<strong>Pesos como são publicados:</strong> não convertemos para dólares nem calculamos valores. Corrientes aparece em litros de gasolina comum, como fixa a Disposición 1144/2024 (27/9/2024); o valor em pesos desse anexo é de outubro de 2024 e não é o de hoje, então não o usamos.',
		'<strong>Os preços mudam a cada temporada:</strong> antes de pagar, confirme o valor no link oficial.',
		'<strong>O que falta:</strong> o valor da licença de Buenos Aires (não consta na página lida); os valores para residentes de Corrientes; Santa Fe, Chaco e Misiones (não lemos suas fontes oficiais; de Santa Fe, só o acordo de reconhecimento recíproco); as licenças adicionais de Terra do Fogo para residentes e os demais adicionais de zona.',
		'<strong>Uma esquisitice, como consta:</strong> o adicional diário dos rios Grande e Menéndez para estrangeiros de países limítrofes (AR$ 65.000) é mais caro que o do estrangeiro geral (AR$ 59.000). É o que diz a resolução; confirme com a Secretaría de Ambiente.',
	],
	'h7' => 'Use a tabela e os gráficos',
	'p7' => 'Você pode usar esta tabela e este gráfico citando a Pescamapa com link para esta nota.',
	'p7b' => '<a href="{csv}" download>Baixe os dados (CSV)</a> (nomes das colunas em espanhol): uma linha por região, categoria e período, com o valor, a moeda ou unidade, a vigência, a fonte oficial com o link e a data em que a lemos.',
	'p7c' => 'Para o passo a passo, veja {n:permiso-pesca-argentina-paso-a-paso|como tirar a licença de pesca na Argentina} e {n:pesca-desde-brasil-corrientes|a licença de Corrientes a partir do Brasil}.',
	'faq_h' => 'Perguntas frequentes',
	'faq' => [
		[ 'Preciso de licença para pescar na Argentina?', 'Sim. Não existe licença nacional: é preciso a da região onde você pesca. Em Buenos Aires até os menores de 14 anos devem tirá-la, embora seja gratuita. O portal de Santa Cruz esclarece que a licença é pessoal e intransferível. Os valores estão na tabela acima.' ],
		[ 'Onde se tira a licença?', 'No órgão de cada região. Na Patagônia, online no portal de Neuquén ou no de Río Negro; em Santa Cruz e Corrientes, online no portal; em Terra do Fogo, cadastrando-se no SIPED e retirando a licença em um ponto de venda; em Entre Ríos, no portal de trâmites do Governo (trâmite 708); em Buenos Aires, seguindo o instrutivo do Ministério de Desenvolvimento Agrário. Os links oficiais estão na tabela.' ],
		[ 'A licença de uma província vale em outra?', 'Depende. A licença unificada vale em Neuquén, Río Negro, Chubut e nos Parques Nacionais; a de Santa Cruz vale só nessa província (Regulamento, art. 5), e Terra do Fogo e Corrientes emitem a sua. As categorias próprias de uma província, como a licença de 10 dias de Río Negro para países limítrofes, valem só onde são emitidas. Santa Fe publicou um acordo de reconhecimento recíproco com Entre Ríos, Chaco e Corrientes: confirme o alcance antes de viajar.' ],
		[ 'Quanto custa a licença para um estrangeiro?', 'Em 2026/27, por um dia: Patagônia {m:50000}, Terra do Fogo {m:78000} e Santa Cruz {m:90000}; pela temporada: {m:280000}, {m:260000} e {m:450000}. Entre Ríos cobra {m:24251.6} por 5 dias (outubro de 2026) e Corrientes fixa a licença em litros de gasolina comum: 16 por um dia e 36 por seis. Fontes e datas, na tabela.' ],
	],
	'f' => [
		'regl' => 'Regulamento Geral de Pesca Esportiva Continental Patagônico: categorias e valores 2026/27 (texto atualizado em 26/10/2026)',
		'nqn' => 'Direção Provincial de Fauna de Neuquén: comprar licença de pesca, temporada 2026-2027',
		'rn' => 'Mesa Directiva de Pesca Deportiva Zona Andina: licenças de pesca de Río Negro (venda online)',
		'rnlim' => 'Mesa Directiva de Pesca Deportiva Zona Andina: licença para estrangeiros não residentes de país limítrofe',
		'sc' => 'Pesca e Aquicultura de Santa Cruz: valores das licenças 2026/27',
		'tdf' => 'Resolución 259/2026 da Secretaría de Ambiente de Terra do Fogo (temporada 2026/27)',
		'ctes' => 'Dirección de Recursos Naturales de Corrientes: portal de licenças; Disposición 1144/2024',
		'er' => 'Governo de Entre Ríos: obter licença e permissão de caça ou pesca esportiva (trâmite 708, custos de outubro de 2026)',
		'ba' => 'Ministério de Desenvolvimento Agrário de Buenos Aires: obtenha sua licença de pesca esportiva',
		'sf' => 'Governo de Santa Fe: acordo de reconhecimento recíproco de licenças',
	],
];
// Sources: key => [ url, official, date read ] (same as licencias.json)
$FU = [
	'regl' => [ $u['regl'], 1, '20261106' ], 'nqn' => [ $u['nqn'], 1, '20261116' ], 'rn' => [ $u['rn'], 1, '20261116' ], 'rnlim' => [ $u['rnlim'], 1, '20261106' ],
	'sc' => [ $u['sc'], 1, '20261104' ], 'tdf' => [ $u['tdf'], 1, '20261104' ], 'ctes' => [ $u['ctes'], 1, '20261106' ], 'er' => [ $u['er'], 1, '20261118' ],
	'ba' => [ $u['ba'], 1, '20261027' ], 'sf' => [ $u['sf'], 1, '20261020' ],
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
		update_field( 'foto_autor', 'Pescamapa', $att );
		update_field( 'foto_origen', 'propia', $att );
	}
	wp_update_post( [ 'ID' => $att, 'post_content' => "Elaboración propia de Pescamapa ($hoy), hecha con wordpress/build/licencias-2026-27/ a partir de licencias.json (valores con fuente oficial y fecha de lectura)." ] );
	return (int) $att;
};
foreach ( $A as $k => $a ) {
	$adj[ $k ] = $subir( $a['archivo'], $a['titulo'], $a['alt'] );
}
$adj['csv'] = $subir( $CSV, 'Licencias de pesca 2026/27: valores por región, fuente y fecha de lectura (CSV)', null );
$url_de  = fn( int $id ) => $id ? (string) wp_get_attachment_url( $id ) : '';
$url_img = fn( int $id ) => $id ? ( (string) wp_get_attachment_image_url( $id, 'large' ) ?: $url_de( $id ) ) : '';

// ---- Resolver: {m:NUM} pesos, {g:clave|texto} guide, {n:clave|texto} note, {u:clave|texto} or {u:clave} external, {csv} ----
$res = function ( string $html, string $i ) use ( $u, $adj, $url_de ): string {
	$html = preg_replace_callback( '/\{m:([0-9.]+)\}/', function ( $m ) use ( $i ) {
		$v   = (float) $m[1];
		$dec = ( floor( $v ) === $v ) ? 0 : 2;
		return 'es' === $i ? '$ ' . number_format( $v, $dec, ',', '.' ) : ( 'en' === $i ? 'AR$ ' . number_format( $v, $dec, '.', ',' ) : 'AR$ ' . number_format( $v, $dec, ',', '.' ) );
	}, $html );
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
	$html = preg_replace_callback( '/\{u:([a-z0-9]+)\|([^}]+)\}/', function ( $m ) use ( $u ) {
		isset( $u[ $m[1] ] ) || WP_CLI::error( "enlace externo desconocido {$m[1]}" );
		return '<a href="' . esc_url( $u[ $m[1] ] ) . '" rel="noopener">' . $m[2] . '</a>';
	}, $html );
	$html = preg_replace_callback( '/\{u:([a-z0-9]+)\}/', function ( $m ) use ( $u ) {
		isset( $u[ $m[1] ] ) || WP_CLI::error( "enlace externo desconocido {$m[1]}" );
		return esc_url( $u[ $m[1] ] );
	}, $html );
	return str_replace( '{csv}', esc_url( $url_de( $adj['csv'] ) ?: '#csv-pendiente-en-simulacro' ), $html );
};

// ---- The notes ----
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
	$fig = fn( string $k, string $cap ) => pm_lc_figura( $adj[ $k ], $url_img( $adj[ $k ] ) ?: '#imagen-pendiente-en-simulacro', $A[ $k ]['alt'][ $i ], esc_html( $res( $cap, $i ) ) );
	$cuerpo = pm_lc_p( $res( $n['p0'], $i ) ) . pm_lc_p( $res( $n['fecha'], $i ) )
		. pm_lc_h2( $n['h1'] ) . pm_lc_p( $res( $n['tabla_i'], $i ) )
		. pm_lc_tabla( $n['tabla_cols'], array_map( fn( $f ) => array_map( fn( $c ) => $res( $c, $i ), $f ), $n['tabla_rows'] ) )
		. $fig( 'precios', $n['cap1'] )
		. pm_lc_h2( $n['h2'] ) . pm_lc_p( $res( $n['p2'], $i ) )
		. pm_lc_h2( $n['h3'] ) . pm_lc_p( $res( $n['p3'], $i ) )
		. pm_lc_h2( $n['h4'] ) . pm_lc_p( $res( $n['p4'], $i ) )
		. pm_lc_h2( $n['h5'] ) . pm_lc_p( $res( $n['p5'], $i ) ) . $fig( 'donde', $n['cap2'] )
		. pm_lc_h2( $n['h6'] ) . pm_lc_lista( array_map( fn( $x ) => $res( $x, $i ), $n['l6'] ) )
		. pm_lc_h2( $n['h7'] ) . pm_lc_p( $res( $n['p7'], $i ) ) . pm_lc_p( $res( $n['p7b'], $i ) ) . pm_lc_p( $res( $n['p7c'], $i ) )
		. pm_lc_h2( $n['faq_h'] ) . pm_lc_faq( array_map( fn( $q ) => [ $q[0], $res( $q[1], $i ) ], $n['faq'] ) );
	! preg_match( '/\{[a-z0-9:|.-]+/', $cuerpo, $m ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	foreach ( [ 'operador', 'operator', 'operadora', 'vosotros' ] as $mala ) {
		false === stripos( $cuerpo, $mala ) || WP_CLI::error( "$i: palabra no permitida «$mala»" );
	}
	$fuentes = [];
	foreach ( $FU as $k => [ $url, $ofi, $fecha ] ) {
		$fuentes[] = [ 'nombre' => $n['f'][ $k ], 'url' => $url, 'oficial' => $ofi, 'consultada' => $fecha ];
	}
	$datos = array_map( fn( $d ) => $res( $d, $i ), $n['datos'] );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['tabla_rows'] ) . ' filas de tabla, ' . count( $datos ) . ' datos, ' . count( $fuentes ) . ' fuentes, ' . count( $n['faq'] ) . " FAQ; title $tl / description $dl; portada #{$adj['precios']}" );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $datos ), 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'datos', 'nota_fecha' => $hoy, 'nota_informativa' => 1, 'nota_origen' => $N['es']['orig'] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		set_post_thumbnail( $id, $adj['precios'] );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$cont = get_post_field( 'post_content', $id );
	$ok   = get_post( $id )->post_name === $n['slug'] && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $datos )
		&& count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && false !== strpos( $cont, 'wp-image-' . $adj['precios'] ) && false !== strpos( $cont, 'wp-image-' . $adj['donde'] )
		&& 1 === substr_count( $cont, '<table>' ) && count( $n['tabla_rows'] ) === substr_count( $cont, '<tr><td>' )
		&& false !== strpos( $cont, esc_url( $url_de( $adj['csv'] ) ) ) && false !== strpos( $cont, esc_url( $u['sc'] ) ) && false !== strpos( $cont, 'en' === $i ? '50,000' : '50.000' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
// The informative flag lives on the Spanish row (valid for es/en/pt by clave): turned on here, only if it is off.
$es = $existe( 'es' )[0] ?? null;
if ( $es ) {
	$on = (bool) pm_campo( 'nota_informativa', $es->ID );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": nota_informativa en la fila es #{$es->ID}: " . ( $on ? 'ya activa' : 'apagada -> activa' ) );
	if ( $aplicar && ! $on ) {
		update_field( 'nota_informativa', 1, $es->ID );
	}
}
if ( $aplicar ) {
	// pm_buscar() caches the published notes before this run created them: look the new one up directly.
	$nota_es = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $x ) => 'es' === pm_idioma_de( $x->ID ) ) )[0] ?? null;
	$nota_es || WP_CLI::error( 'no encuentro la nota es recién creada.' );
	clean_post_cache( $nota_es->ID );
	$e  = pm_nota( $nota_es->ID );
	$ok = 'datos' === $e['tipo'] && '' === (string) $e['destino'] && $e['informativa'] && (int) $e['foto'] === $adj['precios'];
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee tipo ' . ( $e['tipo'] ?? '?' ) . ', destino «' . ( $e['destino'] ?? '?' ) . '», informativa ' . ( $e['informativa'] ? 'sí' : 'NO' ) . ', fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
	$ok || WP_CLI::error( 'la nota es no lee tipo, destino, informativa o foto como se esperaba.' );
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$x = $existe( $i )[0] ?? null;
		$x || WP_CLI::error( "falta la nota ($i)." );
		$r = wp_remote_get( add_query_arg( 'pm_verif', time(), get_permalink( $x->ID ) ), [ 'timeout' => 30, 'redirection' => 3 ] );
		if ( is_wp_error( $r ) || 200 !== (int) wp_remote_retrieve_response_code( $r ) ) {
			WP_CLI::log( "RENDER [{$i}]: no se pudo pedir la página: solo verificada la meta" );
			continue;
		}
		$html  = wp_remote_retrieve_body( $r );
		$estan = array_values( array_filter( [ 'class="cta-nota"', 'class="barra-pedir"', 'class="guias-especie"' ], fn( $m ) => false !== strpos( $html, $m ) ) );
		WP_CLI::log( ( ! $estan && false !== strpos( $html, 'class="prose"' ) ? 'VERIFIED' : 'FALLÓ' ) . ": render [{$i}] sin CTA " . ( $estan ? '(siguen: ' . implode( ', ', $estan ) . ')' : '' ) );
	}
	flush_rewrite_rules( false );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
