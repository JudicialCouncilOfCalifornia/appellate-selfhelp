<?php
/**
 * Gettext functions.
 *
 * @package WPML\ST
 */

namespace WPML\ST\Gettext;

use WPML_ST_Gettext_Filters_Activation, WPML, Auryn;

/**
 * Decide if gettext filters should be turned on.
 *
 * @param string $lang Language.
 *
 * @return mixed
 * @throws Auryn\InjectionException Auryn Exception.
 */
function should_filters_be_turned_on( $lang = null ) {
	/**
	 * Instance of WPML_ST_Gettext_Filters_Activation
	 *
	 * @var WPML_ST_Gettext_Filters_Activation
	 */
	static $gettext_filters_activation;

	$gettext_filters_activation = $gettext_filters_activation ?: WPML\Container\make( '\WPML_ST_Gettext_Filters_Activation' );

	return $gettext_filters_activation->should_be_turned_on( $lang );
}
