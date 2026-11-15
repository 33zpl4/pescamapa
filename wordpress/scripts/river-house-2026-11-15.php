<?php
/**
 * River House (lodge near the city of Río Gallegos, Santa Cruz): NEW card + one sentence in the Río Gallegos guide (es, en, pt), 15/11/2026.
 * Source: what the operator told Pescamapa (its brochure and its emails of 15/11/2026, in which it also accepted EXPRESSLY to be published and offered
 * high-quality photos, not yet received) as passed on by the owner. Everything public is attributed ("según el operador") and limited to what it said:
 * near the city of Río Gallegos; per its brochure it fishes different stretches of the Río Gallegos and its tributaries each day depending on the river,
 * with local guides; public-access stretches easy to reach from the road, so one may sometimes share the water; rents full tackle (rods, reels, lines,
 * waders, boots) and sells flies at the lodge; arranges the fishing licence a few days before arrival from a copy of the angler's passport; most wading
 * spots accessible. NOT said, so NOT written: modalities, season, programme, capacity, languages, what is included, anything about the licence price.
 * NOT PUBLISHED, on any public surface: rates (owner, 15/11/2026: quoted only to the client), phone, email, names of people, and the commission (internal
 * field `comision` only). The read-back fails if any of those shows in a public field. No photo yet: the card has none and is ready for them (field Fotos,
 * "Autor de la foto" = River House, when they arrive).
 * Tier (copies Karku): estado "acuerdo" + prest_consentimiento on => "Operador con acuerdo con Pescamapa", no "sin verificar" notice, first in the list
 * (title order among agreements: Karku, River House), LocalBusiness in the JSON-LD of /guias-de-pesca/rio-gallegos/ (never the guide itself).
 * Equipment sentence in the guide: ONLY for River House (Karku and Solid Adventures were not asked / did not answer).
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = river-house-2026-11-15).
 */

$aplicar = true;
$hoy     = '20261115';

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

$nombre = 'River House';
$campos = [
	'tipo' => [ 'alojamiento', 'operador' ], 'prest_destino' => 'rio-gallegos', 'localidad' => 'Cerca de la ciudad de Río Gallegos, Santa Cruz',
	'descripcion' => 'Lodge cerca de la ciudad de Río Gallegos, en Santa Cruz. Según su folleto, el operador pesca cada día distintos tramos del río Gallegos y de sus afluentes, según cómo esté el río, con guías locales. Dice que hay tramos de acceso público y fáciles de alcanzar desde la ruta, por lo que a veces se puede coincidir con otros pescadores, y que la mayoría de los lugares de vadeo son accesibles. Alquila el equipo completo (cañas, reels, líneas, waders y botas) y vende moscas en el lodge. Gestiona la licencia de pesca unos días antes de la llegada, con la copia del pasaporte del pescador. Todo lo anterior es lo que nos dijo el operador y lo que dice su folleto. Su tarifa no se publica: se cotiza a quien pide opciones.',
	'descripcion_en' => 'Lodge near the city of Río Gallegos, in Santa Cruz. According to its brochure, the operator fishes different stretches of the Río Gallegos and its tributaries each day, depending on how the river is running, with local guides. It says some stretches are publicly accessible and easy to reach from the road, so you may sometimes share the water with other anglers, and that most of the wading spots are accessible. It rents a full set of tackle (rods, reels, lines, waders and boots) and sells flies at the lodge. It arranges the fishing licence a few days before arrival, from a copy of the angler’s passport. All of the above is what the operator told us and what its brochure says. Its rate is not published: it is quoted to anglers who ask for options.',
	'descripcion_pt' => 'Lodge perto da cidade de Río Gallegos, em Santa Cruz. Segundo o folheto, o operador pesca a cada dia trechos diferentes do rio Gallegos e de seus afluentes, conforme as condições do rio, com guias locais. Diz que há trechos de acesso público e fáceis de alcançar pela estrada, por isso às vezes é possível encontrar outros pescadores, e que a maioria dos pontos de vadeio é acessível. Aluga o equipamento completo (varas, reels, linhas, waders e botas) e vende moscas no lodge. Providencia a licença de pesca alguns dias antes da chegada, com a cópia do passaporte do pescador. Tudo isso é o que o operador nos disse e o que diz o folheto. A tarifa não é publicada: é informada a quem pede opções.',
	'prest_especies' => [ 'trucha' ],
	'precio' => '', 'precio_fecha' => '',
	'estado' => 'acuerdo', 'prest_consentimiento' => 1, 'prest_publico' => 0, 'datos_ejemplo' => 0, 'verificado' => $hoy,
	'comision' => '10 % pagado por el lodge al terminar la estadía, sobre lo que abonan los huéspedes por la estancia. La licencia de pesca queda excluida (gasto que el lodge gestiona por cuenta del pescador): lo propuso el dueño y FALTA la confirmación escrita del operador. Acordado por correo el 15/11/2026. INTERNO: no se publica en ninguna página.',
	'notas' => 'Ficha cargada el 15/11/2026 con lo que el operador dijo por correo y en su folleto, tal como lo pasó el dueño; sin sitio leído. El operador aceptó expresamente que se publique su ficha (por eso Aceptó aparecer + Trabajamos juntos) y ofreció fotos en alta calidad, que llegarán por correo o WhatsApp: cuando lleguen, sumarlas en el campo Fotos con «Autor de la foto» = River House (docs/prestadores.md, punto 8 de la estrategia y regla del 13/11/2026). TARIFAS: las informó el operador, NO PUBLICAR (decisión del dueño, 15/11/2026: solo se cotizan al cliente); cifras en docs/prestadores.md, «Acuerdos con operadores». COMISIÓN: ver el campo Acuerdo comercial; no se publica ni el porcentaje. Pendiente: confirmación escrita del operador sobre la exclusión de la licencia; quién factura; capacidad, temporada, modalidades e idiomas (no los dijo, por eso están vacíos); si dice o no que la licencia va incluida en la estancia. No hay contacto publicado ni se escribe acá (teléfono, correo y personas no se copian al repo).',
];

$frase = [
	'es' => ' River House, un lodge cerca de la ciudad de Río Gallegos que aceptó figurar en esta guía, alquila el equipo completo (cañas, reels, líneas, waders y botas), según el operador.',
	'en' => ' River House, a lodge near the city of Río Gallegos that agreed to be listed in this guide, rents a full set of tackle (rods, reels, lines, waders and boots), according to the operator.',
	'pt' => ' A River House, um lodge perto da cidade de Río Gallegos que aceitou figurar neste guia, aluga o equipamento completo (varas, reels, linhas, waders e botas), segundo o operador.',
];
$guias = [
	'es' => [ 'cuerpo' => [ [ 'de fines de enero a principios de abril, y no muestra tarifa.', 'de fines de enero a principios de abril, y no muestra tarifa.' . $frase['es'] ] ] ],
	'en' => [ 'cuerpo' => [ [ 'from late January to early April, and it shows no rate.', 'from late January to early April, and it shows no rate.' . $frase['en'] ] ] ],
	'pt' => [ 'cuerpo' => [ [ 'do fim de janeiro ao começo de abril, e não mostra tarifa.', 'do fim de janeiro ao começo de abril, e não mostra tarifa.' . $frase['pt'] ] ] ],
];

// --- 1. The card ----------------------------------------------------------------------------------
$pid = pm_ff_ficha_id( $nombre );
if ( ! $pid ) {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» NUEVA (no existe), campos:" );
	foreach ( $campos as $k => $v ) {
		WP_CLI::log( "  + $k: " . pm_ff_ver( $v ) );
	}
	$cambios = $campos;
} else {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» #$pid (consentimiento=" . (int) pm_campo( 'prest_consentimiento', $pid ) . ' estado=' . pm_campo( 'estado', $pid ) . ')' );
	$cambios = pm_ff_ficha_plan( $pid, $campos );
}
// The public texts must carry no rate, commission, contact or person (checked before anything is written).
$publico = '';
foreach ( [ 'descripcion', 'descripcion_en', 'descripcion_pt', 'localidad' ] as $k ) {
	$publico .= ' ' . $campos[ $k ];
}
foreach ( $frase as $f ) {
	$publico .= ' ' . $f;
}
$prohibido = '/\d{1,3}[.,]\d{3}|US\$|USD|\b350\b|\b2\.?000\b|\d ?%|comisi|commission|comiss|@|\+\d|\btel[eé]fono|whatsapp|\bphone\b/iu';
if ( preg_match( $prohibido, $publico, $m ) ) {
	WP_CLI::error( 'el texto público lleva algo que no se publica: «' . $m[0] . '»' );
}
WP_CLI::log( 'OK: el texto público (es/en/pt y la frase de la guía) no lleva cifras, comisión, contacto ni nombres.' );

// --- 2. Guide (es, en, pt) ---------------------------------------------------------------------------
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

// --- Apply -------------------------------------------------------------------------------------------
if ( ! $pid ) {
	$pid = pm_ff_crear( $nombre, $cambios );
	WP_CLI::log( "✓ ficha «{$nombre}» creada #$pid" );
} else {
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

// --- Read back ---------------------------------------------------------------------------------------
$ok = pm_ff_ficha_verificar( $pid, $nombre, $campos );
$orden = [];
$r     = null;
foreach ( pm_prestadores_destino( 'rio-gallegos' ) as $x ) {
	$orden[] = $x['nombre'] . ( $x['acuerdo'] && $x['consentimiento'] ? ' [acuerdo]' : '' );
	if ( (int) $x['id'] === $pid ) {
		$r = $x;
	}
}
WP_CLI::log( 'Orden de la lista de rio-gallegos: ' . implode( ' > ', $orden ) );
$volcado = $r ? wp_json_encode( $r, JSON_UNESCAPED_UNICODE ) : '';
$pos     = array_search( $nombre . ' [acuerdo]', $orden, true );
$bien    = $r && pm_prestador_publico( $r ) && $r['acuerdo'] && $r['consentimiento'] && ! $r['publico'] && '' === $r['precio'] && '' === (string) $r['precio_fecha'] && [] === $r['fotos'] && 0 === $r['capacidad']
	&& false !== $pos && $pos <= 1 && ! preg_match( $prohibido, $volcado );
foreach ( $orden as $n => $o ) {
	// every agreement goes above every public-information card
	if ( false === strpos( $o, '[acuerdo]' ) ) {
		$bien = $bien && $n > (int) $pos;
	}
}
WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» en la lista de rio-gallegos | posición=" . ( false === $pos ? 'no está' : $pos + 1 ) . ' publico=' . (int) ( $r['publico'] ?? 0 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ' precio="' . ( $r['precio'] ?? '' ) . '" fotos=' . count( $r['fotos'] ?? [] ) . ' sin cifras, comisión ni contacto en la ficha pública=' . (int) ( $r && ! preg_match( $prohibido, $volcado ) ) );
$ok = $ok && $bien;
foreach ( $plan as $i => $x ) {
	$ok = pm_ff_guia_verificar( $x ) && $ok;
	$p  = get_post( $x['id'] );
	$sin = ! preg_match( $prohibido, $frase[ $i ] ) && false !== strpos( $p->post_content, trim( $frase[ $i ] ) );
	WP_CLI::log( ( $sin ? 'VERIFIED' : 'FALLÓ' ) . ": guía rio-gallegos ({$i}) lleva la frase de equipo de River House, sin cifras ni comisión" );
	$ok = $ok && $sin;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
