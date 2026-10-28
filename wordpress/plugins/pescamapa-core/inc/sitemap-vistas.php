<?php
/** Sitemap of the views that have no post: home, map, credits, interviews (and notes, once there are any) × languages. */

defined( 'ABSPATH' ) || exit;

class PM_Sitemap_Vistas extends WP_Sitemaps_Provider {
	public function __construct() {
		$this->name        = 'vistas';
		$this->object_type = 'vistas';
	}

	public function get_url_list( $page_num, $object_subtype = '' ) {
		$out = [];
		$vistas = [ 'pm_url_inicio', 'pm_url_mapa', 'pm_url_pique', 'pm_url_viaje', 'pm_url_terminos', 'pm_url_privacidad', 'pm_url_rio', 'pm_url_especies', 'pm_url_entrevistas' ];
		if ( pm_hay_notas() ) {
			$vistas[] = 'pm_url_notas';
		}
		foreach ( $vistas as $fn ) {
			foreach ( pm_idiomas() as $i ) {
				$out[] = [ 'loc' => $fn( $i ) ];
			}
		}
		foreach ( pm_rio_datos()['destinos'] ?? [] as $clave => $x ) {
			if ( ! empty( $x['rio'] ) && isset( pm_rio_config()[ $clave ] ) ) {
				foreach ( pm_idiomas() as $i ) {
					$out[] = [ 'loc' => pm_url_rio_punto( $clave, $i ) ];
				}
			}
		}
		foreach ( pm_posts( 'pm_destino' ) as $p ) {
			if ( pm_paquetes( $p->ID ) ) {
				$out[] = [ 'loc' => pm_url_paquetes( pm_clave_de( $p->ID ), pm_idioma_de( $p->ID ) ) ];
			}
		}
		return $out;
	}

	public function get_max_num_pages( $object_subtype = '' ) {
		return 1;
	}
}
