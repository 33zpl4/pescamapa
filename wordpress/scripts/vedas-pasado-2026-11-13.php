<?php
/**
 * Stale closure text, 13/11/2026. Four notes (es/en/pt) still said, in the present tense, that the 2026 extraordinary closures of the Paraná
 * were in force: "Desde el 29 de mayo… rige una veda… km 1232 y 1238… No tiene fecha de fin" and "Desde el 9 de septiembre… rige una veda… km 1109
 * y 1150". According to Perfil (research folder corrientes-2-20261113-2313, files 09 to 12) the first one was lifted by Disposición 639/26 (~4 June),
 * a second stretch (km 1180 to 1239) was added on 16 September, and Disposición 1054 lifted the closure on 29 September 2026 at 13:00.
 * Same timeline as the tables of the two price notes (precio-viaje-dorado-lodge, viaje-dorado-amigos-precios): attributed to the press, "número
 * pendiente" where the article does not give the disposition number. We do NOT say that no closure is in force today: the articles end on 29/9.
 * Notes: tallas-cupos-vedas (two list items), golden-dorado-guia, cuando-pescar-dorado, cuando-pescar-surubi. Only the list items that are
 * wrong are replaced; slugs and titles are not touched. Goya's published text is NOT wrong (it already says the 2026 closures were upstream, far
 * from Goya, and gives no km), so it is not touched.
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = vedas-pasado-2026-11-13).
 */
$aplicar = true;
$hoy     = '20261113';

$u = [
	'abc'  => 'https://www.abc.com.py/interior/2026/06/01/corrientes-y-chaco-decretan-veda-temporal-para-preservar-cardumenes-de-surubi-en-el-rio-parana/',
	'lit'  => 'https://www.ellitoral.com.ar/sociedad/2026-9-9-19-35-0-rige-una-veda-extraordinaria-para-proteger-un-cardumen-de-dorados-en-el-rio-parana',
	'p639' => 'https://www.perfil.com/noticias/nea/tras-la-dispersion-de-los-cardumenes-de-surubi-corrientes-levanto-la-veda-extraordinaria-en-el-parana.phtml',
	'p909' => 'https://www.perfil.com/noticias/nea/dispusieron-la-veda-extraordinaria-en-el-rio-parana-por-una-concentracion-masiva-de-dorados.phtml',
	'p916' => 'https://www.perfil.com/noticias/nea/fijaron-la-veda-extraordinaria-en-un-nuevo-tramo-del-rio-parana-para-proteger-dorados-y-sabalos.phtml',
	'p929' => 'https://www.perfil.com/noticias/nea/corrientes-levanto-la-veda-extraordinaria-de-pesca-en-el-rio-parana-que-cambia-desde-hoy.phtml',
];
function pm_vp_a( string $url, string $txt ): string {
	return '<a href="' . $url . '" rel="noopener">' . $txt . '</a>';
}

$T = [
	'es' => [
		'ini_a' => [ 'Desde el 29 de mayo de 2026' ],
		'ini_b' => [ 'Desde el 9 de septiembre de 2026', 'En septiembre de 2026 se dispuso otra' ],
		'a'     => 'Desde el 29 de mayo de 2026 rigió una veda extraordinaria en el Paraná entre los km 1232 y 1238, dispuesta por Corrientes (Disposición 618/26) y Chaco (Disposición 023) para proteger una concentración de surubíes (' . pm_vp_a( $u['abc'], 'ABC Color' ) . ', ' . pm_vp_a( $u['p639'], 'Perfil' ) . '). Según la prensa (Perfil, 4/6/2026), la Disposición 639/26 la levantó hacia el 4 de junio.',
		'b'     => 'Según la prensa (' . pm_vp_a( $u['p909'], 'Perfil' ) . ', ' . pm_vp_a( $u['lit'], 'El Litoral' ) . '): el 9 de septiembre de 2026, a las 18:00, se dispuso una veda extraordinaria entre los km 1109 y 1150%AGUAS% por una concentración de dorados (número de disposición pendiente); el 16 de septiembre se sumó el tramo de los km 1180 a 1239, del riacho INTA a la zona de Confluencia (' . pm_vp_a( $u['p916'], 'Perfil' ) . '; número pendiente); y el 29 de septiembre de 2026, a las 13:00, la Disposición 1054 levantó la veda (' . pm_vp_a( $u['p929'], 'Perfil' ) . '). Si vuelven a concentrarse peces, Recursos Naturales dictará un nuevo acto con restricciones: consultá con Recursos Naturales o con tu guía antes de salir.',
		'aguas' => ', aguas arriba de Goya,',
	],
	'en' => [
		'ini_a' => [ 'Since 29 May 2026' ],
		'ini_b' => [ 'Since 9 September 2026', 'In September 2026 another one' ],
		'a'     => 'From 29 May 2026 an extraordinary closure applied on the Paraná between km 1232 and 1238, ordered by Corrientes (Disposición 618/26) and Chaco (Disposición 023) to protect a concentration of surubí (' . pm_vp_a( $u['abc'], 'ABC Color' ) . ', ' . pm_vp_a( $u['p639'], 'Perfil' ) . ', in Spanish). According to the press (Perfil, 4/6/2026), Disposición 639/26 lifted it around 4 June.',
		'b'     => 'According to the press (' . pm_vp_a( $u['p909'], 'Perfil' ) . ', ' . pm_vp_a( $u['lit'], 'El Litoral' ) . ', in Spanish): on 9 September 2026 at 18:00 an extraordinary closure was ordered between km 1109 and 1150%AGUAS% because of a concentration of dorado (order number pending); on 16 September the stretch from km 1180 to 1239, from the INTA creek to the Confluencia area, was added (' . pm_vp_a( $u['p916'], 'Perfil' ) . '; number pending); and on 29 September 2026 at 13:00 Disposición 1054 lifted the closure (' . pm_vp_a( $u['p929'], 'Perfil' ) . '). If fish concentrate again, Recursos Naturales will issue a new act with restrictions: check with Recursos Naturales or your guide before you go.',
		'aguas' => ', upstream of Goya,',
	],
	'pt' => [
		'ini_a' => [ 'Desde 29 de maio de 2026' ],
		'ini_b' => [ 'Desde 9 de setembro de 2026', 'Em setembro de 2026 foi decretado outro' ],
		'a'     => 'Desde 29 de maio de 2026 vigorou um defeso extraordinário no Paraná entre os km 1232 e 1238, decretado por Corrientes (Disposição 618/26) e Chaco (Disposição 023) para proteger uma concentração de surubins (' . pm_vp_a( $u['abc'], 'ABC Color' ) . ', ' . pm_vp_a( $u['p639'], 'Perfil' ) . ', em espanhol). Segundo a imprensa (Perfil, 4/6/2026), a Disposição 639/26 o suspendeu por volta de 4 de junho.',
		'b'     => 'Segundo a imprensa (' . pm_vp_a( $u['p909'], 'Perfil' ) . ', ' . pm_vp_a( $u['lit'], 'El Litoral' ) . ', em espanhol): em 9 de setembro de 2026, às 18:00, foi decretado um defeso extraordinário entre os km 1109 e 1150%AGUAS% por uma concentração de dourados (número da disposição pendente); em 16 de setembro acrescentou-se o trecho dos km 1180 a 1239, do riacho INTA até a zona de Confluencia (' . pm_vp_a( $u['p916'], 'Perfil' ) . '; número pendente); e em 29 de setembro de 2026, às 13:00, a Disposição 1054 suspendeu o defeso (' . pm_vp_a( $u['p929'], 'Perfil' ) . '). Se os peixes voltarem a se concentrar, Recursos Naturais emitirá um novo ato com restrições: consulte Recursos Naturais ou o seu guia antes de sair.',
		'aguas' => ', rio acima de Goya,',
	],
];

/** Which list items each note has: [ clave => [ 'a'?, 'b'?, 'goya'? ] ]. */
$notas = [
	'tallas-cupos-vedas'   => [ 'a' => true, 'b' => true, 'goya' => true ],
	'golden-dorado-guia'   => [ 'b' => true ],
	'cuando-pescar-dorado' => [ 'b' => true ],
	'cuando-pescar-surubi' => [ 'a' => true ],
];
$marca_ok = [ 'a' => '639/26', 'b' => 'Disposición 1054' ];
$mal      = 0;
$cambios  = 0;

foreach ( $notas as $clave => $cual ) {
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$p = pm_buscar( 'pm_nota', $clave, $i );
		if ( ! $p ) {
			WP_CLI::warning( "$clave ($i): no existe" );
			$mal++;
			continue;
		}
		$c     = $p->post_content;
		$nuevo = $c;
		$hechos = [];
		foreach ( [ 'a', 'b' ] as $k ) {
			if ( empty( $cual[ $k ] ) ) {
				continue;
			}
			$texto = str_replace( '%AGUAS%', ( 'b' === $k && ! empty( $cual['goya'] ) ) ? $T[ $i ]['aguas'] : '', $T[ $i ][ $k ] );
			if ( false !== strpos( $nuevo, $texto ) ) {
				$hechos[] = "$k ya estaba";
				continue;
			}
			$n = 0;
			foreach ( $T[ $i ][ "ini_$k" ] as $ini ) {
				$nuevo = preg_replace_callback( '#<li>' . preg_quote( $ini, '#' ) . '(?:(?!</li>).)*</li>#us', function ( $m ) use ( $texto, &$n ) {
					if ( 0 === $n ) {
						$n++;
						return '<li>' . $texto . '</li>';
					}
					// A second item of the same kind (tallas-cupos-vedas has the dorado one as its own item): drop duplicates only when we already put the text.
					return '';
				}, $nuevo, -1, $cuenta );
				if ( 'b' === $k && 'tallas-cupos-vedas' === $clave && $n > 0 ) {
					break;
				}
			}
			$n ? $hechos[] = "$k reescrito" : $hechos[] = "$k NO ENCONTRADO";
			if ( ! $n ) {
				WP_CLI::warning( "$clave ($i): no encontré el ítem «$k» (¿editado?); no lo toco" );
				$mal++;
			}
		}
		// Empty list items left by a dropped duplicate.
		$nuevo = preg_replace( '#<!-- wp:list-item -->\s*<!-- /wp:list-item -->\s*#u', '', $nuevo );
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $clave ($i) #{$p->ID}: " . implode( ' · ', $hechos ) . ( $nuevo !== $c ? ' (' . strlen( $c ) . ' → ' . strlen( $nuevo ) . ' bytes)' : '' ) );
		if ( $nuevo === $c ) {
			continue;
		}
		$cambios++;
		if ( ! $aplicar ) {
			continue;
		}
		$r = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( "$clave ($i): " . $r->get_error_message() );
		}
		$leido = get_post( $p->ID )->post_content;
		$ok    = true;
		foreach ( [ 'a', 'b' ] as $k ) {
			if ( ! empty( $cual[ $k ] ) && false === strpos( $leido, $marca_ok[ $k ] ) ) {
				$ok = false;
			}
		}
		foreach ( [ 'No tiene fecha de fin', 'It has no end date', 'Não tem data de término', 'Press reports give no end date', 'La prensa no da fecha de fin', 'A imprensa não dá data de fim' ] as $viejo ) {
			if ( false !== strpos( $leido, $viejo ) ) {
				$ok = false;
			}
		}
		update_field( 'actualizado', $hoy, $p->ID );
		update_post_meta( $p->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #{$p->ID} slug=" . $p->post_name . ' ' . get_permalink( $p->ID ) );
		$ok || $mal++;
	}
}

// tallas-cupos-vedas: add the Perfil articles to the sources.
if ( $aplicar ) {
	$quiero = [
		[ 'Perfil (4/6/2026): Tras la dispersión de los cardúmenes de surubí, Corrientes levantó la veda extraordinaria en el Paraná (Disposición 618/26 y 639/26)', $u['p639'] ],
		[ 'Perfil (10/9/2026): Dispusieron la veda extraordinaria en el río Paraná por una concentración masiva de dorados', $u['p909'] ],
		[ 'Perfil (16/9/2026): Fijaron la veda extraordinaria en un nuevo tramo del río Paraná', $u['p916'] ],
		[ 'Perfil (29/9/2026): Corrientes levantó la veda extraordinaria de pesca en el río Paraná (Disposición 1054)', $u['p929'] ],
	];
	foreach ( [ 'es', 'en', 'pt' ] as $i ) {
		$p = pm_buscar( 'pm_nota', 'tallas-cupos-vedas', $i );
		if ( ! $p ) {
			continue;
		}
		$filas = array_map( function ( $f ) {
			$f['consultada'] = str_replace( '-', '', (string) ( $f['consultada'] ?? '' ) );
			$f['oficial']    = empty( $f['oficial'] ) ? 0 : 1;
			return $f;
		}, array_values( array_filter( pm_filas( 'fuentes', $p->ID ), 'is_array' ) ) );
		$urls = array_column( $filas, 'url' );
		foreach ( $quiero as [ $nombre, $url ] ) {
			if ( ! in_array( $url, $urls, true ) ) {
				$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => 0, 'consultada' => $hoy ];
			}
		}
		update_field( 'fuentes', $filas, $p->ID );
		$ok = count( array_intersect( array_column( pm_filas( 'fuentes', $p->ID ), 'url' ), array_column( $quiero, 1 ) ) ) === count( $quiero );
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": tallas-cupos-vedas ($i) fuentes de Perfil" );
		$ok || $mal++;
	}
}
$mal && WP_CLI::error( "$mal avisos: revisar." );
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios notas cambian." );
