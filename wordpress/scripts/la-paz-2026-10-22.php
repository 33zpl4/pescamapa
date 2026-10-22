<?php
/**
 * La Paz (Entre Ríos), 22/10/2026: fourth destination guide, in Spanish and Portuguese.
 * Built from docs/investigacion/la-paz.md (audited research). Different province than the
 * other three guides: the rules are Entre Ríos', not Corrientes'.
 *
 * What is published: the licence fees from the official table of the Dirección de Recursos
 * Naturales y Fiscalización de Entre Ríos (sent by the owner), the regional licence
 * agreement, and an honest "we could not confirm sizes, quotas and closures online: ask the
 * Dirección or your guide" (owner ruling 22/10/2026: what is not verifiable online is not
 * chased). Anything unconfirmed is a hidden PENDIENTE note. One outfitter, as a "listado con
 * información pública" (docs/prestadores.md).
 *
 * Idempotent: a guide or outfitter that exists is never touched. Dry run first
 * (dry run was clean: es + pt + 1 outfitter; applied); run with wp-script.yml (script = la-paz-2026-10-22).
 */

$aplicar = true;
$hoy     = '20261022';

function pm_lp_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_lp_h( string $texto ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$texto}</h2>\n<!-- /wp:heading -->";
}
function pm_lp_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_lp_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$fuentes = [
	[ 'Dirección de Recursos Naturales y Fiscalización de Entre Ríos: cuadro de permisos y licencias de caza y pesca (aranceles)', 'https://www.entrerios.gov.ar/', 1 ],
	[ 'Gobierno de Entre Ríos: trámite para obtener la licencia o el permiso de pesca deportiva', 'https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva', 1 ],
	[ 'Elonce: la provincia actualizó los aranceles de caza y pesca (29/05/2026)', 'https://www.elonce.com/uid-875603', 0 ],
	[ 'Gobierno de Santa Fe: Res. 0336/25, validez regional de licencias de pesca deportiva', 'https://www.santafe.gob.ar/ms/ambiente/wp-content/uploads/sites/93/2026/03/RES.-0336-25-VALIDEZ-REGIONAL-LICENCIAS-PESC-DEPORTIVA.pdf', 1 ],
	[ 'Turismo La Paz (Entre Ríos)', 'https://lapazentrerios.tur.ar/', 1 ],
	[ 'Municipalidad de La Paz (Entre Ríos)', 'https://lapaz.gob.ar/', 1 ],
	[ 'INA: sistema de alerta hidrológico (altura del Paraná)', 'https://alerta.ina.gob.ar/', 1 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$comun = [
	'clave'                => 'la-paz',
	'autor'                => 'Equipo Pescamapa',
	'actualizado'          => $hoy,
	'fuentes'              => $fuentes_filas,
	'reglamento_autoridad' => 'Dirección de Recursos Naturales y Fiscalización de Entre Ríos',
	'reglamento_url'       => 'https://www.entrerios.gov.ar/',
];

$guias = [];

// --- Español ---------------------------------------------------------------
$guias['es'] = [
	'titulo' => 'La Paz',
	'slug'   => 'pesca-en-la-paz',
	'cuerpo' => implode( "\n\n", [
		pm_lp_p( 'La Paz es la cabecera del departamento La Paz, en <strong>Entre Ríos</strong>, sobre la margen del río <strong>Paraná</strong>. Es otra provincia que Corrientes: las reglas y la licencia son las de Entre Ríos. Las especies más buscadas son el <strong>dorado</strong> y el <strong>surubí</strong>.' ),
		pm_lp_pendiente( 'confirmar las especies, las distancias y el tramo del río con una fuente oficial abierta.' ),
		pm_lp_h( 'Licencia de pesca en Entre Ríos' ),
		pm_lp_p( 'Para pescar en Entre Ríos hace falta licencia de pesca deportiva. Según el cuadro de permisos y licencias de la <strong>Dirección de Recursos Naturales y Fiscalización de Entre Ríos</strong> (consultado el 22/10/2026), los aranceles son:' ),
		pm_lp_lista( [
			'Licencia anual para residentes de Entre Ríos: <strong>$15.444,44</strong>.',
			'Licencia anual para residentes de otras provincias: <strong>$24.251,60</strong>.',
			'Permiso transitorio de 4 días para residentes de otras provincias: <strong>$5.488,52</strong>.',
			'Permiso para extranjeros (5 días): <strong>$24.251,60</strong>.',
		] ),
		pm_lp_p( 'Los montos se ajustan y la provincia los actualizó por última vez, según la prensa, el 29 de mayo de 2026: confirmá el valor al tramitar la licencia en el <a href="https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva" rel="noopener">trámite del Gobierno de Entre Ríos</a>. Existe además un acuerdo regional de reconocimiento recíproco de licencias en el Litoral que incluye a Entre Ríos (Resolución 0336/25 de Santa Fe): si tenés licencia de otra provincia de la región, confirmá con la Dirección las condiciones antes de viajar.' ),
		pm_lp_h( 'Tallas, cupos y vedas' ),
		pm_lp_p( 'No pudimos confirmar online la resolución vigente de <strong>tallas mínimas, cupos y vedas</strong> de Entre Ríos, y por eso no los publicamos. Antes de salir, consultalos con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos o con tu guía, que conoce lo que rige ese año. Las vedas por desove y las extraordinarias cambian cada año.' ),
		pm_lp_pendiente( 'resolución vigente de tallas, cupos y vedas (¿sigue la 276/2007?): pedirla a la Dirección o a Gemini con enlace.' ),
		pm_lp_h( 'Fiestas y torneos' ),
		pm_lp_p( 'La Paz tiene fiestas y torneos de pesca embarcada con devolución. No encontramos publicadas las fechas de la próxima edición: las anuncian la <a href="https://lapaz.gob.ar/" rel="noopener">Municipalidad de La Paz</a> y <a href="https://lapazentrerios.tur.ar/" rel="noopener">Turismo La Paz</a>.' ),
		pm_lp_pendiente( 'nombre oficial y fecha de la fiesta emblema (las fuentes chocan: «del Dorado» según el informe, «del Surubí Entrerriano» según un documento parlamentario sin año).' ),
		pm_lp_h( 'Guías y alojamientos' ),
		pm_lp_p( 'Abajo figura el guía que encontramos con información pública; todavía no lo verificamos con él y por eso está marcado.' ),
		pm_lp_h( 'Cómo llegar' ),
		pm_lp_p( 'La Paz está sobre la <strong>ruta nacional 12</strong>, al norte de Paraná. Para salir en lancha propia hace falta la habilitación náutica y el despacho ante Prefectura. La altura del río se consulta en el sistema de alerta del INA.' ),
	] ),
	'campos' => [
		'h1'          => 'Pesca deportiva en La Paz, Entre Ríos: licencia, aranceles y dónde preguntar',
		'bajada'      => 'La Paz, sobre el Paraná: dorado y surubí. Qué licencia hace falta en Entre Ríos y cuánto cuesta (aranceles al 22/10/2026) y qué consultar antes de salir.',
		'modalidades' => "Embarcada con guía\nBaitcasting y spinning\nTrolling\nCon carnada",
		'epoca'       => 'Depende de la especie, de la altura del río y de las vedas vigentes.',
		'seo_title'   => 'Pesca en La Paz, Entre Ríos: licencia, aranceles y reglas',
		'seo_description' => 'Guía de pesca en La Paz, Entre Ríos: dorado y surubí en el Paraná, licencia de pesca de Entre Ríos con aranceles vigentes, qué consultar antes de salir y fiestas de pesca.',
		'temporada'   => [
			[ 'meses' => 'Fiestas y torneos', 'que' => 'Fechas sin confirmar: las anuncian la Municipalidad de La Paz y Turismo La Paz.' ],
			[ 'meses' => 'Antes de cada salida', 'que' => 'Mirá la altura del Paraná (INA) y confirmá las vedas vigentes con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos.' ],
		],
		'reglamento_resumen' => 'Para pescar en Entre Ríos hace falta licencia de pesca deportiva. Según el cuadro de la Dirección de Recursos Naturales y Fiscalización de Entre Ríos (consultado el 22/10/2026): anual residentes de Entre Ríos $15.444,44; anual residentes de otras provincias $24.251,60; permiso transitorio de 4 días (otras provincias) $5.488,52; extranjeros (5 días) $24.251,60. No pudimos confirmar online la resolución vigente de tallas, cupos y vedas: consultala con la Dirección o con tu guía antes de salir.',
		'aguas' => [
			[ 'nombre' => 'Río Paraná frente a La Paz', 'nota' => 'La altura se consulta en el sistema de alerta del INA. Antes de pescar, confirmá las vedas vigentes.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => '¿Qué licencia hace falta para pescar en La Paz, Entre Ríos?', 'respuesta' => 'La licencia de pesca deportiva de Entre Ríos. Se tramita en el portal de trámites del Gobierno de Entre Ríos y los aranceles los fija la Dirección de Recursos Naturales y Fiscalización.' ],
			[ 'pregunta' => '¿Cuánto cuesta la licencia para un turista argentino?', 'respuesta' => 'Según el cuadro de la Dirección de Recursos Naturales y Fiscalización de Entre Ríos (consultado el 22/10/2026): licencia anual para residentes de otras provincias, $24.251,60; permiso transitorio de 4 días, $5.488,52. Extranjeros: permiso de 5 días, $24.251,60. Los montos se ajustan: confirmalos al tramitar.' ],
			[ 'pregunta' => '¿Me sirve la licencia de otra provincia?', 'respuesta' => 'Hay un acuerdo regional de reconocimiento recíproco de licencias en el Litoral que incluye a Entre Ríos (Resolución 0336/25 de Santa Fe). Las condiciones exactas confirmalas con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos antes de viajar.' ],
			[ 'pregunta' => '¿Qué tallas y cupos rigen en La Paz?', 'respuesta' => 'No pudimos confirmar online la resolución vigente de tallas, cupos y vedas de Entre Ríos, así que no los publicamos. Consultalos con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos o con tu guía antes de salir.' ],
			[ 'pregunta' => '¿Hay vedas?', 'respuesta' => 'Sí, pueden existir vedas por desove y vedas extraordinarias. Las fechas cambian cada año y no pudimos confirmarlas online: consultalas con la Dirección de Recursos Naturales y Fiscalización de Entre Ríos.' ],
			[ 'pregunta' => '¿Cuándo es la fiesta de pesca de La Paz?', 'respuesta' => 'La Paz organiza fiestas y torneos de pesca embarcada con devolución. No encontramos publicadas las fechas de la próxima edición: las anuncian la Municipalidad de La Paz y Turismo La Paz.' ],
			[ 'pregunta' => '¿Cómo llego a La Paz?', 'respuesta' => 'Por la ruta nacional 12, al norte de la ciudad de Paraná.' ],
			[ 'pregunta' => '¿Dónde veo la altura del río?', 'respuesta' => 'En el sistema de alerta hidrológico del INA, que muestra la altura del Paraná.' ],
		],
	],
];

// --- Português (BR) ----------------------------------------------------------
$guias['pt'] = [
	'titulo' => 'La Paz',
	'slug'   => 'pesca-em-la-paz',
	'cuerpo' => implode( "\n\n", [
		pm_lp_p( 'La Paz é a sede do departamento La Paz, em <strong>Entre Ríos</strong>, às margens do <strong>rio Paraná</strong>. É outra província, não Corrientes: as regras e a licença são as de Entre Ríos. As espécies mais procuradas são o <strong>dourado</strong> e o <strong>surubim</strong>.' ),
		pm_lp_pendiente( 'confirmar as espécies, as distâncias e o trecho do rio com uma fonte oficial aberta.' ),
		pm_lp_h( 'Licença de pesca em Entre Ríos' ),
		pm_lp_p( 'Para pescar em Entre Ríos é preciso licença de pesca esportiva. Segundo o quadro de permissões e licenças da <strong>Direção de Recursos Naturais e Fiscalização de Entre Ríos</strong> (consultado em 22/10/2026), as tarifas são:' ),
		pm_lp_lista( [
			'Licença anual para residentes de Entre Ríos: <strong>$15.444,44</strong> (pesos argentinos).',
			'Licença anual para residentes de outras províncias: <strong>$24.251,60</strong>.',
			'Permissão transitória de 4 dias para residentes de outras províncias: <strong>$5.488,52</strong>.',
			'Permissão para estrangeiros (5 dias): <strong>$24.251,60</strong>.',
		] ),
		pm_lp_p( 'Os valores são reajustados e a província os atualizou pela última vez, segundo a imprensa, em 29 de maio de 2026: confirme o valor ao tirar a licença no <a href="https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva" rel="noopener">trâmite do Governo de Entre Ríos</a>. Há também um acordo regional de reconhecimento recíproco de licenças no Litoral que inclui Entre Ríos (Resolução 0336/25 de Santa Fe).' ),
		pm_lp_h( 'Tamanhos, limites e proibições' ),
		pm_lp_p( 'Não conseguimos confirmar online a resolução vigente de <strong>tamanhos mínimos, limites e proibições (vedas)</strong> de Entre Ríos e, por isso, não os publicamos. Antes de sair, consulte a Direção de Recursos Naturais e Fiscalização de Entre Ríos ou o seu guia, que conhece o que vale naquele ano.' ),
		pm_lp_h( 'Festas e torneios' ),
		pm_lp_p( 'La Paz tem festas e torneios de pesca embarcada com devolução. Não encontramos publicadas as datas da próxima edição: quem as anuncia é a <a href="https://lapaz.gob.ar/" rel="noopener">Prefeitura de La Paz</a> e o <a href="https://lapazentrerios.tur.ar/" rel="noopener">Turismo La Paz</a>.' ),
		pm_lp_h( 'Guias e hospedagem' ),
		pm_lp_p( 'Abaixo está o guia que encontramos com informação pública; ainda não o verificamos com ele, por isso está marcado.' ),
		pm_lp_h( 'Como chegar' ),
		pm_lp_p( 'La Paz fica na <strong>rota nacional 12</strong>, ao norte de Paraná. Para sair com lancha própria é preciso a habilitação náutica e o despacho na Prefeitura Naval. A altura do rio é consultada no sistema de alerta do INA.' ),
	] ),
	'campos' => [
		'h1'          => 'Pesca esportiva em La Paz, Entre Ríos: licença, tarifas e a quem perguntar',
		'bajada'      => 'La Paz, no Paraná: dourado e surubim. Que licença é preciso em Entre Ríos e quanto custa (tarifas em 22/10/2026) e o que consultar antes de sair.',
		'modalidades' => "Embarcada com guia\nBaitcasting e spinning\nCorrico (trolling)\nCom isca",
		'epoca'       => 'Depende da espécie, da altura do rio e das proibições vigentes.',
		'seo_title'   => 'Pesca em La Paz, Entre Ríos: licença, tarifas e regras',
		'seo_description' => 'Guia de pesca em La Paz, Entre Ríos: dourado e surubim no Paraná, licença de pesca de Entre Ríos com tarifas vigentes, o que consultar antes de sair e festas de pesca.',
		'temporada'   => [
			[ 'meses' => 'Festas e torneios', 'que' => 'Datas não confirmadas: quem as anuncia é a Prefeitura de La Paz e o Turismo La Paz.' ],
			[ 'meses' => 'Antes de cada saída', 'que' => 'Veja a altura do Paraná (INA) e confirme as proibições vigentes com a Direção de Recursos Naturais e Fiscalização de Entre Ríos.' ],
		],
		'reglamento_resumen' => 'Para pescar em Entre Ríos é preciso licença de pesca esportiva. Segundo o quadro da Direção de Recursos Naturais e Fiscalização de Entre Ríos (consultado em 22/10/2026): anual para residentes de Entre Ríos $15.444,44; anual para residentes de outras províncias $24.251,60; permissão transitória de 4 dias (outras províncias) $5.488,52; estrangeiros (5 dias) $24.251,60. Não conseguimos confirmar online a resolução vigente de tamanhos, limites e proibições: consulte a Direção ou o seu guia antes de sair.',
		'aguas' => [
			[ 'nombre' => 'Rio Paraná em frente a La Paz', 'nota' => 'A altura é consultada no sistema de alerta do INA. Antes de pescar, confirme as proibições vigentes.', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'Que licença é preciso para pescar em La Paz, Entre Ríos?', 'respuesta' => 'A licença de pesca esportiva de Entre Ríos. É tirada no portal de trâmites do Governo de Entre Ríos e as tarifas são fixadas pela Direção de Recursos Naturais e Fiscalização.' ],
			[ 'pregunta' => 'Quanto custa a licença para um turista argentino?', 'respuesta' => 'Segundo o quadro da Direção de Recursos Naturais e Fiscalização de Entre Ríos (consultado em 22/10/2026): licença anual para residentes de outras províncias, $24.251,60; permissão transitória de 4 dias, $5.488,52. Estrangeiros: permissão de 5 dias, $24.251,60 (pesos argentinos). Os valores são reajustados: confirme ao tirar a licença.' ],
			[ 'pregunta' => 'Serve a licença de outra província?', 'respuesta' => 'Há um acordo regional de reconhecimento recíproco de licenças no Litoral que inclui Entre Ríos (Resolução 0336/25 de Santa Fe). Confirme as condições exatas com a Direção de Recursos Naturais e Fiscalização de Entre Ríos antes de viajar.' ],
			[ 'pregunta' => 'Que tamanhos e limites valem em La Paz?', 'respuesta' => 'Não conseguimos confirmar online a resolução vigente de tamanhos, limites e proibições de Entre Ríos, então não os publicamos. Consulte a Direção de Recursos Naturais e Fiscalização de Entre Ríos ou o seu guia antes de sair.' ],
			[ 'pregunta' => 'Quando é a festa de pesca de La Paz?', 'respuesta' => 'La Paz organiza festas e torneios de pesca embarcada com devolução. Não encontramos publicadas as datas da próxima edição: quem as anuncia é a Prefeitura de La Paz e o Turismo La Paz.' ],
			[ 'pregunta' => 'Como chego a La Paz?', 'respuesta' => 'Pela rota nacional 12, ao norte da cidade de Paraná.' ],
			[ 'pregunta' => 'Onde vejo a altura do rio?', 'respuesta' => 'No sistema de alerta hidrológico do INA, que mostra a altura do Paraná.' ],
		],
	],
];

// --- Create ---------------------------------------------------------------------
$es_id = 0;
foreach ( [ 'es', 'pt' ] as $i ) {
	$g  = $guias[ $i ];
	$ya = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'la-paz', 'numberposts' => -1 ] );
	$existe = array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $i );
	if ( $existe ) {
		WP_CLI::log( "la-paz ($i) ya existe: no toco nada." );
		if ( 'es' === $i ) {
			$es_id = reset( $existe )->ID;
		}
		continue;
	}
	WP_CLI::log( "la-paz ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $fuentes_filas ) . ' fuentes' );
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
		$campos += [
			'region' => 'litoral', 'provincia' => 'entre-rios', 'lat' => -30.745, 'lon' => -59.645,
			'especies' => [ 'dorado', 'surubi' ],
		];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	if ( 'es' === $i ) {
		$es_id = $id;
		$proximo = get_page_by_path( 'la-paz', OBJECT, 'pm_proximo' );
		if ( $proximo && get_post_thumbnail_id( $proximo->ID ) ) {
			set_post_thumbnail( $id, get_post_thumbnail_id( $proximo->ID ) );
			WP_CLI::log( 'foto: la de la tarjeta "próximamente"' );
		} else {
			WP_CLI::warning( 'sin foto: cargala desde el panel (Imagen destacada)' );
		}
	}
	WP_CLI::log( "✓ la-paz ($i) publicada #$id: " . get_permalink( $id ) );
	$leida = pm_filas( 'faqs', $id );
	WP_CLI::log( '  VERIFIED: ' . count( $leida ) . ' preguntas y ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes leídas de vuelta' );
}

// --- Outfitter: "listado con información pública" -------------------------------------
$hay = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => 'Víctor Flores Pesca', 'numberposts' => 1, 'fields' => 'ids' ] );
if ( $hay ) {
	WP_CLI::log( '· prestador Víctor Flores Pesca ya existe' );
} elseif ( ! $aplicar ) {
	WP_CLI::log( '· prestador Víctor Flores Pesca: se crearía (listado público, sin verificar)' );
} else {
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => 'Víctor Flores Pesca' ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::warning( $pid->get_error_message() );
	} else {
		$f = [
			'tipo' => [ 'guia' ], 'prest_destino' => 'la-paz', 'localidad' => 'La Paz', 'descripcion' => 'Guía de pesca con excursiones embarcadas en La Paz.',
			'prest_modalidades' => [ 'carnada', 'trolling', 'spinning' ], 'lancha' => '', 'motor' => '',
			'prest_publico' => 1, 'prest_fuente' => 'http://www.victorflores.com.ar/', 'estado' => 'relevado', 'verificado' => $hoy,
			'datos_ejemplo' => 0, 'prest_consentimiento' => 0,
			'notas' => 'Listado con información pública (informe de investigación del 22/10/2026, auditado). Pescamapa no abrió el sitio ni confirmó contacto, precio ni idiomas. Falta contactarlo.',
		];
		foreach ( $f as $k => $v ) {
			update_field( $k, $v, $pid );
		}
		WP_CLI::log( "✓ prestador Víctor Flores Pesca #$pid (listado público, sin verificar)" );
	}
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
