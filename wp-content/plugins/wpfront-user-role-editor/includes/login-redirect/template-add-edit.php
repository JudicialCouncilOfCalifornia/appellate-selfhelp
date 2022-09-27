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
 * Template for WPFront User Role Editor Login Redirect Add Edit
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Login_Redirect;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect as LoginRedirect;

if(!class_exists('WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect_Add_Edit_View')) {
    
    class WPFront_User_Role_Editor_Login_Redirect_Add_Edit_View {
        
        /**
         *
         * @var WPFront_User_Role_Editor_Login_Redirect_Entity 
         */
        private $role_entity;
        
        public function __construct($entity = null) {
            $this->role_entity = $entity;
        }
        
        public function view() {
            ?>
            <div class="wrap login-redirect-add-edit">
                <?php $this->title(); ?>
                <?php $this->display_notices(); ?>
                <form method="post" class="validate" action="<?php echo $this->get_form_action_url(); ?>">
                    <table class="form-table">
                        <tbody>
                            <?php $this->role_row(); ?>
                            <?php $this->priority_row(); ?>
                            <?php $this->login_redirect_url_row(); ?>
                            <?php $this->logout_redirect_url_row(); ?>
                            <?php $this->deny_wp_admin_row(); ?>
                            <?php $this->disable_toolbar_row(); ?>
                        </tbody>
                    </table>
                    <?php wp_nonce_field('add-edit-login-redirect'); ?>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
            $this->scripts();
        }
        
        protected function title() {
            if(empty($this->role_entity)) {
                ?>
                <h2>
                    <?php echo __('Add New Login Redirect', 'wpfront-user-role-editor'); ?>
                    <p><?php echo __('Enter the URL where the user will be redirected after login or on wp-admin access.', 'wpfront-user-role-editor'); ?></p>
                </h2>
                <div id="login-redirect-custom-role-disabled" class="error below-h2 hidden">
                    <p><?php echo __('Custom roles not supported in free version.', 'wpfront-user-role-editor') . ' ' . sprintf('<a target="_blank" href="https://wpfront.com/lgnred">%s</a>', __('Upgrade to Pro.', 'wpfront-user-role-editor')); ?></p>
                </div>
                <?php
            } else {
                ?>
                <h2>
                    <?php echo __('Edit Login Redirect', 'wpfront-user-role-editor'); ?>
                </h2>
                <?php
            }
        }
        
        protected function display_notices() {
            if ((isset($_GET['changes-saved']) && $_GET['changes-saved'] == 'true')) {
                Utils::notice_updated(__('Changes saved.', 'wpfront-user-role-editor'));
            }
        }
        
        protected function get_form_action_url() {
            if(empty($this->role_entity)) {
                return LoginRedirect::instance()->get_add_new_url();
            } else {
                return LoginRedirect::instance()->get_edit_url($this->role_entity->role);
            }
        }

        protected function role_row() {
            ?>
            <tr class="form-required <?php echo $this->is_role_valid() ? '' : 'form-invalid' ?>"">
                <th scope="row">
                    <?php echo __('Role', 'wpfront-user-role-editor'); ?><span class="description"> (<?php echo __('required', 'wpfront-user-role-editor'); ?>)</span>
                </th>
                <td>
                    <?php
                    if(empty($this->role_entity)) {
                        ?>
                        <select name="role" id="login-redirect-role">
                            <?php
                            $roles = $this->get_roles();
                            $current_role = $this->get_current_role();
                            foreach ($roles as $name => $role) {
                                $selected = '';
                                if($name == $current_role) {
                                    $selected = 'selected';
                                }
                                echo "<option value='".esc_attr($name)."' data-allowed='".esc_attr($role->allowed)."' $selected>".esc_html($role->display_name)."</option>";
                            }
                            ?>
                        </select>
                        <?php
                    } else {
                        ?>
                        <select name="role" id="login-redirect-role" disabled="true">
                            <?php
                            $display_name = LoginRedirect::instance()->get_role_display($this->role_entity->role);
                            echo "<option>".esc_html($display_name)."</option>";
                            ?>
                        </select>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
        
        protected function priority_row() {
            ?>
            <tr class="form-required <?php echo $this->is_priority_valid() ? '' : 'form-invalid' ?>">
                <th scope="row">
                    <?php echo __('Priority', 'wpfront-user-role-editor'); ?><span class="description"> (<?php echo __('required', 'wpfront-user-role-editor'); ?>)</span>
                </th>
                <td>
                    <input id="login-redirect-priority" class="small-text" name="priority" type="number" value="<?php echo esc_attr($this->get_current_priority()); ?>" aria-required="true"  />
                </td>
            </tr>
            <?php
        }
        
        protected function login_redirect_url_row() {
            ?>
            <tr class="form-required <?php echo $this->is_url_valid() ? '' : 'form-invalid' ?>">
                <th scope="row">
                    <?php echo __('Login Redirect URL', 'wpfront-user-role-editor'); ?><span class="description"> (<?php echo __('required', 'wpfront-user-role-editor'); ?>)</span>
                </th>
                <td>
                    <input id="login-redirect-url" class="regular-text" name="url" type="text" value="<?php echo esc_attr($this->get_current_url()); ?>" aria-required="true"  />
                    <br />
                    <span class="description">[<?php echo __('Relative to home URL (recommended) or absolute URL.', 'wpfront-user-role-editor'); ?>]</span>
                </td>
            </tr>
            <?php
        }
        
        protected function logout_redirect_url_row() {
            ?>
            <tr>
                <th scope="row">
                    <?php echo __('Logout Redirect URL', 'wpfront-user-role-editor'); ?>
                </th>
                <td>
                    <input class="regular-text" name="logout_url" type="text" value="<?php echo esc_attr($this->get_current_logout_url()); ?>" aria-required="true"  />
                    <br />
                    <span class="description">[<?php echo __('Relative to home URL (recommended) or absolute URL.', 'wpfront-user-role-editor'); ?>]</span>
                </td>
            </tr>
            <?php
        }
        
        protected function deny_wp_admin_row() {
            ?>
            <tr>
                <th scope="row">
                    <?php echo __('Deny WP-ADMIN', 'wpfront-user-role-editor'); ?>
                </th>
                <td>
                    <input name="deny_wpadmin" type="checkbox" <?php echo $this->get_current_deny_wpadmin() ? 'checked' : '' ?> />
                </td>
            </tr>
            <?php
        }
        
        protected function disable_toolbar_row() {
            ?>
            <tr>
                <th scope="row">
                    <?php echo __('Disable Toolbar', 'wpfront-user-role-editor'); ?>
                </th>
                <td>
                    <input name="disable_toolbar" type="checkbox" <?php echo $this->get_current_disable_toolbar() ? 'checked' : '' ?> />
                </td>
            </tr>
            <?php
        }
        
        protected function get_current_role() {
            if(empty($_POST['role']))
                return '';
            
            return $_POST['role'];
        }
        
        protected function get_current_priority() {
            if(!empty($_POST['submit'])) {
                return isset($_POST['priority']) ? $_POST['priority'] : '';
            }
            
            if(!empty($this->role_entity)) {
                return $this->role_entity->priority;
            }
            
            return LoginRedirect::instance()->get_next_priority();
        }
        
        protected function get_current_url() {
            if(!empty($_POST['submit'])) {
                return empty($_POST['url']) ? '' : $_POST['url'];
            }
            
            if(!empty($this->role_entity)) {
                return $this->role_entity->url;
            }
            
            return '';
        }
        
        protected function get_current_logout_url() {
            if(!empty($_POST['submit'])) {
                return empty($_POST['logout_url']) ? '' : $_POST['logout_url'];
            }
            
            if(!empty($this->role_entity)) {
                return $this->role_entity->logout_url;
            }
            
            return '';
        }
        
        protected function get_current_deny_wpadmin() {
            if(!empty($_POST['submit'])) {
                return !empty($_POST['deny_wpadmin']);
            }
            
            if(!empty($this->role_entity)) {
                return $this->role_entity->deny_wpadmin;
            }
            
            return false;
        }
        
        protected function get_current_disable_toolbar() {
            if(!empty($_POST['submit'])) {
                return !empty($_POST['disable_toolbar']);
            }
            
            if(!empty($this->role_entity)) {
                return $this->role_entity->disable_toolbar;
            }
            
            return false;
        }
        
        protected function is_role_valid() {
            if(!empty($_POST['role'])) {
                $role = $_POST['role'];
                $roles_info = $this->get_roles();
                if(!isset($roles_info[$role])) {
                    return false;
                }
                
                return $roles_info[$role]->allowed;
            }
            
            return true;
        }
        
        protected function is_priority_valid() {
            if(!empty($_POST['submit'])) {
                if(empty($_POST['priority'])) {
                    return false;
                }
                $priority = intval($_POST['priority']);
                return $priority > 0;
            }
            
            return true;
        }
        
        protected function is_url_valid() {
            if(!empty($_POST['submit'])) {
                return !empty($_POST['url']);
            }
            
            return true;
        }
        
        protected function get_roles() {
            return LoginRedirect::instance()->get_roles_info_for_new();
        }
        
        protected function scripts() {
            ?>
            <script type="text/javascript">
                (function($) {
                    var role_check = function () {
                        var $option = $("#login-redirect-role option:selected");

                        if ($option.length === 0)
                            return;

                        if ($option.data("allowed")) {
                            $("#form-login-redirect input").prop("disabled", false)
                            $("#login-redirect-custom-role-disabled").addClass("hidden");
                        } else {
                            $("#form-login-redirect input").prop("disabled", true)
                            $("#login-redirect-custom-role-disabled").removeClass("hidden");
                        }
                    };

                    $("#login-redirect-role").change(function() {
                        $(this).closest("tr").removeClass("form-invalid");
                        role_check();
                    });
                    
                    $("#login-redirect-priority").blur(function() {
                        if($(this).val() != "")
                            $(this).closest("tr").removeClass("form-invalid");
                    });
                    
                    $("#login-redirect-url").blur(function() {
                        if($(this).val() != "")
                            $(this).closest("tr").removeClass("form-invalid");
                    });
                    
                    role_check();
                })(jQuery);
            </script>
            <?php
        }
    
    }
    
}

