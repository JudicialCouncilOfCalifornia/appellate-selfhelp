<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_Lazyload extends Ressio_Plugin
{
    static public $blankImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
    static public $blankIframe = 'about:blank';

    private $numImages = 0;
    private $numIframes = 0;

    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        $params = $this->loadConfig(dirname(__FILE__) . '/config.json', $params);
        sort($params->srcsetwidth);

        parent::__construct($di, $params);
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagIMG($event, $optimizer, $node)
    {
        if ($this->params->image) {
            $this->numImages++;
            if ($this->numImages > $this->params->skipimages) {
                if ($this->params->srcset && $this->params->addsrcset && !$node->hasAttribute('srcset')) {
                    // @todo refactor: generation of srcset attribute is not responsibility of the lazy-loading plugin
                    $this->createSrcset($node);
                }
                $this->lazyfyNode($node, $optimizer);
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagVIDEO($event, $optimizer, $node)
    {
        if ($this->params->video) {
            if ($node->hasAttribute('ress-nolazy')) {
                $node->removeAttribute('ress-nolazy');
                return;
            }
            if ($optimizer->nodeIsDetached($node) || $optimizer->isNoscriptState()) {
                return;
            }

            $modified = false;

            if ($node->hasAttribute('src') && !$node->hasAttribute('data-src')) {
                $src = $node->getAttribute('src');
                if (strncmp($src, 'data:', 5) === 0 && !preg_match('/(?:__|\}\})$/', $src)) {
                    $modified = true;
                    $node->setAttribute('data-src', $src);
                    $node->removeAttribute('src');
                }
            }

            if ($node->hasAttribute('poster') && !$node->hasAttribute('data-poster')) {
                $src = $node->getAttribute('poster');
                if (strncmp($src, 'data:', 5) === 0 && !preg_match('/(?:__|\}\})$/', $src)) {
                    $modified = true;
                    $node->setAttribute('data-poster', $src);
                    $node->removeAttribute('poster');
                    // @todo Optimize poster image
                }
            }

            if ($modified) {
                // @todo Add <noscript> verswion
                $node->addClass('lazy');
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagIFRAME($event, $optimizer, $node)
    {
        // @todo Exclude list for iframes
        if ($this->params->video || $this->params->iframe) {
            $this->numIframes++;
            if ($this->numIframes > $this->params->skipiframes) {
                $this->lazyfyNode($node, $optimizer);
            }
        }
    }

    /**
     * @param $node IRessio_HtmlNode
     * @param $optimizer IRessio_HtmlOptimizer
     */
    private function lazyfyNode($node, $optimizer)
    {
        if ($node->hasAttribute('ress-nolazy')) {
            $node->removeAttribute('ress-nolazy');
            return;
        }

        if ($node->hasAttribute('onload') || $node->hasAttribute('onerror')) {
            return;
        }

        if ($optimizer->nodeIsDetached($node) || $optimizer->isNoscriptState() ||
            !$node->hasAttribute('src') ||
            strncmp($node->getAttribute('src'), 'data:', 5) === 0
        ) {
            return;
        }

        if ($node->hasAttribute('width') && $node->hasAttribute('height')
            && $node->getAttribute('width') === '1' && $node->getAttribute('height') === '1') {
            return;
        }

        // skip data attributes (sliders, etc.)
        if ($node instanceof DOMElement) {
            if ($node->hasAttributes()) {
                foreach ($node->attributes as $attr) {
                    if (strncmp($attr->nodeName, 'data-', 5) === 0) {
                        return;
                    }
                }
            }
        } else {
            if (count($node->attributes)) {
                foreach ($node->attributes as $name => $value) {
                    if (strncmp($name, 'data-', 5) === 0) {
                        return;
                    }
                }
            }
        }

        $src = $node->getAttribute('src');
        if (preg_match('/(?:__|\}\})$/', $src)) {
            // template-like URL: __dummy__ or {{dummy}}
            return;
        }

        switch ($this->params->noscriptpos) {
            case 'none':
                break;
            case 'before':
                $optimizer->nodeInsertBefore($node, 'noscript', null, $optimizer->nodeToString($node));
                break;
            case 'after':
                $optimizer->nodeInsertAfter($node, 'noscript', null, $optimizer->nodeToString($node));
                break;
        }

        $node->addClass('lazy');

        if ($node->getTag() === 'img') {
            // @todo Make set width/height optional
            if (!$node->hasAttribute('width') && !$node->hasAttribute('height')) {
                $src_imagepath = $this->di->urlRewriter->urlToFilepath($src);
                if ($this->di->filesystem->isFile($src_imagepath)) {
                    $src_imagesize = getimagesize($src_imagepath);
                    if ($src_imagesize !== false) {
                        list($src_width, $src_height) = $src_imagesize;
                        $node->setAttribute('width', $src_width);
                        $node->setAttribute('height', $src_height);
                    }
                }
            }
            $node->setAttribute('src', $this->params->lqip ? $this->getLQIP($src) : self::$blankImage);
            if ($this->params->srcset && $node->hasAttribute('srcset')) {
                $node->setAttribute('data-srcset', $node->getAttribute('srcset') . ', ' . $src);
                $node->removeAttribute('srcset');
            } else {
                $node->setAttribute('data-src', $src);
            }
        } else {
            $node->setAttribute('src', self::$blankIframe);
            $node->setAttribute('data-src', $src);
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     */
    public function onHtmlIterateTagBODYAfter($event, $optimizer, $node)
    {
        static $async = array('async' => true, 'defer' => true);
        $suffix = $this->params->debug ? '.js' : '.min.js';

        // @todo get loadPolyfill from config
        if ($this->config->html->removeiecond) {
            $vendor = $this->di->deviceDetector->vendor();
            $loadPolyfill = ($vendor === 'ms' || $vendor === 'unknown');
        } else {
            $loadPolyfill = true;
        }
        if ($loadPolyfill) {
            // add IE8 polyfill from //cdnjs.cloudflare.com/ajax/libs/ie8/0.6.0/ie8.js
            $optimizer->prependHead(array('!--', null, '[if IE 8]><script src="//cdnjs.cloudflare.com/ajax/libs/ie8/0.6.0/ie8.js"></script><![endif]'));
        }

        $optimizer->appendScriptDeclaration(file_get_contents(RESSIO_PATH . '/classes/plugin/lazyload/js/lazyloadxt' . $suffix), $async);
        if ($this->params->edgey > 0) {
            $optimizer->appendScriptDeclaration('lazyLoadXT.edgeY=' . (int)$this->params->edgey . ';', $async);
        }
        $optimizer->appendStyleDeclaration('img.lazy,iframe.lazy{display:none}');

        $addons = (isset($this->params->addons) && is_array($this->params->addons)) ? $this->params->addons : array();
        if (($this->params->video || $this->params->iframe) && !in_array('video', $addons, true)) {
            $addons[] = 'video';
        }
        if ($this->params->srcset && !in_array('srcset', $addons, true)) {
            $addons[] = 'srcset';
        }
        foreach ($addons as $addon) {
            $optimizer->appendScriptDeclaration(file_get_contents(RESSIO_PATH . '/classes/plugin/lazyload/js/lazyloadxt.' . $addon . $suffix), $async);
        }
    }

    /**
     * @param $node IRessio_HtmlNode
     */
    protected function createSrcset($node)
    {
        /** @var Ressio_UrlRewriter $urlRewriter */
        $urlRewriter = $this->di->urlRewriter;

        $src = $node->getAttribute('src');
        $src_imagepath = $urlRewriter->urlToFilepath($src);
        if ($src_imagepath === null || !$this->di->filesystem->isFile($src_imagepath)) {
            return;
        }

        /** @var IRessio_imgRescale $imgRescaler */
        $imgRescaler = $this->di->imgRescaler;

        $src_ext = strtolower(pathinfo($src_imagepath, PATHINFO_EXTENSION));
        if ($src_ext === 'jpeg') {
            $src_ext = 'jpg';
        }
        if (!in_array($src_ext, $imgRescaler->getSupportedExts(), true)) {
            return;
        }

        $size = getimagesize($src_imagepath);
        if ($size === false) {
            return;
        }

        list($src_width, $src_height) = $size;

        /** @var array $required */
        $required = $this->params->srcsetwidth;
        if ($src_width <= $required[0] || $src_height <= 0) {
            return;
        }

        $srcset = array();
        $widths = array();

        foreach (array_reverse($required) as $new_width) {
            if ($new_width > $src_width) {
                continue;
            }
            if ($new_width === $src_width) {
                $widths[$new_width] = $src_imagepath;
                continue;
            }

            $new_height = round($src_height * $new_width / $src_width);
            if ($new_height === 0) {
                $new_height = 1;
            }

            // @todo How to access plugin's protected method? make it public???
            $dest_imagepath = $this->getRescaledPath($src_imagepath, $new_width, $new_height, $src_ext);
            $widths[$new_width] = $dest_imagepath;

            if (isset($widths[2 * $new_width])) {
                $imagepath = $widths[2 * $new_width];
            } elseif (isset($widths[3 * $new_width])) {
                $imagepath = $widths[3 * $new_width];
            } else {
                $imagepath = $src_imagepath;
            }
            $dest_imagepath = $imgRescaler->rescale($imagepath, $dest_imagepath, $new_width, $new_height, $src_ext);

            $new_imageuri = $urlRewriter->filepathToUrl($dest_imagepath);
            $srcset[] = $new_imageuri  . ' ' . $new_width . 'w';
        }
        $srcset = array_reverse($srcset);
        $srcset[] = $urlRewriter->filepathToUrl($src_imagepath) . ' ' . $src_width . 'w';

        // @todo extract directory into srcset-base and extension into srcset-ext
        $node->setAttribute('srcset', implode(',', $srcset));
    }

    /**
     * @param $src_url string
     * @return string
     */
    public function getLQIP($src_url)
    {
        $rescaler = $this->di->imgRescaler;

        $src_ext = strtolower(pathinfo($src_url, PATHINFO_EXTENSION));
        if ($src_ext === 'jpeg') {
            $src_ext = 'jpg';
        }

        if (in_array($src_ext, $rescaler->getSupportedExts(), true)) {
            $urlRewriter = $this->di->urlRewriter;

            $src_imagepath = $urlRewriter->urlToFilepath($src_url);
            if ($this->di->filesystem->isFile($src_imagepath)) {
                $src_imagesize = getimagesize($src_imagepath);
                if ($src_imagesize !== false) {
                    list($src_width, $src_height) = $src_imagesize;
                    $dest_imagepath = $this->getLQIPPath($src_imagepath);

                    $jpegquality = $this->config->img->jpegquality;
                    $this->config->img->jpegquality = 0;
                    $dest_imagepath = $rescaler->rescale($src_imagepath, $dest_imagepath, $src_width, $src_height, 'jpg');
                    $this->config->img->jpegquality = $jpegquality;

                    return $urlRewriter->filepathToUrl($dest_imagepath);
                }
            }
        }

        return self::$blankImage;
    }

    /**
     * @param $src_imagepath string
     * @return string
     */
    public function getLQIPPath($src_imagepath)
    {
        if (defined('PATHINFO_FILENAME')) {
            $src_imagename = pathinfo($src_imagepath, PATHINFO_FILENAME);
        } else {
            $base = basename($src_imagepath);
            $src_imagename = substr($base, 0, strrpos($base, '.'));
        }

        // @todo: move cache dir to settings
        $dest_imagedir = dirname($src_imagepath);
        if (basename($dest_imagedir) !== 'imgcache') {
            $dest_imagedir .= '/imgcache';
        }

        return $dest_imagedir . '/' . $src_imagename . '_lqip.jpg';
    }

    /**
     * @param $src_imagepath string
     * @param $dest_width int
     * @param $dest_height int
     * @param $dest_ext string
     * @return string
     */
    public function getRescaledPath($src_imagepath, $dest_width, $dest_height, $dest_ext)
    {
        if (defined('PATHINFO_FILENAME')) {
            $src_imagename = pathinfo($src_imagepath, PATHINFO_FILENAME);
        } else {
            $base = basename($src_imagepath);
            $src_imagename = substr($base, 0, strrpos($base, '.'));
        }

        // @todo: move cache dir to settings
        $dest_imagedir = dirname($src_imagepath) . '/imgcache';

        return $dest_imagedir . '/' . $src_imagename . '_' . $dest_width . 'x' . $dest_height . '.' . $dest_ext;
    }
}