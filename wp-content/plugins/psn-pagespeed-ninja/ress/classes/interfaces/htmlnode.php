<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_HtmlNode
{
    /**
     * @return string
     */
    public function getTag();

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name);

    /**
     * @param string $name
     * @return string
     */
    public function getAttribute($name);

    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value);

    /**
     * @param string $name
     */
    public function removeAttribute($name);

    /**
     * @param string $class
     */
    public function addClass($class);

    /**
     * @param string $class
     */
    public function removeClass($class);
}
