<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_Googlefont extends Ressio_Plugin
{
    protected $fonts = array();

    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        $params = $this->loadConfig(dirname(__FILE__) . '/config.json', $params);

        parent::__construct($di, $params);
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagLINKBefore($event, $optimizer, $node)
    {
        if ($node->hasAttribute('rel') && $node->hasAttribute('href') && $node->getAttribute('rel') === 'stylesheet'
            && (!$node->hasAttribute('type') || $node->getAttribute('type') === 'text/css')
        ) {
            $url = $node->getAttribute('href');
            // @todo support &subset=...
            // @todo merge variants of the same font
            if (preg_match('#^(?:https?:)?//fonts\.googleapis\.com/css\?family=([^&]+)#', $url, $match)) {
                $fonts = explode('|', $match[1]);
                $this->fonts = array_merge($this->fonts, $fonts);
                $optimizer->nodeDetach($node);
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer Ressio_HtmlOptimizer_Base
     */
    public function onHtmlIterateAfter($event, $optimizer)
    {
        if (count($this->fonts) === 0) {
            return;
        }

        $url = '//fonts.googleapis.com/css?family=' . implode('%7C', $this->fonts);

        $isHtml5 = ($optimizer->doctype === IRessio_HtmlOptimizer::DOCTYPE_HTML5);

        switch ($this->params->method) {
            case 'async':
                $linkCode = '<link href="' . $url . '" rel="stylesheet"' . ($isHtml5 ? '' : ' type="text/css"') . '>';
                $scriptCode =
                    "WebFontConfig={google:{families:['" . implode("','", $this->fonts) . "']}};"
                    . '(function(d){'
                    . "var f=d.createElement('script'),s=d.getElementsByTagName('script')[0];"
                    . "f.src=('https:'==d.location.protocol?'https':'http')+'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';"
                    . "f.type='text/javascript';"
                    . 'f.async=true;'
                    . 's.parentNode.insertBefore(f,s);'
                    . '})(document);';
                // @todo load webfont.js in requestAnimationFrame or onload event

                if ($optimizer->insertBeforeStyleScript(array('noscript', null, $linkCode))) {
                    static $async = array('async' => true, 'defer' => true);
                    $optimizer->appendScriptDeclaration($scriptCode, $async);
                }
                break;

            case 'fout':
                $orig = '<link rel="stylesheet" href="' . $url . '"' . ($isHtml5 ? '' : ' type="text/css"/') . '>';

                $script = file_get_contents(RESSIO_PATH . '/classes/plugin/googlefont/js/loadfont.min.js');
                $script .= "ress_loadGooglefont('$url');";

                $optimizer->prependHead(
                    array('link', array('rel' => 'dns-prefetch', 'href' => '//fonts.googleapis.com'), false),
                    array('link', array('rel' => 'dns-prefetch', 'href' => '//fonts.gstatic.com'), false),
                    array('link', array('rel' => 'preconnect', 'href' => '//fonts.googleapis.com', 'crossorigin' => 'anonymous'), false),
                    array('link', array('rel' => 'preconnect', 'href' => '//fonts.gstatic.com', 'crossorigin' => 'anonymous'), false),
                    array('script', null, $script),
                    array('noscript', null, $orig)
                );

                break;

            case 'first':
            case 'foit':
            default:
                $attrs = array();
                $attrs['rel'] = 'stylesheet';
                $attrs['href'] = $url;
                if (!$isHtml5) {
                    $attrs['type'] = 'text/css';
                }

                $optimizer->prependHead(
                    array('link', array('rel' => 'dns-prefetch', 'href' => '//fonts.googleapis.com'), false),
                    array('link', array('rel' => 'dns-prefetch', 'href' => '//fonts.gstatic.com'), false),
                    array('link', array('rel' => 'preconnect', 'href' => '//fonts.googleapis.com', 'crossorigin' => 'anonymous'), false),
                    array('link', array('rel' => 'preconnect', 'href' => '//fonts.gstatic.com', 'crossorigin' => 'anonymous'), false),
                    array('link', $attrs, false)
                );
        }

    }

}