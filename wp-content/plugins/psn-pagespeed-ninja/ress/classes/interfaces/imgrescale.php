<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * CSS minification interface
 */
interface IRessio_ImgRescale
{
    /**
     * Get list of supported image formats
     * @return array
     */
    public function getSupportedExts();

    /**
     * Rescale Image
     * @param string $src_imagepath
     * @param string $dest_imagepath
     * @param int $dest_width
     * @param int $dest_height
     * @param string|bool $dest_ext
     * @return string
     */
    public function rescale($src_imagepath, $dest_imagepath, $dest_width, $dest_height, $dest_ext = false);
}