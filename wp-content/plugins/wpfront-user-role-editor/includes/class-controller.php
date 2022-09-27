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
 * Controllers for WPFront User Role Editor
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_Controller')) {

    /**
     * Controller class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_Controller extends WPFront_User_Role_Editor_Cache {
        
        abstract protected function setUp();
        abstract protected function initialize();
        
        protected static $keys = array();
        protected static $instances = array();
        
        protected $WPFURE;
        protected $Options;
        protected $UtilsClass;
        protected $RolesHelperClass;
        
        protected $cap;
        
        protected function __construct() {
            parent::__construct(static::class);
            
            $this->setUp();
        }
        
        protected function _setUp($cap) {
            $this->cap = $cap;
            
            if(static::class === \WPFront\URE\WPFront_User_Role_Editor::class) {
                $this->WPFURE = $this;
            } else {
                $this->WPFURE = \WPFront\URE\WPFront_User_Role_Editor::instance();
            }
            
            if(static::class === \WPFront\URE\Options\WPFront_User_Role_Editor_Options::class) {
                $this->Options = $this;
            } else {
                $this->Options = \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance();
            }
            
            $this->UtilsClass = \WPFront\URE\WPFront_User_Role_Editor_Utils::class;
            $this->RolesHelperClass = \WPFront\URE\WPFront_User_Role_Editor_Roles_Helper::class;
        }
        
        public static function instance() {
            $key = static::class;
            
            if(isset(self::$keys[$key])) {
                $key = self::$keys[$key];
            }
            
            if(isset(self::$instances[$key])) {
                return self::$instances[$key];
            }
            
            $obj = new static();
            self::$instances[$key] = $obj;
            
            return $obj;
        }
        
        public static function init() {
            static::instance()->initialize();
        }
        
        public static function load($network_admin_only = false) {
            if($network_admin_only) {
                if(!is_network_admin() && !WPFront_User_Role_Editor_Utils::doing_network_ajax()) {
                    return;
                }
            }
            
            if(method_exists(static::class, 'get_debug_setting')) {
                $debug_values = call_user_func(array(static::class, 'get_debug_setting'));
                
                $debug = WPFront_User_Role_Editor_Debug::instance();
                $debug->add_setting($debug_values['key'], $debug_values['label'], $debug_values['position'], $debug_values['description']);
                
                $disabled = $debug->is_disabled($debug_values['key']);
                
                if($disabled) {
                    return;
                }
            }
            
            $key = static::class;
            
            $firstLevel = true;
            while(true) {
                $parent = get_parent_class($key);
                $refClass = new \ReflectionClass($parent);
                if($refClass->isAbstract()) {
                    break;
                }
                $key = $parent;
                $firstLevel = false;
            }
            
            if($firstLevel) {
                add_action('wpfront_ure_init', [static::class, 'init']);
            }
            
            self::$keys[static::class] = $key;
            
            self::$instances[$key] = new static();
        }
        
        protected function in_admin_ui() {
            if($this->UtilsClass::doing_ajax()) {
                return false;
            }
            
            if(!is_admin()) {
                return false;
            }
            
            return true;
        }
        
        public function get_cap() {
            return $this->cap;
        }
        
        protected function set_cap($cap) {
            $this->cap = $cap;
        }
    
    }
    
}

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_View_Controller')) {

    /**
     * View Controller class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_View_Controller extends WPFront_User_Role_Editor_Controller {
        
        protected $ViewClass;
        protected $EntityClass;
        
        protected $menu_slug;
        
        protected $menu_title;
        protected $menu_link;

        protected function _setUp($cap, $menu_slug = '') {
            parent::_setUp($cap);
            
            $this->menu_slug = $menu_slug;
        }
        
        public function get_menu_slug() {
            return $this->menu_slug;
        }
        
        protected function set_admin_menu($title, $link, $priority = 10) {
            $this->menu_title = $title;
            $this->menu_link = $link;
            
            add_action('admin_menu', array($this, 'admin_menu'), $priority);
        }
        
        protected function set_network_admin_menu($title, $link, $priority = 10) {
            $this->menu_title = $title;
            $this->menu_link = $link;
            
            add_action('network_admin_menu', array($this, 'admin_menu'), $priority);
        }
        
        public function admin_menu() {
            $page_hook_suffix = add_submenu_page($this->WPFURE->get_parent_menu_slug($this->get_menu_slug(), $this->get_cap()), $this->menu_title, $this->menu_link, $this->get_cap(), $this->get_menu_slug(), array($this, 'view'));
        
            $this->add_menu_hooks($page_hook_suffix);
        }
        
        protected function add_menu_hooks($page_hook_suffix) {
            add_action('admin_print_styles-' . $page_hook_suffix, array($this, 'admin_print_styles'));
            add_action('admin_print_scripts-' . $page_hook_suffix, array($this, 'admin_print_scripts'));
            add_action('load-' . $page_hook_suffix, array($this, 'load_view'));
        }
        
        public function load_view() {
            if(!current_user_can($this->get_cap())) {
                $this->WPFURE->permission_denied();
                return false;
            }
            
            return true;
        }
        
        public function view() {
            if(!current_user_can($this->get_cap())) {
                $this->WPFURE->permission_denied();
                return false;
            }
            
            return true;
        }
        
        public function admin_print_styles() {
            $this->UtilsClass::enqueue_font_awesome_styles();
            wp_enqueue_style('wpfront-user-role-editor-styles', $this->WPFURE->get_asset_url('css/roles.css'), array(), $this->WPFURE::VERSION);
        }
        
        public function admin_print_scripts() {
            wp_enqueue_script('jquery');
        }
        
        public function get_self_url($params = array()) {
            $url = menu_page_url($this->get_menu_slug(), false);
            if(!empty($params)) {
                foreach ($params as $key => $value) {
                    $url = $url . "&$key=$value";
                }
            }
            
            return $url;
        }
        
    }
    
}


if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_View')) {

    /**
     * View base
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_View {
        
        protected $WPFURE;
        protected $UtilsClass;
        protected $RolesHelperClass;

        public function __construct() {
            $this->WPFURE = \WPFront\URE\WPFront_User_Role_Editor::instance();
            $this->UtilsClass = \WPFront\URE\WPFront_User_Role_Editor_Utils::class;
            $this->RolesHelperClass = \WPFront\URE\WPFront_User_Role_Editor_Roles_Helper::class;
        }
        
        protected function title($title, $add_new = array(), $search = null) {
            ?>
            <h2>
                <?php echo $title; ?>
                <?php
                if (!empty($add_new)) {
                    ?>
                    <a href="<?php echo $add_new[1]; ?>" class="add-new-h2"><?php echo $add_new[0]; ?></a>
                    <?php
                }
                if (!empty($search)) {
                    ?>
                    <span class="subtitle"><?php echo sprintf(__('Search results for "%s"', 'wpfront-user-role-editor'), $search); ?></span>
                    <?php
                }
                ?>
            </h2>
            <?php
        }
        
    }
    
}