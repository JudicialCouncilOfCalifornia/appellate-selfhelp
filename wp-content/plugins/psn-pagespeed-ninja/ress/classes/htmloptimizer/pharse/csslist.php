<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_HtmlOptimizer_Pharse_CSSList extends HTML_Node
{
    /** @var Ressio_DI */
    public $di;
    /** @var Ressio_Config */
    public $config;

    public $styleList = array();

    /**
     * Class constructor
     * @param Ressio_DI $di
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di)
    {
        parent::__construct('~stylesheet~', null);
        $this->di = $di;
        $this->config = $di->config;
    }

    /**
     * Returns the node as string
     * @param bool $attributes Print attributes (of child tags)
     * @param bool|int $recursive How many sublevels of childtags to print. True for all.
     * @param bool $content_only Only print text, false will print tags too.
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function toString($attributes = true, $recursive = true, $content_only = false)
    {
        return $this->di->cssCombiner->combineToHtml($this->styleList, $this->self_close_str);
    }
}
