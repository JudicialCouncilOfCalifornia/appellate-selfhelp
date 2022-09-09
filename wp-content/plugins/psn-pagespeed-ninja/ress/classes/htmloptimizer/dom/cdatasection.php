<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_HtmlOptimizer_Dom_CdataSection extends DOMCdataSection
{
    public function detach()
    {
        $this->parentNode->removeChild($this);
    }
}
