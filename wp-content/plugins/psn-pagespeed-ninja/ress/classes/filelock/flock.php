<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_FileLock_flock implements IRessio_FileLock
{
    private $locks = array();

    public function __construct()
    {
        register_shutdown_function(array($this, 'shutdown'));
    }

    public function shutdown()
    {
        foreach ($this->locks as $filename => $fp) {
            // remove locked file
            @unlink($filename);
            $this->unlock($filename);
        }
    }

    /**
     * @param $filename string
     * @return bool
     */
    public function lock($filename)
    {
        $lockfile = $filename . '.lock';
        // based on http://stackoverflow.com/questions/17708885/flock-removing-locked-file-without-race-condition#answer-18745264
        while (true) {
            $fp = @fopen($lockfile, 'cb');
            if (!$fp) {
                return false;
            }
            if (!flock($fp, LOCK_EX)) {
                fclose($fp);
                // @todo if(--$count){usleep(10000};continue;}
                return false;
            }

            $fp_stat = fstat($fp);
            $file_stat = @stat($lockfile);
            if ($file_stat === false || $fp_stat['ino'] === $file_stat['ino']) {
                break;
            }

            fclose($fp);
        }
        $this->locks[$filename] = $fp;
        return true;
    }

    /**
     * @param $filename string
     * @param $local bool
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

        $fp = @fopen($filename . '.lock', 'cb');
        if (!$fp) {
            return false;
        }
        $unlocked = flock($fp, LOCK_EX | LOCK_NB);
        if ($unlocked) {
            flock($fp, LOCK_UN);
        }
        fclose($fp);
        return !$unlocked;
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
        $fp = $this->locks[$filename];
        $lockfile = $filename . '.lock';
        $deleted = (DIRECTORY_SEPARATOR === '/') && @unlink($lockfile);
        flock($fp, LOCK_UN);
        fclose($fp);
        // @todo Windows: lock file may be flocked at this point, check possible side effects
        $deleted || @unlink($lockfile);
        unset($this->locks[$filename]);
        return true;
    }
}
