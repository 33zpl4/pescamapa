<?php
/**
 * Esquina (draft #38), 20/10/2026: regulation data from the owner's research
 * (licence process and categories, río Corriente sport-only rule, pacú
 * minimum size) and the generic closure wording agreed for every guide (no
 * dates or km to chase). Does NOT set "reglamento verificado": that date goes
 * in when someone reads the official page, per the protocol in docs/estado.md.
 * Run with wp-script.yml (script = esquina-reglamento-2026-10-20).
 *
 * Replaces the regulation summary; replaces the licence and closure FAQs by
 * question; adds the pacú FAQ once.
 */

$p = get_posts( [ 'post_type' => 'pm_destino', 'post_status' => 'any', 'meta_key' => 'clave', 'meta_value' => 'esquina', 'numberposts' => 1 ] )[0] ?? null;
if ( ! $p || 'es' !== pm_idioma_de( $p->ID ) ) {
	WP_CLI::error( 'No encontré Esquina en español.' );
}
$id = $p->ID;

update_field( 'reglamento_resumen', 'Para pescar en Corrientes hace falta licencia de pesca deportiva. Se saca online en el portal de Recursos Naturales: completás el formulario con tu DNI, pagás por pasarela digital y recibís la credencial con código QR, que podés mostrar en el celular junto con tu DNI. Hay categorías de costa y embarcada, y según residencia (correntinos, resto del país, extranjeros); jubilados, pensionados y menores de 12 años tramitan una exención sin costo. En el río Corriente y los riachos de su delta solo se permite la pesca deportiva con caña o línea de mano: nada de redes ni espineles. El dorado es Monumento Natural Provincial (Ley 6730) y se pesca con devolución. El pacú tiene medida mínima de 45 cm, y los operadores locales recomiendan devolverlo en arroyos y esteros. Las vedas por desove (o el régimen de días fijos por semana) las fija cada año Recursos Naturales: si viajás entre noviembre y enero, consultá la disposición vigente antes de salir.', $id );

$nuevas = [
	'¿Hace falta licencia?' => 'Sí. Se saca online en el portal de Recursos Naturales de Corrientes con tu DNI; pagás por pasarela digital y recibís una credencial con QR que vale en el celular (llevá también el DNI). Hay categorías de costa y embarcada, para correntinos, resto del país y extranjeros. Jubilados, pensionados y menores de 12 años tramitan una exención sin costo. Si sos de Santa Fe, Entre Ríos o Chaco, mirá el acuerdo regional de reconocimiento recíproco.',
	'¿Qué fechas abarca la veda en Corrientes?' => 'Cambian cada año: Recursos Naturales fija las vedas por desove (o un régimen de días fijos por semana) según los informes de cada primavera. Si viajás entre noviembre y enero, consultá la disposición vigente con tu guía o en el portal oficial antes de salir.',
	'¿Me puedo llevar un pacú?' => 'La medida mínima es 45 cm y solo se pesca con caña o reel (mosca, baitcasting o carnada vegetal como frutos de ingá). En arroyos y esteros los operadores locales recomiendan devolverlo para cuidar el lugar.',
];
$faqs = pm_filas( 'faqs', $id );
foreach ( $faqs as &$f ) {
	if ( isset( $nuevas[ $f['pregunta'] ] ) ) {
		$f['respuesta'] = $nuevas[ $f['pregunta'] ];
		unset( $nuevas[ $f['pregunta'] ] );
	}
}
unset( $f );
foreach ( $nuevas as $q => $a ) {
	$faqs[] = [ 'pregunta' => $q, 'respuesta' => $a ];
}
update_field( 'faqs', $faqs, $id );
update_post_meta( $id, '_pm_es_editado', time() );

WP_CLI::success( "Esquina #$id ({$p->post_status}): reglamento y preguntas actualizados. La fecha de verificación queda vacía." );
