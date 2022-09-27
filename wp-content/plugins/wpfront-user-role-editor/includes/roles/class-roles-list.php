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
 * Controller for WPFront User Role Editor List Roles
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Roles;

if (!defined('ABSPATH')) {
    exit();
}

require_once dirname(__FILE__) . '/template-roles-list.php';
require_once dirname(__FILE__) . '/class-role-delete.php';

if (!class_exists('\WPFront\URE\Roles\WPFront_User_Role_Editor_Roles_List')) {

    /**
     * List Roles controller.
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Roles_List extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {

        protected $objView = null;
        protected $role_data = null;
        protected $role_user_counts = null;

        protected $AddEditViewClass;
        protected $DeleteViewClass;
        
        protected $UserProfile;

        protected function setUp() {
            $this->_setUp('list_roles', 'wpfront-user-role-editor-all-roles');
            
            $this->ViewClass = WPFront_User_Role_Editor_Roles_List_View::class;
            $this->AddEditViewClass = WPFront_User_Role_Editor_Role_Add_Edit::class;
            $this->DeleteViewClass = WPFront_User_Role_Editor_Role_Delete::class;
            
            $this->UserProfile = \WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_User_Profile::instance();
        }
        
        protected function initialize() {
            if(!$this->in_admin_ui()) {
                return;
            }
            
            $this->set_admin_menu(__('All Roles', 'wpfront-user-role-editor'), __('All Roles', 'wpfront-user-role-editor'));
            
            add_filter('wpfront_ure_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 10, 1);
            add_filter('wpfront_ure_ms_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 10, 1);
            
            //Set Site Role column with links
            add_filter('manage_users_columns', array($this, 'manage_users_columns'), PHP_INT_MAX);
            add_filter('manage_users_custom_column', array($this, 'manage_users_custom_column'), PHP_INT_MAX, 3);
        }
        
        public function admin_print_scripts() {
            parent::admin_print_scripts();
            
            wp_enqueue_script('postbox');
            wp_enqueue_script('jquery-ui-draggable');
        }
        
        /**
         * Hooks on options class to display ui.
         * 
         * @param array $option_keys
         */
        public function wpfront_ure_options_register_ui_field($option_keys) {
            $option_keys['override_edit_permissions'] = '';
            
            add_action('wpfront_ure_options_ui_field_override_edit_permissions_label', array($this, 'options_ui_label'));
            add_action('wpfront_ure_options_ui_field_override_edit_permissions', array($this, 'options_ui_field'));
            add_action('wpfront_ure_options_ui_field_override_edit_permissions_update', array($this, 'options_ui_update'));
            add_action('wpfront_ure_options_ui_field_override_edit_permissions_help', array($this, 'options_ui_help'));
            
            return $option_keys;
        }
        
        public function options_ui_label() {
            echo __('Override Role Edit Permissions', 'wpfront-user-role-editor');
        }
        
        public function options_ui_field() {
            $key = 'override_edit_permissions';
            
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
        
        public function options_ui_update() {
            $key = 'override_edit_permissions';
            $value = !empty($_POST[$key]);
            
            if(is_network_admin()) {
                $this->Options->set_network_option($key, $value, 'ms_', false);
            } else {
                $this->Options->set_option($key, $value, false);
            }
        }
        
        public function options_ui_help() {
            return '<strong>' . __('Override Role Edit Permissions', 'wpfront-user-role-editor') . '</strong>: ' . __('Allows you to edit non-editable roles.', 'wpfront-user-role-editor');
        }
        
        /**
         * Hooks on load-page. Sets the view controller based on current user action.
         * 
         */
        public function load_view() {
            if(!parent::load_view()) {
                return;
            }
            
            add_filter('editable_roles', array($this, 'editable_roles_filter_callback'), PHP_INT_MAX, 1);
            
            if($this->set_default_role()) {
                return;
            }
            
            $this->objView = $this->AddEditViewClass::instance();
            if($this->objView->edit_role()) {
                $this->objView->set_edit_help_tab();
                return;
            }
            
            $this->objView = $this->DeleteViewClass::instance();
            if($this->objView->delete_role()) {
                $this->objView->set_help_tab();
                return;
            }
            
            $this->objView = new $this->ViewClass();
            $this->set_help_tab();
        }
        
        /**
         * Displays the view for this controller.
         */
        public function view() {
            if(!parent::view()) {
                return;
            }
            
            $this->objView->view();
        }
        
        /**
         * Returns an array containing filter links information.
         * 
         * @return array
         */
        public function get_list_filter_data() {
            $filter_data = array();

            $roles = $this->RolesHelperClass::get_roles();
            $page = $this->get_self_url();

            $filter_data['all'] = array(
                'display' => __('All', 'wpfront-user-role-editor'),
                'url' => $page,
                'count' => count($roles)
            );

            $count_users = $this->get_role_user_counts();

            $count = 0;
            foreach ($roles as $role_name) {
                if(isset($count_users[$role_name]) && $count_users[$role_name] > 0)
                    $count++;
            }
            
            $filter_data['haveusers'] = array(
                'display' => __('Having Users', 'wpfront-user-role-editor'),
                'url' => $page . '&list=haveusers',
                'count' => $count
            );

            $filter_data['nousers'] = array(
                'display' => __('No Users', 'wpfront-user-role-editor'),
                'url' => $page . '&list=nousers',
                'count' => count($roles) - $count
            );

            $count = 0;
            foreach ($roles as $role_name) {
                if ($this->RolesHelperClass::is_default_role($role_name))
                    $count++;
            }
            $filter_data['builtin'] = array(
                'display' => __('Built-In', 'wpfront-user-role-editor'),
                'url' => $page . '&list=builtin',
                'count' => $count
            );

            $filter_data['custom'] = array(
                'display' => __('Custom', 'wpfront-user-role-editor'),
                'url' => $page . '&list=custom',
                'count' => count($roles) - $count
            );

            return $filter_data;
        }
        
        /**
         * Returns an array containing role information formatted for display.
         * 
         * @return array
         */
        public function get_role_data() {
            if($this->role_data !== null) {
                return $this->role_data;
            }
            
            $roles = $this->RolesHelperClass::get_names();

            $editable_roles = get_editable_roles();
            $user_default = get_option('default_role');
            $default_sec_roles = $this->UserProfile->get_new_user_default_secondary_roles();

            $count_users = $this->get_role_user_counts();

            $role_data = array();
            
            foreach ($roles as $key => $value) {
                $caps = $this->RolesHelperClass::get_capabilities($key);
                $allowed_caps_count = count(array_filter($caps));
                $denied_caps_count = count($caps) - $allowed_caps_count;
                
                $user_default_order = $key == $user_default ? 1 : (in_array($key, $default_sec_roles) ? 2 : 0);
                
                $role_data[$key] = array(
                    'role_name' => $key,
                    'display_name' => $value,
                    'is_default' => $this->RolesHelperClass::is_default_role($key),
                    'user_count' => isset($count_users[$key]) ? $count_users[$key] : 0,
                    'caps_count' => $allowed_caps_count . ' / ' . $denied_caps_count,
                    'user_default' => $user_default_order
                );

                $role_data = $this->role_data_set_edit_url($key, $editable_roles, $role_data);
                $role_data = $this->role_data_set_delete_url($key, $editable_roles, $role_data);
                $role_data = $this->role_data_set_default_url($key, $user_default_order, $role_data);
            }
            
            $this->role_data = $role_data;
            
            return $role_data;
        }
        
        /**
         * Returns number of users per role.
         * 
         * @return int[] role_name=>count
         */
        protected function get_role_user_counts() {
            if($this->role_user_counts !== null) {
                return $this->role_user_counts;
            }
            
            $count_users = count_users();
            $count_users = $count_users['avail_roles'];
            
            $this->role_user_counts = $count_users;
            
            return $count_users;
        }


        /**
         * Helper function to set role data edit url details.
         * 
         * @param string $role_name
         * @param array $editable_roles
         * @param array $role_data
         */
        protected function role_data_set_edit_url($role_name, $editable_roles, $role_data) {
            if (current_user_can('edit_roles')) {
                $role_data[$role_name]['edit_url'] = $this->get_edit_role_url($role_name);
                $role_data[$role_name]['is_editable'] = array_key_exists($role_name, $editable_roles);
            } else {
                $role_data[$role_name]['edit_url'] = null;
                $role_data[$role_name]['is_editable'] = false;
            }
            
            return $role_data;
        }
        
        /**
         * Helper function to set role data delete url details.
         * 
         * @param string $role_name
         * @param array $editable_roles
         * @param array $role_data
         */
        protected function role_data_set_delete_url($role_name, $editable_roles, $role_data) {
            if (current_user_can('delete_roles')) {
                if (!array_key_exists($role_name, $editable_roles))
                    $role_data[$role_name]['delete_url'] = null;
                else {
                    $role_data[$role_name]['delete_url'] = $this->get_delete_role_url($role_name);
                }
            } else {
                $role_data[$role_name]['delete_url'] = null;
            }
            
            return $role_data;
        }
        
        /**
         * Helper function to set role data set default role url details.
         * 
         * @param string $role_name
         * @param string $user_default_role
         * @param array $role_data
         */
        protected function role_data_set_default_url($role_name, $user_default_order, $role_data) {
            if(current_user_can('manage_options')) {
                $role_data[$role_name]['set_default_url'] = $this->get_set_default_role_url($role_name, $user_default_order);
            } else {
                $role_data[$role_name]['set_default_url'] = null;
            }
            
            return $role_data;
        }
        
        /**
         * Returns set new user default role url for the passed role with nonce.
         * 
         * @param string $role_name
         * @return string
         */
        protected function get_set_default_role_url($role_name, $user_default_order) {
            $url = add_query_arg(array(
                'new_user_default_role' => $role_name,
                'action' => $user_default_order > 0 ? 'minus' : 'plus'
            ), $this->get_self_url());
            
            return wp_nonce_url($url, 'default_role_action');
        }
        
        /**
         * Updates the new user default role based on request params.
         * Returns whether user action is set default role.
         * 
         * @return boolean
         */
        protected function set_default_role() {
            if(!empty($_GET['new_user_default_role'])) {
                check_admin_referer('default_role_action');
                
                if(!current_user_can('manage_options')) {
                    $this->WPFURE->permission_denied();
                    return true;
                }
                
                $role = trim($_GET['new_user_default_role']);
                
                if(!$this->RolesHelperClass::is_role($role) || empty($_GET['action'])) {
                    if(wp_safe_redirect($this->get_self_url())) {
                        exit();
                    }
                    return true;
                }
                
                $action = $_GET['action'];
                $default = get_option('default_role');
                $default_sec_roles = $this->UserProfile->get_new_user_default_secondary_roles();
                if($action == 'minus') {
                    if($role == $default) {
                        $r = reset($default_sec_roles);
                        update_option('default_role', $r);
                        array_splice($default_sec_roles, 0, 1);
                    }
                        
                    $index = array_search($role, $default_sec_roles);
                    if($index !== false) {
                        array_splice($default_sec_roles, $index, 1);
                    }
                } elseif($action == 'plus') {
                    if(!$this->RolesHelperClass::is_role($default)) {
                        if($role == $this->RolesHelperClass::ADMINISTRATOR_ROLE_KEY) {
                            update_option('default_role', $role);
                        } else {
                            $r = reset($default_sec_roles);
                            if($r === false) {
                                update_option('default_role', $role);
                            } else {
                                update_option('default_role', $r);
                                array_splice($default_sec_roles, 0, 1);
                                $default_sec_roles[] = $role;
                            }
                        }
                    } else {
                        if($role == $this->RolesHelperClass::ADMINISTRATOR_ROLE_KEY) {
                            update_option('default_role', $role);
                            array_unshift($default_sec_roles, $default);
                        } else {
                            $default_sec_roles[] = $role;
                        }
                    }
                }
                $this->UserProfile->set_new_user_default_secondary_roles($default_sec_roles);

                if(wp_safe_redirect($this->get_self_url() . '&default-role-updated=true')) {
                    exit();
                }
                        
                return true;
            }
            
            return false;
        }
        
        /**
         * Rename role column to user_role column.
         * 
         * @param array $columns
         * @return array
         */
        public function manage_users_columns($columns) {
            if(current_user_can('list_roles') && current_user_can('edit_roles') && array_key_exists('role', $columns)) {
                $keys = array_keys($columns);
                $keys[array_search('role', $keys)] = 'user_role';
                return array_combine($keys, $columns); 
            }
            
            return $columns;
        }
        
        /**
         * Returns edit link on user_role custom column.
         * 
         * @param string $value
         * @param string $column
         * @param int $user_id
         * @return string
         */
        public function manage_users_custom_column($value, $column, $user_id) {
            if($column === 'user_role') {
                $user = get_userdata($user_id);
                $roles = $user->roles;
                $links = array();
                if(empty($roles)) {
                    $value = __('None', 'wpfront-user-role-editor');
                } else {
                    foreach ($roles as $role) {
                        $display = $this->RolesHelperClass::get_display_name($role);
                        $url = $this->get_edit_role_url($role);
                        $links[] = "<a href='$url'>$display</a>";
                    }
                    
                    return implode(', ', $links);
                }
            }
            
            return $value;
        }
        
        /**
         * Returns 'All Roles' menu url.
         * 
         * @return string
         */
        public function get_list_roles_url() {
            return $this->get_self_url();
        }
        
        /**
         * Returns 'Add New' role menu url.
         * 
         * @return string
         */
        public function get_add_new_role_url() {
            return $this->AddEditViewClass::instance()->get_add_new_role_url();
        }
        
        /**
         * Returns edit url for the passed role name.
         * 
         * @param string $role_name
         * @return string
         */
        protected function get_edit_role_url($role_name) {
            return $this->AddEditViewClass::instance()->get_edit_role_url($role_name);
        }
        
        /**
         * Returns delete url for the passed role name.
         * 
         * @param string $role_name
         * @return string
         */
        protected function get_delete_role_url($role_name) {
            return $this->DeleteViewClass::instance()->get_delete_role_url($role_name);
        }
        
        /**
         * Returns override_edit_permissions setting value.
         * 
         * @return boolean
         */
        public function override_edit_permissions() {
            return $this->Options->get_option_boolean('override_edit_permissions', true);
        }
        
        /**
         * Hooks into editable_roles WordPress filter.
         * @param array $roles
         * @return array
         */
        public function editable_roles_filter_callback($roles) {
            if($this->RolesHelperClass::is_super_admin()) {
                return wp_roles()->roles;
            }
            
            if($this->override_edit_permissions()) {
                $roles = wp_roles()->roles;
            } else {
                unset($roles[$this->RolesHelperClass::ADMINISTRATOR_ROLE_KEY]);
            }
            
            if(!defined('WPFURE_ADMINISTRATOR_ROLE_EDIT') || !WPFURE_ADMINISTRATOR_ROLE_EDIT) {
                unset($roles[$this->RolesHelperClass::ADMINISTRATOR_ROLE_KEY]);
            }

            return $roles;
        }
        
        /**
         * Sets the help tab of list roles.
         */
        protected function set_help_tab() {
            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('This screen lists all the existing roles within your site.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('To add a new role, click the Add New button at the top of the screen or Add New in the Roles menu section.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'columns',
                    'title' => __('Columns', 'wpfront-user-role-editor'),
                    'content' => '<p><strong>'
                    . __('Display Name', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Used to display this role within this site.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Role Name', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Is used by WordPress to identify this role.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Type', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Says whether the role is a WordPress built-in role or not. There are five built-in roles.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('User Default', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Displays whether a role is the default role of a new user.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Users', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Number of users in that role.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Capabilities', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Number of capabilities that role have.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Menu Edited', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Displays whether the menu has been edited for this role. This is a pro feature.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'actions',
                    'title' => __('Actions', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Hovering over a row in the roles list will display action links that allow you to manage roles. You can perform the following actions:', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('View', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Display details about the role. You can see the capabilities assigned for that role. View link will only appear when you do not have permission to edit that role.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Edit', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Allows you to edit that role. You can see the capabilities assigned for that role and also edit them. Edit link will only appear when you have permission to edit that role.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Delete', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Allows you to delete that role. Delete action will not appear if you do not have permission to delete that role.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Default', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Allows you to set that role as the default role for new user registration.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Edit Menu', 'wpfront-user-role-editor')
                    . '</strong>: '
                    . __('Takes you to the menu editor screen for that role. You need "edit_role_menus" capability for this link to appear. This is a pro feature.', 'wpfront-user-role-editor')
                    . '</p>'
                )
            );
            
            $sidebar = array(
                array(
                    __('Documentation on Roles', 'wpfront-user-role-editor'),
                    'list-roles/'
                )
            );
            
            $this->UtilsClass::set_help_tab($tabs, $sidebar);
        }
        
        public static function get_debug_setting() {
            return array('key' => 'roles', 'label' => __('All Roles & Add New', 'wpfront-user-role-editor'), 'position' => 10, 'description' => __('Disables all roles actions and also users ability to create new role.', 'wpfront-user-role-editor'));
        }
    }
    
    WPFront_User_Role_Editor_Roles_List::load();
}
