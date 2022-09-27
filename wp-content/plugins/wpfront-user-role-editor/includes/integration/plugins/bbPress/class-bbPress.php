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

namespace WPFront\URE\Integration;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor_Roles_Helper as RolesHelper;

if (!class_exists('\WPFront\URE\Integration\WPFront_User_Role_Editor_bbPress_Integration')) {

    /**
     * bbPress Integration
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_bbPress_Integration {

        protected static $caps_mapping = array(
            'create_forums' => 'edit_forums',
            'read_others_forums' => 'spectate',
            'create_topics' => 'edit_topics',
            'read_others_topics' => 'spectate',
            'create_replies' => 'edit_replies',
            'read_others_replies' => 'spectate'
        );
        
        protected function __construct() {
            
        }


        public static function init() {
            $instance = new WPFront_User_Role_Editor_bbPress_Integration();
            
            add_filter('bbp_get_caps_for_role', array($instance, 'bbp_get_caps_for_role'), 10, 2);
        }
        
        /**
         * 
         * @param (string|bool)[] $caps
         * @param string $role
         * @return string[]
         */
        public function bbp_get_caps_for_role($caps, $role) {
            foreach (self::$caps_mapping as $cap => $check) {
                if(isset($caps[$check])) {
                    $caps[$cap] = $caps[$check];
                }
            }
            
            return $caps;
        }
        
    }
    
    WPFront_User_Role_Editor_bbPress_Integration::init();

}


