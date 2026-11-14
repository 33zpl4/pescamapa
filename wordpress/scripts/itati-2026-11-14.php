<?php
/**
 * Itatí y el Alto Paraná (Corrientes), 14/11/2026: NEW destination guide (es, en, pt, published) with two operator cards (Suindá Lodge, Itatí Lodge).
 * Owner-approved new place for a real gap (demand: "golden dorado fishing", docs/estrategia-seo-2026-10-31.md). Facts and evidence levels:
 * docs/investigacion/corrientes-dorado-lodges-2026-11-13.md. First-hand pages (branch `investigacion`, read 13/11/2026):
 *   - Suindá Lodge, Nervous Waters (the group that says it runs its lodges): corrientes-1-20261113-2308/02 (header "October to May", rates block
 *     "RATES 2026-27 / Season: October 2026 – May 2027": 7,950 / 7,595 / 6,495 / 5,350 / 4,050 for 7/6/5/4/3 nights, per person, double, shared guide; single
 *     room and boat +60 %; non-angler US$ 650 per night; full / half day on arrival or departure US$ 950 / 750 per boat; included / not included; 10 anglers;
 *     getting there by CNQ / RES) and corrientes-2-20261113-2313/03 (FAQ: 50 % deposit within 2 weeks, full payment 120 days before; licence arranged
 *     before arrival; dorado lodges are fly and spinning).
 *   - Itatí Lodge, SET Fly Fishing: corrientes-3-20261113-2316/01 (5 rooms for 10 anglers, "Just 40 minutes from Corrientes Airport", season
 *     "September–May (avoid Jan–Feb for heat)", full weeks and short stays "4 nights 3 days"; NO rate), /03 (airports; deposit 50 % non-refundable within
 *     15 days, full payment 90 days before; address kept out of the page), /06 (October 2024 season article).
 *   - COMIP–UNNE report (corrientes-2-20261113-2313/16): Itatí km 1279, three hotel complexes, about 60 guides, low impact, October tournament.
 *   - Closures of 2026: Perfil articles (corrientes-2-20261113-2313/09 to 12), "número pendiente" where the article gives none.
 * Rules: Disposición 853/22 as already published (paso-de-la-patria-2026-10-21, note tallas-cupos-vedas); licence litres as in the note
 * pesca-desde-brasil-corrientes. No new regulatory claim. No agency figure anywhere (the script refuses to run if one appears). Cards: "información pública,
 * sin verificar" (no consent, no agreement), prices only from the operator's own page with the date read, no contact data, no photo.
 * Coordinates: the repo has none for Itatí; the unset default would pin the guide at lat 0 / lon 0, so the pin uses an approximate town point (Itatí
 * lies about 39 km of river upstream of Paso de la Patria, km 1240 to km 1279 in the COMIP report): flagged PENDIENTE for the owner to confirm.
 * Idempotent: an existing guide is never touched; an existing card only gets its empty fields filled. Dry run first ($aplicar = false).
 * Run with wp-script.yml (script = itati-2026-11-14).
 */

$aplicar = false;
$hoy     = '20261114'; // date of the guide text
$leido   = '20261113'; // date the operators' pages and the press were read (branch `investigacion`)

// ---- helpers (same pattern as rio-gallegos-glencross-2026-11-13 / paso-de-la-patria-2026-10-21) ---------------------------------------
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
/** Anything an agency (not the operator's own page) published for these lodges: it must never reach a card, a guide or a FAQ. */
const PM_CG_PROHIBIDO = '/7[.,]850|4[.,]500|7[.,]000|6[.,]250|5[.,]100|4[.,]250|3[.,]200|US\$ ?450\b|US\$ ?50\b|Farbank|Fly Water|Yellow Dog|Orvis|flyfishingcaribe/u';
// ---- end helpers ---------------------------------------------------------------------------------------------------------------------------

$D = json_decode( <<<'JSON'
{
 "clave": "itati",
 "compartido": {
  "region": "litoral",
  "provincia": "corrientes",
  "lat": -27.27,
  "lon": -58.23,
  "especies": [
   "dorado"
  ]
 },
 "guias": {
  "es": {
   "titulo": "Itatí",
   "slug": "pesca-en-itati",
   "cuerpo": [
    [
     "p",
     "Itatí está sobre el <strong>Alto Paraná</strong>, en la margen argentina del río que separa la Argentina de Paraguay. Según el <a href=\"https://comip.org.ar/wp-content/uploads/2023/08/INFORME-FINAL-Pesca-Recreativa-FEBRERO-2019-%E2%80%93-ENERO-2020-COMIP-UNNE.pdf\" rel=\"noopener\">informe «Evaluación de la pesca recreativa en el río Paraná en tramos compartidos entre Argentina y Paraguay» (Acta Complementaria N.º 3, informe final de febrero de 2019 a enero de 2020; Convenio COMIP-UNNE, Instituto de Ictiología del Nordeste)</a>, que lo ubica en el km 1279 del río, en el pueblo había entonces tres complejos hoteleros de tamaño medio con alojamiento y lancha con guía, unos 60 guías (en general por cuenta propia) y una afluencia baja de pescadores, y cada octubre se hacía un concurso de pesca variada embarcada con devolución. Son datos de 2017 a 2020: pueden haber cambiado. Encontramos dos lodges de dorado con guía y página propia: <strong>Suindá</strong>, de Nervous Waters, e <strong>Itatí Lodge</strong>, de SET Fly Fishing. Abajo, las tarjetas dicen solo lo que cada operador publica."
    ],
    [
     "h2",
     "Datos clave"
    ],
    [
     "table",
     [
      "Dato",
      "Qué dice",
      "Fuente"
     ],
     [
      [
       "Río y zona",
       "Alto Paraná, margen argentina; Itatí en el km 1279",
       "Informe COMIP–UNNE (2019-2020)"
      ],
      [
       "Especies que venden los lodges",
       "Dorado; pacú y pirá pitá como complemento",
       "Páginas de Suindá y de Itatí Lodge (13/11/2026)"
      ],
      [
       "Pesca del dorado",
       "Solo pesca y devolución",
       "Disposición 853/22"
      ],
      [
       "Licencia",
       "Licencia de Corrientes; turista extranjero: 16 a 36 litros de nafta súper según los días",
       "Recursos Naturales; nuestra nota sobre Brasil"
      ],
      [
       "Tarifa publicada por el operador",
       "Suindá: de US$ 4.050 a US$ 7.950 por persona (2026-27, doble, guía compartido). Itatí Lodge: no publica tarifa",
       "Página de Suindá (13/11/2026)"
      ],
      [
       "Temporada según los operadores",
       "De septiembre u octubre a mayo; no coinciden en enero y febrero",
       "Páginas de los operadores (13/11/2026)"
      ],
      [
       "Cómo llegar",
       "Itatí Lodge a 40 minutos del aeropuerto de Corrientes; Suindá a 45 minutos en auto y un traslado en lancha desde Corrientes",
       "Páginas de los operadores (13/11/2026)"
      ]
     ]
    ],
    [
     "h2",
     "Qué se puede llevar: las reglas básicas de Corrientes"
    ],
    [
     "p",
     "La <strong>Disposición 853/22</strong> de la Dirección de Recursos Naturales (Boletín Oficial de Corrientes, 26 de diciembre de 2022), tal como la transcribimos en <a href=\"{n:tallas-cupos-vedas}\">la nota de tallas, cupos y vedas</a>, rige hasta que otro acto la modifique. Para las especies más buscadas dice:"
    ],
    [
     "ul",
     [
      "<strong>Dorado y surubí</strong> (manchado y atigrado): solo pesca y devolución. No se retienen.",
      "<strong>Pacú</strong>: talla mínima 45 cm; cupo de 1 por licencia vigente y un solo ejemplar por embarcación, sin importar cuántos pescadores haya a bordo.",
      "<strong>Patí</strong>: talla mínima 70 cm; cupo de 2.",
      "<strong>Boga</strong>: talla mínima 45 cm; cupo de 2 por pescador con licencia vigente y hasta 6 por embarcación (si hay más pescadores a bordo, se reparten esas 6).",
      "<strong>Tararira</strong>: talla mínima 30 cm; cupo de 5."
     ]
    ],
    [
     "p",
     "Las especies del anexo solo pueden movilizarse dentro de la localidad donde se pescaron: está prohibido llevarlas a otro destino, nacional o internacional."
    ],
    [
     "h2",
     "Licencia de pesca"
    ],
    [
     "p",
     "Hace falta la licencia de pesca deportiva de Corrientes: se saca por internet en el portal de Recursos Naturales y se lleva con el documento de identidad. Para quien tiene domicilio fuera de la Argentina existe la licencia de turista extranjero, que se fija en litros de nafta súper: 16 litros por un día, 20 por dos, 24 por tres, 28 por cuatro y 36 por seis (así figura en <a href=\"{n:pesca-desde-brasil-corrientes}\">nuestra nota sobre pescar en Corrientes desde Brasil</a>, que también da el valor aproximado en pesos). El acuerdo regional de reconocimiento de licencias (Santa Fe, Entre Ríos y Chaco) es para residentes de esas provincias y no cubre los tramos de jurisdicción exclusivamente correntina. No encontramos una norma que reconozca licencias de otros países: quien viene de afuera tiene que sacar la de Corrientes."
    ],
    [
     "p",
     "Qué dice cada operador: <strong>Suindá</strong> no incluye la licencia en la tarifa (la lista como no incluida) y Nervous Waters dice en sus preguntas frecuentes que pide los datos del pescador para sacarla antes de la llegada. La página de <strong>Itatí Lodge</strong> no menciona la licencia: preguntalo antes de reservar."
    ],
    [
     "p",
     "Todos los permisos por región, en pesos y en dólares aproximados: <a href=\"{n:licencias-extranjeros-argentina}\">licencia de pesca para extranjeros en Argentina 2026/27</a>."
    ],
    [
     "pend",
     "precios de la licencia 2026 con fecha (la nota de Brasil da un valor aproximado calculado con el precio de la nafta); el texto de la Disposición 853/22 en el Boletín Oficial no se pudo releer el 13/11/2026 (el PDF no abrió en el runner): la transcripción es la de las notas ya publicadas."
    ],
    [
     "h2",
     "Vedas: lo que pasó en 2026"
    ],
    [
     "p",
     "Recursos Naturales puede disponer vedas extraordinarias por tramo del Paraná, y en 2026 lo hizo. Esto es lo que contó la prensa (no leímos las disposiciones, solo las notas de Perfil):"
    ],
    [
     "ul",
     [
      "Perfil, 4/6/2026: la Disposición 639/26 levantó la veda de la Disposición 618/26, que prohibía toda pesca entre los km 1232 y 1238 por una concentración de surubíes.",
      "Perfil, 10/9/2026: desde el 9 de septiembre, a las 18:00, veda extraordinaria entre los km 1109 y 1150 por una concentración de dorados (<a href=\"https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml\" rel=\"noopener\">nota</a>; el número de la disposición no figura en la nota: número pendiente). La nota dice que el área es dinámica y que la veda seguiría al cardumen.",
      "Perfil, 16/9/2026: se sumó el tramo de los km 1180 a 1239, del riacho INTA a la zona de Confluencia, en jurisdicción de Paso de la Patria (<a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">nota</a>; número pendiente).",
      "Perfil, 29/9/2026: la Disposición 1054 levantó la veda desde las 13:00 de ese día; si vuelven a concentrarse peces, habrá un nuevo acto con restricciones (<a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">nota</a>)."
     ]
    ],
    [
     "p",
     "El informe COMIP–UNNE ubica Itatí en el km 1279. Todos los tramos con kilómetros que dio la prensa están entre los km 1109 y 1239, y la nota del 10/9 dice que la veda podía seguir al cardumen: no podemos decir que Itatí quedara afuera ni adentro."
    ],
    [
     "p",
     "Las vedas cambian cada año y a veces de una semana a otra: antes de salir, consultá las vigentes con <a href=\"https://www.recnaturalesctes.gob.ar/\" rel=\"noopener\">Recursos Naturales</a> o con tu guía. En <a href=\"{n:tallas-cupos-vedas}\">la nota de tallas, cupos y vedas</a> está la misma línea de tiempo."
    ],
    [
     "pend",
     "números de las disposiciones de septiembre de 2026 (veda de los km 1109 a 1150 y de los km 1180 a 1239): la prensa no los da."
    ],
    [
     "h2",
     "Cómo llegar"
    ],
    [
     "p",
     "Según la página de cada operador: <strong>Itatí Lodge</strong> está a 40 minutos del aeropuerto de Corrientes. <strong>Suindá</strong> queda en la margen sur (argentina) del Paraná, lo más cerca del pueblo ribereño de Itatí; desde Buenos Aires (Aeroparque) hay vuelos diarios sin escalas a Corrientes (CNQ) o Resistencia (RES), de una hora aproximadamente; desde Corrientes son 45 minutos en auto hasta la orilla y un traslado corto en lancha hasta el lodge, y desde Resistencia unas dos horas en auto hasta la orilla y la lancha. La página de información de SET nombra Corrientes como aeropuerto preferido y Resistencia como alternativa."
    ],
    [
     "h2",
     "Cuánto cuesta"
    ],
    [
     "p",
     "Los lodges de dorado venden estadías de 3 a 7 noches, por persona. En <a href=\"{n:precio-viaje-dorado-lodge}\">cuánto cuesta un viaje de pesca de dorado</a> comparamos las tarifas por cantidad de noches y lo que queda fuera del precio, y en <a href=\"{n:viaje-dorado-amigos-precios}\">el viaje de dorado con amigos</a> hay una opción más barata, en posada. Abajo, cada tarjeta muestra solo la tarifa que publica la página del propio operador, con la fecha en que la leímos; la que no publica una tarifa dice «a consultar»."
    ],
    [
     "h2",
     "Para leer antes de ir"
    ],
    [
     "ul",
     [
      "<a href=\"{especie}\">El dorado</a>: la especie, sus tamaños y dónde vive.",
      "<a href=\"{n:cuando-pescar-dorado}\">Cuándo pescar dorado en el Paraná</a>: las ventanas y por qué las fuentes no coinciden.",
      "<a href=\"{n:licencias-extranjeros-argentina}\">Licencia de pesca para extranjeros en Argentina 2026/27</a>."
     ]
    ],
    [
     "p",
     "<strong>¿Querés que te ayudemos a elegir?</strong> Decinos qué querés pescar y cuándo en <a href=\"{viaje}\">el formulario del viaje</a> y consultamos a los operadores que encajan; te respondemos con las opciones, sin costo ni compromiso."
    ],
    [
     "pend",
     "programas de días de semana de Itatí Lodge; tarifa de Itatí Lodge (su página no la publica); confirmación de ambos operadores; coordenadas del pin (las que cargamos son aproximadas, del municipio: confirmar); foto con su crédito (foto pendiente)."
    ]
   ],
   "campos": {
    "h1": "Pesca de dorado en Itatí y el Alto Paraná, Corrientes: lodges, reglas y licencia",
    "bajada": "Itatí, sobre el Alto Paraná, tiene lodges de dorado con guía que encontramos. Qué dice la página de cada operador, qué podés llevarte, qué licencia hace falta y qué pasó con las vedas de 2026.",
    "modalidades": "Embarcada con guía\nMosca (flycast)\nBaitcasting y spinning (según el lodge)",
    "epoca": "De septiembre u octubre a mayo según los operadores; no coinciden en enero y febrero.",
    "seo_title": "Pesca de dorado en Itatí: lodges, reglas y licencia",
    "seo_description": "Pesca de dorado en Itatí y el Alto Paraná, Corrientes: dos lodges con lo que publica cada operador, pesca y devolución, licencia, vedas de 2026 y cómo llegar.",
    "reglamento_resumen": "Para pescar en Corrientes hace falta licencia de pesca deportiva, que se tramita online en el portal de Recursos Naturales. La Disposición 853/22 (Boletín Oficial, 26/12/2022, vigente hasta nuevo acto) fija que dorado y surubí son solo pesca y devolución y que pacú (45 cm, cupo 1 por licencia y un solo ejemplar por embarcación), patí (70 cm, cupo 2), boga (45 cm, cupo 2 por pescador y hasta 6 por embarcación) y tararira (30 cm, cupo 5) tienen talla mínima y cupo. Lo retenido solo puede movilizarse dentro de la localidad donde se pescó. La provincia puede disponer vedas extraordinarias por tramo del Paraná: en 2026 las hubo, según la prensa, y se levantaron el 29 de septiembre. Antes de salir, consultá las vigentes con Recursos Naturales.",
    "temporada": [
     {
      "meses": "Suindá (Nervous Waters)",
      "que": "Su página dice «octubre a mayo» en el encabezado y «Season: October 2026 – May 2027» en las tarifas; el texto describe la primavera (de fines de septiembre a diciembre, antes del desove de diciembre) y el verano (de enero a abril)."
     },
     {
      "meses": "Itatí Lodge (SET Fly Fishing)",
      "que": "Su página dice «septiembre a mayo (evitar enero y febrero por el calor)». En un artículo de octubre de 2024, SET pone de septiembre a noviembre como lo mejor para el dorado, antes del desove, y febrero y marzo para pacú y pirapitá."
     },
     {
      "meses": "Entre las dos fuentes",
      "que": "No coinciden en el comienzo (septiembre, fines de septiembre u octubre) ni en enero y febrero: Suindá los incluye en su temporada y SET los desaconseja por el calor. Confirmalo con el operador."
     },
     {
      "meses": "Antes de cada salida",
      "que": "Consultá las vedas vigentes con Recursos Naturales o con tu guía."
     },
     {
      "meses": "Calendario por especie y mes",
      "que": "PENDIENTE: confirmar con una fuente técnica o con guías locales."
     }
    ],
    "aguas": [
     {
      "nombre": "Río Paraná (Alto Paraná) frente a Itatí",
      "nota": "Según el informe COMIP–UNNE, Itatí está en el km 1279. Consultá las vedas vigentes antes de pescar acá.",
      "lat": "",
      "lon": ""
     }
    ],
    "faqs": [
     {
      "pregunta": "¿Cuándo conviene ir a pescar dorado a Itatí?",
      "respuesta": "Los operadores no coinciden. Suindá dice «octubre a mayo» (tarifas de octubre de 2026 a mayo de 2027) y describe el desove en diciembre; Itatí Lodge dice «septiembre a mayo» y recomienda evitar enero y febrero por el calor, y en un artículo de octubre de 2024 SET pone de septiembre a noviembre como lo mejor para el dorado. Páginas leídas el 13/11/2026; confirmalo con el operador."
     },
     {
      "pregunta": "¿Cuánto cuesta un viaje a un lodge de Itatí?",
      "respuesta": "Suindá publica en su página, para 2026-27, por persona en habitación doble y con guía compartido: US$ 7.950 por 7 noches y 6 días de pesca, US$ 7.595 por 6 noches, US$ 6.495 por 5, US$ 5.350 por 4 y US$ 4.050 por 3 noches (leída el 13/11/2026). Itatí Lodge no publica tarifa en su página: se consulta. Comparación con otros lodges: nuestra nota sobre cuánto cuesta un viaje de pesca de dorado."
     },
     {
      "pregunta": "¿Qué incluye y qué no incluye la tarifa de Suindá?",
      "respuesta": "Según su página: incluye alojamiento en doble, todas las comidas, vinos, cerveza y bebidas locales e internacionales, guía profesional y equipo de préstamo (cañas, reeles y líneas). No incluye pasajes aéreos, traslados desde y hacia el lodge, licencias de pesca, lavandería ni propinas. La página de Itatí Lodge no publica qué incluye."
     },
     {
      "pregunta": "¿Hace falta licencia de pesca y la incluye el lodge?",
      "respuesta": "Hace falta la licencia de Corrientes (para quien vive fuera de la Argentina, la de turista extranjero, que se calcula en litros de nafta súper). Suindá la lista como no incluida y Nervous Waters dice que pide los datos para sacarla antes de la llegada; la página de Itatí Lodge no habla de licencia: preguntalo antes de reservar."
     },
     {
      "pregunta": "¿Se puede pescar con spinning o solo con mosca?",
      "respuesta": "Suindá dice que también permite spinning y baitcasting, y Nervous Waters dice que sus lodges de dorado son de mosca y de spinning. La página de Itatí Lodge describe equipo de mosca (cañas #7 y #8, líneas tropicales): preguntale si ofrece spinning."
     },
     {
      "pregunta": "¿Puedo quedarme con el dorado que pesque?",
      "respuesta": "No. Según la Disposición 853/22 de Recursos Naturales (Boletín Oficial, 26/12/2022), dorado y surubí son solo pesca y devolución en Corrientes."
     },
     {
      "pregunta": "¿Cómo llego a Itatí desde Buenos Aires?",
      "respuesta": "Según los operadores: vuelo de una hora de Aeroparque a Corrientes (CNQ) o Resistencia (RES). Itatí Lodge está a 40 minutos del aeropuerto de Corrientes; para Suindá son 45 minutos en auto desde Corrientes (unas dos horas desde Resistencia) hasta la orilla y un traslado corto en lancha."
     },
     {
      "pregunta": "¿Hay recargo por viajar solo y tarifa para acompañantes que no pescan?",
      "respuesta": "Suindá, en su página de 2026-27: habitación y lancha individuales con un 60 % de recargo, y acompañante que no pesca a US$ 650 por noche por persona, según disponibilidad (leída el 13/11/2026). Itatí Lodge no publica estas condiciones."
     },
     {
      "pregunta": "¿Cómo se paga la reserva?",
      "respuesta": "Cada operador dice lo suyo. Nervous Waters, en su página de preguntas frecuentes: una vez confirmadas las fechas manda una factura electrónica; para confirmar la reserva hace falta un depósito del 50 % dentro de las dos semanas siguientes, y el viaje se paga completo 120 días antes de la llegada (acepta transferencia, cheque y tarjeta). SET Fly Fishing, en su página de información del viaje (para todos sus paquetes de la Argentina): un depósito del 50 % por pescador, no reembolsable, dentro de los 15 días de la reserva, y el pago total 90 días antes del inicio. Todos los pagos son no reembolsables, pero se pueden transferir a otro cliente para las mismas fechas, y recomienda un seguro de viaje."
     },
     {
      "pregunta": "¿Hubo vedas en 2026 cerca de Itatí?",
      "respuesta": "Según la prensa (Perfil), hubo vedas extraordinarias en el Paraná en septiembre, en dos tramos (km 1109 a 1150 y km 1180 a 1239), y la Disposición 1054 las levantó el 29/9/2026. El informe COMIP–UNNE ubica Itatí en el km 1279. Consultá las vigentes con Recursos Naturales antes de salir."
     }
    ]
   },
   "fuentes": [
    [
     "Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)",
     "https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf",
     1
    ],
    [
     "Recursos Naturales de Corrientes",
     "https://www.recnaturalesctes.gob.ar/",
     1
    ],
    [
     "Ministerio de Turismo de Corrientes: trámites y permisos (licencia de pesca)",
     "https://corrientes.tur.ar/tramites-y-permisos/",
     1
    ],
    [
     "Ministerio de Turismo de Corrientes: acuerdo regional de licencias (alcance)",
     "https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/",
     1
    ],
    [
     "Perfil: se levantó la veda de los km 1232 a 1238, Disposición 639/26 (4/6/2026)",
     "https://www.perfil.com/noticias/nea/tras-la-dispersion-de-los-cardumenes-de-surubi-corrientes-levanto-la-veda-extraordinaria-en-el-parana.phtml",
     0
    ],
    [
     "Perfil: veda extraordinaria entre los km 1109 y 1150 por una concentración de dorados (10/9/2026)",
     "https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml",
     0
    ],
    [
     "Perfil: veda en un nuevo tramo, km 1180 a 1239 (16/9/2026)",
     "https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml",
     0
    ],
    [
     "Perfil: se levantó la veda extraordinaria, Disposición 1054 (29/9/2026)",
     "https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml",
     0
    ],
    [
     "Informe COMIP–UNNE: Evaluación de la pesca recreativa en el río Paraná en tramos compartidos entre Argentina y Paraguay (feb. 2019 – ene. 2020)",
     "https://comip.org.ar/wp-content/uploads/2023/08/INFORME-FINAL-Pesca-Recreativa-FEBRERO-2019-%E2%80%93-ENERO-2020-COMIP-UNNE.pdf",
     0
    ],
    [
     "Nervous Waters: Suindá Lodge (operador; tarifas 2026-27, leída el 13/11/2026)",
     "https://www.nervouswaters.com/the-lodges/suinda-lodge/",
     0
    ],
    [
     "Nervous Waters: preguntas frecuentes (pago, licencia, spinning)",
     "https://www.nervouswaters.com/faqs/",
     0
    ],
    [
     "SET Fly Fishing: Itatí Lodge (operador)",
     "https://setflyfishing.com/en/itati-lodge/",
     0
    ],
    [
     "SET Fly Fishing: información del viaje (aeropuertos, depósito y pago)",
     "https://setflyfishing.com/en/trip-information-package/",
     0
    ],
    [
     "SET Fly Fishing: estaciones y temporadas en Argentina (octubre de 2024)",
     "https://setflyfishing.com/en/argentinas-fly-fishing-seasons-when-and-where-to-catch-the-prize/",
     0
    ]
   ],
   "autoridad": "Dirección de Recursos Naturales de Corrientes"
  },
  "en": {
   "titulo": "Itatí",
   "slug": "fishing-in-itati",
   "cuerpo": [
    [
     "p",
     "Itatí is on the <strong>Upper Paraná</strong>, on the Argentine bank of the river that separates Argentina from Paraguay. According to <a href=\"https://comip.org.ar/wp-content/uploads/2023/08/INFORME-FINAL-Pesca-Recreativa-FEBRERO-2019-%E2%80%93-ENERO-2020-COMIP-UNNE.pdf\" rel=\"noopener\">the report “Evaluación de la pesca recreativa en el río Paraná en tramos compartidos entre Argentina y Paraguay” (Acta Complementaria N.º 3, final report for February 2019 to January 2020; COMIP-UNNE agreement, Instituto de Ictiología del Nordeste; in Spanish)</a>, which places it at km 1279 of the river, the town then had three mid-sized hotel complexes offering lodging and a boat with a guide, about 60 guides (generally self-employed) and a low number of anglers, and every October a catch-and-release mixed-species boat tournament was held. That is data from 2017 to 2020: it may have changed. We found two guided dorado lodges with pages of their own: <strong>Suindá</strong>, from Nervous Waters, and <strong>Itatí Lodge</strong>, from SET Fly Fishing. Below, the cards say only what each operator publishes."
    ],
    [
     "h2",
     "Key facts"
    ],
    [
     "table",
     [
      "Item",
      "What it says",
      "Source"
     ],
     [
      [
       "River and area",
       "Upper Paraná, Argentine bank; Itatí at km 1279",
       "COMIP–UNNE report (2019-2020)"
      ],
      [
       "Species the lodges sell",
       "Dorado; pacú and pirá pitá as a complement",
       "Suindá and Itatí Lodge pages (13/11/2026)"
      ],
      [
       "Dorado fishing",
       "Catch and release only",
       "Disposición 853/22"
      ],
      [
       "Licence",
       "Corrientes licence; foreign tourist: 16 to 36 litres of súper petrol depending on the days",
       "Recursos Naturales; our note on Brazil"
      ],
      [
       "Rate published by the operator",
       "Suindá: US$ 4,050 to US$ 7,950 per person (2026-27, double room, shared guide). Itatí Lodge: no rate published",
       "Suindá page (13/11/2026)"
      ],
      [
       "Season according to the operators",
       "From September or October to May; they differ on January and February",
       "Operators’ pages (13/11/2026)"
      ],
      [
       "Getting there",
       "Itatí Lodge is 40 minutes from Corrientes airport; Suindá is a 45-minute drive plus a boat transfer from Corrientes",
       "Operators’ pages (13/11/2026)"
      ]
     ]
    ],
    [
     "h2",
     "What you can keep: the basic rules of Corrientes"
    ],
    [
     "p",
     "<strong>Disposición 853/22</strong> of the Natural Resources Directorate (Corrientes Official Gazette, 26 December 2022), as we transcribe it in <a href=\"{n:tallas-cupos-vedas}\">our note on size and bag limits and closed seasons</a>, applies until another act changes it. For the most sought-after species it says:"
    ],
    [
     "ul",
     [
      "<strong>Dorado and surubí</strong> (spotted and striped): catch and release only. They cannot be kept.",
      "<strong>Pacú</strong>: minimum size 45 cm; bag limit 1 per valid licence and a single fish per boat, no matter how many anglers are aboard.",
      "<strong>Patí</strong>: minimum size 70 cm; bag limit 2.",
      "<strong>Boga</strong>: minimum size 45 cm; bag limit 2 per angler with a valid licence and up to 6 per boat (if there are more anglers aboard, those 6 are shared among them).",
      "<strong>Tararira</strong> (trahira): minimum size 30 cm; bag limit 5."
     ]
    ],
    [
     "p",
     "Species in the annex may only be moved within the town where they were caught: taking them to another destination, domestic or international, is forbidden."
    ],
    [
     "h2",
     "Fishing licence"
    ],
    [
     "p",
     "You need the Corrientes sport fishing licence: it is bought online on the Natural Resources portal and carried with your identity document. For people whose home is outside Argentina there is the foreign tourist licence, which is set in litres of “súper” petrol: 16 litres for one day, 20 for two, 24 for three, 28 for four and 36 for six (that is how it appears in <a href=\"{n:pesca-desde-brasil-corrientes}\">our note on fishing in Corrientes from Brazil</a>, which also gives the approximate value in pesos). The regional licence-recognition agreement (Santa Fe, Entre Ríos and Chaco) is for residents of those provinces and does not cover stretches under exclusively Corrientes jurisdiction. We found no rule recognising licences from other countries: visitors from abroad need the Corrientes licence."
    ],
    [
     "p",
     "What each operator says: <strong>Suindá</strong> does not include the licence in the rate (it lists it as not included) and Nervous Waters says in its frequently asked questions that it asks for the angler’s details to get it before arrival. The <strong>Itatí Lodge</strong> page does not mention the licence: ask before you book."
    ],
    [
     "p",
     "All permits by region, in pesos and approximate dollars: <a href=\"{n:licencias-extranjeros-argentina}\">fishing license for foreigners in Argentina 2026/27</a>."
    ],
    [
     "pend",
     "precios de la licencia 2026 con fecha (la nota de Brasil da un valor aproximado calculado con el precio de la nafta); el texto de la Disposición 853/22 en el Boletín Oficial no se pudo releer el 13/11/2026 (el PDF no abrió en el runner): la transcripción es la de las notas ya publicadas."
    ],
    [
     "h2",
     "Closed seasons: what happened in 2026"
    ],
    [
     "p",
     "Recursos Naturales can order extraordinary closures on stretches of the Paraná, and in 2026 it did. This is what the press reported (we have not read the orders themselves, only the Perfil articles, in Spanish):"
    ],
    [
     "ul",
     [
      "Perfil, 4/6/2026: Disposición 639/26 lifted the closure of Disposición 618/26, which had banned all fishing between km 1232 and 1238 because of a concentration of surubí.",
      "Perfil, 10/9/2026: from 9 September at 18:00, an extraordinary closure between km 1109 and 1150 because of a concentration of dorado (<a href=\"https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml\" rel=\"noopener\">article</a>; the article does not give the order number: number pending). It says the protected area is dynamic and that the closure would follow the shoal.",
      "Perfil, 16/9/2026: the stretch from km 1180 to 1239, from the INTA creek to the Confluencia area, under Paso de la Patria jurisdiction, was added (<a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">article</a>; number pending).",
      "Perfil, 29/9/2026: Disposición 1054 lifted the closure from 13:00 that day; if fish concentrate again, a new act with restrictions will follow (<a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">article</a>)."
     ]
    ],
    [
     "p",
     "The COMIP–UNNE report places Itatí at km 1279. Every stretch whose kilometres the press gave lies between km 1109 and 1239, and the 10/9 article says the closure could follow the shoal: we cannot say that Itatí was left out or included."
    ],
    [
     "p",
     "Closures change every year and sometimes from one week to the next: before you go, check the current ones with <a href=\"https://www.recnaturalesctes.gob.ar/\" rel=\"noopener\">Recursos Naturales</a> or your guide. The same timeline is in <a href=\"{n:tallas-cupos-vedas}\">our note on size and bag limits and closed seasons</a>."
    ],
    [
     "pend",
     "números de las disposiciones de septiembre de 2026 (veda de los km 1109 a 1150 y de los km 1180 a 1239): la prensa no los da."
    ],
    [
     "h2",
     "Getting there"
    ],
    [
     "p",
     "According to each operator’s page: <strong>Itatí Lodge</strong> is 40 minutes from Corrientes airport. <strong>Suindá</strong> is on the southern (Argentine) bank of the Paraná, closest to the river town of Itatí; from Buenos Aires (Aeroparque) there are daily non-stop flights to Corrientes (CNQ) or Resistencia (RES), about one hour; from Corrientes it is a 45-minute drive to the water’s edge and a short boat transfer to the lodge, and from Resistencia about two hours by car to the water’s edge, then the boat. SET’s information page names Corrientes as the preferred airport and Resistencia as the alternative."
    ],
    [
     "h2",
     "How much it costs"
    ],
    [
     "p",
     "Dorado lodges sell stays of 3 to 7 nights, per person. In <a href=\"{n:precio-viaje-dorado-lodge}\">how much a dorado fishing trip costs</a> we compare the rates by number of nights and what is left out of the price, and in <a href=\"{n:viaje-dorado-amigos-precios}\">the dorado trip with friends</a> there is a cheaper option, in an inn. Below, each card shows only the rate published on the operator’s own page, with the date we read it; a card whose operator does not publish a rate says “ask for a quote”."
    ],
    [
     "h2",
     "Read before you go"
    ],
    [
     "ul",
     [
      "<a href=\"{especie}\">The golden dorado</a>: the species, its sizes and where it lives.",
      "<a href=\"{n:cuando-pescar-dorado}\">When to fish golden dorado on the Paraná</a>: the windows and why the sources disagree.",
      "<a href=\"{n:licencias-extranjeros-argentina}\">Fishing license for foreigners in Argentina 2026/27</a>."
     ]
    ],
    [
     "p",
     "<strong>Want us to help you choose?</strong> Tell us what you want to fish and when in <a href=\"{viaje}\">the trip form</a> and we will ask the operators that fit; we write back with the options, at no cost or commitment."
    ],
    [
     "pend",
     "programas de días de semana de Itatí Lodge; tarifa de Itatí Lodge (su página no la publica); confirmación de ambos operadores; coordenadas del pin (las que cargamos son aproximadas, del municipio: confirmar); foto con su crédito (foto pendiente)."
    ]
   ],
   "campos": {
    "h1": "Golden dorado fishing in Itatí and the Upper Paraná, Corrientes: lodges, rules and licence",
    "bajada": "Itatí, on the Upper Paraná, has guided dorado lodges. What each operator’s page says, what you can keep, which licence you need and what happened with the 2026 closures.",
    "modalidades": "Guided boat fishing\nFly fishing (flycast)\nBaitcasting and spinning (depending on the lodge)",
    "epoca": "From September or October to May according to the operators; they differ on January and February.",
    "seo_title": "Golden dorado fishing in Itatí: lodges, rules, licence",
    "seo_description": "Golden dorado fishing in Itatí, Upper Paraná, Corrientes: two lodges as each operator publishes them, catch and release, licence, 2026 closures and access.",
    "reglamento_resumen": "Fishing in Corrientes requires a sport fishing licence, bought online on the Natural Resources portal. Disposición 853/22 (Official Gazette, 26/12/2022, in force until another act replaces it) sets dorado and surubí as catch and release only, and gives minimum sizes and bag limits for pacú (45 cm, 1 per licence and a single fish per boat), patí (70 cm, 2), boga (45 cm, 2 per angler and up to 6 per boat) and tararira (30 cm, 5). Fish you keep may only be moved within the town where they were caught. The province can order extraordinary closures on stretches of the Paraná: in 2026 there were some, according to the press, and they were lifted on 29 September. Before you go, check the current ones with Natural Resources.",
    "temporada": [
     {
      "meses": "Suindá (Nervous Waters)",
      "que": "Its page says “October to May” in the header and “Season: October 2026 – May 2027” in the rates; the text describes spring (late September to December, before the December spawn) and summer (January to April)."
     },
     {
      "meses": "Itatí Lodge (SET Fly Fishing)",
      "que": "Its page says “September–May (avoid Jan–Feb for heat)”. In an October 2024 article, SET puts September to November as the best time for dorado, before the spawn, and February and March for pacú and pirapitá."
     },
     {
      "meses": "Between the two sources",
      "que": "They differ on the start (September, late September or October) and on January and February: Suindá includes them in its season and SET advises against them because of the heat. Confirm with the operator."
     },
     {
      "meses": "Before every trip",
      "que": "Check the closures in force with Recursos Naturales or your guide."
     },
     {
      "meses": "Calendario por especie y mes",
      "que": "PENDIENTE: confirmar con una fuente técnica o con guías locales."
     }
    ],
    "aguas": [
     {
      "nombre": "Paraná River (Upper Paraná) off Itatí",
      "nota": "According to the COMIP–UNNE report, Itatí is at km 1279. Check the closures in force before fishing here.",
      "lat": "",
      "lon": ""
     }
    ],
    "faqs": [
     {
      "pregunta": "When is the best time to fish dorado in Itatí?",
      "respuesta": "The operators disagree. Suindá says “October to May” (rates from October 2026 to May 2027) and describes the December spawn; Itatí Lodge says “September–May” and advises avoiding January and February for the heat, and in an October 2024 article SET puts September to November as the best time for dorado. Pages read on 13/11/2026; confirm with the operator."
     },
     {
      "pregunta": "How much does a trip to a lodge in Itatí cost?",
      "respuesta": "Suindá publishes on its page, for 2026-27, per person in a double room and with a shared guide: US$ 7,950 for 7 nights and 6 fishing days, US$ 7,595 for 6 nights, US$ 6,495 for 5, US$ 5,350 for 4 and US$ 4,050 for 3 nights (read on 13/11/2026). Itatí Lodge publishes no rate on its page: ask for it. For a comparison with other lodges, see our note on how much a dorado fishing trip costs."
     },
     {
      "pregunta": "What does the Suindá rate include and exclude?",
      "respuesta": "According to its page: it includes accommodation based on double occupancy, all meals, wines, beer and local and international spirits, a professional guide and loaner equipment (rods, reels and lines). It does not include airfares, transfers in and out of the lodge, fishing licences, laundry or gratuities. The Itatí Lodge page does not publish what is included."
     },
     {
      "pregunta": "Do I need a fishing licence, and does the lodge include it?",
      "respuesta": "You need the Corrientes licence (for people who live outside Argentina, the foreign tourist licence, which is calculated in litres of súper petrol). Suindá lists it as not included and Nervous Waters says it asks for your details to get it before arrival; the Itatí Lodge page does not mention the licence: ask before you book."
     },
     {
      "pregunta": "Can I fish with spinning gear or only fly?",
      "respuesta": "Suindá says it also allows spinning and baitcasting, and Nervous Waters says its dorado lodges are both fly and spinning. The Itatí Lodge page describes fly tackle (#7 and #8 rods, tropical lines): ask whether it offers spinning."
     },
     {
      "pregunta": "Can I keep the dorado I catch?",
      "respuesta": "No. Under Disposición 853/22 of Recursos Naturales (Official Gazette, 26/12/2022), dorado and surubí are catch and release only in Corrientes."
     },
     {
      "pregunta": "How do I get to Itatí from Buenos Aires?",
      "respuesta": "According to the operators: a one-hour flight from Aeroparque to Corrientes (CNQ) or Resistencia (RES). Itatí Lodge is 40 minutes from Corrientes airport; for Suindá it is a 45-minute drive from Corrientes (about two hours from Resistencia) to the water’s edge and a short boat transfer."
     },
     {
      "pregunta": "Is there a single supplement, and a rate for companions who do not fish?",
      "respuesta": "Suindá, on its 2026-27 page: a single room and boat carry a 60% surcharge, and a non-angler companion is US$ 650 per night per person, depending on availability (read on 13/11/2026). Itatí Lodge does not publish these conditions."
     },
     {
      "pregunta": "How is the booking paid?",
      "respuesta": "Each operator says its own. Nervous Waters, on its frequently asked questions page: once the dates are confirmed it sends an electronic invoice; to confirm the reservation a 50% deposit is needed within the next two weeks, and the trip is paid in full 120 days before arrival (it accepts wire transfer, cheque and card). SET Fly Fishing, on its trip information page (for all its Argentine packages): a 50% non-refundable deposit per angler within 15 days of booking, and full payment 90 days before the start. All payments are non-refundable, but they can be transferred to another client for the same dates, and it recommends trip insurance."
     },
     {
      "pregunta": "Were there closures in 2026 near Itatí?",
      "respuesta": "According to the press (Perfil), there were extraordinary closures on the Paraná in September, on two stretches (km 1109 to 1150 and km 1180 to 1239), and Disposición 1054 lifted them on 29/9/2026. The COMIP–UNNE report places Itatí at km 1279. Check the current ones with Recursos Naturales before you go."
     }
    ]
   },
   "fuentes": [
    [
     "Corrientes Official Gazette: Disposición 853/22 (size and bag limits)",
     "https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf",
     1
    ],
    [
     "Recursos Naturales de Corrientes",
     "https://www.recnaturalesctes.gob.ar/",
     1
    ],
    [
     "Corrientes Tourism Ministry: procedures and permits (fishing licence)",
     "https://corrientes.tur.ar/tramites-y-permisos/",
     1
    ],
    [
     "Corrientes Tourism Ministry: regional licence agreement (scope)",
     "https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/",
     1
    ],
    [
     "Perfil: closure of km 1232 to 1238 lifted, Disposición 639/26 (4/6/2026, in Spanish)",
     "https://www.perfil.com/noticias/nea/tras-la-dispersion-de-los-cardumenes-de-surubi-corrientes-levanto-la-veda-extraordinaria-en-el-parana.phtml",
     0
    ],
    [
     "Perfil: extraordinary closure between km 1109 and 1150 for a dorado concentration (10/9/2026, in Spanish)",
     "https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml",
     0
    ],
    [
     "Perfil: closure on a new stretch, km 1180 to 1239 (16/9/2026, in Spanish)",
     "https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml",
     0
    ],
    [
     "Perfil: extraordinary closure lifted, Disposición 1054 (29/9/2026, in Spanish)",
     "https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml",
     0
    ],
    [
     "COMIP–UNNE report: Evaluación de la pesca recreativa en el río Paraná en tramos compartidos entre Argentina y Paraguay (Feb. 2019 – Jan. 2020, in Spanish)",
     "https://comip.org.ar/wp-content/uploads/2023/08/INFORME-FINAL-Pesca-Recreativa-FEBRERO-2019-%E2%80%93-ENERO-2020-COMIP-UNNE.pdf",
     0
    ],
    [
     "Nervous Waters: Suindá Lodge (operator; 2026-27 rates, read on 13/11/2026)",
     "https://www.nervouswaters.com/the-lodges/suinda-lodge/",
     0
    ],
    [
     "Nervous Waters: frequently asked questions (payment, licence, spinning)",
     "https://www.nervouswaters.com/faqs/",
     0
    ],
    [
     "SET Fly Fishing: Itatí Lodge (operator)",
     "https://setflyfishing.com/en/itati-lodge/",
     0
    ],
    [
     "SET Fly Fishing: trip information (airports, deposit and payment)",
     "https://setflyfishing.com/en/trip-information-package/",
     0
    ],
    [
     "SET Fly Fishing: Argentina’s fly fishing seasons (October 2024)",
     "https://setflyfishing.com/en/argentinas-fly-fishing-seasons-when-and-where-to-catch-the-prize/",
     0
    ]
   ],
   "autoridad": "Dirección de Recursos Naturales de Corrientes"
  },
  "pt": {
   "titulo": "Itatí",
   "slug": "pesca-em-itati",
   "cuerpo": [
    [
     "p",
     "Itatí fica no <strong>Alto Paraná</strong>, na margem argentina do rio que separa a Argentina do Paraguai. Segundo <a href=\"https://comip.org.ar/wp-content/uploads/2023/08/INFORME-FINAL-Pesca-Recreativa-FEBRERO-2019-%E2%80%93-ENERO-2020-COMIP-UNNE.pdf\" rel=\"noopener\">o relatório «Evaluación de la pesca recreativa en el río Paraná en tramos compartidos entre Argentina y Paraguay» (Acta Complementaria N.º 3, relatório final de fevereiro de 2019 a janeiro de 2020; Convênio COMIP-UNNE, Instituto de Ictiología del Nordeste; em espanhol)</a>, que o situa no km 1279 do rio, o povoado tinha então três complexos hoteleiros de porte médio com hospedagem e lancha com guia, cerca de 60 guias (em geral autônomos) e uma baixa afluência de pescadores, e todo outubro se fazia um torneio de pesca variada embarcada com devolução. São dados de 2017 a 2020: podem ter mudado. Encontramos dois lodges de dourado com guia e página própria: <strong>Suindá</strong>, da Nervous Waters, e <strong>Itatí Lodge</strong>, da SET Fly Fishing. Abaixo, os cartões dizem só o que cada operador publica."
    ],
    [
     "h2",
     "Dados-chave"
    ],
    [
     "table",
     [
      "Dado",
      "O que diz",
      "Fonte"
     ],
     [
      [
       "Rio e zona",
       "Alto Paraná, margem argentina; Itatí no km 1279",
       "Relatório COMIP–UNNE (2019-2020)"
      ],
      [
       "Espécies que os lodges vendem",
       "Dourado; pacu e pirapitá como complemento",
       "Páginas do Suindá e do Itatí Lodge (13/11/2026)"
      ],
      [
       "Pesca do dourado",
       "Somente pesca e devolução",
       "Disposição 853/22"
      ],
      [
       "Licença",
       "Licença de Corrientes; turista estrangeiro: 16 a 36 litros de gasolina comum conforme os dias",
       "Recursos Naturales; nossa nota sobre o Brasil"
      ],
      [
       "Tarifa publicada pelo operador",
       "Suindá: de US$ 4.050 a US$ 7.950 por pessoa (2026-27, quarto duplo, guia compartilhado). Itatí Lodge: não publica tarifa",
       "Página do Suindá (13/11/2026)"
      ],
      [
       "Temporada segundo os operadores",
       "De setembro ou outubro a maio; divergem em janeiro e fevereiro",
       "Páginas dos operadores (13/11/2026)"
      ],
      [
       "Como chegar",
       "Itatí Lodge a 40 minutos do aeroporto de Corrientes; Suindá a 45 minutos de carro e um traslado de lancha desde Corrientes",
       "Páginas dos operadores (13/11/2026)"
      ]
     ]
    ],
    [
     "h2",
     "O que pode levar: as regras básicas de Corrientes"
    ],
    [
     "p",
     "A <strong>Disposição 853/22</strong> da Direção de Recursos Naturais (Diário Oficial de Corrientes, 26 de dezembro de 2022), como a transcrevemos em <a href=\"{n:tallas-cupos-vedas}\">nossa nota de tamanhos, limites e defesos</a>, vale até que outro ato a modifique. Para as espécies mais procuradas, diz:"
    ],
    [
     "ul",
     [
      "<strong>Dourado e surubim</strong> (pintado e cachara): somente pesca e devolução. Não podem ser retidos.",
      "<strong>Pacu</strong>: tamanho mínimo de 45 cm; limite de 1 por licença válida e um único exemplar por embarcação, independentemente de quantos pescadores estejam a bordo.",
      "<strong>Patí</strong>: tamanho mínimo de 70 cm; limite de 2.",
      "<strong>Boga</strong>: tamanho mínimo de 45 cm; limite de 2 por pescador com licença válida e até 6 por embarcação (se houver mais pescadores a bordo, esses 6 são divididos entre eles).",
      "<strong>Traíra</strong>: tamanho mínimo de 30 cm; limite de 5."
     ]
    ],
    [
     "p",
     "As espécies do anexo só podem circular dentro da localidade onde foram pescadas: é proibido levá-las para outro destino, nacional ou internacional."
    ],
    [
     "h2",
     "Licença de pesca"
    ],
    [
     "p",
     "É preciso a licença de pesca esportiva de Corrientes: ela é tirada pela internet no portal de Recursos Naturais e levada com o documento de identidade. Para quem tem domicílio fora da Argentina existe a licença de turista estrangeiro, fixada em litros de gasolina comum: 16 litros por um dia, 20 por dois, 24 por três, 28 por quatro e 36 por seis (é como aparece em <a href=\"{n:pesca-desde-brasil-corrientes}\">nossa nota sobre pescar em Corrientes desde o Brasil</a>, que também dá o valor aproximado em pesos). O acordo regional de reconhecimento de licenças (Santa Fe, Entre Ríos e Chaco) vale para residentes dessas províncias e não cobre os trechos de jurisdição exclusivamente de Corrientes. Não encontramos uma norma que reconheça licenças de outros países: quem vem de fora precisa tirar a de Corrientes."
    ],
    [
     "p",
     "O que diz cada operador: o <strong>Suindá</strong> não inclui a licença na tarifa (a lista como não incluída) e a Nervous Waters diz nas perguntas frequentes que pede os dados do pescador para tirá-la antes da chegada. A página do <strong>Itatí Lodge</strong> não menciona a licença: pergunte antes de reservar."
    ],
    [
     "p",
     "Todas as licenças por região, em pesos e em dólares aproximados: <a href=\"{n:licencias-extranjeros-argentina}\">licença de pesca para estrangeiros na Argentina 2026/27</a>."
    ],
    [
     "pend",
     "precios de la licencia 2026 con fecha (la nota de Brasil da un valor aproximado calculado con el precio de la nafta); el texto de la Disposición 853/22 en el Boletín Oficial no se pudo releer el 13/11/2026 (el PDF no abrió en el runner): la transcripción es la de las notas ya publicadas."
    ],
    [
     "h2",
     "Defesos: o que aconteceu em 2026"
    ],
    [
     "p",
     "Recursos Naturales pode decretar defesos extraordinários por trecho do Paraná, e em 2026 o fez. Isto é o que a imprensa contou (não lemos as disposições, só as matérias do Perfil, em espanhol):"
    ],
    [
     "ul",
     [
      "Perfil, 4/6/2026: a Disposição 639/26 suspendeu o defeso da Disposição 618/26, que proibia toda a pesca entre os km 1232 e 1238 por uma concentração de surubins.",
      "Perfil, 10/9/2026: a partir de 9 de setembro, às 18:00, defeso extraordinário entre os km 1109 e 1150 por uma concentração de dourados (<a href=\"https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml\" rel=\"noopener\">matéria</a>; a matéria não traz o número da disposição: número pendente). Diz que a área é dinâmica e que o defeso acompanharia o cardume.",
      "Perfil, 16/9/2026: acrescentou-se o trecho dos km 1180 a 1239, do riacho INTA até a zona de Confluencia, na jurisdição de Paso de la Patria (<a href=\"https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml\" rel=\"noopener\">matéria</a>; número pendente).",
      "Perfil, 29/9/2026: a Disposição 1054 suspendeu o defeso a partir das 13:00 desse dia; se os peixes voltarem a se concentrar, haverá um novo ato com restrições (<a href=\"https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml\" rel=\"noopener\">matéria</a>)."
     ]
    ],
    [
     "p",
     "O relatório COMIP–UNNE situa Itatí no km 1279. Todos os trechos com quilômetros que a imprensa informou ficam entre os km 1109 e 1239, e a matéria de 10/9 diz que o defeso podia acompanhar o cardume: não podemos dizer que Itatí ficou de fora nem de dentro."
    ],
    [
     "p",
     "Os defesos mudam todo ano e às vezes de uma semana para outra: antes de sair, consulte os vigentes com <a href=\"https://www.recnaturalesctes.gob.ar/\" rel=\"noopener\">Recursos Naturales</a> ou com o seu guia. A mesma linha do tempo está em <a href=\"{n:tallas-cupos-vedas}\">nossa nota de tamanhos, limites e defesos</a>."
    ],
    [
     "pend",
     "números de las disposiciones de septiembre de 2026 (veda de los km 1109 a 1150 y de los km 1180 a 1239): la prensa no los da."
    ],
    [
     "h2",
     "Como chegar"
    ],
    [
     "p",
     "Segundo a página de cada operador: o <strong>Itatí Lodge</strong> fica a 40 minutos do aeroporto de Corrientes. O <strong>Suindá</strong> fica na margem sul (argentina) do Paraná, o mais perto do povoado ribeirinho de Itatí; de Buenos Aires (Aeroparque) há voos diários sem escalas para Corrientes (CNQ) ou Resistencia (RES), de cerca de uma hora; de Corrientes são 45 minutos de carro até a margem e um traslado curto de lancha até o lodge, e de Resistencia cerca de duas horas de carro até a margem e a lancha. A página de informações da SET cita Corrientes como aeroporto preferido e Resistencia como alternativa."
    ],
    [
     "h2",
     "Quanto custa"
    ],
    [
     "p",
     "Os lodges de dourado vendem estadias de 3 a 7 noites, por pessoa. Em <a href=\"{n:precio-viaje-dorado-lodge}\">quanto custa uma viagem de pesca de dourado</a> comparamos as tarifas por quantidade de noites e o que fica fora do preço, e em <a href=\"{n:viaje-dorado-amigos-precios}\">a viagem de dourado com amigos</a> há uma opção mais barata, em pousada. Abaixo, cada cartão mostra apenas a tarifa publicada na página do próprio operador, com a data em que a lemos; quando o operador não publica tarifa, o cartão diz «sob consulta»."
    ],
    [
     "h2",
     "Para ler antes de ir"
    ],
    [
     "ul",
     [
      "<a href=\"{especie}\">O dourado</a>: a espécie, seus tamanhos e onde vive.",
      "<a href=\"{n:cuando-pescar-dorado}\">Quando pescar dourado no Paraná</a>: as janelas e por que as fontes não coincidem.",
      "<a href=\"{n:licencias-extranjeros-argentina}\">Licença de pesca para estrangeiros na Argentina 2026/27</a>."
     ]
    ],
    [
     "p",
     "<strong>Quer que a gente ajude a escolher?</strong> Conte o que quer pescar e quando no <a href=\"{viaje}\">formulário da viagem</a> e consultamos os operadores que se encaixam; respondemos com as opções, sem custo nem compromisso."
    ],
    [
     "pend",
     "programas de días de semana de Itatí Lodge; tarifa de Itatí Lodge (su página no la publica); confirmación de ambos operadores; coordenadas del pin (las que cargamos son aproximadas, del municipio: confirmar); foto con su crédito (foto pendiente)."
    ]
   ],
   "campos": {
    "h1": "Pesca de dourado em Itatí e no Alto Paraná, Corrientes: lodges, regras e licença",
    "bajada": "Itatí, no Alto Paraná, tem lodges de dourado com guia. O que diz a página de cada operador, o que pode levar, que licença é preciso e o que houve com os defesos de 2026.",
    "modalidades": "Embarcada com guia\nFly (flycast)\nBaitcasting e spinning (conforme o lodge)",
    "epoca": "De setembro ou outubro a maio segundo os operadores; divergem em janeiro e fevereiro.",
    "seo_title": "Pesca de dourado em Itatí: lodges, regras e licença",
    "seo_description": "Pesca de dourado em Itatí e no Alto Paraná, Corrientes: dois lodges com o que cada operador publica, pesca e devolução, licença, defesos de 2026 e como chegar.",
    "reglamento_resumen": "Para pescar em Corrientes é preciso licença de pesca esportiva, tirada online no portal de Recursos Naturais. A Disposição 853/22 (Diário Oficial, 26/12/2022, vigente até novo ato) fixa que dourado e surubim são somente pesca e devolução e que pacu (45 cm, limite 1 por licença e um único exemplar por embarcação), patí (70 cm, limite 2), boga (45 cm, limite 2 por pescador e até 6 por embarcação) e traíra (30 cm, limite 5) têm tamanho mínimo e limite. O que for retido só pode circular dentro da localidade onde foi pescado. A província pode decretar defesos extraordinários por trecho do Paraná: em 2026 houve, segundo a imprensa, e foram suspensos em 29 de setembro. Antes de sair, consulte os vigentes com Recursos Naturais.",
    "temporada": [
     {
      "meses": "Suindá (Nervous Waters)",
      "que": "A página diz «outubro a maio» no cabeçalho e «Season: October 2026 – May 2027» nas tarifas; o texto descreve a primavera (do fim de setembro a dezembro, antes da desova de dezembro) e o verão (de janeiro a abril)."
     },
     {
      "meses": "Itatí Lodge (SET Fly Fishing)",
      "que": "A página diz «setembro a maio (evitar janeiro e fevereiro pelo calor)». Em um artigo de outubro de 2024, a SET aponta de setembro a novembro como o melhor para o dourado, antes da desova, e fevereiro e março para pacu e pirapitá."
     },
     {
      "meses": "Entre as duas fontes",
      "que": "Divergem no começo (setembro, fim de setembro ou outubro) e em janeiro e fevereiro: o Suindá os inclui na temporada e a SET os desaconselha pelo calor. Confirme com o operador."
     },
     {
      "meses": "Antes de cada saída",
      "que": "Consulte os defesos vigentes com Recursos Naturales ou com o seu guia."
     },
     {
      "meses": "Calendario por especie y mes",
      "que": "PENDIENTE: confirmar con una fuente técnica o con guías locales."
     }
    ],
    "aguas": [
     {
      "nombre": "Rio Paraná (Alto Paraná) em frente a Itatí",
      "nota": "Segundo o relatório COMIP–UNNE, Itatí fica no km 1279. Consulte os defesos vigentes antes de pescar aqui.",
      "lat": "",
      "lon": ""
     }
    ],
    "faqs": [
     {
      "pregunta": "Quando convém ir pescar dourado em Itatí?",
      "respuesta": "Os operadores divergem. O Suindá diz «outubro a maio» (tarifas de outubro de 2026 a maio de 2027) e descreve a desova em dezembro; o Itatí Lodge diz «setembro a maio» e recomenda evitar janeiro e fevereiro pelo calor, e em um artigo de outubro de 2024 a SET aponta de setembro a novembro como o melhor para o dourado. Páginas lidas em 13/11/2026; confirme com o operador."
     },
     {
      "pregunta": "Quanto custa uma viagem a um lodge de Itatí?",
      "respuesta": "O Suindá publica na sua página, para 2026-27, por pessoa em quarto duplo e com guia compartilhado: US$ 7.950 por 7 noites e 6 dias de pesca, US$ 7.595 por 6 noites, US$ 6.495 por 5, US$ 5.350 por 4 e US$ 4.050 por 3 noites (lida em 13/11/2026). O Itatí Lodge não publica tarifa na página: consulte. Comparação com outros lodges: nossa nota sobre quanto custa uma viagem de pesca de dourado."
     },
     {
      "pregunta": "O que a tarifa do Suindá inclui e não inclui?",
      "respuesta": "Segundo a página: inclui hospedagem em quarto duplo, todas as refeições, vinhos, cerveja e bebidas locais e internacionais, guia profissional e equipamento de empréstimo (varas, carretilhas e linhas). Não inclui passagens aéreas, traslados de e para o lodge, licenças de pesca, lavanderia nem gorjetas. A página do Itatí Lodge não publica o que está incluído."
     },
     {
      "pregunta": "É preciso licença de pesca e o lodge a inclui?",
      "respuesta": "É preciso a licença de Corrientes (para quem mora fora da Argentina, a de turista estrangeiro, calculada em litros de gasolina comum). O Suindá a lista como não incluída e a Nervous Waters diz que pede os dados para tirá-la antes da chegada; a página do Itatí Lodge não fala de licença: pergunte antes de reservar."
     },
     {
      "pregunta": "Dá para pescar com spinning ou só com fly?",
      "respuesta": "O Suindá diz que também permite spinning e baitcasting, e a Nervous Waters diz que seus lodges de dourado são de fly e de spinning. A página do Itatí Lodge descreve equipamento de fly (varas #7 e #8, linhas tropicais): pergunte se oferece spinning."
     },
     {
      "pregunta": "Posso ficar com o dourado que pescar?",
      "respuesta": "Não. Segundo a Disposição 853/22 de Recursos Naturais (Diário Oficial, 26/12/2022), dourado e surubim são somente pesca e devolução em Corrientes."
     },
     {
      "pregunta": "Como chego a Itatí desde Buenos Aires?",
      "respuesta": "Segundo os operadores: voo de uma hora do Aeroparque a Corrientes (CNQ) ou Resistencia (RES). O Itatí Lodge fica a 40 minutos do aeroporto de Corrientes; para o Suindá são 45 minutos de carro desde Corrientes (cerca de duas horas desde Resistencia) até a margem e um traslado curto de lancha."
     },
     {
      "pregunta": "Há acréscimo para viajar sozinho e tarifa para acompanhantes que não pescam?",
      "respuesta": "O Suindá, na página de 2026-27: quarto e lancha individuais com 60% de acréscimo, e acompanhante que não pesca a US$ 650 por noite por pessoa, conforme a disponibilidade (lida em 13/11/2026). O Itatí Lodge não publica essas condições."
     },
     {
      "pregunta": "Como se paga a reserva?",
      "respuesta": "Cada operador diz a sua. Nervous Waters, na página de perguntas frequentes: confirmadas as datas, envia uma fatura eletrônica; para confirmar a reserva é preciso um depósito de 50% nas duas semanas seguintes, e a viagem é paga por completo 120 dias antes da chegada (aceita transferência, cheque e cartão). SET Fly Fishing, na página de informações da viagem (para todos os seus pacotes na Argentina): um depósito de 50% por pescador, não reembolsável, em até 15 dias da reserva, e o pagamento total 90 dias antes do início. Todos os pagamentos são não reembolsáveis, mas podem ser transferidos para outro cliente nas mesmas datas, e recomenda um seguro de viagem."
     },
     {
      "pregunta": "Houve defesos em 2026 perto de Itatí?",
      "respuesta": "Segundo a imprensa (Perfil), houve defesos extraordinários no Paraná em setembro, em dois trechos (km 1109 a 1150 e km 1180 a 1239), e a Disposição 1054 os suspendeu em 29/9/2026. O relatório COMIP–UNNE situa Itatí no km 1279. Consulte os vigentes com Recursos Naturales antes de sair."
     }
    ]
   },
   "fuentes": [
    [
     "Diário Oficial de Corrientes: Disposição 853/22 (tamanhos e limites)",
     "https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf",
     1
    ],
    [
     "Recursos Naturales de Corrientes",
     "https://www.recnaturalesctes.gob.ar/",
     1
    ],
    [
     "Ministério do Turismo de Corrientes: trâmites e permissões (licença de pesca)",
     "https://corrientes.tur.ar/tramites-y-permisos/",
     1
    ],
    [
     "Ministério do Turismo de Corrientes: acordo regional de licenças (alcance)",
     "https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/",
     1
    ],
    [
     "Perfil: defeso dos km 1232 a 1238 suspenso, Disposição 639/26 (4/6/2026, em espanhol)",
     "https://www.perfil.com/noticias/nea/tras-la-dispersion-de-los-cardumenes-de-surubi-corrientes-levanto-la-veda-extraordinaria-en-el-parana.phtml",
     0
    ],
    [
     "Perfil: defeso extraordinário entre os km 1109 e 1150 por uma concentração de dourados (10/9/2026, em espanhol)",
     "https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml",
     0
    ],
    [
     "Perfil: defeso em um novo trecho, km 1180 a 1239 (16/9/2026, em espanhol)",
     "https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml",
     0
    ],
    [
     "Perfil: defeso extraordinário suspenso, Disposição 1054 (29/9/2026, em espanhol)",
     "https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml",
     0
    ],
    [
     "Relatório COMIP–UNNE: Evaluación de la pesca recreativa en el río Paraná en tramos compartidos entre Argentina y Paraguay (fev. 2019 – jan. 2020, em espanhol)",
     "https://comip.org.ar/wp-content/uploads/2023/08/INFORME-FINAL-Pesca-Recreativa-FEBRERO-2019-%E2%80%93-ENERO-2020-COMIP-UNNE.pdf",
     0
    ],
    [
     "Nervous Waters: Suindá Lodge (operador; tarifas 2026-27, lida em 13/11/2026)",
     "https://www.nervouswaters.com/the-lodges/suinda-lodge/",
     0
    ],
    [
     "Nervous Waters: perguntas frequentes (pagamento, licença, spinning)",
     "https://www.nervouswaters.com/faqs/",
     0
    ],
    [
     "SET Fly Fishing: Itatí Lodge (operador)",
     "https://setflyfishing.com/en/itati-lodge/",
     0
    ],
    [
     "SET Fly Fishing: informações da viagem (aeroportos, depósito e pagamento)",
     "https://setflyfishing.com/en/trip-information-package/",
     0
    ],
    [
     "SET Fly Fishing: temporadas de pesca com mosca na Argentina (outubro de 2024)",
     "https://setflyfishing.com/en/argentinas-fly-fishing-seasons-when-and-where-to-catch-the-prize/",
     0
    ]
   ],
   "autoridad": "Dirección de Recursos Naturales de Corrientes"
  }
 },
 "fichas": [
  {
   "nombre": "Suindá Lodge (Nervous Waters)",
   "campos": {
    "tipo": [
     "alojamiento",
     "operador"
    ],
    "localidad": "Alto Paraná, margen argentina, cerca de Itatí (Corrientes)",
    "descripcion": "Lodge de madera de Nervous Waters sobre un barranco de la margen sur (argentina) del Alto Paraná, lo más cerca del pueblo ribereño de Itatí, según su página. Recibe hasta 10 pescadores en dos cabañas de dos dormitorios y dos habitaciones frente al río. Se pesca en lanchas de 21 pies pensadas para dos pescadores, desde la lancha o vadeando bancos de arena. Busca dorado y, como complemento, pacú y pirá pitá; con mosca, y el operador dice que también permite spinning y baitcasting. Presta cañas, reeles y líneas. Dice que sus dorados promedian de 5 a 20 libras y que hay peces de más de 30. Es un grupo que dice operar sus propios lodges.",
    "descripcion_en": "Wooden lodge of Nervous Waters on a bluff of the southern (Argentine) bank of the Upper Paraná, closest to the river town of Itatí, according to its page. It takes up to 10 anglers in two two-bedroom cabins and two rooms facing the river. Fishing is from 21-foot boats designed for two anglers, from the boat or wading sandbars. It targets dorado and, as a complement, pacú and pirá pitá; on the fly, and the operator says it also allows spinning and baitcasting. It lends rods, reels and lines. It says its dorado average 5 to 20 pounds and that there are fish over 30. It is a group that says it operates its own lodges.",
    "descripcion_pt": "Lodge de madeira da Nervous Waters em um barranco da margem sul (argentina) do Alto Paraná, o mais perto do povoado ribeirinho de Itatí, segundo a página. Recebe até 10 pescadores em duas cabanas de dois quartos e dois quartos de frente para o rio. Pesca-se em lanchas de 21 pés pensadas para dois pescadores, da lancha ou caminhando em bancos de areia. Busca dourado e, como complemento, pacu e pirapitá; com fly, e o operador diz que também permite spinning e baitcasting. Empresta varas, carretilhas e linhas. Diz que seus dourados têm em média de 5 a 20 libras e que há peixes de mais de 30. É um grupo que diz operar os próprios lodges.",
    "prest_modalidades": [
     "mosca",
     "spinning"
    ],
    "prest_especies": [
     "dorado",
     "pacu"
    ],
    "incluye": [
     "alojamiento",
     "comida",
     "bebidas",
     "equipos"
    ],
    "prest_temporada": "Octubre a mayo (encabezado de su página); «October 2026 – May 2027» en las tarifas",
    "prest_temporada_en": "October to May (header of its page); “October 2026 – May 2027” in the rates",
    "prest_temporada_pt": "Outubro a maio (cabeçalho da página); «October 2026 – May 2027» nas tarifas",
    "prest_programa": "Estadías de 3 a 7 noches, con 2 a 6 días de pesca con guía compartido (según su tarifa 2026-27)",
    "prest_programa_en": "Stays of 3 to 7 nights, with 2 to 6 fishing days with a shared guide (according to its 2026-27 rates)",
    "prest_programa_pt": "Estadias de 3 a 7 noites, com 2 a 6 dias de pesca com guia compartilhado (segundo a tarifa 2026-27)",
    "prest_estadia_minima": "Su tarifa empieza en 3 noches y 2 días de pesca",
    "prest_estadia_minima_en": "Its rates start at 3 nights and 2 fishing days",
    "prest_estadia_minima_pt": "A tarifa começa em 3 noites e 2 dias de pesca",
    "prest_acompanante": "US$ 650 por noche por persona, según disponibilidad",
    "prest_acompanante_en": "US$ 650 per night per person, depending on availability",
    "prest_acompanante_pt": "US$ 650 por noite por pessoa, conforme a disponibilidade",
    "prest_no_incluye": "Pasajes aéreos internacionales y nacionales, traslados desde y hacia el lodge, licencias de pesca, lavandería y propinas para guías y personal",
    "prest_no_incluye_en": "International and domestic airfares, transfers in and out of the lodge, fishing licences, laundry and gratuities for guides and house staff",
    "prest_no_incluye_pt": "Passagens aéreas internacionais e nacionais, traslados de e para o lodge, licenças de pesca, lavanderia e gorjetas para guias e equipe",
    "capacidad": 10,
    "prest_capacidad_unidad": "total",
    "precio": "US$ 7.950 por persona por 7 noches y 6 días de pesca, en habitación doble y con guía compartido; US$ 7.595 por 6 noches, US$ 6.495 por 5, US$ 5.350 por 4 y US$ 4.050 por 3 noches. Tarifa 2026-27 de la página de Nervous Waters, el grupo que opera el lodge (temporada «octubre de 2026 a mayo de 2027»), leída el 13/11/2026. Habitación y lancha individuales: 60 % de recargo. Día completo de pesca el día de llegada o salida: US$ 950 por lancha; medio día: US$ 750",
    "precio_en": "US$ 7,950 per person for 7 nights and 6 fishing days, in a double room and with a shared guide; US$ 7,595 for 6 nights, US$ 6,495 for 5, US$ 5,350 for 4 and US$ 4,050 for 3 nights. 2026-27 rate from the page of Nervous Waters, the group that operates the lodge (season “October 2026 to May 2027”), read on 13/11/2026. Single room and boat: 60% surcharge. Full fishing day on arrival or departure day: US$ 950 per boat; half day: US$ 750",
    "precio_pt": "US$ 7.950 por pessoa por 7 noites e 6 dias de pesca, em quarto duplo e com guia compartilhado; US$ 7.595 por 6 noites, US$ 6.495 por 5, US$ 5.350 por 4 e US$ 4.050 por 3 noites. Tarifa 2026-27 da página da Nervous Waters, o grupo que opera o lodge (temporada «outubro de 2026 a maio de 2027»), lida em 13/11/2026. Quarto e lancha individuais: 60% de acréscimo. Dia completo de pesca no dia de chegada ou saída: US$ 950 por lancha; meio dia: US$ 750",
    "precio_fecha": "20261113",
    "prest_fuente": "https://www.nervouswaters.com/the-lodges/suinda-lodge/",
    "web": "https://www.nervouswaters.com/the-lodges/suinda-lodge/",
    "notas": "Listado con información pública (página del operador leída el 13/11/2026 con investigar.yml, rama investigacion, carpeta corrientes-1-20261113-2308, archivo 02; y preguntas frecuentes en corrientes-2-20261113-2313, archivo 03). Tarifa y condiciones: solo las de la página de Nervous Waters, que dice operar sus lodges («operating our lodges under our own ownership»); si se la prefiere tratar como intermediaria (docs/prestadores.md, estrategia de listados, punto 5), bajar precio y acompañante. El texto de la página también habla de «late Sept.» y de primavera desde fines de septiembre; la tarifa dice octubre de 2026 a mayo de 2027. Licencia: «Fishing Licenses» figura como no incluida; las preguntas frecuentes de Nervous Waters dicen que piden los datos para sacarla antes de la llegada. No se publican cifras de agencias (Farbank y otras): están solo en las notas de precios, sin nombre. Sin contacto. Sin foto. Pendiente: confirmar con el operador."
   }
  },
  {
   "nombre": "Itatí Lodge (SET Fly Fishing)",
   "campos": {
    "tipo": [
     "alojamiento",
     "operador"
    ],
    "localidad": "Itatí (Corrientes), Alto Paraná",
    "descripcion": "Lodge de SET Fly Fishing sobre el Alto Paraná, en Itatí. Según su página, tiene 5 habitaciones para 10 pescadores en un edificio elevado, a 50 yardas del muelle, con comedor, happy hour diario y chamamé junto a la pileta. Se pesca en skiffs con plataforma completa y motor eléctrico Minn Kota, con cañas #7 y #8 y líneas tropicales; el operador busca dorados grandes (12 a 30 libras o más) y ofrece pacú y pirá pitá como complemento. Está a 40 minutos del aeropuerto de Corrientes. Su página no muestra tarifa.",
    "descripcion_en": "Lodge of SET Fly Fishing on the Upper Paraná, in Itatí. According to its page, it has 5 rooms for 10 anglers in an elevated building, 50 yards from the dock, with a dining room, a daily happy hour and chamamé music by the pool. Fishing is from skiffs with full platforms and Minn Kota electric motors, with #7 and #8 rods and tropical lines; the operator targets big dorado (12 to 30 pounds or more) and offers pacú and pirá pitá as a complement. It is 40 minutes from Corrientes airport. Its page shows no rate.",
    "descripcion_pt": "Lodge da SET Fly Fishing no Alto Paraná, em Itatí. Segundo a página, tem 5 quartos para 10 pescadores em um prédio elevado, a 50 jardas do cais, com sala de jantar, happy hour diário e chamamé junto à piscina. Pesca-se em skiffs com plataforma completa e motor elétrico Minn Kota, com varas #7 e #8 e linhas tropicais; o operador busca dourados grandes (12 a 30 libras ou mais) e oferece pacu e pirapitá como complemento. Fica a 40 minutos do aeroporto de Corrientes. A página não mostra tarifa.",
    "prest_modalidades": [
     "mosca"
    ],
    "prest_especies": [
     "dorado",
     "pacu"
    ],
    "prest_temporada": "Septiembre a mayo; recomienda evitar enero y febrero por el calor (según su página)",
    "prest_temporada_en": "September to May; it advises avoiding January and February for the heat (according to its page)",
    "prest_temporada_pt": "Setembro a maio; recomenda evitar janeiro e fevereiro pelo calor (segundo a página)",
    "prest_programa": "Semanas completas y estadías cortas de 4 noches y 3 días (según su página)",
    "prest_programa_en": "Full weeks and short stays of 4 nights and 3 days (according to its page)",
    "prest_programa_pt": "Semanas completas e estadias curtas de 4 noites e 3 dias (segundo a página)",
    "prest_estadia_minima": "Estadía corta de 4 noches y 3 días; no publica una más corta",
    "prest_estadia_minima_en": "Short stay of 4 nights and 3 days; it publishes no shorter one",
    "prest_estadia_minima_pt": "Estadia curta de 4 noites e 3 dias; não publica uma mais curta",
    "capacidad": 10,
    "prest_capacidad_unidad": "total",
    "precio": "",
    "precio_fecha": "",
    "prest_fuente": "https://setflyfishing.com/en/itati-lodge/",
    "web": "https://setflyfishing.com/en/itati-lodge/",
    "notas": "Listado con información pública (página del operador leída el 13/11/2026 con investigar.yml, rama investigacion, carpeta corrientes-3-20261113-2316, archivo 01; aeropuertos, depósito y pago en el archivo 03; estaciones en el archivo 06). Dirección que da SET en su página de información: Paraje Guayu S/N, Itatí (no se publica el domicilio). La página del operador NO publica tarifa: «a consultar». Que la licencia, los vuelos chárter y los traslados estén incluidos sale solo de una agencia: NO se publica. Depósito del 50 % no reembolsable a los 15 días de la reserva y pago total 90 días antes: de la página de información de SET, para todos sus paquetes de la Argentina (en la FAQ de la guía). Sin contacto. Sin foto. Pendiente: tarifa, qué incluye y programas de días de semana (preguntar al operador)."
   }
  }
 ]
}
JSON
, true );

if ( ! is_array( $D ) ) {
	WP_CLI::error( 'El JSON de datos no se pudo leer: ' . json_last_error_msg() );
}
$clave  = $D['clave'];
$orden  = [ 'es', 'en', 'pt' ];

// --- Guards: SEO lengths (title <= 60, description 70-160) and unresolved markers ------------------------------------------------------
foreach ( $orden as $i ) {
	$c = $D['guias'][ $i ]['campos'];
	$tl = mb_strlen( $c['seo_title'] );
	$dl = mb_strlen( $c['seo_description'] );
	WP_CLI::log( "SEO ({$i}): title {$tl}, description {$dl}" );
	if ( $tl > 60 || $dl < 70 || $dl > 160 ) {
		WP_CLI::error( "SEO fuera de rango ({$i}): title {$tl}, description {$dl}" );
	}
	if ( count( $c['faqs'] ) < 8 || count( $c['faqs'] ) > 10 ) {
		WP_CLI::error( "FAQs fuera de rango ({$i}): " . count( $c['faqs'] ) );
	}
}
// The visible text of every language must carry no agency figure, and no uppercase PENDIENTE outside the hidden notes.
foreach ( $orden as $i ) {
	$g = $D['guias'][ $i ];
	$visible = wp_json_encode( [ $g['campos'], array_filter( $g['cuerpo'], fn( $b ) => 'pend' !== $b[0] ) ], JSON_UNESCAPED_UNICODE );
	if ( preg_match( PM_CG_PROHIBIDO, $visible, $m ) ) {
		WP_CLI::error( "cifra o nombre de agencia en el texto público ({$i}): {$m[0]}" );
	}
}
foreach ( $D['fichas'] as $f ) {
	$visible = wp_json_encode( array_diff_key( $f['campos'], [ 'notas' => 1 ] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( preg_match( PM_CG_PROHIBIDO, $visible, $m ) ) {
		WP_CLI::error( "cifra o nombre de agencia en la ficha «{$f['nombre']}»: {$m[0]}" );
	}
	if ( preg_match( '/@|https?:\/\/|\+\d{2}[ \d]{6,}/u', wp_json_encode( array_diff_key( $f['campos'], [ 'web' => 1, 'prest_fuente' => 1, 'notas' => 1 ] ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) ) {
		WP_CLI::error( "contacto o enlace en un texto público de la ficha «{$f['nombre']}»" );
	}
}

// --- 1. Cards -----------------------------------------------------------------------------------------------------------------------------
$plan_fichas = [];
foreach ( $D['fichas'] as $f ) {
	$nombre = $f['nombre'];
	$campos = $f['campos'] + [ 'prest_destino' => $clave, 'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado', 'verificado' => $leido ];
	$pid    = pm_cg_ficha_id( $nombre );
	if ( ! $pid ) {
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» NUEVA, campos:" );
		foreach ( $campos as $k => $v ) {
			WP_CLI::log( "  + {$k}: " . pm_cg_ver( $v ) );
		}
		$plan_fichas[ $nombre ] = [ 0, $campos ];
		continue;
	}
	// Exists: only fields that are empty today are filled; nothing an editor wrote is replaced.
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» #{$pid} ya existe: solo se llenan los campos vacíos" );
	$cambios = [];
	foreach ( $campos as $k => $nuevo ) {
		$antes = pm_campo( $k, $pid );
		$vacio = '' === pm_cg_norm( $k, $antes ) || ( is_array( $antes ) && ! $antes );
		if ( $vacio && '' !== pm_cg_norm( $k, $nuevo ) ) {
			WP_CLI::log( "  ~ {$k}: vacío → " . pm_cg_ver( $nuevo ) );
			$cambios[ $k ] = $nuevo;
		} else {
			WP_CLI::log( "  = {$k} se deja (" . ( pm_cg_norm( $k, $antes ) === pm_cg_norm( $k, $nuevo ) ? 'igual' : 'ya tiene otro valor' ) . ')' );
		}
	}
	$plan_fichas[ $nombre ] = [ $pid, $cambios ];
}

// --- 2. Guides (es, then en and pt with the same clave) ------------------------------------------------------------------------------------
$plan = [];
foreach ( $orden as $i ) {
	$g  = $D['guias'][ $i ];
	$ya = pm_cg_guia( $clave, $i );
	$cuerpo = pm_cg_bloques( pm_cg_resolver_todo( $g['cuerpo'], $i, $clave ) );
	$campos = pm_cg_resolver_todo( $g['campos'], $i, $clave );
	$campos['fuentes'] = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $leido ], $g['fuentes'] );
	$campos += [ 'idioma' => $i, 'clave' => $clave, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'reglamento_autoridad' => $g['autoridad'], 'reglamento_url' => 'https://www.recnaturalesctes.gob.ar/' ];
	if ( 'es' === $i ) {
		$campos += $D['compartido'];
	}
	WP_CLI::log( ( $ya ? "{$clave} ({$i}) ya existe #{$ya->ID}: no toco nada" : ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$clave} ({$i}) se crea /{$g['slug']}/" ) . ' · ' . count( $campos['faqs'] ) . ' preguntas, ' . count( $campos['fuentes'] ) . ' fuentes, ' . count( $campos['temporada'] ) . ' filas de temporada, ' . count( $g['cuerpo'] ) . ' bloques, ' . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' caracteres de cuerpo' );
	if ( ! $ya ) {
		WP_CLI::log( "  titulo: {$g['titulo']} | h1: {$campos['h1']}" );
		WP_CLI::log( "  seo_title: {$campos['seo_title']}" );
		WP_CLI::log( "  seo_description: {$campos['seo_description']}" );
		WP_CLI::log( "  bajada: {$campos['bajada']}" );
		WP_CLI::log( "  epoca: {$campos['epoca']}" );
		WP_CLI::log( '  preguntas: ' . implode( ' | ', array_column( $campos['faqs'], 'pregunta' ) ) );
	}
	$plan[ $i ] = [ 'ya' => $ya, 'titulo' => $g['titulo'], 'slug' => $g['slug'], 'cuerpo' => $cuerpo, 'campos' => $campos ];
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

// --- Apply ----------------------------------------------------------------------------------------------------------------------------
foreach ( $plan_fichas as $nombre => [ $pid, $cambios ] ) {
	if ( ! $pid ) {
		$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
		if ( is_wp_error( $pid ) ) {
			WP_CLI::error( $pid->get_error_message() );
		}
		$plan_fichas[ $nombre ][0] = (int) $pid;
		WP_CLI::log( "✓ ficha «{$nombre}» creada #{$pid}" );
	}
	foreach ( $cambios as $k => $v ) {
		update_field( $k, $v, $pid );
	}
}
$t = time();
foreach ( $orden as $i ) {
	$x = $plan[ $i ];
	if ( $x['ya'] ) {
		continue;
	}
	if ( 'es' !== $i && ! ( $plan['es']['ya'] ?? null ) && ! pm_cg_guia( $clave, 'es' ) ) {
		WP_CLI::error( "No hay guía en español de {$clave}: no creo la de {$i}." );
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $x['titulo'], 'post_name' => $x['slug'], 'post_content' => wp_slash( $x['cuerpo'] ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	foreach ( $x['campos'] as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
	$plan[ $i ]['id'] = (int) $id;
	WP_CLI::log( "✓ {$clave} ({$i}) publicada #{$id}: " . get_permalink( $id ) );
	if ( 'es' === $i ) {
		WP_CLI::warning( 'sin foto: "foto pendiente" (no hay una con crédito); cargala desde el panel (Imagen destacada)' );
	}
}
foreach ( $orden as $i ) {
	if ( $plan[ $i ]['ya'] ) {
		$plan[ $i ]['id'] = $plan[ $i ]['ya']->ID;
	}
}

// --- Read back ---------------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $D['fichas'] as $f ) {
	$nombre = $f['nombre'];
	$pid    = $plan_fichas[ $nombre ][0];
	$esperado = $f['campos'] + [ 'prest_destino' => $clave, 'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0 ];
	foreach ( $esperado as $k => $v ) {
		$b = pm_cg_norm( $k, pm_campo( $k, $pid ) ) === pm_cg_norm( $k, $v );
		if ( ! $b ) {
			$vacio_antes = ! empty( $plan_fichas[ $nombre ][1] ) && ! array_key_exists( $k, $plan_fichas[ $nombre ][1] );
			WP_CLI::log( ( $vacio_antes ? 'DISTINTO (ya tenía otro valor; no se tocó)' : 'FALLÓ' ) . ": «{$nombre}» #{$pid} {$k} = " . pm_cg_ver( pm_campo( $k, $pid ) ) );
			$ok = $ok && $vacio_antes;
			continue;
		}
		WP_CLI::log( "VERIFIED: «{$nombre}» #{$pid} {$k} = " . pm_cg_ver( pm_campo( $k, $pid ) ) );
	}
	$publico = '';
	foreach ( [ '', '_en', '_pt' ] as $s ) {
		foreach ( [ 'descripcion', 'precio', 'prest_temporada', 'prest_programa', 'prest_estadia_minima', 'prest_acompanante', 'prest_no_incluye' ] as $k ) {
			$publico .= ' ' . pm_campo( $k . $s, $pid );
		}
	}
	$limpio = ! preg_match( PM_CG_PROHIBIDO, $publico );
	WP_CLI::log( ( $limpio ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» ningún texto público (es/en/pt) lleva cifras ni nombres de agencia" );
	$ok = $ok && $limpio;
	$sin_fecha = '' === trim( (string) pm_campo( 'precio_fecha', $pid ) );
	$monto_sin_fecha = $sin_fecha && ( '' !== trim( (string) pm_campo( 'precio', $pid ) ) || '' !== trim( (string) pm_campo( 'prest_acompanante', $pid ) ) );
	WP_CLI::log( ( $monto_sin_fecha ? 'FALLÓ' : 'VERIFIED' ) . ": «{$nombre}» precio o acompañante " . ( $sin_fecha ? 'vacíos (a consultar)' : 'con fecha ' . pm_campo( 'precio_fecha', $pid ) ) );
	$ok = $ok && ! $monto_sin_fecha;
}
$lista = pm_prestadores_destino( $clave );
foreach ( $D['fichas'] as $f ) {
	$pid = $plan_fichas[ $f['nombre'] ][0];
	$r   = null;
	foreach ( $lista as $x ) {
		if ( (int) $x['id'] === $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['consentimiento'] && ! $r['acuerdo'];
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": «{$f['nombre']}» sale en la lista de {$clave} como información pública (publico=" . (int) ( $r['publico'] ?? 0 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ') capacidad=' . ( $r['capacidad'] ?? '' ) . ' unidad=' . ( $r['capacidad_unidad'] ?? '' ) . ' precio_fecha=' . ( $r['precio_fecha'] ?? '' ) );
	$ok = $ok && $bien;
}
foreach ( $orden as $i ) {
	$x  = $plan[ $i ];
	$id = $x['id'];
	$p  = get_post( $id );
	$b  = $p && 'publish' === $p->post_status && $p->post_name === $x['slug'] && pm_idioma_de( $id ) === $i && pm_clave_de( $id ) === $clave;
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) #{$id} publicada, slug {$p->post_name}, idioma " . pm_idioma_de( $id ) . ', clave ' . pm_clave_de( $id ) . ' ' . get_permalink( $id ) );
	$ok = $ok && $b;
	if ( $x['ya'] ) {
		continue; // an existing guide is not compared (it may have been edited)
	}
	$esperado = $x['campos'];
	foreach ( $esperado as $k => $v ) {
		if ( isset( PM_CG_REPETIDORES[ $k ] ) ) {
			$leido_ = pm_cg_filas( $k, $id, PM_CG_REPETIDORES[ $k ] );
			$env    = pm_cg_filas_envio( $v, PM_CG_REPETIDORES[ $k ] );
			$bb     = wp_json_encode( $leido_, JSON_UNESCAPED_UNICODE ) === wp_json_encode( $env, JSON_UNESCAPED_UNICODE );
			WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) {$k}: " . count( $leido_ ) . ' filas leídas, ' . count( $env ) . ' enviadas' );
			$ok = $ok && $bb;
			continue;
		}
		if ( 'es' !== $i && array_key_exists( $k, $D['compartido'] ) ) {
			continue;
		}
		$bb = pm_cg_norm( $k, pm_campo( $k, $id ) ) === pm_cg_norm( $k, $v );
		WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) {$k} = " . mb_substr( pm_cg_ver( pm_campo( $k, $id ) ), 0, 140 ) );
		$ok = $ok && $bb;
	}
	// The body as the visitor gets it: the hidden notes are gone, nothing from an agency, every link resolved.
	$html = apply_filters( 'the_content', $p->post_content );
	$sin  = false === strpos( $html, 'PENDIENTE' ) && ! preg_match( PM_CG_PROHIBIDO, $html ) && false === strpos( $html, '{n:' ) && false === strpos( $html, '{viaje}' );
	WP_CLI::log( ( $sin ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} ({$i}) cuerpo publicado sin notas PENDIENTE, sin agencias y sin marcadores (" . mb_strlen( wp_strip_all_tags( $html ) ) . ' caracteres visibles, ' . substr_count( $html, '<a ' ) . ' enlaces, ' . substr_count( $html, '<table' ) . ' tablas)' );
	$ok = $ok && $sin;
}
$es = $plan['es']['id'];
foreach ( $D['compartido'] as $k => $v ) {
	$bb = pm_cg_norm( $k, pm_campo( $k, $es ) ) === pm_cg_norm( $k, $v );
	WP_CLI::log( ( $bb ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$clave} (es) compartido {$k} = " . pm_cg_ver( pm_campo( $k, $es ) ) );
	$ok = $ok && $bb;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
