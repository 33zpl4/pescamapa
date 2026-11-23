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

/**
 * Interface string with an optional per-destination override: `<base>.<clave>` wins over `<base>` when it exists in ui.json
 * (e.g. guias.title.esquel-trevelin). Used where one page needs its own title, H1, meta or intro without a new field.
 */
function pm_t_clave( string $base, string $clave, array $vars = [], ?string $idioma = null ): string {
	$i = $idioma ?? pm_lang();
	return isset( pm_ui()['textos'][ $i ][ "$base.$clave" ] ) ? pm_t( "$base.$clave", $vars, $i ) : pm_t( $base, $vars, $i );
}

/** Does ui.json carry this exact key in this language? (Optional texts: print nothing when absent.) */
function pm_t_existe( string $clave, ?string $idioma = null ): bool {
	return isset( pm_ui()['textos'][ $idioma ?? pm_lang() ][ $clave ] );
}

/** Chip label for a species/technique key on planned destinations. */
function pm_etiqueta( string $clave, ?string $idioma = null ): string {
	return pm_ui()['etiquetas'][ $clave ][ $idioma ?? pm_lang() ] ?? $clave;
}

// --- URLs ------------------------------------------------------------------

const PM_SEGMENTOS = [
	'es' => [ 'especies' => 'especies', 'mapa' => 'mapa', 'creditos' => 'creditos', 'entrevistas' => 'entrevistas', 'notas' => 'notas', 'pique' => 'pique', 'viaje' => 'armar-viaje', 'terminos' => 'terminos', 'privacidad' => 'privacidad', 'rio' => 'altura-del-rio', 'paquetes' => 'paquetes-de-pesca', 'guias' => 'guias-de-pesca', 'calendario' => 'calendario-de-pesca', 'paraguias' => 'para-guias-y-lodges', 'nosotros' => 'quienes-somos', 'presupuesto' => 'pedir-presupuesto-pesca', 'licencia' => 'calculadora-licencia-de-pesca', 'estimador' => 'cuanto-cuesta-viaje-de-pesca' ],
	'pt' => [ 'especies' => 'especies', 'mapa' => 'mapa', 'creditos' => 'creditos', 'entrevistas' => 'entrevistas', 'notas' => 'notas', 'pique' => 'pique', 'viaje' => 'planejar-viagem', 'terminos' => 'termos', 'privacidad' => 'privacidade', 'rio' => 'altura-do-rio', 'paquetes' => 'pacotes-de-pesca', 'guias' => 'guias-de-pesca', 'calendario' => 'calendario-de-pesca', 'paraguias' => 'para-guias-e-lodges', 'nosotros' => 'quem-somos', 'presupuesto' => 'pedir-orcamento-pesca', 'licencia' => 'calculadora-licenca-de-pesca', 'estimador' => 'quanto-custa-viagem-de-pesca' ],
	'en' => [ 'especies' => 'species', 'mapa' => 'map', 'creditos' => 'credits', 'entrevistas' => 'interviews', 'notas' => 'notes', 'pique' => 'bite-zones', 'viaje' => 'plan-your-trip', 'terminos' => 'terms', 'privacidad' => 'privacy', 'rio' => 'river-levels', 'paquetes' => 'fishing-packages', 'guias' => 'fishing-guides', 'calendario' => 'fishing-calendar', 'paraguias' => 'for-guides-and-lodges', 'nosotros' => 'about', 'presupuesto' => 'get-fishing-lodge-quotes', 'licencia' => 'fishing-licence-calculator', 'estimador' => 'fishing-trip-cost-estimator' ],
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
function pm_url_entrevistas( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['entrevistas'], $i );
}
function pm_url_notas( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['notas'], $i );
}
function pm_url_paquetes( string $clave, string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['paquetes'] . '/' . $clave, $i );
}
function pm_url_guias( string $clave, string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['guias'] . '/' . $clave, $i );
}
function pm_url_calendario( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['calendario'], $i );
}
function pm_url_paraguias( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['paraguias'], $i );
}
function pm_url_nosotros( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['nosotros'], $i );
}
function pm_url_presupuesto( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['presupuesto'], $i );
}
function pm_url_licencia( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['licencia'], $i );
}
function pm_url_estimador( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['estimador'], $i );
}
function pm_url_provincia( string $region, string $provincia, string $i ): string {
	return pm_url( $region . '/' . $provincia, $i );
}
function pm_url_especies( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['especies'], $i );
}
/**
 * URL slug of a species per language. The `clave` stays the shared key (and the Spanish slug); pt and en
 * get the word people search with ("trout", "truta"). A species not listed keeps its clave in every language.
 * The old URL (/en/species/trucha/) still resolves and 301s to the new one (see inc/rutas.php).
 */
const PM_SLUGS_ESPECIE = [
	'trucha'   => [ 'pt' => 'truta', 'en' => 'trout' ],
	'dorado'   => [ 'pt' => 'dourado', 'en' => 'golden-dorado' ],
	'surubi'   => [ 'pt' => 'surubim' ],
	'pejerrey' => [ 'pt' => 'peixe-rei' ],
];

function pm_slug_especie( string $clave, string $i ): string {
	return PM_SLUGS_ESPECIE[ $clave ][ $i ] ?? $clave;
}

/** The clave a species slug stands for in this language; a slug that is already a clave (old URL) passes through. */
function pm_clave_de_slug_especie( string $slug, string $i ): string {
	foreach ( PM_SLUGS_ESPECIE as $clave => $por_idioma ) {
		if ( ( $por_idioma[ $i ] ?? null ) === $slug ) {
			return $clave;
		}
	}
	return $slug;
}

function pm_url_especie( string $clave, string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['especies'] . '/' . pm_slug_especie( $clave, $i ), $i );
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

/**
 * How fresh a dated fact is, in words a reader trusts more than a stale exact date: "en los últimos 30 días",
 * "en los últimos 3 meses", else "en <mes de año>". The exact date stays in the data (and in llms.txt).
 */
function pm_fecha_relativa( ?string $ymd, ?string $idioma = null ): string {
	if ( ! $ymd ) {
		return '';
	}
	$dias = (int) floor( ( time() - strtotime( $ymd ) ) / DAY_IN_SECONDS );
	if ( $dias <= 30 ) {
		return pm_t( 'fecha.rel.30', [], $idioma );
	}
	if ( $dias <= 90 ) {
		return pm_t( 'fecha.rel.90', [], $idioma );
	}
	$i   = $idioma ?? pm_lang();
	$mes = wp_date( 'en' === $i ? 'F Y' : 'F \d\e Y', strtotime( $ymd ) );
	return pm_t( 'fecha.rel.mes', [ 'mes' => 'en' === $i ? $mes : mb_strtolower( $mes ) ], $idioma );
}

/** Localised month and year, e.g. "noviembre de 2026" / "November 2026" (lowercase in es/pt, for mid-sentence use). */
function pm_mes_anio( ?string $ymd, ?string $idioma = null ): string {
	if ( ! $ymd ) {
		return '';
	}
	$f = pm_fecha( $ymd, $idioma ); // "19 de octubre de 2026" / "19 October 2026"
	return trim( (string) preg_replace( '/^\d+( de)? /u', '', $f ) );
}

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
