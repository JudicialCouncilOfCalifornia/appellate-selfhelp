<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_CssOptimizer
{
    /**
     * @param $buffer string
     * @param $srcBase string
     * @param $targetBase string
     * @return string
     */
    public function run($buffer, $srcBase = null, $targetBase = null);
}
