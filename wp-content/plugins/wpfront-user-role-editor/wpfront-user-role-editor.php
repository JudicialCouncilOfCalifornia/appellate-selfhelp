<?php

/*
 * Plugin Name: WPFront User Role Editor
 * Plugin URI: http://wpfront.com/user-role-editor-pro/ 
 * Description: Allows you to manage your site's security using user role permissions.
 * Version: 3.2.1.11184
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Syam Mohan
 * Author URI: http://wpfront.com
 * License: GPL v3 
 * Text Domain: wpfront-user-role-editor
 * Domain Path: /languages
 */

/*
  WPFront User Role Editor Plugin
  Copyright (C) 2014, WPFront.com
  Website: wpfront.com
  Contact: syam@wpfront.com

  WPFront User Role Editor Plugin is distributed under the GNU General Public License, Version 3,
  June 2007. Copyright (C) 2007 Free Software Foundation, Inc., 51 Franklin
  St, Fifth Floor, Boston, MA 02110, USA

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
  ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
  ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
  ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor')) {

    class WPFront_User_Role_Editor {

        const VERSION = '3.2.1.11184';
        const PLUGIN_SLUG = 'wpfront-user-role-editor';

        protected static $instance = null;
        protected $plugin_url = null;
        protected $plugin_dir = null;
        protected $includes_dir = null;
        protected $plugin_basename = null;
        protected $plugin_file = null;
        protected $parent_menu_slug = null;

        protected function __construct() {
            $this->plugin_url = plugin_dir_url(__FILE__);
            $this->plugin_dir = plugin_dir_path(__FILE__);
            $this->includes_dir = trailingslashit($this->plugin_dir . 'includes');
            $this->plugin_basename = plugin_basename(__FILE__);
            $this->plugin_file = __FILE__;
        }

        /**
         * Singleton instance.
         * 
         * @return WPFront_User_Role_Editor
         */
        public static function instance() {
            if (self::$instance === null) {
                self::$instance = new WPFront_User_Role_Editor();
            }

            return self::$instance;
        }

        /**
         * Hooks into plugins_loaded.
         * Loads controller files and fires wpfront_ure_init.
         */
        public static function init() {
            add_action('plugins_loaded', array(self::instance(), 'plugins_loaded'));
            self::instance()->includes();
            add_action('admin_enqueue_scripts', array(self::instance(), 'admin_enqueue_styles'));
        }

        public function plugins_loaded() {
            load_plugin_textdomain('wpfront-user-role-editor', false, basename($this->plugin_dir) . '/languages/');
        }

        /**
         * Loads controller files.
         */
        protected function includes() {
            require_once $this->includes_dir . 'class-uninstall.php';
            require_once $this->includes_dir . 'class-roles-helper.php';
            require_once $this->includes_dir . 'class-utils.php';
            require_once $this->includes_dir . 'class-cache.php';
            require_once $this->includes_dir . 'class-entity.php';
            require_once $this->includes_dir . 'class-controller.php';
            require_once $this->includes_dir . 'settings/class-options.php';
            require_once $this->includes_dir . 'class-debug.php';
            require_once $this->includes_dir . 'users/class-assign-migrate.php';
            require_once $this->includes_dir . 'users/class-user-profile.php';
            require_once $this->includes_dir . 'roles/class-roles-list.php';
            require_once $this->includes_dir . 'roles/class-role-add-edit.php';
            require_once $this->includes_dir . 'restore/class-restore.php';
            require_once $this->includes_dir . 'login-redirect/class-login-redirect.php';
            require_once $this->includes_dir . 'bulk-edit/class-bulk-edit.php';
            require_once $this->includes_dir . 'add-remove-cap/class-add-remove-cap.php';
            require_once $this->includes_dir . 'nav-menu/class-nav-menu-permissions.php';
            require_once $this->includes_dir . 'widget/class-widget-permissions.php';
            require_once $this->includes_dir . 'users/class-user-permissions.php';
            require_once $this->includes_dir . 'media/class-media-permissions.php';
            require_once $this->includes_dir . 'shortcodes/class-shortcodes.php';
            require_once $this->includes_dir . 'post-type/class-post-type.php';
            require_once $this->includes_dir . 'taxonomies/class-taxonomies.php';
            require_once $this->includes_dir . 'wp/includes.php';
            require_once $this->includes_dir . 'go-pro/class-go-pro.php';

            require_once $this->includes_dir . 'integration/plugins/class-wpfront-user-role-editor-plugin-integration.php';


            if (file_exists($this->includes_dir . 'ppro/includes.php')) {
                require_once $this->includes_dir . 'ppro/includes.php';
            }

            if (file_exists($this->includes_dir . 'bpro/includes.php')) {
                require_once $this->includes_dir . 'bpro/includes.php';
            }
        }

        /**
         * Returns parent menu slug for sub menu items.
         * Also adds the parent menu on the very first call.
         * 
         * @param string $submenu_slug
         * @param string $submenu_capability
         * @return string
         */
        public function get_parent_menu_slug($submenu_slug, $submenu_capability) {
            if ($this->parent_menu_slug == null) {
                $this->parent_menu_slug = $submenu_slug;
                if (is_network_admin()) {
                    $position = 9;
                } else {
                    $position = 69;
                }
                add_menu_page(__('Roles', 'wpfront-user-role-editor'), __('Roles', 'wpfront-user-role-editor'), $submenu_capability, $submenu_slug, null, 'dashicons-groups', $position);
            }

            return $this->parent_menu_slug;
        }

        /**
         * Returns the includes directory path.
         * 
         * @return string
         */
        public function get_includes_dir() {
            return $this->includes_dir;
        }

        /**
         * Returns the plugin directory path.
         * 
         * @return string
         */
        public function get_plugin_dir() {
            return $this->plugin_dir;
        }

        /**
         * Returns the url of the asset passed.
         * Passed path should be relative to assets directory.
         * 
         * @param string $relativePath
         * @return string
         */
        public function get_asset_url($relativePath) {
            return $this->plugin_url . 'assets/' . $relativePath;
        }

        /**
         * Returns the plugin base name.
         * 
         * @return string
         */
        public function get_plugin_basename() {
            return $this->plugin_basename;
        }

        /**
         * Returns the plugin file.
         * 
         * @return string
         */
        public function get_plugin_file() {
            return $this->plugin_file;
        }

        /**
         * WP die with a permission denied message.
         */
        public function permission_denied() {
            wp_die(
                    __('You do not have sufficient permissions to access this page.', 'wpfront-user-role-editor'),
                    __('Access Denied', 'wpfront-user-role-editor'),
                    array('response' => 403, 'back_link' => true)
            );
        }

        /**
         * Hooks into admin_enqueue_scripts and enqueues wp-admin styles.
         */
        public function admin_enqueue_styles() {
            wp_enqueue_style('wpfront-user-role-editor-admin-css', $this->get_asset_url('css/admin.css'), array(), self::VERSION);
        }

    }

    WPFront_User_Role_Editor::init();
}

