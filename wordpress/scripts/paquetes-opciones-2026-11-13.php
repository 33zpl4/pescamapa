<?php
/**
 * "Pedir cotización" follow-up of PR 172 (wording "opciones"), 13/11/2026. The package pages' Google description (stored field
 * paquetes_description of Goya and Esquina, written by paquetes-intros-2026-10-28) still said "Cotización sin costo" / "Free quote" /
 * "Cotação sem custo" as a promise. Replaced only where the field still holds that text (an editor's own text is left alone).
 * Run with wp-script.yml (script = paquetes-opciones-2026-11-13).
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
$cambios = 0;

// destino => idioma => [ new description for Google (<= 160) ]
$nuevo = [
	'goya' => [
		'es' => 'Paquetes de pesca en Goya: dorado con devolución en la Reserva Isoró, carnada en el Paraná, nocturna, paseos y packs con alojamiento. Pedí opciones sin costo.',
		'en' => 'Fishing packages in Goya: catch-and-release dorado in the Isoró Reserve, bait fishing on the Paraná, night trips, boat tours and lodging packs. Request options.',
		'pt' => 'Pacotes de pescaria em Goya, Argentina: dourado com devolução na Reserva Isoró, isca natural no Paraná, pesca noturna, passeios e hospedagem. Peça opções.',
	],
	'esquina' => [
		'es' => 'Paquetes de pesca en Esquina: carnada viva, señuelos, mosca, al garete y trolling, todo incluido y packs con alojamiento. Pedí opciones sin costo.',
		'en' => 'Fishing packages in Esquina, Argentina: live bait, lures, fly fishing, drift fishing and trolling, all-inclusive and lodging packs. Request options.',
		'pt' => 'Pacotes de pescaria em Esquina, Argentina: isca viva, artificiais, mosca, deriva e corrico, tudo incluído e pacotes com hospedagem. Peça opções.',
	],
];
foreach ( $nuevo as $destino => $idiomas ) {
	foreach ( $idiomas as $i => $desc ) {
		$p = pm_buscar( 'pm_destino', $destino, $i );
		if ( ! $p ) {
			WP_CLI::log( "$destino ($i): no hay guía en este idioma." );
			continue;
		}
		$actual = trim( (string) pm_campo( 'paquetes_description', $p->ID ) );
		if ( $actual === $desc ) {
			WP_CLI::log( "$destino ($i) #{$p->ID}: ya está." );
			continue;
		}
		if ( ! preg_match( '/Cotizaci[óo]n sin costo|Free quote|Cotação sem custo/u', $actual ) ) {
			WP_CLI::log( "$destino ($i) #{$p->ID}: la descripción es otra (" . mb_strlen( $actual ) . ' car.): «' . $actual . '»; no la toco.' );
			continue;
		}
		mb_strlen( $desc ) <= 160 || WP_CLI::error( "$destino ($i): " . mb_strlen( $desc ) . ' car.' );
		WP_CLI::log( pm_sc_modo( $aplicar ) . ": $destino ($i) #{$p->ID}: «{$actual}» (" . mb_strlen( $actual ) . ") → «{$desc}» (" . mb_strlen( $desc ) . ')' );
		$cambios++;
		if ( $aplicar ) {
			pm_sc_campo( $p->ID, 'paquetes_description', $desc, "$destino ($i)" );
			WP_CLI::log( "VERIFIED: $destino ($i) #{$p->ID} paquetes_description leída de vuelta" );
		}
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
