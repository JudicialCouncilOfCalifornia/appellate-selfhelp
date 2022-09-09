<?php

/**
 * Advanced Mobile Device Detection
 *
 * @version    ###VERSION###
 * @license    ###LICENSE###
 * @copyright  ###COPYRIGHT###
 * @date       ###DATE###
 */

class AmddDatabaseException extends Exception
{
}

abstract class AmddDatabase
{
    /**
     * get instance of database object
     * @static
     * @param array $options
     * @return AmddDatabase
     * @throws AmddDatabaseException
     */
    public static function getInstance($options)
    {
        static $handlers = array();

        if (!isset($options['handler'])) {
            return null;
        }

        $handlerName = $options['handler'];
        $handlerName = strtolower($handlerName);

        if (!isset($handlers[$handlerName])) {
            $className = 'AmddDatabase' . $handlerName;
            if (!class_exists($className, false)) {
                $path = dirname(__FILE__) . "/{$handlerName}/db.{$handlerName}.php";
                if (!is_file($path)) {
                    throw new AmddDatabaseException('File not found: ' . $path, 1);
                }
                require_once $path;
                if (!class_exists($className)) {
                    throw new AmddDatabaseException('Class not found: ' . $className, 1);
                }
            }
            $handlers[$handlerName] = new $className($options);
        }

        return $handlers[$handlerName];
    }

    /**
     * Get device data from main table
     * @param string $ua device User-Agent header
     * @return string json-encoded device capabilities (null if not found)
     */
    abstract public function getDevice($ua);

    /**
     * Get list of devices for group from main table
     * @param string $group device group
     * @return array list of objects with ua and data fields
     */
    abstract public function getDevices($group);

    /**
     * Get device from cache table
     * @param string $ua device User-Agent header
     * @return string json-encoded device capabilities (null if not found)
     */
    abstract public function getDeviceFromCache($ua);

    /**
     * Put device to cache table
     * @param string $ua device User-Agent header
     * @param string $data json-encoded device capabilities
     * @param integer $limit cache size (0 = disabled, -1 = infinite)
     */
    abstract public function putDeviceToCache($ua, $data, $limit = 0);

    /**
     * Clear cache table (if implemented)
     */
    abstract public function clearCache();

    /**
     * Update/create device database from stream
     * (all previous data will be erased)
     * @param $stream resource
     */
    abstract public function updateDatabase($stream);

    /**
     * Check database existance
     * @return bool
     */
    abstract public function checkDatabase();

    /**
     * Drop device database
     */
    abstract public function dropDatabase();
}