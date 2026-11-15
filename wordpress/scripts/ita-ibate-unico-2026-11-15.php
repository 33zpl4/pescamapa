<?php
/**
 * Itá Ibaté (Corrientes), 15/11/2026: removes the claim that Dorados on the Fly is "the only lodge with a page of its own that we found" (es, en, pt).
 * It was true on 13/11/2026; on 15/11/2026 we read the own sites of more operators in the area (docs/prestadores-brasil-2026-11-15.md: Pousada Gêmeos,
 * in Itá Ibaté; La Regina, Itatí area; others), so the sentence is false. No card is created (owner decides that later) and no operator is named.
 * What is replaced, per language (each piece only when it still holds the text published on 14/11/2026, read live on 15/11/2026 with investigar.yml,
 * folder vivo-gallegos-ibate-20261115-2151; anything else is logged as «falta» and the script refuses to write that guide):
 *  - body paragraph "Esta guía es corta a propósito…": «el lodge cuya página leímos es Dorados on the Fly…» + one sentence saying that other fishing
 *    lodges in the area with sites of their own do not have a card in this guide yet;
 *  - the "cuándo conviene ir" FAQ answer (it feeds the FAQPage JSON-LD): «el lodge cuya página leímos»;
 *  - field `bajada` (es, en; the pt one was already rewritten by pt-pousadas-titulos-2026-11-15) and `seo_description` (the meta description): same wording.
 * The row «Lodge con página propia» of the key-facts table is left as it is: it labels Dorados on the Fly and does not claim it is the only one.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = ita-ibate-unico-2026-11-15).
 */

$aplicar = false;
$hoy     = '20261115';

$guias = [
	'es' => [
		'cuerpo' => [
			[ 'el único lodge con página propia que encontramos es <strong>Dorados on the Fly</strong>', 'el lodge cuya página leímos es <strong>Dorados on the Fly</strong>' ],
			[ ' Lo que sigue es lo que sí tiene fuente.', ' Hay otros alojamientos de pesca con sitio propio en la zona que todavía no tienen ficha en esta guía. Lo que sigue es lo que sí tiene fuente.' ],
		],
		'faq' => [ '¿Cuándo conviene ir a pescar dorado a Itá Ibaté?', 'el único lodge con página propia que encontramos, no publica temporada', 'el lodge cuya página leímos, no publica temporada' ],
		'meta' => [
			'bajada' => [ 'qué publica el único lodge con página propia que encontramos y qué reglas valen.', 'qué publica el lodge cuya página leímos y qué reglas valen.' ],
			'seo_description' => [ 'el lodge con página propia,', 'el lodge cuya página leímos,' ],
		],
	],
	'en' => [
		'cuerpo' => [
			[ 'the only lodge with a page of its own that we found is <strong>Dorados on the Fly</strong>', 'the lodge whose page we read is <strong>Dorados on the Fly</strong>' ],
			[ ' What follows is what has a source.', ' There are other fishing lodges in the area with sites of their own that do not yet have a card in this guide. What follows is what has a source.' ],
		],
		'faq' => [ 'When is the best time to fish dorado in Itá Ibaté?', 'the only lodge with a page of its own that we found, publishes no season', 'the lodge whose page we read, publishes no season' ],
		'meta' => [
			'bajada' => [ 'what the one lodge with a page of its own that we found publishes, and which rules apply.', 'what the lodge whose page we read publishes, and which rules apply.' ],
			'seo_description' => [ 'the lodge with its own page,', 'the lodge whose page we read,' ],
		],
	],
	'pt' => [
		'cuerpo' => [
			[ 'o único lodge com página própria que encontramos é o <strong>Dorados on the Fly</strong>', 'o lodge cuja página lemos é o <strong>Dorados on the Fly</strong>' ],
			[ ' O que segue é o que tem fonte.', ' Há outras pousadas de pesca na região com site próprio que ainda não têm ficha neste guia. O que segue é o que tem fonte.' ],
		],
		'faq' => [ 'Quando convém ir pescar dourado em Itá Ibaté?', 'o único lodge com página própria que encontramos, não publica temporada', 'o lodge cuja página lemos, não publica temporada' ],
		'meta' => [
			// bajada: already «a pousada de pesca (lodge) cuja página lemos» (pt-pousadas-titulos-2026-11-15); nothing to change there.
			'seo_description' => [ 'a pousada de pesca com página própria,', 'a pousada de pesca cuja página lemos,' ],
		],
	],
];

function pm_iu_cambiar( string $texto, string $viejo, string $nuevo ): array {
	if ( false !== strpos( $texto, $nuevo ) ) {
		return [ $texto, 'ya' ];
	}
	if ( false === strpos( $texto, $viejo ) ) {
		return [ $texto, 'falta' ];
	}
	return [ str_replace( $viejo, $nuevo, $texto ), 'cambia' ];
}

$plan = [];
foreach ( $guias as $i => $g ) {
	$p = pm_buscar( 'pm_destino', 'ita-ibate', $i );
	if ( ! $p ) {
		WP_CLI::warning( "guía ita-ibate ($i): no existe, se omite" );
		continue;
	}
	$texto   = $p->post_content;
	$estados = [];
	foreach ( $g['cuerpo'] as $n => [ $viejo, $nuevo ] ) {
		[ $texto, $e ] = pm_iu_cambiar( $texto, $viejo, $nuevo );
		$estados[]     = "cuerpo #$n: $e";
		'cambia' === $e && WP_CLI::log( "  guía ($i) cuerpo #$n\n      ANTES:   $viejo\n      DESPUÉS: $nuevo" );
	}
	$filas = pm_filas( 'faqs', $p->ID );
	[ $pregunta, $viejo, $nuevo ] = $g['faq'];
	$hallada = false;
	foreach ( $filas as $k => $f ) {
		if ( ( $f['pregunta'] ?? '' ) === $pregunta ) {
			$hallada = true;
			[ $filas[ $k ]['respuesta'], $e ] = pm_iu_cambiar( (string) $f['respuesta'], $viejo, $nuevo );
			$estados[] = "FAQ: $e";
			'cambia' === $e && WP_CLI::log( "  guía ($i) FAQ «{$pregunta}»\n      ANTES (respuesta completa):   {$f['respuesta']}\n      DESPUÉS (respuesta completa): {$filas[ $k ]['respuesta']}" );
		}
	}
	$hallada || $estados[] = 'FAQ: falta (pregunta no encontrada)';
	$metas = [];
	foreach ( $g['meta'] as $campo => [ $viejo, $nuevo ] ) {
		$ahora = (string) get_post_meta( $p->ID, $campo, true );
		[ $despues, $e ] = pm_iu_cambiar( $ahora, $viejo, $nuevo );
		$estados[] = "$campo: $e";
		if ( 'seo_description' === $campo && mb_strlen( $despues ) > 160 ) {
			WP_CLI::error( "ita-ibate ($i) seo_description: " . mb_strlen( $despues ) . ' caracteres (máx. 160)' );
		}
		if ( 'cambia' === $e ) {
			WP_CLI::log( "  guía ($i) $campo\n      ANTES:   $ahora\n      DESPUÉS: $despues" );
			$metas[ $campo ] = $despues;
		}
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía ita-ibate ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' | ' . implode( ' | ', $estados ) );
	foreach ( $estados as $e ) {
		false !== strpos( $e, 'falta' ) && WP_CLI::warning( "guía ita-ibate ($i): $e" );
	}
	$restos = $texto . ' ' . wp_json_encode( $filas, JSON_UNESCAPED_UNICODE ) . ' ' . ( $metas['bajada'] ?? get_post_meta( $p->ID, 'bajada', true ) ) . ' ' . ( $metas['seo_description'] ?? get_post_meta( $p->ID, 'seo_description', true ) );
	if ( preg_match_all( '/.{0,60}(único lodge|only lodge|one lodge with a page|único com página|lodge com página própria que encontramos).{0,60}/u', $restos, $mm ) ) {
		foreach ( $mm[0] as $r ) {
			WP_CLI::warning( "guía ita-ibate ($i): después del cambio todavía queda: …{$r}…" );
		}
	} else {
		WP_CLI::log( "  guía ($i): después del cambio no queda ninguna frase de «el único lodge»" );
	}
	$plan[ $i ] = [ 'id' => $p->ID, 'texto' => $texto, 'filas' => $filas, 'metas' => $metas, 'estados' => $estados ];
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
foreach ( $plan as $i => $x ) {
	foreach ( $x['estados'] as $e ) {
		false !== strpos( $e, 'falta' ) && WP_CLI::error( "no escribo la guía ($i): $e" );
	}
}
$t = time();
foreach ( $plan as $i => $x ) {
	if ( array_filter( $x['estados'], fn( $e ) => false !== strpos( $e, 'cambia' ) ) ) {
		wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['texto'] ) ] );
		update_field( 'faqs', $x['filas'], $x['id'] );
		foreach ( $x['metas'] as $campo => $v ) {
			update_field( $campo, $v, $x['id'] );
		}
		update_field( 'actualizado', $hoy, $x['id'] );
		update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
	}
}
$ok = true;
foreach ( $plan as $i => $x ) {
	$p     = get_post( $x['id'] );
	$filas = pm_filas( 'faqs', $x['id'] );
	$bien  = true;
	foreach ( $guias[ $i ]['cuerpo'] as [ , $nuevo ] ) {
		$bien = $bien && false !== strpos( $p->post_content, $nuevo );
	}
	$hit = false;
	foreach ( $filas as $f ) {
		$hit = $hit || ( ( $f['pregunta'] ?? '' ) === $guias[ $i ]['faq'][0] && false !== strpos( (string) $f['respuesta'], $guias[ $i ]['faq'][2] ) );
	}
	$bien = $bien && $hit;
	foreach ( $guias[ $i ]['meta'] as $campo => [ , $nuevo ] ) {
		$bien = $bien && false !== strpos( (string) get_post_meta( $x['id'], $campo, true ), $nuevo );
	}
	// Nothing in the body, the FAQ or the two meta fields still says it is the only one.
	$todo = $p->post_content . ' ' . wp_json_encode( $filas, JSON_UNESCAPED_UNICODE ) . ' ' . get_post_meta( $x['id'], 'bajada', true ) . ' ' . get_post_meta( $x['id'], 'seo_description', true );
	$sin  = ! preg_match( '/único lodge|only lodge|one lodge with a page|único com página|lodge com página própria que encontramos/u', $todo );
	$bien = $bien && $sin;
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": guía ita-ibate ($i) #{$x['id']}: " . count( $guias[ $i ]['cuerpo'] ) . ' párrafos, 1 FAQ y ' . count( $guias[ $i ]['meta'] ) . ' campos leídos de vuelta; sin «el único lodge con página propia» ' . get_permalink( $x['id'] ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'APLICADO' );
