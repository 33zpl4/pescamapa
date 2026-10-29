<?php
/**
 * READ ONLY (writes nothing). Prints the live English SEO title, description and H1 of the guides, species pages and
 * notes that talk about dorado, with their length, and whether they already say "golden dorado" (what English
 * speakers search: docs/seo-palabras-clave-en-2026-10-29.md). Used to write exact replacements, since editors may have
 * changed what the original scripts loaded. Run with wp-script.yml (script = leer-titulos-en-2026-10-29).
 */

$tipos = [ 'pm_destino', 'pm_especie', 'pm_nota' ];
foreach ( $tipos as $tipo ) {
	foreach ( pm_posts( $tipo ) as $p ) {
		if ( 'en' !== pm_idioma_de( $p->ID ) ) {
			continue;
		}
		$t = (string) pm_campo( 'seo_title', $p->ID );
		$d = (string) pm_campo( 'seo_description', $p->ID );
		$h = (string) pm_campo( 'h1', $p->ID );
		$todo = $t . ' ' . $d . ' ' . $h . ' ' . $p->post_title;
		if ( ! preg_match( '/dorado/i', $todo ) ) {
			continue;
		}
		WP_CLI::log( sprintf( '[%s #%d %s] %s', $tipo, $p->ID, pm_clave_de( $p->ID ), $p->post_status ) );
		WP_CLI::log( sprintf( '  title(%d%s): %s', mb_strlen( $t ), preg_match( '/golden dorado/i', $t ) ? ', golden' : '', $t ) );
		WP_CLI::log( sprintf( '  desc(%d%s): %s', mb_strlen( $d ), preg_match( '/golden dorado/i', $d ) ? ', golden' : '', $d ) );
		if ( '' !== $h ) {
			WP_CLI::log( sprintf( '  h1(%d%s): %s', mb_strlen( $h ), preg_match( '/golden dorado/i', $h ) ? ', golden' : '', $h ) );
		}
	}
}
WP_CLI::log( 'SOLO LECTURA: no se escribió nada' );
