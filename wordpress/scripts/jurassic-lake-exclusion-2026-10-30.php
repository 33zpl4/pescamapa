<?php
/**
 * Jurassic Lake guide (es/en), 30/10/2026: remove the two "exclusion area" lines (Strobel 200 m, Barrancoso 100 m). The Word version of the
 * regulation the owner uploaded (16 pages, same layout as the signed copy) does NOT have them; they appear only in the third-party copy
 * (pesca.clubnautico.org). Until the official text is confirmed we publish only what the signed text says: Art. 11 (release within 200 m of
 * river mouths and sources) and Art. 15 a (no boat fishing within 200 m of river mouths in lakes). Replaces exact fragments; each must be
 * found once or the script stops. Idempotent. Dry run first. Run with wp-script.yml (script = jurassic-lake-exclusion-2026-10-30).
 */

$aplicar = false;

$cambios = [
	'en' => [
		'cuerpo' => [
			' Fishing is banned on both banks within <strong>200 m</strong> of the mouths and sources of rivers linked to the lake.' => '',
			' Fishing is banned on both banks within <strong>100 m</strong> of the river\'s source and mouth.' => '',
		],
		'reglamento_resumen' => [
			', 200 m exclusion, no motors' => ', no motors',
			', 100 m exclusion, daily' => ', daily',
			' Read on 30/10/2026 (Annex I in a third-party copy that matches the signed original in arts. 5, 7, 11, 12 and 16).' => ' Read on 30/10/2026 in two copies of the regulation; the exclusion distances that appear in only one of them are not published.',
		],
		'faq' => [
			'pregunta' => 'Can I fish near the mouth of the Barrancoso?',
			'respuesta' => 'Yes. The lodges describe their beats as next to the mouth. The regulation requires every fish to be released within 200 m of river mouths and sources (Article 11) and bans fishing from a boat within 200 m of river mouths in lakes (Article 15 a). Annex I may also set minimum distances around the mouths of Strobel and the Barrancoso; the copies we compared differ on this, so ask your operator which sectors are open on your dates and the distance they respect.',
		],
	],
	'es' => [
		'cuerpo' => [
			' Está prohibido pescar sobre ambas márgenes en un radio de <strong>200 m</strong> desde las nacientes y desembocaduras de los ríos vinculados al lago.' => '',
			' Está prohibido pescar sobre ambas márgenes en <strong>100 m</strong> desde sus nacientes y desembocadura.' => '',
		],
		'reglamento_resumen' => [
			', exclusión de 200 m, sin motores' => ', sin motores',
			', exclusión de 100 m, certificado' => ', certificado',
			' Leído el 30/10/2026 (Anexo I en copia de terceros, coincidente con el original en los arts. 5, 7, 11, 12 y 16).' => ' Leído el 30/10/2026 en dos copias del reglamento; las distancias de exclusión que figuran en una sola de ellas no se publican.',
		],
		'faq' => [
			'pregunta' => '¿Se puede pescar cerca de la desembocadura del Barrancoso?',
			'respuesta' => 'Sí. Los lodges describen sus sectores como cercanos a la boca. El reglamento exige devolver todos los peces a menos de 200 m de desembocaduras y nacientes de ríos (art. 11) y prohíbe pescar desde embarcación a menos de 200 m de las desembocaduras de ríos en lagos (art. 15 a). El Anexo I podría fijar además distancias mínimas alrededor de las bocas del Strobel y del Barrancoso; las copias que comparamos difieren en esto, así que preguntale al operador qué sectores están habilitados en tus fechas y qué distancia respetan.',
		],
	],
];

function pm_jx_reemplazar( string $texto, array $pares, string $donde ): string {
	foreach ( $pares as $viejo => $nuevo ) {
		if ( false === strpos( $texto, $viejo ) ) {
			if ( '' !== $nuevo && false !== strpos( $texto, $nuevo ) ) {
				continue; // already done
			}
			if ( '' === $nuevo ) {
				continue; // already removed (nothing to find)
			}
			WP_CLI::error( "No encontré el fragmento en $donde: " . mb_substr( $viejo, 0, 70 ) );
		}
		$texto = str_replace( $viejo, $nuevo, $texto );
	}
	return $texto;
}

foreach ( $cambios as $idioma => $c ) {
	$p = pm_buscar( 'pm_destino', 'jurassic-lake', $idioma );
	if ( ! $p ) {
		WP_CLI::error( "No encontré la guía jurassic-lake ($idioma)." );
	}
	$cuerpo0 = $p->post_content;
	$cuerpo  = pm_jx_reemplazar( $cuerpo0, $c['cuerpo'], "cuerpo $idioma" );
	$res0    = (string) pm_campo( 'reglamento_resumen', $p->ID );
	$res     = pm_jx_reemplazar( $res0, $c['reglamento_resumen'], "resumen $idioma" );
	$filas   = pm_filas( 'faqs', $p->ID );
	$tocada  = false;
	foreach ( $filas as $k => $f ) {
		if ( ( $f['pregunta'] ?? '' ) === $c['faq']['pregunta'] && ( $f['respuesta'] ?? '' ) !== $c['faq']['respuesta'] ) {
			$filas[ $k ]['respuesta'] = $c['faq']['respuesta'];
			$tocada = true;
		}
	}
	$sigue = ( false !== strpos( $cuerpo, 'mouths and sources of rivers linked' ) ) || ( false !== strpos( $cuerpo, 'desembocaduras de los ríos vinculados' ) ) || ( false !== strpos( $cuerpo, '<strong>100 m</strong>' ) );
	WP_CLI::log( "jurassic-lake ($idioma) #{$p->ID}: cuerpo " . ( $cuerpo !== $cuerpo0 ? 'cambia' : 'igual' ) . ', resumen ' . ( $res !== $res0 ? 'cambia' : 'igual' ) . ', FAQ ' . ( $tocada ? 'cambia' : 'igual' ) );
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $cuerpo ) ] );
	update_field( 'reglamento_resumen', $res, $p->ID );
	update_field( 'faqs', $filas, $p->ID );
	$leida = get_post_field( 'post_content', $p->ID );
	$ok    = false === strpos( $leida, '100 m</strong>' ) && false === strpos( $leida, 'sources of rivers linked' ) && false === strpos( $leida, 'desembocaduras de los ríos vinculados' ) && ! preg_match( '/(100|200) m exclusion|exclusión de (100|200) m/u', (string) pm_campo( 'reglamento_resumen', $p->ID ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": jurassic-lake ($idioma) sin las distancias de exclusión; " . count( pm_filas( 'faqs', $p->ID ) ) . ' preguntas' );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
