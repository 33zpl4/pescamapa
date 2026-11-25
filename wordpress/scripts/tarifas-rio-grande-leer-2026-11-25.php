<?php
/**
 * SOLO LECTURA (25/11/2026). Antes de corregir la cifra de la semana de trucha de mar en el río Grande (script tarifas-rio-grande-2026-11-25):
 * por cada nota y guía, en cada idioma, imprime las frases y filas de tabla del contenido que llevan 12.950, 14.950 o 13.950 (también con coma),
 * y los campos seo_title, seo_description, bajada, nota_datos, nota_origen y fuentes que la llevan. No escribe nada.
 * Uso: wp-script.yml, script = tarifas-rio-grande-leer-2026-11-25.
 */
$re = '/1[234][.,]950/u';
$q  = new WP_Query( [ 'post_type' => [ 'pm_nota', 'pm_destino' ], 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids', 'no_found_rows' => true ] );
$n  = 0;
foreach ( $q->posts as $id ) {
	$clave = pm_clave_de( $id );
	$i     = pm_idioma_de( $id );
	$tipo  = get_post_type( $id );
	$hits  = [];
	$c     = (string) get_post_field( 'post_content', $id );
	if ( preg_match( $re, $c ) ) {
		// Table rows, list items, paragraphs, headings: one line each.
		preg_match_all( '#<(tr|li|p|h[1-6])\b[^>]*>(.*?)</\1>#su', $c, $m );
		foreach ( $m[2] as $k => $frag ) {
			$t = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $frag ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
			if ( preg_match( $re, $t ) ) {
				$hits[] = [ 'contenido/' . $m[1][ $k ], $t ];
			}
		}
	}
	foreach ( [ 'seo_title', 'seo_description', 'bajada', 'nota_origen', 'precio', 'precio_en', 'precio_pt' ] as $f ) {
		$v = (string) get_post_meta( $id, $f, true );
		if ( preg_match( $re, $v ) ) {
			$hits[] = [ $f, $v ];
		}
	}
	foreach ( [ 'nota_datos', 'fuentes' ] as $f ) {
		foreach ( (array) pm_filas( $f, $id ) as $k => $fila ) {
			$v = wp_json_encode( $fila, JSON_UNESCAPED_UNICODE );
			if ( preg_match( $re, $v ) ) {
				$hits[] = [ "{$f}[{$k}]", $v ];
			}
		}
	}
	if ( ! $hits ) {
		continue;
	}
	$n++;
	WP_CLI::log( "== $tipo $clave ($i) #$id actualizado=" . get_post_meta( $id, 'actualizado', true ) . ' nota_fecha=' . get_post_meta( $id, 'nota_fecha', true ) . ' status=' . get_post_status( $id ) );
	foreach ( $hits as [ $donde, $t ] ) {
		WP_CLI::log( "   [$donde] $t" );
	}
}
WP_CLI::log( "VERIFIED: $n posts con la cifra (solo lectura, nada escrito)." );
