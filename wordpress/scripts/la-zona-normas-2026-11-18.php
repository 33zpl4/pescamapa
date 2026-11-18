<?php
/**
 * La Zona: reglamento de la CARU (texto original de 2004, art. por art.), llamado 01-2026 (Boletín Oficial de Entre Ríos del 12/11/2026), reapertura
 * atribuida a la prensa, aclaración de especies y arreglo de «listing … listing» (18/11/2026).
 * Toca, en es/en/pt: la nota «La Zona (Salto Grande)», la guía de Concordia (clave concordia) y la ficha «La Zona Lodge».
 * Cada edición es un reemplazo de texto exacto (debe aparecer una sola vez): si el texto publicado ya no es el esperado queda «falta» y no se escribe nada.
 * Idempotente. Simulacro por defecto ($aplicar = false). Sin nombres de personas, sin teléfonos ni correos, sin enlaces comerciales.
 * Docs: docs/investigacion/la-zona-normas-2026-11-18.md. Corre con wp-script.yml (script = la-zona-normas-2026-11-18; aplicar=true para la real).
 */

$aplicar = false;
$hoy     = '20261118';

/** Reemplazo exacto: devuelve [nuevo contenido, estado] con estado ya | cambia | falta. */
function pm_lz_rep( string $c, string $viejo, string $nuevo ): array {
	if ( false !== strpos( $c, $nuevo ) ) {
		return [ $c, 'ya' ];
	}
	if ( 1 === substr_count( $c, $viejo ) ) {
		return [ str_replace( $viejo, $nuevo, $c ), 'cambia' ];
	}
	return [ $c, 'falta' ];
}
function pm_lz_ficha_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}
function pm_lz_post( string $tipo, string $clave, string $i ): ?WP_Post {
	$ps = get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	foreach ( $ps as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}

// ---- Fuentes que se suman (nombre; url vacía = sin enlace) --------------------------------------------------------------------------------
$F = [
	'es' => [
		[ 'CARU, Resolución 27/2004 (Paysandú, 12/11/2004), Anexo 1: texto original del reglamento de pesca y devolución del dorado en la zona de seguridad (documento leído; las modificatorias no se leyeron todas)', '', 1 ],
		[ 'Boletín Oficial de Entre Ríos, 12/11/2026: llamado al Concurso Público de Méritos y Antecedentes N° 01-2026 (Concursos)', '', 1 ],
		[ 'El Entre Ríos (4/6/2026): «la “Zona” vuelve a operar tras despejarse la incertidumbre regulatoria sobre su concesión» (prensa)', 'https://www.elentrerios.com/actualidad/pesca-deportiva-internacional-la-ldquozonardquo-vuelve-a-operar-tras-despejarse-la-incertidumbre-regulatoria-sobre-su-concesin.htm', 0 ],
		[ 'Dos Florines (2/7/2026): «Tras dos años reabrió La Zona» (prensa)', 'https://dosflorines.com.ar/tras-dos-anos-reabrio-la-zona-tradicional-lodge-de-pesca-internacional-de-concordia-es-turismo-divisas-y-empleo/', 0 ],
		[ 'Revista Aire Libre (2/7/2026): reapertura de La Zona (prensa)', 'https://www.revista-airelibre.com/2026/07/02/reabrio-en-concordia-el-lodge-de-pesca-internacional-la-zona/', 0 ],
		[ 'El Entre Ríos (8/9/2026 y 18/9/2026): La Zona, segunda etapa y temporada (prensa)', 'https://www.elentrerios.com/actualidad/la-zona-y-turismo-de-concordia-avanzan-en-acciones-conjuntas-para-la-temporada-primavera-verano.htm', 0 ],
		[ 'Revista El Pato (30/9/2026): «La Zona de los grandes» (prensa)', 'https://revistaelpato.com/2026/09/30/la-zona-de-los-grandes/', 0 ],
	],
	'en' => [
		[ 'CARU, Resolution 27/2004 (Paysandú, 12/11/2004), Annex 1: original text of the regulation on dorado fishing and release in the safety zone (document read; not all later amendments were read)', '', 1 ],
		[ 'Boletín Oficial of Entre Ríos, 12/11/2026: call for Public Competition of Merits and Background No. 01-2026 (Concursos)', '', 1 ],
		[ 'El Entre Ríos (4/6/2026): “La Zona is back in operation after the regulatory uncertainty over its concession was cleared” (press, our translation)', 'https://www.elentrerios.com/actualidad/pesca-deportiva-internacional-la-ldquozonardquo-vuelve-a-operar-tras-despejarse-la-incertidumbre-regulatoria-sobre-su-concesin.htm', 0 ],
		[ 'Dos Florines (2/7/2026): “Two years later, La Zona reopened” (press, our translation)', 'https://dosflorines.com.ar/tras-dos-anos-reabrio-la-zona-tradicional-lodge-de-pesca-internacional-de-concordia-es-turismo-divisas-y-empleo/', 0 ],
		[ 'Revista Aire Libre (2/7/2026): reopening of La Zona (press)', 'https://www.revista-airelibre.com/2026/07/02/reabrio-en-concordia-el-lodge-de-pesca-internacional-la-zona/', 0 ],
		[ 'El Entre Ríos (8/9/2026 and 18/9/2026): La Zona, second stage and season (press)', 'https://www.elentrerios.com/actualidad/la-zona-y-turismo-de-concordia-avanzan-en-acciones-conjuntas-para-la-temporada-primavera-verano.htm', 0 ],
		[ 'Revista El Pato (30/9/2026): “La Zona de los grandes” (press)', 'https://revistaelpato.com/2026/09/30/la-zona-de-los-grandes/', 0 ],
	],
	'pt' => [
		[ 'CARU, Resolução 27/2004 (Paysandú, 12/11/2004), Anexo 1: texto original do regulamento de pesca e devolução do dourado na zona de segurança (documento lido; nem todas as alterações posteriores foram lidas)', '', 1 ],
		[ 'Boletim Oficial de Entre Ríos, 12/11/2026: chamamento ao Concurso Público de Méritos e Antecedentes N° 01-2026 (Concursos)', '', 1 ],
		[ 'El Entre Ríos (4/6/2026): «a “Zona” volta a operar após se dissipar a incerteza regulatória sobre sua concessão» (imprensa, tradução nossa)', 'https://www.elentrerios.com/actualidad/pesca-deportiva-internacional-la-ldquozonardquo-vuelve-a-operar-tras-despejarse-la-incertidumbre-regulatoria-sobre-su-concesin.htm', 0 ],
		[ 'Dos Florines (2/7/2026): «Após dois anos, La Zona reabriu» (imprensa, tradução nossa)', 'https://dosflorines.com.ar/tras-dos-anos-reabrio-la-zona-tradicional-lodge-de-pesca-internacional-de-concordia-es-turismo-divisas-y-empleo/', 0 ],
		[ 'Revista Aire Libre (2/7/2026): reabertura de La Zona (imprensa)', 'https://www.revista-airelibre.com/2026/07/02/reabrio-en-concordia-el-lodge-de-pesca-internacional-la-zona/', 0 ],
		[ 'El Entre Ríos (8/9/2026 e 18/9/2026): La Zona, segunda etapa e temporada (imprensa)', 'https://www.elentrerios.com/actualidad/la-zona-y-turismo-de-concordia-avanzan-en-acciones-conjuntas-para-la-temporada-primavera-verano.htm', 0 ],
		[ 'Revista El Pato (30/9/2026): «La Zona de los grandes» (imprensa)', 'https://revistaelpato.com/2026/09/30/la-zona-de-los-grandes/', 0 ],
	],
];

// ---- Textos comunes por idioma ------------------------------------------------------------------------------------------------------------
$T = [];

$T['es'] = [
	'llamado' => 'Después figura un nuevo pliego, el Concurso Público de Méritos y Antecedentes N° 01-2026. <strong>El llamado está publicado</strong> en el Boletín Oficial de Entre Ríos del 12/11/2026 (sección Concursos; Dirección de Recursos Naturales y Fiscalización, autorizado por el Decreto 3450 de 2026). Objeto: un proyecto de conservación e investigación del dorado y de desarrollo turístico asociado en la zona de seguridad aguas abajo de Salto Grande, jurisdicción argentina; contrato de 10 años; canon de 36.000 litros de nafta súper por semestre; visita técnica el 26/11/2026 a las 10:00; presentación y apertura de ofertas el 10/12/2026 a las 10:00. Según el pliego: solo pesca deportiva con devolución, de lunes a jueves y de día; hasta 2 embarcaciones simultáneas del lado argentino, salvo autorización; hasta 4 personas por embarcación; la temporada y la veda se ajustan a la CARU y la autoridad puede decretar vedas totales o parciales; la fecha de apertura se fija por una resolución posterior (el pliego no la trae). <strong>Al 18/11/2026 no hay adjudicación: las ofertas se abren el 10/12/2026.</strong> Quién tiene hoy el permiso del lado argentino no está publicado en ningún acto oficial que hayamos leído.',
	'prensa' => ' <strong>Reapertura, según la prensa.</strong> El Entre Ríos (4/6/2026) titula que «la “Zona” vuelve a operar tras despejarse la incertidumbre regulatoria sobre su concesión» y cita a la empresa que la opera: retomó el servicio «en ejercicio de los derechos emergentes de la concesión oportunamente prorrogada por la Provincia de Entre Ríos hasta el año 2029», según informó la propia empresa. Dos Florines y Revista Aire Libre (ambos del 2/7/2026) hablan de una reapertura de La Zona tras dos años sin operar «debido a trabas administrativas»; El Entre Ríos (18/9/2026) cita a la empresa: una temporada con «altísimo nivel de reservas». Revista El Pato (30/9/2026) escribe que a la reserva «solo pueden ingresar dos embarcaciones con concesión del lado argentino y dos del lado uruguayo». Es lo que dicen la empresa y la prensa: no leímos un acto oficial que otorgue o prorrogue esa concesión, y el concurso N° 01-2026 sigue abierto.',
	'orig' => 'Son los artículos del reglamento de la CARU (Resolución 27/2004), en su texto actualizado. Leímos también el texto original, de Paysandú, 12/11/2004 (Anexo 1), que ya fija lo esencial: solo dorado (art. 5.1), solo desde embarcaciones (art. 3.1), de lunes a jueves y de día (art. 7.2), sin carnada y con anzuelo sin rebaba (art. 8.1), un solo equipo por pescador (art. 9.1), sin pinzas (art. 10) y hasta cuatro embarcaciones a la vez (art. 11.1). La CARU puede modificar el reglamento y lo modificó en puntos (por ejemplo, el límite de 500 m, Resolución 63/2026); no leímos todas las modificatorias: antes de ir, mirá el texto vigente.',
	'veda_cola' => ' El aviso de la CARU del 7/10/2026 dice que «se encuentra en vigencia la veda anual de pesca deportiva y comercial del Dorado (Salminus brasiliensis) en el río Uruguay, comprendida entre el 1 de septiembre y el 31 de diciembre de cada año»; no nombra La Zona.',
	'especies' => ' Las demás especies no están autorizadas en la zona reglamentada de la represa (art. 5.1).',
];
$T['en'] = [
	'llamado' => 'A new tender document then appears, Concurso Público de Méritos y Antecedentes N° 01-2026 (public competition of merits and background). <strong>The call is published</strong> in the Boletín Oficial of Entre Ríos of 12/11/2026 (Concursos section; Dirección de Recursos Naturales y Fiscalización, authorised by Decree 3450 of 2026). Object: a project for dorado conservation and research and the tourism development tied to it in the safety zone downstream of Salto Grande, Argentine jurisdiction; a 10-year contract; a fee of 36,000 litres of super petrol per half-year; technical visit on 26/11/2026 at 10:00; submission and opening of bids on 10/12/2026 at 10:00. According to the tender document: sport fishing with release only, Monday to Thursday and in daylight; up to 2 boats at a time on the Argentine side, unless authorised; up to 4 people per boat; the season and the closed season follow CARU and the authority may decree full or partial closures; the opening date is set by a later resolution (the document does not give it). <strong>As of 18/11/2026 there is no award: bids open on 10/12/2026.</strong> Who holds the Argentine-side permit today is not published in any official act we have read.',
	'prensa' => ' <strong>Reopening, according to the press.</strong> El Entre Ríos (4/6/2026) headlines that “La Zona is back in operation after the regulatory uncertainty over its concession was cleared” and quotes the company that runs it: it resumed service “in exercise of the rights arising from the concession duly extended by the Province of Entre Ríos until 2029”, as the company itself reported (our translation). Dos Florines and Revista Aire Libre (both 2/7/2026) report a reopening of La Zona after two years without operating “due to administrative obstacles”; El Entre Ríos (18/9/2026) quotes the company: a season with an “extremely high level of bookings”. Revista El Pato (30/9/2026) writes that the reserve admits “only two boats with a concession on the Argentine side and two on the Uruguayan side”. This is what the company and the press say: we did not read an official act that grants or extends that concession, and tender N° 01-2026 is still open.',
	'orig' => 'These are the articles of the CARU regulation (Resolution 27/2004), in its updated text. We also read the original text, from Paysandú, 12/11/2004 (Annex 1), which already sets the essentials: golden dorado only (art. 5.1), only from boats (art. 3.1), Monday to Thursday and in daylight (art. 7.2), no bait and a barbless hook (art. 8.1), one rod per angler (art. 9.1), no grips or pliers (art. 10) and up to four boats at a time (art. 11.1). CARU may change the regulation and has changed it in places (for example the 500 m limit, Resolution 63/2026); we did not read every amendment: check the current text before you go.',
	'veda_cola' => ' The CARU notice of 7/10/2026 says that “the annual closed season for sport and commercial fishing of Dorado (Salminus brasiliensis) in the Uruguay River is in force, from 1 September to 31 December of each year” (our translation); it does not name La Zona.',
	'especies' => ' No other species is authorised in the regulated zone of the dam (art. 5.1).',
];
$T['pt'] = [
	'llamado' => 'Depois consta um novo edital, o Concurso Público de Méritos y Antecedentes N° 01-2026. <strong>O chamamento está publicado</strong> no Boletim Oficial de Entre Ríos de 12/11/2026 (seção Concursos; Dirección de Recursos Naturales y Fiscalización, autorizado pelo Decreto 3450 de 2026). Objeto: um projeto de conservação e pesquisa do dourado e de desenvolvimento turístico associado na zona de segurança a jusante de Salto Grande, jurisdição argentina; contrato de 10 anos; canon de 36.000 litros de gasolina super por semestre; visita técnica em 26/11/2026 às 10:00; apresentação e abertura das propostas em 10/12/2026 às 10:00. Segundo o edital: só pesca esportiva com devolução, de segunda a quinta e de dia; até 2 embarcações simultâneas do lado argentino, salvo autorização; até 4 pessoas por embarcação; a temporada e o defeso seguem a CARU e a autoridade pode decretar defesos totais ou parciais; a data de abertura é fixada por resolução posterior (o edital não a traz). <strong>Em 18/11/2026 não há adjudicação: as propostas são abertas em 10/12/2026.</strong> Quem tem hoje a licença do lado argentino não está publicado em nenhum ato oficial que tenhamos lido.',
	'prensa' => ' <strong>Reabertura, segundo a imprensa.</strong> O El Entre Ríos (4/6/2026) titula que «a “Zona” volta a operar após se dissipar a incerteza regulatória sobre sua concessão» e cita a empresa que a opera: retomou o serviço «no exercício dos direitos decorrentes da concessão oportunamente prorrogada pela Província de Entre Ríos até o ano de 2029», segundo informou a própria empresa (tradução nossa). Dos Florines e Revista Aire Libre (ambos de 2/7/2026) falam de uma reabertura de La Zona após dois anos sem operar «devido a entraves administrativos»; o El Entre Ríos (18/9/2026) cita a empresa: uma temporada com «altíssimo nível de reservas». A Revista El Pato (30/9/2026) escreve que na reserva «só podem entrar duas embarcações com concessão do lado argentino e duas do lado uruguaio». É o que dizem a empresa e a imprensa: não lemos um ato oficial que outorgue ou prorrogue essa concessão, e o concurso N° 01-2026 segue aberto.',
	'orig' => 'São os artigos do regulamento da CARU (Resolução 27/2004), no texto atualizado. Lemos também o texto original, de Paysandú, 12/11/2004 (Anexo 1), que já fixa o essencial: só dourado (art. 5.1), só em embarcações (art. 3.1), de segunda a quinta e de dia (art. 7.2), sem isca natural e com anzol sem farpa (art. 8.1), um só equipamento por pescador (art. 9.1), sem alicates (art. 10) e até quatro embarcações ao mesmo tempo (art. 11.1). A CARU pode modificar o regulamento e o modificou em pontos (por exemplo, o limite de 500 m, Resolução 63/2026); não lemos todas as alterações: antes de ir, veja o texto vigente.',
	'veda_cola' => ' O aviso da CARU de 7/10/2026 diz que «está em vigor o defeso anual da pesca esportiva e comercial do Dourado (Salminus brasiliensis) no rio Uruguai, compreendido entre 1.º de setembro e 31 de dezembro de cada ano» (tradução nossa); não nomeia La Zona.',
	'especies' => ' As demais espécies não são autorizadas na zona regulamentada da represa (art. 5.1).',
];

// ---- Ediciones de la NOTA (clave la-zona-salto-grande) -----------------------------------------------------------------------------------
$NOTA = [
	'es' => [
		[ '<strong>Al 13/11/2026 no encontramos publicado quién tiene el permiso del lado argentino.</strong>', '<strong>No está publicado quién tiene hoy el permiso del lado argentino:</strong> el Boletín Oficial de Entre Ríos del 12/11/2026 trae el llamado al Concurso N° 01-2026 y las ofertas se abren el 10/12/2026, así que al 18/11/2026 no hay adjudicación.' ],
		[ 'Son los artículos del reglamento de la CARU, en su texto actualizado:', $T['es']['orig'] ],
		[ 'son la excepción a la especie.', 'son la excepción a la especie.' . $T['es']['especies'] ],
		[ '<strong>Máximo dos minutos fuera del agua</strong> para cada pez (art. 10.3).', '<strong>Devolución obligatoria en el mismo lugar y máximo dos minutos fuera del agua</strong> para cada pez (art. 10.3), en camilla de malla sin nudos y <strong>sin pinzas</strong> de ningún tipo (art. 10).' ],
		[ 'queda como pregunta abierta (abajo).', 'queda como pregunta abierta (abajo).' . $T['es']['veda_cola'] ],
		[ 'Después figura un nuevo pliego, el Concurso Público de Méritos y Antecedentes N° 01-2026 (permiso de diez años, hasta dos embarcaciones, pesca de lunes a jueves). Al 13/11/2026 no encontramos una adjudicación publicada.', $T['es']['llamado'] ],
		[ 'y no decimos que a alguno le falte.', 'y no decimos que a alguno le falte.' . $T['es']['prensa'] ],
		[ 'Quién es el permisionario del lado argentino, y si el concurso N° 01-2026 ya tiene adjudicación.', 'Quién tiene hoy el permiso del lado argentino (no está publicado) y el resultado del concurso N° 01-2026, cuyas ofertas se abren el 10/12/2026.' ],
		[ 'Al 13/11/2026 no encontramos publicado quién tiene el permiso del lado argentino.', 'No está publicado quién tiene hoy el permiso del lado argentino; el llamado al Concurso N° 01-2026 salió en el Boletín Oficial de Entre Ríos del 12/11/2026 y las ofertas se abren el 10/12/2026.' ],
	],
	'en' => [
		[ '<strong>As of 13 November 2026 we found no published record of who holds the Argentine-side permit.</strong>', '<strong>It is not published who holds the Argentine-side permit today:</strong> the Boletín Oficial of Entre Ríos of 12/11/2026 carries the call for Competition N° 01-2026 and bids open on 10/12/2026, so as of 18/11/2026 there is no award.' ],
		[ 'These are the articles of the CARU regulation, in its updated text:', $T['en']['orig'] ],
		[ 'are the exception to the species rule.', 'are the exception to the species rule.' . $T['en']['especies'] ],
		[ '<strong>No more than two minutes out of the water</strong> for each fish (art. 10.3).', '<strong>Release is compulsory, at the same spot, and no more than two minutes out of the water</strong> for each fish (art. 10.3), in a knotless mesh cradle and <strong>with no grips or pliers</strong> of any kind (art. 10).' ],
		[ 'it stays an open question (below).', 'it stays an open question (below).' . $T['en']['veda_cola'] ],
		[ 'A new tender document then appears, Concurso Público de Méritos y Antecedentes N° 01-2026 (ten-year permit, up to two boats, fishing Monday to Thursday). As of 13 November 2026 we found no published award.', $T['en']['llamado'] ],
		[ 'and we do not say either is missing one.', 'and we do not say either is missing one.' . $T['en']['prensa'] ],
		[ 'Who the Argentine-side permit holder is, and whether tender N° 01-2026 has been awarded.', 'Who holds the Argentine-side permit today (it is not published) and the outcome of tender N° 01-2026, whose bids open on 10/12/2026.' ],
		[ 'As of 13 November 2026 we found no published record of who holds the Argentine-side permit.', 'It is not published who holds the Argentine-side permit today; the call for Competition N° 01-2026 appeared in the Boletín Oficial of Entre Ríos of 12/11/2026 and bids open on 10/12/2026.' ],
	],
	'pt' => [
		[ '<strong>Em 13/11/2026 não encontramos publicado quem tem a licença do lado argentino.</strong>', '<strong>Não está publicado quem tem hoje a licença do lado argentino:</strong> o Boletim Oficial de Entre Ríos de 12/11/2026 traz o chamamento ao Concurso N° 01-2026 e as propostas são abertas em 10/12/2026, então em 18/11/2026 não há adjudicação.' ],
		[ 'São os artigos do regulamento da CARU, no texto atualizado:', $T['pt']['orig'] ],
		[ 'são a exceção quanto à espécie.', 'são a exceção quanto à espécie.' . $T['pt']['especies'] ],
		[ '<strong>No máximo dois minutos fora da água</strong> para cada peixe (art. 10.3).', '<strong>Devolução obrigatória no mesmo lugar e no máximo dois minutos fora da água</strong> para cada peixe (art. 10.3), em maca de malha sem nós e <strong>sem alicates</strong> de nenhum tipo (art. 10).' ],
		[ 'fica como pergunta aberta (abaixo).', 'fica como pergunta aberta (abaixo).' . $T['pt']['veda_cola'] ],
		[ 'Depois consta um novo edital, o Concurso Público de Méritos y Antecedentes N° 01-2026 (licença de dez anos, até duas embarcações, pesca de segunda a quinta). Em 13/11/2026 não encontramos adjudicação publicada.', $T['pt']['llamado'] ],
		[ 'e não dizemos que algum deles esteja sem licença.', 'e não dizemos que algum deles esteja sem licença.' . $T['pt']['prensa'] ],
		[ 'Quem é o permissionário do lado argentino, e se o concurso N° 01-2026 já tem adjudicação.', 'Quem tem hoje a licença do lado argentino (não está publicado) e o resultado do concurso N° 01-2026, cujas propostas são abertas em 10/12/2026.' ],
		[ 'Em 13/11/2026 não encontramos publicado quem tem a licença do lado argentino.', 'Não está publicado quem tem hoje a licença do lado argentino; o chamamento ao Concurso N° 01-2026 saiu no Boletim Oficial de Entre Ríos de 12/11/2026 e as propostas são abertas em 10/12/2026.' ],
	],
];
// «listing … listing» en inglés: el enlace a la guía y la palabra repetida (regex porque el enlace lleva la URL de la guía).
$NOTA_RE = [
	'en' => [ '#(<a href="[^"]*">)the Concordia listing</a> as a public-information listing:#', '$1the Concordia guide</a> as a public-information entry:' ],
];
// Datos de la nota (repetidor nota_datos).
$NOTA_DATOS = [
	'es' => [ 'Permisionario del lado argentino: al 13/11/2026 no encontramos una adjudicación publicada.', 'Permiso del lado argentino: no está publicado quién lo tiene hoy; el llamado al Concurso N° 01-2026 salió en el Boletín Oficial de Entre Ríos del 12/11/2026 y las ofertas se abren el 10/12/2026.' ],
	'en' => [ 'Argentine-side permit holder: as of 13 November 2026 we found no published award.', 'Argentine-side permit: it is not published who holds it today; the call for Competition N° 01-2026 appeared in the Boletín Oficial of Entre Ríos of 12/11/2026 and bids open on 10/12/2026.' ],
	'pt' => [ 'Permissionário do lado argentino: em 13/11/2026 não encontramos adjudicação publicada.', 'Licença do lado argentino: não está publicado quem a tem hoje; o chamamento ao Concurso N° 01-2026 saiu no Boletim Oficial de Entre Ríos de 12/11/2026 e as propostas são abertas em 10/12/2026.' ],
];

// ---- Ediciones de la GUÍA (clave concordia) ----------------------------------------------------------------------------------------------
$GUIA = [
	'es' => [
		[ 'Para pescar en La Zona hay que contratar con el permisionario de uno de los dos países. Del lado argentino, el llamado de 2023 fue declarado fracasado por el Decreto 3874/25 de Entre Ríos (Boletín Oficial del 28/1/2026) y hay un nuevo pliego, el Concurso Público de Méritos y Antecedentes N° 01-2026. <strong>Al 13/11/2026 no encontramos una adjudicación publicada.</strong>',
			'<strong>Cómo funciona.</strong> En La Zona solo puede llevar pescadores el permisionario que habilita cada país (una entidad con personería jurídica; cada Estado puede habilitar uno): el pescador contrata con él y lleva los documentos que lo acrediten (art. 4 del reglamento de la CARU, texto original de 2004). Del lado argentino, el llamado de 2023 fue declarado fracasado por el Decreto 3874/25 de Entre Ríos (Boletín Oficial del 28/1/2026) y el Boletín Oficial de Entre Ríos del 12/11/2026 publica el nuevo llamado, el Concurso Público de Méritos y Antecedentes N° 01-2026: contrato de 10 años, visita técnica el 26/11/2026 y apertura de ofertas el 10/12/2026 a las 10:00. <strong>Quién tiene hoy el permiso no está publicado, y al 18/11/2026 no hay adjudicación porque las ofertas se abren el 10/12/2026.</strong> El pliego admite solo la pesca deportiva con devolución, de lunes a jueves y de día, con hasta 2 embarcaciones a la vez del lado argentino (salvo autorización) y hasta 4 personas por embarcación; no trae la fecha de apertura de la temporada, que se fija por una resolución posterior.' ],
		[ 'es lo que dice el lugar, no una garantía de captura.',
			'es lo que dice el lugar, no una garantía de captura. <strong>Dentro de La Zona, la CARU solo autoriza la pesca deportiva del dorado (art. 5.1 del texto original de 2004)</strong>: las otras especies que nombra la publicación no son de la zona reglamentada de la represa, y la publicación no dice dónde se pescan. <strong>Reapertura, según la prensa:</strong> El Entre Ríos (4/6/2026) titula que «la “Zona” vuelve a operar tras despejarse la incertidumbre regulatoria sobre su concesión», y Dos Florines y Revista Aire Libre (2/7/2026) hablan de una reapertura de La Zona tras dos años sin operar; es lo que dicen la prensa y la empresa, no un acto oficial que hayamos leído.' ],
		[ '<strong>Solo dorado y solo con devolución</strong>, con un máximo de dos minutos fuera del agua por pez.',
			'<strong>Solo dorado</strong>: es la única especie cuya pesca deportiva autoriza la CARU en La Zona (art. 5.1). <strong>Devolución obligatoria</strong> en el mismo lugar, con un máximo de dos minutos fuera del agua por pez, en camilla de malla sin nudos y sin pinzas de ningún tipo (art. 10).' ],
		[ 'ni se desembarca, salvo emergencia.', 'ni se desembarca, salvo emergencia (art. 3.1).' ],
		[ 'sin carnada y sin trolling.', 'sin carnada y sin trolling (arts. 7.1 y 8.1); un solo equipo por pescador (art. 9.1).' ],
		[ 'desde el 1 de junio de 2023 (Resolución CARU 37/2023).', 'según el art. 7.2 del texto de 2004 y la Resolución CARU 37/2023 (desde el 1 de junio de 2023).' ],
		[ 'con hasta cuatro personas a bordo, guía y timonel incluidos.', 'con hasta cuatro personas a bordo, guía y timonel incluidos (arts. 11.1 y 11.3).' ],
		[ 'Los artículos y las resoluciones, uno por uno, están en ', 'Los artículos citados son los del texto original del reglamento (Resolución CARU 27/2004, Paysandú, 12/11/2004, Anexo 1); la CARU puede modificarlo y ya lo modificó en puntos (por ejemplo, el límite de 500 m, Resolución 63/2026), y no leímos todas las modificatorias: antes de ir, mirá el texto vigente. Los artículos y las resoluciones, uno por uno, están en ' ],
		[ '<td>Al 13/11/2026 no encontramos una adjudicación publicada</td>', '<td>No está publicado quién lo tiene hoy; concurso 01-2026 con ofertas el 10/12/2026 (sin adjudicación al 18/11/2026)</td>' ],
		[ '<td>Entre Ríos, Decreto 3874/25 y pliego 01-2026</td>', '<td>Boletín Oficial de Entre Ríos del 12/11/2026 y pliego 01-2026</td>' ],
	],
	'en' => [
		[ 'To fish La Zona you must book with the permit holder of one of the two countries. On the Argentine side, the 2023 call was declared failed by Entre Ríos Decree 3874/25 (Official Gazette of 28/1/2026) and there is a new tender, Public Competition of Merits and Background No. 01-2026. <strong>As of 13/11/2026 we found no published award.</strong>',
			'<strong>How it works.</strong> In La Zona only the permit holder that each country authorises may take anglers out (an entity with legal personality; each State may authorise one): the angler books with it and carries documents that prove it (art. 4 of the CARU regulation, original 2004 text). On the Argentine side, the 2023 call was declared failed by Entre Ríos Decree 3874/25 (Official Gazette of 28/1/2026) and the Boletín Oficial of Entre Ríos of 12/11/2026 publishes the new call, Public Competition of Merits and Background No. 01-2026: a 10-year contract, technical visit on 26/11/2026 and bid opening on 10/12/2026 at 10:00. <strong>Who holds the permit today is not published, and as of 18/11/2026 there is no award because bids open on 10/12/2026.</strong> The tender document admits only sport fishing with release, Monday to Thursday and in daylight, with up to 2 boats at a time on the Argentine side (unless authorised) and up to 4 people per boat; it does not give the season opening date, which is set by a later resolution.' ],
		[ 'that is what the place says, not a guarantee of a catch.',
			'that is what the place says, not a guarantee of a catch. <strong>Inside La Zona, CARU authorises sport fishing for dorado only (art. 5.1 of the original 2004 text)</strong>: the other species the post names are not from the regulated zone of the dam, and the post does not say where they are fished. <strong>Reopening, according to the press:</strong> El Entre Ríos (4/6/2026) headlines that “La Zona is back in operation after the regulatory uncertainty over its concession was cleared”, and Dos Florines and Revista Aire Libre (2/7/2026) report a reopening of La Zona after two years without operating; that is what the press and the company say, not an official act we have read.' ],
		[ '<strong>Dorado only and catch and release only</strong>, with a maximum of two minutes out of the water per fish.',
			'<strong>Dorado only</strong>: it is the only species whose sport fishing CARU authorises in La Zona (art. 5.1). <strong>Release is compulsory</strong>, at the same spot, with a maximum of two minutes out of the water per fish, in a knotless mesh cradle and with no grips or pliers of any kind (art. 10).' ],
		[ 'no landing, except in an emergency.', 'no landing, except in an emergency (art. 3.1).' ],
		[ 'no bait and no trolling.', 'no bait and no trolling (arts. 7.1 and 8.1); one rod per angler (art. 9.1).' ],
		[ 'since 1 June 2023 (CARU Resolution 37/2023).', 'under art. 7.2 of the 2004 text and CARU Resolution 37/2023 (since 1 June 2023).' ],
		[ 'with up to four people aboard, guide and boatman included.', 'with up to four people aboard, guide and boatman included (arts. 11.1 and 11.3).' ],
		[ 'The articles and resolutions, one by one, are in ', 'The articles cited are those of the original text of the regulation (CARU Resolution 27/2004, Paysandú, 12/11/2004, Annex 1); CARU may change it and has changed it in places (for example the 500 m limit, Resolution 63/2026), and we did not read every amendment: check the current text before you go. The articles and resolutions, one by one, are in ' ],
		[ '<td>As of 13/11/2026 we found no published award</td>', '<td>It is not published who holds it today; tender 01-2026 with bids on 10/12/2026 (no award as of 18/11/2026)</td>' ],
		[ '<td>Entre Ríos, Decree 3874/25 and tender 01-2026</td>', '<td>Boletín Oficial of Entre Ríos of 12/11/2026 and tender 01-2026</td>' ],
	],
	'pt' => [
		[ 'Para pescar em La Zona é preciso contratar com o permissionário de um dos dois países. Do lado argentino, a licitação de 2023 foi declarada fracassada pelo Decreto 3874/25 de Entre Ríos (Boletim Oficial de 28/1/2026) e há um novo edital, o Concurso Público de Méritos e Antecedentes N° 01-2026. <strong>Em 13/11/2026 não encontramos adjudicação publicada.</strong>',
			'<strong>Como funciona.</strong> Em La Zona só pode levar pescadores o permissionário que cada país habilita (uma entidade com personalidade jurídica; cada Estado pode habilitar um): o pescador contrata com ele e leva os documentos que o comprovem (art. 4 do regulamento da CARU, texto original de 2004). Do lado argentino, a licitação de 2023 foi declarada fracassada pelo Decreto 3874/25 de Entre Ríos (Boletim Oficial de 28/1/2026) e o Boletim Oficial de Entre Ríos de 12/11/2026 publica o novo chamamento, o Concurso Público de Méritos e Antecedentes N° 01-2026: contrato de 10 anos, visita técnica em 26/11/2026 e abertura das propostas em 10/12/2026 às 10:00. <strong>Quem tem hoje a licença não está publicado, e em 18/11/2026 não há adjudicação porque as propostas são abertas em 10/12/2026.</strong> O edital admite só a pesca esportiva com devolução, de segunda a quinta e de dia, com até 2 embarcações por vez do lado argentino (salvo autorização) e até 4 pessoas por embarcação; não traz a data de abertura da temporada, fixada por resolução posterior.' ],
		[ 'é o que diz o lugar, não uma garantia de captura.',
			'é o que diz o lugar, não uma garantia de captura. <strong>Dentro de La Zona, a CARU só autoriza a pesca esportiva do dourado (art. 5.1 do texto original de 2004)</strong>: as outras espécies que a publicação cita não são da zona regulamentada da represa, e a publicação não diz onde são pescadas. <strong>Reabertura, segundo a imprensa:</strong> o El Entre Ríos (4/6/2026) titula que «a “Zona” volta a operar após se dissipar a incerteza regulatória sobre sua concessão», e Dos Florines e Revista Aire Libre (2/7/2026) falam de uma reabertura de La Zona após dois anos sem operar; é o que dizem a imprensa e a empresa, não um ato oficial que tenhamos lido.' ],
		[ '<strong>Só dourado e só com devolução</strong>, com no máximo dois minutos fora da água por peixe.',
			'<strong>Só dourado</strong>: é a única espécie cuja pesca esportiva a CARU autoriza em La Zona (art. 5.1). <strong>Devolução obrigatória</strong> no mesmo lugar, com no máximo dois minutos fora da água por peixe, em maca de malha sem nós e sem alicates de nenhum tipo (art. 10).' ],
		[ 'nem se desembarca, salvo emergência.', 'nem se desembarca, salvo emergência (art. 3.1).' ],
		[ 'sem isca natural e sem corrico.', 'sem isca natural e sem corrico (arts. 7.1 e 8.1); um só equipamento por pescador (art. 9.1).' ],
		[ 'desde 1.º de junho de 2023 (Resolução CARU 37/2023).', 'conforme o art. 7.2 do texto de 2004 e a Resolução CARU 37/2023 (desde 1.º de junho de 2023).' ],
		[ 'com até quatro pessoas a bordo, guia e piloto incluídos.', 'com até quatro pessoas a bordo, guia e piloto incluídos (arts. 11.1 e 11.3).' ],
		[ 'Os artigos e as resoluções, um por um, estão na ', 'Os artigos citados são os do texto original do regulamento (Resolução CARU 27/2004, Paysandú, 12/11/2004, Anexo 1); a CARU pode modificá-lo e já o modificou em pontos (por exemplo, o limite de 500 m, Resolução 63/2026), e não lemos todas as alterações: antes de ir, veja o texto vigente. Os artigos e as resoluções, um por um, estão na ' ],
		[ '<td>Em 13/11/2026 não encontramos adjudicação publicada</td>', '<td>Não está publicado quem a tem hoje; concurso 01-2026 com propostas em 10/12/2026 (sem adjudicação em 18/11/2026)</td>' ],
		[ '<td>Entre Ríos, Decreto 3874/25 e edital 01-2026</td>', '<td>Boletim Oficial de Entre Ríos de 12/11/2026 e edital 01-2026</td>' ],
	],
];

// ---- Ficha La Zona Lodge ------------------------------------------------------------------------------------------------------------------
$FICHA = [
	'descripcion' => [ 'es lo que dice el lugar, no una garantía de captura.', 'es lo que dice el lugar, no una garantía de captura. Dentro de La Zona la CARU solo autoriza la pesca deportiva del dorado (art. 5.1 del texto original de 2004): las otras especies que nombra la publicación no son de la zona reglamentada de la represa. La prensa informó su reapertura tras dos años sin operar (El Entre Ríos, 4/6/2026; Dos Florines y Revista Aire Libre, 2/7/2026).' ],
	'descripcion_en' => [ 'that is what the place says, not a guarantee of a catch.', 'that is what the place says, not a guarantee of a catch. Inside La Zona, CARU authorises sport fishing for dorado only (art. 5.1 of the original 2004 text): the other species the post names are not from the regulated zone of the dam. The press reported its reopening after two years without operating (El Entre Ríos, 4/6/2026; Dos Florines and Revista Aire Libre, 2/7/2026).' ],
	'descripcion_pt' => [ 'é o que diz o lugar, não uma garantia de captura.', 'é o que diz o lugar, não uma garantia de captura. Dentro de La Zona a CARU só autoriza a pesca esportiva do dourado (art. 5.1 do texto original de 2004): as outras espécies que a publicação cita não são da zona regulamentada da represa. A imprensa informou sua reabertura após dois anos sem operar (El Entre Ríos, 4/6/2026; Dos Florines e Revista Aire Libre, 2/7/2026).' ],
];
$ESPECIES_FICHA = [ 'dorado' ];

// ---- Plan -----------------------------------------------------------------------------------------------------------------------------------
$hay_falta = false;
$plan      = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	foreach ( [ 'nota' => [ 'pm_nota', 'la-zona-salto-grande', $NOTA[ $i ] ], 'guia' => [ 'pm_destino', 'concordia', $GUIA[ $i ] ] ] as $que => [ $tipo, $clave, $eds ] ) {
		$p = pm_lz_post( $tipo, $clave, $i );
		if ( ! $p ) {
			WP_CLI::warning( "$que ($i): no existe $clave" );
			$hay_falta = true;
			continue;
		}
		$c = $p->post_content;
		foreach ( $eds as $n => [ $viejo, $nuevo ] ) {
			[ $c, $e ] = pm_lz_rep( $c, $viejo, $nuevo );
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $que ($i) #{$p->ID} edición " . ( $n + 1 ) . ": $e" );
			if ( 'falta' === $e ) {
				WP_CLI::warning( "$que ($i) edición " . ( $n + 1 ) . ': no encuentro (o hay más de una vez) «' . mb_substr( $viejo, 0, 90 ) . '…»' );
				$hay_falta = true;
			}
		}
		if ( 'nota' === $que && isset( $NOTA_RE[ $i ] ) ) {
			$cnt = 0;
			$c2  = preg_replace( $NOTA_RE[ $i ][0], $NOTA_RE[ $i ][1], $c, -1, $cnt );
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": nota ($i) «listing … listing»: " . ( $cnt ? "cambia ($cnt)" : ( false !== strpos( $c, 'public-information entry' ) ? 'ya' : 'falta' ) ) );
			if ( ! $cnt && false === strpos( $c, 'public-information entry' ) ) {
				$hay_falta = true;
			}
			$c = $c2;
		}
		$plan[] = [ 'que' => $que, 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c ];
	}
}

// Ficha.
$fid = pm_lz_ficha_id( 'La Zona Lodge' );
$fid || ( WP_CLI::warning( 'no encuentro la ficha La Zona Lodge' ) || ( $hay_falta = true ) );
$ficha_cambios = [];
if ( $fid ) {
	foreach ( $FICHA as $campo => [ $viejo, $nuevo ] ) {
		$v = (string) pm_campo( $campo, $fid );
		[ $v2, $e ] = pm_lz_rep( $v, $viejo, $nuevo );
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha #$fid $campo: $e" );
		if ( 'falta' === $e ) {
			WP_CLI::warning( "ficha $campo: no encuentro «$viejo»" );
			$hay_falta = true;
		}
		$ficha_cambios[ $campo ] = $v2;
	}
	$esp = (array) pm_campo( 'prest_especies', $fid );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha prest_especies " . implode( ',', $esp ) . ' → ' . implode( ',', $ESPECIES_FICHA ) );
}

// Verificación previa de que nada público lleva enlaces comerciales, contactos ni cuentas.
$prohibido = '/@|instagram\.com|lazonalodge|\+\d{2}[ \d]{6,}|tel[eé]fono|whatsapp|Untamed|href="https?:\/\/(?!(www\.)?(pescamapa\.com))/iu';
foreach ( $plan as $x ) {
	// Solo miro lo que agregamos, no los enlaces que ya tenía la nota (los enlaces ya existentes son internos o de fuentes).
	$nuevo_txt = str_replace( $x['antes'], '', $x['despues'] );
	foreach ( [ $T, $GUIA, $NOTA ] as $bloque ) {
		$s = wp_json_encode( $bloque, JSON_UNESCAPED_UNICODE );
		if ( preg_match( '/@|instagram\.com|lazonalodge|\+\d{2}[ \d]{6,}|tel[eé]fono|whatsapp|Untamed|https?:\/\//iu', $s, $m ) ) {
			WP_CLI::error( 'texto nuevo con algo que no se publica: ' . $m[0] );
		}
	}
	break;
}
WP_CLI::log( 'OK: el texto nuevo no lleva enlaces, cuentas, teléfonos ni el nombre de la empresa.' );

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' . ( $hay_falta ? ' (HAY «falta»: revisar antes de aplicar)' : '' ) );
	return;
}
if ( $hay_falta ) {
	WP_CLI::error( 'no aplico: hay textos que no están como se esperaba (ver «falta»).' );
}

// ---- Aplicar ------------------------------------------------------------------------------------------------------------------------------
$t = time();
foreach ( $plan as $x ) {
	$i = $x['i'];
	if ( $x['antes'] !== $x['despues'] ) {
		$r = wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['despues'] ) ], true );
		is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
		update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
	}
	update_field( 'actualizado', $hoy, $x['id'] );
	// Fuentes: se suman las que falten (por nombre).
	$filas   = pm_filas( 'fuentes', $x['id'] );
	$nombres = array_column( $filas, 'nombre' );
	$nuevas  = 0;
	foreach ( $F[ $i ] as [ $nombre, $url, $of ] ) {
		if ( in_array( $nombre, $nombres, true ) ) {
			continue;
		}
		$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $of, 'consultada' => $hoy ];
		$nuevas++;
	}
	if ( $nuevas ) {
		update_field( 'fuentes', $filas, $x['id'] );
	}
	WP_CLI::log( "✓ {$x['que']} ($i) #{$x['id']}: contenido " . ( $x['antes'] !== $x['despues'] ? 'actualizado' : 'ya estaba' ) . ", +$nuevas fuentes" );
	if ( 'nota' === $x['que'] ) {
		$datos = pm_filas( 'nota_datos', $x['id'] );
		foreach ( $datos as $k => $d ) {
			if ( ( $d['dato'] ?? '' ) === $NOTA_DATOS[ $i ][0] ) {
				$datos[ $k ]['dato'] = $NOTA_DATOS[ $i ][1];
				update_field( 'nota_datos', $datos, $x['id'] );
				WP_CLI::log( "✓ nota ($i) dato del permiso actualizado" );
				break;
			}
		}
	}
}
foreach ( $ficha_cambios as $campo => $v ) {
	update_field( $campo, $v, $fid );
}
update_field( 'prest_especies', $ESPECIES_FICHA, $fid );
update_field( 'verificado', $hoy, $fid );

// ---- Lectura de vuelta ---------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $plan as $x ) {
	$i = $x['i'];
	$c = get_post( $x['id'] )->post_content;
	$eds = 'nota' === $x['que'] ? $NOTA[ $i ] : $GUIA[ $i ];
	foreach ( $eds as $n => [ $viejo, $nuevo ] ) {
		$b = false !== strpos( $c, $nuevo );
		WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) #{$x['id']} edición " . ( $n + 1 ) );
		$ok = $ok && $b;
	}
	if ( 'nota' === $x['que'] && 'en' === $i ) {
		$b = false !== strpos( $c, 'public-information entry' ) && ! preg_match( '/Concordia listing<\/a> as a public-information listing/', $c );
		WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ': nota (en) ya no repite «listing … listing»' );
		$ok = $ok && $b;
	}
	$nombres = array_column( pm_filas( 'fuentes', $x['id'] ), 'nombre' );
	$bf      = ! array_diff( array_column( $F[ $i ], 0 ), $nombres ) && (string) pm_campo( 'actualizado', $x['id'] ) !== '';
	WP_CLI::log( ( $bf ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) fuentes nuevas presentes" );
	$ok = $ok && $bf;
	$html = apply_filters( 'the_content', $c );
	$bl   = ! preg_match( '#href="https?://(www\.)?(instagram\.com|lazonalodge)#i', $html ) && false === stripos( $html, 'Untamed' );
	WP_CLI::log( ( $bl ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) sin enlaces a Instagram ni al sitio del lugar y sin el nombre de la empresa" );
	$ok = $ok && $bl;
}
foreach ( $FICHA as $campo => [ $viejo, $nuevo ] ) {
	$b = false !== strpos( (string) pm_campo( $campo, $fid ), $nuevo );
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": ficha #$fid $campo con la aclaración de especies" );
	$ok = $ok && $b;
}
$esp = (array) pm_campo( 'prest_especies', $fid );
$b   = $esp === $ESPECIES_FICHA;
WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": ficha prest_especies = " . implode( ',', $esp ) );
$ok = $ok && $b;
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
