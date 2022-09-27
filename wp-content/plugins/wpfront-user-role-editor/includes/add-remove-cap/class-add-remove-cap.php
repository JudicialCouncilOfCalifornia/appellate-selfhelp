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
 * Controller for WPFront User Role Editor Add Edit Capability
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Bulk_Edit;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;
use WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit as BulkEdit;
use \WPFront\URE\WPFront_User_Role_Editor_Debug;


require_once dirname(__FILE__) . '/template-add-remove-cap.php';

if (!class_exists('\WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Add_Remove_Cap')) {

    /**
     * Restore Role
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Add_Remove_Cap extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller implements iWPFront_URE_Bulk_Edit_Controller {

        const CAP = 'edit_roles';

        protected $RolesList;
        private $error = null;

        protected function setUp() {
            $this->_setUp('edit_roles', '');

            $this->RolesList = \WPFront\URE\Roles\WPFront_User_Role_Editor_Roles_List::instance();
        }

        protected function initialize() {
            
        }

        /**
         * Hooks into wpfront_ure_bulk_edit_controllers.
         */
        public static function register($controllers) {
            return static::instance()->_register($controllers);
        }

        protected function _register($controllers) {
            $debug = WPFront_User_Role_Editor_Debug::instance();
            $debug->add_setting('add-remove-cap', __('Add or Remove Capability', 'wpfront-user-role-editor'), 20, __('Disables add or remove capability functionality.', 'wpfront-user-role-editor'));
            
            if($debug->is_disabled('add-remove-cap')) {
                return $controllers;
            }
            
            if (!$this->in_admin_ui()) {
                return $controllers;
            }

            if (current_user_can($this->get_cap())) {
                $controllers[] = static::instance();
            }

            return $controllers;
        }

        /**
         * Called from Bulk Edit screen. Add/Remove cap logic.
         * 
         * @return void
         */
        public function load_view() {
            if (!parent::load_view()) {
                return;
            }

            add_filter('editable_roles', array($this, 'editable_roles_filter_callback'), PHP_INT_MAX, 1);

            $this->set_help_tab();

            if (!empty($_POST['submit'])) {
                check_admin_referer('add-remove-capability');

                if (empty($_POST['action_type'])) {
                    return;
                }
                $action_type = $_POST['action_type'];
                if (!in_array($action_type, array('add', 'remove'))) {
                    return;
                }

                if (!isset($_POST['capability'])) {
                    return;
                }
                $capability = trim($_POST['capability']);

                if ($capability == '') {
                    $this->error = __('Invalid capability.', 'wpfront-user-role-editor');
                    return;
                }

                if ($action_type == 'add' && !$this->RolesHelperClass::is_super_admin()) {
                    //network caps check
                    if ($this->RolesHelperClass::is_network_capability($capability)) {
                        $this->error = __('You must be a Super Admin to add this capability.', 'wpfront-user-role-editor');
                        return;
                    }
                }

                $denied_cap = !empty($_POST['denied_cap']);

                if (empty($_POST['roles_type'])) {
                    return;
                }
                $roles_type = $_POST['roles_type'];
                if (!in_array($roles_type, array('all', 'selected'))) {
                    return;
                }

                if ($roles_type == 'selected') {
                    if (empty($_POST['selected-roles'])) {
                        $_POST['selected-roles'] = array();
                    }

                    if (!is_array($_POST['selected-roles'])) {
                        return;
                    }

                    $selected_roles = $_POST['selected-roles'];

                    unset($selected_roles[RolesHelper::ADMINISTRATOR_ROLE_KEY]);

                    $selected_roles = array_keys($selected_roles);
                    $selected_roles = array_intersect($selected_roles, array_keys($this->get_editable_roles()));
                } else {
                    $selected_roles = array_keys($this->get_editable_roles());
                }

                $count = 0;

                foreach ($selected_roles as $role) {
                    $role_object = get_role($role);
                    if (empty($role_object)) {
                        continue;
                    }

                    if ($action_type == 'add') {
                        if ($role == RolesHelper::ADMINISTRATOR_ROLE_KEY) {
                            $role_object->add_cap($capability);
                        } else {
                            $role_object->add_cap($capability, !$denied_cap);
                        }
                    } else {
                        $role_object->remove_cap($capability);
                    }

                    $count++;
                }

                if ($action_type == 'add' && !in_array(RolesHelper::ADMINISTRATOR_ROLE_KEY, $selected_roles)) {
                    $role_object = get_role(RolesHelper::ADMINISTRATOR_ROLE_KEY);
                    if (!empty($role_object)) {
                        $role_object->add_cap($capability);
                        $count++;
                    }
                }

                $url = BulkEdit::instance()->get_screen_url($this) . '&changes-saved=' . $count;
                wp_safe_redirect($url);
                exit();
            }
        }

        /**
         * Displays the add/edit role view.
         */
        public function view() {
            if (!parent::view()) {
                return;
            }

            $objView = new WPFront_User_Role_Editor_Add_Remove_Cap_View();
            $objView->view($this->error);
        }

        /**
         * Hooks into editable_roles WordPress filter.
         * @param array $roles
         * @return array
         */
        public function editable_roles_filter_callback($roles) {
            if ($this->RolesHelperClass::is_super_admin()) {
                return wp_roles()->roles;
            }

            if ($this->RolesList->override_edit_permissions()) {
                $roles = wp_roles()->roles;
            }

            return $roles;
        }

        /**
         * Returns roles with their display names.
         * 
         * @return string[] Associative (name=>display).
         */
        public function get_editable_roles() {
            $roles = RolesHelper::get_names();
            if (!$this->RolesList->override_edit_permissions()) {
                unset($roles[RolesHelper::ADMINISTRATOR_ROLE_KEY]);
            }

            $editable_roles = get_editable_roles();

            return array_intersect_key($roles, $editable_roles);
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
                    . __('This screen allows you to add a capability to roles or remove a capability from roles within your site.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'action',
                    'title' => __('Action', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Select "Add Capability" to add a capability to roles.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('Select "Remove Capability" to remove a capability from roles.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'capability',
                    'title' => __('Capability', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('Use the Capability field to name the capability to be added or removed.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'denied-capability',
                    'title' => __('Denied Capability', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('If checked, this capability will be denied for the selected roles. Except "Administrator" role, which will have this capability enabled.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'roles',
                    'title' => __('Roles', 'wpfront-user-role-editor'),
                    'content' => '<p><strong>'
                    . __('All Roles', 'wpfront-user-role-editor')
                    . '</strong>: ' . __('Select "All Roles", if you want the current action to be applied to all roles within your site.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p><strong>'
                    . __('Selected Roles', 'wpfront-user-role-editor')
                    . '</strong>: ' . __('Select "Selected Roles", if you want to individually select the roles. When this option is selected, "Administrator" role is included by default on "Add Capability" action and excluded by default on "Remove Capability" action.', 'wpfront-user-role-editor')
                    . '</p>'
                )
            );

            $sidebar = array(
                array(
                    __('Documentation on Add/Remove Capability', 'wpfront-user-role-editor'),
                    'add-remove-capability/'
                )
            );

            Utils::set_help_tab($tabs, $sidebar);
        }

        /**
         * Bulk edit screen key.
         * 
         * @return string
         */
        public function get_key() {
            return 'add-remove-cap';
        }

        /**
         * Called from Bulk Edit. Text displayed on bulk edit selection.
         * 
         * @return string
         */
        public function get_option_text() {
            return __('Add or Remove Capability', 'wpfront-user-role-editor');
        }

    }

    add_filter('wpfront_ure_bulk_edit_controllers', '\WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Add_Remove_Cap::register', 1);
}