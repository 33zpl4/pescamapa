<?php
/**
 * Seeds the outfitter section for Goya and Esquina (owner, 20/10/2026): one
 * record per outfitter we know of, with EXAMPLE values where we don't know
 * the real ones, so the section is in place and gets filled by hand.
 * Run with wp-script.yml (script = prestadores-2026-10-20).
 *
 * Every record is marked "Datos de ejemplo" and without consent: none is
 * public. Logged-in editors see them in the guide as dashed preview cards.
 * Each record's "Notas internas" says which data is real (from
 * docs/investigacion/esquina.md) and which is an example.
 *
 * Idempotent: skips a title that already exists.
 */

$ejemplo = 'DATOS DE EJEMPLO: equipo, lancha, motor, capacidad, qué incluye y precio son inventados hasta confirmarlos con el prestador.';

$lista = [
	// --- Esquina (nombres reales, de la investigación) ---------------------------
	[ 'Don Joaquín River Lodge', 'esquina', [ 'alojamiento', 'operador' ], [ 'mosca', 'spinning' ], [ 'dorado', 'surubi' ],
		'Lodge familiar sobre el río Corriente, especializado en dorado con mosca. Pensión completa con guía y lancha.', 'Tracker de 5,5 m con plataforma', 'Fuera de borda 60 HP + eléctrico de proa', 2,
		[ 'lancha', 'motor_electrico', 'plataforma', 'chalecos' ], [ 'comida', 'bebidas', 'alojamiento', 'traslados', 'combustible' ],
		'Desde USD 5.825 por persona, 7 noches y 6 días de pesca', '20261020', 'Septiembre – abril',
		'REAL (listados de operadores): 8 habitaciones, hasta 16 pescadores, 7 noches / 6 días de pesca, pensión completa, guía y lancha compartidos, traslado desde Esquina. Precio USD 5.825 pp para 14–21/11/2026 (frontierstrvl.co.uk). Fuentes: fishingbooker.com/es/charters/view/15938, aardvarkmcleod.com. Web e Instagram propios: buscar.' ],
	[ 'Nomads Outfitters', 'esquina', [ 'alojamiento', 'operador' ], [ 'mosca' ], [ 'dorado' ],
		'Lodge de pesca con mosca en los ríos Corriente y Paraná, con devolución.', 'Lancha de 5 m con plataforma', 'Fuera de borda 40 HP', 2,
		[ 'lancha', 'plataforma', 'chalecos' ], [ 'comida', 'bebidas', 'alojamiento' ],
		'Desde USD 3.500 por persona, paquete de 5 días', '20261020', 'Octubre – abril',
		'REAL (listados): incluye comidas, bebidas, guía y lavandería; no incluye licencia, traslados ni propinas; seña 50 %; catch and release. Capacidad 6–12 según listado. fishingbooker.com/charters/view/19931. Verificar si sigue operando.' ],
	[ 'Hotel Puerto Valle', 'esquina', [ 'alojamiento' ], [ 'mosca', 'spinning' ], [ 'dorado' ],
		'Hotel con paquetes de pesca hacia el Paraná.', 'Lancha de 6 m', 'Fuera de borda 90 HP', 3,
		[ 'lancha', 'chalecos' ], [ 'comida', 'alojamiento', 'combustible' ],
		'Desde $ 250.000 por persona y noche con pesca', '20261020', 'Todo el año según el río',
		'REAL: hotelpuertovalle.com, paquetes de pesca con mosca. DUDA: confirmar que opere en la zona de Esquina.' ],
	[ 'Gabriel Gutiérrez — guía de pesca', 'esquina', [ 'guia' ], [ 'trolling', 'carnada' ], [ 'surubi', 'dorado' ],
		'Guía de pesca embarcada en el Paraná frente a Esquina, especialista en surubí con trolling.', 'Lancha de 5,8 m', 'Fuera de borda 90 HP', 3,
		[ 'lancha', 'sonda', 'equipos', 'chalecos' ], [ 'carnada', 'combustible', 'equipos' ],
		'Desde $ 180.000 por lancha, día completo', '20261020', 'Marzo – agosto (surubí)',
		'REAL: nombrado en Elonce (diciembre 2025), trolling de surubí. Sin contacto: buscar en Instagram/Facebook.' ],
	[ 'Matías Pavoni — guía de pesca', 'esquina', [ 'guia' ], [ 'spinning' ], [ 'dorado' ],
		'Guía de pesca con señuelos artificiales, especialista en dorado.', 'Tracker de 5,2 m con plataforma', 'Fuera de borda 60 HP + eléctrico de proa', 2,
		[ 'lancha', 'motor_electrico', 'plataforma', 'chalecos' ], [ 'combustible', 'equipos' ],
		'Desde $ 160.000 por lancha, día completo', '20261020', 'Octubre – abril (dorado)',
		'REAL: nombrado en El Litoral (2021), dorado de 103 cm con señuelos. Verificar si sigue activo y su contacto.' ],
	[ 'Posada Morotí', 'esquina', [ 'alojamiento' ], [ 'carnada', 'spinning' ], [ 'dorado', 'surubi' ],
		'Posada en Esquina con plan de pesca.', '', '', 0,
		[], [ 'comida', 'alojamiento' ],
		'Desde $ 90.000 por persona y noche', '20261020', 'Todo el año',
		'REAL: nombrada por La Nación (2022) entre las posadas de Esquina con pesca. Verificar si ofrece salidas o trabaja con guías.' ],
	[ 'Cabañas Ara Pyahu', 'esquina', [ 'alojamiento' ], [], [],
		'Cabañas para grupos de pescadores.', '', '', 0,
		[], [ 'alojamiento' ],
		'Desde $ 60.000 por persona y noche', '20261020', 'Todo el año',
		'REAL: cabaña para 4 (listado sin fecha). DUDA: ubicación exacta. Solo alojamiento.' ],
	// --- Goya (ejemplos para completar) -----------------------------------------
	[ 'Agus — guía de pesca en Goya', 'goya', [ 'guia', 'operador' ], [ 'carnada', 'spinning' ], [ 'surubi', 'dorado' ],
		'Guía y operador local de Goya, con su familia.', 'Lancha de 5,8 m con plataforma', 'Fuera de borda 90 HP + eléctrico de proa', 3,
		[ 'lancha', 'motor_electrico', 'plataforma', 'sonda', 'chalecos' ], [ 'carnada', 'comida', 'combustible' ],
		'Desde $ 200.000 por lancha, día completo', '20261020', 'Todo el año; surubí marzo – agosto',
		'REAL: contacto de Cheyenne, uno de los operadores grandes de Goya (respondió qué mira un pescador al elegir guía). Completar nombre, contacto, lancha, motor y precios, y pedirle una entrevista.' ],
	[ 'Ejemplo — complejo de cabañas en Goya', 'goya', [ 'alojamiento' ], [ 'carnada' ], [ 'surubi' ],
		'Ejemplo de ficha de alojamiento: reemplazar por un complejo real.', '', '', 0,
		[], [ 'alojamiento', 'comida' ],
		'Desde $ 70.000 por persona y noche', '20261020', 'Todo el año',
		'EJEMPLO completo: reemplazar el nombre y todos los datos por un alojamiento real, o borrar.' ],
	[ 'Ejemplo — operador con paquete en Goya', 'goya', [ 'operador' ], [ 'spinning', 'trolling' ], [ 'dorado', 'surubi' ],
		'Ejemplo de ficha de operador con paquete: reemplazar por uno real.', 'Lancha de 6 m', 'Fuera de borda 115 HP', 4,
		[ 'lancha', 'sonda', 'equipos', 'chalecos' ], [ 'carnada', 'comida', 'alojamiento', 'traslados', 'combustible' ],
		'Desde $ 450.000 por persona, 2 días con alojamiento', '20261020', 'Octubre – abril',
		'EJEMPLO completo: reemplazar el nombre y todos los datos por un operador real, o borrar.' ],
];

foreach ( $lista as [ $nombre, $destino, $tipo, $mod, $esp, $desc, $lancha, $motor, $cap, $equipo, $incluye, $precio, $fecha, $temporada, $notas ] ) {
	if ( get_posts( [ 'post_type' => 'pm_prestador', 'post_status' => 'any', 'title' => $nombre, 'fields' => 'ids' ] ) ) {
		WP_CLI::log( "· $nombre: ya existe" );
		continue;
	}
	$id = wp_insert_post( [ 'post_type' => 'pm_prestador', 'post_status' => 'publish', 'post_title' => $nombre ], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::warning( "$nombre: " . $id->get_error_message() );
		continue;
	}
	$campos = [
		'tipo'                 => $tipo,
		'prest_destino'        => $destino,
		'localidad'            => 'esquina' === $destino ? 'Esquina' : 'Goya',
		'descripcion'          => $desc,
		'prest_modalidades'    => $mod,
		'prest_especies'       => $esp,
		'lancha'               => $lancha,
		'motor'                => $motor,
		'capacidad'            => $cap ?: '',
		'prest_temporada'      => $temporada,
		'equipo'               => $equipo,
		'incluye'              => $incluye,
		'precio'               => $precio,
		'precio_fecha'         => $fecha,
		'estado'               => 'relevado',
		'datos_ejemplo'        => 1,
		'prest_consentimiento' => 0,
		'notas'                => $notas . "\n\n" . $ejemplo,
	];
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	WP_CLI::log( "✓ $nombre ($destino) #$id" );
}
WP_CLI::success( 'Prestadores cargados como datos de ejemplo (no públicos).' );
