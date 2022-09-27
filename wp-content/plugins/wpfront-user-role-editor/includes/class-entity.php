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
 * Entity base for WPFront User Role Editor
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('\WPFront\URE\WPFront_User_Role_Editor_Entity_Base')) {

    /**
     * Entity base class
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    abstract class WPFront_User_Role_Editor_Entity_Base extends \WPFront\URE\WPFront_User_Role_Editor_Cache {
        
        const DB_VERSION_OPTION_KEY = 'wpfront-user-role-editor-db-version';
        
        private $table_name;
        
        protected abstract function table_name_suffix();
        protected abstract function table_create_sql();
        
        public function __construct() {
            $table_name_suffix = $this->table_name_suffix();
            $this->table_name = 'wpfront_ure_' . $table_name_suffix;
            parent::__construct($table_name_suffix);
            
            $table_version_key = $this->db_version_key();
            if(empty($this->cache_get("entity-$table_version_key"))) {
                if($this->dbDelta()) {
                    $this->cache_set("entity-$table_version_key", true);
                }
            }
        }
        
        protected function table_name() {
            global $wpdb;
            return $wpdb->prefix . $this->table_name;
        }
        
        protected function dbDelta() {
            if (defined('WP_UNINSTALL_PLUGIN')) {
                return false;
            }
            
            $table_version_key = $this->db_version_key();
            
            $db_version = $this->get_db_version($table_version_key);
            if (empty($db_version)) {
                $db_version = '0.0';
            }
            
            if (version_compare($db_version, \WPFront\URE\WPFront_User_Role_Editor::VERSION, '>=')) {
                return true;
            }
            
            $this->pre_custom_upgrade_script();
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $sql = $this->table_create_sql();
            
            dbDelta($sql);
            
            global $wpdb;
            $table_name = $this->table_name();
            
            $sql = "SHOW TABLE STATUS where name like '$table_name'";
            $status = $wpdb->get_row($sql);
            if(empty($status)) {
                return false;
            }
            
            $length = strlen('utf8mb4_unicode');
            if(substr($status->Collation, 0, $length) !== 'utf8mb4_unicode') {
                $sql = "ALTER TABLE $table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci;";
                $result = $wpdb->query($sql);
                if($result === false) {
                    error_log("Unable to set collation for table $table_name. Error - {$wpdb->last_error}");
                }
            }
            
            $this->drop_index_id();
            $this->custom_upgrade_script();
            
            $this->set_db_version($table_version_key, \WPFront\URE\WPFront_User_Role_Editor::VERSION);
            
            return true;
        }
        
        protected function drop_index_id() {
            $table_version_key = $this->db_version_key();
            $db_version = $this->get_db_version($table_version_key);
            
            if(empty($db_version)) { //new install
                return;
            }
            
            global $wpdb;
            $table_name = $this->table_name();
            $sql = "SHOW INDEX FROM $table_name WHERE Key_name='id'";
            $result = $wpdb->get_row($sql);
            
            if (!empty($result)) {
                $wpdb->query("ALTER TABLE {$this->table_name()} DROP INDEX id;");
            }
        }
        
        protected function custom_upgrade_script() {
        }
        
        protected function pre_custom_upgrade_script() {
        }
        
        private function db_version_key() {
            if(static::class === \WPFront\URE\Options\WPFront_User_Role_Editor_Options_Entity::class) {
                return self::DB_VERSION_OPTION_KEY;
            }
            
            return $this->table_name() . '-db-version';
        }
        
        private function get_db_version($key) {
            if(static::class === \WPFront\URE\Options\WPFront_User_Role_Editor_Options_Entity::class) {
                return get_option($key);
            }
            
            return \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance()->get_option($key);
        }
        
        private function set_db_version($key, $value) {
            if(static::class === \WPFront\URE\Options\WPFront_User_Role_Editor_Options_Entity::class) {
                update_option($key, $value);
                return;
            }
            
            \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance()->set_option($key, $value);
        }
        
        private function delete_db_version($key) {
            if(static::class === \WPFront\URE\Options\WPFront_User_Role_Editor_Options_Entity::class) {
                delete_option($key);
            }
            //happens only on uninstall doesn't need.
            //\WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance()->delete_option($key);
        }
        
        public function uninstall() {
            $this->delete_db_version($this->db_version_key());
            
            global $wpdb;
            $table_name = $this->table_name();

            $sql = "DROP TABLE IF EXISTS $table_name";
            $wpdb->query($sql);
        }
        
        public function register() {
            WPFront_User_Role_Editor_Uninstall::register_entity($this);
        }
        
    }

}