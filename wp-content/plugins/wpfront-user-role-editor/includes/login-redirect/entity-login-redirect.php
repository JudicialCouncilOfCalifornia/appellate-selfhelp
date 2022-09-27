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
 * Entity for WPFront User Role Editor Login Redirect
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Login_Redirect;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as WPFURE;
use WPFront\URE\Options\WPFront_User_Role_Editor_Options as Options;

require_once dirname(dirname(__FILE__)) . '/settings/entity-options.php';

if (!class_exists('WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect_Entity')) {

    /**
     * Login Redirect Entity
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Login_Redirect_Entity extends \WPFront\URE\WPFront_User_Role_Editor_Entity_Base {
        
        /**
         * Primary key.
         * 
         * @var int
         */
        public $id;
        
        /**
         * WP role name
         * 
         * @var string 
         */
        public $role;
        
        /**
         * Role priority.
         * 
         * @var int 
         */
        public $priority;
        
        /**
         * Login redirect URL.
         * 
         * @var string
         */
        public $url;
        
        /**
         * Logout redirect URL.
         * 
         * @var string|null
         */
        public $logout_url;
        
        /**
         * Deny WP-ADMIN access.
         * 
         * @var bool
         */
        public $deny_wpadmin;

        /**
         * Disable site tool bar.
         * 
         * @var bool
         */
        public $disable_toolbar;


        protected function table_name_suffix() {
            return 'login_redirect';
        }

        protected function table_create_sql() {
            $table_name = $this->table_name();
            
            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            
            $sql = "CREATE TABLE $table_name (\n"
                . "id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n"
                . "role varchar(250) DEFAULT NULL,\n"
                . "priority int DEFAULT NULL,\n"
                . "url varchar(2000) DEFAULT NULL,\n"
                . "deny_wpadmin bit DEFAULT NULL,\n"
                . "disable_toolbar bit DEFAULT NULL,\n"
                . "logout_url varchar(2000) DEFAULT '',\n"
                . "PRIMARY KEY  (id),\n"
                . "KEY priority (priority),\n"
                . "KEY role (role)\n"
                . ") $charset_collate;";
            
            return $sql;
        }
        
        /**
         * Adds a new login redirect.
         * 
         * @global \wpdb $wpdb
         * @return boolean Success|Fail
         */
        public function add() {
            $this->priority = intval($this->priority);
            $this->deny_wpadmin = !empty($this->deny_wpadmin);
            $this->disable_toolbar = !empty($this->disable_toolbar);
            
            global $wpdb;
            $tablename = $this->table_name();
            
            if($this->priority < 1) {
                $this->priority = 1;
            } else {
                $priority = $this->get_next_priority();
                if($this->priority > $priority) {
                    $this->priority = $priority;
                }
            }
            
            $sql = "UPDATE $tablename "
                    . "SET priority = priority + 1 "
                    . "WHERE priority >= " . $this->priority;
            $wpdb->query($sql);

            $result = $wpdb->insert(
                    $tablename, 
                    array(
                        'role' => $this->role,
                        'priority' => $this->priority,
                        'url' => $this->url,
                        'deny_wpadmin' => $this->deny_wpadmin,
                        'disable_toolbar' => $this->disable_toolbar,
                        'logout_url' => $this->logout_url
                    ), 
                    array(
                        '%s',
                        '%d',
                        '%s',
                        '%d',
                        '%d',
                        '%s'
                    )
                );

            $this->cache_flush();
            
            if($result === false) {
                return false;
            } else {
                $this->id = $wpdb->insert_id;
                return true;
            }
            
        }
        
        /**
         * Updates an existing login redirect based on ID.
         * 
         * @global \wpdb $wpdb
         * @return boolean Success|Fail
         */
        public function update() {
            $this->priority = intval($this->priority);
            $this->deny_wpadmin = !empty($this->deny_wpadmin);
            $this->disable_toolbar = !empty($this->disable_toolbar);
            
            global $wpdb;
            $tablename = $this->table_name();
            
            $sql = "SELECT priority FROM $tablename WHERE id = $this->id";
            $db_priority = $wpdb->get_var($sql);
            if(empty($db_priority)) {
                return false;
            }
            
            if ($db_priority < $this->priority) {
                $sql = "UPDATE $tablename "
                        . "SET priority = priority - 1 "
                        . "WHERE priority > $db_priority AND priority <= $this->priority";
                $wpdb->query($sql);
            }

            if ($db_priority > $this->priority) {
                $sql = "UPDATE $tablename "
                        . "SET priority = priority + 1 "
                        . "WHERE priority >= $this->priority AND priority < $db_priority";
                $wpdb->query($sql);
            }
            
            if($this->priority < 1) {
                $this->priority = 1;
            } else {
                $priority = $this->get_next_priority();
                if($this->priority > $priority) {
                    $this->priority = $priority;
                }
            }
            
            $result = $wpdb->update(
                    $tablename, 
                    array(
                        'role' => $this->role,
                        'priority' => $this->priority,
                        'url' => $this->url,
                        'deny_wpadmin' => $this->deny_wpadmin,
                        'disable_toolbar' => $this->disable_toolbar,
                        'logout_url' => $this->logout_url
                    ),
                    array(
                        'id' => $this->id
                    ),
                    array(
                        '%s',
                        '%d',
                        '%s',
                        '%d',
                        '%d',
                        '%s'
                    ),
                    array(
                        '%d'
                    )
                );

            $this->cache_flush();
            
            return !empty($result);
        }
        
        /**
         * Deletes a login redirect based role name.
         * 
         * @param string $role
         * @return bool
         */
        public function delete($role) {
            global $wpdb;
            $tablename = $this->table_name();
            
            $sql = "SELECT priority "
                    . "FROM $tablename "
                    . "WHERE role = %s";
            $current_priority = $wpdb->get_var($wpdb->prepare($sql, $role));
            if(empty($current_priority)) {
                return false;
            }

            $sql = "UPDATE $tablename "
                    . "SET priority = priority - 1 "
                    . "WHERE priority > %d";
            $wpdb->query($wpdb->prepare($sql, $current_priority));
            
            $sql = "DELETE FROM $tablename WHERE role = %s";
            $sql = $wpdb->prepare($sql, $role);
            $result = $wpdb->query($sql);
            
            $this->cache_flush();
            
            return !empty($result);
        }
        
        /**
         * Returns the next priority value.
         * 
         * @global \wpdb $wpdb
         * @return int
         */
        public function get_next_priority() {
            $data = $this->cache_get('next_priority');
            if($data !== false) {
                return $data;
            }
            
            $tablename = $this->table_name();
            $sql = "SELECT MAX(priority) FROM $tablename";

            global $wpdb;
            $result = $wpdb->get_var($sql);

            $data = intval($result) + 1;
            
            $this->cache_set('next_priority', $data);
            
            return $data;
        }
        
        /**
         * Returns all login redirects as associative array.
         * 
         * @global \wpdb $wpdb
         * @return \WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Login_Redirect_Entity[] priority => WPFront_User_Role_Editor_Login_Redirect_Entity.
         */
        public function get_all_login_redirects() {
            $data = $this->cache_get('all_login_redirects');
            if($data !== false) {
                return $data;
            }
            
            $table_name = $this->table_name();
            
            $sql = "SELECT id, role, priority, url, logout_url, (deny_wpadmin + 0) AS deny_wpadmin, (disable_toolbar + 0) AS disable_toolbar "
                . "FROM $table_name "
                . "ORDER BY priority";
            
            global $wpdb;
            $results = $wpdb->get_results($sql);
            
            $data = array();
            foreach ($results as $value) {
                $entity = new WPFront_User_Role_Editor_Login_Redirect_Entity();
                
                $entity->id = intval($value->id);
                $entity->role = $value->role;
                $entity->priority = intval($value->priority);
                $entity->url = $value->url;
                $entity->deny_wpadmin = (bool)$value->deny_wpadmin;
                $entity->disable_toolbar = (bool)$value->disable_toolbar;
                $entity->logout_url = $value->logout_url;
                
                $data[$entity->priority] = $entity;
            }
            
            $this->cache_set('all_login_redirects', $data);
            
            return $data;
        }
        
        protected function cache_flush() {
            $this->cache_delete('all_login_redirects');
            $this->cache_delete('next_priority');
        }
        
    }
    
    (new WPFront_User_Role_Editor_Login_Redirect_Entity())->register();

}