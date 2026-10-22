<?php
/**
 * 22/10/2026: the source "licenciasrecursosnaturales.gob.ar" cited in the Paso de la
 * Patria guide does not resolve (ENOTFOUND from GitHub). Owner OK: point it to the
 * provincial Turismo page that lists the permits, which does open.
 *
 * Finds every `fuentes` row of every guide (all languages) whose url contains the dead
 * domain and rewrites only its name and url (other fields stay as they are). Idempotent.
 * Dry run was clean (2 rows: es and pt of Paso de la Patria); applied. Run with wp-script.yml (script = fuente-licencias-2026-10-22).
 */

$aplicar = true;

$dominio = 'licenciasrecursosnaturales.gob.ar';
$nuevo   = 'https://corrientes.tur.ar/tramites-y-permisos/';
$nombres = [
	'es' => 'Ministerio de Turismo de Corrientes: trámites y permisos (licencia de pesca)',
	'pt' => 'Ministério do Turismo de Corrientes: trâmites e permissões (licença de pesca)',
	'en' => 'Corrientes Ministry of Tourism: procedures and permits (fishing licence)',
];

$tocadas = 0;
foreach ( pm_posts( 'pm_destino' ) as $p ) {
	$i     = pm_idioma_de( $p->ID );
	$filas = pm_filas( 'fuentes', $p->ID );
	$cambio = false;
	foreach ( $filas as $k => $f ) {
		if ( false === strpos( (string) ( $f['url'] ?? '' ), $dominio ) ) {
			continue;
		}
		WP_CLI::log( sprintf( '%s (%s) fila %d: %s -> %s', $p->post_name, $i, $k, $f['url'], $nuevo ) );
		$filas[ $k ]['url']    = $nuevo;
		$filas[ $k ]['nombre'] = $nombres[ $i ] ?? $nombres['es'];
		$cambio = true;
		$tocadas++;
	}
	if ( $cambio && $aplicar ) {
		update_field( 'fuentes', $filas, $p->ID );
		$leidas = pm_filas( 'fuentes', $p->ID );
		$ok = true;
		foreach ( $leidas as $f ) {
			$ok = $ok && false === strpos( (string) ( $f['url'] ?? '' ), $dominio );
		}
		WP_CLI::log( '  VERIFIED: ' . ( $ok ? 'ya no queda el dominio viejo' : 'SIGUE el dominio viejo' ) . '; filas: ' . count( $leidas ) . ' (antes ' . count( $filas ) . ')' );
	}
}
WP_CLI::log( "$tocadas fila(s) con el dominio viejo. " . ( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' ) );
