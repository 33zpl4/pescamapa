<?php
/**
 * Read-only, 31/10/2026: prints the LIVE English guides of jurassic-lake, san-martin-junin and chascomus (body, h1, bajada,
 * modalidades, epoca, temporada, reglamento_resumen, aguas, faqs, fuentes) so the Portuguese versions are translated from what is
 * published today, not from the original scripts (the Jurassic Lake script still carries the 200 m / 100 m exclusion lines that were
 * removed live on 30/10). Writes nothing. Run with wp-script.yml (script = volcar-guias-en-2026-10-31).
 */
foreach ( [ 'jurassic-lake', 'san-martin-junin', 'chascomus' ] as $clave ) {
	$p = pm_buscar( 'pm_destino', $clave, 'en' );
	if ( ! $p ) {
		WP_CLI::log( "#### $clave (en): NO EXISTE" );
		continue;
	}
	WP_CLI::log( "#### $clave (en) #{$p->ID} slug={$p->post_name} titulo={$p->post_title}" );
	foreach ( [ 'h1', 'bajada', 'modalidades', 'epoca', 'seo_title', 'seo_description', 'reglamento_resumen', 'reglamento_autoridad', 'reglamento_url', 'reglamento_verificado', 'actualizado' ] as $k ) {
		WP_CLI::log( "@@ $k: " . str_replace( "\n", ' | ', (string) pm_campo( $k, $p->ID ) ) );
	}
	foreach ( [ 'temporada', 'aguas', 'faqs', 'fuentes' ] as $k ) {
		WP_CLI::log( "@@ $k: " . wp_json_encode( pm_filas( $k, $p->ID ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	}
	WP_CLI::log( '@@ cuerpo:' );
	WP_CLI::log( $p->post_content );
	WP_CLI::log( '#### fin ' . $clave );
}
