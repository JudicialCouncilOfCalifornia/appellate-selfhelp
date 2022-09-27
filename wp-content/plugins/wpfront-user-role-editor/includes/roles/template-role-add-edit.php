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
 * Template for WPFront User Role Editor Role Add/Edit
 *
 * @author Syam Mohan <syam@wpfront.com>
 * @copyright 2014 WPFront.com
 */

namespace WPFront\URE\Roles;

if (!defined('ABSPATH')) {
    exit();
}

use WPFront\URE\Taxonomies\WPFront_User_Role_Editor_Taxonomies;
use WPFront\URE\Post_Type\WPFront_User_Role_Editor_Post_Type;

if (!class_exists('WPFront\URE\Roles\WPFront_User_Role_Editor_Role_Add_Edit_View')) {

    class WPFront_User_Role_Editor_Role_Add_Edit_View extends \WPFront\URE\WPFront_User_Role_Editor_View {

        protected $RoleAddEdit;

        public function __construct() {
            parent::__construct();

            $this->RoleAddEdit = WPFront_User_Role_Editor_Role_Add_Edit::instance();
        }

        public function view() {
            add_thickbox();
            ?>
            <div class="wrap role-add-new">
                <?php $this->title(__('Add New Role', 'wpfront-user-role-editor')); ?>
                <?php $this->display_notices(); ?>
                <?php $this->display_errors(); ?>
                <?php $this->display_description(); ?>
                <form method="post" id="createuser" name="createuser" class="validate">
                    <?php wp_nonce_field('add-new-role'); ?>
                    <?php $this->display_name_fields(); ?>
                    <?php $this->display_subhead_controls(); ?>
                    <div class="metabox-holder">
                        <?php $this->create_meta_boxes(); ?>
                    </div>
                    <?php
                    $this->submit_button();

                    $role_data = $this->RoleAddEdit->get_role_data();
                    $role_name = $role_data['role_name'];
                    $read_only = $role_data['is_readonly'];
                    ?>
                    <input type="hidden" id="role-add-edit-role-name" name="role-add-edit-role-name" value="<?php echo esc_attr($role_name); ?>" />
                    <input type="hidden" id="role-add-edit-is-readonly" name="role-add-edit-is-readonly" value="<?php echo esc_attr($read_only); ?>" />
                </form>
            </div>
            <?php $this->scripts(); ?>
            <?php
        }

        protected function title($title, $add_new = array(), $search = null) {
            $is_edit_role = $this->RoleAddEdit->edit_role();

            if ($is_edit_role) {
                $title = __('Edit Role', 'wpfront-user-role-editor');

                if (current_user_can('create_roles')) {
                    $add_new[0] = __('Add New', 'wpfront-user-role-editor');
                    $add_new[1] = $this->RoleAddEdit->get_add_new_role_url();
                }
            }

            parent::title($title, $add_new);
        }

        protected function display_description() {
            $is_edit_role = $this->RoleAddEdit->edit_role();
            if (!$is_edit_role) {
                printf('<p>%s</p>', __('Create a brand new role and add it to this site.', 'wpfront-user-role-editor'));
            }
        }

        protected function display_errors() {
            $role_data = $this->RoleAddEdit->get_role_data();
            if (!empty($role_data['error'])) {
                $this->UtilsClass::notice_error($role_data['error']);
            }
        }

        protected function display_notices() {
            if (!empty($_GET['role-added']) && $_GET['role-added'] === 'true') {
                $this->UtilsClass::notice_updated(__('New role added.', 'wpfront-user-role-editor'));
            } elseif (!empty($_GET['role-updated']) && $_GET['role-updated'] === 'true') {
                $this->UtilsClass::notice_updated(__('Role updated.', 'wpfront-user-role-editor'));
            }
        }

        protected function display_name_fields() {
            ?>
            <table class="form-table">
                <tbody>
                    <tr class="form-field form-required <?php echo $this->is_display_name_valid() ? '' : 'form-invalid' ?>">
                        <th scope="row">
                            <label for="display_name">
                                <?php echo __('Display Name', 'wpfront-user-role-editor'); ?> <span class="description">(<?php echo __('required', 'wpfront-user-role-editor'); ?>)</span>
                            </label>
                        </th>
                        <td>
                            <input name="display_name" type="text" id="display_name" value="<?php echo esc_attr($this->get_role_display_name()); ?>" aria-required="true" <?php echo $this->is_role_display_name_disabled() ? 'disabled' : ''; ?> />
                        </td>
                    </tr>
                    <tr class="form-field form-required <?php echo $this->is_role_name_valid() ? '' : 'form-invalid' ?>">
                        <th scope="row">
                            <label for="role_name">
                                <?php echo __('Role Name', 'wpfront-user-role-editor'); ?> <span class="description">(<?php echo __('required', 'wpfront-user-role-editor'); ?>)</span>
                            </label>
                        </th>
                        <td>
                            <input name="role_name" type="text" id="role_name" value="<?php echo esc_attr($this->get_role_name()); ?>" aria-required="true" <?php echo $this->is_role_name_disabled() ? 'disabled' : ''; ?> />
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
        }

        protected function display_subhead_controls() {
            $disabled = $this->is_sub_controls_disabled();
            ?>
            <table class="form-table sub-head">
                <tbody>
                    <tr>
                        <th class="sub-head">
                            <h3> <?php echo __('Capabilities', 'wpfront-user-role-editor'); ?></h3>
                        </th>
                        <td class="sub-head-controls">
                            <div>
                                <label class="view-type button button-secondary <?php echo $disabled ? 'disabled' : ''; ?>"><input class="view-type" name="view-type" type="radio" checked="true" value="allow" <?php echo $disabled ? 'disabled' : ''; ?> /><?php echo __('Allow Caps View', 'wpfront-user-role-editor') ?></label>
                                <label class="view-type button button-secondary <?php echo $disabled ? 'disabled' : ''; ?>"><input class="view-type" name="view-type" type="radio" value="deny" <?php echo $disabled ? 'disabled' : ''; ?> /><?php echo __('Deny Caps View', 'wpfront-user-role-editor') ?></label>
                                <div class="spacer"></div>
                                <select <?php echo $disabled ? 'disabled' : ''; ?>>
                                    <option value=""><?php echo __('Copy from', 'wpfront-user-role-editor'); ?></option>
                                    <?php
                                    $roles = $this->get_copy_from_roles();
                                    foreach ($roles as $name => $display) {
                                        printf('<option value="%s">%s</option>', esc_attr($name), esc_html($display));
                                    }
                                    ?>
                                </select>
                                <input type="button" id="cap_apply" name="cap_apply" class="button action" value="<?php echo __('Apply', 'wpfront-user-role-editor'); ?>" <?php echo $disabled ? 'disabled' : ''; ?> />  
                            </div>
                            <div class="spacer"></div>
                            <div class="select-all-none">
                                <input type="button" class="button action chk-helpers select-all" value="<?php echo __('Select All', 'wpfront-user-role-editor'); ?>" <?php echo $disabled ? 'disabled' : ''; ?> />               
                                <input type="button" class="button action chk-helpers select-none" value="<?php echo __('Select None', 'wpfront-user-role-editor'); ?>" <?php echo $disabled ? 'disabled' : ''; ?> />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php
        }

        protected function create_meta_boxes() {
            $groups = $this->RoleAddEdit->get_meta_box_groups();

            foreach ($groups as $group_name => $value) {
                if (empty($value->caps)) {
                    continue;
                }

                //TODO:do not display disabled on add screen???
//                if($value->disabled && $value->mode === 'ADD') {
//                    continue;
//                }

                add_meta_box("postbox-$group_name", $this->postbox_title($value), array($this, 'postbox_render'), $this->RoleAddEdit->get_menu_slug(), 'normal', 'default', $value);
            }

            do_meta_boxes($this->RoleAddEdit->get_menu_slug(), 'normal', null);

            wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
            wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        }

        public function postbox_render($context, $args) {
            $value = $args['args'];
            
            $disabled = false;
            $role_data = $this->RoleAddEdit->get_role_data();
            if (!empty($role_data)) {
                $disabled = $role_data['is_readonly'];
            }
            ?>
            <div class="main <?php echo $value->deprecated ? 'deprecated' : 'active'; ?> <?php echo $value->hidden ? 'hidden' : 'visible';?>">
                <?php
                if ($value->group_obj->type === 'custom_post' && $value->caps === 'defaulted') {
                    echo WPFront_User_Role_Editor_Post_Type::instance()->get_cpt_customizable_hint_text($value->group_obj, $disabled);
                } elseif ($value->group_obj->type === 'taxonomy' && $value->caps === 'defaulted') {
                    echo WPFront_User_Role_Editor_Taxonomies::instance()->get_taxonomy_customizable_hint_text($value->group_obj, $disabled);
                } else {
                    $this->render_caps($value);
                }
                ?>
            </div>
            <?php
        }

        public function render_caps($value) {
            foreach ($value->caps as $cap) {
                $enabled = apply_filters("wpfront_ure_capability_{$cap}_functionality_enabled", true, $cap);
                $help_url = apply_filters('wpfront_ure_capability_ui_help_link', '', $cap, $value->group_obj);
                $help_url = apply_filters("wpfront_ure_capability_{$cap}_ui_help_link", $help_url, $cap);
                ?>
                <div>
                    <input type="checkbox" class="allow" id="<?php echo 'cap-' . esc_attr($cap) . '-allow'; ?>" name="capabilities[<?php echo esc_attr($cap); ?>][allow]" <?php echo $value->disabled ? 'disabled' : '' ?> <?php echo $this->is_cap_granted($cap) ? 'checked' : '' ?> />
                    <input type="checkbox" class="deny hidden" id="<?php echo 'cap-' . esc_attr($cap) . '-deny'; ?>" name="capabilities[<?php echo esc_attr($cap); ?>][deny]" <?php echo $value->disabled ? 'disabled' : '' ?> <?php echo $this->is_cap_denied($cap) ? 'checked' : '' ?> />
                    <label class="cap-label cap-label-<?php echo esc_attr($cap); ?> <?php echo $enabled ? '' : 'disabled'; ?> <?php echo $this->is_cap_denied($cap) ? 'denied' : '' ?>" data-cap="<?php echo esc_attr($cap); ?>" title="<?php echo esc_attr($cap); ?>"><?php echo esc_html($cap); ?></label>
                    <?php
                    if (!empty($help_url)) {
                        ?>
                        <a target="_blank" href="<?php echo esc_attr($help_url); ?>">
                            <i class="fa fa-question-circle-o"></i>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
        }

        protected function postbox_title($value) {
            return '<label class="select-all ' . ($value->deprecated ? 'deprecated' : 'active') . '"><input id="' . $value->key . '" type="checkbox" class="select-all" ' . ($value->disabled ? 'disabled' : '') . ' />' . $value->display_name . '</label>';
        }

        protected function get_cap_state($cap) {
            $role_data = $this->RoleAddEdit->get_role_data();
            if (empty($role_data)) {
                return null;
            }

            if (isset($role_data['capabilities'][$cap])) {
                return $role_data['capabilities'][$cap];
            }

            return null;
        }

        protected function is_cap_granted($cap) {
            return $this->get_cap_state($cap) === true;
        }

        protected function is_cap_denied($cap) {
            return $this->get_cap_state($cap) === false;
        }

        protected function submit_button() {
            $is_edit_role = $this->RoleAddEdit->edit_role();

            $attr = ['id' => 'createusersub'];
            if ($this->is_sub_controls_disabled()) {
                $attr['disabled'] = true;
            }

            submit_button(
                    $is_edit_role ? __('Update Role', 'wpfront-user-role-editor') : __('Add New Role', 'wpfront-user-role-editor'),
                    'primary',
                    'createrole',
                    true,
                    $attr
            );
        }

        protected function get_role_name() {
            $role_data = $this->RoleAddEdit->get_role_data();

            if ($role_data === null) {
                return '';
            }

            return $role_data['role_name'];
        }

        protected function is_role_name_disabled() {
            $is_edit_role = $this->RoleAddEdit->edit_role();

            return $is_edit_role;
        }

        protected function is_role_name_valid() {
            $role_data = $this->RoleAddEdit->get_role_data();

            if ($role_data === null) {
                return false;
            }

            return $role_data['is_role_name_valid'];
        }

        protected function get_role_display_name() {
            $role_data = $this->RoleAddEdit->get_role_data();

            if ($role_data === null) {
                return '';
            }

            return $role_data['display_name'];
        }

        protected function is_role_display_name_disabled() {
            $role_data = $this->RoleAddEdit->get_role_data();

            if ($role_data === null) {
                return true;
            }

            return $role_data['is_readonly'];
        }

        protected function is_display_name_valid() {
            $role_data = $this->RoleAddEdit->get_role_data();

            if ($role_data === null) {
                return false;
            }

            return $role_data['is_display_name_valid'];
        }

        protected function is_sub_controls_disabled() {
            return $this->is_role_display_name_disabled();
        }

        protected function get_copy_from_roles() {
            $role_data = $this->RoleAddEdit->get_role_data();

            if ($role_data !== null && $role_data['is_readonly']) {
                return array();
            }

            return $this->RolesHelperClass::get_names();
        }

        protected function ajax_url() {
            return json_encode(admin_url('admin-ajax.php'));
        }

        protected function scripts() {
            ?>
            <script type="text/javascript">
                (function ($) {
                    var editRole = <?php echo $this->RoleAddEdit->edit_role() ? 'true' : 'false'; ?>;
                    var $viewType = $('input.view-type');

                    function change_select_all($divs) {
                        var viewType = $viewType.filter(':checked').val();

                        $divs.each(function () {
                            var $this = $(this);
                            var $chks = $this.find("input." + viewType);

                            if ($chks.length == 0) {
                                return;
                            }

                            if ($chks.length === $chks.filter(":checked").length) {
                                $this.closest("div.postbox").find("input.select-all").prop("checked", true);
                            } else {
                                $this.closest("div.postbox").find("input.select-all").prop("checked", false);
                            }

                            //disable select-all if all chechboxes are disabled.
                            if ($chks.length === $chks.filter(":disabled").length) {
                                $this.closest("div.postbox").find("input.select-all").prop("disabled", true);
                            }
                        });
                    }

                    function set_cap_fields_state($labels, value, viewType) {
                        var viewType = viewType || $viewType.filter(':checked').val();

                        //find all labels with same cap and apply the action to all labels.
                        var l = $();
                        $labels.each(function (i, e) {
                            var $e = $(e);
                            var cap = $e.data("cap");

                            l = l.add($("label.cap-label[data-cap='" + cap + "']"));
                        });

                        $labels = l;

                        if (viewType == 'allow') {
                            $labels.prev().prop('checked', false);
                            $labels.prev().prev().prop('checked', value);
                            $labels.removeClass('denied');
                        } else {
                            $labels.prev().prop('checked', value);
                            $labels.prev().prev().prop('checked', false);
                            $labels.removeClass('denied');
                            if (value) {
                                $labels.addClass('denied');
                            }
                        }

                        change_select_all($labels.closest('div.main'));
                    }

                    //postbox
                    $(function () {
                        $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                        postboxes.add_postbox_toggles('<?php echo $this->RoleAddEdit->get_menu_slug(); ?>');
                        $('div.postbox div.main.hidden').closest('div.postbox').addClass('hide-if-js');
                        $('div.postbox div.main.visible').closest('div.postbox').removeClass('hide-if-js');
                    });

                    //select all - none buttons
                    $("div.role-add-new table.sub-head td.sub-head-controls div.select-all-none").on("click", "input.chk-helpers", function () {
                        var $labels = $("div.role-add-new div.postbox div.inside label");

                        if ($(this).hasClass('select-all')) {
                            set_cap_fields_state($labels, true);
                        } else {
                            var viewType = $viewType.filter(':checked').val();
                            if (viewType === 'allow') {
                                set_cap_fields_state($labels.filter(':not(.denied)'), false);
                            } else {
                                set_cap_fields_state($labels.filter('.denied'), false);
                            }
                        }
                    });

                    //select all caps checkbox
                    $("div.role-add-new div.postbox label.select-all").on("click", "input.select-all:not(:disabled)", function (event) {
                        var $this = $(this);
                        var $labels = $this.closest('div').next().find("label");
                        set_cap_fields_state($labels, $this.prop('checked'));

                        event.stopPropagation();
                    });

                    $("div.role-add-new div.postbox label.select-all").on("click", function (event) {
                        event.stopPropagation();
                    });

                    //set select all caps on caps click
                    $("div.role-add-new").on("click", "div.postbox div.main input", function () {
                        var $this = $(this);
                        var $label = $this.next();
                        if ($this.hasClass('allow')) {
                            $label = $label.next();
                        }

                        set_cap_fields_state($label, $this.prop('checked'));
                    });

                    //auto role name from display name
                    if (!editRole) {
                        $("#display_name").keyup(function () {
                            var $this = $(this);
                            if ($.trim($this.val()) == "")
                                return;
                            $("#role_name").val($.trim($this.val()).toLowerCase().replace(/ /g, "_").replace(/\W/g, ""));
                        });

                        $("#role_name").blur(function () {
                            var ele = $(this);
                            var str = $.trim(ele.val()).toLowerCase();
                            str = str.replace(/ /g, "_").replace(/\W/g, "");
                            ele.val(str);
                            if (str != "") {
                                ele.parent().parent().removeClass("form-invalid");
                            }
                        });
                    }

                    //validation
                    $("#createusersub").click(function () {
                        var role_name = $("#role_name");
                        var display_name = $("#display_name");
                        if ($.trim(role_name.val()) == "") {
                            role_name.parent().parent().addClass("form-invalid");
                        }

                        if ($.trim(display_name.val()) == "") {
                            display_name.parent().parent().addClass("form-invalid");
                        }

                        if ($.trim(display_name.val()) == "") {
                            display_name.focus();
                            return false;
                        }

                        if ($.trim(role_name.val()) == "") {
                            role_name.focus();
                            return false;
                        }

                        return true;
                    });

                    //validation status set
                    $("#display_name").blur(function () {
                        var $this = $(this);
                        if ($.trim($this.val()) != "") {
                            $this.parent().parent().removeClass("form-invalid");
                        }
                        $("#role_name").blur();
                    });

                    //copy capabilities
                    $("#cap_apply").click(function () {
                        var $this = $(this);

                        if ($this.prev().val() == "")
                            return;

                        var button = $this.prop("disabled", true);
                        var data = {
                            "action": "wpfront_user_role_editor_copy_capabilities",
                            "role": $this.prev().val(),
                            "nonce": <?php echo json_encode(wp_create_nonce('copy-capabilities')); ?>
                        };

                        var ajaxurl = <?php echo $this->ajax_url(); ?>;
                        $.post(ajaxurl, data, function (response) {
                            $("div.role-add-new div.postbox input").prop("checked", false);
                            $("div.role-add-new div.postbox label").removeClass('denied');

                            var allowed = [];
                            var denied = [];

                            for (m in response) {
                                if (response[m]) {
                                    allowed.push('.cap-label-' + $.escapeSelector(m));
                                } else {
                                    denied.push('.cap-label-' + $.escapeSelector(m));
                                }
                            }

                            if (allowed.length > 0) {
                                set_cap_fields_state($(allowed.join()), true, 'allow');
                            }

                            if (denied.length > 0) {
                                set_cap_fields_state($(denied.join()), true, 'deny');
                            }

                            button.prop("disabled", false);
                        }, 'json');
                    });

                    //view change
                    $viewType.change(function () {
                        //active class change
                        $('label.view-type').removeClass('active');
                        var $this = $(this);
                        $this.parent().addClass('active');
                        var val = $this.val();

                        //cap label for set
                        $('label.cap-label').each(function () {
                            var $label = $(this);
                            var cap = $label.data('cap');
                            var chkId = 'cap' + '-' + cap + '-' + val;
                            $label.prop('for', chkId);

                            if (val === 'allow') {
                                $label.prev().addClass('hidden').prev().removeClass('hidden');
                            } else {
                                $label.prev().removeClass('hidden').prev().addClass('hidden');
                            }

                            //disable other checkboxes with same cap
                            var $chks = $("input[id='" + chkId + "']");
                            if ($chks.length > 1) {
                                $chks.slice(1).prop('disabled', true);
                            }

                            //remove id if its checkbox is disabled, because we could have multiple checkboxes with same id.
                            if ($label.siblings("#" + $.escapeSelector(chkId)).is(":disabled")) {
                                $label.prop('for', '');
                            }
                        });

                        //set select all caps checkbox state on load
                        change_select_all($("div.role-add-new div.postbox div.main"));
                    }).filter(':checked').change();

                    //customize capability
                    $(function () {
                        var $div = null;
                        var $divInner;
                        var fnSubmit = null;

                        $("div.role-add-new").on("click", "a.thickbox.customize_capability", function () {
                            var $this = $(this);

                            if ($div === null) {
                                $div = $('<div id="div_customize_capability" class="hidden" />');
                                $divInner = $("<div class='thickbox' style='text-align:center'></div>");
                                $divInner.append($('<p><label><?php echo __('Capability Type', 'wpfront-user-role-editor'); ?> <input type="text" class="capability" /></label></p>'));
                                $divInner.append('<p><input type="submit" class="button button-secondary customize_capability" value="<?php echo __('Submit', 'wpfront-user-role-editor'); ?>" /></p>');
                                $divInner.append('<p class="error hidden"></p>');
                                $div.append($divInner);
                                $("body").append($div);

                                $divInner.on('click', 'input[type="submit"].customize_capability', function () {
                                    $(this).prop('disabled', true);
                                    fnSubmit(tb_remove);
                                });
                            }

                            var cap = $this.data("cap");
                            $div.find("input.capability").val(cap);

                            var url = '#TB_inline?&width=400&height=200&inlineId=div_customize_capability';
                            $this.prop("href", url);

                            var $error = $divInner.find("p.error").html("").addClass("hidden");

                            var delete_duplicates_under_other_caps = function (caps) {
                                caps.forEach((cap) => {
                                    var $label = $("#postbox-other .inside .main label[data-cap='" + cap + "' ]");
                                    $label.closest('div').remove();
                                });
                            };

                            var fnDone = function (error) {
                                if (error) {
                                    $error.html(error).removeClass("hidden");
                                }

                                $divInner.find("input[type='submit'].customize_capability").prop('disabled', false);
                            };

                            fnSubmit = function (fn) {
                                $error.addClass("hidden");

                                $.ajax({
                                    type: 'POST',
                                    url: ajaxurl,
                                    data: {
                                        action: $this.data("action"),
                                        key: $this.data("key"),
                                        cap: $divInner.find("input.capability").val(),
                                        nonce: $this.data("nonce")
                                    },
                                    success: function (response) {
                                        if (!response.success) {
                                            fnDone(response.data.error);
                                            return;
                                        }
                                        var $readOnly = $('#role-add-edit-is-readonly').val();

                                        $.post(ajaxurl, {
                                            action: response.data.action,
                                            key: $this.data("key"),
                                            nonce: $this.data("nonce"),
                                            role_name: $('#role-add-edit-role-name').val(),
                                            read_only: $readOnly
                                        }, function (response) {
                                            if (!response.success) {
                                                fn();
                                                return;
                                            }
                                            if (!$readOnly) {
                                                $this.closest('.postbox').find('input').prop('disabled', false);
                                            }

                                            var $postbox = $this.closest('.postbox');
                                            $this.closest('.main').html(response.data.html);

                                            delete_duplicates_under_other_caps(response.data.caps);

                                            //reset all checkbox state.
                                            $viewType.filter(':checked').triggerHandler('change');

                                            fnDone();
                                            fn();
                                        });
                                    },
                                    error: function (xhr, textStatus, error) {
                                        var ex = "<?php echo esc_html(__('An unexpected error occured. Please reload this page.', 'wpfront-user-role-editor')); ?>";
                                        $divInner.html(ex + "<br>" + error);
                                    }
                                });
                            };
                        });
                    });
                })(jQuery);
            </script>
            <?php
        }

    }

}

