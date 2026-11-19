<?php
/**
 * River House (Río Gallegos): structured fields of the card, 19/11/2026. The card (river-house-2026-11-15) has agreement + consent but its structured
 * fields are empty, so the «En una mirada» table of /guias-de-pesca/rio-gallegos/ (it only lists a row that has format, includes, season or a dated price)
 * leaves it out. This fills ONLY what is already documented in the repo; nothing new is claimed.
 * Sources (all in the repo): docs/prestadores.md, «River House en el sitio (15/11/2026)» and «Acuerdos con operadores» (the lodge description «semanas y días de pesca guiados» and what it said by email and in its
 * brochure: different stretches of the Río Gallegos and its tributaries each day depending on the river, local guides, rents a full set of tackle, arranges the
 * fishing licence from a copy of the passport, sells flies at the lodge); wordpress/scripts/river-house-2026-11-15.php (the same facts, field by field);
 * wordpress/scripts/karku-acuerdo-2026-11-13.php (the season wording: official river season 1 Nov to 30 May, same field prest_temporada).
 * Fields (only if EMPTY, never overwritten): prest_programa (+_pt/_en), prest_temporada (+_pt/_en), prest_modalidades = mosca, incluye = licencia
 * («Trámite de licencia»: the lodge ARRANGES it; the doc says it does NOT say the cost is included, and the label says «trámite», not «incluida»).
 * Equipment is RENTED: it goes in the programme text («alquiler de equipo completo»); the options `equipos` («Equipos de pesca prestados») and `equipo` are NOT touched.
 * NOT written: price (stays «a consultar», by agreement), contacts, links, commission, capacity, languages, season of its own weeks.
 * Modality «mosca»: asked by the owner on 19/11/2026; the docs only show it sells flies and rents rods, reels, lines and waders.
 * Dry run first ($aplicar = false): prints each field with its current value. Run with wp-script.yml (script = river-house-campos-2026-11-19).
 */

$aplicar = false;
$nombre  = 'River House';

$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
1 === count( $ids ) || WP_CLI::error( 'Se esperaba una sola ficha "' . $nombre . '", hay ' . count( $ids ) );
$pid = (int) $ids[0];
( 'rio-gallegos' === (string) pm_campo( 'prest_destino', $pid ) ) || WP_CLI::error( 'la ficha no está en rio-gallegos' );
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha $nombre #$pid (consentimiento=" . (int) pm_campo( 'prest_consentimiento', $pid ) . ' estado=' . (string) pm_campo( 'estado', $pid ) . ')' );

$campos = [
	'prest_programa'      => 'Estadías por semana y días de pesca con guía local en distintos tramos del Gallegos y afluentes, según el río; alquiler de equipo completo',
	'prest_programa_pt'   => 'Estadias por semana e dias de pesca com guia local em diferentes trechos do Gallegos e afluentes, conforme o rio; aluguel de equipamento completo',
	'prest_programa_en'   => 'Week-long stays and fishing days with a local guide on different stretches of the Gallegos and its tributaries, depending on the river; full tackle rental',
	'prest_temporada'     => 'Temporada oficial del río: del 1 de noviembre al 30 de mayo, según el reglamento',
	'prest_temporada_pt'  => 'Temporada oficial do rio: de 1º de novembro a 30 de maio, conforme o regulamento',
	'prest_temporada_en'  => 'Official river season: 1 November to 30 May, according to the regulations',
	'prest_modalidades'   => [ 'mosca' ],
	'incluye'             => [ 'licencia' ],
];

// Public texts: no price, contact, link, percentage or the word «operador» (checked before anything is written).
$prohibido = '/\d[\d.,]{2,}|US\$|USD|\d ?%|comisi|commission|comiss|@|https?:|www\.|\+\d|\btel[eé]fono|whatsapp|\boperador|\boperator/iu';
foreach ( $campos as $k => $v ) {
	if ( is_string( $v ) && preg_match( $prohibido, $v, $m ) ) {
		WP_CLI::error( "el texto de $k lleva algo que no se publica: «{$m[0]}»" );
	}
}
$opciones = pm_ui()['opciones'] ?? [];
foreach ( [ 'prest_modalidades' => 'modalidad', 'incluye' => 'incluye' ] as $k => $lista ) {
	foreach ( $campos[ $k ] as $clave ) {
		isset( $opciones[ $lista ][ $clave ] ) || WP_CLI::error( "la opción «$clave» no existe en la lista $lista" );
	}
}
in_array( 'equipos', $campos['incluye'], true ) && WP_CLI::error( 'el equipo es alquilado: nunca va como incluido' );

$vacio  = fn( $v ) => null === $v || false === $v || '' === $v || [] === $v || ( is_string( $v ) && '' === trim( $v ) );
$ver    = fn( $v ) => is_array( $v ) ? implode( ',', $v ) : (string) $v;
$escribe = [];
foreach ( $campos as $k => $nuevo ) {
	$antes = pm_campo( $k, $pid );
	if ( $vacio( $antes ) ) {
		WP_CLI::log( "  + $k: " . $ver( $nuevo ) );
		$escribe[ $k ] = $nuevo;
	} else {
		WP_CLI::log( "  = $k ya tiene valor, no se toca: " . $ver( $antes ) );
	}
}
// The price must stay empty: the script does not touch it and refuses to go on if somebody filled it with a figure.
$precio = (string) pm_campo( 'precio', $pid );
WP_CLI::log( '  precio (no se toca): "' . $precio . '" fecha "' . (string) pm_campo( 'precio_fecha', $pid ) . '"' );

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada (' . count( $escribe ) . ' campos vacíos se llenarían)' );
	return;
}

foreach ( $escribe as $k => $v ) {
	update_field( $k, $v, $pid );
}
wp_cache_flush();

// ---- read back -----------------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $campos as $k => $nuevo ) {
	$leido = pm_campo( $k, $pid );
	$b     = isset( $escribe[ $k ] ) ? $ver( $leido ) === $ver( $nuevo ) : ! $vacio( $leido );
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» #$pid $k = " . $ver( $leido ) );
	$ok = $ok && $b;
}
$r = null;
foreach ( pm_prestadores_destino( 'rio-gallegos' ) as $x ) {
	if ( (int) $x['id'] === $pid ) {
		$r = $x;
	}
}
$b = $r && '' !== (string) $r['programa'] && '' !== (string) $r['temporada'] && in_array( 'mosca', $r['modalidades'], true )
	&& ! in_array( 'equipos', $r['incluye'], true ) && ! in_array( 'equipos', $r['equipo'], true )
	&& '' === (string) $r['precio'] && '' === (string) $r['precio_fecha'];
WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": ficha pública de {$nombre}: programa, temporada y modalidad leídos; equipo NO figura como incluido; precio vacío" );
$ok = $ok && $b;
WP_CLI::log( $ok ? 'VERIFIED: todo leído de vuelta' : 'FALLÓ: hay campos que no coinciden (ver arriba)' );
