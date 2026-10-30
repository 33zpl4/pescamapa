<?php
/**
 * Junín y San Martín: one guide, not two (30/10/2026). A guide with clave `san-martin-junin` has been published since 24/10/2026
 * (san-martin-junin-2026-10-24, built on the 2025/26 booklet). On 30/10/2026 a second guide (clave `junin-de-los-andes`) was created for the same
 * place by mistake: the map shows the name twice. The newer text (2026/27 annexes, permit values, validity, authorised guides, preferential zones)
 * is better, so it moves INTO the old guide, which keeps its URL, title, H1 and SEO fields (it may already be indexed). Then:
 *   - the old guide takes body, subtitle, techniques, season, regulation fields, waters, FAQs and sources from the new one, plus the official pages
 *     of the regulation as sources (what junin-fuentes-2026-10-30 would have added);
 *   - the cover photo (Chad Shmukler) goes to the old guide;
 *   - Tipiliuke Lodge and Hostería San Huberto point to `san-martin-junin`;
 *   - the duplicate guides (es, en) are moved to the trash (recoverable). Their URLs stop working.
 * The old content stays in the post revisions. Idempotent: if the duplicate no longer exists nothing is done. Dry run first.
 * Run with wp-script.yml (script = junin-unificar-2026-10-30).
 */

$aplicar = false;
$hoy     = '20261030';
$R       = 'https://reglamentodepesca.org.ar/reglamento-items/';
$oficiales = [
	'en' => [
		[ 'Regulation of Continental Sport Fishing of Patagonia (official site): Neuquén annex, general points, read on 30/10/2026', $R . 'anexo-neuquen/' ],
		[ 'Regulation of Continental Sport Fishing of Patagonia (official site): Río Negro annex, general points, read on 30/10/2026', $R . 'anexo-rio-negro/' ],
		[ 'Regulation 2026/27 (official site): list of environments of Neuquén; we read the entries in the AICACYP copy listed above', $R . 'ambientes-neuquen/' ],
		[ 'Regulation 2026/27 (official site): list of environments of the National Parks of the south; read in the AICACYP copy listed above', $R . 'ambientes-parques-nacionales-del-sur/' ],
	],
	'es' => [
		[ 'Reglamento de Pesca Deportiva Continental Patagónico (sitio oficial): Anexo Neuquén, puntos generales, leído el 30/10/2026', $R . 'anexo-neuquen/' ],
		[ 'Reglamento de Pesca Deportiva Continental Patagónico (sitio oficial): Anexo Río Negro, puntos generales, leído el 30/10/2026', $R . 'anexo-rio-negro/' ],
		[ 'Reglamento 2026/27 (sitio oficial): listado de ambientes de Neuquén; leímos las fichas en la copia de AICACYP listada arriba', $R . 'ambientes-neuquen/' ],
		[ 'Reglamento 2026/27 (sitio oficial): listado de ambientes de los Parques Nacionales del sur; leído en la copia de AICACYP listada arriba', $R . 'ambientes-parques-nacionales-del-sur/' ],
	],
];
$campos_a_copiar = [ 'bajada', 'modalidades', 'epoca', 'temporada', 'reglamento_autoridad', 'reglamento_url', 'reglamento_verificado', 'reglamento_resumen', 'aguas', 'faqs', 'actualizado', 'autor' ];

$trasladar = [];
foreach ( [ 'es', 'en' ] as $i ) {
	$viejo = pm_buscar( 'pm_destino', 'san-martin-junin', $i );
	$nuevo = pm_buscar( 'pm_destino', 'junin-de-los-andes', $i );
	if ( ! $viejo ) {
		WP_CLI::error( "No encontré la guía vieja san-martin-junin ($i)." );
	}
	if ( ! $nuevo ) {
		WP_CLI::log( "($i) la guía duplicada ya no existe: nada que unir." );
		continue;
	}
	$fuentes = pm_filas( 'fuentes', $nuevo->ID );
	$urls    = array_column( $fuentes, 'url' );
	foreach ( $oficiales[ $i ] as [ $nombre, $url ] ) {
		if ( ! in_array( $url, $urls, true ) ) {
			$fuentes[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => 1, 'consultada' => $hoy ];
		}
	}
	WP_CLI::log( ( $aplicar ? 'UNE' : 'SIMULACRO' ) . " ($i): contenido de #{$nuevo->ID} «{$nuevo->post_title}» pasa a #{$viejo->ID} «{$viejo->post_title}» /{$viejo->post_name}/ ("
		. count( pm_filas( 'faqs', $nuevo->ID ) ) . ' preguntas, ' . count( $fuentes ) . ' fuentes; hoy la vieja tiene ' . count( pm_filas( 'faqs', $viejo->ID ) ) . ' y ' . count( pm_filas( 'fuentes', $viejo->ID ) ) . '); #' . $nuevo->ID . ' va a la papelera' );
	$trasladar[ $i ] = [ $viejo, $nuevo, $fuentes ];
}

$att = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => 'junin-pescador-volcan.jpg', 'numberposts' => 1, 'fields' => 'ids' ] );
$att = $att ? (int) $att[0] : 0;
$prest = [];
foreach ( [ 'Tipiliuke Lodge', 'Hostería San Huberto' ] as $nombre ) {
	$ids = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( $ids ) {
		$prest[ $nombre ] = (int) $ids[0];
	}
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ': foto #' . $att . ' a la guía vieja (es); prestadores a mover: ' . implode( ', ', array_map( fn( $n, $id ) => "$n #$id", array_keys( $prest ), $prest ) ) );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

foreach ( $trasladar as $i => [ $viejo, $nuevo, $fuentes ] ) {
	wp_update_post( [ 'ID' => $viejo->ID, 'post_content' => wp_slash( $nuevo->post_content ) ] );
	foreach ( $campos_a_copiar as $c ) {
		update_field( $c, pm_campo( $c, $nuevo->ID ), $viejo->ID );
	}
	update_field( 'fuentes', $fuentes, $viejo->ID );
	if ( 'es' === $i && $att ) {
		set_post_thumbnail( $viejo->ID, $att );
	}
	$ok = false !== strpos( get_post_field( 'post_content', $viejo->ID ), 'Neuquén' ) && count( pm_filas( 'fuentes', $viejo->ID ) ) === count( $fuentes ) && count( pm_filas( 'faqs', $viejo->ID ) ) === count( pm_filas( 'faqs', $nuevo->ID ) );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": guía san-martin-junin ($i) #{$viejo->ID} con el contenido nuevo: " . count( pm_filas( 'faqs', $viejo->ID ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $viejo->ID ) ) . ' fuentes' );
	$ok || WP_CLI::error( "($i) la unión no quedó." );
}
if ( isset( $trasladar['es'] ) && $att ) {
	$vid = $trasladar['es'][0]->ID;
	WP_CLI::log( ( (int) get_post_thumbnail_id( $vid ) === $att ? 'VERIFIED' : 'FALLÓ' ) . ": foto #$att en la guía vieja (es), crédito " . pm_campo( 'foto_autor', $att ) );
}
foreach ( $prest as $nombre => $id ) {
	update_field( 'prest_destino', 'san-martin-junin', $id );
	WP_CLI::log( ( 'san-martin-junin' === pm_campo( 'prest_destino', $id ) ? 'VERIFIED' : 'FALLÓ' ) . ": prestador $nombre #$id apunta a san-martin-junin" );
}
foreach ( $trasladar as $i => [ $viejo, $nuevo ] ) {
	wp_trash_post( $nuevo->ID );
	WP_CLI::log( ( 'trash' === get_post_status( $nuevo->ID ) ? 'VERIFIED' : 'FALLÓ' ) . ": duplicado ($i) #{$nuevo->ID} en la papelera" );
}
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
