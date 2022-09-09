<?php
/**
 * WPML_Displayed_String_Filter class file.
 *
 * @package WPML\ST
 */

/**
 * Class WPML_Displayed_String_Filter
 *
 * Handles all string translating when rendering translated strings to the user, unless auto-registering is
 * active for strings.
 */
class WPML_Displayed_String_Filter {
	/**
	 * WPML Core instance.
	 *
	 * @var SitePress
	 */
	protected $sitepress;

	/**
	 * Language.
	 *
	 * @var string
	 */
	protected $language;

	/**
	 * ST DB Cache Factory.
	 *
	 * @var WPML_ST_DB_Cache_Factory
	 */
	protected $db_cache_factory;

	/**
	 * ST DB Cache.
	 *
	 * @var WPML_ST_DB_Cache
	 */
	protected $db_cache;

	/**
	 * WPML_Displayed_String_Filter constructor.
	 *
	 * @param SitePress                     $sitepress        WPML Core instance.
	 * @param string                        $language         Language.
	 * @param null|object                   $existing_filter  Existing filter (deprecated).
	 * @param null|WPML_ST_DB_Cache_Factory $db_cache_factory ST DB Cache Factory.
	 *
	 * @throws \Auryn\InjectionException Auryn Exception.
	 */
	public function __construct( $sitepress, $language, $existing_filter = null, $db_cache_factory = null ) {
		$this->sitepress = $sitepress;
		$this->language  = $language;

		if ( $db_cache_factory instanceof WPML_ST_DB_Cache_Factory ) {
			$this->db_cache_factory = $db_cache_factory;
		} else {
			$this->db_cache_factory = new WPML_ST_DB_Cache_Factory();
		}

		$this->db_cache = $this->db_cache_factory->create( $language );
	}

	/**
	 * Clear cache.
	 */
	public function clear_cache() {
		$this->db_cache->clear_cache();
	}

	/**
	 * Translate by name and context.
	 *
	 * @param string       $untranslated_text Untranslated text.
	 * @param string       $name              Name of the string.
	 * @param string|array $context           Context.
	 * @param null|boolean $has_translation   If string has translation.
	 *
	 * @return string
	 */
	public function translate_by_name_and_context( $untranslated_text, $name, $context = '', &$has_translation = null ) {
		$translation = $this->get_translation( $untranslated_text, $name, $context );

		if ( $translation ) {
			$res             = $translation->get_value();
			$has_translation = $translation->has_translation();
		} else {
			$res             = $untranslated_text;
			$has_translation = false;
		}

		return $res;
	}

	/**
	 * Transform translation parameters.
	 *
	 * @param string       $name    Name of the string.
	 * @param string|array $context Context.
	 *
	 * @return array
	 */
	protected function transform_parameters( $name, $context ) {
		list ( $domain, $gettext_context ) = wpml_st_extract_context_parameters( $context );

		return array( $name, $domain, $gettext_context );
	}

	/**
	 * Truncates a string to the maximum string table column width.
	 *
	 * @param string $string String to translate.
	 *
	 * @return string
	 */
	protected function truncate_long_string( $string ) {
		return strlen( $string ) > WPML_STRING_TABLE_NAME_CONTEXT_LENGTH
			? mb_substr( $string, 0, WPML_STRING_TABLE_NAME_CONTEXT_LENGTH )
			: $string;
	}

	/**
	 * Get translation of the string.
	 *
	 * @param string       $untranslated_text Untranslated text.
	 * @param string       $name              Name of the string.
	 * @param string|array $context           Context.
	 *
	 * @return WPML_ST_Page_Translation|null
	 */
	protected function get_translation( $untranslated_text, $name, $context ) {
		list ( $name, $domain, $gettext_context ) = $this->transform_parameters( $name, $context );
		$untranslated_text = is_string( $untranslated_text ) ? $untranslated_text : '';

		$translation = $this->db_cache->get_translation( $name, $domain, $untranslated_text, $gettext_context );

		if ( ! $translation ) {
			list( $name, $domain ) = array_map( array( $this, 'truncate_long_string' ), array( $name, $domain ) );
			$translation = $this->db_cache->get_translation( $name, $domain, $untranslated_text, $gettext_context );
		}

		return $translation;
	}
}
