<?php
/**
 * Maps.
 * - Hero map: Argentina's real outline pre-projected in data/hero.json
 *   (wordpress/build/generar-datos.mjs). Pins are placed by bilinear
 *   interpolation on the same projection's lat/lon grid. Pure SVG, no JS.
 * - Interactive map: Leaflet + OpenStreetMap tiles, pins as data attributes
 *   read by the theme's assets/mapa.js.
 */

defined( 'ABSPATH' ) || exit;

function pm_hero(): array {
	static $h = null;
	return $h ??= json_decode( file_get_contents( PM_DIR . 'data/hero.json' ), true );
}

/** [x, y] in the hero SVG for a lat/lon. */
function pm_proyectar( float $lat, float $lon ): array {
	$g   = pm_hero()['grid'];
	$fil = ( $lat - $g['lat0'] ) / $g['paso'];
	$col = ( $lon - $g['lon0'] ) / $g['paso'];
	$f0  = max( 0, min( count( $g['x'] ) - 2, (int) floor( $fil ) ) );
	$c0  = max( 0, min( count( $g['x'][0] ) - 2, (int) floor( $col ) ) );
	$tf  = $fil - $f0;
	$tc  = $col - $c0;
	$interp = function ( array $m ) use ( $f0, $c0, $tf, $tc ) {
		$a = $m[ $f0 ][ $c0 ] * ( 1 - $tc ) + $m[ $f0 ][ $c0 + 1 ] * $tc;
		$b = $m[ $f0 + 1 ][ $c0 ] * ( 1 - $tc ) + $m[ $f0 + 1 ][ $c0 + 1 ] * $tc;
		return round( $a * ( 1 - $tf ) + $b * $tf, 1 );
	};
	return [ $interp( $g['x'] ), $interp( $g['y'] ) ];
}

/**
 * @param array $puntos [ ['nombre', 'coordenadas' => [lat, lon], 'url'?, 'destacado'?] ]
 */
function pm_mapa_hero( array $puntos, string $etiqueta ): string {
	$h = pm_hero();
	$svg = sprintf( '<svg class="mapa-hero" viewBox="0 0 %d %d" role="img" aria-label="%s">', $h['ancho'], $h['alto'], esc_attr( $etiqueta ) );
	$svg .= '<defs><radialGradient id="mh-glow" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="var(--lime)" stop-opacity="0.55"/><stop offset="100%" stop-color="var(--lime)" stop-opacity="0"/></radialGradient></defs>';
	$svg .= '<path class="vecinos" d="' . esc_attr( $h['vecinos'] ) . '"/>';
	$svg .= '<path class="pais" d="' . esc_attr( $h['pais'] ) . '"/>';
	foreach ( $h['rios'] as $r ) {
		$svg .= '<path class="rio" d="' . esc_attr( $r ) . '"/>';
	}
	$destacados = '';
	foreach ( $puntos as $p ) {
		[ $x, $y ] = pm_proyectar( $p['coordenadas'][0], $p['coordenadas'][1] );
		$titulo = '<title>' . esc_html( $p['nombre'] ) . '</title>';
		if ( ! empty( $p['destacado'] ) ) {
			$destacados .= '<a href="' . esc_url( $p['url'] ) . '" class="destacado">' . $titulo
				. "<circle class=\"halo\" cx=\"$x\" cy=\"$y\" r=\"22\" fill=\"url(#mh-glow)\"/>"
				. "<circle class=\"pulso\" cx=\"$x\" cy=\"$y\" r=\"8\"/>"
				. "<circle class=\"nucleo\" cx=\"$x\" cy=\"$y\" r=\"7\"/>"
				. '<text x="' . ( $x + 14 ) . '" y="' . ( $y + 5 ) . '">' . esc_html( $p['nombre'] ) . '</text></a>';
		} else {
			$svg .= "<g class=\"punto\">{$titulo}<circle cx=\"$x\" cy=\"$y\" r=\"5\"/></g>";
		}
	}
	return $svg . $destacados . '</svg>';
}

/** Decorative contour lines behind the hero (deterministic). */
function pm_curvas(): string {
	$W = 1600;
	$H = 900;
	$out = '';
	for ( $i = 0; $i < 18; $i++ ) {
		$base = 40 + $i * 48;
		$amp  = 38 + ( $i % 5 ) * 14;
		$fase = $i * 0.55;
		$pts  = [];
		for ( $x = -80; $x <= $W + 80; $x += 80 ) {
			$pts[] = [ $x, $base + $amp * sin( $x / 260 + $fase ) + 22 * sin( $x / 97 + $fase * 1.7 ) ];
		}
		$d = sprintf( 'M%d %.1f', $pts[0][0], $pts[0][1] );
		$n = count( $pts );
		for ( $k = 0; $k < $n - 1; $k++ ) {
			$p0 = $pts[ max( 0, $k - 1 ) ];
			$p1 = $pts[ $k ];
			$p2 = $pts[ $k + 1 ];
			$p3 = $pts[ min( $n - 1, $k + 2 ) ];
			$d .= sprintf(
				' C%.1f %.1f %.1f %.1f %d %.1f',
				$p1[0] + ( $p2[0] - $p0[0] ) / 6, $p1[1] + ( $p2[1] - $p0[1] ) / 6,
				$p2[0] - ( $p3[0] - $p1[0] ) / 6, $p2[1] - ( $p3[1] - $p1[1] ) / 6,
				$p2[0], $p2[1]
			);
		}
		$out .= "<path d=\"{$d}\"/>";
	}
	return "<svg class=\"curvas\" viewBox=\"0 0 {$W} {$H}\" preserveAspectRatio=\"xMidYMid slice\" aria-hidden=\"true\">{$out}</svg>";
}

/**
 * Interactive Leaflet map. Pin: ['nombre', 'coordenadas', 'url'?, 'detalle'?, 'tipo' => destino|proximo|agua]
 */
function pm_mapa_interactivo( array $pins, string $etiqueta, array $op = [] ): string {
	$op = $op + [ 'centro' => [ -38.5, -63.5 ], 'zoom' => 4, 'alto' => '560px' ];
	wp_enqueue_style( 'leaflet' );
	wp_enqueue_script( 'pm-mapa' );
	return sprintf(
		'<div class="mapa" role="region" aria-label="%s" style="--alto:%s" data-pins="%s" data-centro="%s" data-zoom="%d" data-guia="%s" data-pronto="%s"></div><noscript><p class="note">%s</p></noscript>',
		esc_attr( $etiqueta ),
		esc_attr( $op['alto'] ),
		esc_attr( wp_json_encode( array_values( $pins ) ) ),
		esc_attr( wp_json_encode( $op['centro'] ) ),
		(int) $op['zoom'],
		esc_attr( pm_t( 'mapa.popup.guia' ) ),
		esc_attr( pm_t( 'mapa.popup.pronto' ) ),
		esc_html( pm_t( 'mapa.sinjs' ) )
	);
}
