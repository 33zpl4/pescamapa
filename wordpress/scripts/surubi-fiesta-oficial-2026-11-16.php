<?php
/**
 * Rewrites the festival note (clave fiesta-nacional-del-surubi-2027, es/en/pt) with the organiser's OFFICIAL site as the main source (16/11/2026). The first version (surubi-fiesta-2026-11-16) was built
 * from press only (Elonce 10/8 and 23/8/2026) because no official site had been found; the owner then gave https://www.goyasurubi.com/index/ and confirmed the banner: 28 April to 2 May 2027,
 * 50th edition, "$1.000.000.000 en premios, sorteos y trofeos", organised by the Comision Municipal de Pesca (Co.Mu.Pe.) with the Government of Corrientes and the Municipality of Goya, #MundialDePesca.
 * Read with investigar.yml (browser mode) on 16/11/2026: home page + banner (folder fiesta-surubi-oficial-20261116-20261116-1107 of the branch investigacion) and the 2027 rules, the registration,
 * system and cap notices and "Que es la Fiesta" (fiesta-surubi-oficial-2-20261116-20261116-1122). The prize grid is an image inside the registration notice, read by eye.
 * What changes vs the press version: dates, prize amount, cap, 1,413 registrations (4,239 anglers) and waiting list now come from the organiser; teams are of 2 OR 3 anglers (the press said "minimum two"; "about 4,200 anglers"
 * is the organiser's own dinner-capacity figure, not an Elonce estimate); the fishing ground has 14 zones (the 2025 magazine said 12); the contest is Sat 1 May 17:00 to Sun 2 May 08:00; registration fee, prize grid,
 * rules and scoring are added. The 2025 magazine and Elonce's 23/8 note leave the sources (superseded by the official pages). Elonce 10/8 stays only for "the highest figure in the event's history".
 * Also: the surubi species page FAQ answer on the dates cites the organiser instead of Elonce. NOT written, on purpose: names of people or teams, anything not on the site, USD conversions, 2026 official results (not read).
 * Idempotent: a note that already cites the rules URL is not touched; a note whose text is not the press version (edited by hand) is not touched either. Dry run first ($aplicar = false). Run with wp-script.yml (script = surubi-fiesta-oficial-2026-11-16).
 */
// --- Helpers shared by the seo-consultas-2026-11-13 scripts (copied in each file: wp-script.yml sends one file) ---
if ( ! function_exists( 'pm_sc_h2' ) ) {
	function pm_sc_h2( string $texto, int $nivel = 2 ): string {
		return "<!-- wp:heading" . ( 2 === $nivel ? '' : ' {"level":' . $nivel . '}' ) . " -->\n<h{$nivel} class=\"wp-block-heading\">{$texto}</h{$nivel}>\n<!-- /wp:heading -->\n\n";
	}
	function pm_sc_p( string $html ): string {
		return "<!-- wp:paragraph -->\n<p>{$html}</p>\n<!-- /wp:paragraph -->\n\n";
	}
	function pm_sc_lista( array $items ): string {
		$li = implode( '', array_map( fn( $i ) => "<!-- wp:list-item --><li>{$i}</li><!-- /wp:list-item -->", $items ) );
		return "<!-- wp:list -->\n<ul class=\"wp-block-list\">{$li}</ul>\n<!-- /wp:list -->\n\n";
	}
	/** FAQ as a group block with the class pm-faq: visible on the page and read by pm_faqs_contenido() for the FAQPage JSON-LD. */
	function pm_sc_faq( array $faqs ): string {
		$in = '';
		foreach ( $faqs as [ $q, $a ] ) {
			$in .= "<!-- wp:heading {\"level\":3} -->\n<h3 class=\"wp-block-heading\">{$q}</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph -->\n<p>{$a}</p>\n<!-- /wp:paragraph -->\n\n";
		}
		return "<!-- wp:group {\"className\":\"pm-faq\"} -->\n<div class=\"wp-block-group pm-faq\">" . rtrim( $in ) . "</div>\n<!-- /wp:group -->\n\n";
	}
	function pm_sc_a( string $url, string $texto ): string {
		return '<a href="' . esc_url( $url ) . '">' . $texto . '</a>';
	}
	/** Heading blocks with offset: [ [ offset, texto plano ], … ]. */
	function pm_sc_encabezados( string $c ): array {
		preg_match_all( '#<!-- wp:heading[^>]*-->\s*<h[1-6][^>]*>(.*?)</h[1-6]>\s*<!-- /wp:heading -->#su', $c, $m, PREG_OFFSET_CAPTURE );
		$out = [];
		foreach ( $m[0] as $n => $b ) {
			$out[] = [ $b[1], trim( wp_strip_all_tags( $m[1][ $n ][0] ) ), strlen( $b[0] ) ];
		}
		return $out;
	}
	/** Offset of the first heading whose text matches $regex, or null. */
	function pm_sc_pos_encabezado( string $c, string $regex ): ?int {
		foreach ( pm_sc_encabezados( $c ) as [ $pos, $texto ] ) {
			if ( preg_match( $regex, $texto ) ) {
				return $pos;
			}
		}
		return null;
	}
	/** Offset just after the first paragraph block whose plain text matches $regex, or null. */
	function pm_sc_pos_tras_parrafo( string $c, string $regex ): ?int {
		preg_match_all( '#<!-- wp:paragraph[^>]*-->\s*<p[^>]*>(.*?)</p>\s*<!-- /wp:paragraph -->\s*#su', $c, $m, PREG_OFFSET_CAPTURE );
		foreach ( $m[0] as $n => $b ) {
			if ( preg_match( $regex, wp_strip_all_tags( $m[1][ $n ][0] ) ) ) {
				return $b[1] + strlen( $b[0] );
			}
		}
		return null;
	}
	function pm_sc_insertar( string $c, int $pos, string $bloque ): string {
		return substr( $c, 0, $pos ) . $bloque . substr( $c, $pos );
	}
	/** Replace the text of one heading (exact plain text), keeping its markup; null when it is not there. */
	function pm_sc_renombrar( string $c, string $viejo, string $nuevo ): ?string {
		$n = 0;
		$r = preg_replace_callback( '#(<h[1-6][^>]*>)(.*?)(</h[1-6]>)#su', function ( $m ) use ( $viejo, $nuevo, &$n ) {
			if ( 0 === $n && trim( wp_strip_all_tags( $m[2] ) ) === $viejo ) {
				$n++;
				return $m[1] . $nuevo . $m[3];
			}
			return $m[0];
		}, $c );
		return $n ? $r : null;
	}
	/** Save post content and fail loudly when the read-back differs. */
	function pm_sc_guardar( int $id, string $nuevo, array $debe_contener, string $etiqueta ): void {
		$r = wp_update_post( [ 'ID' => $id, 'post_content' => wp_slash( $nuevo ) ], true );
		if ( is_wp_error( $r ) ) {
			WP_CLI::error( $etiqueta . ': ' . $r->get_error_message() );
		}
		$leido = get_post( $id )->post_content;
		foreach ( $debe_contener as $t ) {
			if ( false === strpos( $leido, $t ) ) {
				WP_CLI::error( "$etiqueta: la lectura de vuelta no contiene «{$t}»." );
			}
		}
	}
	function pm_sc_campo( int $id, string $campo, string $valor, string $etiqueta ): void {
		update_field( $campo, $valor, $id );
		if ( pm_campo( $campo, $id ) !== $valor ) {
			WP_CLI::error( "$etiqueta: el campo $campo no quedó como se esperaba." );
		}
	}
	function pm_sc_modo( bool $aplicar ): string {
		return $aplicar ? 'APLICA' : 'SIMULACRO';
	}
}




$aplicar = false;
$clave   = 'fiesta-nacional-del-surubi-2027';
$hoy     = '20261116';
$base    = 'https://www.goyasurubi.com/index/';

$u = [
	'of'   => $base,
	'reg'  => $base . 'index.php/concurso/reglamento-2027',
	'ins'  => $base . 'index.php/1562-edicion-de-oro-del-mundial-de-pesca-el-proximo-10-de-agosto-abren-las-inscripciones-de-equipos',
	'reh'  => $base . 'index.php/1563-50-fns-comupe-vuelve-a-habilitar-el-periodo-de-inscripciones',
	'com'  => $base . 'index.php/1564-comunicado-oficial-de-comupe-respecto-al-procedimiento-establecido-para-inscripciones-para-la-50-fns',
	'que'  => $base . 'index.php/nuestra-fiesta/ique-es-la-fn-del-surubi',
	'e1'   => 'https://www.elonce.com/sociedad/fiesta-nacional-del-surubi-habra-1-000-millones-en-premios-y-esperan-1400-equipos.htm',
	'c26'  => 'https://www.canal26.com/turismo/2026/04/15/confirman-fechas-y-artistas-que-tocaran-en-la-fiesta-nacional-del-surubi-estara-la-joaqui-kapanga-y-el-polaco/',
	'perf' => 'https://www.perfil.com/noticias/nea/fiesta-nacional-del-surubi-en-goya-los-ganadores-del-mundial-de-pesca-y-quien-saco-la-pieza-mayor.phtml',
	'disp' => 'https://boletinoficial.corrientes.gob.ar/assets/articulo_adjuntos/7523/original/26-12-2022.pdf',
	'emb'  => 'https://www.infoteca.cnptia.embrapa.br/infoteca/bitstream/doc/1039656/1/CNPASA2015dc21.pdf',
];
// Official pages (goyasurubi.com, the organiser's own site) and the 2025-2027 Corrientes rule are "oficial".
$oficiales = [ 'of', 'reg', 'ins', 'reh', 'com', 'que', 'disp' ];
// Read on 16/11/2026 with investigar.yml (browser mode): folders fiesta-surubi-oficial-20261116-20261116-1107 and fiesta-surubi-oficial-2-20261116-20261116-1122 of the branch investigacion.

$N = [];
$N['es'] = [
	'bajada' => 'La 50.ª edición, del 28 de abril al 2 de mayo de 2027 según el sitio oficial: cupo, inscripción, premios y reglamento del concurso. Con fuentes y fechas.',
	'seo_title' => 'Fiesta Nacional del Surubí 2027: fechas, cupo y premios',
	'seo_description' => 'La 50.ª Fiesta Nacional del Surubí es del 28 de abril al 2 de mayo de 2027 en Goya: cupo de 1.400 equipos, inscripción, premios y reglamento oficial.',
	'datos' => [
		'La 50.ª Fiesta Nacional del Surubí («Bodas de Oro») es del 28 de abril al 2 de mayo de 2027 en Goya, Corrientes; la organiza la Comisión Municipal de Pesca (Co.Mu.Pe.) con el Gobierno de Corrientes y el Municipio de Goya (sitio oficial, leído el 16/11/2026).',
		'El concurso de pesca es del sábado 1 de mayo (desde las 17:00) al domingo 2 de mayo de 2027 (hasta las 8:00) (reglamento 2027, art. 4).',
		'Cupo de 1.400 equipos: se completó en dos horas, con 1.413 registros (4.239 pescadores) y unos 300 equipos en lista de espera (Co.Mu.Pe., comunicado oficial).',
		'Inscripción: $1.350.000 en cuotas; al contado, de $1.100.000 (agosto de 2026) a $1.350.000 (abril de 2027) (Co.Mu.Pe., comunicado de apertura de inscripciones).',
		'Premios: «$1.000.000.000 en premios, sorteos y trofeos» (banner del sitio oficial); 1.º del concurso, $100.000.000; 1.º de pieza mayor, $50.000.000.',
		'Vale una pieza desde 50 cm, que se devuelve con vida; la cancha tiene 14 zonas (reglamento 2027; comunicado oficial).',
		'En 2026 (49.ª edición, del 27/4 al 3/5) la pieza mayor fue un surubí de 119 cm (Perfil, 4/5/2026).',
	],
	'p0' => '<strong>Respuesta corta.</strong> La 50.ª Fiesta Nacional del Surubí se hace del <strong>28 de abril al 2 de mayo de 2027</strong> en Goya (Corrientes). Según el {of|sitio oficial (leído el 16/11/2026)}, la organiza la Comisión Municipal de Pesca (Co.Mu.Pe.) con el Gobierno de Corrientes y el Municipio de Goya, y su banner anuncia «$1.000.000.000 en premios, sorteos y trofeos». El concurso de pesca es del sábado 1 de mayo (desde las 17:00) al domingo 2 de mayo (hasta las 8:00) ({reg|reglamento 2027}). El cupo de <strong>1.400 equipos</strong> se completó en dos horas, con 1.413 registros y unos 300 equipos en lista de espera ({com|comunicado oficial}).',
	'h1' => 'Fechas, inscripción y cupo de la edición 2027',
	'l1' => [
		'<strong>Fechas:</strong> del 28 de abril al 2 de mayo de 2027, en Goya, Corrientes: la 50.ª edición, que la Co.Mu.Pe. llama «Edición de Oro» y «Bodas de Oro» ({of|sitio oficial, 16/11/2026}; {com|comunicado}). La organizan la Co.Mu.Pe., el Gobierno de Corrientes y el Municipio de Goya.',
		'<strong>El concurso de pesca:</strong> del sábado 1 de mayo de 2027 desde las 17:00 hasta el domingo 2 a las 8:00. Las embarcaciones largan entre las 13:45 y las 15:00 según el grupo, y el sorteo de zonas es ese sábado a las 7:00. Si el clima impide la largada, el reglamento prevé demorarla o, en un caso extremo, suspender el concurso y sortear los premios materiales entre los equipos inscriptos ({reg|reglamento 2027, arts. 4, 10 y 12}).',
		'<strong>Inscripción:</strong> la apertura se anunció para el lunes 10 de agosto de 2026 a las 8:00 y el martes 11 de agosto a las 11:00 el sistema «se habilita nuevamente» ({ins|comunicado de apertura}; {reh|comunicado}). Es por la web oficial de inscripción: cada equipo es de dos o tres pescadores (al inscribirse hay que cargar al menos dos), con CUIT o CUIL de cada uno y los talles de la indumentaria oficial, y se paga al contado o en cuotas ({ins|comunicado}; {reg|arts. 6 y 7}).',
		'<strong>Valor de la inscripción (por equipo, según el art. 7):</strong> $1.350.000 en cuotas, que vencen el día 15 de cada mes; al contado, $1.100.000 si se pagaba en agosto, con aumentos mensuales hasta $1.350.000 en abril de 2027 ({ins|comunicado de la Co.Mu.Pe.}).',
		'<strong>Cupo:</strong> 1.400 equipos. La Co.Mu.Pe. lo explica por la capacidad de las zonas de pesca y del parque cerrado, la seguridad, la logística, la fiscalización y la capacidad del salón de la cena de entrega de premios: unos 4.200 pescadores, alrededor de 5.000 personas con fiscales, logística y autoridades ({com|comunicado oficial}). El reglamento le reserva la facultad de ampliar o reducir el cupo por razones excepcionales ({reg|art. 7}).',
		'<strong>Qué pasó con el cupo:</strong> se registraron 1.413 equipos (4.239 pescadores) y el cupo se completó en dos horas; unos 300 equipos no pudieron inscribirse y quedaron en lista de espera. Si la Co.Mu.Pe. decide ampliar el cupo, lo hará con un sorteo entre los de la lista, ante escribano público y transmitido en vivo. La cancha de pesca queda en 14 zonas ({com|comunicado oficial}).',
	],
	'h_pr' => 'Premios de la edición 2027',
	'p_pr' => 'La Co.Mu.Pe. presenta la grilla de premios como «sin precedentes», con una bolsa total «que alcanzará los 1.000 millones de pesos» ({ins|comunicado}); el banner del {of|sitio oficial} dice «$1.000.000.000 en premios, sorteos y trofeos». Elonce ({e1|10/8/2026}) la llamó la cifra más alta de la historia del evento. La grilla que publicó la Co.Mu.Pe. (una imagen dentro del comunicado de apertura de inscripciones, leída el 16/11/2026) dice, en pesos:',
	'l_pr' => [
		'<strong>Concurso (por puntaje), del 1.º al 20.º lugar:</strong> $100.000.000 el 1.º, $50.000.000 el 2.º, $25.000.000 el 3.º, $20.000.000 el 4.º y $18.000.000 el 5.º, bajando hasta $3.000.000 en el 19.º y el 20.º.',
		'<strong>Pieza mayor (por longitud), del 1.º al 15.º lugar:</strong> $50.000.000 el 1.º, $25.000.000 el 2.º y $15.000.000 el 3.º, bajando hasta $3.000.000 en el 14.º y el 15.º.',
		'<strong>Zonas, del 1.º al 5.º de cada zona:</strong> $5.000.000, $3.300.000, $2.100.000, $1.800.000 y $1.500.000.',
		'<strong>Además:</strong> dama mejor clasificada, menor mejor clasificado, trofeos y sorteos.',
	],
	'p_pr2' => 'No sumamos la grilla: el total anunciado incluye también sorteos y trofeos.',
	'h2' => 'Cómo es el concurso (reglamento 2027)',
	'l2' => [
		'Es pesca embarcada en el río Paraná, margen izquierdo y afluentes, dentro de la cancha que fija el Fiscal General; las zonas se sortean por grupo según la potencia del motor (grupos A a N y un grupo V para barcos) ({reg|art. 10}).',
		'Los equipos son de dos o tres pescadores; los menores de 18 van con un mayor y con autorización escrita ({reg|arts. 6 y 7}).',
		'Se pesca desde la embarcación anclada o amarrada dentro de la zona asignada, sin desembarcar ni mover la embarcación para cobrar una pieza. Cada pescador usa una sola caña con un solo anzuelo; para sacar las piezas se puede usar lazo o copo, y están prohibidos el bichero y el «bogagrip» ({reg|arts. 8 y 14}).',
		'Vale una pieza desde los 50 cm; los surubíes menores de 50 cm se devuelven enseguida. Cada pieza se mide con los elementos de la organización y se fotografía en el «pescadómetro» y al devolverla con vida; una pieza que no se pueda devolver con vida no vale, salvo causas ajenas al equipo constatadas por el fiscal ({reg|arts. 15 y 16}).',
		'Puntaje por la longitud de cada surubí: de 50 a menos de 60 cm, 1 punto por cada 10 cm de largo más la fracción; de 60 a menos de 80 cm, 5 puntos más lo mismo; de 80 a menos de 100 cm, 10 puntos más lo mismo; desde 100 cm, 15 puntos más lo mismo. Ejemplo del reglamento: una pieza de 125 cm suma 27,5 puntos ({reg|art. 19}).',
		'La Co.Mu.Pe. publica en goyasurubi.com el puntaje provisorio a las 13:00 del día en que termina el concurso (se puede impugnar hasta las 16:00) y el definitivo a las 17:00 ({reg|art. 19}).',
	],
	'p2' => 'En Goya cada pieza se devuelve con vida, y es coherente con la regla de Corrientes: el surubí es solo pesca y devolución ({disp|Disposición 853/22}).',
	'h3' => 'Lo que pasó en 2026',
	'p3' => 'La 49.ª edición fue del 27 de abril al 3 de mayo de 2026 y tuvo como epicentro el predio Costa Surubí, con más de 1.000 lanchas anunciadas ({c26|Canal 26, 15/4/2026}). Según {perf|Perfil (4/5/2026)}, participaron unas 1.400 embarcaciones; ganó por acumulación de puntos un equipo de Santa Fe, con 42,40 puntos y 3 piezas, y la pieza mayor fue un surubí de 119 cm, fiscalizado en la Zona 11. Los resultados oficiales no los leímos: el sitio de la Fiesta los enlaza a otro sitio.',
	'h4' => 'Si querés ir',
	'p4' => 'Para competir en 2027 hay que estar en un equipo inscripto: el cupo está completo, hay lista de espera y solo la Co.Mu.Pe. decide si lo amplía ({com|comunicado oficial}); las novedades están en el {of|sitio oficial}. Si querés ir a Goya sin competir, la descripción de la Fiesta en el {que|sitio oficial} dice que el programa incluye muestra comercial y artesanal, concurso de pesca de costa y de la palometa, competencias deportivas y festivales gratuitos de canto y música (es la descripción general, no el programa de 2027). Para pescar surubí en otras fechas, mirá {n:cuando-pescar-surubi|cuándo pescar el surubí}, la {g:goya|guía de Goya} y {esp:surubi|la ficha del surubí}. <strong>Contanos qué buscás y te armamos la mejor opción para vos:</strong> <a href="{viaje}">pedir opciones</a>, sin costo y con respuesta en 48 horas.',
	'h5' => 'Cómo se llama el pez',
	'p5' => 'En la Argentina se dice surubí. En Brasil, según {emb|Embrapa Pesca e Aquicultura (diciembre de 2015)}, las especies del género <em>Pseudoplatystoma</em> se conocen popularmente como «surubim» o «pintado», y el mismo nombre puede designar especies distintas según la región. Más en {esp:surubi|la ficha del surubí}.',
	'faq_h' => 'Preguntas frecuentes sobre la Fiesta Nacional del Surubí',
	'faq' => [
		[ '¿Cuándo es la Fiesta Nacional del Surubí 2027?', 'Del 28 de abril al 2 de mayo de 2027, en Goya, Corrientes: es la 50.ª edición, según la Co.Mu.Pe. (sitio oficial, leído el 16/11/2026). El concurso de pesca es del sábado 1 de mayo (desde las 17:00) al domingo 2 (hasta las 8:00), según el reglamento 2027.' ],
		[ '¿Cuántos equipos participan?', 'El tope es de 1.400 equipos. Se registraron 1.413 (4.239 pescadores) y el cupo se completó en dos horas; unos 300 equipos quedaron en lista de espera (Co.Mu.Pe., comunicado oficial). Cada equipo es de dos o tres pescadores.' ],
		[ '¿Cómo y cuánto cuesta inscribirse?', 'Por la web oficial de inscripción, por equipo, con al menos dos pescadores, al contado o en cuotas. El valor es de $1.350.000 en cuotas; al contado fue de $1.100.000 en agosto de 2026 y sube hasta $1.350.000 en abril de 2027 (Co.Mu.Pe.). El cupo de 2027 está completo y hay lista de espera: consultá a la Co.Mu.Pe. por una ampliación.' ],
		[ '¿Cuánto se paga de premio?', 'El banner del sitio oficial anuncia «$1.000.000.000 en premios, sorteos y trofeos». En la grilla de la Co.Mu.Pe., el 1.º del concurso cobra $100.000.000 y el 1.º de la pieza mayor, $50.000.000.' ],
		[ '¿Te quedás con el surubí que pescás en el concurso?', 'No: según el reglamento 2027 la pieza vale desde los 50 cm, se mide, se fotografía y se devuelve con vida, y en Corrientes el surubí es solo pesca y devolución (Disposición 853/22).' ],
		[ '¿Qué tamaño tuvo la pieza mayor de 2026?', 'Un surubí de 119 cm, fiscalizado en la Zona 11, según Perfil (4/5/2026).' ],
	],
	'f' => [
		'of'   => 'Fiesta Nacional del Surubí, sitio oficial (Co.Mu.Pe.): portada, banner y novedades',
		'reg'  => 'Fiesta Nacional del Surubí: Reglamento del 50.º Concurso Nacional del Surubí, año 2027',
		'ins'  => 'Co.Mu.Pe.: abren las inscripciones de equipos del 10 de agosto (condiciones, pago y premios)',
		'reh'  => 'Co.Mu.Pe.: vuelve a habilitarse el período de inscripciones (martes 11 de agosto, 11:00)',
		'com'  => 'Co.Mu.Pe.: comunicado oficial sobre el procedimiento de inscripción y el cupo de la 50.ª edición',
		'que'  => 'Fiesta Nacional del Surubí: ¿Qué es la Fiesta Nacional del Surubí? (sitio oficial)',
		'e1'   => 'Elonce (10/8/2026): Fiesta Nacional del Surubí, 1.000 millones en premios y tope de 1.400 equipos',
		'c26'  => 'Canal 26 (15/4/2026): fechas y programa de la 49.ª Fiesta Nacional del Surubí',
		'perf' => 'Perfil (4/5/2026): ganadores y pieza mayor de la 49.ª Fiesta Nacional del Surubí',
		'disp' => 'Boletín Oficial de Corrientes: Disposición 853/22 (tallas y cupos)',
		'emb'  => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (nombres populares)',
	],
	'orig' => 'Fiesta Nacional del Surubí 2027, reescrita el 16/11/2026 con el sitio oficial: hechos leídos con investigar.yml en goyasurubi.com (portada y banner, reglamento 2027, comunicados de apertura de inscripciones, de rehabilitación del sistema y de cupo, y «Qué es la Fiesta»; carpetas fiesta-surubi-oficial-20261116-20261116-1107 y fiesta-surubi-oficial-2-20261116-20261116-1122 de la rama investigacion). La grilla de premios es una imagen del comunicado, leída a ojo. La prensa (Elonce, Canal 26, Perfil) solo se usa para lo que el sitio oficial no dice. No se escriben nombres de personas ni equipos.',
];
$N['en'] = [
	'bajada' => 'The 50th edition, from 28 April to 2 May 2027 according to the official site: entry cap, registration, prizes and the contest rules. With sources and dates.',
	'seo_title' => 'National Surubí Festival 2027 in Goya: dates and entry cap',
	'seo_description' => 'The 50th National Surubí Festival runs 28 April to 2 May 2027 in Goya, Corrientes: 1,400-team cap, registration, prizes and the official contest rules.',
	'datos' => [
		'The 50th National Surubí Festival (the “Golden Anniversary”) runs from 28 April to 2 May 2027 in Goya, Corrientes; it is organised by the Municipal Fishing Commission (Co.Mu.Pe.) with the Corrientes government and the Goya municipality (official site, read on 16/11/2026).',
		'The fishing contest runs from Saturday 1 May (from 17:00) to Sunday 2 May 2027 (until 08:00) (2027 rules, art. 4).',
		'Cap of 1,400 teams: it filled in two hours, with 1,413 registrations (4,239 anglers) and about 300 teams on a waiting list (Co.Mu.Pe., official notice).',
		'Registration: ARS 1,350,000 in instalments; paying in full, from ARS 1,100,000 (August 2026) to ARS 1,350,000 (April 2027) (Co.Mu.Pe., registration-opening notice).',
		'Prizes: “$1.000.000.000 in prizes, raffles and trophies” (banner of the official site); 1st in the contest, ARS 100,000,000; 1st for biggest fish, ARS 50,000,000.',
		'A fish counts from 50 cm and is released alive; the fishing ground has 14 zones (2027 rules; official notice).',
		'In 2026 (49th edition, 27/4 to 3/5) the biggest fish was a 119 cm surubí (Perfil, 4/5/2026).',
	],
	'p0' => '<strong>Short answer.</strong> The 50th National Surubí Festival runs from <strong>28 April to 2 May 2027</strong> in Goya (Corrientes). According to the {of|official site (read on 16/11/2026)}, it is organised by the Municipal Fishing Commission (Co.Mu.Pe.) with the Corrientes government and the Goya municipality, and its banner announces “$1.000.000.000 in prizes, raffles and trophies” (Argentine pesos). The fishing contest runs from Saturday 1 May (from 17:00) to Sunday 2 May (until 08:00) ({reg|2027 rules}). The cap of <strong>1,400 teams</strong> filled in two hours, with 1,413 registrations and about 300 teams on a waiting list ({com|official notice}).',
	'h1' => 'Dates, registration and entry cap for 2027',
	'l1' => [
		'<strong>Dates:</strong> 28 April to 2 May 2027, in Goya, Corrientes: the 50th edition, which Co.Mu.Pe. calls the “Gold Edition” and the “Golden Anniversary” ({of|official site, 16/11/2026}; {com|notice}). It is organised by Co.Mu.Pe., the Corrientes government and the Goya municipality.',
		'<strong>The fishing contest:</strong> from Saturday 1 May 2027 at 17:00 to Sunday 2 May at 08:00. Boats set off between 13:45 and 15:00 depending on the group, and the zone draw is that Saturday at 07:00. If the weather prevents the start, the rules allow delaying it or, in an extreme case, cancelling the contest and drawing the material prizes among registered teams ({reg|2027 rules, arts. 4, 10 and 12}).',
		'<strong>Registration:</strong> opening was announced for Monday 10 August 2026 at 08:00, and on Tuesday 11 August at 11:00 the system was “enabled again” ({ins|opening notice}; {reh|notice}). It is through the official registration website: each team has two or three anglers (at least two must be entered), with each one’s tax ID (CUIT or CUIL) and sizes for the official clothing, and you pay in full or in instalments ({ins|notice}; {reg|arts. 6 and 7}).',
		'<strong>Registration fee (per team, according to art. 7):</strong> ARS 1,350,000 in instalments, due on the 15th of each month; paying in full, ARS 1,100,000 if paid in August, rising monthly to ARS 1,350,000 in April 2027 ({ins|Co.Mu.Pe. notice}).',
		'<strong>Cap:</strong> 1,400 teams. Co.Mu.Pe. explains it by the capacity of the fishing zones and the closed park, safety, logistics, officiating and the capacity of the hall for the prize dinner: about 4,200 anglers, around 5,000 people with officials, logistics and authorities ({com|official notice}). The rules reserve the right to raise or lower the cap for exceptional reasons ({reg|art. 7}).',
		'<strong>What happened with the cap:</strong> 1,413 teams registered (4,239 anglers) and the cap filled in two hours; about 300 teams could not register and went on a waiting list. If Co.Mu.Pe. decides to raise the cap, it will do so with a draw among those on the list, before a notary and streamed live. The fishing ground stays at 14 zones ({com|official notice}).',
	],
	'h_pr' => 'Prizes for 2027',
	'p_pr' => 'Co.Mu.Pe. presents the prize list as “unprecedented”, with a total purse “that will reach 1,000 million pesos” ({ins|notice}); the banner of the {of|official site} says “$1.000.000.000 in prizes, raffles and trophies”. Elonce ({e1|10/8/2026}) called it the highest figure in the event’s history. The list Co.Mu.Pe. published (an image inside the registration-opening notice, read on 16/11/2026) says, in Argentine pesos:',
	'l_pr' => [
		'<strong>Contest (by points), 1st to 20th place:</strong> ARS 100,000,000 for 1st, ARS 50,000,000 for 2nd, ARS 25,000,000 for 3rd, ARS 20,000,000 for 4th and ARS 18,000,000 for 5th, going down to ARS 3,000,000 for 19th and 20th.',
		'<strong>Biggest fish (by length), 1st to 15th place:</strong> ARS 50,000,000 for 1st, ARS 25,000,000 for 2nd and ARS 15,000,000 for 3rd, going down to ARS 3,000,000 for 14th and 15th.',
		'<strong>Zones, 1st to 5th in each zone:</strong> ARS 5,000,000, ARS 3,300,000, ARS 2,100,000, ARS 1,800,000 and ARS 1,500,000.',
		'<strong>Also:</strong> best-placed woman, best-placed minor, trophies and raffles.',
	],
	'p_pr2' => 'We do not add up the list: the announced total also includes raffles and trophies.',
	'h2' => 'How the contest works (2027 rules)',
	'l2' => [
		'It is boat fishing on the Paraná river, left bank and tributaries, inside the fishing ground set by the Chief Official; zones are drawn by group according to engine power (groups A to N and a group V for large boats) ({reg|art. 10}).',
		'Teams have two or three anglers; minors under 18 go with an adult and written authorisation ({reg|arts. 6 and 7}).',
		'You fish from the boat anchored or tied up inside the assigned zone, without going ashore or moving the boat to land a fish. Each angler uses one rod with a single hook; a landing loop or net may be used to take fish out, and gaffs and the “bogagrip” are banned ({reg|arts. 8 and 14}).',
		'A fish counts from 50 cm; surubí under 50 cm are released at once. Each fish is measured with the organisers’ equipment and photographed on the “pescadómetro” and when it is released alive; a fish that cannot be released alive does not count, unless the official confirms causes beyond the team’s control ({reg|arts. 15 and 16}).',
		'Points by length of each surubí: 50 to under 60 cm, 1 point per 10 cm of length plus the fraction; 60 to under 80 cm, 5 points plus the same; 80 to under 100 cm, 10 points plus the same; 100 cm and over, 15 points plus the same. The rules’ own example: a 125 cm fish scores 27.5 points ({reg|art. 19}).',
		'Co.Mu.Pe. publishes the provisional score on goyasurubi.com at 13:00 on the day the contest ends (it can be challenged until 16:00) and the final score at 17:00 ({reg|art. 19}).',
	],
	'p2' => 'In Goya every fish is released alive, which fits the Corrientes rule: surubí is catch and release only ({disp|Disposición 853/22}).',
	'h3' => 'What happened in 2026',
	'p3' => 'The 49th edition ran from 27 April to 3 May 2026 with the Costa Surubí grounds as its centre, with more than 1,000 boats announced ({c26|Canal 26, 15/4/2026}). According to {perf|Perfil (4/5/2026)}, about 1,400 boats took part; a team from Santa Fe won on accumulated points, with 42.40 points and 3 fish, and the biggest fish was a 119 cm surubí, officiated in Zone 11. We did not read the official results: the festival’s site links them to another site.',
	'h4' => 'If you want to go',
	'p4' => 'To compete in 2027 you must be on a registered team: the cap is full, there is a waiting list and only Co.Mu.Pe. decides whether to raise it ({com|official notice}); news is on the {of|official site}. If you want to go to Goya without competing, the festival’s description on the {que|official site} says the programme includes a commercial and craft show, a shore fishing contest and a palometa contest, sports competitions and free song and music festivals (that is the general description, not the 2027 programme). To fish surubí on other dates, see {n:cuando-pescar-surubi|when to fish surubí}, the {g:goya|Goya guide} and {esp:surubi|the surubí page}. <strong>Tell us what you are looking for and we will put together the best option for you:</strong> <a href="{viaje}">request options</a>, at no cost and with a reply within 48 hours.',
	'h5' => 'What the fish is called',
	'p5' => 'In Argentina it is surubí. In Brazil, according to {emb|Embrapa Pesca e Aquicultura (December 2015)}, the species of the genus <em>Pseudoplatystoma</em> are popularly known as “surubim” or “pintado”, and the same name can refer to different species depending on the region. More on {esp:surubi|the surubí page}.',
	'faq_h' => 'National Surubí Festival: frequently asked questions',
	'faq' => [
		[ 'When is the National Surubí Festival 2027?', 'From 28 April to 2 May 2027, in Goya, Corrientes: it is the 50th edition, according to Co.Mu.Pe. (official site, read on 16/11/2026). The fishing contest runs from Saturday 1 May (from 17:00) to Sunday 2 May (until 08:00), according to the 2027 rules.' ],
		[ 'How many teams take part?', 'The cap is 1,400 teams. 1,413 registered (4,239 anglers) and the cap filled in two hours; about 300 teams went on a waiting list (Co.Mu.Pe., official notice). Each team has two or three anglers.' ],
		[ 'How do I enter and how much does it cost?', 'Through the official registration website, by team, with at least two anglers, paying in full or in instalments. The fee is ARS 1,350,000 in instalments; paying in full it was ARS 1,100,000 in August 2026 and rises to ARS 1,350,000 in April 2027 (Co.Mu.Pe.). The 2027 cap is full and there is a waiting list: ask Co.Mu.Pe. about an increase.' ],
		[ 'How much is the prize money?', 'The official site’s banner announces “$1.000.000.000 in prizes, raffles and trophies”. In Co.Mu.Pe.’s list, 1st in the contest gets ARS 100,000,000 and 1st for the biggest fish ARS 50,000,000.' ],
		[ 'Do you keep the surubí you catch in the contest?', 'No: under the 2027 rules a fish counts from 50 cm, is measured, photographed and released alive, and in Corrientes surubí is catch and release only (Disposición 853/22).' ],
		[ 'How big was the biggest fish of 2026?', 'A 119 cm surubí, officiated in Zone 11, according to Perfil (4/5/2026).' ],
	],
	'f' => [
		'of'   => 'National Surubí Festival, official site (Co.Mu.Pe.): home page, banner and news',
		'reg'  => 'National Surubí Festival: Rules of the 50th National Surubí Contest, year 2027',
		'ins'  => 'Co.Mu.Pe.: team registration opens on 10 August (conditions, payment and prizes)',
		'reh'  => 'Co.Mu.Pe.: registration period enabled again (Tuesday 11 August, 11:00)',
		'com'  => 'Co.Mu.Pe.: official notice on the registration procedure and the cap for the 50th edition',
		'que'  => 'National Surubí Festival: What is the National Surubí Festival? (official site)',
		'e1'   => 'Elonce (10/8/2026): National Surubí Festival, 1,000 million pesos in prizes and a 1,400-team cap',
		'c26'  => 'Canal 26 (15/4/2026): dates and programme of the 49th National Surubí Festival',
		'perf' => 'Perfil (4/5/2026): winners and biggest fish of the 49th National Surubí Festival',
		'disp' => 'Boletín Oficial de Corrientes: Disposición 853/22 (sizes and bag limits)',
		'emb'  => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (popular names)',
	],
];
$N['pt'] = [
	'bajada' => 'A 50ª edição, de 28 de abril a 2 de maio de 2027 segundo o site oficial: limite de equipes, inscrição, prêmios e regulamento do concurso. Com fontes e datas.',
	'seo_title' => 'Festa Nacional do Surubim 2027 em Goya: datas e vagas',
	'seo_description' => 'A 50ª Festa Nacional do Surubi (surubim) será de 28 de abril a 2 de maio de 2027 em Goya: limite de 1.400 equipes, inscrição, prêmios e regulamento oficial.',
	'datos' => [
		'A 50ª Festa Nacional do Surubi («Bodas de Ouro») será de 28 de abril a 2 de maio de 2027 em Goya, Corrientes; é organizada pela Comissão Municipal de Pesca (Co.Mu.Pe.) com o Governo de Corrientes e o Município de Goya (site oficial, lido em 16/11/2026).',
		'O concurso de pesca vai do sábado 1º de maio (a partir das 17:00) ao domingo 2 de maio de 2027 (até as 8:00) (regulamento 2027, art. 4).',
		'Limite de 1.400 equipes: esgotou em duas horas, com 1.413 registros (4.239 pescadores) e cerca de 300 equipes em lista de espera (Co.Mu.Pe., comunicado oficial).',
		'Inscrição: ARS 1.350.000 parcelada; à vista, de ARS 1.100.000 (agosto de 2026) a ARS 1.350.000 (abril de 2027) (Co.Mu.Pe., comunicado de abertura das inscrições).',
		'Prêmios: «$1.000.000.000 em prêmios, sorteios e troféus» (banner do site oficial); 1º do concurso, ARS 100.000.000; 1º da peça maior, ARS 50.000.000.',
		'Vale a peça a partir de 50 cm, que é devolvida viva; a área de pesca tem 14 zonas (regulamento 2027; comunicado oficial).',
		'Em 2026 (49ª edição, de 27/4 a 3/5) a peça maior foi um surubi de 119 cm (Perfil, 4/5/2026).',
	],
	'p0' => '<strong>Resposta curta.</strong> A 50ª Festa Nacional do Surubi será de <strong>28 de abril a 2 de maio de 2027</strong> em Goya (Corrientes). Segundo o {of|site oficial (lido em 16/11/2026)}, é organizada pela Comissão Municipal de Pesca (Co.Mu.Pe.) com o Governo de Corrientes e o Município de Goya, e seu banner anuncia «$1.000.000.000 em prêmios, sorteios e troféus» (pesos argentinos). O concurso de pesca vai do sábado 1º de maio (a partir das 17:00) ao domingo 2 de maio (até as 8:00) ({reg|regulamento 2027}). O limite de <strong>1.400 equipes</strong> esgotou em duas horas, com 1.413 registros e cerca de 300 equipes em lista de espera ({com|comunicado oficial}).',
	'h1' => 'Datas, inscrição e limite da edição 2027',
	'l1' => [
		'<strong>Datas:</strong> de 28 de abril a 2 de maio de 2027, em Goya, Corrientes: a 50ª edição, que a Co.Mu.Pe. chama de «Edição de Ouro» e «Bodas de Ouro» ({of|site oficial, 16/11/2026}; {com|comunicado}). Organizam a Co.Mu.Pe., o Governo de Corrientes e o Município de Goya.',
		'<strong>O concurso de pesca:</strong> do sábado 1º de maio de 2027, a partir das 17:00, ao domingo 2 às 8:00. As embarcações largam entre 13:45 e 15:00 conforme o grupo, e o sorteio das zonas é nesse sábado às 7:00. Se o clima impedir a largada, o regulamento prevê atrasá-la ou, em caso extremo, suspender o concurso e sortear os prêmios materiais entre as equipes inscritas ({reg|regulamento 2027, arts. 4, 10 e 12}).',
		'<strong>Inscrição:</strong> a abertura foi anunciada para a segunda-feira 10 de agosto de 2026 às 8:00 e na terça-feira 11 de agosto às 11:00 o sistema foi «habilitado novamente» ({ins|comunicado de abertura}; {reh|comunicado}). É pelo site oficial de inscrição: cada equipe tem dois ou três pescadores (ao se inscrever é preciso cadastrar ao menos dois), com o CUIT ou CUIL de cada um e os tamanhos da roupa oficial, e se paga à vista ou parcelado ({ins|comunicado}; {reg|arts. 6 e 7}).',
		'<strong>Valor da inscrição (por equipe, segundo o art. 7):</strong> ARS 1.350.000 parcelado, com vencimento no dia 15 de cada mês; à vista, ARS 1.100.000 se pago em agosto, com aumentos mensais até ARS 1.350.000 em abril de 2027 ({ins|comunicado da Co.Mu.Pe.}).',
		'<strong>Limite:</strong> 1.400 equipes. A Co.Mu.Pe. o explica pela capacidade das zonas de pesca e do parque fechado, a segurança, a logística, a fiscalização e a capacidade do salão do jantar de entrega de prêmios: cerca de 4.200 pescadores, uns 5.000 com fiscais, logística e autoridades ({com|comunicado oficial}). O regulamento reserva a ela a faculdade de ampliar ou reduzir o limite por razões excepcionais ({reg|art. 7}).',
		'<strong>O que aconteceu com o limite:</strong> 1.413 equipes se registraram (4.239 pescadores) e as vagas se esgotaram em duas horas; cerca de 300 equipes não conseguiram se inscrever e ficaram em lista de espera. Se a Co.Mu.Pe. decidir ampliar o limite, fará um sorteio entre os da lista, diante de tabelião público e transmitido ao vivo. A área de pesca fica com 14 zonas ({com|comunicado oficial}).',
	],
	'h_pr' => 'Prêmios da edição 2027',
	'p_pr' => 'A Co.Mu.Pe. apresenta a lista de prêmios como «sem precedentes», com uma bolsa total «que chegará a 1.000 milhões de pesos» ({ins|comunicado}); o banner do {of|site oficial} diz «$1.000.000.000 em prêmios, sorteios e troféus». A Elonce ({e1|10/8/2026}) a chamou de o maior valor da história do evento. A lista que a Co.Mu.Pe. publicou (uma imagem dentro do comunicado de abertura das inscrições, lida em 16/11/2026) diz, em pesos argentinos:',
	'l_pr' => [
		'<strong>Concurso (por pontos), do 1º ao 20º lugar:</strong> ARS 100.000.000 o 1º, ARS 50.000.000 o 2º, ARS 25.000.000 o 3º, ARS 20.000.000 o 4º e ARS 18.000.000 o 5º, descendo até ARS 3.000.000 no 19º e no 20º.',
		'<strong>Peça maior (por comprimento), do 1º ao 15º lugar:</strong> ARS 50.000.000 o 1º, ARS 25.000.000 o 2º e ARS 15.000.000 o 3º, descendo até ARS 3.000.000 no 14º e no 15º.',
		'<strong>Zonas, do 1º ao 5º de cada zona:</strong> ARS 5.000.000, ARS 3.300.000, ARS 2.100.000, ARS 1.800.000 e ARS 1.500.000.',
		'<strong>Além disso:</strong> dama mais bem classificada, menor mais bem classificado, troféus e sorteios.',
	],
	'p_pr2' => 'Não somamos a lista: o total anunciado inclui também sorteios e troféus.',
	'h2' => 'Como é o concurso (regulamento 2027)',
	'l2' => [
		'É pesca embarcada no rio Paraná, margem esquerda e afluentes, dentro da área que o Fiscal Geral define; as zonas são sorteadas por grupo conforme a potência do motor (grupos A a N e um grupo V para barcos) ({reg|art. 10}).',
		'As equipes têm dois ou três pescadores; menores de 18 vão com um adulto e autorização escrita ({reg|arts. 6 e 7}).',
		'Pesca-se da embarcação ancorada ou amarrada dentro da zona designada, sem desembarcar nem mover a embarcação para capturar uma peça. Cada pescador usa uma só vara com um só anzol; para tirar as peças pode-se usar laço ou puçá, e são proibidos o bichero (gancho) e o «bogagrip» ({reg|arts. 8 e 14}).',
		'Vale a peça a partir de 50 cm; os surubins menores de 50 cm são devolvidos de imediato. Cada peça é medida com os equipamentos da organização e fotografada no «pescadómetro» e ao ser devolvida viva; uma peça que não possa ser devolvida viva não vale, salvo causas alheias à equipe constatadas pelo fiscal ({reg|arts. 15 e 16}).',
		'Pontuação pelo comprimento de cada surubi: de 50 a menos de 60 cm, 1 ponto por cada 10 cm de comprimento mais a fração; de 60 a menos de 80 cm, 5 pontos mais o mesmo; de 80 a menos de 100 cm, 10 pontos mais o mesmo; a partir de 100 cm, 15 pontos mais o mesmo. Exemplo do regulamento: uma peça de 125 cm soma 27,5 pontos ({reg|art. 19}).',
		'A Co.Mu.Pe. publica em goyasurubi.com a pontuação provisória às 13:00 do dia em que o concurso termina (pode ser contestada até as 16:00) e a definitiva às 17:00 ({reg|art. 19}).',
	],
	'p2' => 'Em Goya cada peça é devolvida viva, o que é coerente com a regra de Corrientes: o surubim é só pesca e soltura ({disp|Disposición 853/22}).',
	'h3' => 'O que aconteceu em 2026',
	'p3' => 'A 49ª edição foi de 27 de abril a 3 de maio de 2026, com o parque Costa Surubí como epicentro e mais de 1.000 lanchas anunciadas ({c26|Canal 26, 15/4/2026}). Segundo a {perf|Perfil (4/5/2026)}, participaram cerca de 1.400 embarcações; venceu por acúmulo de pontos uma equipe de Santa Fe, com 42,40 pontos e 3 peças, e o maior peixe foi um surubi de 119 cm, fiscalizado na Zona 11. Não lemos os resultados oficiais: o site da Festa os liga a outro site.',
	'h4' => 'Se você quer ir',
	'p4' => 'Para competir em 2027 é preciso estar em uma equipe inscrita: o limite está completo, há lista de espera e só a Co.Mu.Pe. decide se o amplia ({com|comunicado oficial}); as novidades estão no {of|site oficial}. Se quer ir a Goya sem competir, a descrição da Festa no {que|site oficial} diz que a programação inclui mostra comercial e artesanal, concurso de pesca de costa e de palometa, competições esportivas e festivais gratuitos de canto e música (é a descrição geral, não a programação de 2027). Para pescar surubim em outras datas, veja {n:cuando-pescar-surubi|quando pescar o surubim}, o {g:goya|guia de Goya} e {esp:surubi|a ficha do surubim}. <strong>Conte o que você procura e montamos a melhor opção para você:</strong> <a href="{viaje}">pedir opções</a>, sem custo e com resposta em 48 horas.',
	'h5' => 'Como se chama o peixe',
	'p5' => 'Na Argentina se diz surubí. No Brasil, segundo a {emb|Embrapa Pesca e Aquicultura (dezembro de 2015)}, as espécies do gênero <em>Pseudoplatystoma</em> são conhecidas popularmente como «surubim» ou «pintado», e o mesmo nome pode designar espécies diferentes conforme a região. Mais em {esp:surubi|a ficha do surubim}.',
	'faq_h' => 'Perguntas frequentes sobre a Festa Nacional do Surubi',
	'faq' => [
		[ 'Quando é a Festa Nacional do Surubi 2027?', 'De 28 de abril a 2 de maio de 2027, em Goya, Corrientes: é a 50ª edição, segundo a Co.Mu.Pe. (site oficial, lido em 16/11/2026). O concurso de pesca vai do sábado 1º de maio (a partir das 17:00) ao domingo 2 (até as 8:00), segundo o regulamento 2027.' ],
		[ 'Quantas equipes participam?', 'O limite é de 1.400 equipes. Registraram-se 1.413 (4.239 pescadores) e as vagas se esgotaram em duas horas; cerca de 300 equipes ficaram em lista de espera (Co.Mu.Pe., comunicado oficial). Cada equipe tem dois ou três pescadores.' ],
		[ 'Como me inscrevo e quanto custa?', 'Pelo site oficial de inscrição, por equipe, com ao menos dois pescadores, à vista ou parcelado. O valor é de ARS 1.350.000 parcelado; à vista foi de ARS 1.100.000 em agosto de 2026 e sobe até ARS 1.350.000 em abril de 2027 (Co.Mu.Pe.). O limite de 2027 está completo e há lista de espera: pergunte à Co.Mu.Pe. sobre uma ampliação.' ],
		[ 'Quanto se paga de prêmio?', 'O banner do site oficial anuncia «$1.000.000.000 em prêmios, sorteios e troféus». Na lista da Co.Mu.Pe., o 1º do concurso recebe ARS 100.000.000 e o 1º da peça maior, ARS 50.000.000.' ],
		[ 'Fica-se com o surubim pescado no concurso?', 'Não: pelo regulamento 2027 a peça vale a partir de 50 cm, é medida, fotografada e devolvida viva, e em Corrientes o surubim é só pesca e soltura (Disposición 853/22).' ],
		[ 'Qual foi o tamanho do maior peixe de 2026?', 'Um surubi de 119 cm, fiscalizado na Zona 11, segundo a Perfil (4/5/2026).' ],
	],
	'f' => [
		'of'   => 'Festa Nacional do Surubi, site oficial (Co.Mu.Pe.): página inicial, banner e novidades',
		'reg'  => 'Festa Nacional do Surubi: Regulamento do 50º Concurso Nacional do Surubi, ano 2027',
		'ins'  => 'Co.Mu.Pe.: abrem as inscrições de equipes em 10 de agosto (condições, pagamento e prêmios)',
		'reh'  => 'Co.Mu.Pe.: o período de inscrições é habilitado de novo (terça-feira 11 de agosto, 11:00)',
		'com'  => 'Co.Mu.Pe.: comunicado oficial sobre o procedimento de inscrição e o limite da 50ª edição',
		'que'  => 'Festa Nacional do Surubi: O que é a Festa Nacional do Surubi? (site oficial)',
		'e1'   => 'Elonce (10/8/2026): Festa Nacional do Surubi, 1.000 milhões em prêmios e limite de 1.400 equipes',
		'c26'  => 'Canal 26 (15/4/2026): datas e programação da 49ª Festa Nacional do Surubi',
		'perf' => 'Perfil (4/5/2026): vencedores e maior peixe da 49ª Festa Nacional do Surubi',
		'disp' => 'Boletín Oficial de Corrientes: Disposición 853/22 (tamanhos e cotas)',
		'emb'  => 'Embrapa Pesca e Aquicultura (2015): A pesca e a aquicultura de surubins no Brasil (nomes populares)',
	],
];

// The surubí species page cited Elonce for the dates in its FAQ; the organiser's own site now says it.
$FICHA = [
	'es' => [ 'viejo' => 'según Elonce (10/8/2026). Detalle:', 'nuevo' => 'según la Co.Mu.Pe., organizadora (sitio oficial, leído el 16/11/2026). Detalle:', 'fuente' => 'Fiesta Nacional del Surubí, sitio oficial (Co.Mu.Pe.): portada, banner y novedades' ],
	'en' => [ 'viejo' => 'according to Elonce (10/8/2026). Details:', 'nuevo' => 'according to Co.Mu.Pe., the organiser (official site, read on 16/11/2026). Details:', 'fuente' => 'National Surubí Festival, official site (Co.Mu.Pe.): home page, banner and news' ],
	'pt' => [ 'viejo' => 'segundo a Elonce (10/8/2026). Detalhe:', 'nuevo' => 'segundo a Co.Mu.Pe., organizadora (site oficial, lido em 16/11/2026). Detalhe:', 'fuente' => 'Festa Nacional do Surubi, site oficial (Co.Mu.Pe.): página inicial, banner e novidades' ],
];

/** {key|text} -> external link; {g:|n:|esp:clave|text} -> internal; {viaje}. */
$res = function ( string $html, string $i ) use ( $u ): string {
	$html = preg_replace_callback( '/\{(?:(g|n|esp):)?([a-z0-9-]+)\|([^}]+)\}/u', function ( $m ) use ( $i, $u ) {
		[ , $tipo, $c, $txt ] = $m;
		if ( '' === $tipo ) {
			isset( $u[ $c ] ) || WP_CLI::error( "URL externa desconocida: $c" );
			return '<a href="' . esc_url( $u[ $c ] ) . '" rel="noopener">' . $txt . '</a>';
		}
		$cpt = [ 'g' => 'pm_destino', 'n' => 'pm_nota', 'esp' => 'pm_especie' ][ $tipo ];
		$p   = pm_buscar( $cpt, $c, $i ) ?? pm_buscar( $cpt, $c, 'es' );
		return $p ? '<a href="' . esc_url( get_permalink( $p->ID ) ) . '">' . $txt . '</a>' : $txt;
	}, $html );
	return str_replace( '{viaje}', esc_url( pm_url_viaje( $i ) . '?destino=goya&especie=surubi' ), $html );
};

// ---------------- Part 1: the three notes ----------------
$cambios = 0;
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$n    = $N[ $i ];
	$nota = array_values( array_filter( get_posts( [ 'post_type' => 'pm_nota', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => $clave, 'numberposts' => -1 ] ), fn( $p ) => pm_idioma_de( $p->ID ) === $i ) )[0] ?? null;
	if ( ! $nota ) {
		WP_CLI::warning( "$clave ($i): la nota no existe; no hay nada que reescribir." );
		continue;
	}
	$c = $nota->post_content;
	if ( false !== strpos( $c, $u['reg'] ) ) {
		WP_CLI::log( "$clave ($i) #{$nota->ID} ya cita el sitio oficial: no toco nada." );
		continue;
	}
	if ( false === strpos( $c, $u['e1'] ) ) {
		WP_CLI::warning( "$clave ($i) #{$nota->ID}: no reconozco la versión de prensa (alguien la editó); no toco nada." );
		continue;
	}
	$tl = mb_strlen( $n['seo_title'] );
	$dl = mb_strlen( $n['seo_description'] );
	( $tl <= 60 && $dl >= 70 && $dl <= 160 ) || WP_CLI::error( "$i: SEO fuera de rango: title $tl / description $dl" );
	$cuerpo = pm_sc_p( $res( $n['p0'], $i ) )
		. pm_sc_h2( $n['h1'] ) . pm_sc_lista( array_map( fn( $x ) => $res( $x, $i ), $n['l1'] ) )
		. pm_sc_h2( $n['h_pr'] ) . pm_sc_p( $res( $n['p_pr'], $i ) ) . pm_sc_lista( $n['l_pr'] ) . pm_sc_p( $n['p_pr2'] )
		. pm_sc_h2( $n['h2'] ) . pm_sc_lista( array_map( fn( $x ) => $res( $x, $i ), $n['l2'] ) ) . pm_sc_p( $res( $n['p2'], $i ) )
		. pm_sc_h2( $n['h3'] ) . pm_sc_p( $res( $n['p3'], $i ) )
		. pm_sc_h2( $n['h4'] ) . pm_sc_p( $res( $n['p4'], $i ) )
		. pm_sc_h2( $n['h5'] ) . pm_sc_p( $res( $n['p5'], $i ) )
		. pm_sc_h2( $n['faq_h'] ) . pm_sc_faq( $n['faq'] );
	! preg_match( '/\{[a-z0-9:|-]+/', $cuerpo, $m ) || WP_CLI::error( "$i: marcador sin resolver {$m[0]}" );
	// Every link in the text must be in the sources table, and every source must be cited.
	foreach ( $n['f'] as $k => $nombre ) {
		false !== strpos( $cuerpo, esc_url( $u[ $k ] ) ) || WP_CLI::error( "$i: la fuente «$k» está en la tabla pero no se cita en el texto." );
	}
	$fuentes = [];
	foreach ( $n['f'] as $k => $nombre ) {
		$fuentes[] = [ 'nombre' => $nombre, 'url' => $u[ $k ], 'oficial' => in_array( $k, $oficiales, true ) ? 1 : 0, 'consultada' => $hoy ];
	}
	$antes = [ count( pm_faqs_contenido( $c ) ), count( pm_filas( 'fuentes', $nota->ID ) ), str_word_count( wp_strip_all_tags( $c ) ) ];
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": $clave ($i) #{$nota->ID} /{$nota->post_name}/ — FAQ {$antes[0]} → " . count( $n['faq'] ) . ", fuentes {$antes[1]} → " . count( $fuentes ) . ", palabras {$antes[2]} → " . str_word_count( wp_strip_all_tags( $cuerpo ) ) . ', ' . count( $n['datos'] ) . " datos; title $tl / description $dl" );
	foreach ( $fuentes as $f ) {
		WP_CLI::log( '   fuente' . ( $f['oficial'] ? ' (oficial)' : '' ) . ': ' . $f['nombre'] );
	}
	$cambios++;
	if ( ! $aplicar ) {
		WP_CLI::log( "   --- texto ($i) ---\n" . trim( preg_replace( '/\n{2,}/', "\n", wp_strip_all_tags( preg_replace( '#</(p|li|h[1-6])>#', "\n", $cuerpo ) ) ) ) );
		continue;
	}
	$slug = $nota->post_name;
	$r    = wp_update_post( [ 'ID' => $nota->ID, 'post_content' => wp_slash( $cuerpo ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	$campos = [ 'bajada' => $n['bajada'], 'nota_datos' => array_map( fn( $d ) => [ 'dato' => $d ], $n['datos'] ), 'fuentes' => $fuentes, 'seo_title' => $n['seo_title'], 'seo_description' => $n['seo_description'], 'actualizado' => $hoy ];
	if ( 'es' === $i ) {
		$campos['nota_origen'] = $N['es']['orig'];
	}
	foreach ( $campos as $k => $v ) {
		update_field( $k, $v, $nota->ID );
	}
	update_post_meta( $nota->ID, 'es' === $i ? '_pm_es_editado' : '_pm_traducido', time() + ( 'es' === $i ? 0 : 1 ) );
	$g    = get_post( $nota->ID );
	$urls = array_column( pm_filas( 'fuentes', $nota->ID ), 'url' );
	$ok   = $g->post_name === $slug && false !== strpos( $g->post_content, $u['reg'] ) && false !== strpos( $g->post_content, 'especie=surubi' ) && false !== strpos( $g->post_content, '119' )
		&& false === strpos( $g->post_content, 'elonce.com/sociedad/fiesta-del-surubi-agotaron' )
		&& count( pm_faqs_contenido( $g->post_content ) ) === count( $n['faq'] ) && count( $urls ) === count( $fuentes ) && in_array( $u['reg'], $urls, true )
		&& count( pm_filas( 'nota_datos', $nota->ID ) ) === count( $n['datos'] ) && pm_campo( 'bajada', $nota->ID ) === $n['bajada'] && pm_campo( 'seo_title', $nota->ID ) === $n['seo_title'];
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": $clave ($i) #{$nota->ID}, FAQ " . count( pm_faqs_contenido( $g->post_content ) ) . ', ' . count( $urls ) . ' fuentes ' . get_permalink( $nota->ID ) );
	$ok || WP_CLI::error( "$clave ($i): la lectura de vuelta no coincide." );
}

// ---------------- Part 2: the surubí species page (FAQ answer + source row) ----------------
foreach ( [ 'es', 'en', 'pt' ] as $i ) {
	$p = pm_buscar( 'pm_especie', 'surubi', $i );
	if ( ! $p ) {
		WP_CLI::warning( "ficha surubí ($i): no existe." );
		continue;
	}
	$f = $FICHA[ $i ];
	if ( false === strpos( $p->post_content, $f['viejo'] ) ) {
		WP_CLI::log( "ficha surubí ($i) #{$p->ID}: la respuesta de la Fiesta ya no cita a Elonce (o no está); no toco nada." );
		continue;
	}
	$nuevo = str_replace( $f['viejo'], $f['nuevo'], $p->post_content );
	$filas = pm_filas( 'fuentes', $p->ID );
	$hubo  = false;
	foreach ( $filas as $k => $fila ) {
		if ( ( $fila['url'] ?? '' ) === $u['e1'] ) {
			$filas[ $k ] = [ 'nombre' => $f['fuente'], 'url' => $u['of'], 'oficial' => 1, 'consultada' => $hoy ];
			$hubo = true;
		}
	}
	$hubo || $filas[] = [ 'nombre' => $f['fuente'], 'url' => $u['of'], 'oficial' => 1, 'consultada' => $hoy ];
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": ficha surubí ($i) #{$p->ID}: la respuesta de la Fiesta cita a la Co.Mu.Pe.; fuente " . ( $hubo ? 'de Elonce reemplazada' : 'agregada' ) . ' por el sitio oficial' );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	$slug = $p->post_name;
	$r    = wp_update_post( [ 'ID' => $p->ID, 'post_content' => wp_slash( $nuevo ) ], true );
	is_wp_error( $r ) && WP_CLI::error( $r->get_error_message() );
	update_field( 'fuentes', $filas, $p->ID );
	$g  = get_post( $p->ID );
	$ok = $g->post_name === $slug && false !== strpos( $g->post_content, $f['nuevo'] ) && false === strpos( $g->post_content, $f['viejo'] ) && in_array( $u['of'], array_column( pm_filas( 'fuentes', $p->ID ), 'url' ), true );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": ficha surubí ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "ficha surubí ($i): la lectura de vuelta no coincide." );
}
$aplicar && flush_rewrite_rules( false );
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
