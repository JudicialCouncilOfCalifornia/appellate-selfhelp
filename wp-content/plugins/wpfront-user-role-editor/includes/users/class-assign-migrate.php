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
 * Controller for WPFront User Role Editor Assign Migrate
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
use \WPFront\URE\WPFront_User_Role_Editor_Debug;

require_once dirname(__FILE__) . '/template-assign-migrate.php';

if (!class_exists('\WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_Migrate')) {

    /**
     * Assign Migrate class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Assign_Migrate extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {

        const MENU_SLUG = 'wpfront-user-role-editor-assign-roles';
        const CAP = 'promote_users';

        /**
         *
         * @var \WP_User[]
         */
        private $users = null;

        /**
         *
         * @var string[] 
         */
        private $primary_roles = null;

        /**
         *
         * @var string[] name => display
         */
        private $secondary_roles = null;

        /**
         *
         * @var string
         */
        private $error = null;

        protected function setUp() {
            $this->_setUp('promote_users', 'wpfront-user-role-editor-assign-roles');
        }

        /**
         * Hooks into wpfront_ure_init.
         */
        public function initialize() {

            $debug = WPFront_User_Role_Editor_Debug::instance();
            $debug->add_setting('assign-migrate', __('Assign/Migrate Role', 'wpfront-user-role-editor'), 210, __('Disables role assignment and role migration functionalities.', 'wpfront-user-role-editor'));

            if ($debug->is_disabled('assign-migrate')) {
                return;
            }
            
            add_action('admin_init', array($this, 'admin_init'));

            if (!$this->in_admin_ui()) {
                return;
            }

            $this->set_admin_menu(__('Assign Roles | Migrate Users', 'wpfront-user-role-editor'), __('Assign / Migrate', 'wpfront-user-role-editor'));

            add_filter('user_row_actions', array($this, 'user_row_actions'), 10, 2);
        }

        /**
         * Adds ajax functions on admin_init
         */
        public function admin_init() {
            add_action('wp_ajax_wpfront_user_role_editor_assign_roles_user_autocomplete', array($this, 'assign_roles_user_autocomplete_callback'), 10, 0);
        }

        public function admin_print_scripts() {
            parent::admin_print_scripts();

            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-autocomplete');
        }

        public function admin_menu() {
            $page_hook_suffix = add_users_page($this->menu_title, $this->menu_link, $this->get_cap(), $this->get_menu_slug(), array($this, 'view'));

            $this->add_menu_hooks($page_hook_suffix);
        }

        /**
         * Hooks into user_row_actions filter to add Assign Roles link.
         * 
         * @param string[] $actions
         * @param \WP_User $user
         * @return string[]
         */
        public function user_row_actions($actions, $user) {
            if (current_user_can(self::CAP) && $user->ID !== wp_get_current_user()->ID && current_user_can('promote_user', $user->ID)) {
                $actions['assign_roles'] = sprintf('<a href="%s">%s</a>', $this->get_self_url($user->ID), __('Assign Roles', 'wpfront-user-role-editor'));
            }

            return $actions;
        }

        public function load_view() {
            if (!parent::load_view()) {
                return;
            }

            if (!empty($_POST['assign'])) {
                check_admin_referer('assign-roles');

                if (empty($_POST['assign-user'])) {
                    $this->error = __('Invalid user.', 'wpfront-user-role-editor');
                    return;
                }

                $user_id = $_POST['assign-user-id'];
                $user = get_userdata($user_id);

                if (empty($user)) {
                    $this->error = __('Invalid user.', 'wpfront-user-role-editor');
                    return;
                }

                if ($user->ID === wp_get_current_user()->ID) {
                    $this->error = __('Logged in user\'s role can not be changed.', 'wpfront-user-role-editor');
                    return;
                }

                $primary_role = '';
                if (!empty($_POST['primary-role'])) {
                    $primary_role = $_POST['primary-role'];
                }

                $primary_roles = $this->get_assign_roles_primary_roles();
                if (empty($primary_roles[$primary_role])) {
                    $this->error = __('Invalid primary role specified.', 'wpfront-user-role-editor');
                    return;
                }

                $secondary_roles = array();
                if (!empty($_POST['secondary-roles'])) {
                    $secondary_roles = $_POST['secondary-roles'];
                }

                $allowed_secondary_roles = $this->get_assign_roles_secondary_roles();
                foreach ($secondary_roles as $name => $value) {
                    if (empty($allowed_secondary_roles[$name])) {
                        $this->error = __('Invalid secondary role specified.', 'wpfront-user-role-editor');
                        return;
                    }
                }

                $user->set_role($primary_role);

                foreach ($secondary_roles as $name => $value) {
                    $user->add_role($name);
                }

                $url = $this->get_self_url($user_id) . '&roles-assigned=true';
                wp_safe_redirect($url);
                exit();
            }

            if (!empty($_POST['migrate'])) {
                check_admin_referer('migrate-users');

                $from_primary_role = '';
                if (!empty($_POST['migrate-from-primary-role'])) {
                    $from_primary_role = $_POST['migrate-from-primary-role'];
                }

                $primary_roles = $this->get_migrate_from_primary_roles();
                if (empty($primary_roles[$from_primary_role])) {
                    $this->error = __('Invalid primary role specified.', 'wpfront-user-role-editor');
                    return;
                }

                $primary_role = '';
                if (!empty($_POST['primary-role'])) {
                    $primary_role = $_POST['primary-role'];
                }

                $primary_roles = $this->get_migrate_to_primary_roles();
                if (empty($primary_roles[$primary_role])) {
                    $this->error = __('Invalid primary role specified.', 'wpfront-user-role-editor');
                    return;
                }

                $secondary_roles = array();
                if (!empty($_POST['secondary-roles'])) {
                    $secondary_roles = $_POST['secondary-roles'];
                }

                $allowed_secondary_roles = $this->get_migrate_secondary_roles();
                foreach ($secondary_roles as $name => $value) {
                    if (empty($allowed_secondary_roles[$name])) {
                        $this->error = __('Invalid secondary role specified.', 'wpfront-user-role-editor');
                        return;
                    }
                }

                $users = $this->get_users();
                $count = 0;
                foreach ($users as $user) {
                    if ($user->ID === wp_get_current_user()->ID) {
                        continue;
                    }

                    $roles = $user->roles;
                    $user_primary = '';
                    if (!empty($roles)) {
                        $user_primary = reset($roles);
                    }

                    if ($user_primary === $from_primary_role) {
                        $user->set_role($primary_role);
                        foreach ($secondary_roles as $name => $value) {
                            $user->add_role($name);
                        }

                        $count++;
                    }
                }

                $url = $this->get_self_url() . "&users-migrated=$count";
                wp_safe_redirect($url);
                exit();
            }

            $this->set_help_tab();
        }

        /**
         * Displays the login redirect view.
         */
        public function view() {
            if (!parent::view()) {
                return;
            }

            $objView = new WPFront_User_Role_Editor_Assign_Migrate_View();
            $objView->view();
        }

        /**
         * Returns array of user objects who can be assigned.
         * 
         * @return \WP_User[]
         */
        public function get_users() {
            if ($this->users === null) {
                $users = get_users(array('exclude' => array(wp_get_current_user()->ID)));
                $this->users = array_values(array_filter($users, array($this, 'filter_promote_user')));
            }

            return $this->users;
        }

        /**
         * Array filter function to find users who can be assigned.
         * 
         * @param \WP_User $user
         * @return boolean
         */
        public function filter_promote_user($user) {
            return current_user_can('promote_user', $user->ID);
        }

        /**
         * Returns assignable primary roles.
         * 
         * @return string[] name=>display
         */
        protected function get_primary_roles() {
            if ($this->primary_roles === null) {
                $roles = RolesHelper::get_names();
                $this->primary_roles = $roles;
            }

            return $this->primary_roles;
        }

        /**
         * Returns primary roles after applying filter.
         * 
         * @param string $filter
         * @return string[] name=>display
         */
        protected function get_primary_roles_filtered($filter) {
            $roles = $this->get_primary_roles();
            $roles = apply_filters($filter, $roles);
            $roles[''] = '&mdash;' . __('No role for this site', 'wpfront-user-role-editor') . '&mdash;';
            return $roles;
        }

        /**
         * Returns primary roles list for assign roles.
         * 
         * @return string[] name=>display
         */
        public function get_assign_roles_primary_roles() {
            return $this->get_primary_roles_filtered('wpfront_ure_assign_user_roles_primary_roles');
        }

        /**
         * Returns from primary roles list for migrate users.
         * 
         * @return string[] name=>display
         */
        public function get_migrate_from_primary_roles() {
            return $this->get_primary_roles_filtered('wpfront_ure_migrate_users_from_primary_roles');
        }

        /**
         * Returns to primary roles list for migrate users.
         * 
         * @return string[] name=>display
         */
        public function get_migrate_to_primary_roles() {
            return $this->get_primary_roles_filtered('wpfront_ure_migrate_users_to_primary_roles');
        }

        /**
         * Returns assignable secondary roles.
         * 
         * @return string[] name=>display
         */
        public function get_secondary_roles() {
            if ($this->secondary_roles === null) {
                $roles = $this->get_primary_roles();
                unset($roles[RolesHelper::ADMINISTRATOR_ROLE_KEY]);
                $this->secondary_roles = $roles;
            }

            return $this->secondary_roles;
        }

        /**
         * Returns secondary roles list for assign roles.
         * 
         * @return string[] name=>display
         */
        public function get_assign_roles_secondary_roles() {
            $roles = $this->get_secondary_roles();

            $roles = apply_filters('wpfront_ure_assign_user_roles_secondary_roles', $roles);

            return $roles;
        }

        /**
         * Returns secondary roles list for assign roles.
         * 
         * @return string[] name=>display
         */
        public function get_migrate_secondary_roles() {
            $roles = $this->get_secondary_roles();

            $roles = apply_filters('wpfront_ure_migrate_users_to_secondary_roles', $roles);

            return $roles;
        }

        /**
         * Returns the current error.
         * 
         * @return string
         */
        public function get_error_string() {
            return $this->error;
        }

        /**
         * Returns self url.
         * 
         * @param int $user_id
         * @return string
         */
        public function get_self_url($user_id = null) {
            $append = array();

            if (!empty($user_id)) {
                $append['user'] = $user_id;
            }

            return parent::get_self_url($append);
        }

        /**
         * Sets the help tab
         * 
         * @param string $screen
         */
        protected function set_help_tab() {
            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('This screen allows you to assign multiple roles to a user and also allows you to migrate users from a role to another role.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'assignroles',
                    'title' => __('Assign Roles', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('To assign multiple roles to a user, select that user within the User drop down list and select the primary role you want for that user using the Primary Role drop down list. Select the secondary roles using the check boxes below, then click Assign Roles.', 'wpfront-user-role-editor')
                    . '</p>'
                ),
                array(
                    'id' => 'migrateusers',
                    'title' => __('Migrate Users', 'wpfront-user-role-editor'),
                    'content' => '<p>'
                    . __('To migrate users from one role to another role or to add secondary roles to users belonging to a particular primary role, use the migrate users functionality.', 'wpfront-user-role-editor')
                    . '</p>'
                    . '<p>'
                    . __('Select the users using the From Primary Role drop down, to primary role using the Primary Role drop down and secondary roles using the check boxes then click Migrate Users.', 'wpfront-user-role-editor')
                    . '</p>'
                )
            );

            $sidebar = array(
                array(
                    __('Documentation on Assign / Migrate Users', 'wpfront-user-role-editor'),
                    'assign-migrate-users/'
                )
            );

            Utils::set_help_tab($tabs, $sidebar);
        }

        public function assign_roles_user_autocomplete_callback() {
            $search_string = $_REQUEST['term'];

            $args = array(
                'search' => '*' . $search_string . '*',
                'search_columns' => array(
                    'user_login',
                    'user_nicename',
                    'user_email',
                    'display_name',
                ),
                'orderby' => 'display_name',
                'number' => 10,
                'fields' => array('ID', 'display_name', 'user_email'),
                'exclude' => array(wp_get_current_user()->ID)
            );
            $users_found = get_users($args);

            $user_details = array();
            foreach ($users_found as $user) {
                $user_details[] = array(
                    "label" => $user->display_name . '<' . $user->user_email . '>',
                    "value" => $user->ID
                );
            }

            echo json_encode($user_details);
            exit;
        }

    }

    add_action('wpfront_ure_init', '\WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_Migrate::init');
}