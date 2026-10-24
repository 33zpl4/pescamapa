<?php
/**
 * San Martín y Junín de los Andes (Neuquén), 24/10/2026: fifth destination guide, in Spanish and English.
 * Built from docs/investigacion/san-martin-junin.md. Next in the SEO matrix after La Paz
 * (docs/seo-oportunidades.md). One combined guide, as in docs/seo-guia-wireframes.md (model 2).
 * Same `clave` as the "próximamente" card (san-martin-junin), which hides itself.
 *
 * Patagonia, not the Litoral: a different authority (Dirección Provincial de Fauna de Neuquén
 * and, inside the park, Parques Nacionales) and a different rule book (Reglamento de Pesca
 * Continental Patagónico). Nothing from Corrientes or Entre Ríos applies.
 *
 * What is published: the official 2025/2026 booklet of the Reglamento (Parte Primera and Anexo
 * Neuquén, pp. 7-13 and 33-49, sent by the owner on 24/10/2026), always with its year because the
 * 2026-2027 edition is not out; plus what two press outlets agree on (permit sales since
 * 15/10/2026, season opens 1 November). Not confirmed, so said so and kept as hidden PENDIENTE
 * notes: the 2026-2027 fees and dates, the Parques Nacionales annex (Lácar, Huechulafquen) and
 * any outfitter (no opened source, none listed).
 *
 * Idempotent: a guide that exists is never touched. Dry run was clean (es + en, 10 FAQs and 5 sources each; applied);
 * run with wp-script.yml (script = san-martin-junin-2026-10-24).
 */

$aplicar = true;
$hoy     = '20261024';

function pm_smj_p( string $html ): string {
	return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->";
}
function pm_smj_h( string $texto ): string {
	return "<!-- wp:heading -->\n<h2 class=\"wp-block-heading\">{$texto}</h2>\n<!-- /wp:heading -->";
}
function pm_smj_lista( array $items ): string {
	$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
	return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->";
}
function pm_smj_pendiente( string $texto ): string {
	return "<!-- wp:quote -->\n<blockquote class=\"wp-block-quote\"><!-- wp:paragraph -->\n<p>PENDIENTE: {$texto}</p>\n<!-- /wp:paragraph --></blockquote>\n<!-- /wp:quote -->";
}

$permisos_url = 'https://cazaypesca.neuquen.gob.ar/permisos';

$fuentes = [
	[ 'Reglamento de Pesca Deportiva Continental Patagónico 2025/2026: Parte Primera y Anexo Neuquén (folleto oficial y portal interprovincial)', 'https://reglamentodepesca.org.ar/reglamento/', 1 ],
	[ 'Caza y Pesca de Neuquén (Dirección Provincial de Fauna): permisos de pesca', $permisos_url, 1 ],
	[ 'Neuquén Turismo: folleto digital de pesca 2026', 'https://turismo.neuquen.gob.ar/wp-content/uploads/2026/06/Folleto_Pesca_Digital-2026.pdf', 1 ],
	[ 'Noticias NQN: Neuquén habilitó la compra de permisos para la nueva temporada de pesca (15/10/2026)', 'https://www.noticiasnqn.com.ar/noticias/2026/10/15/356687-neuquen-habilito-la-compra-de-permisos-para-la-nueva-temporada-de-pesca', 0 ],
	[ 'ANBariloche: arranca la venta de permisos de pesca para la temporada 2026-2027 (16/10/2026)', 'https://www.anbariloche.com.ar/noticias/2026/10/16/120191-arranca-la-venta-de-permisos-de-pesca-para-la-temporada-2026-2027', 0 ],
];
$fuentes_filas = array_map( fn( $f ) => [ 'nombre' => $f[0], 'url' => $f[1], 'oficial' => $f[2], 'consultada' => $hoy ], $fuentes );

$comun = [
	'clave'                => 'san-martin-junin',
	'autor'                => 'Equipo Pescamapa',
	'actualizado'          => $hoy,
	'fuentes'              => $fuentes_filas,
	'reglamento_autoridad' => 'Dirección Provincial de Fauna de Neuquén',
	'reglamento_url'       => $permisos_url,
];

$guias = [];

// --- Español ---------------------------------------------------------------
$guias['es'] = [
	'titulo' => 'San Martín y Junín de los Andes',
	'slug'   => 'pesca-san-martin-junin-de-los-andes',
	'cuerpo' => implode( "\n\n", [
		pm_smj_p( 'San Martín de los Andes y Junín de los Andes están en el sudoeste de <strong>Neuquén</strong>, en plena <strong>Patagonia</strong>, a unos 40 km una de la otra por la ruta nacional 234. Es otra pesca que la del Litoral: truchas de río y de lago, y sobre todo <strong>pesca con mosca</strong>. Los ambientes más nombrados son el <strong>río Chimehuín</strong> (que nace en el lago Huechulafquen y pasa por Junín), el <strong>Malleo</strong>, el <strong>Collón Curá</strong>, el <strong>Quilquihue</strong> y los lagos <strong>Lácar</strong> y <strong>Lolog</strong>.' ),
		pm_smj_pendiente( 'confirmar con una fuente oficial abierta la distancia, las especies (arco iris, marrón, fontinalis) y qué ambientes están en el Parque Nacional Lanín.' ),
		pm_smj_h( 'Temporada 2026-2027: no toda la Patagonia abre el mismo día' ),
		pm_smj_p( 'Según la prensa regional (consultada el 24/10/2026), la <strong>temporada de pesca en la Patagonia empieza el 1 de noviembre de 2026</strong> y los permisos 2026-2027 se venden online desde el 15 de octubre. Cada ambiente tiene su propia fecha de cierre. En la edición <strong>2025-2026</strong> del Reglamento (la última publicada al 24/10/2026), la temporada general iba del 1 de noviembre de 2025 al 1 de mayo de 2026, y el <strong>Chimehuín, el Malleo, el Collón Curá y el Quilquihue cerraban el 31 de mayo de 2026</strong>. Las fechas de la edición 2026-2027 todavía no estaban publicadas: confirmalas antes de salir.' ),
		pm_smj_h( 'Permiso de pesca en Neuquén' ),
		pm_smj_p( 'Para pescar hace falta el permiso de la temporada. Se compra online en el sitio oficial de <a href="' . $permisos_url . '" rel="noopener">Caza y Pesca de Neuquén</a>. El permiso es personal y se exhibe junto con el documento de identidad. Los valores del <a href="https://reglamentodepesca.org.ar/reglamento/" rel="noopener">Reglamento de Pesca Deportiva Continental Patagónico</a>, edición <strong>2025-2026</strong> (los de 2026-2027 todavía no figuraban), son, en pesos:' ),
		pm_smj_lista( [
			'Residentes en el país: diario <strong>$10.000</strong>; 10 días <strong>$20.000</strong>; temporada <strong>$40.000</strong>; menores de 13 a 17 años, temporada $20.000.',
			'Sin cargo: mayores de 65, jubilados y pensionados, menores de hasta 12 años y personas con discapacidad con acreditación oficial.',
			'Extranjeros no residentes: diario <strong>$50.000</strong>; 10 días <strong>$140.000</strong>; temporada <strong>$280.000</strong>.',
			'Pesca de arrastre (trolling), además del permiso ordinario: diario $20.000; 10 días $40.000; temporada $80.000.',
			'<strong>Zona preferencial</strong> (La Boca del Chimehuín y el río Correntoso), además del permiso ordinario: diario $10.000; 10 días $20.000; temporada $40.000. Vale solo en Neuquén.',
		] ),
		pm_smj_pendiente( 'valores del permiso 2026-2027: cuando el portal del reglamento o Caza y Pesca de Neuquén los publiquen, reemplazar la lista y quitar el año 2025-2026.' ),
		pm_smj_h( 'Reglas por río y lago (Anexo Neuquén, edición 2025-2026)' ),
		pm_smj_p( 'Estas son las disposiciones del Anexo Neuquén de la edición 2025-2026 para los ambientes de la zona. Pueden cambiar cada temporada: confirmalas con tu guía o con la Dirección Provincial de Fauna.' ),
		pm_smj_lista( [
			'<strong>Chimehuín, Zona I (La Boca):</strong> desde la naciente hasta 250 m aguas abajo del puente. Es zona preferencial: pide el permiso adicional. <strong>Solo mosca</strong>, anzuelo sin rebaba, <strong>devolución obligatoria de todos los peces</strong>. Está prohibida la navegación y toda otra actividad acuática. Cierre 2025-2026: 31 de mayo.',
			'<strong>Chimehuín, Zona II</strong> (hasta la confluencia con el Collón Curá): mosca, spinning o spinning con mosca; devolución obligatoria de todos los salmónidos, anzuelo simple sin rebaba; límite de 1 perca. Cierre 2025-2026: 31 de mayo.',
			'<strong>Malleo:</strong> entre el límite con el Parque Nacional Lanín y el puente de la ruta 23, solo mosca y devolución obligatoria de todos los peces. Desde el puente de la ruta 23 hasta el Aluminé, mosca o spinning con mosca, anzuelo sin rebaba, devolución de los salmónidos y límite de 1 perca. Cierre 2025-2026: 31 de mayo.',
			'<strong>Collón Curá:</strong> mosca, spinning o spinning con mosca; devolución obligatoria de los salmónidos, anzuelo simple sin rebaba; límite de 2 percas. Cierre 2025-2026: 31 de mayo.',
			'<strong>Quilquihue:</strong> desde la boca hasta el final de La Laguna, mosca o spinning con mosca (límite de 2 percas, devolución de los salmónidos); desde ahí hasta el Chimehuín también se permite spinning, con anzuelo simple sin rebaba. Cierre 2025-2026: 31 de mayo.',
			'<strong>Lago Lolog</strong> (compartido con el Parque Nacional Lanín): mosca, spinning, arrastre o spinning con mosca; límite de 2 salmónidos; devolución de las percas. Desde el 2 de mayo de 2026, sin arrastre y con devolución de todo. Cierre 2025-2026: 31 de octubre.',
		] ),
		pm_smj_h( 'Dentro del Parque Nacional Lanín' ),
		pm_smj_p( '<strong>Lácar y Huechulafquen</strong> están en el Parque Nacional Lanín: no figuran en el Anexo Neuquén sino en el de Parques Nacionales. Reglas generales de ese anexo (edición 2025-2026):' ),
		pm_smj_lista( [
			'Solo se puede pescar en los cuerpos de agua que el anexo lista; <strong>todo cuerpo de agua no mencionado está excluido</strong>. Lo que el listado no especifica se rige por el reglamento general.',
			'Se prohíbe sacrificar y tener ejemplares de especies autóctonas, y transportar o liberar peces vivos.',
			'Se prohíben los motores de dos tiempos a carburación en las embarcaciones, incluso auxiliares y apagados; se permiten los de cuatro tiempos o los de dos tiempos de emisiones ultra bajas (hay que llevar el manual del motor).',
			'Está prohibido navegar con motor por los ríos y arroyos y la flotación (drift) de los ríos, salvo en el Arrayanes, Frey, Hua Hum, Limay, Manso, Rivadavia y Puelo.',
			'Se accede a las costas solo por senderos habilitados. No se puede llevar perros ni otras mascotas ni mover troncos de la costa, por el huillín: si ves uno, avisá al guardaparque.',
			'Se permite el dropper y la pesca al hilo (hasta dos moscas, un anzuelo simple sin rebaba cada una). El calzado de vadeo con suela de fieltro está prohibido.',
			'El "día de pesca" son las horas de luz entre el crepúsculo matutino y vespertino que publica Hidrografía Naval para la ciudad más cercana.',
		] ),
		pm_smj_p( 'Lo que dice el listado de ese anexo (edición 2025-2026) para los ambientes de la zona:' ),
		pm_smj_lista( [
			'<strong>Lago Lácar:</strong> pesca permitida del 1 de noviembre de 2025 al 31 de mayo de 2026 y durante octubre de 2026; límite de 2 salmónidos por día durante toda la temporada.',
			'<strong>Lago Huechulafquen:</strong> mismas fechas; límite de 2 salmónidos; el arrastre (trolling) está habilitado salvo desde el arroyo Blanco hasta el río Chimehuín y 500 m más hasta Quilantral. Figura con alerta de didymo.',
			'<strong>Lago Paimún y Epulafquen:</strong> límite de 2 salmónidos por día. En el <strong>río Paimún</strong> solo se pesca en el curso medio e inferior, con límite de 1 salmónido.',
			'<strong>Malleo dentro del parque:</strong> prohibida la navegación, solo mosca y devolución obligatoria de todo.',
			'<strong>Lago Lolog:</strong> límite de 2 salmónidos por día durante toda la temporada (ambiente compartido con Neuquén).',
		] ),
		pm_smj_p( 'El reglamento no menciona un ingreso al parque ni un permiso aparte para pescar: confirmalo con la Intendencia del Parque Nacional Lanín, que atiende en San Martín de los Andes (Perito Moreno 1008, 2972-427233, lanin@apn.gob.ar).' ),
		pm_smj_pendiente( 'confirmar con la Intendencia del Lanín si se cobra ingreso al parque a quien va a pescar (el reglamento 2025-2026 no lo dice) y las fechas 2026-2027 de Lácar y Huechulafquen.' ),
		pm_smj_h( 'Reglas generales' ),
		pm_smj_lista( [
			'Se pesca solo de día, con un solo equipo por pescador y un señuelo artificial con un único anzuelo.',
			'En ríos y arroyos, y hasta 200 metros de su naciente o desembocadura en un lago, hay que devolver todos los peces con anzuelo simple sin rebaba o con la rebaba aplastada, salvo lo que diga el listado de cada ambiente. En Neuquén no se pueden sacrificar salmónidos salvo las excepciones del listado, y el salmón del Atlántico siempre se devuelve.',
			'Se permite el "dropper" (hasta dos moscas con un anzuelo simple cada una), salvo en La Boca del Chimehuín y en el Correntoso.',
			'En todos los ríos de Neuquén está prohibida la navegación con motor (con excepción del Limay).',
			'Está prohibido pescar desde puentes, pasarelas y estructuras similares, y encender fuego en las costas de los ríos fuera de los sitios habilitados.',
			'Está prohibido el calzado de vadeo con <strong>suela de fieltro</strong> y el plomo en anclas, plomadas y señuelos.',
			'El reglamento recomienda no vadear los ríos y arroyos en noviembre, para no dañar las camas de desove.',
		] ),
		pm_smj_p( '<strong>Didymo:</strong> el reglamento obliga a pescadores, guías y prestadores a desinfectar equipos, embarcaciones y vehículos cuando la autoridad lo pida, y Neuquén recuerda que las medidas de bioseguridad contra el alga son obligatorias en todos los ambientes. Antes de pasar de un río o lago a otro, limpiá y secá tu equipo.' ),
		pm_smj_h( 'Guías y alojamientos' ),
		pm_smj_p( 'Para pescar con mosca en estos ríos lo habitual es salir con un guía: conoce el nivel del río, qué se pesca ese día y los accesos. El reglamento de Neuquén prohíbe contratar guías que no estén habilitados como prestadores turísticos de la provincia: la nómina está en <a href="https://www.neuquentur.gob.ar/" rel="noopener">neuquentur.gob.ar</a>. Todavía no cargamos guías ni lodges propios: no publicamos ninguno sin una fuente que hayamos podido abrir. Si sos guía o tenés un lodge en la zona, escribinos a info@pescamapa.com.' ),
		pm_smj_p( 'La Dirección Provincial de Fauna atiende en San Martín de los Andes (Mariano Moreno 465, 0297 241-2211) y en Junín de los Andes (Roca 29, 0297 249 1277), según el reglamento 2025-2026.' ),
		pm_smj_h( 'Cómo llegar' ),
		pm_smj_p( 'San Martín de los Andes tiene el aeropuerto de Chapelco (CPC). Junín de los Andes queda a unos 40 km por la ruta nacional 234. Los accesos a muchos puntos de pesca son de ripio: preguntá por su estado antes de salir. Solo se puede entrar a los ambientes de pesca por los accesos habilitados.' ),
		pm_smj_pendiente( 'confirmar distancia San Martín–Junín (≈40 km RN 234), vuelos a Chapelco y estado de los accesos de ripio.' ),
	] ),
	'campos' => [
		'h1'          => 'Pesca con mosca en San Martín y Junín de los Andes: temporada, permiso y reglas del Chimehuín y el Malleo',
		'bajada'      => 'Chimehuín, Malleo, Collón Curá, Quilquihue y los lagos Lácar y Lolog, en Neuquén. Cuándo abre la temporada 2026-2027, cómo se compra el permiso y qué dice el reglamento de cada río.',
		'modalidades' => "Mosca (vadeo y flotada)\nSpinning con señuelo mosca, donde esté permitido\nSalida guiada",
		'epoca'       => 'La temporada de pesca en la Patagonia empieza el 1 de noviembre; los ríos principales cerraron el 31 de mayo en 2026.',
		'seo_title'    => 'Pesca con mosca en San Martín y Junín de los Andes (2026/2027)',
		'seo_description' => 'Guía de pesca en San Martín y Junín de los Andes, Neuquén: reglas del Chimehuín, Malleo, Collón Curá y Quilquihue, temporada 2026-2027 (abre el 1 de noviembre), permiso de pesca y valores de referencia.',
		'temporada'   => [
			[ 'meses' => 'Desde el 1 de noviembre', 'que' => 'Abre la temporada 2026-2027 en la Patagonia (según la prensa). Cada ambiente tiene su fecha de cierre.' ],
			[ 'meses' => 'Hasta mayo', 'que' => 'En 2026 el Chimehuín, el Malleo, el Collón Curá y el Quilquihue cerraron el 31 de mayo; la temporada general, el 1 de mayo (reglamento 2025-2026). Confirmá las de 2027.' ],
			[ 'meses' => 'Antes de cada salida', 'que' => 'Confirmá la modalidad, el límite y los cierres de ese río o lago con la Dirección Provincial de Fauna o con tu guía.' ],
		],
		'reglamento_resumen' => 'La temporada de pesca en la Patagonia empieza el 1 de noviembre de 2026; los permisos 2026-2027 se venden online desde el 15 de octubre en Caza y Pesca de Neuquén (consultado el 24/10/2026). Reglas del Anexo Neuquén, edición 2025-2026 (la última publicada): en la Boca del Chimehuín solo mosca y devolución de todos los peces; en el Malleo alto, solo mosca y devolución; Chimehuín, Malleo, Collón Curá y Quilquihue cerraron el 31 de mayo de 2026; prohibido el calzado con suela de fieltro. Permiso de referencia 2025-2026: residentes $10.000 por día, $20.000 por 10 días, $40.000 la temporada; extranjeros $50.000, $140.000 y $280.000. Lácar y Huechulafquen (Parque Nacional Lanín): rigen las reglas del anexo de Parques Nacionales (solo ambientes listados, sin motores de dos tiempos a carburación, sin fieltro); Lácar y Huechulafquen se pescaban del 1/11 al 31/5 y en octubre de 2026, con límite de 2 salmónidos; fechas 2026-2027 sin confirmar.',
		'aguas' => [
			[ 'nombre' => 'Río Chimehuín', 'nota' => 'La Boca (Zona I): solo mosca, devolución obligatoria y permiso adicional (reglamento 2025-2026). Zona II hasta el Collón Curá.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Río Malleo', 'nota' => 'Tramo alto: solo mosca y devolución de todos los peces (reglamento 2025-2026).', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Lago Lácar', 'nota' => 'Frente a San Martín de los Andes, en el Parque Nacional Lanín: 2 salmónidos por día; pesca del 1/11 al 31/5 y en octubre (reglamento 2025-2026).', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => '¿Cuándo empieza la temporada de pesca 2026-2027 en San Martín y Junín de los Andes?', 'respuesta' => 'Según la prensa regional (consultada el 24/10/2026), la temporada de pesca en la Patagonia empieza el 1 de noviembre de 2026. Cada ambiente tiene su cierre: en la edición 2025-2026 del reglamento, el Chimehuín, el Malleo, el Collón Curá y el Quilquihue cerraban el 31 de mayo y la temporada general el 1 de mayo. Las fechas 2026-2027 todavía no estaban publicadas.' ],
			[ 'pregunta' => '¿Dónde se compra el permiso de pesca de Neuquén?', 'respuesta' => 'Online, en el sitio de Caza y Pesca de Neuquén (cazaypesca.neuquen.gob.ar/permisos). Según la prensa, la venta de la temporada 2026-2027 abrió el 15 de octubre de 2026.' ],
			[ 'pregunta' => '¿Cuánto cuesta el permiso de pesca en Neuquén?', 'respuesta' => 'El Reglamento de Pesca Deportiva Continental Patagónico (edición 2025-2026, consultada el 24/10/2026) fija, para residentes en el país: $10.000 por día, $20.000 por 10 días y $40.000 la temporada; para extranjeros no residentes: $50.000 por día, $140.000 por 10 días y $280.000 la temporada. La pesca de arrastre y la zona preferencial (La Boca del Chimehuín) suman un permiso adicional. Los valores 2026-2027 todavía no figuraban: confirmalos al comprar.' ],
			[ 'pregunta' => '¿Qué reglas rigen en la Boca del Chimehuín?', 'respuesta' => 'Según el Anexo Neuquén 2025-2026: solo pesca con mosca, anzuelo sin rebaba y devolución obligatoria de todos los peces, desde la naciente hasta 250 metros aguas abajo del puente. Es zona preferencial: hace falta un permiso adicional además del ordinario, y está prohibida la navegación y toda otra actividad acuática. Confirmá que no cambió para 2026-2027.' ],
			[ 'pregunta' => '¿Se puede pescar con spinning en el Malleo?', 'respuesta' => 'En el tramo alto (hasta el puente de la ruta 23), no: solo mosca y devolución de todos los peces. Más abajo, hasta el Aluminé, se permite mosca o spinning con señuelo mosca, con anzuelo sin rebaba. Es lo que decía el Anexo Neuquén 2025-2026.' ],
			[ 'pregunta' => '¿Qué reglas rigen en los lagos Lácar y Huechulafquen?', 'respuesta' => 'Están en el Parque Nacional Lanín y se rigen por el anexo de Parques Nacionales del reglamento. Reglas generales (edición 2025-2026): solo se pesca en los ambientes que el anexo lista, no se pueden usar motores de dos tiempos a carburación, no se permiten perros ni mascotas, y se prohíbe el calzado con suela de fieltro. Según el listado 2025-2026: el Lácar y el Huechulafquen se pescaban del 1 de noviembre al 31 de mayo y en octubre de 2026, con límite de 2 salmónidos por día; en el Huechulafquen el arrastre estaba habilitado salvo cerca de la boca del Chimehuín. Confirmá las fechas 2026-2027 con tu guía o en la Intendencia del Parque Nacional Lanín (San Martín de los Andes, 2972-427233).' ],
			[ 'pregunta' => '¿Se puede pescar en bote o en balsa (drift) en los ríos del Parque Nacional Lanín?', 'respuesta' => 'Según el anexo de Parques Nacionales 2025-2026, está prohibida la flotación de los ríos y arroyos del parque, salvo en el Arrayanes, Frey, Hua Hum, Limay, Manso, Rivadavia y Puelo, y no se puede navegar con motor por los ríos. Confirmá cada tramo con tu guía.' ],
			[ 'pregunta' => '¿Hace falta un guía habilitado?', 'respuesta' => 'No es obligatorio ir con guía, pero si lo contratás, el reglamento de Neuquén prohíbe contratar guías que no estén habilitados como prestadores turísticos de la provincia. La nómina está en neuquentur.gob.ar.' ],
			[ 'pregunta' => '¿Hay que desinfectar el equipo de pesca (didymo)?', 'respuesta' => 'Sí: el reglamento obliga a pescadores, guías y prestadores a desinfectar equipos, embarcaciones y vehículos cuando lo requiera la autoridad, y prohíbe el calzado de vadeo con suela de fieltro. Limpiá y secá el equipo antes de pasar de un río o lago a otro.' ],
			[ 'pregunta' => '¿Cómo llego a San Martín y Junín de los Andes?', 'respuesta' => 'San Martín de los Andes tiene el aeropuerto de Chapelco (CPC). Junín de los Andes queda a unos 40 km por la ruta nacional 234. Los accesos a muchos puntos de pesca son de ripio.' ],
		],
	],
];

// --- English ---------------------------------------------------------------
$guias['en'] = [
	'titulo' => 'San Martín and Junín de los Andes',
	'slug'   => 'fishing-san-martin-junin-de-los-andes',
	'cuerpo' => implode( "\n\n", [
		pm_smj_p( 'San Martín de los Andes and Junín de los Andes are in southwestern <strong>Neuquén</strong>, in <strong>Patagonia</strong>, about 40 km apart on national route 234. It is a different kind of fishing from the Litoral: river and lake trout, and above all <strong>fly fishing</strong>. The best-known waters are the <strong>Chimehuín</strong> (which flows out of Lake Huechulafquen past Junín), <strong>Malleo</strong>, <strong>Collón Curá</strong> and <strong>Quilquihue</strong> rivers and <strong>Lakes Lácar</strong> and <strong>Lolog</strong>.' ),
		pm_smj_pendiente( 'confirm distance, species (rainbow, brown, brook) and which waters are inside Lanín National Park with an open official source.' ),
		pm_smj_h( '2026-2027 season: not all of Patagonia opens on the same day' ),
		pm_smj_p( 'According to regional press (checked 24/10/2026), the <strong>Patagonian fishing season opens on 1 November 2026</strong> and 2026-2027 permits have been on sale online since 15 October. Each water has its own closing date. In the <strong>2025-2026</strong> edition of the regulation (the latest published on 24/10/2026) the general season ran from 1 November 2025 to 1 May 2026, and the <strong>Chimehuín, Malleo, Collón Curá and Quilquihue closed on 31 May 2026</strong>. The 2026-2027 dates were not published yet: confirm them before you go.' ),
		pm_smj_h( 'Fishing permit in Neuquén' ),
		pm_smj_p( 'You need the season permit to fish. It is bought online on the official <a href="' . $permisos_url . '" rel="noopener">Caza y Pesca de Neuquén</a> site, and shown together with your ID. Fees in the <a href="https://reglamentodepesca.org.ar/reglamento/" rel="noopener">Patagonian Continental Sport Fishing Regulation</a>, <strong>2025-2026</strong> edition (2026-2027 fees were not shown yet), in Argentine pesos:' ),
		pm_smj_lista( [
			'Residents of Argentina: daily <strong>$10,000</strong>; 10 days <strong>$20,000</strong>; season <strong>$40,000</strong>; ages 13 to 17, season $20,000.',
			'Free: over 65, retirees and pensioners, children up to 12 and people with an officially certified disability.',
			'Foreign non-residents: daily <strong>$50,000</strong>; 10 days <strong>$140,000</strong>; season <strong>$280,000</strong>.',
			'Trolling, on top of the ordinary permit: daily $20,000; 10 days $40,000; season $80,000.',
			'<strong>Preferential zone</strong> (the Boca of the Chimehuín and the Correntoso River), on top of the ordinary permit: daily $10,000; 10 days $20,000; season $40,000. Valid in Neuquén only.',
		] ),
		pm_smj_h( 'Water-by-water rules (Neuquén Annex, 2025-2026 edition)' ),
		pm_smj_p( 'These are the Neuquén Annex rules of the 2025-2026 edition for the waters of the area. They can change every season: confirm them with your guide or the provincial wildlife directorate.' ),
		pm_smj_lista( [
			'<strong>Chimehuín, Zone I (La Boca):</strong> from the source to 250 m downstream of the bridge. Preferential zone: additional permit required. <strong>Fly only</strong>, barbless hook, <strong>all fish must be released</strong>. Navigation and any other aquatic activity are prohibited. 2025-2026 closing: 31 May.',
			'<strong>Chimehuín, Zone II</strong> (down to the Collón Curá confluence): fly, spinning or spinning with a fly; all salmonids released, single barbless hook; limit 1 perch. 2025-2026 closing: 31 May.',
			'<strong>Malleo:</strong> from the Lanín National Park boundary to the route 23 bridge, fly only and all fish released. From the route 23 bridge to the Aluminé, fly or spinning with a fly, barbless hook, salmonids released and a limit of 1 perch. 2025-2026 closing: 31 May.',
			'<strong>Collón Curá:</strong> fly, spinning or spinning with a fly; salmonids released, single barbless hook; limit 2 perch. 2025-2026 closing: 31 May.',
			'<strong>Quilquihue:</strong> from the mouth to the end of La Laguna, fly or spinning with a fly (limit 2 perch, salmonids released); from there to the Chimehuín spinning is also allowed, single barbless hook. 2025-2026 closing: 31 May.',
			'<strong>Lake Lolog</strong> (shared with Lanín National Park): fly, spinning, trolling or spinning with a fly; limit 2 salmonids; perch released. From 2 May 2026, no trolling and everything released. 2025-2026 closing: 31 October.',
		] ),
		pm_smj_h( 'Inside Lanín National Park' ),
		pm_smj_p( '<strong>Lácar and Huechulafquen</strong> are inside Lanín National Park: they are in the National Parks annex, not the Neuquén one. General rules of that annex (2025-2026 edition):' ),
		pm_smj_lista( [
			'You may fish only in the waters the annex lists; <strong>any water not mentioned is excluded</strong>. What the list does not specify follows the general regulation.',
			'Keeping or possessing native species is prohibited, and so is transporting or releasing live fish.',
			'Carburetted two-stroke motors are banned on boats, even auxiliary and switched off; four-stroke or ultra-low-emission two-stroke motors are allowed (carry the engine manual).',
			'Motorised navigation on rivers and streams is banned, and so is floating (drift) rivers, except on the Arrayanes, Frey, Hua Hum, Limay, Manso, Rivadavia and Puelo.',
			'Shores may be reached only by authorised trails. Dogs and other pets are banned, and logs on the bank must not be moved, to protect the huillín (river otter): report sightings to a park ranger.',
			'A dropper and Euro-style fishing are allowed (up to two flies, one single barbless hook each). Felt-soled wading boots are banned.',
			'The "fishing day" is the daylight between morning and evening twilight published by the Naval Hydrography Service for the nearest city.',
		] ),
		pm_smj_p( 'What that annex\'s list says (2025-2026 edition) for the waters of the area:' ),
		pm_smj_lista( [
			'<strong>Lake Lácar:</strong> fishing allowed from 1 November 2025 to 31 May 2026 and during October 2026; limit 2 salmonids a day all season.',
			'<strong>Lake Huechulafquen:</strong> same dates; limit 2 salmonids; trolling is allowed except from the Blanco stream to the Chimehuín River and 500 m further to Quilantral. Flagged for didymo.',
			'<strong>Lake Paimún and Epulafquen:</strong> limit 2 salmonids a day. On the <strong>Paimún River</strong> only the middle and lower course may be fished, limit 1 salmonid.',
			'<strong>Malleo inside the park:</strong> navigation prohibited, fly only, everything released.',
			'<strong>Lake Lolog:</strong> limit 2 salmonids a day all season (shared with Neuquén).',
		] ),
		pm_smj_p( 'The regulation does not mention a park entrance fee or a separate permit to fish: confirm with the Lanín National Park office in San Martín de los Andes (Perito Moreno 1008, 2972-427233, lanin@apn.gob.ar).' ),
		pm_smj_h( 'General rules' ),
		pm_smj_lista( [
			'Fishing is daylight only, one rod per angler, with one artificial lure carrying a single hook.',
			'On rivers and streams, and within 200 metres of their source or mouth at a lake, all fish must be released using a single barbless hook (or one with the barb crushed), unless the water\'s entry says otherwise. In Neuquén salmonids may not be kept except where the list allows, and Atlantic salmon must always be released.',
			'A "dropper" (up to two flies, one single hook each) is allowed, except at La Boca of the Chimehuín and on the Correntoso.',
			'Motorised navigation is banned on all Neuquén rivers (except the Limay).',
			'Fishing from bridges and walkways is prohibited, and so are fires on river banks outside authorised sites.',
			'Felt-soled wading boots are banned, and so is lead in anchors, sinkers and lures.',
			'The regulation recommends not wading rivers and streams in November, to protect spawning beds.',
		] ),
		pm_smj_p( '<strong>Didymo:</strong> the regulation requires anglers, guides and outfitters to disinfect gear, boats and vehicles when the authority asks, and Neuquén reminds everyone that biosecurity measures against the algae are mandatory on all waters. Before moving from one river or lake to another, clean and dry your gear.' ),
		pm_smj_h( 'Guides and lodges' ),
		pm_smj_p( 'Most anglers fly fish these rivers with a guide: they know the river level, what is working that day and the access points. Neuquén\'s regulation prohibits hiring guides who are not licensed as tourism providers in the province: the list is at <a href="https://www.neuquentur.gob.ar/" rel="noopener">neuquentur.gob.ar</a>. We have not listed any guide or lodge yet because we have no source we could open. If you are a guide or run a lodge here, write to info@pescamapa.com.' ),
		pm_smj_p( 'The provincial wildlife directorate has offices in San Martín de los Andes (Mariano Moreno 465, 0297 241-2211) and Junín de los Andes (Roca 29, 0297 249 1277), according to the 2025-2026 regulation.' ),
		pm_smj_h( 'Getting there' ),
		pm_smj_p( 'San Martín de los Andes has Chapelco airport (CPC). Junín de los Andes is about 40 km away on national route 234. Access roads to many fishing spots are gravel: ask about their condition before you go. Fishing waters may only be entered through authorised access points.' ),
	] ),
	'campos' => [
		'h1'          => 'Fly fishing in San Martín and Junín de los Andes: season, permit and rules for the Chimehuín and Malleo',
		'bajada'      => 'Chimehuín, Malleo, Collón Curá, Quilquihue and Lakes Lácar and Lolog, in Neuquén. When the 2026-2027 season opens, how to buy the permit and what the regulation says for each river.',
		'modalidades' => "Fly fishing (wading and drift boat)\nSpinning with a fly, where allowed\nGuided day",
		'epoca'       => 'The Patagonian fishing season opens on 1 November; the main rivers closed on 31 May in 2026.',
		'seo_title'    => 'Fly fishing in San Martín and Junín de los Andes (2026/2027)',
		'seo_description' => 'Fishing guide to San Martín and Junín de los Andes, Neuquén: Chimehuín, Malleo, Collón Curá and Quilquihue rules, the 2026-2027 season (opens 1 November), the fishing permit and reference fees.',
		'temporada'   => [
			[ 'meses' => 'From 1 November', 'que' => 'The 2026-2027 season opens in Patagonia (according to the press). Each water has its own closing date.' ],
			[ 'meses' => 'Until May', 'que' => 'In 2026 the Chimehuín, Malleo, Collón Curá and Quilquihue closed on 31 May; the general season on 1 May (2025-2026 regulation). Confirm the 2027 dates.' ],
			[ 'meses' => 'Before every trip', 'que' => 'Confirm the method, limit and closures for that river or lake with the provincial wildlife directorate or your guide.' ],
		],
		'reglamento_resumen' => 'The Patagonian fishing season opens on 1 November 2026; 2026-2027 permits have been on sale online since 15 October on Caza y Pesca de Neuquén (checked 24/10/2026). Neuquén Annex, 2025-2026 edition (latest published): La Boca of the Chimehuín fly only and everything released; upper Malleo fly only and everything released; Chimehuín, Malleo, Collón Curá and Quilquihue closed on 31 May 2026; felt-soled boots banned. Reference permit fees 2025-2026: residents $10,000 a day, $20,000 for 10 days, $40,000 for the season; foreigners $50,000, $140,000 and $280,000. Lácar and Huechulafquen (Lanín National Park): National Parks annex rules apply (listed waters only, no carburetted two-stroke motors, no felt); Lácar and Huechulafquen were fished 1 Nov to 31 May and in October 2026, limit 2 salmonids; 2026-2027 dates not confirmed.',
		'aguas' => [
			[ 'nombre' => 'Chimehuín River', 'nota' => 'La Boca (Zone I): fly only, all fish released, additional permit (2025-2026 regulation). Zone II down to the Collón Curá.', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Malleo River', 'nota' => 'Upper stretch: fly only, all fish released (2025-2026 regulation).', 'lat' => '', 'lon' => '' ],
			[ 'nombre' => 'Lake Lácar', 'nota' => 'In front of San Martín de los Andes, inside Lanín National Park: 2 salmonids a day; fishing 1 Nov to 31 May and in October (2025-2026 regulation).', 'lat' => '', 'lon' => '' ],
		],
		'faqs' => [
			[ 'pregunta' => 'When does the 2026-2027 fishing season start in San Martín and Junín de los Andes?', 'respuesta' => 'According to regional press (checked 24/10/2026), the Patagonian fishing season opens on 1 November 2026. Each water has its own closing date: in the 2025-2026 edition of the regulation the Chimehuín, Malleo, Collón Curá and Quilquihue closed on 31 May and the general season on 1 May. The 2026-2027 dates were not published yet.' ],
			[ 'pregunta' => 'Where do I buy the Neuquén fishing permit?', 'respuesta' => 'Online, on the Caza y Pesca de Neuquén site (cazaypesca.neuquen.gob.ar/permisos). According to the press, sales for the 2026-2027 season opened on 15 October 2026.' ],
			[ 'pregunta' => 'How much is the fishing permit in Neuquén?', 'respuesta' => 'The Patagonian Continental Sport Fishing Regulation (2025-2026 edition, checked 24/10/2026) sets, for residents of Argentina: $10,000 a day, $20,000 for 10 days and $40,000 for the season; for foreign non-residents: $50,000 a day, $140,000 for 10 days and $280,000 for the season. Trolling and the preferential zone (La Boca of the Chimehuín) need an additional permit. The 2026-2027 fees were not shown yet: confirm them when you buy.' ],
			[ 'pregunta' => 'What are the rules at La Boca of the Chimehuín?', 'respuesta' => 'According to the 2025-2026 Neuquén Annex: fly fishing only, barbless hook and all fish released, from the source to 250 metres downstream of the bridge. It is a preferential zone: an additional permit is required on top of the ordinary one, and navigation and any other aquatic activity are prohibited. Confirm it has not changed for 2026-2027.' ],
			[ 'pregunta' => 'Can I spin fish on the Malleo?', 'respuesta' => 'On the upper stretch (to the route 23 bridge), no: fly only and all fish released. Further down, to the Aluminé, fly or spinning with a fly is allowed, with a barbless hook. That is what the 2025-2026 Neuquén Annex said.' ],
			[ 'pregunta' => 'What are the rules on Lakes Lácar and Huechulafquen?', 'respuesta' => 'They are inside Lanín National Park and follow the National Parks annex of the regulation. General rules (2025-2026 edition): fish only in the waters the annex lists, no carburetted two-stroke motors, no dogs or pets, no felt-soled boots. The 2025-2026 list: Lácar and Huechulafquen were fished from 1 November to 31 May and in October 2026, limit 2 salmonids a day; on Huechulafquen trolling was allowed except near the Chimehuín mouth. Confirm the 2026-2027 dates with your guide or the Lanín National Park office (San Martín de los Andes, 2972-427233).' ],
			[ 'pregunta' => 'Can I fish from a boat or drift boat on rivers inside Lanín National Park?', 'respuesta' => 'According to the 2025-2026 National Parks annex, floating the park\'s rivers and streams is prohibited, except on the Arrayanes, Frey, Hua Hum, Limay, Manso, Rivadavia and Puelo, and motorised navigation on rivers is banned. Confirm each stretch with your guide.' ],
			[ 'pregunta' => 'Do I need a licensed guide?', 'respuesta' => 'A guide is not mandatory, but if you hire one, Neuquén\'s regulation prohibits hiring guides who are not licensed as tourism providers in the province. The list is at neuquentur.gob.ar.' ],
			[ 'pregunta' => 'Do I have to disinfect my fishing gear (didymo)?', 'respuesta' => 'Yes: the regulation requires anglers, guides and outfitters to disinfect gear, boats and vehicles when the authority requires it, and bans felt-soled wading boots. Clean and dry your gear before moving from one river or lake to another.' ],
			[ 'pregunta' => 'How do I get to San Martín and Junín de los Andes?', 'respuesta' => 'San Martín de los Andes has Chapelco airport (CPC). Junín de los Andes is about 40 km away on national route 234. Access roads to many fishing spots are gravel.' ],
		],
	],
];

// --- Create ---------------------------------------------------------------------
foreach ( [ 'es', 'en' ] as $i ) {
	$g  = $guias[ $i ];
	$ya = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'san-martin-junin', 'numberposts' => -1 ] );
	$existe = array_filter( $ya, fn( $p ) => pm_idioma_de( $p->ID ) === $i );
	if ( $existe ) {
		WP_CLI::log( "san-martin-junin ($i) ya existe: no toco nada." );
		continue;
	}
	WP_CLI::log( "san-martin-junin ($i): " . ( $aplicar ? 'se crea' : 'se crearía' ) . " «{$g['titulo']}» /{$g['slug']}/ con " . count( $g['campos']['faqs'] ) . ' preguntas y ' . count( $fuentes_filas ) . ' fuentes' );
	if ( ! $aplicar ) {
		continue;
	}
	$id = wp_insert_post( [
		'post_type'    => 'pm_destino',
		'post_status'  => 'publish',
		'post_title'   => $g['titulo'],
		'post_name'    => $g['slug'],
		'post_content' => wp_slash( $g['cuerpo'] ),
	], true );
	if ( is_wp_error( $id ) ) {
		WP_CLI::error( $id->get_error_message() );
	}
	$campos = $g['campos'] + $comun + [ 'idioma' => $i ];
	if ( 'es' === $i ) {
		// Same pin as the "próximamente" card (Junín de los Andes); no trout species sheet exists yet.
		$campos += [ 'region' => 'patagonia', 'provincia' => 'neuquen', 'lat' => -39.94, 'lon' => -71.07, 'especies' => [] ];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $id );
	}
	update_post_meta( $id, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	if ( 'es' === $i ) {
		$proximo = get_page_by_path( 'san-martin-junin', OBJECT, 'pm_proximo' );
		if ( $proximo && get_post_thumbnail_id( $proximo->ID ) ) {
			set_post_thumbnail( $id, get_post_thumbnail_id( $proximo->ID ) );
			WP_CLI::log( 'foto: la de la tarjeta "próximamente"' );
		} else {
			WP_CLI::warning( 'sin foto: cargala desde el panel (Imagen destacada)' );
		}
	}
	WP_CLI::log( "✓ san-martin-junin ($i) publicada #$id: " . get_permalink( $id ) );
	WP_CLI::log( '  VERIFIED: ' . count( pm_filas( 'faqs', $id ) ) . ' preguntas y ' . count( pm_filas( 'fuentes', $id ) ) . ' fuentes leídas de vuelta' );
}

if ( $aplicar ) {
	flush_rewrite_rules( false );
}
WP_CLI::log( $aplicar ? 'APLICADO' : 'SIMULACRO: no se escribió nada' );
