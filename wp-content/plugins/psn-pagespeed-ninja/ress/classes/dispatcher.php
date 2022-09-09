<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Dispatcher implements IRessio_Dispatcher
{
    /** @var Ressio_DI */
    private $di;
    /** @var Ressio_Config */
    public $config;

    /** @var array */
    private $listeners = array();
    /** @var int */
    private $counter = 0;

    /**
     */
    public function __construct()
    {
    }

    /**
     * @param $di Ressio_DI
     * @throws ERessio_UnknownDiKey
     */
    public function setDI($di)
    {
        $this->di = $di;
        $this->config = $di->config;
    }

    /**
     * @param $eventNames string[]|string
     * @param $callableObj array|string
     * @param $priority int
     * @throws ERessio_InvalidEventName
     */
    public function addListener($eventNames, $callableObj, $priority = self::ORDER_STANDARD)
    {
        if (is_array($eventNames)) {
            foreach ($eventNames as $eventName) {
                $this->addListener($eventName, $callableObj, $priority);
            }
        } elseif (is_string($eventNames)) {
            $this->counter++;
            if (!isset($this->listeners[$eventNames])) {
                $this->listeners[$eventNames] = array();
            }
            $this->listeners[$eventNames][$priority * (1 << 24) + $this->counter] = $callableObj;
        } else {
            throw new ERessio_InvalidEventName();
        }
    }

    /**
     * @param $eventNames array|string
     * @param $callableObj array|string
     * @throws ERessio_InvalidEventName
     */
    public function removeListener($eventNames, $callableObj)
    {
        if (is_array($eventNames)) {
            foreach ($eventNames as $eventName) {
                $this->removeListener($eventName, $callableObj);
            }
        } elseif (is_string($eventNames)) {
            if (is_array($this->listeners[$eventNames])) {
                foreach ($this->listeners[$eventNames] as $i => $listener) {
                    if ($listener === $callableObj) {
                        unset($this->listeners[$eventNames][$i]);
                    }
                }
            }
        } else {
            throw new ERessio_InvalidEventName();
        }
    }

    /**
     * @param $eventNames array|string
     * @throws ERessio_InvalidEventName
     */
    public function clearListeners($eventNames)
    {
        if (is_array($eventNames)) {
            foreach ($eventNames as $eventName) {
                $this->clearListeners($eventName);
            }
        } elseif (is_string($eventNames)) {
            unset($this->listeners[$eventNames]);
        } else {
            throw new ERessio_InvalidEventName();
        }
    }

    /**
     * @param $eventName string
     * @param $params array
     */
    public function triggerEvent($eventName, $params = array())
    {
        if (isset($this->listeners[$eventName])) {
            $event = new Ressio_Event($eventName);
            $Args = array($event);
            // Trick from http://php.net/manual/en/function.call-user-func-array.php#91503
            foreach ($params as &$arg) {
                $Args[] = &$arg;
            }
            foreach ($this->listeners[$eventName] as $listener) {
                call_user_func_array($listener, $Args);
                if ($event->isStopped()) {
                    break;
                }
            }
        }
    }
}