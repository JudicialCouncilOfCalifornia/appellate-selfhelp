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
 * Template for WPFront User Role Editor Post Type Add Edit
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Post_Type;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;

if (!class_exists('WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type_Add_Edit_View')) {

    class WPFront_User_Role_Editor_Post_Type_Add_Edit_View {

        /**
         *
         * @var WPFront_User_Role_Editor_Post_Type 
         */
        private $controller;
        private $post_type_data;
        private $post_type_obj;
        private $clone_from;

        public function __construct($controller, $data = null, $post_type_obj = null, $clone = null) {
            $this->controller = $controller;
            $this->post_type_data = $data;
            $this->post_type_obj = $post_type_obj;
            $this->clone_from = $clone;
        }

        public function view() {
            ?>
            <div class="wrap post-type-add-edit">
                <?php $this->title(); ?>
                <?php $this->display_notices(); ?>
                <?php
                if (empty($this->post_type_data)) {
                    $action = $this->controller->get_add_new_url();
                } else {
                    $action = $this->controller->get_edit_url($this->post_type_data->name);
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
                    <?php wp_nonce_field('add-edit-post-type'); ?>
                    <?php submit_button(null, 'primary', 'submit2', false); ?>
                </form>
            </div>
            <?php $this->scripts(); ?>
            <?php
        }

        public function title() {
            if (empty($this->post_type_data)) {
                ?>              
                <h2>
                    <?php echo __('Add New Post Type', 'wpfront-user-role-editor'); ?>
                </h2>
                <?php
            } else {
                ?>
                <h2>
                    <?php echo __('Edit Post Type', 'wpfront-user-role-editor'); ?>
                </h2>
                <?php
            }
        }

        protected function display_notices() {
            if (!empty($this->controller->errorMsg)) {
                Utils::notice_error($this->controller->errorMsg);
            }

            if (!empty($_GET['post-type-added'])) {
                Utils::notice_updated(__('Post type added successfully.', 'wpfront-user-role-editor'));
            } elseif (!empty($_GET['post-type-updated'])) {
                Utils::notice_updated(__('Post type updated successfully.', 'wpfront-user-role-editor'));
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
                    $this->dropdown_basic_settings(
                            'Status',
                            'status',
                            true
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
                            __('Menu Name Label', 'wpfront-user-role-editor'),
                            'menu_name',
                            __('Label for the menu name. Default is the same as plural label. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            '%S0'
                    );
                    $this->textbox_additional_labels(
                            __('Add New Label', 'wpfront-user-role-editor'),
                            'add_new',
                            __('Default is Add New for both non-hierarchical (like posts) and hierarchical (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Add New', 'wpfront-user-role-editor')
                    );
                    ?>
                    <?php
                    $this->textbox_additional_labels(
                            __('Add New Item Label', 'wpfront-user-role-editor'),
                            'add_new_item',
                            __('Label for adding a new singular item. Default is Add New Post for non-hierarchical types (like posts) and Add New Page for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Add New %S1', 'wpfront-user-role-editor')
                    );
                    ?>
                    <?php
                    $this->textbox_additional_labels(
                            __('Edit Item Label', 'wpfront-user-role-editor'),
                            'edit_item',
                            __('Label for editing a new singular item. Default is Edit Post for non-hierarchical types (like posts) and Edit Page for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Edit %S1', 'wpfront-user-role-editor')
                    );
                    ?>    
                    <?php
                    $this->textbox_additional_labels(
                            __('New Item Label', 'wpfront-user-role-editor'),
                            'new_item',
                            __('Label for the new item page title. Default is New Post for non-hierarchical types (like posts) and New Page for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('New %S1', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('View Item Label', 'wpfront-user-role-editor'),
                            'view_item',
                            __('Label for viewing a singular item. Default is View Post for non-hierarchical types (like posts) and View Page for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('View %S1', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('View Items Label', 'wpfront-user-role-editor'),
                            'view_items',
                            __('Label for viewing post type archives. Default is View Posts for non-hierarchical types (like posts) and View Pages for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('View %S0', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Search Items Label', 'wpfront-user-role-editor'),
                            'search_items',
                            __('Label for searching plural items. Default is Search Posts for non-hierarchical types (like posts) and Search Pages for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Search %S0', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Not Found Label', 'wpfront-user-role-editor'),
                            'not_found',
                            __('Label used when no items are found. Default is No posts found for non-hierarchical types (like posts) and No pages found for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('No %s0 found.', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Not Found in Trash Label', 'wpfront-user-role-editor'),
                            'not_found_in_trash',
                            __('Label used when no items are found in the trash. Default is No posts found in trash for non-hierarchical types (like posts) and No pages found in trash for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('No %s0 found in Trash.', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Parent Item Colon Label', 'wpfront-user-role-editor'),
                            'parent_item_colon',
                            __('Label used to prefix parents of hierarchical items. Not used on non-hierarchical post types. Default is Parent Page:. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Parent %S1:', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('All Items Label', 'wpfront-user-role-editor'),
                            'all_items',
                            __('Label to signify all items in a submenu link. Default is All posts for non-hierarchical types (like posts) and All pages for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('%S0', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Archives Label', 'wpfront-user-role-editor'),
                            'archives',
                            __('Label for archives in nav menus. Default is Post Archives for non-hierarchical types (like posts) and Page Archives for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('%S0', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Insert into item Label', 'wpfront-user-role-editor'),
                            'insert_into_item',
                            __(' Label for the media frame button. Default is Insert into post for non-hierarchical types (like posts) and Insert into page for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Insert into %s1', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Uploaded to this item Label', 'wpfront-user-role-editor'),
                            'uploaded_to_this_item',
                            __(' Label for the media frame filter. Default is Uploaded to this post for non-hierarchical types (like posts) and Uploaded to this page for hierarchical types (like pages). Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Uploaded to this %s1', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Featured Image Label', 'wpfront-user-role-editor'),
                            'featured_image',
                            __('Label for the featured image meta box title. Default is Featured image. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Featured image', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Set Featured Image Label', 'wpfront-user-role-editor'),
                            'set_featured_image',
                            __('Label for setting the featured image. Default is Set featured image. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Set featured image', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Remove Featured Image Label', 'wpfront-user-role-editor'),
                            'remove_featured_image',
                            __('Label for removing the featured image. Default is Remove featured image. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Remove featured image', 'wpfront-user-role-editor')
                    );
                    ?>  
                    <?php
                    $this->textbox_additional_labels(
                            __('Use Featured Image Label', 'wpfront-user-role-editor'),
                            'use_featured_image',
                            __('Label in the media frame for using a featured image. Default is Use as featured image. Leave empty to use default value.', 'wpfront-user-role-editor'),
                            __('Use as featured image', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Filter Items List Label', 'wpfront-user-role-editor'),
                            'filter_items_list',
                            __('Label for the table views hidden heading. Default is Filter posts list for non-hierarchical types (like posts) and Filter pages list for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('Filter %s0 list', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Item List Navigation Label', 'wpfront-user-role-editor'),
                            'items_list_navigation',
                            __('Label for the table pagination hidden heading. Default is Posts lists navigation for non-hierarchical types (like posts) and Pages lists navigation for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('%S0 list navigation', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Item List Label', 'wpfront-user-role-editor'),
                            'items_list',
                            __('Label for the table hidden heading. Default is Posts list for non-hierarchical types (like posts) and Pages list for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('%S0 list', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Item Published Label', 'wpfront-user-role-editor'),
                            'item_published',
                            __('Label used when an item is published. Default is Post publsihed for non-hierarchical types (like posts) and Page published for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('%S1 published.', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Item Published Privately Label', 'wpfront-user-role-editor'),
                            'item_published_privately',
                            __('Label used when an item is published private visibility. Default is Post publsihed privately for non-hierarchical types (like posts) and Page published privately for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('%S1 published privately.', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Item Reverted to Draft Label', 'wpfront-user-role-editor'),
                            'item_reverted_to_draft',
                            __('Label used when an item is switched to a draft. Default is Post reverted to draft for non-hierarchical types (like posts) and Page reverted to draft for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('%S1 reverted to draft.', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Item Scheduled Label', 'wpfront-user-role-editor'),
                            'item_scheduled',
                            __('Label used when an item is scheduled for publishing. Default is Post scheduled for non-hierarchical types (like posts) and Page scheduled for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('%S1 scheduled.', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Item Updated', 'wpfront-user-role-editor'),
                            'item_updated',
                            __('Label used when an item is updated. Default is Post updated for non-hierarchical types (like posts) and Page updated for hierarchical types (like pages).', 'wpfront-user-role-editor'),
                            __('%S1 updated.', 'wpfront-user-role-editor')
                    );
                    ?> 
                    <?php
                    $this->textbox_additional_labels(
                            __('Description', 'wpfront-user-role-editor'),
                            'description',
                            __('A short descriptive summary of what the post type is.', 'wpfront-user-role-editor')
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
                            __('Whether a post type is intended for use publicly either via the admin interface or by front-end users. Default value is false.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Hierarchical', 'wpfront-user-role-editor'),
                            'hierarchical',
                            __('Whether the post type is hierarchical (e.g. page). Default is false.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Exclude From Search', 'wpfront-user-role-editor'),
                            'exclude_from_search',
                            __('Whether to exclude posts with this post type from front end search results. Default is the opposite value of <b>"Public"</b>.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Publicaly Queryable', 'wpfront-user-role-editor'),
                            'publicly_queryable',
                            __('Whether queries can be performed on the front end for the post type as part of parse_request(). Default is inherited from <b>"Public"</b>.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show UI', 'wpfront-user-role-editor'),
                            'show_ui',
                            __('Whether to generate and allow a UI for managing this post type in the admin. Default is value of <b>"Public"</b>.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in Menu', 'wpfront-user-role-editor'),
                            'show_in_menu',
                            __('Where to show the post type in the admin menu. If true, the post type is shown in its own top level menu. If false, no menu is shown. Default is value of <b>"Show UI"</b>.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in Nav Menu', 'wpfront-user-role-editor'),
                            'show_in_nav_menus',
                            __('Makes this post type available for selection in navigation menus. Default is value of <b>"Public"</b>.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in Admin Bar', 'wpfront-user-role-editor'),
                            'show_in_admin_bar',
                            __('Makes this post type available via the admin bar. Default is value of <b>"Show in Menu"</b>.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Show in REST', 'wpfront-user-role-editor'),
                            'show_in_rest',
                            __('Whether to include the post type in the REST API. Set this to true for the post type to be available in the block editor.', 'wpfront-user-role-editor'),
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
                            __('REST API Controller class name. Default is WP_REST_Posts_Controller', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Has Archive', 'wpfront-user-role-editor'),
                            'has_archive',
                            __('Whether there should be post type archives, or if a string, the archive slug to use. Will generate the proper rewrite rules if <b>"Rewrite"</b> is enabled. Default false.', 'wpfront-user-role-editor'),
                            true
                    );
                    $this->textbox_advanced_settings(
                            __('Menu Position', 'wpfront-user-role-editor'),
                            'menu_position',
                            __('The position in the menu order the post type should appear. To work, <b>"show_in_menu"</b> must be true. Default is null.', 'wpfront-user-role-editor')
                    );
                    $this->textbox_advanced_settings(
                            __('Menu Icon', 'wpfront-user-role-editor'),
                            'menu_icon',
                            __('The url to the icon to be used for this menu. Defaults to use the posts icon.', 'wpfront-user-role-editor')
                    );
                    $this->multilist_advanced_settings(
                            __('Supports', 'wpfront-user-role-editor'),
                            'supports',
                            __('Core feature(s) the post type supports. Serves as an alias for calling add_post_type_support() directly.', 'wpfront-user-role-editor')
                    );
                    $this->textbox_advanced_settings(
                            __('Custom Supports', 'wpfront-user-role-editor'),
                            'custom_supports',
                            __('Core feature(s) the post type supports. Serves as an alias for calling add_post_type_support() directly.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Can Export', 'wpfront-user-role-editor'),
                            'can_export',
                            __('Whether to allow this post type to be exported. Default true.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Query Var', 'wpfront-user-role-editor'),
                            'query_var',
                            __('Sets the query_var key for this post type. Defaults to <b>"Name"</b> key. If false, a post type cannot be loaded at ?{query_var}={post_slug}. If specified as a string, the query ?{query_var_string}={post_slug} will be valid.', 'wpfront-user-role-editor')
                    );
                    $this->dropdown_advanced_settings_boolean(
                            __('Delete with User', 'wpfront-user-role-editor'),
                            'delete_with_user',
                            __('Whether to delete posts of this type when deleting a user. If true, posts of this type belonging to the user will be moved to Trash when the user is deleted. If false, posts of this type belonging to the user will not be trashed or deleted. If not set (the default), posts are trashed if post type supports the author feature. Otherwise posts are not trashed or deleted. Default is null.', 'wpfront-user-role-editor')
                    );
                    $this->multilist_advanced_settings(
                            __('Taxonomies', 'wpfront-user-role-editor'),
                            'taxonomies',
                            __('An array of taxonomy identifiers that will be registered for the post type.', 'wpfront-user-role-editor')
                    );
                    $this->rewrite_settings();
                    ?>                                     
                </tbody>
            </table>
            <?php
        }

        protected function textbox_basic_settings($label, $name) {
            $value = $this->get_property_value($name);
            $attr = 'maxlength="20" ';
            if ($name === 'name') {
                if (!empty($this->post_type_data)) {
                    $value = $this->post_type_data->name;
                    $attr = 'disabled="true"';
                }
            } elseif ($name === 'singular_name') {
                $value = $this->get_labels_value('singular_name');
            }

            $this->textbox_row($label, $name, $value, (object) ['required' => true, 'attr' => $attr]);
        }

        protected function dropdown_basic_settings($label, $name, $default_value = '', $arg_value = null, $current_property_value = null) {
            $options = [(object) ['label' => __('Active', 'wpfront-user-role-editor'), 'value' => true], (object) ['label' => __('Inactive', 'wpfront-user-role-editor'), 'value' => false]];
            $attr = '';
            if ($arg_value === null) {
                $arg_value = $this->get_property_value($name, $default_value);
            }

            if ($arg_value === 1) {
                $arg_value = true;
            } elseif ($arg_value === 0) {
                $arg_value = false;
            }

            if ($current_property_value === true) {
                $current_property_value = __('Activate', 'wpfront-user-role-editor');
            } elseif ($current_property_value === false) {
                $current_property_value = __('Deactivate', 'wpfront-user-role-editor');
            }

            if (isset($this->post_type_data->source_type) && $this->post_type_data->source_type === $this->controller::SOURCE_TYPE_BUILTIN) {
                $attr = 'disabled="true"';
            }

            $this->dropdown_row($label, $name, $options, $arg_value, (object) ['attr' => $attr]);
        }

        protected function textbox_additional_labels($label, $name, $help, $auto_format = '') {
            $this->textbox_row($label, $name, $this->get_labels_value($name), (object) ['help' => $help, 'help_current_value' => $this->get_current_labels_value($name), 'auto_format' => $auto_format]);
        }

        protected function dropdown_advanced_settings_boolean($label, $name, $help, $default_value = '', $exclude_default = false, $arg_value = null, $current_property_value = null) {
            $options = [(object) ['label' => __('True', 'wpfront-user-role-editor'), 'value' => true], (object) ['label' => __('False', 'wpfront-user-role-editor'), 'value' => false]];
            if (!$exclude_default) {
                array_unshift($options, (object) ['label' => __('Default', 'wpfront-user-role-editor'), 'value' => '']);
            }

            if ($name === 'map_meta_cap') {
                $customized = $this->controller->get_customizied_custom_post_types_from_settings();
                if (!empty($customized)) {
                    $value = $this->get_property_value('name');
                    if (in_array($value, $customized)) {
                        $arg_value = true;
                    } else {
                        $arg_value = null;
                    }
                }
            }

            if ($arg_value === null) {
                $arg_value = $this->get_property_value($name, $default_value);
            }

            if ($current_property_value === null) {
                $current_property_value = $this->get_current_property_value($name);
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

            $has_depends = null;
            switch ($name) {
                case 'has_archive':
                case 'show_in_menu':
                case 'query_var':
                    $has_depends = $name . '_slug';
                    break;

                default:
                    break;
            }

            if (!empty($has_depends)) {
                if (empty($arg_value) || is_bool($arg_value)) { //selected default or true/false
                    $current_property_value = '';
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

        protected function rewrite_settings() {
            $arg_value = $this->get_property_value('rewrite', '');
            if ($arg_value === null) {
                $arg_value = '';
            }

            $current_property_value = $this->get_current_property_value('rewrite');

            $this->dropdown_advanced_settings_boolean(
                    __('Rewrite', 'wpfront-user-role-editor'),
                    'rewrite',
                    __('Triggers the handling of rewrites for this post type. To prevent rewrite, set to false. Defaults to true, using <b>"Name"</b> as slug. ', 'wpfront-user-role-editor'),
                    '',
                    false,
                    is_array($arg_value) ? true : $arg_value,
                    !empty($current_property_value) ? 'True' : 'False'
            );

            $this->textbox_advanced_settings(
                    __('Rewrite Slug', 'wpfront-user-role-editor'),
                    'rewrite_slug',
                    __('Customize the permastruct slug. Defaults to <b>"Name"</b> key.', 'wpfront-user-role-editor'),
                    isset($arg_value['slug']) ? $arg_value['slug'] : null,
                    isset($current_property_value['slug']) ? $current_property_value['slug'] : ''
            );


            $this->dropdown_advanced_settings_boolean(
                    __('Rewrite With Front', 'wpfront-user-role-editor'),
                    'rewrite_with_front',
                    __('Whether the permastruct should be prepended with WP_Rewrite::$front. Default true. ', 'wpfront-user-role-editor'),
                    '',
                    false,
                    isset($arg_value['with_front']) ? $arg_value['with_front'] : null,
                    isset($current_property_value['with_front']) ? $current_property_value['with_front'] : ''
            );

            $this->dropdown_advanced_settings_boolean(
                    __('Rewrite Feeds', 'wpfront-user-role-editor'),
                    'rewrite_feeds',
                    __('Whether the feed permastruct should be built for this post type. Default is value of <b>"has_archive"<b>.', 'wpfront-user-role-editor'),
                    '',
                    false,
                    isset($arg_value['feeds']) ? $arg_value['feeds'] : null,
                    isset($current_property_value['feeds']) ? $current_property_value['feeds'] : ''
            );

            $this->dropdown_advanced_settings_boolean(
                    __('Rewrite Pages', 'wpfront-user-role-editor'),
                    'rewrite_pages',
                    __('Whether the permastruct should provide for pagination. Default true.', 'wpfront-user-role-editor'),
                    '',
                    false,
                    isset($arg_value['pages']) ? $arg_value['pages'] : null,
                    isset($current_property_value['pages']) ? $current_property_value['pages'] : ''
            );

            $this->textbox_advanced_settings(
                    __('Rewrite EP Mask', 'wpfront-user-role-editor'),
                    'rewrite_ep_mask',
                    __('Endpoint mask to assign. If not specified and permalink_epmask is set, inherits from $permalink_epmask. If not specified and permalink_epmask is not set, defaults to EP_PERMALINK.', 'wpfront-user-role-editor'),
                    isset($arg_value['ep_mask']) ? $arg_value['ep_mask'] : null,
                    isset($current_property_value['ep_mask']) ? $current_property_value['ep_mask'] : ''
            );
            return;
        }

        protected function textbox_advanced_settings($label, $name, $help, $prop_value = null, $prop_current_value = null) {
            if ($name === 'custom_supports') {
                $prop_value = $this->get_property_value('supports');
                $core_supports = array_keys($this->get_core_supports());
                if (is_array($prop_value)) {
                    $prop_value = array_diff($prop_value, $core_supports);
                    $prop_value = implode(', ', $prop_value);
                }

                if (empty($prop_value)) {
                    $prop_value = $this->get_property_value($name);
                }

                $prop_current_value = '';
                if (isset($this->post_type_obj->name)) {
                    $prop_current_value = array_keys(get_all_post_type_supports($this->post_type_obj->name));
                    if (is_array($prop_current_value)) {
                        $prop_current_value = array_diff($prop_current_value, $core_supports);
                    } else {
                        $prop_current_value = [];
                    }
                    $prop_current_value = implode(', ', $prop_current_value);
                }
            } elseif ($name === 'capability_type') {
                $customized = $this->controller->get_customizied_custom_post_types_from_settings();
                if (!empty($customized)) {
                    $value = $this->get_property_value('name');
                    if (in_array($value, $customized)) {
                        $prop_value = $value;
                    }
                }

                if (empty($prop_value)) {
                    $prop_value = $this->get_property_value($name);
                }
                $prop_current_value = $this->get_current_property_value($name);
            } else {
                if ($prop_value === null) {
                    $prop_value = $this->get_property_value($name);
                }

                if ($prop_current_value === null) {
                    $prop_current_value = $this->get_current_property_value($name);
                }
            }

            $this->textbox_row($label, $name, $prop_value, (object) ['help' => $help, 'help_current_value' => $prop_current_value]);
        }

        protected function checkbox_advanced_settings($label, $name, $help) {
            $this->checkbox_row($label, $name, (object) ['help' => $help]);
        }

        protected function multilist_advanced_settings($label, $name, $help) {
            $options = [];
            $values = $this->get_property_value($name);
            $current_property_value = '';

            if ($name == 'supports') {
                $options = $this->get_core_supports();
                if (isset($this->post_type_obj->name)) {
                    $current_property_value = get_all_post_type_supports($this->post_type_obj->name);
                }
            } else if ($name == 'taxonomies') {
                $taxonomies = get_taxonomies(['public' => true], 'objects');
                foreach ($taxonomies as $taxonomy) {
                    $options[$taxonomy->name] = $taxonomy->label;
                }

                if (isset($this->post_type_obj->name)) {
                    $current_property_value = get_object_taxonomies($this->post_type_obj->name, 'objects');
                }
            }

            if (is_array($current_property_value)) {
                $p = [];
                foreach ($current_property_value as $key => $value) {
                    if (!empty($options[$key])) {
                        $p[] = $options[$key];
                    }
                }
                $current_property_value = implode(', ', $p);
            } else {
                $current_property_value = strval($current_property_value);
            }

            $obj = ['help' => $help, 'help_current_value' => $current_property_value];

            $this->multilist_row($label, $name, $values, $options, (object) $obj);
        }

        private function get_core_supports() {
            return [
                'title' => __('Title', 'wpfront-user-role-editor'),
                'editor' => __('Editor', 'wpfront-user-role-editor'),
                'comments' => __('Comments', 'wpfront-user-role-editor'),
                'revisions' => __('Revisions', 'wpfront-user-role-editor'),
                'trackbacks' => __('Trackbacks', 'wpfront-user-role-editor'),
                'author' => __('Author', 'wpfront-user-role-editor'),
                'excerpt' => __('Excerpt', 'wpfront-user-role-editor'),
                'page-attributes' => __('Page Attributes', 'wpfront-user-role-editor'),
                'thumbnail' => __('Thumbnail', 'wpfront-user-role-editor'),
                'custom-fields' => __('Custom Fields', 'wpfront-user-role-editor'),
                'post-formats' => __('Post Formats', 'wpfront-user-role-editor')
            ];
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
                    <input id="<?php echo esc_attr($name); ?>" class="regular-text <?php echo $class; ?>" name="<?php echo esc_attr($name); ?>" type="text" value="<?php echo esc_attr($value); ?>" <?php echo $attr; ?> />
                    <?php
                    if (!empty($obj->help)) {
                        $this->echo_help_tooltip($obj->help, $obj->help_current_value);
                    }
                    ?>
                </td>
            </tr>
            <?php
        }

        protected function dropdown_row($label, $name, $options, $value, $obj) {
            $attr = '';
            $placeholder = '';
            if (!empty($obj->attr)) {
                $attr .= $obj->attr . ' ';
            }
            if ($name === 'show_in_menu') {
                $placeholder = __('top level menu', 'wpfront-user-role-editor');
            }
            if ($name === 'has_archive' || $name === 'query_var') {
                if (isset($this->post_type_obj->name)) {
                    $placeholder = $this->post_type_obj->name;
                }
            }
            ?>             
            <tr>
                <th scope="row">
                    <?php echo esc_html($label); ?>
                </th>
                <td>
                    <select name="<?php echo esc_attr($name); ?>" class="<?php echo!empty($obj->txt) ? 'has-depends' : ''; ?>" <?php echo $attr; ?> >
                        <?php
                        foreach ($options as $option) {
                            $selected = $option->value === $value ? 'selected' : '';
                            if ($option->value === true) {
                                $option->value = '1';
                            } elseif ($option->value === false) {
                                $option->value = '0';
                            }
                            $option_value = esc_attr($option->value);
                            $option_label = esc_html($option->label);
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

        protected function multilist_row($label, $name, $values, $options, $obj) {
            if (empty($values)) {
                $values = [];
            }
            ?>
            <tr>
                <th scope="row">
                    <?php
                    echo esc_html($label);
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

        protected function echo_help_tooltip($pretext, $current_value) {
            $title = esc_attr($pretext);
            if (!empty($this->post_type_obj)) {
                $title .= '<br />' . esc_attr(sprintf(__('Current value is "<b>%s</b>"', 'wpfront-user-role-editor'), $current_value));
            }
            ?>
            <i class="fa fa-question-circle-o" title="<?php echo $title; ?>"></i>
            <?php
        }

        protected function get_property_value($prop, $default = '') {
            if (!empty($_POST['submit'])) {
                return !isset($_POST[$prop]) ? $default : $_POST[$prop];
            }

            if (!empty($this->post_type_data)) { //edit
                if ($prop === 'taxonomies') {
                    return $this->post_type_data->$prop;
                }

                if (isset($this->post_type_data->post_type_arg[$prop])) {
                    return $this->post_type_data->post_type_arg[$prop];
                }

                if (isset($this->post_type_data->$prop)) {
                    return $this->post_type_data->$prop;
                }

                return null;
            }

            if (!empty($this->clone_from)) { //add with clone
                if (isset($this->clone_from->post_type_arg)) { //our post type
                    if (isset($this->clone_from->post_type_arg[$prop])) {
                        $f = $this->clone_from->post_type_arg[$prop];
                        return $this->clone_from->post_type_arg[$prop];
                    }

                    if (isset($this->clone_from->$prop)) { //our post type                 
                        return $this->clone_from->$prop;
                    }

                    return null;
                }



                if (is_object($this->clone_from->post_type_obj)) { //for built-in and other                  
                    if ($prop === 'supports') {
                        $supports = get_all_post_type_supports($this->clone_from->post_type_obj->name);
                        if (is_array($supports)) {
                            return array_keys($supports);
                        }

                        return [];
                    }

                    if ($prop === 'taxonomies') {
                        return get_object_taxonomies($this->clone_from->post_type_obj->name);
                    }

                    if (isset($this->clone_from->post_type_obj->$prop)) {
                        return $this->clone_from->post_type_obj->$prop;
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

            if (!empty($this->post_type_data)) { //edit
                if (isset($this->post_type_data->post_type_arg['labels'][$prop])) {
                    return $this->post_type_data->post_type_arg['labels'][$prop];
                }
            }

            if (!empty($this->clone_from)) { //add with clone
                if (isset($this->clone_from->post_type_arg['labels'][$prop])) { //our post type
                    return $this->clone_from->post_type_arg['labels'][$prop];
                }

                if (isset($this->clone_from->post_type_obj->labels->$prop)) { //for built-in and others
                    return $this->clone_from->post_type_obj->labels->$prop;
                }
            }

            return $default;
        }

        protected function get_current_labels_value($prop) {
            if (!empty($this->post_type_obj) && isset($this->post_type_obj->labels) && isset($this->post_type_obj->labels->$prop)) {
                return $this->post_type_obj->labels->$prop;
            }

            return '';
        }

        protected function get_current_property_value($prop) {
            if (!empty($this->post_type_obj)) {
                if (isset($this->post_type_obj->$prop)) {
                    if ($prop == 'rest_base' && $this->post_type_obj->$prop == '') {
                        return $this->post_type_obj->name;
                    } elseif ($prop == 'rest_controller_class' && $this->post_type_obj->$prop == '') {
                        return 'WP_REST_Posts_Controller';
                    } else {
                        return $this->post_type_obj->$prop;
                    }
                }

                if ($prop === 'menu_position') {
                    return __('at the bottom', 'wpfront-user-role-editor');
                }

                if ($prop === 'menu_icon') {
                    return __('use "Posts" menu icon', 'wpfront-user-role-editor');
                }
            }

            return null; //Either on ADD screen or on a Deactivated post type.
        }

        protected function scripts() {
            ?>           
            <script type="text/javascript">
                (function ($) {
                    var $div = $('div.wrap.post-type-add-edit');

                    //select change to hide/show textboxes.
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
                        var $feeds = $("select[name='rewrite_feeds']");
                        var $pages = $("select[name='rewrite_pages']");
                        if ($(this).val() === '1') {
                            $rewrite_slug.prop('disabled', false);
                            $ep_mask.prop('disabled', false);
                            $with_front.prop('disabled', false);
                            $feeds.prop('disabled', false);
                            $pages.prop('disabled', false);
                        } else {
                            $rewrite_slug.prop('disabled', true);
                            $ep_mask.prop('disabled', true);
                            $with_front.prop('disabled', true);
                            $feeds.prop('disabled', true);
                            $pages.prop('disabled', true);
                        }
                    });

                    //parent item colon
                    $div.on('change', "select[name='hierarchical']", function () {
                        var $parent_item_colon = $("input[name='parent_item_colon']");
                        if ($(this).val() === '1') {
                            $parent_item_colon.prop('disabled', false);
                        } else {
                            $parent_item_colon.prop('disabled', true);
                        }
                    });

                    $div.find("select.has-depends, select[name='rewrite'], select[name='hierarchical']").trigger('change');

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

                    //clear labels
                    $div.find(".clear-labels").on('click', function () {
                        $div.find("#postbox-labels input.auto-populate").val('');
                        return false;
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

                    $div.find(".chosen-select").chosen({width: "27em"});

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



    