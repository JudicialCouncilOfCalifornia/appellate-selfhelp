<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_DeviceDetector
{
    /** @return string */
    public function os();

    /** @return string */
    public function os_version();

    /** @return string */
    public function vendor();

    /** @return string */
    public function vendor_version();

    /** @return string */
    public function browser();

    /** @return string */
    public function browser_version();

    /** @return int|bool */
    public function screen_width();

    /** @return int|bool */
    public function screen_height();

    /** @return float|bool */
    public function screen_dpr();

    /** @return array|null */
    public function browser_imgformats();

    /** @return bool */
    public function browser_js();

    /** @return string */
    public function category();

    /** @return bool */
    public function isDesktop();

    /** @return bool */
    public function isMobile();
}