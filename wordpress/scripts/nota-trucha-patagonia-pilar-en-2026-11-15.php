<?php
/**
 * Correction to the English pillar note (clave trucha-patagonia-pilar), 15/11/2026, after the Keyword Planner data of the same day: plain "patagonia" in
 * English is dominated by the clothing brand; the travel terms are small but pricier ("trout fishing in patagonia", "patagonia fishing trip(s)", "fishing lodge",
 * "fly fishing packages", "fishing guides", "brown trout patagonia", "tierra del fuego brown trout"). Changes: "Argentina" in the title, H1 and meta title/description
 * (slug unchanged: stable URL), and the words "fishing trip", "fishing lodge", "fishing guides" and "fly fishing packages" in H2/H3, the FAQ and the cost/CTA
 * lines, where the note describes where and how a trip is booked. NO fact, number, date or source changes. The Spanish and Portuguese notes are not touched.
 * Idempotent: skipped when the title already says "Argentina". Dry run first ($aplicar = false). Run with wp-script.yml
 * (script = nota-trucha-patagonia-pilar-en-2026-11-15).
 */
$aplicar = false;
$clave   = 'trucha-patagonia-pilar';
$titulo  = 'Trout fishing in Patagonia, Argentina: brown, rainbow and sea-run';
$seo_t   = 'Trout fishing in Patagonia, Argentina: brown and rainbow';
$seo_d   = 'Trout fishing in Patagonia, Argentina: brown, rainbow and sea-run trout, where to go, when, which permit and how to book a fishing trip, with sources.';
$pares   = [
	[ '<strong>Short answer.</strong> Patagonia has three', '<strong>Short answer.</strong> Argentine Patagonia has three' ],
	[ '>Where to fish trout in Patagonia</h2>', '>Where to go on a trout fishing trip in Patagonia</h2>' ],
	[ 'The rules, the permit and the operators that publish information are in the guide itself.', 'The rules, the permit and the fishing lodges and fishing guides that publish information are in the guide itself.' ],
	[ '>Brown trout (Salmo trutta)</h2>', '>Brown trout in Patagonia and Tierra del Fuego (Salmo trutta)</h2>' ],
	[ '>How much a trout trip costs</h2>', '>How much a trout fishing trip costs</h2>' ],
	[ 'Dated prices, with their source, are in three notes:', 'Dated prices for fishing lodges and guided fly fishing days, with their source, are in three notes:' ],
	[ '<strong>Want us to ask the operators that fit?</strong> Tell us your dates', '<strong>Planning a trout fishing trip?</strong> Tell us your dates' ],
	[ '>Where in Patagonia can I fish for trout?</h3>', '>Where can I book a trout fishing trip or a fishing lodge in Patagonia?</h3>' ],
	[ 'The table above links to each one, with its waters, rules and permit.</p>', 'The table above links to each one, with its waters, rules and permit, and the fishing lodges and fishing guides that publish information. To compare a fly fishing package, send your dates in the form linked in this note and we ask the operators that fit.</p>' ],
	[ 'Dated prices with their source are in three notes: a week', 'Dated prices for fishing lodges, guided days and fly fishing packages, with their source, are in three notes: a week' ],
];
$tl = mb_strlen( $seo_t );
$dl = mb_strlen( $seo_d );
( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "SEO fuera de rango: title $tl / description $dl" );
$p = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $x ) => 'en' === pm_idioma_de( $x->ID ) ) )[0] ?? null;
$p || WP_CLI::error( 'no existe la nota en inglés del pilar.' );
if ( $p->post_title === $titulo ) {
	WP_CLI::success( "#{$p->ID} ya dice «Argentina»: no toco nada." );
	return;
}
$c = $p->post_content;
$falta = [];
foreach ( $pares as $i => [ $viejo, $nuevo ] ) {
	if ( 1 !== substr_count( $c, $viejo ) ) {
		$falta[] = $i;
		continue;
	}
	$c = str_replace( $viejo, $nuevo, $c );
}
$falta && WP_CLI::error( 'no encontré (una sola vez) el texto de los cambios: ' . implode( ', ', $falta ) . '; el contenido fue editado, no toco nada.' );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": #{$p->ID} título «{$p->post_title}» -> «{$titulo}»; seo title $tl / description $dl; " . count( $pares ) . ' reemplazos en el contenido, ninguno cambia un dato' );
if ( ! $aplicar ) {
	WP_CLI::success( 'Simulacro terminado: 1 nota.' );
	return;
}
$r = wp_update_post( [ 'ID' => $p->ID, 'post_title' => $titulo, 'post_content' => wp_slash( $c ) ], true );
is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
update_field( 'seo_title', $seo_t, $p->ID );
update_field( 'seo_description', $seo_d, $p->ID );
$g  = get_post( $p->ID );
$n  = pm_nota( $p->ID );
$ok = $g->post_title === $titulo && $g->post_name === 'trout-fishing-in-patagonia' && $n['seo_title'] === $seo_t && $n['seo_description'] === $seo_d
	&& false !== strpos( $g->post_content, 'fishing lodge' ) && false !== strpos( $g->post_content, 'fly fishing package' ) && false !== strpos( $g->post_content, 'fishing guides' )
	&& 8 === count( pm_faqs_contenido( $g->post_content ) ) && 17 === count( pm_filas( 'fuentes', $p->ID ) );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota en #{$p->ID} " . get_permalink( $p->ID ) );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
WP_CLI::success( 'Aplicado: 1 nota.' );
