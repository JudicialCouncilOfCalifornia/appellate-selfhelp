<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * CSS minification interface
 */
interface IRessio_CssMinify
{
    /**
     * Minify CSS
     * @param string $str
     * @param string $srcBase
     * @param string $targetBase
     * @return string
     * @throws ERessio_InvalidCss
     */
    public function minify($str, $srcBase = null, $targetBase = null);

    /**
     * Minify CSS in style=""
     * @param string $str
     * @param string $srcBase
     * @return string
     * @throws ERessio_InvalidCss
     */
    public function minifyInline($str, $srcBase = null);
}