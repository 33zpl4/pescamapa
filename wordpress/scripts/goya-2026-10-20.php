<?php
/**
 * Goya, 20/10/2026: regulation and calendar update from the owner's research,
 * checked against official sources (laws 6655 and 6730 of Corrientes, Santa
 * Fe Res. 0336/25 on regional licence recognition, national Res. 9/2021).
 * Run with wp-script.yml (script = goya-2026-10-20).
 *
 * Surgical: replaces the calendar PENDIENTE note and adds the dorado line in
 * the body only where the original text is still there; replaces the
 * calendar rows, the regulation summary and the Isoró note; appends FAQs and
 * sources that aren't there yet. Never touches the "verified" date: nobody
 * has confirmed the regulation with the authority yet.
 */

$hoy = '20261020';

$fuentes_nuevas = [
	[ 'nombre' => 'Legislatura de Corrientes: Ley 6655 (pesca en el arroyo Ysoró)', 'url' => 'https://hcdcorrientes.gov.ar/wp-content/uploads/2023/11/Ley-6655-1.pdf', 'oficial' => 1, 'consultada' => $hoy ],
	[ 'nombre' => 'Senado de Corrientes: Ley 6730 (dorado, Monumento Natural Provincial)', 'url' => 'https://senadoctes.gov.ar/normativas/leyes/6730.pdf', 'oficial' => 1, 'consultada' => $hoy ],
	[ 'nombre' => 'Gobierno de Santa Fe: Res. 0336/25, validez regional de licencias de pesca deportiva', 'url' => 'https://www.santafe.gob.ar/ms/ambiente/wp-content/uploads/sites/93/2026/03/RES.-0336-25-VALIDEZ-REGIONAL-LICENCIAS-PESC-DEPORTIVA.pdf', 'oficial' => 1, 'consultada' => $hoy ],
	[ 'nombre' => 'Gobierno de Santa Fe: acuerdo de reconocimiento recíproco de licencias con Entre Ríos, Chaco y Corrientes', 'url' => 'https://www.santafe.gob.ar/noticias/noticia/287531/', 'oficial' => 1, 'consultada' => $hoy ],
	[ 'nombre' => 'Argentina.gob.ar: Resolución 9/2021, medidas mínimas (surubí 85 cm)', 'url' => 'https://www.argentina.gob.ar/normativa/nacional/norma-352495/texto', 'oficial' => 1, 'consultada' => $hoy ],
];

$t = [
	'es' => [
		'ancla_especies' => 'Para Corrientes en general',
		'dorado'         => 'El <strong>dorado</strong> es Monumento Natural Provincial de Corrientes (Ley 6730): se pesca con devolución.',
		'ancla_cal'      => 'calendario orientativo por especie',
		'calendario'     => [
			'<strong>Cómo cambia con la altura del río.</strong> En bajante, los riachos desagotan hacia el cauce principal y los peces se concentran en las bocas de los arroyos y en los pozones del Paraná: es cuando mejor rinden los señuelos y el trolling. En creciente, el agua entra a las islas y los bañados, los peces se dispersan para comer en el monte inundado y conviene buscarlos contra las barrancas y en los riachos profundos.',
			'<strong>Orientación por especie</strong> (cambia cada año con el río; confirmalo con tu guía): el <strong>dorado</strong> rinde más de octubre a abril, con aguas templadas, en correderas, bocas de lagunas y bancos de arena; el <strong>surubí</strong> se busca sobre todo de marzo a agosto, en pozones y veriles, con carnada viva, anclado o a la deriva; el <strong>pacú</strong> y la <strong>boga</strong> aparecen en verano, de diciembre a marzo, cerca de camalotales y barrancas con árboles.',
		],
		'temporada'      => [
			[ 'meses' => 'Octubre – abril', 'que' => 'Dorado: más activo con aguas templadas, en correderas, bocas de lagunas y bancos de arena. Señuelos y trolling.' ],
			[ 'meses' => 'Marzo – agosto', 'que' => 'Surubí: pozones y veriles del Paraná, con carnada viva, anclado o a la deriva.' ],
			[ 'meses' => 'Diciembre – marzo', 'que' => 'Pacú y boga: cerca de camalotales y barrancas con árboles.' ],
			[ 'meses' => '28 de abril – 2 de mayo de 2027', 'que' => '50.ª Fiesta Nacional del Surubí. Pico de demanda de alojamiento y transporte: reservá con tiempo.' ],
			[ 'meses' => 'Antes de cada salida', 'que' => 'Mirá la altura del Paraná en Goya en el INA, el pronóstico y las vedas vigentes. Las épocas son orientativas y cambian con el río.' ],
		],
		'reglamento'     => 'Para pescar en Corrientes hace falta licencia de pesca deportiva: se tramita online en el portal de Recursos Naturales, con categorías para pesca de costa y embarcada, y según residencia (Corrientes, resto del país, extranjeros). Llevá el comprobante y tu DNI. Si tenés licencia vigente como residente de Santa Fe, Entre Ríos o Chaco, el acuerdo regional de reconocimiento recíproco te permite pescar respetando las normas de Corrientes; confirmá el alcance antes de viajar. El dorado es Monumento Natural Provincial (Ley 6730) y se pesca con devolución. En el arroyo Ysoró la pesca extractiva está prohibida: solo pesca con devolución (Ley 6655). Para el surubí, la medida mínima de referencia nacional es 85 cm; consultá la vigente en Corrientes. La provincia puede disponer vedas temporales: en septiembre de 2026 dispuso una veda extraordinaria entre los km 1109 y 1150 del Paraná, aguas arriba de Goya (cerca del km 978). Verificá siempre el tramo que vas a pescar.',
		'isoro_nombre'   => 'Sistema Paraná–Isoró',
		'isoro'          => 'Riachos, lagunas, esteros y bañados conectados al Paraná: humedal protegido y zona de reproducción de peces. En el arroyo Ysoró la pesca extractiva está prohibida (Ley 6655): solo con devolución. Conviene ir con guía habilitado.',
		'faqs'           => [
			[ 'pregunta' => '¿Sirve mi licencia de Santa Fe, Entre Ríos o Chaco?', 'respuesta' => 'Si sos residente de una de esas provincias y tu licencia está vigente, el acuerdo regional de reconocimiento recíproco te permite pescar en Corrientes respetando sus normas. Si no, sacá la licencia correntina online en Recursos Naturales. Confirmá el alcance antes de viajar.' ],
			[ 'pregunta' => '¿Me puedo llevar un dorado?', 'respuesta' => 'No. El dorado es Monumento Natural Provincial de Corrientes (Ley 6730) y se pesca con devolución.' ],
		],
	],
	'pt' => [
		'ancla_especies' => 'Para Corrientes em geral',
		'dorado'         => 'O <strong>dourado</strong> é Monumento Natural Provincial de Corrientes (Lei 6730): a pesca é sempre com soltura.',
		'ancla_cal'      => 'calendário orientativo por espécie',
		'calendario'     => [
			'<strong>Como o nível do rio muda a pesca.</strong> Na vazante, os riachos escoam para o canal principal e os peixes se concentram nas bocas dos arroios e nos poços do Paraná: é quando as iscas artificiais e o corrico rendem mais. Na cheia, a água entra nas ilhas e nos banhados, os peixes se espalham para comer na mata alagada e o melhor é procurá-los junto aos barrancos e nos riachos profundos.',
			'<strong>Orientação por espécie</strong> (muda a cada ano com o rio; confirme com o seu guia): o <strong>dourado</strong> rende mais de outubro a abril, com águas mornas, em corredeiras, bocas de lagoas e bancos de areia; o <strong>surubim</strong> é procurado sobretudo de março a agosto, em poços e barrancos, com isca viva, ancorado ou à deriva; o <strong>pacu</strong> e a <strong>piapara</strong> aparecem no verão, de dezembro a março, perto de camalotes e barrancos com árvores.',
		],
		'temporada'      => [
			[ 'meses' => 'Outubro – abril', 'que' => 'Dourado: mais ativo com águas mornas, em corredeiras, bocas de lagoas e bancos de areia. Iscas artificiais e corrico.' ],
			[ 'meses' => 'Março – agosto', 'que' => 'Surubim: poços e barrancos do Paraná, com isca viva, ancorado ou à deriva.' ],
			[ 'meses' => 'Dezembro – março', 'que' => 'Pacu e piapara: perto de camalotes e barrancos com árvores.' ],
			[ 'meses' => '28 de abril – 2 de maio de 2027', 'que' => '50ª Festa Nacional do Surubim. Pico de demanda de hospedagem e transporte: reserve com antecedência.' ],
			[ 'meses' => 'Antes de cada saída', 'que' => 'Confira o nível do Paraná em Goya no INA, a previsão do tempo e os defesos vigentes. As épocas são orientativas e mudam com o rio.' ],
		],
		'reglamento'     => 'Para pescar em Corrientes é preciso ter licença de pesca esportiva: é emitida online no portal de Recursos Naturales, com categorias para pesca de barranco e embarcada, e conforme a residência (Corrientes, resto da Argentina, estrangeiros). Leve o comprovante e o seu documento. Quem tem licença vigente como residente de Santa Fe, Entre Ríos ou Chaco pode pescar em Corrientes pelo acordo regional de reconhecimento recíproco, respeitando as regras locais; confirme o alcance antes de viajar. O dourado é Monumento Natural Provincial (Lei 6730) e a pesca é com soltura. No arroio Ysoró a pesca extrativa é proibida: só pesque e solte (Lei 6655). Para o surubim, o tamanho mínimo de referência nacional é 85 cm; confira o vigente em Corrientes. A província pode decretar defesos temporários: em setembro de 2026 decretou um defeso extraordinário entre os km 1109 e 1150 do Paraná, rio acima de Goya (perto do km 978). Confira sempre o trecho onde vai pescar.',
		'isoro_nombre'   => 'Sistema Paraná–Isoró',
		'isoro'          => 'Riachos, lagoas, banhados e alagados ligados ao Paraná: área úmida protegida e zona de reprodução de peixes. No arroio Ysoró a pesca extrativa é proibida (Lei 6655): só pesque e solte. O ideal é ir com guia habilitado.',
		'faqs'           => [
			[ 'pregunta' => 'Minha licença de Santa Fe, Entre Ríos ou Chaco vale em Corrientes?', 'respuesta' => 'Se você é residente de uma dessas províncias e a licença está vigente, o acordo regional de reconhecimento recíproco permite pescar em Corrientes respeitando as regras locais. Caso contrário, tire a licença de Corrientes online em Recursos Naturales. Confirme o alcance antes de viajar.' ],
			[ 'pregunta' => 'Posso levar um dourado?', 'respuesta' => 'Não. O dourado é Monumento Natural Provincial de Corrientes (Lei 6730) e a pesca é com soltura.' ],
		],
	],
	'en' => [
		'ancla_especies' => 'For Corrientes as a whole',
		'dorado'         => 'Golden <strong>dorado</strong> is a Provincial Natural Monument in Corrientes (Law 6730): it is always catch and release.',
		'ancla_cal'      => 'indicative calendar by species',
		'calendario'     => [
			'<strong>How the river level changes the fishing.</strong> When the river drops, the side channels drain into the main channel and fish gather at creek mouths and in the deep holes of the Paraná: that is when lures and trolling work best. When it rises, water floods the islands and marshes, fish spread out to feed in the flooded forest, and it pays to look for them along the banks and in deep side channels.',
			'<strong>By species</strong> (it shifts every year with the river; check with your guide): <strong>golden dorado</strong> fishes best from October to April in warmer water, in rapids, lagoon mouths and sandbanks; <strong>surubí</strong> is mostly targeted from March to August in deep holes and drop-offs, with live bait, anchored or drifting; <strong>pacú</strong> and <strong>boga</strong> show up in summer, December to March, near floating vegetation and tree-lined banks.',
		],
		'temporada'      => [
			[ 'meses' => 'October – April', 'que' => 'Golden dorado: most active in warmer water, in rapids, lagoon mouths and sandbanks. Lures and trolling.' ],
			[ 'meses' => 'March – August', 'que' => 'Surubí: deep holes and drop-offs of the Paraná, with live bait, anchored or drifting.' ],
			[ 'meses' => 'December – March', 'que' => 'Pacú and boga: near floating vegetation and tree-lined banks.' ],
			[ 'meses' => '28 April – 2 May 2027', 'que' => '50th National Surubí Festival. Peak demand for lodging and transport: book early.' ],
			[ 'meses' => 'Before every trip', 'que' => 'Check the Paraná level at Goya on the INA site, the forecast and current closures. Seasons are indicative and shift with the river.' ],
		],
		'reglamento'     => 'Fishing in Corrientes requires a sport fishing licence, issued online through the provincial Natural Resources portal, with categories for bank and boat fishing and by residence (Corrientes, rest of Argentina, foreign visitors). Carry the receipt and your ID. Residents of Santa Fe, Entre Ríos or Chaco with a valid licence can fish in Corrientes under the regional mutual-recognition agreement, following local rules; check its scope before you travel. Golden dorado is a Provincial Natural Monument (Law 6730) and is catch and release. In the Ysoró creek, harvesting fish is banned: catch and release only (Law 6655). For surubí, the national reference minimum size is 85 cm; check the current Corrientes rule. The province can order temporary closures: in September 2026 it closed the Paraná between km 1109 and 1150, upstream of Goya (around km 978). Always check the stretch you plan to fish.',
		'isoro_nombre'   => 'Paraná–Isoró system',
		'isoro'          => 'Side channels, lagoons, marshes and swamps linked to the Paraná: a protected wetland and fish breeding area. Harvesting fish in the Ysoró creek is banned (Law 6655): catch and release only. Best visited with a licensed guide.',
		'faqs'           => [
			[ 'pregunta' => 'Is my Santa Fe, Entre Ríos or Chaco licence valid?', 'respuesta' => 'If you are a resident of one of those provinces and your licence is valid, the regional mutual-recognition agreement lets you fish in Corrientes under its rules. Otherwise, buy the Corrientes licence online from the Natural Resources office. Check the scope before you travel.' ],
			[ 'pregunta' => 'Can I keep a golden dorado?', 'respuesta' => 'No. Golden dorado is a Provincial Natural Monument in Corrientes (Law 6730) and is catch and release.' ],
		],
	],
];

function pm_goya_parrafo( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}

foreach ( [ 'es', 'pt', 'en' ] as $i ) {
	$p = pm_buscar( 'pm_destino', 'goya', $i );
	if ( ! $p ) {
		WP_CLI::warning( "goya ($i) no existe" );
		continue;
	}
	$x   = $t[ $i ];
	$id  = $p->ID;
	$log = [];

	// Body: calendar note → orientation paragraphs; dorado line after the species paragraph.
	$html = $p->post_content;
	$re_cal = '#<!-- wp:quote -->(?:(?!<!-- /wp:quote -->).)*' . preg_quote( $x['ancla_cal'], '#' ) . '(?:(?!<!-- /wp:quote -->).)*<!-- /wp:quote -->#s';
	if ( preg_match( $re_cal, $html ) ) {
		$html  = preg_replace( $re_cal, implode( "\n\n", array_map( 'pm_goya_parrafo', $x['calendario'] ) ), $html, 1 );
		$log[] = 'calendario en el texto';
	} else {
		WP_CLI::warning( "goya ($i): no encontré la nota del calendario (¿editada?), no toco el texto ahí" );
	}
	if ( ! str_contains( $html, 'Ley 6730' ) && ! str_contains( $html, 'Lei 6730' ) && ! str_contains( $html, 'Law 6730' ) ) {
		$pos = strpos( $html, $x['ancla_especies'] );
		$fin = false !== $pos ? strpos( $html, '<!-- /wp:paragraph -->', $pos ) : false;
		if ( false !== $fin ) {
			$fin  += strlen( '<!-- /wp:paragraph -->' );
			$html  = substr( $html, 0, $fin ) . "\n\n" . pm_goya_parrafo( $x['dorado'] ) . substr( $html, $fin );
			$log[] = 'dorado en el texto';
		} else {
			WP_CLI::warning( "goya ($i): no encontré el párrafo de especies, no agrego la línea del dorado" );
		}
	}
	if ( $html !== $p->post_content ) {
		wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $html ) ] );
	}

	// Fields.
	update_field( 'temporada', $x['temporada'], $id );
	update_field( 'reglamento_resumen', $x['reglamento'], $id );
	$aguas = pm_filas( 'aguas', $id );
	foreach ( $aguas as &$a ) {
		if ( $a['nombre'] === $x['isoro_nombre'] ) {
			$a['nota'] = $x['isoro'];
		}
	}
	unset( $a );
	update_field( 'aguas', $aguas, $id );

	$faqs = pm_filas( 'faqs', $id );
	$ya   = array_column( $faqs, 'pregunta' );
	foreach ( $x['faqs'] as $f ) {
		if ( ! in_array( $f['pregunta'], $ya, true ) ) {
			$faqs[] = $f;
		}
	}
	update_field( 'faqs', $faqs, $id );

	$fuentes = array_map( function ( $f ) {
		$f['consultada'] = str_replace( '-', '', (string) $f['consultada'] );
		$f['oficial']    = empty( $f['oficial'] ) ? 0 : 1;
		return $f;
	}, pm_filas( 'fuentes', $id ) );
	$urls = array_column( $fuentes, 'url' );
	foreach ( $fuentes_nuevas as $f ) {
		if ( ! in_array( $f['url'], $urls, true ) ) {
			$fuentes[] = $f;
		}
	}
	update_field( 'fuentes', $fuentes, $id );
	update_field( 'actualizado', $hoy, $id );

	// Translation bookkeeping: Spanish changed, and pt/en are in step with it.
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );

	$log[] = 'calendario, reglamento, Isoró, preguntas, fuentes';
	WP_CLI::log( "✓ goya ($i) #$id: " . implode( '; ', $log ) );
}

WP_CLI::success( 'Goya actualizada en los tres idiomas.' );
