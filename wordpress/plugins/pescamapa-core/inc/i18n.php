<?php
/**
 * Languages without a translation plugin: every destination and species is
 * one post per language, sharing a `clave`. Spanish lives at the root, Brazilian
 * Portuguese under /pt/, English under /en/. Interface strings come from
 * data/ui.json (edit that file to change a label).
 */

defined( 'ABSPATH' ) || exit;

function pm_ui(): array {
	static $ui = null;
	return $ui ??= json_decode( file_get_contents( PM_DIR . 'data/ui.json' ), true );
}

/** @return string[] es, pt, en */
function pm_idiomas(): array {
	return pm_ui()['idiomas'];
}

function pm_idioma_valido( ?string $i ): string {
	return in_array( $i, pm_idiomas(), true ) ? $i : 'es';
}

/** Language of the current request. Set by the router; Spanish otherwise. */
function pm_lang( ?string $nuevo = null ): string {
	static $actual = 'es';
	if ( null !== $nuevo ) {
		$actual = pm_idioma_valido( $nuevo );
	}
	return $actual;
}

function pm_meta_idioma( ?string $idioma = null ): array {
	return pm_ui()['meta'][ $idioma ?? pm_lang() ];
}

/** Interface string. Placeholders: %lugar% etc. */
function pm_t( string $clave, array $vars = [], ?string $idioma = null ): string {
	$texto = pm_ui()['textos'][ $idioma ?? pm_lang() ][ $clave ] ?? $clave;
	$reemplazos = [];
	foreach ( $vars as $k => $v ) {
		$reemplazos[ "%{$k}%" ] = $v;
	}
	return strtr( $texto, $reemplazos );
}

/** Chip label for a species/technique key on planned destinations. */
function pm_etiqueta( string $clave, ?string $idioma = null ): string {
	return pm_ui()['etiquetas'][ $clave ][ $idioma ?? pm_lang() ] ?? $clave;
}

// --- URLs ------------------------------------------------------------------

const PM_SEGMENTOS = [
	'es' => [ 'especies' => 'especies', 'mapa' => 'mapa', 'creditos' => 'creditos' ],
	'pt' => [ 'especies' => 'especies', 'mapa' => 'mapa', 'creditos' => 'creditos' ],
	'en' => [ 'especies' => 'species', 'mapa' => 'map', 'creditos' => 'credits' ],
];

function pm_url( string $ruta, string $idioma ): string {
	$prefijo = pm_meta_idioma( $idioma )['prefijo'];
	return home_url( user_trailingslashit( $prefijo . '/' . ltrim( $ruta, '/' ) ) );
}

function pm_url_inicio( string $i ): string {
	return home_url( '' === pm_meta_idioma( $i )['prefijo'] ? '/' : pm_meta_idioma( $i )['prefijo'] . '/' );
}
function pm_url_mapa( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['mapa'], $i );
}
function pm_url_creditos( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['creditos'], $i );
}
function pm_url_especie( string $clave, string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['especies'] . '/' . $clave, $i );
}

/** Same view in every language (home, map, credits). */
function pm_alternas_fijas( callable $url ): array {
	$out = [];
	foreach ( pm_idiomas() as $i ) {
		$out[ $i ] = $url( $i );
	}
	return $out;
}

// html lang="" follows the request language.
add_filter( 'language_attributes', function ( $salida ) {
	return preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( pm_meta_idioma()['html'] ) . '"', $salida );
} );

add_filter( 'locale', function ( $locale ) {
	if ( is_admin() ) {
		return $locale;
	}
	return [ 'es' => 'es_AR', 'pt' => 'pt_BR', 'en' => 'en_US' ][ pm_lang() ] ?? $locale;
} );

/** Localised date, e.g. "19 de octubre de 2026". */
function pm_fecha( ?string $ymd, ?string $idioma = null ): string {
	if ( ! $ymd ) {
		return '';
	}
	$ts = strtotime( $ymd . ' 12:00:00 UTC' );
	$meses = [
		'es' => [ 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' ],
		'pt' => [ 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro' ],
		'en' => [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ],
	];
	$i = $idioma ?? pm_lang();
	$d = (int) gmdate( 'j', $ts );
	$m = $meses[ $i ][ (int) gmdate( 'n', $ts ) - 1 ];
	$y = gmdate( 'Y', $ts );
	return 'en' === $i ? "$d $m $y" : "$d de $m de $y";
}
