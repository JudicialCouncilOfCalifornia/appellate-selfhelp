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
 * Controller for WPFront User Role Editor Login Page Url.
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\WP;

if (!defined('ABSPATH')) {
    exit();
}

use \WPFront\URE\Options\WPFront_User_Role_Editor_Options as Options;
use \WPFront\URE\WPFront_User_Role_Editor_Debug;

if (!class_exists('\WPFront\URE\WP\WPFront_User_Role_Editor_Login_Page_Url')) {

    /**
     * Login_Page_Url class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Login_Page_Url {

        protected static $instance = null;

        /**
         * Singleton instance.
         * 
         * @return WPFront_User_Role_Editor_Login_Page_Url
         */
        public static function instance() {
            if (self::$instance === null) {
                self::$instance = new WPFront_User_Role_Editor_Login_Page_Url ();
            }

            return self::$instance;
        }

        /**
         * Hooks into wpfront_ure_init.
         */
        public static function init() {
            $debug = WPFront_User_Role_Editor_Debug::instance();
            $debug->add_setting('login_page_url', __('Login Page URL', 'wpfront-user-role-editor'), 220, __('Disables Login Page URL functionality.', 'wpfront-user-role-editor'));

            if ($debug->is_disabled('login_page_url')) {
                return;
            }

            $instance = self::instance();

            add_action('admin_init', array($instance, 'admin_init'));
            add_filter('wpfront_ure_options_register_ui_field', array($instance, 'wpfront_ure_options_register_ui_field'), 50, 1);
            /* Set custom login page */
            add_filter('login_url', array($instance, 'login_page_url_callback'), PHP_INT_MAX, 1);
        }

        /**
         * Adds ajax functions on admin_init
         */
        public function admin_init() {
            add_action('wp_ajax_wpfront_user_role_editor_login_page_url_autocomplete', array(self::instance(), 'autocomplete_callback'));
        }

        /**
         * Hooks on options class to display ui.
         * 
         * @param array $option_keys
         */
        public function wpfront_ure_options_register_ui_field($option_keys) {
            $option_keys['login_page_url'] = '';

            add_action('wpfront_ure_options_ui_field_login_page_url_label', array($this, 'options_ui_label'));
            add_action('wpfront_ure_options_ui_field_login_page_url', array($this, 'options_ui_field'));
            add_action('wpfront_ure_options_ui_field_login_page_url_update', array($this, 'login_page_url_options_ui_update'));
            add_action('wpfront_ure_options_ui_field_login_page_url_help', array($this, 'options_ui_help'));

            return $option_keys;
        }

        public function options_ui_label() {
            echo __('Login Page URL', 'wpfront-user-role-editor');
        }

        public function options_ui_field() {
            $key = 'login_page_url';
            $id = '';
            $title = '';

            $post_data = $this->login_page_url();
            if (!empty($post_data)) {
                if (!empty(intval($post_data))) {
                    $p = get_post($post_data);
                    if (!empty($p->ID) && $p->post_status == 'publish') {
                        $id = $p->ID;
                        $title = $p->post_title;
                    }
                } else {
                    $title = $post_data;
                }
            }

            $title = esc_attr($title);
            echo "<input type='text' name='$key' id='login_page_url' value='$title' class='regular-text' >";
            /* hidden field to store selected post type id */
            echo "<input type='hidden' name='login_page_url_selected_post_id' id='login_page_url_selected_post_id' value='$id' >";
            ?>
            <script type="text/javascript">
                (function ($) {

                    $(function () {
                        $("#login_page_url").autocomplete({
                            source: function (request, response) {
                                $.ajax({
                                    url: ajaxurl,
                                    dataType: "json",
                                    type: "POST",
                                    data: {
                                        action: "wpfront_user_role_editor_login_page_url_autocomplete",
                                        term: request.term
                                    },
                                    success: function (data) {
                                        response(data);
                                    },
                                    error: function (response) {
                                        console.log(response.responseText);
                                    },
                                });
                            },
                            select: function (event, ui) {
                                $(this).val(ui.item.label);
                                $("#login_page_url_selected_post_id").val(ui.item.value);
                                return false;
                            },
                            change: function (event, ui) {
                                if (!ui.item) {
                                    $("#login_page_url_selected_post_id").val('');
                                }
                            },
                            minLength: 2,
                        });
                    });

                })(jQuery);
            </script>

            <?php

        }

        public function autocomplete_callback() {
            $search_string = $_REQUEST['term'];

            $args = array(
                's' => $search_string,
                'post_type' => 'any',
                'post_status' => 'publish',
                'posts_per_page' => 10
            );
            $post_found = get_posts($args);

            $post_title = array();
            foreach ($post_found as $posts) {
                $post_title[] = array(
                    "label" => $posts->post_title,
                    "value" => $posts->ID
                );
            }

            echo json_encode($post_title);
            exit();
        }

        public function login_page_url_options_ui_update() {
            $key = 'login_page_url';

            if (!empty($_POST['login_page_url_selected_post_id'])) {
                $value = intval($_POST['login_page_url_selected_post_id']);
            } else {
                $value = $_POST[$key];
            }

            Options::instance()->set_option($key, $value);
        }

        public function options_ui_help() {
            return '<strong>' . __('Login Page URL', 'wpfront-user-role-editor') . '</strong>: ' . __('Select the login page or enter a URL to set the login page of your site.', 'wpfront-user-role-editor');
        }

        /**
         * Returns id or url.
         * 
         * @return int/string
         */
        public function login_page_url() {
            return Options::instance()->get_option('login_page_url');
        }

        /**
         * Returns login url.
         * 
         * @return string 
         */
        public function login_page_url_callback($login_url) {
            $post_data = $this->login_page_url();

            if (empty($post_data)) {
                return $login_url;
            }

            if (!empty(intval($post_data))) {
                $post = get_post($post_data);
                if (empty($post->ID) || $post->post_status != 'publish') {
                    return $login_url;
                }

                return get_permalink($post);
            }

            return $post_data;
        }

    }

    add_action('wpfront_ure_init', array(WPFront_User_Role_Editor_Login_Page_Url::class, 'init'));
}
