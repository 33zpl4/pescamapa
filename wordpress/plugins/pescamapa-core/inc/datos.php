<?php
/**
 * Read model: destinations, species and planned places as plain arrays, the
 * way templates use them. Shared fields (region, province, coordinates,
 * species, photo) always come from the Spanish post with the same clave.
 */

defined( 'ABSPATH' ) || exit;

/** Posts of a type, any language, published in WordPress (both ficha states). */
function pm_posts( string $tipo ): array {
	static $cache = [];
	return $cache[ $tipo ] ??= get_posts( [
		'post_type'      => $tipo,
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	] );
}

function pm_idioma_de( int $post_id ): string {
	return pm_idioma_valido( (string) get_post_meta( $post_id, 'idioma', true ) );
}

function pm_clave_de( int $post_id ): string {
	return (string) get_post_meta( $post_id, 'clave', true );
}

/** The post of a type with this clave in this language (or null). */
function pm_buscar( string $tipo, string $clave, string $idioma ): ?WP_Post {
	foreach ( pm_posts( $tipo ) as $p ) {
		if ( pm_clave_de( $p->ID ) === $clave && pm_idioma_de( $p->ID ) === $idioma ) {
			return $p;
		}
	}
	return null;
}

/**
 * How many sources the published guides and notes cite (home proof line, llms.txt; 14/11/2026). Read straight from the
 * database. `unicas` = distinct sources (same URL in es/en/pt, or in several guides, counts once; a row without URL counts
 * by name); `filas` = every row; `fecha` = last modification (Y-m-d) of any of those posts, i.e. the day this count was
 * last able to change.
 */
function pm_fuentes_conteo(): array {
	static $c = null;
	if ( null !== $c ) {
		return $c;
	}
	$vistas = [];
	$filas  = 0;
	$fecha  = '';
	foreach ( [ 'pm_destino', 'pm_nota' ] as $tipo ) {
		foreach ( pm_posts( $tipo ) as $p ) {
			for ( $i = 0, $n = (int) get_post_meta( $p->ID, 'fuentes', true ); $i < $n; $i++ ) {
				$nombre = trim( (string) get_post_meta( $p->ID, "fuentes_{$i}_nombre", true ) );
				$url    = trim( (string) get_post_meta( $p->ID, "fuentes_{$i}_url", true ) );
				if ( '' === $nombre && '' === $url ) {
					continue;
				}
				++$filas;
				$vistas[ '' !== $url ? strtolower( rtrim( preg_replace( '#^https?://(www\.)?#i', '', $url ), '/' ) ) : mb_strtolower( $nombre ) ] = true;
			}
			$fecha = max( $fecha, substr( (string) $p->post_modified, 0, 10 ) );
		}
	}
	return $c = [ 'unicas' => count( $vistas ), 'filas' => $filas, 'fecha' => $fecha ];
}

/** Repeater rows as arrays (SCF), [] when empty. */
function pm_filas( string $nombre, int $post_id ): array {
	$v = pm_campo( $nombre, $post_id );
	return is_array( $v ) ? array_values( $v ) : [];
}

function pm_destino( int $post_id ): array {
	$idioma = pm_idioma_de( $post_id );
	$clave  = pm_clave_de( $post_id );
	$fuente = 'es' === $idioma ? get_post( $post_id ) : pm_buscar( 'pm_destino', $clave, 'es' );
	$fid    = $fuente ? $fuente->ID : $post_id;

	$aguas = array_map( function ( $a ) {
		$a['coordenadas'] = ( is_numeric( $a['lat'] ?? null ) && is_numeric( $a['lon'] ?? null ) ) ? [ (float) $a['lat'], (float) $a['lon'] ] : null;
		return $a;
	}, pm_sin_pendientes( pm_filas( 'aguas', $post_id ) ) );

	return [
		'id'          => $post_id,
		'idioma'      => $idioma,
		'clave'       => $clave,
		'nombre'      => get_the_title( $post_id ),
		'slug'        => get_post_field( 'post_name', $post_id ),
		'url'         => get_permalink( $post_id ),
		'region'      => (string) pm_campo( 'region', $fid ),
		'provincia'   => (string) pm_campo( 'provincia', $fid ),
		'coordenadas' => [ (float) pm_campo( 'lat', $fid ), (float) pm_campo( 'lon', $fid ) ],
		'especies'    => (array) ( pm_campo( 'especies', $fid ) ?: [] ),
		'foto'        => (int) get_post_thumbnail_id( $fid ),
		'alturas_url' => (string) pm_campo( 'alturas_url', $fid ),
		'h1'          => (string) pm_campo( 'h1', $post_id ),
		'bajada'      => (string) pm_campo( 'bajada', $post_id ),
		'modalidades' => array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) pm_campo( 'modalidades', $post_id ) ) ) ) ),
		'epoca'       => (string) pm_campo( 'epoca', $post_id ),
		'aguas'       => $aguas,
		'temporada'   => pm_sin_pendientes( pm_filas( 'temporada', $post_id ) ),
		'abre'        => (string) pm_campo( 'temporada_abre', $fid ),
		'cierra'      => (string) pm_campo( 'temporada_cierra', $fid ),
		'pico'        => array_values( array_filter( array_map( 'intval', preg_split( '/[^0-9]+/', (string) pm_campo( 'pico_meses', $fid ), -1, PREG_SPLIT_NO_EMPTY ) ), fn( $m ) => $m >= 1 && $m <= 12 ) ),
		'pico_quien'  => trim( (string) pm_campo( 'pico_quien', $fid ) ),
		'temporada_nota' => trim( (string) pm_campo( 'temporada_nota', $post_id ) ),
		'reglamento'  => [
			'autoridad'  => (string) pm_campo( 'reglamento_autoridad', $post_id ),
			'url'        => (string) pm_campo( 'reglamento_url', $post_id ),
			'verificado' => (string) pm_campo( 'reglamento_verificado', $post_id ),
			'resumen'    => (string) pm_campo( 'reglamento_resumen', $post_id ),
		],
		'faqs'        => pm_sin_pendientes( pm_filas( 'faqs', $post_id ) ),
		'fuentes'     => array_values( array_filter( pm_filas( 'fuentes', $post_id ), fn( $f ) => ! empty( $f['nombre'] ) ) ),
		'mas'         => array_values( array_filter( pm_sin_pendientes( pm_filas( 'mas', $post_id ) ), fn( $m ) => ! empty( $m['nombre'] ) && ! empty( $m['url'] ) ) ),
		'seo_title'   => (string) pm_campo( 'seo_title', $post_id ),
		'seo_description' => (string) pm_campo( 'seo_description', $post_id ),
		'actualizado' => (string) pm_campo( 'actualizado', $post_id ),
		'autor'       => (string) pm_campo( 'autor', $post_id ),
	];
}

/** One card per destination: this language's version, else the Spanish one. In publication order. */
function pm_destinos_lista( string $idioma ): array {
	$por_clave = [];
	foreach ( pm_posts( 'pm_destino' ) as $p ) {
		$clave = pm_clave_de( $p->ID );
		$i     = pm_idioma_de( $p->ID );
		if ( $i === $idioma || ( 'es' === $i && ! isset( $por_clave[ $clave ] ) ) ) {
			$por_clave[ $clave ] = $p->ID;
		}
	}
	// Oldest guide first (Goya, then Esquina…): the order they were published,
	// taken from the Spanish version so every language lists them the same way.
	$fecha = fn( $clave ) => (string) get_post_field( 'post_date', pm_buscar( 'pm_destino', $clave, 'es' ) ?? $por_clave[ $clave ] );
	uksort( $por_clave, fn( $a, $b ) => strcmp( $fecha( $a ), $fecha( $b ) ) );
	return array_map( 'pm_destino', array_values( $por_clave ) );
}

/**
 * Published packages of a destination post: each with its dated "desde" price only when amount, currency, unit and
 * date are all there (a price without a date is never shown). The internal field never leaves this function.
 */
function pm_paquetes( int $post_id ): array {
	$lineas = fn( $v ) => array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) $v ) ) ) );
	$out    = [];
	foreach ( pm_filas( 'paquetes', $post_id ) as $r ) {
		if ( empty( $r['publicado'] ) || '' === trim( (string) ( $r['titulo'] ?? '' ) ) ) {
			continue;
		}
		$monto  = is_numeric( $r['desde_monto'] ?? null ) ? (float) $r['desde_monto'] : 0.0;
		$moneda = in_array( $r['desde_moneda'] ?? '', [ 'ARS', 'USD', 'BRL', 'EUR' ], true ) ? $r['desde_moneda'] : '';
		$unidad = trim( (string) ( $r['desde_unidad'] ?? '' ) );
		$fecha  = (string) ( $r['desde_fecha'] ?? '' );
		$out[]  = [
			'titulo'     => trim( (string) $r['titulo'] ),
			'foto'       => (int) ( $r['foto'] ?? 0 ),
			'resumen'    => trim( (string) ( $r['resumen'] ?? '' ) ),
			'modalidad'  => trim( (string) ( $r['modalidad'] ?? '' ) ),
			'duracion'   => trim( (string) ( $r['duracion'] ?? '' ) ),
			'capacidad'  => trim( (string) ( $r['capacidad'] ?? '' ) ),
			'incluye'    => $lineas( $r['incluye'] ?? '' ),
			'no_incluye' => $lineas( $r['no_incluye'] ?? '' ),
			'variantes'  => $lineas( $r['variantes'] ?? '' ),
			'precio'     => ( $monto > 0 && $moneda && $unidad && $fecha ) ? [ 'monto' => $monto, 'moneda' => $moneda, 'unidad' => $unidad, 'fecha' => $fecha ] : null,
		];
	}
	return $out;
}

/** "Before you travel" blocks of the packages page: title, paragraphs, and the source with the date it was read. */
function pm_paquetes_info( int $post_id ): array {
	$out = [];
	foreach ( pm_filas( 'paquetes_info', $post_id ) as $r ) {
		$parrafos = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) ( $r['texto'] ?? '' ) ) ) ) );
		if ( '' === trim( (string) ( $r['titulo'] ?? '' ) ) || ! $parrafos ) {
			continue;
		}
		$out[] = [
			'titulo'     => trim( (string) $r['titulo'] ),
			'parrafos'   => $parrafos,
			'fuente'     => trim( (string) ( $r['fuente_nombre'] ?? '' ) ),
			'url'        => (string) ( $r['fuente_url'] ?? '' ),
			'consultada' => (string) ( $r['consultada'] ?? '' ),
		];
	}
	return $out;
}

/** Packages of the guide of this destination in this language (never borrowed from another language). */
function pm_destino_paquetes( string $clave, string $idioma ): array {
	$p = pm_buscar( 'pm_destino', $clave, $idioma );
	return $p ? pm_paquetes( $p->ID ) : [];
}

function pm_alternas_paquetes( string $clave ): array {
	$out = [];
	foreach ( pm_idiomas() as $i ) {
		if ( pm_destino_paquetes( $clave, $i ) ) {
			$out[ $i ] = pm_url_paquetes( $clave, $i );
		}
	}
	return $out;
}

/** The city page "Guías de pesca en <lugar>" exists in a language when the guide exists there and the destination has at least one public outfitter. */
function pm_destino_guias( string $clave, string $idioma ): bool {
	return (bool) pm_buscar( 'pm_destino', $clave, $idioma ) && (bool) pm_prestadores_destino( $clave );
}

function pm_alternas_guias( string $clave ): array {
	$out = [];
	foreach ( pm_idiomas() as $i ) {
		if ( pm_destino_guias( $clave, $i ) ) {
			$out[ $i ] = pm_url_guias( $clave, $i );
		}
	}
	return $out;
}

/**
 * "US$ 150", "$ 150.000", "R$ 600" with the separators of the page's language. A whole amount shows no decimals;
 * one with cents shows two ("€ 599,69" in es/pt, "€ 599.69" in en).
 */
function pm_paquete_precio_txt( array $precio, string $idioma ): string {
	$monto = round( (float) $precio['monto'], 2 );
	$dec   = abs( $monto - round( $monto ) ) > 0.001 ? 2 : 0;
	$n     = 'en' === $idioma ? number_format( $monto, $dec, '.', ',' ) : number_format( $monto, $dec, ',', '.' );
	return [ 'ARS' => '$ ', 'USD' => 'US$ ', 'BRL' => 'R$ ', 'EUR' => '€ ' ][ $precio['moneda'] ] . $n;
}

/**
 * "Updated on" date of a packages page (Y-m-d): the later of the guide's modified date and the dates of the "desde" prices
 * of the published packages (ACF stores them as Ymd or Y-m-d), so a price edit moves the date the page shows.
 */
function pm_paquetes_modificado( array $paquetes, string $base ): string {
	$max = $base;
	foreach ( $paquetes as $p ) {
		$t = strtotime( (string) ( $p['precio']['fecha'] ?? '' ) );
		$f = $t ? gmdate( 'Y-m-d', $t ) : '';
		if ( $f && $f > $max ) {
			$max = $f;
		}
	}
	return $max;
}

/** Language versions of a destination: idioma => url. */
function pm_alternas_destino( string $clave ): array {
	$out = [];
	foreach ( pm_posts( 'pm_destino' ) as $p ) {
		if ( pm_clave_de( $p->ID ) === $clave ) {
			$out[ pm_idioma_de( $p->ID ) ] = get_permalink( $p->ID );
		}
	}
	return $out;
}

function pm_especie( int $post_id ): array {
	return [
		'id'          => $post_id,
		'idioma'      => pm_idioma_de( $post_id ),
		'clave'       => pm_clave_de( $post_id ),
		'nombre'      => get_the_title( $post_id ),
		'url'         => pm_url_especie( pm_clave_de( $post_id ), pm_idioma_de( $post_id ) ),
		'cientifico'  => (string) pm_campo( 'cientifico', $post_id ),
		'resumen'     => (string) pm_campo( 'resumen', $post_id ),
		'fuentes'     => pm_filas( 'fuentes', $post_id ),
		'actualizado' => (string) pm_campo( 'actualizado', $post_id ),
		'seo_title'   => (string) pm_campo( 'seo_title', $post_id ),
		'seo_description' => (string) pm_campo( 'seo_description', $post_id ),
	];
}

/** The species in this language, or the Spanish one, by clave. */
function pm_especie_en( string $clave, string $idioma ): ?array {
	$p = pm_buscar( 'pm_especie', $clave, $idioma ) ?? pm_buscar( 'pm_especie', $clave, 'es' );
	return $p ? pm_especie( $p->ID ) : null;
}

function pm_especies_lista( string $idioma ): array {
	$out = [];
	foreach ( pm_posts( 'pm_especie' ) as $p ) {
		if ( pm_idioma_de( $p->ID ) === $idioma ) {
			$out[] = pm_especie( $p->ID );
		}
	}
	return $out;
}

function pm_alternas_especie( string $clave ): array {
	$out = [];
	foreach ( pm_posts( 'pm_especie' ) as $p ) {
		if ( pm_clave_de( $p->ID ) === $clave ) {
			$out[ pm_idioma_de( $p->ID ) ] = pm_url_especie( $clave, pm_idioma_de( $p->ID ) );
		}
	}
	return $out;
}

/** "Coming soon" places, ordered, minus those that already have a guide. */
function pm_proximos_lista( string $idioma ): array {
	$guias = [];
	foreach ( pm_posts( 'pm_destino' ) as $p ) {
		$guias[ pm_clave_de( $p->ID ) ] = true;
	}
	$out = [];
	foreach ( pm_posts( 'pm_proximo' ) as $p ) {
		$clave = $p->post_name;
		if ( isset( $guias[ $clave ] ) ) {
			continue;
		}
		$nombre = get_the_title( $p->ID );
		$agua   = (string) pm_campo( 'agua', $p->ID );
		$out[]  = [
			'id'          => $p->ID,
			'clave'       => $clave,
			'nombre'      => ( 'es' === $idioma ? '' : (string) pm_campo( "nombre_{$idioma}", $p->ID ) ) ?: $nombre,
			'agua'        => ( 'es' === $idioma ? '' : (string) pm_campo( "agua_{$idioma}", $p->ID ) ) ?: $agua,
			'provincia'   => PM_PROVINCIAS[ (string) pm_campo( 'provincia', $p->ID ) ] ?? (string) pm_campo( 'provincia', $p->ID ),
			'region'      => (string) pm_campo( 'region', $p->ID ),
			'especies'    => (array) ( pm_campo( 'especies', $p->ID ) ?: [] ),
			'coordenadas' => [ (float) pm_campo( 'lat', $p->ID ), (float) pm_campo( 'lon', $p->ID ) ],
			'foto'        => (int) get_post_thumbnail_id( $p->ID ),
			'orden'       => (int) ( pm_campo( 'orden', $p->ID ) ?: 99 ),
		];
	}
	usort( $out, fn( $a, $b ) => $a['orden'] <=> $b['orden'] ?: strcmp( $a['nombre'], $b['nombre'] ) );
	return $out;
}

/** Destinations that list this species (in the requested language when translated). */
/** Published destinations of one province (region + province keys), this language with Spanish fallback. */
function pm_destinos_provincia( string $region, string $provincia, string $idioma ): array {
	return array_values( array_filter( pm_destinos_lista( $idioma ), fn( $d ) => $d['region'] === $region && $d['provincia'] === $provincia ) );
}

/** Every region/province pair that has a published destination, in the destinations' order. */
function pm_provincias_lista(): array {
	$out = [];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		if ( $d['region'] && $d['provincia'] ) {
			$out[ $d['region'] . '/' . $d['provincia'] ] = [ 'region' => $d['region'], 'provincia' => $d['provincia'] ];
		}
	}
	return array_values( $out );
}

function pm_destinos_con_especie( string $clave_especie, string $idioma ): array {
	return array_values( array_filter( pm_destinos_lista( $idioma ), fn( $d ) => in_array( $clave_especie, $d['especies'], true ) ) );
}
