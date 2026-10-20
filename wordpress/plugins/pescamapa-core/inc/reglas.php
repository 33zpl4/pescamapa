<?php
/**
 * Trust rules, same as the Astro build used to enforce:
 * a guide or species can only be "publicado" (indexable) when
 *   - at least one source is official,
 *   - a person verified the current regulation less than a year ago (guides),
 *   - no "PENDIENTE" mark remains in the text or the fields,
 *   - the Spanish version is published (for translations),
 *   - the shared fields and the photo credit are complete (Spanish guides).
 * If any is missing on save, the ficha goes back to "borrador" and the form's
 * "Chequeo de publicación" box lists what is missing. The site never breaks.
 */

defined( 'ABSPATH' ) || exit;

const PM_VIGENCIA_REGLAMENTO_DIAS = 365;

/** @return string[] human-readable list of what blocks publication. */
function pm_que_falta_para_publicar( int $post_id ): array {
	$tipo = get_post_type( $post_id );
	if ( ! in_array( $tipo, [ 'pm_destino', 'pm_especie' ], true ) ) {
		return [];
	}
	$faltan = [];
	$idioma = pm_idioma_de( $post_id );
	$clave  = pm_clave_de( $post_id );
	$fuentes = pm_filas( 'fuentes', $post_id );

	if ( ! array_filter( $fuentes, fn( $f ) => ! empty( $f['oficial'] ) ) ) {
		$faltan[] = 'Al menos una fuente marcada como oficial.';
	}

	$textos = get_post_field( 'post_content', $post_id ) . wp_json_encode( get_post_meta( $post_id ) );
	if ( str_contains( $textos, 'PENDIENTE' ) ) {
		$faltan[] = 'Quedan marcas "PENDIENTE" en el texto o en algún campo.';
	}

	if ( 'pm_destino' === $tipo ) {
		$verificado = (string) pm_campo( 'reglamento_verificado', $post_id );
		if ( ! $verificado ) {
			$faltan[] = 'La fecha en que una persona verificó el reglamento vigente (pestaña Reglamento).';
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
			if ( $foto && ! pm_campo( 'foto_autor', $foto ) ) {
				$faltan[] = 'El autor de la foto destacada (en la biblioteca de medios → la foto → "Autor de la foto").';
			}
		}
	}

	if ( 'es' !== $idioma ) {
		$fuente = pm_buscar( $tipo, $clave, 'es' );
		if ( ! $fuente ) {
			$faltan[] = "No existe la versión en español con la clave \"{$clave}\".";
		} elseif ( 'publicado' !== pm_campo( 'estado', $fuente->ID ) ) {
			$faltan[] = 'Publicar primero la versión en español.';
		}
	}

	$desc = (string) pm_campo( 'seo_description', $post_id );
	if ( mb_strlen( $desc ) < 70 ) {
		$faltan[] = 'La descripción para Google tiene que tener al menos 70 caracteres.';
	}

	return $faltan;
}

// After the form is saved: a "publicado" that doesn't qualify goes back to draft.
add_action( 'acf/save_post', function ( $post_id ) {
	if ( ! is_numeric( $post_id ) || ! in_array( get_post_type( (int) $post_id ), [ 'pm_destino', 'pm_especie' ], true ) ) {
		return;
	}
	if ( 'publicado' === pm_campo( 'estado', (int) $post_id ) && pm_que_falta_para_publicar( (int) $post_id ) ) {
		update_field( 'estado', 'borrador', (int) $post_id );
	}
}, 20 );

/** Is this page indexable? Drafts are visible but noindex. */
function pm_es_publicado( int $post_id ): bool {
	return 'publicado' === pm_campo( 'estado', $post_id ) && ! pm_que_falta_para_publicar( $post_id );
}
