<?php
/**
 * Lector SOLO LECTURA (25/11/2026) para la auditoría de «qué dice cada nota de precios sobre el servicio» (antes de servicio-en-notas-precios-2026-11-25.php).
 * No escribe nada (ni posts, ni opciones, ni archivos). Por nota (clave) e idioma imprime: id, slug, estado, nota_informativa (se lee de la fila es),
 * los primeros 600 caracteres de texto visible del contenido guardado, las preguntas de la FAQ (pm_faqs_contenido), cada oración con
 * presupuesto/opciones/gratis/quote/orçamento/free, y qué marcadores trae la página renderizada (cta-nota, barra-pedir, contraste, enlace a la página de presupuesto).
 * Además imprime las primeras 60 líneas de /llms.txt y, en los tres idiomas, el <title>, la meta description y el H1 de la portada.
 * Uso: Run wp-script con script = servicio-notas-leer-2026-11-25 (no hace falta `aplicar`).
 */
$claves = [
	'cuanto-cuesta-viaje-pesca-argentina',
	'precio-semana-trucha-patagonia-norte',
	'precio-viaje-dorado-lodge',
	'viaje-dorado-amigos-precios',
	'paquete-incluye-no-incluye',
	'como-preparar-viaje-pesca-argentina',
	'precio-pesca-bariloche-limay',
	'precio-pesca-patagonia-sur',
];
$texto = function ( string $html ): string {
	$html = preg_replace( '#<!--.*?-->#s', ' ', $html );
	$html = preg_replace( '#<(script|style|noscript|svg|template)\b.*?</\1\s*>#is', ' ', $html );
	$html = preg_replace( '#<[^>]*>#', ' ', $html );
	return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( $html, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
};
$get = function ( string $url ) {
	$r = wp_remote_get( $url, [ 'timeout' => 20, 'redirection' => 3, 'limit_response_size' => 2000000, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)' ] );
	return is_wp_error( $r ) ? [ 0, 'ERROR ' . $r->get_error_message() ] : [ (int) wp_remote_retrieve_response_code( $r ), (string) wp_remote_retrieve_body( $r ) ];
};
$re = '/presupuesto|opciones|gratis|gratuito|quotes?\b|options|free\b|orçamento|opções|grátis|gratuito/iu';

foreach ( $claves as $clave ) {
	$filas = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	echo "\n######## $clave (" . count( $filas ) . " filas)\n";
	$por = [];
	foreach ( $filas as $p ) {
		$por[ pm_idioma_de( $p->ID ) ] = $p;
	}
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		if ( empty( $por[ $i ] ) ) {
			echo "-- $i: NO EXISTE\n";
			continue;
		}
		$p = $por[ $i ];
		$c = (string) $p->post_content;
		$t = $texto( $c );
		$informativa = ( 'es' === $i ) ? (int) (bool) pm_campo( 'nota_informativa', $p->ID ) : '(se lee de es)';
		echo "-- $i #{$p->ID} /{$p->post_name}/ estado={$p->post_status} informativa=$informativa palabras=" . str_word_count( $t ) . ' tablas=' . substr_count( $c, '<table' ) . ' pm-faq=' . substr_count( $c, 'pm-faq' ) . "\n";
		echo '   primeros 600: ' . mb_substr( $t, 0, 600 ) . "\n";
		echo '   FAQ: ';
		$faqs = pm_faqs_contenido( $c );
		echo $faqs ? "\n" . implode( "\n", array_map( fn( $f, $n ) => '     ' . ( $n + 1 ) . '. ' . $f['pregunta'], $faqs, array_keys( $faqs ) ) ) : '(sin FAQ en el contenido)';
		echo "\n";
		$oraciones = preg_split( '/(?<=[.!?])\s+/u', $t );
		$hits      = array_values( array_filter( $oraciones, fn( $s ) => preg_match( $re, $s ) ) );
		echo '   oraciones con servicio (' . count( $hits ) . "):\n";
		foreach ( array_slice( $hits, 0, 14 ) as $s ) {
			echo '     · ' . mb_substr( $s, 0, 300 ) . "\n";
		}
		[ $st, $html ] = $get( get_permalink( $p ) );
		$b = $st === 200 ? $html : '';
		echo "   render: status=$st cta-nota=" . substr_count( $b, 'class="cta-nota"' ) . ' barra-pedir=' . substr_count( $b, 'barra-pedir' ) . ' contraste=' . substr_count( $b, 'class="contraste"' ) . ' banda-gratis=' . substr_count( $b, 'banda-gratis' ) . ' enlace-presupuesto=' . ( preg_match( '#pedir-presupuesto-pesca|pedir-orcamento-pesca|get-fishing-lodge-quotes#', $c ) ? 'en contenido' : 'no' ) . ' faqpage=' . ( false !== strpos( $b, '"FAQPage"' ) ? 'si' : 'no' ) . "\n";
	}
}

echo "\n######## /llms.txt (primeras 60 líneas)\n";
[ $st, $b ] = $get( home_url( '/llms.txt' ) );
echo "status=$st\n" . implode( "\n", array_slice( preg_split( '/\R/', $b ), 0, 60 ) ) . "\n";

foreach ( [ 'es' => '/', 'pt' => '/pt/', 'en' => '/en/' ] as $i => $ruta ) {
	[ $st, $b ] = $get( home_url( $ruta ) );
	echo "\n######## portada $i ($ruta) status=$st\n";
	preg_match( '#<title>(.*?)</title>#su', $b, $m1 );
	preg_match( '#<meta[^>]+name="description"[^>]+content="([^"]*)"#iu', $b, $m2 );
	preg_match_all( '#<h1\b[^>]*>(.*?)</h1>#su', $b, $m3 );
	echo '  title: ' . html_entity_decode( trim( $m1[1] ?? '' ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) . "\n  description: " . html_entity_decode( $m2[1] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8' ) . "\n  h1 (" . count( $m3[1] ) . '): ' . implode( ' | ', array_map( fn( $h ) => $texto( $h ), $m3[1] ) ) . "\n";
}
WP_CLI::success( 'Lectura terminada (solo lectura).' );
