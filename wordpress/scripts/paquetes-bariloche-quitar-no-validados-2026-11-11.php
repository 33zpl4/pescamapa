<?php
/**
 * Bariloche (es/en/pt), 11/11/2026: owner decision, the two old packages whose price no operator validated are taken off the page.
 * They are the rows that do NOT start with one of the six operator names followed by ":" (Trout Bariloche, Southern Rivers Patagonia,
 * Patagonia in the Loop, Patagonia Drift Fly Fishing, Outfitters Patagonia, Guides Patagonia): the lake day (from US$ 400) and the
 * Limay day with Claudio Antonelli (from US$ 550). They are NOT deleted (internal notes): publicado = 0 and a line added to "interno".
 * Safety: if a language does not have exactly 2 published non-operator rows, it warns and does not touch that language.
 * Every other row goes back as it was.
 *
 * Idempotent: unpublished rows are not counted again and the note is added once.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = paquetes-bariloche-quitar-no-validados-2026-11-11).
 */
$aplicar = false;
$destino = 'bariloche';
$nota    = ' Despublicado el 11/11/2026 por decisión del dueño: precio sin validar por un operador.';
$ops     = [ 'Trout Bariloche', 'Southern Rivers Patagonia', 'Patagonia in the Loop', 'Patagonia Drift Fly Fishing', 'Outfitters Patagonia', 'Guides Patagonia' ];

$es_operador = function ( string $titulo ) use ( $ops ): bool {
	foreach ( $ops as $o ) {
		if ( 0 === strpos( trim( $titulo ), $o . ':' ) ) {
			return true;
		}
	}
	return false;
};

$cambios = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = pm_buscar( 'pm_destino', $destino, $i );
	if ( ! $p ) {
		WP_CLI::warning( "$destino ($i): no hay guía en este idioma." );
		continue;
	}
	$filas = pm_filas( 'paquetes', $p->ID );
	$ids   = [];
	foreach ( $filas as $k => $f ) {
		$tit = (string) ( $f['titulo'] ?? '' );
		if ( ! empty( $f['publicado'] ) && ! $es_operador( $tit ) ) {
			$ids[] = $k;
		}
	}
	if ( 2 !== count( $ids ) ) {
		WP_CLI::warning( "$destino ($i) #{$p->ID}: hay " . count( $ids ) . ' filas publicadas que no son de operador (se esperaban 2); no se toca este idioma.' );
		foreach ( $ids as $k ) {
			WP_CLI::log( "   - {$filas[$k]['titulo']}" );
		}
		continue;
	}
	foreach ( $ids as $k ) {
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $destino ($i) #{$p->ID}: despublicaría «{$filas[$k]['titulo']}»" );
		$filas[ $k ]['publicado'] = 0;
		if ( false === strpos( (string) ( $filas[ $k ]['interno'] ?? '' ), trim( $nota ) ) ) {
			$filas[ $k ]['interno'] = rtrim( (string) ( $filas[ $k ]['interno'] ?? '' ) ) . $nota;
		}
	}
	$cambios += 2;
	WP_CLI::log( "   $destino ($i): hoy " . count( pm_paquetes( $p->ID ) ) . ' publicados, quedarían ' . ( count( pm_paquetes( $p->ID ) ) - 2 ) . '.' );
	if ( $aplicar ) {
		update_field( 'paquetes', $filas, $p->ID );
		$n  = count( pm_paquetes( $p->ID ) );
		$ok = 6 === $n;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $destino ($i): pm_paquetes devuelve $n (se esperaban 6), " . count( pm_filas( 'paquetes', $p->ID ) ) . ' filas guardadas' );
		$ok || WP_CLI::error( "$destino ($i): la lectura de vuelta no coincide." );
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios filas." );
