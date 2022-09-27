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
 * Helper class for WPFront User Role Editor
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\Taxonomies\WPFront_User_Role_Editor_Taxonomies;
use WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type;

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_Roles_Helper')) {

    /**
     * Roles helper class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Roles_Helper {

        const ADMINISTRATOR_ROLE_KEY = 'administrator';
        const EDITOR_ROLE_KEY = 'editor';
        const AUTHOR_ROLE_KEY = 'author';
        const CONTRIBUTOR_ROLE_KEY = 'contributor';
        const SUBSCRIBER_ROLE_KEY = 'subscriber';

        protected static $DEFAULT_ROLES = array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY, self::SUBSCRIBER_ROLE_KEY);
        
        protected static $STANDARD_CAPABILITIES = array(
            'dashboard' => array(
                'read' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY, self::SUBSCRIBER_ROLE_KEY),
                'edit_dashboard' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'posts' => array(
                'publish_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'edit_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY),
                'delete_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY),
                'edit_published_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'delete_published_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'edit_others_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_others_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'read_private_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_private_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_private_posts' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'manage_categories' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'media' => array(
                'upload_files' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'unfiltered_upload' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'pages' => array(
                'publish_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_published_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_published_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_others_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_others_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'read_private_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'edit_private_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'delete_private_pages' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'comments' => array(
                'moderate_comments' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'themes' => array(
                'switch_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_theme_options' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'delete_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'install_themes' => array(self::ADMINISTRATOR_ROLE_KEY),
                'update_themes' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'plugins' => array(
                'activate_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'install_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'update_plugins' => array(self::ADMINISTRATOR_ROLE_KEY),
                'delete_plugins' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'users' => array(
                'list_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'create_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'edit_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'delete_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'promote_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                //'add_users' => array(self::ADMINISTRATOR_ROLE_KEY),
                'remove_users' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'tools' => array(
                'import' => array(self::ADMINISTRATOR_ROLE_KEY),
                'export' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'admin' => array(
                'manage_options' => array(self::ADMINISTRATOR_ROLE_KEY),
                'update_core' => array(self::ADMINISTRATOR_ROLE_KEY),
                'unfiltered_html' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'links' => array(
                'manage_links' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY)
            ),
            'deprecated' => array(
                'edit_files' => array(self::ADMINISTRATOR_ROLE_KEY),
                'level_0' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY, self::SUBSCRIBER_ROLE_KEY),
                'level_1' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY, self::CONTRIBUTOR_ROLE_KEY),
                'level_2' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY, self::AUTHOR_ROLE_KEY),
                'level_3' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_4' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_5' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_6' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_7' => array(self::ADMINISTRATOR_ROLE_KEY, self::EDITOR_ROLE_KEY),
                'level_8' => array(self::ADMINISTRATOR_ROLE_KEY),
                'level_9' => array(self::ADMINISTRATOR_ROLE_KEY),
                'level_10' => array(self::ADMINISTRATOR_ROLE_KEY)
            ),
            'network' => array(
                'manage_network' => array(),
                'manage_sites' => array(),
                'create_sites' => array(),
                'delete_sites' => array(),
                'manage_network_roles' => array(),
                'manage_network_users' => array(),
                'manage_network_themes' => array(),
                'manage_network_plugins' => array(),
                'manage_network_options' => array(),
                'upgrade_network' => array()
            )
        );
        
        protected static $ROLE_CAPS = array(
            'list_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'edit_role_menus',
            'edit_nav_menu_permissions',
            'edit_content_shortcodes',
            'delete_content_shortcodes',
            'edit_login_redirects',
            'delete_login_redirects',
            'bulk_edit_roles',
            'edit_widget_permissions',
            'create_posttypes',
            'edit_posttypes',
            'delete_posttypes',
            'create_taxonomies',
            'edit_taxonomies',
            'delete_taxonomies'
        );
        protected static $capability_group_names = null;
        protected static $custom_capability_groups = array();
        protected static $group_capabilities_cache = array();

        /**
         * Checks whether given role is a WP default role.
         * 
         * @param string $role_name
         * @return bool
         */
        public static function is_default_role($role_name) {
            return in_array($role_name, self::$DEFAULT_ROLES);
        }

        /**
         * Check whether given role exists within the system.
         * 
         * @param string $role_name
         * @return bool
         */
        public static function is_role($role_name) {
            if (empty($role_name)) {
                return false;
            }

            return wp_roles()->is_role($role_name);
        }

        /**
         * Returns the display name of the given role.
         * 
         * @param string $role_name
         * @return string
         */
        public static function get_display_name($role_name, $default = false) {
            if (self::is_role($role_name)) {
                $names = wp_roles()->get_names();
                return $names[$role_name];
            }

            if ($default) {
                $display_name = ucfirst($role_name);
                $display_name = translate_user_role($display_name);
                return $display_name;
            }

            return null;
        }

        /**
         * Returns role with its display names.
         * 
         * @return string[] Associative (name=>display).
         */
        public static function get_names() {
            $roles = wp_roles()->get_names();

            $admin_name = null;
            if (isset($roles[self::ADMINISTRATOR_ROLE_KEY])) {
                $admin_name = $roles[self::ADMINISTRATOR_ROLE_KEY];
                unset($roles[self::ADMINISTRATOR_ROLE_KEY]);
            }

            asort($roles);
            if (!empty($admin_name)) {
                $roles = array(self::ADMINISTRATOR_ROLE_KEY => $admin_name) + $roles;
            }

            return $roles;
        }

        /**
         * Returns the roles.
         * 
         * @return string[] List of role names.
         */
        public static function get_roles() {
            return array_keys(wp_roles()->role_names);
        }

        /**
         * Returns role object or null.
         * 
         * @param string $role_name
         * @return \WP_Role
         */
        public static function get_role($role_name) {
            return wp_roles()->get_role($role_name);
        }

        /**
         * Returns role capabilities.
         * 
         * @param string $role_name
         * @return bool[]|null Associative (cap=>grant)
         */
        public static function get_capabilities($role_name) {
            if (self::is_role($role_name)) {
                return wp_roles()->role_objects[$role_name]->capabilities;
            }

            return null;
        }

        /**
         * Returns default role names.
         * 
         * @return array
         */
        public static function get_default_rolenames() {
            return self::$DEFAULT_ROLES;
        }

        /**
         * Adds a new role to the system.
         * 
         * @param string $role
         * @param string $display_name
         * @param array $capabilities
         * @return \WP_Role
         */
        public static function add_role($role, $display_name, $capabilities) {
            return wp_roles()->add_role($role, $display_name, $capabilities);
        }

        /**
         * Updates an existing role in the system.
         * 
         * @param string $role
         * @param string $display_name
         * @param array $capabilities
         * @return \WP_Role
         */
        public static function update_role($role, $display_name, $capabilities) {
            if (self::is_role($role)) {
                if (!self::can_set_network_capability()) {
                    foreach ($capabilities as $cap => $value) {
                        if (self::is_network_capability($cap)) {
                            unset($capabilities[$cap]);
                        }
                    }

                    $caps_existing = self::get_capabilities($role);
                    $network_caps = array();
                    foreach ($caps_existing as $cap => $value) {
                        if (self::is_network_capability($cap)) {
                            $network_caps[$cap] = $value;
                        }
                    }
                    $capabilities = array_merge($capabilities, $network_caps);
                }

                wp_roles()->remove_role($role);
                return wp_roles()->add_role($role, $display_name, $capabilities);
            }

            return null;
        }

        /**
         * Removes a role from system.
         * 
         * @param string $role
         */
        public static function remove_role($role) {
            wp_roles()->remove_role($role);
        }

        /**
         * Remove roles from system.
         * 
         * @param string[] $roles
         */
        public static function remove_roles($roles) {
            if (empty($roles)) {
                return;
            }

            $roles_obj = wp_roles();
            $roles_obj->use_db = false;
            foreach ($roles as $role) {
                $roles_obj->remove_role($role);
            }

            $role = reset($roles);
            $roles_obj->role_objects[$role] = true;
            $roles_obj->use_db = true;
            $roles_obj->remove_role($role);
        }

        /**
         * Adds a capability to role.
         * 
         * @param string $role_name
         * @param string $cap
         * @param boolean $grant
         * @return boolean
         */
        public static function add_capability_to_role($role_name, $cap, $grant = true) {
            $role = self::get_role($role_name);
            if (!empty($role)) {
                $role->add_cap($cap, $grant);
                return true;
            }

            return false;
        }

        /**
         * Add capabilities to role.
         * 
         * @param string $role_name
         * @param bool[] $caps cap => grant
         * @return boolean
         */
        public static function add_capabilities_to_role($role_name, $caps) {
            $role = self::get_role($role_name);
            if (empty($role)) {
                return false;
            }

            $role_objects = wp_roles();
            $role_objects->use_db = false;
            foreach ($caps as $cap => $grant) {
                $role->add_cap($cap, $grant);
            }

            $role_objects->use_db = true;
            foreach ($caps as $cap => $grant) {
                $role->add_cap($cap, $grant);
                break;
            }

            return true;
        }

        /**
         * Remove capabilities from role.
         * 
         * @param string $role_name
         * @param string[] $caps
         * @return boolean
         */
        public static function remove_capabilities_from_role($role_name, $caps) {
            $role = self::get_role($role_name);
            if (empty($role)) {
                return false;
            }

            $role_objects = wp_roles();
            $role_objects->use_db = false;
            foreach ($caps as $cap) {
                $role->remove_cap($cap);
            }

            $role_objects->use_db = true;
            foreach ($caps as $cap) {
                $role->remove_cap($cap);
                break;
            }

            return true;
        }

        /**
         * Returns capability groups with their display names.
         * 
         * @return array string => object
         */
        public static function get_capabilty_groups() {
            if (self::$capability_group_names !== null) {
                return self::$capability_group_names;
            }

            self::$capability_group_names = array(
                'dashboard' => (object) array('key' => 'dashboard', 'label' => __('Dashboard', 'wpfront-user-role-editor'), 'type' => 'default'),
                'posts' => (object) array('key' => 'posts', 'label' => __('Posts', 'wpfront-user-role-editor'), 'type' => 'default'),
                'media' => (object) array('key' => 'media', 'label' => __('Media', 'wpfront-user-role-editor'), 'type' => 'default'),
                'pages' => (object) array('key' => 'pages', 'label' => __('Pages', 'wpfront-user-role-editor'), 'type' => 'default'),
                'comments' => (object) array('key' => 'comments', 'label' => __('Comments', 'wpfront-user-role-editor'), 'type' => 'default'),
                'themes' => (object) array('key' => 'themes', 'label' => __('Themes', 'wpfront-user-role-editor'), 'type' => 'default'),
                'plugins' => (object) array('key' => 'plugins', 'label' => __('Plugins', 'wpfront-user-role-editor'), 'type' => 'default'),
                'users' => (object) array('key' => 'users', 'label' => __('Users', 'wpfront-user-role-editor'), 'type' => 'default'),
                'roles' => (object) array('key' => 'roles', 'label' => __('Roles (WPFront)', 'wpfront-user-role-editor'), 'type' => 'wpfront'),
                'tools' => (object) array('key' => 'tools', 'label' => __('Tools', 'wpfront-user-role-editor'), 'type' => 'default'),
                'admin' => (object) array('key' => 'admin', 'label' => __('Admin', 'wpfront-user-role-editor'), 'type' => 'default'),
                'links' => (object) array('key' => 'links', 'label' => __('Links', 'wpfront-user-role-editor'), 'type' => 'default'),
                'deprecated' => (object) array('key' => 'deprecated', 'label' => __('Deprecated', 'wpfront-user-role-editor'), 'type' => 'default')
            );

            $post_types = WPFront_User_Role_Editor_Post_Type::instance()->get_user_visible_cpt();
            foreach ($post_types as $name => $data) {
                self::$capability_group_names["cpt_$name"] = (object) array(
                            'key' => $name,
                            'label' => $data->label . ' (' . __('Post Type', 'wpfront-user-role-editor') . ')',
                            'type' => 'custom_post',
                            'data' => $data
                );
            }

            $taxonomies = WPFront_User_Role_Editor_Taxonomies::instance()->get_all_taxonomies_data();
            foreach ($taxonomies as $name => $data) {
                if (!is_taxonomy_viewable($name)) {
                    continue;
                }

                self::$capability_group_names["tax_$name"] = (object) array(
                            'key' => $name,
                            'label' => $data->label . ' (' . __('Taxonomy', 'wpfront-user-role-editor') . ')',
                            'type' => 'taxonomy',
                            'data' => $data
                );
            }

            foreach (self::$custom_capability_groups as $group => $data) {
                if (!isset(self::$capability_group_names[$group])) {
                    self::$capability_group_names[$group] = (object) array('key' => $data->key, 'label' => $data->label, 'type' => $data->type);
                }
            }

            self::$capability_group_names['other'] = (object) array('key' => 'other', 'label' => __('Other Capabilities', 'wpfront-user-role-editor'), 'type' => 'other');

            self::$capability_group_names = apply_filters('wpfront_ure_capability_groups', self::$capability_group_names);

            return self::$capability_group_names;
        }

        /**
         * Returns capabilities against a group.
         * 
         * @param object $group
         * @return string[]
         */
        public static function get_group_capabilities($group) {
            $group_key = $group->key;
            $group_cache_key = $group->key . '_' . $group->type;

            if (array_key_exists($group_cache_key, self::$group_capabilities_cache)) {
                return self::$group_capabilities_cache[$group_cache_key];
            }

            $group_caps = array();

            switch ($group->type) {
                case 'default':
                    if (array_key_exists($group_key, self::$STANDARD_CAPABILITIES)) {
                        $group_caps = array_keys(self::$STANDARD_CAPABILITIES[$group_key]);
                    }

                    if ($group_key === 'posts' || $group_key === 'pages') {
                        $group_caps = array();
                        $post_type = $group_key === 'posts' ? 'post' : 'page';
                        $group_caps = array_merge($group_caps, self::get_post_type_caps(get_post_type_object($post_type), $group_key === 'posts'));
                        $group_caps = array_values(array_unique($group_caps));
                    }

                    if ($group_key === 'network') {
                        $roles = self::get_roles();
                        foreach ($roles as $role) {
                            $caps = self::get_capabilities($role);

                            foreach ($caps as $cap => $allow) {
                                if (strpos($cap, 'manage_network') === 0) {
                                    $group_caps[] = $cap;
                                }
                            }
                        }

                        $group_caps = array_unique($group_caps);
                    }

                    break;

                case 'custom_post':
                    $post_type_object = get_post_type_object($group_key);

                    if ($post_type_object->capability_type === 'post' || $post_type_object->capability_type === 'page' || $post_type_object->capability_type === 'attachment') {
                        return 'defaulted';
                    }

                    $group_caps = self::get_post_type_caps($post_type_object);
                    break;

                case 'taxonomy':
                    $tax_obj = get_taxonomy($group_key);
                    $caps = $tax_obj->cap;
                    if ($caps->manage_terms === 'manage_categories' || $caps->manage_terms === 'manage_post_tags') {
                        return 'defaulted';
                    }

                    $caps = (array) $caps;
                    $group_caps = array_values($caps);
                    break;

                case 'wpfront':
                    $group_caps = self::$ROLE_CAPS;
                    break;

                case 'other':
                    $other_caps = array();

                    $roles = self::get_roles();
                    foreach ($roles as $role) {
                        $caps = self::get_capabilities($role);

                        foreach ($caps as $cap => $allow) {
                            $other_caps[$cap] = $cap;
                        }
                    }

                    $groups = self::get_capabilty_groups();
                    foreach ($groups as $name => $group) {
                        if ($group->type === 'other') {
                            continue;
                        }

                        $caps = self::get_group_capabilities($group);

                        if (!is_array($caps)) {
                            continue;
                        }

                        foreach ($caps as $cap) {
                            unset($other_caps[$cap]);
                        }
                    }

                    $group_caps = array_keys($other_caps);
                    break;
            }

            if (array_key_exists($group_key, self::$custom_capability_groups)) {
                $custom_caps = self::$custom_capability_groups[$group_key]->caps;

                foreach ($custom_caps as $cap => $value) {
                    $group_caps[] = $cap;
                }
            }

            $group_caps = apply_filters('wpfront_ure_role_group_capabilities', $group_caps, $group);

            self::$group_capabilities_cache[$group_cache_key] = $group_caps;
            return $group_caps;
        }

        /**
         * Returns caps from post type object.
         * 
         * @param \WP_Post_Type $post_type_object
         * @return string[]
         */
        private static function get_post_type_caps($post_type_object, $add_missing_std_caps = false) {
            $caps = array();
            $meta_caps = array('read', 'read_post', 'edit_post', 'delete_post');

            $posttype_caps = $post_type_object->cap;
            if ($posttype_caps->create_posts === $posttype_caps->edit_posts) {
                $meta_caps[] = 'create_posts';
            }

            foreach ($posttype_caps as $post_cap => $cap) {
                if (!in_array($post_cap, $meta_caps)) {
                    $caps[$post_cap] = $cap;
                }
            }

            $post_caps = array_keys(self::$STANDARD_CAPABILITIES['posts']);
            $post_caps_existing = array();
            
            $caps_order = array();
            foreach ($post_caps as $cap) {
                if (isset($caps[$cap])) {
                    $caps_order[] = $caps[$cap];
                    unset($caps[$cap]);
                    $post_caps_existing[] = $cap;
                }
            }
            
            if($add_missing_std_caps) {
                $caps_order = array_merge($caps_order, array_diff($post_caps, $post_caps_existing));
            }

            $caps_order = array_merge($caps_order, array_values($caps));
            return array_values($caps_order);
        }

        /**
         * Returns standard capabilities against a role.
         * 
         * @param string $role_name
         * @return bool[] Associative (cap=>enabled).
         */
        public static function get_standard_capabilities($role_name) {
            $std_caps = array();

            foreach (self::$STANDARD_CAPABILITIES as $group => $caps) {
                foreach ($caps as $cap => $roles) {
                    $std_caps[$cap] = in_array($role_name, $roles);
                }
            }

            return $std_caps;
        }

        /**
         * Adds a capability group to group capabilities.
         * 
         * @param string $key Group identifier
         * @param string $display Group name displayed
         */
        public static function add_capability_group($key, $display) {
            $key = strtolower($key);

            if (array_key_exists($key, self::$custom_capability_groups)) {
                return;
            }

            self::$custom_capability_groups[$key] = (object) array('key' => $key, 'label' => $display, 'type' => 'custom', 'caps' => array());
        }

        /**
         * Adds a new custom capability to group.
         * 
         * @param string $group_key
         * @param string $cap
         * @return boolean
         */
        public static function add_new_capability_to_group($group_key, $cap) {
            $group_key = strtolower($group_key);

            if (!array_key_exists($group_key, self::$custom_capability_groups)) {
                return false;
            }

            self::$custom_capability_groups[$group_key]->caps[$cap] = true;

            return true;
        }

        /**
         * Returns Posts standard capabilities.
         * 
         * @return string[]
         */
        public static function get_standard_posts_capabilities() {
            return array_keys(self::$STANDARD_CAPABILITIES['posts']);
        }

        public static function get_standard_network_capabilities() {
            return array_keys(self::$STANDARD_CAPABILITIES['network']);
        }

        /**
         * Hooks into wpfront_ure_capability_ui_help_link to return cap help link.
         * 
         * @param string $cap
         * @param object $group
         * @return string
         */
        public static function cap_help_link($help_link, $cap, $group) {
            $has_help = false;

            switch ($group->type) {
                case 'default':
                    $group_key = $group->key;
                    if (array_key_exists($group_key, self::$STANDARD_CAPABILITIES)) {
                        $has_help = array_key_exists($cap, self::$STANDARD_CAPABILITIES[$group_key]);
                    } else {
                        $has_help = false;
                    }
                    break;

                case 'wpfront':
                    $has_help = in_array($cap, self::$ROLE_CAPS);
            }

            if ($has_help) {
                $help_link = self::get_wpfront_help_link($cap);
            }

            return $help_link;
        }

        /**
         * wpfront.com cap help link.
         * 
         * @param string $cap
         * @return string
         */
        public static function get_wpfront_help_link($cap) {
            return 'https://wpfront.com/wordpress-capabilities/#' . $cap;
        }

        /**
         *
         * @var boolean
         */
        private static $user_is_admin = null;

        /**
         * Hooks into user_has_cap and handles Administrator role caps.
         * 
         * @param string[] $allcaps
         * @param string[] $caps
         * @param string[] $args
         * @return string[]
         */
        public static function user_has_cap_administrator($allcaps, $caps, $args) {
            if (self::$user_is_admin === null) {
                if (!self::is_role(self::ADMINISTRATOR_ROLE_KEY)) {
                    self::$user_is_admin = false;
                    return $allcaps;
                }

                $user_id = intval($args[1]);
                $user = get_userdata($user_id);
                if (empty($user)) {
                    return $allcaps;
                }

                if (empty($user->roles)) {
                    self::$user_is_admin = false;
                    return $allcaps;
                }

                self::$user_is_admin = in_array(self::ADMINISTRATOR_ROLE_KEY, $user->roles);
            }

            if (self::$user_is_admin) {
                foreach ($caps as $cap) {
                    $allcaps[$cap] = true;
                }
            }

            return $allcaps;
        }

        public static function restore_role_custom_caps($custom_caps) {
            foreach (self::$ROLE_CAPS as $cap) {
                $custom_caps[$cap] = 'manage_options';
            }

            return $custom_caps;
        }

        public static function add_wpfront_caps_to_roles() {
            $role = self::get_role(self::ADMINISTRATOR_ROLE_KEY);
            if (!empty($role)) {
                $key = 'role_capabilities_processed';
                $processed = \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance()->get_option($key);
                if (version_compare($processed, \WPFront\URE\WPFront_User_Role_Editor::VERSION, '>=')) {
                    return;
                }

                if (!is_multisite()) {
                    foreach (self::$ROLE_CAPS as $cap) {
                        $role->add_cap($cap);
                    }
                }

                \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance()->set_option($key, \WPFront\URE\WPFront_User_Role_Editor::VERSION);
            }
        }

        public static function is_super_admin() {
            return is_multisite() && is_super_admin();
        }

        public static function can_set_network_capability() {
            return self::is_super_admin();
        }

        public static function is_network_capability($cap) {
            $std_caps = self::$STANDARD_CAPABILITIES['network'];
            if (isset($std_caps[$cap])) {
                return true;
            }

            if (substr($cap, 0, 14) === 'manage_network') {
                return true;
            }

            return false;
        }

        public static function init() {
            //switch_blog
            add_action('admin_init', '\WPFront\URE\WPFront_User_Role_Editor_Roles_Helper::add_wpfront_caps_to_roles', 1);

            add_filter('wpfront_ure_restore_role_custom_caps', '\WPFront\URE\WPFront_User_Role_Editor_Roles_Helper::restore_role_custom_caps');

            add_filter('wpfront_ure_capability_ui_help_link', '\WPFront\URE\WPFront_User_Role_Editor_Roles_Helper::cap_help_link', 10, 3);

            self::add_wpfront_caps_to_roles();
        }
        
        public static function clear_cache() {
            self::$capability_group_names = null;
            self::$group_capabilities_cache = array();
            
            //not a cache, do not clear
            //self::$custom_capability_groups = array();
        }

    }

    add_action('wpfront_ure_init', array(WPFront_User_Role_Editor_Roles_Helper::class, 'init'), 1);
    
    add_action('switch_blog', array(WPFront_User_Role_Editor_Roles_Helper::class, 'clear_cache'), 1);
}