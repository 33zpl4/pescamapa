<?php
/**
 * Read-only, reusable: which public outfitter cards are due for a new check. A card is due when its newest date (data
 * verified or price read) is older than PM_PREST_REVISION_DIAS (90 days) or missing. Prints the source to re-read and the
 * internal notes' first line (where the platform reference price lives). Run with wp-script.yml (script = fichas-por-revisar)
 * once a month, or whenever a batch of listings turns three months old. Never prints contact data.
 */

$hoy  = time();
$due  = [];
$ok   = 0;
foreach ( pm_posts( 'pm_prestador' ) as $p ) {
	$r = pm_prestador( $p->ID );
	if ( ! pm_prestador_publico( $r ) ) {
		continue;
	}
	$f    = max( (string) pm_campo( 'verificado', $p->ID ), $r['precio_fecha'] );
	$dias = $f ? (int) floor( ( $hoy - strtotime( $f ) ) / DAY_IN_SECONDS ) : null;
	if ( null === $dias || $dias > PM_PREST_REVISION_DIAS ) {
		$nota  = trim( strtok( (string) pm_campo( 'notas', $p->ID ), "\n" ) );
		$due[] = sprintf( '  #%d %s (%s) — %s — fuente: %s — %s', $r['id'], $r['nombre'], $r['destino'], null === $dias ? 'sin fecha' : "{$dias} días", $r['fuente'] ?: 'sin fuente', mb_substr( $nota, 0, 140 ) );
	} else {
		$ok++;
	}
}
WP_CLI::log( 'Fichas públicas al día (≤ ' . PM_PREST_REVISION_DIAS . ' días): ' . $ok );
WP_CLI::log( 'Fichas por revisar: ' . count( $due ) );
foreach ( $due as $l ) {
	WP_CLI::log( $l );
}
WP_CLI::log( 'FIN (solo lectura)' );
