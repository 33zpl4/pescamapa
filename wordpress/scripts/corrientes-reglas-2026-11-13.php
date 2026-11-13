<?php
/**
 * Corrientes guides, 13/11/2026: the Goya guide cited "Resolución 9/2021, medidas mínimas (surubí 85 cm)" (argentina.gob.ar norma-352495) as a
 * "national reference minimum size" for the surubí. That page (read 13/11/2026, research folder corrientes-2-20261113-2313, file 18) is the
 * Resolución 9/2021 of the CARU (Comisión Administradora del Río Uruguay), signed in Paysandú on 17/06/2021, "Prácticas de pesca comercial
 * y artesanal": a rule of the Uruguay River, with the 85 cm in a recital about commercial fishing. It is not a rule of the Paraná, so it goes.
 *  - Goya (es/pt/en): the sentence on the 85 cm becomes the Disposición 853/22 rule that the Corrientes guides already publish (dorado and surubí:
 *    catch and release only, so no minimum size to keep one), says it is transcribed from the Disposición and that we could not open the Boletín
 *    Oficial text on 13/11/2026; the source row "Argentina.gob.ar: Resolución 9/2021…" is replaced by the Boletín Oficial row of the Disposición.
 *  - Paso de la Patria (es/pt/en): adds the same "transcribed; could not reopen the Boletín Oficial on 13/11/2026" line to the regulation summary.
 *  - Esquina (es/pt/en): the pacú 45 cm (summary, FAQ and text) now names the Disposición 853/22 that supports it, and the Boletín Oficial row is added.
 *  - La Paz (Entre Ríos) publishes no size or bag limit ("no pudimos confirmarlo"): nothing to change. Not touched.
 * Idempotent. Simulacro first ($aplicar = false). Run with wp-script.yml (script = corrientes-reglas-2026-11-13).
 */
$aplicar = false;
$hoy     = '20261113';
$bo_url  = 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf';
$bo_fila = [ 'nombre' => 'Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)', 'url' => $bo_url, 'oficial' => 1, 'consultada' => '20261021' ];
$caru    = 'https://www.argentina.gob.ar/normativa/nacional/norma-352495/texto';

$goya = [
	'es' => [
		'Para el surubí, la medida mínima de referencia nacional es 85 cm; consultá la vigente en Corrientes.',
		'Para el surubí, la Disposición 853/22 de Recursos Naturales (transcrita en nuestra guía de Paso de la Patria) dispone solo pesca y devolución: no se retiene, por eso no publicamos una medida mínima. El 13/11/2026 no pudimos abrir el texto del Boletín Oficial: confirmalo con Recursos Naturales.',
	],
	'pt' => [
		'Para o surubim, o tamanho mínimo de referência nacional é 85 cm; confira o vigente em Corrientes.',
		'Para o surubim, a Disposição 853/22 de Recursos Naturais (transcrita no nosso guia de Paso de la Patria) prevê somente pesca e devolução: não pode ser retido, por isso não publicamos tamanho mínimo. Em 13/11/2026 não conseguimos abrir o texto do Diário Oficial: confirme com Recursos Naturais.',
	],
	'en' => [
		'For surubí, the national reference minimum size is 85 cm; check the current Corrientes rule.',
		'For surubí, Disposición 853/22 of Recursos Naturales (transcribed in our Paso de la Patria guide) allows catch and release only: it cannot be kept, so we publish no minimum size. On 13/11/2026 we could not open the Boletín Oficial text: confirm it with Recursos Naturales.',
	],
];
$paso = [
	'es' => ' Estos valores están transcritos de la Disposición 853/22; el 13/11/2026 no pudimos abrir el texto del Boletín Oficial, así que confirmalos con Recursos Naturales antes de salir.',
	'pt' => ' Estes valores estão transcritos da Disposição 853/22; em 13/11/2026 não conseguimos abrir o texto do Diário Oficial, então confirme com Recursos Naturais antes de sair.',
	'en' => ' These values are transcribed from Disposición 853/22; on 13/11/2026 we could not open the Boletín Oficial text, so confirm them with Recursos Naturales before you go.',
];
$esquina = [
	'es' => [ [ 'El pacú tiene medida mínima de 45 cm,', 'El pacú tiene medida mínima de 45 cm (Disposición 853/22),' ], [ 'La medida mínima es 45 cm y solo', 'La medida mínima es 45 cm (Disposición 853/22) y solo' ] ],
	'pt' => [ [ 'O pacu tem medida mínima de 45 cm,', 'O pacu tem medida mínima de 45 cm (Disposição 853/22),' ], [ 'A medida mínima é 45 cm e só', 'A medida mínima é 45 cm (Disposição 853/22) e só' ] ],
	'en' => [ [ 'The pacú has a minimum size of 45 cm,', 'The pacú has a minimum size of 45 cm (Disposición 853/22),' ], [ 'The minimum size is 45 cm and', 'The minimum size is 45 cm (Disposición 853/22) and' ] ],
];

function pm_cr_fuentes( int $id, array $fila_bo, string $caru ): array {
	$filas = array_map( function ( $f ) {
		$f['consultada'] = str_replace( '-', '', (string) ( $f['consultada'] ?? '' ) );
		$f['oficial']    = empty( $f['oficial'] ) ? 0 : 1;
		return $f;
	}, array_values( array_filter( pm_filas( 'fuentes', $id ), 'is_array' ) ) );
	return $filas;
}
function pm_cr_marcar( int $id, string $i ): void {
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
}

$falla = 0;
foreach ( [ 'es', 'pt', 'en' ] as $i ) {
	// ---------------- Goya ----------------
	$p = pm_buscar( 'pm_destino', 'goya', $i );
	if ( ! $p ) {
		WP_CLI::warning( "goya ($i) no existe" );
		$falla++;
	} else {
		$id      = $p->ID;
		$resumen = (string) get_post_meta( $id, 'reglamento_resumen', true );
		[ $viejo, $nuevo ] = $goya[ $i ];
		if ( str_contains( $resumen, $viejo ) ) {
			WP_CLI::log( pm_sc_modo_cr( $aplicar ) . ": goya ($i) #$id resumen: «{$viejo}» → «{$nuevo}»" );
			$aplicar && update_field( 'reglamento_resumen', str_replace( $viejo, $nuevo, $resumen ), $id );
		} elseif ( str_contains( $resumen, $nuevo ) ) {
			WP_CLI::log( "· goya ($i): el resumen ya estaba corregido" );
		} else {
			WP_CLI::warning( "goya ($i): no encontré la frase de los 85 cm en el resumen (¿editada?); no lo toco" );
			$falla++;
		}
		$filas = pm_cr_fuentes( $id, $bo_fila, $caru );
		$urls  = array_column( $filas, 'url' );
		$ya_bo = in_array( $bo_url, $urls, true );
		$nuevas = [];
		$cambio = false;
		foreach ( $filas as $f ) {
			if ( ( $f['url'] ?? '' ) === $caru ) {
				$cambio = true;
				if ( ! $ya_bo ) {
					$nuevas[] = $bo_fila;
					$ya_bo    = true;
				}
				continue;
			}
			$nuevas[] = $f;
		}
		if ( $cambio ) {
			WP_CLI::log( pm_sc_modo_cr( $aplicar ) . ": goya ($i) fuentes: se quita «Argentina.gob.ar: Resolución 9/2021…» (CARU, río Uruguay) y queda «{$bo_fila['nombre']}»" );
			if ( $aplicar ) {
				update_field( 'fuentes', $nuevas, $id );
				update_field( 'actualizado', $hoy, $id );
				pm_cr_marcar( $id, $i );
			}
		} else {
			WP_CLI::log( "· goya ($i): la fuente de la CARU ya no estaba" );
		}
		if ( $aplicar ) {
			update_field( 'actualizado', $hoy, $id );
			pm_cr_marcar( $id, $i );
		}
	}

	// ---------------- Paso de la Patria ----------------
	$p = pm_buscar( 'pm_destino', 'paso-de-la-patria', $i );
	if ( ! $p ) {
		WP_CLI::warning( "paso-de-la-patria ($i) no existe" );
		$falla++;
	} else {
		$id      = $p->ID;
		$resumen = (string) get_post_meta( $id, 'reglamento_resumen', true );
		if ( str_contains( $resumen, trim( $paso[ $i ] ) ) ) {
			WP_CLI::log( "· paso-de-la-patria ($i): la aclaración ya estaba" );
		} elseif ( '' === trim( $resumen ) ) {
			WP_CLI::warning( "paso-de-la-patria ($i): resumen vacío; no lo toco" );
			$falla++;
		} else {
			WP_CLI::log( pm_sc_modo_cr( $aplicar ) . ": paso-de-la-patria ($i) #$id resumen: se agrega «" . trim( $paso[ $i ] ) . '»' );
			if ( $aplicar ) {
				update_field( 'reglamento_resumen', rtrim( $resumen ) . $paso[ $i ], $id );
				update_field( 'actualizado', $hoy, $id );
				pm_cr_marcar( $id, $i );
			}
		}
	}

	// ---------------- Esquina ----------------
	$p = pm_buscar( 'pm_destino', 'esquina', $i );
	if ( ! $p ) {
		WP_CLI::warning( "esquina ($i) no existe" );
		$falla++;
		continue;
	}
	$id    = $p->ID;
	$hechos = 0;
	$resumen = (string) get_post_meta( $id, 'reglamento_resumen', true );
	$r2      = $resumen;
	foreach ( $esquina[ $i ] as [ $v, $n ] ) {
		$r2 = str_replace( $v, $n, $r2 );
	}
	if ( $r2 !== $resumen ) {
		WP_CLI::log( pm_sc_modo_cr( $aplicar ) . ": esquina ($i) #$id resumen: pacú 45 cm atribuido a la Disposición 853/22" );
		$aplicar && update_field( 'reglamento_resumen', $r2, $id );
		$hechos++;
	}
	$faqs  = pm_filas( 'faqs', $id );
	$f_cambio = false;
	foreach ( $faqs as &$f ) {
		$orig = (string) ( $f['respuesta'] ?? '' );
		$nv   = $orig;
		foreach ( $esquina[ $i ] as [ $v, $n ] ) {
			$nv = str_replace( $v, $n, $nv );
		}
		if ( $nv !== $orig ) {
			$f['respuesta'] = $nv;
			$f_cambio       = true;
		}
	}
	unset( $f );
	if ( $f_cambio ) {
		WP_CLI::log( pm_sc_modo_cr( $aplicar ) . ": esquina ($i) #$id FAQ del pacú: atribuida a la Disposición 853/22" );
		$aplicar && update_field( 'faqs', $faqs, $id );
		$hechos++;
	}
	$cont = $p->post_content;
	$c2   = $cont;
	foreach ( $esquina[ $i ] as [ $v, $n ] ) {
		$c2 = str_replace( $v, $n, $c2 );
	}
	if ( $c2 !== $cont ) {
		WP_CLI::log( pm_sc_modo_cr( $aplicar ) . ": esquina ($i) #$id texto: pacú atribuido" );
		$aplicar && wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $c2 ) ] );
		$hechos++;
	}
	$filas = pm_cr_fuentes( $id, $bo_fila, $caru );
	if ( ! in_array( $bo_url, array_column( $filas, 'url' ), true ) ) {
		$filas[] = $bo_fila;
		WP_CLI::log( pm_sc_modo_cr( $aplicar ) . ": esquina ($i) fuentes: se agrega el Boletín Oficial de Corrientes (Disposición 853/22)" );
		$aplicar && update_field( 'fuentes', $filas, $id );
		$hechos++;
	}
	if ( $aplicar && $hechos ) {
		update_field( 'actualizado', $hoy, $id );
		pm_cr_marcar( $id, $i );
	}
	if ( ! $hechos ) {
		WP_CLI::log( "· esquina ($i): ya estaba atribuido" );
	}
}

function pm_sc_modo_cr( bool $aplicar ): string {
	return $aplicar ? 'APLICA' : 'SIMULACRO';
}

// ---------------- Lectura de vuelta ----------------
if ( $aplicar ) {
	$mal = 0;
	foreach ( [ 'es', 'pt', 'en' ] as $i ) {
		$g = pm_buscar( 'pm_destino', 'goya', $i );
		if ( $g ) {
			$r = (string) pm_campo( 'reglamento_resumen', $g->ID );
			$u = array_column( pm_filas( 'fuentes', $g->ID ), 'url' );
			$ok = str_contains( $r, '853/22' ) && ! str_contains( $r, '85 cm' ) && ! in_array( $caru, $u, true ) && in_array( $bo_url, $u, true );
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": goya ($i) #{$g->ID} " . get_permalink( $g->ID ) );
			$ok || $mal++;
		}
		$e = pm_buscar( 'pm_destino', 'esquina', $i );
		if ( $e ) {
			$r  = (string) pm_campo( 'reglamento_resumen', $e->ID );
			$ok = str_contains( $r, '853/22' ) && in_array( $bo_url, array_column( pm_filas( 'fuentes', $e->ID ), 'url' ), true );
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": esquina ($i) #{$e->ID} " . get_permalink( $e->ID ) );
			$ok || $mal++;
		}
		$x = pm_buscar( 'pm_destino', 'paso-de-la-patria', $i );
		if ( $x ) {
			$ok = str_contains( (string) pm_campo( 'reglamento_resumen', $x->ID ), trim( $paso[ $i ] ) );
			WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": paso-de-la-patria ($i) #{$x->ID} " . get_permalink( $x->ID ) );
			$ok || $mal++;
		}
	}
	$mal && WP_CLI::error( "$mal lecturas de vuelta no coinciden." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ( $falla ? " ($falla avisos)" : '' ) . '.' );
