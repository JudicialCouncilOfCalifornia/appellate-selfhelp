<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_HtmlOptimizer_Stream_CSSList
{
    /** @var Ressio_DI */
    public $di;
    /** @var Ressio_Config */
    public $config;
    /** @var string */
    public $self_close_str;

    public $styleList = array();

    /**
     * Class constructor
     * @param Ressio_DI $di
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di)
    {
        $this->di = $di;
        $this->config = $di->config;
        $this->self_close_str = '';
    }

    /**
     * Returns the node as string
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function toString()
    {
        if (!count($this->styleList)) {
            return '';
        }

        return $this->di->cssCombiner->combineToHtml($this->styleList, $this->self_close_str);
    }
}
