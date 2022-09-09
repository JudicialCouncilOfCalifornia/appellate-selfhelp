<?php

/**
 * Advanced Mobile Device Detection
 *
 * @version     ###VERSION###
 * @license     ###LICENSE###
 * @copyright   ###COPYRIGHT###
 * @date        ###DATE###
 */
class AmddDatabaseMySQLi extends AmddDatabase
{
    /** @var mysqli */
    private $resource;
    private $sql;

    private $dbHost;
    private $dbUser;
    private $dbPassword;
    private $dbDatabase;
    private $dbTableName;

    private $table;
    private $tableCache;

    public function __construct($options)
    {
        if (!function_exists('mysqli_connect')) {
            throw new AmddDatabaseException('The "mysqli" extension is not available', 1);
        }
        $this->dbHost = $options['dbHost'];
        $this->dbUser = $options['dbUser'];
        $this->dbPassword = $options['dbPassword'];
        $this->dbDatabase = $options['dbDatabase'];
        $this->dbTableName = $options['dbTableName'];

        $this->connect();
        $this->table = $this->dbTableName;
        $this->tableCache = $this->dbTableName . '_cache';
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            mysqli_close($this->resource);
        }
    }

    public function getDevice($ua)
    {
        $query = "SELECT `data` FROM `{$this->table}` WHERE `ua`=" . $this->Quote($ua);
        $this->setQuery($query);
        return $this->loadResult();
    }

    public function getDevices($group)
    {
        $query = "SELECT `ua`, `data` FROM `{$this->table}` WHERE `group`=" . $this->Quote($group);
        $this->setQuery($query);
        return $this->loadObjectList();
    }

    public function getDeviceFromCache($ua)
    {
        $query = "SELECT `data` FROM `{$this->tableCache}` WHERE `ua`=" . $this->Quote($ua);
        $this->setQuery($query);
        $data = $this->loadResult();

        if ($data !== null) {
            $query = "UPDATE `{$this->tableCache}` SET time=" . time() . ' WHERE `ua`=' . $this->Quote($ua);
            $this->setQuery($query);
            $this->query();
        }

        return $data;
    }

    public function putDeviceToCache($ua, $data, $limit = 0)
    {
        if ($limit >= 0) {
            $query = "SELECT COUNT(*) FROM `{$this->tableCache}`";
            $this->setQuery($query);
            $cacheSize = $this->loadResult();

            if ($cacheSize > $limit) {
                $query = "DELETE FROM `{$this->tableCache}` WHERE time <="
                    . ' (SELECT time FROM'
                    . "   (SELECT time FROM `{$this->tableCache}` ORDER BY time DESC LIMIT $limit, 1)"
                    . ' foo)';
                $this->setQuery($query);
                $this->query();
            }
        }

        if ($limit !== 0) {
            $x_ua = $this->Quote($ua);
            $x_data = $this->Quote($data);
            $x_time = time();
            $query = "INSERT IGNORE INTO `{$this->tableCache}` (`ua`, `data`, `time`)"
                . " VALUES ($x_ua, $x_data, $x_time)"
                . " ON DUPLICATE KEY UPDATE `data`=$x_data, `time`=$x_time";
            $this->setQuery($query);
            $this->query();
        }
    }

    public function clearCache()
    {
        $query = "TRUNCATE `{$this->tableCache}`";
        $this->setQuery($query);
        $this->query();
    }

    private function connect()
    {
        $host = $this->dbHost;
        $user = $this->dbUser;
        $password = $this->dbPassword;
        $database = $this->dbDatabase;

        $port = null;
        $socket = null;
        $targetSlot = substr(strstr($host, ':'), 1);
        if ($targetSlot !== false && $targetSlot !== '') {
            if (is_numeric($targetSlot)) {
                $port = $targetSlot;
            } else {
                $socket = $targetSlot;
            }
            $host = substr($host, 0, -(strlen($targetSlot) + 1));
            if ($host === '') {
                $host = 'localhost';
            }
        }

        $this->resource = mysqli_connect($host, $user, $password, null, $port, $socket);
        if (!$this->resource) {
            throw new AmddDatabaseException('Could not connect to MySQL', 2);
        }

        mysqli_query($this->resource, "SET NAMES 'utf8'");

        if ($database && !mysqli_select_db($this->resource, $database)) {
            throw new AmddDatabaseException('Could not connect to database', 3);
        }
    }

    private function setQuery($query)
    {
        $this->sql = $query;
    }

    private function Quote($text)
    {
        return "'" . mysqli_real_escape_string($this->resource, $text) . "'";
    }

    /**
     * @return mysqli_result|false
     * @throws AmddDatabaseException
     */
    private function query()
    {
        if (!is_object($this->resource)) {
            return false;
        }
        $sql = $this->sql;
        $cursor = mysqli_query($this->resource, $sql);
        if (!$cursor) {
            $errorNum = mysqli_errno($this->resource);
            if ($errorNum === 2006 || $errorNum === 2013) {
                $this->connect();
                if ($this->resource) {
                    $cursor = mysqli_query($this->resource, $sql);
                    if ($cursor) {
                        return $cursor;
                    }
                }
            }
            throw new AmddDatabaseException(mysqli_error($this->resource) . " SQL=$sql", mysqli_errno($this->resource));
        }
        return $cursor;
    }

    private function loadResult()
    {
        if (!($cur = $this->query())) {
            return null;
        }
        $ret = null;
        $row = mysqli_fetch_row($cur);
        if ($row) {
            $ret = $row[0];
        }
        mysqli_free_result($cur);
        return $ret;
    }

    private function loadObjectList($key = '')
    {
        if (!($cur = $this->query())) {
            return null;
        }
        $array = array();
        while ($row = mysqli_fetch_object($cur)) {
            if ($key) {
                $array[$row->$key] = $row;
            } else {
                $array[] = $row;
            }
        }
        mysqli_free_result($cur);
        return $array;
    }

    /**
     * @param array $queries
     * @throws AmddDatabaseException
     */
    private function batchQueries($queries)
    {
        foreach ($queries as $query) {
            $this->setQuery($query);
            $this->query();
        }
    }

    public function updateDatabase($stream)
    {
        $amdd_prefix = $this->dbTableName;

        $this->batchQueries(array(
            "CREATE TABLE IF NOT EXISTS `{$amdd_prefix}_cache` ("
            . '  `ua` varchar(255) collate utf8_bin NOT NULL,'
            . '  `data` varchar(255) collate utf8_bin NOT NULL,'
            . '  `time` int(10) unsigned NOT NULL'
            . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin',

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

        while (!feof($stream)) {
            $ua = rtrim(fgets($stream));
            $group = rtrim(fgets($stream));
            $data = rtrim(fgets($stream));
            $empty = rtrim(fgets($stream));
            if ($ua === '' || $data === '' || $empty !== '') {
                break;
            }

            if ($insert_sql !== '') {
                $insert_sql .= ',';
            }
            // @todo use fast quote implementation instead of MySQL's real escape
            $insert_sql .= '('
                . $this->Quote($ua) . ','
                . "'$group',"
                . $this->Quote($data)
                . ')';

            if (strlen($insert_sql) > 50000) {
                $query = $insert_sql_head . $insert_sql;
                $this->setQuery($query);
                $this->query();
                $insert_sql = '';
            }
        }

        if ($insert_sql !== '') {
            $query = $insert_sql_head . $insert_sql;
            $this->setQuery($query);
            $this->query();
        }

        $this->batchQueries(array(
            "ALTER TABLE `{$amdd_prefix}_tmp` ENABLE KEYS",

            "CREATE TABLE IF NOT EXISTS `{$amdd_prefix}` ("
            . '  `dummy` int'
            . ') ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin',

            "RENAME TABLE `{$amdd_prefix}` TO `{$amdd_prefix}_old`, `{$amdd_prefix}_tmp` TO `{$amdd_prefix}`;",

            "TRUNCATE `{$amdd_prefix}_cache`",

            "DROP TABLE `{$amdd_prefix}_old`"
        ));
    }

    public function checkDatabase()
    {
        $this->setQuery("SHOW TABLES LIKE `{$this->dbTableName}``");
        return ($this->loadResult() !== null);
    }

    public function dropDatabase()
    {
        $this->setQuery("DROP TABLE `{$this->table}`, `{$this->tableCache}`");
        $this->query();
    }
}