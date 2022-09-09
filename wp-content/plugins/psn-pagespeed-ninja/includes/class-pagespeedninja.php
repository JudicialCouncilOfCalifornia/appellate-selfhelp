<?php

class PagespeedNinja
{
    /** @var string $plugin_name The string used to uniquely identify this plugin. */
    protected $plugin_name;

    /** @var string $plugin_textdomain Official slug for this plugin on wordpress.org. */
    protected $plugin_slug;

    /** @var string $version The current version of the plugin. */
    protected $version;

    /** @var string $plugin_dir_path Path to plugin files */
    protected $plugin_dir_path;

    /** @var array $option Plugin settings */
    protected $options;

    public function __construct() {
        $this->plugin_name     = 'pagespeedninja';
        $this->plugin_slug     = 'psn-pagespeed-ninja';
        $this->version         = '0.9.40';
        $this->plugin_dir_path = plugin_dir_path(dirname(__FILE__));
    }

    /**
     * @return string The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * @return string Official slug of the plugin.
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }

    /**
     * @return string The version of the plugin
     */
    public function get_version() {
        return $this->version;
    }

    public function activate() {
        require_once $this->plugin_dir_path . 'includes/class-pagespeedninja-activator.php';
        PagespeedNinja_Activator::activate($this->get_plugin_name(), $this->get_version());
    }

    public function deactivate() {
        require_once $this->plugin_dir_path . 'includes/class-pagespeedninja-activator.php';
        PagespeedNinja_Activator::deactivate($this->get_plugin_name(), $this->get_version());
    }

    public function run() {
        add_action('upgrader_process_complete', array($this, 'upgrader_process_complete'), 10, 2);
        add_action('pagespeedninja_daily_event', array($this, 'cron_daily'));
        add_action('plugins_loaded', array($this, 'init'));

        $this->set_locale();
        $this->define_cache_hooks();
        if (is_admin()) {
            $this->define_admin_hooks();
        } else {
            $this->define_public_hooks();
        }
    }

    public function init() {
        $this->options = get_option('pagespeedninja_config');
        if (
            $this->options === false ||
            !isset($this->options['version']) ||
            version_compare($this->options['version'], $this->get_version(), '<')
        ) {
            $this->update_config();
        }

        if ($this->options['img_scaletype'] === 'fit') {
            $this->add_image_sizes();
        }
    }

    /**
     * @param $upgrader_object Plugin_Upgrader
     * @param $options array
     */
    public function upgrader_process_complete($upgrader_object, $options) {
        if (
            isset($options['type'], $options['plugins']) &&
            $options['type'] === 'plugin' &&
            in_array($this->get_plugin_slug() . '/' . $this->get_plugin_name() . '.php', $options['plugins'], true)
        ) {
            $this->update_config();
        }
    }

    public function cron_daily() {
        /** @var array $options */
        $options = get_option('pagespeedninja_config');

        // update Above-the-fold CSS
        if ($options['allow_ext_atfcss'] === '1' && $options['psi_MinimizeRenderBlockingResources'] && $options['css_abovethefold'] && $options['css_abovethefoldautoupdate']) {
            $atfCSS = $this->loadATFCSS();
            if ($atfCSS !== '') {
                $options['css_abovethefoldstyle'] = $atfCSS;
                update_option('pagespeedninja_config', $options);
            }
        }

        // clear sessions
        $session_dir = dirname(dirname(__FILE__)) . '/admin/sessions';
        $h = opendir($session_dir);
        while (($file = readdir($h)) !== false) {
            /** @var string $file */
            $file_path = $session_dir . '/' . $file;
            if ($file[0] === '.' || !is_file($file_path)) {
                continue;
            }
            if (filemtime($file_path) < time() - 24*60*60) {
                unlink($file_path);
                continue;
            }
        }
        closedir($h);

        // clear RESS cache
        if (!class_exists('Ressio', false)) {
            include_once dirname(dirname(__FILE__)) . '/ress/ressio.php';
        }
        Ressio::registerAutoloading(true);

        $di = new Ressio_DI();
        $di->config = new stdClass;
        $di->config->cachedir = RESSIO_PATH . '/cache';
        $di->config->cachettl = max(24 * 60, (int)$options['ress_caching_ttl']) * 60;
        $di->config->webrootpath = rtrim(ABSPATH, '/');
        $di->config->staticdir = $options['staticdir'];
        $di->filesystem = new Ressio_Filesystem_Native();
        $di->filelock = new Ressio_FileLock_flock();
        $plugin = new Ressio_Plugin_FileCacheCleaner($di, null);
    }

    /**
     * @return string
     */
    private function loadATFCSS() {
        if (!function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $websiteURL = rtrim(get_option('home'), '/') . '/?pagespeedninja=no';

        $data = array(
            'url' => $websiteURL
        );

        $tmp_filename = download_url('https://pagespeed.ninja/api/getcss?' . http_build_query($data), 60);
        if (is_string($tmp_filename)) {
            $css = @file_get_contents($tmp_filename);
            @unlink($tmp_filename);
            return $css;
        }
        return '';
    }

    private function update_config() {
        $this->activate();
        $this->options = get_option('pagespeedninja_config');
    }

    private function set_locale() {
        require_once $this->plugin_dir_path . 'includes/class-pagespeedninja-i18n.php';
        $plugin_i18n = new PagespeedNinja_i18n();
        $plugin_i18n->set_domain($this->get_plugin_slug());
        add_action('plugins_loaded', array($plugin_i18n, 'load_plugin_textdomain'));
    }

    private function add_image_sizes() {
        if (function_exists('add_image_size')) {
            add_image_size('mobile-320', 320, 99999);
            add_image_size('mobile-640', 640, 99999);
            add_image_size('mobile-360', 360, 99999);
            add_image_size('mobile-720', 720, 99999);
            add_image_size('mobile-1080', 1080, 99999);
            add_image_size('tablet-768', 768, 99999);
            add_image_size('tablet-800', 800, 99999);
            add_image_size('tablet-1024', 1024, 99999);
            add_image_size('tablet-1280', 1280, 99999);
        }
    }

    private function define_cache_hooks() {
        // @todo implement accurate dependencies in advanced cache
        $reset_cache_actions = array(
            'clean_attachment_cache', // after the given attachment's cache is cleaned
//          'clean_comment_cache', // immediately after a comment has been removed from the object cache
            'clean_page_cache', // immediately after the given page's cache is cleaned
            'clean_post_cache', // immediately after the given post's cache is cleaned
            'customize_save_after', // after Customize settings have been saved
            'post_stuck', // once a post has been added to the sticky list
            'post_unstuck', // once a post has been removed from the sticky list
            'switch_theme', // after the theme is switched
        );
        foreach ($reset_cache_actions as $action) {
            add_action($action, array($this, 'reset_cache'));
        }
    }

    public function reset_cache() {
        $cache_dir = dirname(dirname(__FILE__)) . '/cache';
        @touch($cache_dir . '/pagecache.stamp');

        // @todo regenerate ATF CSS
    }

    private function define_admin_hooks() {
        require_once $this->plugin_dir_path . 'admin/class-pagespeedninja-admin.php';
        $plugin_admin = new PagespeedNinja_Admin($this->get_plugin_name(), $this->get_version());

        add_action('admin_init', array($plugin_admin, 'admin_init'));
        add_action('admin_menu', array($plugin_admin, 'admin_menu'));
        add_action('admin_head', array($plugin_admin, 'admin_head'));
        add_filter('plugin_action_links_' . plugin_basename($this->plugin_dir_path . 'pagespeedninja.php'),
            array($plugin_admin, 'admin_plugin_settings_link'));
        add_filter('plugin_row_meta', array($plugin_admin, 'admin_plugin_meta_links'), 10, 2);

        add_filter('pre_update_option_pagespeedninja_config', array($plugin_admin, 'validate_config'), 10, 2);
        add_action('update_option_pagespeedninja_config', array($plugin_admin, 'update_config'), 10, 2);

        add_action('wp_ajax_pagespeedninja_get_cache_size', array($plugin_admin, 'get_cache_size'));

        add_action('wp_ajax_pagespeedninja_clear_images', array($plugin_admin, 'clear_images'));
        add_action('wp_ajax_pagespeedninja_clear_cache_expired', array($plugin_admin, 'clear_cache_expired'));
        add_action('wp_ajax_pagespeedninja_clear_cache_all', array($plugin_admin, 'clear_cache_all'));
        add_action('wp_ajax_pagespeedninja_clear_pagecache_expired', array($plugin_admin, 'clear_pagecache_expired'));
        add_action('wp_ajax_pagespeedninja_clear_pagecache_all', array($plugin_admin, 'clear_pagecache_all'));
        add_action('wp_ajax_pagespeedninja_clear_amddcache', array($plugin_admin, 'clear_amddcache'));

        add_action('wp_ajax_pagespeedninja_key', array($plugin_admin, 'ajax_key'));
    }

    private function define_public_hooks() {
        require_once $this->plugin_dir_path . 'public/class-pagespeedninja-public.php';
        $plugin_public = new PagespeedNinja_Public($this->get_plugin_name(), $this->get_version());

        // Smart Slider 3: priority=-100
        // Better AMP: priority=2 (redirect to AMP)
        add_action('template_redirect', array($plugin_public, 'template_redirect'), -150);

        add_filter('wp_cache_meta', array($plugin_public, 'wp_cache_meta'));
        add_action('wp_footer', array($plugin_public, 'wp_footer'), 100);
    }
}
