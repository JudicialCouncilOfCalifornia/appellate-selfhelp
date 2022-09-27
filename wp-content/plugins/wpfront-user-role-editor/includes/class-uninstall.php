<?php
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

/**
 * Cache for WPFront User Role Editor
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_Uninstall')) {

    /**
     * Uninstall class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Uninstall {
        
        private static $entities = array();
        private static $callbacks = array();
        
        public static function register_entity($entity) {
            if($entity instanceof \WPFront\URE\Options\WPFront_User_Role_Editor_Options_Entity) {
                return;
            }
            
            self::$entities[] = $entity;
        }
        
        public static function register_callback($fnc) {
            self::$callbacks[] = $fnc;
        }
        
        public static function uninstall() {
            if(!self::remove_data()) {
                return;
            }
            
            if(is_multisite()) {
                $blog_ids = get_sites(array('fields' => 'ids'));
                foreach ($blog_ids as $blogid) {
                    switch_to_blog($blogid);
                    self::uninstall_action();
                    restore_current_blog();
                }
                
            } else {
                self::uninstall_action();
            }
            
            wp_cache_flush();
        }
        
        private static function uninstall_action() {
            foreach (self::$callbacks as $callback) {
                call_user_func($callback);
            }
            
            foreach (self::$entities as $entity) {
                $entity->uninstall();
            }
            
            $options = new \WPFront\URE\Options\WPFront_User_Role_Editor_Options_Entity();
            $options->uninstall();
        }
        
        private static function remove_data() {
            $obj = new WPFront_User_Role_Editor_Uninstall();
            return $obj->get_option('remove_data_on_uninstall');
        }
        
        protected function __construct() {
        }
        
        public static function init() {
            $obj = new WPFront_User_Role_Editor_Uninstall();
            add_filter('wpfront_ure_options_register_ui_field', array($obj, 'register_ui_field'), PHP_INT_MAX, 1);
            
            add_action('wpfront_ure_options_ui_field_remove_data_on_uninstall_label', array($obj, 'options_ui_label'));
            add_action('wpfront_ure_options_ui_field_remove_data_on_uninstall', array($obj, 'options_ui_field'));
            add_action('wpfront_ure_options_ui_field_remove_data_on_uninstall_update', array($obj, 'options_ui_update'));
            add_action('wpfront_ure_options_ui_field_remove_data_on_uninstall_help', array($obj, 'options_ui_help'));
        }

        public function register_ui_field($option_keys) {
            $option_keys['remove_data_on_uninstall'] = '';
            
            return $option_keys;
        }
        
        public function options_ui_label() {
            echo __('Remove Data on Uninstall', 'wpfront-user-role-editor');
        }
        
        public function options_ui_field() {
            $key = 'remove_data_on_uninstall';
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $checked = !empty($_POST[$key]);
            } else {
                $checked = $this->get_option($key);
            }
            
            $checked = $checked ? 'checked' : '';
            
            echo "<input type='checkbox' name='$key' $checked />";
        }
        
        public function options_ui_update() {
            $key = 'remove_data_on_uninstall';
            $value = !empty($_POST[$key]);
            
            $this->set_option($key, $value, false);
        }
        
        public function options_ui_help() {
            return '<strong>' . __('Remove Data on Uninstall', 'wpfront-user-role-editor') . '</strong>: ' . __('If enabled, removes all data related to this plugin from database(except roles data) including license information if any, while deleting the plugin. This will not deactivate the license automatically.', 'wpfront-user-role-editor');
        }
        
        protected function get_option($key) {
            return \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance()->get_network_option_boolean($key, '');
        }
        
        protected function set_option($key, $value, $auto_load = true) {
            \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance()->set_network_option($key, $value, '', $auto_load);
        }
        
    }
    
    add_action('wpfront_ure_init', '\WPFront\URE\WPFront_User_Role_Editor_Uninstall::init');
    
}