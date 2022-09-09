<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_JsCombiner implements IRessio_JsCombiner
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
     * Returns the node as string
     * @param array $scriptList
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function combineToHtml($scriptList)
    {
        /** @var string[] $deps */
        $deps = array(
            'js',
            get_class($this->di->jsMinify), // @todo move to combiner
            $this->config->js->inlinelimit
        );
        // add file's timestamp to hash
        foreach ($scriptList as &$item) {
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
        $cache_id = $cache->id($deps, 'htmljs');
        $result = $cache->getOrLock($cache_id);
        if (!is_string($result)) {
            $s = $this->_combineToHtml($scriptList);
            if ($result) {
                $cache->storeAndUnlock($cache_id, $s);
            }
            $result = $s;
        }

        $wrapper = new stdClass;
        $wrapper->content = $result;
        $this->di->dispatcher->triggerEvent('JsCombinerAfter', array($wrapper));
        return $wrapper->content;
    }

    /**
     * @param array $scriptList
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    protected function _combineToHtml($scriptList)
    {
        $urlRewriter = $this->di->urlRewriter;

        $js = $this->combine($scriptList);

        // @todo support $this->attributes

        $s = '';
        foreach ($js as $item) {
            if (isset($item['error'])) {
                if (isset($item['content'])) {
                    $s .= '<script' . $item['attr'] . '>' . $item['content'] . '</script>';
                } else {
                    $q = (strpos($item['src'], '"') === false) ? '"' : "'";
                    $s .= '<script src=' . $q . $item['src'] . $q . $item['attr'] . '></script>';
                }
            } else {
                $content = $item['content'];
                if (strlen($content) <= $this->config->js->inlinelimit) {
                    $s .= '<script' . $item['attr'] . '>' . $content . '</script>';
                } else {
                    $hash = substr($item['hash'], 0, $this->config->filehashsize);

                    $cacheFile = $this->config->webrootpath . $this->config->staticdir . '/' . $hash . '.js';
                    $fs = $this->di->filesystem;
                    $fs->putContents($cacheFile, $content);
                    $fs->putContents($cacheFile . '.gz', gzencode($content, 9));

                    switch ($this->config->fileloader) {
                        case 'php':
                            $cacheFile = $this->config->fileloaderphppath . '?' . $hash . '.js';
                    }
                    // DO NOT minify URL (because of caching)
                    $s .= '<script src="' . $urlRewriter->filepathToUrl($cacheFile) . '"' . $item['attr'] . '></script>';
                }
            }
        }

        return $s;
    }

    /**
     * @param $scriptList array
     * @return array
     * @throws ERessio_UnknownDiKey
     */
    public function combine($scriptList)
    {
        $dispatcher = $this->di->dispatcher;
        $minifyJs = $this->di->jsMinify;

        $dispatcher->triggerEvent('JsCombineBefore', array(&$scriptList));

        $js = array();
        $regex = $this->config->js->excludeminifyregex;

        $hash_prefix = get_class($minifyJs);
        $item_hash = $hash_prefix;
        $item_content = '';
        $async = true;
        $defer = true;

        foreach ($scriptList as $item) {
            if ($item['type'] === 'inline') {
                $content = $item['script'];
                try {
                    $dispatcher->triggerEvent('JsInlineMinifyBefore', array(&$content));
                    $minified = $minifyJs->minifyInline($content);
                    $dispatcher->triggerEvent('JsInlineMinifyAfter', array(&$minified));

                    $minified = rtrim($minified, "; \t\n\r\0\x0B") . ';';

                    $comment_start = strrpos($minified, '//');
                    if ($comment_start !== false) {
                        $comment_end = strrpos($minified, "\n");
                        if ($comment_end === false || $comment_start > $comment_end) {
                            $minified .= "\n";
                        }
                    }
                    $comment_start = strrpos($minified, '/*');
                    if ($comment_start !== false) {
                        $comment_end = strrpos($minified, '*/');
                        if ($comment_end === false || $comment_start > $comment_end) {
                            $minified .= "//*/\n";
                        }
                    }

                    if ($this->config->js->wraptrycatch) {
                        $minified = 'try{' . rtrim($minified, ';') . '}catch(e){console.log(e)}';
                    }

                    $item_hash .= '|' . $content;
                    $item_content .= $minified;
                    $async = $async && $item['async'];
                    $defer = $defer && $item['defer'];
                } catch (ERessio_InvalidJs $e) {
                    $this->di->logger->warning('Catched error in Ressio_JsCombiner::combine: ' . $e->getMessage());
                    if ($item_content !== '') {
                        $js[] = array('hash' => sha1($item_hash), 'content' => $item_content, 'attr' => ($async ? ' async' : '') . ($defer ? ' defer' : ''));
                    }
                    $async = $item['async'];
                    $defer = $item['defer'];
                    $js[] = array('hash' => false, 'content' => $content, 'error' => true, 'attr' => ($async ? ' async' : '') . ($defer ? ' defer' : ''));
                    $item_hash = $hash_prefix;
                    $item_content = '';
                    $async = true;
                    $defer = true;
                }
            } else {
                $urlRewriter = $this->di->urlRewriter;
                $fs = $this->di->filesystem;
                $src = $item['src'];
                $path = isset($item['filename']) ? $item['filename'] : $urlRewriter->urlToFilepath($src);
                if ($path === null || pathinfo($path, PATHINFO_EXTENSION) !== 'js') {
                    // external url
                    $path = null;
                    $isMinified = false;
                } else {
                    // local js file
                    // @todo make check for .min.js optional
                    $isMinified = preg_match('/\.min\.js$/', $path);
//                    if (!$isMinified) {
//                        $path_min = substr($path, 0, -2) . 'min.js';
//                        if ($fs->isFile($path_min)) {
//                            $path = $path_min;
//                            $isMinified = true;
//                        }
//                    }
                }
                if ($path === null) {
                    $path = $urlRewriter->expand($src);
                }
                try {
                    $content = $fs->getContents($path);
                    if ($content === false) {
                        throw new ERessio_InvalidJs('File ' . $path . ' not found.');
                    }
                    if (strncmp($content, "\x1f\x8b", 2) === 0) {
                        $content = gzinflate(substr($content, 10, -8));
                    }

                    if ($isMinified || ($regex !== null && preg_match($regex, $src))) {
                        $minified = $content;
                    } else {
                        $dispatcher->triggerEvent('JsFileMinifyBefore', array($item['src'], &$content));
                        $minified = $minifyJs->minify($content);
                        $dispatcher->triggerEvent('JsFileMinifyAfter', array($item['src'], &$minified));
                    }

                    $minified = rtrim($minified, "; \t\n\r\0\x0B") . ';';

                    $comment_start = strrpos($minified, '//');
                    if ($comment_start !== false) {
                        $comment_end = strrpos($minified, "\n");
                        if ($comment_end === false || $comment_start > $comment_end) {
                            $minified .= "\n";
                        }
                    }
                    $comment_start = strrpos($minified, '/*');
                    if ($comment_start !== false) {
                        $comment_end = strrpos($minified, '*/');
                        if ($comment_end === false || $comment_start > $comment_end) {
                            $minified .= "//*/\n";
                        }
                    }

                    if ($this->config->js->wraptrycatch) {
                        $minified = 'try{' . rtrim($minified, ';') . '}catch(e){console.log(e)}';
                    }
                    $item_hash .= '|' . $content;
                    $item_content .= $minified;
                    $async = $async && $item['async'];
                    $defer = $defer && $item['defer'];
                } catch (ERessio_InvalidJs $e) {
                    $this->di->logger->warning('Catched error in Ressio_JsCombiner::combine: ' . $e->getMessage() . ' [in file: ' . $item['src'] . ']');
                    if ($item_content !== '') {
                        $js[] = array('hash' => sha1($item_hash), 'content' => $item_content, 'attr' => ($async ? ' async' : '') . ($defer ? ' defer' : ''));
                    }
                    $js[] = array('hash' => false, 'src' => $src, 'error' => true, 'attr' => ($async ? ' async' : '') . ($defer ? ' defer' : ''));
                    $item_hash = $hash_prefix;
                    $item_content = '';
                    $async = true;
                    $defer = true;
                }
            }
        }
        if ($item_content !== '') {
            $js[] = array('hash' => sha1($item_hash), 'content' => $item_content, 'attr' => ($async ? ' async' : '') . ($defer ? ' defer' : ''));
        }

        $crossfileoptimization = $this->config->js->crossfileoptimization && count($scriptList) > 1;
        foreach ($js as &$jsitem) {
            if (!isset($jsitem['error'])) {
                $content = $jsitem['content'];
                if ($crossfileoptimization) {
                    try {
                        $content = $minifyJs->minify($content);
                    } catch (ERessio_InvalidJs $e) {
                        $this->di->logger->warning('Catched error in Ressio_JsCombiner::combine: ' . $e->getMessage());
                    }
                }
                $dispatcher->triggerEvent('JsCombineAfter', array(&$content));
                $jsitem['content'] = $content;
            }
        }
        unset($jsitem);

        return $js;
    }
}