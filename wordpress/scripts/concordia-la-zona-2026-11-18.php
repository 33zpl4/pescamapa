<?php
/**
 * Concordia (Entre Ríos), 18/11/2026: NEW short destination guide "Pesca en Concordia y La Zona" (es, en, pt; clave concordia, the same as the
 * "próximamente" card, which hides itself and lends its photo) + ONE public-information card (La Zona Lodge) + links from the La Zona note and the dorado pillar.
 * Decision A (docs/investigacion/la-zona-lugares-2026-11-17.md): notes have no card mechanism (only destination guides and city pages read pm_prestador), so the
 * only existing way to show a card is a guide. Every claim of the guide comes from what the La Zona note already publishes (CARU texts, Entre Ríos Boletín
 * Oficial, El Cronista, read 13/11/2026) plus the lodge's own post as the owner read it on 18/11/2026 (Instagram del lugar, 15/10/2026): attributed, as TEXT
 * (no link to the site or to Instagram, no phone, no email, no photo, no price). NOT said: records, sizes, number of boats, reopening, "the best", season,
 * guides, which permit covers the fishing. The Entre Ríos licence for the Uruguay river was not read in an official page: the guide says so.
 * Edits to existing notes (each only when it still holds the text published on 13/11/2026; otherwise «falta» and nothing is written for that note):
 *  - La Zona note (es, en, pt): one paragraph after "what the market offers" linking the guide;
 *  - dorado pillar (es, en, pt): "Concordia ... todavía no tiene guía" becomes a link to the guide.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = concordia-la-zona-2026-11-18; aplicar=true for the real run).
 */

$aplicar = false;
$hoy     = '20261118'; // date of the guide text and of the owner's reading of the lodge post
$leido   = '20261113'; // date the CARU texts, the Boletín Oficial and the press were read (note la-zona-salto-grande)
$clave   = 'concordia';

function pm_cg_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_cg_h( string $texto, int $nivel = 2 ): string {
	$attr = 2 === $nivel ? '' : ' {"level":' . $nivel . '}';
	return "<!-- wp:heading{$attr} -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->";
}
function pm_cg_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_cg_tabla( array $cabecera, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
function pm_cg_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}
/** Blocks as the data file writes them: ["p", html] ["h2", text] ["h3", text] ["ul", [items]] ["table", [head], [rows]] ["pend", text]. */
function pm_cg_bloques( array $bloques ): string {
	$out = [];
	foreach ( $bloques as $b ) {
		switch ( $b[0] ) {
			case 'p':
				$out[] = pm_cg_p( $b[1] );
				break;
			case 'h2':
				$out[] = pm_cg_h( $b[1], 2 );
				break;
			case 'h3':
				$out[] = pm_cg_h( $b[1], 3 );
				break;
			case 'ul':
				$out[] = pm_cg_lista( $b[1] );
				break;
			case 'table':
				$out[] = pm_cg_tabla( $b[1], $b[2] );
				break;
			case 'pend':
				$out[] = pm_cg_pendiente( $b[1] );
				break;
			default:
				WP_CLI::error( 'bloque desconocido: ' . $b[0] );
		}
	}
	return implode( "\n\n", $out );
}
/** {n:clave} -> URL of that note in the language (Spanish if it has no translation); {viaje} and {especie}. Fails on anything left over. */
function pm_cg_resolver( string $html, string $i, string $clave_destino ): string {
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\}/', function ( $m ) use ( $i ) {
		$ids = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $m[1], 'numberposts' => -1, 'fields' => 'ids' ] );
		$por = [];
		foreach ( $ids as $id ) {
			$por[ pm_idioma_de( (int) $id ) ] = (int) $id;
		}
		if ( ! isset( $por[ $i ] ) ) {
			WP_CLI::warning( "nota {$m[1]} no existe en {$i}: el enlace va a la versión en español" );
		}
		$id = $por[ $i ] ?? ( $por['es'] ?? 0 );
		if ( ! $id ) {
			WP_CLI::error( "No encuentro la nota {$m[1]} (ni en español)." );
		}
		return esc_url( get_permalink( $id ) );
	}, $html );
	$html = str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) . '?destino=' . $clave_destino . '&especie=dorado' ), $html );
	$html = str_replace( '{especie}', esc_url( pm_url_especie( 'dorado', $i ) ), $html );
	if ( preg_match( '/\{(n:[a-z0-9-]*|viaje|especie)\}/', $html, $m ) ) {
		WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
	}
	return $html;
}
function pm_cg_resolver_todo( $v, string $i, string $clave ) {
	if ( is_string( $v ) ) {
		return pm_cg_resolver( $v, $i, $clave );
	}
	if ( is_array( $v ) ) {
		return array_map( fn( $x ) => pm_cg_resolver_todo( $x, $i, $clave ), $v );
	}
	return $v;
}

/** The guide of this clave in this language, by direct query (pm_buscar caches the list and would not see a post created in this run). */
function pm_cg_guia( string $clave, string $i ): ?WP_Post {
	$ps = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	foreach ( $ps as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}
function pm_cg_ver( $v ): string {
	return is_array( $v ) ? implode( ',', array_map( fn( $x ) => is_array( $x ) ? wp_json_encode( $x, JSON_UNESCAPED_UNICODE ) : (string) $x, $v ) ) : (string) $v;
}
function pm_cg_norm( string $k, $v ): string {
	if ( in_array( $k, [ 'precio_fecha', 'verificado', 'actualizado' ], true ) ) {
		return preg_replace( '/\D/', '', (string) $v );
	}
	if ( in_array( $k, [ 'prest_consentimiento', 'prest_publico', 'datos_ejemplo', 'capacidad' ], true ) ) {
		return (string) (int) $v;
	}
	if ( in_array( $k, [ 'lat', 'lon' ], true ) ) {
		return (string) round( (float) $v, 4 );
	}
	return is_array( $v ) ? implode( ',', $v ) : trim( (string) $v );
}
/** Repeater rows reduced to the keys we wrote, dates as digits, oficial as 0/1: comparable with what was sent. */
function pm_cg_filas( string $campo, int $id, array $claves ): array {
	return array_map( function ( $f ) use ( $claves ) {
		$o = [];
		foreach ( $claves as $c ) {
			$v = $f[ $c ] ?? '';
			$o[ $c ] = 'consultada' === $c ? preg_replace( '/\D/', '', (string) $v ) : ( 'oficial' === $c ? (int) ! empty( $v ) : ( in_array( $c, [ 'lat', 'lon' ], true ) ? ( '' === (string) $v ? '' : (string) round( (float) $v, 4 ) ) : (string) $v ) );
		}
		return $o;
	}, pm_filas( $campo, $id ) );
}
function pm_cg_filas_envio( array $filas, array $claves ): array {
	return array_map( function ( $f ) use ( $claves ) {
		$o = [];
		foreach ( $claves as $c ) {
			$v = $f[ $c ] ?? '';
			$o[ $c ] = 'consultada' === $c ? preg_replace( '/\D/', '', (string) $v ) : ( 'oficial' === $c ? (int) ! empty( $v ) : ( in_array( $c, [ 'lat', 'lon' ], true ) ? ( '' === (string) $v ? '' : (string) round( (float) $v, 4 ) ) : (string) $v ) );
		}
		return $o;
	}, $filas );
}
const PM_CG_REPETIDORES = [
	'fuentes'   => [ 'nombre', 'url', 'oficial', 'consultada' ],
	'temporada' => [ 'meses', 'que' ],
	'aguas'     => [ 'nombre', 'nota', 'lat', 'lon' ],
	'faqs'      => [ 'pregunta', 'respuesta' ],
];
function pm_cg_ficha_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}

// ---- data ---------------------------------------------------------------------------------------------------------------------------------
$u = [
	'reg'    => 'https://www.argentina.gob.ar/normativa/nacional/norma-102959/actualizacion',
	'r63'    => 'https://caru.org.uy/nuevositio/wp-content/uploads/2026/09/RESOLUCION-N%C2%B0-63-26_Modifica-Reglamento-de-Pesca_Area-pesca-deportiva.pdf',
	'r111'   => 'https://www.caru.org.uy/web/wp-content/uploads/2023/08/RESOLUCION-N-111-23-Veda-del-Dorado.pdf',
	'a111'   => 'https://caru.org.uy/nuevositio/2026/10/07/veda-del-dorado-2026-1-09-31-12/',
	'bo'     => 'https://www.entrerios.gov.ar/boletin/calendario/Boletin/2026/Enero/28-01-26.pdf',
	'pliego' => 'https://www.entrerios.gov.ar/boletin/calendario/Boletin/anexos/2026/anexo_3450.pdf',
	'libre'  => 'https://libreentrerios.com/pesca-del-dorado-en-salto-grande-relanzan-concurso-fracasado',
	'cron'   => 'https://www.cronista.com/clase/break/el-extraordinario-rio-donde-se-pescan-los-mejores-dorados-esta-cerca-de-caba-y-tiene-pique-todo-el-ano',
];
$oficial = [ 'reg' => 1, 'r63' => 1, 'r111' => 1, 'a111' => 1, 'bo' => 1, 'pliego' => 1, 'libre' => 0, 'cron' => 0 ];

$G = [];
$G['es'] = [
	'titulo' => 'Concordia', 'slug' => 'pesca-en-concordia',
	'cuerpo' => [
		[ 'p', '<strong>Concordia</strong> está en Entre Ríos, sobre el <strong>río Uruguay</strong>, bajo la represa de Salto Grande. Aguas abajo de la presa está <strong>La Zona</strong>, la Zona de Seguridad donde la CARU regula la pesca del dorado con devolución. Esta guía es corta a propósito: reúne lo que ya publicamos en <a href="{n:la-zona-salto-grande}">nuestra nota sobre La Zona</a>, leído en los textos de la CARU y del Boletín Oficial de Entre Ríos, y la ficha de un lugar de Concordia, con su fecha. Lo que no pudimos confirmar, lo decimos.' ],
		[ 'h2', 'Datos clave' ],
		[ 'table', [ 'Dato', 'Qué dice', 'Fuente' ], [
			[ 'Río y zona', 'Río Uruguay, bajo la represa de Salto Grande; La Zona es la Zona de Seguridad aguas abajo de la presa', 'CARU, Resolución 27/2004 (texto actualizado)' ],
			[ 'Qué se pesca en La Zona', 'Solo dorado, con devolución, desde embarcaciones autorizadas, de lunes a jueves y de día', 'CARU (días desde el 1/6/2023)' ],
			[ 'Dónde empieza', 'A 500 m de la presa desde la Resolución 63/2026 (Boletín Oficial del 28/9/2026)', 'CARU' ],
			[ 'Veda del dorado en el río Uruguay', 'Del 1 de septiembre al 31 de diciembre; no encontramos un texto que exceptúe La Zona', 'CARU, Resolución 111/2023' ],
			[ 'Permiso del lado argentino', 'Al 13/11/2026 no encontramos una adjudicación publicada', 'Entre Ríos, Decreto 3874/25 y pliego 01-2026' ],
			[ 'Licencia de pesca de Entre Ríos para el río Uruguay', 'No la leímos en una página oficial: no la podemos confirmar', 'Pendiente' ],
			[ 'Lugar listado', 'La Zona Lodge, según su propia publicación (ficha abajo)', 'Publicación del lugar, octubre de 2026' ],
			[ 'Tarifa y temporada', 'A consultar: la publicación no las trae', 'Publicación del lugar, octubre de 2026' ],
		] ],
		[ 'h2', 'Las reglas de La Zona, en corto' ],
		[ 'ul', [
			'<strong>Solo dorado y solo con devolución</strong>, con un máximo de dos minutos fuera del agua por pez.',
			'<strong>Solo desde embarcaciones autorizadas</strong>: no se pesca desde la costa ni se desembarca, salvo emergencia.',
			'<strong>Señuelo artificial o mosca</strong>, con anzuelo sin rebaba o con la rebaba aplastada o rellenada; sin carnada y sin trolling.',
			'<strong>De lunes a jueves y solo de día</strong>, desde el 1 de junio de 2023 (Resolución CARU 37/2023).',
			'<strong>Hasta cuatro embarcaciones a la vez</strong>, con hasta cuatro personas a bordo, guía y timonel incluidos.',
		] ],
		[ 'p', 'Los artículos y las resoluciones, uno por uno, están en <a href="{n:la-zona-salto-grande}">la nota sobre La Zona</a>.' ],
		[ 'h2', 'La veda del dorado' ],
		[ 'p', 'La CARU fija la veda del dorado en el río Uruguay del <strong>1 de septiembre al 31 de diciembre de cada año</strong>, para la pesca comercial y deportiva (Resolución CARU 111/2023; aviso de la CARU del 7/10/2026). El reglamento de La Zona dice que su temporada se ajusta a lo que establece la CARU para el río. No encontramos ningún texto que exceptúe La Zona de la veda, ni uno que confirme que rige adentro: preguntale al lugar, por escrito, cómo se aplica a tus fechas. La página de turismo de la Municipalidad de Concordia da otras fechas; la norma es la resolución de la CARU.' ],
		[ 'h2', 'Quién puede llevarte' ],
		[ 'p', 'Para pescar en La Zona hay que contratar con el permisionario de uno de los dos países. Del lado argentino, el llamado de 2023 fue declarado fracasado por el Decreto 3874/25 de Entre Ríos (Boletín Oficial del 28/1/2026) y hay un nuevo pliego, el Concurso Público de Méritos y Antecedentes N° 01-2026. <strong>Al 13/11/2026 no encontramos una adjudicación publicada.</strong>' ],
		[ 'p', '<strong>La Zona Lodge</strong> figura abajo como listado con información pública, todavía sin verificar con el lugar. Según una publicación del propio lugar (Instagram del lugar, 15/10/2026, que leímos el 18/11/2026), está en Concordia, en la «exclusiva reserva de Salto Grande», a orillas del río Uruguay, con régimen <em>all inclusive</em>: alojamiento, comidas, bebidas, jornadas de pesca e infraestructura de la estadía, y traslado diario de la posada al río. La misma publicación dice que es una región «famosa por los grandes dorados» y menciona dorado, surubí, tararira, patí, armado, pacú y boga: es lo que dice el lugar, no una garantía de captura.' ],
		[ 'p', 'Esa publicación no dice qué permiso cubre la pesca en La Zona, y tampoco trae precio, temporada, embarcaciones ni guías: queda como pregunta abierta. No decimos que al lugar le falte ningún permiso.' ],
		[ 'h2', 'Cómo llegar' ],
		[ 'p', 'Concordia está a unos 430 km de Buenos Aires, según El Cronista (nota leída el 13/11/2026). Las descargas de la represa cambian el río: en <a href="{n:la-zona-salto-grande}">la nota sobre La Zona</a> está el reporte de caudales y niveles de la Comisión Técnica Mixta de Salto Grande.' ],
		[ 'h2', 'Cuánto cuesta' ],
		[ 'p', 'La publicación del lugar no trae tarifa y no publicamos tarifas de agencias: <strong>a consultar</strong>. Para tener una referencia de los lodges de dorado en general, mirá <a href="{n:precio-viaje-dorado-lodge}">cuánto cuesta un viaje de pesca de dorado</a>.' ],
		[ 'h2', 'Para leer antes de ir' ],
		[ 'ul', [
			'<a href="{n:la-zona-salto-grande}">La Zona (Salto Grande): reglas del dorado y acceso</a>.',
			'<a href="{especie}">El dorado</a>: la especie, sus tamaños y dónde vive.',
			'<a href="{n:cuando-pescar-dorado}">Cuándo pescar dorado</a>: las ventanas y por qué las fuentes no coinciden.',
		] ],
		[ 'p', '<strong>Contanos qué buscás y te armamos la mejor opción para vos.</strong> Decinos fechas, cuántos van y qué querés pescar en <a href="{viaje}">el formulario del viaje</a>: sin costo y sin compromiso.' ],
		[ 'pend', 'permiso del lado argentino (Concurso N° 01-2026) y si la veda rige dentro de La Zona (preguntas a Recursos Naturales y Fiscalización de Entre Ríos y a la CARU); licencia, tallas y cupos de Entre Ríos para el río Uruguay; tarifa, temporada, embarcaciones y guías de La Zona Lodge (la publicación no los trae); consentimiento del lugar; foto con su crédito.' ],
	],
	'campos' => [
		'h1' => 'Pesca de dorado en Concordia y La Zona: reglas, veda y alojamiento',
		'bajada' => 'Concordia, sobre el río Uruguay y bajo la represa de Salto Grande: qué dice la CARU de La Zona, la veda del dorado, quién puede llevarte y un alojamiento listado con su fecha.',
		'modalidades' => 'Embarcada, con devolución (reglamento de La Zona)',
		'epoca' => 'Veda del dorado del 1 de septiembre al 31 de diciembre en el río Uruguay (CARU). En La Zona se pesca de lunes a jueves y de día.',
		'seo_title' => 'Pesca en Concordia y La Zona: reglas, veda y alojamiento',
		'seo_description' => 'Concordia y La Zona, bajo la represa de Salto Grande: reglas de la CARU para el dorado, veda del 1/9 al 31/12, quién puede llevarte y un alojamiento listado.',
		'reglamento_resumen' => 'La Zona (Zona de Seguridad aguas abajo de la represa de Salto Grande): reglamento de la CARU (Resolución 27/2004 y reformas; texto leído el 13/11/2026). Solo dorado, con devolución, desde embarcaciones autorizadas, de lunes a jueves y de día; la pesca empieza a 500 m de la presa desde la Resolución 63/2026. Veda del dorado en el río Uruguay: 1/9 al 31/12 (Resolución CARU 111/2023); no encontramos un texto que exceptúe La Zona. Licencia, tallas y cupos de Entre Ríos para el río Uruguay: no los leímos en una página oficial, no los podemos confirmar.',
		'temporada' => [
			[ 'meses' => '1 de septiembre – 31 de diciembre', 'que' => 'Veda del dorado en el río Uruguay (CARU, Resolución 111/2023). No encontramos un texto que exceptúe La Zona ni uno que confirme que rige adentro: preguntalo por escrito.' ],
			[ 'meses' => 'Lunes a jueves', 'que' => 'Días de pesca en La Zona, solo de día, desde el 1/6/2023 (Resolución CARU 37/2023).' ],
			[ 'meses' => 'La Zona Lodge', 'que' => 'Su publicación de octubre de 2026 no trae temporada ni fechas: consultalo.' ],
		],
		'aguas' => [
			[ 'nombre' => 'Río Uruguay, bajo la represa de Salto Grande (La Zona)', 'nota' => 'Zona de Seguridad aguas abajo de la presa; la pesca regulada empieza a 500 m (Resolución CARU 63/2026).', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => '¿Qué es La Zona en Concordia?', 'respuesta' => 'Es como llaman los pescadores a la Zona de Seguridad aguas abajo de la represa de Salto Grande, en el río Uruguay, entre Concordia y Salto (Uruguay). El reglamento de la CARU permite pescar dorado con devolución desde una línea a 500 m de la presa hasta el final de esa zona.' ],
			[ 'pregunta' => '¿Puede pescar cualquiera en La Zona?', 'respuesta' => 'No. Se pesca solo desde embarcaciones autorizadas y contratando con el permisionario que habilita cada país; no se puede pescar desde la costa. Al 13/11/2026 no encontramos publicado quién tiene el permiso del lado argentino.' ],
			[ 'pregunta' => '¿Qué días se puede pescar en La Zona?', 'respuesta' => 'De lunes a jueves y solo con luz diurna, desde el 1 de junio de 2023 (Resolución CARU 37/2023).' ],
			[ 'pregunta' => '¿Cuándo es la veda del dorado en el río Uruguay?', 'respuesta' => 'Del 1 de septiembre al 31 de diciembre de cada año, para la pesca comercial y deportiva (Resolución CARU 111/2023). No encontramos un texto que exceptúe La Zona: preguntale al lugar, por escrito, cómo se aplica a tus fechas.' ],
			[ 'pregunta' => '¿Puedo quedarme con el dorado que pesque en Concordia?', 'respuesta' => 'En La Zona, no: solo se pesca con devolución. Fuera de La Zona no leímos las reglas de Entre Ríos para el río Uruguay: no lo podemos confirmar.' ],
			[ 'pregunta' => '¿Hace falta licencia de pesca en Concordia?', 'respuesta' => 'No pudimos confirmarlo: no leímos las tasas, tallas y cupos de Entre Ríos para el río Uruguay en una página oficial. Consultalo con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos o con tu guía antes de salir.' ],
			[ 'pregunta' => '¿Qué incluye La Zona Lodge según su publicación?', 'respuesta' => 'Según una publicación del propio lugar (Instagram del lugar, 15/10/2026): régimen all inclusive con alojamiento en apartamentos climatizados para grupos, desayuno, almuerzo y cena con bebidas, jornadas de pesca, piscina y traslado diario de la posada al río Uruguay. No trae precio, temporada, embarcaciones ni guías.' ],
			[ 'pregunta' => '¿Cuánto cuesta pescar en La Zona?', 'respuesta' => 'La publicación del lugar no trae tarifa: se consulta. No publicamos tarifas de agencias.' ],
			[ 'pregunta' => '¿Cómo llego a Concordia desde Buenos Aires?', 'respuesta' => 'Concordia está a unos 430 km de Buenos Aires, según El Cronista (nota leída el 13/11/2026).' ],
		],
	],
	'fuentes' => [
		'reg' => 'CARU, Resolución 27/2004: Reglamento de Pesca con Devolución en la Zona de Seguridad de Salto Grande, texto actualizado (Argentina.gob.ar)',
		'r63' => 'CARU, Resolución 63/2026 (Boletín Oficial 28/9/2026): límite de 500 m',
		'r111' => 'CARU, Resolución 111/2023: veda del dorado del 1 de septiembre al 31 de diciembre',
		'a111' => 'CARU: aviso «Veda del dorado 2026» (7/10/2026)',
		'bo' => 'Boletín Oficial de Entre Ríos del 28/1/2026: Decreto 3874/25, concurso declarado fracasado',
		'pliego' => 'Entre Ríos, Recursos Naturales y Fiscalización: pliego del Concurso N° 01-2026',
		'libre' => 'Libreentrerios (17/3/2026): estado del nuevo concurso (prensa)',
		'cron' => 'El Cronista: el río donde se pescan los mejores dorados (distancia desde Buenos Aires)',
	],
	'autoridad' => 'CARU (reglamento de La Zona) y Dirección de Recursos Naturales y Fiscalización de Entre Ríos',
];

$G['en'] = [
	'titulo' => 'Concordia', 'slug' => 'fishing-in-concordia',
	'cuerpo' => [
		[ 'p', '<strong>Concordia</strong> is in Entre Ríos, on the <strong>Uruguay River</strong>, below the Salto Grande dam. Downstream of the dam is <strong>La Zona</strong>, the Security Zone where the CARU regulates catch-and-release dorado fishing. This guide is short on purpose: it gathers what we already published in <a href="{n:la-zona-salto-grande}">our note on La Zona</a>, read in the CARU texts and the Entre Ríos Official Gazette, and the listing of one place in Concordia, with its date. What we could not confirm, we say so.' ],
		[ 'h2', 'Key facts' ],
		[ 'table', [ 'Item', 'What it says', 'Source' ], [
			[ 'River and area', 'Uruguay River, below the Salto Grande dam; La Zona is the Security Zone downstream of the dam', 'CARU, Resolution 27/2004 (updated text)' ],
			[ 'What is fished in La Zona', 'Dorado only, catch and release, from authorised boats, Monday to Thursday and in daylight', 'CARU (days since 1/6/2023)' ],
			[ 'Where it starts', '500 m from the dam since Resolution 63/2026 (Official Gazette of 28/9/2026)', 'CARU' ],
			[ 'Dorado closed season on the Uruguay River', '1 September to 31 December; we found no text that exempts La Zona', 'CARU, Resolution 111/2023' ],
			[ 'Permit on the Argentine side', 'As of 13/11/2026 we found no published award', 'Entre Ríos, Decree 3874/25 and tender 01-2026' ],
			[ 'Entre Ríos fishing licence for the Uruguay River', 'We did not read it on an official page: we cannot confirm it', 'Pending' ],
			[ 'Listed place', 'La Zona Lodge, according to its own post (listing below)', 'The place’s post, October 2026' ],
			[ 'Rate and season', 'On request: the post has neither', 'The place’s post, October 2026' ],
		] ],
		[ 'h2', 'The rules of La Zona, in short' ],
		[ 'ul', [
			'<strong>Dorado only and catch and release only</strong>, with a maximum of two minutes out of the water per fish.',
			'<strong>Only from authorised boats</strong>: no fishing from the bank and no landing, except in an emergency.',
			'<strong>Artificial lure or fly</strong>, with a barbless hook or the barb crushed or filled; no bait and no trolling.',
			'<strong>Monday to Thursday and in daylight only</strong>, since 1 June 2023 (CARU Resolution 37/2023).',
			'<strong>Up to four boats at a time</strong>, with up to four people aboard, guide and boatman included.',
		] ],
		[ 'p', 'The articles and resolutions, one by one, are in <a href="{n:la-zona-salto-grande}">the note on La Zona</a>.' ],
		[ 'h2', 'The dorado closed season' ],
		[ 'p', 'The CARU sets the dorado closed season on the Uruguay River from <strong>1 September to 31 December every year</strong>, for commercial and sport fishing (CARU Resolution 111/2023; CARU notice of 7/10/2026). The La Zona rules say its season follows what the CARU sets for the river. We found no text that exempts La Zona from the closed season, nor one confirming that it applies inside: ask the place, in writing, how it applies to your dates. The tourism page of the Municipality of Concordia gives other dates; the rule is the CARU resolution.' ],
		[ 'h2', 'Who can take you' ],
		[ 'p', 'To fish La Zona you must book with the permit holder of one of the two countries. On the Argentine side, the 2023 call was declared failed by Entre Ríos Decree 3874/25 (Official Gazette of 28/1/2026) and there is a new tender, Public Competition of Merits and Background No. 01-2026. <strong>As of 13/11/2026 we found no published award.</strong>' ],
		[ 'p', '<strong>La Zona Lodge</strong> is listed below as public information, not yet verified with the place. According to a post by the place itself (the place’s Instagram, 15/10/2026, read by us on 18/11/2026), it is in Concordia, in the “exclusive Salto Grande reserve”, on the banks of the Uruguay River, with an <em>all inclusive</em> system: lodging, meals, drinks, fishing days and the facilities of the stay, and a daily transfer from the lodge to the river. The same post says it is a region “famous for big dorado” and mentions dorado, surubí, tararira, patí, armado, pacú and boga: that is what the place says, not a guarantee of a catch.' ],
		[ 'p', 'That post does not say which permit covers the fishing in La Zona, and it gives no price, season, boats or guides: it stays an open question. We do not say the place lacks any permit.' ],
		[ 'h2', 'Getting there' ],
		[ 'p', 'Concordia is about 430 km from Buenos Aires, according to El Cronista (article read on 13/11/2026). The dam’s releases change the river: <a href="{n:la-zona-salto-grande}">the note on La Zona</a> has the flows and levels report of the Salto Grande Joint Technical Commission.' ],
		[ 'h2', 'What it costs' ],
		[ 'p', 'The place’s post gives no rate and we do not publish agency rates: <strong>on request</strong>. For a general reference on dorado lodges, see <a href="{n:precio-viaje-dorado-lodge}">how much a golden dorado fishing trip costs</a>.' ],
		[ 'h2', 'To read before you go' ],
		[ 'ul', [
			'<a href="{n:la-zona-salto-grande}">La Zona (Salto Grande): golden dorado rules and access</a>.',
			'<a href="{especie}">The golden dorado</a>: the species, its sizes and where it lives.',
			'<a href="{n:cuando-pescar-dorado}">When to fish golden dorado</a>: the windows and why the sources differ.',
		] ],
		[ 'p', '<strong>Tell us what you are looking for and we will put together the best option for you.</strong> Give us dates, how many are going and what you want to catch in <a href="{viaje}">the trip form</a>: free and with no commitment.' ],
		[ 'pend', 'permit on the Argentine side (Competition No. 01-2026) and whether the closed season applies inside La Zona; Entre Ríos licence, sizes and limits for the Uruguay River; La Zona Lodge rate, season, boats and guides (not in its post); the place’s consent; photo with credit.' ],
	],
	'campos' => [
		'h1' => 'Golden dorado fishing in Concordia and La Zona: rules, closed season and lodging',
		'bajada' => 'Concordia, on the Uruguay River below the Salto Grande dam: what the CARU says about La Zona, the dorado closed season, who can take you and a lodging listed with its date.',
		'modalidades' => 'Boat fishing, catch and release (La Zona rules)',
		'epoca' => 'Dorado closed season from 1 September to 31 December on the Uruguay River (CARU). In La Zona you fish Monday to Thursday, in daylight.',
		'seo_title' => 'Fishing in Concordia and La Zona: rules and closed season',
		'seo_description' => 'Concordia and La Zona below the Salto Grande dam: CARU rules for golden dorado, closed season 1 Sep to 31 Dec, who can take you and a listed lodging.',
		'reglamento_resumen' => 'La Zona (Security Zone downstream of the Salto Grande dam): CARU rules (Resolution 27/2004 and amendments; text read on 13/11/2026). Dorado only, catch and release, from authorised boats, Monday to Thursday in daylight; fishing starts 500 m from the dam since Resolution 63/2026. Dorado closed season on the Uruguay River: 1/9 to 31/12 (CARU Resolution 111/2023); we found no text that exempts La Zona. Entre Ríos licence, sizes and limits for the Uruguay River: not read on an official page, we cannot confirm them.',
		'temporada' => [
			[ 'meses' => '1 September – 31 December', 'que' => 'Dorado closed season on the Uruguay River (CARU, Resolution 111/2023). We found no text that exempts La Zona nor one confirming that it applies inside: ask in writing.' ],
			[ 'meses' => 'Monday to Thursday', 'que' => 'Fishing days in La Zona, daylight only, since 1/6/2023 (CARU Resolution 37/2023).' ],
			[ 'meses' => 'La Zona Lodge', 'que' => 'Its October 2026 post gives no season or dates: ask.' ],
		],
		'aguas' => [
			[ 'nombre' => 'Uruguay River, below the Salto Grande dam (La Zona)', 'nota' => 'Security Zone downstream of the dam; regulated fishing starts 500 m out (CARU Resolution 63/2026).', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'What is La Zona in Concordia?', 'respuesta' => 'It is what anglers call the Security Zone downstream of the Salto Grande dam, on the Uruguay River, between Concordia and Salto (Uruguay). The CARU rules allow catch-and-release dorado fishing from a line 500 m from the dam to the end of that zone.' ],
			[ 'pregunta' => 'Can anyone fish La Zona?', 'respuesta' => 'No. You fish only from authorised boats and by booking with the permit holder of each country; fishing from the bank is not allowed. As of 13/11/2026 we found no published holder of the Argentine permit.' ],
			[ 'pregunta' => 'Which days can you fish La Zona?', 'respuesta' => 'Monday to Thursday and in daylight only, since 1 June 2023 (CARU Resolution 37/2023).' ],
			[ 'pregunta' => 'When is the dorado closed season on the Uruguay River?', 'respuesta' => 'From 1 September to 31 December every year, for commercial and sport fishing (CARU Resolution 111/2023). We found no text that exempts La Zona: ask the place, in writing, how it applies to your dates.' ],
			[ 'pregunta' => 'Can I keep the dorado I catch in Concordia?', 'respuesta' => 'In La Zona, no: it is catch and release only. Outside La Zona we did not read the Entre Ríos rules for the Uruguay River: we cannot confirm it.' ],
			[ 'pregunta' => 'Do I need a fishing licence in Concordia?', 'respuesta' => 'We could not confirm it: we did not read the Entre Ríos fees, sizes and limits for the Uruguay River on an official page. Ask the Entre Ríos Natural Resources and Enforcement Directorate or your guide before you go.' ],
			[ 'pregunta' => 'What does La Zona Lodge include according to its post?', 'respuesta' => 'According to a post by the place itself (the place’s Instagram, 15/10/2026): an all inclusive system with lodging in air-conditioned apartments for groups, breakfast, lunch and dinner with drinks, fishing days, a pool and a daily transfer from the lodge to the Uruguay River. It gives no price, season, boats or guides.' ],
			[ 'pregunta' => 'How much does it cost to fish La Zona?', 'respuesta' => 'The place’s post gives no rate: ask. We do not publish agency rates.' ],
			[ 'pregunta' => 'How do I get to Concordia from Buenos Aires?', 'respuesta' => 'Concordia is about 430 km from Buenos Aires, according to El Cronista (article read on 13/11/2026).' ],
		],
	],
	'fuentes' => [
		'reg' => 'CARU, Resolution 27/2004: Catch-and-Release Fishing Rules for the Salto Grande Security Zone, updated text (Argentina.gob.ar)',
		'r63' => 'CARU, Resolution 63/2026 (Official Gazette 28/9/2026): 500 m limit',
		'r111' => 'CARU, Resolution 111/2023: dorado closed season, 1 September to 31 December',
		'a111' => 'CARU: notice “Dorado closed season 2026” (7/10/2026)',
		'bo' => 'Entre Ríos Official Gazette, 28/1/2026: Decree 3874/25, tender declared failed',
		'pliego' => 'Entre Ríos, Natural Resources and Enforcement: tender document, Competition No. 01-2026',
		'libre' => 'Libreentrerios (17/3/2026): status of the new tender (press)',
		'cron' => 'El Cronista: the river where the best dorado are caught (distance from Buenos Aires)',
	],
	'autoridad' => 'CARU (La Zona rules) and the Entre Ríos Natural Resources and Enforcement Directorate',
];

$G['pt'] = [
	'titulo' => 'Concordia', 'slug' => 'pesca-em-concordia',
	'cuerpo' => [
		[ 'p', '<strong>Concordia</strong> fica em Entre Ríos, no <strong>rio Uruguai</strong>, abaixo da represa de Salto Grande. Rio abaixo da represa está <strong>La Zona</strong>, a Zona de Segurança onde a CARU regula a pesca do dourado com devolução. Este guia é curto de propósito: reúne o que já publicamos em <a href="{n:la-zona-salto-grande}">nossa nota sobre La Zona</a>, lido nos textos da CARU e do Boletim Oficial de Entre Ríos, e a ficha de um lugar de Concordia, com sua data. O que não conseguimos confirmar, dizemos.' ],
		[ 'h2', 'Dados-chave' ],
		[ 'table', [ 'Dado', 'O que diz', 'Fonte' ], [
			[ 'Rio e zona', 'Rio Uruguai, abaixo da represa de Salto Grande; La Zona é a Zona de Segurança rio abaixo da represa', 'CARU, Resolução 27/2004 (texto atualizado)' ],
			[ 'O que se pesca em La Zona', 'Só dourado, com devolução, em embarcações autorizadas, de segunda a quinta e de dia', 'CARU (dias desde 1/6/2023)' ],
			[ 'Onde começa', 'A 500 m da represa desde a Resolução 63/2026 (Boletim Oficial de 28/9/2026)', 'CARU' ],
			[ 'Defeso do dourado no rio Uruguai', 'De 1.º de setembro a 31 de dezembro; não encontramos texto que exclua La Zona', 'CARU, Resolução 111/2023' ],
			[ 'Permissão do lado argentino', 'Em 13/11/2026 não encontramos adjudicação publicada', 'Entre Ríos, Decreto 3874/25 e edital 01-2026' ],
			[ 'Licença de pesca de Entre Ríos para o rio Uruguai', 'Não a lemos em página oficial: não podemos confirmá-la', 'Pendente' ],
			[ 'Lugar listado', 'La Zona Lodge, segundo a própria publicação (ficha abaixo)', 'Publicação do lugar, outubro de 2026' ],
			[ 'Tarifa e temporada', 'Sob consulta: a publicação não traz', 'Publicação do lugar, outubro de 2026' ],
		] ],
		[ 'h2', 'As regras de La Zona, em resumo' ],
		[ 'ul', [
			'<strong>Só dourado e só com devolução</strong>, com no máximo dois minutos fora da água por peixe.',
			'<strong>Só em embarcações autorizadas</strong>: não se pesca da margem nem se desembarca, salvo emergência.',
			'<strong>Isca artificial ou mosca</strong>, com anzol sem farpa ou com a farpa amassada ou preenchida; sem isca natural e sem corrico.',
			'<strong>De segunda a quinta e só de dia</strong>, desde 1.º de junho de 2023 (Resolução CARU 37/2023).',
			'<strong>Até quatro embarcações ao mesmo tempo</strong>, com até quatro pessoas a bordo, guia e piloto incluídos.',
		] ],
		[ 'p', 'Os artigos e as resoluções, um por um, estão na <a href="{n:la-zona-salto-grande}">nota sobre La Zona</a>.' ],
		[ 'h2', 'O defeso do dourado' ],
		[ 'p', 'A CARU fixa o defeso do dourado no rio Uruguai de <strong>1.º de setembro a 31 de dezembro de cada ano</strong>, para a pesca comercial e esportiva (Resolução CARU 111/2023; aviso da CARU de 7/10/2026). O regulamento de La Zona diz que sua temporada segue o que a CARU estabelece para o rio. Não encontramos texto que exclua La Zona do defeso, nem um que confirme que vale lá dentro: pergunte ao lugar, por escrito, como se aplica às suas datas. A página de turismo da Prefeitura de Concordia dá outras datas; a norma é a resolução da CARU.' ],
		[ 'h2', 'Quem pode levar você' ],
		[ 'p', 'Para pescar em La Zona é preciso contratar com o permissionário de um dos dois países. Do lado argentino, a licitação de 2023 foi declarada fracassada pelo Decreto 3874/25 de Entre Ríos (Boletim Oficial de 28/1/2026) e há um novo edital, o Concurso Público de Méritos e Antecedentes N° 01-2026. <strong>Em 13/11/2026 não encontramos adjudicação publicada.</strong>' ],
		[ 'p', 'A <strong>La Zona Lodge</strong> aparece abaixo como listagem com informação pública, ainda não verificada com o lugar. Segundo uma publicação do próprio lugar (Instagram do lugar, 15/10/2026, lida por nós em 18/11/2026), fica em Concordia, na «exclusiva reserva de Salto Grande», às margens do rio Uruguai, em sistema <em>all inclusive</em>: hospedagem, refeições, bebidas, jornadas de pesca e a infraestrutura da estadia, e traslado diário da pousada até o rio. A mesma publicação diz que é uma região «famosa pelos grandes dourados» e menciona dourado, surubim, traíra, pati, armado, pacu e piapara: é o que diz o lugar, não uma garantia de captura.' ],
		[ 'p', 'Essa publicação não diz qual licença cobre a pesca em La Zona, e também não traz preço, temporada, embarcações nem guias: fica como pergunta em aberto. Não dizemos que ao lugar falte alguma licença.' ],
		[ 'h2', 'Como chegar' ],
		[ 'p', 'Concordia fica a cerca de 430 km de Buenos Aires, segundo El Cronista (matéria lida em 13/11/2026). As descargas da represa mudam o rio: na <a href="{n:la-zona-salto-grande}">nota sobre La Zona</a> está o relatório de vazões e níveis da Comissão Técnica Mista de Salto Grande.' ],
		[ 'h2', 'Quanto custa' ],
		[ 'p', 'A publicação do lugar não traz tarifa e não publicamos tarifas de agências: <strong>sob consulta</strong>. Para ter uma referência dos lodges de dourado em geral, veja <a href="{n:precio-viaje-dorado-lodge}">quanto custa uma viagem de pesca de dourado</a>.' ],
		[ 'h2', 'Para ler antes de ir' ],
		[ 'ul', [
			'<a href="{n:la-zona-salto-grande}">La Zona (Salto Grande): regras do dourado e acesso</a>.',
			'<a href="{especie}">O dourado</a>: a espécie, seus tamanhos e onde vive.',
			'<a href="{n:cuando-pescar-dorado}">Quando pescar dourado</a>: as janelas e por que as fontes não coincidem.',
		] ],
		[ 'p', '<strong>Conte o que você procura e montamos a melhor opção para você.</strong> Diga datas, quantos vão e o que quer pescar no <a href="{viaje}">formulário da viagem</a>: sem custo e sem compromisso.' ],
		[ 'pend', 'permissão do lado argentino (Concurso N° 01-2026) e se o defeso vale dentro de La Zona; licença, tamanhos e cotas de Entre Ríos para o rio Uruguai; tarifa, temporada, embarcações e guias da La Zona Lodge (a publicação não traz); consentimento do lugar; foto com crédito.' ],
	],
	'campos' => [
		'h1' => 'Pesca de dourado em Concordia e La Zona: regras, defeso e hospedagem',
		'bajada' => 'Concordia, no rio Uruguai e abaixo da represa de Salto Grande: o que diz a CARU sobre La Zona, o defeso do dourado, quem pode levar você e uma hospedagem listada com sua data.',
		'modalidades' => 'Embarcada, com devolução (regulamento de La Zona)',
		'epoca' => 'Defeso do dourado de 1.º de setembro a 31 de dezembro no rio Uruguai (CARU). Em La Zona se pesca de segunda a quinta e de dia.',
		'seo_title' => 'Pesca em Concordia e La Zona: regras, defeso e hospedagem',
		'seo_description' => 'Concordia e La Zona, abaixo da represa de Salto Grande: regras da CARU para o dourado, defeso de 1/9 a 31/12, quem pode levar você e uma hospedagem listada.',
		'reglamento_resumen' => 'La Zona (Zona de Segurança rio abaixo da represa de Salto Grande): regulamento da CARU (Resolução 27/2004 e reformas; texto lido em 13/11/2026). Só dourado, com devolução, em embarcações autorizadas, de segunda a quinta e de dia; a pesca começa a 500 m da represa desde a Resolução 63/2026. Defeso do dourado no rio Uruguai: 1/9 a 31/12 (Resolução CARU 111/2023); não encontramos texto que exclua La Zona. Licença, tamanhos e cotas de Entre Ríos para o rio Uruguai: não lidos em página oficial, não podemos confirmá-los.',
		'temporada' => [
			[ 'meses' => '1.º de setembro – 31 de dezembro', 'que' => 'Defeso do dourado no rio Uruguai (CARU, Resolução 111/2023). Não encontramos texto que exclua La Zona nem um que confirme que vale lá dentro: pergunte por escrito.' ],
			[ 'meses' => 'Segunda a quinta', 'que' => 'Dias de pesca em La Zona, só de dia, desde 1/6/2023 (Resolução CARU 37/2023).' ],
			[ 'meses' => 'La Zona Lodge', 'que' => 'Sua publicação de outubro de 2026 não traz temporada nem datas: consulte.' ],
		],
		'aguas' => [
			[ 'nombre' => 'Rio Uruguai, abaixo da represa de Salto Grande (La Zona)', 'nota' => 'Zona de Segurança rio abaixo da represa; a pesca regulada começa a 500 m (Resolução CARU 63/2026).', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'O que é La Zona em Concordia?', 'respuesta' => 'É como os pescadores chamam a Zona de Segurança rio abaixo da represa de Salto Grande, no rio Uruguai, entre Concordia e Salto (Uruguai). O regulamento da CARU permite pescar dourado com devolução a partir de uma linha a 500 m da represa até o fim dessa zona.' ],
			[ 'pregunta' => 'Qualquer pessoa pode pescar em La Zona?', 'respuesta' => 'Não. Pesca-se só em embarcações autorizadas e contratando com o permissionário que cada país habilita; não se pode pescar da margem. Em 13/11/2026 não encontramos publicado quem tem a permissão do lado argentino.' ],
			[ 'pregunta' => 'Em que dias se pode pescar em La Zona?', 'respuesta' => 'De segunda a quinta e só de dia, desde 1.º de junho de 2023 (Resolução CARU 37/2023).' ],
			[ 'pregunta' => 'Quando é o defeso do dourado no rio Uruguai?', 'respuesta' => 'De 1.º de setembro a 31 de dezembro de cada ano, para a pesca comercial e esportiva (Resolução CARU 111/2023). Não encontramos texto que exclua La Zona: pergunte ao lugar, por escrito, como se aplica às suas datas.' ],
			[ 'pregunta' => 'Posso ficar com o dourado que pescar em Concordia?', 'respuesta' => 'Em La Zona, não: só se pesca com devolução. Fora de La Zona não lemos as regras de Entre Ríos para o rio Uruguai: não podemos confirmar.' ],
			[ 'pregunta' => 'É preciso licença de pesca em Concordia?', 'respuesta' => 'Não conseguimos confirmar: não lemos as taxas, tamanhos e cotas de Entre Ríos para o rio Uruguai em página oficial. Consulte a Direção de Recursos Naturais e Fiscalização de Entre Ríos ou o seu guia antes de sair.' ],
			[ 'pregunta' => 'O que inclui a La Zona Lodge segundo sua publicação?', 'respuesta' => 'Segundo uma publicação do próprio lugar (Instagram do lugar, 15/10/2026): sistema all inclusive com hospedagem em apartamentos climatizados para grupos, café da manhã, almoço e jantar com bebidas, jornadas de pesca, piscina e traslado diário da pousada até o rio Uruguai. Não traz preço, temporada, embarcações nem guias.' ],
			[ 'pregunta' => 'Quanto custa pescar em La Zona?', 'respuesta' => 'A publicação do lugar não traz tarifa: consulte. Não publicamos tarifas de agências.' ],
			[ 'pregunta' => 'Como chego a Concordia desde Buenos Aires?', 'respuesta' => 'Concordia fica a cerca de 430 km de Buenos Aires, segundo El Cronista (matéria lida em 13/11/2026).' ],
		],
	],
	'fuentes' => [
		'reg' => 'CARU, Resolução 27/2004: Regulamento de Pesca com Devolução na Zona de Segurança de Salto Grande, texto atualizado (Argentina.gob.ar)',
		'r63' => 'CARU, Resolução 63/2026 (Boletim Oficial 28/9/2026): limite de 500 m',
		'r111' => 'CARU, Resolução 111/2023: defeso do dourado, de 1.º de setembro a 31 de dezembro',
		'a111' => 'CARU: aviso «Veda do dourado 2026» (7/10/2026)',
		'bo' => 'Boletim Oficial de Entre Ríos de 28/1/2026: Decreto 3874/25, concurso declarado fracassado',
		'pliego' => 'Entre Ríos, Recursos Naturales y Fiscalización: edital do Concurso N° 01-2026',
		'libre' => 'Libreentrerios (17/3/2026): situação do novo concurso (imprensa)',
		'cron' => 'El Cronista: o rio onde se pescam os melhores dourados (distância de Buenos Aires)',
	],
	'autoridad' => 'CARU (regulamento de La Zona) e Direção de Recursos Naturais e Fiscalização de Entre Ríos',
];
$compartido = [ 'region' => 'litoral', 'provincia' => 'entre-rios', 'lat' => -31.39, 'lon' => -58.02, 'especies' => [ 'dorado' ] ];

// ---- the card -----------------------------------------------------------------------------------------------------------------------------
$ficha = [
	'nombre' => 'La Zona Lodge',
	'campos' => [
		'tipo' => [ 'alojamiento', 'operador' ],
		'localidad' => 'Concordia (Entre Ríos), río Uruguay, zona de Salto Grande',
		'descripcion' => 'Alojamiento de pesca en Concordia, a orillas del río Uruguay, en la zona de Salto Grande, según una publicación del propio lugar (Instagram del lugar, octubre de 2026). La publicación habla de una estructura completa con régimen all inclusive: hospedaje en apartamentos amplios y climatizados para grupos, desayuno, almuerzo y cena completos con bebidas, jornadas de pesca, piscina al aire libre, salón de estar y juegos con mesa de billar, y traslado diario de la posada al río Uruguay. Dice que está en una región «famosa por los grandes dorados» y menciona dorado, surubí, tararira, patí, armado, pacú y boga: es lo que dice el lugar, no una garantía de captura. No trae precio, temporada, embarcaciones ni guías, y no dice qué permiso cubre la pesca en La Zona. Su tarifa: a consultar.',
		'descripcion_en' => 'Fishing lodging in Concordia, on the banks of the Uruguay River, in the Salto Grande area, according to a post by the place itself (the place’s Instagram, October 2026). The post describes a full facility with an all inclusive system: lodging in spacious air-conditioned apartments for groups, full breakfast, lunch and dinner with drinks, fishing days, an outdoor pool, a lounge and games room with a pool table, and a daily transfer from the lodge to the Uruguay River. It says it is in a region “famous for big dorado” and mentions dorado, surubí, tararira, patí, armado, pacú and boga: that is what the place says, not a guarantee of a catch. It gives no price, season, boats or guides, and does not say which permit covers the fishing in La Zona. Its rate: on request.',
		'descripcion_pt' => 'Hospedagem de pesca em Concordia, às margens do rio Uruguai, na região de Salto Grande, segundo uma publicação do próprio lugar (Instagram do lugar, outubro de 2026). A publicação fala de uma estrutura completa em sistema all inclusive: hospedagem em apartamentos amplos e climatizados para grupos, café da manhã, almoço e jantar completos com bebidas, jornadas de pesca, piscina ao ar livre, sala de estar e jogos com mesa de sinuca, e traslado diário da pousada até o rio Uruguai. Diz que fica em uma região «famosa pelos grandes dourados» e menciona dourado, surubim, traíra, pati, armado, pacu e piapara: é o que diz o lugar, não uma garantia de captura. Não traz preço, temporada, embarcações nem guias, e não diz qual licença cobre a pesca em La Zona. A tarifa: sob consulta.',
		'prest_especies' => [ 'dorado', 'surubi', 'tararira', 'pati', 'pacu', 'boga' ],
		'incluye' => [ 'alojamiento', 'comida', 'bebidas', 'traslados' ],
		'precio' => '', 'precio_fecha' => '',
		'prest_fuente' => 'Instagram del lugar, octubre de 2026 (texto, sin enlace)',
		'web' => '',
		'notas' => 'Listado con información pública. Fuente: publicación de la cuenta del lugar (15/10/2026, «hace 4 semanas» al 18/11/2026), leída por el dueño en capturas y en el carrusel; el sitio web estaba caído (certificado vencido) el 18/11/2026. Sin precio, temporada, botes ni guías: la publicación no los trae. Las historias más viejas de la cuenta ofrecen también una cabaña de alquiler temporario en Concordia: es otra oferta, no se mezcla. No se guardan teléfonos ni correos en ningún archivo. Pendiente: escribir al lugar para pasar a consentimiento (docs/investigacion/la-zona-lugares-2026-11-17.md, preguntas).',
	],
];
$ficha['campos'] += [ 'prest_destino' => $clave, 'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado', 'verificado' => $hoy ];

// Nothing public may carry a link, contact, account, record, boat count, reopening or a "best" claim.
$prohibido = '/@|https?:\/\/|instagram\.com|lazonalodge|\+\d{2}[ \d]{6,}|tel[eé]fono|whatsapp|\bmejor(es)? (punto|lugar|destino|pesquer)|r[eé]cord|reabr|reopen|reabert|\bbotes\b|\bboats?\b[^.]{0,20}\d|\d+ (botes|boats|lanchas)/iu';
foreach ( $G as $i => $g ) {
	$visible = wp_json_encode( [ $g['campos'], array_filter( $g['cuerpo'], fn( $b ) => 'pend' !== $b[0] ) ], JSON_UNESCAPED_UNICODE );
	$visible = preg_replace( '#href=\\\\"[^"]*\\\\"#', '', $visible ); // the guide links only to our own notes, resolved later
	if ( preg_match( $prohibido, $visible, $m ) ) {
		WP_CLI::error( "texto público de la guía ($i) lleva algo que no se publica: «{$m[0]}»" );
	}
	$c  = $g['campos'];
	$tl = mb_strlen( $c['seo_title'] );
	$dl = mb_strlen( $c['seo_description'] );
	WP_CLI::log( "SEO ($i): title $tl, description $dl, faqs " . count( $c['faqs'] ) );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 && count( $c['faqs'] ) >= 8 ) || WP_CLI::error( "SEO o FAQs fuera de rango ($i)" );
}
$vf = wp_json_encode( array_diff_key( $ficha['campos'], [ 'notas' => 1, 'prest_fuente' => 1 ] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
if ( preg_match( $prohibido, $vf, $m ) ) {
	WP_CLI::error( "texto público de la ficha lleva algo que no se publica: «{$m[0]}»" );
}
WP_CLI::log( 'OK: texto público de la guía (es/en/pt) y de la ficha sin enlaces, contactos, cuentas, récords, número de botes ni «el mejor».' );

// ---- edits to existing notes --------------------------------------------------------------------------------------------------------------
$guia_txt = [ 'es' => 'la ficha de Concordia', 'en' => 'the Concordia listing', 'pt' => 'a ficha de Concordia' ];
$nota_ins = [
	'es' => [ 'ancla' => 'por cinco años; no leímos su resultado.', 'texto' => '<strong>Un lugar de Concordia.</strong> La Zona Lodge figura en {guia} como listado con información pública: según una publicación del propio lugar (Instagram del lugar, 15/10/2026, leída el 18/11/2026) está en la reserva de Salto Grande, a orillas del río Uruguay, con régimen all inclusive (alojamiento, comidas con bebidas, jornadas de pesca y traslado diario al río). Esa publicación no dice qué permiso cubre la pesca en La Zona: sigue como pregunta abierta.' ],
	'en' => [ 'ancla' => 'for five years; we did not read its outcome.', 'texto' => '<strong>A place in Concordia.</strong> La Zona Lodge is in {guia} as a public-information listing: according to a post by the place itself (the place’s Instagram, 15/10/2026, read on 18/11/2026) it is in the Salto Grande reserve, on the banks of the Uruguay River, with an all inclusive system (lodging, meals with drinks, fishing days and a daily transfer to the river). That post does not say which permit covers the fishing in La Zona: it remains an open question.' ],
	'pt' => [ 'ancla' => 'por cinco anos; não lemos o resultado.', 'texto' => '<strong>Um lugar de Concordia.</strong> A La Zona Lodge aparece em {guia} como listagem com informação pública: segundo uma publicação do próprio lugar (Instagram do lugar, 15/10/2026, lida em 18/11/2026) fica na reserva de Salto Grande, às margens do rio Uruguai, em sistema all inclusive (hospedagem, refeições com bebidas, jornadas de pesca e traslado diário até o rio). Essa publicação não diz qual licença cobre a pesca em La Zona: segue como pergunta em aberto.' ],
];
$pilar_ins = [
	'es' => [ 'viejo' => 'y figura en nuestra lista de destinos próximos (río Uruguay, dorado): todavía no tiene guía.', 'nuevo' => 'y tiene una guía corta: %s.', 'txt' => 'la guía de Concordia y La Zona' ],
	'en' => [ 'viejo' => 'and is on our list of coming destinations (Uruguay River, golden dorado): it has no guide yet.', 'nuevo' => 'and has a short guide: %s.', 'txt' => 'the Concordia and La Zona guide' ],
	'pt' => [ 'viejo' => 'e está na nossa lista de destinos próximos (rio Uruguai, dourado): ainda não tem guia.', 'nuevo' => 'e tem um guia curto: %s.', 'txt' => 'o guia de Concordia e La Zona' ],
];
// ---- 1. card + 2. guides plan -------------------------------------------------------------------------------------------------------------
$orden = [ 'es', 'en', 'pt' ];
$pid   = pm_cg_ficha_id( $ficha['nombre'] );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': ficha «' . $ficha['nombre'] . '» ' . ( $pid ? "#$pid ya existe: solo se llenan los campos vacíos" : 'NUEVA, campos:' ) );
$cambios_ficha = [];
foreach ( $ficha['campos'] as $k => $nuevo ) {
	if ( ! $pid ) {
		WP_CLI::log( "  + {$k}: " . pm_cg_ver( $nuevo ) );
		$cambios_ficha[ $k ] = $nuevo;
		continue;
	}
	$antes = pm_campo( $k, $pid );
	$vacio = '' === pm_cg_norm( $k, $antes ) || ( is_array( $antes ) && ! $antes );
	if ( $vacio && '' !== pm_cg_norm( $k, $nuevo ) ) {
		WP_CLI::log( "  ~ {$k}: vacío → " . pm_cg_ver( $nuevo ) );
		$cambios_ficha[ $k ] = $nuevo;
	} else {
		WP_CLI::log( "  = {$k} se deja" );
	}
}
$plan = [];
foreach ( $orden as $i ) {
	$g  = $G[ $i ];
	$ya = pm_cg_guia( $clave, $i );
	$cuerpo = pm_cg_bloques( pm_cg_resolver_todo( $g['cuerpo'], $i, $clave ) );
	$campos = pm_cg_resolver_todo( $g['campos'], $i, $clave );
	$campos['fuentes'] = array_map( fn( $k, $n ) => [ 'nombre' => $n, 'url' => $u[ $k ], 'oficial' => $oficial[ $k ], 'consultada' => $leido ], array_keys( $g['fuentes'] ), $g['fuentes'] );
	$campos += [ 'idioma' => $i, 'clave' => $clave, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'reglamento_autoridad' => $g['autoridad'], 'reglamento_url' => $u['reg'] ];
	if ( 'es' === $i ) {
		$campos += $compartido;
	}
	WP_CLI::log( ( $ya ? "{$clave} ({$i}) ya existe #{$ya->ID}: no toco nada" : ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$clave} ({$i}) se crea /{$g['slug']}/" ) . ' · ' . count( $campos['faqs'] ) . ' preguntas, ' . count( $campos['fuentes'] ) . ' fuentes, ' . count( $campos['temporada'] ) . ' filas de temporada, ' . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' caracteres de cuerpo' );
	if ( ! $ya ) {
		WP_CLI::log( "  h1: {$campos['h1']}\n  seo_title: {$campos['seo_title']}\n  seo_description: {$campos['seo_description']}\n  bajada: {$campos['bajada']}\n  epoca: {$campos['epoca']}\n  preguntas: " . implode( ' | ', array_column( $campos['faqs'], 'pregunta' ) ) );
	}
	$plan[ $i ] = [ 'ya' => $ya, 'titulo' => $g['titulo'], 'slug' => $g['slug'], 'cuerpo' => $cuerpo, 'campos' => $campos ];
}

// ---- 3. edits plan (La Zona note, dorado pillar) ------------------------------------------------------------------------------------------
$guia_url = function ( string $i ) use ( &$plan ) {
	$id = $plan[ $i ]['id'] ?? ( $plan[ $i ]['ya']->ID ?? 0 );
	return $id ? get_permalink( $id ) : 'https://pescamapa.com/(guia-de-concordia-' . $i . ')/';
};
$edits = [];
$hay_falta = false;
$marcas = [ 'es' => 'Un lugar de Concordia.', 'en' => 'A place in Concordia.', 'pt' => 'Um lugar de Concordia.' ];
foreach ( $orden as $i ) {
	foreach ( [ 'nota' => 'la-zona-salto-grande', 'pilar' => 'golden-dorado-guia' ] as $que => $k ) {
		$p = pm_buscar( 'pm_nota', $k, $i );
		if ( ! $p ) {
			WP_CLI::warning( "$que ($i): la nota $k no existe en este idioma" );
			$hay_falta = true;
			continue;
		}
		$c      = get_post( $p->ID )->post_content;
		$enlace = '<a href="' . esc_url( $guia_url( $i ) ) . '">';
		if ( 'nota' === $que ) {
			$viejo = $nota_ins[ $i ]['ancla'] . '</p>';
			$nuevo = $viejo . "\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>" . str_replace( '{guia}', $enlace . $guia_txt[ $i ] . '</a>', $nota_ins[ $i ]['texto'] ) . '</p>';
			$e     = false !== strpos( $c, $marcas[ $i ] ) ? 'ya' : ( 1 === substr_count( $c, $viejo ) ? 'cambia' : 'falta' );
		} else {
			$viejo = $pilar_ins[ $i ]['viejo'];
			$nuevo = sprintf( $pilar_ins[ $i ]['nuevo'], $enlace . $pilar_ins[ $i ]['txt'] . '</a>' );
			$e     = ( false === strpos( $c, $viejo ) && preg_match( '#/(pesca-en-concordia|fishing-in-concordia|pesca-em-concordia)/#', $c ) ) ? 'ya' : ( 1 === substr_count( $c, $viejo ) ? 'cambia' : 'falta' );
		}
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $que ($i) #{$p->ID} " . get_permalink( $p->ID ) . ": $e" );
		if ( 'cambia' === $e ) {
			WP_CLI::log( "      ANTES:   $viejo\n      DESPUÉS: $nuevo" );
		}
		if ( 'falta' === $e ) {
			WP_CLI::warning( "$que ($i): no encontré el texto publicado el 13/11/2026 (o aparece más de una vez); no escribo" );
			$hay_falta = true;
		}
		$edits[] = [ 'que' => $que, 'i' => $i, 'id' => $p->ID, 'estado' => $e ];
	}
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' . ( $hay_falta ? ' (HAY «falta» o notas ausentes: revisar antes de aplicar)' : '' ) );
	return;
}
if ( $hay_falta ) {
	WP_CLI::error( 'no aplico: hay textos que no están como se esperaba (ver «falta»).' );
}

// ---- Apply --------------------------------------------------------------------------------------------------------------------------------
if ( ! $pid ) {
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $ficha['nombre'] ], true );
	is_wp_error( $pid ) && WP_CLI::error( $pid->get_error_message() );
	WP_CLI::log( "✓ ficha «{$ficha['nombre']}» creada #$pid" );
}
foreach ( $cambios_ficha as $k => $v ) {
	update_field( $k, $v, $pid );
}
$t = time();
foreach ( $orden as $i ) {
	$x = $plan[ $i ];
	if ( $x['ya'] ) {
		continue;
	}
	if ( 'es' !== $i && ! pm_cg_guia( $clave, 'es' ) ) {
		WP_CLI::error( "No hay guía en español de {$clave}: no creo la de {$i}." );
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $x['titulo'], 'post_name' => $x['slug'], 'post_content' => wp_slash( $x['cuerpo'] ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	foreach ( $x['campos'] as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
	$plan[ $i ]['id'] = (int) $id;
	WP_CLI::log( "✓ {$clave} ({$i}) publicada #{$id}: " . get_permalink( $id ) );
	if ( 'es' === $i ) {
		$proximo = get_page_by_path( 'concordia', OBJECT, 'pm_proximo' );
		if ( $proximo && get_post_thumbnail_id( $proximo->ID ) ) {
			set_post_thumbnail( $id, get_post_thumbnail_id( $proximo->ID ) );
			WP_CLI::log( 'foto: la de la tarjeta "próximamente"' );
		} else {
			WP_CLI::warning( 'sin foto: cargala desde el panel (Imagen destacada)' );
		}
	}
}
foreach ( $orden as $i ) {
	$plan[ $i ]['id'] = $plan[ $i ]['id'] ?? $plan[ $i ]['ya']->ID;
}
// Edits again, now that the guide permalinks exist (the plan above only had placeholders).
foreach ( $edits as $e ) {
	$i = $e['i'];
	$p = get_post( $e['id'] );
	if ( 'ya' === $e['estado'] ) {
		WP_CLI::log( "{$e['que']} ($i) #{$e['id']}: ya estaba" );
		continue;
	}
	$enlace = '<a href="' . esc_url( get_permalink( $plan[ $i ]['id'] ) ) . '">';
	if ( 'nota' === $e['que'] ) {
		$parrafo = str_replace( '{guia}', $enlace . $guia_txt[ $i ] . '</a>', $nota_ins[ $i ]['texto'] );
		$viejo   = $nota_ins[ $i ]['ancla'] . '</p>';
		$nuevo   = $viejo . "\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n<p>" . $parrafo . '</p>';
		$ya      = false !== strpos( $p->post_content, get_permalink( $plan[ $i ]['id'] ) );
	} else {
		$viejo = $pilar_ins[ $i ]['viejo'];
		$nuevo = sprintf( $pilar_ins[ $i ]['nuevo'], $enlace . $pilar_ins[ $i ]['txt'] . '</a>' );
		$ya    = false === strpos( $p->post_content, $viejo );
	}
	if ( $ya ) {
		WP_CLI::log( "{$e['que']} ($i) #{$e['id']}: ya estaba" );
		continue;
	}
	if ( 1 !== substr_count( $p->post_content, $viejo ) ) {
		WP_CLI::error( "{$e['que']} ($i): el texto esperado no está exactamente una vez; no escribo." );
	}
	$r = wp_update_post( [ 'ID' => $e['id'], 'post_content' => wp_slash( str_replace( $viejo, $nuevo, $p->post_content ) ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	update_post_meta( $e['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
}

// ---- Read back ----------------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $ficha['campos'] as $k => $v ) {
	if ( 'notas' === $k ) {
		continue;
	}
	$b = pm_cg_norm( $k, pm_campo( $k, $pid ) ) === pm_cg_norm( $k, $v );
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": «{$ficha['nombre']}» #$pid $k = " . mb_substr( pm_cg_ver( pm_campo( $k, $pid ) ), 0, 120 ) );
	$ok = $ok && $b;
}
$publico = '';
foreach ( [ '', '_en', '_pt' ] as $s ) {
	$publico .= ' ' . pm_campo( 'descripcion' . $s, $pid ) . ' ' . pm_campo( 'precio' . $s, $pid );
}
$limpio = ! preg_match( $prohibido, $publico . ' ' . pm_campo( 'localidad', $pid ) );
WP_CLI::log( ( $limpio ? 'VERIFIED' : 'FALLÓ' ) . ": «{$ficha['nombre']}» texto público es/en/pt sin enlaces, contactos ni cuentas; precio vacío (a consultar)" );
$ok = $ok && $limpio;
$r = null;
foreach ( pm_prestadores_destino( $clave ) as $x ) {
	if ( (int) $x['id'] === (int) $pid ) {
		$r = $x;
	}
}
$bien = $r && pm_prestador_publico( $r ) && ! $r['consentimiento'] && ! $r['acuerdo'] && '' === $r['precio'] && [] === $r['fotos'];
WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": «{$ficha['nombre']}» sale en la lista de {$clave} como información pública (publico=" . (int) ( $r['publico'] ?? 0 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ' fotos=' . count( $r['fotos'] ?? [] ) . ')' );
$ok = $ok && $bien;
foreach ( $orden as $i ) {
	$x  = $plan[ $i ];
	$id = $x['id'];
	$p  = get_post( $id );
	$b  = $p && 'publish' === $p->post_status && $p->post_name === $x['slug'] && pm_idioma_de( $id ) === $i && pm_clave_de( $id ) === $clave;
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) #{$id} publicada, slug {$p->post_name} " . get_permalink( $id ) );
	$ok = $ok && $b;
	if ( $x['ya'] ) {
		continue;
	}
	foreach ( $x['campos'] as $k => $v ) {
		if ( isset( PM_CG_REPETIDORES[ $k ] ) ) {
			$bb = wp_json_encode( pm_cg_filas( $k, $id, PM_CG_REPETIDORES[ $k ] ), JSON_UNESCAPED_UNICODE ) === wp_json_encode( pm_cg_filas_envio( $v, PM_CG_REPETIDORES[ $k ] ), JSON_UNESCAPED_UNICODE );
			WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) $k" );
			$ok = $ok && $bb;
			continue;
		}
		if ( 'es' !== $i && array_key_exists( $k, $compartido ) ) {
			continue;
		}
		$bb = pm_cg_norm( $k, pm_campo( $k, $id ) ) === pm_cg_norm( $k, $v );
		WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) $k = " . mb_substr( pm_cg_ver( pm_campo( $k, $id ) ), 0, 100 ) );
		$ok = $ok && $bb;
	}
	$html = apply_filters( 'the_content', $p->post_content );
	$sin  = false === strpos( $html, 'PENDIENTE' ) && false === strpos( $html, '{n:' ) && false === strpos( $html, '{viaje}' ) && ! preg_match( '#href="https?://(www\.)?(instagram\.com|lazonalodge)#i', $html );
	WP_CLI::log( ( $sin ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) cuerpo publicado sin PENDIENTE, sin marcadores y sin enlaces a Instagram ni al sitio del lugar (" . substr_count( $html, '<a ' ) . ' enlaces)' );
	$ok = $ok && $sin;
}
foreach ( $compartido as $k => $v ) {
	$bb = pm_cg_norm( $k, pm_campo( $k, $plan['es']['id'] ) ) === pm_cg_norm( $k, $v );
	WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} (es) $k = " . pm_cg_ver( pm_campo( $k, $plan['es']['id'] ) ) );
	$ok = $ok && $bb;
}
foreach ( $edits as $e ) {
	$c   = get_post( $e['id'] )->post_content;
	$url = get_permalink( $plan[ $e['i'] ]['id'] );
	$bb  = false !== strpos( $c, 'href="' . esc_url( $url ) . '"' ) && ( 'nota' === $e['que'] || false === strpos( $c, $pilar_ins[ $e['i'] ]['viejo'] ) );
	WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": {$e['que']} ({$e['i']}) #{$e['id']} enlaza la guía de Concordia" . ( 'pilar' === $e['que'] ? ' y ya no dice que no tiene guía' : '' ) );
	$ok = $ok && $bb;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
