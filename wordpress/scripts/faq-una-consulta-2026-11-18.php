<?php
/**
 * Hub «Cómo preparar un viaje de pesca a la Argentina» (clave como-preparar-viaje-pesca-argentina, es/en/pt): suma dos preguntas a su FAQ (18/11/2026).
 * Decisión del dueño (18/11/2026): vender la utilidad («una sola consulta, en vez de diez»), no la etiqueta «comparador». La FAQ del hub vive en el contenido
 * (grupo pm-faq: es la FAQ visible y la fuente del JSON-LD FAQPage, pm_faqs_contenido()), por eso se agrega acá y no en ui.json. Mismo texto que
 * viaje.faq.1 y viaje.faq.2 de ui.json (formulario /armar-viaje/).
 * Solo toca post_content de las tres notas y el campo 'actualizado'. Idempotente (si la pregunta ya está, no hace nada). Lee de vuelta (VERIFIED por idioma).
 * Simulacro por defecto ($aplicar = false). Con wp-script.yml (script = faq-una-consulta-2026-11-18; aplicar=true para la corrida real).
 */

$aplicar = false;
$hoy     = '20261118';
$clave   = 'como-preparar-viaje-pesca-argentina';

$preguntas = [
	'es' => [
		[ '¿Tengo que escribirle a cada lugar uno por uno?', 'No: con una sola consulta preguntamos por vos a los lugares y guías que encajan con tu viaje y te respondemos en hasta 48 horas con las opciones lado a lado. Es gratis.' ],
		[ '¿Hay alguna forma de comparar lugares y guías de pesca en la Argentina?', 'Sí: eso hacemos. Comparamos los lugares y guías con los que hablamos que encajan con tu viaje, con las condiciones y precios que nos da cada uno. No vendemos el viaje: reservás directo.' ],
	],
	'en' => [
		[ 'Do I have to write to each place one by one?', 'No. With a single inquiry we ask the places and guides that fit your trip on your behalf, and we reply within 48 hours with the options side by side. It is free.' ],
		[ 'Is there a way to compare fishing places and guides in Argentina?', 'Yes, that is what we do. We compare the places and guides we talk to that fit your trip, using the conditions and prices each one gives us. We do not sell the trip: you book directly.' ],
	],
	'pt' => [
		[ 'Preciso escrever para cada lugar, um por um?', 'Não: com uma só consulta, perguntamos por você aos lugares e guias que combinam com a sua viagem e respondemos em até 48 horas com as opções lado a lado. É grátis.' ],
		[ 'Existe alguma forma de comparar lugares e guias de pesca na Argentina?', 'Sim, é isso que fazemos. Comparamos os lugares e guias com os quais conversamos que combinam com a sua viagem, com as condições e os preços que cada um nos passa. Não vendemos a viagem: você reserva direto.' ],
	],
];

function pm_fu_item( string $q, string $a ): string {
	return "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
}

$cambios = 0;
foreach ( $preguntas as $i => $lista ) {
	$p = pm_buscar( 'pm_nota', $clave, $i );
	if ( ! $p ) {
		WP_CLI::warning( "hub ($i): no existe, se salta." );
		continue;
	}
	$c     = $p->post_content;
	$nuevo = $c;
	$deben = [];
	$hay   = preg_match( '#<div class="[^"]*\bpm-faq\b[^"]*">#u', $c );
	WP_CLI::log( "hub ($i) #{$p->ID}: grupo pm-faq " . ( $hay ? 'presente' : 'AUSENTE' ) . ', ' . count( pm_faqs_contenido( $c ) ) . ' preguntas hoy.' );
	if ( ! $hay ) {
		WP_CLI::warning( "hub ($i): sin grupo pm-faq, no se toca." );
		continue;
	}
	foreach ( $lista as [ $q, $a ] ) {
		if ( false !== strpos( $nuevo, $q ) ) {
			WP_CLI::log( "  ya está: {$q}" );
			continue;
		}
		$n     = 0;
		$nuevo = preg_replace_callback( '#(<div class="[^"]*\bpm-faq\b[^"]*">.*?)(</div>\s*<!-- /wp:group -->)#su', function ( $m ) use ( $q, $a, &$n ) {
			$n++;
			return $m[1] . "\n\n" . rtrim( pm_fu_item( $q, $a ) ) . $m[2];
		}, $nuevo, 1 );
		$n || WP_CLI::error( "hub ($i): no pude insertar «{$q}»." );
		$deben[] = $q;
	}
	if ( ! $deben ) {
		continue;
	}
	$cambios++;
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": hub ($i) #{$p->ID}: +" . count( $deben ) . ' preguntas en la FAQ.' );
	if ( ! $aplicar ) {
		continue;
	}
	$r = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
	is_wp_error( $r ) && WP_CLI::error( "hub ($i): " . $r->get_error_message() );
	update_field( 'actualizado', $hoy, $p->ID );
	$leido = get_post( $p->ID )->post_content;
	$ok    = true;
	foreach ( $deben as $q ) {
		false === strpos( $leido, $q ) && ( $ok = false );
	}
	$faqs = pm_faqs_contenido( $leido );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": hub ($i) #{$p->ID} " . get_permalink( $p->ID ) . ' · FAQ con ' . count( $faqs ) . ' preguntas (JSON-LD)' );
	$ok || WP_CLI::error( "hub ($i): la lectura de vuelta no coincide." );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios notas." );
