<?php
/**
 * Read-only, 13/11/2026: prints the LIVE es/en/pt species pages (dorado, surubi, trucha), the notes golden-dorado-guia and
 * la-zona-salto-grande, and the guides rio-gallegos, jurassic-lake and el-calafate (body, faqs, fuentes, summary fields), so the
 * size/record and Santa Cruz Anexo I patches are written against what is published today. Writes nothing.
 * Run with wp-script.yml (script = volcar-tamanos-2026-11-13).
 */
$items = [
	[ 'pm_especie', [ 'dorado', 'surubi', 'trucha' ] ],
	[ 'pm_nota', [ 'golden-dorado-guia', 'la-zona-salto-grande' ] ],
	[ 'pm_destino', [ 'rio-gallegos', 'jurassic-lake', 'el-calafate' ] ],
];
foreach ( $items as [ $tipo, $claves ] ) {
	foreach ( $claves as $clave ) {
		foreach ( [ 'es', 'en', 'pt' ] as $i ) {
			$p = pm_buscar( $tipo, $clave, $i );
			if ( ! $p ) {
				WP_CLI::log( "#### $tipo $clave ($i): NO EXISTE" );
				continue;
			}
			WP_CLI::log( "#### $tipo $clave ($i) #{$p->ID} slug={$p->post_name} url=" . get_permalink( $p->ID ) );
			foreach ( [ 'faqs', 'fuentes', 'temporada', 'aguas' ] as $k ) {
				$f = pm_filas( $k, $p->ID );
				$f && WP_CLI::log( "@@ $k: " . wp_json_encode( $f, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
			}
			foreach ( [ 'seo_description', 'seo_titulo', 'reglamento_resumen', 'actualizado', 'titulo_seo' ] as $k ) {
				$v = pm_campo( $k, $p->ID );
				is_scalar( $v ) && '' !== (string) $v && WP_CLI::log( "@@ $k: " . $v );
			}
			WP_CLI::log( '@@ cuerpo:' );
			WP_CLI::log( $p->post_content );
			WP_CLI::log( "#### fin $tipo $clave ($i)" );
		}
	}
}
