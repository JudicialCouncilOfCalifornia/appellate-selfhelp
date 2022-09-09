<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_FileLock_mkdir implements IRessio_FileLock
{
    private $locks = array();

    public function __construct()
    {
        register_shutdown_function(array($this, 'shutdown'));
    }

    public function shutdown()
    {
        foreach ($this->locks as $file => $bool) {
            @unlink($file);
            rmdir($file . '.lock');
        }
    }

    /**
     * @param $filename string
     * @return bool
     */
    public function lock($filename)
    {
        $lockfile = $filename . '.lock';
        $timeout = 60000;
        $time = 0;
        while (!@mkdir($lockfile)) {
            $delay = mt_rand(1, 10);
            usleep($delay);
            $time += $delay;
            if ($time >= $timeout) {
                return false;
            }
        }
        $this->locks[$filename] = true;
        return true;
    }

    /**
     * @param string $filename
     * @param bool $local
     * @return bool
     */
    public function isLocked($filename, $local = false)
    {
        if (isset($this->locks[$filename])) {
            return true;
        }
        if ($local) {
            return false;
        }
        $lockfile = $filename . '.lock';
        return file_exists($lockfile);
    }

    /**
     * @param $filename string
     * @return bool
     */
    public function unlock($filename)
    {
        if (!isset($this->locks[$filename])) {
            return false;
        }
        $lockfile = $filename . '.lock';
        $result = rmdir($lockfile);
        if ($result) {
            unset($this->locks[$filename]);
        }
        return $result;
    }

}

