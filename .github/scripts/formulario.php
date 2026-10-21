<?php
/**
 * Diagnosis used by test-wp.yml: how the editor form loads a guide's repeaters,
 * as administrator and as the content editor (value loaded + acf/prepare_field).
 * Env: POST_ID.
 */

$id = (int) getenv( 'POST_ID' );
foreach ( [ 'admin', 'cheyenne' ] as $u ) {
	wp_set_current_user( get_user_by( 'login', $u )->ID );
	foreach ( [ 'field_pm_d_fuentes', 'field_pm_d_temporada', 'field_pm_d_faqs' ] as $k ) {
		$f          = acf_get_field( $k );
		$f['value'] = acf_get_value( $id, $f );
		$cargada    = is_array( $f['value'] ) ? reset( $f['value'] ) : null;
		$p          = acf_prepare_field( $f );
		$despues    = ( $p && is_array( $p['value'] ?? null ) ) ? reset( $p['value'] ) : null;
		echo $u . ' ' . $k . ' subcampos=' . implode( ',', array_map( fn( $x ) => $x['key'], $f['sub_fields'] ?? [] ) )
			. ' fila0_cargada=' . wp_json_encode( $cargada, JSON_UNESCAPED_UNICODE )
			. ' fila0_tras_prepare=' . wp_json_encode( $despues, JSON_UNESCAPED_UNICODE )
			. ' prepare=' . ( $p ? 'ok' : 'false' ) . "\n";
	}
}
