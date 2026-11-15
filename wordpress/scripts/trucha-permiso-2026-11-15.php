<?php
/**
 * Pillar note "Trout fishing in Patagonia" (clave trucha-patagonia-pilar, es/en/pt), 15/11/2026: replaces the vague "depends on the province and the year" (permit
 * section and FAQ "Do I need a permit?") with what OUR Patagonian permit note already says, with its link (clave licencia-pesca-patagonia-precios, section "¿Dónde vale el
 * permiso patagónico?" and the Río Negro paragraph): the ordinary 2026/27 permit is valid in Neuquén, Río Negro, Chubut and the National Parks; a category that belongs to
 * one province (the note's example: Río Negro's 10-day permit for foreigners from neighbouring countries) is valid only in the jurisdiction that issues it; each province
 * has annexes with exceptions by river and lake; Santa Cruz and Tierra del Fuego have their own permit.
 * NOT written, on purpose: "art. 4 of the general regulation" and "licencia social de Río Negro": our permit note does not say them (the text of art. 4 was never read; see
 * docs/investigacion/patagonia-anexos-2026-27-fuentes-2026-10-30.md), so they are not repeated here.
 * Each replacement needs the old sentence exactly once; anything else is logged as DISTINTO and left alone. Idempotent (skipped when the new sentence is there).
 * Dry run first ($aplicar = false). Run with wp-script.yml (script = trucha-permiso-2026-11-15).
 */
$aplicar = true;
$clave   = 'trucha-patagonia-pilar';
$clave_n = 'licencia-pesca-patagonia-precios';

$T = [
	'es' => [
		'ancla' => 'nuestra nota del permiso patagónico',
		'pe1'   => [
			'Cada provincia fija sus fechas y sus valores, y que el permiso patagónico valga en el ambiente que querés depende de la provincia y del año. Las notas de abajo lo explican, con fechas y fuentes:',
			'Cada provincia fija sus fechas y sus valores. Según %s, el permiso ordinario 2026/27 sirve en Neuquén, Río Negro, Chubut y los Parques Nacionales; las categorías propias de una provincia (por ejemplo, el permiso de 10 días de Río Negro para extranjeros de países limítrofes) solo valen en la jurisdicción que las emite; cada provincia tiene además anexos con excepciones por río y lago, y Santa Cruz y Tierra del Fuego tienen su propio permiso. Las notas de abajo lo explican, con fechas y fuentes:',
		],
		'faq'   => [
			'Que el permiso patagónico valga en un ambiente depende de la provincia y del año: mirá ',
			'El permiso ordinario 2026/27 sirve en Neuquén, Río Negro, Chubut y los Parques Nacionales; las categorías propias de una provincia solo valen en la jurisdicción que las emite, y cada provincia tiene anexos con excepciones por río y lago (Santa Cruz y Tierra del Fuego tienen su propio permiso). Detalle: mirá ',
		],
	],
	'en' => [
		'ancla' => 'our Patagonian permit note',
		'pe1'   => [
			'Each province sets its own dates and values, and whether the Patagonian permit is valid on the water you want depends on the province and the year. The notes below explain it, with dates and sources:',
			'Each province sets its own dates and values. According to %s, the ordinary 2026/27 permit is valid in Neuquén, Río Negro, Chubut and the National Parks; categories that belong to one province (for example, Río Negro’s 10-day permit for foreigners from neighbouring countries) are valid only in the jurisdiction that issues them; each province also has annexes with exceptions by river and lake, and Santa Cruz and Tierra del Fuego have their own permit. The notes below explain it, with dates and sources:',
		],
		'faq'   => [
			'Whether the Patagonian permit is valid on a given water depends on the province and the year: see ',
			'The ordinary 2026/27 permit is valid in Neuquén, Río Negro, Chubut and the National Parks; categories that belong to one province are valid only in the jurisdiction that issues them, and each province has annexes with exceptions by river and lake (Santa Cruz and Tierra del Fuego have their own permit). Details: see ',
		],
	],
	'pt' => [
		'ancla' => 'nossa nota da licença patagônica',
		'pe1'   => [
			'Cada província fixa suas datas e seus valores, e se a licença patagônica vale no ambiente que você quer depende da província e do ano. As notas abaixo explicam, com datas e fontes:',
			'Cada província fixa suas datas e seus valores. Segundo %s, a licença ordinária 2026/27 vale em Neuquén, Río Negro, Chubut e nos Parques Nacionais; as categorias próprias de uma província (por exemplo, a licença de 10 dias de Río Negro para estrangeiros de países limítrofes) só valem na jurisdição que as emite; cada província tem ainda anexos com exceções por rio e lago, e Santa Cruz e Tierra del Fuego têm a sua própria licença. As notas abaixo explicam, com datas e fontes:',
		],
		'faq'   => [
			'Se a licença patagônica vale em um ambiente depende da província e do ano: veja ',
			'A licença ordinária 2026/27 vale em Neuquén, Río Negro, Chubut e nos Parques Nacionais; as categorias próprias de uma província só valem na jurisdição que as emite, e cada província tem anexos com exceções por rio e lago (Santa Cruz e Tierra del Fuego têm a sua própria licença). Detalhes: veja ',
		],
	],
];
$nota = function ( string $c, string $i ): ?WP_Post {
	return array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $c, 'numberposts' => -1 ] ), fn( $x ) => pm_idioma_de( $x->ID ) === $i ) )[0] ?? null;
};
$cambios = 0;
$distintos = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = $nota( $clave, $i );
	$n = $nota( $clave_n, $i );
	if ( ! $p || ! $n ) {
		WP_CLI::warning( "($i): falta " . ( $p ? '' : "el pilar $clave " ) . ( $n ? '' : "la nota $clave_n" ) . '; no toco nada.' );
		continue;
	}
	$enlace = '<a href="' . esc_url( get_permalink( $n->ID ) ) . '">' . $T[ $i ]['ancla'] . '</a>';
	$c      = $p->post_content;
	$antes_faqs = count( pm_faqs_contenido( $c ) );
	$pares  = [
		[ $T[ $i ]['pe1'][0], sprintf( $T[ $i ]['pe1'][1], $enlace ) ],
		[ $T[ $i ]['faq'][0], $T[ $i ]['faq'][1] ],
	];
	$hechos = 0;
	foreach ( $pares as $k => [ $viejo, $nuevo ] ) {
		if ( false !== strpos( $c, $nuevo ) ) {
			WP_CLI::log( "($i) #{$p->ID} reemplazo $k: ya está." );
			$hechos++;
			continue;
		}
		if ( 1 !== substr_count( $c, $viejo ) ) {
			WP_CLI::warning( "DISTINTO ($i) #{$p->ID} reemplazo $k: la frase vieja aparece " . substr_count( $c, $viejo ) . ' veces (esperaba 1); no toco nada en esta nota.' );
			$distintos++;
			continue 2;
		}
		$c = str_replace( $viejo, $nuevo, $c );
		$hechos++;
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ($i) #{$p->ID} reemplazo $k\n    ANTES:   $viejo\n    DESPUÉS: $nuevo" );
	}
	if ( $c === $p->post_content ) {
		continue;
	}
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$slug = $p->post_name;
	$r    = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $c ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	$g  = get_post( $p->ID );
	$ok = false !== strpos( $g->post_content, $pares[0][1] ) && false !== strpos( $g->post_content, $pares[1][1] ) && false === strpos( $g->post_content, $pares[0][0] ) && false === strpos( $g->post_content, $pares[1][0] )
		&& $g->post_name === $slug && count( pm_faqs_contenido( $g->post_content ) ) === $antes_faqs && false !== strpos( $g->post_content, get_permalink( $n->ID ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": pilar ($i) #{$p->ID} con la frase del permiso y el enlace a la nota #{$n->ID}; $antes_faqs preguntas en la FAQ; slug $slug " . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios notas, $distintos distintas (no tocadas)." );
