<?php
/**
 * @package Dynamic_Copyright_Year_Shortcode
 * @version 0.1.1
 */

/*
Plugin Name: Dynamic Copyright Year Shortcode
Plugin URI: http://wordpress.org/plugins/dynamic-copyright-year-shortcode/
Description: This plugin will assist you to keep your website copyright date updated by adding the current year where you use the shortcode. 
Author: Deepak Kumar Vellingiri
Version: 0.1
Author URI: http://www.dwebsight.com/about-deepak/
*/

defined( 'ABSPATH' ) or die( 'Direct access prohibited!' );

add_action( 'plugins_loaded', 'dynamic_copyright_year_shortcode_load_textdomain' );
/**
 * Load plugin textdomain.
 *
 * @since 0.1.1
 */
function dynamic_copyright_year_shortcode_load_textdomain() {
  load_plugin_textdomain( 'dynamic-copyright-year-shortcode', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' ); 
}

/**
 * [current_year]
 * 
 * Returns the Current Year as a string in four digits.
 */

function get_current_year() {
	$current_date = getdate();
	return '
		<div class="dcywrapper">
			<div class="dcycontainer">
				<span class=="dcyitem">
					'.$current_date[year].'
				</span>
			</div>
		</div>';
	}
	
add_shortcode('current_year', 'get_current_year');

/**
 * [current_year_with_copyright_symbol]
 * 
 * Returns the Current Year with a copyright symbol
 */
function get_current_year_with_copyright_symbol() {
	$current_date = getdate();
	return '
		<div class="dcywrapper">
			<div class="dcycontainer">
				<span class=="dcyitem">
					&copy '.$current_date[year].'
				</span>
			</div>
		</div>';
	}
	
add_shortcode('current_year_with_copyright_symbol', 'get_current_year_with_copyright_symbol');

/**
 * [current_year_with_copyright_symbol_and_title]
 * 
 * Returns the Current Year with a copyright symbol with site title
 */
function get_current_year_with_copyright_symbol_and_title() {
	$current_date = getdate();
	$site_title = get_bloginfo( 'name' );
	return '
		<div class="dcywrapper">
			<div class="dcycontainer">
				<span class=="dcyitem">
				'. $site_title .' &copy '.$current_date[year] .'
				</span>
			</div>
		</div>';
	}
	
add_shortcode('current_year_with_copyright_symbol_and_title', 'get_current_year_with_copyright_symbol_and_title');

