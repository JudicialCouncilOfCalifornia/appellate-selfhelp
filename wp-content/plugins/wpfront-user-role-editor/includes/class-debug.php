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
 * Controller for WPFront User Role Editor Debug
 * @author Jinu Varghese
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

use \WPFront\URE\Options\iWPFront_User_Role_Editor_Settings_Controller;

/**
 * Debug controller
 *
 * @author Jinu Varghese
 * @copyright 2014 WPFront.com
 */
if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_Debug')) {

    class WPFront_User_Role_Editor_Debug implements iWPFront_User_Role_Editor_Settings_Controller {

        protected static $instance = null;
        protected $debug_keys = array();
        protected $settings_key = 'debug-settings';
        protected $settings_obj = null;
        protected $options;
        protected $debug_enabled = null;

        protected function __construct(){
        }

        public static function instance() {
            if (self::$instance === null) {
                self::$instance = new WPFront_User_Role_Editor_Debug();
            }

            return self::$instance;
        }

        public static function init() {
            if (self::$instance->debug_enabled() == false) {
                return;
            }

            add_filter('wpfront_ure_settings_controllers', array(self::instance(), 'register_settings'), 10);
        }

        public function register_settings($controllers) {
            if (is_multisite() && !is_network_admin()) {
                return $controllers;
            }

            if (current_user_can('manage_options')) {
                $controllers[] = self::instance();
            }

            return $controllers;
        }

        public function getKey() {
            return 'debug';
        }

        public function getTitle() {
            return __('Debug', 'wpfront-user-role-editor');
        }

        public function view_callback() {
            $this->view_settings();
        }

        public function load_view_callback($parent) {
            if (!empty($_POST['submit'])) {
                $settings_obj = new \stdClass();
                $settings = empty($_POST[$this->settings_key]) ? [] : $_POST[$this->settings_key];
                foreach ($this->debug_keys as $position => $setting) {
                    $key = $setting['key'];
                    if (!empty($settings[$key])) {
                        $settings_obj->$key = true;
                    }
                }

                $this->get_options_obj()->set_network_option($this->settings_key, $settings_obj, '', false);

                if (wp_safe_redirect($parent->getControllerUrl($this) . '&changes-saved=true')) {
                    exit();
                }
            }

            $this->set_help_tab();
        }

        protected function set_help_tab() {
            ksort($this->debug_keys);

            $help = '';
            foreach ($this->debug_keys as $position => $setting) {
                $label = $setting['label'];
                $description = $setting['description'];
                $h = sprintf(__('<strong>Disable %s</strong>: %s', 'wpfront-user-role-editor'), $label, $description);
                $help .= "<p>$h</p>";
            }

            $tabs = array(
                array(
                    'id' => 'overview',
                    'title' => __('Overview', 'wpfront-user-role-editor'),
                    'content' => $help
                )
            );

            $sidebar = array(
                array(
                    __('Documentation on Debug', 'wpfront-user-role-editor'),
                    'debug/'
                )
            );

            \WPFront\URE\WPFront_User_Role_Editor_Utils::set_help_tab($tabs, $sidebar);
        }

        public function display_notices() {
            if ((isset($_GET['changes-saved']) && $_GET['changes-saved'] == 'true')) {
                \WPFront\URE\WPFront_User_Role_Editor_Utils::notice_updated(__('Debug settings saved.', 'wpfront-user-role-editor'));
            }
        }

        protected function view_settings() {
            ?>
            <div id="wpfront-user-role-editor-debug">
                <table class="form-table">
                    <?php $this->display_rows(); ?>
                </table>
            </div>
            <?php
        }

        protected function display_rows() {
            ksort($this->debug_keys);

            foreach ($this->debug_keys as $position => $setting) {
                $key = $setting['key'];
                $label = $setting['label'];
                $label = sprintf(__('Disable %s', 'wpfront-user-role-editor'), $label);

                $disabled = $this->is_disabled($key);
                $checked = $disabled ? 'checked' : '';
                ?>
                <tr data-priority="<?php echo esc_attr($position); ?>">
                    <th scope="row">
                        <?php echo esc_html($label); ?>
                    </th>
                    <td>
                        <input type="checkbox" name="<?php echo esc_attr("{$this->settings_key}[$key]"); ?>" <?php echo $checked; ?> />
                    </td>
                </tr>
                <?php
            }
        }

        public function add_setting($setting, $label = null, $position = null, $description = null) {
            if ($this->debug_enabled() == false) {
                return false;
            }

            if (is_array($setting)) {
                $position = $setting['position'];
            } else {
                $setting = array('key' => $setting, 'label' => $label, 'description' => $description);
            }

            if (isset($this->debug_keys[$position])) {
                $old = $this->debug_keys[$position]['key'];
                $new = $setting['key'];
                if($old != $new) {
                    throw new \Exception("Debug $position is already defined - $old/$new");
                }
            }
            
            $this->debug_keys[$position] = $setting;

            return true;
        }

        public function is_disabled($key) {
            if ($this->debug_enabled() == false) {
                return;
            }

            $obj = $this->get_settings_obj();
            return !empty($obj->$key);
        }

        protected function get_options_obj() {
            if (empty($this->options)) {
                $this->options = \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance();
            }

            return $this->options;
        }

        protected function get_settings_obj() {
            if ($this->settings_obj === null) {
                $this->settings_obj = $this->get_options_obj()->get_network_option($this->settings_key, '');
            }

            return $this->settings_obj;
        }

        public function debug_enabled() {
            if($this->debug_enabled !== null) {
                return $this->debug_enabled;
            }
            
            $this->debug_enabled = false;
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                $this->debug_enabled = true;
            }

            if (defined('WPF_DEBUG') && WPF_DEBUG) {
                $this->debug_enabled = true;
            }

            return $this->debug_enabled;
        }

    }

    add_action('wpfront_ure_init', array(WPFront_User_Role_Editor_Debug::class, 'init'));
}


