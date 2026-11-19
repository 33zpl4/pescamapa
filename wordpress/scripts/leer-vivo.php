<?php
/**
 * Lector en vivo, SOLO LECTURA (17/11/2026). Reemplazo barato de investigar.yml mientras no hay minutos de Actions:
 * corre en el servidor (wp-script.yml, ~1 min) y pide cada URL con wp_remote_get. No escribe nada: ni posts, ni opciones, ni archivos.
 *
 * Entrada: wordpress/scripts/leer-vivo-urls.txt, una por línea. Formatos:
 *   https://sitio/pagina/
 *   https://sitio/pagina/ | cadena uno ; cadena dos     (dice presente/ausente por cada cadena, sin distinguir mayúsculas)
 *   https://sitio/pagina/ | cadena ; cadena || contexto: palabra1 ; palabra2   (directiva opcional tras `||`: por cada palabra, hasta 3 extractos de ±250 caracteres de TEXTO visible, sin etiquetas, sin distinguir mayúsculas ni tildes)
 *   https://sitio/pagina/ || contexto: palabra1 ; palabra2                      (solo extractos, sin cadenas presente/ausente)
 *   # comentario (y líneas vacías) se ignoran.
 * Con `contexto:` además imprime la fecha de publicación si la encuentra (meta article:published_time, <time datetime>, JSON-LD datePublished/dateModified, dd/mm/aaaa al comienzo del texto) y los 600 primeros caracteres del <main>/<article>.
 * Se leen como máximo 1,5 MB por página, timeout 15 s.
 * wp-script.yml copia ese txt al servidor junto con el script (/tmp/pm-script-urls.txt).
 * Por URL imprime: status, redirección final, <title>, meta description, nº de h1, tipos de JSON-LD y las cadenas pedidas.
 * Al final imprime el registro de PHP (últimas 20 entradas de la última hora del tema o pescamapa-core; ver docs/wordpress.md).
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

// Texto visible: sin script/style/comentarios/etiquetas, entidades decodificadas, espacios colapsados.
$pm_texto = function ( $html ) {
	$html = preg_replace( '#<!--.*?-->#s', ' ', $html );
	$html = preg_replace( '#<(script|style|noscript|svg|template)\b.*?</\1\s*>#is', ' ', $html );
	$html = preg_replace( '#<[^>]*>#', ' ', $html );
	$html = html_entity_decode( $html, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return trim( preg_replace( '/\s+/u', ' ', $html ) );
};
// Normaliza (minúsculas, sin tildes) conservando el nº de caracteres, para usar los mismos desplazamientos en el original.
$pm_norm = function ( $t ) {
	$out = '';
	foreach ( preg_split( '//u', $t, -1, PREG_SPLIT_NO_EMPTY ) as $ch ) {
		$n = remove_accents( $ch );
		$n = mb_strtolower( $n );
		$out .= ( 1 === mb_strlen( $n ) ) ? $n : $ch;
	}
	return $out;
};

foreach ( $lineas as $linea ) {
	$linea = trim( $linea );
	if ( '' === $linea || '#' === $linea[0] ) {
		continue;
	}
	$contexto = [];
	if ( false !== strpos( $linea, '||' ) ) {
		list( $linea, $dir ) = array_map( 'trim', explode( '||', $linea, 2 ) );
		$dir      = preg_replace( '/^contexto\s*:/iu', '', $dir );
		$contexto = array_values( array_filter( array_map( 'trim', explode( ';', $dir ) ), 'strlen' ) );
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
	$r = wp_remote_get( $url, [ 'timeout' => 15, 'redirection' => 5, 'limit_response_size' => 1500000, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)', 'sslverify' => true ] );
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
	// Always: a PHP warning/notice printed into the page (19/11/2026: one reached the home page unseen).
	if ( preg_match( '#<b>(Warning|Notice|Deprecated|Fatal error|Parse error)</b>:\s*([^<]*)#', $html, $err ) ) {
		echo '  ALERTA PHP: ' . $err[1] . ': ' . trim( $err[2] ) . "\n";
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

	if ( $contexto ) {
		$visible = $pm_texto( $html );
		// Fecha de publicación.
		$fechas = [];
		if ( preg_match_all( '#<meta\b[^>]*>#i', $html, $metas2 ) ) {
			foreach ( $metas2[0] as $tag ) {
				if ( preg_match( '#\b(?:property|name|itemprop)\s*=\s*["\']([^"\']*(?:published_time|modified_time|datePublished|dateModified|article:published|pubdate|date)[^"\']*)["\']#i', $tag, $k ) && preg_match( '#\bcontent\s*=\s*(["\'])(.*?)\1#is', $tag, $v ) ) {
					$fechas[] = "meta {$k[1]}: " . trim( $v[2] );
				}
			}
		}
		if ( preg_match_all( '#<time\b[^>]*\bdatetime\s*=\s*["\']([^"\']+)["\']#i', $html, $tm ) ) {
			foreach ( array_slice( array_unique( $tm[1] ), 0, 3 ) as $d ) {
				$fechas[] = "<time datetime>: {$d}";
			}
		}
		if ( preg_match_all( '#"(datePublished|dateModified)"\s*:\s*"([^"]+)"#i', $html, $jd, PREG_SET_ORDER ) ) {
			foreach ( array_slice( $jd, 0, 4 ) as $d ) {
				$fechas[] = "JSON-LD {$d[1]}: {$d[2]}";
			}
		}
		if ( preg_match_all( '#\b\d{1,2}[/.-]\d{1,2}[/.-](?:19|20)\d{2}\b#', mb_substr( $visible, 0, 3000 ), $dd ) ) {
			foreach ( array_slice( array_unique( $dd[0] ), 0, 3 ) as $d ) {
				$fechas[] = "texto (encabezado): {$d}";
			}
		}
		echo '  fecha: ' . ( $fechas ? implode( ' | ', array_unique( $fechas ) ) : '(no encontrada)' ) . "\n";
		// Primeros 600 caracteres de <main>/<article>.
		$principal = '';
		if ( preg_match( '#<main\b[^>]*>(.*?)</main>#is', $html, $mm ) || preg_match( '#<article\b[^>]*>(.*?)</article>#is', $html, $mm ) ) {
			$principal = mb_substr( $pm_texto( $mm[1] ), 0, 600 );
		}
		echo '  main/article: ' . ( '' !== $principal ? $principal : '(no hay <main> ni <article>)' ) . "\n";
		echo '  texto visible: ' . mb_strlen( $visible ) . " caracteres\n";
		$vn = $pm_norm( $visible );
		foreach ( $contexto as $pal ) {
			$pn  = $pm_norm( $pal );
			$pos = 0;
			$n   = 0;
			$ult = -1000;
			echo "  contexto «{$pal}»:\n";
			while ( $n < 3 && false !== ( $i = mb_strpos( $vn, $pn, $pos ) ) ) {
				$pos = $i + max( 1, mb_strlen( $pn ) );
				if ( $i - $ult < 250 ) {
					continue; // coincidencia dentro del extracto anterior
				}
				$ult = $i;
				$n++;
				$ini = max( 0, $i - 250 );
				echo "    {$n}) …" . mb_substr( $visible, $ini, ( $i - $ini ) + mb_strlen( $pn ) + 250 ) . "…\n";
			}
			if ( 0 === $n ) {
				echo "    (sin coincidencias)\n";
			}
		}
	}
}

// Registro de PHP (19/11/2026): últimas 20 entradas de la última hora que mencionen el tema o pescamapa-core. Solo lectura.
// El registro es wp-content/pm-logs/errores-php.php (PM_ERROR_LOG; su primer renglón es la cabecera de salida, que aquí se salta). Si no se puede leer, lo dice.
echo "\n== Registro de PHP (última hora, tema y pescamapa-core)\n";
$pm_log = defined( 'PM_ERROR_LOG' ) ? PM_ERROR_LOG : '';
$pm_est = get_option( 'pm_registro_estado', '(sin dato)' );
echo '  estado del registro (opción pm_registro_estado): ' . $pm_est . "\n";
if ( 0 !== strpos( (string) $pm_est, 'ok' ) ) {
	echo "  ALERTA: el plugin no pudo preparar el registro\n";
	$fallos++;
}
if ( '' === $pm_log || ! is_file( $pm_log ) || ! is_readable( $pm_log ) ) {
	echo '  (registro no legible o todavía no existe: ' . ( '' === $pm_log ? 'PM_ERROR_LOG sin definir' : preg_replace( '#^.*?(wp-content/)#', '$1', $pm_log ) ) . ")\n";
} else {
	$tam   = filesize( $pm_log );
	$cola  = (string) file_get_contents( $pm_log, false, null, max( 0, $tam - 524288 ) );
	$desde = time() - 3600;
	$hits  = [];
	foreach ( preg_split( '/\R/', $cola ) as $l ) {
		if ( ! preg_match( '#^\[(\d{2}-\w{3}-\d{4} \d{2}:\d{2}:\d{2}) UTC\] PHP (Warning|Fatal error|Notice|Deprecated|Parse error):\s*(.*)$#', $l, $m ) ) {
			continue;
		}
		if ( ! preg_match( '#wp-content/(themes/pescamapa|plugins/pescamapa-core)/#', $m[3] ) ) {
			continue;
		}
		if ( false !== strpos( $m[3], 'Pescamapa autoprueba controlada' ) ) {
			continue; // aviso provocado a propósito por autoprueba-errores: no cuenta como error
		}
		$t = strtotime( $m[1] . ' UTC' );
		if ( $t && $t < $desde ) {
			continue;
		}
		// Sin la ruta del servidor: desde wp-content en adelante.
		$hits[] = '  [' . $m[1] . ' UTC] ' . $m[2] . ': ' . preg_replace( '#/[^\s]*?(wp-content/)#', '$1', $m[3] );
	}
	echo '  ' . preg_replace( '#^.*?(wp-content/)#', '$1', $pm_log ) . ' (' . $tam . " bytes). Entradas del tema o del plugin en la última hora: " . count( $hits ) . "\n";
	foreach ( array_slice( $hits, -20 ) as $h ) {
		echo mb_substr( $h, 0, 400 ) . "\n";
	}
	if ( $hits ) {
		$fallos++;
	}
}

echo "\nLeídas: {$total}; con error o 4xx/5xx: {$fallos}. No se escribió nada.\n";
