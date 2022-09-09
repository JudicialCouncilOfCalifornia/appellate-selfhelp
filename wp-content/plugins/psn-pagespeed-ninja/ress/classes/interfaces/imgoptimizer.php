<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_ImgOptimizer
{
    /**
     * @param $srcFile string
     * @return bool
     */
    public function run($srcFile);

    /**
     * @param $src_imagepath string
     * @param $src_timestamp int
     * @param $backup_imagepath string
     */
    public function backup($src_imagepath, $src_timestamp, $backup_imagepath);

    /**
     * @return bool
     */
    public function restore();
}
