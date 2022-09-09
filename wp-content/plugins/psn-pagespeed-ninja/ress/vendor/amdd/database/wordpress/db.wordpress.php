<?php
/**
 * Advanced Mobile Device Detection
 *
 * @version        ###VERSION###
 * @license        ###LICENSE###
 * @copyright    ###COPYRIGHT###
 * @date        ###DATE###
 */

class AmddDatabaseWordpress extends AmddDatabase
{
    private $dbTableName;

    private $table;
    private $tableCache;

    public function __construct($options)
    {
        $this->dbTableName = $options['dbTableName'];

        $this->table = $this->dbTableName;
        $this->tableCache = $this->dbTableName . '_cache';
    }

    public function getDevice($ua)
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT `data` FROM `{$this->table}` WHERE `ua`=%s", $ua);
        return $wpdb->get_var($query);
    }

    public function getDevices($group)
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT `ua`, `data` FROM `{$this->table}` WHERE `group`=%s", $group);
        return $wpdb->get_results($query);
    }

    public function getDeviceFromCache($ua)
    {
        global $wpdb;

        $query = $wpdb->prepare("SELECT `data` FROM `{$this->tableCache}` WHERE `ua`=%s", $ua);
        $data = $wpdb->get_var($query);

        if ($data !== null) {
            $query = $wpdb->prepare("UPDATE `{$this->tableCache}` SET time=" . time() . ' WHERE `ua`=%s', $ua);
            $wpdb->query($query);
        }

        return $data;
    }

    public function putDeviceToCache($ua, $data, $limit = 0)
    {
        global $wpdb;

        if ($limit >= 0) {
            $query = "SELECT COUNT(*) FROM `{$this->tableCache}`";
            $cacheSize = $wpdb->get_var($query);

            if ($cacheSize > $limit) {
                $query = $wpdb->prepare("DELETE FROM `{$this->tableCache}` WHERE time <="
                    . ' (SELECT time FROM'
                    . "   (SELECT time FROM `{$this->tableCache}` ORDER BY time DESC LIMIT %d, 1)"
                    . ' foo)', $limit);
                $wpdb->query($query);
            }
        }

        if ($limit !== 0) {
            $time = time();
            $query = $wpdb->prepare("INSERT IGNORE INTO `{$this->tableCache}` (`ua`, `data`, `time`)"
                . ' VALUES (%s, %s, %d)'
                . ' ON DUPLICATE KEY UPDATE `data`=%s, `time`=%d', $ua, $data, $time, $data, $time);
            $wpdb->query($query);
        }
    }

    public function clearCache()
    {
        global $wpdb;

        $query = "TRUNCATE `{$this->tableCache}`";
        $wpdb->query($query);
    }

    /**
     * @param array $queries
     * @throws RuntimeException
     */
    private function batchQueries($queries)
    {
        global $wpdb;

        foreach ($queries as $query) {
            $wpdb->query($query);
        }
    }

    public function updateDatabase($stream)
    {
        global $wpdb;
        $amdd_prefix = $this->dbTableName;

        $this->batchQueries(array(
            "DROP TABLE IF EXISTS `{$amdd_prefix}_tmp`",

            "CREATE TABLE `{$amdd_prefix}_tmp` ("
            . '  `ua` varchar(255) collate utf8_bin NOT NULL,'
            . '  `group` varchar(32) collate utf8_bin NOT NULL,'
            . '  `data` varchar(255) collate utf8_bin NOT NULL,'
            . '  UNIQUE KEY `ua` (`ua`),'
            . '  KEY `group` (`group`)'
            . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin',

            "ALTER TABLE `{$amdd_prefix}_tmp` DISABLE KEYS"
        ));

        $insert_sql_head = "INSERT INTO `{$amdd_prefix}_tmp` VALUES ";
        $insert_sql = '';
        $counter = 0;

        while (!feof($stream)) {
            $ua = rtrim(fgets($stream));
            $group = rtrim(fgets($stream));
            $data = rtrim(fgets($stream));
            $empty = rtrim(fgets($stream));
            if ($ua === '' || $data === '' || !empty($empty)) {
                break;
            }

            if ($insert_sql !== '') {
                $insert_sql .= ',';
            }
            $insert_sql .= '('
                . "'" . addcslashes($ua, "\\'") . "',"
                . "'" . addcslashes($group, "\\'") . "',"
                . "'" . addcslashes($data, "\\'") . "'"
                . ')';

            if (strlen($insert_sql) > 50000) {
                $query = $insert_sql_head . $insert_sql;
                $wpdb->query($query);
                $insert_sql = '';
                $counter++;
            }
        }

        if ($insert_sql !== '') {
            $query = $insert_sql_head . $insert_sql;
            $wpdb->query($query);
        }

        $this->batchQueries(array(
            "ALTER TABLE `{$amdd_prefix}_tmp` ENABLE KEYS",

            "CREATE TABLE IF NOT EXISTS `{$amdd_prefix}_cache` ("
            . '  `ua` varchar(255) collate utf8_bin NOT NULL,'
            . '  `data` varchar(255) collate utf8_bin NOT NULL,'
            . '  `time` int(10) unsigned NOT NULL'
            . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin',

            "CREATE TABLE IF NOT EXISTS `{$amdd_prefix}` ("
            . '  `dummy` int'
            . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin',

            "RENAME TABLE `{$amdd_prefix}` TO `{$amdd_prefix}_old`, `{$amdd_prefix}_tmp` TO `{$amdd_prefix}`",

            "TRUNCATE TABLE `{$amdd_prefix}_cache`",

            "DROP TABLE `{$amdd_prefix}_old`"
        ));
    }

    public function checkDatabase()
    {
        global $wpdb;
        $query = "SHOW TABLES LIKE `{$this->dbTableName}``";
        return ($wpdb->get_var($query) !== null);
    }

    public function dropDatabase()
    {
        global $wpdb;
        $query = "DROP TABLE `{$this->table}`, `{$this->tableCache}`";
        $wpdb->query($query);
    }
}
