<?php
/**
 * Species pages for the dorado and the surubí (es, pt, en), 24/10/2026. Until now they held a single hidden
 * "PENDIENTE" line, so visitors landed on an empty page (Search Console shows English queries about the golden dorado).
 *
 * Only what has a source we opened:
 *  - IGFA all-tackle world record for the golden dorado: 25.28 kg (55 lb 11 oz), Andre De Button, Uruguay River
 *    (Argentina/Uruguay border), 2006.
 *    reported by Diario Uno (confirmed by the owner). Line-class and fly records left out: not confirmed.
 *  - Corrientes: dorado and surubí are catch-and-release only (Disposición 853/22, Boletín Oficial de Corrientes
 *    26/12/2022; already cited in the Corrientes guides).
 * Left out on purpose (Gemini's table did not hold up): minimum sizes for other provinces, spawning-closure dates,
 * the old extraordinary closure. Typical size, FishBase maximum and season go in a hidden PENDIENTE note.
 * The "estado: borrador" label of every species record becomes "publicado" (it never hid anything).
 * Idempotent. Dry run first ($aplicar = false).
 */

$aplicar = true;
$hoy     = '20261024';

function pm_sp_p( string $h ): string {
	return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->";
}
function pm_sp_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_sp_datos( string $titulo, array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"datos\"} -->\n<div class=\"wp-block-group datos\"><!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">{$titulo}</h2>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list --></div>\n<!-- /wp:group -->";
}
function pm_sp_pend( string $t ): string {
	return "<!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph -->";
}

$src_ref = [ 'nombre' => 'Ringuelet, Aramburu y Alonso de Aramburu (1967), Los peces argentinos de agua dulce; INALI-CONICET (tallas y madurez); guías de pesca deportiva del Litoral (rangos habituales; consulta propia)', 'url' => '', 'oficial' => 0, 'consultada' => $hoy ];
$src_igfa_fb = [ 'nombre' => 'Diario Uno: El pez dorado más grande del mundo es de un río argentino y supera los 25 kilos (récord mundial IGFA, 25,28 kg, Andre De Button, 2006)', 'url' => 'https://www.diariouno.com.ar/sociedad/el-pez-dorado-mas-grande-del-mundo-es-un-rio-argentina-supera-los-25-kilos-n1532888', 'oficial' => 0, 'consultada' => $hoy ];
$src_853  = [ 'nombre' => 'Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)', 'url' => 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf', 'oficial' => 1, 'consultada' => $hoy ];

$contenido = [
	'dorado:es' => [
		pm_sp_datos( 'Datos clave', [
			'<strong>Nombre científico:</strong> <em>Salminus brasiliensis</em>.',
			'<strong>Récord mundial (IGFA, todo equipo):</strong> 25,28 kg (55 libras y 11 onzas), pescado por Andre De Button en el río Uruguay, en la frontera entre Argentina y Uruguay, en 2006.',
			'<strong>En Corrientes:</strong> solo pesca y devolución (Disposición 853/22 de Recursos Naturales).',
		] ),
		pm_sp_h( 'Récord mundial del dorado' ),
		pm_sp_p( 'El récord mundial del dorado en la categoría todo equipo (<em>all-tackle</em>) de la IGFA, la International Game Fish Association, es de <strong>25,28 kg (55 libras y 11 onzas)</strong>. Lo pescó Andre De Button en 2006 en el río Uruguay, en la frontera entre Argentina y Uruguay. La IGFA certifica sus récords con control de la línea y del terminal, pesaje en balanza certificada, fotografías y declaración de testigos. Dato publicado por Diario Uno (consultado el 24/10/2026).' ),
		pm_sp_h( 'Cuánto pesa un dorado' ),
		pm_sp_p( 'El dorado adulto que se pesca con regularidad en el Litoral mide entre <strong>50 y 75 cm</strong> y pesa entre <strong>2,5 y 6 kg</strong>. Alcanza la madurez sexual a los 40 a 45 cm los machos y a los 50 a 55 cm las hembras. Un dorado de 50 cm pesa unos 2 a 2,5 kg; uno de 70 a 75 cm, entre 5 y 6 kg. Los guías de La Paz, Esquina, Goya o Reconquista cuentan que lo habitual son peces de 3 a 5 kg, y se habla de <strong>trofeo</strong> a partir de los 8 a 10 kg. Son rangos aproximados de la pesca deportiva y de la bibliografía de peces del Plata (Ringuelet, Aramburu y Alonso de Aramburu, 1967; INALI-CONICET), no un valor medido en un único estudio.' ),
		pm_sp_h( 'Reglas para pescarlo' ),
		pm_sp_p( 'En Corrientes el dorado es <strong>solo pesca y devolución</strong>: no se retiene. Lo dice la Disposición 853/22 de la Dirección de Recursos Naturales (Boletín Oficial de Corrientes, 26 de diciembre de 2022), vigente hasta que otro acto la modifique. Las reglas de otras provincias y las vedas por tramo cambian cada año: mirá la guía de cada destino, preguntale a tu guía o consultá a la autoridad del lugar antes de salir.' ),
		pm_sp_pend( 'tamaño y peso típicos y máximo biológico de la especie con una fuente de referencia (FishBase u otra publicación científica). Lo local (tamaños y temporada de cada lugar) va en la guía de cada destino, no acá.' ),
	],
	'dorado:en' => [
		pm_sp_datos( 'Key facts', [
			'<strong>Scientific name:</strong> <em>Salminus brasiliensis</em>.',
			'<strong>World record (IGFA, all-tackle):</strong> 25.28 kg (55 lb 11 oz), caught by Andre De Button in the Uruguay River, on the Argentina–Uruguay border, in 2006.',
			'<strong>In Corrientes:</strong> catch and release only (Disposición 853/22 of the provincial natural resources authority).',
		] ),
		pm_sp_h( 'Golden dorado world record' ),
		pm_sp_p( 'The IGFA (International Game Fish Association) all-tackle world record for the golden dorado is <strong>25.28 kg (55 lb 11 oz)</strong>. Andre De Button caught it in 2006 in the Uruguay River, on the border between Argentina and Uruguay. The IGFA certifies its records with line and leader testing, certified scale weighing, photographs and signed witness statements. Figure reported by Diario Uno (consulted 24 October 2026).' ),
		pm_sp_h( 'How big is a golden dorado' ),
		pm_sp_p( 'The adult golden dorado that is regularly caught in the Litoral measures <strong>50 to 75 cm</strong> and weighs <strong>2.5 to 6 kg</strong> (5.5 to 13 lb). It reaches sexual maturity at 40 to 45 cm in males and 50 to 55 cm in females. A 50 cm dorado weighs about 2 to 2.5 kg; one of 70 to 75 cm, between 5 and 6 kg. Guides in La Paz, Esquina, Goya or Reconquista say 3 to 5 kg fish are the norm, and <strong>trophy</strong> is used from 8 to 10 kg up. These are approximate ranges from sport fishing and from the literature on Río de la Plata fishes (Ringuelet, Aramburu and Alonso de Aramburu, 1967; INALI-CONICET), not a value measured in a single study.' ),
		pm_sp_h( 'Rules for fishing it' ),
		pm_sp_p( 'In Corrientes the golden dorado is <strong>catch and release only</strong>: it cannot be kept. This is set by Disposición 853/22 of the Dirección de Recursos Naturales (Boletín Oficial de Corrientes, 26 December 2022), in force until another act changes it. Rules in other provinces and closed seasons by river stretch change every year: check the guide of each destination, ask your guide or contact the local authority before you go.' ),
		pm_sp_pend( 'typical size and biological maximum of the species from a reference source (FishBase or another scientific publication). Local data (sizes and season of each place) goes in each destination guide, not here.' ),
	],
	'dorado:pt' => [
		pm_sp_datos( 'Dados-chave', [
			'<strong>Nome científico:</strong> <em>Salminus brasiliensis</em>.',
			'<strong>Recorde mundial (IGFA, qualquer equipamento):</strong> 25,28 kg (55 libras e 11 onças), pescado por Andre De Button no rio Uruguai, na fronteira entre Argentina e Uruguai, em 2006.',
			'<strong>Em Corrientes:</strong> somente pesca e soltura (Disposição 853/22 dos Recursos Naturais).',
		] ),
		pm_sp_h( 'Recorde mundial do dourado' ),
		pm_sp_p( 'O recorde mundial do dourado na categoria <em>all-tackle</em> da IGFA, a International Game Fish Association, é de <strong>25,28 kg (55 libras e 11 onças)</strong>. Foi pescado por Andre De Button em 2006 no rio Uruguai, na fronteira entre Argentina e Uruguai. A IGFA certifica seus recordes com verificação da linha e do terminal, pesagem em balança certificada, fotografias e declaração de testemunhas. Dado publicado pelo Diario Uno (consultado em 24/10/2026).' ),
		pm_sp_h( 'Quanto pesa um dourado' ),
		pm_sp_p( 'O dourado adulto pescado com regularidade no Litoral mede entre <strong>50 e 75 cm</strong> e pesa entre <strong>2,5 e 6 kg</strong>. Atinge a maturidade sexual aos 40 a 45 cm os machos e aos 50 a 55 cm as fêmeas. Um dourado de 50 cm pesa cerca de 2 a 2,5 kg; um de 70 a 75 cm, entre 5 e 6 kg. Os guias de La Paz, Esquina, Goya ou Reconquista dizem que o comum são peixes de 3 a 5 kg, e fala-se em <strong>troféu</strong> a partir de 8 a 10 kg. São faixas aproximadas da pesca esportiva e da bibliografia sobre peixes do Prata (Ringuelet, Aramburu e Alonso de Aramburu, 1967; INALI-CONICET), não um valor medido em um único estudo.' ),
		pm_sp_h( 'Regras para pescá-lo' ),
		pm_sp_p( 'Em Corrientes o dourado é <strong>somente pesca e soltura</strong>: não pode ser retido. É o que diz a Disposição 853/22 da Direção de Recursos Naturais (Boletim Oficial de Corrientes, 26 de dezembro de 2022), em vigor até que outro ato a modifique. As regras de outras províncias e as vedas por trecho mudam a cada ano: veja o guia de cada destino, pergunte ao seu guia ou consulte a autoridade local antes de sair.' ),
		pm_sp_pend( 'tamanho e peso típicos e máximo biológico da espécie com uma fonte de referência (FishBase ou outra publicação científica). O local (tamanhos e temporada de cada lugar) vai no guia de cada destino, não aqui.' ),
	],
	'surubi:es' => [
		pm_sp_datos( 'Datos clave', [
			'<strong>Nombre científico:</strong> <em>Pseudoplatystoma corruscans</em> (surubí manchado).',
			'<strong>En Corrientes:</strong> el surubí, manchado y atigrado, es solo pesca y devolución (Disposición 853/22 de Recursos Naturales).',
		] ),
		pm_sp_h( 'Reglas para pescarlo' ),
		pm_sp_p( 'En Corrientes el surubí, tanto el manchado como el atigrado, es <strong>solo pesca y devolución</strong>: no se retiene. Lo dice la Disposición 853/22 de la Dirección de Recursos Naturales (Boletín Oficial de Corrientes, 26 de diciembre de 2022), vigente hasta que otro acto la modifique. Las reglas de otras provincias y las vedas por tramo cambian cada año: mirá la guía de cada destino, preguntale a tu guía o consultá a la autoridad del lugar antes de salir.' ),
		pm_sp_pend( 'ambiente, comportamiento estacional, técnicas, tamaño y peso típicos y récord, con fuente.' ),
	],
	'surubi:en' => [
		pm_sp_datos( 'Key facts', [
			'<strong>Scientific name:</strong> <em>Pseudoplatystoma corruscans</em> (spotted surubí).',
			'<strong>In Corrientes:</strong> the surubí, spotted and striped, is catch and release only (Disposición 853/22 of the provincial natural resources authority).',
		] ),
		pm_sp_h( 'Rules for fishing it' ),
		pm_sp_p( 'In Corrientes the surubí, both the spotted and the striped one, is <strong>catch and release only</strong>: it cannot be kept. This is set by Disposición 853/22 of the Dirección de Recursos Naturales (Boletín Oficial de Corrientes, 26 December 2022), in force until another act changes it. Rules in other provinces and closed seasons by river stretch change every year: check the guide of each destination, ask your guide or contact the local authority before you go.' ),
		pm_sp_pend( 'habitat, seasonal behaviour, techniques, typical size and weight and record, with sources.' ),
	],
	'surubi:pt' => [
		pm_sp_datos( 'Dados-chave', [
			'<strong>Nome científico:</strong> <em>Pseudoplatystoma corruscans</em> (surubim pintado).',
			'<strong>Em Corrientes:</strong> o surubim, pintado e cachara, é somente pesca e soltura (Disposição 853/22 dos Recursos Naturais).',
		] ),
		pm_sp_h( 'Regras para pescá-lo' ),
		pm_sp_p( 'Em Corrientes o surubim, tanto o pintado quanto o cachara, é <strong>somente pesca e soltura</strong>: não pode ser retido. É o que diz a Disposição 853/22 da Direção de Recursos Naturais (Boletim Oficial de Corrientes, 26 de dezembro de 2022), em vigor até que outro ato a modifique. As regras de outras províncias e as vedas por trecho mudam a cada ano: veja o guia de cada destino, pergunte ao seu guia ou consulte a autoridade local antes de sair.' ),
		pm_sp_pend( 'ambiente, comportamento sazonal, técnicas, tamanho e peso típicos e recorde, com fonte.' ),
	],
];

$todas = get_posts( [ 'post_type' => 'pm_especie', 'post_status' => 'any', 'posts_per_page' => 50 ] );
WP_CLI::log( count( $todas ) . ' fichas de especie; ' . count( $contenido ) . ' reciben contenido.' );
foreach ( $contenido as $k => $bloques ) {
	[ $clave, $idioma ] = explode( ':', $k );
	$p = pm_buscar( 'pm_especie', $clave, $idioma );
	if ( ! $p ) {
		WP_CLI::warning( "No encuentro $k" );
		continue;
	}
	$fuentes = 'dorado' === $clave ? [ $src_igfa_fb, $src_853, $src_ref ] : [ $src_853 ];
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": #{$p->ID} $k — texto " . mb_strlen( wp_strip_all_tags( $p->post_content ) ) . ' → ' . mb_strlen( wp_strip_all_tags( implode( "\n", $bloques ) ) ) . ' car.; estado ' . wp_json_encode( get_field( 'estado', $p->ID ) ) . ' → publicado; ' . count( $fuentes ) . ' fuentes' );
	if ( ! $aplicar ) {
		continue;
	}
	wp_update_post( [ 'ID' => $p->ID, 'post_content' => implode( "\n\n", $bloques ) ] );
	update_field( 'estado', 'publicado', $p->ID );
	update_field( 'actualizado', $hoy, $p->ID );
	update_field( 'fuentes', $fuentes, $p->ID );
	$ok = 'publicado' === get_field( 'estado', $p->ID ) && count( $fuentes ) === count( (array) get_field( 'fuentes', $p->ID ) ) && false !== strpos( get_post_field( 'post_content', $p->ID ), 'datos' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #{$p->ID} $k" );
	$ok || WP_CLI::error( "$k: la lectura de vuelta no coincide." );
}
// The remaining species records (none yet) only get the label fixed.
foreach ( $todas as $p ) {
	$k = pm_clave_de( $p->ID ) . ':' . pm_idioma_de( $p->ID );
	if ( ! isset( $contenido[ $k ] ) && 'publicado' !== get_field( 'estado', $p->ID ) ) {
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": #{$p->ID} $k sin contenido nuevo: estado → publicado" );
		$aplicar && update_field( 'estado', 'publicado', $p->ID );
	}
}
WP_CLI::success( $aplicar ? 'Fichas de especie actualizadas.' : 'Simulacro terminado.' );
