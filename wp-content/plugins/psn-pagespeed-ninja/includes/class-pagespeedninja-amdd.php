<?php

class PagespeedNinja_Amdd
{
    public static function getConfig()
    {
        $ress_dir = dirname(dirname(__FILE__)) . '/ress';
        if (!class_exists('Amdd', false)) {
            include_once $ress_dir . '/vendor/amdd/amdd.php';
        }
        if (!class_exists('AmddDatabase', false)) {
            include_once $ress_dir . '/vendor/amdd/database/database.php';
        }

        global $wpdb;
        return array(
            'handler' => 'wordpress',
            'cacheSize' => 1000,
            'dbTableName' => $wpdb->prefix . 'psninja_amdd'
        );
    }

    public static function dropDatabase()
    {
        $options = self::getConfig();
        Amdd::dropDatabase($options);
    }

    public static function clearCache()
    {
        $options = self::getConfig();
        $amddDb = AmddDatabase::getInstance($options);
        $amddDb->clearCache();
    }

    /**
     * @param string $path
     * @throws AmddDatabaseException
     */
    public static function updateDatabaseFromFile($path)
    {
        $options = self::getConfig();
        Amdd::updateDatabaseFromFile($path, $options);
    }
}