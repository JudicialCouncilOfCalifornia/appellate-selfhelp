<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * @property IRessio_Cache $cache
 * @property Ressio_Config $config
 * @property IRessio_CssCombiner $cssCombiner
 * @property IRessio_CssMinify $cssMinify
 * @property IRessio_CssOptimizer $cssOptimizer
 * @property IRessio_DeviceDetector $deviceDetector
 * @property IRessio_Dispatcher $dispatcher
 * @property IRessio_Filelock $filelock
 * @property IRessio_Filesystem $filesystem
 * @property IRessio_HtmlOptimizer $htmlOptimizer
 * @property IRessio_ImgOptimizer $imgOptimizer
 * @property IRessio_ImgRescale $imgRescaler
 * @property IRessio_JsCombiner $jsCombiner
 * @property IRessio_JsMinify $jsMinify
 * @property IRessio_Logger $logger
 * @property Ressio_UrlRewriter $urlRewriter
 */
class Ressio_DI
{
    private $_di = array();

    /**
     * @param string $key
     * @param string|array|object $value
     */
    public function set($key, $value)
    {
        $this->_di[$key] = $value;
        if (is_object($value)) {
            $this->{$key} = $value;
        } else {
            unset($this->{$key});
        }
    }

    /**
     * @param string $key
     * @return object|null
     * @throws ERessio_UnknownDiKey
     */
    public function __get($key)
    {
        if (!isset($this->_di[$key])) {
            throw new ERessio_UnknownDiKey('Unknown key: ' . $key);
        }

        if (isset($this->{$key})) {
            return $this->{$key};
        }

        return ($this->{$key} = $this->createNew($key));
    }

    /**
     * @param string $key
     * @return object|null
     * @throws ERessio_UnknownDiKey
     */
    public function get($key)
    {
        return $this->{$key};
    }

    /**
     * @param string $key
     * @return object|null
     * @throws ERessio_UnknownDiKey
     */
    public function getNew($key)
    {
        if (!isset($this->_di[$key])) {
            throw new ERessio_UnknownDiKey('Unknown key: ' . $key);
        }

        return $this->createNew($key);
    }

    /**
     * @param string $key
     * @return string|array|object
     * @throws ERessio_UnknownDiKey
     */
    public function getName($key)
    {
        if (!isset($this->_di[$key])) {
            throw new ERessio_UnknownDiKey('Unknown key: ' . $key);
        }

        return $this->_di[$key];
    }

    /**
     * @param string $key
     * @return object|null
     * @throws ERessio_UnknownDiKey
     */
    private function createNew($key)
    {
        $value = $this->_di[$key];

        /** @var object|null $result */
        $result = null;

        /** @var string $className */
        /** @var string $methodName */
        if (is_string($value)) {
            if (strpos($value, ':') === false) {
                // "classname"
                $result = new $value();
            } else {
                // "classname:methodname"
                list($className, $methodName) = explode(':', $value, 2);
                $result = call_user_func(array($className, $methodName));
            }
        } elseif (is_array($value)) {
            if (is_string($value[0])) {
                $className = $value[0];
                $params = isset($value[1]) ? (array)$value[1] : array();
                if (strpos($className, ':') === false) {
                    // array("classname", (array)options)
                    try {
                        $reflect = new ReflectionClass($className);
                        $result = $reflect->newInstanceArgs($params);
                    } catch (ReflectionException $e) {
                        // don't use di->logger to avoid self-call
                        trigger_error('PageSpeed Ninja: reflection error ' . $e->getMessage());
                    }
                } else {
                    // array("classname:methodname", (array)options)
                    list($className, $methodName) = explode(':', $className, 2);
                    $result = call_user_func_array(array($className, $methodName), $params);
                }
            } elseif (is_object($value[0])) {
                // array($obj, "methodname" [, (array)option])
                /** @var object $obj */
                list($obj, $methodName) = $value;
                $params = isset($value[2]) ? (array)$value[2] : array();
                $result = call_user_func_array(array($obj, $methodName), $params);
            }
        } elseif (is_object($value)) {
            $result = $value;
        }

        if (is_object($result) && method_exists($result, 'setDI')) {
            $result->setDI($this);
        }

        return $result;
    }
}