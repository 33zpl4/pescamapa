<?php
/**
 * Portuguese (Brazil) version of the Chascomús guide (clave chascomus), 31/10/2026: owner's rule of completing languages. Translated
 * from the LIVE English guide (volcar-guias-en-2026-10-31). Same limits and the same PENDIENTE markers (distance, ramps, fees).
 * Shared data stays on the Spanish guide. Idempotent. Dry run first ($aplicar = false); run with wp-script.yml
 * (script = chascomus-pt-2026-10-31).
 */

$aplicar = true;
$hoy     = '20261031';

function pm_cp_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_cp_h( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_cp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_cp_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'lic'   => 'https://www.gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva',
	'dec'   => 'https://normas.gba.gob.ar/documentos/V9A2qFPV.html',
	'vedas' => 'https://www.gba.gob.ar/desarrollo_agrario/DAPA/vedas',
	'tur'   => 'https://chascomus.gob.ar/turismo/seccion/24/chascomus-actual?subsec=calidaddevida',
	'nota'  => 'https://www.chascomus.gob.ar/digital/nota/12430/recomendaciones-para-disfrutar-la-laguna-de-forma-segura-y-responsable.html',
];
$tel = '(2241) 603414 ou (2241) 430405';

$cuerpo = implode( "\n\n", [
	pm_cp_p( '<strong>Chascomús</strong> fica na província de <strong>Buenos Aires</strong>, na maior das sete lagoas do sistema das Encadenadas: cerca de 3.044 hectares, segundo o site de turismo municipal. É pesca de <strong>peixe-rei</strong> (pejerrey) numa lagoa rasa, a um dia de viagem da cidade e muito diferente da pesca de rio do Litoral: aqui as regras são as da Província de Buenos Aires, com uma <strong>veda anual</strong> e um <strong>tamanho mínimo</strong>, e o que mais muda a saída é o <strong>vento</strong>.' ),
	pm_cp_pendiente( 'confirmar com fonte aberta a distância até a cidade de Buenos Aires (a imprensa diz ≈120 km), a estrada de acesso e a profundidade média da lagoa.' ),
	pm_cp_h( 'Licença de pesca esportiva' ),
	pm_cp_p( 'Quem pesca em águas da província de Buenos Aires precisa de uma <a href="' . $u['lic'] . '" rel="noopener">licença de pesca esportiva</a> (conferido em 27/10/2026), fixada pelo artigo 21 do <a href="' . $u['dec'] . '" rel="noopener">Decreto 3237/95</a>, que regulamenta a Lei 11.477. Segundo o site do Ministério de Desenvolvimento Agrário:' ),
	pm_cp_lista( [
		'<strong>Menores de 14 anos</strong> também precisam tirar: é gratuita.',
		'<strong>Aposentados</strong> a obtêm apresentando a carteira de aposentado, gratuita e sem vencimento.',
		'Quem não tem aposentadoria e tem mais de 65 anos (homens) ou 60 (mulheres) e comprova residência na província de Buenos Aires tem direito a uma licença nas mesmas condições da de aposentado (Resolução 46/05).',
		'Para informações e trâmite: Dirección Provincial de Pesca, rua 51 esquina 12, Torre I, 8º andar, telefone +54 221 429-5315, e as instruções no site do Ministério.',
	] ),
	pm_cp_p( 'O preço das licenças pagas não está nessa página nem no decreto: <strong>não conseguimos confirmá-lo</strong>. Confira o site do Ministério antes de ir.' ),
	pm_cp_h( 'Tamanho mínimo do peixe-rei: 25 cm' ),
	pm_cp_p( 'O Ministério de Desenvolvimento Agrário de Buenos Aires indica, na sua régua oficial de medição (material de divulgação, conferido em 27/10/2026), um <strong>tamanho mínimo de captura de 25 cm</strong> para o peixe-rei, espécie nativa. Meça o peixe antes de decidir ficar com ele: o que for menor volta para a água.' ),
	pm_cp_h( 'Veda anual do peixe-rei: de 1º de setembro a 30 de novembro' ),
	pm_cp_p( 'Segundo a <a href="' . $u['vedas'] . '" rel="noopener">página de vedas</a> do Ministério (conferida em 27/10/2026), a veda do peixe-rei vai <strong>de 1º de setembro a 30 de novembro</strong> em toda a província (Disposición 89/08, renovada automaticamente). Nesse período:' ),
	pm_cp_lista( [
		'São proibidas as competições esportivas de peixe-rei e a pesca comercial em águas interiores.',
		'A pesca esportiva e recreativa de peixe-rei é permitida só aos <strong>sábados, domingos e feriados</strong>, dentro do limite diário por pescador (em algumas lagoas, como Chasicó, Cochicó e del Monte, também às sextas).',
		'A navegação a motor para esporte é proibida e os barcos com motor de popa de combustão interna podem navegar a no máximo <strong>15 km/h</strong>.',
		'Máximo de <strong>10 peças</strong> por pescador e por dia em Chascomús (grupo "as demais lagoas bonaerenses").',
	] ),
	pm_cp_p( 'Durante a veda a página fixa o limite diário por pescador lagoa por lagoa. Chascomús não tem limite próprio: entra em <strong>"as demais lagoas bonaerenses", com 10 peças por pescador e por dia</strong>. Confirme na página oficial antes de ir, porque as disposições mudam.' ),
	pm_cp_h( 'Onde pescar: a lagoa e seus acessos' ),
	pm_cp_p( 'Segundo o <a href="' . $u['tur'] . '" rel="noopener">site de turismo de Chascomús</a>, a lagoa é uma das escolhidas para pesca esportiva e esportes náuticos, e tem uma estrada de circunvalação de cerca de 30 km. O píer e mirante dos pescadores fica na parte central da orla, administrado pela prefeitura.' ),
	pm_cp_lista( [
		'<strong>Pesca da margem:</strong> o píer dos pescadores e a orla, na área central. Não encontramos tarifa publicada para usá-los; confirme com o escritório de turismo.',
		'<strong>Levar o próprio barco:</strong> a prefeitura pede que os barcos sejam colocados e retirados <strong>só nas rampas habilitadas</strong>, que se respeitem as áreas demarcadas com boias e que se navegue em velocidade adequada (<a href="' . $u['nota'] . '" rel="noopener">nota municipal de 6/1/2026</a>). Quais são essas rampas, quanto custam e que exigências valem para navegar (registro, seguro, velocidade): <strong>não conseguimos confirmar</strong>.',
		'<strong>Alugar um barco:</strong> o site de turismo municipal menciona aluguel de barcos para passeio ou pesca esportiva, sem dizer quem oferece, nem o preço ou as condições.',
		'<strong>Guarderias e clubes:</strong> há guarderias de barcos privadas e clubes ao longo da orla; suas tarifas não são publicadas e alguns clubes reservam os barcos para os sócios.',
		'<strong>Taxa municipal:</strong> não conseguimos confirmar se há cobrança para entrar ou colocar um barco na água, nem o valor.',
		'<strong>Estacionamento e acessibilidade</strong> em cada acesso: não conseguimos confirmar.',
	] ),
	pm_cp_p( 'Para confirmar as rampas habilitadas, custos, exigências e aluguel, ligue para o <strong>Escritório de Turismo de Chascomús: ' . $tel . '</strong>, ou pergunte ao seu guia antes de levar o próprio barco.' ),
	pm_cp_pendiente( 'acessos, rodada 3 (28/10/2026, docs/investigacion/chascomus.md §12): quais rampas estão habilitadas e seu custo; a taxa municipal de embarcações (valor e se vale hoje); exigências da Prefectura (registro, seguro, velocidade em nós, zonas); quem aluga barcos e a que preço; estacionamento e acessibilidade; se o píer cobra. As duas páginas municipais citadas vieram do Gemini e não foram abertas do sandbox: o proprietário as abre antes de contarem como confirmadas.' ),
	pm_cp_h( 'O vento decide' ),
	pm_cp_p( 'Numa lagoa rasa como esta, o vento decide a saída. No alto deste guia mostramos o <strong>vento e a chuva</strong> dos últimos dias e dos próximos (modelo Open-Meteo para Chascomús): uma referência, não uma garantia.' ),
	pm_cp_h( 'Guias, barqueiros e hospedagem' ),
	pm_cp_p( 'Ainda não listamos guias, barqueiros nem hospedagem: não temos nem site próprio deles nem consentimento. Se você oferece esse serviço em Chascomús, escreva para info@pescamapa.com. Nunca publicamos contatos que você não tenha confirmado.' ),
	pm_cp_h( 'Outras lagoas próximas' ),
	pm_cp_p( 'Chascomús faz parte das Encadenadas, um sistema de sete lagoas conectadas. As outras têm seus próprios acessos e regras: vamos incluí-las quando tivermos fontes abertas.' ),
	pm_cp_h( 'Regras gerais para ir tranquilo' ),
	pm_cp_lista( [
		'Leve uma licença válida (o trâmite e as isenções estão acima).',
		'Meça cada peixe-rei: 25 cm é o tamanho mínimo de captura.',
		'De 1º de setembro a 30 de novembro, só sábados, domingos e feriados, até 10 peças por pescador e por dia; sem navegação esportiva a motor e no máximo 15 km/h com motor de popa.',
		'As regras mudam por disposição oficial: confirme na página de vedas do Ministério ou com a Dirección Provincial de Pesca.',
	] ),
] );

$fuentes = array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $x[3] ], [
	[ 'Ministério de Desenvolvimento Agrário de Buenos Aires: vedas (veda anual do peixe-rei)', $u['vedas'], 1, '20261027' ],
	[ 'Ministério de Desenvolvimento Agrário de Buenos Aires: obtenha sua licença de pesca esportiva', $u['lic'], 1, '20261027' ],
	[ 'Decreto 3237/95 (regulamento da Lei 11.477), texto atualizado', $u['dec'], 1, '20261027' ],
	[ 'Turismo de Chascomús (Prefeitura)', 'https://turismo.chascomus.gob.ar/', 1, '20261027' ],
	[ 'Canal 26: a lagoa com a melhor pesca de peixe-rei, orla e entardeceres (19/05/2026)', 'https://www.canal26.com/turismo/2026/05/19/finde-largo-del-25-de-mayo-la-laguna-con-mejor-pesca-de-pejerrey-costanera-y-atardeceres-en-familia/', 0, '20261027' ],
	[ 'Prefeitura de Chascomús: recomendações para aproveitar a lagoa com segurança e responsabilidade (6/1/2026)', $u['nota'], 1, '20261028' ],
	[ 'Turismo de Chascomús: a lagoa, a pesca esportiva e o píer dos pescadores', $u['tur'], 1, '20261028' ],
] );

$g = [
	'titulo' => 'Chascomús', 'slug' => 'pesca-em-chascomus',
	'campos' => [
		'h1'          => 'Pesca de peixe-rei em Chascomús: licença, tamanho mínimo, veda e vento',
		'bajada'      => 'A lagoa de Chascomús, na província de Buenos Aires: qual licença você precisa, o tamanho mínimo de 25 cm, a veda de 1º de setembro a 30 de novembro e o vento do dia.',
		'modalidades' => "Pesca da margem (píer e orla)\nPesca embarcada (rampas)\nDia com guia",
		'epoca'       => 'A veda do peixe-rei vai de 1º de setembro a 30 de novembro; no resto do ano pesca-se com licença e respeitando o tamanho mínimo.',
		'seo_title'   => 'Pesca de peixe-rei em Chascomús: licença, veda e tamanho',
		'seo_description' => 'Guia de pesca da lagoa de Chascomús (Buenos Aires): licença, tamanho mínimo de 25 cm, veda do peixe-rei de 1º/9 a 30/11, acessos e vento. Fontes oficiais.',
		'temporada'   => [
			[ 'meses' => '1º de setembro – 30 de novembro', 'que' => 'Veda do peixe-rei em toda a província: só sábados, domingos e feriados, até 10 peças por pescador e por dia em Chascomús, sem competições nem navegação esportiva a motor (Disposición 89/08, página de vedas do Ministério).' ],
			[ 'meses' => 'Resto do ano', 'que' => 'Pesca com licença e respeitando o tamanho mínimo de 25 cm. Confirme limites e disposições vigentes com o Ministério.' ],
			[ 'meses' => 'Antes de cada saída', 'que' => 'Confira o vento do dia e as disposições vigentes: mudam por resolução.' ],
		],
		'reglamento_resumen' => 'Lei 11.477 e Decreto 3237/95 (província de Buenos Aires): licença de pesca esportiva obrigatória (gratuita para menores de 14, aposentados e quem se enquadra na Resolução 46/05; preço das demais: perguntar ao Ministério). Tamanho mínimo do peixe-rei: 25 cm. Veda de 1/9 a 30/11: só sábados, domingos e feriados, até 10 peças por pescador e por dia em Chascomús, sem competições nem navegação esportiva a motor, motores de popa a no máximo 15 km/h (conferido em 27/10/2026).',
		'aguas' => [
			[ 'nombre' => 'Lagoa de Chascomús', 'nota' => 'A maior das sete Encadenadas (≈3.044 ha, segundo o site municipal). Píer e mirante dos pescadores na orla; estrada de circunvalação de cerca de 30 km.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Preciso de licença para pescar na lagoa de Chascomús?', 'respuesta' => 'Sim: quem pesca em águas da província de Buenos Aires precisa de licença de pesca esportiva (artigo 21 do Decreto 3237/95). Menores de 14 anos e aposentados a obtêm de graça (conferido em 27/10/2026 no site do Ministério de Desenvolvimento Agrário). Não conseguimos confirmar o preço das demais: pergunte ao Ministério.' ],
			[ 'pregunta' => 'Qual é o tamanho mínimo do peixe-rei em Buenos Aires?', 'respuesta' => '25 cm, segundo a régua oficial de medição do Ministério de Desenvolvimento Agrário de Buenos Aires (conferido em 27/10/2026). Os menores são devolvidos.' ],
			[ 'pregunta' => 'Quando é a veda do peixe-rei?', 'respuesta' => 'De 1º de setembro a 30 de novembro em toda a província de Buenos Aires (Disposición 89/08, renovada automaticamente), segundo a página de vedas do Ministério (conferida em 27/10/2026).' ],
			[ 'pregunta' => 'Posso pescar peixe-rei durante a veda?', 'respuesta' => 'Só pesca esportiva e recreativa, aos sábados, domingos e feriados, dentro do limite diário por pescador. Competições de peixe-rei e pesca comercial são proibidas.' ],
			[ 'pregunta' => 'Quantos peixes-rei posso levar por dia em Chascomús?', 'respuesta' => 'Durante a veda, 10 peças por pescador e por dia: Chascomús não tem limite próprio e entra em "as demais lagoas bonaerenses" na página de vedas do Ministério (conferida em 27/10/2026). Confirme nessa página antes de ir.' ],
			[ 'pregunta' => 'Posso usar motor durante a veda?', 'respuesta' => 'A navegação esportiva a motor é proibida durante a veda, e os barcos com motor de popa de combustão interna podem navegar a no máximo 15 km/h, segundo a página de vedas do Ministério.' ],
			[ 'pregunta' => 'Onde posso pescar sem barco em Chascomús?', 'respuesta' => 'No píer e mirante dos pescadores e ao longo da orla na área central, administrados pela prefeitura, segundo o site de turismo de Chascomús. Não encontramos tarifa publicada para usá-los: confirme com o Escritório de Turismo, ' . $tel . '.' ],
			[ 'pregunta' => 'Há guias de pesca e aluguel de barcos?', 'respuesta' => 'O site de turismo municipal menciona aluguel de barcos para passeio ou pesca esportiva, mas não diz quem oferece nem a que preço. Não listamos guias ou barqueiros sem consentimento ou site próprio: se você oferece o serviço, escreva para info@pescamapa.com. Para alugar um barco, pergunte ao Escritório de Turismo: ' . $tel . '.' ],
			[ 'pregunta' => 'Onde posso colocar um barco na água em Chascomús?', 'respuesta' => 'A prefeitura pede que os barcos sejam colocados e retirados só nas rampas habilitadas (nota municipal de 6/1/2026). Não conseguimos confirmar quais são nem quanto custam: pergunte ao Escritório de Turismo, ' . $tel . ', antes de levar o próprio barco.' ],
		],
	],
];
$comun = [ 'clave' => 'chascomus', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'reglamento_autoridad' => 'Ministerio de Desarrollo Agrario de la Provincia de Buenos Aires (Dirección Provincial de Pesca)', 'reglamento_url' => $u['lic'], 'reglamento_verificado' => '' ];

$t = mb_strlen( $g['campos']['seo_title'] );
$d = mb_strlen( $g['campos']['seo_description'] );
( $t <= 60 && $d >= 70 && $d <= 160 ) || WP_CLI::error( "SEO fuera de rango: title $t, description $d" );
pm_buscar( 'pm_destino', 'chascomus', 'es' ) || WP_CLI::error( 'no existe la guía chascomus (es).' );
if ( pm_buscar( 'pm_destino', 'chascomus', 'pt' ) ) {
	WP_CLI::log( 'chascomus (pt) ya existe: no toco nada.' );
	return;
}
WP_CLI::log( 'chascomus (pt): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas, ' . count( $g['campos']['aguas'] ) . ' aguas y ' . count( $fuentes ) . " fuentes (title $t, desc $d)" );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
foreach ( $g['campos'] + $comun + [ 'idioma' => 'pt', 'fuentes' => $fuentes ] as $k => $v ) {
	update_field( $k, $v, $id );
}
update_post_meta( $id, '_pm_traducido', time() + 1 );
$dd = pm_destino( $id );
$ok = count( pm_filas( 'faqs', $id ) ) === 9 && count( pm_filas( 'fuentes', $id ) ) === 7 && count( pm_filas( 'temporada', $id ) ) === 3 && $dd['region'] && $dd['foto'];
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": chascomus (pt) #$id " . get_permalink( $id ) . " (región {$dd['region']}, foto #{$dd['foto']} heredadas)" );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
