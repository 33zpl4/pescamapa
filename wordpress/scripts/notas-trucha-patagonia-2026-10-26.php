<?php
/**
 * Trout, 26/10/2026: the long-form note "When to fish trout in Patagonia" (es, en, pt) and the species
 * page "Trucha" (es, en, pt). Built from docs/borradores/cuando-pescar-trucha-patagonia.md (owner OK:
 * "translate and publish"; the Nov-Dec river-type explanation and the Esquel / Santa Cruz precisions
 * come from Gemini without a source of their own and are written as "our reading"). Same rules as the
 * dorado and surubí notes: evergreen body + dated yearly box, no named outfitters linked, form as the only CTA.
 *
 * Idempotent: a note or species page that exists (same clave + language) is never touched.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = notas-trucha-patagonia-2026-10-26).
 */

$aplicar = false;
$hoy     = '20261026';

function pm_n_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_n_h2( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_n_h3( string $t ): string {
	return "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$t}</h3>\n<!-- /wp:heading -->";
}
function pm_n_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_n_tabla( array $cabecera, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
/** The dated yearly box: the only place where closures and event dates live. */
function pm_n_caja( string $titulo, array $items, string $pie ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph -->\n<p><em>{$pie}</em></p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->";
}

// Dorado note content, es/en/pt. Placeholders: {goya} {esquina} {paso} {lapaz} {viaje} {especie} are resolved per language.
function pm_sp_pend_f( string $t ): string {
	return "<!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph -->";
}

$trucha = [];

$trucha['es'] = [
	'titulo' => 'Cuándo pescar trucha en la Patagonia: meses, río, viento y temporada',
	'slug'   => 'cuando-pescar-trucha-patagonia',
	'bajada' => 'La temporada va de noviembre a abril-mayo, pero la mejor ventana depende de la zona y del tipo de río: elegí primero el río y después el mes.',
	'seo_title' => 'Cuándo pescar trucha en la Patagonia: mejores meses | Pescamapa',
	'seo_description' => 'Los mejores meses para pescar trucha en la Patagonia según la zona (Neuquén, Esquel, Santa Cruz y Tierra del Fuego), el tipo de río, el viento y la temporada.',
	'datos' => [
		'La temporada abre en noviembre y cierra entre abril y mayo; las fechas cambian por ambiente y por año.',
		'En el norte, enero a marzo es lo más pedido; en Santa Cruz, el comienzo de la temporada y el otoño; en Tierra del Fuego, de enero a principios de abril.',
		'En noviembre y diciembre importa el tipo de río: deshielo en los ríos grandes, agua cristalina en los arroyos de vertiente.',
		'Las truchas rinden entre 7 y 15,5 °C; por encima de 20 °C conviene dejar de pescar.',
		'Con un guía local no hace falta seguir las fechas por tu cuenta: las conoce.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Respuesta corta.</strong> La temporada de trucha en la Patagonia va de la primavera (noviembre) al otoño (abril-mayo), y la mejor ventana depende de la zona: en el norte, el verano (enero a marzo) es lo más pedido; en Santa Cruz, los operadores del lago Strobel hablan del comienzo de la temporada y del otoño; en Tierra del Fuego, la corrida de truchas de mar va de diciembre a abril, con el pico entre enero y principios de abril. <strong>Se puede pescar en todo el período</strong>: cambian el agua, el viento y la técnica.' ),
		pm_n_h2( '¿Cuándo es la temporada y qué meses son los mejores?' ),
		pm_n_p( 'La temporada oficial arranca a comienzos de noviembre y cierra entre abril y mayo, con fechas distintas para cada ambiente. Las ventanas por zona, según las fuentes:' ),
		pm_n_tabla( [ 'Zona', 'Ambientes', 'Qué dicen las fuentes' ], [
			[ '<strong>Norte</strong> (Neuquén y Río Negro)', 'Chimehuín, Malleo, Collón Curá, Limay', 'Los operadores describen la temporada como noviembre a abril, con enero a marzo como lo más pedido (<a href="https://www.yellowdogflyfishing.com/blogs/back-stage-pass/frequently-asked-questions-fly-fishing-patagonia" rel="noopener">Yellow Dog</a>). Una guía de viaje ubica noviembre y diciembre como temporada temprana, buena para la mosca seca y con poca gente, y marzo y abril como la temporada tardía, para peces más grandes (<a href="https://comunidad.espoesia.com/?p=41549" rel="noopener">guía</a>)' ],
			[ '<strong>Centro</strong> (Esquel, Chubut)', 'Futaleufú y ríos de la zona', 'Un lodge de Esquel indica temporada del 1 de noviembre al 30 de abril, con agua alta de deshielo al principio, y marzo-abril como lo mejor para la seca (<a href="https://www.aardvarkmcleod.com/autumn-highlights-at-el-encuentro-lodge-seasons/" rel="noopener">lodge</a>). Las fontinalis grandes de los lagos de la zona de Río Pico se buscan en marzo y abril, cuando los machos toman la librea de celo (nuestra lectura)' ],
			[ '<strong>Sur</strong> (Santa Cruz)', 'Lago Strobel y río Barrancoso', 'La temporada del lodge va de septiembre a mayo. El comienzo (hasta mediados de enero) es el pico, con jornadas de 25 o más piques; de fines de enero a febrero baja la tasa, de 3 a 10 peces grandes por día; desde mediados de marzo hasta mayo el otoño vuelve a ser excepcional en el Barrancoso y en las caletas del lago (<a href="https://farbank.com/blogs/fly-water-travel/when-is-the-best-time-to-fish-at-jurassic-lake-lodge" rel="noopener">Farbank</a>). El Barrancoso es el único afluente del lago y depende de la lluvia: si el verano es seco, se pesca el lago, con viento fuerte (nuestra lectura)' ],
			[ '<strong>Sur</strong> (río Santa Cruz)', 'Río Santa Cruz', 'Es un río glacial, de agua verde y opaca, con una corrida de truchas arcoíris que viajan al mar (steelhead). No es de verano: la corrida se pesca en el otoño, de fines de febrero a mayo, con el pico en marzo y abril, con cañas de dos manos pesadas, y no es un destino de volumen (nuestra lectura)' ],
			[ '<strong>Tierra del Fuego</strong>', 'Río Grande', 'Los peces empiezan a entrar en noviembre y siguen entrando hasta abril; los operadores concentran lo mejor entre la primera semana de enero y la primera semana de abril. Estancia María Behety: de noviembre a abril, con pozones llenos en febrero y marzo y, a principios de abril, caudales más bajos y algunos de los peces más grandes (<a href="https://farbank.com/blogs/fly-water-travel/rio-grande-seasonal-breakdown" rel="noopener">Farbank</a>). Paul Pritchard ubica la corrida de truchas de mar entre diciembre y abril (<a href="https://www.youtube.com/watch?v=_uKgJmRCsJI" rel="noopener">video</a>)' ],
		] ),
		pm_n_p( '<strong>Nuestra lectura:</strong> la Patagonia no tiene una sola temporada porque tampoco tiene un solo río. El norte y el centro se mueven con el deshielo y el calor; Santa Cruz, con el caudal del Barrancoso; Tierra del Fuego, con la corrida de los peces que vuelven del mar. El verano (enero-febrero) es la ventana más cómoda y más reservada; el otoño es para peces más grandes.' ),
		pm_n_h2( '¿Qué pasa mes a mes?' ),
		pm_n_lista( [
			'<strong>Noviembre y diciembre: elegí el tipo de río.</strong> Las fuentes parecen contradecirse (agua alta y streamers según unos, mosca seca y poca gente según otros) porque hablan de dos tipos de río. En los <strong>ríos grandes de cordillera</strong> (el Aluminé, el Limay, el Corcovado, los tramos bajos del Chimehuín) el deshielo alcanza su pico en estas semanas: agua alta, fría y turbia, con pocas eclosiones y truchas pegadas al fondo o a las orillas lentas, y se pesca con líneas de hundimiento, ninfas y streamers. En los <strong>arroyos de vertiente</strong> (el Malleo superior en Neuquén, el arroyo Pescado en Esquel), que nacen de napas subterráneas, el agua corre cristalina, estable y filtrada, y ya hay eclosiones tempranas de efímeras: ahí sí se pesca la seca a pez visto. En <strong>lagos y bahías</strong>, hacia fines de noviembre y en diciembre, se habla de la emergencia de libélulas y damiselas, que atrae a las truchas hacia las orillas. Y la <strong>nieve del invierno</strong> cambia las fechas: en un año con poca nieve el deshielo termina a mediados de noviembre y en diciembre ya se pesca seca; en un año muy nevado se estira hasta Año Nuevo. (Nuestra lectura.) <strong>Tip para quien viaja en estas semanas:</strong> si buscás seca, apuntá a vertientes y bocas de lagos; si vas a un río grande, llevá líneas rápidas y streamers pesados. En los ríos de desove de Neuquén, el reglamento recomienda además no vadear en noviembre, para no dañar las camas, según nuestra guía de {sanmartin}.',
			'<strong>Enero y febrero.</strong> Pleno verano: agua baja y clara, y es la ventana más pedida. Un video de pesca con mosca (<a href="https://www.youtube.com/watch?v=1wjx9AstB3o" rel="noopener">Ventures Fly Co</a>) explica que las truchas están más activas entre 7 y 15,5 °C y se enlentecen por debajo de 3,3 °C; por encima de 20 °C la mortalidad en captura y devolución se dispara y conviene dejar de pescar (no es específico de la Patagonia). Los insectos terrestres, como los saltamontes y los escarabajos, aparecen de mediados de verano a comienzos de otoño, según el mismo video.',
			'<strong>Marzo y abril.</strong> Otoño: el clima baja y cambian el viento y las horas de luz; los operadores de Strobel y de Tierra del Fuego hablan de peces grandes antes del invierno. A fines de abril empiezan a cerrar hoteles y excursiones, según un <a href="https://www.youtube.com/watch?v=0H8EUaPx-E0" rel="noopener">video de viajes</a> (Samuel and Audrey).',
		] ),
		pm_n_h2( '¿Cómo influyen el viento y el río?' ),
		pm_n_lista( [
			'<strong>El viento define el equipo, sobre todo en el extremo sur.</strong> En el Río Grande, el río corre de oeste a este, igual que el viento dominante, y en el 85 % de los pozones el viento ayuda a lanzar río abajo a 45 grados, según el <a href="https://www.youtube.com/watch?v=3-FHS3zm-UA" rel="noopener">video de Fly Fishing Nation</a> en el lodge Kau Tapen. Allí se usan cañas Spey de 12 a 12,5 pies, línea 7, y de hasta 13 pies, línea 8 o 9, para mosca grande.',
			'<strong>Líneas.</strong> Cabezas Scandi para días calmos y Skagit con puntas pesadas para cortar el viento o el agua turbia (mismo video). Todd Moen (<a href="https://www.youtube.com/watch?v=2dRfHmdFmSI" rel="noopener">Catch Magazine</a>) habla de puntas de hundimiento de 12 pies de T-11 hasta 30 pies de T-20, según la profundidad.',
			'<strong>El carácter del río.</strong> Moen lo describe como un cauce no muy ancho que el viento y el clima del sur vuelven uno de los más exigentes del mundo. La trucha recién llegada del mar pelea pegada al fondo.',
			'<strong>Santa Cruz.</strong> Según el lodge, el caudal del río Barrancoso es el factor del lago Strobel: cuando hay agua, los peces entran a los pozos (Farbank).',
		] ),
		pm_n_h2( '¿Qué se puede y qué no?' ),
		pm_n_p( 'La pesca patagónica se rige por un reglamento continental común, con anexos por provincia. Cada ambiente tiene su propia fecha de apertura y cierre, y muchos se pescan solo con mosca, con anzuelo sin rebaba y con devolución obligatoria: confirmalo con tu guía o con la autoridad de la provincia. Hace falta el permiso de la temporada, y el reglamento obliga a desinfectar equipos, botas y embarcaciones por el alga didymo cuando la autoridad lo pida. Nuestra guía de {sanmartin} lo detalla para Neuquén.' ),
		pm_n_p( 'Si pescás con guía, él conoce las fechas, las zonas y las medidas del momento y te ayuda a moverte.' ),
		pm_n_caja( 'Al día · temporada y novedades, 26/10/2026', [
			'Neuquén abre la temporada 2026-2027 el 1 de noviembre de 2026; los permisos se venden online desde el 15 de octubre (<a href="https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027" rel="noopener">ANBariloche</a>, <a href="https://www.noticiasnqn.com.ar/noticias/2026/10/15/356687-neuquen-habilito-la-compra-de-permisos-para-la-nueva-temporada-de-pesca" rel="noopener">NoticiasNQN</a>).',
			'La temporada 2025-2026 cerró el 31 de mayo de 2026, con algunos ambientes abiertos después bajo reglas especiales (<a href="https://www.noticiasnqn.com.ar/amp/noticias/2026/05/31/337486-cerro-la-temporada-de-pesca-en-neuquen-pero-algunos-ambientes-seguiran-habilitados" rel="noopener">NoticiasNQN</a>).',
			'Las fechas de cada ambiente, los permisos y las tarifas cambian cada temporada: tu guía y la Dirección de Fauna de la provincia las tienen al día.',
		], 'Esta caja se actualiza una vez por año; el resto de la nota no cambia.' ),
		pm_n_h2( 'Si venís de afuera: ¿cuándo conviene ir?' ),
		pm_n_lista( [
			'<strong>Enero</strong> es la ventana más pedida: mejor clima, pero más gente y más viento hacia el sur, según un video de viajes (Samuel and Audrey), que recomienda reservar con mucha anticipación y ve a Esquel como alternativa menos saturada.',
			'<strong>Marzo</strong> y <strong>abril</strong> son para peces grandes y menos gente, pero los servicios empiezan a cerrar hacia fines de abril.',
			'En Strobel, el lodge ofrece paquetes semanales completos en el comienzo de la temporada: conviene reservar temprano (Farbank).',
		] ),
		pm_n_p( '<strong>¿Querés que te ayudemos a armar el viaje?</strong> Contanos fechas, grupo y qué buscás en <a href="{viaje}">el formulario</a> y te escribimos con opciones de guía y alojamiento, sin costo ni compromiso.' ),
		pm_n_h2( 'Nuestra lectura' ),
		pm_n_p( 'Si hubiera que quedarse con una sola idea: <strong>elegí primero la zona y el tipo de río, y después el mes</strong>. Enero-febrero es la ventana más cómoda en el norte y el centro; el comienzo de la temporada y el otoño favorecen los peces grandes en Santa Cruz; y en Tierra del Fuego, de enero a principios de abril, la corrida marca el ritmo. Y dos advertencias: en Santa Cruz el Barrancoso depende de la lluvia, y la pesca de steelhead del río Santa Cruz es de otoño, no de la temporada clásica de verano. Para más sobre la especie, mirá la <a href="{especie}">ficha de la trucha</a>.' ),
		pm_n_p( '<em>No hablamos con los operadores ni con las personas citadas, ni pescamos con ellos. Esta nota resume información pública y lo que dicen guías y operadores en sus páginas y videos; la interpretación es nuestra y puede tener errores.</em>' ),
	],
];

$trucha['en'] = [
	'titulo' => 'When to fish trout in Patagonia: months, river, wind and season',
	'slug'   => 'best-time-to-fish-trout-patagonia',
	'bajada' => 'The season runs from November to April–May, but the best window depends on the zone and the type of river: pick the river first, then the month.',
	'seo_title' => 'Best time to fish trout in Patagonia: best months | Pescamapa',
	'seo_description' => 'The best months to fish trout in Argentine Patagonia by zone (Neuquén, Esquel, Santa Cruz and Tierra del Fuego), by type of river, with wind and season notes.',
	'datos' => [
		'The season opens in November and closes between April and May; dates change by water and by year.',
		'In the north, January to March is the most requested; in Santa Cruz, the start of the season and autumn; in Tierra del Fuego, January to early April.',
		'In November and December the type of river matters: snowmelt on the big rivers, clear water on spring creeks.',
		'Trout are most active between 7 and 15.5 °C; above 20 °C it is better to stop fishing.',
		'With a local guide you do not need to track the dates yourself: they know them.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Short answer.</strong> The trout season in Patagonia runs from spring (November) to autumn (April–May), and the best window depends on the zone: in the north, summer (January to March) is the most requested; in Santa Cruz, Lago Strobel operators talk about the start of the season and autumn; in Tierra del Fuego, the sea-trout run goes from December to April, peaking from January to early April. <strong>You can fish the whole period</strong>: the water, the wind and the technique change.' ),
		pm_n_h2( 'When is the season and which months are best?' ),
		pm_n_p( 'The official season opens in early November and closes between April and May, with different dates for each water. The windows by zone, according to the sources:' ),
		pm_n_tabla( [ 'Zone', 'Waters', 'What the sources say' ], [
			[ '<strong>North</strong> (Neuquén and Río Negro)', 'Chimehuín, Malleo, Collón Curá, Limay', 'Operators describe the season as November to April, with January to March the most requested (<a href="https://www.yellowdogflyfishing.com/blogs/back-stage-pass/frequently-asked-questions-fly-fishing-patagonia" rel="noopener">Yellow Dog</a>). A travel guide places November and December as early season, good for dry flies and uncrowded, and March and April as late season, for bigger fish (<a href="https://comunidad.espoesia.com/?p=41549" rel="noopener">guide</a>)' ],
			[ '<strong>Centre</strong> (Esquel, Chubut)', 'Futaleufú and nearby rivers', 'An Esquel lodge gives a season from 1 November to 30 April, with high snowmelt water at the start, and March–April as best for dry flies (<a href="https://www.aardvarkmcleod.com/autumn-highlights-at-el-encuentro-lodge-seasons/" rel="noopener">lodge</a>). The big brook trout of the lakes around Río Pico are sought in March and April, when the males take on their spawning colours (our reading)' ],
			[ '<strong>South</strong> (Santa Cruz)', 'Lago Strobel and the Barrancoso River', 'The lodge season runs from September to May. The start (until mid-January) is the peak, with days of 25 or more hook-ups; from late January to February the rate drops, to 3 to 10 big fish a day; from mid-March to May autumn is exceptional again on the Barrancoso and in the lake’s coves (<a href="https://farbank.com/blogs/fly-water-travel/when-is-the-best-time-to-fish-at-jurassic-lake-lodge" rel="noopener">Farbank</a>). The Barrancoso is the lake’s only inflow and depends on rain: in a dry summer you fish the lake, in strong wind (our reading)' ],
			[ '<strong>South</strong> (Santa Cruz River)', 'Santa Cruz River', 'A glacial river with opaque green water and a run of rainbow trout that go to sea (steelhead). It is not a summer fishery: the run is fished in autumn, from late February to May, peaking in March and April, with heavy two-handed rods, and it is not a volume destination (our reading)' ],
			[ '<strong>Tierra del Fuego</strong>', 'Río Grande', 'Fish start entering in November and keep entering until April; operators concentrate the best between the first week of January and the first week of April. Estancia María Behety: November to April, with pools full in February and March and, in early April, lower flows and some of the biggest fish (<a href="https://farbank.com/blogs/fly-water-travel/rio-grande-seasonal-breakdown" rel="noopener">Farbank</a>). Paul Pritchard places the sea-trout run between December and April (<a href="https://www.youtube.com/watch?v=_uKgJmRCsJI" rel="noopener">video</a>)' ],
		] ),
		pm_n_p( '<strong>Our reading:</strong> Patagonia has no single season because it has no single river. The north and centre move with snowmelt and heat; Santa Cruz with the flow of the Barrancoso; Tierra del Fuego with the run of fish returning from the sea. Summer (January–February) is the most comfortable and most booked window; autumn is for bigger fish.' ),
		pm_n_h2( 'What happens month by month?' ),
		pm_n_lista( [
			'<strong>November and December: choose the type of river.</strong> The sources seem to contradict each other (high water and streamers according to some, dry flies and few people according to others) because they are talking about two types of river. On the <strong>big mountain rivers</strong> (the Aluminé, the Limay, the Corcovado, the lower stretches of the Chimehuín) snowmelt peaks in these weeks: high, cold, murky water, few hatches and trout hugging the bottom or the slow banks, so you fish sinking lines, nymphs and streamers. On the <strong>spring creeks</strong> (the upper Malleo in Neuquén, the Arroyo Pescado near Esquel), which rise from underground water, the water runs clear, steady and filtered, and early mayfly hatches are already on: that is where you sight-fish the dry fly. On <strong>lakes and bays</strong>, from late November and in December, people talk about dragonfly and damselfly emergences that draw trout to the shoreline. And the <strong>winter snowpack</strong> changes the dates: in a low-snow year the runoff ends in mid-November and by December you are fishing dries; in a heavy-snow year it stretches into the New Year. (Our reading.) <strong>Tip if you travel in these weeks:</strong> if you want dry flies, aim for spring creeks and lake mouths; if you go to a big river, bring fast lines and heavy streamers. On the Neuquén spawning rivers, the regulation also recommends not wading in November, to avoid damaging the redds, according to our guide to {sanmartin}.',
			'<strong>January and February.</strong> Midsummer: low, clear water and the most requested window. A fly-fishing video (<a href="https://www.youtube.com/watch?v=1wjx9AstB3o" rel="noopener">Ventures Fly Co</a>) explains that trout are most active between 7 and 15.5 °C and slow down below 3.3 °C; above 20 °C catch-and-release mortality spikes and it is best to stop fishing (not specific to Patagonia). Terrestrial insects, such as hoppers and beetles, show up from midsummer to early autumn, according to the same video.',
			'<strong>March and April.</strong> Autumn: the weather cools and wind and daylight change; Strobel and Tierra del Fuego operators talk about big fish before winter. From late April hotels and excursions begin to close, according to a <a href="https://www.youtube.com/watch?v=0H8EUaPx-E0" rel="noopener">travel video</a> (Samuel and Audrey).',
		] ),
		pm_n_h2( 'How do wind and river matter?' ),
		pm_n_lista( [
			'<strong>Wind defines the gear, especially in the far south.</strong> On the Río Grande the river runs west to east, like the prevailing wind, and in 85% of the pools the wind helps you cast downstream at 45 degrees, according to the <a href="https://www.youtube.com/watch?v=3-FHS3zm-UA" rel="noopener">Fly Fishing Nation video</a> at Kau Tapen lodge. There they use Spey rods of 12 to 12.5 feet, line 7, and up to 13 feet, line 8 or 9, for big flies.',
			'<strong>Lines.</strong> Scandi heads for calm days and Skagit with heavy tips to cut the wind or murky water (same video). Todd Moen (<a href="https://www.youtube.com/watch?v=2dRfHmdFmSI" rel="noopener">Catch Magazine</a>) talks about sink tips from 12 feet of T-11 up to 30 feet of T-20, depending on depth.',
			'<strong>The character of the river.</strong> Moen describes it as a not very wide channel that the southern wind and weather make one of the most demanding rivers in the world. A trout fresh from the sea fights hard on the bottom.',
			'<strong>Santa Cruz.</strong> According to the lodge, the flow of the Barrancoso River is the key at Lago Strobel: when there is water, fish move into the pools (Farbank).',
		] ),
		pm_n_h2( 'What is allowed and what is not?' ),
		pm_n_p( 'Patagonian fishing is governed by a common continental regulation, with annexes by province. Each water has its own opening and closing dates, and many are fly-fishing only, with barbless hooks and mandatory release: confirm it with your guide or the provincial authority. You need the season permit, and the regulation requires disinfecting gear, boots and boats because of the didymo alga when the authority asks. Our guide to {sanmartin} details this for Neuquén.' ),
		pm_n_p( 'If you fish with a guide, they know the dates, the areas and the rules of the moment and help you move around.' ),
		pm_n_caja( 'Up to date · season and news, 26 October 2026', [
			'Neuquén opens the 2026–2027 season on 1 November 2026; permits have been on sale online since 15 October (<a href="https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027" rel="noopener">ANBariloche</a>, <a href="https://www.noticiasnqn.com.ar/noticias/2026/10/15/356687-neuquen-habilito-la-compra-de-permisos-para-la-nueva-temporada-de-pesca" rel="noopener">NoticiasNQN</a>, in Spanish).',
			'The 2025–2026 season closed on 31 May 2026, with some waters open afterwards under special rules (<a href="https://www.noticiasnqn.com.ar/amp/noticias/2026/05/31/337486-cerro-la-temporada-de-pesca-en-neuquen-pero-algunos-ambientes-seguiran-habilitados" rel="noopener">NoticiasNQN</a>, in Spanish).',
			'The dates of each water, permits and fees change every season: your guide and the province’s fauna authority keep them up to date.',
		], 'This box is updated once a year; the rest of the article does not change.' ),
		pm_n_h2( 'Travelling from abroad: when should you go?' ),
		pm_n_lista( [
			'<strong>January</strong> is the most requested window: the best weather, but more people and more wind towards the south, according to a travel video (Samuel and Audrey), which recommends booking well ahead and sees Esquel as a less crowded alternative.',
			'<strong>March</strong> and <strong>April</strong> are for big fish and fewer people, but services start closing towards the end of April.',
			'At Strobel, the lodge offers full-week packages at the start of the season: book early (Farbank).',
		] ),
		pm_n_p( '<strong>Want us to help plan the trip?</strong> Tell us your dates, group and what you are after in <a href="{viaje}">the form</a> and we will write back with guide and lodging options, at no cost or commitment.' ),
		pm_n_h2( 'Our take' ),
		pm_n_p( 'If you keep one idea: <strong>pick the zone and the type of river first, then the month</strong>. January–February is the most comfortable window in the north and centre; the start of the season and autumn favour big fish in Santa Cruz; and in Tierra del Fuego, from January to early April, the run sets the pace. And two warnings: in Santa Cruz the Barrancoso depends on rain, and steelhead fishing on the Santa Cruz River is an autumn fishery, not part of the classic summer season. For more on the species, see the <a href="{especie}">trout page</a>.' ),
		pm_n_p( '<em>We have not spoken with the operators or people cited, nor fished with them. This article summarises public information and what guides and operators say on their pages and in videos; the interpretation is ours and may contain errors.</em>' ),
	],
];

$trucha['pt'] = [
	'titulo' => 'Quando pescar truta na Patagônia: meses, rio, vento e temporada',
	'slug'   => 'quando-pescar-truta-patagonia',
	'bajada' => 'A temporada vai de novembro a abril-maio, mas a melhor janela depende da zona e do tipo de rio: escolha primeiro o rio e depois o mês.',
	'seo_title' => 'Quando pescar truta na Patagônia: melhores meses | Pescamapa',
	'seo_description' => 'Os melhores meses para pescar truta na Patagônia argentina por zona (Neuquén, Esquel, Santa Cruz e Terra do Fogo), tipo de rio, vento e temporada.',
	'datos' => [
		'A temporada abre em novembro e fecha entre abril e maio; as datas mudam por ambiente e por ano.',
		'No norte, janeiro a março é o mais procurado; em Santa Cruz, o início da temporada e o outono; na Terra do Fogo, de janeiro ao início de abril.',
		'Em novembro e dezembro importa o tipo de rio: degelo nos rios grandes, água cristalina nos arroios de nascente.',
		'As trutas rendem entre 7 e 15,5 °C; acima de 20 °C convém parar de pescar.',
		'Com um guia local não é preciso acompanhar as datas por conta própria: ele as conhece.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Resposta curta.</strong> A temporada de truta na Patagônia vai da primavera (novembro) ao outono (abril-maio), e a melhor janela depende da zona: no norte, o verão (janeiro a março) é o mais procurado; em Santa Cruz, os operadores do lago Strobel falam do início da temporada e do outono; na Terra do Fogo, a corrida de trutas do mar vai de dezembro a abril, com o pico de janeiro ao início de abril. <strong>Dá para pescar em todo o período</strong>: mudam a água, o vento e a técnica.' ),
		pm_n_h2( 'Quando é a temporada e quais são os melhores meses?' ),
		pm_n_p( 'A temporada oficial começa no início de novembro e fecha entre abril e maio, com datas diferentes para cada ambiente. As janelas por zona, segundo as fontes:' ),
		pm_n_tabla( [ 'Zona', 'Ambientes', 'O que dizem as fontes' ], [
			[ '<strong>Norte</strong> (Neuquén e Río Negro)', 'Chimehuín, Malleo, Collón Curá, Limay', 'Os operadores descrevem a temporada como novembro a abril, com janeiro a março como o mais procurado (<a href="https://www.yellowdogflyfishing.com/blogs/back-stage-pass/frequently-asked-questions-fly-fishing-patagonia" rel="noopener">Yellow Dog</a>). Um guia de viagem coloca novembro e dezembro como início de temporada, bom para a mosca seca e com pouca gente, e março e abril como fim de temporada, para peixes maiores (<a href="https://comunidad.espoesia.com/?p=41549" rel="noopener">guia</a>)' ],
			[ '<strong>Centro</strong> (Esquel, Chubut)', 'Futaleufú e rios da região', 'Um lodge de Esquel indica temporada de 1.º de novembro a 30 de abril, com água alta de degelo no começo, e março-abril como o melhor para a seca (<a href="https://www.aardvarkmcleod.com/autumn-highlights-at-el-encuentro-lodge-seasons/" rel="noopener">lodge</a>). As fontinalis grandes dos lagos da região de Río Pico são procuradas em março e abril, quando os machos assumem a librea de desova (nossa leitura)' ],
			[ '<strong>Sul</strong> (Santa Cruz)', 'Lago Strobel e rio Barrancoso', 'A temporada do lodge vai de setembro a maio. O início (até meados de janeiro) é o pico, com dias de 25 ou mais fisgadas; do fim de janeiro a fevereiro a taxa cai, para 3 a 10 peixes grandes por dia; de meados de março a maio o outono volta a ser excepcional no Barrancoso e nas enseadas do lago (<a href="https://farbank.com/blogs/fly-water-travel/when-is-the-best-time-to-fish-at-jurassic-lake-lodge" rel="noopener">Farbank</a>). O Barrancoso é o único afluente do lago e depende da chuva: num verão seco se pesca o lago, com vento forte (nossa leitura)' ],
			[ '<strong>Sul</strong> (rio Santa Cruz)', 'Rio Santa Cruz', 'É um rio glacial, de água verde e opaca, com uma corrida de trutas arco-íris que vão ao mar (steelhead). Não é de verão: a corrida se pesca no outono, do fim de fevereiro a maio, com o pico em março e abril, com varas de duas mãos pesadas, e não é um destino de volume (nossa leitura)' ],
			[ '<strong>Terra do Fogo</strong>', 'Río Grande', 'Os peixes começam a entrar em novembro e continuam entrando até abril; os operadores concentram o melhor entre a primeira semana de janeiro e a primeira semana de abril. Estancia María Behety: de novembro a abril, com poços cheios em fevereiro e março e, no início de abril, vazões menores e alguns dos maiores peixes (<a href="https://farbank.com/blogs/fly-water-travel/rio-grande-seasonal-breakdown" rel="noopener">Farbank</a>). Paul Pritchard coloca a corrida de trutas do mar entre dezembro e abril (<a href="https://www.youtube.com/watch?v=_uKgJmRCsJI" rel="noopener">vídeo</a>)' ],
		] ),
		pm_n_p( '<strong>Nossa leitura:</strong> a Patagônia não tem uma única temporada porque também não tem um único rio. O norte e o centro se movem com o degelo e o calor; Santa Cruz, com a vazão do Barrancoso; a Terra do Fogo, com a corrida dos peixes que voltam do mar. O verão (janeiro-fevereiro) é a janela mais confortável e mais reservada; o outono é para peixes maiores.' ),
		pm_n_h2( 'O que acontece mês a mês?' ),
		pm_n_lista( [
			'<strong>Novembro e dezembro: escolha o tipo de rio.</strong> As fontes parecem se contradizer (água alta e streamers segundo uns, mosca seca e pouca gente segundo outros) porque falam de dois tipos de rio. Nos <strong>rios grandes de cordilheira</strong> (o Aluminé, o Limay, o Corcovado, os trechos baixos do Chimehuín) o degelo chega ao pico nessas semanas: água alta, fria e turva, poucas eclosões e trutas coladas no fundo ou nas margens lentas, e se pesca com linhas de afundamento, ninfas e streamers. Nos <strong>arroios de nascente</strong> (o Malleo superior em Neuquén, o arroio Pescado em Esquel), que nascem de lençóis subterrâneos, a água corre cristalina, estável e filtrada, e já há eclosões precoces de efêmeras: ali sim se pesca a seca a olho. Em <strong>lagos e baías</strong>, do fim de novembro e em dezembro, fala-se da emergência de libélulas e donzelinhas, que atrai as trutas para as margens. E a <strong>neve do inverno</strong> muda as datas: num ano com pouca neve o degelo termina em meados de novembro e em dezembro já se pesca seca; num ano muito nevado se estende até o Ano-Novo. (Nossa leitura.) <strong>Dica para quem viaja nessas semanas:</strong> se quer seca, mire as nascentes e as bocas de lagos; se vai a um rio grande, leve linhas rápidas e streamers pesados. Nos rios de desova de Neuquén, o regulamento recomenda ainda não vadear em novembro, para não danificar os ninhos, segundo o nosso guia de {sanmartin}.',
			'<strong>Janeiro e fevereiro.</strong> Pleno verão: água baixa e clara, e a janela mais procurada. Um vídeo de pesca com mosca (<a href="https://www.youtube.com/watch?v=1wjx9AstB3o" rel="noopener">Ventures Fly Co</a>) explica que as trutas estão mais ativas entre 7 e 15,5 °C e desaceleram abaixo de 3,3 °C; acima de 20 °C a mortalidade na captura e soltura dispara e convém parar de pescar (não é específico da Patagônia). Os insetos terrestres, como gafanhotos e besouros, aparecem de meados do verão ao início do outono, segundo o mesmo vídeo.',
			'<strong>Março e abril.</strong> Outono: o clima esfria e mudam o vento e as horas de luz; os operadores de Strobel e da Terra do Fogo falam de peixes grandes antes do inverno. No fim de abril começam a fechar hotéis e excursões, segundo um <a href="https://www.youtube.com/watch?v=0H8EUaPx-E0" rel="noopener">vídeo de viagens</a> (Samuel and Audrey).',
		] ),
		pm_n_h2( 'Como influenciam o vento e o rio?' ),
		pm_n_lista( [
			'<strong>O vento define o equipamento, sobretudo no extremo sul.</strong> No Río Grande, o rio corre de oeste a leste, como o vento dominante, e em 85% dos poços o vento ajuda a lançar rio abaixo a 45 graus, segundo o <a href="https://www.youtube.com/watch?v=3-FHS3zm-UA" rel="noopener">vídeo da Fly Fishing Nation</a> no lodge Kau Tapen. Ali usam varas Spey de 12 a 12,5 pés, linha 7, e de até 13 pés, linha 8 ou 9, para moscas grandes.',
			'<strong>Linhas.</strong> Cabeças Scandi para dias calmos e Skagit com pontas pesadas para cortar o vento ou a água turva (mesmo vídeo). Todd Moen (<a href="https://www.youtube.com/watch?v=2dRfHmdFmSI" rel="noopener">Catch Magazine</a>) fala de pontas de afundamento de 12 pés de T-11 até 30 pés de T-20, conforme a profundidade.',
			'<strong>O caráter do rio.</strong> Moen o descreve como um leito não muito largo que o vento e o clima do sul tornam um dos rios mais exigentes do mundo. A truta recém-chegada do mar briga colada no fundo.',
			'<strong>Santa Cruz.</strong> Segundo o lodge, a vazão do rio Barrancoso é o fator do lago Strobel: quando há água, os peixes entram nos poços (Farbank).',
		] ),
		pm_n_h2( 'O que se pode e o que não se pode?' ),
		pm_n_p( 'A pesca patagônica é regida por um regulamento continental comum, com anexos por província. Cada ambiente tem suas próprias datas de abertura e fechamento, e muitos se pescam só com mosca, com anzol sem farpa e com soltura obrigatória: confirme com seu guia ou com a autoridade da província. É preciso o permiso da temporada, e o regulamento obriga a desinfetar equipamentos, botas e embarcações por causa da alga didymo quando a autoridade pedir. O nosso guia de {sanmartin} detalha isso para Neuquén.' ),
		pm_n_p( 'Se você pesca com guia, ele conhece as datas, as zonas e as medidas do momento e ajuda você a se orientar.' ),
		pm_n_caja( 'Em dia · temporada e novidades, 26/10/2026', [
			'Neuquén abre a temporada 2026-2027 em 1.º de novembro de 2026; as permissões estão à venda online desde 15 de outubro (<a href="https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027" rel="noopener">ANBariloche</a>, <a href="https://www.noticiasnqn.com.ar/noticias/2026/10/15/356687-neuquen-habilito-la-compra-de-permisos-para-la-nueva-temporada-de-pesca" rel="noopener">NoticiasNQN</a>, em espanhol).',
			'A temporada 2025-2026 fechou em 31 de maio de 2026, com alguns ambientes abertos depois sob regras especiais (<a href="https://www.noticiasnqn.com.ar/amp/noticias/2026/05/31/337486-cerro-la-temporada-de-pesca-en-neuquen-pero-algunos-ambientes-seguiran-habilitados" rel="noopener">NoticiasNQN</a>, em espanhol).',
			'As datas de cada ambiente, as permissões e as tarifas mudam a cada temporada: seu guia e a autoridade de fauna da província as têm em dia.',
		], 'Esta caixa é atualizada uma vez por ano; o resto do texto não muda.' ),
		pm_n_h2( 'Se você vem de fora: quando convém ir?' ),
		pm_n_lista( [
			'<strong>Janeiro</strong> é a janela mais procurada: melhor clima, mas mais gente e mais vento rumo ao sul, segundo um vídeo de viagens (Samuel and Audrey), que recomenda reservar com muita antecedência e vê Esquel como alternativa menos lotada.',
			'<strong>Março</strong> e <strong>abril</strong> são para peixes grandes e menos gente, mas os serviços começam a fechar no fim de abril.',
			'Em Strobel, o lodge oferece pacotes semanais completos no início da temporada: convém reservar cedo (Farbank).',
		] ),
		pm_n_p( '<strong>Quer ajuda para montar a viagem?</strong> Conte as datas, o grupo e o que procura no <a href="{viaje}">formulário</a> e escrevemos com opções de guia e hospedagem, sem custo nem compromisso.' ),
		pm_n_h2( 'Nossa leitura' ),
		pm_n_p( 'Se for para ficar com uma ideia só: <strong>escolha primeiro a zona e o tipo de rio, e depois o mês</strong>. Janeiro-fevereiro é a janela mais confortável no norte e no centro; o início da temporada e o outono favorecem os peixes grandes em Santa Cruz; e na Terra do Fogo, de janeiro ao início de abril, a corrida dita o ritmo. E dois avisos: em Santa Cruz o Barrancoso depende da chuva, e a pesca de steelhead do rio Santa Cruz é de outono, não da temporada clássica de verão. Para saber mais sobre a espécie, veja a <a href="{especie}">página da truta</a>.' ),
		pm_n_p( '<em>Não falamos com os operadores nem com as pessoas citadas, nem pescamos com eles. Este texto resume informação pública e o que dizem guias e operadores em suas páginas e vídeos; a interpretação é nossa e pode conter erros.</em>' ),
	],
];

function pm_f_datos( string $titulo, array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"datos\"} -->\n<div class=\"wp-block-group datos\"><!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">{$titulo}</h2>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list --></div>\n<!-- /wp:group -->";
}

$ficha = [];
$ficha['es'] = [
	'titulo' => 'Trucha', 'slug' => 'trucha',
	'cientifico' => 'Oncorhynchus mykiss, Salmo trutta, Salvelinus fontinalis',
	'resumen' => 'Trucha arcoíris, marrón y de arroyo: las especies de la pesca con mosca en la Patagonia.',
	'seo_title' => 'Trucha en la Patagonia: especies, tamaño y reglas | Pescamapa',
	'seo_description' => 'Las truchas de la pesca patagónica (arcoíris, marrón y fontinalis): tamaños, la trucha marrón de mar del Río Grande, temperatura ideal y reglas.',
	'cuerpo' => [
		pm_f_datos( 'Datos clave', [
			'<strong>Especies que se pescan en la Patagonia:</strong> trucha arcoíris (<em>Oncorhynchus mykiss</em>), trucha marrón (<em>Salmo trutta</em>) y trucha de arroyo o fontinalis (<em>Salvelinus fontinalis</em>).',
			'<strong>Trucha marrón de mar (Río Grande, Tierra del Fuego):</strong> promedio de unos 4,5 kg (10 libras), con ejemplares de 9 a 13,5 kg (20 a 30 libras) cada año, según Todd Moen (Catch Magazine).',
			'<strong>Temperatura:</strong> las truchas rinden entre 7 y 15,5 °C; por encima de 20 °C la mortalidad al devolverlas se dispara.',
			'<strong>Reglas:</strong> en muchos ambientes de la Patagonia se pesca solo con mosca, con anzuelo sin rebaba y con devolución obligatoria.',
		] ),
		pm_n_h2( 'Las truchas de la pesca patagónica' ),
		pm_n_p( 'La pesca con mosca en la Patagonia gira en torno a tres especies: la trucha <strong>arcoíris</strong> y la trucha <strong>marrón</strong>, que son las más buscadas, y la trucha de <strong>arroyo</strong> o fontinalis, presente en algunos lagos y ríos del centro de la región. Los ríos de cordillera, los arroyos de vertiente, los lagos y la costa atlántica de Tierra del Fuego ofrecen pescas muy distintas para la misma especie.' ),
		pm_n_h2( 'La trucha marrón de mar del Río Grande' ),
		pm_n_p( 'En el Río Grande, en Tierra del Fuego, las truchas marrones introducidas desarrollaron el hábito de migrar al océano Atlántico para alimentarse, porque en el río hay poco forraje, y vuelven con tamaños enormes. Según Todd Moen, en el <a href="https://www.youtube.com/watch?v=2dRfHmdFmSI" rel="noopener">video de Catch Magazine</a>, el promedio ronda las 10 libras (unos 4,5 kg) y cada año se registran ejemplares de 20 a 30 libras (9 a 13,5 kg). La corrida entra desde noviembre y sigue hasta abril.' ),
		pm_n_h2( 'Con qué temperatura rinden' ),
		pm_n_p( 'Según un video de pesca con mosca de <a href="https://www.youtube.com/watch?v=1wjx9AstB3o" rel="noopener">Ventures Fly Co</a>, las truchas están más activas entre 7 y 15,5 °C y casi no comen por debajo de 3,3 °C; por encima de 20 °C la mortalidad en captura y devolución se dispara por falta de oxígeno y conviene dejar de pescar. No es un dato específico de la Patagonia, pero sirve para decidir el horario y el mes.' ),
		pm_n_h2( 'Reglas para pescarla' ),
		pm_n_p( 'La pesca patagónica se rige por un reglamento continental común, con anexos por provincia: cada ambiente tiene su propia fecha de apertura y cierre, y muchos se pescan solo con mosca, con anzuelo sin rebaba y con devolución obligatoria. Hace falta el permiso de la temporada. Confirmalo con tu guía o con la autoridad de la provincia; nuestra guía de {sanmartin} lo detalla para Neuquén.' ),
		pm_n_h2( 'Cuándo pescarla' ),
		pm_n_p( 'La temporada abre en noviembre y cierra entre abril y mayo, y la mejor ventana depende de la zona y del tipo de río: mirá <a href="{nota}">cuándo pescar trucha en la Patagonia</a>.' ),
		pm_sp_pend_f( 'tamaño y peso típicos y máximo biológico de cada especie con una fuente de referencia (FishBase u otra publicación científica), y los récords de la IGFA por especie.' ),
	],
];
$ficha['en'] = [
	'titulo' => 'Trout', 'slug' => 'trout',
	'cientifico' => 'Oncorhynchus mykiss, Salmo trutta, Salvelinus fontinalis',
	'resumen' => 'Rainbow, brown and brook trout: the fly-fishing species of Patagonia.',
	'seo_title' => 'Trout in Patagonia: species, size and rules | Pescamapa',
	'seo_description' => 'The trout of Patagonian fly fishing (rainbow, brown and brook): sizes, the sea-run brown trout of the Río Grande, ideal water temperature and rules.',
	'cuerpo' => [
		pm_f_datos( 'Key facts', [
			'<strong>Species fished in Patagonia:</strong> rainbow trout (<em>Oncorhynchus mykiss</em>), brown trout (<em>Salmo trutta</em>) and brook trout (<em>Salvelinus fontinalis</em>).',
			'<strong>Sea-run brown trout (Río Grande, Tierra del Fuego):</strong> an average of about 4.5 kg (10 lb), with fish of 9 to 13.5 kg (20 to 30 lb) every year, according to Todd Moen (Catch Magazine).',
			'<strong>Temperature:</strong> trout are most active between 7 and 15.5 °C; above 20 °C mortality after release spikes.',
			'<strong>Rules:</strong> many Patagonian waters are fly-fishing only, with barbless hooks and mandatory release.',
		] ),
		pm_n_h2( 'The trout of Patagonian fishing' ),
		pm_n_p( 'Fly fishing in Patagonia revolves around three species: <strong>rainbow</strong> and <strong>brown</strong> trout, the most sought after, and <strong>brook</strong> trout (fontinalis), present in some lakes and rivers in the centre of the region. Mountain rivers, spring creeks, lakes and the Atlantic coast of Tierra del Fuego offer very different fishing for the same species.' ),
		pm_n_h2( 'The sea-run brown trout of the Río Grande' ),
		pm_n_p( 'On the Río Grande, in Tierra del Fuego, the introduced brown trout developed the habit of migrating to the Atlantic Ocean to feed, because the river holds little forage, and they come back at huge sizes. According to Todd Moen, in the <a href="https://www.youtube.com/watch?v=2dRfHmdFmSI" rel="noopener">Catch Magazine video</a>, the average is around 10 lb (about 4.5 kg) and fish of 20 to 30 lb (9 to 13.5 kg) are recorded every year. The run enters from November and continues until April.' ),
		pm_n_h2( 'What temperature they like' ),
		pm_n_p( 'According to a fly-fishing video by <a href="https://www.youtube.com/watch?v=1wjx9AstB3o" rel="noopener">Ventures Fly Co</a>, trout are most active between 7 and 15.5 °C and hardly feed below 3.3 °C; above 20 °C catch-and-release mortality spikes from lack of oxygen and it is best to stop fishing. It is not specific to Patagonia, but it helps you decide the time of day and the month.' ),
		pm_n_h2( 'Rules for fishing them' ),
		pm_n_p( 'Patagonian fishing is governed by a common continental regulation, with annexes by province: each water has its own opening and closing dates, and many are fly-fishing only, with barbless hooks and mandatory release. You need the season permit. Confirm with your guide or the provincial authority; our guide to {sanmartin} details it for Neuquén.' ),
		pm_n_h2( 'When to fish them' ),
		pm_n_p( 'The season opens in November and closes between April and May, and the best window depends on the zone and the type of river: see <a href="{nota}">when to fish trout in Patagonia</a>.' ),
		pm_sp_pend_f( 'typical size and weight and biological maximum of each species from a reference source (FishBase or another scientific publication), and IGFA records by species.' ),
	],
];
$ficha['pt'] = [
	'titulo' => 'Truta', 'slug' => 'truta',
	'cientifico' => 'Oncorhynchus mykiss, Salmo trutta, Salvelinus fontinalis',
	'resumen' => 'Truta arco-íris, marrom e de riacho: as espécies da pesca com mosca na Patagônia.',
	'seo_title' => 'Truta na Patagônia: espécies, tamanho e regras | Pescamapa',
	'seo_description' => 'As trutas da pesca patagônica (arco-íris, marrom e fontinalis): tamanhos, a truta marrom do mar do Río Grande, temperatura ideal e regras.',
	'cuerpo' => [
		pm_f_datos( 'Dados-chave', [
			'<strong>Espécies pescadas na Patagônia:</strong> truta arco-íris (<em>Oncorhynchus mykiss</em>), truta marrom (<em>Salmo trutta</em>) e truta de riacho ou fontinalis (<em>Salvelinus fontinalis</em>).',
			'<strong>Truta marrom do mar (Río Grande, Terra do Fogo):</strong> média de cerca de 4,5 kg (10 libras), com exemplares de 9 a 13,5 kg (20 a 30 libras) todos os anos, segundo Todd Moen (Catch Magazine).',
			'<strong>Temperatura:</strong> as trutas rendem entre 7 e 15,5 °C; acima de 20 °C a mortalidade após a soltura dispara.',
			'<strong>Regras:</strong> em muitos ambientes da Patagônia se pesca só com mosca, com anzol sem farpa e com soltura obrigatória.',
		] ),
		pm_n_h2( 'As trutas da pesca patagônica' ),
		pm_n_p( 'A pesca com mosca na Patagônia gira em torno de três espécies: a truta <strong>arco-íris</strong> e a truta <strong>marrom</strong>, as mais procuradas, e a truta de <strong>riacho</strong> ou fontinalis, presente em alguns lagos e rios do centro da região. Rios de cordilheira, arroios de nascente, lagos e a costa atlântica da Terra do Fogo oferecem pescarias muito diferentes para a mesma espécie.' ),
		pm_n_h2( 'A truta marrom do mar do Río Grande' ),
		pm_n_p( 'No Río Grande, na Terra do Fogo, as trutas marrons introduzidas desenvolveram o hábito de migrar para o oceano Atlântico para se alimentar, porque no rio há pouco alimento, e voltam com tamanhos enormes. Segundo Todd Moen, no <a href="https://www.youtube.com/watch?v=2dRfHmdFmSI" rel="noopener">vídeo da Catch Magazine</a>, a média gira em torno de 10 libras (cerca de 4,5 kg) e todos os anos são registrados exemplares de 20 a 30 libras (9 a 13,5 kg). A corrida entra a partir de novembro e segue até abril.' ),
		pm_n_h2( 'Com que temperatura rendem' ),
		pm_n_p( 'Segundo um vídeo de pesca com mosca da <a href="https://www.youtube.com/watch?v=1wjx9AstB3o" rel="noopener">Ventures Fly Co</a>, as trutas estão mais ativas entre 7 e 15,5 °C e quase não comem abaixo de 3,3 °C; acima de 20 °C a mortalidade na captura e soltura dispara por falta de oxigênio e convém parar de pescar. Não é um dado específico da Patagônia, mas ajuda a decidir o horário e o mês.' ),
		pm_n_h2( 'Regras para pescá-la' ),
		pm_n_p( 'A pesca patagônica é regida por um regulamento continental comum, com anexos por província: cada ambiente tem suas próprias datas de abertura e fechamento, e muitos se pescam só com mosca, com anzol sem farpa e com soltura obrigatória. É preciso o permiso da temporada. Confirme com seu guia ou com a autoridade da província; o nosso guia de {sanmartin} detalha isso para Neuquén.' ),
		pm_n_h2( 'Quando pescá-la' ),
		pm_n_p( 'A temporada abre em novembro e fecha entre abril e maio, e a melhor janela depende da zona e do tipo de rio: veja <a href="{nota}">quando pescar truta na Patagônia</a>.' ),
		pm_sp_pend_f( 'tamanho e peso típicos e máximo biológico de cada espécie com uma fonte de referência (FishBase ou outra publicação científica), e os recordes da IGFA por espécie.' ),
	],
];

// --- Sources ------------------------------------------------------------------------------
$f = fn( $nombre, $url, $ofi = 0 ) => [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $ofi, 'consultada' => $hoy ];
$fuentes_nota = [
	$f( 'Reglamento de Pesca Deportiva Continental Patagónico 2025/2026 (portal interprovincial)', 'https://reglamentodepesca.org.ar/reglamento/', 1 ),
	$f( 'Caza y Pesca de Neuquén (Dirección Provincial de Fauna): permisos de pesca', 'https://cazaypesca.neuquen.gob.ar/permisos', 1 ),
	$f( 'ANBariloche (16/10/2026): arranca la venta de permisos de pesca para la temporada 2026-2027', 'https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027' ),
	$f( 'NoticiasNQN (15/10/2026): Neuquén habilitó la compra de permisos para la nueva temporada de pesca', 'https://www.noticiasnqn.com.ar/noticias/2026/10/15/356687-neuquen-habilito-la-compra-de-permisos-para-la-nueva-temporada-de-pesca' ),
	$f( 'NoticiasNQN (31/5/2026): cerró la temporada de pesca en Neuquén, pero algunos ambientes seguirán habilitados', 'https://www.noticiasnqn.com.ar/amp/noticias/2026/05/31/337486-cerro-la-temporada-de-pesca-en-neuquen-pero-algunos-ambientes-seguiran-habilitados' ),
	$f( 'Yellow Dog Fly Fishing: frequently asked questions, fly fishing Patagonia', 'https://www.yellowdogflyfishing.com/blogs/back-stage-pass/frequently-asked-questions-fly-fishing-patagonia' ),
	$f( 'Guía de pesca con mosca en la Patagonia argentina (Espoesía)', 'https://comunidad.espoesia.com/?p=41549' ),
	$f( 'El Encuentro Lodge (Esquel): temporadas', 'https://www.aardvarkmcleod.com/autumn-highlights-at-el-encuentro-lodge-seasons/' ),
	$f( 'Farbank: when is the best time to fish at Jurassic Lake Lodge', 'https://farbank.com/blogs/fly-water-travel/when-is-the-best-time-to-fish-at-jurassic-lake-lodge' ),
	$f( 'Farbank: Río Grande seasonal breakdown', 'https://farbank.com/blogs/fly-water-travel/rio-grande-seasonal-breakdown' ),
	$f( 'Paul Pritchard: Sea Trout on the Fly, Rio Grande (video)', 'https://www.youtube.com/watch?v=_uKgJmRCsJI' ),
	$f( 'Fly Fishing Nation: Kau Tapen, how to fish the Rio Grande (video)', 'https://www.youtube.com/watch?v=3-FHS3zm-UA' ),
	$f( 'Catch Magazine (Todd Moen): Fly fishing for huge sea-run brown trout (video)', 'https://www.youtube.com/watch?v=2dRfHmdFmSI' ),
	$f( 'Ventures Fly Co: The best and worst times of year for fly fishing (video)', 'https://www.youtube.com/watch?v=1wjx9AstB3o' ),
	$f( 'Samuel and Audrey: Best time to visit Patagonia, Argentina, month by month (video)', 'https://www.youtube.com/watch?v=0H8EUaPx-E0' ),
];
$fuentes_ficha = [
	$f( 'Reglamento de Pesca Deportiva Continental Patagónico 2025/2026 (portal interprovincial)', 'https://reglamentodepesca.org.ar/reglamento/', 1 ),
	$f( 'Caza y Pesca de Neuquén (Dirección Provincial de Fauna): permisos de pesca', 'https://cazaypesca.neuquen.gob.ar/permisos', 1 ),
	$f( 'Catch Magazine (Todd Moen): Fly fishing for huge sea-run brown trout (video)', 'https://www.youtube.com/watch?v=2dRfHmdFmSI' ),
	$f( 'Ventures Fly Co: The best and worst times of year for fly fishing (video)', 'https://www.youtube.com/watch?v=1wjx9AstB3o' ),
];
$origen = "Borrador revisado con el dueño (docs/borradores/cuando-pescar-trucha-patagonia.md). Ventanas por zona: de resúmenes de búsqueda web (páginas no abiertas) y videos resumidos por Gemini, sin minutos verificados. La explicación de noviembre-diciembre por tipo de río (deshielo vs vertiente), la libélula en lagos, la fontinalis de Río Pico en marzo-abril, el Barrancoso y el steelhead del río Santa Cruz vienen de Gemini sin fuente propia: están como 'nuestra lectura'. Afuera: tasas de re-reserva, escalas EZE-AEP, suelas de fieltro, piques por día. Confirmar con un guía de la zona en las entrevistas.";

function pm_n_destino( string $clave, string $idioma, string $nombre ): string {
	$p = pm_buscar( 'pm_destino', $clave, $idioma ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a>' : esc_html( $nombre );
}
function pm_n_existe( string $tipo, string $clave, string $idioma ): bool {
	$ya = get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	return (bool) array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $idioma );
}

// --- Species page first (the note links to it) -----------------------------------------------
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$s = $ficha[ $i ];
	if ( pm_n_existe( 'pm_especie', 'trucha', $i ) ) {
		WP_CLI::log( "especie trucha ($i) ya existe: no toco nada." );
		continue;
	}
	$nota_url = trailingslashit( pm_url_notas( $i ) ) . $trucha[ $i ]['slug'] . '/';
	$cuerpo   = strtr( implode( "\n\n", $s['cuerpo'] ), [ '{sanmartin}' => pm_n_destino( 'san-martin-junin', $i, 'San Martín y Junín de los Andes' ), '{nota}' => esc_url( $nota_url ) ] );
	if ( preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) ) {
		WP_CLI::error( "especie trucha ($i): marcador sin resolver {$m[0]}" );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": especie trucha ($i) «{$s['titulo']}» — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., title ' . mb_strlen( $s['seo_title'] ) . ' / description ' . mb_strlen( $s['seo_description'] ) . ' car.' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_especie', 'post_status' => 'publish', 'post_title' => $s['titulo'], 'post_name' => $s['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	foreach ( [ 'clave' => 'trucha', 'idioma' => $i, 'cientifico' => $s['cientifico'], 'resumen' => $s['resumen'], 'seo_title' => $s['seo_title'], 'seo_description' => $s['seo_description'], 'actualizado' => $hoy, 'fuentes' => $fuentes_ficha ] as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes_ficha ) && false !== strpos( get_post_field( 'post_content', $id ), 'datos' ) && 'trucha' === pm_clave_de( $id );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": especie trucha ($i) #$id " . pm_url_especie( 'trucha', $i ) );
	$ok || WP_CLI::error( "especie trucha ($i): la lectura de vuelta no coincide." );
}

// --- The note --------------------------------------------------------------------------------
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n = $trucha[ $i ];
	if ( pm_n_existe( 'pm_nota', 'cuando-pescar-trucha-patagonia', $i ) ) {
		WP_CLI::log( "nota trucha ($i) ya existe: no toco nada." );
		continue;
	}
	$viaje  = ( function_exists( 'pm_url_viaje' ) ? pm_url_viaje( $i ) : home_url( '/armar-viaje/' ) ) . '?especie=trucha';
	$cuerpo = strtr( implode( "\n\n", $n['cuerpo'] ), [
		'{sanmartin}' => pm_n_destino( 'san-martin-junin', $i, 'San Martín y Junín de los Andes' ),
		'{viaje}'     => esc_url( $viaje ),
		'{especie}'   => esc_url( pm_url_especie( 'trucha', $i ) ),
	] );
	if ( preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) ) {
		WP_CLI::error( "nota trucha ($i): marcador sin resolver {$m[0]}" );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": nota trucha ($i) «{$n['titulo']}» /{$n['slug']}/ — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes_nota ) . ' fuentes; title ' . mb_strlen( $n['seo_title'] ) . ' / description ' . mb_strlen( $n['seo_description'] ) . ' car.' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = [
		'clave' => 'cuando-pescar-trucha-patagonia', 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
		'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ),
		'fuentes' => $fuentes_nota, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'],
	];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_origen' => $origen ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes_nota ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'caja-anio' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": nota trucha ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "nota trucha ($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( $aplicar ? 'Trucha: ficha y nota creadas.' : 'Simulacro terminado.' );
