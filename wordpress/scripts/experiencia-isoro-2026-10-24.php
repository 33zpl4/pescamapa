<?php
/**
 * Experiencia Isoró (Goya), 24/10/2026: real listing from the operator's own 2026 brochure
 * (PDF the owner sent). It REPLACES the example record "Agus — guía de pesca en Goya"
 * (placeholder data, hidden from visitors): same post, renamed and filled in. If that example
 * no longer exists, it looks for 'Experiencia Isoró' and updates it; never creates a second one,
 * and never overwrites a record that is not marked as example data unless it already is this one.
 *
 * Published (our words, from the brochure): what they offer, where, modalities, jornadas, packs,
 * boat, catch-and-release. NOT published: phones, e-mail, Instagram (internal notes only),
 * prices (none in the brochure), photos (the brochure's belong to them; ask for the ones we may use).
 * Kept as someone entered them on 20/10/2026 (dated price, capacity); equipo and incluye are cut back to
 * what the brochure supports (the earlier values are saved in the notes). Cleared here: motor (a marketing phrase) and
 * temporada ("Todo el año" needs a nuance). Not in the brochure: boat length, languages.
 * Dry run first ($aplicar = false); then apply with wp-script.yml (script = experiencia-isoro-2026-10-24).
 */

$aplicar = false;
$hoy     = '20261024';
$ejemplo = 'Agus — guía de pesca en Goya';
$nombre  = 'Experiencia Isoró';

$desc = 'Pesca deportiva con devolución y paseos náuticos por la Reserva Natural Isoró y el río Paraná, con base en Goya (la boca del Isoró queda 4 km aguas abajo del puerto). En la reserva, dorado con flycast, baitcast y spinning; en el Paraná, pesca con carnada anclada o a garete, también de noche. Media jornada o jornada completa (11 a 12 horas de pesca). Packs de un día de pesca, o de 1 a 7 noches de alojamiento con días de pesca, con alojamientos que los acompañan, entre ellos Ogaraity Apart Hotel. Atienden por su cuenta, también a quienes no se alojan con ellos. Paseos náuticos de hasta 5 personas para observar aves y fotografiar, de media jornada o de 8 a 10 horas.';
$notas = "Fuente: folleto 'Experiencia Isoró | Pesca & Naturaleza' (2026), enviado por el dueño el 24/10/2026. Material del propio prestador; todavía NO hablamos con ellos para el listado.\n"
	. "CONTACTO INTERNO (no se imprime): Tomás Frattini, WhatsApp +54 3777 603774; Augusto Frattini, WhatsApp +54 11 53840108; tomasfraa@gmail.com; Instagram @tomasfrattini.pesca. ('Agus' = Augusto, según el dueño.)\n"
	. "Lancha: Astillero La Paz (ALP), modelo Guaycurú 630; butacas o back to back, parabrisas, plataformas de casteo en proa y popa, cajones, luces de navegación. Motor: el folleto no lo dice.\n"
	. "Aliados que muestra el folleto ('Nos acompañan'): Condado Hotel Casino, Costa Apart & Hotel, Ogaraity Apart Hotel, Cabañas Refugio del Río, Coleman, ALP. Candidatos para más alojamientos.\n"
	. "Packs (sin precio): 1 día de pesca; 1+1, 2+1, 3+2, 4+3, 5+4 y 7+6 (días de alojamiento + días de pesca). Pedirles precios, temporada, idiomas y capacidad por lancha.\n"
	. "Fotos: las del folleto son de ellos; pedir permiso y las originales (crédito del autor). Instagram grande y activo según el dueño (no verificado por nosotros).";

$notas .= "\nValores previos de equipo e incluye (carga del 20/10, sin confirmar; el folleto no los respalda): equipo = lancha completa, motor eléctrico, sonda y GPS, equipos prestados, chalecos; incluye = carnada, comida, bebidas, combustible, traslados, equipos de pesca, trámite de licencia. Restaurar solo si el prestador lo confirma.";
$notas .= "\nDueño (24/10/2026): Experiencia Isoró y Ogaraity son independientes; ambos ofrecen excursiones. Ogaraity las hace con un guía propio y también con Experiencia Isoró.";

$existentes = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $ejemplo, 'posts_per_page' => 5 ] );
if ( ! $existentes ) {
	$existentes = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 's' => 'Isoró', 'posts_per_page' => 5 ] );
}
if ( count( $existentes ) > 1 ) {
	WP_CLI::error( 'Hay más de una ficha candidata: decidir a mano cuál es.' );
}
if ( $existentes && ! get_field( 'datos_ejemplo', $existentes[0]->ID ) && $nombre !== $existentes[0]->post_title ) {
	WP_CLI::error( "La ficha #{$existentes[0]->ID} '{$existentes[0]->post_title}' no es de ejemplo: no se toca." );
}

$campos = [
	'tipo' => [ 'guia', 'operador' ], 'prest_destino' => 'goya', 'localidad' => 'Goya', 'descripcion' => $desc,
	'prest_modalidades' => [ 'mosca', 'spinning', 'carnada' ], 'prest_especies' => [ 'dorado' ],
	'lancha' => 'Astillero La Paz (ALP), modelo Guaycurú 630, con plataformas de casteo en proa y popa', 'motor' => '',
	'prest_temporada' => '', 'equipo' => [ 'plataforma' ], 'incluye' => [],
	'prest_publico' => 1, 'estado' => 'relevado', 'verificado' => $hoy, 'datos_ejemplo' => 0, 'notas' => $notas,
];

if ( $existentes ) {
	$id = $existentes[0]->ID;
	foreach ( $campos as $k => $v ) {
		WP_CLI::log( "  $k: " . wp_trim_words( wp_json_encode( get_field( $k, $id ), JSON_UNESCAPED_UNICODE ), 12 ) . '  →  ' . wp_trim_words( wp_json_encode( $v, JSON_UNESCAPED_UNICODE ), 12 ) );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": actualizar #$id '{$existentes[0]->post_title}' → '$nombre'. fotos=" . wp_json_encode( get_field( 'fotos', $id ) ) . ' consentimiento=' . wp_json_encode( get_field( 'prest_consentimiento', $id ) ) );
} else {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": crear '$nombre' (goya, guía y operador, listado público)." );
}
// Ogaraity: its fishing trips are its own guide's and Experiencia Isoró's (owner, 24/10/2026).
$oga = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 's' => 'Ogaraity', 'posts_per_page' => 5 ] );
if ( 1 === count( $oga ) ) {
	$oga_id   = $oga[0]->ID;
	$oga_desc = 'Apart hotel en Goya (Av. Millán Medina 7410), con vistas al río según su ficha de Booking, donde figura entre los mejor valorados (octubre de 2026). Ofrece excursiones de pesca con un guía propio y también con Experiencia Isoró, que además atiende por su cuenta. Departamentos con cocina equipada, aire acondicionado, baño privado, terraza o balcón y jardín; piscina al aire libre (de temporada), estacionamiento privado y wifi gratis, desayuno disponible; admite mascotas bajo petición. Atienden en español, inglés y portugués.';
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": Ogaraity #$oga_id descripcion → " . wp_trim_words( $oga_desc, 30 ) );
	if ( $aplicar ) {
		update_field( 'descripcion', $oga_desc, $oga_id );
		$ok2 = false !== strpos( (string) get_field( 'descripcion', $oga_id ), 'Experiencia Isoró' );
		WP_CLI::log( ( $ok2 ? 'VERIFIED' : 'FALLÓ' ) . ": Ogaraity #$oga_id" );
		$ok2 || WP_CLI::error( 'Ogaraity: la lectura de vuelta no coincide.' );
	}
}

if ( ! $aplicar ) {
	return;
}
if ( $existentes ) {
	$id = $existentes[0]->ID;
	wp_update_post( [ 'ID' => $id, 'post_title' => $nombre, 'post_name' => 'experiencia-isoro' ] );
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
$ok = $nombre === get_the_title( $id ) && 'goya' === get_field( 'prest_destino', $id ) && 1 === (int) get_field( 'prest_publico', $id ) && 0 === (int) get_field( 'datos_ejemplo', $id ) && in_array( 'dorado', (array) get_field( 'prest_especies', $id ), true );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #$id " . get_the_title( $id ) . ' fotos=' . wp_json_encode( get_field( 'fotos', $id ) ) );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( 'Experiencia Isoró lista (listado público, sin verificar con el prestador).' );
