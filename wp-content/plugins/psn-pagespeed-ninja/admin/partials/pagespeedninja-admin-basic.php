<?php
/** @var array $config */
/** @var PagespeedNinja_Admin $this */

ob_start();
include dirname(dirname(dirname(__FILE__))) . '/includes/options.json.php';
$options = ob_get_clean();
$options = json_decode($options);
/** @var array $options */
?>
<div class="psnwrap">
    <form action="options.php" method="post" id="pagespeedninja_form">
        <?php settings_fields('pagespeedninja_config'); ?>
        <?php /*do_settings_sections('pagespeedninja_config');*/ ?>
        <?php $config = get_option('pagespeedninja_config'); ?>
        <?php /* @todo refactor code to just loop hidden parameters and remove config-specific code from PagespeedNinja_Admin::hidden */ ?>
        <?php $this->hidden($config, 'css_abovethefoldlocal'); ?>
        <?php $this->hidden($config, 'css_abovethefoldstyle'); ?>
        <?php /* @TODO ATF-CSS should be updated automatically after homepage content is changed */ ?>
        <?php
            foreach ($options as $section) {
                if (isset($section->id)) {
                    $this->hidden($config, 'psi_' . $section->id);
                }
            }
        ?>
    </form>

    <div id="pagespeedninja">
        <div class="headerbar">
            <a href="#" class="save" title="<?php esc_attr_e('Save changes'); ?>"><?php _e('Save'); ?></a>
            <div class="logo"></div>
        </div>
        <div class="tabs">
            <a href="#" class="active basic"><?php _e('General'); ?></a>
            <a href="#" class="advanced"><?php _e('Advanced'); ?></a>
        </div>
        <!--div class="preview">
            <div class="iframe">
                <iframe src="about:blank" sandbox="allow-forms allow-pointer-lock allow-popups allow-same-origin allow-scripts"></iframe>
            </div>
            <a class="dragger closed">preview</a>
            <div class="overlay_fix"></div>
        </div!-->
        <div class="main">
            <div class="column" id="desktop">
                <h2>
                    <?php _e('Desktop'); ?>
                    <div class="gps_result_orig"><span class="gps_loading" id="pagespeed_desktop_orig" title="<?php esc_attr_e('Original score'); ?>">&nbsp;</span></div>
                    <div class="gps_result"><span class="gps_loading" id="pagespeed_desktop" title="<?php esc_attr_e('Current score'); ?>">&nbsp;</span></div>
                    <div class="gps_result_new hide"><a href="#" class="thickbox"><span id="pagespeed_desktop_new" title="<?php esc_attr_e('Estimated new score (click to test website in popup)'); ?>">&nbsp;</span></a></div>
                </h2>
                <div id="desktop-should-fix" class="hide">
                    <h3><?php _e('Should Fix'); ?></h3>
                </div>
                <div id="desktop-consider-fixing" class="hide">
                    <h3><?php _e('Consider Fixing'); ?></h3>
                </div>
                <div id="desktop-passed" class="hide">
                    <h3><?php _e('Passed'); ?></h3>
                </div>
                <?php /* @todo extract into $this->sectionHeader('dekstop', 'Desktop'); */ ?>
                <div id="desktop-waiting">
                    <?php
                    foreach ($options as $section) :
                        if (isset($section->id) && $section->type === 'speed') :
                            ?>
                    <div id="desktop_<?php echo $section->id; ?>">
                        <div class="header">
                            <div class="title"><?php echo $section->title; ?></div>
                            <div class="field" data-tooltip="<?php
                            // @todo extract into $this->section('desktop', $section->id, $section->title);
                            _e("Color of the enabled switch depends on how it affects your website's PageSpeed Insights score. Green: Improves the score. Orange: a minor or no effect on the score. Red: negatively affects the score. Note: some settings have inter-related effects, which is why also other switches may change color.");
                            ?>"><?php $this->checkbox('pagespeedninja_config_desktop_' . $section->id, 'desktop_' . $section->id); ?></div>
                        </div>
                    </div>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>

            <div class="column" id="mobile">
                <h2>
                    <?php _e('Mobile'); ?>
                    <div class="gps_result_orig"><span class="gps_loading" id="pagespeed_mobile_orig" title="<?php esc_attr_e('Original score'); ?>">&nbsp;</span></div>
                    <div class="gps_result"><span class="gps_loading" id="pagespeed_mobile" title="<?php esc_attr_e('Current score'); ?>">&nbsp;</span></div>
                    <div class="gps_result_new hide"><a href="#" class="thickbox"><span id="pagespeed_mobile_new" title="<?php esc_attr_e('Estimated new score (click to test website in popup)'); ?>">&nbsp;</span></a></div>
                </h2>
                <div id="mobile-should-fix" class="hide">
                    <h3><?php _e('Should Fix'); ?></h3>
                </div>
                <div id="mobile-consider-fixing" class="hide">
                    <h3><?php _e('Consider Fixing'); ?></h3>
                </div>
                <div id="mobile-passed" class="hide">
                    <h3><?php _e('Passed'); ?></h3>
                </div>
                <div id="mobile-waiting">
                    <?php
                    foreach ($options as $section) :
                        if (isset($section->id) && $section->type === 'speed') :
                            ?>
                        <div id="mobile_<?php echo $section->id; ?>">
                            <div class="header">
                                <div class="title"><?php echo $section->title; ?></div>
                                <div class="field"><?php $this->checkbox('pagespeedninja_config_mobile_' . $section->id, 'mobile_' . $section->id); ?></div>
                            </div>
                        </div>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>

            <div class="column" id="usability">
                <h2>
                    <?php _e('Usability'); ?>
                    <div class="gps_result_orig"><span class="gps_loading" id="pagespeed_usability_orig" title="<?php esc_attr_e('Original score'); ?>">&nbsp;</span></div>
                    <div class="gps_result"><span class="gps_loading" id="pagespeed_usability" title="<?php esc_attr_e('Current score'); ?>">&nbsp;</span></div>
                    <div class="gps_result_new hide"><a href="#" class="thickbox"><span id="pagespeed_usability_new" title="<?php esc_attr_e('Estimated new score (click to test website in popup)'); ?>">&nbsp;</span></a></div>
                </h2>
                <div id="usability-should-fix" class="hide">
                    <h3><?php _e('Should Fix'); ?></h3>
                </div>
                <div id="usability-consider-fixing" class="hide">
                    <h3><?php _e('Consider Fixing'); ?></h3>
                </div>
                <div id="usability-passed" class="hide">
                    <h3><?php _e('Passed'); ?></h3>
                </div>
                <div id="usability-waiting">
                    <?php
                    foreach ($options as $section) :
                        if (isset($section->id) && $section->type === 'usability') :
                            ?>
                    <div id="usability_<?php echo $section->id; ?>">
                        <div class="header">
                            <div class="title"><?php echo $section->title; ?></div>
                            <div class="field"><?php $this->checkbox('pagespeedninja_config_usability_' . $section->id, 'usability_' . $section->id); ?></div>
                        </div>
                    </div>
                            <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>