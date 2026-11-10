#!/usr/bin/env node
// Arma wordpress/scripts/precios-paquetes-2026-11-05.php con los tres JSON de contenido/precios/ embebidos.
// Uso: node wordpress/build/precios-a-script.mjs [--aplicar]
import { readFileSync, writeFileSync } from 'node:fs';
const aplicar = process.argv.includes('--aplicar');
const actualizar = process.argv.includes('--actualizar'); // reescribe contenido de notas que ya existen (no crea)
const solo = (process.argv.find(a => a.startsWith('--solo=')) || '').slice(7); // claves a actualizar, separadas por coma
const marcador = (process.argv.find(a => a.startsWith('--marcador=')) || '').slice(11); // texto que debe quedar en el contenido (verificación)
const d = new URL('../contenido/precios/', import.meta.url).pathname;
const es0 = JSON.parse(readFileSync(`${d}paginas.es.json`, 'utf8'));
// Las claves, la especie y los tipos de bloque son del original: la traducción no los toca.
const j = l => { const o = JSON.parse(readFileSync(`${d}paginas.${l}.json`, 'utf8')); o.paginas.forEach((p, i) => { p.clave = es0.paginas[i].clave; p.especie = es0.paginas[i].especie; p.bloques.forEach((b, k) => { b.t = es0.paginas[i].bloques[k].t; }); }); return JSON.stringify(o); };
const php = String.raw`<?php
/**
 * Cinco páginas de precios de paquetes de pesca (es/en/pt), 5/11/2026. Fuente única de contenido:
 * wordpress/contenido/precios/paginas.{es,en,pt}.json (este archivo se genera con wordpress/build/precios-a-script.mjs; no editar a mano).
 * Los precios salen de páginas de operadores leídas con investigar.yml el 5/11/2026 (carpeta paquetes-precios-20261105-* de la rama
 * investigacion; detalle interno en docs/investigacion/precios-paquetes-2026-11-05.md). En público la fuente se muestra como
 * "investigación propia a partir de datos públicos y proveedores asociados" (decisión del dueño, 5/11/2026).
 * Idempotente: una nota con la misma clave e idioma no se toca. Simulacro primero ($aplicar = false); se corre con wp-script.yml.
 */
$aplicar = ${aplicar ? 'true' : 'false'};
$actualizar = ${actualizar ? 'true' : 'false'};
$solo = array_filter( explode( ',', '${solo}' ) );
$marcador = '${marcador}';
$hoy     = '20261105';
$datos   = [
	'es' => json_decode( <<<'JSON_ES'
${j('es')}
JSON_ES
, true ),
	'en' => json_decode( <<<'JSON_EN'
${j('en')}
JSON_EN
, true ),
	'pt' => json_decode( <<<'JSON_PT'
${j('pt')}
JSON_PT
, true ),
];
foreach ( $datos as $l => $v ) {
	if ( ! is_array( $v ) || empty( $v['paginas'] ) ) {
		WP_CLI::error( "JSON $l inválido" );
	}
}

function pm_pp_bloque( array $b ): string {
	switch ( $b['t'] ) {
		case 'h2':
			return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$b['h']}</h2>\n<!-- /wp:heading -->";
		case 'p':
			return "<!-- wp:paragraph -->\n<p>{$b['h']}</p>\n<!-- /wp:paragraph -->";
		case 'lista':
			$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $b['items'] ) );
			return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
		case 'tabla':
			$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $b['cab'] ) );
			$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $b['filas'] ) );
			return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
	}
	WP_CLI::error( 'bloque desconocido: ' . $b['t'] );
}
function pm_pp_cuerpo( array $pg, string $cierre ): string {
	$bloques = array_map( 'pm_pp_bloque', $pg['bloques'] );
	$bloques[] = pm_pp_bloque( [ 't' => 'p', 'h' => '<em>' . $cierre . '</em>' ] );
	return implode( "\n\n", $bloques );
}
function pm_pp_resolver( string $cuerpo, string $l ): string {
	return preg_replace_callback( '/\{(n|d):([a-z0-9-]+)\}/', function ( $m ) use ( $l ) {
		$tipo = 'n' === $m[1] ? 'pm_nota' : 'pm_destino';
		$p    = pm_buscar( $tipo, $m[2], $l ) ?? pm_buscar( $tipo, $m[2], 'es' );
		return $p ? esc_url( get_permalink( $p ) ) : esc_url( home_url( '/' ) );
	}, str_replace(
		[ '{viaje}', '{calendario}' ],
		[ esc_url( pm_url_viaje( $l ) ), esc_url( pm_url_calendario( $l ) ) ],
		$cuerpo
	) );
}

$fuentes_txt = [
	'es' => 'Investigación propia a partir de datos públicos y proveedores asociados',
	'en' => 'Our own research from public data and partner providers',
	'pt' => 'Pesquisa própria a partir de dados públicos e fornecedores parceiros',
];
$creadas = [];
foreach ( [ 'es', 'en', 'pt' ] as $l ) {
	foreach ( $datos[ $l ]['paginas'] as $pg ) {
		$clave = $pg['clave'];
		$tl = mb_strlen( $pg['seo_title'] );
		$dl = mb_strlen( $pg['seo_description'] );
		if ( $tl > 60 || $dl < 70 || $dl > 160 ) {
			WP_CLI::error( "SEO fuera de rango $clave ($l): title $tl / description $dl" );
		}
		$ya = array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $l );
		if ( $actualizar ) {
			if ( ( $solo && ! in_array( $clave, $solo, true ) ) || ! $ya ) {
				continue;
			}
			$id     = reset( $ya )->ID;
			$cuerpo = pm_pp_resolver( pm_pp_cuerpo( $pg, $datos[ $l ]['cierre'] ), $l );
			WP_CLI::log( ( $aplicar ? 'ACTUALIZA' : 'SIMULACRO' ) . ": $clave ($l) #$id — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., ' . count( $pg['datos'] ) . ' datos' );
			if ( $aplicar ) {
				wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $cuerpo ) ] );
				update_field( 'nota_datos', array_map( fn( $x ) => [ 'dato' => $x ], $pg['datos'] ), $id );
				update_field( 'actualizado', $hoy, $id );
				update_field( 'bajada', $pg['bajada'], $id );
				if ( isset( $pg['fuentes'] ) ) {
					update_field( 'fuentes', array_map( fn( $f ) => [ 'nombre' => $f['nombre'], 'url' => $f['url'] ?? '', 'oficial' => ! empty( $f['oficial'] ) ? 1 : 0, 'consultada' => $hoy ], $pg['fuentes'] ), $id );
				}
				$guardado = get_post_field( 'post_content', $id );
				$tablas = count( array_filter( $pg['bloques'], fn( $b ) => 'tabla' === $b['t'] ) );
				$ok = substr_count( $guardado, '<table' ) === $tablas && false === strpos( $guardado, '{n:' ) && md5( wp_strip_all_tags( $guardado ) ) === md5( wp_strip_all_tags( $cuerpo ) );
				WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($l) #$id " . get_permalink( $id ) );
				$ok || WP_CLI::error( "$clave ($l): la lectura de vuelta no coincide." );
			}
			continue;
		}
		if ( $ya ) {
			WP_CLI::log( "$clave ($l) ya existe: no toco nada." );
			continue;
		}
		$cuerpo = pm_pp_cuerpo( $pg, $datos[ $l ]['cierre'] );
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($l) «{$pg['titulo']}» /{$pg['slug']}/ — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., ' . count( $pg['datos'] ) . " datos; title $tl / description $dl" );
		if ( ! $aplicar ) {
			continue;
		}
		$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $pg['titulo'], 'post_name' => $pg['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
		$campos = [
			'clave' => $clave, 'idioma' => $l, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
			'bajada' => $pg['bajada'], 'nota_datos' => array_map( fn( $x ) => [ 'dato' => $x ], $pg['datos'] ),
			'fuentes' => isset( $pg['fuentes'] ) ? array_map( fn( $f ) => [ 'nombre' => $f['nombre'], 'url' => $f['url'] ?? '', 'oficial' => ! empty( $f['oficial'] ) ? 1 : 0, 'consultada' => $hoy ], $pg['fuentes'] ) : [ [ 'nombre' => $fuentes_txt[ $l ], 'url' => '', 'oficial' => 0, 'consultada' => $hoy ] ],
			'seo_title' => $pg['seo_title'], 'seo_description' => $pg['seo_description'],
			'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_especie' => $pg['especie'],
			'nota_origen' => 'Precios leídos el 5/11/2026 en páginas de operadores (investigar.yml, rama investigacion). Detalle interno: docs/investigacion/precios-paquetes-2026-11-05.md. En público: investigación propia a partir de datos públicos y proveedores asociados.',
		];
		foreach ( $campos as $k => $v ) {
			update_field( $k, $v, $id );
		}
		$creadas[] = [ $id, $l, $clave ];
		$ok = count( pm_filas( 'nota_datos', $id ) ) === count( $pg['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'wp-block-table' );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($l) #$id " . get_permalink( $id ) );
		$ok || WP_CLI::error( "$clave ($l): la lectura de vuelta no coincide." );
	}
}
// Segunda pasada: con todas creadas, se resuelven los enlaces entre notas.
foreach ( $creadas as [ $id, $l, $clave ] ) {
	$c = get_post_field( 'post_content', $id );
	$r = pm_pp_resolver( $c, $l );
	if ( preg_match( '/\{[a-z]+(:[a-z0-9-]+)?\}/', $r, $m ) ) {
		WP_CLI::error( "$clave ($l): queda un marcador sin resolver: {$m[0]}" );
	}
	wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $r ) ] );
}
if ( ! $aplicar ) {
	// Simulacro: comprobar que existen las notas y guías a las que se enlaza.
	foreach ( [ 'cuando-pescar-trucha-patagonia' => 'pm_nota', 'rio-grande' => 'pm_destino' ] as $c => $t ) {
		WP_CLI::log( ( pm_buscar( $t, $c, 'es' ) ? 'existe' : 'FALTA' ) . ": $t $c (es)" );
	}
	WP_CLI::success( 'Simulacro terminado.' );
	return;
}
flush_rewrite_rules( false );
WP_CLI::success( count( $creadas ) . ' notas creadas.' );
`;
writeFileSync(new URL(actualizar ? '../scripts/precios-actualizar-2026-11-05.php' : '../scripts/precios-paquetes-2026-11-05.php', import.meta.url).pathname, php);
console.log('escrito', aplicar ? '(APLICA)' : '(simulacro)', php.length, 'bytes');
