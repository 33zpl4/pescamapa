<?php
/**
 * Entre Ríos: permiso de pesca deportiva (tarifas de octubre de 2026) y regla del dorado (Ley 11.174 y Decreto 726/25), 18/11/2026.
 * Fuentes leídas por el director el 18/11/2026: portal oficial de Entre Ríos, trámite 708 «Obtener licencia y permiso de caza o pesca deportiva»
 * (última actualización 02/10/2026) con su adjunto «Costos Permisos y Licencias Oct2026»; Ley provincial 11.174 (sancionada 5/12/2024, promulgada
 * 20/12/2024) y Decreto 726/25 MDE (1/4/2025; Boletín Oficial de Entre Ríos del 12/5/2025). La página del trámite no dice qué norma fija las tarifas
 * ni qué se le pide a un extranjero sin DNI; no se sabe si el permiso cubre La Zona: todo eso queda como «no confirmado».
 * Toca, en es/en/pt (cada edición es un reemplazo exacto o una inserción; si el texto publicado no es el esperado queda «falta»):
 *  - nota «Permiso de pesca en la Argentina paso a paso» (clave permiso-pesca-argentina-paso-a-paso): sección y tablas de Entre Ríos;
 *  - hub de licencias para extranjeros (clave licencias-extranjeros-argentina): fila, viñeta, «qué no cubre», fecha y fuente;
 *  - nota «La Zona (Salto Grande)» (la-zona-salto-grande) y pilar del dorado (golden-dorado-guia): frases de «no leímos la tarifa»;
 *  - guías de La Paz (la-paz) y de Concordia (concordia): sección «¿Se puede pescar dorado en Entre Ríos?», FAQ (= JSON-LD), fuentes y,
 *    en Concordia, la oficina donde sacar el permiso (sin teléfono) y la tabla de datos clave.
 * Moneda: pesos, con el formato de cada página (la nota del permiso no convierte; el hub convierte con su propio tipo de cambio de xe.com del 8/11/2026).
 * NO se afirma nada sobre cómo se aplica la veda de la CARU dentro de La Zona. Sin teléfonos, correos ni enlaces comerciales (solo el trámite oficial).
 * Idempotente. Simulacro por defecto ($aplicar = false). Corre con wp-script.yml (script = entre-rios-permiso-2026-11-18; aplicar=true para la real).
 */

$aplicar = false;
$hoy     = '20261118';
$T       = 'https://portal.entrerios.gov.ar/pf/tramite/708/obtener-licencia-y-permiso-de-caza-o-pesca-deportiva';

// ---- Helpers --------------------------------------------------------------------------------------------------------------------------------
function pm_er_post( string $tipo, string $clave, string $i ): ?WP_Post {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ) as $p ) {
		if ( pm_idioma_de( $p->ID ) === $i ) {
			return $p;
		}
	}
	return null;
}
function pm_er_link( string $tipo, string $clave, string $i ): string {
	$p = pm_er_post( $tipo, $clave, $i ) ?? pm_er_post( $tipo, $clave, 'es' );
	$p || WP_CLI::error( "No encuentro $tipo $clave." );
	return esc_url( get_permalink( $p->ID ) );
}
/** Reemplazo exacto: [ contenido, estado ] con estado ya | cambia | falta. */
function pm_er_rep( string $c, string $viejo, string $nuevo ): array {
	if ( false !== strpos( $c, $nuevo ) ) {
		return [ $c, 'ya' ];
	}
	if ( 1 === substr_count( $c, $viejo ) ) {
		return [ str_replace( $viejo, $nuevo, $c ), 'cambia' ];
	}
	return [ $c, 'falta' ];
}
/** Reemplazo por expresión regular (una sola coincidencia); $nuevo es una función que recibe las coincidencias. $marca = texto que indica que ya está hecho. */
function pm_er_rx( string $c, string $patron, callable $nuevo, string $marca ): array {
	if ( false !== strpos( $c, $marca ) ) {
		return [ $c, 'ya' ];
	}
	if ( 1 === preg_match_all( $patron, $c ) ) {
		return [ preg_replace_callback( $patron, $nuevo, $c ), 'cambia' ];
	}
	return [ $c, 'falta' ];
}
function pm_er_h( string $t ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$t}</h2>\n<!-- /wp:heading -->";
}
function pm_er_p( string $h ): string {
	return "<!-- wp:paragraph -->\n<p>{$h}</p>\n<!-- /wp:paragraph -->";
}
/** Inserta $bloques antes del primer encabezado que cumple $regex; $marca = texto del encabezado nuevo (ya hecho). */
function pm_er_ins( string $c, string $regex, array $bloques, string $marca ): array {
	if ( false !== strpos( $c, $marca ) ) {
		return [ $c, 'ya' ];
	}
	preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
	foreach ( $m[0] as $n => $b ) {
		if ( preg_match( $regex, trim( wp_strip_all_tags( $m[1][ $n ][0] ) ) ) ) {
			return [ substr( $c, 0, $b[1] ) . implode( "\n\n", $bloques ) . "\n\n" . substr( $c, $b[1] ), 'cambia' ];
		}
	}
	return [ $c, 'falta' ];
}

// ---- Textos ---------------------------------------------------------------------------------------------------------------------------------
$L = [];

// ===== ESPAÑOL =====
$L['es'] = [
	// Nota del permiso
	'nota_er_i'  => [ 'Entre Ríos publica el trámite «Obtener licencia y permiso de caza o pesca deportiva» (última actualización 02/10/2026 en el portal, leído el 16/11/2026).',
		'Entre Ríos publica el trámite <a href="' . $T . '" rel="noopener">«Obtener licencia y permiso de caza o pesca deportiva»</a> (última actualización 02/10/2026 en el portal, leído el 16/11/2026 y el 18/11/2026).' ],
	'nota_er_c_rx' => '#<p>El permiso de 5 días para extranjeros está en la tabla de abajo.*?</p>#su',
	'nota_er_c_marca' => 'Costos Permisos y Licencias Oct2026',
	'nota_celda' => [ '<td>Permiso de 5 días para extranjeros</td>', '<td>Permiso de 5 días para extranjeros: $ 24.251,60 (octubre de 2026)</td>' ],
	'nota_costo_rx' => '#Cuadro leído el 22/10/2026, en la (<a href="[^"]*">guía de La Paz</a>)#u',
	'nota_costo_nuevo' => 'Portal oficial de Entre Ríos (octubre de 2026), leído el 18/11/2026; mismos valores en la ',
	'nota_costo_marca' => 'Portal oficial de Entre Ríos (octubre de 2026), leído el 18/11/2026',
	// Hub
	'hub_fila'   => [ '<strong>Entre Ríos</strong> (La Paz, Concordia)', '–', '5 días: $ 24.251,60 (≈ US$ 16)', '–' ],
	'hub_vineta' => [ '<strong>Entre Ríos, Santa Fe, Chaco, Misiones y el Delta de Buenos Aires:</strong> cada provincia tiene la suya; todavía no las incluimos porque no leímos sus fuentes oficiales.',
		'<strong>La Paz, Concordia y el resto de Entre Ríos:</strong> el permiso de pesca deportiva de la provincia; para un extranjero, el de 5 días cuesta $ 24.251,60 (≈ US$ 16), según el portal oficial de Entre Ríos (octubre de 2026, leído el 18/11/2026). Paso a paso en <a href="%s">cómo sacar el permiso de pesca en la Argentina</a>. No confirmado: qué se le pide a un extranjero sin DNI y si cubre La Zona.</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Santa Fe, Chaco, Misiones y el Delta de Buenos Aires:</strong> cada provincia tiene la suya; todavía no las incluimos porque no leímos sus fuentes oficiales.' ],
	'hub_nocubre' => [ 'Las licencias de Entre Ríos, Santa Fe,', 'De Entre Ríos solo incluimos el permiso de 5 días para extranjeros, no sus otras categorías. Las licencias de Santa Fe,' ],
	'hub_fecha'  => [ 'leídos entre el 4 y el 8 de noviembre de 2026.', 'leídos entre el 4 y el 8 de noviembre de 2026; el de Entre Ríos, del portal oficial de la provincia, el 18 de noviembre de 2026.' ],
	'hub_fuente' => [ 'Gobierno de Entre Ríos: trámite «Obtener licencia y permiso de caza o pesca deportiva» y documento «Costos Permisos y Licencias Oct2026» (portal oficial, última actualización 02/10/2026; permiso de 5 días para extranjeros)', $T, 1 ],
	// Nota La Zona y pilar
	'zona_falta' => [ 'Las tasas de licencia, tallas y cupos de Entre Ríos para el río Uruguay: no las leímos en una página oficial.',
		'Las tallas y cupos de Entre Ríos para el río Uruguay (no los leímos en una página oficial) y si el permiso de pesca deportiva de Entre Ríos, que el portal oficial cobra en pesos (octubre de 2026), cubre La Zona.' ],
	'pilar'      => [ 'Todavía no leímos las tasas de licencia de Entre Ríos para ese río ni las tarifas propias de los operadores, así que no publicamos nada sobre ellos.',
		'El permiso de pesca de Entre Ríos (5 días para extranjeros: $ 24.251,60, octubre de 2026) está en <a href="%s">nuestra nota del permiso</a>; no confirmamos si cubre La Zona. Todavía no leímos las tarifas propias de los operadores, así que no publicamos nada sobre ellas.' ],
	// Guías: sección
	'lp_enc'     => '/^Tallas, cupos y vedas/u',
	'cz_enc'     => '/^Cómo llegar$/u',
	'sec_h'      => '¿Se puede pescar dorado en Entre Ríos?',
	'cz_h'       => 'Permiso de pesca de Entre Ríos y dorado',
	'corta'      => '<strong>Respuesta corta.</strong> Sí, pero solo de forma deportiva, con devolución obligatoria y con el permiso de pesca deportiva de la provincia. La pesca comercial del dorado está prohibida en toda Entre Ríos.',
	'ley'        => 'Lo dice la <strong>Ley provincial 11.174</strong> (sancionada el 5/12/2024 y promulgada el 20/12/2024) y su <strong>Decreto 726/25 MDE</strong> (1/4/2025; publicado en el Boletín Oficial de Entre Ríos del 12/5/2025). El art. 1 declara al dorado pez de interés turístico provincial. El art. 2 prohíbe su pesca con fines comerciales, el acopio, la venta, la tenencia y el tránsito en toda la provincia. El art. 4, inciso a), exceptúa la pesca deportiva con devolución obligatoria, y el art. 5 deja a la autoridad de aplicación fijar la modalidad: esa modalidad no la leímos.',
	'autoridad'  => 'El decreto designa autoridad de aplicación al Ministerio de Desarrollo Económico y a la Dirección de Recursos Naturales y Fiscalización. El permiso de pesca deportiva lo emite esa Dirección, pescar de forma deportiva sin ese permiso es una infracción y la multa llega a 2.000 litros de nafta súper.',
	'lp_donde'   => 'El permiso se tramita en el <a href="' . $T . '" rel="noopener">trámite oficial del Gobierno de Entre Ríos</a> (última actualización 02/10/2026, leído el 18/11/2026), es personal y se paga antes de pescar; se puede solicitar, entre otros lugares, en la oficina de turismo de La Paz. Los valores de octubre de 2026 de su documento «Costos Permisos y Licencias Oct2026» (en pesos) coinciden con los de la sección de arriba. Paso a paso, en <a href="%s">nuestra nota sobre el permiso de pesca en la Argentina</a>.',
	'cz_regla'   => 'Es una regla provincial, distinta de la veda del dorado de la CARU en el río Uruguay (sección «La veda del dorado»): no leímos nada que diga cómo se aplican entre sí.',
	'cz_donde'   => '<strong>Dónde sacar el permiso.</strong> El portal del <a href="' . $T . '" rel="noopener">Gobierno de Entre Ríos</a> lista una oficina en Concordia, en las calles Mitre y Pellegrini, entre los puestos donde se puede solicitar. El trámite es personal, se hace antes de pescar y pide el DNI del titular (última actualización 02/10/2026, leído el 18/11/2026). Valores de octubre de 2026, en pesos: permiso para extranjeros de 5 días, $24.251,60; permiso transitorio de 4 días para residentes de otras provincias, $5.488,52. Paso a paso, en <a href="%s">nuestra nota sobre el permiso de pesca en la Argentina</a>.',
	'cz_nc'      => '<strong>No confirmado:</strong> si ese permiso cubre la pesca en La Zona (donde la CARU exige contratar con el permisionario de cada país), qué documento se le pide a un extranjero que no tiene DNI, y qué norma fija las tarifas (la página del trámite no lo dice).',
	// Concordia: tabla, pend, resumen
	'cz_fila_rx' => '#(<td>Licencia de pesca de Entre Ríos para el río Uruguay</td>)<td>[^<]*</td><td>Pendiente</td>#u',
	'cz_fila'    => [ '<td>Permiso de pesca deportiva de Entre Ríos: 5 días para extranjeros, $24.251,60 (octubre de 2026). No confirmado: si cubre La Zona</td><td>Portal oficial de Entre Ríos (trámite actualizado el 2/10/2026) y Decreto 726/25 MDE</td>', 'Portal oficial de Entre Ríos (trámite actualizado el 2/10/2026)' ],
	'cz_pend'    => [ 'licencia, tallas y cupos de Entre Ríos para el río Uruguay;', 'tallas y cupos de Entre Ríos para el río Uruguay; si el permiso de pesca de Entre Ríos cubre La Zona y qué se le pide a un extranjero sin DNI;' ],
	'cz_resumen' => [ 'Licencia, tallas y cupos de Entre Ríos para el río Uruguay: no los leímos en una página oficial, no los podemos confirmar.',
		'Permiso de pesca deportiva de Entre Ríos (portal oficial, octubre de 2026): extranjeros, 5 días, $24.251,60; no confirmamos si cubre La Zona. Tallas y cupos de Entre Ríos para el río Uruguay: no los leímos en una página oficial. Dorado: Ley 11.174 y Decreto 726/25 (solo pesca deportiva con devolución obligatoria y con permiso provincial).' ],
	// FAQ
	'faq_dorado_lp' => [ '¿Se puede pescar dorado en Entre Ríos?', 'Sí, pero solo de forma deportiva, con devolución obligatoria y con el permiso de pesca deportiva de la provincia; la pesca comercial del dorado está prohibida (Ley provincial 11.174 y Decreto 726/25 MDE, Boletín Oficial de Entre Ríos del 12/5/2025).' ],
	'faq_donde_lp'  => [ '¿Dónde saco el permiso de pesca en La Paz?', 'En el trámite oficial del Gobierno de Entre Ríos (actualizado el 2/10/2026, leído el 18/11/2026), que se puede solicitar, entre otros lugares, en la oficina de turismo de La Paz. Es personal y se hace antes de pescar.' ],
	'faq_dorado_cz' => [ '¿Se puede pescar dorado en Entre Ríos?', 'Sí, pero solo de forma deportiva, con devolución obligatoria y con el permiso de pesca deportiva de la provincia; la pesca comercial está prohibida (Ley provincial 11.174 y Decreto 726/25 MDE, Boletín Oficial de Entre Ríos del 12/5/2025). Es una regla provincial, distinta de la veda del dorado de la CARU en el río Uruguay.' ],
	'faq_donde_cz'  => [ '¿Dónde saco el permiso de pesca en Concordia?', 'El portal del Gobierno de Entre Ríos lista una oficina en Concordia, en las calles Mitre y Pellegrini (trámite «Obtener licencia y permiso de caza o pesca deportiva», actualizado el 2/10/2026, leído el 18/11/2026). El trámite es personal y se hace antes de pescar.' ],
	'faq_lic_cz'    => [ '¿Hace falta licencia de pesca en Concordia?', 'Para pescar dorado de forma deportiva en Entre Ríos hace falta el permiso de pesca deportiva de la provincia (Decreto 726/25 MDE). Se tramita en el portal del Gobierno de Entre Ríos y se puede solicitar en la oficina de Concordia (calles Mitre y Pellegrini); es personal y se paga antes de pescar. Octubre de 2026: permiso para extranjeros de 5 días, $24.251,60. No confirmamos si cubre La Zona: preguntalo al lugar o a la Dirección de Recursos Naturales y Fiscalización de Entre Ríos.' ],
	'faq_quedar_cz' => [ '¿Puedo quedarme con el dorado que pesque en Concordia?', 'No. En La Zona solo se pesca con devolución, y en toda Entre Ríos la Ley 11.174 prohíbe la tenencia y el tránsito del dorado y solo exceptúa la pesca deportiva con devolución obligatoria (arts. 2 y 4, inc. a). Las tallas y cupos de Entre Ríos para el río Uruguay no los leímos.' ],
	// Fuentes de las guías
	'fuentes'    => [
		[ 'Gobierno de Entre Ríos: trámite «Obtener licencia y permiso de caza o pesca deportiva» y documento «Costos Permisos y Licencias Oct2026» (portal oficial, última actualización 02/10/2026)', $T, 1 ],
		[ 'Entre Ríos, Ley provincial 11.174 (sancionada 5/12/2024, promulgada 20/12/2024): el dorado, pez de interés turístico provincial; prohíbe su pesca comercial y exceptúa la deportiva con devolución', '', 1 ],
		[ 'Entre Ríos, Decreto 726/25 MDE (1/4/2025; Boletín Oficial de Entre Ríos del 12/5/2025): reglamenta la Ley 11.174 y designa la autoridad de aplicación', '', 1 ],
	],
];

// ===== ENGLISH =====
$L['en'] = [
	'nota_er_i'  => [ 'Entre Ríos publishes the procedure “Obtener licencia y permiso de caza o pesca deportiva” (last updated 02/10/2026 on the portal, read 16/11/2026).',
		'Entre Ríos publishes the procedure <a href="' . $T . '" rel="noopener">“Obtener licencia y permiso de caza o pesca deportiva”</a> (last updated 02/10/2026 on the portal, read 16/11/2026 and 18/11/2026).' ],
	'nota_er_c_rx' => '#<p>The 5-day permit for foreigners is in the table below.*?</p>#su',
	'nota_er_c_marca' => 'Costos Permisos y Licencias Oct2026',
	'nota_celda' => [ '<td>5-day permit for foreigners</td>', '<td>5-day permit for foreigners: $ 24.251,60 (October 2026)</td>' ],
	'nota_costo_rx' => '#Price list read on 22/10/2026, in the (<a href="[^"]*">La Paz guide</a>)#u',
	'nota_costo_nuevo' => 'Entre Ríos official portal (October 2026), read on 18/11/2026; same values in the ',
	'nota_costo_marca' => 'Entre Ríos official portal (October 2026), read on 18/11/2026',
	'hub_fila'   => [ '<strong>Entre Ríos</strong> (La Paz, Concordia)', '–', '5 days: $ 24,251.60 (≈ US$ 16)', '–' ],
	'hub_vineta' => [ '<strong>Entre Ríos, Santa Fe, Chaco, Misiones, and the Buenos Aires Delta:</strong> each province has its own; we have not included them yet because we have not read their official sources.',
		'<strong>La Paz, Concordia and the rest of Entre Ríos:</strong> the province’s sport fishing permit; for a foreigner, the 5-day permit costs $ 24,251.60 (≈ US$ 16), according to the official Entre Ríos portal (October 2026, read on 18 November 2026). Step by step in <a href="%s">how to get a fishing permit in Argentina</a>. Not confirmed: what a foreigner without a DNI is asked for and whether it covers La Zona.</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Santa Fe, Chaco, Misiones, and the Buenos Aires Delta:</strong> each province has its own; we have not included them yet because we have not read their official sources.' ],
	'hub_nocubre' => [ 'Licenses for Entre Ríos, Santa Fe,', 'For Entre Ríos we include only the 5-day permit for foreigners, not its other categories. Licenses for Santa Fe,' ],
	'hub_fecha'  => [ 'read between 4 and 8 November 2026.', 'read between 4 and 8 November 2026; the Entre Ríos value is from the province’s official portal, read on 18 November 2026.' ],
	'hub_fuente' => [ 'Government of Entre Ríos: procedure “Obtener licencia y permiso de caza o pesca deportiva” and document “Costos Permisos y Licencias Oct2026” (official portal, last updated 02/10/2026; 5-day permit for foreigners)', $T, 1 ],
	'zona_falta' => [ 'Entre Ríos licence fees, sizes and bag limits for the Uruguay River: we did not read them on an official page.',
		'Entre Ríos sizes and bag limits for the Uruguay River (we did not read them on an official page), and whether the Entre Ríos sport fishing permit, which the official portal charges in pesos (October 2026), covers La Zona.' ],
	'pilar'      => [ 'We have not yet read the Entre Ríos licence fees for that river or the operators\' own rates, so we publish nothing on them.',
		'The Entre Ríos fishing permit (5 days for foreigners: $ 24.251,60, October 2026) is in <a href="%s">our permit note</a>; we have not confirmed whether it covers La Zona. We have not yet read the operators’ own rates, so we publish nothing on them.' ],
	'lp_enc'     => '/^Sizes, bag limits/u',
	'cz_enc'     => '/^Getting there$/u',
	'sec_h'      => 'Can you fish dorado in Entre Ríos?',
	'cz_h'       => 'Entre Ríos fishing permit and dorado',
	'corta'      => '<strong>Short answer.</strong> Yes, but only as sport fishing, with mandatory release and with the province’s sport fishing permit. Commercial fishing of dorado is prohibited across Entre Ríos.',
	'ley'        => 'This is set by <strong>provincial Law 11.174</strong> (passed on 5/12/2024 and promulgated on 20/12/2024) and its <strong>Decree 726/25 MDE</strong> (1/4/2025; published in the Boletín Oficial of Entre Ríos on 12/5/2025). Art. 1 declares the dorado a fish of provincial tourist interest. Art. 2 prohibits catching it for commercial purposes, and also stockpiling, sale, possession and transport, anywhere in the province. Art. 4, paragraph a), exempts sport fishing with mandatory release, and art. 5 leaves the enforcement authority to set the method: we did not read that method.',
	'autoridad'  => 'The decree names the Ministry of Economic Development and the Natural Resources and Enforcement Directorate (Dirección de Recursos Naturales y Fiscalización) as the enforcement authority. The sport fishing permit is issued by that Directorate, sport fishing without it is an infraction, and the fine can reach 2,000 litres of super petrol (nafta súper).',
	'lp_donde'   => 'The permit is processed through the <a href="' . $T . '" rel="noopener">official Entre Ríos Government procedure</a> (last updated 02/10/2026, read on 18/11/2026); it is personal and paid before fishing, and it can be requested, among other places, at the La Paz tourist office. The October 2026 values in its document “Costos Permisos y Licencias Oct2026” (in pesos) match those in the section above. Step by step, in <a href="%s">our note on the fishing permit in Argentina</a>.',
	'cz_regla'   => 'This is a provincial rule, separate from the CARU dorado closed season on the Uruguay River (section “The dorado closed season”): we read nothing that says how the two apply to each other.',
	'cz_donde'   => '<strong>Where to get the permit.</strong> The <a href="' . $T . '" rel="noopener">Entre Ríos Government</a> portal lists an office in Concordia, on Mitre and Pellegrini streets, among the places where it can be requested. The procedure is personal, done before fishing, and asks for the holder’s DNI (last updated 02/10/2026, read on 18/11/2026). October 2026 values, in pesos: 5-day permit for foreigners, $24,251.60; 4-day temporary permit for residents of other provinces, $5,488.52. Step by step, in <a href="%s">our note on the fishing permit in Argentina</a>.',
	'cz_nc'      => '<strong>Not confirmed:</strong> whether that permit covers fishing in La Zona (where CARU requires booking with each country’s permit holder), what document a foreigner without a DNI is asked for, and which rule sets the fees (the procedure page does not say).',
	'cz_fila_rx' => '#(<td>Entre Ríos fishing licence for the Uruguay River</td>)<td>[^<]*</td><td>Pending</td>#u',
	'cz_fila'    => [ '<td>Entre Ríos sport fishing permit: 5 days for foreigners, $24,251.60 (October 2026). Not confirmed: whether it covers La Zona</td><td>Entre Ríos official portal (procedure updated 2/10/2026) and Decree 726/25 MDE</td>', 'Entre Ríos official portal (procedure updated 2/10/2026)' ],
	'cz_pend'    => [ 'Entre Ríos licence, sizes and limits for the Uruguay River;', 'sizes and limits of Entre Ríos for the Uruguay River; whether the Entre Ríos fishing permit covers La Zona and what a foreigner without a DNI is asked for;' ],
	'cz_resumen' => [ 'Entre Ríos licence, sizes and limits for the Uruguay River: not read on an official page, we cannot confirm them.',
		'Entre Ríos sport fishing permit (official portal, October 2026): foreigners, 5 days, $24,251.60; we have not confirmed whether it covers La Zona. Entre Ríos sizes and limits for the Uruguay River: not read on an official page. Dorado: Law 11.174 and Decree 726/25 (sport fishing only, with mandatory release and a provincial permit).' ],
	'faq_dorado_lp' => [ 'Can you fish dorado in Entre Ríos?', 'Yes, but only as sport fishing, with mandatory release and with the province’s sport fishing permit; commercial fishing of dorado is prohibited (provincial Law 11.174 and Decree 726/25 MDE, Boletín Oficial of Entre Ríos of 12/5/2025).' ],
	'faq_donde_lp'  => [ 'Where do I get the fishing permit in La Paz?', 'Through the official Entre Ríos Government procedure (updated 2/10/2026, read on 18/11/2026), which can be requested, among other places, at the La Paz tourist office. It is personal and done before fishing.' ],
	'faq_dorado_cz' => [ 'Can you fish dorado in Entre Ríos?', 'Yes, but only as sport fishing, with mandatory release and with the province’s sport fishing permit; commercial fishing is prohibited (provincial Law 11.174 and Decree 726/25 MDE, Boletín Oficial of Entre Ríos of 12/5/2025). This is a provincial rule, separate from the CARU dorado closed season on the Uruguay River.' ],
	'faq_donde_cz'  => [ 'Where do I get the fishing permit in Concordia?', 'The Entre Ríos Government portal lists an office in Concordia, on Mitre and Pellegrini streets (procedure “Obtener licencia y permiso de caza o pesca deportiva”, updated 2/10/2026, read on 18/11/2026). The procedure is personal and done before fishing.' ],
	'faq_lic_cz'    => [ 'Do I need a fishing licence in Concordia?', 'To fish dorado as sport fishing in Entre Ríos you need the province’s sport fishing permit (Decree 726/25 MDE). It is processed through the Entre Ríos Government portal and can be requested at the Concordia office (Mitre and Pellegrini streets); it is personal and paid before fishing. October 2026: 5-day permit for foreigners, $24,251.60. We have not confirmed whether it covers La Zona: ask the place or the Entre Ríos Natural Resources and Enforcement Directorate.' ],
	'faq_quedar_cz' => [ 'Can I keep the dorado I catch in Concordia?', 'No. In La Zona it is catch and release only, and across Entre Ríos Law 11.174 prohibits possession and transport of dorado and only exempts sport fishing with mandatory release (arts. 2 and 4, para. a). We did not read the Entre Ríos sizes and bag limits for the Uruguay River.' ],
	'fuentes'    => [
		[ 'Government of Entre Ríos: procedure “Obtener licencia y permiso de caza o pesca deportiva” and document “Costos Permisos y Licencias Oct2026” (official portal, last updated 02/10/2026)', $T, 1 ],
		[ 'Entre Ríos, provincial Law 11.174 (passed 5/12/2024, promulgated 20/12/2024): the dorado as a fish of provincial tourist interest; prohibits commercial fishing and exempts sport fishing with release', '', 1 ],
		[ 'Entre Ríos, Decree 726/25 MDE (1/4/2025; Boletín Oficial of Entre Ríos, 12/5/2025): regulates Law 11.174 and names the enforcement authority', '', 1 ],
	],
];

// ===== PORTUGUÊS =====
$L['pt'] = [
	'nota_er_i'  => [ 'Entre Ríos publica o trâmite «Obtener licencia y permiso de caza o pesca deportiva» (última atualização 02/10/2026 no portal, lido em 16/11/2026).',
		'Entre Ríos publica o trâmite <a href="' . $T . '" rel="noopener">«Obtener licencia y permiso de caza o pesca deportiva»</a> (última atualização 02/10/2026 no portal, lido em 16/11/2026 e em 18/11/2026).' ],
	'nota_er_c_rx' => '#<p>A licença de 5 dias para estrangeiros está na tabela abaixo.*?</p>#su',
	'nota_er_c_marca' => 'Costos Permisos y Licencias Oct2026',
	'nota_celda' => [ '<td>Licença de 5 dias para estrangeiros</td>', '<td>Licença de 5 dias para estrangeiros: $ 24.251,60 (outubro de 2026)</td>' ],
	'nota_costo_rx' => '#Quadro lido em 22/10/2026, no (<a href="[^"]*">guia de La Paz</a>)#u',
	'nota_costo_nuevo' => 'Portal oficial de Entre Ríos (outubro de 2026), lido em 18/11/2026; mesmos valores no ',
	'nota_costo_marca' => 'Portal oficial de Entre Ríos (outubro de 2026), lido em 18/11/2026',
	'hub_fila'   => [ '<strong>Entre Ríos</strong> (La Paz, Concordia)', '–', '5 dias: $ 24.251,60 (≈ US$ 16)', '–' ],
	'hub_vineta' => [ '<strong>Entre Ríos, Santa Fé, Chaco, Misiones e o Delta de Buenos Aires:</strong> cada província tem a sua; ainda não as incluímos porque não lemos suas fontes oficiais.',
		'<strong>La Paz, Concordia e o restante de Entre Ríos:</strong> a licença de pesca esportiva da província; para um estrangeiro, a de 5 dias custa $ 24.251,60 (≈ US$ 16), segundo o portal oficial de Entre Ríos (outubro de 2026, lido em 18/11/2026). Passo a passo em <a href="%s">como tirar a licença de pesca na Argentina</a>. Não confirmado: o que se pede a um estrangeiro sem DNI e se cobre La Zona.</li><!-- /wp:list-item --><!-- wp:list-item --><li><strong>Santa Fé, Chaco, Misiones e o Delta de Buenos Aires:</strong> cada província tem a sua; ainda não as incluímos porque não lemos suas fontes oficiais.' ],
	'hub_nocubre' => [ 'As licenças de Entre Ríos, Santa Fé,', 'De Entre Ríos incluímos só a licença de 5 dias para estrangeiros, não as demais categorias. As licenças de Santa Fé,' ],
	'hub_fecha'  => [ 'lidos entre 4 e 8 de novembro de 2026.', 'lidos entre 4 e 8 de novembro de 2026; o de Entre Ríos, do portal oficial da província, em 18 de novembro de 2026.' ],
	'hub_fuente' => [ 'Governo de Entre Ríos: trâmite «Obtener licencia y permiso de caza o pesca deportiva» e documento «Costos Permisos y Licencias Oct2026» (portal oficial, última atualização 02/10/2026; licença de 5 dias para estrangeiros)', $T, 1 ],
	'zona_falta' => [ 'As taxas de licença, tamanhos e cotas de Entre Ríos para o rio Uruguai: não as lemos em página oficial.',
		'Os tamanhos e cotas de Entre Ríos para o rio Uruguai (não os lemos em página oficial) e se a licença de pesca esportiva de Entre Ríos, que o portal oficial cobra em pesos (outubro de 2026), cobre La Zona.' ],
	'pilar'      => [ 'Ainda não lemos as taxas de licença de Entre Ríos para esse rio nem as tarifas próprias dos operadores, então não publicamos nada sobre eles.',
		'A licença de pesca de Entre Ríos (5 dias para estrangeiros: $ 24.251,60, outubro de 2026) está na <a href="%s">nossa nota da licença</a>; não confirmamos se cobre La Zona. Ainda não lemos as tarifas próprias dos operadores, então não publicamos nada sobre elas.' ],
	'lp_enc'     => '/^Tamanhos, limites/u',
	'cz_enc'     => '/^Como chegar$/u',
	'sec_h'      => 'Pode-se pescar dourado em Entre Ríos?',
	'cz_h'       => 'Licença de pesca de Entre Ríos e dourado',
	'corta'      => '<strong>Resposta curta.</strong> Sim, mas só como pesca esportiva, com devolução obrigatória e com a licença de pesca esportiva da província. A pesca comercial do dourado é proibida em toda Entre Ríos.',
	'ley'        => 'Quem diz é a <strong>Lei provincial 11.174</strong> (sancionada em 5/12/2024 e promulgada em 20/12/2024) e seu <strong>Decreto 726/25 MDE</strong> (1/4/2025; publicado no Boletim Oficial de Entre Ríos de 12/5/2025). O art. 1 declara o dourado peixe de interesse turístico provincial. O art. 2 proíbe sua pesca com fins comerciais, o acúmulo, a venda, a posse e o trânsito em toda a província. O art. 4, inciso a), exclui a pesca esportiva com devolução obrigatória, e o art. 5 deixa à autoridade de aplicação a definição da modalidade: essa modalidade não a lemos.',
	'autoridad'  => 'O decreto designa como autoridade de aplicação o Ministério de Desenvolvimento Econômico e a Direção de Recursos Naturais e Fiscalização (Dirección de Recursos Naturales y Fiscalización). A licença de pesca esportiva é emitida por essa Direção, pescar de forma esportiva sem ela é infração e a multa chega a 2.000 litros de gasolina comum (nafta súper).',
	'lp_donde'   => 'A licença é tirada no <a href="' . $T . '" rel="noopener">trâmite oficial do Governo de Entre Ríos</a> (última atualização 02/10/2026, lido em 18/11/2026), é pessoal e paga antes de pescar; pode ser solicitada, entre outros lugares, no escritório de turismo de La Paz. Os valores de outubro de 2026 do seu documento «Costos Permisos y Licencias Oct2026» (em pesos) coincidem com os da seção acima. Passo a passo, na <a href="%s">nossa nota sobre a licença de pesca na Argentina</a>.',
	'cz_regla'   => 'É uma regra provincial, distinta do defeso do dourado da CARU no rio Uruguai (seção «O defeso do dourado»): não lemos nada que diga como as duas se aplicam entre si.',
	'cz_donde'   => '<strong>Onde tirar a licença.</strong> O portal do <a href="' . $T . '" rel="noopener">Governo de Entre Ríos</a> lista um escritório em Concordia, nas ruas Mitre e Pellegrini, entre os locais onde ela pode ser solicitada. O trâmite é pessoal, feito antes de pescar, e pede o DNI do titular (última atualização 02/10/2026, lido em 18/11/2026). Valores de outubro de 2026, em pesos: licença para estrangeiros de 5 dias, $24.251,60; permissão transitória de 4 dias para residentes de outras províncias, $5.488,52. Passo a passo, na <a href="%s">nossa nota sobre a licença de pesca na Argentina</a>.',
	'cz_nc'      => '<strong>Não confirmado:</strong> se essa licença cobre a pesca em La Zona (onde a CARU exige contratar com o permissionário de cada país), que documento se pede a um estrangeiro sem DNI e que norma fixa as tarifas (a página do trâmite não diz).',
	'cz_fila_rx' => '#(<td>Licença de pesca de Entre Ríos para o rio Uruguai</td>)<td>[^<]*</td><td>Pendente</td>#u',
	'cz_fila'    => [ '<td>Licença de pesca esportiva de Entre Ríos: 5 dias para estrangeiros, $24.251,60 (outubro de 2026). Não confirmado: se cobre La Zona</td><td>Portal oficial de Entre Ríos (trâmite atualizado em 2/10/2026) e Decreto 726/25 MDE</td>', 'Portal oficial de Entre Ríos (trâmite atualizado em 2/10/2026)' ],
	'cz_pend'    => [ 'licença, tamanhos e cotas de Entre Ríos para o rio Uruguai;', 'tamanhos e cotas de Entre Ríos para o rio Uruguai; se a licença de pesca de Entre Ríos cobre La Zona e o que se pede a um estrangeiro sem DNI;' ],
	'cz_resumen' => [ 'Licença, tamanhos e cotas de Entre Ríos para o rio Uruguai: não lidos em página oficial, não podemos confirmá-los.',
		'Licença de pesca esportiva de Entre Ríos (portal oficial, outubro de 2026): estrangeiros, 5 dias, $24.251,60; não confirmamos se cobre La Zona. Tamanhos e cotas de Entre Ríos para o rio Uruguai: não lidos em página oficial. Dourado: Lei 11.174 e Decreto 726/25 (só pesca esportiva, com devolução obrigatória e licença provincial).' ],
	'faq_dorado_lp' => [ 'Pode-se pescar dourado em Entre Ríos?', 'Sim, mas só como pesca esportiva, com devolução obrigatória e com a licença de pesca esportiva da província; a pesca comercial do dourado é proibida (Lei provincial 11.174 e Decreto 726/25 MDE, Boletim Oficial de Entre Ríos de 12/5/2025).' ],
	'faq_donde_lp'  => [ 'Onde tiro a licença de pesca em La Paz?', 'No trâmite oficial do Governo de Entre Ríos (atualizado em 2/10/2026, lido em 18/11/2026), que pode ser solicitado, entre outros lugares, no escritório de turismo de La Paz. É pessoal e feito antes de pescar.' ],
	'faq_dorado_cz' => [ 'Pode-se pescar dourado em Entre Ríos?', 'Sim, mas só como pesca esportiva, com devolução obrigatória e com a licença de pesca esportiva da província; a pesca comercial é proibida (Lei provincial 11.174 e Decreto 726/25 MDE, Boletim Oficial de Entre Ríos de 12/5/2025). É uma regra provincial, distinta do defeso do dourado da CARU no rio Uruguai.' ],
	'faq_donde_cz'  => [ 'Onde tiro a licença de pesca em Concordia?', 'O portal do Governo de Entre Ríos lista um escritório em Concordia, nas ruas Mitre e Pellegrini (trâmite «Obtener licencia y permiso de caza o pesca deportiva», atualizado em 2/10/2026, lido em 18/11/2026). O trâmite é pessoal e feito antes de pescar.' ],
	'faq_lic_cz'    => [ 'É preciso licença de pesca em Concordia?', 'Para pescar dourado de forma esportiva em Entre Ríos é preciso a licença de pesca esportiva da província (Decreto 726/25 MDE). Ela é tirada no portal do Governo de Entre Ríos e pode ser solicitada no escritório de Concordia (ruas Mitre e Pellegrini); é pessoal e paga antes de pescar. Outubro de 2026: licença para estrangeiros de 5 dias, $24.251,60. Não confirmamos se cobre La Zona: pergunte ao lugar ou à Direção de Recursos Naturais e Fiscalização de Entre Ríos.' ],
	'faq_quedar_cz' => [ 'Posso ficar com o dourado que pescar em Concordia?', 'Não. Em La Zona só se pesca com devolução, e em toda Entre Ríos a Lei 11.174 proíbe a posse e o trânsito do dourado e só exclui a pesca esportiva com devolução obrigatória (arts. 2 e 4, inc. a). Os tamanhos e cotas de Entre Ríos para o rio Uruguai não os lemos.' ],
	'fuentes'    => [
		[ 'Governo de Entre Ríos: trâmite «Obtener licencia y permiso de caza o pesca deportiva» e documento «Costos Permisos y Licencias Oct2026» (portal oficial, última atualização 02/10/2026)', $T, 1 ],
		[ 'Entre Ríos, Lei provincial 11.174 (sancionada em 5/12/2024, promulgada em 20/12/2024): o dourado, peixe de interesse turístico provincial; proíbe a pesca comercial e exclui a esportiva com devolução', '', 1 ],
		[ 'Entre Ríos, Decreto 726/25 MDE (1/4/2025; Boletim Oficial de Entre Ríos de 12/5/2025): regulamenta a Lei 11.174 e designa a autoridade de aplicação', '', 1 ],
	],
];

// ---- Verificación previa: nada de contactos, cuentas ni enlaces comerciales en el texto nuevo --------------------------------------------------
$todo = wp_json_encode( $L, JSON_UNESCAPED_UNICODE );
$sin_t = str_replace( $T, '', $todo );
if ( preg_match( '/@|instagram|whatsapp|lazonalodge|\+\d{2}[ \d]{6,}|tel[eé]fono|https?:\/\//iu', $sin_t, $m ) ) {
	WP_CLI::error( 'texto nuevo con algo que no se publica: ' . $m[0] );
}
WP_CLI::log( 'OK: el texto nuevo no lleva teléfonos, correos, cuentas ni enlaces que no sean el trámite oficial.' );

// ---- Plan -------------------------------------------------------------------------------------------------------------------------------------
$hay_falta = false;
$plan      = []; // por post: id, que, i, antes, despues, fuentes[], faqs[], campos[]
$log       = function ( string $que, string $i, int $id, string $que_edicion, string $estado, bool $critica ) use ( &$hay_falta, $aplicar ) {
	WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": $que ($i) #$id $que_edicion: $estado" . ( 'falta' === $estado && ! $critica ? ' (no crítica)' : '' ) );
	if ( 'falta' === $estado ) {
		WP_CLI::warning( "$que ($i) $que_edicion: no encuentro el texto esperado (o está más de una vez)" );
		if ( $critica ) {
			$hay_falta = true;
		}
	}
};

foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$t = $L[ $i ];

	// 1) Nota del permiso paso a paso.
	$p = pm_er_post( 'pm_nota', 'permiso-pesca-argentina-paso-a-paso', $i );
	if ( ! $p ) {
		WP_CLI::warning( "nota permiso ($i): no existe" );
		$hay_falta = true;
	} else {
		$c      = $p->post_content;
		$guia_p = pm_er_link( 'pm_destino', 'la-paz', $i );
		$guia_c = pm_er_link( 'pm_destino', 'concordia', $i );
		$zona   = pm_er_link( 'pm_nota', 'la-zona-salto-grande', $i );
		[ $c, $e ] = pm_er_rep( $c, $t['nota_er_i'][0], $t['nota_er_i'][1] );
		$log( 'nota permiso', $i, $p->ID, 'párrafo del trámite (enlace oficial)', $e, false );
		// Párrafo de cierre de la sección: se reemplaza por dos párrafos (valores / no confirmado).
		$cierre = [
			'es' => [
				'<p>Valores de octubre de 2026, en pesos, del documento «Costos Permisos y Licencias Oct2026», adjunto del <a href="' . $T . '" rel="noopener">trámite oficial</a> (portal de Entre Ríos, leído el 18/11/2026): <strong>permiso para extranjeros, 5 días: $ 24.251,60</strong>; permiso transitorio de 4 días para residentes de otras provincias: $ 5.488,52. Se genera el permiso y se paga <strong>antes de pescar</strong>. Son los mismos valores del cuadro que cita nuestra <a href="%1$s">guía de La Paz</a> (leído el 22/10/2026), que trae además las demás categorías.</p>',
				'<p><strong>No confirmado:</strong> qué documento se le pide a un extranjero que no tiene DNI (la página lista el DNI del titular y no dice qué se pide a un extranjero) y si este permiso cubre la pesca en La Zona, el tramo bajo la represa de Salto Grande, donde la CARU exige contratar con el permisionario de cada país (<a href="%2$s">nuestra nota sobre La Zona</a>). La página tampoco dice qué norma fija las tarifas. En Concordia el permiso se puede solicitar en la oficina de las calles Mitre y Pellegrini (<a href="%3$s">guía de Concordia</a>).</p>',
			],
			'en' => [
				'<p>October 2026 values, in pesos, from the document “Costos Permisos y Licencias Oct2026” attached to the <a href="' . $T . '" rel="noopener">official procedure</a> (Entre Ríos portal, read on 18/11/2026): <strong>permit for foreigners, 5 days: $ 24.251,60</strong>; 4-day temporary permit for residents of other provinces: $ 5.488,52. You generate the permit and pay <strong>before fishing</strong>. These are the same values as the table cited by our <a href="%1$s">La Paz guide</a> (read on 22/10/2026), which also lists the other categories.</p>',
				'<p><strong>Not confirmed:</strong> what document a foreigner without a DNI is asked for (the page lists the holder’s DNI and does not say what is asked of a foreigner), and whether this permit covers fishing in La Zona, the stretch below the Salto Grande dam, where CARU requires booking with each country’s permit holder (<a href="%2$s">our note on La Zona</a>). The page does not say which rule sets the fees either. In Concordia the permit can be requested at the office on Mitre and Pellegrini streets (<a href="%3$s">Concordia guide</a>).</p>',
			],
			'pt' => [
				'<p>Valores de outubro de 2026, em pesos, do documento «Costos Permisos y Licencias Oct2026», anexo do <a href="' . $T . '" rel="noopener">trâmite oficial</a> (portal de Entre Ríos, lido em 18/11/2026): <strong>licença para estrangeiros, 5 dias: $ 24.251,60</strong>; permissão transitória de 4 dias para residentes de outras províncias: $ 5.488,52. A licença é gerada e paga <strong>antes de pescar</strong>. São os mesmos valores do quadro que cita o nosso <a href="%1$s">guia de La Paz</a> (lido em 22/10/2026), que traz também as demais categorias.</p>',
				'<p><strong>Não confirmado:</strong> que documento se pede a um estrangeiro que não tem DNI (a página lista o DNI do titular e não diz o que se pede a um estrangeiro) e se esta licença cobre a pesca em La Zona, o trecho abaixo da represa de Salto Grande, onde a CARU exige contratar com o permissionário de cada país (<a href="%2$s">nossa nota sobre La Zona</a>). A página também não diz que norma fixa as tarifas. Em Concordia a licença pode ser solicitada no escritório das ruas Mitre e Pellegrini (<a href="%3$s">guia de Concordia</a>).</p>',
			],
		][ $i ];
		$bloque_er = sprintf( $cierre[0], $guia_p, $zona, $guia_c ) . "\n<!-- /wp:paragraph -->\n\n<!-- wp:paragraph -->\n" . sprintf( $cierre[1], $guia_p, $zona, $guia_c );
		[ $c, $e ] = pm_er_rx( $c, $t['nota_er_c_rx'], fn( $m ) => $bloque_er, $t['nota_er_c_marca'] );
		$log( 'nota permiso', $i, $p->ID, 'párrafo de valores y «no confirmado»', $e, true );
		[ $c, $e ] = pm_er_rep( $c, $t['nota_celda'][0], $t['nota_celda'][1] );
		$log( 'nota permiso', $i, $p->ID, 'tabla de dónde sacarlo (5 días)', $e, true );
		[ $c, $e ] = pm_er_rx( $c, $t['nota_costo_rx'], fn( $m ) => $t['nota_costo_nuevo'] . $m[1], $t['nota_costo_marca'] );
		$log( 'nota permiso', $i, $p->ID, 'tabla de costos (fuente y fecha)', $e, true );
		$plan[] = [ 'que' => 'nota permiso', 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c, 'fuente_fecha' => 'tramite/708', 'nota' => true ];
	}

	// 2) Hub de licencias.
	$p = pm_er_post( 'pm_nota', 'licencias-extranjeros-argentina', $i );
	if ( ! $p ) {
		WP_CLI::warning( "hub licencias ($i): no existe" );
		$hay_falta = true;
	} else {
		$c       = $p->post_content;
		$permiso = pm_er_link( 'pm_nota', 'permiso-pesca-argentina-paso-a-paso', $i );
		// Fila nueva en la tabla, tras la de Corrientes.
		$fila = '<tr>' . implode( '', array_map( fn( $x ) => "<td>{$x}</td>", $t['hub_fila'] ) ) . '</tr>';
		[ $c, $e ] = pm_er_rx( $c, '#<tr>\s*<td[^>]*><strong>Corrientes</strong>.*?</tr>#su', fn( $m ) => $m[0] . $fila, $t['hub_fila'][0] );
		$log( 'hub', $i, $p->ID, 'fila de Entre Ríos en la tabla', $e, true );
		[ $c, $e ] = pm_er_rep( $c, $t['hub_vineta'][0], sprintf( $t['hub_vineta'][1], $permiso ) );
		$log( 'hub', $i, $p->ID, 'viñeta «qué permiso necesito»', $e, true );
		[ $c, $e ] = pm_er_rep( $c, $t['hub_nocubre'][0], $t['hub_nocubre'][1] );
		$log( 'hub', $i, $p->ID, '«qué no cubre todavía»', $e, true );
		[ $c, $e ] = pm_er_rep( $c, $t['hub_fecha'][0], $t['hub_fecha'][1] );
		$log( 'hub', $i, $p->ID, 'párrafo de fechas de lectura', $e, false );
		$plan[] = [ 'que' => 'hub', 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c, 'fuentes' => [ $t['hub_fuente'] ] ];
	}

	// 3) Nota La Zona: frase de lo que no sabemos.
	$p = pm_er_post( 'pm_nota', 'la-zona-salto-grande', $i );
	if ( ! $p ) {
		WP_CLI::warning( "nota La Zona ($i): no existe" );
		$hay_falta = true;
	} else {
		$c = $p->post_content;
		[ $c, $e ] = pm_er_rep( $c, $t['zona_falta'][0], $t['zona_falta'][1] );
		$log( 'nota La Zona', $i, $p->ID, '«lo que no sabemos» (licencia de Entre Ríos)', $e, false );
		// La lista «falta» también vive en el campo nota_datos o en el cuerpo: solo se toca el cuerpo.
		$plan[] = [ 'que' => 'nota La Zona', 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c ];
	}

	// 4) Pilar del dorado.
	$p = pm_er_post( 'pm_nota', 'golden-dorado-guia', $i );
	if ( ! $p ) {
		WP_CLI::warning( "pilar dorado ($i): no existe" );
	} else {
		$c = $p->post_content;
		[ $c, $e ] = pm_er_rep( $c, $t['pilar'][0], sprintf( $t['pilar'][1], $permiso ?? pm_er_link( 'pm_nota', 'permiso-pesca-argentina-paso-a-paso', $i ) ) );
		$log( 'pilar dorado', $i, $p->ID, 'frase «no leímos las tasas de Entre Ríos»', $e, false );
		$plan[] = [ 'que' => 'pilar dorado', 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c ];
	}

	// 5) Guía de La Paz.
	$p = pm_er_post( 'pm_destino', 'la-paz', $i );
	if ( ! $p ) {
		WP_CLI::warning( "guía La Paz ($i): no existe" );
		$hay_falta = true;
	} else {
		$c       = $p->post_content;
		$permiso = pm_er_link( 'pm_nota', 'permiso-pesca-argentina-paso-a-paso', $i );
		$bloques = [ pm_er_h( $t['sec_h'] ), pm_er_p( $t['corta'] ), pm_er_p( $t['ley'] ), pm_er_p( $t['autoridad'] ), pm_er_p( sprintf( $t['lp_donde'], $permiso ) ) ];
		[ $c, $e ] = pm_er_ins( $c, $t['lp_enc'], $bloques, $t['sec_h'] );
		$log( 'guía La Paz', $i, $p->ID, 'sección «¿Se puede pescar dorado en Entre Ríos?»', $e, true );
		$plan[] = [ 'que' => 'guía La Paz', 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c, 'fuentes' => $t['fuentes'], 'faqs' => [ [ 'add', $t['faq_dorado_lp'] ], [ 'add', $t['faq_donde_lp'] ] ] ];
	}

	// 6) Guía de Concordia.
	$p = pm_er_post( 'pm_destino', 'concordia', $i );
	if ( ! $p ) {
		WP_CLI::warning( "guía Concordia ($i): no existe" );
		$hay_falta = true;
	} else {
		$c       = $p->post_content;
		$permiso = pm_er_link( 'pm_nota', 'permiso-pesca-argentina-paso-a-paso', $i );
		$bloques = [ pm_er_h( $t['cz_h'] ), pm_er_p( $t['corta'] ), pm_er_p( $t['ley'] ), pm_er_p( $t['autoridad'] ), pm_er_p( $t['cz_regla'] ), pm_er_p( sprintf( $t['cz_donde'], $permiso ) ), pm_er_p( $t['cz_nc'] ) ];
		[ $c, $e ] = pm_er_ins( $c, $t['cz_enc'], $bloques, $t['cz_h'] );
		$log( 'guía Concordia', $i, $p->ID, 'sección del permiso y el dorado', $e, true );
		[ $c, $e ] = pm_er_rx( $c, $t['cz_fila_rx'], fn( $m ) => $m[1] . $t['cz_fila'][0], $t['cz_fila'][1] );
		$log( 'guía Concordia', $i, $p->ID, 'fila «licencia de Entre Ríos» de datos clave', $e, true );
		[ $c, $e ] = pm_er_rep( $c, $t['cz_pend'][0], $t['cz_pend'][1] );
		$log( 'guía Concordia', $i, $p->ID, 'nota oculta PENDIENTE', $e, false );
		$res    = (string) pm_campo( 'reglamento_resumen', $p->ID );
		[ $res2, $e ] = pm_er_rep( $res, $t['cz_resumen'][0], $t['cz_resumen'][1] );
		$log( 'guía Concordia', $i, $p->ID, 'campo reglamento_resumen', $e, true );
		$plan[] = [
			'que' => 'guía Concordia', 'i' => $i, 'id' => $p->ID, 'antes' => $p->post_content, 'despues' => $c, 'fuentes' => $t['fuentes'],
			'campos' => [ 'reglamento_resumen' => $res2 ],
			'faqs' => [ [ 'update', $t['faq_lic_cz'] ], [ 'update', $t['faq_quedar_cz'] ], [ 'add', $t['faq_dorado_cz'] ], [ 'add', $t['faq_donde_cz'] ] ],
		];
	}
}

// FAQ y fuentes: simulacro de lo que cambiaría.
foreach ( $plan as $k => $x ) {
	if ( ! empty( $x['faqs'] ) ) {
		$filas = pm_filas( 'faqs', $x['id'] );
		foreach ( $x['faqs'] as [ $modo, [ $q, $a ] ] ) {
			$idx = null;
			foreach ( $filas as $n => $f ) {
				if ( ( $f['pregunta'] ?? '' ) === $q ) {
					$idx = $n;
				}
			}
			if ( 'update' === $modo ) {
				if ( null === $idx ) {
					WP_CLI::warning( "{$x['que']} ({$x['i']}) FAQ «$q»: no existe, no se puede actualizar" );
					$hay_falta = true;
					continue;
				}
				$estado = ( $filas[ $idx ]['respuesta'] ?? '' ) === $a ? 'ya' : 'cambia';
				$filas[ $idx ]['respuesta'] = $a;
			} else {
				if ( null !== $idx ) {
					$estado = ( $filas[ $idx ]['respuesta'] ?? '' ) === $a ? 'ya' : 'cambia';
					$filas[ $idx ]['respuesta'] = $a;
				} else {
					$estado  = 'agrega';
					$filas[] = [ 'pregunta' => $q, 'respuesta' => $a ];
				}
			}
			WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$x['que']} ({$x['i']}) #{$x['id']} FAQ «$q»: $estado" );
		}
		$plan[ $k ]['faqs_final'] = array_map( fn( $f ) => [ 'pregunta' => (string) ( $f['pregunta'] ?? '' ), 'respuesta' => (string) ( $f['respuesta'] ?? '' ) ], $filas );
		WP_CLI::log( "   {$x['que']} ({$x['i']}): preguntas " . count( $filas ) );
	}
	if ( ! empty( $x['fuentes'] ) ) {
		$filas = pm_filas( 'fuentes', $x['id'] );
		$urls  = array_filter( array_column( $filas, 'url' ) );
		$nombres = array_column( $filas, 'nombre' );
		$nuevas  = [];
		foreach ( $x['fuentes'] as [ $nombre, $url, $of ] ) {
			if ( in_array( $nombre, $nombres, true ) || ( $url && in_array( $url, $urls, true ) ) ) {
				continue;
			}
			$nuevas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => $of, 'consultada' => $hoy ];
		}
		WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": {$x['que']} ({$x['i']}) #{$x['id']} fuentes: +" . count( $nuevas ) . ' (ya había ' . count( $filas ) . ')' );
		$plan[ $k ]['fuentes_nuevas'] = $nuevas;
	}
}

if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' . ( $hay_falta ? ' (HAY «falta»: revisar antes de aplicar)' : '' ) );
	return;
}
if ( $hay_falta ) {
	WP_CLI::error( 'no aplico: hay textos que no están como se esperaba (ver «falta»).' );
}

// ---- Aplicar ----------------------------------------------------------------------------------------------------------------------------------
$ahora = time();
foreach ( $plan as $x ) {
	$i = $x['i'];
	if ( $x['antes'] !== $x['despues'] ) {
		$r = wp_update_post( [ 'ID' => $x['id'], 'post_content' => wp_slash( $x['despues'] ) ], true );
		is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
		update_post_meta( $x['id'], 'es' === $i ? '_pm_es_editado' : '_pm_traducido', $ahora + ( 'es' === $i ? 0 : 1 ) );
	}
	foreach ( $x['campos'] ?? [] as $campo => $v ) {
		update_field( $campo, $v, $x['id'] );
	}
	if ( ! empty( $x['faqs_final'] ) ) {
		update_field( 'faqs', $x['faqs_final'], $x['id'] );
	}
	if ( ! empty( $x['fuentes_nuevas'] ) ) {
		$filas = pm_filas( 'fuentes', $x['id'] );
		foreach ( $x['fuentes_nuevas'] as $n ) {
			$filas[] = $n;
		}
		update_field( 'fuentes', $filas, $x['id'] );
	}
	if ( ! empty( $x['nota'] ) ) {
		// La fuente del trámite de Entre Ríos de la nota del permiso: fecha de lectura.
		$filas = pm_filas( 'fuentes', $x['id'] );
		foreach ( $filas as $n => $f ) {
			if ( false !== strpos( (string) ( $f['url'] ?? '' ), 'tramite/708' ) ) {
				$filas[ $n ]['consultada'] = $hoy;
			}
		}
		update_field( 'fuentes', $filas, $x['id'] );
	}
	update_field( 'actualizado', $hoy, $x['id'] );
	WP_CLI::log( "✓ {$x['que']} ($i) #{$x['id']}: " . ( $x['antes'] !== $x['despues'] ? 'contenido actualizado' : 'contenido sin cambios' ) );
}

// ---- Lectura de vuelta ------------------------------------------------------------------------------------------------------------------------
$ok = true;
foreach ( $plan as $x ) {
	$i = $x['i'];
	$c = get_post( $x['id'] )->post_content;
	$norm     = fn( string $v ) => preg_replace( '/\s+/u', ' ', $v );
	$nuevo_ok = $norm( $c ) === $norm( $x['despues'] );
	WP_CLI::log( ( $nuevo_ok ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) #{$x['id']} contenido " . ( $c === $x['despues'] ? 'igual al esperado' : 'distinto del esperado' ) );
	$ok = $ok && $nuevo_ok;
	if ( ! empty( $x['faqs_final'] ) ) {
		$leidas = pm_filas( 'faqs', $x['id'] );
		$b      = count( $leidas ) === count( $x['faqs_final'] );
		foreach ( $x['faqs'] as [ , [ $q, $a ] ] ) {
			$hallada = false;
			foreach ( $leidas as $f ) {
				if ( ( $f['pregunta'] ?? '' ) === $q && ( $f['respuesta'] ?? '' ) === $a ) {
					$hallada = true;
				}
			}
			$b = $b && $hallada;
		}
		WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) FAQ leídas de vuelta: " . count( $leidas ) );
		$ok = $ok && $b;
	}
	if ( ! empty( $x['fuentes'] ) ) {
		$leidas = pm_filas( 'fuentes', $x['id'] );
		$nombres = array_column( $leidas, 'nombre' );
		$b = true;
		foreach ( $x['fuentes'] as [ $nombre, $url ] ) {
			$b = $b && ( in_array( $nombre, $nombres, true ) || ( $url && in_array( $url, array_column( $leidas, 'url' ), true ) ) );
		}
		WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) fuentes leídas de vuelta: " . count( $leidas ) );
		$ok = $ok && $b;
	}
	foreach ( $x['campos'] ?? [] as $campo => $v ) {
		$b = (string) pm_campo( $campo, $x['id'] ) === $v;
		WP_CLI::log( ( $b ? 'VERIFIED' : 'FALLÓ' ) . ": {$x['que']} ($i) campo $campo" );
		$ok = $ok && $b;
	}
}
$ok || WP_CLI::error( 'alguna lectura de vuelta no coincide.' );
WP_CLI::success( 'Aplicado: ' . count( $plan ) . ' páginas revisadas.' );
