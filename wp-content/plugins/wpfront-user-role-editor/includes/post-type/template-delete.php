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
 * Template for WPFront User Role Editor Post Type Delete
 *
 * @author Vaisagh D <vaisaghd@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Post_Type;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\WPFront_User_Role_Editor as URE;
use WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type as PostType;

if (!class_exists('WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type_Delete_View')) {

    class WPFront_User_Role_Editor_Post_Type_Delete_View {

        /**
         *
         * @var WPFront_User_Role_Editor_Post_Type 
         */
        private $controller;
        private $post_type_entitties;
        private $display = array();

        public function __construct($controller, $entities, $view = 'delete') {
            $this->controller = $controller;
            $this->post_type_entitties = $entities;
            
            $this->display['action'] = $view;
            switch ($view) {
                case 'restore':
                    $this->display['url'] = $this->controller->get_restore_url(null);
                    $this->display['button_text'] = __('Confirm Restore', 'wpfront-user-role-editor');
                    $this->display['title'] = __('Restore Post Types', 'wpfront-user-role-editor');
                    $this->display['description'] = __('The following Post Types will be restored.', 'wpfront-user-role-editor');
                    break;
                
                default:
                    $this->display['url'] = $this->controller->get_delete_url(null);
                    $this->display['button_text'] = __('Confirm Delete', 'wpfront-user-role-editor');
                    $this->display['title'] = __('Delete Post Types', 'wpfront-user-role-editor');
                    $this->display['description'] = __('The following Post Types will be deleted.', 'wpfront-user-role-editor');
                    break;
            }
        }

        public function view() {
            ?>
            <div class="wrap post-type">
                <?php
                $this->title();
                ?>
                <form id="form-post-type" method="post" action="<?php echo esc_attr($this->display['url']); ?>">
                    <ol>
                        <?php $this->post_types_display(); ?>
                    </ol>   
                    <input type="hidden" name="action" value="<?php echo esc_attr($this->display['action']); ?>" />
                    <?php
                    wp_nonce_field('bulk-action-view-post');
                    submit_button($this->display['button_text'], 'button-secondary');
                    ?>
                </form>
            </div>
            <?php
        }

        protected function title() {
            ?>
            <h2>
                <?php echo esc_html($this->display['title']); ?>
                <p><?php echo esc_html($this->display['description']); ?></p>
            </h2>
            <?php
        }

        protected function post_types_display() {
            foreach ($this->post_type_entitties as $entity) {
                $post_type_label = $entity->label;
                $post_type_name = $entity->name;
                
                echo "<li>".esc_html($post_type_label)." [".esc_html($post_type_name)."]</li>";
                echo "<input type='hidden' name='post_types[]' value='".esc_attr($post_type_name)."' />";
            }
        }

    }

}

