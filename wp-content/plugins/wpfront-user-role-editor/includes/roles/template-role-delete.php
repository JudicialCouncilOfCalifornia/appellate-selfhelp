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
 * Template for WPFront User Role Editor Role Delete
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Roles;

if (!defined('ABSPATH')) {
    exit();
}

if(!class_exists('WPFront\URE\Roles\WPFront_User_Role_Editor_Role_Delete_View')) {
    
    class WPFront_User_Role_Editor_Role_Delete_View extends \WPFront\URE\WPFront_User_Role_Editor_View {
        
        protected $RoleDelete;
        
        public function __construct() {
            parent::__construct();
            
            $this->RoleDelete = WPFront_User_Role_Editor_Role_Delete::instance();
        }
        
        public function view() {
            ?>
            <div class="wrap delete-roles">
                <?php $this->title(__('Delete Roles', 'wpfront-user-role-editor')); ?>
                <?php $this->description(); ?>
                <form method="post">
                    <?php wp_nonce_field('delete-roles'); ?>
                    <ul>
                        <?php $this->display_data(); ?>
                    </ul>
                    <?php 
                    $attr = [];
                    if(!$this->is_submit_allowed()) {
                        $attr['disabled'] = true;
                    }
                    submit_button(__('Confirm Deletion', 'wpfront-user-role-editor'), 'primary', 'confirm-delete', true, $attr);
                    ?>
                </form>
            </div>
            <?php
        }
        
        protected function description() {
            ?>
            <p><?php echo __('You have specified these roles for deletion', 'wpfront-user-role-editor'); ?>:</p>
            <?php
        }
        
        protected function display_data() {
            $role_data = $this->RoleDelete->get_delete_data();
            
            foreach ($role_data as $key => $value) {
                ?>
                <li>
                    <?php $this->display_role_data($value); ?>
                </li>
                <?php
            }
        }
        
        protected function display_role_data($data) {
            printf('%s: <strong>%s</strong> [<strong>%s</strong>]', __('Role', 'wpfront-user-role-editor'), esc_html($data->name), esc_html($data->display_name));
            if(!empty($data->status_message)) {
                printf(' - <strong>%s</strong>', esc_html($data->status_message));
            }
            ?>
            <input type="hidden" name="delete-roles[<?php echo esc_attr($data->name); ?>]" value="1" />
            <?php
        }
        
        protected function is_submit_allowed() {
            $role_data = $this->RoleDelete->get_delete_data();
            
            foreach ($role_data as $key => $value) {
                if ($value->is_deletable)
                    return true;
            }

            return false;
        }
    
    }
    
}

