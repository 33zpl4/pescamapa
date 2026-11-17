<?php
/**
 * 17/11/2026 (regla del dueño, aprobada): no se muestran enlaces a sitios comerciales de lugares, operadores, agencias o tiendas
 * (invitan a irse del sitio). Se enlazan organismos, reglamentos y prensa. Plantillas y pie «Fuentes» ya lo cumplen
 * (pm_fuente_enlazable / pm_dominios_prensa en inc/datos.php); este script limpia el CUERPO ya publicado de notas, guías y fichas de especie.
 *
 * Qué hace: recorre pm_nota, pm_destino y pm_especie publicados (todos los idiomas): post_content, post_excerpt y los campos de texto (post meta no
 * interna) que contengan «<a ». Quita la etiqueta <a> de los dominios de $quitar y deja el texto del enlace tal cual
 * (<a href="https://comercial">Texto</a> → Texto). No cambia redacción: antes de escribir comprueba que el texto sin etiquetas sea idéntico.
 * Idempotente: una segunda corrida no encuentra nada que quitar. Simulacro por defecto ($aplicar = false); wp-script.yml con aplicar=true
 * pone $aplicar = true en una copia, sin commitear. En el simulacro imprime TODOS los dominios externos hallados (clase, cantidad, posts).
 *
 * RE-CORRER LOS GENERADORES VIEJOS REINTRODUCE LOS ENLACES (notas-dorado-surubi-2026-10-26, notas-trucha-patagonia-2026-10-26,
 * nota-trucha-patagonia-pilar-2026-11-15, preparar-viaje-hub, semana-lodge, rio-santa-cruz…): si se corre alguno, hay que volver a aplicar este.
 *
 * Criterio de clases (no hay regex genérico para quitar; lo que no está en una lista explícita NO se quita):
 *  - interno: enlaces relativos, #, mailto/tel y el propio dominio.
 *  - dejar: gobiernos y organismos (patrón .gob./.gov./.edu), prensa (pm_dominios_prensa() + medios evidentes de $dejar), organismos binacionales,
 *    academia y ciencia, Wikimedia y licencias, mapas, redes del propio sitio (URL con «pescamapa»).
 *  - quitar: la lista explícita $quitar = sitios de lodges, outfitters, guías, agencias de viaje, tiendas/marcas, marketplaces de reservas y blogs de
 *    esas empresas (incluye pesca.clubnautico.org: copia de un reglamento en el sitio de un club, que el helper declara NO oficial).
 *  - duda: todo lo demás (no se quita; se reporta para decidir).
 * Sin nombres de personas ni contactos en el log. Ejecutar con wp-script.yml (script = quitar-enlaces-comerciales-2026-11-17).
 */

$aplicar = false;

// ---- Lista explícita: dominios comerciales cuyos enlaces del cuerpo se quitan (host exacto o subdominio). ----
$quitar = [
	// Operadores, lodges, outfitters y guías de pesca
	'jurassiclake.com', 'estancialagunaverde.com', 'tightlooptravel.com', 'welcomeargentina.com', 'pesca.clubnautico.org',
	'nervouswaters.com', 'setflyfishing.com', 'hooked.com.ar', 'tipiliuke.com', 'dorados-on-the-fly.vercel.app',
	'tresamigosoutfitters.com', 'riomansolodge.com', 'karkuflyfishing.com', 'solidadventures.com', 'pointeroutfitters.com',
	'troutbariloche.com', 'southernriverspatagonia.com', 'patagoniaintheloop.com', 'patagoniadrift.com.ar', 'kautapen.com',
	'argentinaheritagefishing.com', 'limaylodge.com', 'sanjoseflyfishing.com', 'laspampaslodge.com', 'lapilaricalodge.com.ar',
	'kdflyfishing.com', 'wherewisemenfish.com', 'tatafishinglodge.com', 'riverplatedorados.com', 'posadadelpaso.com',
	'pescaconguia.com.ar', 'olsenfamily.com.ar', 'lazonalodge.com', 'fishingwithlarry.com', 'challhuaquen.com',
	'bitacoragoya.com.ar', 'fishingbraziladventures.com.br', 'pousadalapesca.com.br', 'pescanaargentina.com.br',
	// Dueño 17/11/2026: publicador sin identificar, foro y sitio comercial
	'reglamentodepesca.org.ar', 'comunidad.espoesia.com', 'eltransitoibera.com',
	// Agencias de viaje, tiendas, marcas y blogs de esas empresas
	'farbank.com', 'yellowdogflyfishing.com', 'orvis.com', 'frontierstravel.com', 'fishingworldguide.com', 'fishcolorado.com',
	'aardvarkmcleod.com', 'sportquestholidays.com', 'globe-truiteur.com', 'fansfishing.com',
	// Marketplaces de reservas y reseñas
	'booking.com', 'tripadvisor.com', 'fishingbooker.com',
];

// ---- Dominios que se dejan por criterio explícito (prensa evidente, organismos, ciencia, licencias, mapas). Además: pm_dominios_prensa() y patrón gob/gov/edu. ----
$dejar = [
	// Prensa
	'impactocorrientes.com', 'corrientesaldia.info', 'abc.com.py', 'canal26.com', 'rionegro.com.ar', 'diario7lagos.com.ar',
	'diariouno.com.ar', 'primeraedicion.com.ar', 'libreentrerios.com', 'cronista.com', 'elpais.com.uy', 'cholilaonline.ar',
	// Organismos binacionales / de cuenca, entidades de pesca
	'comip.org.ar', 'caru.org.uy', 'saltogrande.org', 'igfa.org', 'argentina.travel',
	// Ciencia, academia, datos
	'fishbase.se', 'schema.org', 'doi.org', 'scielo.org.ar', 'infoteca.cnptia.embrapa.br', 'bibliotecadigital.exactas.uba.ar',
	'rdi.uncoma.edu.ar', 'ni.bio.br', 'impo.com.uy', 'open-meteo.com',
	// Wikimedia, licencias, mapas
	'commons.wikimedia.org', 'wikimedia.org', 'wikipedia.org', 'creativecommons.org', 'unsplash.com',
	'openstreetmap.org', 'maps.google.com', 'maps.app.goo.gl',
];

if ( ! function_exists( 'pm_qe_en_lista' ) ) {
	function pm_qe_en_lista( string $host, array $lista ): bool {
		foreach ( $lista as $d ) {
			if ( $host === $d || str_ends_with( $host, '.' . $d ) ) {
				return true;
			}
		}
		return false;
	}
	/** [ clase, host ] de un href: interno | quitar | dejar | duda. */
	function pm_qe_clasificar( string $href, array $quitar, array $dejar ): array {
		$h = trim( html_entity_decode( $href, ENT_QUOTES, 'UTF-8' ) );
		if ( '//' === substr( $h, 0, 2 ) ) {
			$h = 'https:' . $h;
		}
		if ( ! preg_match( '#^https?://#i', $h ) ) {
			return [ 'interno', '(relativo/mailto/tel)' ];
		}
		$host = pm_fuente_host( $h );
		if ( '' === $host ) {
			return [ 'duda', '(host ilegible)' ];
		}
		$propio = (string) preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
		if ( $host === 'pescamapa.com' || ( '' !== $propio && ( $host === $propio || str_ends_with( $host, '.' . $propio ) ) ) ) {
			return [ 'interno', $host ];
		}
		if ( pm_qe_en_lista( $host, $quitar ) ) {
			return [ 'quitar', $host ];
		}
		$ruta = (string) wp_parse_url( $h, PHP_URL_PATH );
		if (
			pm_qe_en_lista( $host, $dejar ) || pm_qe_en_lista( $host, pm_dominios_prensa() )
			|| preg_match( '/(^|\.)(gob|gov|gub)(\.[a-z]{2})?$|\.gob\.[a-z]{2}$|\.gov\.[a-z]{2}$|(^|\.)edu(\.[a-z]{2})?$|\.uba\.ar$/', $host )
			|| ( 'google.com' === $host && 0 === strpos( $ruta, '/maps' ) )
			|| ( preg_match( '/(^|\.)(instagram|facebook|x|twitter|tiktok)\.com$/', $host ) && false !== stripos( $h, 'pescamapa' ) )
		) {
			return [ 'dejar', $host ];
		}
		return [ 'duda', $host ];
	}
	/** Quita los <a> de clase «quitar» de un texto: [ nuevo, enlaces quitados por host, todos los hosts vistos [host => [clase, n]] ]. */
	function pm_qe_procesar( string $txt, array $quitar, array $dejar ): array {
		$quitados = [];
		$vistos   = [];
		$nuevo    = preg_replace_callback(
			'~<a\b([^>]*)>(.*?)</a>~is',
			function ( $m ) use ( &$quitados, &$vistos, $quitar, $dejar ) {
				if ( ! preg_match( '~\bhref\s*=\s*(?:"([^"]*)"|\'([^\']*)\')~i', $m[1], $h ) ) {
					return $m[0];
				}
				$href = '' !== ( $h[1] ?? '' ) ? $h[1] : ( $h[2] ?? '' );
				[ $clase, $host ] = pm_qe_clasificar( $href, $quitar, $dejar );
				$vistos[ $host ] = [ $clase, ( $vistos[ $host ][1] ?? 0 ) + 1 ];
				if ( 'quitar' !== $clase ) {
					return $m[0];
				}
				$quitados[ $host ] = ( $quitados[ $host ] ?? 0 ) + 1;
				return $m[2];
			},
			$txt
		);
		return [ (string) $nuevo, $quitados, $vistos ];
	}
	/** Hrefs a dominios «quitar» que quedan en un texto, incluso escapados (\"https://…). */
	function pm_qe_restos( string $txt, array $quitar, array $dejar ): int {
		$n = 0;
		if ( preg_match_all( '~href\s*=\s*\\\\*["\']\s*(?:https?:)?//([^/"\'\\\\\s?#]+)~i', $txt, $m ) ) {
			foreach ( $m[1] as $host ) {
				$host = (string) preg_replace( '/^www\./', '', strtolower( $host ) );
				if ( pm_qe_en_lista( $host, $quitar ) ) {
					$n++;
				}
			}
		}
		return $n;
	}
	/** Campos de texto de un post que pueden llevar enlaces: [ campo => valor ] (content, excerpt, meta no interna con «<a »). */
	function pm_qe_campos( WP_Post $p ): array {
		$out = [];
		foreach ( [ 'post_content', 'post_excerpt' ] as $c ) {
			if ( false !== stripos( (string) $p->$c, '<a ' ) ) {
				$out[ $c ] = (string) $p->$c;
			}
		}
		foreach ( (array) get_post_meta( $p->ID ) as $k => $vals ) {
			if ( '_' === substr( (string) $k, 0, 1 ) ) {
				continue;
			}
			$v = $vals[0] ?? '';
			if ( is_string( $v ) && ! is_serialized( $v ) && false !== stripos( $v, '<a ' ) ) {
				$out[ 'meta:' . $k ] = $v;
			}
		}
		return $out;
	}
}

$modo = $aplicar ? 'APLICA' : 'SIMULACRO';
WP_CLI::log( "$modo: quitar enlaces comerciales del cuerpo de notas, guías y especies (lista explícita de " . count( $quitar ) . ' dominios).' );

// ---- 1) Inventario y plan ----
$inventario = [];      // host => [ clase, total, [ etiqueta => n ] ]
$plan       = [];      // id => [ post, [ campo => nuevo ], [ host => n ] ]
$escaneados = 0;
$tipos_tocar = [ 'pm_nota', 'pm_destino', 'pm_especie' ];
foreach ( array_merge( $tipos_tocar, [ 'pm_entrevista', 'pm_proximo', 'pm_prestador' ] ) as $tipo ) {
	$tocar = in_array( $tipo, $tipos_tocar, true );
	$posts = get_posts( [ 'post_type' => $tipo, 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ] );
	$n_tipo = 0;
	foreach ( $posts as $p ) {
		$n_tipo++;
		$etiqueta = ( $tocar ? '' : '[solo lectura] ' ) . substr( $tipo, 3 ) . ':' . pm_clave_de( $p->ID ) . '(' . pm_idioma_de( $p->ID ) . ')#' . $p->ID;
		foreach ( pm_qe_campos( $p ) as $campo => $valor ) {
			[ $nuevo, $quitados, $vistos ] = pm_qe_procesar( $valor, $quitar, $dejar );
			foreach ( $vistos as $host => [ $clase, $n ] ) {
				$inventario[ $host ][0]              = $clase;
				$inventario[ $host ][1]              = ( $inventario[ $host ][1] ?? 0 ) + $n;
				$inventario[ $host ][2][ $etiqueta ] = ( $inventario[ $host ][2][ $etiqueta ] ?? 0 ) + $n;
			}
			if ( $tocar && $quitados ) {
				if ( wp_strip_all_tags( $nuevo ) !== wp_strip_all_tags( $valor ) ) {
					WP_CLI::error( "$etiqueta ($campo): quitar el enlace cambiaría el texto; no escribo nada." );
				}
				$plan[ $p->ID ]['p']              = $p;
				$plan[ $p->ID ]['campos'][ $campo ] = $nuevo;
				foreach ( $quitados as $host => $n ) {
					$plan[ $p->ID ]['hosts'][ $host ] = ( $plan[ $p->ID ]['hosts'][ $host ] ?? 0 ) + $n;
				}
			}
		}
	}
	WP_CLI::log( "Escaneados $tipo: $n_tipo" . ( $tocar ? '' : ' (solo lectura, no se tocan)' ) );
	if ( $tocar ) {
		$escaneados += $n_tipo;
	}
}

// Dominios externos hallados, por clase.
$orden = [ 'quitar' => 1, 'duda' => 2, 'dejar' => 3, 'interno' => 4 ];
uksort( $inventario, function ( $a, $b ) use ( $inventario, $orden ) {
	return [ $orden[ $inventario[ $a ][0] ], -$inventario[ $a ][1], $a ] <=> [ $orden[ $inventario[ $b ][0] ], -$inventario[ $b ][1], $b ];
} );
WP_CLI::log( "\n=== DOMINIOS (clase | dominio | enlaces | posts) ===" );
foreach ( $inventario as $host => [ $clase, $total, $en ] ) {
	$lista = [];
	foreach ( $en as $et => $n ) {
		$lista[] = $et . '×' . $n;
	}
	$cortar = in_array( $clase, [ 'dejar', 'interno' ], true ) && count( $lista ) > 8;
	WP_CLI::log( sprintf( '%-7s | %s | %d | %s', strtoupper( $clase ), $host, $total, $cortar ? implode( ', ', array_slice( $lista, 0, 8 ) ) . ' … +' . ( count( $lista ) - 8 ) . ' posts' : implode( ', ', $lista ) ) );
}

// ---- 2) Resumen del plan ----
$enlaces_total = 0;
$por_host      = [];
foreach ( $plan as $id => $it ) {
	foreach ( $it['hosts'] as $h => $n ) {
		$enlaces_total += $n;
		$por_host[ $h ] = ( $por_host[ $h ] ?? 0 ) + $n;
	}
}
arsort( $por_host );
WP_CLI::log( "\n=== PLAN ===" );
WP_CLI::log( count( $plan ) . " posts con enlaces a quitar de $escaneados escaneados; $enlaces_total enlaces." );
foreach ( $por_host as $h => $n ) {
	WP_CLI::log( "  quitar $h: $n" );
}
foreach ( $plan as $id => $it ) {
	$p   = $it['p'];
	$det = [];
	foreach ( $it['hosts'] as $h => $n ) {
		$det[] = "{$h}×{$n}";
	}
	WP_CLI::log( sprintf( '%s: #%d %s:%s(%s) campos [%s]: %s', $modo, $id, substr( $p->post_type, 3 ), pm_clave_de( $id ), pm_idioma_de( $id ), implode( ', ', array_keys( $it['campos'] ) ), implode( ', ', $det ) ) );
}
$dudosos = array_keys( array_filter( $inventario, fn( $x ) => 'duda' === $x[0] ) );
WP_CLI::log( 'Dominios en duda (NO se quitan; a decidir): ' . ( $dudosos ? implode( ', ', $dudosos ) : 'ninguno' ) );

// ---- 3) Aplicar y leer de vuelta ----
$ok_total = 0;
if ( $aplicar ) {
	foreach ( $plan as $id => $it ) {
		$p = $it['p'];
		$args = [ 'ID' => $id ];
		foreach ( $it['campos'] as $campo => $nuevo ) {
			if ( 'post_content' === $campo || 'post_excerpt' === $campo ) {
				$args[ $campo ] = wp_slash( $nuevo );
			}
		}
		if ( count( $args ) > 1 ) {
			$r = wp_update_post( $args, true );
			if ( is_wp_error( $r ) ) {
				WP_CLI::error( "#$id: " . $r->get_error_message() );
			}
		}
		foreach ( $it['campos'] as $campo => $nuevo ) {
			if ( 'meta:' === substr( $campo, 0, 5 ) ) {
				update_post_meta( $id, substr( $campo, 5 ), wp_slash( $nuevo ) );
			}
		}
		clean_post_cache( $id );
		$leido = get_post( $id );
		$restos = 0;
		$mal    = 0;
		foreach ( $it['campos'] as $campo => $nuevo ) {
			$v = 'meta:' === substr( $campo, 0, 5 ) ? (string) get_post_meta( $id, substr( $campo, 5 ), true ) : (string) $leido->$campo;
			$restos += pm_qe_restos( $v, $quitar, $dejar );
			if ( $v !== $nuevo ) {
				$mal++;
			}
		}
		$ok = 0 === $restos && 0 === $mal;
		WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": #$id " . substr( $p->post_type, 3 ) . ':' . pm_clave_de( $id ) . '(' . pm_idioma_de( $id ) . "): href a dominios quitados que quedan: $restos; campos distintos de lo escrito: $mal" );
		if ( ! $ok ) {
			WP_CLI::error( "#$id: la lectura de vuelta no es la esperada." );
		}
		$ok_total++;
		usleep( 600000 );
	}
	// Barrido final: ningún post publicado de los tres tipos conserva un href a los dominios quitados.
	$restos_global = 0;
	foreach ( $tipos_tocar as $tipo ) {
		foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'publish', 'numberposts' => -1 ] ) as $p ) {
			foreach ( pm_qe_campos( $p ) as $v ) {
				$restos_global += pm_qe_restos( $v, $quitar, $dejar );
			}
		}
	}
	WP_CLI::log( ( 0 === $restos_global ? 'VERIFIED' : 'FALLÓ' ) . ": barrido final de $escaneados posts: href a dominios quitados que quedan: $restos_global" );
	0 === $restos_global || WP_CLI::error( 'Quedan enlaces a dominios quitados.' );
}

WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ': ' . count( $plan ) . " posts, $enlaces_total enlaces" . ( $aplicar ? ", $ok_total verificados." : '.' ) );
