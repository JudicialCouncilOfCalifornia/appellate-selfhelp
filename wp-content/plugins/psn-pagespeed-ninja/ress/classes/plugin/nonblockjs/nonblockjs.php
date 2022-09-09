<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_NonBlockJS extends Ressio_Plugin
{
    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params)
    {
        $params = $this->loadConfig(dirname(__FILE__) . '/config.json', $params);

        parent::__construct($di, $params);
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagSCRIPT($event, $optimizer, $node)
    {
        if ($node->hasAttribute('type') && $node->getAttribute('type') !== 'text/javascript') {
            return;
        }

        // @todo Is this check necessary???
        if ($node->hasAttribute('src')) {
            // check against exclusion regex
            $src = trim($node->getAttribute('src'));
            $regex = $this->config->js->excludemergeregex;
            if ($regex !== null && preg_match($regex, $src)) {
                return;
            }
        }

        $node->setAttribute('type', 'text/ress');
        if ($node->hasAttribute('src')) {
            $node->setAttribute('ress-src', $node->getAttribute('src'));
            $node->removeAttribute('src');
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     */
    public function onHtmlIterateAfter($event, $optimizer)
    {
        $scriptData = file_get_contents(RESSIO_PATH . '/classes/plugin/nonblockjs/js/nonblockjs.min.js');
        $optimizer->prependHead(array('script', null, $scriptData));

    }

    /**
     * @param $event Ressio_Event
     * @param $wrapper stdClass
     */
    public function onJsCombinerAfter($event, $wrapper)
    {
        $wrapper->content = preg_replace_callback('#<script(\s[^>]*|)>#i', array($this, 'regex_callback'), $wrapper->content);
    }

    /**
     * @param $matches array
     * @return string
     */
    public function regex_callback($matches)
    {
        $attributes = $matches[1];

        if (strpos($attributes, ' type="text/javascript"') !== false) {
            $attributes = str_replace(' type="text/javascript"', ' type="text/ress"', $attributes);
        } else {
            $attributes .= ' type="text/ress"';
        }
        $attributes = str_replace(' src="', ' ress-src="', $attributes);

        return '<script' . $attributes . '>';
    }
}