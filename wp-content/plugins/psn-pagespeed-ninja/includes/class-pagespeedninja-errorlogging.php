<?php

class PagespeedNinja_ErrorLogging
{
    protected static $error_types_map = array(
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE ERROR',
        E_CORE_WARNING => 'CORE WARNING',
        E_COMPILE_ERROR => 'COMPILE ERROR',
        E_COMPILE_WARNING => 'COMPILE WARNING',
        E_USER_ERROR => 'USER ERROR',
        E_USER_WARNING => 'USER WARNING',
        E_USER_NOTICE => 'USER NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER DEPRECATED',
        65536 => 'EXCEPTION'
    );

    protected static $error_log;

    public static function init()
    {
        /** @var array $options */
        $options = get_option('pagespeedninja_config');
        if (isset($options['errorlogging']) && !$options['errorlogging']) {
            return;
        }

        // enable logging for backend settings page and frontend pages only
        if (is_admin() && !(isset($_GET['page']) && $_GET['page'] === 'pagespeedninja')) {
            return;
        }

        self::$error_log = dirname(__FILE__) . '/error_log.php';
        if (!is_file(self::$error_log)) {
            $header = array();
            $header[] = '<?php die(); __halt_compiler();';
            $header[] = '/*';
            $header[] = '';
            $header[] = 'Domain: ' . $_SERVER['HTTP_HOST'];
            $header[] = '';
            $header[] = 'PHP version: ' . PHP_VERSION;
            $header[] = 'Interface: ' . $_SERVER['GATEWAY_INTERFACE'];
            $header[] = 'Server API: ' . PHP_SAPI;
            $header[] = 'Server: ' . $_SERVER['SERVER_SOFTWARE'];
            $header[] = 'OS: ' . php_uname();
            $header[] = '';
            $header[] = 'Wordpress version: ' . $GLOBALS['wp_version'];
            $header[] = 'Activated plugins:';
            /** @var array $plugins */
            $plugins = get_option('active_plugins');
            $plugins_dir = plugin_dir_path(dirname(dirname(__FILE__)));
            foreach ($plugins as $plugin) {
                $data = get_file_data($plugins_dir . $plugin, array('name' => 'Plugin Name', 'version' => 'Version'));
                $header[] = '  ' . $data['name'] . ' ' . $data['version'] . ' (' . $plugin . ')';
            }
            $header[] = '';
            $header[] = '*/';
            $header[] = '?>';
            file_put_contents(self::$error_log, implode(PHP_EOL, $header) . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        set_error_handler(array(__CLASS__, 'log_error'));
        set_exception_handler(array(__CLASS__, 'log_exception'));
        register_shutdown_function(array(__CLASS__, 'log_fatal'));
    }

    public static function log_fatal()
    {
        $error = error_get_last();
        if ($error !== null && $error['type'] === E_ERROR) {
            self::log_error($error['type'], $error['message'], $error['file'], $error['line'], null, debug_backtrace());
        }
    }

    /**
     * @param Exception $e
     */
    public static function log_exception($e)
    {
        self::log_error(65536, 'Type ' . get_class($e) . '; ' . $e->getMessage(), $e->getFile(), $e->getLine(), null, $e->getTrace());
    }

    /**
     * @param int $type
     * @param string $message
     * @param string $file
     * @param int $line
     * @param array $errcontext
     * @param array $trace
     * @return bool
     */
    public static function log_error($type, $message, $file, $line, $errcontext = null, $trace = null)
    {
        if ($trace === null) {
            $trace = debug_backtrace();
        }
        $strTrace = array();
        /** @var array $trace */
        foreach ($trace as $item) {
            if (isset($item['file'], $item['line'])) {
                $strTrace[] = $item['file'] . ':' . $item['line'];
            }
        }
        $strTrace = implode(' <- ', $strTrace);

        if (isset(self::$error_types_map[$type])) {
            $type = self::$error_types_map[$type];
        }

        $message = '[' . date('r') . "] $type:  $message  in $file on line $line" . PHP_EOL .
            "                                  $strTrace" . PHP_EOL;

        file_put_contents(self::$error_log, $message, FILE_APPEND | LOCK_EX);

        return false;
    }
}

