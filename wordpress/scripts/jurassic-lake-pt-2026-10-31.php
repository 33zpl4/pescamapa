<?php
/**
 * Portuguese (Brazil) version of the Jurassic Lake / Lago Strobel guide (clave jurassic-lake), 31/10/2026: owner's rule of
 * completing languages. Translated from the LIVE English guide (dumped by volcar-guias-en-2026-10-31), which no longer carries the
 * 200 m / 100 m exclusion lines removed on 30/10. Same limits: no prices, Moro Creek not in Anexo I, calendar pending.
 * Shared data (region, coordinates, species, cover photo) stays on the Spanish guide. Idempotent. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = jurassic-lake-pt-2026-10-31).
 */

$aplicar = false;
$hoy     = '20261031';

function pm_jp_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_jp_h( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_jp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_jp_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$cuerpo = implode( "\n\n", [
	pm_jp_p( '<strong>Jurassic Lake</strong> é o nome que os pescadores dão ao <strong>lago Strobel</strong>, um lago remoto no centro de Santa Cruz, Patagônia, conhecido por trutas arco-íris muito grandes. O nome é marca registrada de um lodge, não o nome do lago: o lago é o Strobel e seu principal afluente é o <strong>rio Barrancoso</strong>. Esta página reúne as regras, a licença e como se chega. É um guia para planejar: confirme tudo com o seu operador e com a autoridade provincial antes de viajar.' ),
	pm_jp_h( 'Regras no lago Strobel e no rio Barrancoso' ),
	pm_jp_p( 'O regulamento 2026/27 é a <strong>Disposición 241/2026</strong> da Secretaria de Estado de Pesca e Aquicultura de Santa Cruz (assinada em 22 de setembro de 2026, em vigor desde 1º de outubro de 2026). Seu Anexo I lista cada água. Para estas duas:' ),
	pm_jp_lista( [
		'<strong>Lago Strobel</strong>: aberto o ano todo, sem veda. <strong>Só captura e devolução.</strong> Anzol simples, com ou sem farpa. <strong>Motores de combustão interna ou qualquer propulsão mecânica são proibidos</strong> para navegar no lago. Desinfete e lave o equipamento no fim de cada dia (Didymo).',
		'<strong>Rio Barrancoso</strong>: aberto de <strong>1º de outubro de 2026 a 3 de maio de 2027</strong>. Captura e devolução absoluta. Anzol simples, com ou sem farpa. É classificado como zona de máxima vulnerabilidade à alga Didymo: é obrigatório levar o <strong>certificado diário de desinfecção</strong> e lavar o equipamento no fim do dia.',
		'Não há limite de captura nem tamanho mínimo listados para nenhuma das duas águas: com devolução obrigatória, não se pode reter nem transportar nenhum peixe (artigo 12).',
	] ),
	pm_jp_p( 'O arroio Moro, que os lodges mencionam, não aparece no Anexo I que lemos; às águas não listadas aplica-se o regulamento geral. O Anexo diz que a lista da web mostra só algumas águas, então pergunte à autoridade ou ao seu operador.' ),
	pm_jp_h( 'Regras gerais que valem aqui também' ),
	pm_jp_lista( [
		'<strong>Uma isca artificial com um anzol</strong> (artigo 8) e <strong>uma vara em uso por vez</strong> (artigo 10). Onde a devolução é obrigatória, use anzol sem farpa ou com a farpa amassada e devolva o peixe vivo (artigo 11).',
		'<strong>Sem chumbo</strong>: pesos, chumbadas e iscas com cabeça ou núcleo de chumbo são proibidos (artigo 15 r).',
		'<strong>Só de dia</strong>: pesca do nascer ao pôr do sol, sem pesca noturna (artigos 6 e 15 t).',
		'<strong>É proibido pescar de barco a menos de 200 m das bocas e nascentes de rios nos lagos</strong> (artigo 15 a).',
		'<strong>Didymo</strong>: waders, botas, equipamento, flutuadores e veículos devem ser desinfetados; solas de feltro só são permitidas seguindo o protocolo (artigos 17 e 18).',
		'Os fiscais podem usar câmeras corporais e drones para documentar as fiscalizações (artigo 1). Não use drone para localizar peixes (Anexo II, práticas de baixo impacto).',
	] ),
	pm_jp_p( 'O regulamento observa que é melhor não vadear em cascalho fino de rios e arroios até 1º de janeiro de 2027, para proteger os ninhos de desova. É uma recomendação, não uma regra.' ),
	pm_jp_h( 'A licença e quanto custa' ),
	pm_jp_p( 'A licença provincial é <strong>pessoal, nominal e intransferível</strong>. Ela é tramitada pelo SIPESQ (o sistema de coordenação de pesca da província) e levada, em papel ou digital, junto com o documento. Vale só dentro de Santa Cruz. Valores para 2026/27, em pesos argentinos, publicados pelo ministério provincial em 1º de outubro de 2026:' ),
	pm_jp_lista( [
		'<strong>Estrangeiros não residentes</strong>: dia AR$ 90.000; semana AR$ 300.000; temporada completa AR$ 450.000.',
		'<strong>Corrico (trolling)</strong>: licença adicional à ordinária: AR$ 20.000 por dia, AR$ 30.000 por semana, AR$ 40.000 por temporada.',
		'<strong>Residentes na Argentina</strong>: dia AR$ 15.000; fim de semana AR$ 20.000; semana AR$ 25.000; temporada AR$ 40.000.',
	] ),
	pm_jp_p( 'A licença de temporada vale até 30 de setembro de 2027. Os valores são atualizados pela autoridade: confirme o valor vigente no SIPESQ antes de pagar, e confira o câmbio do dia. O corrico é uma modalidade de pesca: se ele é permitido no Strobel com a proibição de motores em vigor, o Anexo não diz; pergunte ao seu operador.' ),
	pm_jp_h( 'Como chegar' ),
	pm_jp_p( 'A partir de <strong>Gobernador Gregores</strong>, o guia turístico Welcome Argentina descreve a rota provincial 25 para o norte, virando para o sul no povoado de Las Horquetas (115 km de Gregores) e depois cruzando a Meseta del Viento; diz que só com veículos 4x4. Não verificamos a estrada nem o seu estado. A maioria dos pescadores visitantes não dirige esse trecho por conta própria: os lodges organizam o traslado.' ),
	pm_jp_lista( [
		'O <strong>Jurassic Lake Lodge</strong> diz que opera um voo fretado desde El Calafate todo sábado de manhã (cerca de uma hora até sua pista privada), com um traslado terrestre desde El Calafate de cerca de seis horas incluído nos pacotes.',
		'A <strong>Estancia Laguna Verde</strong>, por meio da agência Tight Loop Travel, descreve um trajeto cênico de cerca de cinco horas desde El Calafate.',
	] ),
	pm_jp_p( 'Os tempos são os informados pelos próprios operadores e variam conforme o operador e a estrada; não planeje uma conexão apertada.' ),
	pm_jp_h( 'Quando ir' ),
	pm_jp_p( 'A temporada geral de pesca vai de 1º de outubro de 2026 a 30 de setembro de 2027. Segundo o Jurassic Lake Lodge, o início da temporada (outubro ao começo de janeiro) é quando as trutas grandes sobem o Barrancoso e o arroio Moro, e o meio da temporada (meados de janeiro a meados de março) é pesca de visada ao longo da margem do lago. Essa é a descrição do próprio lodge, não um calendário oficial. Lembre que o Barrancoso fecha em 3 de maio de 2027.' ),
	pm_jp_pendiente( 'vento e clima por mês, com fonte; nota de equipamento para trutas pesadas e vento; estado da estrada pelo lado de Gobernador Gregores.' ),
	pm_jp_h( 'Lodges e operadores' ),
	pm_jp_p( 'Várias estâncias e lodges ao redor do lago recebem pescadores. Abaixo estão os que encontramos com informação pública; ainda não entramos em contato, por isso estão marcados. Os pacotes costumam ser vendidos por semana ou por várias noites, por pessoa, em dólares; não mostramos preços porque não confirmamos nenhuma tarifa vigente com um operador.' ),
] );

$fuentes = array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => '20261030' ], [
	[ 'Ministério da Produção de Santa Cruz (MINPRO): abertura da temporada 2026/27, valores das licenças (01/10/2026)', 'https://minpro.santacruz.gob.ar/2026/10/01/santa-cruz-abrio-la-temporada-de-pesca-deportiva-2026-2027/', 1 ],
	[ 'Portal de pesca esportiva de Santa Cruz: Regulamento Geral 2026/27 (Disposición 241/26) e Anexo I, ambientes', 'https://pesca.santacruz.gob.ar/reglamento/', 1 ],
	[ 'Portal de pesca esportiva de Santa Cruz: lista de ambientes (Anexo I)', 'https://pesca.santacruz.gob.ar/ambientes/', 1 ],
	[ 'Cópia do regulamento com anexos hospedada por pesca.clubnautico.org (terceiro: o portal oficial estava fora do ar em 30/10/2026)', 'https://pesca.clubnautico.org/reglamento-santa-cruz-2026-2027.pdf', 0 ],
	[ 'La Voz de Santa Cruz: a província abriu a temporada de pesca esportiva 2026/27 (01/10/2026)', 'https://lavozdesantacruz.com.ar/2026/10/01/la-provincia-abrio-la-temporada-de-pesca-deportiva-2026-2027/', 0 ],
	[ 'Welcome Argentina: Gobernador Gregores e o vale do rio Chico (acesso por estrada)', 'https://www.welcomeargentina.com/gobernadorgregores/valle-rio-chico.html', 0 ],
	[ 'Jurassic Lake Lodge: localização e como chegar (operador)', 'https://www.jurassiclake.com/location', 0 ],
	[ 'Estancia Laguna Verde (operador)', 'https://estancialagunaverde.com/', 0 ],
	[ 'Tight Loop Travel: Estancia Laguna Verde (agência)', 'https://www.tightlooptravel.com/estancia-laguna-verde.html', 0 ],
] );

$g = [
	'titulo' => 'Jurassic Lake (Lago Strobel)', 'slug' => 'pesca-no-jurassic-lake',
	'campos' => [
		'h1'          => 'Pesca no Jurassic Lake (Lago Strobel), Santa Cruz: regras, licença e como chegar',
		'bajada'      => 'Jurassic Lake é o nome que os pescadores usam para o lago Strobel, no centro de Santa Cruz, Patagônia. As regras 2026/27, a licença para não residentes e como se chega.',
		'modalidades' => "Mosca (flycast)\nSpinning e baitcasting\nCaptura e devolução",
		'epoca'       => 'O lago Strobel está aberto o ano todo; o rio Barrancoso de 1º/10/2026 a 3/5/2027.',
		'seo_title'   => 'Guia de pesca no Jurassic Lake: regras e licença do Strobel',
		'seo_description' => 'Guia de pesca no Jurassic Lake (lago Strobel), Santa Cruz: captura e devolução, regras do Anexo I 2026/27, licença para não residentes com valores e como chegar.',
		'temporada'   => [
			[ 'meses' => 'Lago Strobel', 'que' => 'Aberto o ano todo. Só captura e devolução.' ],
			[ 'meses' => 'Rio Barrancoso', 'que' => 'De 1º de outubro de 2026 a 3 de maio de 2027. Captura e devolução absoluta.' ],
			[ 'meses' => 'Temporada geral', 'que' => 'De 1º de outubro de 2026 a 30 de setembro de 2027 (Disposición 241/26).' ],
			[ 'meses' => 'Calendário por mês', 'que' => 'PENDIENTE: vento, clima e melhores meses, com fonte.' ],
		],
		'reglamento_resumen' => 'Disposición 241/2026 (22/09/2026, em vigor desde 1/10/2026), Regulamento Geral de Pesca Esportiva Continental de Santa Cruz, temporada 2026/27. Strobel: aberto o ano todo, captura e devolução, anzol simples com ou sem farpa, sem motores. Barrancoso: 1/10/2026 a 3/5/2027, devolução absoluta, certificado diário de desinfecção. Licença pessoal pelo SIPESQ. Lido em 30/10/2026 em duas cópias do regulamento; as distâncias de exclusão que aparecem em só uma delas não são publicadas.',
		'aguas' => [
			[ 'nombre' => 'Lago Strobel', 'nota' => 'Só captura e devolução; sem motores. Confirme os setores abertos com o seu operador.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Barrancoso', 'nota' => 'O principal afluente do lago. De 1º/10/2026 a 3/5/2027, captura e devolução absoluta.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Jurassic Lake é o nome verdadeiro do lago?', 'respuesta' => 'Não. O lago se chama Strobel. "Jurassic Lake" é a marca registrada de um lodge, tão conhecida que muita gente acha que é o nome do lago.' ],
			[ 'pregunta' => 'Posso levar os peixes que pescar?', 'respuesta' => 'Não. O lago Strobel e o rio Barrancoso são só de captura e devolução, e nessas águas não se pode reter nem transportar nenhum peixe (Anexo I e artigo 12 da Disposición 241/26).' ],
			[ 'pregunta' => 'Qual licença preciso e quanto custa?', 'respuesta' => 'A licença provincial de Santa Cruz, pessoal e intransferível, pelo SIPESQ. Para estrangeiros não residentes em 2026/27: dia AR$ 90.000, semana AR$ 300.000, temporada AR$ 450.000 (pesos argentinos, publicados pelo ministério provincial em 1º de outubro de 2026). Confirme o valor vigente antes de pagar.' ],
			[ 'pregunta' => 'Que anzol e equipamento posso usar?', 'respuesta' => 'Uma isca artificial com um anzol simples, com ou sem farpa (a farpa amassada é a escolha segura), uma vara por vez e sem chumbo (artigos 8, 10 e 15 r, e Anexo I).' ],
			[ 'pregunta' => 'Posso pescar de barco a motor?', 'respuesta' => 'No lago Strobel, não: o Anexo I proíbe de forma absoluta navegar com embarcações de combustão interna ou propulsão mecânica. Além disso, é proibido pescar de barco a menos de 200 m das nascentes e bocas de rios (artigo 15 a).' ],
			[ 'pregunta' => 'Posso pescar perto da boca do Barrancoso?', 'respuesta' => 'Sim. Os lodges descrevem seus trechos como vizinhos à boca. O regulamento exige devolver todo peixe a menos de 200 m das bocas e nascentes de rios (artigo 11) e proíbe pescar de barco a menos de 200 m das bocas de rios nos lagos (artigo 15 a). O Anexo I pode fixar também distâncias mínimas ao redor das bocas do Strobel e do Barrancoso; as cópias que comparamos divergem nesse ponto, então pergunte ao seu operador quais setores estão abertos nas suas datas e que distância respeitam.' ],
			[ 'pregunta' => 'Quando está aberto?', 'respuesta' => 'Lago Strobel: o ano todo. Rio Barrancoso: de 1º de outubro de 2026 a 3 de maio de 2027. A temporada geral vai até 30 de setembro de 2027.' ],
			[ 'pregunta' => 'Como chego lá?', 'respuesta' => 'Segundo o Jurassic Lake Lodge, por um voo fretado de uma hora desde El Calafate aos sábados ou por um traslado terrestre de cerca de seis horas; a Estancia Laguna Verde descreve cerca de cinco horas de estrada. A partir de Gobernador Gregores o acesso é pela rota provincial 25, só 4x4, e não verificamos a estrada.' ],
			[ 'pregunta' => 'O que é o certificado de desinfecção?', 'respuesta' => 'No rio Barrancoso, zona de máxima vulnerabilidade à alga Didymo, o Anexo I exige levar o certificado diário de desinfecção e lavar o equipamento no fim do dia. Pergunte ao seu lodge como ele é obtido.' ],
			[ 'pregunta' => 'Quanto custa uma viagem?', 'respuesta' => 'Os lodges vendem pacotes de várias noites, por pessoa, em dólares. Não temos nenhuma tarifa vigente confirmada com um operador, então não publicamos nenhuma.' ],
		],
	],
];
$comun = [ 'clave' => 'jurassic-lake', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'reglamento_autoridad' => 'Secretaría de Estado de Pesca y Acuicultura de Santa Cruz', 'reglamento_url' => 'https://pesca.santacruz.gob.ar/reglamento/', 'reglamento_verificado' => '20261030' ];

$t = mb_strlen( $g['campos']['seo_title'] );
$d = mb_strlen( $g['campos']['seo_description'] );
( $t <= 60 && $d >= 70 && $d <= 160 ) || WP_CLI::error( "SEO fuera de rango: title $t, description $d" );
pm_buscar( 'pm_destino', 'jurassic-lake', 'es' ) || WP_CLI::error( 'no existe la guía jurassic-lake (es).' );
if ( pm_buscar( 'pm_destino', 'jurassic-lake', 'pt' ) ) {
	WP_CLI::log( 'jurassic-lake (pt) ya existe: no toco nada.' );
	return;
}
WP_CLI::log( 'jurassic-lake (pt): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas, ' . count( $g['campos']['aguas'] ) . ' aguas y ' . count( $fuentes ) . " fuentes (title $t, desc $d)" );
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
$ok = count( pm_filas( 'faqs', $id ) ) === 10 && count( pm_filas( 'fuentes', $id ) ) === 9 && count( pm_filas( 'temporada', $id ) ) === 4 && 'patagonia' === $dd['region'] && $dd['foto'];
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": jurassic-lake (pt) #$id " . get_permalink( $id ) . " (región {$dd['region']}, foto #{$dd['foto']} heredadas)" );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
