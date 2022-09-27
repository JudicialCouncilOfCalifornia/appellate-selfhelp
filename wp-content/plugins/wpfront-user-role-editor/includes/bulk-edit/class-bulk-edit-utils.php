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
 * Utilities for Bulk Edit 
 *
 * @author Jinu Varghese
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Bulk_Edit;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;

if (!class_exists('\WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit_Utils')) {

    /**
     * Bulk Edit Utils class
     *
     * @author Jinu Varghese
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Bulk_Edit_Utils {
        
        public static function users_list_view() {
            ?>
            <p>
                <label><input name="select-users" type="radio" value="all" checked="checked" /><?php echo __('All Users', 'wpfront-user-role-editor'); ?></label>
            </p>
            <p>
                <label><input name="select-users" type="radio" value="selected" /><?php echo __('Selected Users', 'wpfront-user-role-editor'); ?></label>
            </p>
            <p class="hidden loading-image">
                <img src="<?php echo URE::instance()->get_asset_url('images/loading.gif'); ?>" />
            </p>
            <p id="users-container" class="hidden"></p>
            <input type="hidden" name="selected-users" />
            <input type="hidden" id="current-user-id" value="<?php echo get_current_user_id() ?>"/>
            <script type="text/javascript">
                (function ($) {
                    var $container = $('#users-container');
                    $container.closest('form').prop('id', 'users-filter');

                    $("input[name='select-users']").on('change', function () {
                        var $this = $(this);
                        if ($this.val() === "selected") {
                            $container.removeClass("hidden");
                        } else {
                            $container.addClass("hidden");
                        }
                    });

                    $container.on('click', "td.column-username a", function (e) {
                        e.preventDefault();
                        e.stopImmediatePropagation();

                        var $this = $(this);
                        var $checkbox = $this.closest('td').prev().find('[type=checkbox]');
                        $checkbox.prop("checked", !$checkbox.prop("checked"));

                        setSelectedUsers();

                        return false;
                    });

                    $container.on('change', "#cb-select-all-1, #cb-select-all-2, tbody th:first-child input[type='checkbox']", function () {
                        setSelectedUsers();
                    });

                    function setSelectedUsers() {
                        var $current = $container.next().val();
                        if ($current === "") {
                            $current = ",";
                        }

                        $container.find("tbody th:first-child input[type='checkbox']").each(function () {
                            var $this = $(this);
                            if ($this.prop("checked")) {
                                if ($current.indexOf("," + $this.val() + ",") === -1) {
                                    $current = $current + $this.val() + ",";
                                }
                            } else {
                                $current = $current.replace("," + $this.val() + ",", ",");
                            }
                        });

                        $container.next().val($.trim($current));
                    }

                    function getParam(href, name) {
                        href = href.split('?')
                        if (href.length > 1) {
                            href = href[1];
                            var parts = href.split('&');
                            for (var i = 0; i < parts.length; i++) {
                                var s = parts[i].split('=');
                                if (s[0] === name) {
                                    return s[1];
                                }
                            }
                        }

                        return '';
                    }

                    var role = '';
                    var search = '';
                    var orderby = '';
                    var order = '';
                    var paged = '';

                    $container.on('click', 'a, #search-submit', function (e) {
                        e.preventDefault();

                        var $this = $(this);
                        var href = $this.prop('href');

                        if ($this.is('ul.subsubsub a')) {
                            role = getParam(href, 'role');
                            search = '';
                            orderby = '';
                            order = '';
                            paged = '';
                        }

                        if ($this.is('th.column-username a')) {
                            orderby = getParam(href, 'orderby');
                            order = getParam(href, 'order');
                            paged = '';
                        }

                        if ($this.is('.pagination-links a')) {
                            paged = getParam(href, 'paged');
                        }

                        if ($this.is('#search-submit')) {
                            search = $('#user-search-input').val();
                            paged = '';
                        }

                        load_users();
                        return false;
                    });


                    function load_users() {
                        $container.html("").append($container.prev().clone().removeClass('hidden'));

                        var data = {
                            'role': role,
                            's': search,
                            'orderby': orderby,
                            'order': order,
                            'paged': paged
                        };

                        for (var m in data) {
                            if (data[m] === '') {
                                delete data[m];
                            }
                        }

                        $.extend(data, {
                            "action": "wpfront_ure_bulk_edit_user_post_permissions_users_table",
                            "nonce": "<?php echo wp_create_nonce('users-table'); ?>"
                        });

                        $.post(ajaxurl, data, function (response) {
                            $container.html(response);

                            var current_user_id = $("#current-user-id").val();
                            $container.find("input[type='checkbox'][value='" + current_user_id + "']").remove();

                            $container.find("td.column-username a").prop("href", "#");
                            $container.find("div.actions").remove();
                            $container.find("td.email.column-email > a").contents().unwrap();
                            $container.find("th.column-email > a").contents().unwrap();
                            $container.find("td.column-posts > a").contents().unwrap();

                            $container.find("tbody th:first-child input[type='checkbox']").each(function () {
                                var $this = $(this);
                                var $current = "," + $container.next().val() + ",";
                                $this.prop("checked", $current.indexOf("," + $this.val() + ",") !== -1);
                            });
                        });
                    }

                    load_users();

                })(jQuery);
            </script>
            <?php
        }

        /**
         * Returns an array of user ids
         * 
         * @return int[]
         */
        public static function get_user_ids($index, $process_records, $include_current_user = false) {
            $query = array();
            
            if(!$include_current_user){
                $current_user_id = get_current_user_id();
                $query['exclude'] = $current_user_id;
            }
            
            $query['orderby'] = 'ID';
            $query['order'] = 'ASC';
            $query['offset'] = $index;
            $query['number'] = $process_records;
            $query['fields'] = 'ID';

            $wp_query = new \WP_User_Query($query);
            $users = $wp_query->get_results();

            if (empty($users)) {
                return [];
            }

            return $users;
        }
        
        public static function get_post_ids($post_type, $post_id_index, $process_records) {
            $query = array();

            $query['orderby'] = 'ID';

            if ($post_id_index !== null) {
                $query['offset'] = $post_id_index;
            } else {
                $query['posts_per_page'] = -1;
            }

            if ($process_records !== null) {
                $query['posts_per_page'] = $process_records;
            }

            $query['fields'] = 'ids';
            $query['post_type'] = $post_type;
            $query['post_status'] = 'any';

            $wp_query = new \WP_Query($query);
            $post_ids = $wp_query->posts;

            return $post_ids;
        }

        
        /**
         * Returns the user ids to edit.
         * 
         * @return string[] all|array
         */
        public static function get_current_selected_users() { 
            if (empty($_POST['select-users'])) {
                return null;
            }
            $select_users = $_POST['select-users'];

            if ($select_users === 'all') {
                return 'all';
            } else {
                if (empty($_POST['selected-users'])) {
                    return null;
                }
                $selected_users = $_POST['selected-users'];
                $selected_users = trim($selected_users);
                $selected_users = trim($selected_users, ',');
                if (empty($selected_users)) {
                    return null;
                }

                return explode(',', $selected_users);
            }
        }
        
        /**
         * Returns current selected users count.
         * 
         * @return int
         */
        public static function get_current_selected_users_count() { 
            $selected_users = self::get_current_selected_users();

            if ($selected_users === 'all') {
                $count = count_users();
                return $count['total_users'] - 1;
            }

            return count($selected_users);
        }

        /**
         * Returns the user table on selected users.
         */
        public static function users_table_callback() { 
            check_ajax_referer('users-table', 'nonce');

            $GLOBALS['hook_suffix'] = '';

            $screen = \WP_Screen::get();
            $screen->id = 'users';
            \WP_Screen::get($screen)->set_current_screen();
            
            add_filter('user_row_actions', array(WPFront_User_Role_Editor_Bulk_Edit_Utils::class ,'users_table_row_actions'), PHP_INT_MAX);
            add_filter('bulk_actions-users', array(WPFront_User_Role_Editor_Bulk_Edit_Utils::class, 'users_table_bulk_actions'), PHP_INT_MAX);

            if (isset($_POST['orderby'])) {
                $_GET['orderby'] = $_POST['orderby'];
            }

            if (isset($_POST['order'])) {
                $_GET['order'] = $_POST['order'];
            }

            $wp_list_table = _get_list_table('WP_Users_List_Table');
            $wp_list_table->prepare_items();
            $wp_list_table->views();
            $wp_list_table->search_box(__('Search Users'), 'user');
            $wp_list_table->display();

            die();
        }
        
        public static function users_table_bulk_actions() {   
            return array();
        }

        public static function users_table_row_actions() {     
            return array();
        }
    }

}