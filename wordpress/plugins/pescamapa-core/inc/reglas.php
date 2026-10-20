<?php
/**
 * Completeness guidance, never a gate. Every guide and species published in
 * WordPress is public and indexable. What is still missing to make a ficha
 * complete is listed for editors inside the form ("Para completar la
 * ficha"), and never shown to visitors:
 *   - an official source,
 *   - a regulation check by a person, less than a year old,
 *   - "PENDIENTE" notes left in the text or fields (they are hidden on the
 *     public page, see pm_sin_pendientes()),
 *   - shared fields and the photo credit (Spanish guides),
 *   - the Spanish version (for translations).
 */

defined( 'ABSPATH' ) || exit;

const PM_VIGENCIA_REGLAMENTO_DIAS = 365;

/** @return string[] what would make the ficha more complete (for editors). */
function pm_sugerencias( int $post_id ): array {
	$tipo = get_post_type( $post_id );
	if ( ! in_array( $tipo, [ 'pm_destino', 'pm_especie' ], true ) ) {
		return [];
	}
	$faltan  = [];
	$idioma  = pm_idioma_de( $post_id );
	$clave   = pm_clave_de( $post_id );
	$fuentes = pm_filas( 'fuentes', $post_id );

	if ( ! array_filter( $fuentes, fn( $f ) => ! empty( $f['oficial'] ) ) ) {
		$faltan[] = 'Sumar al menos una fuente oficial.';
	}

	$textos = get_post_field( 'post_content', $post_id ) . wp_json_encode( get_post_meta( $post_id ) );
	if ( str_contains( $textos, 'PENDIENTE' ) ) {
		$faltan[] = 'Completar las notas "PENDIENTE" (no se muestran en la web, pero marcan info por cargar).';
	}

	if ( 'pm_destino' === $tipo ) {
		$verificado = (string) pm_campo( 'reglamento_verificado', $post_id );
		if ( ! $verificado ) {
			$faltan[] = 'Verificar el reglamento vigente con la autoridad y cargar la fecha (pestaña Reglamento).';
		} elseif ( ( time() - strtotime( $verificado ) ) > PM_VIGENCIA_REGLAMENTO_DIAS * DAY_IN_SECONDS ) {
			$faltan[] = 'Volver a verificar el reglamento: la última verificación tiene más de un año.';
		}
		if ( 'es' === $idioma ) {
			foreach ( [ 'region' => 'la región', 'provincia' => 'la provincia', 'lat' => 'la latitud', 'lon' => 'la longitud' ] as $k => $nombre ) {
				if ( '' === (string) pm_campo( $k, $post_id ) ) {
					$faltan[] = "Completar {$nombre}.";
				}
			}
			if ( ! pm_campo( 'especies', $post_id ) ) {
				$faltan[] = 'Elegir al menos una especie.';
			}
			$foto = (int) get_post_thumbnail_id( $post_id );
			if ( ! $foto ) {
				$faltan[] = 'Agregar una foto (Imagen destacada).';
			} elseif ( ! pm_campo( 'foto_autor', $foto ) ) {
				$faltan[] = 'Cargar el autor de la foto (biblioteca de medios → la foto → "Autor de la foto").';
			}
		}
	}

	if ( 'es' !== $idioma && ! pm_buscar( $tipo, $clave, 'es' ) ) {
		$faltan[] = "Crear la versión en español con la clave \"{$clave}\" (de ahí salen la foto y la ubicación).";
	}

	if ( mb_strlen( (string) pm_campo( 'seo_description', $post_id ) ) < 70 ) {
		$faltan[] = 'Alargar la descripción para Google (ideal: 70 a 160 caracteres).';
	}

	return $faltan;
}

/** Drop rows (FAQs, calendar, waters) that still carry a PENDIENTE note. */
function pm_sin_pendientes( array $filas ): array {
	return array_values( array_filter( $filas, fn( $f ) => ! str_contains( wp_json_encode( $f ), 'PENDIENTE' ) ) );
}

// Public pages never show internal notes: blocks/paragraphs with PENDIENTE go.
add_filter( 'the_content', function ( $html ) {
	if ( is_admin() || ! str_contains( $html, 'PENDIENTE' ) ) {
		return $html;
	}
	$html = preg_replace( '#<blockquote\b[^>]*>(?:(?!</blockquote>).)*PENDIENTE(?:(?!</blockquote>).)*</blockquote>#s', '', $html );
	$html = preg_replace( '#<(p|li)\b[^>]*>(?:(?!</\1>).)*PENDIENTE(?:(?!</\1>).)*</\1>#s', '', $html );
	return $html;
}, 20 );
