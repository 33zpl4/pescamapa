<?php
/**
 * Portuguese (Brazil) version of the San Martín and Junín de los Andes guide (clave san-martin-junin), 31/10/2026: owner's rule of
 * completing languages. Translated from the LIVE English guide (volcar-guias-en-2026-10-31): 2026/27 rules by river, permit,
 * preferential zone, validity across Patagonia, no prices. Shared data stays on the Spanish guide. Idempotent. Dry run first
 * ($aplicar = false); run with wp-script.yml (script = san-martin-junin-pt-2026-10-31).
 */

$aplicar = false;
$hoy     = '20261031';

function pm_sp_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_sp_h( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_sp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_sp_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$cuerpo = implode( "\n\n", [
	pm_sp_p( '<strong>Junín de los Andes</strong> e <strong>San Martín de los Andes</strong>, no sudoeste de Neuquén, são a base histórica da pesca com mosca no norte da Patagônia. Três rios correm perto: o <strong>Chimehuín</strong>, o <strong>Malleo</strong> e o <strong>Quilquihue</strong>, com o Collón Curá e o Aluminé um pouco mais longe. Esta página reúne as regras 2026/27, a licença e como se chega. É um guia para planejar: confirme tudo com o seu operador e com a autoridade provincial antes de viajar.' ),
	pm_sp_h( 'A licença e quanto custa' ),
	pm_sp_p( 'As licenças de pesca da temporada 2026/27 são vendidas online no portal de caça e pesca de Neuquén. A temporada começa em <strong>1º de novembro de 2026</strong> e dura um ano, embora algumas águas fechem em 1º de maio de 2027. É preciso o documento, ou o passaporte se você é estrangeiro não residente. Valores anunciados pela Direção Provincial de Fauna em 16 de outubro de 2026, em pesos argentinos:' ),
	pm_sp_lista( [
		'<strong>Estrangeiros não residentes</strong>: um dia AR$ 50.000; dez dias AR$ 140.000; temporada completa AR$ 280.000. Residentes de países limítrofes: dez dias AR$ 50.000.',
		'<strong>Residentes na Argentina</strong>: um dia AR$ 12.500; dez dias AR$ 25.000; temporada AR$ 50.000.',
		'<strong>Corrico (trolling)</strong> (licença adicional à ordinária): AR$ 25.000 por dia, AR$ 50.000 por dez dias, AR$ 100.000 pela temporada.',
		'<strong>Zona preferencial</strong> (Zona I do Chimehuín e todo o Correntoso): adicional de AR$ 12.500 por dia, AR$ 25.000 por dez dias ou AR$ 50.000 pela temporada, conforme anunciado em 16 de outubro de 2026. O texto do anexo no site do regulamento lista AR$ 10.000, AR$ 20.000 e AR$ 40.000 para as mesmas licenças, o que pode ser da temporada anterior: confirme ao comprar.',
	] ),
	pm_sp_p( 'O portal de licenças de Río Negro mostra os mesmos números. Segundo os anexos de Neuquén e Río Negro do regulamento 2026/27, a licença patagônica de pesca vale em Río Negro, Neuquén, Chubut e nos Parques Nacionais: uma licença comprada online precisa do código QR e do seu documento, sem carimbo adicional. As licenças adicionais (corrico, zonas preferenciais) valem só onde são emitidas, um texto que vimos num trecho do regulamento geral: confirme ao comprar. O anexo de Neuquén proíbe contratar guias de pesca que não sejam prestadores turísticos habilitados pela província (é infração), então confira a lista provincial antes de reservar.' ),
	pm_sp_h( 'Regras por rio, 2026/27' ),
	pm_sp_p( 'Estas são as fichas do anexo 2026/27 de Neuquén, que lemos numa cópia hospedada por terceiros porque o site oficial não respondeu. Datas, limites e zonas podem mudar: pergunte ao seu operador quais regras valem no dia.' ),
	pm_sp_lista( [
		'<strong>Chimehuín</strong> (1º de novembro de 2026 a 31 de maio de 2027). A Zona I, da saída do lago até 250 m a jusante da ponte sinalizada, é <strong>zona preferencial com licença adicional obrigatória</strong>: só mosca, anzol sem farpa, e todo peixe deve ser devolvido. A Zona II, do fim da Zona I até a confluência com o Collón Curá: mosca, spinning e spinning com isca de peso de mosca; limite de uma perca; todos os salmonídeos devolvidos; anzol simples sem farpa.',
		'<strong>Malleo</strong> (1º de novembro de 2026 a 31 de maio de 2027). Do limite do Parque Nacional até a ponte da Rota 23: só mosca e todo peixe devolvido. Da ponte da Rota 23 até o Aluminé: mosca ou spinning leve, uma perca, todos os salmonídeos devolvidos, anzol sem farpa. A parte dentro do Parque Nacional Lanín está no anexo dos Parques Nacionais.',
		'<strong>Quilquihue</strong> (1º de novembro de 2026 a 31 de maio de 2027). Da boca até o fim da lagoa: mosca ou spinning leve, duas percas, todos os salmonídeos devolvidos, anzol sem farpa. Do fim da lagoa até o Chimehuín: mosca e spinning, anzol simples sem farpa.',
		'<strong>Rios Collón Curá e Aluminé</strong>: duas percas, todos os salmonídeos devolvidos, anzol simples sem farpa, mesma temporada.',
		'<strong>Lagos Huechulafquen e Lolog</strong> (dentro do Parque Nacional Lanín): limite de dois salmonídeos; botas de vadeio com sola de feltro são proibidas nas águas do Parque Nacional que lemos.',
	] ),
	pm_sp_p( 'Em geral, "devolução" nessas fichas significa que todo salmonídeo volta vivo, e o anzol simples sem farpa é a norma. O rio Correntoso também é zona preferencial em toda a sua extensão, com a mesma licença adicional e sem navegação. Onde o anexo não diz nada sobre solas de feltro, como nas fichas de Neuquén, não encontramos proibição, mas o anexo dos Parques Nacionais proíbe o feltro água por água: a escolha segura é sola de borracha. Desinfete sempre o equipamento contra a alga Didymo.' ),
	pm_sp_h( 'Como chegar' ),
	pm_sp_p( 'O aeroporto comercial mais próximo é <strong>Chapelco</strong>, em San Martín de los Andes; a rota habitual desde Buenos Aires é um voo de cerca de duas horas, segundo o Tipiliuke Lodge. O Tipiliuke se situa a 22 km de Junín de los Andes e a 35 km de San Martín, e a cerca de três horas de estrada de Bariloche pelo Caminho dos Sete Lagos. O último trecho até o lodge é uma estrada de cascalho de 6 km. São os tempos informados pelo operador: não planeje uma conexão apertada.' ),
	pm_sp_h( 'Quando ir' ),
	pm_sp_p( 'A temporada geral começa em 1º de novembro de 2026 e as fichas do anexo acima fecham em 31 de maio de 2027. O Tipiliuke descreve sua própria temporada de pesca como do início de novembro a maio. O ministério de turismo de Neuquén menciona eventos locais como a Fiesta Nacional de la Trucha e a Fiesta Provincial de las Marrones Migratorias; ainda não temos datas confirmadas.' ),
	pm_sp_pendiente( 'calendário por mês e por rio com fonte técnica; eclosões; níveis dos rios e onde conferi-los; a seção do Collón Curá e do Aluminé; lista de guias da província.' ),
	pm_sp_h( 'Lodges e operadores' ),
	pm_sp_p( 'Abaixo estão os que encontramos com informação pública; ainda não entramos em contato, por isso estão marcados. O Tipiliuke não publica tarifa: pede que você escreva pelo formulário de reservas. Não mostramos preços porque não confirmamos nenhum com um operador.' ),
] );

$fuentes = array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => '20261030' ], [
	[ 'ANBariloche: começa a venda das licenças de pesca 2026-2027 (16/10/2026), valores e regras anunciados pela Direção de Fauna de Neuquén', 'https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027', 0 ],
	[ 'Portal de caça e pesca de Neuquén: licenças (onde se compram; não conseguimos abri-lo)', 'https://cazaypesca.neuquen.gob.ar/permisos/', 1 ],
	[ 'Regulamento 2026/27, lista de ambientes de Neuquén (Anexo), cópia hospedada pela AICACYP (terceiro; o site oficial não respondeu em 30/10/2026)', 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Neuquen.pdf', 0 ],
	[ 'Regulamento 2026/27, lista de ambientes dos Parques Nacionais do sul (Anexo), cópia hospedada pela AICACYP (terceiro)', 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Parques-Nacionales.pdf', 0 ],
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina: valores das licenças 2026/2027 (portal de Río Negro; mesmos números)', 'https://www.permisosdepesca.com.ar/', 1 ],
	[ 'Perfil Weekend: temporada 2026-2027, valores das licenças e mudanças decididas pela Comissão Consultiva', 'https://weekend.perfil.com/noticias/pesca/temporada-de-pesca-2026-2027-en-la-patagonia-cuanto-costaran-las-licencias-y-que-cambios-habra.phtml', 0 ],
	[ 'Tipiliuke Lodge: localização e como chegar (operador)', 'https://tipiliuke.com/location', 0 ],
	[ 'Tipiliuke Lodge: pesca com mosca (operador)', 'https://tipiliuke.com/activities/fly-fishing', 0 ],
	[ 'Tipiliuke Lodge: o lodge (operador)', 'https://tipiliuke.com/the-lodge', 0 ],
	[ 'Regulamento de Pesca Esportiva Continental da Patagônia (site oficial): anexo de Neuquén, pontos gerais, lido em 30/10/2026', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/', 1 ],
	[ 'Regulamento de Pesca Esportiva Continental da Patagônia (site oficial): anexo de Río Negro, pontos gerais, lido em 30/10/2026', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-rio-negro/', 1 ],
	[ 'Regulamento 2026/27 (site oficial): lista de ambientes de Neuquén; lemos as fichas na cópia da AICACYP acima', 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-neuquen/', 1 ],
	[ 'Regulamento 2026/27 (site oficial): lista de ambientes dos Parques Nacionais do sul; lido na cópia da AICACYP acima', 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/', 1 ],
] );

$g = [
	'titulo' => 'San Martín e Junín de los Andes', 'slug' => 'pesca-com-mosca-san-martin-junin-de-los-andes',
	'campos' => [
		'h1'          => 'Pesca com mosca em San Martín e Junín de los Andes: temporada, licença e regras do Chimehuín e do Malleo',
		'bajada'      => 'Chimehuín, Malleo e Quilquihue no sudoeste de Neuquén. As regras 2026/27 por rio, a licença para não residentes e como se chega.',
		'modalidades' => "Mosca (flycast)\nSpinning leve\nDescidas em barco de deriva\nCaptura e devolução",
		'epoca'       => 'De 1º de novembro de 2026 a 31 de maio de 2027 nos rios; alguns lagos até outubro de 2027.',
		'seo_title'   => 'Pesca com mosca em San Martín e Junín de los Andes (2026/27)',
		'seo_description' => 'Guia de pesca de San Martín e Junín de los Andes, Neuquén: regras do Chimehuín, Malleo e Quilquihue, temporada 2026-2027, licença e valores de referência.',
		'temporada'   => [
			[ 'meses' => 'Novembro', 'que' => 'Abre a temporada 2026/27 (1º de novembro de 2026).' ],
			[ 'meses' => 'Até maio', 'que' => 'Os rios do anexo fecham em 31 de maio de 2027; algumas águas fecham em 1º de maio.' ],
			[ 'meses' => 'Calendário por mês', 'que' => 'PENDIENTE: eclosões, níveis dos rios e melhores meses, com fonte técnica.' ],
		],
		'reglamento_resumen' => 'Regulamento Geral de Pesca Esportiva Continental da Patagônia, temporada 2026/27. Anexos dos ambientes de Neuquén e dos Parques Nacionais lidos em 30/10/2026 em cópias hospedadas por terceiros (AICACYP); o site oficial não respondeu. Licenças em cazaypesca.neuquen.gob.ar; valores 2026/27 da Direção Provincial de Fauna (16/10/2026). A validade da licença patagônica foi lida nos anexos de Neuquén e Río Negro (textos copiados do site do regulamento pelo proprietário em 30/10/2026); as regras gerais (solas de feltro) não foram lidas no texto geral 2026/27.',
		'aguas' => [
			[ 'nombre' => 'Rio Chimehuín', 'nota' => 'A Zona I é zona preferencial com licença adicional, só mosca e devolução total. A Zona II vai até o Collón Curá.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Malleo', 'nota' => 'Só mosca e devolução total acima da ponte da Rota 23; mosca ou spinning leve abaixo.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Quilquihue', 'nota' => 'Mosca ou spinning leve; salmonídeos devolvidos.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Qual licença preciso em Junín de los Andes e quanto custa?', 'respuesta' => 'A licença de pesca de Neuquén, comprada online no portal de caça e pesca da província. Para estrangeiros não residentes em 2026/27: um dia AR$ 50.000, dez dias AR$ 140.000, temporada AR$ 280.000 (pesos argentinos, anunciados em 16 de outubro de 2026). Confirme o valor vigente antes de pagar.' ],
			[ 'pregunta' => 'Quando começa a temporada?', 'respuesta' => 'Em 1º de novembro de 2026. Os rios do anexo que lemos fecham em 31 de maio de 2027, e algumas águas fecham em 1º de maio.' ],
			[ 'pregunta' => 'É preciso uma licença adicional para o Chimehuín?', 'respuesta' => 'Para a Zona I (da saída do lago até 250 m a jusante da ponte sinalizada) e para todo o Correntoso, sim: são zonas preferenciais com licença adicional obrigatória. Os valores anunciados para 2026/27 são AR$ 12.500 por dia, AR$ 25.000 por dez dias e AR$ 50.000 pela temporada; o texto do anexo lista valores menores, então confirme ao comprar.' ],
			[ 'pregunta' => 'Posso levar os peixes que pescar?', 'respuesta' => 'Na Zona I do Chimehuín, no Malleo acima da ponte da Rota 23 e nos outros rios que lemos, todos os salmonídeos são devolvidos. Só a perca pode ser retida, dentro do limite diário de cada trecho. Confira o trecho que você vai pescar antes de ir.' ],
			[ 'pregunta' => 'Que anzol e modalidade são permitidos?', 'respuesta' => 'Um anzol simples sem farpa nos trechos que lemos. Só mosca na Zona I do Chimehuín e no Malleo acima da ponte da Rota 23; nos demais, mosca e spinning leve com isca de peso de mosca.' ],
			[ 'pregunta' => 'Uma licença de outra província patagônica vale?', 'respuesta' => 'Sim. Os anexos de Neuquén e Río Negro do regulamento 2026/27 dizem que a licença patagônica de pesca vale em Río Negro, Neuquén, Chubut e nos Parques Nacionais; uma licença comprada online precisa do código QR e do seu documento. As licenças adicionais (corrico, zonas preferenciais) valem só onde são emitidas, segundo um trecho do regulamento geral que vimos: confirme ao comprar.' ],
			[ 'pregunta' => 'Posso usar botas de vadeio com sola de feltro?', 'respuesta' => 'Não nas águas do Parque Nacional que lemos (por exemplo Huechulafquen e Lolog). Não encontramos proibição nas fichas de Neuquén, mas a sola de borracha é a escolha segura. Desinfete o equipamento contra a alga Didymo.' ],
			[ 'pregunta' => 'Como chego desde Buenos Aires?', 'respuesta' => 'O aeroporto comercial mais próximo é Chapelco, em San Martín de los Andes, a cerca de duas horas de voo de Buenos Aires segundo o Tipiliuke Lodge. De Bariloche são cerca de três horas de estrada, segundo o mesmo operador.' ],
			[ 'pregunta' => 'Quanto custa uma viagem com lodge?', 'respuesta' => 'O Tipiliuke não publica tarifa e pede que você escreva pelo formulário de reservas. Não temos nenhuma tarifa vigente confirmada com um operador, então não publicamos nenhuma.' ],
			[ 'pregunta' => 'Onde encontro guias habilitados?', 'respuesta' => 'O ministério de turismo de Neuquén publica a lista de prestadores de pesca habilitados, e contratar um guia que não está nela é infração segundo o anexo de Neuquén. Confira a lista antes de reservar.' ],
		],
	],
];
$comun = [ 'clave' => 'san-martin-junin', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'reglamento_autoridad' => 'Dirección Provincial de Fauna de Neuquén', 'reglamento_url' => 'https://cazaypesca.neuquen.gob.ar/permisos/', 'reglamento_verificado' => '20261030' ];

$t = mb_strlen( $g['campos']['seo_title'] );
$d = mb_strlen( $g['campos']['seo_description'] );
( $t <= 60 && $d >= 70 && $d <= 160 ) || WP_CLI::error( "SEO fuera de rango: title $t, description $d" );
pm_buscar( 'pm_destino', 'san-martin-junin', 'es' ) || WP_CLI::error( 'no existe la guía san-martin-junin (es).' );
if ( pm_buscar( 'pm_destino', 'san-martin-junin', 'pt' ) ) {
	WP_CLI::log( 'san-martin-junin (pt) ya existe: no toco nada.' );
	return;
}
WP_CLI::log( 'san-martin-junin (pt): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas, ' . count( $g['campos']['aguas'] ) . ' aguas y ' . count( $fuentes ) . " fuentes (title $t, desc $d)" );
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
$ok = count( pm_filas( 'faqs', $id ) ) === 10 && count( pm_filas( 'fuentes', $id ) ) === 13 && count( pm_filas( 'temporada', $id ) ) === 3 && 'patagonia' === $dd['region'] && $dd['foto'];
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": san-martin-junin (pt) #$id " . get_permalink( $id ) . " (región {$dd['region']}, foto #{$dd['foto']} heredadas)" );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
