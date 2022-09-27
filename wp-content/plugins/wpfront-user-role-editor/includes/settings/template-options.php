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
 * Template for WPFront User Role Editor Options
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Options;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront\URE\Roles\WPFront_User_Role_Editor_Options_View')) {

    class WPFront_User_Role_Editor_Options_View extends \WPFront\URE\WPFront_User_Role_Editor_View {

        /**
         *
         * @var string[] key => group.
         */
        protected $option_keys;

        /**
         * 
         * @param iWPFront_User_Role_Editor_Settings_Controller[] $controllers
         * @param iWPFront_User_Role_Editor_Settings_Controller $current
         */
        public function view($parent, $controllers, $current) {
            ?>
            <div class="wrap">
                <?php $this->title(__('WPFront User Role Editor Settings', 'wpfront-user-role-editor')); ?>
                <?php $current->display_notices(); ?>
                <nav class="nav-tab-wrapper" aria-label="Tabbed Menu">
                    <?php 
                    foreach ($controllers as $ctrl) {
                        $class = 'nav-tab';
                        $url = $parent->getControllerUrl($ctrl);
                        if($ctrl->getKey() === $current->getKey()) {
                            $class .= ' nav-tab-active';
                            $url = '#!';
                        }
                        ?>
                        <a class="<?php echo esc_attr($class); ?>" href="<?php echo esc_attr($url); ?>"><?php echo esc_html($ctrl->getTitle()); ?></a>
                        <?php 
                    } 
                    ?>
                </nav>
                <div>
                    <form method="post" action="<?php echo esc_attr($parent->getControllerUrl($current)); ?>">
                        <?php wp_nonce_field('save-settings'); ?>
                        <div class="inside">
                            <?php echo $current->view_callback(); ?>
                            <?php submit_button(); ?>
                        </div>
                    </form>
                </div>
            </div>
            <?php
        }

        public function view_settings($option_keys) {
            $this->option_keys = $option_keys;
            ?>
            <div id="wpfront-user-role-editor-options">
                <table  class="form-table">
                    <?php $this->display_rows(); ?>
                </table>
            </div>
            <?php
        }

        public function display_notices() {
            if ((isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true')) {
                $this->UtilsClass::notice_updated(__('Settings updated.', 'wpfront-user-role-editor'));
            }
        }

        protected function display_rows() {
            foreach ($this->option_keys as $key => $group) {
                ?>
                <tr>
                    <th scope="row">
                        <?php do_action('wpfront_ure_options_ui_field_' . $key . '_label', $key); ?>
                    </th>
                    <td>
                        <?php do_action('wpfront_ure_options_ui_field_' . $key, $key); ?>
                    </td>
                </tr>
                <?php
            }
        }

    }

}