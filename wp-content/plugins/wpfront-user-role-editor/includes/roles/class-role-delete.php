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
 * Controller for WPFront User Role Editor Delete Role
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Roles;

if (!defined('ABSPATH')) {
    exit();
}

require_once dirname(__FILE__) . '/template-role-delete.php';

if (!class_exists('\WPFront\URE\Roles\WPFront_User_Role_Editor_Role_Delete')) {

    /**
     * Delete Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Role_Delete extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {
        
        protected $role_data;
        
        protected $RolesList;

        protected function setUp() {
            $this->_setUp('delete_roles');
            
            $this->ViewClass = WPFront_User_Role_Editor_Role_Delete_View::class;
            $this->RolesList = WPFront_User_Role_Editor_Roles_List::instance();
        }
        
        protected function initialize() {
        }
        
        /**
         * Performs delete roles functionality.
         * Returns whether current user action is delete role.
         * 
         * @return boolean
         */
        public function delete_role() {
            if(!empty($_POST['confirm-delete'])) {
                check_admin_referer('delete-roles');
                
                if(!current_user_can($this->get_cap())) {
                    $this->WPFURE->permission_denied();
                    return true;
                }
                
                $roles = $this->get_delete_data();
                
                foreach ($roles as $key => $value) {
                    if($value->is_deletable) {
                        remove_role($key);
                    }
                }
                
                if(wp_safe_redirect($this->RolesList->get_list_roles_url(). '&roles-deleted=true')) {
                    exit();
                }
                
                return true;
            }
            
            if(!empty($_GET['delete_role'])) {
                return true;
            }
            
            if(!empty($_POST['action_top']) && $_POST['action_top'] === 'delete' && !empty($_POST['selected-roles'])) {
                return true;
            }
            
            if(!empty($_POST['action_bottom']) && $_POST['action_bottom'] === 'delete' && !empty($_POST['selected-roles'])) {
                return true;
            }
            
            return false;
        }
        
        /**
         * Displays delete roles view.
         */
        public function view() {
            if(!current_user_can($this->get_cap())) {
                $this->WPFURE->permission_denied();
                return;
            }
            
            $objView = new $this->ViewClass();
            $objView->view();
        }
        
        /**
         * Returns an array of role names submitted for delete.
         * 
         * @return array
         */
        protected function get_submitted_roles() {
            if(!empty($_POST['confirm-delete'])) {
                return array_keys($_POST['delete-roles']);
            }
            
            if(!empty($_GET['delete_role'])) {
                return array(trim($_GET['delete_role']));
            }
            
            if(!empty($_POST['action_top']) && $_POST['action_top'] === 'delete' && !empty($_POST['selected-roles'])) {
                return array_keys($_POST['selected-roles']);
            }
            
            if(!empty($_POST['action_bottom']) && $_POST['action_bottom'] === 'delete' && !empty($_POST['selected-roles'])) {
                return array_keys($_POST['selected-roles']);
            }
            
            return array();
        }
        
        /**
         * Returns an array containing role data, with delete permission information.
         * 
         * @return array
         */
        public function get_delete_data() {
            if($this->role_data !== null) {
                return $this->role_data;
            }
            
            $this->role_data = array();
            $editable_roles = get_editable_roles();
            $delete_roles = $this->get_submitted_roles();

            $user = wp_get_current_user();
            $user_roles = $user->roles;
            
            foreach ($delete_roles as $role) {
                if ($this->RolesHelperClass::is_role($role)) {
                    $status_message = '';
                    $is_deletable = true;
                    
                    if(!$this->RolesHelperClass::is_super_admin()) {
                        if (!array_key_exists($role, $editable_roles)) {
                            $status_message = __('This role cannot be deleted: Permission denied.', 'wpfront-user-role-editor');
                            $is_deletable = false;
                        } elseif (in_array($role, $user_roles)) {
                            $status_message = __('Current user\'s role cannot be deleted.', 'wpfront-user-role-editor');
                            $is_deletable = false;
                        }
                    }
                    
                    $this->role_data[$role] = (OBJECT) array(
                                'name' => $role,
                                'display_name' => $this->RolesHelperClass::get_display_name($role),
                                'is_deletable' => $is_deletable,
                                'status_message' => $status_message
                    );
                }
            }
            
            return $this->role_data;
        }
        
        /**
         * Returns the delete role url for the passed role name.
         * 
         * @param string $role_name
         * @return string
         */
        public function get_delete_role_url($role_name) {
            return menu_page_url($this->RolesList->get_menu_slug(), false) . '&delete_role=' . $role_name;
        }
        
        /**
         * Sets the help tab of delete roles.
         */
        public function set_help_tab() {
            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('This screen allows you to delete roles from your WordPress site.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('Use the Roles List screen to select the roles you want to delete. You can delete individual roles using the Delete row action link or delete multiple roles at the same time using the bulk action.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('You cannot delete administrator role, current user’s role and roles you do not have permission to.', 'wpfront-user-role-editor')
                    . '</p>'
                )
            );
            
            $sidebar = array(
                array(
                    __('Documentation on Delete Roles', 'wpfront-user-role-editor'),
                    'delete-role/'
                )
            );
            
            $this->UtilsClass::set_help_tab($tabs, $sidebar);
        }
        
    }
    
}