<?php
/**
 * New note "La Zona (Salto Grande)" (es/en/pt siblings, clave la-zona-salto-grande), 13/11/2026. Answer first, every statement with its
 * resolution and date, read on 13/11/2026 in: CARU Res. 27/2004 (updated text), 63/2026, 111/2023, 01/2023, CARU notices of 22/9 and
 * 7/10/2026, Entre Ríos Boletín Oficial of 28/1/2026 (Decreto 3874/25) and the tender document 01-2026, the Salto Grande Joint Technical
 * Commission flows report, the IGFA page, and the operators' own sites. NOT written: agency prices, names of people, a total length of the
 * zone, who holds the Argentine permit, whether the closed season applies inside the zone (all stated as unknown).
 * Also replaces the pillar's "we have not read the rules" line with a pointer to the note. Idempotent. Simulacro first ($aplicar = false).
 * Run with wp-script.yml (script = nota-la-zona-2026-11-13).
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
$hoy     = '20261113';
$clave   = 'la-zona-salto-grande';
$cambios = 0;

$u = [
	'reg'   => 'https://www.argentina.gob.ar/normativa/nacional/norma-102959/actualizacion',
	'r63'   => 'https://caru.org.uy/nuevositio/wp-content/uploads/2026/09/RESOLUCION-N%C2%B0-63-26_Modifica-Reglamento-de-Pesca_Area-pesca-deportiva.pdf',
	'a63'   => 'https://caru.org.uy/nuevositio/2026/09/22/caru-establece-nuevo-limite-para-la-pesca-deportiva-en-la-zona-de-seguridad-de-salto-grande-resolucion-63-26/',
	'r01'   => 'https://www.caru.org.uy/web/wp-content/uploads/2023/03/BO-AR-Res-CARU-N%C2%B0-01-23.pdf',
	'r111'  => 'https://www.caru.org.uy/web/wp-content/uploads/2023/08/RESOLUCION-N-111-23-Veda-del-Dorado.pdf',
	'a111'  => 'https://caru.org.uy/nuevositio/2026/10/07/veda-del-dorado-2026-1-09-31-12/',
	'old'   => 'https://servicios.infoleg.gob.ar/infolegInternet/anexos/160000-164999/163143/norma.htm',
	'bo'    => 'https://www.entrerios.gov.ar/boletin/calendario/Boletin/2026/Enero/28-01-26.pdf',
	'pliego' => 'https://www.entrerios.gov.ar/boletin/calendario/Boletin/anexos/2026/anexo_3450.pdf',
	'libre' => 'https://libreentrerios.com/pesca-del-dorado-en-salto-grande-relanzan-concurso-fracasado',
	'ctm'   => 'https://www.saltogrande.org/docs/hidrologia/CaudalesNiveles.pdf',
	'igfa'  => 'https://igfa.org/game-fish-database/?search_term_1=126&search_type=SpeciesID',
	'muni'  => 'https://www.concordia.gob.ar/turismo/atractivos/pesca-deportiva',
	'lodge' => 'https://lazonalodge.com/',
	'rp'    => 'https://www.riverplatedorados.com/',
	'ag'    => 'https://www.fishingwithlarry.com/lazonalodge-argentina.html',
	'cron'  => 'https://www.cronista.com/clase/break/el-extraordinario-rio-donde-se-pescan-los-mejores-dorados-esta-cerca-de-caba-y-tiene-pique-todo-el-ano',
	'impo'  => 'https://www.impo.com.uy/bases/resoluciones-originales/795-2020',
	'uno'   => 'https://www.diariouno.com.ar/sociedad/el-pez-dorado-mas-grande-del-mundo-es-un-rio-argentina-supera-los-25-kilos-n1532888',
];

$N = [];
$N['es'] = [
	'titulo' => 'La Zona (Salto Grande): reglas del dorado y acceso, 2026',
	'slug' => 'la-zona-salto-grande-pesca-dorado',
	'seo_title' => 'La Zona (Salto Grande): reglas del dorado y acceso, 2026',
	'seo_description' => 'La Zona, bajo la represa de Salto Grande: reglas de la CARU para el dorado, veda, quién puede llevarte y lo que todavía no se sabe. Con fuentes y fechas.',
	'bajada' => 'Qué es La Zona, qué dice el reglamento de la CARU, cómo se aplica la veda, quién puede llevarte y qué falta saber, con cada dato fechado y con su fuente.',
	'datos' => [
		'La Zona es el nombre que los pescadores le dan a la Zona de Seguridad aguas abajo de la Represa de Salto Grande, en el río Uruguay, entre Concordia (Argentina) y Salto (Uruguay).',
		'Solo dorado, con devolución, desde embarcaciones autorizadas, de lunes a jueves y de día, desde el 1/6/2023 (CARU).',
		'La pesca empieza a 500 m de la presa desde la Resolución CARU 63/2026 (Boletín Oficial del 28/9/2026); de 2023 a 2026 fue a 200 m.',
		'Veda del dorado en el río Uruguay: del 1 de septiembre al 31 de diciembre (CARU, Resolución 111/2023). No encontramos un texto que exceptúe la zona.',
		'Permisionario del lado argentino: al 13/11/2026 no encontramos una adjudicación publicada.',
		'Precio: a consultar. No publicamos tarifas de agencias.',
	],
	'p0' => '<strong>Respuesta corta.</strong> La Zona es la Zona de Seguridad aguas abajo de la Represa de Salto Grande, en el río Uruguay, entre Concordia y Salto. Solo se pesca dorado, con devolución, desde embarcaciones autorizadas, de lunes a jueves y de día, y hay que contratar con el permisionario que habilita cada país. <strong>Al 13/11/2026 no encontramos publicado quién tiene el permiso del lado argentino.</strong> La veda del dorado en el río Uruguay va del 1 de septiembre al 31 de diciembre y no encontramos un texto que exceptúe la zona.',
	'h_que' => 'Qué es La Zona y dónde empieza',
	'que1' => '«La Zona» es como llaman los pescadores a la <strong>Zona de Seguridad aguas abajo de la Represa de Salto Grande</strong>, en el río Uruguay, entre Concordia (Argentina) y Salto (Uruguay). La pesca deportiva regulada ocupa el área que va desde una línea imaginaria paralela al eje de la presa, a <strong>500 metros</strong> aguas abajo, hasta el final de la zona de seguridad (Reglamento de Pesca con Devolución de la CARU, Resolución 27/2004 y sus reformas, texto actualizado leído el 13/11/2026). El límite cambió dos veces:',
	'que_l' => [
		'<strong>500 m</strong> desde la Resolución CARU 46/16 (22/9/2016).',
		'<strong>200 m</strong> desde la Resolución CARU 01/2023 (5/1/2023; publicada en el Boletín Oficial argentino el 7/3/2023).',
		'<strong>500 m otra vez</strong> desde la Resolución CARU 63/2026, firmada el 21/9/2026 y publicada en el Boletín Oficial el 28/9/2026, a pedido de la Comisión Técnica Mixta de Salto Grande, que invoca la seguridad de la presa, de la navegación y de las personas.',
	],
	'que2' => 'No publicamos un largo total de la zona: ningún texto de la CARU que leímos lo da.',
	'h_reg' => 'Quién la regula',
	'reg1' => 'La CARU, la Comisión Administradora del Río Uruguay (binacional), dicta el reglamento. La resolución que aprobó el reglamento deja a cada Estado lo referido a permisos de pesca, adjudicaciones y fiscalización, y cada Estado puede habilitar <strong>un solo permisionario</strong> (art. 4.2 a). Las autoridades de aplicación y control son, en Argentina, la Dirección de Recursos Naturales de Entre Ríos (hoy Dirección de Recursos Naturales y Fiscalización) y, en Uruguay, la Dirección Nacional de Recursos Acuáticos (DINARA), junto con las Prefecturas Navales de los dos países (art. 2.1).',
	'h_reglas' => 'Las reglas del reglamento',
	'reglas_i' => 'Son los artículos del reglamento de la CARU, en su texto actualizado:',
	'reglas' => [
		'<strong>Solo dorado y solo con devolución</strong> (arts. 5.1 y 10.3). Los concursos de pesca, que autoriza la autoridad, son la excepción a la especie.',
		'<strong>Solo desde embarcaciones autorizadas:</strong> no se puede pescar desde la costa ni desembarcar, salvo por emergencia (art. 3.1).',
		'<strong>Señuelo artificial o mosca</strong> (fly casting, spinning y bait casting), con <strong>anzuelo sin rebaba</strong> o con la rebaba aplastada o rellenada; <strong>sin carnada</strong> y <strong>sin trolling</strong> (arts. 7.1 y 8.1). Un solo equipo por pescador (art. 9.1).',
		'<strong>Máximo dos minutos fuera del agua</strong> para cada pez (art. 10.3).',
		'<strong>Hasta cuatro embarcaciones a la vez</strong>, no más de dos por país en cada turno; hasta cuatro personas a bordo, guía y timonel incluidos; embarcaciones de 4,50 a 6,50 m de eslora y 15 km/h como máximo (art. 11).',
		'<strong>De lunes a jueves, solo con luz diurna</strong>, desde el 1 de junio de 2023 (Resolución CARU 37/2023, Boletín Oficial del 11/4/2023). Los textos anteriores (el Digesto de la CARU de 2018 y un llamado uruguayo de 2020, entre ellos) dicen de viernes a lunes.',
		'<strong>Clientes:</strong> contratan con el permisionario y llevan los documentos que acrediten ese vínculo y su identidad; el permisionario comunica a la Prefectura la filiación de sus clientes con al menos 48 horas de antelación (art. 4.2 b y c).',
	],
	'h_veda' => 'La veda del dorado: cómo se aplica acá',
	'veda1' => 'La CARU fija la veda del dorado en el río Uruguay del <strong>1 de septiembre al 31 de diciembre de cada año</strong>, para la pesca comercial y deportiva (Resolución CARU 111/2023, del 1/8/2023; aviso de la CARU del 7/10/2026). El reglamento de La Zona dice que su temporada «se ajustará a lo establecido por la CARU en el río Uruguay». No encontramos ningún texto que exceptúe la zona de la veda, y tampoco uno que confirme que rige adentro: queda como pregunta abierta (abajo). Un sitio de agencia que vende viajes a La Zona Lodge lista septiembre a noviembre como «mid-season» y llama a La Zona una pesquería de todo el año; preguntale al operador, por escrito, cómo se aplica la veda a tus fechas. La página de turismo de la Municipalidad de Concordia da otras fechas (del 16 de octubre al 14 de enero, con devolución); la norma es la resolución de la CARU, no esa página.',
	'h_quien' => 'Quién puede llevarte: lo que no está resuelto',
	'quien1' => 'Para pescar en La Zona hay que contratar con el permisionario de uno de los dos países. <strong>Lado argentino:</strong> el llamado de 2023 fue declarado fracasado por el Decreto 3874/25 de Entre Ríos (20/12/2025; Boletín Oficial del 28/1/2026), porque la Resolución CARU 207/2023 prohibió marcar peces y eso cambió las condiciones del llamado. Después figura un nuevo pliego, el Concurso Público de Méritos y Antecedentes N° 01-2026 (permiso de diez años, hasta dos embarcaciones, pesca de lunes a jueves). Al 13/11/2026 no encontramos una adjudicación publicada.',
	'quien2' => '<strong>Lo que ofrece el mercado.</strong> La Zona Lodge (Concordia) ofrece, según su propio sitio, pesca en lanchas debajo de la represa de Salto Grande. Del lado uruguayo, River Plate Dorados dice en su propio sitio que en 2013 adquirió en una subasta pública el derecho exclusivo de usar las dos únicas embarcaciones; que está del lado uruguayo lo dice el sitio de una agencia. No pudimos verificar con un texto oficial qué permiso cubre a cada uno en 2026, y no decimos que a alguno le falte. Del lado uruguayo hubo además un llamado en 2020 (Resolución 795/2020 del Ministerio de Ganadería, Agricultura y Pesca, 12/8/2020) para un permisionario por cinco años; no leímos su resultado.',
	'quien3' => 'Precio: a consultar. No publicamos tarifas de agencias. Si querés comparar, {viaje}.',
	'viaje_t' => 'pedinos opciones para pescar dorado y preguntamos a los operadores que encajan',
	'h_pre' => 'Qué preguntar antes de pagar',
	'pre' => [
		'¿Qué permiso cubre el viaje en tus fechas? Pedí el número de la resolución o del decreto.',
		'¿Qué días vas a pescar? El reglamento dice de lunes a jueves.',
		'¿Cuántos pescadores por embarcación? A bordo caben cuatro personas, guía y timonel incluidos.',
		'¿Cómo se aplica la veda del 1 de septiembre al 31 de diciembre a tus fechas? Pedilo por escrito.',
		'¿El permisionario va a comunicar tus datos a la Prefectura con 48 horas de antelación?',
	],
	'h_rio' => 'Cómo llegar y cómo está el río',
	'rio1' => 'Concordia está a unos 430 km de Buenos Aires, según El Cronista (nota leída el 13/11/2026), algo más de cinco horas por ruta. Las descargas de la represa cambian el río. La Comisión Técnica Mixta de Salto Grande publica un {ctm}. Ejemplo, lectura de las 09:00 del 13/11/2026: nivel en Concordia 8,57 m; caudal total erogado por Salto Grande, medio diario, 11.787 m³/s. Es el dato de ese día, no una predicción. Todavía no tenemos una página de altura del río para Concordia.',
	'ctm_t' => 'reporte diario de caudales y niveles (PDF)',
	'h_rec' => 'El récord',
	'rec1' => 'El récord mundial todo equipo del dorado en la IGFA es de 25,28 kg; la ficha de la IGFA muestra solo el peso. Según la prensa (Diario Uno, consultado el 24/10/2026), se pescó en el río Uruguay en 2006. Más en {esp} y en {pilar}.',
	'esp_t' => 'la ficha del dorado', 'pilar_t' => 'la guía de pesca del dorado en la Argentina',
	'h_falta' => 'Lo que no sabemos todavía',
	'falta' => [
		'Quién es el permisionario del lado argentino, y si el concurso N° 01-2026 ya tiene adjudicación.',
		'Si la veda del 1 de septiembre al 31 de diciembre rige dentro de La Zona.',
		'Las tasas de licencia, tallas y cupos de Entre Ríos para el río Uruguay: no las leímos en una página oficial.',
		'Las tarifas que publican los propios operadores.',
		'Si el permiso uruguayo del llamado de 2020 sigue vigente.',
		'El largo total de la zona: ningún texto de la CARU que leímos lo da (el sitio de una agencia habla de 1.000 metros).',
	],
	'faq_h' => 'Preguntas frecuentes',
	'faq' => [
		[ '¿Qué es La Zona en la Argentina?', 'Es como llaman los pescadores a la Zona de Seguridad aguas abajo de la Represa de Salto Grande, en el río Uruguay, entre Concordia (Argentina) y Salto (Uruguay). El reglamento de la CARU permite pescar dorado con devolución desde una línea a 500 m de la presa hasta el final de esa zona (Resolución 27/2004 y reformas; los 500 m rigen desde la Resolución 63/2026).' ],
		[ '¿Puede pescar cualquiera en La Zona?', 'No. Se pesca solo desde embarcaciones autorizadas y contratando con el permisionario que habilita cada país, uno por Estado; no se puede pescar desde la costa. El cliente lleva los documentos que acrediten su vínculo con el permisionario y su identidad, y el permisionario avisa a la Prefectura con al menos 48 horas. Al 13/11/2026 no encontramos publicado quién tiene el permiso del lado argentino.' ],
		[ '¿Qué días se puede pescar en La Zona?', 'De lunes a jueves y solo con luz diurna, desde el 1 de junio de 2023 (Resolución CARU 37/2023). Los textos anteriores, como el Digesto de la CARU de 2018, dicen de viernes a lunes.' ],
		[ '¿Cuándo es la veda del dorado en el río Uruguay?', 'Del 1 de septiembre al 31 de diciembre de cada año, para la pesca comercial y deportiva (Resolución CARU 111/2023; aviso de la CARU del 7/10/2026). No encontramos un texto que exceptúe La Zona, y el reglamento dice que su temporada se ajusta a lo que establece la CARU: preguntale al operador, por escrito, cómo se aplica a tus fechas.' ],
	],
	'f' => [
		'reg' => 'CARU, Resolución 27/2004: Reglamento de Pesca con Devolución en la Zona de Seguridad aguas abajo de la Represa de Salto Grande, texto actualizado (Argentina.gob.ar)',
		'r63' => 'CARU, Resolución 63/2026 (21/9/2026; Boletín Oficial 28/9/2026): límite de 500 m',
		'a63' => 'CARU: aviso del 22/9/2026 sobre la Resolución 63/26',
		'r01' => 'CARU, Resolución 01/2023 (5/1/2023), Boletín Oficial argentino del 7/3/2023: límite de 200 m',
		'old' => 'Infoleg: reforma de la Resolución CARU 27/2004 que fijó la pesca de viernes a lunes (texto anterior, para comparar)',
		'r111' => 'CARU, Resolución 111/2023 (1/8/2023): veda del dorado del 1 de septiembre al 31 de diciembre',
		'a111' => 'CARU: aviso «Veda del dorado 2026» (7/10/2026)',
		'bo' => 'Boletín Oficial de Entre Ríos del 28/1/2026: Decreto 3874/25 (20/12/2025), concurso declarado fracasado',
		'pliego' => 'Entre Ríos, Dirección de Recursos Naturales y Fiscalización: Pliego del Concurso Público de Méritos y Antecedentes N° 01-2026',
		'libre' => 'Libreentrerios (17/3/2026): estado del nuevo concurso (prensa)',
		'impo' => 'Uruguay, Ministerio de Ganadería, Agricultura y Pesca: Resolución 795/2020 (12/8/2020), llamado a un permisionario',
		'ctm' => 'Comisión Técnica Mixta de Salto Grande: reporte de caudales y niveles del 13/11/2026, 09:00',
		'igfa' => 'IGFA: base de datos de peces de caza, dorado (récord todo equipo de 25,28 kg, solo el peso)',
		'uno' => 'Diario Uno: el dorado más grande del mundo (récord, río Uruguay, 2006; prensa)',
		'muni' => 'Municipalidad de Concordia: pesca deportiva (fechas de veda distintas a las de la CARU)',
		'lodge' => 'La Zona Fishing Lodge, sitio propio (operador)',
		'rp' => 'River Plate Dorados, sitio propio (operador)',
		'ag' => 'Sitio de una agencia que vende viajes a La Zona Lodge (temporadas y lado uruguayo)',
		'cron' => 'El Cronista: el río donde se pescan los mejores dorados (distancia desde Buenos Aires)',
	],
];
$N['en'] = [
	'titulo' => 'La Zona (Salto Grande): golden dorado rules and access, 2026',
	'slug' => 'la-zona-salto-grande-golden-dorado',
	'seo_title' => 'La Zona (Salto Grande): golden dorado rules and access',
	'seo_description' => 'La Zona below the Salto Grande dam: CARU rules for golden dorado, the closed season, who can take you and what is still unknown. Sources and dates.',
	'bajada' => 'What La Zona is, what the CARU rules say, how the closed season applies, who can take you and what is still unknown, with every fact dated and sourced.',
	'datos' => [
		'La Zona is the name anglers give to the Safety Zone downstream of the Salto Grande Dam on the Uruguay River, between Concordia (Argentina) and Salto (Uruguay).',
		'Golden dorado only, catch and release, from authorised boats, Monday to Thursday and daylight only since 1 June 2023 (CARU).',
		'Fishing starts 500 m from the dam since CARU Resolution 63/2026 (Boletín Oficial, 28 September 2026); from 2023 to 2026 it was 200 m.',
		'Golden dorado closed season on the Uruguay River: 1 September to 31 December (CARU, Resolution 111/2023). We found no text that exempts the zone.',
		'Argentine-side permit holder: as of 13 November 2026 we found no published award.',
		'Price: on request. We do not publish agency rates.',
	],
	'p0' => '<strong>Short answer.</strong> La Zona is the Safety Zone downstream of the Salto Grande Dam on the Uruguay River, between Concordia and Salto. Only golden dorado is fished, catch and release, from authorised boats, Monday to Thursday in daylight, and you must book with the permit holder each country authorises. <strong>As of 13 November 2026 we found no published record of who holds the Argentine-side permit.</strong> The golden dorado closed season on the Uruguay River runs from 1 September to 31 December and we found no text that exempts the zone.',
	'h_que' => 'What La Zona is and where it starts',
	'que1' => '“La Zona” is what anglers call the <strong>Safety Zone downstream of the Salto Grande Dam</strong> on the Uruguay River, between Concordia (Argentina) and Salto (Uruguay). The regulated sport-fishing area runs from an imaginary line parallel to the dam axis, <strong>500 metres</strong> downstream, to the end of the safety zone (CARU Catch-and-Release Fishing Regulation, Resolution 27/2004 as amended, updated text read on 13 November 2026). The line has changed twice:',
	'que_l' => [
		'<strong>500 m</strong> since CARU Resolution 46/16 (22 September 2016).',
		'<strong>200 m</strong> since CARU Resolution 01/2023 (5 January 2023; published in the Argentine Boletín Oficial on 7 March 2023).',
		'<strong>500 m again</strong> since CARU Resolution 63/2026, signed on 21 September 2026 and published in the Boletín Oficial on 28 September 2026, at the request of the Salto Grande Joint Technical Commission, which cites the safety of the dam, of navigation and of people.',
	],
	'que2' => 'We do not state a total length for the zone: no CARU text we read gives one.',
	'h_reg' => 'Who regulates it',
	'reg1' => 'CARU, the Uruguay River Administrative Commission (binational), issues the regulation. The resolution that approved the regulation leaves permits, awards and enforcement to each State, and each State may authorise <strong>one permit holder</strong> (art. 4.2 a). The enforcing authorities are, in Argentina, the Entre Ríos Dirección de Recursos Naturales (now the Dirección de Recursos Naturales y Fiscalización) and, in Uruguay, the Dirección Nacional de Recursos Acuáticos (DINARA), together with the Naval Prefectures of both countries (art. 2.1).',
	'h_reglas' => 'The rules in the regulation',
	'reglas_i' => 'These are the articles of the CARU regulation, in its updated text:',
	'reglas' => [
		'<strong>Golden dorado only, and catch and release only</strong> (arts. 5.1 and 10.3). Fishing contests, which the authority authorises, are the exception to the species rule.',
		'<strong>Only from authorised boats:</strong> no fishing from the shore and no landing, except in an emergency (art. 3.1).',
		'<strong>Artificial lure or fly</strong> (fly casting, spinning and bait casting), with a <strong>barbless hook</strong> or one with the barb crushed or filled; <strong>no bait</strong> and <strong>no trolling</strong> (arts. 7.1 and 8.1). One rod per angler (art. 9.1).',
		'<strong>No more than two minutes out of the water</strong> for each fish (art. 10.3).',
		'<strong>Up to four boats at a time</strong>, no more than two per country per shift; up to four people on board, guide and skipper included; boats of 4.50 to 6.50 m in length and 15 km/h at most (art. 11).',
		'<strong>Monday to Thursday, daylight only</strong>, since 1 June 2023 (CARU Resolution 37/2023, Boletín Oficial of 11 April 2023). Earlier texts (the 2018 CARU Digesto and a 2020 Uruguayan call, among them) say Friday to Monday.',
		'<strong>Clients:</strong> book with the permit holder and carry documents that prove that link and their identity; the permit holder notifies the Prefectura of its clients\' identities at least 48 hours ahead (art. 4.2 b and c).',
	],
	'h_veda' => 'The golden dorado closed season: how it applies here',
	'veda1' => 'CARU sets the golden dorado closed season on the Uruguay River from <strong>1 September to 31 December every year</strong>, for commercial and sport fishing (CARU Resolution 111/2023, of 1 August 2023; CARU notice of 7 October 2026). The La Zona regulation says its season “will follow what CARU establishes on the Uruguay River” (our translation). We found no text that exempts the zone from the closed season, and none that confirms it applies inside: it stays an open question (below). An agency site that sells trips to La Zona Lodge lists September to November as “mid-season” and calls La Zona a year-round fishery; ask the operator, in writing, how the closed season applies to your dates. The Concordia municipality tourism page gives different dates (16 October to 14 January, with release); the CARU resolution is the norm, not that page.',
	'h_quien' => 'Who can take you: what is unresolved',
	'quien1' => 'To fish La Zona you must book with the permit holder of one of the two countries. <strong>Argentine side:</strong> the 2023 call was declared failed by Entre Ríos Decree 3874/25 (20 December 2025; Boletín Oficial of 28 January 2026), because CARU Resolution 207/2023 banned tagging fish and that changed the terms of the call. A new tender document then appears, Concurso Público de Méritos y Antecedentes N° 01-2026 (ten-year permit, up to two boats, fishing Monday to Thursday). As of 13 November 2026 we found no published award.',
	'quien2' => '<strong>What the market offers.</strong> La Zona Lodge (Concordia) offers, according to its own site, fishing from boats below the Salto Grande dam. On the Uruguayan side, River Plate Dorados says on its own site that in 2013 it acquired at a public auction the exclusive right to use the only two boats; that it is on the Uruguayan side comes from an agency site. We could not verify from an official text which permit covers each of them in 2026, and we do not say either is missing one. On the Uruguayan side there was also a call in 2020 (Resolution 795/2020 of the Ministry of Livestock, Agriculture and Fisheries, 12 August 2020) for one permit holder for five years; we did not read its outcome.',
	'quien3' => 'Price: on request. We do not publish agency rates. If you want to compare, {viaje}.',
	'viaje_t' => 'ask us for options to fish golden dorado and we ask the operators that fit',
	'h_pre' => 'What to ask before you pay',
	'pre' => [
		'Which permit covers the trip on your dates? Ask for the resolution or decree number.',
		'Which days will you fish? The regulation says Monday to Thursday.',
		'How many anglers per boat? Four people fit on board, guide and skipper included.',
		'How does the 1 September to 31 December closed season apply to your dates? Ask in writing.',
		'Will the permit holder notify the Prefectura of your details 48 hours ahead?',
	],
	'h_rio' => 'Getting there and how the river is',
	'rio1' => 'Concordia is about 430 km from Buenos Aires, according to El Cronista (article read on 13 November 2026), a little over five hours by road. Dam releases change the river. The Salto Grande Joint Technical Commission publishes a {ctm}. Example, reading at 09:00 on 13 November 2026: Concordia level 8.57 m; total discharge from Salto Grande, daily mean, 11,787 m³/s. It is that day\'s figure, not a forecast. We do not have a river-level page for Concordia yet.',
	'ctm_t' => 'daily report of flows and levels (PDF)',
	'h_rec' => 'The record',
	'rec1' => 'The IGFA all-tackle world record for the golden dorado is 25.28 kg; the IGFA page shows only the weight. According to the press (Diario Uno, consulted 24 October 2026), it was caught in the Uruguay River in 2006. More on {esp} and in {pilar}.',
	'esp_t' => 'the golden dorado species page', 'pilar_t' => 'our golden dorado fishing guide',
	'h_falta' => 'What we still do not know',
	'falta' => [
		'Who the Argentine-side permit holder is, and whether tender N° 01-2026 has been awarded.',
		'Whether the 1 September to 31 December closed season applies inside La Zona.',
		'Entre Ríos licence fees, sizes and bag limits for the Uruguay River: we did not read them on an official page.',
		'The rates the operators publish themselves.',
		'Whether the Uruguayan permit from the 2020 call is still in force.',
		'The total length of the zone: no CARU text we read gives one (one agency site says 1,000 metres).',
	],
	'faq_h' => 'Frequently asked questions',
	'faq' => [
		[ 'What is La Zona in Argentina?', 'It is what anglers call the Safety Zone downstream of the Salto Grande Dam on the Uruguay River, between Concordia (Argentina) and Salto (Uruguay). The CARU regulation allows catch-and-release fishing for golden dorado from a line 500 m from the dam to the end of that zone (Resolution 27/2004 as amended; the 500 m applies since Resolution 63/2026).' ],
		[ 'Can anyone fish La Zona?', 'No. You fish only from authorised boats and by booking with the permit holder each country authorises, one per State; shore fishing is not allowed. Clients carry documents proving their link with the permit holder and their identity, and the permit holder notifies the Prefectura at least 48 hours ahead. As of 13 November 2026 we found no published record of who holds the Argentine-side permit.' ],
		[ 'Which days can you fish La Zona?', 'Monday to Thursday and in daylight only, since 1 June 2023 (CARU Resolution 37/2023). Earlier texts, such as the 2018 CARU Digesto, say Friday to Monday.' ],
		[ 'When is the golden dorado closed season on the Uruguay River?', '1 September to 31 December every year, for commercial and sport fishing (CARU Resolution 111/2023; CARU notice of 7 October 2026). We found no text that exempts La Zona, and the regulation says its season follows what CARU establishes: ask the operator, in writing, how it applies to your dates.' ],
	],
	'f' => [
		'reg' => 'CARU, Resolution 27/2004: Catch-and-Release Fishing Regulation in the Safety Zone downstream of the Salto Grande Dam, updated text (Argentina.gob.ar)',
		'r63' => 'CARU, Resolution 63/2026 (21/9/2026; Boletín Oficial 28/9/2026): 500 m limit',
		'a63' => 'CARU: notice of 22/9/2026 on Resolution 63/26',
		'r01' => 'CARU, Resolution 01/2023 (5/1/2023), Argentine Boletín Oficial of 7/3/2023: 200 m limit',
		'old' => 'Infoleg: amendment of CARU Resolution 27/2004 that set fishing Friday to Monday (earlier text, for comparison)',
		'r111' => 'CARU, Resolution 111/2023 (1/8/2023): golden dorado closed season, 1 September to 31 December',
		'a111' => 'CARU: notice "Veda del dorado 2026" (7/10/2026)',
		'bo' => 'Entre Ríos Boletín Oficial of 28/1/2026: Decree 3874/25 (20/12/2025), call declared failed',
		'pliego' => 'Entre Ríos, Dirección de Recursos Naturales y Fiscalización: tender document, Concurso Público de Méritos y Antecedentes N° 01-2026',
		'libre' => 'Libreentrerios (17/3/2026): status of the new tender (press)',
		'impo' => 'Uruguay, Ministry of Livestock, Agriculture and Fisheries: Resolution 795/2020 (12/8/2020), call for a permit holder',
		'ctm' => 'Salto Grande Joint Technical Commission: flows and levels report of 13/11/2026, 09:00',
		'igfa' => 'IGFA: game fish database, dorado (all-tackle record of 25.28 kg, weight only)',
		'uno' => 'Diario Uno: the biggest golden dorado in the world (record, Uruguay River, 2006; press)',
		'muni' => 'Municipality of Concordia: sport fishing (closed-season dates different from CARU\'s)',
		'lodge' => 'La Zona Fishing Lodge, own site (operator)',
		'rp' => 'River Plate Dorados, own site (operator)',
		'ag' => 'Site of an agency that sells trips to La Zona Lodge (seasons and Uruguayan side)',
		'cron' => 'El Cronista: the river where the best dorados are caught (distance from Buenos Aires)',
	],
];
$N['pt'] = [
	'titulo' => 'La Zona (Salto Grande): regras do dourado e acesso, 2026',
	'slug' => 'la-zona-salto-grande-pesca-dourado',
	'seo_title' => 'La Zona (Salto Grande): regras do dourado e acesso',
	'seo_description' => 'La Zona, abaixo da represa de Salto Grande: regras da CARU para o dourado, defeso, quem pode levar você e o que ainda não se sabe. Com fontes e datas.',
	'bajada' => 'O que é La Zona, o que diz o regulamento da CARU, como o defeso se aplica, quem pode levar você e o que falta saber, com cada dado datado e com sua fonte.',
	'datos' => [
		'La Zona é o nome que os pescadores dão à Zona de Segurança a jusante da Represa de Salto Grande, no rio Uruguai, entre Concordia (Argentina) e Salto (Uruguai).',
		'Só dourado, com devolução, em embarcações autorizadas, de segunda a quinta e de dia, desde 1/6/2023 (CARU).',
		'A pesca começa a 500 m da barragem desde a Resolução CARU 63/2026 (Boletim Oficial de 28/9/2026); de 2023 a 2026 foi a 200 m.',
		'Defeso do dourado no rio Uruguai: de 1.º de setembro a 31 de dezembro (CARU, Resolução 111/2023). Não encontramos texto que isente a zona.',
		'Permissionário do lado argentino: em 13/11/2026 não encontramos adjudicação publicada.',
		'Preço: sob consulta. Não publicamos tarifas de agências.',
	],
	'p0' => '<strong>Resposta curta.</strong> La Zona é a Zona de Segurança a jusante da Represa de Salto Grande, no rio Uruguai, entre Concordia e Salto. Só se pesca dourado, com devolução, em embarcações autorizadas, de segunda a quinta e de dia, e é preciso contratar com o permissionário que cada país habilita. <strong>Em 13/11/2026 não encontramos publicado quem tem a licença do lado argentino.</strong> O defeso do dourado no rio Uruguai vai de 1.º de setembro a 31 de dezembro e não encontramos texto que isente a zona.',
	'h_que' => 'O que é La Zona e onde começa',
	'que1' => '«La Zona» é como os pescadores chamam a <strong>Zona de Segurança a jusante da Represa de Salto Grande</strong>, no rio Uruguai, entre Concordia (Argentina) e Salto (Uruguai). A pesca esportiva regulada ocupa a área que vai de uma linha imaginária paralela ao eixo da barragem, a <strong>500 metros</strong> a jusante, até o fim da zona de segurança (Regulamento de Pesca com Devolução da CARU, Resolução 27/2004 e suas reformas, texto atualizado lido em 13/11/2026). O limite mudou duas vezes:',
	'que_l' => [
		'<strong>500 m</strong> desde a Resolução CARU 46/16 (22/9/2016).',
		'<strong>200 m</strong> desde a Resolução CARU 01/2023 (5/1/2023; publicada no Boletim Oficial argentino em 7/3/2023).',
		'<strong>500 m de novo</strong> desde a Resolução CARU 63/2026, assinada em 21/9/2026 e publicada no Boletim Oficial em 28/9/2026, a pedido da Comissão Técnica Mista de Salto Grande, que invoca a segurança da barragem, da navegação e das pessoas.',
	],
	'que2' => 'Não publicamos um comprimento total da zona: nenhum texto da CARU que lemos o informa.',
	'h_reg' => 'Quem a regula',
	'reg1' => 'A CARU, a Comissão Administradora do Rio Uruguai (binacional), edita o regulamento. A resolução que aprovou o regulamento deixa a cada Estado o que se refere a licenças de pesca, adjudicações e fiscalização, e cada Estado pode habilitar <strong>um único permissionário</strong> (art. 4.2 a). As autoridades de aplicação e controle são, na Argentina, a Dirección de Recursos Naturales de Entre Ríos (hoje Dirección de Recursos Naturales y Fiscalización) e, no Uruguai, a Dirección Nacional de Recursos Acuáticos (DINARA), junto com as Prefeituras Navais dos dois países (art. 2.1).',
	'h_reglas' => 'As regras do regulamento',
	'reglas_i' => 'São os artigos do regulamento da CARU, no texto atualizado:',
	'reglas' => [
		'<strong>Só dourado e só com devolução</strong> (arts. 5.1 e 10.3). Os torneios de pesca, que a autoridade autoriza, são a exceção quanto à espécie.',
		'<strong>Só em embarcações autorizadas:</strong> não se pode pescar da margem nem desembarcar, salvo emergência (art. 3.1).',
		'<strong>Isca artificial ou mosca</strong> (fly casting, spinning e bait casting), com <strong>anzol sem farpa</strong> ou com a farpa amassada ou preenchida; <strong>sem isca natural</strong> e <strong>sem trolling</strong> (arts. 7.1 e 8.1). Um só equipamento por pescador (art. 9.1).',
		'<strong>No máximo dois minutos fora da água</strong> para cada peixe (art. 10.3).',
		'<strong>Até quatro embarcações ao mesmo tempo</strong>, não mais de duas por país em cada turno; até quatro pessoas a bordo, guia e timoneiro incluídos; embarcações de 4,50 a 6,50 m de comprimento e 15 km/h no máximo (art. 11).',
		'<strong>De segunda a quinta, só com luz do dia</strong>, desde 1.º de junho de 2023 (Resolução CARU 37/2023, Boletim Oficial de 11/4/2023). Os textos anteriores (o Digesto da CARU de 2018 e uma licitação uruguaia de 2020, entre eles) dizem de sexta a segunda.',
		'<strong>Clientes:</strong> contratam com o permissionário e levam os documentos que comprovem esse vínculo e sua identidade; o permissionário comunica à Prefeitura Naval a filiação dos clientes com pelo menos 48 horas de antecedência (art. 4.2 b e c).',
	],
	'h_veda' => 'O defeso do dourado: como se aplica aqui',
	'veda1' => 'A CARU fixa o defeso do dourado no rio Uruguai de <strong>1.º de setembro a 31 de dezembro de cada ano</strong>, para a pesca comercial e esportiva (Resolução CARU 111/2023, de 1/8/2023; aviso da CARU de 7/10/2026). O regulamento de La Zona diz que sua temporada «se ajustará ao que a CARU estabeleça no rio Uruguai» (tradução nossa). Não encontramos texto que isente a zona do defeso, nem um que confirme que vale lá dentro: fica como pergunta aberta (abaixo). O site de uma agência que vende viagens ao La Zona Lodge lista setembro a novembro como «mid-season» e chama La Zona de pescaria de o ano todo; pergunte ao operador, por escrito, como o defeso se aplica às suas datas. A página de turismo da Prefeitura de Concordia dá outras datas (de 16 de outubro a 14 de janeiro, com devolução); a norma é a resolução da CARU, não essa página.',
	'h_quien' => 'Quem pode levar você: o que não está resolvido',
	'quien1' => 'Para pescar em La Zona é preciso contratar com o permissionário de um dos dois países. <strong>Lado argentino:</strong> a licitação de 2023 foi declarada fracassada pelo Decreto 3874/25 de Entre Ríos (20/12/2025; Boletim Oficial de 28/1/2026), porque a Resolução CARU 207/2023 proibiu a marcação de peixes e isso mudou as condições do chamamento. Depois consta um novo edital, o Concurso Público de Méritos y Antecedentes N° 01-2026 (licença de dez anos, até duas embarcações, pesca de segunda a quinta). Em 13/11/2026 não encontramos adjudicação publicada.',
	'quien2' => '<strong>O que o mercado oferece.</strong> O La Zona Lodge (Concordia) oferece, segundo o próprio site, pesca em lanchas abaixo da represa de Salto Grande. Do lado uruguaio, a River Plate Dorados diz no próprio site que em 2013 adquiriu em leilão público o direito exclusivo de usar as duas únicas embarcações; que fica do lado uruguaio, diz o site de uma agência. Não conseguimos verificar com um texto oficial qual licença cobre cada um em 2026, e não dizemos que algum deles esteja sem licença. Do lado uruguaio houve também uma licitação em 2020 (Resolução 795/2020 do Ministério da Pecuária, Agricultura e Pesca, 12/8/2020) para um permissionário por cinco anos; não lemos o resultado.',
	'quien3' => 'Preço: sob consulta. Não publicamos tarifas de agências. Se quiser comparar, {viaje}.',
	'viaje_t' => 'peça opções para pescar dourado e perguntamos aos operadores que se encaixam',
	'h_pre' => 'O que perguntar antes de pagar',
	'pre' => [
		'Que licença cobre a viagem nas suas datas? Peça o número da resolução ou do decreto.',
		'Em que dias você vai pescar? O regulamento diz de segunda a quinta.',
		'Quantos pescadores por embarcação? A bordo cabem quatro pessoas, guia e timoneiro incluídos.',
		'Como o defeso de 1.º de setembro a 31 de dezembro se aplica às suas datas? Peça por escrito.',
		'O permissionário vai comunicar seus dados à Prefeitura Naval com 48 horas de antecedência?',
	],
	'h_rio' => 'Como chegar e como está o rio',
	'rio1' => 'Concordia fica a cerca de 430 km de Buenos Aires, segundo El Cronista (matéria lida em 13/11/2026), pouco mais de cinco horas por estrada. As descargas da represa mudam o rio. A Comissão Técnica Mista de Salto Grande publica um {ctm}. Exemplo, leitura das 09:00 de 13/11/2026: nível em Concordia 8,57 m; vazão total de Salto Grande, média diária, 11.787 m³/s. É o dado daquele dia, não uma previsão. Ainda não temos uma página de altura do rio para Concordia.',
	'ctm_t' => 'relatório diário de vazões e níveis (PDF)',
	'h_rec' => 'O recorde',
	'rec1' => 'O recorde mundial todo equipamento do dourado na IGFA é de 25,28 kg; a página da IGFA mostra só o peso. Segundo a imprensa (Diario Uno, consultado em 24/10/2026), foi pescado no rio Uruguai em 2006. Mais em {esp} e em {pilar}.',
	'esp_t' => 'a ficha do dourado', 'pilar_t' => 'o nosso guia de pesca do dourado na Argentina',
	'h_falta' => 'O que ainda não sabemos',
	'falta' => [
		'Quem é o permissionário do lado argentino, e se o concurso N° 01-2026 já tem adjudicação.',
		'Se o defeso de 1.º de setembro a 31 de dezembro vale dentro de La Zona.',
		'As taxas de licença, tamanhos e cotas de Entre Ríos para o rio Uruguai: não as lemos em página oficial.',
		'As tarifas que os próprios operadores publicam.',
		'Se a licença uruguaia da licitação de 2020 segue em vigor.',
		'O comprimento total da zona: nenhum texto da CARU que lemos o informa (o site de uma agência fala em 1.000 metros).',
	],
	'faq_h' => 'Perguntas frequentes',
	'faq' => [
		[ 'O que é La Zona na Argentina?', 'É como os pescadores chamam a Zona de Segurança a jusante da Represa de Salto Grande, no rio Uruguai, entre Concordia (Argentina) e Salto (Uruguai). O regulamento da CARU permite pescar dourado com devolução a partir de uma linha a 500 m da barragem até o fim dessa zona (Resolução 27/2004 e reformas; os 500 m valem desde a Resolução 63/2026).' ],
		[ 'Qualquer pessoa pode pescar em La Zona?', 'Não. Pesca-se só em embarcações autorizadas e contratando com o permissionário que cada país habilita, um por Estado; não se pode pescar da margem. O cliente leva os documentos que comprovem seu vínculo com o permissionário e sua identidade, e o permissionário avisa a Prefeitura Naval com pelo menos 48 horas. Em 13/11/2026 não encontramos publicado quem tem a licença do lado argentino.' ],
		[ 'Em que dias se pode pescar em La Zona?', 'De segunda a quinta e só com luz do dia, desde 1.º de junho de 2023 (Resolução CARU 37/2023). Os textos anteriores, como o Digesto da CARU de 2018, dizem de sexta a segunda.' ],
		[ 'Quando é o defeso do dourado no rio Uruguai?', 'De 1.º de setembro a 31 de dezembro de cada ano, para a pesca comercial e esportiva (Resolução CARU 111/2023; aviso da CARU de 7/10/2026). Não encontramos texto que isente La Zona, e o regulamento diz que sua temporada se ajusta ao que a CARU estabelece: pergunte ao operador, por escrito, como se aplica às suas datas.' ],
	],
	'f' => [
		'reg' => 'CARU, Resolução 27/2004: Regulamento de Pesca com Devolução na Zona de Segurança a jusante da Represa de Salto Grande, texto atualizado (Argentina.gob.ar)',
		'r63' => 'CARU, Resolução 63/2026 (21/9/2026; Boletim Oficial 28/9/2026): limite de 500 m',
		'a63' => 'CARU: aviso de 22/9/2026 sobre a Resolução 63/26',
		'r01' => 'CARU, Resolução 01/2023 (5/1/2023), Boletim Oficial argentino de 7/3/2023: limite de 200 m',
		'old' => 'Infoleg: reforma da Resolução CARU 27/2004 que fixou a pesca de sexta a segunda (texto anterior, para comparar)',
		'r111' => 'CARU, Resolução 111/2023 (1/8/2023): defeso do dourado, de 1.º de setembro a 31 de dezembro',
		'a111' => 'CARU: aviso «Veda do dourado 2026» (7/10/2026)',
		'bo' => 'Boletim Oficial de Entre Ríos de 28/1/2026: Decreto 3874/25 (20/12/2025), concurso declarado fracassado',
		'pliego' => 'Entre Ríos, Dirección de Recursos Naturales y Fiscalización: edital do Concurso Público de Méritos y Antecedentes N° 01-2026',
		'libre' => 'Libreentrerios (17/3/2026): situação do novo concurso (imprensa)',
		'impo' => 'Uruguai, Ministério da Pecuária, Agricultura e Pesca: Resolução 795/2020 (12/8/2020), licitação de um permissionário',
		'ctm' => 'Comissão Técnica Mista de Salto Grande: relatório de vazões e níveis de 13/11/2026, 09:00',
		'igfa' => 'IGFA: base de dados de peixes de pesca esportiva, dourado (recorde todo equipamento de 25,28 kg, só o peso)',
		'uno' => 'Diario Uno: o maior dourado do mundo (recorde, rio Uruguai, 2006; imprensa)',
		'muni' => 'Prefeitura de Concordia: pesca esportiva (datas de defeso diferentes das da CARU)',
		'lodge' => 'La Zona Fishing Lodge, site próprio (operador)',
		'rp' => 'River Plate Dorados, site próprio (operador)',
		'ag' => 'Site de uma agência que vende viagens ao La Zona Lodge (temporadas e lado uruguaio)',
		'cron' => 'El Cronista: o rio onde se pescam os melhores dourados (distância de Buenos Aires)',
	],
];
$oficial = [ 'reg' => 1, 'r63' => 1, 'a63' => 1, 'r01' => 1, 'old' => 1, 'r111' => 1, 'a111' => 1, 'bo' => 1, 'pliego' => 1, 'impo' => 1, 'ctm' => 1, 'igfa' => 0, 'muni' => 1 ];

// The pillar (golden-dorado-guia): cover photo and the pointer that replaces "we have not read the rules".
$pil_es = pm_buscar( 'pm_nota', 'golden-dorado-guia', 'es' );
$att    = $pil_es ? (int) get_post_thumbnail_id( $pil_es->ID ) : 0;
$att || WP_CLI::warning( 'la guía pilar (es) no tiene foto de portada: la nota nueva tampoco la tendrá.' );

$existe = fn( string $i ) => array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i );
$ids = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n = $N[ $i ];
	if ( $ya = $existe( $i ) ) {
		$ids[ $i ] = array_values( $ya )[0]->ID;
		WP_CLI::log( "$clave ($i) #{$ids[$i]} ya existe: no toco nada." );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	$esp   = pm_buscar( 'pm_especie', 'dorado', $i ) ?? pm_buscar( 'pm_especie', 'dorado', 'es' );
	$pilar = pm_buscar( 'pm_nota', 'golden-dorado-guia', $i ) ?? $pil_es;
	if ( ! $esp || ! $pilar ) {
		WP_CLI::error( "$i: falta la ficha del dorado o la guía pilar." );
	}
	$rep = [
		'{viaje}' => pm_sc_a( pm_url_viaje( $i ) . '?especie=dorado', $n['viaje_t'] ),
		'{ctm}'   => pm_sc_a( $u['ctm'], $n['ctm_t'] ),
		'{esp}'   => pm_sc_a( get_permalink( $esp->ID ), $n['esp_t'] ),
		'{pilar}' => pm_sc_a( get_permalink( $pilar->ID ), $n['pilar_t'] ),
	];
	$cuerpo = pm_sc_p( $n['p0'] )
		. pm_sc_h2( $n['h_que'] ) . pm_sc_p( $n['que1'] ) . pm_sc_lista( $n['que_l'] ) . pm_sc_p( $n['que2'] )
		. pm_sc_h2( $n['h_reg'] ) . pm_sc_p( $n['reg1'] )
		. pm_sc_h2( $n['h_reglas'] ) . pm_sc_p( $n['reglas_i'] ) . pm_sc_lista( $n['reglas'] )
		. pm_sc_h2( $n['h_veda'] ) . pm_sc_p( $n['veda1'] )
		. pm_sc_h2( $n['h_quien'] ) . pm_sc_p( $n['quien1'] ) . pm_sc_p( $n['quien2'] ) . pm_sc_p( $n['quien3'] )
		. pm_sc_h2( $n['h_pre'] ) . pm_sc_lista( $n['pre'] )
		. pm_sc_h2( $n['h_rio'] ) . pm_sc_p( $n['rio1'] )
		. pm_sc_h2( $n['h_rec'] ) . pm_sc_p( $n['rec1'] )
		. pm_sc_h2( $n['h_falta'] ) . pm_sc_lista( $n['falta'] )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] );
	$cuerpo = strtr( $cuerpo, $rep );
	! preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	$fuentes = [];
	foreach ( $n['f'] as $k => $nombre ) {
		$fuentes[] = [ 'nombre' => $nombre, 'url' => $u[ $k ], 'oficial' => $oficial[ $k ] ?? 0, 'consultada' => $hoy ];
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . " fuentes, 4 FAQ; title $tl / description $dl; portada #$att" );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ), 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_especie' => 'dorado', 'nota_origen' => 'La Zona (Salto Grande), 13/11/2026: hechos leídos en los textos de la CARU (Res. 27/2004 actualizada, 63/2026, 111/2023, 01/2023), el Boletín Oficial de Entre Ríos (Decreto 3874/25), el pliego 01-2026, el reporte de la CTM de Salto Grande y los sitios propios de los operadores. Sin tarifas de agencias, sin nombres de personas, sin afirmar quién tiene el permiso argentino. Pendiente: preguntar a la Dirección de Recursos Naturales y Fiscalización de Entre Ríos y a la CARU. Docs: docs/investigacion/la-zona-salto-grande-2026-11-13.md.' ];
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
	$ok   = get_post( $id )->post_name === $n['slug'] && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( $cont, 'pm-faq' ) && ( false !== strpos( $cont, '8,57' ) || false !== strpos( $cont, '8.57' ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
	$ids[ $i ] = $id;
}
if ( $aplicar ) {
	$nota_es = pm_buscar( 'pm_nota', $clave, 'es' );
	$e = $nota_es ? pm_nota( $nota_es->ID ) : null;
	WP_CLI::log( ( $e && 'dorado' === $e['especie'] && $e['fecha'] && ( ! $att || $e['foto'] === $att ) ? 'VERIFIED' : 'FALLÓ' ) . ': nota es lee especie ' . ( $e['especie'] ?? '?' ) . ', fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
	flush_rewrite_rules( false );
}

// --- Pillar note: replace "we have not read the rules..." with a pointer to the new note (keeps what is unread honest) ---
$ptr = [
	'es' => [
		'viejo' => 'Tampoco leímos todavía el reglamento, la licencia ni los operadores del río Uruguay, así que no publicamos nada sobre ellos.',
		'nuevo' => 'Del río Uruguay leímos el reglamento de pesca de La Zona, la veda del dorado y los llamados a permisionario: está en %s. Todavía no leímos las tasas de licencia de Entre Ríos para ese río ni las tarifas propias de los operadores, así que no publicamos nada sobre ellos.',
	],
	'en' => [
		'viejo' => 'We have not yet read the rules, the license or the operators for the Uruguay River either, so we publish nothing on them.',
		'nuevo' => 'For the Uruguay River we have read the La Zona fishing rules, the golden dorado closed season and the permit calls: they are in %s. We have not yet read the Entre Ríos licence fees for that river or the operators\' own rates, so we publish nothing on them.',
	],
	'pt' => [
		'viejo' => 'Também ainda não lemos o regulamento, a licença nem os operadores do rio Uruguai, então não publicamos nada sobre eles.',
		'nuevo' => 'Do rio Uruguai lemos o regulamento de pesca de La Zona, o defeso do dourado e as licitações de permissionário: estão em %s. Ainda não lemos as taxas de licença de Entre Ríos para esse rio nem as tarifas próprias dos operadores, então não publicamos nada sobre eles.',
	],
];
foreach ( $ptr as $i => $x ) {
	$p = pm_buscar( 'pm_nota', 'golden-dorado-guia', $i );
	if ( ! $p ) {
		WP_CLI::warning( "pilar dorado ($i): no existe." );
		continue;
	}
	$c = $p->post_content;
	$nueva = pm_buscar( 'pm_nota', $clave, $i );
	$nuevo_txt = $nueva ? sprintf( $x['nuevo'], pm_sc_a( get_permalink( $nueva->ID ), esc_html( $N[ $i ]['titulo'] ) ) ) : '';
	if ( $nueva && false !== strpos( $c, get_permalink( $nueva->ID ) ) ) {
		WP_CLI::log( "pilar dorado ($i) #{$p->ID}: ya enlaza la nota de La Zona." );
		continue;
	}
	if ( false === strpos( $c, $x['viejo'] ) ) {
		WP_CLI::warning( "pilar dorado ($i) #{$p->ID}: no encontré la línea «no leímos el reglamento»; no enlazo." );
		continue;
	}
	if ( ! $nueva ) {
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": pilar dorado ($i) #{$p->ID}: se reemplazaría la línea «no leímos…» por el enlace a la nota (la nota todavía no existe en este simulacro)." );
		$cambios++;
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": pilar dorado ($i) #{$p->ID}: la línea «no leímos…» pasa a enlazar la nota de La Zona" );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, str_replace( $x['viejo'], $nuevo_txt, $c ), [ get_permalink( $nueva->ID ) ], "pilar dorado ($i)" );
		WP_CLI::log( "VERIFIED: pilar dorado ($i) #{$p->ID}" );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
