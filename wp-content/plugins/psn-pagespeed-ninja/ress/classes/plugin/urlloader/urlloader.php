<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_UrlLoader extends Ressio_Plugin
{
    /** @var int */
    protected $filehashsize = 12;
    /** @var string */
    protected $targetDir;

    protected $mimeToExt = array(
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
        'image/vnd.microsoft.icon' => 'ico',
        'image/x-icon' => 'ico',
        'text/css' => 'css',
        'text/javascript' => 'js',
        'application/javascript' => 'js',
        'application/x-javascript' => 'js'
    );

    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        $params = $this->loadConfig(dirname(__FILE__) . '/config.json', $params);

        parent::__construct($di, $params);

        $this->targetDir = $this->config->webrootpath . $this->config->staticdir . '/loaded/';
        if (!$di->filesystem->isDir($this->targetDir)) {
            $di->filesystem->makeDir($this->targetDir);
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     * @throws ERessio_UnknownDiKey
     */
    public function onHtmlIterateTagIMGBefore($event, $optimizer, $node)
    {
        if (!$this->params->loadimg || $optimizer->nodeIsDetached($node)) {
            return;
        }

        // @todo: parse srcset attribute
        if ($node->hasAttribute('src')) {
            $url = $node->getAttribute('src');
            $url = $this->loadUrl($url);
            if ($url !== null) {
                $node->setAttribute('src', $url);
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     * @throws ERessio_UnknownDiKey
     */
    public function onHtmlIterateTagSCRIPTBefore($event, $optimizer, $node)
    {
        // @todo find common patterns in embedded <script></script> blocks (GA, etc.)

        if (!$this->params->loadscript || $optimizer->nodeIsDetached($node)) {
            return;
        }

        if ($node->hasAttribute('src')) {
            $url = $node->getAttribute('src');
            $regex = $this->config->js->excludemergeregex;
            if ($regex !== null && preg_match($regex, $url)) {
                return;
            }
            $url = $this->loadUrl($url, 'js');
            if ($url !== null) {
                $node->setAttribute('src', $url);
            }
        }
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     * @throws ERessio_UnknownDiKey
     */
    public function onHtmlIterateTagLINKBefore($event, $optimizer, $node)
    {
        if (!$this->params->loadcss || $optimizer->nodeIsDetached($node)) {
            return;
        }

        if ($node->hasAttribute('rel') && $node->hasAttribute('href') && $node->getAttribute('rel') === 'stylesheet') {
            $url = $node->getAttribute('href');
            $regex = $this->config->css->excludemergeregex;
            if ($regex !== null && preg_match($regex, $url)) {
                return;
            }
            $url = $this->loadUrl($url, 'css', array($this, 'cssRebase'));
            if ($url !== null) {
                // @todo load assets (background images, fonts, imported css)
                $node->setAttribute('href', $url);
            }
        }
    }

    /**
     * @param $url string
     * @param $defaultExt string|null
     * @param $callback callback|null
     * @return string|null
     * @throws ERessio_UnknownDiKey
     */
    protected function loadUrl($url, $defaultExt = null, $callback = null)
    {
        if (strncmp($url, '//', 2) === 0) {
            $url = 'http:' . $url;
        } elseif (strpos($url, '://') === false) {
            return null;
        }

        $url = html_entity_decode($url);

        $parsed = @parse_url($url);
        $host = $parsed['host'];
        if (!in_array($host, $this->params->allowedhosts, true)) {
            return null;
        }
        if (!$this->params->loadqueue && isset($parsed['queue']) && $parsed['queue'] !== '') {
            return null;
        }
        if (!$this->params->loadphp && strlen($parsed['path']) > 3
            && substr_compare($parsed['path'], '.php', -4, 4) === 0
        ) {
            return null;
        }

        /** @var string[] $deps */
        $deps = array(
            'plugin_urlloader',
            $url
        );

        $cache = $this->di->cache;
        $cache_id = $cache->id($deps, 'file');
        $result = $cache->getOrLock($cache_id);
        $cached_data = false;
        if (is_string($result)) {
            $valid = false;
            if ($result[0] === '{') {
                $cached_data = json_decode($result);
                if ($cached_data && isset($cached_data->content) && time() < $cached_data->expiration) {
                    $valid = true;
                    $result = $cached_data->content;
                }
            }
            if (!$valid) {
                $result = $cache->lock($cache_id);
            }
        }

        if (!is_string($result)) {

            $s = '';
            $data = $this->doLoadUrl($url, $cached_data);

            if ($data !== null) {
                $content = $data->content;
                $contentType = $data->contentType;

                if ($defaultExt === null) {
                    if (isset($this->mimeToExt[$contentType])) {
                        $defaultExt = $this->mimeToExt[$contentType];
                    } else {
                        $content = null;
                    }
                }

                if ($content !== null) {
                    $hash = substr(sha1($url), 0, $this->filehashsize);
                    $targetFile = $this->targetDir . $hash . '.' . $defaultExt;

                    if ($callback !== null) {
                        $content = call_user_func_array($callback, array(&$content, $url, &$targetFile));
                    }

                    if ($content !== null) {
                        $this->di->filesystem->putContents($targetFile, $content);
                        $s = $this->di->urlRewriter->filepathToUrl($targetFile);
                    }
                }

            } else {
                $data = new stdClass;
                $data->expiration = time() + $this->di->config->cachettl;
            }

            $data->content = $s;
            if ($result) {
                $data = json_encode($data);
                $cache->storeAndUnlock($cache_id, $data);
            }

            $result = $s;
        }

        return $result === '' ? null : $result;
    }

    /**
     * @param $content string
     * @param $origUrl string
     * @param $targetFile string
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function cssRebase($content, $origUrl, $targetFile)
    {
        $targetUrl = $this->di->urlRewriter->filepathToUrl($targetFile);

        $minifyCss = new Ressio_CssMinify_None;
        $minifyCss->setDI($this->di);
        return $minifyCss->minify($content, dirname($origUrl), dirname($targetUrl));
    }

    /**
     * @param $date string
     * @return int
     */
    protected function parseTimestamp($date)
    {
        static $months = array(
            'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6,
            'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12
        );

        if (!preg_match('/^(?:Sun|Mon|Tue|Wed|Thu|Fri|Sat), (\d\d) (Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) (\d\d\d\d) (\d\d):(\d\d):(\d\d) GMT
/', $date, $matches)) {
            return -1;
        }

        list($skip, $day, $month, $year, $hour, $minute, $second) = $matches;

        if (!isset($months[$month])) {
            return -1;
        }
        $month = $months[$month];

        return gmmktime($hour, $minute, $second, $month, $day, $year);
    }

    /**
     * @param $url string
     * @param $cached_data stdClass
     * @return stdClass|null
     */
    protected function doLoadUrl($url, $cached_data)
    {
        switch ($this->params->mode) {
            case 'curl':
                $response = $this->loadCurl($url, $cached_data);
                break;
            case 'fsock':
                $response = $this->loadFsock($url, $cached_data);
                break;
            case 'stream':
            default:
                $response = $this->loadStream($url, $cached_data);
                break;
        }

        if ($response === null) {
            return null;
        }

        $data = new stdClass;
        $data->content = $response->content;
        $data->expiration = time() + $this->di->config->cachettl;

        $statusCode = null;
        foreach ($response->headers as $line) {
            $line = trim($line);
            $pos = strpos($line, ' ');
            if ($pos === false) {
                continue;
            }
            $header = substr($line, 0, $pos);
            $value = trim(substr($line, $pos + 1));
            switch ($header) {
                case 'HTTP/1.0':
                case 'HTTP/1.1':
                    $statusCode = substr($value, 0, 3);
                    break;
                case 'Age:':
                    $data->expiration = time() + (int)$value;
                    break;
                case 'Cache-Control:':
                    if (preg_match('/\bno-(?:cache|store)\b/', $value)) {
                        return null;
                    }
                    break;
                case 'Content-Type:':
                    $data->contentType = $value;
                    break;
                case 'ETag:':
                    $data->ETag = $value;
                    break;
                case 'Expires:':
                    $expiration = $this->parseTimestamp($value);
                    if ($expiration > 0) {
                        if ($expiration < time()) {
                            return null;
                        }
                        $data->expiration = $expiration;
                    }
                    break;
                case 'Last-Modified:':
                    $data->lastModified = $value;
                    break;
            }
        }

        switch ($statusCode) {
            case '200':
                break;
            case '304':
                $data->content = $cached_data->content;
                break;
            default:
                return null;
        }

        return $data;
    }

    /**
     * @param $url string
     * @param $cached_data stdClass
     * @return stdClass|null
     */
    protected function loadStream($url, $cached_data)
    {
        $headers = '';
        if (isset($cached_data->lastModified)) {
            $headers .= 'If-Modified-Since: ' . $cached_data->lastModified . "\r\n";
        }
        if (isset($cached_data->ETag)) {
            $headers .= 'If-None-Match: ' . $cached_data->ETag . "\r\n";
        }
        $opts = array('http' =>
            array(
                'timeout' => $this->params->timeout,
                'ignore_errors' => true,
                'header' => $headers
            )
        );
        $context = stream_context_create($opts);

        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            return null;
        }

        $response = new stdClass;
        $response->content = $content;
        $response->headers = $http_response_header;

        return $response;
    }

    /**
     * @param $url string
     * @param $cached_data stdClass
     * @return stdClass|null
     */
    protected function loadCurl($url, $cached_data)
    {
        if (!function_exists('curl_init')) {
            return null;
        }

        $headers = array();
        if (isset($cached_data->lastModified)) {
            $headers[] = 'If-Modified-Since: ' . $cached_data->lastModified;
        }
        if (isset($cached_data->ETag)) {
            $headers[] = 'If-None-Match: ' . $cached_data->ETag;
        }

        $c = curl_init($url);
        if (count($headers)) {
            curl_setopt($c, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($c, CURLOPT_HEADER, 1);
        curl_setopt($c, CURLOPT_AUTOREFERER, 1);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, $this->params->timeout);
        curl_setopt($c, CURLOPT_TIMEOUT, $this->params->timeout);
        $content = curl_exec($c);
        curl_close($c);

        if ($content === false) {
            return null;
        }

        $posPrev = 0;
        $pos = 0;
        while ($pos !== false) {
            $protocol = substr($content, $pos, 9);
            if ($protocol !== 'HTTP/1.0 ' || $protocol !== 'HTTP/1.1 ' || !isset($content[$pos + 11])) {
                // error ???
                break;
            }
            $posPrev = $pos;
            $statusPrefix = $content[$pos + 9];
            $pos = strpos($content, "\r\n\r\n", $pos);
            if ($statusPrefix !== '1' && $statusPrefix !== '3') {
                break;
            }
        }
        if ($pos === false) {
            $headers = substr($content, $posPrev);
            $content = '';
        } else {
            $headers = substr($content, $posPrev, $pos - $posPrev);
            $content = substr($content, $pos);
        }

        $response = new stdClass;
        $response->content = $content;
        $response->headers = explode("\r\n", $headers);

        return $response;
    }

    /**
     * @param $url string
     * @param $cached_data stdClass
     * @return stdClass|null
     */
    protected function loadFsock($url, $cached_data)
    {
        $parsed = @parse_url($url);
        if (isset($parsed['user'], $parsed['pass'])) {
            return null;
        }

        $ssl = false;
        if (isset($parsed['scheme'])) {
            $scheme = $parsed['scheme'];
            if ($scheme === 'https') {
                $ssl = true;
            }
        }

        $host = $parsed['host'];
        $port = isset($parsed['port']) ? $parsed['port'] : ($ssl ? 443 : 80);
        $path = $parsed['path'];
        if (isset($parsed['query'])) {
            $path .= '?' . $parsed['query'];
        }

        $fp = @fsockopen(($ssl ? 'ssl://' : '') . $host, $port, $errno, $errstr, $this->params->timeout);
        if (!$fp) {
            return null;
        }

        $request = "GET {$path} HTTP/1.0\r\n";
        $request .= "Host: {$host}\r\n";
        if (isset($cached_data->lastModified)) {
            $request .= 'If-Modified-Since: ' . $cached_data->lastModified . "\r\n";
        }
        if (isset($cached_data->ETag)) {
            $request .= 'If-None-Match: ' . $cached_data->ETag . "\r\n";
        }
        $request .= "Connection: close\r\n\r\n";

        if (!fwrite($fp, $request)) {
            return null;
        }

        $rs = '';
        while ($d = fread($fp, 32768)) {
            $rs .= $d;
        }

        $info = stream_get_meta_data($fp);
        fclose($fp);
        if ($info['timed_out']) {
            return null;
        }

        $response = new stdClass;
        list($header, $content) = explode("\r\n\r\n", $rs, 2);
        $response->content = $content;
        $response->headers = explode("\r\n", $header);

        // @todo follow Location header in 3xx redirects

        return $response;
    }
}
