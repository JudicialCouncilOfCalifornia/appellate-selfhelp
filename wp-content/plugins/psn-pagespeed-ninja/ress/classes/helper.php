<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Helper class
 */
class Ressio_Helper
{
    /**
     * @var int gzip compression level (1-9, 1 is faster, 9 is smaller)
     */
    public static $gzLevel = 9;

    /**
     * Get hash of string
     * @param string $string
     * @param int $size
     * @return string
     */
    public static function hash($string, $size = 40)
    {
        return substr(sha1($string), 0, $size);
    }

    /**
     * Compress and convert $str into url-safe format
     * @static
     * @param string $str
     * @return string
     */
    public static function encode($str)
    {
        $str = gzdeflate($str, self::$gzLevel);
        $str = base64_encode($str);
        $str = rtrim($str, '=');
        $str = strtr($str, '+/', '-_'); // RFC 4648 'base64url' encoding
        return $str;
    }

    /**
     * Decode data encoded by RbHelper::encode method
     * @static
     * @param string $str
     * @return string (or false on error)
     */
    public static function decode($str)
    {
        $str = strtr($str, '-_', '+/');
        $pad = strlen($str) % 4;
        if ($pad) {
            $str .= str_repeat('=', 4 - $pad);
        }
        $str = base64_decode($str);
        $str = gzinflate($str);
        return $str;
    }

    /**
     * Remove UTF-8 byte-order-mark (BOM)
     * @static
     * @param string $str
     * @return string
     */
    public static function removeBOM($str)
    {
        // other BOM sequences are not used in webdev
        if (strncmp($str, "\xEF\xBB\xBF", 3) === 0) {
            $str = substr($str, 3);
        }
        return $str;
    }

    /**
     * Get URL to ressio directory ("/ress/" usually)
     * @static
     * @return string
     */
    public static function ressioURL()
    {
        static $url;
        if ($url === null) {
            if (strpos(PHP_SAPI, 'cgi') !== false && !ini_get('cgi.fix_pathinfo') && !empty($_SERVER['REQUEST_URI'])) {
                $script_name = $_SERVER['PHP_SELF'];
            } else {
                $script_name = $_SERVER['SCRIPT_NAME'];
            }

            $basepath = rtrim(dirname($script_name), '/\\');
            if ($basepath === '.') {
                $basepath = '';
            }

            $rootpath = dirname($_SERVER['SCRIPT_FILENAME']);
            if ($rootpath === '.') {
                $rootpath = '';
            }
            $ressioPath = '';

            if (strpos(RESSIO_PATH, $rootpath) === 0) {
                $ressioPath = substr(RESSIO_PATH, strlen($rootpath));
            }

            $url = $basepath . $ressioPath . '/';
        }
        return $url;
    }

    /**
     * Get requested compression mode
     * @todo move to RbGzip class
     * @static
     * @return string|bool
     * false - no compression
     * 'deflate' - deflate method
     * 'gzip'/'x-gzip' - gzip method
     * 'compress'/'x-compress' - compress method
     */
    public static function getRequestedCompression()
    {
        /** @var string|bool $method */
        static $method;
        if ($method !== null) {
            return $method;
        }

        $method = false;

        // check zlib
        if (!extension_loaded('zlib')) {
            return false;
        }

        // Workaround for IE5.5 and IE6
        // More info about issue:
        // http://support.microsoft.com/default.aspx?scid=kb;en-us;Q313712
        // http://support.microsoft.com/default.aspx?scid=kb;en-us;Q312496
        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $iePrefix = 'Mozilla/4.0 (compatible; MSIE ';
        if (strpos($ua, $iePrefix) === 0 && strpos($ua, 'Opera') === false) {
            $version = (int)substr($ua, strlen($iePrefix));
            // version less than IE6 SP1 (Windows XP SP2)
            if ($version === 5 || ($version === 6 && strpos($ua, 'SV1') === false)) {
                return false;
            }
        }

        // parse Accept-Encoding header

        // list methods in decreasing compression level order
        static $supportedMethods = array('deflate', 'gzip', 'x-gzip', 'compress', 'x-compress');

        $acceptEncoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '';
        /** @var array $acceptEncoding */
        $acceptEncoding = explode(',', $acceptEncoding);

        $bestQ = 0.0;
        $bestMethodId = -1;
        if (is_array($acceptEncoding)) {
            /** @var string[] $acceptEncoding */
            foreach ($acceptEncoding as $encoding) {
                $encoding = preg_split('#;\s*q=#', $encoding, 2);
                $q = isset($encoding[1]) ? (float)$encoding[1] : 1.0;
                $encoding = strtolower(trim($encoding[0]));

                $methodId = array_search($encoding, $supportedMethods, true);
                if ($methodId === false) {
                    continue;
                }

                if ($q > $bestQ) {
                    $bestQ = $q;
                    $bestMethodId = $methodId;
                } elseif ($q === $bestQ && $methodId < $bestMethodId) {
                    $bestMethodId = $methodId;
                }
            }
        }
        if ($bestMethodId >= 0) {
            $method = $supportedMethods[$bestMethodId];
        }

        return $method;
    }

    private static $status = 'HTTP/1.1 200 OK';
    private static $status_code = 200;
    private static $headers = array();

    /**
     * @param string $line
     * @param bool $override
     * @param int $http_response_code
     */
    public static function setHeader($line, $override = true, $http_response_code = null)
    {
        if (strpos($line, ':') === false) {
            // status code
            self::$status = $line;
            self::$status_code = $http_response_code;
        } else {
            list($prop, $value) = explode(':', $line, 2);
            if ($override || !isset(self::$headers[$prop])) {
                self::$headers[$prop] = $line;
            } else {
                if (is_array(self::$headers[$prop])) {
                    self::$headers[$prop][] = $line;
                } else {
                    self::$headers[$prop] = array(self::$headers[$prop], $line);
                }
            }
        }
    }

    /**
     * @return string[]
     */
    public static function getHeaders()
    {
        return self::$headers;
    }

    public static function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }
        // @todo use DI container or plugins instead
        if (function_exists('Ressio_FuncOverride_sendHeaders')) {
            Ressio_FuncOverride_sendHeaders(self::$status_code, self::$status, self::$headers);
        } else {
            if (strcmp(PHP_VERSION, 'PHP 4.3.0') >= 0) {
                header(self::$status, true, self::$status_code);
            } else {
                header(self::$status);
            }
            foreach (self::$headers as $line) {
                if (!is_array($line)) {
                    header($line);
                } else {
                    foreach ($line as $header_line) {
                        header($header_line, false);
                    }
                }
            }
        }
    }
}
