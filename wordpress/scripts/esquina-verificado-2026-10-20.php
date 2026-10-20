<?php
/**
 * Esquina: the owner's team verified the regulation on 20/10/2026. Sets the
 * (optional) "Reglamento verificado el" date on the Spanish guide.
 * Run with wp-script.yml (script = esquina-verificado-2026-10-20).
 */

$p = pm_buscar( 'pm_destino', 'esquina', 'es' );
if ( ! $p ) {
	WP_CLI::error( 'No encontré Esquina (es) publicada.' );
}
update_field( 'reglamento_verificado', '20261020', $p->ID );
WP_CLI::success( "Esquina #{$p->ID}: reglamento verificado el 20/10/2026." );
