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
 * Class for WPFront User Role Editor Post Type Custom Capabilities.
 *
 * @author Jinu Varghese
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Post_Type;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\Options\WPFront_User_Role_Editor_Options as Options;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;
use WPFront\URE\WPFront_User_Role_Editor_Debug;

if (!class_exists('\WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type_Custom_Capability')) {

    /**
     * Post Type Custom capability class
     *
     * @author Jinu Varghese
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_Post_Type_Custom_Capability {

        /**
         *
         * @var WPFront_User_Role_Editor_Post_Type_Custom_Capability[] 
         */
        private static $custom_cap_objs = array();
        protected $cap_names = array();

        /**
         * Used in restore role to restore properly.
         * 
         * @var string[]
         */
        protected static $custom_caps = array();

        protected abstract function init($controller);

        /**
         * Returns custom cap prefix.
         */
        protected abstract function cap_prefix();

        /**
         * Returns cap prefix to add before in sort.
         */
        protected abstract function add_before_prefix();

        /**
         * Returns the cap to check while defining role defaults.
         */
        protected abstract function role_default_value_cap($post_type);
        
        protected abstract function can_merge();
        
        protected abstract function get_debug_setting();

        
        public static function initialize($controller) {
            foreach (self::$custom_cap_objs as $key => $obj) {
                $obj->init($controller);

                if (!is_admin()) {
                    continue;
                }

                //sort cap in RolesHelper.
                add_filter('wpfront_ure_role_group_capabilities', array($obj, 'order_group_caps'), 10, 2);
            }
        }

        public static function register_post_type_args($controller, $args, $post_type) {
            foreach (self::$custom_cap_objs as $key => $obj) {
                $args = $obj->post_type_args($args, $post_type);
            }
            return $args;
        }

        public static function register($key, $objCustomCap) {
            $debug_setting = $objCustomCap->get_debug_setting();
            $debug = WPFront_User_Role_Editor_Debug::instance();
            $debug->add_setting($debug_setting);

            if($debug->is_disabled($debug_setting['key'])) {
                return;
            }
            
            self::$custom_cap_objs[$key] = $objCustomCap;
        }

        protected function post_type_args($args, $post_type) {
            //attachment caps are handled in media permissions class.
            if ($post_type === 'attachment') {
                return $args;
            }

            $args_local = $args;

            if (empty($args_local['capability_type'])) {
                $args_local['capability_type'] = 'post';
            }

            if (!$this->is_user_visible($args_local)) {
                return $args;
            }

            if (!is_array($args_local['capability_type'])) {
                $capability_type = array($args_local['capability_type'], $args_local['capability_type'] . 's');
            } else {
                $capability_type = $args_local['capability_type'];
            }

            list( $singular_base, $plural_base ) = $capability_type;

            if (empty($args['capabilities'])) {
                $args['capabilities'] = array();
            }

            $prefix = $this->cap_prefix();
            $cap = $prefix . '_' . $plural_base;
            $this->cap_names[$post_type] = [$cap, $plural_base];

            if($this->can_merge()) {
                if(!isset($args['map_meta_cap'])) {
                    if ( empty( $args['capabilities'] ) && in_array( $args_local['capability_type'], array( 'post', 'page' ), true ) ) {
                        $args['map_meta_cap'] = true;
                    }
                }
                
                $args['capabilities'] = array_merge($args['capabilities'], array($prefix . '_posts' => $cap));
            }

            if (!is_admin()) {
                return $args;
            }

            $map_cap = $this->role_default_value_cap($post_type);
            $settings_key = $this->update_role_caps_settings_key();
            $this->update_role_caps($post_type, $cap, $map_cap, $singular_base, $settings_key);

            if (Utils::doing_ajax()) {
                return $args;
            }

            $this->toggle_cap_functionality($cap);

            //for help links
            if ($plural_base === 'posts' || $plural_base === 'pages') {
                add_filter("wpfront_ure_capability_{$cap}_ui_help_link", array($this, 'cap_help_link'), 10, 2);
            }

            return $args;
        }

        
        /**
         * Checks whether post type is user visible.
         * 
         * @param array|string $args Array if post type arguments or post type name.
         * @return boolean
         */
        protected function is_user_visible($args) {
            $public = false;
            $show_ui = false;
            $cap_not_defaulted = false;
            
            if(is_array($args)) {
                if (!isset($args['public'])) { //public default is false.
                    return false;
                }

                if (!isset($args['show_ui'])) {
                    $args['show_ui'] = $args['public'];
                }
                
                if(empty($args['capability_type'])) {
                    $args['capability_type'] = 'post';
                }
                
                $public = $args['public'];
                $show_ui = $args['show_ui'];
                $cap_not_defaulted = $args['capability_type'] !== 'post';
            } else {
                $post_type_obj = get_post_type_object($args);
                if(empty($post_type_obj)) {
                    return false;
                }
                
                $public = $post_type_obj->public;
                $show_ui = $post_type_obj->show_ui;
                $cap_not_defaulted = $post_type_obj->capability_type != 'post';
            }
            
            return $public && ($show_ui || $cap_not_defaulted);
        }

        protected function get_capability_name($post_type) {
            return $this->cap_names[$post_type][0];
        }

        protected function get_capability_name_base($post_type) {
            return $this->cap_names[$post_type][1];
        }

        /**
         * Orders the custom cap in RolesHelper.
         * 
         * @param string[] $group_caps
         * @param object $group
         * @return string[]
         */
        public function order_group_caps($group_caps, $group) { 
            if ($group->type === 'default') {
                if ($group->key === 'posts') {
                    $group_caps = $this->reorder_cap('post', $group_caps);
                }

                if ($group->key === 'pages') {
                    $group_caps = $this->reorder_cap('page', $group_caps);
                }
            }

            if ($group->type === 'custom_post') {
                $group_caps = $this->reorder_cap($group->key, $group_caps);
            }

            return $group_caps;
        }

        /**
         * Orders the custom cap in list.
         * 
         * @param string $post_type
         * @param string[] $caps
         * @return string[]
         */
        protected function reorder_cap($post_type, $caps) {
            if(!$this->is_user_visible($post_type)) {
                return $caps;
            }
            
            $cap = $this->get_capability_name($post_type);

            $index = array_search($cap, $caps);
            if ($index !== false) {
                array_splice($caps, $index, 1);
            }

            $index = array_search($this->add_before_prefix() . '_' . $this->get_capability_name_base($post_type), $caps);
            array_splice($caps, $index, 0, $cap);

            return $caps;
        }

        /**
         * Returns cap help link.
         * 
         * @param string $help_link
         * @param string $cap
         * @return string
         */
        public function cap_help_link($help_link, $cap) {
            return RolesHelper::get_wpfront_help_link($cap);
        }

        protected function toggle_cap_functionality($cap) {
            add_filter("wpfront_ure_capability_{$cap}_functionality_enabled", '__return_false');
        }

        protected function update_role_caps_settings_key() {
            $settings_key = "post_type_custom_capabilities_default_state_processed";
            return $settings_key;
        }

        protected function update_role_caps($post_type, $cap, $check_cap, $capability_type, $settings_key) {
            if ($post_type !== 'post' && $capability_type === 'post') {
                return;
            }

            self::$custom_caps[$cap] = $check_cap;

            $value = Options::instance()->get_option($settings_key);
            if (!empty($value[$cap . '_' . $post_type])) {
                return;
            }

            $role_names = RolesHelper::get_roles();
            foreach ($role_names as $role_name) {
                $role = RolesHelper::get_role($role_name);
                
                if($check_cap === true) {
                    $role->add_cap($cap, true);
                    continue;
                }
                
                if (isset($role->capabilities[$check_cap])) {
                    if (!isset($role->capabilities[$cap])) {
                        $role->add_cap($cap, $role->capabilities[$check_cap]);
                    }
                }
            }

            if (empty($value)) {
                $value = array();
            }

            $value[$cap . '_' . $post_type] = true;

            Options::instance()->set_option($settings_key, $value);
        }

        public static function restore_role_custom_caps($custom_caps) { 
            foreach (self::$custom_caps as $cap => $check) {
                $custom_caps[$cap] = $check;
            }

            return $custom_caps;
        }

    }

    add_filter('wpfront_ure_restore_role_custom_caps', array(WPFront_User_Role_Editor_Post_Type_Custom_Capability::class, 'restore_role_custom_caps'));
}

require_once dirname(__FILE__) . '/custom-caps/class-read-others-capability.php';
require_once dirname(__FILE__) . '/custom-caps/class-create-posts-capability.php';
