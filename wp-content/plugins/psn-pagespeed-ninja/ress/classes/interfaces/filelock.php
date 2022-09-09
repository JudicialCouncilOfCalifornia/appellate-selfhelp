<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_FileLock
{
    /**
     * @param $filename string
     * @return bool
     */
    public function lock($filename);

    /**
     * @param $filename string
     * @return bool
     */
    public function unlock($filename);

    /**
     * @param $filename string
     * @param $local bool
     * @return bool
     */
    public function isLocked($filename, $local = false);
}

