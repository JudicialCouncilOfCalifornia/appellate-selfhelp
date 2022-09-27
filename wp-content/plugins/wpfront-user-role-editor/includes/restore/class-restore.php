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
 * Controller for WPFront User Role Editor Restore Role
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Restore;

if (!defined('ABSPATH')) {
    exit();
}

require_once dirname(__FILE__) . '/template-restore.php';

if (!class_exists('\WPFront\URE\Restore\WPFront_User_Role_Editor_Restore')) {

    /**
     * Restore Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Restore extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {

        protected function setUp() {
            $this->_setUp('edit_roles', 'wpfront-user-role-editor-restore');

            $this->ViewClass = WPFront_User_Role_Editor_Restore_View::class;
        }

        protected function initialize() {
            add_action('admin_init', array($this, 'admin_init'));

            if (!$this->in_admin_ui()) {
                return;
            }

            $this->set_admin_menu(__('Restore Role', 'wpfront-user-role-editor'), __('Restore', 'wpfront-user-role-editor'));

            add_filter('wpfront_ure_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 20, 1);
            add_filter('wpfront_ure_ms_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 20, 1);
        }

        /**
         * Adds ajax functions on admin_init
         */
        public function admin_init() {
            add_action('wp_ajax_wpfront_user_role_editor_restore_role', array($this, 'restore_role_callback'), 10, 0);
        }

        /**
         * Hooks on options class to display ui.
         * 
         * @param array $option_keys
         */
        public function wpfront_ure_options_register_ui_field($option_keys) {
            $option_keys['remove_nonstandard_capabilities_restore'] = '';

            add_action('wpfront_ure_options_ui_field_remove_nonstandard_capabilities_restore_label', array($this, 'options_ui_label'));
            add_action('wpfront_ure_options_ui_field_remove_nonstandard_capabilities_restore', array($this, 'options_ui_field'));
            add_action('wpfront_ure_options_ui_field_remove_nonstandard_capabilities_restore_update', array($this, 'options_ui_update'));
            add_action('wpfront_ure_options_ui_field_remove_nonstandard_capabilities_restore_help', array($this, 'options_ui_help'));

            return $option_keys;
        }

        public function options_ui_label() {
            echo __('Remove Non-Standard Capabilities on Restore', 'wpfront-user-role-editor');
        }

        public function options_ui_field() {
            $key = 'remove_nonstandard_capabilities_restore';

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $checked = !empty($_POST[$key]);
            } else {
                if (is_network_admin()) {
                    $checked = $this->Options->get_network_option_boolean($key);
                } else {
                    $checked = $this->Options->get_option_boolean($key, true);
                }
            }

            $checked = $checked ? 'checked' : '';

            echo "<input type='checkbox' name='$key' $checked />";
        }

        public function options_ui_update() {
            $key = 'remove_nonstandard_capabilities_restore';
            $value = !empty($_POST[$key]);

            if (is_network_admin()) {
                $this->Options->set_network_option($key, $value, 'ms_', false);
            } else {
                $this->Options->set_option($key, $value, false);
            }
        }

        public function options_ui_help() {
            return '<strong>' . __('Remove Non-Standard Capabilities on Restore', 'wpfront-user-role-editor') . '</strong>: ' . __(' If enabled, while restoring WordPress built-in roles, non-standard capabilities will be removed.', 'wpfront-user-role-editor');
        }

        public function load_view() {
            if (!parent::load_view()) {
                return;
            }

            $this->set_help_tab();
        }

        /**
         * Displays the add/edit role view.
         */
        public function view() {
            if (!parent::view()) {
                return;
            }

            $objView = new $this->ViewClass();
            $objView->view();
        }

        /**
         * Returns restorable roles with display name.
         * 
         * @return array
         */
        public function get_restorable_roles() {
            $rolenames = $this->RolesHelperClass::get_default_rolenames();
            $roles = array();

            foreach ($rolenames as $role) {
                $display_name = $this->RolesHelperClass::get_display_name($role, true);
                $roles[$role] = $display_name;
            }

            return $roles;
        }

        /**
         * Restore action ajax call back.
         */
        public function restore_role_callback() {
            check_ajax_referer('restore-role', 'nonce');

            if (!current_user_can($this->get_cap())) {
                wp_die(-1, 403);
            }

            if (empty($_POST['role'])) {
                wp_die(-1, 403);
            }

            $role = $_POST['role'];
            $allowed_roles = $this->get_restorable_roles();

            if (!array_key_exists($role, $allowed_roles)) {
                wp_die(-1, 403);
            }

            $this->restore_role($role);

            wp_die(json_encode(true));
        }

        protected function restore_role($role) {
            $remove_non_std = $this->remove_nonstandard_capabilities_restore();
            if ($remove_non_std) {
                remove_role($role);
            }

            $role_object = get_role($role);
            if (empty($role_object)) {
                $this->RolesHelperClass::add_role($role, $this->RolesHelperClass::get_display_name($role, true), array());
            }

            if ($role === $this->RolesHelperClass::ADMINISTRATOR_ROLE_KEY) {
                $groups = $this->RolesHelperClass::get_capabilty_groups();

                if (isset($groups['network'])) {
                    $this->RolesHelperClass::remove_capabilities_from_role($role, $this->RolesHelperClass::get_standard_network_capabilities());
                    unset($groups['network']);
                }

                $grant = array();
                foreach ($groups as $name => $group) {
                    $caps = $this->RolesHelperClass::get_group_capabilities($group);
                    if (is_array($caps)) {
                        $grant = array_merge($grant, $caps);
                    }
                }
                $grant = array_fill_keys($grant, true);
                $this->RolesHelperClass::add_capabilities_to_role($role, $grant);
            } else {
                $caps = $this->RolesHelperClass::get_standard_capabilities($role);
                $custom_caps = apply_filters('wpfront_ure_restore_role_custom_caps', array(), $role);

                $custom_caps_check = array();
                foreach ($custom_caps as $cap => $depend_on) {
                    if ($cap === $depend_on) {
                        continue;
                    }

                    if (!isset($custom_caps_check[$depend_on])) {
                        $custom_caps_check[$depend_on] = array();
                    }

                    $custom_caps_check[$depend_on][] = $cap;
                }

                $grant = array();
                $remove = array();
                if (is_array($caps)) {
                    foreach ($caps as $cap => $enabled) {
                        if ($enabled) {
                            $grant[$cap] = true;
                        } else {
                            $remove[] = $cap;
                        }

                        $this->custom_caps_restore($custom_caps_check, $cap, $enabled, $grant, $remove);
                    }
                }

                $this->RolesHelperClass::add_capabilities_to_role($role, $grant);
                $this->RolesHelperClass::remove_capabilities_from_role($role, $remove);
            }
        }

        protected function custom_caps_restore($custom_caps, $cap, $enabled, &$grant, &$remove) {
            if (isset($custom_caps[$cap])) {
                $check = $custom_caps[$cap];
                foreach ($check as $c_cap) {
                    if ($enabled) {
                        $grant[$c_cap] = true;
                        $this->custom_caps_restore($custom_caps, $c_cap, true, $grant, $remove);
                    } else {
                        $remove[] = $c_cap;
                    }
                }
            }
        }

        /**
         * Sets the help tab
         */
        protected function set_help_tab() {
            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('This screen allows you to restore WordPress built-in roles to its standard capability settings.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('To restore a role, click the Restore button then Confirm.', 'wpfront-user-role-editor')
                    . '</p>'
                )
            );

            $sidebar = array(
                array(
                    __('Documentation on Restore', 'wpfront-user-role-editor'),
                    'restore-role/'
                )
            );

            $this->UtilsClass::set_help_tab($tabs, $sidebar);
        }

        /**
         * Returns remove_nonstandard_capabilities_restore setting value.
         * 
         * @return boolean
         */
        public function remove_nonstandard_capabilities_restore() {
            return $this->Options->get_option_boolean('remove_nonstandard_capabilities_restore', true);
        }

        public static function get_debug_setting() {
            return array('key' => 'restore', 'label' => __('Restore', 'wpfront-user-role-editor'), 'position' => 50, 'description' =>  __('Disables users ability to restore WordPress built-in roles.', 'wpfront-user-role-editor'));
        }
    }

    WPFront_User_Role_Editor_Restore::load();
}