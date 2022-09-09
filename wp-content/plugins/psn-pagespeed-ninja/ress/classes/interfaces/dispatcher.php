<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

interface IRessio_Dispatcher
{
    const ORDER_FIRST = -5;
    const ORDER_STANDARD = 0;
    const ORDER_LAST = 5;

    /**
     * @param $eventNames array|string
     * @param $callableObj array|string
     * @param $priority int
     */
    public function addListener($eventNames, $callableObj, $priority = self::ORDER_STANDARD);

    /**
     * @param $eventNames array|string
     * @param $callableObj array|string
     */
    public function removeListener($eventNames, $callableObj);

    /**
     * @param $eventNames array|string
     */
    public function clearListeners($eventNames);

    /**
     * @param $eventName string
     * @param $params array
     */
    public function triggerEvent($eventName, $params = array());
}