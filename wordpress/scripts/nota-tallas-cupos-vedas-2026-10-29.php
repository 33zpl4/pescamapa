<?php
/**
 * Note "Tallas mínimas, cupos y vedas" (es/pt/en), 29/10/2026: one table per jurisdiction with ONLY what our own guides
 * already verified against official sources (Corrientes: Disposición 853/22; Buenos Aires: pejerrey; Neuquén: 2025-2026
 * annex). Each block says which rule it comes from and when it was consulted. What was not confirmed is said in the last
 * section, not filled in. Not included on purpose: the 85 cm "national reference" for surubí (cited in the Goya guide
 * but not read in full: Corrientes requires release), cupos of Buenos Aires outside the veda (no source), Entre Ríos.
 *
 * Idempotent: a language that already exists is not touched. Dry run first; run with wp-script.yml
 * (script = nota-tallas-cupos-vedas-2026-10-29).
 */

$aplicar = false;
$hoy     = '20261029';

function pm_tc_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_tc_h2( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_tc_h3( string $t ): string {
	return "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$t}</h3>\n<!-- /wp:heading -->";
}
function pm_tc_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_tc_tabla( array $cab, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cab ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}

$u_disp  = 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf';
$u_ley   = 'https://senadoctes.gov.ar/normativas/leyes/6730.pdf';
$u_abc   = 'https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/';
$u_ellit = 'https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana';
$u_veda  = 'https://www.gba.gob.ar/desarrollo_agrario/DAPA/vedas';
$u_lic   = 'https://www.gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva';
$u_dec   = 'https://normas.gba.gob.ar/documentos/V9A2qFPV.html';
$u_reg   = 'https://reglamentodepesca.org.ar/reglamento/';
$u_perm  = 'https://cazaypesca.neuquen.gob.ar/permisos';

$f = fn( $nombre, $url, $ofi, $fecha ) => [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $ofi, 'consultada' => $fecha ];
$fuentes = [
	'es' => [
		$f( 'Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)', $u_disp, 1, '20261021' ),
		$f( 'Senado de Corrientes: Ley 6730 (dorado, Monumento Natural Provincial)', $u_ley, 1, '20261020' ),
		$f( 'ABC Color (1/6/2026): Corrientes y Chaco decretan veda temporal para preservar cardúmenes de surubí en el Paraná', $u_abc, 0, '20261026' ),
		$f( 'El Litoral (9/9/2026): rige una veda extraordinaria para proteger un cardumen de dorados en el Paraná', $u_ellit, 0, '20261026' ),
		$f( 'Ministerio de Desarrollo Agrario de Buenos Aires: vedas (veda anual del pejerrey)', $u_veda, 1, '20261027' ),
		$f( 'Ministerio de Desarrollo Agrario de Buenos Aires: obtenga su licencia de pesca deportiva', $u_lic, 1, '20261027' ),
		$f( 'Decreto 3237/95 (reglamento de la Ley 11.477), texto actualizado', $u_dec, 1, '20261027' ),
		$f( 'Reglamento de Pesca Deportiva Continental Patagónico 2025/2026 (Parte Primera y Anexo Neuquén)', $u_reg, 1, '20261024' ),
		$f( 'Caza y Pesca de Neuquén: permisos de pesca', $u_perm, 1, '20261024' ),
	],
	'pt' => [
		$f( 'Boletim Oficial de Corrientes: Disposição 853/22 (tamanhos e limites)', $u_disp, 1, '20261021' ),
		$f( 'Senado de Corrientes: Lei 6730 (dourado, Monumento Natural Provincial)', $u_ley, 1, '20261020' ),
		$f( 'ABC Color (1/6/2026): Corrientes e Chaco decretam defeso temporário para preservar cardumes de surubim no Paraná', $u_abc, 0, '20261026' ),
		$f( 'El Litoral (9/9/2026): defeso extraordinário para proteger um cardume de dourados no Paraná', $u_ellit, 0, '20261026' ),
		$f( 'Ministério de Desenvolvimento Agrário de Buenos Aires: defesos (defeso anual do pejerrei)', $u_veda, 1, '20261027' ),
		$f( 'Ministério de Desenvolvimento Agrário de Buenos Aires: licença de pesca esportiva', $u_lic, 1, '20261027' ),
		$f( 'Decreto 3237/95 (regulamento da Lei 11.477), texto atualizado', $u_dec, 1, '20261027' ),
		$f( 'Regulamento de Pesca Esportiva Continental Patagônico 2025/2026 (Parte Primeira e Anexo Neuquén)', $u_reg, 1, '20261024' ),
		$f( 'Caza y Pesca de Neuquén: permissões de pesca', $u_perm, 1, '20261024' ),
	],
	'en' => [
		$f( 'Corrientes Official Gazette: Disposición 853/22 (size and bag limits)', $u_disp, 1, '20261021' ),
		$f( 'Corrientes Senate: Law 6730 (dorado, Provincial Natural Monument)', $u_ley, 1, '20261020' ),
		$f( 'ABC Color (1/6/2026): Corrientes and Chaco order a temporary closure to protect surubí shoals on the Paraná', $u_abc, 0, '20261026' ),
		$f( 'El Litoral (9/9/2026): extraordinary closure to protect a shoal of dorado on the Paraná', $u_ellit, 0, '20261026' ),
		$f( 'Buenos Aires Ministry of Agrarian Development: closures (annual pejerrey closed season)', $u_veda, 1, '20261027' ),
		$f( 'Buenos Aires Ministry of Agrarian Development: get your sport fishing licence', $u_lic, 1, '20261027' ),
		$f( 'Decree 3237/95 (regulation of Law 11.477), updated text', $u_dec, 1, '20261027' ),
		$f( 'Patagonian Continental Sport Fishing Regulation 2025/2026 (Part One and Neuquén Annex)', $u_reg, 1, '20261024' ),
		$f( 'Caza y Pesca de Neuquén: fishing permits', $u_perm, 1, '20261024' ),
	],
];

$notas = [];

$notas['es'] = [
	'titulo' => 'Tallas mínimas, cupos y vedas de la pesca deportiva: lo que verificamos, por provincia y especie',
	'slug'   => 'tallas-cupos-vedas',
	'bajada' => 'Medidas, cupos y vedas que ya verificamos en fuentes oficiales para Corrientes, Buenos Aires y Neuquén. Lo que no pudimos confirmar, lo decimos.',
	'seo_title' => 'Tallas, cupos y vedas de pesca por provincia | Pescamapa',
	'seo_description' => 'Medidas mínimas, cupos y vedas de la pesca deportiva en Corrientes, Buenos Aires y Neuquén, con la norma y la fecha de consulta. Lo no verificado, aclarado.',
	'datos' => [
		'Corrientes: el dorado y el surubí son de pesca y devolución obligatoria (Disposición 853/22).',
		'Corrientes: pacú 45 cm, patí 70 cm, boga 45 cm y tararira 30 cm, con cupo por pescador.',
		'Buenos Aires: pejerrey 25 cm; en la veda (1/9 al 30/11) se pesca solo sábados, domingos y feriados, con hasta 10 piezas por pescador y día en Chascomús.',
		'Neuquén: las reglas se fijan por temporada; la edición publicada que pudimos leer es la 2025-2026.',
		'De las demás provincias todavía no verificamos medidas ni cupos.',
	],
	'cuerpo' => [
		pm_tc_p( '<strong>Respuesta corta.</strong> Esta página junta las medidas mínimas, los cupos y las vedas que ya verificamos en fuentes oficiales, para las zonas donde tenemos guía. No es un reglamento nacional: cada provincia fija las suyas y las cambia. Cada bloque dice de qué norma sale y cuándo la consultamos; lo que no pudimos confirmar online no está, y lo marcamos al final.' ),
		pm_tc_h2( 'Corrientes (Paso de la Patria, Goya, Esquina)' ),
		pm_tc_p( 'Rige la <a href="' . $u_disp . '" rel="noopener">Disposición 853/22</a> de la Dirección de Recursos Naturales (Boletín Oficial de Corrientes, 26 de diciembre de 2022), vigente hasta que otro acto la modifique. Consultada el 21/10/2026.' ),
		pm_tc_tabla( [ 'Especie', 'Medida mínima', 'Cupo', 'Observaciones' ], [
			[ 'Dorado', '—', 'Pesca y devolución obligatoria', 'Monumento Natural Provincial (<a href="' . $u_ley . '" rel="noopener">Ley 6730</a>)' ],
			[ 'Surubí', '—', 'Pesca y devolución obligatoria', 'Disposición 853/22' ],
			[ 'Pacú', '45 cm', '1 por licencia y un solo ejemplar por embarcación', '—' ],
			[ 'Patí', '70 cm', '2', '—' ],
			[ 'Boga', '45 cm', '2 por pescador y hasta 6 por embarcación', '—' ],
			[ 'Tararira', '30 cm', '5', '—' ],
		] ),
		pm_tc_p( 'Lo retenido solo puede movilizarse dentro de la localidad donde se pescó.' ),
		pm_tc_h3( 'Vedas extraordinarias de 2026 (cambian: confirmá cuáles siguen vigentes)' ),
		pm_tc_lista( [
			'Desde el 29 de mayo de 2026 rige una veda extraordinaria en el Paraná entre los km 1232 y 1238, dispuesta por Corrientes (Disposición 618) y Chaco (Disposición 023) para proteger una concentración de surubíes. No tiene fecha de fin (<a href="' . $u_abc . '" rel="noopener">ABC Color, 1/6/2026</a>).',
			'En septiembre de 2026 se dispuso otra entre los km 1109 y 1150, aguas arriba de Goya, para proteger un cardumen de dorados (<a href="' . $u_ellit . '" rel="noopener">El Litoral, 9/9/2026</a>).',
		] ),
		pm_tc_p( '<em>Las vedas por desove y las extraordinarias las fija cada año Recursos Naturales: antes de salir, confirmá las vigentes en el tramo que vas a pescar.</em>' ),
		pm_tc_h2( 'Buenos Aires (laguna de Chascomús)' ),
		pm_tc_p( 'Rigen la Ley 11.477 y el <a href="' . $u_dec . '" rel="noopener">Decreto 3237/95</a>, y la veda anual del pejerrey que publica el <a href="' . $u_veda . '" rel="noopener">Ministerio de Desarrollo Agrario</a>. Consultados el 27/10/2026.' ),
		pm_tc_tabla( [ 'Especie', 'Medida mínima', 'Cupo', 'Veda y reglas' ], [
			[ 'Pejerrey', '25 cm', 'Hasta 10 piezas por pescador y por día en Chascomús durante la veda; fuera de la veda no tenemos un cupo confirmado', 'Del 1/9 al 30/11: solo sábados, domingos y feriados; sin competencias ni motonáutica; motores fuera de borda a 15 km/h como máximo' ],
		] ),
		pm_tc_p( 'La <a href="' . $u_lic . '" rel="noopener">licencia de pesca deportiva</a> es obligatoria. Es gratuita para menores de 14 años, jubilados y quienes cumplan la Resolución 46/05; el valor de las demás lo informa el Ministerio.' ),
		pm_tc_h2( 'Patagonia (Neuquén)' ),
		pm_tc_p( 'Las reglas se fijan por temporada. La temporada 2026-2027 empieza el 1 de noviembre de 2026 y los permisos se venden online desde el 15 de octubre (<a href="' . $u_perm . '" rel="noopener">Caza y Pesca de Neuquén</a>, consultado el 24/10/2026). El reglamento publicado más reciente que pudimos leer es el de 2025-2026 (<a href="' . $u_reg . '" rel="noopener">Anexo Neuquén</a>):' ),
		pm_tc_lista( [
			'Boca del Chimehuín: solo mosca y devolución de todos los peces.',
			'Malleo alto: solo mosca y devolución.',
			'Chimehuín, Malleo, Collón Curá y Quilquihue cerraron el 31 de mayo de 2026.',
			'Prohibido el calzado con suela de fieltro.',
			'Lácar y Huechulafquen (Parque Nacional Lanín): rigen las reglas del anexo de Parques Nacionales, con límite de 2 salmónidos; las fechas 2026-2027 están sin confirmar.',
		] ),
		pm_tc_p( '<em>Confirmá el reglamento 2026-2027 apenas se publique.</em>' ),
		pm_tc_h2( 'Lo que todavía no verificamos' ),
		pm_tc_p( '<strong>Entre Ríos</strong>: no pudimos confirmar online la resolución vigente de medidas, cupos y vedas. Tampoco verificamos todavía Santa Fe, Chaco, Misiones, Córdoba ni el resto de las provincias. No las publicamos: preguntá a la autoridad provincial o a tu guía. Cuando una fuente oficial confirme un dato, lo sumamos con su fecha.' ),
		pm_tc_p( '<em>Información orientativa: la norma oficial vigente prevalece sobre cualquier resumen, incluido este.</em>' ),
	],
];

$notas['pt'] = [
	'titulo' => 'Tamanhos mínimos, limites e defesos da pesca esportiva: o que verificamos, por província e espécie',
	'slug'   => 'tamanhos-limites-defesos',
	'bajada' => 'Medidas, limites e defesos que já verificamos em fontes oficiais para Corrientes, Buenos Aires e Neuquén. O que não conseguimos confirmar, dizemos.',
	'seo_title' => 'Tamanhos e limites de pesca por província | Pescamapa',
	'seo_description' => 'Tamanhos mínimos, limites e defesos da pesca esportiva em Corrientes, Buenos Aires e Neuquén, com a norma e a data de consulta. O não verificado, esclarecido.',
	'datos' => [
		'Corrientes: o dourado e o surubim são de pesca e devolução obrigatória (Disposição 853/22).',
		'Corrientes: pacu 45 cm, pati 70 cm, boga 45 cm e traíra 30 cm, com limite por pescador.',
		'Buenos Aires: pejerrei 25 cm; no defeso (1/9 a 30/11) pesca-se só aos sábados, domingos e feriados, com até 10 peças por pescador e dia em Chascomús.',
		'Neuquén: as regras são fixadas por temporada; a edição publicada que conseguimos ler é a 2025-2026.',
		'Das demais províncias ainda não verificamos tamanhos nem limites.',
	],
	'cuerpo' => [
		pm_tc_p( '<strong>Resposta curta.</strong> Esta página reúne os tamanhos mínimos, os limites e os defesos que já verificamos em fontes oficiais, para as zonas onde temos guia. Não é um regulamento nacional: cada província fixa os seus e os muda. Cada bloco diz de qual norma sai e quando a consultamos; o que não conseguimos confirmar online não está aqui, e marcamos no final.' ),
		pm_tc_h2( 'Corrientes (Paso de la Patria, Goya, Esquina)' ),
		pm_tc_p( 'Vale a <a href="' . $u_disp . '" rel="noopener">Disposição 853/22</a> da Direção de Recursos Naturais (Boletim Oficial de Corrientes, 26 de dezembro de 2022), em vigor até que outro ato a modifique. Consultada em 21/10/2026.' ),
		pm_tc_tabla( [ 'Espécie', 'Tamanho mínimo', 'Limite', 'Observações' ], [
			[ 'Dourado', '—', 'Pesca e devolução obrigatória', 'Monumento Natural Provincial (<a href="' . $u_ley . '" rel="noopener">Lei 6730</a>)' ],
			[ 'Surubim', '—', 'Pesca e devolução obrigatória', 'Disposição 853/22' ],
			[ 'Pacu', '45 cm', '1 por licença e um único exemplar por embarcação', '—' ],
			[ 'Pati', '70 cm', '2', '—' ],
			[ 'Boga', '45 cm', '2 por pescador e até 6 por embarcação', '—' ],
			[ 'Traíra', '30 cm', '5', '—' ],
		] ),
		pm_tc_p( 'O que for mantido só pode circular dentro da localidade onde foi pescado.' ),
		pm_tc_h3( 'Defesos extraordinários de 2026 (mudam: confirme quais continuam em vigor)' ),
		pm_tc_lista( [
			'Desde 29 de maio de 2026 vale um defeso extraordinário no Paraná entre os km 1232 e 1238, decretado por Corrientes (Disposição 618) e Chaco (Disposição 023) para proteger uma concentração de surubins. Não tem data de término (<a href="' . $u_abc . '" rel="noopener">ABC Color, 1/6/2026</a>).',
			'Em setembro de 2026 foi decretado outro entre os km 1109 e 1150, rio acima de Goya, para proteger um cardume de dourados (<a href="' . $u_ellit . '" rel="noopener">El Litoral, 9/9/2026</a>).',
		] ),
		pm_tc_p( '<em>Os defesos de desova e os extraordinários são fixados a cada ano por Recursos Naturais: antes de sair, confirme os vigentes no trecho onde vai pescar.</em>' ),
		pm_tc_h2( 'Buenos Aires (lagoa de Chascomús)' ),
		pm_tc_p( 'Valem a Lei 11.477 e o <a href="' . $u_dec . '" rel="noopener">Decreto 3237/95</a>, e o defeso anual do pejerrei publicado pelo <a href="' . $u_veda . '" rel="noopener">Ministério de Desenvolvimento Agrário</a>. Consultados em 27/10/2026.' ),
		pm_tc_tabla( [ 'Espécie', 'Tamanho mínimo', 'Limite', 'Defeso e regras' ], [
			[ 'Pejerrei', '25 cm', 'Até 10 peças por pescador e por dia em Chascomús durante o defeso; fora do defeso não temos um limite confirmado', 'De 1/9 a 30/11: só aos sábados, domingos e feriados; sem competições nem esportes náuticos a motor; motores de popa a 15 km/h no máximo' ],
		] ),
		pm_tc_p( 'A <a href="' . $u_lic . '" rel="noopener">licença de pesca esportiva</a> é obrigatória. É gratuita para menores de 14 anos, aposentados e quem cumpra a Resolução 46/05; o valor das demais é informado pelo Ministério.' ),
		pm_tc_h2( 'Patagônia (Neuquén)' ),
		pm_tc_p( 'As regras são fixadas por temporada. A temporada 2026-2027 começa em 1.º de novembro de 2026 e as permissões são vendidas online desde 15 de outubro (<a href="' . $u_perm . '" rel="noopener">Caza y Pesca de Neuquén</a>, consultado em 24/10/2026). O regulamento publicado mais recente que conseguimos ler é o de 2025-2026 (<a href="' . $u_reg . '" rel="noopener">Anexo Neuquén</a>):' ),
		pm_tc_lista( [
			'Boca del Chimehuín: só mosca e devolução de todos os peixes.',
			'Malleo alto: só mosca e devolução.',
			'Chimehuín, Malleo, Collón Curá e Quilquihue fecharam em 31 de maio de 2026.',
			'Proibido o calçado com sola de feltro.',
			'Lácar e Huechulafquen (Parque Nacional Lanín): valem as regras do anexo de Parques Nacionais, com limite de 2 salmonídeos; as datas 2026-2027 estão sem confirmar.',
		] ),
		pm_tc_p( '<em>Confirme o regulamento 2026-2027 assim que for publicado.</em>' ),
		pm_tc_h2( 'O que ainda não verificamos' ),
		pm_tc_p( '<strong>Entre Ríos</strong>: não conseguimos confirmar online a resolução vigente de tamanhos, limites e defesos. Também ainda não verificamos Santa Fe, Chaco, Misiones, Córdoba nem as demais províncias. Não as publicamos: pergunte à autoridade provincial ou ao seu guia. Quando uma fonte oficial confirmar um dado, o acrescentamos com sua data.' ),
		pm_tc_p( '<em>Informação orientativa: a norma oficial vigente prevalece sobre qualquer resumo, inclusive este.</em>' ),
	],
];

$notas['en'] = [
	'titulo' => 'Size limits, bag limits and closed seasons in sport fishing: what we have verified, by province and species',
	'slug'   => 'size-bag-limits-closed-seasons',
	'bajada' => 'Size limits, bag limits and closed seasons we have already verified in official sources for Corrientes, Buenos Aires and Neuquén. What we could not confirm, we say.',
	'seo_title' => 'Fishing size limits, bag limits and closures | Pescamapa',
	'seo_description' => 'Minimum sizes, bag limits and closed seasons for sport fishing in Corrientes, Buenos Aires and Neuquén, with the rule and date checked. Unverified: stated.',
	'datos' => [
		'Corrientes: dorado and surubí are catch and release only (Disposición 853/22).',
		'Corrientes: pacú 45 cm, patí 70 cm, boga 45 cm and tararira 30 cm, with a bag limit per angler.',
		'Buenos Aires: pejerrey 25 cm; during the closed season (1 Sep to 30 Nov) fishing is allowed only on Saturdays, Sundays and holidays, up to 10 fish per angler per day in Chascomús.',
		'Neuquén: rules are set each season; the latest published edition we could read is 2025-2026.',
		'For the other provinces we have not verified sizes or bag limits yet.',
	],
	'cuerpo' => [
		pm_tc_p( '<strong>Short answer.</strong> This page gathers the size limits, bag limits and closed seasons we have already verified in official sources, for the areas where we have a guide. It is not a national regulation: each province sets its own and changes them. Each block says which rule it comes from and when we checked it; what we could not confirm online is not here, and we list it at the end.' ),
		pm_tc_h2( 'Corrientes (Paso de la Patria, Goya, Esquina)' ),
		pm_tc_p( '<a href="' . $u_disp . '" rel="noopener">Disposición 853/22</a> of the Natural Resources Directorate (Corrientes Official Gazette, 26 December 2022) applies until another act changes it. Checked on 21/10/2026.' ),
		pm_tc_tabla( [ 'Species', 'Minimum size', 'Bag limit', 'Notes' ], [
			[ 'Dorado', '—', 'Catch and release only', 'Provincial Natural Monument (<a href="' . $u_ley . '" rel="noopener">Law 6730</a>)' ],
			[ 'Surubí', '—', 'Catch and release only', 'Disposición 853/22' ],
			[ 'Pacú', '45 cm', '1 per licence and a single fish per boat', '—' ],
			[ 'Patí', '70 cm', '2', '—' ],
			[ 'Boga', '45 cm', '2 per angler and up to 6 per boat', '—' ],
			[ 'Tararira', '30 cm', '5', '—' ],
		] ),
		pm_tc_p( 'Fish you keep may only be moved within the town where they were caught.' ),
		pm_tc_h3( '2026 extraordinary closures (they change: check which are still in force)' ),
		pm_tc_lista( [
			'Since 29 May 2026 an extraordinary closure applies on the Paraná between km 1232 and 1238, ordered by Corrientes (Disposición 618) and Chaco (Disposición 023) to protect a concentration of surubí. It has no end date (<a href="' . $u_abc . '" rel="noopener">ABC Color, 1/6/2026</a>).',
			'In September 2026 another one was ordered between km 1109 and 1150, upstream of Goya, to protect a shoal of dorado (<a href="' . $u_ellit . '" rel="noopener">El Litoral, 9/9/2026</a>).',
		] ),
		pm_tc_p( '<em>Spawning closures and extraordinary ones are set every year by Natural Resources: before you go, check the ones in force on the stretch you plan to fish.</em>' ),
		pm_tc_h2( 'Buenos Aires (Chascomús lagoon)' ),
		pm_tc_p( 'Law 11.477 and <a href="' . $u_dec . '" rel="noopener">Decree 3237/95</a> apply, together with the annual pejerrey closed season published by the <a href="' . $u_veda . '" rel="noopener">Ministry of Agrarian Development</a>. Checked on 27/10/2026.' ),
		pm_tc_tabla( [ 'Species', 'Minimum size', 'Bag limit', 'Closed season and rules' ], [
			[ 'Pejerrey', '25 cm', 'Up to 10 fish per angler per day in Chascomús during the closed season; outside it we have no confirmed bag limit', '1 Sep to 30 Nov: only Saturdays, Sundays and holidays; no competitions or motorsports; outboards at 15 km/h at most' ],
		] ),
		pm_tc_p( 'A <a href="' . $u_lic . '" rel="noopener">sport fishing licence</a> is required. It is free for children under 14, retirees and anyone who meets Resolution 46/05; the fee for everyone else is set by the Ministry.' ),
		pm_tc_h2( 'Patagonia (Neuquén)' ),
		pm_tc_p( 'Rules are set each season. The 2026-2027 season opens on 1 November 2026 and permits are sold online since 15 October (<a href="' . $u_perm . '" rel="noopener">Caza y Pesca de Neuquén</a>, checked on 24/10/2026). The most recent regulation we could read is the 2025-2026 one (<a href="' . $u_reg . '" rel="noopener">Neuquén Annex</a>):' ),
		pm_tc_lista( [
			'Boca del Chimehuín: fly fishing only, and all fish are released.',
			'Upper Malleo: fly fishing only, catch and release.',
			'The Chimehuín, Malleo, Collón Curá and Quilquihue closed on 31 May 2026.',
			'Felt-soled boots are banned.',
			'Lácar and Huechulafquen (Lanín National Park): the National Parks annex applies, with a limit of 2 salmonids; the 2026-2027 dates are unconfirmed.',
		] ),
		pm_tc_p( '<em>Check the 2026-2027 regulation as soon as it is published.</em>' ),
		pm_tc_h2( 'What we have not verified yet' ),
		pm_tc_p( '<strong>Entre Ríos</strong>: we could not confirm the current resolution on sizes, bag limits and closures online. We have not verified Santa Fe, Chaco, Misiones, Córdoba or the other provinces either. We do not publish them: ask the provincial authority or your guide. When an official source confirms a figure, we add it with its date.' ),
		pm_tc_p( '<em>For guidance only: the official rule in force prevails over any summary, including this one.</em>' ),
	],
];

// Length checks the SEO fields must pass (the site's own limits: title <= 60, description 70-160).
foreach ( $notas as $i => $n ) {
	if ( mb_strlen( $n['seo_title'] ) > 60 || mb_strlen( $n['seo_description'] ) > 160 || mb_strlen( $n['seo_description'] ) < 70 ) {
		WP_CLI::error( "$i: seo_title " . mb_strlen( $n['seo_title'] ) . ' / seo_description ' . mb_strlen( $n['seo_description'] ) . ' fuera de rango' );
	}
}

foreach ( [ 'es', 'pt', 'en' ] as $i ) {
	$n = $notas[ $i ];
	if ( pm_buscar( 'pm_nota', 'tallas-cupos-vedas', $i ) ) {
		WP_CLI::log( "tallas-cupos-vedas ($i) ya existe: no toco nada." );
		continue;
	}
	$cuerpo = implode( "\n\n", $n['cuerpo'] );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": tallas-cupos-vedas ($i) «{$n['titulo']}» /{$n['slug']}/ — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes[ $i ] ) . ' fuentes; title ' . mb_strlen( $n['seo_title'] ) . ', description ' . mb_strlen( $n['seo_description'] ) );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [
		'post_type'    => 'pm_nota',
		'post_status'  => 'publish',
		'post_title'   => $n['titulo'],
		'post_name'    => $n['slug'],
		'post_content' => wp_slash( $cuerpo ),
	], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = [
		'clave' => 'tallas-cupos-vedas', 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
		'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ),
		'fuentes' => $fuentes[ $i ], 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'],
	];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_origen' => 'Compilada el 29/10/2026 solo con lo que ya verificaron nuestras guías de Corrientes (Disposición 853/22), Chascomús (Ministerio de Desarrollo Agrario de Buenos Aires) y Neuquén (reglamento 2025-2026). Excluido a propósito: surubí 85 cm de referencia nacional (Res. 9/2021, citada en la guía de Goya, no leída completa), cupo del pejerrey fuera de veda (sin fuente) y Entre Ríos (no confirmado).' ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes[ $i ] ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'wp-block-table' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": tallas-cupos-vedas ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "tallas-cupos-vedas ($i): la lectura de vuelta no coincide." );
}
if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::success( $aplicar ? 'Nota creada.' : 'Simulacro terminado.' );
