<?php
/**
 * Apart Ogaraity Hotel (Goya), 24/10/2026: first lodging listing, as a "listado con
 * información pública" (docs/prestadores.md; model in docs/modelo-alojamientos.md).
 *
 * Facts come from the Booking.com listing the owner pasted (24/10/2026). Published: what
 * the property is and offers, in our words, with the source link. NOT published: the phone
 * (internal), the Booking score and reviews (not ours to copy), prices (none given),
 * Booking photos. Photos will be the ones the property gives us (owner: "fotos dadas por ellos").
 * Owner confirmed (24/10/2026) that they run fishing excursions and that photos are theirs,
 * ceded; he also allows "uno de los mejor valorados en Booking" (published without a number).
 *
 * The record already existed (#46 'Ogaraity Apart Hotel', published, with placeholder data marked
 * "Datos de ejemplo" and 4 photos): this script UPDATES it, never creates a second one.
 * Idempotent: running it again rewrites the same values.
 * (old line) Dry run first
 * (set $aplicar = false), then apply with wp-script.yml (script = ogaraity-2026-10-24).
 */

$aplicar = true;
$nombre  = 'Apart Ogaraity Hotel';
$hoy     = '20261024';

$desc = 'Apart hotel en Goya (Av. Millán Medina 7410), con vistas al río según su ficha de Booking, donde figura entre los mejor valorados (octubre de 2026). Organizan excursiones de pesca. Departamentos con cocina equipada, aire acondicionado, baño privado, terraza o balcón y jardín; piscina al aire libre (de temporada), estacionamiento privado y wifi gratis, desayuno disponible; admite mascotas bajo petición. Atienden en español, inglés y portugués.';
$notas = "Listado con información pública a partir de la ficha de Booking pegada por el dueño (24/10/2026); Pescamapa no abrió el enlace (el entorno no llega a booking.com).\n"
	. "Contacto INTERNO (no se imprime): (contacto interno fuera del repo)\n"
	. "Confirmado por el dueño de Pescamapa (24/10/2026): hacen excursiones de pesca; \"uno de los mejor valorados en Booking\" (se publica sin cifra). Fotos cedidas por el prestador (3 recibidas en el chat; subirlas en wp-admin con su autor). Falta hablar con ellos: cómo organizan las salidas (lancha propia o guía), especies, precios (hoy 'a consultar'), condición especial por Pescamapa.\n"
	. "Puntaje y reseñas de Booking: no se copian (ni el 9,9). La foto 'pic-2' de la carpeta parece de una web: usar solo las que el prestador cedió.\n"
	. 'Accesibilidad: Booking dice unidad accesible en silla de ruedas pero plantas superiores solo por escalera: ambiguo, no se marca.';

$existentes = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 's' => 'Ogaraity', 'posts_per_page' => 20 ] );
if ( count( $existentes ) > 1 ) {
	WP_CLI::error( 'Hay más de una ficha con "Ogaraity": decidir a mano cuál es.' );
}
$campos = [
	'tipo' => [ 'alojamiento', 'operador' ], 'prest_destino' => 'goya', 'localidad' => 'Goya', 'descripcion' => $desc,
	'incluye' => [ 'alojamiento' ], 'prest_idiomas' => [ 'es', 'en', 'pt' ],
	'prest_publico' => 1, 'prest_fuente' => 'https://www.booking.com/hotel/ar/ogaraity-goya.es.html',
	'estado' => 'relevado', 'verificado' => $hoy, 'datos_ejemplo' => 0, 'notas' => $notas,
];
// Not touched on purpose: fotos (already 4, ceded), prest_consentimiento (someone ticked it), title.
if ( $existentes ) {
	$id = $existentes[0]->ID;
	foreach ( $campos as $k => $v ) {
		WP_CLI::log( "  $k: " . wp_trim_words( wp_json_encode( get_field( $k, $id ), JSON_UNESCAPED_UNICODE ), 14 ) . '  →  ' . wp_trim_words( wp_json_encode( $v, JSON_UNESCAPED_UNICODE ), 14 ) );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": actualizar #$id '{$existentes[0]->post_title}'. fotos=" . wp_json_encode( get_field( 'fotos', $id ) ) . ' consentimiento=' . wp_json_encode( get_field( 'prest_consentimiento', $id ) ) );
} else {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": crear '$nombre' (goya, alojamiento, listado público)." );
}
if ( ! $aplicar ) {
	return;
}
if ( $existentes ) {
	$id = $existentes[0]->ID;
} else {
	$id = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos['prest_consentimiento'] = 0;
}
foreach ( $campos as $k => $v ) {
	update_field( $k, $v, $id );
}
// Read back.
$ok = 'alojamiento' === ( get_field( 'tipo', $id )[0] ?? '' ) && 'goya' === get_field( 'prest_destino', $id ) && 1 === (int) get_field( 'prest_publico', $id ) && 0 === (int) get_field( 'datos_ejemplo', $id );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #$id " . get_the_title( $id ) . ' fotos=' . wp_json_encode( get_field( 'fotos', $id ) ) );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( 'Ogaraity listo (listado público, sin verificar).' );
