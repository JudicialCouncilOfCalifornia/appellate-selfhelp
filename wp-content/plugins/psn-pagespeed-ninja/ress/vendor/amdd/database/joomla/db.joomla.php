<?php
/**
 * Advanced Mobile Device Detection
 *
 * @version        ###VERSION###
 * @license        ###LICENSE###
 * @copyright    ###COPYRIGHT###
 * @date        ###DATE###
 */

/** @todo split into Joomla and Joomla-Legacy */
class AmddDatabaseJoomla extends AmddDatabase
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
        $db = JFactory::getDbo();

        $query = "SELECT `data` FROM `{$this->table}` WHERE `ua`=" . $db->quote($ua);
        $db->setQuery($query);
        return $db->loadResult();
    }

    public function getDevices($group)
    {
        $db = JFactory::getDbo();

        $query = "SELECT `ua`, `data` FROM `{$this->table}` WHERE `group`=" . $db->quote($group);
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getDeviceFromCache($ua)
    {
        $db = JFactory::getDbo();

        $query = "SELECT `data` FROM `{$this->tableCache}` WHERE `ua`=" . $db->quote($ua);
        $db->setQuery($query);
        $data = $db->loadResult();

        if ($data !== null) {
            $query = "UPDATE `{$this->tableCache}` SET time=" . time() . ' WHERE `ua`=' . $db->quote($ua);
            $db->setQuery($query);
            $db->query();
        }

        return $data;
    }

    public function putDeviceToCache($ua, $data, $limit = 0)
    {
        $db = JFactory::getDbo();

        if ($limit >= 0) {
            $query = "SELECT COUNT(*) FROM `{$this->tableCache}`";
            $db->setQuery($query);
            $cacheSize = $db->loadResult();

            if ($cacheSize > $limit) {
                $query = "DELETE FROM `{$this->tableCache}` WHERE time <="
                    . ' (SELECT time FROM'
                    . "   (SELECT time FROM `{$this->tableCache}` ORDER BY time DESC LIMIT $limit, 1)"
                    . ' foo)';
                $db->setQuery($query);
                $db->query();
            }
        }

        if ($limit !== 0) {
            $x_ua = $db->quote($ua);
            $x_data = $db->quote($data);
            $x_time = time();
            $query = "INSERT IGNORE INTO `{$this->tableCache}` (`ua`, `data`, `time`)"
                . " VALUES ($x_ua, $x_data, $x_time)"
                . " ON DUPLICATE KEY UPDATE `data`=$x_data, `time`=$x_time";
            $db->setQuery($query);
            $db->query();
        }
    }

    public function clearCache()
    {
        $db = JFactory::getDbo();

        $query = "TRUNCATE `{$this->tableCache}`";
        $db->setQuery($query);
        $db->query();
    }

    /**
     * @param array $queries
     * @throws RuntimeException
     */
    private function batchQueries($queries)
    {
        $db = JFactory::getDbo();

        foreach ($queries as $query) {
            $db->setQuery($query);
            $db->query();
        }
    }

    public function updateDatabase($stream)
    {
        $db = JFactory::getDbo();
        $amdd_prefix = $this->dbTableName;

        // @todo Support other databases (at least PostgreSQL)
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

        $config = JFactory::getConfig();
        $debuglevel = (strncmp(JVERSION, '1.5.', 4) === 0) ? $config->getValue('config.debug') : $config->get('debug');
        if (version_compare(JVERSION, '3.0', '>=')) {
            $db->setDebug(0);
        } else {
            $db->debug(0);
        }

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
                $db->setQuery($query);
                $db->query();
                $insert_sql = '';
                $counter++;
            }
        }

        if ($insert_sql !== '') {
            $query = $insert_sql_head . $insert_sql;
            $db->setQuery($query);
            $db->query();
        }

        if (version_compare(JVERSION, '3.0', '>=')) {
            $db->setDebug($debuglevel);
        } else {
            $db->debug($debuglevel);
        }
        if ($debuglevel) {
            $db->setQuery("# Insert $counter amdd queries");
            $db->query();
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
        $db = JFactory::getDbo();
        $db->setQuery("SHOW TABLES LIKE `{$this->dbTableName}``");
        return ($db->loadResult() !== null);
    }

    public function dropDatabase()
    {
        $db = JFactory::getDbo();
        $db->setQuery("DROP TABLE `{$this->table}`, `{$this->tableCache}`");
        $db->query();
    }
}