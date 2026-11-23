<?php
/**
 * El Rincón Lodge (Estancia Rincón de los Morros, 1 h 40 min by vehicle from the city of Río Gallegos): NEW card with three photos, 23/11/2026.
 * Source: the lodge's own material, sent to Pescamapa on 23/11/2026 so that we publish it: a 5-page Spanish PDF programme and photos.
 * Everything public is attributed ("según el lodge") and limited to what the PDF says: the estancia and the access (20 km of the Gallegos, 35 km of the
 * Penitente, both banks), the original main house brought from England in 1902 and its rooms (4 single + 2 double), the sea-trout run (mid-December to
 * the end of April), techniques and rods per river, local guides in 4x4 with a pool rotation (the lodge says they are registered with the Santa Cruz
 * tourism secretariat: its statement, never our check), the catch book, what to bring.
 * NOT in the PDF, so NOT written: prices (the lodge asked that they are NOT shown: field empty, "a consultar"; the rate is not written anywhere, notes
 * included), what the stay includes item by item, programme length, weekly capacity, languages, transfers.
 * Tier: consent (the lodge sent its material to be listed) + first-hand data, NO commission agreement recorded yet => estado "contactado" (NOT "acuerdo"),
 * prest_consentimiento on, field `comision` untouched (the owner is confirming it). The card is therefore public WITHOUT the "Operador con acuerdo"
 * mark and WITHOUT the "sin verificar" notice (that one needs consentimiento off), after the cards that have an agreement.
 * Photos (ceded by the lodge for this listing, credit "El Rincón Lodge", origen cedida): casa (main photo), living, estancia, in that order. A fourth photo
 * with identifiable people is NOT used. Files in wordpress/fotos/, copied to /tmp/pm-fotos by wp-script.yml; reused by _pm_archivo_origen.
 * Idempotent: finds the card by title (creates only if there is none, stops if there are two), writes only the fields that differ, reuses the photos.
 * Dry run first ($aplicar = false). Run with wp-script.yml (script = el-rincon-lodge-2026-11-23).
 */

$aplicar = false;
$hoy     = '20261123';
$nombre  = 'El Rincón Lodge';
$carpeta = '/tmp/pm-fotos';

$fotos = [
	[
		'archivo' => 'el-rincon-lodge-casa.jpg',
		'titulo'  => 'Casa principal de El Rincón Lodge',
		'alt'     => [
			'es' => 'Casa principal de El Rincón Lodge, de chapa blanca con techo rojo y galería vidriada, rodeada de árboles y lavandas.',
			'en' => 'Main house of El Rincón Lodge, white corrugated metal with a red roof and a glazed veranda, surrounded by trees and lavender.',
			'pt' => 'Casa principal do El Rincón Lodge, de chapa branca com telhado vermelho e varanda envidraçada, cercada de árvores e lavandas.',
		],
	],
	[
		'archivo' => 'el-rincon-lodge-living.jpg',
		'titulo'  => 'Sala de estar y comedor de El Rincón Lodge',
		'alt'     => [
			'es' => 'Sala de estar y comedor de El Rincón Lodge, con chimenea encendida, sillones y mesa de madera.',
			'en' => 'Living and dining room of El Rincón Lodge, with a lit fireplace, armchairs and a wooden table.',
			'pt' => 'Sala de estar e sala de jantar do El Rincón Lodge, com lareira acesa, poltronas e mesa de madeira.',
		],
	],
	[
		'archivo' => 'el-rincon-lodge-estancia.jpg',
		'titulo'  => 'La estancia de El Rincón Lodge vista desde el campo',
		'alt'     => [
			'es' => 'Estancia patagónica de techos rojos vista desde el campo, con corrales de madera y una meseta al fondo.',
			'en' => 'Patagonian estancia with red roofs seen from the fields, with wooden corrals and a mesa in the background.',
			'pt' => 'Estância patagônica de telhados vermelhos vista do campo, com currais de madeira e uma mesa rochosa ao fundo.',
		],
	],
];
$autor  = 'El Rincón Lodge';
$origen = 'cedida';

$campos = [
	'tipo'          => [ 'alojamiento', 'operador' ],
	'prest_destino' => 'rio-gallegos',
	'localidad'     => 'Estancia Rincón de los Morros, a 1 h 40 min de Río Gallegos (Santa Cruz)',
	'descripcion'   => 'Programa de pesca con alojamiento en la Estancia Rincón de los Morros, en el sur de la Patagonia, a 1 h 40 min en vehículo de la ciudad de Río Gallegos. Según el lodge, los pescadores acceden a 20 km del río Gallegos, donde la trucha marrón anádroma sube durante toda la temporada, y a 35 km del río Penitente, con trucha anádroma y muchas truchas marrones residentes, en ambas márgenes de los dos ríos. Conserva la casa principal original, traída de Inglaterra en 1902, a orillas del Penitente: cuatro habitaciones individuales y dos dobles, living amplio con hogar a leña, sala de desayuno, comedor con cocina regional y un jardín sobre el río con lugar para el cordero al palo. Según el lodge, la entrada principal de trucha marina va de mediados de diciembre a fines de abril. Se pesca con caña de una o dos manos según el día y las condiciones. En el Gallegos, al que se llega en unos 15 minutos en vehículo según el tramo, recomienda caña de dos manos de hasta 13 pies, #7 u #8; el Penitente queda a pasos del lodge y se pesca fuera de la rotación, antes del desayuno o después del almuerzo, con caña de una mano de hasta 10 pies #6 o #7 (puede subir en pocas horas con lluvia o nieve en la cordillera y bajar igual de rápido). El lodge cuenta residentes del Penitente de hasta 3 kg y truchas marinas «hasta 10 kg o más», y anota cada pez en el libro de capturas del Gallegos o del Penitente. Los guías son locales, llevan a los pescadores en 4×4 hasta el río en cada salida con una rotación de pozones y, según el lodge, están inscriptos ante la Secretaría de Turismo de Santa Cruz. Para llevar: waders, botas de vadeo, campera de vadeo y ropa de abrigo.',
	'descripcion_en' => 'Fishing programme with lodging at Estancia Rincón de los Morros, in southern Patagonia, 1 h 40 min by vehicle from the city of Río Gallegos. According to the lodge, anglers have access to 20 km of the Río Gallegos, where sea-run brown trout run all season, and to 35 km of the Río Penitente, with sea trout and many resident brown trout, on both banks of both rivers. It keeps the original main house, brought from England in 1902, on the bank of the Penitente: four single rooms and two doubles, a large living room with a wood fireplace, a breakfast room, a dining room with regional cooking and a garden on the river with a place for cordero al palo (spit-roasted lamb). According to the lodge, the main sea-trout run goes from mid-December to the end of April. You fish with a one-hand or two-hand rod depending on the day and the conditions. On the Gallegos, about 15 minutes away by vehicle depending on the beat, it recommends a two-hand rod up to 13 ft, #7 or #8; the Penitente is steps from the lodge and is fished outside the rotation, before breakfast or after lunch, with a one-hand rod up to 10 ft #6 or #7 (it can rise in a few hours with rain or snow in the cordillera and drop just as fast). The lodge reports residents in the Penitente up to 3 kg and sea trout “up to 10 kg or more”, and records every fish in the Gallegos or Penitente catch book. The guides are local, drive anglers in 4×4 vehicles to the river each session following a pool rotation and, according to the lodge, are registered with the Santa Cruz tourism secretariat. To bring: waders, wading boots, a wading jacket and warm clothing.',
	'descripcion_pt' => 'Programa de pesca com hospedagem na Estancia Rincón de los Morros, no sul da Patagônia, a 1 h 40 min de veículo da cidade de Río Gallegos. Segundo o lodge, os pescadores têm acesso a 20 km do rio Gallegos, onde a truta marrom anádroma sobe durante toda a temporada, e a 35 km do rio Penitente, com truta anádroma e muitas trutas marrons residentes, nas duas margens dos dois rios. Conserva a casa principal original, trazida da Inglaterra em 1902, às margens do Penitente: quatro quartos individuais e dois duplos, sala ampla com lareira a lenha, sala de café da manhã, sala de jantar com cozinha regional e um jardim sobre o rio com lugar para o cordero al palo (cordeiro no espeto). Segundo o lodge, a principal subida da truta marinha vai de meados de dezembro ao fim de abril. Pesca-se com vara de uma ou duas mãos conforme o dia e as condições. No Gallegos, a cerca de 15 minutos de veículo conforme o trecho, recomenda vara de duas mãos de até 13 pés, #7 ou #8; o Penitente fica a poucos passos do lodge e se pesca fora do rodízio, antes do café da manhã ou depois do almoço, com vara de uma mão de até 10 pés #6 ou #7 (pode subir em poucas horas com chuva ou neve na cordilheira e baixar com a mesma rapidez). O lodge cita residentes do Penitente de até 3 kg e trutas marinhas «de até 10 kg ou mais», e registra cada peixe no livro de capturas do Gallegos ou do Penitente. Os guias são locais, levam os pescadores em veículos 4×4 até o rio a cada saída, com um rodízio de poços e, segundo o lodge, estão inscritos na Secretaria de Turismo de Santa Cruz. Para levar: waders, botas de vadeio, jaqueta de vadeio e roupas de frio.',
	'prest_especies'    => [ 'trucha-marron-anadroma' ],
	'prest_modalidades' => [ 'mosca' ],
	'prest_programa'    => 'Estadía en la estancia con pesca guiada en el río Gallegos y en el Penitente, con rotación de pozones; el Penitente también se pesca fuera de la rotación',
	'prest_programa_pt' => 'Estadia na estância com pesca guiada nos rios Gallegos e Penitente, com rodízio de poços; o Penitente também se pesca fora do rodízio',
	'prest_programa_en' => 'Stay at the estancia with guided fishing on the Gallegos and Penitente rivers, following a pool rotation; the Penitente can also be fished outside the rotation',
	'prest_temporada'    => 'Entrada principal de trucha marina: de mediados de diciembre a fines de abril, según el lodge',
	'prest_temporada_pt' => 'Principal subida da truta marinha: de meados de dezembro ao fim de abril, segundo o lodge',
	'prest_temporada_en' => 'Main sea-trout run: mid-December to the end of April, according to the lodge',
	'precio' => '', 'precio_fecha' => '',
	'estado' => 'contactado', 'prest_consentimiento' => 1, 'prest_publico' => 0, 'datos_ejemplo' => 0, 'verificado' => $hoy,
	'notas' => 'Ficha cargada el 23/11/2026 con el material que el propio lodge mandó a Pescamapa para que lo publiquemos: programa en PDF de 5 páginas, en español, y fotos. Todo lo público está atribuido («según el lodge») y limitado a lo que dice el PDF. TARIFA: el lodge pidió que NO se muestre; no la tenemos cargada acá ni en ninguna página (precio y fecha vacíos: «a consultar»). Estado «Contactado» + Aceptó aparecer: sin acuerdo comercial registrado todavía; la comisión la está confirmando el dueño (campo Acuerdo comercial sin tocar). Cuando se confirme por escrito: estado «Trabajamos juntos» y completar el campo Acuerdo comercial. FOTOS: tres, cedidas por el lodge para este listado, con crédito «El Rincón Lodge» (casa principal, living, estancia); se quitan si lo piden. Una cuarta, con personas identificables, NO se usa hasta que el dueño la confirme. SIN DATO en el PDF, por eso vacío: qué incluye la estadía punto por punto, duración del programa, cupo por semana (hay 4 habitaciones individuales y 2 dobles), idiomas, traslados. Moscas que cita el lodge (para una nota, no para la ficha): Yuk Bug, beadheads, Copper John, Sunray Shadow, Girdle Bug, Woolly Bugger, Egg Sucking Leech y ninfas #6 a #12 (más grandes con agua alta). El contacto del lodge no se publica ni se copia al repo; queda fuera de él.',
];

$prohibido = '/US\$|USD|\$\s?\d|€|\b\d{1,3}[.,]\d{3}\b|\d ?%|comisi|commission|comiss|@|https?:|www\.|\+\d|\btel[eé]fono|whatsapp|\bphone\b|\boperador|\boperator|\boperadora/iu';
$publicos  = [ 'localidad', 'descripcion', 'descripcion_en', 'descripcion_pt', 'prest_programa', 'prest_programa_en', 'prest_programa_pt', 'prest_temporada', 'prest_temporada_en', 'prest_temporada_pt' ];

$ver  = fn( $v ) => is_array( $v ) ? implode( ',', $v ) : (string) $v;
$norm = function ( string $k, $v ): string {
	if ( in_array( $k, [ 'precio_fecha', 'verificado' ], true ) ) {
		return preg_replace( '/\D/', '', (string) $v );
	}
	if ( in_array( $k, [ 'prest_consentimiento', 'prest_publico', 'datos_ejemplo' ], true ) ) {
		return (string) (int) $v;
	}
	return is_array( $v ) ? implode( ',', $v ) : (string) $v;
};

// ---- Checks before anything is written -----------------------------------------------------------------------------------------------------
foreach ( $publicos as $k ) {
	if ( preg_match( $prohibido, $campos[ $k ], $m ) ) {
		WP_CLI::error( "el texto público de $k lleva algo que no se publica: «{$m[0]}»" );
	}
}
'' === $campos['precio'] && '' === $campos['precio_fecha'] || WP_CLI::error( 'el precio debe quedar vacío' );
'acuerdo' !== $campos['estado'] && ! array_key_exists( 'comision', $campos ) || WP_CLI::error( 'esta ficha no lleva acuerdo ni comisión' );
$opciones = pm_ui()['opciones'] ?? [];
foreach ( [ 'tipo' => 'tipo', 'prest_modalidades' => 'modalidad' ] as $k => $lista ) {
	foreach ( $campos[ $k ] as $clave ) {
		isset( $opciones[ $lista ][ $clave ] ) || WP_CLI::error( "la opción «$clave» no existe en la lista $lista" );
	}
}
WP_CLI::log( 'OK: el texto público (es/en/pt) no lleva precio, enlace, contacto, comisión ni la palabra «operador»; precio vacío; sin acuerdo ni comisión.' );

// ---- 1. The card: by title, never a duplicate -----------------------------------------------------------------------------------------------
$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 3, 'fields' => 'ids' ] );
count( $ids ) > 1 && WP_CLI::error( "hay más de una ficha «$nombre»: " . implode( ',', $ids ) );
$pid = $ids ? (int) $ids[0] : 0;
if ( ! $pid ) {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» NUEVA (no existe), campos:" );
	foreach ( $campos as $k => $v ) {
		WP_CLI::log( "  + $k: " . $ver( $v ) );
	}
	$cambios = $campos;
} else {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": ficha «{$nombre}» #$pid YA existe (consentimiento=" . (int) pm_campo( 'prest_consentimiento', $pid ) . ' estado=' . pm_campo( 'estado', $pid ) . '); se actualiza solo lo que difiere:' );
	$cambios = [];
	foreach ( $campos as $k => $nuevo ) {
		$antes = pm_campo( $k, $pid );
		$igual = $norm( $k, $antes ) === $norm( $k, $nuevo );
		WP_CLI::log( ( $igual ? '  = ' : '  ~ ' ) . $k . ( $igual ? ' sin cambios' : '' ) );
		if ( ! $igual ) {
			WP_CLI::log( '      ANTES:   ' . $ver( $antes ) );
			WP_CLI::log( '      DESPUÉS: ' . $ver( $nuevo ) );
			$cambios[ $k ] = $nuevo;
		}
	}
	// A fee, an agreement or a price somebody added later is not ours to overwrite silently: report it.
	foreach ( [ 'comision', 'prest_oferta' ] as $k ) {
		'' !== trim( (string) pm_campo( $k, $pid ) ) && WP_CLI::warning( "la ficha ya tiene $k cargado; este script no lo toca" );
	}
}

// ---- 2. The photos: reuse by _pm_archivo_origen, else sideload --------------------------------------------------------------------------------
$plan_fotos = [];
foreach ( $fotos as $n => $f ) {
	$ya   = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $f['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
	$att  = $ya ? (int) $ya[0] : 0;
	$ruta = "$carpeta/{$f['archivo']}";
	WP_CLI::log( '  foto ' . ( $n + 1 ) . " ({$f['archivo']}): " . ( $att ? "adjunto #$att ya cargado, se reutiliza" : ( is_file( $ruta ) ? 'por cargar (' . size_format( filesize( $ruta ) ) . ')' : 'FALTA el archivo' ) ) . " | crédito «$autor», origen $origen" );
	foreach ( $f['alt'] as $i => $a ) {
		WP_CLI::log( "      alt $i: $a" );
	}
	if ( ! $att && ! is_file( $ruta ) ) {
		WP_CLI::error( "falta {$f['archivo']} en $carpeta" );
	}
	$plan_fotos[ $n ] = $att;
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

// ---- Apply ---------------------------------------------------------------------------------------------------------------------------------------
if ( ! $pid ) {
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
	is_wp_error( $pid ) && WP_CLI::error( $pid->get_error_message() );
	$pid = (int) $pid;
	WP_CLI::log( "✓ ficha «{$nombre}» creada #$pid" );
}
foreach ( $cambios as $k => $v ) {
	update_field( $k, $v, $pid );
}

require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$adjuntos = [];
foreach ( $fotos as $n => $f ) {
	$att = $plan_fotos[ $n ];
	if ( ! $att ) {
		$tmp = wp_tempnam( $f['archivo'] );
		copy( "$carpeta/{$f['archivo']}", $tmp );
		$att = media_handle_sideload( [ 'name' => $f['archivo'], 'tmp_name' => $tmp ], $pid, $f['titulo'] );
		if ( is_wp_error( $att ) ) {
			@unlink( $tmp );
			WP_CLI::error( $att->get_error_message() );
		}
		update_post_meta( $att, '_pm_archivo_origen', $f['archivo'] );
		WP_CLI::log( "✓ foto cargada #$att {$f['archivo']}" );
	}
	update_post_meta( $att, '_wp_attachment_image_alt', $f['alt']['es'] );
	foreach ( $f['alt'] as $i => $a ) {
		update_post_meta( $att, "_pm_alt_$i", $a );
	}
	update_field( 'foto_autor', $autor, $att );
	update_field( 'foto_origen', $origen, $att );
	$adjuntos[ $n ] = (int) $att;
}
// Our three first (main photo = house), then anything else the card already had.
$gal = array_map( 'intval', (array) ( pm_campo( 'fotos', $pid ) ?: [] ) );
update_field( 'fotos', array_merge( $adjuntos, array_values( array_diff( $gal, $adjuntos ) ) ), $pid );
wp_cache_flush();

// ---- Read back -----------------------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $campos as $k => $nuevo ) {
	$leido = pm_campo( $k, $pid );
	$b     = $norm( $k, $leido ) === $norm( $k, $nuevo );
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» #$pid $k = " . mb_substr( $ver( $leido ), 0, 140 ) . ( mb_strlen( $ver( $leido ) ) > 140 ? '… (' . mb_strlen( $ver( $leido ) ) . ' caracteres)' : '' ) );
	$ok = $ok && $b;
}
$b = '' === (string) pm_campo( 'comision', $pid ) && 'acuerdo' !== (string) pm_campo( 'estado', $pid );
WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ': sin acuerdo ni comisión (estado=' . pm_campo( 'estado', $pid ) . ", comision=\"" . (string) pm_campo( 'comision', $pid ) . '")' );
$ok = $ok && $b;

$fotos_leidas = array_map( 'intval', (array) ( pm_campo( 'fotos', $pid ) ?: [] ) );
foreach ( $adjuntos as $n => $att ) {
	$b = ( $fotos_leidas[ $n ] ?? 0 ) === $att && wp_attachment_is_image( $att ) && $autor === (string) pm_campo( 'foto_autor', $att ) && $origen === (string) pm_campo( 'foto_origen', $att );
	foreach ( $fotos[ $n ]['alt'] as $i => $a ) {
		$b = $b && pm_alt_foto( $att, $i, '' ) === $a;
	}
	WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ': foto ' . ( $n + 1 ) . " #$att posición " . ( array_search( $att, $fotos_leidas, true ) + 1 ) . ' ' . wp_get_attachment_url( $att ) . ' crédito=' . pm_campo( 'foto_autor', $att ) . ' origen=' . pm_campo( 'foto_origen', $att ) . ' alt_es=' . pm_alt_foto( $att, 'es', '' ) . ' | alt_en=' . pm_alt_foto( $att, 'en', '' ) . ' | alt_pt=' . pm_alt_foto( $att, 'pt', '' ) );
	$ok = $ok && $b;
}

$r     = null;
$orden = [];
foreach ( pm_prestadores_destino( 'rio-gallegos' ) as $x ) {
	$orden[] = $x['nombre'] . ( $x['acuerdo'] && $x['consentimiento'] ? ' [acuerdo]' : '' );
	if ( (int) $x['id'] === $pid ) {
		$r = $x;
	}
}
WP_CLI::log( 'Orden de la lista de rio-gallegos: ' . implode( ' > ', $orden ) );
$sin_tipo = $r ? array_diff_key( $r, [ 'tipo' => 1 ] ) : []; // the key "operador" of the type is not public text
$volcado  = wp_json_encode( $sin_tipo, JSON_UNESCAPED_UNICODE );
$b = $r && pm_prestador_publico( $r ) && ! $r['acuerdo'] && $r['consentimiento'] && '' === $r['precio'] && '' === (string) $r['precio_fecha'] && 3 <= count( $r['fotos'] ) && array_slice( $r['fotos'], 0, 3 ) === $adjuntos
	&& ! preg_match( $prohibido, (string) $volcado );
WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": «{$nombre}» aparece en pm_prestadores_destino('rio-gallegos') | posición=" . ( array_search( $nombre, $orden, true ) === false ? 'no está' : array_search( $nombre, $orden, true ) + 1 ) . ' consentimiento=' . (int) ( $r['consentimiento'] ?? 0 ) . ' acuerdo=' . (int) ( $r['acuerdo'] ?? 0 ) . ' precio="' . ( $r['precio'] ?? '?' ) . '" fotos=' . count( $r['fotos'] ?? [] ) . ' sin precio, enlace, contacto ni «operador» en el texto público=' . (int) ( $r && ! preg_match( $prohibido, (string) $volcado ) ) );
$ok = $ok && $b;
foreach ( $publicos as $k ) {
	if ( preg_match( $prohibido, (string) pm_campo( $k, $pid ), $m ) ) {
		WP_CLI::log( "FALLÓ: el campo público $k leído de vuelta lleva «{$m[0]}»" );
		$ok = false;
	}
}
$ok || WP_CLI::error( 'algo no se leyó de vuelta como se esperaba.' );
WP_CLI::log( 'VERIFIED: todo leído de vuelta' );
WP_CLI::log( 'APLICADO' );
