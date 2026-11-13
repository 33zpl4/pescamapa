<?php
/**
 * Río Grande, 13/11/2026: NEW card Villa María Lodge; Kau Tapen Lodge card moved into the new fields (+ companion rate); guide es/en/pt.
 * First-hand sources (operators' own pages, files in branch `investigacion`, folder operadores-fichas-1-20261113-1633 unless noted):
 *  - Villa María Lodge: its page on nervouswaters.com (the group that runs it: "operating our lodges under our own ownership"), file 17 (.html:
 *    "RATES 2027", season 25 Dec 2026 to 26 Mar 2027, 13,950 / 14,950 / 12,950, "Non-angler: $650 per night per person", included / not included,
 *    "weeks run from Friday to Friday", Delta Eco Lodge night, 6 anglers in six en-suite rooms) and the 2025/26 rate sheet (PDF text, file 18:
 *    "Fishing License: $595 (aprox per angler)"). The page states no deposit or cancellation terms: none is published.
 *  - Kau Tapen Lodge: its page on nervouswaters.com (operadores-fichas-2-20261113-1640, file 01: same rates, "Non-angler: $650 per night per person",
 *    Saturday-to-Saturday weeks, not included) and kautapen.com/rates (r1-tarifas-lodges-20261110-0931, file 02: licence "$550 approx. per angler",
 *    capacity "up to 10 guests (and occasionally 12)" on its accommodations page).
 * Cards stay "listado con información pública" (no consent, no agreement). No contact data, no photo.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = rio-grande-villa-maria-2026-11-13).
 */

$aplicar = true;
$hoy     = '20261113';
$vm_url  = 'https://www.nervouswaters.com/the-lodges/villa-maria-lodge/';

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
	'Villa María Lodge' => [
		'tipo' => [ 'alojamiento' ], 'prest_destino' => 'rio-grande', 'localidad' => 'Estancia José Menéndez, río Grande, Tierra del Fuego',
		'descripcion' => 'Lodge en la Estancia José Menéndez (fundada en 1894), sobre el curso bajo del río Grande, a pocas millas de la zona de marea, según su página en Nervous Waters. Recibe seis pescadores en seis habitaciones individuales con baño, con dos pescadores por guía; el operador describe 20 km de agua de acceso privado y una rotación por sectores nuevos cada mañana y cada tarde. Se pesca vadeando, con cañas de dos manos (recomienda de 7 a 9, de 12 a 14 pies) o de una mano, y toda la pesca es con devolución. Presta equipo de pesca. Sus semanas van de viernes a viernes; según el operador, las de viernes a viernes incluyen una noche en su Delta Eco Lodge (habitaciones compartidas) con un día de pesca guiada de dorado, sujeto a disponibilidad y por orden de llegada, con almuerzo, cena, desayuno y barra libre; no incluye el traslado de Ezeiza a Marina ni de Marina a Aeroparque. La tarifa incluye alojamiento en habitación single (según disponibilidad), traslado desde el aeropuerto de Río Grande, comidas, vinos, cerveza y bebidas espirituosas, guía y equipo de préstamo (cañas, reeles y líneas). El operador concentra su temporada en enero, febrero y marzo.',
		'descripcion_en' => 'Lodge at Estancia José Menéndez (founded in 1894), on the lower Río Grande, a few miles above the tidal influence, according to its page on Nervous Waters. It takes six anglers in six single en-suite rooms, with two anglers per guide; the operator describes 20 km of private water and a rotation through new beats every morning and evening. You fish wading, with two-handed rods (it recommends 7 to 9 weight, 12 to 14 feet) or single-handed rods, and all fishing is catch and release. It lends fishing gear. Its weeks run Friday to Friday; according to the operator, the Friday-to-Friday weeks include one night at its Delta Eco Lodge (shared rooms) with one day of guided dorado fishing, subject to availability and first come, first served, with lunch, dinner, breakfast and open bar; it does not include the transfer from Ezeiza to Marina or from Marina to Aeroparque. The rate includes lodging in a single room (subject to availability), transfer from Río Grande airport, meals, wines, beer and spirits, a guide and loaner gear (rods, reels and lines). The operator focuses its season on January, February and March.',
		'descripcion_pt' => 'Lodge na Estancia José Menéndez (fundada em 1894), no curso baixo do rio Grande, a poucas milhas da zona de maré, segundo a página dele na Nervous Waters. Recebe seis pescadores em seis quartos individuais com banheiro, com dois pescadores por guia; o operador descreve 20 km de água de acesso privado e um rodízio por trechos novos a cada manhã e a cada tarde. Pesca-se com vadeio, com varas de duas mãos (recomenda de 7 a 9, de 12 a 14 pés) ou de uma mão, e toda a pesca é com devolução. Empresta equipamento de pesca. As semanas vão de sexta a sexta; segundo o operador, as de sexta a sexta incluem uma noite no seu Delta Eco Lodge (quartos compartilhados) com um dia de pesca guiada de dourado, sujeito à disponibilidade e por ordem de chegada, com almoço, jantar, café da manhã e open bar; não inclui o traslado de Ezeiza a Marina nem de Marina a Aeroparque. A tarifa inclui hospedagem em quarto single (conforme disponibilidade), traslado do aeroporto de Río Grande, refeições, vinhos, cerveja e destilados, guia e equipamento emprestado (varas, molinetes e linhas). O operador concentra a temporada em janeiro, fevereiro e março.',
		'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha-marron-anadroma' ], 'equipo' => [ 'equipos' ],
		'capacidad' => 6, 'prest_capacidad_unidad' => 'total',
		'incluye' => [ 'alojamiento', 'comida', 'bebidas', 'traslados', 'equipos' ],
		'prest_temporada' => 'Del 25 de diciembre de 2026 al 26 de marzo de 2027 (su tarifa 2027); el operador concentra su temporada en enero, febrero y marzo',
		'prest_temporada_en' => '25 December 2026 to 26 March 2027 (its 2027 rate); the operator focuses its season on January, February and March',
		'prest_temporada_pt' => 'De 25 de dezembro de 2026 a 26 de março de 2027 (a tarifa 2027); o operador concentra a temporada em janeiro, fevereiro e março',
		'prest_programa' => '7 noches y 6,5 días de pesca; semanas de viernes a viernes, con una noche en su Delta Eco Lodge y un día de pesca de dorado según el operador',
		'prest_programa_en' => '7 nights and 6.5 fishing days; Friday-to-Friday weeks, with one night at its Delta Eco Lodge and a day of dorado fishing according to the operator',
		'prest_programa_pt' => '7 noites e 6,5 dias de pesca; semanas de sexta a sexta, com uma noite no seu Delta Eco Lodge e um dia de pesca de dourado segundo o operador',
		'prest_estadia_minima' => 'Tarifas por semana completa (7 noches); su página no publica estadías más cortas',
		'prest_estadia_minima_en' => 'Rates are for a full week (7 nights); its page publishes no shorter stays',
		'prest_estadia_minima_pt' => 'Tarifas por semana completa (7 noites); a página não publica estadias mais curtas',
		'precio' => 'US$ 13.950 por persona y semana del 25/12 al 8/1; US$ 14.950 del 8/1 al 5/3; US$ 12.950 del 5/3 al 26/3 (temporada 2027; 7 noches y 6,5 días de pesca; habitación single y guía compartido). Fuente: su página en Nervous Waters, leída el 13/11/2026',
		'precio_en' => 'US$ 13,950 per person per week from 25 Dec to 8 Jan; US$ 14,950 from 8 Jan to 5 Mar; US$ 12,950 from 5 Mar to 26 Mar (2027 season; 7 nights and 6.5 fishing days; single room and shared guide). Source: its page on Nervous Waters, read on 13/11/2026',
		'precio_pt' => 'US$ 13.950 por pessoa e semana de 25/12 a 8/1; US$ 14.950 de 8/1 a 5/3; US$ 12.950 de 5/3 a 26/3 (temporada 2027; 7 noites e 6,5 dias de pesca; quarto single e guia compartilhado). Fonte: a página dele na Nervous Waters, lida em 13/11/2026',
		'precio_fecha' => '20261113',
		'prest_acompanante' => 'US$ 650 por noche por persona (tarifa 2027, según su página en Nervous Waters)',
		'prest_acompanante_en' => 'US$ 650 per night per person (2027 rate, according to its page on Nervous Waters)',
		'prest_acompanante_pt' => 'US$ 650 por noite por pessoa (tarifa 2027, segundo a página dele na Nervous Waters)',
		'prest_no_incluye' => 'Pasajes aéreos nacionales e internacionales, el traslado de ida y vuelta desde Ushuaia al lodge, la licencia de pesca (aparte; su planilla de la temporada 2025/26 la estimaba en unos US$ 595 por pescador), la lavandería, una noche en Ushuaia el día de llegada y las propinas para guías y personal; tampoco el traslado de Ezeiza a Marina ni de Marina a Aeroparque',
		'prest_no_incluye_en' => 'Domestic and international airfares, the round-trip transfer from Ushuaia to the lodge, the fishing licence (separate; its 2025/26 rate sheet estimated about US$ 595 per angler), laundry, an overnight in Ushuaia on the arrival day and tips for guides and staff; nor the transfer from Ezeiza to Marina or from Marina to Aeroparque',
		'prest_no_incluye_pt' => 'Passagens aéreas nacionais e internacionais, o traslado de ida e volta de Ushuaia ao lodge, a licença de pesca (à parte; a planilha da temporada 2025/26 a estimava em cerca de US$ 595 por pescador), a lavanderia, uma noite em Ushuaia no dia da chegada e as gorjetas para guias e equipe; nem o traslado de Ezeiza a Marina ou de Marina a Aeroparque',
		'prest_publico' => 1, 'prest_consentimiento' => 0, 'datos_ejemplo' => 0, 'estado' => 'relevado', 'verificado' => '20261113',
		'prest_fuente' => $vm_url, 'web' => $vm_url,
		'notas' => 'Listado con información pública (página del operador en nervouswaters.com leída el 13/11/2026 con investigar.yml, rama investigacion, carpeta operadores-fichas-1-20261113-1633, archivos 17 y 18). Nervous Waters es la marca del grupo que opera el lodge ("operating our lodges under our own ownership", The Kautapen Group): se usa como página propia del operador, no de una agencia; si el dueño prefiere tratarla como intermediaria (docs/prestadores.md, punto 5), bajar la tarifa. Tarifa 2027 (25/12/2026 a 26/3/2027): 13.950 / 14.950 / 12.950; acompañante US$ 650 por noche; licencia: la planilla 2025/26 dice "Fishing License: $595 (aprox per angler)" (no hay cifra 2027). La página NO dice condiciones de depósito ni cancelación: no se publica ninguna. DISCREPANCIAS DE SU PÁGINA: arriba dice "Season: January to April", la tarifa dice 25/12 a 26/3 y el texto dice que concentra la temporada en enero, febrero y marzo; no incluye "traslado de ida y vuelta desde Ushuaia" pero en "Getting There" cuenta que se los recibe en Ushuaia el viernes (unas 3 horas en auto); la planilla 2025/26 dice "Day room at Río Grande hotel" donde la página 2027 dice "Overnight at Ushuaia". Sin respuesta del operador. Contacto no publicado. Falta contactarlo y confirmar.',
	],
	'Kau Tapen Lodge' => [
		'capacidad' => 10, 'prest_capacidad_unidad' => 'semana',
		'prest_programa' => '7 noches y 6,5 días de pesca; semanas de sábado a sábado, con una noche en su Delta Eco Lodge y un día de pesca de dorado según el operador',
		'prest_programa_en' => '7 nights and 6.5 fishing days; Saturday-to-Saturday weeks, with one night at its Delta Eco Lodge and a day of dorado fishing according to the operator',
		'prest_programa_pt' => '7 noites e 6,5 dias de pesca; semanas de sábado a sábado, com uma noite no seu Delta Eco Lodge e um dia de pesca de dourado segundo o operador',
		'prest_estadia_minima' => 'Tarifas por semana completa (7 noches); su ficha no publica estadías más cortas',
		'prest_estadia_minima_en' => 'Rates are for a full week (7 nights); its page publishes no shorter stays',
		'prest_estadia_minima_pt' => 'Tarifas por semana completa (7 noites); a página não publica estadias mais curtas',
		'prest_acompanante' => 'US$ 650 por noche por persona (tarifa 2027, según su ficha en Nervous Waters)',
		'prest_acompanante_en' => 'US$ 650 per night per person (2027 rate, according to its page on Nervous Waters)',
		'prest_acompanante_pt' => 'US$ 650 por noite por pessoa (tarifa 2027, segundo a página dele na Nervous Waters)',
		'prest_no_incluye' => 'Pasajes aéreos nacionales e internacionales, el traslado de ida y vuelta desde Ushuaia al lodge, la licencia de pesca (su sitio la estima en unos US$ 550 por pescador), la lavandería, una noche en Ushuaia el día de llegada y las propinas para guías y personal; tampoco el traslado de Ezeiza a Marina ni de Marina a Aeroparque',
		'prest_no_incluye_en' => 'Domestic and international airfares, the round-trip transfer from Ushuaia to the lodge, the fishing licence (its website estimates about US$ 550 per angler), laundry, an overnight in Ushuaia on the arrival day and tips for guides and staff; nor the transfer from Ezeiza to Marina or from Marina to Aeroparque',
		'prest_no_incluye_pt' => 'Passagens aéreas nacionais e internacionais, o traslado de ida e volta de Ushuaia ao lodge, a licença de pesca (o site estima cerca de US$ 550 por pescador), a lavanderia, uma noite em Ushuaia no dia da chegada e as gorjetas para guias e equipe; nem o traslado de Ezeiza a Marina ou de Marina a Aeroparque',
		'precio_fecha' => '20261113',
		'verificado' => '20261113',
		'notas' => 'Listado con información pública (sitio leído el 4/11/2026 con investigar.yml). 13/11/2026: se leyó su página en nervouswaters.com (operadores-fichas-2-20261113-1640, archivo 01): mismas tarifas 2027 que kautapen.com/rates (US$ 13.950 / 14.950 / 12.950), "Non-angler: $650 per night per person", semanas de sábado a sábado con una noche en el Delta Eco Lodge, qué no incluye; por eso precio_fecha pasa a 13/11/2026. La licencia "unos US$ 550" NO está en esa página: sale de kautapen.com/rates (leída el 10/11/2026). DISCREPANCIA: kautapen.com/rates lista "Day room (if needed) at Río Grande hotel" donde nervouswaters.com lista "Overnight at Ushuaia on arrival date" (la ficha usa la de Nervous Waters). Capacidad: 10 (a veces 12) por kautapen.com/accomodations; nervouswaters.com dice 12. Pescamapa no confirmó contacto, disponibilidad ni habilitación. Falta contactarlo.',
	],
];

$u_vm = 'Villa María Lodge';
$guias = [
	'es' => [
		'cuerpo' => [
			[ 'Una tarifa se muestra solo cuando el operador la publica con su temporada.',
			  'Una tarifa se muestra solo cuando el operador la publica con su temporada. Hoy lo hacen Kau Tapen Lodge (su sitio, leído el 4/11/2026) y Villa María Lodge (su página en Nervous Waters, leída el 13/11/2026).' ],
		],
		'temporada' => [ 'Diciembre a abril' => [ 'Kau Tapen del 26 de diciembre de 2026 al 17 de abril de 2027;', 'Kau Tapen del 26 de diciembre de 2026 al 17 de abril de 2027; Villa María Lodge del 25 de diciembre de 2026 al 26 de marzo de 2027;' ] ],
		'faqs' => [
			'¿Cuánto cuesta una semana en un lodge de Río Grande?' => [ 'Los demás operadores que encontramos no publican tarifa.',
				'Villa María Lodge publica en su página de Nervous Waters, para su temporada del 25 de diciembre de 2026 al 26 de marzo de 2027, US$ 13.950 por persona y semana del 25/12 al 8/1, US$ 14.950 del 8/1 al 5/3 y US$ 12.950 del 5/3 al 26/3, también en habitación single y con guía compartido (leída el 13/11/2026). Los dos lodges cobran a un acompañante que no pesca US$ 650 por noche por persona (según su ficha en Nervous Waters, leída el 13/11/2026). Los demás operadores que encontramos no publican tarifa.' ],
			'¿Qué incluye la tarifa de un lodge?' => [ 'Cada lodge incluye cosas distintas: pedí el detalle.',
				'En el caso de Villa María Lodge, según su página: alojamiento en habitación single, traslado desde el aeropuerto de Río Grande, todas las comidas, vinos, cerveza y bebidas espirituosas, guía profesional (dos pescadores por guía) y equipo de préstamo (cañas, reeles y líneas); no incluye pasajes, el traslado de ida y vuelta desde Ushuaia, la licencia, la lavandería, una noche en Ushuaia el día de llegada ni las propinas. Según el operador, las semanas de viernes a viernes suman una noche en su Delta Eco Lodge con un día de pesca guiada de dorado, sujeto a disponibilidad. Cada lodge incluye cosas distintas: pedí el detalle.' ],
		],
		'fuentes' => [
			[ 'Villa María Lodge en Nervous Waters: tarifas 2027 y qué incluye (operador)', 'https://www.nervouswaters.com/the-lodges/villa-maria-lodge/', 0, '20261113' ],
			[ 'Villa María Lodge: planilla de tarifas 2025/26 (operador)', 'https://www.nervouswaters.com/wp-content/uploads/2025/03/2.-Villa-Maria-Lodge-Rates-2025-26.pdf', 0, '20261113' ],
			[ 'Kau Tapen Lodge en Nervous Waters: tarifas 2027 y acompañante (operador)', 'https://www.nervouswaters.com/the-lodges/kau-tapen-lodge/', 0, '20261113' ],
		],
	],
	'en' => [
		'cuerpo' => [
			[ 'A rate is shown only when the operator publishes it with its season.',
			  'A rate is shown only when the operator publishes it with its season. Today that is the case for Kau Tapen Lodge (its website, read on 4/11/2026) and Villa María Lodge (its page on Nervous Waters, read on 13/11/2026).' ],
		],
		'temporada' => [ 'December to April' => [ 'Kau Tapen from 26 December 2026 to 17 April 2027;', 'Kau Tapen from 26 December 2026 to 17 April 2027; Villa María Lodge from 25 December 2026 to 26 March 2027;' ] ],
		'faqs' => [
			'How much does a week at a Río Grande lodge cost?' => [ 'The other operators we found publish no rate.',
				'Villa María Lodge publishes on its Nervous Waters page, for its season from 25 December 2026 to 26 March 2027, USD 13,950 per person per week from 25 Dec to 8 Jan, USD 14,950 from 8 Jan to 5 Mar and USD 12,950 from 5 Mar to 26 Mar, also in a single room with a shared guide (read on 13 November 2026). Both lodges charge a non-fishing companion USD 650 per night per person (according to their pages on Nervous Waters, read on 13 November 2026). The other operators we found publish no rate.' ],
			'What does a lodge rate include?' => [ 'Each lodge includes different things: ask for the detail.',
				'For Villa María Lodge, according to its page: lodging in a single room, transfer from Río Grande airport, all meals, wines, beer and spirits, a professional guide (two anglers per guide) and loaner equipment (rods, reels and lines); it does not include flights, the round-trip transfer from Ushuaia, the licence, laundry, an overnight in Ushuaia on the arrival day or tips. According to the operator, Friday-to-Friday weeks add one night at its Delta Eco Lodge with one day of guided dorado fishing, subject to availability. Each lodge includes different things: ask for the detail.' ],
		],
		'fuentes' => [
			[ 'Villa María Lodge on Nervous Waters: 2027 rates and what is included (operator)', 'https://www.nervouswaters.com/the-lodges/villa-maria-lodge/', 0, '20261113' ],
			[ 'Villa María Lodge: 2025/26 rate sheet (operator)', 'https://www.nervouswaters.com/wp-content/uploads/2025/03/2.-Villa-Maria-Lodge-Rates-2025-26.pdf', 0, '20261113' ],
			[ 'Kau Tapen Lodge on Nervous Waters: 2027 rates and companion rate (operator)', 'https://www.nervouswaters.com/the-lodges/kau-tapen-lodge/', 0, '20261113' ],
		],
	],
	'pt' => [
		'cuerpo' => [
			[ 'Uma tarifa só é mostrada quando o operador a publica com sua temporada.',
			  'Uma tarifa só é mostrada quando o operador a publica com sua temporada. Hoje isso vale para o Kau Tapen Lodge (o site dele, lido em 4/11/2026) e o Villa María Lodge (a página dele na Nervous Waters, lida em 13/11/2026).' ],
		],
		'temporada' => [ 'Dezembro a abril' => [ 'Kau Tapen de 26 de dezembro de 2026 a 17 de abril de 2027;', 'Kau Tapen de 26 de dezembro de 2026 a 17 de abril de 2027; Villa María Lodge de 25 de dezembro de 2026 a 26 de março de 2027;' ] ],
		'faqs' => [
			'Quanto custa uma semana em um lodge de Río Grande?' => [ 'Os demais operadores que encontramos não publicam tarifa.',
				'O Villa María Lodge publica na página dele na Nervous Waters, para a temporada de 25 de dezembro de 2026 a 26 de março de 2027, US$ 13.950 por pessoa e semana de 25/12 a 8/1, US$ 14.950 de 8/1 a 5/3 e US$ 12.950 de 5/3 a 26/3, também em quarto single e com guia compartilhado (lida em 13/11/2026). Os dois lodges cobram de um acompanhante que não pesca US$ 650 por noite por pessoa (segundo a página de cada um na Nervous Waters, lida em 13/11/2026). Os demais operadores que encontramos não publicam tarifa.' ],
			'O que a tarifa de um lodge inclui?' => [ 'Cada lodge inclui coisas diferentes: peça o detalhe.',
				'No caso do Villa María Lodge, segundo a página: hospedagem em quarto single, traslado do aeroporto de Río Grande, todas as refeições, vinhos, cerveja e destilados, guia profissional (dois pescadores por guia) e equipamento emprestado (varas, molinetes e linhas); não inclui passagens, o traslado de ida e volta de Ushuaia, a licença, a lavanderia, uma noite em Ushuaia no dia da chegada nem as gorjetas. Segundo o operador, as semanas de sexta a sexta somam uma noite no seu Delta Eco Lodge com um dia de pesca guiada de dourado, sujeito à disponibilidade. Cada lodge inclui coisas diferentes: peça o detalhe.' ],
		],
		'fuentes' => [
			[ 'Villa María Lodge na Nervous Waters: tarifas 2027 e o que inclui (operador)', 'https://www.nervouswaters.com/the-lodges/villa-maria-lodge/', 0, '20261113' ],
			[ 'Villa María Lodge: planilha de tarifas 2025/26 (operador)', 'https://www.nervouswaters.com/wp-content/uploads/2025/03/2.-Villa-Maria-Lodge-Rates-2025-26.pdf', 0, '20261113' ],
			[ 'Kau Tapen Lodge na Nervous Waters: tarifas 2027 e acompanhante (operador)', 'https://www.nervouswaters.com/the-lodges/kau-tapen-lodge/', 0, '20261113' ],
		],
	],
];

// --- 1. Cards -------------------------------------------------------------------------------------
$plan_fichas = [];
foreach ( $fichas as $nombre => $campos ) {
	$pid = pm_ff_ficha_id( $nombre );
	if ( ! $pid && 'Villa María Lodge' !== $nombre ) {
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
	if ( $p = pm_ff_guia_plan( 'rio-grande', $i, $g, $aplicar ) ) {
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
foreach ( $fichas as $nombre => $campos ) {
	$pid = $plan_fichas[ $nombre ][0];
	$ok  = pm_ff_ficha_verificar( $pid, $nombre, $campos ) && $ok;
	$r   = null;
	foreach ( pm_prestadores_destino( 'rio-grande' ) as $x ) {
		if ( (int) $x['id'] === $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && ! $r['consentimiento'] && '2026-11-13' === $r['precio_fecha'];
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» en la lista de rio-grande como información pública (publico=" . (int) ( $r['publico'] ?? 0 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ') capacidad=' . ( $r['capacidad'] ?? '' ) . ' unidad=' . ( $r['capacidad_unidad'] ?? '' ) . ' precio_fecha=' . ( $r['precio_fecha'] ?? '' ) . ' acompañante="' . ( $r['acompanante'] ?? '' ) . '"' );
	$ok = $ok && $bien;
}
foreach ( $plan as $i => $x ) {
	$ok = pm_ff_guia_verificar( $x ) && $ok;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
