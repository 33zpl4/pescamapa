<?php
/**
 * Concordia, 18/11/2026: (1) foto de la guía con crédito «Municipalidad de Concordia»; (2) sección con lo que dice la página «Pesca deportiva» de la
 * Subsecretaría de Turismo de la Municipalidad de Concordia (fuente de gobierno, leída por el dueño el 18/11/2026) con la veda del dorado de la
 * Municipalidad (16/10 al 14/1) y la de la CARU (1/9 al 31/12) mostradas las dos, sin resolver cuál rige, y una FAQ; (3) normas CARU leídas el 18/11/2026
 * (Res. 46/09 texto original, 46/16 texto original, 111/2023 y 37/2023 con leer-vivo, run 44252877973): los avisos «46/09 y 46/16 no las leímos» de la nota de La Zona,
 * la guía de Concordia y la ficha pasan a decir lo que dicen esos textos (art. 5.1 y fechas); (4) la nota del permiso paso a paso: si «Qué llevar y qué guardar» estuviera
 * dos veces con el mismo contenido, se quita la segunda (en es y pt la lectura mostró UNA aparición; en inglés se cuenta aquí).
 * No leído y dicho así: Res. CARU 1/2023 y 63/2026 (texto propio; la CARU publica la 63/26 como PDF), Decreto 3874/25 de Entre Ríos (no se encontró un texto oficial).
 * NO se afirma cómo se aplica la veda dentro de La Zona. Sin nombres de personas, sin teléfonos ni correos, sin enlaces comerciales.
 * La foto viaja en wordpress/fotos/concordia-pescador-atardecer.jpg (wp-script.yml la copia a /tmp/pm-fotos). Origen «cedida»: el dueño la eligió de la página
 * municipal, que NO declara licencia de sus fotos; riesgo conocido (docs/prestadores.md).
 * Cada edición de texto es un reemplazo exacto sobre el contenido publicado: si no está como se espera queda «falta» y no se escribe nada. Idempotente.
 * Simulacro por defecto ($aplicar = false). Corre con wp-script.yml (script = concordia-municipalidad-normas-2026-11-18; aplicar=true para la real).
 */

$aplicar = false;
$hoy     = '20261118';
$carpeta = '/tmp/pm-fotos';
$foto    = [
	'archivo' => 'concordia-pescador-atardecer.jpg', 'autor' => 'Municipalidad de Concordia', 'origen' => 'cedida', 'pagina' => 'https://www.concordia.gob.ar',
	'titulo'  => 'Pescador al atardecer con la caña curvada (Municipalidad de Concordia)',
	'alt'     => [
		'es' => 'Pescador con la caña curvada al atardecer, desde una embarcación sobre aguas calmas',
		'en' => 'Angler with a bent rod at sunset, fishing from a boat on calm water',
		'pt' => 'Pescador com a vara curvada ao entardecer, pescando de um barco em águas calmas',
	],
];

function pm_cm_rep_todo( string $c, string $viejo, string $nuevo ): array {
	if ( false !== strpos( $c, $nuevo ) && false === strpos( $c, $viejo ) ) {
		return [ $c, 'ya' ];
	}
	$n = substr_count( $c, $viejo );
	return $n ? [ str_replace( $viejo, $nuevo, $c ), "cambia($n)" ] : [ $c, 'falta' ];
}
function pm_cm_post( string $tipo, string $clave, string $i ): ?WP_Post {
	$ps = get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	foreach ( $ps as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}
function pm_cm_ficha_id( string $nombre ): int {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 2, 'fields' => 'ids' ] );
	return 1 === count( $ids ) ? (int) $ids[0] : 0;
}
function pm_cm_p( string $h ): string {
	return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->";
}
function pm_cm_h( string $t, int $n = 2 ): string {
	return '<!-- wp:heading' . ( 2 === $n ? '' : ' {"level":' . $n . '}' ) . " -->\n<h{$n} class=\"wp-block-heading\">{$t}</h{$n}>\n<!-- /wp:heading -->";
}
function pm_cm_tabla( array $cab, array $filas ): string {
	$th = implode( '', array_map( fn( $c ) => "<th>{$c}</th>", $cab ) );
	$tr = implode( '', array_map( fn( $f ) => '<tr>' . implode( '', array_map( fn( $c ) => "<td>{$c}</td>", $f ) ) . '</tr>', $filas ) );
	return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tr}</tbody></table></figure>\n<!-- /wp:table -->";
}
/** Quita la segunda aparición de una sección h2 si es idéntica a la primera. [contenido, apariciones, estado]. */
function pm_cm_dedupe( string $c, string $h ): array {
	$pat = '#<!-- wp:heading[^>]*-->\s*<h2[^>]*>' . preg_quote( $h, '#' ) . '</h2>\s*<!-- /wp:heading -->#u';
	if ( ! preg_match_all( $pat, $c, $m, PREG_OFFSET_CAPTURE ) ) {
		return [ $c, 0, 'sin la sección' ];
	}
	$n = count( $m[0] );
	if ( $n < 2 ) {
		return [ $c, 1, 'una sola' ];
	}
	$cualquiera = '#<!-- wp:heading[^>]*-->\s*<h2#u';
	$fin        = function ( int $desde ) use ( $c, $cualquiera ) {
		return preg_match( $cualquiera, $c, $mm, PREG_OFFSET_CAPTURE, $desde ) ? $mm[0][1] : strlen( $c );
	};
	$a0 = $m[0][0][1];
	$a1 = $fin( $a0 + strlen( $m[0][0][0] ) );
	$b0 = $m[0][1][1];
	$b1 = $fin( $b0 + strlen( $m[0][1][0] ) );
	$s1 = trim( substr( $c, $a0, $a1 - $a0 ) );
	$s2 = trim( substr( $c, $b0, $b1 - $b0 ) );
	if ( $s1 !== $s2 ) {
		return [ $c, $n, 'DISTINTAS (no se toca)' ];
	}
	return [ substr( $c, 0, $b0 ) . substr( $c, $b1 ), $n, 'quita la segunda' ];
}

// ---- Normas: reemplazos globales por idioma (se aplican a nota, guía y ficha) ----------------------------------------------------------------
$NORM = [
	'es' => [
		[ 'según el texto original de 2004; las Resoluciones CARU 46/09 y 46/16 lo modificaron y no las leímos)',
			'según el texto original de 2004; lo modificaron la Resolución CARU 46/09 (17/12/2009) y la 46/16 (Boletín Oficial del 7/10/2016), que leímos; el texto propio de la 1/2023 y de la 63/2026 no lo leímos)' ],
		[ 'art. 5.1 del texto original de 2004; las Resoluciones CARU 46/09 y 46/16 lo modificaron y no las leímos',
			'art. 5.1, según la Resolución CARU 46/09 del 17/12/2009: dorado, salvo en los concursos de pesca, donde excepcionalmente pueden capturarse otras especies' ],
		[ 'Según el texto original de 2004 (art. 5.1), en la zona reglamentada de la represa solo se autoriza la pesca deportiva del dorado; las Resoluciones CARU 46/09 y 46/16 modificaron el reglamento y no las leímos.',
			'Según el art. 5.1 del reglamento, en la redacción de la Resolución CARU 46/09 (17/12/2009; Boletín Oficial del 19/1/2010), en la zona reglamentada de la represa se autoriza la pesca deportiva del dorado, salvo en los concursos de pesca, donde excepcionalmente pueden capturarse otras especies. La Resolución CARU 46/16 (Boletín Oficial del 7/10/2016) cierra los primeros 500 metros de la zona de seguridad, con la excepción de la navegación propia de la pesca con devolución, y deroga el art. 14.5; las Resoluciones 1/2023 y 63/2026 modifican el ámbito físico y no leímos su texto propio.' ],
		[ '<strong>Las Resoluciones CARU 46/09 y 46/16 también modificaron el reglamento y no las leímos</strong>: antes de ir, mirá el texto vigente de la CARU, que puede modificarlo.',
			'<strong>Las Resoluciones CARU 46/09 (17/12/2009; Boletín Oficial del 19/1/2010) y 46/16 (Boletín Oficial del 7/10/2016) también modificaron el reglamento y las leímos</strong>: la 46/09 volvió a redactar el art. 5.1 (dorado, salvo en los concursos de pesca, donde excepcionalmente pueden capturarse otras especies), habilitó esos concursos, protegió los cuencos para peces (art. 14.5, derogado después por la 46/16) y fijó los días de pesca de viernes a lunes, que la 37/2023 cambió; la 46/16 cerró los primeros 500 m. <strong>Las Resoluciones 1/2023 y 63/2026 modifican el ámbito físico y no leímos su texto propio</strong>: antes de ir, mirá el texto vigente de la CARU, que puede modificarlo.' ],
		[ 'las Resoluciones 46/09 y 46/16 no las leímos, y los días salen de la 37/2023',
			'leímos las Resoluciones 46/09 (17/12/2009) y 46/16 (7/10/2016) pero no el texto propio de la 1/2023 ni de la 63/2026, y los días salen de la 37/2023' ],
		[ 'según el texto original de 2004 (art. 5.1), es la única especie cuya pesca deportiva autoriza la CARU en La Zona.',
			'según el art. 5.1 (Resolución CARU 46/09, del 17/12/2009), es la especie cuya pesca deportiva autoriza la CARU en La Zona, salvo en los concursos de pesca, donde excepcionalmente pueden capturarse otras especies.' ],
	],
	'en' => [
		[ 'under the original 2004 text; CARU Resolutions 46/09 and 46/16 amended it and we did not read them)',
			'under the original 2004 text; it was amended by CARU Resolution 46/09 (17/12/2009) and 46/16 (Boletín Oficial of 7/10/2016), which we read; we did not read the own text of 1/2023 or 63/2026)' ],
		[ 'art. 5.1 of the original 2004 text; CARU Resolutions 46/09 and 46/16 amended it and we did not read them',
			'art. 5.1, as worded by CARU Resolution 46/09 of 17/12/2009: dorado, except in fishing contests, where other species may exceptionally be caught' ],
		[ 'Under the original 2004 text (art. 5.1), only sport fishing for dorado is authorised in the regulated zone of the dam; CARU Resolutions 46/09 and 46/16 amended the regulation and we did not read them.',
			'Under art. 5.1 of the regulation, as worded by CARU Resolution 46/09 (17/12/2009; Boletín Oficial of 19/1/2010), sport fishing for dorado is authorised in the regulated zone of the dam, except in fishing contests, where other species may exceptionally be caught. CARU Resolution 46/16 (Boletín Oficial of 7/10/2016) closes the first 500 metres of the safety zone, except for the navigation that catch-and-release fishing needs, and repeals art. 14.5; Resolutions 1/2023 and 63/2026 amend the physical scope and we did not read their own text.' ],
		[ '<strong>CARU Resolutions 46/09 and 46/16 also amended the regulation and we did not read them</strong>: check CARU\'s current text before you go, as it may change it.',
			'<strong>CARU Resolutions 46/09 (17/12/2009; Boletín Oficial of 19/1/2010) and 46/16 (Boletín Oficial of 7/10/2016) also amended the regulation and we read them</strong>: 46/09 reworded art. 5.1 (dorado, except in fishing contests, where other species may exceptionally be caught), enabled those contests, protected the fish-passage basins (art. 14.5, later repealed by 46/16) and set the fishing days to Friday to Monday, which 37/2023 changed; 46/16 closed the first 500 m. <strong>Resolutions 1/2023 and 63/2026 amend the physical scope and we did not read their own text</strong>: check CARU\'s current text before you go, as it may change it.' ],
		[ 'we did not read Resolutions 46/09 and 46/16, and the days come from 37/2023',
			'we read Resolutions 46/09 (17/12/2009) and 46/16 (7/10/2016) but not the own text of 1/2023 or 63/2026, and the days come from 37/2023' ],
		[ 'under the original 2004 text (art. 5.1), it is the only species whose sport fishing CARU authorises in La Zona.',
			'under art. 5.1 (CARU Resolution 46/09, of 17/12/2009), it is the species whose sport fishing CARU authorises in La Zona, except in fishing contests, where other species may exceptionally be caught.' ],
	],
	'pt' => [
		[ 'segundo o texto original de 2004; as Resoluções CARU 46/09 e 46/16 o modificaram e não as lemos)',
			'segundo o texto original de 2004; o modificaram a Resolução CARU 46/09 (17/12/2009) e a 46/16 (Boletim Oficial de 7/10/2016), que lemos; não lemos o texto próprio da 1/2023 nem da 63/2026)' ],
		[ 'art. 5.1 do texto original de 2004; as Resoluções CARU 46/09 e 46/16 o modificaram e não as lemos',
			'art. 5.1, conforme a Resolução CARU 46/09 de 17/12/2009: dourado, salvo nos concursos de pesca, em que excepcionalmente podem ser capturadas outras espécies' ],
		[ 'Segundo o texto original de 2004 (art. 5.1), na zona regulamentada da represa só se autoriza a pesca esportiva do dourado; as Resoluções CARU 46/09 e 46/16 modificaram o regulamento e não as lemos.',
			'Segundo o art. 5.1 do regulamento, na redação da Resolução CARU 46/09 (17/12/2009; Boletim Oficial de 19/1/2010), na zona regulamentada da represa se autoriza a pesca esportiva do dourado, salvo nos concursos de pesca, em que excepcionalmente podem ser capturadas outras espécies. A Resolução CARU 46/16 (Boletim Oficial de 7/10/2016) fecha os primeiros 500 metros da zona de segurança, com a exceção da navegação própria da pesca com devolução, e revoga o art. 14.5; as Resoluções 1/2023 e 63/2026 modificam o âmbito físico e não lemos o texto próprio delas.' ],
		[ '<strong>As Resoluções CARU 46/09 e 46/16 também modificaram o regulamento e não as lemos</strong>: antes de ir, veja o texto vigente da CARU, que pode modificá-lo.',
			'<strong>As Resoluções CARU 46/09 (17/12/2009; Boletim Oficial de 19/1/2010) e 46/16 (Boletim Oficial de 7/10/2016) também modificaram o regulamento e as lemos</strong>: a 46/09 redigiu de novo o art. 5.1 (dourado, salvo nos concursos de pesca, em que excepcionalmente podem ser capturadas outras espécies), habilitou esses concursos, protegeu as bacias de passagem de peixes (art. 14.5, revogado depois pela 46/16) e fixou os dias de pesca de sexta a segunda, que a 37/2023 mudou; a 46/16 fechou os primeiros 500 m. <strong>As Resoluções 1/2023 e 63/2026 modificam o âmbito físico e não lemos o texto próprio delas</strong>: antes de ir, veja o texto vigente da CARU, que pode modificá-lo.' ],
		[ 'as Resoluções 46/09 e 46/16 não as lemos, e os dias saem da 37/2023',
			'lemos as Resoluções 46/09 (17/12/2009) e 46/16 (7/10/2016), mas não o texto próprio da 1/2023 nem da 63/2026, e os dias saem da 37/2023' ],
		[ 'segundo o texto original de 2004 (art. 5.1), é a única espécie cuja pesca esportiva a CARU autoriza em La Zona.',
			'conforme o art. 5.1 (Resolução CARU 46/09, de 17/12/2009), é a espécie cuja pesca esportiva a CARU autoriza em La Zona, salvo nos concursos de pesca, em que excepcionalmente podem ser capturadas outras espécies.' ],
	],
];

// ---- Sección municipal (guía de Concordia) -------------------------------------------------------------------------------------------------------
$SEC = [];
$SEC['es'] = [
	'ancla' => 'Cómo llegar',
	'bloques' => [
		[ 'h2', 'Lo que dice la Municipalidad de Concordia' ],
		[ 'p', 'La página «Pesca deportiva» de la Subsecretaría de Turismo de la Municipalidad de Concordia, que consultamos el 18/11/2026, describe la pesca en el río Uruguay en general, no solo en La Zona. Esto es lo que dice esa página, resumido y atribuido: no lo verificamos por nuestra cuenta.' ],
		[ 'table', [ 'Tema', 'Qué dice la página municipal' ], [
			[ 'Dónde se practica', 'El punto principal, de costa y embarcada, es el Camping de Pesca La Tortuga Alegre, a 14 km del centro, aguas abajo de la represa, sobre el río Uruguay y el arroyo Ayuí Grande' ],
			[ 'Competencias anuales', 'Fiesta Nacional de la Pesca de la Boga y Torneo de Pesca del Amarillo' ],
			[ 'Boga', 'Temporada de principios de noviembre a fines de marzo; medida mínima de 45 cm; máximo de 3 piezas por pescador; se pesca fondeado en correderas, con maíz, lombriz o masa' ],
			[ 'Dorado', 'Dice que los ejemplares mayores, contra el murallón de la represa, suelen oscilar entre 4 y 25 kg, y menciona la pesca a la deriva con carnada viva (morenas) o con señuelos, con spinning o trolling' ],
			[ 'Tararira', 'En el lago de Salto Grande, de 0,5 a 2,5 kg; con spinning, baitcast y mosca' ],
			[ 'Surubí', 'Tras la aparición de un cardumen en los alrededores de Puerto Yeruá; trolling con señuelos de fondo; habilitada solo con devolución entre el 16 de marzo y el 14 de agosto' ],
			[ 'Otras especies', 'Bagres amarillos, patíes, armados y manduvíes' ],
		] ],
		[ 'p', '<strong>Ojo con las modalidades.</strong> La carnada viva y el trolling son las que cita la página municipal para el río en general. No son las de la zona reservada de la represa (La Zona), donde la CARU admite solo mosca, spinning y baitcasting y prohíbe el trolling y la carnada (arts. 7.1 y 8.1 del texto original de 2004; la Resolución 46/09 reformó otros puntos del reglamento y no verificamos si tocó estos: confirmalo en el texto vigente de la CARU).' ],
		[ 'h3', 'Veda del dorado: dos fechas que no coinciden' ],
		[ 'table', [ 'Fuente', 'Fechas', 'Qué dice' ], [
			[ 'Municipalidad de Concordia, página de turismo (consultada el 18/11/2026)', 'Del 16 de octubre al 14 de enero', 'En ese período la pesca del dorado está permitida únicamente con devolución' ],
			[ 'CARU, aviso del 7/10/2026 y Resolución 111/2023 (Paysandú, 1/8/2023; Boletín Oficial del 17/8/2023)', 'Del 1 de septiembre al 31 de diciembre de cada año', 'Veda para la protección del dorado en sus modalidades comercial y deportiva (art. 1 de la Resolución 111/2023)' ],
		] ],
		[ 'p', '<strong>Las dos fechas no coinciden y no resolvemos cuál rige.</strong> Confirmalas por escrito con la CARU y con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos antes de ir. No afirmamos cómo se aplica la veda dentro de La Zona: ningún texto que leímos lo dice.' ],
	],
	'faq' => [ '¿Cuándo es la veda del dorado en Concordia?', 'Depende de la fuente y las dos fechas no coinciden. La página de turismo de la Municipalidad de Concordia (consultada el 18/11/2026) dice que la veda del dorado va del 16 de octubre al 14 de enero y que en ese período solo se pesca con devolución. La CARU, en su aviso del 7/10/2026 y en la Resolución 111/2023, fija la veda del dorado en el río Uruguay del 1 de septiembre al 31 de diciembre de cada año. No resolvemos cuál rige: confirmalo por escrito con la CARU y con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos antes de ir.' ],
	'fuentes' => [
		[ 'Municipalidad de Concordia, Subsecretaría de Turismo: «Pesca deportiva» (página consultada el 18/11/2026; no declara licencia de sus fotos ni fechas de actualización)', 'https://www.concordia.gob.ar/turismo/atractivos/pesca-deportiva', 1 ],
		[ 'CARU, Resolución 46/2009 (Paysandú, 17/12/2009; Boletín Oficial del 19/1/2010): modificación del reglamento de pesca (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2009-163143/texto', 1 ],
		[ 'CARU, Resolución 46/2016 (Boletín Oficial del 7/10/2016): primeros 500 m de la zona de seguridad (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2016-266292/texto', 1 ],
	],
	'fuentes_nota' => [
		[ 'CARU, Resolución 46/2009 (Paysandú, 17/12/2009; Boletín Oficial del 19/1/2010): modificación del reglamento de pesca (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2009-163143/texto', 1 ],
		[ 'CARU, Resolución 46/2016 (Boletín Oficial del 7/10/2016): primeros 500 m de la zona de seguridad (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2016-266292/texto', 1 ],
	],
	'ficha_add' => ' Fechas de veda del dorado: la Municipalidad de Concordia publica del 16 de octubre al 14 de enero y la CARU, del 1 de septiembre al 31 de diciembre; no coinciden: confirmalas antes de ir.',
];
$SEC['en'] = [
	'ancla' => 'Getting there',
	'bloques' => [
		[ 'h2', 'What the Municipality of Concordia says' ],
		[ 'p', 'The “Pesca deportiva” (sport fishing) page of the Tourism Undersecretariat of the Municipalidad de Concordia, which we consulted on 18/11/2026, describes fishing on the Uruguay River in general, not only in La Zona. This is what that page says, summarised and attributed: we did not verify it ourselves.' ],
		[ 'table', [ 'Topic', 'What the municipal page says' ], [
			[ 'Where it is done', 'The main spot, from the bank and from boats, is the Camping de Pesca La Tortuga Alegre, 14 km from the town centre, downstream of the dam, on the Uruguay River and the Ayuí Grande stream' ],
			[ 'Annual competitions', 'Fiesta Nacional de la Pesca de la Boga (national boga fishing festival) and Torneo de Pesca del Amarillo (yellow catfish tournament)' ],
			[ 'Boga', 'Season from early November to late March; minimum size 45 cm; maximum 3 fish per angler; fished at anchor in the rapids, with corn, worm or dough' ],
			[ 'Dorado', 'Says the larger fish, against the dam wall, usually range from 4 to 25 kg, and mentions drift fishing with live bait (morenas) or lures, with spinning or trolling' ],
			[ 'Tararira (trahira)', 'In the Salto Grande lake, 0.5 to 2.5 kg; with spinning, baitcast and fly' ],
			[ 'Surubí', 'After a shoal appeared around Puerto Yeruá; trolling with bottom lures; allowed only with release between 16 March and 14 August' ],
			[ 'Other species', 'Yellow catfish, patí, armado and manduví catfish' ],
		] ],
		[ 'p', '<strong>Mind the methods.</strong> Live bait and trolling are the ones the municipal page cites for the river in general. They are not those of the reserved zone of the dam (La Zona), where CARU admits only fly, spinning and baitcasting and bans trolling and bait (arts. 7.1 and 8.1 of the original 2004 text; Resolution 46/09 reworded other points of the regulation and we did not check whether it touched these: confirm it in CARU\'s current text).' ],
		[ 'h3', 'Dorado closed season: two dates that do not match' ],
		[ 'table', [ 'Source', 'Dates', 'What it says' ], [
			[ 'Municipalidad de Concordia, tourism page (consulted 18/11/2026)', '16 October to 14 January', 'In that period dorado fishing is allowed only with release' ],
			[ 'CARU, notice of 7/10/2026 and Resolution 111/2023 (Paysandú, 1/8/2023; Boletín Oficial of 17/8/2023)', '1 September to 31 December every year', 'Closed season to protect the dorado, commercial and sport fishing (art. 1 of Resolution 111/2023)' ],
		] ],
		[ 'p', '<strong>The two dates do not match and we do not settle which one applies.</strong> Confirm them in writing with CARU and with the Entre Ríos Natural Resources and Enforcement Directorate before you go. We do not say how the closed season applies inside La Zona: no text we read says so.' ],
	],
	'faq' => [ 'When is the dorado closed season in Concordia?', 'It depends on the source and the two dates do not match. The tourism page of the Municipalidad de Concordia (consulted 18/11/2026) says the dorado closed season runs from 16 October to 14 January and that in that period you may fish only with release. CARU, in its notice of 7/10/2026 and in Resolution 111/2023, sets the dorado closed season on the Uruguay River from 1 September to 31 December every year. We do not settle which one applies: confirm it in writing with CARU and with the Entre Ríos Natural Resources and Enforcement Directorate before you go.' ],
	'fuentes' => [
		[ 'Municipalidad de Concordia, Tourism Undersecretariat: “Pesca deportiva” (page consulted 18/11/2026; it states no licence for its photos nor update dates)', 'https://www.concordia.gob.ar/turismo/atractivos/pesca-deportiva', 1 ],
		[ 'CARU, Resolution 46/2009 (Paysandú, 17/12/2009; Boletín Oficial of 19/1/2010): amendment of the fishing regulation (original text, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2009-163143/texto', 1 ],
		[ 'CARU, Resolution 46/2016 (Boletín Oficial of 7/10/2016): first 500 m of the safety zone (original text, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2016-266292/texto', 1 ],
	],
	'fuentes_nota' => [
		[ 'CARU, Resolution 46/2009 (Paysandú, 17/12/2009; Boletín Oficial of 19/1/2010): amendment of the fishing regulation (original text, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2009-163143/texto', 1 ],
		[ 'CARU, Resolution 46/2016 (Boletín Oficial of 7/10/2016): first 500 m of the safety zone (original text, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2016-266292/texto', 1 ],
	],
	'ficha_add' => ' Dorado closed-season dates: the Municipalidad de Concordia publishes 16 October to 14 January and CARU 1 September to 31 December; they do not match: confirm them before you go.',
];
$SEC['pt'] = [
	'ancla' => 'Como chegar',
	'bloques' => [
		[ 'h2', 'O que diz a Prefeitura de Concordia' ],
		[ 'p', 'A página «Pesca deportiva» da Subsecretaria de Turismo da Municipalidad de Concordia (a prefeitura), que consultamos em 18/11/2026, descreve a pesca no rio Uruguai em geral, não só em La Zona. Isto é o que diz essa página, resumido e atribuído: não o verificamos por conta própria.' ],
		[ 'table', [ 'Tema', 'O que diz a página municipal' ], [
			[ 'Onde se pratica', 'O ponto principal, de barranco e embarcada, é o Camping de Pesca La Tortuga Alegre, a 14 km do centro, abaixo da represa, no rio Uruguai e no arroio Ayuí Grande' ],
			[ 'Competições anuais', 'Fiesta Nacional de la Pesca de la Boga (festa nacional da pesca da boga) e Torneo de Pesca del Amarillo (torneio do bagre-amarelo)' ],
			[ 'Boga', 'Temporada do início de novembro ao fim de março; medida mínima de 45 cm; máximo de 3 peixes por pescador; pesca-se fundeado nas corredeiras, com milho, minhoca ou massa' ],
			[ 'Dourado', 'Diz que os exemplares maiores, contra o paredão da represa, costumam variar entre 4 e 25 kg, e menciona a pesca à deriva com isca viva (morenas) ou com iscas artificiais, com spinning ou corrico' ],
			[ 'Tararira (traíra)', 'No lago de Salto Grande, de 0,5 a 2,5 kg; com spinning, baitcast e mosca' ],
			[ 'Surubi', 'Após o aparecimento de um cardume nos arredores de Puerto Yeruá; corrico com iscas de fundo; habilitada somente com devolução entre 16 de março e 14 de agosto' ],
			[ 'Outras espécies', 'Bagres-amarelos, patis, armados e manduvís' ],
		] ],
		[ 'p', '<strong>Atenção às modalidades.</strong> A isca viva e o corrico são as que a página municipal cita para o rio em geral. Não são as da zona reservada da represa (La Zona), onde a CARU admite só mosca, spinning e baitcasting e proíbe o corrico e a isca natural (arts. 7.1 e 8.1 do texto original de 2004; a Resolução 46/09 reformou outros pontos do regulamento e não verificamos se mexeu nestes: confirme no texto vigente da CARU).' ],
		[ 'h3', 'Defeso do dourado: duas datas que não coincidem' ],
		[ 'table', [ 'Fonte', 'Datas', 'O que diz' ], [
			[ 'Municipalidad de Concordia, página de turismo (consultada em 18/11/2026)', 'De 16 de outubro a 14 de janeiro', 'Nesse período a pesca do dourado é permitida somente com devolução' ],
			[ 'CARU, aviso de 7/10/2026 e Resolução 111/2023 (Paysandú, 1/8/2023; Boletim Oficial de 17/8/2023)', 'De 1.º de setembro a 31 de dezembro de cada ano', 'Defeso para a proteção do dourado, pesca comercial e esportiva (art. 1 da Resolução 111/2023)' ],
		] ],
		[ 'p', '<strong>As duas datas não coincidem e não resolvemos qual vale.</strong> Confirme-as por escrito com a CARU e com a Dirección de Recursos Naturales y Fiscalización de Entre Ríos antes de ir. Não afirmamos como o defeso se aplica dentro de La Zona: nenhum texto que lemos o diz.' ],
	],
	'faq' => [ 'Quando é o defeso do dourado em Concordia?', 'Depende da fonte e as duas datas não coincidem. A página de turismo da Municipalidad de Concordia (consultada em 18/11/2026) diz que o defeso do dourado vai de 16 de outubro a 14 de janeiro e que nesse período só se pesca com devolução. A CARU, no aviso de 7/10/2026 e na Resolução 111/2023, fixa o defeso do dourado no rio Uruguai de 1.º de setembro a 31 de dezembro de cada ano. Não resolvemos qual vale: confirme por escrito com a CARU e com a Dirección de Recursos Naturales y Fiscalización de Entre Ríos antes de ir.' ],
	'fuentes' => [
		[ 'Municipalidad de Concordia, Subsecretaria de Turismo: «Pesca deportiva» (página consultada em 18/11/2026; não declara licença das fotos nem datas de atualização)', 'https://www.concordia.gob.ar/turismo/atractivos/pesca-deportiva', 1 ],
		[ 'CARU, Resolução 46/2009 (Paysandú, 17/12/2009; Boletim Oficial de 19/1/2010): modificação do regulamento de pesca (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2009-163143/texto', 1 ],
		[ 'CARU, Resolução 46/2016 (Boletim Oficial de 7/10/2016): primeiros 500 m da zona de segurança (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2016-266292/texto', 1 ],
	],
	'fuentes_nota' => [
		[ 'CARU, Resolução 46/2009 (Paysandú, 17/12/2009; Boletim Oficial de 19/1/2010): modificação do regulamento de pesca (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2009-163143/texto', 1 ],
		[ 'CARU, Resolução 46/2016 (Boletim Oficial de 7/10/2016): primeiros 500 m da zona de segurança (texto original, Argentina.gob.ar)', 'https://www.argentina.gob.ar/normativa/nacional/resoluci%C3%B3n-46-2016-266292/texto', 1 ],
	],
	'ficha_add' => ' Datas do defeso do dourado: a Municipalidad de Concordia publica de 16 de outubro a 14 de janeiro e a CARU, de 1.º de setembro a 31 de dezembro; não coincidem: confirme-as antes de ir.',
];

$HEAD_PERMISO = [ 'es' => 'Qué llevar y qué guardar', 'en' => 'What to carry and what to keep', 'pt' => 'O que levar e o que guardar' ];
$FICHA_CAMPOS = [ 'es' => 'descripcion', 'en' => 'descripcion_en', 'pt' => 'descripcion_pt' ];

// Lo nuevo no lleva enlaces, cuentas, teléfonos ni nombres de empresas.
foreach ( [ $NORM, $SEC ] as $bloque ) {
	$s = wp_json_encode( array_map( function ( $x ) {
		unset( $x['fuentes'], $x['fuentes_nota'] );
		return $x;
	}, $bloque ), JSON_UNESCAPED_UNICODE );
	if ( preg_match( '/@|instagram\.com|lazonalodge|\+\d{2}[ \d]{6,}|tel[eé]fono|whatsapp|Untamed|https?:\/\//iu', $s, $m ) ) {
		WP_CLI::error( 'texto nuevo con algo que no se publica: ' . $m[0] );
	}
}
WP_CLI::log( 'OK: el texto nuevo no lleva enlaces, cuentas, teléfonos ni nombres de empresas.' );

// ---- Plan -----------------------------------------------------------------------------------------------------------------------------------------
$hay_falta = false;
$plan      = [];
$ficha_id  = pm_cm_ficha_id( 'La Zona Lodge' );
$ficha_id || ( WP_CLI::warning( 'no encuentro la ficha La Zona Lodge' ) || ( $hay_falta = true ) );

foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	foreach ( [ 'nota' => [ 'pm_nota', 'la-zona-salto-grande' ], 'guia' => [ 'pm_destino', 'concordia' ] ] as $que => [ $tipo, $clave ] ) {
		$p = pm_cm_post( $tipo, $clave, $i );
		if ( ! $p ) {
			WP_CLI::warning( "$que ($i): no existe $clave" );
			$hay_falta = true;
			continue;
		}
		$c = $p->post_content;
		foreach ( $NORM[ $i ] as $n => [ $v, $nu ] ) {
			[ $c, $e ] = pm_cm_rep_todo( $c, $v, $nu );
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $que ($i) #{$p->ID} norma " . ( $n + 1 ) . ": $e" );
		}
		if ( preg_match( '/46\/09 (y|and|e) 46\/16|no las leímos|did not read them|não as lemos|did not read Resolutions/u', $c, $m ) ) {
			WP_CLI::warning( "$que ($i): queda un aviso viejo de normas: «{$m[0]}»" );
			$hay_falta = true;
		}
		$x = [ 'que' => $que, 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'faqs' => null, 'fuentes' => 'nota' === $que ? $SEC[ $i ]['fuentes_nota'] : $SEC[ $i ]['fuentes'] ];
		if ( 'guia' === $que ) {
			// Sección municipal antes del encabezado «Cómo llegar» (o su equivalente).
			$bl = [];
			foreach ( $SEC[ $i ]['bloques'] as $b ) {
				$bl[] = 'h2' === $b[0] ? pm_cm_h( $b[1], 2 ) : ( 'h3' === $b[0] ? pm_cm_h( $b[1], 3 ) : ( 'p' === $b[0] ? pm_cm_p( $b[1] ) : pm_cm_tabla( $b[1], $b[2] ) ) );
			}
			$sec = implode( "\n\n", $bl );
			if ( false !== strpos( $c, $SEC[ $i ]['bloques'][0][1] ) ) {
				WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía ($i) sección municipal: ya" );
			} else {
				$ancla = "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$SEC[ $i ]['ancla']}</h2>";
				if ( 1 === substr_count( $c, $ancla ) ) {
					$c = str_replace( $ancla, $sec . "\n\n" . $ancla, $c );
					WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía ($i) sección municipal: agrega antes de «{$SEC[ $i ]['ancla']}»" );
				} else {
					WP_CLI::warning( "guía ($i): no encuentro una sola vez el encabezado «{$SEC[ $i ]['ancla']}»" );
					$hay_falta = true;
				}
			}
			$filas = pm_filas( 'faqs', $p->ID );
			$q     = $SEC[ $i ]['faq'][0];
			$hay   = false;
			foreach ( $filas as $f ) {
				$hay = $hay || ( ( $f['pregunta'] ?? '' ) === $q );
			}
			if ( ! $hay ) {
				$filas[] = [ 'pregunta' => $q, 'respuesta' => $SEC[ $i ]['faq'][1] ];
			}
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": guía ($i) FAQ «{$q}»: " . ( $hay ? 'ya' : 'agrega' ) );
			$x['faqs'] = array_map( fn( $f ) => [ 'pregunta' => (string) ( $f['pregunta'] ?? '' ), 'respuesta' => (string) ( $f['respuesta'] ?? '' ) ], $filas );
		}
		$x['despues'] = $c;
		$plan[]       = $x;
	}
}

// Ficha: avisos de normas + fechas de veda (se agrega al final de la descripción si todavía no está).
$ficha_nuevo = [];
if ( $ficha_id ) {
	foreach ( $FICHA_CAMPOS as $i => $campo ) {
		$v = (string) pm_campo( $campo, $ficha_id );
		foreach ( $NORM[ $i ] as $n => [ $vj, $nu ] ) {
			[ $v, $e ] = pm_cm_rep_todo( $v, $vj, $nu );
			if ( 'falta' !== $e ) {
				WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha #$ficha_id $campo norma " . ( $n + 1 ) . ": $e" );
			}
		}
		if ( preg_match( '/46\/09 (y|and|e) 46\/16|no las leímos|did not read them|não as lemos/u', $v, $m ) ) {
			WP_CLI::warning( "ficha $campo: queda un aviso viejo: «{$m[0]}»" );
			$hay_falta = true;
		}
		if ( false === strpos( $v, 'Municipalidad de Concordia' ) ) {
			$v .= $SEC[ $i ]['ficha_add'];
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha #$ficha_id $campo: agrega las fechas de veda" );
		} else {
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha #$ficha_id $campo fechas de veda: ya" );
		}
		$ficha_nuevo[ $campo ] = $v;
	}
}

// Nota del permiso: ¿«Qué llevar y qué guardar» dos veces?
$permiso = [];
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = pm_cm_post( 'pm_nota', 'permiso-pesca-argentina-paso-a-paso', $i );
	if ( ! $p ) {
		WP_CLI::warning( "nota del permiso ($i): no existe" );
		$hay_falta = true;
		continue;
	}
	[ $c2, $n, $e ] = pm_cm_dedupe( $p->post_content, $HEAD_PERMISO[ $i ] );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": nota del permiso ($i) #{$p->ID} «{$HEAD_PERMISO[ $i ]}»: $n aparición(es) en el contenido, $e" );
	if ( false !== strpos( $e, 'DISTINTAS' ) || 'sin la sección' === $e ) {
		$hay_falta = true;
	}
	$permiso[ $i ] = [ 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c2 ];
}

// Foto.
$es_guia = pm_cm_post( 'pm_destino', 'concordia', 'es' );
$ya      = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $foto['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
$att     = $ya ? (int) $ya[0] : 0;
$ruta    = "$carpeta/{$foto['archivo']}";
$antes_f = $es_guia ? (int) get_post_thumbnail_id( $es_guia->ID ) : 0;
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': foto de la guía es #' . ( $es_guia ? $es_guia->ID : '?' ) . ' hoy ' . ( $antes_f ? "#$antes_f" : 'sin foto' ) . '; archivo ' . ( $att ? "ya cargado (#$att)" : ( is_file( $ruta ) ? 'en ' . $carpeta . ' (' . size_format( filesize( $ruta ) ) . ')' : 'NO ESTÁ en ' . $carpeta ) ) );
if ( ! $att && ! is_file( $ruta ) ) {
	$hay_falta = true;
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' . ( $hay_falta ? ' (HAY «falta»: revisar antes de aplicar)' : '' ) );
	return;
}
if ( $hay_falta ) {
	WP_CLI::error( 'no aplico: hay textos que no están como se esperaba (ver «falta» y advertencias).' );
}

// ---- Aplicar --------------------------------------------------------------------------------------------------------------------------------------
$t = time();
foreach ( $plan as $x ) {
	$i = $x['i'];
	if ( $x['antes'] !== $x['despues'] ) {
		$r = wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['despues'] ) ], true );
		is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
		update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
	}
	if ( $x['faqs'] ) {
		update_field( 'faqs', $x['faqs'], $x['id'] );
	}
	$filas   = pm_filas( 'fuentes', $x['id'] );
	$nombres = array_column( $filas, 'nombre' );
	$urls    = array_filter( array_column( $filas, 'url' ) );
	$nuevas  = 0;
	foreach ( $x['fuentes'] as [ $nombre, $url, $of ] ) {
		if ( in_array( $nombre, $nombres, true ) || in_array( $url, $urls, true ) ) {
			continue;
		}
		$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $of, 'consultada' => $hoy ];
		$nuevas++;
	}
	$nuevas && update_field( 'fuentes', $filas, $x['id'] );
	update_field( 'actualizado', $hoy, $x['id'] );
	WP_CLI::log( "✓ {$x['que']} ($i) #{$x['id']}: contenido " . ( $x['antes'] !== $x['despues'] ? 'actualizado' : 'ya estaba' ) . ", +$nuevas fuentes" );
}
if ( $ficha_id ) {
	foreach ( $ficha_nuevo as $campo => $v ) {
		update_field( $campo, $v, $ficha_id );
	}
	update_field( 'verificado', $hoy, $ficha_id );
}
foreach ( $permiso as $i => $x ) {
	if ( $x['antes'] !== $x['despues'] ) {
		$r = wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['despues'] ) ], true );
		is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
		update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $t + ( 'es' === $i ? 0 : 1 ) );
		WP_CLI::log( "✓ nota del permiso ($i) #{$x['id']}: segunda copia quitada" );
	}
}
if ( ! $att ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$tmp = wp_tempnam( $foto['archivo'] );
	copy( $ruta, $tmp );
	$att = media_handle_sideload( [ 'name' => $foto['archivo'], 'tmp_name' => $tmp ], $es_guia->ID, $foto['titulo'] );
	if ( is_wp_error( $att ) ) {
		@unlink( $tmp );
		WP_CLI::error( $att->get_error_message() );
	}
	update_post_meta( $att, '_pm_archivo_origen', $foto['archivo'] );
}
update_post_meta( $att, '_wp_attachment_image_alt', $foto['alt']['es'] );
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	update_post_meta( $att, "_pm_alt_$i", $foto['alt'][ $i ] );
}
update_field( 'foto_autor', $foto['autor'], $att );
update_field( 'foto_origen', $foto['origen'], $att );
update_field( 'foto_pagina', $foto['pagina'], $att );
wp_update_post( [ 'ID' => $att, 'post_content' => "Foto elegida por el dueño de la página municipal de turismo de Concordia (pesca deportiva), el $hoy. La página no declara licencia de sus fotos; se usa con crédito a la Municipalidad de Concordia (riesgo conocido, docs/prestadores.md)." ] );
set_post_thumbnail( $es_guia->ID, $att );

// ---- Lectura de vuelta ----------------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $plan as $x ) {
	$i = $x['i'];
	$c = get_post( $x['id'] )->post_content;
	$b = preg_replace( '/\s+/u', ' ', $c ) === preg_replace( '/\s+/u', ' ', $x['despues'] ) && ! preg_match( '/46\/09 (y|and|e) 46\/16|no las leímos|did not read them|não as lemos/u', $c );
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) #{$x['id']} contenido y sin avisos viejos" );
	$ok = $ok && $b;
	$nombres = array_column( pm_filas( 'fuentes', $x['id'] ), 'nombre' );
	$bf      = ! array_diff( array_column( $x['fuentes'], 0 ), $nombres );
	WP_CLI::log( ( $bf ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) fuentes nuevas presentes" );
	$ok = $ok && $bf;
	if ( 'guia' === $x['que'] ) {
		$q    = $SEC[ $i ]['faq'][0];
		$leidas = pm_filas( 'faqs', $x['id'] );
		$bq   = false;
		foreach ( $leidas as $f ) {
			$bq = $bq || ( ( $f['pregunta'] ?? '' ) === $q && ( $f['respuesta'] ?? '' ) === $SEC[ $i ]['faq'][1] );
		}
		$bv = false !== strpos( $c, $SEC[ $i ]['bloques'][0][1] );
		WP_CLI::log( ( $bq ? 'VERIFIED' : 'FALLÓ' ) . ": guía ($i) FAQ de la veda leída de vuelta (" . count( $leidas ) . ' preguntas)' );
		WP_CLI::log( ( $bv ? 'VERIFIED' : 'FALLÓ' ) . ": guía ($i) sección municipal presente" );
		$ok = $ok && $bq && $bv;
	}
}
foreach ( $ficha_nuevo as $campo => $v ) {
	$b = (string) pm_campo( $campo, $ficha_id ) === $v;
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": ficha #$ficha_id $campo" );
	$ok = $ok && $b;
}
foreach ( $permiso as $i => $x ) {
	[ , $n ] = pm_cm_dedupe( get_post( $x['id'] )->post_content, $HEAD_PERMISO[ $i ] );
	$b = $n <= 1;
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": nota del permiso ($i) con {$n} aparición(es) de «{$HEAD_PERMISO[ $i ]}»" );
	$ok = $ok && $b;
}
$html = pm_foto( $att, pm_alt_foto( $att, 'es', 'Concordia' ), '400px' );
$bfo  = (int) get_post_thumbnail_id( $es_guia->ID ) === $att && wp_attachment_is_image( $att ) && false !== strpos( $html, '<img' ) && false !== strpos( $html, 'Municipalidad de Concordia' ) && false !== strpos( $html, 'href="https://www.concordia.gob.ar"' );
WP_CLI::log( ( $bfo ? 'VERIFIED' : 'FALLÓ' ) . ": foto #$att en la guía es: img + crédito «Municipalidad de Concordia» con enlace a concordia.gob.ar" );
$ok = $ok && $bfo;
foreach ( [ 'en', 'pt' ] as $i ) {
	$g = pm_cm_post( 'pm_destino', 'concordia', $i );
	WP_CLI::log( "guía ($i) #" . ( $g ? $g->ID : '?' ) . ' foto propia: ' . ( $g && get_post_thumbnail_id( $g->ID ) ? '#' . get_post_thumbnail_id( $g->ID ) : 'no (hereda de es por clave)' ) );
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
