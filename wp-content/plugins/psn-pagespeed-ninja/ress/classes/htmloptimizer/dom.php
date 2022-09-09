<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_HtmlOptimizer_Dom extends Ressio_HtmlOptimizer_Base
{
    /** @var string */
    public $origDoctype;

    /** @var Ressio_HtmlOptimizer_Dom_Document */
    public $dom;

    private $baseFound = false;

    /** @var Ressio_HtmlOptimizer_Dom_Element|null */
    private $lastJsNode;
    /** @var Ressio_HtmlOptimizer_Dom_Element|null */
    private $lastAsyncJsNode;
    /** @var Ressio_HtmlOptimizer_Dom_Element|null */
    private $lastCssNode;

    /** @var int */
    public $noscriptCounter = 0;

    /** @var bool */
    public $headMode;

    // TODO Is it safe to remove (with references from insertBeforeStyleScript)?
    public $classNodeCssList = 'Ressio_HtmlOptimizer_Dom_Element';
    public $classNodeJsList = 'Ressio_HtmlOptimizer_Dom_Element';

    private $jscssLists = array();
    private $jscssIndex = -1;

    /**
     * @param $buffer string
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function run($buffer)
    {
        //@todo Implement caching (for static html pages) -> move to Ressio::run
        //@todo (necessary to split parsing and optimization to support browser-specific optimization)

        // parse html
        $dom = new Ressio_HtmlOptimizer_Dom_Document();
        $dom->loadHTML($buffer);

        $this->dom = $dom;

        $this->lastJsNode = null;
        $this->lastAsyncJsNode = null;
        $this->lastCssNode = null;

        $this->headMode = true;

        $this->dispatcher->triggerEvent('HtmlIterateBefore', array($this));

        $this->domIterate($dom, $this->config->html->mergespace);

        if ($this->origDoctype === null && $this->config->html->forcehtml5) {
            $dom->addDoctype('html');
        }

        $this->dispatcher->triggerEvent('HtmlIterateAfter', array($this));

        // @todo process RESS'ed style/script tags
        $nodes = $dom->getElementsByTagName('ressscript');
        if ($nodes->length) {
            $combiner = $this->di->jsCombiner;
            $tmpDoc = new DOMDocument();
            $removeList = array();
            foreach ($nodes as $node) {
                $index = (int)$node->getAttribute('index');
                $html = $combiner->combineToHtml($this->jscssLists[$index]);
                $tmpDoc->loadHTML('<div>' . $html . '</div>');
                /** @var Ressio_HtmlOptimizer_Dom_Element $parent */
                $parent = $node->parentNode;
                foreach ($tmpDoc->getElementsByTagName('div')->item(0)->childNodes as $child) {
                    $parent->insertBefore($dom->importNode($child, true), $node);
                }
                $removeList[] = $node;
            }
            foreach ($removeList as $node) {
                $node->parentNode->removeChild($node);
            }
        }
        $nodes = $dom->getElementsByTagName('resscss');
        if ($nodes->length) {
            $combiner = $this->di->cssCombiner;
            $tmpDoc = new DOMDocument();
            $removeList = array();
            foreach ($nodes as $node) {
                $index = (int)$node->getAttribute('index');
                $html = $combiner->combineToHtml($this->jscssLists[$index]);
                $tmpDoc->loadHTML('<div>' . $html . '</div>');
                /** @var Ressio_HtmlOptimizer_Dom_Element $parent */
                $parent = $node->parentNode;
                foreach ($tmpDoc->getElementsByTagName('div')->item(0)->childNodes as $child) {
                    $parent->insertBefore($dom->importNode($child, true), $node);
                }
                $removeList[] = $node;
            }
            foreach ($removeList as $node) {
                $node->parentNode->removeChild($node);
            }
        }

        $buffer = $dom->saveHTML();

        $this->dom = null;
        $this->lastJsNode = null;
        $this->lastAsyncJsNode = null;
        $this->lastCssNode = null;
        $this->jscssLists = array();
        $this->jscssIndex = -1;

        return $buffer;
    }

    /**
     * @param $file string
     * @param $attribs array|null
     * @param $head bool|Ressio_HtmlOptimizer_Dom_Element
     */
    public function appendScript($file, $attribs = null, $head = true)
    {
        /** @var Ressio_HtmlOptimizer_Dom_Element $jsNode */
        if ($this->lastAsyncJsNode !== null) {
            $jsNode = $this->dom->addChild('script');
        } else {
            if (!($head instanceof Ressio_HtmlOptimizer_Dom_Element)) {
                $injects = $this->dom->getElementsByTagName($head ? 'head' : 'body');
                $head = $injects->item(0);
                if ($head === null) {
                    $head = $this->dom;
                }
            }
            $jsNode = $this->dom->createElement('script');
            $head->addChild($jsNode);
        }

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $jsNode->setAttribute('type', 'text/javascript');
        }
        $jsNode->setAttribute('src', $file);
        if (is_array($attribs)) {
            /** @var $attribs array */
            foreach ($attribs as $name => $value) {
                $jsNode->setAttribute($name, $value);
            }
        }
        $this->addJS($jsNode, true);
    }

    /**
     * @param $content string
     * @param $attribs array|null
     * @param $head bool|Ressio_HtmlOptimizer_Dom_Element
     */
    public function appendScriptDeclaration($content, $attribs = null, $head = true)
    {
        /** @var Ressio_HtmlOptimizer_Dom_Element $jsNode */
        if ($this->lastAsyncJsNode !== null) {
            $jsNode = $this->dom->addChild('script');
        } else {
            if (!($head instanceof Ressio_HtmlOptimizer_Dom_Element)) {
                $injects = $this->dom->getElementsByTagName($head ? 'head' : 'body');
                /** @var Ressio_HtmlOptimizer_Dom_Element $head */
                $head = $injects->item(0);
                if ($head === null) {
                    $head = $this->dom;
                }
            }
            $jsNode = $this->dom->createElement('script');
            $head->addChild($jsNode);
        }

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $jsNode->setAttribute('type', 'text/javascript');
        }
        if (is_array($attribs)) {
            /** @var $attribs array */
            foreach ($attribs as $name => $value) {
                $jsNode->setAttribute($name, $value);
            }
        }
        $jsNode->textContent = $content;
        $this->addJs($jsNode, true, true);
    }

    /**
     * @param $file string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendStylesheet($file, $attribs = null, $head = true)
    {
        /** @var Ressio_HtmlOptimizer_Dom_Element $cssNode */
        if ($this->lastCssNode !== null) {
            $cssNode = $this->dom->addChild('link');
        } else {
            $injects = $this->dom->getElementsByTagName($head ? 'head' : 'body');
            $head = $injects->item(0);
            if ($head === null) {
                $head = $this->dom;
            }
            $cssNode = $this->dom->createElement('link');
            $head->addChild($cssNode);
        }

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $cssNode->setAttribute('type', 'text/css');
        }
        $cssNode->setAttribute('rel', 'stylesheet');
        $cssNode->setAttribute('href', $file);
        if (is_array($attribs)) {
            /** @var $attribs array */
            foreach ($attribs as $name => $value) {
                $cssNode->setAttribute($name, $value);
            }
        }
        $this->addCss($cssNode);
    }

    /**
     * @param $content string
     * @param $attribs array|null
     * @param $head bool|Ressio_HtmlOptimizer_Dom_Element
     */
    public function appendStyleDeclaration($content, $attribs = null, $head = true)
    {
        /** @var Ressio_HtmlOptimizer_Dom_Element $cssNode */
        if ($this->lastCssNode !== null) {
            $cssNode = $this->dom->addChild('style');
        } else {
            if (!($head instanceof Ressio_HtmlOptimizer_Dom_Element)) {
                $injects = $this->dom->getElementsByTagName($head ? 'head' : 'body');
                /** @var Ressio_HtmlOptimizer_Dom_Element $head */
                $head = $injects->item(0);
                if ($head === null) {
                    $head = $this->dom;
                }
            }
            $cssNode = $this->dom->createElement('style');
            $head->addChild($cssNode);
        }

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $cssNode->setAttribute('type', 'text/css');
        }
        if (is_array($attribs)) {
            /** @var $attribs array */
            foreach ($attribs as $name => $value) {
                $cssNode->setAttribute($name, $value);
            }
        }
        $cssNode->textContent = $content;
        $this->addCss($cssNode, true);
    }

    /**
     * @param Ressio_HtmlOptimizer_Dom_Element|Ressio_HtmlOptimizer_Dom_Document $node
     * @param bool $mergeSpace
     * @return bool
     * @throws ERessio_UnknownDiKey
     */
    protected function domProcess(&$node, $mergeSpace)
    {
        // @todo skip xml and asp tags

        // doctype
        if ($node instanceof DOMDocumentType) {
            /** @var DOMDocumentType $node */
            $this->origDoctype = $node->name . ($node->publicId ? ' ' . $node->publicId : '') . ($node->systemId ? ' ' . $node->systemId : '');
            if ($this->config->html->forcehtml5) {
                if ($this->origDoctype !== 'html') {
                    $this->dom->addDoctype('html');
                }
            } elseif (strpos($node->name, 'DTD HTML') !== false) {
                $this->doctype = self::DOCTYPE_HTML4;
            } elseif (strpos($node->name, 'DTD XHTML') !== false) {
                $this->doctype = self::DOCTYPE_XHTML;
            } else {
                $this->doctype = self::DOCTYPE_HTML5;
            }
            return false;
        }

        $isCDATASection = $node instanceof Ressio_HtmlOptimizer_Dom_CdataSection;
        // CDATA is text in xhtml and comment in html
        if (($node instanceof Ressio_HtmlOptimizer_Dom_Text && !$isCDATASection) ||
            ($this->doctype === self::DOCTYPE_XHTML && $isCDATASection)
        ) {
            /** @var Ressio_HtmlOptimizer_Dom_Text $node */
            if ($mergeSpace) {
                $node->textContent = preg_replace('/\s{2,}|[\n\r\t\f]/S', ' ', $node->textContent);
                if ($node->textContent === ' ' && isset($this->tags_nospaces[$node->parentNode->nodeName])) {
                    $this->nodeDetach($node);
                }
            }
            return false;
        }

        // remove comments
        if ($node instanceof Ressio_HtmlOptimizer_Dom_Comment ||
            ($this->doctype !== self::DOCTYPE_XHTML && $isCDATASection)
        ) {
            /** @var Ressio_HtmlOptimizer_Dom_Comment $node */
            if ($this->config->html->removecomments) {
                if ($node->textContent === '' || ($node->textContent[0] !== '!' &&
                        strncmp($node->textContent, '[if ', 4) !== 0 &&
                        strncmp($node->textContent, '[endif]', 7) !== 0 && strncmp($node->textContent, '<![endif]', 9) !== 0 &&
                        strncmp($node->textContent, '!RESS![if ', 10) !== 0 && strncmp($node->textContent, '!RESS![endif]', 13) !== 0)
                ) {
                    $this->nodeDetach($node);
                } else {
                    // check comments (keep IE ones on IE, [if, <![ : <!--[if IE]>, <!--<![endif]--> )
                    // stop css/style combining in IE cond block
                    // @todo don't remove non-comment node <!--[if !IE]>-->HTML<!--<![endif]--> and <![if expression]>HTML<![endif]>
                    if ($this->config->html->removeiecond) {
                        $vendor = $this->di->deviceDetector->vendor();
                        if ($vendor !== 'ms' && $vendor !== 'unknown') { // if not IE browser
                            $this->nodeDetach($node);
                            return false;
                        }
                    }
                    // @todo: parse as html and compress internals
                    $this->breakCss();
                    $this->breakJs();
                    if ($mergeSpace) {
                        // @todo rewrite
                        $inner = $node->textContent;
                        $inner = preg_replace('#\s+<!--$#', '<!--', ltrim($inner));
                        $node->textContent = $inner;
                    }
                }
            }
            return false;
        }

        // disable optimizing of nodes with ress-safe attribute
        if ($node->hasAttribute('ress-safe')) {
            $node->removeAttribute('ress-safe');
            return false;
        }

        // @todo: remove first and last spaces in block elements
        // @todo: remove space after open/close tag if there is space before the tag

        /** @var Ressio_HtmlOptimizer_Dom_Element $node */

        // check and parse ress-media attribute
        if ($node->hasAttribute('ress-media')) {
            if (!$this->matchRessMedia($node->getAttribute('ress-media'))) {
                $this->nodeDetach($node);
                return false;
            }
            $node->removeAttribute('ress-media');
        }

        $iterateChildren = !isset($this->tags_selfclose[$node->nodeName]);

        $tagName = strtoupper($node->nodeName);
        $this->dispatcher->triggerEvent('HtmlIterateTag' . $tagName . 'Before', array($this, $node));
        if ($node->parentNode === null) {
            return false;
        }

        switch ($node->nodeName) {
            case 'a':
            case 'area':
                if ($node->hasAttribute('href')) {
                    $uri = $node->getAttribute('href');
                    if ($this->config->js->minifyattribute && strncmp($uri, 'javascript:', 11) === 0) {
                        $node->setAttribute('href', 'javascript:' . $this->jsMinifyInline(substr($uri, 11)));
                    }
                }
                break;

            case 'base':
                // save base href (use first tag only)
                if (!$this->baseFound && $node->hasAttribute('href')) {
                    $base = $node->getAttribute('href');
                    if (substr($base, -1) !== '/') {
                        $base = dirname($base);
                        if ($base === '.') {
                            $base = '';
                        }
                        $base .= '/';
                    }
                    $this->urlRewriter->setBase($base);
                    $node->setAttribute('href', $this->urlRewriter->getBase());
                    $this->baseFound = true;
                }
                break;

            case 'body':
                $this->headMode = false;
                // set css break point to preserve css files order after dynamically adding styles to head using js
                if (!$this->config->css->mergeheadbody) {
                    $this->breakCss();
                }
                if (!$this->config->js->mergeheadbody) {
                    $this->breakJs(true);
                }
                break;

            case 'img':
                // @todo Auto set alt="" if not exists
                if ($this->noscriptCounter) {
                    break;
                }

                if ($this->config->img->minify && $node->hasAttribute('src')) {
                    $src = $node->getAttribute('src');
                    if ($src !== '' && strncmp($src, 'data:', 5) !== 0) {
                        $src_file = $this->urlRewriter->urlToFilepath($src);
                        if ($src_file !== null) {
                            $this->di->imgOptimizer->run($src_file);
                        }
                    }
                }
                if (($this->config->img->minify || $this->config->html->urlminify) && $node->hasAttribute('srcset')) {
                    $srcset = $node->getAttribute('srcset');
                    $srclist = explode(',', $srcset);
                    foreach ($srclist as &$srcitem) {
                        list($src, $params) = preg_split('/\s+/', trim($srcitem), 2);
                        if (strncmp($src, 'data:', 5) !== 0) {
                            if ($this->config->img->minify) {
                                $src_file = $this->urlRewriter->urlToFilepath($src);
                                if ($src_file !== null) {
                                    $this->di->imgOptimizer->run($src_file);
                                }
                            }
                            if ($this->config->html->urlminify) {
                                $src = $this->urlRewriter->minify($src);
                                $srcitem = "$src $params";
                            }
                        }
                    }
                    unset($srcitem);
                    $node->setAttribute('srcset', implode(',', $srclist));
                }

                break;

            case 'picture':
                // parse <picture> elements
                break;

            case 'script':
                $iterateChildren = false; // don't change script sources
                if ($this->noscriptCounter) {
                    $this->nodeDetach($node);
                    break;
                }

                if ($node->hasAttribute('ress-noasync')) {
                    $node->removeAttribute('ress-noasync');
                    $autoasync = false;
                } else {
                    $autoasync = $this->config->js->autoasync;
                }

                if (
                    $node->hasAttribute('onload') ||
                    ($node->hasAttribute('data-cfasync') && $node->getAttribute('data-cfasync') === 'false')
                ) {
                    $this->breakJs(true);
                    break;
                }

                if ($this->config->js->forceasync) {
                    $node->setAttribute('async', 'async');
                }
                if ($this->config->js->forcedefer) {
                    $node->setAttribute('defer', 'defer');
                }

                // break if there attributes other than type=text/javascript, defer, async
                if ($node->attributes->item(0) !== null) {
                    $attributes = array();
                    foreach ($node->attributes as $name => $anode) {
                        $attributes[$name] = $anode->nodeValue;
                    }
                    if ($this->config->js->checkattributes) {
                        // @todo support language="javascript" attribute
                        if (isset($attributes['type']) && $attributes['type'] === 'text/javascript') {
                            unset($attributes['type']);
                            if ($this->config->html->removedefattr) {
                                $node->removeAttribute('type');
                            }
                        }
                        if (isset($attributes['language']) && strcasecmp($attributes['language'], 'javascript') === 0) {
                            unset($attributes['language']);
                            if ($this->config->html->removedefattr) {
                                $node->removeAttribute('language');
                            }
                        }
                        unset($attributes['defer'], $attributes['async'], $attributes['src'],
                            $attributes['ress-merge'], $attributes['ress-nomerge']);
                        if (count($attributes) > 0) {
                            $this->breakJs(true);
                            break;
                        }
                    } else {
                        if (isset($attributes['type']) && $attributes['type'] !== 'text/javascript') {
                            $this->breakJs(true);
                            break;
                        }
                    }
                }

                // set type=text/javascript in html4 and remove in html5
                if ($this->doctype !== self::DOCTYPE_HTML5 && !$node->hasAttribute('type')) {
                    $node->setAttribute('type', 'text/javascript');
                }

                if (!$node->hasAttribute('src')) { // inline
                    $scriptBlob = $node->textContent;

                    if (empty($scriptBlob)) {
                        if ($this->config->js->merge) {
                            $this->nodeDetach($node);
                        }
                        return false;
                    }

                    // @todo: refactor clear comments
                    $scriptBlob = preg_replace(array('#^\s*<!--.*?[\r\n]+#', '#//\s*<!--.*$#m', '#//\s*-->.*$#m', '#\s*-->\s*$#'), '', $scriptBlob);
                    $scriptBlob = preg_replace('#^\s*(?://\s*)?<!\[CDATA\[\s*(.*?)\s*(?://\s*)?\]\]>\s*$#', '\1', $scriptBlob);
                    $scriptBlob = preg_replace('#^\s*/\*\s*<!\[CDATA\[\s*\*/\s*(.*?)\s*/\*\s*\]\]>\s*\*/\s*$#', '\1', $scriptBlob);

                    $node->textContent = $scriptBlob;

                    // @todo ";" may be in string values: var x={"a":";"};
                    // @todo another pattern: array initialization and a sequence of push
                    if (
                        $this->config->js->skipinits
                        && strlen($scriptBlob) < 512
                        && preg_match('#^var\s+\w+\s*=\s*(?:\{[^;]+?\}|\'[^\']+?\'|"[^"]+?"|\d+);?\s*$#', $scriptBlob)
                    ) {
                        // skip (probable page-dependent) js variables initialization from merging
                        $this->breakJs();
                        break;
                    }

                    $autoasync = ($autoasync && (strpos($scriptBlob, '.write') === false || !preg_match('#\.write(?!\(\))#', $scriptBlob)));

                    if ($node->hasAttribute('ress-nomerge')) {
                        $node->removeAttribute('ress-nomerge');
                        $merge = false;
                    } elseif ($node->hasAttribute('ress-merge')) {
                        $node->removeAttribute('ress-merge');
                        $merge = true;
                    } else {
                        $merge =
                            is_bool($this->config->js->mergeinline)
                                ? $this->config->js->mergeinline
                                : $this->headMode;
                        if ($merge && $node->hasAttribute('id')) {
                            $id = $node->getAttribute('id');
                            if (preg_match('/([\'"])#?' . preg_quote($id, '/') . '\1/', $scriptBlob)) {
                                $merge = false;
                            }
                        }
                    }

                    if ($merge) {
                        $this->addJs($node, false, true, $autoasync);
                    } else {
                        $this->breakJs(true);
                    }
                } else { // external
                    if ($node->hasAttribute('ress-nomerge')) {
                        $node->removeAttribute('ress-nomerge');
                        $merge = false;
                    } elseif ($node->hasAttribute('ress-merge')) {
                        $node->removeAttribute('ress-merge');
                        $merge = true;
                    } else {
                        $merge = $this->config->js->merge;
                    }

                    if ($merge) {
                        $src = $node->getAttribute('src');
                        if (strpos($src, '#') !== false) {
                            $merge = false;
                        } else {
                            $regex = $this->config->js->excludemergeregex;
                            if ($regex !== null && preg_match($regex, $src)) {
                                $merge = false;
                            } else {
                                $srcFile = $this->urlRewriter->urlToFilepath($src);
                                $merge = ($srcFile !== null) && (pathinfo($srcFile, PATHINFO_EXTENSION) === 'js') && $this->di->filesystem->isFile($srcFile);
                            }
                        }
                    }

                    if ($merge) {
                        $this->addJs($node, false, false, $autoasync);
                    } else {
                        $this->breakJs($this->config->js->autoasync);
                    }
                }
                break;

            case 'link':
                // break if there attributes other than type=text/css, rel=stylesheet, href
                if (!$node->hasAttribute('href') || $node->getAttribute('rel') !== 'stylesheet') {
                    break;
                }
                if ($this->noscriptCounter) {
                    break;
                }

                $attributes = array();
                foreach ($node->attributes as $name => $anode) {
                    $attributes[$name] = $anode->nodeValue;
                }
                if (isset($attributes['onload'])) {
                    $this->breakCss();
                    break;
                }
                if ($this->config->css->checklinkattributes) {
                    if (isset($attributes['type']) && $attributes['type'] === 'text/css') {
                        unset($attributes['type']);
                    }
                    unset($attributes['rel'], $attributes['media'], $attributes['href'],
                        $attributes['ress-merge'], $attributes['ress-nomerge']);
                    if (count($attributes) > 0) {
                        // @todo use AllowCDN()
                        if (!preg_match('#^(https?:)?//fonts\.googleapis\.com/css#', $node->getAttribute('href'))) {
                            $this->breakCss();
                        }
                        break;
                    }
                } else {
                    if (isset($attributes['type']) && $attributes['type'] !== 'text/css') {
                        break;
                    }
                }

                // set type=text/css in html4 and remove in html5
                if ($this->doctype !== self::DOCTYPE_HTML5 && !$node->hasAttribute('type')) {
                    $node->setAttribute('type', 'text/css');
                }

                if ($node->hasAttribute('ress-nomerge')) {
                    $node->removeAttribute('ress-nomerge');
                    $merge = false;
                } else {
                    // minify css file (for external: breakpoint/load/@import)
                    $merge = $this->config->css->merge;
                    if ($merge) {
                        $src = $node->getAttribute('href');
                        if (strpos($src, '#') !== false) {
                            $merge = false;
                        } else {
                            $regex = $this->config->css->excludemergeregex;
                            if ($regex !== null && preg_match($regex, $src)) {
                                $merge = false;
                            } else {
                                $srcFile = $this->urlRewriter->urlToFilepath($src);
                                $merge = ($srcFile !== null) && (pathinfo($srcFile, PATHINFO_EXTENSION) === 'css') && $this->di->filesystem->isFile($srcFile);
                            }
                        }
                    }
                }

                if ($merge) {
                    $this->addCss($node);
                } else {
                    $this->breakCss();
                }

                break;

            case 'style':
                $iterateChildren = false; // don't change style sources
                if ($this->noscriptCounter) {
                    break;
                }

                $attributes = array();
                foreach ($node->attributes as $name => $anode) {
                    $attributes[$name] = $anode->nodeValue;
                }
                if ($this->config->css->checkstyleattributes) {
                    // break if there attributes other than type=text/css
                    if (isset($attributes['type']) && $attributes['type'] === 'text/css') {
                        unset($attributes['type']);
                    }
                    unset($attributes['media'],
                        $attributes['ress-merge'], $attributes['ress-nomerge']);
                    if (count($attributes) > 0) {
                        $this->breakCss();
                        break;
                    }
                } else {
                    if (isset($attributes['type']) && $attributes['type'] !== 'text/css') {
                        break;
                    }
                }

                if ($node->childNodes->item(0) === null) {
                    if ($this->config->css->mergeinline) {
                        $this->nodeDetach($node);
                    }
                    return false;
                }

                // set type=text/css in html4 and remove in html5
                if ($this->doctype !== self::DOCTYPE_HTML5 && !$node->hasAttribute('type')) {
                    $node->setAttribute('type', 'text/css');
                }
                // remove media attribute if it is empty or "all"
                if ($this->config->html->removedefattr && $node->hasAttribute('media')) {
                    $media = $node->getAttribute('media');
                    // @todo: parse media
//                    $media = $this->filterMedia($media);
                    if ($media === '' || $media === 'all') {
                        $node->removeAttribute('media');
                    }
                }
                // css break point if scoped=... attribute
                if ($node->hasAttribute('scoped')) {
                    $this->breakCss();
                }

                // @todo: check type

                if ($node->hasAttribute('ress-nomerge')) {
                    $node->removeAttribute('ress-nomerge');
                    $merge = false;
                } elseif ($node->hasAttribute('ress-merge')) {
                    $node->removeAttribute('ress-merge');
                    $merge = true;
                } else {
                    $merge =
                        is_bool($this->config->css->mergeinline)
                            ? $this->config->css->mergeinline
                            : $this->headMode;
                }

                if ($merge) {
                    $this->addCss($node, true);
                } else {
                    $this->breakCss();
                }

                break;

            case 'noscript':
                // @todo remove if js is enabled?
                break;

            case 'svg':
                // @todo implement svg optimization
                break;
        }

        $this->dispatcher->triggerEvent('HtmlIterateTag' . $tagName, array($this, $node));
        if ($node->parentNode === null) {
            return false;
        }

        if (($node->nodeName !== 'script') && ($node->nodeName !== 'ressscript')) {
            $this->breakJs();
        }

        if ($node->hasAttribute('onload') || $node->hasAttribute('onerror')) {
            $this->breakJs(true);
        }

        // minify uri in attributes
        if ($this->config->html->urlminify && isset($this->uriAttrs[$node->nodeName]) &&
            !($node->nodeName === 'link' && $node->hasAttribute('rel') && $node->getAttribute('rel') !== 'stylesheet')
        ) {
            foreach ($this->uriAttrs[$node->nodeName] as $attrName) {
                if ($node->hasAttribute($attrName)) {
                    $uri = $node->getAttribute($attrName);
                    if ($uri !== '' && strncmp($uri, 'data:', 5) !== 0) {
                        $node->setAttribute($attrName, $this->urlRewriter->minify($uri));
                    }
                }
            }
        }

        //minify style attribute (css)
        if ($this->config->css->minifyattribute && $node->hasAttribute('style')) {
            $node->setAttribute('style', $this->cssMinifyInline($node->getAttribute('style'), $this->urlRewriter->getBase(), $this->urlRewriter->getBase()));
        }

        //minify on* handlers (js)
        if ($this->config->js->minifyattribute) {
            foreach ($node->attributes as $name => $anode) {
                if (isset($this->jsEvents[$name])) {
                    $node->setAttribute($name, $this->jsMinifyInline($anode->nodeValue));
                }
            }
        }

        //compress class attribute
        if ($node->hasAttribute('class')) {
            $node->setAttribute('class', preg_replace('/\s{2,}|[\n\r\t\f]/S', ' ', $node->getAttribute('class')));
        }

        //remove default attributes with default values (type=text for input etc)
        if ($this->config->html->removedefattr) {
            switch ($this->doctype) {
                case self::DOCTYPE_HTML5:
                    $defaultAttrs = $this->defaultAttrsHtml5;
                    break;
                case self::DOCTYPE_HTML4:
                    $defaultAttrs = $this->defaultAttrsHtml4;
                    break;
                default:
                    $defaultAttrs = array();
            }
            if (isset($defaultAttrs[$node->nodeName])) {
                foreach ($defaultAttrs[$node->nodeName] as $attrName => $attrValue) {
                    if ($node->getAttribute($attrName) === $attrValue) {
                        $node->removeAttribute($attrName);
                    }
                }
            }
        }

        // rearrange attributes to improve gzip compression
        // (e.g. always use <input type=" or <option value=", etc.)
        if ($this->config->html->sortattr && $node->attributes->item(1) !== null && isset($this->attrFirst[$node->nodeName])) {
            $this->cmpAttrFirst = $this->attrFirst[$node->nodeName];
            $attributes = array();
            foreach ($node->attributes as $name => $anode) {
                $attributes[$name] = $anode->nodeValue;
                $node->removeAttribute($name);
            }
            uksort($attributes, array($this, 'attrFirstCmp'));
            foreach ($attributes as $name => $value) {
                $node->setAttribute($name, $value);
            }
        }

        $this->dispatcher->triggerEvent('HtmlIterateTag' . $tagName . 'After', array($this, $node));
        if ($node->parentNode === null) {
            return false;
        }

        return $iterateChildren;
    }

    /**
     * @param Ressio_HtmlOptimizer_Dom_Element|Ressio_HtmlOptimizer_Dom_Document $node
     * @param bool $mergeSpace
     * @throws ERessio_UnknownDiKey
     */
    protected function domIterate(&$node, $mergeSpace)
    {
        $mergeSpace = $mergeSpace && !isset($this->tags_preservespaces[$node->nodeName]);
        $child = $node->childNodes->item(0);
        while ($child !== null) {
            $nextChild = $child->nextSibling;
            $this->dispatcher->triggerEvent('HtmlIterateNodeBefore', array($this, $child));
            if ($child->parentNode !== null && $this->domProcess($child, $mergeSpace)) {
                if ($child->nodeName === 'noscript') {
                    $this->noscriptCounter++;
                }
                $this->domIterate($child, $mergeSpace);
                if ($child->nodeName === 'noscript') {
                    $this->noscriptCounter--;
                }
                if ($child->nodeName === 'body') {
                    // move async scripts to the end
                    /** @var Ressio_HtmlOptimizer_Dom_Element $jsNode */
                    if ($this->lastAsyncJsNode !== null) {
                        $jsNode = $this->lastAsyncJsNode;
                    } else {
                        $jsNode = $this->dom->createElement('ressscript');
                        $index = ++$this->jscssIndex;
                        $jsNode->setAttribute('index', $index);
                        $this->jscssLists[$index] = array();
                    }
                    $child->appendChild($jsNode);
                    $this->lastJsNode = $this->lastAsyncJsNode = $jsNode;
                }
                $this->dispatcher->triggerEvent('HtmlIterateNodeAfter', array($this, $child));
            }
            $child = $nextChild;
        }
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @param $append bool
     * @param $inline bool
     * @param $autoasync bool
     */
    private function addJs(&$node, $append = false, $inline = false, $autoasync = false)
    {
        // save src/content because $node will be destroyed
        $src = $inline ? $node->textContent : $node->getAttribute('src');
        $async = $node->hasAttribute('async');
        $defer = $node->hasAttribute('defer');

        // @todo: take into account difference between async and defer

        $jsAsync = $append || $async || $defer || $autoasync;

        if ($this->lastJsNode !== null) {
            $jsNode = $this->lastJsNode;
            // joint script tags
            $this->nodeDetach($node);
        } elseif ($this->lastAsyncJsNode !== null) {
            $jsNode = $this->lastAsyncJsNode;
            if (!$append) {
                $node->parentNode->replaceChild($jsNode, $node);
            } else {
                $this->nodeDetach($node);
            }
            /** @var Ressio_HtmlOptimizer_Dom_Element $node */
            $node = $jsNode;
        } else {
            $jsNode = $this->dom->createElement('ressscript');
            $index = ++$this->jscssIndex;
            $jsNode->setAttribute('index', $index);
            $this->jscssLists[$index] = array();

            $node->parentNode->replaceChild($jsNode, $node);
            /** @var Ressio_HtmlOptimizer_Pharse_JSList $node */
            $node = $jsNode;

            $this->lastJsNode = $this->lastAsyncJsNode = $node;
        }

        if (!$jsAsync) {
            $this->lastAsyncJsNode = null;
        }

        $index = (int)$jsNode->getAttribute('index');
        $this->jscssLists[$index][] = $inline
            ? array(
                'type' => 'inline',
                'script' => $src,
                'async' => $async,
                'defer' => $defer
            ) : array(
                'type' => 'ref',
                'src' => $src,
                'async' => $async,
                'defer' => $defer
            );
    }

    private function breakJs($full = false)
    {
        $this->lastJsNode = null;
        if ($full) {
            $this->lastAsyncJsNode = null;
        }
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @param $inline bool
     */
    private function addCss(&$node, $inline = false)
    {
        $src = $inline ? $node->textContent : $node->getAttribute('href');

        $media = $node->hasAttribute('media') ? $node->getAttribute('media') : 'all';

        if ($this->lastCssNode !== null) {
            $this->nodeDetach($node);
        } else {
            /** @var Ressio_HtmlOptimizer_Dom_Element $newNode */
            $newNode = $this->dom->createElement('resscss');
            $index = ++$this->jscssIndex;
            $newNode->setAttribute('index', $index);
            $this->jscssLists[$index] = array();

            $node->parentNode->replaceChild($newNode, $node);
            /** @var Ressio_HtmlOptimizer_Pharse_CSSList $node */
            $node = $newNode;

            $this->lastCssNode = $node;
        }

        $index = (int)$this->lastCssNode->getAttribute('index');
        $this->jscssLists[$index][] = $inline
            ? array(
                'type' => 'inline',
                'style' => $src,
                'media' => $media
            )
            : array(
                'type' => 'ref',
                'src' => $src,
                'media' => $media
            );
    }

    private function breakCss()
    {
        $this->lastCssNode = null;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @return string
     */
    public function nodeToString($node)
    {
        // @note returns node instead of text (to allow insert into other nodes)
        // return $node->cloneNode();
        return $this->dom->saveHTML($node);
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     */
    public function nodeDetach(&$node)
    {
        $node->parentNode->removeChild($node);
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @return bool
     */
    public function nodeIsDetached($node)
    {
        return $node->parentNode === null;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @param $text string
     */
    public function nodeSetInnerText(&$node, $text)
    {
        $node->textContent = $text;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @return string
     */
    public function nodeGetInnerText(&$node)
    {
        return $node->textContent;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @param $tag string
     * @param $attribs array
     */
    public function nodeWrap(&$node, $tag, $attribs = null)
    {
        $newNode = $this->dom->createElement($tag);
        if ($attribs) {
            /** @var $attribs array */
            foreach ($attribs as $name => $value) {
                $newNode->setAttribute($name, $value);
            }
        }
        $node->parentNode->insertBefore($newNode, $node);
        $node = $newNode->appendChild($node);
    }

    /**
     * @param string $tag
     * @param string[]|null $attribs
     * @param string|null|false $content
     * @return DOMElement
     */
    protected function createNode($tag, $attribs = null, $content = null)
    {
        $newNode = $this->dom->createElement($tag);
        if ($attribs !== null) {
            /** @var $attribs array */
            foreach ($attribs as $name => $value) {
                $newNode->setAttribute($name, $value);
            }
        }
        if ($content !== false) {
            // if ($content === null) { $newNode->nodeValue = ''; }
            $newNode->appendChild($this->dom->createTextNode($content));
        }
        return $newNode;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertBefore(&$node, $tag, $attribs = null, $content = null)
    {
        $newNode = $this->createNode($tag, $attribs, $content);
        $node->parentNode->insertBefore($newNode, $node);
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Dom_Element
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertAfter(&$node, $tag, $attribs = null, $content = null)
    {
        $newNode = $this->createNode($tag, $attribs, $content);
        $node->parentNode->insertBefore($newNode, $node->nextSibling);
    }

    /**
     * @param $nodedata,... array (string $tag, array $attribs, string $content)
     * @return bool return false if no <head> found
     */
    public function prependHead($nodedata)
    {
        $head = $this->dom->getElementsByTagName('head')->item(0);
        if ($head !== null) {
            $injectPoint = $head->firstChild;
            foreach (func_get_args() as $node) {
                list($tag, $attribs, $content) = $node;
                if ($tag === '!--') {
                    $newNode = $this->dom->createComment($content);
                } else {
                    $newNode = $this->createNode($tag, $attribs, $content);
                }
                $head->insertBefore($newNode, $injectPoint);
            }
            return true;
        }
        return false;
    }

    /**
     * @param $nodedata array (string $tag, array $attribs, string $content)
     * @return bool return false if no <link rel=stylesheet>, <style>, <script>, or combining wrappers
     */
    public function insertBeforeStyleScript($nodedata)
    {
        /** @var Ressio_HtmlOptimizer_Dom_Element $node */
        $node = $this->dom;
        $parentStack = array();
        $parentPos = array();
        $level = 0;

        while ($node !== null) {
            $isLink = ($node->nodeName === 'link') && $node->hasAttribute('rel') && ($node->getAttribute('rel') === 'stylesheet' || $node->getAttribute('rel') === 'ress-css');
            $isStyle = ($node->nodeName === 'style');
            $isCss = $isLink || $isStyle || ($node instanceof $this->classNodeCssList && $node->nodeName === 'resscss');

            if ($isCss || ($node instanceof $this->classNodeJsList && $node->nodeName === 'ressscript')) {
                $parent = $node->parentNode;

                foreach (func_get_args() as $_node) {
                    list($tag, $attribs, $content) = $_node;
                    $newNode = $this->createNode($tag, $attribs, $content);
                    $parent->insertBefore($newNode, $node);
                }

                return true;
            }

            if ($node->hasChildNodes()) {
                $level++;
                $parentStack[$level] = $node;
                $parentPos[$level] = 0;
                $node = $node->firstChild;
            } else {
                while ($level > 0) {
                    $parentPos[$level]++;
                    $pos = $parentPos[$level];
                    /** @var Ressio_HtmlOptimizer_Dom_Element $parent */
                    $parent = $parentStack[$level];
                    if ($pos < $parent->childNodes->length) {
                        $node = $parent->childNodes->item($pos);
                        break;
                    }
                    $level--;
                }
                if ($level === 0) {
                    break;
                }
            }
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isNoscriptState()
    {
        return $this->noscriptCounter > 0;
    }
}
