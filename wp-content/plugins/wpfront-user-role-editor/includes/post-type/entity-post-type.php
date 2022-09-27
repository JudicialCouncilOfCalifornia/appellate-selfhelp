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
 * Entity for WPFront User Role Editor Post Type
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Post_Type;

if (!defined('ABSPATH')) {
    exit();
}

if (!class_exists('WPFront\URE\Login_Redirect\WPFront_User_Role_Editor_Post_Type_Entity')) {

    /**
     * Post Type Entity
     *
     * @author Vaisagh D <vaisaghd@wpfront.com>
     * @copyright 2014 WPFront.com
     */
    class WPFront_User_Role_Editor_Post_Type_Entity extends \WPFront\URE\WPFront_User_Role_Editor_Entity_Base {

        /**
         * Primary key.
         * 
         * @var int
         */
        public $id;

        /**
         * Post Type Name.
         * 
         * @var text
         */
        public $name;

        /**
         * Post Type Label.
         * 
         * @var string 
         */
        public $label;

        /**
         * Post Type Status.
         * 
         * @var int 
         */
        public $status;

        /**
         * Post Type Object.
         * 
         * @var \WP_Post_Type 
         */
        public $post_type_arg;

        /**
         * Post Type Taxonomy.
         * 
         * @var array 
         */
        public $taxonomies;

        /**
         * Post Type Capability.
         * 
         * @var string
         */
        public $capability_type;

        protected function table_name_suffix() {
            return 'post_type';
        }

        protected function table_create_sql() {
            $table_name = $this->table_name();

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $table_name (\n"
                    . "id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,\n"
                    . "name varchar(150) DEFAULT NULL,\n"
                    . "label varchar(250) DEFAULT NULL,\n"
                    . "status int NOT NULL DEFAULT 1,\n"
                    . "post_type_arg text DEFAULT NULL,\n"
                    . "taxonomies varchar(1000) NOT NULL DEFAULT '',\n"
                    . "capability_type varchar(1000) DEFAULT NULL,\n"
                    . "PRIMARY KEY  (id),\n"
                    . "UNIQUE KEY name (name)\n"
                    . ") $charset_collate;";

            return $sql;
        }

        /**
         * Adds a new post type.
         * 
         * @global \wpdb $wpdb
         * @return boolean Success|Fail
         */
        public function add() {
            global $wpdb;
            $tablename = $this->table_name();

            if (!is_array($this->taxonomies)) {
                $this->taxonomies = [];
            }

            $result = $wpdb->insert(
                    $tablename,
                    array(
                        'name' => $this->name,
                        'label' => $this->label,
                        'status' => $this->status,
                        'post_type_arg' => maybe_serialize($this->post_type_arg),
                        'taxonomies' => implode(',', $this->taxonomies),
                        'capability_type' => maybe_serialize($this->capability_type)
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s'
                    )
            );

            $this->sync($this->name, $this->status === 1 ? $this->taxonomies : []);

            $this->cache_flush();

            if ($result === false) {
                return false;
            } else {
                $this->id = $wpdb->insert_id;
                return true;
            }
        }

        /**
         * Updates an existing post type based on ID.
         * 
         * @global \wpdb $wpdb
         * @return boolean Success|Fail
         */
        public function update() {
            global $wpdb;
            $tablename = $this->table_name();

            $sql = $wpdb->prepare("SELECT id FROM $tablename WHERE name = %s", $this->name);
            $id = $wpdb->get_var($sql);
            if (empty($id)) {
                return $this->add();
            }
            $this->id = $id;

            if (!is_array($this->taxonomies)) {
                $this->taxonomies = [];
            }

            $result = $wpdb->update(
                    $tablename,
                    array(
                        'name' => $this->name,
                        'label' => $this->label,
                        'status' => $this->status,
                        'post_type_arg' => maybe_serialize($this->post_type_arg),
                        'taxonomies' => implode(',', $this->taxonomies),
                        'capability_type' => maybe_serialize($this->capability_type)
                    ),
                    array(
                        'id' => $this->id
                    ),
                    array(
                        '%s',
                        '%s',
                        '%d',
                        '%s',
                        '%s',
                        '%s'
                    ),
                    array(
                        '%d'
                    )
            );

            $this->sync($this->name, $this->status === 1 ? $this->taxonomies : []);

            $this->cache_flush();

            return $result !== false;
        }

        /**
         * Deletes a post type.
         * 
         * @param string $role
         * @return bool
         */
        public function delete($action = 'delete') {
            global $wpdb;
            $tablename = $this->table_name();

            $sql = "DELETE FROM $tablename WHERE id = %d";
            $sql = $wpdb->prepare($sql, $this->id);
            $result = $wpdb->query($sql);

            $this->sync($this->name, $action === 'delete' ? [] : $this->taxonomies);

            $this->cache_flush();

            return !empty($result);
        }

        /**
         * Returns all post types.
         * 
         * @param string $role
         * @return bool
         */
        public function get_all() {
            $data = $this->cache_get('all_post_types');
            if ($data !== false) {
                return $data;
            }

            $table_name = $this->table_name();

            $sql = "SELECT id, name, label, status, post_type_arg, taxonomies, capability_type "
                    . "FROM $table_name ";

            global $wpdb;
            $results = $wpdb->get_results($sql);

            $data = array();
            foreach ($results as $value) {
                $entity = new WPFront_User_Role_Editor_Post_Type_Entity();

                $entity->id = intval($value->id);
                $entity->name = $value->name;
                $entity->label = $value->label;
                $entity->status = intval($value->status);
                $entity->post_type_arg = maybe_unserialize($value->post_type_arg);
                $entity->taxonomies = empty($value->taxonomies) ? [] : explode(',', $value->taxonomies);
                $entity->capability_type = maybe_unserialize($value->capability_type);

                $data[$entity->name] = $entity;
            }

            $this->cache_set('all_post_types', $data);

            return $data;
        }

        /**
         * Activates/Deactivates a post type.
         * 
         * @return bool
         */
        public function update_status($status) {
            global $wpdb;
            $tablename = $this->table_name();

            $sql = "UPDATE $tablename SET status=%d WHERE id=%d";
            $sql = $wpdb->prepare($sql, $status, $this->id);
            $result = $wpdb->query($sql);

            $this->sync($this->name, $status === 1 ? $this->taxonomies : []);

            $this->cache_flush();

            return !empty($result);
        }

        public function sync_taxonomies($taxonomy, $post_types) {
            $tablename = $this->table_name();

            $sql = "SELECT id, name, taxonomies FROM $tablename";
            global $wpdb;
            $results = $wpdb->get_results($sql);

            foreach ($results as $obj) {
                $taxonomies = empty($obj->taxonomies) ? [] : explode(',', $obj->taxonomies);
                $update = false;

                if (in_array($obj->name, $post_types)) { //post_type is present in the list passed, add taxonomy if not exists.
                    if (!in_array($taxonomy, $taxonomies)) { //taxonomy do not exists in data, add it.
                        $taxonomies[] = $taxonomy;
                        $update = true;
                    }
                } else { //post type not present in the list passed, so remove if taxonomy exists.
                    if (in_array($taxonomy, $taxonomies)) { //taxonomy exists in data, remove it.
                        $taxonomies = array_diff($taxonomies, [$taxonomy]);
                        $update = true;
                    }
                }

                if ($update) {
                    $wpdb->update(
                            $tablename,
                            array(
                                'taxonomies' => implode(',', $taxonomies)
                            ),
                            array(
                                'id' => $obj->id
                            ),
                            array(
                                '%s'
                            ),
                            array(
                                '%d'
                            )
                    );
                }
            }

            $this->cache_flush();
        }

        protected function sync($post_type, $taxonomies) {
            $entity = new \WPFront\URE\Taxonomies\WPFront_User_Role_Editor_Taxonomies_Entity();
            $entity->sync_post_types($post_type, $taxonomies);
        }

        protected function cache_flush() {
            $this->cache_delete('all_post_types');

            $options = \WPFront\URE\Options\WPFront_User_Role_Editor_Options::instance();
            $options->set_option(WPFront_User_Role_Editor_Post_Type::DATA_EDITED_KEY, true);
        }

    }

    (new WPFront_User_Role_Editor_Post_Type_Entity())->register();
}