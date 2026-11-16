<?php
/**
 * SOLO LECTURA (16/11/2026), paso 0 del plan del dorado y el surubí: inventario de todas las páginas publicadas que tratan
 * dorado o surubí (notas con esa especie o que lo nombran, fichas de especie, guías con esa especie), en es/en/pt.
 * Por página: tipo, idioma, clave, URL, título SEO, H1, palabras, foto, enlaces internos salientes y entrantes (contados
 * sobre el contenido de todas las notas, guías, especies y próximos publicados), idiomas que faltan de su clave.
 * No escribe nada. Correr con wp-script.yml (script = inventario-dorado-surubi-2026-11-16).
 */
$tipos   = [ 'pm_nota', 'pm_especie', 'pm_destino', 'pm_proximo', 'pm_entrevista' ];
$todos   = [];
foreach ( $tipos as $t ) {
	foreach ( get_posts( [ 'post_type' => $t, 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true ] ) as $p ) {
		$todos[ $p->ID ] = $p;
	}
}
$home = untrailingslashit( home_url() );
$norm = fn( string $u ) => trailingslashit( strtolower( strtok( preg_replace( '#^https?://(www\.)?#i', '', $u ), '#?' ) ) );

// Enlaces salientes internos por página (URL normalizada).
$sal = [];
foreach ( $todos as $id => $p ) {
	preg_match_all( '#href=["\']([^"\']+)["\']#i', (string) $p->post_content, $m );
	$set = [];
	foreach ( $m[1] as $h ) {
		if ( 0 === strpos( $h, '/' ) ) {
			$h = $home . $h;
		}
		if ( false !== stripos( $h, preg_replace( '#^https?://#', '', $home ) ) ) {
			$set[ $norm( $h ) ] = true;
		}
	}
	$sal[ $id ] = array_keys( $set );
}
$url_de = [];
foreach ( $todos as $id => $p ) {
	$url_de[ $id ] = $norm( get_permalink( $id ) );
}

$dor = '/dorado|surub[ií]|golden|pintado|pir[aá]|pacú|salminus|pseudoplatystoma/iu';
$sel = [];
foreach ( $todos as $id => $p ) {
	$tipo = $p->post_type;
	$fid  = $p->ID;
	$es   = 'es' === pm_idioma_de( $id ) ? $p : pm_buscar( $tipo, pm_clave_de( $id ), 'es' );
	$fid  = $es ? $es->ID : $id;
	$txt  = wp_strip_all_tags( $p->post_title . ' ' . $p->post_content );
	$n    = preg_match_all( $dor, $txt );
	$ok   = false;
	if ( 'pm_especie' === $tipo ) {
		$ok = in_array( pm_clave_de( $id ), [ 'dorado', 'surubi' ], true );
	} elseif ( 'pm_nota' === $tipo ) {
		$ok = in_array( (string) pm_campo( 'nota_especie', $fid ), [ 'dorado', 'surubi' ], true ) || $n >= 4;
	} elseif ( 'pm_destino' === $tipo ) {
		$esp = (array) ( pm_campo( 'especies', $fid ) ?: [] );
		$ok  = (bool) array_intersect( $esp, [ 'dorado', 'surubi' ] );
	} else {
		$ok = $n >= 3;
	}
	if ( $ok ) {
		$sel[] = $id;
	}
}

$mapa_tipo = [ 'pm_nota' => 'nota', 'pm_especie' => 'especie', 'pm_destino' => 'guía', 'pm_proximo' => 'próximo', 'pm_entrevista' => 'entrevista' ];
WP_CLI::log( 'INVENTARIO: ' . count( $sel ) . ' páginas (de ' . count( $todos ) . ' publicadas). Columnas: tipo | idioma | clave | URL | SEO title (largo) | H1 | palabras | foto | salientes | entrantes | idiomas que faltan | menciones dorado/surubí' );
usort( $sel, fn( $a, $b ) => [ $todos[ $a ]->post_type, pm_clave_de( $a ), pm_idioma_de( $a ) ] <=> [ $todos[ $b ]->post_type, pm_clave_de( $b ), pm_idioma_de( $b ) ] );
foreach ( $sel as $id ) {
	$p     = $todos[ $id ];
	$tipo  = $p->post_type;
	$i     = pm_idioma_de( $id );
	$clave = pm_clave_de( $id );
	$seo   = (string) pm_campo( 'seo_title', $id );
	$h1    = 'pm_destino' === $tipo ? ( (string) pm_campo( 'h1', $id ) ?: $p->post_title ) : $p->post_title;
	$es    = 'es' === $i ? $p : pm_buscar( $tipo, $clave, 'es' );
	$foto  = $es ? (int) get_post_thumbnail_id( $es->ID ) : 0;
	$ent   = 0;
	$entl  = [];
	foreach ( $todos as $oid => $o ) {
		if ( $oid !== $id && in_array( $url_de[ $id ], $sal[ $oid ], true ) ) {
			++$ent;
			$entl[] = pm_clave_de( $oid ) . ':' . pm_idioma_de( $oid );
		}
	}
	$falta = [];
	foreach ( [ 'es', 'en', 'pt' ] as $x ) {
		if ( ! pm_buscar( $tipo, $clave, $x ) ) {
			$falta[] = $x;
		}
	}
	$sal_int = array_values( array_filter( $sal[ $id ], fn( $u ) => $u !== $url_de[ $id ] ) );
	$slugs   = array_map( fn( $u ) => trim( str_replace( strtolower( preg_replace( '#^https?://(www\.)?#', '', $home ) ), '', $u ), '/' ), $sal_int );
	WP_CLI::log( implode( ' | ', [
		$mapa_tipo[ $tipo ] ?? $tipo, $i, $clave, get_permalink( $id ),
		$seo ? $seo . ' (' . mb_strlen( $seo ) . ')' : '(sin SEO title)', $h1,
		str_word_count( wp_strip_all_tags( $p->post_content ) ), $foto && wp_attachment_is_image( $foto ) ? 'foto #' . $foto : 'SIN FOTO',
		count( $sal_int ), $ent, $falta ? implode( ',', $falta ) : '-',
		preg_match_all( $dor, wp_strip_all_tags( $p->post_content ) ),
	] ) );
	WP_CLI::log( '    SALEN: ' . ( $slugs ? implode( ' ; ', $slugs ) : '(ninguno)' ) );
	WP_CLI::log( '    ENTRAN: ' . ( $entl ? implode( ' ; ', $entl ) : '(ninguno)' ) );
}

// Enlaces desde las plantillas: ¿la portada enlaza al pilar? (clave del pilar en ui.json)
$ui = json_decode( (string) @file_get_contents( WP_PLUGIN_DIR . '/pescamapa-core/data/ui.json' ), true );
WP_CLI::log( 'ui.json legible: ' . ( is_array( $ui ) ? 'sí' : 'no' ) );
$pilar = pm_buscar( 'pm_nota', 'golden-dorado-guia', 'es' );
WP_CLI::log( 'pilar es: ' . ( $pilar ? get_permalink( $pilar->ID ) : 'no existe' ) );
WP_CLI::success( 'Inventario terminado (solo lectura).' );
