<?php
/**
 * 18/11/2026 (orden del dueño): hacia el visitante no se dice «operador(es)» cuando describimos lo que hace Pescamapa.
 * Los llamados a la acción de notas, guías y especies (es/en/pt) dicen «consultamos a los operadores que encajan» / «we ask the operators
 * that fit» / «consultamos os operadores que se encaixam»: pasan a «los lugares y guías que encajan» / «the places and guides that fit» /
 * «os lugares e guias que se encaixam». Solo esas frases (descripción del servicio); «según el operador» en datos atribuidos a un
 * prestador concreto NO se toca (es lenguaje de la fuente). Texto del cuerpo + campos bajada / seo_description / seo_title.
 * Idempotente (una segunda corrida no encuentra nada). Simulacro por defecto ($aplicar = false); wp-script.yml con aplicar=true.
 * Imprime por post cuántas frases cambia y, al final, cuántas veces queda «operador(es)» en otros contextos (informativo, no se toca).
 * Run with wp-script.yml (script = copy-operadores-cta-2026-11-18).
 */
if ( ! function_exists( 'pm_sc_guardar' ) ) {
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
}

$aplicar = false;

$pares = [
	[ 'los operadores que encajan', 'los lugares y guías que encajan' ],
	[ 'the operators that fit', 'the places and guides that fit' ],
	[ 'os operadores que se encaixam', 'os lugares e guias que se encaixam' ],
	[ 'os operadores que combinam', 'os lugares e guias que combinam' ],
];

$aplica = function ( string $texto ) use ( $pares ): array {
	$n = 0;
	foreach ( $pares as [ $viejo, $nuevo ] ) {
		$c = substr_count( $texto, $viejo );
		if ( $c ) {
			$texto = str_replace( $viejo, $nuevo, $texto );
			$n    += $c;
		}
	}
	return [ $texto, $n ];
};

$modo    = $aplicar ? 'APLICA' : 'SIMULACRO';
$tocados = 0;
$frases  = 0;
$restan  = 0;
$ok      = 0;
foreach ( [ 'pm_nota', 'pm_destino', 'pm_especie' ] as $tipo ) {
	foreach ( get_posts( [ 'post_type' => $tipo, 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ] ) as $p ) {
		$etq = substr( $tipo, 3 ) . ':' . pm_clave_de( $p->ID ) . '(' . pm_idioma_de( $p->ID ) . ') #' . $p->ID;
		[ $nuevo, $n_cuerpo ] = $aplica( $p->post_content );
		$campos = [];
		foreach ( [ 'bajada', 'seo_description', 'seo_title' ] as $campo ) {
			$v = pm_campo( $campo, $p->ID );
			if ( is_string( $v ) && '' !== $v ) {
				[ $v2, $c ] = $aplica( $v );
				if ( $c ) {
					$campos[ $campo ] = $v2;
				}
			}
		}
		$resto = preg_match_all( '/operadores?|operators?/iu', wp_strip_all_tags( $nuevo ) );
		$restan += $resto;
		if ( ! $n_cuerpo && ! $campos ) {
			continue;
		}
		$tocados++;
		$frases += $n_cuerpo + count( $campos );
		WP_CLI::log( "$modo: $etq: $n_cuerpo frases en el cuerpo, campos: " . ( $campos ? implode( ', ', array_keys( $campos ) ) : 'ninguno' ) . "; «operador(es)» que quedan en otro contexto: $resto" );
		if ( ! $aplicar ) {
			continue;
		}
		if ( $n_cuerpo ) {
			pm_sc_guardar( $p->ID, $nuevo, [], $etq );
		}
		foreach ( $campos as $campo => $valor ) {
			pm_sc_campo( $p->ID, $campo, $valor, $etq );
		}
		$leido = get_post( $p->ID )->post_content;
		$mal   = 0;
		foreach ( $pares as [ $viejo ] ) {
			$mal += substr_count( $leido, $viejo );
		}
		WP_CLI::log( ( 0 === $mal ? 'VERIFIED' : 'FALLÓ' ) . ": $etq: sin las frases viejas en el cuerpo ($mal)" );
		0 === $mal || WP_CLI::error( "$etq: quedaron frases viejas." );
		$ok++;
	}
}
WP_CLI::success( ( $aplicar ? 'Aplicado' : 'Simulacro terminado' ) . ": $tocados posts, $frases cambios" . ( $aplicar ? ", $ok verificados" : '' ) . "; «operador(es)» que quedan en otros contextos (no se tocan): $restan." );
