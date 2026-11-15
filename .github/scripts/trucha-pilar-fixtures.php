<?php
// Fixtures for the trout pillar note test (15/11/2026): the guides, notes and species page that wordpress/scripts/nota-trucha-patagonia-pilar-2026-11-15.php
// links to or edits, in es/en/pt, all marked with the meta _prueba_tp so the step can remove them. Plus a generated cover photo on the note whose photo the script reuses.
require_once ABSPATH . 'wp-admin/includes/image.php';
$guias = [
	'san-martin-junin' => [ 'neuquen', -40.15, -71.35 ], 'bariloche' => [ 'rio-negro', -41.13, -71.31 ], 'esquel-trevelin' => [ 'chubut', -42.91, -71.32 ],
	'jurassic-lake' => [ 'santa-cruz', -48.37, -71.2 ], 'rio-santa-cruz' => [ 'santa-cruz', -49.98, -68.91 ], 'rio-gallegos' => [ 'santa-cruz', -51.62, -69.22 ],
	'rio-grande' => [ 'tierra-del-fuego', -53.79, -67.71 ],
];
$notas = [ 'cuando-pescar-trucha-patagonia', 'licencias-extranjeros-argentina', 'licencia-pesca-patagonia-precios', 'reglas-equipo-patagonia', 'precio-semana-trucha-patagonia-norte', 'precio-pesca-bariloche-limay', 'precio-pesca-patagonia-sur' ];
$ancla = [ 'es' => 'La temporada abre en noviembre: mirá cuándo pescar trucha en la Patagonia.', 'en' => 'The season opens in November: see when to fish trout in Patagonia.', 'pt' => 'A temporada abre em novembro: veja quando pescar truta na Patagônia.' ];
$corta = [ 'es' => '<strong>Respuesta corta.</strong> Texto de prueba.', 'en' => '<strong>Short answer.</strong> Test text.', 'pt' => '<strong>Resposta curta.</strong> Texto de teste.' ];
$p = fn( string $t ) => "<!-- wp:paragraph -->\n<p>{$t}</p>\n<!-- /wp:paragraph -->";

// Cover photo with an author, on the note whose photo the script reuses.
$png = wp_upload_bits( 'portada-tp.png', null, '' );
$im  = imagecreatetruecolor( 400, 300 );
imagefill( $im, 0, 0, imagecolorallocate( $im, 40, 100, 140 ) );
imagepng( $im, $png['file'] );
$att = wp_insert_attachment( [ 'post_mime_type' => 'image/png', 'post_title' => 'portada-tp', 'post_status' => 'inherit' ], $png['file'] );
wp_update_attachment_metadata( $att, wp_generate_attachment_metadata( $att, $png['file'] ) );
update_field( 'foto_autor', 'AUTOR-PORTADA-TP', $att );
update_post_meta( $att, '_prueba_tp', 1 );

foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	foreach ( $guias as $c => [ $prov, $lat, $lon ] ) {
		$id = wp_insert_post( [ 'post_type' => 'pm_destino', 'post_status' => 'publish', 'post_title' => "Guía $c $i", 'post_name' => "prueba-tp-$c-$i", 'post_content' => 'Texto.' ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $c, $id );
		update_post_meta( $id, '_prueba_tp', 1 );
		if ( 'es' === $i ) {
			update_field( 'region', 'patagonia', $id ); update_field( 'provincia', $prov, $id ); update_field( 'lat', $lat, $id ); update_field( 'lon', $lon, $id ); update_field( 'especies', [ 'trucha' ], $id );
		}
	}
	foreach ( $notas as $c ) {
		$cuerpo = 'cuando-pescar-trucha-patagonia' === $c ? $p( $corta[ $i ] ) . "\n\n" . $p( 'Segundo párrafo.' ) : $p( 'Nota de prueba' );
		$id = wp_insert_post( [ 'post_type' => 'pm_nota', 'post_status' => 'publish', 'post_title' => "Nota $c $i", 'post_name' => "prueba-tp-$c-$i", 'post_content' => $cuerpo ], true );
		update_field( 'idioma', $i, $id );
		update_field( 'clave', $c, $id );
		update_post_meta( $id, '_prueba_tp', 1 );
		if ( 'es' === $i ) {
			update_field( 'nota_fecha', '20261026', $id );
			if ( 'precio-semana-trucha-patagonia-norte' === $c ) { set_post_thumbnail( $id, $att ); }
		}
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_especie', 'post_status' => 'publish', 'post_title' => "Trucha $i", 'post_name' => "prueba-tp-trucha-$i", 'post_content' => $p( 'Datos.' ) . "\n\n" . $p( $ancla[ $i ] ) . "\n\n" . $p( 'Cierre.' ) ], true );
	update_field( 'idioma', $i, $id );
	update_field( 'clave', 'trucha', $id );
	update_post_meta( $id, '_prueba_tp', 1 );
}
WP_CLI::log( 'fixtures de la nota pilar de la trucha creados (foto #' . $att . ')' );
