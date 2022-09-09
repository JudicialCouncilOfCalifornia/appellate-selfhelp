<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_CssCombiner implements IRessio_CssCombiner
{
    /** @var Ressio_DI */
    private $di;
    /** @var Ressio_Config */
    private $config;

    /**
     * @param $di Ressio_DI
     * @throws ERessio_UnknownDiKey
     */
    public function setDI($di)
    {
        $this->di = $di;
        $this->config = $di->config;
    }

    /**
     * @param $styleList array
     * @param $self_close_str string
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function combineToHtml($styleList, $self_close_str = '')
    {
        /** @var string[] $deps */
        $deps = array(
            'css',
            get_class($this->di->cssMinify), // @todo move to combiner
            $this->di->deviceDetector->vendor(),
            $this->config->css->inlinelimit
        );
        // add file's timestamp to hash
        foreach ($styleList as &$item) {
            if ($item['type'] !== 'inline') {
                $filename = $this->di->urlRewriter->urlToFilepath($item['src']);
                if ($filename !== null) {
                    $item['filename'] = $filename;
                    $item['time'] = $this->di->filesystem->getModificationTime($filename);
                }
            }
            $deps[] = json_encode($item);
        }
        unset($item);

        $cache = $this->di->cache;
        $cache_id = $cache->id($deps, 'htmlcss');
        $result = $cache->getOrLock($cache_id);
        if (!is_string($result)) {
            $s = $this->_combineToHtml($styleList, $self_close_str);
            if ($result) {
                $cache->storeAndUnlock($cache_id, $s);
            }
            $result = $s;
        }

        $wrapper = new stdClass;
        $wrapper->content = $result;
        $this->di->dispatcher->triggerEvent('CssCombinerAfter', array($wrapper));
        return $wrapper->content;
    }

    /**
     * @param $styleList array
     * @param $self_close_str string
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    protected function _combineToHtml($styleList, $self_close_str)
    {
        $urlRewriter = $this->di->urlRewriter;

        switch ($this->config->fileloader) {
            case 'php':
                $targetDir = dirname($this->config->fileloaderphppath) . '/';
                break;
            case 'file':
            default:
                $targetDir = $this->config->webrootpath . $this->config->staticdir . '/';
        }

        $css = $this->combine($styleList, $urlRewriter->filepathToUrl($targetDir));

        $s = '';
        foreach ($css as $item) {
            if (isset($item['error'])) {
                if (isset($item['content'])) {
                    $s .= '<style' . $item['attr'] . '>' . $item['content'] . '</style>';
                } else {
                    $q = (strpos($item['src'], '"') === false) ? '"' : "'";
                    $s .= '<link rel="stylesheet" href=' . $q . $item['src'] . $q
                        . $item['attr'] . $self_close_str . '>';
                }
            } else {
                $content = $item['content'];
                if (strlen($content) <= $this->config->css->inlinelimit) {
                    $s .= '<style' . $item['attr'] . '>' . $content . '</style>';
                } else {
                    $hash = substr($item['hash'], 0, $this->config->filehashsize);

                    $cacheFile = $this->config->webrootpath . $this->config->staticdir . '/' . $hash . '.css';
                    $fs = $this->di->filesystem;
                    $fs->putContents($cacheFile, $content);
                    $fs->putContents($cacheFile . '.gz', gzencode($content, 9));

                    switch ($this->config->fileloader) {
                        case 'php':
                            $cacheFile = $this->config->fileloaderphppath . '?' . $hash . '.css';
                    }
                    // DO NOT minify URL (because of caching)
                    $s .= '<link rel="stylesheet" href="' . $urlRewriter->filepathToUrl($cacheFile) . '"'
                        . $item['attr'] . $self_close_str . '>';
                }
            }
        }

        return $s;
    }

    /**
     * @param $styleList array
     * @param $targetUrl string
     * @return array
     * @throws ERessio_UnknownDiKey
     */
    public function combine($styleList, $targetUrl)
    {
        $urlRewriter = $this->di->urlRewriter;
        $dispatcher = $this->di->dispatcher;
        $minifyCss = $this->di->cssMinify;

        $dispatcher->triggerEvent('CssCombineBefore', array(&$styleList, &$targetUrl));

        $css = array();
        $base = $urlRewriter->getBase();
        $targetBase = dirname($targetUrl . 'x');
        if ($targetBase === '\\') {
            $targetBase = '/';
        }
        $regex = $this->config->css->excludeminifyregex;

        $hash_prefix = get_class($minifyCss);
        $item_hash = $hash_prefix;
        $item_content = '';

        $i = 0;
        foreach ($styleList as $item) {
            $i++;
            if ($item['type'] === 'inline') {
                $content = $item['style'];
                $media = $item['media'];
                if (!in_array($media, array('', 'all'), true)) {
                    $content = '@media ' . $media . '{' . $content . '}';
                }
                try {
                    $dispatcher->triggerEvent('CssInlineMinifyBefore', array(&$content));
                    $minified = $minifyCss->minify($content, $base, $targetBase);
                    $dispatcher->triggerEvent('CssInlineMinifyAfter', array(&$minified));
                    if ($i>1 && strpos($minified, '@import') !== false) {
                        throw new ERessio_InvalidCss('Unprocessed @import rule in inline style');
                    }
                    $item_hash .= '|' . $content;
                    $item_content .= $minified;
                } catch (ERessio_InvalidCss $e) {
                    $i = 0;
                    $this->di->logger->warning('Catched error in Ressio_CssCombiner::combine: ' . $e->getMessage());
                    if ($item_content !== '') {
                        $css[] = array('hash' => sha1($item_hash), 'content' => $item_content, 'attr' => '');
                    }
                    $css[] = array('hash' => false, 'content' => $content, 'error' => true, 'attr' => ' media="' . $item['media'] . '"');
                    $item_hash = $hash_prefix;
                    $item_content = '';
                }
            } else {
                $fs = $this->di->filesystem;
                $src = $item['src'];
                $path = isset($item['filename']) ? $item['filename'] : $urlRewriter->urlToFilepath($src);
                if ($path === null || pathinfo($path, PATHINFO_EXTENSION) !== 'css') {
                    // external url
                    $path = null;
                } else {
                    // local css file
                    // @todo make check for .min.css optional
//                    if (!preg_match('/\.min\.css$/', $path)) {
//                        $path_min = substr($path, 0, -3) . 'min.css';
//                        if ($fs->isFile($path_min)) {
//                            $path = $path_min;
//                        }
//                    }
                }
                if ($path === null) {
                    $path = $urlRewriter->expand($src);
                }
                try {
                    $content = $fs->getContents($path);
                    if ($content === false) {
                        throw new ERessio_InvalidCss('File ' . $path . ' not found.');
                    }
                    if (strncmp($content, "\x1f\x8b", 2) === 0) {
                        $content = gzinflate(substr($content, 10, -8));
                    }
                    $media = $item['media'];
                    if (!in_array($media, array('', 'all'), true)) {
                        $content = '@media ' . $media . '{' . $content . '}';
                    }
                    if ($regex !== null && preg_match($regex, $src)) {
                        // @TODO rewrite urls
                        $minified = $content;
                    } else {
                        $dispatcher->triggerEvent('CssFileMinifyBefore', array($src, &$content, $targetBase));
                        $minified = $minifyCss->minify($content, dirname($src), $targetBase);
                        $dispatcher->triggerEvent('CssFileMinifyAfter', array($src, &$minified));
                    }
                    if ($i>1 && strpos($minified, '@import') !== false) {
                        throw new ERessio_InvalidCss('Unprocessed @import rule in link style');
                    }
                    $item_hash .= '|' . $content;
                    $item_content .= $minified;
                } catch (ERessio_InvalidCss $e) {
                    $i = 0;
                    $this->di->logger->warning('Catched error in Ressio_CssCombiner::combine: ' . $e->getMessage() . ' [in file: ' . $item['src'] . ']');
                    if ($item_content !== '') {
                        $css[] = array('hash' => sha1($item_hash), 'content' => $item_content, 'attr' => '');
                    }
                    $css[] = array('hash' => false, 'src' => $src, 'error' => true, 'attr' => ' media="' . $item['media'] . '"');
                    $item_hash = $hash_prefix;
                    $item_content = '';
                }
            }
        }

        if ($item_content !== '') {
            $css[] = array('hash' => sha1($item_hash), 'content' => $item_content, 'attr' => '');
        }

        $crossfileoptimization = $this->config->css->crossfileoptimization && count($styleList) > 1;
        foreach ($css as &$cssitem) {
            if (!isset($cssitem['error'])) {
                $content = $cssitem['content'];
                if ($crossfileoptimization) {
                    try {
                        $content = $minifyCss->minify($content, $targetBase, $targetBase);
                    } catch (ERessio_InvalidCss $e) {
                        $this->di->logger->warning('Catched error in Ressio_CssCombiner::combine: ' . $e->getMessage());
                    }
                }
                $dispatcher->triggerEvent('CssCombineAfter', array(&$content));
                $cssitem['content'] = $content;
            }
        }
        unset($cssitem);

        return $css;
    }
}