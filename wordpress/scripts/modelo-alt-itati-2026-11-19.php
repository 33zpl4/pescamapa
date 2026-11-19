<?php
/**
 * 19/11/2026 (decisiones del dueño, puntos 4 y 5). Dos cosas, un solo wp-script:
 *
 *  A. Alt de las fotos de Goya y de Esquina (fotos de socios sin descripción; el dueño dijo qué muestran):
 *     - Goya, adjunto «Experiencia Isoro (1)» (también en la nota de la Fiesta del Surubí): «Lancha de pesca con un guía en un riacho de Goya».
 *     - Esquina, adjunto «DSC6941» (título «Esquina»): «Dos botes de pesca en la orilla del río, en Esquina».
 *     Se escribe en _pm_alt_es/en/pt y en _wp_attachment_image_alt, y ESTE caso sí pisa: solo si el valor está vacío o es el respaldo
 *     «Nombre, Provincia» (nunca un alt escrito a mano). Los adjuntos se buscan por la foto destacada de la guía (todas las ediciones),
 *     de la nota de la Fiesta del Surubí y por nombre de archivo/título, y solo se tocan si el archivo o el título coinciden
 *     (isoro / dsc6941); si no coinciden se listan y no se escribe nada.
 *  B. Meta description de la guía de Itatí (es/en/pt): «lo que publica cada operador» pasa a «lo que publica cada uno» (≤160 caracteres).
 *
 * Idempotente. Simulacro por defecto ($aplicar = false; wp-script.yml aplicar=true lo cambia en la copia del servidor). Solo escribe
 * post meta y el campo seo_description; imprime antes/después completos y relee (VERIFIED / FALLÓ). Sin datos personales.
 */

$aplicar = false;

$fotos = [
	'goya'    => [
		'patron' => '/experiencia[-_ ]?isoro/i',
		'texto'  => [
			'es' => 'Lancha de pesca con un guía en un riacho de Goya',
			'en' => 'Fishing boat with a guide on a backwater channel in Goya',
			'pt' => 'Lancha de pesca com um guia em um riacho em Goya',
		],
		'respaldo' => '/^Goya(,\s*[^,]+)?$/iu',
	],
	'esquina' => [
		'patron' => '/dsc6941/i',
		'texto'  => [
			'es' => 'Dos botes de pesca en la orilla del río, en Esquina',
			'en' => 'Two fishing boats on the riverbank in Esquina',
			'pt' => 'Dois botes de pesca na margem do rio, em Esquina',
		],
		'respaldo' => '/^Esquina(,\s*[^,]+)?$/iu',
	],
];

$meta_itati = [
	'es' => [ 'lo que publica cada operador', 'lo que publica cada uno' ],
	'en' => [ 'as each operator publishes them', 'as each one publishes them' ],
	'pt' => [ 'o que cada operador publica', 'o que cada um publica' ],
];

$modo = $aplicar ? 'APLICA' : 'SIMULACRO';
WP_CLI::log( $modo );

// ---------- A. Alt de Goya y Esquina
$cand = [];
foreach ( array_keys( $fotos ) as $clave ) {
	foreach ( get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		$att = (int) get_post_thumbnail_id( $p->ID );
		if ( $att ) {
			$cand[ $clave ][ $att ][] = 'destino:' . $clave . '[' . pm_idioma_de( $p->ID ) . ']';
		}
	}
}
// La foto de Goya también ilustra la nota de la Fiesta del Surubí y otras: se buscan los adjuntos por nombre.
foreach ( get_posts( [ 'post_type' => 'attachment', 'post_status' => 'any', 'numberposts' => -1, 'post_mime_type' => 'image', 'orderby' => 'ID', 'order' => 'ASC' ] ) as $a ) {
	$f = (string) get_post_meta( $a->ID, '_wp_attached_file', true );
	foreach ( $fotos as $clave => $c ) {
		if ( preg_match( $c['patron'], basename( $f ) ) || preg_match( $c['patron'], (string) $a->post_title ) ) {
			$cand[ $clave ][ $a->ID ][] = 'adjunto por nombre';
		}
	}
}
foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'numberposts' => -1 ] ) as $n ) {
	$att = (int) get_post_thumbnail_id( $n->ID );
	if ( $att && isset( $cand['goya'][ $att ] ) ) {
		$cand['goya'][ $att ][] = 'nota:' . pm_clave_de( $n->ID ) . '[' . pm_idioma_de( $n->ID ) . ']';
	}
}

$escritos = [];
foreach ( $fotos as $clave => $c ) {
	foreach ( $cand[ $clave ] ?? [] as $att => $usos ) {
		$f = (string) get_post_meta( $att, '_wp_attached_file', true );
		$t = (string) get_post( $att )->post_title;
		WP_CLI::log( "#$att ($clave) archivo=$f | título «$t» | usos: " . implode( ', ', $usos ) );
		if ( ! preg_match( $c['patron'], basename( $f ) ) && ! preg_match( $c['patron'], $t ) ) {
			WP_CLI::log( '   NO COINCIDE con la foto indicada por el dueño: no se toca' );
			continue;
		}
		$cambios = [];
		foreach ( [ 'es', 'en', 'pt' ] as $l ) {
			$v = trim( (string) get_post_meta( $att, "_pm_alt_$l", true ) );
			WP_CLI::log( "   ANTES _pm_alt_$l=«$v»" );
			if ( '' === $v || preg_match( $c['respaldo'], $v ) ) {
				$cambios[ "_pm_alt_$l" ] = $c['texto'][ $l ];
			} else {
				WP_CLI::log( "   _pm_alt_$l ya tiene un texto propio: no se pisa" );
			}
		}
		$w = trim( (string) get_post_meta( $att, '_wp_attachment_image_alt', true ) );
		WP_CLI::log( "   ANTES _wp_attachment_image_alt=«$w»" );
		if ( '' === $w || preg_match( $c['respaldo'], $w ) ) {
			$cambios['_wp_attachment_image_alt'] = $c['texto']['es'];
		} else {
			WP_CLI::log( '   _wp_attachment_image_alt ya tiene un texto propio: no se pisa' );
		}
		foreach ( $cambios as $k => $v ) {
			WP_CLI::log( "   DESPUÉS $k=«$v»" );
		}
		if ( $aplicar ) {
			foreach ( $cambios as $k => $v ) {
				update_post_meta( $att, $k, $v );
			}
		}
		$escritos[ $att ] = $cambios;
	}
	if ( empty( $cand[ $clave ] ) ) {
		WP_CLI::log( "AVISO: no se encontró ningún adjunto para $clave" );
	}
}
if ( $aplicar ) {
	foreach ( $escritos as $att => $cambios ) {
		foreach ( $cambios as $k => $v ) {
			$ok = (string) get_post_meta( $att, $k, true ) === $v;
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #$att $k=«$v»" );
			$ok || WP_CLI::error( "#$att $k: la lectura de vuelta no coincide." );
		}
	}
}

// ---------- B. Meta description de Itatí
foreach ( get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'itati', 'numberposts' => -1 ] ) as $p ) {
	$l = pm_idioma_de( $p->ID );
	$v = (string) pm_campo( 'seo_description', $p->ID );
	WP_CLI::log( "Itatí [$l] #{$p->ID} seo_description ANTES: $v" );
	[ $viejo, $nuevo ] = $meta_itati[ $l ] ?? [ '', '' ];
	if ( '' === $viejo || false === strpos( $v, $viejo ) ) {
		WP_CLI::log( '   sin la frase vieja: nada que cambiar' );
		continue;
	}
	$d = str_replace( $viejo, $nuevo, $v );
	if ( mb_strlen( $d ) > 160 ) {
		WP_CLI::error( "Itatí [$l]: la meta quedaría de " . mb_strlen( $d ) . ' caracteres (máx. 160).' );
	}
	WP_CLI::log( "   DESPUÉS (" . mb_strlen( $d ) . " caracteres): $d" );
	if ( $aplicar ) {
		update_field( 'seo_description', $d, $p->ID );
		$ok = (string) pm_campo( 'seo_description', $p->ID ) === $d && false === strpos( (string) pm_campo( 'seo_description', $p->ID ), 'operador' );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": Itatí [$l] seo_description" );
		$ok || WP_CLI::error( "Itatí [$l]: la lectura de vuelta no coincide." );
	}
}
WP_CLI::success( $aplicar ? 'Aplicado' : 'Simulacro terminado: no se escribió nada' );
