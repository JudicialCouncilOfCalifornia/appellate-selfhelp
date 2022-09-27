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
 * Controller for WPFront User Role Editor Add/Edit Role
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Roles;

if (!defined('ABSPATH')) {
    exit();
}

use \WPFront\URE\WPFront_User_Role_Editor_Debug;

require_once dirname(__FILE__) . '/template-role-add-edit.php';

if (!class_exists('\WPFront\URE\Roles\WPFront_User_Role_Editor_Role_Add_Edit')) {

    /**
     * Add Edit Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Role_Add_Edit extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {
        
        protected $mode = 'EDIT';
        protected $role_data = null;
        
        protected $RolesList;

        protected function setUp() {
            $this->_setUp('edit_roles', 'wpfront-user-role-editor-add-new');
            
            $this->ViewClass = WPFront_User_Role_Editor_Role_Add_Edit_View::class;
            $this->RolesList = WPFront_User_Role_Editor_Roles_List::instance();
        }
        
        public function get_cap() {
            if($this->mode === 'ADD') {
                return 'create_roles';
            } else {
                return 'edit_roles';
            }
        }
        
        protected function initialize() {
            $debug = WPFront_User_Role_Editor_Debug::instance();
            
            $debug_setting = $this->RolesList->get_debug_setting();
            $debug->add_setting($debug_setting);
            
            if($debug->is_disabled($debug_setting['key'])) {
                return;
            }
            
            add_action('wp_before_admin_bar_render', array($this, 'admin_bar_menu'), 1);
            add_action('admin_init', array($this, 'admin_init'));
            
            if(!$this->in_admin_ui()) {
                return;
            }
            
            $this->set_admin_menu(__('Add New Role', 'wpfront-user-role-editor'), __('Add New', 'wpfront-user-role-editor'));
            
            add_filter('wpfront_ure_capability_edit_role_menus_functionality_enabled', '__return_false');
            add_filter('wpfront_ure_capability_edit_content_shortcodes_functionality_enabled', '__return_false');
            add_filter('wpfront_ure_capability_delete_content_shortcodes_functionality_enabled', '__return_false');
        }
        
        /**
         * Adds ajax functions on admin_init
         */
        public function admin_init() {
            add_action('wp_ajax_wpfront_user_role_editor_copy_capabilities', array($this, 'copy_capabilities_callback'), 10, 0);
        }
        
        public function admin_print_scripts() {
            parent::admin_print_scripts();
            
            wp_enqueue_script('postbox');
            wp_enqueue_script('jquery-ui-draggable');
        }
        
        /**
         * Hooks into admin_bar_menu to add Roles under New in tool bar.
         * 
         * @param WP_Admin_Bar $wp_admin_bar
         */
        public function admin_bar_menu() {
            if(current_user_can('create_roles')) {
                global $wp_admin_bar;
                $wp_admin_bar->add_menu(array(
                    'parent' => 'new-content',
                    'id' => $this->get_menu_slug(),
                    'title' => __('Role', 'wpfront-user-role-editor'),
                    'href' => admin_url('admin.php?page=' . $this->get_menu_slug()))
                );
            }
        }
        
        public function load_view() {
            $this->mode = 'ADD';
            
            $this->edit_role();
            $this->set_add_help_tab();
        }
        
        /**
         * Returns whether the current user action is edit role.
         * 
         * @return boolean
         */
        public function edit_role() {
            if(!empty($_POST['createrole'])) {
                check_admin_referer('add-new-role');
                
                if($this->mode === 'ADD') {
                    if(!current_user_can($this->get_cap())) {
                        $this->WPFURE->permission_denied();
                        return false;
                    }
                    
                    $role_data = $this->get_role_data();
                    if(!empty($role_data['error'])) {
                        return false;
                    }
                    
                    $this->RolesHelperClass::add_role($role_data['role_name'], $role_data['display_name'], $role_data['capabilities']);
                    
                    if(wp_safe_redirect($this->get_edit_role_url($role_data['role_name']) . '&role-added=true')) {
                        exit();
                    }
                    
                    return false;
                } else {
                    if(!current_user_can($this->get_cap())) {
                        $this->WPFURE->permission_denied();
                        return true;
                    }
                    
                    $role_data = $this->get_role_data();
                    if(empty($role_data)) {
                        if(wp_safe_redirect($this->RolesList->get_list_roles_url())) {
                            exit();
                        }
                        return true;
                    }
                    
                    if($role_data['is_readonly']) {
                        $this->WPFURE->permission_denied();
                        return true;
                    }
                    
                    if(!empty($role_data['error'])) {
                        return true;
                    }
                    
                    $this->RolesHelperClass::update_role($role_data['role_name'], $role_data['display_name'], $role_data['capabilities']);
                    
                    if(wp_safe_redirect($this->get_edit_role_url($role_data['role_name']) . '&role-updated=true')) {
                        exit();
                    }
                    
                    return true;
                }
            }
            
            if(!empty($_GET['edit_role'])) {
                if(empty($this->get_role_data())) {
                    if(wp_safe_redirect($this->RolesList->get_list_roles_url())) {
                        exit();
                    }
                }
                return true;
            }
            
            return false;
        }
        
        /**
         * Returns role information as array.
         * 
         * @return array
         */
        public function get_role_data() {
            if($this->role_data !== null) {
                return $this->role_data;
            }
            
            if($this->mode === 'ADD') {
                $error = null;
                $role_name = '';
                $display_name = '';
                $is_readonly = false;
                $is_role_name_valid = true;
                $is_display_name_valid = true;
                $capabilities = array();
                
                if(!empty($_POST['createrole'])) {
                    $posted = $this->get_role_name_posted();
                    $role_name = $posted[0];
                    $is_role_name_valid = $posted[1];
                    $error = $posted[2];
                    
                    $posted = $this->get_display_name_posted();
                    $display_name = $posted[0];
                    $is_display_name_valid = $posted[1];
                    $error = $error ?? $posted[2];

                    $capabilities = $this->get_capabilities_posted();
                }
            } else {
                if(empty($_GET['edit_role'])) {
                    return null;
                }
                
                $role_name = $_GET['edit_role'];
                $is_role_name_valid = true;
                if(!$this->RolesHelperClass::is_role($role_name)) {
                    return null;
                }
                
                $display_name = $this->RolesHelperClass::get_display_name($role_name);
                $is_display_name_valid = true;
                
                $capabilities = $this->RolesHelperClass::get_capabilities($role_name);
                
                $error = null;
                
                if(!empty($_POST['createrole'])) {
                    $posted = $this->get_display_name_posted();
                    $display_name = $posted[0];
                    $is_display_name_valid = $posted[1];
                    $error = $posted[2];
                    
                    $capabilities = $this->get_capabilities_posted();
                }
                
                $is_readonly = !$this->is_role_editable($role_name);
            }
            
            $this->role_data = array();
            
            $this->role_data['role_name'] = $role_name;
            $this->role_data['is_role_name_valid'] = $is_role_name_valid;
            $this->role_data['display_name'] = $display_name;
            $this->role_data['is_display_name_valid'] = $is_display_name_valid;
            $this->role_data['capabilities'] = $capabilities;
            $this->role_data['is_readonly'] = $is_readonly;
            $this->role_data['error'] = $error;
            
            return $this->role_data;
        }
        
        protected function get_role_name_posted() {
            $role_name = '';
            $is_role_name_valid = true;
            $error = null;
            
            if(!empty($_POST['role_name'])) {
                $role_name = strtolower(preg_replace('/\W/', '', preg_replace('/ /', '_', trim($_POST['role_name']))));
                if($role_name === '') {
                    $is_role_name_valid = false;
                    $error = __('Role name cannot be empty.', 'wpfront-user-role-editor');
                } else {
                    if($this->is_role_exists($role_name)) {
                        $is_role_name_valid = false;
                        $error = __('This role already exists in this site.', 'wpfront-user-role-editor');
                    }
                }
            } else {
                $is_role_name_valid = false;
                $error = __('Role name cannot be empty.', 'wpfront-user-role-editor');
            }
            
            return [$role_name, $is_role_name_valid, $error];
        }
        
        protected function get_display_name_posted() {
            $display_name = '';
            $is_display_name_valid = true;
            $error = null;
            
            if(!empty($_POST['display_name'])) {
                $display_name = trim($_POST['display_name']);
                if($display_name === '') {
                    $is_display_name_valid = false;
                    $error = __('Display name cannot be empty.', 'wpfront-user-role-editor');
                }
            } else {
                $is_display_name_valid = false;
                $error = __('Display name cannot be empty.', 'wpfront-user-role-editor');
            }
            
            return [$display_name, $is_display_name_valid, $error];
        }
        
        protected function get_capabilities_posted() {
            $capabilities = array();
                    
            if(!empty($_POST['capabilities'])) {
                foreach ($_POST['capabilities'] as $key => $value) {
                    if(isset($value['allow'])) {
                        $capabilities[$key] = true;
                    } elseif(isset($value['deny'])) {
                        $capabilities[$key] = false;
                    }
                }
            }
            
            return $capabilities;
        }
        
        protected function is_role_exists($role_name) {
            return $this->RolesHelperClass::is_role($role_name);
        }
        
        protected function is_role_editable($role_name) {
            $editable_roles = get_editable_roles();
            return array_key_exists($role_name, $editable_roles);
        }
        
        /**
         * Returns capability group info.
         * 
         * @return array
         */
        public function get_meta_box_groups() {
            $caps_groups = $this->RolesHelperClass::get_capabilty_groups();
            $role_data = $this->get_role_data();
            
            $caps_data = array();
            
            foreach ($caps_groups as $group => $obj) {
                $caps = $this->RolesHelperClass::get_group_capabilities($obj);
                
                if(empty($caps)) {
                    continue;
                }
                
                $is_disabled = false;
                if(!empty($role_data)) {
                    $is_disabled = $role_data['is_readonly'];
                }
                
                if($caps === 'defaulted') {
                    $is_disabled = true;
                } else {
                    if($group == 'network' && !$this->RolesHelperClass::can_set_network_capability()) {
                        $is_disabled = true;
                    }
                }
                
                $caps_data[$group] = (OBJECT) array(
                            'caps' => $caps,
                            'display_name' => $obj->label,
                            'deprecated' => false,
                            'disabled' => $is_disabled,
                            'hidden' => false,
                            'key' => $group,
                            'group_obj' => $obj,
                            'mode' => $this->mode
                );
            }
            
            return $caps_data;
        }
        
        /**
         * Displays the add/edit role view.
         */
        public function view() {
            $cap = $this->get_cap();
            
            if(!current_user_can($cap)) {
                $this->WPFURE->permission_denied();
                return;
            }
            
            $objView = new $this->ViewClass();
            $objView->view();
        }
        
        /**
         * Returns the 'Add New' role menu url.
         * 
         * @return string
         */
        public function get_add_new_role_url() {
            return menu_page_url($this->get_menu_slug(), false);
        }
        
        /**
         * Retunrs the edit url for the passed role.
         * 
         * @param string $role_name
         * @return string
         */
        public function get_edit_role_url($role_name) {
            return menu_page_url($this->RolesList->get_menu_slug(), false) . '&edit_role=' . $role_name;
        }
        
        /**
         * Returns json string for copy capabilities ajax call back.
         * 
         * @return string
         */
        public function copy_capabilities_callback() {
            check_ajax_referer('copy-capabilities', 'nonce');

            if (empty($_POST['role'])) {
                wp_die('{}');
            }

            $caps = $this->RolesHelperClass::get_capabilities($_POST['role']);
            if ($caps == null) {
                wp_die('{}');
            }
            
            wp_die(json_encode($caps));
        }
        
        /**
         * Sets the help tab of edit roles.
         */
        protected function set_add_help_tab() {
            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('This screen allows you to add a new role within your site.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('You can copy capabilities from existing roles using the Copy from drop down list. Select the role you want to copy from, then click Apply to copy the capabilities. You can select or deselect capabilities even after you copy.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'displayname',
                    'title' => __('Display Name', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Use the Display Name field to set the display name for the new role. WordPress uses display name to display this role within your site. This field is required.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'rolename',
                    'title' => __('Role Name', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Use the Role Name field to set the role name for the new role. WordPress uses role name to identify this role within your site. Once set role name cannot be changed. This field is required. This plugin will auto populate role name from the display name you have given, but you can change it.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'capabilities',
                    'title' => __('Capabilities', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Capabilities are displayed as different groups for easy access. The Roles section displays capabilities created by this plugin. The Other Capabilities section displays non-standard capabilities within your site. These are usually created by plugins and themes. Use the check boxes to select the capabilities required for this new role.', 'wpfront-user-role-editor')
                    . '</p>'
                )
            );
            
            $sidebar = array(
                array(
                    __('Documentation on Add New Role', 'wpfront-user-role-editor'),
                    'add-role/'
                )
            );
            
            $this->UtilsClass::set_help_tab($tabs, $sidebar);
        }
        
        /**
         * Sets the help tab of edit roles.
         */
        public function set_edit_help_tab() {
            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('This screen allows you to edit a role within your site.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('You can copy capabilities from existing roles using the Copy from drop down list. Select the role you want to copy from, then click Apply to copy the capabilities. You can select or deselect capabilities even after you copy.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'displayname',
                    'title' => __('Display Name', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Use the Display Name field to edit display name of the role. WordPress uses display name to display this role within your site. This field is required.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'rolename',
                    'title' => __('Role Name', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Role Name is read only. WordPress uses role name to identify this role within your site.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'capabilities',
                    'title' => __('Capabilities', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Capabilities are displayed as different groups for easy access. The Roles section displays capabilities created by this plugin. The Other Capabilities section displays non-standard capabilities within your site. These are usually created by plugins and themes. Use the check boxes to select the capabilities required.', 'wpfront-user-role-editor')
                    . '</p>'
                )
            );
            
            $sidebar = array(
                array(
                    __('Documentation on Edit Role', 'wpfront-user-role-editor'),
                    'edit-role/'
                )
            );
            
            $this->UtilsClass::set_help_tab($tabs, $sidebar);
        }
    }
    
    add_action('wpfront_ure_init', '\WPFront\URE\Roles\WPFront_User_Role_Editor_Role_Add_Edit::init');
    
}