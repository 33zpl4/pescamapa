<?php
/**
 * Esquina (Corrientes), 28/10/2026: Portuguese (BR) and English versions of the Spanish guide, so the
 * "Paquetes de pesca en Esquina" page can exist in all three languages (it hangs off the guide of its own language).
 * A translation adds or changes NO data: same figures, same sources, same PENDIENTE notes (hidden on the site) as the
 * Spanish guide as written by esquina-2026-10-20 + esquina-reglamento-2026-10-20 (regulation verified 20/10/2026).
 * Run with wp-script.yml (script = esquina-traducciones-2026-10-28).
 *
 * Dry run was clean (29/10/2026: pt and en, 9 questions and 8 sources each); applied. Idempotent: a language that already exists is never touched. Shared fields (region, province, location, species,
 * photo) are inherited from the Spanish guide. Dry run first.
 */

$aplicar = true;
$hoy     = '20261020';

function pm_et_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_et_h( string $texto, int $nivel = 2 ): string {
	$attr = 2 === $nivel ? '' : ' {"level":' . $nivel . '}';
	return "<!-- wp:heading{$attr} -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->";
}
function pm_et_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$guias = [];

// --- Português (BR) -----------------------------------------------------------
$guias['pt'] = [
	'slug'   => 'pesca-em-esquina',
	'cuerpo' => implode( "\n\n", [
		pm_et_p( 'Esquina fica no sul de Corrientes, onde o <strong>rio Corriente</strong> deságua no <strong>Paraná</strong>. Esse encontro forma um delta de ilhas, riachos, lagoas e banhados, e é o motivo de se vir pescar aqui: as águas grandes do Paraná de um lado, as águas mais calmas e claras do Corriente do outro.' ),
		pm_et_h( 'O delta de Esquina: zonas de pesca e ambientes' ),
		pm_et_h( 'O rio Corriente e seus riachos', 3 ),
		pm_et_p( 'Água mais clara e mais lenta que a do Paraná. É o ambiente que os lodges escolhem para o <strong>dourado na mosca</strong>, e nos banhados pequenos também aparecem dourados grandes e traíras.' ),
		pm_et_pendiente( 'confirmar com um guia local o canal Torello, nomes de riachos e lagoas, bancos de areia e se se usam iscas artificiais leves no Corriente.' ),
		pm_et_h( 'O leito principal do Paraná', 3 ),
		pm_et_p( 'Em frente a Esquina o Paraná é rio de lancha: <strong>corrico (trolling)</strong> e pesca embarcada com guia para <strong>surubim</strong> e dourado. Nos últimos anos a imprensa regional noticiou surubins de mais de 40 quilos pescados (e devolvidos) nesta zona.' ),
		pm_et_pendiente( 'poços, corredeiras e pesca ancorada em Esquina: validar com guia local.' ),
		pm_et_h( 'Espécies-alvo e modalidades' ),
		pm_et_p( 'As estrelas são o <strong>dourado</strong> e o <strong>surubim</strong>; também se pescam o <strong>pati</strong> e o <strong>pacu</strong>, a espécie que dá nome à festa da cidade. Pesca-se embarcado, com spinning, corrico e mosca. O <strong>dourado</strong> é Monumento Natural Provincial de Corrientes (Lei 6730): pesca-se com devolução.' ),
		pm_et_p( '<strong>Como muda com a altura do rio.</strong> Como em todo o Paraná correntino, na vazante os peixes se concentram nas bocas de arroios e nos poços, e rendem as iscas artificiais e o corrico; na cheia a água entra em ilhas e banhados, os peixes se dispersam e convém procurá-los junto às barrancas e nos riachos fundos. Antes de reservar, veja a altura do rio.' ),
		pm_et_pendiente( 'boga, baitcasting e técnica por espécie em Esquina: confirmar com guia local.' ),
		pm_et_h( 'Quando ir: calendário real, não "o ano todo"' ),
		pm_et_p( 'Haver pesca com devolução aberta não significa que todas as espécies rendam igual o ano inteiro. As épocas do calendário são uma referência regional: quem as define é o rio (cheias e vazantes), a temperatura da água e as proibições (vedas) que a província determinar. Pergunte sempre ao seu guia o que rende na sua data.' ),
		pm_et_h( 'Guias de pesca, cabanas e pousadas' ),
		pm_et_p( 'Há lodges com pacotes de vários dias (sobretudo de pesca com mosca, com pensão completa, guia e lancha) e guias locais que oferecem saídas de lancha. Uma saída típica inclui lancha, combustível, guia e isca; alguns emprestam o equipamento. Peça sempre por escrito o que está incluído e o que não está.' ),
		pm_et_pendiente( 'preços 2026 de uma saída com guia independente; tracker e almoço na ilha.' ),
		pm_et_h( 'Como chegar, o que levar e o que fazer além de pescar' ),
		pm_et_p( 'Esquina fica a cerca de <strong>600 km de Buenos Aires</strong> e a cerca de <strong>330 km da cidade de Corrientes</strong>, com ônibus diretos desde a capital provincial. Para dourado na mosca, os lodges recomendam varas de 9 pés e meio, linha 7 ou 8 (flutuante, intermediária e de afundamento rápido), moscas tipo baitfish e poppers, e líder de aço. Para spinning e corrico, consulte o equipamento com o seu guia.' ),
		pm_et_p( 'Para quem acompanha: praias no rio Corriente, turismo rural em estâncias, fotografia e observação de aves. Em fevereiro, a <strong>Festa Nacional do Pacu</strong> lota a cidade: em 2026 foi de 8 a 15 de fevereiro (39.ª edição).' ),
		pm_et_pendiente( 'distâncias e rotas desde Rosário, Córdoba e a fronteira com o Brasil; data da Festa do Pacu 2027; site oficial do município.' ),
	] ),
	'campos' => [
		'h1'          => 'Pesca esportiva em Esquina, Corrientes: guia completo de espécies, temporadas e serviços',
		'bajada'      => 'Onde o rio Corriente se abre para o Paraná há ilhas, riachos e banhados. Dourado, surubim e pacu com devolução, e como ler o rio antes de ir.',
		'modalidades' => "Embarcada com guia\nSpinning\nCorrico (trolling)\nMosca",
		'epoca'       => 'Dourado de outubro a abril e surubim de março a agosto, como referência; tudo depende da altura do rio.',
		'seo_title'   => 'Pesca em Esquina, Corrientes: dourado, surubim e temporadas',
		'seo_description' => 'Guia de pesca em Esquina, Corrientes: dourado e surubim no Paraná e no rio Corriente, quando ir, licença, proibições, guias e a Festa do Pacu.',
		'temporada'   => [
			[ 'meses' => 'Outubro – abril', 'que' => 'Dourado: mais ativo com águas mornas. Mosca no rio Corriente e seus riachos; iscas artificiais e corrico no Paraná. Sempre com devolução.' ],
			[ 'meses' => 'Março – agosto', 'que' => 'Surubim: referência regional, em poços e barrancos do Paraná. Exemplares grandes são relatados também fora desses meses.' ],
			[ 'meses' => 'Dezembro – março', 'que' => 'Pacu: temporada de verão, perto de camalotes e barrancas com árvores.' ],
			[ 'meses' => 'Fevereiro', 'que' => 'Festa Nacional do Pacu (em 2026, de 8 a 15). Pico de procura por hospedagem: reserve com antecedência.' ],
			[ 'meses' => 'Antes de cada saída', 'que' => 'Veja a altura do Paraná, a previsão do tempo e as proibições vigentes. As épocas são orientativas e mudam com o rio.' ],
		],
		'reglamento_autoridad' => 'Dirección de Recursos Naturales de Corrientes',
		'reglamento_resumen' => 'Para pescar em Corrientes é preciso licença de pesca esportiva. Ela é tirada online no portal de Recursos Naturais: você preenche o formulário com o seu documento (DNI), paga por meio digital e recebe a credencial com código QR, que pode mostrar no celular junto com o documento. Há categorias de costa e embarcada, e conforme a residência (correntinos, resto do país, estrangeiros); aposentados, pensionistas e menores de 12 anos tramitam uma isenção sem custo. No rio Corriente e nos riachos do seu delta só se permite a pesca esportiva com vara ou linha de mão: nada de redes nem espinhéis. O dourado é Monumento Natural Provincial (Lei 6730) e pesca-se com devolução. O pacu tem medida mínima de 45 cm, e os operadores locais recomendam devolvê-lo em arroios e estuários. As proibições de desova (ou o regime de dias fixos por semana) são fixadas a cada ano por Recursos Naturais: se for viajar entre novembro e janeiro, consulte a disposição vigente antes de sair.',
		'aguas' => [
			[ 'nombre' => 'Rio Paraná em frente a Esquina', 'nota' => 'Leito principal: pesca embarcada com guia, corrico e spinning para surubim e dourado.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Rio Corriente e seu delta', 'nota' => 'Águas mais claras e lentas, riachos e banhados: o ambiente do dourado na mosca.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Ilhas e riachos do Paraná', 'nota' => 'Riachos, lagoas e banhados entre ilhas. Convém ir com guia.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'O que se pesca em Esquina?', 'respuesta' => 'Sobretudo dourado e surubim; também pati e pacu, e traíras nos banhados. É a oferta típica, não uma garantia de captura: depende do rio e da época.' ],
			[ 'pregunta' => 'É preciso licença?', 'respuesta' => 'Sim. Tira-se online no portal de Recursos Naturais de Corrientes com o seu documento (DNI); você paga por meio digital e recebe uma credencial com QR que vale no celular (leve também o documento). Há categorias de costa e embarcada, para correntinos, resto do país e estrangeiros. Aposentados, pensionistas e menores de 12 anos tramitam uma isenção sem custo. Se você é de Santa Fe, Entre Ríos ou Chaco, veja o acordo regional de reconhecimento recíproco.' ],
			[ 'pregunta' => 'Posso levar um dourado?', 'respuesta' => 'Não. O dourado é Monumento Natural Provincial de Corrientes (Lei 6730) e pesca-se com devolução.' ],
			[ 'pregunta' => 'Quando é a Festa Nacional do Pacu?', 'respuesta' => 'Em fevereiro. Em 2026 foi de 8 a 15 de fevereiro (39.ª edição). É o momento de maior procura por hospedagem em Esquina.' ],
			[ 'pregunta' => 'Quais datas abrange a veda em Corrientes?', 'respuesta' => 'Mudam a cada ano: Recursos Naturais fixa as proibições de desova (ou um regime de dias fixos por semana) conforme os relatórios de cada primavera. Se for viajar entre novembro e janeiro, consulte a disposição vigente com o seu guia ou no portal oficial antes de sair.' ],
			[ 'pregunta' => 'Como chego desde Buenos Aires?', 'respuesta' => 'São cerca de 600 km por estrada. Desde a cidade de Corrientes são cerca de 330 km, com ônibus diretos.' ],
			[ 'pregunta' => 'Posso levar um pacu?', 'respuesta' => 'A medida mínima é 45 cm e só se pesca com vara ou molinete (mosca, baitcasting ou isca vegetal, como frutos de ingá). Em arroios e estuários os operadores locais recomendam devolvê-lo para cuidar do lugar.' ],
			[ 'pregunta' => 'Dá para pescar da margem ou precisa de lancha? PENDIENTE', 'respuesta' => 'PENDIENTE: confirmar com a Prefeitura e guias onde se pode pescar da margem.' ],
			[ 'pregunta' => 'Quanto custa uma saída de pesca embarcada? PENDIENTE', 'respuesta' => 'PENDIENTE: pedir preços 2026 a guias locais.' ],
		],
	],
	'fuentes' => [
		[ 'Recursos Naturales de Corrientes', 'https://www.recnaturalesctes.gob.ar/', 1 ],
		[ 'Senado de Corrientes: Lei 6730 (dourado, Monumento Natural Provincial)', 'https://senadoctes.gov.ar/normativas/leyes/6730.pdf', 1 ],
		[ 'Governo de Santa Fe: acordo de reconhecimento recíproco de licenças com Entre Ríos, Chaco e Corrientes', 'https://www.santafe.gob.ar/noticias/noticia/287531/', 1 ],
		[ 'Argentina.travel: Esquina', 'https://www.argentina.travel/pt/actividades/esquina', 1 ],
		[ 'INA: sistema de alerta hidrológico (altura do Paraná)', 'https://alerta.ina.gob.ar/', 1 ],
		[ 'Welcome Argentina: pescar em Esquina', 'https://www.welcomeargentina.com/esquina/pesca-esquina.html', 0 ],
		[ 'La Nación: Esquina, o rio e a fauna', 'https://www.lanacion.com.ar/revista-lugares/esquina-la-localidad-correntina-que-hizo-feliz-a-maradona-tiene-un-rio-increible-y-mucha-fauna-nid03052024/', 0 ],
		[ 'Prensa Mercosur: Festa Nacional do Pacu 2026', 'https://prensamercosur.org/2026/02/05/la-fiesta-nacional-del-pacu-impulsa-el-turismo-y-la-agenda-de-verano-en-corrientes/', 0 ],
	],
];

// --- English --------------------------------------------------------------------
$guias['en'] = [
	'slug'   => 'fishing-in-esquina',
	'cuerpo' => implode( "\n\n", [
		pm_et_p( 'Esquina is in southern Corrientes, where the <strong>Corriente River</strong> flows into the <strong>Paraná</strong>. That meeting forms a delta of islands, side channels, lagoons and marshes, and it is the reason people come to fish here: the big waters of the Paraná on one side, the calmer, clearer waters of the Corriente on the other.' ),
		pm_et_h( 'The Esquina delta: fishing areas and habitats' ),
		pm_et_h( 'The Corriente River and its side channels', 3 ),
		pm_et_p( 'Clearer and slower water than the Paraná. It is the habitat lodges choose for <strong>fly fishing for dorado</strong>, and in the small marshes big dorado and tararira (trahira) also turn up.' ),
		pm_et_pendiente( 'confirm with a local guide the Torello channel, names of side channels and lagoons, sandbars and whether light lures are used on the Corriente.' ),
		pm_et_h( 'The main channel of the Paraná', 3 ),
		pm_et_p( 'In front of Esquina the Paraná is a boat river: <strong>trolling</strong> and guided boat fishing for <strong>surubí</strong> and dorado. In recent years the regional press has reported surubí of over 40 kilos caught (and released) in this area.' ),
		pm_et_pendiente( 'deep holes, runs and anchored fishing in Esquina: validate with a local guide.' ),
		pm_et_h( 'Target species and methods' ),
		pm_et_p( 'The stars are <strong>dorado</strong> and <strong>surubí</strong>; <strong>patí</strong> and <strong>pacú</strong> are also caught, the species the town\'s festival is named after. Fishing is from a boat, with spinning, trolling and fly gear. The <strong>dorado</strong> is a Provincial Natural Monument of Corrientes (Law 6730): it is fished catch-and-release.' ),
		pm_et_p( '<strong>How it changes with the river level.</strong> As on the whole Paraná in Corrientes, when the river is low the fish gather at stream mouths and in deep holes, and lures and trolling work; when it is high the water floods islands and marshes, the fish scatter, and it pays to look along the banks and in the deep side channels. Check the river level before you book.' ),
		pm_et_pendiente( 'boga, baitcasting and technique by species in Esquina: confirm with a local guide.' ),
		pm_et_h( 'When to go: a real calendar, not "all year"' ),
		pm_et_p( 'Catch-and-release fishing being open does not mean every species performs equally all year. The calendar periods are a regional reference: they are set by the river (floods and low water), water temperature and the closures the province decides. Always ask your guide what is working on your dates.' ),
		pm_et_h( 'Fishing guides, cabins and inns' ),
		pm_et_p( 'There are lodges with multi-day packages (mostly fly fishing, with full board, guide and boat) and local guides who offer boat days. A typical day includes the boat, fuel, a guide and bait; some lend gear. Always ask in writing what is included and what is not.' ),
		pm_et_pendiente( '2026 prices for a day with an independent guide; tracker and lunch on the island.' ),
		pm_et_h( 'Getting there, what to bring and what to do besides fishing' ),
		pm_et_p( 'Esquina is about <strong>600 km from Buenos Aires</strong> and about <strong>330 km from the city of Corrientes</strong>, with direct buses from the provincial capital. For fly fishing for dorado, lodges recommend 9.5-foot rods, 7- or 8-weight line (floating, intermediate and fast-sinking), baitfish-style flies and poppers, and steel leader. For spinning and trolling, ask your guide about gear.' ),
		pm_et_p( 'For companions: beaches on the Corriente River, rural tourism on estancias, photography and birdwatching. In February the <strong>National Pacú Festival</strong> fills the town: in 2026 it ran from 8 to 15 February (39th edition).' ),
		pm_et_pendiente( 'distances and routes from Rosario, Córdoba and the Brazilian border; date of the 2027 Pacú Festival; official municipality website.' ),
	] ),
	'campos' => [
		'h1'          => 'Sport fishing in Esquina, Corrientes: a complete guide to species, seasons and services',
		'bajada'      => 'Where the Corriente River opens into the Paraná there are islands, side channels and marshes. Dorado, surubí and pacú, catch-and-release, and how to read the river before you go.',
		'modalidades' => "Guided boat fishing\nSpinning\nTrolling\nFly fishing",
		'epoca'       => 'Dorado from October to April and surubí from March to August as a reference; everything depends on the river level.',
		'seo_title'   => 'Fishing in Esquina, Corrientes: dorado, surubí and seasons',
		'seo_description' => 'Fishing guide to Esquina, Corrientes: dorado and surubí on the Paraná and the Corriente River, when to go, licence, closures, guides and the Pacú Festival.',
		'temporada'   => [
			[ 'meses' => 'October – April', 'que' => 'Dorado: most active in warm water. Fly fishing on the Corriente River and its side channels; lures and trolling on the Paraná. Always catch-and-release.' ],
			[ 'meses' => 'March – August', 'que' => 'Surubí: regional reference, in deep holes and drop-offs of the Paraná. Large fish are also reported outside those months.' ],
			[ 'meses' => 'December – March', 'que' => 'Pacú: summer season, near floating vegetation and wooded banks.' ],
			[ 'meses' => 'February', 'que' => 'National Pacú Festival (in 2026, 8–15 February). Peak demand for lodging: book early.' ],
			[ 'meses' => 'Before every trip', 'que' => 'Check the Paraná level, the forecast and the closures in force. The periods are indicative and change with the river.' ],
		],
		'reglamento_autoridad' => 'Dirección de Recursos Naturales de Corrientes',
		'reglamento_resumen' => 'Fishing in Corrientes requires a sport fishing licence. You get it online on the Recursos Naturales portal: fill in the form with your ID (DNI), pay through the digital gateway and receive the credential with a QR code, which you can show on your phone together with your ID. There are shore and boat categories, and rates by residence (Corrientes residents, rest of the country, foreigners); retirees, pensioners and children under 12 apply for an exemption at no cost. On the Corriente River and the side channels of its delta only sport fishing with a rod or hand line is allowed: no nets or longlines. The dorado is a Provincial Natural Monument (Law 6730) and is fished catch-and-release. The pacú has a minimum size of 45 cm, and local operators recommend releasing it in streams and marshes. Spawning closures (or the regime of fixed days per week) are set every year by Recursos Naturales: if you travel between November and January, check the current provision before you leave.',
		'aguas' => [
			[ 'nombre' => 'Paraná River off Esquina', 'nota' => 'Main channel: guided boat fishing, trolling and spinning for surubí and dorado.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Corriente River and its delta', 'nota' => 'Clearer, slower water, side channels and marshes: the fly fishing habitat for dorado.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Islands and side channels of the Paraná', 'nota' => 'Side channels, lagoons and marshes between islands. Best with a guide.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'What can you catch in Esquina?', 'respuesta' => 'Mostly dorado and surubí; also patí and pacú, and tararira in the marshes. That is the typical offer, not a guarantee of a catch: it depends on the river and the season.' ],
			[ 'pregunta' => 'Do I need a fishing licence?', 'respuesta' => 'Yes. You get it online on the Recursos Naturales portal of Corrientes with your ID (DNI); you pay through the digital gateway and receive a credential with a QR that is valid on your phone (carry your ID too). There are shore and boat categories, for Corrientes residents, the rest of the country and foreigners. Retirees, pensioners and children under 12 apply for an exemption at no cost. If you are from Santa Fe, Entre Ríos or Chaco, see the regional reciprocal recognition agreement.' ],
			[ 'pregunta' => 'Can I keep a dorado?', 'respuesta' => 'No. The dorado is a Provincial Natural Monument of Corrientes (Law 6730) and is fished catch-and-release.' ],
			[ 'pregunta' => 'When is the National Pacú Festival?', 'respuesta' => 'In February. In 2026 it ran from 8 to 15 February (39th edition). It is the time of highest demand for lodging in Esquina.' ],
			[ 'pregunta' => 'What dates does the closed season cover in Corrientes?', 'respuesta' => 'They change every year: Recursos Naturales sets the spawning closures (or a regime of fixed days per week) based on each spring\'s reports. If you travel between November and January, check the current provision with your guide or on the official portal before you leave.' ],
			[ 'pregunta' => 'How do I get there from Buenos Aires?', 'respuesta' => 'About 600 km by road. From the city of Corrientes it is about 330 km, with direct buses.' ],
			[ 'pregunta' => 'Can I keep a pacú?', 'respuesta' => 'The minimum size is 45 cm and it is fished only with a rod or reel (fly, baitcasting or plant bait such as ingá fruit). In streams and marshes local operators recommend releasing it to look after the place.' ],
			[ 'pregunta' => 'Can you fish from the shore or do you need a boat? PENDIENTE', 'respuesta' => 'PENDIENTE: confirm with the municipality and guides where shore fishing is possible.' ],
			[ 'pregunta' => 'How much does a day of boat fishing cost? PENDIENTE', 'respuesta' => 'PENDIENTE: ask local guides for 2026 prices.' ],
		],
	],
	'fuentes' => [
		[ 'Recursos Naturales de Corrientes', 'https://www.recnaturalesctes.gob.ar/', 1 ],
		[ 'Corrientes Senate: Law 6730 (dorado, Provincial Natural Monument)', 'https://senadoctes.gov.ar/normativas/leyes/6730.pdf', 1 ],
		[ 'Government of Santa Fe: reciprocal licence recognition agreement with Entre Ríos, Chaco and Corrientes', 'https://www.santafe.gob.ar/noticias/noticia/287531/', 1 ],
		[ 'Argentina.travel: Esquina', 'https://www.argentina.travel/pt/actividades/esquina', 1 ],
		[ 'INA: hydrological alert system (Paraná level)', 'https://alerta.ina.gob.ar/', 1 ],
		[ 'Welcome Argentina: fishing in Esquina', 'https://www.welcomeargentina.com/esquina/pesca-esquina.html', 0 ],
		[ 'La Nación: Esquina, the river and the wildlife', 'https://www.lanacion.com.ar/revista-lugares/esquina-la-localidad-correntina-que-hizo-feliz-a-maradona-tiene-un-rio-increible-y-mucha-fauna-nid03052024/', 0 ],
		[ 'Prensa Mercosur: National Pacú Festival 2026', 'https://prensamercosur.org/2026/02/05/la-fiesta-nacional-del-pacu-impulsa-el-turismo-y-la-agenda-de-verano-en-corrientes/', 0 ],
	],
];

$es = pm_buscar( 'pm_destino', 'esquina', 'es' );
if ( ! $es ) {
	WP_CLI::error( 'No encontré Esquina (es).' );
}
$estado_es = $es->post_status;

foreach ( [ 'pt', 'en' ] as $i ) {
	if ( pm_buscar( 'pm_destino', 'esquina', $i ) ) {
		WP_CLI::log( "esquina ($i) ya existe: no toco nada." );
		continue;
	}
	$g = $guias[ $i ];
	WP_CLI::log( "esquina ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " /{$g['slug']}/ ({$estado_es}) con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $g['fuentes'] ) . ' fuentes' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [
		'post_type'    => 'pm_destino',
		'post_status'  => $estado_es,
		'post_title'   => 'Esquina',
		'post_name'    => $g['slug'],
		'post_content' => wp_slash( $g['cuerpo'] ),
	], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $g['fuentes'] );
	$campos = $g['campos'] + [
		'idioma' => $i, 'clave' => 'esquina', 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
		'reglamento_url' => 'https://www.recnaturalesctes.gob.ar/', 'reglamento_verificado' => $hoy, 'fuentes' => $filas,
	];
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, '_pm_traducido', time() + 1 );
	WP_CLI::log( "✓ esquina ($i) #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
