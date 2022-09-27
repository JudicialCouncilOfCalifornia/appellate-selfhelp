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
 * WPFront User Role Editor Login Redirect List Table
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Login_Redirect;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect as LoginRedirect;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;

if (!class_exists('\WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('\WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect_List_Table')) {

    /**
     * Login Redirect List Table
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Login_Redirect_List_Table extends \WP_List_Table {

        /**
         *
         * @var string[] Associative (name=>display) 
         */
        protected $allowed_roles;

        public function __construct() {
            parent::__construct(array('screen' => 'login-redirect'));
        }

        function ajax_user_can() {
            return current_user_can('edit_login_redirects');
        }

        function prepare_items() {
            $search = '';
            if(!empty($_GET['s'])) {
                $search = $_GET['s'];
            }
            
            $this->items = LoginRedirect::instance()->get_login_redirects($search);
            
            $this->set_pagination_args(array(
                'total_items' => count($this->items),
                'per_page' => PHP_INT_MAX,
            ));
        }

        function get_bulk_actions() {
            $actions = array();
            
            if (current_user_can('delete_login_redirects')) {
                $actions['delete'] = __('Delete', 'wpfront-user-role-editor');
            }

            return $actions;
        }

        function no_items() {
            echo __('No login redirects found.', 'wpfront-user-role-editor');
        }

        function get_views() {
            $role_links = array();

            return $role_links;
        }

        function pagination($which) {
            parent::pagination($which);
        }

        function get_columns() {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'role' => __('Role', 'wpfront-user-role-editor'),
                'priority' => __('Priority', 'wpfront-user-role-editor'),
                'url' => __('Login Redirect URL', 'wpfront-user-role-editor'),
                'logout_url' => __('Logout Redirect URL', 'wpfront-user-role-editor'),
                'deny_wpadmin' => __('WP-ADMIN', 'wpfront-user-role-editor'),
                'disable_toolbar' => __('Toolbar', 'wpfront-user-role-editor')
            );

            return $columns;
        }

        function get_sortable_columns() {
            return array();
        }

        function display_rows() {
            foreach ($this->items as $item) {
                $alt = empty($alt) ? 'alternate' : '';
                $item->role_display = $this->get_role_display($item->role);
                ?>
                <tr class="<?php echo $alt; ?>">
                    <?php
                    list( $columns, $hidden ) = $this->get_column_info();
                    
                    foreach ($columns as $column_name => $column_display_name) {
                        $class = "class='$column_name column-$column_name'";

                        $style = '';
                        if (in_array($column_name, $hidden))
                            $style = ' style="display:none;"';

                        $attributes = "$class$style";
                        
                        switch ($column_name) {
                            case 'cb':
                                $this->cb_cell($item);
                                break;
                            
                            case 'role':
                                $this->role_cell($item, $attributes);
                                break;
                            
                            case 'priority':
                                echo "<td $attributes>" . esc_html($item->priority) . "</td>";
                                break;

                            case 'url':
                                echo "<td $attributes>" . esc_html($this->format_url($item->url)) . "</td>";
                                break;

                            case 'logout_url':
                                echo "<td $attributes>" . esc_html($this->format_url($item->logout_url)) . "</td>";
                                break;

                            case 'deny_wpadmin':
                                echo "<td $attributes>" . ($item->deny_wpadmin ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>') . "</td>";
                                break;

                            case 'disable_toolbar':
                                echo "<td $attributes>" . ($item->disable_toolbar ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>') . "</td>";
                                break;
                        }
                    }
                    ?>
                </tr>
                <?php
            }
        }
        
        protected function cb_cell($item) {
            ?>
            <th scope="row" class="check-column">
                <label class="screen-reader-text" for="role_<?php echo esc_attr($item->role); ?>"><?php echo sprintf(__('Select %s', 'wpfront-user-role-editor'), esc_html($item->role_display)); ?></label>
                <input type="checkbox" id="role_<?php echo esc_attr($item->role); ?>" name="roles[]" value="<?php echo esc_attr($item->role); ?>" />
            </th>
            <?php
        }
        
        protected function role_cell($item, $attributes) {
            $can_edit = current_user_can('edit_login_redirects');
            if($can_edit) {
                $allowed_roles = $this->get_allowed_roles();
                $can_edit = isset($allowed_roles[$item->role]);
            }
            ?>
            <td <?php echo $attributes; ?>>
                <?php
                if($can_edit) {
                    $edit_link = esc_url_raw(LoginRedirect::instance()->get_edit_url($item->role));
                    ?>
                    <strong>
                        <a href="<?php echo esc_attr($edit_link); ?>" class="edit">
                            <?php echo esc_html($item->role_display); ?>
                        </a>
                    </strong>
                    <?php
                } else {
                    ?>
                    <strong>
                        <?php echo esc_html($item->role_display); ?>
                    </strong>
                    <?php
                }
                $actions = array();
                if ($can_edit) {
                    $edit_link = esc_url_raw(LoginRedirect::instance()->get_edit_url($item->role));
                    $display = __('Edit', 'wpfront-user-role-editor');
                    $actions['edit'] = "<a href='".esc_attr($edit_link)."'>$display</a>";
                }
                if (current_user_can('delete_login_redirects')) {
                    $delete_link = esc_url_raw(LoginRedirect::instance()->get_delete_url($item->role));
                    $display = __('Delete', 'wpfront-user-role-editor');
                    $actions['delete'] = "<a href='".esc_attr($delete_link)."'>$display</a>";
                }
                echo $this->row_actions($actions);
                ?>
            </td>
            <?php
        }
        
        protected function get_role_display($role_name) {
            return LoginRedirect::instance()->get_role_display($role_name);
        }
        
        protected function format_url($url) {
            return LoginRedirect::instance()->format_url($url);
        }
        
        protected function get_allowed_roles() {
            if(!empty($this->allowed_roles)) {
                return $this->allowed_roles;
            }
            $this->allowed_roles = LoginRedirect::instance()->get_allowed_roles();
            return $this->allowed_roles;
        }

    }

}
