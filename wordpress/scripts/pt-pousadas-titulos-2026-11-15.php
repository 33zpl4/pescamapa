<?php
/**
 * Portuguese titles, H1, intros and descriptions for the Brazilian search terms (Keyword Planner, Brazil, 15/11/2026): "pousada(s)" is the dominant word
 * ("pesca na argentina pousadas", "pousadas pesca argentina"), "pescaria" is the other, and "valores 2026/27" / "quanto custa" / "preço" are what people ask about prices.
 * Scope (owner, 15/11/2026): Portuguese only; Litoral guides (seo_title, seo_description, H1 and subtitle fields, and `paquetes_description` of the packages page), the
 * hub note of package pages, and the four dorado/package price notes (post title = H1, seo_title, seo_description). NOTHING else: no slug, no clave, no fact, no figure,
 * no source. No "melhor" as a promise (no superlatives without a source). "pousada(s)" only on pages that really talk about lodges and posadas (Esquina, Paso de la
 * Patria, Itatí, Itá Ibaté, package pages of Esquina and the hub); Goya, Iberá, La Paz and Bariloche get "pescaria" and Argentina/Corrientes.
 * Each field changes only when it still holds the text we read live on 15/11/2026 (investigar.yml, folder pt-vivo-20261115-2103): anything edited by a person since is
 * left alone and logged as DISTINTO. Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = pt-pousadas-titulos-2026-11-15).
 * The "Quanto custa pescar em <lugar>?" FAQ rows are in a separate script (pt-faq-custo-2026-11-15): they need the theme that renders a link in an answer.
 */
$aplicar = false;

$norm = fn( string $s ): string => trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $s ), ENT_QUOTES, 'UTF-8' ) ) );
$cambios = 0;
$distintos = 0;
$faltan = 0;
$lim = [ 'seo_title' => 60, 'seo_description' => 160, 'paquetes_description' => 160 ];

$guias = [
	'esquina' => [
		'seo_title' => [ 'Pesca em Esquina, Corrientes: dourado, surubim e temporadas', 'Pesca em Esquina, Corrientes: pousadas, dourado e surubim' ],
		'seo_description' => [ 'Guia de pesca em Esquina, Corrientes: dourado e surubim no Paraná e no rio Corriente, quando ir, licença, proibições, guias e a Festa do Pacu.', 'Pesca em Esquina, Corrientes, Argentina: pousadas de pesca e guias, dourado e surubim no Paraná e no rio Corriente, quando ir, licença e proibições.' ],
		'h1' => [ 'Pesca esportiva em Esquina, Corrientes: guia completo de espécies, temporadas e serviços', 'Pesca esportiva em Esquina, Corrientes: pousadas de pesca, espécies e temporadas' ],
		'bajada' => [ 'Onde o rio Corriente se abre para o Paraná há ilhas, riachos e banhados. Dourado, surubim e pacu com devolução, e como ler o rio antes de ir.', 'Onde o rio Corriente se abre para o Paraná há ilhas, riachos e banhados. Pousadas de pesca e pescaria de dourado, surubim e pacu com devolução, e como ler o rio antes de ir.' ],
		'paquetes_description' => [ 'Pacotes de pescaria em Esquina, Argentina: isca viva, artificiais, mosca, deriva e corrico, tudo incluído e pacotes com hospedagem. Peça opções.', 'Pacotes de pescaria e pousadas de pesca em Esquina, Corrientes, Argentina: isca viva, artificiais, mosca, corrico e pacotes com hospedagem. Peça opções.' ],
	],
	'paso-de-la-patria' => [
		'seo_title' => [ 'Pesca em Paso de la Patria: dourado, tamanhos, limites e licença', 'Pesca em Paso de la Patria: pousadas, dourado e licença' ],
		'seo_description' => [ 'Guia de pesca em Paso de la Patria, Corrientes: dourado e surubim no Paraná, tamanhos e limites vigentes, licença, festas do dourado e como chegar.', 'Pesca em Paso de la Patria, Corrientes, Argentina: pousadas e guias, dourado e surubim no Paraná, tamanhos e limites vigentes, licença e festas do dourado.' ],
		'h1' => [ 'Pesca esportiva em Paso de la Patria, Corrientes: dourado, regras e licença', 'Pesca esportiva em Paso de la Patria, Corrientes: pousadas, dourado, regras e licença' ],
		'bajada' => [ 'O dourado é o emblema de Paso de la Patria, no Paraná e perto da confluência com o Paraguai. O que pode levar, que licença é preciso e quando são os torneios.', 'O dourado é o emblema de Paso de la Patria, no Paraná e perto da confluência com o Paraguai. Pousadas de pesca e guias, o que pode levar, que licença é preciso e quando são os torneios.' ],
	],
	'itati' => [
		'seo_title' => [ 'Pesca de dourado em Itatí: lodges, regras e licença', 'Pescaria de dourado em Itatí: pousadas, regras e licença' ],
		'seo_description' => [ 'Pesca de dourado em Itatí e no Alto Paraná, Corrientes: dois lodges com o que cada operador publica, pesca e devolução, licença, defesos de 2026 e como chegar.', 'Pescaria de dourado em Itatí e no Alto Paraná, Corrientes: lodges e pousadas com o que cada operador publica, devolução, licença, defesos de 2026 e como chegar.' ],
		'h1' => [ 'Pesca de dourado em Itatí e no Alto Paraná, Corrientes: lodges, regras e licença', 'Pescaria de dourado em Itatí e no Alto Paraná, Corrientes: lodges e pousadas, regras e licença' ],
		'bajada' => [ 'Itatí, no Alto Paraná, tem lodges de dourado com guia. O que diz a página de cada operador, o que pode levar, que licença é preciso e o que houve com os defesos de 2026.', 'Itatí, no Alto Paraná, tem lodges e pousadas de pesca de dourado, com guia. O que diz a página de cada operador, o que pode levar, que licença é preciso e o que houve com os defesos de 2026.' ],
	],
	'ita-ibate' => [
		'seo_title' => [ 'Pesca de dourado em Itá Ibaté: regras, licença e lodge', 'Pousada de pesca em Itá Ibaté: dourado, regras e licença' ],
		'seo_description' => [ 'Pesca de dourado em Itá Ibaté, Alto Paraná, Corrientes: o relatório COMIP–UNNE, o lodge com página própria, pesca e devolução, licença e defesos de 2026.', 'Pescaria de dourado em Itá Ibaté, Alto Paraná, Corrientes: o relatório COMIP–UNNE, a pousada de pesca com página própria, devolução, licença e defesos de 2026.' ],
		'h1' => [ 'Pesca de dourado em Itá Ibaté, Corrientes: o que há, regras e licença', 'Pescaria de dourado em Itá Ibaté, Corrientes: pousada, regras e licença' ],
		'bajada' => [ 'Itá Ibaté, no Alto Paraná, vive da pesca recreativa e recebe sobretudo pescadores do Brasil. O que diz o relatório COMIP–UNNE, o que publica o único lodge com página própria que encontramos e quais regras valem.', 'Itá Ibaté, no Alto Paraná, vive da pesca recreativa e recebe sobretudo pescadores do Brasil. O que diz o relatório COMIP–UNNE, o que publica a pousada de pesca (lodge) cuja página lemos e quais regras valem.' ],
	],
	'esteros-del-ibera' => [
		'seo_title' => [ 'Pesca de dourado nos Esteros do Iberá: lodges e regras', 'Pescaria de dourado nos Esteros do Iberá: lodges e regras' ],
		'seo_description' => [ 'Pesca de dourado nos Esteros do Iberá, Corrientes: três lodges com o que cada um publica, tarifas com data, pesca e devolução, licença e como chegar.', 'Pescaria de dourado nos Esteros do Iberá, Corrientes, Argentina: três lodges com o que cada um publica, tarifas com data, devolução, licença e como chegar.' ],
		'h1' => [ 'Pesca de dourado nos Esteros do Iberá, Corrientes: lodges, regras e licença', 'Pescaria de dourado nos Esteros do Iberá, Corrientes: lodges, regras e licença' ],
	],
	'goya' => [
		'seo_title' => [ 'Pesca em Goya, Corrientes: surubim, Isoró e temporadas', 'Pescaria em Goya, Corrientes: surubim, Isoró e temporadas' ],
		'seo_description' => [ 'Guia para pescar em Goya, o berço do surubim: rio Paraná, riacho e Isoró, espécies, quando ir, licença, guias e a Festa Nacional do Surubim 2027.', 'Guia para pescar em Goya, Corrientes, o berço do surubim: rio Paraná, riacho e Isoró, espécies, quando ir, licença, guias e a Festa Nacional do Surubim 2027.' ],
		'h1' => [ 'Pesca esportiva em Goya, Corrientes: surubim, riachos e as áreas úmidas do Isoró', 'Pescaria esportiva em Goya, Corrientes: surubim, riachos e as áreas úmidas do Isoró' ],
		'paquetes_description' => [ 'Pacotes de pescaria em Goya, Argentina: dourado com devolução na Reserva Isoró, isca natural no Paraná, pesca noturna, passeios e hospedagem. Peça opções.', 'Pacotes de pescaria em Goya, Corrientes, Argentina: dourado com devolução no Isoró, isca natural no Paraná, pesca noturna, passeios e hospedagem. Peça opções.' ],
	],
	'la-paz' => [
		'seo_title' => [ 'Pesca em La Paz, Entre Ríos: licença, tarifas e regras', 'Pescaria em La Paz, Entre Ríos: licença, tarifas e regras' ],
		'seo_description' => [ 'Guia de pesca em La Paz, Entre Ríos: dourado e surubim no Paraná, licença de pesca de Entre Ríos com tarifas vigentes, o que consultar antes de sair e festas de pesca.', 'Pescaria em La Paz, Entre Ríos, Argentina: dourado e surubim no Paraná, licença de Entre Ríos com tarifas vigentes, o que consultar antes de sair e festas.' ],
	],
];

// Notes: [ clave => [ campo => [ [antes aceptados], después ] ] ]. `titulo` is the post title (the H1 of the note); the slug is never passed.
$notas = [
	'viaje-dorado-amigos-precios' => [
		'titulo'          => [ [ 'Viagem de pesca de dourado com amigos: quantos dias, quanto custa e onde se hospedar (2026/27)' ], 'Viagem de pesca de dourado com amigos: quantos dias, quanto custa e onde se hospedar (valores 2026/27)' ],
		'seo_title'       => [ [ 'Viagem de pesca de dourado com amigos: preços 2026/27' ], 'Pesca de dourado com amigos: quanto custa, valores 2026/27' ],
		'seo_description' => [ [ 'Quanto custa uma viagem de pesca de dourado com amigos na Argentina: a partir de US$ 605 por pessoa em pousada até US$ 4.050 em lodge. Dias e o que inclui.' ], 'Quanto custa pescar dourado com amigos na Argentina: valores 2026/27, de US$ 605 por pessoa em pousada a US$ 4.050 em lodge. Dias e o que inclui.' ],
	],
	'precio-viaje-dorado-lodge' => [
		'titulo'          => [ [ 'Quanto custa uma viagem de pesca de dourado na Argentina: lodges all inclusive, por quantidade de noites (2026/27)' ], 'Quanto custa uma viagem de pesca de dourado na Argentina: lodges all inclusive, por quantidade de noites (valores 2026/27)' ],
		'seo_title'       => [ [ 'Quanto custa uma viagem de pesca de dourado (2026/27)' ], 'Quanto custa pescar dourado na Argentina: valores 2026/27' ],
		'seo_description' => [ [ 'Preços de lodges de dourado na Argentina para 2026/27: de US$ 4.050 por 3 noites a US$ 7.950 por 7 noites, por pessoa, com inclusões e custo por dia.' ], 'Quanto custa pescar dourado na Argentina: valores 2026/27 de lodges, de US$ 4.050 por 3 noites a US$ 7.950 por 7, por pessoa, com o que inclui e custo por dia.' ],
	],
	'pesca-desde-brasil-corrientes' => [
		'titulo'          => [ [ 'Pesca em Corrientes desde o Brasil: licença, documentos, rotas e custos (2026/27)' ], 'Pesca em Corrientes desde o Brasil: quanto custa a licença, documentos, rotas e custos (valores 2026/27)' ],
		'seo_title'       => [ [ 'Pescar em Corrientes desde o Brasil: licença e rotas' ], 'Pescar em Corrientes vindo do Brasil: quanto custa, 2026/27' ],
		'seo_description' => [ [ 'Como pescar em Corrientes vindo do Brasil: licença de turista estrangeiro (a partir de $ 35.000 por dia), documentos de entrada e rotas com tempos.' ], 'Quanto custa pescar em Corrientes vindo do Brasil: valores 2026/27 da licença de turista estrangeiro (a partir de $ 35.000 por dia), documentos e rotas.' ],
	],
	'paquete-incluye-no-incluye' => [
		'titulo'          => [ [ 'O que inclui um pacote de pesca na Argentina e o que é cobrado à parte (exemplos 2026/27)' ], 'O que inclui um pacote de pesca na Argentina e o que é cobrado à parte (valores 2026/27)' ],
		'seo_title'       => [ [ 'Pacotes de pesca na Argentina: o que inclui e o que é extra', 'O que inclui um pacote de pesca na Argentina' ], 'Pacotes de pesca na Argentina: o que inclui, valores 2026/27' ],
		'seo_description' => [ [ 'O que está incluído e o que é cobrado à parte em pacotes de pesca na Argentina: licença, traslados, voos, quarto individual e gorjetas, com exemplos 2026/27.' ], 'O que inclui e o que é cobrado à parte em pacotes de pesca na Argentina: licença, traslados, voos, quarto individual e gorjetas, com preço e valores 2026/27.' ],
	],
	// Hub of the package pages (created 13/11/2026 by seo-consultas-pacotes-dorado-2026-11-13).
	'paquetes-de-pesca-argentina' => [
		'titulo'          => [ [ 'Pacotes de pesca na Argentina: onde há, quanto custam e o que incluem' ], 'Pousadas e pacotes de pesca na Argentina: onde há, quanto custam e o que incluem' ],
		'seo_title'       => [ [ 'Pacotes de pesca na Argentina: preços por destino' ], 'Pousadas e pacotes de pesca na Argentina: preços' ],
		'seo_description' => [ [ 'Pacotes de pesca na Argentina por destino, com preço de referência e data, e as notas de custo: o que inclui e o que é extra. Peça opções.' ], 'Pousadas e pacotes de pesca na Argentina por destino, com preço de referência e data, e as notas de custo: o que inclui e o que é extra. Peça opções.' ],
	],
];

// --- Guides (pm_destino, pt) ---
foreach ( $guias as $clave => $campos ) {
	$p = pm_buscar( 'pm_destino', $clave, 'pt' );
	if ( ! $p ) {
		WP_CLI::warning( "guía $clave (pt): no existe, no toco nada." );
		$faltan++;
		continue;
	}
	foreach ( $campos as $campo => [ $antes, $despues ] ) {
		if ( isset( $lim[ $campo ] ) && mb_strlen( $despues ) > $lim[ $campo ] ) {
			WP_CLI::error( "$clave/$campo: " . mb_strlen( $despues ) . " caracteres (máx. {$lim[ $campo ]})." );
		}
		$ahora = (string) get_post_meta( $p->ID, $campo, true );
		if ( $norm( $ahora ) === $norm( $despues ) ) {
			WP_CLI::log( "guía $clave #{$p->ID} $campo: ya está." );
			continue;
		}
		if ( $norm( $ahora ) !== $norm( $antes ) ) {
			WP_CLI::warning( "DISTINTO guía $clave #{$p->ID} $campo: tiene «$ahora» y esperaba «$antes»; no lo toco." );
			$distintos++;
			continue;
		}
		WP_CLI::log( pm_pt_modo( $aplicar ) . ": guía $clave #{$p->ID} $campo (" . mb_strlen( $despues ) . ")\n    ANTES:   $ahora\n    DESPUÉS: $despues" );
		$cambios++;
		if ( $aplicar ) {
			update_field( $campo, $despues, $p->ID );
			$leido = (string) get_post_meta( $p->ID, $campo, true );
			$ok    = $norm( $leido ) === $norm( $despues ) && 'pt' === pm_idioma_de( $p->ID ) && pm_clave_de( $p->ID ) === $clave;
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": guía $clave #{$p->ID} $campo " . get_permalink( $p->ID ) );
			$ok || WP_CLI::error( "$clave/$campo: la lectura de vuelta no coincide." );
		}
	}
}

// --- Notes (pm_nota, pt) ---
foreach ( $notas as $clave => $campos ) {
	$p = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $x ) => 'pt' === pm_idioma_de( $x->ID ) ) )[0] ?? null;
	if ( ! $p ) {
		WP_CLI::warning( "nota $clave (pt): no existe, no toco nada." );
		$faltan++;
		continue;
	}
	$slug = $p->post_name;
	foreach ( $campos as $campo => [ $antes_ok, $despues ] ) {
		if ( isset( $lim[ $campo ] ) && mb_strlen( $despues ) > $lim[ $campo ] ) {
			WP_CLI::error( "$clave/$campo: " . mb_strlen( $despues ) . " caracteres (máx. {$lim[ $campo ]})." );
		}
		$ahora = 'titulo' === $campo ? get_post( $p->ID )->post_title : (string) get_post_meta( $p->ID, $campo, true );
		if ( $norm( $ahora ) === $norm( $despues ) ) {
			WP_CLI::log( "nota $clave #{$p->ID} $campo: ya está." );
			continue;
		}
		if ( ! in_array( $norm( $ahora ), array_map( $norm, $antes_ok ), true ) ) {
			WP_CLI::warning( "DISTINTO nota $clave #{$p->ID} $campo: tiene «$ahora» y esperaba «{$antes_ok[0]}»; no lo toco." );
			$distintos++;
			continue;
		}
		WP_CLI::log( pm_pt_modo( $aplicar ) . ": nota $clave #{$p->ID} $campo (" . mb_strlen( $despues ) . ") slug $slug no cambia\n    ANTES:   $ahora\n    DESPUÉS: $despues" );
		$cambios++;
		if ( $aplicar ) {
			if ( 'titulo' === $campo ) {
				$r = wp_update_post( [ 'ID' => $p->ID, 'post_title' => $despues ], true ); // no post_name: the live slug stays
				is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
				$leido = get_post( $p->ID )->post_title;
			} else {
				update_field( $campo, $despues, $p->ID );
				$leido = (string) get_post_meta( $p->ID, $campo, true );
			}
			$ok = $norm( $leido ) === $norm( $despues ) && get_post( $p->ID )->post_name === $slug && 'pt' === pm_idioma_de( $p->ID );
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota $clave #{$p->ID} $campo (slug $slug) " . get_permalink( $p->ID ) );
			$ok || WP_CLI::error( "$clave/$campo: la lectura de vuelta no coincide o cambió el slug." );
		}
	}
}

function pm_pt_modo( bool $aplicar ): string {
	return $aplicar ? 'APLICA' : 'SIMULACRO';
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios, $distintos campos distintos (no tocados), $faltan páginas que faltan." );
