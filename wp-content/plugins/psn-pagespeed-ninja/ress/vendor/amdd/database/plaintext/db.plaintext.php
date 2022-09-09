<?php

/**
 * Advanced Mobile Device Detection
 *
 * @version        ###VERSION###
 * @license        ###LICENSE###
 * @copyright    ###COPYRIGHT###
 * @date        ###DATE###
 */
class AmddDatabasePlaintext extends AmddDatabase
{
    /** @var string */
    private $dbPath;
    /** @var array */
    private $deviceList = array();

    public function __construct($options = array())
    {
        if (!is_dir($options['dbPath'])) {
            throw new AmddDatabaseException('The directory ' . $options['dbPath'] . ' is not exist', 1);
        }
        $this->dbPath = $options['dbPath'];
    }

    public function __destruct()
    {
    }

    public function getDevice($ua)
    {
        $group = AmddUA::getGroup($ua);
        $this->loadGroup($group);
        return isset($this->deviceList[$group]->$ua->data) ? $this->deviceList[$group]->$ua->data : null;
    }

    public function getDevices($group)
    {
        $this->loadGroup($group);
        return $this->deviceList[$group];
    }

    private function loadGroup($group)
    {
        if (isset($this->deviceList[$group])) {
            return;
        }

        $deviceFile = $this->dbPath . DIRECTORY_SEPARATOR . ($group !== '' ? $group : 'unclassified');
        if (is_file($deviceFile)) {
            $this->deviceList[$group] = json_decode(file_get_contents($deviceFile));
        } else {
            $this->deviceList[$group] = array();
        }

        foreach ($this->deviceList[$group] as $ua => &$data) {
            $data->ua = $ua;
        }
        //unset($data);
    }

    public function getDeviceFromCache($ua)
    {
        return null;
    }

    public function putDeviceToCache($ua, $data, $limit = 0)
    {
    }

    public function clearCache()
    {
    }

    public function updateDatabase($stream)
    {
        $devices = array();

        while (!feof($stream)) {
            $ua = rtrim(fgets($stream));
            $group = rtrim(fgets($stream));
            $data = rtrim(fgets($stream));
            $empty = rtrim(fgets($stream));
            if ($ua === '' || $data === '' || $empty !== '') {
                break;
            }

            if (!isset($devices[$group])) {
                $devices[$group] = array();
            }
            $data_obj = new stdClass;
            $data_obj->data = $data;
            $devices[$group][$ua] = $data_obj;
        }

        foreach ($devices as $group => $list) {
            $deviceFile = $this->dbPath . DIRECTORY_SEPARATOR . ($group !== '' ? $group : 'unclassified');
            $deviceFileTmp = $deviceFile . '.tmp';
            file_put_contents($deviceFileTmp, json_encode($list));
            if (!rename($deviceFileTmp, $deviceFile)) {
                copy($deviceFileTmp, $deviceFile);
                unlink($deviceFileTmp);
            }
        }
    }

    public function checkDatabase()
    {
        return file_exists($this->dbPath . DIRECTORY_SEPARATOR . 'android');
    }

    public function dropDatabase()
    {
        if (is_dir($this->dbPath)) {
            foreach (scandir($this->dbPath, @constant('SCANDIR_SORT_NONE')) as $file) {
                if ($file !== '.' && $file !== '..') {
                    unlink($this->dbPath . DIRECTORY_SEPARATOR . $file);
                }
            }
        }
    }
}