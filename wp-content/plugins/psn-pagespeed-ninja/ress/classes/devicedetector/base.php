<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

abstract class Ressio_DeviceDetector_Base implements IRessio_DeviceDetector
{
    /** @var Ressio_DI */
    protected $di;
    /** @var Ressio_Config */
    protected $config;

    protected $ua;

    protected $_os;
    protected $_os_version;
    protected $_vendor;
    protected $_vendor_version;
    protected $_browser;
    protected $_browser_version;

    public function __construct($ua = null)
    {
        $this->ua = ($ua !== null) ? $ua : $_SERVER['HTTP_USER_AGENT'];
    }

    /**
     * @param $di Ressio_DI
     * @throws ERessio_UnknownDiKey
     */
    public function setDI($di)
    {
        $this->di = $di;
        $this->config = $di->config;
    }

    protected function extractOS()
    {
        // @todo: check $_SERVER['HTTP_UA_OS'] for windows version

        static $osRegexp = array(
            '/\bAndroid ([\w._+]+)/' => 'android',
            '/(?:iPhone; CPU iPhone|iPad; CPU) OS ([\d_]+)/' => 'ios',
            '/\bWindows Phone OS ([\d.]+)/' => 'windowsphone',
            '/\bWindows Phone ([789][\d.]+)/' => 'windowsphone',
            '/\bWindows (?:Phone|Mobile) ([\d._]+)/' => 'windowsmobile',
            '/\bWindows CE\b/' => 'windowsce',
            '/\bBB(10)\b/' => 'blackberry',
            '/\bBlackBerry ?(\d{4})/' => 'blackberry',
            '/\bSeries(\d0\/[\d._]+)/' => 'symbian',
            '/\bS(60)(?:; |\/)SymbOS/' => 'symbian',
            '/\bMeeGo(?:/([\d._]+))?\b/' => 'meego',
            '/\bMaemo\b/' => 'maemo',
            '/\bPalmOS|\bPalmSource|\bBlazer/' => 'palmos',
            '/\b(?:web|hpw)OS/([\d.]+)/' => 'webos',
            '/\bBada(?: |\/)([\d.]+)\b' => 'bada',
            '/\bBREW[ \/]([\d.]+)/' => 'brew',
            '/\bJ2ME/|\bMIDP\b|\bCLDC\b/' => 'j2me',
            '/\bWindows (NT [\d.]+)/' => 'windows',
            '/\bWindows ([\d.]+)/' => 'windows',
            '/\bMac OS X ([\d._]+)/' => 'macosx',
            '/\bLinux/' => 'linux',
            '/\bWindows/' => 'windows'
        );

        $this->_os = 'unknown';
        $this->_os_version = 'unknown';

        foreach ($osRegexp as $rule => $os) {
            if (preg_match($rule, $this->ua, $match)) {
                $this->_os = $os;
                if (isset($match[1]) && !empty($match[1])) {
                    $this->_os_version = str_replace('_', '.', $match[1]);
                }
                return;
            }
        }
    }

    protected function extractVendor()
    {
        static $vendorRegexp = array(
            '/\bTrident\/([\d.]+)/' => 'ms',
            '/\bPresto\/([\d.]+)/' => 'o',
            '/\b(?:Apple)?Web[kK]it\/([\d.]+)/' => 'webkit',
            '/\bFirefox\/([\w.]+)/' => 'moz',
            '/\bOpera[ M\/]/' => 'o',
            '/\bGecko\/([\d.]+)/' => 'moz'
        );

        $this->_vendor = 'unknown';
        $this->_vendor_version = 'unknown';

        foreach ($vendorRegexp as $rule => $vendor) {
            if (preg_match($rule, $this->ua, $match)) {
                $this->_vendor = $vendor;
                if (isset($match[1]) && !empty($match[1])) {
                    $this->_vendor_version = str_replace('_', '.', $match[1]);
                }
                return;
            }
        }
    }

    protected function extractBrowser()
    {
        static $browserRegexp = array(
            '/\bCr(?:Mo|iOS)\/([\d.]+)/' => 'chromemobile',
            '/\bAndroid .*?Chrome\/([\d.]+) Mobile Safari/' => 'chromemobile',
            '/\bDolfin\/([\d.]+)\b/' => 'dolphin',
            '/\bSkyfire/' => 'skyfire',
            '/\bBolt\/([\d.]+)\b/' => 'bolt',
            '/\bBlazer[ \/]([\d.]+)/' => 'blazer',
            '/\bTizen[ \/]([\d.]+)/' => 'tizen',
            '/MSIE/' => 'ie',
            '/Firefox/' => 'firefox',
            '/Chrome/' => 'chrome',
            '/Opera/' => 'opera',
            '/Safari/' => 'safari'
        );

        $this->_browser = 'unknown';
        $this->_browser_version = 'unknown';

        foreach ($browserRegexp as $rule => $browser) {
            if (preg_match($rule, $this->ua, $match)) {
                $this->_browser = $browser;
                if (isset($match[1]) && !empty($match[1])) {
                    $this->_browser_version = str_replace('_', '.', $match[1]);
                }
                return;
            }
        }
    }

    /** @return string */
    public function os()
    {
        if ($this->_os === null) {
            $this->extractOS();
        }
        return $this->_os;
    }

    /** @return string */
    public function os_version()
    {
        if ($this->_os_version === null) {
            $this->extractOS();
        }
        return $this->_os_version;
    }

    /** @return string */
    public function vendor()
    {
        if ($this->_vendor === null) {
            $this->extractVendor();
        }
        return $this->_vendor;
    }

    /** @return string */
    public function vendor_version()
    {
        if ($this->_vendor_version === null) {
            $this->extractVendor();
        }
        return $this->_vendor_version;
    }

    /** @return string */
    public function browser()
    {
        if ($this->_browser === null) {
            $this->extractBrowser();
        }
        return $this->_browser;
    }

    /** @return string */
    public function browser_version()
    {
        if ($this->_browser_version === null) {
            $this->extractBrowser();
        }
        return $this->_browser_version;
    }
}