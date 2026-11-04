<?php
/**
 * Portuguese (Brazil) version of the Esquel and Trevelin guide (clave esquel-trevelin), 4/11/2026: owner's rule of completing languages.
 * Mirrors the Spanish and English guides of the same day (same body, 10 FAQs, 4 waters, 5 season rows, 10 sources, same limits: the National
 * Parks annex was not read). Shared data (region, coordinates, species, season dates, cover photo) stays on the Spanish guide. Also adds the
 * Portuguese texts of the four outfitter cards. Idempotent. Dry run first ($aplicar = false); run with wp-script.yml
 * (script = esquel-trevelin-pt-2026-11-04).
 */

$aplicar = true;
$hoy     = '20261104';

function pm_etp_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_etp_h( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_etp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_etp_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'anx'  => 'https://reglamentodepesca.org.ar/reglamento-items/anexo-chubut/',
	'amb'  => 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-chubut/',
	'anrn' => 'https://reglamentodepesca.org.ar/reglamento-items/anexo-rio-negro/',
	'rn'   => 'https://www.permisosdepesca.com.ar/',
	'apn'  => 'https://www.argentina.gob.ar/parquesnacionales/losalerces',
	'prensa' => 'https://cholilaonline.ar/2026/10/provincia-realizara-el-acto-de-apertura-de-la-temporada-de-pesca.html',
	'pil'  => 'https://lapilaricalodge.com.ar/es/fly-fishing',
	'aho'  => 'https://www.argentinaheritagefishing.com/esquel',
	'lpl'  => 'https://www.laspampaslodge.com/',
	'chal' => 'https://challhuaquen.com/',
];
$fuentes = [
	[ 'Regulamento de Pesca Esportiva Continental Patagônico: Anexo Chubut, temporada 2026/27 (texto da Dirección de Pesca Continental, lido em 4/11/2026)', $u['anx'], 1 ],
	[ 'Regulamento de Pesca Esportiva Continental Patagônico: Ambientes Chubut, temporada 2026/27 (lido em 4/11/2026)', $u['amb'], 1 ],
	[ 'Regulamento de Pesca Esportiva Continental Patagônico: Anexo Río Negro, validade da licença patagônica (lido em 30/10/2026)', $u['anrn'], 1 ],
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina: valores 2026/2027 da licença patagônica', $u['rn'], 1 ],
	[ 'Administração de Parques Nacionais: Parque Nacional Los Alerces', $u['apn'], 1 ],
	[ 'Cholila Online: abertura da temporada 2026/27 em Esquel, Disposición 131/2026 (imprensa local)', $u['prensa'], 0 ],
	[ 'La Pilarica River & Mountain Lodge: pesca com mosca no Carrileufú (operador)', $u['pil'], 0 ],
	[ 'AHO Fishing: Esquel e Trevelin (operador)', $u['aho'], 0 ],
	[ 'Las Pampas Lodge (operador)', $u['lpl'], 0 ],
	[ 'Challhuaquen Lodge (operador)', $u['chal'], 0 ],
];
$filas = array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $fuentes );

$cuerpo = implode( "\n\n", [
	pm_etp_p( '<strong>Esquel</strong> e <strong>Trevelin</strong> são a base para pescar no <strong>Futaleufú</strong>, no <strong>Carrileufú</strong>, nos lagos <strong>Rosario</strong> e <strong>Fontana</strong> e nos rios do entorno do Parque Nacional Los Alerces, com Cholila e Río Pico mais longe. Esta página reúne a temporada e as regras 2026/27 lidas do anexo oficial de Chubut, a licença, o que os lodges publicam e o que ainda não lemos.' ),
	pm_etp_h( 'A temporada 2026/27' ),
	pm_etp_p( 'Segundo o <strong>Anexo Chubut</strong>, a temporada geral vai de <strong>1º de novembro de 2026 a 1º de maio de 2027</strong>. A captura da <strong>truta-de-riacho</strong> em rios e riachos termina em 1º de março de 2027: a partir dessa data é preciso devolvê-la viva. O anexo recomenda não vadear rios e riachos até 15 de dezembro de 2026, para proteger os leitos de desova, e esclarece que a autoridade pode mudar o cronograma diante de uma emergência hídrica ou por critérios biológicos.' ),
	pm_etp_p( 'Um esclarecimento: a imprensa local informou que a Disposición 131/2026 habilitou a temporada desde 30 de outubro, enquanto o anexo publicado diz 1º de novembro. Seguimos o anexo e dizemos isso; pergunte ao seu guia a data exata do ambiente.' ),
	pm_etp_p( 'Alguns ambientes têm outras datas: o Futaleufú, o lago Fontana e o Corcovado habilitam a pesca com devolução obrigatória desde 1º de outubro até o começo da temporada geral; o <strong>lago Rosario</strong> está habilitado o ano todo, com devolução obrigatória de todos os salmonídeos de 1º de agosto a 31 de outubro.' ),
	pm_etp_h( 'Regras gerais que lemos' ),
	pm_etp_lista( [
		'<strong>Anzóis sem farpa</strong> em todas as modalidades e todos os ambientes. São proibidas as botas de vadeio com <strong>sola de feltro</strong> em toda a província, e é preciso lavar todo o equipamento (alga Didymo).',
		'<strong>Dropper:</strong> com mosca permite-se só uma mosca seca e uma ninfa; não se permitem dois streamers nem duas ninfas.',
		'<strong>"Só mosca"</strong> permite também spinning ou baitcasting com bóia (buldo) e mosca artificial. Em <strong>"exclusivo equipamento de mosca"</strong> só se permite vara e carretilha de mosca, e ter iscas de spinning (colheres, ondulantes) na bolsa ou no colete conta como uso: infração e apreensão.',
		'Nos ambientes que não constam da lista pode-se sacrificar por dia um salmonídeo, duas percas e quatro pejerreis, salvo em rios e riachos, onde a devolução é obrigatória. Nas áreas de devolução obrigatória é proibido levar peixes.',
		'Proíbem-se o chumbo, as iscas com pilhas ou baterias, e pescar de flutuadores ou belly-boats a menos de 200 m da foz de um rio.',
		'<strong>Motores:</strong> a pesca embarcada com o motor em funcionamento só se pratica nos ambientes habilitados; nos demais, à deriva ou com o motor desligado. São proibidos os motores de dois tempos carburados nos ambientes compartilhados com Parques Nacionais e nos de alta sensibilidade.',
		'<strong>Guias:</strong> os guarda-pescas verificam se os guias e seus auxiliares estão habilitados (Resolução 072/97-OPT). Podem usar câmeras corporais e drones.',
	] ),
	pm_etp_h( 'Regras por água que lemos' ),
	pm_etp_lista( [
		'<strong>Rio Futaleufú ou Grande</strong> (Parque Nacional Los Alerces, jurisdição Chubut): todas as modalidades; pesca habilitada até 30 de junho de 2027; um salmonídeo de menos de 35 cm; uma única isca artificial com um único anzol; proíbem-se a pesca com motor em funcionamento e o corrico (trolling). É um ambiente compartilhado com Parques Nacionais.',
		'<strong>Lago Rosario:</strong> todas as modalidades, o ano todo; corrico habilitado; duas trutas e quatro pejerreis por dia; devolução obrigatória dentro de 200 m da nascente do rio Nant y Fall.',
		'<strong>Lago Fontana:</strong> todas as modalidades; corrico habilitado; três salmonídeos por dia; os riachos que chegam a ele fecham à pesca a partir de 1º de março de 2027. <strong>Arroio Fontana:</strong> só mosca e devolução obrigatória.',
		'<strong>Rio Carrileufú:</strong> exclusivo equipamento de mosca e devolução obrigatória.',
		'<strong>Lago Cholila:</strong> todas as modalidades; um salmonídeo e duas percas por dia; não se permite pescar de arrasto.',
		'<strong>Rio Percey:</strong> todas as modalidades, com anzol simples e devolução obrigatória.',
		'<strong>Rio Corcovado:</strong> pesca desde 1º de outubro com devolução obrigatória até o início da temporada geral; não navegável a motor; do lago Wintter ao arroio Las Carpas, exclusivo equipamento de mosca e devolução obrigatória.',
		'<strong>Lago Engaño:</strong> exclusivo equipamento de mosca e devolução obrigatória; os riachos tributários fecham a partir de 1º de março de 2027.',
		'<strong>Lago Puelo</strong> (Parque Nacional e Chubut): todas as modalidades; adere ao regulamento de Parques Nacionais; pesca até 31 de maio de 2027; dois salmonídeos por dia.',
	] ),
	pm_etp_h( 'A licença e quanto custa' ),
	pm_etp_p( 'Segundo o anexo de Río Negro do regulamento 2026/27, a licença patagônica vale em Río Negro, Neuquén, Chubut e Parques Nacionais. Valores 2026/27 da Mesa Directiva Zona Andina, em pesos argentinos: <strong>estrangeiros não residentes</strong>, um dia AR$ 50.000, dez dias AR$ 140.000, temporada AR$ 280.000; <strong>residentes na Argentina</strong>, um dia AR$ 12.500, dez dias AR$ 25.000, temporada AR$ 50.000. O corrico (trolling) leva uma licença adicional (AR$ 25.000 por dia, AR$ 50.000 por dez dias e AR$ 100.000 por temporada, segundo a imprensa). O Anexo Chubut esclarece que uma licença digital gerenciada pela plataforma DINO pode ser baixada de novo de graça se for perdida. Leve-a sempre com o seu documento.' ),
	pm_etp_h( 'Quando ir' ),
	pm_etp_p( 'O La Pilarica Lodge trabalha de novembro a abril e diz que janeiro e fevereiro trazem mais quantidade e tamanho de peixes, que novembro e dezembro pescam bem e com menos gente, e que março e começo de abril podem ser excepcionais para marrons. É a descrição de um operador: para escolher a semana, pergunte ao seu lodge.' ),
	pm_etp_h( 'Como chegar' ),
	pm_etp_p( 'A AHO Fishing diz que seu lodge fica a 50 minutos de carro do aeroporto de Esquel e a três horas do de Bariloche. O La Pilarica Lodge inclui os traslados do aeroporto de Esquel. O Las Pampas Lodge, em Río Pico, fica a cerca de duas horas e meia ao sul do aeroporto de Esquel, segundo o seu site. São os tempos de cada operador.' ),
	pm_etp_pendiente( 'as regras do Parque Nacional Los Alerces (lagos Futalaufquen, Verde, Menéndez e Arrayanes), que estão no anexo de Parques Nacionais e não o lemos; as regras do Lago Puelo no anexo do Parque; calendário por mês com fonte técnica; níveis do rio; foto com seu crédito; guias independentes.' ),
	pm_etp_h( 'Lodges e operadores' ),
	pm_etp_p( 'Abaixo estão os que encontramos com informação pública; ainda não os contatamos, por isso aparecem marcados. Uma tarifa só é mostrada quando o operador a publica com sua temporada, e nenhum destes a publica.' ),
] );

$g = [
	'titulo' => 'Esquel y Trevelin (Chubut)', 'slug' => 'pesca-em-esquel-e-trevelin',
	'campos' => [
		'clave' => 'esquel-trevelin', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
		'reglamento_autoridad' => 'Dirección de Pesca Continental de Chubut',
		'reglamento_url' => $u['anx'], 'reglamento_verificado' => $hoy,
		'h1'          => 'Pesca em Esquel e Trevelin, Chubut: regras e licença 2026/27',
		'bajada'      => 'O Futaleufú, o Carrileufú e os lagos Rosario e Fontana. A temporada e as regras 2026/27 lidas do anexo oficial de Chubut, a licença e o que falta ler.',
		'modalidades' => "Mosca (flycast)\nSpinning e baitcasting onde se permite\nPesca embarcada\nCaptura e devolução",
		'epoca'       => 'De 1º de novembro de 2026 a 1º de maio de 2027; alguns ambientes com outras datas.',
		'seo_title'   => 'Pesca em Esquel e Trevelin, Chubut: regras e licença',
		'seo_description' => 'Guia de pesca em Esquel e Trevelin, Chubut: temporada e regras 2026/27 do anexo oficial, rios Futaleufú e Carrileufú, lagos, licença e lodges.',
		'temporada_nota' => 'Ambientes com outras datas: o Futaleufú até 30 de junho de 2027, o lago Puelo até 31 de maio de 2027 e a truta-de-riacho em rios e riachos até 1º de março de 2027.',
		'temporada'   => [
			[ 'meses' => '1º de outubro', 'que' => 'Futaleufú, lago Fontana e Corcovado habilitam a pesca com devolução obrigatória até começar a temporada geral.' ],
			[ 'meses' => '1º de novembro', 'que' => 'Começa a temporada geral 2026/27 (a imprensa local fala de 30 de outubro).' ],
			[ 'meses' => '1º de março', 'que' => 'Termina a captura de truta-de-riacho em rios e riachos; desde então se devolve viva. Alguns riachos e rios fecham à pesca.' ],
			[ 'meses' => '1º de maio', 'que' => 'Fecha a temporada geral (2027). O Futaleufú vai até 30 de junho e o lago Puelo até 31 de maio.' ],
			[ 'meses' => 'Calendário por mês', 'que' => 'PENDIENTE: eclosões, níveis e melhores meses com fonte técnica.' ],
		],
		'reglamento_resumen' => 'Anexo Chubut e Ambientes Chubut, temporada 2026/27, lidos em 4/11/2026 no site do Regulamento de Pesca Esportiva Continental Patagônico, que reproduz o texto da Dirección de Pesca Continental. Temporada geral de 1/11/2026 a 1/5/2027, com anzóis sem farpa e sem sola de feltro. Não lemos o anexo de Parques Nacionais (Los Alerces, Lago Puelo); os valores da licença são os de Río Negro, confirme-os ao comprar.',
		'aguas' => [
			[ 'nombre' => 'Rio Futaleufú', 'nota' => 'Todas as modalidades; pesca até 30 de junho de 2027; um salmonídeo de menos de 35 cm; sem motor nem corrico.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Carrileufú', 'nota' => 'Exclusivo equipamento de mosca e devolução obrigatória.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Lago Rosario', 'nota' => 'Habilitado o ano todo; devolução obrigatória de 1º de agosto a 31 de outubro.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Lago Fontana', 'nota' => 'Todas as modalidades e corrico; três salmonídeos por dia.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Quando é a temporada de pesca em Esquel e Trevelin?', 'respuesta' => 'Segundo o Anexo Chubut, a temporada geral 2026/27 vai de 1º de novembro de 2026 a 1º de maio de 2027. A imprensa local diz que a Disposición 131/2026 a habilitou desde 30 de outubro; o anexo publicado diz 1º de novembro. Alguns ambientes têm outras datas: o Futaleufú vai até 30 de junho de 2027 e o lago Rosario está habilitado o ano todo.' ],
			[ 'pregunta' => 'Que licença preciso e quanto custa?', 'respuesta' => 'A licença patagônica, que segundo o anexo de Río Negro vale em Río Negro, Neuquén, Chubut e Parques Nacionais. Valores 2026/27 para estrangeiros não residentes: um dia AR$ 50.000, dez dias AR$ 140.000, temporada AR$ 280.000 (Mesa Directiva Zona Andina). A licença digital é gerenciada pela plataforma DINO e pode ser baixada de novo de graça se for perdida.' ],
			[ 'pregunta' => 'Posso usar botas com sola de feltro?', 'respuesta' => 'Não. São proibidas em todos os ambientes da província de Chubut, e é preciso lavar todo o equipamento por causa da alga Didymo.' ],
			[ 'pregunta' => 'Pode pescar com anzol com farpa?', 'respuesta' => 'Não. O anexo proíbe a farpa em qualquer tipo de anzol e em todas as modalidades, para favorecer a devolução dos peixes.' ],
			[ 'pregunta' => 'O que significam "só mosca" e "exclusivo equipamento de mosca"?', 'respuesta' => 'Em "só mosca" é preciso usar uma mosca artificial e permite-se spinning ou baitcasting com bóia (buldo). Em "exclusivo equipamento de mosca" só se permite vara e carretilha de mosca, e levar iscas de spinning na bolsa ou no colete já conta como uso: é infração e apreensão.' ],
			[ 'pregunta' => 'Quantos peixes posso levar?', 'respuesta' => 'Nos ambientes que não estão na lista, um salmonídeo, duas percas e quatro pejerreis por dia, salvo em rios e riachos, onde a devolução é obrigatória. Cada ambiente com regra própria tem seu limite: por exemplo, um salmonídeo de menos de 35 cm no Futaleufú e duas trutas e quatro pejerreis no lago Rosario.' ],
			[ 'pregunta' => 'Quais são as regras do rio Futaleufú?', 'respuesta' => 'No trecho do Parque Nacional Los Alerces com jurisdição de Chubut permitem-se todas as modalidades, com uma única isca artificial e um único anzol; é proibida a pesca com motor em funcionamento e o corrico; pode-se sacrificar um salmonídeo de menos de 35 cm; a pesca vai até 30 de junho de 2027. É um ambiente compartilhado com Parques Nacionais.' ],
			[ 'pregunta' => 'Onde posso pescar só com mosca?', 'respuesta' => 'O rio Carrileufú e o lago Engaño são exclusivo equipamento de mosca com devolução obrigatória; o arroio Fontana é só mosca. Confira o ambiente exato no anexo do ano.' ],
			[ 'pregunta' => 'Quais são as regras do Parque Nacional Los Alerces?', 'respuesta' => 'Ainda não lemos o anexo de Parques Nacionais, então não publicamos as regras dos lagos Futalaufquen, Verde, Menéndez nem Arrayanes. O Futaleufú dentro do Parque consta no anexo de Chubut, como ambiente compartilhado. Consulte o seu guia ou a administração do Parque.' ],
			[ 'pregunta' => 'Quanto custa uma semana em um lodge da região?', 'respuesta' => 'Não podemos dizer com uma fonte do próprio operador: La Pilarica Lodge, AHO Fishing, Las Pampas Lodge e Challhuaquen não publicam tarifas nos sites que lemos. A La Pilarica diz que sua semana inclui sete noites, todas as refeições e bebidas, seis dias de pesca guiada, traslados do aeroporto de Esquel, entradas do parque e licenças de pesca; não inclui voos, equipamento nem gorjetas.' ],
		],
	],
];
$t = mb_strlen( $g['campos']['seo_title'] );
$d = mb_strlen( $g['campos']['seo_description'] );
if ( $t > 60 || $d > 160 || $d < 70 || 10 !== count( $g['campos']['faqs'] ) ) {
	WP_CLI::error( "SEO o preguntas fuera de rango: title $t, description $d" );
}

if ( pm_buscar( 'pm_destino', 'esquel-trevelin', 'pt' ) ) {
	WP_CLI::log( 'esquel-trevelin (pt) ya existe: no toco la guía.' );
} elseif ( ! pm_buscar( 'pm_destino', 'esquel-trevelin', 'es' ) ) {
	WP_CLI::error( 'No encontré la guía esquel-trevelin (es).' );
} else {
	WP_CLI::log( 'esquel-trevelin (pt): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con 10 preguntas y " . count( $filas ) . " fuentes (title $t, description $d)" );
	if ( $aplicar ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
		foreach ( $g['campos'] + [ 'idioma' => 'pt', 'fuentes' => $filas ] as $k => $v ) {
			update_field( $k, $v, $id );
		}
		update_post_meta( $id, '_pm_traducido', time() + 1 );
		WP_CLI::log( "✓ esquel-trevelin (pt) publicada #$id: " . get_permalink( $id ) );
		WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
	}
}

$tarjetas = [
	'La Pilarica River & Mountain Lodge' => [
		'descripcion_pt' => 'Lodge de pesca com mosca em Cholila que pesca o rio Carrileufú e os rios e lagos ao redor do Parque Nacional Los Alerces, sobretudo de barco e a pé. Trabalha de novembro a abril. Segundo o seu site, a semana inclui sete noites em um de cinco quartos, todas as refeições e bebidas, seis dias de pesca guiada, traslados do aeroporto de Esquel, entradas do parque e licenças de pesca; não inclui voos, equipamento nem gorjetas. Oferece uma tarifa menor para acompanhantes que não pescam. Não publica tarifa: cota com as suas datas.',
	],
	'AHO Fishing, Esquel y Trevelin' => [
		'descripcion_pt' => 'Programa de pesca em um lodge a 50 minutos de carro do aeroporto de Esquel e a três horas do de Bariloche, segundo o seu site. Tem seis quartos duplos com banheiro privativo e serviço completo de hospedagem. O site descreve programas em Esquel e Trevelin em vários rios da região. Não publica tarifa.',
	],
	'Las Pampas Lodge' => [
		'descripcion_pt' => 'Lodge de troncos e pedra em Río Pico, a cerca de duas horas e meia ao sul do aeroporto de Esquel segundo o seu site, com quartos duplos com banheiro privativo. Oferece pesca com mosca de truta-marrom, arco-íris e de riacho em riachos de nascente, rios e lagos de montanha, com almoço na água servido pelos guias. Não publica programas nem tarifas: pede que você consulte.',
	],
	'Challhuaquen Lodge' => [
		'descripcion_pt' => 'Lodge de pesca em Trevelin, na rota 259, km 59, segundo o seu site. O site que lemos não publica programas, capacidade nem tarifas.',
	],
];
foreach ( $tarjetas as $nombre => $campos ) {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( ! $ids ) {
		WP_CLI::warning( "$nombre: no existe la ficha, se salta" );
		continue;
	}
	$pid = $ids[0];
	if ( (string) pm_campo( 'descripcion_pt', $pid ) !== '' ) {
		WP_CLI::log( "· $nombre (#$pid) ya tiene descripción en portugués: no se toca" );
		continue;
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $nombre (#$pid) → descripción en portugués" );
	if ( $aplicar ) {
		foreach ( $campos as $k => $v ) {
			update_field( $k, $v, $pid );
		}
		$ok = (string) pm_campo( 'descripcion_pt', $pid ) === $campos['descripcion_pt'];
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$pid portugués leído de vuelta" );
		$ok || WP_CLI::error( 'la ficha no quedó.' );
	}
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
