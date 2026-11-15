<?php
/**
 * Portuguese guides of the Litoral: adds the FAQ question "Quanto custa pescar em <lugar>?" (Keyword Planner Brazil, 15/11/2026: "quanto custa", "valores", "preço" are
 * the price terms people use). The answer quotes NO figure: it names what the price depends on and links to the dated price notes (the figures live there, each with
 * the date it was read). Guides: Esquina, Paso de la Patria, Goya, Itatí, Itá Ibaté, Esteros do Iberá (La Paz has its own license-fee questions).
 * The answer carries two links: the FAQ field is a plain textarea and the theme now lets `<a href>` through (destino.php, wp_kses) and strips tags in the FAQPage
 * JSON-LD (seo.php). RUN ONLY AFTER that theme is deployed, or the answer prints the raw tags. Skipped when the guide already has a "Quanto custa pescar em…"
 * question. Dry run first ($aplicar = false). Run with wp-script.yml (script = pt-faq-custo-2026-11-15).
 */
$aplicar = true;

$guias = [
	'esquina'           => 'Esquina',
	'paso-de-la-patria' => 'Paso de la Patria',
	'goya'              => 'Goya',
	'itati'             => 'Itatí',
	'ita-ibate'         => 'Itá Ibaté',
	'esteros-del-ibera' => 'Esteros do Iberá',
];
$nota_url = function ( string $clave ): ?string {
	$n = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $x ) => 'pt' === pm_idioma_de( $x->ID ) ) )[0] ?? null;
	return $n ? get_permalink( $n->ID ) : null;
};
$u_lodge  = $nota_url( 'precio-viaje-dorado-lodge' );
$u_brasil = $nota_url( 'pesca-desde-brasil-corrientes' );
if ( ! $u_lodge || ! $u_brasil ) {
	WP_CLI::warning( 'faltan las notas de precios en portugués (' . ( $u_lodge ? '' : 'precio-viaje-dorado-lodge ' ) . ( $u_brasil ? '' : 'pesca-desde-brasil-corrientes' ) . '): no toco nada.' );
	return;
}
$cambios = 0;
$faltan  = 0;
foreach ( $guias as $clave => $lugar ) {
	$p = pm_buscar( 'pm_destino', $clave, 'pt' );
	if ( ! $p ) {
		WP_CLI::warning( "guía $clave (pt): no existe." );
		$faltan++;
		continue;
	}
	$filas = pm_filas( 'faqs', $p->ID );
	foreach ( $filas as $f ) {
		if ( preg_match( '/Quanto custa pescar em/iu', (string) ( $f['pregunta'] ?? '' ) ) ) {
			WP_CLI::log( "guía $clave #{$p->ID}: ya tiene «{$f['pregunta']}»." );
			continue 2;
		}
	}
	$q = "Quanto custa pescar em {$lugar}?";
	$a = 'Depende dos dias de pesca, do tipo de hospedagem (pousada ou lodge) e de o pacote incluir ou não a licença e os traslados: confirme isso com o operador antes de reservar. '
		. 'Os valores que cada pousada e lodge publica, com a data em que os lemos, estão em <a href="' . esc_url( $u_lodge ) . '">Quanto custa uma viagem de pesca de dourado na Argentina (valores 2026/27)</a>; '
		. 'para quem vem do Brasil, a licença de Corrientes, os documentos e as rotas estão em <a href="' . esc_url( $u_brasil ) . '">Pesca em Corrientes desde o Brasil</a>.';
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía $clave #{$p->ID}: pregunta nº " . ( count( $filas ) + 1 ) . " «{$q}» (" . count( $filas ) . ' preguntas antes)' );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$filas[] = [ 'pregunta' => $q, 'respuesta' => $a ];
	update_field( 'faqs', $filas, $p->ID );
	$leidas = pm_filas( 'faqs', $p->ID );
	$ult    = end( $leidas );
	$ok     = count( $leidas ) === count( $filas ) && ( $ult['pregunta'] ?? '' ) === $q && false !== strpos( (string) ( $ult['respuesta'] ?? '' ), $u_lodge ) && 'pt' === pm_idioma_de( $p->ID );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": guía $clave #{$p->ID} tiene " . count( $leidas ) . ' preguntas, la última «' . ( $ult['pregunta'] ?? '?' ) . '» ' . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "$clave: la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios guías, $faltan que faltan." );
