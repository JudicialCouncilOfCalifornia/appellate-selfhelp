<?php
/** @var array $config */
/** @var PagespeedNinja_Admin $this */

ob_start();
include dirname(dirname(dirname(__FILE__))) . '/includes/presets.json.php';
$presets_list = ob_get_clean();
$presets_list = json_decode($presets_list);
/** @var array $presets_list */

$extra_presets_list = array();
$extra_presets_dir = dirname(dirname(__FILE__)) . '/extras/presets';
$extra_presets_files = glob($extra_presets_dir . '/*.json');
foreach ($extra_presets_files as $preset_file) {
    $preset_name = basename($preset_file, '.json');
    $preset_data = @file_get_contents($preset_file);
    $preset_data = @json_decode($preset_data);
    if (!isset($preset_data->base, $preset_data->title, $preset_data->tooltip, $preset_data->options)) {
        continue;
    }
    $extra_presets_list[$preset_name] = $preset_data;
    $extra_presets_list[$preset_name]->name = $preset_name;
}

ob_start();
include dirname(dirname(dirname(__FILE__))) . '/includes/options.json.php';
$options = ob_get_clean();
$options = json_decode($options);
/** @var array $options */

$default_preset = 'safe';
$popup_settings = array('allow_ext_atfcss', 'allow_ext_stats', 'footer');

$settings = array();
foreach ($options as $section) {
    if (isset($section->items)) {
        /** @var array {$section->items} */
        foreach ($section->items as $item) {
            if (in_array($item->name, $popup_settings, true)) {
                $settings[$item->name] = $item;
            }
        }
    }
}

?>
<div class="psnwrap">
    <div id="pagespeedninja">
        <div class="headerbar">
            <div class="logo"></div>
        </div>
    </div>
</div>

<div id="pagespeedninja_afterinstall_popup" style="display:none">
<div id="pagespeedninja">
<div class="column-wide">
    <form action="options.php" method="post" id="pagespeedninja-popup-form" class="content show">
        <?php settings_fields('pagespeedninja_config'); ?>
        <?php /*do_settings_sections('pagespeedninja_config');*/ ?>
        <?php
            $config = get_option('pagespeedninja_config');
            $config['afterinstall_popup'] = '1';
            $this->hidden($config, 'afterinstall_popup');
        ?>
        <div class="presets_popup hidden">
            <?php
            foreach ($extra_presets_list as $preset) {
                ?><label><input type="radio" name="pagespeedninja_preset" value="<?php echo $preset->name; ?>"> <span class="presettitle"><?php echo $preset->title; ?></span><span class="presettooltip"><?php echo $preset->tooltip; ?></span></label><?php
            }
            foreach ($presets_list as $preset) {
                ?><label><input type="radio" name="pagespeedninja_preset" value="<?php echo $preset->name; ?>"<?php echo $preset->name === $default_preset ? ' checked' : ''; ?>> <span class="presettitle"><?php echo $preset->title; ?></span><span class="presettooltip"><?php echo $preset->tooltip; ?></span></label><?php
            }
            ?>
        </div>
        <div class="preset line">
            <div class="title"><?php _e('Optimization Profile Preset'); ?></div>
            <div class="dropdown field"><span id="pagespeedninja_profilename"></span><span class="expando"></span></div>
        </div>
        <?php
        $tabindex = 1;
        foreach ($popup_settings as $name) {
            $item = $settings[$name];
            $cfg_name = 'pagespeedninja_config[' . $name . ']';
            $cfg_id = 'pagespeedninja_config_' . $name;
            switch ($item->type) {
                case 'checkbox':
                    ?><div class="line">
                    <div tabindex="<?php echo $tabindex++; ?>" class="title" data-tooltip="<?php echo esc_attr($item->tooltip); ?>"><?php echo $item->title; ?></div>
                    <div class="field">
                      <input type="hidden" name="<?php echo $cfg_name; ?>" value="0" />
                      <input type="checkbox" name="<?php echo $cfg_name; ?>" id="<?php echo $cfg_id; ?>" value="1"<?php echo ($item->default ? ' checked' : ''); ?> />
                      <label for="<?php echo $cfg_id; ?>"></label>
                    </div>
                    </div><?php
                    break;
                case 'text':
                    ?><div class="line">
                    <div tabindex="<?php echo $tabindex++; ?>" class="title" data-tooltip="<?php echo esc_attr($item->tooltip); ?>"><?php echo $item->title; ?></div>
                    <div class="field">
                      <input type="text" name="<?php echo $cfg_name; ?>" value="" id="<?php echo $cfg_id; ?>" />
                    </div>
                    </div><?php
                    break;
            }
        }
        ?>
        <p><?php _e('These settings may be changed further in the Advanced settings of PageSpeed Ninja plugin.'); ?></p>
        <input type="submit" value="Save" />
    </form>
</div>
</div>
</div>

<style>
    #TB_title, #TB_closeAjaxWindow {
        display: none;
    }
</style>

<script>
    jQuery(function () {
        setTimeout(function () {
            window.tb_remove = function () {
                return false;
            };
            tb_show('', '"#TB_inline?width=727&height=500&inlineId=pagespeedninja_afterinstall_popup');
        }, 0);
        jQuery('#pagespeedninja_profilename').html(
            jQuery('#pagespeedninja-popup-form > .presets_popup input[type=radio][value=<?php echo $default_preset; ?>] + .presettitle').html()
        );
        jQuery('#pagespeedninja-popup-form > .presets_popup input:radio:checked').parent().addClass('checked');
        jQuery('#pagespeedninja-popup-form > .presets_popup input:radio').click(function() {
            jQuery('#pagespeedninja-popup-form .presets_popup label').removeClass('checked');
            jQuery(this).parent().addClass('checked');
            jQuery('#pagespeedninja_profilename').html(
                jQuery(this).next('.presettitle').html()
            );
            jQuery('#pagespeedninja-popup-form > .presets_popup').addClass('hidden');
        });
        jQuery('#pagespeedninja-popup-form > .preset > .dropdown').click(function() {
            jQuery('#pagespeedninja-popup-form > .presets_popup').toggleClass('hidden');
            jQuery(this).parent().addClass('checked');
            return false;
        });
        jQuery('body').click('#TB_window', function() {
            jQuery('#pagespeedninja-popup-form > .presets_popup').addClass('hidden');
        });
    });
</script>