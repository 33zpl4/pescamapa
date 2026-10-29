<?php
/**
 * English guides, 29/10/2026: English speakers search "golden dorado", not "dorado" (Keyword Planner export,
 * docs/seo-palabras-clave-en-2026-10-29.md). The live read (leer-titulos-en-2026-10-29) showed that among the English pages
 * that mention dorado only the Esquina guide still lacked "golden" (the species page and the "best time" note already have it).
 * Rewrites the Esquina (en) SEO title, description and H1 ONLY if each still holds the exact text read live on 29/10/2026;
 * an editor's own text is left alone. Dry run first. Run with wp-script.yml (script = en-golden-dorado-2026-10-29).
 */

$aplicar = false;

$cambios = [
	'seo_title'       => [
		'Fishing in Esquina, Corrientes: dorado, surubí and seasons',
		'Fishing in Esquina: golden dorado, surubí and seasons',
	],
	'seo_description' => [
		'Fishing guide to Esquina, Corrientes: dorado and surubí on the Paraná and the Corriente River, when to go, licence, closures, guides and the Pacú Festival.',
		'Esquina, Corrientes fishing guide: golden dorado and surubí on the Paraná and Corriente rivers, when to go, licence, closures, guides and the Pacú Festival.',
	],
	'h1'              => [
		'Sport fishing in Esquina, Corrientes: a complete guide to species, seasons and services',
		'Sport fishing in Esquina, Corrientes: golden dorado, surubí, seasons and services',
	],
];
$limites = [ 'seo_title' => 60, 'seo_description' => 160, 'h1' => 100 ];

$p = pm_buscar( 'pm_destino', 'esquina', 'en' );
if ( ! $p ) {
	WP_CLI::error( 'No encontré Esquina (en).' );
}
foreach ( $cambios as $campo => [ $viejo, $nuevo ] ) {
	$actual = trim( (string) pm_campo( $campo, $p->ID ) );
	if ( $actual === $nuevo ) {
		WP_CLI::log( "$campo: ya dice golden dorado, no toco nada." );
		continue;
	}
	if ( $actual !== $viejo ) {
		WP_CLI::log( "$campo: tiene otro texto (" . mb_strlen( $actual ) . ' car.): no lo toco.' );
		continue;
	}
	if ( mb_strlen( $nuevo ) > $limites[ $campo ] ) {
		WP_CLI::warning( "$campo: el texto nuevo mide " . mb_strlen( $nuevo ) . " (> {$limites[ $campo ]}): no lo toco." );
		continue;
	}
	WP_CLI::log( "esquina (en) #{$p->ID} $campo: " . ( $aplicar ? 'se cambia' : 'se cambiaría' ) . ' (' . mb_strlen( $viejo ) . ' → ' . mb_strlen( $nuevo ) . " car.)\n  antes: $viejo\n  ahora: $nuevo" );
	if ( $aplicar ) {
		update_field( $campo, $nuevo, $p->ID );
		WP_CLI::log( '  VERIFIED: ' . ( trim( (string) pm_campo( $campo, $p->ID ) ) === $nuevo ? 'leído de vuelta' : 'NO coincide' ) );
	}
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
