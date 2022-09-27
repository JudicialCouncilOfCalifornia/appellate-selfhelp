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
 * Controller for WPFront User Role Editor Bulk Edit
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Bulk_Edit;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;
use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;
use \WPFront\URE\WPFront_User_Role_Editor_Debug;

require_once dirname(__FILE__) . '/template-bulk-edit.php';
require_once dirname(__FILE__) . '/class-bulk-edit-utils.php';

if (!class_exists('\WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit')) {

    /**
     * Bulk Edit class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Bulk_Edit extends \WPFront\URE\WPFront_User_Role_Editor_View_Controller {
        const MENU_SLUG = 'wpfront-user-role-editor-bulk-edit';
        const CAP = 'bulk_edit_roles';

        /**
         *
         * @var iWPFront_URE_Bulk_Edit_Controller[] 
         */
        protected $controllers = null;
        
        /**
         *
         * @var iWPFront_URE_Bulk_Edit_Controller 
         */
        protected $current_controller = null;


        protected function setUp() {
            $this->_setUp('bulk_edit_roles', 'wpfront-user-role-editor-bulk-edit');
        }
        
        protected function initialize() {
            $debug = WPFront_User_Role_Editor_Debug::instance();
            $debug->add_setting('bulk-edit', __('Bulk Edit', 'wpfront-user-role-editor'), 120, __('Disables bulk edit functionality.', 'wpfront-user-role-editor'));
            
            if($debug->is_disabled('bulk-edit')) {
                return;
            }
            
            if(!is_admin()) {
                return;
            }
            
            $controllers = self::instance()->get_controllers();
            if(empty($controllers)) {
                return;
            }
            
            $this->set_admin_menu(__('Bulk Edit', 'wpfront-user-role-editor'), __('Bulk Edit', 'wpfront-user-role-editor'), 70);
        }
        
        /**
         * Allow controllers to completely init from wpfront_ure_init.
         */
        public static function add_hook() {
            add_action('wp_loaded', '\WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit::init');
        }
        
        /**
         * Apply filter to find controllers and returns.
         * 
         * @return iWPFront_URE_Bulk_Edit_Controller[]
         */
        public function get_controllers() {
            if($this->controllers === null) {
                $this->controllers = apply_filters('wpfront_ure_bulk_edit_controllers', array());
                
                $controllers = array();
                foreach ($this->controllers as $ctrl) {
                    $cap = $ctrl->get_cap();
                    
                    if(current_user_can($cap)) {
                        $controllers[] = $ctrl;
                    }
                }
                
                $this->controllers = $controllers;
            }
            
            return $this->controllers;
        }
        
        public function admin_print_styles() {
            if(!empty($this->current_controller)) {
                $this->current_controller->admin_print_styles();
            } else {
                wp_enqueue_style('wpfront-user-role-editor-styles', WPFURE::instance()->get_asset_url('css/roles.css'), array(), WPFURE::VERSION);
            }
        }
        
        public function admin_print_scripts() {
            if(!empty($this->current_controller)) {
                $this->current_controller->admin_print_scripts();   
            }
        }
        
        /**
         * Hooks on 'load-view' and sets the current controller.
         */
        public function load_view() {
            if(!parent::load_view()) {
                return;
            }
            
            if(isset($_GET['screen'])) {
                $screen = $_GET['screen'];
                $controllers = $this->get_controllers();
                foreach ($controllers as $ctrl) {
                    if($ctrl->get_key() === $screen) {
                        $this->current_controller = $ctrl;
                        break;
                    }
                }
            }
            
            if(!empty($this->current_controller)) {
                $this->current_controller->load_view();
            }
        }
        
        /**
         * Displays the bulk edit view.
         */
        public function view() {
            if(!parent::view()) {
                return;
            }
            
            if(!empty($this->current_controller)) {
                $this->current_controller->view();
                return;
            }
            
            $objView = new WPFront_User_Role_Editor_Bulk_Edit_View();
            $objView->view();
        }
        
        /**
         * Returns the bulk edit screen URL for a controller.
         * 
         * @param iWPFront_URE_Bulk_Edit_Controller $controller
         * @return string
         */
        public function get_screen_url($controller) {
            return $this->get_self_url(['screen' => $controller->get_key()]);
        }
        
    }
    
    add_action('wpfront_ure_init', '\WPFront\URE\Bulk_Edit\WPFront_User_Role_Editor_Bulk_Edit::add_hook');
    
    interface iWPFront_URE_Bulk_Edit_Controller {
        public function get_cap();
        public function get_key();
        public function get_option_text();
        public function load_view();
        public function view();
        public function admin_print_styles();
        public function admin_print_scripts();
    }
    
}