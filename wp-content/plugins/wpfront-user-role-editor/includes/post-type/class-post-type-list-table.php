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
 * WPFront User Role Editor Post Type List Table
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Post_Type;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('\WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

if (!class_exists('\WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type_List_Table')) {

    /**
     * Post Type List Table
     *
     * @author Vaisagh D <vaisaghd@wpfront.com>
     * @copyright 2015 WPFront.com
     */
    class WPFront_User_Role_Editor_Post_Type_List_Table extends \WP_List_Table {

        /**
         *
         * @var WPFront_User_Role_Editor_Post_Type 
         */
        private $controller;

        /**
         * 
         * @param WPFront_User_Role_Editor_Post_Type $controller
         */
        public function __construct($controller) {
            $this->controller = $controller;
            parent::__construct(array('screen' => 'post-type'));
        }

        function prepare_items() {
            $search = '';
            if (!empty($_GET['s'])) {
                $search = $_GET['s'];
            }

            $this->items = $this->controller->search($search);
            $this->items = $this->controller->apply_active_list_filter($this->items);

            $this->set_pagination_args(array(
                'total_items' => count($this->items),
                'per_page' => PHP_INT_MAX,
            ));
        }

        function get_bulk_actions() {
            $actions = array();

            if (current_user_can('delete_posttypes')) {
                $actions['delete'] = __('Delete', 'wpfront-user-role-editor');
            }

            if (current_user_can('edit_posttypes')) {
                $actions['restore'] = __('Restore', 'wpfront-user-role-editor');
                $actions['activate'] = __('Activate', 'wpfront-user-role-editor');
                $actions['deactivate'] = __('Deactivate', 'wpfront-user-role-editor');
            }

            return $actions;
        }

        function get_columns() {
            $columns = array(
                'cb' => '<input type="checkbox" />',
                'name' => __('Name', 'wpfront-user-role-editor'),
                'label' => __('Label', 'wpfront-user-role-editor'),
                'source' => __('Source', 'wpfront-user-role-editor'),
                'taxonomies' => __('Taxonomies', 'wpfront-user-role-editor'),
                'status' => __('Active', 'wpfront-user-role-editor'),
                'edited' => __('Edited', 'wpfront-user-role-editor')
            );

            return $columns;
        }

        function display_rows() {
            foreach ($this->items as $item) {
                $alt = empty($alt) ? 'alternate' : '';
                ?>
                <tr class="<?php echo $alt; ?>">
                    <?php
                    list( $columns, $hidden ) = $this->get_column_info();

                    foreach ($columns as $column_name => $column_display_name) {
                        $class = "class='$column_name column-$column_name'";

                        $style = '';
                        if (in_array($column_name, $hidden)) {
                            $style = ' style="display:none;"';
                        }

                        $attributes = "$class$style";

                        switch ($column_name) {
                            case 'cb':
                                $this->cb_cell($item);
                                break;

                            case 'name':
                                $this->name_cell($item, $attributes);
                                break;

                            case 'label':
                                $this->label_cell($item);
                                break;

                            case 'source':
                                $this->source_cell($item);
                                break;

                            case 'taxonomies':
                                $this->taxonomies_cell($item);
                                break;

                            case 'status':
                                $this->status_cell($item);
                                break;

                            case 'edited':
                                $this->edited_cell($item);
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
                <?php if ($item->can_edit) { ?>
                    <label class="screen-reader-text" for="post_type_select" ?></label>
                    <input type="checkbox" id="post_type_<?php echo esc_attr($item->name); ?>" name="post_types[]" value="<?php echo esc_attr($item->name); ?>" />
                <?php } ?>
            </th>
            <?php
        }

        protected function name_cell($item, $attributes) {
            ?>

            <td <?php echo $attributes; ?>>
                <?php
                if ($item->can_edit) {
                    $edit_link = esc_url_raw($this->controller->get_edit_url($item->name));
                    ?>
                    <strong>
                        <a href="<?php echo esc_attr($edit_link); ?>" class="edit">
                            <?php echo esc_html($item->name); ?>
                        </a>
                    </strong>
                    <?php
                } else {
                    ?> <?php echo esc_html($item->name); ?> <?php
                }
                $actions = array();
                if ($item->can_edit) {
                    $edit_link = $this->controller->get_edit_url($item->name);
                    $display = __('Edit', 'wpfront-user-role-editor');
                    $actions['edit'] = "<a href='".esc_attr($edit_link)."'>".esc_html($display)."</a>";
                }
                if ($item->can_delete) {
                    $delete_link = $this->controller->get_delete_url($item->name);
                    $display = __('Delete', 'wpfront-user-role-editor');
                    $actions['delete'] = "<a href='".esc_attr($delete_link)."'>".esc_html($display)."</a>";
                }
                if ($item->can_activate) {
                    $activate_link = $this->controller->get_activate_url($item->name);
                    $display = __('Activate', 'wpfront-user-role-editor');
                    $actions['activate'] = "<a href='".esc_attr($activate_link)."'>".esc_html($display)."</a>";
                }
                if ($item->can_deactivate) {
                    $deactivate_link = $this->controller->get_deactivate_url($item->name);
                    $display = __('Deactivate', 'wpfront-user-role-editor');
                    $actions['deactivate'] = "<a href='".esc_attr($deactivate_link)."'>".esc_html($display)."</a>";
                }
                if ($item->can_clone) {
                    $clone_link = $this->controller->get_clone_url($item->name);
                    $display = __('Clone', 'wpfront-user-role-editor');
                    $actions['clone'] = "<a href='".esc_attr($clone_link)."'>".esc_html($display)."</a>";
                }
                if ($item->can_restore) {
                    $restore_link = $this->controller->get_restore_url($item->name);
                    $display = __('Restore', 'wpfront-user-role-editor');
                    $actions['restore'] = "<a href='".esc_attr($restore_link)."'>".esc_html($display)."</a>";
                }
                echo $this->row_actions($actions);
                ?> 
            </td>
            <?php
        }

        protected function label_cell($item) {
            ?>
            <td class="label column-label">
                <?php echo esc_html($item->label); ?>
            </td> 
            <?php
        }

        protected function source_cell($item) {
            ?>
            <td class="source column-source">
                <?php
                switch ($item->source_type) {
                    case 0:
                        echo __('Built-In', 'wpfront-user-role-editor');
                        break;

                    case 1:
                        echo __('Other', 'wpfront-user-role-editor');
                        break;

                    default:
                        echo __('User Defined', 'wpfront-user-role-editor');
                        break;
                }
                ?>
            </td>
            <?php
        }

        protected function status_cell($item) {
            $i = '';
            ?>
            <td class="status column-status">
                <?php
                switch ($item->status) {
                    case 0:
                        $i = '<i class ="fa fa-times"></i>';
                        break;

                    case 1:
                        $i = '<i class ="fa fa-check"></i>';
                        break;
                }
                echo $i;
                ?>
            </td>               
            <?php
        }

        protected function taxonomies_cell($item) {
            ?>
            <td class="taxonomies column-taxonomies">
                <?php
                $taxonomies = $item->taxonomies;
                foreach ($taxonomies as $taxonomy) {
                    if (!empty($taxonomy)) {
                        $taxonomy_type = get_taxonomy($taxonomy);
                        if (!empty($taxonomy_type)) {
                            $taxonomy_types[] = $taxonomy_type->label;
                        }
                    }
                }
                if (!empty($taxonomy_types)) {
                    $taxonomy_types = implode(', ', $taxonomy_types);
                    echo $taxonomy_types;
                }
                ?>
            </td>
            <?php
        }

        protected function edited_cell($item) {
            $i = '';
            if (!empty($item->entity)) {
                $i = '<i class ="fa fa-check-circle"></i>';
            }
            ?>
            <td class ="edited column-edited">
                <?php echo $i; ?>          
            </td>
            <?php
        }

    }

}
    
