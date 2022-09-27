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
 * Controller for WPFront User Role Editor Go Pro
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Go_Pro;

if (!defined('ABSPATH')) {
    exit(); //@codeCoverageIgnore
}

require_once dirname(__FILE__) . '/template-go-pro.php';

if (!class_exists('\WPFront\URE\Go_Pro\WPFront_User_Role_Editor_Go_Pro')) {

    /**
     * Go Pro class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Go_Pro extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {
        
        const MENU_SLUG = 'wpfront-user-role-editor-go-pro';
        
        protected static $CAP = 'manage_options';
        
        protected $product;
        
        protected function setUp() {
            $this->_setUp('manage_options', 'wpfront-user-role-editor-go-pro');

            $this->product = 'WPFront User Role Editor Pro';
            
            $this->ViewClass = WPFront_User_Role_Editor_Go_Pro_View::class;
        }
        
        
        protected function initialize() {
            do_action('wpfront_ure_init');
            
            if(!$this->in_admin_ui()) {
                return;
            }
            
            $this->plugin_license_active();
        }
        
        /**
         * Setup hooks when plugin license is active.
         */
        protected function plugin_license_active() {
            $this->set_admin_menu($this->get_page_title(), $this->get_menu_label(), 1000);
            
            add_filter('plugin_action_links_' . $this->WPFURE->get_plugin_basename(), array($this, 'plugin_action_links'), PHP_INT_MAX);
            add_filter('network_admin_plugin_action_links_' . $this->WPFURE->get_plugin_basename(), array($this, 'plugin_action_links'), PHP_INT_MAX);
        }
        
        /**
         * Adds the 'Upgrade' plugin action link.
         * 
         * @param string[] $links
         * @return string[]
         */
        public function plugin_action_links($links) {
            $url = 'https://wpfront.com/user-role-editor-pro/';
            $text = __('Upgrade', 'wpfront-user-role-editor');
            $a = sprintf('<a style="color:red;" target="_blank" href="%s">%s</a>', $url, $text);
            array_unshift($links, $a);
            
            return $links;
        }
        
        /**
         * Displays the go pro view.
         */
        public function view() {
            if(parent::view()) {
                $objView = new $this->ViewClass();
                $objView->view($this->product);
            }
        }
        
        /**
         * Returns the label used in admin menu.
         * 
         * @return string
         */
        protected function get_menu_label() {
            return '<span class="wpfront-go-pro">' . __('Go Pro', 'wpfront-user-role-editor') . '</span>' ;
        }
        
        protected function get_page_title() {
            return __('Go Pro', 'wpfront-user-role-editor');
        }
    }
    
    add_action('plugins_loaded', array(WPFront_User_Role_Editor_Go_Pro::class, 'init'));
    
}