<?php
/**
 * Pejerrey, 27/10/2026: species page (es, en, pt) with what is confirmed in docs/investigacion/chascomus.md, plus the
 * species link in two guides: Chascomús gets "pejerrey" and San Martín y Junín gets "trucha" (the sheet exists since 26/10).
 *
 * Published (with source and date): the 25 cm minimum size (Ministerio de Desarrollo Agrario de Buenos Aires, its
 * official measuring ruler; the species is "autóctona" there), the veda 1/9-30/11 with its days (the Ministry's
 * vedas page) and the 10-piece limit during the veda in lagoons without their own limit (owner, 27/10/2026). Not
 * published (hidden PENDIENTE note): typical and maximum size, habitat, baits and techniques, best season,
 * IGFA record, taxonomy source.
 *
 * Idempotent: a species page that exists is never touched; the guides' species are only added when missing.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = pejerrey-especie-2026-10-27).
 */

$aplicar = false;
$hoy     = '20261027';

function pm_pj_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_pj_h2( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_pj_datos( string $titulo, array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<li>{$i}</li>", $items ) );
	return "<!-- wp:group {\"className\":\"datos\"} -->\n<div class=\"wp-block-group datos\"><!-- wp:heading {\"level\":2} -->\n<h2 class=\"wp-block-heading\">{$titulo}</h2>\n<!-- /wp:heading -->\n<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list --></div>\n<!-- /wp:group -->";
}
function pm_pj_pend( string $t ): string {
	return "<!-- wp:paragraph -->\n<p>PENDIENTE: {$t}</p>\n<!-- /wp:paragraph -->";
}
function pm_pj_existe( string $tipo, string $clave, string $idioma ): bool {
	$ya = get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] );
	return (bool) array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $idioma );
}
function pm_pj_guia( string $clave, string $idioma, string $nombre ): string {
	$p = pm_buscar( 'pm_destino', $clave, $idioma ) ?? pm_buscar( 'pm_destino', $clave, 'es' );
	return $p ? '<a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( get_the_title( $p ) ) . '</a>' : esc_html( $nombre );
}

$vedas    = 'https://www.gba.gob.ar/desarrollo_agrario/DAPA/vedas';
$licencia = 'https://www.gba.gob.ar/desarrollo_agrario/pesca/articulos/obtenga_su_licencia_de_pesca_deportiva';

$ficha = [];
$ficha['es'] = [
	'titulo' => 'Pejerrey', 'slug' => 'pejerrey', 'cientifico' => 'Odontesthes bonariensis',
	'resumen' => 'El pejerrey de las lagunas bonaerenses: talla mínima de 25 cm y veda del 1 de septiembre al 30 de noviembre.',
	'seo_title' => 'Pejerrey: talla mínima, veda y dónde pescarlo | Pescamapa',
	'seo_description' => 'El pejerrey en la provincia de Buenos Aires: talla mínima de 25 cm, veda del 1 de septiembre al 30 de noviembre, cupo en la veda y dónde pescarlo.',
	'cuerpo' => [
		pm_pj_datos( 'Datos clave', [
			'<strong>Especie autóctona</strong>, según el Ministerio de Desarrollo Agrario de la Provincia de Buenos Aires.',
			'<strong>Talla mínima de captura:</strong> 25 cm (regla oficial de medición del Ministerio, consultada el 27/10/2026).',
			'<strong>Veda:</strong> del 1 de septiembre al 30 de noviembre en toda la provincia; en ese período solo se pesca sábados, domingos y feriados (<a href="' . $vedas . '" rel="noopener">página de vedas</a>, consultada el 27/10/2026).',
			'<strong>Cupo en la veda:</strong> 10 piezas por pescador y por día en las lagunas que no tienen un cupo propio, como Chascomús (cupos por laguna en la misma página).',
		] ),
		pm_pj_h2( 'Talla mínima: 25 cm' ),
		pm_pj_p( 'El Ministerio de Desarrollo Agrario de la Provincia de Buenos Aires indica una talla mínima de captura de <strong>25 cm</strong> en su regla oficial de medición (material de difusión, consultado el 27/10/2026). Medí cada pez antes de decidir si lo llevás: lo que no llega a esa medida se devuelve.' ),
		pm_pj_h2( 'La veda del pejerrey' ),
		pm_pj_p( 'Según la <a href="' . $vedas . '" rel="noopener">página de vedas</a> del Ministerio, la veda rige <strong>del 1 de septiembre al 30 de noviembre</strong> (Disposición 89/08, con renovación automática). Están prohibidas las competencias de pejerrey y la pesca comercial; la pesca deportiva se permite solo los sábados, domingos y feriados, con un máximo de piezas por pescador y por día que varía por laguna. Se prohíbe la motonáutica y las embarcaciones con motor fuera de borda de combustión interna navegan a 15 km/h como máximo.' ),
		pm_pj_h2( 'Licencia' ),
		pm_pj_p( 'Para pescar en aguas de la Provincia de Buenos Aires hace falta la <a href="' . $licencia . '" rel="noopener">licencia de pesca deportiva</a>. Los valores no figuran en la página del Ministerio: consultalos antes de salir.' ),
		pm_pj_h2( 'Dónde pescarlo' ),
		pm_pj_p( 'La laguna de Chascomús es la mayor de las siete Encadenadas: mirá nuestra guía de {chascomus}, con el viento del día y los accesos que podemos confirmar.' ),
		pm_pj_pend( 'tamaño y peso típicos y máximo de la especie, hábitat, carnadas y técnicas, mejor época y récord, con una fuente de referencia (ficha del MAGyP u otra publicación). Confirmar también el nombre científico con esa ficha.' ),
	],
];
$ficha['en'] = [
	'titulo' => 'Pejerrey', 'slug' => 'pejerrey', 'cientifico' => 'Odontesthes bonariensis',
	'resumen' => 'The pejerrey (silverside) of the Buenos Aires lagoons: 25 cm minimum size and a closed season from 1 September to 30 November.',
	'seo_title' => 'Pejerrey: size, closed season and where to fish | Pescamapa',
	'seo_description' => 'Pejerrey fishing in Buenos Aires province: 25 cm minimum size, closed season 1 September to 30 November, the limit during it and where to fish.',
	'cuerpo' => [
		pm_pj_datos( 'Key facts', [
			'<strong>Native species</strong>, according to the Buenos Aires Ministry of Agrarian Development.',
			'<strong>Minimum catch size:</strong> 25 cm (official measuring ruler of the Ministry, checked 27/10/2026).',
			'<strong>Closed season:</strong> 1 September to 30 November across the province; during it you may fish only on Saturdays, Sundays and public holidays (<a href="' . $vedas . '" rel="noopener">closed seasons page</a>, checked 27/10/2026).',
			'<strong>Limit during the closed season:</strong> 10 pieces per angler per day on lagoons without a limit of their own, such as Chascomús (lagoon-by-lagoon limits on the same page).',
		] ),
		pm_pj_h2( 'Minimum size: 25 cm' ),
		pm_pj_p( 'The Buenos Aires Ministry of Agrarian Development states a <strong>minimum catch size of 25 cm</strong> on its official measuring ruler (outreach material, checked 27/10/2026). Measure every fish before deciding to keep it: anything shorter goes back in the water.' ),
		pm_pj_h2( 'The pejerrey closed season' ),
		pm_pj_p( 'According to the Ministry\'s <a href="' . $vedas . '" rel="noopener">closed seasons page</a>, the closed season runs <strong>from 1 September to 30 November</strong> (Disposición 89/08, renewed automatically). Pejerrey competitions and commercial fishing are prohibited; sport fishing is allowed only on Saturdays, Sundays and public holidays, with a per-angler daily limit that varies by lagoon. Powerboating is prohibited and boats with an internal-combustion outboard may travel at 15 km/h at most.' ),
		pm_pj_h2( 'Licence' ),
		pm_pj_p( 'To fish in Buenos Aires province waters you need the <a href="' . $licencia . '" rel="noopener">sport fishing licence</a>. Prices are not on the Ministry\'s page: check them before you go.' ),
		pm_pj_h2( 'Where to fish it' ),
		pm_pj_p( 'The Chascomús lagoon is the largest of the seven Encadenadas: see our guide to {chascomus}, with the day\'s wind and the access points we can confirm.' ),
		pm_pj_pend( 'typical and maximum size and weight, habitat, baits and techniques, best season and record, with a reference source (MAGyP sheet or another publication). Also confirm the scientific name against that sheet.' ),
	],
];
$ficha['pt'] = [
	'titulo' => 'Peixe-rei (pejerrey)', 'slug' => 'peixe-rei', 'cientifico' => 'Odontesthes bonariensis',
	'resumen' => 'O pejerrey (peixe-rei) das lagoas de Buenos Aires: tamanho mínimo de 25 cm e defeso de 1º de setembro a 30 de novembro.',
	'seo_title' => 'Pejerrey: tamanho mínimo, defeso e onde pescar | Pescamapa',
	'seo_description' => 'O pejerrey na província de Buenos Aires: tamanho mínimo de 25 cm, defeso de 1º de setembro a 30 de novembro, limite no defeso e onde pescar.',
	'cuerpo' => [
		pm_pj_datos( 'Dados-chave', [
			'<strong>Espécie nativa</strong>, segundo o Ministério de Desenvolvimento Agrário da Província de Buenos Aires.',
			'<strong>Tamanho mínimo de captura:</strong> 25 cm (régua oficial de medição do Ministério, consultada em 27/10/2026).',
			'<strong>Defeso:</strong> de 1º de setembro a 30 de novembro em toda a província; nesse período só se pesca aos sábados, domingos e feriados (<a href="' . $vedas . '" rel="noopener">página de defesos</a>, consultada em 27/10/2026).',
			'<strong>Limite no defeso:</strong> 10 peças por pescador e por dia nas lagoas que não têm limite próprio, como Chascomús (limites por lagoa na mesma página).',
		] ),
		pm_pj_h2( 'Tamanho mínimo: 25 cm' ),
		pm_pj_p( 'O Ministério de Desenvolvimento Agrário da Província de Buenos Aires indica um <strong>tamanho mínimo de captura de 25 cm</strong> em sua régua oficial de medição (material de divulgação, consultado em 27/10/2026). Meça cada peixe antes de decidir levá-lo: o que não alcança essa medida volta para a água.' ),
		pm_pj_h2( 'O defeso do pejerrey' ),
		pm_pj_p( 'Segundo a <a href="' . $vedas . '" rel="noopener">página de defesos</a> do Ministério, o defeso vai <strong>de 1º de setembro a 30 de novembro</strong> (Disposição 89/08, com renovação automática). Ficam proibidas as competições de pejerrey e a pesca comercial; a pesca esportiva só é permitida aos sábados, domingos e feriados, com um máximo de peças por pescador e por dia que varia por lagoa. É proibida a motonáutica e as embarcações com motor de popa a combustão podem navegar a no máximo 15 km/h.' ),
		pm_pj_h2( 'Licença' ),
		pm_pj_p( 'Para pescar em águas da Província de Buenos Aires é preciso a <a href="' . $licencia . '" rel="noopener">licença de pesca esportiva</a>. Os valores não constam da página do Ministério: consulte-os antes de sair.' ),
		pm_pj_h2( 'Onde pescar' ),
		pm_pj_p( 'A lagoa de Chascomús é a maior das sete Encadenadas: veja nosso guia de {chascomus}, com o vento do dia e os acessos que conseguimos confirmar.' ),
		pm_pj_pend( 'tamanho e peso típicos e máximo, habitat, iscas e técnicas, melhor época e recorde, com uma fonte de referência (ficha do MAGyP ou outra publicação). Confirmar também o nome científico com essa ficha.' ),
	],
];

$f = fn( $nombre, $url, $ofi = 1 ) => [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $ofi, 'consultada' => $hoy ];
$fuentes = [
	$f( 'Ministerio de Desarrollo Agrario de Buenos Aires: vedas (veda anual del pejerrey)', $vedas ),
	$f( 'Ministerio de Desarrollo Agrario de Buenos Aires: obtenga su licencia de pesca deportiva', $licencia ),
	$f( 'Decreto 3237/95 (reglamento de la Ley 11.477), texto actualizado', 'https://normas.gba.gob.ar/documentos/V9A2qFPV.html' ),
];

// --- Species page ---------------------------------------------------------------------------
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$s = $ficha[ $i ];
	if ( pm_pj_existe( 'pm_especie', 'pejerrey', $i ) ) {
		WP_CLI::log( "especie pejerrey ($i) ya existe: no toco nada." );
		continue;
	}
	$cuerpo = strtr( implode( "\n\n", $s['cuerpo'] ), [ '{chascomus}' => pm_pj_guia( 'chascomus', $i, 'Chascomús' ) ] );
	if ( preg_match( '/\{[a-z_]+\}/', $cuerpo, $m ) ) {
		WP_CLI::error( "especie pejerrey ($i): marcador sin resolver {$m[0]}" );
	}
	if ( mb_strlen( $s['seo_title'] ) > 60 || mb_strlen( $s['seo_description'] ) > 160 ) {
		WP_CLI::error( "especie pejerrey ($i): el título SEO pasa de 60 o la descripción de 160." );
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": especie pejerrey ($i) «{$s['titulo']}» /{$s['slug']}/ — " . mb_strlen( wp_strip_all_tags( $cuerpo ) ) . ' car., title ' . mb_strlen( $s['seo_title'] ) . ' / description ' . mb_strlen( $s['seo_description'] ) . ' car.' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_especie', 'post_status' => 'publish', 'post_title' => $s['titulo'], 'post_name' => $s['slug'], 'post_content' => wp_slash( $cuerpo ) ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	foreach ( [ 'clave' => 'pejerrey', 'idioma' => $i, 'cientifico' => $s['cientifico'], 'resumen' => $s['resumen'], 'seo_title' => $s['seo_title'], 'seo_description' => $s['seo_description'], 'actualizado' => $hoy, 'fuentes' => $fuentes ] as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	$ok = count( pm_filas( 'fuentes', $id ) ) === count( $fuentes ) && 'pejerrey' === pm_clave_de( $id );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": especie pejerrey ($i) #$id " . pm_url_especie( 'pejerrey', $i ) );
	$ok || WP_CLI::error( "especie pejerrey ($i): la lectura de vuelta no coincide." );
}

// --- Species link in the guides (the shared data lives in the Spanish guide) -----------------
foreach ( [ 'chascomus' => 'pejerrey', 'san-martin-junin' => 'trucha' ] as $destino => $especie ) {
	$g = pm_buscar( 'pm_destino', $destino, 'es' );
	if ( ! $g ) {
		WP_CLI::warning( "$destino: no existe la guía en español, se omite." );
		continue;
	}
	$actual = array_values( (array) ( pm_campo( 'especies', $g->ID ) ?: [] ) );
	if ( in_array( $especie, $actual, true ) ) {
		WP_CLI::log( "$destino ya tiene $especie: no toco nada." );
		continue;
	}
	$nuevo = array_merge( $actual, [ $especie ] );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $destino #{$g->ID} especies " . wp_json_encode( $actual ) . ' → ' . wp_json_encode( $nuevo ) );
	if ( ! $aplicar ) {
		continue;
	}
	update_field( 'especies', $nuevo, $g->ID );
	$ok = array_values( (array) pm_campo( 'especies', $g->ID ) ) == $nuevo;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $destino especies = " . wp_json_encode( pm_campo( 'especies', $g->ID ) ) );
	$ok || WP_CLI::error( "$destino: la lectura de vuelta no coincide." );
}
if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
