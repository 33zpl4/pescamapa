<?php
/**
 * Portuguese packages page of Bariloche, 31/10/2026: the two packages (lake day, Limay float) and the three "before you travel"
 * blocks, as they are in Spanish and English on 31/10 (prices: lake from USD 400 for 1 angler / 600 for 2; Limay from USD 550 / 650;
 * both dated 31/10/2026; the Limay row carries the Claudio Antonelli photo). Hangs from the Portuguese guide: run AFTER
 * bariloche-pt-2026-10-31. Idempotent: a pt guide that already has packages or blocks is not touched. Dry run first
 * ($aplicar = false); run with wp-script.yml (script = bariloche-paquetes-pt-2026-10-31).
 */

$aplicar = true;
$hoy     = '20261031';
$lineas  = fn( array $l ) => implode( "\n", $l );

$p = pm_buscar( 'pm_destino', 'bariloche', 'pt' );
$p || WP_CLI::error( 'no existe la guía bariloche (pt): correr bariloche-pt-2026-10-31 antes.' );
$foto = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_pm_archivo_origen', 'meta_value' => 'limay-pescador-bote.jpg', 'numberposts' => 1, 'fields' => 'ids' ] );
$foto = $foto ? (int) $foto[0] : 0;

$interno = 'Versão em português dos pacotes de Bariloche (31/10/2026). Preços: os mesmos das versões es/en; lago segundo o agente com quem se trabalha se houver interessados; Limay = referência de mercado até ter operador local. Ver os scripts paquetes-*-bariloche de 30 e 31/10.';
$paquetes = [
	[
		'publicado' => 1, 'titulo' => 'Trutas do lago Nahuel Huapi (Bariloche): dia de barco com churrasco na praia',
		'resumen'   => 'Saída privada de barco com guia pelo lago Nahuel Huapi, pescando onde o lago recebe rios e cachoeiras, com um churrasco na praia ao meio-dia. As regras de captura de cada data são confirmadas pelo guia.',
		'modalidad' => 'Mosca com streamer e linha afundante, ou spinning', 'duracion' => 'Dia completo; também dá para montar meio dia', 'capacidad' => 'Saída privada para o seu grupo; a quantidade por barco é confirmada na proposta',
		'incluye'   => $lineas( [ 'Guia privado', 'Equipamento de pesca completo (vara, carretilha e moscas)', 'Churrasco ao meio-dia com bebidas', 'Licenças de pesca', 'Barco e combustível' ] ),
		'no_incluye' => 'Voos e traslados até Bariloche', 'variantes' => $lineas( [ 'Dia completo com churrasco na praia', 'Meio dia, manhã ou tarde' ] ),
		'desde_monto' => 400, 'desde_moneda' => 'USD', 'desde_unidad' => 'para 1 pescador, dia completo; 2 pescadores: US$ 600 no total', 'desde_fecha' => $hoy, 'interno' => $interno, 'foto' => 0,
	],
	[
		'publicado' => 1, 'titulo' => 'Pesca com mosca no rio Limay (Bariloche): descida de barco a remo, dia completo',
		'resumen'   => 'Descida de um dia em barco a remo pelo rio Limay, a partir do lago Nahuel Huapi, pescando com mosca à deriva e lançando o streamer para a margem. Saída cedo, com churrasco ao meio-dia. As regras do trecho e os horários de pesca de cada data são confirmados pelo guia.',
		'modalidad' => 'Mosca (streamer e mosca seca), de um barco a remo sem motor', 'duracion' => 'Dia completo, com saída cedo', 'capacidad' => 'Saída privada para o seu grupo',
		'incluye'   => $lineas( [ 'Guia privado', 'Equipamento de pesca completo (vara, carretilha e moscas)', 'Churrasco ao meio-dia com bebidas', 'Licenças de pesca', 'Barco' ] ),
		'no_incluye' => 'Voos e traslados até Bariloche', 'variantes' => '',
		'desde_monto' => 550, 'desde_moneda' => 'USD', 'desde_unidad' => 'para 1 pescador, dia completo; 2 pescadores: US$ 650 no total', 'desde_fecha' => $hoy, 'interno' => $interno, 'foto' => $foto,
	],
];
$annp = 'https://reglamentodepesca.org.ar/reglamento-items/ambientes-parques-nacionales-del-sur/';
$bloques = [
	[ 'titulo' => 'Regras do lago na temporada 2026/27', 'texto' => $lineas( [ 'O lago Nahuel Huapi está aberto de 1º de novembro de 2026 a 1º de maio de 2027, com limite diário de dois salmonídeos.', 'De 2 a 31 de maio de 2027 o limite é um salmonídeo de menos de 40 cm e é proibido pescar a menos de 200 m das bocas de rios e arroios.', 'É proibido navegar a motor a menos de 300 m dos paredões da ilha Victoria e da ilha Fray Menéndez, e há proibições de desembarque em partes dos braços Blest e Tristeza e em algumas ilhas.' ] ), 'fuente_nombre' => 'Regulamento de pesca 2026/27: ambientes dos Parques Nacionais', 'fuente_url' => $annp, 'consultada' => '20261030' ],
	[ 'titulo' => 'A licença de pesca', 'texto' => $lineas( [ 'A licença de Río Negro é vendida online pela Mesa Directiva de Pesca Deportiva Zona Andina. Valores da temporada 2026/27 para não residentes, em pesos argentinos: um dia AR$ 50.000, dez dias AR$ 140.000, temporada AR$ 280.000.', 'Se a licença estiver incluída na proposta, dizemos na proposta; se não, você a tira antes da saída.' ] ), 'fuente_nombre' => 'Licenças de pesca (Mesa Directiva de Pesca Deportiva Zona Andina)', 'fuente_url' => 'https://www.permisosdepesca.com.ar/', 'consultada' => '20261030' ],
	[ 'titulo' => 'Regras do Limay no Parque Nacional, temporada 2026/27', 'texto' => $lineas( [ 'O Limay superior, dentro do Parque Nacional, pesca-se de 1º de novembro de 2026 a 31 de maio de 2027.', 'Barcos sem motor são permitidos, exceto nos primeiros 400 m abaixo do lago. Do lago até Rincón Chico é só mosca e devolução obrigatória.', 'O limite é um salmonídeo e, desde 1º de abril de 2027, todos os peixes são devolvidos. O anexo lista também os horários de pesca permitidos por mês.' ] ), 'fuente_nombre' => 'Regulamento de pesca 2026/27: ambientes dos Parques Nacionais', 'fuente_url' => $annp, 'consultada' => '20261030' ],
];

if ( pm_filas( 'paquetes', $p->ID ) || pm_filas( 'paquetes_info', $p->ID ) ) {
	WP_CLI::log( "bariloche (pt) #{$p->ID}: ya tiene paquetes o bloques, no los toco." );
	return;
}
WP_CLI::log( ( $aplicar ? 'APLICA' : 'SIMULACRO' ) . ": bariloche (pt) #{$p->ID}: " . count( $paquetes ) . ' paquetes (con precio fechado) y ' . count( $bloques ) . ' bloques → ' . pm_url_paquetes( 'bariloche', 'pt' ) . '; foto del Limay #' . ( $foto ?: 'NINGUNA' ) );
if ( ! $aplicar ) {
	WP_CLI::log( 'SIMULACRO: no se escribió nada' );
	return;
}
update_field( 'paquetes', $paquetes, $p->ID );
update_field( 'paquetes_info', $bloques, $p->ID );
$leidos = pm_paquetes( $p->ID );
$ok = 2 === count( $leidos ) && 2 === count( array_filter( array_column( $leidos, 'precio' ) ) ) && 3 === count( pm_paquetes_info( $p->ID ) ) && (int) $leidos[1]['foto'] === $foto && false === strpos( wp_json_encode( $leidos ), 'Versão em português dos pacotes' );
WP_CLI::log( ( $ok ? 'VERIFIED' : 'FALLÓ' ) . ': ' . count( $leidos ) . ' paquetes, ' . count( pm_paquetes_info( $p->ID ) ) . ' bloques, precios ' . implode( ' / ', array_map( fn( $l ) => $l['precio']['moneda'] . ' ' . $l['precio']['monto'], $leidos ) ) . '; el interno no sale' );
$ok || WP_CLI::error( 'la lectura de vuelta no coincide.' );
flush_rewrite_rules( false );
WP_CLI::log( 'APLICADO' );
