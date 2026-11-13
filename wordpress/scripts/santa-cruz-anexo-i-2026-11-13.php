<?php
/**
 * Santa Cruz Anexo I read from the Boletín Oficial text, 13/11/2026 (second pass over Disposición 241/2026, Secretaría de Estado de Pesca y
 * Acuicultura, dated 22/09/2026, published in BO 6164 of 8/10/2026 and BO 6166 of 15/10/2026, in force from 1/10/2026; the PDF of the Anexo
 * was read in full, file 21 of the research folder fuentes-2-1-20261113-2207).
 *  - Jurassic Lake (es/en/pt): the FAQ "mouth of the Barrancoso" no longer says "the copies differ / Annex I may set distances": the Barrancoso
 *    and Strobel entries have NO exclusion distance of their own (only arts. 11 and 15 a apply). The summary field says the same. A sweep reads
 *    every guide, note, species page and provider card for a "100 m" next to "Barrancoso" and only reports it (a 100 m rule is NOT in the text).
 *  - Río Gallegos (es/en/pt): Gallegos Chico "strict" release, Penitente hook, art. 16 (motors in rivers), gravel/spawning advice (Anexo I preamble),
 *    FAQ on motors, "what we have not read" paragraph, hidden pending line (Truchaike and Los Plateados leave it), summary field, official sources.
 *  - El Calafate (es/en/pt): Río Santa Cruz detail (hook, Chinook 1/8-30/11/2027, róbalo, shore zone, R.P.A.T. trolling, hydroelectric closure),
 *    two FAQs, official sources.
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = santa-cruz-anexo-i-2026-11-13).
 */
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
	/** Replace the text of one heading (exact plain text), keeping its markup; null when it is not there. */
	function pm_sc_renombrar( string $c, string $viejo, string $nuevo ): ?string {
		$n = 0;
		$r = preg_replace_callback( '#(<h[1-6][^>]*>)(.*?)(</h[1-6]>)#su', function ( $m ) use ( $viejo, $nuevo, &$n ) {
			if ( 0 === $n && trim( wp_strip_all_tags( $m[2] ) ) === $viejo ) {
				$n++;
				return $m[1] . $nuevo . $m[3];
			}
			return $m[0];
		}, $c );
		return $n ? $r : null;
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
	function pm_sc_campo( int $id, string $campo, string $valor, string $etiqueta ): void {
		update_field( $campo, $valor, $id );
		if ( pm_campo( $campo, $id ) !== $valor ) {
			WP_CLI::error( "$etiqueta: el campo $campo no quedó como se esperaba." );
		}
	}
	function pm_sc_modo( bool $aplicar ): string {
		return $aplicar ? 'APLICA' : 'SIMULACRO';
	}
}



$aplicar = true;
$hoy     = '20261113';
$cambios = 0;

$u_bo1 = 'https://boletinoficial.santacruz.gob.ar/legislacion/disposiciones-completas/60895';
$u_bo2 = 'https://boletinoficial.santacruz.gob.ar/legislacion/disposiciones-completas/60897';
$u_pdf = 'https://boletinoficial.santacruz.gob.ar/adjuntos/DISPOSICION_N_241_-_REGLAMENTO_2.pdf';

/** Replace $viejo by $nuevo in $texto; null when $viejo is not there. */
function pm_t_cambiar( string $texto, string $viejo, string $nuevo ): ?string {
	return false === strpos( $texto, $viejo ) ? null : str_replace( $viejo, $nuevo, $texto );
}
/** Apply [ viejo, nuevo, etiqueta ] pairs to $texto; pairs whose $nuevo is already there are done; a missing $viejo is a warning. */
function pm_t_aplicar( string $texto, array $pares, string $donde, array &$tareas ): string {
	foreach ( $pares as [ $viejo, $nuevo, $etiqueta ] ) {
		if ( false !== strpos( $texto, $nuevo ) ) {
			continue;
		}
		$r = pm_t_cambiar( $texto, $viejo, $nuevo );
		if ( null === $r ) {
			WP_CLI::warning( "$donde: no encontré el texto de «{$etiqueta}»." );
			continue;
		}
		$texto    = $r;
		$tareas[] = $etiqueta;
	}
	return $texto;
}
/** Add the official sources whose URL is not there yet; rows to write or null. */
function pm_t_fuentes( int $id, array $quiero, string $hoy ): ?array {
	$filas = array_map( function ( $f ) {
		$f['consultada'] = isset( $f['consultada'] ) ? str_replace( '-', '', (string) $f['consultada'] ) : '';
		return $f;
	}, array_values( array_filter( pm_filas( 'fuentes', $id ), 'is_array' ) ) );
	$urls  = array_column( $filas, 'url' );
	$nuevo = false;
	foreach ( $quiero as [ $nombre, $url ] ) {
		if ( ! in_array( $url, $urls, true ) ) {
			$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => 1, 'consultada' => $hoy ];
			$nuevo   = true;
		}
	}
	return $nuevo ? $filas : null;
}

$X = [
	'es' => [
		'src_bo'  => 'Boletín Oficial de Santa Cruz: Disposición 241/2026 del 22/9/2026, Reglamento General de Pesca Deportiva Continental 2026/2027 (BO 6164, 8/10/2026; leída el 13/11/2026)',
		'src_bo2' => 'Boletín Oficial de Santa Cruz: Disposición 241/2026 del 22/9/2026, tercera parte (BO 6166, 15/10/2026; leída el 13/11/2026)',
		'src_pdf' => 'Boletín Oficial de Santa Cruz: Disposición 241/2026, texto del reglamento y del Anexo I (PDF, leído completo el 13/11/2026)',
		'jur' => [
			'faq_q' => '¿Se puede pescar cerca de la desembocadura del Barrancoso?',
			'viejo' => 'El Anexo I podría fijar además distancias mínimas alrededor de las bocas del Strobel y del Barrancoso; las copias que comparamos difieren en esto, así que preguntale al operador qué sectores están habilitados en tus fechas y qué distancia respetan.',
			'nuevo' => 'Las fichas del Anexo I del Strobel y del Barrancoso (Disposición 241/2026, Boletín Oficial de Santa Cruz del 8/10/2026) no fijan una distancia de exclusión propia: rigen solo esos dos artículos generales. Preguntale al operador qué sectores están habilitados en tus fechas.',
			'res_viejo' => ' Leído el 30/10/2026 en dos copias del reglamento; las distancias de exclusión que figuran en una sola de ellas no se publican.',
			'res_nuevo' => ' Fichas leídas del texto publicado en el Boletín Oficial de Santa Cruz (n.º 6164 del 8/10/2026 y n.º 6166 del 15/10/2026): no traen una distancia de exclusión propia para el Strobel ni para el Barrancoso.',
		],
		'gal' => [
			'cuerpo' => [
				[ '<strong>solo mosca</strong> y devolución obligatoria. Hay veda total desde el puente Nuevo', '<strong>solo mosca</strong> y devolución obligatoria estricta. Hay veda total desde el puente Nuevo', 'Gallegos Chico: devolución obligatoria estricta' ],
				[ 'Penitente:</strong> del 1 de noviembre de 2026 al 3 de mayo de 2027, captura y devolución obligatoria.</li>', 'Penitente:</strong> del 1 de noviembre de 2026 al 3 de mayo de 2027, captura y devolución obligatoria, con anzuelo simple, con o sin rebaba.</li>', 'Penitente: anzuelo simple' ],
				[ '<strong>Lo que todavía no leímos:</strong> del Anexo I, solo las fichas que cita esta guía; el propio anexo dice que lista algunos de los ambientes más destacados. Las reglas cambian cada temporada: confirmalas con tu guía o con la autoridad antes de salir.',
				  '<strong>Qué leímos:</strong> las fichas del río Gallegos, el Gallegos Chico, el Chico Sur y el Penitente están leídas del texto de la Disposición 241/2026 publicado en el Boletín Oficial de Santa Cruz (22/9/2026; n.º 6164 del 8/10/2026 y n.º 6166 del 15/10/2026). El Anexo I lista solo algunos ambientes; a los que no figuran se les aplica el reglamento general. Las reglas cambian cada temporada: confirmalas con tu guía o con la autoridad antes de salir.', 'párrafo «lo que todavía no leímos»' ],
				[ 'PENDIENTE: las fichas del Anexo I más allá de las que cita la guía; Bella Sofía, Los Plateados, Truchaike y Los Morros (sin sitio propio que abriera);', 'PENDIENTE: Bella Sofía y Los Morros (sin sitio propio que abriera);', 'línea PENDIENTE: salen Truchaike, Los Plateados y las fichas del anexo' ],
			],
			'rx_pos' => '/área de exclusión/u',
			'bloque' => [
				'<strong>Motores (art. 16):</strong> en todos los ríos de la provincia está prohibida la navegación con motor, salvo las excepciones que traiga el Anexo I; las fichas del río Gallegos, el Gallegos Chico, el Chico Sur y el Penitente no traen ninguna.',
				'<strong>Grava y desove:</strong> el Anexo I recomienda «no vadear o ingresar en sectores con grava (pedregullo fino) en ríos y arroyos hasta el 01/01/2027, para evitar dañar las camas de desove». Es una recomendación, no una prohibición.',
				'Fuente: Disposición 241/2026 (22/9/2026), Boletín Oficial de Santa Cruz n.º 6164 (8/10/2026) y n.º 6166 (15/10/2026).',
			],
			'marca' => 'Motores (art. 16)',
			'faq' => [
				[ '¿Se puede navegar con motor en el río Gallegos?', 'No. El artículo 16 del Reglamento General prohíbe la navegación con motor en todos los ríos de la provincia, salvo las excepciones del Anexo I, y las fichas del río Gallegos, el Gallegos Chico, el Chico Sur y el Penitente no traen ninguna (Disposición 241/2026, Boletín Oficial de Santa Cruz n.º 6164 y n.º 6166).' ],
			],
			'faq_cambios' => [
				[ 'El anexo que leímos no impone mosca en el cauce principal del Gallegos', 'El Anexo I no impone mosca en el cauce principal del Gallegos', 'FAQ solo mosca: el Anexo I' ],
			],
			'res_viejo' => 'del Anexo I, solo las fichas citadas arriba.',
			'res_nuevo' => 'del Anexo I, las fichas del Gallegos, Gallegos Chico, Chico Sur y Penitente, leídas del texto del Boletín Oficial (n.º 6164 y n.º 6166).',
		],
		'cal' => [
			'rx_h2' => '/^El permiso y cuánto cuesta$/u',
			'h2' => 'El río Santa Cruz en detalle',
			'intro' => 'Lo que trae el Anexo I para el río Santa Cruz, además de lo de arriba (Disposición 241/2026, Boletín Oficial de Santa Cruz n.º 6164 del 8/10/2026 y n.º 6166 del 15/10/2026):',
			'items' => [
				'<strong>Anzuelo:</strong> uno simple o doble, con o sin rebaba, salvo excepciones.',
				'<strong>Zona de pesca de costa autorizada:</strong> desde la bajada a la ribera a la altura del Monumento Héroes de Malvinas, cerca de la calle Bella Vista, hasta el cruce del Interconectado Provincial, en ambas márgenes (norte y sur); incluye la isla Pavón, en su margen desde el puente de la ruta nacional 3 hasta la zona que se conoce como «La Noria», que se marcará con cartelería.',
				'<strong>Salmón chinook:</strong> temporada acotada del 1 de agosto al 30 de noviembre de 2027, con señuelo libre con un anzuelo simple o doble, con o sin rebaba; límite diario de un ejemplar por pescador y el resto de las especies que se capturen se devuelven. Zona de costa: desde la bajada urbana de la calle Bella Vista hasta el cruce del Interconectado Provincial.',
				'<strong>Róbalo</strong> (<em>Eleginops maclovinus</em>): todo el año, con spinning, mosca o línea de fondo con hasta tres anzuelos y carnada libre; sin plomo. Hasta diez por pescador y por jornada, con talla mínima de 30 cm de longitud total; es la única especie que se puede sacrificar y las demás se liberan. Zona: desde la bajada a la ribera del Monumento Héroes de Malvinas, cerca de la calle Bella Vista, hasta el alambrado perimetral de la Estancia La Esperanza (margen norte).',
				'<strong>Trolling:</strong> solo para guías de pesca registrados en el R.P.A.T., con unidades flotantes motorizadas homologadas que cumplan el artículo 16, en dos tramos: del puente de la ruta nacional 3 a la toma de agua de Puerto Santa Cruz (parte baja) y de la Chacra de Teira (Tapera Los Frutales) al cruce del Interconectado (parte alta).',
				'<strong>Veda total en las obras hidroeléctricas:</strong> el tramo que va de la Estancia El Rincón (zona de «Los Laberintos») hasta «La Barrancosa», delimitado por los campamentos de obra de Cóndor Cliff (70° 07\' O) y el punto fijado en 70° 10\' O.',
			],
			'faqs' => [
				[ '¿Se puede pescar róbalo en el río Santa Cruz?', 'Sí, todo el año, con spinning, mosca o línea de fondo con hasta tres anzuelos y carnada libre, sin plomo. El cupo es de diez por pescador y por jornada, con talla mínima de 30 cm, y es la única especie que se puede sacrificar. La zona va del Monumento Héroes de Malvinas, cerca de la calle Bella Vista, hasta el alambrado de la Estancia La Esperanza, en la margen norte (Anexo I de la Disposición 241/2026).' ],
				[ '¿Cuándo se pesca el salmón chinook en el río Santa Cruz?', 'Del 1 de agosto al 30 de noviembre de 2027, con un ejemplar por día y por pescador; las demás especies que se capturen se devuelven. La zona de costa va de la bajada urbana de la calle Bella Vista al cruce del Interconectado Provincial (Anexo I de la Disposición 241/2026).' ],
			],
		],
	],
	'en' => [
		'src_bo'  => 'Santa Cruz Official Gazette: Disposición 241/2026 of 22/9/2026, General Regulation of Continental Sport Fishing 2026/2027 (BO 6164, 8/10/2026; read 13/11/2026)',
		'src_bo2' => 'Santa Cruz Official Gazette: Disposición 241/2026 of 22/9/2026, part 3 (BO 6166, 15/10/2026; read 13/11/2026)',
		'src_pdf' => 'Santa Cruz Official Gazette: Disposición 241/2026, text of the regulation and of Annex I (PDF, read in full on 13/11/2026)',
		'jur' => [
			'faq_q' => 'Can I fish near the mouth of the Barrancoso?',
			'viejo' => 'Annex I may also set minimum distances around the mouths of Strobel and the Barrancoso; the copies we compared differ on this, so ask your operator which sectors are open on your dates and the distance they respect.',
			'nuevo' => 'The Annex I entries for Strobel and the Barrancoso (Disposición 241/2026, Santa Cruz Official Gazette of 8/10/2026) set no exclusion distance of their own: only those two general articles apply. Ask your operator which sectors are open on your dates.',
			'res_viejo' => ' Read on 30/10/2026 in two copies of the regulation; the exclusion distances that appear in only one of them are not published.',
			'res_nuevo' => ' Entries read from the text published in the Santa Cruz Official Gazette (no. 6164 of 8/10/2026 and no. 6166 of 15/10/2026): they carry no exclusion distance of their own for Strobel or the Barrancoso.',
		],
		'gal' => [
			'cuerpo' => [
				[ '<strong>fly only</strong> and mandatory release. Fishing is banned from the Puente Nuevo', '<strong>fly only</strong> and strict mandatory release. Fishing is banned from the Puente Nuevo', 'Gallegos Chico: strict release' ],
				[ 'Penitente:</strong> 1 November 2026 to 3 May 2027, mandatory catch and release.</li>', 'Penitente:</strong> 1 November 2026 to 3 May 2027, mandatory catch and release, single hook with or without barb.</li>', 'Penitente: single hook' ],
				[ '<strong>What we have not read yet:</strong> of Annex I, only the entries this guide cites; the annex itself says it lists some of the most outstanding environments. Rules change every season: confirm them with your guide or the authority before you go.',
				  '<strong>What we read:</strong> the entries for the Río Gallegos, the Gallegos Chico, the Chico Sur and the Penitente are read from the text of Disposición 241/2026 published in the Santa Cruz Official Gazette (22/9/2026; no. 6164 of 8/10/2026 and no. 6166 of 15/10/2026). Annex I lists only some environments; waters not listed fall under the general regulation. Rules change every season: confirm them with your guide or the authority before you go.', 'paragraph «what we have not read yet»' ],
				[ 'PENDIENTE: the Annex I entries beyond the ones this guide cites; Bella Sofía, Los Plateados, Truchaike and Los Morros (no site of their own that opened);', 'PENDIENTE: Bella Sofía and Los Morros (no site of their own that opened);', 'PENDIENTE line: Truchaike, Los Plateados and the annex entries leave' ],
			],
			'rx_pos' => '/exclusion area/u',
			'bloque' => [
				'<strong>Motors (Article 16):</strong> motor navigation is banned in all the province\'s rivers, except for the exceptions Annex I lists; the entries for the Río Gallegos, the Gallegos Chico, the Chico Sur and the Penitente list none.',
				'<strong>Gravel and spawning:</strong> Annex I recommends “not wading or entering gravel areas (fine gravel) in rivers and streams until 01/01/2027, to avoid damaging spawning beds” (our translation). It is a recommendation, not a ban.',
				'Source: Disposición 241/2026 (22/9/2026), Santa Cruz Official Gazette no. 6164 (8/10/2026) and no. 6166 (15/10/2026).',
			],
			'marca' => 'Motors (Article 16)',
			'faq' => [
				[ 'Can I use a motor on the Río Gallegos?', 'No. Article 16 of the General Regulation bans motor navigation in all the province\'s rivers, except for the exceptions in Annex I, and the entries for the Río Gallegos, the Gallegos Chico, the Chico Sur and the Penitente list none (Disposición 241/2026, Santa Cruz Official Gazette no. 6164 and no. 6166).' ],
			],
			'faq_cambios' => [
				[ 'The annex we read does not impose fly on the main Gallegos channel', 'Annex I does not impose fly on the main Gallegos channel', 'FAQ fly only: Annex I' ],
			],
			'res_viejo' => 'of Annex I, only the entries cited above.',
			'res_nuevo' => 'of Annex I, the entries for the Gallegos, Gallegos Chico, Chico Sur and Penitente, read from the Official Gazette text (no. 6164 and no. 6166).',
		],
		'cal' => [
			'rx_h2' => '/^The permit and what it costs$/u',
			'h2' => 'The Santa Cruz River in detail',
			'intro' => 'What Annex I says about the Santa Cruz River, on top of the above (Disposición 241/2026, Santa Cruz Official Gazette no. 6164 of 8/10/2026 and no. 6166 of 15/10/2026):',
			'items' => [
				'<strong>Hook:</strong> one single or double hook, barbed or barbless, with exceptions.',
				'<strong>Authorised shore fishing zone:</strong> from the river access at the Héroes de Malvinas monument, near Bella Vista street, to the crossing of the Provincial Interconnected power line, on both banks (north and south); it includes Isla Pavón, on its bank from the Route 3 bridge to the area locally known as “La Noria”, which will be marked with signs.',
				'<strong>Chinook salmon:</strong> a short season from 1 August to 30 November 2027, with a free lure with one single or double hook, barbed or barbless; daily limit of one fish per angler, and every other species caught must be released. Shore zone: from the urban access at Bella Vista street to the crossing of the Provincial Interconnected power line.',
				'<strong>Róbalo</strong> (<em>Eleginops maclovinus</em>): all year, with spinning, fly or bottom line with up to three hooks and free bait; no lead. Up to ten per angler per day, minimum size 30 cm total length; it is the only species that may be kept and all others are released. Zone: from the river access at the Héroes de Malvinas monument, near Bella Vista street, to the perimeter fence of Estancia La Esperanza (north bank).',
				'<strong>Trolling:</strong> only for fishing guides registered in the R.P.A.T., with approved motorised floating units that meet Article 16, on two stretches: from the Route 3 bridge to the Puerto Santa Cruz water intake (lower part) and from the Chacra de Teira (Tapera Los Frutales) to the Interconnected power line crossing (upper part).',
				'<strong>Total closure at the hydroelectric works:</strong> the stretch from Estancia El Rincón (“Los Laberintos” area) to “La Barrancosa”, bounded by the Cóndor Cliff work camps (70° 07\' W) and the point fixed at 70° 10\' W.',
			],
			'faqs' => [
				[ 'Can I fish róbalo in the Santa Cruz River?', 'Yes, all year, with spinning, fly or bottom line with up to three hooks and free bait, without lead. The limit is ten per angler per day, minimum size 30 cm, and it is the only species that may be kept. The zone runs from the Héroes de Malvinas monument, near Bella Vista street, to the fence of Estancia La Esperanza on the north bank (Annex I of Disposición 241/2026).' ],
				[ 'When can I fish Chinook salmon in the Santa Cruz River?', 'From 1 August to 30 November 2027, one fish per day per angler; other species caught must be released. The shore zone runs from the urban access at Bella Vista street to the crossing of the Provincial Interconnected power line (Annex I of Disposición 241/2026).' ],
			],
		],
	],
	'pt' => [
		'src_bo'  => 'Boletim Oficial de Santa Cruz: Disposición 241/2026 de 22/9/2026, Regulamento Geral de Pesca Esportiva Continental 2026/2027 (BO 6164, 8/10/2026; lido em 13/11/2026)',
		'src_bo2' => 'Boletim Oficial de Santa Cruz: Disposición 241/2026 de 22/9/2026, terceira parte (BO 6166, 15/10/2026; lido em 13/11/2026)',
		'src_pdf' => 'Boletim Oficial de Santa Cruz: Disposición 241/2026, texto do regulamento e do Anexo I (PDF, lido por completo em 13/11/2026)',
		'jur' => [
			'faq_q' => 'Posso pescar perto da boca do Barrancoso?',
			'viejo' => 'O Anexo I pode fixar também distâncias mínimas ao redor das bocas do Strobel e do Barrancoso; as cópias que comparamos divergem nesse ponto, então pergunte ao seu operador quais setores estão abertos nas suas datas e que distância respeitam.',
			'nuevo' => 'As fichas do Anexo I do Strobel e do Barrancoso (Disposición 241/2026, Boletim Oficial de Santa Cruz de 8/10/2026) não fixam uma distância de exclusão própria: valem só esses dois artigos gerais. Pergunte ao seu operador quais setores estão abertos nas suas datas.',
			'res_viejo' => ' Lido em 30/10/2026 em duas cópias do regulamento; as distâncias de exclusão que aparecem em só uma delas não são publicadas.',
			'res_nuevo' => ' Fichas lidas do texto publicado no Boletim Oficial de Santa Cruz (n.º 6164 de 8/10/2026 e n.º 6166 de 15/10/2026): não trazem distância de exclusão própria para o Strobel nem para o Barrancoso.',
		],
		'gal' => [
			'cuerpo' => [
				[ '<strong>só mosca</strong> e devolução obrigatória. Há defeso total da ponte', '<strong>só mosca</strong> e devolução obrigatória estrita. Há defeso total da ponte', 'Gallegos Chico: devolução obrigatória estrita' ],
				[ 'Penitente:</strong> de 1º de novembro de 2026 a 3 de maio de 2027, captura e devolução obrigatória.</li>', 'Penitente:</strong> de 1º de novembro de 2026 a 3 de maio de 2027, captura e devolução obrigatória, com anzol simples, com ou sem farpa.</li>', 'Penitente: anzol simples' ],
				[ '<strong>O que ainda não lemos:</strong> do Anexo I, só as fichas que este guia cita; o próprio anexo diz que lista alguns dos ambientes mais destacados. As regras mudam a cada temporada: confirme-as com seu guia ou com a autoridade antes de sair.',
				  '<strong>O que lemos:</strong> as fichas do rio Gallegos, do Gallegos Chico, do Chico Sur e do Penitente foram lidas do texto da Disposición 241/2026 publicado no Boletim Oficial de Santa Cruz (22/9/2026; n.º 6164 de 8/10/2026 e n.º 6166 de 15/10/2026). O Anexo I lista só alguns ambientes; os que não constam seguem o regulamento geral. As regras mudam a cada temporada: confirme-as com seu guia ou com a autoridade antes de sair.', 'parágrafo «o que ainda não lemos»' ],
				[ 'PENDIENTE: as fichas do Anexo I além das que este guia cita; Bella Sofía, Los Plateados, Truchaike e Los Morros (sem site próprio que abrisse);', 'PENDIENTE: Bella Sofía e Los Morros (sem site próprio que abrisse);', 'linha PENDIENTE: saem Truchaike, Los Plateados e as fichas do anexo' ],
			],
			'rx_pos' => '/área de exclusão/u',
			'bloque' => [
				'<strong>Motores (artigo 16):</strong> em todos os rios da província é proibida a navegação com motor, salvo as exceções que o Anexo I traga; as fichas do rio Gallegos, do Gallegos Chico, do Chico Sur e do Penitente não trazem nenhuma.',
				'<strong>Cascalho e desova:</strong> o Anexo I recomenda «não vadear ou entrar em setores com cascalho (pedregulho fino) em rios e riachos até 01/01/2027, para evitar danificar as camas de desova» (tradução nossa). É uma recomendação, não uma proibição.',
				'Fonte: Disposición 241/2026 (22/9/2026), Boletim Oficial de Santa Cruz n.º 6164 (8/10/2026) e n.º 6166 (15/10/2026).',
			],
			'marca' => 'Motores (artigo 16)',
			'faq' => [
				[ 'Posso navegar com motor no rio Gallegos?', 'Não. O artigo 16 do Regulamento Geral proíbe a navegação com motor em todos os rios da província, salvo as exceções do Anexo I, e as fichas do rio Gallegos, do Gallegos Chico, do Chico Sur e do Penitente não trazem nenhuma (Disposición 241/2026, Boletim Oficial de Santa Cruz n.º 6164 e n.º 6166).' ],
			],
			'faq_cambios' => [
				[ 'O anexo que lemos não impõe mosca no leito principal do Gallegos', 'O Anexo I não impõe mosca no leito principal do Gallegos', 'FAQ só mosca: o Anexo I' ],
			],
			'res_viejo' => 'do Anexo I, só as fichas citadas acima.',
			'res_nuevo' => 'do Anexo I, as fichas do Gallegos, Gallegos Chico, Chico Sur e Penitente, lidas do texto do Boletim Oficial (n.º 6164 e n.º 6166).',
		],
		'cal' => [
			'rx_h2' => '/^A licença e quanto custa$/u',
			'h2' => 'O rio Santa Cruz em detalhe',
			'intro' => 'O que o Anexo I traz para o rio Santa Cruz, além do que está acima (Disposición 241/2026, Boletim Oficial de Santa Cruz n.º 6164 de 8/10/2026 e n.º 6166 de 15/10/2026):',
			'items' => [
				'<strong>Anzol:</strong> um simples ou duplo, com ou sem farpa, salvo exceções.',
				'<strong>Zona de pesca de margem autorizada:</strong> da descida à margem na altura do Monumento Héroes de Malvinas, perto da rua Bella Vista, até o cruzamento do Interconectado Provincial, nas duas margens (norte e sul); inclui a ilha Pavón, na sua margem, da ponte da rota nacional 3 até a zona conhecida localmente como «La Noria», que será sinalizada com placas.',
				'<strong>Salmão-chinook:</strong> temporada reduzida de 1º de agosto a 30 de novembro de 2027, com isca livre com um anzol simples ou duplo, com ou sem farpa; limite diário de um exemplar por pescador, e as demais espécies capturadas devem ser devolvidas. Zona de margem: da descida urbana da rua Bella Vista até o cruzamento do Interconectado Provincial.',
				'<strong>Robalo</strong> (<em>Eleginops maclovinus</em>): o ano todo, com spinning, mosca ou linha de fundo com até três anzóis e isca livre; sem chumbo. Até dez por pescador por dia, com tamanho mínimo de 30 cm de comprimento total; é a única espécie que pode ser abatida, e as demais são soltas. Zona: da descida à margem do Monumento Héroes de Malvinas, perto da rua Bella Vista, até a cerca perimetral da Estancia La Esperanza (margem norte).',
				'<strong>Trolling:</strong> só para guias de pesca registrados no R.P.A.T., com embarcações motorizadas homologadas que cumpram o artigo 16, em dois trechos: da ponte da rota nacional 3 até a captação de água de Puerto Santa Cruz (parte baixa) e da Chacra de Teira (Tapera Los Frutales) até o cruzamento do Interconectado (parte alta).',
				'<strong>Defeso total nas obras hidrelétricas:</strong> o trecho que vai da Estancia El Rincón (zona de «Los Laberintos») até «La Barrancosa», delimitado pelos acampamentos de obra de Cóndor Cliff (70° 07\' O) e o ponto fixado em 70° 10\' O.',
			],
			'faqs' => [
				[ 'Posso pescar robalo no rio Santa Cruz?', 'Sim, o ano todo, com spinning, mosca ou linha de fundo com até três anzóis e isca livre, sem chumbo. O limite é de dez por pescador por dia, com tamanho mínimo de 30 cm, e é a única espécie que pode ser abatida. A zona vai do Monumento Héroes de Malvinas, perto da rua Bella Vista, até a cerca da Estancia La Esperanza, na margem norte (Anexo I da Disposición 241/2026).' ],
				[ 'Quando se pesca o salmão-chinook no rio Santa Cruz?', 'De 1º de agosto a 30 de novembro de 2027, um exemplar por dia por pescador; as demais espécies capturadas são devolvidas. A zona de margem vai da descida urbana da rua Bella Vista até o cruzamento do Interconectado Provincial (Anexo I da Disposición 241/2026).' ],
			],
		],
	],
];

/** Generic patch of one guide: body pairs, optional new blocks, faq changes/additions, summary tail, sources. */
function pm_t_destino( string $clave, string $i, array $o, array $fuentes, string $hoy, bool $aplicar, int &$cambios ): void {
	$p = pm_buscar( 'pm_destino', $clave, $i );
	if ( ! $p ) {
		WP_CLI::warning( "$clave ($i): no existe, se salta." );
		return;
	}
	$c      = $p->post_content;
	$nuevo  = $c;
	$tareas = [];
	$nuevo  = pm_t_aplicar( $nuevo, $o['cuerpo'] ?? [], "$clave ($i) cuerpo", $tareas );
	if ( ! empty( $o['bloque_h2'] ) && false === strpos( $nuevo, $o['bloque_h2'] ) ) {
		$pos = pm_sc_pos_encabezado( $nuevo, $o['rx_h2'] );
		if ( null === $pos ) {
			WP_CLI::warning( "$clave ($i): no encontré el encabezado de anclaje; no agrego «{$o['bloque_h2']}»." );
		} else {
			$nuevo    = pm_sc_insertar( $nuevo, $pos, $o['bloque'] );
			$tareas[] = "bloque «{$o['bloque_h2']}»";
		}
	}
	if ( ! empty( $o['bloque_p'] ) && false === strpos( $nuevo, $o['bloque_marca'] ) ) {
		$pos = pm_sc_pos_tras_parrafo( $nuevo, $o['rx_pos'] );
		if ( null === $pos ) {
			WP_CLI::warning( "$clave ($i): no encontré el párrafo de anclaje; no agrego «{$o['bloque_marca']}»." );
		} else {
			$b = '';
			foreach ( $o['bloque_p'] as $par ) {
				$b .= pm_sc_p( $par );
			}
			$nuevo    = pm_sc_insertar( $nuevo, $pos, $b );
			$tareas[] = "párrafos «{$o['bloque_marca']}»";
		}
	}
	// FAQs.
	$filas  = pm_filas( 'faqs', $p->ID );
	$faqs_n = $filas;
	foreach ( $o['faq_cambios'] ?? [] as [ $viejo, $nv, $et ] ) {
		$hecho = false;
		foreach ( $faqs_n as $k => $f ) {
			$r = pm_t_cambiar( (string) ( $f['respuesta'] ?? '' ), $viejo, $nv );
			if ( null !== $r ) {
				$faqs_n[ $k ]['respuesta'] = $r;
				$hecho = true;
				$tareas[] = $et;
			} elseif ( false !== strpos( (string) ( $f['respuesta'] ?? '' ), $nv ) ) {
				$hecho = true;
			}
		}
		$hecho || WP_CLI::warning( "$clave ($i): no encontré la FAQ de «{$et}»." );
	}
	foreach ( $o['faq_reemplazos'] ?? [] as [ $q, $viejo, $nv, $et ] ) {
		$visto = false;
		foreach ( $faqs_n as $k => $f ) {
			if ( ( $f['pregunta'] ?? '' ) !== $q ) {
				continue;
			}
			$visto = true;
			$r = pm_t_cambiar( (string) $f['respuesta'], $viejo, $nv );
			if ( null !== $r ) {
				$faqs_n[ $k ]['respuesta'] = $r;
				$tareas[] = $et;
			} elseif ( false === strpos( (string) $f['respuesta'], $nv ) ) {
				WP_CLI::warning( "$clave ($i): la FAQ «{$q}» no tiene el texto esperado." );
			}
		}
		$visto || WP_CLI::warning( "$clave ($i): no existe la FAQ «{$q}»." );
	}
	$preg = array_column( $faqs_n, 'pregunta' );
	foreach ( $o['faqs'] ?? [] as [ $q, $a ] ) {
		if ( ! in_array( $q, $preg, true ) ) {
			$faqs_n[] = [ 'pregunta' => $q, 'respuesta' => $a ];
			$tareas[] = "FAQ nueva «{$q}»";
		}
	}
	// Summary field.
	$res   = (string) pm_campo( 'reglamento_resumen', $p->ID );
	$res_n = $res;
	if ( ! empty( $o['res_viejo'] ) && false === strpos( $res, $o['res_nuevo'] ) ) {
		$r = pm_t_cambiar( $res, $o['res_viejo'], $o['res_nuevo'] );
		if ( null === $r ) {
			WP_CLI::warning( "$clave ($i): no encontré la frase del resumen." );
		} else {
			$res_n    = $r;
			$tareas[] = 'reglamento_resumen';
		}
	}
	$fuentes_n = pm_t_fuentes( $p->ID, $fuentes, $hoy );
	$fuentes_n && $tareas[] = 'fuentes oficiales (Boletín Oficial)';
	if ( ! $tareas ) {
		WP_CLI::log( "$clave ($i) #{$p->ID}: ya está." );
		return;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) #{$p->ID}: " . implode( ' · ', $tareas ) );
	$cambios++;
	if ( ! $aplicar ) {
		return;
	}
	if ( $nuevo !== $c ) {
		pm_sc_guardar( $p->ID, $nuevo, [], "$clave ($i)" );
	}
	if ( $res_n !== $res ) {
		pm_sc_campo( $p->ID, 'reglamento_resumen', $res_n, "$clave ($i)" );
	}
	if ( $faqs_n !== $filas ) {
		update_field( 'faqs', $faqs_n, $p->ID );
		count( pm_filas( 'faqs', $p->ID ) ) === count( $faqs_n ) || WP_CLI::error( "$clave ($i): las FAQ no quedaron." );
	}
	if ( $fuentes_n ) {
		update_field( 'fuentes', $fuentes_n, $p->ID );
		count( pm_filas( 'fuentes', $p->ID ) ) === count( $fuentes_n ) || WP_CLI::error( "$clave ($i): las fuentes no quedaron." );
	}
	update_field( 'actualizado', $hoy, $p->ID );
	$leido = get_post( $p->ID )->post_content;
	$resl  = (string) pm_campo( 'reglamento_resumen', $p->ID );
	$pf    = array_column( pm_filas( 'faqs', $p->ID ), 'pregunta' );
	$rf    = implode( ' ', array_column( pm_filas( 'faqs', $p->ID ), 'respuesta' ) );
	$ok    = true;
	foreach ( $o['verificar'] ?? [] as $t ) {
		if ( false === strpos( $leido, $t ) && false === strpos( $resl, $t ) && false === strpos( $rf, $t ) && ! in_array( $t, $pf, true ) ) {
			$ok = false;
			WP_CLI::warning( "$clave ($i): la lectura de vuelta no contiene «{$t}»." );
		}
	}
	foreach ( $o['no_debe'] ?? [] as $t ) {
		if ( false !== strpos( $leido, $t ) ) {
			$ok = false;
			WP_CLI::warning( "$clave ($i): la lectura de vuelta todavía contiene «{$t}»." );
		}
	}
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}

foreach ( $X as $i => $x ) {
	$fu = [ [ $x['src_bo'], $u_bo1 ], [ $x['src_bo2'], $u_bo2 ], [ $x['src_pdf'], $u_pdf ] ];
	// 1. Jurassic Lake: the FAQ about the mouth of the Barrancoso and the summary.
	pm_t_destino( 'jurassic-lake', $i, [
		'faq_reemplazos' => [ [ $x['jur']['faq_q'], $x['jur']['viejo'], $x['jur']['nuevo'], 'FAQ de la boca del Barrancoso: el Anexo I no fija distancia propia' ] ],
		'res_viejo' => $x['jur']['res_viejo'], 'res_nuevo' => $x['jur']['res_nuevo'],
		'verificar' => [ $x['jur']['nuevo'], $x['jur']['res_nuevo'] ],
		'no_debe' => [ '100 m</strong> desde sus nacientes', 'within 100 m' ],
	], $fu, $hoy, $aplicar, $cambios );
	// 2. Río Gallegos.
	$g = $x['gal'];
	pm_t_destino( 'rio-gallegos', $i, [
		'cuerpo' => $g['cuerpo'], 'rx_pos' => $g['rx_pos'], 'bloque_p' => $g['bloque'], 'bloque_marca' => $g['marca'],
		'faqs' => $g['faq'], 'faq_cambios' => $g['faq_cambios'], 'res_viejo' => $g['res_viejo'], 'res_nuevo' => $g['res_nuevo'],
		'verificar' => [ $g['marca'], $g['faq'][0][0], $g['res_nuevo'] ],
		'no_debe' => [ 'Truchaike', 'Los Plateados' ],
	], $fu, $hoy, $aplicar, $cambios );
	// 3. El Calafate.
	$k      = $x['cal'];
	$bloque = pm_sc_h2( $k['h2'] ) . pm_sc_p( $k['intro'] ) . pm_sc_lista( $k['items'] );
	pm_t_destino( 'el-calafate', $i, [
		'bloque_h2' => $k['h2'], 'rx_h2' => $k['rx_h2'], 'bloque' => $bloque,
		'faqs' => $k['faqs'], 'verificar' => [ $k['h2'], $k['faqs'][0][0] ],
	], $fu, $hoy, $aplicar, $cambios );
}

// --- Sweep (read only): a "100 m" rule next to the Barrancoso anywhere on the site ------------------------------------
foreach ( [ 'pm_destino', 'pm_nota', 'pm_especie', 'pm_prestador', 'page' ] as $tipo ) {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'numberposts' => -1 ] ) as $q ) {
		$txt = wp_strip_all_tags( $q->post_content );
		if ( 'pm_destino' === $tipo ) {
			foreach ( (array) pm_filas( 'faqs', $q->ID ) as $f ) {
				$txt .= ' ' . ( $f['respuesta'] ?? '' );
			}
		}
		if ( preg_match( '/Barrancoso.{0,250}?\b100\s?(m\b|metros|meters|metres)|\b100\s?(m\b|metros|meters|metres).{0,250}?Barrancoso/su', $txt, $m ) ) {
			WP_CLI::warning( "BARRIDO: «100 m» junto a Barrancoso en $tipo #{$q->ID} «{$q->post_title}»: " . mb_substr( $m[0], 0, 200 ) );
		}
	}
}
WP_CLI::log( 'BARRIDO terminado (sin avisos arriba = ninguna página dice «100 m» junto al Barrancoso).' );
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
