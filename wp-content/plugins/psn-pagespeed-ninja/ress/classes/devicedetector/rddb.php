<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_DeviceDetector_RDDB extends Ressio_DeviceDetector_AMDD
{
    private $api_url = 'http://api.mobilejoomla.com/get';

    public function setDI($di)
    {
        parent::setDI($di);
        $this->config = $di->config;
        if (isset($this->config->rddb->apiurl)) {
            $this->api_url = $this->config->rddb->apiurl;
        }
    }

    protected function getCaps()
    {
        // fast check for desktop browsers
        $ua = AmddUA::normalize($this->ua);
        if (AmddUA::isDesktop($ua)) {
            $data = new stdClass;
            $data->type = 'desktop';
            $data->markup = '';
            return $data;
        }

        $cache = $this->di->cache;
        $cache_id = $cache->id($ua, 'rddb');
        $devicejson = $cache->getOrLock($cache_id);

        if (!is_string($devicejson)) {
            $headers = $_SERVER;

            static $remove = array(
                'HTTP_ACCEPT_ENCODING',
                'HTTP_ACCEPT_LANGUAGE',
                'HTTP_AUTHORIZATION',
                'HTTP_CACHE_CONTROL',
                'HTTP_CONNECTION',
                'HTTP_CONTENT_LENGTH',
                'HTTP_CONTENT_TYPE',
                'HTTP_COOKIE',
                'HTTP_DNT',
                'HTTP_IF_MODIFIED_SINCE',
                'HTTP_IF_NONE_MATCH',
                'HTTP_ORIGIN',
                'HTTP_PRAGMA',
                'HTTP_REFERER',
                'HTTP_SEC_WEBSOCKET_KEY',
                //'HTTP_SEC_WEBSOCKET_VERSION',
                'HTTP_UPGRADE',
                'HTTP_UPGRADE_INSECURE_REQUESTS'
            );

            foreach ($headers as $h => $v) {
                if (strncmp($h, 'HTTP_', 5) !== 0) {
                    unset($headers[$h]);
                }
            }
            foreach ($remove as $h) {
                unset($headers[$h]);
            }

            $data = array(
                'domain' => $_SERVER['SERVER_NAME'],
                'request_method' => $_SERVER['REQUEST_METHOD'],
                'phpheaders' => 1,
                'headers' => $headers
            );

            $data = http_build_query($data);
            $options = array('http' => array(
                'method' => 'POST',
                'header' =>
                    "Content-Type: application/x-www-form-urlencoded\r\n" .
                    'Content-Length: ' . strlen($data) . "\r\n",
                'content' => $data,
                'timeout' => $this->config->rddb->timeout,
                'ignore_errors' => true,
            ));
            if ($this->config->rddb->proxy) {
                $options['http']['proxy'] = $this->config->rddb->proxy_url;
                $options['http']['request_fulluri'] = true;
                if (!empty($this->config->rddb->proxy_login)) {
                    $options['http']['header'] .= 'Authorization: Basic '
                        . base64_encode($this->config->rddb->proxy_login . ':' . $this->config->rddb->proxy_pass) . "\r\n";
                }
            }

            // @todo use uri reader class abstraction (or use DI->filesystem)
            $devicejson = @file_get_contents($this->api_url, false, stream_context_create($options));

            if ($devicejson !== false) {
                $cache->storeAndUnlock($cache_id, $devicejson);
            } else {
                $cache->delete($cache_id);
            }
        }

        $devicejson = json_decode($devicejson);
        if ($devicejson === false) {
            $devicejson = parent::getCaps();
        }

        return $devicejson;
    }
}
