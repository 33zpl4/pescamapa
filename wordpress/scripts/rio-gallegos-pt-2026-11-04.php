<?php
/**
 * Portuguese (Brazil) version of the Río Gallegos guide (clave rio-gallegos), 4/11/2026: owner's rule of completing languages. Mirrors the
 * Spanish and English guides of the same day (same body, 10 FAQs, 3 waters, 5 season rows, 9 sources, same limits). Shared data (region,
 * coordinates, species, season dates, cover photo) stays on the Spanish guide. Also adds the Portuguese texts of the two outfitter cards.
 * Idempotent. Dry run first ($aplicar = false); run with wp-script.yml (script = rio-gallegos-pt-2026-11-04).
 */

$aplicar = true;
$hoy     = '20261104';

function pm_glp_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_glp_h( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_glp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_glp_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'amb'  => 'https://pesca.santacruz.gob.ar/ambientes/',
	'val'  => 'https://pesca.santacruz.gob.ar/valores/',
	'inicio' => 'https://pesca.santacruz.gob.ar/',
	'kcamp' => 'https://karkuflyfishing.com/the-camp/',
	'kfish' => 'https://karkuflyfishing.com/the-fishing/',
	'kfaq'  => 'https://karkuflyfishing.com/faqs/',
	'lb'    => 'https://www.solidadventures.com/flyfishing-patagonia-searun-browntrout-lasbuitreras/',
	'gs'    => 'https://www.guiasantacruz.net/rio-gallegos/guia-turistica/',
	'rn24'  => 'https://www.rionegro.com.ar/?p=3837803',
];
$fuentes = [
	[ 'Governo de Santa Cruz, Pesca e Aquicultura: Anexo I, Ambientes de Santa Cruz, temporada 2026/2027 (portal oficial, lido em 4/11/2026)', $u['amb'], 1 ],
	[ 'Governo de Santa Cruz, Pesca e Aquicultura: valores e categorias das licenças 2026/2027 (portal oficial, lido em 4/11/2026)', $u['val'], 1 ],
	[ 'Governo de Santa Cruz, Pesca e Aquicultura: portal de trâmites e temporada 2026-2027', $u['inicio'], 1 ],
	[ 'Karku Fly Fishing: o acampamento (operador)', $u['kcamp'], 0 ],
	[ 'Karku Fly Fishing: a pesca e a temporada (operador)', $u['kfish'], 0 ],
	[ 'Karku Fly Fishing: perguntas frequentes e termos (operador)', $u['kfaq'], 0 ],
	[ 'Estancia Las Buitreras, Solid Adventures (operador)', $u['lb'], 0 ],
	[ 'Guía Santa Cruz: Río Gallegos, hospedagens e lodges de pesca (diretório local)', $u['gs'], 0 ],
	[ 'Diario Río Negro, 15/10/2024: onde vale a licença patagônica (imprensa, da temporada 2024/25)', $u['rn24'], 0 ],
];
$filas = array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $fuentes );

$cuerpo = implode( "\n\n", [
	pm_glp_p( '<strong>Río Gallegos</strong>, no sul de Santa Cruz, é a base para pescar a <strong>truta-marrom anádroma</strong> (a que vive no mar e sobe o rio para desovar) no <strong>rio Gallegos</strong>, um rio de pouca declividade que serpenteia pela pampa até o estuário. Os lodges ficam em estâncias a entre 50 minutos e várias horas da cidade. Esta página reúne as regras 2026/27 lidas do anexo oficial, a licença e quanto custa, o que os operadores publicam e o que ainda não confirmamos.' ),
	pm_glp_h( 'As regras do rio Gallegos, 2026/27' ),
	pm_glp_p( 'São as fichas do <strong>Anexo I, Ambientes de Santa Cruz, temporada 2026/2027</strong>, que o portal oficial de Pesca e Aquicultura publica. O próprio anexo esclarece que lista só alguns dos ambientes mais destacados e que, salvo indicação contrária, vale o regulamento geral.' ),
	pm_glp_lista( [
		'<strong>Rio Gallegos:</strong> de <strong>1º de novembro de 2026 a 30 de maio de 2027</strong>. O regime predominante é de <strong>captura e devolução obrigatória</strong> nas duas margens, das nascentes ao estuário marinho.',
		'<strong>Janela especial de fevereiro:</strong> de 1º a 28 de fevereiro de 2027 o limite diário é de <strong>um exemplar de truta-marrom anádroma</strong>.',
		'<strong>Zona de defeso:</strong> as duas margens, a montante e a jusante da <strong>ponte Güer Aike</strong>, numa distância linear de 200 m.',
		'<strong>Gallegos Chico:</strong> de 1º de novembro de 2026 a 3 de maio de 2027, <strong>só mosca</strong> e devolução obrigatória. Há defeso total da ponte Nuevo (rota nacional 40) até a foz.',
		'<strong>Chico Sur:</strong> de 1º de novembro de 2026 a 3 de maio de 2027, captura e devolução obrigatória absoluta e <strong>só mosca</strong>.',
		'<strong>Penitente:</strong> de 1º de novembro de 2026 a 3 de maio de 2027, captura e devolução obrigatória.',
	] ),
	pm_glp_p( 'O anexo define a <strong>área de exclusão</strong> como a faixa de 200 m (ou a indicada) nas duas margens a partir das nascentes e fozes dos cursos vinculados, onde não se pode pescar.' ),
	pm_glp_h( 'A licença e quanto custa' ),
	pm_glp_p( 'A licença é <strong>provincial</strong> e se tramita online no portal oficial. A temporada 2026/27 vai de 1º de outubro de 2026 a 30 de setembro de 2027. Valores publicados pelo portal em 4/11/2026, em pesos argentinos:' ),
	pm_glp_lista( [
		'<strong>Estrangeiros não residentes:</strong> diária AR$ 90.000; semanal AR$ 300.000; temporada AR$ 450.000.',
		'<strong>Corrico (trolling)</strong> (licença adicional que se soma à ordinária): AR$ 20.000 diária, AR$ 30.000 semanal, AR$ 40.000 por temporada.',
		'<strong>Residentes na Argentina:</strong> diária AR$ 15.000; fim de semana AR$ 20.000; semanal AR$ 25.000; temporada AR$ 40.000. Sem custo para maiores de 65 anos, aposentados, pensionistas, menores de 12 anos e pessoas com deficiência.',
	] ),
	pm_glp_p( 'Segundo a imprensa de 2024, a licença patagônica não vale em Santa Cruz nem na Terra do Fogo. A Karku Fly Fishing diz no seu site que cuida das licenças dos seus hóspedes: pergunte ao seu lodge o que está incluído.' ),
	pm_glp_h( 'Quando os peixes entram' ),
	pm_glp_p( 'Segundo a Karku Fly Fishing, a temporada principal do rio Gallegos vai do fim de dezembro a abril. Diz que no começo chegam peixes prateados recém-saídos do mar, que janeiro e fevereiro são os meses dos exemplares maiores e mais agressivos, e que março e abril trazem uma atividade constante e muitos peixes de tamanho médio. É a descrição de um operador: para escolher a semana, pergunte ao seu lodge.' ),
	pm_glp_h( 'Como chegar' ),
	pm_glp_p( 'A Karku Fly Fishing diz que sua estância fica a 50 minutos de carro do aeroporto de Río Gallegos e a cinco minutos do rio, e que inclui os traslados desde o aeroporto. A Estancia Las Buitreras fica a cerca de dez minutos a pé do rio, segundo o seu operador. O diretório Guía Santa Cruz situa o Los Morros Lodge na rota 40, a 180 km da cidade. São os dados de cada operador ou diretório; não verificamos as distâncias.' ),
	pm_glp_pendiente( 'o regulamento geral de pesca de Santa Cruz (solas de feltro, anzóis, desinfecção); Bella Sofía, Los Plateados, Truchaike e Los Morros (sem site próprio que abrisse); calendário por mês com fonte técnica; níveis do rio; foto com seu crédito; guias independentes.' ),
	pm_glp_h( 'Lodges e operadores' ),
	pm_glp_p( 'Abaixo estão os que encontramos com informação pública; ainda não os contatamos, por isso aparecem marcados. Uma tarifa só é mostrada quando o operador a publica com sua temporada, e nenhum destes dois publica a sua.' ),
] );

$g = [
	'titulo' => 'Río Gallegos (Santa Cruz)', 'slug' => 'pesca-em-rio-gallegos',
	'campos' => [
		'clave' => 'rio-gallegos', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
		'reglamento_autoridad' => 'Gobierno de Santa Cruz, Pesca y Acuicultura',
		'reglamento_url' => $u['amb'], 'reglamento_verificado' => $hoy,
		'h1'          => 'Pesca em Río Gallegos, Santa Cruz: truta-do-mar, regras e licença 2026/27',
		'bajada'      => 'A truta-marrom anádroma do rio Gallegos. As regras 2026/27 lidas do anexo oficial, a licença, o que os lodges publicam e o que falta confirmar.',
		'modalidades' => "Mosca (flycast) com vara de duas mãos\nCaptura e devolução",
		'epoca'       => 'De 1º de novembro de 2026 a 30 de maio de 2027 no rio Gallegos; a temporada dos lodges vai do fim de dezembro a abril.',
		'seo_title'   => 'Pesca em Río Gallegos, Santa Cruz: regras e licença',
		'seo_description' => 'Guia de pesca de truta-marrom anádroma no rio Gallegos, Santa Cruz: regras 2026/27 do anexo oficial, licença, lodges e como chegar.',
		'temporada_nota' => 'Gallegos Chico, Chico Sur e Penitente fecham em 3 de maio de 2027.',
		'temporada'   => [
			[ 'meses' => '1º de novembro', 'que' => 'Abre o rio Gallegos (2026/27), com captura e devolução obrigatória.' ],
			[ 'meses' => 'Fim de dezembro a abril', 'que' => 'Temporada principal segundo a Karku Fly Fishing: janeiro e fevereiro para os exemplares maiores, março e abril para a atividade constante (operador).' ],
			[ 'meses' => 'Fevereiro', 'que' => 'De 1º a 28 de fevereiro de 2027 permite-se um exemplar de truta-marrom anádroma por pescador e por dia.' ],
			[ 'meses' => '30 de maio', 'que' => 'Fecha o rio Gallegos (2027). Gallegos Chico, Chico Sur e Penitente fecham em 3 de maio.' ],
			[ 'meses' => 'Calendário por mês', 'que' => 'PENDIENTE: quando os peixes entram e como a água muda mês a mês, com fonte técnica.' ],
		],
		'reglamento_resumen' => 'Anexo I, Ambientes de Santa Cruz, temporada 2026/2027, lido no portal oficial de Pesca e Aquicultura em 4/11/2026. O rio Gallegos abre em 1/11/2026 e fecha em 30/5/2027, com captura e devolução obrigatória, salvo uma janela de 1 a 28/2/2027 com um exemplar de truta-marrom anádroma. Não lemos o regulamento geral; os valores de licença são os do portal, confirme-os ao pagar.',
		'aguas' => [
			[ 'nombre' => 'Rio Gallegos', 'nota' => 'Captura e devolução obrigatória; janela de fevereiro com um exemplar; defeso de 200 m na ponte Güer Aike.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Gallegos Chico', 'nota' => 'Só mosca e devolução; defeso total da ponte Nuevo até a foz.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Chico Sur', 'nota' => 'Só mosca e devolução absoluta.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Quando se pode pescar no rio Gallegos?', 'respuesta' => 'De 1º de novembro de 2026 a 30 de maio de 2027, segundo o Anexo I de ambientes de Santa Cruz. A licença da temporada 2026/27 vale de 1º de outubro de 2026 a 30 de setembro de 2027. Os lodges descrevem sua temporada principal do fim de dezembro a abril.' ],
			[ 'pregunta' => 'Posso ficar com os peixes?', 'respuesta' => 'Em geral não: o regime predominante no rio Gallegos é de captura e devolução obrigatória nas duas margens, das nascentes ao estuário. A exceção é uma janela de 1º a 28 de fevereiro de 2027, com limite diário de um exemplar de truta-marrom anádroma.' ],
			[ 'pregunta' => 'Que licença preciso e quanto custa?', 'respuesta' => 'A licença provincial de Santa Cruz, que se tramita online no portal de Pesca e Aquicultura. Para estrangeiros não residentes em 2026/27: diária AR$ 90.000, semanal AR$ 300.000 e temporada AR$ 450.000. O corrico (trolling) é uma licença adicional. Valores do portal oficial, em pesos, lidos em 4/11/2026.' ],
			[ 'pregunta' => 'A licença patagônica serve?', 'respuesta' => 'Segundo a imprensa de 2024, a licença patagônica não vale em Santa Cruz nem na Terra do Fogo, então é preciso a licença provincial. Confirme com o seu lodge ou no portal oficial antes de pagar.' ],
			[ 'pregunta' => 'Há rios só para mosca?', 'respuesta' => 'Sim. O Gallegos Chico e o Chico Sur são só mosca, com devolução obrigatória. O anexo que lemos não impõe mosca no leito principal do Gallegos, mas não lemos o regulamento geral, então pergunte ao seu guia.' ],
			[ 'pregunta' => 'Onde é proibido pescar?', 'respuesta' => 'No rio Gallegos, a 200 m a montante e a jusante da ponte Güer Aike, nas duas margens. No Gallegos Chico, da ponte Nuevo da rota nacional 40 até a foz. E em geral a 200 m das nascentes e fozes dos cursos vinculados.' ],
			[ 'pregunta' => 'Quanto custa uma semana em um lodge do Gallegos?', 'respuesta' => 'Não podemos dizer com uma fonte do próprio operador: a Karku Fly Fishing e a Estancia Las Buitreras não publicam suas tarifas nos sites que lemos. Há agências que publicam preços, mas não as usamos porque não sabemos o que incluem nem de que temporada são. Peça o detalhe ao lodge.' ],
			[ 'pregunta' => 'O que um lodge do Gallegos inclui?', 'respuesta' => 'Segundo a Karku Fly Fishing: traslados do aeroporto de Río Gallegos, guias, hospedagem em uma cabana (dois quartos individuais e um duplo, com banheiro compartilhado), café da manhã, almoço e jantar, e cuida das licenças. Não inclui lavanderia, equipamento, bebidas alcoólicas nem gorjetas (sugere no mínimo 12 % do custo da viagem). Pede 50 % de depósito não reembolsável e o saldo 90 dias antes.' ],
			[ 'pregunta' => 'Qual é a melhor época para ir?', 'respuesta' => 'A Karku Fly Fishing situa a temporada principal do fim de dezembro a abril: janeiro e fevereiro para os exemplares maiores e mais agressivos, março e abril para uma atividade mais constante. É a opinião de um operador; o rio abre em novembro.' ],
			[ 'pregunta' => 'Como chego?', 'respuesta' => 'Pelo aeroporto de Río Gallegos. A Karku Fly Fishing diz que sua estância fica a 50 minutos de carro e que oferece os traslados. O Los Morros Lodge consta num diretório local a 180 km da cidade pela rota 40.' ],
		],
	],
];
$t = mb_strlen( $g['campos']['seo_title'] );
$d = mb_strlen( $g['campos']['seo_description'] );
if ( $t > 60 || $d > 160 || $d < 70 || 10 !== count( $g['campos']['faqs'] ) ) {
	WP_CLI::error( "SEO o preguntas fuera de rango: title $t, description $d" );
}

if ( pm_buscar( 'pm_destino', 'rio-gallegos', 'pt' ) ) {
	WP_CLI::log( 'rio-gallegos (pt) ya existe: no toco la guía.' );
} elseif ( ! pm_buscar( 'pm_destino', 'rio-gallegos', 'es' ) ) {
	WP_CLI::error( 'No encontré la guía rio-gallegos (es).' );
} else {
	WP_CLI::log( 'rio-gallegos (pt): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con 10 preguntas y " . count( $filas ) . " fuentes (title $t, description $d)" );
	if ( $aplicar ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
		foreach ( $g['campos'] + [ 'idioma' => 'pt', 'fuentes' => $filas ] as $k => $v ) {
			update_field( $k, $v, $id );
		}
		update_post_meta( $id, '_pm_traducido', time() + 1 );
		WP_CLI::log( "✓ rio-gallegos (pt) publicada #$id: " . get_permalink( $id ) );
		WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
	}
}

$tarjetas = [
	'Karku Fly Fishing' => [
		'descripcion_pt' => 'Acampamento de pesca com mosca numa estância centenária a 50 minutos do aeroporto de Río Gallegos, no rio Gallegos. Segundo o seu site, recebe só quatro pescadores por semana, com mais de 40 km de água de acesso exclusivo e mais de 40 poços com nome. Hospeda numa cabana com dois quartos individuais e um duplo, com banheiro compartilhado, energia solar e Wi-Fi. Inclui traslados do aeroporto, guias, café da manhã, almoço e jantar, e diz que cuida das licenças; não inclui lavanderia, equipamento, bebidas alcoólicas nem gorjetas. Pede 50 % de depósito não reembolsável. Não publica tarifa.',
	],
	'Estancia Las Buitreras (Solid Adventures)' => [
		'descripcion_pt' => 'Lodge no rio Gallegos operado pela Solid Adventures, a cerca de dez minutos a pé do rio. Segundo o seu site tem seis quartos duplos e dois individuais, todos com banheiro privativo, e pode hospedar até 14 pessoas mas recebe no máximo 12 pescadores por semana. Os guias falam inglês. Não publica tarifa na página que lemos.',
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
