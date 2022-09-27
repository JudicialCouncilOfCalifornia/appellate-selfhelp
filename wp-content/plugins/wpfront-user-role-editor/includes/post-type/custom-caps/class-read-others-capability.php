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
 * Controller for WPFront User Role Editor Read Others Custom Capabilities.
 *
 * @author Jinu Varghese
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Post_Type\Custom_Caps;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type_Custom_Capability;

if (!class_exists('\WPFront\URE\Post_Type\Custom_Caps\WPFront_User_Role_Editor_Read_Others_Capability')) {

    /**
     * Read others capability class
     *
     * @author Jinu Varghese
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Read_Others_Capability extends WPFront_User_Role_Editor_Post_Type_Custom_Capability {

        protected function init($controller) {
            
        }

        /**
         * Returns custom cap prefix.
         */
        protected function cap_prefix() {
            return 'read_others';
        }
        
        /**
         * Returns cap prefix to add before in sort.
         */
        protected function add_before_prefix() {
            return 'edit_others';
        }
        
         /**
         * Returns the cap to check while defining role defaults.
         */
        protected  function role_default_value_cap($post_type){
            return true;
        }
        
        protected function can_merge() {
            return false;
        }
        
        protected function get_debug_setting() {
            return array('key' => 'read_others-custom-capability', 'label' => __('"read_others" Capabilities', 'wpfront-user-role-editor'), 'position' => 40, 'description' => __('Disables all "read_others_" capabilities.', 'wpfront-user-role-editor'));
        }


    }

    WPFront_User_Role_Editor_Post_Type_Custom_Capability::register('read_others', new WPFront_User_Role_Editor_Read_Others_Capability());
}
    