<?php
/**
 * Step 5 of docs/plan-dorado-surubi-2026-11-16.md (16/11/2026): internal links to the dorado pillar, the cost notes, the permit note and the surubí festival note.
 * One closing paragraph "To plan your trip: …" (es/en/pt, only the links the page does not have yet, never a link to another language) on: the seven Litoral guides (Goya, Esquina, Paso de la Patria,
 * La Paz, Itatí, Itá Ibaté, Esteros del Iberá), the dorado species page, and the notes on costs, season, sizes and permit. Also fixes two cross-language links found in the inventory (step 0):
 * the Portuguese notes that linked the Spanish Esquina guide, and the Portuguese surubí species page linked by its old slug. The home shortcut to the pillar is theme code (inicio.php), not this script.
 * Run AFTER pilar-dorado-2026-11-16 and surubi-fiesta-2026-11-16. Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = enlaces-dorado-surubi-2026-11-16).
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



$aplicar = false;
$hoy     = '20261116';

// Which pages get which links. Each entry: [ post type, clave, [ keys of the pages to link ] ]. A link already present in the page is skipped; a page with nothing to add is not touched.
$k_pilar = 'golden-dorado-guia';
$k_costo = 'precio-viaje-dorado-lodge';
$k_total = 'cuanto-cuesta-viaje-pesca-argentina';
$k_perm  = 'permiso-pesca-argentina-paso-a-paso';
$k_amig  = 'viaje-dorado-amigos-precios';
$k_surub = 'cuando-pescar-surubi';
$k_fiest = 'fiesta-nacional-del-surubi-2027';
$k_hub   = 'como-preparar-viaje-pesca-argentina';
$k_semana = 'semana-de-pesca-en-lodge-argentina';
$plan_enlaces = [
	[ 'pm_destino', 'goya',              [ $k_pilar, $k_costo, $k_total, $k_perm, $k_surub, $k_fiest ] ],
	[ 'pm_destino', 'esquina',           [ $k_pilar, $k_costo, $k_total, $k_perm, $k_surub ] ],
	[ 'pm_destino', 'paso-de-la-patria', [ $k_pilar, $k_costo, $k_total ] ],
	[ 'pm_destino', 'la-paz',            [ $k_pilar, $k_costo, $k_total ] ],
	[ 'pm_destino', 'itati',             [ $k_pilar, $k_total, $k_perm ] ],
	[ 'pm_destino', 'ita-ibate',         [ $k_pilar, $k_total, $k_perm ] ],
	[ 'pm_destino', 'esteros-del-ibera', [ $k_pilar, $k_total, $k_perm ] ],
	[ 'pm_especie', 'dorado',            [ $k_pilar, $k_costo, $k_perm, $k_semana ] ],
	[ 'pm_nota',    $k_costo,            [ $k_pilar ] ],
	[ 'pm_nota',    $k_amig,             [ $k_pilar ] ],
	[ 'pm_nota',    'cuando-pescar-dorado', [ $k_pilar, $k_costo ] ],
	[ 'pm_nota',    $k_total,            [ $k_pilar ] ],
	[ 'pm_nota',    'tallas-cupos-vedas', [ $k_pilar ] ],
	[ 'pm_nota',    $k_surub,            [ $k_fiest ] ],
	[ 'pm_nota',    $k_perm,             [ $k_pilar ] ],
];
$frase = [
	'es' => [ 'intro' => '<strong>Para planificar:</strong> ', 'y' => ' y ', 'pie' => '.',
		$k_pilar => 'la guía de pesca del dorado en la Argentina (dónde, cuándo, reglas y cómo planificar)', $k_costo => 'cuánto cuesta un viaje de pesca de dorado', $k_total => 'cuánto cuesta un viaje de pesca a la Argentina',
		$k_perm => 'el permiso de pesca paso a paso', $k_amig => 'el viaje de dorado con amigos', $k_surub => 'cuándo pescar el surubí', $k_fiest => 'la Fiesta Nacional del Surubí 2027 (fechas y cupo)', $k_semana => 'cómo funciona una semana de pesca en un lodge', $k_hub => 'cómo preparar el viaje' ],
	'en' => [ 'intro' => '<strong>To plan your trip:</strong> ', 'y' => ' and ', 'pie' => '.',
		$k_pilar => 'the golden dorado fishing guide for Argentina (where, when, rules and how to plan)', $k_costo => 'how much a dorado fishing trip costs', $k_total => 'how much an Argentina fishing trip costs',
		$k_perm => 'the fishing permit step by step', $k_amig => 'the dorado trip with friends', $k_surub => 'when to fish surubí', $k_fiest => 'the National Surubí Festival 2027 (dates and entry cap)', $k_semana => 'how a fishing week at a lodge works', $k_hub => 'how to plan the trip' ],
	'pt' => [ 'intro' => '<strong>Para planejar:</strong> ', 'y' => ' e ', 'pie' => '.',
		$k_pilar => 'o guia de pesca de dourado na Argentina (onde, quando, regras e como planejar)', $k_costo => 'quanto custa uma viagem de pesca de dourado', $k_total => 'quanto custa pescar na Argentina',
		$k_perm => 'a licença de pesca passo a passo', $k_amig => 'a viagem de dourado com amigos', $k_surub => 'quando pescar o surubim', $k_fiest => 'a Festa Nacional do Surubim 2027 (datas e vagas)', $k_semana => 'como funciona uma semana de pesca em uma pousada', $k_hub => 'como planejar a viagem' ],
];

$cambios = 0;
foreach ( $plan_enlaces as [ $cpt, $clave, $claves ] ) {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$p = pm_buscar( $cpt, $clave, $i );
		if ( ! $p ) {
			WP_CLI::warning( "$cpt $clave ($i): no existe; se omite." );
			continue;
		}
		$c = $p->post_content;
		$items = [];
		foreach ( $claves as $kc ) {
			$dest = pm_buscar( 'pm_nota', $kc, $i );
			if ( ! $dest ) {
				continue; // never link another language: a missing translation is skipped.
			}
			$url = get_permalink( $dest->ID );
			if ( false !== strpos( $c, $url ) ) {
				continue;
			}
			$items[] = pm_sc_a( $url, $frase[ $i ][ $kc ] );
		}
		if ( ! $items ) {
			WP_CLI::log( "$cpt $clave ($i) #{$p->ID}: ya enlaza todo lo previsto; no toco nada." );
			continue;
		}
		$lista = count( $items ) > 1 ? implode( ', ', array_slice( $items, 0, -1 ) ) . $frase[ $i ]['y'] . end( $items ) : $items[0];
		$bloque = pm_sc_p( $frase[ $i ]['intro'] . $lista . $frase[ $i ]['pie'] );
		$nuevo  = rtrim( $c ) . "\n\n" . rtrim( $bloque ) . "\n";
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": $cpt $clave ($i) #{$p->ID}: párrafo al final con " . count( $items ) . ' enlaces' );
		$cambios++;
		if ( ! $aplicar ) {
			continue;
		}
		$slug = $p->post_name;
		$r = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
		is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
		$g  = get_post( $p->ID );
		$ok = $g->post_name === $slug;
		foreach ( $items as $it ) {
			preg_match( '/href="([^"]+)"/', $it, $m );
			$ok = $ok && false !== strpos( $g->post_content, $m[1] );
		}
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $cpt $clave ($i) #{$p->ID} " . get_permalink( $p->ID ) );
		$ok || WP_CLI::error( "$cpt $clave ($i): la lectura de vuelta no coincide." );
	}
}

// ---- Language fixes: the Portuguese notes linked the Spanish Esquina guide, and a species page by an old slug. ----
$esq_es = pm_buscar( 'pm_destino', 'esquina', 'es' );
$esq_pt = pm_buscar( 'pm_destino', 'esquina', 'pt' );
$sur_pt = pm_buscar( 'pm_especie', 'surubi', 'pt' );
if ( $esq_es && $esq_pt ) {
	$de = get_permalink( $esq_es->ID );
	$a  = get_permalink( $esq_pt->ID );
	foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true ] ) as $n ) {
		if ( 'pt' !== pm_idioma_de( $n->ID ) || false === strpos( $n->post_content, $de ) ) {
			continue;
		}
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": nota pt «" . pm_clave_de( $n->ID ) . "» #{$n->ID}: la guía de Esquina en español pasa a la de portugués" );
		$cambios++;
		if ( $aplicar ) {
			wp_update_post( [ 'ID' => $n->ID, 'post_content' => wp_slash( str_replace( $de, $a, $n->post_content ) ) ] );
			$ok = false === strpos( get_post( $n->ID )->post_content, $de ) && false !== strpos( get_post( $n->ID )->post_content, $a );
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota pt «" . pm_clave_de( $n->ID ) . "» #{$n->ID} enlaza la guía de Esquina en portugués" );
			$ok || WP_CLI::error( 'la corrección del enlace de Esquina no se leyó de vuelta.' );
		}
	}
}
if ( $sur_pt ) {
	$viejo = home_url( '/pt/especies/surubi/' );
	$nuevo = get_permalink( $sur_pt->ID );
	if ( $viejo !== $nuevo ) {
		foreach ( get_posts( [ 'post_type' => [ 'pm_nota', 'pm_destino' ], 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true ] ) as $n ) {
			if ( 'pt' !== pm_idioma_de( $n->ID ) || false === strpos( $n->post_content, $viejo ) ) {
				continue;
			}
			WP_CLI::log( pm_sc_modo( $aplicar ) . ": {$n->post_type} pt «" . pm_clave_de( $n->ID ) . "» #{$n->ID}: la ficha del surubim por su dirección actual" );
			$cambios++;
			if ( $aplicar ) {
				wp_update_post( [ 'ID' => $n->ID, 'post_content' => wp_slash( str_replace( $viejo, $nuevo, $n->post_content ) ) ] );
				$ok = false === strpos( get_post( $n->ID )->post_content, $viejo ) && false !== strpos( get_post( $n->ID )->post_content, $nuevo );
				WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": {$n->post_type} pt «" . pm_clave_de( $n->ID ) . "» #{$n->ID} enlaza " . $nuevo );
				$ok || WP_CLI::error( 'la corrección del enlace de la ficha del surubim no se leyó de vuelta.' );
			}
		}
	}
}
// ---- The comparison table of the pillar (step 2) was published without the class that stacks it on phones: add it. ----
foreach ( [ 'es' => 'Si buscás…', 'en' => 'If you want…', 'pt' => 'Se você procura…' ] as $i => $th ) {
	$p = pm_buscar( 'pm_nota', 'golden-dorado-guia', $i );
	if ( ! $p ) {
		continue;
	}
	$viejo = '<!-- wp:table -->' . "\n" . '<figure class="wp-block-table"><table><thead><tr><th>' . $th . '</th>';
	$nuevo = '<!-- wp:table {"className":"pm-estilo"} -->' . "\n" . '<figure class="wp-block-table pm-estilo"><table><thead><tr><th>' . $th . '</th>';
	if ( false === strpos( $p->post_content, $viejo ) ) {
		WP_CLI::log( "pilar ($i) #{$p->ID}: la tabla de estilo ya tiene la clase (o no está); no toco nada." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": pilar ($i) #{$p->ID}: la tabla de estilo recibe la clase pm-estilo (tarjetas en el móvil)" );
	$cambios++;
	if ( $aplicar ) {
		wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( str_replace( $viejo, $nuevo, $p->post_content ) ) ] );
		$ok = false !== strpos( get_post( $p->ID )->post_content, 'wp-block-table pm-estilo' );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": pilar ($i) #{$p->ID} con la clase pm-estilo" );
		$ok || WP_CLI::error( 'la clase de la tabla no se leyó de vuelta.' );
	}
}
$aplicar && flush_rewrite_rules( false );
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
