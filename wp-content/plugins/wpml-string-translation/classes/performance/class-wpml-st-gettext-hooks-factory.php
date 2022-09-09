<?php
/**
 * WPML_ST_Gettext_Hooks_Factory class file.
 *
 * @package WPML\ST
 */

/**
 * Class WPML_ST_Gettext_Hooks_Factory
 */
class WPML_ST_Gettext_Hooks_Factory {

	/**
	 * String Translation instance.
	 *
	 * @var WPML_String_Translation
	 */
	private $string_translation;

	/**
	 * WPML_ST_Gettext_Filters_Activation instance.
	 *
	 * @var WPML_ST_Gettext_Filters_Activation
	 */
	private $gettext_filters_activation;

	/**
	 * Translate with String Translation.
	 *
	 * @var bool
	 */
	private $translate_with_st;

	/**
	 * WPML_ST_Gettext_Hooks_Factory constructor.
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
		$this->translate_with_st          = $translate_with_st;
	}

	/**
	 * Create WPML_ST_Gettext_Hooks instance.
	 *
	 * @return WPML_ST_Gettext_Hooks
	 */
	public function create() {
		return new WPML_ST_Gettext_Hooks(
			$this->string_translation,
			$this->gettext_filters_activation,
			$this->translate_with_st
		);
	}
}
