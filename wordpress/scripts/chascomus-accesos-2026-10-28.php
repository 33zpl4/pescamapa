<?php
/**
 * Chascomús (es/en), 28/10/2026: "Dónde se pesca: la laguna y sus accesos" with what round 3 confirmed
 * (docs/investigacion/chascomus.md §12), plus the two matching FAQs, one new FAQ, two sources and the date.
 *
 * What is published (each with its source): the municipal note of 6/1/2026 (boyas, velocidad adecuada, bajadas y
 * botaduras SOLO en las bajadas náuticas habilitadas), the municipal tourism page (muelle de pescadores y mirador en la
 * costanera, "alquiler de botes para recreación o pesca deportiva", sin operador ni precio). Everything else the
 * round could not confirm (which ramps are enabled, costs, the municipal vessel fee, Prefectura requirements, who
 * rents boats, parking, accessibility) is NOT stated: the guide sends the reader to the tourism office
 * ((2241) 603414 / (2241) 430405, numbers given by the owner on 28/10/2026). No clubs, no private guarderías, no
 * prices, no fee amounts.
 *
 * Idempotent and careful: the section is replaced only while it still holds the sentence the first script wrote
 * (if someone edited it by hand, it is left alone and the log says so). Dry run first ($aplicar = false); run with
 * wp-script.yml (script = chascomus-accesos-2026-10-28).
 */

$aplicar = true;
$hoy     = '20261028';

function pm_ac_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_ac_h( string $texto ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$texto}</h2>\n<!-- /wp:heading -->";
}
function pm_ac_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_ac_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$turismo_url = 'https://turismo.chascomus.gob.ar/';
$laguna_url  = 'https://chascomus.gob.ar/turismo/seccion/24/chascomus-actual?subsec=calidaddevida';
$nota_url    = 'https://www.chascomus.gob.ar/digital/nota/12430/recomendaciones-para-disfrutar-la-laguna-de-forma-segura-y-responsable.html';
$tel         = '(2241) 603414 o (2241) 430405';

$pendiente_es = pm_ac_pendiente( 'accesos, ronda 3 (28/10/2026, docs/investigacion/chascomus.md §12): cuáles son las bajadas náuticas habilitadas y cuánto cuestan; la tasa municipal por embarcación (monto y si rige hoy); requisitos de Prefectura (registro, seguro, velocidad en nudos, zonas); quién alquila botes y a qué precio; estacionamiento y accesibilidad; si el muelle cobra. Las dos notas municipales citadas las pasó Gemini y no se abrieron desde el sandbox: el dueño las abre antes de darlas por confirmadas.' );
$pendiente_en = pm_ac_pendiente( 'access points, round 3 (28/10/2026, docs/investigacion/chascomus.md §12): which boat ramps are enabled and their cost; the municipal vessel fee (amount and whether it applies today); Prefectura requirements (registration, insurance, speed in knots, zones); who rents boats and at what price; parking and accessibility; whether the pier charges. The two municipal pages cited came from Gemini and were not opened from the sandbox: the owner opens them before they count as confirmed.' );

$secciones = [
	'es' => [
		'marca'  => 'No tenemos un inventario de rampas ni sus costos y horarios',
		'titulo' => 'Dónde se pesca: la laguna y sus accesos',
		'siguiente' => 'El viento manda',
		'html'   => implode( "\n\n", [
			pm_ac_h( 'Dónde se pesca: la laguna y sus accesos' ),
			pm_ac_p( 'Según el <a href="' . $laguna_url . '" rel="noopener">sitio de turismo de Chascomús</a>, la laguna es una de las elegidas para la pesca deportiva y los deportes náuticos, y tiene un camino de circunvalación de unos 30 km. El muelle de pescadores y mirador está en la zona céntrica de la costanera, de administración municipal.' ),
			pm_ac_lista( [
				'<strong>Pescar desde la costa:</strong> el muelle de pescadores y la costanera, en la zona céntrica. No encontramos publicado ningún cobro por usarlos; confirmalo en la Oficina de Turismo.',
				'<strong>Ir con tu propia embarcación:</strong> la Municipalidad pide hacer las bajadas y botaduras <strong>únicamente en las bajadas náuticas habilitadas</strong>, respetar las zonas marcadas con boyas y navegar a velocidad adecuada (<a href="' . $nota_url . '" rel="noopener">nota municipal del 6/1/2026</a>). Cuáles son esas bajadas, cuánto cuestan y qué requisitos rigen para navegar (registro, seguro, velocidad): <strong>no lo podemos confirmar</strong>.',
				'<strong>Alquilar un bote:</strong> el sitio municipal de turismo menciona el alquiler de botes para recreación o pesca deportiva, sin decir quién lo ofrece, ni precio ni condiciones.',
				'<strong>Guarderías y clubes:</strong> hay guarderías náuticas privadas y clubes sobre la costanera; sus tarifas no están publicadas y algunos clubes reservan sus embarcaciones a socios.',
				'<strong>Tasa municipal:</strong> no pudimos confirmar si se cobra una tasa por ingresar o bajar con una embarcación ni su monto.',
				'<strong>Estacionamiento y accesibilidad</strong> de cada acceso: no lo podemos confirmar.',
			] ),
			pm_ac_p( 'Para confirmar las bajadas habilitadas, los costos, los requisitos y el alquiler, llamá a la <strong>Oficina de Turismo de Chascomús: ' . $tel . '</strong>, o preguntale a tu guía antes de ir con tu embarcación.' ),
			$pendiente_es,
		] ),
		'faq_sin_lancha'  => '¿Dónde se puede pescar sin lancha en Chascomús?',
		'faq_prestadores' => '¿Hay guías de pesca y alquiler de lanchas?',
		'faq_nueva'       => '¿Dónde se puede bajar la lancha en Chascomús?',
		'faqs' => [
			'sin' => [ '¿Dónde se puede pescar sin lancha en Chascomús?', 'En el muelle de pescadores y mirador y sobre la costanera, en la zona céntrica, de administración municipal, según el sitio de turismo de Chascomús. No encontramos publicado ningún cobro por usarlos: confirmalo en la Oficina de Turismo, ' . $tel . '.' ],
			'pre' => [ '¿Hay guías de pesca y alquiler de lanchas?', 'El sitio municipal de turismo menciona el alquiler de botes para recreación o pesca deportiva, pero no dice quién lo ofrece ni a qué precio. No listamos guías ni lancheros sin su consentimiento o su sitio propio abierto: si ofrecés el servicio, escribinos a info@pescamapa.com. Para alquilar, consultá en la Oficina de Turismo: ' . $tel . '.' ],
			'nue' => [ '¿Dónde se puede bajar la lancha en Chascomús?', 'La Municipalidad pide hacer las bajadas y botaduras únicamente en las bajadas náuticas habilitadas (nota municipal del 6/1/2026). No pudimos confirmar cuáles son ni cuánto cuestan: preguntá en la Oficina de Turismo, ' . $tel . ', antes de ir con tu embarcación.' ],
		],
		'fuentes' => [
			[ 'Municipalidad de Chascomús: recomendaciones para disfrutar la laguna de forma segura y responsable (6/1/2026)', $nota_url, 1 ],
			[ 'Turismo de Chascomús: la laguna, pesca deportiva y muelle de pescadores', $laguna_url, 1 ],
		],
	],
	'en' => [
		'marca'  => 'We have no inventory of ramps with their costs and hours',
		'titulo' => 'Where to fish: the lagoon and its access points',
		'siguiente' => 'The wind decides',
		'html'   => implode( "\n\n", [
			pm_ac_h( 'Where to fish: the lagoon and its access points' ),
			pm_ac_p( 'According to the <a href="' . $laguna_url . '" rel="noopener">Chascomús tourism site</a>, the lagoon is one of those chosen for sport fishing and water sports, and has a ring road of about 30 km. The fishermen\'s pier and viewpoint is in the central part of the waterfront, run by the municipality.' ),
			pm_ac_lista( [
				'<strong>Fishing from the bank:</strong> the fishermen\'s pier and the waterfront, in the central area. We found no published charge for using them; confirm with the tourism office.',
				'<strong>Bringing your own boat:</strong> the municipality asks that boats be launched and recovered <strong>only at the enabled boat ramps</strong>, that you respect the areas marked with buoys and sail at an adequate speed (<a href="' . $nota_url . '" rel="noopener">municipal note of 6/1/2026</a>). Which ramps those are, what they cost and which requirements apply to sailing (registration, insurance, speed): <strong>we cannot confirm it</strong>.',
				'<strong>Renting a boat:</strong> the municipal tourism site mentions boat rental for recreation or sport fishing, without saying who offers it, nor the price or conditions.',
				'<strong>Boat storage and clubs:</strong> there are private boat storage yards and clubs along the waterfront; their rates are not published and some clubs keep their boats for members.',
				'<strong>Municipal fee:</strong> we could not confirm whether a fee is charged for entering or launching with a boat, or its amount.',
				'<strong>Parking and accessibility</strong> at each access point: we cannot confirm it.',
			] ),
			pm_ac_p( 'To confirm the enabled ramps, costs, requirements and rental, call the <strong>Chascomús Tourism Office: ' . $tel . '</strong>, or ask your guide before bringing your own boat.' ),
			$pendiente_en,
		] ),
		'faq_sin_lancha'  => 'Where can I fish without a boat in Chascomús?',
		'faq_prestadores' => 'Are there fishing guides and boat rentals?',
		'faq_nueva'       => 'Where can I launch a boat in Chascomús?',
		'faqs' => [
			'sin' => [ 'Where can I fish without a boat in Chascomús?', 'At the fishermen\'s pier and viewpoint and along the waterfront in the central area, run by the municipality, according to the Chascomús tourism site. We found no published charge for using them: confirm with the Tourism Office, ' . $tel . '.' ],
			'pre' => [ 'Are there fishing guides and boat rentals?', 'The municipal tourism site mentions boat rental for recreation or sport fishing, but does not say who offers it or at what price. We do not list guides or boatmen without their consent or their own open website: if you offer the service, write to info@pescamapa.com. To rent a boat, ask the Tourism Office: ' . $tel . '.' ],
			'nue' => [ 'Where can I launch a boat in Chascomús?', 'The municipality asks that boats be launched and recovered only at the enabled boat ramps (municipal note of 6/1/2026). We could not confirm which they are or what they cost: ask the Tourism Office, ' . $tel . ', before bringing your own boat.' ],
		],
		'fuentes' => [
			[ 'Municipality of Chascomús: tips to enjoy the lagoon safely and responsibly (6/1/2026)', $nota_url, 1 ],
			[ 'Chascomús Tourism: the lagoon, sport fishing and fishermen\'s pier', $laguna_url, 1 ],
		],
	],
];

foreach ( [ 'es', 'en' ] as $i ) {
	$s    = $secciones[ $i ];
	$ya   = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'chascomus', 'numberposts' => -1 ] );
	$guia = array_values( array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $i ) )[0] ?? null;
	if ( ! $guia ) {
		WP_CLI::warning( "chascomus ($i): no existe la guía" );
		continue;
	}
	$id        = $guia->ID;
	$contenido = $guia->post_content;
	$inicio    = strpos( $contenido, '<h2 class="wp-block-heading">' . $s['titulo'] . '</h2>' );
	$fin       = strpos( $contenido, '<h2 class="wp-block-heading">' . $s['siguiente'] . '</h2>' );
	if ( false === $inicio || false === $fin || $fin < $inicio || false === strpos( substr( $contenido, $inicio, $fin - $inicio ), $s['marca'] ) ) {
		WP_CLI::log( "chascomus ($i) #$id: la sección ya no es la original (nueva o editada a mano): no toco el cuerpo." );
		$cuerpo_nuevo = null;
	} else {
		// Back up to the "<!-- wp:heading -->" comment that opens each of the two headings.
		$desde = strrpos( substr( $contenido, 0, $inicio ), '<!-- wp:heading -->' );
		$hasta = strrpos( substr( $contenido, 0, $fin ), '<!-- wp:heading -->' );
		$cuerpo_nuevo = substr( $contenido, 0, $desde ) . $s['html'] . "\n\n" . substr( $contenido, $hasta );
	}

	$faqs   = pm_filas( 'faqs', $id );
	$tiene  = array_column( $faqs, 'pregunta' );
	$faqs_n = [];
	foreach ( $faqs as $f ) {
		if ( $f['pregunta'] === $s['faq_sin_lancha'] ) {
			$f = [ 'pregunta' => $s['faqs']['sin'][0], 'respuesta' => $s['faqs']['sin'][1] ];
		} elseif ( $f['pregunta'] === $s['faq_prestadores'] ) {
			$f = [ 'pregunta' => $s['faqs']['pre'][0], 'respuesta' => $s['faqs']['pre'][1] ];
		}
		$faqs_n[] = $f;
	}
	if ( ! in_array( $s['faq_nueva'], $tiene, true ) ) {
		$faqs_n[] = [ 'pregunta' => $s['faqs']['nue'][0], 'respuesta' => $s['faqs']['nue'][1] ];
	}

	$fuentes   = pm_filas( 'fuentes', $id );
	$urls      = array_column( $fuentes, 'url' );
	$fuentes_n = $fuentes;
	foreach ( $s['fuentes'] as $f ) {
		if ( ! in_array( $f[1], $urls, true ) ) {
			$fuentes_n[] = [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ];
		}
	}

	WP_CLI::log( "chascomus ($i) #$id: " . ( $aplicar ? 'se actualiza' : 'se actualizaría' ) . ' — cuerpo ' . ( null === $cuerpo_nuevo ? 'sin cambios' : 'reemplazado' ) . ', preguntas ' . count( $faqs ) . ' → ' . count( $faqs_n ) . ', fuentes ' . count( $fuentes ) . ' → ' . count( $fuentes_n ) );
	if ( ! $aplicar ) {
		continue;
	}
	if ( null !== $cuerpo_nuevo ) {
		wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $cuerpo_nuevo ) ] );
	}
	update_field( 'faqs', $faqs_n, $id );
	update_field( 'fuentes', $fuentes_n, $id );
	update_field( 'actualizado', $hoy, $id );

	$leido = get_post( $id )->post_content;
	WP_CLI::log( '  VERIFIED: cuerpo ' . ( false !== strpos( $leido, '<strong>Oficina de Turismo' ) || false !== strpos( $leido, 'Tourism Office:' ) ? 'con la oficina de turismo' : 'SIN la oficina de turismo (revisar)' )
		. ', ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas, ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes, actualizado ' . pm_campo( 'actualizado', $id ) );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
