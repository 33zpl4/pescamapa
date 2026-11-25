<?php
/**
 * El servicio, dicho en las notas de precios (25/11/2026). es + en + pt, por clave.
 * Por qué: amigos del dueño le preguntaron a ChatGPT, Gemini y Claude cómo organizar un viaje de pesca a la Argentina y pedir presupuestos. ChatGPT cita a
 * Pescamapa como fuente de PRECIOS, pero cuando contesta «cómo consigo presupuestos» manda a escribir a 4 o 5 lodges con una lista de preguntas: justo lo que
 * hacemos por el visitante, gratis, y las páginas que lee no lo decían claro. Regla del dueño (25/11/2026): utilidad y UX, un mensaje claro para la persona y para los LLM.
 *
 * Qué hace, por nota e idioma (encuentra la fila por clave + idioma; solo toca post_content; no toca precios, fechas, títulos, slugs ni campos):
 *   a. UN párrafo corto (clase pm-servicio) justo después del primer párrafo («Respuesta corta»), encima de la primera tabla: qué hacemos, gratis, 24–48 h, con enlace
 *      a la página del presupuesto de ese idioma (pm_url_presupuesto).
 *   b. UNA pregunta de FAQ, PRIMERA de la lista (entra al JSON-LD FAQPage porque pm_faqs_contenido lee el grupo pm-faq): «¿Cómo consigo presupuestos de varios lodges de
 *      pesca a la vez?», con enlace al formulario. Si la nota no tiene FAQ, se agrega h2 «Preguntas frecuentes» + grupo pm-faq con esa sola pregunta, antes del cierre en cursiva.
 *   Si la nota ya lo dice bien en esa posición, no se agrega (ver $plan: el hub «como-preparar-viaje-pesca-argentina» ya tiene tres preguntas y el cuerpo; la visión general
 *   «cuanto-cuesta-viaje-pesca-argentina» ya tiene «¿Cómo comparo opciones?»: recibe solo el párrafo de arriba). Se salta toda clave marcada nota_informativa (fila es).
 * No toca `actualizado` (es la fecha de lectura de los precios, no de este texto).
 * Idempotente: el párrafo se reconoce por la clase pm-servicio y la pregunta por su texto; una segunda corrida no cambia nada (el simulacro lo comprueba corriendo la función dos veces).
 * Simulacro primero ($aplicar = false): imprime ANTES/DESPUÉS por fila. Con $aplicar = true guarda y, por fila, imprime VERIFIED leyendo de vuelta el contenido guardado y la página
 * renderizada (párrafo, enlace, primera pregunta, JSON-LD FAQPage, cta-nota intacto). Run with wp-script.yml (script = servicio-en-notas-precios-2026-11-25).
 */
$aplicar = false;

// Claves de las notas de precios y qué se agrega en cada una: a = párrafo de arriba, b = pregunta de FAQ primera.
$plan = [
	'cuanto-cuesta-viaje-pesca-argentina'  => [ 'a' => true, 'b' => false, 'motivo_b' => 'ya tiene «¿Cómo comparo opciones?» con los pasos del servicio' ],
	'precio-semana-trucha-patagonia-norte' => [ 'a' => true, 'b' => true ],
	'precio-viaje-dorado-lodge'            => [ 'a' => true, 'b' => true ],
	'viaje-dorado-amigos-precios'          => [ 'a' => true, 'b' => true ],
	'paquete-incluye-no-incluye'           => [ 'a' => true, 'b' => true ],
	'precio-pesca-bariloche-limay'         => [ 'a' => true, 'b' => true ],
	'precio-pesca-patagonia-sur'           => [ 'a' => true, 'b' => true ],
	'como-preparar-viaje-pesca-argentina'  => [ 'a' => false, 'b' => false, 'motivo_a' => 'el hub ya explica el servicio arriba y en el paso 8', 'motivo_b' => 'ya tiene tres preguntas (escribirle yo a cada lugar, uno por uno, comparar)' ],
];

// Textos. {pres} = página del presupuesto del idioma, {viaje} = formulario del idioma.
$T = [
	'es' => [
		'p'   => '<strong>¿Querés el precio para tus fechas?</strong> Decinos dónde y cuándo: <a href="{pres}">les escribimos nosotros a los lodges y guías que encajan</a> y en 24–48 horas te mandamos sus opciones para comparar, gratis para vos.',
		'q'   => '¿Cómo consigo presupuestos de varios lodges de pesca a la vez?',
		'a'   => 'Completás <a href="{viaje}">un solo formulario</a> con dónde, cuándo y con quién querés pescar, y nosotros les escribimos a los lodges y guías que encajan. En 24–48 horas te mandamos sus opciones lado a lado, con qué incluye cada una, el precio y las fechas con lugar, para que compares y elijas. Es gratis para vos y reservás directo con el lugar que elijas.',
		'h'   => 'Preguntas frecuentes',
	],
	'en' => [
		'p'   => '<strong>Want the price for your dates?</strong> Tell us where and when: <a href="{pres}">we write to the lodges and guides that fit</a> and within 24–48 hours we send you their options to compare, free for you.',
		'q'   => 'How do I get quotes from several fishing lodges at once?',
		'a'   => 'You fill in <a href="{viaje}">one form</a> with where, when and with whom you want to fish, and we write to the lodges and guides that fit. Within 24–48 hours we send you their options side by side, with what each includes, the price and the dates available, so you can compare and choose. It is free for you, and you book directly with the place you choose.',
		'h'   => 'Frequently asked questions',
	],
	'pt' => [
		'p'   => '<strong>Quer o preço para as suas datas?</strong> Diga onde e quando: <a href="{pres}">escrevemos nós às pousadas e guias que combinam</a> e, em 24–48 horas, enviamos as opções delas para você comparar, grátis para você.',
		'q'   => 'Como peço orçamento a várias pousadas de pesca de uma vez?',
		'a'   => 'Você preenche <a href="{viaje}">um só formulário</a> com onde, quando e com quem quer pescar, e nós escrevemos às pousadas e guias que combinam. Em 24–48 horas enviamos as opções delas lado a lado, com o que cada uma inclui, o preço e as datas com vaga, para você comparar e escolher. É grátis para você, e você reserva direto com o lugar que escolher.',
		'h'   => 'Perguntas frequentes',
	],
];

// ---- Helpers (copiados en cada archivo: wp-script.yml manda un solo archivo) ----
if ( ! function_exists( 'pm_sv_texto' ) ) {
	function pm_sv_texto( string $html ): string {
		return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( preg_replace( '#</(p|li|h[1-6])>#', '$0 ', $html ) ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
	}
	function pm_sv_sub( string $s, string $i, array $T ): string {
		return str_replace( [ '{pres}', '{viaje}' ], [ esc_url( pm_url_presupuesto( $i ) ), esc_url( pm_url_viaje( $i ) ) ], $s );
	}
	/** Bloque del párrafo del servicio. */
	function pm_sv_bloque_p( string $i, array $T ): string {
		return "<!-- wp:paragraph {\"className\":\"pm-servicio\"} -->\n<p class=\"pm-servicio\">" . pm_sv_sub( $T[ $i ]['p'], $i, $T ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	/** Pregunta + respuesta (h3 + párrafo), como los escribe pm_sc_faq. */
	function pm_sv_bloque_q( string $i, array $T ): string {
		return "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">" . $T[ $i ]['q'] . "</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>" . pm_sv_sub( $T[ $i ]['a'], $i, $T ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	/** Posiciones [offset, largo, texto plano] de los bloques de párrafo. */
	function pm_sv_parrafos( string $c ): array {
		preg_match_all( '#<!-- wp:paragraph[^>]*-->\s*<p[^>]*>(.*?)</p>\s*<!-- /wp:paragraph -->\s*#su', $c, $m, PREG_OFFSET_CAPTURE );
		$out = [];
		foreach ( $m[0] as $n => $b ) {
			$out[] = [ $b[1], strlen( $b[0] ), pm_sv_texto( $m[1][ $n ][0] ), $m[1][ $n ][0] ];
		}
		return $out;
	}
	/**
	 * Devuelve [ nuevo contenido, [ qué se hizo ], [ avisos ] ]. Idempotente. $quiere = [ 'a' => bool, 'b' => bool ].
	 */
	function pm_sv_aplicar( string $c, string $i, array $quiere, array $T ): array {
		$hecho  = [];
		$avisos = [];
		// a. Párrafo de arriba: después del primer párrafo («Respuesta corta»), antes de la primera tabla.
		if ( ! empty( $quiere['a'] ) ) {
			if ( false !== strpos( $c, 'class="pm-servicio"' ) ) {
				$hecho[] = 'a: ya está (no toco)';
			} else {
				$pos = null;
				foreach ( pm_sv_parrafos( $c ) as [ $o, $l, $txt ] ) {
					if ( preg_match( '/^(Respuesta corta|Short answer|Resposta curta)\b/u', $txt ) ) {
						$pos = $o + $l;
						break;
					}
				}
				$tabla = strpos( $c, '<!-- wp:table' );
				if ( null === $pos ) {
					$avisos[] = 'a: no encuentro el párrafo «Respuesta corta»: no toco';
				} elseif ( false !== $tabla && $pos > $tabla ) {
					$avisos[] = 'a: el párrafo «Respuesta corta» está después de la primera tabla: no toco';
				} else {
					$c       = substr( $c, 0, $pos ) . pm_sv_bloque_p( $i, $T ) . substr( $c, $pos );
					$hecho[] = 'a: párrafo agregado después de «Respuesta corta»';
				}
			}
		}
		// b. FAQ primera.
		if ( ! empty( $quiere['b'] ) ) {
			$q = $T[ $i ]['q'];
			if ( in_array( $q, array_column( pm_faqs_contenido( $c ), 'pregunta' ), true ) ) {
				$hecho[] = 'b: la pregunta ya está (no toco)';
			} elseif ( false !== strpos( $c, 'pm-faq' ) ) {
				$abre = '<div class="wp-block-group pm-faq">';
				if ( 1 !== substr_count( $c, $abre ) ) {
					$avisos[] = 'b: el grupo pm-faq no tiene la forma esperada: no toco';
				} else {
					$c       = str_replace( $abre, $abre . pm_sv_bloque_q( $i, $T ), $c );
					$hecho[] = 'b: pregunta agregada primera en la FAQ existente';
				}
			} else {
				$grupo = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . $T[ $i ]['h'] . "</h2>\n<!-- /wp:heading -->\n\n"
					. "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( pm_sv_bloque_q( $i, $T ) ) . "</div>\n<!-- /wp:group -->\n\n";
				// Antes del último cierre en cursiva (<em>…) si lo que viene después son solo párrafos (p. ej. «Para planificar tu viaje: …»); si no, al final.
				$par = pm_sv_parrafos( $c );
				$cie = null;
				foreach ( $par as $x ) {
					if ( 0 === strpos( ltrim( $x[3] ), '<em>' ) ) {
						$cie = $x;
					}
				}
				$resto = $cie ? preg_replace( '#<!-- wp:paragraph[^>]*-->\s*<p[^>]*>.*?</p>\s*<!-- /wp:paragraph -->\s*#su', '', substr( $c, $cie[0] + $cie[1] ) ) : '?';
				if ( $cie && '' === trim( $resto ) ) {
					$c       = substr( $c, 0, $cie[0] ) . $grupo . substr( $c, $cie[0] );
					$hecho[] = 'b: sección de FAQ nueva (una pregunta) antes del cierre en cursiva';
				} else {
					$c       = rtrim( $c ) . "\n\n" . $grupo;
					$hecho[] = 'b: sección de FAQ nueva (una pregunta) al final';
				}
			}
		}
		return [ $c, $hecho, $avisos ];
	}
	/** Texto plano que se agrega (para comparar el antes y el después sin lo agregado). */
	function pm_sv_sin_agregado( string $c, string $i, array $T ): string {
		$c = str_replace( pm_sv_bloque_p( $i, $T ), '', $c );
		$c = str_replace( pm_sv_bloque_q( $i, $T ), '', $c );
		$c = str_replace( "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">" . $T[ $i ]['h'] . "</h2>\n<!-- /wp:heading -->\n\n<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( pm_sv_bloque_q( $i, $T ) ) . "</div>\n<!-- /wp:group -->\n\n", '', $c );
		return $c;
	}
}

// ---- Filas por clave + idioma ----
$filas = function ( string $clave ) {
	$por = [];
	foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		$por[ pm_idioma_de( $p->ID ) ] = $p;
	}
	return $por;
};
$modo   = $aplicar ? 'APLICA' : 'SIMULACRO';
$cambio = 0;
$fallos = 0;

foreach ( $plan as $clave => $quiere ) {
	$por = $filas( $clave );
	WP_CLI::log( "\n######## $clave" );
	if ( empty( $por['es'] ) ) {
		WP_CLI::warning( "$clave: no hay fila es: no toco nada." );
		continue;
	}
	if ( pm_campo( 'nota_informativa', $por['es']->ID ) ) {
		WP_CLI::log( "$clave: nota_informativa = 1 (pieza informativa, sin llamados comerciales): se salta en los tres idiomas." );
		continue;
	}
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		if ( empty( $por[ $i ] ) ) {
			WP_CLI::log( "-- $i: NO EXISTE: nada que tocar." );
			continue;
		}
		$p      = $por[ $i ];
		$id     = $p->ID;
		$antes  = (string) get_post_field( 'post_content', $id );
		foreach ( [ 'a', 'b' ] as $k ) {
			if ( empty( $quiere[ $k ] ) && isset( $quiere[ 'motivo_' . $k ] ) ) {
				WP_CLI::log( "-- $i #$id: $k no se agrega: " . $quiere[ 'motivo_' . $k ] . '.' );
			}
		}
		[ $despues, $hecho, $avisos ] = pm_sv_aplicar( $antes, $i, $quiere, $T );
		foreach ( $avisos as $a ) {
			WP_CLI::warning( "$clave ($i) #$id: $a" );
			$fallos++;
		}
		$faq_antes   = pm_faqs_contenido( $antes );
		$faq_despues = pm_faqs_contenido( $despues );
		$par_antes   = pm_sv_parrafos( $antes );
		WP_CLI::log( "-- $i #$id /{$p->post_name}/ [$modo] " . implode( '; ', $hecho ) );
		WP_CLI::log( '   ANTES  primer párrafo: ' . mb_substr( $par_antes ? $par_antes[0][2] : '(ninguno)', 0, 230 ) );
		WP_CLI::log( '   ANTES  FAQ (' . count( $faq_antes ) . '): ' . ( $faq_antes ? $faq_antes[0]['pregunta'] : '(sin FAQ)' ) );
		if ( $despues === $antes ) {
			WP_CLI::log( '   DESPUÉS: sin cambios.' );
			continue;
		}
		$cambio++;
		if ( ! empty( $quiere['a'] ) && false !== strpos( $despues, 'class="pm-servicio"' ) && false === strpos( $antes, 'class="pm-servicio"' ) ) {
			WP_CLI::log( '   DESPUÉS párrafo nuevo: ' . pm_sv_texto( pm_sv_bloque_p( $i, $T ) ) );
		}
		if ( $faq_despues && ( ! $faq_antes || $faq_despues[0]['pregunta'] !== $faq_antes[0]['pregunta'] ) ) {
			WP_CLI::log( '   DESPUÉS FAQ (' . count( $faq_despues ) . ') primera: ' . $faq_despues[0]['pregunta'] );
			WP_CLI::log( '   DESPUÉS FAQ primera, respuesta (JSON-LD, texto limpio): ' . $faq_despues[0]['respuesta'] );
		}
		WP_CLI::log( '   contenido: ' . strlen( $antes ) . ' → ' . strlen( $despues ) . ' bytes; tablas ' . substr_count( $antes, '<table' ) . ' → ' . substr_count( $despues, '<table' ) );
		// Comprobaciones que valen también en simulacro.
		$ok = substr_count( $antes, '<table' ) === substr_count( $despues, '<table' )
			&& rtrim( pm_sv_sin_agregado( $despues, $i, $T ) ) === rtrim( $antes )                 // solo se agregó lo nuestro: nada más cambió
			&& pm_sv_aplicar( $despues, $i, $quiere, $T )[0] === $despues                         // idempotente: una segunda corrida no cambia nada
			&& substr_count( $despues, 'class="pm-servicio"' ) <= 1
			&& ( empty( $quiere['b'] ) || ( $faq_despues && $T[ $i ]['q'] === $faq_despues[0]['pregunta'] ) );
		WP_CLI::log( '   ' . ( $ok ? 'CHECK ok: solo lo agregado cambia, una segunda corrida no cambia nada, la pregunta es la primera.' : 'CHECK FALLÓ' ) );
		if ( ! $ok ) {
			$fallos++;
			WP_CLI::warning( "$clave ($i) #$id: las comprobaciones previas fallaron: no guardo esta fila." );
			continue;
		}
		if ( ! $aplicar ) {
			continue;
		}
		$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $despues ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( "$clave ($i): " . $r->get_error_message() );
		}
		// VERIFIED 1: contenido guardado.
		$guardado = (string) get_post_field( 'post_content', $id );
		$gf       = pm_faqs_contenido( $guardado );
		$v1       = ( empty( $quiere['a'] ) || ( 1 === substr_count( $guardado, 'class="pm-servicio"' ) && false !== strpos( $guardado, esc_url( pm_url_presupuesto( $i ) ) ) ) )
			&& ( empty( $quiere['b'] ) || ( $gf && $T[ $i ]['q'] === $gf[0]['pregunta'] && false !== strpos( $guardado, esc_url( pm_url_viaje( $i ) ) ) ) )
			&& substr_count( $guardado, '<table' ) === substr_count( $antes, '<table' )
			&& rtrim( pm_sv_sin_agregado( $guardado, $i, $T ) ) === rtrim( $antes )
			&& get_post_field( 'post_name', $id ) === $p->post_name;
		// VERIFIED 2: la página renderizada.
		$resp = wp_remote_get( add_query_arg( 'pm_v', time(), get_permalink( $id ) ), [ 'timeout' => 25, 'redirection' => 3, 'user-agent' => 'Pescamapa-lector/1.0 (solo lectura)' ] );
		$html = is_wp_error( $resp ) ? '' : (string) wp_remote_retrieve_body( $resp );
		$st   = is_wp_error( $resp ) ? 0 : (int) wp_remote_retrieve_response_code( $resp );
		$ld_q = '';
		if ( preg_match_all( '#<script type="application/ld\+json"[^>]*>(.*?)</script>#su', $html, $mm ) ) {
			foreach ( $mm[1] as $j ) {
				$d = json_decode( $j, true );
				foreach ( ( isset( $d['@graph'] ) ? $d['@graph'] : [ $d ] ) as $nodo ) {
					if ( is_array( $nodo ) && 'FAQPage' === ( $nodo['@type'] ?? '' ) ) {
						$ld_q = (string) ( $nodo['mainEntity'][0]['name'] ?? '' );
					}
				}
			}
		}
		$v2 = 200 === $st
			&& ( empty( $quiere['a'] ) || ( false !== strpos( $html, 'class="pm-servicio"' ) && false !== strpos( $html, esc_url( pm_url_presupuesto( $i ) ) ) ) )
			&& ( empty( $quiere['b'] ) || ( $T[ $i ]['q'] === $ld_q && false !== strpos( $html, esc_html( $T[ $i ]['q'] ) ) ) )
			&& 1 === substr_count( $html, 'class="cta-nota"' )
			&& false !== strpos( $html, 'barra-pedir' );
		WP_CLI::log( ( $v1 && $v2 ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id contenido=" . ( $v1 ? 'ok' : 'NO' ) . " render=" . ( $v2 ? 'ok' : "NO (status $st, primera pregunta del JSON-LD «$ld_q»)" ) . ' ' . get_permalink( $id ) );
		if ( ! ( $v1 && $v2 ) ) {
			$fallos++;
		}
	}
}
WP_CLI::log( "\n$modo: $cambio filas con cambio, $fallos fallos o avisos." );
if ( $fallos ) {
	WP_CLI::error( "Hay $fallos fallos o avisos: revisar el log." );
}
WP_CLI::success( $aplicar ? 'Aplicado.' : 'Simulacro terminado: no se escribió nada.' );
