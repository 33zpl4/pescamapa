<?php
/**
 * Dorado pillar note (clave golden-dorado-guia, es/en/pt), 16/11/2026, follow-up of pilar-dorado-2026-11-16 (step 2). While reading the operators for step 6 we found one page that states
 * the styles the assistants repeat, in the operator's own words: SET Fly Fishing, "Golden Dorado: 3 Lodges 3 Experiences" (https://setflyfishing.com/en/3-lodges-3-experiences/, read on 13/11/2026 with
 * investigar.yml, folder corrientes-3-20261113-2316): Upper Paraná = "trophy waters, the largest dorados on a fly"; Iberá = top water, wildlife, birdwatching; Isoró wetland and middle Paraná = "a lot of
 * action and number of catches", "perfect for those anglers new to Golden Dorado fishing". This rewrites the "number of fish" row (it said we had nothing), fills its place cell and adds one attributed sentence
 * to the "big fish" row, plus the source. It names no program that is not already in our listings (the Isoró lodge has no card) and gives no number of fish per day.
 * Idempotent (skips a pillar that already cites the page; touches nothing if any of the three anchors is missing). Dry run first ($aplicar = false). Run with wp-script.yml (script = pilar-dorado-set-2026-11-16).
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
$clave   = 'golden-dorado-guia';
$hoy     = '20261116';
$u = [ 'set3' => 'https://setflyfishing.com/en/3-lodges-3-experiences/' ];
$link = fn( string $txt ) => '<a href="' . esc_url( $u['set3'] ) . '" rel="noopener">' . $txt . '</a>';

$T = [
	'es' => [
		'celda_viejo' => '<td>Las páginas que leímos hablan de tamaño y de estilo, no de peces por día. Todavía no publicamos una cifra de peces por día para ningún tramo: preguntale al lugar por su promedio de la temporada.</td>',
		'celda_nuevo' => 'Según la página de SET Fly Fishing sobre sus tres programas (%s, leída el 13/11/2026), su opción del humedal del Isoró y el Paraná medio tiene «mucha acción y cantidad de capturas» y es «ideal para quienes recién empiezan» con el dorado (traducción nuestra); la del Iberá es pesca de superficie con fauna y aves, y la del Alto Paraná, aguas de trofeo. Ese programa del Isoró no figura entre nuestras fichas, y ninguna página que leímos da una cifra de peces por día: preguntale al lugar por su promedio de la temporada.',
		'dest_viejo' => '<td>—</td>', 'dest_nuevo' => 'Paraná medio: {g:goya|Goya}, {g:esquina|Esquina}',
		'grande_fin' => 'habla del mejor potencial de peces grandes en el Alto Paraná.',
		'grande_mas' => ' SET Fly Fishing dice que su programa del Alto Paraná son «aguas de trofeo» y «los dorados más grandes con mosca» (traducción nuestra; %s, 13/11/2026).',
		'a_pagina' => 'página', 'fuente' => 'SET Fly Fishing: Golden Dorado, 3 lodges 3 experiences (los tres programas: Alto Paraná, Iberá, Isoró y Paraná medio)',
	],
	'en' => [
		'celda_viejo' => '<td>The pages we read talk about size and style, not fish per day. We do not publish a daily number for any stretch yet: ask the place for its season average.</td>',
		'celda_nuevo' => 'According to the SET Fly Fishing page on its three programmes (%s, read on 13/11/2026), its Isoró wetland and middle Paraná option has “a lot of action and number of catches” and is “perfect for those anglers new to Golden Dorado fishing”; the Iberá one is surface fishing with wildlife and birds, and the Upper Paraná one is trophy waters. That Isoró programme is not among our listings, and no page we read gives a number of fish per day: ask the place for its season average.',
		'dest_viejo' => '<td>—</td>', 'dest_nuevo' => 'Middle Paraná: {g:goya|Goya}, {g:esquina|Esquina}',
		'grande_fin' => 'points to the Upper Paraná as having the best potential for big fish.',
		'grande_mas' => ' SET Fly Fishing says its Upper Paraná programme is “trophy waters” with “the largest dorados on a fly” (%s, 13/11/2026).',
		'a_pagina' => 'page', 'fuente' => 'SET Fly Fishing: Golden Dorado, 3 lodges 3 experiences (the three programmes: Upper Paraná, Iberá, Isoró and middle Paraná)',
	],
	'pt' => [
		'celda_viejo' => '<td>As páginas que lemos falam de tamanho e de estilo, não de peixes por dia. Ainda não publicamos um número de peixes por dia para nenhum trecho: pergunte ao lugar a média da temporada.</td>',
		'celda_nuevo' => 'Segundo a página da SET Fly Fishing sobre seus três programas (%s, lida em 13/11/2026), a opção da área alagada do Isoró e do Paraná médio tem «muita ação e quantidade de capturas» e é «ideal para quem está começando» no dourado (tradução nossa); a do Iberá é pesca de superfície com fauna e aves, e a do Alto Paraná, águas de troféu. Esse programa do Isoró não está entre as nossas fichas, e nenhuma página que lemos dá um número de peixes por dia: pergunte ao lugar a média da temporada.',
		'dest_viejo' => '<td>—</td>', 'dest_nuevo' => 'Paraná médio: {g:goya|Goya}, {g:esquina|Esquina}',
		'grande_fin' => 'fala do melhor potencial de peixes grandes no Alto Paraná.',
		'grande_mas' => ' A SET Fly Fishing diz que seu programa do Alto Paraná são «águas de troféu» e «os maiores dourados na mosca» (tradução nossa; %s, 13/11/2026).',
		'a_pagina' => 'página', 'fuente' => 'SET Fly Fishing: Golden Dorado, 3 lodges 3 experiences (os três programas: Alto Paraná, Iberá, Isoró e Paraná médio)',
	],
];
$cambios = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$t = $T[ $i ];
	$p = pm_buscar( 'pm_nota', $clave, $i );
	if ( ! $p ) {
		WP_CLI::warning( "($i): no existe el pilar; no toco nada." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, $u['set3'] ) ) {
		WP_CLI::log( "($i) #{$p->ID}: ya cita la página de SET; no toco nada." );
		continue;
	}
	$g_es = pm_buscar( 'pm_destino', 'goya', $i ) ?? pm_buscar( 'pm_destino', 'goya', 'es' );
	$q_es = pm_buscar( 'pm_destino', 'esquina', $i ) ?? pm_buscar( 'pm_destino', 'esquina', 'es' );
	$dest = strtr( $t['dest_nuevo'], [
		'{g:goya|Goya}' => $g_es ? '<a href="' . esc_url( get_permalink( $g_es->ID ) ) . '">Goya</a>' : 'Goya',
		'{g:esquina|Esquina}' => $q_es ? '<a href="' . esc_url( get_permalink( $q_es->ID ) ) . '">Esquina</a>' : 'Esquina',
	] );
	$ok_celda = false !== strpos( $c, $t['celda_viejo'] );
	$ok_dest  = 1 === substr_count( $c, $t['dest_viejo'] );
	$ok_grande = false !== strpos( $c, $t['grande_fin'] );
	if ( ! $ok_celda || ! $ok_dest || ! $ok_grande ) {
		WP_CLI::warning( "($i) #{$p->ID}: no encuentro la fila de «cantidad» (" . ( $ok_celda ? 'sí' : 'NO' ) . ') o su celda de lugar (' . ( $ok_dest ? 'sí' : 'NO' ) . ') o el final de la fila de «peces grandes» (' . ( $ok_grande ? 'sí' : 'NO' ) . '); no toco nada.' );
		continue;
	}
	$nuevo = str_replace( $t['celda_viejo'], '<td>' . sprintf( $t['celda_nuevo'], $link( $t['a_pagina'] ) ) . '</td>', $c );
	$nuevo = str_replace( $t['dest_viejo'], '<td>' . $dest . '</td>', $nuevo );
	$nuevo = str_replace( $t['grande_fin'], $t['grande_fin'] . sprintf( $t['grande_mas'], $link( $t['a_pagina'] ) ), $nuevo );
	$filas = pm_filas( 'fuentes', $p->ID );
	$filas[] = [ 'nombre' => $t['fuente'], 'url' => $u['set3'], 'oficial' => 0, 'consultada' => '20261113' ];
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": ($i) #{$p->ID} fila «cantidad» reescrita con la página de SET, celda de lugar y frase en «peces grandes»; 1 fuente nueva; palabras " . str_word_count( wp_strip_all_tags( $c ) ) . ' → ' . str_word_count( wp_strip_all_tags( $nuevo ) ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$slug = $p->post_name;
	$r = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	update_field( 'fuentes', $filas, $p->ID );
	$gg = get_post( $p->ID );
	$ok = $gg->post_name === $slug && 3 === substr_count( $gg->post_content, $u['set3'] ) && false === strpos( $gg->post_content, $t['celda_viejo'] ) && count( pm_faqs_contenido( $gg->post_content ) ) === count( pm_faqs_contenido( $c ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": pilar del dorado ($i) #{$p->ID} con la página de SET (" . substr_count( $gg->post_content, $u['set3'] ) . ' enlaces), ' . count( pm_filas( 'fuentes', $p->ID ) ) . ' fuentes ' . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios notas." );
