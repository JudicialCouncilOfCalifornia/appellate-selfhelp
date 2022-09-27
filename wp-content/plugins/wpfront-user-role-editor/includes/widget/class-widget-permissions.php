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
 * Controller for WPFront User Role Editor Widget Permissions
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Widget;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;
use WPFront\URE\Options\WPFront_User_Role_Editor_Options as Options;

if (!class_exists('\WPFront\URE\Widget\WPFront_User_Role_Editor_Widget_Permissions')) {

    /**
     * Widget Permissions
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Widget_Permissions extends \WPFront\URE\WPFront_User_Role_Editor_Controller {

        protected static $ALL_USERS = 1;
        protected static $LOGGEDIN_USERS = 2;
        protected static $GUEST_USERS = 3;
        protected static $ROLE_USERS = 4;
        protected static $META_DATA_KEY = 'wpfront-user-role-editor-widget-permissions-data';

        const CAP = 'edit_widget_permissions';
        const USE_OLD_WIDGETS_UI = 'use_old_widgets_ui';

        protected function setUp() {
            $this->_setUp('edit_widget_permissions');
        }

        /**
         * Hooks into wpfront_ure_init.
         */
        protected function initialize() {
            //display logic
            add_filter('widget_display_callback', array($this, 'widget_display_callback'), 10, 3);

            //displays controls
            add_action('in_widget_form', array($this, 'in_widget_form'), 10, 3);
            //updates permissions
            add_filter('widget_update_callback', array($this, 'widget_update_callback'), 10, 4);

            //displays roles list
            add_action('wp_widget_permissions_custom_fields_roles_list', array($this, 'widget_permissions_custom_fields_roles_list'), 10, 3);

            if (!$this->in_admin_ui()) {
                return;
            }

            add_filter('wpfront_ure_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 30, 1);
            add_filter('wpfront_ure_ms_options_register_ui_field', array($this, 'wpfront_ure_options_register_ui_field'), 30, 1);

            // diplay old widget ui in wordpress 5.8
            $display_old_widget_ui = $this->Options->get_option_boolean(self::USE_OLD_WIDGETS_UI, true);

            if ($display_old_widget_ui) {
                add_filter('gutenberg_use_widgets_block_editor', '__return_false');
                add_filter('use_widgets_block_editor', '__return_false');
            }

            add_action('admin_print_scripts-widgets.php', array($this, 'enqueue_widget_scripts'));
            add_action('admin_print_styles-widgets.php', array($this, 'enqueue_widget_styles'));
        }

        /**
         * Returns widget permissions object.
         * 
         * @param array $instance
         * @return object
         */
        protected function get_meta_data($instance) {
            if (empty($instance) || empty($instance[self::$META_DATA_KEY])) {
                $data = (OBJECT) array('type' => self::$ALL_USERS);
            } else {
                $data = $instance[self::$META_DATA_KEY];
            }

            $data->type = intval($data->type);

            switch ($data->type) {
                case self::$ALL_USERS:
                case self::$LOGGEDIN_USERS:
                case self::$GUEST_USERS:
                case self::$ROLE_USERS:
                    break;

                default:
                    $data->type = self::$ALL_USERS;
                    break;
            }

            return $data;
        }

        /**
         * Hooks into in_widget_form, display widget custom fields.
         * 
         * @param \WP_Widget $this     The widget instance (passed by reference).
         * @param null      $return   Return null if new fields are added.
         * @param array     $instance An array of the widget's settings.
         */
        public function in_widget_form($widget, $return, $instance) {
            if (empty($instance)) {
                return;
            }

            if (!current_user_can(self::CAP)) {
                return;
            }

            $data = $this->get_meta_data($instance);
            ?>
            <p>
                <label><?php echo __('User Restrictions', 'wpfront-user-role-editor'); ?></label>
                <span class="user-restriction-container">
                    <label><input class="user-restriction-type" type="radio" name="<?php echo esc_attr($widget->get_field_name('user-restriction-type')); ?>" value="<?php echo self::$ALL_USERS; ?>" <?php echo $data->type === self::$ALL_USERS ? 'checked' : ''; ?> /><?php echo __('All Users', 'wpfront-user-role-editor'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo esc_attr($widget->get_field_name('user-restriction-type')); ?>" value="<?php echo self::$LOGGEDIN_USERS; ?>" <?php echo $data->type === self::$LOGGEDIN_USERS ? 'checked' : ''; ?> /><?php echo __('Logged in Users', 'wpfront-user-role-editor'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo esc_attr($widget->get_field_name('user-restriction-type')); ?>" value="<?php echo self::$GUEST_USERS; ?>" <?php echo $data->type === self::$GUEST_USERS ? 'checked' : ''; ?> /><?php echo __('Guest Users', 'wpfront-user-role-editor'); ?></label>
                    <label><input class="user-restriction-type" type="radio" name="<?php echo esc_attr($widget->get_field_name('user-restriction-type')); ?>" value="<?php echo self::$ROLE_USERS; ?>" <?php echo $data->type === self::$ROLE_USERS ? 'checked' : ''; ?> /><?php echo __('Users by Role', 'wpfront-user-role-editor'); ?></label>
                    <span class="roles-container <?php echo $data->type === self::$ROLE_USERS ? '' : 'hidden'; ?>">
                        <?php do_action('wp_widget_permissions_custom_fields_roles_list', $widget, $return, $instance); ?>
                    </span>
                    <?php if ($widget->number !== '__i__') { ?>
                        <span id="<?php echo esc_attr($widget->get_field_id('in-title-access-type')); ?>" class="in-title-access-type hidden">&nbsp;(<?php echo esc_html($this->get_title_text($data)); ?>)</span>
                    <?php } ?>
                </span>
            </p>
            <?php if ($widget->number !== '__i__') { ?>
                <script type="text/javascript">
                    jQuery(function () {
                        wpfront_ure_widget_permissions_update_widget_title(<?php echo json_encode($widget->get_field_id('in-title-access-type')); ?>);
                    });
                </script>
            <?php } ?>
            <?php
        }

        /**
         * Hooks into wp_widget_permissions_custom_fields_roles_list, to display roles list.
         * 
         * @param \WP_Widget $widget
         * @param null $return
         * @param array $instance
         */
        public function widget_permissions_custom_fields_roles_list($widget, $return, $instance) {
            printf(__('%s to limit based on roles.', 'wpfront-user-role-editor'), '<a target="_blank" href="https://wpfront.com/widgets">' . __('Upgrade to Pro', 'wpfront-user-role-editor') . '</a>');
        }

        /**
         * Hooks into widget_update_callback, sets the widgets permission settings.
         * 
         * @param array     $instance     The current widget instance's settings.
         * @param array     $new_instance Array of new widget settings.
         * @param array     $old_instance Array of old widget settings.
         * @param WP_Widget $this         The current widget instance.
         * @return object
         */
        public function widget_update_callback($instance, $new_instance, $old_instance, $widget) {
            if (!current_user_can(self::CAP)) {
                if (empty($old_instance[self::$META_DATA_KEY]))
                    $instance[self::$META_DATA_KEY] = (OBJECT) array('type' => self::$ALL_USERS);
                else
                    $instance[self::$META_DATA_KEY] = $old_instance[self::$META_DATA_KEY];
                return $instance;
            }

            $instance[self::$META_DATA_KEY] = $this->get_widget_post_data($instance, $new_instance, $old_instance, $widget);
            return $instance;
        }

        /**
         * Reads widgets permission settings from $_POST.
         * 
         * @param array     $instance     The current widget instance's settings.
         * @param array     $new_instance Array of new widget settings.
         * @param array     $old_instance Array of old widget settings.
         * @param WP_Widget $this         The current widget instance.
         * @return array
         */
        protected function get_widget_post_data($instance, $new_instance, $old_instance, $widget) {
            if (empty($new_instance['user-restriction-type'])) {
                return (OBJECT) array('type' => self::$ALL_USERS);
            } else {
                return (OBJECT) array('type' => intval($new_instance['user-restriction-type']));
            }
        }

        /**
         * Hooks into widget_display_callback, display logic.
         * @param array     $instance The current widget instance's settings.
         * @param WP_Widget $this     The current widget instance.
         * @param array     $args     An array of default widget arguments.
         * @return boolean
         */
        public function widget_display_callback($instance, $widget, $args) {
            $data = $this->get_meta_data($instance);

            if ($this->is_widget_enabled($data)) {
                return $instance;
            }

            return false;
        }

        /**
         * Checks whether widget is enabled for user.
         * 
         * @param object $data
         * @return boolean
         */
        protected function is_widget_enabled($data) {
            switch ($data->type) {
                case self::$LOGGEDIN_USERS:
                    return is_user_logged_in();

                case self::$GUEST_USERS:
                    return !is_user_logged_in();
            }

            return true;
        }

        /**
         * Returns the title text value.
         * 
         * @param object $data
         * @return string
         */
        protected function get_title_text($data) {
            switch ($data->type) {
                case self::$LOGGEDIN_USERS:
                    return __('Logged in Users', 'wpfront-user-role-editor');

                case self::$GUEST_USERS:
                    return __('Guest Users', 'wpfront-user-role-editor');

                case self::$ROLE_USERS:
                    return __('Users by Role', 'wpfront-user-role-editor');

                default:
                    return __('All Users', 'wpfront-user-role-editor');
            }
        }

        /**
         * Hooks on options class to display ui.
         * 
         * @param array $option_keys
         */
        public function wpfront_ure_options_register_ui_field($option_keys) {
            $option_keys[self::USE_OLD_WIDGETS_UI] = '';

            add_action('wpfront_ure_options_ui_field_use_old_widgets_ui_label', array($this, 'options_ui_label'), 10);
            add_action('wpfront_ure_options_ui_field_use_old_widgets_ui', array($this, 'options_ui_field'), 10, 1);
            add_action('wpfront_ure_options_ui_field_use_old_widgets_ui_update', array($this, 'options_ui_update'), 10, 1);
            add_action('wpfront_ure_options_ui_field_use_old_widgets_ui_help', array($this, 'options_ui_help'), 10);

            return $option_keys;
        }

        public function options_ui_label() {
            echo __('Use Old Widgets UI', 'wpfront-user-role-editor');
        }

        public function options_ui_field($key) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $checked = !empty($_POST[$key]);
            } else {
                if (is_network_admin()) {
                    $checked = Options::instance()->get_network_option_boolean($key);
                } else {
                    $checked = $this->Options->get_option_boolean($key, true);
                }
            }

            $checked = $checked ? 'checked' : '';

            echo "<input type='checkbox' name='$key' $checked />";
        }

        public function options_ui_update($key) {
            $value = !empty($_POST[$key]);

            if (is_network_admin()) {
                Options::instance()->set_network_option($key, $value);
            } else {
                Options::instance()->set_option($key, $value);
            }
        }

        public function options_ui_help() {
            return '<strong>' . __('Use Old Widgets UI', 'wpfront-user-role-editor') . '</strong>: ' . __('Forces WordPress to use Widgets UI prior to version 5.8.', 'wpfront-user-role-editor');
        }

        public function enqueue_widget_scripts() {
            wp_enqueue_script('jquery');
            wp_enqueue_script('wpfront-user-role-editor-widget-permissions-js', WPFURE::instance()->get_asset_url('js/widget-permissions.js'), array('jquery'), WPFURE::VERSION);
        }

        public function enqueue_widget_styles() {
            wp_enqueue_style('wpfront-user-role-editor-widget-permissions-css', WPFURE::instance()->get_asset_url('css/widget-permissions.css'), array(), WPFURE::VERSION);
        }

        public static function uninstall() {
            global $wp_registered_widgets;

            $sidebars_widgets = wp_get_sidebars_widgets();

            foreach ($sidebars_widgets as $sidebar => $widget_ids) {
                foreach ($widget_ids as $id) {
                    if (empty($wp_registered_widgets[$id])) {
                        continue;
                    }

                    $callback = $wp_registered_widgets[$id]['callback'];
                    $widget = $callback[0];
                    $instances = $widget->get_settings();

                    $update = false;
                    foreach ($instances as $index => $instance) {
                        if (isset($instance[self::$META_DATA_KEY])) {
                            unset($instance[self::$META_DATA_KEY]);
                            $update = true;
                        }
                        $instances[$index] = $instance;
                    }

                    if ($update) {
                        $widget->save_settings($instances);
                    }
                }
            }
        }

        public static function get_debug_setting() {
            return array('key' => 'widget-permissions', 'label' => __('Widget Permissions', 'wpfront-user-role-editor'), 'position' => 170, 'description' => __('Disables widget permissions functionality.', 'wpfront-user-role-editor'));
        }
        
    }

    \WPFront\URE\WPFront_User_Role_Editor_Uninstall::register_callback('\WPFront\URE\Widget\WPFront_User_Role_Editor_Widget_Permissions::uninstall');

    WPFront_User_Role_Editor_Widget_Permissions::load();
}