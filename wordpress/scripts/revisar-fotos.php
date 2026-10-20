<?php
/**
 * Monthly photo review (revisar-fotos.yml): prints a Markdown report of the
 * photos worth a look. Read-only; the team decides and fixes in wp-admin.
 * Checks what affects how the site looks and loads, not licensing paperwork:
 *   - cover photos that are vertical (they get cropped) or small (< 1200 px),
 *   - photos without an author (no credit line on the site; not blocking),
 *   - non-descriptive file names (IMG_1234, WhatsApp Image…): renaming the
 *     title helps image search a little,
 *   - heavy display versions (> 350 KB),
 *   - images in a guide's text without alt text,
 *   - photos nobody uses (candidates to delete).
 */

$usos = []; // attachment id => [ "Goya (portada)", … ]
$usar = function ( int $att, string $donde ) use ( &$usos ) {
	if ( $att ) {
		$usos[ $att ][] = $donde;
	}
};
$sin_alt = [];
foreach ( [ 'pm_destino' => 'guía', 'pm_proximo' => 'próximo', 'pm_entrevista' => 'entrevista', 'pm_prestador' => 'prestador', 'pm_especie' => 'especie' ] as $tipo => $nombre ) {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => [ 'publish', 'draft' ], 'numberposts' => -1 ] ) as $p ) {
		$titulo = get_the_title( $p ) . ( 'publish' === $p->post_status ? '' : ' [borrador]' );
		$usar( (int) get_post_thumbnail_id( $p->ID ), "$titulo ($nombre, portada)" );
		if ( 'pm_prestador' === $tipo ) {
			foreach ( (array) get_post_meta( $p->ID, 'fotos', true ) as $g ) {
				$usar( (int) $g, "$titulo (galería)" );
			}
		}
		if ( preg_match_all( '/<img [^>]*>/', $p->post_content, $m ) ) {
			foreach ( $m[0] as $img ) {
				preg_match( '/wp-image-(\d+)/', $img, $id );
				$usar( (int) ( $id[1] ?? 0 ), "$titulo (en el texto)" );
				if ( ! preg_match( '/alt="[^"]+"/', $img ) ) {
					$sin_alt[] = "$titulo: imagen sin texto alternativo";
				}
			}
		}
	}
}

$filas = [];
$sin_uso = [];
foreach ( get_posts( [ 'post_type' => 'attachment', 'post_mime_type' => 'image', 'numberposts' => -1, 'post_status' => 'inherit' ] ) as $a ) {
	$id     = $a->ID;
	$meta   = wp_get_attachment_metadata( $id ) ?: [];
	$w      = (int) ( $meta['width'] ?? 0 );
	$h      = (int) ( $meta['height'] ?? 0 );
	$enlace = '[' . esc_html( get_the_title( $id ) ) . '](' . admin_url( "post.php?post=$id&action=edit" ) . ')';
	if ( empty( $usos[ $id ] ) ) {
		$sin_uso[] = $enlace;
		continue;
	}
	$portada = (bool) array_filter( $usos[ $id ], fn( $u ) => str_contains( $u, 'portada' ) );
	$notas   = [];
	if ( $portada && $w && $h > $w ) {
		$notas[] = "vertical ({$w}×{$h}): en portada se recorta, mejor una horizontal";
	}
	if ( $portada && $w && $w < 1200 ) {
		$notas[] = "chica ({$w} px de ancho): se ve borrosa en pantallas grandes";
	}
	if ( ! get_post_meta( $id, 'foto_autor', true ) ) {
		$notas[] = 'sin autor (no sale el crédito)';
	}
	$archivo = wp_basename( (string) get_attached_file( $id ) );
	if ( preg_match( '/^(img|dsc|pxl|mvimg|whatsapp|image|imagen|foto|photo|screenshot|captura|signal)[-_ ]?|^[\d_-]+\./i', $archivo ) ) {
		$notas[] = "nombre poco descriptivo ($archivo): poné un título que diga qué es y dónde";
	}
	$large = image_get_intermediate_size( $id, 'large' );
	$ruta  = $large ? path_join( dirname( (string) get_attached_file( $id ) ), $large['file'] ) : (string) get_attached_file( $id );
	if ( is_file( $ruta ) && filesize( $ruta ) > 350 * 1024 ) {
		$notas[] = 'pesada (' . size_format( filesize( $ruta ) ) . ' en la versión que se muestra)';
	}
	if ( $notas ) {
		$filas[] = "| $enlace | " . esc_html( implode( '; ', array_unique( $usos[ $id ] ) ) ) . ' | ' . implode( '<br>', $notas ) . ' |';
	}
}

echo '## Revisión de fotos — ' . wp_date( 'j/n/Y' ) . "\n\n";
echo "Nada de esto bloquea la web: es una lista para revisar y corregir lo que valga la pena.\n\n";
if ( $filas ) {
	echo "### Para mirar\n\n| Foto | Dónde se usa | Qué conviene |\n| --- | --- | --- |\n" . implode( "\n", $filas ) . "\n\n";
} else {
	echo "✅ Todas las fotos en uso están bien.\n\n";
}
if ( $sin_alt ) {
	echo "### Imágenes en el texto sin descripción\n\n- " . implode( "\n- ", array_unique( $sin_alt ) ) . "\n\n";
}
if ( $sin_uso ) {
	echo '### Sin usar (' . count( $sin_uso ) . ")\n\nSe pueden borrar desde Fotos si no hacen falta: " . implode( ', ', $sin_uso ) . "\n";
}
