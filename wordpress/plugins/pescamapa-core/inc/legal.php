<?php
/**
 * Terms of use and privacy policy: /terminos/ + /privacidad/ (pt: termos, privacidade; en: terms, privacy).
 * The text lives in data/legal.json (per document and language: title, description, h1, sections);
 * {email}, {privacidad} and {terminos} in it become links. Edit the JSON and bump "actualizado".
 */

defined( 'ABSPATH' ) || exit;

function pm_legal( string $doc, ?string $idioma = null ): array {
	static $todo = null;
	$todo ??= json_decode( file_get_contents( PM_DIR . 'data/legal.json' ), true );
	return $todo[ $doc ][ $idioma ?? pm_lang() ] ?? [];
}

function pm_url_terminos( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['terminos'], $i );
}
function pm_url_privacidad( string $i ): string {
	return pm_url( PM_SEGMENTOS[ $i ]['privacidad'], $i );
}

/** Link to the privacy policy, for the small print under the forms. */
function pm_enlace_privacidad(): string {
	return '<a href="' . esc_url( pm_url_privacidad( pm_lang() ) ) . '">' . esc_html( pm_t( 'legal.privacidad.larga' ) ) . '</a>';
}

/** Section text with its {tokens} turned into links (the rest is escaped). */
function pm_legal_texto( string $texto ): string {
	$i = pm_lang();
	$enlaces = [
		'{email}'      => '<a href="mailto:' . esc_attr( PM_EMAIL_PUBLICO ) . '">' . esc_html( PM_EMAIL_PUBLICO ) . '</a>',
		'{privacidad}' => '<a href="' . esc_url( pm_url_privacidad( $i ) ) . '">' . esc_html( pm_t( 'legal.privacidad.larga' ) ) . '</a>',
		'{terminos}'   => '<a href="' . esc_url( pm_url_terminos( $i ) ) . '">' . esc_html( pm_t( 'legal.terminos.largo' ) ) . '</a>',
	];
	return strtr( esc_html( $texto ), $enlaces );
}
