<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Stub class Ressio_Config for IDE autocomplete
 */
class Ressio_Config
{
    /** @var bool */
    public $autostart;

    /** @var string */
    public $webrootpath;
    /** @var string */
    public $webrooturi;
    /** @var string */
    public $staticdir;
    /** @var string */
    public $cachedir;
    /** @var int */
    public $cachettl;
    /** @var bool */
    public $cachefast;
    /** @var int */
    public $logginglevel;

    /** @var string ('file'|'php') */
    public $fileloader;
    /** @var string */
    public $fileloaderphppath;
    /** @var int */
    public $filehashsize;

    /** @var Ressio_ConfigHtml */
    public $html;
    /** @var Ressio_ConfigImg */
    public $img;
    /** @var Ressio_ConfigJs */
    public $js;
    /** @var Ressio_ConfigCss */
    public $css;

    /** @var Ressio_ConfigAmdd */
    public $amdd;
    /** @var Ressio_ConfigRddb */
    public $rddb;

    /** @var string[] */
    public $cssminifychain;
    /** @var string[] */
    public $jsminifychain;

    /** @var array */
    public $plugins;
    /** @var array */
    public $di;
}

class Ressio_ConfigHtml
{
    /** @var bool */
    public $forcehtml5;
    /** @var bool */
    public $mergespace;
    /** @var bool */
    public $removecomments;
    /** @var int */
    public $gzlevel;
    /** @var bool */
    public $urlminify;
    /** @var bool */
    public $sortattr;
    /** @var bool */
    public $removedefattr;
    /** @var bool */
    public $removeiecond;
}

class Ressio_ConfigImg
{
    /** @var bool */
    public $minify;
    /** @var bool */
    public $minifyrescaled;
    /** @var string */
    public $origsuffix;
    /** @var stdClass */
    public $execoptim;
    /** @var int */
    public $jpegquality;
}

class Ressio_ConfigJs
{
    /** @var bool */
    public $mergeheadbody;
    /** @var int */
    public $inlinelimit;
    /** @var bool */
    public $crossfileoptimization;
    /** @var bool */
    public $wraptrycatch;
    /** @var bool */
    public $autoasync;
    /** @var bool */
    public $forceasync;
    /** @var bool */
    public $forcedefer;
    /** @var bool */
    public $merge;
    /** @var bool */
    public $checkattributes;
    /** @var bool|string */
    public $mergeinline;
    /** @var bool */
    public $minifyattribute;
    /** @var bool */
    public $skipinits;
    /** @var string */
    public $excludeminifyregex;
    /** @var string */
    public $excludemergeregex;
}

class Ressio_ConfigCss
{
    /** @var bool */
    public $mergeheadbody;
    /** @var bool */
    public $merge;
    /** @var int */
    public $inlinelimit;
    /** @var bool */
    public $crossfileoptimization;
    /** @var bool */
    public $checklinkattributes;
    /** @var bool */
    public $checkstyleattributes;
    /** @var bool|string */
    public $mergeinline;
    /** @var bool */
    public $minifyattribute;
    /** @var string */
    public $excludeminifyregex;
    /** @var string */
    public $excludemergeregex;
}

class Ressio_ConfigAmdd
{
    /** @var string */
    public $handler;
    /** @var string */
    public $dbPath;
}

class Ressio_ConfigRddb
{
    /** @var string */
    public $apiurl;
    /** @var int */
    public $timeout;
    /** @var bool */
    public $proxy;
    /** @var string */
    public $proxy_url;
    /** @var string|false */
    public $proxy_login;
    /** @var string */
    public $proxy_pass;
}