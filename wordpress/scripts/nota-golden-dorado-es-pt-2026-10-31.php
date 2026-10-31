<?php
/**
 * Spanish and Portuguese versions of the English pillar "Golden dorado fishing in Argentina" (clave golden-dorado-guia), 31/10/2026.
 * Owner's rule (31/10/2026): what exists in one language and is missing in another gets completed. Same content, same six sources,
 * same limits as the English note of 29/10 (no methods by stretch, no prices). The Spanish note becomes the source of the shared
 * fields (type, date, species, cover photo): the English note's cover photo is copied to it so the English page keeps it.
 * Idempotent: a language that already has the note is not touched. Dry run first ($aplicar = false);
 * run with wp-script.yml (script = nota-golden-dorado-es-pt-2026-10-31).
 */

$aplicar = true;
$hoy     = '20261031';
$clave   = 'golden-dorado-guia';

function pm_gd_p( string $h ): string { return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->"; }
function pm_gd_h2( string $t ): string { return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->"; }
function pm_gd_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_gd_tabla( array $cab, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cab ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
function pm_gd_caja( string $t, array $items, string $pie ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"regla caja-anio\"} -->\n<div class=\"wp-block-group regla caja-anio\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$t}</h3>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n<!-- wp:paragraph {\"className\":\"note\"} -->\n<p class=\"note\">{$pie}</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->";
}
function pm_gd_destino( string $c, string $n, string $i ): string {
	$p = pm_buscar( 'pm_destino', $c, $i ) ?? pm_buscar( 'pm_destino', $c, 'es' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $n ) . '</a>' : esc_html( $n );
}
function pm_gd_nota( string $c, string $t, string $i ): string {
	$p = pm_buscar( 'pm_nota', $c, $i ) ?? pm_buscar( 'pm_nota', $c, 'es' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $t ) . '</a>' : esc_html( $t );
}

$u = [
	'disp' => 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf',
	'tram' => 'https://corrientes.tur.ar/tramites-y-permisos/',
	'acu'  => 'https://corrientes.tur.ar/corrientes-suscribio-el-acuerdo-regional-para-el-reconocimiento-de-licencias-de-pesca-deportiva-con-devolucion-en-el-litoral/',
	'fest' => 'https://pasodelapatria.gob.ar/noticias/fiesta-nacional-del-dorado-un-exito-total',
	'lit'  => 'https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana',
	'perf' => 'https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml',
];

$notas = [];
$notas['es'] = [
	'titulo' => 'Pesca del dorado en la Argentina: dónde ir, cuándo, las reglas y cómo planificar',
	'slug'   => 'pesca-del-dorado-en-la-argentina',
	'bajada' => 'Dónde se pesca el dorado en el Paraná, los meses que repiten los operadores, la regla de devolución en Corrientes, la licencia y cómo armar el viaje. Con fuentes y fechas.',
	'seo_title' => 'Pesca del dorado en la Argentina: guía para planificar',
	'seo_description' => 'Dorado en el Paraná: dónde pescar, mejores meses, regla de devolución en Corrientes, licencia y cómo planificar el viaje, con fuentes.',
	'datos' => [
		'El dorado (Salminus brasiliensis) se pesca en el río Paraná; nuestras guías cubren el Alto Paraná (Paso de la Patria) y el Paraná medio (Goya, Esquina, La Paz).',
		'Los operadores del Alto Paraná en Corrientes repiten octubre–noviembre y marzo–mayo como los mejores meses; algunos ponen el pico antes, de junio a octubre.',
		'En Corrientes el dorado y el surubí son solo pesca y devolución (Disposición 853/22, Boletín Oficial, 26 de diciembre de 2022).',
		'Quien viene del exterior necesita la licencia de pesca deportiva de Corrientes, que se compra online en el portal de Recursos Naturales; no encontramos una norma que reconozca licencias de otros países.',
		'Las vedas extraordinarias cambian cada año: confirmá las vigentes con la autoridad local o con tu guía antes de ir.',
	],
	'cuerpo' => [
		pm_gd_p( '<strong>Respuesta corta.</strong> El dorado es el pez que trae a más pescadores del exterior al río Paraná en Corrientes. Los meses que más repiten los operadores son <strong>octubre–noviembre</strong> y <strong>marzo–mayo</strong> en el Alto Paraná; hace falta la <strong>licencia de pesca deportiva de Corrientes</strong>; y en Corrientes el dorado es <strong>solo pesca y devolución</strong>. Abajo está dónde se pesca, cuándo, qué dice la norma y cómo planificar, con las fuentes y las fechas de cada dato.' ),
		pm_gd_h2( '¿Dónde se pesca el dorado en la Argentina?' ),
		pm_gd_p( 'Nuestras guías cubren dos tramos del Paraná. La elección depende del viaje que quieras.' ),
		pm_gd_tabla( [ 'Tramo', 'Lugares', 'Qué leer después' ], [
			[ '<strong>Alto Paraná</strong>', 'Itatí, Ituzaingó, Paso de la Patria', 'Agua clara y, según un documental de Flylords que citamos en nuestra nota de temporada, el mejor potencial de peces grandes. Paso de la Patria es la sede de la Fiesta Nacional del Dorado: mirá nuestra guía de {paso}.' ],
			[ '<strong>Paraná medio</strong>', 'Goya, Esquina, La Paz', 'Más riachos e islas. No encontramos una fuente con meses para estos tramos, así que preguntale a un guía local: mirá las guías de {goya} y {esquina}.' ],
		] ),
		pm_gd_p( 'El dorado también se pesca fuera de Corrientes, pero cada provincia tiene sus reglas y solo publicamos lo que verificamos. Para el Paraná en Corrientes tenemos las reglas de abajo.' ),
		pm_gd_h2( '¿Cuándo es la mejor época?' ),
		pm_gd_p( 'Las ventanas cambian según el operador y el tramo, así que no damos una sola respuesta. En el Alto Paraná correntino las fuentes que revisamos repiten <strong>octubre a noviembre</strong> y <strong>fines de marzo a mayo</strong>; algunos operadores ponen el pico antes, de junio a octubre, siguiendo a la carnada. El calor de enero y febrero frena la pesca de día. Fuera de la ventana se pesca igual, con otra estrategia.' ),
		pm_gd_p( 'Todas las fuentes, la tabla y las salvedades están en {estacion}.' ),
		pm_gd_h2( '¿Qué reglas hay en Corrientes?' ),
		pm_gd_lista( [
			'<strong>Dorado y surubí: solo pesca y devolución.</strong> No se pueden retener. Fuente: <a href="' . $u['disp'] . '" rel="noopener">Disposición 853/22</a> de Recursos Naturales (Boletín Oficial de Corrientes, 26 de diciembre de 2022; vigente hasta que otro acto la reemplace).',
			'Otras especies tienen talla mínima y cupo (pacú, patí, boga, tararira). Están en nuestra tabla: {tallas}.',
			'Pueden disponerse vedas extraordinarias por tramo del río, y cambian cada año (mirá el recuadro de abajo).',
		] ),
		pm_gd_caja( 'Actualizado · vedas y fiestas, 29 de octubre de 2026', [
			'Desde el 9 de septiembre de 2026 rige una veda extraordinaria en un tramo del Paraná entre los km 1109 y 1150, dispuesta por la Dirección de Recursos Naturales de Corrientes para proteger una concentración excepcional de dorados. La prensa no da fecha de fin (<a href="' . $u['lit'] . '" rel="noopener">El Litoral</a>, <a href="' . $u['perf'] . '" rel="noopener">Perfil</a>).',
			'Fiesta Nacional del Dorado, Paso de la Patria: en 2026 fue la 61.ª edición, del 14 al 17 de agosto, con devolución (<a href="' . $u['fest'] . '" rel="noopener">Municipalidad</a>). El Torneo Apertura de la temporada fue del 2 al 5 de abril de 2026. Las fechas de las próximas ediciones no se publicaron.',
			'No hay un calendario oficial consolidado de vedas por provincia. Un guía local siempre sabe qué tramos están abiertos.',
		], 'Este recuadro se actualiza una vez por año; el resto de la guía no cambia.' ),
		pm_gd_h2( '¿Qué licencia hace falta?' ),
		pm_gd_p( 'La <strong>licencia de pesca deportiva de Corrientes</strong>, que se compra online en el portal de Recursos Naturales y se lleva con el documento (<a href="' . $u['tram'] . '" rel="noopener">Ministerio de Turismo de Corrientes: trámites y permisos</a>). El acuerdo regional de reconocimiento (Santa Fe, Entre Ríos y Chaco) es para residentes de esas provincias y no cubre los tramos de jurisdicción exclusivamente correntina (<a href="' . $u['acu'] . '" rel="noopener">alcance del acuerdo</a>). No encontramos una norma que reconozca licencias de otros países: quien viene de afuera saca la de Corrientes.' ),
		pm_gd_h2( '¿Cómo se planifica el viaje?' ),
		pm_gd_lista( [
			'<strong>Elegí el tramo y el mes</strong> con las dos secciones de arriba; pedile al guía el calendario y con cuánta anticipación conviene reservar.',
			'<strong>Las semanas de fiesta se llenan.</strong> Alrededor de la Fiesta Nacional del Dorado, en agosto, el alojamiento en Paso de la Patria tiene mucha demanda: reservá temprano.',
			'<strong>Revisá las vedas</strong> de tus fechas con tu guía o con Recursos Naturales.',
			'<strong>Llevá la licencia y el documento</strong> a bordo.',
		] ),
		pm_gd_p( '<strong>¿Querés que te ayudemos a armarlo?</strong> Contanos tus fechas, cuántos van y qué buscás en <a href="{viaje}">el formulario del viaje</a> y te escribimos con opciones de guías y alojamiento, sin costo ni compromiso.' ),
		pm_gd_h2( 'Lo que esta guía todavía no cubre' ),
		pm_gd_p( 'Los métodos por tramo (mosca, señuelos, carnada), las tarifas por día publicadas y el Delta cerca de Buenos Aires no están acá porque no los verificamos con una fuente y una fecha. Los sumamos cuando lo hagamos.' ),
		pm_gd_p( '<em>No pescamos con los operadores ni con las personas citadas en las notas enlazadas. Esta guía resume información pública y nuestras propias páginas; la interpretación es nuestra y puede tener errores.</em>' ),
	],
	'fuentes' => [
		[ 'Disposición 853/22, Recursos Naturales de Corrientes (Boletín Oficial, 26/12/2022)', $u['disp'], 1 ],
		[ 'Ministerio de Turismo de Corrientes: trámites y permisos (licencia de pesca)', $u['tram'], 1 ],
		[ 'Ministerio de Turismo de Corrientes: acuerdo regional de licencias (alcance)', $u['acu'], 1 ],
		[ 'Municipalidad de Paso de la Patria: 61.ª Fiesta Nacional del Dorado', $u['fest'], 1 ],
		[ 'El Litoral: veda extraordinaria para proteger un cardumen de dorados (9/9/2026)', $u['lit'], 0 ],
		[ 'Perfil: veda extraordinaria en el Paraná (concentración de dorados)', $u['perf'], 0 ],
	],
	'enlaces' => [ 'paso' => 'Paso de la Patria', 'goya' => 'Goya', 'esquina' => 'Esquina', 'estacion' => 'nuestra nota de temporada “Cuándo pescar el dorado en la Argentina”', 'tallas' => 'tallas, cupos y vedas' ],
];
$notas['pt'] = [
	'titulo' => 'Pesca de dourado na Argentina: onde ir, quando, as regras e como planejar',
	'slug'   => 'pesca-de-dourado-na-argentina',
	'bajada' => 'Onde se pesca o dourado no Paraná, os meses que os operadores repetem, a regra de devolução em Corrientes, a licença e como montar a viagem. Com fontes e datas.',
	'seo_title' => 'Pesca de dourado na Argentina: guia para planejar',
	'seo_description' => 'Dourado no Paraná: onde pescar, melhores meses, regra de devolução em Corrientes, licença e como planejar a viagem, com fontes.',
	'datos' => [
		'O dourado (Salminus brasiliensis) é pescado no rio Paraná; nossos guias cobrem o Alto Paraná (Paso de la Patria) e o Paraná médio (Goya, Esquina, La Paz).',
		'Os operadores do Alto Paraná em Corrientes repetem outubro–novembro e março–maio como os melhores meses; alguns colocam o pico antes, de junho a outubro.',
		'Em Corrientes o dourado e o surubi são só pesca e devolução (Disposición 853/22, Boletim Oficial, 26 de dezembro de 2022).',
		'Quem vem do exterior precisa da licença de pesca esportiva de Corrientes, comprada online no portal de Recursos Naturales; não encontramos norma que reconheça licenças de outros países.',
		'As vedas extraordinárias mudam a cada ano: confirme as vigentes com a autoridade local ou com o seu guia antes de ir.',
	],
	'cuerpo' => [
		pm_gd_p( '<strong>Resposta curta.</strong> O dourado é o peixe que traz mais pescadores do exterior ao rio Paraná em Corrientes. Os meses que os operadores mais repetem são <strong>outubro–novembro</strong> e <strong>março–maio</strong> no Alto Paraná; é preciso a <strong>licença de pesca esportiva de Corrientes</strong>; e em Corrientes o dourado é <strong>só pesca e devolução</strong>. Abaixo está onde se pesca, quando, o que diz a norma e como planejar, com as fontes e as datas de cada dado.' ),
		pm_gd_h2( 'Onde se pesca o dourado na Argentina?' ),
		pm_gd_p( 'Nossos guias cobrem dois trechos do Paraná. A escolha depende da viagem que você quer.' ),
		pm_gd_tabla( [ 'Trecho', 'Lugares', 'O que ler depois' ], [
			[ '<strong>Alto Paraná</strong>', 'Itatí, Ituzaingó, Paso de la Patria', 'Água clara e, segundo um documentário da Flylords que citamos na nossa nota de temporada, o melhor potencial de peixes grandes. Paso de la Patria é a sede da Fiesta Nacional del Dorado: veja nosso guia de {paso}.' ],
			[ '<strong>Paraná médio</strong>', 'Goya, Esquina, La Paz', 'Mais riachos e ilhas. Não encontramos fonte com meses para esses trechos, então pergunte a um guia local: veja os guias de {goya} e {esquina}.' ],
		] ),
		pm_gd_p( 'O dourado também é pescado fora de Corrientes, mas cada província tem suas regras e só publicamos o que verificamos. Para o Paraná em Corrientes temos as regras abaixo.' ),
		pm_gd_h2( 'Quando é a melhor época?' ),
		pm_gd_p( 'As janelas mudam conforme o operador e o trecho, então não damos uma resposta única. No Alto Paraná de Corrientes as fontes que revisamos repetem <strong>outubro a novembro</strong> e <strong>fim de março a maio</strong>; alguns operadores colocam o pico antes, de junho a outubro, seguindo a isca. O calor de janeiro e fevereiro freia a pesca de dia. Fora da janela pesca-se igual, com outra estratégia.' ),
		pm_gd_p( 'Todas as fontes, a tabela e as ressalvas estão em {estacion}.' ),
		pm_gd_h2( 'Quais são as regras em Corrientes?' ),
		pm_gd_lista( [
			'<strong>Dourado e surubi: só pesca e devolução.</strong> Não podem ser retidos. Fonte: <a href="' . $u['disp'] . '" rel="noopener">Disposición 853/22</a> de Recursos Naturales (Boletim Oficial de Corrientes, 26 de dezembro de 2022; vigente até que outro ato a substitua).',
			'Outras espécies têm tamanho mínimo e cota (pacu, patí, boga, traíra). Estão na nossa tabela: {tallas}.',
			'Podem ser decretadas vedas extraordinárias por trecho do rio, e elas mudam a cada ano (veja a caixa abaixo).',
		] ),
		pm_gd_caja( 'Atualizado · vedas e festas, 29 de outubro de 2026', [
			'Desde 9 de setembro de 2026 vigora uma veda extraordinária num trecho do Paraná entre os km 1109 e 1150, decretada pela Dirección de Recursos Naturales de Corrientes para proteger uma concentração excepcional de dourados. A imprensa não dá data de fim (<a href="' . $u['lit'] . '" rel="noopener">El Litoral</a>, <a href="' . $u['perf'] . '" rel="noopener">Perfil</a>).',
			'Fiesta Nacional del Dorado, Paso de la Patria: em 2026 foi a 61.ª edição, de 14 a 17 de agosto, com devolução (<a href="' . $u['fest'] . '" rel="noopener">Prefeitura</a>). O Torneo Apertura da temporada foi de 2 a 5 de abril de 2026. As datas das próximas edições não foram publicadas.',
			'Não há um calendário oficial consolidado de vedas por província. Um guia local sempre sabe quais trechos estão abertos.',
		], 'Esta caixa é atualizada uma vez por ano; o resto do guia não muda.' ),
		pm_gd_h2( 'Qual licença é necessária?' ),
		pm_gd_p( 'A <strong>licença de pesca esportiva de Corrientes</strong>, comprada online no portal de Recursos Naturales e levada junto com o documento (<a href="' . $u['tram'] . '" rel="noopener">Ministério de Turismo de Corrientes: trâmites e permissões</a>). O acordo regional de reconhecimento (Santa Fe, Entre Ríos e Chaco) é para residentes dessas províncias e não cobre os trechos de jurisdição exclusivamente correntina (<a href="' . $u['acu'] . '" rel="noopener">alcance do acordo</a>). Não encontramos norma que reconheça licenças de outros países: quem vem de fora tira a de Corrientes.' ),
		pm_gd_h2( 'Como planejar a viagem?' ),
		pm_gd_lista( [
			'<strong>Escolha o trecho e o mês</strong> com as duas seções acima; peça ao guia o calendário e com quanta antecedência convém reservar.',
			'<strong>As semanas de festa lotam.</strong> Em torno da Fiesta Nacional del Dorado, em agosto, a hospedagem em Paso de la Patria tem muita procura: reserve cedo.',
			'<strong>Confira as vedas</strong> das suas datas com o seu guia ou com Recursos Naturales.',
			'<strong>Leve a licença e o documento</strong> a bordo.',
		] ),
		pm_gd_p( '<strong>Quer que a gente ajude a montar?</strong> Conte suas datas, quantos vão e o que você busca no <a href="{viaje}">formulário da viagem</a> e respondemos com opções de guias e hospedagem, sem custo nem compromisso.' ),
		pm_gd_h2( 'O que este guia ainda não cobre' ),
		pm_gd_p( 'Os métodos por trecho (mosca, iscas artificiais, isca natural), as tarifas diárias publicadas e o Delta perto de Buenos Aires não estão aqui porque não os verificamos com uma fonte e uma data. Vamos incluí-los quando fizermos isso.' ),
		pm_gd_p( '<em>Não pescamos com os operadores nem com as pessoas citadas nas notas vinculadas. Este guia resume informações públicas e nossas próprias páginas; a interpretação é nossa e pode conter erros.</em>' ),
	],
	'fuentes' => [
		[ 'Disposición 853/22, Recursos Naturales de Corrientes (Boletim Oficial, 26/12/2022)', $u['disp'], 1 ],
		[ 'Ministério de Turismo de Corrientes: trâmites e permissões (licença de pesca)', $u['tram'], 1 ],
		[ 'Ministério de Turismo de Corrientes: acordo regional de licenças (alcance)', $u['acu'], 1 ],
		[ 'Prefeitura de Paso de la Patria: 61.ª Fiesta Nacional del Dorado', $u['fest'], 1 ],
		[ 'El Litoral: veda extraordinária para proteger um cardume de dourados (9/9/2026)', $u['lit'], 0 ],
		[ 'Perfil: veda extraordinária no Paraná (concentração de dourados)', $u['perf'], 0 ],
	],
	'enlaces' => [ 'paso' => 'Paso de la Patria', 'goya' => 'Goya', 'esquina' => 'Esquina', 'estacion' => 'nossa nota de temporada “Quando pescar o dourado na Argentina”', 'tallas' => 'tamanhos, cotas e vedas' ],
];

$en = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === 'en' ) );
1 === count( $en ) || WP_CLI::error( 'tiene que existir exactamente una nota golden-dorado-guia (en).' );
$en      = $en[0];
$att     = (int) get_post_thumbnail_id( $en->ID );
$fecha   = (string) pm_campo( 'nota_fecha', $en->ID ) ?: '20261029';
WP_CLI::log( "nota en #{$en->ID}: foto #$att, fecha $fecha" );
$att || WP_CLI::warning( 'la nota en inglés no tiene foto de portada: la española tampoco la tendrá.' );

$existe = fn( string $i ) => array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i );
foreach ( [ 'es', 'pt' ] as $i ) {
	$n = $notas[ $i ];
	if ( $existe( $i ) ) {
		WP_CLI::log( "$clave ($i) ya existe: no toco nada." );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	$e      = $n['enlaces'];
	$cuerpo = strtr( implode( "\n\n", $n['cuerpo'] ), [
		'{paso}' => pm_gd_destino( 'paso-de-la-patria', $e['paso'], $i ), '{goya}' => pm_gd_destino( 'goya', $e['goya'], $i ), '{esquina}' => pm_gd_destino( 'esquina', $e['esquina'], $i ),
		'{estacion}' => pm_gd_nota( 'cuando-pescar-dorado', $e['estacion'], $i ), '{tallas}' => pm_gd_nota( 'tallas-cupos-vedas', $e['tallas'], $i ),
		'{viaje}' => esc_url( pm_url_viaje( $i ) . '?destino=paso-de-la-patria&especie=dorado' ),
	] );
	! preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	$fuentes = array_map( fn( $s ) => [ 'nombre' => $s[0], 'url' => $s[1], 'oficial' => $s[2], 'consultada' => $hoy ], $n['fuentes'] );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) «{$n['titulo']}» /{$n['slug']}/ — " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ' palabras, ' . count( $n['datos'] ) . ' datos, ' . count( $fuentes ) . " fuentes; title $tl / description $dl" );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => $n['titulo'], 'post_name' => $n['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $id ) && WP_CLI::error( $id->get_error_message() );
	$campos = [ 'clave' => $clave, 'idioma' => $i, 'autor' => 'Equipo Pescamapa', 'actualizado' => $hoy, 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ), 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'] ];
	if ( 'es' === $i ) {
		$campos += [ 'nota_tipo' => 'guia', 'nota_fecha' => $fecha, 'nota_especie' => 'dorado', 'nota_origen' => 'Versión en español de la pilar publicada en inglés el 29/10/2026; mismo contenido y mismas seis fuentes; sin métodos por tramo ni precios. Hecha el 31/10/2026 por la regla de completar idiomas.' ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	if ( 'es' === $i ) {
		$att && set_post_thumbnail( $id, $att );
		update_post_meta( $id, '_pm_es_editado', time() );
	} else {
		update_post_meta( $id, '_pm_traducido', time() + 1 );
	}
	$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && count( pm_filas( 'nota_datos', $id ) ) === count( $n['datos'] ) && false !== strpos( get_post_field( 'post_content', $id ), 'caja-anio' ) && ( 'es' !== $i || ( (int) get_post_thumbnail_id( $id ) === $att && 'dorado' === pm_campo( 'nota_especie', $id ) ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #$id " . get_permalink( $id ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}
if ( $aplicar ) {
	update_post_meta( $en->ID, '_pm_traducido', time() + 2 );
	$e = pm_nota( $en->ID );
	WP_CLI::log( ( $e['foto'] === $att && 'dorado' === $e['especie'] && $e['fecha'] ? 'VERIFIED' : 'FALLÓ' ) . ": nota en #{$en->ID} lee de la española: foto #{$e['foto']}, especie {$e['especie']}, fecha {$e['fecha']}" );
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
