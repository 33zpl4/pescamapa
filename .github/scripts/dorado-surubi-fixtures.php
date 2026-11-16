<?php
// Fixtures for the dorado pillar + surubí festival test (16/11/2026): the guides, notes and species pages that wordpress/scripts/pilar-dorado-2026-11-16.php and
// surubi-fiesta-2026-11-16.php link to or edit, in es/en/pt, all marked with the meta _prueba_ds so the step can remove them. The pillar stub carries the headings the script anchors on
// ("where", "best time", "does not cover"), the old call-to-action paragraph, the sentence the script rewrites and one "operadores" wording; the surubí species stub carries an FAQ group.
// The Goya guide gets a generated cover photo with an author (the festival note reuses it).
require_once ABSPATH . 'wp-admin/includes/image.php';
$guias = [
	'itati' => [ 'litoral', 'corrientes', -27.27, -58.24 ], 'ita-ibate' => [ 'litoral', 'corrientes', -27.4, -56.7 ], 'esteros-del-ibera' => [ 'litoral', 'corrientes', -28.5, -57.2 ],
	'goya' => [ 'litoral', 'corrientes', -29.14, -59.26 ], 'esquina' => [ 'litoral', 'corrientes', -30.0, -59.53 ], 'paso-de-la-patria' => [ 'litoral', 'corrientes', -27.32, -58.57 ],
	'la-paz' => [ 'litoral', 'entre-rios', -30.75, -59.64 ],
];
$notas = [
	'cuando-pescar-dorado', 'cuando-pescar-surubi', 'precio-viaje-dorado-lodge', 'viaje-dorado-amigos-precios', 'cuanto-cuesta-viaje-pesca-argentina', 'permiso-pesca-argentina-paso-a-paso',
	'licencias-extranjeros-argentina', 'semana-de-pesca-en-lodge-argentina', 'como-preparar-viaje-pesca-argentina',
];
$p  = fn( string $t ) => "<!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph -->";
$h2 = fn( string $t ) => "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
$T = [
	'es' => [ 'donde' => '¿Dónde se pesca el dorado en la Argentina?', 'epoca' => '¿Cuándo es la mejor época?', 'cubre' => 'Lo que esta guía todavía no cubre', 'cta' => '<strong>¿Querés que te ayudemos a armarlo?</strong> Contanos tus fechas, cuántos van y qué buscás en <a href="/armar-viaje/">el formulario del viaje</a> y te escribimos con opciones de guías y alojamiento, sin costo ni compromiso.',
		'viejo' => 'Los métodos por tramo (mosca, señuelos, carnada), las tarifas por día publicadas y el Delta cerca de Buenos Aires no están acá porque no los verificamos con una fuente y una fecha. Los sumamos cuando lo hagamos.',
		'op' => 'Los meses que más repiten los operadores son octubre–noviembre y marzo–mayo. Las ventanas cambian según el operador y el tramo.', 'faq' => 'Preguntas frecuentes', 'q' => '¿Se puede quedar con un surubí en Corrientes?', 'a' => 'No. Solo pesca y devolución.' ],
	'en' => [ 'donde' => 'Where do you fish golden dorado in Argentina?', 'epoca' => 'When is the best time?', 'cubre' => 'What this guide does not cover yet', 'cta' => '<strong>Want us to help plan it?</strong> Tell us your dates, group size and what you are after in <a href="/en/plan-your-trip/">the trip form</a> and we will write back with guide and lodging options, at no cost or commitment.',
		'viejo' => 'Methods by stretch (fly, lures, bait), published daily rates and the Delta near Buenos Aires are not here because we have not verified them with a source and a date. We add them when we do.',
		'op' => 'The months operators repeat most are October–November and March–May. The windows differ by operator and by stretch.', 'faq' => 'Frequently asked questions', 'q' => 'Can you keep a surubí in Corrientes?', 'a' => 'No. Catch and release only.' ],
	'pt' => [ 'donde' => 'Onde se pesca o dourado na Argentina?', 'epoca' => 'Quando é a melhor época?', 'cubre' => 'O que este guia ainda não cobre', 'cta' => '<strong>Quer que a gente ajude a montar?</strong> Conte suas datas, quantos vão e o que você busca no <a href="/pt/planejar-viagem/">formulário da viagem</a> e respondemos com opções de guias e hospedagem, sem custo nem compromisso.',
		'viejo' => 'Os métodos por trecho (mosca, iscas artificiais, isca natural), as tarifas diárias publicadas e o Delta perto de Buenos Aires não estão aqui porque não os verificamos com uma fonte e uma data. Vamos incluí-los quando fizermos isso.',
		'op' => 'Os meses que os operadores mais repetem são outubro–novembro e março–maio. As janelas mudam conforme o operador e o trecho.', 'faq' => 'Perguntas frequentes', 'q' => 'Posso ficar com o surubim em Corrientes?', 'a' => 'Não. Só pesca e soltura.' ],
];
function pm_ds_foto( string $nombre, array $rgb, string $autor ): int {
	$png = wp_upload_bits( $nombre . '.png', null, '' );
	$im  = imagecreatetruecolor( 400, 300 );
	imagefill( $im, 0, 0, imagecolorallocate( $im, ...$rgb ) );
	imagepng( $im, $png['file'] );
	$att = wp_insert_attachment( [ 'post_mime_type' => 'image/png', 'post_title' => $nombre, 'post_status' => 'inherit' ], $png['file'] );
	wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $png['file'] ) );
	update_field( 'foto_autor', $autor, $att );
	update_post_meta( $att, '_prueba_ds', 1 );
	return $att;
}
$att_goya = pm_ds_foto( 'portada-ds-goya', [ 60, 120, 180 ], 'AUTOR-PORTADA-GOYA' );
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	foreach ( $guias as $c => [ $reg, $prov, $lat, $lon ] ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => "Guía $c $i", 'post_name' => "prueba-ds-$c-$i", 'post_content' => 'Texto.' ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $c, $id );
		update_post_meta( $id, '_prueba_ds', 1 );
		if ( 'es' === $i ) {
			update_field( 'region', $reg, $id ); update_field( 'provincia', $prov, $id ); update_field( 'lat', $lat, $id ); update_field( 'lon', $lon, $id );
			update_field( 'especies', [ 'dorado', 'surubi' ], $id );
			'goya' === $c && set_post_thumbnail( $id, $att_goya );
		}
	}
	// The pillar stub.
	$t = $T[ $i ];
	$cuerpo = $p( '<strong>Respuesta corta.</strong> ' . $t['op'] ) . "\n\n" . $h2( $t['donde'] ) . "\n\n" . $p( 'Tabla de tramos.' ) . "\n\n" . $h2( $t['epoca'] ) . "\n\n" . $p( 'Texto de la época.' ) . "\n\n"
		. $p( $t['cta'] ) . "\n\n" . $h2( 'Equipo' ) . "\n\n" . $p( 'Texto del equipo.' ) . "\n\n" . $h2( $t['cubre'] ) . "\n\n" . $p( $t['viejo'] );
	$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => "Pilar $i", 'post_name' => "prueba-ds-pilar-$i", 'post_content' => $cuerpo ], true );
	update_field( 'idioma', $i, $id );
	update_field( 'clave', 'golden-dorado-guia', $id );
	update_field( 'bajada', $t['op'], $id );
	update_field( 'nota_datos', [ [ 'dato' => $t['op'] ], [ 'dato' => 'Otro dato.' ] ], $id );
	update_field( 'fuentes', [ [ 'nombre' => 'Suindá', 'url' => 'https://www.nervouswaters.com/the-lodges/suinda-lodge/', 'oficial' => 0, 'consultada' => '20261115' ] ], $id );
	update_post_meta( $id, '_prueba_ds', 1 );
	if ( 'es' === $i ) { update_field( 'nota_especie', 'dorado', $id ); update_field( 'nota_fecha', '20261029', $id ); }
	foreach ( $notas as $c ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => "Nota $c $i", 'post_name' => "prueba-ds-$c-$i", 'post_content' => $p( 'Texto.' ) ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $c, $id );
		update_post_meta( $id, '_prueba_ds', 1 );
		'es' === $i && update_field( 'nota_fecha', '20261026', $id );
	}
	foreach ( [ 'dorado', 'surubi' ] as $e ) {
		$cuerpo = $p( 'Datos.' ) . "\n\n" . $h2( 'Reglas' ) . "\n\n" . $p( 'Reglas.' );
		if ( 'surubi' === $e ) {
			$cuerpo .= "\n\n" . $h2( $t['faq'] ) . "\n\n<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\"><!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$t['q']}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$t['a']}</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->";
		}
		$id = wp_insert_post( [ 'post_type' => 'pm_especie', 'post_status' => 'publish', 'post_title' => "$e $i", 'post_name' => "prueba-ds-$e-$i", 'post_content' => $cuerpo ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $e, $id );
		update_post_meta( $id, '_prueba_ds', 1 );
	}
}
WP_CLI::log( "fixtures del pilar del dorado y de la Fiesta del Surubí creados (foto de Goya #$att_goya)" );
