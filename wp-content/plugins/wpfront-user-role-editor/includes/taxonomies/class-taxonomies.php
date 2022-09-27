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
 * Controller for WPFront User Role Editor Taxonomies
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Taxonomies;

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;

if (!defined('ABSPATH')) {
    exit();
}

require_once dirname(__FILE__) . '/entity-taxonomies.php';
require_once dirname(__FILE__) . '/template-taxonomies.php';
require_once dirname(__FILE__) . '/template-add-edit.php';
require_once dirname(__FILE__) . '/template-delete.php';

if (!class_exists('\WPFront\URE\Taxonomies\WPFront_User_Role_Editor_Taxonomies')) {

    /**
     * Taxonomies List class
     *
     * @author Vaisagh D <vaisaghd@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Taxonomies extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {

        const STATUS_ACTIVE = 1;
        const STATUS_INACTIVE = 0;
        const SOURCE_TYPE_BUILTIN = 0;
        const SOURCE_TYPE_OTHER = 1;
        const SOURCE_TYPE_USER_DEFINED = 2;
        const MENU_SLUG = 'wpfront-user-role-editor-taxonomies';
        const DATA_EDITED_KEY = \WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type::DATA_EDITED_KEY; //let Post Type handle rewrite rules flush.

        private $entities = null;
        private $taxonomies_cache = null;
        private $taxonomies_cache_clear = false;
        private $taxonomy_args = array();
        private $objView = null;
        private $taxonomies_unregistered = array();
        public $errorMsg = null;
        protected $AddEditViewClass = null;

        protected function setUp() {
            $this->_setUp('edit_taxonomies', self::MENU_SLUG);
            $this->AddEditViewClass = WPFront_User_Role_Editor_Taxonomies_Add_Edit_View::class;
        }

        protected function initialize() {
            add_action('init', array($this, 'register_taxonomy'), PHP_INT_MAX - 1);
            add_filter('register_taxonomy_args', array($this, 'register_taxonomy_args'), 1, 3);
            add_action('registered_taxonomy', array($this, 'deactivate_other_taxonomies'), PHP_INT_MAX, 3);
            add_action('registered_taxonomy', array($this, 'registered_taxonomy'), 1, 3);
            add_action('registered_taxonomy', array($this, 'attach_post_types_on_taxonomy_registration'), 2, 1);
            add_action('registered_post_type', array($this, 'attach_post_types_on_post_type_registration'), 2, 1);

            if (!is_admin()) {
                return;
            }

            $this->set_admin_menu(__('Taxonomies', 'wpfront-user-role-editor'), __('Taxonomies', 'wpfront-user-role-editor'), 70);
        }

        /**
         * Register the user defined taxonomy.
         *
         * @return string
         */
        public function register_taxonomy() {
            $taxonomies = $this->get_all_taxonomies_data(); //calling data before registration allows to caputre correct source type.
            foreach ($taxonomies as $data) {
                if ($data->status == self::STATUS_ACTIVE && $data->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                    $args = $data->taxonomy_arg;
                    register_taxonomy($data->name, $data->post_types, $args);
                }
            }

            //set state of late registrations
            add_action('registered_taxonomy', array($this, 'clear_cache'));

            //handle post registrations of post types
            add_action('registered_taxonomy_for_object_type', array($this, 'registered_taxonomy_for_object_type'), PHP_INT_MAX, 2);
            add_action('unregistered_taxonomy_for_object_type', array($this, 'unregistered_taxonomy_for_object_type'), PHP_INT_MAX, 2);
        }

        /**
         * Store taxonomy registration arguments and merge them with edited arguments.
         * 
         * @param type $args
         * @param type $taxonomy
         * @param type $object_type
         * @return type
         */
        public function register_taxonomy_args($args, $taxonomy, $object_type) {
            $entity_all = $this->get_all_entities();
            if (isset($entity_all[$taxonomy])) {
                $entity = $entity_all[$taxonomy];
                $saved = $entity->taxonomy_arg;
                if (empty($saved)) {
                    $saved = array();
                }

                if (!empty($entity->capability_type)) { //TODO: move location
                    $type = $entity->capability_type;
                    $caps = $this->get_custom_caps($type);
                    $saved['capabilities'] = $caps;
                }

                $args = array_merge($args, $saved);
            }

            $this->taxonomy_args[$taxonomy] = $args;

            return $args;
        }

        public function attach_post_types_on_taxonomy_registration($taxonomy) {
            $entity_all = $this->get_all_entities();
            if (isset($entity_all[$taxonomy])) {
                $post_types = $entity_all[$taxonomy]->post_types;
                foreach ($post_types as $post_type) {
                    register_taxonomy_for_object_type($taxonomy, $post_type);
                }
            }
        }

        /**
         * To handle late post type registrations.
         * 
         * @param string $post_type
         */
        public function attach_post_types_on_post_type_registration($post_type) {
            $entity_all = $this->get_all_entities();
            foreach ($entity_all as $taxonomy => $entity) {
                $post_types = $entity->post_types;
                if (in_array($post_type, $post_types)) {
                    register_taxonomy_for_object_type($taxonomy, $post_type);
                }
            }
        }

        /**
         * Retrieve singular name after registration if it doesn't exist. WP doesn't provide it as part of arguments.
         * 
         * @param type $taxonomy
         * @param type $object_type
         * @param type $args
         * @return type
         */
        public function registered_taxonomy($taxonomy, $object_type, $args) {
            if (!isset($this->taxonomy_args[$taxonomy])) {
                return;
            }
            
            if(isset($this->taxonomy_args[$taxonomy]['labels'])) {
                $this->taxonomy_args[$taxonomy]['labels'] = (array) $this->taxonomy_args[$taxonomy]['labels']; //fix if the param supplied is not array
            } else {
                $this->taxonomy_args[$taxonomy]['labels'] = array();
            }

            if (!empty($this->taxonomy_args[$taxonomy]['labels']['singular_name'])) {
                return;
            }

            $this->taxonomy_args[$taxonomy]['labels']['singular_name'] = $args['labels']->singular_name;
        }

        /**
         * Deactivate other taxonomy based on DB state.
         * 
         * @param type $taxonomy
         * @param type $object_type
         * @param type $args
         * @return type
         */
        public function deactivate_other_taxonomies($taxonomy, $object_type, $args) {
            if (!empty($args['_builtin'])) {
                return;
            }

            $entity_all = $this->get_all_entities();
            if (isset($entity_all[$taxonomy]) && $entity_all[$taxonomy]->status === self::STATUS_INACTIVE) {
                $taxonomy_object = get_taxonomy($taxonomy);
                $this->taxonomies_unregistered[$taxonomy] = $taxonomy_object;
                unregister_taxonomy($taxonomy);
            }
        }

        public function load_view() {
            if (!parent::load_view()) {
                return;
            }

            if ((!empty($_POST['action']) && $_POST['action'] !== '-1') || (!empty($_POST['action2']) && $_POST['action2'] !== '-1')) {
                $action = $_POST['action'] === '-1' ? $_POST['action2'] : $_POST['action'];

                $taxonomies = [];
                if (!empty($_POST['taxonomies']) && is_array($_POST['taxonomies'])) {
                    foreach ($_POST['taxonomies'] as $value) {
                        $data = $this->get_taxonomy_data($value);
                        if (!empty($data)) {
                            $taxonomies[] = $data;
                        }
                    }
                }

                switch ($action) {
                    case 'delete':
                    case 'restore':
                        $this->handle_action($action, $taxonomies);
                        return;

                    case 'activate':
                    case 'deactivate':
                        $this->activate_deactivate_taxonomies($action, $taxonomies);
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
                        $this->activate_deactivate_taxonomies($screen);
                        return;

                    case 'add-new':
                    case 'edit':
                        $this->add_edit_taxonomies($screen);
                        return;
                    case 'delete':
                    case 'restore':
                        $this->handle_action($screen);
                        return;

                    default:
                        break;
                }
            }

            $this->objView = new WPFront_User_Role_Editor_Taxonomies_List_View($this);
            return;
        }

        private function activate_deactivate_taxonomies($screen, $datas = null) {
            switch ($screen) {
                case 'activate':
                    $cap = 'edit_taxonomies';
                    $check = 'can_activate';
                    $q_arg = 'taxonomy-activated';
                    break;

                case 'deactivate':
                    $cap = 'edit_taxonomies';
                    $check = 'can_deactivate';
                    $q_arg = 'taxonomy-deactivated';
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

                $data = $this->get_taxonomy_data_from_url();
                if (empty($data)) {
                    return;
                }

                $datas = [$data];
            } else { //bulk post
                check_admin_referer('bulk-taxonomies');
            }

            $count = 0;
            foreach ($datas as $data) {
                if ($data->$check) {
                    if (empty($data->entity)) { //no db data, either built-in or other taxonomies.
                        if ($screen === 'deactivate') {
                            $entity = new WPFront_User_Role_Editor_Taxonomies_Entity();
                            $entity->name = $data->name;
                            $entity->label = $data->label;
                            $entity->status = self::STATUS_INACTIVE;
                            $entity->taxonomy_arg = $this->taxonomy_args[$data->name];
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

        protected function add_edit_taxonomies($screen) {
            $data = null;
            $clone = null;
            if ($screen == 'edit') {
                if (!current_user_can('edit_taxonomies')) {
                    $this->WPFURE->permission_denied();
                    exit;
                }
                $data = $this->get_taxonomy_data_from_url();
                if (empty($data)) {
                    return;
                }
                if (!$data->can_edit) {
                    $this->WPFURE->permission_denied();
                    exit;
                }
            } elseif ($screen === 'add-new') {
                if (!current_user_can('create_taxonomies')) {
                    $this->WPFURE->permission_denied();
                    exit;
                }
                if (!empty($_GET['clone'])) {
                    $clone = $this->get_taxonomy_data($_GET['clone']);
                }
            }
            $this->objView = new $this->AddEditViewClass(
                    $this,
                    $data,
                    empty($data) ? null : get_taxonomy($data->name),
                    $clone
            );
            if (!empty($_POST['submit']) || !empty($_POST['submit2'])) {
                check_admin_referer('add-edit-taxonomies');

                $entity = null;
                if ($screen == 'add-new') {
                    if (!current_user_can('create_taxonomies')) {
                        $this->WPFURE->permission_denied();
                        exit;
                    }
                    $name = $this->get_submitted_text('name');
                    if (empty($name)) {
                        $this->errorMsg = __('Name must be provided.', 'wpfront-user-role-editor');
                        return;
                    }

                    if (!$this->is_valid_slug($name)) {
                        $this->errorMsg = __('This taxonomy name is not allowed (Use only lowercase letters, numbers, underscores and hyphens).', 'wpfront-user-role-editor');
                        return;
                    }

                    if (!empty($this->get_taxonomy_data($name))) {
                        $this->errorMsg = __('Taxonomy already exists.', 'wpfront-user-role-editor');
                        return;
                    }

                    $reserved = ['attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and', 'category__in', 'category__not_in', 'category_name',
                        'comments_per_page', 'comments_popup', 'custom', 'customize_messenger_channel', 'customized', 'cpage', 'day', 'debug', 'embed', 'error', 'exact',
                        'feed', 'fields', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name', 'nav_menu', 'nonce', 'nopaging',
                        'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm', 'post', 'post__in', 'post__not_in', 'post_format',
                        'post_mime_type', 'post_status', 'post_tag', 'post_type', 'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots',
                        's', 'search', 'second', 'sentence', 'showposts', 'static', 'status', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in',
                        'tag_id', 'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'terms', 'theme', 'title', 'type', 'types', 'w', 'withcomments', 'withoutcomments',
                        'year'
                    ];
                    if (in_array($name, $reserved)) {
                        $this->errorMsg = __('This taxonomy name is reserved and can not be added.', 'wpfront-user-role-editor');
                        return;
                    }

                    $entity = new WPFront_User_Role_Editor_Taxonomies_Entity();
                    $entity->name = $name;
                    $entity->status = self::STATUS_ACTIVE;
                } else {
                    if (!empty($data->entity)) {
                        $entity = $data->entity;
                    } else {
                        $entity = new WPFront_User_Role_Editor_Taxonomies_Entity();
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

                $entity->post_types = $this->get_submitted_array('post_types');

                $taxonomy_args = array();
                $taxonomy_args = $this->get_advanced_settings_arg();
                $taxonomy_args['labels'] = $this->get_labels_arg();

                if (!empty($this->errorMsg)) {
                    return;
                }

                $taxonomy_args = $this->sanitize_add_edit_taxonomy_args($screen, $taxonomy_args, $entity);

                if (!empty($this->errorMsg)) {
                    return;
                }

                $entity->taxonomy_arg = $taxonomy_args;

                if ($screen == 'add-new') {
                    $result = $entity->add();
                    $url_arg = 'taxonomy-added';
                } else {
                    $result = $entity->update();
                    $url_arg = 'taxonomy-updated';
                }

                if ($result === false) {
                    $this->errorMsg = __('Unexpected error occured.', 'wpfront-user-role-editor');
                    return;
                }

                if (!current_user_can('edit_taxonomies')) {
                    wp_safe_redirect(add_query_arg('taxonomy-added', 'true', $this->get_list_url()));
                    exit;
                } else {
                    wp_safe_redirect(add_query_arg($url_arg, 'true', $this->get_edit_url($entity->name)));
                    exit;
                }
            }
        }

        private function handle_action($action, $datas = null) {
            switch ($action) {
                case 'delete':
                    $cap = 'delete_taxonomies';
                    $check = 'can_delete';
                    $q_arg = 'taxonomy-deleted';
                    break;

                case 'restore':
                    $cap = 'edit_taxonomies';
                    $check = 'can_restore';
                    $q_arg = 'taxonomy-restored';
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
                $data = $this->get_taxonomy_data_from_url();
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
                check_admin_referer('bulk-action-view-taxonomy');
                foreach ($entities as $entity) {
                    $entity->delete($action);
                }
                wp_safe_redirect(add_query_arg($q_arg, 'true', $this->get_list_url()));
                exit;
            }

            $this->objView = new WPFront_User_Role_Editor_Taxonomy_Delete_View($this, $entities, $action);
        }

        protected function sanitize_add_edit_taxonomy_args($screen, $taxonomy_args, $entity) {
            return $taxonomy_args;
        }

        private function get_labels_arg() {
            $args = array();
            $args['name'] = $this->get_submitted_text('label');

            $props = [
                'singular_name',
                'search_items',
                'popular_items',
                'all_items',
                'edit_item',
                'parent_item',
                'parent_item_colon',
                'view_item',
                'update_item',
                'add_new_item',
                'new_item_name',
                'separate_items_with_commas',
                'add_or_remove_items',
                'choose_from_most_used',
                'not_found',
                'no_terms',
                'filter_by_item',
                'items_list_navigation',
                'items_list',
                'most_used',
                'back_to_items'
            ];
            foreach ($props as $prop) {
                $value = $this->get_submitted_text($prop);
                if ($value !== null) {
                    $args[$prop] = $value;
                }
            }


            return $args;
        }

        private function get_advanced_settings_arg() {
            $args = array();

            $props = [
                'rest_base',
                'rest_controller_class'
            ];

            foreach ($props as $prop) {
                $value = $this->get_submitted_text($prop);
                if ($value !== null) {
                    $args[$prop] = $value;
                }
            }

            $props = [
                'public',
                'hierarchical',
                'publicly_queryable',
                'show_ui',
                'show_in_menu',
                'show_in_nav_menus',
                'show_in_rest',
                'show_tagcloud',
                'show_in_quick_edit',
                'show_admin_column',
                'query_var',
                'rewrite'
            ];

            foreach ($props as $prop) {
                $value = $this->get_submitted_boolean($prop);
                if ($value !== null) {
                    $args[$prop] = $value;
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


                $hierarchical = $this->get_submitted_boolean('rewrite_hierarchical');
                if (is_bool($hierarchical)) {
                    $rewrite_array['hierarchical'] = $hierarchical;
                }

                if (empty($rewrite_array)) {
                    $args['rewrite'] = true;
                } else {
                    $args['rewrite'] = $rewrite_array;
                }
            }

            return $args;
        }

        /**
         * Displays the taxonomies view.
         */
        public function view() {
            if (!parent::view()) {
                return;
            }

            if (empty($this->objView)) {
                $this->objView = new WPFront_User_Role_Editor_Taxonomies_List_View($this);
            }

            $this->objView->view();
        }

        public function apply_active_list_filter() {
            $taxonomies = $this->get_all_taxonomies_data();
            $taxonomies = $this->sort_taxonomies_data($taxonomies);

            switch ($this->get_active_list_filter()) {
                case 'all':
                    break;

                case 'builtin':
                    foreach ($taxonomies as $key => $entity) {
                        if ($entity->source_type === self::SOURCE_TYPE_OTHER || $entity->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                            unset($taxonomies[$key]);
                        }
                    }
                    break;

                case 'other':
                    foreach ($taxonomies as $key => $entity) {
                        if ($entity->source_type === self::SOURCE_TYPE_BUILTIN || $entity->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                            unset($taxonomies[$key]);
                        }
                    }
                    break;

                case 'userdefined':
                    foreach ($taxonomies as $key => $entity) {
                        if ($entity->source_type === self::SOURCE_TYPE_OTHER || $entity->source_type === self::SOURCE_TYPE_BUILTIN) {
                            unset($taxonomies[$key]);
                        }
                    }
                    break;

                case 'active':
                    foreach ($taxonomies as $key => $entity) {
                        if ($entity->status === self::STATUS_INACTIVE) {
                            unset($taxonomies[$key]);
                        }
                    }
                    break;

                case 'inactive':
                    foreach ($taxonomies as $key => $entity) {
                        if ($entity->status === self::STATUS_ACTIVE) {
                            unset($taxonomies[$key]);
                        }
                    }
                    break;
            }

            return $taxonomies;
        }

        public function get_active_list_filter() {
            if (empty($_GET['list'])) {
                return 'all';
            }

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

        protected function get_all_entities() {
            if ($this->entities !== null) {
                return $this->entities;
            }

            $entity = new WPFront_User_Role_Editor_Taxonomies_Entity();
            $this->entities = $entity->get_all();

            $this->entities = $this->sanitize_pro_fields($this->entities);

            return $this->entities;
        }

        protected function sanitize_pro_fields($entities) {
            foreach ($entities as $post_type => $entity) {
                $entity->capability_type = null;
            }

            return $entities;
        }

        public function search($search) {
            $taxonomies = $this->get_all_taxonomies_data();
            $taxonomies = $this->sort_taxonomies_data($taxonomies);

            if (empty($search)) {
                return $taxonomies;
            }

            foreach ($taxonomies as $name => $item) {
                if (strpos($item->name, $search) !== false) {
                    continue;
                }

                if (strpos($item->label, $search) !== false) {
                    continue;
                }

                unset($taxonomies[$name]);
            }

            return $taxonomies;
        }

        protected function sort_taxonomies_data($taxonomies) {
            $built_in_taxonomies = array();
            $other_taxonomies = array();
            $user_defined_taxonomies = array();

            foreach ($taxonomies as $taxonomy => $data) {
                if ($data->source_type === self::SOURCE_TYPE_BUILTIN) {
                    $built_in_taxonomies[$taxonomy] = $data;
                    continue;
                }

                if ($data->source_type === self::SOURCE_TYPE_OTHER) {
                    $other_taxonomies[$taxonomy] = $data;
                    continue;
                }

                if ($data->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                    $user_defined_taxonomies[$taxonomy] = $data;
                    continue;
                }
            }

            ksort($built_in_taxonomies);
            ksort($other_taxonomies);
            ksort($user_defined_taxonomies);
            return array_merge($built_in_taxonomies, $other_taxonomies, $user_defined_taxonomies);
        }

        public function get_list_filter_data() {
            $filter_data = array();
            $built_in = [];
            $other = [];
            $user_defined = [];
            $active = [];
            $inactive = [];
            $taxonomies = $this->get_all_taxonomies_data();
            $page = $this->get_self_url();

            $filter_data['all'] = array(
                'display' => __('All', 'wpfront-user-role-editor'),
                'url' => $page . '&list=all',
                'count' => count($taxonomies)
            );

            foreach ($taxonomies as $entity) {
                if ($entity->source_type === self::SOURCE_TYPE_BUILTIN) {
                    $built_in[] = $entity;
                } elseif ($entity->source_type === self::SOURCE_TYPE_OTHER) {
                    $other[] = $entity;
                } elseif ($entity->source_type === self::SOURCE_TYPE_USER_DEFINED) {
                    $user_defined[] = $entity;
                }

                if ($entity->status === self::STATUS_ACTIVE) {
                    $active[] = $entity;
                } elseif ($entity->status === self::STATUS_INACTIVE) {
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

        public function clear_cache() {
            $this->taxonomies_cache_clear = true;
        }

        public function get_all_taxonomies_data() {
            if (!$this->taxonomies_cache_clear && !empty($this->taxonomies_cache)) {
                return $this->taxonomies_cache;
            }

            $has_edit_cap = current_user_can('edit_taxonomies');
            $has_delete_cap = current_user_can('delete_taxonomies');
            $has_clone_cap = current_user_can('create_taxonomies');

            $taxonomies = get_taxonomies([], 'objects');
            $exiting = array();
            foreach ($taxonomies as $name => $taxonomy_obj) {
                if ($taxonomy_obj->_builtin && !is_taxonomy_viewable($name)) {
                    continue;
                }

                $data = new \stdClass();
                $data->name = $taxonomy_obj->name;
                $data->label = $taxonomy_obj->label;
                $data->status = self::STATUS_ACTIVE;
                $data->source_type = $taxonomy_obj->_builtin ? self::SOURCE_TYPE_BUILTIN : self::SOURCE_TYPE_OTHER;
                $data->post_types = is_array($taxonomy_obj->object_type) ? $taxonomy_obj->object_type : [];

                if (isset($this->taxonomy_args[$name])) {
                    $data->taxonomy_arg = $this->taxonomy_args[$name];
                } else {
                    $data->taxonomy_arg = array();
                }

                if ($taxonomy_obj->_builtin) {
                    $data->capability_type = null;
                } else {
                    $cap = $taxonomy_obj->cap->manage_terms;
                    if ($cap === 'manage_categories') {
                        $data->capability_type = null;
                    } else {
                        $data->capability_type = substr($cap, strlen('manage_'));
                    }
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
                    if (isset($this->taxonomies_unregistered[$entity->name])) {
                        $data->source_type = self::SOURCE_TYPE_OTHER;
                    } else {
                        $data->source_type = self::SOURCE_TYPE_USER_DEFINED;
                    }
                } else {
                    $data->source_type = $exiting[$name]->source_type;
                }
                $data->post_types = $entity->post_types;
                $data->capability_type = $entity->capability_type;
                $data->entity = $entity;

                $data->taxonomy_arg = (empty($entity->taxonomy_arg) && isset($exiting[$name]->taxonomy_arg)) ? $exiting[$name]->taxonomy_arg : $entity->taxonomy_arg;
                if (empty($data->taxonomy_arg)) {
                    $data->taxonomy_arg = array();
                }

                $user_edited[$name] = $data;
            }

            //reset source types to intial state to take care of registration changing source type.
            $taxes = array_merge($exiting, $user_edited);
            foreach ($taxes as $name => $data) {
                if (isset($this->taxonomies_cache[$name])) {
                    $data->source_type = $this->taxonomies_cache[$name]->source_type;
                }
            }

            $this->taxonomies_cache = $taxes;

            foreach ($this->taxonomies_cache as $name => $data) {
                if ($data->source_type === self::SOURCE_TYPE_BUILTIN) {
                    $data->can_edit = $has_edit_cap && is_taxonomy_viewable($name);
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
                    } elseif ($data->status == self::STATUS_INACTIVE) {
                        $data->can_activate = true;
                        $data->can_deactivate = false;
                    }
                } else {
                    $data->can_activate = false;
                    $data->can_deactivate = false;
                }

                $data->can_clone = $has_clone_cap;
            }

            return $this->taxonomies_cache;
        }

        /**
         * Return taxonomy list url.
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
         * Returns the add new taxonomies URL.
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
         * Returns the activate taxonomy URL.
         *
         * @return string
         */
        public function get_activate_url($name) {
            return wp_nonce_url($this->get_self_url(['screen' => 'activate', 'name' => $name]));
        }

        /**
         * Returns the deactivate taxonomy URL.
         *
         * @return string
         */
        public function get_deactivate_url($name) {
            return wp_nonce_url($this->get_self_url(['screen' => 'deactivate', 'name' => $name]));
        }

        /**
         * Returns the delete taxonomy URL.
         *
         * @return string
         */
        public function get_delete_url($name) {
            if (empty($name)) {
                return $this->get_self_url(['screen' => 'delete']);
            }

            return $this->get_self_url(['screen' => 'delete', 'name' => $name]);
        }

        public function get_edit_url($name) {
            return $this->get_self_url(['screen' => 'edit', 'name' => $name]);
        }

        /**
         * Returns the clone taxonomies URL.
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

        protected function get_submitted_boolean($name) {
            if (isset($_POST[$name]) && $_POST[$name] == '') {
                return null;
            }

            return !empty($_POST[$name]);
        }

        private function get_submitted_array($name) {
            if (!empty($_POST[$name]) && is_array($_POST[$name])) {
                return $_POST[$name];
            }

            return [];
        }

        public function get_taxonomy_data($taxonomy) {
            $lists = $this->get_all_taxonomies_data();
            if (!empty($lists[$taxonomy])) {
                return $lists[$taxonomy];
            }

            return null;
        }

        protected function get_taxonomy_data_from_url() {
            if (empty($_GET['name'])) {
                wp_safe_redirect($this->get_self_url());
                exit;
            }

            $taxonomy = $this->get_taxonomy_data($_GET['name']);
            if (empty($taxonomy)) {
                $this->errorMsg = __('Taxonomy do not exists.', 'wpfront-user-role-editor');
                $this->objView = new WPFront_User_Role_Editor_Taxonomies_List_View($this);
                return null;
            }

            return $taxonomy;
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

        public function get_taxonomy_customizable_hint_text($group_obj, $disabled) {
            $hint = __('Uses "manage_categories" capability.', 'wpfront-user-role-editor');
            $upgrade_message = sprintf(__('%s to customize capabilities.', 'wpfront-user-role-editor'), '<a href="https://wpfront.com/ureaddedit" target="_blank">' . __('Upgrade to Pro', 'wpfront-user-role-editor') . '</a>');
            $hint .= ' ' . $upgrade_message;

            return $hint;
        }

        protected function get_custom_caps($cap_type) {
            return array(
                'manage_terms' => "manage_$cap_type",
                'edit_terms' => "edit_$cap_type",
                'delete_terms' => "delete_$cap_type",
                'assign_terms' => "assign_$cap_type"
            );
        }

        public function registered_taxonomy_for_object_type($taxonomy, $object_type) {
            $data = $this->get_all_taxonomies_data();
            if (isset($data[$taxonomy])) {
                $data = $data[$taxonomy];
                if (!in_array($object_type, $data->post_types)) {
                    $this->clear_cache();
                }
            }
        }

        public function unregistered_taxonomy_for_object_type($taxonomy, $object_type) {
            $data = $this->get_all_taxonomies_data();
            if (isset($data[$taxonomy])) {
                $data = $data[$taxonomy];
                if (in_array($object_type, $data->post_types)) {
                    $this->clear_cache();
                }
            }
        }

        public static function get_debug_setting() {
            return array('key' => 'taxonomies', 'label' => __('Taxonomies', 'wpfront-user-role-editor'), 'position' => 100, 'description' => __('Disables all taxonomy functionalities.', 'wpfront-user-role-editor'));
        }

    }

    WPFront_User_Role_Editor_Taxonomies::load();
}