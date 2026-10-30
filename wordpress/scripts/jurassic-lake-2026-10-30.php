<?php
/**
 * Jurassic Lake (Lago Strobel, Santa Cruz), 30/10/2026: destination guide in Spanish (the source that holds the shared data) and English
 * (the one the search plan targets: "jurassic lake argentina", docs/seo-palabras-clave-en-2026-10-29.md). Facts, all from
 * docs/investigacion/jurassic-lake-r2-auditoria-2026-10-30.md:
 *  - Disposición 241/2026 (Reglamento General 2026/27), read from the signed copy, arts. 1-18.
 *  - Anexo I (Strobel, Barrancoso), read in a third-party copy of the regulation whose arts. 5, 7, 11, 12 and 16 match the signed copy word for word.
 *  - Permit values: MINPRO Santa Cruz note of 01/10/2026 (La Voz de Santa Cruz repeats them).
 *  - Operators: their own sites, always worded "according to the operator". No rates, no wind numbers, no road condition.
 * Operators are listed as "listado con información pública" (docs/prestadores.md): nothing is verified with them.
 * No photo: none of ours is of this lake; the owner supplies one with its credit.
 *
 * Idempotent: a guide that exists is not touched. Dry run first. Run with wp-script.yml (script = jurassic-lake-2026-10-30).
 */

$aplicar = false;
$hoy     = '20261030';

function pm_jl_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_jl_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_jl_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_jl_pendiente( string $t ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$fuentes = [
	[ 'Santa Cruz Ministry of Production (MINPRO): 2026/27 season opened, permit values (01/10/2026)', 'https://minpro.santacruz.gob.ar/2026/10/01/santa-cruz-abrio-la-temporada-de-pesca-deportiva-2026-2027/', 1 ],
	[ 'Santa Cruz sport fishing portal: Reglamento General 2026/27 (Disposición 241/26) and Anexo I, environments', 'https://pesca.santacruz.gob.ar/reglamento/', 1 ],
	[ 'Santa Cruz sport fishing portal: list of environments (Anexo I)', 'https://pesca.santacruz.gob.ar/ambientes/', 1 ],
	[ 'Copy of the regulation with annexes hosted by pesca.clubnautico.org (third party: the official portal was down on 30/10/2026)', 'https://pesca.clubnautico.org/reglamento-santa-cruz-2026-2027.pdf', 0 ],
	[ 'La Voz de Santa Cruz: the province opened the 2026/27 sport fishing season (01/10/2026)', 'https://lavozdesantacruz.com.ar/2026/10/01/la-provincia-abrio-la-temporada-de-pesca-deportiva-2026-2027/', 0 ],
	[ 'Welcome Argentina: Gobernador Gregores and the Río Chico valley (access by road)', 'https://www.welcomeargentina.com/gobernadorgregores/valle-rio-chico.html', 0 ],
	[ 'Jurassic Lake Lodge: location and how to get there (operator)', 'https://www.jurassiclake.com/location', 0 ],
	[ 'Estancia Laguna Verde (operator)', 'https://estancialagunaverde.com/', 0 ],
	[ 'Tight Loop Travel: Estancia Laguna Verde (agency)', 'https://www.tightlooptravel.com/estancia-laguna-verde.html', 0 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$fuentes_es = [
	[ 'Ministerio de la Producción de Santa Cruz (MINPRO): se abrió la temporada 2026/27, valores de permisos (01/10/2026)', $fuentes[0][1], 1 ],
	[ 'Portal de pesca deportiva de Santa Cruz: Reglamento General 2026/27 (Disposición 241/26) y Anexo I', $fuentes[1][1], 1 ],
	[ 'Portal de pesca deportiva de Santa Cruz: listado de ambientes (Anexo I)', $fuentes[2][1], 1 ],
	[ 'Copia del reglamento con anexos alojada en pesca.clubnautico.org (terceros: el portal oficial no respondía el 30/10/2026)', $fuentes[3][1], 0 ],
	[ 'La Voz de Santa Cruz: la Provincia abrió la temporada de pesca deportiva 2026/2027 (01/10/2026)', $fuentes[4][1], 0 ],
	[ 'Welcome Argentina: Gobernador Gregores y el valle del Río Chico (acceso por tierra)', $fuentes[5][1], 0 ],
	[ 'Jurassic Lake Lodge: ubicación y cómo llegar (operador)', $fuentes[6][1], 0 ],
	[ 'Estancia Laguna Verde (operador)', $fuentes[7][1], 0 ],
	[ 'Tight Loop Travel: Estancia Laguna Verde (agencia)', $fuentes[8][1], 0 ],
];
$fuentes_es_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes_es );

// --- English -----------------------------------------------------------------------
$cuerpo_en = implode( "\n\n", [
	pm_jl_p( '<strong>Jurassic Lake</strong> is the name anglers give to <strong>Lago Strobel</strong>, a remote lake in central Santa Cruz, Patagonia, known for very large rainbow trout. The name is a trade mark of one lodge, not the lake\'s name: the lake is Strobel and its main inflow is the <strong>Barrancoso River</strong>. This page covers the rules, the permit and how people get there. It is a planning guide: confirm everything with your operator and the provincial authority before you travel.' ),
	pm_jl_h( 'Rules at Lago Strobel and the Barrancoso River' ),
	pm_jl_p( 'The 2026/27 regulation is <strong>Disposición 241/2026</strong> of the Santa Cruz Secretary of State for Fishing and Aquaculture (signed 22 September 2026, in force from 1 October 2026). Its Anexo I lists each water. For these two:' ),
	pm_jl_lista( [
		'<strong>Lago Strobel</strong>: open all year, no closed season. <strong>Catch and release only.</strong> Single hook, barbless or barbed. Fishing is banned on both banks within <strong>200 m</strong> of the mouths and sources of rivers linked to the lake. <strong>Motors of internal combustion or any mechanical propulsion are banned</strong> for navigating the lake. Disinfect and wash your gear at the end of each day (Didymo).',
		'<strong>Barrancoso River</strong>: open <strong>1 October 2026 to 3 May 2027</strong>. Catch and release, absolutely. Single hook, barbless or barbed. It is classed as a zone of maximum vulnerability to the Didymo alga: you must carry the <strong>daily disinfection certificate</strong> and wash your gear at the end of the day. Fishing is banned on both banks within <strong>100 m</strong> of the river\'s source and mouth.',
		'No bag limit and no minimum size are listed for either water: with release mandatory, you may not keep or transport any fish (Article 12).',
	] ),
	pm_jl_p( 'Moro Creek, which the lodges mention, does not appear in the Anexo I we read; the general regulation applies to waters not listed. The Annex says the web list only shows some waters, so ask the authority or your operator.' ),
	pm_jl_h( 'General rules that apply here too' ),
	pm_jl_lista( [
		'<strong>One artificial lure with one hook</strong> (Article 8) and <strong>one rod in use at a time</strong> (Article 10). Where release is mandatory, use a barbless or crushed-barb hook and release the fish alive (Article 11).',
		'<strong>No lead</strong>: weights, sinkers and lures with lead heads or cores are banned (Article 15 r).',
		'<strong>Daylight only</strong>: fishing from sunrise to sunset, no night fishing (Articles 6 and 15 t).',
		'<strong>Fishing from a boat is banned within 200 m of river mouths and sources in lakes</strong> (Article 15 a).',
		'<strong>Didymo</strong>: waders, boots, tackle, floats and vehicles must be disinfected; felt soles are only allowed if you follow the protocol (Articles 17 and 18).',
		'Inspectors may use body cameras and drones to document checks (Article 1). Do not use a drone to locate fish (Annex II, low-impact practices).',
	] ),
	pm_jl_p( 'The regulation notes that it is better not to wade on fine gravel in rivers and streams until 1 January 2027, to protect spawning beds. This is a recommendation, not a rule.' ),
	pm_jl_h( 'The permit and what it costs' ),
	pm_jl_p( 'The provincial permit is <strong>personal, in your name and not transferable</strong>. You manage it through SIPESQ (the province\'s fishing coordination system), and you carry it, on paper or digital, with your ID. It is only valid inside Santa Cruz. Values for 2026/27, in Argentine pesos, as published by the provincial ministry on 1 October 2026:' ),
	pm_jl_lista( [
		'<strong>Non-resident foreigners</strong>: day AR$ 90,000; week AR$ 300,000; whole season AR$ 450,000.',
		'<strong>Trolling</strong>: an extra permit on top of the ordinary one: AR$ 20,000 per day, AR$ 30,000 per week, AR$ 40,000 per season.',
		'<strong>Argentine residents</strong>: day AR$ 15,000; weekend AR$ 20,000; week AR$ 25,000; season AR$ 40,000.',
	] ),
	pm_jl_p( 'The season permit is valid until 30 September 2027. Values are updated by the authority: confirm the current amount in SIPESQ before you pay, and check the exchange rate that day. Trolling is a mode of fishing: whether it is allowed on Strobel with the motor ban in force is not stated in the Annex; ask your operator.' ),
	pm_jl_h( 'Getting there' ),
	pm_jl_p( 'From <strong>Gobernador Gregores</strong> the Welcome Argentina tourism guide describes provincial route 25 to the north, turning south at the village of Las Horquetas (115 km from Gregores) and then crossing the Meseta del Viento; it says 4x4 vehicles only. We have not verified the road or its condition. Most visiting anglers do not drive this on their own: the lodges organise the transfer.' ),
	pm_jl_lista( [
		'<strong>Jurassic Lake Lodge</strong> says it runs a charter flight from El Calafate every Saturday morning (about one hour to its private airstrip), with a ground transfer from El Calafate of about six hours included in its packages.',
		'<strong>Estancia Laguna Verde</strong>, through the agency Tight Loop Travel, describes a scenic drive of about five hours from El Calafate.',
	] ),
	pm_jl_p( 'Times are the operators\' own and vary by operator and road; do not plan a tight connection.' ),
	pm_jl_h( 'When to go' ),
	pm_jl_p( 'The general fishing season runs from 1 October 2026 to 30 September 2027. According to Jurassic Lake Lodge, the early season (October to early January) is when big trout move up the Barrancoso and Moro Creek, and the mid season (mid January to mid March) is sight fishing along the lake shore. That is the lodge\'s own description, not an official calendar. Remember the Barrancoso closes on 3 May 2027.' ),
	pm_jl_pendiente( 'wind and weather by month, with a source; gear note for heavy trout and wind; road conditions from the Gobernador Gregores side; photo of the lake with its credit.' ),
	pm_jl_h( 'Lodges and outfitters' ),
	pm_jl_p( 'Several estancias and lodges around the lake take anglers. Below are the ones we found with public information; we have not contacted them yet, which is why they are marked. Packages are usually sold by the week or by several nights, per person, in US dollars; we show no prices because we have not confirmed any current rate with an operator.' ),
] );

// --- Spanish (the source of the shared data) -------------------------------------------------
$cuerpo_es = implode( "\n\n", [
	pm_jl_p( '<strong>Jurassic Lake</strong> es el nombre con que los pescadores extranjeros conocen al <strong>Lago Strobel</strong>, un lago remoto del centro de Santa Cruz, en la Patagonia, famoso por sus truchas arco iris enormes. Es una marca registrada de un lodge, no el nombre del lago: el lago es el Strobel y su afluente principal es el <strong>río Barrancoso</strong>. Esta página reúne las reglas, el permiso y cómo se llega. Es una guía para planificar: confirmá todo con el operador y con la autoridad provincial antes de viajar.' ),
	pm_jl_h( 'Reglas en el Lago Strobel y el río Barrancoso' ),
	pm_jl_p( 'El reglamento 2026/27 es la <strong>Disposición 241/2026</strong> de la Secretaría de Estado de Pesca y Acuicultura de Santa Cruz (firmada el 22 de septiembre de 2026, rige desde el 1 de octubre de 2026). Su Anexo I lista cada ambiente. Para estos dos:' ),
	pm_jl_lista( [
		'<strong>Lago Strobel</strong>: abierto todo el año, sin veda. <strong>Captura y devolución obligatoria.</strong> Anzuelo simple, con o sin rebaba. Está prohibido pescar sobre ambas márgenes en un radio de <strong>200 m</strong> desde las nacientes y desembocaduras de los ríos vinculados al lago. Está <strong>absolutamente prohibida la navegación con motores de combustión interna o propulsión mecánica</strong>. Al terminar la jornada hay que desinfectar y lavar los materiales (Didymo).',
		'<strong>Río Barrancoso</strong>: abierto del <strong>1 de octubre de 2026 al 3 de mayo de 2027</strong>. Captura y devolución obligatoria de forma absoluta. Anzuelo simple, con o sin rebaba. Es zona de máxima vulnerabilidad ante el alga Didymo: hay que portar el <strong>certificado diario de desinfección</strong> y lavar los materiales al terminar. Está prohibido pescar sobre ambas márgenes en <strong>100 m</strong> desde sus nacientes y desembocadura.',
		'Para ninguno de los dos figuran cupo ni talla mínima: con la devolución obligatoria no se puede tener ni transportar ningún ejemplar (art. 12).',
	] ),
	pm_jl_p( 'El arroyo Moro, que mencionan los lodges, no figura en el Anexo I que leímos; a los ambientes no listados se les aplica la reglamentación general. El anexo aclara que el listado web muestra solo algunos ambientes: consultá a la autoridad o al operador.' ),
	pm_jl_h( 'Reglas generales que también valen acá' ),
	pm_jl_lista( [
		'<strong>Un solo señuelo artificial con un solo anzuelo</strong> (art. 8) y <strong>un solo equipo en uso a la vez</strong> (art. 10). Donde la devolución es obligatoria, anzuelo sin rebaba o con rebaba aplastada, y el pez se devuelve vivo (art. 11).',
		'<strong>Sin plomo</strong>: están prohibidos lastres, plomadas y señuelos con plomo en la cabeza o el núcleo (art. 15 r).',
		'<strong>Solo de día</strong>: de la salida a la puesta del sol, sin pesca nocturna (arts. 6 y 15 t).',
		'<strong>Pescar desde una embarcación está prohibido a menos de 200 m</strong> de nacientes y desembocaduras de ríos en lagos (art. 15 a).',
		'<strong>Didymo</strong>: waders, botas, equipos, flotadores y vehículos se desinfectan; las suelas de fieltro solo se admiten si se cumple el protocolo (arts. 17 y 18).',
		'Los inspectores pueden usar cámaras personales y drones (art. 1). No uses drones para ubicar peces (Anexo II, prácticas de bajo impacto).',
	] ),
	pm_jl_p( 'El reglamento recomienda no vadear sectores de grava fina en ríos y arroyos hasta el 1 de enero de 2027, para cuidar las camas de desove. Es una recomendación, no una obligación.' ),
	pm_jl_h( 'El permiso y cuánto cuesta' ),
	pm_jl_p( 'El permiso provincial es <strong>personal, nominativo e intransferible</strong>. Se gestiona por el SIPESQ (el sistema de coordinación pesquera de la provincia) y se porta, impreso o digital, con el documento. Solo vale dentro de Santa Cruz. Valores 2026/27, en pesos argentinos, según el ministerio provincial el 1 de octubre de 2026:' ),
	pm_jl_lista( [
		'<strong>Extranjeros no residentes</strong>: diario $90.000; semanal $300.000; temporada $450.000.',
		'<strong>Trolling</strong>: permiso adicional que se suma al ordinario: $20.000 diario, $30.000 semanal, $40.000 por temporada.',
		'<strong>Residentes en el país</strong>: diario $15.000; fin de semana $20.000; semanal $25.000; temporada $40.000.',
	] ),
	pm_jl_p( 'El permiso de temporada vale hasta el 30 de septiembre de 2027. La autoridad actualiza los valores: confirmá el monto vigente en el SIPESQ antes de pagar. El trolling es una modalidad de pesca: si se puede practicar en el Strobel con la prohibición de motores vigente no lo dice el Anexo; consultá al operador.' ),
	pm_jl_h( 'Cómo llegar' ),
	pm_jl_p( 'Desde <strong>Gobernador Gregores</strong>, la guía de turismo Welcome Argentina describe la ruta provincial 25 hacia el norte, doblar al sur en el paraje Las Horquetas (a 115 km de Gregores) y cruzar luego la Meseta del Viento; dice que es solo para vehículos 4x4. No verificamos el camino ni su estado. La mayoría de los pescadores que visitan no lo hacen por su cuenta: los lodges organizan el traslado.' ),
	pm_jl_lista( [
		'<strong>Jurassic Lake Lodge</strong> dice que tiene un vuelo chárter desde El Calafate todos los sábados por la mañana (una hora hasta su pista privada) y que incluye en sus paquetes un traslado terrestre desde El Calafate de unas seis horas.',
		'<strong>Estancia Laguna Verde</strong>, a través de la agencia Tight Loop Travel, describe un viaje en auto de unas cinco horas desde El Calafate.',
	] ),
	pm_jl_p( 'Los tiempos son los de cada operador y varían según el operador y el camino: no armes conexiones ajustadas.' ),
	pm_jl_h( 'Cuándo ir' ),
	pm_jl_p( 'La temporada general va del 1 de octubre de 2026 al 30 de septiembre de 2027. Según Jurassic Lake Lodge, al comienzo (de octubre a principios de enero) las truchas grandes suben al Barrancoso y al arroyo Moro, y a mitad de temporada (de mediados de enero a mediados de marzo) se pesca a la vista en la costa del lago. Es la descripción del lodge, no un calendario oficial. Recordá que el Barrancoso cierra el 3 de mayo de 2027.' ),
	pm_jl_pendiente( 'viento y clima por mes, con fuente; nota de equipo para truchas grandes y viento; estado del camino desde Gobernador Gregores; foto del lago con su crédito.' ),
	pm_jl_h( 'Lodges y operadores' ),
	pm_jl_p( 'Varias estancias y lodges alrededor del lago reciben pescadores. Abajo están los que encontramos con información pública; todavía no los contactamos, por eso figuran marcados. Los paquetes suelen venderse por semana o por varias noches, por persona, en dólares; no mostramos precios porque no confirmamos ninguna tarifa vigente con un operador.' ),
] );

$comun = [
	'clave'       => 'jurassic-lake',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'reglamento_autoridad' => 'Secretaría de Estado de Pesca y Acuicultura de Santa Cruz',
	'reglamento_url'       => 'https://pesca.santacruz.gob.ar/reglamento/',
	'reglamento_verificado' => $hoy,
];

$guias = [
	'es' => [
		'titulo' => 'Jurassic Lake (Lago Strobel)', 'slug' => 'pesca-en-jurassic-lake', 'cuerpo' => $cuerpo_es, 'fuentes' => $fuentes_es_filas,
		'campos' => [
			'h1'          => 'Pesca en Jurassic Lake (Lago Strobel), Santa Cruz: reglas, permiso y cómo llegar',
			'bajada'      => 'Jurassic Lake es el apodo del Lago Strobel, en el centro de Santa Cruz. Reglas de la temporada 2026/27, permiso de no residente y cómo se llega.',
			'modalidades' => "Mosca (flycast)\nSpinning y baitcasting\nCaptura y devolución",
			'epoca'       => 'Lago Strobel abierto todo el año; río Barrancoso del 1/10/2026 al 3/5/2027.',
			'seo_title'   => 'Pesca en Jurassic Lake (Lago Strobel): reglas y permiso',
			'seo_description' => 'Guía de pesca del Lago Strobel (Jurassic Lake), Santa Cruz: captura y devolución, reglas 2026/27 del Anexo I, permiso de no residente y cómo llegar.',
			'temporada'   => [
				[ 'meses' => 'Lago Strobel', 'que' => 'Abierto todo el año. Captura y devolución obligatoria.' ],
				[ 'meses' => 'Río Barrancoso', 'que' => 'Del 1 de octubre de 2026 al 3 de mayo de 2027. Devolución obligatoria absoluta.' ],
				[ 'meses' => 'Temporada general', 'que' => 'Del 1 de octubre de 2026 al 30 de septiembre de 2027 (Disposición 241/26).' ],
				[ 'meses' => 'Calendario por mes', 'que' => 'PENDIENTE: viento, clima y mejores meses con una fuente.' ],
			],
			'reglamento_resumen' => 'Disposición 241/2026 (22/09/2026, rige desde el 1/10/2026), Reglamento General de Pesca Deportiva Continental de Santa Cruz, temporada 2026/27. Strobel: abierto todo el año, captura y devolución obligatoria, anzuelo simple con o sin rebaba, exclusión de 200 m, sin motores. Barrancoso: 1/10/2026 al 3/5/2027, devolución absoluta, exclusión de 100 m, certificado de desinfección diario. Permiso personal por SIPESQ. Leído el 30/10/2026 (Anexo I en copia de terceros, coincidente con el original en los arts. 5, 7, 11, 12 y 16).',
			'aguas' => [
				[ 'nombre' => 'Lago Strobel', 'nota' => 'Captura y devolución obligatoria; sin motores. Confirmá los sectores habilitados con el operador.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Barrancoso', 'nota' => 'Principal afluente del lago. Del 1/10/2026 al 3/5/2027, devolución obligatoria absoluta.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => '¿Jurassic Lake es el nombre real del lago?', 'respuesta' => 'No. El lago se llama Strobel. "Jurassic Lake" es una marca registrada de un lodge, tan conocida que muchos creen que es el nombre del lago.' ],
				[ 'pregunta' => '¿Puedo llevarme los peces?', 'respuesta' => 'No. En el Lago Strobel y en el río Barrancoso rige la captura y devolución obligatoria, y en esas zonas está prohibido tener o transportar ejemplares (Anexo I y art. 12 de la Disposición 241/26).' ],
				[ 'pregunta' => '¿Qué permiso necesito y cuánto cuesta?', 'respuesta' => 'El permiso provincial de Santa Cruz, personal e intransferible, por el SIPESQ. Para extranjeros no residentes en 2026/27: diario $90.000, semanal $300.000 y temporada $450.000 (pesos argentinos, según el ministerio provincial el 1/10/2026). Confirmá el valor vigente antes de pagar.' ],
				[ 'pregunta' => '¿Qué anzuelo y qué equipo puedo usar?', 'respuesta' => 'Un solo señuelo artificial con un solo anzuelo simple, con o sin rebaba (aplastada es la opción prudente), un equipo a la vez y sin plomo (arts. 8, 10 y 15 r y Anexo I).' ],
				[ 'pregunta' => '¿Puedo pescar desde un bote a motor?', 'respuesta' => 'En el Lago Strobel no: el Anexo I prohíbe absolutamente navegar con motores de combustión interna o propulsión mecánica. Además, no se puede pescar desde embarcación a menos de 200 m de nacientes y desembocaduras (art. 15 a).' ],
				[ 'pregunta' => '¿Se puede pescar en la desembocadura del Barrancoso?', 'respuesta' => 'El Anexo I prohíbe pescar sobre ambas márgenes en 200 m desde las desembocaduras de los ríos vinculados al Strobel y en 100 m desde las del Barrancoso. Cómo se aplica en cada punto lo confirman el operador o los guardapescas: no lo interpretamos.' ],
				[ 'pregunta' => '¿Cuándo está abierto?', 'respuesta' => 'El Lago Strobel, todo el año. El río Barrancoso, del 1 de octubre de 2026 al 3 de mayo de 2027. La temporada general va hasta el 30 de septiembre de 2027.' ],
				[ 'pregunta' => '¿Cómo se llega?', 'respuesta' => 'Según Jurassic Lake Lodge, con un chárter de una hora desde El Calafate los sábados o con un traslado terrestre de unas seis horas; Estancia Laguna Verde habla de unas cinco horas en auto. Desde Gobernador Gregores el acceso es por la ruta provincial 25, solo 4x4, y no verificamos el estado del camino.' ],
				[ 'pregunta' => '¿Qué es el certificado de desinfección?', 'respuesta' => 'En el río Barrancoso, zona de máxima vulnerabilidad al alga Didymo, el Anexo I exige portar el certificado diario de desinfección y lavar los materiales al terminar la jornada. Pedí en el lodge cómo se obtiene.' ],
				[ 'pregunta' => '¿Cuánto cuesta un viaje?', 'respuesta' => 'Los lodges venden paquetes de varias noches, por persona, en dólares. No tenemos una tarifa vigente confirmada con ningún operador, por eso no publicamos ninguna.' ],
			],
		],
	],
	'en' => [
		'titulo' => 'Jurassic Lake (Lago Strobel)', 'slug' => 'fishing-in-jurassic-lake', 'cuerpo' => $cuerpo_en, 'fuentes' => $fuentes_filas,
		'campos' => [
			'h1'          => 'Fishing at Jurassic Lake (Lago Strobel), Santa Cruz: rules, permit and how to get there',
			'bajada'      => 'Jurassic Lake is the name anglers use for Lago Strobel, in central Santa Cruz, Patagonia. The 2026/27 rules, the non-resident permit and how people get there.',
			'modalidades' => "Fly fishing\nSpinning and baitcasting\nCatch and release",
			'epoca'       => 'Lago Strobel is open all year; the Barrancoso River from 1 Oct 2026 to 3 May 2027.',
			'seo_title'   => 'Jurassic Lake fishing guide: Strobel rules and permit',
			'seo_description' => 'Jurassic Lake (Lago Strobel) fishing guide, Santa Cruz: catch and release, 2026/27 Annex I rules, the non-resident permit with prices and how to get there.',
			'temporada'   => [
				[ 'meses' => 'Lago Strobel', 'que' => 'Open all year. Catch and release only.' ],
				[ 'meses' => 'Barrancoso River', 'que' => '1 October 2026 to 3 May 2027. Catch and release, absolutely.' ],
				[ 'meses' => 'General season', 'que' => '1 October 2026 to 30 September 2027 (Disposición 241/26).' ],
				[ 'meses' => 'Calendar by month', 'que' => 'PENDIENTE: wind, weather and best months, with a source.' ],
			],
			'reglamento_resumen' => 'Disposición 241/2026 (22/09/2026, in force from 1/10/2026), General Regulation of Continental Sport Fishing of Santa Cruz, 2026/27 season. Strobel: open all year, catch and release, single hook barbed or barbless, 200 m exclusion, no motors. Barrancoso: 1/10/2026 to 3/5/2027, absolute release, 100 m exclusion, daily disinfection certificate. Personal permit through SIPESQ. Read on 30/10/2026 (Annex I in a third-party copy that matches the signed original in arts. 5, 7, 11, 12 and 16).',
			'aguas' => [
				[ 'nombre' => 'Lago Strobel', 'nota' => 'Catch and release only; no motors. Confirm the open sectors with your operator.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Barrancoso River', 'nota' => 'The lake\'s main inflow. 1 Oct 2026 to 3 May 2027, absolute catch and release.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => 'Is Jurassic Lake the real name of the lake?', 'respuesta' => 'No. The lake is called Strobel. "Jurassic Lake" is a lodge\'s registered brand, so well known that many people think it is the lake\'s name.' ],
				[ 'pregunta' => 'Can I keep the fish I catch?', 'respuesta' => 'No. Lago Strobel and the Barrancoso River are catch and release only, and in those waters you may not keep or transport any fish (Annex I and Article 12 of Disposición 241/26).' ],
				[ 'pregunta' => 'Which permit do I need and how much is it?', 'respuesta' => 'The Santa Cruz provincial permit, personal and not transferable, through SIPESQ. For non-resident foreigners in 2026/27: day AR$ 90,000, week AR$ 300,000, season AR$ 450,000 (Argentine pesos, as published by the provincial ministry on 1 October 2026). Confirm the current amount before you pay.' ],
				[ 'pregunta' => 'What hook and tackle can I use?', 'respuesta' => 'One artificial lure with a single hook, barbed or barbless (a crushed barb is the safe choice), one rod at a time and no lead (Articles 8, 10 and 15 r, and Annex I).' ],
				[ 'pregunta' => 'Can I fish from a motor boat?', 'respuesta' => 'Not on Lago Strobel: Annex I absolutely bans navigating with internal-combustion or mechanically propelled craft. In addition, fishing from a boat is banned within 200 m of river sources and mouths (Article 15 a).' ],
				[ 'pregunta' => 'Can I fish at the mouth of the Barrancoso?', 'respuesta' => 'Annex I bans fishing on both banks within 200 m of the mouths of rivers linked to Strobel, and within 100 m of the Barrancoso\'s own source and mouth. How that applies at each spot is for your operator or the fishing wardens to confirm: we do not interpret it.' ],
				[ 'pregunta' => 'When is it open?', 'respuesta' => 'Lago Strobel: all year. The Barrancoso River: 1 October 2026 to 3 May 2027. The general season runs to 30 September 2027.' ],
				[ 'pregunta' => 'How do I get there?', 'respuesta' => 'According to Jurassic Lake Lodge, by a one-hour charter flight from El Calafate on Saturdays or by a ground transfer of about six hours; Estancia Laguna Verde describes about five hours by road. From Gobernador Gregores the access is provincial route 25, 4x4 only, and we have not verified the road.' ],
				[ 'pregunta' => 'What is the disinfection certificate?', 'respuesta' => 'On the Barrancoso River, a zone of maximum vulnerability to the Didymo alga, Annex I requires you to carry the daily disinfection certificate and to wash your gear at the end of the day. Ask your lodge how it is obtained.' ],
				[ 'pregunta' => 'How much does a trip cost?', 'respuesta' => 'Lodges sell multi-night packages, per person, in US dollars. We have no current rate confirmed with any operator, so we publish none.' ],
			],
		],
	],
];

// SEO limits the site uses: title <= 60, description 70-160.
foreach ( $guias as $i => $g ) {
	$t = mb_strlen( $g['campos']['seo_title'] );
	$d = mb_strlen( $g['campos']['seo_description'] );
	if ( $t > 60 || $d > 160 || $d < 70 ) {
		WP_CLI::error( "SEO fuera de rango ($i): title $t, description $d" );
	}
}

// --- Create (Spanish first: it holds region, province, location and species) ----------------
$especies = [ 'trucha' ];
foreach ( [ 'es', 'en' ] as $i ) {
	$g      = $guias[ $i ];
	$existe = pm_buscar( 'pm_destino', 'jurassic-lake', $i );
	if ( $existe ) {
		WP_CLI::log( "jurassic-lake ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "jurassic-lake ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $g['fuentes'] ) . ' fuentes (title ' . mb_strlen( $g['campos']['seo_title'] ) . ', desc ' . mb_strlen( $g['campos']['seo_description'] ) . ')' );
	if ( ! $aplicar ) {
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
	$campos = $g['campos'] + $comun + [ 'idioma' => $i, 'fuentes' => $g['fuentes'] ];
	if ( 'es' === $i ) {
		$campos += [ 'region' => 'patagonia', 'provincia' => 'santa-cruz', 'lat' => -48.367, 'lon' => -71.2, 'especies' => $especies ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "✓ jurassic-lake ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
}

// --- Outfitters: "listado con información pública" (docs/prestadores.md) ----------------------
$prestadores = [
	[ 'Jurassic Lake Lodge', [ 'alojamiento' ], 'https://www.jurassiclake.com/', 'Lodge on the south-west shore of Lago Strobel, near the mouth of the Barrancoso River (operator\'s description). Per its own site: 10 rooms, private airstrip, charter flight from El Calafate on Saturdays.' ],
	[ 'Estancia Laguna Verde', [ 'alojamiento' ], 'https://estancialagunaverde.com/', 'Estancia and fly-fishing lodge on the south coast of Lago Strobel (operator\'s description). Sold also through travel agencies.' ],
	[ 'Tres Amigos Outfitters', [ 'operador' ], 'https://www.tresamigosoutfitters.com/jurassic-lake-and-glacier-king/', 'Outfitter that sells a trip combining Jurassic Lake and Glacier King (per its own page).' ],
];
foreach ( $prestadores as [ $nombre, $tipo, $url, $desc ] ) {
	$hay = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( $hay ) {
		WP_CLI::log( "· prestador $nombre ya existe" );
		continue;
	}
	if ( ! $aplicar ) {
		WP_CLI::log( "· prestador $nombre: se crearía (listado público, sin verificar)" );
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::warning( $pid->get_error_message() );
		continue;
	}
	$f = [
		'tipo' => $tipo, 'prest_destino' => 'jurassic-lake', 'localidad' => 'Lago Strobel, Santa Cruz', 'descripcion' => $desc,
		'prest_modalidades' => [ 'mosca' ], 'lancha' => '', 'motor' => '',
		'prest_publico' => 1, 'prest_fuente' => $url, 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0,
		'notas' => 'Listado con información pública (sitio del operador leído el 30/10/2026). Pescamapa no confirmó contacto, tarifa, idiomas ni disponibilidad. Falta contactarlo.',
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	WP_CLI::log( "✓ prestador $nombre #$pid (listado público, sin verificar)" );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
