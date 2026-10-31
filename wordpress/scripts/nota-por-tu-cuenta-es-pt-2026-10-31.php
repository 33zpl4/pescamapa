<?php
/**
 * Spanish and Portuguese versions of the note "Fly fishing Argentina on a budget" (clave patagonia-por-tu-cuenta), 31/10/2026.
 * Owner (31/10/2026): "update to pt and esp the new nota too, today". Same content, same sources and same limits as the English
 * note (see nota-por-tu-cuenta-en-2026-10-31.php and its two fixes): one figure for the guided day (around USD 550 per boat), no table of
 * operators, no trip total, no art. 22 of the Reglamento (not read). Spanish uses voseo; Portuguese is Brazilian.
 *
 * The Spanish note becomes the source of the shared fields (type, date, species, cover photo): they are set on it, and the English and
 * Portuguese notes read them from it. The English note was created first, so here it only gets the translation mark.
 * The Portuguese note links the Spanish guides (there are no Portuguese Bariloche or Junín guides) and says so in the link text.
 * Idempotent: a language that already has the note is not touched. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = nota-por-tu-cuenta-es-pt-2026-10-31).
 */

$aplicar = false;
$hoy     = '20261031';
$clave   = 'patagonia-por-tu-cuenta';

function pm_dz_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_dz_h2( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_dz_lista( array $items, bool $ordenada = false ): string {
	$li  = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	$tag = $ordenada ? 'ol' : 'ul';
	$att = $ordenada ? ' {"ordered":true}' : '';
	return "<!-- wp:list{$att} -->\n<{$tag} class=\"wp-block-list\">{$li}</{$tag}>\n<!-- /wp:list -->";
}
function pm_dz_tabla( array $cabecera, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cabecera ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
function pm_dz_caja( string $titulo, array $items, string $pie ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$titulo}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph {\"className\":\"note\"} -->\n<p class=\"note\">{$pie}</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->";
}
/** Link to a destination guide: this language, else Spanish (the Portuguese note then says "(em espanhol)"). */
function pm_dz_destino( string $c, string $nombre, string $i ): string {
	$p = pm_buscar( 'pm_destino', $c, $i ) ?? pm_buscar( 'pm_destino', $c, 'es' );
	if ( ! $p ) {
		return esc_html( $nombre );
	}
	$aviso = ( 'pt' === $i && 'es' === pm_idioma_de( $p->ID ) ) ? ' (em espanhol)' : '';
	return '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $nombre ) . '</a>' . $aviso;
}
function pm_dz_nota( string $c, string $texto, string $i ): string {
	$p = pm_buscar( 'pm_nota', $c, $i ) ?? pm_buscar( 'pm_nota', $c, 'es' );
	if ( ! $p ) {
		return esc_html( $texto );
	}
	$aviso = ( 'pt' === $i && 'es' === pm_idioma_de( $p->ID ) ) ? ' (em espanhol)' : '';
	return '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $texto ) . '</a>' . $aviso;
}

$u = [
	'caza' => 'https://cazaypesca.neuquen.gob.ar/permisos/',
	'rn'   => 'https://www.permisosdepesca.com.ar/',
	'tur'  => 'https://turismo.neuquen.gob.ar/actividades/?turapi_prestacion=Pesca',
	'balsa' => 'https://gobierno.rionegro.gov.ar/servicio/213/balsas',
	'nqi'  => 'https://www.neuqueninforma.gob.ar/noticias/2026/10/28/266319-neuquen-abre-la-temporada-de-pesca-con-una-nueva-oficina-a-la-vera-del-rio-chimehuin',
];

$notas = [];

// ------------------------------------------------------------------ Español
$notas['es'] = [
	'titulo'          => 'Pesca con mosca en la Argentina con poco presupuesto: cómo pescar la Patagonia sin lodge',
	'slug'            => 'pesca-con-mosca-patagonia-por-tu-cuenta',
	'bajada'          => 'Qué necesita un viaje de pesca por tu cuenta en la Patagonia: el permiso 2026/27 y cuánto cuesta, las reglas que definen qué llevar, cómo funciona de verdad el acceso al agua pública y cuánto cuesta un día con guía. Con fuentes y fechas.',
	'seo_title'       => 'Pesca con mosca en la Patagonia por tu cuenta, sin lodge',
	'seo_description' => 'Pescar la Patagonia sin lodge: el permiso 2026/27 y su costo, reglas de equipo, acceso al agua pública y cuánto cuesta un día con guía, con fuentes.',
	'datos'           => [
		'Un permiso de no residente para 2026/27 cuesta ARS 50.000 por día, ARS 140.000 por 10 días o ARS 280.000 por la temporada en Neuquén y Río Negro (vale en Río Negro, Neuquén, Chubut y los Parques Nacionales).',
		'Las zonas preferenciales piden un permiso adicional (ARS 12.500 por día, 25.000 por 10 días, 50.000 por la temporada, según lo anunciado por Neuquén).',
		'Las botas con suela de fieltro están prohibidas en todas las aguas de Río Negro y en cada agua del Parque Nacional que lista la prohibición: no lleves fieltro a ningún lugar de la Patagonia.',
		'Un día completo con guía y bote en el Limay se publica alrededor de USD 550 por bote para uno o dos pescadores; el permiso suele ser aparte.',
		'Que un río sea público no te da derecho a cruzar un campo privado para llegar: el camino legal es un camino público, el acceso de un puente, una rampa de botes o el permiso del dueño.',
	],
	'cuerpo'          => [
		pm_dz_p( '<strong>Respuesta corta.</strong> Sí, se puede pescar la Patagonia sin lodge. Los costos fijos que podemos respaldar con fuente hoy son el <strong>permiso</strong> (ARS 140.000 por 10 días para un no residente en Neuquén y Río Negro en 2026/27) y, si querés bote, <strong>un día con un guía local</strong> (alrededor de USD 550 por bote para uno o dos pescadores en el Limay, según las tarifas que publican los operadores). Todavía no pusimos precio con fuente al auto, las cabañas, el combustible ni el supermercado, así que esta guía <strong>no da un total del viaje</strong>: te cuenta qué es seguro y qué sigue abierto.' ),
		pm_dz_h2( '¿Cuánto cuesta un viaje por tu cuenta?' ),
		pm_dz_p( 'Esto es lo que podemos respaldar, con la fecha en que lo leímos. Todo lo demás queda afuera a propósito.' ),
		pm_dz_tabla( [ 'Rubro', 'Qué encontramos', 'Fuente' ], [
			[ '<strong>Permiso de pesca, no residente</strong>', 'ARS 50.000 por día, ARS 140.000 por 10 días, ARS 280.000 por la temporada, por pescador', 'Valores de Neuquén informados el 16 de octubre de 2026; portal de Río Negro leído el 30 de octubre de 2026' ],
			[ '<strong>Permiso adicional, zonas preferenciales</strong>', 'ARS 12.500 por día, ARS 25.000 por 10 días, ARS 50.000 por la temporada', 'Valores de Neuquén informados el 16 de octubre de 2026' ],
			[ '<strong>Guía y bote, día completo, 1 o 2 pescadores</strong>', 'Alrededor de USD 550 por bote', 'Sitios web de los propios operadores, tarifas leídas el 31 de octubre de 2026' ],
			[ '<strong>Auto, cabaña para dos, combustible, supermercado</strong>', 'Todavía sin precio', 'Los sumamos cuando tengamos fuente y fecha' ],
		] ),
		pm_dz_p( '<strong>Un ejemplo.</strong> Dos pescadores en un viaje de diez días con un día en bote: dos permisos de 10 días son 2 × ARS 140.000 = ARS 280.000, y el día con guía son unos USD 550 por el bote. No convertimos pesos a dólares porque la cotización se mueve: usá la del día en que planifiques.' ),
		pm_dz_p( 'Preguntale a cada guía si el permiso está incluido: la mayoría de los operadores que leímos lo lista como extra, y algunos se ofrecen a tramitarlo por unos USD 35 a 40.' ),
		pm_dz_h2( 'El permiso: qué cubre y dónde se compra' ),
		pm_dz_lista( [
			'<strong>Un permiso, varias jurisdicciones.</strong> Según los anexos 2026/27 de Neuquén y Río Negro, el permiso patagónico vale en Río Negro, Neuquén, Chubut y los Parques Nacionales.',
			'<strong>Dónde comprarlo.</strong> En Neuquén, en la plataforma oficial de Caza y Pesca (<a href="' . $u['caza'] . '" rel="noopener">cazaypesca.neuquen.gob.ar/permisos</a>). Del lado de Bariloche, en la Mesa Directiva de Pesca Deportiva Zona Andina (<a href="' . $u['rn'] . '" rel="noopener">permisosdepesca.com.ar</a>). Los no residentes necesitan el pasaporte.',
			'<strong>Adicionales.</strong> Algunas zonas piden un permiso adicional, por ejemplo partes del Chimehuín y el Correntoso (mirá nuestra guía de {junin}). El trolling tiene su propio adicional: ARS 25.000 por día, ARS 50.000 por 10 días, ARS 100.000 por la temporada.',
			'<strong>Temporada.</strong> Empieza el 1 de noviembre de 2026. Algunas aguas cierran antes: Neuquén informa que ciertas lagunas y ríos terminan el 1 de mayo de 2027. Leé la ficha de cada agua en el portal antes de planificar, y mirá {trucha} para los meses por río.',
			'<strong>Comprá en los portales provinciales.</strong> Si un guía o un lodge vende el permiso dentro de un paquete, compará el precio con la tabla de arriba.',
		] ),
		pm_dz_h2( 'Las reglas que definen qué llevar' ),
		pm_dz_lista( [
			'<strong>Sin suelas de fieltro.</strong> Las botas de vadeo con suela de fieltro están prohibidas en todas las aguas de jurisdicción de Río Negro y en las aguas del Parque Nacional cuya ficha lo dice (cada una repite la prohibición). El anexo de Neuquén que leímos no menciona el fieltro, y todavía no leímos los artículos generales del Reglamento. La regla segura es no llevar fieltro a ningún lugar de la Patagonia: suela de goma, con tachas si querés.',
			'<strong>Moscas sin rebaba.</strong> Varias aguas que leímos exigen un solo anzuelo sin rebaba o con la rebaba aplastada, por ejemplo el lago Traful. Atá o comprá moscas con la rebaba aplastada y quedás cubierto.',
			'<strong>Equipo limpio y seco.</strong> El Reglamento tiene artículos sobre el equipo que viene del exterior y sobre la desinfección que todavía no leímos. Llevá botas y waders limpios y completamente secos, y preguntá en la oficina de permisos o a tu guía.',
			'<strong>Solo mosca y devolución.</strong> Muchas aguas son solo mosca y de captura y devolución. El Limay superior dentro del Parque Nacional Nahuel Huapi es una: botes sin motor, solo mosca y devolución desde el lago hasta Rincón Chico. Cada agua tiene su propia ficha: mirá {bariloche} y {junin}.',
			'<strong>Neuquén: presas, puentes y guías.</strong> No se puede pescar a menos de 500 m de una presa ni desde un puente o pasarela, y contratar a un guía que no está habilitado es una infracción. El 28 de octubre de 2026 el director provincial de Fauna de Neuquén dijo que la multa mínima ahora es de ARS 1.800.000, hasta ARS 390 millones.',
			'<strong>Los ríos crecen de un día para el otro.</strong> El mismo funcionario advirtió que la lluvia en la cordillera puede subir un río de un día para el otro, incluso aguas arriba de donde estás. Mirá el pronóstico y el río antes de vadear.',
		] ),
		pm_dz_h2( 'Agua pública, tierra privada: tres preguntas' ),
		pm_dz_p( 'Un río puede ser público y su costa privada. Antes de manejar una hora hasta un lugar, respondé tres preguntas.' ),
		pm_dz_lista( [
			'<strong>¿Esa agua está abierta a la pesca en tus fechas?</strong> Cada río y lago tiene una ficha en el Reglamento con su temporada, modalidad y límites.',
			'<strong>¿De quién es la costa?</strong> El Código Civil y Comercial pone a los ríos en el dominio público, con su agua, sus playas y su lecho hasta la línea de ribera que fija el promedio de las máximas crecidas ordinarias (art. 235). También dice que el dueño de un inmueble colindante con un cauce apto para el transporte por agua debe dejar libre una franja de quince metros y no puede hacer ahí ningún acto que menoscabe esa actividad (art. 1974, el <em>camino de sirga</em>). Eso protege la navegación. No encontramos en esos artículos un derecho general de los pescadores a cruzar un campo privado, así que no lo tomes como tal. Si un río determinado es apto para el transporte por agua es una cuestión legal: preguntale a un guía local o a la oficina de turismo. Esto no es asesoramiento legal.',
			'<strong>¿Cuál es el camino legal para llegar?</strong> Un camino público, el acceso de un puente, una rampa de botes o el permiso del dueño. El anexo de Parques Nacionales nombra el acceso público al río Traful: la confluencia con el río Minero.',
		], true ),
		pm_dz_p( 'No listamos accesos que no verificamos. Cuando tengamos fuente y fecha de una rampa o un acceso público, lo ponemos en la guía del destino.' ),
		pm_dz_h2( 'Cuándo conviene un día con guía' ),
		pm_dz_p( 'Algunas aguas son para bote. En el Limay superior se entra en botes sin motor, y un bote de deriva cubre tramos adonde no llegás caminando. Muchos pescadores independientes planifican su propio viaje y suman uno o dos días con guía.' ),
		pm_dz_p( 'Las tarifas de los sitios web de varios operadores, leídas el 31 de octubre de 2026, rondan los USD 550 por un día completo con bote para uno o dos pescadores, y el permiso suele ser aparte. Son tarifas publicadas, no cotizaciones para tus fechas, y no pescamos con esos operadores, así que no es una recomendación. En Neuquén la provincia mantiene una lista oficial de guías y agencias habilitados en <a href="' . $u['tur'] . '" rel="noopener">turismo.neuquen.gob.ar</a>. Si una tarifa cambió, avisanos por <a href="{viaje}">el formulario del viaje</a> y actualizamos esta nota.' ),
		pm_dz_p( '<strong>¿Querés un viaje en auto con un día o dos en bote?</strong> Contanos tus fechas, cuántos van y tu experiencia en <a href="{viaje}">el formulario del viaje</a>. Pasamos el pedido a un guía local y te escribimos con opciones, sin costo ni compromiso. Nuestra <a href="{paquetes}">página de paquetes de Bariloche</a> muestra un día completo en el Limay, con lo que incluye y un precio de referencia.' ),
		pm_dz_h2( 'Para moverse' ),
		pm_dz_p( 'Un dato práctico: la balsa de Villa Llanquín cruza vehículos sobre el Limay entre la Ruta 40 y Villa Llanquín. Es gratuita y funciona de 8:00 a 20:00, según la vialidad de Río Negro (<a href="' . $u['balsa'] . '" rel="noopener">Vialidad Rionegrina</a>). Esa página no dice nada sobre el acceso para pescar en ese lugar.' ),
		pm_dz_caja( 'Actualizado · temporada 2026/27, 31 de octubre de 2026', [
			'La temporada empieza el 1 de noviembre de 2026. Los valores de los permisos son los de la tabla de arriba; Neuquén los vende online desde el 15 de octubre. Para el adicional de zona preferencial, el texto del anexo que leímos muestra ARS 10.000, 20.000 y 40.000, mientras que Neuquén anunció ARS 12.500, 25.000 y 50.000: confirmá el monto al comprar.',
			'La Dirección de Fauna de Neuquén abre el 3 de noviembre de 2026 una oficina junto a la boca del Chimehuín, después de quince años sin presencia en ese lugar (<a href="' . $u['nqi'] . '" rel="noopener">Neuquén Informa</a>).',
			'Las tarifas de los operadores son las publicadas el 31 de octubre de 2026 y pueden cambiar.',
		], 'Este recuadro se actualiza una vez por año; el resto de la guía no cambia.' ),
		pm_dz_h2( 'Lo que esta guía todavía no cubre' ),
		pm_dz_p( 'Alquiler de autos (4x2 o 4x4 en caminos de ripio), cabañas para dos en Junín de los Andes, San Martín de los Andes y Dina Huapi, precios de combustible, supermercado, recomendaciones de equipo, los puestos de desinfección y los accesos públicos con nombre no están acá porque no los verificamos con una fuente y una fecha. Los sumamos cuando los tengamos, y después el total del viaje.' ),
		pm_dz_p( '<em>No pescamos con los operadores ni con las personas citadas acá. Esta guía resume información pública y nuestras propias páginas; la interpretación es nuestra y puede tener errores.</em>' ),
	],
	'fuentes'         => [
		[ 'Diario 7 Lagos: la temporada empieza el 1 de noviembre y ya se venden permisos (16/10/2026; informa los valores provinciales)', 'https://www.diario7lagos.com.ar/noticias/2026/10/16/80723-la-temporada-de-pesca-comienza-el-1-de-noviembre-y-ya-se-pueden-comprar-permisos', 0, $hoy ],
		[ 'Neuquén Informa: nueva oficina en la boca del Chimehuín, multas y advertencias por los ríos (28/10/2026)', $u['nqi'], 1, $hoy ],
		[ 'Permisos de Pesca: Mesa Directiva de Pesca Deportiva Zona Andina (Río Negro)', $u['rn'], 1, '20261030' ],
		[ 'Reglamento 2026/27: Anexo Neuquén', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/', 1, '20261030' ],
		[ 'Reglamento 2026/27: Anexo Río Negro', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-rio-negro/', 1, '20261030' ],
		[ 'Reglamento 2026/27: aguas de Parques Nacionales', 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/', 1, '20261030' ],
		[ 'AICACYP: Reglamento 2026/27, aguas de Parques Nacionales (copia, PDF)', 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Parques-Nacionales.pdf', 0, '20261030' ],
		[ 'Código Civil y Comercial (Ley 26.994), arts. 235 y 1974', 'https://www.argentina.gob.ar/normativa/nacional/ley-26994-235975/actualizacion', 1, $hoy ],
		[ 'Turismo Neuquén: lista oficial de prestadores de pesca', $u['tur'], 1, $hoy ],
		[ 'Vialidad Rionegrina: balsas (Villa Llanquín)', $u['balsa'], 1, $hoy ],
		[ 'Trout Bariloche: excursiones de un día y tarifas', 'https://troutbariloche.com/day-trips/', 0, $hoy ],
		[ 'Southern Rivers Patagonia: día completo en el Limay', 'https://southernriverspatagonia.com/limay-full-day-fly-fishing', 0, $hoy ],
		[ 'Patagonia in the Loop: tarifas', 'https://www.patagoniaintheloop.com/', 0, $hoy ],
		[ 'Patagonia Drift Fly Fishing: excursiones de un día', 'https://www.patagoniadrift.com.ar/en/fly-fishing-day-trips/', 0, $hoy ],
	],
];

// ------------------------------------------------------------------ Português (Brasil)
$notas['pt'] = [
	'titulo'          => 'Pesca com mosca na Argentina com orçamento enxuto: como pescar a Patagônia sem lodge',
	'slug'            => 'pesca-com-mosca-patagonia-por-conta-propria',
	'bajada'          => 'O que um roteiro de pesca por conta própria na Patagônia precisa: a licença 2026/27 e quanto custa, as regras que definem o que levar, como funciona de verdade o acesso à água pública e quanto custa um dia com guia. Com fontes e datas.',
	'seo_title'       => 'Pesca com mosca na Patagônia por conta própria, sem lodge',
	'seo_description' => 'Pescar a Patagônia sem lodge: a licença 2026/27 e seu custo, regras de equipamento, acesso à água pública e quanto custa um dia com guia, com fontes.',
	'datos'           => [
		'Uma licença de não residente para 2026/27 custa ARS 50.000 por dia, ARS 140.000 por 10 dias ou ARS 280.000 pela temporada em Neuquén e Río Negro (vale em Río Negro, Neuquén, Chubut e nos Parques Nacionais).',
		'As zonas preferenciais pedem uma licença adicional (ARS 12.500 por dia, 25.000 por 10 dias, 50.000 pela temporada, conforme anunciado por Neuquén).',
		'Botas com sola de feltro são proibidas em todas as águas de Río Negro e em cada água do Parque Nacional que lista a proibição: não leve feltro a nenhum lugar da Patagônia.',
		'Um dia completo com guia e barco no Limay é publicado por volta de USD 550 por barco para um ou dois pescadores; a licença costuma ser à parte.',
		'Um rio ser público não dá o direito de atravessar uma propriedade privada para chegar a ele: o caminho legal é uma estrada pública, o acesso de uma ponte, uma rampa de barcos ou a autorização do dono.',
	],
	'cuerpo'          => [
		pm_dz_p( '<strong>Resposta curta.</strong> Sim, dá para pescar na Patagônia sem lodge. Os custos fixos que conseguimos comprovar com fonte hoje são a <strong>licença</strong> (ARS 140.000 por 10 dias para um não residente em Neuquén e Río Negro em 2026/27) e, se você quiser barco, <strong>um dia com um guia local</strong> (por volta de USD 550 por barco para um ou dois pescadores no Limay, segundo as tarifas que os operadores publicam). Ainda não colocamos preço com fonte no carro, nas cabanas, no combustível nem no supermercado, então este guia <strong>não dá um total da viagem</strong>: ele conta o que é certo e o que ainda está em aberto.' ),
		pm_dz_h2( 'Quanto custa uma viagem por conta própria?' ),
		pm_dz_p( 'Isto é o que conseguimos comprovar, com a data em que lemos. Todo o resto fica de fora de propósito.' ),
		pm_dz_tabla( [ 'Item', 'O que encontramos', 'Fonte' ], [
			[ '<strong>Licença de pesca, não residente</strong>', 'ARS 50.000 por dia, ARS 140.000 por 10 dias, ARS 280.000 pela temporada, por pescador', 'Valores de Neuquén informados em 16 de outubro de 2026; portal de Río Negro lido em 30 de outubro de 2026' ],
			[ '<strong>Licença adicional, zonas preferenciais</strong>', 'ARS 12.500 por dia, ARS 25.000 por 10 dias, ARS 50.000 pela temporada', 'Valores de Neuquén informados em 16 de outubro de 2026' ],
			[ '<strong>Guia e barco, dia completo, 1 ou 2 pescadores</strong>', 'Por volta de USD 550 por barco', 'Sites dos próprios operadores, tarifas lidas em 31 de outubro de 2026' ],
			[ '<strong>Carro, cabana para dois, combustível, supermercado</strong>', 'Ainda sem preço', 'Vamos incluir quando tivermos fonte e data' ],
		] ),
		pm_dz_p( '<strong>Um exemplo.</strong> Dois pescadores numa viagem de dez dias com um dia de barco: duas licenças de 10 dias são 2 × ARS 140.000 = ARS 280.000, e o dia com guia são uns USD 550 pelo barco. Não convertemos pesos em dólares porque a cotação muda: use a do dia em que você for planejar.' ),
		pm_dz_p( 'Pergunte a cada guia se a licença está incluída: a maioria dos operadores que lemos a lista como extra, e alguns se oferecem para providenciá-la por uns USD 35 a 40.' ),
		pm_dz_h2( 'A licença: o que cobre e onde comprar' ),
		pm_dz_lista( [
			'<strong>Uma licença, várias jurisdições.</strong> Segundo os anexos 2026/27 de Neuquén e Río Negro, a licença patagônica vale em Río Negro, Neuquén, Chubut e nos Parques Nacionais.',
			'<strong>Onde comprar.</strong> Em Neuquén, na plataforma oficial de Caza y Pesca (<a href="' . $u['caza'] . '" rel="noopener">cazaypesca.neuquen.gob.ar/permisos</a>). Do lado de Bariloche, na Mesa Directiva de Pesca Deportiva Zona Andina (<a href="' . $u['rn'] . '" rel="noopener">permisosdepesca.com.ar</a>). Não residentes precisam do passaporte.',
			'<strong>Adicionais.</strong> Algumas zonas pedem uma licença adicional, por exemplo partes do Chimehuín e do Correntoso (veja nosso guia de {junin}). A pesca de corrico (trolling) tem seu próprio adicional: ARS 25.000 por dia, ARS 50.000 por 10 dias, ARS 100.000 pela temporada.',
			'<strong>Temporada.</strong> Começa em 1º de novembro de 2026. Algumas águas fecham antes: Neuquén informa que certas lagoas e rios terminam em 1º de maio de 2027. Leia a ficha de cada água no portal antes de planejar, e veja {trucha} para os meses por rio.',
			'<strong>Compre nos portais provinciais.</strong> Se um guia ou um lodge vender a licença dentro de um pacote, compare o preço com a tabela acima.',
		] ),
		pm_dz_h2( 'As regras que definem o que levar' ),
		pm_dz_lista( [
			'<strong>Sem solas de feltro.</strong> Botas de vadeio com sola de feltro são proibidas em todas as águas de jurisdição de Río Negro e nas águas do Parque Nacional cuja ficha diz isso (cada uma repete a proibição). O anexo de Neuquén que lemos não menciona o feltro, e ainda não lemos os artigos gerais do Reglamento. A regra segura é não levar feltro a nenhum lugar da Patagônia: sola de borracha, com cravos se quiser.',
			'<strong>Moscas sem farpa.</strong> Várias águas que lemos exigem um único anzol sem farpa ou com a farpa amassada, por exemplo o lago Traful. Amarre ou compre moscas com a farpa amassada e você fica coberto.',
			'<strong>Equipamento limpo e seco.</strong> O Reglamento tem artigos sobre equipamento vindo do exterior e sobre desinfecção que ainda não lemos. Leve botas e waders limpos e completamente secos, e pergunte no escritório de licenças ou ao seu guia.',
			'<strong>Só mosca e devolução.</strong> Muitas águas são só de mosca e de captura e devolução. O Limay superior dentro do Parque Nacional Nahuel Huapi é uma delas: barcos sem motor, só mosca e devolução do lago até Rincón Chico. Cada água tem sua própria ficha: veja {bariloche} e {junin}.',
			'<strong>Neuquén: barragens, pontes e guias.</strong> Não se pode pescar a menos de 500 m de uma barragem nem de uma ponte ou passarela, e contratar um guia que não é habilitado é uma infração. Em 28 de outubro de 2026 o diretor provincial de Fauna de Neuquén disse que a multa mínima agora é de ARS 1.800.000, até ARS 390 milhões.',
			'<strong>Os rios sobem de um dia para o outro.</strong> O mesmo funcionário alertou que a chuva na cordilheira pode fazer um rio subir de um dia para o outro, mesmo acima de onde você está. Confira a previsão e o rio antes de vadear.',
		] ),
		pm_dz_h2( 'Água pública, terra privada: três perguntas' ),
		pm_dz_p( 'Um rio pode ser público e sua margem privada. Antes de dirigir uma hora até um ponto, responda a três perguntas.' ),
		pm_dz_lista( [
			'<strong>Essa água está aberta para a pesca nas suas datas?</strong> Cada rio e lago tem uma ficha no Reglamento com sua temporada, modalidade e limites.',
			'<strong>De quem é a margem?</strong> O Código Civil e Comercial coloca os rios no domínio público, com sua água, suas praias e seu leito até a linha de ribeira fixada pela média das maiores cheias ordinárias (art. 235). Diz também que o dono de um imóvel vizinho a um curso apto para o transporte por água deve deixar livre uma faixa de quinze metros e não pode fazer ali nada que prejudique essa atividade (art. 1974, o <em>camino de sirga</em>). Isso protege a navegação. Não encontramos nesses artigos um direito geral de os pescadores atravessarem uma propriedade privada, então não o trate como tal. Se um determinado rio é apto para o transporte por água é uma questão jurídica: pergunte a um guia local ou ao escritório de turismo. Isto não é aconselhamento jurídico.',
			'<strong>Qual é o caminho legal para chegar?</strong> Uma estrada pública, o acesso de uma ponte, uma rampa de barcos ou a autorização do dono. O anexo dos Parques Nacionais nomeia o acesso público ao rio Traful: a confluência com o rio Minero.',
		], true ),
		pm_dz_p( 'Não listamos acessos que não verificamos. Quando tivermos fonte e data de uma rampa ou de um acesso público, ele entra no guia do destino.' ),
		pm_dz_h2( 'Quando vale a pena um dia com guia' ),
		pm_dz_p( 'Algumas águas são para barco. No Limay superior entra-se em barcos sem motor, e um barco de descida cobre trechos aonde você não chega a pé. Muitos pescadores independentes planejam a própria viagem e acrescentam um ou dois dias com guia.' ),
		pm_dz_p( 'As tarifas nos sites de vários operadores, lidas em 31 de outubro de 2026, ficam por volta de USD 550 por um dia completo de barco para um ou dois pescadores, e a licença costuma ser à parte. São tarifas publicadas, não cotações para as suas datas, e não pescamos com esses operadores, então isto não é uma recomendação. Em Neuquén a província mantém uma lista oficial de guias e agências habilitados em <a href="' . $u['tur'] . '" rel="noopener">turismo.neuquen.gob.ar</a>. Se uma tarifa mudou, avise-nos pelo <a href="{viaje}">formulário da viagem</a> e atualizamos esta nota.' ),
		pm_dz_p( '<strong>Quer uma viagem de carro com um ou dois dias de barco?</strong> Conte suas datas, quantos vão e a sua experiência no <a href="{viaje}">formulário da viagem</a>. Repassamos o pedido a um guia local e respondemos com opções, sem custo nem compromisso. Nossa <a href="{paquetes}">página de pacotes de Bariloche</a> (em espanhol) mostra um dia completo no Limay, com o que inclui e um preço de referência.' ),
		pm_dz_h2( 'Para se deslocar' ),
		pm_dz_p( 'Um detalhe prático: a balsa de Villa Llanquín leva veículos pelo Limay entre a Rota 40 e Villa Llanquín. É gratuita e funciona das 8h às 20h, segundo a vialidade de Río Negro (<a href="' . $u['balsa'] . '" rel="noopener">Vialidad Rionegrina</a>). Essa página não diz nada sobre o acesso para pescar naquele lugar.' ),
		pm_dz_caja( 'Atualizado · temporada 2026/27, 31 de outubro de 2026', [
			'A temporada começa em 1º de novembro de 2026. Os valores das licenças são os da tabela acima; Neuquén as vende online desde 15 de outubro. Para o adicional de zona preferencial, o texto do anexo que lemos mostra ARS 10.000, 20.000 e 40.000, enquanto Neuquén anunciou ARS 12.500, 25.000 e 50.000: confirme o valor ao comprar.',
			'A Direção de Fauna de Neuquén abre em 3 de novembro de 2026 um escritório junto à foz do Chimehuín, depois de quinze anos sem presença nesse local (<a href="' . $u['nqi'] . '" rel="noopener">Neuquén Informa</a>).',
			'As tarifas dos operadores são as publicadas em 31 de outubro de 2026 e podem mudar.',
		], 'Esta caixa é atualizada uma vez por ano; o resto do guia não muda.' ),
		pm_dz_h2( 'O que este guia ainda não cobre' ),
		pm_dz_p( 'Aluguel de carro (4x2 ou 4x4 em estradas de cascalho), cabanas para dois em Junín de los Andes, San Martín de los Andes e Dina Huapi, preços de combustível, supermercado, recomendações de equipamento, os postos de desinfecção e os acessos públicos com nome não estão aqui porque não os verificamos com uma fonte e uma data. Vamos incluí-los quando tivermos, e depois o total da viagem.' ),
		pm_dz_p( '<em>Não pescamos com os operadores nem com as pessoas citadas aqui. Este guia resume informações públicas e nossas próprias páginas; a interpretação é nossa e pode conter erros.</em>' ),
	],
	'fuentes'         => [
		[ 'Diario 7 Lagos: a temporada começa em 1º de novembro e as licenças já estão à venda (16/10/2026; informa os valores provinciais)', 'https://www.diario7lagos.com.ar/noticias/2026/10/16/80723-la-temporada-de-pesca-comienza-el-1-de-noviembre-y-ya-se-pueden-comprar-permisos', 0, $hoy ],
		[ 'Neuquén Informa: novo escritório na foz do Chimehuín, multas e alertas sobre os rios (28/10/2026)', $u['nqi'], 1, $hoy ],
		[ 'Permisos de Pesca: Mesa Directiva de Pesca Deportiva Zona Andina (Río Negro)', $u['rn'], 1, '20261030' ],
		[ 'Reglamento 2026/27: Anexo Neuquén', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-neuquen/', 1, '20261030' ],
		[ 'Reglamento 2026/27: Anexo Río Negro', 'https://reglamentodepesca.org.ar/reglamento-items/anexo-rio-negro/', 1, '20261030' ],
		[ 'Reglamento 2026/27: águas dos Parques Nacionais', 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/', 1, '20261030' ],
		[ 'AICACYP: Reglamento 2026/27, águas dos Parques Nacionais (cópia, PDF)', 'https://www.aicacyp.ar/wp-content/uploads/2026/10/Reglamento-PDCP-2026-27-Ambientes-Parques-Nacionales.pdf', 0, '20261030' ],
		[ 'Código Civil e Comercial (Lei 26.994), arts. 235 e 1974', 'https://www.argentina.gob.ar/normativa/nacional/ley-26994-235975/actualizacion', 1, $hoy ],
		[ 'Turismo Neuquén: lista oficial de prestadores de pesca', $u['tur'], 1, $hoy ],
		[ 'Vialidad Rionegrina: balsas (Villa Llanquín)', $u['balsa'], 1, $hoy ],
		[ 'Trout Bariloche: passeios de um dia e tarifas', 'https://troutbariloche.com/day-trips/', 0, $hoy ],
		[ 'Southern Rivers Patagonia: dia completo no Limay', 'https://southernriverspatagonia.com/limay-full-day-fly-fishing', 0, $hoy ],
		[ 'Patagonia in the Loop: tarifas', 'https://www.patagoniaintheloop.com/', 0, $hoy ],
		[ 'Patagonia Drift Fly Fishing: passeios de um dia', 'https://www.patagoniadrift.com.ar/en/fly-fishing-day-trips/', 0, $hoy ],
	],
];

// ------------------------------------------------------------------ Guards and creation
$en = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === 'en' ) );
if ( 1 !== count( $en ) ) {
	WP_CLI::error( 'tiene que existir exactamente una nota patagonia-por-tu-cuenta (en).' );
}
$ya_foto = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => 'junin-pescador-volcan.jpg', 'numberposts' => 1, 'fields' => 'ids' ] );
$att     = $ya_foto ? (int) $ya_foto[0] : 0;
$att || WP_CLI::error( 'la foto junin-pescador-volcan.jpg no está cargada.' );

$existe = fn( string $i ) => array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i ) );
$origen = 'Versión en español de la nota publicada primero en inglés el 31/10/2026 (idea 1 de docs/ideas-contenido-seo-2026-10-31.md). Mismo contenido y mismos límites: solo lo leído por nosotros; sin presupuesto total ni precios de auto, cabañas, combustible o supermercado, sin accesos con nombre, sin el artículo 22 del reglamento general (no se leyó). Una sola cifra para el día con guía (alrededor de USD 550 por bote). Pedido del dueño: hoy, en español y portugués.';

foreach ( [ 'es', 'pt' ] as $i ) {
	$n = $notas[ $i ];
	if ( $existe( $i ) ) {
		WP_CLI::log( "$clave ($i) ya existe: no toco nada." );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	if ( $tl > 60 || $dl < 70 || $dl > 160 ) {
		WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	}
	$enlaces = [
		'{bariloche}' => pm_dz_destino( 'bariloche', 'es' === $i ? 'nuestra guía de Bariloche' : 'nosso guia de Bariloche', $i ),
		'{junin}'     => pm_dz_destino( 'san-martin-junin', 'San Martín y Junín de los Andes', $i ),
		'{trucha}'    => pm_dz_nota( 'cuando-pescar-trucha-patagonia', 'es' === $i ? 'nuestra nota sobre cuándo pescar trucha en la Patagonia' : 'nossa nota sobre quando pescar truta na Patagônia', $i ),
		'{viaje}'     => esc_url( pm_url_viaje( $i ) . '?destino=bariloche&especie=trucha' ),
		'{paquetes}'  => esc_url( pm_url_paquetes( 'bariloche', 'es' ) ),
	];
	$cuerpo = strtr( implode( "\n\n", $n['cuerpo'] ), $enlaces );
	if ( preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) ) {
		WP_CLI::error( "$i: queda un marcador sin resolver {$m[0]}" );
	}
	$fuentes = array_map( fn( $s ) => [ 'nombre' => $s[0], 'url' => $s[1], 'oficial' => $s[2], 'consultada' => $s[3] ], $n['fuentes'] );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) «{$n['titulo']}» /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . " fuentes; title $tl / description $dl car." );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = [
		'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy,
		'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ),
		'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'],
	];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'guia', 'nota_fecha' => $hoy, 'nota_especie' => 'trucha', 'nota_origen' => $origen ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		set_post_thumbnail( $id, $att );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'caja-anio' ) && ( 'es' !== $i || ( (int) get_post_thumbnail_id( $id ) === $att && 'trucha' === pm_campo( 'nota_especie', $id ) ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}

if ( $aplicar ) {
	// The English note now has a Spanish source: mark it as translated after the Spanish edit, and check that it reads the shared fields from it.
	update_post_meta( $en[0]->ID, '_pm_traducido', time() + 2 );
	$e = pm_nota( $en[0]->ID );
	WP_CLI::log( ( $e['foto'] === $att && 'guia' === $e['tipo'] && 'trucha' === $e['especie'] ? 'VERIFIED' : 'FALLÓ' ) . ": nota en #{$en[0]->ID} lee de la española: foto #{$e['foto']}, tipo {$e['tipo']}, especie {$e['especie']}, fecha {$e['fecha']}" );
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
