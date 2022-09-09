<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Abstract JS minification class
 */
interface IRessio_JsMinify
{
    /**
     * Minify JS
     * @param string $str
     * @return string
     * @throws ERessio_InvalidJs
     */
    public function minify($str);

    /**
     * Minify JS in style=""
     * @param string $str
     * @return string
     * @throws ERessio_InvalidJs
     */
    public function minifyInline($str);
}
