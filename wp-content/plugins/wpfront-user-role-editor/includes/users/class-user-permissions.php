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
 * Controller for WPFront User Role Editor User Permissions
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\User_Permissions;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;
use WPFront\URE\Options\WPFront_User_Role_Editor_Options as Options;

if (!class_exists('\WPFront\URE\User_Permissions\WPFront_User_Role_Editor_User_Permissions')) {

    /**
     * User Permissions class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_User_Permissions extends \WPFront\URE\WPFront_User_Role_Editor_Controller {
        
        protected static $user_capabilities = array(
            'edit_users_higher_level' => 'edit_users',
            'delete_users_higher_level' => 'delete_users',
            'promote_users_higher_level' => 'promote_users',
            'promote_users_to_higher_level' => 'promote_users'
        );
        
        protected function setUp() {
        }
        
        /**
         * Init function
         */
        protected function initialize() {
            if(!is_admin()) {
                return;
            }
            
            RolesHelper::add_capability_group('users', __('Users', 'wpfront-user-role-editor'));
            
            foreach (self::$user_capabilities as $cap => $value) {
                RolesHelper::add_new_capability_to_group('users', $cap);
                
                add_filter("wpfront_ure_capability_{$cap}_functionality_enabled", '__return_false');
                add_filter("wpfront_ure_capability_{$cap}_ui_help_link", array($this, 'cap_help_link'), 10, 2);
            }
            
            $this->add_capabilities_to_roles();
            
            add_filter('wpfront_ure_restore_role_custom_caps', array($this, 'restore_role_custom_caps'));
        }
        
        /**
         * Sets the new capabilities for the first time.
         * 
         * @global \WP_Role[] $wp_roles
         */
        public function add_capabilities_to_roles() {
            $option_key = 'user_permission_capabilities_processed';
            $processed = Options::instance()->get_option_boolean($option_key);
            if (!empty($processed)) {
                return;
            }

            global $wp_roles;

            foreach ($wp_roles->role_objects as $key => $role) {
                foreach (self::$user_capabilities as $u_cap => $cap) {
                    if ($role->has_cap($cap)) {
                        $role->add_cap($u_cap);
                    }
                }
            }

            Options::instance()->set_option($option_key, true);
        }
        
        public function restore_role_custom_caps($custom_caps) {
            foreach (self::$user_capabilities as $key => $value) {
                $custom_caps[$key] = $value;
            }
            
            return $custom_caps;
        }
        
        public function cap_help_link($help_link, $cap) {
            return RolesHelper::get_wpfront_help_link($cap);
        }
        
        public static function get_debug_setting() {
            return array('key' => 'user-permissions', 'label' => __('User Level Permissions', 'wpfront-user-role-editor'), 'position' => 190, 'description' => __('Disables all user level permissions.', 'wpfront-user-role-editor'));
        }
    }
    
    WPFront_User_Role_Editor_User_Permissions::load();
    
}
