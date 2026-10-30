<?php
/**
 * Junín y San Martín guide (es/en), 30/10/2026: three things read in the Anexo Neuquén and the Anexo Río Negro (texts copied from the regulation
 * site by the owner; docs/investigacion/fuentes/):
 *  1. Cross validity of the Patagonian permit (both annexes). Only "additional permits valid where issued" still comes from an excerpt.
 *  2. Hiring a guide that is not an authorised tourism provider of Neuquén is an offence (it was worded as a recommendation).
 *  3. The preferential zone permit: the annex text lists AR$ 10,000 / 20,000 / 40,000, the 16/10/2026 announcement AR$ 12,500 / 25,000 / 50,000.
 *     The guide keeps the announced values and says the text differs. Also the whole Correntoso River is a preferential zone.
 * Replaces exact fragments in the body, the regulation summary and two FAQs. Each must be found or already replaced. Idempotent. Dry run first.
 * Run with wp-script.yml (script = junin-validez-2026-10-30).
 */

$aplicar = true;

$cambios = [
	'en' => [
		'cuerpo' => [
			'According to the general regulation (we saw this wording in an excerpt, not in the 2026/27 PDF), permits issued by Neuquén, Río Negro, Chubut and the National Parks in the standard categories and prices are valid across those jurisdictions, while additional permits (trolling, preferential zones) are valid only where they are issued. Confirm this when you buy.'
				=> 'According to the Neuquén and Río Negro annexes of the 2026/27 regulation, the Patagonian fishing permit is valid in Río Negro, Neuquén, Chubut and the National Parks: a permit bought online needs its QR code and your ID, with no extra stamp. Additional permits (trolling, preferential zones) are valid only where they are issued, a wording we saw in an excerpt of the general regulation: confirm it when you buy.',
			'The provincial ministry also recommends hiring only guides and agencies that are authorised by the province.'
				=> 'The Neuquén annex bans hiring fishing guides who are not authorised tourism providers of the province (it is an offence), so check the provincial list before you book.',
			'<strong>Preferential zone</strong> (for example the mouth of the Chimehuín): an extra AR$ 12,500 per day, AR$ 25,000 for ten days or AR$ 50,000 for the season.'
				=> '<strong>Preferential zone</strong> (Zone I of the Chimehuín and the whole Correntoso): an extra AR$ 12,500 per day, AR$ 25,000 for ten days or AR$ 50,000 for the season, as announced on 16 October 2026. The annex text on the regulation site lists AR$ 10,000, AR$ 20,000 and AR$ 40,000 for the same permits, which may be last season\'s figures: confirm when you buy.',
			'In general, "release" in these entries means every salmonid goes back alive, and a single barbless hook is the norm.'
				=> 'In general, "release" in these entries means every salmonid goes back alive, and a single barbless hook is the norm. The Correntoso River is also a preferential zone along its whole length, with the same additional permit and no navigation.',
		],
		'reglamento_resumen' => [
			'Cross validity of permits and the general rules (felt soles, Didymo) were not read in the general 2026/27 text.'
				=> 'The validity of the Patagonian permit was read in the Neuquén and Río Negro annexes (texts copied from the regulation site by the owner on 30/10/2026); the general rules (felt soles) were not read in the general 2026/27 text.',
		],
		'faqs' => [
			[
				'pregunta' => 'Is a permit from another Patagonian province valid?',
				'respuesta' => 'Yes. The Neuquén and Río Negro annexes of the 2026/27 regulation say the Patagonian fishing permit is valid in Río Negro, Neuquén, Chubut and the National Parks; a permit bought online needs its QR code and your ID. Additional permits (trolling, preferential zones) are valid only where they are issued, according to an excerpt of the general regulation we saw: confirm it when you buy.',
			],
			[
				'pregunta' => 'Is an additional permit needed for the Chimehuín?',
				'respuesta' => 'For Zone I (from the outflow of the lake to 250 m downstream of the signed bridge) and for the whole Correntoso, yes: they are preferential zones with a compulsory additional permit. The announced 2026/27 values are AR$ 12,500 per day, AR$ 25,000 for ten days and AR$ 50,000 for the season; the annex text lists lower figures, so confirm the amount when you buy.',
			],
			[
				'pregunta' => 'Where do I find authorised guides?',
				'respuesta' => 'The Neuquén tourism ministry publishes the list of authorised fishing providers, and hiring a guide who is not on it is an offence under the Neuquén annex. Check the list before you book.',
			],
		],
	],
	'es' => [
		'cuerpo' => [
			'Según el reglamento general (vimos esa redacción en un extracto, no en el PDF 2026/27), los permisos que expiden Neuquén, Río Negro, Chubut y Parques Nacionales en las categorías y valores estándar valen en todas esas jurisdicciones, mientras que los adicionales (trolling, zonas preferenciales) valen solo donde se emiten. Confirmalo al comprar.'
				=> 'Según los anexos de Neuquén y de Río Negro del reglamento 2026/27, el permiso patagónico de pesca vale en Río Negro, Neuquén, Chubut y Parques Nacionales: el permiso comprado online necesita su código QR y tu documento, sin estampillado adicional. Los permisos adicionales (trolling, zonas preferenciales) valen solo donde se emiten, según una redacción que vimos en un extracto del reglamento general: confirmalo al comprar.',
			'El ministerio provincial recomienda además contratar solo guías y agencias habilitados por la provincia.'
				=> 'El anexo de Neuquén prohíbe contratar guías de pesca que no estén habilitados como prestadores turísticos de la provincia (es una infracción): consultá el listado provincial antes de reservar.',
			'<strong>Zona preferencial</strong> (por ejemplo la boca del Chimehuín): adicional de $12.500 por día, $25.000 por diez días o $50.000 por temporada.'
				=> '<strong>Zona preferencial</strong> (Zona I del Chimehuín y todo el río Correntoso): adicional de $12.500 por día, $25.000 por diez días o $50.000 por temporada, según el anuncio del 16 de octubre de 2026. El texto del anexo en el sitio del reglamento lista $10.000, $20.000 y $40.000 para los mismos permisos, que pueden ser los de la temporada anterior: confirmalo al comprar.',
			'En general, "devolución" en estas fichas significa que todos los salmónidos vuelven vivos al agua, y el anzuelo simple sin rebaba es la norma.'
				=> 'En general, "devolución" en estas fichas significa que todos los salmónidos vuelven vivos al agua, y el anzuelo simple sin rebaba es la norma. El río Correntoso también es zona preferencial en toda su extensión, con el mismo permiso adicional y sin navegación.',
		],
		'reglamento_resumen' => [
			'La validez cruzada de permisos y las reglas generales (fieltro, Didymo) no se leyeron en el texto general 2026/27.'
				=> 'La validez del permiso patagónico se leyó en los anexos de Neuquén y de Río Negro (textos copiados del sitio del reglamento por el dueño el 30/10/2026); las reglas generales (fieltro) no se leyeron en el texto general 2026/27.',
		],
		'faqs' => [
			[
				'pregunta' => '¿Sirven los permisos de otra provincia patagónica?',
				'respuesta' => 'Sí. Los anexos de Neuquén y de Río Negro del reglamento 2026/27 dicen que el permiso patagónico de pesca vale en Río Negro, Neuquén, Chubut y Parques Nacionales; el permiso comprado online necesita su código QR y tu documento. Los permisos adicionales (trolling, zonas preferenciales) valen solo donde se emiten, según un extracto del reglamento general que vimos: confirmalo al comprar.',
			],
			[
				'pregunta' => '¿Hace falta un permiso adicional para el Chimehuín?',
				'respuesta' => 'Para la Zona I (de la boca del lago a 250 m aguas abajo del puente señalizado) y para todo el río Correntoso sí: son zonas preferenciales con permiso adicional obligatorio. Los valores anunciados para 2026/27 son $12.500 por día, $25.000 por diez días y $50.000 por temporada; el texto del anexo lista cifras menores, así que confirmá el monto al comprar.',
			],
			[
				'pregunta' => '¿Dónde busco guías habilitados?',
				'respuesta' => 'El ministerio de turismo de Neuquén publica el listado de prestadores de pesca habilitados, y contratar un guía que no figure en él es una infracción según el anexo de Neuquén. Consultá el listado antes de reservar.',
			],
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
	$tocadas = 0;
	foreach ( $c['faqs'] as $nueva ) {
		$esta = false;
		foreach ( $filas as $k => $f ) {
			if ( ( $f['pregunta'] ?? '' ) === $nueva['pregunta'] || ( 0 === $tocadas && false ) ) {
				$esta = true;
				if ( ( $f['respuesta'] ?? '' ) !== $nueva['respuesta'] ) {
					$filas[ $k ]['respuesta'] = $nueva['respuesta'];
					$tocadas++;
				}
			}
		}
		$esta || WP_CLI::error( "junin-de-los-andes ($idioma): no encontré la pregunta «{$nueva['pregunta']}»." );
	}
	WP_CLI::log( "junin-de-los-andes ($idioma) #{$p->ID}: cuerpo " . ( $cuerpo !== $cuerpo0 ? 'cambia' : 'igual' ) . ', resumen ' . ( $res !== $res0 ? 'cambia' : 'igual' ) . ", FAQs que cambian: $tocadas" );
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $cuerpo ) ] );
	update_field( 'reglamento_resumen', $res, $p->ID );
	update_field( 'faqs', $filas, $p->ID );
	$leida = get_post_field( 'post_content', $p->ID );
	$ultimo = array_key_last( $c['cuerpo'] );
	$ok    = false !== strpos( $leida, $c['cuerpo'][ $ultimo ] ) && false !== strpos( $leida, $c['cuerpo'][ array_key_first( $c['cuerpo'] ) ] ) && false !== strpos( (string) pm_campo( 'reglamento_resumen', $p->ID ), 'Neuquén' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": junin-de-los-andes ($idioma) con la validez, los guías habilitados y las zonas preferenciales; " . count( pm_filas( 'faqs', $p->ID ) ) . ' preguntas' );
	$ok || WP_CLI::error( 'la corrección no quedó.' );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
