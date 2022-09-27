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
 * Template for WPFront User Role Editor Login Redirect Delete
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Login_Redirect;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;
use WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect as LoginRedirect;

if(!class_exists('WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect_Delete_View')) {
    
    class WPFront_User_Role_Editor_Login_Redirect_Delete_View {
        /**
         *
         * @var string[] 
         */
        private $roles;
        
        public function __construct($roles) {
            $this->roles = $roles;
        }
        
        public function view() {
            ?>
            <div class="wrap login-redirect">
                <?php $this->title(); ?>
                <form id="form-login-redirect" method='post'>
                    <ol>
                        <?php $this->roles_display(); ?>
                    </ol>
                    <?php wp_nonce_field('delete-login-redirect'); ?>
                    <?php submit_button(__('Confirm Delete', 'wpfront-user-role-editor'), 'button-secondary'); ?>
                </form>
            </div>
            <?php
        }
        
        protected function title() {
            ?>
            <h2>
                <?php echo __('Delete Login Redirects', 'wpfront-user-role-editor'); ?>
                <p><?php echo __('The following role configurations will be deleted.', 'wpfront-user-role-editor'); ?></p>
            </h2>
            <?php
        }
        
        protected function roles_display() {
            foreach ($this->roles as $role) {
                $display = LoginRedirect::instance()->get_role_display($role);
                echo "<li><strong>".esc_html($display)."</strong> [".esc_html($role)."]</li>";
                echo "<input type='hidden' name='bulk-delete[".esc_attr($role)."]' />";
            }
        }
    }
    
}

