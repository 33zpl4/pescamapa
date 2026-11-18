<?php
/**
 * Alt text of the featured photos (owner approved, 18/11/2026). pm_alt_foto() reads the attachment meta _pm_alt_<es|en|pt>;
 * the guide/home/province/note templates now use it, with the old text ("Nombre, Provincia") as the fallback.
 *
 * What it does, one run (simulacro first, then aplicar=true):
 *  1. LISTS every featured photo of pm_destino (guías), pm_nota (notas), pm_especie (especies) and pm_proximo (próximos),
 *     with the posts that use it, file name, attachment title/description, _wp_attachment_image_alt, _pm_alt_es/en/pt and credit.
 *  2. COMPLETES _pm_alt_es/en/pt only where EMPTY, and only for photos whose file is in $textos below (written from what the photo
 *     shows: the repo's loading scripts and the photo's own description; 8 to 16 words; place or species only when true of that photo;
 *     no "foto de", no brands, no keywords stuffing). An existing alt is NEVER overwritten. _wp_attachment_image_alt is set to the
 *     Spanish text only when it is empty.
 *  3. A photo whose file is not in $textos is NOT completed: it is listed under "SIN BASE" with its data, so a person says what it shows.
 * Idempotent. Dry run by default ($aplicar = false; wp-script.yml aplicar=true flips it in the server copy). Writes only post meta.
 */

$aplicar = false;

// File name without extension (and without -scaled / -1 suffixes) => alt es/en/pt.
$textos = [
	'esquel-trevelin-pescador-moscas' => [
		'es' => 'Pescador con gorra abre una caja de moscas junto a un río claro y montañas nevadas',
		'en' => 'A fisherman in a cap opens a fly box by a clear river and snowy mountains',
		'pt' => 'Pescador de boné abre uma caixa de moscas junto a um rio claro e montanhas nevadas',
	],
	'paso-de-la-patria-dorado-saltando' => [
		'es' => 'Un dorado salta fuera del agua verde oscuro entre salpicaduras, con la boca abierta',
		'en' => 'A golden dorado leaps out of dark green water in a burst of spray, mouth open',
		'pt' => 'Um dourado salta para fora da água verde-escura entre respingos, com a boca aberta',
	],
	'limay-pescador-bote' => [
		'es' => 'Pescador en un bote verde, con la caña doblada, sobre un río calmo y colinas',
		'en' => 'An angler in a green boat, rod bent, plays a fish on a calm river',
		'pt' => 'Pescador em um bote verde, com a vara curvada, em um rio calmo sob colinas',
	],
	'las-buitreras-rio-gallegos-paisaje' => [
		'es' => 'Un río serpentea entre pampa dorada bajo un cielo suave, con una caña adelante',
		'en' => 'A river winds through golden pampa under a soft sky, with a fly rod in front',
		'pt' => 'Um rio serpenteia entre pampa dourada sob um céu suave, com uma vara na frente',
	],
	'bariloche-pescador-rio' => [
		'es' => 'Pescador con mosca, con waders y sombrero de ala ancha, lanza en un río calmo',
		'en' => 'A fly angler in waders and a wide-brimmed hat casts in a calm river',
		'pt' => 'Pescador com mosca, de waders e chapéu de aba larga, lança em um rio calmo',
	],
	'jurassic-lake-pescador-oleaje' => [
		'es' => 'Pescador de pie entre salpicaduras del viento, en la orilla de un lago turquesa',
		'en' => 'An angler stands in wind-blown spray at the edge of a turquoise lake',
		'pt' => 'Pescador de pé entre respingos do vento, na margem de um lago turquesa',
	],
	'junin-pescador-volcan' => [
		'es' => 'Pescador con mosca lanza en un río claro, con un volcán nevado y árboles detrás',
		'en' => 'A fly angler in waders casts in a clear river, with a snowy volcano behind',
		'pt' => 'Pescador com mosca lança em um rio claro, com um vulcão nevado e árvores atrás',
	],
	'rio-grande-pescador-atardecer' => [
		'es' => 'Pescador en aguas bajas de un río ancho al atardecer, con la caña en arco',
		'en' => 'An angler in the shallows of a wide river at sunset, rod bent in an arc',
		'pt' => 'Pescador em águas rasas de um rio largo ao entardecer, com a vara em arco',
	],
	'rio-gallegos-trucha-de-mar' => [
		'es' => 'Pescador sonriente arrodillado en aguas bajas sostiene una trucha marrón de mar plateada y grande',
		'en' => 'A smiling angler kneels in shallow water holding a large silver sea-run brown trout',
		'pt' => 'Pescador sorridente ajoelhado em águas rasas segura uma grande truta marrom prateada de mar',
	],
	'la-paz-atardecer-canoa' => [
		'es' => 'Personas reunidas junto a una canoa en la orilla de un río ancho, al atardecer',
		'en' => 'People gather around a canoe on the shore of a wide river at sunset',
		'pt' => 'Pessoas reunidas junto a uma canoa na margem de um rio largo, ao entardecer',
	],
	'dorado-saltando-con-senuelo' => [
		'es' => 'Un dorado salta fuera del agua con un señuelo en la boca, levantando gotas',
		'en' => 'A golden dorado leaps out of the water with a lure in its mouth, spraying droplets',
		'pt' => 'Um dourado salta para fora da água com uma isca na boca, levantando gotas',
	],
	'pescador-silueta-al-atardecer' => [
		'es' => 'Silueta de un pescador con una caña de spinning contra un cielo violeta al atardecer',
		'en' => 'Silhouette of an angler with a spinning rod against a violet sky at sunset',
		'pt' => 'Silhueta de um pescador com uma vara de spinning contra um céu violeta ao entardecer',
	],
	'dorado-tres-pescadores' => [
		'es' => 'Tres pescadores sonríen a bordo de una embarcación con un dorado grande entre los tres',
		'en' => 'Three anglers smile on board a boat with a large golden dorado between them',
		'pt' => 'Três pescadores sorriem a bordo de uma embarcação com um grande dourado entre os três',
	],
	'surubi-pescador-a-bordo' => [
		'es' => 'Un pescador sonríe a bordo con un surubí atigrado en las manos, con camalotes al fondo',
		'en' => 'An angler smiles on board holding a tiger-striped surubí, with floating water plants behind',
		'pt' => 'Um pescador sorri a bordo com um surubi pintado nas mãos, com aguapés ao fundo',
	],
	'pescador-con-mosca-en-un-lago' => [
		'es' => 'Pescador con mosca, con el agua a la cintura, en un lago con bosque y colinas',
		'en' => 'A fly angler waist-deep in a lake, with forest and hills behind him',
		'pt' => 'Pescador com mosca, com a água na cintura, em um lago com floresta e colinas',
	],
	'concordia-pescador-atardecer' => [
		'es' => 'Pescador con caña al atardecer sobre el agua',
		'en' => 'Angler with a rod at sunset over the water',
		'pt' => 'Pescador com vara ao entardecer sobre a água',
	],
	'commons-el-calafate' => [
		'es' => 'Pastizal húmedo con dos caballos que pastan, el lago Argentino y montañas a lo lejos',
		'en' => 'Green wet pasture with two horses grazing, Lago Argentino and a line of mountains beyond',
		'pt' => 'Pastagem úmida e verde com dois cavalos pastando, o lago Argentino e montanhas ao longe',
	],
	'commons-ushuaia' => [
		'es' => 'Veleros amarrados en una bahía calma, con el pueblo en la costa y montañas nevadas',
		'en' => 'Sailboats moored in a calm bay, with the town along the shore and snow-dusted mountains behind',
		'pt' => 'Veleiros ancorados em uma baía calma, com a cidade na costa e montanhas com neve atrás',
	],
	'commons-itati' => [
		'es' => 'El ancho río Paraná, de aguas marrones, visto desde la orilla, con un sauce',
		'en' => 'The wide, brown Paraná River seen from the shore, with a willow and a small island',
		'pt' => 'O largo rio Paraná, de águas marrons, visto da margem, com um salgueiro e uma ilha',
	],
	'commons-esteros-del-ibera' => [
		'es' => 'El sol se pone sobre el agua calma de un humedal, con juncos y libélulas',
		'en' => 'The sun sets over the calm water of a wetland, with reeds and dragonflies',
		'pt' => 'O sol se põe sobre a água calma de um banhado, com juncos e libélulas',
	],
	'commons-rio-santa-cruz' => [
		'es' => 'Un río ancho que se divide alrededor de islas verdes bajo un cielo nublado de estepa',
		'en' => 'A wide river splitting around green islands under a cloudy steppe sky',
		'pt' => 'Um rio largo que se divide em torno de ilhas verdes sob um céu nublado',
	],
	'commons-ita-ibate' => [
		'es' => 'Dos pilares decorados a la entrada de un pueblo, junto a una ruta, bajo cielo despejado',
		'en' => 'Two decorated pillars at a town entrance beside a road under a clear sky',
		'pt' => 'Dois pilares decorados na entrada de uma cidade, junto a uma estrada, sob céu limpo',
	],
	// Photos loaded from Commons in the Astro era (description of the Commons file; nothing beyond what it says).
	'goya' => [
		'es' => 'Fachada de una catedral frente a una plaza, en una ciudad del litoral argentino',
		'en' => 'Facade of a cathedral facing a square, in a city of the Argentine Litoral',
		'pt' => 'Fachada de uma catedral diante de uma praça, em uma cidade do litoral argentino',
	],
	'esquina' => [
		'es' => 'Vista de una ciudad a orillas del río, tomada desde el agua',
		'en' => 'View of a riverside city, taken from the water',
		'pt' => 'Vista de uma cidade à beira do rio, tirada desde a água',
	],
	'chascomus' => [
		'es' => 'Atardecer sobre una laguna, con el cielo teñido de colores sobre el agua',
		'en' => 'Sunset over a lagoon, with the sky coloured above the water',
		'pt' => 'Entardecer sobre uma lagoa, com o céu colorido sobre a água',
	],
	'mar-del-plata' => [
		'es' => 'Vista de una ciudad costera con la costa del océano Atlántico',
		'en' => 'View of a coastal city with the Atlantic Ocean shoreline',
		'pt' => 'Vista de uma cidade costeira com o litoral do oceano Atlântico',
	],
];
// Validation of our own texts: 8 to 16 words, no "foto de", Concordia as the owner dictated.
foreach ( $textos as $k => $t ) {
	foreach ( [ 'es', 'en', 'pt' ] as $l ) {
		$n = count( preg_split( '/\s+/u', trim( $t[ $l ] ?? '' ) ) );
		$min = 'concordia-pescador-atardecer' === $k ? 6 : 8; // the owner's wording for Concordia is shorter and stays as dictated.
		if ( $n < $min || $n > 16 || preg_match( '/^\s*(foto|photo|picture|imagem|imagen) (de|of)\b/iu', $t[ $l ] ) ) {
			WP_CLI::error( "alt fuera de reglas: $k/$l ($n palabras)" );
		}
	}
}

$base = function ( int $att ) : string {
	$f = (string) get_post_meta( $att, '_wp_attached_file', true );
	$n = strtolower( preg_replace( '/\.[a-z0-9]+$/i', '', basename( $f ) ) );
	$n = preg_replace( '/-scaled$/', '', $n );
	return isset( $GLOBALS['textos'][ $n ] ) ? $n : preg_replace( '/-\d+$/', '', $n );
};
$corto = fn( $s ) => mb_substr( trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( (string) $s ) ) ), 0, 140 );

// Unique featured photos with the posts that use them.
$usos = [];
foreach ( [ 'pm_destino', 'pm_nota', 'pm_especie', 'pm_proximo' ] as $tipo ) {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'publish', 'posts_per_page' => -1, 'no_found_rows' => true, 'orderby' => 'ID', 'order' => 'ASC' ] ) as $p ) {
		$att = (int) get_post_thumbnail_id( $p->ID );
		if ( ! $att ) {
			continue;
		}
		$usos[ $att ][] = sprintf( '%s:%s[%s]', str_replace( 'pm_', '', $tipo ), pm_clave_de( $p->ID ) ?: $p->post_name, pm_idioma_de( $p->ID ) ?: '-' );
	}
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': ' . count( $usos ) . ' fotos destacadas distintas' );

$completadas = [];
$sin_base    = [];
$cambios     = 0;
foreach ( $usos as $att => $lista ) {
	if ( ! wp_attachment_is_image( $att ) ) {
		WP_CLI::log( "ROTA: adjunto #$att no es imagen | usos: " . implode( ', ', $lista ) );
		continue;
	}
	$clave = $base( $att );
	$post  = get_post( $att );
	$wpalt = (string) get_post_meta( $att, '_wp_attachment_image_alt', true );
	$ex    = [];
	foreach ( [ 'es', 'en', 'pt' ] as $l ) {
		$ex[ $l ] = (string) get_post_meta( $att, "_pm_alt_$l", true );
	}
	WP_CLI::log( "#$att $clave | " . get_post_meta( $att, '_wp_attached_file', true ) . ' | título «' . $corto( $post->post_title ) . '» | descr «' . $corto( $post->post_content ) . '» | credito=' . pm_campo( 'foto_autor', $att ) . '/' . pm_campo( 'foto_origen', $att ) );
	WP_CLI::log( "   usos: " . implode( ', ', $lista ) );
	WP_CLI::log( "   _wp_attachment_image_alt=«$wpalt»" );
	foreach ( $ex as $l => $v ) {
		WP_CLI::log( "   ANTES _pm_alt_$l=«$v»" );
	}
	$t = $textos[ $clave ] ?? null;
	if ( ! $t ) {
		if ( '' === $ex['es'] || '' === $ex['en'] || '' === $ex['pt'] ) {
			$sin_base[ $att ] = "$clave | " . implode( ', ', $lista ) . ' | título «' . $corto( $post->post_title ) . '»';
			WP_CLI::log( '   SIN BASE: no está en la tabla; no se completa' );
		} else {
			WP_CLI::log( '   completa (es/en/pt ya cargados)' );
		}
		continue;
	}
	$nuevo = [];
	foreach ( [ 'es', 'en', 'pt' ] as $l ) {
		if ( '' === $ex[ $l ] ) {
			$nuevo[ $l ] = $t[ $l ];
		}
	}
	$poner_wp = '' === $wpalt;
	if ( ! $nuevo && ! $poner_wp ) {
		WP_CLI::log( '   completa: nada que escribir' );
		continue;
	}
	foreach ( $nuevo as $l => $v ) {
		WP_CLI::log( "   DESPUÉS _pm_alt_$l=«$v»" );
	}
	if ( $poner_wp ) {
		WP_CLI::log( "   DESPUÉS _wp_attachment_image_alt=«{$t['es']}»" );
	}
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	foreach ( $nuevo as $l => $v ) {
		update_post_meta( $att, "_pm_alt_$l", $v );
	}
	if ( $poner_wp ) {
		update_post_meta( $att, '_wp_attachment_image_alt', $t['es'] );
	}
	$completadas[ $att ] = [ $t, $ex, $poner_wp ];
}

if ( $aplicar ) {
	foreach ( $completadas as $att => [ $t, $ex, $poner_wp ] ) {
		$ok = true;
		foreach ( [ 'es', 'en', 'pt' ] as $l ) {
			$v   = (string) get_post_meta( $att, "_pm_alt_$l", true );
			$ok  = $ok && '' !== $v && ( '' !== $ex[ $l ] ? $v === $ex[ $l ] : $v === $t[ $l ] );
		}
		$ok = $ok && '' !== (string) get_post_meta( $att, '_wp_attachment_image_alt', true );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": alt #$att es=«" . get_post_meta( $att, '_pm_alt_es', true ) . '»' );
	}
}
WP_CLI::log( "\nFotos con alt a escribir: $cambios | sin base (no completadas): " . count( $sin_base ) );
foreach ( $sin_base as $att => $d ) {
	WP_CLI::log( "SIN BASE #$att $d" );
}
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
}
