<?php
/**
 * Cover photos for the notes that had none, 14/11/2026 (owner: "las notas, sobre todo las de portada, deben tener foto").
 * (a) three photos the owner downloaded from Unsplash (credit "Foto: <author>, Unsplash", licence = Unsplash licence, foto_origen=unsplash);
 * (b) eight provisional photos from Wikimedia Commons (author, licence, licence URL, page and date read come from Commons:
 *     /tmp/pm-fotos/fotos-wp-notas.json, written by scripts/commons.py bajar-wp-notas, centre-cropped to 4:3, <= 1600 px).
 * The photo goes on the Spanish note (en/pt inherit it by clave, pm_nota()). NEVER replaces a photo a note already has.
 * Alt text per language lives in the attachment meta _pm_alt_es/_en/_pt (pm_alt_foto()); the attachment description carries
 * the "FOTO PROVISORIA" mark for the Commons ones. Idempotent (attachment reused by file name). Dry run first ($aplicar = false).
 * Run with wp-script.yml (script = fotos-notas-2026-11-14).
 */

$aplicar = true;
$carpeta = '/tmp/pm-fotos';
$hoy     = '2026-11-14';
$unsplash_lic = 'https://unsplash.com/license';

$comm = json_decode( (string) @file_get_contents( "$carpeta/fotos-wp-notas.json" ), true );
if ( ! is_array( $comm ) || ! $comm ) {
	WP_CLI::error( 'no pude leer fotos-wp-notas.json' );
}

// nota => [ origin, file, author, profile, title, alt es/en/pt ]
$fotos = [
	'precio-pesca-patagonia-sur' => [
		'tipo' => 'unsplash', 'archivo' => 'fitz-roy-rio-de-montana.jpg', 'autor' => 'Samuel Quek', 'perfil' => '',
		'titulo' => 'Mountain river with the Fitz Roy peaks behind',
		'alt' => [
			'es' => 'Río de montaña de aguas claras y rápidos entre arbustos verdes, con las cumbres nevadas del cerro Fitz Roy al fondo y cielo azul',
			'en' => 'Clear mountain river with rapids between green shrubs, and the snow-covered peaks of Mount Fitz Roy behind under a blue sky',
			'pt' => 'Rio de montanha de águas claras e corredeiras entre arbustos verdes, com os picos nevados do cerro Fitz Roy ao fundo e céu azul',
		],
	],
	'paquetes-de-pesca-argentina' => [
		'tipo' => 'unsplash', 'archivo' => 'pescador-con-mosca-vadeando-torso.jpg', 'autor' => 'Gaspar Zaldo', 'perfil' => '',
		'titulo' => 'Fly fisherman wading, seen from the chest down',
		'alt' => [
			'es' => 'Pescador con mosca dentro del agua, visto del pecho para abajo (sin la cara), con waders grises, la caña y la línea en las manos y un bosque otoñal detrás',
			'en' => 'Fly fisherman standing in the water, seen from the chest down (no face), in grey waders with the rod and line in his hands and an autumn forest behind',
			'pt' => 'Pescador com mosca dentro da água, visto do peito para baixo (sem o rosto), com waders cinza, a vara e a linha nas mãos e uma floresta de outono atrás',
		],
	],
	'reglas-equipo-patagonia' => [
		'tipo' => 'unsplash', 'archivo' => 'pescador-vadeando-lago-rio-negro.jpg', 'autor' => 'Agustín Gorordo Coggiola', 'perfil' => 'https://unsplash.com/es/@instaagus',
		'titulo' => 'Angler wading into a lake in Río Negro, seen from behind',
		'alt' => [
			'es' => 'Pescador de espaldas, metido en el agua hasta las rodillas en un lago de Río Negro, con una colina de bosque enfrente y el cielo cubierto',
			'en' => 'Angler seen from behind, knee-deep in a lake in Río Negro, with a forested hill ahead and an overcast sky',
			'pt' => 'Pescador de costas, com a água até os joelhos em um lago de Río Negro, com uma colina de floresta à frente e o céu nublado',
		],
	],
];
$comm_alt = [
	'viaje-dorado-amigos-precios' => [
		'titulo' => 'Fishing boat moored on the Paraná at sunset, Corrientes',
		'es' => 'Embarcación de pesca amarrada sobre el río Paraná al atardecer, con el cielo anaranjado y un puente largo al fondo (Corrientes)',
		'en' => 'Fishing boat moored on the Paraná River at sunset, with an orange sky and a long bridge in the background (Corrientes)',
		'pt' => 'Embarcação de pesca ancorada no rio Paraná ao entardecer, com o céu alaranjado e uma ponte longa ao fundo (Corrientes)',
	],
	'precio-viaje-dorado-lodge' => [
		'titulo' => 'Sunrise over the Paraná River, Corrientes',
		'es' => 'Amanecer sobre el río Paraná en Corrientes: cielo cargado de nubes con tonos rojizos y el sol asomando sobre el agua en el horizonte',
		'en' => 'Sunrise over the Paraná River in Corrientes: a cloudy sky with reddish tones and the sun appearing over the water on the horizon',
		'pt' => 'Amanhecer sobre o rio Paraná em Corrientes: céu carregado de nuvens com tons avermelhados e o sol surgindo sobre a água no horizonte',
	],
	'precio-pesca-bariloche-limay' => [
		'titulo' => 'The Limay River between rock walls, with the orange bow of a boat',
		'es' => 'Aguas azul turquesa del río Limay entre barrancas de roca y arbustos verdes, con la proa naranja de un bote en primer plano',
		'en' => 'Turquoise water of the Limay River between rock walls and green shrubs, with the orange bow of a boat in the foreground',
		'pt' => 'Águas azul-turquesa do rio Limay entre barrancos de rocha e arbustos verdes, com a proa laranja de um bote em primeiro plano',
	],
	'precio-semana-trucha-patagonia-norte' => [
		'titulo' => 'Clear stretch of the Chimehuín River with a gravel bank',
		'es' => 'Tramo de aguas transparentes del río Chimehuín, con una orilla de grava, arbustos verdes y una persona a lo lejos sobre la costa',
		'en' => 'Clear-water stretch of the Chimehuín River, with a gravel bank, green shrubs and a person far off on the shore',
		'pt' => 'Trecho de águas transparentes do rio Chimehuín, com uma margem de cascalho, arbustos verdes e uma pessoa ao longe na margem',
	],
	'licencia-pesca-patagonia-precios' => [
		'titulo' => 'Fly fisherman wading at the mouth of the Chimehuín River',
		'es' => 'Pescador con mosca metido en el agua, visto de lejos, en la boca del río Chimehuín, con una ladera de monte detrás',
		'en' => 'Fly fisherman wading in the water, seen from afar, at the mouth of the Chimehuín River, with a scrubby hillside behind',
		'pt' => 'Pescador com mosca dentro da água, visto de longe, na foz do rio Chimehuín, com uma encosta de mato atrás',
	],
	'pesca-desde-brasil-corrientes' => [
		'titulo' => 'The Uruguay River from Paso de los Libres, with a moored boat and the international bridge',
		'es' => 'El río Uruguay desde Paso de los Libres: una lancha blanca amarrada junto al muro de piedra y, al fondo, el puente internacional hacia Brasil',
		'en' => 'The Uruguay River from Paso de los Libres: a white boat moored beside the stone wall and, in the distance, the international bridge to Brazil',
		'pt' => 'O rio Uruguai desde Paso de los Libres: uma lancha branca amarrada junto ao muro de pedra e, ao fundo, a ponte internacional para o Brasil',
	],
	'paquete-incluye-no-incluye' => [
		'titulo' => 'Small boat with two people against the sunset on the Paraná',
		'es' => 'Bote con dos personas a contraluz sobre el río Paraná al atardecer, con el cielo anaranjado y el sol sobre el horizonte',
		'en' => 'Small boat with two people in silhouette on the Paraná River at sunset, with an orange sky and the sun on the horizon',
		'pt' => 'Barco pequeno com duas pessoas em contraluz no rio Paraná ao entardecer, com o céu alaranjado e o sol no horizonte',
	],
	'licencias-extranjeros-argentina' => [
		'titulo' => 'Angler casting at dawn on the Paraná, in silhouette',
		'es' => 'Silueta de un pescador lanzando la caña al amanecer sobre el río Paraná, con el cielo violeta y anaranjado',
		'en' => 'Silhouette of an angler casting his rod at dawn on the Paraná River, with a violet and orange sky',
		'pt' => 'Silhueta de um pescador lançando a vara ao amanhecer no rio Paraná, com o céu violeta e alaranjado',
	],
];
foreach ( $comm_alt as $clave => $t ) {
	$c = $comm[ $clave ] ?? null;
	if ( ! $c ) {
		WP_CLI::error( "falta $clave en fotos-wp-notas.json" );
	}
	$fotos[ $clave ] = [
		'tipo' => 'commons', 'archivo' => $c['archivo_local'], 'autor' => $c['autor'], 'perfil' => $c['pagina'], 'titulo' => $t['titulo'],
		'alt' => [ 'es' => $t['es'], 'en' => $t['en'], 'pt' => $t['pt'] ], 'c' => $c,
	];
}

function pm_fn_nota( string $clave, string $idioma ): ?WP_Post {
	foreach ( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		if ( pm_idioma_de( $p->ID ) === $idioma ) {
			return $p;
		}
	}
	return null;
}

$ok_todo = true;
foreach ( $fotos as $clave => $f ) {
	$es = pm_fn_nota( $clave, 'es' );
	if ( ! $es ) {
		WP_CLI::error( "No encontré la nota $clave (es)." );
	}
	$antes = (int) get_post_thumbnail_id( $es->ID );
	if ( $antes && wp_attachment_is_image( $antes ) ) {
		WP_CLI::log( "OMITE: $clave (es) #{$es->ID} ya tiene la foto #$antes; no se reemplaza" );
		continue;
	}
	$ruta = "$carpeta/{$f['archivo']}";
	if ( ! is_file( $ruta ) ) {
		WP_CLI::error( "falta {$f['archivo']} en $carpeta" );
	}
	$dim = @getimagesize( $ruta );
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave (es) #{$es->ID}: {$f['archivo']} " . ( $dim ? "{$dim[0]}x{$dim[1]}" : '(¡no se lee como imagen!)' ) . ' ' . size_format( filesize( $ruta ) ) . " | autor={$f['autor']} | {$f['tipo']}" . ( 'commons' === $f['tipo'] ? " | {$f['c']['licencia']} | {$f['c']['licencia_url']} | {$f['perfil']} | leída {$f['c']['leida']}" : " | perfil=" . ( $f['perfil'] ?: '(sin)' ) ) );
	if ( ! $dim || $dim[0] > 1600 || abs( $dim[0] / $dim[1] - 4 / 3 ) > 0.03 ) {
		WP_CLI::error( "$clave: la foto no es 4:3 de hasta 1600 px" );
	}
	if ( ! $aplicar ) {
		continue;
	}
	$ya  = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => $f['archivo'], 'numberposts' => 1, 'fields' => 'ids' ] );
	$att = $ya ? (int) $ya[0] : 0;
	if ( ! $att ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$tmp = wp_tempnam( $f['archivo'] );
		copy( $ruta, $tmp );
		$att = media_handle_sideload( [ 'name' => $f['archivo'], 'tmp_name' => $tmp ], $es->ID, $f['titulo'] );
		if ( is_wp_error( $att ) ) {
			@unlink( $tmp );
			WP_CLI::error( $att->get_error_message() );
		}
		update_post_meta( $att, '_pm_archivo_origen', $f['archivo'] );
	}
	update_post_meta( $att, '_wp_attachment_image_alt', $f['alt']['es'] );
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		update_post_meta( $att, "_pm_alt_$i", $f['alt'][ $i ] );
	}
	update_field( 'foto_autor', $f['autor'], $att );
	if ( 'unsplash' === $f['tipo'] ) {
		update_field( 'foto_origen', 'unsplash', $att );
		update_field( 'foto_licencia', 'Licencia Unsplash', $att );
		update_field( 'foto_licencia_url', $unsplash_lic, $att );
		update_field( 'foto_pagina', $f['perfil'], $att ); // the author's profile, only where the owner gave it
		wp_update_post( [ 'ID' => $att, 'post_content' => "Foto del dueño, bajada de Unsplash el $hoy (autor: {$f['autor']}). Licencia Unsplash: $unsplash_lic. No hay URL de la página de la foto." ] );
	} else {
		update_field( 'foto_origen', 'licencia', $att );
		update_field( 'foto_licencia', $f['c']['licencia'], $att );
		update_field( 'foto_licencia_url', $f['c']['licencia_url'], $att );
		update_field( 'foto_pagina', $f['c']['pagina'], $att );
		wp_update_post( [ 'ID' => $att, 'post_content' => "FOTO PROVISORIA ($hoy): {$f['c']['archivo']} de Wikimedia Commons, leída {$f['c']['leida']}, recortada al centro en 4:3. El dueño la reemplazará por una mejor. {$f['c']['pagina']}" ] );
	}
	set_post_thumbnail( $es->ID, $att );

	// Read back: es has it, en and pt inherit it by key, credit and alt render in each language.
	$ok = (int) get_post_thumbnail_id( $es->ID ) === $att && wp_attachment_is_image( $att ) && pm_campo( 'foto_autor', $att ) === $f['autor'];
	$partes = [];
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$p = pm_fn_nota( $clave, $i );
		if ( ! $p ) {
			$partes[ $i ] = 'sin versión';
			continue;
		}
		$n    = pm_nota( $p->ID );
		$html = pm_foto( $att, pm_alt_foto( $att, $i, $n['titulo'] ), '400px' );
		$bien = (int) $n['foto'] === $att && false !== strpos( $html, 'alt="' . esc_attr( $f['alt'][ $i ] ) . '"' ) && false !== strpos( $html, '<figcaption>' ) && false !== strpos( $html, esc_html( $f['autor'] ) );
		if ( 'unsplash' === $f['tipo'] ) {
			$bien = $bien && false !== strpos( $html, '>Unsplash</a>' ) && false !== strpos( $html, $unsplash_lic );
		} else {
			$bien = $bien && false !== strpos( $html, esc_html( $f['c']['licencia'] ) );
		}
		$partes[ $i ] = $bien ? 'ok' : 'NO';
		$ok = $ok && $bien;
	}
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": foto $clave #$att ({$f['archivo']}) es/en/pt=" . wp_json_encode( $partes ) . ' ' . wp_get_attachment_url( $att ) . ' nota: ' . get_permalink( $es->ID ) );
	$ok_todo = $ok_todo && $ok;
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
$ok_todo || WP_CLI::error( 'alguna foto no quedó.' );
WP_CLI::log( 'APLICADO' );
