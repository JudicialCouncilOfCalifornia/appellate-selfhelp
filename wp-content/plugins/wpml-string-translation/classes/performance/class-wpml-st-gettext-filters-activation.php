<?php
/**
 * WPML_ST_Gettext_Filters_Activation class file.
 *
 * @package WPML\ST
 */

use WPML\ST\Package\Domains;

/**
 * Class WPML_ST_Gettext_Filters_Activation
 */
class WPML_ST_Gettext_Filters_Activation {
	const ALL_STRINGS_ARE_IN_ENGLISH_OPTION = 'wpml-st-all-strings-are-in-english';

	/**
	 * Instance of WPML Core.
	 *
	 * @var SitePress
	 */
	private $sitepress;

	/** @var Domains */
	private $domains;

	/**
	 * All source strings are in English.
	 *
	 * @var bool
	 */
	private $all_strings_are_in_english;

	/**
	 * WPML_ST_Gettext_Filters_Activation constructor.
	 *
	 * @param SitePress $sitepress Instance of WPML Core.
	 */
	public function __construct( SitePress $sitepress, Domains $domains ) {
		$this->sitepress                  = $sitepress;
		$this->domains                    = $domains;
		$this->all_strings_are_in_english = $this->get_all_strings_are_in_english();
	}

	/**
	 * Get value of "all string are in English".
	 *
	 * @return bool
	 */
	public function are_all_strings_in_english() {
		return $this->all_strings_are_in_english;
	}

	/**
	 * Decide if gettext filters should be turned on.
	 *
	 * @param string|null $lang Language.
	 * @param string|null $domain
	 *
	 * @return bool
	 */
	public function should_be_turned_on( $lang = null, $domain = null ) {
		$lang = $lang ? $lang : $this->get_current_language();

		$sitepress_settings = $this->sitepress->get_settings();

		return ( 'en' !== $lang || ! $this->all_strings_are_in_english || $this->domains->isPackage( $domain ) )
			&& isset( $sitepress_settings['setup_complete'] ) && $sitepress_settings['setup_complete'];
	}

	/**
	 * Get current language.
	 *
	 * @return bool|mixed|string|null
	 */
	public function get_current_language() {
		if ( is_admin() && ! $this->is_ajax_request_coming_from_frontend() ) {
			$current_lang = $this->sitepress->get_admin_language();
		} else {
			$current_lang = $this->sitepress->get_current_language();
		}

		if ( ! $current_lang ) {
			$current_lang = $this->sitepress->get_default_language();
			if ( ! $current_lang ) {
				$current_lang = 'en';
			}
		}

		return $current_lang;
	}

	/**
	 * Get value of  "all string are in English".
	 */
	protected function get_all_strings_are_in_english() {
		return get_option( self::ALL_STRINGS_ARE_IN_ENGLISH_OPTION );
	}

	/**
	 * Is ajax call.
	 *
	 * @return bool
	 */
	protected function is_ajax() {
		return defined( 'DOING_AJAX' ) && DOING_AJAX;
	}

	/**
	 * Is ajax request coming from frontend.
	 *
	 * @return bool
	 */
	protected function is_ajax_request_coming_from_frontend() {
		if ( ! $this->is_ajax() ) {
			return false;
		}

		if ( ! isset( $_SERVER['HTTP_REFERER'] ) ) {
			return false;
		}

		return false === strpos( filter_var( wp_unslash( $_SERVER['HTTP_REFERER'] ), FILTER_SANITIZE_STRING ), admin_url() );
	}
}
