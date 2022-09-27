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
 * Template for WPFront User Role Editor Login Redirect
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

require_once dirname(__FILE__) . '/class-login-redirect-list-table.php';

if(!class_exists('WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect_View')) {
    
    class WPFront_User_Role_Editor_Login_Redirect_View {
        
        public function view() {
            ?>
            <div class="wrap login-redirect">
                <?php $this->title(); ?>
                <?php $this->display_notices(); ?>
                <?php
                $list_table = new WPFront_User_Role_Editor_Login_Redirect_List_Table();
                $list_table->prepare_items();
                ?>
                <form action="" method="get" class="search-form">
                    <input type="hidden" name="page" value="<?php echo esc_attr(LoginRedirect::MENU_SLUG); ?>" />
                    <?php $list_table->search_box(__('Search', 'wpfront-user-role-editor'), 'login-redirect'); ?>
                </form>
                <form id="form-login-redirect" method='post'>
                    <?php
                    $list_table->display();
                    ?>
                </form>
            </div>
            <?php
        }
        
        protected function title() {
            ?>
            <h2>
                <?php echo __('Login Redirects', 'wpfront-user-role-editor'); ?>
                <a href="<?php echo esc_attr($this->get_add_new_url()); ?>" class="add-new-h2"><?php echo __('Add New', 'wpfront-user-role-editor'); ?></a>
            </h2>
            <?php
        }
        
        protected function display_notices() {
            if ((isset($_GET['deleted']) && $_GET['deleted'] == 'true')) {
                Utils::notice_updated(__('Role configurations deleted.', 'wpfront-user-role-editor'));
            }
        }
        
        protected function get_add_new_url() {
            return LoginRedirect::instance()->get_add_new_url();
        }
    
    }
    
}

