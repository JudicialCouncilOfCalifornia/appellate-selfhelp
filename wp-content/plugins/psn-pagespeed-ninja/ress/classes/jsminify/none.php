<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * No JS minification
 */
class Ressio_JsMinify_None implements IRessio_JsMinify
{
    /**
     * Minify JS
     * @param string $str
     * @return string
     */
    public function minify($str)
    {
        return $str;
    }

    /**
     * Minify JS in onevent=""
     * @param string $str
     * @return string
     */
    public function minifyInline($str)
    {
        return $str;
    }
}