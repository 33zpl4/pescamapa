<?php
/**
 * Lector en vivo, SOLO LECTURA (17/11/2026). Reemplazo barato de investigar.yml mientras no hay minutos de Actions:
 * corre en el servidor (wp-script.yml, ~1 min) y pide cada URL con wp_remote_get. No escribe nada: ni posts, ni opciones, ni archivos.
 *
 * Entrada: wordpress/scripts/leer-vivo-urls.txt, una por línea. Formatos:
 *   https://sitio/pagina/
 *   https://sitio/pagina/ | cadena uno ; cadena dos     (dice presente/ausente por cada cadena, sin distinguir mayúsculas)
 *   # comentario (y líneas vacías) se ignoran.
 * wp-script.yml copia ese txt al servidor junto con el script (/tmp/pm-script-urls.txt).
 * Por URL imprime: status, redirección final, <title>, meta description, nº de h1, tipos de JSON-LD y las cadenas pedidas.
 * Uso: Run wp-script con script = leer-vivo (no hace falta `aplicar`: no escribe).
 */
$candidatos = [ '/tmp/pm-script-urls.txt', __DIR__ . '/leer-vivo-urls.txt' ];
$archivo    = '';
foreach ( $candidatos as $c ) {
	if ( is_readable( $c ) ) {
		$archivo = $c;
		break;
	}
}
if ( ! $archivo ) {
	WP_CLI::error( 'No encuentro leer-vivo-urls.txt (se esperaba en /tmp/pm-script-urls.txt).' );
}

$lineas = preg_split( '/\R/', (string) file_get_contents( $archivo ) );
$total  = 0;
$fallos = 0;

foreach ( $lineas as $linea ) {
	$linea = trim( $linea );
	if ( '' === $linea || '#' === $linea[0] ) {
		continue;
	}
	$partes  = array_map( 'trim', explode( '|', $linea, 2 ) );
	$url     = $partes[0];
	$cadenas = isset( $partes[1] ) ? array_values( array_filter( array_map( 'trim', explode( ';', $partes[1] ) ), 'strlen' ) ) : [];
	if ( ! preg_match( '#^https?://#i', $url ) ) {
		WP_CLI::warning( "Línea ignorada (no es una URL): {$linea}" );
		continue;
	}
	$total++;
	echo "\n== {$url}\n";
	$r = wp_remote_get( $url, [ 'timeout' => 20, 'redirection' => 5, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)', 'sslverify' => true ] );
	if ( is_wp_error( $r ) ) {
		echo '  ERROR: ' . $r->get_error_message() . "\n";
		$fallos++;
		continue;
	}
	$status = (int) wp_remote_retrieve_response_code( $r );
	$html   = (string) wp_remote_retrieve_body( $r );
	$final  = $url;
	if ( isset( $r['http_response'] ) && is_object( $r['http_response'] ) && method_exists( $r['http_response'], 'get_response_object' ) ) {
		$obj = $r['http_response']->get_response_object();
		if ( isset( $obj->url ) && $obj->url ) {
			$final = (string) $obj->url;
		}
	}
	echo "  status: {$status}" . ( $final !== $url ? "  (final: {$final})" : '' ) . '  bytes: ' . strlen( $html ) . "\n";
	if ( $status >= 400 || '' === $html ) {
		$fallos++;
	}

	$titulo = preg_match( '#<title[^>]*>(.*?)</title>#is', $html, $m ) ? trim( html_entity_decode( wp_strip_all_tags( $m[1] ), ENT_QUOTES, 'UTF-8' ) ) : '(sin title)';
	echo "  title: {$titulo}\n";

	$desc = '(sin meta description)';
	if ( preg_match_all( '#<meta\b[^>]*>#i', $html, $metas ) ) {
		foreach ( $metas[0] as $tag ) {
			if ( preg_match( '#\bname\s*=\s*["\']description["\']#i', $tag ) && preg_match( '#\bcontent\s*=\s*(["\'])(.*?)\1#is', $tag, $c ) ) {
				$desc = trim( html_entity_decode( $c[2], ENT_QUOTES, 'UTF-8' ) );
				break;
			}
		}
	}
	echo "  description: {$desc}\n";
	echo '  h1: ' . preg_match_all( '#<h1\b#i', $html ) . "\n";

	$tipos = [];
	if ( preg_match_all( '#<script[^>]+type\s*=\s*["\']application/ld\+json["\'][^>]*>(.*?)</script>#is', $html, $js ) ) {
		$recoger = function ( $nodo ) use ( &$recoger, &$tipos ) {
			if ( ! is_array( $nodo ) ) {
				return;
			}
			if ( isset( $nodo['@type'] ) ) {
				foreach ( (array) $nodo['@type'] as $t ) {
					$tipos[] = (string) $t;
				}
			}
			foreach ( $nodo as $v ) {
				if ( is_array( $v ) ) {
					$recoger( $v );
				}
			}
		};
		foreach ( $js[1] as $bloque ) {
			$d = json_decode( trim( $bloque ), true );
			if ( null === $d ) {
				$tipos[] = '(JSON-LD que no parsea)';
				continue;
			}
			$recoger( $d );
		}
	}
	echo '  json-ld: ' . ( $tipos ? implode( ', ', array_unique( $tipos ) ) : '(ninguno)' ) . "\n";

	if ( $cadenas ) {
		$plano = mb_strtolower( html_entity_decode( $html, ENT_QUOTES, 'UTF-8' ) );
		foreach ( $cadenas as $cad ) {
			echo '  [' . ( false !== mb_strpos( $plano, mb_strtolower( $cad ) ) ? 'presente' : 'ausente ' ) . "] {$cad}\n";
		}
	}
}

echo "\nLeídas: {$total}; con error o 4xx/5xx: {$fallos}. No se escribió nada.\n";
