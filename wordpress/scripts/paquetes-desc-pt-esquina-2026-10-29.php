<?php
/**
 * Esquina (pt), 29/10/2026: the Google description of the packages page was 179 characters (Google cuts near 160).
 * Replaces it with a 150-character one, ONLY if the field still holds the text paquetes-intros-2026-10-28 loaded
 * (anything an editor wrote since is left alone). Run with wp-script.yml (script = paquetes-desc-pt-esquina-2026-10-29).
 */

$aplicar = false;

$viejo = 'Pacotes de pescaria em Esquina, Argentina: isca viva, iscas artificiais, mosca, pesca à deriva e corrico, tudo incluído e pacotes de vários dias com hospedagem. Cotação sem custo.';
$nuevo = 'Pacotes de pescaria em Esquina, Argentina: isca viva, artificiais, mosca, deriva e corrico, tudo incluído e pacotes com hospedagem. Cotação sem custo.';

$p = pm_buscar( 'pm_destino', 'esquina', 'pt' );
if ( ! $p ) {
	WP_CLI::error( 'No encontré Esquina (pt).' );
}
$actual = trim( (string) pm_campo( 'paquetes_description', $p->ID ) );
if ( $actual === $nuevo ) {
	WP_CLI::log( 'Ya estaba acortada: no toco nada.' );
} elseif ( $actual !== $viejo ) {
	WP_CLI::log( 'El campo tiene otro texto (' . mb_strlen( $actual ) . ' car.): no lo toco.' );
} else {
	WP_CLI::log( "esquina (pt) #{$p->ID}: " . ( $aplicar ? 'se acorta' : 'se acortaría' ) . ' de ' . mb_strlen( $viejo ) . ' a ' . mb_strlen( $nuevo ) . ' car.' );
	if ( $aplicar ) {
		update_field( 'paquetes_description', $nuevo, $p->ID );
		WP_CLI::log( '  VERIFIED: ' . ( trim( (string) pm_campo( 'paquetes_description', $p->ID ) ) === $nuevo ? 'leído de vuelta' : 'NO coincide' ) );
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
