<?php
/**
 * Isoró Fishing Camp (Goya), 24/10/2026: a HIDDEN draft record, to be completed next session.
 *
 * What is solid: the name, that it is in Goya, that it is a different operator from Experiencia Isoró,
 * and (owner) that it is a big one with a successful Instagram profile. The long profile text the owner
 * pasted (named "Agustin Enrique", with packages, boat, "verified @isorofishingcamp") has an unknown origin
 * (possibly written by an AI) and the owner later said Agus belongs to Experiencia Isoró, so it is kept in the
 * internal notes only and NOT turned into public fields. The record stays marked "Datos de ejemplo", so
 * visitors do not see it. To publish: confirm the data, untick "Datos de ejemplo", tick "Listado con
 * información pública" (or the operator's consent).
 *
 * Idempotent: if a record with this name exists it is left alone. Dry run first ($aplicar = false).
 */

$aplicar = true;
$nombre  = 'Isoró Fishing Camp';
$notas   = "BORRADOR OCULTO (24/10/2026). Prestador distinto de Experiencia Isoró. Según el dueño: es uno grande, con un perfil exitoso en Instagram. Falta todo lo demás: qué ofrece, precios, contacto, fotos cedidas y permiso.\n"
	. "TEXTO RECIBIDO DEL DUEÑO (origen desconocido, posiblemente generado por una IA; NO verificado; varios datos parecen inventados; además lo atribuía a 'Agustin Enrique' y el dueño aclaró que Agus es de Experiencia Isoró):\n"
	. "- Título 'Isoró Fishing Camp', base en Goya, Corrientes (río Paraná y esteros del Isoró). Foco: dorado y surubí con mosca y baitcast.\n"
	. "- Expediciones guiadas, campamentos remotos en islas y salidas técnicas de día; recomendado 2 pescadores por guía y lancha; bote tipo skiff con plataforma de casteo en proa y motor eléctrico; devolución estricta de las especies deportivas.\n"
	. "- Especies: dorado, surubí, tararira, pacú. Técnicas: mosca (a la vista), baitcast, carnada.\n"
	. "- Paquetes: día completo Paraná e Isoró, 8 a 9 horas (bote, combustible, guía, almuerzo, bebidas, hielo; sin permiso provincial ni equipo de mosca); expedición de campamento de 3 a 5 días todo incluido (alojamiento en campamento de isla, comidas, traslados desde el puerto de Goya; sin traslados hasta Goya, equipo personal ni propinas).\n"
	. "- El texto marcaba como verificados el contacto y el Instagram '@isorofishingcamp': nadie lo comprobó.\n"
	. 'PRÓXIMA SESIÓN: ver su Instagram, pedirles datos y fotos cedidas; después completar la ficha y publicarla.';

$ya = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 's' => 'Fishing Camp', 'posts_per_page' => 5 ] );
if ( $ya ) {
	foreach ( $ya as $p ) {
		WP_CLI::log( "EXISTE #{$p->ID} '{$p->post_title}' [{$p->post_status}]" );
	}
	WP_CLI::success( 'Ya hay una ficha parecida: no se crea otra.' );
	return;
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": crear '$nombre' (goya, operador, oculto como borrador)." );
if ( ! $aplicar ) {
	return;
}
$id = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
if ( is_wp_error( $id ) ) {
	WP_CLI::error( $id->get_error_message() );
}
foreach ( [ 'tipo' => [ 'operador' ], 'prest_destino' => 'goya', 'localidad' => 'Goya', 'estado' => 'relevado', 'datos_ejemplo' => 1, 'prest_consentimiento' => 0, 'prest_publico' => 0, 'notas' => $notas ] as $k => $v ) {
	update_field( $k, $v, $id );
}
$ok = 1 === (int) get_field( 'datos_ejemplo', $id ) && 'goya' === get_field( 'prest_destino', $id );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #$id " . get_the_title( $id ) . ' (oculta)' );
$ok || WP_CLI::error( 'La lectura de vuelta no coincide.' );
WP_CLI::success( 'Isoró Fishing Camp guardado como borrador oculto.' );
