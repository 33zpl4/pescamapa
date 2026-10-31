<?php
/**
 * Portuguese (Brazil) version of the Bariloche guide (clave bariloche), 31/10/2026: owner's rule of completing languages. Mirrors
 * the English guide of 30/10 (same body, 10 FAQs, 4 waters, 3 season rows, 10 sources, same limits: Río Negro environments and the
 * general regulation text not read). Shared data (region, coordinates, species, cover photo) stays on the Spanish guide.
 * Idempotent: if the pt guide exists nothing is touched. Dry run first ($aplicar = false); run with wp-script.yml
 * (script = bariloche-pt-2026-10-31). Run BEFORE bariloche-paquetes-pt-2026-10-31 (the packages hang from this post).
 */

$aplicar = false;
$hoy     = '20261031';

function pm_bp_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_bp_h( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_bp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_bp_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'rn'    => 'https://www.permisosdepesca.com.ar/',
	'anrn'  => 'https://reglamentodepesca.org.ar/reglamento-items/anexo-rio-negro/',
	'annc'  => 'https://reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/',
	'annp'  => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/',
	'annn'  => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-neuquen/',
	'perf'  => 'https://weekend.perfil.com/noticias/pesca/temporada-de-pesca-2026-2027-en-la-patagonia-cuanto-costaran-las-licencias-y-que-cambios-habra.phtml',
	'rml'   => 'https://www.riomansolodge.com/lodge-rates-details/',
	'rmf'   => 'https://www.riomansolodge.com/faq/',
	'lrl'   => 'https://limaylodge.com/about-us/',
	'tloc'  => 'https://tipiliuke.com/location',
];

$cuerpo = implode( "\n\n", [
	pm_bp_p( '<strong>San Carlos de Bariloche</strong> é a base para pescar no <strong>lago Nahuel Huapi</strong> e nos rios do Parque Nacional Nahuel Huapi: o <strong>Manso</strong>, o <strong>Traful</strong> e o <strong>Limay</strong> superior; o Limay abaixo das barragens fica mais longe de carro. Esta página reúne as regras 2026/27 que lemos, a licença e o que ainda não lemos. É um guia para planejar: confirme tudo com o seu guia e com a autoridade antes de viajar.' ),
	pm_bp_h( 'A licença e quanto custa' ),
	pm_bp_p( 'A licença de Río Negro é emitida pela <strong>Mesa Directiva de Pesca Deportiva Zona Andina</strong> (Av. 12 de Octubre e Onelli, Bariloche), que a vende online. A temporada 2026/27 começa em <strong>1º de novembro de 2026</strong>. Valores do portal, em pesos argentinos e com impostos:' ),
	pm_bp_lista( [
		'<strong>Estrangeiros não residentes</strong>: um dia AR$ 50.000; dez dias AR$ 140.000; temporada AR$ 280.000. Residentes de países limítrofes: dez dias AR$ 50.000.',
		'<strong>Residentes na Argentina</strong>: um dia AR$ 12.500; dez dias AR$ 25.000; temporada AR$ 50.000; de 13 a 17 anos, temporada AR$ 25.000. Sem custo para maiores de 65, menores de até 12 anos e pessoas com deficiência.',
		'<strong>Corrico (trolling)</strong> (licença adicional à ordinária): AR$ 25.000 por dia, AR$ 50.000 por dez dias, AR$ 100.000 pela temporada, segundo o Perfil sobre a decisão da Comissão Consultiva. O portal de Río Negro lista a licença, mas os valores não apareceram no que lemos.',
	] ),
	pm_bp_p( 'Segundo o anexo de Río Negro do regulamento 2026/27, a licença patagônica de pesca vale em Río Negro, Neuquén, Chubut e nos Parques Nacionais, desde que sua autenticidade possa ser conferida no local (o anexo de Neuquén diz o mesmo para a licença online, com seu código QR e o seu documento). Ou seja, uma licença ordinária cobre o lago, os rios dentro do Parque Nacional e as águas provinciais. Desde 2026/27 Río Negro vende também uma licença social separada para o rio Negro e outras águas longe daqui: ela não vale no resto da Patagônia. Leve o documento ou o passaporte e a licença.' ),
	pm_bp_h( 'Regras que lemos: Parque Nacional Nahuel Huapi, 2026/27' ),
	pm_bp_p( 'Estas são as fichas do anexo 2026/27 dos Parques Nacionais, lidas numa cópia hospedada por terceiros porque o site oficial não respondeu. As águas que não constam no anexo não estão abertas à pesca. Pergunte sempre ao seu guia quais regras valem no dia.' ),
	pm_bp_lista( [
		'<strong>Lago Nahuel Huapi</strong>: aberto de 1º de novembro de 2026 a 1º de maio de 2027, com limite diário de dois salmonídeos. De 2 a 31 de maio de 2027 (e em outubro de 2027) o limite é um salmonídeo de menos de 40 cm, e é proibido pescar a menos de 200 m das bocas de rios e arroios. Até 31 de dezembro de 2026 não se pesca no oeste do braço Campanario. É proibido navegar a motor a menos de 300 m dos paredões da ilha Victoria e da ilha Fray Menéndez, e há proibições de desembarque em partes dos braços Blest e Tristeza e em algumas ilhas.',
		'<strong>Rio Manso</strong>: o Manso superior (nascentes ao lago Mascardi) não se pesca. O Manso médio (Mascardi ao lago Hess e salto 4 ao lago Steffen) é de devolução obrigatória; a pesca embarcada sem motor é permitida de 1º de novembro de 2026 a 30 de abril de 2027, e entre o lago Hess e o salto 4 a pesca e a navegação são proibidas. O Manso inferior (lago Steffen à confluência com o Villegas) é só mosca, com limite de dois salmonídeos, e barcos sem motor são permitidos de 1º de novembro de 2026 a 31 de março de 2027. Alerta de Didymo: trate o seu equipamento.',
		'<strong>Rio Traful</strong>: a navegação é proibida em qualquer forma; só mosca e devolução obrigatória; o único acesso público é pela confluência com o rio Minero. <strong>Lago Traful</strong>: uma truta arco-íris de menos de 40 cm por dia em novembro (anzol sem farpa), duas entre 1º de dezembro de 2026 e 31 de março de 2027; sem pesca a menos de 100 m do Bosque Sumergido.',
		'<strong>Limay superior</strong> (dentro do Parque Nacional): de 1º de novembro de 2026 a 31 de maio de 2027; barcos sem motor permitidos, exceto nos primeiros 400 m abaixo do lago; do lago até Rincón Chico é só mosca e devolução obrigatória; o limite é um salmonídeo e desde 1º de abril de 2027 todos os peixes são devolvidos. O anexo lista também os horários de pesca permitidos por mês.',
		'<strong>Lago Mascardi</strong>: de 1º de novembro de 2026 a 1º de maio de 2027, dois salmonídeos. <strong>Lago Steffen</strong>: um salmonídeo; proibidos barcos com motores de mais de 50 HP.',
		'<strong>Solas de feltro são proibidas</strong> nas águas do Parque Nacional que lemos e em todas as águas de jurisdição de Río Negro (anexo de Río Negro). Desinfete o equipamento contra a alga Didymo.',
	] ),
	pm_bp_h( 'O que ainda não lemos' ),
	pm_bp_p( '<strong>Não lemos</strong> a lista de ambientes de jurisdição de Río Negro (por exemplo o Limay a jusante, o Ñirihuau e o Pichileufu), então não publicamos regras para eles: pergunte à Mesa Directiva (+54 294 442-1515) ou ao seu guia. Abaixo das barragens, o anexo de Neuquén divide o Limay em zonas, da barragem de Piedra del Águila até o paraje Álamo Guacho, com trechos só de mosca e regras de devolução; isso está no anexo de Neuquén. Nas águas de Neuquén é proibido pescar a menos de 500 m das barragens e de pontes e passarelas, e contratar um guia que não seja prestador turístico habilitado pela província é uma infração. O anexo de Río Negro diz também que a pesca é só de dia e que transportar peixes vivos é proibido sem autorização.' ),
	pm_bp_h( 'Como chegar e quando ir' ),
	pm_bp_p( 'Bariloche tem aeroporto próprio; o Tipiliuke Lodge descreve o voo desde Buenos Aires como de cerca de duas horas. O Rio Manso Lodge diz que inclui os traslados desde a região de Bariloche e que o lodge fica a 1,5 a 2 horas de estrada do aeroporto regional mais próximo. São os tempos informados pelos próprios operadores. A temporada começa em 1º de novembro de 2026; o lago Nahuel Huapi fecha em 1º de maio de 2027 e o Limay dentro do Parque em 31 de maio de 2027.' ),
	pm_bp_pendiente( 'Ambientes de Río Negro (Limay a jusante, Ñirihuau, Pichileufu); o texto geral do regulamento 2026/27 (artigos 4 e 22); calendário por mês com fonte técnica; níveis dos rios; lista de guias.' ),
	pm_bp_h( 'Lodges e operadores' ),
	pm_bp_p( 'Abaixo estão os que encontramos com informação pública; ainda não entramos em contato com eles, por isso estão marcados. Uma tarifa só aparece quando o operador a publica com a sua temporada.' ),
] );

$fuentes = array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => '20261030' ], [
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina (Bariloche): valores 2026/2027 e venda online de licenças', $u['rn'], 1 ],
	[ 'Regulamento de Pesca Esportiva Continental da Patagônia (site oficial): anexo de Río Negro, pontos gerais, lido em 30/10/2026', $u['anrn'], 1 ],
	[ 'Regulamento de Pesca Esportiva Continental da Patagônia (site oficial): anexo de Neuquén, pontos gerais, lido em 30/10/2026', $u['annc'], 1 ],
	[ 'Regulamento 2026/27 (site oficial): lista de ambientes dos Parques Nacionais do sul; lemos as fichas numa cópia hospedada pela AICACYP (terceiro), porque nosso leitor não abre o site oficial', $u['annp'], 1 ],
	[ 'Regulamento 2026/27 (site oficial): lista de ambientes de Neuquén (Limay abaixo das barragens); lido na mesma cópia da AICACYP', $u['annn'], 1 ],
	[ 'Perfil Weekend: temporada 2026-2027, licenças de corrico e mudanças decididas pela Comissão Consultiva', $u['perf'], 0 ],
	[ 'Rio Manso Lodge: tarifas e detalhes 2026-27 (operador)', $u['rml'], 0 ],
	[ 'Rio Manso Lodge: perguntas frequentes (operador)', $u['rmf'], 0 ],
	[ 'Limay River Lodge: quem somos (operador)', $u['lrl'], 0 ],
	[ 'Tipiliuke Lodge: localização, com o voo de Buenos Aires a Bariloche (operador)', $u['tloc'], 0 ],
] );

$g = [
	'titulo' => 'Bariloche e o Nahuel Huapi', 'slug' => 'pesca-em-bariloche',
	'campos' => [
		'h1'          => 'Pesca em Bariloche e no Nahuel Huapi: lago, rios, regras e licença 2026/27',
		'bajada'      => 'O lago Nahuel Huapi e os rios Manso, Traful e Limay do Parque Nacional. As regras 2026/27 que lemos, a licença de Río Negro e o que ainda falta ler.',
		'modalidades' => "Mosca (flycast)\nSpinning\nPesca embarcada sem motor\nCaptura e devolução",
		'epoca'       => 'De 1º de novembro de 2026 a 1º de maio de 2027 no lago; o Limay dentro do Parque até 31 de maio de 2027.',
		'seo_title'   => 'Pesca em Bariloche e Nahuel Huapi: regras e licença',
		'seo_description' => 'Guia de pesca de Bariloche e Nahuel Huapi: regras 2026/27 do lago e dos rios Manso, Traful e Limay, valores da licença de Río Negro e lodges.',
		'temporada'   => [
			[ 'meses' => 'Novembro', 'que' => 'Abre a temporada 2026/27 (1º de novembro de 2026).' ],
			[ 'meses' => 'Até 1º de maio', 'que' => 'O lago Nahuel Huapi e o lago Mascardi fecham em 1º de maio de 2027; o Limay dentro do Parque fecha em 31 de maio de 2027.' ],
			[ 'meses' => 'Calendário por mês', 'que' => 'PENDIENTE: eclosões, níveis dos rios e melhores meses, com fonte técnica.' ],
		],
		'reglamento_resumen' => 'Regulamento Geral de Pesca Esportiva Continental da Patagônia, temporada 2026/27. Anexos dos Parques Nacionais e de Neuquén lidos em 30/10/2026 em cópias hospedadas por terceiros (AICACYP); pontos gerais do anexo de Río Negro copiados do site do regulamento pelo proprietário no mesmo dia. Valores da licença do portal da Mesa Directiva Zona Andina. A lista de ambientes de Río Negro e o texto geral (artigos 4 e 22) não foram lidos.',
		'aguas' => [
			[ 'nombre' => 'Lago Nahuel Huapi', 'nota' => 'Dois salmonídeos por dia; sem pesca a menos de 200 m das bocas de rios de 2 a 31 de maio.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Manso', 'nota' => 'Superior fechado; médio com devolução obrigatória; inferior só mosca.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Traful', 'nota' => 'Só mosca, devolução obrigatória, sem navegação; acesso público pelo rio Minero.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Limay (trecho do Parque)', 'nota' => 'Só mosca e devolução até Rincón Chico; sem motor.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Qual licença preciso para pescar em Bariloche e quanto custa?', 'respuesta' => 'A licença patagônica de Río Negro, comprada online no portal da Mesa Directiva Zona Andina. Para estrangeiros não residentes em 2026/27: um dia AR$ 50.000, dez dias AR$ 140.000, temporada AR$ 280.000 (pesos argentinos, impostos incluídos). Confirme o valor vigente antes de pagar.' ],
			[ 'pregunta' => 'Preciso de uma licença separada para o Parque Nacional?', 'respuesta' => 'Segundo o anexo de Río Negro do regulamento 2026/27, a licença patagônica vale em Río Negro, Neuquén, Chubut e nos Parques Nacionais. Não lemos uma licença separada do Parque para o lago e os rios: confirme ao comprar.' ],
			[ 'pregunta' => 'Quando começa a temporada?', 'respuesta' => 'Em 1º de novembro de 2026. O lago Nahuel Huapi e o lago Mascardi fecham em 1º de maio de 2027, e o Limay dentro do Parque em 31 de maio de 2027.' ],
			[ 'pregunta' => 'Quantos peixes posso levar do lago Nahuel Huapi?', 'respuesta' => 'Até 1º de maio de 2027, dois salmonídeos por dia. De 2 a 31 de maio de 2027, um salmonídeo de menos de 40 cm. Os guias locais costumam devolver tudo.' ],
			[ 'pregunta' => 'Quais são as regras do rio Manso?', 'respuesta' => 'O Manso superior não se pesca. O Manso médio é de devolução obrigatória, e a pesca embarcada sem motor é permitida de 1º de novembro de 2026 a 30 de abril de 2027. O Manso inferior, do lago Steffen ao Villegas, é só mosca com limite de dois salmonídeos.' ],
			[ 'pregunta' => 'Como se pesca o rio Traful?', 'respuesta' => 'Só mosca e devolução obrigatória, sem navegação. O único acesso público é pela confluência com o rio Minero.' ],
			[ 'pregunta' => 'Posso usar botas com sola de feltro?', 'respuesta' => 'Não. São proibidas nas águas do Parque Nacional que lemos e em todas as águas de jurisdição de Río Negro. Desinfete o equipamento contra a alga Didymo.' ],
			[ 'pregunta' => 'Quais são as regras dos rios de Río Negro fora do Parque?', 'respuesta' => 'Ainda não lemos a lista de ambientes de Río Negro (o Limay a jusante, o Ñirihuau, o Pichileufu), então não publicamos regras para eles. Pergunte à Mesa Directiva pelo +54 294 442-1515, ou ao seu guia.' ],
			[ 'pregunta' => 'Quanto custa uma estadia num lodge?', 'respuesta' => 'O Rio Manso Lodge publica para 2026-27 US$ 1.050 por pescador e noite ou US$ 7.000 por semana, por pessoa em quarto duplo (site próprio, lido em 30/10/2026). Os outros operadores não publicam tarifa ou não conseguimos ler.' ],
			[ 'pregunta' => 'Como chego a Bariloche?', 'respuesta' => 'Bariloche tem aeroporto próprio; segundo o Tipiliuke Lodge, o voo desde Buenos Aires leva cerca de duas horas. O Rio Manso Lodge inclui os traslados desde a região de Bariloche.' ],
		],
	],
];
$comun = [ 'clave' => 'bariloche', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'reglamento_autoridad' => 'Mesa Directiva de Pesca Deportiva Zona Andina (Río Negro) e Administración de Parques Nacionales', 'reglamento_url' => $u['rn'], 'reglamento_verificado' => '20261030' ];

$t = mb_strlen( $g['campos']['seo_title'] );
$d = mb_strlen( $g['campos']['seo_description'] );
( $t <= 60 && $d >= 70 && $d <= 160 ) || WP_CLI::error( "SEO fuera de rango: title $t, description $d" );
pm_buscar( 'pm_destino', 'bariloche', 'es' ) || WP_CLI::error( 'no existe la guía bariloche (es), fuente de los datos compartidos.' );
if ( pm_buscar( 'pm_destino', 'bariloche', 'pt' ) ) {
	WP_CLI::log( 'bariloche (pt) ya existe: no toco nada.' );
	return;
}
WP_CLI::log( 'bariloche (pt): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas, ' . count( $g['campos']['aguas'] ) . ' aguas y ' . count( $fuentes ) . " fuentes (title $t, desc $d)" );
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
$ok = count( pm_filas( 'faqs', $id ) ) === 10 && count( pm_filas( 'fuentes', $id ) ) === 10 && count( pm_filas( 'temporada', $id ) ) === 3 && 'patagonia' === $dd['region'] && $dd['foto'];
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": bariloche (pt) #$id " . get_permalink( $id ) . " (región {$dd['region']}, foto #{$dd['foto']} heredadas de la española)" );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
