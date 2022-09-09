<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_CssCombiner
{
    /**
     * @param $styleList array
     * @param $self_close_str string
     * @return string
     */
    public function combineToHtml($styleList, $self_close_str = '');

    /**
     * @param $styleList array
     * @param $targetUrl string
     * @return array
     */
    public function combine($styleList, $targetUrl);
}