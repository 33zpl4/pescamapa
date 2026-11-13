<?php
/**
 * Santa Cruz general regulation, 13/11/2026: Disposición N° 241/2026 of the Secretaría de Estado de Pesca y Acuicultura (signed 22/09/2026,
 * in force from 1/10/2026), articles 1-18, read in the signed PDF (scanned, read by eye) and on https://pesca.santacruz.gob.ar/reglamento/.
 *  - Río Gallegos (es/en/pt): new section with art. 18 (disinfection, felt soles), 8 and 11 (lure and hook, barbs), 10 (one rod), 15 r (no lead),
 *    15 t (no night fishing), 11-12 (mandatory release); four FAQs; the "we did not read the general regulation" sentences (summary, fly-only
 *    FAQ, hidden pending line) are replaced by what is read and what is still unread (Annex I beyond the entries the guide cites).
 *  - Jurassic Lake (es/en/pt): hook "single or double" (art. 8, body and FAQ), disinfection sentence, two FAQs.
 *  - El Calafate (es/en/pt): the art. 18 sentence after the paragraph that opens the rules.
 * NOT written: that guides must be registered (arts. 1-18 do not say it). Idempotent. Simulacro first ($aplicar = false).
 * Run with wp-script.yml (script = santa-cruz-reglamento-general-2026-11-13).
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


$aplicar = true;
$hoy     = '20261113';
$cambios = 0;

$u_pdf = 'https://pesca.santacruz.gob.ar/static/docs/reglamento-pesca-2026-2027.pdf';
$u_web = 'https://pesca.santacruz.gob.ar/reglamento/';

/** Replace $viejo by $nuevo in a text field; returns the new value or null when $viejo is not there. */
function pm_scr_cambiar( string $texto, string $viejo, string $nuevo ): ?string {
	return false === strpos( $texto, $viejo ) ? null : str_replace( $viejo, $nuevo, $texto );
}

// --- Texts (each fact: Disposición N° 241/2026, general regulation arts. 1-18, read 13/11/2026) --------------------------
$L = [
	'es' => [
		'disp' => 'Disposición N° 241/2026',
		'fuente_pdf' => 'Disposición N° 241/2026, Secretaría de Estado de Pesca y Acuicultura de Santa Cruz: Reglamento General de Pesca Deportiva Continental, temporada 2026/2027 (firmada el 22/9/2026, rige desde el 1/10/2026; texto firmado leído el 13/11/2026)',
		'fuente_web' => 'Gobierno de Santa Cruz, Pesca y Acuicultura: Reglamento de Pesca, temporada 2026/2027 (artículos 1 a 18 y protocolo Didymo; leído el 13/11/2026)',
		'h2' => 'El reglamento general de Santa Cruz, 2026/27',
		'rx_pos' => '/área de exclusión/u',
		'intro' => 'El anexo de ambientes se lee junto con el <strong>Reglamento General</strong>, que aprueba la <strong>Disposición N° 241/2026</strong> de la Secretaría de Estado de Pesca y Acuicultura de Santa Cruz (firmada el 22/9/2026, rige desde el 1/10/2026). Leímos sus artículos 1 a 18 el 13/11/2026, en el texto firmado y en el portal oficial. Lo que importa para quien viene a pescar:',
		'items' => [
			'<strong>Desinfección del equipo (art. 18):</strong> todo pescador que entra a la provincia debe usar equipos nuevos o previamente desinfectados. Pescadores, guías de pesca continental y prestadores turísticos deben desinfectar waders, botas, equipos de pesca, flotadores y vehículos, y el cambio de cuenca exige una sanitización inmediata. El portal indica el procedimiento: retirar restos de vegetación, barro, algas y sedimentos; remojar con lavandina o agua caliente (más de 60 °C); secar al sol y esperar 48 horas antes de reutilizar.',
			'<strong>Suelas de fieltro (art. 18):</strong> no están prohibidas; su uso queda condicionado al cumplimiento riguroso de esas pautas de desinfección.',
			'<strong>Señuelo y anzuelo (art. 8):</strong> un solo señuelo artificial con un único anzuelo, simple o doble, con o sin rebaba; si el señuelo trae más anzuelos, hay que quitarlos o inutilizarlos. Donde la devolución es obligatoria, el anzuelo es simple o doble con micro rebaba o rebaba aplastada (art. 11).',
			'<strong>Un solo equipo (art. 10):</strong> se usa un solo equipo de pesca a la vez; se pueden llevar equipos de repuesto, pero no desplegarlos juntos.',
			'<strong>Sin plomo (art. 15 r):</strong> están prohibidos los lastres, contrapesos, plomadas de fondo, líneas plomadas y los señuelos con plomo en la cabeza o el núcleo.',
			'<strong>Sin pesca nocturna (art. 15 t):</strong> prohibida en toda la provincia; el día de pesca va de la salida a la puesta del sol (art. 6).',
			'<strong>Devolución obligatoria (art. 11):</strong> en todos los ríos y arroyos, y a 200 m sobre ambas costas desde sus nacientes o desembocaduras lacustres, se libera de inmediato y sin daño la totalidad de los ejemplares, salvo las excepciones del Anexo I (en el río Gallegos, la ventana de febrero de arriba). Donde la devolución es obligatoria no se puede tener ni transportar ningún ejemplar (art. 12).',
		],
		'cierre' => '<strong>Lo que todavía no leímos:</strong> del Anexo I, solo las fichas que cita esta guía; el propio anexo dice que lista algunos de los ambientes más destacados. Las reglas cambian cada temporada: confirmalas con tu guía o con la autoridad antes de salir.',
		'faqs' => [
			[ '¿Tengo que desinfectar mi equipo para pescar en Santa Cruz?', 'Sí. El artículo 18 del Reglamento General (Disposición N° 241/2026) obliga a todo pescador que entra a la provincia a usar equipos nuevos o previamente desinfectados, y a pescadores, guías y prestadores turísticos a desinfectar waders, botas, equipos, flotadores y vehículos; al cambiar de cuenca hay que sanitizar de inmediato. El portal oficial indica: retirar vegetación, barro, algas y sedimentos; remojar con lavandina o agua caliente (más de 60 °C); secar al sol y esperar 48 horas antes de reutilizar.' ],
			[ '¿Se pueden usar botas con suela de fieltro en Santa Cruz?', 'No están prohibidas, pero su uso queda condicionado al cumplimiento riguroso de las pautas de desinfección del artículo 18 (Disposición N° 241/2026), sobre todo al cambiar de cuenca. Preguntale a tu lodge o a tu guía qué rutina de desinfección siguen.' ],
			[ '¿Qué señuelos y anzuelos se pueden usar en Santa Cruz?', 'Un solo señuelo artificial con un único anzuelo, simple o doble, con o sin rebaba (art. 8); donde la devolución es obligatoria, con micro rebaba o rebaba aplastada (art. 11). Se usa un solo equipo a la vez (art. 10). Están prohibidos los lastres y los señuelos con plomo en la cabeza o el núcleo (art. 15 r) y la pesca nocturna en toda la provincia (art. 15 t). El Anexo I puede traer excepciones por ambiente. Fuente: Disposición N° 241/2026.' ],
			[ '¿La devolución es obligatoria en todos los ríos de Santa Cruz?', 'La regla general del artículo 11 es la liberación inmediata y sin daño de todos los ejemplares en ríos y arroyos y a 200 m de sus nacientes o desembocaduras lacustres, salvo las excepciones que fije el Anexo I para cada ambiente. En el río Gallegos rige la devolución obligatoria, con la ventana de febrero de 2027 descrita arriba. Fuente: Disposición N° 241/2026.' ],
		],
		'resumen_viejo' => 'No leímos el reglamento general; los valores de permiso son',
		'resumen_nuevo' => 'Reglamento General (Disposición N° 241/2026, rige desde el 1/10/2026): artículos 1 a 18 leídos el 13/11/2026; del Anexo I, solo las fichas citadas arriba. Los valores de permiso son',
		'mosca_viejo' => 'pero no leímos el reglamento general, así que preguntale a tu guía.',
		'mosca_nuevo' => 'y el reglamento general (art. 9) autoriza spinning, mosca y trolling, salvo lo que restrinja el anexo de cada ambiente: confirmalo con tu guía.',
		'pend_nuevo' => 'las fichas del Anexo I más allá de las que cita la guía; ',
	],
	'en' => [
		'disp' => 'Disposición N° 241/2026',
		'fuente_pdf' => 'Disposición N° 241/2026, Santa Cruz Secretary of State for Fishing and Aquaculture: General Regulation of Continental Sport Fishing, 2026/2027 season (signed 22/9/2026, in force from 1/10/2026; signed text read 13/11/2026)',
		'fuente_web' => 'Government of Santa Cruz, Fishing and Aquaculture: Fishing Regulation, 2026/2027 season (articles 1 to 18 and Didymo protocol; read 13/11/2026)',
		'h2' => 'The general regulation of Santa Cruz, 2026/27',
		'rx_pos' => '/exclusion area/u',
		'intro' => 'The environments annex is read together with the <strong>General Regulation</strong>, approved by <strong>Disposición N° 241/2026</strong> of the Santa Cruz Secretary of State for Fishing and Aquaculture (signed 22 September 2026, in force from 1 October 2026). We read its articles 1 to 18 on 13 November 2026, in the signed text and on the official portal. What matters to a visiting angler:',
		'items' => [
			'<strong>Disinfecting your gear (Article 18):</strong> every angler entering the province must use new or previously disinfected equipment. Anglers, continental fishing guides and tourism providers must disinfect waders, boots, fishing gear, float tubes and vehicles, and changing basin requires immediate sanitising. The portal gives the procedure: remove vegetation, mud, algae and sediment; soak in bleach or hot water (above 60 °C); dry in the sun and wait 48 hours before reuse.',
			'<strong>Felt soles (Article 18):</strong> not banned; their use is conditional on strictly following those disinfection rules.',
			'<strong>Lure and hook (Article 8):</strong> one artificial lure with a single hook, simple or double, barbed or barbless; if the lure comes with more hooks, the rest must be removed or made unusable. Where release is mandatory, the hook is single or double with a micro barb or a crushed barb (Article 11).',
			'<strong>One rod (Article 10):</strong> one fishing rig in use at a time; spare rigs may be carried but not deployed together.',
			'<strong>No lead (Article 15 r):</strong> weights, counterweights, bottom sinkers, weighted lines and lures with lead in their heads or cores are banned.',
			'<strong>No night fishing (Article 15 t):</strong> banned across the whole province; the fishing day runs from sunrise to sunset (Article 6).',
			'<strong>Mandatory release (Article 11):</strong> in all rivers and streams, and within 200 m on both banks of their lake sources or mouths, every fish is released immediately and unharmed, except for the exceptions in Annex I (on the Río Gallegos, the February window above). Where release is mandatory you may not hold or transport any fish (Article 12).',
		],
		'cierre' => '<strong>What we have not read yet:</strong> of Annex I, only the entries this guide cites; the annex itself says it lists some of the most outstanding environments. Rules change every season: confirm them with your guide or the authority before you go.',
		'faqs' => [
			[ 'Do I have to disinfect my gear to fish in Santa Cruz?', 'Yes. Article 18 of the General Regulation (Disposición N° 241/2026) requires every angler entering the province to use new or previously disinfected equipment, and anglers, guides and tourism providers to disinfect waders, boots, gear, float tubes and vehicles; changing basin calls for immediate sanitising. The official portal gives the procedure: remove vegetation, mud, algae and sediment; soak in bleach or hot water (above 60 °C); dry in the sun and wait 48 hours before reuse.' ],
			[ 'Can I wear felt-soled wading boots in Santa Cruz?', 'They are not banned, but their use is conditional on strictly following the disinfection rules of Article 18 (Disposición N° 241/2026), above all when changing basin. Ask your lodge or guide what disinfection routine they follow.' ],
			[ 'What lures and hooks are allowed in Santa Cruz?', 'One artificial lure with a single hook, simple or double, barbed or barbless (Article 8); where release is mandatory, with a micro barb or a crushed barb (Article 11). One rod in use at a time (Article 10). Weights and lures with lead in the head or core are banned (Article 15 r), and so is night fishing across the whole province (Article 15 t). Annex I can add exceptions for each water. Source: Disposición N° 241/2026.' ],
			[ 'Is release mandatory on every river in Santa Cruz?', 'The general rule of Article 11 is immediate, unharmed release of every fish in rivers and streams and within 200 m of their lake sources or mouths, except for the exceptions Annex I sets for each water. On the Río Gallegos release is mandatory, with the February 2027 window described above. Source: Disposición N° 241/2026.' ],
		],
		'resumen_viejo' => 'We did not read the general regulation; permit values are',
		'resumen_nuevo' => 'General Regulation (Disposición N° 241/2026, in force from 1/10/2026): articles 1 to 18 read on 13/11/2026; of Annex I, only the entries cited above. Permit values are',
		'mosca_viejo' => 'but we did not read the general regulation, so ask your guide.',
		'mosca_nuevo' => 'and the general regulation (Article 9) allows spinning, fly and trolling, except where the annex of a given water restricts it: confirm it with your guide.',
		'pend_nuevo' => 'the Annex I entries beyond the ones this guide cites; ',
	],
	'pt' => [
		'disp' => 'Disposición N° 241/2026',
		'fuente_pdf' => 'Disposición N° 241/2026, Secretaria de Estado de Pesca e Aquicultura de Santa Cruz: Regulamento Geral de Pesca Esportiva Continental, temporada 2026/2027 (assinada em 22/9/2026, em vigor desde 1/10/2026; texto assinado lido em 13/11/2026)',
		'fuente_web' => 'Governo de Santa Cruz, Pesca e Aquicultura: Regulamento de Pesca, temporada 2026/2027 (artigos 1 a 18 e protocolo Didymo; lido em 13/11/2026)',
		'h2' => 'O regulamento geral de Santa Cruz, 2026/27',
		'rx_pos' => '/área de exclusão/u',
		'intro' => 'O anexo de ambientes lê-se junto com o <strong>Regulamento Geral</strong>, aprovado pela <strong>Disposición N° 241/2026</strong> da Secretaria de Estado de Pesca e Aquicultura de Santa Cruz (assinada em 22/9/2026, em vigor desde 1/10/2026). Lemos os artigos 1 a 18 em 13/11/2026, no texto assinado e no portal oficial. O que importa para quem vem pescar:',
		'items' => [
			'<strong>Desinfecção do equipamento (art. 18):</strong> todo pescador que entra na província deve usar equipamentos novos ou previamente desinfetados. Pescadores, guias de pesca continental e prestadores turísticos devem desinfetar waders, botas, equipamentos de pesca, flutuadores e veículos, e a mudança de bacia exige sanitização imediata. O portal indica o procedimento: retirar restos de vegetação, barro, algas e sedimentos; deixar de molho em água sanitária ou água quente (mais de 60 °C); secar ao sol e esperar 48 horas antes de reutilizar.',
			'<strong>Solas de feltro (art. 18):</strong> não são proibidas; o uso fica condicionado ao cumprimento rigoroso dessas regras de desinfecção.',
			'<strong>Isca e anzol (art. 8):</strong> uma única isca artificial com um único anzol, simples ou duplo, com ou sem farpa; se a isca vier com mais anzóis, é preciso removê-los ou inutilizá-los. Onde a soltura é obrigatória, o anzol é simples ou duplo com microfarpa ou farpa amassada (art. 11).',
			'<strong>Um só equipamento (art. 10):</strong> usa-se um só equipamento de pesca por vez; podem-se levar equipamentos reserva, mas não os usar juntos.',
			'<strong>Sem chumbo (art. 15 r):</strong> são proibidos lastros, contrapesos, chumbadas de fundo, linhas chumbadas e iscas com chumbo na cabeça ou no núcleo.',
			'<strong>Sem pesca noturna (art. 15 t):</strong> proibida em toda a província; o dia de pesca vai do nascer ao pôr do sol (art. 6).',
			'<strong>Soltura obrigatória (art. 11):</strong> em todos os rios e arroios, e a 200 m nas duas margens de suas nascentes ou fozes lacustres, todos os exemplares são soltos de imediato e sem dano, salvo as exceções do Anexo I (no rio Gallegos, a janela de fevereiro acima). Onde a soltura é obrigatória não se pode ter nem transportar nenhum exemplar (art. 12).',
		],
		'cierre' => '<strong>O que ainda não lemos:</strong> do Anexo I, só as fichas que este guia cita; o próprio anexo diz que lista alguns dos ambientes mais destacados. As regras mudam a cada temporada: confirme-as com seu guia ou com a autoridade antes de sair.',
		'faqs' => [
			[ 'Preciso desinfetar meu equipamento para pescar em Santa Cruz?', 'Sim. O artigo 18 do Regulamento Geral (Disposición N° 241/2026) obriga todo pescador que entra na província a usar equipamentos novos ou previamente desinfetados, e pescadores, guias e prestadores turísticos a desinfetar waders, botas, equipamentos, flutuadores e veículos; ao mudar de bacia é preciso sanitizar de imediato. O portal oficial indica: retirar vegetação, barro, algas e sedimentos; deixar de molho em água sanitária ou água quente (mais de 60 °C); secar ao sol e esperar 48 horas antes de reutilizar.' ],
			[ 'Posso usar botas com sola de feltro em Santa Cruz?', 'Não são proibidas, mas o uso fica condicionado ao cumprimento rigoroso das regras de desinfecção do artigo 18 (Disposición N° 241/2026), sobretudo ao mudar de bacia. Pergunte ao seu lodge ou guia que rotina de desinfecção seguem.' ],
			[ 'Que iscas e anzóis posso usar em Santa Cruz?', 'Uma única isca artificial com um único anzol, simples ou duplo, com ou sem farpa (art. 8); onde a soltura é obrigatória, com microfarpa ou farpa amassada (art. 11). Um só equipamento por vez (art. 10). São proibidos lastros e iscas com chumbo na cabeça ou no núcleo (art. 15 r) e a pesca noturna em toda a província (art. 15 t). O Anexo I pode trazer exceções por ambiente. Fonte: Disposición N° 241/2026.' ],
			[ 'A soltura é obrigatória em todos os rios de Santa Cruz?', 'A regra geral do artigo 11 é a soltura imediata e sem dano de todos os exemplares em rios e arroios e a 200 m de suas nascentes ou fozes lacustres, salvo as exceções que o Anexo I fixa para cada ambiente. No rio Gallegos vale a soltura obrigatória, com a janela de fevereiro de 2027 descrita acima. Fonte: Disposición N° 241/2026.' ],
		],
		'resumen_viejo' => 'Não lemos o regulamento geral; os valores de licença são',
		'resumen_nuevo' => 'Regulamento Geral (Disposición N° 241/2026, em vigor desde 1/10/2026): artigos 1 a 18 lidos em 13/11/2026; do Anexo I, só as fichas citadas acima. Os valores de licença são',
		'mosca_viejo' => 'mas não lemos o regulamento geral, então pergunte ao seu guia.',
		'mosca_nuevo' => 'e o regulamento geral (art. 9) autoriza spinning, mosca e trolling, salvo o que o anexo de cada ambiente restrinja: confirme com seu guia.',
		'pend_nuevo' => 'as fichas do Anexo I além das que este guia cita; ',
	],
];

/** Add the PDF and portal sources when their URL is not on the guide yet; returns the rows to write (or null when nothing to add). */
function pm_scr_fuentes( int $id, array $quiero, string $hoy ): ?array {
	$filas = array_map( function ( $f ) {
		$f['consultada'] = isset( $f['consultada'] ) ? str_replace( '-', '', (string) $f['consultada'] ) : '';
		return $f;
	}, array_values( array_filter( pm_filas( 'fuentes', $id ), 'is_array' ) ) );
	$urls = array_column( $filas, 'url' );
	$nuevo = false;
	foreach ( $quiero as [ $nombre, $url ] ) {
		if ( ! in_array( $url, $urls, true ) ) {
			$filas[] = [ 'nombre' => $nombre, 'url' => $url, 'oficial' => 1, 'consultada' => $hoy ];
			$nuevo = true;
		}
	}
	return $nuevo ? $filas : null;
}

// --- 1. Río Gallegos: rules section, FAQs, summary, fly FAQ, hidden pending line, sources ------------------------------
foreach ( $L as $i => $x ) {
	$p = pm_buscar( 'pm_destino', 'rio-gallegos', $i );
	if ( ! $p ) {
		WP_CLI::warning( "rio-gallegos ($i): no existe, se salta." );
		continue;
	}
	$c     = $p->post_content;
	$nuevo = $c;
	$tareas = [];
	if ( false === strpos( $c, $x['h2'] ) ) {
		$pos = pm_sc_pos_tras_parrafo( $c, $x['rx_pos'] );
		if ( null === $pos ) {
			WP_CLI::warning( "rio-gallegos ($i): no encontré el párrafo del área de exclusión; no agrego la sección." );
		} else {
			$bloque = pm_sc_h2( $x['h2'] ) . pm_sc_p( $x['intro'] ) . pm_sc_lista( $x['items'] ) . pm_sc_p( $x['cierre'] );
			$nuevo  = pm_sc_insertar( $nuevo, $pos, $bloque );
			$tareas[] = "H2 «{$x['h2']}» (7 reglas, Disposición N° 241/2026)";
		}
	}
	// Hidden pending line: the general regulation is no longer "unread".
	$n = 0;
	$nuevo = preg_replace_callback( '#(PENDIENTE: )(?:el reglamento general de pesca de Santa Cruz|the general fishing regulation of Santa Cruz|o regulamento geral de pesca de Santa Cruz) \([^)]*\);\s*#u', function ( $m ) use ( $x, &$n ) {
		$n++;
		return $m[1] . $x['pend_nuevo'];
	}, $nuevo );
	$n && $tareas[] = 'línea PENDIENTE: sale «reglamento general», entra lo que falta del Anexo I';

	// Fields.
	$resumen = (string) pm_campo( 'reglamento_resumen', $p->ID );
	$resumen_n = pm_scr_cambiar( $resumen, $x['resumen_viejo'], $x['resumen_nuevo'] );
	$resumen_n && $tareas[] = 'reglamento_resumen: sale «No leímos el reglamento general»';
	$filas = pm_filas( 'faqs', $p->ID );
	$faqs_n = $filas;
	$mosca = false;
	foreach ( $faqs_n as $k => $f ) {
		$r = pm_scr_cambiar( (string) ( $f['respuesta'] ?? '' ), $x['mosca_viejo'], $x['mosca_nuevo'] );
		if ( null !== $r ) {
			$faqs_n[ $k ]['respuesta'] = $r;
			$mosca = true;
		}
	}
	$mosca && $tareas[] = 'FAQ «solo mosca»: sale «no leímos el reglamento general»';
	$preg = array_column( $faqs_n, 'pregunta' );
	$agrega = 0;
	foreach ( $x['faqs'] as [ $q, $a ] ) {
		if ( ! in_array( $q, $preg, true ) ) {
			$faqs_n[] = [ 'pregunta' => $q, 'respuesta' => $a ];
			$agrega++;
		}
	}
	$agrega && $tareas[] = "$agrega FAQ nuevas: " . implode( ' | ', array_column( $x['faqs'], 0 ) );
	$fuentes_n = pm_scr_fuentes( $p->ID, [ [ $x['fuente_pdf'], $u_pdf ], [ $x['fuente_web'], $u_web ] ], $hoy );
	$fuentes_n && $tareas[] = 'fuentes: Disposición N° 241/2026 (PDF y portal)';
	if ( ! $tareas ) {
		WP_CLI::log( "rio-gallegos ($i) #{$p->ID}: ya está." );
		continue;
	}
	if ( null === $resumen_n && false !== stripos( $resumen, 'general' ) && false === strpos( $resumen, '241/2026' ) ) {
		WP_CLI::warning( "rio-gallegos ($i): el resumen menciona el reglamento general pero no encontré la frase exacta; revisar." );
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": rio-gallegos ($i) #{$p->ID}: " . implode( ' · ', $tareas ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	if ( $nuevo !== $c ) {
		pm_sc_guardar( $p->ID, $nuevo, [ $x['h2'] ], "rio-gallegos ($i)" );
	}
	$resumen_n && pm_sc_campo( $p->ID, 'reglamento_resumen', $resumen_n, "rio-gallegos ($i)" );
	if ( $faqs_n !== $filas ) {
		update_field( 'faqs', $faqs_n, $p->ID );
		count( pm_filas( 'faqs', $p->ID ) ) === count( $faqs_n ) || WP_CLI::error( "rio-gallegos ($i): las FAQ no quedaron." );
	}
	if ( $fuentes_n ) {
		update_field( 'fuentes', $fuentes_n, $p->ID );
		count( pm_filas( 'fuentes', $p->ID ) ) === count( $fuentes_n ) || WP_CLI::error( "rio-gallegos ($i): las fuentes no quedaron." );
	}
	$leido = get_post( $p->ID )->post_content;
	$pf    = array_column( pm_filas( 'faqs', $p->ID ), 'pregunta' );
	$ok    = false !== strpos( $leido, $x['h2'] ) && in_array( $x['faqs'][0][0], $pf, true ) && false === strpos( (string) pm_campo( 'reglamento_resumen', $p->ID ), $x['resumen_viejo'] ) && false === stripos( $leido, 'felt soles, hooks, disinfection' );
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": rio-gallegos ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "rio-gallegos ($i): la lectura de vuelta no coincide." );
}

// --- 2. Jurassic Lake: hook "simple o doble" (art. 8), disinfection sentence, one FAQ -----------------------------------
$J = [
	'es' => [
		'cuerpo' => [
			[ '<strong>Un solo señuelo artificial con un solo anzuelo</strong> (art. 8)', '<strong>Un solo señuelo artificial con un único anzuelo, simple o doble</strong> (art. 8)' ],
			[ 'las suelas de fieltro solo se admiten si se cumple el protocolo (arts. 17 y 18).', 'quien entra a la provincia debe usar equipos nuevos o previamente desinfectados, y las suelas de fieltro solo se admiten si se cumple el protocolo (arts. 17 y 18). Procedimiento del portal: retirar vegetación y barro, remojar con lavandina o agua a más de 60 °C, secar al sol y esperar 48 horas.' ],
		],
		'faq' => [ 'Un solo señuelo artificial con un solo anzuelo simple, con o sin rebaba', 'Un solo señuelo artificial con un único anzuelo, simple o doble, con o sin rebaba' ],
	],
	'en' => [
		'cuerpo' => [
			[ '<strong>One artificial lure with one hook</strong> (Article 8)', '<strong>One artificial lure with one hook, single or double</strong> (Article 8)' ],
			[ 'felt soles are only allowed if you follow the protocol (Articles 17 and 18).', 'anyone entering the province must use new or previously disinfected equipment, and felt soles are only allowed if you follow the protocol (Articles 17 and 18). The portal\'s procedure: remove vegetation and mud, soak in bleach or water above 60 °C, dry in the sun and wait 48 hours.' ],
		],
		'faq' => [ 'One artificial lure with a single hook, barbed or barbless', 'One artificial lure with a single hook, single or double, barbed or barbless' ],
	],
	'pt' => [
		'cuerpo' => [
			[ '<strong>Uma isca artificial com um anzol</strong> (artigo 8)', '<strong>Uma isca artificial com um anzol, simples ou duplo</strong> (artigo 8)' ],
			[ 'solas de feltro só são permitidas seguindo o protocolo (artigos 17 e 18).', 'quem entra na província deve usar equipamentos novos ou previamente desinfetados, e solas de feltro só são permitidas seguindo o protocolo (artigos 17 e 18). Procedimento do portal: retirar vegetação e barro, deixar de molho em água sanitária ou água a mais de 60 °C, secar ao sol e esperar 48 horas.' ],
		],
		'faq' => [ 'Uma isca artificial com um anzol simples, com ou sem farpa', 'Uma isca artificial com um único anzol, simples ou duplo, com ou sem farpa' ],
	],
];
foreach ( $J as $i => $j ) {
	$x = $L[ $i ];
	$p = pm_buscar( 'pm_destino', 'jurassic-lake', $i );
	if ( ! $p ) {
		WP_CLI::warning( "jurassic-lake ($i): no existe, se salta." );
		continue;
	}
	$c = $p->post_content;
	$nuevo = $c;
	$tareas = [];
	foreach ( $j['cuerpo'] as [ $v, $n ] ) {
		if ( false !== strpos( $nuevo, $v ) ) {
			$nuevo = str_replace( $v, $n, $nuevo );
			$tareas[] = 'cuerpo: «' . mb_substr( wp_strip_all_tags( $v ), 0, 40 ) . '…»';
		} elseif ( false === strpos( $nuevo, $n ) ) {
			WP_CLI::warning( "jurassic-lake ($i): no encontré «" . mb_substr( wp_strip_all_tags( $v ), 0, 50 ) . '…» en el cuerpo.' );
		}
	}
	$filas  = pm_filas( 'faqs', $p->ID );
	$faqs_n = $filas;
	$hook   = false;
	foreach ( $faqs_n as $k => $f ) {
		$r = pm_scr_cambiar( (string) ( $f['respuesta'] ?? '' ), $j['faq'][0], $j['faq'][1] );
		if ( null !== $r ) {
			$faqs_n[ $k ]['respuesta'] = $r;
			$hook = true;
		}
	}
	$hook && $tareas[] = 'FAQ de anzuelo: «simple o doble» (art. 8)';
	$hook || false !== strpos( implode( ' ', array_column( $faqs_n, 'respuesta' ) ), $j['faq'][1] ) || WP_CLI::warning( "jurassic-lake ($i): no encontré la FAQ de anzuelo." );
	[ $q, $a ] = $x['faqs'][0];
	if ( ! in_array( $q, array_column( $faqs_n, 'pregunta' ), true ) ) {
		$faqs_n[] = [ 'pregunta' => $q, 'respuesta' => $a ];
		[ $q2, $a2 ] = $x['faqs'][1];
		$faqs_n[] = [ 'pregunta' => $q2, 'respuesta' => $a2 ];
		$tareas[] = '2 FAQ nuevas (desinfección, suelas de fieltro)';
	}
	$fuentes_n = pm_scr_fuentes( $p->ID, [ [ $x['fuente_pdf'], $u_pdf ] ], $hoy );
	$fuentes_n && $tareas[] = 'fuente: PDF de la Disposición N° 241/2026';
	if ( ! $tareas ) {
		WP_CLI::log( "jurassic-lake ($i) #{$p->ID}: ya está." );
		continue;
	}
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": jurassic-lake ($i) #{$p->ID}: " . implode( ' · ', $tareas ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	if ( $nuevo !== $c ) {
		pm_sc_guardar( $p->ID, $nuevo, [ $j['cuerpo'][0][1] ], "jurassic-lake ($i)" );
	}
	if ( $faqs_n !== $filas ) {
		update_field( 'faqs', $faqs_n, $p->ID );
		count( pm_filas( 'faqs', $p->ID ) ) === count( $faqs_n ) || WP_CLI::error( "jurassic-lake ($i): las FAQ no quedaron." );
	}
	if ( $fuentes_n ) {
		update_field( 'fuentes', $fuentes_n, $p->ID );
		count( pm_filas( 'fuentes', $p->ID ) ) === count( $fuentes_n ) || WP_CLI::error( "jurassic-lake ($i): las fuentes no quedaron." );
	}
	$resp = implode( ' ', array_column( pm_filas( 'faqs', $p->ID ), 'respuesta' ) );
	$cont = get_post( $p->ID )->post_content;
	$dis  = false !== strpos( $cont, 'previamente desinfectados' ) || false !== strpos( $cont, 'previously disinfected' ) || false !== strpos( $cont, 'previamente desinfetados' );
	$ok   = false !== strpos( $resp, $j['faq'][1] ) && in_array( $x['faqs'][0][0], array_column( pm_filas( 'faqs', $p->ID ), 'pregunta' ), true ) && $dis;
	WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ": jurassic-lake ($i) #{$p->ID} " . get_permalink( $p->ID ) );
	$ok || WP_CLI::error( "jurassic-lake ($i): la lectura de vuelta no coincide." );
}

// --- 3. El Calafate (Santa Cruz): the biosecurity sentence after the paragraph that opens the rules --------------------
$C = [
	'es' => [ 'rx' => '/temporada general va del/u', 'marca' => 'previamente desinfectados', 'p' => '<strong>Desinfección del equipo.</strong> El artículo 18 del Reglamento General exige a todo pescador que entra a la provincia usar equipos nuevos o previamente desinfectados, y desinfectar waders, botas, equipos, flotadores y vehículos al cambiar de cuenca; las botas con suela de fieltro solo se admiten si se cumple. Procedimiento del portal: retirar vegetación y barro, remojar con lavandina o agua a más de 60 °C, secar al sol y esperar 48 horas (Disposición N° 241/2026, leída el 13/11/2026).' ],
	'en' => [ 'rx' => '/general season runs from/u', 'marca' => 'previously disinfected', 'p' => '<strong>Disinfecting your gear.</strong> Article 18 of the General Regulation requires every angler entering the province to use new or previously disinfected equipment, and to disinfect waders, boots, gear, float tubes and vehicles when changing basin; felt-soled boots are only allowed if you comply. The portal\'s procedure: remove vegetation and mud, soak in bleach or water above 60 °C, dry in the sun and wait 48 hours (Disposición N° 241/2026, read 13 November 2026).' ],
	'pt' => [ 'rx' => '/temporada geral vai de/u', 'marca' => 'previamente desinfetados', 'p' => '<strong>Desinfecção do equipamento.</strong> O artigo 18 do Regulamento Geral exige que todo pescador que entra na província use equipamentos novos ou previamente desinfetados, e desinfete waders, botas, equipamentos, flutuadores e veículos ao mudar de bacia; botas com sola de feltro só são permitidas se isso for cumprido. Procedimento do portal: retirar vegetação e barro, deixar de molho em água sanitária ou água a mais de 60 °C, secar ao sol e esperar 48 horas (Disposición N° 241/2026, lida em 13/11/2026).' ],
];
foreach ( $C as $i => $cx ) {
	$p = pm_buscar( 'pm_destino', 'el-calafate', $i );
	if ( ! $p ) {
		WP_CLI::warning( "el-calafate ($i): no existe, se salta." );
		continue;
	}
	$c = $p->post_content;
	if ( false !== strpos( $c, $cx['marca'] ) ) {
		WP_CLI::log( "el-calafate ($i) #{$p->ID}: ya tiene la frase de desinfección." );
		continue;
	}
	$pos = pm_sc_pos_tras_parrafo( $c, $cx['rx'] );
	if ( null === $pos ) {
		WP_CLI::warning( "el-calafate ($i) #{$p->ID}: no encontré el párrafo de reglas; no agrego la frase." );
		continue;
	}
	$fuentes_n = pm_scr_fuentes( $p->ID, [ [ $L[ $i ]['fuente_pdf'], $u_pdf ] ], $hoy );
	WP_CLI::log( pm_sc_modo( $aplicar ) . ": el-calafate ($i) #{$p->ID}: párrafo de desinfección (art. 18) tras el de la temporada general" . ( $fuentes_n ? ' + fuente PDF' : '' ) );
	$cambios++;
	if ( ! $aplicar ) {
		continue;
	}
	pm_sc_guardar( $p->ID, pm_sc_insertar( $c, $pos, pm_sc_p( $cx['p'] ) ), [ $cx['marca'] ], "el-calafate ($i)" );
	if ( $fuentes_n ) {
		update_field( 'fuentes', $fuentes_n, $p->ID );
		count( pm_filas( 'fuentes', $p->ID ) ) === count( $fuentes_n ) || WP_CLI::error( "el-calafate ($i): las fuentes no quedaron." );
	}
	WP_CLI::log( "VERIFIED: el-calafate ($i) #{$p->ID} " . get_permalink( $p->ID ) );
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios cambios." );
