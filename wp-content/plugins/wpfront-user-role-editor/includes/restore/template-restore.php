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
 * Template for WPFront User Role Editor Role Restore
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Restore;

if (!defined('ABSPATH')) {
    exit();
}

if(!class_exists('WPFront\URE\Restore\WPFront_User_Role_Editor_Restore_View')) {
    
    class WPFront_User_Role_Editor_Restore_View extends \WPFront\URE\WPFront_User_Role_Editor_View {
        
        protected $Restore;
        
        public function __construct() {
            parent::__construct();
            
            $this->Restore = WPFront_User_Role_Editor_Restore::instance();
        }
        
        public function view() {
            ?>
            <div class="wrap role-restore">
                <?php $this->title(__('Restore Role', 'wpfront-user-role-editor')); ?>
                
                <table class="form-table">
                    <tbody>
                        <?php $this->display_restore_rows(); ?>
                    </tbody>
                </table>
            </div>
            <?php
            $this->scripts();
        }
        
        protected function display_restore_rows() {
            $roles = $this->Restore->get_restorable_roles();
            
            foreach ($roles as $name => $display) {
                $this->display_role_row($name, $display);
            }
            
        }
        
        protected function display_role_row($role_name, $display_name) {
            ?>
            <tr class="form-field">
                <th scope="row">
                    <?php echo esc_html($display_name); ?>
                </th>
                <td>
                    <button class="button button-primary restore-role" value="<?php echo esc_attr($role_name); ?>"><?php echo __('Restore', 'wpfront-user-role-editor'); ?></button>
                    <div class="restore-role-button-container">
                        <button class="button restore-role-cancel" value="<?php echo esc_attr($role_name); ?>"><?php echo __('Cancel', 'wpfront-user-role-editor'); ?></button>
                        <button class="button restore-role-confirm" value="<?php echo esc_attr($role_name); ?>"><?php echo __('Confirm', 'wpfront-user-role-editor'); ?></button>
                    </div>
                    <div class="restore-role-loader">
                        <img src="<?php echo esc_attr($this->WPFURE->get_asset_url('images/loading.gif')); ?>" />
                    </div>
                    <div class="restore-role-success">
                        <button class="button button" disabled="true">
                            <i class="fa fa-check fa-1"></i>
                            <?php echo __('Restored', 'wpfront-user-role-editor'); ?>
                        </button>
                    </div>
                </td>
            </tr>
            <?php
        }
        
        protected function ajax_url() {
            return json_encode(admin_url('admin-ajax.php'));
        }
        
        protected function scripts() {
            ?>
            <script type="text/javascript">
                (function($) {
                    $("button.restore-role").click(function() {
                        
                        $(this).hide().next().show();
                    });

                    $("button.restore-role-cancel").click(function() {
                        $(this).parent().hide().prev().show();
                    });

                    $("button.restore-role-confirm").click(function() {
                        $("button.restore-role-confirm").prop("disabled", true);

                        var _this = $(this).parent().hide().next().show();

                        var data = {
                            "action": "wpfront_user_role_editor_restore_role",
                            "role": $(this).val(),
                            "nonce": <?php echo json_encode(wp_create_nonce("restore-role")); ?>
                        };

                        var response_process = function(response) {
                            if (typeof response === "undefined" || response == null) {
                                response = {"result": false, "message": <?php echo json_encode(__('Unexpected error / Timed out', 'wpfront-user-role-editor')); ?>};
                            }
                            _this.hide();
                            if (response.result)
                                _this.next().show();
                            else
                                _this.next().text(response.message).css("color", "Red").show();

                            $("button.restore-role-confirm").prop("disabled", false);
                        };

                        var ajaxurl = <?php echo $this->ajax_url(); ?>;
                        $.post(ajaxurl, data, response_process, "json").fail(function() {
                            response_process();
                        });
                    });
                })(jQuery);
            </script>
            <?php
        }
    
    }
    
}

