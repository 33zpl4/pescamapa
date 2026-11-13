<?php
/**
 * Search Console follow-up, 13/11/2026, fixes 6 and 7.
 *  6. PT: <title> of "O que inclui um pacote de pesca..." becomes "Pacotes de pesca na Argentina: o que inclui e o que é extra" (slug unchanged);
 *     hub note "Fishing packages in Argentina" (clave paquetes-de-pesca-argentina; slugs pacotes-de-pesca-na-argentina / paquetes-de-pesca-en-argentina /
 *     fishing-packages-in-argentina) listing the package pages that exist in each language and the cost notes, with the "from" price and date
 *     exactly as those pages print them (read live, nothing typed by hand), and the request CTA. Same clave in the three languages: hreflang is automatic.
 *  7. Dorado pillar note (clave golden-dorado-guia): section "Uruguay River: Concordia and Salto Grande" from facts already published
 *     (IGFA record on the species page, the surubí season note, src/content/proximos/concordia.md) and an honest line in "what this guide does not cover".
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = seo-consultas-pacotes-dorado-2026-11-13).
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
				WP_CLI::error( "$etiqueta: la lectura de vuelta no contiene «$t»." );
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
$hoy     = '20261113';
$cambios = 0;
$clave_hub = 'paquetes-de-pesca-argentina';

// 1. PT note "O que inclui um pacote de pesca na Argentina...": new <title>; slug and H1 stay.
$p = pm_buscar( 'pm_nota', 'paquete-incluye-no-incluye', 'pt' );
$nuevo_titulo = 'Pacotes de pesca na Argentina: o que inclui e o que é extra';
if ( ! $p ) {
	WP_CLI::warning( 'nota paquete-incluye-no-incluye (pt): no existe.' );
} elseif ( pm_campo( 'seo_title', $p->ID ) === $nuevo_titulo ) {
	WP_CLI::log( "nota pacotes (pt) #{$p->ID}: el title ya está." );
} else {
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": nota pacotes (pt) #{$p->ID} " . get_permalink( $p->ID ) );
	WP_CLI::log( '  ANTES seo_title: «' . pm_campo( 'seo_title', $p->ID ) . '» · slug: ' . $p->post_name );
	WP_CLI::log( "  DESPUÉS seo_title: «$nuevo_titulo» (" . mb_strlen( $nuevo_titulo ) . ') · slug igual' );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_campo( $p->ID, 'seo_title', $nuevo_titulo, 'nota pacotes (pt)' );
		$ok = get_post( $p->ID )->post_name === $p->post_name;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota pacotes (pt) #{$p->ID} slug {$p->post_name}" );
		$ok || WP_CLI::error( 'el slug cambió' );
	}
}

// 2. Hub "Fishing packages in Argentina" (es/en/pt): the package pages that exist in each language, with the "from" price and date
//    exactly as those pages print them, the cost notes with their first key fact, and the request CTA.
$hub = [
	'es' => [
		'slug' => 'paquetes-de-pesca-en-argentina', 'titulo' => 'Paquetes de pesca en Argentina: dónde hay, cuánto cuestan y qué incluyen',
		'seo_title' => 'Paquetes de pesca en Argentina: precios por destino', 'seo_description' => 'Paquetes de pesca en Argentina por destino, con precio de referencia y fecha, y las notas de costo: qué incluye y qué es extra. Pedinos opciones.',
		'bajada' => 'Los paquetes de pesca que publicamos, por destino, con el precio «desde» y la fecha de cada uno, y las notas que explican qué incluye cada precio.',
		'h_paq' => 'Paquetes por destino', 'h_notas' => 'Cuánto cuesta y qué incluye', 'lugar_l' => 'Paquetes de pesca en %s',
		'p1' => 'Cada destino de abajo tiene su página de paquetes. El precio es el «desde» que muestra esa página, con su fecha; el final depende del grupo y de las fechas.',
		'nota_de' => 'nota del %s', 'datos' => 'Paquetes publicados en: %s.',
		'ref' => 'La nota de lo que incluye y lo que se cobra aparte: ',
	],
	'en' => [
		'slug' => 'fishing-packages-in-argentina', 'titulo' => 'Fishing packages in Argentina: where to find them, what they cost and what they include',
		'seo_title' => 'Fishing packages in Argentina: prices by destination', 'seo_description' => 'Fishing packages in Argentina by destination, with a reference price and date, and the cost notes: what is included and what is extra. Request options.',
		'bajada' => 'The fishing packages we publish, by destination, with the “from” price and date of each, and the notes that explain what each price covers.',
		'h_paq' => 'Packages by destination', 'h_notas' => 'What it costs and what is included', 'lugar_l' => 'Fishing packages in %s',
		'p1' => 'Each destination below has its own packages page. The price is the “from” price that page shows, with its date; the final price depends on the group and the dates.',
		'nota_de' => 'note of %s', 'datos' => 'Packages published in: %s.',
		'ref' => '',
	],
	'pt' => [
		'slug' => 'pacotes-de-pesca-na-argentina', 'titulo' => 'Pacotes de pesca na Argentina: onde há, quanto custam e o que incluem',
		'seo_title' => 'Pacotes de pesca na Argentina: preços por destino', 'seo_description' => 'Pacotes de pesca na Argentina por destino, com preço de referência e data, e as notas de custo: o que inclui e o que é extra. Peça opções.',
		'bajada' => 'Os pacotes de pesca que publicamos, por destino, com o preço «a partir de» e a data de cada um, e as notas que explicam o que cada preço cobre.',
		'h_paq' => 'Pacotes por destino', 'h_notas' => 'Quanto custa e o que inclui', 'lugar_l' => 'Pacotes de pescaria em %s',
		'p1' => 'Cada destino abaixo tem a sua página de pacotes. O preço é o «a partir de» que essa página mostra, com a data; o final depende do grupo e das datas.',
		'nota_de' => 'nota de %s', 'datos' => 'Pacotes publicados em: %s.',
		'ref' => '',
	],
];
$notas_costo = [ 'precio-viaje-dorado-lodge', 'viaje-dorado-amigos-precios', 'precio-pesca-bariloche-limay', 'precio-semana-trucha-patagonia-norte', 'precio-pesca-patagonia-sur', 'paquete-incluye-no-incluye', 'pesca-desde-brasil-corrientes' ];
$fuentes_txt = [ 'es' => 'Investigación propia a partir de datos públicos y proveedores asociados', 'en' => 'Our own research from public data and partner providers', 'pt' => 'Pesquisa própria a partir de dados públicos e fornecedores parceiros' ];
$visto = [];
foreach ( $hub as $i => $h ) {
	$ya = pm_buscar( 'pm_nota', $clave_hub, $i );
	if ( $ya ) {
		WP_CLI::log( "hub paquetes ($i) #{$ya->ID} ya existe: no toco nada." );
		continue;
	}
	// Package pages that exist in this language, with their packages.
	$filas = [];
	$nombres = [];
	foreach ( pm_destinos_lista( $i ) as $d ) {
		$paq = pm_destino_paquetes( $d['clave'], $i );
		if ( ! $paq ) {
			continue;
		}
		$partes = [];
		foreach ( $paq as $pq ) {
			$partes[] = esc_html( $pq['titulo'] ) . ': ' . ( $pq['precio']
				? esc_html( pm_t( 'paquetes.precio.desde', [ 'precio' => pm_paquete_precio_txt( $pq['precio'], $i ), 'unidad' => $pq['precio']['unidad'] ], $i ) ) . '. ' . esc_html( pm_t( 'paquetes.precio.fecha', [ 'fecha' => pm_fecha( $pq['precio']['fecha'], $i ) ], $i ) )
				: esc_html( pm_t( 'paquetes.precio.consultar', [], $i ) ) );
		}
		$filas[]   = '<strong>' . pm_sc_a( pm_url_paquetes( $d['clave'], $i ), esc_html( sprintf( $h['lugar_l'], $d['nombre'] ) ) ) . '</strong><br>' . implode( '<br>', $partes );
		$nombres[] = $d['nombre'];
	}
	$costos = [];
	$datos_notas = [];
	foreach ( $notas_costo as $k ) {
		$n = pm_buscar( 'pm_nota', $k, $i );
		if ( ! $n ) {
			continue;
		}
		$nn      = pm_nota( $n->ID );
		$primero = $nn['datos'][0] ?? '';
		$costos[] = '<strong>' . pm_sc_a( get_permalink( $n->ID ), esc_html( $nn['titulo'] ) ) . '</strong>' . ( $primero ? '<br>' . esc_html( $primero ) : '' ) . ' <span class="note">(' . esc_html( sprintf( $h['nota_de'], pm_fecha( $nn['fecha'], $i ) ) ) . ')</span>';
	}
	if ( ! $filas ) {
		WP_CLI::warning( "hub paquetes ($i): no hay ninguna página de paquetes en este idioma; se salta." );
		continue;
	}
	$cuerpo = pm_sc_p( esc_html( $h['p1'] ) ) . pm_sc_h2( esc_html( $h['h_paq'] ) ) . pm_sc_lista( $filas );
	if ( $costos ) {
		$cuerpo .= pm_sc_h2( esc_html( $h['h_notas'] ) ) . pm_sc_lista( $costos );
	}
	$cuerpo .= pm_sc_p( esc_html( pm_t( 'inicio.pedido.p1', [], $i ) ) . ' ' . pm_sc_a( pm_url_viaje( $i ), esc_html( pm_t( 'inicio.armar', [], $i ) ) ) );
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": hub paquetes ($i) /{$h['slug']}/ «{$h['titulo']}»: " . count( $filas ) . ' destinos (' . implode( ', ', $nombres ) . '), ' . count( $costos ) . ' notas de costo' );
	foreach ( $filas as $f ) {
		WP_CLI::log( '   · ' . wp_strip_all_tags( str_replace( '<br>', ' | ', $f ) ) );
	}
	foreach ( $costos as $f ) {
		WP_CLI::log( '   · ' . wp_strip_all_tags( str_replace( '<br>', ' | ', $f ) ) );
	}
	if ( mb_strlen( $h['seo_title'] ) > 60 || mb_strlen( $h['seo_description'] ) > 160 ) {
		WP_CLI::error( "SEO fuera de rango ($i)" );
	}
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $h['titulo'], 'post_name' => $h['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = [
		'clave' => $clave_hub, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $h['bajada'],
		'nota_datos' => [ [ 'dato' => sprintf( $h['datos'], implode( ', ', $nombres ) ) ] ],
		'fuentes' => [ [ 'nombre' => $fuentes_txt[ $i ], 'url' => '', 'oficial' => 0, 'consultada' => $hoy ] ],
		'seo_title' => $h['seo_title'], 'seo_description' => $h['seo_description'], 'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_especie' => 'dorado',
		'nota_origen' => 'Índice de paquetes y notas de costo (13/11/2026): se arma con las páginas de paquetes y las notas que ya existen; los precios y fechas son los que muestran esas páginas. Docs: docs/seo-consultas-2026-11-13.md.',
	];
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	$visto[ $i ] = $id;
	// Cover: the one of the "what is included" note (every note carries a photo).
	$base = pm_buscar( 'pm_nota', 'paquete-incluye-no-incluye', 'es' );
	if ( $base && get_post_thumbnail_id( $base->ID ) ) {
		set_post_thumbnail( $id, get_post_thumbnail_id( $base->ID ) );
	}
	$ok = get_post( $id )->post_name === $h['slug'] && count( pm_filas( 'nota_datos', $id ) ) === 1 && false !== strpos( get_post_field( 'post_content', $id ), 'paquetes' ) || false !== strpos( get_post_field( 'post_content', $id ), 'pacotes' ) || false !== strpos( get_post_field( 'post_content', $id ), 'packages' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": hub paquetes ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "hub paquetes ($i): lectura de vuelta" );
}
if ( $aplicar && $visto ) {
	flush_rewrite_rules( false );
}

// 3. Dorado pillar note: "Uruguay River: Concordia and Salto Grande" and the honest line in "what this guide does not cover".
$u = [
	'es' => [
		'h2' => 'Río Uruguay: Concordia y Salto Grande',
		'p'  => [
			'De este río es el récord mundial del dorado: el récord todo equipo de la IGFA, 25,28 kg (55 libras y 11 onzas), lo pescó Andre De Button en 2006 en el río Uruguay, en la frontera entre Argentina y Uruguay (dato publicado por Diario Uno, consultado el 24/10/2026; está en %1$s).',
			'Concordia, en Entre Ríos, está sobre el río Uruguay, bajo la represa de Salto Grande, y figura en nuestra lista de destinos próximos (río Uruguay, dorado): todavía no tiene guía. Los únicos meses que tenemos para este tramo salen de %2$s: los guías de Concordia hablan de una corrida de surubí de fines de septiembre a mediados de noviembre, con el pico en octubre, y cuentan que con las compuertas abiertas el río subió a 11 metros, frente a los 5 habituales. Eso es del surubí, no del dorado: no tenemos una fuente con meses de dorado para el río Uruguay.',
		],
		'faltan' => 'Tampoco leímos todavía el reglamento, la licencia ni los operadores del río Uruguay, así que no publicamos nada sobre ellos.',
		'rx_falta' => '/Delta/u', 'l_esp' => 'la ficha del dorado', 'l_nota' => 'nuestra nota de temporada del surubí',
	],
	'en' => [
		'h2' => 'Uruguay River: Concordia and Salto Grande',
		'p'  => [
			'The golden dorado world record comes from this river: the IGFA all-tackle record, 25.28 kg (55 lb 11 oz), was caught by Andre De Button in 2006 in the Uruguay River, on the border between Argentina and Uruguay (figure reported by Diario Uno, consulted 24 October 2026; it is on %1$s).',
			'Concordia, in Entre Ríos, sits on the Uruguay River below the Salto Grande dam and is on our list of coming destinations (Uruguay River, golden dorado): it has no guide yet. The only months we have for this stretch come from %2$s: Concordia guides talk about a surubí run from late September to mid-November, peaking in October, and say that with the dam gates open the river rose to 11 metres, against the usual 5. That is about surubí, not dorado: we have no source with dorado months for the Uruguay River.',
		],
		'faltan' => 'We have not yet read the rules, the license or the operators for the Uruguay River either, so we publish nothing on them.',
		'rx_falta' => '/Delta/u', 'l_esp' => 'the golden dorado species page', 'l_nota' => 'our surubí season note',
	],
	'pt' => [
		'h2' => 'Rio Uruguai: Concordia e Salto Grande',
		'p'  => [
			'Deste rio é o recorde mundial do dourado: o recorde todo equipamento da IGFA, 25,28 kg (55 libras e 11 onças), foi pescado por Andre De Button em 2006 no rio Uruguai, na fronteira entre a Argentina e o Uruguai (dado publicado pelo Diario Uno, consultado em 24/10/2026; está em %1$s).',
			'Concordia, em Entre Ríos, fica no rio Uruguai, abaixo da represa de Salto Grande, e está na nossa lista de destinos próximos (rio Uruguai, dourado): ainda não tem guia. Os únicos meses que temos para este trecho vêm de %2$s: os guias de Concordia falam de uma corrida de surubim do fim de setembro a meados de novembro, com pico em outubro, e contam que com as comportas abertas o rio subiu a 11 metros, contra os 5 habituais. Isso é do surubim, não do dourado: não temos uma fonte com meses de dourado para o rio Uruguai.',
		],
		'faltan' => 'Também ainda não lemos o regulamento, a licença nem os operadores do rio Uruguai, então não publicamos nada sobre eles.',
		'rx_falta' => '/Delta/u', 'l_esp' => 'a ficha do dourado', 'l_nota' => 'a nossa nota de temporada do surubim',
	],
];
foreach ( $u as $i => $t ) {
	$p = pm_buscar( 'pm_nota', 'golden-dorado-guia', $i );
	if ( ! $p ) {
		WP_CLI::warning( "pilar dorado ($i): no existe." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, $t['h2'] ) ) {
		WP_CLI::log( "pilar dorado ($i) #{$p->ID}: ya tiene la sección." );
		continue;
	}
	$esp  = pm_buscar( 'pm_especie', 'dorado', $i ) ?? pm_buscar( 'pm_especie', 'dorado', 'es' );
	$nota = pm_buscar( 'pm_nota', 'cuando-pescar-surubi', $i ) ?? pm_buscar( 'pm_nota', 'cuando-pescar-surubi', 'es' );
	$pos  = pm_sc_pos_encabezado( $c, '/no cubre|does not cover|não cobre/iu' );
	$pos2 = pm_sc_pos_tras_parrafo( $c, $t['rx_falta'] );
	if ( ! $esp || ! $nota || null === $pos || null === $pos2 ) {
		WP_CLI::warning( "pilar dorado ($i): falta la ficha, la nota o el encabezado «no cubre»; se salta (pos=" . var_export( $pos, true ) . ', pos2=' . var_export( $pos2, true ) . ').' );
		continue;
	}
	$sec = pm_sc_h2( $t['h2'] ) . pm_sc_p( sprintf( $t['p'][0], pm_sc_a( get_permalink( $esp->ID ), $t['l_esp'] ) ) ) . pm_sc_p( sprintf( $t['p'][1], '', pm_sc_a( get_permalink( $nota->ID ), $t['l_nota'] ) ) );
	// Insert the later position first so the earlier offset stays valid.
	$nuevo = pm_sc_insertar( pm_sc_insertar( $c, $pos2, pm_sc_p( $t['faltan'] ) ), $pos, $sec );
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": pilar dorado ($i) #{$p->ID} " . get_permalink( $p->ID ) . ": H2 «{$t['h2']}» antes de «no cubre» + línea en esa lista" );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_guardar( $p->ID, $nuevo, [ $t['h2'], $t['faltan'] ], "pilar dorado ($i)" );
		WP_CLI::log( "VERIFIED: pilar dorado ($i) #{$p->ID}" );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
