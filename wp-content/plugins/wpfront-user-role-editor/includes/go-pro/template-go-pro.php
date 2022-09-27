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
 * Template for WPFront User Role Editor Go Pro
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Go_Pro;

if (!defined('ABSPATH')) {
    exit(); //@codeCoverageIgnore
}

if(!class_exists('WPFront\URE\Go_Pro\WPFront_User_Role_Editor_Go_Pro_View')) {
    
    class WPFront_User_Role_Editor_Go_Pro_View extends \WPFront\URE\WPFront_User_Role_Editor_View {
        
        public function view($title) {
            ?>
            <div class="wrap go-pro">
                <?php $this->title($title); ?>
                <?php do_action('wpfront_ure_internal_go_pro_template'); ?>
                <?php $this->go_pro_html(); ?>
                <?php $this->mailchimp_signup(); ?>
            </div>
            <?php
        }
        
        protected function go_pro_html() {
            if(!apply_filters('wpfront_ure_internal_go_pro_template_show_comparison', true)) {
                return;
            }
            ?>
            <div class="container">
                <div class="col col1">
                    <div class="cell header">
                        <h3>Free Version</h3>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/add-role/">Add/Edit/Delete</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/restore-role/">Restore Roles</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/assign-migrate-users/">Assign/Migrate Users</a>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        &nbsp;
                    </div>
                </div>
                <div class="col col2">
                    <div class="cell header">
                        <h3>Personal Pro</h3>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/add-role/">Add/Edit/Delete</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/restore-role/">Restore Roles</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/assign-migrate-users/">Assign/Migrate Users</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/navigation-menu-permissions/">Navigation Menu Permissions Advanced</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/widget-permissions/">Widget Permissions Advanced</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/login-redirect/">Login Redirect Advanced</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/menu-editor/">Admin Menu Editor</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/media-attachment-file-permissions/">Media Library Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-level-permissions/">User Level Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/content-restriction-shortcodes/">Content Restriction Shortcodes</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/posts-pages-extended-permissions/">Posts/Pages Extended Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/custom-post-type-permissions/">Custom Post Type Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/export-roles/">Import/Export</a>
                    </div>
                    <div class="cell">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/support/forum/user-role-editor-pro-2/">Private Forums & Updates for One Year</a>
                    </div>
                    <div class="cell footer">
                        <a class="button-primary" href="https://wpfront.com/ppro" target="_blank">
                            <i class="fa fa-shopping-cart"></i>
                            Buy Now
                        </a>
                    </div>
                </div>
                <div class="col col3">
                    <div class="cell header">
                        <h3>Business Pro</h3>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/add-role/">Add/Edit/Delete</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/restore-role/">Restore Roles</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/assign-migrate-users/">Assign/Migrate Users</a>
                    </div>   
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/navigation-menu-permissions/">Navigation Menu Permissions Advanced</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/widget-permissions/">Widget Permissions Advanced</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/login-redirect/">Login Redirect Advanced</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/menu-editor/">Admin Menu Editor</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/media-attachment-file-permissions/">Media Library Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-level-permissions/">User Level Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/content-restriction-shortcodes/">Content Restriction Shortcodes</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/posts-pages-extended-permissions/">Posts/Pages Extended Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/custom-post-type-permissions/">Custom Post Type Permissions</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/export-roles/">Import/Export</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/user-role-editor-pro/multisite-sync-roles/">Multisite Support</a>
                    </div>
                    <div class="cell">
                        <a target="_blank" href="https://wpfront.com/support/forum/user-role-editor-pro-2/">Private Forums & Updates for One Year</a>
                    </div>
                    <div class="cell footer">
                        <a class="button-primary" href="https://wpfront.com/bpro" target="_blank">
                            <i class="fa fa-shopping-cart"></i>
                            Buy Now
                        </a>
                    </div>
                </div>
            </div>
            <?php
        }
        
        protected function mailchimp_signup() {
            ?>
            <!-- Begin MailChimp Signup Form -->
            <link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
            <style type="text/css">
                    #mc_embed_signup{background:#fff; clear:left; font:14px Helvetica,Arial,sans-serif; }
            </style>
            <div id="mc_embed_signup" style="max-width:602px;margin-top:5px;">
            <form action="//wpfront.us10.list-manage.com/subscribe/post?u=025ec8aba76cfe3dcb048824b&amp;id=40d9eecd94" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                <div id="mc_embed_signup_scroll">
                    <h2>Get notified on new features and releases</h2>
            <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
            <div class="mc-field-group">
                    <label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
            </label>
                    <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
            </div>
            <div class="mc-field-group">
                    <label for="mce-FNAME">First Name </label>
                    <input type="text" value="" name="FNAME" class="" id="mce-FNAME">
            </div>
            <div class="mc-field-group">
                    <label for="mce-LNAME">Last Name </label>
                    <input type="text" value="" name="LNAME" class="" id="mce-LNAME">
            </div>
                    <div id="mce-responses" class="clear">
                            <div class="response" id="mce-error-response" style="display:none"></div>
                            <div class="response" id="mce-success-response" style="display:none"></div>
                    </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                <div style="position: absolute; left: -5000px;"><input type="text" name="b_025ec8aba76cfe3dcb048824b_40d9eecd94" tabindex="-1" value=""></div>
                <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button" style="background-color:#00a0d2;"></div>
                </div>
            </form>
            </div>
            <script type='text/javascript' src='//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js'></script><script type='text/javascript'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]='EMAIL';ftypes[0]='email';fnames[1]='FNAME';ftypes[1]='text';fnames[2]='LNAME';ftypes[2]='text';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
            <!--End mc_embed_signup-->
            <?php
        }
    
    }
    
}

