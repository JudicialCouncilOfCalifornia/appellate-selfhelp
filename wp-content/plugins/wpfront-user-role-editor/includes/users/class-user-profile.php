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
 * Controller for WPFront User Role Editor User Profile
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Assign_Migrate;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;
use WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_Migrate as AssignMigrate;

require_once dirname(__FILE__) . '/template-user-profile-secondary-roles.php';

if (!class_exists('\WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_User_Profile')) {

    /**
     * User Profile class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_User_Profile extends \WPFront\URE\WPFront_User_Role_Editor_Controller {
        
        const CAP = 'promote_users';

        protected $AssignMigrate = null;

        protected $invited_users_roles = null;

        protected function setUp() {
            $this->_setUp('promote_users');
            
            $this->AssignMigrate = \WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_Migrate::instance();
        }
        
        /**
         * Hooks into wpfront_ure_init.
         */
        protected function initialize() {
            //invite_user - retrieve secondary roles and set flag
            add_action('init', array($this, 'maybe_add_existing_user_to_blog'), 1);
            //signup_user - retrieve secondary roles and set flag
            add_action('wpmu_activate_user', array($this, 'wpmu_activate_user'), 1, 3);
            
            //invite_user - add
            //signup_user - add
            add_action('add_user_to_blog', array($this, 'add_user_to_blog'), PHP_INT_MAX, 1);
            
            if(!$this->in_admin_ui()) {
                return;
            }
            
            //hide user profile secondary roles settings.
            add_filter('wpfront_ure_options_register_ui_field', array($this, 'register_hide_secondary_roles_option_field'), 1000, 1);
            add_filter('wpfront_ure_ms_options_register_ui_field', array($this, 'register_hide_secondary_roles_option_field'), 1000, 1);
            
            //new user display - both
            add_action('user_new_form', array($this, 'user_new_form'), 10);
            //new user update - single
            add_action('edit_user_created_user', array($this, 'edit_user_created_user'), PHP_INT_MAX, 1);
            //edit user display - both
            add_action('edit_user_profile', array($this, 'edit_user_profile'), 10, 1);
            //edit user update - both
            add_action('profile_update', array($this, 'edit_user_profile_update'), PHP_INT_MAX, 2);
            
            //add existing user - multi - invite
            add_action('invite_user', array($this, 'invite_user'), PHP_INT_MAX, 3);
            //add new user - multi - invite
            add_filter('signup_user_meta', array($this, 'signup_user_meta'), PHP_INT_MAX, 1);
            
            //general settings - new user default secondary roles
            add_action('load-options-general.php', array($this, 'load_options_general'));
            global $wp_version;
            if(version_compare($wp_version, '5.5', '>=')) {
                add_filter('allowed_options', array($this, 'whitelist_options'));
            } else {
                add_filter('whitelist_options', array($this, 'whitelist_options'));
            }
        }
        
        /**
         * Hooks into user_new_form action. Displays roles on new user form.
         */
        public function user_new_form() {
            if(!$this->new_user_promote_allowed()) {
                return;
            }
            
            $roles = $this->get_secondary_roles();
            $roles = apply_filters('wpfront_ure_add_user_profile_secondary_roles', $roles);
            
            $objView = new WPFront_User_Role_Editor_User_Profile_Secondary_Roles_View($this);
            $objView->view($roles);
        }
        
        /**
         * Hooks into edit_user_created_user action and updates secondary roles of new user.
         * 
         * @param int $user_id
         */
        public function edit_user_created_user($user_id, $skip_permission_checks = false) {
            $user = get_userdata($user_id);
            if (empty($user)) {
                return;
            }
            
            if(!$skip_permission_checks && !$this->new_user_promote_allowed()) {
                $roles = $this->get_new_user_default_secondary_roles();
            } elseif($this->hide_secondary_roles()) {
                $roles = $this->get_new_user_default_secondary_roles();
            } else {
                $roles = $this->get_submitted_roles($user, 'wpfront_ure_add_user_profile_secondary_roles');
            }
            
            foreach ($roles as $role) {
                $user->add_role($role);
            }
        }
        
        protected function new_user_promote_allowed() {
            if (is_network_admin()) {
                return false;
            }

            if (!current_user_can('promote_users')) {
                return false;
            }
            
            return true;
        }
        
        /**
         * Hooks into edit_user_profile action. Displays roles on edit user profile.
         * 
         * @param \WP_User $user
         */
        public function edit_user_profile($user) {
            if (!$this->edit_user_promote_allowed($user->ID)) {
                return;
            }
            
            $roles = $this->get_secondary_roles();
            $roles = apply_filters('wpfront_ure_edit_user_profile_secondary_roles', $roles, $user);
            
            $objView = new WPFront_User_Role_Editor_User_Profile_Secondary_Roles_View($this, $user);
            $objView->view($roles);
        }
        
        /**
         * Hooks into profile_update action and updates secondary roles while editing user.
         * 
         * @param int $user_id
         */
        public function edit_user_profile_update($user_id, $old_user) {
            if (!$this->edit_user_promote_allowed($user_id)) {
                return;
            }

            $user = get_userdata($user_id);
            if (empty($user)) {
                return;
            }
            
            if($this->hide_secondary_roles()) {
                $roles = $old_user->roles;
                array_shift($roles);
            } else {
                $roles = $this->get_submitted_roles($user, 'wpfront_ure_edit_user_profile_secondary_roles');
            }
            
            foreach ($roles as $role) {
                $user->add_role($role);
            }
        }
        
        protected function edit_user_promote_allowed($user_id) {
            if (is_network_admin()) {
                return false;
            }

            if (!current_user_can('promote_users')) {
                return false;
            }

            if ($user_id === wp_get_current_user()->ID) {
                return false;
            }

            if (!current_user_can('promote_user', $user_id)) {
                return false;
            }
            
            return true;
        }
        
        protected function get_submitted_roles($user, $filter) {
            if($this->hide_secondary_roles()) {
                return array();
            }
            
            if(empty($_POST['wpfront-secondary-roles'])) {
                return array();
            }
            
            $roles = array_keys($_POST['wpfront-secondary-roles']);
            
            $allowed = $this->get_secondary_roles();
            $allowed = apply_filters($filter, $allowed, $user);
            $allowed_roles = array();
            foreach ($roles as $role) {
                if(isset($allowed[$role])) {
                    $allowed_roles[] = $role;
                }
            }
            
            return $allowed_roles;
        }
        
        public function add_user_to_blog($user_id) {
            if($this->invited_users_roles !== null) { //invite/signup email click
                $roles = array();
                foreach ($this->invited_users_roles as $role) {
                    $roles[$role] = '';
                }

                $_POST['wpfront-secondary-roles'] = $roles;
                
                $this->edit_user_created_user($user_id, true);
                return;
            }
            
            $this->edit_user_created_user($user_id, false);
        }
        
        public function invite_user($user_id, $role, $newuser_key) {
            $newuser_key = 'new_user_' . $newuser_key;
            $option = get_option($newuser_key);
            
            $roles = $this->get_submitted_roles(get_userdata($user_id), 'wpfront_ure_add_user_profile_secondary_roles');
            
            $option['secondary-roles'] = $roles;
            
            update_option($newuser_key, $option);
        }
        
        public function maybe_add_existing_user_to_blog() {
            if ( false === strpos( $_SERVER['REQUEST_URI'], '/newbloguser/' ) ) {
                return;
            }
            
            $parts = explode('/', $_SERVER['REQUEST_URI']);
            $key   = array_pop($parts);

            if ($key == '') {
                $key = array_pop( $parts );
            }

            $option = get_option('new_user_' . $key);
            
            if(!empty($option['secondary-roles']) && is_array($option['secondary-roles'])) {
                $this->invited_users_roles = $option['secondary-roles'];
            } else {
                $this->invited_users_roles = array();
            }
        }
        
        //multi - add new user - store roles in meta
        public function signup_user_meta($meta) {
            $roles = $this->get_submitted_roles(null, 'wpfront_ure_add_user_profile_secondary_roles');
            
            $meta['secondary-roles'] = $roles;
            
            return $meta;
        }
        
        public function wpmu_activate_user($user_id, $password, $meta) {
            if(empty($meta['secondary-roles'])) {
                $this->invited_users_roles = array();
                return;
            }
            
            $this->invited_users_roles = $meta['secondary-roles'];
        }

        /**
         * Returns the list of secondary roles available.
         * 
         * @return string[] name=>display
         */
        public function get_secondary_roles() {
            return $this->AssignMigrate->get_secondary_roles();
        }
        
        public function hide_secondary_roles() {
            return $this->Options->get_option_boolean('hide_secondary_roles', true);
        }
        
        public function register_hide_secondary_roles_option_field($option_keys) {
            $option_keys['hide_secondary_roles'] = '';
            
            add_action('wpfront_ure_options_ui_field_hide_secondary_roles_label', array($this, 'options_ui_label'));
            add_action('wpfront_ure_options_ui_field_hide_secondary_roles', array($this, 'options_ui_field'));
            add_action('wpfront_ure_options_ui_field_hide_secondary_roles_update', array($this, 'options_ui_update'));
            add_action('wpfront_ure_options_ui_field_hide_secondary_roles_help', array($this, 'options_ui_help'));
            
            return $option_keys;
        }
        
        public function options_ui_label() {
            echo __('Hide Secondary Roles in User Profile', 'wpfront-user-role-editor');
        }
        
        public function options_ui_field($key) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $checked = !empty($_POST[$key]);
            } else {
                if(is_network_admin()) {
                    $checked = $this->Options->get_network_option_boolean($key);
                } else {
                    $checked = $this->Options->get_option_boolean($key, true);
                }
            }
            
            $checked = $checked ? 'checked' : '';
            
            echo "<input type='checkbox' name='$key' $checked />";
        }
        
        public function options_ui_update($key) {
            $value = !empty($_POST[$key]);
            
            if(is_network_admin()) {
                $this->Options->set_network_option($key, $value, 'ms_', false);
            } else {
                $this->Options->set_option($key, $value, false);
            }
        }
        
        public function options_ui_help() {
            return '<strong>' . __('Hide Secondary Roles in User Profile', 'wpfront-user-role-editor') . '</strong>: ' . __('If enabled, hides secondary roles option while adding and editing a user.', 'wpfront-user-role-editor');
        }
        
        public function load_options_general() {
            if(!current_user_can('manage_options')) {
                return;
            }
            
            if(is_multisite()) {
                add_settings_field('new-user-default-role', __('New User Default Role', 'wpfront-user-role-editor'), array($this, 'new_user_default_role'), 'general');
            }
            
            add_settings_field('new-user-default-secondary-roles', __('New User Default Secondary Roles', 'wpfront-user-role-editor'), array($this, 'new_user_sec_roles'), 'general');
        }
        
        public function new_user_default_role() {
            ?>
            <select name="default_role" id="default_role"><?php wp_dropdown_roles( get_option( 'default_role' ) ); ?></select>
            <?php
        }
        
        public function new_user_sec_roles() {
            $roles = $this->get_secondary_roles();
            
            $objView = new WPFront_User_Role_Editor_User_Profile_Secondary_Roles_View($this);
            $objView->display_secondary_roles($roles, $this->get_new_user_default_secondary_roles());
            $objView->add_general_settings_script();
        }
        
        public function whitelist_options($whitelist_options) {
            if(!current_user_can('manage_options')) {
                return $whitelist_options;
            }
            
            global $action, $option_page;
            
            if(empty($action) || empty($option_page)) {
                return $whitelist_options;
            }
            
            if($action == 'update' && $option_page == 'general') {
                $roles = array();
                if(!empty($_POST['wpfront-secondary-roles'] && is_array($_POST['wpfront-secondary-roles']))) {
                    $roles = array_keys($_POST['wpfront-secondary-roles']);
                }
                
                $this->set_new_user_default_secondary_roles($roles);
                
                if(is_multisite() && !empty($_POST['default_role'])) {
                    update_option('default_role', $_POST['default_role']);
                }
            }
            
            return $whitelist_options;
        }
        
        public function set_new_user_default_secondary_roles($roles) {
            $this->Options->set_option('new_user_default_secondary_roles', implode(',', $roles), false);
        }
        
        public function get_new_user_default_secondary_roles() {
            $roles = $this->Options->get_option('new_user_default_secondary_roles');
            $roles = explode(',', $roles);
            
            $default_role = get_option('default_role');
            
            $sec_roles = array();
            foreach ($roles as $role) {
                if($role == $default_role) {
                    continue;
                }
                
                if($this->RolesHelperClass::is_role($role)) {
                    $sec_roles[] = $role;
                }
            }
            
            return $sec_roles;
        }
        
        public static function get_debug_setting() {
            return array('key' => 'user-profile', 'label' => __('User Profile Functions', 'wpfront-user-role-editor'), 'position' => 200, 'description' => __('Disables all user profile functionalities.', 'wpfront-user-role-editor'));
        }
    }
    
    WPFront_User_Role_Editor_User_Profile::load();
    
}