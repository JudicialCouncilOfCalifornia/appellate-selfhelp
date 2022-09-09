<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_JsCombiner
{
    /**
     * Returns the node as string
     * @param array $scriptList
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function combineToHtml($scriptList);

    /**
     * @param $scriptList array
     * @return array
     */
    public function combine($scriptList);
}