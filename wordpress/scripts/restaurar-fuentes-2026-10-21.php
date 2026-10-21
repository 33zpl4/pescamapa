<?php
/**
 * 21/10/2026: the Spanish Goya guide shows 18 empty rows under "Fuentes" (the
 * rows exist, every value is blank); pt and en kept theirs. Looks for fully
 * blank rows in the repeaters of every Spanish guide and species, and refills
 * a blank `fuentes` row from the same row of the translation (same clave,
 * same position) when that one has values. Only fully blank rows are ever
 * touched; anything with content is left alone. Everything else is reported.
 *
 * DRY RUN while $aplicar is false: prints what it would do, writes nothing.
 * Run with wp-script.yml (script = restaurar-fuentes-2026-10-21).
 */

$aplicar = false;

$repetidores = [ 'pm_destino' => [ 'fuentes', 'faqs', 'aguas', 'temporada', 'mas' ], 'pm_especie' => [ 'fuentes' ] ];

$vacia = fn( $fila ) => ! array_filter( array_map( fn( $v ) => is_string( $v ) ? trim( $v ) : $v, (array) $fila ) );

foreach ( $repetidores as $tipo => $nombres ) {
	foreach ( pm_posts( $tipo ) as $p ) {
		$i = pm_idioma_de( $p->ID );
		foreach ( $nombres as $n ) {
			$filas = pm_filas( $n, $p->ID );
			$vacias = array_keys( array_filter( $filas, $vacia ) );
			if ( ! $filas ) {
				continue;
			}
			WP_CLI::log( sprintf( '%s %s (%s) %s: %d filas, %d vacías', $tipo, $p->post_name, $i, $n, count( $filas ), count( $vacias ) ) );
			if ( ! $vacias || 'fuentes' !== $n || 'es' !== $i ) {
				continue;
			}
			$otra = pm_buscar( $tipo, pm_clave_de( $p->ID ), 'pt' ) ?? pm_buscar( $tipo, pm_clave_de( $p->ID ), 'en' );
			if ( ! $otra ) {
				WP_CLI::warning( "  sin traducción de donde restaurar" );
				continue;
			}
			$fuente = pm_filas( 'fuentes', $otra->ID );
			$nuevo  = $filas;
			$cambios = 0;
			foreach ( $vacias as $k ) {
				if ( isset( $fuente[ $k ] ) && ! $vacia( $fuente[ $k ] ) ) {
					$nuevo[ $k ] = $fuente[ $k ];
					$cambios++;
				}
			}
			WP_CLI::log( "  restaurables desde " . pm_idioma_de( $otra->ID ) . ": $cambios de " . count( $vacias ) );
			if ( $aplicar && $cambios ) {
				update_field( 'fuentes', $nuevo, $p->ID );
				$leidas = pm_filas( 'fuentes', $p->ID );
				WP_CLI::log( '  ✓ escrito; primera fila ahora: ' . wp_json_encode( $leidas[0] ?? null, JSON_UNESCAPED_UNICODE ) );
			}
		}
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
