<?php
/**
 * Chascomús (Buenos Aires), 27/10/2026: sixth destination guide, in Spanish and English.
 * Built from docs/investigacion/chascomus.md (rounds 1 and 2, audited). Same `clave` as the "próximamente"
 * card (chascomus), which hides itself and lends its photo.
 *
 * Province of Buenos Aires, not the Litoral: its own authority (Ministerio de Desarrollo Agrario, Dirección
 * Provincial de Pesca) and rule book (Ley 11.477, Decreto 3237/95, veda del pejerrey). Nothing from Corrientes,
 * Entre Ríos or Neuquén applies.
 *
 * Published (each with its year and source): the licence page of the Ministry as the owner opened it on
 * 27/10/2026; the veda 1/9-30/11 with its days and the 15 km/h limit (Ministry's vedas page, text pasted by the
 * owner); the 25 cm minimum size (the Ministry's own measuring ruler, image sent by the owner on 27/10/2026);
 * what the municipal tourism site and 2026 tourism notes say about the lagoon and its access areas.
 * Bag limit during the veda: 10 pieces per angler per day for "the rest of the Buenos Aires lagoons" (owner, 27/10/2026:
 * "es el dato que figura en la web oficial"). NOT published (kept as hidden PENDIENTE notes): licence
 * fees, categories of licence, costs and schedules of ramps, clubs, guides and anything about motors on the lagoon.
 *
 * Idempotent: a guide that exists is never touched. Dry run first ($aplicar = false); run with wp-script.yml
 * (script = chascomus-2026-10-27).
 */

$aplicar = false;
$hoy     = '20261027';

function pm_ch_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_ch_h( string $texto ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$texto}</h2>\n<!-- /wp:heading -->";
}
function pm_ch_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_ch_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$vedas_url    = 'https://www.gba.gob.ar/desarrollo_agrario/DAPA/vedas';
$licencia_url = 'https://www.gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva';
$decreto_url  = 'https://normas.gba.gob.ar/documentos/V9A2qFPV.html';
$turismo_url  = 'https://turismo.chascomus.gob.ar/';
$nota_url     = 'https://www.canal26.com/turismo/2026/05/19/finde-largo-del-25-de-mayo-la-laguna-con-mejor-pesca-de-pejerrey-costanera-y-atardeceres-en-familia/';

$fuentes = [
	[ 'Ministerio de Desarrollo Agrario de Buenos Aires: vedas (veda anual del pejerrey)', $vedas_url, 1 ],
	[ 'Ministerio de Desarrollo Agrario de Buenos Aires: obtenga su licencia de pesca deportiva', $licencia_url, 1 ],
	[ 'Decreto 3237/95 (reglamento de la Ley 11.477), texto actualizado', $decreto_url, 1 ],
	[ 'Turismo de Chascomús (Municipalidad)', $turismo_url, 1 ],
	[ 'Canal 26: la laguna con mejor pesca de pejerrey, costanera y atardeceres (19/05/2026)', $nota_url, 0 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$comun = [
	'clave'                => 'chascomus',
	'autor'                => 'Equipo Pescamapa',
	'actualizado'          => $hoy,
	'fuentes'              => $fuentes_filas,
	'reglamento_autoridad' => 'Ministerio de Desarrollo Agrario de la Provincia de Buenos Aires (Dirección Provincial de Pesca)',
	'reglamento_url'       => $licencia_url,
];

$guias = [];

// --- Español ---------------------------------------------------------------
$guias['es'] = [
	'titulo' => 'Chascomús',
	'slug'   => 'pesca-chascomus',
	'cuerpo' => implode( "\n\n", [
		pm_ch_p( '<strong>Chascomús</strong> está en la provincia de <strong>Buenos Aires</strong>, sobre una laguna que es la mayor de las siete del sistema de las Encadenadas: unas 3.044 hectáreas, según el sitio de turismo municipal. Es pesca de <strong>pejerrey</strong> en una laguna llana, una escapada de un día desde la ciudad, muy distinta de la pesca de río del Litoral: acá el reglamento es el de la Provincia de Buenos Aires, con una <strong>veda anual</strong> y una <strong>talla mínima</strong>, y lo que más cambia la salida es el <strong>viento</strong>.' ),
		pm_ch_pendiente( 'confirmar con fuente abierta la distancia a la Ciudad de Buenos Aires (la prensa dice ≈120 km), la ruta de acceso y la profundidad media de la laguna.' ),
		pm_ch_h( 'Licencia de pesca deportiva' ),
		pm_ch_p( 'Quien pesca en aguas de la Provincia de Buenos Aires necesita su <a href="' . $licencia_url . '" rel="noopener">licencia de pesca deportiva</a> (consultada el 27/10/2026), prevista en el artículo 21 del <a href="' . $decreto_url . '" rel="noopener">Decreto 3237/95</a>, que reglamenta la Ley 11.477. Según el sitio del Ministerio de Desarrollo Agrario:' ),
		pm_ch_lista( [
			'Los <strong>menores de 14 años</strong> también deben tramitarla: es gratuita.',
			'Los <strong>jubilados</strong> la obtienen presentando el carné que acredite su condición, sin cargo y sin vencimiento.',
			'Quien no tenga jubilación, sea mayor de 65 años (varones) o de 60 (mujeres) y acredite residencia en la Provincia de Buenos Aires, tiene derecho a una licencia de las mismas características que la de un jubilado (Resolución 46/05).',
			'Para más información y para el trámite: Dirección Provincial de Pesca, calle 51 esquina 12, Torre I, piso 8, teléfono (221) 429-5315, y el instructivo del sitio del Ministerio.',
		] ),
		pm_ch_p( 'El valor de las licencias que se pagan no figura en esa página ni en el decreto: <strong>no lo podemos confirmar</strong>. Consultalo en el sitio del Ministerio antes de salir.' ),
		pm_ch_pendiente( 'categorías de licencia (federada, anual, turística, de concurso) y valores 2026: confirmar con el texto abierto del art. 21 y con la resolución anual del Ministerio; no se publican hasta entonces.' ),
		pm_ch_h( 'Talla mínima del pejerrey: 25 cm' ),
		pm_ch_p( 'El Ministerio de Desarrollo Agrario de la Provincia de Buenos Aires indica en su regla oficial de medición (material de difusión, consultado el 27/10/2026) una <strong>talla mínima de captura de 25 cm</strong> para el pejerrey, una especie autóctona. Medí el pez antes de decidir si lo llevás: lo que no llega a esa medida se devuelve al agua.' ),
		pm_ch_h( 'Veda anual del pejerrey: del 1 de septiembre al 30 de noviembre' ),
		pm_ch_p( 'Según la <a href="' . $vedas_url . '" rel="noopener">página de vedas</a> del Ministerio (consultada el 27/10/2026), la veda de pesca del pejerrey rige <strong>del 1 de septiembre al 30 de noviembre</strong> en todo el territorio de la provincia (Disposición 89/08, con renovación automática). Durante ese período:' ),
		pm_ch_lista( [
			'Están prohibidas las competencias deportivas relacionadas con el pejerrey y la pesca comercial en aguas interiores.',
			'La pesca deportiva y recreativa de pejerrey se permite solo los <strong>sábados, domingos y feriados</strong>, respetando el número máximo de piezas por pescador y por día (en algunas lagunas, como Chasicó, Cochicó y del Monte, también los viernes).',
			'Se prohíbe la motonáutica y las embarcaciones con motor fuera de borda de combustión interna navegan a una velocidad máxima de <strong>15 km/h</strong>.',
			'Máximo de <strong>10 piezas</strong> por pescador y por día en Chascomús (grupo "resto de las lagunas bonaerenses").',
		] ),
		pm_ch_p( 'En la veda, la página de vedas fija el número máximo de piezas por pescador y por día laguna por laguna. Chascomús no tiene un cupo propio: queda en el grupo de <strong>"el resto de las lagunas bonaerenses", con 10 piezas por pescador y por día</strong>. Confirmalo en la página oficial antes de salir, porque las disposiciones cambian.' ),
		pm_ch_pendiente( 'cupo fuera de la veda: la página de vedas solo fija el cupo durante la veda. Un resumen de buscador de la misma página mencionó 15 para "el resto de las lagunas"; el dueño confirmó 10 en la web oficial (27/10/2026). Si cambia, ajustar (docs/investigacion/chascomus.md §8).' ),
		pm_ch_h( 'Dónde se pesca: la laguna y sus accesos' ),
		pm_ch_p( 'Según el <a href="' . $turismo_url . '" rel="noopener">sitio de turismo de Chascomús</a>, la laguna es una de las elegidas para la pesca deportiva y los deportes acuáticos, y tiene un camino de circunvalación de unos 30 km. Notas de turismo de 2026 mencionan, para pescar sin lancha, la zona del club de pesca, la costanera municipal y el muelle público (un muelle de pescadores y mirador en la zona céntrica de la costanera), y para ir embarcado, bajadas de lanchas, alquiler de botes y guardería náutica.' ),
		pm_ch_p( 'No tenemos un inventario de rampas ni sus costos y horarios: <strong>no lo podemos confirmar</strong>. Preguntá en la costanera, en el club o a tu guía antes de ir con tu embarcación.' ),
		pm_ch_pendiente( 'rampas, clubes y guarderías con administrador, costo y horario (ronda propia; OpenStreetMap trae poco y sin nombres en la zona). No publicar nombres de clubes sin su sitio abierto.' ),
		pm_ch_h( 'El viento manda' ),
		pm_ch_p( 'En una laguna llana como esta el viento decide la salida. Arriba de esta guía mostramos el <strong>viento y la lluvia</strong> de los últimos días y de los próximos (modelo Open-Meteo para Chascomús): son una referencia, no una garantía.' ),
		pm_ch_h( 'Guías, lancheros y alojamiento' ),
		pm_ch_p( 'Todavía no listamos guías, lancheros ni alojamientos: no tenemos su sitio propio abierto ni su consentimiento. Si ofrecés este servicio en Chascomús, escribinos a info@pescamapa.com. Nunca publicamos datos de contacto sin que los confirmes.' ),
		pm_ch_h( 'Otras lagunas cerca' ),
		pm_ch_p( 'Chascomús forma parte de las Encadenadas, un sistema de siete lagunas conectadas. Las demás tienen otros accesos y reglas propias: las sumaremos cuando tengamos fuentes abiertas.' ),
		pm_ch_pendiente( 'nombres, accesos y arancel de las otras lagunas (Vitel, Adela, del Burro, Chis Chis, Tablillas, Las Barrancas): solo vienen de un informe sin fuente abierta; confirmar antes de nombrar pesqueros.' ),
		pm_ch_h( 'Reglas generales para ir tranquilo' ),
		pm_ch_lista( [
			'Llevá la licencia vigente (el trámite y las excepciones están arriba).',
			'Medí cada pejerrey: 25 cm es la talla mínima de captura.',
			'Del 1 de septiembre al 30 de noviembre, solo sábados, domingos y feriados, hasta 10 piezas por pescador y por día; sin motonáutica y a 15 km/h máx. con motor fuera de borda.',
			'Las normas cambian por disposición: confirmalas en la página oficial de vedas o con la Dirección Provincial de Pesca.',
		] ),
	] ),
	'campos' => [
		'h1'          => 'Pesca de pejerrey en Chascomús: licencia, talla mínima, veda y viento',
		'bajada'      => 'La laguna de Chascomús, en la provincia de Buenos Aires: qué licencia hace falta, la talla mínima de 25 cm, la veda del 1 de septiembre al 30 de noviembre y el viento del día.',
		'modalidades' => "Pesca de costa (muelle y costanera)\nPesca embarcada (bajadas de lanchas)\nSalida guiada",
		'epoca'       => 'Hay veda del pejerrey del 1 de septiembre al 30 de noviembre; el resto del año se pesca con licencia y respetando la talla mínima.',
		'seo_title'    => 'Pesca de pejerrey en Chascomús: licencia, veda y talla (2026)',
		'seo_description' => 'Guía de pesca en la laguna de Chascomús (Buenos Aires): licencia de pesca deportiva, talla mínima de 25 cm, veda del pejerrey del 1/9 al 30/11, accesos y viento del día. Fuentes oficiales.',
		'temporada'   => [
			[ 'meses' => '1 de septiembre – 30 de noviembre', 'que' => 'Veda del pejerrey en toda la provincia: solo sábados, domingos y feriados, hasta 10 piezas por pescador y por día en Chascomús, sin competencias ni motonáutica (Disposición 89/08, página de vedas del Ministerio).' ],
			[ 'meses' => 'Resto del año', 'que' => 'Se pesca con licencia y respetando la talla mínima de 25 cm. Confirmá cupos y disposiciones vigentes con el Ministerio.' ],
			[ 'meses' => 'Antes de cada salida', 'que' => 'Mirá el viento del día y las disposiciones vigentes: cambian por resolución.' ],
		],
		'reglamento_resumen' => 'Ley 11.477 y Decreto 3237/95 (Provincia de Buenos Aires): licencia de pesca deportiva obligatoria (gratuita para menores de 14, jubilados y quienes cumplan la Resolución 46/05; valor de las demás: consultar al Ministerio). Talla mínima del pejerrey: 25 cm. Veda del 1/9 al 30/11: solo sábados, domingos y feriados, hasta 10 piezas por pescador y por día en Chascomús, sin competencias ni motonáutica, fuera de borda a 15 km/h máx. (consultado el 27/10/2026).',
		'aguas' => [
			[ 'nombre' => 'Laguna de Chascomús', 'nota' => 'La mayor de las siete Encadenadas (≈3.044 ha, según el sitio municipal). Muelle de pescadores y mirador en la costanera; camino de circunvalación de unos 30 km.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => '¿Hace falta licencia para pescar en la laguna de Chascomús?', 'respuesta' => 'Sí: quien pesca en aguas de la Provincia de Buenos Aires necesita licencia de pesca deportiva (art. 21 del Decreto 3237/95). Menores de 14 años y jubilados la tienen sin cargo (consultado el 27/10/2026 en el sitio del Ministerio de Desarrollo Agrario). El valor de las demás no lo pudimos confirmar: consultalo en el Ministerio.' ],
			[ 'pregunta' => '¿Cuál es la talla mínima del pejerrey en Buenos Aires?', 'respuesta' => '25 cm de captura, según la regla de medición oficial del Ministerio de Desarrollo Agrario de la Provincia de Buenos Aires (consultada el 27/10/2026). Lo que no llega a esa medida se devuelve.' ],
			[ 'pregunta' => '¿Cuándo es la veda del pejerrey?', 'respuesta' => 'Del 1 de septiembre al 30 de noviembre, en toda la provincia de Buenos Aires (Disposición 89/08, con renovación automática), según la página de vedas del Ministerio (consultada el 27/10/2026).' ],
			[ 'pregunta' => '¿Se puede pescar pejerrey durante la veda?', 'respuesta' => 'Solo la pesca deportiva y recreativa, los sábados, domingos y feriados, respetando el máximo de piezas por pescador y por día. Están prohibidas las competencias de pejerrey y la pesca comercial.' ],
			[ 'pregunta' => '¿Cuántos pejerreyes se pueden llevar por día en Chascomús?', 'respuesta' => 'Durante la veda, 10 piezas por pescador y por día: Chascomús no tiene un cupo propio y queda en el grupo "el resto de las lagunas bonaerenses" de la página de vedas del Ministerio (consultada el 27/10/2026). Confirmalo en esa página antes de salir.' ],
			[ 'pregunta' => '¿Se puede navegar con motor durante la veda?', 'respuesta' => 'Durante la veda se prohíbe la motonáutica, y las embarcaciones con motor fuera de borda de combustión interna pueden navegar a una velocidad máxima de 15 km/h, según la página de vedas del Ministerio.' ],
			[ 'pregunta' => '¿Dónde se puede pescar sin lancha en Chascomús?', 'respuesta' => 'Notas de turismo de 2026 mencionan la zona del club de pesca, la costanera municipal y el muelle público de pescadores, que también es mirador. No tenemos un inventario con costos ni horarios.' ],
			[ 'pregunta' => '¿Hay guías de pesca y alquiler de lanchas?', 'respuesta' => 'Notas de turismo mencionan guías de pesca, alquiler de botes y bajadas de lanchas, pero no listamos prestadores sin su consentimiento o su sitio propio abierto. Si ofrecés el servicio, escribinos a info@pescamapa.com.' ],
		],
	],
];

// --- English ---------------------------------------------------------------
$guias['en'] = [
	'titulo' => 'Chascomús',
	'slug'   => 'fishing-chascomus',
	'cuerpo' => implode( "\n\n", [
		pm_ch_p( '<strong>Chascomús</strong> is in <strong>Buenos Aires</strong> province, on the largest of the seven lagoons of the Encadenadas system: about 3,044 hectares, according to the municipal tourism site. It is <strong>pejerrey</strong> (silverside) fishing on a shallow lagoon, a day trip from the city and very different from the river fishing of the Litoral: here the rules are the Province of Buenos Aires\'s, with an <strong>annual closed season</strong> and a <strong>minimum size</strong>, and what changes the trip most is the <strong>wind</strong>.' ),
		pm_ch_pendiente( 'confirm with an open source the distance to the city of Buenos Aires (press says ≈120 km), the access road and the lagoon\'s average depth.' ),
		pm_ch_h( 'Sport fishing licence' ),
		pm_ch_p( 'Anyone fishing in Buenos Aires province waters needs a <a href="' . $licencia_url . '" rel="noopener">sport fishing licence</a> (checked 27/10/2026), set by article 21 of <a href="' . $decreto_url . '" rel="noopener">Decree 3237/95</a>, which regulates Law 11.477. According to the Ministry of Agrarian Development\'s site:' ),
		pm_ch_lista( [
			'<strong>Children under 14</strong> must also get one: it is free.',
			'<strong>Retirees</strong> get it by showing their retirement card, free and with no expiry.',
			'Anyone without a pension who is over 65 (men) or 60 (women) and proves residence in Buenos Aires province is entitled to a licence with the same terms as a retiree\'s (Resolution 46/05).',
			'For information and the procedure: Dirección Provincial de Pesca, calle 51 corner 12, Torre I, 8th floor, phone +54 221 429-5315, and the instructions on the Ministry\'s site.',
		] ),
		pm_ch_p( 'The price of the paid licences is not on that page or in the decree: <strong>we cannot confirm it</strong>. Check the Ministry\'s site before you go.' ),
		pm_ch_h( 'Minimum size for pejerrey: 25 cm' ),
		pm_ch_p( 'The Buenos Aires Ministry of Agrarian Development states, on its official measuring ruler (outreach material, checked 27/10/2026), a <strong>minimum catch size of 25 cm</strong> for pejerrey, a native species. Measure the fish before deciding to keep it: anything shorter goes back in the water.' ),
		pm_ch_h( 'Annual pejerrey closed season: 1 September to 30 November' ),
		pm_ch_p( 'According to the Ministry\'s <a href="' . $vedas_url . '" rel="noopener">closed seasons page</a> (checked 27/10/2026), the pejerrey closed season runs <strong>from 1 September to 30 November</strong> across the whole province (Disposición 89/08, renewed automatically). During that period:' ),
		pm_ch_lista( [
			'Sport competitions related to pejerrey and commercial fishing in inland waters are prohibited.',
			'Sport and recreational pejerrey fishing is allowed only on <strong>Saturdays, Sundays and public holidays</strong>, within the per-angler daily limit (on some lagoons, such as Chasicó, Cochicó and del Monte, Fridays too).',
			'Powerboating is prohibited and boats with an internal-combustion outboard may travel at a maximum of <strong>15 km/h</strong>.',
			'Maximum of <strong>10 pieces</strong> per angler per day at Chascomús (group "the rest of the Buenos Aires lagoons").',
		] ),
		pm_ch_p( 'During the closed season the page sets the daily per-angler limit lagoon by lagoon. Chascomús has no limit of its own: it falls under <strong>"the rest of the Buenos Aires lagoons", at 10 pieces per angler per day</strong>. Confirm it on the official page before you go, because orders change.' ),
		pm_ch_h( 'Where to fish: the lagoon and its access points' ),
		pm_ch_p( 'According to the <a href="' . $turismo_url . '" rel="noopener">Chascomús tourism site</a>, the lagoon is one of those chosen for sport fishing and water sports, and has a ring road of about 30 km. 2026 tourism articles mention, for fishing without a boat, the area around the fishing club, the municipal waterfront and the public pier (a fishermen\'s pier and viewpoint in the central part of the waterfront), and for boat fishing, boat ramps, boat rental and boat storage.' ),
		pm_ch_p( 'We have no inventory of ramps with their costs and hours: <strong>we cannot confirm it</strong>. Ask on the waterfront, at the club or your guide before bringing your own boat.' ),
		pm_ch_h( 'The wind decides' ),
		pm_ch_p( 'On a shallow lagoon like this the wind decides the trip. At the top of this guide we show the <strong>wind and rain</strong> of the past days and the coming ones (Open-Meteo model for Chascomús): a reference, not a guarantee.' ),
		pm_ch_h( 'Guides, boatmen and lodging' ),
		pm_ch_p( 'We do not list guides, boatmen or lodging yet: we have neither their own website nor their consent. If you offer this service in Chascomús, write to info@pescamapa.com. We never publish contact details you have not confirmed.' ),
		pm_ch_h( 'Other lagoons nearby' ),
		pm_ch_p( 'Chascomús is part of the Encadenadas, a system of seven connected lagoons. The others have their own access and rules: we will add them when we have open sources.' ),
		pm_ch_h( 'General rules to go with peace of mind' ),
		pm_ch_lista( [
			'Carry a valid licence (the procedure and exemptions are above).',
			'Measure every pejerrey: 25 cm is the minimum catch size.',
			'From 1 September to 30 November, Saturdays, Sundays and public holidays only, up to 10 pieces per angler per day; no powerboating and 15 km/h max with an outboard.',
			'Rules change by official order: confirm them on the Ministry\'s closed seasons page or with the Dirección Provincial de Pesca.',
		] ),
	] ),
	'campos' => [
		'h1'          => 'Pejerrey fishing in Chascomús: licence, minimum size, closed season and wind',
		'bajada'      => 'The Chascomús lagoon, in Buenos Aires province: which licence you need, the 25 cm minimum size, the closed season from 1 September to 30 November and the day\'s wind.',
		'modalidades' => "Bank fishing (pier and waterfront)\nBoat fishing (boat ramps)\nGuided day",
		'epoca'       => 'The pejerrey closed season runs from 1 September to 30 November; the rest of the year you fish with a licence and respect the minimum size.',
		'seo_title'    => 'Pejerrey fishing in Chascomús: licence, closed season and size (2026)',
		'seo_description' => 'Fishing guide to the Chascomús lagoon (Buenos Aires): sport fishing licence, 25 cm minimum size, pejerrey closed season 1 Sep to 30 Nov, access points and the day\'s wind. Official sources.',
		'temporada'   => [
			[ 'meses' => '1 September – 30 November', 'que' => 'Pejerrey closed season across the province: Saturdays, Sundays and holidays only, up to 10 pieces per angler per day at Chascomús, no competitions or powerboating (Disposición 89/08, Ministry closed seasons page).' ],
			[ 'meses' => 'Rest of the year', 'que' => 'Fishing with a licence and respecting the 25 cm minimum size. Confirm limits and current orders with the Ministry.' ],
			[ 'meses' => 'Before every trip', 'que' => 'Check the day\'s wind and the current orders: they change by resolution.' ],
		],
		'reglamento_resumen' => 'Law 11.477 and Decree 3237/95 (Buenos Aires province): sport fishing licence required (free for under-14s, retirees and those covered by Resolution 46/05; price of the others: ask the Ministry). Pejerrey minimum size: 25 cm. Closed season 1/9 to 30/11: Saturdays, Sundays and holidays only, up to 10 pieces per angler per day at Chascomús, no competitions or powerboating, outboards at 15 km/h max (checked 27/10/2026).',
		'aguas' => [
			[ 'nombre' => 'Chascomús lagoon', 'nota' => 'The largest of the seven Encadenadas (≈3,044 ha, according to the municipal site). Fishermen\'s pier and viewpoint on the waterfront; ring road of about 30 km.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Do I need a licence to fish the Chascomús lagoon?', 'respuesta' => 'Yes: anyone fishing in Buenos Aires province waters needs a sport fishing licence (article 21 of Decree 3237/95). Under-14s and retirees get it free (checked 27/10/2026 on the Ministry of Agrarian Development site). We could not confirm the price of the others: ask the Ministry.' ],
			[ 'pregunta' => 'What is the minimum size for pejerrey in Buenos Aires?', 'respuesta' => '25 cm, according to the official measuring ruler of the Buenos Aires Ministry of Agrarian Development (checked 27/10/2026). Shorter fish are returned.' ],
			[ 'pregunta' => 'When is the pejerrey closed season?', 'respuesta' => 'From 1 September to 30 November across Buenos Aires province (Disposición 89/08, renewed automatically), according to the Ministry\'s closed seasons page (checked 27/10/2026).' ],
			[ 'pregunta' => 'Can I fish for pejerrey during the closed season?', 'respuesta' => 'Only sport and recreational fishing, on Saturdays, Sundays and public holidays, within the per-angler daily limit. Pejerrey competitions and commercial fishing are prohibited.' ],
			[ 'pregunta' => 'How many pejerrey can I keep per day at Chascomús?', 'respuesta' => 'During the closed season, 10 pieces per angler per day: Chascomús has no limit of its own and falls under "the rest of the Buenos Aires lagoons" on the Ministry\'s closed seasons page (checked 27/10/2026). Confirm it on that page before you go.' ],
			[ 'pregunta' => 'Can I use a motor during the closed season?', 'respuesta' => 'Powerboating is prohibited during the closed season, and boats with an internal-combustion outboard may travel at a maximum of 15 km/h, according to the Ministry\'s closed seasons page.' ],
			[ 'pregunta' => 'Where can I fish without a boat in Chascomús?', 'respuesta' => '2026 tourism articles mention the area around the fishing club, the municipal waterfront and the public fishermen\'s pier, which is also a viewpoint. We have no inventory with costs or hours.' ],
			[ 'pregunta' => 'Are there fishing guides and boat rentals?', 'respuesta' => 'Tourism articles mention fishing guides, boat rental and boat ramps, but we do not list providers without their consent or their own open website. If you offer the service, write to info@pescamapa.com.' ],
		],
	],
];

// --- Create ---------------------------------------------------------------------
foreach ( [ 'es', 'en' ] as $i ) {
	$g  = $guias[ $i ];
	$ya = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'chascomus', 'numberposts' => -1 ] );
	$existe = array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $i );
	if ( $existe ) {
		WP_CLI::log( "chascomus ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "chascomus ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $fuentes_filas ) . ' fuentes' );
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
	$campos = $g['campos'] + $comun + [ 'idioma' => $i ];
	if ( 'es' === $i ) {
		// Same pin as the "próximamente" card; no pejerrey species sheet exists yet.
		$campos += [ 'region' => 'pampa', 'provincia' => 'buenos-aires', 'lat' => -35.57, 'lon' => -58.01, 'especies' => [] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	if ( 'es' === $i ) {
		$proximo = get_page_by_path( 'chascomus', OBJECT, 'pm_proximo' );
		if ( $proximo && get_post_thumbnail_id( $proximo->ID ) ) {
			set_post_thumbnail( $id, get_post_thumbnail_id( $proximo->ID ) );
			WP_CLI::log( 'foto: la de la tarjeta "próximamente"' );
		} else {
			WP_CLI::warning( 'sin foto: cargala desde el panel (Imagen destacada)' );
		}
	}
	WP_CLI::log( "✓ chascomus ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas y ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes leídas de vuelta' );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
