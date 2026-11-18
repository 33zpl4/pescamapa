<?php
/**
 * Notas: completa 'nota_destino' (18/11/2026). El formulario de pedido (vistas/viaje.php) lee ?destino=<clave>&especie=<clave>; el botón de la nota
 * (vistas/nota.php, clase cta-nota) y la barra móvil arman ese enlace con pm_nota()['destino'], que sale de la fila EN ESPAÑOL (en/pt la heredan por
 * clave y la clave de destino es la misma en los tres idiomas: viaje.php valida contra pm_destinos_lista($idioma), que es por clave).
 * Simulacro: lista TODAS las notas (es/en/pt) con destino y especie actuales y las guías que su cuerpo enlaza o nombra. Aplicar: escribe SOLO lo
 * de $asignar (nota única de UN destino con guía propia publicada y campo vacío); lo demás queda vacío a propósito.
 * Solo toca el campo 'nota_destino' de la fila es; lee de vuelta (VERIFIED por nota). Idempotente. Sin enlaces nuevos.
 * Con wp-script.yml (script = notas-destino-2026-11-18; aplicar=true para la corrida real).
 */

$aplicar = false;

// clave de la nota => clave de destino. Solo notas de UN destino, con guía publicada, y campo vacío.
$asignar = [
	'la-zona-salto-grande' => 'concordia',
];

$destinos = []; // clave => [ idioma => post ]
foreach ( get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true ] ) as $p ) {
	$destinos[ pm_clave_de( $p->ID ) ][ pm_idioma_de( $p->ID ) ] = $p;
}
$por_ruta = [];
$nombres  = [];
foreach ( $destinos as $c => $por_idioma ) {
	foreach ( $por_idioma as $i => $p ) {
		$por_ruta[ trailingslashit( wp_parse_url( get_permalink( $p->ID ), PHP_URL_PATH ) ) ] = $c;
		$nombres[ $c ][] = trim( (string) get_the_title( $p->ID ) );
	}
}
WP_CLI::log( 'guías publicadas (clave: idiomas): ' . implode( ', ', array_map( fn( $c ) => $c . ':' . implode( '/', array_keys( $destinos[ $c ] ) ), array_keys( $destinos ) ) ) );
// El select del formulario usa pm_destinos_lista(): las claves que acepta viaje.php.
$aceptan = array_column( pm_destinos_lista( 'es' ), 'clave' );
foreach ( [ 'en', 'pt' ] as $i ) {
	$otro = array_column( pm_destinos_lista( $i ), 'clave' );
	WP_CLI::log( "claves del select {$i}: " . count( $otro ) . ( array_diff( $aceptan, $otro ) || array_diff( $otro, $aceptan ) ? ' DIFIEREN de es' : ' (iguales a es)' ) );
}
WP_CLI::log( 'claves del select es (' . count( $aceptan ) . '): ' . implode( ', ', $aceptan ) );

$claves_guia = function ( string $html ) use ( $por_ruta ): array {
	$out = [];
	if ( preg_match_all( '/href="([^"]+)"/', $html, $m ) ) {
		foreach ( $m[1] as $h ) {
			$ruta = trailingslashit( wp_parse_url( $h, PHP_URL_PATH ) ?: '' );
			if ( isset( $por_ruta[ $ruta ] ) ) {
				$out[ $por_ruta[ $ruta ] ] = ( $out[ $por_ruta[ $ruta ] ] ?? 0 ) + 1;
			} elseif ( preg_match( '#/(?:guias-de-pesca|fishing-guides)/([a-z0-9-]+)/$#', $ruta, $g ) ) {
				$out[ 'ciudad:' . $g[1] ] = ( $out[ 'ciudad:' . $g[1] ] ?? 0 ) + 1;
			}
		}
	}
	arsort( $out );
	return $out;
};
$nombrados = function ( string $html ) use ( $nombres ): array {
	$txt = mb_strtolower( wp_strip_all_tags( $html ) );
	$out = [];
	foreach ( $nombres as $c => $lista ) {
		$n = 0;
		foreach ( array_unique( $lista ) as $nom ) {
			if ( mb_strlen( $nom ) >= 4 ) {
				$n += substr_count( $txt, mb_strtolower( $nom ) );
			}
		}
		if ( $n ) {
			$out[ $c ] = $n;
		}
	}
	arsort( $out );
	return $out;
};
$fmt = fn( array $a ) => $a ? implode( ', ', array_map( fn( $k, $v ) => "{$k}×{$v}", array_keys( $a ), $a ) ) : '-';

$posts = get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true, 'orderby' => 'ID', 'order' => 'ASC' ] );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': notas publicadas (filas): ' . count( $posts ) );
$por_clave = [];
foreach ( $posts as $p ) {
	$i   = pm_idioma_de( $p->ID );
	$c   = pm_clave_de( $p->ID );
	$por_clave[ $c ][ $i ] = $p;
	$es  = 'es' === $i ? $p : pm_buscar( 'pm_nota', $c, 'es' );
	$fid = $es ? $es->ID : $p->ID;
	WP_CLI::log( sprintf( '#%d %s [%s] «%s» | tipo=%s destino=%s especie=%s | enlaza: %s | nombra: %s', $p->ID, $c, $i, get_the_title( $p->ID ), pm_campo( 'nota_tipo', $fid ), pm_campo( 'nota_destino', $fid ) ?: '(vacío)', pm_campo( 'nota_especie', $fid ) ?: '(vacía)', $fmt( $claves_guia( $p->post_content ) ), $fmt( array_slice( $nombrados( $p->post_content ), 0, 6, true ) ) ) );
}

// ---- Plan -----------------------------------------------------------------------------------------------------------------------------------
$plan      = [];
$hay_falta = false;
foreach ( $asignar as $nota => $dest ) {
	$es = $por_clave[ $nota ]['es'] ?? null;
	if ( ! $es ) {
		WP_CLI::log( "falta: la nota {$nota} no existe en es" );
		$hay_falta = true;
		continue;
	}
	if ( ! in_array( $dest, $aceptan, true ) || ! isset( $destinos[ $dest ]['es'] ) ) {
		WP_CLI::log( "falta: {$dest} no está en el select ni tiene guía en es" );
		$hay_falta = true;
		continue;
	}
	$hoy = (string) pm_campo( 'nota_destino', $es->ID );
	if ( $dest === $hoy ) {
		WP_CLI::log( "ya está: {$nota} #{$es->ID} nota_destino={$hoy}" );
		$plan[ $nota ] = [ 'id' => $es->ID, 'dest' => $dest, 'ya' => true ];
	} elseif ( '' !== $hoy ) {
		WP_CLI::log( "falta: {$nota} ya tiene destino «{$hoy}»: no se pisa" );
		$hay_falta = true;
	} else {
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$nota} #{$es->ID} nota_destino '' -> {$dest}" );
		$plan[ $nota ] = [ 'id' => $es->ID, 'dest' => $dest, 'ya' => false ];
	}
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' . ( $hay_falta ? ' (HAY «falta»: revisar antes de aplicar)' : '' ) );
	return;
}
$hay_falta && WP_CLI::error( 'Hay «falta»: no escribo nada.' );

// ---- Apply ----------------------------------------------------------------------------------------------------------------------------------
foreach ( $plan as $nota => $x ) {
	if ( ! $x['ya'] ) {
		update_field( 'nota_destino', $x['dest'], $x['id'] );
		usleep( 600000 );
	}
}
foreach ( $plan as $nota => $x ) {
	clean_post_cache( $x['id'] );
	$n  = pm_nota( $x['id'] );
	$ok = $x['dest'] === $n['destino'];
	foreach ( [ 'en', 'pt' ] as $i ) {
		$h  = $por_clave[ $nota ][ $i ] ?? null;
		$ok = $ok && $h && $x['dest'] === pm_nota( $h->ID )['destino'];
	}
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": {$nota} nota_destino={$n['destino']} (es; en y pt lo leen por clave) especie={$n['especie']}" );
}
