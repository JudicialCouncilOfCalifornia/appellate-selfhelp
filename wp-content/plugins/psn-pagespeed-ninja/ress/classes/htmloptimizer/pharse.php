<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

defined('RESSIO_PATH') or die('RESS: Restricted access');

include_once RESSIO_LIBS . '/pharse/pharse_parser_html.php';

class Ressio_HtmlOptimizer_Pharse extends Ressio_HtmlOptimizer_Base
{
    /** @var string */
    public $origDoctype;

    /** @var HTML_Node */
    public $dom;

    private $baseFound = false;

    /** @var Ressio_HtmlOptimizer_Pharse_JSList|null */
    private $lastJsNode;
    /** @var Ressio_HtmlOptimizer_Pharse_JSList|null */
    private $lastAsyncJsNode;
    /** @var Ressio_HtmlOptimizer_Pharse_CSSList|null */
    private $lastCssNode;

    /** @var int */
    public $noscriptCounter = 0;

    /** @var bool */
    public $headMode;

    public $classNodeCssList = 'Ressio_HtmlOptimizer_Pharse_CSSList';
    public $classNodeJsList = 'Ressio_HtmlOptimizer_Pharse_JSList';

    public function __construct()
    {
        $this->tags_selfclose['~stylesheet~'] = 0;
        $this->tags_nospaces['~root~'] = 0;
    }

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
        $page = new HTML_Parser_HTML5($buffer);

        $dom = $page->root;
        $this->dom = $dom;

        $this->lastJsNode = null;
        $this->lastAsyncJsNode = null;
        $this->lastCssNode = null;

        $this->headMode = true;

        $this->dispatcher->triggerEvent('HtmlIterateBefore', array($this));

        $this->domIterate($dom, $this->config->html->mergespace);

        if ($this->origDoctype === null && $this->config->html->forcehtml5) {
            $offset = 0;
            $dom->addDoctype('html', $offset);
        }

//        if ($this->config->js->autoasync && $this->lastAsyncJsNode) {
//            $this->lastAsyncJsNode->attributes['async'] = 'async';
//        }

//        if ($this->lastCssNode) {
//            // move to the end of body
//            $body = $dom->getChildrenByTag('body');
//            if (count($body) === 1) {
//                $this->lastCssNode->changeParent($body[0]);
//            }
//        }

        $this->dispatcher->triggerEvent('HtmlIterateAfter', array($this));

        $buffer = (string)$dom;

        $this->dom = null;
        $this->lastJsNode = null;
        $this->lastAsyncJsNode = null;
        $this->lastCssNode = null;

        return $buffer;
    }

    /**
     * @param $file string
     * @param $attribs array|null
     * @param $head bool|HTML_Node
     */
    public function appendScript($file, $attribs = null, $head = true)
    {
        if ($this->lastAsyncJsNode !== null) {
            $node = $this->dom->addChild('script');
            if ($this->doctype !== self::DOCTYPE_HTML5) {
                $node->attributes['type'] = 'text/javascript';
            }
            $node->attributes['src'] = $file;
            if (is_array($attribs)) {
                $node->attributes = array_merge($node->attributes, $attribs);
            }
            $this->addJs($node, true);
        } else {
            $jsNode = new $this->classNodeJsList($this->di);
            $jsNode->scriptList[] = array(
                'type' => 'ref',
                'src' => $file,
                'async' => isset($attribs['async']),
                'defer' => isset($attribs['defer'])
            );
            if (!($head instanceof HTML_Node)) {
                $injects = $this->dom->getChildrenByTag($head ? 'head' : 'body');
                $head = count($injects) ? $injects[0] : $this->dom;
                $head = $head->lastChild();
            }
            $index = $head->index();
            $head->parent->addChild($jsNode, $index);
            $this->lastJsNode = $this->lastAsyncJsNode = $jsNode;
        }
    }

    /**
     * @param $content string
     * @param $attribs array|null
     * @param $head bool|HTML_Node
     */
    public function appendScriptDeclaration($content, $attribs = null, $head = true)
    {
        if ($this->lastAsyncJsNode !== null) {
            /** @var HTML_Node $node */
            $node = $this->dom->addChild('script');
            if ($this->doctype !== self::DOCTYPE_HTML5) {
                $node->attributes['type'] = 'text/javascript';
            }
            if (is_array($attribs)) {
                $node->attributes = array_merge($node->attributes, $attribs);
            }
            $node->addText($content);
            $this->addJs($node, true, true);
        } else {
            $jsNode = new $this->classNodeJsList($this->di);
            $jsNode->scriptList[] = array(
                'type' => 'inline',
                'script' => $content,
                'async' => isset($attribs['async']),
                'defer' => isset($attribs['defer'])
            );
            if (!($head instanceof HTML_Node)) {
                $injects = $this->dom->getChildrenByTag($head ? 'head' : 'body');
                $head = count($injects) ? $injects[0] : $this->dom;
                $head = $head->lastChild();
            }
            $index = $head->index();
            $head->parent->addChild($jsNode, $index);
            $this->lastJsNode = $this->lastAsyncJsNode = $jsNode;
        }
    }

    /**
     * @param $file string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendStylesheet($file, $attribs = null, $head = true)
    {
        /** @var HTML_Node $node */
        $node = $this->dom->addChild('link');

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $node->attributes['type'] = 'text/css';
        }
        $node->attributes['rel'] = 'stylesheet';
        $node->attributes['href'] = $file;
        if (is_array($attribs)) {
            $node->attributes = array_merge($node->attributes, $attribs);
        }

        if ($this->lastCssNode !== null) {
            $this->addCss($node);
        } else {
            $cssNode = new $this->classNodeCssList($this->di);
            $cssNode->styleList[] = array(
                'type' => 'ref',
                'src' => $file,
                'media' => 'all'
            );
            $parent = $this->dom->getChildrenByTag($head ? 'head' : 'body');
            $parent = count($parent) ? $parent[0] : $this->dom;
            $parent->addChild($cssNode);
            $node->detach();
        }
    }

    /**
     * @param $content string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendStyleDeclaration($content, $attribs = null, $head = true)
    {
        /** @var HTML_Node $node */
        $node = $this->dom->addChild('style');

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $node->attributes['type'] = 'text/css';
        }
        if (is_array($attribs)) {
            $node->attributes = array_merge($node->attributes, $attribs);
        }

        $node->addText($content);

        if ($this->lastCssNode !== null) {
            $this->addCss($node, true);
        } else {
            $cssNode = new $this->classNodeCssList($this->di);
            $cssNode->styleList[] = array(
                'type' => 'inline',
                'style' => $content,
                'media' => 'all'
            );
            $parent = $this->dom->getChildrenByTag($head ? 'head' : 'body');
            $parent = count($parent) ? $parent[0] : $this->dom;
            $parent->addChild($cssNode);
            $node->detach();
        }
    }

    /**
     * @param HTML_Node $node
     * @param bool $mergeSpace
     * @throws ERessio_UnknownDiKey
     */
    protected function domIterate(&$node, $mergeSpace)
    {
        // skip xml and asp tags
        if ($node instanceof $node->childClass_XML ||
            $node instanceof $node->childClass_ASP
        ) {
            return;
        }

        // doctype
        if ($node instanceof $node->childClass_Doctype) {
            /** @var HTML_Node_DOCTYPE $node */
            $this->origDoctype = $node->dtd;
            if ($this->config->html->forcehtml5) {
                $node->dtd = 'html';
            } elseif (strpos($node->dtd, 'DTD HTML')) {
                $this->doctype = self::DOCTYPE_HTML4;
            } elseif (strpos($node->dtd, 'DTD XHTML')) {
                $this->doctype = self::DOCTYPE_XHTML;
            } else {
                $this->doctype = self::DOCTYPE_HTML5;
            }
            return;
        }

        // CDATA is text in xhtml and comment in html
        if ($node instanceof $node->childClass_Text ||
            ($this->doctype === self::DOCTYPE_XHTML && $node instanceof $node->childClass_CDATA)
        ) {
            /** @var HTML_Node_TEXT $node */
            if ($mergeSpace) {
                $node->text = preg_replace('/\s+/m', ' ', $node->text);
                if ($node->text === ' ' && isset($this->tags_nospaces[$node->parent->tag])) {
                    $node->detach();
                }
            }
            return;
        }

        // remove comments
        if ($node instanceof $node->childClass_Comment ||
            ($this->doctype !== self::DOCTYPE_XHTML && $node instanceof $node->childClass_CDATA)
        ) {
            /** @var HTML_Node_COMMENT $node */
            if ($this->config->html->removecomments && $node->text !== '' && $node->text[0] !== '!') {
                $node->detach();
            }
            return;
        }

        // check comments (keep IE ones on IE, [if, <![ : <!--[if IE]>, <!--<![endif]--> )
        // stop css/style combining in IE cond block
        if ($node instanceof $node->childClass_Conditional) {
            /** @var HTML_Node_CONDITIONAL $node */
            // @todo don't remove non-comment node <!--[if !IE]>-->HTML<!--<![endif]--> and <![if expression]>HTML<![endif]>
            if ($this->config->html->removeiecond) {
                $vendor = $this->di->deviceDetector->vendor();
                if ($vendor !== 'ms' && $vendor !== 'unknown') { // if not IE browser
                    $node->detach();
                    return;
                }
            }
            // @todo: parse as html and compress internals
            $this->breakCss();
            $this->breakJs();
            if ($mergeSpace) {
                $inner = $node->children[0]->text;
                $inner = preg_replace('#\s+<!--$#', '<!--', ltrim($inner));
                $node->children[0]->text = $inner;
            }
            return;
        }

        // disable optimizing of nodes with ress-safe attribute
        if (isset($node->attributes['ress-safe'])) {
            unset($node->attributes['ress-safe']);
            return;
        }

        // lowercase tags
        $tagName = strtoupper($node->tag);
        $node->tag = strtolower($node->tag);

        // @todo: remove first and last spaces in block elements
        // @todo: remove space after open/close tag if there is space before the tag

        /** @var HTML_Node $node */

        // check and parse ress-media attribute
        if (isset($node->attributes['ress-media'])) {
            if (!$this->matchRessMedia($node->attributes['ress-media'])) {
                $node->detach();
                return;
            }
            unset($node->attributes['ress-media']);
        }

        $iterateChildren = !isset($this->tags_selfclose[$node->tag]);

        $this->dispatcher->triggerEvent('HtmlIterateTag' . $tagName . 'Before', array($this, $node));
        if ($node->parent === null && $node->tag !== '~root~') {
            return;
        }

        switch ($node->tag) {
            case 'a':
            case 'area':
                if (isset($node->attributes['href'])) {
                    $uri = $node->attributes['href'];
                    if ($this->config->js->minifyattribute && strncmp($uri, 'javascript:', 11) === 0) {
                        $node->attributes['href'] = 'javascript:' . $this->jsMinifyInline(substr($uri, 11));
                    }
                }
                break;

            case 'base':
                // save base href (use first tag only)
                if (!$this->baseFound && isset($node->attributes['href'])) {
                    $base = $node->attributes['href'];
                    if (substr($base, -1) !== '/') {
                        $base = dirname($base);
                        if ($base === '.') {
                            $base = '';
                        }
                        $base .= '/';
                    }
                    $this->urlRewriter->setBase($base);
                    $node->attributes['href'] = $this->urlRewriter->getBase();
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

                if ($this->config->img->minify && isset($node->attributes['src'])) {
                    $src = $node->attributes['src'];
                    if ($src !== '' && strncmp($src, 'data:', 5) !== 0) {
                        $src_file = $this->urlRewriter->urlToFilepath($src);
                        if ($src_file !== null) {
                            $this->di->imgOptimizer->run($src_file);
                        }
                    }
                }
                if (($this->config->img->minify || $this->config->html->urlminify) && isset($node->attributes['srcset'])) {
                    $srcset = $node->attributes['srcset'];
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
                    $node->attributes['srcset'] = implode(',', $srclist);
                }

                break;

            case 'picture':
                // parse <picture> elements
                break;

            case 'script':
                $iterateChildren = false; // don't change script sources
                if ($this->noscriptCounter) {
                    $node->detach();
                    break;
                }

                if (isset($node->attributes['ress-noasync'])) {
                    unset($node->attributes['ress-noasync']);
                    $autoasync = false;
                } else {
                    $autoasync = $this->config->js->autoasync;
                }

                if (
                    isset($node->attributes['onload']) ||
                    (isset($node->attributes['data-cfasync']) && $node->attributes['data-cfasync'] === 'false')
                ) {
                    $this->breakJs(true);
                    break;
                }

                if ($this->config->js->forceasync) {
                    $node->attributes['async'] = false;
                }
                if ($this->config->js->forcedefer) {
                    $node->attributes['defer'] = false;
                }

                // break if there attributes other than type=text/javascript, defer, async
                if (count($node->attributes)) {
                    $attributes = $node->attributes;
                    if ($this->config->js->checkattributes) {
                        // @todo support language="javascript" attribute
                        if (isset($attributes['type']) && $attributes['type'] === 'text/javascript') {
                            unset($attributes['type']);
                            if ($this->config->html->removedefattr) {
                                unset($node->attributes['type']);
                            }
                        }
                        if (isset($attributes['language']) && strcasecmp($attributes['language'], 'javascript') === 0) {
                            unset($attributes['language']);
                            if ($this->config->html->removedefattr) {
                                unset($node->attributes['language']);
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
                if ($this->doctype !== self::DOCTYPE_HTML5 && !isset($node->attributes['type'])) {
                    $node->attributes['type'] = 'text/javascript';
                }

                if (!isset($node->attributes['src'])) { // inline
                    if (count($node->children) === 0) {
                        if ($this->config->js->merge) {
                            $node->detach();
                        }
                        return;
                    }

                    $scriptBlob = $node->children[0]->text;
                    // @todo: refactor clear comments
                    $scriptBlob = preg_replace(array('#^\s*<!--.*?[\r\n]+#', '#//\s*<!--.*$#m', '#//\s*-->.*$#m', '#\s*-->\s*$#'), '', $scriptBlob);
                    $scriptBlob = preg_replace('#^\s*(?://\s*)?<!\[CDATA\[\s*(.*?)\s*(?://\s*)?\]\]>\s*$#', '\1', $scriptBlob);
                    $scriptBlob = preg_replace('#^\s*/\*\s*<!\[CDATA\[\s*\*/\s*(.*?)\s*/\*\s*\]\]>\s*\*/\s*$#', '\1', $scriptBlob);

                    $node->children[0]->text = $scriptBlob;

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

                    if (isset($node->attributes['ress-nomerge'])) {
                        unset($node->attributes['ress-nomerge']);
                        $merge = false;
                    } elseif (isset($node->attributes['ress-merge'])) {
                        unset($node->attributes['ress-merge']);
                        $merge = true;
                    } else {
                        $merge =
                            is_bool($this->config->js->mergeinline)
                                ? $this->config->js->mergeinline
                                : $this->headMode;
                        if ($merge && isset($node->attributes['id'])) {
                            $id = $node->attributes['id'];
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
                    if (isset($node->attributes['ress-nomerge'])) {
                        unset($node->attributes['ress-nomerge']);
                        $merge = false;
                    } elseif (isset($node->attributes['ress-merge'])) {
                        unset($node->attributes['ress-merge']);
                        $merge = true;
                    } else {
                        $merge = $this->config->js->merge;
                    }

                    if ($merge) {
                        $src = $node->attributes['src'];
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
                if (!isset($node->attributes['rel'], $node->attributes['href']) || $node->attributes['rel'] !== 'stylesheet') {
                    break;
                }
                if ($this->noscriptCounter) {
                    break;
                }

                $attributes = $node->attributes;
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
                        if (!preg_match('#^(https?:)?//fonts\.googleapis\.com/css#', $node->attributes['href'])) {
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
                if ($this->doctype !== self::DOCTYPE_HTML5 && !isset($node->attributes['type'])) {
                    $node->attributes['type'] = 'text/css';
                }

                if (isset($node->attributes['ress-nomerge'])) {
                    unset($node->attributes['ress-nomerge']);
                    $merge = false;
                } else {
                    // minify css file (for external: breakpoint/load/@import)
                    $merge = $this->config->css->merge;
                    if ($merge) {
                        $src = $node->attributes['href'];
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

                $attributes = $node->attributes;
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

                if (count($node->children) === 0) {
                    if ($this->config->css->mergeinline) {
                        $node->detach();
                    }
                    return;
                }

                // set type=text/css in html4 and remove in html5
                if ($this->doctype !== self::DOCTYPE_HTML5 && !isset($node->attributes['type'])) {
                    $node->attributes['type'] = 'text/css';
                }
                // remove media attribute if it is empty or "all"
                if (isset($node->attributes['media']) && $this->config->html->removedefattr) {
                    $media = $node->attributes['media'];
                    // @todo: parse media
//                    $media = $this->filterMedia($media);
                    if ($media === '' || $media === 'all') {
                        unset($node->attributes['media']);
                    }
                }
                // css break point if scoped=... attribute
                if (isset($node->attributes['scoped'])) {
                    $this->breakCss();
                }

                // @todo: check type

                if (isset($node->attributes['ress-nomerge'])) {
                    unset($node->attributes['ress-nomerge']);
                    $merge = false;
                } elseif (isset($node->attributes['ress-merge'])) {
                    unset($node->attributes['ress-merge']);
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
        if ($node->parent === null && $node->tag !== '~root~') {
            return;
        }

        if (($node->tag !== 'script') && ($node->tag !== '~javascript~')) {
            $this->breakJs();
        }

        if (isset($node->attributes['onload']) || isset($node->attributes['onerror'])) {
            $this->breakJs(true);
        }

        // minimal form of self-close tags
        if (isset($this->tags_selfclose[$node->tag])) {
            $node->self_close_str = ($this->doctype === self::DOCTYPE_XHTML) ? '/' : '';
        }

        // minify uri in attributes
        if ($this->config->html->urlminify && isset($this->uriAttrs[$node->tag]) &&
            !($node->tag === 'link' && isset($node->attributes['rel']) && $node->attributes['rel'] !== 'stylesheet')
        ) {
            foreach ($this->uriAttrs[$node->tag] as $attrName) {
                if (isset($node->attributes[$attrName])) {
                    $uri = $node->attributes[$attrName];
                    if ($uri !== '' && strncmp($uri, 'data:', 5) !== 0) {
                        $node->attributes[$attrName] = $this->urlRewriter->minify($uri);
                    }
                }
            }
        }

        //minify style attribute (css)
        if ($this->config->css->minifyattribute && isset($node->attributes['style'])) {
            $node->attributes['style'] = $this->cssMinifyInline($node->attributes['style'], $this->urlRewriter->getBase(), $this->urlRewriter->getBase());
        }

        //minify on* handlers (js)
        if ($this->config->js->minifyattribute) {
            foreach ($node->attributes as $name => &$value) {
                if (isset($this->jsEvents[$name])) {
                    $value = $this->jsMinifyInline($value);
                }
            }
            unset($value);
        }

        //compress class attribute
        if (isset($node->attributes['class'])) {
            $node->attributes['class'] = preg_replace('#\s+#', ' ', $node->attributes['class']);
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
            if (isset($defaultAttrs[$node->tag])) {
                foreach ($defaultAttrs[$node->tag] as $attrName => $attrValue) {
                    if (isset($node->attributes[$attrName]) && $node->attributes[$attrName] === $attrValue) {
                        unset($node->attributes[$attrName]);
                    }
                }
            }
        }

        // rearrange attributes to improve gzip compression
        // (e.g. always use <input type=" or <option value=", etc.)
        if ($this->config->html->sortattr && count($node->attributes) >= 2 && isset($this->attrFirst[$node->tag])) {
            $this->cmpAttrFirst = $this->attrFirst[$node->tag];
            uksort($node->attributes, array($this, 'attrFirstCmp'));
        }

        $this->dispatcher->triggerEvent('HtmlIterateTag' . $tagName . 'After', array($this, $node));
        if ($node->parent === null && $node->tag !== '~root~') {
            return;
        }

        if ($iterateChildren) {
            $children = $node->children;
            $mergeSpace = $mergeSpace && !isset($this->tags_preservespaces[$node->tag]);
            if ($node->tag === 'noscript') {
                $this->noscriptCounter++;
            }
            foreach ($children as $child) {
                $this->dispatcher->triggerEvent('HtmlIterateNodeBefore', array($this, $child));
                if ($child->parent === null) {
                    unset($child);
                    continue;
                }
                $this->domIterate($child, $mergeSpace);
                $this->dispatcher->triggerEvent('HtmlIterateNodeAfter', array($this, $child));
                if ($child->parent === null) {
                    unset($child);
                    continue;
                }
            }
            if ($node->tag === 'noscript') {
                $this->noscriptCounter--;
            }
            if ($node->tag === 'body') {
                // move async scripts to the end
                /** @var Ressio_HtmlOptimizer_Pharse_JSList $jsNode */
                if ($this->lastAsyncJsNode !== null) {
                    $jsNode = $this->lastAsyncJsNode;
                } else {
                    $jsNode = new $this->classNodeJsList($this->di);
                }
                $index = $node->childCount();
                $jsNode->changeParent($node, $index);
                $this->lastJsNode = $this->lastAsyncJsNode = $jsNode;
            }
        }

        //@todo: remove closing tags for </li> etc (modify HTML_Node::toString)
        //@todo: remove quotes in attribute values (modify HTML_Node::toString_attributes)
    }

    /**
     * @param $node HTML_Node
     * @param $append bool
     * @param $inline bool
     * @param $autoasync bool
     */
    private function addJs(&$node, $append = false, $inline = false, $autoasync = false)
    {
        // save src/content because $node will be destroyed
        $src = $inline ? $node->children[0]->text : $node->attributes['src'];
        $async = isset($node->attributes['async']);
        $defer = isset($node->attributes['defer']);

        // @todo: take into account difference between async and defer

        $jsAsync = $append || $async || $defer || $autoasync;

        if ($this->lastJsNode !== null) {
            $jsNode = $this->lastJsNode;
            // joint script tags
            $node->detach();
        } elseif ($this->lastAsyncJsNode !== null) {
            $jsNode = $this->lastAsyncJsNode;
            if (!$append) {
                $index = $node->index();
                $jsNode->changeParent($node->parent, $index);
            }
            $node->detach();
            /** @var Ressio_HtmlOptimizer_Pharse_JSList $node */
            $node = $jsNode;
        } else {
            $jsNode = new $this->classNodeJsList($this->di);
            $index = $node->index();
            $node->parent->addChild($jsNode, $index);
            $node->detach();
            /** @var Ressio_HtmlOptimizer_Pharse_JSList $node */
            $node = $jsNode;

            $this->lastJsNode = $this->lastAsyncJsNode = $node;
        }

        if (!$jsAsync) {
            $this->lastAsyncJsNode = null;
        }

        $jsNode->scriptList[] = $inline
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
     * @param $node HTML_Node
     * @param $inline bool
     */
    private function addCss(&$node, $inline = false)
    {
        $src = $inline ? $node->children[0]->text : $node->attributes['href'];

        $media = isset($node->attributes['media']) ? $node->attributes['media'] : 'all';

        if ($this->lastCssNode !== null) {
            $node->detach();
        } else {
            /** @var Ressio_HtmlOptimizer_Pharse_CSSList $newNode */
            $newNode = new $this->classNodeCssList($this->di);
            $index = $node->index();
            $node->parent->addChild($newNode, $index);
            $node->detach();
            /** @var Ressio_HtmlOptimizer_Pharse_CSSList $node */
            $node = $newNode;

            $this->lastCssNode = $node;
        }

        $this->lastCssNode->styleList[] = $inline
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
     * @param $node HTML_Node
     * @return string
     */
    public function nodeToString($node)
    {
        return $node->toString();
    }

    /**
     * @param $node HTML_Node
     */
    public function nodeDetach(&$node)
    {
        $node->detach();
    }

    /**
     * @param $node HTML_Node
     * @return bool
     */
    public function nodeIsDetached($node)
    {
        return $node->parent === null;
    }

    /**
     * @param $node HTML_Node
     * @param $text string
     */
    public function nodeSetInnerText(&$node, $text)
    {
        $node->children = array(
            new $node->childClass_Text($node, $text)
        );
    }

    /**
     * @param $node HTML_Node
     * @return string
     */
    public function nodeGetInnerText(&$node)
    {
        return $node->children[0]->text;
    }

    /**
     * @param $node HTML_Node
     * @param $tag string
     * @param $attribs array
     */
    public function nodeWrap(&$node, $tag, $attribs = null)
    {
        $newNode = $node->wrap($tag);
        if ($attribs) {
            $newNode->attributes = $attribs;
        }
    }

    /**
     * @param $node HTML_Node
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertBefore(&$node, $tag, $attribs = null, $content = null)
    {
        /** @var HTML_Node $newNode */
        $newNode = new $node->childClass($tag, $attribs);
        if ($content !== null) {
            $newNode->addText($content);
        }
        $node->parent->insertChild($newNode, $node->index());
    }

    /**
     * @param $node HTML_Node
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertAfter(&$node, $tag, $attribs = null, $content = null)
    {
        /** @var HTML_Node $newNode */
        $newNode = new $node->childClass($tag, $attribs);
        if ($content !== null) {
            $newNode->addText($content);
        }
        $node->parent->insertChild($newNode, $node->index() + 1);
    }

    /**
     * @param $nodedata,... array (string $tag, array $attribs, string $content)
     * @return bool return false if no <head> found
     */
    public function prependHead($nodedata)
    {
        /** @var HTML_Node[] $heads */
        $heads = $this->dom->getChildrenByTag('head');
        if (count($heads) === 1) {
            $head = $heads[0];

            foreach (array_reverse(func_get_args()) as $node) {
                list($tag, $attribs, $content) = $node;
                $offset = 0;
                if ($tag === '!--') {
                    $head->addComment($content, $offset);
                } else {
                    $newNode = $head->addChild($tag, $offset);
                    if ($attribs) {
                        $newNode->attributes = $attribs;
                    }
                    if ($content === false) {
                        $newNode->self_close = true;
                        $newNode->self_close_str = ($this->doctype === self::DOCTYPE_XHTML) ? '/' : '';
                    }
                    if ($content !== null) {
                        $newNode->addText($content);
                    }
                }
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
        /** @var HTML_Node $node */
        $node = $this->dom;
        $parentStack = array();
        $parentPos = array();
        $level = 0;

        while ($node !== null) {
            $isLink = ($node->tag === 'link') && isset($node->attributes['rel']) && ($node->attributes['rel'] === 'stylesheet' || $node->attributes['rel'] === 'ress-css');
            $isStyle = ($node->tag === 'style');
            $isCss = $isLink || $isStyle || $node instanceof $this->classNodeCssList;

            if ($isCss || $node instanceof $this->classNodeJsList) {
                $parent = $node->parent;
                $index = $parent->findChild($node);

                foreach (array_reverse(func_get_args()) as $node) {
                    list($tag, $attribs, $content) = $node;
                    /** @var HTML_Node $newNode */
                    $newNode = new $node->childClass($tag, $attribs);
                    if ($content !== null) {
                        $newNode->addText($content);
                    }
                    $parent->insertChild($newNode, $index);
                }

                return true;
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
