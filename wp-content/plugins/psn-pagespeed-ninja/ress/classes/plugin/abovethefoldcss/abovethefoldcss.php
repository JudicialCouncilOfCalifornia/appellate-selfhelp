<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_AboveTheFoldCSS extends Ressio_Plugin
{
    /**
     * @var bool
     */
    protected $loadAboveTheFoldCSS = false;

    protected $relayout = false;

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
        if (empty($this->params->abovethefoldcss)) {
            // exit if no css is set
            return;
        }

        if (empty($this->params->cookie)) {
            $this->loadAboveTheFoldCSS = true;
        } elseif (!isset($_COOKIE[$this->params->cookie])) {
            $this->loadAboveTheFoldCSS = true;
            // PHP 5.2+ is required for httponly parameter
            setcookie($this->params->cookie, '1', time() + $this->params->cookietime, '/', $_SERVER['HTTP_HOST'], false, true);
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagLINK($event, $optimizer, $node)
    {
        if (!$this->loadAboveTheFoldCSS || $optimizer->nodeIsDetached($node)) {
            return;
        }

        if ($node->hasAttribute('rel') && $node->hasAttribute('href')
            && $node->getAttribute('rel') === 'stylesheet'
            && (!$node->hasAttribute('type') || $node->getAttribute('type') === 'text/css')
            && !$node->hasAttribute('onload') && !$node->hasAttribute('as')
        ) {
            $optimizer->nodeInsertAfter($node, 'noscript', null, $optimizer->nodeToString($node));
            $node->setAttribute('rel', 'preload');
            $node->setAttribute('as', 'style');
            $node->setAttribute('onload', "this.rel='stylesheet'");
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagSCRIPTBefore($event, $optimizer, $node)
    {
        if (!$this->loadAboveTheFoldCSS || $this->relayout) {
            return;
        }

        if ($node->hasAttribute('type') && $node->getAttribute('type') !== 'text/javascript') {
            return;
        }

        if ($node->hasAttribute('src')) {
            $src = $node->getAttribute('src');
            if (strpos($src, 'masonry') !== false) {
                $this->relayout = true;
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @throws ERessio_UnknownClassName
     */
    public function onHtmlIterateAfter($event, $optimizer)
    {
        if (!$this->loadAboveTheFoldCSS) {
            return;
        }

        $scriptData = file_get_contents(RESSIO_PATH . '/classes/plugin/abovethefoldcss/js/nonblockcss.min.js');
        $optimizer->prependHead(array('script', null, $scriptData));

        if ($this->relayout) {
            $scriptData = file_get_contents(RESSIO_PATH . '/classes/plugin/abovethefoldcss/js/relayout.min.js');
            $optimizer->appendScriptDeclaration($scriptData);
        }

        $styleData = $this->params->abovethefoldcss . 'img.lazy{display:none}';

        if ($optimizer instanceof Ressio_HtmlOptimizer_Pharse) {

            /** @var Ressio_HtmlOptimizer_Pharse $optimizer */
            /** @var HTML_Node $node */
            $node = $optimizer->dom;
            $parentStack = array();
            $parentPos = array();
            $level = 0;

            while ($node !== null) {
                $isLink = (
                    $node->tag === 'link')
                    && isset($node->attributes['rel'])
                    && ($node->attributes['rel'] === 'stylesheet'
                        || ($node->attributes['rel'] === 'preload' && isset($node->attributes['as']) && $node->attributes['as'] === 'style')
                    );
                $isStyle = ($node->tag === 'style');

                if ($isLink || $isStyle || $node instanceof $optimizer->classNodeCssList || $node instanceof $optimizer->classNodeJsList) {
                    $parent = $node->parent;
                    $index = $parent->findChild($node);

                    $nodeStyle = $parent->addChild('style', $index);
                    if ($optimizer->doctype !== Ressio_HtmlOptimizer_Pharse::DOCTYPE_HTML5) {
                        $nodeStyle->attributes['type'] = 'text/css';
                    }
                    $nodeStyle->addText($styleData);
                    break;
                }

                if (count($node->children)) {
                    $level++;
                    $parentStack[$level] = $node;
                    $parentPos[$level] = 0;
                    $node = $node->children[0];
                } else {
                    while ($level > 0) {
                        $parentPos[$level]++;
                        $pos = $parentPos[$level];
                        $parent = $parentStack[$level];
                        if ($pos < count($parent->children)) {
                            $node = $parent->children[$pos];
                            break;
                        }
                        $level--;
                    }
                    if ($level === 0) {
                        break;
                    }
                }
            }

        } elseif ($optimizer instanceof Ressio_HtmlOptimizer_Dom) {

            /** @var Ressio_HtmlOptimizer_Dom $optimizer */
            /** @var Ressio_HtmlOptimizer_Dom_Element $node */
            $node = $optimizer->dom;
            $parentStack = array();
            $parentPos = array();
            $level = 0;

            while ($node !== null) {
                $isLink = (
                    $node->nodeName === 'link')
                    && $node->hasAttribute('rel')
                    && ($node->getAttribute('rel') === 'stylesheet'
                        || ($node->getAttribute('rel') === 'preload' && $node->hasAttribute('as') && $node->getAttribute('as') === 'style')
                    );
                $isStyle = ($node->nodeName === 'style' || $node->nodeName === 'resscss');

                if ($isLink || $isStyle || $node->nodeName === 'ressscript') {
                    $parent = $node->parentNode;

                    /** @var Ressio_HtmlOptimizer_Dom_Element $nodeStyle */
                    $nodeStyle = $optimizer->dom->createElement('style');
                    $nodeStyle = $parent->insertBefore($nodeStyle, $node);
                    if ($optimizer->doctype !== Ressio_HtmlOptimizer_Pharse::DOCTYPE_HTML5) {
                        $nodeStyle->setAttribute('type', 'text/css');
                    }
                    $nodeStyle->textContent = $styleData;
                    break;
                }

                if ($node->hasChildNodes()) {
                    $level++;
                    $parentStack[$level] = $node;
                    $parentPos[$level] = 0;
                    $node = $node->childNodes->item(0);
                } else {
                    while ($level > 0) {
                        $parentPos[$level]++;
                        $pos = $parentPos[$level];
                        $parent = $parentStack[$level];
                        $node = $parent->childNodes->item($pos);
                        if ($node !== null) {
                            break;
                        }
                        $level--;
                    }
                    if ($level === 0) {
                        break;
                    }
                }
            }

        } elseif ($optimizer instanceof Ressio_HtmlOptimizer_Stream) {

            /** @var Ressio_HtmlOptimizer_Stream $optimizer */
            $i = 0;
            array_splice($optimizer->dom, 0, 0);
            foreach ($optimizer->dom as $value) {
                $isLink = false;
                $isStyle = false;
                if (is_string($value)) {
                    if (preg_match('#^<link\s#i', $value)) {
                        $startPos = strpos($value, ' ');
                        $endPos = strpos($value, '>');
                        $attributes = $optimizer->parseAttributes(substr($value, $startPos, $endPos - $startPos));
                        $isLink =
                            isset($attributes['rel'])
                            && ($attributes['rel'] === 'stylesheet'
                                || ($attributes['rel'] === 'preload' && isset($attributes['as']) && $attributes['as'] === 'style')
                            );
                    }
                    $isStyle = preg_match('#^<style[\s>]#i', $value);
                }

                if ($isLink || $isStyle || $value instanceof $optimizer->classNodeCssList || $value instanceof $optimizer->classNodeJsList) {
                    /** @var $value Ressio_HtmlOptimizer_Stream_CSSList */
                    $inject =
                        '<style' . ($optimizer->doctype !== Ressio_HtmlOptimizer_Stream::DOCTYPE_HTML5 ? ' type="text/css"' : '') . '>' .
                        $styleData .
                        '</style>';
                    array_splice($optimizer->dom, $i, 0, $inject);
                    break;
                }

                $i++;
            }

        } else {
            throw new ERessio_UnknownClassName('Unknown Html Optimizer class: ' . get_class($optimizer));
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $wrapper stdClass
     */
    public function onCssCombinerAfter($event, $wrapper)
    {
        if ($this->loadAboveTheFoldCSS) {
            $wrapper->content = preg_replace_callback('#<link(\s[^>]*|)>#i', array($this, 'regex_callback'), $wrapper->content);
        }
    }

    /**
     * @param $matches array
     * @return string
     */
    public function regex_callback($matches)
    {
        list($content, $attributes) = $matches;

        $attributes = str_replace(
            ' rel="stylesheet" ',
            ' rel="preload" as="style" onload="this.rel=\'stylesheet\'" ',
            $attributes
        );

        return '<link' . $attributes . '><noscript>' . $content . '</noscript>';
    }
}