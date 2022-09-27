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
 * Entity for WPFront User Role Editor Options
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Options;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront\URE\Options\WPFront_User_Role_Editor_Options_Entity')) {

    /**
     * Options Entity
     *
     * @author Syam Mohan <syam@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Options_Entity extends \WPFront\URE\WPFront_User_Role_Editor_Entity_Base {

        private static $cache = null;

        protected function table_name_suffix() {
            return 'options';
        }

        protected function table_create_sql() {
            $table_name = $this->table_name();

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (\n"
                    . "id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n"
                    . "option_name varchar(250) DEFAULT NULL,\n"
                    . "option_value longtext DEFAULT NULL,\n"
                    . "auto_load tinyint DEFAULT 1,\n"
                    . "PRIMARY KEY  (id),\n"
                    . "KEY option_name (option_name),\n"
                    . "KEY auto_load (auto_load)\n"
                    . ") $charset_collate;";

            return $sql;
        }

        private function load_options() {
            if (self::$cache !== null) {
                return;
            }

            self::$cache = array();

            $found = false;
            $this->cache_get('role_capabilities_processed', $found);
            if (!$found) {
                global $wpdb;
                $table_name = $this->table_name();

                $sql = "SELECT option_name, option_value FROM $table_name WHERE auto_load=1";
                $results = $wpdb->get_results($sql);

                foreach ($results as $value) {
                    self::$cache[$value->option_name] = maybe_unserialize($value->option_value);
                }
            }
        }

        /**
         * Returns option value against option name.
         * 
         * @global \wpdb $wpdb
         * @param string $option_name
         * @param boolean $exists 
         * @return string
         */
        public function get_option($option_name, &$exists = null) {
            $this->load_options();

            $found = false;
            $value = $this->cache_get($option_name, $found);
            if ($value !== false) {
                $exists = true;
                return $value;
            }
            if ($found) {
                $exists = false;
                return false;
            }

            if (isset(self::$cache[$option_name])) {
                $value = self::$cache[$option_name];
                $exists = true;
            } else {
                global $wpdb;
                $table_name = $this->table_name();

                $sql = $wpdb->prepare("SELECT option_value FROM $table_name WHERE option_name = %s", $option_name);
                $value = $wpdb->get_row($sql);

                if (empty($value)) {
                    $exists = false;
                    $value = false;
                } else {
                    $value = maybe_unserialize($value->option_value);
                    $exists = true;
                }
            }

            $this->cache_set($option_name, $value);

            return $value;
        }

        /**
         * Inserts or Updates an option value.
         * 
         * @global \wpdb $wpdb
         * @param string $option_name
         * @param string $value
         * @param boolean $auto_load
         */
        public function update_option($option_name, $value, $auto_load = true) {
            if ($value === false) {
                $value = '0';
            }

            global $wpdb;
            $table_name = $this->table_name();

            $sql = $wpdb->prepare("SELECT EXISTS(SELECT 1 FROM $table_name WHERE option_name = %s)", $option_name);
            $result = $wpdb->get_var($sql);

            if (empty($result)) {
                $wpdb->insert(
                        $table_name,
                        array(
                            'option_name' => $option_name,
                            'option_value' => maybe_serialize($value),
                            'auto_load' => $auto_load
                        ),
                        array(
                            '%s',
                            '%s',
                            '%d'
                        )
                );
            } else {
                $wpdb->update(
                        $table_name,
                        array(
                            'option_value' => maybe_serialize($value),
                            'auto_load' => $auto_load
                        ),
                        array(
                            'option_name' => $option_name
                        ),
                        array(
                            '%s',
                            '%d'
                        ),
                        array(
                            '%s'
                        )
                );
            }

            $this->cache_set($option_name, $value);
        }

        /**
         * Deletes an option value against its name.
         * 
         * @global \wpdb $wpdb
         * @param string $option_name
         */
        public function delete_option($option_name) {
            global $wpdb;
            $table_name = $this->table_name();

            $this->cache_delete($option_name);
            unset(self::$cache[$option_name]);

            $wpdb->delete(
                    $table_name,
                    array(
                        'option_name' => $option_name
                    ),
                    array(
                        '%s'
                    )
            );
        }

    }

    (new WPFront_User_Role_Editor_Options_Entity())->register();
}