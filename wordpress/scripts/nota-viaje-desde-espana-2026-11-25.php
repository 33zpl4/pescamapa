<?php
/**
 * Note "Viaje de pesca a la Argentina desde España" (es only, clave viaje-pesca-argentina-desde-espana), 25/11/2026.
 * Why: friends of the owner in Spain asked three AI assistants how to plan a fishing trip to Argentina; the follow-ups were gear on the plane, car and 4x4, flights, cost in euros and permits.
 * Pescamapa was cited for prices but had nothing for that traveller. Owner approved the note; secondary market: no home-page placement, no inbound links from this script.
 * Fishing prices and licences are OUR published figures (cost overview 16/11/2026, precios.json, licencias.json); euro conversion at the ECB reference rate of 24/11/2026 (1 EUR = 1.0963 USD);
 * ARS to USD at the official rate of 20/11/2026 used by the site's calculators (1,580 ARS per USD). Airline, entry, driving and airport facts are dated in docs/borradores/viaje-desde-espana-2026-11-25.md
 * (verified on the open pages on 25/11/2026).
 * Spanish only for now; en/pt siblings after the owner reads it. Cover photo: reuses the attachment of the note precio-pesca-patagonia-sur (owner's Unsplash photo, provisional, see docs/estado.md); no new image.
 * Idempotent: a note with the same clave + language, or the same slug, is never touched. Dry run first. Run with wp-script.yml (script = nota-viaje-desde-espana-2026-11-25).
 */
// --- Helpers shared by the seo-consultas-2026-11-13 scripts (copied in each file: wp-script.yml sends one file) ---
// --- Helpers shared by the seo-consultas-2026-11-13 scripts (copied in each file: wp-script.yml sends one file) ---
if ( ! function_exists( 'pm_sc_h2' ) ) {
	function pm_sc_h2( string $texto, int $nivel = 2 ): string {
		return "<!-- wp:heading" . ( 2 === $nivel ? '' : ' {"level":' . $nivel . '}' ) . " -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->\n\n";
	}
	function pm_sc_p( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	function pm_sc_lista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
	}
	/** FAQ as a group block with the class pm-faq: visible on the page and read by pm_faqs_contenido() for the FAQPage JSON-LD. */
	function pm_sc_faq( array $faqs ): string {
		$in = '';
		foreach ( $faqs as [ $q, $a ] ) {
			$in .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( $in ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	function pm_sc_a( string $url, string $texto ): string {
		return '<a href="' . esc_url( $url ) . '">' . $texto . '</a>';
	}
	/** Heading blocks with offset: [ [ offset, texto plano ], … ]. */
	function pm_sc_encabezados( string $c ): array {
		preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
		$out = [];
		foreach ( $m[0] as $n => $b ) {
			$out[] = [ $b[1], trim( wp_strip_all_tags( $m[1][ $n ][0] ) ), strlen( $b[0] ) ];
		}
		return $out;
	}
	/** Offset of the first heading whose text matches $regex, or null. */
	function pm_sc_pos_encabezado( string $c, string $regex ): ?int {
		foreach ( pm_sc_encabezados( $c ) as [ $pos, $texto ] ) {
			if ( preg_match( $regex, $texto ) ) {
				return $pos;
			}
		}
		return null;
	}
	/** Offset just after the first paragraph block whose plain text matches $regex, or null. */
	function pm_sc_pos_tras_parrafo( string $c, string $regex ): ?int {
		preg_match_all( '#<!-- wp:paragraph[^>]*-->\s*<p[^>]*>(.*?)</p>\s*<!-- /wp:paragraph -->\s*#su', $c, $m, PREG_OFFSET_CAPTURE );
		foreach ( $m[0] as $n => $b ) {
			if ( preg_match( $regex, wp_strip_all_tags( $m[1][ $n ][0] ) ) ) {
				return $b[1] + strlen( $b[0] );
			}
		}
		return null;
	}
	function pm_sc_insertar( string $c, int $pos, string $bloque ): string {
		return substr( $c, 0, $pos ) . $bloque . substr( $c, $pos );
	}
	/** Save post content and fail loudly when the read-back differs. */
	function pm_sc_guardar( int $id, string $nuevo, array $debe_contener, string $etiqueta ): void {
		$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $nuevo ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( $etiqueta . ': ' . $r->get_error_message() );
		}
		$leido = get_post( $id )->post_content;
		foreach ( $debe_contener as $t ) {
			if ( false === strpos( $leido, $t ) ) {
				WP_CLI::error( "$etiqueta: la lectura de vuelta no contiene «{$t}»." );
			}
		}
	}
	function pm_sc_modo( bool $aplicar ): string {
		return $aplicar ? 'APLICA' : 'SIMULACRO';
	}
}
if ( ! function_exists( 'pm_tp_tabla' ) ) {
	function pm_tp_tabla( array $cabecera, array $filas ): string {
		$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
		$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
		return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->\n\n";
	}
	/** The dated yearly box (same markup as the other notes): the only place with dates that change every season. */
	function pm_tp_caja( string $titulo, array $items, string $pie ): string {
		$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
		return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph -->\n<p><em>{$pie}</em></p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n";
	}
	/** Guide (pm_destino) of a clave in a language, else in Spanish. */
	function pm_tp_guia( string $clave, string $i ): ?WP_Post {
		return pm_buscar( 'pm_destino', $clave, $i ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	}
}


$aplicar = false;
$hoy     = '20261125';
$clave   = 'viaje-pesca-argentina-desde-espana';
$foto_de = 'precio-pesca-patagonia-sur'; // note whose cover photo is reused (owner's Unsplash photo, Fitz Roy)
$i       = 'es';

$N = [
	'titulo' => 'Viaje de pesca a la Argentina desde España: vuelos, cañas, auto y cuánto cuesta en euros (2026/27)',
	'slug' => 'viaje-de-pesca-a-argentina-desde-espana',
	'seo_title' => 'Viaje de pesca a Argentina desde España: cañas, auto y euros',
	'seo_description' => 'Pescar en la Argentina desde España: vuelos, entrada, cañas en el avión, auto y 4x4, permisos y tres presupuestos aproximados en euros, con fuente y fecha.',
	'bajada' => 'Lo que necesita saber quien viaja desde España a pescar en la Argentina: vuelos, entrada, cañas en el avión, auto y un presupuesto aproximado en euros, con fuente y fecha.',
	'p0' => '<strong>Respuesta corta.</strong> Para pescar en la Argentina desde España hace falta un pasaporte, un vuelo largo hasta Buenos Aires, otro vuelo dentro del país, y decidir si llevás tus cañas o usás las del lodge o el guía. En seis puntos:',
	'comercial' => '<strong>¿Preferís que lo armemos nosotros?</strong> Nos contás una sola vez dónde, cuándo y con quién querés pescar; escribimos a los lodges y guías que encajan y en 24 a 48 horas recibís sus opciones para comparar. Es gratis. <a href="{p:presupuesto}">Pedir presupuesto</a> o <a href="{viaje}">armar el viaje en el formulario</a>.',
	'h_cuando' => 'Cuándo ir',
	'cuando_p' => '<strong>Trucha en la Patagonia: de noviembre a abril o mayo. Dorado en el Litoral: de octubre a abril.</strong> La temporada patagónica 2026/27 va del 1 de noviembre de 2026 al 1 de mayo de 2027 (Neuquén, Río Negro, Chubut y Parques Nacionales), y cada ambiente tiene sus propias fechas de apertura y cierre. En Corrientes el dorado se pesca de octubre a abril, con épocas que cambian con la altura del río; no hay veda por fechas, pero la devolución es obligatoria todo el año. Mes por mes y por destino: <a href="{p:calendario}">calendario de pesca</a>.',
	'h_llegar' => 'Cómo llegar',
	'llegar_p1' => '<strong>Volás a Buenos Aires y desde allí a la zona de pesca.</strong> Hay vuelo directo Madrid–Buenos Aires (Ezeiza) con Iberia, Aerolíneas Argentinas y Air Europa, de unas 12 h 30 a 13 h; desde otras ciudades españolas conectás en Madrid. El precio del vuelo cambia con la fecha: miralo en el buscador de cada aerolínea.',
	'llegar_p2' => '<strong>Ojo con los dos aeropuertos.</strong> Los vuelos de España llegan a Ezeiza; los vuelos internos salen de Aeroparque o de Ezeiza según el vuelo: mirá en el pasaje de qué aeropuerto sale cada tramo. Entre uno y otro hay entre 45 y 90 minutos según el tráfico, así que dejá margen de varias horas si cambiás de aeropuerto.',
	'llegar_p3' => '<strong>Entrada.</strong> Con pasaporte español ordinario no hace falta visado para turismo hasta 90 días. El pasaporte tiene que tener 6 meses de vigencia desde la entrada.',
	'h_canas' => 'Qué hago con las cañas: llevarlas o alquilar',
	'canas_p' => '<strong>Con lodge o guía, las cañas y el equipo suelen estar incluidos o se alquilan allá</strong> (un equipo completo ronda los US$ 80 por día, ≈ 73 €, aproximado). Preguntá por escrito qué incluye antes de pagar: <a href="{n:paquete-incluye-no-incluye}">qué incluye un paquete de pesca y qué cuesta aparte</a>. Si querés llevar las tuyas, van en bodega y la aerolínea cobra aparte:',
	'canas_p2' => '<strong>Botas:</strong> las suelas de fieltro están prohibidas en varias aguas de Parques Nacionales y en todas las de Río Negro, y el equipo se desinfecta por el alga Didymo. Llevá suela de goma y preguntale a tu guía.',
	'h_auto' => '¿Hace falta auto, y 4x4?',
	'auto_p1' => '<strong>No hace falta 4x4: la ruta de los Siete Lagos entre Bariloche y San Martín de los Andes (184 km) está toda asfaltada.</strong> El ripio aparece en los accesos a algunos ríos: preguntale al guía o al lodge por ese último tramo. Con lodge o guía no necesitás auto (coche) y el traslado suele estar incluido; en el Litoral se pesca desde la lancha del guía o del lodge. Detalle: <a href="{g:bariloche}">guía de Bariloche</a> y <a href="{g:san-martin-junin}">guía de San Martín y Junín de los Andes</a>.',
	'auto_p2' => '<strong>Si alquilás:</strong> con tu carné español podés manejar hasta un año desde que entrás, con el pasaporte que muestra la entrada; no hace falta el permiso internacional. Pedí por escrito si el contrato permite ripio y qué cubre el seguro: los seguros contratados fuera de la Argentina no suelen cubrir allí, y los autos de alquiler no pueden salir del país.',
	'auto_p3' => 'Antes de salir en invierno o con mal tiempo, mirá en Vialidad Nacional si hay cortes por nieve o desprendimientos en la ruta.',
	'h_permisos' => 'Permisos de pesca',
	'permisos_p' => '<strong>El permiso se paga en pesos.</strong> Para un extranjero no residente, el permiso ordinario de la Patagonia (Neuquén, Río Negro, Chubut y Parques Nacionales) cuesta $ 50.000 por 1 día, $ 140.000 por 10 días y $ 280.000 la temporada 2026/27: unos US$ 89 (≈ 80 €) el de 10 días, aproximado, al cambio oficial del 20/11/2026. En Corrientes, la licencia de turista extranjero se cobra en litros de nafta súper: 16 litros por 1 día y 36 por 6 días. Cada paquete incluye o no la licencia: preguntalo por escrito. Calculá la tuya con la <a href="{p:licencia}">calculadora de licencia de pesca</a>; el trámite, en <a href="{n:permiso-pesca-argentina-paso-a-paso}">permiso de pesca en Argentina, paso a paso</a>.',
	'h_costo' => 'Cuánto cuesta, en euros',
	'costo_p' => '<strong>Tres ejemplos con nuestros precios publicados, en dólares y en euros, siempre aproximados.</strong> Pasamos a euros con el cambio de referencia del BCE del 24/11/2026 (1 € = 1,0963 US$) y los pesos a dólares con el cambio oficial del 20/11/2026 ($ 1.580 por dólar). Ninguno incluye los vuelos desde España.',
	'costo_p2' => 'Son los precios que publican los lodges y los guías, para sus fechas: no son una cotización. El detalle de cada tipo de viaje está en <a href="{n:cuanto-cuesta-viaje-pesca-argentina}">cuánto cuesta un viaje de pesca a la Argentina 2026/27</a>, y con tus fechas y tu grupo, en el <a href="{p:estimador}">estimador de costo</a>.',
	'h_presup' => 'Cómo pedir presupuestos',
	'presup_p' => '<strong>Nos contás una sola vez dónde, cuándo y con quién querés pescar; nosotros escribimos a los lodges y guías que encajan.</strong> En 24 a 48 horas recibís sus opciones para compararlas lado a lado. Es gratis. Decinos también que viajás desde España: las fechas del vuelo y si llevás tus cañas. <a href="{p:presupuesto}">Pedir presupuesto</a> o <a href="{viaje}">armar el viaje en el formulario</a>. Antes de comparar dos opciones, pedile a cada una la misma lista por escrito: días de pesca, guía, equipo, licencia y traslados. El orden completo de los pasos, en <a href="{n:como-preparar-viaje-pesca-argentina}">cómo preparar un viaje de pesca a la Argentina</a>.',
	'faq_h' => 'Preguntas frecuentes',
	'cierre' => '<em>Fuentes leídas el 25/11/2026; los precios en dólares, entre el 4 y el 13/11/2026. Las condiciones de las aerolíneas, los requisitos de entrada y los precios cambian: confirmalos con la aerolínea, el consulado y el lodge o el guía antes de reservar.</em>',
	'datos' => [
		'Vuelo: directo Madrid–Buenos Aires (Ezeiza) con Iberia, Aerolíneas Argentinas y Air Europa, de unas 12 h 30 a 13 h; desde otras ciudades españolas, conectás en Madrid.',
		'Entrada: con pasaporte español ordinario no hace falta visado para turismo hasta 90 días; el pasaporte, con 6 meses de vigencia desde la entrada.',
		'Cañas: van en bodega y la aerolínea cobra aparte (Iberia, desde 100 € por trayecto si lo pagás online); con lodge o guía, el equipo suele estar incluido o se alquila allá.',
		'Auto (coche): no hace falta 4x4, la ruta de los Siete Lagos (Bariloche–San Martín de los Andes, 184 km) está toda asfaltada; con tu carné español podés manejar hasta un año, sin permiso internacional.',
		'Permiso: se paga en pesos; para un extranjero no residente, el ordinario de la Patagonia cuesta $ 50.000 por 1 día y $ 140.000 por 10 días (≈ US$ 89, unos 80 €, aproximado).',
		'Cuánto cuesta, aproximado y sin vuelos: de 550 a 640 € un fin de semana largo de dorado en Corrientes; de 5.290 a 8.390 € una semana en un lodge de trucha. Cambio: 1 € = 1,0963 US$ (BCE, 24/11/2026).',
	],
	'canas_l' => [
		'<strong>Iberia:</strong> el equipo de pesca va en como máximo dos bultos (uno con hasta dos cañas y otro con botas y aparejos), de hasta 23 kg (32 kg con recargo). En largo radio hacia América cuesta desde 100 € por trayecto si lo pagás online; según tu tarifa puede contar como una de tus piezas facturadas, y si superás tu número de piezas es un extra.',
		'<strong>Air Europa:</strong> el equipo deportivo no entra en la franquicia y se paga aparte. Un kit de pesca (dos cañas, un carrete, una red, un par de botas y una caja de aparejos) puede pesar hasta 23 kg (32 kg con recargo) y medir 250 cm lineales como máximo; en largo radio, 120 €.',
		'<strong>Aerolíneas Argentinas:</strong> preguntale el cargo para tu ruta; el tramo interno suele ser con ella.',
	],
	'tabla_cols' => [
		'Viaje',
		'Qué cuenta el precio',
		'Precio publicado (US$)',
		'En euros (aproximado)',
	],
	'tabla_rows' => [
		[ '<strong>Fin de semana largo de dorado en Corrientes</strong> (3 noches, 2 días de pesca)', 'Paquete en una posada de Esquina: alojamiento, comidas, lancha, guía, carnada, combustible y licencia', 'Desde US$ 605 por persona (grupo de tres) y US$ 705 (grupo de dos); precios «desde» del 4/11/2026', '≈ 550 a 640 € por persona' ],
		[ '<strong>10 días por tu cuenta en el norte de la Patagonia, con 3 días de guía</strong>', 'Solo guía y permiso: 3 días completos cerca de Bariloche, por bote para uno o dos pescadores, a US$ 500 el día (6/11/2026), más el permiso de 10 días ($ 140.000 ≈ US$ 89). No incluye alojamiento, comidas, auto ni vuelos', 'US$ 1.589 (1.500 + 89) si vas solo; US$ 839 (750 + 89) si compartís el bote con otro pescador', '≈ 1.450 € solo; ≈ 770 € compartiendo el bote' ],
		[ '<strong>Una semana en un lodge de trucha, Patagonia norte</strong> (7 noches, 6 días de pesca)', 'Todo incluido, habitación doble, guía compartido; la licencia depende del lodge (si no está incluida, sumá ≈ US$ 89)', 'US$ 5.800 a US$ 9.200 por persona (5/11/2026)', '≈ 5.290 a 8.390 € por persona' ],
	],
	'faq' => [
		[ '¿Qué hago con el material deportivo? ¿Tengo que facturarlo o lo alquilan ellos?', 'Las dos cosas son posibles. Con lodge o guía, las cañas y el equipo suelen estar incluidos o se alquilan allá (un equipo completo ronda los US$ 80 por día, ≈ 73 €, aproximado). Si llevás las tuyas, van en bodega y la aerolínea cobra aparte: Iberia, desde 100 € por trayecto en largo radio si lo pagás online; Air Europa, 120 €. A Aerolíneas Argentinas preguntale el cargo de tu ruta.' ],
		[ '¿Y el coche si quisiera ir solo?', 'Con tu carné español podés manejar hasta un año desde que entrás; no hace falta el permiso internacional. Pedí por escrito si el contrato permite ripio y qué cubre el seguro: los contratados fuera de la Argentina no suelen cubrir allí.' ],
		[ '¿Transporte hasta allí, es necesario un todoterreno?', 'No hace falta 4x4: la ruta de los Siete Lagos entre Bariloche y San Martín de los Andes (184 km) está toda asfaltada. El ripio aparece en los accesos a algunos ríos: preguntale al guía o al lodge por ese último tramo. Con lodge o guía, el traslado suele estar incluido.' ],
		[ '¿Hay vuelo directo desde España y desde qué aeropuerto salen los vuelos a la Patagonia?', 'Madrid–Buenos Aires (Ezeiza) tiene vuelo directo con Iberia, Aerolíneas Argentinas y Air Europa, de unas 12 h 30 a 13 h; desde otras ciudades españolas conectás en Madrid. Los vuelos internos salen de Aeroparque o de Ezeiza según el vuelo: mirá en el pasaje de qué aeropuerto sale cada tramo. Entre ambos hay entre 45 y 90 minutos según el tráfico.' ],
		[ '¿Cuánto cuesta en euros?', 'Aproximado y sin vuelos: de 550 a 640 € por persona un fin de semana largo de dorado en una posada de Corrientes (desde US$ 605 a 705), unos 1.450 € por 10 días por tu cuenta en el norte de la Patagonia contando solo 3 días de guía y el permiso (770 € si compartís el bote), y de 5.290 a 8.390 € una semana en un lodge de trucha (US$ 5.800 a 9.200). Al cambio del BCE del 24/11/2026, 1 € = 1,0963 US$.' ],
		[ '¿Necesito visado, y qué permiso de pesca?', 'Con pasaporte español ordinario no hace falta visado para turismo hasta 90 días; el pasaporte, con 6 meses de vigencia desde la entrada. El permiso de pesca se paga aparte, en pesos: en la Patagonia, $ 140.000 (≈ US$ 89) por 10 días para un extranjero no residente. Calculalo en la <a href="{p:licencia}">calculadora de licencia de pesca</a>.' ],
	],
];

$n = $N;

$fn_nota = function ( string $c ) {
	return function ( string $i ) use ( $c ) {
		$p = pm_buscar( 'pm_nota', $c, $i ) ?? pm_buscar( 'pm_nota', $c, 'es' );
		return $p ? get_permalink( $p->ID ) : '';
	};
};
$fn_guia = function ( string $c ) {
	return function ( string $i ) use ( $c ) {
		$p = pm_tp_guia( $c, $i );
		return $p ? get_permalink( $p->ID ) : '';
	};
};
$fn_pagina = function ( string $k ) {
	return function ( string $i ) use ( $k ) {
		return pm_url( PM_SEGMENTOS[ $i ][ $k ], $i );
	};
};
// Sources: official pages first; airline and press pages are not official; our own notes carry their own read dates.
$fuentes_def = [
	[ 'Banco Central Europeo: tipo de cambio de referencia del euro, dólar estadounidense (1 € = 1,0963 US$, 24/11/2026)', 'https://www.ecb.europa.eu/stats/policy_and_exchange_rates/euro_reference_exchange_rates/html/eurofxref-graph-usd.en.html', 1, '20261125' ],
	[ 'Consulado General de la Argentina en Madrid: ¿necesito visa? (españoles, turismo hasta 90 días)', 'https://cmadr.cancilleria.gob.ar/es/2-%C2%BFnecesito-visa', 1, '20261125' ],
	[ 'Embajada de la Argentina en España: pasaporte y documentación (validez de 6 meses desde la entrada)', 'https://eespa.cancilleria.gob.ar/es/content/pasaporte-y-documentaci%C3%B3n', 1, '20261125' ],
	[ 'Gobierno argentino: información sobre licencias de conducir internacionales, España (hasta un año desde el ingreso)', 'https://www.argentina.gob.ar/transporte/mundial-sub-20-argentina-2023/informacion-sobre-licencias-de-conducir-internacionales-2', 1, '20261125' ],
	[ 'Reglamento General de Pesca Deportiva Continental Patagónico 2026/27: categorías y valores del permiso', 'https://reglamentodepesca.org.ar/reglamento/', 1, '20261106' ],
	[ 'Dirección de Recursos Naturales de Corrientes: Disposición 853/22 (devolución obligatoria, sin veda por fechas)', 'https://www.recnaturalesctes.gob.ar/', 1, '20261124' ],
	[ 'Iberia: equipajes especiales (equipo de pesca, cargos de largo radio)', 'https://www.iberia.com/es/equipajes/especiales/', 0, '20261125' ],
	[ 'Air Europa: equipaje deportivo (kit de pesca, cargos)', 'https://www.aireuropa.com/es/es/aea/informacion-para-volar/equipaje/equipajes-especiales/equipaje-deportivo.html', 0, '20261125' ],
	[ 'Aerolíneas Argentinas: vuelos directos a Madrid y Roma', 'https://www.aerolineas.com.ar/destinos/europa/vuelos-directos-a-madrid-y-roma', 0, '20261125' ],
	[ 'Interpatagonia: Ruta de los Siete Lagos (184 km, asfaltada desde 2015)', 'https://www.interpatagonia.com/sietelagos/', 0, '20261125' ],
	[ 'Alamo: oficina del aeropuerto de Bariloche (condiciones de alquiler)', 'https://www.alamo.com/es/oficinas-de-alquiler-de-autos/ar/aeropuerto-internacional-de-bariloche-lka0.html', 0, '20261125' ],
	[ 'Pescamapa: cuánto cuesta un viaje de pesca a la Argentina 2026/27 (precios por tipo de viaje)', $fn_nota( 'cuanto-cuesta-viaje-pesca-argentina' ), 0, '20261116' ],
	[ 'Pescamapa: un día de pesca con mosca en Bariloche y el Río Limay (precios al 6/11/2026)', $fn_nota( 'precio-pesca-bariloche-limay' ), 0, '20261106' ],
	[ 'Pescamapa: una semana de pesca de trucha en Neuquén y Chubut (5/11/2026)', $fn_nota( 'precio-semana-trucha-patagonia-norte' ), 0, '20261105' ],
	[ 'Pescamapa: viaje de pesca de dorado con amigos (precios «desde» del 4/11/2026)', $fn_nota( 'viaje-dorado-amigos-precios' ), 0, '20261104' ],
	[ 'Pescamapa: guía de Bariloche (accesos y ripio)', $fn_guia( 'bariloche' ), 0, '20261030' ],
	[ 'Pescamapa: guía de San Martín y Junín de los Andes (accesos y ripio)', $fn_guia( 'san-martin-junin' ), 0, '20261030' ],
	[ 'Pescamapa: calculadora de licencia de pesca (valores 2026/27)', $fn_pagina( 'licencia' ), 0, '20261106' ],
];

$nota_tipo = 'como';
$origen    = 'Viaje de pesca a la Argentina desde España, 25/11/2026 (pedido del dueño: amigos en España consultaron a tres asistentes de IA; seguían preguntando por el material, el auto y el todoterreno). Precios de pesca y permisos: los nuestros ya publicados (nota de costos del 16/11, precios.json, licencias.json). Cambio: BCE 24/11/2026 (1,0963 US$ por euro); pesos a dólares al oficial del 20/11/2026 que usan las calculadoras ($ 1.580). Vuelos, entrada, equipaje de las aerolíneas, licencia de conducir y ruta de los Siete Lagos: verificados en las páginas abiertas el 25/11/2026. Sin tarifas aéreas, sin pagos con tarjeta ni cambio, sin lista de aeropuertos de los vuelos internos por destino, sin la política de equipaje de pesca de Aerolíneas Argentinas (no hay página oficial). Detalle en docs/borradores/viaje-desde-espana-2026-11-25.md. Portada: la de precio-pesca-patagonia-sur (provisoria).';

$armar = function ( array $n ): string {
	return pm_sc_p( $n['p0'] ) . pm_sc_lista( $n['datos'] ) . pm_sc_p( $n['comercial'] )
		. pm_sc_h2( $n['h_cuando'] ) . pm_sc_p( $n['cuando_p'] )
		. pm_sc_h2( $n['h_llegar'] ) . pm_sc_p( $n['llegar_p1'] ) . pm_sc_p( $n['llegar_p2'] ) . pm_sc_p( $n['llegar_p3'] )
		. pm_sc_h2( $n['h_canas'] ) . pm_sc_p( $n['canas_p'] ) . pm_sc_lista( $n['canas_l'] ) . pm_sc_p( $n['canas_p2'] )
		. pm_sc_h2( $n['h_auto'] ) . pm_sc_p( $n['auto_p1'] ) . pm_sc_p( $n['auto_p2'] ) . pm_sc_p( $n['auto_p3'] )
		. pm_sc_h2( $n['h_permisos'] ) . pm_sc_p( $n['permisos_p'] )
		. pm_sc_h2( $n['h_costo'] ) . pm_sc_p( $n['costo_p'] ) . pm_tp_tabla( $n['tabla_cols'], $n['tabla_rows'] ) . pm_sc_p( $n['costo_p2'] )
		. pm_sc_h2( $n['h_presup'] ) . pm_sc_p( $n['presup_p'] )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] )
		. pm_sc_p( $n['cierre'] );
};

/** Resolve {n:clave}, {n:clave|texto}, {g:clave}, {p:pagina} (segment of the site) and {viaje}; fails on anything left over. */
$resolver = function ( string $html, string $i ): string {
	$nota = function ( string $c ) use ( $i ): string {
		$por = [];
		foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $c, 'numberposts' => -1, 'fields' => 'ids' ] ) as $id ) {
			$por[ pm_idioma_de( (int) $id ) ] = (int) $id;
		}
		$id = $por[ $i ] ?? ( $por['es'] ?? 0 );
		$id || WP_CLI::error( "No encuentro la nota {$c} (ni en español)." );
		return get_permalink( $id );
	};
	$guia = function ( string $c ) use ( $i ): string {
		$g = pm_tp_guia( $c, $i );
		$g || WP_CLI::error( "No encuentro la guía {$c}." );
		return get_permalink( $g->ID );
	};
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\|([^}]+)\}/', fn( $m ) => '<a href="' . esc_url( $nota( $m[1] ) ) . '">' . $m[2] . '</a>', $html );
	$html = preg_replace_callback( '/\{n:([a-z0-9-]+)\}/', fn( $m ) => esc_url( $nota( $m[1] ) ), $html );
	$html = preg_replace_callback( '/\{g:([a-z0-9-]+)\}/', fn( $m ) => esc_url( $guia( $m[1] ) ), $html );
	$html = preg_replace_callback( '/\{p:([a-z]+)\}/', function ( $m ) use ( $i ) {
		isset( PM_SEGMENTOS[ $i ][ $m[1] ] ) || WP_CLI::error( "segmento desconocido {$m[1]}" );
		return esc_url( pm_url( PM_SEGMENTOS[ $i ][ $m[1] ], $i ) );
	}, $html );
	$html = str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) ), $html );
	if ( preg_match( '/\{(n:|g:|p:|viaje)[^}]*\}?/', $html, $m ) ) {
		WP_CLI::error( "queda un marcador sin resolver: {$m[0]}" );
	}
	return $html;
};

// Cover photo: the attachment already loaded for another note (credit and alt live on the attachment).
$nota_foto = pm_buscar( 'pm_nota', $foto_de, 'es' );
$att       = $nota_foto ? (int) get_post_thumbnail_id( $nota_foto->ID ) : 0;
$att || WP_CLI::error( "la nota {$foto_de} (es) no tiene foto de portada: la foto es obligatoria." );

// Idempotence: same clave (any status) or same slug (any clave) means nothing is touched.
$ya_clave = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i ) );
$ya_slug  = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'name' => $n['slug'], 'numberposts' => 1 ] );
$cambios  = 0;
if ( $ya_clave ) {
	WP_CLI::log( "$clave ($i) #{$ya_clave[0]->ID} ya existe: no toco nada." );
} elseif ( $ya_slug ) {
	WP_CLI::error( "el slug {$n['slug']} ya lo usa #{$ya_slug[0]->ID} con otra clave: no creo la nota." );
} else {
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	count( $n['faq'] ) >= 5 && count( $n['faq'] ) <= 10 || WP_CLI::error( "$i: la FAQ debe tener entre 5 y 10 preguntas." );
	$cuerpo = $resolver( $armar( $n ), $i );
	if ( preg_match( '/PENDIENTE|no pudimos|agencia|operador|prestador/iu', wp_strip_all_tags( $cuerpo ), $mm ) ) {
		WP_CLI::error( "$i: el texto tiene una palabra prohibida: {$mm[0]}." );
	}
	$datos   = array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] );
	$fuentes = [];
	foreach ( $fuentes_def as [ $nombre, $url, $oficial, $consultada ] ) {
		$fuentes[] = [ 'nombre' => $nombre, 'url' => is_callable( $url ) ? $url( $i ) : $url, 'oficial' => $oficial, 'consultada' => $consultada ];
	}
	foreach ( $fuentes as $f ) {
		$f['url'] || WP_CLI::error( "fuente sin URL: {$f['nombre']}" );
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) «{$n['titulo']}» (" . mb_strlen( $n['titulo'] ) . " car.) /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . ' fuentes, ' . count( $n['faq'] ) . " FAQ; title $tl / description $dl; portada #$att" );
	$cambios++;
	if ( $aplicar ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
		is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
		$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => $datos, 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'], 'nota_tipo' => $nota_tipo, 'nota_fecha' => $hoy, 'nota_origen' => $origen ];
		foreach ( $campos as $k => $v ) {
			update_field( $k, $v, $id );
		}
		set_post_thumbnail( $id, $att );
		update_post_meta( $id, '_pm_es_editado', time() );
		$cont = get_post_field( 'post_content', $id );
		$ok   = get_post( $id )->post_name === $n['slug'] && pm_clave_de( $id ) === $clave && count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $datos ) && false !== strpos( $cont, 'pm-faq' ) && count( pm_faqs_contenido( $cont ) ) === count( $n['faq'] ) && ! preg_match( '/\{(n|g|p):|\{viaje\}/', $cont );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
		$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
		$e  = pm_nota( $id );
		$ok = $e && $e['fecha'] && (int) $e['foto'] === $att;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': la nota lee fecha ' . ( $e['fecha'] ?? '?' ) . ', foto #' . ( $e['foto'] ?? 0 ) );
		$ok || WP_CLI::error( 'la nota no lee fecha o foto como se esperaba.' );
		flush_rewrite_rules( false );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
