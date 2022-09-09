<?php

class WPML_ST_Script_Translations_Hooks_Factory implements IWPML_Backend_Action_Loader, IWPML_Frontend_Action_Loader {

	/**
	 * Create hooks.
	 *
	 * @return array|IWPML_Action
	 * @throws \Auryn\InjectionException Auryn Exception.
	 */
	public function create() {
		global $wpdb;

		$hooks = array();

		$filesystem = wpml_get_filesystem_direct();

		$jed_file_manager = new WPML_ST_JED_File_Manager(
			WPML\Container\make( 'WPML_ST_JED_Strings_Retrieve' ),
			new WPML_ST_JED_File_Builder(),
			$filesystem,
			new WPML_Language_Records( $wpdb )
		);

		$hooks['update'] = $this->get_update_hooks( $jed_file_manager );

		if ( ! wpml_is_ajax() && ! wpml_is_rest_request() ) {
			$hooks['filtering'] = $this->get_filtering_hooks( $filesystem, $jed_file_manager );
		}

		return $hooks;
	}

	/**
	 * @param WPML_ST_JED_File_Manager $jed_file_manager
	 *
	 * @return WPML_ST_JED_File_Update_Hooks
	 */
	private function get_update_hooks( $jed_file_manager ) {
		global $wpdb;

		return new WPML_ST_JED_File_Update_Hooks(
			$jed_file_manager,
			new WPML_ST_JED_Locales_Domains_Mapper( $wpdb )
		);
	}

	/**
	 * @param WP_Filesystem_Direct $filesystem
	 * @param WPML_ST_JED_File_Manager $jed_file_manager
	 *
	 * @return WPML_ST_Script_Translations_Hooks
	 */
	private function get_filtering_hooks( $filesystem, $jed_file_manager ) {
		global $wpdb;

		$files_dictionary = new WPML_ST_Translations_File_Dictionary(
			new WPML_ST_Translations_File_Dictionary_Storage_Table( $wpdb )
		);

		$wpml_file = new WPML_File( new WPML_WP_API(), $filesystem );

		return new WPML_ST_Script_Translations_Hooks( $files_dictionary, $jed_file_manager, $wpml_file );
	}

}
