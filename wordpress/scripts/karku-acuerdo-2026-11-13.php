<?php
/**
 * Karku Fly Fishing: first-hand data and agreement, 13/11/2026. Source: the operator's own emails to Pescamapa (bookings desk) on
 * 13/11/2026, after it reviewed its listing. No person's name, email or phone is written anywhere.
 *
 * Facts (all from those emails): normal programme Saturday to Saturday, also arranges longer or shorter stays subject to availability;
 * receives 4 anglers a week; sells from December; open rods as of 13/11/2026 for 2027: 2-9 Jan (2), 4-13 Feb (4, as the operator wrote it),
 * 6-13 Mar (4), 13-20 Mar (4), 27 Mar-3 Apr (2). Agreement by email: 20 % commission on bookings of anglers who arrive through Pescamapa,
 * the operator recognises clients who mention Pescamapa (no codes or links), Pescamapa may also handle the sale. The operator asked that its
 * RATES ARE NOT PUBLISHED on any website: they are given only to an angler who asks for a quote. So precio / precio_fecha stay empty and the rate
 * lives ONLY in the internal notes, marked "NO PUBLICAR". Nothing about non-angler rates or gear loans (asked, not answered).
 *
 * What it writes (only fields that exist on main; no contact data):
 *  - the pm_prestador card (descripcion / _en / _pt, prest_temporada / _en / _pt, capacidad cleared, estado "acuerdo", prest_consentimiento on,
 *    comision, verificado, notas). Public effect of consent + estado=acuerdo: see docs/prestadores.md ("Acuerdos con operadores").
 *  - the Río Gallegos guide (es, en, pt): the sentences that said Karku does not publish its rate "in the site we read", and the framing
 *    sentence that said the listed operators are all unverified.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = karku-acuerdo-2026-11-13).
 */

$aplicar = false;
$hoy     = '20261113';
$nombre  = 'Karku Fly Fishing';

$campos = [
	'descripcion' => 'Campamento de pesca con mosca en una estancia centenaria a 50 minutos del aeropuerto de Río Gallegos, sobre el río Gallegos. Recibe 4 pescadores por semana. Según su sitio, tiene más de 40 km de agua de acceso exclusivo y más de 40 pozones con nombre. Aloja en una cabaña con dos habitaciones individuales y una doble, con baño compartido, energía solar y Wi-Fi. Incluye traslados desde el aeropuerto, guías, desayuno, almuerzo y cena, y se ocupa de los permisos; no incluye lavandería, equipo, bebidas alcohólicas ni propinas. Pide un 50 % de depósito no reembolsable. El programa normal va de sábado a sábado, y el operador también arma estadías más largas o más cortas, según disponibilidad (nos lo confirmó el 13/11/2026). Pidió que su tarifa no se publique: la da a quien consulta por una cotización. Plazas libres al 13/11/2026 (2027): del 2 al 9 de enero (2 cañas); del 4 al 13 de febrero (4 cañas); del 6 al 13 de marzo (4 cañas); del 13 al 20 de marzo (4 cañas); del 27 de marzo al 3 de abril (2 cañas). Consultá por otras fechas o estadías más cortas.',
	'descripcion_en' => 'Fly-fishing camp on a century-old estancia 50 minutes from Río Gallegos airport, on the Río Gallegos. It takes 4 anglers a week. According to its website it has more than 40 km of exclusive water and more than 40 named pools. It sleeps guests in a cabin with two single rooms and one double, shared bathroom, solar power and Wi-Fi. It includes airport transfers, guides, breakfast, lunch and dinner, and takes care of permits; it does not include laundry, equipment, alcoholic drinks or tips. It asks for a 50% non-refundable deposit. The normal programme runs Saturday to Saturday, and the operator also arranges longer or shorter stays, subject to availability (it confirmed this to us on 13/11/2026). It asked that its rate not be published: it gives it to anglers who ask for a quote. Open rods as of 13/11/2026 (2027): 2 to 9 January (2 rods); 4 to 13 February (4 rods); 6 to 13 March (4 rods); 13 to 20 March (4 rods); 27 March to 3 April (2 rods). Ask about other dates or shorter stays.',
	'descripcion_pt' => 'Acampamento de pesca com mosca numa estância centenária a 50 minutos do aeroporto de Río Gallegos, no rio Gallegos. Recebe 4 pescadores por semana. Segundo o seu site, tem mais de 40 km de água de acesso exclusivo e mais de 40 poços com nome. Hospeda numa cabana com dois quartos individuais e um duplo, banheiro compartilhado, energia solar e Wi-Fi. Inclui traslados desde o aeroporto, guias, café da manhã, almoço e jantar, e cuida das licenças; não inclui lavanderia, equipamento, bebidas alcoólicas nem gorjetas. Pede 50 % de depósito não reembolsável. O programa normal vai de sábado a sábado, e o operador também monta estadias mais longas ou mais curtas, conforme a disponibilidade (nos confirmou em 13/11/2026). Pediu que a sua tarifa não seja publicada: informa a quem pede uma cotação. Vagas livres em 13/11/2026 (2027): de 2 a 9 de janeiro (2 varas); de 4 a 13 de fevereiro (4 varas); de 6 a 13 de março (4 varas); de 13 a 20 de março (4 varas); de 27 de março a 3 de abril (2 varas). Consulte outras datas ou estadias mais curtas.',
	'prest_temporada' => 'Desde diciembre; la temporada oficial del río va del 1 de noviembre al 30 de mayo',
	'prest_temporada_en' => 'From December; the official river season runs from 1 November to 30 May',
	'prest_temporada_pt' => 'A partir de dezembro; a temporada oficial do rio vai de 1º de novembro a 30 de maio',
	'precio' => '',
	'precio_fecha' => '',
	'capacidad' => '',
	'estado' => 'acuerdo',
	'prest_consentimiento' => 1,
	'comision' => '20 % sobre reservas de pescadores que llegan por Pescamapa (acordado por correo, 13/11/2026)',
	'verificado' => $hoy,
	'notas' => 'Listado con información pública (sitio leído el 4/11/2026) y, desde el 13/11/2026, datos de primera mano del operador por correo a Pescamapa (mesa de reservas). CONFIRMADO por el operador el 13/11/2026: revisó la ficha y pidió correcciones; programa normal de sábado a sábado, también arma estadías más largas o más cortas según disponibilidad; vende desde diciembre; recibe 4 pescadores por semana; plazas libres 2027 (están en la descripción, con fecha). ACUERDO (13/11/2026, por correo): 20 % de comisión sobre reservas de pescadores que llegan por Pescamapa; el operador reconoce a los clientes que mencionan Pescamapa, sin códigos ni enlaces; Pescamapa también puede encargarse de la venta. TARIFA: US$ 5.500 por pescador, semana completa, 2027. NO PUBLICAR (pedido del operador, 13/11/2026): la tarifa solo se da a quien pide una cotización; ni en la ficha, ni en la guía, ni en las FAQ, ni en llms.txt. SIGUE DEL SITIO (leído el 4/11, sin reconfirmar): más de 40 km de agua y más de 40 pozones (así los describe el operador), cabaña con dos individuales y una doble, baño compartido, energía solar y Wi-Fi, qué incluye y qué no, 50 % de depósito no reembolsable, 50 minutos desde el aeropuerto, permisos. SIN RESPUESTA (se preguntó): tarifa para acompañantes que no pescan y préstamo de equipo; no se publica nada de eso. El contacto del operador no se publica. Falta: pasarle la ficha final y definir cómo se factura cuando vendamos nosotros.',
];

function pm_kk_cambiar( string $texto, string $viejo, string $nuevo ): array {
	if ( false !== strpos( $texto, $nuevo ) ) {
		return [ $texto, 'ya' ];
	}
	if ( false === strpos( $texto, $viejo ) ) {
		return [ $texto, 'falta' ];
	}
	return [ str_replace( $viejo, $nuevo, $texto ), 'cambia' ];
}

// --- 1. The card ---------------------------------------------------------------------------------
$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
if ( 1 !== count( $ids ) ) {
	WP_CLI::error( 'Se esperaba una sola ficha "' . $nombre . '", hay ' . count( $ids ) );
}
$pid = (int) $ids[0];
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha $nombre #$pid (publico=" . (int) pm_campo( 'prest_publico', $pid ) . ' consentimiento=' . (int) pm_campo( 'prest_consentimiento', $pid ) . ' estado=' . pm_campo( 'estado', $pid ) . ')' );
$ver     = fn( $v ) => is_array( $v ) ? implode( ',', $v ) : (string) $v;
$cambios = [];
foreach ( $campos as $k => $nuevo ) {
	$antes = pm_campo( $k, $pid );
	$norm  = in_array( $k, [ 'precio_fecha', 'verificado' ], true ) ? fn( $v ) => preg_replace( '/\D/', '', (string) $v )
		: ( 'prest_consentimiento' === $k ? fn( $v ) => (string) (int) $v : fn( $v ) => (string) $v );
	$igual = $norm( $antes ) === $norm( $nuevo );
	WP_CLI::log( ( $igual ? '  = ' : '  ~ ' ) . $k . ( $igual ? ' sin cambios' : '' ) );
	if ( ! $igual ) {
		WP_CLI::log( '      ANTES:   ' . $ver( $antes ) );
		WP_CLI::log( '      DESPUÉS: ' . $ver( $nuevo ) );
		$cambios[ $k ] = $nuevo;
	}
}

// --- 2. The Río Gallegos guide -----------------------------------------------------------------
$guias = [
	'es' => [
		'cuerpo' => [
			[ 'Abajo están los que encontramos con información pública, por eso figuran marcados como sin verificar. Una tarifa se muestra solo cuando sale de una fuente del propio operador y con su temporada: la de Estancia Las Buitreras viene del programa 2027 que nos envió Solid Adventures el 13/11/2026; Karku Fly Fishing no publica la suya.',
			  'Abajo están los que encontramos con información pública; los que figuran marcados como sin verificar todavía no los confirmó el propio operador. Una tarifa se muestra solo cuando sale de una fuente del propio operador y con su temporada: la de Estancia Las Buitreras viene del programa 2027 que nos envió Solid Adventures el 13/11/2026; Karku Fly Fishing pidió no publicar la suya y la da a quien consulta por una cotización.' ],
		],
		'faqs' => [
			'¿Cuánto cuesta una semana en un lodge del Gallegos?' => [
				'Karku Fly Fishing no publica su tarifa en el sitio que leímos.',
				'Karku Fly Fishing pidió no publicar su tarifa: la da a quien consulta por una cotización.' ],
		],
	],
	'en' => [
		'cuerpo' => [
			[ 'Below are the ones we found with public information, which is why they are marked as unverified. A rate is shown only when it comes from the operator\'s own source with its season: the Estancia Las Buitreras rate comes from the 2027 programme Solid Adventures sent us on 13/11/2026; Karku Fly Fishing does not publish its own.',
			  'Below are the ones we found with public information; those marked as unverified have not yet been confirmed by the operator itself. A rate is shown only when it comes from the operator\'s own source with its season: the Estancia Las Buitreras rate comes from the 2027 programme Solid Adventures sent us on 13/11/2026; Karku Fly Fishing asked us not to publish its own and gives it to anglers who ask for a quote.' ],
		],
		'faqs' => [
			'How much does a week at a Gallegos lodge cost?' => [
				'Karku Fly Fishing publishes no rate on the site we read.',
				'Karku Fly Fishing asked us not to publish its rate: it gives it to anglers who ask for a quote.' ],
		],
	],
	'pt' => [
		'cuerpo' => [
			[ 'Abaixo estão os que encontramos com informação pública, por isso aparecem marcados como sem verificação. Uma tarifa só é mostrada quando vem de uma fonte do próprio operador e com sua temporada: a da Estancia Las Buitreras vem do programa 2027 que a Solid Adventures nos enviou em 13/11/2026; a Karku Fly Fishing não publica a sua.',
			  'Abaixo estão os que encontramos com informação pública; os que aparecem marcados como sem verificação ainda não foram confirmados pelo próprio operador. Uma tarifa só é mostrada quando vem de uma fonte do próprio operador e com sua temporada: a da Estancia Las Buitreras vem do programa 2027 que a Solid Adventures nos enviou em 13/11/2026; a Karku Fly Fishing pediu que não publiquemos a sua e informa a quem pede uma cotação.' ],
		],
		'faqs' => [
			'Quanto custa uma semana em um lodge do Gallegos?' => [
				'A Karku Fly Fishing não publica a sua tarifa no site que lemos.',
				'A Karku Fly Fishing pediu que não publiquemos a sua tarifa: informa a quem pede uma cotação.' ],
		],
	],
];

$plan = [];
foreach ( $guias as $i => $g ) {
	$p = pm_buscar( 'pm_destino', 'rio-gallegos', $i );
	if ( ! $p ) {
		WP_CLI::log( "guía rio-gallegos ($i): no existe, se omite" );
		continue;
	}
	$texto   = $p->post_content;
	$estados = [];
	foreach ( $g['cuerpo'] as $n => [ $viejo, $nuevo ] ) {
		[ $texto, $e ] = pm_kk_cambiar( $texto, $viejo, $nuevo );
		$estados[]     = "cuerpo #$n: $e";
		'cambia' === $e && WP_CLI::log( "  guía ($i) cuerpo #$n\n      ANTES:   $viejo\n      DESPUÉS: $nuevo" );
	}
	$filas = pm_filas( 'faqs', $p->ID );
	foreach ( $g['faqs'] as $pregunta => [ $viejo, $nuevo ] ) {
		$hallada = false;
		foreach ( $filas as $k => $f ) {
			if ( ( $f['pregunta'] ?? '' ) === $pregunta ) {
				$hallada = true;
				[ $filas[ $k ]['respuesta'], $e ] = pm_kk_cambiar( (string) $f['respuesta'], $viejo, $nuevo );
				$estados[] = 'FAQ «' . mb_substr( $pregunta, 0, 40 ) . "»: $e";
				'cambia' === $e && WP_CLI::log( "  guía ($i) FAQ «$pregunta»\n      ANTES (respuesta completa):   {$f['respuesta']}\n      DESPUÉS (respuesta completa): {$filas[ $k ]['respuesta']}" );
			}
		}
		$hallada || $estados[] = 'FAQ «' . mb_substr( $pregunta, 0, 40 ) . '»: falta (pregunta no encontrada)';
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía rio-gallegos ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' | ' . implode( ' | ', $estados ) );
	foreach ( $estados as $e ) {
		if ( false !== strpos( $e, 'falta' ) ) {
			WP_CLI::warning( "guía ($i): $e" );
		}
	}
	$plan[ $i ] = [ 'id' => $p->ID, 'texto' => $texto, 'filas' => $filas, 'estados' => $estados ];
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

// --- Apply ---------------------------------------------------------------------------------------
foreach ( $plan as $i => $x ) {
	foreach ( $x['estados'] as $e ) {
		if ( false !== strpos( $e, 'falta' ) ) {
			WP_CLI::error( "no escribo la guía ($i): $e" );
		}
	}
}
foreach ( $cambios as $k => $v ) {
	update_field( $k, $v, $pid );
}
$t = time();
foreach ( $plan as $i => $x ) {
	wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['texto'] ) ] );
	update_field( 'faqs', $x['filas'], $x['id'] );
	update_field( 'actualizado', $hoy, $x['id'] );
}
foreach ( $plan as $i => $x ) {
	update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
}

// --- Read back -----------------------------------------------------------------------------------
$ok = true;
$r  = null;
foreach ( pm_prestadores_destino( 'rio-gallegos' ) as $x ) {
	if ( (int) $x['id'] === $pid ) {
		$r = $x;
	}
}
$volcado = $r ? wp_json_encode( $r, JSON_UNESCAPED_UNICODE ) : '';
$sin_tarifa = '' === $r['precio'] && '' === (string) $r['precio_fecha'] && ! preg_match( '/5[.,]500|US\$ ?5/', $volcado );
$bien = $r && pm_prestador_publico( $r ) && $r['acuerdo'] && $r['consentimiento'] && 0 === $r['capacidad'] && $sin_tarifa;
WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": ficha #$pid en la lista de rio-gallegos | publico=" . (int) $r['publico'] . ' consentimiento=' . (int) $r['consentimiento'] . ' acuerdo=' . (int) $r['acuerdo'] . ' capacidad=' . $r['capacidad'] . ' precio="' . $r['precio'] . '" precio_fecha="' . $r['precio_fecha'] . '" sin cifra de tarifa en la ficha pública=' . (int) $sin_tarifa );
$ok = $ok && $bien;
foreach ( [ 'descripcion', 'descripcion_en', 'descripcion_pt', 'prest_temporada', 'prest_temporada_en', 'prest_temporada_pt', 'comision', 'notas' ] as $k ) {
	$b = (string) pm_campo( $k, $pid ) === (string) $campos[ $k ];
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": ficha campo $k (" . mb_strlen( (string) pm_campo( $k, $pid ) ) . ' caracteres)' );
	$ok = $ok && $b;
}
foreach ( [ 'descripcion', 'descripcion_en', 'descripcion_pt', 'prest_temporada', 'prest_temporada_en', 'prest_temporada_pt' ] as $k ) {
	$b = ! preg_match( '/5[.,]500/', (string) pm_campo( $k, $pid ) );
	$ok = $ok && $b;
}
$b = 'acuerdo' === pm_campo( 'estado', $pid ) && '' === (string) pm_campo( 'capacidad', $pid ) && preg_replace( '/\D/', '', (string) pm_campo( 'verificado', $pid ) ) === $hoy && false !== strpos( (string) pm_campo( 'notas', $pid ), 'NO PUBLICAR' );
WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ': ficha estado=' . pm_campo( 'estado', $pid ) . ' capacidad="' . pm_campo( 'capacidad', $pid ) . '" verificado=' . pm_campo( 'verificado', $pid ) . ' notas con la marca NO PUBLICAR' );
$ok = $ok && $b;
foreach ( $plan as $i => $x ) {
	$p     = get_post( $x['id'] );
	$filas = pm_filas( 'faqs', $x['id'] );
	$bien  = ! preg_match( '/5[.,]500/', $p->post_content . wp_json_encode( $filas, JSON_UNESCAPED_UNICODE ) );
	foreach ( $guias[ $i ]['cuerpo'] as [ , $nuevo ] ) {
		$bien = $bien && false !== strpos( $p->post_content, $nuevo );
	}
	foreach ( $guias[ $i ]['faqs'] as $pregunta => [ , $nuevo ] ) {
		$hit = false;
		foreach ( $filas as $f ) {
			$hit = $hit || ( ( $f['pregunta'] ?? '' ) === $pregunta && false !== strpos( (string) $f['respuesta'], $nuevo ) );
		}
		$bien = $bien && $hit;
	}
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": guía rio-gallegos ($i) #{$x['id']}: " . count( $guias[ $i ]['cuerpo'] ) . ' párrafo y ' . count( $guias[ $i ]['faqs'] ) . ' FAQ leídos de vuelta, sin "5.500" ni "5,500", ' . count( $filas ) . ' FAQs en total ' . get_permalink( $x['id'] ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'APLICADO' );
