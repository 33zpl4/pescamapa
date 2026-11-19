<?php
/**
 * 19/11/2026 (orden del dueño, continuación de copy-operadores-cta-2026-11-18): la portada y /llms.txt muestran el campo `bajada`
 * de las guías, y tres de ellas decían «operador(es)» al describir lo que hacemos: Itatí («Qué dice la página de cada operador»),
 * Iberá («según cada operador») y río Santa Cruz («cuándo van los operadores»), en es/en/pt. Origen: el campo bajada de cada guía
 * (no ui.json, no la plantilla). Se reemplazan solo esas frases exactas; «según el operador» como atribución de una fuente no se toca.
 * Campos revisados: bajada, seo_description, seo_title de destinos, notas y especies. Idempotente. Simulacro por defecto ($aplicar = false):
 * imprime antes/después completos; con aplicar=true guarda por update_field y relee (VERIFIED / FALLÓ).
 * Al final cuenta, solo para informar, cuántas bajadas conservan «operador(es)» (no se tocan).
 * Run with wp-script.yml (script = copy-operadores-bajada-2026-11-19).
 */
if ( ! function_exists( 'pm_sc_campo' ) ) {
	function pm_sc_campo( int $id, string $campo, string $valor, string $etiqueta ): void {
		update_field( $campo, $valor, $id );
		if ( pm_campo( $campo, $id ) !== $valor ) {
			WP_CLI::error( "$etiqueta: el campo $campo no quedó como se esperaba." );
		}
	}
}

$aplicar = false;

$pares = [
	[ 'Qué dice la página de cada operador', 'Qué dice la página de cada lugar y guía' ],
	[ 'cuándo van los operadores', 'cuándo se sale a pescar' ],
	[ 'según cada operador.', 'según cada lugar.' ],
	[ 'What each operator’s page says', 'What each place’s and guide’s page says' ],
	[ "What each operator's page says", "What each place's and guide's page says" ],
	[ 'when operators go', 'when trips run' ],
	[ 'according to each operator.', 'according to each place.' ],
	[ 'O que diz a página de cada operador', 'O que diz a página de cada lugar e guia' ],
	[ 'quando vão os operadores', 'quando se sai para pescar' ],
	[ 'segundo cada operador.', 'segundo cada lugar.' ],
];

$modo    = $aplicar ? 'APLICA' : 'SIMULACRO';
$cambios = 0;
$ok      = 0;
$restan  = 0;
foreach ( [ 'pm_destino', 'pm_nota', 'pm_especie' ] as $tipo ) {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ] ) as $p ) {
		$etq = substr( $tipo, 3 ) . ':' . pm_clave_de( $p->ID ) . '(' . pm_idioma_de( $p->ID ) . ') #' . $p->ID;
		foreach ( [ 'bajada', 'seo_description', 'seo_title' ] as $campo ) {
			$v = pm_campo( $campo, $p->ID );
			if ( ! is_string( $v ) || '' === $v ) {
				continue;
			}
			$nuevo = $v;
			foreach ( $pares as [ $viejo, $reemp ] ) {
				$nuevo = str_replace( $viejo, $reemp, $nuevo );
			}
			if ( $nuevo === $v ) {
				if ( 'bajada' === $campo && preg_match( '/operadores?|operators?/iu', $v ) ) {
					$restan++;
					WP_CLI::log( "INFO sin tocar: $etq bajada conserva «operador(es)»: $v" );
				}
				continue;
			}
			$cambios++;
			WP_CLI::log( "$modo: $etq $campo\n  antes:   $v\n  después: $nuevo" );
			if ( ! $aplicar ) {
				continue;
			}
			pm_sc_campo( $p->ID, $campo, $nuevo, $etq );
			$leido = (string) pm_campo( $campo, $p->ID );
			$mal   = 0;
			foreach ( $pares as [ $viejo ] ) {
				$mal += substr_count( $leido, $viejo );
			}
			WP_CLI::log( ( ( 0 === $mal && $leido === $nuevo ) ? 'VERIFIED' : 'FALLÓ' ) . ": $etq $campo" );
			( 0 === $mal && $leido === $nuevo ) || WP_CLI::error( "$etq $campo: la lectura de vuelta no coincide." );
			$ok++;
		}
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $cambios campos" . ( $aplicar ? ", $ok verificados" : '' ) . "; bajadas con «operador(es)» que no se tocan: $restan." );
