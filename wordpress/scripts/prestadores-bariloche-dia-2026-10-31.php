<?php
/**
 * Bariloche, 31/10/2026: four day-charter operators as "listado con información pública" (level 1, docs/prestadores.md), the first
 * piece of P1 in docs/ideas-contenido-seo-2026-10-31.md (silo B: guides and day trips, the top-scored content). Everything comes from
 * each operator's own website, read on 31/10/2026 through investigar.yml (branch investigacion, folder diy-guias-precios-20261031-0714);
 * written in our words, attributed ("según el operador"), with the rate dated. What a site does not say is not filled in
 * (Patagonia Drift does not say whether the licence is included; two sites do not state the guides' languages). No contact is
 * published (the card never shows it); the card carries the "public information, not yet verified" mark and the correction/removal
 * link. Owner's rule (29/10/2026): list now, verify later; the risk is a false claim, not listing.
 *
 * Fields: Spanish base texts + English versions (descripcion_en, precio_en); Portuguese falls back to Spanish. Idempotent by title.
 * Dry run first ($aplicar = false); run with wp-script.yml (script = prestadores-bariloche-dia-2026-10-31).
 */

$aplicar = true;
$hoy     = '20261031';

$nota_interna = 'Listado con información pública (sitio del operador leído el 31/10/2026 por investigar.yml). Pescamapa no confirmó contacto, disponibilidad ni habilitación con el registro provincial. Falta contactarlo y cruzarlo con el directorio de prestadores de Neuquén / Río Negro.';

$prestadores = [
	[
		'nombre'   => 'Trout Bariloche',
		'tipo'     => [ 'guia', 'operador' ],
		'fuente'   => 'https://troutbariloche.com/day-trips/',
		'loc'      => 'Bariloche, Río Negro',
		'es'       => 'Guías locales de Bariloche con salidas privadas de día completo y medio día en el Limay, el Manso y los lagos del Nahuel Huapi, en bote de deriva o vadeando. Según su sitio (leído el 31/10/2026): día completo en el Limay US$ 520 por bote para 1 o 2 pescadores, con traslado desde Bariloche, equipo de pesca y almuerzo junto al río; un tercer pescador paga US$ 60; la licencia va aparte (US$ 40) y la tramitan. Guías que hablan inglés.',
		'en'       => 'Local Bariloche guides running private full-day and half-day trips on the Limay, the Manso and the Nahuel Huapi lakes, from a drift boat or wading. According to its website (read on 31 October 2026): a full day on the Limay is USD 520 per boat for 1 or 2 anglers, with transport from Bariloche, fishing gear and a riverside lunch; a third angler pays USD 60; the licence is extra (USD 40) and they arrange it. English-speaking guides.',
		'precio'   => 'Desde US$ 520 por bote, día completo en el Limay, 1 o 2 pescadores (licencia aparte)',
		'precio_en' => 'From USD 520 per boat, full day on the Limay, 1 or 2 anglers (licence extra)',
		'incluye'  => [ 'traslados', 'equipos', 'comida', 'licencia' ],
		'equipo'   => [ 'lancha', 'equipos' ],
		'idiomas'  => [ 'en', 'es' ],
	],
	[
		'nombre'   => 'Southern Rivers Patagonia',
		'tipo'     => [ 'guia', 'operador' ],
		'fuente'   => 'https://southernriverspatagonia.com/limay-full-day-fly-fishing',
		'loc'      => 'Bariloche, Río Negro',
		'es'       => 'Guías y outfitter de Bariloche: día completo en el Limay con guía privado y bote para 1 o 2 pescadores, flotando y parando a vadear cuando el río lo permite. Según su sitio (leído el 31/10/2026): US$ 530 en total por 1 o 2 pescadores, con traslados desde el alojamiento en Bariloche, equipo y almuerzo argentino junto al río; la licencia (US$ 40 por persona y día, la pueden tramitar) y las propinas van aparte. Dice que abril y mayo son los mejores meses para las marrones migratorias.',
		'en'       => 'Bariloche guides and outfitter: a full day on the Limay with a private guide and a boat for 1 or 2 anglers, floating and stopping to wade when the river allows. According to its website (read on 31 October 2026): USD 530 in total for 1 or 2 anglers, with transfers from your accommodation in Bariloche, gear and an Argentine riverside lunch; the licence (USD 40 per person per day, they can arrange it) and gratuities are extra. It says April and May are the best months for migratory browns.',
		'precio'   => 'US$ 530 en total por 1 o 2 pescadores, día completo en el Limay (licencia y propinas aparte)',
		'precio_en' => 'USD 530 in total for 1 or 2 anglers, full day on the Limay (licence and gratuities extra)',
		'incluye'  => [ 'traslados', 'equipos', 'comida', 'bebidas', 'licencia' ],
		'equipo'   => [ 'lancha', 'equipos' ],
		'idiomas'  => [],
	],
	[
		'nombre'   => 'Patagonia in the Loop',
		'tipo'     => [ 'guia', 'operador' ],
		'fuente'   => 'https://www.patagoniaintheloop.com/',
		'loc'      => 'Bariloche, Río Negro',
		'es'       => 'Servicio de guías de pesca con mosca con base en Bariloche, en los ríos y lagos del Parque Nacional Nahuel Huapi: Limay, Manso y los lagos de la cuenca del Manso (Roca, Fonck, Mascardi, Hess). Según su sitio (leído el 31/10/2026): medio día US$ 430 y día completo US$ 550 para 1 o 2 pescadores, con traslados ida y vuelta, bote de deriva (balsa en el Manso), equipo y almuerzo junto al río; el Manso suma US$ 100 de logística; la licencia (US$ 35, la tramitan) y la entrada al Parque para los lagos del Manso (US$ 25) van aparte. Guía bilingüe; declara habilitación en el Parque y en Neuquén.',
		'en'       => 'Fly fishing guide service based in Bariloche, on the rivers and lakes of Nahuel Huapi National Park: the Limay, the Manso and the Manso basin lakes (Roca, Fonck, Mascardi, Hess). According to its website (read on 31 October 2026): half day USD 430 and full day USD 550 for 1 or 2 anglers, with round-trip transfers, a drift boat (raft on the Manso), gear and a riverside lunch; Manso trips add USD 100 for logistics; the licence (USD 35, they arrange it) and the park entrance fee for the Manso lakes (USD 25) are extra. Bilingual guide; states it is authorised in the Park and in Neuquén.',
		'precio'   => 'Día completo US$ 550 y medio día US$ 430 por 1 o 2 pescadores (licencia aparte)',
		'precio_en' => 'Full day USD 550 and half day USD 430 for 1 or 2 anglers (licence extra)',
		'incluye'  => [ 'traslados', 'equipos', 'comida', 'bebidas', 'licencia' ],
		'equipo'   => [ 'lancha', 'equipos' ],
		'idiomas'  => [ 'en', 'es' ],
	],
	[
		'nombre'   => 'Patagonia Drift Fly Fishing',
		'tipo'     => [ 'guia', 'operador' ],
		'fuente'   => 'https://www.patagoniadrift.com.ar/en/fly-fishing-day-trips/',
		'loc'      => 'Bariloche, Río Negro',
		'es'       => 'Salidas de un día desde Bariloche en bote de deriva: flotada del Limay y lagos del Parque Nacional (Mascardi, Hess, Roca, Fonck, Guillelmo); también programas con lodge y expediciones de varios días con campamento. Según su sitio (leído el 31/10/2026): flotada del Limay US$ 550 y lagos US$ 580, día completo para 1 o 2 pescadores, con almuerzo junto al río con vino y bebidas; máximo dos pescadores por guía y bote; prestan waders y equipo si se avisa. Su página no dice si la licencia está incluida: preguntar.',
		'en'       => 'Day trips from Bariloche by drift boat: the Limay float and the National Park lakes (Mascardi, Hess, Roca, Fonck, Guillelmo); also lodge programmes and multi-day camping expeditions. According to its website (read on 31 October 2026): the Limay float is USD 550 and the lakes USD 580, full day for 1 or 2 anglers, with a riverside lunch with wine and drinks; at most two anglers per guide and boat; waders and gear provided on request. Its page does not say whether the licence is included: ask.',
		'precio'   => 'Flotada del Limay US$ 550 y lagos US$ 580, día completo por 1 o 2 pescadores',
		'precio_en' => 'Limay float USD 550 and lakes USD 580, full day for 1 or 2 anglers',
		'incluye'  => [ 'comida', 'bebidas', 'equipos' ],
		'equipo'   => [ 'lancha', 'equipos' ],
		'idiomas'  => [],
	],
];

$destino = pm_buscar( 'pm_destino', 'bariloche', 'es' );
$destino || WP_CLI::error( 'No encontré la guía bariloche (es).' );

$creados = [];
foreach ( $prestadores as $p ) {
	$ya = get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $p['nombre'], 'numberposts' => 1, 'fields' => 'ids' ] );
	if ( $ya ) {
		WP_CLI::log( "· {$p['nombre']} ya existe (#{$ya[0]}): no se toca" );
		continue;
	}
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$p['nombre']} → ficha pública sin verificar en bariloche; precio «{$p['precio']}» ($hoy); incluye " . implode( ',', $p['incluye'] ) . '; idiomas ' . ( $p['idiomas'] ? implode( ',', $p['idiomas'] ) : 'sin dato' ) );
	if ( ! $aplicar ) {
		continue;
	}
	$pid = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $p['nombre'] ], true );
	if ( is_wp_error( $pid ) ) {
		WP_CLI::error( $pid->get_error_message() );
	}
	$f = [
		'tipo' => $p['tipo'], 'prest_destino' => 'bariloche', 'localidad' => $p['loc'],
		'descripcion' => $p['es'], 'descripcion_en' => $p['en'],
		'prest_modalidades' => [ 'mosca' ], 'prest_especies' => [ 'trucha' ], 'equipo' => $p['equipo'], 'capacidad' => 2,
		'lancha' => 'Bote de deriva', 'lancha_en' => 'Drift boat', 'motor' => '', 'prest_idiomas' => $p['idiomas'],
		'incluye' => $p['incluye'], 'precio' => $p['precio'], 'precio_en' => $p['precio_en'], 'precio_fecha' => $hoy,
		'prest_publico' => 1, 'prest_fuente' => $p['fuente'], 'estado' => 'relevado', 'verificado' => $hoy,
		'datos_ejemplo' => 0, 'prest_consentimiento' => 0, 'web' => $p['fuente'], 'notas' => $nota_interna,
	];
	foreach ( $f as $k => $v ) {
		update_field( $k, $v, $pid );
	}
	$creados[ $p['nombre'] ] = $pid;
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}

// Read-back: each card is public, hangs from bariloche, shows its dated price, and the internal note never leaves the card data.
$lista = pm_prestadores_destino( 'bariloche', false );
$ok    = true;
foreach ( $creados as $nombre => $pid ) {
	$r = null;
	foreach ( $lista as $x ) {
		if ( (int) $x['id'] === (int) $pid ) {
			$r = $x;
		}
	}
	$bien = $r && $r['precio'] && $r['precio_fecha'] === '2026-10-31' && pm_prestador_publico( $r ) && ! $r['acuerdo'];
	WP_CLI::log( ( $bien ? 'VERIFIED' : 'FALLÓ' ) . ": $nombre #$pid " . ( $r ? "precio «{$r['precio']}» ({$r['precio_fecha']}), público, sin acuerdo" : 'no aparece en la lista de bariloche' ) );
	$ok = $ok && $bien;
}
$ok || WP_CLI::error( 'alguna ficha no se leyó de vuelta como se esperaba.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO: ' . count( $creados ) . ' fichas' );
