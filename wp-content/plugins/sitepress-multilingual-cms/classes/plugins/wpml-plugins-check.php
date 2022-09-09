<?php

class WPML_Plugins_Check {
	/**
	 * @param string $bundle_json
	 * @param string $tm_version
	 * @param string $st_version
	 */
	public static function disable_outdated(
		$bundle_json,
		$tm_version,
		$st_version
	) {
		$required_versions = json_decode( $bundle_json, true );

		if ( version_compare( $tm_version, $required_versions['wpml-translation-management'], '<' ) ) {
			remove_action( 'wpml_loaded', 'wpml_tm_load', 10 );
		}

		if ( version_compare( $st_version, $required_versions['wpml-string-translation'], '<' ) ) {
			remove_action( 'wpml_before_init', 'load_wpml_st_basics' );
		}
	}
}
