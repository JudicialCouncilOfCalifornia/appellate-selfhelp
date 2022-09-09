<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri Ltd., PageSpeed Ninja Team. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_Widgets extends Ressio_Plugin
{
    private $jsInjected = false;

    private $protocols = array('' => 0, 'http:' => 0, 'https:' => 0);
    private $widgetsSrc = array(
        // Facebook
        '//connect.facebook.net/%LANG%/all.js' => true,
        '//connect.facebook.net/%LANG%/sdk.js' => true,
        // Twitter
        '//platform.twitter.com/widgets.js' => true,
        // Google Plus
        '//apis.google.com/js/api.js' => true,
        '//apis.google.com/js/platform.js' => true,
        '//apis.google.com/js/plusone.js' => true,
        // ShareThis
        '//s.sharethis.com/loader.js' => true,
        // Gravatar
        '//gravatar.com/js/gprofiles.js' => true,
        '//s.gravatar.com/js/gprofiles.js' => true,
        // AdsByGoogle
        '//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js' => true,
        // Others
        '//reactandshare.azureedge.net/plugin/rns.js' => true,
    );

    private $widgetsEmbed = array();
    private $src_regex;

    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        $params = $this->loadConfig(__DIR__ . '/config.json', $params);

        parent::__construct($di, $params);

        $this->generateEmbeds();
        foreach ($this->widgetsEmbed as $i => $regex_replace) {
            $this->widgetsEmbed[$i][0] = '#' . $regex_replace[0] . '#';
        }
    }

    /**
     * @param IRessio_HtmlOptimizer $optimizer
     * @param IRessio_HtmlNode $node
     */
    private function injectJsLoader($optimizer, $node)
    {
        $optimizer->appendScriptDeclaration(
            file_get_contents(RESSIO_PATH . '/classes/plugin/widgets/loader.min.js'),
            array('async' => true, 'defer' => true),
            ($optimizer instanceof Ressio_HtmlOptimizer_Stream) ? false : $node
        );
        $this->jsInjected = true;
    }

    /**
     * @param Ressio_Event $event
     * @param IRessio_HtmlOptimizer $optimizer
     * @param IRessio_HtmlNode $node
     */
    public function onHtmlIterateTagSCRIPTBefore($event, $optimizer, $node)
    {
        if ($node->hasAttribute('type') && $node->getAttribute('type') !== 'text/javascript') {
            return;
        }

        // @todo check attributes, skip if unusual one is presented (e.g. id for reference, etc.)
        // external resource
        if ($node->hasAttribute('src')) {
            $src = trim($node->getAttribute('src'));

            if (($pos = strpos($src, '//')) === false) {
                return;
            }

            // check against excludemergeregex
            $regex = $this->config->js->excludemergeregex;
            if ($regex !== null && preg_match($regex, $src)) {
                return;
            }

            $protocol = substr($src, 0, $pos);
            if (!isset($this->protocols[strtolower($protocol)])) {
                return;
            }
            $src = substr($src, $pos);

            $lang = false;
            if (preg_match('#/\w\w[_-]\w\w/#', $src, $matches)) {
                $lang = $matches[0];
                $src = str_replace($lang, '/%LANG%/', $src);
            }

            $anchor = false;
            $pos = strpos($src, '#');
            if ($pos !== false) {
                $anchor = substr($src, $pos + 1);
                $src = substr($src, 0, $pos);
            }

            $query = false;
            $pos = strpos($src, '?');
            if ($pos !== false) {
                $query = substr($src, $pos + 1);
                $src = substr($src, 0, $pos);
            }

            $newSrc = false;
            if (isset($this->widgetsSrc[$src])) {
                $newSrc = $this->widgetsSrc[$src];
                if ($newSrc === true) {
                    $newSrc = $src;
                }
            } elseif ($node->hasAttribute('async') || $node->hasAttribute('defer')) {
                // @todo make this check optional
                // @todo check attributes, skip if unusual one is presented (e.g. id for reference, etc.)
                $newSrc = $src;
            }

            if ($newSrc !== false) {
                $newSrc = $protocol . $newSrc;

                if ($lang !== false) {
                    $newSrc = str_replace('/%LANG%/', $lang, $newSrc);
                }

                if ($query !== false) {
                    $newSrc .= '?' . $query;
                }

                if ($anchor !== false) {
                    $newSrc .= '#' . $anchor;
                }

                if (!$this->jsInjected) {
                    $this->injectJsLoader($optimizer, $node);
                }

                $content = 'ress_js("' . $newSrc . '");';
                $node->removeAttribute('src');
                $optimizer->nodeSetInnerText($node, $content);
                //$node->attributes = array();
            }
        } else { // embedded javascript
            // @todo make check of embedded js optional (regex-based, might be slow)
            $content = $optimizer->nodeGetInnerText($node);

            foreach ($this->widgetsEmbed as $regex) {
                if (preg_match_all($regex[0], $content, $matches, PREG_SET_ORDER)) {
                    $replace = $regex[1];
                    foreach ($matches as $match) {
                        $vars = array();
                        foreach ($match as $key => $value) {
                            if (strncmp($key, 'var', 3) === 0) {
                                $vars[] = $value;
                            }
                        }
                        $src = false;
                        foreach ($match as $key => $value) {
                            if ($key !== 0 && $value !== '' && !in_array($value, $vars, true)) {
                                $src = $value;
                            }
                        }
                        if ($src !== false) {
                            // Skip GA (TODO: implement a blacklist instead of hardcoded value)
                            if (strpos($src, 'google-analytics.com') !== false) {
                                continue;
                            }
                            if (!$this->jsInjected) {
                                $this->injectJsLoader($optimizer, $node);
                            }
                            $newContent = 'ress_js(' . $src . ');';
                            if ($replace !== true) {
                                $newContent = str_replace('%RESSJS%', $newContent, $replace);
                            }
                            $content = str_replace($match[0], $newContent, $content);
                        }
                    }
                }
            }

            $optimizer->nodeSetInnerText($node, $content);
        }
    }

    private function generateEmbeds()
    {
        $quoted_string = '(?:"[^"]+"|\'[^\']+\')';
        $this->src_regex = '('
            . $this->re_or(
                '(' . $quoted_string . '\s*\+\s*\w+\s*\+\s*)?' . $quoted_string,
                '\(' . $this->re_or(
                    'document\.location\.protocol\s*==\s*(?:"https:"|\'https:\')',
                    '(?:"https:"|\'https:\')\s*==\s*document\.location\.protocol'
                ) . '\s*\?\s*' . $quoted_string . '\s*:\s*' . $quoted_string . '\s*\)\s*\+\s*' . $quoted_string
            ) . ')';

        $this->widgetsEmbed = array();

        // Facebook-like widget
        $this->widgetsEmbed[] = array(
            $this->re_quote('( function ( @1 , @2 , @3 ) { ') .
            $this->re_quote('var  @4 , @5 = \1.getElementsByTagName( \2 ) [ 0 ] ; ') .
            $this->re_quote(' if ( \1.getElementById( \3 ) ) return ; ') .
            $this->re_quote('\4 = \1.createElement( \2 ) ; ') .
            $this->re_quote('\4.id = \3 ; ') .
            $this->re_quote('\4.src = %SRC% ; ') .
            $this->re_quote('\5.parentNode.insertBefore( \4 , \5 ) ') . ';?' .
            $this->re_quote(' } ') .
            $this->re_or(
                $this->re_quote('( document , "script" , "%ID%" )'),
                $this->re_quote(') ( document , "script" , "%ID%"')
            ) .
            $this->re_quote(' ) ;')
        , true);

        // General widget
        $this->widgetsEmbed[] = array(
            $this->re_quote('( function ( ) { ') .
            $this->re_quote('var  @1 = document.createElement( "script" ) ; ') .
            $this->re_shuffle(
                $this->re_quote('\1.type = "text/javascript" ; '),
                $this->re_quote('\1.async = ') . '(?:true|1)' . $this->re_quote(' ; '),
                $this->re_quote('\1.src = %SRC% ; ')
            ) .
            $this->re_or(
                $this->re_quote('var  @2 = document.getElementsByTagName( "script" ) [ 0 ] ; \2.parentNode.insertBefore( \1 , \2 ) '),
                $this->re_quote('( document.getElementsByTagName( "head" ) [ 0 ] || document.getElementsByTagName( "body" ) [ 0 ] ).appendChild( \1 ) ')
            ) . ';?\s*' .
            $this->re_quote('} ) ( ) ;')
        , true);

        // GoogleAnalytics
        /*        $this->widgetsEmbed[] = array(
                    $this->re_quote('( function ( @1 , @2 , @3 , @4 , @5 , @6 , @7 ) { ') .
                    $this->re_quote('\1') . $this->re_or($this->re_quote('[ "GoogleAnalyticsObject" ]'), $this->re_quote('.GoogleAnalyticsObject')) . $this->re_quote(' = \5 ') . '[;,]\s*' .
                    $this->re_quote('\1[ \5 ] = \1[ \5 ] || function ( ) { ( \1[ \5 ].q = \1[ \5 ].q || [] ).push( arguments ) } ') . '[;,]\s*' .
                    $this->re_quote('\1[ \5 ].l = 1 * new  Date') . '(?:\(\s*\))?' . $this->re_quote(' ') . '[;,]\s*' .
                    $this->re_quote('\6 = \2.createElement( \3 ) ') . '[;,]\s*' .
                    $this->re_quote('\7 = \2.getElementsByTagName( \3 ) [ 0 ] ') . '[;,]\s*' .
                    $this->re_quote('\6.async = ') . '(?:true|1)' . $this->re_quote(' ') . '[;,]\s*' .
                    $this->re_quote('\6.src = \4 ') . '[;,]\s*' .
                    $this->re_quote('\7.parentNode.insertBefore( \6 , \7 ) ') .
                    $this->re_quote('} ) ( window , document , "script" , %SRC% , "ga" ) ') . ';?'
                , '(function(w,g){w.GoogleAnalyticsObject=g;w[g]=w[g]||function(){(w[g].q=w[g].q||[]).push(arguments)},w[g].l=1*new Date()})(window,\'ga\');%RESSJS%');*/

        // Facebook
        $this->widgetsEmbed[] = array(
            $this->re_quote('{ var  @1 = document.createElement( "script" ); ') .
            $this->re_quote('\1.async = true; ') .
            $this->re_quote('\1.src = %SRC% ; ') .
            $this->re_quote('var  @2 = document.getElementsByTagName( "script" ) [ 0 ] ; ') .
            $this->re_quote('\2.parentNode.insertBefore( \1 , \2 ) ; ') .
            $this->re_quote('_fbq.loaded = true ; ') .
            $this->re_quote('}')
        , '{%RESSJS%_fbq.loaded=!0;}');

        // Pingdom
        $this->widgetsEmbed[] = array(
            $this->re_quote('( function ( ) { ') .
            $this->re_quote('var  @1 = document.getElementsByTagName( "script" ) [ 0 ] , ') .
            $this->re_quote('@2 = document.createElement( "script" ) ; ') .
            $this->re_quote('\2.async = "async" ; ') .
            $this->re_quote('\2.src = %SRC% ; ') .
            $this->re_quote('\1.parentNode.insertBefore( \2 , \1 ) ; ') .
            $this->re_quote('} ) ( ) ;')
        , true);
    }

    /**
     * @param string $s
     * @return string
     */
    private function re_quote($s)
    {
        $regex = preg_quote($s, '#');

        $regex = str_replace(
            array('\\\\', '  ', ' ', '%ID%'),
            array('\\', '\s+', '\s*', '[\w-]+'),
            $regex);

        $regex = preg_replace(
            array('#@(\d+)#', '#\\\\(\d+)#', '#"([^"]+)"#'),
            array('(?P<var\1>\w+)', '(?P=var\1)', '(?:"\1"|\'\1\')'),
            $regex);

        return str_replace('%SRC%', $this->src_regex, $regex);
    }

    /**
     * @return string
     */
    private function re_or()
    {
        $args = func_get_args();
        return '(?:' . implode('|', $args) . ')';
    }

    /**
     * @return string
     */
    private function re_shuffle()
    {
        switch (func_num_args()) {
            case 1:
                return func_get_arg(0);
            case 2:
                $a = func_get_arg(0);
                $b = func_get_arg(1);
                return '(?:' . $a . $b . '|' . $b . $a . ')';
            case 3:
                $a = func_get_arg(0);
                $b = func_get_arg(1);
                $c = func_get_arg(2);
                return '(?:' . $a . $b . $c . '|' . $a . $c . $b . '|' . $b . $a . $c . '|' . $b . $c . $a . '|' . $c . $a . $b . '|' . $c . $b . $a . ')';
        }
        return '';
    }
}