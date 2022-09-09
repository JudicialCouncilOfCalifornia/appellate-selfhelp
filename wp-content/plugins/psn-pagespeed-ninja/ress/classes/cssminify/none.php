<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * No CSS minification
 */
class Ressio_CssMinify_None implements IRessio_CssMinify
{
    /** @var Ressio_DI */
    public $di;
    /** @var Ressio_Config */
    public $config;

    /** @var string */
    public $srcBase;
    /** @var string */
    public $targetBase;

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
     * Minify CSS
     * @param string $str
     * @param string $srcBase
     * @param string $targetBase
     * @return string
     */
    public function minify($str, $srcBase = null, $targetBase = null)
    {
        $this->srcBase = $srcBase;
        $this->targetBase = $targetBase;

        if ($srcBase !== $targetBase) {
            $str = preg_replace_callback(
                '#(?<=url\()\s*(?:"([^"]*?)"|\'([^\']*?)\'|([^ )]*?))\s*(?=\))#',
                array($this, 'replaceUrlsCallback'),
                $str
            );
        }

        return $str;
    }

    /**
     * Minify CSS in style=""
     * @param string $str
     * @param string $srcBase
     * @return string
     */
    public function minifyInline($str, $srcBase = null)
    {
        return $str;
    }

    /**
     * @param string $url
     * @return string
     */
    private function escapeUrl($url)
    {
        // @todo merge escapeUrl and replaceUrlsCallback with corresponding methods from Ressio_CssOptimizer
        if (!strpbrk($url, ' ()\'"')) {
            return $url;
        }

        /** @var int[] $c */
        $c = count_chars($url);
        $url1_extra = $c[32/* */] + $c[34/*"*/] + $c[39/*'*/] + $c[40/*(*/] + $c[41/*)*/];
        $url2_extra = 2 + $c[39/*'*/];
        $url3_extra = 2 + $c[34/*"*/];

        if ($url1_extra < $url2_extra) {
            if ($url1_extra < $url3_extra) {
                return addcslashes($url, ' "\'()');
            }
            return '"' . addcslashes($url, '"') . '"';
        }
        if ($url2_extra < $url3_extra) {
            return "'" . addcslashes($url, "'") . "'";
        }
        return '"' . addcslashes($url, '"') . '"';
    }

    public function replaceUrlsCallback($url)
    {
        $relurl = trim($url[0], ' \'"');
        $relurl = stripslashes($relurl);

        if (strncasecmp($relurl, 'data:', 5) === 0) {
            return $this->escapeUrl($relurl);
        }

        $urlRewriter = $this->di->urlRewriter;

        if (strpos($relurl, '://') === false) {
            $relurl = $urlRewriter->getRebasedUrl($relurl, $this->srcBase, $this->targetBase);
        }
        /*
        if ($relurl[0] === '/') {
            // prior to PHP 5.3.3: E_WARNING is emitted when URL parsing failed.
            $parsed_url = @parse_url($this->srcBase);
            $absUrl = '';
            $absUrl .= isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
            $absUrl .= isset($parsed_url['host']) ? $parsed_url['host'] : '';
            $absUrl .= isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
            $relurl = $absUrl . $relurl;
        }
        */

        $src_file = $urlRewriter->urlToFilepath($relurl);
        if ($this->config->img->minify) {
            if ($src_file !== null) {
                $this->di->imgOptimizer->run($src_file);
            }
        }

        // @todo: inlining small files

        if (strpos($relurl, '"') !== false) {
            $relurl = "'$relurl'";
        } elseif (strpos($relurl, "'") !== false) {
            $relurl = '"' . $relurl . '"';
        }
        return $relurl;
    }

}