<?php
/**
 * PageSpeed Ninja
 *
 * @link              http://pagespeed.ninja
 * @wordpress-plugin
 * Plugin Name:       PageSpeed Ninja
 * Plugin URI:        http://pagespeed.ninja
 * Description:       The quickest and most advanced performance plugin. Make your site super fast and fix PageSpeed issues with one click! Try different settings to find the best set of options for your site.
 * Version:           0.9.40
 * Author:            PageSpeed Ninja
 * Author URI:        https://wordpress.org/support/users/pagespeed/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       psn-pagespeed-ninja
 */

defined( 'WPINC' ) || die;

// optional error logging
include_once plugin_dir_path( __FILE__ ) . 'includes/class-pagespeedninja-errorlogging.php';
PagespeedNinja_ErrorLogging::init();

require plugin_dir_path( __FILE__ ) . 'includes/class-pagespeedninja.php';

function run_pagespeedninja() {
    $plugin = new PagespeedNinja();
    register_activation_hook(__FILE__, array($plugin, 'activate') );
    register_deactivation_hook(__FILE__, array($plugin, 'deactivate') );
    $plugin->run();
}

run_pagespeedninja();
