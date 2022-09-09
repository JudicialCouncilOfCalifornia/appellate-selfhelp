<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

defined('RESSIO_PATH') or die('RESS: Restricted access');

require_once RESSIO_LIBS . '/jsmin-php/jsmin.php';

/**
 * JS minification using JSMin library
 */
class Ressio_JsMinify_Jsmin implements IRessio_JsMinify
{
    /**
     * Minify JS
     * @param string $str
     * @return string
     * @throws ERessio_InvalidJs
     */
    public function minify($str)
    {
        try {
            $str = JSMin::minify($str);
            $str = ltrim($str, "\n");
        } catch (Exception $e) {
            throw new ERessio_InvalidJs('Error in Ressio_JsMinify_Jsmin::minify: ' . $e->getMessage());
        }
        return $str;
    }

    /**
     * Minify JS in onevent=""
     * @param string $str
     * @return string
     * @throws ERessio_InvalidJs
     */
    public function minifyInline($str)
    {
        try {
            $str = JSMin::minify($str);
            $str = ltrim($str, "\n");
        } catch (Exception $e) {
            throw new ERessio_InvalidJs('Catched error in Ressio_JsMinify_Jsmin::minifyInline: ' . $e->getMessage());
        }
        return $str;
    }
}