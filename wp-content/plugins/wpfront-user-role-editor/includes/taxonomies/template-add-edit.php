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
 * Template for WPFront User Role Editor Taxonomies Add Edit
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Taxonomies;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\Taxonomies\WPFront_User_Role_Editor_Taxonomies as Taxonomies;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;

if (!class_exists('WPFront\URE\Taxonomies\WPFront_User_Role_Editor_Taxonomies_Add_Edit_View')) {

    class WPFront_User_Role_Editor_Taxonomies_Add_Edit_View {

        /**
         *
         * @var WPFront_User_Role_Editor_Taxonomies
         */
        private $controller;
        private $taxonomy_data;
        private $taxonomy_obj;

        public function __construct($controller, $data = null, $taxonomy_obj = null, $clone = null) {
            $this->controller = $controller;
            $this->taxonomy_data = $data;
            $this->taxonomy_obj = $taxonomy_obj;
            $this->clone_from = $clone;
        }

        public function view() {
            ?>
            <div class="wrap taxonomy-add-edit">
                <?php $this->title(); ?>
                <?php $this->display_notices(); ?>
                <?php
                if (empty($this->taxonomy_data)) {
                    $action = $this->controller->get_add_new_url();
                } else {
                    $action = $this->controller->get_edit_url($this->taxonomy_data->name);
                }
                ?>
                <form method="post" class="validate" action="<?php echo esc_attr($action); ?>">
                    <?php $this->create_meta_boxes(); ?>
                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2" style="display:flow-root">
                            <div id="post-body-content" style="position:relative">
                                <?php do_meta_boxes($this->controller->get_menu_slug(), 'normal', null); ?>
                            </div>
                            <div id="postbox-container-1" class="postbox-container" style="position: sticky; top: 40px;">
                                <?php do_meta_boxes($this->controller->get_menu_slug(), 'side', null); ?>
                            </div>
                        </div>
                    </div>
                    <?php wp_nonce_field('add-edit-taxonomies'); ?>
                    <?php submit_button(null, 'primary', 'submit2', false); ?>
                </form>
            </div>
            <?php $this->scripts(); ?>
            <?php
        }

        public function title() {
            if (empty($this->taxonomy_data)) {
                ?>
                <h2>
                    <?php echo __('Add New Taxonomy', 'wpfront-user-role-editor'); ?>
                </h2>
                <?php
            } else {
                ?>
                <h2>
                    <?php echo __('Edit Taxonomy', 'wpfront-user-role-editor'); ?>
                </h2>
                <?php
            }
        }

        protected function display_notices() {
            if (!empty($this->controller->errorMsg)) {
                Utils::notice_error($this->controller->errorMsg);
            }

            if (!empty($_GET['taxonomy-added'])) {
                Utils::notice_updated(__('Taxonomy added successfully.', 'wpfront-user-role-editor'));
            } elseif (!empty($_GET['taxonomy-updated'])) {
                Utils::notice_updated(__('Taxonomy updated successfully.', 'wpfront-user-role-editor'));
            }
        }

        protected function get_meta_box_groups() {
            return [
                (object) [
                    'group_name' => 'basic_settings',
                    'title' => __('Basic Settings', 'wpfront-user-role-editor'),
                    'render' => 'postbox_render_basic_settings'
                ],
                (object) [
                    'group_name' => 'labels',
                    'title' => __('Additional Labels', 'wpfront-user-role-editor'),
                    'render' => 'postbox_render_labels'
                ],
                (object) [
                    'group_name' => 'advanced_settings',
                    'title' => __('Advanced Settings', 'wpfront-user-role-editor'),
                    'render' => 'postbox_render_advanced_settings'
                ]
            ];
        }

        protected function create_meta_boxes() {
            $groups = $this->get_meta_box_groups();

            foreach ($groups as $group) {
                add_meta_box("postbox-{$group->group_name}", $group->title, array($this, $group->render), $this->controller->get_menu_slug(), 'normal', 'default', $group);
            }

            add_meta_box("postbox-side", __('Actions', 'wpfront-user-role-editor'), array($this, 'action_buttons'), $this->controller->get_menu_slug(), 'side', 'default', $group);

            wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
            wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        }

        public function action_buttons() {
            submit_button();
            ?>
            <p>
                <a class="auto-populate-labels button button-secondary"><?php echo __('Auto Populate Labels', 'wpfront-user-role-editor'); ?></a>
            </p>
            <p>
                <a class="clear-labels button button-secondary"><?php echo __('Clear Labels', 'wpfront-user-role-editor'); ?></a>
            </p>
            <?php
        }

        public function postbox_render_basic_settings() {
            ?>
            <table class="form-table">
                <tbody>
                    <?php
                    $this->textbox_basic_settings(__('Name', 'wpfront-user-role-editor'), 'name');
                    $this->textbox_basic_settings(__('Plural Label', 'wpfront-user-role-editor'), 'label');
                    $this->textbox_basic_settings(__('Singular Label', 'wpfront-user-role-editor'), 'singular_name');
                    $this->multilist_basic_settings(
                            __('Post Types', 'wpfront-user-role-editor'),
                            'post_types',
                            __('Post types for the taxonomy.', 'wpfront-user-role-editor')
                    );
                    ?>
                </tbody>
            </table>
            <?php
        }

        public function postbox_render_labels() {
            ?>
            <table class="form-table">
                <tbody>
                    <?php
                    $this->textbox_additional_labels(
                            __('Search Items Label', 'wpfront-user-role-editor'),
                            'search_items',
                            __(' Default Search Tags/Search Categories. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Search %S0', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Popular Items Label', 'wpfront-user-role-editor'),
                            'popular_items',
                            __('This label is only used for non-hierarchical taxonomies. Default Popular Tags. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Popular %S0', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('All Items Label', 'wpfront-user-role-editor'),
                            'all_items',
                            __('Default is All Tags/All Categories. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('%S0', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Edit Item Label', 'wpfront-user-role-editor'),
                            'edit_item',
                            __('Default Edit Tag/Edit Category. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Edit %S1', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Parent Item Label', 'wpfront-user-role-editor'),
                            'parent_item',
                            __('This label is only used for hierarchical taxonomies. Default Parent Category. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Parent %S1', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Parent Item Colon', 'wpfront-user-role-editor'),
                            'parent_item_colon',
                            __('The same as parent_item, but with colon : in the end.', 'wpfront-user-role-editor'),
                            __('Parent %S1:', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('View Items Label', 'wpfront-user-role-editor'),
                            'view_item',
                            __('Default is View Tag/View Category.', 'wpfront-user-role-editor'),
                            __('View %S1', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Update Item Label', 'wpfront-user-role-editor'),
                            'update_item',
                            __('Default is Update Tag/Update Category.', 'wpfront-user-role-editor'),
                            __('Update %S1', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Add New Item Label', 'wpfront-user-role-editor'),
                            'add_new_item',
                            __(' Default is Add New Tag/Add New Category.', 'wpfront-user-role-editor'),
                            __('Add New %S1', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('New Item Name Label', 'wpfront-user-role-editor'),
                            'new_item_name',
                            __('Default New Tag Name/New Category Name.', 'wpfront-user-role-editor'),
                            __('New %S1 Name', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Separate Items with Commas Label', 'wpfront-user-role-editor'),
                            'separate_items_with_commas',
                            __('This label is only used for non-hierarchical taxonomies. Default Separate tags with commas, used in the meta box.', 'wpfront-user-role-editor'),
                            __('Separate %s0 with commas', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Add or Remove Items Label', 'wpfront-user-role-editor'),
                            'add_or_remove_items',
                            __('This label is only used for non-hierarchical taxonomies. Default Add or remove tags, used in the meta box when JavaScript is disabled.', 'wpfront-user-role-editor'),
                            __('Add or remove %s0', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Choose from Most Used Label', 'wpfront-user-role-editor'),
                            'choose_from_most_used',
                            __('This label is only used on non-hierarchical taxonomies. Default Choose from the most used tags, used in the meta box.', 'wpfront-user-role-editor'),
                            __('Choose from the most used %s0', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Not Found Label', 'wpfront-user-role-editor'),
                            'not_found',
                            __('Default is No tags found/No categories found, used in the meta box and taxonomy list table.', 'wpfront-user-role-editor'),
                            __('No %s0 found.', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('No Terms Label', 'wpfront-user-role-editor'),
                            'no_terms',
                            __('Default is No tags/No categories, used in the posts and media list tables.', 'wpfront-user-role-editor'),
                            __('No %s0', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Filter by Item Label', 'wpfront-user-role-editor'),
                            'filter_by_item',
                            __('This label is only used for hierarchical taxonomies. Default Filter by category, used in the posts list table.', 'wpfront-user-role-editor'),
                            __('Filter by %S1', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Item List Navigation Label', 'wpfront-user-role-editor'),
                            'items_list_navigation',
                            __('Label for the table pagination hidden heading.', 'wpfront-user-role-editor'),
                            __('%S0 list navigation', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Item List Label', 'wpfront-user-role-editor'),
                            'items_list',
                            __('Label for the table hidden heading.', 'wpfront-user-role-editor'),
                            __('%S0 list', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Most Used Label', 'wpfront-user-role-editor'),
                            'most_used',
                            __('Title for the Most Used tab. Default Most Used.', 'wpfront-user-role-editor'),
                            __('Most Used', 'wpfront-user-role-editor')
                    );
                    $this->textbox_additional_labels(
                            __('Back to Items Label', 'wpfront-user-role-editor'),
                            'back_to_items',
                            __('Label displayed after a term has been updated.', 'wpfront-user-role-editor'),
                            __('&larr; Go to %S0', 'wpfront-user-role-editor')
                    );
                    ?>
                </tbody>
            </table>
            <?php
        }

        public function postbox_render_advanced_settings() {
            ?>
            <table class="form-table">
                <tbody>
                    <?php
                    $this->dropdown_advanced_settings_boolean(
                            __('Public', 'wpfront-user-role-editor'),
                            'public',
                            __('Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users. The default settings of <b>"Publicly Queryable"</b>, <b>"Show UI"</b>, and <b>"Show in Nav Menus"</b> are inherited from <b>"Public"</b>.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Hierarchical', 'wpfront-user-role-editor'),
                            'hierarchical',
                            __('Whether the taxonomy is hierarchical. Default false.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Publicaly Queryable', 'wpfront-user-role-editor'),
                            'publicly_queryable',
                            __('Whether the taxonomy is publicly queryable. If not set, the default is inherited from <b>"Public"</b>.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in Menu', 'wpfront-user-role-editor'),
                            'show_in_menu',
                            __('Whether to show the taxonomy in the admin menu. If true, the taxonomy is shown as a submenu of the object type menu. If false, no menu is shown <b>"Show UI"</b> must be true. If not set, default is inherited from <b>"Show UI"</b> (Default is true).', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show UI', 'wpfront-user-role-editor'),
                            'show_ui',
                            __('Whether to generate and allow a UI for managing terms in this taxonomy in the admin. If not set, the default is inherited from <b>"Public"</b> (Default is true).', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in Nav Menus', 'wpfront-user-role-editor'),
                            'show_in_nav_menus',
                            __('Makes this taxonomy available for selection in navigation menus. If not set, the default is inherited from <b>"Public"</b>.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in REST', 'wpfront-user-role-editor'),
                            'show_in_rest',
                            __('Whether to include the taxonomy in the REST API. Set this to true for the taxonomy to be available in the block editor.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->textbox_advanced_settings(
                            __('REST Base', 'wpfront-user-role-editor'),
                            'rest_base',
                            __('To change the base url of REST API route. Default is <b>"Name"</b>.', 'wpfront-user-role-editor')
                    );
                    $this->textbox_advanced_settings(
                            __('REST Controller Class', 'wpfront-user-role-editor'),
                            'rest_controller_class',
                            __('REST API Controller class name. Default is WP_REST_Terms_Controller', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show Tagcloud', 'wpfront-user-role-editor'),
                            'show_tagcloud',
                            __('Whether to list the taxonomy in the Tag Cloud Widget controls. If not set, the default is inherited from <b>"Show UI"</b> (Default is true).', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in Quick Edit', 'wpfront-user-role-editor'),
                            'show_in_quick_edit',
                            __('Whether to show the taxonomy in the quick/bulk edit panel. If not set, the default is inherited from <b>"Show UI"</b> (Default is true).', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show Admin Column', 'wpfront-user-role-editor'),
                            'show_admin_column',
                            __('Whether to display a column for the taxonomy on its post type listing screens. Default is false.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Query Var', 'wpfront-user-role-editor'),
                            'query_var',
                            __('Sets the query var key for this taxonomy. Default is <b>"Name"</b> key. If false, a taxonomy cannot be loaded at ?{query_var}={term_slug}. If a string, the query ?{query_var}={term_slug} will be valid.', 'wpfront-user-role-editor')
                    );
                    $this->rewrite_settings();
                    ?>
                </tbody>
            </table>
            <?php
        }

        protected function rewrite_settings() {
            $arg_value = $this->get_property_value('rewrite', '');
            if ($arg_value === null) {
                $arg_value = '';
            }

            $current_property_value = $this->get_current_property_value('rewrite');

            $this->dropdown_advanced_settings_boolean(
                    __('Rewrite', 'wpfront-user-role-editor'),
                    'rewrite',
                    __('Triggers the handling of rewrites for this taxonomy. Default is true, using <b>"Name"</b> as slug. To prevent rewrite, set to false. To specify rewrite rules, an array can be passed with any of these keys:', 'wpfront-user-role-editor'),
                    '',
                    false,
                    is_array($arg_value) ? true : $arg_value,
                    !empty($current_property_value) ? 'True' : 'False'
            );

            $this->textbox_advanced_settings(
                    __('Rewrite Slug', 'wpfront-user-role-editor'),
                    'rewrite_slug',
                    __(' Customize the permastruct slug. Default is <b>"Name"</b> key.', 'wpfront-user-role-editor'),
                    isset($arg_value['slug']) ? $arg_value['slug'] : null,
                    isset($current_property_value['slug']) ? $current_property_value['slug'] : ''
            );


            $this->dropdown_advanced_settings_boolean(
                    __('Rewrite With Front', 'wpfront-user-role-editor'),
                    'rewrite_with_front',
                    __('Should the permastruct be prepended with WP_Rewrite::$front. Default is true. ', 'wpfront-user-role-editor'),
                    '',
                    false,
                    isset($arg_value['with_front']) ? $arg_value['with_front'] : null,
                    isset($current_property_value['with_front']) ? $current_property_value['with_front'] : ''
            );

            $this->dropdown_advanced_settings_boolean(
                    __('Rewrite Hierarchical', 'wpfront-user-role-editor'),
                    'rewrite_hierarchical',
                    __('Either hierarchical rewrite tag or not. Default is false.', 'wpfront-user-role-editor'),
                    '',
                    false,
                    isset($arg_value['hierarchical']) ? $arg_value['hierarchical'] : null,
                    isset($current_property_value['hierarchical']) ? $current_property_value['hierarchical'] : ''
            );

            $this->textbox_advanced_settings(
                    __('Rewrite EP Mask', 'wpfront-user-role-editor'),
                    'rewrite_ep_mask',
                    __('Assign an endpoint mask. Default is EP_NONE. ', 'wpfront-user-role-editor'),
                    isset($arg_value['ep_mask']) ? $arg_value['ep_mask'] : null,
                    isset($current_property_value['ep_mask']) ? $current_property_value['ep_mask'] : ''
            );
            return;
        }

        protected function textbox_basic_settings($label, $name) {
            $value = $this->get_property_value($name);
            $attr = '';
            if ($name === 'name') {
                if (!empty($this->taxonomy_data)) {
                    $value = $this->taxonomy_data->name;
                    $attr = 'disabled="true"';
                }
            } elseif ($name === 'singular_name') {
                $value = $this->get_labels_value('singular_name');
            }

            $this->textbox_row($label, $name, $value, (object) ['required' => true, 'attr' => $attr]);
        }

        protected function multilist_basic_settings($label, $name, $help) {
            $options = [];
            $values = $this->get_property_value('post_types');
            $current_property_value = '';

            if (isset($this->taxonomy_data->post_types)) {
                $post_types = $this->taxonomy_data->post_types;
                foreach ($post_types as $post_type) {
                    if ($post_type === 'link') {
                        $object_types[] = $post_type;
                    } elseif (!empty($post_type)) {
                        $object_type = get_post_type_object($post_type);
                        if (!empty($object_type)) {
                            $object_types[] = $object_type->label;
                        }
                    }
                }
                if (!empty($object_types)) {
                    $current_property_value = implode(', ', $object_types);
                }
            }

            $post_types = get_post_types();
            foreach ($post_types as $post_type) {
                $post_type_obj = get_post_type_object($post_type);
                if (is_post_type_viewable($post_type) || empty($post_type_obj->_builtin)) {

                    $options[$post_type_obj->name] = $post_type_obj->label;
                }
            }

            $obj = ['help' => $help, 'help_current_value' => $current_property_value];

            $this->multilist_row($label, $name, $values, $options, (object) $obj);
        }

        protected function textbox_additional_labels($label, $name, $help, $auto_format = '') {
            $this->textbox_row($label, $name, $this->get_labels_value($name), (object) ['help' => $help, 'help_current_value' => $this->get_current_labels_value($name), 'auto_format' => $auto_format]);
        }

        protected function dropdown_advanced_settings_boolean($label, $name, $help, $default_value = '', $exclude_default = false, $arg_value = null, $current_property_value = null) {
            $options = [(object) ['label' => __('True', 'wpfront-user-role-editor'), 'value' => true], (object) ['label' => __('False', 'wpfront-user-role-editor'), 'value' => false]];
            if (!$exclude_default) {
                array_unshift($options, (object) ['label' => __('Default', 'wpfront-user-role-editor'), 'value' => '']);
            }

            if ($arg_value === null) {
                $arg_value = $this->get_property_value($name, $default_value);
                if ($name === 'customize_capability') { //for customize_capability default = null, false = ''
                    if ($arg_value === null) {
                        $arg_value = '';
                    } elseif ($arg_value === '') {
                        $arg_value = false;
                    }
                }
            }

            if ($current_property_value === null) {
                if($name === 'customize_capability') {
                    if(empty($this->taxonomy_data->capability_type)) {
                        $current_property_value = 'manage_categories';
                    } else {
                        $current_property_value = $this->taxonomy_data->capability_type;
                    }
                } else {
                    $current_property_value = $this->get_current_property_value($name);
                }
            }

            if ($current_property_value === true) {
                $current_property_value = __('True', 'wpfront-user-role-editor');
            } elseif ($current_property_value === false) {
                $current_property_value = __('False', 'wpfront-user-role-editor');
            }

            $obj = ['help' => $help, 'help_current_value' => $current_property_value];

            //On POST with a validation error $arg_value will be one of empty string, 1 or 0
            if ($arg_value === '1') {
                $arg_value = true;
            } elseif ($arg_value === '0') {
                $arg_value = false;
            }

            switch ($name) {
                case 'query_var':
                case 'customize_capability':
                    $has_depends = $name . '_slug';
                    break;

                default:
                    break;
            }

            if (!empty($has_depends)) {
                if (empty($arg_value) || is_bool($arg_value)) { //selected default or true/false
                    if ($name === 'customize_capability') {
                        $current_property_value = $this->get_property_value('label', '');
                        $current_property_value = $this->controller->sanitize_capability_type($current_property_value);
                    } else {
                        $current_property_value = '';
                    }
                } else { //specified a value in textbox.
                    $current_property_value = $arg_value;
                    $arg_value = true;
                }

                $obj['txt'] = ['name' => $has_depends, 'value' => $current_property_value, 'depends_on' => 'true'];
            }

            // $arg_value will be null when Default is selected.
            if ($arg_value === null) {
                $arg_value = '';
            }

            $this->dropdown_row($label, $name, $options, $arg_value, (object) $obj);
        }

        protected function textbox_advanced_settings($label, $name, $help, $prop_value = null, $prop_current_value = null) {
            if ($prop_value === null) {
                $prop_value = $this->get_property_value($name);
            }

            if ($prop_current_value === null) {
                $prop_current_value = $this->get_current_property_value($name);
            }

            $this->textbox_row($label, $name, $prop_value, (object) ['help' => $help, 'help_current_value' => $prop_current_value]);
        }

        protected function textbox_row($label, $name, $value, $obj) {
            $attr = '';
            $class = '';
            if (!empty($obj->attr)) {
                $attr .= $obj->attr . ' ';
            }
            if (!empty($obj->required)) {
                $attr .= 'aria-required="true" ';
            }
            if (!empty($obj->help_current_value)) {
                $attr .= 'placeholder="' . esc_attr($obj->help_current_value) . '" ';
            }
            if (!empty($obj->auto_format)) {
                $attr .= 'data-auto-format="' . esc_attr($obj->auto_format) . '" ';
                $class .= 'auto-populate';
            }
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            ?>
            <tr class="<?php echo!empty($obj->required) ? 'form-required ' : ''; ?>">
                <th scope="row">
                    <?php echo esc_html($label); ?>
                    <?php if (!empty($obj->required)) { ?>
                        <span class="description"> (<?php echo __('required', 'wpfront-user-role-editor'); ?>)</span>
                    <?php } ?>
                </th>
                <td>
                    <input class="regular-text <?php echo $class; ?>" name="<?php echo esc_attr($name); ?>" type="text" value="<?php echo esc_attr($value); ?>" <?php echo $attr; ?> />
                    <?php
                    if (!empty($obj->help)) {
                        $this->echo_help_tooltip($obj->help, $obj->help_current_value);
                    }
                    ?>
                </td>
            </tr>
            <?php
        }

        protected function multilist_row($label, $name, $values, $options, $obj) {
            if (empty($values)) {
                $values = [];
            }
            ?>
            <tr>
                <th scope="row">
                    <?php
                    echo $label;
                    $placeholder = __('Choose From Options', 'wpfront-user-role-editor');
                    ?>
                </th>
                <td>
                    <select data-placeholder="<?php echo esc_attr($placeholder); ?>" name="<?php echo esc_attr($name); ?>[]" class="chosen-select" multiple>
                        <?php
                        foreach ($options as $value => $label) {
                            $selected = in_array($value, $values) ? 'selected' : '';
                            echo "<option value='".esc_attr($value)."' $selected>".esc_html($label)."</option>";
                        }
                        ?>
                    </select>
                    <?php
                    if (!empty($obj->help)) {
                        $this->echo_help_tooltip($obj->help, $obj->help_current_value);
                    }
                    ?>
            </tr>
            <?php
        }

        protected function dropdown_row($label, $name, $options, $value, $obj) {
            $attr = '';
            $placeholder = '';
            if (!empty($obj->attr)) {
                $attr .= $obj->attr . ' ';
            }
            ?>
            <tr>
                <th scope="row">
                    <?php echo esc_html($label); ?>
                </th>
                <td>
                    <select id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" class="<?php echo!empty($obj->txt) ? 'has-depends' : ''; ?>" <?php echo $attr; ?> >
                        <?php
                        foreach ($options as $option) {
                            $selected = $option->value === $value ? 'selected' : '';
                            if ($option->value === true) {
                                $option->value = '1';
                            } elseif ($option->value === false) {
                                $option->value = '0';
                            }
                            $option_value=esc_attr($option->value);
                            $option_label=esc_html($option->label);
                            echo "<option value='$option_value' $selected>$option_label</option>";
                                                    }
                        ?>
                    </select>
                    <?php
                    if (!empty($obj->txt)) {
                        $txt_value = $obj->txt['value'];
                        if (!empty($_POST['submit'])) { //on a POST with validation error, display POSTed value.
                            $txt_value = $_POST[$obj->txt['name']];
                        }
                        ?>
                        <input type="text" name="<?php echo esc_attr($obj->txt['name']); ?>" placeholder="<?php echo esc_attr($placeholder); ?>" value="<?php echo esc_attr($txt_value); ?>" data-depends="<?php echo esc_attr($name); ?>" data-depends-on="<?php echo esc_attr($obj->txt['depends_on']); ?>" />
                        <?php
                    }
                    ?>
                    <?php
                    if (!empty($obj->help)) {
                        $this->echo_help_tooltip($obj->help, $obj->help_current_value);
                    }
                    ?>
            </tr>
            <?php
        }

        protected function echo_help_tooltip($pretext, $current_value) {
            $title = esc_attr($pretext);
            if (!empty($this->taxonomy_obj)) {
                $title .= '<br />' . esc_attr(sprintf(__('Current value is "<b>%s</b>"', 'wpfront-user-role-editor'), $current_value));
            }
            ?>
            <i class="fa fa-question-circle-o" title="<?php echo $title; ?>"></i>
            <?php
        }

        protected function get_property_value($prop, $default = '') {
            if (!empty($_POST['submit'])) {
                if (!isset($_POST[$prop])) {
                    return $default;
                }

                if ($prop === 'customize_capability') {
                    if ($_POST[$prop] === '') { //for customize_capability, default = null, false = ''
                        return $default;
                    }
                }

                return $_POST[$prop];
            }

            if (!empty($this->taxonomy_data)) { //edit
                if (isset($this->taxonomy_data->taxonomy_arg[$prop])) {
                    return $this->taxonomy_data->taxonomy_arg[$prop];
                }
                if (isset($this->taxonomy_data->$prop)) {
                    return $this->taxonomy_data->$prop;
                }

                if ($prop === 'post_types') {
                    return $this->taxonomy_data->$prop;
                }

                if ($prop === 'customize_capability') {
                    return $this->taxonomy_data->capability_type;
                }

                return null;
            }

            if (!empty($this->clone_from)) { //add with clone
                if (isset($this->clone_from->taxonomy_arg)) { //our taxonomy
                    if (isset($this->clone_from->taxonomy_arg[$prop])) {
                        return $this->clone_from->taxonomy_arg[$prop];
                    }

                    if (isset($this->clone_from->$prop)) { //our taxonomy
                        return $this->clone_from->$prop;
                    }

                    return null;
                }
                return null;
            }

            return $default;
        }

        protected function get_labels_value($prop, $default = '') {
            if (!empty($_POST['submit'])) {
                return empty($_POST[$prop]) ? $default : $_POST[$prop];
            }

            if (!empty($this->taxonomy_data)) { //edit
                if (isset($this->taxonomy_data->taxonomy_arg['labels'][$prop])) {
                    return $this->taxonomy_data->taxonomy_arg['labels'][$prop];
                }
            }
            if (!empty($this->clone_from)) { //add with clone
                if (isset($this->clone_from->taxonomy_arg['labels'][$prop])) { //our taxonomy
                    return $this->clone_from->taxonomy_arg['labels'][$prop];
                }
            }
            return $default;
        }

        protected function get_current_labels_value($prop) {
            if (!empty($this->taxonomy_obj) && isset($this->taxonomy_obj->labels) && isset($this->taxonomy_obj->labels->$prop)) {
                return $this->taxonomy_obj->labels->$prop;
            }

            return '';
        }

        protected function get_current_property_value($prop) {
            if (!empty($this->taxonomy_obj)) {
                if (isset($this->taxonomy_obj->$prop)) {
                    if ($prop == 'rest_base' && $this->taxonomy_obj->$prop == '') {
                        return $this->taxonomy_obj->name;
                    } elseif ($prop == 'rest_controller_class' && $this->taxonomy_obj->$prop == '') {
                        return 'WP_REST_Terms_Controller';
                    } else {
                        return $this->taxonomy_obj->$prop;
                    }
                }
            }

            return null; //Either on ADD screen or on a Deactivated taxonomy.
        }

        protected function scripts() {
            ?>
            <script type="text/javascript">
                (function ($) {
                    var $div = $('div.wrap.taxonomy-add-edit');

                    $div.on('change', 'select.has-depends', function () {
                        var $select = $(this);
                        var $d = $select.next();
                        var on = $d.data('depends-on');
                        if ($select.val() == on) {
                            $d.show();
                        } else {
                            $d.hide();
                        }
                    });

                    //rewrite disable
                    $div.on('change', "select[name='rewrite']", function () {
                        var $rewrite_slug = $("input[name='rewrite_slug']");
                        var $ep_mask = $("input[name='rewrite_ep_mask']");
                        var $with_front = $("select[name='rewrite_with_front']");
                        var $hierarchical = $("select[name='rewrite_hierarchical']");
                        if ($(this).val() === '1') {
                            $rewrite_slug.prop('disabled', false);
                            $ep_mask.prop('disabled', false);
                            $with_front.prop('disabled', false);
                            $hierarchical.prop('disabled', false);
                        } else {
                            $rewrite_slug.prop('disabled', true);
                            $ep_mask.prop('disabled', true);
                            $with_front.prop('disabled', true);
                            $hierarchical.prop('disabled', true);
                        }
                    });

                    //auto populate labels and validation
                    $div.find(".auto-populate-labels").on('click', function () {
                        var S0 = $("input[name='label']");
                        var S1 = $("input[name='singular_name']");

                        var ret = false;
                        if ($.trim(S1.val()) === "") {
                            S1.parent().parent().addClass("form-invalid");
                            S1.focus();
                            ret = true;
                        } else {
                            S1.parent().parent().removeClass("form-invalid");
                        }

                        if ($.trim(S0.val()) === "") {
                            S0.parent().parent().addClass("form-invalid");
                            S0.focus();
                            ret = true;
                        } else {
                            S0.parent().parent().removeClass("form-invalid");
                        }

                        if (ret) {
                            return false;
                        }

                        S0 = $.trim(S0.val());
                        S1 = $.trim(S1.val());
                        var s0 = S0.toLowerCase();
                        var s1 = S1.toLowerCase();

                        $div.find("#postbox-labels input.auto-populate").each(function (i, e) {
                            var $e = $(e);
                            if ($e.val() !== "" || $e.is(":disabled")) {
                                return;
                            }

                            var format = $e.data("auto-format");
                            if (typeof String.prototype.replaceAll === "function") {
                                format = format.replaceAll("%S0", S0);
                                format = format.replaceAll("%S1", S1);
                                format = format.replaceAll("%s0", s0);
                                format = format.replaceAll("%s1", s1);
                            } else {
                                format = format.replace("%S0", S0);
                                format = format.replace("%S1", S1);
                                format = format.replace("%s0", s0);
                                format = format.replace("%s1", s1);
                            }

                            $e.val(format);
                        });

                        return false;
                    });

                    //hierarchical disable
                    $div.on('change', "select[name='hierarchical']", function () {
                        var $parent_item = $("input[name='parent_item']");
                        var $popular_items = $("input[name='popular_items']");
                        var $parent_item_colon = $("input[name='parent_item_colon']");
                        var $separate_items_with_commas = $("input[name='separate_items_with_commas']");
                        var $add_or_remove_items = $("input[name='add_or_remove_items']");
                        var $choose_from_most_used = $("input[name='choose_from_most_used']");
                        var $filter_by_item = $("input[name='filter_by_item']");
                        if ($(this).val() === '1') {
                            $popular_items.prop('disabled', true);
                            $parent_item.prop('disabled', false);
                            $parent_item_colon.prop('disabled', false);
                            $separate_items_with_commas.prop('disabled', true);
                            $add_or_remove_items.prop('disabled', true);
                            $choose_from_most_used.prop('disabled', true);
                            $filter_by_item.prop('disabled', false);
                        } else {
                            $popular_items.prop('disabled', false);
                            $parent_item.prop('disabled', true);
                            $parent_item_colon.prop('disabled', true);
                            $separate_items_with_commas.prop('disabled', false);
                            $add_or_remove_items.prop('disabled', false);
                            $choose_from_most_used.prop('disabled', false);
                            $filter_by_item.prop('disabled', true);
                        }
                    });

                    //validation
                    $div.children('form').on('submit', function () {
                        var name = $("input[name='name']");
                        var labels = $("input[name='label']");
                        var label = $("input[name='singular_name']");

                        var ele = null;

                        if ($.trim(label.val()) == "") {
                            label.parent().parent().addClass("form-invalid");
                            ele = label;
                        }

                        if ($.trim(labels.val()) == "") {
                            labels.parent().parent().addClass("form-invalid");
                            ele = labels;
                        }

                        if ($.trim(name.val()) == "") {
                            name.parent().parent().addClass("form-invalid");
                            ele = name;
                        }

                        if (ele !== null) {
                            ele.focus();
                            return false;
                        }
                    });
                    ;

                    $div.find(".chosen-select").chosen({width: "27em"});

                    $div.find("select.has-depends, select[name='rewrite'], select[name='hierarchical']").trigger('change');
                    
                    //clear labels
                    $div.find(".clear-labels").on('click', function () {
                        $div.find("#postbox-labels input.auto-populate").val('');
                        return false;
                    });

                    //postbox
                    $(function () {
                        $div.find('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                        postboxes.add_postbox_toggles('<?php echo $this->controller->get_menu_slug(); ?>');
                    });

                    //for tooltip
                    $(function () {
                        $div.find('i').tooltip({
                            position: {my: "left+10 center", at: "right center"},
                            content: function () {
                                return $(this).prop('title');
                            },
                            hide: 50
                        });
                    });
                })(jQuery);
            </script>
            <?php
        }

    }

}