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
 * Controller for WPFront User Role Editor Post Type
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Post_Type;

if (!defined('ABSPATH')) {
    exit();
}

require_once dirname(__FILE__) . '/entity-post-type.php';
require_once dirname(__FILE__) . '/template-post-type.php';
require_once dirname(__FILE__) . '/template-add-edit.php';
require_once dirname(__FILE__) . '/template-delete.php';
require_once dirname(__FILE__) . '/class-abstract-post-type-custom-cap.php';

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use \WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type_Custom_Capability;

if (!class_exists('\WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type')) {

    /**
     * Post Type List class
     *
     * @author Vaisagh D <vaisaghd@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Post_Type extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {

        const STATUS_ACTIVE = 1;
        const STATUS_INACTIVE = 0;
        const SOURCE_TYPE_BUILTIN = 0;
        const SOURCE_TYPE_OTHER = 1;
        const SOURCE_TYPE_USER_DEFINED = 2;
        const MENU_SLUG = 'wpfront-user-role-editor-post-types';
        const DATA_EDITED_KEY = 'wpfront-user-role-editor-post-types-data-edited';

        private $entities = null;
        private $post_types_cache = null;
        private $post_types_cache_clear = false;
        private $post_type_args = array();
        private $post_types_unregistered = array();
        private $objView = null;
        public $errorMsg = null;
        protected $AddEditViewClass = null;

        protected function setUp() {
            $this->_setUp('edit_posttypes', self::MENU_SLUG);
            $this->AddEditViewClass = WPFront_User_Role_Editor_Post_Type_Add_Edit_View::class;
        }

        protected function initialize() {
            add_action('init', array($this, 'register_post_types'), PHP_INT_MAX);
            add_filter('register_post_type_args', array($this, 'register_post_type_args'), 1, 2);
            add_action('registered_post_type', array($this, 'registered_post_type'), 1, 2);
            add_action('registered_post_type', array($this, 'deactivate_other_post_types'), PHP_INT_MAX, 2);
            add_action('wp_loaded', array($this, 'flush_rewrite_rules'), 1);

            WPFront_User_Role_Editor_Post_Type_Custom_Capability::initialize($this);

            if (!is_admin()) {
                return;
            }

            $this->set_admin_menu(__('Post Types', 'wpfront-user-role-editor'), __('Post Types', 'wpfront-user-role-editor'), 60);
        }

        /**
         * Register the user defined post type.
         *
         * @return string
         */
        public function register_post_types() {
            $post_types = $this->get_all_post_types_data();
            foreach ($post_types as $data) {
                if ($data->status == self::STATUS_ACTIVE && $data->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                    $args = $data->post_type_arg;
                    $args['taxonomies'] = $data->taxonomies;
                    register_post_type($data->name, $args);
                }
            }

            $this->register_post_type_taxonomies_supports();

            //set state of late registrations
            add_action('registered_post_type', array($this, 'clear_cache'));
        }

        /**
         * Taxonomies and supports needs to be registered within 'init' hook.
         * For built-in types as part of 'arg' is not enough.
         */
        protected function register_post_type_taxonomies_supports() {
            $post_types = $this->get_all_post_types_data();
            foreach ($post_types as $data) {
                if (empty($data->entity)) { //don't do anything if not edited.
                    continue;
                }

                //handle only built-in. other will be handled in 'register_post_type_args'.
                if ($data->status == self::STATUS_ACTIVE && $data->source_type === self::SOURCE_TYPE_BUILTIN) {
                    if (isset($data->taxonomies) && is_array($data->taxonomies)) {
                        $registered = get_object_taxonomies($data->name);
                        $requested = $data->taxonomies;

                        $unregister = array_diff($registered, $requested);
                        $register = array_diff($requested, $registered);

                        foreach ($unregister as $tax) {
                            unregister_taxonomy_for_object_type($tax, $data->name);
                        }

                        foreach ($register as $tax) {
                            register_taxonomy_for_object_type($tax, $data->name);
                        }
                    }

                    if (isset($data->post_type_arg['supports'])) {
                        $registered = array_keys(get_all_post_type_supports($data->name));
                        $requested = is_array($data->post_type_arg['supports']) ? $data->post_type_arg['supports'] : [];

                        $unregister = array_diff($registered, $requested);
                        $register = array_diff($requested, $registered);

                        foreach ($unregister as $feature) {
                            remove_post_type_support($data->name, $feature);
                        }

                        foreach ($register as $feature) {
                            add_post_type_support($data->name, $feature);
                        }
                    }
                }
            }
        }

        /**
         * Store post type registration arguments and merge them with edited arguments.
         * 
         * @param type $args
         * @param type $post_type
         * @return type
         */
        public function register_post_type_args($args, $post_type) {
            $entity_all = $this->get_all_entities();
            $add_custom_caps = true;
            if (isset($entity_all[$post_type])) {
                if (empty($args['_builtin']) && $entity_all[$post_type]->status == self::STATUS_INACTIVE) {
                    $add_custom_caps = false;
                }

                $saved = $entity_all[$post_type]->post_type_arg;
                $saved['taxonomies'] = $entity_all[$post_type]->taxonomies;

                if (isset($saved['capability_type'])) {
                    $saved['capabilities'] = array(); //override, otherwise custom mapping will take over.
                }

                $args = array_merge($args, $saved);
            }

            if ($add_custom_caps) {
                $args = WPFront_User_Role_Editor_Post_Type_Custom_Capability::register_post_type_args($this, $args, $post_type);
            }

            $this->post_type_args[$post_type] = $args;

            return $args;
        }

        /**
         * Retrieve singular name after registration if it doesn't exist. WP doesn't provide it as part of arguments.
         * 
         * @param type $post_type
         * @param type $post_type_object
         * @return type
         */
        public function registered_post_type($post_type, $post_type_object) {
            if (empty($this->post_type_args[$post_type])) {
                return;
            }

            if (!empty($this->post_type_args[$post_type]['labels']['singular_name'])) {
                return;
            }

            $this->post_type_args[$post_type]['labels']['singular_name'] = $post_type_object->labels->singular_name;
        }

        /**
         * Deactivate other post types based on DB state.
         * 
         * @param type $post_type
         * @param type $post_type_object
         * @return type
         */
        public function deactivate_other_post_types($post_type, $post_type_object) {
            if (!empty($post_type_object->_builtin)) {
                return;
            }

            $entity_all = $this->get_all_entities();
            if (isset($entity_all[$post_type]) && $entity_all[$post_type]->status === self::STATUS_INACTIVE) {
                $this->post_types_unregistered[$post_type] = $post_type_object;
                unregister_post_type($post_type);
            }
        }

        public function flush_rewrite_rules() {
            $value = $this->Options->get_option(self::DATA_EDITED_KEY);
            if (!empty($value)) {
                flush_rewrite_rules();
                $this->Options->set_option(self::DATA_EDITED_KEY, false);
            }
        }

        public function load_view() {
            if (!parent::load_view()) {
                return;
            }

            if ((!empty($_POST['action']) && $_POST['action'] !== '-1') || (!empty($_POST['action2']) && $_POST['action2'] !== '-1')) {
                $action = $_POST['action'] === '-1' ? $_POST['action2'] : $_POST['action'];

                $post_types = [];
                if (!empty($_POST['post_types']) && is_array($_POST['post_types'])) {
                    foreach ($_POST['post_types'] as $value) {
                        $data = $this->get_post_type_data($value);
                        if (!empty($data)) {
                            $post_types[] = $data;
                        }
                    }
                }

                switch ($action) {
                    case 'delete':
                    case 'restore':
                        $this->handle_action($action, $post_types);
                        return;

                    case 'activate':
                    case 'deactivate':
                        $this->activate_deactivate_post_type($action, $post_types);
                        return;
                }

                wp_redirect($this->get_list_url());
                exit;
            }

            if (!empty($_GET['screen'])) {
                $screen = $_GET['screen'];

                switch ($screen) {
                    case 'activate':
                    case 'deactivate':
                        $this->activate_deactivate_post_type($screen);
                        return;

                    case 'add-new':
                    case 'edit':
                        $this->add_edit_post_type($screen);
                        return;

                    case 'delete':
                    case 'restore':
                        $this->handle_action($screen);
                        return;

                    default:
                        break;
                }
            }

            $this->objView = new WPFront_User_Role_Editor_Post_Type_List_View($this);
            return;
        }

        private function activate_deactivate_post_type($screen, $datas = null) {
            switch ($screen) {
                case 'activate':
                    $cap = 'edit_posttypes';
                    $check = 'can_activate';
                    $q_arg = 'post-type-activated';
                    break;

                case 'deactivate':
                    $cap = 'edit_posttypes';
                    $check = 'can_deactivate';
                    $q_arg = 'post-type-deactivated';
                    break;

                default:
                    wp_redirect($this->get_list_url());
                    exit;
            }

            if (!current_user_can($cap)) {
                $this->WPFURE->permission_denied();
                exit;
            }

            if ($datas === null) { //url activate/deactivate
                if (empty($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'])) {
                    $this->WPFURE->permission_denied();
                    exit;
                }

                $data = $this->get_post_type_data_from_url();
                if (empty($data)) {
                    return;
                }

                $datas = [$data];
            } else { //bulk post
                check_admin_referer('bulk-post-type');
            }

            $count = 0;
            foreach ($datas as $data) {
                if ($data->$check) {
                    if (empty($data->entity)) { //no db data, either built-in or other post type.
                        if ($screen === 'deactivate') {
                            $entity = new WPFront_User_Role_Editor_Post_Type_Entity();
                            $entity->name = $data->name;
                            $entity->label = $data->label;
                            $entity->status = self::STATUS_INACTIVE;
                            $entity->post_type_arg = $this->post_type_args[$data->name];
                            $entity->taxonomies = isset($entity->post_type_arg['taxonomies']) ? $entity->post_type_arg['taxonomies'] : [];
                            $entity->update();
                            $count++;
                        }
                        continue;
                    }

                    if ($screen === 'activate') {
                        $data->entity->update_status(self::STATUS_ACTIVE);
                    } else {
                        $data->entity->update_status(self::STATUS_INACTIVE);
                    }
                    $count++;
                }
            }

            wp_safe_redirect(add_query_arg($q_arg, $count, $this->get_list_url()));
            exit;
        }

        private function add_edit_post_type($screen) {
            $data = null;
            $clone = null;
            if ($screen == 'edit') {
                if (!current_user_can('edit_posttypes')) {
                    $this->WPFURE->permission_denied();
                    exit;
                }
                $data = $this->get_post_type_data_from_url();
                if (empty($data)) {
                    return;
                }
                if (!$data->can_edit) {
                    $this->WPFURE->permission_denied();
                    exit;
                }
            } elseif ($screen === 'add-new') {
                if (!current_user_can('create_posttypes')) {
                    $this->WPFURE->permission_denied();
                    exit;
                }
                if (!empty($_GET['clone'])) {
                    $clone = $this->get_post_type_data($_GET['clone']);
                }
            }

            $this->objView = new $this->AddEditViewClass(
                    $this,
                    $data,
                    empty($data) ? null : get_post_type_object($data->name),
                    $clone
            );

            if (!empty($_POST['submit']) || !empty($_POST['submit2'])) {
                check_admin_referer('add-edit-post-type');

                $entity = null;
                if ($screen == 'add-new') {
                    if (!current_user_can('create_posttypes')) {
                        $this->WPFURE->permission_denied();
                        exit;
                    }
                    $name = $this->get_submitted_text('name');
                    if (empty($name)) {
                        $this->errorMsg = __('Name must be provided.', 'wpfront-user-role-editor');
                        return;
                    }

                    if (!$this->is_valid_slug($name)) {
                        $this->errorMsg = __('This post type name is not allowed (Use only lowercase letters, numbers, underscores and hyphens).', 'wpfront-user-role-editor');
                        return;
                    }

                    if (strlen($name) > 20) {
                        $this->errorMsg = __('This post type name is too long.', 'wpfront-user-role-editor');
                        return;
                    }

                    if (!empty($this->get_post_type_data($name))) {
                        $this->errorMsg = __('Post type already exists.', 'wpfront-user-role-editor');
                        return;
                    }

                    $reserved = ['action', 'author', 'order', 'theme'];
                    if (in_array($name, $reserved)) {
                        $this->errorMsg = __('This post type name is reserved and can not be added.', 'wpfront-user-role-editor');
                        return;
                    }

                    $entity = new WPFront_User_Role_Editor_Post_Type_Entity();
                    $entity->name = $name;
                    $entity->status = self::STATUS_ACTIVE;
                } else {
                    if (!empty($data->entity)) {
                        $entity = $data->entity;
                    } else {
                        $entity = new WPFront_User_Role_Editor_Post_Type_Entity();
                        $entity->name = $data->name;
                        $entity->status = self::STATUS_ACTIVE;
                    }
                }

                $labels = $this->get_submitted_text('label');
                if (empty($labels)) {
                    $this->errorMsg = __('Plural label must be provided.', 'wpfront-user-role-editor');
                    return;
                }

                $entity->label = $labels; //WordPress stores plural on label.

                $label = $this->get_submitted_text('singular_name');
                if (empty($label)) {
                    $this->errorMsg = __('Singular label must be provided.', 'wpfront-user-role-editor');
                    return;
                }

                $entity->taxonomies = $this->get_submitted_array('taxonomies');

                if (!empty($data) && $data->source_type === self::SOURCE_TYPE_BUILTIN) {
                    $entity->status = self::STATUS_ACTIVE;
                } else {
                    $value = $this->get_submitted_boolean('status');
                    $entity->status = $value ? self::STATUS_ACTIVE : self::STATUS_INACTIVE;
                }

                $post_type_args = array();
                $post_type_args = $this->get_advanced_settings_arg();
                $post_type_args['labels'] = $this->get_labels_arg();

                if (!empty($this->errorMsg)) {
                    return;
                }

                $post_type_args = $this->sanitize_add_edit_post_type_args($screen, $post_type_args, $entity);

                if (!empty($this->errorMsg)) {
                    return;
                }

                $entity->post_type_arg = $post_type_args;

                if ($screen == 'add-new') {
                    $result = $entity->add();
                    $url_arg = 'post-type-added';
                } else {
                    $result = $entity->update();
                    $url_arg = 'post-type-updated';
                }

                if ($result === false) {
                    $this->errorMsg = __('Unexpected error occured.', 'wpfront-user-role-editor');
                    return;
                }

                if (!current_user_can('edit_posttypes')) {
                    $args = apply_filters('wpfront_ure_post_type_add_edit_success_url_args', array('post-type-added' => 'true'), $entity);
                    wp_safe_redirect(add_query_arg($args, $this->get_list_url()));
                    exit;
                } else {
                    $args = apply_filters('wpfront_ure_post_type_add_edit_success_url_args', array($url_arg => 'true'), $entity);
                    wp_safe_redirect(add_query_arg($args, $this->get_edit_url($entity->name)));
                    exit;
                }
            }
        }

        protected function sanitize_add_edit_post_type_args($screen, $post_type_args, $entity) {
            return $post_type_args;
        }

        private function handle_action($action, $datas = null) {
            switch ($action) {
                case 'delete':
                    $cap = 'delete_posttypes';
                    $check = 'can_delete';
                    $q_arg = 'post-types-deleted';
                    break;

                case 'restore':
                    $cap = 'edit_posttypes';
                    $check = 'can_restore';
                    $q_arg = 'post-types-restored';
                    break;

                default:
                    wp_redirect($this->get_list_url());
                    exit;
            }

            if (!current_user_can($cap)) {
                $this->WPFURE->permission_denied();
                exit;
            }

            $entities = [];
            if (empty($datas)) {
                $data = $this->get_post_type_data_from_url();
                if (!empty($data->$check)) {
                    $entities = [$data->entity];
                }
            } else {
                foreach ($datas as $data) {
                    if ($data->$check) {
                        $entities[] = $data->entity;
                    }
                }
            }

            if (empty($entities)) {
                wp_redirect($this->get_list_url());
                exit;
            }

            if (!empty($_POST['submit'])) {
                check_admin_referer('bulk-action-view-post');
                foreach ($entities as $entity) {
                    $entity->delete($action);
                }
                wp_safe_redirect(add_query_arg($q_arg, 'true', $this->get_list_url()));
                exit;
            }

            $this->objView = new WPFront_User_Role_Editor_Post_Type_Delete_View($this, $entities, $action);
        }

        private function get_advanced_settings_arg() {
            $args = array();

            $props = [
                'rest_base',
                'rest_controller_class',
                'menu_position',
                'menu_icon'
            ];

            foreach ($props as $prop) {
                $value = $this->get_submitted_text($prop);
                if ($value !== null) {
                    $args[$prop] = $value;
                }
            }

            if (isset($args['menu_position'])) {
                $args['menu_position'] = (int) $args['menu_position'];
            }

            $props = [
                'supports'
            ];

            foreach ($props as $prop) {
                $value = $this->get_submitted_array($prop);
                if ($value !== null) {
                    $args[$prop] = $value;
                }
            }

            if (empty($args['supports'])) {
                $args['supports'] = false;
            }

            $custom_supports = $this->get_submitted_text_array('custom_supports');
            if (!empty($custom_supports)) {
                $supports = empty($args['supports']) ? [] : $args['supports'];
                $args['supports'] = array_merge($supports, $custom_supports);
            }

            $props = [
                'public',
                'hierarchical',
                'exclude_from_search',
                'publicly_queryable',
                'show_ui',
                'show_in_menu',
                'show_in_nav_menus',
                'show_in_admin_bar',
                'show_in_rest',
                'has_archive',
                'query_var',
                'can_export',
                'delete_with_user',
                'rewrite'
            ];

            foreach ($props as $prop) {
                $value = $this->get_submitted_boolean($prop);
                if ($value !== null) {
                    $args[$prop] = $value;
                }
            }

            if (!empty($args['show_in_menu'])) {
                $slug = $this->get_submitted_text('show_in_menu_slug');
                if ($slug !== null) {
                    $args['show_in_menu'] = $slug;
                }
            }

            if (!empty($args['has_archive'])) {
                $slug = $this->get_submitted_text('has_archive_slug');
                if ($slug !== null) {
                    if (!$this->is_valid_rewrite_slug($slug)) {
                        $this->errorMsg = __('This archive name is not allowed (Use only lowercase letters, numbers, underscores, hyphens and slashes).', 'wpfront-user-role-editor');
                        return;
                    } else {
                        $args['has_archive'] = $slug;
                    }
                }
            }

            if (!empty($args['query_var'])) {
                $slug = $this->get_submitted_text('query_var_slug');
                if ($slug !== null) {
                    $args['query_var'] = $slug;
                }
            }

            if (!empty($args['rewrite'])) {
                $rewrite_array = [];

                $slug = $this->get_submitted_text('rewrite_slug');
                if (!empty($slug)) {
                    if (!$this->is_valid_rewrite_slug($slug)) {
                        $this->errorMsg = __('This rewrite slug is not allowed (Use only lowercase letters, numbers, underscores, hyphens and slashes).', 'wpfront-user-role-editor');
                        return;
                    } else {
                        $rewrite_array['slug'] = $slug;
                    }
                }

                $with_front = $this->get_submitted_boolean('rewrite_with_front');
                if (is_bool($with_front)) {
                    $rewrite_array['with_front'] = $with_front;
                }

                $ep_mask = $this->get_submitted_text('rewrite_ep_mask');
                if ($ep_mask !== null) {
                    if (!$this->is_valid_ep_mask($ep_mask)) {
                        $this->errorMsg = __('This Rewrite EP Mask is not allowed (Use only numbers).', 'wpfront-user-role-editor');
                        return;
                    }
                    $rewrite_array['ep_mask'] = $ep_mask;
                }

                $feeds = $this->get_submitted_boolean('rewrite_feeds');
                if (is_bool($feeds)) {
                    $rewrite_array['feeds'] = $feeds;
                }

                $pages = $this->get_submitted_boolean('rewrite_pages');
                if (is_bool($pages)) {
                    $rewrite_array['pages'] = $pages;
                }

                if (empty($rewrite_array)) {
                    $args['rewrite'] = true;
                } else {
                    $args['rewrite'] = $rewrite_array;
                }
            }

            return $args;
        }

        private function get_labels_arg() {
            $args = array();
            $args['name'] = $this->get_submitted_text('label');

            $props = [
                'singular_name',
                'add_new',
                'add_new_item',
                'edit_item',
                'new_item',
                'view_item',
                'view_items',
                'search_items',
                'not_found',
                'not_found_in_trash',
                'parent_item_colon',
                'all_items',
                'archives',
                'attributes',
                'insert_into_item',
                'uploaded_to_this_item',
                'featured_image',
                'set_featured_image',
                'remove_featured_image',
                'use_featured_image',
                'menu_name',
                'filter_items_list',
                'items_list_navigation',
                'items_list',
                'item_published',
                'item_published_privately',
                'item_reverted_to_draft',
                'item_scheduled',
                'item_updated',
                'description'
            ];

            foreach ($props as $prop) {
                $value = $this->get_submitted_text($prop);
                if ($value !== null) {
                    $args[$prop] = $value;
                }
            }


            return $args;
        }

        private function get_post_type_data_from_url() {
            if (empty($_GET['name'])) {
                wp_safe_redirect($this->get_self_url());
                exit;
            }

            $post_type = $this->get_post_type_data($_GET['name']);
            if (empty($post_type) && empty($_GET['post-type-deleted'])) {
                $this->errorMsg = __('Post type do not exists.', 'wpfront-user-role-editor');
                $this->objView = new WPFront_User_Role_Editor_Post_Type_List_View($this);
                return null;
            }

            return $post_type;
        }

        protected function get_submitted_text($name) {
            if (empty($_POST[$name])) {
                return null;
            }

            $txt = trim($_POST[$name]);

            if (empty($txt)) {
                return null;
            }

            return $txt;
        }

        private function get_submitted_array($name) {
            if (!empty($_POST[$name]) && is_array($_POST[$name])) {
                return $_POST[$name];
            }

            return null;
        }

        protected function get_submitted_text_array($name) {
            if (!empty($_POST[$name])) {
                $txt = $_POST[$name];
                $values = explode(',', $txt);
                $result = [];
                foreach ($values as $value) {
                    $result[] = trim($value);
                }

                return $result;
            }

            return null;
        }

        protected function get_submitted_boolean($name) {
            if (isset($_POST[$name]) && $_POST[$name] == '') {
                return null;
            }

            return !empty($_POST[$name]);
        }

        /**
         * Displays the post type view.
         */
        public function view() {
            if (!parent::view()) {
                return;
            }

            if (empty($this->objView)) {
                $this->objView = new WPFront_User_Role_Editor_Post_Type_List_View($this);
            }

            $this->objView->view();
        }

        public function get_active_list_filter() {
            if (empty($_GET['list']))
                return 'all';

            $list = $_GET['list'];

            switch ($list) {
                case 'all':
                case 'builtin':
                case 'other':
                case 'userdefined':
                case 'active':
                case 'inactive':
                    break;
                default:
                    $list = 'all';
                    break;
            }

            return $list;
        }

        public function get_list_filter_data() {
            $filter_data = array();
            $built_in = [];
            $other = [];
            $user_defined = [];
            $active = [];
            $inactive = [];
            $post_types = $this->get_all_post_types_data();
            $page = $this->get_self_url();

            $filter_data['all'] = array(
                'display' => __('All', 'wpfront-user-role-editor'),
                'url' => $page . '&list=all',
                'count' => count($post_types)
            );

            foreach ($post_types as $entity) {
                if ($entity->source_type === self::SOURCE_TYPE_BUILTIN) {
                    $built_in[] = $entity;
                }
                if ($entity->source_type === self::SOURCE_TYPE_OTHER) {
                    $other[] = $entity;
                }

                if ($entity->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                    $user_defined[] = $entity;
                }

                if ($entity->status === self::STATUS_ACTIVE) {
                    $active[] = $entity;
                }

                if ($entity->status === self::STATUS_INACTIVE) {
                    $inactive[] = $entity;
                }
            }

            $filter_data['builtin'] = array(
                'display' => __('Built-In', 'wpfront-user-role-editor'),
                'url' => $page . '&list=builtin',
                'count' => count($built_in)
            );

            $filter_data['other'] = array(
                'display' => __('Other', 'wpfront-user-role-editor'),
                'url' => $page . '&list=other',
                'count' => count($other)
            );

            $filter_data['userdefined'] = array(
                'display' => __('User Defined', 'wpfront-user-role-editor'),
                'url' => $page . '&list=userdefined',
                'count' => count($user_defined)
            );

            $filter_data['active'] = array(
                'display' => __('Active', 'wpfront-user-role-editor'),
                'url' => $page . '&list=active',
                'count' => count($active)
            );

            $filter_data['inactive'] = array(
                'display' => __('Inactive', 'wpfront-user-role-editor'),
                'url' => $page . '&list=inactive',
                'count' => count($inactive)
            );

            return $filter_data;
        }

        public function apply_active_list_filter($post_types = null) {
            if ($post_types === null) {
                $post_types = $this->get_all_post_types_data();
            }

            switch ($this->get_active_list_filter()) {
                case 'all':
                    break;
                case 'builtin':
                    foreach ($post_types as $key => $entity) {
                        if ($entity->source_type === self::SOURCE_TYPE_OTHER || $entity->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                            unset($post_types[$key]);
                        }
                    }

                    break;

                case 'other':
                    foreach ($post_types as $key => $entity) {
                        if ($entity->source_type === self::SOURCE_TYPE_BUILTIN || $entity->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                            unset($post_types[$key]);
                        }
                    }
                    break;
                case 'userdefined':
                    foreach ($post_types as $key => $entity) {
                        if ($entity->source_type === self::SOURCE_TYPE_OTHER || $entity->source_type === self::SOURCE_TYPE_BUILTIN) {
                            unset($post_types[$key]);
                        }
                    }
                    break;
                case 'active':
                    foreach ($post_types as $key => $entity) {
                        if ($entity->status === self::STATUS_INACTIVE) {
                            unset($post_types[$key]);
                        }
                    }
                    break;
                case 'inactive':
                    foreach ($post_types as $key => $entity) {
                        if ($entity->status === self::STATUS_ACTIVE) {
                            unset($post_types[$key]);
                        }
                    }
                    break;
            }

            return $post_types;
        }

        protected function get_all_entities() {
            if ($this->entities !== null) {
                return $this->entities;
            }

            $entity = new WPFront_User_Role_Editor_Post_Type_Entity();
            $entities = $entity->get_all();

            foreach ($entities as $post_type => $entity) {
                if (!is_array($entity->post_type_arg)) { //customized from role add/edit screen.
                    $entity->post_type_arg = array();
                    $entity->post_type_arg['map_meta_cap'] = true;
                }

                if (!empty($entity->capability_type)) {
                    $entity->post_type_arg['capability_type'] = $entity->capability_type;
                }
            }

            $this->entities = $this->sanitize_pro_fields($entities);

            return $this->entities;
        }

        protected function sanitize_pro_fields($entities) {
            foreach ($entities as $post_type => $entity) {
                if (isset($entity->post_type_arg['capability_type'])) {
                    unset($entity->post_type_arg['capability_type']);
                }

                if (isset($entity->post_type_arg['map_meta_cap'])) {
                    unset($entity->post_type_arg['map_meta_cap']);
                }

                $entity->capability_type = null;
            }

            return $entities;
        }

        public function clear_cache() {
            $this->post_types_cache_clear = true;
        }

        /**
         * Returns list of post types.
         *
         * @return object[] Associative(post_type => object)
         */
        public function get_all_post_types_data() {
            if (!$this->post_types_cache_clear && !empty($this->post_types_cache)) {
                return $this->post_types_cache;
            }

            $has_edit_cap = current_user_can('edit_posttypes');
            $has_delete_cap = current_user_can('delete_posttypes');
            $has_clone_cap = current_user_can('create_posttypes');

            $post_types = get_post_types([], 'objects');
            $exiting = array();
            foreach ($post_types as $name => $post_type_obj) {
                if ($post_type_obj->_builtin && !is_post_type_viewable($name)) {
                    continue;
                }

                $data = new \stdClass();
                $data->name = $post_type_obj->name;
                $data->label = $post_type_obj->label;
                $data->status = self::STATUS_ACTIVE;
                $data->source_type = $post_type_obj->_builtin ? self::SOURCE_TYPE_BUILTIN : self::SOURCE_TYPE_OTHER;
                $taxes = get_object_taxonomies($post_type_obj->name);
                $data->taxonomies = is_array($taxes) ? $taxes : [];
                
                if (isset($this->post_type_args[$name])) {
                    $data->post_type_arg = $this->post_type_args[$name];
                } else {
                    $data->post_type_arg = array();
                }
                
                $data->entity = null;

                $exiting[$name] = $data;
            }

            $entity_all = $this->get_all_entities();
            $user_edited = [];
            foreach ($entity_all as $name => $entity) {
                $data = new \stdClass();
                $data->name = $entity->name;
                $data->label = $entity->label;
                $data->status = $entity->status;
                if (!isset($exiting[$name])) {
                    if (isset($this->post_types_unregistered[$entity->name])) {
                        $data->source_type = self::SOURCE_TYPE_OTHER;
                    } else {
                        $data->source_type = self::SOURCE_TYPE_USER_DEFINED;
                    }
                } else {
                    $data->source_type = $exiting[$name]->source_type;
                }

                if (isset($this->post_type_args[$name])) {  //only cap type and map_meta_cap may exist in post_type_arg on customization from role add/edit screen.
                    $entity->post_type_arg = array_merge($this->post_type_args[$name], $entity->post_type_arg);
                }

                $data->post_type_arg = $entity->post_type_arg;

                $data->taxonomies = $entity->taxonomies;
                $data->entity = $entity;

                $user_edited[$name] = $data;
            }

            //reset source types to intial state to take care of registration changing source type.
            $post_types_merged = array_merge($exiting, $user_edited);
            foreach ($post_types_merged as $name => $data) {
                if (isset($this->post_types_cache[$name])) {
                    $data->source_type = $this->post_types_cache[$name]->source_type;
                }
            }

            $this->post_types_cache = $post_types_merged;

            foreach ($this->post_types_cache as $name => $data) {
                if ($data->source_type === self::SOURCE_TYPE_BUILTIN) {
                    $data->can_edit = $has_edit_cap && is_post_type_viewable($name);
                } else {
                    $data->can_edit = $has_edit_cap;
                }
                $data->can_delete = $has_delete_cap && $data->source_type === self::SOURCE_TYPE_USER_DEFINED;
                if ($data->source_type === self::SOURCE_TYPE_BUILTIN || $data->source_type === self::SOURCE_TYPE_OTHER) {
                    $data->can_restore = $has_edit_cap && !empty($data->entity);
                } else {
                    $data->can_restore = false;
                }
                if ($data->can_edit && ($data->source_type === self::SOURCE_TYPE_USER_DEFINED || $data->source_type === self::SOURCE_TYPE_OTHER)) {
                    if ($data->status == self::STATUS_ACTIVE) {
                        $data->can_activate = false;
                        $data->can_deactivate = true;
                    } else if ($data->status == self::STATUS_INACTIVE) {
                        $data->can_activate = true;
                        $data->can_deactivate = false;
                    }
                } else {
                    $data->can_activate = false;
                    $data->can_deactivate = false;
                }
                $data->can_clone = $has_clone_cap;
            }

            return $this->post_types_cache;
        }

        /**
         * Returns the post type.
         *
         * @return string
         */
        public function get_post_type_data($post_type) {
            $lists = $this->get_all_post_types_data();
            if (!empty($lists[$post_type])) {
                return $lists[$post_type];
            }

            return null;
        }

        public function search($search) {
            $post_types = $this->get_all_post_types_data();
            $post_types = $this->sort_post_types_data($post_types);

            if (empty($search)) {
                return $post_types;
            }

            foreach ($post_types as $name => $item) {
                if (strpos($item->name, $search) !== false) {
                    continue;
                }

                if (strpos($item->label, $search) !== false) {
                    continue;
                }

                unset($post_types[$name]);
            }

            return $post_types;
        }

        protected function sort_post_types_data($post_types) {
            $built_in_post_types = array();
            $other_post_types = array();
            $user_defined_post_types = array();

            foreach ($post_types as $post_type => $data) {
                if ($data->source_type === self::SOURCE_TYPE_BUILTIN) {
                    $built_in_post_types[$post_type] = $data;
                    continue;
                }

                if ($data->source_type === self::SOURCE_TYPE_OTHER) {
                    $other_post_types[$post_type] = $data;
                    continue;
                }

                if ($data->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                    $user_defined_post_types[$post_type] = $data;
                    continue;
                }
            }

            ksort($built_in_post_types);
            ksort($other_post_types);
            ksort($user_defined_post_types);
            return array_merge($built_in_post_types, $other_post_types, $user_defined_post_types);
        }

        protected function is_valid_slug($slug) {
            if (empty($slug)) {
                return false;
            }

            return sanitize_key($slug) === $slug;
        }

        protected function is_valid_rewrite_slug($slug) {
            $key = strtolower($slug);
            $key = preg_replace('/[^a-z0-9_\-\/]/', '', $key);

            return $key === $slug;
        }

        private function is_valid_ep_mask($ep_mask) {
            return !preg_match("/[^0-9]/", $ep_mask);
        }

        /**
         * Return post type list url.
         *
         * @return string
         */
        public function get_list_url($key = '') {
            if (empty($key)) {
                return $this->get_self_url();
            }

            return add_query_arg($key, 'true', $this->get_self_url());
        }

        /**
         * Returns the add new post type URL.
         *
         * @return string
         */
        public function get_add_new_url($clone = null) {
            $p = ['screen' => 'add-new'];
            if (!empty($clone)) {
                $p['clone'] = $clone;
            }
            return $this->get_self_url($p);
        }

        /**
         * Returns the edit post type URL.
         *
         * @return string
         */
        public function get_edit_url($name) {
            return $this->get_self_url(['screen' => 'edit', 'name' => $name]);
        }

        /**
         * Returns the delete post type URL.
         *
         * @return string
         */
        public function get_delete_url($name) {
            if (empty($name)) {
                return $this->get_self_url(['screen' => 'delete']);
            }

            return $this->get_self_url(['screen' => 'delete', 'name' => $name]);
        }

        /**
         * Returns the activate post type URL.
         *
         * @return string
         */
        public function get_activate_url($name) {
            return wp_nonce_url($this->get_self_url(['screen' => 'activate', 'name' => $name]));
        }

        /**
         * Returns the deactivate post type URL.
         *
         * @return string
         */
        public function get_deactivate_url($name) {
            return wp_nonce_url($this->get_self_url(['screen' => 'deactivate', 'name' => $name]));
        }

        /**
         * Returns the clone post type URL.
         *
         * @return string
         */
        public function get_clone_url($name) {
            return $this->get_add_new_url($name);
        }

        public function get_restore_url($name) {
            if (empty($name)) {
                return $this->get_self_url(['screen' => 'restore']);
            }

            return $this->get_self_url(['screen' => 'restore', 'name' => $name]);
        }

        public function get_user_visible_cpt() {
            $post_types = get_post_types(array(
                '_builtin' => false
            ));

            $cpts = array();

            foreach ($post_types as $name => $value) {
                $post_type_object = get_post_type_object($name);

                if (!$this->is_cpt_user_visible($post_type_object)) {
                    continue;
                }

                $cpts[$name] = $post_type_object;
            }

            return $cpts;
        }

        protected function is_cpt_user_visible($post_type_object) {
            if ($post_type_object->_builtin) {
                return false;
            }

            return is_post_type_viewable($post_type_object) || $post_type_object->show_ui || $post_type_object->capability_type !== 'post';
        }

        public function get_cpt_customizable_hint_text($group_obj, $disabled) {
            $post_type_obj = get_post_type_object($group_obj->key);
            $post_type = $post_type_obj->capability_type;
            $post_type_obj = get_post_type_object($post_type);

            $hint = __('Uses "%s" capability.', 'wpfront-user-role-editor');
            $hint = sprintf($hint, $post_type_obj->label);

            $upgrade_message = sprintf(__('%s to customize capabilities.', 'wpfront-user-role-editor'), '<a href="https://wpfront.com/ureaddedit" target="_blank">' . __('Upgrade to Pro', 'wpfront-user-role-editor') . '</a>');
            $hint .= ' ' . $upgrade_message;

            return $hint;
        }

        public function get_customizied_custom_post_types_from_settings() {
            return array();
        }

        public function admin_print_scripts() {
            parent::admin_print_scripts();
            wp_enqueue_script('jquery-ui-tooltip', null, array('jquery'));
            wp_enqueue_script('postbox');
            wp_enqueue_script('wpfront-user-role-editor-post-types', WPFURE::instance()->get_asset_url('js/chosen/chosen.jquery.min.js'), array('jquery'), WPFURE::VERSION);
        }

        public function admin_print_styles() {
            parent::admin_print_styles();
            wp_enqueue_style('wpfront-user-role-editor-post-types', WPFURE::instance()->get_asset_url('css/chosen/chosen.min.css'), array(), WPFURE::VERSION);
        }
        
        public static function get_debug_setting() {
            return array('key' => 'post-type', 'label' => __('Post Types', 'wpfront-user-role-editor'), 'position' => 90, 'description' => __('Disables all Post Type functionalities including custom capabilities.', 'wpfront-user-role-editor'));
        }

    }

    WPFront_User_Role_Editor_Post_Type::load();
}