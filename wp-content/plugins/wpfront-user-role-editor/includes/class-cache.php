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
 * Cache for WPFront User Role Editor
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_Cache')) {

    /**
     * Cache class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_Cache {
        
        private $global_group = null;
        private $group = null;

        protected function __construct($group, $persist = true) {
            $this->group = 'wpfront_ure_' . $group . '_' . \WPFront\URE\WPFront_User_Role_Editor::VERSION;
            
            if(!$persist) {
                wp_cache_add_non_persistent_groups($this->group);
            }
        }
        
        protected function cache_set($key, $data) {
            if(defined('WPFURE_DISABLE_OBJECT_CACHE') && WPFURE_DISABLE_OBJECT_CACHE) {
                return;
            }
            
            wp_cache_set($key, $data, $this->group);
        }
        
        protected function cache_get($key, &$found = null) {
            if(defined('WPFURE_DISABLE_OBJECT_CACHE') && WPFURE_DISABLE_OBJECT_CACHE) {
                return false;
            }
            
            return wp_cache_get($key, $this->group, false, $found);
        }
        
        protected function cache_delete($key) {
            wp_cache_delete($key, $this->group);
        }
        
        protected function cache_global_set($key, $data) {
            if(defined('WPFURE_DISABLE_OBJECT_CACHE') && WPFURE_DISABLE_OBJECT_CACHE) {
                return;
            }
            
            if(empty($this->global_group)) {
                $this->global_group = 'global_' . $this->group;
                wp_cache_add_global_groups($this->global_group);
            }
            
            wp_cache_set($key, $data, $this->global_group);
        }
        
        protected function cache_global_get($key, &$found = null) {
            if(defined('WPFURE_DISABLE_OBJECT_CACHE') && WPFURE_DISABLE_OBJECT_CACHE) {
                $found = false;
                return false;
            }
            
            if(empty($this->global_group)) {
                $found = false;
                return false;
            }
            
            return wp_cache_get($key, $this->global_group, false, $found);
        }
        
        protected function cache_global_delete($key) {
            if(empty($this->global_group)) {
                return;
            }
            
            wp_cache_delete($key, $this->global_group);
        }
        
    }
    
}