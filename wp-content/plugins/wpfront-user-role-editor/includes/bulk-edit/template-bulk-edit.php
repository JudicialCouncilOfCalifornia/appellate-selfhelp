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
 * Template for WPFront User Role Editor Bulk Edit
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Bulk_Edit;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;
use WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit as Bulk_Edit;

if(!class_exists('WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit_View')) {
    
    class WPFront_User_Role_Editor_Bulk_Edit_View {
        
        public function view() {
            ?>
            <div class="wrap bulk-edit">
                <?php $this->title(); ?>
                <form method="get">
                    <input type="hidden" name="page" value="<?php echo esc_attr(Bulk_Edit::MENU_SLUG); ?>" />
                    <div class="options">
                        <?php $this->display_options(); ?>
                    </div>
                    <?php submit_button(__('Next Step', 'wpfront-user-role-editor')); ?>
                </form>
            </div>
            <?php
        }
        
        protected function title() {
            ?>
            <h2><?php echo __('Bulk Edit', 'wpfront-user-role-editor'); ?></h2>
            <p>
                <?php echo __('Select an option from below then click next step.', 'wpfront-user-role-editor'); ?>
            </p>
            <?php
        }
        
        protected function display_options() {
            $controllers = Bulk_Edit::instance()->get_controllers();
            $select = true;
            foreach ($controllers as $ctlr) {
                ?>
                <p>
                    <label><input type="radio" name="screen" value="<?php echo esc_attr($ctlr->get_key()); ?>" <?php echo $select ? 'checked' : '' ?> /><?php echo esc_html($ctlr->get_option_text()); ?></label>
                </p>
                <?php
                $select = false;
            }
        }
    
    }
    
}

