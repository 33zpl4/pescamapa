<?php
/**
 * Search Console follow-up, 13/11/2026, fix 4 (content part): EN <title> of the Esquel y Trevelin destination page
 * "Fly fishing in Esquel, Argentina: season, rules and permit 2026/27". The guides-page title/H1/meta/FAQ are template overrides
 * (ui.json guias.*.esquel-trevelin, ships with the merge). Also logs the ES/PT titles and the season fields the FAQ is built from.
 * Idempotent. Simulacro first. Run with wp-script.yml (script = seo-consultas-esquel-2026-11-13).
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
$cambios = 0;
$nuevo   = 'Fly fishing in Esquel, Argentina: season, rules and permit 2026/27';
// EN destination page <title> (query "fly fishing esquel argentina"). ES/PT are only read and logged: the decision is in the report.
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = pm_buscar( 'pm_destino', 'esquel-trevelin', $i );
	if ( ! $p ) {
		WP_CLI::warning( "esquel-trevelin ($i): no existe." );
		continue;
	}
	$actual = (string) pm_campo( 'seo_title', $p->ID );
	WP_CLI::log( "LEÍDO esquel-trevelin ($i) #{$p->ID}: seo_title «{$actual}» · h1 «" . pm_campo( 'h1', $p->ID ) . '»' );
	if ( 'en' !== $i ) {
		continue;
	}
	if ( $actual === $nuevo ) {
		WP_CLI::log( 'esquel-trevelin (en): el title ya está.' );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": esquel-trevelin (en): ANTES «{$actual}» → DESPUÉS «{$nuevo}» (" . mb_strlen( $nuevo ) . ')' );
	$cambios++;
	if ( $aplicar ) {
		pm_sc_campo( $p->ID, 'seo_title', $nuevo, 'esquel-trevelin (en)' );
		WP_CLI::log( 'VERIFIED: esquel-trevelin (en) #' . $p->ID . ' ' . get_permalink( $p->ID ) );
	}
}
// Río Grande: the guide's own page is untouched; log its season fields, which feed the FAQ of the guides page.
foreach ( [ 'rio-grande', 'esquel-trevelin' ] as $k ) {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$p = pm_buscar( 'pm_destino', $k, $i );
		if ( $p ) {
			$d = pm_destino( $p->ID );
			WP_CLI::log( "LEÍDO temporada $k ($i): abre {$d['abre']} · cierra {$d['cierra']} · autoridad «{$d['reglamento']['autoridad']}» · verificado {$d['reglamento']['verificado']} · nota «{$d['temporada_nota']}»" );
		}
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
