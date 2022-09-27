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
 * Template for WPFront User Role Editor List Roles
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Roles;

if (!defined('ABSPATH')) {
    exit();
}

if(!class_exists('WPFront\URE\Roles\WPFront_User_Role_Editor_Roles_List_View')) {
    
    class WPFront_User_Role_Editor_Roles_List_View extends \WPFront\URE\WPFront_User_Role_Editor_View {
        
        protected $RolesList;

        protected $custom_columns = null;
        protected $role_data = null;

        public function __construct() {
            parent::__construct();
            
            $this->RolesList = WPFront_User_Role_Editor_Roles_List::instance();
        }
        
        public function view() {
            ?>
            <div class="wrap list-roles">
                <?php
                    $add_new = array();
                    if (current_user_can('create_roles')) {
                        $add_new[0] = __('Add New', 'wpfront-user-role-editor');
                        $add_new[1] = $this->RolesList->get_add_new_role_url();
                    }
                ?>
                <?php $this->title(__('Roles', 'wpfront-user-role-editor'), $add_new, $this->get_search_term()); ?>
                <?php $this->display_notices(); ?>
                <?php $this->filter_links(); ?>
                <form method = "post">
                    <?php wp_nonce_field('roles-list'); ?>
                    <?php $this->search_box(); ?>
                    <?php $this->bulk_actions('top'); ?>
                    <table class="wp-list-table widefat fixed users">
                        <thead>
                            <?php $this->table_header(); ?>
                        </thead>
                        <tfoot>
                            <?php $this->table_header(); ?>
                        </tfoot>
                        <tbody id="the-list">
                            <?php $this->create_rows(); ?>
                        </tbody>
                    </table>
                    <?php $this->bulk_actions('bottom'); ?>
                </form>
            </div>
            <?php
            $this->scripts();
        }
        
        protected function display_notices() {
            if ((isset($_GET['default-role-updated']) && $_GET['default-role-updated'] == 'true')) {
                $this->UtilsClass::notice_updated(__('New users\'s default roles has been updated.', 'wpfront-user-role-editor'));
            }
            
            if ((isset($_GET['roles-deleted']) && $_GET['roles-deleted'] == 'true')) {
                $this->UtilsClass::notice_updated(__('Selected roles have been deleted.', 'wpfront-user-role-editor'));
            }
        }
        
        protected function filter_links() {
            ?>
            <ul class="subsubsub">
                <li>
                    <?php
                    $link_data = array();
                    $active_filter = $this->get_active_list_filter();
                    $filter_data = $this->RolesList->get_list_filter_data();
                    foreach ($filter_data as $key => $value) {
                        $link_data[] = sprintf('<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_attr($value['url']), ($active_filter == $key ? 'current' : ''), esc_html($value['display']), esc_html($value['count']));
                    }
                    echo implode('&#160;|&#160;</li><li> ', $link_data);
                    ?>
                </li>
            </ul>
            <?php
        }
        
        protected function search_box() {
            ?>
            <p class = "search-box">
                <label class = "screen-reader-text" for = "role-search-input"><?php echo __('Search Roles', 'wpfront-user-role-editor') . ':'; ?></label>
                <input type="search" id="role-search-input" name="s" value="<?php echo esc_attr($this->get_search_term()); ?>">
                <input type="submit" name="search-submit" id="search-submit" class="button" value="<?php echo __('Search Roles', 'wpfront-user-role-editor'); ?>">
            </p>
            <?php
        }
        
        protected function bulk_actions($position) {
            ?>
            <div class="tablenav <?php echo $position; ?>">
                <div class="alignleft actions bulkactions">
                    <select name="action_<?php echo $position; ?>">
                        <option value="" selected="selected"><?php echo __('Bulk Actions', 'wpfront-user-role-editor'); ?></option>
                        <?php if (current_user_can('delete_roles')) { ?>
                            <option value="delete"><?php echo __('Delete', 'wpfront-user-role-editor'); ?></option>
                        <?php } ?>
                    </select>
                    <input type="submit" name="doaction_<?php echo $position; ?>" class="button bulk action" value="<?php echo __('Apply', 'wpfront-user-role-editor'); ?>">
                </div>
                <div class="tablenav-pages one-page"><span class="displaying-num"><?php echo sprintf(__('%s item(s)', 'wpfront-user-role-editor'), count($this->get_roles())); ?></span>
                    <br class="clear">
                </div>
            </div>
            <?php
        }
        
        protected function table_header() {
            $custom_columns = $this->get_custom_columns();
            ?>
            <tr>
                <td scope="col" id="cb" class="manage-column column-cb check-column">
                    <label class="screen-reader-text" for="cb-select-all-1"><?php echo __('Select All', 'wpfront-user-role-editor'); ?></label>
                    <input id="cb-select-all-1" type="checkbox" />
                </td>
                <th scope="col" id="displayname" class="manage-column column-displayname">
                    <a><span><?php echo __('Display Name', 'wpfront-user-role-editor'); ?></span></a>
                </th>
                <th scope="col" id="rolename" class="manage-column column-rolename">
                    <a><span><?php echo __('Role Name', 'wpfront-user-role-editor'); ?></span></a>
                </th>
                <th scope="col" id="roletype" class="manage-column column-roletype">
                    <a><span><?php echo __('Type', 'wpfront-user-role-editor'); ?></span></a>
                </th>
                <th scope="col" id="userdefault" class="manage-column column-userdefault num">
                    <a><span><?php echo __('New User Default', 'wpfront-user-role-editor'); ?></span></a>
                </th>
                <th scope="col" id="usercount" class="manage-column column-usercount num">
                    <a><span><?php echo __('Users', 'wpfront-user-role-editor'); ?></span></a>
                </th>
                <th scope="col" id="capscount" class="manage-column column-capscount num">
                    <a><span><?php echo __('Capabilities', 'wpfront-user-role-editor'); ?></span></a>
                </th>
                <?php
                foreach ($custom_columns as $key => $value) {
                    printf('
                        <th scope="col" id="%1$s" class="manage-column column-%1$s num">
                            <a><span>%2$s</span></a>
                        </th>
                        ', esc_attr($key), esc_html($value));
                }
                ?>
            </tr>
            <?php
        }
        
        protected function create_rows() {
            $roles = $this->get_roles();
            
            $index = 0;
            foreach ($roles as $key => $value) {
                $this->create_row($key, $value, $index);
                $index++;
            }
        }
        
        protected function create_row($role_name, $value, $index) {
            ?>
            <tr id="<?php echo $role_name; ?>" class="<?php echo $index % 2 == 0 ? 'alternate' : ''; ?>">
                <?php $this->cell_select_checkbox($role_name, $value['display_name'], $value['is_editable']); ?>
                <?php $this->cell_display_name($role_name, $value); ?>
                <?php $this->cell_role_name($role_name, $value); ?>
                <?php $this->cell_role_type($value['is_default']); ?>
                <?php $this->cell_user_default($value['user_default']); ?>
                <?php $this->cell_user_count($value['user_count']); ?>
                <?php $this->cell_caps_count($value['caps_count']); ?>
                <?php $this->cell_custom_columns($role_name); ?>
            </tr>
            <?php
        }
        
        protected function cell_select_checkbox($role_name, $display_name, $is_editable) {
            ?>
            <th scope="row" class="check-column">
                <?php if($is_editable) { ?>
                    <label class="screen-reader-text" for="cb-select-<?php echo esc_attr($role_name); ?>"><?php echo sprintf(__('Select %s', 'wpfront-user-role-editor'), esc_html($display_name)) ?></label>
                    <input type="checkbox" name="selected-roles[<?php echo esc_attr($role_name); ?>]" id="cb-select-<?php echo esc_html($role_name); ?>" />
                <?php } ?>
            </th>
            <?php
        }
        
        protected function cell_display_name($role_name, $value) {
            $display_name = $value['display_name'];
            $edit_url = esc_url_raw(($value['edit_url']));
            $is_editable = $value['is_editable'];
            $delete_url = esc_url_raw($value['delete_url']);
            $set_default_url = esc_url_raw($value['set_default_url']);
            ?>
            <td class="displayname column-displayname">
                <strong>
                    <?php
                    if (empty($edit_url))
                        echo $display_name;
                    else
                        printf('<a href="%s">%s</a>', esc_attr($edit_url), esc_html($display_name));
                    ?>
                </strong>
                <br />
                <?php $this->row_actions($role_name, $value, $edit_url, $is_editable, $delete_url, $set_default_url); ?>
            </td>
            <?php
        }
        
        protected function row_actions($role_name, $role_data, $edit_url, $is_editable, $delete_url, $set_default_url) {
            ?>
            <div class="row-actions">
                <?php
                $links = array();
                if (!empty($edit_url)) {
                    $links[] = sprintf('<span class="edit"><a href="%s">%s</a></span>', esc_attr($edit_url), ($is_editable ? __('Edit', 'wpfront-user-role-editor') : __('View', 'wpfront-user-role-editor')));
                }
                if (!empty($delete_url)) {
                    $links[] = sprintf('<span class="delete"><a href="%s">%s</a></span>', esc_attr($delete_url), __('Delete', 'wpfront-user-role-editor'));
                }
                if (!empty($set_default_url)) {
                    $text = __('Default', 'wpfront-user-role-editor');
                    if($role_data['user_default'] > 0) {
                        $text = '-' . $text;
                    } else {
                        $text = '+' . $text;
                    }
                    $links[] = sprintf('<span class="set-default"><a href="%s">%s</a></span>', esc_attr($set_default_url), esc_html($text));
                }
                $custom_links = apply_filters('role_row_actions', array(), get_role($role_name));
                foreach ($custom_links as $link_key => $link_value) {
                    $links[] = sprintf('<span class="%s">%s</span>', $link_key, $link_value);
                }
                echo implode(' | ', $links);
                ?>
            </div>
            <?php
        }
        
        protected function cell_role_name($role_name, $value) {
            ?>
            <td class="rolename column-rolename">
                <?php echo esc_html($role_name); ?>
            </td>
            <?php
        }
        
        protected function cell_role_type($is_default) {
            ?>
            <td class="roletype column-roletype">
                <?php echo $is_default ? __('Built-In', 'wpfront-user-role-editor') : __('Custom', 'wpfront-user-role-editor'); ?>
            </td>
            <?php
        }
        
        protected function cell_user_default($user_default) {
            ?>
            <td class="userdefault column-userdefault num">
                <?php
                if ($user_default == 1) {
                    ?>
                    <i class="fa fa-check-circle"></i>
                    <?php
                } elseif ($user_default == 2) {
                    ?>
                    <i class="fa fa-check-circle-o"></i>
                    <?php
                }
                ?>
            </td>
            <?php
        }
        
        protected function cell_user_count($user_count) {
            ?>
            <td class="usercount column-usercount num">
                <?php echo esc_html($user_count); ?>
            </td>
            <?php
        }
        
        protected function cell_caps_count($caps_count) {
            ?>
            <td class="capscount column-capscount num">
                <?php echo esc_html($caps_count); ?>
            </td>
            <?php
        }
        
        protected function cell_custom_columns($role_name) {
            $custom_columns = $this->get_custom_columns();
            
            foreach ($custom_columns as $column_key => $column_value) {
                echo "<td class='$column_key column-$column_key num'>"
                . apply_filters('manage_roles_custom_column', $column_value, $column_key, $role_name)
                . "</td>";
            }
        }

        protected function get_active_list_filter() {
            if (empty($_GET['list']))
                return 'all';

            $list = $_GET['list'];

            switch ($list) {
                case 'all':
                case 'haveusers':
                case 'nousers':
                case 'builtin':
                case 'custom':
                    break;
                default:
                    $list = 'all';
                    break;
            }

            return $list;
        }

        protected function get_search_term() {
            if (empty($_POST['s']))
                return '';

            return esc_html($_POST['s']);
        }
        
        protected function get_roles() {
            if($this->role_data != null) {
                return $this->role_data;
            }
            
            $role_data = $this->RolesList->get_role_data();
            
            $role_data = $this->apply_active_list_filter($role_data);
            $role_data = $this->apply_search_term($role_data);
            
            $this->role_data = $role_data;
            
            return $role_data;
        }
        
        protected function apply_active_list_filter($role_data) {
            switch ($this->get_active_list_filter()) {
                case 'all':
                    break;
                case 'haveusers':
                    foreach ($role_data as $key => $value) {
                        if ($value['user_count'] == 0)
                            unset($role_data[$key]);
                    }
                    break;
                case 'nousers':
                    foreach ($role_data as $key => $value) {
                        if ($value['user_count'] !== 0)
                            unset($role_data[$key]);
                    }
                    break;
                case 'builtin':
                    foreach ($role_data as $key => $value) {
                        if (!$value['is_default'])
                            unset($role_data[$key]);
                    }
                    break;
                case 'custom':
                    foreach ($role_data as $key => $value) {
                        if ($value['is_default'])
                            unset($role_data[$key]);
                    }
                    break;
            }
            
            return $role_data;
        }
        
        protected function apply_search_term($role_data) {
            $search = $this->get_search_term();
            $search = strtolower(trim($search));
            if ($search !== '') {
                foreach ($role_data as $key => $value) {
                    if (strpos(strtolower($value['display_name']), $search) === false) {
                        unset($role_data[$key]);
                    }
                }
            }
            
            return $role_data;
        }
        
        protected function get_custom_columns() {
            if($this->custom_columns === null) {
                $this->custom_columns = apply_filters('manage_roles_columns', array());
            }
            
            return $this->custom_columns;
        }
        
        protected function scripts() {
        }
    
    }
    
}

