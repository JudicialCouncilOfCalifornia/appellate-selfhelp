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
 * Utilities for WPFront User Role Editor
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

require_once dirname(__FILE__) . '/globals.php';

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_Utils')) {

    /**
     * Utils class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Utils {
        
        /**
         * Enqueues font-awesome css.
         */
        public static function enqueue_font_awesome_styles() {
            wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0');
        }
        
        /**
         * Sets the help tab of current screen. Returns whether it was successful.
         * 
         * @param array $tabs
         * @param array $sidebar
         * @return boolean
         */
        public static function set_help_tab($tabs, $sidebar) {
            $screen = get_current_screen();
            
            if(empty($screen)) {
                return false;
            }

            if(!empty($tabs)) {
                foreach ($tabs as $value) {
                    $screen->add_help_tab($value);
                }
            }

            if (!empty($sidebar)) {
                $s = '<p><strong>' . __('Links:', 'wpfront-user-role-editor') . '</strong></p>';

                foreach ($sidebar as $value) {
                    $s .= '<p><a target="_blank" href="https://wpfront.com/user-role-editor-pro/' . $value[1] . '">' . $value[0] . '</a></p>';
                }

                $s .= '<p><a target="_blank" href="https://wpfront.com/user-role-editor-pro/faq/">' . __('FAQ', 'wpfront-user-role-editor') . '</a></p>';
                $s .= '<p><a target="_blank" href="https://wpfront.com/support/">' . __('Support', 'wpfront-user-role-editor') . '</a></p>';
                $s .= '<p><a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/wpfront-user-role-editor">' . __('Review', 'wpfront-user-role-editor') . '</a></p>';
                $s .= '<p><a target="_blank" href="https://wpfront.com/contact/">' . __('Contact', 'wpfront-user-role-editor') . '</a></p>';

                $screen->set_help_sidebar($s);
            }
            
            return true;
        }
        
        /**
         * Returns whether current action is AJAX.
         * 
         * @return boolean
         */
        public static function doing_ajax() {
            if (defined('DOING_AJAX') && DOING_AJAX) {
                return true;
            }

            if (!empty($_SERVER['PHP_SELF']) && $_SERVER['PHP_SELF'] === '/wp-admin/admin-ajax.php') {
                return true;
            }

            if (!empty($_SERVER['DOING_AJAX']) && $_SERVER['DOING_AJAX'] === '/wp-admin/admin-ajax.php') {
                return true;
            }
            
            if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {
                return true;
            }

//            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
//                return true;
//            }
            
            return false;
        }
        
        public static function network_ajax_url() {
            return admin_url('admin-ajax.php?network=1');
        }
        
        public static function doing_network_ajax() {
            return self::doing_ajax() && !empty($_GET['network']);
        }
        
        public static function notice_error($message) {
            ?>
            <div class="error notice is-dismissible">
                <p>
                    <strong><?php echo $message; ?></strong>
                </p>
            </div>
            <?php
        }
        
        public static function notice_updated($message) {
            ?>
            <div class="updated notice is-dismissible">
                <p>
                    <strong><?php echo $message; ?></strong>
                </p>
            </div>
            <?php
        }
    }
    
}