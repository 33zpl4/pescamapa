<?php
/**
 * Paso de la Patria (Corrientes), 21/10/2026: third destination guide, in
 * Spanish and Portuguese, published at the owner's request ("you can publish
 * now"). Built from docs/investigacion/paso-de-la-patria.md. The size and quota
 * rules are the Anexo I of Disposición 853/22 (Boletín Oficial de Corrientes,
 * 26/12/2022), with its footnotes, transcribed from the official text by the owner. "Reglamento
 * verificado" stays empty: nobody confirmed that no later act changed it.
 * Anything unconfirmed is a hidden PENDIENTE note. Outfitters are loaded as
 * "listado con información pública" (docs/prestadores.md).
 *
 * Idempotent: a guide or outfitter that exists is never touched.
 * Run with wp-script.yml (script = paso-de-la-patria-2026-10-21).
 */

$hoy = '20261021';

function pm_pdp_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_pdp_h( string $texto ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$texto}</h2>\n<!-- /wp:heading -->";
}
function pm_pdp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_pdp_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$fuentes = [
	[ 'Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)', 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf', 1 ],
	[ 'Recursos Naturales de Corrientes', 'https://www.recnaturalesctes.gob.ar/', 1 ],
	[ 'Licencias de pesca de Recursos Naturales (trámite online)', 'https://licenciasrecursosnaturales.gob.ar', 1 ],
	[ 'Ministerio de Turismo de Corrientes: acuerdo regional de licencias (alcance)', 'https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/', 1 ],
	[ 'Ministerio de Turismo de Corrientes: Paso de la Patria', 'https://corrientes.tur.ar/destinos/paso-de-la-patria/', 1 ],
	[ 'Ministerio de Turismo de Corrientes: cómo llegar por tierra', 'https://corrientes.tur.ar/como-llegar-por-tierra/', 1 ],
	[ 'Argentina.gob.ar: pesca en el Paraná', 'https://www.argentina.gob.ar/node/509695', 1 ],
	[ 'Municipalidad de Paso de la Patria: normativa y controles de navegación', 'https://www.pasodelapatria.gob.ar/?q=taxonomy%2Fterm%2F745', 1 ],
	[ 'Municipalidad de Paso de la Patria: 61.ª Fiesta Nacional del Dorado', 'https://pasodelapatria.gob.ar/noticias/fiesta-nacional-del-dorado-un-exito-total', 1 ],
	[ 'Municipalidad de Paso de la Patria: Torneo Apertura 2026', 'https://www.pasodelapatria.gob.ar/noticias/apertura-de-la-temporada-de-pesca-del-dorado', 1 ],
	[ 'INA: altura del Paraná en Paso de la Patria', 'https://alerta.ina.gov.ar/a5/secciones?seriesId=18', 1 ],
	[ 'Argentina.gob.ar: paso internacional Paso de la Patria', 'https://www.argentina.gob.ar/seguridad/pasosinternacionales/detalle/rio/76/Puerto-Paso-de-la-Patria-Paso-de-la-Patria', 1 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$comun = [
	'clave'      => 'paso-de-la-patria',
	'autor'      => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'fuentes'    => $fuentes_filas,
	'reglamento_autoridad' => 'Dirección de Recursos Naturales de Corrientes',
	'reglamento_url'       => 'https://www.recnaturalesctes.gob.ar/',
];

$guias = [];

// --- Español ---------------------------------------------------------------
$guias['es'] = [
	'titulo' => 'Paso de la Patria',
	'slug'   => 'pesca-en-paso-de-la-patria',
	'cuerpo' => implode( "\n\n", [
		pm_pdp_p( 'Paso de la Patria está sobre el <strong>Paraná</strong>, cerca de donde se junta con el río Paraguay y a unos 36 km de la ciudad de Corrientes. Para el turismo provincial y nacional el <strong>dorado</strong> es el emblema del destino; también se buscan surubí, pacú y boga.' ),
		pm_pdp_h( 'Qué se puede llevar: las reglas básicas de Corrientes' ),
		pm_pdp_p( 'La <strong>Disposición 853/22</strong> de la Dirección de Recursos Naturales (Boletín Oficial de Corrientes, 26 de diciembre de 2022) rige hasta que otro acto la modifique. Para las especies más buscadas dice:' ),
		pm_pdp_lista( [
			'<strong>Dorado y surubí</strong> (manchado y atigrado): solo pesca y devolución. No se retienen.',
			'<strong>Pacú</strong>: talla mínima 45 cm; cupo de 1 por licencia vigente y un solo ejemplar por embarcación, sin importar cuántos pescadores haya a bordo.',
			'<strong>Patí</strong>: talla mínima 70 cm; cupo de 2.',
			'<strong>Boga</strong>: talla mínima 45 cm; cupo de 2 por pescador con licencia vigente y hasta 6 por embarcación (si hay más pescadores a bordo, se reparten esas 6).',
			'<strong>Tararira</strong>: talla mínima 30 cm; cupo de 5.',
		] ),
		pm_pdp_p( 'La misma disposición fija estas otras medidas mínimas y cupos: armado 45 cm (cupo 5); bagre amarillo 25 cm (5); corvina 25 cm (5); mandure 40 cm (5); moncholo 30 cm (5); palometa 20 cm (10); raya 80 cm de diámetro (1); sábalo 40 cm (3); salmón 45 cm (2); virreyna 30 cm (5).' ),
		pm_pdp_p( 'Las especies del anexo solo pueden movilizarse dentro de la localidad donde se pescaron: está prohibido llevarlas a otro destino, nacional o internacional. Antes de salir confirmá también que no haya una veda extraordinaria en el tramo.' ),
		pm_pdp_h( 'Licencia de pesca' ),
		pm_pdp_p( 'La licencia se saca por internet en el portal de Recursos Naturales de Corrientes y se lleva junto con el DNI. El acuerdo regional de reconocimiento de licencias (Santa Fe, Entre Ríos y Chaco) es para residentes de esas provincias y no cubre los tramos de jurisdicción exclusivamente correntina. No encontramos una norma que reconozca licencias de otros países: quien viene de afuera tiene que sacar la de Corrientes.' ),
		pm_pdp_pendiente( 'precio y duración de cada categoría y reglas para extranjeros: copiarlos del portal con fecha.' ),
		pm_pdp_h( 'Vedas' ),
		pm_pdp_p( 'La provincia puede disponer vedas extraordinarias por tramo del Paraná. En 2026 hubo vedas de ese tipo en tramos cercanos a Paso de la Patria, por concentración de dorados y surubíes. Cambian cada año: antes de salir, consultá las vigentes con Recursos Naturales.' ),
		pm_pdp_h( 'Torneos' ),
		pm_pdp_p( 'La <strong>Fiesta Nacional del Dorado</strong> la organiza la Municipalidad: en 2026 fue su 61.ª edición, del 14 al 17 de agosto, con devolución. El <strong>Torneo Apertura</strong> de la temporada del dorado se hizo del 2 al 5 de abril, también con devolución. Las fechas de las próximas ediciones todavía no están publicadas.' ),
		pm_pdp_h( 'Guías, lodges y posadas' ),
		pm_pdp_p( 'Hay guías, posadas y lodges con pesca embarcada. Abajo figuran los que encontramos con información pública; todavía no los verificamos con cada uno, y por eso están marcados.' ),
		pm_pdp_h( 'Cómo llegar' ),
		pm_pdp_p( 'Se llega por la <strong>ruta provincial 9</strong>, con acceso desde la ruta nacional 12. Según el Ministerio de Turismo de Corrientes, está a unos 36 km de la ciudad de Corrientes y a unos 20 km de Resistencia; de Posadas, a unos 309 km. Desde Corrientes hay minibús. El paso internacional de Paso de la Patria es una lancha de pasajeros a Paso de Patria, en Paraguay: no hay paso terrestre directo desde Brasil.' ),
		pm_pdp_pendiente( 'calendario por especie y mes, rangos de altura del río, equipo por especie, costas habilitadas para pescar, rampas y combustible, y cómo entrar desde Brasil con lancha propia.' ),
	] ),
	'campos' => [
		'h1'          => 'Pesca deportiva en Paso de la Patria, Corrientes: dorado, reglas y licencia',
		'bajada'      => 'El dorado es el emblema de Paso de la Patria, sobre el Paraná y cerca de la confluencia con el Paraguay. Qué podés llevarte, qué licencia hace falta y cuándo son los torneos.',
		'modalidades' => "Embarcada con guía\nBaitcasting y spinning\nTrolling\nMosca (flycast)\nCon carnada",
		'epoca'       => 'Depende de la especie, de la altura del río y de las vedas vigentes.',
		'seo_title'   => 'Pesca en Paso de la Patria: dorado, tallas, cupos y licencia',
		'seo_description' => 'Guía de pesca en Paso de la Patria, Corrientes: dorado y surubí en el Paraná, tallas y cupos vigentes, licencia, fiestas del dorado y cómo llegar.',
		'temporada'   => [
			[ 'meses' => 'Abril', 'que' => 'Torneo Apertura de la temporada del dorado (en 2026, del 2 al 5 de abril, con devolución).' ],
			[ 'meses' => 'Agosto', 'que' => 'Fiesta Nacional del Dorado (61.ª edición en 2026, del 14 al 17 de agosto, con devolución). Pico de demanda de alojamiento: reservá con tiempo.' ],
			[ 'meses' => 'Calendario por especie', 'que' => 'PENDIENTE: confirmar con una fuente técnica o con guías locales.' ],
			[ 'meses' => 'Antes de cada salida', 'que' => 'Mirá la altura del Paraná en la estación de Paso de la Patria (INA), el pronóstico y las vedas vigentes.' ],
		],
		'reglamento_resumen' => 'Para pescar en Corrientes hace falta licencia de pesca deportiva, que se tramita online en el portal de Recursos Naturales. La Disposición 853/22 (Boletín Oficial, 26/12/2022, vigente hasta nuevo acto) fija que dorado y surubí son solo pesca y devolución y que pacú (45 cm, cupo 1 por licencia y un solo ejemplar por embarcación), patí (70 cm, cupo 2), boga (45 cm, cupo 2 por pescador y hasta 6 por embarcación) y tararira (30 cm, cupo 5) tienen talla mínima y cupo. Lo retenido solo puede movilizarse dentro de la localidad donde se pescó. La provincia puede disponer vedas extraordinarias por tramo; cambian cada año, así que antes de salir consultá las vigentes con Recursos Naturales.',
		'aguas' => [
			[ 'nombre' => 'Río Paraná frente a Paso de la Patria', 'nota' => 'Cauce principal. La altura se consulta en la estación de Paso de la Patria del INA.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Confluencia Paraná–Paraguay', 'nota' => 'Rasgo geográfico de la zona. Consultá las vedas vigentes antes de pescar acá.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => '¿Qué licencia hace falta para pescar en Paso de la Patria?', 'respuesta' => 'La licencia de pesca deportiva de Corrientes. Se saca por internet en el portal de Recursos Naturales y se lleva con el DNI. El precio y la duración dependen de la categoría y se ven al tramitarla.' ],
			[ 'pregunta' => '¿Me sirve la licencia de otra provincia o de otro país?', 'respuesta' => 'El acuerdo regional de reconocimiento (Santa Fe, Entre Ríos y Chaco) es para residentes de esas provincias y no cubre los tramos de jurisdicción exclusivamente correntina. No encontramos una norma que reconozca licencias de otros países: sacá la de Corrientes.' ],
			[ 'pregunta' => '¿Qué tallas y cupos rigen en Paso de la Patria?', 'respuesta' => 'Los de la Disposición 853/22 de Recursos Naturales (Boletín Oficial, 26/12/2022, vigente hasta que otro acto la modifique): dorado y surubí, solo pesca y devolución; pacú, 45 cm y cupo de 1 por licencia y un solo ejemplar por embarcación; patí, 70 cm y cupo de 2; boga, 45 cm y cupo de 2 por pescador (hasta 6 por embarcación); tararira, 30 cm y cupo de 5. El anexo incluye también otras especies, como armado, mandure, palometa o sábalo.' ],
			[ 'pregunta' => '¿Me puedo llevar el pescado a otra ciudad?', 'respuesta' => 'No. Según la Disposición 853/22, las especies del anexo solo pueden movilizarse dentro de la localidad donde se pescaron: está prohibido llevarlas a otro destino, nacional o internacional.' ],
			[ 'pregunta' => '¿Hay vedas?', 'respuesta' => 'Sí, pueden existir. La provincia dispone vedas extraordinarias por tramo del Paraná y en 2026 hubo en zonas cercanas a Paso de la Patria. Cambian cada año: consultá las vigentes con Recursos Naturales antes de salir.' ],
			[ 'pregunta' => '¿Cuándo es la Fiesta Nacional del Dorado?', 'respuesta' => 'En 2026 fue la 61.ª edición, del 14 al 17 de agosto, con devolución y organizada por la Municipalidad. La fecha de la próxima todavía no está publicada.' ],
			[ 'pregunta' => '¿Cuándo es el Torneo Apertura?', 'respuesta' => 'En 2026 se hizo del 2 al 5 de abril, con devolución. La fecha del próximo todavía no está publicada.' ],
			[ 'pregunta' => '¿Cómo llego desde Corrientes o Resistencia?', 'respuesta' => 'Por la ruta provincial 9, con acceso desde la ruta nacional 12. Según el Ministerio de Turismo de Corrientes son unos 36 km desde la ciudad de Corrientes y unos 20 km desde Resistencia. Desde Corrientes hay minibús.' ],
			[ 'pregunta' => '¿Se puede llegar desde Brasil?', 'respuesta' => 'El paso internacional de Paso de la Patria es una lancha de pasajeros hacia Paso de Patria, en Paraguay: no hay paso terrestre directo desde Brasil.' ],
			[ 'pregunta' => '¿Dónde veo la altura del río?', 'respuesta' => 'En la estación Paso de la Patria del sistema de alerta hidrológico del INA, que muestra la altura del Paraná.' ],
		],
	],
];

// --- Português (BR) ----------------------------------------------------------
$guias['pt'] = [
	'titulo' => 'Paso de la Patria',
	'slug'   => 'pesca-em-paso-de-la-patria',
	'cuerpo' => implode( "\n\n", [
		pm_pdp_p( 'Paso de la Patria fica no <strong>rio Paraná</strong>, perto de onde ele se junta ao rio Paraguai e a cerca de 36 km da cidade de Corrientes. Para o turismo provincial e nacional, o <strong>dourado</strong> é o emblema do destino; também se pescam surubim, pacu e boga.' ),
		pm_pdp_h( 'O que pode levar: as regras básicas de Corrientes' ),
		pm_pdp_p( 'A <strong>Disposição 853/22</strong> da Direção de Recursos Naturais (Diário Oficial de Corrientes, 26 de dezembro de 2022) vale até que outro ato a modifique. Para as espécies mais procuradas, diz:' ),
		pm_pdp_lista( [
			'<strong>Dourado e surubim</strong> (pintado e cachara): somente pesca e devolução. Não podem ser retidos.',
			'<strong>Pacu</strong>: tamanho mínimo de 45 cm; limite de 1 por licença válida e um único exemplar por embarcação, independentemente de quantos pescadores estejam a bordo.',
			'<strong>Patí</strong>: tamanho mínimo de 70 cm; limite de 2.',
			'<strong>Boga</strong>: tamanho mínimo de 45 cm; limite de 2 por pescador com licença válida e até 6 por embarcação (se houver mais pescadores a bordo, esses 6 são divididos entre eles).',
			'<strong>Traíra</strong>: tamanho mínimo de 30 cm; limite de 5.',
		] ),
		pm_pdp_p( 'A mesma disposição fixa estes outros tamanhos mínimos e limites (nomes como no texto oficial): armado 45 cm (limite 5); bagre amarillo 25 cm (5); corvina 25 cm (5); mandure 40 cm (5); moncholo 30 cm (5); palometa 20 cm (10); raya 80 cm de diâmetro (1); sábalo 40 cm (3); salmón 45 cm (2); virreyna 30 cm (5).' ),
		pm_pdp_p( 'As espécies do anexo só podem circular dentro da localidade onde foram pescadas: é proibido levá-las para outro destino, nacional ou internacional. Antes de sair, confirme também se não há uma proibição extraordinária no trecho.' ),
		pm_pdp_h( 'Licença de pesca' ),
		pm_pdp_p( 'A licença é tirada pela internet no portal de Recursos Naturais de Corrientes e levada junto com o documento de identidade. O acordo regional de reconhecimento de licenças (Santa Fe, Entre Ríos e Chaco) vale para residentes dessas províncias e não cobre os trechos de jurisdição exclusivamente de Corrientes. Não encontramos uma norma que reconheça licenças de outros países: quem vem de fora precisa tirar a de Corrientes.' ),
		pm_pdp_h( 'Proibições (vedas)' ),
		pm_pdp_p( 'A província pode decretar proibições extraordinárias por trecho do Paraná. Em 2026 houve proibições desse tipo em trechos próximos a Paso de la Patria, por concentração de dourados e surubins. Mudam todo ano: antes de sair, consulte as vigentes com Recursos Naturais.' ),
		pm_pdp_h( 'Torneios' ),
		pm_pdp_p( 'A <strong>Festa Nacional do Dourado</strong> é organizada pela Prefeitura: em 2026 foi a 61.ª edição, de 14 a 17 de agosto, com devolução. O <strong>Torneio de Abertura</strong> da temporada do dourado foi de 2 a 5 de abril, também com devolução. As datas das próximas edições ainda não foram publicadas.' ),
		pm_pdp_h( 'Guias, lodges e pousadas' ),
		pm_pdp_p( 'Há guias, pousadas e lodges com pesca embarcada. Abaixo estão os que encontramos com informação pública; ainda não os verificamos com cada um, por isso estão marcados.' ),
		pm_pdp_h( 'Como chegar' ),
		pm_pdp_p( 'O acesso é pela <strong>rota provincial 9</strong>, a partir da rota nacional 12. Segundo o Ministério do Turismo de Corrientes, fica a cerca de 36 km da cidade de Corrientes e a cerca de 20 km de Resistencia; de Posadas, a cerca de 309 km. De Corrientes há minibus. A passagem internacional de Paso de la Patria é uma lancha de passageiros para Paso de Patria, no Paraguai: não há passagem terrestre direta a partir do Brasil.' ),
	] ),
	'campos' => [
		'h1'          => 'Pesca esportiva em Paso de la Patria, Corrientes: dourado, regras e licença',
		'bajada'      => 'O dourado é o emblema de Paso de la Patria, no Paraná e perto da confluência com o Paraguai. O que pode levar, que licença é preciso e quando são os torneios.',
		'modalidades' => "Embarcada com guia\nBaitcasting e spinning\nCorrico (trolling)\nFly (flycast)\nCom isca",
		'epoca'       => 'Depende da espécie, da altura do rio e das proibições vigentes.',
		'seo_title'   => 'Pesca em Paso de la Patria: dourado, tamanhos, limites e licença',
		'seo_description' => 'Guia de pesca em Paso de la Patria, Corrientes: dourado e surubim no Paraná, tamanhos e limites vigentes, licença, festas do dourado e como chegar.',
		'temporada'   => [
			[ 'meses' => 'Abril', 'que' => 'Torneio de Abertura da temporada do dourado (em 2026, de 2 a 5 de abril, com devolução).' ],
			[ 'meses' => 'Agosto', 'que' => 'Festa Nacional do Dourado (61.ª edição em 2026, de 14 a 17 de agosto, com devolução). Pico de procura por hospedagem: reserve com antecedência.' ],
			[ 'meses' => 'Antes de cada saída', 'que' => 'Veja a altura do Paraná na estação de Paso de la Patria (INA), a previsão do tempo e as proibições vigentes.' ],
		],
		'reglamento_resumen' => 'Para pescar em Corrientes é preciso licença de pesca esportiva, tirada online no portal de Recursos Naturais. A Disposição 853/22 (Diário Oficial, 26/12/2022, vigente até novo ato) fixa que dourado e surubim são somente pesca e devolução e que pacu (45 cm, limite 1 por licença e um único exemplar por embarcação), patí (70 cm, limite 2), boga (45 cm, limite 2 por pescador e até 6 por embarcação) e traíra (30 cm, limite 5) têm tamanho mínimo e limite. O que for retido só pode circular dentro da localidade onde foi pescado. A província pode decretar proibições extraordinárias por trecho; mudam todo ano, então antes de sair consulte as vigentes com Recursos Naturais.',
		'aguas' => [
			[ 'nombre' => 'Rio Paraná em frente a Paso de la Patria', 'nota' => 'Leito principal. A altura é consultada na estação de Paso de la Patria do INA.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Confluência Paraná–Paraguai', 'nota' => 'Característica geográfica da região. Consulte as proibições vigentes antes de pescar aqui.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Que licença é preciso para pescar em Paso de la Patria?', 'respuesta' => 'A licença de pesca esportiva de Corrientes. É tirada pela internet no portal de Recursos Naturais e levada com o documento de identidade. O preço e a validade dependem da categoria e aparecem ao tirá-la.' ],
			[ 'pregunta' => 'Serve a licença de outra província ou de outro país?', 'respuesta' => 'O acordo regional de reconhecimento (Santa Fe, Entre Ríos e Chaco) vale para residentes dessas províncias e não cobre os trechos de jurisdição exclusivamente de Corrientes. Não encontramos uma norma que reconheça licenças de outros países: tire a de Corrientes.' ],
			[ 'pregunta' => 'Que tamanhos e limites valem em Paso de la Patria?', 'respuesta' => 'Os da Disposição 853/22 de Recursos Naturais (Diário Oficial, 26/12/2022, vigente até que outro ato a modifique): dourado e surubim, somente pesca e devolução; pacu, 45 cm e limite de 1 por licença e um único exemplar por embarcação; patí, 70 cm e limite de 2; boga, 45 cm e limite de 2 por pescador (até 6 por embarcação); traíra, 30 cm e limite de 5. O anexo inclui também outras espécies, como armado, mandure, palometa ou sábalo.' ],
			[ 'pregunta' => 'Posso levar o peixe para outra cidade?', 'respuesta' => 'Não. Segundo a Disposição 853/22, as espécies do anexo só podem circular dentro da localidade onde foram pescadas: é proibido levá-las para outro destino, nacional ou internacional.' ],
			[ 'pregunta' => 'Quando é a Festa Nacional do Dourado?', 'respuesta' => 'Em 2026 foi a 61.ª edição, de 14 a 17 de agosto, com devolução e organizada pela Prefeitura. A data da próxima ainda não foi publicada.' ],
			[ 'pregunta' => 'Dá para chegar do Brasil?', 'respuesta' => 'A passagem internacional de Paso de la Patria é uma lancha de passageiros para Paso de Patria, no Paraguai: não há passagem terrestre direta a partir do Brasil.' ],
			[ 'pregunta' => 'Onde vejo a altura do rio?', 'respuesta' => 'Na estação Paso de la Patria do sistema de alerta hidrológico do INA, que mostra a altura do Paraná.' ],
		],
	],
];

// --- Create ---------------------------------------------------------------------
$es_id = 0;
foreach ( [ 'es', 'pt' ] as $i ) {
	$g = $guias[ $i ];
	$ya = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'paso-de-la-patria', 'numberposts' => -1 ] );
	$existe = array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $i );
	if ( $existe ) {
		WP_CLI::log( "paso-de-la-patria ($i) ya existe: no toco nada." );
		if ( 'es' === $i ) {
			$es_id = reset( $existe )->ID;
		}
		continue;
	}
	$id = wp_insert_post( [
		'post_type'    => 'pm_destino',
		'post_status'  => 'publish',
		'post_title'   => $g['titulo'],
		'post_name'    => $g['slug'],
		'post_content' => wp_slash( $g['cuerpo'] ),
	], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i ];
	if ( 'es' === $i ) {
		$campos += [
			'region' => 'litoral', 'provincia' => 'corrientes', 'lat' => -27.32, 'lon' => -58.57,
			'especies' => [ 'dorado', 'surubi' ],
			'alturas_url' => 'https://alerta.ina.gov.ar/a5/secciones?seriesId=18',
		];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	if ( 'es' === $i ) {
		$es_id = $id;
		$proximo = get_page_by_path( 'paso-de-la-patria', OBJECT, 'pm_proximo' );
		if ( $proximo && get_post_thumbnail_id( $proximo->ID ) ) {
			set_post_thumbnail( $id, get_post_thumbnail_id( $proximo->ID ) );
			WP_CLI::log( 'foto: la de la tarjeta "próximamente"' );
		} else {
			WP_CLI::warning( 'sin foto: cargala desde el panel (Imagen destacada)' );
		}
	}
	WP_CLI::log( "✓ paso-de-la-patria ($i) publicada #$id: " . get_permalink( $id ) );
}

// --- Outfitters: "listado con información pública" ---------------------------------
$prestadores = [
	[ 'FansFishing', [ 'operador' ], 'Salidas de pesca guiadas y blog sobre la pesca de la zona.', [], 'https://fansfishing.com/', '', '' ],
	[ 'Tatá Lodge', [ 'alojamiento' ], 'Lodge de pesca.', [], 'https://www.tatafishinglodge.com/', '', '' ],
	[ 'Posada del Paso', [ 'alojamiento' ], 'Posada con servicio de pesca embarcada propio.', [], 'https://www.posadadelpaso.com/pesca/', 'Lancha de 6,40 m', 'Motor de 90 hp' ],
	[ 'Del Paso Fishing', [ 'guia' ], 'Guía de pesca con lancha; ofrece también paquetes con alojamiento.', [ 'carnada', 'trolling', 'mosca', 'spinning' ], 'https://pescaconguia.com.ar/guias/del-paso-fishing', '', '' ],
	[ 'Pousada La Pesca', [ 'operador' ], 'Pousada brasileña con paquetes de pesca y traslado en Paso de la Patria (en portugués).', [], 'https://www.pousadalapesca.com.br/tarifario', '', '' ],
];
foreach ( $prestadores as [ $nombre, $tipo, $desc, $modalidades, $fuente, $lancha, $motor ] ) {
	$hay = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( $hay ) {
		WP_CLI::log( "· prestador $nombre ya existe" );
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::warning( $pid->get_error_message() );
		continue;
	}
	$f = [
		'tipo' => $tipo, 'prest_destino' => 'paso-de-la-patria', 'localidad' => 'Paso de la Patria', 'descripcion' => $desc,
		'prest_modalidades' => $modalidades, 'lancha' => $lancha, 'motor' => $motor,
		'prest_publico' => 1, 'prest_fuente' => $fuente, 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0,
		'notas' => 'Listado con información pública (informe de investigación del 21/10/2026). Los datos salen de lo que el agente reportó de su sitio; Pescamapa no abrió el enlace. Falta contactarlo.',
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	WP_CLI::log( "✓ prestador $nombre #$pid (listado público, sin verificar)" );
}

flush_rewrite_rules( false );
WP_CLI::success( 'Paso de la Patria lista.' );
