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
 * Template for WPFront User Role Editor Migrate
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

if(!class_exists('WPFront\URE\Assign_Migrate\WPFront_User_Role_Editor_Migrate_View')) {
    
    class WPFront_User_Role_Editor_Migrate_View extends WPFront_User_Role_Editor_Assign_Migrate_View {
        
        public function view() {
            ?>
            <div class="wrap migrate-users">
                <?php
                $this->title();
                $this->display_notices();
                ?>
                <form method="post" action="<?php echo esc_attr(AssignMigrate::instance()->get_self_url()); ?>">
                    <?php
                    $this->form_table();
                    wp_nonce_field('migrate-users');
                    $this->migrate_button();
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
                <?php echo __('Migrate Users', 'wpfront-user-role-editor'); ?>
            </h2>
            <?php
        }
        
        protected function display_notices() {
            $error = AssignMigrate::instance()->get_error_string();
            if(!empty($error) && !empty($_POST['migrate'])) {
                Utils::notice_error($error);
            } elseif(isset($_GET['users-migrated'])) {
                $count = $_GET['users-migrated'];
                Utils::notice_updated(sprintf(__('%d user(s) migrated.'), $count));
            }
        }
        
        protected function form_table() {
            ?>
            <table class="form-table">
                <tbody>
                    <?php $this->from_primary_role_row(); ?>
                    <?php $this->primary_role_row(AssignMigrate::instance()->get_migrate_to_primary_roles()); ?>
                    <?php $this->secondary_roles_row(AssignMigrate::instance()->get_migrate_secondary_roles()); ?>
                </tbody>
            </table>
            <?php
        }
        
        protected function from_primary_role_row() {
            ?>
            <tr>
                <th scope="row">
                    <?php echo __('From Primary Role', 'wpfront-user-role-editor'); ?>
                </th>
                <td>
                    <?php $this->from_primary_role_dropdown(); ?>
                </td>
            </tr>
            <?php
        }
        
        protected function from_primary_role_dropdown() {
            ?>
            <select id="migrate_from_role" name="migrate-from-primary-role">
                <?php
                $roles = AssignMigrate::instance()->get_migrate_from_primary_roles();
                $current_role = $this->get_current_from_primary_role();
                foreach ($roles as $name => $display) {
                    $selected = $name === $current_role ? 'selected' : '';
                    echo "<option $selected value='".esc_attr($name)."'>".esc_html($display)."</option>";
                }
                ?>
            </select>
            <?php
        }
        
        protected function get_current_from_primary_role() {
            if(!empty($_POST['migrate-from-primary-role'])) {
                return $_POST['migrate-from-primary-role'];
            }
            
            return null;
        }
        
        protected function get_current_primary_role() {
            if(!empty($_POST['primary-role'])) {
                return $_POST['primary-role'];
            }
            
            return null;
        }
        
        protected function get_current_secondary_roles() {
            if(!empty($_POST['migrate'])) {
                if(empty($_POST['secondary-roles'])) {
                    return array();
                }
                
                return array_keys($_POST['secondary-roles']);
            }
            
            return array();
        }
        
        protected function migrate_button() {
            submit_button(__('Migrate Users'), 'primary', 'migrate');
        }
    
    }
    
}

