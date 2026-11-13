<?php
/**
 * Jurassic Lake / Lago Strobel: the three operator cards and the guide (es, en, pt), 13/11/2026.
 * First-hand sources (operators' own pages, files in branch `investigacion`):
 *  - Jurassic Lake Lodge: /seasons, /pre-trip-info, /charter-flight, /the-lodge, /location (operadores-fichas-1-20261113-1633 files 01-04,
 *    jurassic-lake-operadores-20261030-0630) and the rate page /rates (r1-tarifas-lodges-20261110-0931, file 03, fetched 10/11/2026: that is the
 *    date of the figures; the pre-trip page was read 13/11/2026).
 *  - Estancia Laguna Verde: /the-lodge, /about-us-why (operadores-fichas-1, files 07, 09). Tres Amigos Outfitters: combo page, dateModified 2024-09-06.
 * Never written: the US$ 700 single supplement (agency figure), the "Jurassic Full Week US$ 6,900" (unexplained; only in the internal notes),
 * any Laguna Verde or Tres Amigos figure, any contact data. New card fields (programa, estadía mínima, acompañante, no incluye, unidad de
 * capacidad) are the ones added by PR 175. Cards stay "listado con información pública" (no consent, no agreement).
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = jurassic-fichas-2026-11-13).
 */

$aplicar = false;
$hoy     = '20261113';

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
	'Jurassic Lake Lodge' => [
		'descripcion' => 'Lodge en la costa sudoeste del lago Strobel, cerca de la boca del río Barrancoso (descripción del operador). Según su propio sitio: 10 habitaciones, pista de aterrizaje privada y vuelo chárter desde El Calafate con tarifa aparte, en ciertos sábados según su página de información previa. Recibe hasta diez pescadores, en habitaciones individuales o dobles con baño privado. Se pesca vadeando o desde la orilla, con un guía cada dos o tres pescadores. Su lista de equipaje pide llevar waders transpirables y botas de vadeo con suela de goma o de fieltro, y detalla cañas y líneas para llevar; las moscas se venden en el lodge. Según su página de información previa, los paquetes incluyen la licencia de pesca, el traslado terrestre desde El Calafate (unas seis horas) y las bebidas, salvo la selección de whisky. Pide un depósito del 50 % dentro de los 10 días de la reserva y el saldo 60 días antes de la llegada; el depósito no se reembolsa salvo que el cliente sea reemplazado.',
		'descripcion_en' => 'Lodge on the south-west shore of Lago Strobel, near the mouth of the Barrancoso River (operator\'s description). Per its own site: 10 rooms, private airstrip and a charter flight from El Calafate at a separate rate, on certain Saturdays according to its pre-trip page. It takes up to ten anglers, in single or double rooms with a private bathroom. You fish wading or from the bank, with one guide for every two or three anglers. Its packing list asks guests to bring breathable waders and rubber-sole or felt wading boots, and lists rods and lines to bring; flies are sold at the lodge. According to its pre-trip page, the packages include the fishing licence, the ground transfer from El Calafate (about six hours) and drinks, except the whiskey selection. It asks for a 50% deposit within 10 days of booking and the balance 60 days before arrival; the deposit is non-refundable unless the client is replaced.',
		'descripcion_pt' => 'Lodge na margem sudoeste do lago Strobel, perto da foz do rio Barrancoso (descrição do operador). Segundo o próprio site: 10 quartos, pista de pouso privada e voo fretado desde El Calafate com tarifa à parte, em certos sábados segundo a página de informação prévia. Recebe até dez pescadores, em quartos individuais ou duplos com banheiro privativo. Pesca-se com vadeio ou da margem, com um guia para cada dois ou três pescadores. A lista de bagagem pede waders respiráveis e botas de vadeio com sola de borracha ou de feltro, e detalha varas e linhas para levar; as moscas são vendidas no lodge. Segundo a página de informação prévia, os pacotes incluem a licença de pesca, o traslado terrestre desde El Calafate (cerca de seis horas) e as bebidas, exceto a seleção de uísque. Pede um depósito de 50 % em até 10 dias da reserva e o saldo 60 dias antes da chegada; o depósito não é reembolsável, a menos que o cliente seja substituído.',
		'prest_temporada' => 'Según su página de temporadas: primavera, de septiembre a principios de enero; verano, de mediados de enero a mediados de marzo; otoño, de mediados de marzo a mayo',
		'prest_temporada_en' => 'According to its seasons page: spring, September to early January; summer, mid-January to mid-March; autumn, mid-March to May',
		'prest_temporada_pt' => 'Segundo a página de temporadas: primavera, de setembro ao começo de janeiro; verão, de meados de janeiro a meados de março; outono, de meados de março a maio',
		'prest_programa' => '7 noches y 6,5 días de pesca, de sábado a sábado; 4 noches y 3,5 días, de martes a sábado, y 3 noches y 2,5 días, de sábado a martes (estos dos, de enero a abril)',
		'prest_programa_en' => '7 nights and 6.5 fishing days, Saturday to Saturday; 4 nights and 3.5 days, Tuesday to Saturday, and 3 nights and 2.5 days, Saturday to Tuesday (these two, January to April)',
		'prest_programa_pt' => '7 noites e 6,5 dias de pesca, de sábado a sábado; 4 noites e 3,5 dias, de terça a sábado, e 3 noites e 2,5 dias, de sábado a terça (estes dois, de janeiro a abril)',
		'prest_estadia_minima' => 'Al comienzo de la temporada, solo semanas completas; las medias semanas empiezan a mediados de enero (según su página de temporadas)',
		'prest_estadia_minima_en' => 'At the start of the season, full weeks only; half weeks start in mid-January (according to its seasons page)',
		'prest_estadia_minima_pt' => 'No começo da temporada, só semanas completas; as meias semanas começam em meados de janeiro (segundo a página de temporadas)',
		'precio' => 'US$ 8.000 por pescador por la semana de 7 noches; US$ 5.200 por 4 noches y US$ 4.200 por 3 noches (enero a abril). Es la «tarifa final por pescador» de su página de tarifas, leída el 10/11/2026; la página no muestra año ni temporada',
		'precio_en' => 'US$ 8,000 per angler for the 7-night week; US$ 5,200 for 4 nights and US$ 4,200 for 3 nights (January to April). It is the "final rate per angler" on its rate page, read on 10/11/2026; the page shows no year or season',
		'precio_pt' => 'US$ 8.000 por pescador pela semana de 7 noites; US$ 5.200 por 4 noites e US$ 4.200 por 3 noites (janeiro a abril). É a «tarifa final por pescador» da página de tarifas, lida em 10/11/2026; a página não mostra ano nem temporada',
		'precio_fecha' => '20261110',
		'prest_no_incluye' => 'El vuelo chárter desde El Calafate (tarifa aparte), los pasajes, la selección de whisky (se paga por medida o por botella) y las propinas (sugiere US$ 80 a 100 por persona por día; en el lodge solo se paga en efectivo)',
		'prest_no_incluye_en' => 'The charter flight from El Calafate (separate rate), flights, the whiskey selection (paid by the shot or the bottle) and tips (it suggests US$ 80 to 100 per person per day; the lodge takes cash only)',
		'prest_no_incluye_pt' => 'O voo fretado desde El Calafate (tarifa à parte), as passagens, a seleção de uísque (paga por dose ou por garrafa) e as gorjetas (sugere US$ 80 a 100 por pessoa por dia; no lodge só se paga em dinheiro)',
		'incluye' => [ 'alojamiento', 'comida', 'bebidas', 'traslados', 'licencia' ],
		'capacidad' => 10,
		'prest_capacidad_unidad' => 'total',
		'verificado' => '20261113',
		'notas' => 'Listado con información pública (sitio del operador leído el 30/10/2026). 13/11/2026: se leyeron sus páginas /seasons, /pre-trip-info, /charter-flight y /the-lodge (investigar.yml, rama investigacion, carpeta operadores-fichas-1-20261113-1633); la tarifa sale de /rates, leída el 10/11/2026 (carpeta r1-tarifas-lodges-20261110-0931), por eso precio_fecha es 10/11 y no 13/11. Pescamapa no confirmó contacto, disponibilidad ni idiomas con el operador. SIN RESOLVER (preguntarle al lodge): (1) su página de planes /pricing-plans/list muestra "Jurassic Full Week" a US$ 6.900, válida 12 meses, con vuelo chárter de ida y vuelta, y no se pudo conciliar con US$ 8.000: NO se publica en la ficha (sí figura, como cifra distinta, en la nota de precios); (2) /rates no muestra año ni temporada para US$ 8.000 / 5.200 / 4.200. NO PUBLICAR: el suplemento de habitación individual (US$ 700) viene de una agencia, no del operador. DISCREPANCIAS DE SU SITIO: temporada: /seasons dice de septiembre a principios de enero (primavera) y la página de inicio dice octubre; la ficha cita /seasons. Vuelo chárter: /location y /charter-flight dicen todos los sábados, /pre-trip-info dice "ciertos sábados" a tarifa fija; la ficha usa /pre-trip-info. Acompañante que no pesca: no publicado (queda vacío). Falta contactarlo.',
	],
	'Estancia Laguna Verde' => [
		'descripcion' => 'Estancia y lodge de pesca con mosca en la costa sur del lago Strobel (descripción del operador). Se vende también a través de agencias de viajes. Según su sitio, el edificio principal recibe hasta 12 pescadores por semana, con ocho habitaciones individuales y dos dobles, todas con baño en suite, y ofrece un guía cada dos pescadores (también guía individual). Su sitio no publica tarifa ni temporada.',
		'descripcion_en' => 'Estancia and fly-fishing lodge on the south coast of Lago Strobel (operator\'s description). Sold also through travel agencies. According to its website, the main building takes up to 12 anglers a week, with eight single and two double rooms, all with en-suite bathrooms, and it offers one guide for every two anglers (single guiding is also available). Its website publishes no rate or season.',
		'descripcion_pt' => 'Estância e lodge de pesca com mosca na costa sul do lago Strobel (descrição do operador). Vendida também por agências de viagens. Segundo o site, o edifício principal recebe até 12 pescadores por semana, com oito quartos individuais e dois duplos, todos com banheiro privativo, e oferece um guia para cada dois pescadores (também guia individual). O site não publica tarifa nem temporada.',
		'capacidad' => 12,
		'prest_capacidad_unidad' => 'semana',
		'verificado' => '20261113',
		'notas' => 'Listado con información pública (sitio del operador leído el 30/10/2026). 13/11/2026: se leyeron /the-lodge y /about-us-why de su sitio (operadores-fichas-1-20261113-1633, archivos 07 y 09): hasta 12 pescadores por semana, 8 habitaciones individuales y 2 dobles, un guía cada dos pescadores (guía individual disponible). Sin precio, temporada ni duración en su sitio: precio vacío. Tight Loop Travel (agencia) lista paquetes de 3 días/4 noches, 4/5 y 6/7: son cifras de agencia, no se publican; hay que pedirlas al operador. NO PUBLICADO por contradicción en su sitio: "habitaciones individuales sin costo extra" (/about-us-why) frente a "alojamiento y guía individual por una tarifa especial" (/the-lodge). Pescamapa no confirmó contacto, tarifa, idiomas ni disponibilidad con el operador. Falta contactarlo (tarifa, temporada y duraciones).',
	],
	'Tres Amigos Outfitters' => [
		'notas' => 'Listado con información pública (sitio del operador leído el 30/10/2026). 13/11/2026: la página del combo Jurassic Lake + Glacier King tiene dateModified 2024-09-06 (JSON-LD de la propia página): sus cifras no se publican (sin precio) y la descripción queda como está. Pescamapa no confirmó contacto, tarifa, idiomas ni disponibilidad con el operador. Falta pedirle tarifa y temporada vigentes.',
	],
];

$guias = [
	'es' => [
		'cuerpo' => [
			[ 'dice que tiene un vuelo chárter desde El Calafate todos los sábados por la mañana (una hora hasta su pista privada) y que incluye en sus paquetes un traslado terrestre desde El Calafate de unas seis horas.',
			  'dice, en su página de información previa, que ofrece un vuelo chárter desde El Calafate en ciertos sábados, con una tarifa aparte (una hora hasta su pista privada), y que cada paquete incluye un traslado terrestre desde El Calafate de unas seis horas.' ],
			[ '(de octubre a principios de enero)', '(de septiembre a principios de enero, según su página de temporadas)' ],
			[ 'Recordá que el Barrancoso cierra el 3 de mayo de 2027.', 'Recordá que el Barrancoso abre el 1 de octubre de 2026 y cierra el 3 de mayo de 2027.' ],
			[ 'no mostramos precios porque no confirmamos ninguna tarifa vigente con un operador.',
			  'la única tarifa que mostramos es la de Jurassic Lake Lodge, tal como la publica en su propio sitio (leída el 10/11/2026; la página no muestra año ni temporada); Estancia Laguna Verde y Tres Amigos Outfitters no publican una tarifa que podamos usar (la página de Tres Amigos se modificó por última vez en 2024).' ],
		],
		'faqs' => [
			'¿Cómo se llega?' => [ 'con un chárter de una hora desde El Calafate los sábados o con un traslado terrestre de unas seis horas;',
				'con un chárter de una hora desde El Calafate en ciertos sábados, a tarifa aparte, o con el traslado terrestre de unas seis horas que incluye cada paquete;' ],
			'¿Cuánto cuesta un viaje?' => [ 'Los lodges venden paquetes de varias noches, por persona, en dólares. No tenemos una tarifa vigente confirmada con ningún operador, por eso no publicamos ninguna.',
				'Jurassic Lake Lodge publica en su propio sitio una «tarifa final por pescador» de US$ 8.000 por 7 noches y 6,5 días de pesca (de sábado a sábado), US$ 5.200 por 4 noches y 3,5 días (de martes a sábado) y US$ 4.200 por 3 noches y 2,5 días (de sábado a martes), estos dos últimos de enero a abril (leída el 10/11/2026; la página no muestra año ni temporada). Según su página de información previa, los paquetes incluyen la licencia de pesca, el traslado terrestre desde El Calafate y las bebidas (salvo la selección de whisky); no incluyen el vuelo chárter desde El Calafate, que se paga aparte, ni las propinas (sugiere US$ 80 a 100 por persona por día). Pide un 50 % de depósito dentro de los 10 días de la reserva y el saldo 60 días antes; el depósito no se reembolsa salvo que el cliente sea reemplazado. Estancia Laguna Verde y Tres Amigos Outfitters no publican una tarifa que podamos usar. Confirmá la tarifa de tu fecha con el lodge.' ],
		],
		'fuentes' => [
			[ 'Jurassic Lake Lodge: tarifas y programas (operador)', 'https://www.jurassiclake.com/rates', 0, '20261110' ],
			[ 'Jurassic Lake Lodge: información previa al viaje (operador)', 'https://www.jurassiclake.com/pre-trip-info', 0, '20261113' ],
			[ 'Jurassic Lake Lodge: temporadas (operador)', 'https://www.jurassiclake.com/seasons', 0, '20261113' ],
		],
	],
	'en' => [
		'cuerpo' => [
			[ 'says it runs a charter flight from El Calafate every Saturday morning (about one hour to its private airstrip), with a ground transfer from El Calafate of about six hours included in its packages.',
			  'says, on its pre-trip page, that it offers a charter flight from El Calafate on certain Saturdays at a separate rate (about one hour to its private airstrip), and that every package includes a ground transfer from El Calafate of about six hours.' ],
			[ '(October to early January)', '(September to early January, according to its seasons page)' ],
			[ 'Remember the Barrancoso closes on 3 May 2027.', 'Remember the Barrancoso opens on 1 October 2026 and closes on 3 May 2027.' ],
			[ 'we show no prices because we have not confirmed any current rate with an operator.',
			  'the only rate we show is Jurassic Lake Lodge\'s, as it publishes it on its own website (read on 10/11/2026; the page shows no year or season); Estancia Laguna Verde and Tres Amigos Outfitters publish no rate we can use (the Tres Amigos page was last modified in 2024).' ],
		],
		'faqs' => [
			'How do I get there?' => [ 'by a one-hour charter flight from El Calafate on Saturdays or by a ground transfer of about six hours;',
				'by a one-hour charter flight from El Calafate on certain Saturdays, at a separate rate, or by the ground transfer of about six hours that every package includes;' ],
			'How much does a trip cost?' => [ 'Lodges sell multi-night packages, per person, in US dollars. We have no current rate confirmed with any operator, so we publish none.',
				'Jurassic Lake Lodge publishes on its own website a "final rate per angler" of US$ 8,000 for 7 nights and 6.5 fishing days (Saturday to Saturday), US$ 5,200 for 4 nights and 3.5 days (Tuesday to Saturday) and US$ 4,200 for 3 nights and 2.5 days (Saturday to Tuesday), the last two from January to April (read on 10/11/2026; the page shows no year or season). According to its pre-trip page, the packages include the fishing licence, the ground transfer from El Calafate and drinks (except the whiskey selection); they do not include the charter flight from El Calafate, which is paid separately, or tips (it suggests US$ 80 to 100 per person per day). It asks for a 50% deposit within 10 days of booking and the balance 60 days before; the deposit is non-refundable unless the client is replaced. Estancia Laguna Verde and Tres Amigos Outfitters publish no rate we can use. Confirm the rate for your date with the lodge.' ],
		],
		'fuentes' => [
			[ 'Jurassic Lake Lodge: rates and programmes (operator)', 'https://www.jurassiclake.com/rates', 0, '20261110' ],
			[ 'Jurassic Lake Lodge: pre-trip information (operator)', 'https://www.jurassiclake.com/pre-trip-info', 0, '20261113' ],
			[ 'Jurassic Lake Lodge: seasons (operator)', 'https://www.jurassiclake.com/seasons', 0, '20261113' ],
		],
	],
	'pt' => [
		'cuerpo' => [
			[ 'diz que opera um voo fretado desde El Calafate todo sábado de manhã (cerca de uma hora até sua pista privada), com um traslado terrestre desde El Calafate de cerca de seis horas incluído nos pacotes.',
			  'diz, na sua página de informação prévia, que oferece um voo fretado desde El Calafate em certos sábados, a tarifa à parte (cerca de uma hora até sua pista privada), e que cada pacote inclui um traslado terrestre desde El Calafate de cerca de seis horas.' ],
			[ '(outubro ao começo de janeiro)', '(de setembro ao começo de janeiro, segundo a página de temporadas)' ],
			[ 'Lembre que o Barrancoso fecha em 3 de maio de 2027.', 'Lembre que o Barrancoso abre em 1º de outubro de 2026 e fecha em 3 de maio de 2027.' ],
			[ 'não mostramos preços porque não confirmamos nenhuma tarifa vigente com um operador.',
			  'a única tarifa que mostramos é a do Jurassic Lake Lodge, como a publica no próprio site (lida em 10/11/2026; a página não mostra ano nem temporada); a Estancia Laguna Verde e a Tres Amigos Outfitters não publicam uma tarifa que possamos usar (a página da Tres Amigos foi modificada pela última vez em 2024).' ],
		],
		'faqs' => [
			'Como chego lá?' => [ 'por um voo fretado de uma hora desde El Calafate aos sábados ou por um traslado terrestre de cerca de seis horas;',
				'por um voo fretado de uma hora desde El Calafate em certos sábados, a tarifa à parte, ou pelo traslado terrestre de cerca de seis horas que cada pacote inclui;' ],
			'Quanto custa uma viagem?' => [ 'Os lodges vendem pacotes de várias noites, por pessoa, em dólares. Não temos nenhuma tarifa vigente confirmada com um operador, então não publicamos nenhuma.',
				'O Jurassic Lake Lodge publica no próprio site uma «tarifa final por pescador» de US$ 8.000 por 7 noites e 6,5 dias de pesca (de sábado a sábado), US$ 5.200 por 4 noites e 3,5 dias (de terça a sábado) e US$ 4.200 por 3 noites e 2,5 dias (de sábado a terça), estes dois últimos de janeiro a abril (lida em 10/11/2026; a página não mostra ano nem temporada). Segundo a página de informação prévia, os pacotes incluem a licença de pesca, o traslado terrestre desde El Calafate e as bebidas (exceto a seleção de uísque); não incluem o voo fretado desde El Calafate, pago à parte, nem as gorjetas (sugere US$ 80 a 100 por pessoa por dia). Pede 50 % de depósito em até 10 dias da reserva e o saldo 60 dias antes; o depósito não é reembolsável, a menos que o cliente seja substituído. A Estancia Laguna Verde e a Tres Amigos Outfitters não publicam uma tarifa que possamos usar. Confirme a tarifa da sua data com o lodge.' ],
		],
		'fuentes' => [
			[ 'Jurassic Lake Lodge: tarifas e programas (operador)', 'https://www.jurassiclake.com/rates', 0, '20261110' ],
			[ 'Jurassic Lake Lodge: informação prévia à viagem (operador)', 'https://www.jurassiclake.com/pre-trip-info', 0, '20261113' ],
			[ 'Jurassic Lake Lodge: temporadas (operador)', 'https://www.jurassiclake.com/seasons', 0, '20261113' ],
		],
	],
];

// --- 1. Cards -------------------------------------------------------------------------------------
$plan_fichas = [];
foreach ( $fichas as $nombre => $campos ) {
	$pid = pm_ff_ficha_id( $nombre );
	if ( ! $pid ) {
		WP_CLI::error( "Se esperaba una sola ficha «{$nombre}»" );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» #$pid (publico=" . (int) pm_campo( 'prest_publico', $pid ) . ' consentimiento=' . (int) pm_campo( 'prest_consentimiento', $pid ) . ' estado=' . pm_campo( 'estado', $pid ) . ')' );
	$plan_fichas[ $nombre ] = [ $pid, pm_ff_ficha_plan( $pid, $campos ) ];
}

// --- 2. Guide (es, en, pt) ------------------------------------------------------------------------
$plan = [];
foreach ( $guias as $i => $g ) {
	if ( $p = pm_ff_guia_plan( 'jurassic-lake', $i, $g, $aplicar ) ) {
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
	foreach ( pm_prestadores_destino( 'jurassic-lake' ) as $x ) {
		if ( (int) $x['id'] === $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && ! $r['consentimiento'];
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» sigue en la lista de jurassic-lake como información pública (publico=" . (int) ( $r['publico'] ?? 0 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ') capacidad=' . ( $r['capacidad'] ?? '' ) . ' unidad=' . ( $r['capacidad_unidad'] ?? '' ) . ' precio_fecha=' . ( $r['precio_fecha'] ?? '' ) );
	$ok = $ok && $bien;
}
// No figure that must stay out of the public card.
foreach ( [ 'Jurassic Lake Lodge', 'Estancia Laguna Verde', 'Tres Amigos Outfitters' ] as $nombre ) {
	$pid = $plan_fichas[ $nombre ][0];
	$pub = '';
	foreach ( [ '', '_en', '_pt' ] as $s ) {
		foreach ( [ 'descripcion', 'precio', 'prest_temporada', 'prest_programa', 'prest_estadia_minima', 'prest_acompanante', 'prest_no_incluye' ] as $k ) {
			$pub .= ' ' . pm_campo( $k . $s, $pid );
		}
	}
	$limpio = ! preg_match( '/6[.,]900|\b700\b/', $pub ) && ( 'Jurassic Lake Lodge' === $nombre || '' === trim( (string) pm_campo( 'precio', $pid ) ) ) && '' === trim( (string) pm_campo( 'prest_acompanante', $pid ) );
	WP_CLI::log( ( $limpio ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» sin 6.900, sin 700, sin acompañante" . ( 'Jurassic Lake Lodge' === $nombre ? '' : ', sin precio' ) );
	$ok = $ok && $limpio;
}
foreach ( $plan as $i => $x ) {
	$ok = pm_ff_guia_verificar( $x ) && $ok;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'APLICADO' );
