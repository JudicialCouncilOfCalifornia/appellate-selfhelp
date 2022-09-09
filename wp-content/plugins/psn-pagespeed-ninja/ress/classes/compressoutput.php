<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_CompressOutput
{
    /** @var int */
    private static $gzLevel = 0;

    /** @var string|bool */
    private static $encoding = '';

    /**
     * @static
     * @param int $gzLevel Compression level
     * @param bool $autostart Set Ressio_CompressOutput::compress as output handler
     */
    public static function init($gzLevel, $autostart = true)
    {
        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            $gzLevel = 0;
        } else {
            self::$encoding = Ressio_Helper::getRequestedCompression();
            if (self::$encoding === false) {
                $gzLevel = 0;
            }
        }

        self::$gzLevel = $gzLevel;

        if ($autostart && $gzLevel) {
            ob_start('Ressio_CompressOutput::compress', 0, false);
        }
    }

    /**
     * Content compressing by requesting method
     * @static
     * @param string $content
     * @return string
     */
    public static function compress($content)
    {
        if ($content === '' || headers_sent()) {
            return $content;
        }

        $encoding = self::$encoding;
        /** @var string|false $encoded */
        $encoded = false;
        switch ($encoding) {
            case 'deflate':
                $encoded = gzdeflate($content, self::$gzLevel);
                break;
            case 'gzip':
            case 'x-gzip':
                $encoded = gzencode($content, self::$gzLevel);
                break;
            case 'compress':
            case 'x-compress':
                $encoded = gzcompress($content, self::$gzLevel);
                break;
        }
        if ($encoded === false) {
            return $content;
        }

        Ressio_Helper::setHeader('Vary: Accept-Encoding');
        Ressio_Helper::setHeader('Content-Encoding: ' . self::$encoding);
        Ressio_Helper::setHeader('Content-Length: ' . strlen($encoded));
        return $encoded;
    }
}
