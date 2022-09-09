<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * CSS minification
 */
class Ressio_CssMinify_Ress implements IRessio_CssMinify
{
    /** @var Ressio_DI */
    protected $di;

    /** @var Ressio_Config */
    protected $config;

    /**
     * @param $di Ressio_DI
     * @throws ERessio_UnknownDiKey
     */
    public function setDI($di)
    {
        $this->di = $di;
        $this->config = $di->config;
    }

    /**
     * Minify CSS
     * @param string $str
     * @param string $srcBase
     * @param string $targetBase
     * @return string
     * @throws ERessio_UnknownDiKey
     * @throws ERessio_InvalidCss
     */
    public function minify($str, $srcBase = null, $targetBase = null)
    {
        return (string)$this->di->cssOptimizer->run($str, $srcBase, $targetBase);
    }

    /**
     * Minify CSS in style=""
     * @param string $str
     * @param string $srcBase
     * @return string
     * @throws ERessio_UnknownDiKey
     * @throws ERessio_InvalidCss
     */
    public function minifyInline($str, $srcBase = null)
    {
        $str = '*{' . $str . '}';

        $str = $this->minify($str, $srcBase);
        $str = trim($str, '* {}');

        return $str;
    }
}