<?php
/**
 * Two long-form notes, 26/10/2026: "When to fish the dorado" and "When to fish the surubí", each in
 * Spanish, English and Portuguese (6 posts). Built from docs/borradores/cuando-pescar-*.md (owner's
 * rules: evergreen body + a dated yearly box for closures and events; no named outfitters or lodges
 * linked, only the trip-request form; people in videos are named, neutrally, with a link).
 *
 * Idempotent: a note that exists (same clave + language) is never touched. Dry run first
 * ($aplicar = false); run with wp-script.yml (script = notas-dorado-surubi-2026-10-26).
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
$dorado = [];

$dorado['es'] = [
	'titulo' => 'Cuándo pescar el dorado: meses, río, clima y vedas',
	'slug'   => 'cuando-pescar-dorado',
	'bajada' => 'Octubre-noviembre y marzo-mayo son los meses donde mejor se junta todo para el dorado en el Alto Paraná; la ventana se corre según el tramo del río.',
	'seo_title' => 'Cuándo pescar dorado: mejores meses y vedas | Pescamapa',
	'seo_description' => 'Los mejores meses para pescar dorado en el Paraná, cómo cambian según el tramo del río, qué hacer con el calor y el frío y cómo moverte con las vedas.',
	'datos' => [
		'Mejores meses en el Alto Paraná correntino, según los operadores consultados: octubre–noviembre y marzo–mayo.',
		'Algunos tramos y operadores ubican el pico antes, de junio a octubre, siguiendo la migración del forraje.',
		'El calor fuerte de enero y febrero apaga la pesca de día; con agua fría se pesca abajo, con línea de hundimiento.',
		'En Corrientes el dorado es de pesca y devolución obligatoria.',
		'Un guía local conoce qué tramos están abiertos en cada momento.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Respuesta corta.</strong> Los meses donde mejor se junta todo para pescar dorado en el Paraná van <strong>de octubre a noviembre</strong> y <strong>de marzo a mayo</strong>. Son las ventanas que más repiten los operadores del Alto Paraná correntino (Itatí, Ituzaingó, Paso de la Patria). Algunos tramos y operadores corren el pico hacia el invierno y la primavera, de junio a octubre. <strong>Se puede pescar dorado fuera de esos meses</strong>: lo que cambia es que las condiciones son más exigentes y hay que adaptar la técnica.' ),
		pm_n_h2( '¿Qué meses se pesca mejor?' ),
		pm_n_p( 'Las ventanas que declaran las fuentes que consultamos:' ),
		pm_n_tabla( [ 'Fuente', 'Tramo', 'Mejores meses que declara' ], [
			[ '<a href="https://www.orvis.com/dorados-on-the-fly/28FE.html" rel="noopener">Dorados on the Fly</a>', 'Alto Paraná, cerca de Itá Ibaté', 'De octubre a principios de enero y de fines de marzo a mayo. De fines de enero a principios de marzo el agua se calienta y el dorado "se apaga" (en ese momento se pescan pacú y pira pitá)' ],
			[ '<a href="https://www.yellowdogflyfishing.com/blogs/back-stage-pass/4-must-visit-golden-dorado-fishing-destinations-in-argentina" rel="noopener">Yellow Dog Fly Fishing</a>', 'Alto Paraná', 'De octubre a principios de noviembre y de fines de diciembre a abril' ],
			[ '<a href="https://www.youtube.com/watch?v=uHt3QY83Vt0" rel="noopener">Paraná River Outfitters</a>', 'Corrientes (tramo no precisado)', 'De junio a octubre, con la migración del forraje en primavera' ],
			[ '<a href="https://www.frontierstravel.com/sight-fishing-for-golden-dorado-at-karanda-lodge" rel="noopener">Karandá Lodge</a>', 'Paraná', 'De junio a diciembre' ],
			[ '<a href="https://www.youtube.com/watch?v=TzHxMam_h38" rel="noopener">Atando Piques</a>', 'Ituzaingó (Alto Paraná)', 'Junio y julio no son plena temporada: el agua todavía está fría y hay que pescar abajo, con línea de hundimiento' ],
		] ),
		pm_n_p( 'Paraná River Outfitters explica su ventana así: los dorados siguen a los peces forrajeros río arriba en primavera "devorándolos para engordar antes de desovar", y en esa época se los pesca a pez visto sobre bancos de arena y junto a troncos caídos, barrancas y caídas de profundidad. Atando Piques, pescando en Ituzaingó en junio y julio, aclara que viajaron fuera de la plena temporada: "las temperaturas todavía no eran tan altas y el agua no tenía ese calor que suele activar con más fuerza a estos peces". Las citas de Paraná River Outfitters están traducidas del inglés.' ),
		pm_n_h3( 'Por qué las ventanas no coinciden: depende de la zona del Paraná' ),
		pm_n_p( 'El Paraná cambia mucho de un tramo a otro, y por eso el pico se corre. Esta es nuestra lectura; lo que no tiene fuente propia está dicho como hipótesis.' ),
		pm_n_lista( [
			'<strong>El agua se calienta antes o después según el tramo.</strong> En el Alto Paraná el río es ancho, con costas de piedra y grandes bancos de arena, y la pesca activa en la superficie depende de que el agua gane temperatura: por eso ahí los mejores meses son los de primavera (octubre-noviembre) y los del otoño templado (marzo a mayo). Más al sur, en el Paraná medio (Esquina, Goya, La Paz), el río tiene más riachos e islas donde los grandes dorados esperan al forraje que sube, y la ventana podría arrancar antes. No encontramos fuentes con meses para esos tramos: es lo primero que conviene preguntarle a un guía de la zona.',
			'<strong>El desove y el calor acotan la temporada.</strong> La migración de desove ocurre típicamente entre fines de octubre y enero, según las condiciones del río, y el calor fuerte de enero y febrero apaga la pesca de día. Eso recorta las ventanas por los dos lados y explica por qué cada operador declara las suyas.',
			'<strong>Fuera de la ventana, el dorado sigue ahí, pero se pesca distinto.</strong> Con agua fría baja a los pozones y a la caída de las piedras y se pesca con líneas de hundimiento, con menos piques por jornada. Con agua cálida sube a cazar a la superficie.',
		] ),
		pm_n_p( 'Dicho de otro modo: los mejores meses no son una regla sino el punto donde las condiciones suelen juntarse (agua templada, forraje en movimiento y pique activo). Esa ventana se corre según el tramo del Paraná, y fuera de ella se puede pescar con otra técnica y expectativas más bajas.' ),
		pm_n_h2( '¿Cuándo se puede pescar y cuándo no?' ),
		pm_n_p( 'En el Litoral hay reglas que cambian con el tiempo: vedas por desove, vedas extraordinarias para proteger un cardumen y normas distintas en cada provincia. En Corrientes, el dorado es de <strong>pesca y devolución obligatoria</strong>, según la <a href="https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf" rel="noopener">Disposición 853/22 de Recursos Naturales</a> (Boletín Oficial de Corrientes, 26/12/2022).' ),
		pm_n_p( 'Si pescás con un guía local, no tenés que preocuparte por esto: conoce qué tramos están abiertos en cada momento y te ayuda a moverte. Si vas por tu cuenta, confirmá antes de salir con la autoridad del lugar (Prefectura o la Dirección de Recursos Naturales de la provincia).' ),
		pm_n_caja( 'Al día · vedas y novedades, 26/10/2026', [
			'Desde el 9 de septiembre de 2026 rige una veda extraordinaria en un tramo del Paraná entre los km 1109 y 1150, dispuesta por la Dirección de Recursos Naturales de Corrientes para proteger una concentración excepcional de dorados. No tiene fecha de fin (<a href="https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana" rel="noopener">El Litoral</a>, <a href="https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml" rel="noopener">Perfil</a>).',
			'No hay un calendario oficial consolidado de vedas por provincia. Tu guía local siempre está al tanto.',
		], 'Esta caja se actualiza una vez por año; el resto de la nota no cambia.' ),
		pm_n_h2( '¿Cómo influye el río y el clima?' ),
		pm_n_lista( [
			'<strong>Temperatura.</strong> El calor extremo de enero y febrero baja la actividad; en esos meses se pesca temprano a la mañana y al atardecer. Un relato de viaje al Paraná describe el mejor pique a primera hora y al final de la tarde (<a href="https://fishcolorado.com/blogs/cutthroat-anglers-blog/bucket-list-trip-golden-dorado-on-the-fly-in-argentina" rel="noopener">Cutthroat Anglers</a>).',
			'<strong>Pre-desove.</strong> Según Paraná River Outfitters, los grandes dorados siguen al forraje río arriba en primavera y se alimentan con intensidad antes de desovar.',
			'<strong>Presión y lluvia.</strong> Andy Danylchuk, investigador de la Universidad de Massachusetts Amherst, dice en el <a href="https://www.youtube.com/watch?v=oFv-uvcbXw0" rel="noopener">podcast de Flylords</a> que las bajas abruptas de presión con lluvias intensas y crecida suelen activar a los grandes predadores.',
			'<strong>Nivel y claridad del agua.</strong> Según la <a href="https://www.youtube.com/watch?v=9oQtkRY8bag" rel="noopener">película de Flylords</a>, el Alto Paraná en Itatí tiene aguas claras todo el año y es el tramo con mayor potencial de ejemplares grandes; los dorados grandes se concentran cerca de troncos caídos, barrancas, puntas de piedra y bancos de arena en bajante, y atacan sobre todo a primera hora y al final de la tarde.',
			'<strong>Desove.</strong> El guía Matías Claret (<a href="https://farbank.com/blogs/fly-water-travel/golden-dorado-spawning-season" rel="noopener">Farbank</a>) indica que la migración de desove ocurre típicamente entre fines de octubre y enero, según las condiciones del río.',
		] ),
		pm_n_h2( '¿Cómo cambia según el lugar?' ),
		pm_n_lista( [
			'<strong>Alto Paraná (Itatí, Ituzaingó, Paso de la Patria):</strong> aguas claras y el dorado más grande; ver la guía de {paso}.',
			'<strong>Esteros de Jetu e Iberá:</strong> pesca en bañados y esteros; la película de Flylords destaca la reserva de Jetu como área protegida con piques frecuentes.',
			'<strong>Paraná medio (Goya, Esquina, La Paz):</strong> ver las guías de {goya}, {esquina} y {lapaz}.',
		] ),
		pm_n_h2( 'Si venís de afuera: ¿cuándo conviene reservar?' ),
		pm_n_lista( [
			'<strong>Octubre–noviembre</strong> y <strong>marzo–mayo</strong> son las ventanas que más se repiten en el Alto Paraná correntino; algunos operadores ubican el pico antes (junio a octubre). Tu guía te dirá si hay alguna veda o cierre en tus fechas.',
			'Preguntá siempre al operador su calendario y cuánta anticipación necesita.',
			'Confirmá la licencia y el reglamento de la provincia con anticipación.',
		] ),
		pm_n_p( '<strong>¿Querés que te ayudemos a armar el viaje?</strong> Contanos fechas, grupo y qué buscás en <a href="{viaje}">el formulario</a> y te escribimos con opciones de guía y alojamiento, sin costo ni compromiso.' ),
		pm_n_h2( 'Nuestra lectura' ),
		pm_n_p( 'Si hubiera que quedarse con una sola idea: <strong>octubre-noviembre y marzo-mayo son los meses donde mejor se junta todo para el dorado en el Alto Paraná</strong>, la ventana se corre según el tramo, y fuera de ella se puede pescar con otra técnica. Para más sobre la especie, mirá la <a href="{especie}">ficha del dorado</a>.' ),
		pm_n_p( '<em>No hablamos con las personas ni los operadores citados, ni pescamos con ellos. Esta nota resume información pública y lo que dicen guías e investigadores en videos y entrevistas; la interpretación es nuestra y puede tener errores.</em>' ),
	],
];

$dorado['en'] = [
	'titulo' => 'When to fish golden dorado in Argentina: months, river, weather and rules',
	'slug'   => 'best-time-to-fish-golden-dorado',
	'bajada' => 'October–November and March–May are when conditions line up best for golden dorado on the Upper Paraná; the window shifts with the stretch of river.',
	'seo_title' => 'Best time to fish golden dorado in Argentina | Pescamapa',
	'seo_description' => 'The best months to fish golden dorado on the Paraná, how they change with the stretch of river, what to do in heat and cold, and how to handle closures.',
	'datos' => [
		'Best months on the Upper Paraná in Corrientes, according to the operators we checked: October–November and March–May.',
		'Some stretches and operators put the peak earlier, from June to October, following the baitfish migration.',
		'The heat of January and February slows daytime fishing; in cold water you fish deeper, with sinking lines.',
		'In Corrientes golden dorado is catch and release only.',
		'A local guide knows which stretches are open at any given time.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Short answer.</strong> The months when everything lines up best for golden dorado on the Paraná are <strong>October to November</strong> and <strong>March to May</strong>. Those are the windows operators on the Upper Paraná in Corrientes (Itatí, Ituzaingó, Paso de la Patria) repeat most. Some stretches and operators move the peak to winter and spring, from June to October. <strong>You can fish dorado outside those months</strong>: conditions are tougher and you have to adapt your technique.' ),
		pm_n_h2( 'Which months are best?' ),
		pm_n_p( 'The windows declared by the sources we checked:' ),
		pm_n_tabla( [ 'Source', 'Stretch', 'Best months declared' ], [
			[ '<a href="https://www.orvis.com/dorados-on-the-fly/28FE.html" rel="noopener">Dorados on the Fly</a>', 'Upper Paraná, near Itá Ibaté', 'October to early January and late March to May. From late January to early March the water gets too warm and the dorado go off the bite (pacú and pira pitá are fished then)' ],
			[ '<a href="https://www.yellowdogflyfishing.com/blogs/back-stage-pass/4-must-visit-golden-dorado-fishing-destinations-in-argentina" rel="noopener">Yellow Dog Fly Fishing</a>', 'Upper Paraná', 'October to early November and late December to April' ],
			[ '<a href="https://www.youtube.com/watch?v=uHt3QY83Vt0" rel="noopener">Paraná River Outfitters</a>', 'Corrientes (stretch not specified)', 'June to October, with the spring baitfish migration' ],
			[ '<a href="https://www.frontierstravel.com/sight-fishing-for-golden-dorado-at-karanda-lodge" rel="noopener">Karandá Lodge</a>', 'Paraná', 'June to December' ],
			[ '<a href="https://www.youtube.com/watch?v=TzHxMam_h38" rel="noopener">Atando Piques</a>', 'Ituzaingó (Upper Paraná)', 'June and July are not peak season: the water is still cold and you have to fish deep, with a sinking line' ],
		] ),
		pm_n_p( 'Paraná River Outfitters explains its window like this: in spring the dorado follow the baitfish upriver, "devouring them to fatten up before they spawn", and that is when they are fished by sight over sandbars and along dead falls, cut banks and drop-offs. Atando Piques, fishing at Ituzaingó in June and July, says they travelled outside the peak season because "temperatures were not yet that high and the water did not have the heat that usually fires these fish up" (our translation from Spanish).' ),
		pm_n_h3( 'Why the windows do not match: it depends on the stretch of the Paraná' ),
		pm_n_p( 'The Paraná changes a lot from one stretch to the next, which is why the peak moves. This is our reading; anything without a source of its own is presented as a hypothesis.' ),
		pm_n_lista( [
			'<strong>The water warms earlier or later depending on the stretch.</strong> The Upper Paraná is wide, with rocky banks and big sandbars, and active surface fishing depends on the water warming up: that is why the best months there are spring (October–November) and mild autumn (March to May). Further south, on the middle Paraná (Esquina, Goya, La Paz), there are more side channels and islands where big dorado wait for the baitfish moving up, and the window may start earlier. We found no sources with months for those stretches: it is the first thing to ask a local guide.',
			'<strong>Spawning and heat narrow the season.</strong> The spawning migration typically happens between late October and January, depending on river conditions, and the strong heat of January and February slows daytime fishing. That trims the windows from both sides and explains why every operator declares its own.',
			'<strong>Outside the window the dorado is still there, but you fish differently.</strong> In cold water it drops into deep holes and rock ledges and is fished with sinking lines, with fewer bites per day. In warm water it comes up to hunt on the surface.',
		] ),
		pm_n_p( 'In other words: the best months are not a rule but the point where conditions tend to come together (mild water, baitfish on the move and active bites). That window shifts with the stretch of the Paraná, and outside it you can still fish with a different technique and lower expectations.' ),
		pm_n_h2( 'When can you fish and when not?' ),
		pm_n_p( 'In the Litoral some rules change over time: spawning closures, extraordinary closures to protect a shoal, and different rules in each province. In Corrientes, golden dorado is <strong>catch and release only</strong>, under <a href="https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf" rel="noopener">Disposición 853/22 of Recursos Naturales</a> (Boletín Oficial de Corrientes, 26 December 2022).' ),
		pm_n_p( 'If you fish with a local guide you do not need to worry about this: they know which stretches are open at any time and help you move around. If you go on your own, check before you leave with the local authority (Prefectura or the province’s Dirección de Recursos Naturales).' ),
		pm_n_caja( 'Up to date · closures and news, 26 October 2026', [
			'Since 9 September 2026 an extraordinary closure applies to a stretch of the Paraná between km 1109 and 1150, ordered by the Dirección de Recursos Naturales of Corrientes to protect an exceptional concentration of golden dorado. It has no end date (<a href="https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana" rel="noopener">El Litoral</a>, <a href="https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml" rel="noopener">Perfil</a>, in Spanish).',
			'There is no consolidated official calendar of closures by province. Your local guide is always up to date.',
		], 'This box is updated once a year; the rest of the article does not change.' ),
		pm_n_h2( 'How do the river and the weather matter?' ),
		pm_n_lista( [
			'<strong>Temperature.</strong> Extreme heat in January and February lowers activity; in those months you fish early in the morning and in the evening. A trip report from the Paraná describes the best bite at first light and late afternoon (<a href="https://fishcolorado.com/blogs/cutthroat-anglers-blog/bucket-list-trip-golden-dorado-on-the-fly-in-argentina" rel="noopener">Cutthroat Anglers</a>).',
			'<strong>Pre-spawn.</strong> According to Paraná River Outfitters, big dorado follow the baitfish upriver in spring and feed hard before spawning.',
			'<strong>Pressure and rain.</strong> Andy Danylchuk, a researcher at the University of Massachusetts Amherst, says on the <a href="https://www.youtube.com/watch?v=oFv-uvcbXw0" rel="noopener">Flylords podcast</a> that sudden drops in barometric pressure with heavy rain and rising water tend to switch on big predators.',
			'<strong>Water level and clarity.</strong> According to the <a href="https://www.youtube.com/watch?v=9oQtkRY8bag" rel="noopener">Flylords film</a>, the Upper Paraná at Itatí has clear water all year and the best potential for big fish; big dorado gather near fallen trees, cut banks, rocky points and sandbars on falling water, and attack mostly at first light and late afternoon.',
			'<strong>Spawning.</strong> Guide Matías Claret (<a href="https://farbank.com/blogs/fly-water-travel/golden-dorado-spawning-season" rel="noopener">Farbank</a>) says the spawning migration typically happens between late October and January, depending on river conditions.',
		] ),
		pm_n_h2( 'How does it change by place?' ),
		pm_n_lista( [
			'<strong>Upper Paraná (Itatí, Ituzaingó, Paso de la Patria):</strong> clear water and the biggest dorado; see the {paso} guide.',
			'<strong>Jetu and Iberá wetlands:</strong> fishing in marshes and wetlands; the Flylords film highlights the Jetu reserve as a protected area with frequent bites.',
			'<strong>Middle Paraná (Goya, Esquina, La Paz):</strong> see the guides to {goya}, {esquina} and {lapaz}.',
		] ),
		pm_n_h2( 'Travelling from abroad: when should you book?' ),
		pm_n_lista( [
			'<strong>October–November</strong> and <strong>March–May</strong> are the windows repeated most on the Upper Paraná in Corrientes; some operators place the peak earlier (June to October). Your guide will tell you if there is any closure on your dates.',
			'Always ask the operator for its calendar and how far ahead it needs you to book.',
			'Check the licence and the provincial rules in advance.',
		] ),
		pm_n_p( '<strong>Want us to help plan the trip?</strong> Tell us your dates, group and what you are after in <a href="{viaje}">the form</a> and we will write back with guide and lodging options, at no cost or commitment.' ),
		pm_n_h2( 'Our take' ),
		pm_n_p( 'If you keep one idea: <strong>October–November and March–May are when conditions line up best for golden dorado on the Upper Paraná</strong>, the window shifts with the stretch, and outside it you can fish with a different technique. For more on the species, see the <a href="{especie}">golden dorado page</a>.' ),
		pm_n_p( '<em>We have not spoken with the people or operators cited, nor fished with them. This article summarises public information and what guides and researchers say in videos and interviews; the interpretation is ours and may contain errors.</em>' ),
	],
];

$dorado['pt'] = [
	'titulo' => 'Quando pescar o dourado na Argentina: meses, rio, clima e regras',
	'slug'   => 'quando-pescar-dourado',
	'bajada' => 'Outubro-novembro e março-maio são os meses em que tudo se junta melhor para o dourado no Alto Paraná; a janela muda conforme o trecho do rio.',
	'seo_title' => 'Quando pescar dourado: melhores meses e vedas | Pescamapa',
	'seo_description' => 'Os melhores meses para pescar dourado no Paraná, como mudam conforme o trecho do rio, o que fazer no calor e no frio e como lidar com as vedas.',
	'datos' => [
		'Melhores meses no Alto Paraná correntino, segundo os operadores consultados: outubro–novembro e março–maio.',
		'Alguns trechos e operadores colocam o pico antes, de junho a outubro, acompanhando a migração dos peixes-forragem.',
		'O calor de janeiro e fevereiro enfraquece a pesca de dia; com água fria se pesca no fundo, com linha de afundamento.',
		'Em Corrientes o dourado é de pesque e solte obrigatório.',
		'Um guia local sabe quais trechos estão abertos em cada momento.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Resposta curta.</strong> Os meses em que tudo se junta melhor para pescar dourado no Paraná vão <strong>de outubro a novembro</strong> e <strong>de março a maio</strong>. São as janelas que mais se repetem entre os operadores do Alto Paraná correntino (Itatí, Ituzaingó, Paso de la Patria). Alguns trechos e operadores deslocam o pico para o inverno e a primavera, de junho a outubro. <strong>Dá para pescar dourado fora desses meses</strong>: as condições são mais exigentes e é preciso adaptar a técnica.' ),
		pm_n_h2( 'Em que meses se pesca melhor?' ),
		pm_n_p( 'As janelas declaradas pelas fontes que consultamos:' ),
		pm_n_tabla( [ 'Fonte', 'Trecho', 'Melhores meses declarados' ], [
			[ '<a href="https://www.orvis.com/dorados-on-the-fly/28FE.html" rel="noopener">Dorados on the Fly</a>', 'Alto Paraná, perto de Itá Ibaté', 'De outubro ao início de janeiro e do fim de março a maio. Do fim de janeiro ao início de março a água esquenta demais e o dourado "se apaga" (nessa época se pescam pacu e pira pitá)' ],
			[ '<a href="https://www.yellowdogflyfishing.com/blogs/back-stage-pass/4-must-visit-golden-dorado-fishing-destinations-in-argentina" rel="noopener">Yellow Dog Fly Fishing</a>', 'Alto Paraná', 'De outubro ao início de novembro e do fim de dezembro a abril' ],
			[ '<a href="https://www.youtube.com/watch?v=uHt3QY83Vt0" rel="noopener">Paraná River Outfitters</a>', 'Corrientes (trecho não especificado)', 'De junho a outubro, com a migração dos peixes-forragem na primavera' ],
			[ '<a href="https://www.frontierstravel.com/sight-fishing-for-golden-dorado-at-karanda-lodge" rel="noopener">Karandá Lodge</a>', 'Paraná', 'De junho a dezembro' ],
			[ '<a href="https://www.youtube.com/watch?v=TzHxMam_h38" rel="noopener">Atando Piques</a>', 'Ituzaingó (Alto Paraná)', 'Junho e julho não são plena temporada: a água ainda está fria e é preciso pescar no fundo, com linha de afundamento' ],
		] ),
		pm_n_p( 'A Paraná River Outfitters explica a sua janela assim: na primavera os dourados seguem os peixes-forragem rio acima, "devorando-os para engordar antes de desovar", e nessa época são pescados a olho sobre bancos de areia e junto a troncos caídos, barrancos e desníveis do fundo. O Atando Piques, pescando em Ituzaingó em junho e julho, esclarece que viajaram fora da plena temporada porque "as temperaturas ainda não eram tão altas e a água não tinha aquele calor que costuma ativar mais esses peixes" (tradução nossa do espanhol).' ),
		pm_n_h3( 'Por que as janelas não coincidem: depende do trecho do Paraná' ),
		pm_n_p( 'O Paraná muda muito de um trecho para outro, e por isso o pico se desloca. Esta é a nossa leitura; o que não tem fonte própria é apresentado como hipótese.' ),
		pm_n_lista( [
			'<strong>A água esquenta antes ou depois conforme o trecho.</strong> O Alto Paraná é largo, com margens de pedra e grandes bancos de areia, e a pesca ativa na superfície depende de a água esquentar: por isso ali os melhores meses são os da primavera (outubro-novembro) e os do outono ameno (março a maio). Mais ao sul, no Paraná médio (Esquina, Goya, La Paz), há mais riachos e ilhas onde os grandes dourados esperam os peixes-forragem que sobem, e a janela pode começar antes. Não encontramos fontes com meses para esses trechos: é a primeira pergunta para um guia da região.',
			'<strong>A desova e o calor limitam a temporada.</strong> A migração de desova costuma ocorrer entre o fim de outubro e janeiro, conforme as condições do rio, e o calor forte de janeiro e fevereiro enfraquece a pesca de dia. Isso recorta as janelas dos dois lados e explica por que cada operador declara a sua.',
			'<strong>Fora da janela o dourado continua lá, mas se pesca diferente.</strong> Com água fria ele desce aos poços e aos degraus de pedra e se pesca com linhas de afundamento, com menos ataques por dia. Com água quente sobe para caçar na superfície.',
		] ),
		pm_n_p( 'Em outras palavras: os melhores meses não são uma regra, e sim o ponto em que as condições costumam se juntar (água amena, peixes-forragem em movimento e ataques ativos). Essa janela se desloca conforme o trecho do Paraná, e fora dela dá para pescar com outra técnica e expectativas mais baixas.' ),
		pm_n_h2( 'Quando se pode pescar e quando não?' ),
		pm_n_p( 'No Litoral há regras que mudam com o tempo: vedas de desova, vedas extraordinárias para proteger um cardume e normas diferentes em cada província. Em Corrientes, o dourado é de <strong>pesque e solte obrigatório</strong>, segundo a <a href="https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf" rel="noopener">Disposição 853/22 de Recursos Naturales</a> (Boletín Oficial de Corrientes, 26/12/2022).' ),
		pm_n_p( 'Se você pesca com um guia local, não precisa se preocupar com isso: ele sabe quais trechos estão abertos em cada momento e ajuda você a se orientar. Se for por conta própria, confirme antes de sair com a autoridade local (Prefectura ou a Dirección de Recursos Naturales da província).' ),
		pm_n_caja( 'Em dia · vedas e novidades, 26/10/2026', [
			'Desde 9 de setembro de 2026 vigora uma veda extraordinária em um trecho do Paraná entre os km 1109 e 1150, determinada pela Dirección de Recursos Naturales de Corrientes para proteger uma concentração excepcional de dourados. Não tem data de término (<a href="https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana" rel="noopener">El Litoral</a>, <a href="https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml" rel="noopener">Perfil</a>, em espanhol).',
			'Não há um calendário oficial consolidado de vedas por província. O seu guia local sempre está a par.',
		], 'Esta caixa é atualizada uma vez por ano; o resto do texto não muda.' ),
		pm_n_h2( 'Como influenciam o rio e o clima?' ),
		pm_n_lista( [
			'<strong>Temperatura.</strong> O calor extremo de janeiro e fevereiro reduz a atividade; nesses meses se pesca cedo de manhã e ao entardecer. Um relato de viagem ao Paraná descreve a melhor pesca ao amanhecer e no fim da tarde (<a href="https://fishcolorado.com/blogs/cutthroat-anglers-blog/bucket-list-trip-golden-dorado-on-the-fly-in-argentina" rel="noopener">Cutthroat Anglers</a>).',
			'<strong>Pré-desova.</strong> Segundo a Paraná River Outfitters, os grandes dourados seguem os peixes-forragem rio acima na primavera e se alimentam com intensidade antes de desovar.',
			'<strong>Pressão e chuva.</strong> Andy Danylchuk, pesquisador da Universidade de Massachusetts Amherst, diz no <a href="https://www.youtube.com/watch?v=oFv-uvcbXw0" rel="noopener">podcast da Flylords</a> que quedas bruscas de pressão com chuva forte e cheia costumam ativar os grandes predadores.',
			'<strong>Nível e clareza da água.</strong> Segundo o <a href="https://www.youtube.com/watch?v=9oQtkRY8bag" rel="noopener">filme da Flylords</a>, o Alto Paraná em Itatí tem água clara o ano todo e é o trecho com maior potencial para exemplares grandes; os dourados grandes se concentram perto de troncos caídos, barrancos, pontas de pedra e bancos de areia na vazante, e atacam sobretudo ao amanhecer e no fim da tarde.',
			'<strong>Desova.</strong> O guia Matías Claret (<a href="https://farbank.com/blogs/fly-water-travel/golden-dorado-spawning-season" rel="noopener">Farbank</a>) indica que a migração de desova costuma ocorrer entre o fim de outubro e janeiro, conforme as condições do rio.',
		] ),
		pm_n_h2( 'Como muda conforme o lugar?' ),
		pm_n_lista( [
			'<strong>Alto Paraná (Itatí, Ituzaingó, Paso de la Patria):</strong> águas claras e o dourado maior; veja o guia de {paso}.',
			'<strong>Esteros de Jetu e Iberá:</strong> pesca em banhados e esteros; o filme da Flylords destaca a reserva de Jetu como área protegida com ataques frequentes.',
			'<strong>Paraná médio (Goya, Esquina, La Paz):</strong> veja os guias de {goya}, {esquina} e {lapaz}.',
		] ),
		pm_n_h2( 'Se você vem de fora: quando convém reservar?' ),
		pm_n_lista( [
			'<strong>Outubro–novembro</strong> e <strong>março–maio</strong> são as janelas que mais se repetem no Alto Paraná correntino; alguns operadores colocam o pico antes (junho a outubro). Seu guia dirá se há alguma veda ou fechamento nas suas datas.',
			'Pergunte sempre ao operador o calendário dele e com quanta antecedência precisa da reserva.',
			'Confirme a licença e o regulamento da província com antecedência.',
		] ),
		pm_n_p( '<strong>Quer ajuda para montar a viagem?</strong> Conte as datas, o grupo e o que procura no <a href="{viaje}">formulário</a> e escrevemos com opções de guia e hospedagem, sem custo nem compromisso.' ),
		pm_n_h2( 'Nossa leitura' ),
		pm_n_p( 'Se for para ficar com uma ideia só: <strong>outubro-novembro e março-maio são os meses em que tudo se junta melhor para o dourado no Alto Paraná</strong>, a janela muda conforme o trecho, e fora dela dá para pescar com outra técnica. Para saber mais sobre a espécie, veja a <a href="{especie}">página do dourado</a>.' ),
		pm_n_p( '<em>Não falamos com as pessoas nem com os operadores citados, nem pescamos com eles. Este texto resume informação pública e o que dizem guias e pesquisadores em vídeos e entrevistas; a interpretação é nossa e pode conter erros.</em>' ),
	],
];

$surubi = [];

$surubi['es'] = [
	'titulo' => 'Cuándo pescar el surubí: meses, río, clima y vedas',
	'slug'   => 'cuando-pescar-surubi',
	'bajada' => 'El surubí no tiene una sola temporada: se mueve con el agua. Otoño y primavera en el Paraná medio, los meses fríos en el Alto Paraná y octubre en Concordia.',
	'seo_title' => 'Cuándo pescar surubí: mejores meses y vedas | Pescamapa',
	'seo_description' => 'Los mejores meses para pescar surubí en el Paraná y el Uruguay, cómo cambian según el tramo del río, el nivel del agua y cómo moverte con las vedas.',
	'datos' => [
		'No hay una sola temporada: la ventana cambia con el tramo del río.',
		'En Concordia (río Uruguay), los guías hablan de una corrida de fines de septiembre a mediados de noviembre, con el pico en octubre.',
		'En el Paraná medio la pesca se junta en otoño y primavera; en el Alto Paraná se lo busca en pozones profundos con el agua fría.',
		'La creciente activa al surubí; con agua fría se apuesta en el fondo.',
		'En Corrientes el surubí es de pesca y devolución obligatoria.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Respuesta corta.</strong> El surubí no tiene una sola temporada: cambia con el tramo del río. En el Paraná medio (Goya, Esquina, La Paz) la actividad se junta en el otoño y en la primavera; en el Alto Paraná (Itá Ibaté, Paso de la Patria) los pescadores de trolling lo buscan en los pozones profundos con el agua fría; en el río Uruguay, al pie de la represa de Salto Grande, los guías hablan de una corrida de fines de septiembre a mediados de noviembre. <strong>Se puede pescar en otros meses</strong>: esas son las ventanas donde las condiciones suelen juntarse.' ),
		pm_n_h2( '¿Qué meses se pesca mejor?' ),
		pm_n_tabla( [ 'Tramo', 'Ventana', 'Qué dicen las fuentes' ], [
			[ '<strong>Río Uruguay</strong> (Concordia, bajo Salto Grande)', 'Fines de septiembre a mediados de noviembre; pico en octubre', 'El guía Martín: "Es un cardumen de surubí que se arrimó; todos los años para esta fecha siempre lo pescamos". El guía Ezequiel, a la pregunta de qué temporada es: "en octubre y mediados del mes que viene; arrancó también unos días antes, a fines de septiembre" (<a href="https://www.youtube.com/watch?v=f7RRLtnYvCA" rel="noopener">video del canal MDS</a>)' ],
			[ '<strong>Paraná medio</strong> (Goya, Esquina, La Paz)', 'Otoño y primavera', 'Matías Jalil, guía profesional en Esquina, explica que en ciertas épocas hay que mirar si la panza del surubí está marrón, señal de que se apuesta en el fondo y no se alimenta; si nada, está activo (<a href="https://www.youtube.com/watch?v=ovnJfOqeHhI" rel="noopener">video</a>). Fishing World Guide da marzo a junio como mejor temporada (<a href="https://www.fishingworldguide.com/en/fish/surubi-catfish" rel="noopener">ficha</a>)' ],
			[ '<strong>Alto Paraná</strong> (Itá Ibaté, Paso de la Patria, Yahapé)', 'Meses fríos y salida del invierno', 'Pablo y Valentín Di Santi (Tiempo de Pesca) muestran un surubí de unos 70 kg en Itá Ibaté, pescado a trolling a primera hora (<a href="https://www.youtube.com/watch?v=gfw1emcS9-E" rel="noopener">video</a>), y otro de 1,52 m en Paso de la Patria con el guía Monchi (<a href="https://www.youtube.com/watch?v=l5cdtcuMQ6w" rel="noopener">video</a>)' ],
		] ),
		pm_n_p( 'Solo la ventana de Concordia está dicha con meses en las fuentes que consultamos; las otras dos son nuestra lectura de lo que muestran los videos y fichas, y las dejamos como orientación.' ),
		pm_n_h3( 'Por qué cada fuente dice un mes distinto' ),
		pm_n_p( 'Porque el surubí cambia de conducta con la temperatura y la técnica cambia con él. Nuestra lectura, con lo que dicen las fuentes:' ),
		pm_n_lista( [
			'<strong>Con agua fría baja a los pozones.</strong> Es un pez de fondo: cuando el río se enfría se amontona en fosas profundas. El documental <a href="https://www.youtube.com/watch?v=qlABe4o2Vr0" rel="noopener">Giant Surubi Catfish – Chasing Monsters</a> ubica pozos de unos 15 metros en Ituzaingó donde reposan los ejemplares más grandes. Para el trolling con señuelos que rascan el fondo eso significa mucho pez en poco lugar.',
			'<strong>Con agua templada patrulla.</strong> En otoño y primavera sale a los camalotales y a las bocas de arroyo a cazar forraje. Es la ventana del garete y de la pesca anclada con morena viva que muestran los guías de Esquina.',
			'<strong>Cerca de una represa, el río manda.</strong> En Concordia los guías cuentan que con las compuertas abiertas el río subió a 11 metros, frente a los 5 habituales, y los cardúmenes quedaron apretados contra los veriles.',
		] ),
		pm_n_p( 'Dicho de otro modo: los mejores meses no son una regla sino el punto donde las condiciones suelen juntarse, y esa ventana se corre según el tramo.' ),
		pm_n_h2( '¿Cuándo se puede pescar y cuándo no?' ),
		pm_n_p( 'En el Litoral hay reglas que cambian con el tiempo: vedas por desove, vedas extraordinarias para proteger un cardumen y normas distintas en cada provincia. En Corrientes, el surubí es de <strong>pesca y devolución obligatoria</strong>, según la <a href="https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf" rel="noopener">Disposición 853/22 de Recursos Naturales</a> (Boletín Oficial de Corrientes, 26/12/2022).' ),
		pm_n_p( 'Si pescás con un guía local, no tenés que preocuparte por esto: conoce qué tramos están abiertos en cada momento y te ayuda a moverte. Si vas por tu cuenta, confirmá antes de salir con la autoridad del lugar (Prefectura o la Dirección de Recursos Naturales de la provincia).' ),
		pm_n_caja( 'Al día · vedas y novedades, 26/10/2026', [
			'Desde el 29 de mayo de 2026 rige una veda extraordinaria en el Paraná entre los km 1232 y 1238, dispuesta por Corrientes (Disposición 618) y Chaco (Disposición 023) para proteger una concentración de surubíes. No tiene fecha de fin (<a href="https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/" rel="noopener">ABC</a>, <a href="https://www.perfil.com/noticias/nea/veda-en-el-parana-que-tramo-quedo-cerrado-a-la-pesca-y-que-sanciones-preven-para-los-infractores.phtml" rel="noopener">Perfil</a>).',
			'Fiesta Nacional del Surubí, en Goya: 50.ª edición del 28 de abril al 2 de mayo de 2027, con concurso de pesca y devolución (según {goya_enlace}). Es el momento de más demanda de alojamiento: reservá con tiempo.',
			'No hay un calendario oficial consolidado de vedas por provincia. Tu guía local siempre está al tanto.',
		], 'Esta caja se actualiza una vez por año; el resto de la nota no cambia.' ),
		pm_n_h2( '¿Cómo influye el río y el clima?' ),
		pm_n_lista( [
			'<strong>La creciente activa el movimiento.</strong> Monchi, guía local en Paso de la Patria, mientras pescaba: "Estos bichos se están moviendo porque está creciendo el río" (<a href="https://www.youtube.com/watch?v=l5cdtcuMQ6w" rel="noopener">video de Tiempo de Pesca</a>).',
			'<strong>Los cortes de agua.</strong> Matías Jalil enseña a buscar el límite donde el agua marrón del canal principal se junta con el agua filtrada que baja de los camalotales, y a fondear sobre el veril, en un escalón de unos 7 metros (<a href="https://www.youtube.com/watch?v=HjmYOEio64U" rel="noopener">video</a>).',
			'<strong>Camalotes y arroyos.</strong> "Tiene que estar acostado al camalote siempre", dice un guía de Esquina (<a href="https://www.youtube.com/watch?v=L2WNzKoTUZU" rel="noopener">video de Naturaltube</a>); Jalil lo llama "la casa del surubí": arroyos con camalotales y carrizales.',
			'<strong>Pozones.</strong> Pozas de unos 15 metros, casi a oscuras y con corriente violenta, según el documental de Cyril Chauquet. Nuestra lectura: esa concentración de ejemplares en pocos pozos es la razón por la que las autoridades han cerrado tramos puntuales.',
			'<strong>La hora.</strong> Jalil recomienda insistir en la madrugada cerrada, a las 2 o 3 de la mañana, con pesca anclada (<a href="https://www.youtube.com/watch?v=93aLllEKMuo" rel="noopener">video</a>); Di Santi pescó a trolling a primera hora.',
			'<strong>La morena.</strong> En el documental explican que la morena (<em>Gymnotus</em>) emite un campo eléctrico que los grandes predadores detectan con sus barbillones, y por eso es el cebo más usado.',
		] ),
		pm_n_h2( '¿Cómo cambia según el lugar?' ),
		pm_n_lista( [
			'<strong>Alto Paraná (Itá Ibaté, Paso de la Patria):</strong> piedras y pozones profundos; trolling de media agua rozando la piedra. En Itá Ibaté, Punta Gallino, los Di Santi vieron un ejemplar estimado en 70 kg. Ver la guía de {paso}.',
			'<strong>Paraná medio (Goya, Esquina, La Paz):</strong> camalotales, arroyos y bocas de riacho; pesca al garete o anclada con morena viva, con plomos de 40 a 60 gramos. Ver las guías de {goya}, {esquina} y {lapaz}.',
			'<strong>Río Uruguay (Concordia):</strong> fondos rocosos y desniveles de 7 a 12 metros; trolling con señuelos de buceo profundo arrastrados a 20 o 30 metros de la popa.',
		] ),
		pm_n_h2( 'Si venís de afuera: ¿cuándo conviene ir?' ),
		pm_n_lista( [
			'<strong>Pintado y atigrado.</strong> Hay dos surubíes: el pintado (<em>Pseudoplatystoma corruscans</em>) y el atigrado (<em>P. reticulatum</em>). El pintado es el gigante: 1,52 m medidos en Paso de la Patria, y un guía de Ituzaingó ubica a los más grandes cerca de 1,80 m y entre 70 y 90 kg. En la pelea no salta como el dorado: busca el fondo y obliga a un combate lento.',
			'<strong>Con mosca</strong> es una sorpresa poco frecuente, según Chris Dorsey en los esteros de Corrientes (<a href="https://www.youtube.com/watch?v=ssQf5GVY3eE" rel="noopener">video</a>); lo habitual es el trolling o la carnada viva.',
			'<strong>Fecha ancla:</strong> la Fiesta Nacional del Surubí en Goya concentra la demanda de alojamiento; las fechas del año están en la caja de la sección sobre reglas.',
			'<strong>Devolución.</strong> Pablo Di Santi y Matías Jalil insisten en devolver los ejemplares grandes: son reproductores. Jalil: sostener al pez de la cola dentro del agua, sin moverlo hacia adelante y atrás, hasta que se vaya solo.',
		] ),
		pm_n_p( '<strong>¿Querés que te ayudemos a armar el viaje?</strong> Contanos fechas, grupo y qué buscás en <a href="{viaje}">el formulario</a> y te escribimos con opciones de guía y alojamiento, sin costo ni compromiso.' ),
		pm_n_h2( 'Nuestra lectura' ),
		pm_n_p( 'Si hubiera que quedarse con una sola idea: <strong>el surubí se mueve con el agua</strong>. Otoño y primavera en el Paraná medio, meses fríos en los pozones del Alto Paraná y octubre en Concordia son donde las condiciones suelen juntarse. Para más sobre la especie, mirá la <a href="{especie}">ficha del surubí</a>.' ),
		pm_n_p( '<em>No hablamos con las personas citadas ni pescamos con ellas. Esta nota resume información pública y lo que dicen guías y pescadores en videos; la interpretación es nuestra y puede tener errores.</em>' ),
	],
];

$surubi['en'] = [
	'titulo' => 'When to fish surubí in Argentina: months, river, weather and rules',
	'slug'   => 'best-time-to-fish-surubi',
	'bajada' => 'Surubí has no single season: it moves with the water. Autumn and spring on the middle Paraná, the cold months on the Upper Paraná, and October at Concordia.',
	'seo_title' => 'Best time to fish surubí in Argentina | Pescamapa',
	'seo_description' => 'The best months to fish surubí on the Paraná and Uruguay rivers, how it changes by stretch of river, what water level does and how to handle closures.',
	'datos' => [
		'There is no single season: the window changes with the stretch of river.',
		'At Concordia (Uruguay River) guides talk about a run from late September to mid-November, peaking in October.',
		'On the middle Paraná fishing comes together in autumn and spring; on the Upper Paraná it is sought in deep holes in cold water.',
		'Rising water switches the surubí on; in cold water it sits on the bottom.',
		'In Corrientes surubí is catch and release only.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Short answer.</strong> Surubí has no single season: it changes with the stretch of river. On the middle Paraná (Goya, Esquina, La Paz) activity comes together in autumn and spring; on the Upper Paraná (Itá Ibaté, Paso de la Patria) trolling anglers look for it in deep holes in cold water; on the Uruguay River, below the Salto Grande dam, guides talk about a run from late September to mid-November. <strong>You can fish it in other months</strong>: these are the windows where conditions tend to line up.' ),
		pm_n_h2( 'Which months are best?' ),
		pm_n_tabla( [ 'Stretch', 'Window', 'What the sources say' ], [
			[ '<strong>Uruguay River</strong> (Concordia, below Salto Grande)', 'Late September to mid-November; peak in October', 'Guide Martín: "It is a shoal of surubí that moved in; every year around this date we always catch them". Guide Ezequiel, asked what the season is: "in October and mid next month; it also started a few days earlier, in late September" (<a href="https://www.youtube.com/watch?v=f7RRLtnYvCA" rel="noopener">MDS channel video</a>, in Spanish, our translation)' ],
			[ '<strong>Middle Paraná</strong> (Goya, Esquina, La Paz)', 'Autumn and spring', 'Matías Jalil, a professional guide in Esquina, explains that at certain times of year you should check whether the surubí’s belly is brown, a sign it is settling on the bottom and not feeding; if it is swimming, it is active (<a href="https://www.youtube.com/watch?v=ovnJfOqeHhI" rel="noopener">video</a>). Fishing World Guide gives March to June as the best season (<a href="https://www.fishingworldguide.com/en/fish/surubi-catfish" rel="noopener">page</a>)' ],
			[ '<strong>Upper Paraná</strong> (Itá Ibaté, Paso de la Patria, Yahapé)', 'Cold months and the end of winter', 'Pablo and Valentín Di Santi (Tiempo de Pesca) show a surubí of about 70 kg at Itá Ibaté, caught trolling at first light (<a href="https://www.youtube.com/watch?v=gfw1emcS9-E" rel="noopener">video</a>), and another of 1.52 m at Paso de la Patria with guide Monchi (<a href="https://www.youtube.com/watch?v=l5cdtcuMQ6w" rel="noopener">video</a>)' ],
		] ),
		pm_n_p( 'Only the Concordia window is stated in months by the sources we checked; the other two are our reading of what the videos and pages show, and we offer them as guidance.' ),
		pm_n_h3( 'Why each source says a different month' ),
		pm_n_p( 'Because surubí changes behaviour with temperature, and technique changes with it. Our reading, with what the sources say:' ),
		pm_n_lista( [
			'<strong>In cold water it drops into the deep holes.</strong> It is a bottom fish: when the river cools it piles into deep pools. The documentary <a href="https://www.youtube.com/watch?v=qlABe4o2Vr0" rel="noopener">Giant Surubi Catfish – Chasing Monsters</a> places holes of about 15 metres at Ituzaingó where the biggest fish rest. For trolling with lures that scrape the bottom, that means a lot of fish in little space.',
			'<strong>In mild water it patrols.</strong> In autumn and spring it comes out to the floating vegetation and creek mouths to hunt baitfish. That is the window for the drift and anchored fishing with live morena that the Esquina guides show.',
			'<strong>Near a dam, the river rules.</strong> At Concordia guides say that with the gates open the river rose to 11 metres, against the usual 5, and the shoals were squeezed against the ledges.',
		] ),
		pm_n_p( 'In other words: the best months are not a rule but the point where conditions tend to come together, and that window shifts with the stretch.' ),
		pm_n_h2( 'When can you fish and when not?' ),
		pm_n_p( 'In the Litoral some rules change over time: spawning closures, extraordinary closures to protect a shoal, and different rules in each province. In Corrientes, surubí is <strong>catch and release only</strong>, under <a href="https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf" rel="noopener">Disposición 853/22 of Recursos Naturales</a> (Boletín Oficial de Corrientes, 26 December 2022).' ),
		pm_n_p( 'If you fish with a local guide you do not need to worry about this: they know which stretches are open at any time and help you move around. If you go on your own, check before you leave with the local authority (Prefectura or the province’s Dirección de Recursos Naturales).' ),
		pm_n_caja( 'Up to date · closures and news, 26 October 2026', [
			'Since 29 May 2026 an extraordinary closure applies to the Paraná between km 1232 and 1238, ordered by Corrientes (Disposición 618) and Chaco (Disposición 023) to protect a concentration of surubí. It has no end date (<a href="https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/" rel="noopener">ABC</a>, <a href="https://www.perfil.com/noticias/nea/veda-en-el-parana-que-tramo-quedo-cerrado-a-la-pesca-y-que-sanciones-preven-para-los-infractores.phtml" rel="noopener">Perfil</a>, in Spanish).',
			'Fiesta Nacional del Surubí, in Goya: 50th edition from 28 April to 2 May 2027, with a catch-and-release tournament (according to {goya_enlace}). It is the busiest time for lodging: book early.',
			'There is no consolidated official calendar of closures by province. Your local guide is always up to date.',
		], 'This box is updated once a year; the rest of the article does not change.' ),
		pm_n_h2( 'How do the river and the weather matter?' ),
		pm_n_lista( [
			'<strong>Rising water sets things moving.</strong> Monchi, a local guide at Paso de la Patria, while fishing: "These fish are moving because the river is rising" (<a href="https://www.youtube.com/watch?v=l5cdtcuMQ6w" rel="noopener">Tiempo de Pesca video</a>, our translation).',
			'<strong>Water edges.</strong> Matías Jalil teaches you to look for the line where the brown water of the main channel meets the filtered water coming off the floating vegetation, and to anchor over the ledge, a step of about 7 metres (<a href="https://www.youtube.com/watch?v=HjmYOEio64U" rel="noopener">video</a>).',
			'<strong>Floating islands and creeks.</strong> "It always has to be lying against the camalote", says a guide in Esquina (<a href="https://www.youtube.com/watch?v=L2WNzKoTUZU" rel="noopener">Naturaltube video</a>); Jalil calls it "the surubí’s home": creeks with floating vegetation and reed beds.',
			'<strong>Deep holes.</strong> Pools of about 15 metres, nearly dark and with violent current, according to Cyril Chauquet’s documentary. Our reading: that concentration of fish in a few holes is why authorities have closed specific stretches.',
			'<strong>Time of day.</strong> Jalil recommends persisting in the dead of night, at 2 or 3 in the morning, with anchored fishing (<a href="https://www.youtube.com/watch?v=93aLllEKMuo" rel="noopener">video</a>); the Di Santis fished trolling at first light.',
			'<strong>Morena.</strong> The documentary explains that the morena (<em>Gymnotus</em>, a knifefish) gives off an electric field that big predators pick up with their barbels, which is why it is the most used bait.',
		] ),
		pm_n_h2( 'How does it change by place?' ),
		pm_n_lista( [
			'<strong>Upper Paraná (Itá Ibaté, Paso de la Patria):</strong> rock and deep holes; mid-depth trolling brushing the rocks. At Itá Ibaté, Punta Gallino, the Di Santis saw a fish estimated at 70 kg. See the {paso} guide.',
			'<strong>Middle Paraná (Goya, Esquina, La Paz):</strong> floating vegetation, creeks and channel mouths; drift or anchored fishing with live morena, with 40 to 60 gram sinkers. See the guides to {goya}, {esquina} and {lapaz}.',
			'<strong>Uruguay River (Concordia):</strong> rocky bottoms and ledges of 7 to 12 metres; trolling with deep-diving lures dragged 20 or 30 metres behind the boat.',
		] ),
		pm_n_h2( 'Travelling from abroad: when should you go?' ),
		pm_n_lista( [
			'<strong>Pintado and atigrado.</strong> There are two surubí: the pintado (<em>Pseudoplatystoma corruscans</em>) and the atigrado (<em>P. reticulatum</em>). The pintado is the giant: 1.52 m measured at Paso de la Patria, and a guide at Ituzaingó puts the biggest at nearly 1.80 m and 70 to 90 kg. In the fight it does not jump like a dorado: it heads for the bottom and forces a slow battle.',
			'<strong>On fly</strong> it is a rare surprise, according to Chris Dorsey in the Corrientes wetlands (<a href="https://www.youtube.com/watch?v=ssQf5GVY3eE" rel="noopener">video</a>); trolling or live bait is the norm.',
			'<strong>Anchor date:</strong> the Fiesta Nacional del Surubí in Goya concentrates demand for lodging; the year’s dates are in the box in the section on rules.',
			'<strong>Release.</strong> Pablo Di Santi and Matías Jalil insist on releasing big fish: they are breeders. Jalil: hold the fish by the tail in the water, without moving it back and forth, until it swims off on its own.',
		] ),
		pm_n_p( '<strong>Want us to help plan the trip?</strong> Tell us your dates, group and what you are after in <a href="{viaje}">the form</a> and we will write back with guide and lodging options, at no cost or commitment.' ),
		pm_n_h2( 'Our take' ),
		pm_n_p( 'If you keep one idea: <strong>surubí moves with the water</strong>. Autumn and spring on the middle Paraná, the cold months in the Upper Paraná holes and October at Concordia are where conditions tend to come together. For more on the species, see the <a href="{especie}">surubí page</a>.' ),
		pm_n_p( '<em>We have not spoken with the people cited, nor fished with them. This article summarises public information and what guides and anglers say in videos; the interpretation is ours and may contain errors.</em>' ),
	],
];

$surubi['pt'] = [
	'titulo' => 'Quando pescar o surubi na Argentina: meses, rio, clima e regras',
	'slug'   => 'quando-pescar-surubi',
	'bajada' => 'O surubi não tem uma única temporada: ele se move com a água. Outono e primavera no Paraná médio, os meses frios no Alto Paraná e outubro em Concordia.',
	'seo_title' => 'Quando pescar surubi: melhores meses e vedas | Pescamapa',
	'seo_description' => 'Os melhores meses para pescar surubi no Paraná e no Uruguai, como muda conforme o trecho do rio, o que faz o nível da água e como lidar com as vedas.',
	'datos' => [
		'Não há uma única temporada: a janela muda conforme o trecho do rio.',
		'Em Concordia (rio Uruguai), os guias falam de uma corrida do fim de setembro a meados de novembro, com pico em outubro.',
		'No Paraná médio a pesca se junta no outono e na primavera; no Alto Paraná ele é procurado em poços fundos com água fria.',
		'A cheia ativa o surubi; com água fria ele fica no fundo.',
		'Em Corrientes o surubi é de pesque e solte obrigatório.',
	],
	'cuerpo' => [
		pm_n_p( '<strong>Resposta curta.</strong> O surubi não tem uma única temporada: muda conforme o trecho do rio. No Paraná médio (Goya, Esquina, La Paz) a atividade se junta no outono e na primavera; no Alto Paraná (Itá Ibaté, Paso de la Patria) os pescadores de corrico o procuram nos poços fundos com água fria; no rio Uruguai, ao pé da represa de Salto Grande, os guias falam de uma corrida do fim de setembro a meados de novembro. <strong>Dá para pescar em outros meses</strong>: essas são as janelas em que as condições costumam se juntar.' ),
		pm_n_h2( 'Em que meses se pesca melhor?' ),
		pm_n_tabla( [ 'Trecho', 'Janela', 'O que dizem as fontes' ], [
			[ '<strong>Rio Uruguai</strong> (Concordia, abaixo de Salto Grande)', 'Do fim de setembro a meados de novembro; pico em outubro', 'O guia Martín: "É um cardume de surubi que se aproximou; todo ano nessa data a gente sempre pesca". O guia Ezequiel, quando perguntado que temporada é: "em outubro e meados do mês que vem; começou também uns dias antes, no fim de setembro" (<a href="https://www.youtube.com/watch?v=f7RRLtnYvCA" rel="noopener">vídeo do canal MDS</a>, em espanhol, tradução nossa)' ],
			[ '<strong>Paraná médio</strong> (Goya, Esquina, La Paz)', 'Outono e primavera', 'Matías Jalil, guia profissional em Esquina, explica que em certas épocas é preciso olhar se a barriga do surubi está marrom, sinal de que ele se assenta no fundo e não se alimenta; se está nadando, está ativo (<a href="https://www.youtube.com/watch?v=ovnJfOqeHhI" rel="noopener">vídeo</a>). A Fishing World Guide dá março a junho como melhor temporada (<a href="https://www.fishingworldguide.com/en/fish/surubi-catfish" rel="noopener">página</a>)' ],
			[ '<strong>Alto Paraná</strong> (Itá Ibaté, Paso de la Patria, Yahapé)', 'Meses frios e fim do inverno', 'Pablo e Valentín Di Santi (Tiempo de Pesca) mostram um surubi de cerca de 70 kg em Itá Ibaté, pescado em corrico logo cedo (<a href="https://www.youtube.com/watch?v=gfw1emcS9-E" rel="noopener">vídeo</a>), e outro de 1,52 m em Paso de la Patria com o guia Monchi (<a href="https://www.youtube.com/watch?v=l5cdtcuMQ6w" rel="noopener">vídeo</a>)' ],
		] ),
		pm_n_p( 'Só a janela de Concordia está dita com meses nas fontes que consultamos; as outras duas são a nossa leitura do que mostram os vídeos e as páginas, e ficam como orientação.' ),
		pm_n_h3( 'Por que cada fonte diz um mês diferente' ),
		pm_n_p( 'Porque o surubi muda de comportamento com a temperatura e a técnica muda com ele. Nossa leitura, com o que dizem as fontes:' ),
		pm_n_lista( [
			'<strong>Com água fria desce aos poços.</strong> É um peixe de fundo: quando o rio esfria, ele se amontoa em fossas profundas. O documentário <a href="https://www.youtube.com/watch?v=qlABe4o2Vr0" rel="noopener">Giant Surubi Catfish – Chasing Monsters</a> localiza poços de cerca de 15 metros em Ituzaingó onde repousam os maiores exemplares. Para o corrico com iscas que raspam o fundo, isso significa muito peixe em pouco lugar.',
			'<strong>Com água amena ele patrulha.</strong> No outono e na primavera sai para os camalotes e as bocas de arroio para caçar peixes-forragem. É a janela da deriva e da pesca ancorada com morena viva que os guias de Esquina mostram.',
			'<strong>Perto de uma represa, quem manda é o rio.</strong> Em Concordia os guias contam que com as comportas abertas o rio subiu a 11 metros, contra os 5 habituais, e os cardumes ficaram apertados contra os desníveis do fundo.',
		] ),
		pm_n_p( 'Em outras palavras: os melhores meses não são uma regra, e sim o ponto em que as condições costumam se juntar, e essa janela se desloca conforme o trecho.' ),
		pm_n_h2( 'Quando se pode pescar e quando não?' ),
		pm_n_p( 'No Litoral há regras que mudam com o tempo: vedas de desova, vedas extraordinárias para proteger um cardume e normas diferentes em cada província. Em Corrientes, o surubi é de <strong>pesque e solte obrigatório</strong>, segundo a <a href="https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf" rel="noopener">Disposição 853/22 de Recursos Naturales</a> (Boletín Oficial de Corrientes, 26/12/2022).' ),
		pm_n_p( 'Se você pesca com um guia local, não precisa se preocupar com isso: ele sabe quais trechos estão abertos em cada momento e ajuda você a se orientar. Se for por conta própria, confirme antes de sair com a autoridade local (Prefectura ou a Dirección de Recursos Naturales da província).' ),
		pm_n_caja( 'Em dia · vedas e novidades, 26/10/2026', [
			'Desde 29 de maio de 2026 vigora uma veda extraordinária no Paraná entre os km 1232 e 1238, determinada por Corrientes (Disposição 618) e Chaco (Disposição 023) para proteger uma concentração de surubis. Não tem data de término (<a href="https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/" rel="noopener">ABC</a>, <a href="https://www.perfil.com/noticias/nea/veda-en-el-parana-que-tramo-quedo-cerrado-a-la-pesca-y-que-sanciones-preven-para-los-infractores.phtml" rel="noopener">Perfil</a>, em espanhol).',
			'Fiesta Nacional del Surubí, em Goya: 50.ª edição de 28 de abril a 2 de maio de 2027, com torneio de pesque e solte (segundo {goya_enlace}). É o momento de maior procura por hospedagem: reserve com antecedência.',
			'Não há um calendário oficial consolidado de vedas por província. O seu guia local sempre está a par.',
		], 'Esta caixa é atualizada uma vez por ano; o resto do texto não muda.' ),
		pm_n_h2( 'Como influenciam o rio e o clima?' ),
		pm_n_lista( [
			'<strong>A cheia põe tudo em movimento.</strong> Monchi, guia local em Paso de la Patria, enquanto pescava: "Esses bichos estão se movendo porque o rio está subindo" (<a href="https://www.youtube.com/watch?v=l5cdtcuMQ6w" rel="noopener">vídeo do Tiempo de Pesca</a>, tradução nossa).',
			'<strong>Os cortes de água.</strong> Matías Jalil ensina a procurar o limite onde a água marrom do canal principal se junta à água filtrada que desce dos camalotes, e a ancorar sobre o desnível, um degrau de cerca de 7 metros (<a href="https://www.youtube.com/watch?v=HjmYOEio64U" rel="noopener">vídeo</a>).',
			'<strong>Camalotes e arroios.</strong> "Tem que estar sempre encostado no camalote", diz um guia de Esquina (<a href="https://www.youtube.com/watch?v=L2WNzKoTUZU" rel="noopener">vídeo da Naturaltube</a>); Jalil chama de "a casa do surubi": arroios com camalotes e juncais.',
			'<strong>Poços.</strong> Poços de cerca de 15 metros, quase no escuro e com corrente violenta, segundo o documentário de Cyril Chauquet. Nossa leitura: essa concentração de exemplares em poucos poços é a razão pela qual as autoridades fecharam trechos pontuais.',
			'<strong>A hora.</strong> Jalil recomenda insistir na madrugada fechada, às 2 ou 3 da manhã, com pesca ancorada (<a href="https://www.youtube.com/watch?v=93aLllEKMuo" rel="noopener">vídeo</a>); os Di Santi pescaram em corrico logo cedo.',
			'<strong>A morena.</strong> O documentário explica que a morena (<em>Gymnotus</em>, peixe-elétrico) emite um campo elétrico que os grandes predadores detectam com os barbilhões, e por isso é a isca mais usada.',
		] ),
		pm_n_h2( 'Como muda conforme o lugar?' ),
		pm_n_lista( [
			'<strong>Alto Paraná (Itá Ibaté, Paso de la Patria):</strong> pedras e poços fundos; corrico de meia água roçando a pedra. Em Itá Ibaté, Punta Gallino, os Di Santi viram um exemplar estimado em 70 kg. Veja o guia de {paso}.',
			'<strong>Paraná médio (Goya, Esquina, La Paz):</strong> camalotes, arroios e bocas de riacho; pesca à deriva ou ancorada com morena viva, com chumbos de 40 a 60 gramas. Veja os guias de {goya}, {esquina} e {lapaz}.',
			'<strong>Rio Uruguai (Concordia):</strong> fundos rochosos e desníveis de 7 a 12 metros; corrico com iscas de mergulho profundo arrastadas a 20 ou 30 metros da popa.',
		] ),
		pm_n_h2( 'Se você vem de fora: quando convém ir?' ),
		pm_n_lista( [
			'<strong>Pintado e atigrado.</strong> Há dois surubis: o pintado (<em>Pseudoplatystoma corruscans</em>) e o atigrado (<em>P. reticulatum</em>). O pintado é o gigante: 1,52 m medidos em Paso de la Patria, e um guia de Ituzaingó coloca os maiores perto de 1,80 m e entre 70 e 90 kg. Na briga ele não salta como o dourado: busca o fundo e obriga a um combate lento.',
			'<strong>Com mosca</strong> é uma surpresa pouco frequente, segundo Chris Dorsey nos esteros de Corrientes (<a href="https://www.youtube.com/watch?v=ssQf5GVY3eE" rel="noopener">vídeo</a>); o habitual é o corrico ou a isca viva.',
			'<strong>Data âncora:</strong> a Fiesta Nacional del Surubí em Goya concentra a procura por hospedagem; as datas do ano estão na caixa da seção sobre regras.',
			'<strong>Devolução.</strong> Pablo Di Santi e Matías Jalil insistem em devolver os exemplares grandes: são reprodutores. Jalil: segurar o peixe pela cauda dentro da água, sem movê-lo para a frente e para trás, até que vá embora sozinho.',
		] ),
		pm_n_p( '<strong>Quer ajuda para montar a viagem?</strong> Conte as datas, o grupo e o que procura no <a href="{viaje}">formulário</a> e escrevemos com opções de guia e hospedagem, sem custo nem compromisso.' ),
		pm_n_h2( 'Nossa leitura' ),
		pm_n_p( 'Se for para ficar com uma ideia só: <strong>o surubi se move com a água</strong>. Outono e primavera no Paraná médio, meses frios nos poços do Alto Paraná e outubro em Concordia são onde as condições costumam se juntar. Para saber mais sobre a espécie, veja a <a href="{especie}">página do surubi</a>.' ),
		pm_n_p( '<em>Não falamos com as pessoas citadas nem pescamos com elas. Este texto resume informação pública e o que dizem guias e pescadores em vídeos; a interpretação é nossa e pode conter erros.</em>' ),
	],
];

// --- Sources (the rows shown under each note and emitted as `citation`) -----------------------
$f = fn( $nombre, $url, $ofi = 0 ) => [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $ofi, 'consultada' => $hoy ];
$src_853 = $f( 'Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)', 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf', 1 );
$fuentes = [
	'dorado' => [
		$src_853,
		$f( 'El Litoral (9/9/2026): rige una veda extraordinaria para proteger un cardumen de dorados en el Paraná', 'https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana' ),
		$f( 'Perfil: dispusieron la veda extraordinaria en el río Paraná por una concentración masiva de dorados', 'https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml' ),
		$f( 'Dorados on the Fly (lodge del Alto Paraná)', 'https://www.orvis.com/dorados-on-the-fly/28FE.html' ),
		$f( 'Yellow Dog Fly Fishing: 4 must-visit golden dorado destinations in Argentina', 'https://www.yellowdogflyfishing.com/blogs/back-stage-pass/4-must-visit-golden-dorado-fishing-destinations-in-argentina' ),
		$f( 'Frontiers Travel: sight fishing for golden dorado at Karandá Lodge', 'https://www.frontierstravel.com/sight-fishing-for-golden-dorado-at-karanda-lodge' ),
		$f( 'Nervous Waters: Paraná River Outfitters, Golden Dorado PROgram (video)', 'https://www.youtube.com/watch?v=uHt3QY83Vt0' ),
		$f( 'Atando Piques: ¿Se puede pescar dorados fuera de temporada? Corrientes (video)', 'https://www.youtube.com/watch?v=TzHxMam_h38' ),
		$f( 'Flylords: Golden Dorado, Chasing Argentine Gold (película)', 'http://www.youtube.com/watch?v=9oQtkRY8bag' ),
		$f( 'Flylords Podcast ep. 007: Researching Golden Dorado in Argentina with Dr. Andy Danylchuk', 'http://www.youtube.com/watch?v=oFv-uvcbXw0' ),
		$f( 'Cutthroat Anglers: Bucket list trip, golden dorado on the fly in Argentina', 'https://fishcolorado.com/blogs/cutthroat-anglers-blog/bucket-list-trip-golden-dorado-on-the-fly-in-argentina' ),
		$f( 'Farbank (Matías Claret): golden dorado spawning season', 'https://farbank.com/blogs/fly-water-travel/golden-dorado-spawning-season' ),
	],
	'surubi' => [
		$src_853,
		$f( 'ABC Color (1/6/2026): Corrientes y Chaco decretan veda temporal para preservar cardúmenes de surubí en el Paraná', 'https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/' ),
		$f( 'Perfil: veda en el Paraná, qué tramo quedó cerrado a la pesca y qué sanciones prevén', 'https://www.perfil.com/noticias/nea/veda-en-el-parana-que-tramo-quedo-cerrado-a-la-pesca-y-que-sanciones-preven-para-los-infractores.phtml' ),
		$f( 'Primera Edición: activaron una veda extraordinaria en el Paraná para proteger una masiva concentración de surubíes', 'https://www.primeraedicion.com.ar/nota/101110879/activaron-una-veda-extraordinaria-en-el-parana-para-proteger-una-masiva-concentracion-de-surubies/' ),
		$f( 'Fishing World Guide: surubi catfish', 'https://www.fishingworldguide.com/en/fish/surubi-catfish' ),
		$f( 'Tiempo de Pesca (Pablo y Valentín Di Santi): Surubí de 70 kg (video)', 'https://www.youtube.com/watch?v=gfw1emcS9-E' ),
		$f( 'Tiempo de Pesca: Surubí grande (río Paraná), pesca y devolución (video)', 'https://www.youtube.com/watch?v=l5cdtcuMQ6w' ),
		$f( 'Matías Jalil: ¿Dónde y cómo pescar un surubí? (video)', 'https://www.youtube.com/watch?v=HjmYOEio64U' ),
		$f( 'Matías Jalil: Pesca de surubí en Esquina, Fishing Show cap. 5 (video)', 'https://www.youtube.com/watch?v=ovnJfOqeHhI' ),
		$f( 'Matías Jalil: Cómo y cuándo clavar, pesca anclada de surubí (video)', 'https://www.youtube.com/watch?v=93aLllEKMuo' ),
		$f( 'Naturaltube: pesca de surubí en Esquina, Corrientes (video)', 'https://www.youtube.com/watch?v=L2WNzKoTUZU' ),
		$f( 'Cyril Chauquet: Giant Surubi Catfish, Chasing Monsters (documental)', 'https://www.youtube.com/watch?v=qlABe4o2Vr0' ),
		$f( 'MDS: surubí de más de 50 kg en Concordia (video)', 'https://www.youtube.com/watch?v=f7RRLtnYvCA' ),
		$f( 'Chris Dorsey’s Outdoor World: Unforgettable Golden Dorado in Argentina (video)', 'https://www.youtube.com/watch?v=ssQf5GVY3eE' ),
	],
];
$origen = [
	'dorado' => "Borrador revisado con el dueño (docs/borradores/cuando-pescar-dorado.md). Meses por operador: de los resúmenes de la búsqueda web (no se abrieron las páginas) y de los videos, resumidos por Gemini sin minutos verificados. Hipótesis nuestra, marcada en el texto: el Paraná medio podría arrancar antes; no hay fuente con meses para Esquina/Goya/La Paz. La veda extraordinaria (km 1109-1150, 9/9/2026) viene de prensa; confirmar vigencia en Recursos Naturales de Corrientes y actualizar la caja una vez por año. No se cita a The Angler Company (decisión del dueño).",
	'surubi' => "Borrador revisado con el dueño (docs/borradores/cuando-pescar-surubi.md). Solo la ventana de Concordia tiene meses declarados (guías en el video de MDS); Paraná medio y Alto Paraná son lectura nuestra de los videos y de Fishing World Guide (marzo-junio, un solo dato). Veda extraordinaria km 1232-1238 (Corrientes Disp. 618, Chaco 023, 29/5/2026) y Fiesta Nacional 2027 viven en la caja del año. Afuera por falta de fuente: tallas del atigrado, límites de Salto Grande, conflicto con la CARU, cupo de Chaco.",
];

$notas = [ 'dorado' => $dorado, 'surubi' => $surubi ];
$claves = [ 'dorado' => 'cuando-pescar-dorado', 'surubi' => 'cuando-pescar-surubi' ];
$enlace_texto = [ 'es' => 'nuestra guía de Goya', 'en' => 'our Goya guide', 'pt' => 'nosso guia de Goya' ];

/** Link to a destination guide in this language (else the Spanish one); plain name if there is none. */
function pm_n_destino( string $clave, string $idioma, string $nombre ): string {
	$p = pm_buscar( 'pm_destino', $clave, $idioma ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a>' : esc_html( $nombre );
}

foreach ( $notas as $especie => $porIdioma ) {
	$clave = $claves[ $especie ];
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$n  = $porIdioma[ $i ];
		$ya = array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i );
		if ( $ya ) {
			WP_CLI::log( "$clave ($i) ya existe: no toco nada." );
			continue;
		}
		$viaje = ( function_exists( 'pm_url_viaje' ) ? pm_url_viaje( $i ) : home_url( '/armar-viaje/' ) ) . '?destino=' . ( 'dorado' === $especie ? 'paso-de-la-patria' : 'goya' ) . '&especie=' . $especie;
		$enlaces = [
			'{goya_enlace}' => '<a href="' . esc_url( ( pm_buscar( 'pm_destino', 'goya', $i ) ?? pm_buscar( 'pm_destino', 'goya', 'es' ) ) ? get_permalink( pm_buscar( 'pm_destino', 'goya', $i ) ?? pm_buscar( 'pm_destino', 'goya', 'es' ) ) : home_url( '/' ) ) . '">' . $enlace_texto[ $i ] . '</a>',
			'{goya}'        => pm_n_destino( 'goya', $i, 'Goya' ),
			'{esquina}'     => pm_n_destino( 'esquina', $i, 'Esquina' ),
			'{paso}'        => pm_n_destino( 'paso-de-la-patria', $i, 'Paso de la Patria' ),
			'{lapaz}'       => pm_n_destino( 'la-paz', $i, 'La Paz' ),
			'{viaje}'       => esc_url( $viaje ),
			'{especie}'     => esc_url( pm_url_especie( $especie, $i ) ),
		];
		$cuerpo = strtr( implode( "\n\n", $n['cuerpo'] ), $enlaces );
		if ( preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) ) {
			WP_CLI::error( "$clave ($i): queda un marcador sin resolver: {$m[0]}" );
		}
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) «{$n['titulo']}» /{$n['slug']}/ — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes[ $especie ] ) . ' fuentes; title ' . mb_strlen( $n['seo_title'] ) . ' / description ' . mb_strlen( $n['seo_description'] ) . ' car.' );
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
			'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
			'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ),
			'fuentes' => $fuentes[ $especie ], 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'],
		];
		if ( 'es' === $i ) {
			$campos += [ 'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_origen' => $origen[ $especie ] ];
		}
		foreach ( $campos as $k => $v ) {
			update_field( $k, $v, $id );
		}
		update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
		$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes[ $especie ] ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'caja-anio' );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
		$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
	}
}
WP_CLI::success( $aplicar ? 'Notas creadas.' : 'Simulacro terminado.' );
