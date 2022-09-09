<?php

class PagespeedNinja_Public
{
    /** @var string */
    private $plugin_name;

    /** @var string */
    private $version;

    /** @var bool */
    private $disabled = false;

    /** @var bool */
    private $started = false;

    /** @var string */
    private $viewportWidth = '0';

    /** @var string */
    private $testKey = '';

    private $foundTime = 0;
    private $foundScripts = array();
    private $foundStyles = array();

    /** @var array */
    private static $disabledOptions = array(
        'disable_autoload' => true,
        'cachefast' => false,
        'html' => array(
            'mergespace' => false,
            'removecomments' => false,
            'urlminify' => false,
            'gzlevel' => 0,
            'sortattr' => false,
            'removedefattr' => false,
            'removeiecond' => false,
        ),
        'css' => array(
            'merge' => false,
            'mergeinline' => false,
            'crossfileoptimization' => false,
            'inlinelimit' => 0,
            'checklinkattributes' => true,
            'checkstyleattributes' => true,
            'minifyattribute' => false,
            'excludemergeregex' => null,
        ),
        'js' => array(
            'merge' => false,
            'mergeinline' => false,
            'autoasync' => false,
            'crossfileoptimization' => false,
            'inlinelimit' => 0,
            'wraptrycatch' => true,
            'checkattributes' => true,
            'minifyattribute' => false,
            'skipinits' => false,
            'excludemergeregex' => null,
        ),
        'img' => array(
            'minify' => false,
            'minifyrescaled' => false,
            'jpegquality' => 100,
        ),
        'di' => array(
            'deviceDetector' => 'Ressio_DeviceDetector_Amdd',
            'cssMinify' => 'Ressio_CssMinify_None',
            'jsMinify' => 'Ressio_JsMinify_None',
            'imgOptimizer.gif' => null,
            'imgOptimizer.jpg' => null,
            'imgOptimizer.png' => null,
            'imgOptimizer.webp' => null,
            'imgOptimizer.svg' => null,
        ),
        'plugins' => array(
            'Ressio_Plugin_FileCacheCleaner' => null
        )
    );

    /**
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        if (isset($_GET['pagespeedninja'])) {
            switch ($_GET['pagespeedninja']) {
                case 'no':
                    define('DONOTCACHEPAGE', true);
                    $this->disabled = true;
                    $_COOKIE = array();
                    break;
                case 'desktop':
                    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453 Safari/537.36';
                    $_COOKIE = array();
                    break;
                case 'mobile':
                    $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/537.36 (KHTML, like Gecko) Version/8.0 Mobile/12F70 Safari/600.1.4';
                    $_COOKIE = array();
                    break;
                case 'test':
                    define('DONOTCACHEPAGE', true);
                    $this->testKey = $_REQUEST['pagespeedninjakey'];
                    // remove cookies to display guest page in backend
                    // @todo develop a way to reset cookies once and allow user to login in the preview iframe
                    $_COOKIE = array();
                    break;
                default:
                    break;
            }
            unset($_GET['pagespeedninja'], $_REQUEST['pagespeedninja']);
        }
    }

    public function template_redirect()
    {
        if (
            $this->disabled
            || defined('XMLRPC_REQUEST') || defined('REST_REQUEST')
            || defined('DOING_AJAX') || defined('DOING_CRON')
            || defined('WP_ADMIN') || defined('WP_INSTALLING')
            || (defined('SHORTINIT') && SHORTINIT)
            || (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET')
            || isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            || isset($_GET['preview'])
            || isset($_GET['wp_scrape_key'])
            || isset($_GET['fl_builder']) || isset($_GET['mbuilder'])
            || is_404() || is_admin() || is_feed() || is_comment_feed() || is_preview() || is_robots() || is_trackback()
            || (function_exists('is_customize_preview') && is_customize_preview()) // WordPress 4.0.0+
            || headers_sent()
            // disable for AMP plugin
            || (defined('AMP_QUERY_VAR') && get_query_var(AMP_QUERY_VAR, 0))
            // @todo optionally disable for logged users???
        ) {
            return;
        }

        $options = get_option('pagespeedninja_config');
        if ($options['afterinstall_popup'] !== '1') {
            return;
        }

        ob_start(array($this, 'ob_callback'));
        $this->started = true;

        if (($options['psi_MinifyCss'] && $options['css_merge']) ||
            ($options['psi_MinifyJavaScript'] && $options['js_merge'])
        ) {
            global $concatenate_scripts;
            $concatenate_scripts = false;
        }

        if ($options['psi_MinifyJavaScript']) {
            $emoji_priority = has_action('wp_head', 'print_emoji_detection_script');
            if ($emoji_priority !== false) {
                $mergewpemoji = $options['wp_mergewpemoji'];
                switch ($mergewpemoji) {
                    case 'default':
                        break;
                    case 'merge':
                        remove_action('wp_head', 'print_emoji_detection_script', $emoji_priority);
                        add_action('wp_head', array($this, 'print_emoji_detection_script'), $emoji_priority);
                        break;
                    case 'disable':
                        remove_action('wp_head', 'print_emoji_detection_script', $emoji_priority);
                }
            }
        }
    }

    /**
     * @param array $wp_cache_meta
     * @return mixed
     */
    public function wp_cache_meta($wp_cache_meta)
    {
        // Support WP Super Cache
        if ($this->started && class_exists('Ressio_Helper', false)) {
            $headers = Ressio_Helper::getHeaders();
            foreach ($headers as $header) {
                $key = substr($header, 0, strpos($header, ':') - 1);
                $wp_cache_meta['headers'][$key] = $header;
            }
        }

        return $wp_cache_meta;
    }

    /**
     * @param string $buffer
     * @return string|false
     * @throws ERessio_Exception
     * @throws ERessio_UnknownDiKey
     */
    public function ob_callback($buffer)
    {
        $buffer = ltrim($buffer);
        if (
            $buffer === '' // empty page
            || (defined('DONOTMINIFY') && DONOTMINIFY) // disabled optimization
            || $buffer[0] !== '<' // bypass non-HTML (partials, json, etc.)
            || strncmp($buffer, '<?xml ', 6) === 0 // bypass XML (sitemap, etc.)
            || preg_match('/<html\s[^>]*?(?:⚡|\bamp\b)[^>]*>/u', $buffer) // bypass amp pages (detected by <html amp> or <html ⚡>)
        ) {
            return false;
        }

        /** @var array $options */
        $options = get_option('pagespeedninja_config');

        // skip logged users
        if (!(bool)$options['enablelogged'] && is_user_logged_in()) {
            return false;
        }

        if ($this->testKey) {
            $filename = dirname(dirname(__FILE__)) . '/admin/sessions/' . $this->testKey;
            if (is_file($filename)) {
                $override = file_get_contents($filename);
                $override = json_decode($override, true);
                foreach ($override as $name => $value) {
                    $options[$name] = $value;
                }
            }
        }

        $gzip = (bool)$options['html_gzip'];
        if ($gzip && (
            class_exists('W3_Plugin_TotalCache', false)
            || function_exists('check_richards_toolbox_gzip')
            || function_exists('wp_cache_phase2')
            || headers_sent()
            || in_array('ob_gzhandler', ob_list_handlers(), true)
        )) {
            $gzip = false;
        }

        $webrooturi = parse_url(get_option('siteurl'), PHP_URL_PATH);
        if ($webrooturi === null) {
            $webrooturi = '';
        }

        $css_excludemergeregex = null;
        if (!empty($options['css_excludelist_prepared'])) {
            $css_excludemergeregex = $options['css_excludelist_prepared'];
        }

        $js_excludemergeregex = null;
        if (!empty($options['js_excludelist_prepared'])) {
            $js_excludemergeregex = $options['js_excludelist_prepared'];
        }

        $ress_options = self::$disabledOptions;

        if (!class_exists('Ressio', false)) {
            include_once dirname(dirname(__FILE__)) . '/ress/ressio.php';
        }
        Ressio::registerAutoloading(true);

        // @todo don't load AMDD for disabled rescaling (use simple device detector instead)
        include_once dirname(dirname(__FILE__)) . '/includes/class-pagespeedninja-amdd.php';
        $ress_options['amdd'] = PagespeedNinja_Amdd::getConfig();

        $ress_options = array_merge($ress_options, array(
            'webrootpath' => rtrim(ABSPATH, '/'),
            'webrooturi' => $webrooturi,
            'staticdir' => $options['staticdir'],
            'fileloader' => ($options['distribmode'] === 'php') ? 'php' : 'file',
            'fileloaderphppath' => rtrim(ABSPATH, '/') . $options['staticdir'] . '/f.php',
        ));

        if (!is_user_logged_in()) {
            $ttl = (int)$options['ress_caching_ttl'] * 60;
            $ress_options['cachettl'] = max(24 * 60 * 60, $ttl);
            Ressio_Helper::setHeader('Expires: ' . gmdate('D, d M Y H:i:s', time() + $ttl) . ' GMT');
            Ressio_Helper::setHeader('Cache-Control: private, must-revalidate, max-age=' . $ttl);
        }

        switch ($options['htmloptimizer']) {
            case 'pharse':
                $ress_options['di']['htmlOptimizer'] = 'Ressio_HtmlOptimizer_Pharse';
                break;
            case 'stream':
                $ress_options['di']['htmlOptimizer'] = 'Ressio_HtmlOptimizer_Stream';
                break;
            case 'dom':
                $ress_options['di']['htmlOptimizer'] = 'Ressio_HtmlOptimizer_Dom';
                break;
            default:
                trigger_error('PageSpeed Ninja: unknown html optimizer value: ' . var_export($options['htmloptimizer'], true));
        }

        if ($options['http2']) {
            $ress_options['plugin']['Ressio_Plugin_Http2'] = null;
        }

        if ($options['psi_AvoidLandingPageRedirects']) {
            // @todo AvoidLandingPageRedirects (check .htaccess/plugins in backend and display advices?)
        }

        if ($options['psi_EnableGzipCompression']) {
            $ress_options['html']['gzlevel'] = $gzip ? 5 : 0;
            $ress_options['html']['sortattr'] = (bool)$options['html_sortattr'];
        }

        if ($options['psi_LeverageBrowserCaching']) {
            if ($options['css_loadurl'] || $options['js_loadurl'] || $options['img_loadurl']) {
                $ress_options['plugins']['Ressio_Plugin_UrlLoader'] = array(
                    // @todo add support of 'loadqueue' and 'loadphp'
                    'loadcss' => $options['css_loadurl'],
                    'loadscript' => $options['js_loadurl'],
                    'loadimg' => $options['img_loadurl']
                );
            }
        }

        if ($options['psi_MainResourceServerResponseTime']) {
            $ress_options['cachefast'] = (bool)$options['caching_processed'];
            if ($options['dnsprefetch']) {
                $ress_options['plugins']['Ressio_Plugin_DnsPrefetch'] = null;
            }
        }

        if ($options['psi_MinifyCss']) {
            $ress_options['css']['merge'] = (bool)$options['css_merge'];
            $ress_options['css']['excludemergeregex'] = $css_excludemergeregex;
            if ($options['css_mergeinline'] === 'head') {
                $ress_options['css']['mergeinline'] = 'head';
            } else {
                $ress_options['css']['mergeinline'] = (bool)$options['css_mergeinline'];
            }
            $ress_options['css']['minifyattribute'] = (bool)$options['css_minifyattribute'];
            $ress_options['css']['inlinelimit'] = (int)$options['css_inlinelimit'];
            $ress_options['css']['crossfileoptimization'] = (bool)$options['css_crossfileoptimization'];
            $ress_options['css']['checklinkattributes'] = (bool)$options['css_checklinkattributes'];
            $ress_options['css']['checkstyleattributes'] = (bool)$options['css_checkstyleattributes'];
            switch ($options['css_di_cssMinify']) {
                case 'none':
                    $ress_options['di']['cssMinify'] = 'Ressio_CssMinify_None';
                    break;
                case 'ress':
                    $ress_options['di']['cssMinify'] = 'Ressio_CssMinify_Ress';
                    break;
                case 'csstidy':
                    $ress_options['di']['cssMinify'] = 'Ressio_CssMinify_CssTidy';
                    break;
                case 'both':
                    $ress_options['di']['cssMinify'] = 'Ressio_CssMinify_Chain';
                    $ress_options['cssminifychain'] = array('Ressio_CssMinify_CssTidy', 'Ressio_CssMinify_Ress');
                    break;
                default:
                    trigger_error('PageSpeed Ninja: unknown css_di_cssMinify value ' . var_export($options['css_di_cssMinify'], true));
            }
        }

        if ($options['psi_MinifyHTML']) {
            $ress_options['html']['mergespace'] = (bool)$options['html_mergespace'];
            $ress_options['html']['removecomments'] = (bool)$options['html_removecomments'];
            $ress_options['html']['urlminify'] = (bool)$options['html_minifyurl'];
            $ress_options['html']['removedefattr'] = (bool)$options['html_removedefattr'];
            $ress_options['html']['removeiecond'] = (bool)$options['html_removeiecond'];
        }

        if ($options['psi_MinifyJavaScript']) {
            $ress_options['js']['merge'] = (bool)$options['js_merge'];
            $ress_options['js']['excludemergeregex'] = $js_excludemergeregex;
            if ($options['js_mergeinline'] === 'head') {
                $ress_options['js']['mergeinline'] = 'head';
            } else {
                $ress_options['js']['mergeinline'] = (bool)$options['js_mergeinline'];
            }
            $ress_options['js']['autoasync'] = (bool)$options['js_autoasync'];
            $ress_options['js']['minifyattribute'] = (bool)$options['js_minifyattribute'];
            $ress_options['js']['inlinelimit'] = (int)$options['js_inlinelimit'];
            $ress_options['js']['crossfileoptimization'] = (bool)$options['js_crossfileoptimization'];
            $ress_options['js']['wraptrycatch'] = (bool)$options['js_wraptrycatch'];
            $ress_options['js']['checkattributes'] = (bool)$options['js_checkattributes'];
            $ress_options['js']['skipinits'] = (bool)$options['js_skipinits'];
            if ($options['js_widgets']) {
                $ress_options['plugins']['Ressio_Plugin_Widgets'] = null;
            }
            switch ($options['js_di_jsMinify']) {
                case 'none':
                    $ress_options['di']['jsMinify'] = 'Ressio_JsMinify_None';
                    break;
                case 'jsmin':
                    $ress_options['di']['jsMinify'] = 'Ressio_JsMinify_JsMin';
                    break;
                default:
                    trigger_error('PageSpeed Ninja: unknown js_di_jsMinify value ' . var_export($options['js_di_jsMinify'], true));
            }
        }

        if ($options['psi_MinimizeRenderBlockingResources']) {
            if ($options['css_abovethefold']) {
                $ress_options['plugins']['Ressio_Plugin_Abovethefoldcss'] = array(
                    'cookie' => (
                        $options['css_abovethefoldcookie'] &&
                        !(defined('WP_CACHE') && WP_CACHE)
                    ) ? 'psn_atf' : '',
                    'cookietime' => 24 * 60 * 60,
                    'abovethefoldcss' => $options['css_abovethefoldstyle']
                );
            }
            if ($options['css_nonblockjs']) {
                $ress_options['plugins']['Ressio_Plugin_Nonblockjs'] = array();
            }
            switch ($options['css_googlefonts']) {
                case 'fout':
                    $ress_options['plugins']['Ressio_Plugin_GoogleFont'] = array('method' => 'fout');
                    break;
                case 'foit':
                case 'sync':
                    $ress_options['plugins']['Ressio_Plugin_GoogleFont'] = array('method' => 'foit');
                    break;
                case 'async':
                    $ress_options['plugins']['Ressio_Plugin_GoogleFont'] = array('method' => 'async');
                    break;
                case 'none':
                    break;
            }
        }

        if ($options['psi_OptimizeImages']) {
            $ress_options['img']['minify'] = $options['img_minify'];
            switch ($options['img_driver']) {
                case 'imagick':
                    $imgOptimizer_class = 'Ressio_ImgOptimizer_Imagick';
                    $imgRescale_class = 'Ressio_ImgRescale_Imagick';
                    break;
                case 'gd2':
                default:
                    $imgOptimizer_class = 'Ressio_ImgOptimizer_GD';
                    $imgRescale_class = 'Ressio_ImgRescale_GD';
                    break;
            }
            $ress_options['di']['imgOptimizer'] = $imgOptimizer_class;
            $ress_options['di']['imgRescale'] = $imgRescale_class;
            $ress_options['di']['imgOptimizer.gif'] = $imgOptimizer_class;
            $ress_options['di']['imgOptimizer.jpg'] = $imgOptimizer_class;
            $ress_options['di']['imgOptimizer.png'] = $imgOptimizer_class;
            $ress_options['di']['imgOptimizer.webp'] = $imgOptimizer_class;
            // @todo extract creation of svgz into EnableGzipCompression section
            $ress_options['di']['imgOptimizer.svg'] = 'Ressio_ImgOptimizer_SvgGz';
            /* @todo add svg.gz optimization (if .htaccess is optimized) */
            $ress_options['img']['jpegquality'] = (int)$options['img_jpegquality'];
            if ($options['img_scaletype'] !== 'none') {
                include_once dirname(__FILE__) . '/ress/wprescale.php';
                $ress_options['plugins']['Ressio_Plugin_WpRescale'] = array(
                    'scaletype' => $options['img_scaletype'],
                    'bufferwidth' => (int)$options['img_bufferwidth'],
                    'keeporig' => false,
                    'setdimension' => true,
                    'templatewidth' => (int)$options['img_templatewidth'],
                    'wideimgclass' => $options['img_wideimgclass'],
                    'wrapwideimg' => (bool)$options['img_wrapwide'],
                );
            }
        }
        if ($options['psi_PrioritizeVisibleContent']) {
            // @todo (most options are shared with MinimizeRenderBlockingResources)
            if ($options['img_lazyload'] || $options['img_lazyload_iframe']) {
                include_once dirname(__FILE__) . '/ress/wplazyload.php';
                $ress_options['plugins']['Ressio_Plugin_WpLazyload'] = array(
                    'image' => (bool)$options['img_lazyload'],
                    'iframe' => (bool)$options['img_lazyload_iframe'],
                    'lqip' => (bool)$options['img_lazyload_lqip'],
                    'edgey' => (int)$options['img_lazyload_edgey'],
                    'noscriptpos' => $options['img_lazyload_noscript'],
                    'skipimages' => (int)$options['img_lazyload_skip'],
                    'addsrcset' => (bool)$options['img_lazyload_addsrcset']
                );
                if ($options['img_lazyload_addsrcset']) {
                    global $_wp_additional_image_sizes;
                    $widths = array();
                    foreach ($_wp_additional_image_sizes as $name => $sizes) {
                        if (!$sizes['crop'] && $sizes['width'] > 1) {
                            $widths[$sizes['width']] = 1;
                        }
                    }
                    $ress_options['plugins']['Ressio_Plugin_WpLazyload']['srcsetwidth'] = array_keys($widths);
                }
            }
        }

        $ressio = new Ressio($ress_options);

        $this->foundTime = time();
        // @todo use another events to don't update database every time
        // @todo (e.g. trigger events in addJs() and addCss() methods)
        // @todo (but may conflict UrlLoader plugin)
        if ($options['psi_MinifyJavaScript']) {
            $ressio->di->dispatcher->addListener('HtmlIterateTagSCRIPTBefore', array($this, 'collectScriptURLs'), -1);
        }
        if ($options['psi_MinifyCss']) {
            $ressio->di->dispatcher->addListener('HtmlIterateTagLINKBefore', array($this, 'collectStyleURLs'), -1);
        }

        if ($options['psi_AvoidPlugins']) {
            if ($options['remove_objects']) {
                $ressio->di->dispatcher->addListener(
                    array(
                        'HtmlIterateTagOBJECTBefore',
                        'HtmlIterateTagEMBEDBefore',
                        'HtmlIterateTagAPPLETBefore'
                    ),
                    array($this, 'RessioRemoveTag')
                );
            }
        }

        if ($options['psi_ConfigureViewport']) {
            $this->viewportWidth = $options['viewport_width'];
            $ressio->di->dispatcher->addListener('HtmlIterateAfter', array($this, 'RessioAddViewportTag'));
        }

        $ressio->di->dispatcher->addListener('RunAfter', array($this, 'onRunAfter'));
        $buffer = $ressio->run($buffer);

        global $pagespeedninja_cache;
        if ($pagespeedninja_cache !== null) {
            $this->tryPurgeCache($ressio);
        }

        Ressio::unregisterAutoloading();

        return $buffer;
    }

    /**
     * @param Ressio_Event $event
     * @param IRessio_HtmlOptimizer $optimizer
     * @param IRessio_HtmlNode $node
     */
    public function RessioRemoveTag($event, $optimizer, $node)
    {
        $optimizer->nodeDetach($node);
    }

    /**
     * @param Ressio_Event $event
     * @param IRessio_HtmlOptimizer $optimizer
     */
    public function RessioAddViewportTag($event, $optimizer)
    {
        $width = $this->viewportWidth === '0' ? 'device-width' : $this->viewportWidth;
        $optimizer->prependHead(
            array('meta', array('name' => 'viewport', 'content' => 'width=' . $width . ', initial-scale=1'), false)
        );
    }

    /**
     * @param Ressio_Event $event
     * @param string $buffer
     */
    public function onRunAfter($event, &$buffer)
    {
        do_action('pagespeedninja_cache_save', $buffer);

        global $wpdb;
        $time = date('Y-m-d H:i:s');
        $values = array();
        foreach ($this->foundScripts as $url) {
            $url = preg_replace('/[?&]ver=[^?&]*$/', '', $url);
            $values[] = $wpdb->prepare('(%s, UNHEX(%s), %s, %d)', $url, sha1($url), $time, 1);
        }
        foreach ($this->foundStyles as $url) {
            $url = preg_replace('/[?&]ver=[^?&]*$/', '', $url);
            $values[] = $wpdb->prepare('(%s, UNHEX(%s), %s, %d)', $url, sha1($url), $time, 2);
        }

        if (count($values)) {
            $sql = "INSERT IGNORE INTO `{$wpdb->prefix}psninja_urls` (`url`, `hash`, `time`, `type`) VALUES " . implode(',', $values) . ';';
            $wpdb->query($sql);
        }
    }

    /**
     * @param Ressio $ressio
     */
    protected function tryPurgeCache($ressio)
    {
        $filelock = $ressio->di->filelock;
        $fs = $ressio->di->filesystem;

        $lock = PAGESPEEDNINJA_CACHE_DIR . '/cachecleaner.stamp';

        if (!$fs->isFile($lock)) {
            $fs->touch($lock);
            return;
        }
        if (!$filelock->lock($lock)) {
            return;
        }

        $cache_timestamp = @filemtime(PAGESPEEDNINJA_CACHE_DIR . '/pagecache.stamp');
        $aging_time = max($cache_timestamp, time() - PAGESPEEDNINJA_CACHE_TTL);
        if ($fs->getModificationTime($lock) > $aging_time) {
            $filelock->unlock($lock);
            return;
        }

        $fs->touch($lock);
        $filelock->unlock($lock);

        global $pagespeedninja_cache;
        $pagespeedninja_cache->purgeCache();
    }

    /**
     * @param Ressio_Event $event
     * @param IRessio_HtmlOptimizer $optimizer
     * @param IRessio_HtmlNode $node
     */
    public function collectScriptURLs($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->foundScripts[] = $node->getAttribute('src');
        }
    }

    /**
     * @param Ressio_Event $event
     * @param IRessio_HtmlOptimizer $optimizer
     * @param IRessio_HtmlNode $node
     */
    public function collectStyleURLs($event, $optimizer, $node)
    {
        if (
            $node->hasAttribute('rel') && $node->hasAttribute('href') &&
            $node->getAttribute('rel') === 'stylesheet'
        ) {
            $this->foundStyles[] = $node->getAttribute('href');
        }
    }

    public function wp_footer()
    {
        if (!$this->started) {
            return;
        }

        $options = get_option('pagespeedninja_config');
        $footer = $options['footer'] === '1';
        echo $footer ? '<small class="pagespeedninja" style="display:block;text-align:center">' : '<!-- ';
        echo sprintf(__('Optimized with <a href="%s">PageSpeed Ninja</a>'), 'https://pagespeed.ninja/');
        echo $footer ? '</small>' : ' -->';
    }

    public function print_emoji_detection_script()
    {
        $settings = array(
            'baseUrl' => apply_filters('emoji_url', 'https://s.w.org/images/core/emoji/2.2.1/72x72/'),
            'ext' => apply_filters('emoji_ext', '.png'),
            'svgUrl' => apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2.2.1/svg/'),
            'svgExt' => apply_filters('emoji_svg_ext', '.svg'),
        );

        $version = 'ver=' . get_bloginfo('version');
        $file = apply_filters('script_loader_src', includes_url("js/wp-emoji-release.min.js?$version"), 'concatemoji');

        ?><script type="text/javascript" ress-merge>window._wpemojiSettings =<?php echo wp_json_encode($settings); ?>;</script><?php
        ?><script type="text/javascript" src="<?php echo $file; ?>" ress-merge></script><?php
    }
}
