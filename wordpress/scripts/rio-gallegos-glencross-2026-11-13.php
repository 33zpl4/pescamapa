<?php
/**
 * Río Gallegos, 13/11/2026: NEW card Estancia Glencross (Solid Adventures); Las Buitreras and Karku moved into the new card fields; one sentence in
 * the guide (es, en, pt).
 * Sources (operators' own pages, branch `investigacion`): Glencross: solidadventures.com/glencross (gallegos-2-20261113-0925, file 02; the page's
 * JSON-LD dateModified is 2025-02-10): species, 85,000 ha working estancia, private access to the upper Río Gallegos and the Rubens and Penitente,
 * horse riding / hiking / condor watching, "The season runs from late January to early April and can be booked as a half-week or full-week trip",
 * combo with Las Buitreras (2-hour drive). Weekdays and a price exist only on an agency page: NOT published. No Glencross price anywhere.
 * Las Buitreras: the operator's own 2027 programme (see las-buitreras-2027-2026-11-13) and its confirmations of 13/11/2026 (already in the card).
 * Karku Fly Fishing: operator's emails of 13/11/2026 (see karku-acuerdo-2026-11-13). Karku asked that its RATES ARE NOT PUBLISHED: this script writes
 * no rate and no companion text for Karku, and its read-back fails if any rate shows up.
 * Cards stay as they are in tier (Glencross: información pública, sin verificar; no consent, no agreement). No contact data, no photo.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = rio-gallegos-glencross-2026-11-13).
 */

$aplicar = false;
$hoy     = '20261113';
$gl_url  = 'https://www.solidadventures.com/glencross/';

// ---- helpers (same pattern as las-buitreras-2027-2026-11-13 / karku-acuerdo-2026-11-13) -------------------------------------------
function pm_ff_ver( $v ): string {
	return is_array( $v ) ? implode( ',', $v ) : (string) $v;
}
function pm_ff_norm( string $k, $v ): string {
	if ( in_array( $k, [ 'precio_fecha', 'verificado' ], true ) ) {
		return preg_replace( '/\D/', '', (string) $v );
	}
	if ( in_array( $k, [ 'prest_consentimiento', 'prest_publico', 'datos_ejemplo', 'capacidad' ], true ) ) {
		return (string) (int) $v;
	}
	return is_array( $v ) ? implode( ',', $v ) : (string) $v;
}
/** Logs ANTES / DESPUÉS in full for every field that changes; returns the fields to write. */
function pm_ff_ficha_plan( int $pid, array $campos ): array {
	$cambios = [];
	foreach ( $campos as $k => $nuevo ) {
		$antes = pm_campo( $k, $pid );
		$igual = pm_ff_norm( $k, $antes ) === pm_ff_norm( $k, $nuevo );
		WP_CLI::log( ( $igual ? '  = ' : '  ~ ' ) . $k . ( $igual ? ' sin cambios' : '' ) );
		if ( ! $igual ) {
			WP_CLI::log( '      ANTES:   ' . pm_ff_ver( $antes ) );
			WP_CLI::log( '      DESPUÉS: ' . pm_ff_ver( $nuevo ) );
			$cambios[ $k ] = $nuevo;
		}
	}
	return $cambios;
}
function pm_ff_ficha_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}
/** Reads every field back; logs one VERIFIED/FALLÓ line per field with the value read. */
function pm_ff_ficha_verificar( int $pid, string $nombre, array $campos ): bool {
	$ok = true;
	foreach ( $campos as $k => $nuevo ) {
		$b = pm_ff_norm( $k, pm_campo( $k, $pid ) ) === pm_ff_norm( $k, $nuevo );
		WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» #$pid $k = " . pm_ff_ver( pm_campo( $k, $pid ) ) );
		$ok = $ok && $b;
	}
	return $ok;
}
function pm_ff_crear( string $nombre, array $campos ): int {
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	return (int) $pid;
}
function pm_ff_cambiar( string $texto, string $viejo, string $nuevo ): array {
	if ( false !== strpos( $texto, $nuevo ) ) {
		return [ $texto, 'ya' ];
	}
	if ( false === strpos( $texto, $viejo ) ) {
		return [ $texto, 'falta' ];
	}
	return [ str_replace( $viejo, $nuevo, $texto ), 'cambia' ];
}
/** Plans the text edits of one guide: body replacements, FAQ answers (by question), temporada rows (by "meses"), sources to add. */
function pm_ff_guia_plan( string $clave, string $i, array $g, bool $aplicar ): ?array {
	$p = pm_buscar( 'pm_destino', $clave, $i );
	if ( ! $p ) {
		WP_CLI::warning( "guía $clave ($i): no existe, se omite" );
		return null;
	}
	$texto   = $p->post_content;
	$estados = [];
	foreach ( $g['cuerpo'] ?? [] as $n => [ $viejo, $nuevo ] ) {
		[ $texto, $e ] = pm_ff_cambiar( $texto, $viejo, $nuevo );
		$estados[]     = "cuerpo #$n: $e";
		'cambia' === $e && WP_CLI::log( "  guía $clave ($i) cuerpo #$n\n      ANTES:   $viejo\n      DESPUÉS: $nuevo" );
	}
	$filas = pm_filas( 'faqs', $p->ID );
	foreach ( $g['faqs'] ?? [] as $pregunta => [ $viejo, $nuevo ] ) {
		$hallada = false;
		foreach ( $filas as $k => $f ) {
			if ( ( $f['pregunta'] ?? '' ) === $pregunta ) {
				$hallada = true;
				[ $filas[ $k ]['respuesta'], $e ] = pm_ff_cambiar( (string) $f['respuesta'], $viejo, $nuevo );
				$estados[] = 'FAQ «' . mb_substr( $pregunta, 0, 40 ) . "»: $e";
				'cambia' === $e && WP_CLI::log( "  guía $clave ($i) FAQ «{$pregunta}»\n      ANTES (respuesta completa):   {$f['respuesta']}\n      DESPUÉS (respuesta completa): {$filas[ $k ]['respuesta']}" );
			}
		}
		$hallada || $estados[] = 'FAQ «' . mb_substr( $pregunta, 0, 40 ) . '»: falta (pregunta no encontrada)';
	}
	$temp = pm_filas( 'temporada', $p->ID );
	foreach ( $g['temporada'] ?? [] as $meses => [ $viejo, $nuevo ] ) {
		$hallada = false;
		foreach ( $temp as $k => $f ) {
			if ( ( $f['meses'] ?? '' ) === $meses ) {
				$hallada = true;
				[ $temp[ $k ]['que'], $e ] = pm_ff_cambiar( (string) $f['que'], $viejo, $nuevo );
				$estados[] = "temporada «{$meses}»: $e";
				'cambia' === $e && WP_CLI::log( "  guía $clave ($i) temporada «{$meses}»\n      ANTES:   {$f['que']}\n      DESPUÉS: {$temp[ $k ]['que']}" );
			}
		}
		$hallada || $estados[] = "temporada «{$meses}»: falta (fila no encontrada)";
	}
	// Existing rows are written back as the date picker stores them (Ymd) and with oficial as 0/1.
	$fuentes = array_map( fn( $f ) => [ 'nombre' => $f['nombre'] ?? '', 'url' => $f['url'] ?? '', 'oficial' => empty( $f['oficial'] ) ? 0 : 1, 'consultada' => preg_replace( '/\D/', '', (string) ( $f['consultada'] ?? '' ) ) ], pm_filas( 'fuentes', $p->ID ) );
	foreach ( $g['fuentes'] ?? [] as [ $nombre, $url, $oficial, $consultada ] ) {
		$hay = false;
		foreach ( $fuentes as $f ) {
			$hay = $hay || ( ( $f['url'] ?? '' ) === $url );
		}
		if ( ! $hay ) {
			$fuentes[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $oficial, 'consultada' => $consultada ];
			WP_CLI::log( "  guía $clave ($i) fuente nueva: $nombre — $url" );
			$estados[] = 'fuente ' . $url . ': cambia';
		} else {
			$estados[] = 'fuente ' . $url . ': ya';
		}
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía $clave ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' | ' . implode( ' | ', $estados ) );
	foreach ( $estados as $e ) {
		false !== strpos( $e, 'falta' ) && WP_CLI::warning( "guía $clave ($i): $e" );
	}
	return [ 'id' => $p->ID, 'clave' => $clave, 'i' => $i, 'texto' => $texto, 'filas' => $filas, 'temp' => $temp, 'fuentes' => $fuentes, 'estados' => $estados, 'g' => $g ];
}
function pm_ff_guia_aplicar( array $plan, string $hoy ): void {
	wp_update_post( [ 'ID' => $plan['id'], 'post_content' => wp_slash( $plan['texto'] ) ] );
	update_field( 'faqs', $plan['filas'], $plan['id'] );
	if ( ! empty( $plan['g']['temporada'] ) ) {
		update_field( 'temporada', $plan['temp'], $plan['id'] );
	}
	if ( ! empty( $plan['g']['fuentes'] ) ) {
		update_field( 'fuentes', $plan['fuentes'], $plan['id'] );
	}
	update_field( 'actualizado', $hoy, $plan['id'] );
}
function pm_ff_guia_verificar( array $plan ): bool {
	$p = get_post( $plan['id'] );
	$filas = pm_filas( 'faqs', $plan['id'] );
	$temp  = pm_filas( 'temporada', $plan['id'] );
	$fuentes = pm_filas( 'fuentes', $plan['id'] );
	$bien = true;
	foreach ( $plan['g']['cuerpo'] ?? [] as [ , $nuevo ] ) {
		$bien = $bien && false !== strpos( $p->post_content, $nuevo );
	}
	foreach ( $plan['g']['faqs'] ?? [] as $pregunta => [ , $nuevo ] ) {
		$hit = false;
		foreach ( $filas as $f ) {
			$hit = $hit || ( ( $f['pregunta'] ?? '' ) === $pregunta && false !== strpos( (string) $f['respuesta'], $nuevo ) );
		}
		$bien = $bien && $hit;
	}
	foreach ( $plan['g']['temporada'] ?? [] as $meses => [ , $nuevo ] ) {
		$hit = false;
		foreach ( $temp as $f ) {
			$hit = $hit || ( ( $f['meses'] ?? '' ) === $meses && false !== strpos( (string) $f['que'], $nuevo ) );
		}
		$bien = $bien && $hit;
	}
	foreach ( $plan['g']['fuentes'] ?? [] as [ , $url ] ) {
		$hit = false;
		foreach ( $fuentes as $f ) {
			$hit = $hit || ( ( $f['url'] ?? '' ) === $url );
		}
		$bien = $bien && $hit;
	}
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": guía {$plan['clave']} ({$plan['i']}) #{$plan['id']}: " . count( $plan['g']['cuerpo'] ?? [] ) . ' párrafos, ' . count( $plan['g']['faqs'] ?? [] ) . ' FAQs, ' . count( $plan['g']['temporada'] ?? [] ) . ' filas de temporada, ' . count( $plan['g']['fuentes'] ?? [] ) . ' fuentes leídos de vuelta; ' . count( $filas ) . ' FAQs en total ' . get_permalink( $plan['id'] ) );
	return $bien;
}
// ---- end helpers --------------------------------------------------------------------------------------------------------------------

$fichas = [
	'Estancia Glencross (Solid Adventures)' => [
		'tipo' => [ 'alojamiento', 'operador' ], 'prest_destino' => 'rio-gallegos', 'localidad' => 'Estancia Glencross, alto río Gallegos, Santa Cruz',
		'descripcion' => 'Estancia en actividad de 85.000 hectáreas (ganado y ovejas), en la alta cordillera de Santa Cruz y sobre la frontera con Chile, donde Solid Adventures ofrece pesca con mosca de trucha marrón residente y de trucha marrón de mar. Según su página, tiene acceso privado a la parte más alta del río Gallegos y a sus afluentes Rubens y Penitente, tres ríos con paisajes distintos; en los afluentes suele usarse caña de una mano. Los huéspedes se alojan en la casa principal de la estancia. Además de pescar, el operador menciona cabalgatas, caminatas, observación de cóndores y otras excursiones a pedido. Su temporada va de fines de enero a principios de abril y se reserva como media semana o como semana completa. Solid Adventures lo ofrece como extensión de una semana en Las Buitreras, a unas dos horas en auto según su página. Su página no muestra tarifa.',
		'descripcion_en' => 'Working estancia of 85,000 hectares (cattle and sheep), high in the Andes of Santa Cruz and on the border with Chile, where Solid Adventures offers fly fishing for resident brown trout and sea-run brown trout. According to its page, it has private access to the upper Río Gallegos and its tributaries Rubens and Penitente, three rivers with different scenery; in the tributaries a single-hand rod is usually used. Guests stay in the main house of the estancia. Besides fishing, the operator mentions horseback riding, hiking, condor watching and other excursions on request. Its season runs from late January to early April and it is booked as a half week or a full week. Solid Adventures offers it as an extension to a week at Las Buitreras, about a two-hour drive away according to its page. Its page shows no rate.',
		'descripcion_pt' => 'Estância em atividade de 85.000 hectares (gado e ovelhas), na alta cordilheira de Santa Cruz e na fronteira com o Chile, onde a Solid Adventures oferece pesca com mosca de truta marrom residente e de truta marrom de mar. Segundo a página, tem acesso privado à parte mais alta do rio Gallegos e aos afluentes Rubens e Penitente, três rios com paisagens distintas; nos afluentes costuma-se usar vara de uma mão. Os hóspedes ficam na casa principal da estância. Além de pescar, o operador menciona cavalgadas, caminhadas, observação de condores e outras excursões sob pedido. A temporada vai do fim de janeiro ao começo de abril e é reservada como meia semana ou semana completa. A Solid Adventures a oferece como extensão de uma semana em Las Buitreras, a cerca de duas horas de carro segundo a página. A página não mostra tarifa.',
		'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha', 'trucha-marron-anadroma' ],
		'prest_temporada' => 'De fines de enero a principios de abril (según su página)',
		'prest_temporada_en' => 'From late January to early April (according to its page)',
		'prest_temporada_pt' => 'Do fim de janeiro ao começo de abril (segundo a página)',
		'prest_programa' => 'Media semana o semana completa (según su página; no publica los días ni la tarifa)',
		'prest_programa_en' => 'Half week or full week (according to its page; it publishes neither the days nor the rate)',
		'prest_programa_pt' => 'Meia semana ou semana completa (segundo a página; não publica os dias nem a tarifa)',
		'precio' => '', 'precio_fecha' => '',
		'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado', 'verificado' => '20261113',
		'prest_fuente' => $gl_url, 'web' => $gl_url,
		'notas' => 'Listado con información pública (página del operador leída el 13/11/2026 con investigar.yml, rama investigacion, carpeta gallegos-2-20261113-0925, archivo 02; el JSON-LD de la página dice dateModified 2025-02-10, así que la temporada puede no estar al día). Solo lo que dice la página del operador. Los días de la semana de la media semana y de la semana completa, y una tarifa, salen únicamente de una página de agencia (Fly Water Travel): NO se publican. Precio vacío ("a consultar"). Solid Adventures ofreció por correo una comisión del 10 % (docs/prestadores.md, "Acuerdos con operadores"), pero esta ficha no tiene consentimiento ni acuerdo. Pendiente de preguntarle a Solid Adventures: fecha de apertura 2027, tarifa 2027, días de la media semana y capacidad. Contacto no publicado.',
	],
	'Estancia Las Buitreras (Solid Adventures)' => [
		'prest_programa' => '7 noches, 6 días de pesca con guía; sábado a sábado',
		'prest_programa_en' => '7 nights, 6 guided fishing days; Saturday to Saturday',
		'prest_programa_pt' => '7 noites, 6 dias de pesca com guia; sábado a sábado',
		'prest_estadia_minima' => 'Solo semanas completas; enero de 2027 completo al 13/11/2026',
		'prest_estadia_minima_en' => 'Full weeks only; January 2027 fully booked as of 13/11/2026',
		'prest_estadia_minima_pt' => 'Só semanas completas; janeiro de 2027 lotado em 13/11/2026',
		'prest_acompanante' => 'US$ 3.000 por semana',
		'prest_acompanante_en' => 'US$ 3,000 per week',
		'prest_acompanante_pt' => 'US$ 3.000 por semana',
		'prest_no_incluye' => 'Vuelos, equipo de pesca, bebidas espirituosas y propinas; el traslado fuera del horario recomendado cuesta unos US$ 300 por auto',
		'prest_no_incluye_en' => 'Flights, fishing tackle, spirits and tips; the transfer outside the recommended schedule costs about US$ 300 per car',
		'prest_no_incluye_pt' => 'Voos, equipamento de pesca, destilados e gorjetas; o traslado fora do horário recomendado custa cerca de US$ 300 por carro',
		'capacidad' => 12, 'prest_capacidad_unidad' => 'semana',
	],
	'Karku Fly Fishing' => [
		'prest_programa' => 'Sábado a sábado; también arma estadías más largas o más cortas según disponibilidad (confirmado por el operador el 13/11/2026)',
		'prest_programa_en' => 'Saturday to Saturday; it also arranges longer or shorter stays subject to availability (confirmed by the operator on 13/11/2026)',
		'prest_programa_pt' => 'Sábado a sábado; também monta estadias mais longas ou mais curtas conforme a disponibilidade (confirmado pelo operador em 13/11/2026)',
		'prest_no_incluye' => 'Lavandería, equipo de pesca, bebidas alcohólicas y propinas',
		'prest_no_incluye_en' => 'Laundry, fishing equipment, alcoholic drinks and tips',
		'prest_no_incluye_pt' => 'Lavanderia, equipamento de pesca, bebidas alcoólicas e gorjetas',
		'capacidad' => 4, 'prest_capacidad_unidad' => 'semana',
	],
];

$guias = [
	'es' => [
		'cuerpo' => [ [ 'y la da a quien consulta por una cotización.',
			'y la da a quien consulta por una cotización. Solid Adventures ofrece además Estancia Glencross, en el alto río Gallegos: según su página, se reserva como media semana o como semana completa, de fines de enero a principios de abril, y no muestra tarifa.' ] ],
		'fuentes' => [ [ 'Solid Adventures: Estancia Glencross (operador)', 'https://www.solidadventures.com/glencross/', 0, '20261113' ] ],
	],
	'en' => [
		'cuerpo' => [ [ 'and gives it to anglers who ask for a quote.',
			'and gives it to anglers who ask for a quote. Solid Adventures also offers Estancia Glencross, on the upper Río Gallegos: according to its page, it is booked as a half week or a full week, from late January to early April, and it shows no rate.' ] ],
		'fuentes' => [ [ 'Solid Adventures: Estancia Glencross (operator)', 'https://www.solidadventures.com/glencross/', 0, '20261113' ] ],
	],
	'pt' => [
		'cuerpo' => [ [ 'e informa a quem pede uma cotação.',
			'e informa a quem pede uma cotação. A Solid Adventures oferece também a Estancia Glencross, no alto rio Gallegos: segundo a página, reserva-se como meia semana ou semana completa, do fim de janeiro ao começo de abril, e não mostra tarifa.' ] ],
		'fuentes' => [ [ 'Solid Adventures: Estancia Glencross (operador)', 'https://www.solidadventures.com/glencross/', 0, '20261113' ] ],
	],
];

// --- 1. Cards -------------------------------------------------------------------------------------
$plan_fichas = [];
foreach ( $fichas as $nombre => $campos ) {
	$pid = pm_ff_ficha_id( $nombre );
	if ( ! $pid && 'Estancia Glencross (Solid Adventures)' !== $nombre ) {
		WP_CLI::error( "Se esperaba una sola ficha «{$nombre}»" );
	}
	if ( ! $pid ) {
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» NUEVA (no existe), campos:" );
		foreach ( $campos as $k => $v ) {
			WP_CLI::log( "  + $k: " . pm_ff_ver( $v ) );
		}
		$plan_fichas[ $nombre ] = [ 0, $campos ];
		continue;
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» #$pid (publico=" . (int) pm_campo( 'prest_publico', $pid ) . ' consentimiento=' . (int) pm_campo( 'prest_consentimiento', $pid ) . ' estado=' . pm_campo( 'estado', $pid ) . ')' );
	$plan_fichas[ $nombre ] = [ $pid, pm_ff_ficha_plan( $pid, $campos ) ];
}

// --- 2. Guide (es, en, pt) ------------------------------------------------------------------------
$plan = [];
foreach ( $guias as $i => $g ) {
	if ( $p = pm_ff_guia_plan( 'rio-gallegos', $i, $g, $aplicar ) ) {
		$plan[ $i ] = $p;
	}
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
foreach ( $plan as $i => $x ) {
	foreach ( $x['estados'] as $e ) {
		false !== strpos( $e, 'falta' ) && WP_CLI::error( "no escribo la guía ($i): $e" );
	}
}

// --- Apply ----------------------------------------------------------------------------------------
foreach ( $plan_fichas as $nombre => [ $pid, $cambios ] ) {
	if ( ! $pid ) {
		$pid = pm_ff_crear( $nombre, $cambios );
		$plan_fichas[ $nombre ][0] = $pid;
		WP_CLI::log( "✓ ficha «{$nombre}» creada #$pid" );
		continue;
	}
	foreach ( $cambios as $k => $v ) {
		update_field( $k, $v, $pid );
	}
}
$t = time();
foreach ( $plan as $i => $x ) {
	pm_ff_guia_aplicar( $x, $hoy );
}
foreach ( $plan as $i => $x ) {
	update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
}

// --- Read back ------------------------------------------------------------------------------------
$ok = true;
// Karku: whatever the card prints for the public, in any language, must carry no rate.
$kid = $plan_fichas['Karku Fly Fishing'][0];
$pub = '';
foreach ( [ '', '_en', '_pt' ] as $s ) {
	foreach ( [ 'descripcion', 'precio', 'prest_temporada', 'prest_programa', 'prest_estadia_minima', 'prest_acompanante', 'prest_no_incluye' ] as $k ) {
		$pub .= ' ' . pm_campo( $k . $s, $kid );
	}
}
$kk = ! preg_match( '/5[.,]500|US\$ ?5|USD ?5/', $pub ) && '' === trim( (string) pm_campo( 'prest_acompanante', $kid ) ) && '' === trim( (string) pm_campo( 'precio', $kid ) );
WP_CLI::log( ( $kk ? 'VERIFIED' : 'FALLÓ' ) . ': Karku: ningún texto público (es/en/pt) lleva tarifa ni acompañante' );
$ok = $ok && $kk;
foreach ( $fichas as $nombre => $campos ) {
	$pid = $plan_fichas[ $nombre ][0];
	$ok  = pm_ff_ficha_verificar( $pid, $nombre, $campos ) && $ok;
	$r   = null;
	foreach ( pm_prestadores_destino( 'rio-gallegos' ) as $x ) {
		if ( (int) $x['id'] === $pid ) {
			$r = $x;
		}
	}
	$esperado_acuerdo = 'Karku Fly Fishing' === $nombre;
	$volcado = $r ? wp_json_encode( $r, JSON_UNESCAPED_UNICODE ) : '';
	$sin_tarifa_ajena = ( 'Estancia Las Buitreras (Solid Adventures)' === $nombre ) || ! preg_match( '/5[.,]500|3[.,]490/', $volcado );
	$bien = $r && pm_prestador_publico( $r ) && ( $esperado_acuerdo ? ( $r['acuerdo'] && $r['consentimiento'] ) : ( ! $r['acuerdo'] && ! $r['consentimiento'] ) ) && $sin_tarifa_ajena && ( 'Karku Fly Fishing' !== $nombre || ( '' === $r['precio'] && '' === $r['acompanante'] ) ) && ( 'Estancia Glencross (Solid Adventures)' !== $nombre || ( '' === $r['precio'] && '' === (string) $r['precio_fecha'] ) );
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» en la lista de rio-gallegos como información pública (publico=" . (int) ( $r['publico'] ?? 0 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ') capacidad=' . ( $r['capacidad'] ?? '' ) . ' unidad=' . ( $r['capacidad_unidad'] ?? '' ) . ' precio_fecha=' . ( $r['precio_fecha'] ?? '' ) . ' acompañante="' . ( $r['acompanante'] ?? '' ) . '"' );
	$ok = $ok && $bien;
}
foreach ( $plan as $i => $x ) {
	$ok = pm_ff_guia_verificar( $x ) && $ok;
	$p  = get_post( $x['id'] );
	$sin = ! preg_match( '/3[.,]490|5[.,]500/', $p->post_content . wp_json_encode( pm_filas( 'faqs', $x['id'] ), JSON_UNESCAPED_UNICODE ) );
	WP_CLI::log( ( $sin ? 'VERIFIED' : 'FALLÓ' ) . ": guía rio-gallegos ({$i}) sin 3.490 ni 5.500 en el cuerpo ni en las FAQ" );
	$ok = $ok && $sin;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
