<?php
defined('ABSPATH') || die();

global $pagespeedninja_cache;
$pagespeedninja_cache = new PagespeedNinja_Cache();

class PagespeedNinja_Cache
{
    public $enabled = true;
    public $uri_hash;
    public $cache_file;
    public $headers = array();

    public function __construct()
    {
        if ($this->disabledCaching()) {
            $this->enabled = false;
            return;
        }

        // @todo Should PageSpeed Ninja affect logged users (advantage: smaller page size, disadvantage: longer page generation time)

        $uri = $_SERVER['REQUEST_URI'];
        if ($uri === '/index.php') {
            $uri = '/';
        }

        $this->uri_hash = $this->getRequestHash($uri);

        // get cached content
        $cache_dir = PAGESPEEDNINJA_CACHE_DIR . '/' . substr($this->uri_hash, 0, 2);
        $this->cache_file = $cache_dir . '/' . $this->uri_hash;
        if (!is_dir($cache_dir) && !@mkdir($cache_dir) && !is_dir($cache_dir)) {
            // cannot create directory
            $this->enabled = false;
            return;
        }

        $cache = $this->readData();

        if ($cache === false) {
            add_filter('wp_headers', array($this, 'save_headers'));
            add_filter('status_header', array($this, 'save_status'), 0, 2);
            add_action('pagespeedninja_cache_save', array($this, 'save'));

            return;
        }

        list($hash, $headers, $content) = explode("\n\n", $cache, 3);
        $headers = explode("\n", $headers);

        $client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : false;
        $etag = '"' . $hash . '"';

        // check ETag
        if ($client_etag === $etag) {
            $http1x = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
            header($http1x . ' 304 Not Modified', true, 304);
            header('Status: 304 Not Modified');
            header('ETag: ' . $etag);
            exit(0);
        }

        // print, set Ressio's caching headers & exit
        foreach ($headers as $header) {
            header($header);
        }
        header('ETag: ' . $etag);
        header('Cache-Control: public');

        $encoding = false;
        if (extension_loaded('zlib') && !ini_get('zlib.output_compression')) {
            include_once PAGESPEEDNINJA_CACHE_RESSDIR . '/classes/helper.php';
            $encoding = Ressio_Helper::getRequestedCompression();
        }

        $encoded = false;
        switch ($encoding) {
            case 'deflate':
                $cache_file_encoded = $this->cache_file . '.zz';
                $encoded = $this->readFileThreadSafe($cache_file_encoded);
                if ($encoded === false) {
                    $encoded = gzdeflate($content, 9);
                    if ($encoded !== false) {
                        $this->writeFileThreadSafe($cache_file_encoded, $encoded);
                    }
                }
                break;
            case 'gzip':
            case 'x-gzip':
                $cache_file_encoded = $this->cache_file . '.gz';
                $encoded = $this->readFileThreadSafe($cache_file_encoded);
                if ($encoded === false) {
                    $encoded = gzencode($content, 9);
                    if ($encoded !== false) {
                        $this->writeFileThreadSafe($cache_file_encoded, $encoded);
                    }
                }
                break;
            default:
                break;
        }

        if ($encoded !== false) {
            header('Vary: Accept-Encoding');
            header('Content-Encoding: ' . $encoding);
            header('Content-Length: ' . strlen($encoded));
            echo $encoded;
        } else {
            echo $content;
        }

        flush();
        exit(0);
    }

    /**
     * @param array $headers
     * @return array
     */
    public function save_headers($headers)
    {
        unset($headers['ETag']);
        $this->headers = array();
        foreach ($headers as $name => $value) {
            $this->headers[] = "$name: $value";
        }
        return $headers;
    }

    /**
     * @param string $status_header
     * @param int $code
     * @return string
     */
    public function save_status($status_header, $code)
    {
        // disable caching of errors and redirects
        if ($code !== 200) {
            $this->enabled = false;
        }
        return $status_header;
    }

    public function save($content)
    {
        if (
            !$this->enabled
            || (defined('DONOTCACHEPAGE') && DONOTCACHEPAGE)
            || is_user_logged_in()
            || is_search()
        ) {
            return;
        }

        $hash = sha1($this->uri_hash . time());
        $etag = '"' . $hash . '"';

        header('ETag: ' . $etag);
        header('Cache-Control: public');

        $data = $hash . "\n\n"
            . implode("\n", $this->headers) . "\n\n"
            . $content;

        $this->writeData($data);
    }

    /**
     * Purge old cache files
     * @param integer $ttl
     */
    public function purgeCache($ttl = null)
    {
        if ($ttl === null) {
            $ttl = PAGESPEEDNINJA_CACHE_TTL;
        }

        $cache_timestamp = @filemtime(PAGESPEEDNINJA_CACHE_DIR . '/pagecache.stamp');
        // -1h to fix mtime with DST on Windows
        $aging_time = ($ttl === 0) ? (time() - 1) : max($cache_timestamp, time() - $ttl) - 60 * 60;

        // iterate cache directory
        foreach (scandir(PAGESPEEDNINJA_CACHE_DIR, @constant('SCANDIR_SORT_NONE')) as $subdir) {
            $subdir_path = PAGESPEEDNINJA_CACHE_DIR . '/' . $subdir;
            if ($subdir[0] === '.' || !is_dir($subdir_path)) {
                continue;
            }
            $h = opendir($subdir_path);
            $files = 0;
            while (($file = readdir($h)) !== false) {
                $file_path = $subdir_path . '/' . $file;
                if ($file[0] === '.') {
                    continue;
                }
                $orig_file_path = preg_replace('/\\.[gz]z$/', '', $file_path);
                if (!is_file($orig_file_path) || @filemtime($orig_file_path) < $aging_time) {
                    unlink($file_path);
                } else {
                    $files++;
                }
            }
            closedir($h);
            if ($files === 0) {
                @rmdir($subdir_path);
            }
        }
    }

    /**
     * @return bool
     */
    protected function disabledCaching()
    {
        return
            // other entry points or debug mode
            defined('DOING_AJAX') || defined('DOING_CRON') ||
            defined('WP_INSTALLING') ||
            defined('XMLRPC_REQUEST') || defined('REST_REQUEST') ||
            (defined('WP_ADMIN') && WP_ADMIN) ||
            (defined('WP_DEBUG') && WP_DEBUG) ||
            (defined('SHORTINIT') && SHORTINIT) ||

            // post request
            ($_SERVER['REQUEST_METHOD'] !== 'GET') ||

            // pagespeed-ninja disabled mode
            (isset($_GET['pagespeedninja']) && $_GET['pagespeedninja'] === 'no') ||

            // preview post
            isset($_GET['preview']) ||

            // WordPress file editor test
            isset($_GET['wp_scrape_key']) ||

            // Beaver Builder
            isset($_GET['fl_builder']) ||

            // Massive Dynamic Live Website Builder
            isset($_GET['mbuilder']) ||

            // headers sent (error/warning/etc.)
            headers_sent() ||

            // a logged user
            (defined('AUTH_COOKIE') && isset($_COOKIE[AUTH_COOKIE])) ||
            (defined('SECURE_AUTH_COOKIE') && isset($_COOKIE[SECURE_AUTH_COOKIE])) ||
            (defined('LOGGED_IN_COOKIE') && isset($_COOKIE[LOGGED_IN_COOKIE])) ||
            strpos("\n" . implode("\n", array_keys($_COOKIE)), "\nwordpress_") !== false ||

            // WooCommerce
            isset($_COOKIE['woocommerce_items_in_cart']) ||

            // a commenter
            (defined('COOKIEHASH') && isset($_COOKIE['comment_author_' . COOKIEHASH]))
            ;
    }

    /**
     * @param string $uri
     * @return string
     */
    protected function getRequestHash($uri)
    {
        $hash_data = array();

        $hash_data[] = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : '';
        $hash_data[] = $_SERVER['HTTP_HOST'];
        $hash_data[] = isset($_SERVER['HTTP_PORT']) ? $_SERVER['HTTP_PORT'] : '';
        $hash_data[] = $uri;

        if (PAGESPEEDNINJA_CACHE_DEVICEDEPENDENT) {

            define('RESSIO_LIBS', PAGESPEEDNINJA_CACHE_RESSDIR . '/vendor');
            if (!class_exists('AmddUA', false)) {
                include_once RESSIO_LIBS . '/amdd/ua.php';
            }
            if (!class_exists('Amdd', false)) {
                include_once RESSIO_LIBS . '/amdd/amdd.php';
            }

            $ua = AmddUA::getUserAgentFromRequest();

            include_once PAGESPEEDNINJA_CACHE_RESSDIR . '/classes/interfaces/devicedetector.php';
            include_once PAGESPEEDNINJA_CACHE_RESSDIR . '/classes/devicedetector/base.php';
            include_once PAGESPEEDNINJA_CACHE_RESSDIR . '/classes/devicedetector/amdd.php';

            $detector = new Ressio_DeviceDetector_AMDD($ua);
            $hash_data[] = ($detector->vendor() === 'ms') ? 'ms' : '';

            try {
                global $table_prefix;
                $caps = Amdd::getCapabilities($ua, false, array(
                    'handler' => 'mysqli',
                    'cacheSize' => 1000,
                    'dbTableName' => $table_prefix . 'psninja_amdd',
                    'dbHost' => DB_HOST,
                    'dbUser' => DB_USER,
                    'dbPassword' => DB_PASSWORD,
                    'dbDatabase' => DB_NAME,
                ));
            } catch (AmddDatabaseException $e) {
                $caps = new stdClass();
            }
            $hash_data[] = (isset($caps->screenWidth, $caps->screenHeight) ? $caps->screenWidth . 'x' . $caps->screenHeight : '') .
                '@' . (isset($caps->pixelRatio) ? $caps->pixelRatio : '1');

        }

        return sha1(implode('|', $hash_data));
    }

    /**
     * @param string $data
     * @return bool
     */
    protected function writeData($data)
    {
        return $this->writeFileThreadSafe($this->cache_file, $data);
    }

    /**
     * @return string|false
     */
    protected function readData()
    {
        if (!file_exists($this->cache_file)) {
            return false;
        }

        $timestamp = filemtime($this->cache_file);
        $cache_timestamp = @filemtime(PAGESPEEDNINJA_CACHE_DIR . '/pagecache.stamp');
        if ($timestamp < $cache_timestamp || $timestamp < time() - PAGESPEEDNINJA_CACHE_TTL) {
            return false;
        }

        return $this->readFileThreadSafe($this->cache_file);
    }

    /**
     * @param $filename string
     * @param $data string
     * @return bool
     */
    protected function writeFileThreadSafe($filename, $data)
    {
        if (file_put_contents($filename, $data, LOCK_EX) === strlen($data)) {
            return true;
        }

        @unlink($filename);
        return false;
    }

    /**
     * @param $filename string
     * @return bool|string
     */
    protected function readFileThreadSafe($filename)
    {
        if (!file_exists($filename)) {
            return false;
        }

        $f = fopen($filename, 'rb');
        if (!flock($f, LOCK_SH)) {
            fclose($f);
            return false;
        }
        $data = file_get_contents($filename);
        flock($f, LOCK_UN);
        fclose($f);

        return $data;
    }
}
