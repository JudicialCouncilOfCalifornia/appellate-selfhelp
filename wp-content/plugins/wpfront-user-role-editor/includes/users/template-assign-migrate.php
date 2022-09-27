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
 * Template for WPFront User Role Editor Assign Migrate
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Assign_Migrate;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;
use WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_Migrate as AssignMigrate;

if(!class_exists('WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Assign_Migrate_View')) {
    
    class WPFront_User_Role_Editor_Assign_Migrate_View {
        
        public function view() {
            $objAssign = new WPFront_User_Role_Editor_Assign_View();
            $objAssign->view();

            $objMigrate = new WPFront_User_Role_Editor_Migrate_View();
            $objMigrate->view();
        }
        
        protected function primary_role_row($roles) {
            ?>
            <tr>
                <th scope="row">
                    <?php echo __('Primary Role', 'wpfront-user-role-editor'); ?>
                </th>
                <td>
                    <?php $this->primary_role_dropdown($roles); ?>
                </td>
            </tr>
            <?php
        }
        
        protected function primary_role_dropdown($roles) {
            ?>
            <select class="primary-role" name="primary-role">
                <?php
                $current_role = $this->get_current_primary_role();
                foreach ($roles as $name => $display) {
                    $selected = $name === $current_role ? 'selected' : '';
                    echo "<option $selected value='".esc_attr($name)."'>".esc_html($display)."</option>";
                }
                ?>
            </select>
            <?php
        }
        
        protected function secondary_roles_row($roles) {
            ?>
            <tr>
                <th scope="row">
                    <?php echo __('Secondary Roles', 'wpfront-user-role-editor'); ?>
                </th>
                <td>
                    <div class="role-list">
                        <?php
                        $current_roles = $this->get_current_secondary_roles();
                        
                        foreach ($roles as $name => $display) {
                            $checked = in_array($name, $current_roles) ? 'checked' : '';
                            ?>
                            <div class="role-list-item">
                                <label>
                                    <input type="checkbox" name="secondary-roles[<?php echo esc_attr($name); ?>]" <?php echo $checked; ?> />
                                    <?php echo esc_html($display); ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <?php
        }
        
        protected function scripts() {
            ?>
            <script type="text/javascript">
                (function($) {
                    $('.primary-role').change(function() {
                        var $this = $(this);
                        
                        if($this.val() == '') {
                            $this.closest('tr').next().find('.role-list-item input').prop('disabled', true);
                        } else {
                            $this.closest('tr').next().find('.role-list-item input').prop('disabled', false);
                        }
                    }).change();
                })(jQuery);
            </script>
            <?php
        }
    
    }
    
}

require_once dirname(__FILE__) . '/template-assign.php';
require_once dirname(__FILE__) . '/template-migrate.php';

