<?php
// Fixtures for the trip-preparation notes test (16/11/2026): the guides, notes and species pages that wordpress/scripts/semana-lodge-2026-11-16.php,
// preparar-viaje-hub-2026-11-16.php and saptdf-correccion-2026-11-16.php link to or edit, in es/en/pt, all marked with the meta _prueba_vn so the step can remove them.
// Plus generated cover photos on the two notes whose photo the scripts reuse, and the OLD texts the SAPTDF correction replaces (licence hub, Río Grande guide).
require_once ABSPATH . 'wp-admin/includes/image.php';
$guias = [
	'san-martin-junin' => [ 'patagonia', 'neuquen', -40.15, -71.35 ], 'bariloche' => [ 'patagonia', 'rio-negro', -41.13, -71.31 ], 'esquel-trevelin' => [ 'patagonia', 'chubut', -42.91, -71.32 ],
	'jurassic-lake' => [ 'patagonia', 'santa-cruz', -48.37, -71.2 ], 'rio-santa-cruz' => [ 'patagonia', 'santa-cruz', -49.98, -68.91 ], 'rio-gallegos' => [ 'patagonia', 'santa-cruz', -51.62, -69.22 ],
	'rio-grande' => [ 'patagonia', 'tierra-del-fuego', -53.79, -67.71 ],
	'itati' => [ 'litoral', 'corrientes', -27.27, -58.24 ], 'ita-ibate' => [ 'litoral', 'corrientes', -27.4, -56.7 ], 'esteros-del-ibera' => [ 'litoral', 'corrientes', -28.5, -57.2 ],
	'goya' => [ 'litoral', 'corrientes', -29.14, -59.26 ], 'esquina' => [ 'litoral', 'corrientes', -30.0, -59.53 ], 'paso-de-la-patria' => [ 'litoral', 'corrientes', -27.32, -58.57 ],
];
$notas = [
	'precio-semana-trucha-patagonia-norte', 'precio-viaje-dorado-lodge', 'paquete-incluye-no-incluye', 'cuanto-cuesta-viaje-pesca-argentina', 'permiso-pesca-argentina-paso-a-paso',
	'precio-pesca-patagonia-sur', 'golden-dorado-guia', 'trucha-patagonia-pilar', 'cuando-pescar-dorado', 'cuando-pescar-trucha-patagonia', 'reglas-equipo-patagonia', 'licencias-extranjeros-argentina',
];
$corta = [ 'es' => '<strong>Respuesta corta.</strong> Texto de prueba.', 'en' => '<strong>Short answer.</strong> Test text.', 'pt' => '<strong>Resposta curta.</strong> Texto de teste.' ];
$p = fn( string $t ) => "<!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph -->";
$li = fn( string $t ) => "<!-- wp:list -->\n<ul class=\"wp-block-list\"><!-- wp:list-item --><li>{$t}</li><!-- /wp:list-item --></ul>\n<!-- /wp:list -->";
// The texts the SAPTDF correction replaces, exactly as the live pages carry them.
$saptdf_hub = [
	'es' => 'Para entrar al río Grande y sus afluentes hace falta estar registrado y tener una reserva previa en el sistema de accesos saptdf.ar; el río está habilitado del 30 de octubre de 2026 al 18 de abril de 2027.',
	'en' => 'To access the Grande River and its tributaries, you must be registered and have a prior reservation in the saptdf.ar access system; the river is open from 30 October 2026 to 18 April 2027.',
	'pt' => 'Para acessar o rio Grande e seus afluentes é necessário estar registrado e ter uma reserva prévia no sistema de acessos saptdf.ar; o rio está aberto de 30 de outubro de 2026 a 18 de abril de 2027.',
];
$saptdf_guia = [
	'es' => 'Quien reserva y no avisa con 24 horas de anticipación puede ser suspendido del sistema.',
	'en' => 'Booking and not cancelling 24 hours ahead can get you suspended from the system.',
	'pt' => 'Quem reserva e não avisa com 24 horas de antecedência pode ser suspenso do sistema.',
];
$saptdf_aguas = [
	'es' => 'Devolución obligatoria de todos los salmónidos; acceso con reserva en el SAPTDF; hasta el 18 de abril de 2027.',
	'en' => 'Mandatory release of all salmonids; access by booking on SAPTDF; until 18 April 2027.',
	'pt' => 'Devolução obrigatória de todos os salmonídeos; acesso com reserva no SAPTDF; até 18 de abril de 2027.',
];

function pm_vn_foto( string $nombre, array $rgb, string $autor ): int {
	$png = wp_upload_bits( $nombre . '.png', null, '' );
	$im  = imagecreatetruecolor( 400, 300 );
	imagefill( $im, 0, 0, imagecolorallocate( $im, ...$rgb ) );
	imagepng( $im, $png['file'] );
	$att = wp_insert_attachment( [ 'post_mime_type' => 'image/png', 'post_title' => $nombre, 'post_status' => 'inherit' ], $png['file'] );
	wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $png['file'] ) );
	update_field( 'foto_autor', $autor, $att );
	update_post_meta( $att, '_prueba_vn', 1 );
	return $att;
}
$att_semana = pm_vn_foto( 'portada-vn-semana', [ 200, 120, 40 ], 'AUTOR-PORTADA-SEMANA' );
$att_hub    = pm_vn_foto( 'portada-vn-hub', [ 40, 100, 140 ], 'AUTOR-PORTADA-HUB' );

foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	foreach ( $guias as $c => [ $reg, $prov, $lat, $lon ] ) {
		$cuerpo = 'rio-grande' === $c ? $li( 'Los pescadores registrados en el <strong>SAPTDF</strong> y con reserva previa acceden al sector. ' . $saptdf_guia[ $i ] ) : 'Texto.';
		$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => "Guía $c $i", 'post_name' => "prueba-vn-$c-$i", 'post_content' => $cuerpo ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $c, $id );
		update_post_meta( $id, '_prueba_vn', 1 );
		if ( 'rio-grande' === $c ) {
			update_field( 'aguas', [ [ 'nombre' => 'Río Grande', 'nota' => $saptdf_aguas[ $i ], 'lat' => '', 'lon' => '' ], [ 'nombre' => 'Río Claro', 'nota' => 'Otra nota.', 'lat' => '', 'lon' => '' ] ], $id );
		}
		if ( 'es' === $i ) {
			update_field( 'region', $reg, $id ); update_field( 'provincia', $prov, $id ); update_field( 'lat', $lat, $id ); update_field( 'lon', $lon, $id ); update_field( 'especies', [ 'itati' === $c || 'ita-ibate' === $c || 'esteros-del-ibera' === $c || 'goya' === $c || 'esquina' === $c || 'paso-de-la-patria' === $c ? 'dorado' : 'trucha' ], $id );
		}
	}
	foreach ( $notas as $c ) {
		$cuerpo = $p( $corta[ $i ] ) . "\n\n" . $p( 'Segundo párrafo.' );
		if ( 'licencias-extranjeros-argentina' === $c ) { $cuerpo .= "\n\n" . $p( 'Cómo se tramita: texto previo. ' . $saptdf_hub[ $i ] ); }
		$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => "Nota $c $i", 'post_name' => "prueba-vn-$c-$i", 'post_content' => $cuerpo ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $c, $id );
		update_post_meta( $id, '_prueba_vn', 1 );
		if ( 'es' === $i ) {
			update_field( 'nota_fecha', '20261026', $id );
			if ( 'paquete-incluye-no-incluye' === $c ) { set_post_thumbnail( $id, $att_semana ); }
			if ( 'precio-viaje-dorado-lodge' === $c ) { set_post_thumbnail( $id, $att_hub ); }
		}
	}
	foreach ( [ 'dorado', 'trucha' ] as $e ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_especie', 'post_status' => 'publish', 'post_title' => "$e $i", 'post_name' => "prueba-vn-$e-$i", 'post_content' => $p( 'Datos.' ) ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $e, $id );
		update_post_meta( $id, '_prueba_vn', 1 );
	}
}
WP_CLI::log( "fixtures de las notas de preparación del viaje creados (fotos #$att_semana y #$att_hub)" );
