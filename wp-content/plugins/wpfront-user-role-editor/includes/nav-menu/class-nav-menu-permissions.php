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
 * Controller for WPFront User Role Editor Nav Menu Permissions
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Nav_Menu;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;
use WPFront\URE\Options\WPFront_User_Role_Editor_Options as Options;

require_once dirname(__FILE__) . '/class-nav-menu-walker.php';

if (!class_exists('\WPFront\URE\Nav_Menu\WPFront_User_Role_Editor_Nav_Menu_Permissions')) {

    /**
     * Nav Menu Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Nav_Menu_Permissions extends \WPFront\URE\WPFront_User_Role_Editor_Controller {
        
        protected static $ALL_USERS = 1;
        protected static $LOGGEDIN_USERS = 2;
        protected static $GUEST_USERS = 3;
        protected static $ROLE_USERS = 4;
        protected static $META_DATA_KEY = 'wpfront-user-role-editor-nav-menu-data';
        
        const CAP = 'edit_nav_menu_permissions';

        protected function setUp() {
            $this->_setUp('edit_nav_menu_permissions');
        }
        
        /**
         * Hooks into wpfront_ure_init.
         */
        protected function initialize() {
            add_filter('wpfront_ure_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 30, 1);
            add_filter('wpfront_ure_ms_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 30, 1);
            
            if($this->disable_navigation_menu_permissions()) {
                return;
            }
            
            //apply logic
            add_filter('wp_get_nav_menu_items', array($this, 'override_nav_menu_items'), 10, 3);
            
            if(!is_admin()) {
                return;
            }
            
            //set menu walker
            add_action('wp_loaded', array($this, 'wp_init'), PHP_INT_MAX);
            
            //set custom fields
            add_action('wp_nav_menu_item_custom_fields', array($this, 'menu_item_custom_fields'), 10, 4);
            //set menu item title
            add_action('wp_nav_menu_item_title_user_restriction_type', array($this, 'menu_item_title_user_restriction_type'), 10, 4);
            //set menu item role list
            add_action('wp_nav_menu_item_custom_fields_roles_list', array($this, 'menu_item_custom_fields_roles_list'), 10, 4);
            
            //save menu item data
            add_action('wp_update_nav_menu_item', array($this, 'update_nav_menu_item'), 10, 3);
            
            if(Utils::doing_ajax()) {
                return;
            }
            
            add_action('admin_print_scripts-nav-menus.php', array($this, 'enqueue_menu_scripts'));
            add_action('admin_print_styles-nav-menus.php', array($this, 'enqueue_menu_styles'));
            add_action('load-nav-menus.php', array($this, 'menu_walker_override_notice_action'));
        }
        
        /**
         * Hooks into init and sets the filter for overriding menu walker.
         */
        public function wp_init() {
            add_filter('wp_edit_nav_menu_walker', array($this, 'override_edit_nav_menu_walker'), PHP_INT_MAX);
        }
        
        /**
         * Hooks into wp_edit_nav_menu_walker and sets the menu walker class.
         * 
         * @param string $current
         * @return string
         */
        public function override_edit_nav_menu_walker($current = 'Walker_Nav_Menu_Edit') {
            if ($current !== 'Walker_Nav_Menu_Edit' && !$this->override_navigation_menu_permissions()) {
                return $current;
            }
            
            return '\WPFront\URE\Nav_Menu\WPFront_User_Role_Editor_Nav_Menu_Walker';
        }
        
        /**
         * Returns data saved for menu item.
         * 
         * @param int $menu_item_db_id
         * @return object
         */
        protected function get_meta_data($menu_item_db_id) {
            $data = get_post_meta($menu_item_db_id, self::$META_DATA_KEY, true);

            if (empty($data)) {
                $data = (OBJECT) array('type' => self::$ALL_USERS);
            }

            switch (intval($data->type)) {
                case self::$LOGGEDIN_USERS:
                case self::$GUEST_USERS:
                case self::$ROLE_USERS:
                    $data->type = intval($data->type);
                    break;
                default:
                    $data->type = self::$ALL_USERS;
                    break;
            }

            return $data;
        }
        
        /**
         * Hooks into wp_nav_menu_item_custom_fields, displays the custom fields.
         * 
         * @param int $item_id
         * @param object $item
         * @param int $depth
         * @param array $args
         */
        public function menu_item_custom_fields($item_id, $item, $depth, $args) {
            if (!current_user_can(self::CAP)) {
                return;
            }

            $data = $this->get_meta_data($item_id);
            ?>
            <p class="description description-wide"></p>
            <p class="description description-wide">
                <label><?php echo __('User Restrictions', 'wpfront-user-role-editor'); ?></label>
                <span class="user-restriction-container">
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . esc_attr($item_id); ?>" value="<?php echo self::$ALL_USERS; ?>" <?php echo $data->type === self::$ALL_USERS ? 'checked' : ''; ?> /><?php echo __('All Users', 'wpfront-user-role-editor'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . esc_attr($item_id); ?>" value="<?php echo self::$LOGGEDIN_USERS; ?>" <?php echo $data->type === self::$LOGGEDIN_USERS ? 'checked' : ''; ?> /><?php echo __('Logged in Users', 'wpfront-user-role-editor'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . esc_attr($item_id); ?>" value="<?php echo self::$GUEST_USERS; ?>" <?php echo $data->type === self::$GUEST_USERS ? 'checked' : ''; ?> /><?php echo __('Guest Users', 'wpfront-user-role-editor'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo 'user-restriction-type-' . esc_attr($item_id); ?>" value="<?php echo self::$ROLE_USERS; ?>" <?php echo $data->type === self::$ROLE_USERS ? 'checked' : ''; ?> /><?php echo __('Users by Role', 'wpfront-user-role-editor'); ?></label>
                    <span class="roles-container <?php echo $data->type === self::$ROLE_USERS ? '' : 'hidden'; ?>">
                        <?php do_action('wp_nav_menu_item_custom_fields_roles_list', $item_id, $item, $depth, $args); ?>
                    </span>
                </span>
            </p>
            <?php
        }
        
        /**
         * Hooks into wp_nav_menu_item_custom_fields_roles_list, display the roles list.
         * 
         * @param int $item_id
         * @param object $item
         * @param int $depth
         * @param array $args
         */
        public function menu_item_custom_fields_roles_list($item_id, $item, $depth, $args) {
            printf(__('%s to limit based on roles.', 'wpfront-user-role-editor'), '<a target="_blank" href="https://wpfront.com/navmenu">' . __('Upgrade to Pro', 'wpfront-user-role-editor') . '</a>');
        }
        
        /**
         * Hooks into wp_nav_menu_item_title_user_restriction_type, appends to menu item title.
         * 
         * @param int $item_id
         * @param object $item
         * @param int $depth
         * @param array $args
         */
        public function menu_item_title_user_restriction_type($item_id, $item, $depth, $args) {
            if (!current_user_can(self::CAP)) {
                return;
            }

            $data = $this->get_meta_data($item_id);
            $text = __('All Users', 'wpfront-user-role-editor');

            switch ($data->type) {
                case self::$LOGGEDIN_USERS:
                    $text = __('Logged in Users', 'wpfront-user-role-editor');
                    break;
                case self::$GUEST_USERS:
                    $text = __('Guest Users', 'wpfront-user-role-editor');
                    break;
                case self::$ROLE_USERS:
                    $text = __('Users by Role', 'wpfront-user-role-editor');
                    break;
            }
            ?>
            <span class="is-submenu">
                <?php echo '(' . $text . ')'; ?>
            </span>
            <?php
        }
        
        /**
         * Hooks into wp_update_nav_menu_item, saves data in DB.
         * 
         * @param int $menu_id
         * @param object $menu_item_db_id
         * @param array $args
         */
        public function update_nav_menu_item($menu_id, $menu_item_db_id, $args) {
            if (!current_user_can(self::CAP)) {
                return;
            }

            $data = $this->get_nav_menu_item_post_data($menu_item_db_id);

            update_post_meta($menu_item_db_id, self::$META_DATA_KEY, $data);
        }
        
        /**
         * Reads data from $_POST and creates data object.
         * 
         * @param int $menu_item_db_id
         * @return object
         */
        protected function get_nav_menu_item_post_data($menu_item_db_id) {
            $data = $this->get_meta_data($menu_item_db_id);

            if (!empty($_POST['user-restriction-type-' . $menu_item_db_id])) {
                $data->type = intval($_POST['user-restriction-type-' . $menu_item_db_id]);
            }
            
            return $data;
        }
        
        /**
         * Hooks into wp_get_nav_menu_items, applies display logic.
         * 
         * @param array $items
         * @param object $menu
         * @param array $args
         */
        public function override_nav_menu_items($items, $menu, $args) {
            if (is_admin()) {
                return $items;
            }

            $remove_parent = array();

            foreach ($items as $key => $item) {
                $data = $this->get_meta_data($item->db_id);

                if (!$this->is_nav_menu_enabled($data)) {
                    $remove_parent[] = $item->ID;
                    unset($items[$key]);
                }
            }

            while (!empty($remove_parent)) {
                foreach ($items as $key => $item) {
                    if (empty($item)) {
                        continue;
                    }

                    if (intval($item->menu_item_parent) === intval($remove_parent[0])) {
                        $remove_parent[] = $item->ID;
                        unset($items[$key]);
                    }
                }
                
                array_shift($remove_parent);
            }

            return array_values($items);
        }
        
        /**
         * Checks whether menu should be displayed.
         * 
         * @param object $data
         * @return boolean
         */
        protected function is_nav_menu_enabled($data) {
            switch ($data->type) {
                case self::$LOGGEDIN_USERS:
                    return is_user_logged_in();
                    
                case self::$GUEST_USERS:
                    return !is_user_logged_in();
            }
            
            return true;
        }
        
        /**
         * Hooks into load-nav-menus.php, adds action display notice.
         */
        public function menu_walker_override_notice_action() {
            add_action('admin_notices', array($this, 'menu_walker_override_warning'));
        }
        
        /**
         * Hooks into admin_notices, to display walker class override warning.
         */
        public function menu_walker_override_warning() {
            if ($this->disable_navigation_menu_permissions()) {
                return;
            }
            
            if(!current_user_can(self::CAP)) {
                return;
            }
            
            $menu_walker = apply_filters('wp_edit_nav_menu_walker', 'Walker_Nav_Menu_Edit', 0);
            if ($menu_walker !== $this->override_edit_nav_menu_walker()) {
                $message = sprintf(
                        '%s %s %s %s',
                        __('Menu walker class is overriden by a theme/plugin.', 'wpfront-user-role-editor'),
                        sprintf(__('Current value = %s.', 'wpfront-user-role-editor'), $menu_walker),
                        __('Navigation menu permissions may still work.', 'wpfront-user-role-editor'),
                        '<a target="_blank" href="' . $this->nav_menu_help_url() . '#navigation-menu-permission-warning">' . __('How to fix?', 'wpfront-user-role-editor') . '</a>'
                );

                Utils::notice_error($message);
            }
        }
        
        /**
         * Hooks on options class to display ui.
         * 
         * @param array $option_keys
         */
        public function wpfront_ure_options_register_ui_field($option_keys) {
            $option_keys['disable_navigation_menu_permissions'] = '';
            add_action('wpfront_ure_options_ui_field_disable_navigation_menu_permissions_label', array($this, 'options_ui_label'), 10, 1);
            add_action('wpfront_ure_options_ui_field_disable_navigation_menu_permissions', array($this, 'options_ui_field'), 10, 1);
            add_action('wpfront_ure_options_ui_field_disable_navigation_menu_permissions_update', array($this, 'options_ui_update'), 10, 1);
            add_action('wpfront_ure_options_ui_field_disable_navigation_menu_permissions_help', array($this, 'options_ui_help'), 10, 2);
            
            $option_keys['override_navigation_menu_permissions'] = '';
            add_action('wpfront_ure_options_ui_field_override_navigation_menu_permissions_label', array($this, 'options_ui_label'), 10, 1);
            add_action('wpfront_ure_options_ui_field_override_navigation_menu_permissions', array($this, 'options_ui_field'), 10, 1);
            add_action('wpfront_ure_options_ui_field_override_navigation_menu_permissions_update', array($this, 'options_ui_update'), 10, 1);
            add_action('wpfront_ure_options_ui_field_override_navigation_menu_permissions_help', array($this, 'options_ui_help'), 10, 2);
            
            return $option_keys;
        }
        
        public function options_ui_label($key) {
            if($key === 'disable_navigation_menu_permissions') {
                echo __('Disable Navigation Menu Permissions', 'wpfront-user-role-editor');
            } else {
                echo __('Override Navigation Menu Permissions', 'wpfront-user-role-editor');
            }
        }
        
        public function options_ui_field($key) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $checked = !empty($_POST[$key]);
            } else {
                if(is_network_admin()) {
                    $checked = Options::instance()->get_network_option_boolean($key);
                } else {
                    $checked = $this->Options->get_option_boolean($key, true);
                }
            }
            
            $checked = $checked ? 'checked' : '';
            
            echo "<input type='checkbox' name='".esc_attr($key)."' $checked />";
        }
        
        public function options_ui_update($key) {
            $value = !empty($_POST[$key]);
            
            if(is_network_admin()) {
                Options::instance()->set_network_option($key, $value);
            } else {
                Options::instance()->set_option($key, $value);
            }
        }
        
        public function options_ui_help($help, $key) {
            if($key === 'disable_navigation_menu_permissions') {
                return '<strong>' . __('Disable Navigation Menu Permissions', 'wpfront-user-role-editor') . '</strong>: ' . __('If enabled, disables navigation menu permissions functionality.', 'wpfront-user-role-editor');
            } else {
                return '<strong>' . __('Override Navigation Menu Permissions', 'wpfront-user-role-editor') . '</strong>: ' . __('If enabled, tries to reset navigation menu permissions UI, when a conflict is detected.', 'wpfront-user-role-editor');
            }
        }
        
        /**
         * Return disable_navigation_menu_permissions setting value.
         * 
         * @return boolean
         */
        public function disable_navigation_menu_permissions() {
            return Options::instance()->get_option_boolean('disable_navigation_menu_permissions', true);
        }
        
        /**
         * Return override_navigation_menu_permissions setting value.
         * 
         * @return boolean
         */
        public function override_navigation_menu_permissions() {
            return Options::instance()->get_option_boolean('override_navigation_menu_permissions', true);
        }
        
        public function enqueue_menu_scripts() {
            wp_enqueue_script('jquery');
            wp_enqueue_script('wpfront-user-role-editor-nav-menu-js', WPFURE::instance()->get_asset_url('js/nav-menu.js'), array('jquery'), WPFURE::VERSION);
        }

        public function enqueue_menu_styles() {
            wp_enqueue_style('wpfront-user-role-editor-nav-menu-css', WPFURE::instance()->get_asset_url('css/nav-menu.css'), array(), WPFURE::VERSION);
        }
        
        public function nav_menu_help_url() {
            return 'https://wpfront.com/user-role-editor-pro/navigation-menu-permissions/';
        }
        
        public static function uninstall() {
            delete_post_meta_by_key(self::$META_DATA_KEY);
        }
        
        public static function get_debug_setting() {
            return array('key' => 'nav-menu-permissions', 'label' => __('Navigation Menu Permissions', 'wpfront-user-role-editor'), 'position' => 160, 'description' => __('Disables navigation menu permissions functionality.', 'wpfront-user-role-editor'));
        }
        
    }
    
    \WPFront\URE\WPFront_User_Role_Editor_Uninstall::register_callback('\WPFront\URE\Nav_Menu\WPFront_User_Role_Editor_Nav_Menu_Permissions::uninstall');
    
    WPFront_User_Role_Editor_Nav_Menu_Permissions::load();
    
}