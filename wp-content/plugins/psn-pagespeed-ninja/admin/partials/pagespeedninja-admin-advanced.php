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

$presets = array();
foreach($extra_presets_list as $preset) {
    $presets[$preset->name] = array();
}
foreach ($presets_list as $preset) {
    $presets[$preset->name] = array();
}

foreach ($options as $section) {
    if (isset($section->items)) {
        /** @var array {$section->items} */
        foreach ($section->items as $item) {
            if (isset($item->presets)) {
                $item_presets = get_object_vars($item->presets);
                foreach ($item_presets as $name => $value) {
                    if (!isset($presets[$name])) {
                        trigger_error("PageSpeed Ninja: unknown preset name $name in {$item->name} section.");
                    }
                }
                foreach ($presets_list as $preset) {
                    $name = $preset->name;
                    $value = isset($item_presets[$name]) ? $item_presets[$name] : $item->default;
                    $presets[$name][$item->name] = "'" . $item->name . "':" . (is_string($value) ? "'$value'" : $value);
                }
            }
        }
    }
}

foreach ($extra_presets_list as $preset) {
    $name = $preset->name;
    $presets[$name] = $presets[$preset->base];
    foreach ($preset->options as $option_name => $option_value) {
        $presets[$name][$option_name] = "'" . $option_name . "':" . (is_string($option_value) ? "'$option_value'" : $option_value);
    }
}

foreach ($presets as $preset => &$values) {
    $values = "'$preset':{" . implode(',', $values) . '}';
}
unset($values);
echo "<script>\nvar pagespeedninja_presets={\n" . implode(",\n", $presets) . "};\n</script>";

?>
<div class="psnwrap">
    <form action="options.php" method="post" id="pagespeedninja_form">
        <?php settings_fields('pagespeedninja_config'); ?>
        <?php /*do_settings_sections('pagespeedninja_config');*/ ?>
        <?php $config = get_option('pagespeedninja_config'); ?>

        <div id="pagespeedninja">
            <div class="headerbar">
                <a href="#" class="save" title="<?php esc_attr_e('Save changes'); ?>"><?php _e('Save'); ?></a>
                <div class="logo"></div>
            </div>
            <div class="tabs">
                <a href="#" class="basic"><?php _e('General'); ?></a>
                <a href="#" class="active advanced"><?php _e('Advanced'); ?></a>
            </div>

            <div class="main column-wide">

                <div class="presets">
                    <h3><?php _e('Presets'); ?></h3>
                    <?php foreach ($extra_presets_list as $preset): ?>
                    <label data-tooltip="<?php echo esc_attr($preset->tooltip); ?>"><input type="radio" name="preset" id="pagespeedninja_preset_<?php echo $preset->name; ?>" onclick="pagespeedninjaLoadPreset('<?php echo $preset->name; ?>')"> <?php echo $preset->title; ?></label>
                    <?php endforeach; ?>
                    <?php foreach ($presets_list as $preset): ?>
                    <label data-tooltip="<?php echo esc_attr($preset->tooltip); ?>"><input type="radio" name="preset" id="pagespeedninja_preset_<?php echo $preset->name; ?>" onclick="pagespeedninjaLoadPreset('<?php echo $preset->name; ?>')"> <?php echo $preset->title; ?></label>
                    <?php endforeach; ?>
                    <label data-tooltip="<?php _e('Your current preset.'); ?>"><input type="radio" name="preset" id="pagespeedninja_preset_custom" onclick="pagespeedninjaLoadPreset('')"> <?php _e('Custom'); ?></label>
                </div>

                <?php
                $first = true;
                /** @var stdClass $section */
                /** @var array {$section->items} */
                foreach ($options as $section) : ?>
                    <div<?php echo isset($section->id) ? ' id="psi_' . $section->id . '"' : ''; ?>>
                        <div class="header">
                            <div class="expando<?php echo $first ? ' open' : ''; ?>"></div>
                            <div class="title"><?php echo $section->title; ?></div>
                            <?php
                            if (isset($section->id)) {
                                $this->render('checkbox', 'psi_' . $section->id, $config);
                            }
                            ?>
                        </div>
                        <div class="content<?php echo $first ? ' show' : ''; ?>">
                            <?php $first = false; ?>
                            <?php if (!isset($section->items) || count($section->items) === 0) : ?>
                                <div class="line todo"><?php _e('Will be implemented further.'); ?></div>
                            <?php else : ?>
                                <?php foreach ($section->items as $item) :
                                    if ($item->type === 'hidden') {
                                        continue;
                                    }
                                    ?>
                                    <div class="line"><?php
                                    $this->title($item->title, isset($item->tooltip) ? $item->tooltip : '');
                                    $this->render($item->type, $item->name, $config, $item);
                                    ?></div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </form>
</div>