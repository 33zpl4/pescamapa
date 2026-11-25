<?php
/**
 * Note "Cómo pedir presupuestos de pesca en la Argentina: cuatro caminos y cuál te conviene" (es only, clave como-pedir-presupuestos-pesca-argentina), 25/11/2026.
 * Why: friends of the owner asked three AI assistants how to organise a fishing trip to Argentina and get quotes; the answers were: write to each lodge, book a guide on a platform
 * (Hooked, Pesqa) or use an agency that packages flights. Pescamapa was cited only for prices. The owner approved (25/11/2026) a page that answers that question with the four routes side by side.
 * Goal he set (docs/ux-paginas.md): make things easier and quicker for whoever plans the trip. So the note says when another route is the better one.
 * What it says about Hooked and Pesqa comes only from the owner's reading of their pages and terms on 25/11/2026 (listed in $fuentes_def); about us, only what the quote page says.
 * Every claim about third parties and where it comes from: docs/borradores/cuatro-caminos-presupuestos-2026-11-25.md.
 * The comparison is four cards (a `regla` group each with a list), not a table: it reads on a 390 px phone with the CSS the theme already has.
 * Spanish only for now; en/pt after the owner reads it. Cover photo: reuses the attachment of the note precio-pesca-patagonia-sur (provisional, see docs/estado.md); no new image.
 * Idempotent: a note with the same clave + language, or the same slug, is never touched. Dry run first. Run with wp-script.yml (script = nota-cuatro-caminos-2026-11-25).
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
if ( ! function_exists( 'pm_cc_tarjeta' ) ) {
	/** One route as a card: the theme's `regla` box (cream) with a heading and a list of "Label: text" lines. $titulo may carry links. */
	function pm_cc_tarjeta( string $titulo, array $filas ): string {
		$li = '';
		foreach ( $filas as [ $rotulo, $texto ] ) {
			$li .= "<!-- wp:list-item --><li><strong>{$rotulo}:</strong> {$texto}</li><!-- /wp:list-item -->";
		}
		return "<!-- wp:group {\"className\":\"regla camino\"} -->\n<div class=\"wp-block-group regla camino\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list --></div>\n<!-- /wp:group -->\n\n";
	}
	function pm_cc_lista_ord( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list {\"ordered\":true} -->\n<ol class=\"wp-block-list\">{$li}</ol>\n<!-- /wp:list -->\n\n";
	}
}


$aplicar = false;
$hoy     = '20261125';
$clave   = 'como-pedir-presupuestos-pesca-argentina';
$foto_de = 'precio-pesca-patagonia-sur'; // note whose cover photo is reused (provisional)
$i       = 'es';

$N = [
	'titulo' => 'Cómo pedir presupuestos de pesca en la Argentina: cuatro caminos y cuál te conviene',
	'slug' => 'como-pedir-presupuestos-de-pesca-en-argentina',
	'seo_title' => 'Cómo pedir presupuestos de pesca en Argentina: 4 caminos',
	'seo_description' => 'Escribir al lodge, reservar un guía en una plataforma (Hooked, Pesqa), una agencia o pedir opciones a Pescamapa: cuál sirve a cada viaje y qué pedir.',
	'bajada' => 'Cuatro formas de conseguir precios para un viaje de pesca a la Argentina, lado a lado, y cuál te conviene según tu viaje.',
	'datos' => [
		'Escribirle directo al lodge o al guía: el mejor si ya sabés a qué lugar querés ir.',
		'Reservar un guía en una plataforma (Hooked, Pesqa): el más rápido para un día con guía, reservado en minutos.',
		'Una agencia que arma el paquete: el que resuelve todo junto, con vuelos y traslados, desde tu país.',
		'Pescamapa: un solo pedido para comparar varios lugares en un viaje de varios días, sobre todo si viajás solo, con alguien que no pesca o con fechas que podés mover. Es gratis.',
	],
	'h_lado' => 'Los cuatro caminos, lado a lado',
	'tarjetas' => [
		[ 'Escribirle directo a cada lodge o guía', [
			[ 'Sirve para', 'un lugar que ya elegiste.' ],
			[ 'Qué ves antes de pagar', 'lo que cada uno te responda, cada uno a su manera.' ],
			[ 'Tu tiempo', 'un mensaje y una espera por cada lugar.' ],
			[ 'Quién ve tus datos', 'cada lugar al que escribís.' ],
			[ 'Costo para vos', 'el precio del lugar.' ],
		] ],
		[ 'Reservar un guía en una plataforma: <a href="https://hooked.com.ar/" rel="noopener">Hooked</a> y <a href="https://pesqa.ar/" rel="noopener">Pesqa</a>', [
			[ 'Sirve para', 'un día o medio día con guía, reservado ya.' ],
			[ 'Qué ves antes de pagar', 'el precio publicado, qué incluye y las reseñas.' ],
			[ 'Tu tiempo', 'minutos: elegís guía y fecha, y reservás o consultás.' ],
			[ 'Quién ve tus datos', 'el guía que elegís y la plataforma.' ],
			[ 'Costo para vos', 'el precio publicado del guía; pagás online la salida o una seña.' ],
		] ],
		[ 'Una agencia que arma el paquete', [
			[ 'Sirve para', 'tener lodge, traslados y, según la agencia, vuelos en un solo trato.' ],
			[ 'Qué ves antes de pagar', 'la propuesta del paquete, que se pide por consulta.' ],
			[ 'Tu tiempo', 'una consulta; la agencia gestiona el resto.' ],
			[ 'Quién ve tus datos', 'la agencia.' ],
			[ 'Costo para vos', 'el precio del paquete; preguntá el mínimo de personas.' ],
		] ],
		[ '<a href="{p:presupuesto}">Pescamapa: un pedido, opciones comparadas</a>', [
			[ 'Sirve para', 'comparar varios lugares para un viaje de varios días.' ],
			[ 'Qué ves antes de pagar', 'las opciones lado a lado: total para tu grupo, qué incluye y fechas con lugar.' ],
			[ 'Tu tiempo', 'un solo pedido; las opciones llegan en 24 a 48 horas.' ],
			[ 'Quién ve tus datos', 'nosotros; los lugares reciben tu pedido sin tu nombre ni tu contacto.' ],
			[ 'Costo para vos', 'gratis. Reservás directo con el lugar que elijas, al mismo precio.' ],
		] ],
	],
	'lado_pie' => '<em>Lo de Hooked y Pesqa sale de sus páginas, leídas el 25/11/2026. Los sitios cambian: confirmalo en cada uno antes de reservar.</em>',
	'h_cual' => 'Cuál te conviene según tu viaje',
	'casos' => [
		'<strong>Un día con guía la semana que viene.</strong> Una plataforma (Hooked o Pesqa): elegís la salida y la fecha, reservás en minutos y ves el precio, qué incluye y las reseñas antes de pagar. Es el camino más rápido.',
		'<strong>Una semana en un lodge para cuatro.</strong> Pescamapa: un solo pedido con las fechas y los cuatro, y recibís el total del grupo de cada lugar lado a lado, sin escribirle a uno por uno. <a href="{viaje}">Pedir opciones</a>, gratis. Cómo es una semana por dentro: <a href="{n:semana-de-pesca-en-lodge-argentina}">cómo funciona una semana de pesca en un lodge</a>.',
		'<strong>Voy solo o con alguien que no pesca.</strong> Pescamapa: en el pedido indicás cuántos pescan y cuántos te acompañan, y el total que te mandamos cuenta a todos. <a href="{viaje}">Armar el viaje</a>.',
		'<strong>Quiero todo resuelto, con vuelos desde mi país.</strong> Una agencia: un solo trato para el paquete completo. Si salís de España, mirá antes <a href="{n:viaje-pesca-argentina-desde-espana}">el viaje de pesca a la Argentina desde España</a>.',
		'<strong>Ya sé a qué lodge quiero ir.</strong> Escribile directo, que es el camino más corto. Con la lista de abajo, tu pedido sale completo en un solo mensaje.',
	],
	'h_pedir' => 'Qué pedirle a cada uno para poder comparar',
	'pedir_p' => '<strong>Pedí lo mismo a todos y las respuestas se pueden poner lado a lado.</strong> Por escrito, para cada opción:',
	'pedir_l' => [
		'El precio por persona y el mínimo de personas.',
		'Cuántas noches y cuántos días de pesca incluye.',
		'La habitación: doble, individual o compartida, y el recargo por individual.',
		'El guía: compartido o privado, y cuántos pescadores por guía.',
		'Lancha, combustible y equipo: qué incluye y qué se alquila.',
		'Los traslados: desde dónde y hasta dónde, y si están incluidos.',
		'La licencia de pesca: incluida o aparte (la calculás con la <a href="{p:licencia}">calculadora de licencia</a>).',
		'La cancelación: qué pasa si cambiás la fecha o si el clima no deja salir.',
		'Qué es extra: comidas, bebidas, acompañantes y excursiones.',
		'Las fechas con lugar para tu grupo.',
	],
	'pedir_p2' => 'Pedido por Pescamapa, les hacemos las mismas preguntas a todos los lugares. Para tener una referencia antes de pedir: <a href="{n:cuanto-cuesta-viaje-pesca-argentina}">cuánto cuesta un viaje de pesca a la Argentina</a> y el <a href="{p:estimador}">estimador de costo</a>; para elegir el mes, el <a href="{p:calendario}">calendario de pesca</a>; para ordenar todos los pasos, <a href="{n:como-preparar-viaje-pesca-argentina}">cómo preparar un viaje de pesca a la Argentina</a>.',
	'h_como' => 'Cómo funciona el camino de Pescamapa',
	'como_l' => [
		'<strong>Contanos tu viaje:</strong> dónde, cuándo y cuántos son. Un solo pedido.',
		'<strong>Les preguntamos a los lugares</strong> que encajan con tu pedido.',
		'<strong>Te mandamos las opciones comparadas en 24 a 48 horas.</strong> Elegís y reservás directo, al mismo precio.',
	],
	'como_p' => '<strong>Gratis para vos.</strong> <a href="{viaje}">Armar el viaje en el formulario</a> o ver <a href="{p:presupuesto}">cómo es el pedido de presupuesto</a>.',
	'faq_h' => 'Preguntas frecuentes',
	'cierre' => '<em>Revisado el 25/11/2026. Lo que decimos de Hooked y de Pesqa sale de sus páginas y términos, leídos el 25/11/2026; precios y condiciones cambian: confirmalos con cada uno antes de reservar.</em>',
	'faq' => [
		[ '¿Conviene escribirle directo al lodge o usar una plataforma?', 'Depende de tu viaje. Si ya sabés a qué lodge querés ir, escribile directo. Si querés un día con guía, una plataforma es lo más rápido: elegís y reservás. Si querés comparar varios lugares para un viaje de varios días, pedí opciones a Pescamapa.' ],
		[ '¿Pescamapa cobra algo?', 'Es gratis para vos. Reservás y pagás directo con el lugar que elijas. Algunos lugares y guías nos pagan una comisión por los pescadores que llegan por Pescamapa.' ],
		[ '¿Quién ve mis datos?', 'Los lugares reciben tu pedido sin tu nombre ni tu contacto. Tus datos quedan con nosotros hasta que elegís un lugar; recién ahí te conectamos con él. En Hooked y en Pesqa, tus datos van al guía o al lugar que elegís y a la plataforma; en una agencia, mirá su política de privacidad.' ],
		[ '¿Puedo reservar en Hooked o Pesqa y también pedirles opciones a ustedes?', 'Sí, sirven para momentos distintos: en una plataforma elegís un guía y reservás; con nosotros mandás un pedido y te responden los lugares y guías que encajan, para que compares. Podés reservar un día con guía en una plataforma y, para el resto del viaje, pedirnos opciones.' ],
		[ '¿Cuánto tardan en responder?', 'Te mandamos las opciones en 24 a 48 horas. Con los otros caminos depende de cada lugar, guía o agencia; en una plataforma son minutos si reservás, y lo que tarde el guía si consultás.' ],
		[ '¿Sirve si viajo desde el exterior?', 'Sí. Atendemos en español, portugués e inglés: en el pedido indicás desde dónde viajás y si llevás tus cañas. Si salís de España, mirá <a href="{n:viaje-pesca-argentina-desde-espana}">el viaje de pesca a la Argentina desde España</a>.' ],
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
// Sources: our own pages carry their own dates; Hooked and Pesqa pages were checked by the owner on 25/11/2026 (commercial sites, not official sources).
$fuentes_def = [
	[ 'Pescamapa: pedir presupuesto a varios lodges de pesca a la vez (cómo funciona, quién ve tus datos, cuánto cuesta)', $fn_pagina( 'presupuesto' ), 0, '20261125' ],
	[ 'Hooked: guías de pesca en Argentina (cómo reservar, precios publicados, qué incluye; leído el 25/11/2026)', 'https://hooked.com.ar/', 0, '20261125' ],
	[ 'Hooked: términos y condiciones (comisión al guía, datos, cancelación; leído el 25/11/2026)', 'https://hooked.com.ar/legal/terms', 0, '20261125' ],
	[ 'Pesqa: plataforma para planificar la salida de pesca (reserva con seña, precios, reseñas; leído el 25/11/2026)', 'https://pesqa.ar/', 0, '20261125' ],
	[ 'Pesqa: términos y condiciones (comisión al proveedor, datos; leído el 25/11/2026)', 'https://pesqa.ar/terminos', 0, '20261125' ],
	[ 'Pesqa: política de cancelaciones (flexible, moderada, estricta; leído el 25/11/2026)', 'https://pesqa.ar/cancelaciones', 0, '20261125' ],
	[ 'Pescamapa: cuánto cuesta un viaje de pesca a la Argentina 2026/27 (precios por tipo de viaje)', $fn_nota( 'cuanto-cuesta-viaje-pesca-argentina' ), 0, '20261116' ],
	[ 'Pescamapa: cómo preparar un viaje de pesca a la Argentina (los pasos en orden)', $fn_nota( 'como-preparar-viaje-pesca-argentina' ), 0, '20261116' ],
];

$nota_tipo = 'como';
$origen    = 'Cómo pedir presupuestos de pesca en la Argentina, 25/11/2026 (pedido del dueño: amigos preguntaron a ChatGPT, Gemini y Claude cómo organizar un viaje de pesca y conseguir presupuestos; las respuestas mandaban a escribir a cada lodge, a reservar un guía en una plataforma (Hooked, Pesqa) o a una agencia con vuelos, y citaban a Pescamapa solo por precios). Lo que dice de Hooked y de Pesqa sale de la lectura de sus páginas y términos que hizo el dueño el 25/11/2026 (hooked.com.ar, hooked.com.ar/legal/terms, pesqa.ar, pesqa.ar/terminos, pesqa.ar/cancelaciones); de nosotros, solo lo que dice la página de pedir presupuesto; de las agencias, lo genérico de docs/prestadores-brasil-2026-11-15.md. Tabla de cada afirmación sobre terceros y qué hay que reverificar: docs/borradores/cuatro-caminos-presupuestos-2026-11-25.md. Portada: la de precio-pesca-patagonia-sur (provisoria).';

$armar = function ( array $n ): string {
	// The four 'datos' (stored as nota_datos) are the short answer: the note template shows them first in the «Datos clave» box, followed by its own «Pedir opciones» button.
	// The cases come first (the answer for the reader's own trip), then the four routes side by side.
	$c = pm_sc_h2( $n['h_cual'] ) . implode( '', array_map( 'pm_sc_p', $n['casos'] ) ) . pm_sc_h2( $n['h_lado'] );
	foreach ( $n['tarjetas'] as [ $titulo, $filas ] ) {
		$c .= pm_cc_tarjeta( $titulo, $filas );
	}
	return $c . pm_sc_p( $n['lado_pie'] )
		. pm_sc_h2( $n['h_pedir'] ) . pm_sc_p( $n['pedir_p'] ) . pm_cc_lista_ord( $n['pedir_l'] ) . pm_sc_p( $n['pedir_p2'] )
		. pm_sc_h2( $n['h_como'] ) . pm_cc_lista_ord( $n['como_l'] ) . pm_sc_p( $n['como_p'] )
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
	if ( preg_match( '/PENDIENTE|no pudimos|operador|prestador/iu', wp_strip_all_tags( $cuerpo ), $mm ) ) {
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
