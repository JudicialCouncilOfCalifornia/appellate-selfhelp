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
 * Template for WPFront User Role Editor Assign
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Assign_Migrate;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_Migrate as AssignMigrate;

if (!class_exists('WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_View')) {

    class WPFront_User_Role_Editor_Assign_View extends WPFront_User_Role_Editor_Assign_Migrate_View {

        private $current_user_id = null;

        public function view() {
            ?>
            <div class="wrap assign-roles">
                <?php
                $this->title();
                $this->display_notices();
                ?>
                <form method="post" action="<?php echo esc_attr(AssignMigrate::instance()->get_self_url()); ?>">
                    <?php
                    $this->form_table();
                    wp_nonce_field('assign-roles');
                    $this->assign_button();
                    ?>
                </form>
                <?php
                $this->scripts();
                ?>
            </div>
            <?php
        }

        protected function title() {
            ?>
            <h2>
                <?php echo __('Assign Roles', 'wpfront-user-role-editor'); ?>
            </h2>
            <?php
        }

        protected function display_notices() {
            $error = AssignMigrate::instance()->get_error_string();
            if (!empty($error) && !empty($_POST['assign'])) {
                Utils::notice_error($error);
            } elseif (!empty($_GET['roles-assigned'])) {
                Utils::notice_updated(__('Roles updated successfully.', 'wpfront-user-role-editor'));
            }
        }

        protected function form_table() {
            ?>
            <table class="form-table">
                <tbody>
                    <?php $this->user_list_row(); ?>
                    <?php $this->primary_role_row(AssignMigrate::instance()->get_assign_roles_primary_roles()); ?>
                    <?php $this->secondary_roles_row(AssignMigrate::instance()->get_assign_roles_secondary_roles()); ?>
                </tbody>
            </table>
            <?php
        }

        protected function user_list_row() {
            ?>
            <tr>
                <th scope="row">
                    <?php echo __('User', 'wpfront-user-role-editor'); ?>
                </th>
                <td>
                    <?php $this->user_list_autocomplete(); ?>
                </td>
            </tr>
            <?php
        }

        protected function user_list_autocomplete() {
            if (!empty($_GET['user'])) {
                $user_id = $_GET['user'];
                $user = get_userdata($user_id);
                $value = $user->display_name . '<' . $user->user_email . '>';
            } else {
                $value = '';
                $user_id = '';
            }
            ?>

            <input type="text" id="assign-users-list" name="assign-user" placeholder="<?php echo __('Search User...', 'wpfront-user-role-editor'); ?>" value="<?php echo esc_attr($value); ?>">
            <input type="hidden" id="assign-users-list-hidden" name="assign-user-id" value="<?php echo esc_attr($user_id); ?>">

            <?php
        }

        protected function assign_button() {
            submit_button(__('Assign Roles'), 'primary', 'assign');
        }

        protected function get_current_user_id() {
            if ($this->current_user_id === null) {
                if (!empty($_POST['assign-user'])) {
                    $this->current_user_id = $_POST['assign-user'];
                    return $this->current_user_id;
                }

                if (!empty($_GET['user'])) {
                    $this->current_user_id = $_GET['user'];
                    return $this->current_user_id;
                }

                $users = AssignMigrate::instance()->get_users();
                if (!empty($users)) {
                    $this->current_user_id = $users[0]->ID;
                    return $this->current_user_id;
                }
            }

            return $this->current_user_id;
        }

        protected function get_current_primary_role() {
            if (!empty($_POST['primary-role'])) {
                return $_POST['primary-role'];
            }

            $user_id = $this->get_current_user_id();
            $user = get_userdata($user_id);

            if (!empty($user) && !empty($user->roles)) {
                $roles = $user->roles;
                return reset($roles);
            }

            return '';
        }

        protected function get_current_secondary_roles() {
            if (!empty($_POST['assign'])) {
                if (empty($_POST['secondary-roles'])) {
                    return array();
                }

                return array_keys($_POST['secondary-roles']);
            }

            $user_id = $this->get_current_user_id();
            $user = get_userdata($user_id);

            if (!empty($user) && !empty($user->roles)) {
                $roles = array_values($user->roles);
                array_shift($roles);
                return $roles;
            }

            return array();
        }

        protected function scripts() {
            ?>
            <script type="text/javascript">
                (function ($) {

                    $(function () {
                        $("#assign-users-list").autocomplete({
                            source: function (request, response) {
                                $.ajax({
                                    url: ajaxurl,
                                    dataType: "json",
                                    type: "POST",
                                    data: {
                                        action: "wpfront_user_role_editor_assign_roles_user_autocomplete",
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
                                var page_url = <?php echo json_encode(AssignMigrate::instance()->get_self_url() . '&user=') ?>;
                                window.location.replace(page_url + ui.item.value);
                                return false;
                            },
                            change: function (event, ui) {
                                if (!ui.item) {
                                    $(this).val('');
                                }
                            },
                            minLength: 3,
                        });
                    });

                })(jQuery);
            </script>

            <?php
            parent::scripts();
        }

    }

}

