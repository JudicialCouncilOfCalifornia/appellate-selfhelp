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
 * Template for WPFront User Role Editor Add or Remove Cap
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Bulk_Edit;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Add_Remove_Cap as Add_Remove_Cap;
use WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit as Bulk_Edit;

if(!class_exists('WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Add_Remove_Cap_View')) {
    
    class WPFront_User_Role_Editor_Add_Remove_Cap_View extends \WPFront\URE\WPFront_User_Role_Editor_View {
        
        private $error = null;
        
        public function view($error) {
            $this->error = $error;
            ?>
            <div class="wrap add-remove-capability">
                <?php $this->title(); ?>
                <form method="post" class="validate" action="<?php echo esc_attr(Bulk_Edit::instance()->get_screen_url(Add_Remove_Cap::instance())); ?>">
                    <table class="form-table">
                        <tbody>
                            <?php $this->action_row(); ?>
                            <?php $this->capability_row(); ?>
                            <?php $this->denied_cap_row(); ?>
                            <?php $this->roles_row(); ?>
                        </tbody>
                    </table>
                    <?php wp_nonce_field('add-remove-capability'); ?>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php $this->scripts(); ?>
            <?php
        }
        
        protected function title($title = '', $add_new = array(), $search = null) {
            ?>
            <h2><?php echo __('Add/Remove Capability', 'wpfront-user-role-editor'); ?></h2>
            <?php $this->display_notices(); ?>
            <p>
                <?php echo __('Add/Remove a capability to/from roles within this site.', 'wpfront-user-role-editor'); ?>
            </p>
            <?php
        }
        
        protected function display_notices() {
            if(!empty($this->error)) {
                Utils::notice_error($this->error);
            } elseif(isset($_GET['changes-saved'])) {
                Utils::notice_updated(sprintf(__('%d role(s) updated.', 'wpfront-user-role-editor'), $_GET['changes-saved']));
            }
        }
        
        protected function action_row() {
            ?>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __('Action', 'wpfront-user-role-editor'); ?>
                    </label>
                </th>
                <td>
                    <fieldset>
                        <label><input name="action_type" type="radio" value="add" <?php echo $this->get_current_action() === 'add' ? 'checked' : ''; ?> /><?php echo __('Add Capability', 'wpfront-user-role-editor'); ?></label>
                        <br />
                        <label><input name="action_type" type="radio" value="remove" <?php echo $this->get_current_action() === 'remove' ? 'checked' : ''; ?> /><?php echo __('Remove Capability', 'wpfront-user-role-editor'); ?></label>
                    </fieldset>
                </td>
            </tr>
            <?php
        }
        
        protected function capability_row() {
            ?>
            <tr class="form-required <?php echo $this->is_capability_field_invalid() ? 'form-invalid' : ''; ?>">
                <th scope="row">
                    <label for="capability">
                        <?php echo __('Capability', 'wpfront-user-role-editor'); ?> <span class="description">(<?php echo __('required', 'wpfront-user-role-editor'); ?>)</span>
                    </label>
                </th>
                <td>
                    <input class="regular-text" name="capability" type="text" id="capability" value="<?php echo esc_attr($this->get_current_capability()); ?>" aria-required="true"  />
                </td>
            </tr>
            <?php
        }
        
        protected function denied_cap_row() {
            ?>
            <tr>
                <th scope="row">
                    <label for="denied_cap">
                        <?php echo __('Add as "Denied Capability"', 'wpfront-user-role-editor'); ?>
                    </label>
                </th>
                <td>
                    <input id="denied_cap" name="denied_cap" type="checkbox" value="add" <?php echo $this->get_current_denied_cap() ? 'checked' : ''; ?> <?php echo $this->get_current_action() === 'remove' ? 'disabled' : ''; ?> />
                </td>
            </tr>
            <?php
        }
        
        protected function roles_row() {
            ?>
            <tr>
                <th scope="row">
                    <label>
                        <?php echo __('Roles', 'wpfront-user-role-editor'); ?>
                    </label>
                </th>
                <td>
                    <fieldset>
                        <label><input name="roles_type" type="radio" value="all" <?php echo $this->get_current_roles_type() === 'all' ? 'checked' : ''; ?> /><?php echo __('All Roles', 'wpfront-user-role-editor'); ?></label>
                        <br />
                        <label><input name="roles_type" type="radio" value="selected" <?php echo $this->get_current_roles_type() === 'selected' ? 'checked' : ''; ?> /><?php echo __('Selected Roles', 'wpfront-user-role-editor'); ?></label>
                        <div class="<?php echo $this->get_current_roles_type() === 'all' ? 'hidden' : ''; ?>">
                            <?php
                            $admin_role = $this->RolesHelperClass::get_display_name($this->RolesHelperClass::ADMINISTRATOR_ROLE_KEY);
                            if(!empty($admin_role)) {
                                ?>
                                <label><input id="chk_admin" type="checkbox" disabled="true" <?php echo $this->get_current_action() === 'add' ? 'checked' : ''; ?> /><?php echo esc_html($admin_role); ?></label>
                                <br />
                                <?php
                            }
                            
                            $roles = $this->get_roles();
                            $selected_roles = $this->get_current_selected_roles();
                            foreach ($roles as $role_name => $role_display) {
                                ?>
                                <label><input type="checkbox" name="selected-roles[<?php echo esc_attr($role_name); ?>]" <?php echo array_key_exists($role_name, $selected_roles) ? 'checked' : ''; ?> /><?php echo esc_html($role_display); ?></label>
                                <br />
                                <?php
                            }
                            ?>
                        </div>
                    </fieldset>
                </td>
            </tr>
            <?php
        }
        
        protected function get_roles() {
            $roles = Add_Remove_Cap::instance()->get_editable_roles();
            unset($roles[$this->RolesHelperClass::ADMINISTRATOR_ROLE_KEY]);
            return $roles;
        }
        
        protected function get_current_action() {
            if(!empty($_POST['action_type'])) {
                if($_POST['action_type'] === 'remove') {
                    return 'remove';
                }
            }
            
            return 'add';
        }
        
        protected function get_current_capability() {
            if(!empty($_POST['capability'])) {
                return $_POST['capability'];
            }
            
            return '';
        }
        
        protected function get_current_denied_cap() {
            return !empty($_POST['denied_cap']);
        }
        
        protected function get_current_roles_type() {
            if(!empty($_POST['roles_type'])) {
                if($_POST['roles_type'] === 'selected') {
                    return 'selected';
                }
            }
            
            return 'all';
        }
        
        protected function get_current_selected_roles() {
            if(!empty($_POST['selected-roles'])) {
                if($_POST['selected-roles']) {
                    return $_POST['selected-roles'];
                }
            }
            
            return array();
        }
        
        protected function is_capability_field_invalid() {
            if(!empty($_POST['submit'])) {
                return empty($_POST['capability']);
            }
            
            return false;
        }
        
        protected function scripts() {
            ?>
            <script type="text/javascript">

                (function ($) {

                    var $container = $('div.wrap.add-remove-capability');
                    
                    $container.find('input[name="action_type"]').change(function () {
                        if ($(this).val() === 'add') {
                            $container.find("#denied_cap").prop('disabled', false);
                            $container.find("#chk_admin").prop('checked', true);
                        } else {
                            $container.find("#denied_cap").prop('disabled', true);
                            $container.find("#chk_admin").prop('checked', false);
                        }
                    });
                    
                    $container.find("#capability").blur(function() {
                        var $this = $(this);
                        
                        if($.trim($this.val()) !== "") {
                            $this.closest("tr").removeClass("form-invalid");
                        }
                    });

                    $container.find('input[name="roles_type"]').change(function () {
                        if ($(this).val() === 'all') {
                            $(this).closest('fieldset').find('div').addClass('hidden');
                        } else {
                            $(this).closest('fieldset').find('div').removeClass('hidden');
                        }
                    });
                    
                    $container.find("form").submit(function() {
                        var $capability = $container.find("#capability");
                        if($.trim($capability.val()) === "") {
                            $capability.closest("tr").addClass("form-invalid");
                            return false;
                        }
                    });

                })(jQuery);

            </script>
            <?php
        }
    
    }
    
}

