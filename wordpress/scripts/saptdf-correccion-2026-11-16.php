<?php
/**
 * SAPTDF correction (es/en/pt), 16/11/2026: what our pages say about the Tierra del Fuego access system (SAPTDF) for foreigners on the Grande River, set against the official sources
 * read on 16/11/2026. See the comment below for what the sources say. Run with wp-script.yml (script = saptdf-correccion-2026-11-16). Dry run first ($aplicar = false).
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

if ( ! function_exists( 'pm_pp_olista' ) ) {
	/** Ordered list block. */
	function pm_pp_olista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">{$li}</ol>\n<!-- /wp:list -->\n\n";
	}
}

$aplicar = false;

/**
 * Correction (16/11/2026). Official sources read on 16/11/2026 (investigar.yml run saptdf-1): the SAPTDF page (saptdf.ar) is titled "Sistema de acceso de pesca para residentes de Tierra del
 * Fuego" (registration by email with the DNI and a valid permit; reservations up to 48 hours ahead); the ministry page (prodyambiente) lists it as the "beneficio de ingreso gratuito" to
 * cotos; Resolución 259/26 says that anglers registered in SAPTDF with a prior booking "podrán acceder" to El Tropezón and other open accesses on the Grande River and requires a SAPTDF
 * booking for the Claro River. Nothing read says that a foreign visitor needs SAPTDF to fish the Grande River. The licence hub said "para entrar al río Grande hace falta estar registrado y
 * tener una reserva previa en saptdf.ar": that is not backed. Fixed there (es/en/pt); the Río Grande guide gets one added sentence and its "aguas" note for the Grande River is worded as the
 * resolution words it. Each replacement needs exactly one match of the old text (live text can have been edited): otherwise it is logged and skipped, never forced.
 */
$post = [
	[ 'pm_nota', 'licencias-extranjeros-argentina', [
		'es' => [ 'Para entrar al río Grande y sus afluentes hace falta estar registrado y tener una reserva previa en el sistema de accesos saptdf.ar; el río está habilitado', 'La Resolución 259/26 dice que los pescadores registrados en el Sistema de Accesos de Pesca de Tierra del Fuego (saptdf.ar) y con reserva previa pueden entrar al sector «El Tropezón» y a otros accesos habilitados del río Grande, y pide esa reserva para el río Claro. La página del propio sistema, leída el 16/11/2026, lo presenta como el sistema de acceso de pesca para residentes de Tierra del Fuego, con alta por DNI, y no dice qué corresponde a un visitante extranjero: preguntalo a tu guía o a la Dirección General de Recursos Hídricos. El río está habilitado' ],
		'en' => [ 'To access the Grande River and its tributaries, you must be registered and have a prior reservation in the saptdf.ar access system; the river is open from', 'Resolución 259/26 says that anglers registered in the Tierra del Fuego Fishing Access System (saptdf.ar) with a prior booking can reach the “El Tropezón” sector and other open accesses on the Grande River, and it requires that booking for the Claro River. The system’s own page, read on 16/11/2026, presents it as the fishing access system for residents of Tierra del Fuego, with registration by national ID, and does not say what applies to a foreign visitor: ask your guide or the Dirección General de Recursos Hídricos. The river is open from' ],
		'pt' => [ 'Para acessar o rio Grande e seus afluentes é necessário estar registrado e ter uma reserva prévia no sistema de acessos saptdf.ar; o rio está aberto de', 'A Resolución 259/26 diz que os pescadores registrados no Sistema de Acessos de Pesca da Terra do Fogo (saptdf.ar) e com reserva prévia podem entrar no setor «El Tropezón» e em outros acessos habilitados do rio Grande, e pede essa reserva para o rio Claro. A página do próprio sistema, lida em 16/11/2026, o apresenta como o sistema de acesso de pesca para residentes da Terra do Fogo, com cadastro por DNI, e não diz o que vale para um visitante estrangeiro: pergunte ao seu guia ou à Dirección General de Recursos Hídricos. O rio está aberto de' ],
	] ],
	[ 'pm_destino', 'rio-grande', [
		'es' => [ 'Quien reserva y no avisa con 24 horas de anticipación puede ser suspendido del sistema.', 'Quien reserva y no avisa con 24 horas de anticipación puede ser suspendido del sistema. La página del sistema (saptdf.ar, leída el 16/11/2026) lo presenta como el sistema de acceso de pesca para residentes de Tierra del Fuego, con alta por DNI, y no dice qué corresponde a un visitante extranjero: preguntalo a quien te guíe.' ],
		'en' => [ 'Booking and not cancelling 24 hours ahead can get you suspended from the system.', 'Booking and not cancelling 24 hours ahead can get you suspended from the system. The system’s page (saptdf.ar, read on 16/11/2026) presents it as the fishing access system for residents of Tierra del Fuego, with registration by national ID, and does not say what applies to a foreign visitor: ask whoever guides you.' ],
		'pt' => [ 'Quem reserva e não avisa com 24 horas de antecedência pode ser suspenso do sistema.', 'Quem reserva e não avisa com 24 horas de antecedência pode ser suspenso do sistema. A página do sistema (saptdf.ar, lida em 16/11/2026) o apresenta como o sistema de acesso de pesca para residentes da Terra do Fogo, com cadastro por DNI, e não diz o que vale para um visitante estrangeiro: pergunte a quem for guiar você.' ],
	] ],
];
$aguas = [
	'es' => [ 'Devolución obligatoria de todos los salmónidos; acceso con reserva en el SAPTDF; hasta el 18 de abril de 2027.', 'Devolución obligatoria de todos los salmónidos; hasta el 18 de abril de 2027. Acceso a El Tropezón y a otros accesos habilitados con registro y reserva en el SAPTDF, sistema para residentes.' ],
	'en' => [ 'Mandatory release of all salmonids; access by booking on SAPTDF; until 18 April 2027.', 'Mandatory release of all salmonids; until 18 April 2027. El Tropezón and other open accesses by registration and booking on SAPTDF, a system for residents.' ],
	'pt' => [ 'Devolução obrigatória de todos os salmonídeos; acesso com reserva no SAPTDF; até 18 de abril de 2027.', 'Devolução obrigatória de todos os salmonídeos; até 18 de abril de 2027. Acesso a El Tropezón e a outros acessos habilitados com cadastro e reserva no SAPTDF, sistema para residentes.' ],
];

$cambios = 0;
$omitidos = 0;
foreach ( $post as [ $tipo, $c, $por_idioma ] ) {
	foreach ( $por_idioma as $i => [ $viejo, $nuevo ] ) {
		$p = pm_buscar( $tipo, $c, $i );
		if ( ! $p ) {
			WP_CLI::log( "$tipo $c ($i): no existe, nada que corregir." );
			$omitidos++;
			continue;
		}
		$cont = $p->post_content;
		if ( false !== strpos( $cont, $nuevo ) ) {
			WP_CLI::log( "$tipo $c ($i) #{$p->ID}: ya corregido." );
			continue;
		}
		$n = substr_count( $cont, $viejo );
		if ( 1 !== $n ) {
			WP_CLI::warning( "$tipo $c ($i) #{$p->ID}: el texto viejo aparece $n veces (esperada 1): no lo toco." );
			$omitidos++;
			continue;
		}
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": $tipo $c ($i) #{$p->ID}: reemplazo de 1 frase" );
		$cambios++;
		if ( $aplicar ) {
			pm_sc_guardar( $p->ID, str_replace( $viejo, $nuevo, $cont ), [ $nuevo ], "$tipo $c ($i)" );
			false === strpos( get_post( $p->ID )->post_content, $viejo ) || WP_CLI::error( "$tipo $c ($i): el texto viejo sigue en la página." );
			WP_CLI::log( "VERIFIED: $tipo $c ($i) #{$p->ID} corregido y sin el texto viejo" );
		}
	}
}
foreach ( $aguas as $i => [ $viejo, $nuevo ] ) {
	$p = pm_buscar( 'pm_destino', 'rio-grande', $i );
	if ( ! $p ) {
		continue;
	}
	$filas = pm_filas( 'aguas', $p->ID );
	$hubo  = false;
	foreach ( $filas as &$f ) {
		if ( ( $f['nota'] ?? '' ) === $viejo ) {
			$f['nota'] = $nuevo;
			$hubo      = true;
		} elseif ( ( $f['nota'] ?? '' ) === $nuevo ) {
			WP_CLI::log( "pm_destino rio-grande ($i) #{$p->ID}: la nota del río Grande ya está corregida." );
		}
	}
	unset( $f );
	if ( ! $hubo ) {
		WP_CLI::log( "pm_destino rio-grande ($i) #{$p->ID}: la nota del río Grande no tiene el texto viejo: no la toco." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": pm_destino rio-grande ($i) #{$p->ID}: nota de aguas del río Grande" );
	$cambios++;
	if ( $aplicar ) {
		update_field( 'aguas', $filas, $p->ID );
		$leidas = array_column( pm_filas( 'aguas', $p->ID ), 'nota' );
		in_array( $nuevo, $leidas, true ) && ! in_array( $viejo, $leidas, true ) || WP_CLI::error( "rio-grande ($i): la lectura de vuelta de 'aguas' no coincide." );
		WP_CLI::log( "VERIFIED: pm_destino rio-grande ($i) #{$p->ID} aguas corregidas" );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios, $omitidos omitidos." );
