<?php
/**
 * Junín y San Martín guide (es/en), 30/10/2026: the cross validity of the Patagonian permit is now read, not reported. The "Anexo Río Negro"
 * of the regulation (text copied from reglamentodepesca.org.ar by the owner, docs/investigacion/fuentes/anexo-rio-negro-2026-27-copiado-2026-10-30.md)
 * says the permit is valid in Río Negro, Neuquén, Chubut and the National Parks. Only the claim that additional permits are valid just where they
 * are issued still comes from an excerpt, and the guide keeps saying so. Replaces exact fragments in the body, the regulation summary and one FAQ.
 * Each must be found or already replaced. Idempotent. Dry run first. Run with wp-script.yml (script = junin-validez-2026-10-30).
 */

$aplicar = false;

$cambios = [
	'en' => [
		'cuerpo' => [
			'According to the general regulation (we saw this wording in an excerpt, not in the 2026/27 PDF), permits issued by Neuquén, Río Negro, Chubut and the National Parks in the standard categories and prices are valid across those jurisdictions, while additional permits (trolling, preferential zones) are valid only where they are issued. Confirm this when you buy.'
				=> 'According to the Río Negro annex of the 2026/27 regulation, the Patagonian fishing permit is valid in Río Negro, Neuquén, Chubut and the National Parks, as long as its authenticity can be checked on the spot. Additional permits (trolling, preferential zones) are valid only where they are issued, a wording we saw in an excerpt of the general regulation: confirm it when you buy.',
		],
		'reglamento_resumen' => [
			'Cross validity of permits and the general rules (felt soles, Didymo) were not read in the general 2026/27 text.'
				=> 'The validity of the Patagonian permit in the four jurisdictions was read in the Río Negro annex (text copied from the regulation site by the owner on 30/10/2026); the general rules (felt soles, Didymo) were not read in the general 2026/27 text.',
		],
		'faq' => [
			'pregunta' => 'Is a permit from another Patagonian province valid?',
			'respuesta' => 'Yes. The Río Negro annex of the 2026/27 regulation says the Patagonian fishing permit is valid in Río Negro, Neuquén, Chubut and the National Parks, as long as its authenticity can be checked on the spot. Additional permits (trolling, preferential zones) are valid only where they are issued, according to an excerpt of the general regulation we saw: confirm it when you buy.',
		],
	],
	'es' => [
		'cuerpo' => [
			'Según el reglamento general (vimos esa redacción en un extracto, no en el PDF 2026/27), los permisos que expiden Neuquén, Río Negro, Chubut y Parques Nacionales en las categorías y valores estándar valen en todas esas jurisdicciones, mientras que los adicionales (trolling, zonas preferenciales) valen solo donde se emiten. Confirmalo al comprar.'
				=> 'Según el anexo de Río Negro del reglamento 2026/27, el permiso patagónico de pesca vale en Río Negro, Neuquén, Chubut y Parques Nacionales, siempre que su autenticidad pueda comprobarse en el lugar. Los permisos adicionales (trolling, zonas preferenciales) valen solo donde se emiten, según una redacción que vimos en un extracto del reglamento general: confirmalo al comprar.',
		],
		'reglamento_resumen' => [
			'La validez cruzada de permisos y las reglas generales (fieltro, Didymo) no se leyeron en el texto general 2026/27.'
				=> 'La validez del permiso patagónico en las cuatro jurisdicciones se leyó en el anexo de Río Negro (texto copiado del sitio del reglamento por el dueño el 30/10/2026); las reglas generales (fieltro, Didymo) no se leyeron en el texto general 2026/27.',
		],
		'faq' => [
			'pregunta' => '¿Sirven los permisos de otra provincia patagónica?',
			'respuesta' => 'Sí. El anexo de Río Negro del reglamento 2026/27 dice que el permiso patagónico de pesca vale en Río Negro, Neuquén, Chubut y Parques Nacionales, siempre que su autenticidad pueda comprobarse en el lugar. Los permisos adicionales (trolling, zonas preferenciales) valen solo donde se emiten, según un extracto del reglamento general que vimos: confirmalo al comprar.',
		],
	],
];

function pm_jv_reemplazar( string $texto, array $pares, string $donde ): string {
	foreach ( $pares as $viejo => $nuevo ) {
		if ( false !== strpos( $texto, $viejo ) ) {
			$texto = str_replace( $viejo, $nuevo, $texto );
		} elseif ( false === strpos( $texto, $nuevo ) ) {
			WP_CLI::error( "No encontré el fragmento en $donde: " . mb_substr( $viejo, 0, 70 ) );
		}
	}
	return $texto;
}

foreach ( $cambios as $idioma => $c ) {
	$p = pm_buscar( 'pm_destino', 'junin-de-los-andes', $idioma );
	if ( ! $p ) {
		WP_CLI::error( "No encontré la guía junin-de-los-andes ($idioma)." );
	}
	$cuerpo0 = $p->post_content;
	$cuerpo  = pm_jv_reemplazar( $cuerpo0, $c['cuerpo'], "cuerpo $idioma" );
	$res0    = (string) pm_campo( 'reglamento_resumen', $p->ID );
	$res     = pm_jv_reemplazar( $res0, $c['reglamento_resumen'], "resumen $idioma" );
	$filas   = pm_filas( 'faqs', $p->ID );
	$tocada  = false;
	$esta    = false;
	foreach ( $filas as $k => $f ) {
		if ( ( $f['pregunta'] ?? '' ) === $c['faq']['pregunta'] ) {
			$esta = true;
			if ( ( $f['respuesta'] ?? '' ) !== $c['faq']['respuesta'] ) {
				$filas[ $k ]['respuesta'] = $c['faq']['respuesta'];
				$tocada = true;
			}
		}
	}
	$esta || WP_CLI::error( "junin-de-los-andes ($idioma): no encontré la pregunta de los permisos de otras provincias." );
	WP_CLI::log( "junin-de-los-andes ($idioma) #{$p->ID}: cuerpo " . ( $cuerpo !== $cuerpo0 ? 'cambia' : 'igual' ) . ', resumen ' . ( $res !== $res0 ? 'cambia' : 'igual' ) . ', FAQ ' . ( $tocada ? 'cambia' : 'igual' ) );
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $cuerpo ) ] );
	update_field( 'reglamento_resumen', $res, $p->ID );
	update_field( 'faqs', $filas, $p->ID );
	$leida = get_post_field( 'post_content', $p->ID );
	$ok    = false !== strpos( $leida, $c['cuerpo'][ array_key_first( $c['cuerpo'] ) ] ) && false !== strpos( (string) pm_campo( 'reglamento_resumen', $p->ID ), 'Río Negro' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": junin-de-los-andes ($idioma) con la validez leída en el anexo de Río Negro; " . count( pm_filas( 'faqs', $p->ID ) ) . ' preguntas' );
	$ok || WP_CLI::error( 'la corrección no quedó.' );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
