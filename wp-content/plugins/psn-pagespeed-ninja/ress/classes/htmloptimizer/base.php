<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

abstract class Ressio_HtmlOptimizer_Base implements IRessio_HtmlOptimizer
{
    /** @var Ressio_DI */
    protected $di;
    /** @var Ressio_Config */
    protected $config;
    /** @var Ressio_Dispatcher */
    protected $dispatcher;
    /** @var Ressio_UrlRewriter $urlRewriter */
    protected $urlRewriter;

    protected $tags_selfclose = array(
        'area' => 0, 'base' => 0, 'basefont' => 0, 'br' => 0, 'col' => 0,
        'command' => 0, 'embed' => 0, 'frame' => 0, 'hr' => 0, 'img' => 0,
        'input' => 0, 'ins' => 0, 'keygen' => 0, 'link' => 0, 'meta' => 0,
        'param' => 0, 'source' => 0, 'track' => 0, 'wbr' => 0
    );
    protected $tags_nospaces = array(
        'html' => 0, 'head' => 0, 'body' => 0,
        'audio' => 0, 'canvas' => 0, 'embed' => 0, 'iframe' => 0, 'map' => 0,
        'object' => 0, 'ol' => 0, 'table' => 0, 'tbody' => 0, 'tfoot' => 0,
        'thead' => 0, 'tr' => 0, 'ul' => 0, 'video' => 0
    );
    protected $tags_preservespaces = array(
        'code' => 0, 'pre' => 0, 'textarea' => 0
    );
    protected $jsEvents = array(
        'onabort' => 0, 'onblur' => 0, 'oncancel' => 0, 'oncanplay' => 0, 'oncanplaythrough' => 0,
        'onchange' => 0, 'onclick' => 0, 'onclose' => 0, 'oncontextmenu' => 0, 'oncuechange' => 0,
        'ondblclick' => 0, 'ondrag' => 0, 'ondragend' => 0, 'ondragenter' => 0, 'ondragleave' => 0,
        'ondragover' => 0, 'ondragstart' => 0, 'ondrop' => 0, 'ondurationchange' => 0, 'onemptied' => 0,
        'onended' => 0, 'onerror' => 0, 'onfocus' => 0, 'oninput' => 0, 'oninvalid' => 0,
        'onkeydown' => 0, 'onkeypress' => 0, 'onkeyup' => 0, 'onload' => 0, 'onloadeddata' => 0,
        'onloadedmetadata' => 0, 'onloadstart' => 0, 'onmousedown' => 0, 'onmousemove' => 0, 'onmouseout' => 0,
        'onmouseover' => 0, 'onmouseup' => 0, 'onmousewheel' => 0, 'onpause' => 0, 'onplay' => 0,
        'onplaying' => 0, 'onprogress' => 0, 'onratechange' => 0, 'onreset' => 0, 'onscroll' => 0,
        'onseeked' => 0, 'onseeking' => 0, 'onselect' => 0, 'onshow' => 0, 'onstalled' => 0,
        'onsubmit' => 0, 'onsuspend' => 0, 'ontimeupdate' => 0, 'onvolumechange' => 0, 'onwaiting' => 0
    );
    protected $uriAttrs = array(
        'a' => array('href'),
        'area' => array('href'),
        'audio' => array('src'),
        'embed' => array('src'),
        'form' => array('action'),
        'frame' => array('src'),
        'html' => array('manifest'),
        'iframe' => array('src'),
        'img' => array('src'),
        'input' => array('formaction', 'src'),
        'link' => array('href'),
        'object' => array('data'),
        'script' => array('src'),
        'source' => array('src'),
        'track' => array('src'),
        'video' => array('poster', 'src')
    );
    protected $attrFirst = array(
        'a' => array('href'),
        'div' => array('class', 'id'),
        'iframe' => array('src'),
        'img' => array('src'),
        'input' => array('type', 'name'),
        'label' => array('for'),
        'link' => array('type', 'rel', 'href'),
        'option' => array('value'),
        'param' => array('type', 'name'),
        'script' => array('type'),
        'select' => array('name'),
        'span' => array('class', 'id'),
        'style' => array('type'),
        'textarea' => array('cols', 'rows', 'name')
    );
    protected $defaultAttrsHtml4 = array(
        'area' => array(
            'shape' => 'rect'
        ),
        'button' => array(
            'type' => 'submit'
        ),
        'form' => array(
            'enctype' => 'application/x-www-form-urlencoded',
            'method' => 'get'
        ),
        'input' => array(
            'type' => 'text'
        )
    );
    protected $defaultAttrsHtml5 = array(
        'area' => array(
            'shape' => 'rect'
        ),
        'button' => array(
            'type' => 'submit'
        ),
        'command' => array(
            'type' => 'command'
        ),
        'form' => array(
            'autocomplete' => 'on',
            'enctype' => 'application/x-www-form-urlencoded',
            'method' => 'get'
        ),
        'input' => array(
            'type' => 'text'
        ),
        'marquee' => array(
            'behavior' => 'scroll',
            'direction' => 'left'
        ),
        'ol' => array(
            'type' => 'decimal'
        ),
        'script' => array(
            'type' => 'text/javascript'
        ),
        'style' => array(
            'type' => 'text/css'
        ),
        'td' => array(
            'colspan' => '1',
            'rowspan' => '1'
        ),
        'textarea' => array(
            'wrap' => 'soft'
        ),
        'th' => array(
            'colspan' => '1',
            'rowspan' => '1'
        ),
        'track' => array(
            'kind' => 'subtitles'
        )
    );
    protected $htmlTags = array(
        '!doctype',
        'a', 'abbr', 'acronym', 'address', 'applet', 'area', 'article', 'aside', 'audio',
        'b', 'base', 'basefont', 'bdi', 'bdo', 'big', 'blockquote', 'body', 'br', 'button',
        'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'command',
        'datalist', 'dd', 'del', 'details', 'dfn', 'dir', 'div', 'dl', 'dt',
        'em', 'embed',
        'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'frame', 'frameset',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html',
        'i', 'iframe', 'img', 'input', 'ins',
        'kbd', 'keygen',
        'label', 'legend', 'li', 'link',
        'map', 'mark', 'menu', 'meta', 'meter',
        'nav', 'noframes', 'noscript',
        'object', 'ol', 'optgroup', 'option', 'output',
        'p', 'param', 'pre', 'progress',
        'q',
        'rp', 'rt', 'ruby',
        's', 'samp', 'script', 'section', 'select', 'small', 'source', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup',
        'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead', 'time', 'title', 'tr', 'track', 'tt',
        'u', 'ul',
        'var', 'video',
        'wbr'
    );

    /** @var int */
    public $doctype = self::DOCTYPE_HTML5;

    /**
     * @param $di Ressio_DI
     * @throws ERessio_UnknownDiKey
     */
    public function setDI($di)
    {
        $this->di = $di;
        $this->config = $di->config;
        $this->dispatcher = $di->dispatcher;
        $this->urlRewriter = $di->urlRewriter;
    }

    /** @var array */
    protected $cmpAttrFirst;

    /**
     * Comparison method to sort attributes for better gzip compression
     * @param string $attr1
     * @param string $attr2
     * @return int
     */
    public function attrFirstCmp($attr1, $attr2)
    {
        $value1 = array_search($attr1, $this->cmpAttrFirst, true);
        if ($value1 === false) {
            $value1 = 1000;
        }

        $value2 = array_search($attr2, $this->cmpAttrFirst, true);
        if ($value2 === false) {
            $value2 = 1000;
        }

        return $value1 - $value2;
    }

    /**
     * Minify CSS
     * @param string $str
     * @param string $srcBase
     * @param string $targetBase
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function cssMinifyInline($str, $srcBase = null, $targetBase = null)
    {
        try {
            return $this->di->cssMinify->minifyInline($str, $srcBase);
        } catch (ERessio_InvalidCss $e) {
            $this->di->logger->warning('Catched error in Ressio_HtmlOptimizer_Base::cssMinifyInline: ' . $e->getMessage());
            return $str;
        }
    }

    /**
     * Minify JS
     * @param string $str
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function jsMinifyInline($str)
    {
        try {
            return $this->di->jsMinify->minifyInline($str);
        } catch (ERessio_InvalidJs $e) {
            $this->di->logger->warning('Catched error in Ressio_HtmlOptimizer_Base::jsMinifyInline: ' . $e->getMessage());
            return $str;
        }
    }

    /**
     * @param string $ressMedia
     * @return bool
     * @throws ERessio_UnknownDiKey
     */
    public function matchRessMedia($ressMedia)
    {
        //Example: ress-media="mobile and (vendor: webkit) and not (os: android)"

        $device = $this->di->deviceDetector;

        $ressMedia = trim($ressMedia);
        $size = strlen($ressMedia);
        /** @var int $i */
        $i = 0;

        while ($i < $size) {
            // parse "not"
            $invertRule = false;
            if ($i + 4 < $size
                && $ressMedia[$i] === 'n' && $ressMedia[$i + 1] === 'o' && $ressMedia[$i + 2] === 't'
                && ($ressMedia[$i + 3] <= ' ' || $ressMedia[$i + 3] === '(')
            ) {
                $invertRule = true;
                $i += 3;
                while ($i < $size && $ressMedia[$i] <= ' ') {
                    $i++;
                }
                if ($i === $size) {
                    $this->di->logger->warning('Wrong ress-media query: ' . $ressMedia);
                    return false;
                }
            }

            if ($ressMedia[$i] === '(') {
                // parse prop:value
                $j = strpos($ressMedia, ')', $i + 1);
                if ($j === false) {
                    $this->di->logger->warning('Wrong ress-media query: ' . $ressMedia);
                    return false;
                }
                /** @var string $prop */
                /** @var string $value */
                list($prop, $value) = explode(':', substr($ressMedia, $i + 1, $j - $i - 1), 2);
                if ($value === null) {
                    $this->di->logger->warning('Wrong ress-media query: ' . $ressMedia);
                    return false;
                }
                $prop = trim($prop);
                $value = trim($value);
                $compare = '=';
                if (strlen($prop) > 4 && $prop[0] === 'm' && $prop[3] === '-') {
                    if ($prop[1] === 'i' && $prop[2] === 'n') {
                        // min-
                        $compare = '>=';
                        $prop = substr($prop, 4);
                    } elseif ($prop[1] === 'a' && $prop[2] === 'x') {
                        // max-
                        $compare = '<=';
                        $prop = substr($prop, 4);
                    }
                }

                // @todo support px/em in values
                // 1em = 16px = 12pt
                // Aem => (16*A)px
                // Apt => (4/3*A)px
                $result = false;
                switch ($prop) {
                    case 'vendor':
                        $result = strcasecmp($device->vendor(), $value) === 0;
                        break;
                    case 'vendor-version':
                        $result = version_compare($device->vendor_version(), $value, $compare);
                        break;
                    case 'os':
                        $result = strcasecmp($device->os(), $value) === 0;
                        break;
                    case 'os-version':
                        $result = version_compare($device->os_version(), $value, $compare);
                        break;
                    case 'browser':
                        $result = strcasecmp($device->browser(), $value) === 0;
                        break;
                    case 'browser-version':
                        $result = version_compare($device->browser_version(), $value, $compare);
                        break;
                    case 'device-pixel-ratio':
                        if (strpos($value, '/') !== false) {
                            list($x, $y) = explode('/', $value, 2);
                            $value = (int)$x / (int)$y;
                        }
                        $value = (float)$value;
                        $dpr = (float)$device->screen_dpr();
                        switch ($compare) {
                            case '=':
                                $result = ($dpr === $value);
                                break;
                            case '<=':
                                $result = ($dpr <= $value);
                                break;
                            case '>=':
                                $result = ($dpr >= $value);
                                break;
                        }
                        break;
                    case 'device-width':
                        $width = (int)$device->screen_width();
                        if ($width === 0) {
                            $result = true;
                            break;
                        }
                        $value = (int)$value;
                        switch ($compare) {
                            case '=':
                                $result = ($width === $value);
                                break;
                            case '<=':
                                $result = ($width <= $value);
                                break;
                            case '>=':
                                $result = ($width >= $value);
                                break;
                        }
                        break;
                    case 'device-height':
                        $height = (int)$device->screen_height();
                        if ($height === 0) {
                            $result = true;
                            break;
                        }
                        $value = (int)$value;
                        switch ($compare) {
                            case '=':
                                $result = ($height === $value);
                                break;
                            case '<=':
                                $result = ($height <= $value);
                                break;
                            case '>=':
                                $result = ($height >= $value);
                                break;
                        }
                        break;
                    case 'device-size':
                        $value = (int)$value;
                        switch ($compare) {
                            case '=':
                                $this->di->logger->warning('Unknown property (' . $prop . ') in ress-media query: ' . $ressMedia);
                                return false;
                                break;
                            case '<=':
                                $deviceSize = (int)max($device->screen_height(), $device->screen_width());
                                $result = ($deviceSize === 0) || ($deviceSize <= $value);
                                break;
                            case '>=':
                                $deviceSize = (int)min($device->screen_height(), $device->screen_width());
                                $result = ($deviceSize === 0) || ($deviceSize >= $value);
                                break;
                        }
                        break;
                    default:
                        $this->di->logger->warning('Unknown property (' . $prop . ') in ress-media query: ' . $ressMedia);
                        return false;
                }

                if ($result === $invertRule) {
                    // "(false)" or "not(true)"
                    return false;
                }

                $i = $j + 1;
                while ($i < $size && $ressMedia[$i] <= ' ') {
                    $i++;
                }

            } else {
                // parse device category name
                $j = strpos($ressMedia, ' ', $i + 1);
                if ($j === false) {
                    $value = substr($ressMedia, $i);
                    $i = $size;
                } else {
                    $value = substr($ressMedia, $i, $j - $i);
                    /** @var int $i */
                    $i = $j + 1;
                    while ($i < $size && $ressMedia[$i] <= ' ') {
                        $i++;
                    }
                }
                switch ($value) {
                    case 'mobile':
                        $result = $device->isMobile();
                        break;
                    case 'desktop':
                        $result = $device->isDesktop();
                        break;
                    default:
                        $result = ($device->category() === $value);
                }

                if ($result === $invertRule) {
                    // "false" or "not true"
                    return false;
                }
            }

            if ($i >= $size) {
                break;
            }

            // parse "and"
            if ($i + 4 < $size
                && $ressMedia[$i] === 'a' && $ressMedia[$i + 1] === 'n' && $ressMedia[$i + 2] === 'd'
                && ($ressMedia[$i + 3] <= ' ' || $ressMedia[$i + 3] === '(')
            ) {
                $i += 3;
                while ($i < $size && $ressMedia[$i] <= ' ') {
                    $i++;
                }
            } else {
                $this->di->logger->warning('Wrong ress-media query: ' . $ressMedia);
                return false;
            }
        }
        return true;
    }
}
