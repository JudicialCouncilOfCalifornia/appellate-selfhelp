<?php
/**
 * WPML_ST_DB_Cache_Factory class file.
 *
 * @package WPML\ST
 */

use WPML\Utilities\Lock;

/**
 * Class WPML_ST_DB_Cache_Factory
 */
class WPML_ST_DB_Cache_Factory {

	/**
	 * WordPress environment instance.
	 *
	 * @var WP
	 */
	private $wp;

	/**
	 * WPML_ST_DB_Cache_Factory constructor.
	 *
	 * @param WP $wp WordPress environment instance.
	 */
	public function __construct( $wp = null ) {
		if ( ! $wp ) {
			global $wp;
			if ( ! $wp ) {
				// phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
				$GLOBALS['wp_rewrite'] = new WP_Rewrite();
				$wp                    = new WP();
				$GLOBALS['wp']         = $wp;
				// phpcs:enable WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}
		$this->wp = $wp;
	}

	/**
	 * Create db cache.
	 *
	 * @param string $language Language.
	 *
	 * @return WPML_ST_DB_Cache
	 * @throws Auryn\InjectionException Auryn Exception.
	 */
	public function create( $language ) {
		global $wpdb;

		$persist = $this->create_persist();

		$retriever = WPML\Container\make(
			'WPML_ST_DB_Translation_Retrieve',
			[ ':lock' => new Lock( $wpdb, WPML_ST_DB_Cache::class ) ]
		);

		$url_preprocessor = new WPML_ST_Page_URL_Preprocessor( new WPML_ST_WP_Wrapper( $this->wp ) );

		return new WPML_ST_DB_Cache(
			$language,
			$persist,
			$retriever,
			$url_preprocessor,
			new WPML_ST_DB_Shutdown_Url_Validator( $this->wp )
		);
	}

	/**
	 * Create persistent cache.
	 *
	 * @return WPML_ST_Page_Translations_Cached_Persist
	 * @throws Auryn\InjectionException Auryn Exception.
	 */
	public function create_persist() {
		$db_persist     = WPML\Container\make( 'WPML_ST_Page_Translations_Persist' );
		$cache          = new WPML_WP_Cache( WPML_ST_Page_Translations_Cached_Persist::CACHE_GROUP );
		$cached_persist = new WPML_ST_Page_Translations_Cached_Persist( $db_persist, $cache );

		return $cached_persist;
	}
}
