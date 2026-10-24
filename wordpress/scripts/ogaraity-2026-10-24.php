<?php
/**
 * Apart Ogaraity Hotel (Goya), 24/10/2026: first lodging listing, as a "listado con
 * información pública" (docs/prestadores.md; model in docs/modelo-alojamientos.md).
 *
 * Facts come from the Booking.com listing the owner pasted (24/10/2026). Published: what
 * the property is and offers, in our words, with the source link. NOT published: the phone
 * (internal), the Booking score and reviews (not ours to copy), prices (none given),
 * Booking photos. Photos will be the ones the property gives us (owner: "fotos dadas por ellos").
 * Whether it runs fishing trips is NOT confirmed: Booking lists fishing as an off-site paid
 * activity, so the card says only that; the owner's word that they take guests fishing goes
 * to the internal notes until we hear it from them.
 *
 * Idempotent: a listing with this title is never touched. Dry run first
 * (set $aplicar = false), then apply with wp-script.yml (script = ogaraity-2026-10-24).
 */

$aplicar = false;
$nombre  = 'Apart Ogaraity Hotel';
$hoy     = '20261024';

if ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'fields' => 'ids' ] ) ) {
	WP_CLI::success( "$nombre ya existe: no se toca nada." );
	return;
}
$desc = 'Apart hotel en Goya (Av. Millán Medina 7410). Departamentos con cocina equipada, aire acondicionado, baño privado, terraza o balcón y jardín; piscina al aire libre (de temporada), estacionamiento privado y wifi gratis, desayuno disponible. Su ficha de Booking indica vistas al río y que admite mascotas bajo petición. Atienden en español, inglés y portugués. Booking lista la pesca como una actividad fuera del alojamiento y de pago: consultá con ellos cómo se organiza.';
$notas = "Listado con información pública a partir de la ficha de Booking pegada por el dueño (24/10/2026); Pescamapa no abrió el enlace (el entorno no llega a booking.com).\n"
	. "Contacto INTERNO (no se imprime): +54 3777 74-4653.\n"
	. "Dicho por el dueño: está junto al río y hace salidas de pesca seguido. NO confirmado por el prestador: preguntar cómo las organiza (lancha propia o guía), especies y precio, y recién ahí sumar el tipo 'Paquetes y salidas'.\n"
	. "Puntaje y reseñas de Booking: no se copian. Fotos: solo las que ceda el prestador (con crédito); la 'pic-2' de la carpeta parece de una web, no usar sin su permiso.\n"
	. 'Accesibilidad: Booking dice unidad accesible en silla de ruedas pero plantas superiores solo por escalera: ambiguo, no se marca.';

WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": crear '$nombre' (goya, alojamiento, listado público)." );
if ( ! $aplicar ) {
	return;
}
$id = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}
$campos = [
	'tipo' => [ 'alojamiento' ], 'prest_destino' => 'goya', 'localidad' => 'Goya', 'descripcion' => $desc,
	'incluye' => [ 'alojamiento' ], 'prest_idiomas' => [ 'es', 'en', 'pt' ],
	'prest_publico' => 1, 'prest_fuente' => 'https://www.booking.com/hotel/ar/ogaraity-goya.es.html',
	'estado' => 'relevado', 'verificado' => $hoy, 'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'notas' => $notas,
];
foreach ( $campos as $k => $v ) {
	update_field( $k, $v, $id );
}
// Read back.
$ok = 'alojamiento' === ( get_field( 'tipo', $id )[0] ?? '' ) && 'goya' === get_field( 'prest_destino', $id ) && 1 === (int) get_field( 'prest_publico', $id );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #$id " . get_the_title( $id ) );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( 'Ogaraity listo (listado público, sin verificar).' );
