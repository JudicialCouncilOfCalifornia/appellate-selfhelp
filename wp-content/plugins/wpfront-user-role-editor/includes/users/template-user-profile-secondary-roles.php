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
 * Template for WPFront User Role Editor user profile secondary roles
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Assign_Migrate;

if (!defined('ABSPATH')) {
    exit();
}

if(!class_exists('WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_User_Profile_Secondary_Roles_View')) {
    
    class WPFront_User_Role_Editor_User_Profile_Secondary_Roles_View {
        
        private $controller;
        
        /**
         *
         * @var \WP_User
         */
        private $user = null;
        
        public function __construct($controller, $user = null) {
            $this->controller = $controller;
            $this->user = $user;
        }

        public function view($roles) {
            if($this->controller->hide_secondary_roles()) {
                return;
            } 
            
            ?>
            <table class="form-table user-profile-secondary-roles">
                <tbody>
                    <tr class="user-secondary-role-wrap">
                        <th scope="row"><?php echo __('Secondary Roles', 'wpfront-user-role-editor') ?></th>
                        <td>
                            <?php $this->display_secondary_roles($roles); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <script type="text/javascript">
                (function() {
                    var $ = jQuery;
                    
                    $('select[name="role"]').each(function() {
                        var $role = $(this).closest('tr');

                        var $sec_role = $role.closest('form').find('tr.user-secondary-role-wrap');
                        $sec_role.insertAfter($role);
                    });
                    $('table.user-profile-secondary-roles').remove();
                })();
            </script>
            <?php
        }
        
        public function display_secondary_roles($roles, $selected = null) {
            ?>
            <div class="role-containers">
                <?php
                if(empty($roles)) {
                    echo __('No roles for this site.', 'wpfront-user-role-editor');
                } else {
                    if($selected === null) {
                        $selected = $this->get_current_secondary_roles();
                    }

                    foreach ($roles as $name => $display) {
                        $checked = in_array($name, $selected) ? 'checked' : '';
                        ?>
                        <div class="role-container">
                            <label>
                                <input type="checkbox" name="wpfront-secondary-roles[<?php echo esc_attr($name); ?>]" <?php echo $checked; ?> />
                                <?php echo esc_html($display); ?>
                            </label>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
            <?php
        }
        
        public function add_general_settings_script() {
            ?>
            <script type="text/javascript">
                addLoadEvent(function() {
                    var $ = jQuery;

                    $sec_roles_row = $('div.role-containers').closest('tr');
                    $default_role_row = $('#default_role').closest('tr');

                    $sec_roles_row.insertAfter($default_role_row);
                });
            </script>
            <?php
        }
        
        /**
         * Returns currently selected roles list.
         * 
         * @return type
         */
        protected function get_current_secondary_roles() {
            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                if(empty($_POST['wpfront-secondary-roles'])) {
                    return array();
                }
                
                return array_keys($_POST['wpfront-secondary-roles']);
            }
            
            if(empty($this->user)) {
                return $this->controller->get_new_user_default_secondary_roles();
            }
            
            $roles = array_values($this->user->roles);
            array_shift($roles);
            return $roles;
        }
    
    }
    
}

