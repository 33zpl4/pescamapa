<?php
/**
 * Esquel and Trevelin (Chubut), 4/11/2026: destination guide in Spanish (source of the shared data) and English, plus four public-information
 * outfitter cards. Everything stated comes from what was read; what is missing is marked, not hidden (owner, 4/11/2026: "build it with what we
 * have and tell me what is missing"). Facts:
 *  - Season, general rules and rules by water 2026/27: "Anexo Chubut" and "Ambientes Chubut" of reglamentodepesca.org.ar (which reproduces the
 *    Dirección de Pesca Continental's text), read on 4/11/2026 with investigar.yml in browser mode (rama investigacion, chubut-reglamento-20261104).
 *    The environments page was captured cut at 12,000 characters; the rest was extracted from its HTML. A water that is not in the list falls
 *    under annex point 5.
 *  - Permit: the Patagonian permit is valid in Chubut (Río Negro annex, read on 30/10/2026 for the Bariloche guide); values are those of the
 *    Río Negro portal already published there. The Chubut annex adds that a digital permit is managed through the DINO platform.
 *  - A discrepancy is said openly: local press says Disposición 131/2026 opened the season on 30/10/2026; the annex says 1/11/2026.
 *  - Operators: their own sites (La Pilarica, AHO Fishing Esquel, Las Pampas Lodge, Challhuaquen). None publishes a rate.
 *  - NOT read: the National Parks annex for Parque Nacional Los Alerces (Futalaufquen, Verde, Menéndez, Arrayanes) and Lago Puelo; river levels.
 * Idempotent. Dry run first ($aplicar = false). Run with wp-script.yml (script = esquel-trevelin-2026-11-04).
 */

$aplicar = true;
$hoy     = '20261104';

function pm_et_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_et_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_et_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_et_pendiente( string $t ): string {
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
$fuentes_es = [
	[ 'Reglamento de Pesca Deportiva Continental Patagónico: Anexo Chubut, temporada 2026/27 (texto de la Dirección de Pesca Continental, leído el 4/11/2026)', $u['anx'], 1 ],
	[ 'Reglamento de Pesca Deportiva Continental Patagónico: Ambientes Chubut, temporada 2026/27 (leído el 4/11/2026)', $u['amb'], 1 ],
	[ 'Reglamento de Pesca Deportiva Continental Patagónico: Anexo Río Negro, validez del permiso patagónico (leído el 30/10/2026)', $u['anrn'], 1 ],
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina: valores 2026/2027 del permiso patagónico', $u['rn'], 1 ],
	[ 'Administración de Parques Nacionales: Parque Nacional Los Alerces', $u['apn'], 1 ],
	[ 'Cholila Online: apertura de la temporada 2026/27 en Esquel, Disposición 131/2026 (prensa local)', $u['prensa'], 0 ],
	[ 'La Pilarica River & Mountain Lodge: pesca con mosca en el Carrileufú (operador)', $u['pil'], 0 ],
	[ 'AHO Fishing: Esquel y Trevelin (operador)', $u['aho'], 0 ],
	[ 'Las Pampas Lodge (operador)', $u['lpl'], 0 ],
	[ 'Challhuaquen Lodge (operador)', $u['chal'], 0 ],
];
$fuentes_en = [
	[ 'Regulation of Continental Sport Fishing of Patagonia: Chubut annex, 2026/27 season (text of the Dirección de Pesca Continental, read on 4 November 2026)', $u['anx'], 1 ],
	[ 'Regulation of Continental Sport Fishing of Patagonia: Chubut environments, 2026/27 season (read on 4 November 2026)', $u['amb'], 1 ],
	[ 'Regulation of Continental Sport Fishing of Patagonia: Río Negro annex, validity of the Patagonian permit (read on 30 October 2026)', $u['anrn'], 1 ],
	[ 'Mesa Directiva de Pesca Deportiva Zona Andina: 2026/2027 values of the Patagonian permit', $u['rn'], 1 ],
	[ 'National Parks Administration: Los Alerces National Park', $u['apn'], 1 ],
	[ 'Cholila Online: opening of the 2026/27 season in Esquel, Disposición 131/2026 (local press)', $u['prensa'], 0 ],
	[ 'La Pilarica River & Mountain Lodge: fly fishing on the Carrileufú (operator)', $u['pil'], 0 ],
	[ 'AHO Fishing: Esquel and Trevelin (operator)', $u['aho'], 0 ],
	[ 'Las Pampas Lodge (operator)', $u['lpl'], 0 ],
	[ 'Challhuaquen Lodge (operator)', $u['chal'], 0 ],
];
$filas = fn( array $f ) => array_map( fn( $x ) => [ 'nombre' => $x[0], 'url' => $x[1], 'oficial' => $x[2], 'consultada' => $hoy ], $f );

// --- Spanish -------------------------------------------------------------------------------
$cuerpo_es = implode( "\n\n", [
	pm_et_p( '<strong>Esquel</strong> y <strong>Trevelin</strong> son la base para pescar en el <strong>Futaleufú</strong>, el <strong>Carrileufú</strong>, los lagos <strong>Rosario</strong> y <strong>Fontana</strong> y los ríos del entorno del Parque Nacional Los Alerces, con Cholila y Río Pico más lejos. Esta página reúne la temporada y las reglas 2026/27 leídas del anexo oficial de Chubut, el permiso, lo que publican los lodges y lo que todavía no leímos.' ),
	pm_et_h( 'La temporada 2026/27' ),
	pm_et_p( 'Según el <strong>Anexo Chubut</strong>, la temporada general va del <strong>1 de noviembre de 2026 al 1 de mayo de 2027</strong>. La captura de la <strong>trucha de arroyo</strong> en ríos y arroyos termina el 1 de marzo de 2027: desde esa fecha hay que devolverla viva. El anexo recomienda no vadear ríos y arroyos hasta el 15 de diciembre de 2026, para proteger las camas de desove, y aclara que la autoridad puede cambiar el cronograma ante una emergencia hídrica o por criterios biológicos.' ),
	pm_et_p( 'Una aclaración: la prensa local informó que la Disposición 131/2026 habilitó la temporada desde el 30 de octubre, mientras que el anexo publicado dice 1 de noviembre. Seguimos el anexo y lo decimos; preguntale a tu guía por la fecha exacta del ambiente.' ),
	pm_et_p( 'Algunos ambientes tienen otras fechas: el Futaleufú, el lago Fontana y el Corcovado habilitan la pesca con devolución obligatoria desde el 1 de octubre hasta el comienzo de la temporada general; el <strong>lago Rosario</strong> está habilitado todo el año, con devolución obligatoria de todos los salmónidos del 1 de agosto al 31 de octubre.' ),
	pm_et_h( 'Reglas generales que leímos' ),
	pm_et_lista( [
		'<strong>Anzuelos sin rebaba</strong> en todas las modalidades y todos los ambientes. Se prohíben las botas de vadeo con <strong>suela de fieltro</strong> en toda la provincia, y hay que lavar todo el equipo (alga Didymo).',
		'<strong>Dropper:</strong> con mosca se permite solo una mosca seca y una ninfa; no se permiten dos streamers ni dos ninfas.',
		'<strong>"Mosca solamente"</strong> permite también spinning o baitcasting con buldo y mosca artificial. En <strong>"exclusivo equipo de mosca"</strong> solo se permite caña y reel de mosca, y tener señuelos de spinning (cucharas, ondulantes) en el bolso o el chaleco se considera uso: infracción y decomiso.',
		'En los ambientes que no figuran en el listado se puede sacrificar por día un salmónido, dos percas y cuatro pejerreyes, salvo en ríos y arroyos, donde la devolución es obligatoria. En las áreas de devolución obligatoria está prohibido llevar peces.',
		'Se prohíbe el plomo, los señuelos con pilas o baterías, y pescar desde flotadores o belly-boats a menos de 200 m de la boca de un río.',
		'<strong>Motores:</strong> la pesca embarcada con motor en funcionamiento solo se practica en los ambientes habilitados; en el resto, a la deriva o con el motor apagado. Están prohibidos los motores de dos tiempos carburados en los ambientes compartidos con Parques Nacionales y en los de alta sensibilidad.',
		'<strong>Guías:</strong> los guardapescas verifican que los guías y sus auxiliares estén habilitados (Resolución 072/97-OPT). Pueden usar cámaras corporales y drones.',
	] ),
	pm_et_h( 'Reglas por agua que leímos' ),
	pm_et_lista( [
		'<strong>Río Futaleufú o Grande</strong> (Parque Nacional Los Alerces, jurisdicción Chubut): todas las modalidades; pesca habilitada hasta el 30 de junio de 2027; un salmónido menor de 35 cm; un solo señuelo artificial con un único anzuelo; se prohíben la pesca con motor en funcionamiento y el trolling. Es un ambiente compartido con Parques Nacionales.',
		'<strong>Lago Rosario:</strong> todas las modalidades, todo el año; trolling habilitado; dos truchas y cuatro pejerreyes por día; devolución obligatoria dentro de los 200 m de la naciente del río Nant y Fall.',
		'<strong>Lago Fontana:</strong> todas las modalidades; trolling habilitado; tres salmónidos por día; los arroyos que le llegan se cierran a la pesca desde el 1 de marzo de 2027. <strong>Arroyo Fontana:</strong> mosca solamente y devolución obligatoria.',
		'<strong>Río Carrileufú:</strong> exclusivo equipo de mosca y devolución obligatoria.',
		'<strong>Lago Cholila:</strong> todas las modalidades; un salmónido y dos percas por día; no se permite pescar de arrastre.',
		'<strong>Río Percey:</strong> todas las modalidades, con anzuelo simple y devolución obligatoria.',
		'<strong>Río Corcovado:</strong> pesca desde el 1 de octubre con devolución obligatoria hasta el inicio de la temporada general; no navegable a motor; del lago Wintter al arroyo Las Carpas, exclusivo equipo de mosca y devolución obligatoria.',
		'<strong>Lago Engaño:</strong> exclusivo equipo de mosca y devolución obligatoria; los arroyos tributarios se cierran desde el 1 de marzo de 2027.',
		'<strong>Lago Puelo</strong> (Parque Nacional y Chubut): todas las modalidades; adhiere a la reglamentación de Parques Nacionales; pesca hasta el 31 de mayo de 2027; dos salmónidos por día.',
	] ),
	pm_et_h( 'El permiso y cuánto cuesta' ),
	pm_et_p( 'Según el anexo de Río Negro del reglamento 2026/27, el permiso patagónico vale en Río Negro, Neuquén, Chubut y Parques Nacionales. Valores 2026/27 de la Mesa Directiva Zona Andina, en pesos argentinos: <strong>extranjeros no residentes</strong>, un día $50.000, diez días $140.000, temporada $280.000; <strong>residentes en el país</strong>, un día $12.500, diez días $25.000, temporada $50.000. El trolling lleva un permiso adicional ($25.000 por día, $50.000 por diez días y $100.000 por temporada, según la prensa). El Anexo Chubut aclara que un permiso digital gestionado por la plataforma DINO se puede volver a descargar gratis si se pierde. Llevalo siempre con tu documento.' ),
	pm_et_h( 'Cuándo ir' ),
	pm_et_p( 'La Pilarica Lodge trabaja de noviembre a abril y dice que enero y febrero traen más cantidad y tamaño de peces, que noviembre y diciembre pescan bien y con menos gente, y que marzo y principios de abril pueden ser excepcionales para marrones. Es la descripción de un operador: para elegir la semana, preguntale a tu lodge.' ),
	pm_et_h( 'Cómo llegar' ),
	pm_et_p( 'AHO Fishing dice que su lodge está a 50 minutos en auto del aeropuerto de Esquel y a tres horas del de Bariloche. La Pilarica Lodge incluye los traslados desde el aeropuerto de Esquel. Las Pampas Lodge, en Río Pico, queda a unas dos horas y media al sur del aeropuerto de Esquel, según su sitio. Son los tiempos de cada operador.' ),
	pm_et_pendiente( 'las reglas del Parque Nacional Los Alerces (lagos Futalaufquen, Verde, Menéndez y Arrayanes), que están en el anexo de Parques Nacionales y no lo leímos; las reglas de Lago Puelo en el anexo del Parque; calendario por mes con una fuente técnica; alturas del río; foto con su crédito; guías independientes.' ),
	pm_et_h( 'Lodges y operadores' ),
	pm_et_p( 'Abajo están los que encontramos con información pública; todavía no los contactamos, por eso figuran marcados. Una tarifa se muestra solo cuando el operador la publica con su temporada, y ninguno de estos la publica.' ),
] );

// --- English -------------------------------------------------------------------------------
$cuerpo_en = implode( "\n\n", [
	pm_et_p( '<strong>Esquel</strong> and <strong>Trevelin</strong> are the base for fishing the <strong>Futaleufú</strong>, the <strong>Carrileufú</strong>, lakes <strong>Rosario</strong> and <strong>Fontana</strong> and the rivers around Los Alerces National Park, with Cholila and Río Pico farther away. This page brings together the 2026/27 season and rules read from the official Chubut annex, the permit, what the lodges publish and what we have not read yet.' ),
	pm_et_h( 'The 2026/27 season' ),
	pm_et_p( 'According to the <strong>Chubut Annex</strong>, the general season runs from <strong>1 November 2026 to 1 May 2027</strong>. The catch of <strong>brook trout</strong> in rivers and streams ends on 1 March 2027: from then on it must be released alive. The annex recommends not wading rivers and streams until 15 December 2026, to protect the spawning beds, and says the authority may change the schedule in a water emergency or for biological reasons.' ),
	pm_et_p( 'A note: local press reported that Disposición 131/2026 opened the season on 30 October, while the published annex says 1 November. We follow the annex and say so; ask your guide for the exact date of the water you will fish.' ),
	pm_et_p( 'Some waters have other dates: the Futaleufú, Lake Fontana and the Corcovado allow fishing with mandatory release from 1 October until the general season begins; <strong>Lake Rosario</strong> is open all year, with mandatory release of all salmonids from 1 August to 31 October.' ),
	pm_et_h( 'General rules we have read' ),
	pm_et_lista( [
		'<strong>Barbless hooks</strong> in every method and on every water. <strong>Felt-soled</strong> wading boots are banned across the province, and all gear must be washed (Didymo algae).',
		'<strong>Dropper:</strong> with a fly rod only one dry fly and one nymph are allowed; two streamers or two nymphs are not.',
		'<strong>"Fly only"</strong> also allows spinning or baitcasting with a bubble float and an artificial fly. In <strong>"exclusive fly equipment"</strong> only a fly rod and reel are allowed, and carrying spinning lures (spoons, spinners) in a bag or vest counts as using them: an offence with confiscation.',
		'On waters not in the list you may keep, per day, one salmonid, two perch and four pejerrey, except on rivers and streams, where release is mandatory. In mandatory-release areas it is banned to carry fish.',
		'Lead, lures with batteries, and fishing from floats or belly boats within 200 m of a river mouth are banned.',
		'<strong>Motors:</strong> fishing from a boat with the motor running is allowed only on waters that say so; elsewhere you drift or keep the motor off. Carburetted two-stroke motors are banned on waters shared with National Parks and on highly sensitive waters.',
		'<strong>Guides:</strong> wardens check that guides and their assistants are licensed (Resolución 072/97-OPT). They may use body cameras and drones.',
	] ),
	pm_et_h( 'Rules by water we have read' ),
	pm_et_lista( [
		'<strong>Futaleufú or Grande River</strong> (Los Alerces National Park, Chubut jurisdiction): all methods; fishing open until 30 June 2027; one salmonid under 35 cm; a single artificial lure with one hook; fishing with the motor running and trolling are banned. It is shared with National Parks.',
		'<strong>Lake Rosario:</strong> all methods, all year; trolling allowed; two trout and four pejerrey a day; mandatory release within 200 m of the source of the Nant y Fall River.',
		'<strong>Lake Fontana:</strong> all methods; trolling allowed; three salmonids a day; the streams that feed it close to fishing from 1 March 2027. <strong>Fontana stream:</strong> fly only and mandatory release.',
		'<strong>Carrileufú River:</strong> exclusive fly equipment and mandatory release.',
		'<strong>Lake Cholila:</strong> all methods; one salmonid and two perch a day; dragging is not allowed.',
		'<strong>Percey River:</strong> all methods, with a single hook and mandatory release.',
		'<strong>Corcovado River:</strong> open from 1 October with mandatory release until the general season starts; not navigable with a motor; from Lake Wintter to the Las Carpas stream, exclusive fly equipment and mandatory release.',
		'<strong>Lake Engaño:</strong> exclusive fly equipment and mandatory release; the feeder streams close from 1 March 2027.',
		'<strong>Lake Puelo</strong> (National Park and Chubut): all methods; follows the National Parks regulation; fishing until 31 May 2027; two salmonids a day.',
	] ),
	pm_et_h( 'The permit and what it costs' ),
	pm_et_p( 'According to the Río Negro annex of the 2026/27 regulation, the Patagonian permit is valid in Río Negro, Neuquén, Chubut and the National Parks. 2026/27 values from the Mesa Directiva Zona Andina, in Argentine pesos: <strong>non-resident foreigners</strong>, one day AR$ 50,000, ten days AR$ 140,000, season AR$ 280,000; <strong>residents of Argentina</strong>, one day AR$ 12,500, ten days AR$ 25,000, season AR$ 50,000. Trolling takes an extra permit (AR$ 25,000 a day, AR$ 50,000 for ten days and AR$ 100,000 for the season, according to the press). The Chubut Annex says a digital permit managed through the DINO platform can be downloaded again for free if lost. Always carry it with your ID.' ),
	pm_et_h( 'When to go' ),
	pm_et_p( 'La Pilarica Lodge works from November to April and says January and February bring more and bigger fish, November and December fish well with fewer people, and March and early April can be exceptional for browns. This is one operator\'s description: to choose your week, ask your lodge.' ),
	pm_et_h( 'Getting there' ),
	pm_et_p( 'AHO Fishing says its lodge is a 50-minute drive from Esquel airport and three hours from Bariloche airport. La Pilarica Lodge includes transfers from Esquel airport. Las Pampas Lodge, in Río Pico, is about two and a half hours south of Esquel airport, according to its website. These are each operator\'s own times.' ),
	pm_et_pendiente( 'the rules of Los Alerces National Park (lakes Futalaufquen, Verde, Menéndez and Arrayanes), which are in the National Parks annex and we have not read it; the rules of Lake Puelo in the Park annex; calendar by month with a technical source; river levels; photo with its credit; independent guides.' ),
	pm_et_h( 'Lodges and outfitters' ),
	pm_et_p( 'Below are the ones we found with public information; we have not contacted them yet, which is why they are marked. A rate is shown only when the operator publishes it with its season, and none of these does.' ),
] );

$comun = [
	'clave'       => 'esquel-trevelin',
	'autor'       => 'Equipo Pescamapa',
	'actualizado' => $hoy,
	'reglamento_autoridad' => 'Dirección de Pesca Continental de Chubut',
	'reglamento_url'       => $u['anx'],
	'reglamento_verificado' => $hoy,
];

$guias = [
	'es' => [
		'titulo' => 'Esquel y Trevelin (Chubut)', 'slug' => 'pesca-en-esquel-y-trevelin', 'cuerpo' => $cuerpo_es, 'fuentes' => $filas( $fuentes_es ),
		'campos' => [
			'h1'          => 'Pesca en Esquel y Trevelin, Chubut: reglas y permiso 2026/27',
			'bajada'      => 'El Futaleufú, el Carrileufú y los lagos Rosario y Fontana. La temporada y las reglas 2026/27 leídas del anexo oficial de Chubut, el permiso y lo que falta leer.',
			'modalidades' => "Mosca (flycast)\nSpinning y baitcasting donde se permite\nPesca embarcada\nCaptura y devolución",
			'epoca'       => 'Del 1 de noviembre de 2026 al 1 de mayo de 2027; algunos ambientes con otras fechas.',
			'seo_title'   => 'Pesca en Esquel y Trevelin, Chubut: reglas y permiso',
			'seo_description' => 'Guía de pesca en Esquel y Trevelin, Chubut: temporada y reglas 2026/27 del anexo oficial, ríos Futaleufú y Carrileufú, lagos, permiso y lodges.',
			'temporada'   => [
				[ 'meses' => '1 de octubre', 'que' => 'Futaleufú, lago Fontana y Corcovado habilitan la pesca con devolución obligatoria hasta que empiece la temporada general.' ],
				[ 'meses' => '1 de noviembre', 'que' => 'Empieza la temporada general 2026/27 (la prensa local habla del 30 de octubre).' ],
				[ 'meses' => '1 de marzo', 'que' => 'Termina la captura de trucha de arroyo en ríos y arroyos; desde entonces se devuelve viva. Algunos arroyos y ríos se cierran a la pesca.' ],
				[ 'meses' => '1 de mayo', 'que' => 'Cierra la temporada general (2027). El Futaleufú llega hasta el 30 de junio y el lago Puelo hasta el 31 de mayo.' ],
				[ 'meses' => 'Calendario por mes', 'que' => 'PENDIENTE: eclosiones, alturas y mejores meses con una fuente técnica.' ],
			],
			'reglamento_resumen' => 'Anexo Chubut y Ambientes Chubut, temporada 2026/27, leídos el 4/11/2026 en el sitio del Reglamento de Pesca Deportiva Continental Patagónico, que reproduce el texto de la Dirección de Pesca Continental. Temporada general del 1/11/2026 al 1/5/2027, con anzuelos sin rebaba y sin suela de fieltro. No leímos el anexo de Parques Nacionales (Los Alerces, Lago Puelo); los valores del permiso son los de Río Negro, confirmalos al comprar.',
			'aguas' => [
				[ 'nombre' => 'Río Futaleufú', 'nota' => 'Todas las modalidades; pesca hasta el 30 de junio de 2027; un salmónido menor de 35 cm; sin motor ni trolling.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Río Carrileufú', 'nota' => 'Exclusivo equipo de mosca y devolución obligatoria.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Lago Rosario', 'nota' => 'Habilitado todo el año; devolución obligatoria del 1 de agosto al 31 de octubre.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Lago Fontana', 'nota' => 'Todas las modalidades y trolling; tres salmónidos por día.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => '¿Cuándo es la temporada de pesca en Esquel y Trevelin?', 'respuesta' => 'Según el Anexo Chubut, la temporada general 2026/27 va del 1 de noviembre de 2026 al 1 de mayo de 2027. La prensa local dice que la Disposición 131/2026 la habilitó desde el 30 de octubre; el anexo publicado dice 1 de noviembre. Algunos ambientes tienen otras fechas: el Futaleufú llega hasta el 30 de junio de 2027 y el lago Rosario está habilitado todo el año.' ],
				[ 'pregunta' => '¿Qué permiso necesito y cuánto cuesta?', 'respuesta' => 'El permiso patagónico, que según el anexo de Río Negro vale en Río Negro, Neuquén, Chubut y Parques Nacionales. Valores 2026/27 para extranjeros no residentes: un día $50.000, diez días $140.000, temporada $280.000 (Mesa Directiva Zona Andina). El permiso digital se gestiona por la plataforma DINO y se puede volver a descargar gratis si se pierde.' ],
				[ 'pregunta' => '¿Puedo usar botas con suela de fieltro?', 'respuesta' => 'No. Están prohibidas en todos los ambientes de la provincia del Chubut, y hay que lavar todo el equipo por el alga Didymo.' ],
				[ 'pregunta' => '¿Se puede pescar con anzuelo con rebaba?', 'respuesta' => 'No. El anexo prohíbe la rebaba en cualquier tipo de anzuelo y en todas las modalidades, para favorecer la devolución de los peces.' ],
				[ 'pregunta' => '¿Qué significa "mosca solamente" y "exclusivo equipo de mosca"?', 'respuesta' => 'En "mosca solamente" hay que usar una mosca artificial y se permite spinning o baitcasting con buldo o boyita. En "exclusivo equipo de mosca" solo se permite caña y reel de mosca, y llevar señuelos de spinning en el bolso o el chaleco ya se considera uso: es infracción y decomiso.' ],
				[ 'pregunta' => '¿Cuántos peces puedo llevarme?', 'respuesta' => 'En los ambientes que no están en el listado, un salmónido, dos percas y cuatro pejerreyes por día, salvo en ríos y arroyos, donde la devolución es obligatoria. Cada ambiente con regla propia tiene su límite: por ejemplo, un salmónido menor de 35 cm en el Futaleufú y dos truchas y cuatro pejerreyes en el lago Rosario.' ],
				[ 'pregunta' => '¿Cuáles son las reglas del río Futaleufú?', 'respuesta' => 'En el tramo del Parque Nacional Los Alerces con jurisdicción de Chubut se permiten todas las modalidades, con un solo señuelo artificial y un único anzuelo; está prohibida la pesca con motor en funcionamiento y el trolling; se puede sacrificar un salmónido menor de 35 cm; la pesca llega hasta el 30 de junio de 2027. Es un ambiente compartido con Parques Nacionales.' ],
				[ 'pregunta' => '¿Dónde puedo pescar solo con mosca?', 'respuesta' => 'El río Carrileufú y el lago Engaño son exclusivo equipo de mosca con devolución obligatoria; el arroyo Fontana es mosca solamente. Verificá el ambiente exacto en el anexo del año.' ],
				[ 'pregunta' => '¿Qué reglas tiene el Parque Nacional Los Alerces?', 'respuesta' => 'Todavía no leímos el anexo de Parques Nacionales, así que no publicamos las reglas de los lagos Futalaufquen, Verde, Menéndez ni Arrayanes. El Futaleufú dentro del Parque sí figura en el anexo de Chubut, como ambiente compartido. Consultá a tu guía o a la administración del Parque.' ],
				[ 'pregunta' => '¿Cuánto cuesta una semana en un lodge de la zona?', 'respuesta' => 'No lo podemos decir con una fuente del propio operador: La Pilarica Lodge, AHO Fishing, Las Pampas Lodge y Challhuaquen no publican tarifas en los sitios que leímos. La Pilarica dice que su semana incluye siete noches, todas las comidas y bebidas, seis días de pesca guiada, traslados desde el aeropuerto de Esquel, entradas al parque y licencias de pesca; no incluye vuelos, equipo ni propinas.' ],
			],
		],
	],
	'en' => [
		'titulo' => 'Esquel and Trevelin (Chubut)', 'slug' => 'fishing-in-esquel-and-trevelin', 'cuerpo' => $cuerpo_en, 'fuentes' => $filas( $fuentes_en ),
		'campos' => [
			'h1'          => 'Fishing in Esquel and Trevelin, Chubut: rules and 2026/27 permit',
			'bajada'      => 'The Futaleufú, the Carrileufú and lakes Rosario and Fontana. The 2026/27 season and rules read from the official Chubut annex, the permit and what is still to read.',
			'modalidades' => "Fly fishing\nSpinning and baitcasting where allowed\nFishing from a boat\nCatch and release",
			'epoca'       => '1 November 2026 to 1 May 2027; some waters have other dates.',
			'seo_title'   => 'Fishing in Esquel and Trevelin, Chubut: rules, permit',
			'seo_description' => 'Fishing guide to Esquel and Trevelin, Chubut: 2026/27 season and rules from the official annex, Futaleufú and Carrileufú rivers, lakes, permit and lodges.',
			'temporada'   => [
				[ 'meses' => '1 October', 'que' => 'Futaleufú, Lake Fontana and the Corcovado allow fishing with mandatory release until the general season begins.' ],
				[ 'meses' => '1 November', 'que' => 'The general 2026/27 season starts (local press says 30 October).' ],
				[ 'meses' => '1 March', 'que' => 'The catch of brook trout in rivers and streams ends; from then on it is released alive. Some streams and rivers close to fishing.' ],
				[ 'meses' => '1 May', 'que' => 'The general season closes (2027). The Futaleufú runs to 30 June and Lake Puelo to 31 May.' ],
				[ 'meses' => 'Calendar by month', 'que' => 'PENDIENTE: hatches, river levels and best months, with a technical source.' ],
			],
			'reglamento_resumen' => 'Chubut Annex and Chubut Environments, 2026/27 season, read on 4 November 2026 on the site of the Regulation of Continental Sport Fishing of Patagonia, which reproduces the text of the Dirección de Pesca Continental. General season from 1 November 2026 to 1 May 2027, with barbless hooks and no felt soles. We did not read the National Parks annex (Los Alerces, Lake Puelo); permit values are Río Negro\'s, confirm them when you buy.',
			'aguas' => [
				[ 'nombre' => 'Futaleufú River', 'nota' => 'All methods; fishing until 30 June 2027; one salmonid under 35 cm; no motor or trolling.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Carrileufú River', 'nota' => 'Exclusive fly equipment and mandatory release.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Lake Rosario', 'nota' => 'Open all year; mandatory release from 1 August to 31 October.', 'lat' => '', 'lon' => '' ],
				[ 'nombre' => 'Lake Fontana', 'nota' => 'All methods and trolling; three salmonids a day.', 'lat' => '', 'lon' => '' ],
			],
			'faqs' => [
				[ 'pregunta' => 'When is the fishing season in Esquel and Trevelin?', 'respuesta' => 'According to the Chubut Annex, the general 2026/27 season runs from 1 November 2026 to 1 May 2027. Local press says Disposición 131/2026 opened it from 30 October; the published annex says 1 November. Some waters have other dates: the Futaleufú runs to 30 June 2027 and Lake Rosario is open all year.' ],
				[ 'pregunta' => 'Which permit do I need and how much is it?', 'respuesta' => 'The Patagonian permit, which according to the Río Negro annex is valid in Río Negro, Neuquén, Chubut and the National Parks. 2026/27 values for non-resident foreigners: one day AR$ 50,000, ten days AR$ 140,000, season AR$ 280,000 (Mesa Directiva Zona Andina). A digital permit is managed through the DINO platform and can be downloaded again for free if lost.' ],
				[ 'pregunta' => 'Can I wear felt-soled boots?', 'respuesta' => 'No. They are banned on every water in Chubut province, and all gear must be washed because of the Didymo alga.' ],
				[ 'pregunta' => 'Can I fish with a barbed hook?', 'respuesta' => 'No. The annex bans barbs on any kind of hook and in every method, to favour releasing the fish.' ],
				[ 'pregunta' => 'What do "fly only" and "exclusive fly equipment" mean?', 'respuesta' => 'In "fly only" you must use an artificial fly and may use spinning or baitcasting with a bubble float. In "exclusive fly equipment" only a fly rod and reel are allowed, and carrying spinning lures in a bag or vest already counts as using them: an offence with confiscation.' ],
				[ 'pregunta' => 'How many fish can I keep?', 'respuesta' => 'On waters not in the list, one salmonid, two perch and four pejerrey a day, except on rivers and streams, where release is mandatory. Each water with its own rule has its own limit: for example one salmonid under 35 cm on the Futaleufú and two trout and four pejerrey on Lake Rosario.' ],
				[ 'pregunta' => 'What are the rules on the Futaleufú River?', 'respuesta' => 'On the stretch inside Los Alerces National Park under Chubut jurisdiction all methods are allowed, with a single artificial lure and one hook; fishing with the motor running and trolling are banned; you may keep one salmonid under 35 cm; fishing runs until 30 June 2027. It is shared with National Parks.' ],
				[ 'pregunta' => 'Where can I fish with a fly only?', 'respuesta' => 'The Carrileufú River and Lake Engaño are exclusive fly equipment with mandatory release; the Fontana stream is fly only. Check the exact water in the current year\'s annex.' ],
				[ 'pregunta' => 'What are the rules in Los Alerces National Park?', 'respuesta' => 'We have not read the National Parks annex yet, so we publish no rules for lakes Futalaufquen, Verde, Menéndez or Arrayanes. The Futaleufú inside the Park does appear in the Chubut annex, as a shared water. Ask your guide or the Park administration.' ],
				[ 'pregunta' => 'How much does a week at a lodge in the area cost?', 'respuesta' => 'We cannot say from the operator\'s own source: La Pilarica Lodge, AHO Fishing, Las Pampas Lodge and Challhuaquen publish no rates on the sites we read. La Pilarica says its week includes seven nights, all meals and drinks, six days of guided fishing, transfers from Esquel airport, park entries and fishing licences; it does not include flights, equipment or tips.' ],
			],
		],
	],
];

foreach ( $guias as $i => $g ) {
	$t = mb_strlen( $g['campos']['seo_title'] );
	$d = mb_strlen( $g['campos']['seo_description'] );
	if ( $t > 60 || $d > 160 || $d < 70 ) {
		WP_CLI::error( "SEO fuera de rango ($i): title $t, description $d" );
	}
	if ( 10 !== count( $g['campos']['faqs'] ) ) {
		WP_CLI::error( "Se esperaban 10 preguntas ($i)" );
	}
}

foreach ( [ 'es', 'en' ] as $i ) {
	$g = $guias[ $i ];
	if ( pm_buscar( 'pm_destino', 'esquel-trevelin', $i ) ) {
		WP_CLI::log( "esquel-trevelin ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "esquel-trevelin ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $g['fuentes'] ) . ' fuentes (title ' . mb_strlen( $g['campos']['seo_title'] ) . ', description ' . mb_strlen( $g['campos']['seo_description'] ) . ')' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => $g['titulo'], 'post_name' => $g['slug'], 'post_content' => wp_slash( $g['cuerpo'] ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i, 'fuentes' => $g['fuentes'] ];
	if ( 'es' === $i ) {
		$campos += [
			'region' => 'patagonia', 'provincia' => 'chubut', 'lat' => -42.91, 'lon' => -71.32, 'especies' => [ 'trucha' ],
			'temporada_abre' => '20261101', 'temporada_cierra' => '20270501', 'pico_meses' => '1,2', 'pico_quien' => 'La Pilarica Lodge',
		];
	}
	$campos['temporada_nota'] = 'es' === $i
		? 'Ambientes con otras fechas: el Futaleufú hasta el 30 de junio de 2027, el lago Puelo hasta el 31 de mayo de 2027 y la trucha de arroyo en ríos y arroyos hasta el 1 de marzo de 2027.'
		: 'Waters with other dates: the Futaleufú until 30 June 2027, Lake Puelo until 31 May 2027 and brook trout in rivers and streams until 1 March 2027.';
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	WP_CLI::log( "✓ esquel-trevelin ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, ' . count( pm_filas( 'temporada', $id ) ) . ' filas de temporada leídas de vuelta' );
}

// --- Outfitters: "listado con información pública" (docs/prestadores.md) ----------------------
$nota_interna = 'Listado con información pública (sitio leído el 4/11/2026 con investigar.yml). Pescamapa no confirmó contacto, tarifa, disponibilidad ni habilitación. No publica tarifa en el sitio que leímos. Falta contactarlo.';
$prestadores  = [
	[
		'nombre' => 'La Pilarica River & Mountain Lodge', 'tipo' => [ 'alojamiento', 'operador' ], 'fuente' => $u['pil'], 'loc' => 'Cholila, Chubut', 'cap' => 0,
		'es' => 'Lodge de pesca con mosca en Cholila que pesca el río Carrileufú y los ríos y lagos alrededor del Parque Nacional Los Alerces, sobre todo desde el bote y a pie. Trabaja de noviembre a abril. Según su sitio, la semana incluye siete noches en una de cinco habitaciones, todas las comidas y bebidas, seis días de pesca guiada, traslados desde el aeropuerto de Esquel, entradas al parque y licencias de pesca; no incluye vuelos, equipo ni propinas. Ofrece una tarifa menor para acompañantes que no pescan. No publica tarifa: la cotiza con tus fechas.',
		'en' => 'Fly-fishing lodge in Cholila that fishes the Carrileufú River and the rivers and lakes around Los Alerces National Park, mostly from a boat and on foot. It works from November to April. According to its website the week includes seven nights in one of five rooms, all meals and drinks, six days of guided fishing, transfers from Esquel airport, park entries and fishing licences; it does not include flights, equipment or tips. It offers a lower rate for non-fishing companions. It publishes no rate: it quotes it for your dates.',
		'incluye' => [ 'alojamiento', 'comida', 'bebidas', 'traslados', 'licencia' ], 'equipo' => [], 'idiomas' => [ 'en', 'es' ],
	],
	[
		'nombre' => 'AHO Fishing, Esquel y Trevelin', 'tipo' => [ 'operador' ], 'fuente' => $u['aho'], 'loc' => 'Esquel, Chubut', 'cap' => 0,
		'es' => 'Programa de pesca en un lodge a 50 minutos en auto del aeropuerto de Esquel y a tres horas del de Bariloche, según su sitio. Tiene seis habitaciones dobles con baño privado y servicio completo de alojamiento. Su sitio describe programas en Esquel y Trevelin con varios ríos de la zona. No publica tarifa.',
		'en' => 'Fishing programme at a lodge 50 minutes by car from Esquel airport and three hours from Bariloche airport, according to its website. It has six double rooms with private bathrooms and full lodging service. Its website describes programmes in Esquel and Trevelin on several rivers of the area. It publishes no rate.',
		'incluye' => [ 'alojamiento' ], 'equipo' => [], 'idiomas' => [ 'en' ],
	],
	[
		'nombre' => 'Las Pampas Lodge', 'tipo' => [ 'alojamiento' ], 'fuente' => $u['lpl'], 'loc' => 'Río Pico, Chubut', 'cap' => 0,
		'es' => 'Lodge de troncos y piedra en Río Pico, a unas dos horas y media al sur del aeropuerto de Esquel según su sitio, con habitaciones dobles con baño privado. Ofrece pesca con mosca de trucha marrón, arcoíris y de arroyo en arroyos de manantial, ríos y lagos de montaña, con almuerzo en el agua que sirven los guías. No publica programas ni tarifas: pide que lo consultes.',
		'en' => 'Log-and-stone lodge in Río Pico, about two and a half hours south of Esquel airport according to its website, with double rooms and private bathrooms. It offers fly fishing for brown, rainbow and brook trout on spring creeks, rivers and mountain lakes, with a lunch served on the water by the guides. It publishes no programmes or rates: it asks you to get in touch.',
		'incluye' => [ 'alojamiento', 'comida' ], 'equipo' => [], 'idiomas' => [ 'en' ],
	],
	[
		'nombre' => 'Challhuaquen Lodge', 'tipo' => [ 'alojamiento' ], 'fuente' => $u['chal'], 'loc' => 'Trevelin, Chubut', 'cap' => 0,
		'es' => 'Lodge de pesca en Trevelin, sobre la ruta 259, km 59, según su sitio. El sitio que leímos no publica programas, capacidad ni tarifas.',
		'en' => 'Fishing lodge in Trevelin, on Route 259, km 59, according to its website. The site we read publishes no programmes, capacity or rates.',
		'incluye' => [], 'equipo' => [], 'idiomas' => [ 'es' ],
	],
];
$creados = [];
foreach ( $prestadores as $p ) {
	if ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $p['nombre'], 'numberposts' => 1, 'fields' => 'ids' ] ) ) {
		WP_CLI::log( "· prestador {$p['nombre']} ya existe" );
		continue;
	}
	if ( ! $aplicar ) {
		WP_CLI::log( "· prestador {$p['nombre']}: se crearía (listado público, sin verificar, sin precio)" );
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $p['nombre'] ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	$f = [
		'tipo' => $p['tipo'], 'prest_destino' => 'esquel-trevelin', 'localidad' => $p['loc'],
		'descripcion' => $p['es'], 'descripcion_en' => $p['en'],
		'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha' ], 'equipo' => $p['equipo'], 'capacidad' => $p['cap'],
		'lancha' => '', 'motor' => '', 'prest_idiomas' => $p['idiomas'], 'incluye' => $p['incluye'],
		'precio' => '', 'precio_fecha' => '',
		'prest_publico' => 1, 'prest_fuente' => $p['fuente'], 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'web' => $p['fuente'], 'notas' => $nota_interna,
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	$creados[ $p['nombre'] ] = $pid;
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok = true;
foreach ( $creados as $nombre => $pid ) {
	$r = null;
	foreach ( pm_prestadores_destino( 'esquel-trevelin' ) as $x ) {
		if ( (int) $x['id'] === (int) $pid ) {
			$r = $x;
		}
	}
	$bien = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && '' === $r['precio'];
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$pid " . ( $r ? 'aparece en la lista de esquel-trevelin, sin precio' : 'no aparece en la lista de esquel-trevelin' ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna ficha no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
