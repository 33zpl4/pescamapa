<?php
/**
 * Esteros del Iberá (Corrientes), 14/11/2026: NEW destination guide (es, en, pt, published) with three operator cards (Pirá Lodge, La Alondra'i, Uguay Lodge).
 * Owner-approved new place (demand: "golden dorado fishing", docs/estrategia-seo-2026-10-31.md). First-hand pages (branch `investigacion`, read 13/11/2026):
 *   - Pirá, Nervous Waters (corrientes-1-20261113-2308/03): rates 2026-27 identical to Suindá (7,950 / 7,595 / 6,495 / 5,350 / 4,050), "Season: All year round"
 *     in the rates, "September to May" in the header, spawn "typically November through early January"; 7 rooms, up to 12 guests (header: "10 anglers - 14
 *     guests"); 2 anglers per guide/boat; licences, charters and transfers not included; CNQ 3.5 h / RES 4.5 h, 5,000' grass strip, sleeper bus to Mercedes;
 *     payment terms in corrientes-2-20261113-2313/03 (50 % deposit, full payment 120 days before).
 *   - La Alondra'i, SET (corrientes-3-20261113-2316/07, in Spanish: "Desde Septiembre al final de Mayo", three fishing days in the marsh with nights in
 *     camps; NO rate), /02 and /04 (2023 article and Golden Dorado Experience page), /06 (October 2024 season article), /03 (deposit and payment).
 *   - Uguay, page of Pointer Outfitters (corrientes-1-20261113-2308/06): US$ 6,400 "based on single occupancy and shared guide", no year, "Subject to change",
 *     "Check for inaugural 2X1 Rate"; "October to April"; includes licences and government fees; capacity text contradicts itself (4 guests / six rooms): none
 *     published. DECISION FOR THE OWNER: Pointer Outfitters appears as agency in docs/investigacion/corrientes-dorado-lodges-2026-11-13.md; the price is shown
 *     because the task said so, with the date read and "sin año"; to withdraw it, empty precio and precio_fecha of the card.
 * No lodge in Carlos Pellegrini sells dorado per the research: Carlos Pellegrini is not named as a base. Rules as in itati-2026-11-14 (Disposición 853/22 as
 * already published; the 2026 closures were on the Paraná and the text says so; no new regulatory claim; no rule of the reserve is claimed). No agency figure
 * anywhere (the script refuses to run if one appears). Cards: "información pública, sin verificar", no contact data, no photo. Pin: approximate point in the
 * wetlands (the repo has none; unset would pin lat 0 / lon 0): flagged PENDIENTE.
 * Idempotent; dry run first ($aplicar = false). Run with wp-script.yml (script = esteros-del-ibera-2026-11-14).
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
 "clave": "esteros-del-ibera",
 "compartido": {
  "region": "litoral",
  "provincia": "corrientes",
  "lat": -28.5,
  "lon": -57.6,
  "especies": [
   "dorado"
  ]
 },
 "guias": {
  "es": {
   "titulo": "Esteros del Iberá",
   "slug": "pesca-en-los-esteros-del-ibera",
   "cuerpo": [
    [
     "p",
     "Los <strong>Esteros del Iberá</strong> son un humedal de Corrientes de aguas claras, con canales, lagunas y bancos de plantas flotantes. Según la página de Pirá, el lodge está en la Reserva Provincial de los Esteros del Iberá, en 1.300.000 hectáreas de reserva natural, y el río Corriente queda a 20 minutos al sur. Encontramos tres lodges de dorado con página propia en los esteros: <strong>Pirá</strong> (Nervous Waters), <strong>La Alondra’i</strong> (SET Fly Fishing) y <strong>Uguay</strong> (en la reserva privada Guasú-kua; lo ofrece Pointer Outfitters). Abajo, las tarjetas dicen solo lo que cada página publica, con su fecha."
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
       "Zona",
       "Esteros del Iberá (humedal de Corrientes) y río Corriente",
       "Páginas de Pirá y de Uguay (13/11/2026)"
      ],
      [
       "Especie que venden los lodges",
       "Dorado",
       "Páginas de los operadores (13/11/2026)"
      ],
      [
       "Pesca del dorado",
       "Solo pesca y devolución",
       "Disposición 853/22"
      ],
      [
       "Licencia",
       "Licencia de Corrientes; turista extranjero: 16 a 36 litros de nafta súper según los días. Uguay dice que la incluye; Pirá, no",
       "Recursos Naturales; páginas de los operadores"
      ],
      [
       "Tarifa publicada por el operador",
       "Pirá: de US$ 4.050 a US$ 7.950 por persona (2026-27, doble, guía compartido). Uguay: US$ 6.400 por 7 noches en habitación individual, sin año. La Alondra’i: no publica",
       "Páginas de los operadores (13/11/2026)"
      ],
      [
       "Temporada según los operadores",
       "Pirá: todo el año (encabezado: septiembre a mayo). La Alondra’i: septiembre a fines de mayo. Uguay: octubre a abril",
       "Páginas de los operadores (13/11/2026)"
      ],
      [
       "Cómo llegar",
       "Pirá: 3,5 h en auto desde Corrientes o 4,5 h desde Resistencia, pista de pasto propia o bus cama a Mercedes. Uguay: aeropuerto de Corrientes o Posadas",
       "Páginas de Pirá y de Uguay (13/11/2026)"
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
     "p",
     "Los esteros son una reserva: no leímos normas propias de la reserva ni de los campos privados, y las reglas de arriba son las de la provincia. Preguntale al lodge y a Recursos Naturales si algo más rige donde vas a pescar."
    ],
    [
     "pend",
     "normas propias de la Reserva Provincial y del Parque Nacional Iberá y de los campos privados (Guasú-kua): no leídas."
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
     "Qué dice cada operador: <strong>Pirá</strong> no incluye la licencia en la tarifa (la lista como no incluida) y Nervous Waters dice en sus preguntas frecuentes que pide los datos del pescador para sacarla antes de la llegada. <strong>Uguay</strong> dice que incluye las licencias de pesca y las tasas del gobierno. La página de <strong>La Alondra’i</strong> no menciona la licencia: preguntalo antes de reservar."
    ],
    [
     "p",
     "Todos los permisos por región, en pesos y en dólares aproximados: <a href=\"{n:licencias-extranjeros-argentina}\">licencia de pesca para extranjeros en Argentina 2026/27</a>."
    ],
    [
     "pend",
     "precios de la licencia 2026 con fecha (la nota de Brasil da un valor aproximado calculado con el precio de la nafta)."
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
     "Las vedas de 2026 que leímos en la prensa son del río Paraná: no leímos ninguna para los Esteros del Iberá ni para el río Corriente. Confirmalo con Recursos Naturales antes de salir."
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
     "<strong>Pirá</strong>, según su página: vuelos de una hora desde Buenos Aires a Corrientes (CNQ) o Resistencia (RES), y desde ahí 3,5 horas en auto desde Corrientes o 4,5 desde Resistencia; también vuelo chárter a su pista de pasto de 5.000 pies, o bus cama que sale de Buenos Aires a las 21:00 y llega a Mercedes hacia las 6:30, a 1 hora y 15 minutos en auto del lodge. <strong>Uguay</strong>, según la página de Pointer Outfitters: aeropuerto de Corrientes (CNQ) o de Posadas, con alguien esperando para llevarte al lodge, y vuelo chárter a pedido. La página de <strong>La Alondra’i</strong> no dice cómo llegar; la de información de SET nombra Corrientes como aeropuerto preferido y Resistencia como alternativa."
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
     "texto de la Disposición 853/22 en el Boletín Oficial (no se pudo releer el 13/11/2026: el PDF no abrió en el runner; la transcripción es la de las notas ya publicadas); normas propias de la reserva o del parque y de los campos privados (no leídas); precios de la licencia 2026 con fecha; números de las disposiciones de septiembre de 2026; tarifa de La Alondra’i (su página no la publica); año y vigencia de la tarifa de Uguay; capacidad de Uguay (la página se contradice: 4 huéspedes y seis habitaciones); programas de días de semana; confirmación de los tres operadores; coordenadas del pin (las que cargamos son aproximadas, de la zona general de los esteros: confirmar); foto con su crédito (foto pendiente). Carlos Pellegrini no se nombra como base: ningún lodge de allí vende dorado según la investigación del 13/11/2026."
    ]
   ],
   "campos": {
    "h1": "Pesca de dorado en los Esteros del Iberá, Corrientes: lodges, reglas y licencia",
    "bajada": "Tres lodges que encontramos pescan dorado en los Esteros del Iberá y el río Corriente. Qué publica cada página, qué podés llevarte, qué licencia hace falta y cuándo, según cada operador.",
    "modalidades": "Embarcada con guía\nMosca (flycast)\nBaitcasting y spinning (según el lodge)",
    "epoca": "Entre septiembre u octubre y abril o mayo según el operador; Pirá dice que todo el año.",
    "seo_title": "Pesca de dorado en los Esteros del Iberá: lodges y reglas",
    "seo_description": "Pesca de dorado en los Esteros del Iberá, Corrientes: tres lodges con lo que publica cada uno, tarifas con fecha, pesca y devolución, licencia y cómo llegar.",
    "reglamento_resumen": "Para pescar en Corrientes hace falta licencia de pesca deportiva, que se tramita online en el portal de Recursos Naturales. La Disposición 853/22 (Boletín Oficial, 26/12/2022, vigente hasta nuevo acto) fija que dorado y surubí son solo pesca y devolución y que pacú (45 cm, cupo 1 por licencia y un solo ejemplar por embarcación), patí (70 cm, cupo 2), boga (45 cm, cupo 2 por pescador y hasta 6 por embarcación) y tararira (30 cm, cupo 5) tienen talla mínima y cupo. Lo retenido solo puede movilizarse dentro de la localidad donde se pescó. La provincia puede disponer vedas extraordinarias por tramo del Paraná: en 2026 las hubo, según la prensa, y se levantaron el 29 de septiembre. Antes de salir, consultá las vigentes con Recursos Naturales.",
    "temporada": [
     {
      "meses": "Pirá (Nervous Waters)",
      "que": "Su página dice «todo el año» en las tarifas y «our fishing season is year-round» en el texto, y «septiembre a mayo» en el encabezado. Cuenta que el desove es típicamente de noviembre a principios de enero, según el nivel del agua, y que de junio a agosto quedan muchos dorados residentes."
     },
     {
      "meses": "La Alondra’i (SET Fly Fishing)",
      "que": "Su página dice «desde septiembre al final de mayo». En un artículo de octubre de 2024, SET destaca de mediados de febrero a abril y de octubre a Thanksgiving, por la temperatura del agua."
     },
     {
      "meses": "Uguay (Pointer Outfitters)",
      "que": "La página dice «October to April»."
     },
     {
      "meses": "Entre las fuentes",
      "que": "No coinciden: Pirá dice todo el año (y septiembre a mayo en su encabezado), La Alondra’i septiembre a mayo y Uguay octubre a abril. Confirmalo con el operador."
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
      "nombre": "Esteros del Iberá",
      "nota": "Humedal de Corrientes de canales, lagunas y plantas flotantes. Según Pirá, el lodge está en 1.300.000 hectáreas de reserva natural.",
      "lat": "",
      "lon": ""
     },
     {
      "nombre": "Río Corriente",
      "nota": "Según Pirá, empieza a 20 minutos al sur del lodge.",
      "lat": "",
      "lon": ""
     }
    ],
    "faqs": [
     {
      "pregunta": "¿Cuándo conviene ir a pescar dorado a los Esteros del Iberá?",
      "respuesta": "Cada operador dice lo suyo. Pirá dice todo el año en sus tarifas (y de septiembre a mayo en su encabezado) y que el desove es típicamente de noviembre a principios de enero; La Alondra’i, de septiembre a fines de mayo, y en un artículo de 2024 SET destaca de mediados de febrero a abril y de octubre a Thanksgiving; Uguay, de octubre a abril. Páginas leídas el 13/11/2026; confirmalo con el operador."
     },
     {
      "pregunta": "¿Cuánto cuesta un viaje de pesca de dorado al Iberá?",
      "respuesta": "Pirá publica en su página, para 2026-27, por persona en habitación doble y con guía compartido: US$ 7.950 por 7 noches y 6 días de pesca, US$ 7.595 por 6 noches, US$ 6.495 por 5, US$ 5.350 por 4 y US$ 4.050 por 3 noches. Uguay publica US$ 6.400 por persona por 7 noches y 6 días, en habitación individual y con guía compartido, sin año y «sujeto a cambios», con una tarifa inaugural 2X1 a consultar (leída el 13/11/2026). La Alondra’i no publica tarifa: se consulta."
     },
     {
      "pregunta": "¿Qué incluye y qué no incluye?",
      "respuesta": "Pirá, según su página: alojamiento en doble, todas las comidas, vinos, cerveza y bebidas, guía profesional (dos pescadores por guía y lancha) y equipo de préstamo; no incluye pasajes, charters privados, licencias, lavandería, traslados desde y hacia el lodge ni propinas. Uguay, según la página de Pointer Outfitters: alojamiento, comidas y bebidas, guía, vehículos, recibimiento y traslados desde y hacia el aeropuerto, y licencias y tasas del gobierno; no incluye propinas, vuelos, seguro, alquiler de equipo ni moscas."
     },
     {
      "pregunta": "¿Hace falta licencia de pesca y la incluye el lodge?",
      "respuesta": "Hace falta la licencia de Corrientes (para quien vive fuera de la Argentina, la de turista extranjero, que se calcula en litros de nafta súper). Según sus páginas, Uguay la incluye; Pirá la lista como no incluida y Nervous Waters dice que pide los datos para sacarla antes de la llegada; la página de La Alondra’i no habla de licencia: preguntalo antes de reservar."
     },
     {
      "pregunta": "¿Se puede pescar con spinning o solo con mosca?",
      "respuesta": "Nervous Waters dice que sus lodges de dorado son de mosca y de spinning, y la página de Pirá menciona la opción de pescar con spinning. Uguay dice que el programa puede combinar mosca y spinning. SET describe en sus páginas pesca con mosca (patrones de superficie en La Alondra’i): preguntale si ofrece spinning."
     },
     {
      "pregunta": "¿Puedo quedarme con el dorado que pesque?",
      "respuesta": "No. Según la Disposición 853/22 de Recursos Naturales (Boletín Oficial, 26/12/2022), dorado y surubí son solo pesca y devolución en Corrientes."
     },
     {
      "pregunta": "¿Cómo llego a los Esteros del Iberá?",
      "respuesta": "Pirá: vuelo de una hora de Buenos Aires a Corrientes o Resistencia y luego 3,5 horas en auto desde Corrientes o 4,5 desde Resistencia; también vuelo chárter a su pista de pasto, o bus cama a Mercedes y 1 hora y 15 minutos en auto. Uguay: aeropuerto de Corrientes o de Posadas, con recibimiento. La Alondra’i no lo dice en su página."
     },
     {
      "pregunta": "¿Hay recargo por viajar solo y tarifa para acompañantes que no pescan?",
      "respuesta": "Pirá, en su página de 2026-27: habitación y lancha individuales con un 60 % de recargo, y acompañante que no pesca a US$ 650 por noche por persona, según disponibilidad (leída el 13/11/2026). El precio de Uguay que publica su página es el de habitación individual. La Alondra’i no publica estas condiciones."
     },
     {
      "pregunta": "¿Cómo se paga la reserva?",
      "respuesta": "Cada operador dice lo suyo. Nervous Waters, en su página de preguntas frecuentes: una vez confirmadas las fechas manda una factura electrónica; para confirmar la reserva hace falta un depósito del 50 % dentro de las dos semanas siguientes, y el viaje se paga completo 120 días antes de la llegada (acepta transferencia, cheque y tarjeta). SET Fly Fishing, en su página de información del viaje (para todos sus paquetes de la Argentina): un depósito del 50 % por pescador, no reembolsable, dentro de los 15 días de la reserva, y el pago total 90 días antes del inicio. Todos los pagos son no reembolsables, pero se pueden transferir a otro cliente para las mismas fechas, y recomienda un seguro de viaje. La página de Uguay no publica condiciones de pago."
     },
     {
      "pregunta": "¿Cuántos pescadores entran por lodge y por guía?",
      "respuesta": "Pirá: siete habitaciones y hasta 12 huéspedes según el texto de su página (el encabezado dice «10 anglers - 14 guests»), con dos pescadores por guía y lancha. Uguay: un guía por cada dos o tres pescadores; la capacidad de su página se contradice (4 huéspedes como máximo y seis habitaciones): a confirmar. La Alondra’i no publica una capacidad."
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
     "Nervous Waters: Pirá Lodge (operador; tarifas 2026-27, leída el 13/11/2026)",
     "https://www.nervouswaters.com/the-lodges/pira-lodge/",
     0
    ],
    [
     "Nervous Waters: preguntas frecuentes (pago, licencia, spinning)",
     "https://www.nervouswaters.com/faqs/",
     0
    ],
    [
     "SET Fly Fishing: La Alondra’i (operador)",
     "https://setflyfishing.com/en/la-alondrai/",
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
    ],
    [
     "Pointer Outfitters: Uguay Lodge (página donde se ofrece; tarifa sin año, leída el 13/11/2026)",
     "https://pointeroutfitters.com/uguay-lodge/",
     0
    ]
   ],
   "autoridad": "Dirección de Recursos Naturales de Corrientes"
  },
  "en": {
   "titulo": "Iberá Wetlands",
   "slug": "fishing-in-the-ibera-wetlands",
   "cuerpo": [
    [
     "p",
     "The <strong>Iberá Wetlands</strong> (Esteros del Iberá) are a clear-water wetland in Corrientes, with channels, lagoons and floating-plant islands. According to the Pirá page, the lodge is in the Iberá Marshlands Provincial Reserve, in 1,300,000 hectares of nature preserve, and the Corriente River is 20 minutes south. We found three dorado lodges with pages of their own in the wetlands: <strong>Pirá</strong> (Nervous Waters), <strong>La Alondra’i</strong> (SET Fly Fishing) and <strong>Uguay</strong> (in the private Guasú-kua reserve; offered by Pointer Outfitters). Below, the cards say only what each page publishes, with its date."
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
       "Area",
       "Iberá Wetlands (a wetland in Corrientes) and the Corriente River",
       "Pirá and Uguay pages (13/11/2026)"
      ],
      [
       "Species the lodges sell",
       "Dorado",
       "Operators’ pages (13/11/2026)"
      ],
      [
       "Dorado fishing",
       "Catch and release only",
       "Disposición 853/22"
      ],
      [
       "Licence",
       "Corrientes licence; foreign tourist: 16 to 36 litres of súper petrol depending on the days. Uguay says it includes it; Pirá does not",
       "Recursos Naturales; operators’ pages"
      ],
      [
       "Rate published by the operator",
       "Pirá: US$ 4,050 to US$ 7,950 per person (2026-27, double room, shared guide). Uguay: US$ 6,400 for 7 nights in a single room, no year. La Alondra’i: none published",
       "Operators’ pages (13/11/2026)"
      ],
      [
       "Season according to the operators",
       "Pirá: year-round (header: September to May). La Alondra’i: September to late May. Uguay: October to April",
       "Operators’ pages (13/11/2026)"
      ],
      [
       "Getting there",
       "Pirá: 3.5 h by car from Corrientes or 4.5 h from Resistencia, its own grass strip or a sleeper bus to Mercedes. Uguay: Corrientes or Posadas airport",
       "Pirá and Uguay pages (13/11/2026)"
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
     "p",
     "The wetlands are a reserve: we have not read rules of the reserve itself or of the private estates, and the rules above are the province’s. Ask the lodge and Recursos Naturales whether anything else applies where you will fish."
    ],
    [
     "pend",
     "normas propias de la Reserva Provincial y del Parque Nacional Iberá y de los campos privados (Guasú-kua): no leídas."
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
     "What each operator says: <strong>Pirá</strong> does not include the licence in the rate (it lists it as not included) and Nervous Waters says in its frequently asked questions that it asks for the angler’s details to get it before arrival. <strong>Uguay</strong> says it includes fishing licences and government fees. The <strong>La Alondra’i</strong> page does not mention the licence: ask before you book."
    ],
    [
     "p",
     "All permits by region, in pesos and approximate dollars: <a href=\"{n:licencias-extranjeros-argentina}\">fishing license for foreigners in Argentina 2026/27</a>."
    ],
    [
     "pend",
     "precios de la licencia 2026 con fecha (la nota de Brasil da un valor aproximado calculado con el precio de la nafta)."
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
     "The 2026 closures we read about in the press are on the Paraná River: we read none for the Iberá Wetlands or the Corriente River. Confirm with Recursos Naturales before you go."
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
     "<strong>Pirá</strong>, according to its page: one-hour flights from Buenos Aires to Corrientes (CNQ) or Resistencia (RES), then 3.5 hours by car from Corrientes or 4.5 from Resistencia; also a charter flight to its 5,000-foot grass strip, or a sleeper bus that leaves Buenos Aires at 9:00 p.m. and reaches Mercedes at about 6:30 a.m., 1 hour 15 minutes by car from the lodge. <strong>Uguay</strong>, according to the Pointer Outfitters page: Corrientes (CNQ) or Posadas airport, with someone waiting to drive you to the lodge, and a private charter on request. The <strong>La Alondra’i</strong> page does not say how to get there; SET’s information page names Corrientes as the preferred airport and Resistencia as the alternative."
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
     "texto de la Disposición 853/22 en el Boletín Oficial (no se pudo releer el 13/11/2026: el PDF no abrió en el runner; la transcripción es la de las notas ya publicadas); normas propias de la reserva o del parque y de los campos privados (no leídas); precios de la licencia 2026 con fecha; números de las disposiciones de septiembre de 2026; tarifa de La Alondra’i (su página no la publica); año y vigencia de la tarifa de Uguay; capacidad de Uguay (la página se contradice: 4 huéspedes y seis habitaciones); programas de días de semana; confirmación de los tres operadores; coordenadas del pin (las que cargamos son aproximadas, de la zona general de los esteros: confirmar); foto con su crédito (foto pendiente). Carlos Pellegrini no se nombra como base: ningún lodge de allí vende dorado según la investigación del 13/11/2026."
    ]
   ],
   "campos": {
    "h1": "Golden dorado fishing in the Iberá Wetlands, Corrientes: lodges, rules and licence",
    "bajada": "Three lodges we found fish dorado in the Iberá Wetlands and the Corriente River. What each page publishes, what you can keep, which licence you need and when, according to each operator.",
    "modalidades": "Guided boat fishing\nFly fishing (flycast)\nBaitcasting and spinning (depending on the lodge)",
    "epoca": "Between September or October and April or May according to the operator; Pirá says year-round.",
    "seo_title": "Golden dorado fishing in the Iberá Wetlands: lodges, rules",
    "seo_description": "Golden dorado fishing in the Iberá Wetlands, Corrientes: three lodges as each publishes them, dated rates, catch and release, licence and how to get there.",
    "reglamento_resumen": "Fishing in Corrientes requires a sport fishing licence, bought online on the Natural Resources portal. Disposición 853/22 (Official Gazette, 26/12/2022, in force until another act replaces it) sets dorado and surubí as catch and release only, and gives minimum sizes and bag limits for pacú (45 cm, 1 per licence and a single fish per boat), patí (70 cm, 2), boga (45 cm, 2 per angler and up to 6 per boat) and tararira (30 cm, 5). Fish you keep may only be moved within the town where they were caught. The province can order extraordinary closures on stretches of the Paraná: in 2026 there were some, according to the press, and they were lifted on 29 September. Before you go, check the current ones with Natural Resources.",
    "temporada": [
     {
      "meses": "Pirá (Nervous Waters)",
      "que": "Its page says “All year round” in the rates and “our fishing season is year-round” in the text, and “September to May” in the header. It says the spawn is typically November through early January, depending on water levels, and that from June to August many dorado remain as resident fish."
     },
     {
      "meses": "La Alondra’i (SET Fly Fishing)",
      "que": "Its page says “Septiembre al final de Mayo” (September to the end of May). In an October 2024 article, SET highlights mid-February to April and October to Thanksgiving, for the water temperature."
     },
     {
      "meses": "Uguay (Pointer Outfitters)",
      "que": "The page says “October to April”."
     },
     {
      "meses": "Between the sources",
      "que": "They differ: Pirá says year-round (and September to May in its header), La Alondra’i September to May and Uguay October to April. Confirm with the operator."
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
      "nombre": "Iberá Wetlands",
      "nota": "A wetland in Corrientes of channels, lagoons and floating plants. According to Pirá, the lodge is in 1,300,000 hectares of nature preserve.",
      "lat": "",
      "lon": ""
     },
     {
      "nombre": "Corriente River",
      "nota": "According to Pirá, it begins 20 minutes south of the lodge.",
      "lat": "",
      "lon": ""
     }
    ],
    "faqs": [
     {
      "pregunta": "When is the best time to fish dorado in the Iberá Wetlands?",
      "respuesta": "Each operator says its own. Pirá says year-round in its rates (and September to May in its header) and that the spawn is typically November through early January; La Alondra’i, September to the end of May, and in a 2024 article SET highlights mid-February to April and October to Thanksgiving; Uguay, October to April. Pages read on 13/11/2026; confirm with the operator."
     },
     {
      "pregunta": "How much does a dorado fishing trip to the Iberá cost?",
      "respuesta": "Pirá publishes on its page, for 2026-27, per person in a double room and with a shared guide: US$ 7,950 for 7 nights and 6 fishing days, US$ 7,595 for 6 nights, US$ 6,495 for 5, US$ 5,350 for 4 and US$ 4,050 for 3 nights. Uguay publishes US$ 6,400 per person for 7 nights and 6 days, in a single room and with a shared guide, with no year and “subject to change”, with an inaugural 2X1 rate to ask about (read on 13/11/2026). La Alondra’i publishes no rate: ask for it."
     },
     {
      "pregunta": "What is included and what is not?",
      "respuesta": "Pirá, according to its page: accommodation based on double occupancy, all meals, wines, beer and drinks, a professional guide (two anglers per guide and boat) and loaner equipment; it does not include airfares, private charters, licences, laundry, transfers in and out of the lodge or gratuities. Uguay, according to the Pointer Outfitters page: accommodation, meals and drinks, a guide, vehicles, airport reception and transfers, and fishing licences and government fees; it does not include gratuities, flights, insurance, tackle rental or flies."
     },
     {
      "pregunta": "Do I need a fishing licence, and does the lodge include it?",
      "respuesta": "You need the Corrientes licence (for people who live outside Argentina, the foreign tourist licence, which is calculated in litres of súper petrol). According to their pages, Uguay includes it; Pirá lists it as not included and Nervous Waters says it asks for your details to get it before arrival; the La Alondra’i page does not mention the licence: ask before you book."
     },
     {
      "pregunta": "Can I fish with spinning gear or only fly?",
      "respuesta": "Nervous Waters says its dorado lodges are both fly and spinning, and the Pirá page mentions the option to spin fish. Uguay says the programme can combine fly and spin casting. SET describes fly fishing on its pages (surface patterns at La Alondra’i): ask whether it offers spinning."
     },
     {
      "pregunta": "Can I keep the dorado I catch?",
      "respuesta": "No. Under Disposición 853/22 of Recursos Naturales (Official Gazette, 26/12/2022), dorado and surubí are catch and release only in Corrientes."
     },
     {
      "pregunta": "How do I get to the Iberá Wetlands?",
      "respuesta": "Pirá: a one-hour flight from Buenos Aires to Corrientes or Resistencia and then 3.5 hours by car from Corrientes or 4.5 from Resistencia; also a charter flight to its grass strip, or a sleeper bus to Mercedes and 1 hour 15 minutes by car. Uguay: Corrientes or Posadas airport, with reception. La Alondra’i does not say on its page."
     },
     {
      "pregunta": "Is there a single supplement, and a rate for companions who do not fish?",
      "respuesta": "Pirá, on its 2026-27 page: a single room and boat carry a 60% surcharge, and a non-angler companion is US$ 650 per night per person, depending on availability (read on 13/11/2026). The Uguay price its page publishes is for a single room. La Alondra’i does not publish these conditions."
     },
     {
      "pregunta": "How is the booking paid?",
      "respuesta": "Each operator says its own. Nervous Waters, on its frequently asked questions page: once the dates are confirmed it sends an electronic invoice; to confirm the reservation a 50% deposit is needed within the next two weeks, and the trip is paid in full 120 days before arrival (it accepts wire transfer, cheque and card). SET Fly Fishing, on its trip information page (for all its Argentine packages): a 50% non-refundable deposit per angler within 15 days of booking, and full payment 90 days before the start. All payments are non-refundable, but they can be transferred to another client for the same dates, and it recommends trip insurance. The Uguay page publishes no payment terms."
     },
     {
      "pregunta": "How many anglers does each lodge take, and per guide?",
      "respuesta": "Pirá: seven rooms and up to 12 guests according to the text of its page (the header says “10 anglers - 14 guests”), with two anglers per guide and boat. Uguay: one guide for every two or three anglers; the capacity on its page contradicts itself (4 guests maximum and six rooms): to be confirmed. La Alondra’i publishes no capacity."
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
     "Nervous Waters: Pirá Lodge (operator; 2026-27 rates, read on 13/11/2026)",
     "https://www.nervouswaters.com/the-lodges/pira-lodge/",
     0
    ],
    [
     "Nervous Waters: frequently asked questions (payment, licence, spinning)",
     "https://www.nervouswaters.com/faqs/",
     0
    ],
    [
     "SET Fly Fishing: La Alondra’i (operator, in Spanish)",
     "https://setflyfishing.com/en/la-alondrai/",
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
    ],
    [
     "Pointer Outfitters: Uguay Lodge (page where it is offered; rate with no year, read on 13/11/2026)",
     "https://pointeroutfitters.com/uguay-lodge/",
     0
    ]
   ],
   "autoridad": "Dirección de Recursos Naturales de Corrientes"
  },
  "pt": {
   "titulo": "Esteros do Iberá",
   "slug": "pesca-nos-esteros-do-ibera",
   "cuerpo": [
    [
     "p",
     "Os <strong>Esteros do Iberá</strong> são um banhado de águas claras em Corrientes, com canais, lagoas e ilhas de plantas flutuantes. Segundo a página do Pirá, o lodge fica na Reserva Provincial dos Esteros do Iberá, em 1.300.000 hectares de reserva natural, e o rio Corriente fica a 20 minutos ao sul. Encontramos três lodges de dourado com página própria nos esteros: <strong>Pirá</strong> (Nervous Waters), <strong>La Alondra’i</strong> (SET Fly Fishing) e <strong>Uguay</strong> (na reserva privada Guasú-kua; oferecido pela Pointer Outfitters). Abaixo, os cartões dizem só o que cada página publica, com a data."
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
       "Zona",
       "Esteros do Iberá (banhado de Corrientes) e rio Corriente",
       "Páginas do Pirá e do Uguay (13/11/2026)"
      ],
      [
       "Espécie que os lodges vendem",
       "Dourado",
       "Páginas dos operadores (13/11/2026)"
      ],
      [
       "Pesca do dourado",
       "Somente pesca e devolução",
       "Disposição 853/22"
      ],
      [
       "Licença",
       "Licença de Corrientes; turista estrangeiro: 16 a 36 litros de gasolina comum conforme os dias. O Uguay diz que a inclui; o Pirá, não",
       "Recursos Naturales; páginas dos operadores"
      ],
      [
       "Tarifa publicada pelo operador",
       "Pirá: de US$ 4.050 a US$ 7.950 por pessoa (2026-27, quarto duplo, guia compartilhado). Uguay: US$ 6.400 por 7 noites em quarto individual, sem ano. La Alondra’i: não publica",
       "Páginas dos operadores (13/11/2026)"
      ],
      [
       "Temporada segundo os operadores",
       "Pirá: o ano todo (cabeçalho: setembro a maio). La Alondra’i: setembro a fim de maio. Uguay: outubro a abril",
       "Páginas dos operadores (13/11/2026)"
      ],
      [
       "Como chegar",
       "Pirá: 3,5 h de carro desde Corrientes ou 4,5 h desde Resistencia, pista de grama própria ou ônibus-leito até Mercedes. Uguay: aeroporto de Corrientes ou Posadas",
       "Páginas do Pirá e do Uguay (13/11/2026)"
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
     "p",
     "Os esteros são uma reserva: não lemos normas próprias da reserva nem das fazendas privadas, e as regras acima são as da província. Pergunte ao lodge e a Recursos Naturales se algo mais vale onde você vai pescar."
    ],
    [
     "pend",
     "normas propias de la Reserva Provincial y del Parque Nacional Iberá y de los campos privados (Guasú-kua): no leídas."
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
     "O que diz cada operador: o <strong>Pirá</strong> não inclui a licença na tarifa (a lista como não incluída) e a Nervous Waters diz nas perguntas frequentes que pede os dados do pescador para tirá-la antes da chegada. O <strong>Uguay</strong> diz que inclui as licenças de pesca e as taxas do governo. A página do <strong>La Alondra’i</strong> não menciona a licença: pergunte antes de reservar."
    ],
    [
     "p",
     "Todas as licenças por região, em pesos e em dólares aproximados: <a href=\"{n:licencias-extranjeros-argentina}\">licença de pesca para estrangeiros na Argentina 2026/27</a>."
    ],
    [
     "pend",
     "precios de la licencia 2026 con fecha (la nota de Brasil da un valor aproximado calculado con el precio de la nafta)."
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
     "Os defesos de 2026 que lemos na imprensa são do rio Paraná: não lemos nenhum para os Esteros do Iberá nem para o rio Corriente. Confirme com Recursos Naturales antes de sair."
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
     "O <strong>Pirá</strong>, segundo a página: voos de uma hora de Buenos Aires a Corrientes (CNQ) ou Resistencia (RES), e dali 3,5 horas de carro desde Corrientes ou 4,5 desde Resistencia; também voo fretado até a pista de grama de 5.000 pés, ou ônibus-leito que sai de Buenos Aires às 21:00 e chega a Mercedes por volta das 6:30, a 1 hora e 15 minutos de carro do lodge. O <strong>Uguay</strong>, segundo a página da Pointer Outfitters: aeroporto de Corrientes (CNQ) ou de Posadas, com alguém esperando para levar ao lodge, e voo fretado sob pedido. A página do <strong>La Alondra’i</strong> não diz como chegar; a de informações da SET cita Corrientes como aeroporto preferido e Resistencia como alternativa."
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
     "texto de la Disposición 853/22 en el Boletín Oficial (no se pudo releer el 13/11/2026: el PDF no abrió en el runner; la transcripción es la de las notas ya publicadas); normas propias de la reserva o del parque y de los campos privados (no leídas); precios de la licencia 2026 con fecha; números de las disposiciones de septiembre de 2026; tarifa de La Alondra’i (su página no la publica); año y vigencia de la tarifa de Uguay; capacidad de Uguay (la página se contradice: 4 huéspedes y seis habitaciones); programas de días de semana; confirmación de los tres operadores; coordenadas del pin (las que cargamos son aproximadas, de la zona general de los esteros: confirmar); foto con su crédito (foto pendiente). Carlos Pellegrini no se nombra como base: ningún lodge de allí vende dorado según la investigación del 13/11/2026."
    ]
   ],
   "campos": {
    "h1": "Pesca de dourado nos Esteros do Iberá, Corrientes: lodges, regras e licença",
    "bajada": "Três lodges que encontramos pescam dourado nos Esteros do Iberá e no rio Corriente. O que cada página publica, o que pode levar, que licença é preciso e quando, segundo cada operador.",
    "modalidades": "Embarcada com guia\nFly (flycast)\nBaitcasting e spinning (conforme o lodge)",
    "epoca": "Entre setembro ou outubro e abril ou maio conforme o operador; o Pirá diz o ano todo.",
    "seo_title": "Pesca de dourado nos Esteros do Iberá: lodges e regras",
    "seo_description": "Pesca de dourado nos Esteros do Iberá, Corrientes: três lodges com o que cada um publica, tarifas com data, pesca e devolução, licença e como chegar.",
    "reglamento_resumen": "Para pescar em Corrientes é preciso licença de pesca esportiva, tirada online no portal de Recursos Naturais. A Disposição 853/22 (Diário Oficial, 26/12/2022, vigente até novo ato) fixa que dourado e surubim são somente pesca e devolução e que pacu (45 cm, limite 1 por licença e um único exemplar por embarcação), patí (70 cm, limite 2), boga (45 cm, limite 2 por pescador e até 6 por embarcação) e traíra (30 cm, limite 5) têm tamanho mínimo e limite. O que for retido só pode circular dentro da localidade onde foi pescado. A província pode decretar defesos extraordinários por trecho do Paraná: em 2026 houve, segundo a imprensa, e foram suspensos em 29 de setembro. Antes de sair, consulte os vigentes com Recursos Naturais.",
    "temporada": [
     {
      "meses": "Pirá (Nervous Waters)",
      "que": "A página diz «o ano todo» nas tarifas e «our fishing season is year-round» no texto, e «setembro a maio» no cabeçalho. Conta que a desova é tipicamente de novembro ao começo de janeiro, conforme o nível da água, e que de junho a agosto ficam muitos dourados residentes."
     },
     {
      "meses": "La Alondra’i (SET Fly Fishing)",
      "que": "A página diz «desde setembro até o fim de maio». Em um artigo de outubro de 2024, a SET destaca de meados de fevereiro a abril e de outubro ao Thanksgiving, pela temperatura da água."
     },
     {
      "meses": "Uguay (Pointer Outfitters)",
      "que": "A página diz «October to April»."
     },
     {
      "meses": "Entre as fontes",
      "que": "Divergem: o Pirá diz o ano todo (e setembro a maio no cabeçalho), o La Alondra’i setembro a maio e o Uguay outubro a abril. Confirme com o operador."
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
      "nombre": "Esteros do Iberá",
      "nota": "Banhado de Corrientes de canais, lagoas e plantas flutuantes. Segundo o Pirá, o lodge fica em 1.300.000 hectares de reserva natural.",
      "lat": "",
      "lon": ""
     },
     {
      "nombre": "Rio Corriente",
      "nota": "Segundo o Pirá, começa a 20 minutos ao sul do lodge.",
      "lat": "",
      "lon": ""
     }
    ],
    "faqs": [
     {
      "pregunta": "Quando convém ir pescar dourado nos Esteros do Iberá?",
      "respuesta": "Cada operador diz a sua. O Pirá diz o ano todo nas tarifas (e setembro a maio no cabeçalho) e que a desova é tipicamente de novembro ao começo de janeiro; o La Alondra’i, de setembro ao fim de maio, e em um artigo de 2024 a SET destaca de meados de fevereiro a abril e de outubro ao Thanksgiving; o Uguay, de outubro a abril. Páginas lidas em 13/11/2026; confirme com o operador."
     },
     {
      "pregunta": "Quanto custa uma viagem de pesca de dourado ao Iberá?",
      "respuesta": "O Pirá publica na página, para 2026-27, por pessoa em quarto duplo e com guia compartilhado: US$ 7.950 por 7 noites e 6 dias de pesca, US$ 7.595 por 6 noites, US$ 6.495 por 5, US$ 5.350 por 4 e US$ 4.050 por 3 noites. O Uguay publica US$ 6.400 por pessoa por 7 noites e 6 dias, em quarto individual e com guia compartilhado, sem ano e «sujeito a alterações», com uma tarifa inaugural 2X1 a consultar (lida em 13/11/2026). O La Alondra’i não publica tarifa: consulte."
     },
     {
      "pregunta": "O que está incluído e o que não está?",
      "respuesta": "O Pirá, segundo a página: hospedagem em quarto duplo, todas as refeições, vinhos, cerveja e bebidas, guia profissional (dois pescadores por guia e lancha) e equipamento de empréstimo; não inclui passagens, charters privados, licenças, lavanderia, traslados de e para o lodge nem gorjetas. O Uguay, segundo a página da Pointer Outfitters: hospedagem, refeições e bebidas, guia, veículos, recepção e traslados de e para o aeroporto, e licenças de pesca e taxas do governo; não inclui gorjetas, voos, seguro, aluguel de equipamento nem moscas."
     },
     {
      "pregunta": "É preciso licença de pesca e o lodge a inclui?",
      "respuesta": "É preciso a licença de Corrientes (para quem mora fora da Argentina, a de turista estrangeiro, calculada em litros de gasolina comum). Segundo as páginas, o Uguay a inclui; o Pirá a lista como não incluída e a Nervous Waters diz que pede os dados para tirá-la antes da chegada; a página do La Alondra’i não fala de licença: pergunte antes de reservar."
     },
     {
      "pregunta": "Dá para pescar com spinning ou só com fly?",
      "respuesta": "A Nervous Waters diz que seus lodges de dourado são de fly e de spinning, e a página do Pirá menciona a opção de pescar com spinning. O Uguay diz que o programa pode combinar fly e spinning. A SET descreve nas páginas pesca com fly (iscas de superfície no La Alondra’i): pergunte se oferece spinning."
     },
     {
      "pregunta": "Posso ficar com o dourado que pescar?",
      "respuesta": "Não. Segundo a Disposição 853/22 de Recursos Naturales (Diário Oficial, 26/12/2022), dourado e surubim são somente pesca e devolução em Corrientes."
     },
     {
      "pregunta": "Como chego aos Esteros do Iberá?",
      "respuesta": "Pirá: voo de uma hora de Buenos Aires a Corrientes ou Resistencia e depois 3,5 horas de carro desde Corrientes ou 4,5 desde Resistencia; também voo fretado até a pista de grama, ou ônibus-leito até Mercedes e 1 hora e 15 minutos de carro. Uguay: aeroporto de Corrientes ou de Posadas, com recepção. O La Alondra’i não diz na página."
     },
     {
      "pregunta": "Há acréscimo para viajar sozinho e tarifa para acompanhantes que não pescam?",
      "respuesta": "O Pirá, na página de 2026-27: quarto e lancha individuais com 60% de acréscimo, e acompanhante que não pesca a US$ 650 por noite por pessoa, conforme a disponibilidade (lida em 13/11/2026). O preço do Uguay que a página publica é o de quarto individual. O La Alondra’i não publica essas condições."
     },
     {
      "pregunta": "Como se paga a reserva?",
      "respuesta": "Cada operador diz a sua. Nervous Waters, na página de perguntas frequentes: confirmadas as datas, envia uma fatura eletrônica; para confirmar a reserva é preciso um depósito de 50% nas duas semanas seguintes, e a viagem é paga por completo 120 dias antes da chegada (aceita transferência, cheque e cartão). SET Fly Fishing, na página de informações da viagem (para todos os seus pacotes na Argentina): um depósito de 50% por pescador, não reembolsável, em até 15 dias da reserva, e o pagamento total 90 dias antes do início. Todos os pagamentos são não reembolsáveis, mas podem ser transferidos para outro cliente nas mesmas datas, e recomenda um seguro de viagem. A página do Uguay não publica condições de pagamento."
     },
     {
      "pregunta": "Quantos pescadores cabem em cada lodge e por guia?",
      "respuesta": "Pirá: sete quartos e até 12 hóspedes segundo o texto da página (o cabeçalho diz «10 anglers - 14 guests»), com dois pescadores por guia e lancha. Uguay: um guia para cada dois ou três pescadores; a capacidade da página se contradiz (4 hóspedes no máximo e seis quartos): a confirmar. O La Alondra’i não publica capacidade."
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
     "Nervous Waters: Pirá Lodge (operador; tarifas 2026-27, lida em 13/11/2026)",
     "https://www.nervouswaters.com/the-lodges/pira-lodge/",
     0
    ],
    [
     "Nervous Waters: perguntas frequentes (pagamento, licença, spinning)",
     "https://www.nervouswaters.com/faqs/",
     0
    ],
    [
     "SET Fly Fishing: La Alondra’i (operador, em espanhol)",
     "https://setflyfishing.com/en/la-alondrai/",
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
    ],
    [
     "Pointer Outfitters: Uguay Lodge (página onde é oferecido; tarifa sem ano, lida em 13/11/2026)",
     "https://pointeroutfitters.com/uguay-lodge/",
     0
    ]
   ],
   "autoridad": "Dirección de Recursos Naturales de Corrientes"
  }
 },
 "fichas": [
  {
   "nombre": "Pirá Lodge (Nervous Waters)",
   "campos": {
    "tipo": [
     "alojamiento",
     "operador"
    ],
    "localidad": "Esteros del Iberá, borde sur, junto al río Corriente (Corrientes)",
    "descripcion": "Lodge de Nervous Waters en el borde sur de los Esteros del Iberá, donde el estero se junta con el río Corriente, que según su página empieza a 20 minutos al sur. Según su página, tiene siete habitaciones y recibe hasta 12 huéspedes (el encabezado dice «10 anglers - 14 guests») y una pista de pasto de 5.000 pies para vuelos chárter. Se pesca desde skiffs, a la proa o vadeando cuando baja el agua, con dos pescadores por guía y lancha; el operador dice que es de mosca y que también se puede pescar con spinning. Dice que en el río Corriente los dorados promedian de 4 a 8 libras, con peces de más de 10 libras con regularidad y de más de 20 cada temporada. Presta cañas, reeles y líneas. Es un grupo que dice operar sus propios lodges.",
    "descripcion_en": "Lodge of Nervous Waters on the southern edge of the Iberá Wetlands, where the marsh meets the Corriente River, which according to its page begins 20 minutes south. According to its page, it has seven rooms and takes up to 12 guests (the header says “10 anglers - 14 guests”) and has a 5,000-foot grass strip for charter flights. Fishing is from skiffs, from the bow or wading when the water drops, with two anglers per guide and boat; the operator says it is fly fishing and that spinning is also possible. It says that on the Corriente River dorado average 4 to 8 pounds, with regular catches of over 10 pounds and fish over 20 each season. It lends rods, reels and lines. It is a group that says it operates its own lodges.",
    "descripcion_pt": "Lodge da Nervous Waters na borda sul dos Esteros do Iberá, onde o banhado encontra o rio Corriente, que segundo a página começa a 20 minutos ao sul. Segundo a página, tem sete quartos e recebe até 12 hóspedes (o cabeçalho diz «10 anglers - 14 guests») e uma pista de grama de 5.000 pés para voos fretados. Pesca-se de skiffs, da proa ou caminhando quando a água baixa, com dois pescadores por guia e lancha; o operador diz que é pesca com fly e que também se pode pescar com spinning. Diz que no rio Corriente os dourados têm em média de 4 a 8 libras, com peixes de mais de 10 libras com regularidade e de mais de 20 a cada temporada. Empresta varas, carretilhas e linhas. É um grupo que diz operar os próprios lodges.",
    "prest_modalidades": [
     "mosca",
     "spinning"
    ],
    "prest_especies": [
     "dorado"
    ],
    "incluye": [
     "alojamiento",
     "comida",
     "bebidas",
     "equipos"
    ],
    "prest_temporada": "Todo el año en las tarifas y en el texto; «septiembre a mayo» en el encabezado de su página",
    "prest_temporada_en": "Year-round in the rates and in the text; “September to May” in the header of its page",
    "prest_temporada_pt": "O ano todo nas tarifas e no texto; «setembro a maio» no cabeçalho da página",
    "prest_programa": "Estadías de 3 a 7 noches, con 2 a 6 días de pesca; dos pescadores por guía y lancha (según su tarifa 2026-27)",
    "prest_programa_en": "Stays of 3 to 7 nights, with 2 to 6 fishing days; two anglers per guide and boat (according to its 2026-27 rates)",
    "prest_programa_pt": "Estadias de 3 a 7 noites, com 2 a 6 dias de pesca; dois pescadores por guia e lancha (segundo a tarifa 2026-27)",
    "prest_estadia_minima": "Su tarifa empieza en 3 noches y 2 días de pesca",
    "prest_estadia_minima_en": "Its rates start at 3 nights and 2 fishing days",
    "prest_estadia_minima_pt": "A tarifa começa em 3 noites e 2 dias de pesca",
    "prest_acompanante": "US$ 650 por noche por persona, según disponibilidad",
    "prest_acompanante_en": "US$ 650 per night per person, depending on availability",
    "prest_acompanante_pt": "US$ 650 por noite por pessoa, conforme a disponibilidade",
    "prest_no_incluye": "Pasajes aéreos internacionales y nacionales, charters privados, licencias de pesca, lavandería, traslados desde y hacia el lodge y propinas para guías y personal",
    "prest_no_incluye_en": "International and domestic airfares, private charters, fishing licences, laundry, transfers in and out of the lodge and gratuities for guides and house staff",
    "prest_no_incluye_pt": "Passagens aéreas internacionais e nacionais, charters privados, licenças de pesca, lavanderia, traslados de e para o lodge e gorjetas para guias e equipe",
    "capacidad": 10,
    "prest_capacidad_unidad": "total",
    "precio": "US$ 7.950 por persona por 7 noches y 6 días de pesca, en habitación doble y con guía compartido; US$ 7.595 por 6 noches, US$ 6.495 por 5, US$ 5.350 por 4 y US$ 4.050 por 3 noches. Tarifa 2026-27 de la página de Nervous Waters, el grupo que opera el lodge (temporada «todo el año»), leída el 13/11/2026. Habitación y lancha individuales: 60 % de recargo. Día completo de pesca el día de llegada o salida: US$ 950 por lancha; medio día: US$ 750",
    "precio_en": "US$ 7,950 per person for 7 nights and 6 fishing days, in a double room and with a shared guide; US$ 7,595 for 6 nights, US$ 6,495 for 5, US$ 5,350 for 4 and US$ 4,050 for 3 nights. 2026-27 rate from the page of Nervous Waters, the group that operates the lodge (season “all year round”), read on 13/11/2026. Single room and boat: 60% surcharge. Full fishing day on arrival or departure day: US$ 950 per boat; half day: US$ 750",
    "precio_pt": "US$ 7.950 por pessoa por 7 noites e 6 dias de pesca, em quarto duplo e com guia compartilhado; US$ 7.595 por 6 noites, US$ 6.495 por 5, US$ 5.350 por 4 e US$ 4.050 por 3 noites. Tarifa 2026-27 da página da Nervous Waters, o grupo que opera o lodge (temporada «o ano todo»), lida em 13/11/2026. Quarto e lancha individuais: 60% de acréscimo. Dia completo de pesca no dia de chegada ou saída: US$ 950 por lancha; meio dia: US$ 750",
    "precio_fecha": "20261113",
    "prest_fuente": "https://www.nervouswaters.com/the-lodges/pira-lodge/",
    "web": "https://www.nervouswaters.com/the-lodges/pira-lodge/",
    "notas": "Listado con información pública (página del operador leída el 13/11/2026 con investigar.yml, rama investigacion: corrientes-1-20261113-2308 archivo 03; preguntas frecuentes en corrientes-2-20261113-2313 archivo 03). Tarifa y condiciones: solo las de la página de Nervous Waters, que dice operar sus lodges; si se la prefiere tratar como intermediaria (docs/prestadores.md, estrategia de listados, punto 5), bajar precio y acompañante. La página se contradice en la temporada (encabezado «September to May»; tarifas «All year round»; texto «year-round») y en la capacidad (encabezado «10 anglers - 14 guests»; texto «up to 12 guests in seven deluxe rooms»): la ficha pone 10 pescadores (encabezado) y cuenta las dos cifras de huéspedes en la descripción. «One angler at a time» en la proa de los skiffs figura en el texto de la pesca, mientras la tarifa dice «2 anglers per guide/boat»: se publica solo lo de la tarifa. Licencia: «Fishing Licenses» figura como no incluida; una agencia (Farbank) da una cifra de US$ 50 por persona aparte: NO se publica. Sin contacto. Sin foto. Pendiente: confirmar con el operador."
   }
  },
  {
   "nombre": "La Alondra’i (SET Fly Fishing)",
   "campos": {
    "tipo": [
     "alojamiento",
     "operador"
    ],
    "localidad": "Concepción (Corrientes), acceso a los Esteros del Iberá",
    "descripcion": "Lodge de SET Fly Fishing en una casa histórica refaccionada de Concepción, desde donde se accede a los Esteros del Iberá. Según su página, el programa son tres días de pesca dentro del estero, con las noches en tres campamentos estables en distintos sectores (todos con baño, ducha y Wi-Fi; algunos con aire acondicionado). SET cuenta en un artículo de 2023 que el lodge está a 20 minutos del estero, que los campamentos solo se alcanzan por el agua, que no hay otros pescadores ni otro operador y que se pesca en superficie; en otra de sus páginas habla de dorados de 7 a 15 libras. Su página menciona también ecoturismo, cabalgatas y clases de cocina, y dice que es ideal para pescadores en solitario o en grupos de amigos y familias. No muestra tarifa.",
    "descripcion_en": "Lodge of SET Fly Fishing in a restored historic house in Concepción, from where the Iberá Wetlands are reached. According to its page, the programme is three fishing days inside the wetlands, with nights in three permanent camps in different sectors (all with a bathroom, shower and Wi-Fi; some with air conditioning). In a 2023 article SET says the lodge is 20 minutes from the wetlands, that the camps can only be reached by water, that there are no other anglers or outfitters and that fishing is on the surface; on another of its pages it speaks of dorado of 7 to 15 pounds. Its page also mentions ecotourism, horseback riding and cooking classes, and says it is ideal for solo anglers or groups of friends and families. It shows no rate.",
    "descripcion_pt": "Lodge da SET Fly Fishing em uma casa histórica reformada em Concepción, de onde se acessa os Esteros do Iberá. Segundo a página, o programa são três dias de pesca dentro do estero, com as noites em três acampamentos fixos em diferentes setores (todos com banheiro, chuveiro e Wi-Fi; alguns com ar-condicionado). A SET conta em um artigo de 2023 que o lodge fica a 20 minutos do estero, que os acampamentos só se alcançam pela água, que não há outros pescadores nem outro operador e que se pesca na superfície; em outra página fala de dourados de 7 a 15 libras. A página menciona também ecoturismo, cavalgadas e aulas de culinária, e diz que é ideal para pescadores solitários ou grupos de amigos e famílias. Não mostra tarifa.",
    "prest_modalidades": [
     "mosca"
    ],
    "prest_especies": [
     "dorado"
    ],
    "prest_temporada": "Desde septiembre hasta fines de mayo (según su página)",
    "prest_temporada_en": "From September to the end of May (according to its page)",
    "prest_temporada_pt": "De setembro ao fim de maio (segundo a página)",
    "prest_programa": "Tres días de pesca dentro del estero, con noches en campamentos (según su página)",
    "prest_programa_en": "Three fishing days inside the wetlands, with nights in camps (according to its page)",
    "prest_programa_pt": "Três dias de pesca dentro do estero, com noites em acampamentos (segundo a página)",
    "precio": "",
    "precio_fecha": "",
    "prest_fuente": "https://setflyfishing.com/en/la-alondrai/",
    "web": "https://setflyfishing.com/en/la-alondrai/",
    "notas": "Listado con información pública (página del operador leída el 13/11/2026 con investigar.yml, rama investigacion: corrientes-3-20261113-2316 archivo 07, en español aunque cuelga de /en/; el artículo de 2023 es el archivo 02, la página de «Golden Dorado Experience» el 04 —«7-15 pound Dorado», «surface patterns»— y las estaciones el 06). La página del operador NO publica tarifa: «a consultar». Dos agencias (Farbank y Orvis) dan tarifas 2026/27 y dicen que incluye licencia: NO se publican. «Concepción» sale de la página de información de SET, que también da una dirección de oficina en Concepción: no se publica. Sin contacto. Sin foto. Pendiente: tarifa, capacidad, qué incluye y confirmar con el operador."
   }
  },
  {
   "nombre": "Uguay Lodge (Pointer Outfitters)",
   "campos": {
    "tipo": [
     "alojamiento",
     "operador"
    ],
    "localidad": "Esteros del Iberá, reserva privada Guasú-kua (Corrientes)",
    "descripcion": "Lodge dentro de la reserva natural privada Guasú-kua («refugio del ciervo» en guaraní), en los Esteros del Iberá, con acceso exclusivo a la laguna Fernández por un canal privado, según la página de Pointer Outfitters, que lo ofrece en su programa de pesca con mosca. Según esa página, el programa es apto para principiantes y puede combinar mosca y spinning, con un guía por cada dos o tres pescadores. Dice que el dorado promedia de 3 a 10 kilos y que el mayor registrado llegó a 31. Su capacidad, a confirmar: la página dice que recibe como máximo cuatro huéspedes y, a la vez, que tiene seis habitaciones.",
    "descripcion_en": "Lodge inside the private Guasú-kua nature reserve (“refuge of the deer” in Guaraní), in the Iberá Wetlands, with exclusive access to Fernández Lagoon through a private channel, according to the Pointer Outfitters page, which offers it in its fly-fishing programme. According to that page, the programme is beginner-friendly and can combine fly and spin casting, with one guide for every two or three anglers. It says the dorado averages 3 to 10 kilograms and that the largest recorded reached 31. Its capacity is to be confirmed: the page says it takes a maximum of four guests and, at the same time, that it has six rooms.",
    "descripcion_pt": "Lodge dentro da reserva natural privada Guasú-kua («refúgio do veado» em guarani), nos Esteros do Iberá, com acesso exclusivo à lagoa Fernández por um canal privado, segundo a página da Pointer Outfitters, que o oferece em seu programa de pesca com fly. Segundo essa página, o programa é adequado para iniciantes e pode combinar fly e spinning, com um guia para cada dois ou três pescadores. Diz que o dourado tem em média de 3 a 10 quilos e que o maior registrado chegou a 31. A capacidade está por confirmar: a página diz que recebe no máximo quatro hóspedes e, ao mesmo tempo, que tem seis quartos.",
    "prest_modalidades": [
     "mosca",
     "spinning"
    ],
    "prest_especies": [
     "dorado"
    ],
    "incluye": [
     "alojamiento",
     "comida",
     "bebidas",
     "traslados",
     "licencia"
    ],
    "prest_temporada": "Octubre a abril (según la página de Pointer Outfitters)",
    "prest_temporada_en": "October to April (according to the Pointer Outfitters page)",
    "prest_temporada_pt": "Outubro a abril (segundo a página da Pointer Outfitters)",
    "prest_programa": "7 noches y 6 días completos de pesca; un guía por cada dos o tres pescadores",
    "prest_programa_en": "7 nights and 6 full fishing days; one guide for every two or three anglers",
    "prest_programa_pt": "7 noites e 6 dias completos de pesca; um guia para cada dois ou três pescadores",
    "prest_no_incluye": "Propinas para guías y personal (se pagan en el lodge con la factura final), vuelos locales e internacionales, seguro, alquiler de equipo y moscas",
    "prest_no_incluye_en": "Gratuities for guides and staff (paid at the lodge with the final invoice), local and international flights, insurance, tackle rental and flies",
    "prest_no_incluye_pt": "Gorjetas para guias e equipe (pagas no lodge com a fatura final), voos locais e internacionais, seguro, aluguel de equipamento e moscas",
    "precio": "US$ 6.400 por persona por 7 noches y 6 días completos de pesca, en habitación individual y con guía compartido, según la página de Pointer Outfitters, leída el 13/11/2026. La página no muestra el año, dice «sujeto a cambios» y menciona una tarifa inaugural 2X1: consultar. Según esa página, incluye las licencias de pesca y las tasas del gobierno",
    "precio_en": "US$ 6,400 per person for 7 nights and 6 full fishing days, in a single room and with a shared guide, according to the Pointer Outfitters page, read on 13/11/2026. The page shows no year, says “subject to change” and mentions an inaugural 2X1 rate: ask. According to that page, it includes fishing licences and government fees",
    "precio_pt": "US$ 6.400 por pessoa por 7 noites e 6 dias completos de pesca, em quarto individual e com guia compartilhado, segundo a página da Pointer Outfitters, lida em 13/11/2026. A página não mostra o ano, diz «sujeito a alterações» e menciona uma tarifa inaugural 2X1: consulte. Segundo essa página, inclui as licenças de pesca e as taxas do governo",
    "precio_fecha": "20261113",
    "prest_fuente": "https://pointeroutfitters.com/uguay-lodge/",
    "web": "https://pointeroutfitters.com/uguay-lodge/",
    "notas": "Listado con información pública (página leída el 13/11/2026 con investigar.yml, rama investigacion: corrientes-1-20261113-2308 archivo 06; el JSON-LD de la página dice dateModified 2026-10-25). DECISIÓN A REVISAR: la página es de Pointer Outfitters, que en docs/investigacion/corrientes-dorado-lodges-2026-11-13.md figura como agencia o colección («no confirmamos que sea el operador»); el encargo del 14/11/2026 fue publicar su tarifa como «página del operador», con la fecha de lectura y diciendo que no tiene año. Si se la considera agencia, vaciar «precio» y «precio_fecha» (queda «a consultar»). Capacidad: la página dice «4 Guests Maximum» y a la vez «six comfortable rooms»: no se publica ninguna. Que la licencia esté incluida es lo que dice la página. Contacto de la página (persona y teléfono): no se copia. Sin foto. Pendiente: confirmar quién opera, año y vigencia de la tarifa, capacidad."
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
