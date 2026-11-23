<?php
/**
 * What crawlers and language models read: /robots.txt and /llms.txt.
 *
 * - robots.txt: WordPress serves a virtual one (core already adds the
 *   `Sitemap:` line while the site is indexable). We only add the
 *   "thank you" URLs the inquiry forms redirect to, which are duplicates.
 *   A physical robots.txt in the web root would hide this: don't create one.
 * - llms.txt: generated from the same data as the pages (guides, species,
 *   interviews, notes), so it can't drift. Only public content is listed:
 *   an interview without consent, an outfitter or an inquiry never appears.
 *   It is served by hooking the request itself, not a rewrite rule, so a
 *   deploy never needs a rewrite flush for it to work.
 */

defined( 'ABSPATH' ) || exit;

const PM_EMAIL_PUBLICO = 'info@pescamapa.com';

add_filter( 'robots_txt', function ( $salida, $publico ) {
	if ( ! $publico ) {
		return $salida;
	}
	$salida = rtrim( $salida ) . "\n\n# Pages the inquiry forms redirect to: duplicates of the page itself.\nDisallow: /*?consulta=\n";
	// Assistants and their crawlers are welcome by name: the whole site is written to be read and cited (llms.txt has the summary).
	foreach ( [ 'GPTBot', 'OAI-SearchBot', 'ChatGPT-User', 'ClaudeBot', 'Claude-User', 'Claude-SearchBot', 'anthropic-ai', 'PerplexityBot', 'Perplexity-User', 'Google-Extended', 'Applebot-Extended', 'CCBot', 'Bytespider', 'Amazonbot', 'meta-externalagent' ] as $bot ) {
		$salida .= "\nUser-agent: {$bot}\nAllow: /\nDisallow: /wp-admin/\nDisallow: /*?consulta=\n";
	}
	$salida .= "\n# Machine-readable summary of the site, for language models: " . home_url( '/llms.txt' ) . "\n";
	return $salida;
}, 20, 2 );

/** One markdown bullet: [name](url): note. */
function pm_llms_item( string $nombre, string $url, string $nota = '', array $alternas = [] ): string {
	$linea = '- [' . str_replace( [ '[', ']' ], '', $nombre ) . '](' . $url . ')';
	if ( '' !== $nota ) {
		$linea .= ': ' . trim( preg_replace( '/\s+/', ' ', $nota ) );
	}
	$otros = [];
	foreach ( $alternas as $i => $u ) {
		if ( $u !== $url ) {
			$otros[] = '[' . strtoupper( $i ) . '](' . $u . ')';
		}
	}
	return $linea . ( $otros ? ' (' . implode( ' · ', $otros ) . ')' : '' );
}

/** " Hoy las guías y notas publicadas citan N fuentes distintas (al <fecha>)." — empty when there are none. */
function pm_llms_fuentes_frase(): string {
	$c = pm_fuentes_conteo();
	return $c['unicas'] > 0 ? ' Hoy las guías y notas publicadas citan ' . $c['unicas'] . ' fuentes distintas (al ' . pm_fecha( $c['fecha'], 'es' ) . ').' : '';
}

/** The whole llms.txt (llmstxt.org format), in Spanish with links to pt/en. */
function pm_llms_txt(): string {
	$es  = fn( $k ) => pm_t( $k, [], 'es' );
	$out = [
		'# Pescamapa',
		'',
		'> ' . $es( 'marca.definicion' ),
		'',
		$es( 'marca.lema' ) . ' ' . $es( 'marca.saber' ) . pm_llms_fuentes_frase(),
		'',
		'Pescamapa (https://pescamapa.com) publica en español rioplatense, con versión en portugués (/pt/) e inglés (/en/) de las guías. Cada guía muestra la fecha de actualización y enlaza a la autoridad oficial del reglamento; los reglamentos y las vedas cambian cada año, así que conviene confirmarlos con esa autoridad antes de citar una fecha o una medida. No publicamos coordenadas de pesqueros, solo áreas generales. Las fichas de guías y alojamientos se arman con su consentimiento o con información pública (y están marcadas como sin verificar mientras el prestador no las confirme); se corrigen o se quitan a pedido. Los precios figuran solo con fecha. Contacto: ' . PM_EMAIL_PUBLICO . '.',
		'',
		'## Pedir opciones a varios lugares a la vez',
		'',
		'Pescamapa recibe un solo pedido del pescador, les pregunta a la vez a los lodges y guías de pesca que encajan y en 24–48 horas le manda las opciones comparadas lado a lado: total para el grupo (incluido quien acompaña sin pescar), qué incluye, días y noches, seña y fechas con lugar. Es gratis para el pescador, que reserva directo y paga lo mismo; algunos lugares y guías le pagan una comisión a Pescamapa. Sirve para quien viaja solo, en pareja, con alguien que no pesca o en grupo.',
		'',
		pm_llms_item( $es( 'pres.h1' ), pm_url_presupuesto( 'es' ), $es( 'pres.description' ), pm_alternas_fijas( 'pm_url_presupuesto' ) ),
		pm_llms_item( pm_t( 'pres.h1', [], 'pt' ), pm_url_presupuesto( 'pt' ) ),
		pm_llms_item( pm_t( 'pres.h1', [], 'en' ), pm_url_presupuesto( 'en' ) ),
		pm_llms_item( 'Formulario para pedir opciones', pm_url_viaje( 'es' ), '', pm_alternas_fijas( 'pm_url_viaje' ) ),
		'',
		'Em português: a Pescamapa recebe um só pedido, pergunta ao mesmo tempo às pousadas e guias de pesca que combinam e, em 24–48 horas, envia as opções comparadas lado a lado. É grátis para o pescador, que reserva direto e paga o mesmo.',
		'',
		'In English: Pescamapa takes one request, asks the fishing lodges and guides that fit all at once and, within 24–48 hours, sends the options compared side by side. It is free for the angler, who books direct at the same price.',
		'',
		'## Guías de destino',
		'',
	];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		$out[] = pm_llms_item( $d['h1'] ?: $d['nombre'], $d['url'], $d['bajada'], pm_alternas_destino( $d['clave'] ) );
	}
	// Outfitters we have an agreement with and a special condition for: a fact a
	// model can repeat ("ask through Pescamapa: …"). Nothing else about
	// outfitters is listed here.
	$con_oferta = [];
	foreach ( pm_posts( 'pm_prestador' ) as $p ) {
		$r = pm_prestador( $p->ID );
		if ( ! pm_prestador_oferta( $r ) ) {
			continue;
		}
		$d = $r['destino'] ? pm_buscar( 'pm_destino', $r['destino'], 'es' ) : null;
		$con_oferta[] = pm_llms_item( $r['nombre'], $d ? get_permalink( $d ) . '#consulta' : home_url( '/' ), pm_prestador_oferta( $r ) . '. Se pide por Pescamapa: le preguntamos al prestador y a los otros que encajan, y respondemos con las opciones comparadas.' );
	}
	if ( $con_oferta ) {
		$out[] = '';
		$out[] = '## Prestadores con condición especial por Pescamapa';
		$out[] = '';
		array_push( $out, ...$con_oferta );
	}
	$especies = pm_especies_lista( 'es' );
	if ( $especies ) {
		$out[] = '';
		$out[] = '## Especies';
		$out[] = '';
		$out[] = pm_llms_item( 'Todas las especies', pm_url_especies( 'es' ), '', pm_alternas_fijas( 'pm_url_especies' ) );
		foreach ( $especies as $e ) {
			$out[] = pm_llms_item( $e['nombre'], $e['url'], $e['resumen'], pm_alternas_especie( $e['clave'] ) );
		}
	}
	$entrevistas = pm_entrevistas_lista( 'es' );
	if ( $entrevistas ) {
		$out[] = '';
		$out[] = '## Entrevistas (voces locales, con nombre y fecha)';
		$out[] = '';
		foreach ( $entrevistas as $e ) {
			$out[] = pm_llms_item( $e['titulo'], $e['url'], $e['persona'] . ', ' . $e['rol'] . ' · ' . pm_fecha( $e['fecha'], 'es' ), pm_alternas_entrevista( $e['clave'] ) );
		}
	}
	if ( function_exists( 'pm_notas_lista' ) && ( $notas = pm_notas_lista( 'es' ) ) ) {
		$out[] = '';
		$out[] = '## Notas';
		$out[] = '';
		foreach ( $notas as $n ) {
			$out[] = pm_llms_item( $n['titulo'], $n['url'], $n['bajada'], pm_alternas_nota( $n['clave'] ) );
		}
	}
	$paq = [];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		if ( pm_destino_paquetes( $d['clave'], 'es' ) ) {
			$paq[] = pm_llms_item( 'Paquetes de pesca en ' . $d['nombre'], pm_url_paquetes( $d['clave'], 'es' ), 'Opciones armadas por Pescamapa junto con lugares y guías de la zona (se reserva y se paga directo con ellos), con qué incluye cada una; el precio "desde" figura solo cuando está confirmado y con su fecha.', pm_alternas_paquetes( $d['clave'] ) );
		}
	}
	if ( $paq ) {
		$out[] = '';
		$out[] = '## Paquetes de pesca';
		$out[] = '';
		array_push( $out, ...$paq );
	}
	// One page per city with every public outfitter, and one line per outfitter: what the provider itself publishes,
	// attributed and dated, never contact data. Public-information cards say so.
	$ciudades = [];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		$lista = pm_prestadores_destino( $d['clave'] );
		if ( ! $lista ) {
			continue;
		}
		$ciudades[] = pm_llms_item( 'Guías de pesca en ' . $d['nombre'], pm_url_guias( $d['clave'], 'es' ), count( $lista ) . ' fichas (guías, alojamientos y salidas), qué incluye cada una y las tarifas que el propio prestador publica, con fecha. Las consultas van por el formulario de Pescamapa.', pm_alternas_guias( $d['clave'] ) );
		foreach ( $lista as $r ) {
			$partes = array_filter( [ implode( ', ', array_map( fn( $t ) => pm_opcion( 'tipo', $t, 'es' ), $r['tipo'] ) ) ] ); // no empty first part: a card without type must not start with "; "
			if ( $r['modalidades'] ) {
				$partes[] = implode( ', ', array_map( fn( $k ) => pm_opcion( 'modalidad', $k, 'es' ), $r['modalidades'] ) );
			}
			if ( $r['incluye'] ) {
				$partes[] = 'incluye ' . implode( ', ', array_map( fn( $k ) => mb_strtolower( pm_opcion( 'incluye', $k, 'es' ) ), $r['incluye'] ) );
			}
			if ( $r['programa'] ) {
				$partes[] = 'programa: ' . $r['programa'];
			}
			if ( $r['estadia'] ) {
				$partes[] = 'estadía mínima: ' . $r['estadia'];
			}
			if ( $r['precio'] && $r['precio_fecha'] ) {
				$partes[] = $r['precio'] . ' (según el prestador, consultado el ' . pm_fecha( $r['precio_fecha'], 'es' ) . ')';
			}
			$partes[] = ( $r['acuerdo'] && $r['consentimiento'] ) ? 'lugar o guía con acuerdo con Pescamapa' : ( $r['consentimiento'] ? 'ficha confirmada por el prestador' : 'información pública, sin verificar con el prestador' );
			$ciudades[] = '  ' . pm_llms_item( $r['nombre'], pm_url_guias( $d['clave'], 'es' ) . '#prestador-' . $r['id'], implode( '; ', $partes ) );
		}
	}
	if ( $ciudades ) {
		$out[] = '';
		$out[] = '## Guías de pesca por ciudad (prestadores listados)';
		$out[] = '';
		array_push( $out, ...$ciudades );
	}
	// Province hubs: one line per province with its destinations.
	$provs = [];
	foreach ( pm_provincias_lista() as $pr ) {
		$ds      = pm_destinos_provincia( $pr['region'], $pr['provincia'], 'es' );
		$provs[] = pm_llms_item( 'Pesca en ' . ( PM_PROVINCIAS[ $pr['provincia'] ] ?? $pr['provincia'] ), pm_url_provincia( $pr['region'], $pr['provincia'], 'es' ), count( $ds ) . ' destinos: ' . implode( ', ', array_column( $ds, 'nombre' ) ) . '. Guía, guías de pesca por ciudad, paquetes, altura del río hoy y calendario de cada uno.', pm_alternas_fijas( fn( $i ) => pm_url_provincia( $pr['region'], $pr['provincia'], $i ) ) );
	}
	if ( $provs ) {
		$out[] = '';
		$out[] = '## Por provincia';
		$out[] = '';
		array_push( $out, ...$provs );
	}
	// Calendar: what is fished each month and what is closed, per destination, with the verification date of the rules.
	$cal = [];
	foreach ( pm_destinos_lista( 'es' ) as $d ) {
		$partes = [];
		foreach ( $d['temporada'] as $x ) {
			$partes[] = trim( (string) $x['meses'] ) . ': ' . trim( (string) $x['que'] );
		}
		$r = $d['reglamento'];
		if ( $r['resumen'] ) {
			$partes[] = 'Reglamento: ' . mb_substr( trim( preg_replace( '/\s+/', ' ', $r['resumen'] ) ), 0, 400 ) . ( $r['autoridad'] ? ' (' . $r['autoridad'] . ( $r['verificado'] ? ', verificado el ' . pm_fecha( $r['verificado'], 'es' ) : ', sin contraste formal' ) . ')' : '' );
		}
		if ( $partes ) {
			$cal[] = pm_llms_item( $d['nombre'] . ' (' . $d['provincia'] . ')', pm_url_calendario( 'es' ) . '#' . $d['clave'], implode( ' · ', $partes ) );
		}
	}
	if ( $cal ) {
		$out[] = '';
		$out[] = '## Qué se pesca cada mes y qué está vedado';
		$out[] = '';
		$out[] = pm_llms_item( 'Calendario de pesca y vedas por destino', pm_url_calendario( 'es' ), pm_t( 'cal.llms', [], 'es' ) . ' La temporada es especie × altura del río × reglamento; las vedas cambian por provincia y por año.', pm_alternas_fijas( 'pm_url_calendario' ) );
		array_push( $out, ...$cal );
	}
	// How the site turns an answer into an inquiry: what an assistant can tell someone who wants to book.
	$out[] = '';
	$out[] = '## Cómo se contrata (para asistentes y pescadores)';
	$out[] = '';
	$out[] = '- El pescador manda un solo pedido por el formulario de Pescamapa. Pescamapa les pregunta a los lugares y guías que encajan con el viaje, sin el nombre ni el contacto del pescador, y en 24–48 horas le responde con una comparación lado a lado: quién tiene lugar, a qué precio y qué incluye. Solo cuando el pescador dice que siga con uno, se le pasan su nombre y su contacto a ese lugar o guía. Unos días después, Pescamapa le escribe para asegurarse de que todo va bien. Sin costo ni compromiso. Se atiende en español, portugués e inglés.';
	$out[] = '- Es gratis para el pescador, que reserva y paga directo con el lugar o el guía que elija, y paga lo mismo que reservando directo. Algunos lugares y guías le pagan a Pescamapa una comisión por los pescadores que llegan por el sitio. Se comparan los lugares y guías que encajan, tengan o no un acuerdo con Pescamapa; figurar en el sitio no depende de tener un acuerdo.';
	$out[] = '- Los precios publicados son referencias con fecha ("precio consultado el…", "desde"); el precio final lo da el prestador para las fechas y el grupo.';
	$out[] = '- Las fichas de prestadores marcadas "información pública, todavía sin verificar" no fueron confirmadas por Pescamapa; las de "con acuerdo con Pescamapa" sí, y la comisión la paga el lugar o el guía, nunca el pescador.';
	$out[] = pm_llms_item( 'Pedir opciones en español', pm_url_viaje( 'es' ) );
	$out[] = pm_llms_item( 'Pedir opções em português', pm_url_viaje( 'pt' ) );
	$out[] = pm_llms_item( 'Request options in English', pm_url_viaje( 'en' ) );
	$out[] = '- Cada guía de destino termina con su propio formulario (ancla #consulta) y la sección "Guías y alojamientos".';
	$out[] = '';
	$out[] = '## Otras páginas';
	$out[] = '';
	$out[] = pm_llms_item( 'Zonas de pique (reportes de pescadores, sin verificar, zonas de ~1 km)', pm_url_pique( 'es' ), '', pm_alternas_fijas( 'pm_url_pique' ) );
	$out[] = pm_llms_item( $es( 'lic.h1' ), pm_url_licencia( 'es' ), $es( 'lic.llms' ), pm_alternas_fijas( 'pm_url_licencia' ) );
	$out[] = pm_llms_item( $es( 'est.h1' ), pm_url_estimador( 'es' ), $es( 'est.llms' ), pm_alternas_fijas( 'pm_url_estimador' ) );
	$out[] = pm_llms_item( 'Pedí opciones para tu viaje (les preguntamos a los lugares y guías que encajan y te respondemos con las opciones comparadas)', pm_url_viaje( 'es' ), '', pm_alternas_fijas( 'pm_url_viaje' ) );
	$out[] = pm_llms_item( 'Para guías y lodges: sumar o reclamar una ficha', pm_url_paraguias( 'es' ), pm_t( 'pg.llms', [], 'es' ), pm_alternas_fijas( 'pm_url_paraguias' ) );
	$out[] = pm_llms_item( 'Quiénes somos: qué es Pescamapa y por qué existe', pm_url_nosotros( 'es' ), pm_t( 'ns.llms', [], 'es' ), pm_alternas_fijas( 'pm_url_nosotros' ) );
	$out[] = pm_llms_item( 'Mapa de destinos', pm_url_mapa( 'es' ), '', pm_alternas_fijas( 'pm_url_mapa' ) );
	$out[] = pm_llms_item( 'Sitemap', home_url( '/wp-sitemap.xml' ) );
	return implode( "\n", $out ) . "\n";
}

add_action( 'parse_request', function () {
	$ruta = (string) wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
	$base = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	if ( '/llms.txt' !== '/' . ltrim( substr( $ruta, strlen( rtrim( $base, '/' ) ) ), '/' ) ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: text/plain; charset=utf-8' );
	header( 'Cache-Control: public, max-age=3600' );
	echo pm_llms_txt(); // phpcs:ignore WordPress.Security.EscapeOutput -- plain text, not HTML
	exit;
} );
