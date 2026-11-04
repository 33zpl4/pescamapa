<?php
/**
 * Portuguese (Brazil) version of the Río Grande guide (clave rio-grande), 4/11/2026: owner's rule of completing languages. Mirrors the
 * Spanish and English guides of the same day (same body, 10 FAQs, 3 waters, 5 season rows, 8 sources, same limits). Shared data (region,
 * coordinates, species, season dates, cover photo) stays on the Spanish guide. Also adds the Portuguese texts of the four outfitter cards.
 * Idempotent: if the pt guide exists it is not touched; a card that already has a pt description keeps it. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = rio-grande-pt-2026-11-04).
 */

$aplicar = false;
$hoy     = '20261104';

function pm_rgp_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_rgp_h( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_rgp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_rgp_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$u = [
	'res'  => 'https://prodyambiente.tierradelfuego.gob.ar/wp-content/uploads/2026/10/Resolucion-N259-Temporada-pesca-2026.pdf',
	'pag'  => 'https://prodyambiente.tierradelfuego.gob.ar/pesca-deportiva-tdf/',
	'ktr'  => 'https://www.kautapen.com/rates.html',
	'ktl'  => 'https://www.kautapen.com/location.html',
	'aho'  => 'https://www.argentinaheritagefishing.com/rio-grande',
	'sj'   => 'https://sanjoseflyfishing.com/',
	'wuij' => 'https://kdflyfishing.com/destination/wuijoor-lodge',
	'rn24' => 'https://www.rionegro.com.ar/?p=3837803',
];
$fuentes = [
	[ 'Resolução S.A. n.º 259/2026 da Secretaria de Meio Ambiente da Terra do Fogo: regulamento, licenças e valores da temporada 2026/27 (PDF oficial, lido em 4/11/2026)', $u['res'], 1 ],
	[ 'Ministério da Produção e Meio Ambiente da Terra do Fogo: pesca esportiva, temporada 2026-2027 (página oficial, lida em 4/11/2026)', $u['pag'], 1 ],
	[ 'Kau Tapen Lodge: tarifas 2027 (operador)', $u['ktr'], 0 ],
	[ 'Kau Tapen Lodge: localização e como chegar (operador)', $u['ktl'], 0 ],
	[ 'AHO Fishing: Río Grande (operador)', $u['aho'], 0 ],
	[ 'San José Lodge (operador)', $u['sj'], 0 ],
	[ 'KD Flyfishing: Wuijoor Lodge, rio Menéndez (vendedor de viagens)', $u['wuij'], 0 ],
	[ 'Diario Río Negro, 15/10/2024: onde vale a licença patagônica (imprensa, da temporada 2024/25)', $u['rn24'], 0 ],
];
$filas = array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $fuentes );

$cuerpo = implode( "\n\n", [
	pm_rgp_p( '<strong>Río Grande</strong>, no norte da ilha da Terra do Fogo, é a base para pescar a <strong>truta-marrom anádroma</strong> (a que vive no mar e sobe o rio para desovar) no <strong>rio Grande</strong> e no seu afluente, o <strong>Menéndez</strong>. Os lodges ficam em estâncias a 50 a 60 minutos do aeroporto. Esta página reúne as regras 2026/27 lidas do texto oficial, a licença e quanto custa, o que os lodges publicam e o que ainda não confirmamos.' ),
	pm_rgp_h( 'A temporada 2026/27' ),
	pm_rgp_p( 'A Secretaria de Meio Ambiente abriu a temporada com a <strong>Resolução S.A. n.º 259/2026</strong>: começou em <strong>30 de outubro de 2026</strong> e termina em <strong>2 de maio de 2027</strong>, inclusive; depois disso vale o defeso em toda a província. No <strong>rio Grande e seus afluentes</strong> (Candelaria, Ona, Menéndez, Rasmussen, Moneta e Herminita) a pesca abre em 30 de outubro e fecha em <strong>18 de abril de 2027</strong>, inclusive.' ),
	pm_rgp_p( 'Um esclarecimento para você não se confundir: a página do Ministério ainda diz, para o rio Grande, "até 13 de abril de 2025", uma data da temporada passada que ficou sem atualizar. Seguimos a resolução, que é o texto assinado da norma, e por isso publicamos 18 de abril de 2027.' ),
	pm_rgp_h( 'Regras gerais que lemos' ),
	pm_rgp_lista( [
		'<strong>Devolução obrigatória</strong> de todas as peças nos ambientes provinciais de <strong>1º de março de 2027</strong> até o encerramento. Antes dessa data, e salvo nos ambientes com regras próprias, cada pescador pode sacrificar <strong>um exemplar por dia</strong> e não pode transportar mais de um.',
		'<strong>Modalidades permitidas:</strong> colher ou spinning, e mosca. Uma única isca artificial por vara, com um anzol simples ou duplo. No spinning permite-se a bóia (buldo) e a mosca.',
		'<strong>Proibido:</strong> o corrico (trolling) ou arrasto a partir de uma embarcação motorizada, a isca natural, o chumbo, o gancho (garfo), as redes, os espinhéis e as iscas com pilhas ou baterias. A sanção atinge o pescador e o condutor da embarcação.',
		'<strong>Pesca só de dia</strong>, entre os crepúsculos que o Serviço de Hidrografia Naval publica. A pesca noturna é proibida em toda a província.',
		'O salmão-chinook e o salmão-coho figuram entre as espécies permitidas, sujeitas às modalidades e condições de cada ambiente.',
	] ),
	pm_rgp_h( 'O rio Grande e seus afluentes' ),
	pm_rgp_lista( [
		'Só se permite a pesca com <strong>devolução obrigatória de todos os salmonídeos</strong>, de 30 de outubro de 2026 a 18 de abril de 2027.',
		'Os pescadores registrados no <strong>Sistema de Acessos de Pesca da Terra do Fogo (SAPTDF)</strong> e com <strong>reserva prévia</strong> acessam o setor "El Tropezón" e os demais acessos habilitados. Quem reserva e não avisa com 24 horas de antecedência pode ser suspenso do sistema.',
		'<strong>Rio Claro:</strong> só um trecho, com devolução obrigatória, a pé, com reserva no SAPTDF e uma cota de 10 varas por dia.',
		'<strong>Proibido pescar o ano todo</strong> no arroio Mimica, no rio La Unión ou In, no riacho Los Salmones, na lagoa San Luis e no trecho do rio Valdez entre a ponte da rota nacional 3 e o lago Fagnano. O lago Yakush e o rio Indio estão fechados enquanto durar o fechamento preventivo da Reserva Provincial Corazón de la Isla.',
	] ),
	pm_rgp_h( 'A licença e quanto custa' ),
	pm_rgp_p( 'A licença é <strong>provincial</strong>: faz-se o registro em <strong>www.siped.ar</strong> e compra-se em lojas autorizadas de Río Grande, Ushuaia e Tolhuin (entre elas Angler\'s Aventuras Fly Shop, El Robalo e a Asociación Riograndense de Pesca con Mosca, em Río Grande). Os valores da resolução para <strong>estrangeiros não residentes</strong>, em pesos argentinos:' ),
	pm_rgp_lista( [
		'<strong>Licença ordinária:</strong> um dia AR$ 78.000; três dias AR$ 117.000; sete dias AR$ 195.000; temporada AR$ 260.000.',
		'<strong>Licença adicional por zona preferencial, rios Grande e Menéndez:</strong> um dia AR$ 59.000; três dias AR$ 163.000; sete dias AR$ 260.000; temporada AR$ 390.000. É adicional à ordinária.',
		'Para estrangeiros de países limítrofes, o adicional dos rios Grande e Menéndez é de AR$ 65.000 por dia, AR$ 78.000 por três dias, AR$ 130.000 por sete dias e AR$ 175.000 por temporada.',
		'<strong>Residentes na Argentina:</strong> um dia AR$ 26.000; três dias AR$ 39.000; sete dias AR$ 65.000; temporada AR$ 90.000; de 13 a 17 anos, temporada AR$ 20.000. Sem custo para menores de 12 anos, pessoas com deficiência, aposentados e pensionistas, só para pesca com devolução.',
	] ),
	pm_rgp_p( 'Segundo a imprensa de 2024, a licença patagônica não vale na Terra do Fogo nem em Santa Cruz. Um lodge pode incluir ou não a licença na tarifa: leia o que cada um diz.' ),
	pm_rgp_h( 'Quando os peixes entram' ),
	pm_rgp_p( 'Os operadores não concordam, e dizemos isso. A AHO Fishing diz que por volta de meados de novembro os peixes começam a voltar do mar e que no fim de janeiro a maior parte da população já está no rio. A KD Flyfishing, que vende viagens ao Wuijoor Lodge, situa a temporada de truta-do-mar entre dezembro e abril, com o fim de fevereiro até meados de abril como os meses de maior produção. O Kau Tapen Lodge abre sua temporada 2027 em 26 de dezembro de 2026 e a fecha em 17 de abril de 2027. Para escolher a semana, pergunte ao seu lodge.' ),
	pm_rgp_h( 'Como chegar' ),
	pm_rgp_p( 'O Kau Tapen Lodge descreve o voo de Buenos Aires a Río Grande como de cerca de três horas e o traslado do aeroporto ao lodge como de 50 a 60 minutos. A KD Flyfishing informa 50 minutos do aeroporto de Río Grande ao Wuijoor Lodge. Ushuaia é a alternativa, a cerca de três horas por estrada. São os tempos de cada operador.' ),
	pm_rgp_pendiente( 'Estancia María Behety, La Villa María Behety e Despedida Lodge (seus sites não abriram no nosso leitor); texto da resolução no Boletim Oficial; calendário por mês com fonte técnica; níveis do rio; foto com seu crédito; guias independentes.' ),
	pm_rgp_h( 'Lodges e operadores' ),
	pm_rgp_p( 'Abaixo estão os que encontramos com informação pública; ainda não os contatamos, por isso aparecem marcados. Uma tarifa só é mostrada quando o operador a publica com sua temporada.' ),
] );

$g = [
	'titulo' => 'Río Grande (Tierra del Fuego)', 'slug' => 'pesca-em-rio-grande',
	'campos' => [
		'clave' => 'rio-grande', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
		'reglamento_autoridad' => 'Secretaría de Ambiente de Tierra del Fuego (Ministerio de Producción y Ambiente)',
		'reglamento_url' => $u['pag'], 'reglamento_verificado' => $hoy,
		'h1'          => 'Pesca em Río Grande, Terra do Fogo: truta-do-mar, regras e licença 2026/27',
		'bajada'      => 'A truta-marrom anádroma do rio Grande e do Menéndez. As regras 2026/27 lidas do texto oficial, a licença, as tarifas que os lodges publicam e o que falta confirmar.',
		'modalidades' => "Mosca (flycast) com vara de duas mãos e de uma mão\nSpinning (colher)\nCaptura e devolução",
		'epoca'       => 'De 30 de outubro de 2026 a 2 de maio de 2027; no rio Grande e seus afluentes, até 18 de abril de 2027.',
		'seo_title'   => 'Pesca em Río Grande, Terra do Fogo: regras e licença',
		'seo_description' => 'Guia de pesca de truta-marrom anádroma em Río Grande, Terra do Fogo: regras 2026/27, licença, tarifas publicadas pelos lodges e como chegar.',
		'temporada_nota' => 'Datas do rio Grande e seus afluentes; o resto da província fecha em 2 de maio de 2027.',
		'temporada'   => [
			[ 'meses' => '30 de outubro', 'que' => 'Abre a temporada 2026/27 em toda a província (Resolução S.A. 259/2026).' ],
			[ 'meses' => 'Dezembro a abril', 'que' => 'Temporada dos lodges: Kau Tapen de 26 de dezembro de 2026 a 17 de abril de 2027; Wuijoor Lodge de dezembro a abril, com o melhor do fim de fevereiro a meados de abril (segundo o vendedor de viagens).' ],
			[ 'meses' => '1º de março', 'que' => 'A partir desta data a devolução das peças é obrigatória em toda a província.' ],
			[ 'meses' => '18 de abril', 'que' => 'Fecha a pesca no rio Grande e seus afluentes (2027). O resto da província fecha em 2 de maio de 2027.' ],
			[ 'meses' => 'Calendário por mês', 'que' => 'PENDIENTE: quando os peixes entram e como a água muda mês a mês, com fonte técnica.' ],
		],
		'reglamento_resumen' => 'Resolução S.A. n.º 259/2026 da Secretaria de Meio Ambiente da Terra do Fogo (regulamento, licenças e valores da temporada 2026/27), lida no PDF oficial em 4/11/2026. Temporada de 30/10/2026 a 2/5/2027; no rio Grande e seus afluentes, devolução obrigatória até 18/4/2027. Os valores de licença são os da resolução; confirme-os ao comprar.',
		'aguas' => [
			[ 'nombre' => 'Rio Grande', 'nota' => 'Devolução obrigatória de todos os salmonídeos; acesso com reserva no SAPTDF; até 18 de abril de 2027.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Menéndez', 'nota' => 'Afluente do Grande, com o mesmo regime de devolução obrigatória.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Claro', 'nota' => 'Só um trecho, a pé, com reserva no SAPTDF e 10 varas por dia.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Quando é a temporada de pesca em Río Grande?', 'respuesta' => 'A temporada 2026/27 começou em 30 de outubro de 2026 e termina em 2 de maio de 2027. No rio Grande e seus afluentes a pesca fecha em 18 de abril de 2027 (Resolução S.A. 259/2026). A página do Ministério ainda mostra a data da temporada passada, mas a resolução diz 2027.' ],
			[ 'pregunta' => 'Posso ficar com os peixes que pesco?', 'respuesta' => 'No rio Grande e seus afluentes, não: só se permite a pesca com devolução obrigatória de todos os salmonídeos. Em outros ambientes da província você pode sacrificar um exemplar por dia até 28 de fevereiro de 2027; a partir de 1º de março, a devolução é obrigatória em toda a província.' ],
			[ 'pregunta' => 'Que licença preciso e quanto custa?', 'respuesta' => 'A licença provincial da Terra do Fogo, registrada em www.siped.ar. Para estrangeiros não residentes em 2026/27: um dia AR$ 78.000, três dias AR$ 117.000, sete dias AR$ 195.000, temporada AR$ 260.000. Para pescar nos rios Grande e Menéndez soma-se uma licença adicional de zona preferencial: um dia AR$ 59.000, três dias AR$ 163.000, sete dias AR$ 260.000, temporada AR$ 390.000. Valores da Resolução 259/2026, em pesos.' ],
			[ 'pregunta' => 'A licença patagônica serve?', 'respuesta' => 'Segundo a imprensa de 2024, a licença patagônica não vale na Terra do Fogo nem em Santa Cruz. A da Terra do Fogo é provincial. Confirme com o seu lodge ou com a Secretaria de Meio Ambiente antes de comprar.' ],
			[ 'pregunta' => 'Pode pescar com isca natural ou fazer corrico?', 'respuesta' => 'Não. Só se permitem a colher ou spinning e a mosca, com uma única isca artificial e um anzol simples ou duplo. Estão proibidos a isca natural, o corrico a partir de embarcação motorizada, o chumbo, o gancho, as redes e as iscas com pilhas.' ],
			[ 'pregunta' => 'Pode pescar à noite?', 'respuesta' => 'Não. Só se pesca entre os crepúsculos que o Serviço de Hidrografia Naval publica, e a pesca noturna é proibida em toda a província.' ],
			[ 'pregunta' => 'Quanto custa uma semana em um lodge de Río Grande?', 'respuesta' => 'O Kau Tapen Lodge publica no seu site, para a temporada de 26 de dezembro de 2026 a 17 de abril de 2027, US$ 13.950 por pessoa e semana de 26/12 a 9/1, US$ 14.950 de 9/1 a 20/3 e US$ 12.950 de 20/3 a 17/4, em quarto single e com guia compartilhado (site do lodge, lido em 4/11/2026). Os demais operadores que encontramos não publicam tarifa.' ],
			[ 'pregunta' => 'O que a tarifa de um lodge inclui?', 'respuesta' => 'No caso do Kau Tapen: hospedagem, traslado do aeroporto de Río Grande, todas as refeições e bebidas, guia profissional (dois pescadores por guia) e equipamento emprestado. Não inclui passagens, a licença de pesca (o lodge calcula cerca de US$ 550 por pescador), lavanderia nem gorjetas. Pede um depósito de 50 % e ele não é reembolsável. Cada lodge inclui coisas diferentes: peça o detalhe.' ],
			[ 'pregunta' => 'Qual é a melhor época para ir?', 'respuesta' => 'Depende de quem você pergunta. A AHO Fishing diz que os peixes começam a voltar em meados de novembro e que no fim de janeiro a maior parte já está no rio; um vendedor de viagens ao Wuijoor Lodge situa o melhor entre o fim de fevereiro e meados de abril. Pergunte ao seu lodge pela semana que você escolher.' ],
			[ 'pregunta' => 'Como chego a Río Grande?', 'respuesta' => 'Há voos de Buenos Aires a Río Grande, de cerca de três horas segundo o Kau Tapen Lodge, e o traslado ao lodge leva de 50 a 60 minutos. Também se pode chegar por Ushuaia, a cerca de três horas por estrada.' ],
		],
	],
];
$t = mb_strlen( $g['campos']['seo_title'] );
$d = mb_strlen( $g['campos']['seo_description'] );
if ( $t > 60 || $d > 160 || $d < 70 || 10 !== count( $g['campos']['faqs'] ) ) {
	WP_CLI::error( "SEO o preguntas fuera de rango: title $t, description $d" );
}

if ( pm_buscar( 'pm_destino', 'rio-grande', 'pt' ) ) {
	WP_CLI::log( 'rio-grande (pt) ya existe: no toco la guía.' );
} elseif ( ! pm_buscar( 'pm_destino', 'rio-grande', 'es' ) ) {
	WP_CLI::error( 'No encontré la guía rio-grande (es).' );
} else {
	WP_CLI::log( 'rio-grande (pt): ' . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con 10 preguntas y " . count( $filas ) . " fuentes (title $t, description $d)" );
	if ( $aplicar ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
		if ( is_wp_error( $id ) ) {
			WP_CLI::error( $id->get_error_message() );
		}
		foreach ( $g['campos'] + [ 'idioma' => 'pt', 'fuentes' => $filas ] as $k => $v ) {
			update_field( $k, $v, $id );
		}
		update_post_meta( $id, '_pm_traducido', time() + 1 );
		WP_CLI::log( "✓ rio-grande (pt) publicada #$id: " . get_permalink( $id ) );
		WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
	}
}

// --- Portuguese texts of the four cards ---------------------------------------------------
$tarjetas = [
	'Kau Tapen Lodge' => [
		'descripcion_pt' => 'Lodge no rio Grande, em funcionamento desde 1984 segundo o seu site. Hospeda até 10 pescadores (às vezes 12) em quartos individuais com banheiro. O site publica para a temporada de 26 de dezembro de 2026 a 17 de abril de 2027 entre US$ 12.950 e US$ 14.950 por pessoa e semana conforme a data, em quarto single e com guia compartilhado (dois pescadores por guia). Inclui traslado do aeroporto de Río Grande, refeições e bebidas, guia e equipamento emprestado; não inclui passagens, a licença de pesca, lavanderia nem gorjetas. Pede um depósito de 50 % não reembolsável.',
		'precio_pt' => 'US$ 12.950 a 14.950 por pessoa e semana, conforme a data (temporada 26/12/2026 a 17/4/2027; quarto single, guia compartilhado)',
	],
	'San José Lodge' => [
		'descripcion_pt' => 'Lodge de pesca de truta-marrom anádroma na Estancia San José, a uma hora da cidade de Río Grande segundo o seu site. O site não publica tarifas nem capacidade; apenas convida a planejar a viagem.',
	],
	'AHO Fishing, Río Grande' => [
		'descripcion_pt' => 'Programa de pesca em um lodge no rio Grande. Segundo o seu site tem quatro quartos duplos com banheiro privativo para no máximo oito hóspedes, com refeições e bebidas incluídas, e só se permitem quatro varas no rio ao mesmo tempo. Recomenda varas de duas mãos de 7 a 9 e de 12 a 15 pés. Não publica tarifa.',
	],
	'Wuijoor Lodge' => [
		'descripcion_pt' => 'Lodge no rio Menéndez, dentro da Estancia Laura, a cerca de 60 km a sudoeste da cidade de Río Grande, com capacidade para seis pescadores em três quartos duplos. Fundado em 2023 e aberto para pesca em 2024, segundo a KD Flyfishing, que vende viagens a este lodge. A estadia habitual é de sete noites com seis dias de pesca; o traslado do aeroporto de Río Grande leva 50 minutos. Não publicamos tarifa: a página que lemos não a mostra.',
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
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $nombre (#$pid) → descripción en portugués" . ( isset( $campos['precio_pt'] ) ? ' y precio en portugués' : '' ) );
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
