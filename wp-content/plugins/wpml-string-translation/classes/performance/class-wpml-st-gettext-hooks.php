<?php
/**
 * WPML_ST_Gettext_Hooks class file.
 *
 * @package WPML\ST
 */

/**
 * Class WPML_ST_Gettext_Hooks
 */
class WPML_ST_Gettext_Hooks {
	/**
	 * String Translation instance.
	 *
	 * @var WPML_String_Translation
	 */
	private $string_translation;

	/**
	 * WPML_ST_Gettext_Filters_Activation Instance.
	 *
	 * @var WPML_ST_Gettext_Filters_Activation
	 */
	private $gettext_filters_activation;

	/**
	 * Current language.
	 *
	 * @var string
	 */
	private $current_lang;

	/**
	 * Initial language.
	 *
	 * @var string
	 */
	private $initial_language;

	/**
	 * All strings are in English.
	 *
	 * @var bool
	 */
	private $all_strings_are_in_english;

	/**
	 * Translate with String Translation.
	 *
	 * @var bool
	 */
	private $translate_with_st;

	/**
	 * Class filters.
	 *
	 * @var array
	 */
	private $filters = array();

	/**
	 * Class hooks.
	 *
	 * @var array
	 */
	private $hooks = array(
		array( 'gettext', 'icl_sw_filters_gettext', 9, 3 ),
		array( 'gettext_with_context', 'icl_sw_filters_gettext_with_context', 1, 4 ),
		array( 'ngettext', 'icl_sw_filters_ngettext', 9, 5 ),
		array( 'ngettext_with_context', 'icl_sw_filters_nxgettext', 9, 6 ),
	);

	/**
	 * WPML_ST_Gettext_Hooks constructor.
	 *
	 * @param WPML_String_Translation            $string_translation         String Translation instance.
	 * @param WPML_ST_Gettext_Filters_Activation $gettext_filters_activation WPML_ST_Gettext_Filters_Activation
	 *                                                                       instance.
	 * @param boolean                            $translate_with_st          Translate with String Translation.
	 */
	public function __construct(
		WPML_String_Translation $string_translation,
		WPML_ST_Gettext_Filters_Activation $gettext_filters_activation,
		$translate_with_st
	) {
		$this->string_translation         = $string_translation;
		$this->gettext_filters_activation = $gettext_filters_activation;
		$this->initial_language           = $this->gettext_filters_activation->get_current_language();
		$this->current_lang               = $this->initial_language;
		$this->all_strings_are_in_english = $this->gettext_filters_activation->are_all_strings_in_english();
		$this->translate_with_st          = $translate_with_st;
	}

	/**
	 * Init hooks.
	 */
	public function init_hooks() {
		if ( ! $this->translate_with_st ) {
			return;
		}

		if ( $this->all_strings_are_in_english ) {
			add_action( 'wpml_language_has_switched', array( $this, 'switch_language_hook' ), 10, 1 );
		}

		if ( $this->gettext_filters_activation->should_be_turned_on() ) {
			add_action( 'plugins_loaded', array( $this, 'init_gettext_hooks' ), 2 );
		}
	}

	/**
	 * Init gettext hooks.
	 */
	public function init_gettext_hooks() {
		foreach ( $this->hooks as $hook ) {
			call_user_func_array( 'add_filter', $hook );
		}
	}

	/**
	 * Switch language hook.
	 *
	 * @param string $lang Language.
	 */
	public function switch_language_hook( $lang ) {
		if ( $this->string_translation->should_use_admin_language() ) {
			$this->current_lang = $this->string_translation->get_admin_language();
		} elseif ( $lang ) {
			$this->current_lang = $lang;
		} else {
			$this->current_lang = $this->initial_language;
		}

		if ( $this->gettext_filters_activation->should_be_turned_on( $this->current_lang ) ) {
			$this->init_gettext_hooks();
		} else {
			$this->remove_hooks();
		}
	}

	/**
	 * Remove hooks.
	 */
	private function remove_hooks() {
		foreach ( $this->hooks as $hook ) {
			array_pop( $hook );
			call_user_func_array( 'remove_filter', $hook );
		}
	}

	/**
	 * Get filter.
	 *
	 * @param string|null $lang Language.
	 * @param string|null $name Language name.
	 *
	 * @return WPML_Displayed_String_Filter|null
	 */
	public function get_filter( $lang = null, $name = null ) {
		if ( ! $lang ) {
			$lang = $this->current_lang;
			if ( ! $this->all_strings_are_in_english ) {
				$lang = $this->string_translation->get_current_string_language( $name );
			}
		}

		if ( ! $lang ) {
			return null;
		}

		if ( ! ( array_key_exists( $lang, $this->filters ) && $this->filters[ $lang ] ) ) {
			$this->filters[ $lang ] = $this->string_translation->get_string_filter( $lang );
		}

		return $this->filters[ $lang ];
	}

	/**
	 * Clear filters.
	 */
	public function clear_filters() {
		$this->filters = array();
	}
}
