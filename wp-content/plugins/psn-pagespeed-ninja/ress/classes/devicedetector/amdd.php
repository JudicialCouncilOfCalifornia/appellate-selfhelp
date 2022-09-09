<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_DeviceDetector_AMDD extends Ressio_DeviceDetector_Base
{
    /** @var array */
    protected $amddconfig;

    protected $deviceCaps;

    protected $_screen_width;
    protected $_screen_height;
    protected $_screen_dpr;
    protected $_browser_imgformats;
    protected $_browser_js;
    protected $_category;

    /**
     * @param string $ua
     */
    public function __construct($ua = null)
    {
        include_once RESSIO_LIBS . '/amdd/ua.php';
        if ($ua === null) {
            $ua = AmddUA::getUserAgentFromRequest();
        }
        parent::__construct($ua);
    }

    public function setDI($di)
    {
        $this->di = $di;
        $this->config = $di->config;
        $this->amddconfig = (array)$this->config->amdd;
    }

    protected function getCaps()
    {
        include_once RESSIO_LIBS . '/amdd/amdd.php';
        $deviceCaps = null;
        try {
            $deviceCaps = Amdd::getCapabilities($this->ua, false, $this->amddconfig);
        } catch (Exception $e) {
            $this->di->logger->warning('Catched error in Ressio_DeviceDetector_AMDD::updateCaps: ' . $e->getMessage());
        }
        return $deviceCaps;
    }

    protected function updateCaps()
    {
        $this->_category = 'unknown';
        $this->_screen_width = false;
        $this->_screen_height = false;
        $this->_screen_dpr = 1;
        $this->_browser_imgformats = array();
        $this->_browser_js = true;

        $this->deviceCaps = $this->getCaps();

        if (isset($this->deviceCaps->type)) {
            $this->_category = $this->deviceCaps->type;
        }
        if (isset($this->deviceCaps->screenWidth)) {
            $this->_screen_width = $this->deviceCaps->screenWidth;
        }
        if (isset($this->deviceCaps->screenHeight)) {
            $this->_screen_height = $this->deviceCaps->screenHeight;
        }
        if (isset($this->deviceCaps->pixelRatio)) {
            $this->_screen_dpr = $this->deviceCaps->pixelRatio;
        }
        if (isset($this->deviceCaps->imageFormats)) {
            $this->_browser_imgformats = $this->deviceCaps->imageFormats;
        }
        if (isset($this->deviceCaps->jsSupport)) {
            $this->_browser_js = (bool)$this->deviceCaps->jsSupport;
        }
    }

    /** @return int|bool */
    public function screen_width()
    {
        if ($this->_screen_width === null) {
            $this->updateCaps();
        }
        return $this->_screen_width;
    }

    /** @return int|bool */
    public function screen_height()
    {
        if ($this->_screen_height === null) {
            $this->updateCaps();
        }
        return $this->_screen_height;
    }

    /** @return float|bool */
    public function screen_dpr()
    {
        if ($this->_screen_dpr === null) {
            $this->updateCaps();
        }
        return $this->_screen_dpr;
    }

    /** @return array|null */
    public function browser_imgformats()
    {
        if ($this->_browser_imgformats === null) {
            $this->updateCaps();
        }
        return $this->_browser_imgformats;
    }

    /** @return bool */
    public function browser_js()
    {
        if ($this->_browser_js === null) {
            $this->updateCaps();
        }
        return $this->_browser_js;
    }

    /** @return string */
    public function category()
    {
        if ($this->_category === null) {
            $this->updateCaps();
        }
        return $this->_category;
    }

    /** @return bool */
    public function isDesktop()
    {
        if ($this->_category === null) {
            $this->updateCaps();
        }
        return empty($this->_category) || in_array($this->_category, array('tv', 'gametv'), true);
    }

    /** @return bool */
    public function isMobile()
    {
        if ($this->_category === null) {
            $this->updateCaps();
        }
        return in_array($this->_category, array('xhtml', 'iphone', 'gameport', 'chtml', 'wml'), true);
    }
}