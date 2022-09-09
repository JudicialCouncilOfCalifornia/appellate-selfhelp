<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_HtmlOptimizer
{
    const DOCTYPE_HTML4 = 1;
    const DOCTYPE_HTML5 = 2;
    const DOCTYPE_XHTML = 3;

    /**
     * @param $buffer string
     * @return string
     */
    public function run($buffer);

    /**
     * @param $file string
     * @param $attribs array|null
     */
    public function appendScript($file, $attribs = null);

    /**
     * @param $content string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendScriptDeclaration($content, $attribs = null, $head = null);

    /**
     * @param $file string
     * @param $attribs array|null
     */
    public function appendStylesheet($file, $attribs = null);

    /**
     * @param $content string
     * @param $attribs array|null
     */
    public function appendStyleDeclaration($content, $attribs = null);

    /**
     * @param $node IRessio_HtmlNode
     * @return string
     */
    public function nodeToString($node);

    /**
     * @param $node IRessio_HtmlNode
     */
    public function nodeDetach(&$node);

    /**
     * @param $node IRessio_HtmlNode
     * @return bool
     */
    public function nodeIsDetached($node);

    /**
     * @param $node IRessio_HtmlNode
     * @param $text string
     */
    public function nodeSetInnerText(&$node, $text);

    /**
     * @param $node IRessio_HtmlNode
     * @return string
     */
    public function nodeGetInnerText(&$node);

    /**
     * @param $node IRessio_HtmlNode
     * @param $tag string
     * @param $attribs array
     */
    public function nodeWrap(&$node, $tag, $attribs = null);

    /**
     * @param $node IRessio_HtmlNode
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertBefore(&$node, $tag, $attribs = null, $content = null);

    /**
     * @param $node IRessio_HtmlNode
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertAfter(&$node, $tag, $attribs = null, $content = null);

    /**
     * @param $nodedata,... array (string $tag, array $attribs, string $content)
     * @return bool return false if no <head> found
     */
    public function prependHead($nodedata);

    /**
     * @param $nodedata array (string $tag, array $attribs, string $content)
     * @return bool return false if no <link rel=stylesheet>, <style>, <script>, or combining wrappers
     */
    public function insertBeforeStyleScript($nodedata);

    /**
     * @return bool
     */
    public function isNoscriptState();
}
