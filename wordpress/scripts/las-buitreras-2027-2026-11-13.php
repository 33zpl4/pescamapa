<?php
/**
 * Estancia Las Buitreras (Solid Adventures): first-hand data, 13/11/2026. Source: the operator's own programme "Las Buitreras – Season 2027"
 * (PDF, page 14 = itinerary, price, included / not included), sent by Solid Adventures by email to Pescamapa on 13/11/2026 in answer to our
 * enquiry. The operator also confirmed by email that it runs only full weeks and that January 2027 was already fully booked. The PDF is not
 * in the repo. Facts: season 30/12/2026 to 24/4/2027; 7 nights / 6 guided fishing days, Saturday to Saturday; max 12 anglers a week;
 * 5 double + 4 single rooms with private bathroom (the programme supersedes the website's 6 + 2); US$ 9,990 per angler (2 anglers with 1 guide),
 * non-fishing guest US$ 3,000, single room +US$ 500 on availability; return transfer from El Calafate domestic airport (3-4 h minibus);
 * wading, single or double-hand (recommended 7 wt 12 ft double-hand); tackle NOT provided, flies and tackle sold at the lodge shop.
 *
 * What it writes (nothing else, no contact data):
 *  - the pm_prestador card (one post, texts per language in descripcion / _en / _pt, precio, prest_temporada): text, price + date + source label,
 *    season, included, capacity cleared (the card renders it as "per boat"), notes, state "contactado", verified date, one photo with credit.
 *    Still "listado con información pública": no consent, no agreement.
 *  - the Río Gallegos guide (es, en, pt): the sentences that said Las Buitreras publishes no rate, and "how to get there" (Las Buitreras
 *    transfer is from El Calafate, not Río Gallegos). Replaces exact sentences in the live text; reports any it does not find.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = las-buitreras-2027-2026-11-13).
 */

$aplicar = false;
$hoy     = '20261113';
$nombre  = 'Estancia Las Buitreras (Solid Adventures)';
$carpeta = '/tmp/pm-fotos';
$foto    = [
	'archivo' => 'las-buitreras-rio-gallegos-paisaje.jpg', 'autor' => 'Solid Adventures', 'origen' => 'cedida',
	'titulo'  => 'Río Gallegos meandering through the Patagonian pampa, seen past a fly rod and reel (Las Buitreras programme 2027)',
	'alt'     => 'The Río Gallegos winding through golden pampa under a soft evening sky, seen past a fly rod and reel in the foreground',
];

$campos = [
	'descripcion' => 'Lodge sobre el río Gallegos que opera Solid Adventures, a unos diez minutos a pie del río. Según su programa 2027 tiene cinco habitaciones dobles y cuatro individuales, todas con baño privado, y recibe un máximo de 12 pescadores por semana. El operador describe más de 40 km de agua y más de 50 pozones con nombre. Vende semanas completas de sábado a sábado (siete noches, seis días de pesca con guía), y el 13/11/2026 nos confirmó que no arma estadías más cortas y que enero de 2027 ya estaba completo. Se pesca vadeando, con caña de una o dos manos (recomienda una de dos manos 7 wt de 12 pies); no presta equipo y en la tienda del lodge vende moscas y equipo. Los guías hablan inglés. El traslado de ida y vuelta sale del aeropuerto doméstico de El Calafate (3 a 4 horas en minibús); fuera del horario recomendado cuesta unos US$ 300 por auto. Incluye las siete noches, los seis días de pesca con guía, todas las comidas, snacks, gaseosas, vino y cerveza, y la licencia de pesca; no incluye vuelos, equipo, bebidas espirituosas ni propinas. Ofrece Glencross, otro de sus programas, como extensión de la semana.',
	'descripcion_en' => 'Lodge on the Río Gallegos run by Solid Adventures, about a ten-minute walk from the river. According to its 2027 programme it has five double rooms and four single rooms, all with private bathrooms, and takes a maximum of 12 anglers a week. The operator describes more than 40 km of water and more than 50 named pools. It sells full weeks from Saturday to Saturday (seven nights, six guided fishing days), and on 13/11/2026 it confirmed to us that it does not run shorter stays and that January 2027 was already fully booked. You fish wading, with a single- or double-hand rod (it recommends a 7 wt 12 ft double-hand); it does not provide tackle, and the lodge shop sells flies and tackle. The guides speak English. The return transfer leaves from El Calafate domestic airport (3 to 4 hours by minibus); outside the recommended schedule it costs about US$ 300 per car. It includes the seven nights, six guided fishing days, all meals, snacks, soft drinks, wine and beer, and the fishing licence; it does not include flights, tackle, spirits or tips. It offers Glencross, another of its programmes, as an extension to the week.',
	'descripcion_pt' => 'Lodge no rio Gallegos operado pela Solid Adventures, a cerca de dez minutos a pé do rio. Segundo o seu programa 2027 tem cinco quartos duplos e quatro individuais, todos com banheiro privativo, e recebe no máximo 12 pescadores por semana. O operador descreve mais de 40 km de água e mais de 50 poços com nome. Vende semanas completas de sábado a sábado (sete noites, seis dias de pesca com guia), e em 13/11/2026 nos confirmou que não monta estadias mais curtas e que janeiro de 2027 já estava lotado. Pesca-se com vadeio, com vara de uma ou duas mãos (recomenda uma de duas mãos 7 wt de 12 pés); não empresta equipamento e a loja do lodge vende moscas e equipamento. Os guias falam inglês. O traslado de ida e volta sai do aeroporto doméstico de El Calafate (3 a 4 horas de minibus); fora do horário recomendado custa cerca de US$ 300 por carro. Inclui as sete noites, os seis dias de pesca com guia, todas as refeições, lanches, refrigerantes, vinho e cerveja, e a licença de pesca; não inclui voos, equipamento, destilados nem gorjetas. Oferece a Glencross, outro programa seu, como extensão da semana.',
	'prest_temporada' => 'Del 30 de diciembre de 2026 al 24 de abril de 2027, solo semanas completas de sábado a sábado',
	'prest_temporada_en' => '30 December 2026 to 24 April 2027, full Saturday-to-Saturday weeks only',
	'prest_temporada_pt' => 'De 30 de dezembro de 2026 a 24 de abril de 2027, só semanas completas de sábado a sábado',
	'precio' => 'US$ 9.990 por pescador por semana completa (7 noches, 6 días de pesca con guía; 2 pescadores por guía). Acompañante que no pesca: US$ 3.000. Habitación individual: suplemento de US$ 500, según disponibilidad. Fuente: programa 2027 enviado por Solid Adventures, 13/11/2026',
	'precio_en' => 'US$ 9,990 per angler for a full week (7 nights, 6 guided fishing days; 2 anglers per guide). Non-fishing guest: US$ 3,000. Single room: US$ 500 supplement, subject to availability. Source: 2027 programme sent by Solid Adventures, 13/11/2026',
	'precio_pt' => 'US$ 9.990 por pescador por semana completa (7 noites, 6 dias de pesca com guia; 2 pescadores por guia). Acompanhante que não pesca: US$ 3.000. Quarto individual: suplemento de US$ 500, conforme disponibilidade. Fonte: programa 2027 enviado pela Solid Adventures, 13/11/2026',
	'precio_fecha' => $hoy,
	'incluye' => [ 'alojamiento', 'comida', 'bebidas', 'traslados', 'licencia' ],
	'capacidad' => '',
	'estado' => 'contactado',
	'verificado' => $hoy,
	'notas' => 'Listado con información pública (sitio leído el 4/11/2026). 13/11/2026: Solid Adventures contestó por mail una consulta de Pescamapa y mandó el programa "Las Buitreras – Season 2027" (PDF, no se versiona en el repo; la página 14 tiene tarifa, temporada e incluye / no incluye). De ahí salen temporada, tarifa, habitaciones (5 dobles y 4 individuales; el sitio decía 6 y 2, vale el programa), incluye y no incluye. Por mail confirmaron que solo arman semanas completas y que enero de 2027 estaba completo. No aprobaron el listado: sigue como información pública, sin consentimiento ni acuerdo. Foto de la página 5 del programa con el crédito "Solid Adventures", se quita si la piden. El contacto del operador no se publica. Falta: pedirle que revise la ficha y hablar de un acuerdo.',
];

function pm_lb_cambiar( string $texto, string $viejo, string $nuevo ): array {
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
$cortar = fn( $v ) => mb_substr( is_array( $v ) ? implode( ',', $v ) : (string) $v, 0, 160 );
$cambios = [];
foreach ( $campos as $k => $nuevo ) {
	$antes = pm_campo( $k, $pid );
	$igual = is_array( $nuevo ) ? ( (array) $antes === $nuevo ) : ( (string) $antes === (string) $nuevo );
	WP_CLI::log( ( $igual ? '  = ' : '  ~ ' ) . "$k" . ( $igual ? ' sin cambios' : "\n      ANTES:   " . $cortar( $antes ) . "\n      DESPUÉS: " . $cortar( $nuevo ) ) );
	if ( ! $igual ) {
		$cambios[ $k ] = $nuevo;
	}
}

// Photo (gallery field "fotos"; credit on the attachment, shown as "Foto: Solid Adventures").
$ya   = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $foto['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
$att  = $ya ? (int) $ya[0] : 0;
$ruta = "$carpeta/{$foto['archivo']}";
$gal  = array_map( 'intval', (array) ( pm_campo( 'fotos', $pid ) ?: [] ) );
WP_CLI::log( "  foto: galería hoy [" . implode( ',', $gal ) . '], adjunto ' . ( $att ? "#$att ya cargado" : ( is_file( $ruta ) ? 'por cargar (' . size_format( filesize( $ruta ) ) . ')' : 'FALTA ' . $foto['archivo'] ) ) );
if ( ! $att && ! is_file( $ruta ) ) {
	WP_CLI::error( "falta {$foto['archivo']} en $carpeta" );
}

// --- 2. The Río Gallegos guide -----------------------------------------------------------------
$guias = [
	'es' => [
		'cuerpo' => [
			[ 'Abajo están los que encontramos con información pública; todavía no los contactamos, por eso figuran marcados. Una tarifa se muestra solo cuando el operador la publica con su temporada, y ninguno de estos dos publica la suya.',
			  'Abajo están los que encontramos con información pública, por eso figuran marcados como sin verificar. Una tarifa se muestra solo cuando sale de una fuente del propio operador y con su temporada: la de Estancia Las Buitreras viene del programa 2027 que nos envió Solid Adventures el 13/11/2026; Karku Fly Fishing no publica la suya.' ],
			[ 'Estancia Las Buitreras está a unos diez minutos a pie del río, según su operador.',
			  'Estancia Las Buitreras está a unos diez minutos a pie del río y, según su programa 2027, el traslado de ida y vuelta sale del aeropuerto de El Calafate, en un viaje de 3 a 4 horas en minibús.' ],
		],
		'faqs' => [
			'¿Cuánto cuesta una semana en un lodge del Gallegos?' => [
				'No lo podemos decir con una fuente del propio operador: Karku Fly Fishing y Estancia Las Buitreras no publican sus tarifas en los sitios que leímos.',
				'Estancia Las Buitreras: US$ 9.990 por pescador por una semana completa (siete noches y seis días de pesca con guía, de sábado a sábado, con dos pescadores por guía), según el programa 2027 que nos envió Solid Adventures el 13/11/2026. Un acompañante que no pesca paga US$ 3.000 y la habitación individual suma US$ 500, según disponibilidad. Karku Fly Fishing no publica su tarifa en el sitio que leímos.' ],
			'¿Qué incluye un lodge del Gallegos?' => [
				'y el saldo 90 días antes.',
				'y el saldo 90 días antes. Según el programa 2027 de Estancia Las Buitreras: siete noches, seis días de pesca con guía, todas las comidas, snacks, gaseosas, vino y cerveza, licencia de pesca y traslado de ida y vuelta desde el aeropuerto de El Calafate; no incluye vuelos, equipo, bebidas espirituosas ni propinas.' ],
			'¿Cómo llego?' => [
				'Por el aeropuerto de Río Gallegos. Karku Fly Fishing dice que su estancia está a 50 minutos en auto y que ofrece los traslados.',
				'Para Karku Fly Fishing, por el aeropuerto de Río Gallegos: dice que su estancia está a 50 minutos en auto y que ofrece los traslados. Estancia Las Buitreras, en cambio, informa en su programa 2027 un traslado de ida y vuelta desde el aeropuerto de El Calafate, de 3 a 4 horas en minibús.' ],
		],
	],
	'en' => [
		'cuerpo' => [
			[ 'Below are the ones we found with public information; we have not contacted them yet, which is why they are marked. A rate is shown only when the operator publishes it with its season, and neither of these two publishes one.',
			  'Below are the ones we found with public information, which is why they are marked as unverified. A rate is shown only when it comes from the operator\'s own source with its season: the Estancia Las Buitreras rate comes from the 2027 programme Solid Adventures sent us on 13/11/2026; Karku Fly Fishing does not publish its own.' ],
			[ 'Estancia Las Buitreras is about a ten-minute walk from the river, according to its operator.',
			  'Estancia Las Buitreras is about a ten-minute walk from the river and, according to its 2027 programme, the return transfer leaves from El Calafate airport, a 3 to 4 hour minibus ride.' ],
		],
		'faqs' => [
			'How much does a week at a Gallegos lodge cost?' => [
				'We cannot say from the operator\'s own source: Karku Fly Fishing and Estancia Las Buitreras publish no rates on the sites we read.',
				'Estancia Las Buitreras: US$ 9,990 per angler for a full week (seven nights and six guided fishing days, Saturday to Saturday, with two anglers per guide), according to the 2027 programme Solid Adventures sent us on 13/11/2026. A non-fishing guest pays US$ 3,000 and a single room adds US$ 500, subject to availability. Karku Fly Fishing publishes no rate on the site we read.' ],
			'What does a Gallegos lodge include?' => [
				'and the balance 90 days before.',
				'and the balance 90 days before. According to the 2027 programme of Estancia Las Buitreras: seven nights, six guided fishing days, all meals, snacks, soft drinks, wine and beer, fishing licence and a return transfer from El Calafate airport; it does not include flights, tackle, spirits or tips.' ],
			'How do I get there?' => [
				'Through Río Gallegos airport. Karku Fly Fishing says its estancia is a 50-minute drive and that it provides transfers.',
				'For Karku Fly Fishing, through Río Gallegos airport: it says its estancia is a 50-minute drive and that it provides transfers. Estancia Las Buitreras, by contrast, reports in its 2027 programme a return transfer from El Calafate airport, a 3 to 4 hour minibus ride.' ],
		],
	],
	'pt' => [
		'cuerpo' => [
			[ 'Abaixo estão os que encontramos com informação pública; ainda não os contatamos, por isso aparecem marcados. Uma tarifa só é mostrada quando o operador a publica com sua temporada, e nenhum destes dois publica a sua.',
			  'Abaixo estão os que encontramos com informação pública, por isso aparecem marcados como sem verificação. Uma tarifa só é mostrada quando vem de uma fonte do próprio operador e com sua temporada: a da Estancia Las Buitreras vem do programa 2027 que a Solid Adventures nos enviou em 13/11/2026; a Karku Fly Fishing não publica a sua.' ],
			[ 'A Estancia Las Buitreras fica a cerca de dez minutos a pé do rio, segundo o seu operador.',
			  'A Estancia Las Buitreras fica a cerca de dez minutos a pé do rio e, segundo o seu programa 2027, o traslado de ida e volta sai do aeroporto de El Calafate, numa viagem de 3 a 4 horas de minibus.' ],
		],
		'faqs' => [
			'Quanto custa uma semana em um lodge do Gallegos?' => [
				'Não podemos dizer com uma fonte do próprio operador: a Karku Fly Fishing e a Estancia Las Buitreras não publicam suas tarifas nos sites que lemos.',
				'Estancia Las Buitreras: US$ 9.990 por pescador por uma semana completa (sete noites e seis dias de pesca com guia, de sábado a sábado, com dois pescadores por guia), segundo o programa 2027 que a Solid Adventures nos enviou em 13/11/2026. Um acompanhante que não pesca paga US$ 3.000 e o quarto individual soma US$ 500, conforme disponibilidade. A Karku Fly Fishing não publica a sua tarifa no site que lemos.' ],
			'O que um lodge do Gallegos inclui?' => [
				'e o saldo 90 dias antes.',
				'e o saldo 90 dias antes. Segundo o programa 2027 da Estancia Las Buitreras: sete noites, seis dias de pesca com guia, todas as refeições, lanches, refrigerantes, vinho e cerveja, licença de pesca e traslado de ida e volta desde o aeroporto de El Calafate; não inclui voos, equipamento, destilados nem gorjetas.' ],
			'Como chego?' => [
				'Pelo aeroporto de Río Gallegos. A Karku Fly Fishing diz que sua estância fica a 50 minutos de carro e que oferece os traslados.',
				'Para a Karku Fly Fishing, pelo aeroporto de Río Gallegos: diz que sua estância fica a 50 minutos de carro e que oferece os traslados. A Estancia Las Buitreras, por sua vez, informa no seu programa 2027 um traslado de ida e volta desde o aeroporto de El Calafate, de 3 a 4 horas de minibus.' ],
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
	$texto = $p->post_content;
	$estados = [];
	foreach ( $g['cuerpo'] as $n => [ $viejo, $nuevo ] ) {
		[ $texto, $e ] = pm_lb_cambiar( $texto, $viejo, $nuevo );
		$estados[] = "cuerpo #$n: $e";
	}
	$filas = pm_filas( 'faqs', $p->ID );
	foreach ( $g['faqs'] as $pregunta => [ $viejo, $nuevo ] ) {
		$hallada = false;
		foreach ( $filas as $k => $f ) {
			if ( ( $f['pregunta'] ?? '' ) === $pregunta ) {
				$hallada = true;
				[ $filas[ $k ]['respuesta'], $e ] = pm_lb_cambiar( (string) $f['respuesta'], $viejo, $nuevo );
				$estados[] = 'FAQ «' . mb_substr( $pregunta, 0, 40 ) . "»: $e";
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
if ( ! $att ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $foto['archivo'] );
	copy( $ruta, $tmp );
	$att = media_handle_sideload( [ 'name' => $foto['archivo'], 'tmp_name' => $tmp ], $pid, $foto['titulo'] );
	if ( is_wp_error( $att ) ) {
		@unlink( $tmp );
		WP_CLI::error( $att->get_error_message() );
	}
	update_post_meta( $att, '_pm_archivo_origen', $foto['archivo'] );
	update_post_meta( $att, '_wp_attachment_image_alt', $foto['alt'] );
	update_field( 'foto_autor', $foto['autor'], $att );
	update_field( 'foto_origen', $foto['origen'], $att );
}
foreach ( $cambios as $k => $v ) {
	update_field( $k, $v, $pid );
}
if ( ! in_array( $att, $gal, true ) ) {
	update_field( 'fotos', array_merge( [ $att ], $gal ), $pid );
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
$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && ! $r['consentimiento'] && (bool) $r['publico'] && $r['precio_fecha'] === $hoy && false !== strpos( $r['precio'], '9.990' ) && in_array( $att, $r['fotos'], true ) && wp_attachment_is_image( $att ) && 'Solid Adventures' === pm_campo( 'foto_autor', $att );
WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": ficha #$pid en la lista de rio-gallegos | precio_fecha={$r['precio_fecha']} | precio={$r['precio']} | foto #$att (" . wp_get_attachment_url( $att ) . ") crédito=" . pm_campo( 'foto_autor', $att ) . ' | publico=' . (int) $r['publico'] . ' consentimiento=' . (int) $r['consentimiento'] . ' acuerdo=' . (int) $r['acuerdo'] );
$ok = $ok && $bien;
foreach ( [ 'descripcion', 'descripcion_en', 'descripcion_pt', 'prest_temporada', 'prest_temporada_en', 'prest_temporada_pt', 'precio', 'precio_en', 'precio_pt' ] as $k ) {
	$b = (string) pm_campo( $k, $pid ) === (string) $campos[ $k ];
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": ficha campo $k (" . mb_strlen( (string) pm_campo( $k, $pid ) ) . ' caracteres)' );
	$ok = $ok && $b;
}
$b = 'contactado' === pm_campo( 'estado', $pid ) && '' === (string) pm_campo( 'capacidad', $pid ) && (array) pm_campo( 'incluye', $pid ) === $campos['incluye'] && 0 === strpos( (string) pm_campo( 'notas', $pid ), 'Listado con información pública' );
WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ': ficha estado=' . pm_campo( 'estado', $pid ) . ' capacidad="' . pm_campo( 'capacidad', $pid ) . '" incluye=' . implode( ',', (array) pm_campo( 'incluye', $pid ) ) . ' notas actualizadas' );
$ok = $ok && $b;
foreach ( $plan as $i => $x ) {
	$p    = get_post( $x['id'] );
	$filas = pm_filas( 'faqs', $x['id'] );
	$bien = true;
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
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": guía rio-gallegos ($i) #{$x['id']}: " . count( $guias[ $i ]['cuerpo'] ) . ' párrafos y ' . count( $guias[ $i ]['faqs'] ) . ' FAQs leídos de vuelta, ' . count( $filas ) . ' FAQs en total ' . get_permalink( $x['id'] ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'APLICADO' );
