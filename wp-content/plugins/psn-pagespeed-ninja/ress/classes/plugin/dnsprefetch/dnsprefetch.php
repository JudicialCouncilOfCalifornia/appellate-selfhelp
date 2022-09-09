<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_DNSPrefetch extends Ressio_Plugin
{
    /**
     * @var array
     */
    public $domains_list;
    /**
     * @var string
     */
    public $current_domain;

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
     */
    public function onHtmlIterateBefore($event, $optimizer)
    {
        $this->current_domain = $_SERVER['HTTP_HOST'];
        $this->domains_list = array();
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     */
    public function onHtmlIterateAfter($event, $optimizer)
    {
        if (!count($this->domains_list)) {
            return;
        }

        $tags = array();
        foreach (array_keys($this->domains_list) as $domain) {
            $tags[] = array('link', array('rel' => 'dns-prefetch', 'href' => '//' . $domain), false);
        }

        call_user_func_array(array($optimizer, 'prependHead'), $tags);
    }

    public function addDomainFromURL($url)
    {
        if (strpos($url, '//') === false) {
            return;
        }
        $parsed = parse_url($url);
        if (isset($parsed['host'])) {
            $domain = $parsed['host'];
            if ($domain !== $this->current_domain && !isset($this->domains_list[$domain])) {
                $this->domains_list[$domain] = 1;
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagIMGAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->addDomainFromURL($node->getAttribute('src'));
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagSCRIPTAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->addDomainFromURL($node->getAttribute('src'));
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagLINKAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('href') && $node->hasAttribute('rel')) {
            switch ($node->getAttribute('rel')) {
                case 'stylesheet':
                    $this->addDomainFromURL($node->getAttribute('href'));
                    break;
                case 'dns-prefetch':
                    $this->addDomainFromURL($node->getAttribute('href'));
                    $optimizer->nodeDetach($node);
                    break;
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagIFRAMEAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->addDomainFromURL($node->getAttribute('src'));
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagAUDIOAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->addDomainFromURL($node->getAttribute('src'));
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagVIDEOAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->addDomainFromURL($node->getAttribute('src'));
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagSOURCEAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->addDomainFromURL($node->getAttribute('src'));
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagTRACKAfter($event, $optimizer, $node)
    {
        if ($node->hasAttribute('src')) {
            $this->addDomainFromURL($node->getAttribute('src'));
        }
    }

}