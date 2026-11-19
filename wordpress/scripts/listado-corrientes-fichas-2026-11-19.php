<?php
/**
 * Fondo del embudo, contenido (19/11/2026): marca de LISTADO OFICIAL en las fichas públicas de Paso de la Patria, Goya y Esquina que figuran en las
 * páginas «Experiencia de pesca» de Turismo de Corrientes, y seis fichas nuevas de «información pública».
 *
 * 1) Cruce. El script pide las tres páginas oficiales (corrientes.tur.ar), las pasa a texto visible sin tildes ni mayúsculas y busca el NOMBRE de cada
 *    ficha pública del destino correspondiente (nombre completo, o la parte anterior a « — »). Solo presente/ausente: NO imprime ningún texto de las
 *    páginas (llevan teléfonos y correos). Presente => se cargan los tres campos de la marca (prest_listado_fuente / _fecha / _url); ausente => no se
 *    toca (que un nombre no aparezca no prueba que no esté listado con otra grafía). Las coincidencias parciales por una palabra distintiva se
 *    IMPRIMEN como «POSIBLE» para que las mire una persona, y NO se marcan.
 * 2) Fichas nuevas, sin contacto, sin foto y sin tarifa inventada:
 *    - Posada Hambaré (Esquina): lo que dice su propia página sobre el paquete de 2 días de pesca y 3 noches (leído el 4/11/2026). Precio vacío («a consultar»):
 *      su sitio muestra dos tablas de habitaciones distintas y no publica la temporada de las tarifas de los paquetes.
 *    - Hotel Spa Jardín del Paraná (Paso de la Patria): hotel con spa y pesca guiada de dorado; figura en el listado.
 *    - El Gordo y el Flaco, Campamento La Amistad (Goya) y Posada del Muelle (Esquina): SOLO que figuran en el listado, con su localidad. El listado
 *      trae nombre y contacto por lugar, sin tipo de servicio, precio ni inclusiones: no se escribe nada más.
 *    La marca de las nuevas se carga solo si el nombre (o su variante) está en la página oficial al correr el script.
 * Nada de lo que se publica lleva teléfonos, correos, cuentas, enlaces a sitios de los lugares ni fotos. Las fichas existentes solo reciben los tres
 * campos de la marca (y solo si están vacíos). Idempotente. Simulacro primero ($aplicar = false).
 * Correr con wp-script.yml (script = listado-corrientes-fichas-2026-11-19; aplicar=true para el real).
 */

$aplicar = false;
$hoy     = '20261119';
$fuente  = 'Turismo de Corrientes';
$paginas = [
	'paso-de-la-patria' => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-paso-de-la-patria/',
	'goya'              => 'https://corrientes.tur.ar/tour/excursiones-de-pesca/',
	'esquina'           => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-esquina/',
];

// ---- helpers ------------------------------------------------------------------------------------------------------------------------------
function pm_lc_norm( string $t ): string {
	$t = mb_strtolower( remove_accents( html_entity_decode( $t, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
	return trim( preg_replace( '/\s+/u', ' ', preg_replace( '/[^a-z0-9]+/u', ' ', $t ) ) );
}
function pm_lc_texto( string $html ): string {
	$html = preg_replace( '#<!--.*?-->#s', ' ', $html );
	$html = preg_replace( '#<(script|style|noscript|svg|template)\b.*?</\1\s*>#is', ' ', $html );
	return pm_lc_norm( preg_replace( '#<[^>]*>#', ' ', $html ) );
}
/** True when the normalized phrase appears as whole words in the normalized page text. */
function pm_lc_esta( string $texto, string $frase ): bool {
	return '' !== $frase && false !== strpos( ' ' . $texto . ' ', ' ' . $frase . ' ' );
}
function pm_lc_ver( $v ): string {
	return is_array( $v ) ? implode( ',', array_map( 'strval', $v ) ) : (string) $v;
}
function pm_lc_norm_campo( string $k, $v ): string {
	if ( in_array( $k, [ 'precio_fecha', 'verificado', 'prest_listado_fecha' ], true ) ) {
		return preg_replace( '/\D/', '', (string) $v );
	}
	if ( in_array( $k, [ 'prest_consentimiento', 'prest_publico', 'datos_ejemplo' ], true ) ) {
		return (string) (int) $v;
	}
	if ( is_array( $v ) ) {
		$v = array_map( 'strval', $v );
		sort( $v );
		return implode( ',', $v );
	}
	return trim( (string) $v );
}
function pm_lc_ficha_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}
/** Words that say nothing about WHICH place it is: they alone never make a «POSIBLE». */
const PM_LC_GENERICAS = [ 'hotel', 'apart', 'posada', 'pousada', 'lodge', 'fishing', 'pesca', 'camp', 'camping', 'campamento', 'guia', 'guias', 'cabanas', 'turismo', 'paso', 'patria', 'goya', 'esquina', 'parana', 'corrientes', 'experiencia', 'del', 'los', 'las', 'con', 'spa', 'rio', 'club', 'casa', 'complejo', 'dorado', 'surubi' ];

// ---- the three official pages (only presence of names is used and printed) ---------------------------------------------------------------
$textos = [];
foreach ( $paginas as $destino => $url ) {
	$r = wp_remote_get( $url, [ 'timeout' => 20, 'redirection' => 5, 'limit_response_size' => 2000000, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)', 'sslverify' => true ] );
	if ( is_wp_error( $r ) ) {
		WP_CLI::error( "no pude leer la página oficial de $destino: " . $r->get_error_message() );
	}
	$status = (int) wp_remote_retrieve_response_code( $r );
	$texto  = pm_lc_texto( (string) wp_remote_retrieve_body( $r ) );
	WP_CLI::log( "página oficial $destino: status $status, " . mb_strlen( $texto ) . ' caracteres de texto (no se imprime)' );
	( 200 === $status && mb_strlen( $texto ) >= 300 ) || WP_CLI::error( "la página oficial de $destino no devolvió una lectura útil" );
	$textos[ $destino ] = $texto;
}

// ---- new cards -----------------------------------------------------------------------------------------------------------------------------
$comun = [ 'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado' ];
$solo_listado = fn( string $lugar, string $en_lugar_pt, string $en_lugar_en ) => [
	'descripcion'    => "Figura en el listado de pesca de Turismo de Corrientes para {$lugar}. Qué ofrece, qué incluye y a cuánto: a consultar.",
	'descripcion_pt' => "Consta da lista de pesca de Turismo de Corrientes para {$en_lugar_pt}. O que oferece, o que inclui e quanto custa: a consultar.",
	'descripcion_en' => "Appears in the Turismo de Corrientes fishing listing for {$en_lugar_en}. What it offers, what is included and the price: to be asked.",
];
$nuevas = [
	[
		'nombre'  => 'Posada Hambaré',
		'destino' => 'esquina',
		'alias'   => [ 'posada hambare', 'hambare' ],
		'campos'  => $comun + [
			'tipo'           => [ 'alojamiento', 'operador' ],
			'prest_destino'  => 'esquina',
			'localidad'      => 'Esquina (Corrientes), confluencia de los ríos Corrientes y Paraná',
			'descripcion'    => 'Posada de Esquina con vista a la confluencia de los ríos Corrientes y Paraná, según su propia página. Ofrece paquetes de pesca de dorado, pacú y surubí con alojamiento: el de 2 días de pesca y 3 noches incluye comidas, bebidas, lancha, guía, carnada, combustible y licencia. Precio de los paquetes: a consultar.',
			'descripcion_pt' => 'Pousada em Esquina com vista para a confluência dos rios Corrientes e Paraná, segundo a própria página. Oferece pacotes de pesca de dourado, pacu e surubim com hospedagem: o de 2 dias de pesca e 3 noites inclui refeições, bebidas, barco, guia, isca, combustível e licença. Preço dos pacotes: a consultar.',
			'descripcion_en' => 'Lodge in Esquina overlooking the confluence of the Corrientes and Paraná rivers, according to its own page. It offers fishing packages for dorado, pacú and surubí with lodging: the 2 fishing days and 3 nights package includes meals, drinks, boat, guide, bait, fuel and licence. Package price: to be asked.',
			'prest_especies' => [ 'dorado', 'pacu', 'surubi' ],
			'incluye'        => [ 'alojamiento', 'comida', 'bebidas', 'combustible', 'carnada', 'licencia' ],
			'prest_programa'    => '2 días de pesca y 3 noches, con lancha y guía',
			'prest_programa_pt' => '2 dias de pesca e 3 noites, com barco e guia',
			'prest_programa_en' => '2 fishing days and 3 nights, with boat and guide',
			'prest_no_incluye'    => 'Equipo de pesca',
			'prest_no_incluye_pt' => 'Equipamento de pesca',
			'prest_no_incluye_en' => 'Fishing tackle',
			'precio' => '', 'precio_fecha' => '',
			'prest_fuente' => 'https://pousadahambare.com.br/',
			'verificado'   => '20261104', // the day its package page was read; the card says «Datos al 4 de noviembre de 2026»
			'notas'        => 'Listado con información pública. Paquete de 2 días de pesca y 3 noches leído en la página del propio lugar el 4/11/2026 (sin teléfonos ni correos en el repo). Precio vacío a propósito (19/11/2026): su sitio muestra dos tablas de habitaciones distintas (con desayuno y portada) y no publica la temporada de las tarifas de los paquetes; las cifras «desde» leídas el 4 y 5/11/2026 siguen en las notas de precios, no en la ficha. Pendiente: preguntarle la temporada de sus tarifas y si acepta figurar con información propia.',
		],
	],
	[
		'nombre'  => 'Hotel Spa Jardín del Paraná',
		'destino' => 'paso-de-la-patria',
		'alias'   => [ 'jardin del parana', 'hotel spa jardin del parana' ],
		'campos'  => $comun + [
			'tipo'           => [ 'alojamiento', 'guia' ],
			'prest_destino'  => 'paso-de-la-patria',
			'localidad'      => 'Paso de la Patria (Corrientes)',
			'descripcion'    => 'Hotel con spa en Paso de la Patria, a pocas cuadras del río. Según su propia página ofrece pesca guiada de dorado y otras especies, con guías. Qué incluye la salida y a cuánto sale: a consultar.',
			'descripcion_pt' => 'Hotel com spa em Paso de la Patria, a poucas quadras do rio. Segundo a própria página, oferece pesca guiada de dourado e outras espécies, com guias. O que inclui a saída e quanto custa: a consultar.',
			'descripcion_en' => 'Hotel with a spa in Paso de la Patria, a few blocks from the river. According to its own page it offers guided fishing for dorado and other species, with guides. What the trip includes and the price: to be asked.',
			'prest_especies' => [ 'dorado' ],
			'precio' => '', 'precio_fecha' => '',
			'prest_fuente' => 'https://corrientes.tur.ar/tour/experiencia-de-pesca-paso-de-la-patria/',
			'verificado'   => $hoy,
			'notas'        => 'Listado con información pública. Hotel con spa, piscina y parque a 3 cuadras del río; pesca guiada de dorado y otras especies (portada de su página, leída el 19/11/2026, sin extractos con contactos). Qué incluye la salida y el precio: no se leyó (el texto se cortó); sin tarifa. Pendiente: preguntarle qué incluye y a cuánto, y si acepta figurar con información propia.',
		],
	],
	[
		'nombre'  => 'El Gordo y el Flaco',
		'destino' => 'goya',
		'alias'   => [ 'el gordo y el flaco' ],
		'campos'  => $comun + $solo_listado( 'Goya', 'Goya', 'Goya' ) + [
			'prest_destino' => 'goya',
			'localidad'     => 'Goya (Corrientes)',
			'prest_fuente'  => $paginas['goya'],
			'verificado'    => $hoy,
			'notas'         => 'Listado con información pública: solo figura en la página «Experiencia de pesca – Goya» de Turismo de Corrientes (19/11/2026). El listado trae nombre y contacto por lugar, sin tipo de servicio, precio ni inclusiones; no se encontró página propia. Pendiente: escribirle (docs/investigacion/fondo-embudo-2026-11-19.md, sección 4e).',
		],
	],
	[
		'nombre'  => 'Campamento La Amistad',
		'destino' => 'goya',
		'alias'   => [ 'campamento la amistad', 'la amistad' ],
		'campos'  => $comun + $solo_listado( 'Goya', 'Goya', 'Goya' ) + [
			'prest_destino' => 'goya',
			'localidad'     => 'Goya (Corrientes)',
			'prest_fuente'  => $paginas['goya'],
			'verificado'    => $hoy,
			'notas'         => 'Listado con información pública: solo figura en la página «Experiencia de pesca – Goya» de Turismo de Corrientes (19/11/2026). Cuidado con un homónimo de Santa Fe: no es este lugar. Sin tipo de servicio, precio ni inclusiones en el listado; no se encontró página propia. Pendiente: escribirle.',
		],
	],
	[
		'nombre'  => 'Posada del Muelle',
		'destino' => 'esquina',
		'alias'   => [ 'posada del muelle' ],
		'campos'  => $comun + $solo_listado( 'Esquina', 'Esquina', 'Esquina' ) + [
			'prest_destino' => 'esquina',
			'localidad'     => 'Esquina (Corrientes)',
			'prest_fuente'  => $paginas['esquina'],
			'verificado'    => $hoy,
			'notas'         => 'Listado con información pública: solo figura en la página «Experiencia de pesca» de Esquina de Turismo de Corrientes (19/11/2026). Sin tipo de servicio, precio ni inclusiones en el listado; sin página propia leída (solo redes y portales de terceros). Pendiente: escribirle.',
		],
	],
];

// Nothing public may carry a contact, an account, a link, a "best"/record claim or the word "operador" (copy rule).
$prohibido = '/@|https?:\/\/|instagram|facebook|whatsapp|tel[eé]fono|\+\d{2}|\d{6,}|\boperador|\bmejor(es)?\b|r[eé]cord|habilitad|registro/iu';
foreach ( $nuevas as $n ) {
	foreach ( [ 'descripcion', 'descripcion_pt', 'descripcion_en', 'localidad', 'prest_programa', 'prest_programa_pt', 'prest_programa_en', 'prest_no_incluye', 'prest_no_incluye_pt', 'prest_no_incluye_en' ] as $k ) {
		if ( isset( $n['campos'][ $k ] ) && preg_match( $prohibido, $n['campos'][ $k ], $m ) ) {
			WP_CLI::error( "texto público de «{$n['nombre']}» ($k) lleva algo que no se publica: «{$m[0]}»" );
		}
	}
}
WP_CLI::log( 'OK: texto público de las fichas nuevas sin contactos, enlaces, cuentas, superlativos ni «operador».' );

// ---- cross-check: existing public cards ---------------------------------------------------------------------------------------------------
$marcar = []; // [ destino, nombre, pid|0, motivo ]
$sin    = [];
foreach ( $paginas as $destino => $url ) {
	$existentes = pm_prestadores_destino( $destino ); // public only
	WP_CLI::log( "== $destino: " . count( $existentes ) . ' fichas públicas' );
	foreach ( $existentes as $r ) {
		$nombre = (string) $r['nombre'];
		$claves = array_unique( array_filter( [ pm_lc_norm( $nombre ), pm_lc_norm( preg_split( '/\s[—–-]\s/u', $nombre )[0] ) ] ) );
		$esta   = false;
		foreach ( $claves as $c ) {
			$esta = $esta || pm_lc_esta( $textos[ $destino ], $c );
		}
		$ya = $r['listado'] ? ' (ya tiene marca: ' . $r['listado']['fuente'] . ', ' . $r['listado']['fecha'] . ')' : '';
		if ( $esta ) {
			WP_CLI::log( "  LISTADO  «{$nombre}» #{$r['id']}{$ya}" );
			$marcar[] = [ $destino, $nombre, (int) $r['id'], $r['listado'] ? 'ya' : 'nueva' ];
		} else {
			$posible = [];
			foreach ( explode( ' ', pm_lc_norm( $nombre ) ) as $tok ) {
				if ( mb_strlen( $tok ) >= 6 && ! in_array( $tok, PM_LC_GENERICAS, true ) && pm_lc_esta( $textos[ $destino ], $tok ) ) {
					$posible[] = $tok;
				}
			}
			WP_CLI::log( "  ausente  «{$nombre}» #{$r['id']}" . ( $posible ? '  POSIBLE por la palabra: ' . implode( ', ', $posible ) . ' (NO se marca; revisar a mano)' : '' ) . $ya );
		}
	}
}

// ---- cross-check: new cards (by alias, checked now against the page) ------------------------------------------------------------------------
$plan_nuevas = [];
foreach ( $nuevas as $n ) {
	$pid  = pm_lc_ficha_id( $n['nombre'] );
	$esta = false;
	foreach ( $n['alias'] as $a ) {
		$esta = $esta || pm_lc_esta( $textos[ $n['destino'] ], $a );
	}
	WP_CLI::log( ( $pid ? "EXISTE #$pid" : 'NUEVA' ) . " «{$n['nombre']}» ({$n['destino']}): " . ( $esta ? 'figura en el listado oficial' : 'NO se ve en el listado oficial (sin marca)' ) );
	$plan_nuevas[] = [ $n, $pid, $esta ];
	if ( ! $pid ) {
		foreach ( $n['campos'] as $k => $v ) {
			if ( ! in_array( $k, [ 'notas', 'prest_fuente' ], true ) && '' !== $v ) {
				WP_CLI::log( "      $k = " . pm_lc_ver( $v ) );
			}
		}
	}
	if ( $esta ) {
		$marcar[] = [ $n['destino'], $n['nombre'], $pid, 'nueva' ];
	}
}

// The three mark fields (same for every card; the date is today's reading).
$marca = fn( string $destino ) => [ 'prest_listado_fuente' => $fuente, 'prest_listado_fecha' => $hoy, 'prest_listado_url' => $paginas[ $destino ] ];
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': ' . count( $marcar ) . ' fichas con marca (fuente «' . $fuente . '», fecha ' . $hoy . ')' );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

// ---- apply --------------------------------------------------------------------------------------------------------------------------------
$ids = []; // nombre => pid
foreach ( $plan_nuevas as [ $n, $pid, $esta ] ) {
	if ( ! $pid ) {
		$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $n['nombre'] ], true );
		is_wp_error( $pid ) && WP_CLI::error( $pid->get_error_message() );
		WP_CLI::log( "✓ ficha «{$n['nombre']}» creada #$pid" );
		foreach ( $n['campos'] as $k => $v ) {
			if ( '' !== $v ) {
				update_field( $k, $v, $pid );
			}
		}
	} else {
		WP_CLI::log( "ficha «{$n['nombre']}» #$pid ya existía: solo se llenan los campos vacíos" );
		foreach ( $n['campos'] as $k => $v ) {
			if ( '' !== $v && '' === pm_lc_norm_campo( $k, pm_campo( $k, $pid ) ) ) {
				update_field( $k, $v, $pid );
				WP_CLI::log( "  + $k" );
			}
		}
	}
	$ids[ $n['nombre'] ] = (int) $pid;
}
foreach ( $marcar as [ $destino, $nombre, $pid, $motivo ] ) {
	$pid = $pid ?: ( $ids[ $nombre ] ?? 0 );
	if ( ! $pid ) {
		WP_CLI::warning( "sin id para «$nombre»" );
		continue;
	}
	$actual = pm_prestador_listado_datos( $pid );
	if ( $actual ) {
		WP_CLI::log( "  = «{$nombre}» #$pid ya tenía marca ({$actual['fuente']}, {$actual['fecha']}): no se toca" );
		continue;
	}
	foreach ( $marca( $destino ) as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	WP_CLI::log( "  ✓ marca de listado en «{$nombre}» #$pid" );
}
wp_cache_flush();

// ---- read back ----------------------------------------------------------------------------------------------------------------------------
$bien = true;
foreach ( $marcar as [ $destino, $nombre, $pid, $motivo ] ) {
	$pid = $pid ?: ( $ids[ $nombre ] ?? 0 );
	$l   = $pid ? pm_prestador_listado_datos( $pid ) : [];
	$ok  = $l && ( 'ya' === $motivo || ( $fuente === $l['fuente'] && preg_replace( '/\D/', '', $l['fecha'] ) === $hoy && $paginas[ $destino ] === $l['url'] ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": marca «{$nombre}» #$pid " . ( $l ? $l['fuente'] . ' · ' . $l['fecha'] . ' · ' . $l['url'] : '(sin marca)' ) );
	$bien = $bien && $ok;
}
foreach ( $plan_nuevas as [ $n, , ] ) {
	$pid = $ids[ $n['nombre'] ] ?? 0;
	foreach ( $n['campos'] as $k => $v ) {
		if ( '' === $v ) {
			continue;
		}
		$b = pm_lc_norm_campo( $k, pm_campo( $k, $pid ) ) === pm_lc_norm_campo( $k, $v );
		WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": «{$n['nombre']}» #$pid $k" . ( in_array( $k, [ 'notas', 'prest_fuente' ], true ) ? '' : ' = ' . mb_substr( pm_lc_ver( pm_campo( $k, $pid ) ), 0, 90 ) ) );
		$bien = $bien && $b;
	}
	$ficha = pm_prestador( $pid );
	$sale  = false;
	foreach ( pm_prestadores_destino( $n['destino'] ) as $r ) {
		$sale = $sale || (int) $r['id'] === $pid;
	}
	$pub   = wp_json_encode( [ $ficha['descripcion'], $ficha['programa'], $ficha['no_incluye'], $ficha['localidad'], $ficha['precio'] ], JSON_UNESCAPED_UNICODE );
	$limpio = ! preg_match( $prohibido, $pub ) && '' === $ficha['precio'] && '' === $ficha['precio_fecha'] && ! $ficha['consentimiento'] && ! $ficha['ejemplo'] && ! $ficha['fotos'];
	WP_CLI::log( ( $sale && $limpio ? 'VERIFIED' : 'FALLÓ' ) . ": «{$n['nombre']}» sale en la lista de {$n['destino']} como información pública (publico=" . (int) $ficha['publico'] . ' consentimiento=' . (int) $ficha['consentimiento'] . ' fotos=' . count( $ficha['fotos'] ) . '), texto público sin contactos y precio vacío (a consultar)' );
	$bien = $bien && $sale && $limpio;
}
WP_CLI::log( $bien ? 'VERIFIED: todo leído de vuelta' : 'FALLÓ: hay campos que no coinciden (ver arriba)' );
