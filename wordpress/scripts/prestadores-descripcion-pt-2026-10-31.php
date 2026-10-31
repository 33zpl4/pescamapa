<?php
/**
 * Portuguese descriptions (and prices where the card has one) for the eleven public outfitter cards that had none, 31/10/2026:
 * owner's rule of completing languages. Translated from the Spanish base texts; same attributions and dates. A card is touched
 * only if its descripcion_pt is still empty. Dry run first ($aplicar = false); run with wp-script.yml
 * (script = prestadores-descripcion-pt-2026-10-31).
 */

$aplicar = true;

$fichas = [
	'Trout Bariloche' => [
		'pt' => 'Guias locais de Bariloche com saídas privadas de dia completo e meio dia no Limay, no Manso e nos lagos do Nahuel Huapi, de barco de descida ou vadeando. Segundo o site deles (lido em 31/10/2026): dia completo no Limay US$ 520 por barco para 1 ou 2 pescadores, com traslado desde Bariloche, equipamento de pesca e almoço à beira do rio; um terceiro pescador paga US$ 60; a licença é à parte (US$ 40) e eles providenciam. Guias que falam inglês.',
		'precio_pt' => 'A partir de US$ 520 por barco, dia completo no Limay, 1 ou 2 pescadores (licença à parte)', 'lancha_pt' => 'Barco de descida',
	],
	'Southern Rivers Patagonia' => [
		'pt' => 'Guias e outfitter de Bariloche: dia completo no Limay com guia privado e barco para 1 ou 2 pescadores, descendo o rio e parando para vadear quando o rio permite. Segundo o site deles (lido em 31/10/2026): US$ 530 no total por 1 ou 2 pescadores, com traslados desde a hospedagem em Bariloche, equipamento e almoço argentino à beira do rio; a licença (US$ 40 por pessoa e dia, eles podem providenciar) e as gorjetas são à parte. Dizem que abril e maio são os melhores meses para as trutas marrons migratórias.',
		'precio_pt' => 'US$ 530 no total por 1 ou 2 pescadores, dia completo no Limay (licença e gorjetas à parte)', 'lancha_pt' => 'Barco de descida',
	],
	'Patagonia in the Loop' => [
		'pt' => 'Serviço de guias de pesca com mosca com base em Bariloche, nos rios e lagos do Parque Nacional Nahuel Huapi: Limay, Manso e os lagos da bacia do Manso (Roca, Fonck, Mascardi, Hess). Segundo o site deles (lido em 31/10/2026): meio dia US$ 430 e dia completo US$ 550 para 1 ou 2 pescadores, com traslados de ida e volta, barco de descida (bote no Manso), equipamento e almoço à beira do rio; o Manso soma US$ 100 de logística; a licença (US$ 35, eles providenciam) e a entrada do Parque para os lagos do Manso (US$ 25) são à parte. Guia bilíngue; declara habilitação no Parque e em Neuquén.',
		'precio_pt' => 'Dia completo US$ 550 e meio dia US$ 430 por 1 ou 2 pescadores (licença à parte)', 'lancha_pt' => 'Barco de descida',
	],
	'Patagonia Drift Fly Fishing' => [
		'pt' => 'Saídas de um dia desde Bariloche em barco de descida: descida do Limay e lagos do Parque Nacional (Mascardi, Hess, Roca, Fonck, Guillelmo); também programas com lodge e expedições de vários dias com acampamento. Segundo o site deles (lido em 31/10/2026): descida do Limay US$ 550 e lagos US$ 580, dia completo para 1 ou 2 pescadores, com almoço à beira do rio com vinho e bebidas; no máximo dois pescadores por guia e barco; emprestam waders e equipamento se avisado. O site não diz se a licença está incluída: pergunte.',
		'precio_pt' => 'Descida do Limay US$ 550 e lagos US$ 580, dia completo por 1 ou 2 pescadores', 'lancha_pt' => 'Barco de descida',
	],
	'Rio Manso Lodge' => [ 'pt' => 'Lodge perto de Bariloche com pesca guiada. O site próprio publica para 2026-27 US$ 1.050 por pescador e noite ou US$ 7.000 por semana, por pessoa em quarto duplo (lido em 30/10/2026). Diz que trabalha em terras privadas e que não fica dentro de um parque nacional; as páginas do site não coincidem sobre se a licença de pesca está incluída: pergunte.' ],
	'Limay River Lodge' => [ 'pt' => 'Lodge sobre o rio Limay, construído num terreno de frente para o rio comprado em 2013; primeira temporada em 2015 (história contada pelo próprio operador). A página de pesca do site não carregou no nosso leitor, então não há nada mais confirmado: nem tarifa nem capacidade.' ],
	'Tipiliuke Lodge' => [ 'pt' => 'Lodge no vale baixo do Chimehuín, a dez minutos do aeroporto de Chapelco, com nove quartos e pesca com mosca no Chimehuín e no Quilquihue (descrição do operador). Não publica tarifa: pede que você escreva pelo formulário de reservas.' ],
	'Hostería San Huberto' => [ 'pt' => 'Lodge na região de Junín de los Andes. O site (olsenfamily.com.ar) não respondeu ao nosso leitor em 30/10/2026, então não há nada mais confirmado: nem rio, nem tarifa, nem capacidade.' ],
	'Jurassic Lake Lodge' => [ 'pt' => 'Lodge na margem sudoeste do lago Strobel, perto da foz do rio Barrancoso (descrição do operador). Segundo o próprio site: 10 quartos, pista de pouso privada e voo fretado desde El Calafate aos sábados.' ],
	'Estancia Laguna Verde' => [ 'pt' => 'Estância e lodge de pesca com mosca na costa sul do lago Strobel (descrição do operador). Vendida também por agências de viagens.' ],
	'Tres Amigos Outfitters' => [ 'pt' => 'Operador que vende uma viagem que combina Jurassic Lake e Glacier King (segundo a própria página).' ],
];

$n = 0;
foreach ( $fichas as $nombre => $f ) {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( ! $ids ) {
		WP_CLI::warning( "$nombre: no existe" );
		continue;
	}
	$pid = (int) $ids[0];
	if ( '' !== trim( (string) pm_campo( 'descripcion_pt', $pid ) ) ) {
		WP_CLI::log( "· $nombre #$pid: ya tiene portugués, no se toca" );
		continue;
	}
	$campos = [ 'descripcion_pt' => $f['pt'] ] + ( isset( $f['precio_pt'] ) ? [ 'precio_pt' => $f['precio_pt'] ] : [] ) + ( isset( $f['lancha_pt'] ) ? [ 'lancha_pt' => $f['lancha_pt'] ] : [] );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $nombre #$pid: " . implode( ', ', array_keys( $campos ) ) );
	++$n;
	if ( ! $aplicar ) {
		continue;
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	$ok = pm_campo( 'descripcion_pt', $pid ) === $f['pt'];
	WP_CLI::log( ( $ok ? '  VERIFIED' : '  FALLÓ' ) . ": $nombre" );
	$ok || WP_CLI::error( "$nombre: la lectura de vuelta no coincide." );
}
WP_CLI::log( ( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' ) . " ($n fichas)" );
