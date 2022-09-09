<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

// @todo Ressio optimizer supports PHP 5.2 (minimal version required by WordPress),
// @todo consider to rewrite it for modern PHP after WordPress changes it

// @todo Use DI container instead of constants
if (!defined('RESSIO_PATH')) {
    define('RESSIO_PATH', dirname(__FILE__));
}
if (!defined('RESSIO_LIBS')) {
    define('RESSIO_LIBS', RESSIO_PATH . DIRECTORY_SEPARATOR . 'vendor');
}

class Ressio
{
    /** @var Ressio_DI */
    public $di;
    /** @var Ressio_Config */
    public $config;

    /** @var bool */
    protected static $registeredAutoloading = false;

    /**
     * @param array $override_config
     * @param bool $prepend_autoloader
     * @throws ERessio_UnknownDiKey
     * @throws ERessio_Exception
     */
    public function __construct($override_config = null, $prepend_autoloader = false)
    {
        if (!isset($override_config['disable_autoload']) || !$override_config['disable_autoload']) {
            self::registerAutoloading($prepend_autoloader);
        }

        $this->di = new Ressio_DI();

        /** @var Ressio_Config $config */
        $config = self::loadConfig($override_config);

        $this->config = $config;
        $this->di->config = $config;

        if (isset($config->di)) {
            /** @var array {$config->di} */
            foreach ($config->di as $key => $call) {
                $this->di->set($key, $call);
            }
        }

        if (isset($config->plugins)) {
            $dispatcher = $this->di->dispatcher;
            foreach ($config->plugins as $pluginClassname => &$options) {
                /** @var Ressio_Plugin $plugin */
                $plugin = new $pluginClassname($this->di, $options);
                $options = $plugin;
                $priorities = $plugin->getEventPriorities();
                foreach (get_class_methods($plugin) as $method) {
                    /** @var string $method */
                    if (strncmp($method, 'on', 2) === 0) {
                        $eventName = substr($method, 2);
                        $priority = isset($priorities[$eventName]) ? $priorities[$eventName] : 0;
                        $dispatcher->addListener($eventName, array($plugin, $method), $priority);
                    }
                }
            }
        }
    }

    /**
     * @param $prepend_autoloader bool
     */
    public static function registerAutoloading($prepend_autoloader = false)
    {
        if (self::$registeredAutoloading) {
            return;
        }
        self::$registeredAutoloading = true;
        if (function_exists('__autoload')) {
            spl_autoload_register('__autoload');
        }
        if ($prepend_autoloader && version_compare(PHP_VERSION, '5.3', '>=')) {
            spl_autoload_register(array(__CLASS__, 'autoloader'), true, true);
        } else {
            spl_autoload_register(array(__CLASS__, 'autoloader'));
        }
    }

    public static function unregisterAutoloading()
    {
        spl_autoload_unregister(array(__CLASS__, 'autoloader'));
    }

    /**
     * @param array $override_config
     * @return Ressio_Config
     */
    public static function loadConfig($override_config = null)
    {
        // @todo Merge config_base.php and config_user.php

        /** @var Ressio_Config $config */
        $config = new stdClass;
        self::merge_objects($config, include RESSIO_PATH . '/config.php');

        if ($override_config !== null) {
            self::merge_objects($config, $override_config);
        }

        if (empty($config->webrootpath)) {
            $config->webrootpath = substr($_SERVER['SCRIPT_FILENAME'], 0, -strlen($_SERVER['SCRIPT_NAME']));
        }
        if (DIRECTORY_SEPARATOR !== '/') {
            $config->webrootpath = str_replace('/', DIRECTORY_SEPARATOR, $config->webrootpath);
        }
        if (strncmp($config->staticdir, './', 2) === 0) {
            $ress_uri = str_replace(DIRECTORY_SEPARATOR, '/', substr(RESSIO_PATH, strlen($config->webrootpath)));
            $config->staticdir = $ress_uri . substr($config->staticdir, 1);
        }

        if (strncmp($config->cachedir, './', 2) === 0) {
            $config->cachedir = RESSIO_PATH . substr($config->cachedir, 1);
        }
        if (strncmp($config->fileloaderphppath, './', 2) === 0) {
            $config->fileloaderphppath = RESSIO_PATH . substr($config->fileloaderphppath, 1);
        }
        if (strncmp($config->amdd->dbPath, './', 2) === 0) {
            $config->amdd->dbPath = RESSIO_PATH . substr($config->amdd->dbPath, 1);
        }

        return $config;
    }

    private static function merge_objects(&$obj, $obj2)
    {
        foreach ($obj2 as $key => $value) {
            if ((is_array($value) && !isset($value[0])) || is_object($value)) {
                if (!isset($obj->$key)) {
                    $obj->$key = new stdClass;
                }
                self::merge_objects($obj->$key, $value);
            } else {
                $obj->$key = $value;
            }
        }
    }

    /**
     * @param string $class
     */
    public static function autoloader($class)
    {
        $pos = strpos($class, 'Ressio_');
        if ($pos === false) {
            return;
        }

        // remove possible namespace prefix
        if ($class[0] === '\\') {
            $class = substr($class, 1);
            $pos--;
        }

        if ($pos === 0) {
            if (strncmp($class, 'Ressio_Plugin_', 14) === 0) {
                // Ressio_Plugin_Name -> Ressio_Plugin_Name_Name -> plugin/name/name.php
                $class = preg_replace('#(?<=^Ressio_Plugin_)([^_]+)$#', '\1_\1', $class);
            }
            $dir = '/classes/';
        } elseif ($pos === 1) {
            switch ($class[0]) {
                case 'I':
                    $dir = '/classes/interfaces/';
                    break;
                case 'E':
                    $dir = '/classes/exceptions/';
                    break;
                default:
                    return;
            }
        } else {
            return;
        }

        $path = RESSIO_PATH . $dir . str_replace('_', '/', strtolower(substr($class, $pos + 7))) . '.php';
        if (file_exists($path)) {
            include_once $path;
        }
    }

    /**
     * @param $buffer string
     * @return string
     */
    public function ob_callback($buffer)
    {
        // disable any output in ob handler
        $display_errors = ini_get('display_errors');
        ini_set('display_errors', 0);

        $buffer = Ressio_Helper::removeBOM($buffer);
        $result = $this->run($buffer);

        ini_set('display_errors', $display_errors);
        return $result;
    }

    /**
     * @param $content string
     * @return string
     */
    public function run($content)
    {
        $buffer = Ressio_Helper::removeBOM($content);

        //@todo optional cache optimized result

        try {
            $cached = false;

            if ($this->config->cachefast) {
                $cache = $this->di->cache;
                $cache_id = $cache->id(array(json_encode($this->config), $buffer), 'fast');
                $data = $cache->getOrLock($cache_id);
                if (is_string($data)) {
                    $cached = true;
                    list($headers, $buffer) = explode("\n\n", $data, 2);
                    $buffer = gzdecode($buffer);
                    $headers = explode("\n", $headers);
                    foreach ($headers as $header) {
                        Ressio_Helper::setHeader($header, false);
                    }
                }
            }

            if (!$cached) {
                $optimizer = $this->di->htmlOptimizer;
                $this->di->dispatcher->triggerEvent('RunBefore', array(&$buffer, $optimizer));
                $buffer = $optimizer->run($buffer);
                $this->di->dispatcher->triggerEvent('RunAfter', array(&$buffer));

                if ($this->config->cachefast) {
                    /** @var array $data */
                    $data = array();
                    foreach (Ressio_Helper::getHeaders() as $line) {
                        if (!is_array($line)) {
                            $data[] = $line;
                        } else {
                            foreach ($line as $header_line) {
                                $data[] = $header_line;
                            }
                        }
                    }
                    $data = implode("\n", $data) . "\n\n" . gzencode($buffer, 5);
                    $cache->storeAndUnlock($cache_id, $data);
                }
            }

            if ($this->config->html->gzlevel) {
                //@todo move Ressio_CompressOutput to DI
                Ressio_CompressOutput::init($this->config->html->gzlevel, false);
                $buffer = Ressio_CompressOutput::compress($buffer);
            }

            //@todo presend header event
            //@todo Use DI for sendHeaders to simplify intergation with 3rdparty frameworks
            Ressio_Helper::sendHeaders();
        } catch (Exception $e) {
            $this->di->logger->warning('Catched error in Ressio::run: ' . $e->getMessage());
            return $content;
        }

        return $buffer;
    }
}
