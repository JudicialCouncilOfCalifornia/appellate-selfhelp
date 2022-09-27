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
 * Controller for WPFront User Role Editor Login Redirect
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Login_Redirect;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;

require_once dirname(__FILE__) . '/entity-login-redirect.php';
require_once dirname(__FILE__) . '/template-login-redirect.php';
require_once dirname(__FILE__) . '/template-add-edit.php';
require_once dirname(__FILE__) . '/template-delete.php';

if (!class_exists('\WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect')) {

    /**
     * Login Redirect class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Login_Redirect extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {
        const MENU_SLUG = 'wpfront-user-role-editor-login-redirect';
        const CAP = 'edit_login_redirects';

        protected function setUp() {
            $this->_setUp('edit_login_redirects', 'wpfront-user-role-editor-login-redirect');
            
            $this->ViewClass = WPFront_User_Role_Editor_Login_Redirect_View::class;
        }
        
        protected function initialize() {
            add_filter('login_redirect', array($this, 'login_redirect_callback'), PHP_INT_MAX, 3);
            add_filter('logout_redirect', array($this, 'logout_redirect_callback'), PHP_INT_MAX, 3);
            add_filter('show_admin_bar', array($this, 'show_toolbar'), PHP_INT_MAX, 1);
            
            if(!$this->in_admin_ui()) {
                return;
            }
            
            $this->set_admin_menu(__('Login Redirect', 'wpfront-user-role-editor'), __('Login Redirect', 'wpfront-user-role-editor'));
            
            add_action('admin_init', array($this, 'admin_init'), 1);
        }
        
        /**
         * Hooks into login redirect filter.
         * 
         * @param string $redirect_to
         * @param string $request_from
         * @param WP_User $obj_user
         * @return string
         */
        public function login_redirect_callback($redirect_to, $request_from = '', $obj_user = NULL) {
            $entity = $this->get_current_user_role_entity($obj_user);
            
            if(empty($entity)) {
                return $redirect_to;
            } else {
                return $this->format_url($entity->url);
            }
        }
        
        /**
         * Hooks into logout redirect filter.
         * 
         * @param string $redirect_to
         * @param string $request_from
         * @param WP_User $obj_user
         * @return string
         */
        public function logout_redirect_callback($redirect_to, $request_from = '', $obj_user = NULL) {
            $entity = $this->get_current_user_role_entity($obj_user);
            
            if(empty($entity)) {
                return $redirect_to;
            } else {
                if(empty($entity->logout_url)) {
                    return $redirect_to;
                }

                return $this->format_url($entity->logout_url);
            }
        }
        
        /**
         * Hooks into admin_init to handle deny wp-admin functionality.
         */
        public function admin_init() {
            if($this->UtilsClass::doing_ajax()) {
                return;
            }

            $entity = $this->get_current_user_role_entity(null);
            if(empty($entity)) {
                return;
            }
            
            if($entity->deny_wpadmin) {
                wp_redirect($this->format_url($entity->url));
                exit();
            }
        }
        
        /**
         * Hooks into show_admin_bar filter and does disable toolbar functionality.
         * 
         * @param boolean $show
         * @return boolean
         */
        public function show_toolbar($show) {
            $entity = $this->get_current_user_role_entity(null);
            if(!empty($entity) && $entity->disable_toolbar) {
                return false;
            }
            
            return $show;
        }
        
        /**
         * Returns the entity object which corresponds to currently logged in user.
         * 
         * @param WP_User $obj_user
         * @return WPFront_User_Role_Editor_Login_Redirect_Entity
         */
        private function get_current_user_role_entity($obj_user) {
            if (empty($obj_user) || is_wp_error($obj_user)) {
                $obj_user = wp_get_current_user();
            }

            if (empty($obj_user) || is_wp_error($obj_user)) {
                return null;
            }

            if (empty($obj_user->roles) || !is_array($obj_user->roles)) {
                return null;
            }

            $roles = $obj_user->roles;
            if (in_array(RolesHelper::ADMINISTRATOR_ROLE_KEY, $roles)) {
                return null;
            }

            $entity = new WPFront_User_Role_Editor_Login_Redirect_Entity();
            $data = $entity->get_all_login_redirects();

            $allowed_roles = $this->get_allowed_roles();
            
            foreach($data as $priority => $login_redirect) {
                if(!isset($allowed_roles[$login_redirect->role])) {
                    continue;
                }
                
                if(in_array($login_redirect->role, $roles)) {
                    return $login_redirect;
                }
            }

            return null;
        }
        
        public function load_view() {
            if(!parent::load_view()) {
                return;
            }
            
            if(isset($_GET['screen']) && $_GET['screen'] === 'edit') {
                if(!empty($_GET['role'])) {
                    $role = $_GET['role'];
                    $allowed_roles = $this->get_allowed_roles();
                    if(!isset($allowed_roles[$role])) {
                        wp_safe_redirect($this->get_list_url());
                        exit();
                    }
                }
            }
            
            if(!empty($_POST['submit']) && !empty($_POST['bulk-delete'])) {
                if(!current_user_can('delete_login_redirects')) {
                    $this->WPFURE->permission_denied();
                    return;
                }
                
                check_admin_referer('delete-login-redirect');
                
                $roles = array_keys($_POST['bulk-delete']);
                $entity = new WPFront_User_Role_Editor_Login_Redirect_Entity();
                foreach ($roles as $role) {
                    $entity->delete($role);
                }
                
                wp_safe_redirect($this->get_list_url(). '&deleted=true');
                exit();
            }
            
            if(!empty($_POST['submit']) && isset($_GET['screen'])) {
                check_admin_referer('add-edit-login-redirect');
                
                if(empty($_POST['priority'])) {
                    return;
                }
                
                $priority = intval($_POST['priority']);
                if($priority < 1) {
                    return;
                }

                if(empty($_POST['url'])) {
                    return;
                }
                $url = $_POST['url'];
                
                $logout_url = '';
                if(!empty($_POST['logout_url'])) {
                    $logout_url = $_POST['logout_url'];
                }
                
                $deny_wpadmin = !empty($_POST['deny_wpadmin']);
                $disable_toolbar = !empty($_POST['disable_toolbar']);
                
                if($_GET['screen'] === 'add-new') {
                    if(empty($_POST['role'])) {
                        return;
                    }
                    $role = $_POST['role'];
                    $allowed_roles = $this->get_allowed_roles();
                    if(!isset($allowed_roles[$role])) {
                        return;
                    }
                    $entity = new WPFront_User_Role_Editor_Login_Redirect_Entity();
                    $entity->role = $role;
                } elseif($_GET['screen'] === 'edit') {
                    $role = $_GET['role'];
                    $entity = $this->get_login_redirect($role);
                    if(empty($entity)) {
                        wp_safe_redirect($this->get_list_url());
                        exit();
                    }
                }
                    
                $entity->priority = $priority;
                $entity->url = $url;
                $entity->logout_url = $logout_url;
                $entity->deny_wpadmin = $deny_wpadmin;
                $entity->disable_toolbar = $disable_toolbar;
                
                if($_GET['screen'] === 'add-new') {
                    $entity->add();
                } elseif($_GET['screen'] === 'edit') {
                    $entity->update();
                }

                wp_safe_redirect($this->get_edit_url($role). '&changes-saved=true');
                exit();
            }
            
            $screen = 'list';
            
            if(!empty($_GET['screen'])) {
                $screen = $_GET['screen'];
            } else {
                if((!empty($_POST['action']) && $_POST['action'] === 'delete') || (!empty($_POST['action2']) && $_POST['action2'] === 'delete')) {
                    $screen = 'delete';
                }
            }
            
            $this->set_help_tab($screen);
        }
        
        /**
         * Displays the login redirect view.
         */
        public function view() {
            if(!parent::view()) {
                return;
            }
            
            if(!empty($_GET['screen'])) {
                $screen = $_GET['screen'];
                
                switch ($screen) {
                    case 'add-new':
                        $objView = new WPFront_User_Role_Editor_Login_Redirect_Add_Edit_View();
                        $objView->view();
                        return;
                        
                    case 'edit':
                        $role = $_GET['role'];
                        $objView = new WPFront_User_Role_Editor_Login_Redirect_Add_Edit_View($this->get_login_redirect($role));
                        $objView->view();
                        return;
                        
                    case 'delete':
                        if(!current_user_can('delete_login_redirects')) {
                            $this->WPFURE->permission_denied();
                            return;
                        }
                        
                        $role = $_GET['role'];
                        $objView = new WPFront_User_Role_Editor_Login_Redirect_Delete_View(array($role));
                        $objView->view();
                        return;
                }
            }
            
            if((!empty($_POST['action']) && $_POST['action'] === 'delete') || (!empty($_POST['action2']) && $_POST['action2'] === 'delete')) {
                if(!empty($_POST['roles'])) {
                    if(!current_user_can('delete_login_redirects')) {
                        $this->WPFURE->permission_denied();
                        return;
                    }
                    
                    $objView = new WPFront_User_Role_Editor_Login_Redirect_Delete_View($_POST['roles']);
                    $objView->view();
                    return;
                }
            }
            
            $objView = new WPFront_User_Role_Editor_Login_Redirect_View();
            $objView->view();
        }
        
        /**
         * Returns allowed login redirects.
         * 
         * @param string $search
         * @return WPFront_User_Role_Editor_Login_Redirect_Entity[] Associative(priority=>entity)
         */
        public function get_login_redirects($search = null) {
            $entity = new WPFront_User_Role_Editor_Login_Redirect_Entity();
            $lists =  $entity->get_all_login_redirects();
            
            if(empty($search)) {
                return $lists;
            }
            
            foreach($lists as $priority => $entity) {
                $role_display = $this->get_role_display($entity->role);
                if(strpos($role_display, $search) !== false) {
                    continue;
                }
                
                $url = $this->format_url($entity->url);
                if(strpos($url, $search) !== false) {
                    continue;
                }
                
                $logout_url = $this->format_url($entity->logout_url);
                if(strpos($logout_url, $search) !== false) {
                    continue;
                }
                
                unset($lists[$priority]);
            }
            
            return $lists;
        }
        
        /**
         * Returns the entity against role name.
         * 
         * @param string $role_name
         * @return WPFront_User_Role_Editor_Login_Redirect_Entity|null
         */
        public function get_login_redirect($role_name) {
            $lists = $this->get_login_redirects();
            foreach ($lists as $priority => $entity) {
                if($entity->role === $role_name) {
                    return $entity;
                }
            }
            
            return null;
        }
        
        /**
         * Returns allowed roles for login redirect.
         * 
         * @return string[] Associative (name=>display)
         */
        public function get_allowed_roles() {
            $roles = RolesHelper::get_names();
            $std_roles = RolesHelper::get_default_rolenames();
            
            foreach ($roles as $name => $display) {
                if(!in_array($name, $std_roles)) {
                    unset($roles[$name]);
                }
            }
            
            return $roles;
        }
        
        /**
         * Returns roles info for new login redirects.
         * 
         * @return array Array(role_name => (object)[(string)display_name, (bool)allowed]
         */
        public function get_roles_info_for_new() {
            $roles = RolesHelper::get_names();
            unset($roles[RolesHelper::ADMINISTRATOR_ROLE_KEY]);
            
            $login_redirects = $this->get_login_redirects(null);
            
            $existing = array();
            foreach ($login_redirects as $priority => $entity) {
                $existing[$entity->role] = true;
            }
            
            $allowed = $this->get_allowed_roles();
            
            $roles_info = array();
            foreach ($roles as $role_name => $display) {
                if(isset($existing[$role_name])) {
                    continue;
                }
                
                $roles_info[$role_name] = (object)array('display_name' => $display, 'allowed' => isset($allowed[$role_name]));
            }
            
            return $roles_info;
        }
        
        /**
         * Returns the next priority for add.
         * 
         * @return int
         */
        public function get_next_priority() {
            $entity = new WPFront_User_Role_Editor_Login_Redirect_Entity();
            return $entity->get_next_priority();
        }
        
        /**
         * Returns the display text for a role.
         * 
         * @param string $role_name
         * @return string
         */
        public function get_role_display($role_name) {
            $names = RolesHelper::get_names();
            if(isset($names[$role_name])) {
                return $names[$role_name];
            }
            
            return $role_name;
        }
        
        /**
         * Formats the url based on home url.
         * 
         * @param string $url
         * @return string
         */
        public function format_url($url) {
            if(empty($url))
                return '';
            
            $url = strtolower($url);

            if (strpos($url, '://') > -1)
                return $url;

            return home_url($url);
        }
        
        /**
         * Return login redirect list url.
         * 
         * @return string
         */
        public function get_list_url() {
            return $this->get_self_url();
        }
        
        /**
         * Returns the add new login redirect URL.
         * 
         * @return string
         */
        public function get_add_new_url() {
            return $this->get_self_url(['screen' => 'add-new']);
        }
        
        /**
         * Returns the edit login redirect URL for a role.
         * 
         * @param string $role
         * @return string
         */
        public function get_edit_url($role) {
            return $this->get_self_url(['screen' => 'edit', 'role' => $role]);
        }
        
        /**
         * Returns the delete login redirect URL for a role.
         * 
         * @param string $role
         * @return string
         */
        public function get_delete_url($role) {
            return $this->get_self_url(['screen' => 'delete', 'role' => $role]);
        }
        
        /**
         * Sets the help tab
         * 
         * @param string $screen
         */
        protected function set_help_tab($screen) {
            switch($screen) {
                case 'list':
                    $tabs = array(
                        array(
                            'id' => 'overview',
                            'title' => __('Overview', 'wpfront-user-role-editor'),
                            'content' => '<p>'
                            . __('Use this functionality to redirect a user to a specific page after they login based on their role.', 'wpfront-user-role-editor')
                            . '</p>'
                            . '<p>'
                            . __('In addition, you can also deny the user access to WP-ADMIN and remove the toolbar (admin bar) from the front end.', 'wpfront-user-role-editor')
                            . '</p>'
                        ),
                        array(
                            'id' => 'columns',
                            'title' => __('Columns', 'wpfront-user-role-editor'),
                            'content' => '<p>'
                            . sprintf('<b>%s</b>: %s', __('Role', 'wpfront-user-role-editor'), __('The role of the user to qualify for this redirect.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Priority', 'wpfront-user-role-editor'), __('When a user has multiple roles, the role configuration with the highest priority will be selected.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Login Redirect URL', 'wpfront-user-role-editor'), __('The URL where the user will be redirected after login or on WP-ADMIN access if denied.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Logout Redirect URL', 'wpfront-user-role-editor'), __('The URL where the user will be redirected after logout.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('WP-ADMIN', 'wpfront-user-role-editor'), __('Displays whether user has access to WP-ADMIN.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Toolbar', 'wpfront-user-role-editor'), __('Displays whether user will see toolbar on front end.', 'wpfront-user-role-editor'))
                            . '</p>'
                        )
                    );
                    break;
                
                case 'add-new':
                case 'edit':
                    $tabs = array(
                        array(
                            'id' => 'overview',
                            'title' => __('Overview', 'wpfront-user-role-editor'),
                            'content' => '<p>'
                            . __('Add/Edit a new login redirect.', 'wpfront-user-role-editor')
                            . '</p>'
                        ),
                        array(
                            'id' => 'fields',
                            'title' => __('Fields', 'wpfront-user-role-editor'),
                            'content' => '<p>'
                            . sprintf('<b>%s</b>: %s', __('Role', 'wpfront-user-role-editor'), __('The role of the user to qualify for this redirect.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Priority', 'wpfront-user-role-editor'), __('When a user has multiple roles, the role configuration with the highest priority will be selected.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Login Redirect URL', 'wpfront-user-role-editor'), __('The URL where the user will be redirected after login or on WP-ADMIN access if denied.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Logout Redirect URL', 'wpfront-user-role-editor'), __('The URL where the user will be redirected after logout.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Deny WP-ADMIN', 'wpfront-user-role-editor'), __('If enabled user will be redirected to URL on WP-ADMIN access.', 'wpfront-user-role-editor'))
                            . '</p>'
                            . '<p>'
                            . sprintf('<b>%s</b>: %s', __('Disable Toolbar', 'wpfront-user-role-editor'), __('If enabled user will not see toolbar on front end.', 'wpfront-user-role-editor'))
                            . '</p>'
                        )
                    );
                    break;
                
                case 'delete':
                    $tabs = array(
                        array(
                            'id' => 'overview',
                            'title' => __('Overview', 'wpfront-user-role-editor'),
                            'content' => '<p>'
                            . __('Click "Confirm Delete" to delete displayed configurations.', 'wpfront-user-role-editor')
                            . '</p>'
                        )
                    );
                    break;
            }
            
            
            
            $sidebar = array(
                array(
                    __('Documentation on Login Redirect', 'wpfront-user-role-editor'),
                    'login-redirect/'
                )
            );
            
            $this->UtilsClass::set_help_tab($tabs, $sidebar);
        }
        
        public static function get_debug_setting() {
            return array('key' => 'login-redirect', 'label' =>  __('Login Redirect', 'wpfront-user-role-editor'), 'position' => 60, 'description' => __('Disables the login redirect functionality.', 'wpfront-user-role-editor'));
        }
    }
    
    WPFront_User_Role_Editor_Login_Redirect::load();
    
}