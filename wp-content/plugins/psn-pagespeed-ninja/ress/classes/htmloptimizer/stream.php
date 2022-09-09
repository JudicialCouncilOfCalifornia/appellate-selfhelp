<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_HtmlOptimizer_Stream extends Ressio_HtmlOptimizer_Base
{
    /** @var array */
    public $dom;

    /** @var bool */
    private $baseFound = false;

    /** @var Ressio_HtmlOptimizer_Stream_JSList|null */
    private $lastJsNode;
    /** @var Ressio_HtmlOptimizer_Stream_JSList|null */
    private $lastAsyncJsNode;
    /** @var int */
    private $lastJsNodeIndex = -1;

    /** @var Ressio_HtmlOptimizer_Stream_CSSList|null */
    private $lastCssNode;

    /** @var int */
    public $noscriptCounter = 0;

    /** @var bool */
    public $headMode;

    public $classNodeCssList = 'Ressio_HtmlOptimizer_Stream_CSSList';
    public $classNodeJsList = 'Ressio_HtmlOptimizer_Stream_JSList';

    static protected $block_end = array(
        '!--[if' => '<![endif]-->',
        '![if' => '<![endif]>',
        '!--' => '-->',
        'script' => '</script>',
        'style' => '</style>'
    );

    /**
     * @param $buffer string
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function run($buffer)
    {
        $this->lastJsNode = null;
        $this->lastAsyncJsNode = null;
        $this->lastCssNode = null;

        $this->headMode = true;

        $this->dispatcher->triggerEvent('HtmlIterateBefore', array($this));

        $this->dom = array();
        $this->htmlIterate($buffer);

        $this->dispatcher->triggerEvent('HtmlIterateAfter', array($this));

        $buffer = '';
        foreach ($this->dom as $part) {
            if (is_string($part)) {
                $buffer .= $part;
            } else {
                $buffer .= $part->toString();
            }
        }

        $this->dom = null;
        $this->lastJsNode = null;
        $this->lastAsyncJsNode = null;
        $this->lastCssNode = null;

        return $buffer;
    }

    /**
     * @param bool $head
     * @return Ressio_HtmlOptimizer_Stream_JSList
     */
    private function getLastJsNode($head)
    {
        $index = array_search($head ? '</head>' : '</body>', $this->dom, true);

        if ($index !== false) {
            /** @var int $index */
            $index--;
            while ($index >= 0 && !(isset($this->dom[$index]) && $this->dom[$index] instanceof $this->classNodeJsList)) {
                $index--;
            }
            if ($index < 0) {
                $index = false;
            }
        }

        /** @var Ressio_HtmlOptimizer_Stream_JSList $jsNode */
        if ($index !== false) {
            $jsNode = $this->dom[$index];
        } else {
            $jsNode = new $this->classNodeJsList($this->di);
            $this->dom[] = $this->lastJsNode = $this->lastAsyncJsNode = $jsNode;
            end($this->dom);
            $this->lastJsNodeIndex = key($this->dom);
        }

        return $jsNode;
    }

    /**
     * @param $file string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendScript($file, $attribs = null, $head = true)
    {
        if ($this->lastAsyncJsNode !== null) {
            $attributes = array();
            if ($this->doctype !== self::DOCTYPE_HTML5) {
                $attributes['type'] = 'text/javascript';
            }
            $attributes['src'] = $file;
            if (is_array($attribs)) {
                $attributes = array_merge($attributes, $attribs);
            }
            $this->addJs($this->dom, $attributes, null, true);
        } else {
            $jsNode = $this->getLastJsNode($head);
            $jsNode->scriptList[] = array(
                'type' => 'ref',
                'src' => $file,
                'async' => isset($attribs['async']),
                'defer' => isset($attribs['defer'])
            );
        }
    }

    /**
     * @param $content string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendScriptDeclaration($content, $attribs = null, $head = true)
    {
        if ($this->lastAsyncJsNode !== null) {
            $attributes = array();
            if ($this->doctype !== self::DOCTYPE_HTML5) {
                $attributes['type'] = 'text/javascript';
            }
            if (is_array($attribs)) {
                $attributes = array_merge($attributes, $attribs);
            }
            $this->addJs($this->dom, $attributes, $content, true);
        } else {
            $jsNode = $this->getLastJsNode($head);
            $jsNode->scriptList[] = array(
                'type' => 'inline',
                'script' => $content,
                'async' => isset($attribs['async']),
                'defer' => isset($attribs['defer'])
            );
        }
    }

    /**
     * @param $file string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendStylesheet($file, $attribs = null, $head = true)
    {
        $attributes = array();

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $attributes['type'] = 'text/css';
        }
        $attributes['rel'] = 'stylesheet';
        $attributes['href'] = $file;
        if (is_array($attribs)) {
            $attributes = array_merge($attributes, $attribs);
        }
        if ($this->lastCssNode !== null) {
            $this->addCss($this->dom, $attributes, null);
        } else {
            $index = array_search($head ? '</head>' : '</body>', $this->dom, true);

            if ($index !== false) {
                /** @var int $index */
                $index--;
                while ($index >= 0 && !(isset($this->dom[$index]) && $this->dom[$index] instanceof $this->classNodeCssList)) {
                    $index--;
                }
                if ($index < 0) {
                    $index = false;
                }
            }

            /** @var Ressio_HtmlOptimizer_Stream_CSSList $cssNode */
            if ($index !== false) {
                $cssNode = $this->dom[$index];
            } else {
                $cssNode = new $this->classNodeCssList($this->di);
                $this->dom[] = $this->lastCssNode = $cssNode;
            }
            $cssNode->styleList[] = array(
                'type' => 'ref',
                'src' => $file,
                'media' => 'all'
            );
        }
    }

    /**
     * @param $content string
     * @param $attribs array|null
     * @param $head bool
     */
    public function appendStyleDeclaration($content, $attribs = null, $head = true)
    {
        $attributes = array();

        if ($this->doctype !== self::DOCTYPE_HTML5) {
            $attributes['type'] = 'text/css';
        }
        if (is_array($attribs)) {
            $attributes = array_merge($attributes, $attribs);
        }

        if ($this->lastCssNode !== null) {
            $this->addCss($this->dom, $attributes, $content);
        } else {
            $index = array_search($head ? '</head>' : '</body>', $this->dom, true);

            if ($index !== false) {
                /** @var int $index */
                $index--;
                while ($index >= 0 && !(isset($this->dom[$index]) && $this->dom[$index] instanceof $this->classNodeCssList)) {
                    $index--;
                }
                if ($index < 0) {
                    $index = false;
                }
            }

            /** @var Ressio_HtmlOptimizer_Stream_CSSList $cssNode */
            if ($index !== false) {
                $cssNode = $this->dom[$index];
            } else {
                $cssNode = new $this->classNodeCssList($this->di);
                $this->dom[] = $this->lastCssNode = $cssNode;
            }
            $cssNode->styleList[] = array(
                'type' => 'inline',
                'style' => $content,
                'media' => 'all'
            );
        }
    }

    /**
     * @param string $buffer
     * @throws ERessio_UnknownDiKey
     */
    protected function htmlIterate($buffer)
    {
        $pos = 0;

        $node = new Ressio_HtmlOptimizer_Stream_NodeWrapper;

        // @todo parse text nodes (remove spaces)
        // @todo parse all tags (remove spaces, reorder attributes)
        while (preg_match('#<(!(?:--\[if\b|--|\[if\b)|(?:!doctype|base|body|/body|head|/head|iframe|img|link|noscript|/noscript|script|style)(?=[>\s]))#i', $buffer, $matches, PREG_OFFSET_CAPTURE, $pos)) {
            $start = $matches[0][1];
            $tag = substr($buffer, $pos, $start - $pos);
            $this->dom[] = $tag;
            if (strpos($tag, '<') !== false) {
                $this->breakJs();
            }

            $pos = $start;
            $end = strpos($buffer, '>', $start + 1);
            if ($end === false) {
                break;
            }
            $tagName = strtolower($matches[1][0]);
            $tagName_uc = strtoupper($tagName);
            if (strncmp($tagName, '!--', 3) === 0) {
                $tag = $tagName;
                $end = $start + 3;
            } else {
                $tag = substr($buffer, $start, $end - $start + 1);
            }

            $attributes = array();

            $attr_pos = $start + strlen($tagName) + 1;
            if ($attr_pos < $end && $tagName[0] !== '!') {
                $attributes = $this->parseAttributes(substr($buffer, $attr_pos, $end - $attr_pos));

                if (isset($attributes['ress-safe'])) {
                    $this->dom[] = preg_replace('#\s+ress-safe\b#i', '', $tag);
                    $pos = $end + 1;
                    continue;
                }
            }

            $node->tagName = $tagName;
            $node->attributes =& $attributes;
            $node->prepend = '';
            $node->tag = $tag;
            $node->content = false;
            $node->append = '';

            if (isset(self::$block_end[$tagName])) {
                $block_end = strpos($buffer, self::$block_end[$tagName], $end + 1);
                if ($block_end === false) {
                    break;
                }
                $node->content = substr($buffer, $end + 1, $block_end - $end - 1);
                $block_end += strlen(self::$block_end[$tagName]) - 1;
            } else {
                $block_end = $end;
            }

            $this->dispatcher->triggerEvent('HtmlIterateTag' . $tagName_uc . 'Before', array($this, $node));
            if ($node->tag === null) {
                $pos = $end + 1;
                continue;
            }

            // @todo check ress-media attribute
            switch ($tagName) {
                case '!--[if': // IE conds
                    // @todo don't remove non-comment <!--[if !IE]>-->HTML<!--<![endif]-->
                    $remove = false;
                    if ($this->config->html->removeiecond) {
                        $vendor = $this->di->deviceDetector->vendor();
                        // if IE browser
                        $remove = ($vendor !== 'ms' && $vendor !== 'unknown');
                    }
                    if (!$remove) {
                        $this->dom[] = substr($buffer, $start, $block_end - $start + 1);
                        $this->breakCss();
                        $this->breakJs();
                    }
                    $node->tag = null;
                    // @todo: parse as html and compress internals
                    break;

                case '![if': // IE conds
                    $remove = false;
                    if ($this->config->html->removeiecond) {
                        $vendor = $this->di->deviceDetector->vendor();
                        // if IE browser
                        $remove = ($vendor !== 'ms' && $vendor !== 'unknown');
                    }
                    if ($remove) {
                        $this->dom[] = substr($buffer, $end + 1, $block_end - $end - 10);

                    } else {
                        $this->dom[] = substr($buffer, $start, $block_end - $start + 1);
                        $this->breakCss();
                        $this->breakJs();
                    }
                    $node->tag = null;
                    // @todo: parse as html and compress internals
                    break;

                case '!--':
                    // remove comments
                    if (!$this->config->html->removecomments || $block_end - $start <= 6 || $buffer[$start + 4] === '!') {
                        $this->dom[] = substr($buffer, $start, $block_end - $start + 1);
                    }
                    $node->tag = null;
                    break;

                case '!doctype':
                    if (strpos($node->tag, 'DTD HTML')) {
                        $this->doctype = self::DOCTYPE_HTML4;
                    } elseif (strpos($node->tag, 'DTD XHTML')) {
                        $this->doctype = self::DOCTYPE_XHTML;
                    } else {
                        $this->doctype = self::DOCTYPE_HTML5;
                    }
                    $this->dom[] = $node->prepend;
                    $this->dom[] = $node->tag;
                    $this->dom[] = $node->append;
                    $node->tag = null;
                    break;

                case 'base':
                    // save base href (use first tag only)
                    if (!$this->baseFound && isset($attributes['href'])) {
                        $base = $attributes['href'];
                        if (substr($base, -1) !== '/') {
                            $base = dirname($base);
                            if ($base === '.') {
                                $base = '';
                            }
                            $base .= '/';
                        }
                        $this->urlRewriter->setBase($base);
                        $attributes['href'] = $this->urlRewriter->getBase();
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
                    $this->dom[] = $node->prepend;
                    $this->dom[] = $node->tag;
                    $this->dom[] = $node->append;
                    $node->tag = null;
                    break;

                case '/body':
                case '/head':
                    // empty script and style nodes
                    end($this->dom);
                    $current = current($this->dom);
                    while (key($this->dom) !== null && ($current === null || (is_string($current) && trim($current) === '') || $current instanceof $this->classNodeJsList)) {
                        $current = prev($this->dom);
                    }

                    if (!($current instanceof $this->classNodeCssList)) {
                        $this->dom[] = new $this->classNodeCssList($this->di);
                    }

                    /** @var Ressio_HtmlOptimizer_Pharse_JSList $jsNode */
                    if ($this->lastAsyncJsNode !== null) {
                        // move async scripts to the end
                        $jsNode = $this->lastAsyncJsNode;
                        $index = $this->lastJsNodeIndex;
                        unset($this->dom[$index]);
                    } else {
                        $jsNode = new $this->classNodeJsList($this->di);
                        $this->lastJsNode = $this->lastAsyncJsNode = $jsNode;
                    }
                    $this->dom[] = $jsNode;
                    end($this->dom);
                    $this->lastJsNodeIndex = key($this->dom);

                    $this->dom[] = $node->prepend;
                    $this->dom[] = $node->tag;
                    $this->dom[] = $node->append;
                    $node->tag = null;
                    break;

                case 'img':
                    if ($this->noscriptCounter) {
                        break;
                    }
                    if ($this->config->img->minify && isset($attributes['src'])) {
                        $src = $attributes['src'];
                        if ($src !== '' && strncmp($src, 'data:', 5) !== 0) {
                            $src_file = $this->urlRewriter->urlToFilepath($src);
                            if ($src_file !== null) {
                                $this->di->imgOptimizer->run($src_file);
                            }
                        }
                    }
                    if (($this->config->img->minify || $this->config->html->urlminify) && isset($attributes['srcset'])) {
                        $srcset = $attributes['srcset'];
                        $srclist = explode(',', $srcset);
                        foreach ($srclist as &$srcitem) {
                            $srcitem = trim($srcitem);
                            $pair = preg_split('/\s+/', $srcitem, 2, PREG_SPLIT_NO_EMPTY);
                            if (count($pair) > 1) {
                                list($src, $params) = $pair;
                            } else {
                                $src = $srcitem;
                                $params = '';
                            }
                            if (strncmp($src, 'data:', 5) !== 0) {
                                if ($this->config->img->minify) {
                                    $src_file = $this->urlRewriter->urlToFilepath($src);
                                    if ($src_file !== null) {
                                        $this->di->imgOptimizer->run($src_file);
                                    }
                                }
                                if ($this->config->html->urlminify) {
                                    $src = $this->urlRewriter->minify($src);
                                    $srcitem = rtrim("$src $params");
                                }
                            }
                        }
                        unset($srcitem);
                        $attributes['srcset'] = implode(',', $srclist);
                    }
                    break;

                case 'link':
                    // break if there attributes other than type=text/css, rel=stylesheet, href
                    if (!isset($attributes['rel'], $attributes['href']) || $attributes['rel'] !== 'stylesheet') {
                        break;
                    }
                    if ($this->noscriptCounter) {
                        break;
                    }

                    if (isset($attributes['onload'])) {
                        $this->breakCss();
                        break;
                    }
                    if ($this->config->css->checklinkattributes) {
                        $attributes_copy = $attributes;
                        if (isset($attributes_copy['type']) && $attributes_copy['type'] === 'text/css') {
                            unset($attributes_copy['type']);
                        }
                        unset($attributes_copy['rel'], $attributes_copy['media'], $attributes_copy['href'],
                            $attributes_copy['ress-merge'], $attributes_copy['ress-nomerge']);
                        if (count($attributes_copy) > 0) {
                            if (!preg_match('#^(https?:)?//fonts\.googleapis\.com/css#', $attributes['href'])) {
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
                    if ($this->doctype !== self::DOCTYPE_HTML5 && !isset($attributes['type'])) {
                        $attributes['type'] = 'text/css';
                    }

                    if (isset($attributes['ress-nomerge'])) {
                        unset($attributes['ress-nomerge']);
                        $merge = false;
                    } else {
                        // minify css file (for external: breakpoint/load/@import)
                        $merge = $this->config->css->merge;
                        if ($merge) {
                            $src = $attributes['href'];
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
                        $this->addCss($this->dom, $attributes, null);
                        $node->tag = null;
                    } else {
                        if (!preg_match('#^(https?:)?//fonts\.googleapis\.com/css#', $attributes['href'])) {
                            $this->breakCss();
                        }
                    }
                    break;

                case 'style':
                    if ($this->noscriptCounter) {
                        break;
                    }
                    if ($this->config->css->checkstyleattributes) {
                        $attributes_copy = $attributes;
                        // break if there attributes other than type=text/css
                        if (isset($attributes_copy['type']) && $attributes_copy['type'] === 'text/css') {
                            unset($attributes_copy['type']);
                        }
                        unset($attributes_copy['media'],
                            $attributes_copy['ress-merge'], $attributes_copy['ress-nomerge']);
                        if (count($attributes_copy) > 0) {
                            $this->breakCss();
                            break;
                        }
                    } else {
                        if (isset($attributes['type']) && $attributes['type'] !== 'text/css') {
                            break;
                        }
                    }

                    // set type=text/css in html4 and remove in html5
                    if ($this->doctype !== self::DOCTYPE_HTML5 && !isset($attributes['type'])) {
                        $attributes['type'] = 'text/css';
                    }
                    // remove media attribute if it is empty or "all"
                    if (isset($attributes['media']) && $this->config->html->removedefattr) {
                        $media = $attributes['media'];
                        // @todo: parse media
//                      $media = $this->filterMedia($media);
                        if ($media === '' || $media === 'all') {
                            unset($attributes['media']);
                        }
                    }
                    // css break point if scoped=... attribute
                    if (isset($attributes['scoped'])) {
                        $this->breakCss();
                    }

                    // @todo: check type

                    if (isset($attributes['ress-nomerge'])) {
                        unset($attributes['ress-nomerge']);
                        $merge = false;
                    } elseif (isset($attributes['ress-merge'])) {
                        unset($attributes['ress-merge']);
                        $merge = true;
                    } else {
                        $merge =
                            is_bool($this->config->css->mergeinline)
                                ? $this->config->css->mergeinline
                                : $this->headMode;
                    }

                    if ($merge) {
                        $this->addCss($this->dom, $attributes, $node->content);
                        $node->tag = null;
                    } else {
                        $this->breakCss();
                    }

                    break;

                case 'noscript':
                    $this->noscriptCounter++;
                    // @todo merge subsequent <noscript>
                    // @todo remove scripts in noscript
                    break;

                case '/noscript':
                    $this->noscriptCounter--;
                    break;

                case 'script':
                    if ($this->noscriptCounter) {
                        $node->tag = null;
                        break;
                    }
                    if (isset($attributes['ress-noasync'])) {
                        unset($attributes['ress-noasync']);
                        $autoasync = false;
                    } else {
                        $autoasync = $this->config->js->autoasync;
                    }

                    if (
                        isset($attributes['onload']) ||
                        (isset($attributes['data-cfasync']) && $attributes['data-cfasync'] === 'false')
                    ) {
                        $this->breakJs(true);
                        break;
                    }

                    if ($this->config->js->forceasync) {
                        $attributes['async'] = false;
                    }
                    if ($this->config->js->forcedefer) {
                        $attributes['defer'] = false;
                    }

                    // break if there attributes other than type=text/javascript, defer, async
                    if (count($attributes)) {
                        if ($this->config->js->checkattributes) {
                            $attributes_copy = $attributes;
                            // @todo support language="javascript" attribute
                            if (isset($attributes_copy['type']) && $attributes_copy['type'] === 'text/javascript') {
                                unset($attributes_copy['type']);
                                if ($this->config->html->removedefattr) {
                                    unset($attributes['type']);
                                }
                            }
                            if (isset($attributes_copy['language']) && strcasecmp($attributes_copy['language'], 'javascript') === 0) {
                                unset($attributes_copy['language']);
                                if ($this->config->html->removedefattr) {
                                    unset($attributes['language']);
                                }
                            }
                            unset($attributes_copy['defer'], $attributes_copy['async'], $attributes_copy['src'],
                                $attributes_copy['ress-merge'], $attributes_copy['ress-nomerge']);
                            if (count($attributes_copy) > 0) {
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
                    if ($this->doctype !== self::DOCTYPE_HTML5 && !isset($attributes['type'])) {
                        $attributes['type'] = 'text/javascript';
                    }

                    if (!isset($attributes['src'])) { // inline
                        $scriptBlob = $node->content;
                        // @todo: refactor clear comments
                        $scriptBlob = preg_replace(array('#^\s*<!--.*?[\r\n]+#', '#//\s*<!--.*$#m', '#//\s*-->.*$#m', '#\s*-->\s*$#'), '', $scriptBlob);
                        $scriptBlob = preg_replace('#^\s*(?://\s*)?<!\[CDATA\[\s*(.*?)\s*(?://\s*)?\]\]>\s*$#', '\1', $scriptBlob);
                        $scriptBlob = preg_replace('#^\s*/\*\s*<!\[CDATA\[\s*\*/\s*(.*?)\s*/\*\s*\]\]>\s*\*/\s*$#', '\1', $scriptBlob);

                        $node->content = $scriptBlob;

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

                        if (isset($attributes['ress-nomerge'])) {
                            unset($attributes['ress-nomerge']);
                            $merge = false;
                        } elseif (isset($attributes['ress-merge'])) {
                            unset($attributes['ress-merge']);
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
                            $this->addJs($this->dom, $attributes, $node->content, false, $autoasync);
                            $node->tag = null;
                        } else {
                            $this->breakJs(true);
                        }
                    } else { // external
                        if (isset($attributes['ress-nomerge'])) {
                            unset($attributes['ress-nomerge']);
                            $merge = false;
                        } elseif (isset($attributes['ress-merge'])) {
                            unset($attributes['ress-merge']);
                            $merge = true;
                        } else {
                            $merge = $this->config->js->merge;
                        }

                        if ($merge) {
                            $src = $attributes['src'];
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
                            $this->addJs($this->dom, $attributes, null, false, $autoasync);
                            $node->tag = null;
                        } else {
                            $this->breakJs($this->config->js->autoasync);
                        }
                    }

                    break;
            }

            if ($node->tag !== null) {
                $this->dispatcher->triggerEvent('HtmlIterateTag' . $tagName_uc, array($this, $node));
            }

            if (isset($node->attributes['onload']) || isset($node->attributes['onerror'])) {
                $this->breakJs(true);
            }

            // minify uri in attributes
            if ($this->config->html->urlminify && isset($this->uriAttrs[$tagName]) &&
                !($node->tag === 'link' && isset($node->attributes['rel']) && $node->attributes['rel'] !== 'stylesheet')
            ) {
                foreach ($this->uriAttrs[$tagName] as $attrName) {
                    if (isset($node->attributes[$attrName])) {
                        $uri = $node->attributes[$attrName];
                        if ($uri !== '' && strncmp($uri, 'data:', 5) !== 0) {
                            $node->attributes[$attrName] = $this->urlRewriter->minify($uri);
                        }
                    }
                }
            }

            if ($tagName[0] === '/' || isset($this->tags_selfclose[$tagName])) {
                $this->dispatcher->triggerEvent('HtmlIterateTag' . ltrim($tagName_uc, '/') . 'After', array($this, $node));
            }

            if ($node->tag !== null) {
                $this->dom[] = $node->prepend;
                $this->dom[] = $this->tagToString($tagName, $node->attributes, $node->content);
                $this->dom[] = $node->append;
            }

            if ($tagName !== 'script') {
                $this->breakJs();
            }
            $pos = $block_end + 1;
        }

        $last_piece = substr($buffer, $pos);
        if ($last_piece !== false) {
            $this->dom[] = $last_piece;
        }
    }

    /**
     * @param $html array
     * @param $attributes array
     * @param $blob string|null
     * @param $append bool
     * @param $autoasync bool
     */
    private function addJs(&$html, $attributes, $blob, $append = false, $autoasync = false)
    {
        $inline = ($blob !== null);
        $src = $inline ? $blob : $attributes['src'];
        $async = isset($attributes['async']);
        $defer = isset($attributes['defer']);

        // @todo: take into account difference between async and defer

        $jsAsync = $append || $async || $defer || $autoasync;

        if ($this->lastJsNode !== null) {
            $jsNode = $this->lastJsNode;
        } elseif ($this->lastAsyncJsNode !== null) {
            $jsNode = $this->lastAsyncJsNode;
            if (!$append) {
                $index = $this->lastJsNodeIndex;
                unset($html[$index]);
                $html[] = $jsNode;
                end($html);
                $this->lastJsNodeIndex = key($html);
            }
        } else {
            /** @var Ressio_HtmlOptimizer_Pharse_JSList $jsNode */
            $jsNode = new $this->classNodeJsList($this->di);
            $this->lastJsNode = $this->lastAsyncJsNode = $jsNode;
            $html[] = $jsNode;
            end($html);
            $this->lastJsNodeIndex = key($html);
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
     * @param $html array
     * @param $attributes array
     * @param $blob string|null
     */
    private function addCss(&$html, $attributes, $blob)
    {
        $inline = ($blob !== null);
        $src = $inline ? $blob : $attributes['href'];

        $media = isset($attributes['media']) ? $attributes['media'] : 'all';

        if ($this->lastCssNode === null) {
            $html[] = $this->lastCssNode = new $this->classNodeCssList($this->di);
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
     * @param string $tagName
     * @param string[string] $attributes
     * @param string|null $content
     * @return string
     */
    public function tagToString($tagName, $attributes, $content)
    {
        if ($tagName === '!--') {
            return '<!--' . $content . '-->';
        }
        $out = '<' . $tagName;
        if (is_array($attributes)) {
            foreach ($attributes as $key => $value) {
                $out .= ' ' . $key;
                if ($value !== false) {
                    $out .= '=' . (strpos($value, '"') === false ? '"' . $value . '"' : "'" . $value . "'");
                }
            }
        }
        if ($content === false && $this->doctype === self::DOCTYPE_XHTML) {
            $out .= '/';
        }
        $out .= '>';
        if ($content !== false) {
            if ($content !== null) {
                $out .= $content;
            }
            $out .= '</' . $tagName . '>';
        }
        return $out;
    }

    /**
     * @param $str
     * @return array
     */
    public function parseAttributes($str)
    {
        preg_match_all('#\s+([a-z0-9_\-]+)(?:\s*=\s*("[^"]*"|\'[^\']*\'|[^"\'\s]+))?#i', $str, $matches, PREG_SET_ORDER);
        $attributes = array();
        foreach ($matches as $match) {
            if (isset($match[2])) {
                $attributes[$match[1]] = trim($match[2], '"\'');
            } else {
                $attributes[$match[1]] = false;
            }
        }
        return $attributes;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     * @return string
     */
    public function nodeToString($node)
    {
        return $this->tagToString($node->tagName, $node->attributes, $node->content);
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     */
    public function nodeDetach(&$node)
    {
        $node->tag = null;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     * @return bool
     */
    public function nodeIsDetached($node)
    {
        return $node->tag === null;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     * @param $text string
     */
    public function nodeSetInnerText(&$node, $text)
    {
        $node->content = $text;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     * @return string
     */
    public function nodeGetInnerText(&$node)
    {
        return $node->content;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     * @param $tag string
     * @param $attribs array
     */
    public function nodeWrap(&$node, $tag, $attribs = null)
    {
        $node->prepend .= '<' . $tag;
        if ($attribs !== null) {
            /** @var $attribs array */
            foreach ($attribs as $key => $value) {
                $node->prepend .= ' ' . $key;
                if ($value !== false) {
                    $node->prepend .= '=' . (strpos($value, '"') === false ? '"' . $value . '"' : "'" . $value . "'");
                }
            }
        }
        $node->prepend .= '>';

        $node->append = '</' . $tag . '>' . $node->append;
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertBefore(&$node, $tag, $attribs = null, $content = null)
    {
        $node->prepend .= $this->tagToString($tag, $attribs, $content);
    }

    /**
     * @param $node Ressio_HtmlOptimizer_Stream_NodeWrapper
     * @param $tag string
     * @param $attribs array
     * @param $content string
     */
    public function nodeInsertAfter(&$node, $tag, $attribs = null, $content = null)
    {
        $node->append = $this->tagToString($tag, $attribs, $content) . $node->append;
    }

    /**
     * @param $nodedata,... array (string $tag, array $attribs, string $content)
     * @return bool return false if no <head> found
     */
    public function prependHead($nodedata)
    {
        $html = '';
        foreach (func_get_args() as $newNode) {
            list($tag, $attribs, $content) = $newNode;
            $html .= $this->tagToString($tag, $attribs, $content);
        }
        foreach ($this->dom as &$node) {
            if (is_string($node) && strncmp($node, '<head', 5) === 0) {
                // @todo prepend next element instead of append to <head>
                $node .= $html;
                return true;
            }
        }
        return false;
    }

    /**
     * @param $nodedata array (string $tag, array $attribs, string $content)
     * @return bool return false if no <link rel=stylesheet>, <style>, <script>, or combining wrappers
     */
    public function insertBeforeStyleScript($nodedata)
    {
        $i = 0;
        array_splice($this->dom, 0, 0);
        foreach ($this->dom as $value) {
            $isLink = false;
            $isStyle = false;
            if (is_string($value)) {
                if (preg_match('#^<link\s#i', $value)) {
                    $startPos = strpos($value, ' ');
                    $endPos = strpos($value, '>');
                    $attributes = $this->parseAttributes(substr($value, $startPos, $endPos - $startPos));
                    $isLink = isset($attributes['rel']) && ($attributes['rel'] === 'stylesheet' || $attributes['rel'] === 'ress-css');
                }
                $isStyle = preg_match('#^<style[\s>]#i', $value);
            }
            $isCss = $isLink || $isStyle || $value instanceof $this->classNodeCssList;

            if ($isCss || $value instanceof $this->classNodeJsList) {
                $html = '';
                foreach (func_get_args() as $node) {
                    list($tag, $attribs, $content) = $node;
                    $html .= $this->tagToString($tag, $attribs, $content);
                }

                array_splice($this->dom, $i, 0, $html);
                return true;
            }

            $i++;
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
