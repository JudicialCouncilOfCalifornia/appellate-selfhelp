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
 * Template for WPFront User Role Editor Taxonomies
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Taxonomies;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor_Utils as Utils;

require_once dirname(__FILE__) . '/class-taxonomies-list-table.php';

if (!class_exists('WPFront\URE\Taxonomies\WPFront_User_Role_Editor_Taxonomies_List_View')) {

    class WPFront_User_Role_Editor_Taxonomies_List_View {

        /**
         *
         * @var WPFront_User_Role_Editor_Taxonomies 
         */
        private $controller;

        /**
         * 
         * @param WPFront_User_Role_Editor_Taxonomies $controller
         */
        public function __construct($controller) {
            $this->controller = $controller;
        }

        public function view() {
            ?>
            <div class="wrap taxonomy">
                <?php $this->title(); ?>  
                <?php $this->display_notices(); ?>
                <?php $this->filter_links(); ?>

                <?php
                $list_table = new WPFront_User_Role_Editor_Taxonomies_List_Table($this->controller);
                $list_table->prepare_items();
                ?>
                <form action="" method="get" class="search-form">
                    <input type="hidden" name="page" value="<?php echo esc_attr($this->controller::MENU_SLUG); ?>" />
                    <?php $list_table->search_box(__('Search', 'wpfront-user-role-editor'), 'taxonomies'); ?>
                </form>
                <form id="form-taxonomy" method="post">
                    <?php
                    $list_table->display();
                    ?>
                </form>
            </div>
            <?php
        }

        protected function filter_links() {
            ?>
            <ul class="subsubsub">
                <li>
                    <?php
                    $link_data = array();
                    $active_filter = $this->controller->get_active_list_filter();
                    $filter_data = $this->controller->get_list_filter_data();
                    foreach ($filter_data as $key => $value) {
                        $link_data[] = sprintf('<a href="%s" class="%s">%s <span class="count">(%s)</span></a>', esc_attr($value['url']), ($active_filter == $key ? 'current' : ''), esc_html($value['display']), esc_html($value['count']));
                    }
                    echo implode('&#160;|&#160;</li><li> ', $link_data);
                    ?>
                </li>
            </ul>
            <?php
        }

        protected function title() {
            ?>
            <h2>
                <?php
                echo __('Taxonomies', 'wpfront-user-role-editor');
                if (current_user_can('create_taxonomies')) { 
                    ?>
                    <a href="<?php echo esc_attr($this->controller->get_add_new_url()) ?>" class="add-new-h2"><?php echo __('Add New', 'wpfront-user-role-editor'); ?></a>
                    <?php
                }
                ?>
            </h2>
            <?php
        }

        protected function display_notices() {
            if (!empty($this->controller->errorMsg)) {
                Utils::notice_error($this->controller->errorMsg);
            }

            if (isset($_GET['taxonomy-activated'])) {
                $count = $_GET['taxonomy-activated'];
                Utils::notice_updated(sprintf(__('%d taxonomy(s) activated successfully.', 'wpfront-user-role-editor'), $count));
            } elseif (isset($_GET['taxonomy-deactivated'])) {
                $count = $_GET['taxonomy-deactivated'];
                Utils::notice_updated(sprintf(__('%d taxonomy(s) deactivated successfully.', 'wpfront-user-role-editor'), $count));
            } elseif (!empty($_GET['taxonomy-added'])) {
                Utils::notice_updated(__('Taxonomy added successfully.', 'wpfront-user-role-editor'));
            } elseif (!empty($_GET['taxonomy-deleted'])) {
                Utils::notice_updated(__('Taxonomy(s) deleted successfully.', 'wpfront-user-role-editor'));
            } elseif (!empty($_GET['taxonomy-restored'])) {
                Utils::notice_updated(__('Taxonomy(s) restored successfully.', 'wpfront-user-role-editor'));
            }
        }

    }

}
    