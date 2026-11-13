<?php
// Card fields for lodges (13/11/2026): programme, minimum stay, companion, not included, capacity unit.
// Renders the real card partial in es/en/pt, asserts the strings, cleans up its throwaway posts.
$ver = function ( bool $ok, string $msg ) { if ( ! $ok ) { WP_CLI::error( "✖ prestador-campos: $msg" ); } WP_CLI::log( "✓ $msg" ); };
$ids = [];
$nuevo = function ( string $titulo, array $campos ) use ( &$ids ) {
	$id = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $titulo ] );
	$ids[] = $id;
	foreach ( $campos + [ 'tipo' => [ 'alojamiento' ], 'prest_destino' => 'esquina', 'prest_publico' => 1 ] as $k => $v ) { update_field( $k, $v, $id ); }
	return $id;
};
$tarjeta = function ( int $id, string $i ) {
	pm_lang( $i );
	ob_start();
	get_template_part( 'parciales/prestador', null, pm_prestador( $id ) + [ 'lugar' => 'Esquina' ] );
	$h = ob_get_clean();
	pm_lang( 'es' );
	return preg_replace( '/\s+/', ' ', wp_strip_all_tags( str_replace( [ '</dt>', '</dd>' ], [ ': ', ' | ' ], $h ) ) );
};
$hoy = gmdate( 'Y-m-d' );
$lleno = $nuevo( 'PRESTADOR-CAMPOS-LLENO', [
	'capacidad' => 8, 'prest_capacidad_unidad' => 'semana', 'precio' => 'Desde US$ 4.000', 'precio_fecha' => $hoy,
	'prest_programa' => 'PROG-ES', 'prest_programa_en' => 'PROG-EN', 'prest_programa_pt' => 'PROG-PT',
	'prest_estadia_minima' => 'MIN-ES', 'prest_estadia_minima_en' => 'MIN-EN', 'prest_estadia_minima_pt' => 'MIN-PT',
	'prest_acompanante' => 'ACOMP-ES', 'prest_acompanante_en' => 'ACOMP-EN', 'prest_acompanante_pt' => 'ACOMP-PT',
	'prest_no_incluye' => 'NOINC-ES', 'prest_no_incluye_en' => 'NOINC-EN', 'prest_no_incluye_pt' => 'NOINC-PT',
] );
$esp = [
	'es' => [ 'Programa: PROG-ES', 'Estadía mínima: MIN-ES', 'Acompañante que no pesca: ACOMP-ES', 'No incluye: NOINC-ES', 'Recibe 8 pescadores por semana', 'Capacidad: Recibe' ],
	'en' => [ 'Programme: PROG-EN', 'Minimum stay: MIN-EN', 'Non-fishing companion: ACOMP-EN', 'Not included: NOINC-EN', 'Hosts 8 anglers per week' ],
	'pt' => [ 'Programa: PROG-PT', 'Estadia mínima: MIN-PT', 'Acompanhante que não pesca: ACOMP-PT', 'Não inclui: NOINC-PT', 'Recebe 8 pescadores por semana' ],
];
foreach ( $esp as $i => $frases ) {
	$t = $tarjeta( $lleno, $i );
	foreach ( $frases as $f ) { if ( 'Capacidad: Recibe' === $f ) { continue; } $ver( str_contains( $t, $f ), "$i: la tarjeta lleva «{$f}»" ); }
	$ver( ! str_contains( $t, 'por lancha' ) && ! str_contains( $t, 'per boat' ) && ! str_contains( $t, 'por barco' ), "$i: un lodge por semana no dice «por lancha»" );
}
$ver( str_contains( $tarjeta( $lleno, 'es' ), 'ACOMP-ES (precio revisado' ), 'es: el acompañante lleva la fecha del precio' );
$ver( str_contains( $tarjeta( $lleno, 'en' ), 'ACOMP-EN (price checked' ), 'en: el acompañante lleva la fecha del precio' );
$ver( str_contains( $tarjeta( $lleno, 'pt' ), 'ACOMP-PT (preço revisado' ), 'pt: el acompañante lleva la fecha del precio' );
// Orden: after the included list, in the card.
$ver( strpos( $tarjeta( $lleno, 'es' ), 'Programa:' ) < strpos( $tarjeta( $lleno, 'es' ), 'No incluye:' ), 'es: Programa antes de No incluye' );

// Companion without the price date: no figure, "a consultar".
$sinfecha = $nuevo( 'PRESTADOR-CAMPOS-SINFECHA', [ 'prest_acompanante' => 'ACOMP-SIN-FECHA', 'prest_acompanante_en' => 'ACOMP-SIN-FECHA-EN' ] );
foreach ( [ 'es' => 'Acompañante que no pesca: a consultar', 'en' => 'Non-fishing companion: on request', 'pt' => 'Acompanhante que não pesca: sob consulta' ] as $i => $f ) {
	$t = $tarjeta( $sinfecha, $i );
	$ver( str_contains( $t, $f ) && ! str_contains( $t, 'ACOMP-SIN-FECHA' ), "$i: sin fecha del precio el acompañante dice «a consultar» y no muestra el monto" );
}

// Capacity units.
foreach ( [ 'semana' => [ 'es' => 'Recibe 6 pescadores por semana', 'en' => 'Hosts 6 anglers per week', 'pt' => 'Recebe 6 pescadores por semana' ],
	'total' => [ 'es' => 'Capacidad: 6 pescadores', 'en' => 'Capacity: 6 anglers', 'pt' => 'Capacidade: 6 pescadores' ],
	'lancha' => [ 'es' => 'Hasta 6 pescadores por lancha', 'en' => 'Up to 6 anglers per boat', 'pt' => 'Até 6 pescadores por barco' ],
	'' => [ 'es' => 'Hasta 6 pescadores por lancha', 'en' => 'Up to 6 anglers per boat', 'pt' => 'Até 6 pescadores por barco' ] ] as $u => $por_idioma ) {
	$id = $nuevo( "PRESTADOR-CAMPOS-CAP-$u", [ 'tipo' => [ 'guia' ], 'capacidad' => 6 ] + ( '' === $u ? [] : [ 'prest_capacidad_unidad' => $u ] ) );
	foreach ( $por_idioma as $i => $f ) { $ver( str_contains( $tarjeta( $id, $i ), $f ), "capacidad «" . ( $u ?: 'sin unidad' ) . "» en $i: $f" ); }
}

// A card without any new field: nothing new rendered, default capacity wording as before.
$viejo = $nuevo( 'PRESTADOR-CAMPOS-VIEJO', [ 'tipo' => [ 'guia' ], 'capacidad' => 4, 'precio' => 'Desde $ 1', 'precio_fecha' => $hoy ] );
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$t = $tarjeta( $viejo, $i );
	foreach ( [ 'prest.programa', 'prest.estadia', 'prest.acompanante', 'prest.no_incluye', 'prest.capacidad' ] as $k ) { $ver( ! str_contains( $t, $k ), "$i: ficha vieja sin clave sin traducir $k" ); }
	foreach ( array_map( fn( $k ) => pm_t( $k, [], $i ), [ 'prest.programa', 'prest.estadia', 'prest.acompanante', 'prest.no_incluye', 'prest.capacidad_fila' ] ) as $etq ) { $ver( ! str_contains( $t, "$etq:" ), "$i: ficha vieja sin fila «{$etq}»" ); }
}
$ver( str_contains( $tarjeta( $viejo, 'es' ), 'Hasta 4 pescadores por lancha' ), 'es: capacidad por defecto igual que antes' );

// Translation missing: Spanish text, marked lang="es" on the en page.
$parcial = $nuevo( 'PRESTADOR-CAMPOS-FALLBACK', [ 'prest_programa' => 'SOLO-ES-PROG', 'prest_no_incluye' => 'SOLO-ES-NOINC', 'prest_estadia_minima' => 'SOLO-ES-MIN', 'prest_estadia_minima_en' => 'MIN-EN-OK' ] );
pm_lang( 'en' ); ob_start(); get_template_part( 'parciales/prestador', null, pm_prestador( $parcial ) ); $h = ob_get_clean(); pm_lang( 'es' );
$ver( str_contains( $h, 'lang="es">SOLO-ES-PROG' ) && str_contains( $h, 'lang="es">SOLO-ES-NOINC' ), 'en: sin traducción cae al español marcado lang="es"' );
$ver( str_contains( $h, 'MIN-EN-OK' ) && ! str_contains( $h, 'SOLO-ES-MIN' ), 'en: con traducción usa la traducción' );

// llms.txt: programme + minimum stay only.
$ll = pm_llms_txt();
$ver( str_contains( $ll, 'programa: PROG-ES; estadía mínima: MIN-ES' ) && ! str_contains( $ll, 'NOINC-ES' ) && ! str_contains( $ll, 'ACOMP-ES' ), 'llms.txt: programa y estadía mínima, sin acompañante ni no incluye' );

foreach ( $ids as $x ) { wp_delete_post( $x, true ); }
WP_CLI::success( 'campos de ficha de prestador: ok' );
