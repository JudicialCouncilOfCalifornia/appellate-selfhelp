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
 * Controller for WPFront User Role Editor Options
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Options;

if (!defined('ABSPATH')) {
    exit();
}

require_once dirname(__FILE__) . '/entity-options.php';
require_once dirname(__FILE__) . '/template-options.php';

if (!class_exists('WPFront\URE\Options\WPFront_User_Role_Editor_Options')) {
    
    interface iWPFront_User_Role_Editor_Settings_Controller {
        public function getKey();
        public function getTitle();
        public function view_callback();
        public function load_view_callback($options);
        public function display_notices();
    }
    
    /**
     * Options Controller
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Options extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller implements iWPFront_User_Role_Editor_Settings_Controller {
        
        /**
         *
         * @var iWPFront_User_Role_Editor_Settings_Controller 
         */
        protected $controllers = null;
        
        /**
         *
         * @var iWPFront_User_Role_Editor_Settings_Controller[] 
         */
        protected $current_controller = null;

        protected $view = null;
        
        /**
         * UI field keys, populated from filter.
         * 
         * @var string[] key => group.
         */
        protected $option_keys = array();

        protected function setUp() {
            $this->_setUp('manage_options', \WPFront\URE\WPFront_User_Role_Editor::PLUGIN_SLUG);
            
            $this->ViewClass = WPFront_User_Role_Editor_Options_View::class;
            $this->EntityClass = WPFront_User_Role_Editor_Options_Entity::class;
        }

        protected function initialize() {
            if(!$this->in_admin_ui()) {
                return;
            }
            
            $this->set_admin_menu(__('Settings', 'wpfront-user-role-editor'), __('Settings', 'wpfront-user-role-editor'), 100);
            
            add_action('plugin_action_links_' . $this->WPFURE->get_plugin_basename(), array($this, 'plugin_action_links'));
        }
        
        public function plugin_action_links($links) {
            if(current_user_can($this->get_cap())) {
                $url = $this->get_self_url();
                $text = __('Settings', 'wpfront-user-role-editor');
                $a = sprintf('<a href="%s">%s</a>', $url, $text);
                array_unshift($links, $a);
            }
            
            return $links;
        }
        
        public function getKey() {
            return null;
        }
        
        public function getTitle() {
            return __('Settings', 'wpfront-user-role-editor');
        }
        
        public function load_view() {
            if(!parent::load_view()) {
                return;
            }
            
            if(!empty($_POST['submit'])) {
                check_admin_referer('save-settings');
            }
            
            $this->controllers = apply_filters('wpfront_ure_settings_controllers', array());
            array_unshift($this->controllers, $this);
            
            if(!empty($_GET['key'])) {
                $key = $_GET['key'];
                foreach ($this->controllers as $ctrl) {
                    if($ctrl->getKey() === $key) {
                        $this->current_controller = $ctrl;
                        break;
                    }
                }
            }
            
            if(empty($this->current_controller)) {
                $this->current_controller = $this;
            }
            
            $this->current_controller->load_view_callback($this);
        }
        
        public function load_view_callback($options) {
            $this->load_option_keys();
            
            if(!empty($_POST['submit'])) {
                foreach ($this->option_keys as $key => $group) {
                    $error = apply_filters('wpfront_ure_options_ui_field_' . $key . '_validate', '', $key);
                    if(!empty($error)) {
                        return;
                    }
                    
                    do_action('wpfront_ure_options_ui_field_' . $key . '_update', $key);
                }
                
                if(wp_safe_redirect($this->get_self_url() . '&settings-updated=true')) {
                    exit();
                }
            }
            
            $this->set_help_tab();
        }
        
        protected function load_option_keys() {
            $this->option_keys = apply_filters('wpfront_ure_options_register_ui_field', $this->option_keys);
        }
        
        public function view() {
            if(!parent::view()) {
                return;
            }
            
            $view = new $this->ViewClass();
            $view->view($this, $this->controllers, $this->current_controller);
        }
        
        public function view_callback() {
            $view = new $this->ViewClass();
            $view->view_settings($this->option_keys);
        }
        
        public function display_notices() {
            $view = new $this->ViewClass();
            $view->display_notices();
        }
        
        /**
         * Sets the help tab of edit roles.
         */
        protected function set_help_tab() {
            $help = '';
            foreach ($this->option_keys as $key => $group) {
                $h = apply_filters('wpfront_ure_options_ui_field_' . $key . '_help', '', $key);
                if(!empty($h)) {
                    $help .= "<p>$h</p>";
                }
            }
            
            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => $help
                )
            );
            
            $sidebar = array(
                array(
                    __('Documentation on Settings', 'wpfront-user-role-editor'),
                    'settings/'
                )
            );
            
            $this->UtilsClass::set_help_tab($tabs, $sidebar);
        }
        
        public function set_option($key, $value, $auto_load = true) {
            $entity = new $this->EntityClass();
            $entity->update_option($key, $value, $auto_load);
        }
        
        public function get_option($key, $fallback = false) {
            if($fallback && is_multisite()) {
                $exists = false;
                $entity = new $this->EntityClass();
                $result = $entity->get_option($key, $exists);
                if($exists) {
                    return $result;
                }
                
                return $this->get_network_option($key);
                
            } else {
                $entity = new $this->EntityClass();
                return $entity->get_option($key);
            }
        }
        
        public function get_option_boolean($key, $fallback = false) {
            return filter_var($this->get_option($key, $fallback), FILTER_VALIDATE_BOOLEAN);
        }
        
        public function delete_option($key) {
            $entity = new $this->EntityClass();
            $entity->delete_option($key);
        }
        
        public function get_network_option($key, $prefix = 'ms_') {
            $this->switch_to_main_blog();
            $result = $this->get_option($prefix . $key);
            $this->restore_current_blog();
            
            return $result;
        }
        
        public function get_network_option_boolean($key, $prefix = 'ms_') {
            return filter_var($this->get_network_option($key, $prefix), FILTER_VALIDATE_BOOLEAN);
        }
        
        public function set_network_option($key, $value, $prefix = 'ms_', $auto_load = true) {
            $this->switch_to_main_blog();
            $this->set_option($prefix . $key, $value, $auto_load);
            $this->restore_current_blog();
        }
        
        public function delete_network_option($key, $prefix = 'ms_') {
            $this->switch_to_main_blog();
            $this->delete_option($prefix . $key);
            $this->restore_current_blog();
        }
        
        protected function switch_to_main_blog() {
            if(is_multisite()) {
                switch_to_blog(get_main_site_id());
            }
        }
        
        protected function restore_current_blog() {
            if(is_multisite()) {
                restore_current_blog();
            }
        }
        
        /**
         * 
         * @param iWPFront_User_Role_Editor_Settings_Controller $ctrl
         */
        public function getControllerUrl($ctrl) {
            $key = $ctrl->getKey();
            if($key === null) {
                return $this->get_self_url();
            }
            
            return $this->get_self_url(['key' => $ctrl->getKey()]);
        }
        
        public function admin_print_scripts() {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-autocomplete');
        }
    
    }
    
    add_action('wpfront_ure_init', '\WPFront\URE\Options\WPFront_User_Role_Editor_Options::init');

}