<?php
/**
 * WPML_ST_Theme_Localization_Type class file.
 *
 * @package WPML\ST
 */

/**
 * Class WPML_ST_Theme_Localization_Type
 */
class WPML_ST_Theme_Localization_Type {

	const USE_ST_AND_NO_MO_FILES = 3;

	/**
	 * Themes and Plugins Settings Instance.
	 *
	 * @var WPML_ST_Themes_And_Plugins_Settings
	 */
	private $themes_and_plugins_settings;

	/**
	 * ST DB Cache Factory instance.
	 *
	 * @var WPML_ST_DB_Cache_Factory
	 */
	private $st_db_cache_factory;

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		add_action( 'wpml_post_save_theme_localization_type', array( $this, 'save_scaning_alert_settings' ) );
		add_action( 'wpml_post_save_theme_localization_type', array( $this, 'clear_st_cache' ) );
		add_action( 'wpml_st_auto_register_default', array( $this, 'save_setting_to_not_load_mo' ) );
	}

	/**
	 * Save scanning alert settings.
	 */
	public function save_scaning_alert_settings() {
		$themes_and_plugins_settings  = $this->get_themes_and_plugins_settings();
		$display_strings_scan_notices = false;
		if ( array_key_exists( 'wpml_st_display_strings_scan_notices', $_POST ) ) {
			$display_strings_scan_notices = filter_var( $_POST['wpml_st_display_strings_scan_notices'], FILTER_VALIDATE_BOOLEAN );
		}
		$themes_and_plugins_settings->set_strings_scan_notices( $display_strings_scan_notices );
	}

	/**
	 * Clear ST Cache.
	 *
	 * @throws \Auryn\InjectionException Auryn Exception.
	 */
	public function clear_st_cache() {
		$factory = $this->get_st_db_cache_factory();
		$persist = $factory->create_persist();
		$persist->clear_cache();
	}

	/**
	 * Get ST DB Cache Factory.
	 *
	 * @return WPML_ST_DB_Cache_Factory
	 */
	public function get_st_db_cache_factory() {
		if ( ! $this->st_db_cache_factory ) {
			$this->st_db_cache_factory = new WPML_ST_DB_Cache_Factory();
		}

		return $this->st_db_cache_factory;
	}

	/**
	 * Set ST DB Cache Factory.
	 *
	 * @param WPML_ST_DB_Cache_Factory $st_db_cache_factory ST DB Cache Factory instance.
	 *
	 * @return $this
	 */
	public function set_st_db_cache_factory( WPML_ST_DB_Cache_Factory $st_db_cache_factory ) {
		$this->st_db_cache_factory = $st_db_cache_factory;

		return $this;
	}

	/**
	 * Get themes and plugins settings.
	 *
	 * @return WPML_ST_Themes_And_Plugins_Settings
	 */
	public function get_themes_and_plugins_settings() {
		if ( ! $this->themes_and_plugins_settings ) {
			$this->themes_and_plugins_settings = new WPML_ST_Themes_And_Plugins_Settings();
		}

		return $this->themes_and_plugins_settings;
	}

	/**
	 * Set themes and plugins settings.
	 *
	 * @param WPML_ST_Themes_And_Plugins_Settings $themes_and_plugins_settings Themes and Plugins Settings Instance.
	 *
	 * @return $this
	 */
	public function set_themes_and_plugins_settings( WPML_ST_Themes_And_Plugins_Settings $themes_and_plugins_settings ) {
		$this->themes_and_plugins_settings = $themes_and_plugins_settings;

		return $this;
	}

	/**
	 * Save settings to not load .mo file.
	 */
	public function save_setting_to_not_load_mo() {
		global $sitepress;

		$sitepress->set_setting( 'theme_localization_type', self::USE_ST_AND_NO_MO_FILES );
		$sitepress->save_settings();
	}
}
