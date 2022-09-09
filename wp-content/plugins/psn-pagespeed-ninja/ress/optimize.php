<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

require_once dirname(__FILE__) . '/ressio.php';

$ressio = new Ressio();

if (ini_get('expose_php')) {
    // override PHP's header
    Ressio_Helper::setHeader('X-Powered-By: RESSIO');
}

$filename = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
$filename = get_absolute_path($filename);
// @TODO: remove webrooturi from $filename
$fullFilename = $ressio->config->webrootpath . DIRECTORY_SEPARATOR . $filename;

if (!is_file($fullFilename)) {
    sendError404();
}

// @todo  charset in config
Ressio_Helper::setHeader('Content-Type: text/html; charset=utf-8');

$hash = sha1($filename . '//' . filemtime($fullFilename));
$etag = '"' . $hash . '"';
Ressio_Helper::setHeader('ETag: ' . $etag);
if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
    $client_etag = stripslashes($_SERVER['HTTP_IF_NONE_MATCH']);
    if ($client_etag === $etag) {
        setResponseCode(304, 'Not Modified');
        Ressio_Helper::sendHeaders();
        exit();
    }
}

Ressio_Helper::setHeader('Pragma: public');
Ressio_Helper::setHeader('Cache-Control: public, must-revalidate, proxy-revalidate');

$content = file_get_contents($fullFilename);
echo $ressio->run($content);


function sendError404()
{
    setResponseCode(404, 'Not Found');
    Ressio_Helper::sendHeaders();
    echo '<h1>404 Not Found</h1>';
    exit();
}

function setResponseCode($code, $message)
{
    $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
    if (strcmp(PHP_VERSION, 'PHP 4.3.0') >= 0) {
        Ressio_Helper::setHeader("$protocol $code $message", true, $code);
    } else {
        Ressio_Helper::setHeader("$protocol $code $message");
    }
    //header('Status: $code $message');
}

function get_absolute_path($path)
{
    $parts = explode('/', str_replace('\\', '/', $path));
    $absolutes = array();
    foreach ($parts as $part) {
        if ($part === '' || $part === '.') {
            continue;
        }
        if ($part === '..') {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    return implode(DIRECTORY_SEPARATOR, $absolutes);
}
