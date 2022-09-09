<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_Http2 extends Ressio_Plugin
{
    protected $style = array();
    protected $script = array();
    protected $image = array();
    protected $font = array();

    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        // @todo how to deal with caching plugins???
        // @todo get url()s from abovethefoldcss plugin <-- add event to that plugin, update it from lazyload as well
        // @todo collect links to fonts and images from css styles

        $params = $this->loadConfig(dirname(__FILE__) . '/config.json', $params);

        parent::__construct($di, $params);
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagLINKAfter($event, $optimizer, $node)
    {
        if ($optimizer->nodeIsDetached($node)) {
            return;
        }
        if ($node->hasAttribute('href')
            && $node->hasAttribute('rel') && $node->getAttribute('rel') === 'stylesheet'
            && (!$node->hasAttribute('type') || $node->getAttribute('type') === 'text/css')
        ) {
            $url = $node->getAttribute('href');
            $filename = $this->di->urlRewriter->urlToFilepath($url);
            if ($filename !== null && $this->di->filesystem->isFile($filename)) {
                $url = $this->di->urlRewriter->filepathToUrl($filename);
                $this->style[] = $url;
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagSCRIPTAfter($event, $optimizer, $node)
    {
        if ($optimizer->nodeIsDetached($node)) {
            return;
        }
        if ($node->hasAttribute('src')
            && (!$node->hasAttribute('type') || $node->getAttribute('type') === 'text/javascript')
        ) {
            $url = $node->getAttribute('src');
            $filename = $this->di->urlRewriter->urlToFilepath($url);
            if ($filename !== null && $this->di->filesystem->isFile($filename)) {
                $url = $this->di->urlRewriter->filepathToUrl($filename);
                $this->script[] = $url;
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
        if ($optimizer->nodeIsDetached($node)) {
            return;
        }
        if ($node->hasAttribute('src') && !$node->hasAttribute('srcset')) {
            $url = $node->getAttribute('src');
            $filename = $this->di->urlRewriter->urlToFilepath($url);
            if ($filename !== null && $this->di->filesystem->isFile($filename)) {
                $url = $this->di->urlRewriter->filepathToUrl($filename);
                $this->image[] = $url;
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $wrapper stdClass
     */
    public function onCssCombinerAfter($event, $wrapper)
    {
        $content = $wrapper->content;
        if (preg_match_all('/\shref=([\'"])(.*?)\1/', $content, $matches)) {
            foreach ($matches as $match) {
                $url = $match[2];
                $filename = $this->di->urlRewriter->urlToFilepath($url);
                if ($filename !== null && $this->di->filesystem->isFile($filename)) {
                    $url = $this->di->urlRewriter->filepathToUrl($filename);
                    $this->style[] = $url;
                }
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $wrapper stdClass
     */
    public function onJsCombinerAfter($event, $wrapper)
    {
        $content = $wrapper->content;
        if (preg_match_all('/\ssrc=([\'"])(.*?)\1/', $content, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $url = $match[2];
                $filename = $this->di->urlRewriter->urlToFilepath($url);
                if ($filename !== null && $this->di->filesystem->isFile($filename)) {
                    $url = $this->di->urlRewriter->filepathToUrl($filename);
                    $this->script[] = $url;
                }
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer Ressio_HtmlOptimizer_Base
     */
    public function onRunAfter($event, $optimizer)
    {
        foreach ($this->style as $url) {
            Ressio_Helper::setHeader('Link: <' . $url . '>; rel=preload; as=style', false);
        }
        foreach ($this->script as $url) {
            Ressio_Helper::setHeader('Link: <' . $url . '>; rel=preload; as=script', false);
        }
//        foreach ($this->font as $url) {
//            Ressio_Helper::setHeader('Link: <' . $url . '>; rel=preload; as=font', false);
//        }
//        foreach ($this->image as $url) {
//            Ressio_Helper::setHeader('Link: <' . $url . '>; rel=preload; as=image', false);
//        }
    }

}